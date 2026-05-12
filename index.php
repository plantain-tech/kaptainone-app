<?php
/**
 * Kaptain One - Homepage
 */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Warsaw E-Bike & Scooter Rentals for Couriers';
$pageDescription = 'Kaptain One connects Warsaw couriers with e-bike and scooter rentals for Wolt, Glovo, Bolt Food, Uber Eats, and Stuart work.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <span class="hero-kicker">Warsaw Courier Equipment Marketplace</span>
        <h1 class="hero-title">Rent an e-bike or scooter in Warsaw and start delivering today</h1>
        <p class="hero-subtitle">
            Flexible e-bike and scooter rentals for Warsaw couriers, with simple applications,
            clear terms, and owner payout options.
        </p>
        <div class="hero-cta">
            <a href="register.php?role=owner" class="btn btn-primary btn-large">List Your E-Bike or Scooter</a>
            <a href="listings.php" class="btn btn-secondary btn-large">View Rental Packages</a>
        </div>
    </div>
</section>

<!-- Trust Strip -->
<section class="trust-strip">
    <div class="container">
        <p class="trust-label">Equipment ready for Wolt, Glovo, Bolt Food, Uber Eats, and Stuart couriers in Warsaw.</p>
    </div>
</section>

<!-- Solutions Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Marketplace</span>
            <h2 class="section-title">Built For Warsaw Delivery Work</h2>
            <p class="section-subtitle">
                Kaptain One helps couriers find work-ready e-bikes and scooters, while helping local
                asset owners earn rental income from equipment that would otherwise sit idle.
            </p>
        </div>
        <div class="card-grid card-grid-3">
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">For Couriers</h3>
                <p>Browse e-bike and scooter rental options for Wolt, Glovo, Bolt Food, Uber Eats, and Stuart work in Warsaw.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">For Asset Owners</h3>
                <p>List idle e-bikes or scooters and choose payout timing that fits your cash-flow needs.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">For Warsaw</h3>
                <p>A focused local marketplace for courier equipment, pickup coordination, and rental support.</p>
            </article>
        </div>
    </div>
</section>

<!-- Audience Blocks -->
<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="audience-grid">
            <div class="audience-block">
                <div class="audience-visual"></div>
                <div class="audience-content">
                    <span class="text-uppercase" style="color: var(--accent-gold); margin-bottom: 1rem; display: block;">Warsaw Couriers</span>
                    <h3>Get Work-Ready Equipment Faster</h3>
                    <p>Apply for an e-bike or scooter rental, see clear weekly pricing, and keep your next steps organized in your account.</p>
                    <ul class="feature-list">
                        <li>E-bike and scooter rental packages</li>
                        <li>Courier gear and weather protection</li>
                        <li>Application tracking from review to pickup</li>
                    </ul>
                </div>
            </div>
            
            <div class="audience-block">
                <div class="audience-visual"></div>
                <div class="audience-content">
                    <span class="text-uppercase" style="color: var(--accent-gold); margin-bottom: 1rem; display: block;">Asset Owners</span>
                    <h3>Turn Idle Equipment Into Rental Income</h3>
                    <p>Prepare your e-bike or scooter for courier rentals in Warsaw and choose payout timing after completed transactions.</p>
                    <ul class="feature-list">
                        <li>Flexible payout timing: 1, 3, 7, 14, or 30 days</li>
                        <li>Focused Warsaw courier demand</li>
                        <li>Simple listing and support workflow</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Why Kaptain One</span>
            <h2 class="section-title">Focused, Local, Practical</h2>
        </div>
        
        <div class="card-grid card-grid-4">
            <article class="card card-feature">
                <div class="card-icon"></div>
                <h3 class="card-title">Warsaw Only</h3>
                <p>A narrow local launch keeps pickup, support, and rental expectations clear.</p>
            </article>
            
            <article class="card card-feature">
                <div class="card-icon"></div>
                <h3 class="card-title">E-Bikes &amp; Scooters</h3>
                <p>The MVP focuses only on equipment couriers can use for delivery shifts.</p>
            </article>
            
            <article class="card card-feature">
                <div class="card-icon"></div>
                <h3 class="card-title">Clear Terms</h3>
                <p>Weekly pricing, deposits, requirements, and application status are easy to review.</p>
            </article>
            
            <article class="card card-feature">
                <div class="card-icon"></div>
                <h3 class="card-title">Flexible Payouts</h3>
                <p>Asset owners can plan around payout timing options after rental transactions complete.</p>
            </article>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-premium-band section">
    <div class="container services-premium-band__container">
        <div class="services-premium-band__intro">
            <span class="services-premium-band__eyebrow">Core Services</span>
            <h2 class="services-premium-band__title">Courier Rentals, Built Around Warsaw</h2>
            <p class="services-premium-band__text">
                E-bike rentals, scooter rentals, onboarding support, and asset-owner listing support
                for the first version of the Kaptain One marketplace.
            </p>
        </div>

        <div class="services-premium-band__grid">
            <article class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">Courier Equipment</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">E-Bike Rentals</h3>
                    <p class="executive-card__description">Work-ready e-bike options for Warsaw delivery couriers.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">View Packages</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </article>

            <article class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">Flexible Mobility</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">Scooter Rentals</h3>
                    <p class="executive-card__description">Scooter rental packages for couriers who need efficient city movement.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">View Packages</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </article>

            <article class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">Getting Started</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">Courier Onboarding Support</h3>
                    <p class="executive-card__description">Profile, requirements, and application steps organized in one account.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">Create Account</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </article>

            <article class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">Owner Income</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">Asset Owner Listing Support</h3>
                    <p class="executive-card__description">A focused path for listing idle e-bikes and scooters for courier rentals.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">Create Account</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <span class="section-kicker">Warsaw MVP</span>
        <h2>Ready to Start With the Right Equipment?</h2>
        <p>Browse e-bike and scooter rental packages for courier work in Warsaw.</p>
        <div class="hero-cta">
            <a href="listings.php" class="btn btn-primary btn-large">View Rental Packages</a>
            <a href="register.php" class="btn btn-secondary btn-large">Create Account</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
