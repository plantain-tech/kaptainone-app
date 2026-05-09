<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Airport Transfers';
$pageDescription = 'Professional airport transfer service with flight tracking, meet & greet, and premium vehicles.';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Services</span>
        <h1>Airport Transfers</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            Seamless arrivals and departures with professional chauffeurs, flight tracking, and premium vehicles.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="audience-block">
            <div class="audience-visual"></div>
            <div class="audience-content">
                <h3>Stress-Free Airport Transportation</h3>
                <p>Start or end your journey with confidence. Our professional chauffeurs monitor flight times, handle luggage, and ensure timely pickups and drop-offs at all major airports.</p>
                
                <ul class="feature-list">
                    <li>Real-time flight tracking</li>
                    <li>Meet &amp; greet service</li>
                    <li>60 minutes complimentary wait time</li>
                    <li>Professional luggage assistance</li>
                    <li>Premium vehicles</li>
                </ul>
                
                <a href="../partners/request-a-demo.php" class="btn btn-primary" style="margin-top: 1rem;">Book Your Transfer</a>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Benefits</span>
            <h2 class="section-title">Why Choose Kaptain One</h2>
        </div>
        
        <div class="card-grid card-grid-3">
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Flight Tracking</h3>
                <p>We monitor your flight in real-time and adjust pickup times automatically for delays or early arrivals.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Meet &amp; Greet</h3>
                <p>Your chauffeur meets you inside the terminal with a name sign, ready to assist with luggage.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Premium Fleet</h3>
                <p>Luxury sedans, SUVs, and executive vehicles maintained to the highest standards.</p>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Use Cases</span>
            <h2 class="section-title">Perfect For</h2>
        </div>
        
        <div class="card-grid card-grid-4">
            <article class="card">
                <h3 class="card-title">Executive Travel</h3>
                <p>Reliable transportation for business travelers who value punctuality and professionalism.</p>
            </article>
            
            <article class="card">
                <h3 class="card-title">Family Travel</h3>
                <p>Spacious SUVs and professional service for families with luggage and children.</p>
            </article>
            
            <article class="card">
                <h3 class="card-title">VIP Guests</h3>
                <p>White-glove service for discerning travelers who expect the best.</p>
            </article>
            
            <article class="card">
                <h3 class="card-title">Group Transfers</li>
                <p>Coordinated pickups for corporate groups, wedding parties, and events.</p>
            </article>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <span class="section-kicker">Ready to Book?</span>
        <h2>Experience Premium Airport Transfers</h2>
        <p>Let us handle your next airport transfer with professional service you can trust.</p>
        <div class="hero-cta">
            <a href="../partners/request-a-demo.php" class="btn btn-primary btn-large">Request a Quote</a>
            <a href="../partners/become-a-partner.php" class="btn btn-secondary btn-large">Partner With Us</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
