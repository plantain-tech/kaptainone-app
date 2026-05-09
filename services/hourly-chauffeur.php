<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Hourly Chauffeur';
$pageDescription = 'Flexible hourly chauffeur service for meetings, events, and as-directed transportation.';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Services</span>
        <h1>Hourly Chauffeur</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            Flexible as-directed service for meetings, events, and multiple destinations.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="audience-block">
            <div class="audience-visual"></div>
            <div class="audience-content">
                <h3>Your Car, Your Schedule</h3>
                <p>Have a professional chauffeur and premium vehicle at your disposal. Perfect for business meetings, shopping trips, special events, or exploring the city in comfort.</p>
                
                <ul class="feature-list">
                    <li>Minimum 3-hour booking</li>
                    <li>Multiple stops included</li>
                    <li>Professional chauffeur</li>
                    <li>Luxury sedan or SUV</li>
                    <li>Waiting time included</li>
                </ul>
                
                <a href="../partners/request-a-demo.php" class="btn btn-primary" style="margin-top: 1rem;">Book Hourly Service</a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Perfect For</span>
            <h2 class="section-title">Hourly Service Use Cases</h2>
        </div>
        
        <div class="card-grid card-grid-4">
            <article class="card">
                <h3 class="card-title">Business Meetings</h3>
                <p>Multiple client meetings across the city with a dedicated car waiting.</p>
            </article>
            
            <article class="card">
                <h3 class="card-title">Shopping &amp; Events</h3>
                <p>Attend events, shop, and dine with door-to-door service all day.</p>
            </article>
            
            <article class="card">
                <h3 class="card-title">City Tours</h3>
                <p>Explore with a knowledgeable local chauffeur at your own pace.</p>
            </article>
            
            <article class="card">
                <h3 class="card-title">Special Occasions</h3>
                <p>Weddings, anniversaries, and celebrations in style.</p>
            </article>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <span class="section-kicker">Book Now</span>
        <h2>Your Personal Chauffeur Awaits</h2>
        <div class="hero-cta">
            <a href="../partners/request-a-demo.php" class="btn btn-primary btn-large">Request a Quote</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
