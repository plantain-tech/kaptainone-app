<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/booking_state.php';
require_once __DIR__ . '/../../includes/notifications.php';
require_user();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? '')) {
    set_flash('error', 'Security check failed. Please try again.');
    redirect(base_url() . '/dashboard/courier/bookings.php');
}

$user = current_user();
$bookingId = (int) ($_POST['booking_id'] ?? 0);
$expectedStatus = trim((string) ($_POST['expected_status'] ?? ''));
$booking = $bookingId > 0 ? booking_fetch($bookingId) : null;
if (!$booking || (int) $booking['courier_user_id'] !== (int) $user['id']) {
    set_flash('error', 'Booking not found.');
    redirect(base_url() . '/dashboard/courier/bookings.php');
}

$result = booking_transition($bookingId, 'cancelled_by_courier', (int) $user['id'], [
    'reason' => 'Courier cancelled from dashboard.',
    'expected_status' => $expectedStatus,
]);
if ($result['success']) {
    notify(
        (int) $booking['owner_user_id'],
        'booking_cancelled',
        'Booking cancelled',
        'The courier cancelled the request for ' . $booking['listing_title'] . '.',
        base_url() . '/dashboard/owner/booking-detail.php?id=' . $bookingId,
        $bookingId,
        (int) $booking['listing_id']
    );
    set_flash('success', 'Your booking request was cancelled.');
} else {
    set_flash('error', $result['error']);
}

redirect(base_url() . '/dashboard/courier/bookings.php');
