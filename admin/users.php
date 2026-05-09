<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!is_logged_in()) redirect('login.php');

$q = trim($_GET['q'] ?? '');
$params = [];
$where = '';
if ($q !== '') {
    $where = "WHERE u.email LIKE ? OR p.full_name LIKE ? OR p.city LIKE ?";
    $params = ["%$q%", "%$q%", "%$q%"];
}
$users = Database::fetchAll(
    "SELECT u.*, p.full_name, p.phone, p.city, p.work_type, p.preferred_platforms
     FROM users u
     LEFT JOIN user_profiles p ON p.user_id = u.id
     $where
     ORDER BY u.created_at DESC",
    $params
);
$pageTitle = 'Gig Workers';
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= e($pageTitle) ?> | Admin</title><link rel="stylesheet" href="../assets/css/main.css"><link rel="stylesheet" href="../assets/css/responsive.css"></head>
<body class="admin-page"><main class="admin-workspace">
<header class="admin-top"><h1>Gig Workers</h1><a href="dashboard.php" class="btn btn-secondary">Dashboard</a></header>
<form method="get" class="admin-filter"><input class="form-control" name="q" placeholder="Search workers..." value="<?= e($q) ?>"><button class="btn btn-primary">Search</button></form>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Name</th><th>Email</th><th>City</th><th>Work Type</th><th>Platforms</th><th>Joined</th></tr></thead><tbody>
<?php foreach ($users as $worker): ?><tr><td><?= e($worker['full_name'] ?: '-') ?></td><td><?= e($worker['email']) ?></td><td><?= e($worker['city'] ?: '-') ?></td><td><?= e($worker['work_type'] ?: '-') ?></td><td><?= e($worker['preferred_platforms'] ?: '-') ?></td><td><?= format_date($worker['created_at']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
</main></body></html>
