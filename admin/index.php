<?php
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (is_logged_in()) {
    redirect(base_url() . '/admin/dashboard.php');
} else {
    redirect(base_url() . '/admin/login.php');
}
