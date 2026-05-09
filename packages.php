<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/app_data.php';

$pageTitle = 'Equipment Packages';
$pageDescription = 'Browse Kaptain One leasing packages for gig workers in Warsaw.';
$packages = get_active_packages();
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Leasing Packages</span>
        <h1>Work-Ready Equipment Packages</h1>
        <p class="contact-intro-copy">Browse e-bikes, courier gear, seasonal protection, and vehicle leasing pathways for gig work in Warsaw.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="package-grid">
            <?php foreach ($packages as $package): ?>
                <?php require __DIR__ . '/includes/package-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
