<?php
$dashboardTitle = 'Owner Profile';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-panel">
    <h2>Owner profile uses your main Kaptain One profile for now.</h2>
    <p>Day 4 will add listing-specific owner details. You can update your contact details from the profile page.</p>
    <div class="app-actions">
        <a href="<?= base_url() ?>/dashboard/profile.php" class="btn btn-primary">Open Profile</a>
    </div>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
