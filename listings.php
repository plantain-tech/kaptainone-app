<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/listing_helpers.php';

$pageTitle = 'Warsaw E-Bike & Scooter Listings';
$pageDescription = 'Browse active Warsaw e-bike and scooter rentals for Wolt, Glovo, Bolt Food, Uber Eats, and Stuart couriers.';

$assetType = $_GET['asset_type'] ?? 'all';
$district = $_GET['district'] ?? 'all';
$maxPrice = trim($_GET['max_price'] ?? '');
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 24;
$offset = ($page - 1) * $perPage;

$where = ["l.status = 'active'"];
$params = [];
if (in_array($assetType, ['ebike', 'scooter'], true)) {
    $where[] = 'l.asset_type = ?';
    $params[] = $assetType;
}
if (in_array($district, warsaw_districts(), true)) {
    $where[] = 'l.location_district = ?';
    $params[] = $district;
}
if ($maxPrice !== '' && is_numeric($maxPrice)) {
    $where[] = 'l.weekly_price_pln <= ?';
    $params[] = (float) $maxPrice;
}

$order = match ($sort) {
    'price_asc' => 'l.weekly_price_pln ASC, l.created_at DESC',
    'price_desc' => 'l.weekly_price_pln DESC, l.created_at DESC',
    default => 'l.created_at DESC',
};

$whereSql = implode(' AND ', $where);
$countRow = Database::fetch("SELECT COUNT(*) AS total FROM listings l WHERE {$whereSql}", $params);
$total = (int) ($countRow['total'] ?? 0);
$listings = Database::fetchAll(
    "SELECT l.*,
            (SELECT file_path FROM listing_photos lp WHERE lp.listing_id = l.id ORDER BY lp.sort_order, lp.id LIMIT 1) AS primary_photo
     FROM listings l
     WHERE {$whereSql}
     ORDER BY {$order}
     LIMIT {$perPage} OFFSET {$offset}",
    $params
);

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">Warsaw marketplace</span>
        <h1>Browse e-bikes and scooters for courier work</h1>
        <p class="contact-intro-copy">Find active rentals in Warsaw districts, compare weekly prices, and get ready for Wolt, Glovo, Bolt Food, Uber Eats, or Stuart delivery shifts.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form method="get" class="app-form two-col" style="margin-bottom:2rem;">
            <label>Asset type
                <select class="form-control" name="asset_type">
                    <option value="all">All</option>
                    <option value="ebike" <?= $assetType === 'ebike' ? 'selected' : '' ?>>E-bikes</option>
                    <option value="scooter" <?= $assetType === 'scooter' ? 'selected' : '' ?>>Scooters</option>
                </select>
            </label>
            <label>District
                <select class="form-control" name="district">
                    <option value="all">All districts</option>
                    <?php foreach (warsaw_districts() as $item): ?>
                        <option value="<?= e($item) ?>" <?= $district === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Max weekly price
                <input class="form-control" name="max_price" type="number" min="50" max="1500" step="1" value="<?= e($maxPrice) ?>" placeholder="No limit">
            </label>
            <label>Sort
                <select class="form-control" name="sort">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Lowest price</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Highest price</option>
                </select>
            </label>
            <button class="btn btn-primary" type="submit">Filter listings</button>
        </form>

        <?php if (!$listings): ?>
            <div class="dash-panel">
                <h2>No listings match your filters.</h2>
                <p>Try widening the search or check back soon — new equipment is being added.</p>
            </div>
        <?php else: ?>
            <div class="package-grid">
                <?php foreach ($listings as $listing): ?>
                    <article class="package-card">
                        <?php if ($photo = listing_primary_photo($listing)): ?>
                            <img src="<?= e(base_url() . $photo) ?>" alt="<?= e($listing['title']) ?>" style="width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px;margin-bottom:1rem;">
                        <?php else: ?>
                            <div class="dash-panel" style="aspect-ratio:4/3;display:grid;place-items:center;margin-bottom:1rem;">Photo coming soon</div>
                        <?php endif; ?>
                        <div class="package-card__top">
                            <span class="package-card__category"><?= e(listing_asset_label($listing['asset_type'])) ?></span>
                            <span class="status-pill"><?= e(listing_condition_label($listing['condition_grade'])) ?></span>
                        </div>
                        <h3><?= e($listing['title']) ?></h3>
                        <p><?= e($listing['location_district']) ?></p>
                        <strong class="dash-number"><?= e(number_format((float) $listing['weekly_price_pln'], 0)) ?> PLN</strong>
                        <p>Deposit: <?= e(number_format((float) $listing['deposit_pln'], 0)) ?> PLN</p>
                        <a href="<?= base_url() ?>/listing.php?id=<?= (int) $listing['id'] ?>" class="btn btn-primary">View details</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($total > $perPage): ?>
            <div class="app-actions" style="margin-top:2rem;">
                <?php if ($page > 1): ?>
                    <a class="btn btn-secondary" href="?<?= e(http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>">Previous</a>
                <?php endif; ?>
                <?php if ($offset + $perPage < $total): ?>
                    <a class="btn btn-secondary" href="?<?= e(http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
