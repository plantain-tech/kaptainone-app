<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/oauth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$params = [
    'client_id' => $OAUTH_CONFIG['apple']['client_id'],
    'redirect_uri' => $OAUTH_CONFIG['apple']['redirect_uri'],
    'response_type' => 'code id_token',
    'response_mode' => 'form_post',
    'scope' => 'name email',
    'state' => $state
];

redirect($OAUTH_CONFIG['apple']['auth_url'] . '?' . http_build_query($params));
