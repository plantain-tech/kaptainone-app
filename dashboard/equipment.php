<?php
$dashboardTitle = 'My Leased Equipment';
require_once __DIR__ . '/_layout.php';
$items = Database::fetchAll(
    "SELECT l.*, p.title AS package_title, i.name AS item_name, i.serial_number
     FROM leased_equipment l
     LEFT JOIN equipment_packages p ON p.id = l.package_id
     LEFT JOIN equipment_items i ON i.id = l.equipment_item_id
     WHERE l.user_id = ?
     ORDER BY l.created_at DESC",
    [$user['id']]
);
?>
<section class="dash-panel">
    <?php if (!$items): ?>
        <p class="text-muted">No leased equipment yet. Approved applications will appear here after assignment.</p>
    <?php endif; ?>
    <div class="app-grid">
        <?php foreach ($items as $item): ?>
            <article class="app-card">
                <span class="status-pill status-<?= e($item['status']) ?>"><?= e(package_status_label($item['status'])) ?></span>
                <h3><?= e($item['item_name'] ?: $item['package_title']) ?></h3>
                <p>Package: <?= e($item['package_title'] ?: '-') ?></p>
                <p>Start: <?= e($item['start_date'] ?: '-') ?> | Return: <?= e($item['expected_return_date'] ?: '-') ?></p>
                <a href="<?= base_url() ?>/dashboard/support.php" class="btn btn-secondary">Contact Support</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/_footer.php'; ?>
