<!-- ARCHIVED: out of MVP scope, hidden from navigation on 2026-05-11 -->
<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/form_handler.php';

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = FormHandler::processDemoForm($_POST);
}

$pageTitle = 'Request a Demo';
$pageDescription = 'See Kaptain One in action. Request a personalized demo of our premium transportation platform.';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Demo</span>
        <h1>Request a Demo</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            See how Kaptain One can transform your transportation experience.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="content-grid">
            <div>
                <h3 style="margin-bottom: 1.5rem;">What to Expect</h3>
                
                <ul class="feature-list" style="margin-bottom: 2rem;">
                    <li>Personalized platform walkthrough</li>
                    <li>Q&amp;A with our solutions team</li>
                    <li>Pricing and partnership discussion</li>
                    <li>Custom integration options</li>
                    <li>No obligation, no pressure</li>
                </ul>
                
                <p style="color: var(--text-secondary);">
                    Fill out the form and we'll schedule a demo at your convenience. Typical demos run 30-45 minutes.
                </p>
            </div>
            
            <div class="form-card">
                <?php if ($result): ?>
                    <?php if ($result['success']): ?>
                        <div class="alert alert-success">
                            <strong>Thank you!</strong> Your demo request has been received. We'll contact you within 24 hours to schedule.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-error">
                            <strong>Please correct the following errors:</strong>
                            <ul style="margin-top: 0.5rem; padding-left: 1.25rem;">
                                <?php foreach ($result['errors'] as $field => $error): ?>
                                    <li><?= e($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <form method="post" action="">
                    <input type="text" name="website_url" style="display:none;" tabindex="-1" autocomplete="off">
                    
                    <div class="form-grid two">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="full_name" class="form-control" required value="<?= e($_POST['full_name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Company</label>
                            <input type="text" name="company" class="form-control" value="<?= e($_POST['company'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-grid two">
                        <div class="form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?= e($_POST['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-grid two">
                        <div class="form-group">
                            <label>Company Size</label>
                            <select name="company_size" class="form-control">
                                <option value="">Select size</option>
                                <option value="1-10" <?= ($_POST['company_size'] ?? '') === '1-10' ? 'selected' : '' ?>>1-10 employees</option>
                                <option value="11-50" <?= ($_POST['company_size'] ?? '') === '11-50' ? 'selected' : '' ?>>11-50 employees</option>
                                <option value="51-200" <?= ($_POST['company_size'] ?? '') === '51-200' ? 'selected' : '' ?>>51-200 employees</option>
                                <option value="201-500" <?= ($_POST['company_size'] ?? '') === '201-500' ? 'selected' : '' ?>>201-500 employees</option>
                                <option value="500+" <?= ($_POST['company_size'] ?? '') === '500+' ? 'selected' : '' ?>>500+ employees</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Service Interest *</label>
                            <select name="service_interest" class="form-control" required>
                                <option value="">Select service</option>
                                <option value="airport" <?= ($_POST['service_interest'] ?? '') === 'airport' ? 'selected' : '' ?>>Airport Transfers</option>
                                <option value="corporate" <?= ($_POST['service_interest'] ?? '') === 'corporate' ? 'selected' : '' ?>>Corporate Travel</option>
                                <option value="concierge" <?= ($_POST['service_interest'] ?? '') === 'concierge' ? 'selected' : '' ?>>Concierge Service</option>
                                <option value="events" <?= ($_POST['service_interest'] ?? '') === 'events' ? 'selected' : '' ?>>Event Transportation</option>
                                <option value="fleet" <?= ($_POST['service_interest'] ?? '') === 'fleet' ? 'selected' : '' ?>>Fleet Partnership</option>
                                <option value="other" <?= ($_POST['service_interest'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Preferred Contact Method</label>
                        <select name="contact_method" class="form-control">
                            <option value="email" <?= ($_POST['contact_method'] ?? 'email') === 'email' ? 'selected' : '' ?>>Email</option>
                            <option value="phone" <?= ($_POST['contact_method'] ?? '') === 'phone' ? 'selected' : '' ?>>Phone</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control" placeholder="Tell us about your needs and what you'd like to see in the demo..."><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large" style="width: 100%;">Request Demo</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
