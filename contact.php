<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/contact_handler.php';

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = ContactHandler::process($_POST);
}

$pageTitle = 'Contact';
$pageDescription = 'Get in touch with Kaptain One. Request a quote, partnership information, or general inquiries.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Contact</span>
        <h1>Get In Touch</h1>
        <p class="contact-intro-copy">
            Have questions? We'd love to hear from you. Reach out for quotes, partnerships, or general inquiries.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="content-grid contact-section-grid">
            <div class="contact-info-col">
                <div class="contact-info-card">
                    <h3 class="contact-info-title">Contact Information</h3>

                    <div class="contact-info-stack">
                        <div class="contact-info-item">
                            <span class="contact-info-label">Phone</span>
                            <a href="tel:<?= e(site('phone_link')) ?>" class="contact-info-value"><?= e(site('phone')) ?></a>
                        </div>

                        <div class="contact-info-item">
                            <span class="contact-info-label">Email</span>
                            <a href="mailto:<?= e(site('email')) ?>" class="contact-info-value"><?= e(site('email')) ?></a>
                        </div>

                        <div class="contact-info-item">
                            <span class="contact-info-label">Location</span>
                            <span><?= e(site('address')) ?></span>
                        </div>
                    </div>

                    <div class="contact-quick-links">
                        <h4>Quick Links</h4>
                        <a href="partners/become-a-partner.php">Become a Partner</a>
                        <a href="partners/request-a-demo.php">Request a Demo</a>
                        <a href="services/">View Services</a>
                    </div>
                </div>
            </div>

            <div class="form-card contact-form-card contact-form-col">
                <h3 style="margin-bottom: 1.5rem;">Send a Message</h3>

                <?php if ($result): ?>
                    <?php if ($result['success']): ?>
                        <div class="alert alert-success">
                            <strong>Thank you!</strong> Your message has been received. We'll contact you shortly.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-error">
                            <strong>Please correct the following errors:</strong>
                            <ul style="margin-top: 0.5rem; padding-left: 1.25rem;">
                                <?php foreach ($result['errors'] as $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="form-grid two">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" class="form-control" required value="<?= e($_POST['name'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Subject</label>
                        <select name="subject" class="form-control">
                            <?php $selectedSubject = $_POST['subject'] ?? 'General Inquiry'; ?>
                            <option <?= $selectedSubject === 'General Inquiry' ? 'selected' : '' ?>>General Inquiry</option>
                            <option <?= $selectedSubject === 'Quote Request' ? 'selected' : '' ?>>Quote Request</option>
                            <option <?= $selectedSubject === 'Partnership' ? 'selected' : '' ?>>Partnership</option>
                            <option <?= $selectedSubject === 'Support' ? 'selected' : '' ?>>Support</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" class="form-control" rows="5" required><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large" style="width: 100%;">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
