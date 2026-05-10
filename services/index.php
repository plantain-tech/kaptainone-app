<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Courier Rental Services';
$pageDescription = 'Kaptain One services for Warsaw courier e-bike rentals, scooter rentals, onboarding support, and asset owner listings.';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Services</span>
        <h1>Courier Rental Services in Warsaw</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            A focused MVP service list for couriers who need e-bikes or scooters, and owners who want to rent them out.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="card-grid card-grid-4">
            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">E-bike Rentals</h3>
                <p>Work-ready e-bike rental options for Warsaw couriers delivering with Wolt, Glovo, Bolt Food, Uber Eats, and Stuart.</p>
            </article>

            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Scooter Rentals</h3>
                <p>Scooter rental packages for couriers who need efficient city movement and clear weekly terms.</p>
            </article>

            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Courier Onboarding Support</h3>
                <p>Profile setup, package applications, requirements, and approval status organized in one account.</p>
            </article>

            <article class="card">
                <div class="card-icon"></div>
                <h3 class="card-title">Asset Owner Listing Support</h3>
                <p>A focused path for Warsaw owners to prepare idle e-bikes and scooters for courier rentals.</p>
            </article>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <span class="section-kicker">Get Started</span>
        <h2>Find Courier Equipment or List an Asset</h2>
        <div class="hero-cta">
            <a href="../packages.php" class="btn btn-primary btn-large">View Packages</a>
            <a href="../register.php" class="btn btn-secondary btn-large">Create Account</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
