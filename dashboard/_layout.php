<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/app_data.php';
require_user();

$user = current_user();
$dashboardTitle = $dashboardTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($dashboardTitle) ?> | Kaptain One</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/responsive.css">
</head>
<body class="dashboard-shell">
    <aside class="dash-sidebar">
        <a href="<?= base_url() ?>/index.php" class="dash-brand">Kaptain One</a>
        <nav class="dash-nav">
            <a href="<?= base_url() ?>/dashboard/index.php">Overview</a>
            <a href="<?= base_url() ?>/dashboard/profile.php">My Profile</a>
            <a href="<?= base_url() ?>/dashboard/packages.php">Packages</a>
            <a href="<?= base_url() ?>/dashboard/applications.php">Applications</a>
            <a href="<?= base_url() ?>/dashboard/equipment.php">My Equipment</a>
            <a href="<?= base_url() ?>/dashboard/support.php">Support</a>
            <a href="<?= base_url() ?>/auth/logout.php">Logout</a>
        </nav>
    </aside>
    <main class="dash-main">
        <header class="dash-header">
            <div>
                <span class="section-kicker">Gig Worker Portal</span>
                <h1><?= e($dashboardTitle) ?></h1>
            </div>
            <a href="<?= base_url() ?>/packages.php" class="btn btn-primary">Browse Packages</a>
        </header>
