<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/listing_helpers.php';
require_role('asset_owner');

$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security check failed. Please try again.');
    } else {
        $listingId = (int) ($_POST['listing_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $listing = $listingId > 0 ? get_listing($listingId) : null;
        if ($listing && user_can_manage_listing($listing, $user)) {
            $newStatus = match ($action) {
                'pause' => 'paused',
                'activate' => 'active',
                'archive' => 'archived',
                default => null,
            };
            if ($newStatus) {
                Database::update('listings', ['status' => $newStatus], 'id = :id', ['id' => $listingId]);
                set_flash('success', 'Listing status updated.');
            }
        }
    }
    redirect(base_url() . '/dashboard/owner/listings.php');
}

$status = $_GET['status'] ?? 'all';
$sort = $_GET['sort'] ?? 'updated_desc';
$listings = fetch_owner_listings((int) $user['id'], $status, $sort);

$dashboardTitle = 'My Listings';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-panel">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Asset inventory</span>
            <h2>Your listings</h2>
        </div>
        <a href="<?= base_url() ?>/dashboard/owner/listing-new.php" class="btn btn-primary">Create your first listing</a>
    </div>

    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success listing-flash" data-flash-message>
            <span><?= e($message) ?></span>
            <button type="button" aria-label="Dismiss message" data-flash-dismiss>&times;</button>
        </div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="alert alert-error listing-flash" data-flash-message>
            <span><?= e($message) ?></span>
            <button type="button" aria-label="Dismiss message" data-flash-dismiss>&times;</button>
        </div>
    <?php endif; ?>

    <form method="get" class="filter-bar">
        <select name="status">
            <?php foreach (['all' => 'All statuses', 'active' => 'Active', 'paused' => 'Paused', 'draft' => 'Draft', 'archived' => 'Archived'] as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="sort">
            <option value="updated_desc" <?= $sort === 'updated_desc' ? 'selected' : '' ?>>Recently updated</option>
            <option value="created_desc" <?= $sort === 'created_desc' ? 'selected' : '' ?>>Newest first</option>
        </select>
        <button class="btn btn-secondary" type="submit">Apply</button>
    </form>

    <?php if (!$listings): ?>
        <div class="empty-state">
            <h3>You haven't listed anything yet.</h3>
            <p>Create your first Warsaw e-bike or scooter listing for couriers.</p>
            <a href="<?= base_url() ?>/dashboard/owner/listing-new.php" class="btn btn-primary">Create your first listing</a>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>District</th>
                        <th>Weekly</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listings as $item): ?>
                        <tr>
                            <td>
                                <?php if ($photo = listing_primary_photo($item)): ?>
                                    <img src="<?= e(base_url() . $photo) ?>" alt="" style="width:72px;height:54px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    <span class="status-pill">No photo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($item['title']) ?></td>
                            <td><?= e(listing_asset_label($item['asset_type'])) ?></td>
                            <td><?= e($item['location_district']) ?></td>
                            <td><?= e(number_format((float) $item['weekly_price_pln'], 0)) ?> PLN</td>
                            <td><span class="status-pill"><?= e(listing_status_label($item['status'])) ?></span></td>
                            <td><?= e(date('M j, Y', strtotime($item['updated_at']))) ?></td>
                            <td>
                                <div class="app-actions">
                                    <a href="<?= base_url() ?>/dashboard/owner/listing-edit.php?id=<?= (int) $item['id'] ?>" class="btn btn-secondary">Edit</a>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="listing_id" value="<?= (int) $item['id'] ?>">
                                        <?php if ($item['status'] === 'active'): ?>
                                            <button class="btn btn-secondary" type="submit" name="action" value="pause">Pause</button>
                                        <?php elseif ($item['status'] === 'paused'): ?>
                                            <button class="btn btn-secondary" type="submit" name="action" value="activate">Activate</button>
                                        <?php endif; ?>
                                        <button class="btn btn-secondary" type="submit" name="action" value="archive" onclick="return confirm('Archive this listing?')">Archive</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<script>
document.querySelectorAll('[data-flash-message]').forEach((message) => {
    const dismiss = message.querySelector('[data-flash-dismiss]');
    const hide = () => {
        message.style.opacity = '0';
        message.style.transform = 'translateY(-6px)';
        window.setTimeout(() => message.remove(), 220);
    };
    if (dismiss) dismiss.addEventListener('click', hide);
    window.setTimeout(hide, 5000);
});
</script>

<?php require_once __DIR__ . '/_footer.php'; ?>
