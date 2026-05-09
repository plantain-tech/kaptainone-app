<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/oauth.php';

if (user_logged_in()) redirect(base_url() . '/dashboard/index.php');

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = authenticate_user($_POST);
    if ($result['success']) redirect(base_url() . '/dashboard/index.php');
}

$pageTitle = 'Sign In';
require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
    <div class="auth-card">
        <span class="section-kicker">Welcome Back</span>
        <h1>Sign In</h1>
        <p>Access your Kaptain One worker dashboard.</p>

        <?php if ($result && !$result['success']): ?>
            <div class="alert alert-error">
                <?php foreach ($result['errors'] as $error): ?><div><?= e($error) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="oauth-stack">
            <a class="btn btn-secondary" href="<?= base_url() ?>/auth/google.php">Continue with Google</a>
            <a class="btn btn-secondary" href="<?= base_url() ?>/auth/apple.php">Continue with Apple</a>
        </div>

        <form method="post" class="app-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Email<input class="form-control" type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
            <label>Password<input class="form-control" type="password" name="password" required></label>
            <button class="btn btn-primary btn-large" type="submit">Sign In</button>
        </form>
        <p class="auth-switch">New to Kaptain One? <a href="<?= base_url() ?>/register.php">Create account</a></p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
