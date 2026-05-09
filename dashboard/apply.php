<?php
$dashboardTitle = 'Apply for Leasing';
require_once __DIR__ . '/_layout.php';

$packages = get_active_packages();
$selectedPackageId = (int)($_GET['package'] ?? $_POST['package_id'] ?? 0);
$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security check failed. Please try again.';
    }

    $packageId = (int)($_POST['package_id'] ?? 0);
    $message = trim($_POST['applicant_message'] ?? '');

    if (!$packageId || !get_package_by_id($packageId)) $errors[] = 'Please select a package.';

    if (!$errors) {
        Database::insert('leasing_applications', [
            'user_id' => $user['id'],
            'package_id' => $packageId,
            'status' => 'submitted',
            'applicant_message' => $message,
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
        $success = true;
    }
}
?>

<section class="dash-panel">
    <?php if ($success): ?>
        <div class="alert alert-success">Application submitted. You can track it from your applications page.</div>
        <a href="<?= base_url() ?>/dashboard/applications.php" class="btn btn-primary">View Applications</a>
    <?php else: ?>
        <?php if ($errors): ?><div class="alert alert-error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
        <form method="post" class="app-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Package
                <select class="form-control" name="package_id" required>
                    <option value="">Select package</option>
                    <?php foreach ($packages as $package): ?>
                        <option value="<?= (int)$package['id'] ?>" <?= $selectedPackageId === (int)$package['id'] ? 'selected' : '' ?>><?= e($package['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Message to Kaptain One
                <textarea class="form-control" name="applicant_message" rows="5" placeholder="Tell us about your work plans, platform, start date, and equipment needs."></textarea>
            </label>
            <button class="btn btn-primary btn-large" type="submit">Submit Application</button>
        </form>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
