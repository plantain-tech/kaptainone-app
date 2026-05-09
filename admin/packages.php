<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/app_data.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!is_logged_in()) redirect('login.php');

$packages = Database::fetchAll("SELECT * FROM equipment_packages ORDER BY sort_order, title");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Packages | Admin</title><link rel="stylesheet" href="../assets/css/main.css"><link rel="stylesheet" href="../assets/css/responsive.css"></head>
<body class="admin-page"><main class="admin-workspace">
<header class="admin-top"><h1>Equipment Packages</h1><div><a href="package-edit.php" class="btn btn-primary">New Package</a> <a href="dashboard.php" class="btn btn-secondary">Dashboard</a></div></header>
<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Title</th><th>Category</th><th>Weekly</th><th>Deposit</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($packages as $package): ?><tr><td><?= e($package['title']) ?></td><td><?= e($package['category']) ?></td><td><?= number_format((float)$package['weekly_price'], 0) ?> <?= e($package['currency']) ?></td><td><?= number_format((float)$package['deposit_amount'], 0) ?> <?= e($package['currency']) ?></td><td><span class="status-pill status-<?= e($package['availability_status']) ?>"><?= e(package_status_label($package['availability_status'])) ?></span></td><td><a class="btn btn-secondary btn-sm" href="package-edit.php?id=<?= (int)$package['id'] ?>">Edit</a></td></tr><?php endforeach; ?>
</tbody></table></div>
</main></body></html>
