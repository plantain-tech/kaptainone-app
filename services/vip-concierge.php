<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'VIP Concierge';
$pageDescription = 'White-glove transportation service for discerning travelers who expect the extraordinary.';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Services</span>
        <h1>VIP Concierge</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            White-glove transportation for discerning travelers who expect the extraordinary.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="audience-block">
            <div class="audience-visual"></div>
            <div class="audience-content">
                <h3>Beyond Transportation</h3>
                <p>Our VIP Concierge service delivers a seamless luxury experience from door to door. Dedicated account management, bespoke arrangements, and anticipatory service that exceeds expectations.</p>
                
                <ul class="feature-list">
                    <li>Dedicated account manager</li>
                    <li>Bespoke itinerary planning</li>
                    <li>Luxury vehicle fleet access</li>
                    <li>24/7 personal concierge line</li>
                    <li>Global destination support</li>
                </ul>
                
                <a href="../partners/request-a-demo.php" class="btn btn-primary" style="margin-top: 1rem;">Inquire About VIP Service</a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">VIP Benefits</span>
            <h2 class="section-title">The VIP Experience</h2>
        </div>
        
        <div class="card-grid card-grid-3">
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Dedicated Support</h3>
                <p>Your personal account manager available 24/7 for any request or adjustment.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Premium Fleet</h3>
                <p>Access to our finest vehicles including luxury sedans, SUVs, and executive vans.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Global Reach</h3>
                <p>Consistent VIP service in major cities worldwide through our trusted network.</p>
            </article>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <span class="section-kicker">VIP Access</span>
        <h2>Experience True Luxury</h2>
        <div class="hero-cta">
            <a href="../partners/request-a-demo.php" class="btn btn-primary btn-large">Request VIP Consultation</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
