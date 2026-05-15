<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/payment_helpers.php';
require_role('asset_owner');

$dashboardTitle = 'Payout Settings';
$user = current_user();
$allowedDelays = [1, 3, 7, 14, 30];
$feeRates = payment_fee_rates();
$prefs = get_owner_payout_preferences((int) $user['id']);
$selectedDelay = (int) ($prefs['default_payout_delay_days'] ?? 7);
if (!in_array($selectedDelay, $allowedDelays, true)) {
    $selectedDelay = 7;
}

$input = [
    'default_payout_delay_days' => $selectedDelay,
    'bank_account_iban' => $prefs['bank_account_iban'] ?? '',
    'bank_account_holder_name' => $prefs['bank_account_holder_name'] ?? '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = [
        'default_payout_delay_days' => (int) ($_POST['default_payout_delay_days'] ?? 7),
        'bank_account_iban' => strtoupper(trim((string) ($_POST['bank_account_iban'] ?? ''))),
        'bank_account_holder_name' => trim((string) ($_POST['bank_account_holder_name'] ?? '')),
    ];

    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors['csrf'] = 'Security check failed. Please try again.';
    }
    if (!in_array($input['default_payout_delay_days'], $allowedDelays, true)) {
        $errors['default_payout_delay_days'] = 'Choose a supported payout timing.';
    }
    $ibanCompact = preg_replace('/\s+/', '', $input['bank_account_iban']);
    if ($input['bank_account_iban'] !== '' && (!preg_match('/^[A-Z]{2}[0-9A-Z]{13,32}$/', $ibanCompact) || strlen($ibanCompact) > 34)) {
        $errors['bank_account_iban'] = 'Enter a valid IBAN format or leave it blank for now.';
    }
    if (strlen($input['bank_account_holder_name']) > 100) {
        $errors['bank_account_holder_name'] = 'Account holder name must be 100 characters or less.';
    }

    if (!$errors) {
        upsert_owner_payout_preferences(
            (int) $user['id'],
            $input['default_payout_delay_days'],
            $ibanCompact ?: null,
            $input['bank_account_holder_name'] ?: null
        );
        set_flash('success', 'Your payout settings were updated.');
        redirect(base_url() . '/dashboard/owner/payout-settings.php');
    }
}

$completedTotal = (float) (Database::fetch("SELECT COALESCE(SUM(amount_pln), 0) AS total FROM payouts WHERE owner_user_id = ? AND status = 'completed'", [$user['id']])['total'] ?? 0);
$nextPayout = Database::fetch(
    "SELECT * FROM payouts WHERE owner_user_id = ? AND status IN ('scheduled','processing') ORDER BY scheduled_for ASC, id ASC LIMIT 1",
    [$user['id']]
);
$scheduledCount = (int) (Database::fetch("SELECT COUNT(*) AS total FROM payouts WHERE owner_user_id = ? AND status = 'scheduled'", [$user['id']])['total'] ?? 0);

require_once __DIR__ . '/_layout.php';
?>

<section class="payout-page" data-payout-page>
    <div class="payout-hero">
        <span class="section-kicker">Money settings</span>
        <h2>Choose when you receive payments and manage your bank account details.</h2>
        <p>Pick the payout speed that fits your cash flow. Faster payouts have higher platform fees because processing costs more; slower payouts keep more rental income in your pocket.</p>
    </div>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <strong>Please fix the errors below.</strong>
            <?php if (isset($errors['csrf'])): ?><span><?= e($errors['csrf']) ?></span><?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="payout-form" data-payout-form data-initial-delay="<?= (int) $input['default_payout_delay_days'] ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="default_payout_delay_days" value="<?= (int) $input['default_payout_delay_days'] ?>" data-payout-delay-input>

        <section class="payout-panel">
            <div class="payout-section-heading">
                <div>
                    <span class="section-kicker">Payout timing</span>
                    <h3>Select your default payout speed</h3>
                </div>
                <span class="payout-recommendation">Most owners choose 7 days</span>
            </div>
            <p class="text-muted">Choose how quickly you want to receive payments after a rental starts. Existing bookings keep their original payout timing.</p>
            <?php if (isset($errors['default_payout_delay_days'])): ?><small class="field-error"><?= e($errors['default_payout_delay_days']) ?></small><?php endif; ?>

            <div class="payout-card-grid" role="radiogroup" aria-label="Payout timing">
                <?php foreach ($allowedDelays as $delay): ?>
                    <?php
                        $rate = $feeRates[$delay];
                        $feePercent = (int) round($rate * 100);
                        $receivePercent = 100 - $feePercent;
                        $exampleRental = 1000;
                        $exampleFee = calculate_platform_fee($exampleRental, $delay);
                        $examplePayout = calculate_owner_payout($exampleRental, $exampleFee);
                        $isSelected = (int) $input['default_payout_delay_days'] === $delay;
                    ?>
                    <button
                        type="button"
                        class="payout-option-card <?= $isSelected ? 'is-selected' : '' ?>"
                        data-payout-card
                        data-delay="<?= $delay ?>"
                        aria-pressed="<?= $isSelected ? 'true' : 'false' ?>"
                    >
                        <span class="payout-option-card__check" aria-hidden="true">&#10003;</span>
                        <small>Payout delay</small>
                        <strong><?= $delay ?> <?= $delay === 1 ? 'day' : 'days' ?></strong>
                        <div class="payout-option-card__fee">
                            <span>Platform fee</span>
                            <b><?= $feePercent ?>%</b>
                        </div>
                        <div class="payout-option-card__receive">
                            <span>You receive</span>
                            <b><?= $receivePercent ?>% of rental</b>
                        </div>
                        <p>Example: 1,000 PLN rental &rarr; you get <?= e(number_format($examplePayout, 0, '.', ',')) ?> PLN</p>
                    </button>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="payout-layout">
            <section class="payout-panel">
                <div class="payout-section-heading">
                    <div>
                        <span class="section-kicker">Secure account</span>
                        <h3>Secure Bank Account for Payouts</h3>
                    </div>
                </div>
                <div class="app-form two-col">
                    <label class="full-span">
                        IBAN
                        <input class="form-control <?= isset($errors['bank_account_iban']) ? 'has-error' : '' ?>" name="bank_account_iban" value="<?= e($input['bank_account_iban']) ?>" placeholder="PL61 1090 1014 0000 0712 1981 2874" inputmode="text">
                        <?php if (isset($errors['bank_account_iban'])): ?><small class="field-error"><?= e($errors['bank_account_iban']) ?></small><?php endif; ?>
                    </label>
                    <label class="full-span">
                        Account holder name
                        <input class="form-control <?= isset($errors['bank_account_holder_name']) ? 'has-error' : '' ?>" name="bank_account_holder_name" value="<?= e($input['bank_account_holder_name']) ?>" placeholder="Jan Kowalski">
                        <?php if (isset($errors['bank_account_holder_name'])): ?><small class="field-error"><?= e($errors['bank_account_holder_name']) ?></small><?php endif; ?>
                    </label>
                </div>
                <p class="payout-security-note">Your bank details are stored for payout use only. Couriers never see your bank account. Week 2 will add encrypted storage before real money moves.</p>
            </section>

            <section class="payout-panel">
                <span class="section-kicker">Current summary</span>
                <h3>Payout overview</h3>
                <div class="payout-summary-list">
                    <div><span>Total earned to date</span><strong><?= e(number_format($completedTotal, 0, '.', ',')) ?> PLN</strong></div>
                    <div><span>Scheduled payouts</span><strong><?= $scheduledCount ?></strong></div>
                    <div>
                        <span>Next payout</span>
                        <strong>
                            <?php if ($nextPayout): ?>
                                <?= e(number_format((float) $nextPayout['amount_pln'], 0, '.', ',')) ?> PLN on <?= e(format_date($nextPayout['scheduled_for'])) ?>
                            <?php else: ?>
                                No payouts yet
                            <?php endif; ?>
                        </strong>
                    </div>
                </div>
                <p class="text-muted">Your earnings will appear here once you complete your first rental.</p>
                <a href="<?= base_url() ?>/dashboard/owner/payouts.php" class="payout-history-link">View payout history &rarr;</a>
            </section>
        </div>

        <div class="payout-save-bar">
            <div>
                <strong data-payout-selected-label><?= (int) $input['default_payout_delay_days'] ?>-day payout selected</strong>
                <span>Transparent fee math, no hidden charges.</span>
            </div>
            <div class="app-actions">
                <a class="btn btn-secondary" href="<?= base_url() ?>/dashboard/owner/index.php">Cancel</a>
                <button class="btn btn-primary" type="submit" data-payout-save disabled>SAVE SETTINGS</button>
            </div>
        </div>
    </form>
</section>

<script src="<?= asset('js/payout-settings.js') ?>"></script>

<?php require_once __DIR__ . '/_footer.php'; ?>
