<?php
$dashboardTitle = 'Overview';
require_once __DIR__ . '/_layout.php';

if (has_role('asset_owner') && !has_role('gig_worker')) {
    redirect(base_url() . '/dashboard/owner/');
}

$profile = Database::fetch("SELECT * FROM user_profiles WHERE user_id = ?", [$user['id']]) ?? [];
$completion = profile_completion($profile);
$applications = Database::fetchAll(
    "SELECT a.*, p.title AS package_title
     FROM leasing_applications a
     JOIN equipment_packages p ON p.id = a.package_id
     WHERE a.user_id = ?
     ORDER BY a.created_at DESC LIMIT 5",
    [$user['id']]
);
$leased = Database::fetchAll(
    "SELECT l.*, p.title AS package_title, i.name AS item_name
     FROM leased_equipment l
     LEFT JOIN equipment_packages p ON p.id = l.package_id
     LEFT JOIN equipment_items i ON i.id = l.equipment_item_id
     WHERE l.user_id = ?
     ORDER BY l.created_at DESC LIMIT 5",
    [$user['id']]
);
$recommended = array_slice(get_active_packages(), 0, 3);
?>

<?php if (has_role('asset_owner') && has_role('gig_worker')): ?>
    <section class="dash-section">
        <h2>Your Courier Activity</h2>
    </section>
<?php endif; ?>

<section class="dash-grid">
    <div class="dash-panel hero-panel">
        <h2>Welcome, <?= e($profile['full_name'] ?? $user['email']) ?></h2>
        <p>Your worker profile is <?= $completion ?>% complete. Complete your profile to make package approval smoother.</p>
        <div class="progress-bar"><span style="width: <?= $completion ?>%"></span></div>
        <div class="app-actions">
            <a href="<?= base_url() ?>/dashboard/profile.php" class="btn btn-secondary">Complete Profile</a>
            <a href="<?= base_url() ?>/dashboard/apply.php" class="btn btn-primary">Start Application</a>
        </div>
    </div>
    <div class="dash-panel">
        <h3>Active Applications</h3>
        <strong class="dash-number"><?= count($applications) ?></strong>
        <p>Track review and approval status from one place.</p>
    </div>
    <div class="dash-panel">
        <h3>Leased Equipment</h3>
        <strong class="dash-number"><?= count($leased) ?></strong>
        <p>View pickup, active lease, and return status.</p>
    </div>
</section>

<?php if (has_role('asset_owner') && has_role('gig_worker')): ?>
    <section class="dash-section">
        <h2>Your Asset Owner Activity</h2>
        <div class="dash-grid">
            <div class="dash-panel">
                <h3>Active Listings</h3>
                <strong class="dash-number">0</strong>
                <p>Your owner listings will appear after the Day 4 listing flow is built.</p>
            </div>
            <div class="dash-panel">
                <h3>Booking Requests</h3>
                <strong class="dash-number">0</strong>
                <p>No booking requests yet.</p>
            </div>
            <div class="dash-panel">
                <h3>Owner View</h3>
                <p>Open the asset-owner dashboard shell for listings, booking requests, and payout settings.</p>
                <a href="<?= base_url() ?>/dashboard/owner/" class="btn btn-secondary">Open Owner View</a>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="dash-section">
    <h2>Recommended Packages</h2>
    <div class="package-grid compact">
        <?php foreach ($recommended as $package): ?>
            <?php require __DIR__ . '/../includes/package-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="dash-section">
    <h2>Recent Applications</h2>
    <div class="status-list">
        <?php if (!$applications): ?>
            <p class="text-muted">No applications yet.</p>
        <?php endif; ?>
        <?php foreach ($applications as $app): ?>
            <div class="status-row">
                <span><?= e($app['package_title']) ?></span>
                <span class="status-pill status-<?= e($app['status']) ?>"><?= e(application_status_label($app['status'])) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
