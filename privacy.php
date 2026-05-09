<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Privacy Policy';
$pageDescription = 'Privacy policy for Kaptain One.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Privacy</span>
        <h1>Privacy Policy</h1>
        <p class="contact-intro-copy">How Kaptain One handles information submitted through this website.</p>
    </div>
</section>

<section class="section">
    <div class="container container-narrow">
        <div class="card">
            <h2 style="margin-bottom: 1rem;">Information We Collect</h2>
            <p>We collect the information you submit through our contact, demo request, and partner inquiry forms, such as your name, email address, company details, service interests, and message content.</p>

            <h2 style="margin: 2rem 0 1rem;">How We Use It</h2>
            <p>We use submitted information to respond to inquiries, schedule demos, evaluate partnership requests, and improve our transportation services.</p>

            <h2 style="margin: 2rem 0 1rem;">Contact</h2>
            <p>For privacy questions, contact us at <a href="mailto:<?= e(site('email')) ?>" style="color: var(--accent-gold);"><?= e(site('email')) ?></a>.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
