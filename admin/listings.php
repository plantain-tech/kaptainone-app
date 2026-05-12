<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/listing_helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in() && !has_role('admin')) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security check failed. Please try again.');
    } else {
        $listingId = (int) ($_POST['listing_id'] ?? 0);
        if (($_POST['action'] ?? '') === 'force_archive' && $listingId > 0) {
            Database::update('listings', ['status' => 'archived'], 'id = :id', ['id' => $listingId]);
            set_flash('success', 'Listing archived.');
        }
    }
    redirect('listings.php');
}

$status = $_GET['status'] ?? 'all';
$assetType = $_GET['asset_type'] ?? 'all';
$ownerEmail = trim($_GET['owner_email'] ?? '');

$where = ['1=1'];
$params = [];
if (in_array($status, ['draft', 'active', 'paused', 'archived'], true)) {
    $where[] = 'l.status = ?';
    $params[] = $status;
}
if (in_array($assetType, ['ebike', 'scooter'], true)) {
    $where[] = 'l.asset_type = ?';
    $params[] = $assetType;
}
if ($ownerEmail !== '') {
    $where[] = 'u.email LIKE ?';
    $params[] = '%' . $ownerEmail . '%';
}

$listings = Database::fetchAll(
    "SELECT l.*, u.email AS owner_email,
            (SELECT COUNT(*) FROM listing_photos lp WHERE lp.listing_id = l.id) AS photo_count
     FROM listings l
     JOIN users u ON u.id = l.owner_user_id
     WHERE " . implode(' AND ', $where) . "
     ORDER BY l.created_at DESC",
    $params
);

$pageTitle = 'Listings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body class="admin-page">
    <main class="admin-workspace">
        <header class="admin-header">
            <div>
                <a href="dashboard.php" class="section-kicker">Admin dashboard</a>
                <h1>Marketplace Listings</h1>
            </div>
            <a href="../listings.php" target="_blank" class="btn btn-secondary">View public listings</a>
        </header>

        <?php if ($message = flash('success')): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
        <?php if ($message = flash('error')): ?><div class="alert alert-error"><?= e($message) ?></div><?php endif; ?>

        <section class="admin-panel">
            <form method="get" class="app-form two-col">
                <label>Status
                    <select class="form-control" name="status">
                        <?php foreach (['all' => 'All', 'draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused', 'archived' => 'Archived'] as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Asset type
                    <select class="form-control" name="asset_type">
                        <option value="all">All</option>
                        <option value="ebike" <?= $assetType === 'ebike' ? 'selected' : '' ?>>E-bike</option>
                        <option value="scooter" <?= $assetType === 'scooter' ? 'selected' : '' ?>>Scooter</option>
                    </select>
                </label>
                <label>Owner email
                    <input class="form-control" name="owner_email" value="<?= e($ownerEmail) ?>">
                </label>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
        </section>

        <section class="admin-panel">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Owner</th>
                            <th>Type</th>
                            <th>District</th>
                            <th>Weekly</th>
                            <th>Status</th>
                            <th>Photos</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listings as $listing): ?>
                            <tr>
                                <td><?= (int) $listing['id'] ?></td>
                                <td><?= e($listing['title']) ?></td>
                                <td><?= e($listing['owner_email']) ?></td>
                                <td><?= e(listing_asset_label($listing['asset_type'])) ?></td>
                                <td><?= e($listing['location_district']) ?></td>
                                <td><?= e(number_format((float) $listing['weekly_price_pln'], 0)) ?> PLN</td>
                                <td><span class="status-pill"><?= e(listing_status_label($listing['status'])) ?></span></td>
                                <td><?= (int) $listing['photo_count'] ?></td>
                                <td><?= e(date('M j, Y', strtotime($listing['created_at']))) ?></td>
                                <td>
                                    <div class="app-actions">
                                        <a class="btn btn-secondary" href="../listing.php?id=<?= (int) $listing['id'] ?>" target="_blank">View</a>
                                        <?php if ($listing['status'] !== 'archived'): ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="listing_id" value="<?= (int) $listing['id'] ?>">
                                                <button class="btn btn-secondary" type="submit" name="action" value="force_archive" onclick="return confirm('Force archive this listing?')">Force archive</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$listings): ?>
                            <tr><td colspan="10">No listings found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
