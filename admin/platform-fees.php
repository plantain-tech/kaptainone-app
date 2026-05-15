<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/payment_helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!is_logged_in() && !has_role('admin')) {
    redirect('login.php');
}

$delays = [1, 3, 7, 14, 30];
$errors = [];

function admin_user_id_for_fee_update(): ?int {
    if (empty($_SESSION['admin_id'])) {
        return current_user()['id'] ?? null;
    }
    $admin = Database::fetch(
        "SELECT u.id FROM admin_users a LEFT JOIN users u ON u.email = a.email WHERE a.id = ?",
        [$_SESSION['admin_id']]
    );
    return $admin && !empty($admin['id']) ? (int) $admin['id'] : null;
}

function platform_fee_rows(): array {
    $rows = Database::fetchAll("SELECT * FROM platform_fee_tiers ORDER BY payout_delay_days");
    $byDelay = [];
    foreach ($rows as $row) {
        $byDelay[(int) $row['payout_delay_days']] = $row;
    }
    return $byDelay;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Security check failed. Please try again.';
    }

    $submitted = $_POST['fees'] ?? [];
    foreach ($delays as $delay) {
        $raw = trim((string) ($submitted[$delay] ?? ''));
        if ($raw === '' || !is_numeric($raw)) {
            $errors["fee_{$delay}"] = 'Enter a fee percentage.';
            continue;
        }
        $fee = (float) $raw;
        if ($fee < 0) {
            $errors["fee_{$delay}"] = 'Fee must be positive.';
        } elseif ($fee > 50) {
            $errors["fee_{$delay}"] = 'Fee cannot exceed 50%.';
        }
    }

    if (!$errors) {
        $adminUserId = admin_user_id_for_fee_update();
        foreach ($delays as $delay) {
            Database::query(
                "INSERT INTO platform_fee_tiers (payout_delay_days, fee_percentage, updated_by_user_id)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    fee_percentage = VALUES(fee_percentage),
                    updated_by_user_id = VALUES(updated_by_user_id)",
                [$delay, round((float) $submitted[$delay], 2), $adminUserId]
            );
        }
        set_flash('success', 'Platform fee settings updated. New bookings will use the updated rates.');
        redirect('platform-fees.php');
    }
}

$tiers = platform_fee_rows();
$inputFees = [];
foreach ($delays as $delay) {
    $inputFees[$delay] = $_POST['fees'][$delay] ?? number_format((float) ($tiers[$delay]['fee_percentage'] ?? (default_payment_fee_rates()[$delay] * 100)), 2, '.', '');
}

$usageRows = Database::fetchAll(
    "SELECT p.payout_delay_days, COUNT(*) AS total
     FROM payouts p
     GROUP BY p.payout_delay_days"
);
$usage = [];
foreach ($usageRows as $row) {
    $usage[(int) $row['payout_delay_days']] = (int) $row['total'];
}
arsort($usage);
$popularTier = $usage ? array_key_first($usage) : 7;

$pageTitle = 'Platform Fee Settings';
$flashSuccess = flash('success');
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
                <h1>Platform fee settings</h1>
                <p>Configure platform commission rates for each payout speed tier.</p>
            </div>
            <nav class="finance-tabs" aria-label="Financial admin">
                <a href="transactions.php">Transactions</a>
                <a href="payouts.php">Payouts</a>
                <a class="is-active" href="platform-fees.php">Fee settings</a>
            </nav>
        </header>

        <?php if ($flashSuccess): ?><div class="alert alert-success"><?= e($flashSuccess) ?></div><?php endif; ?>
        <?php if (isset($errors['csrf'])): ?><div class="alert alert-error"><?= e($errors['csrf']) ?></div><?php endif; ?>

        <section class="finance-warning">
            <strong>Changes apply to new bookings only.</strong>
            <span>Existing bookings keep the platform fee captured at approval time.</span>
        </section>

        <form method="post" class="fee-settings-form" data-fee-settings-form>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="fee-tier-grid">
                <?php foreach ($delays as $delay): ?>
                    <?php
                        $fee = (float) $inputFees[$delay];
                        $ownerReceives = max(0, 1000 - (1000 * $fee / 100));
                    ?>
                    <article class="fee-tier-card" data-fee-card>
                        <span class="section-kicker"><?= $delay ?>-day payout</span>
                        <h2><?= e(number_format($fee, 2)) ?>%</h2>
                        <label>
                            Fee percentage
                            <span class="fee-input-row">
                                <input class="form-control <?= isset($errors["fee_{$delay}"]) ? 'has-error' : '' ?>" type="number" min="0" max="50" step="0.01" name="fees[<?= $delay ?>]" value="<?= e(number_format($fee, 2, '.', '')) ?>" data-fee-input>
                                <b>%</b>
                            </span>
                        </label>
                        <?php if (isset($errors["fee_{$delay}"])): ?><small class="field-error"><?= e($errors["fee_{$delay}"]) ?></small><?php endif; ?>
                        <p>Example: 1,000 PLN rental &rarr; owner receives <strong data-fee-example><?= e(number_format($ownerReceives, 0, '.', ',')) ?> PLN</strong></p>
                    </article>
                <?php endforeach; ?>
            </div>

            <section class="finance-panel fee-usage-panel">
                <span class="section-kicker">Usage snapshot</span>
                <h3>Current payout tier usage</h3>
                <div class="fee-usage-grid">
                    <?php foreach ($delays as $delay): ?>
                        <div>
                            <span><?= $delay ?> days</span>
                            <strong><?= $usage[$delay] ?? 0 ?></strong>
                            <small><?= e(number_format((float) $inputFees[$delay], 2)) ?>% fee</small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-muted">Most popular tier: <?= (int) $popularTier ?>-day payout.</p>
            </section>

            <div class="payout-save-bar">
                <div>
                    <strong>Fee settings</strong>
                    <span>Transparent pricing, configurable without code deploys.</span>
                </div>
                <div class="app-actions">
                    <a class="btn btn-secondary" href="payouts.php">Cancel</a>
                    <button class="btn btn-primary" type="submit" data-fee-save>SAVE FEE SETTINGS</button>
                </div>
            </div>
        </form>
    </main>

    <script>
        (() => {
            document.querySelectorAll('[data-fee-card]').forEach((card) => {
                const input = card.querySelector('[data-fee-input]');
                const example = card.querySelector('[data-fee-example]');
                const update = () => {
                    const value = Number.parseFloat(input.value || '0');
                    const safe = Number.isFinite(value) ? Math.min(50, Math.max(0, value)) : 0;
                    const payout = Math.max(0, 1000 - (1000 * safe / 100));
                    example.textContent = `${Math.round(payout).toLocaleString('en-US')} PLN`;
                    input.classList.toggle('has-error', value < 0 || value > 50 || !Number.isFinite(value));
                };
                input.addEventListener('input', update);
                update();
            });
            const form = document.querySelector('[data-fee-settings-form]');
            form?.addEventListener('submit', () => {
                const save = form.querySelector('[data-fee-save]');
                if (save) {
                    save.disabled = true;
                    save.textContent = 'SAVING...';
                }
            });
        })();
    </script>
</body>
</html>
