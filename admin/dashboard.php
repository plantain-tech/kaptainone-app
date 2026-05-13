<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check auth
if (!is_logged_in()) {
    redirect('login.php');
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}

// Get stats
$posts_count = Database::fetch("SELECT COUNT(*) as total FROM blog_posts")['total'] ?? 0;
$partners_count = Database::fetch("SELECT COUNT(*) as total FROM partner_inquiries")['total'] ?? 0;
$demos_count = Database::fetch("SELECT COUNT(*) as total FROM demo_requests")['total'] ?? 0;
$new_partners = Database::fetch("SELECT COUNT(*) as total FROM partner_inquiries WHERE status = 'new'")['total'] ?? 0;
$new_demos = Database::fetch("SELECT COUNT(*) as total FROM demo_requests WHERE status = 'new'")['total'] ?? 0;

$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        .admin-container { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: var(--bg-secondary); border-right: 1px solid var(--line-subtle); padding: 2rem; }
        .admin-sidebar .logo { font-size: 1.25rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 2rem; }
        .admin-nav { list-style: none; }
        .admin-nav li { margin-bottom: 0.5rem; }
        .admin-nav a { display: block; padding: 0.75rem 1rem; color: var(--text-secondary); border-radius: var(--radius-md); transition: var(--transition-fast); }
        .admin-nav a:hover, .admin-nav a.active { background: var(--bg-tertiary); color: var(--text-primary); }
        .admin-main { flex: 1; padding: 2rem; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .admin-header h1 { font-size: 1.75rem; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--line-subtle); border-radius: var(--radius-md); padding: 1.5rem; }
        .stat-card h3 { font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-card p { color: var(--text-muted); font-size: 0.9375rem; }
        .stat-card .badge { display: inline-block; background: var(--accent-gold); color: var(--bg-primary); padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 600; margin-left: 0.5rem; }
        .admin-section { background: var(--bg-card); border: 1px solid var(--line-subtle); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; }
        .admin-section h2 { font-size: 1.125rem; margin-bottom: 1rem; }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th, .admin-table td { text-align: left; padding: 1rem; border-bottom: 1px solid var(--line-subtle); }
        .admin-table th { color: var(--text-muted); font-weight: 500; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .admin-table tr:last-child td { border-bottom: none; }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.8125rem; }
        .status-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 600; }
        .status-new { background: var(--accent-gold); color: var(--bg-primary); }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="logo">Kaptain One</div>
            <nav>
                <ul class="admin-nav">
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="users.php">Gig Workers</a></li>
                    <li><a href="listings.php">Marketplace Listings</a></li>
                    <li><a href="bookings.php">Marketplace Bookings</a></li>
                    <li><a href="packages.php">Packages</a></li>
                    <li><a href="applications.php">Leasing Applications</a></li>
                    <li><a href="equipment.php">Equipment</a></li>
                    <li><a href="blog/">Blog Posts</a></li>
                    <li><a href="submissions/partners.php">Partner Inquiries<?= $new_partners > 0 ? " <span class='badge'>{$new_partners}</span>" : '' ?></a></li>
                    <li><a href="submissions/demos.php">Demo Requests<?= $new_demos > 0 ? " <span class='badge'>{$new_demos}</span>" : '' ?></a></li>
                    <li><a href="dashboard.php?logout=1">Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Dashboard</h1>
            </div>
            
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?= number_format($posts_count) ?></h3>
                    <p>Blog Posts</p>
                </div>
                
                <div class="stat-card">
                    <h3><?= number_format($partners_count) ?></h3>
                    <p>Partner Inquiries<?= $new_partners > 0 ? " <span class='badge'>{$new_partners} new</span>" : '' ?></p>
                </div>
                
                <div class="stat-card">
                    <h3><?= number_format($demos_count) ?></h3>
                    <p>Demo Requests<?= $new_demos > 0 ? " <span class='badge'>{$new_demos} new</span>" : '' ?></p>
                </div>
            </div>
            
            
            <div class="admin-section">
                <h2>Quick Links</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="blog/create.php" class="btn btn-primary">+ New Blog Post</a>
                    <a href="listings.php" class="btn btn-secondary">Moderate Listings</a>
                    <a href="bookings.php" class="btn btn-secondary">Review Bookings</a>
                    <a href="../" target="_blank" class="btn btn-secondary">View Site →</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
