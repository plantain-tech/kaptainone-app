<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/app_data.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!is_logged_in()) redirect('login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['lease_id'])) {
        Database::update('leased_equipment', ['status' => $_POST['status'] ?? 'active'], 'id = :id', ['id' => (int)$_POST['lease_id']]);
    } else {
        Database::insert('equipment_items', [
            'package_id' => (int)($_POST['package_id'] ?? 0) ?: null,
            'name' => trim($_POST['name'] ?? ''),
            'type' => trim($_POST['type'] ?? ''),
            'serial_number' => trim($_POST['serial_number'] ?? ''),
            'status' => 'available',
            'notes' => trim($_POST['notes'] ?? '')
        ]);
    }
}

$packages = Database::fetchAll("SELECT id, title FROM equipment_packages ORDER BY title");
$items = Database::fetchAll("SELECT i.*, p.title AS package_title FROM equipment_items i LEFT JOIN equipment_packages p ON p.id = i.package_id ORDER BY i.created_at DESC");
$leases = Database::fetchAll("SELECT l.*, u.email, up.full_name, p.title AS package_title, i.name AS item_name FROM leased_equipment l JOIN users u ON u.id = l.user_id LEFT JOIN user_profiles up ON up.user_id = u.id LEFT JOIN equipment_packages p ON p.id = l.package_id LEFT JOIN equipment_items i ON i.id = l.equipment_item_id ORDER BY l.created_at DESC");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Equipment | Admin</title><link rel="stylesheet" href="../assets/css/main.css"><link rel="stylesheet" href="../assets/css/responsive.css"></head>
<body class="admin-page"><main class="admin-workspace">
<header class="admin-top"><h1>Equipment</h1><a href="dashboard.php" class="btn btn-secondary">Dashboard</a></header>
<section class="dash-panel"><h2>Add Equipment Item</h2><form method="post" class="app-form two-col"><label>Name<input class="form-control" name="name" required></label><label>Type<input class="form-control" name="type"></label><label>Serial<input class="form-control" name="serial_number"></label><label>Package<select class="form-control" name="package_id"><option value="">None</option><?php foreach($packages as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e($p['title']) ?></option><?php endforeach; ?></select></label><label class="full-span">Notes<textarea class="form-control" name="notes"></textarea></label><button class="btn btn-primary">Add Item</button></form></section>
<section class="dash-panel"><h2>Inventory</h2><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Name</th><th>Package</th><th>Serial</th><th>Status</th></tr></thead><tbody><?php foreach($items as $item): ?><tr><td><?= e($item['name']) ?></td><td><?= e($item['package_title'] ?: '-') ?></td><td><?= e($item['serial_number'] ?: '-') ?></td><td><?= e($item['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></section>
<section class="dash-panel"><h2>Active Leases</h2><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Worker</th><th>Package</th><th>Item</th><th>Status</th><th>Update</th></tr></thead><tbody><?php foreach($leases as $lease): ?><tr><td><?= e($lease['full_name'] ?: $lease['email']) ?></td><td><?= e($lease['package_title'] ?: '-') ?></td><td><?= e($lease['item_name'] ?: '-') ?></td><td><?= e($lease['status']) ?></td><td><form method="post" class="inline-admin-form"><input type="hidden" name="lease_id" value="<?= (int)$lease['id'] ?>"><select class="form-control" name="status"><?php foreach(['active','pending_pickup','returned','overdue','maintenance'] as $s): ?><option value="<?= $s ?>" <?= $lease['status'] === $s ? 'selected' : '' ?>><?= e(package_status_label($s)) ?></option><?php endforeach; ?></select><button class="btn btn-primary btn-sm">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div></section>
</main></body></html>
