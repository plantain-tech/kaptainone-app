<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!is_logged_in() && !has_role('admin')) {
    redirect('login.php');
}

$types = ['rental_charge','deposit_hold','deposit_release','deposit_capture','payout_to_owner','platform_fee','refund_to_courier','chargeback','adjustment'];
$statuses = ['pending','completed','failed','reversed'];
$sortMap = [
    'id' => 't.id',
    'booking' => 't.booking_id',
    'type' => 't.transaction_type',
    'amount' => 't.amount_pln',
    'status' => 't.status',
    'created' => 't.created_at',
];

$type = $_GET['type'] ?? 'all';
$status = $_GET['status'] ?? 'all';
$dateFrom = trim((string) ($_GET['date_from'] ?? ''));
$dateTo = trim((string) ($_GET['date_to'] ?? ''));
$sort = $_GET['sort'] ?? 'created';
$dir = strtolower((string) ($_GET['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

if (!isset($sortMap[$sort])) {
    $sort = 'created';
}

$where = ['1=1'];
$params = [];
if (in_array($type, $types, true)) {
    $where[] = 't.transaction_type = ?';
    $params[] = $type;
}
if (in_array($status, $statuses, true)) {
    $where[] = 't.status = ?';
    $params[] = $status;
}
if ($dateFrom !== '') {
    $where[] = 'DATE(t.created_at) >= ?';
    $params[] = $dateFrom;
}
if ($dateTo !== '') {
    $where[] = 'DATE(t.created_at) <= ?';
    $params[] = $dateTo;
}
$whereSql = implode(' AND ', $where);
$totalRows = (int) (Database::fetch("SELECT COUNT(*) AS total FROM transactions t WHERE {$whereSql}", $params)['total'] ?? 0);
$totalPages = max(1, (int) ceil($totalRows / $perPage));
$orderSql = $sortMap[$sort] . ' ' . strtoupper($dir);

$transactions = Database::fetchAll(
    "SELECT t.*, l.id AS listing_id, l.title AS listing_title
     FROM transactions t
     JOIN bookings b ON b.id = t.booking_id
     JOIN listings l ON l.id = b.listing_id
     WHERE {$whereSql}
     ORDER BY {$orderSql}, t.id DESC
     LIMIT {$perPage} OFFSET {$offset}",
    $params
);

function transaction_label(string $type): string {
    return ucwords(str_replace('_', ' ', $type));
}

function transaction_sort_url(string $column, string $currentSort, string $currentDir): string {
    $query = $_GET;
    $query['sort'] = $column;
    $query['dir'] = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
    unset($query['page']);
    return '?' . http_build_query($query);
}

function transaction_sort_marker(string $column, string $currentSort, string $currentDir): string {
    if ($column !== $currentSort) {
        return '';
    }
    return $currentDir === 'asc' ? ' ↑' : ' ↓';
}

$pageTitle = 'Transactions';
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
    <main class="admin-workspace finance-admin-page">
        <header class="admin-header finance-admin-header">
            <div>
                <a href="dashboard.php" class="section-kicker">Admin dashboard</a>
                <h1>Transactions</h1>
                <p>All payment transactions across the platform.</p>
            </div>
            <nav class="finance-tabs" aria-label="Financial admin">
                <a class="is-active" href="transactions.php">Transactions</a>
                <a href="payouts.php">Payouts</a>
            </nav>
        </header>

        <section class="finance-panel">
            <form method="get" class="finance-filter-grid">
                <label>Type<select class="form-control" name="type"><option value="all">All types</option><?php foreach ($types as $value): ?><option value="<?= e($value) ?>" <?= $type === $value ? 'selected' : '' ?>><?= e(transaction_label($value)) ?></option><?php endforeach; ?></select></label>
                <label>Status<select class="form-control" name="status"><option value="all">All statuses</option><?php foreach ($statuses as $value): ?><option value="<?= e($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= e(ucwords($value)) ?></option><?php endforeach; ?></select></label>
                <label>Date from<input class="form-control" type="date" name="date_from" value="<?= e($dateFrom) ?>"></label>
                <label>Date to<input class="form-control" type="date" name="date_to" value="<?= e($dateTo) ?>"></label>
                <div class="finance-filter-actions">
                    <button class="btn btn-primary" type="submit">FILTER</button>
                    <a href="transactions.php" class="finance-action-link">Clear filters</a>
                </div>
            </form>
        </section>

        <section class="finance-panel">
            <?php if (!$transactions): ?>
                <div class="finance-empty-state">
                    <h3>No transactions found.</h3>
                    <p>Try adjusting the type, status, or date range.</p>
                </div>
            <?php else: ?>
                <div class="finance-table-wrap">
                    <table class="finance-table finance-table-carded">
                        <thead>
                            <tr>
                                <th><a href="<?= e(transaction_sort_url('id', $sort, $dir)) ?>">ID<?= e(transaction_sort_marker('id', $sort, $dir)) ?></a></th>
                                <th><a href="<?= e(transaction_sort_url('booking', $sort, $dir)) ?>">Booking<?= e(transaction_sort_marker('booking', $sort, $dir)) ?></a></th>
                                <th>Listing</th>
                                <th><a href="<?= e(transaction_sort_url('type', $sort, $dir)) ?>">Type<?= e(transaction_sort_marker('type', $sort, $dir)) ?></a></th>
                                <th><a href="<?= e(transaction_sort_url('amount', $sort, $dir)) ?>">Amount<?= e(transaction_sort_marker('amount', $sort, $dir)) ?></a></th>
                                <th><a href="<?= e(transaction_sort_url('status', $sort, $dir)) ?>">Status<?= e(transaction_sort_marker('status', $sort, $dir)) ?></a></th>
                                <th>Processor</th>
                                <th><a href="<?= e(transaction_sort_url('created', $sort, $dir)) ?>">Created<?= e(transaction_sort_marker('created', $sort, $dir)) ?></a></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td data-label="ID"><span class="finance-id">#<?= (int) $transaction['id'] ?></span></td>
                                    <td data-label="Booking"><a class="mono-link" href="booking-detail.php?id=<?= (int) $transaction['booking_id'] ?>">#<?= (int) $transaction['booking_id'] ?></a></td>
                                    <td data-label="Listing"><a href="../listing.php?id=<?= (int) $transaction['listing_id'] ?>"><?= e(truncate($transaction['listing_title'], 25)) ?></a></td>
                                    <td data-label="Type"><span class="finance-type finance-type-<?= e($transaction['transaction_type']) ?>"><?= e(transaction_label($transaction['transaction_type'])) ?></span></td>
                                    <td data-label="Amount"><strong class="finance-amount"><?= e(number_format((float) $transaction['amount_pln'], 2, '.', ',')) ?> <?= e($transaction['currency']) ?></strong></td>
                                    <td data-label="Status"><span class="finance-status finance-status-<?= e($transaction['status']) ?>"><?= e(ucwords($transaction['status'])) ?></span></td>
                                    <td data-label="Processor"><span class="finance-muted"><?= e($transaction['processor']) ?></span></td>
                                    <td data-label="Created"><span title="<?= e(format_date($transaction['created_at'], 'H:i:s')) ?>"><?= e(format_date($transaction['created_at'])) ?></span></td>
                                    <td data-label="Actions"><a class="finance-action-link" href="booking-detail.php?id=<?= (int) $transaction['booking_id'] ?>">View booking</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="finance-pagination">
                    <span>Showing <?= $offset + 1 ?>-<?= min($offset + $perPage, $totalRows) ?> of <?= $totalRows ?> transactions</span>
                    <div>
                        <a class="btn btn-secondary <?= $page <= 1 ? 'is-disabled' : '' ?>" href="?<?= e(http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)]))) ?>">Previous</a>
                        <a class="btn btn-secondary <?= $page >= $totalPages ? 'is-disabled' : '' ?>" href="?<?= e(http_build_query(array_merge($_GET, ['page' => min($totalPages, $page + 1)]))) ?>">Next</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
