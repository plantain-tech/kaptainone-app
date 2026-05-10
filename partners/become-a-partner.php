<!-- ARCHIVED: out of MVP scope, hidden from navigation on 2026-05-11 -->
<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/form_handler.php';

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = FormHandler::processPartnerForm($_POST);
}

$pageTitle = 'Become a Partner';
$pageDescription = 'Join the Kaptain One network. Partner with us to offer premium transportation to your clients.';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Partners</span>
        <h1>Become a Partner</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            Join our network of premium transportation providers and corporate partners.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="content-grid">
            <div>
                <h3 style="margin-bottom: 1.5rem;">Why Partner With Kaptain One?</h3>
                
                <ul class="feature-list" style="margin-bottom: 2rem;">
                    <li>Access to premium corporate clients</li>
                    <li>Technology integration support</li>
                    <li>Reliable payment terms</li>
                    <li>Professional brand association</li>
                    <li>24/7 operational support</li>
                </ul>
                
                <p style="color: var(--text-secondary);">
                    Fill out the form and our team will contact you within 24 hours to discuss partnership opportunities.
                </p>
            </div>
            
            <div class="form-card">
                <?php if ($result): ?>
                    <?php if ($result['success']): ?>
                        <div class="alert alert-success">
                            <strong>Thank you!</strong> Your partnership inquiry has been received. We'll contact you within 24 hours.
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
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?= e($_POST['company_name'] ?? '') ?>">
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
                    
                    <div class="form-group">
                        <label>Business Type *</label>
                        <select name="business_type" class="form-control" required>
                            <option value="">Select business type</option>
                            <option value="travel_agency" <?= ($_POST['business_type'] ?? '') === 'travel_agency' ? 'selected' : '' ?>>Travel Agency</option>
                            <option value="concierge" <?= ($_POST['business_type'] ?? '') === 'concierge' ? 'selected' : '' ?>>Concierge Service</option>
                            <option value="hotel" <?= ($_POST['business_type'] ?? '') === 'hotel' ? 'selected' : '' ?>>Hotel / Hospitality</option>
                            <option value="corporate" <?= ($_POST['business_type'] ?? '') === 'corporate' ? 'selected' : '' ?>>Corporate Travel</option>
                            <option value="fleet_provider" <?= ($_POST['business_type'] ?? '') === 'fleet_provider' ? 'selected' : '' ?>>Fleet / Driver Provider</option>
                            <option value="other" <?= ($_POST['business_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-grid two">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="<?= e($_POST['city'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" class="form-control" value="<?= e($_POST['country'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Website</label>
                        <input type="url" name="website" class="form-control" placeholder="https://" value="<?= e($_POST['website'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" class="form-control" placeholder="Tell us about your business and how you'd like to partner..."><?= e($_POST['message'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large" style="width: 100%;">Submit Partnership Inquiry</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
