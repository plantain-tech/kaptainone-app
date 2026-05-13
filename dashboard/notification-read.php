<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/notifications.php';
require_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? '')) {
    set_flash('error', 'Security check failed. Please try again.');
    redirect(base_url() . '/dashboard/notifications.php');
}

$id = (int) ($_POST['notification_id'] ?? 0);
$target = $id > 0 ? notifications_mark_read($id, (int) $_SESSION['user_id']) : null;
redirect($target ?: base_url() . '/dashboard/notifications.php');
