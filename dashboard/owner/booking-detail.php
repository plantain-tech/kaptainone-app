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
$rentalAmount = max(0, (float) $booking['total_amount_pln'] - (float) $booking['deposit_snapshot_pln']);
$depositAmount = (float) $booking['deposit_snapshot_pln'];
$totalAmount = (float) $booking['total_amount_pln'];
$assetLabel = $booking['asset_type'] === 'ebike' ? 'E-bike' : 'Scooter';
$eventDates = [];
foreach ($events as $event) {
    $statusKey = $event['new_status'] ?: $event['event_type'];
    $eventDates[$statusKey] ??= $event['created_at'];
}
$lifecycle = ['requested' => 'Requested', 'approved' => 'Approved', 'active' => 'Active', 'completed' => 'Completed'];
$terminalStatuses = ['rejected', 'cancelled_by_courier', 'cancelled_by_owner', 'expired', 'disputed'];
$statusOrder = array_keys($lifecycle);
$currentIndex = array_search($booking['status'], $statusOrder, true);
$isTerminal = in_array($booking['status'], $terminalStatuses, true);

$dashboardTitle = 'Booking Detail';
require_once __DIR__ . '/_layout.php';
?>

<section class="booking-detail-shell">
    <article class="booking-detail-hero">
        <div class="booking-detail-hero__status">
            <span class="status-pill booking-status-badge status-<?= e($booking['status']) ?>"><?= e(booking_status_label($booking['status'])) ?></span>
        </div>
        <div class="booking-detail-hero__media">
            <?php if ($booking['photo_path']): ?>
                <img src="<?= e(base_url() . $booking['photo_path']) ?>" alt="<?= e($booking['listing_title']) ?>">
            <?php else: ?>
                <div class="booking-detail-hero__placeholder"><?= e($assetLabel) ?></div>
            <?php endif; ?>
        </div>
        <div class="booking-detail-hero__main">
            <div class="booking-detail-meta">
                <span><?= e($assetLabel) ?></span>
                <span><?= e($booking['location_district']) ?></span>
            </div>
            <h2><?= e($booking['listing_title']) ?></h2>
            <div class="booking-date-row">
                <strong><?= e(format_date($booking['start_date'])) ?></strong>
                <span class="booking-date-arrow">to</span>
                <strong><?= e(format_date($booking['end_date'])) ?></strong>
                <span class="booking-duration-pill"><?= (int) $booking['rental_days'] ?> days</span>
            </div>
        </div>
        <div class="booking-detail-total">
            <span>Total due at approval</span>
            <strong><?= e(number_format($totalAmount, 0, '.', ',')) ?> PLN</strong>
        </div>
    </article>

    <?php if ($booking['status'] === 'requested'): ?>
        <form class="booking-detail-actions booking-detail-actions--stacked" method="post" action="<?= base_url() ?>/dashboard/owner/booking-action.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
            <input type="hidden" name="expected_status" value="<?= e($booking['status']) ?>">
            <textarea class="form-control" name="message" maxlength="300" rows="2" placeholder="Add a friendly note for the courier - pickup details, anything they should know."></textarea>
            <div class="app-actions">
                <button class="btn btn-primary" type="submit" name="action" value="approve">Approve request</button>
                <button class="btn btn-secondary" type="submit" name="action" value="reject">Reject request</button>
            </div>
        </form>
    <?php elseif ($booking['status'] === 'approved'): ?>
        <form class="booking-detail-actions booking-detail-actions--stacked" method="post" action="<?= base_url() ?>/dashboard/owner/booking-action.php" onsubmit="return confirm('Cancel this approved booking?');">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
            <input type="hidden" name="expected_status" value="<?= e($booking['status']) ?>">
            <textarea class="form-control" name="message" maxlength="300" required rows="2" placeholder="What changed?"></textarea>
            <button class="btn btn-secondary" type="submit" name="action" value="cancel">Cancel booking</button>
        </form>
    <?php endif; ?>

    <div class="booking-detail-grid">
        <section class="booking-info-card">
            <span class="section-kicker">Cost summary</span>
            <h3>Financial breakdown</h3>
            <div class="booking-money-list">
                <div><span>Rental (<?= (int) $booking['rental_days'] ?> days)</span><strong><?= e(number_format($rentalAmount, 0, '.', ',')) ?> PLN</strong></div>
                <div><span>Deposit (refundable)</span><strong><?= e(number_format($depositAmount, 0, '.', ',')) ?> PLN</strong></div>
                <div class="booking-money-list__total"><span>Total due at approval</span><strong><?= e(number_format($totalAmount, 0, '.', ',')) ?> PLN</strong></div>
            </div>
            <p class="text-muted">Deposit will be refunded after the rental ends in good condition.</p>
        </section>

        <section class="booking-info-card">
            <span class="section-kicker">Courier</span>
            <h3>Courier and pickup</h3>
            <div class="booking-party-list">
                <div><span>Courier</span><strong><?= e($courierFirst) ?> <em>New courier</em></strong></div>
                <div><span>Pickup district</span><strong><?= e($booking['location_district']) ?></strong></div>
                <div><span>Courier ID</span><strong>#<?= (int) $booking['courier_user_id'] ?></strong></div>
            </div>
        </section>
    </div>

    <section class="booking-info-card">
        <span class="section-kicker">Conversation</span>
        <h3>Messages</h3>
        <?php if ($booking['courier_message']): ?>
            <div class="booking-message-block">
                <strong>Courier's message</strong>
                <p><?= nl2br(e($booking['courier_message'])) ?></p>
            </div>
        <?php endif; ?>
        <?php if ($booking['owner_response_message']): ?>
            <div class="booking-message-block">
                <strong>Your response</strong>
                <p><?= nl2br(e($booking['owner_response_message'])) ?></p>
            </div>
        <?php endif; ?>
        <?php if (!$booking['courier_message'] && !$booking['owner_response_message']): ?>
            <p class="text-muted">No messages exchanged yet.</p>
        <?php endif; ?>
    </section>

    <section class="booking-info-card">
        <span class="section-kicker">Lifecycle</span>
        <h3>Status timeline</h3>
        <div class="booking-lifecycle <?= $isTerminal ? 'has-terminal' : '' ?>">
            <?php foreach ($lifecycle as $status => $label): ?>
                <?php
                    $stepIndex = array_search($status, $statusOrder, true);
                    $stateClass = 'is-future';
                    if ($booking['status'] === $status) {
                        $stateClass = 'is-current';
                    } elseif ($currentIndex !== false && $stepIndex < $currentIndex) {
                        $stateClass = 'is-complete';
                    } elseif (isset($eventDates[$status])) {
                        $stateClass = 'is-complete';
                    }
                ?>
                <div class="booking-lifecycle__step <?= e($stateClass) ?>">
                    <span></span>
                    <strong><?= e($label) ?></strong>
                    <small><?= isset($eventDates[$status]) ? e(format_date($eventDates[$status], 'M j, Y')) : 'Not yet' ?></small>
                </div>
            <?php endforeach; ?>
            <?php if ($isTerminal): ?>
                <div class="booking-lifecycle__step is-terminal">
                    <span></span>
                    <strong><?= e(booking_status_label($booking['status'])) ?></strong>
                    <small><?= isset($eventDates[$booking['status']]) ? e(format_date($eventDates[$booking['status']], 'M j, Y')) : 'Final state' ?></small>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="booking-info-card">
        <details class="booking-history">
            <summary>Show full history (<?= count($events) ?> events)</summary>
            <div class="booking-history__list">
                <?php foreach ($events as $event): ?>
                    <div class="booking-history__item">
                        <span></span>
                        <div>
                            <strong><?= e(booking_status_label($event['new_status'] ?: $event['event_type'])) ?></strong>
                            <p title="<?= e(format_date($event['created_at'], 'M j, Y H:i')) ?>"><?= e(booking_relative_time($event['created_at'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    </section>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
