<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Booking Requests Coming Soon';
$pageDescription = 'Booking requests for Kaptain One rentals are launching soon.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Booking flow</span>
        <h1>Booking requests are launching Day 5</h1>
        <p class="contact-intro-copy">Booking requests are launching Day 5 (May 14, 2026). Save this listing — you'll be able to request it then.</p>
        <a href="<?= base_url() ?>/listings.php" class="btn btn-primary">Back to listings</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
