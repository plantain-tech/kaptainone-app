<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/app_data.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!is_logged_in()) redirect('login.php');

$id = (int)($_GET['id'] ?? 0);
$package = $id ? get_package_by_id($id) : null;
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => trim($_POST['slug'] ?? '') ?: slugify($_POST['title'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'short_description' => trim($_POST['short_description'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'included_items' => trim($_POST['included_items'] ?? ''),
        'weekly_price' => (float)($_POST['weekly_price'] ?? 0),
        'monthly_price' => (float)($_POST['monthly_price'] ?? 0),
        'deposit_amount' => (float)($_POST['deposit_amount'] ?? 0),
        'currency' => trim($_POST['currency'] ?? 'PLN'),
        'availability_status' => $_POST['availability_status'] ?? 'available',
        'requirements' => trim($_POST['requirements'] ?? ''),
        'terms' => trim($_POST['terms'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    if ($data['title'] === '') $errors[] = 'Title is required.';
    if (!$errors) {
        if ($id) {
            Database::update('equipment_packages', $data, 'id = :id', ['id' => $id]);
        } else {
            $id = Database::insert('equipment_packages', $data);
        }
        $package = get_package_by_id($id);
        $success = true;
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Package Editor | Admin</title><link rel="stylesheet" href="../assets/css/main.css"><link rel="stylesheet" href="../assets/css/responsive.css"></head>
<body class="admin-page"><main class="admin-workspace narrow">
<header class="admin-top"><h1><?= $id ? 'Edit Package' : 'New Package' ?></h1><a href="packages.php" class="btn btn-secondary">Back</a></header>
<?php if ($success): ?><div class="alert alert-success">Package saved.</div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
<form method="post" class="app-form two-col">
<?php foreach (['title','slug','category','currency'] as $field): ?><label><?= e(ucwords(str_replace('_',' ',$field))) ?><input class="form-control" name="<?= e($field) ?>" value="<?= e($package[$field] ?? '') ?>"></label><?php endforeach; ?>
<label>Weekly Price<input class="form-control" type="number" step="0.01" name="weekly_price" value="<?= e($package['weekly_price'] ?? '0') ?>"></label>
<label>Monthly Price<input class="form-control" type="number" step="0.01" name="monthly_price" value="<?= e($package['monthly_price'] ?? '0') ?>"></label>
<label>Deposit<input class="form-control" type="number" step="0.01" name="deposit_amount" value="<?= e($package['deposit_amount'] ?? '0') ?>"></label>
<label>Availability<select class="form-control" name="availability_status"><?php foreach(['available','limited','waitlist','unavailable'] as $s): ?><option value="<?= $s ?>" <?= ($package['availability_status'] ?? '') === $s ? 'selected' : '' ?>><?= e(package_status_label($s)) ?></option><?php endforeach; ?></select></label>
<?php foreach (['short_description','description','included_items','requirements','terms'] as $field): ?><label class="full-span"><?= e(ucwords(str_replace('_',' ',$field))) ?><textarea class="form-control" name="<?= e($field) ?>" rows="4"><?= e($package[$field] ?? '') ?></textarea></label><?php endforeach; ?>
<label class="full-span"><input type="checkbox" name="is_active" <?= !isset($package['is_active']) || $package['is_active'] ? 'checked' : '' ?>> Active package</label>
<button class="btn btn-primary btn-large">Save Package</button>
</form></main></body></html>
