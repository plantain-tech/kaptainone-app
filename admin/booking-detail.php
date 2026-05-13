<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/booking_state.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!is_logged_in() && !has_role('admin')) {
    redirect('login.php');
}

$bookingId = (int) ($_GET['id'] ?? 0);
$booking = $bookingId > 0 ? Database::fetch(
    "SELECT b.*, l.title AS listing_title, cu.email AS courier_email, ou.email AS owner_email
     FROM bookings b
     JOIN listings l ON l.id = b.listing_id
     JOIN users cu ON cu.id = b.courier_user_id
     JOIN users ou ON ou.id = b.owner_user_id
     WHERE b.id = ?",
    [$bookingId]
) : null;

if (!$booking) {
    http_response_code(404);
    echo 'Booking not found';
    exit;
}
$events = Database::fetchAll("SELECT * FROM booking_events WHERE booking_id = ? ORDER BY created_at ASC", [$bookingId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking #<?= (int) $booking['id'] ?> | Admin</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body class="admin-page">
    <main class="admin-workspace">
        <header class="admin-header">
            <div><a href="bookings.php" class="section-kicker">Back to bookings</a><h1>Booking #<?= (int) $booking['id'] ?></h1></div>
        </header>
        <section class="admin-panel">
            <h2><?= e($booking['listing_title']) ?></h2>
            <div class="booking-breakdown">
                <span>Status: <?= e(booking_status_label($booking['status'])) ?></span>
                <span>Courier: <?= e($booking['courier_email']) ?></span>
                <span>Owner: <?= e($booking['owner_email']) ?></span>
                <span><?= e(format_date($booking['start_date'])) ?> - <?= e(format_date($booking['end_date'])) ?> · <?= e(number_format((float) $booking['total_amount_pln'], 0)) ?> PLN</span>
            </div>
            <?php if ($booking['courier_message']): ?><div class="booking-card__message"><strong>Courier:</strong><br><?= nl2br(e($booking['courier_message'])) ?></div><?php endif; ?>
            <?php if ($booking['owner_response_message']): ?><div class="booking-card__message"><strong>Owner:</strong><br><?= nl2br(e($booking['owner_response_message'])) ?></div><?php endif; ?>
        </section>
        <section class="admin-panel">
            <h2>Audit events</h2>
            <table class="admin-table">
                <thead><tr><th>Event</th><th>Previous</th><th>New</th><th>Actor</th><th>IP</th><th>User agent</th><th>When</th></tr></thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= e($event['event_type']) ?></td>
                            <td><?= e($event['previous_status'] ?? '') ?></td>
                            <td><?= e($event['new_status'] ?? '') ?></td>
                            <td><?= e((string) ($event['actor_user_id'] ?? 'system')) ?></td>
                            <td><?= e($event['ip_address'] ?? '') ?></td>
                            <td><?= e($event['user_agent'] ?? '') ?></td>
                            <td><?= e(format_date($event['created_at'], 'M j, Y H:i')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
