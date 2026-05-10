<?php
$dashboardTitle = 'Owner Overview';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-grid">
    <div class="dash-panel hero-panel">
        <h2>Welcome, <?= e($user['full_name'] ?? $user['email']) ?></h2>
        <p>Your owner account is ready for the Day 4 listing flow. You can prepare your e-bike or scooter details now.</p>
        <div class="app-actions">
            <a href="<?= base_url() ?>/dashboard/owner/listing-new.php" class="btn btn-primary">Create your first listing</a>
            <?php if (has_role('gig_worker')): ?>
                <a href="<?= base_url() ?>/dashboard/index.php" class="btn btn-secondary">Courier View</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="dash-panel">
        <h3>Active Listings</h3>
        <strong class="dash-number">0</strong>
        <p>You have 0 active listings.</p>
    </div>
    <div class="dash-panel">
        <h3>Upcoming Payouts</h3>
        <strong class="dash-number">0</strong>
        <p>Upcoming payouts: nothing scheduled yet.</p>
    </div>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
