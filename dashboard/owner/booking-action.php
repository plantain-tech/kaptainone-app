<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/booking_state.php';
require_once __DIR__ . '/../../includes/notifications.php';
require_role('asset_owner');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? '')) {
    set_flash('error', 'Security check failed. Please try again.');
    redirect(base_url() . '/dashboard/owner/bookings.php');
}

$user = current_user();
$bookingId = (int) ($_POST['booking_id'] ?? 0);
$action = $_POST['action'] ?? '';
$message = trim((string) ($_POST['message'] ?? ($_POST['owner_message'] ?? '')));
$expectedStatus = trim((string) ($_POST['expected_status'] ?? ''));
$booking = $bookingId > 0 ? booking_fetch($bookingId) : null;

if (!$booking || (int) $booking['owner_user_id'] !== (int) $user['id']) {
    set_flash('error', 'Booking not found.');
    redirect(base_url() . '/dashboard/owner/bookings.php');
}

$target = match ($action) {
    'approve' => 'approved',
    'reject' => 'rejected',
    'cancel' => 'cancelled_by_owner',
    default => '',
};

if ($target === '') {
    set_flash('error', 'Choose a valid booking action.');
    redirect(base_url() . '/dashboard/owner/bookings.php');
}
if ($target === 'cancelled_by_owner' && $message === '') {
    set_flash('error', 'Please add a reason before cancelling an approved booking.');
    redirect(base_url() . '/dashboard/owner/bookings.php');
}
if (strlen($message) > 300) {
    set_flash('error', 'Message must be 300 characters or less.');
    redirect(base_url() . '/dashboard/owner/bookings.php');
}

$result = booking_transition($bookingId, $target, (int) $user['id'], [
    'owner_response_message' => $message,
    'expected_status' => $expectedStatus,
]);
if ($result['success']) {
    $type = $target === 'approved' ? 'booking_approved' : ($target === 'rejected' ? 'booking_rejected' : 'booking_cancelled');
    $title = $target === 'approved' ? 'Rental request approved' : ($target === 'rejected' ? 'Rental request declined' : 'Booking cancelled');
    $body = $message !== '' ? $message : ($target === 'approved' ? 'The owner approved your rental request.' : 'The owner updated your booking request.');
    notify(
        (int) $booking['courier_user_id'],
        $type,
        $title,
        $body,
        base_url() . '/dashboard/courier/booking-detail.php?id=' . $bookingId,
        $bookingId,
        (int) $booking['listing_id']
    );
    $flash = $target === 'approved' ? 'You approved the rental request. The courier has been notified.' : ($target === 'rejected' ? 'You declined the rental request.' : 'Booking cancelled and the courier has been notified.');
    set_flash('success', $flash);
} else {
    set_flash('error', $result['error']);
}

redirect(base_url() . '/dashboard/owner/bookings.php');
