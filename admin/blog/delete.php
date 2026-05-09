<?php
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    redirect('../login.php');
}

$id = intval($_GET['id'] ?? 0);
if ($id) {
    try {
        Database::query("DELETE FROM blog_posts WHERE id = ?", [$id]);
    } catch (Exception $e) {
        error_log('Delete post error: ' . $e->getMessage());
    }
}

redirect('index.php');
