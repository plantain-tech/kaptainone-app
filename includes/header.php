<?php
/**
 * Kaptain One - Header Component
 */
if (!isset($pageTitle)) $pageTitle = '';
if (!isset($pageDescription)) $pageDescription = site('tagline');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= page_title($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="theme-color" content="#0a0a0b">

    <link rel="icon" type="image/svg+xml" href="<?= asset('favicon/favicon.svg') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('favicon/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= asset('favicon/favicon-16x16.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('favicon/apple-touch-icon.png') ?>">
    <link rel="shortcut icon" href="<?= asset('favicon/favicon.ico') ?>">
    <link rel="manifest" href="<?= asset('favicon/site.webmanifest') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/services-section.css') ?>">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="<?= base_url() ?>/index.php" class="nav-logo" aria-label="Kaptain One home">
                <img src="<?= asset('images/branding/kaptain-one-logo.png') ?>" alt="Kaptain One" class="nav-logo-image">
            </a>

            <div class="nav-links">
                <a href="<?= base_url() ?>/solutions.php" class="nav-link <?= nav_active('solutions') ?>">Solutions</a>
                <a href="<?= base_url() ?>/services/index.php" class="nav-link <?= nav_active('services') ?>">Services</a>
                <a href="<?= base_url() ?>/gig-workers.php" class="nav-link <?= nav_active('gig-workers') ?>">Gig Workers</a>
                <a href="<?= base_url() ?>/packages.php" class="nav-link <?= nav_active('packages') ?>">Packages</a>
                <a href="<?= base_url() ?>/about.php" class="nav-link <?= nav_active('about') ?>">About</a>
                <a href="<?= base_url() ?>/blog/index.php" class="nav-link <?= nav_active('blog') ?>">Blog</a>
                <a href="<?= base_url() ?>/contact.php" class="nav-link <?= nav_active('contact') ?>">Contact</a>
            </div>

            <div class="nav-cta">
                <a href="<?= base_url() ?>/login.php" class="btn btn-secondary">Sign In</a>
                <a href="<?= base_url() ?>/register.php" class="btn btn-primary">Create Account</a>
            </div>

            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobileMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <div class="mobile-menu" id="mobileMenu">
            <a href="<?= base_url() ?>/solutions.php" class="<?= nav_active('solutions') ?>">Solutions</a>
            <a href="<?= base_url() ?>/services/index.php" class="<?= nav_active('services') ?>">Services</a>
            <a href="<?= base_url() ?>/gig-workers.php" class="<?= nav_active('gig-workers') ?>">Gig Workers</a>
            <a href="<?= base_url() ?>/packages.php" class="<?= nav_active('packages') ?>">Packages</a>
            <a href="<?= base_url() ?>/about.php" class="<?= nav_active('about') ?>">About</a>
            <a href="<?= base_url() ?>/blog/index.php" class="<?= nav_active('blog') ?>">Blog</a>
            <a href="<?= base_url() ?>/contact.php" class="<?= nav_active('contact') ?>">Contact</a>
            <a href="<?= base_url() ?>/login.php" class="btn btn-secondary">Sign In</a>
            <a href="<?= base_url() ?>/register.php" class="btn btn-primary">Create Account</a>
        </div>
    </nav>
