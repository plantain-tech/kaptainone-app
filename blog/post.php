<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

$slug = $_GET['slug'] ?? '';

if (!$slug) {
    redirect('index.php');
}

$post = Database::fetch(
    "SELECT p.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name 
     FROM blog_posts p 
     LEFT JOIN blog_categories c ON p.category_id = c.id 
     LEFT JOIN admin_users u ON p.author_id = u.id 
     WHERE p.slug = ? AND p.status = 'published'",
    [$slug]
);

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Not Found';
    require_once __DIR__ . '/../includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>Post Not Found</h1><p><a href="index.php">← Back to Blog</a></p></div></section>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Related posts
$related = Database::fetchAll(
    "SELECT title, slug, featured_image, excerpt 
     FROM blog_posts 
     WHERE status = 'published' AND id != ? 
     ORDER BY published_at DESC 
     LIMIT 3",
    [$post['id']]
);

$pageTitle = $post['title'];
$pageDescription = $post['meta_description'] ?? truncate($post['excerpt'] ?? $post['content'], 160);
require_once __DIR__ . '/../includes/header.php';
?>

<article>
    <section class="page-hero" style="padding-bottom: 4rem;">
        <div class="container">
            <?php if ($post['category_name']): ?>
                
                <a href="index.php?category=<?= e($post['category_slug']) ?>" class="section-kicker"><?= e($post['category_name']) ?></a>
            <?php endif; ?>
            
            <h1 style="max-width: 900px; margin: 1rem auto 1.5rem; text-align: center;"><?= e($post['title']) ?></h1>
            
            
            <div style="display: flex; justify-content: center; gap: 2rem; color: var(--text-muted); font-size: 0.875rem;">
                <?php if ($post['author_name']): ?>
                    <span>By <?= e($post['author_name']) ?></span>
                <?php endif; ?>
                <span><?= format_date($post['published_at'], 'F j, Y') ?></span>
            </div>
        </div>
    </section>

    <?php if ($post['featured_image']): ?>
        <div class="container" style="margin-bottom: 3rem;">
            <img src="<?= asset('images/uploads/' . e($post['featured_image'])) ?>" 
                 alt="<?= e($post['title']) ?>" 
                 style="width: 100%; height: 500px; object-fit: cover; border-radius: var(--radius-lg);">
        </div>
    <?php endif; ?>

    <section style="padding-bottom: 4rem;">
        <div class="container" style="max-width: 800px;">
            
            <div style="font-size: 1.125rem; line-height: 1.8; color: var(--text-secondary);">
                <?= nl2br(e($post['content'])) ?>
            </div>
        </div>
    </section>

    <?php if (!empty($related)): ?>
        <section class="section" style="background: var(--bg-secondary);">
            <div class="container">
                <h2 style="margin-bottom: 2rem; text-align: center;">Related Posts</h2>

                <div class="blog-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem;">
                    <?php foreach ($related as $rpost): ?>
                        <article class="card blog-card">
                            <div class="blog-card-image" style="aspect-ratio: 16/10;">
                                <?php if ($rpost['featured_image']): ?>
                                    <img src="<?= asset('images/uploads/' . e($rpost['featured_image'])) ?>" alt="<?= e($rpost['title']) ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: var(--bg-tertiary);"></div>
                                <?php endif; ?>
                            </div>

                            <div class="blog-card-content" style="padding: 1.25rem;">
                                <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem;">
                                    <a href="post.php?slug=<?= e($rpost['slug']) ?>" style="color: inherit;"><?= e($rpost['title']) ?></a>
                                </h3>
                                <p style="font-size: 0.9375rem;"><?= e(truncate($rpost['excerpt'], 100)) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</article>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
