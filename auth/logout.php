<?php
require_once __DIR__ . '/../includes/auth.php';
logout_user();
redirect(base_url() . '/login.php');
