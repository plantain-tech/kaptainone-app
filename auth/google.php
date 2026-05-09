<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/oauth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$params = [
    'client_id' => $OAUTH_CONFIG['google']['client_id'],
    'redirect_uri' => $OAUTH_CONFIG['google']['redirect_uri'],
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $state,
    'access_type' => 'offline',
    'prompt' => 'select_account'
];

redirect($OAUTH_CONFIG['google']['auth_url'] . '?' . http_build_query($params));
