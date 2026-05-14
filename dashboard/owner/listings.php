<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/listing_helpers.php';
require_role('asset_owner');

$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security check failed. Please try again.');
    } else {
        $listingId = (int) ($_POST['listing_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $listing = $listingId > 0 ? get_listing($listingId) : null;
        if ($listing && user_can_manage_listing($listing, $user)) {
            $newStatus = match ($action) {
                'pause' => 'paused',
                'activate' => 'active',
                'archive' => 'archived',
                default => null,
            };
            if ($newStatus) {
                Database::update('listings', ['status' => $newStatus], 'id = :id', ['id' => $listingId]);
                $flashMessage = match ($newStatus) {
                    'paused' => "Listing paused. It's no longer visible to couriers.",
                    'active' => "Listing reactivated. It's now visible on the marketplace.",
                    'archived' => 'Listing archived. You can restore it from the archived tab anytime.',
                    default => 'Listing status updated.',
                };
                set_flash('success', $flashMessage);
            }
        }
    }
    redirect(base_url() . '/dashboard/owner/listings.php');
}

$status = $_GET['status'] ?? 'all';
$sort = $_GET['sort'] ?? 'updated_desc';
$listings = fetch_owner_listings((int) $user['id'], $status, $sort);

$dashboardTitle = 'My Listings';
require_once __DIR__ . '/_layout.php';
?>

<section class="dash-panel">
    <div class="section-heading">
        <div>
            <span class="section-kicker">Asset inventory</span>
            <h2>Your listings</h2>
        </div>
    </div>

    <form method="get" class="owner-listing-filters" data-owner-listing-filters>
        <label>
            <span>Status</span>
            <select name="status" class="owner-listing-select">
                <?php foreach (['all' => 'All statuses', 'active' => 'Active', 'paused' => 'Paused', 'draft' => 'Draft', 'archived' => 'Archived'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            <span>Sort</span>
            <select name="sort" class="owner-listing-select">
                <option value="updated_desc" <?= $sort === 'updated_desc' ? 'selected' : '' ?>>Recently updated</option>
                <option value="created_desc" <?= $sort === 'created_desc' ? 'selected' : '' ?>>Newest first</option>
                <option value="updated_asc" <?= $sort === 'updated_asc' ? 'selected' : '' ?>>Oldest first</option>
            </select>
        </label>
        <button class="btn btn-primary owner-listing-filter-apply" type="submit" data-filter-apply>Apply</button>
        <?php if ($status !== 'all' || $sort !== 'updated_desc'): ?>
            <a class="btn btn-secondary" href="<?= base_url() ?>/dashboard/owner/listings.php">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (!$listings): ?>
        <div class="owner-listing-empty">
            <div class="owner-listing-empty__icon" aria-hidden="true">E</div>
            <h3>You haven't listed anything yet.</h3>
            <p>List your first e-bike or scooter to start earning from Warsaw couriers.</p>
            <a href="<?= base_url() ?>/dashboard/owner/listing-new.php" class="btn btn-primary">Create your first listing</a>
        </div>
    <?php else: ?>
        <div class="owner-listing-grid" aria-label="Owner listings">
            <?php foreach ($listings as $item): ?>
                <?php
                    $photo = listing_primary_photo($item);
                    $assetLabel = listing_asset_label($item['asset_type']);
                    $statusLabel = listing_status_label($item['status']);
                    $updatedAt = date('M j, Y', strtotime($item['updated_at']));
                    $toggleAction = $item['status'] === 'active' ? 'pause' : ($item['status'] === 'paused' ? 'activate' : null);
                    $toggleLabel = $toggleAction === 'pause' ? 'Pause' : ($toggleAction === 'activate' ? 'Activate' : null);
                ?>
                <article class="owner-listing-card" data-listing-id="<?= (int) $item['id'] ?>">
                    <div class="owner-listing-photo">
                        <?php if ($photo): ?>
                            <img src="<?= e(base_url() . $photo) ?>" alt="<?= e($item['title']) ?>">
                        <?php else: ?>
                            <div class="owner-listing-photo__placeholder">
                                <span><?= e($assetLabel) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="owner-listing-card__body">
                        <h3><?= e($item['title']) ?></h3>
                        <div class="owner-listing-meta">
                            <span><?= e($assetLabel) ?></span>
                            <span><?= e($item['location_district']) ?></span>
                        </div>
                        <div class="owner-listing-price">
                            <?= e(number_format((float) $item['weekly_price_pln'], 0)) ?> PLN <span>/ week</span>
                        </div>
                        <div class="owner-listing-status-row">
                            <span class="status-pill status-<?= e($item['status']) ?>"><?= e($statusLabel) ?></span>
                            <small>Updated <?= e($updatedAt) ?></small>
                        </div>
                    </div>

                    <div class="owner-listing-actions">
                        <a href="<?= base_url() ?>/dashboard/owner/listing-edit.php?id=<?= (int) $item['id'] ?>" class="btn btn-primary">Edit</a>
                        <?php if ($toggleAction): ?>
                            <button
                                class="btn btn-secondary"
                                type="button"
                                data-listing-action="<?= e($toggleAction) ?>"
                                data-listing-id="<?= (int) $item['id'] ?>"
                                data-listing-title="<?= e($item['title']) ?>"
                            ><?= e($toggleLabel) ?></button>
                        <?php endif; ?>
                        <button
                            class="owner-listing-link-button owner-listing-link-button--danger"
                            type="button"
                            data-listing-action="archive"
                            data-listing-id="<?= (int) $item['id'] ?>"
                            data-listing-title="<?= e($item['title']) ?>"
                        >Archive</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<div class="owner-listing-modal" data-owner-listing-modal hidden>
    <div class="owner-listing-modal__overlay" data-modal-close></div>
    <div class="owner-listing-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="ownerListingModalTitle">
        <div class="owner-listing-modal__header">
            <h3 id="ownerListingModalTitle" data-modal-title>Confirm action</h3>
            <button type="button" class="owner-listing-modal__close" aria-label="Close dialog" data-modal-close>&times;</button>
        </div>
        <p data-modal-message></p>
        <form method="post" data-modal-form>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="listing_id" data-modal-listing-id>
            <input type="hidden" name="action" data-modal-action>
            <div class="owner-listing-modal__actions">
                <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary" data-modal-confirm>Confirm</button>
            </div>
        </form>
    </div>
</div>

<script src="<?= asset('js/owner-listings.js') ?>"></script>

<?php require_once __DIR__ . '/_footer.php'; ?>
