<?php
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Google Sign In Setup Required';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <span class="section-kicker">OAuth Setup</span>
        <h1>Google Sign In Requires Credentials</h1>
        <p class="contact-intro-copy">Add Google OAuth credentials in the Hostinger environment or config placeholders to complete this flow.</p>
        <a href="<?= base_url() ?>/login.php" class="btn btn-primary btn-large" style="margin-top: 2rem;">Back to Sign In</a>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
