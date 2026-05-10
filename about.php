<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About Kaptain One';
$pageDescription = 'Learn about Kaptain One, a Warsaw courier equipment marketplace for e-bike and scooter rentals.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">About</span>
        <h1>Courier Equipment, Made Practical</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            Kaptain One connects Warsaw couriers with local e-bike and scooter rental options built for delivery work.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="audience-block">
            <div class="audience-visual"></div>
            <div class="audience-content">
                <h3>Our Story</h3>
                <p>Kaptain One was founded on a simple belief: delivery work is easier when reliable equipment is simple to access. We connect Warsaw couriers with e-bikes, scooters, clear rental terms, and a practical support workflow.</p>
                
                <p>Today, we focus on couriers working with Wolt, Glovo, Bolt Food, Uber Eats, and Stuart, while helping local asset owners earn income from idle e-bikes and scooters.</p>
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
                <p>We keep every rental step clear, polished, and easy to complete.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Reliability</h3>
                <p>Couriers need equipment they can count on before a shift starts.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Trust</h3>
                <p>Clear terms, profile checks, and careful handling of personal information.</p>
            </article>
            
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Innovation</h3>
                <p>A modern platform for rental applications, equipment status, and support.</p>
            </article>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <span class="section-kicker">Join Us</span>
        <h2>Start With the Right Courier Equipment</h2>
        <div class="hero-cta">
            <a href="packages.php" class="btn btn-primary btn-large">View Packages</a>
            <a href="register.php" class="btn btn-secondary btn-large">Create Account</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
