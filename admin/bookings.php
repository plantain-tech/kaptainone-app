<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/booking_state.php';
require_once __DIR__ . '/../includes/notifications.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!is_logged_in() && !has_role('admin')) {
    redirect('login.php');
}

function admin_actor_user_id(): ?int {
    if (empty($_SESSION['admin_id'])) return null;
    $admin = Database::fetch(
        "SELECT u.id FROM admin_users a LEFT JOIN users u ON u.email = a.email WHERE a.id = ?",
        [$_SESSION['admin_id']]
    );
    return $admin && $admin['id'] ? (int) $admin['id'] : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security check failed. Please try again.');
    } else {
        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $note = trim((string) ($_POST['admin_note'] ?? ''));
        $booking = $bookingId > 0 ? booking_fetch($bookingId) : null;
        $result = booking_transition($bookingId, 'disputed', admin_actor_user_id(), ['actor_role' => 'admin', 'admin_note' => $note]);
        if ($result['success'] && $booking) {
            $body = $note !== '' ? $note : 'A booking was marked disputed for review by Kaptain One support.';
            notify((int) $booking['courier_user_id'], 'system', 'Booking marked for review', $body, base_url() . '/dashboard/courier/booking-detail.php?id=' . $bookingId, $bookingId, (int) $booking['listing_id']);
            notify((int) $booking['owner_user_id'], 'system', 'Booking marked for review', $body, base_url() . '/dashboard/owner/booking-detail.php?id=' . $bookingId, $bookingId, (int) $booking['listing_id']);
        }
        set_flash($result['success'] ? 'success' : 'error', $result['success'] ? 'Booking marked disputed for investigation.' : $result['error']);
    }
    redirect('bookings.php');
}

$status = $_GET['status'] ?? 'all';
$courierEmail = trim($_GET['courier_email'] ?? '');
$ownerEmail = trim($_GET['owner_email'] ?? '');
$listingTerm = trim($_GET['listing'] ?? '');
$where = ['1=1'];
$params = [];
if (in_array($status, ['requested','approved','rejected','cancelled_by_courier','cancelled_by_owner','active','completed','disputed','expired'], true)) {
    $where[] = 'b.status = ?';
    $params[] = $status;
}
if ($courierEmail !== '') {
    $where[] = 'cu.email LIKE ?';
    $params[] = '%' . $courierEmail . '%';
}
if ($ownerEmail !== '') {
    $where[] = 'ou.email LIKE ?';
    $params[] = '%' . $ownerEmail . '%';
}
if ($listingTerm !== '') {
    $where[] = 'l.title LIKE ?';
    $params[] = '%' . $listingTerm . '%';
}

$bookings = Database::fetchAll(
    "SELECT b.*, l.title AS listing_title, cu.email AS courier_email, ou.email AS owner_email
     FROM bookings b
     JOIN listings l ON l.id = b.listing_id
     JOIN users cu ON cu.id = b.courier_user_id
     JOIN users ou ON ou.id = b.owner_user_id
     WHERE " . implode(' AND ', $where) . "
     ORDER BY b.created_at DESC",
    $params
);

$pageTitle = 'Bookings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Admin</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body class="admin-page">
    <main class="admin-workspace">
        <header class="admin-header">
            <div><a href="dashboard.php" class="section-kicker">Admin dashboard</a><h1>Marketplace Bookings</h1></div>
            <a href="listings.php" class="btn btn-secondary">Listings</a>
        </header>
        <?php if ($message = flash('success')): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
        <?php if ($message = flash('error')): ?><div class="alert alert-error"><?= e($message) ?></div><?php endif; ?>

        <section class="admin-panel">
            <form method="get" class="app-form two-col">
                <label>Status<select class="form-control" name="status"><option value="all">All</option><?php foreach (['requested','approved','active','rejected','cancelled_by_courier','cancelled_by_owner','completed','expired','disputed'] as $s): ?><option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e(booking_status_label($s)) ?></option><?php endforeach; ?></select></label>
                <label>Courier email<input class="form-control" name="courier_email" value="<?= e($courierEmail) ?>"></label>
                <label>Owner email<input class="form-control" name="owner_email" value="<?= e($ownerEmail) ?>"></label>
                <label>Listing<input class="form-control" name="listing" value="<?= e($listingTerm) ?>"></label>
                <button class="btn btn-secondary" type="submit">Filter</button>
            </form>
        </section>

        <section class="admin-panel">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>ID</th><th>Listing</th><th>Courier</th><th>Owner</th><th>Status</th><th>Dates</th><th>Total</th><th>Created</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?= (int) $booking['id'] ?></td>
                                <td><?= e($booking['listing_title']) ?></td>
                                <td><?= e($booking['courier_email']) ?></td>
                                <td><?= e($booking['owner_email']) ?></td>
                                <td><span class="status-pill status-<?= e($booking['status']) ?>"><?= e(booking_status_label($booking['status'])) ?></span></td>
                                <td><?= e(format_date($booking['start_date'])) ?> - <?= e(format_date($booking['end_date'])) ?></td>
                                <td><?= e(number_format((float) $booking['total_amount_pln'], 0)) ?> PLN</td>
                                <td><?= e(format_date($booking['created_at'])) ?></td>
                                <td>
                                    <div class="app-actions">
                                        <a class="btn btn-secondary" href="booking-detail.php?id=<?= (int) $booking['id'] ?>">Detail</a>
                                        <?php if (!in_array($booking['status'], booking_terminal_statuses(), true)): ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                                                <input type="hidden" name="admin_note" value="Admin marked disputed from bookings list.">
                                                <button class="btn btn-secondary" type="submit">Mark disputed</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$bookings): ?><tr><td colspan="9">No bookings found.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
