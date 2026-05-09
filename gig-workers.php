<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/app_data.php';

$pageTitle = 'Gig Worker Platform';
$pageDescription = 'Kaptain One helps Warsaw gig workers access equipment, leasing packages, and support for delivery and driving work.';
$packages = array_slice(get_active_packages(), 0, 3);
require_once __DIR__ . '/includes/header.php';
?>

<section class="app-hero">
    <div class="container app-hero__grid">
        <div>
            <span class="section-kicker">Warsaw Gig Worker Platform</span>
            <h1>Start Gig Work With the Right Tools</h1>
            <p>Kaptain One helps couriers, drivers, and side-hustle workers access leasing packages, onboarding support, and work-ready equipment in one polished dashboard.</p>
            <div class="app-actions">
                <a href="<?= base_url() ?>/register.php" class="btn btn-primary btn-large">Create Account</a>
                <a href="<?= base_url() ?>/packages.php" class="btn btn-secondary btn-large">View Packages</a>
            </div>
        </div>
        <div class="app-hero-card">
            <div class="metric-card">
                <span>Market</span>
                <strong>Warsaw</strong>
            </div>
            <div class="metric-card">
                <span>Support For</span>
                <strong>Uber, Bolt, Glovo, Wolt</strong>
            </div>
            <div class="metric-card">
                <span>Workflow</span>
                <strong>Apply, Review, Lease</strong>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">How It Works</span>
            <h2>From Application to First Shift</h2>
        </div>
        <div class="app-grid app-grid-4">
            <?php foreach (['Create your worker profile', 'Choose a leasing package', 'Submit documents and details', 'Track approval and pickup'] as $step): ?>
                <div class="app-card">
                    <h3><?= e($step) ?></h3>
                    <p>Designed for a clean onboarding experience that keeps every next step visible.</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section app-band">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Package Preview</span>
            <h2>Leasing Options for Gig Workers</h2>
        </div>
        <div class="package-grid">
            <?php foreach ($packages as $package): ?>
                <?php require __DIR__ . '/includes/package-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
