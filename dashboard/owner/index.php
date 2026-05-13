<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/listing_helpers.php';
require_role('asset_owner');

$user = current_user();
$activeCount = (int) (Database::fetch("SELECT COUNT(*) AS count FROM listings WHERE owner_user_id = ? AND status = 'active'", [$user['id']])['count'] ?? 0);
$newRequestCount = (int) (Database::fetch("SELECT COUNT(*) AS count FROM bookings WHERE owner_user_id = ? AND status = 'requested'", [$user['id']])['count'] ?? 0);
$activeRentalCount = (int) (Database::fetch("SELECT COUNT(*) AS count FROM bookings WHERE owner_user_id = ? AND status = 'active'", [$user['id']])['count'] ?? 0);
$completedThisMonth = (int) (Database::fetch("SELECT COUNT(*) AS count FROM bookings WHERE owner_user_id = ? AND status = 'completed' AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')", [$user['id']])['count'] ?? 0);
$recentListings = Database::fetchAll(
    "SELECT * FROM listings WHERE owner_user_id = ? ORDER BY updated_at DESC LIMIT 3",
    [$user['id']]
);
$recentRequests = Database::fetchAll(
    "SELECT b.*, l.title AS listing_title, p.full_name AS courier_name
     FROM bookings b
     JOIN listings l ON l.id = b.listing_id
     LEFT JOIN user_profiles p ON p.user_id = b.courier_user_id
     WHERE b.owner_user_id = ? AND b.status = 'requested'
     ORDER BY b.created_at DESC LIMIT 3",
    [$user['id']]
);

$dashboardTitle = 'Owner Overview';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-grid">
    <div class="dash-panel hero-panel">
        <h2>Welcome, <?= e($user['full_name'] ?? $user['email']) ?></h2>
        <p>Your owner account is ready. Create and manage Warsaw e-bike or scooter listings for couriers.</p>
        <div class="app-actions">
            <a href="<?= base_url() ?>/dashboard/owner/listing-new.php" class="btn btn-primary">Create your first listing</a>
            <?php if (has_role('gig_worker')): ?>
                <a href="<?= base_url() ?>/dashboard/index.php" class="btn btn-secondary">Courier View</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="dash-panel">
        <h3>Active Listings</h3>
        <strong class="dash-number"><?= $activeCount ?></strong>
        <p>You have <?= $activeCount ?> active <?= $activeCount === 1 ? 'listing' : 'listings' ?>.</p>
    </div>
    <div class="dash-panel">
        <h3>Upcoming Payouts</h3>
        <strong class="dash-number"><?= $newRequestCount ?></strong>
        <p>New booking requests need review.</p>
        <a href="<?= base_url() ?>/dashboard/owner/bookings.php">Review requests</a>
    </div>
    <div class="dash-panel">
        <h3>Active Rentals</h3>
        <strong class="dash-number"><?= $activeRentalCount ?></strong>
        <p>Rentals currently marked active.</p>
    </div>
    <div class="dash-panel">
        <h3>Completed This Month</h3>
        <strong class="dash-number"><?= $completedThisMonth ?></strong>
        <p>0 PLN earned shown until Day 6 payments are connected.</p>
    </div>
</section>

<section class="dash-panel">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Booking requests</span>
            <h2>Recent courier requests</h2>
        </div>
        <a href="<?= base_url() ?>/dashboard/owner/bookings.php" class="btn btn-secondary">View all</a>
    </div>
    <?php if (!$recentRequests): ?>
        <p class="text-muted">No new requests yet. Couriers will see your active listings on the marketplace.</p>
    <?php else: ?>
        <div class="booking-section">
            <?php foreach ($recentRequests as $request): ?>
                <article class="booking-card">
                    <div></div>
                    <div>
                        <h3><?= e($request['listing_title']) ?></h3>
                        <p><?= e($request['courier_name'] ?: 'New courier') ?> requested <?= e(format_date($request['start_date'])) ?> - <?= e(format_date($request['end_date'])) ?>.</p>
                        <?php if ($request['courier_message']): ?><div class="booking-card__message"><?= nl2br(e($request['courier_message'])) ?></div><?php endif; ?>
                        <form class="booking-action-form" method="post" action="<?= base_url() ?>/dashboard/owner/booking-action.php">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="booking_id" value="<?= (int) $request['id'] ?>">
                            <input type="hidden" name="expected_status" value="<?= e($request['status']) ?>">
                            <textarea class="form-control" name="message" maxlength="300" rows="2" placeholder="Optional note for the courier"></textarea>
                            <div class="app-actions">
                                <button class="btn btn-primary" type="submit" name="action" value="approve">Approve</button>
                                <button class="btn btn-secondary" type="submit" name="action" value="reject">Reject</button>
                            </div>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="dash-panel">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Recent listings</span>
            <h2>Latest activity</h2>
        </div>
        <a href="<?= base_url() ?>/dashboard/owner/listings.php" class="btn btn-secondary">View all</a>
    </div>
    <?php if (!$recentListings): ?>
        <p>No listings yet. Add your first e-bike or scooter when you're ready.</p>
    <?php else: ?>
        <div class="dash-grid">
            <?php foreach ($recentListings as $item): ?>
                <article class="dash-panel">
                    <span class="status-pill"><?= e(listing_status_label($item['status'])) ?></span>
                    <h3><?= e($item['title']) ?></h3>
                    <p><?= e(number_format((float) $item['weekly_price_pln'], 0)) ?> PLN/week</p>
                    <p><?= (int) $item['view_count'] ?> views</p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
