<?php
$dashboardTitle = 'Support';
require_once __DIR__ . '/_layout.php';
$success = false;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) $errors[] = 'Security check failed.';
    $message = trim($_POST['message'] ?? '');
    if ($message === '') $errors[] = 'Please enter a message.';
    if (!$errors) {
        Database::insert('support_messages', [
            'user_id' => $user['id'],
            'subject' => trim($_POST['subject'] ?? 'Worker support'),
            'message' => $message
        ]);
        $success = true;
    }
}
?>
<section class="dash-panel">
    <?php if ($success): ?><div class="alert alert-success">Support message sent.</div><?php endif; ?>
    <?php if ($errors): ?><div class="alert alert-error"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
    <form method="post" class="app-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>Subject<input class="form-control" name="subject" value="Worker support"></label>
        <label>Message<textarea class="form-control" name="message" rows="6" required></textarea></label>
        <button class="btn btn-primary btn-large" type="submit">Send Message</button>
    </form>
</section>
<?php require_once __DIR__ . '/_footer.php'; ?>
