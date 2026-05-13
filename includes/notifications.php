<?php
/**
 * In-app notification helpers.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function notify(int $userId, string $type, string $title, string $body, ?string $linkUrl = null, ?int $relatedBookingId = null, ?int $relatedListingId = null): int {
    return Database::insert('notifications', [
        'user_id' => $userId,
        'type' => $type,
        'related_booking_id' => $relatedBookingId,
        'related_listing_id' => $relatedListingId,
        'title' => substr($title, 0, 120),
        'body' => substr($body, 0, 400),
        'link_url' => $linkUrl,
    ]);
}

function notifications_unread_count(int $userId): int {
    return (int) (Database::fetch("SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND read_at IS NULL", [$userId])['count'] ?? 0);
}

function notifications_recent(int $userId, int $limit = 10): array {
    $limit = max(1, min(50, $limit));
    return Database::fetchAll(
        "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT {$limit}",
        [$userId]
    );
}

function notifications_mark_read(int $notificationId, int $userId): ?string {
    $notification = Database::fetch("SELECT * FROM notifications WHERE id = ? AND user_id = ?", [$notificationId, $userId]);
    if (!$notification) {
        return null;
    }
    if (empty($notification['read_at'])) {
        Database::update('notifications', ['read_at' => date('Y-m-d H:i:s')], 'id = :id AND user_id = :user_id', [
            'id' => $notificationId,
            'user_id' => $userId,
        ]);
    }
    return $notification['link_url'] ?: base_url() . '/dashboard/notifications.php';
}

function notifications_mark_all_read(int $userId): void {
    Database::query("UPDATE notifications SET read_at = NOW() WHERE user_id = ? AND read_at IS NULL", [$userId]);
}

function render_notification_bell(int $userId): void {
    $count = notifications_unread_count($userId);
    $recent = notifications_recent($userId, 10);
    ?>
    <div class="notification-menu" data-notification-menu>
        <button class="notification-bell" type="button" data-notification-toggle aria-label="Notifications">
            <span>Notifications</span>
            <?php if ($count > 0): ?><strong><?= $count ?></strong><?php endif; ?>
        </button>
        <div class="notification-dropdown" data-notification-dropdown hidden>
            <?php if (!$recent): ?>
                <p class="text-muted">No notifications yet.</p>
            <?php else: ?>
                <?php foreach ($recent as $item): ?>
                    <form method="post" action="<?= base_url() ?>/dashboard/notification-read.php" class="notification-read-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="notification_id" value="<?= (int) $item['id'] ?>">
                        <button class="notification-item <?= empty($item['read_at']) ? 'is-unread' : '' ?>" type="submit">
                        <span class="notification-dot"></span>
                        <span>
                            <strong><?= e($item['title']) ?></strong>
                            <small><?= e($item['body']) ?></small>
                            <em><?= e(function_exists('booking_relative_time') ? booking_relative_time($item['created_at']) : format_date($item['created_at'])) ?></em>
                        </span>
                        </button>
                    </form>
                <?php endforeach; ?>
            <?php endif; ?>
            <a class="notification-view-all" href="<?= base_url() ?>/dashboard/notifications.php">View all</a>
        </div>
    </div>
    <?php
}
