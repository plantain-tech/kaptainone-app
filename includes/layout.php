<?php
require_once __DIR__ . '/data.php';

function render_head(string $title, string $description = ''): void {
    global $site;
    $fullTitle = $title ? $title . ' | ' . $site['name'] : $site['name'];
    $description = $description ?: $site['tagline'];
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$fullTitle}</title>
    <meta name="description" content="{$description}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
HTML;
}

function render_header(string $active = ''): void {
    global $nav, $site;
    echo '<div class="topbar">Warsaw e-bike and scooter rentals for delivery couriers.</div>';
    echo '<header class="site-header"><div class="container nav-wrap">';
    echo '<a class="brand" href="index.php">' . htmlspecialchars($site['name']) . '</a>';
    echo '<nav class="nav">';
    foreach ($nav as $item) {
        $isActive = $active === $item['href'] ? ' is-active' : '';
        echo '<a class="nav-link' . $isActive . '" href="' . htmlspecialchars($item['href']) . '">' . htmlspecialchars($item['label']) . '</a>';
    }
    echo '</nav>';
    echo '<div class="nav-actions">';
    echo '<a class="btn btn-secondary" href="packages.php">View Packages</a>';
    echo '<a class="btn btn-primary" href="register.php">Create Account</a>';
    echo '</div></div></header>';
}

function render_footer(): void {
    global $site;
    echo <<<HTML
<footer class="site-footer">
    <div class="container footer-cta">
        <p class="section-kicker">Warsaw courier equipment</p>
        <h2>Start delivery work with the right e-bike or scooter.</h2>
        <p>Built for couriers and local asset owners in Warsaw.</p>
        <a class="btn btn-light" href="packages.php">View Packages</a>
    </div>
    <div class="container footer-grid">
        <div>
            <div class="brand footer-brand">{$site['name']}</div>
            <p>{$site['tagline']}</p>
        </div>
        <div>
            <h4>Company</h4>
            <a href="about.php">About</a>
            <a href="services/index.php">Services</a>
            <a href="packages.php">Packages</a>
        </div>
        <div>
            <h4>Services</h4>
            <a href="gig-workers.php">For Couriers</a>
            <a href="register.php">Create Account</a>
        </div>
        <div>
            <h4>Contact</h4>
            <a href="tel:{$site['phone_link']}">{$site['phone_display']}</a>
            <a href="mailto:{$site['email']}">{$site['email']}</a>
            <span>{$site['city']}</span>
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© {$site['year']} {$site['name']}. All rights reserved.</span>
        <div class="footer-links">
            <a href="#">Terms</a>
            <a href="#">Privacy</a>
        </div>
    </div>
</footer>
<script src="assets/js/main.js"></script>
</body>
</html>
HTML;
}
