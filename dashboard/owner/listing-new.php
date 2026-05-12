<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/listing_helpers.php';
require_once __DIR__ . '/../../includes/upload.php';
require_role('asset_owner');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (request_exceeded_post_max()) {
        $errors['photos'] = 'Photo upload failed because the file is too large. The maximum size is 5MB per photo.';
    } elseif (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Security check failed. Please try again.';
    } else {
        $status = ($_POST['status_action'] ?? 'draft') === 'active' ? 'active' : 'draft';
        $publishing = $status === 'active';
        $photoCount = count_uploaded_files($_FILES['photos'] ?? []);
        $errors = validate_listing_input($_POST, $publishing, $publishing && $photoCount === 0);

        if (!$errors) {
            try {
                $pdo = Database::connect();
                $pdo->beginTransaction();
                $payload = normalize_listing_payload($_POST, (int) $_SESSION['user_id'], $status);
                $listingId = Database::insert('listings', $payload);

                $upload = upload_listing_photos($_FILES['photos'] ?? [], $listingId, 0);
                if ($upload['errors']) {
                    $errors['photos'] = implode(' ', $upload['errors']);
                    throw new RuntimeException('Listing photo upload failed');
                }
                foreach ($upload['paths'] as $idx => $path) {
                    Database::insert('listing_photos', [
                        'listing_id' => $listingId,
                        'file_path' => $path,
                        'sort_order' => $idx,
                    ]);
                }
                $pdo->commit();
                $title = $payload['title'];
                set_flash('success', $status === 'active'
                    ? "Your listing '{$title}' is now live."
                    : "Your listing '{$title}' was saved as draft."
                );
                redirect(base_url() . '/dashboard/owner/listings.php');
            } catch (Throwable $e) {
                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log('Create listing error: ' . $e->getMessage());
                if (empty($errors)) {
                    $errors['listing'] = 'Unable to save this listing right now.';
                }
            }
        }
    }
}

$dashboardTitle = 'Create Listing';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-panel">
    <h2>Create a Warsaw e-bike or scooter listing</h2>
    <p>Add the core details couriers need before they decide whether your asset fits their delivery work.</p>
    <?php if ($errors): ?>
        <div class="alert alert-error" data-form-error-summary>Please fix the errors below and try again.</div>
    <?php endif; ?>
    <?php if (!empty($errors['csrf']) || !empty($errors['listing'])): ?>
        <div class="alert alert-error"><?= e($errors['csrf'] ?? $errors['listing']) ?></div>
    <?php endif; ?>
    <?php require __DIR__ . '/_listing-form.php'; ?>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
