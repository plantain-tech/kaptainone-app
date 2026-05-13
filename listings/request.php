<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/listing_helpers.php';
require_once __DIR__ . '/../includes/booking_state.php';
require_once __DIR__ . '/../includes/notifications.php';

$listingId = (int) ($_GET['listing_id'] ?? $_POST['listing_id'] ?? 0);
$listing = $listingId > 0 ? get_listing($listingId) : null;
$user = current_user();
$errors = [];
$input = [
    'start_date' => $_POST['start_date'] ?? date('Y-m-d'),
    'end_date' => $_POST['end_date'] ?? date('Y-m-d', strtotime('+7 days')),
    'courier_message' => $_POST['courier_message'] ?? '',
];

if (!$listing || $listing['status'] !== 'active') {
    http_response_code(404);
    $pageTitle = 'Listing Unavailable';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <section class="page-hero"><div class="container"><h1>Listing unavailable</h1><p class="contact-intro-copy">This listing is not available for booking.</p><a class="btn btn-primary" href="<?= base_url() ?>/listings.php">Back to listings</a></div></section>
    <?php require_once __DIR__ . '/../includes/footer.php'; exit;
}

if (!$user) {
    set_flash('error', 'Sign in or register to request a rental.');
    redirect(base_url() . '/login.php?return_to=' . urlencode('/listings/request.php?listing_id=' . $listingId));
}

if ((int) $listing['owner_user_id'] === (int) $user['id']) {
    $pageTitle = 'Own Listing';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <section class="page-hero"><div class="container"><span class="section-kicker">Own listing</span><h1>You can't book your own equipment</h1><p class="contact-intro-copy">This listing belongs to your owner account.</p><a class="btn btn-secondary" href="<?= base_url() ?>/listing.php?id=<?= $listingId ?>">Back to listing</a></div></section>
    <?php require_once __DIR__ . '/../includes/footer.php'; exit;
}

if (!has_role('gig_worker')) {
    $pageTitle = 'Courier Role Needed';
    require_once __DIR__ . '/../includes/header.php';
    ?>
    <section class="page-hero"><div class="container"><span class="section-kicker">Courier role needed</span><h1>Add courier access</h1><p class="contact-intro-copy">You're signed in as an asset owner. To request rentals, add the courier role to your account profile.</p><a class="btn btn-primary" href="<?= base_url() ?>/dashboard/profile.php">Go to profile</a></div></section>
    <?php require_once __DIR__ . '/../includes/footer.php'; exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Security check failed. Please try again.';
    }

    $today = date('Y-m-d');
    $latestStart = date('Y-m-d', strtotime('+1 year'));
    if (($input['start_date'] ?? '') < $today) {
        $errors['start_date'] = 'Choose today or a future date.';
    }
    if (($input['start_date'] ?? '') >= $latestStart) {
        $errors['start_date'] = 'Choose a start date within the next 12 months.';
    }
    if (($input['end_date'] ?? '') <= ($input['start_date'] ?? '')) {
        $errors['end_date'] = 'End date must be after the start date.';
    }
    if (strlen(trim($input['courier_message'])) > 500) {
        $errors['courier_message'] = 'Message must be 500 characters or less.';
    }

    $breakdown = booking_price_breakdown((float) $listing['weekly_price_pln'], (float) $listing['deposit_pln'], $input['start_date'], $input['end_date']);
    if (!$breakdown) {
        $errors['dates'] = 'Bookings can be 1 to 30 days for the MVP.';
    }

    $existing = Database::fetch(
        "SELECT id FROM bookings WHERE listing_id = ? AND courier_user_id = ? AND status IN ('requested','approved') LIMIT 1",
        [$listingId, $user['id']]
    );
    if ($existing) {
        $errors['duplicate'] = 'You already have an active request for this listing.';
    }

    if (!$errors && $breakdown) {
        $result = booking_create_request($listing, (int) $user['id'], [
            'start_date' => $input['start_date'],
            'end_date' => $input['end_date'],
            'rental_days' => $breakdown['days'],
            'total_amount_pln' => $breakdown['total_pln'],
            'courier_message' => $input['courier_message'],
        ]);
        if ($result['success']) {
            notify(
                (int) $listing['owner_user_id'],
                'booking_requested',
                'New rental request',
                'A courier requested ' . $listing['title'] . '.',
                base_url() . '/dashboard/owner/booking-detail.php?id=' . (int) $result['booking_id'],
                (int) $result['booking_id'],
                $listingId
            );
            set_flash('success', "Your request was sent to the owner. You'll be notified when they respond.");
            redirect(base_url() . '/dashboard/courier/bookings.php');
        }
        $errors['request'] = $result['error'];
    }
}

$photo = listing_primary_photo($listing);
$pageTitle = 'Request Rental';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Rental request</span>
        <h1>Request this <?= e(strtolower(listing_asset_label($listing['asset_type']))) ?></h1>
        <p class="contact-intro-copy">Choose your rental dates and send a friendly note to the owner.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="dash-grid">
            <aside class="dash-panel">
                <?php if ($photo): ?><img class="booking-card__thumb" src="<?= e(base_url() . $photo) ?>" alt="<?= e($listing['title']) ?>"><?php endif; ?>
                <h2><?= e($listing['title']) ?></h2>
                <p><?= e($listing['location_district']) ?> · <?= e(listing_asset_label($listing['asset_type'])) ?></p>
                <div class="booking-breakdown">
                    <span><strong><?= e(number_format((float) $listing['weekly_price_pln'], 0)) ?> PLN</strong> / week</span>
                    <span>Deposit: <?= e(number_format((float) $listing['deposit_pln'], 0)) ?> PLN</span>
                    <span data-booking-total data-weekly="<?= e((string) $listing['weekly_price_pln']) ?>" data-deposit="<?= e((string) $listing['deposit_pln']) ?>">Pick dates to see the total.</span>
                </div>
            </aside>

            <div class="dash-panel">
                <?php if ($errors): ?>
                    <div class="alert alert-error">Please fix the errors below and try again.</div>
                <?php endif; ?>
                <form method="post" class="app-form" data-booking-request-form>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="listing_id" value="<?= $listingId ?>">
                    <label>Start date <span class="required-marker">*</span>
                        <input class="form-control" type="date" name="start_date" min="<?= e(date('Y-m-d')) ?>" value="<?= e($input['start_date']) ?>" required data-start-date>
                        <?php if (isset($errors['start_date'])): ?><span class="form-error"><?= e($errors['start_date']) ?></span><?php endif; ?>
                    </label>
                    <label>End date <span class="required-marker">*</span>
                        <input class="form-control" type="date" name="end_date" min="<?= e(date('Y-m-d', strtotime('+1 day'))) ?>" value="<?= e($input['end_date']) ?>" required data-end-date>
                        <?php if (isset($errors['end_date'])): ?><span class="form-error"><?= e($errors['end_date']) ?></span><?php endif; ?>
                    </label>
                    <label>Message to owner
                        <textarea class="form-control" name="courier_message" maxlength="500" rows="5" placeholder="Tell the owner a bit about yourself and your delivery schedule. Optional but recommended — owners respond faster to friendly requests." data-char-counter><?= e($input['courier_message']) ?></textarea>
                        <small class="text-muted"><span data-char-count><?= strlen($input['courier_message']) ?></span>/500 characters</small>
                        <?php if (isset($errors['courier_message'])): ?><span class="form-error"><?= e($errors['courier_message']) ?></span><?php endif; ?>
                    </label>
                    <?php foreach (['csrf','dates','duplicate','request'] as $key): ?>
                        <?php if (isset($errors[$key])): ?><div class="form-error full-span"><?= e($errors[$key]) ?></div><?php endif; ?>
                    <?php endforeach; ?>
                    <div class="app-actions">
                        <button class="btn btn-primary" type="submit">Send rental request</button>
                        <a class="btn btn-secondary" href="<?= base_url() ?>/listing.php?id=<?= $listingId ?>">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script src="<?= asset('js/booking-request.js') ?>"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
