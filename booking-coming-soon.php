<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Booking Requests Are Live';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Booking requests</span>
        <h1>Booking requests are now available</h1>
        <p class="contact-intro-copy">Choose an active Warsaw e-bike or scooter listing and send the owner a rental request.</p>
        <a href="<?= base_url() ?>/listings.php" class="btn btn-primary">Back to listings</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
