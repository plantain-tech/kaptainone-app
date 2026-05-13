<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/notifications.php';
require_once __DIR__ . '/../includes/booking_state.php';
require_user();

$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security check failed. Please try again.');
    } else {
        notifications_mark_all_read((int) $user['id']);
        set_flash('success', 'All notifications marked as read.');
    }
    redirect(base_url() . '/dashboard/notifications.php');
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * 50;
$notifications = Database::fetchAll(
    "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50 OFFSET {$offset}",
    [$user['id']]
);

$dashboardTitle = 'Notifications';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-panel">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Updates</span>
            <h2>Your notifications</h2>
        </div>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <button class="btn btn-secondary" type="submit">Mark all as read</button>
        </form>
    </div>
    <?php if (!$notifications): ?>
        <p class="text-muted">No notifications yet. Booking updates will appear here when couriers and owners take action.</p>
    <?php else: ?>
        <div class="booking-section">
            <?php foreach ($notifications as $item): ?>
                <form method="post" action="<?= base_url() ?>/dashboard/notification-read.php" class="notification-read-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="notification_id" value="<?= (int) $item['id'] ?>">
                    <button class="notification-item <?= empty($item['read_at']) ? 'is-unread' : '' ?>" type="submit">
                        <span class="notification-dot"></span>
                        <span>
                            <strong><?= e($item['title']) ?></strong>
                            <small><?= e($item['body']) ?></small>
                            <em title="<?= e(format_date($item['created_at'], 'M j, Y H:i')) ?>"><?= e(booking_relative_time($item['created_at'])) ?></em>
                        </span>
                    </button>
                </form>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
