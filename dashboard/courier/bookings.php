<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/booking_state.php';
require_user();

$user = current_user();
if (!has_role('gig_worker')) {
    set_flash('error', 'Courier access is required to view rental requests.');
    redirect(base_url() . '/dashboard/owner/');
}

$rows = Database::fetchAll(
    "SELECT b.*, l.title AS listing_title, l.asset_type, l.location_district,
            (SELECT file_path FROM listing_photos lp WHERE lp.listing_id = l.id ORDER BY lp.sort_order, lp.id LIMIT 1) AS photo_path
     FROM bookings b
     JOIN listings l ON l.id = b.listing_id
     WHERE b.courier_user_id = ?
     ORDER BY b.created_at DESC",
    [$user['id']]
);

$groups = [
    'Pending requests' => ['requested'],
    'Active rentals' => ['active'],
    'Approved upcoming' => ['approved'],
    'History' => ['rejected', 'cancelled_by_courier', 'cancelled_by_owner', 'completed', 'expired', 'disputed'],
];

$dashboardTitle = 'My Bookings';
require_once __DIR__ . '/../_layout.php';
?>

<section class="dash-panel">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Rental requests</span>
            <h2>Your booking activity</h2>
        </div>
        <a href="<?= base_url() ?>/listings.php" class="btn btn-primary">Browse listings</a>
    </div>
    <?php if (!$rows): ?>
        <p class="text-muted">You haven't requested any rentals yet. Browse listings to find an e-bike or scooter for your delivery work.</p>
    <?php endif; ?>
</section>

<?php foreach ($groups as $title => $statuses): ?>
    <?php $items = array_values(array_filter($rows, fn($row) => in_array($row['status'], $statuses, true))); ?>
    <?php if (!$items && $title !== 'Pending requests') continue; ?>
    <section class="booking-section">
        <div class="booking-section__header">
            <h2><?= e($title) ?></h2>
            <span class="status-pill"><?= count($items) ?></span>
        </div>
        <?php if (!$items): ?>
            <div class="dash-panel"><p class="text-muted">No pending requests right now.</p></div>
        <?php endif; ?>
        <?php foreach ($items as $booking): ?>
            <article class="booking-card">
                <?php if ($booking['photo_path']): ?><img class="booking-card__thumb" src="<?= e(base_url() . $booking['photo_path']) ?>" alt="<?= e($booking['listing_title']) ?>"><?php else: ?><div class="booking-card__thumb"></div><?php endif; ?>
                <div>
                    <div class="booking-card__top">
                        <h3><?= e($booking['listing_title']) ?></h3>
                        <span class="status-pill status-<?= e($booking['status']) ?>"><?= e(booking_status_label($booking['status'])) ?></span>
                    </div>
                    <div class="booking-card__meta">
                        <span><?= e(format_date($booking['start_date'])) ?> - <?= e(format_date($booking['end_date'])) ?> · <?= (int) $booking['rental_days'] ?> days</span>
                        <span><?= e(number_format((float) $booking['total_amount_pln'], 0)) ?> PLN total · <?= e($booking['location_district']) ?></span>
                    </div>
                    <div class="app-actions">
                        <a class="btn btn-secondary" href="<?= base_url() ?>/dashboard/courier/booking-detail.php?id=<?= (int) $booking['id'] ?>">View details</a>
                        <?php if (in_array($booking['status'], ['requested', 'approved'], true)): ?>
                            <form method="post" action="<?= base_url() ?>/dashboard/courier/booking-action.php" onsubmit="return confirm('Cancel this booking request?');">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                                <input type="hidden" name="expected_status" value="<?= e($booking['status']) ?>">
                                <button class="btn btn-secondary" type="submit"><?= $booking['status'] === 'requested' ? 'Cancel request' : 'Cancel booking' ?></button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../_footer.php'; ?>
