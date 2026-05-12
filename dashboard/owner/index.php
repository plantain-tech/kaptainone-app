<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/listing_helpers.php';
require_role('asset_owner');

$user = current_user();
$activeCount = (int) (Database::fetch("SELECT COUNT(*) AS count FROM listings WHERE owner_user_id = ? AND status = 'active'", [$user['id']])['count'] ?? 0);
$recentListings = Database::fetchAll(
    "SELECT * FROM listings WHERE owner_user_id = ? ORDER BY updated_at DESC LIMIT 3",
    [$user['id']]
);

$dashboardTitle = 'Owner Overview';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-grid">
    <div class="dash-panel hero-panel">
        <h2>Welcome, <?= e($user['full_name'] ?? $user['email']) ?></h2>
        <p>Your owner account is ready. Create and manage Warsaw e-bike or scooter listings for couriers.</p>
        <div class="app-actions">
            <a href="<?= base_url() ?>/dashboard/owner/listing-new.php" class="btn btn-primary">Create your first listing</a>
            <?php if (has_role('gig_worker')): ?>
                <a href="<?= base_url() ?>/dashboard/index.php" class="btn btn-secondary">Courier View</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="dash-panel">
        <h3>Active Listings</h3>
        <strong class="dash-number"><?= $activeCount ?></strong>
        <p>You have <?= $activeCount ?> active <?= $activeCount === 1 ? 'listing' : 'listings' ?>.</p>
    </div>
    <div class="dash-panel">
        <h3>Upcoming Payouts</h3>
        <strong class="dash-number">0</strong>
        <p>Upcoming payouts: nothing scheduled yet.</p>
    </div>
</section>

<section class="dash-panel">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Recent listings</span>
            <h2>Latest activity</h2>
        </div>
        <a href="<?= base_url() ?>/dashboard/owner/listings.php" class="btn btn-secondary">View all</a>
    </div>
    <?php if (!$recentListings): ?>
        <p>No listings yet. Add your first e-bike or scooter when you're ready.</p>
    <?php else: ?>
        <div class="dash-grid">
            <?php foreach ($recentListings as $item): ?>
                <article class="dash-panel">
                    <span class="status-pill"><?= e(listing_status_label($item['status'])) ?></span>
                    <h3><?= e($item['title']) ?></h3>
                    <p><?= e(number_format((float) $item['weekly_price_pln'], 0)) ?> PLN/week</p>
                    <p><?= (int) $item['view_count'] ?> views</p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
