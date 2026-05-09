<?php
$dashboardTitle = 'Equipment Packages';
require_once __DIR__ . '/_layout.php';
$packages = get_active_packages();
?>
<section class="package-grid">
    <?php foreach ($packages as $package): ?>
        <?php require __DIR__ . '/../includes/package-card.php'; ?>
    <?php endforeach; ?>
</section>
<?php require_once __DIR__ . '/_footer.php'; ?>
