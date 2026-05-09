<?php
$dashboardTitle = 'Leasing Applications';
require_once __DIR__ . '/_layout.php';
$applications = Database::fetchAll(
    "SELECT a.*, p.title AS package_title
     FROM leasing_applications a
     JOIN equipment_packages p ON p.id = a.package_id
     WHERE a.user_id = ?
     ORDER BY a.created_at DESC",
    [$user['id']]
);
?>
<section class="dash-panel">
    <div class="status-list">
        <?php if (!$applications): ?><p class="text-muted">No applications yet.</p><?php endif; ?>
        <?php foreach ($applications as $app): ?>
            <div class="status-row stacked">
                <div>
                    <strong><?= e($app['package_title']) ?></strong>
                    <p><?= e($app['applicant_message'] ?: 'No message added.') ?></p>
                    <?php if ($app['admin_notes']): ?><p>Admin notes: <?= e($app['admin_notes']) ?></p><?php endif; ?>
                </div>
                <span class="status-pill status-<?= e($app['status']) ?>"><?= e(application_status_label($app['status'])) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php require_once __DIR__ . '/_footer.php'; ?>
