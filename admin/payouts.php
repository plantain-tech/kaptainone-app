<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/payment_processor_mock.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!is_logged_in() && !has_role('admin')) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $payoutId = (int) ($_POST['payout_id'] ?? 0);
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Security check failed. Please try again.');
        redirect('payouts.php');
    }

    if ($action === 'release' && $payoutId > 0) {
        $payout = Database::fetch(
            "SELECT p.*, COALESCE(up.full_name, u.email) AS owner_name
             FROM payouts p
             JOIN users u ON u.id = p.owner_user_id
             LEFT JOIN user_profiles up ON up.user_id = u.id
             WHERE p.id = ?",
            [$payoutId]
        );
        if (!$payout || $payout['status'] !== 'scheduled') {
            set_flash('error', 'Only scheduled payouts can be released early.');
            redirect('payouts.php');
        }
        payments_release_payout_mock($payoutId);
        set_flash('success', 'Payout released to ' . ($payout['owner_name'] ?: 'owner') . '.');
        redirect('payouts.php');
    }
}

$status = $_GET['status'] ?? 'all';
$ownerEmail = trim((string) ($_GET['owner_email'] ?? ''));
$dateFrom = trim((string) ($_GET['date_from'] ?? ''));
$dateTo = trim((string) ($_GET['date_to'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;
$where = ['1=1'];
$params = [];
$statuses = ['scheduled','processing','completed','failed','held'];

if (in_array($status, $statuses, true)) {
    $where[] = 'p.status = ?';
    $params[] = $status;
}
if ($ownerEmail !== '') {
    $where[] = 'u.email LIKE ?';
    $params[] = '%' . $ownerEmail . '%';
}
if ($dateFrom !== '') {
    $where[] = 'p.scheduled_for >= ?';
    $params[] = $dateFrom;
}
if ($dateTo !== '') {
    $where[] = 'p.scheduled_for <= ?';
    $params[] = $dateTo;
}
$whereSql = implode(' AND ', $where);
$totalRows = (int) (Database::fetch("SELECT COUNT(*) AS total FROM payouts p JOIN users u ON u.id = p.owner_user_id WHERE {$whereSql}", $params)['total'] ?? 0);
$totalPages = max(1, (int) ceil($totalRows / $perPage));

$payouts = Database::fetchAll(
    "SELECT p.*, u.email AS owner_email, COALESCE(up.full_name, '') AS owner_name, l.id AS listing_id, l.title AS listing_title
     FROM payouts p
     JOIN users u ON u.id = p.owner_user_id
     LEFT JOIN user_profiles up ON up.user_id = u.id
     JOIN bookings b ON b.id = p.booking_id
     JOIN listings l ON l.id = b.listing_id
     WHERE {$whereSql}
     ORDER BY p.scheduled_for DESC, p.created_at DESC
     LIMIT {$perPage} OFFSET {$offset}",
    $params
);

function payout_owner_short_name(array $payout): string {
    $name = trim((string) ($payout['owner_name'] ?? ''));
    if ($name !== '') {
        $parts = preg_split('/\s+/', $name);
        $first = $parts[0] ?? 'Owner';
        $lastInitial = isset($parts[1]) ? ' ' . strtoupper(substr($parts[1], 0, 1)) . '.' : '';
        return $first . $lastInitial;
    }
    $email = (string) ($payout['owner_email'] ?? 'owner');
    return strtok($email, '@') ?: 'Owner';
}

function payout_label(string $status): string {
    return ucwords(str_replace('_', ' ', $status));
}

$pageTitle = 'Payouts';
$flashSuccess = flash('success');
$flashError = flash('error');
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
                <h1>Payouts</h1>
                <p>Scheduled and completed payouts to asset owners.</p>
            </div>
            <nav class="finance-tabs" aria-label="Financial admin">
                <a href="transactions.php">Transactions</a>
                <a class="is-active" href="payouts.php">Payouts</a>
                <a href="platform-fees.php">Fee settings</a>
            </nav>
        </header>

        <?php if ($flashSuccess): ?><div class="alert alert-success"><?= e($flashSuccess) ?></div><?php endif; ?>
        <?php if ($flashError): ?><div class="alert alert-error"><?= e($flashError) ?></div><?php endif; ?>

        <section class="finance-panel">
            <form method="get" class="finance-filter-grid">
                <label>Status<select class="form-control" name="status"><option value="all">All statuses</option><?php foreach ($statuses as $value): ?><option value="<?= e($value) ?>" <?= $status === $value ? 'selected' : '' ?>><?= e(payout_label($value)) ?></option><?php endforeach; ?></select></label>
                <label>Owner email<input class="form-control" name="owner_email" value="<?= e($ownerEmail) ?>" placeholder="owner@example.com"></label>
                <label>Scheduled from<input class="form-control" type="date" name="date_from" value="<?= e($dateFrom) ?>"></label>
                <label>Scheduled to<input class="form-control" type="date" name="date_to" value="<?= e($dateTo) ?>"></label>
                <div class="finance-filter-actions">
                    <button class="btn btn-primary" type="submit">FILTER</button>
                    <a href="payouts.php" class="finance-action-link">Clear filters</a>
                </div>
            </form>
        </section>

        <section class="finance-panel">
            <?php if (!$payouts): ?>
                <div class="finance-empty-state">
                    <h3>No payouts match your filters.</h3>
                    <p>Try adjusting the date range or status.</p>
                </div>
            <?php else: ?>
                <div class="finance-table-wrap">
                    <table class="finance-table finance-table-carded">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Owner</th>
                                <th>Booking</th>
                                <th>Listing</th>
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
                                    <td data-label="ID"><span class="finance-id">#<?= (int) $payout['id'] ?></span></td>
                                    <td data-label="Owner"><span title="<?= e($payout['owner_email']) ?>"><?= e(payout_owner_short_name($payout)) ?></span></td>
                                    <td data-label="Booking"><a class="mono-link" href="booking-detail.php?id=<?= (int) $payout['booking_id'] ?>">#<?= (int) $payout['booking_id'] ?></a></td>
                                    <td data-label="Listing"><a href="../listing.php?id=<?= (int) $payout['listing_id'] ?>"><?= e(truncate($payout['listing_title'], 25)) ?></a></td>
                                    <td data-label="Amount"><strong class="finance-amount"><?= e(number_format((float) $payout['amount_pln'], 2, '.', ',')) ?> PLN</strong></td>
                                    <td data-label="Delay"><span class="finance-badge finance-badge-muted"><?= (int) $payout['payout_delay_days'] ?> days</span></td>
                                    <td data-label="Scheduled"><span title="<?= e($payout['scheduled_for']) ?>"><?= e(format_date($payout['scheduled_for'])) ?></span></td>
                                    <td data-label="Status"><span class="finance-status finance-status-<?= e($payout['status']) ?>"><?= e(payout_label($payout['status'])) ?></span></td>
                                    <td data-label="Actions" class="finance-actions-cell">
                                        <a class="finance-action-link" href="booking-detail.php?id=<?= (int) $payout['booking_id'] ?>">View booking</a>
                                        <?php if ($payout['status'] === 'scheduled'): ?>
                                            <button
                                                type="button"
                                                class="finance-action-link finance-release-trigger"
                                                data-payout-id="<?= (int) $payout['id'] ?>"
                                                data-payout-amount="<?= e(number_format((float) $payout['amount_pln'], 2, '.', ',')) ?>"
                                                data-owner-name="<?= e(payout_owner_short_name($payout)) ?>"
                                                data-booking-id="<?= (int) $payout['booking_id'] ?>"
                                            >Release now</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="finance-pagination">
                    <span>Showing <?= $offset + 1 ?>-<?= min($offset + $perPage, $totalRows) ?> of <?= $totalRows ?> payouts</span>
                    <div>
                        <a class="btn btn-secondary <?= $page <= 1 ? 'is-disabled' : '' ?>" href="?<?= e(http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)]))) ?>">Previous</a>
                        <a class="btn btn-secondary <?= $page >= $totalPages ? 'is-disabled' : '' ?>" href="?<?= e(http_build_query(array_merge($_GET, ['page' => min($totalPages, $page + 1)]))) ?>">Next</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <div class="finance-modal" data-release-modal hidden>
        <div class="finance-modal__backdrop" data-release-close></div>
        <div class="finance-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="release-title">
            <button class="finance-modal__close" type="button" data-release-close aria-label="Close">&times;</button>
            <h2 id="release-title">Release payout early?</h2>
            <p data-release-message>This will immediately release the selected payout.</p>
            <form method="post" class="finance-modal__actions">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="release">
                <input type="hidden" name="payout_id" value="" data-release-payout-id>
                <button class="btn btn-secondary" type="button" data-release-close>Cancel</button>
                <button class="btn btn-primary" type="submit">Release payout</button>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.querySelector('[data-release-modal]');
            if (!modal) return;
            const message = modal.querySelector('[data-release-message]');
            const input = modal.querySelector('[data-release-payout-id]');
            const close = () => {
                modal.hidden = true;
                document.body.classList.remove('modal-open');
            };
            document.querySelectorAll('[data-release-close]').forEach((button) => button.addEventListener('click', close));
            document.querySelectorAll('.finance-release-trigger').forEach((button) => {
                button.addEventListener('click', () => {
                    input.value = button.dataset.payoutId || '';
                    message.textContent = `This will immediately release ${button.dataset.payoutAmount} PLN to ${button.dataset.ownerName} for booking #${button.dataset.bookingId}, bypassing the scheduled date. This action cannot be undone.`;
                    modal.hidden = false;
                    document.body.classList.add('modal-open');
                });
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.hidden) close();
            });
        })();
    </script>
</body>
</html>
