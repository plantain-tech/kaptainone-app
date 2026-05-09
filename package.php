<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/app_data.php';

$slug = $_GET['slug'] ?? '';
$package = get_package_by_slug($slug);

if (!$package) {
    $pageTitle = 'Package Not Found';
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Package Not Found</h1><p><a href="' . base_url() . '/packages.php">Back to packages</a></p></div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $package['title'];
$pageDescription = $package['short_description'];
require_once __DIR__ . '/includes/header.php';
?>

<section class="package-detail-hero">
    <div class="container package-detail-grid">
        <div>
            <span class="section-kicker"><?= e($package['category']) ?></span>
            <h1><?= e($package['title']) ?></h1>
            <p><?= e($package['description']) ?></p>
            <div class="app-actions">
                <a href="<?= base_url() ?>/dashboard/apply.php?package=<?= (int)$package['id'] ?>" class="btn btn-primary btn-large">Apply for Package</a>
                <a href="<?= base_url() ?>/packages.php" class="btn btn-secondary btn-large">All Packages</a>
            </div>
        </div>
        <aside class="package-summary">
            <span class="status-pill status-<?= e($package['availability_status']) ?>"><?= e(package_status_label($package['availability_status'])) ?></span>
            <div class="summary-price"><?= number_format((float)$package['weekly_price'], 0) ?> <?= e($package['currency']) ?><span>/week</span></div>
            <p>Monthly: <?= number_format((float)$package['monthly_price'], 0) ?> <?= e($package['currency']) ?></p>
            <p>Deposit: <?= number_format((float)$package['deposit_amount'], 0) ?> <?= e($package['currency']) ?></p>
        </aside>
    </div>
</section>

<section class="section">
    <div class="container package-info-grid">
        <div class="app-card">
            <h2>Included Items</h2>
            <p><?= nl2br(e($package['included_items'])) ?></p>
        </div>
        <div class="app-card">
            <h2>Requirements</h2>
            <p><?= nl2br(e($package['requirements'])) ?></p>
        </div>
        <div class="app-card">
            <h2>Terms</h2>
            <p><?= nl2br(e($package['terms'])) ?></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
