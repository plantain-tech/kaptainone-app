<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Terms of Use';
$pageDescription = 'Terms of use for Kaptain One.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Terms</span>
        <h1>Terms of Use</h1>
        <p class="contact-intro-copy">Basic terms for using the Kaptain One website.</p>
    </div>
</section>

<section class="section">
    <div class="container container-narrow">
        <div class="card">
            <h2 style="margin-bottom: 1rem;">Website Use</h2>
            <p>This website provides information about Kaptain One services and lets visitors submit inquiries. Submitting a form does not create a confirmed booking or service agreement.</p>

            <h2 style="margin: 2rem 0 1rem;">Service Requests</h2>
            <p>Demo, contact, and partnership requests are reviewed by our team. Service availability, pricing, and partnership terms are confirmed separately.</p>

            <h2 style="margin: 2rem 0 1rem;">Contact</h2>
            <p>For terms questions, contact us at <a href="mailto:<?= e(site('email')) ?>" style="color: var(--accent-gold);"><?= e(site('email')) ?></a>.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
