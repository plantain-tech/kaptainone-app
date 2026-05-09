<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About';
$pageDescription = 'Learn about Kaptain One - premium ground transportation for executives, VIPs, and discerning travelers.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">About</span>
        <h1>Premium Transportation, Redefined</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            Kaptain One delivers professional chauffeur service that meets the expectations of executives, VIPs, and discerning travelers.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="audience-block">
            <div class="audience-visual"></div>
            <div class="audience-content">
                <h3>Our Story</h3>
                <p>Kaptain One was founded on a simple belief: ground transportation should be as refined as the travelers it serves. We combine professional chauffeurs, premium vehicles, and modern technology to create a seamless experience.</p>
                
                <p>Today, we serve corporate clients, luxury hotels, travel advisors, and individual travelers who demand excellence in every detail.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Our Values</span>
            <h2 class="section-title">What We Stand For</h2>
        </div>
        
        <div class="card-grid card-grid-4">
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Excellence</h3>
                <p>We pursue perfection in every interaction, from booking to drop-off.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Reliability</h3>
                <p>On-time, every time. Your schedule is our priority.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Discretion</h3>
                <p>Professional service with complete confidentiality and respect for privacy.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Innovation</h3>
                <p>Modern technology meets traditional service excellence.</p>
            </article>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <span class="section-kicker">Join Us</span>
        <h2>Experience The Kaptain One Difference</h2>
        <div class="hero-cta">
            <a href="partners/request-a-demo.php" class="btn btn-primary btn-large">Request a Demo</a>
            <a href="partners/become-a-partner.php" class="btn btn-secondary btn-large">Become a Partner</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
