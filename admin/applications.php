<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/app_data.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!is_logged_in()) redirect('login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'submitted';
    Database::update('leasing_applications', [
        'status' => $status,
        'admin_notes' => trim($_POST['admin_notes'] ?? ''),
        'reviewed_at' => date('Y-m-d H:i:s')
    ], 'id = :id', ['id' => $id]);
}

$applications = Database::fetchAll(
    "SELECT a.*, p.title AS package_title, u.email, up.full_name
     FROM leasing_applications a
     JOIN equipment_packages p ON p.id = a.package_id
     JOIN users u ON u.id = a.user_id
     LEFT JOIN user_profiles up ON up.user_id = u.id
     ORDER BY a.created_at DESC"
);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Applications | Admin</title><link rel="stylesheet" href="../assets/css/main.css"><link rel="stylesheet" href="../assets/css/responsive.css"></head>
<body class="admin-page"><main class="admin-workspace">
<header class="admin-top"><h1>Leasing Applications</h1><a href="dashboard.php" class="btn btn-secondary">Dashboard</a></header>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Worker</th><th>Package</th><th>Status</th><th>Message</th><th>Update</th></tr></thead><tbody>
<?php foreach ($applications as $app): ?><tr><td><?= e($app['full_name'] ?: $app['email']) ?></td><td><?= e($app['package_title']) ?></td><td><span class="status-pill status-<?= e($app['status']) ?>"><?= e(application_status_label($app['status'])) ?></span></td><td><?= e(truncate($app['applicant_message'] ?: '-', 90)) ?></td><td><form method="post" class="inline-admin-form"><input type="hidden" name="id" value="<?= (int)$app['id'] ?>"><select class="form-control" name="status"><?php foreach(['submitted','under_review','approved','rejected','cancelled'] as $s): ?><option value="<?= $s ?>" <?= $app['status'] === $s ? 'selected' : '' ?>><?= e(application_status_label($s)) ?></option><?php endforeach; ?></select><input class="form-control" name="admin_notes" placeholder="Admin notes" value="<?= e($app['admin_notes'] ?? '') ?>"><button class="btn btn-primary btn-sm">Save</button></form></td></tr><?php endforeach; ?>
</tbody></table></div></main></body></html>
