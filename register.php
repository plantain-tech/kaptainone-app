<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/oauth.php';

if (user_logged_in()) redirect(base_url() . '/dashboard/index.php');

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = register_user($_POST);
    if ($result['success']) redirect(base_url() . '/dashboard/index.php');
}

$pageTitle = 'Create Account';
require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
    <div class="auth-card">
        <span class="section-kicker">Gig Worker Account</span>
        <h1>Create Your Account</h1>
        <p>Start your profile and apply for leasing packages in Warsaw.</p>

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
            <label>Full Name<input class="form-control" name="full_name" required value="<?= e($_POST['full_name'] ?? '') ?>"></label>
            <label>Email<input class="form-control" type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
            <label>Password<input class="form-control" type="password" name="password" required minlength="8"></label>
            <label>Confirm Password<input class="form-control" type="password" name="password_confirm" required minlength="8"></label>
            <button class="btn btn-primary btn-large" type="submit">Create Account</button>
        </form>
        <p class="auth-switch">Already have an account? <a href="<?= base_url() ?>/login.php">Sign in</a></p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
