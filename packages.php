<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/app_data.php';

$pageTitle = 'Warsaw E-Bike & Scooter Packages';
$pageDescription = 'Browse Kaptain One e-bike and scooter rental packages for Warsaw couriers.';
$packages = get_active_packages();
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Leasing Packages</span>
        <h1>Work-Ready Equipment Packages</h1>
        <p class="contact-intro-copy">Browse e-bikes, scooters, courier gear, and weather protection packages for delivery work in Warsaw.</p>
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
