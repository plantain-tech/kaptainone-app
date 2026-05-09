<?php
/**
 * OAuth provider placeholders.
 *
 * Set real values on Hostinger or in a local-only config override.
 * Never expose provider secrets in frontend code.
 */

$OAUTH_CONFIG = [
    'google' => [
        'client_id' => getenv('GOOGLE_CLIENT_ID') ?: 'GOOGLE_CLIENT_ID',
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: 'GOOGLE_CLIENT_SECRET',
        'redirect_uri' => getenv('GOOGLE_REDIRECT_URI') ?: site('url') . '/auth/google-callback.php',
        'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_url' => 'https://oauth2.googleapis.com/token',
        'user_url' => 'https://www.googleapis.com/oauth2/v3/userinfo',
    ],
    'apple' => [
        'client_id' => getenv('APPLE_CLIENT_ID') ?: 'APPLE_CLIENT_ID',
        'team_id' => getenv('APPLE_TEAM_ID') ?: 'APPLE_TEAM_ID',
        'key_id' => getenv('APPLE_KEY_ID') ?: 'APPLE_KEY_ID',
        'private_key_path' => getenv('APPLE_PRIVATE_KEY_PATH') ?: 'APPLE_PRIVATE_KEY_PATH',
        'redirect_uri' => getenv('APPLE_REDIRECT_URI') ?: site('url') . '/auth/apple-callback.php',
        'auth_url' => 'https://appleid.apple.com/auth/authorize',
        'token_url' => 'https://appleid.apple.com/auth/token',
    ],
];
