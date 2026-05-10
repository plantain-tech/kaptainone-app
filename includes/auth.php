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

function get_user_roles(int $userId): array {
    try {
        $roles = Database::fetchAll("SELECT role FROM user_roles WHERE user_id = ? ORDER BY role", [$userId]);
        if ($roles) {
            return array_values(array_unique(array_column($roles, 'role')));
        }
    } catch (Exception $e) {
        error_log('Load user roles error: ' . $e->getMessage());
    }

    $legacy = Database::fetch("SELECT role FROM users WHERE id = ?", [$userId]);
    return [$legacy['role'] ?? 'gig_worker'];
}

function refresh_user_roles(int $userId): array {
    $roles = get_user_roles($userId);
    $_SESSION['user_roles'] = $roles;
    return $roles;
}

function current_user_roles(): array {
    if (!user_logged_in()) {
        return [];
    }

    if (empty($_SESSION['user_roles']) || !is_array($_SESSION['user_roles'])) {
        return refresh_user_roles((int)$_SESSION['user_id']);
    }

    return $_SESSION['user_roles'];
}

function has_role(string $role): bool {
    return in_array($role, current_user_roles(), true);
}

function require_role(string $role): void {
    require_user();
    if (!has_role($role)) {
        redirect(base_url() . '/login.php');
    }
}

function dashboard_redirect_for_roles(array $roles): string {
    if (in_array('admin', $roles, true)) {
        return base_url() . '/admin/';
    }

    $isWorker = in_array('gig_worker', $roles, true);
    $isOwner = in_array('asset_owner', $roles, true);

    if ($isOwner && !$isWorker) {
        return base_url() . '/dashboard/owner/';
    }

    return base_url() . '/dashboard/';
}

function login_user(int $userId): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_login_time'] = time();
    refresh_user_roles($userId);
    Database::update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $userId]);
}

function logout_user(): void {
    unset($_SESSION['user_id'], $_SESSION['user_login_time'], $_SESSION['user_roles']);
}

function register_user(array $data): array {
    $name = trim($data['full_name'] ?? '');
    $email = strtolower(trim($data['email'] ?? ''));
    $password = $data['password'] ?? '';
    $confirm = $data['password_confirm'] ?? '';
    $roleSelection = $data['role_selection'] ?? 'gig_worker';
    $roleMap = [
        'gig_worker' => ['gig_worker'],
        'asset_owner' => ['asset_owner'],
        'both' => ['gig_worker', 'asset_owner'],
    ];

    $errors = [];
    if (strlen($name) < 2) $errors['full_name'] = 'Please enter your full name';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email';
    if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters';
    if ($password !== $confirm) $errors['password_confirm'] = 'Passwords do not match';
    if (!isset($roleMap[$roleSelection])) $errors['role_selection'] = 'Please choose how you want to use Kaptain One';
    if (!verify_csrf($data['csrf_token'] ?? '')) $errors['csrf'] = 'Security check failed. Please try again.';

    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    if (Database::fetch("SELECT id FROM users WHERE email = ?", [$email])) {
        return ['success' => false, 'errors' => ['email' => 'An account with this email already exists']];
    }

    try {
        $pdo = Database::connect();
        $pdo->beginTransaction();
        $selectedRoles = $roleMap[$roleSelection];
        $legacyRole = in_array('gig_worker', $selectedRoles, true) ? 'gig_worker' : 'gig_worker';

        $userId = Database::insert('users', [
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $legacyRole,
            'auth_provider' => 'email',
            'is_active' => 1
        ]);

        foreach ($selectedRoles as $role) {
            Database::query("INSERT IGNORE INTO user_roles (user_id, role) VALUES (?, ?)", [$userId, $role]);
        }

        Database::insert('user_profiles', [
            'user_id' => $userId,
            'full_name' => $name,
            'city' => 'Warsaw',
            'country' => 'Poland',
            'preferred_language' => 'English'
        ]);

        $pdo->commit();
        login_user($userId);
        return ['success' => true, 'user_id' => $userId, 'roles' => $selectedRoles, 'redirect' => dashboard_redirect_for_roles($selectedRoles)];
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
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
    return ['success' => true, 'roles' => current_user_roles(), 'redirect' => dashboard_redirect_for_roles(current_user_roles())];
}

function profile_completion(array $profile): int {
    $fields = ['full_name', 'phone', 'city', 'country', 'work_type', 'preferred_platforms', 'driver_license_status'];
    $complete = 0;
    foreach ($fields as $field) {
        if (!empty($profile[$field])) $complete++;
    }
    return (int) round(($complete / count($fields)) * 100);
}
