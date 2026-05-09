<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Start session for admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if already logged in
if (is_logged_in()) {
    redirect(base_url() . '/admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $user = Database::fetch(
        "SELECT id, password_hash FROM admin_users WHERE username = ? AND is_active = 1",
        [$username]
    );
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_login_time'] = time();
        
        // Update last login
        Database::update('admin_users', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);
        
        redirect(base_url() . '/admin/dashboard.php');
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Kaptain One</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at 50% 0%, rgba(201, 162, 39, 0.08), transparent 30%),
                linear-gradient(145deg, var(--bg-secondary), var(--bg-primary));
            padding: 2rem 1rem;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--line-subtle);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            box-shadow: var(--shadow-card);
        }
        .login-logo {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 0.9375rem;
        }
        .login-form .form-group {
            margin-bottom: 1.25rem;
        }
        .login-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
        }
        .login-error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            padding: 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.25rem;
            font-size: 0.9375rem;
        }
        .login-return {
            display: inline-flex;
            width: 100%;
            margin-top: 1rem;
            border-color: rgba(201, 162, 39, 0.18);
            color: var(--text-secondary);
        }
        .login-return:hover,
        .login-return:focus-visible {
            color: var(--text-primary);
            border-color: rgba(201, 162, 39, 0.34);
            background: rgba(201, 162, 39, 0.08);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">Kaptain One</div>
            <p class="login-subtitle">Admin Panel</p>
            
            <?php if ($error): ?>
                
                <div class="login-error"><?= e($error) ?></div>
            <?php endif; ?>
            
            <form method="post" class="login-form">
                
                <div class="form-group">
                    
                    <label>Username</label>
                    
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                
                
                <div class="form-group">
                    
                    <label>Password</label>
                    
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                
                <button type="submit" class="btn btn-primary btn-large" style="width: 100%; margin-top: 0.5rem;">Sign In</button>
            </form>

            <a href="<?= base_url() ?>/index.php" class="btn btn-secondary btn-large login-return">Back to Main Site</a>
        </div>
    </div>
</body>
</html>
