<?php
/**
 * Kaptain One - Gig worker authentication helpers.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    return Database::fetch(
        "SELECT u.*, p.full_name, p.phone, p.city, p.country, p.preferred_language, p.work_type
         FROM users u
         LEFT JOIN user_profiles p ON p.user_id = u.id
         WHERE u.id = ? AND u.is_active = 1",
        [$_SESSION['user_id']]
    );
}

function user_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function require_user(): void {
    if (!user_logged_in()) {
        redirect(base_url() . '/login.php');
    }
}

function login_user(int $userId): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_login_time'] = time();
    Database::update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $userId]);
}

function logout_user(): void {
    unset($_SESSION['user_id'], $_SESSION['user_login_time']);
}

function register_user(array $data): array {
    $name = trim($data['full_name'] ?? '');
    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';
    $confirm = $data['password_confirm'] ?? '';

    $errors = [];
    if (strlen($name) < 2) $errors['full_name'] = 'Please enter your full name';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email';
    if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters';
    if ($password !== $confirm) $errors['password_confirm'] = 'Passwords do not match';
    if (!verify_csrf($data['csrf_token'] ?? '')) $errors['csrf'] = 'Security check failed. Please try again.';

    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    if (Database::fetch("SELECT id FROM users WHERE email = ?", [$email])) {
        return ['success' => false, 'errors' => ['email' => 'An account with this email already exists']];
    }

    try {
        $userId = Database::insert('users', [
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'gig_worker',
            'auth_provider' => 'email',
            'is_active' => 1
        ]);

        Database::insert('user_profiles', [
            'user_id' => $userId,
            'full_name' => $name,
            'city' => 'Warsaw',
            'country' => 'Poland',
            'preferred_language' => 'English'
        ]);

        login_user($userId);
        return ['success' => true, 'user_id' => $userId];
    } catch (Exception $e) {
        error_log('Register user error: ' . $e->getMessage());
        return ['success' => false, 'errors' => ['account' => 'Unable to create account right now']];
    }
}

function authenticate_user(array $data): array {
    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';

    $errors = [];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email';
    if ($password === '') $errors['password'] = 'Please enter your password';
    if (!verify_csrf($data['csrf_token'] ?? '')) $errors['csrf'] = 'Security check failed. Please try again.';

    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $user = Database::fetch("SELECT id, password_hash FROM users WHERE email = ? AND is_active = 1", [$email]);
    if (!$user || !$user['password_hash'] || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'errors' => ['login' => 'Invalid email or password']];
    }

    login_user((int)$user['id']);
    return ['success' => true];
}

function profile_completion(array $profile): int {
    $fields = ['full_name', 'phone', 'city', 'country', 'work_type', 'preferred_platforms', 'driver_license_status'];
    $complete = 0;
    foreach ($fields as $field) {
        if (!empty($profile[$field])) $complete++;
    }
    return (int) round(($complete / count($fields)) * 100);
}
