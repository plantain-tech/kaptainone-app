<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Fetch published posts
$posts = Database::fetchAll(
    "SELECT p.*, c.name as category_name, c.slug as category_slug 
     FROM blog_posts p 
     LEFT JOIN blog_categories c ON p.category_id = c.id 
     WHERE p.status = 'published' 
     ORDER BY p.published_at DESC 
     LIMIT {$per_page} OFFSET {$offset}"
);

// Get total count for pagination
$total_result = Database::fetch("SELECT COUNT(*) as total FROM blog_posts WHERE status = 'published'");
$total_posts = $total_result['total'] ?? 0;
$total_pages = ceil($total_posts / $per_page);

$pageTitle = 'Blog';
$pageDescription = 'Insights, updates, and travel tips from Kaptain One.';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Blog</span>
        <h1>Insights &amp; Updates</h1>
        <p style="max-width: 700px; margin: 0 auto; font-size: 1.125rem; color: var(--text-secondary);">
            Industry news, travel tips, and updates from the Kaptain One team.
        </p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($posts)): ?>
            
            <p style="text-align: center; color: var(--text-muted);">No posts yet. Check back soon.</p>
        <?php else: ?>
            
            <div class="blog-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
                <?php foreach ($posts as $post): ?>
                    
                    <article class="card blog-card">
                        <div class="blog-card-image">
                            <?php if ($post['featured_image']): ?>
                                <img src="<?= asset('images/uploads/' . e($post['featured_image'])) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; background: var(--bg-tertiary);"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="blog-card-content">
                            <div class="blog-card-meta">
                                <?php if ($post['category_name']): ?>
                                    <span style="color: var(--accent-gold);"><?= e($post['category_name']) ?></span>
                                <?php endif; ?>
                                <span><?= format_date($post['published_at']) ?></span>
                            </div>
                            
                            <h3 class="card-title" style="font-size: 1.25rem;">
                                <a href="post.php?slug=<?= e($post['slug']) ?>" style="color: inherit;"><?= e($post['title']) ?></a>
                            </h3>
                            
                            <p><?= e(truncate($post['excerpt'] ?? $post['content'], 120)) ?></p>
                            
                            <a href="post.php?slug=<?= e($post['slug']) ?>" style="color: var(--accent-gold); font-weight: 500; margin-top: auto;">Read More →</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">← Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>">Next →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
