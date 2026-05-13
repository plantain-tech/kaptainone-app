<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/listing_helpers.php';
require_once __DIR__ . '/../../includes/upload.php';
require_user();

$listingId = (int) ($_GET['id'] ?? 0);
$listing = $listingId > 0 ? get_listing($listingId) : null;
$user = current_user();
if (!$listing || !user_can_manage_listing($listing, $user)) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Listing unavailable | Kaptain One</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= base_url() ?>/assets/css/main.css">
        <link rel="stylesheet" href="<?= base_url() ?>/assets/css/responsive.css">
    </head>
    <body class="dashboard-shell">
        <main class="dash-main" style="min-height: 100vh; display: grid; place-items: center;">
            <section class="dash-panel" style="max-width: 640px;">
                <span class="section-kicker">Access denied</span>
                <h1>Listing unavailable</h1>
                <p>You do not have permission to edit this listing.</p>
                <a href="<?= base_url() ?>/dashboard/index.php" class="btn btn-secondary">Back to dashboard</a>
            </section>
        </main>
    </body>
    </html>
    <?php exit;
}

$errors = [];
$photos = get_listing_photos($listingId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (request_exceeded_post_max()) {
        $errors['photos'] = 'Photo upload failed because the file is too large. The maximum size is 5MB per photo.';
    } elseif (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Security check failed. Please try again.';
    } elseif (!empty($_POST['delete_photo_id'])) {
        $photoId = (int) $_POST['delete_photo_id'];
        $photo = Database::fetch("SELECT * FROM listing_photos WHERE id = ? AND listing_id = ?", [$photoId, $listingId]);
        if ($photo) {
            Database::query("DELETE FROM listing_photos WHERE id = ?", [$photoId]);
            $absolute = dirname(__DIR__, 2) . $photo['file_path'];
            if (is_file($absolute)) {
                @unlink($absolute);
            }
            set_flash('success', 'Photo deleted.');
            redirect(base_url() . '/dashboard/owner/listing-edit.php?id=' . $listingId);
        }
    } else {
        $status = in_array($_POST['status'] ?? 'draft', ['draft', 'active', 'paused', 'archived'], true) ? $_POST['status'] : 'draft';
        $existingCount = count($photos);
        $photoCount = count_uploaded_files($_FILES['photos'] ?? []);
        $errors = validate_listing_input($_POST, $status === 'active', $status === 'active' && ($existingCount + $photoCount) === 0);

        if (!$errors) {
            try {
                $pdo = Database::connect();
                $pdo->beginTransaction();
                $payload = normalize_listing_payload($_POST, (int) $listing['owner_user_id'], $status);
                unset($payload['owner_user_id']);
                Database::update('listings', $payload, 'id = :id', ['id' => $listingId]);

                $upload = upload_listing_photos($_FILES['photos'] ?? [], $listingId, $existingCount);
                if ($upload['errors']) {
                    $errors['photos'] = implode(' ', $upload['errors']);
                    throw new RuntimeException('Listing photo upload failed');
                }
                foreach ($upload['paths'] as $idx => $path) {
                    Database::insert('listing_photos', [
                        'listing_id' => $listingId,
                        'file_path' => $path,
                        'sort_order' => $existingCount + $idx,
                    ]);
                }
                $pdo->commit();
                set_flash('success', "Your listing '{$payload['title']}' was updated.");
                redirect(base_url() . '/dashboard/owner/listings.php');
            } catch (Throwable $e) {
                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log('Edit listing error: ' . $e->getMessage());
                if (empty($errors)) {
                    $errors['listing'] = 'Unable to save this listing right now.';
                }
            }
        }
    }
    $listing = array_merge($listing, $_POST);
    $photos = get_listing_photos($listingId);
}

$dashboardTitle = 'Edit Listing';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-panel">
    <h2>Edit listing</h2>
    <?php if ($errors): ?>
        <div class="alert alert-error" data-form-error-summary>Please fix the errors below and try again.</div>
    <?php endif; ?>
    <?php if (!empty($errors['csrf']) || !empty($errors['listing'])): ?>
        <div class="alert alert-error"><?= e($errors['csrf'] ?? $errors['listing']) ?></div>
    <?php endif; ?>
    <?php require __DIR__ . '/_listing-form.php'; ?>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
