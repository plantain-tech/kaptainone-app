<?php
/**
 * Kaptain One - Homepage
 */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'Premium Ground Transportation';
$pageDescription = 'Kaptain One delivers executive chauffeur service for airport transfers, corporate travel, and VIP transportation.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <span class="hero-kicker">Premium Ground Transportation</span>
        <h1 class="hero-title">Move With Purpose</h1>
        <p class="hero-subtitle">
            Executive rides. Airport transfers. Event logistics. A premium experience 
            for guests who matter. Professional chauffeur service with modern convenience.
        </p>
        <div class="hero-cta">
            <a href="partners/become-a-partner.php" class="btn btn-primary btn-large">Become a Partner</a>
            <a href="partners/request-a-demo.php" class="btn btn-secondary btn-large">Request a Demo</a>
        </div>
    </div>
</section>

<!-- Trust Strip -->
<section class="trust-strip">
    <div class="container">
        <p class="trust-label">Trusted by Leading Brands</p>
        <div class="trust-logos">
            <span style="font-weight: 600;">HILTON</span>
            <span style="font-weight: 600;">MARRIOTT</span>
            <span style="font-weight: 600;">FOUR SEASONS</span>
            <span style="font-weight: 600;">DELTA</span>
            <span style="font-weight: 600;">AMEX</span>
        </div>
    </div>
</section>

<!-- Solutions Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Solutions</span>
            <h2 class="section-title">Built For Your Business</h2>
            <p class="section-subtitle">
                Whether you're a travel advisor, hotel concierge, or corporate travel manager, 
                we provide premium transportation solutions that elevate your service.
            </p>
        </div>
        <div class="card-grid card-grid-3">
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">For Travel Agents &amp; Concierges</h3>
                <p>Offer your clients premium ground transportation with a booking experience that reflects your brand standards.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">For Transportation Providers</h3>
                <p>Join our network of premium drivers and fleets. Expand your reach with corporate clients and luxury hospitality partners.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">For Corporate Travel</h3>
                <p>Simplify executive transportation with centralized booking, real-time tracking, and detailed reporting.</p>
            </article>
        </div>
    </div>
</section>

<!-- Audience Blocks -->
<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="audience-grid">
            <!-- Block 1 -->
            <div class="audience-block">
                <div class="audience-visual"></div>
                <div class="audience-content">
                    <span class="text-uppercase" style="color: var(--accent-gold); margin-bottom: 1rem; display: block;">Travel Agents &amp; Concierges</span>
                    <h3>Elevate Your Guest Experience</h3>
                    <p>Offer polished airport transfers, hourly chauffeur service, and VIP movement with a booking process that feels as premium as the ride itself.</p>
                    <ul class="feature-list">
                        <li>White-label ready presentation</li>
                        <li>Fast quote and request workflow</li>
                        <li>Executive-class service standards</li>
                    </ul>
                </div>
            </div>
            
            <!-- Block 2 -->
            <div class="audience-block">
                <div class="audience-visual"></div>
                <div class="audience-content">
                    <span class="text-uppercase" style="color: var(--accent-gold); margin-bottom: 1rem; display: block;">Corporate Travel</span>
                    <h3>Executive Transportation Made Simple</h3>
                    <p>Support assistants, coordinators, and business travelers with clear service options and a strong premium image.</p>
                    <ul class="feature-list">
                        <li>Hourly and point-to-point service</li>
                        <li>Professional lead capture</li>
                        <li>Built for trust and conversion</li>
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
            <h2 class="section-title">The Premium Difference</h2>
        </div>
        
        <div class="card-grid card-grid-4">
            <article class="card card-feature">
                <div class="card-icon"></div>
                <h3 class="card-title">Duty of Care</h3>
                <p>Vetted drivers, real-time tracking, and 24/7 support for complete peace of mind.</p>
            </article>
            
            <article class="card card-feature">
                <div class="card-icon"></div>
                <h3 class="card-title">Global Access</h3>
                <p>Premium service in major markets worldwide with consistent quality standards.</p>
            </article>
            
            <article class="card card-feature">
                <div class="card-icon"></div>
                <h3 class="card-title">Management Tools</h3>
                <p>Centralized booking, expense controls, and comprehensive reporting dashboards.</p>
            </article>
            
            <article class="card card-feature">
                <div class="card-icon"></div>
                <h3 class="card-title">Open Platform</h3>
                <p>API-ready architecture for seamless integrations and white-label solutions.</p>
            </article>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-premium-band section">
    <div class="container services-premium-band__container">
        <div class="services-premium-band__intro">
            <span class="services-premium-band__eyebrow">Signature Services</span>
            <h2 class="services-premium-band__title">Executive Mobility, Refined for Every Journey</h2>
            <p class="services-premium-band__text">
                Premium chauffeur services for airport transfers, corporate movement, event logistics, and discreet VIP travel,
                delivered with precision, polish, and executive-level care.
            </p>
        </div>

        <div class="services-premium-band__grid">
            <a href="services/airport-transfers.php" class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">Arrivals &amp; Departures</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">Airport Transfers</h3>
                    <p class="executive-card__description">Flight-aware chauffeur service with polished meet-and-greet execution for seamless airport movement.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">Explore Service</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </a>

            <a href="services/executive-transportation.php" class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">Business Class</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">Executive Transportation</h3>
                    <p class="executive-card__description">Refined black car service for executives, clients, and leadership teams who expect elevated standards.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">Explore Service</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </a>

            <a href="services/corporate-travel.php" class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">Enterprise Mobility</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">Corporate Travel</h3>
                    <p class="executive-card__description">Structured transport solutions for assistants, teams, and premium corporate travel programs.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">Explore Service</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </a>

            <a href="services/hourly-chauffeur.php" class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">As Directed</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">Hourly Chauffeur</h3>
                    <p class="executive-card__description">Flexible executive coverage for meetings, roadshows, city movement, and multi-stop itineraries.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">Explore Service</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </a>

            <a href="services/event-transportation.php" class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">Coordinated Logistics</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">Event Transportation</h3>
                    <p class="executive-card__description">Elevated guest transportation planning for conferences, private events, and VIP schedules.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">Explore Service</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </a>

            <a href="services/vip-concierge.php" class="executive-card">
                <div class="executive-card__glow"></div>
                <div class="executive-card__head">
                    <span class="executive-card__icon" aria-hidden="true">✦</span>
                    <span class="executive-card__label">White-Glove Service</span>
                </div>
                <div class="executive-card__body">
                    <h3 class="executive-card__title">VIP Concierge</h3>
                    <p class="executive-card__description">Discreet, high-touch travel support for guests who value privacy, precision, and polish.</p>
                </div>
                <div class="executive-card__footer">
                    <span class="executive-card__cta">Explore Service</span>
                    <span class="executive-card__arrow" aria-hidden="true">→</span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <span class="section-kicker">Ready to Get Started?</span>
        <h2>Ready to Elevate Your Transportation?</h2>
        <p>Join companies who trust Kaptain One for premium ground travel.</p>
        <div class="hero-cta">
            <a href="partners/become-a-partner.php" class="btn btn-primary btn-large">Become a Partner</a>
            <a href="partners/request-a-demo.php" class="btn btn-secondary btn-large">Request a Demo</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
