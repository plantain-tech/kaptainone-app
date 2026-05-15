<?php
/**
 * Payment and payout calculation helpers for the mocked Day 6 money flow.
 */

require_once __DIR__ . '/db.php';

function default_payment_fee_rates(): array {
    return [
        1 => 0.15,
        3 => 0.10,
        7 => 0.07,
        14 => 0.05,
        30 => 0.03,
    ];
}

function payment_fee_rates(): array {
    $fallback = default_payment_fee_rates();

    try {
        $tiers = Database::fetchAll("SELECT payout_delay_days, fee_percentage FROM platform_fee_tiers ORDER BY payout_delay_days");
        if (!$tiers) {
            return $fallback;
        }

        $rates = $fallback;
        foreach ($tiers as $tier) {
            $rates[(int) $tier['payout_delay_days']] = round(((float) $tier['fee_percentage']) / 100, 4);
        }
        return $rates;
    } catch (Throwable $e) {
        error_log('Platform fee tiers unavailable, using defaults: ' . $e->getMessage());
        return $fallback;
    }
}

function calculate_platform_fee(float $rentalAmountPln, int $payoutDelayDays): float {
    $feeRates = payment_fee_rates();
    if (!isset($feeRates[$payoutDelayDays])) {
        error_log("Platform fee tier not found for {$payoutDelayDays} days; using 7-day fallback.");
    }
    $rate = $feeRates[$payoutDelayDays] ?? $feeRates[7] ?? 0.07;
    return round($rentalAmountPln * $rate, 2);
}

function calculate_owner_payout(float $rentalAmountPln, float $platformFeePln): float {
    return round(max(0, $rentalAmountPln - $platformFeePln), 2);
}

function get_payout_schedule_date(string $approvalDate, int $payoutDelayDays): string {
    return date('Y-m-d', strtotime($approvalDate . ' + ' . $payoutDelayDays . ' days'));
}

function get_owner_payout_preferences(int $ownerUserId): array {
    $prefs = Database::fetch("SELECT * FROM owner_payout_preferences WHERE user_id = ?", [$ownerUserId]);
    if ($prefs) {
        return $prefs;
    }

    return [
        'user_id' => $ownerUserId,
        'default_payout_delay_days' => '7',
        'bank_account_iban' => null,
        'bank_account_holder_name' => null,
        'payout_method' => 'bank_transfer',
    ];
}

function upsert_owner_payout_preferences(int $ownerUserId, int $delayDays, ?string $iban, ?string $holderName): void {
    Database::query(
        "INSERT INTO owner_payout_preferences (user_id, default_payout_delay_days, bank_account_iban, bank_account_holder_name, payout_method)
         VALUES (?, ?, ?, ?, 'bank_transfer')
         ON DUPLICATE KEY UPDATE
            default_payout_delay_days = VALUES(default_payout_delay_days),
            bank_account_iban = VALUES(bank_account_iban),
            bank_account_holder_name = VALUES(bank_account_holder_name),
            payout_method = VALUES(payout_method)",
        [$ownerUserId, (string) $delayDays, $iban, $holderName]
    );
}

function booking_rental_amount(array $booking): float {
    return round(max(0, (float) $booking['total_amount_pln'] - (float) $booking['deposit_snapshot_pln']), 2);
}
