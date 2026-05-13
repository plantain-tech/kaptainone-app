<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/listing_helpers.php';

$listingId = (int) ($_GET['id'] ?? 0);
$listing = $listingId > 0 ? get_listing($listingId) : null;
$user = current_user();

if (!$listing || ($listing['status'] !== 'active' && !user_can_manage_listing($listing, $user))) {
    http_response_code(404);
    $pageTitle = 'Listing Not Found';
    $pageDescription = 'This Kaptain One listing is not available.';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero">
        <div class="container">
            <h1>Listing not available</h1>
            <p class="contact-intro-copy">This listing is not currently active.</p>
            <a href="<?= base_url() ?>/listings.php" class="btn btn-primary">Back to listings</a>
        </div>
    </section>
    <?php require_once __DIR__ . '/includes/footer.php'; exit;
}

Database::query("UPDATE listings SET view_count = view_count + 1 WHERE id = ?", [$listingId]);
$listing['view_count'] = ((int) $listing['view_count']) + 1;
$photos = get_listing_photos($listingId);
$ownerName = trim((string) ($listing['owner_name'] ?? ''));
$ownerDisplay = $ownerName !== '' ? strtok($ownerName, ' ') : 'Verified owner';
$requestHref = base_url() . '/listings/request.php?listing_id=' . $listingId;
$requestText = 'Request rental';
$requestDisabled = false;
if (!$user) {
    $requestText = 'Sign in to request';
} elseif ((int) $listing['owner_user_id'] === (int) $user['id']) {
    $requestText = 'This is your listing';
    $requestDisabled = true;
} elseif (!has_role('gig_worker')) {
    $requestText = 'Add courier role to request';
    $requestHref = base_url() . '/dashboard/profile.php';
}

$pageTitle = $listing['title'] . ' | Warsaw Courier Rental';
$pageDescription = 'View this Warsaw ' . strtolower(listing_asset_label($listing['asset_type'])) . ' rental listing for courier work.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">All listings &gt; <?= e($listing['location_district']) ?> &gt; <?= e(listing_asset_label($listing['asset_type'])) ?></span>
        <h1><?= e($listing['title']) ?></h1>
        <p class="contact-intro-copy"><?= e($listing['location_district']) ?> · <?= e(listing_condition_label($listing['condition_grade'])) ?> · <?= e($ownerDisplay) ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="dash-grid">
            <div class="dash-panel">
                <?php if ($photos): ?>
                    <div class="package-grid" data-listing-gallery>
                        <?php foreach ($photos as $photo): ?>
                            <img class="listing-gallery-image" src="<?= e(base_url() . $photo['file_path']) ?>" data-full-src="<?= e(base_url() . $photo['file_path']) ?>" data-lightbox-image tabindex="0" alt="<?= e($listing['title']) ?>">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dash-panel" style="aspect-ratio:4/3;display:grid;place-items:center;">Photo coming soon</div>
                <?php endif; ?>
            </div>

            <aside class="dash-panel">
                <span class="status-pill"><?= e(listing_asset_label($listing['asset_type'])) ?></span>
                <h2><?= e(number_format((float) $listing['weekly_price_pln'], 0)) ?> PLN/week</h2>
                <p><?= e(number_format(listing_monthly_price($listing), 0)) ?> PLN/month estimate</p>
                <p>Deposit: <?= e(number_format((float) $listing['deposit_pln'], 0)) ?> PLN</p>
                <p>District: <?= e($listing['location_district']) ?></p>
                <?php if ($listing['brand'] || $listing['model']): ?>
                    <p>Model: <?= e(trim(($listing['brand'] ?? '') . ' ' . ($listing['model'] ?? ''))) ?></p>
                <?php endif; ?>
                <div class="app-actions">
                    <?php if ($requestDisabled): ?>
                        <button class="btn btn-secondary" type="button" disabled><?= e($requestText) ?></button>
                    <?php else: ?>
                        <a href="<?= e($requestHref) ?>" class="btn btn-primary"><?= e($requestText) ?></a>
                    <?php endif; ?>
                    <button class="btn btn-secondary" type="button" title="Sign in to save">Save for later</button>
                </div>
            </aside>
        </div>

        <div class="dash-panel" style="margin-top:2rem;">
            <h2>Listing details</h2>
            <p><?= nl2br(e($listing['description'] ?? '')) ?></p>
            <?php if (!empty($listing['included_items'])): ?>
                <h3>Included items</h3>
                <p><?= e($listing['included_items']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($photos): ?>
    <div class="listing-lightbox" data-listing-lightbox hidden>
        <button class="listing-lightbox__backdrop" type="button" data-lightbox-close aria-label="Close photo viewer"></button>
        <button class="listing-lightbox__close" type="button" data-lightbox-close aria-label="Close photo viewer">&times;</button>
        <button class="listing-lightbox__nav listing-lightbox__nav--prev" type="button" data-lightbox-prev aria-label="Previous photo">&lsaquo;</button>
        <img class="listing-lightbox__image" data-lightbox-full alt="">
        <button class="listing-lightbox__nav listing-lightbox__nav--next" type="button" data-lightbox-next aria-label="Next photo">&rsaquo;</button>
    </div>
    <script src="<?= asset('js/listing-lightbox.js') ?>"></script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
