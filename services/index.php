<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Services';
$pageDescription = 'Premium chauffeur services for airport transfers, executive travel, corporate programs, and VIP transportation.';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Services</span>
        <h1>Premium Transportation Services</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            Professional chauffeur services designed for executives, VIPs, and discerning travelers.
        </p>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/services-section.php'; ?>

<section class="cta-section">
    <div class="container">
        <span class="section-kicker">Ready to Book?</span>
        <h2>Experience Premium Transportation</h2>
        <div class="hero-cta">
            <a href="../partners/request-a-demo.php" class="btn btn-primary btn-large">Request a Quote</a>
            <a href="../partners/become-a-partner.php" class="btn btn-secondary btn-large">Partner With Us</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
