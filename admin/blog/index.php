<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    redirect('../login.php');
}

// Get all posts
$posts = Database::fetchAll(
    "SELECT p.*, c.name as category_name 
     FROM blog_posts p 
     LEFT JOIN blog_categories c ON p.category_id = c.id 
     ORDER BY p.created_at DESC"
);

$pageTitle = 'Blog Posts';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <style>
        .admin-container { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 260px; background: var(--bg-secondary); border-right: 1px solid var(--line-subtle); padding: 2rem; position: fixed; height: 100vh; }
        .admin-sidebar .logo { font-size: 1.25rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 2rem; }
        .admin-nav { list-style: none; }
        .admin-nav li { margin-bottom: 0.5rem; }
        .admin-nav a { display: block; padding: 0.75rem 1rem; color: var(--text-secondary); border-radius: var(--radius-md); transition: var(--transition-fast); }
        .admin-nav a:hover, .admin-nav a.active { background: var(--bg-tertiary); color: var(--text-primary); }
        .admin-main { flex: 1; margin-left: 260px; padding: 2rem; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .admin-table { width: 100%; border-collapse: collapse; background: var(--bg-card); border: 1px solid var(--line-subtle); border-radius: var(--radius-md); }
        .admin-table th, .admin-table td { text-align: left; padding: 1rem; border-bottom: 1px solid var(--line-subtle); }
        .admin-table th { color: var(--text-muted); font-weight: 500; font-size: 0.875rem; text-transform: uppercase; }
        .admin-table tr:last-child td { border-bottom: none; }
        .status-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.75rem; font-weight: 600; }
        .status-published { background: #27ae60; color: #fff; }
        .status-draft { background: var(--text-muted); color: #fff; }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.8125rem; }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="logo">Kaptain One</div>
            <nav>
                <ul class="admin-nav">
                    <li><a href="../dashboard.php">Dashboard</a></li>
                    <li><a href="index.php" class="active">Blog Posts</a></li>
                    <li><a href="../submissions/partners.php">Partner Inquiries</a></li>
                    <li><a href="../submissions/demos.php">Demo Requests</a></li>
                    <li><a href="../dashboard.php?logout=1">Logout</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>Blog Posts</h1>
                <a href="create.php" class="btn btn-primary">+ New Post</a>
            </div>
            
            <?php if (empty($posts)): ?>
                
                <p style="color: var(--text-muted);">No posts yet. <a href="create.php">Create your first post</a>.</p>
            <?php else: ?>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            
                            <tr>
                                <td><?= e(truncate($post['title'], 50)) ?></td>
                                <td><?= e($post['category_name'] ?? 'Uncategorized') ?></td>
                                <td>
                                    
                                    <span class="status-badge status-<?= $post['status'] ?>">
                                        <?= ucfirst($post['status']) ?>
                                    </span>
                                </td>
                                <td><?= format_date($post['created_at']) ?></td>
                                <td style="text-align: right;">
                                    <a href="edit.php?id=<?= $post['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-secondary btn-sm" style="margin-left: 0.5rem;" onclick="return confirm('Delete this post?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
