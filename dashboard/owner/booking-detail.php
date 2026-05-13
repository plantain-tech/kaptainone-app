<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/booking_state.php';
require_role('asset_owner');

$user = current_user();
$bookingId = (int) ($_GET['id'] ?? 0);
$booking = $bookingId > 0 ? Database::fetch(
    "SELECT b.*, l.title AS listing_title, l.asset_type, l.location_district,
            p.full_name AS courier_name,
            (SELECT file_path FROM listing_photos lp WHERE lp.listing_id = l.id ORDER BY lp.sort_order, lp.id LIMIT 1) AS photo_path
     FROM bookings b
     JOIN listings l ON l.id = b.listing_id
     LEFT JOIN user_profiles p ON p.user_id = b.courier_user_id
     WHERE b.id = ?",
    [$bookingId]
) : null;

if (!$booking || (int) $booking['owner_user_id'] !== (int) $user['id']) {
    http_response_code(403);
    $dashboardTitle = 'Booking unavailable';
    require_once __DIR__ . '/_layout.php';
    echo '<section class="dash-panel"><h2>Booking unavailable</h2><p>You do not have permission to view this booking.</p></section>';
    require_once __DIR__ . '/_footer.php';
    exit;
}

$events = Database::fetchAll("SELECT * FROM booking_events WHERE booking_id = ? ORDER BY created_at ASC", [$bookingId]);
$courierName = trim((string) ($booking['courier_name'] ?? ''));
$courierFirst = $courierName !== '' ? strtok($courierName, ' ') : 'New courier';

$dashboardTitle = 'Booking Detail';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-panel">
    <div class="booking-card">
        <?php if ($booking['photo_path']): ?><img class="booking-card__thumb" src="<?= e(base_url() . $booking['photo_path']) ?>" alt="<?= e($booking['listing_title']) ?>"><?php endif; ?>
        <div>
            <span class="status-pill status-<?= e($booking['status']) ?>"><?= e(booking_status_label($booking['status'])) ?></span>
            <h2><?= e($booking['listing_title']) ?></h2>
            <div class="booking-breakdown">
                <span>Courier: <?= e($courierFirst) ?></span>
                <span><?= e(format_date($booking['start_date'])) ?> - <?= e(format_date($booking['end_date'])) ?> · <?= (int) $booking['rental_days'] ?> days</span>
                <span>Total: <?= e(number_format((float) $booking['total_amount_pln'], 0)) ?> PLN · Deposit: <?= e(number_format((float) $booking['deposit_snapshot_pln'], 0)) ?> PLN</span>
            </div>
            <?php if ($booking['courier_message']): ?><div class="booking-card__message"><strong>Courier message:</strong><br><?= nl2br(e($booking['courier_message'])) ?></div><?php endif; ?>
            <?php if ($booking['owner_response_message']): ?><div class="booking-card__message"><strong>Your response:</strong><br><?= nl2br(e($booking['owner_response_message'])) ?></div><?php endif; ?>
        </div>
    </div>
</section>

<section class="dash-panel">
    <h2>Audit timeline</h2>
    <div class="booking-timeline">
        <?php foreach ($events as $event): ?>
            <div class="booking-timeline__item <?= $event['new_status'] === $booking['status'] ? 'is-current' : 'is-complete' ?>">
                <strong><?= e(booking_status_label($event['new_status'] ?: $event['event_type'])) ?></strong>
                <span title="<?= e(format_date($event['created_at'], 'M j, Y H:i')) ?>"><?= e(booking_relative_time($event['created_at'])) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
