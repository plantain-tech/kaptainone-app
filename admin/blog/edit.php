<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    redirect('../login.php');
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    redirect('index.php');
}

$post = Database::fetch("SELECT * FROM blog_posts WHERE id = ?", [$id]);
if (!$post) {
    redirect('index.php');
}

$categories = Database::fetchAll("SELECT * FROM blog_categories ORDER BY name");

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $status = $_POST['status'] ?? 'draft';
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    
    if (!$title) $errors[] = 'Title is required';
    if (!$slug) $errors[] = 'Slug is required';
    if (!$content) $errors[] = 'Content is required';
    
    // Check slug uniqueness (excluding current post)
    $existing = Database::fetch("SELECT id FROM blog_posts WHERE slug = ? AND id != ?", [$slug, $id]);
    if ($existing) $errors[] = 'Slug already exists';
    
    if (empty($errors)) {
        try {
            Database::update('blog_posts', [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'category_id' => $category_id ?: null,
                'meta_title' => $meta_title ?: $title,
                'meta_description' => $meta_description,
                'status' => $status,
                'published_at' => $status === 'published' ? ($post['published_at'] ?: date('Y-m-d H:i:s')) : $post['published_at']
            ], 'id = :id', ['id' => $id]);
            $success = true;
            $post = Database::fetch("SELECT * FROM blog_posts WHERE id = ?", [$id]);
        } catch (Exception $e) {
            $errors[] = 'Error updating post: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Edit Post';
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
        .admin-sidebar .logo { font-size: 1.25rem; font-weight: 800; margin-bottom: 2rem; }
        .admin-nav { list-style: none; }
        .admin-nav li { margin-bottom: 0.5rem; }
        .admin-nav a { display: block; padding: 0.75rem 1rem; color: var(--text-secondary); border-radius: var(--radius-md); }
        .admin-nav a:hover, .admin-nav a.active { background: var(--bg-tertiary); color: var(--text-primary); }
        .admin-main { flex: 1; margin-left: 260px; padding: 2rem; max-width: 900px; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .form-section { background: var(--bg-card); border: 1px solid var(--line-subtle); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; }
        .form-section h2 { font-size: 1rem; margin-bottom: 1.25rem; color: var(--text-muted); text-transform: uppercase; }
        .error-list { background: rgba(231, 76, 60, 0.1); border: 1px solid rgba(231, 76, 60, 0.3); color: #e74c3c; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; }
        .success-msg { background: rgba(39, 174, 96, 0.1); border: 1px solid rgba(39, 174, 96, 0.3); color: #27ae60; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; }
        .view-link { color: var(--accent-gold); }
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
                <h1>Edit Post</h1>
                <a href="../../blog/post.php?slug=<?= e($post['slug']) ?>" target="_blank" class="view-link">View on Site →</a>
            </div>
            
            <?php if ($success): ?>
                
                <div class="success-msg">Post updated successfully.</div>
            <?php endif; ?>
            
            <?php if ($errors): ?>
                
                <div class="error-list">
                    <ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            
            
            <form method="post">
                
                <div class="form-section">
                    <h2>Post Content</h2>
                    
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" class="form-control" value="<?= e($post['title']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Slug (URL) *</label>
                        <input type="text" name="slug" class="form-control" value="<?= e($post['slug']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Content *</label>
                        <textarea name="content" class="form-control" rows="10" required><?= e($post['content']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Excerpt</label>
                        <textarea name="excerpt" class="form-control" rows="3"><?= e($post['excerpt']) ?></textarea>
                    </div>
                </div>
                
                
                <div class="form-section">
                    <h2>Publishing</h2>
                    
                    <div class="form-grid two">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control">
                                <option value="">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    
                                    <option value="<?= $cat['id'] ?>" <?= $post['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                
                <div class="form-section">
                    <h2>SEO</h2>
                    
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="<?= e($post['meta_title']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2"><?= e($post['meta_description']) ?></textarea>
                    </div>
                </div>
                
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary btn-large">Save Changes</button>
                    <a href="index.php" class="btn btn-secondary btn-large">Cancel</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
