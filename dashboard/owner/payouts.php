<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('asset_owner');

$dashboardTitle = 'Payout History';
$user = current_user();
$statuses = ['all', 'scheduled', 'processing', 'completed', 'failed', 'held'];
$status = $_GET['status'] ?? 'all';
if (!in_array($status, $statuses, true)) {
    $status = 'all';
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$where = ['p.owner_user_id = ?'];
$params = [$user['id']];
if ($status !== 'all') {
    $where[] = 'p.status = ?';
    $params[] = $status;
}
$whereSql = implode(' AND ', $where);

$totalRows = (int) (Database::fetch("SELECT COUNT(*) AS total FROM payouts p WHERE {$whereSql}", $params)['total'] ?? 0);
$totalPages = max(1, (int) ceil($totalRows / $perPage));

$payouts = Database::fetchAll(
    "SELECT p.*, l.id AS listing_id, l.title AS listing_title, lp.file_path AS photo_path
     FROM payouts p
     JOIN bookings b ON b.id = p.booking_id
     JOIN listings l ON l.id = b.listing_id
     LEFT JOIN listing_photos lp ON lp.listing_id = l.id AND lp.sort_order = (
        SELECT MIN(sort_order) FROM listing_photos WHERE listing_id = l.id
     )
     WHERE {$whereSql}
     ORDER BY p.scheduled_for DESC, p.created_at DESC
     LIMIT {$perPage} OFFSET {$offset}",
    $params
);

$summary = Database::fetch(
    "SELECT
        COALESCE(SUM(CASE WHEN status = 'completed' THEN amount_pln ELSE 0 END), 0) AS earned_total,
        COALESCE(SUM(CASE WHEN status IN ('scheduled','processing') THEN amount_pln ELSE 0 END), 0) AS pending_total
     FROM payouts
     WHERE owner_user_id = ?",
    [$user['id']]
);
$nextPayout = Database::fetch(
    "SELECT amount_pln, scheduled_for FROM payouts WHERE owner_user_id = ? AND status IN ('scheduled','processing') ORDER BY scheduled_for ASC, id ASC LIMIT 1",
    [$user['id']]
);

function payout_status_label(string $status): string {
    return ucwords(str_replace('_', ' ', $status));
}

require_once __DIR__ . '/_layout.php';
?>

<section class="finance-page owner-payouts-page">
    <header class="finance-page-header">
        <div>
            <span class="section-kicker">Money movement</span>
            <h2>Payout history</h2>
            <p>Track your scheduled and completed payouts from rentals.</p>
        </div>
        <a class="btn btn-primary" href="<?= base_url() ?>/dashboard/owner/payout-settings.php">PAYOUT SETTINGS</a>
    </header>

    <div class="finance-summary-grid">
        <article class="finance-summary-card">
            <span>Total earned</span>
            <strong><?= e(number_format((float) ($summary['earned_total'] ?? 0), 0, '.', ',')) ?> PLN</strong>
            <small>Completed payouts</small>
        </article>
        <article class="finance-summary-card">
            <span>Pending payouts</span>
            <strong><?= e(number_format((float) ($summary['pending_total'] ?? 0), 0, '.', ',')) ?> PLN</strong>
            <small>Scheduled or processing</small>
        </article>
        <article class="finance-summary-card">
            <span>Next payout</span>
            <?php if ($nextPayout): ?>
                <strong><?= e(number_format((float) $nextPayout['amount_pln'], 0, '.', ',')) ?> PLN</strong>
                <small><?= e(format_date($nextPayout['scheduled_for'])) ?></small>
            <?php else: ?>
                <strong>None yet</strong>
                <small>Approvals will create payouts</small>
            <?php endif; ?>
        </article>
    </div>

    <nav class="finance-filter-pills" aria-label="Payout status filter">
        <?php foreach ($statuses as $filter): ?>
            <a class="<?= $status === $filter ? 'is-active' : '' ?>" href="?status=<?= e($filter) ?>"><?= e($filter === 'all' ? 'All' : payout_status_label($filter)) ?></a>
        <?php endforeach; ?>
    </nav>

    <section class="finance-panel">
        <?php if (!$payouts): ?>
            <div class="finance-empty-state">
                <h3>No payouts yet.</h3>
                <p>Your earnings will appear here once you approve rental requests and complete bookings.</p>
                <a href="<?= base_url() ?>/dashboard/owner/bookings.php" class="btn btn-primary">VIEW BOOKING REQUESTS</a>
            </div>
        <?php else: ?>
            <div class="finance-table-wrap">
                <table class="finance-table finance-table-carded">
                    <thead>
                        <tr>
                            <th>Listing</th>
                            <th>Booking</th>
                            <th>Amount</th>
                            <th>Delay</th>
                            <th>Scheduled</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payouts as $payout): ?>
                            <tr>
                                <td data-label="Listing">
                                    <a class="finance-listing-cell" href="<?= base_url() ?>/listing.php?id=<?= (int) $payout['listing_id'] ?>">
                                        <?php if (!empty($payout['photo_path'])): ?>
                                            <img src="<?= e($payout['photo_path']) ?>" alt="">
                                        <?php else: ?>
                                            <span class="finance-thumb-placeholder">KO</span>
                                        <?php endif; ?>
                                        <span><?= e(truncate($payout['listing_title'], 30)) ?></span>
                                    </a>
                                </td>
                                <td data-label="Booking"><a class="mono-link" href="<?= base_url() ?>/dashboard/owner/booking-detail.php?id=<?= (int) $payout['booking_id'] ?>">#<?= (int) $payout['booking_id'] ?></a></td>
                                <td data-label="Amount"><strong class="finance-amount"><?= e(number_format((float) $payout['amount_pln'], 0, '.', ',')) ?> PLN</strong></td>
                                <td data-label="Delay"><span class="finance-badge finance-badge-muted"><?= (int) $payout['payout_delay_days'] ?> days</span></td>
                                <td data-label="Scheduled"><span title="<?= e($payout['scheduled_for']) ?>"><?= e(format_date($payout['scheduled_for'])) ?></span></td>
                                <td data-label="Status"><span class="finance-status finance-status-<?= e($payout['status']) ?>"><?= e(payout_status_label($payout['status'])) ?></span></td>
                                <td data-label="Actions"><a class="finance-action-link" href="<?= base_url() ?>/dashboard/owner/booking-detail.php?id=<?= (int) $payout['booking_id'] ?>">View booking</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="finance-pagination">
                <span>Showing <?= $offset + 1 ?>-<?= min($offset + $perPage, $totalRows) ?> of <?= $totalRows ?> payouts</span>
                <div>
                    <a class="btn btn-secondary <?= $page <= 1 ? 'is-disabled' : '' ?>" href="?status=<?= e($status) ?>&page=<?= max(1, $page - 1) ?>">Previous</a>
                    <a class="btn btn-secondary <?= $page >= $totalPages ? 'is-disabled' : '' ?>" href="?status=<?= e($status) ?>&page=<?= min($totalPages, $page + 1) ?>">Next</a>
                </div>
            </div>
        <?php endif; ?>
    </section>
</section>

<?php require_once __DIR__ . '/_footer.php'; ?>
