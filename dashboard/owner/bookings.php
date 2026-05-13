<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/booking_state.php';
require_role('asset_owner');

$user = current_user();
$rows = Database::fetchAll(
    "SELECT b.*, l.title AS listing_title, l.asset_type, l.location_district,
            p.full_name AS courier_name,
            (SELECT COUNT(*) FROM bookings cb WHERE cb.courier_user_id = b.courier_user_id AND cb.status = 'completed') AS completed_count,
            (SELECT file_path FROM listing_photos lp WHERE lp.listing_id = l.id ORDER BY lp.sort_order, lp.id LIMIT 1) AS photo_path
     FROM bookings b
     JOIN listings l ON l.id = b.listing_id
     LEFT JOIN user_profiles p ON p.user_id = b.courier_user_id
     WHERE b.owner_user_id = ?
     ORDER BY b.created_at DESC",
    [$user['id']]
);

$groups = [
    'New requests' => ['requested'],
    'Approved' => ['approved'],
    'Active rentals' => ['active'],
    'History' => ['rejected', 'cancelled_by_courier', 'cancelled_by_owner', 'completed', 'expired', 'disputed'],
];

$dashboardTitle = 'Booking Requests';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-panel">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Courier requests</span>
            <h2>You have <?= count(array_filter($rows, fn($r) => $r['status'] === 'requested')) ?> pending requests</h2>
        </div>
        <a class="btn btn-secondary" href="<?= base_url() ?>/dashboard/owner/listings.php">Review my listings</a>
    </div>
    <?php if (!$rows): ?>
        <p class="text-muted">No booking requests yet. Couriers will find your listings on the marketplace — make sure your photos and description are great.</p>
    <?php endif; ?>
</section>

<?php foreach ($groups as $title => $statuses): ?>
    <?php $items = array_values(array_filter($rows, fn($row) => in_array($row['status'], $statuses, true))); ?>
    <?php if (!$items && $title !== 'New requests') continue; ?>
    <section class="booking-section">
        <div class="booking-section__header">
            <h2><?= e($title) ?></h2>
            <span class="status-pill"><?= count($items) ?></span>
        </div>
        <?php if (!$items): ?><div class="dash-panel"><p class="text-muted">No new requests right now.</p></div><?php endif; ?>
        <?php foreach ($items as $booking): ?>
            <?php $courierName = trim((string) ($booking['courier_name'] ?? '')); $courierFirst = $courierName !== '' ? strtok($courierName, ' ') : 'New courier'; ?>
            <article class="booking-card">
                <?php if ($booking['photo_path']): ?><img class="booking-card__thumb" src="<?= e(base_url() . $booking['photo_path']) ?>" alt="<?= e($booking['listing_title']) ?>"><?php else: ?><div class="booking-card__thumb"></div><?php endif; ?>
                <div>
                    <div class="booking-card__top">
                        <h3><?= e($booking['listing_title']) ?></h3>
                        <span class="status-pill status-<?= e($booking['status']) ?>"><?= e(booking_status_label($booking['status'])) ?></span>
                    </div>
                    <div class="booking-card__meta">
                        <span><?= e($courierFirst) ?> · <?= (int) $booking['completed_count'] > 0 ? 'Verified' : 'New courier' ?></span>
                        <span><?= e(format_date($booking['start_date'])) ?> - <?= e(format_date($booking['end_date'])) ?> · <?= (int) $booking['rental_days'] ?> days · <?= e(number_format((float) $booking['total_amount_pln'], 0)) ?> PLN</span>
                    </div>
                    <?php if ($booking['courier_message']): ?><div class="booking-card__message"><?= nl2br(e($booking['courier_message'])) ?></div><?php endif; ?>
                    <div class="app-actions">
                        <a class="btn btn-secondary" href="<?= base_url() ?>/dashboard/owner/booking-detail.php?id=<?= (int) $booking['id'] ?>">View details</a>
                    </div>
                    <?php if ($booking['status'] === 'requested'): ?>
                        <form class="booking-action-form" method="post" action="<?= base_url() ?>/dashboard/owner/booking-action.php">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                            <input type="hidden" name="expected_status" value="<?= e($booking['status']) ?>">
                            <textarea class="form-control" name="message" maxlength="300" rows="2" placeholder="Add a friendly note for the courier — pickup details, anything they should know."></textarea>
                            <div class="app-actions">
                                <button class="btn btn-primary" type="submit" name="action" value="approve">Approve</button>
                                <button class="btn btn-secondary" type="submit" name="action" value="reject">Reject</button>
                            </div>
                        </form>
                    <?php elseif ($booking['status'] === 'approved'): ?>
                        <form class="booking-action-form" method="post" action="<?= base_url() ?>/dashboard/owner/booking-action.php" onsubmit="return confirm('Cancel this approved booking?');">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                            <input type="hidden" name="expected_status" value="<?= e($booking['status']) ?>">
                            <textarea class="form-control" name="message" maxlength="300" required placeholder="What changed?"></textarea>
                            <button class="btn btn-secondary" type="submit" name="action" value="cancel">Cancel booking</button>
                        </form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endforeach; ?>

<?php require_once __DIR__ . '/_footer.php'; ?>
