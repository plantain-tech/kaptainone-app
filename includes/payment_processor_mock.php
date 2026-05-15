<?php
/**
 * Mock payment processor functions for Day 6.
 *
 * TODO Week 2: Replace with real payment processor integration.
 *   - Research chosen processor: Przelewy24 vs PayU vs Stripe
 *   - Implement order creation, authorization, capture flows
 *   - Handle webhooks for payment confirmation
 *   - Store processor_transaction_id and full responses
 *   - Add error handling and retry logic
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/payment_helpers.php';

function payments_mock_reference(string $prefix, int $bookingId): string {
    return strtoupper($prefix) . '-' . $bookingId . '-' . bin2hex(random_bytes(4));
}

function payments_insert_transaction_mock(
    int $bookingId,
    string $type,
    float $amountPln,
    ?int $initiatedByUserId,
    string $notes,
    ?int $relatedTransactionId = null
): int {
    // TODO Week 2: Replace this mock transaction insert with real processor event recording.
    return Database::insert('transactions', [
        'booking_id' => $bookingId,
        'transaction_type' => $type,
        'amount_pln' => round($amountPln, 2),
        'currency' => 'PLN',
        'processor' => 'mock',
        'processor_transaction_id' => payments_mock_reference($type, $bookingId),
        'processor_response_json' => json_encode(['mock' => true, 'type' => $type], JSON_UNESCAPED_SLASHES),
        'status' => 'completed',
        'related_transaction_id' => $relatedTransactionId,
        'initiated_by_user_id' => $initiatedByUserId,
        'notes' => $notes,
        'completed_at' => date('Y-m-d H:i:s'),
    ]);
}

function payments_charge_rental_mock(int $bookingId, int $courierUserId, float $amountPln): array {
    // TODO Week 2: Replace with real rental payment capture.
    $id = payments_insert_transaction_mock($bookingId, 'rental_charge', $amountPln, $courierUserId, 'Mock rental charge captured from courier.');
    return ['success' => true, 'transaction_id' => $id];
}

function payments_hold_deposit_mock(int $bookingId, int $courierUserId, float $depositPln): array {
    // TODO Week 2: Replace with real deposit authorization/hold.
    $id = payments_insert_transaction_mock($bookingId, 'deposit_hold', $depositPln, $courierUserId, 'Mock refundable deposit hold authorized.');
    return ['success' => true, 'transaction_id' => $id];
}

function payments_release_deposit_mock(int $bookingId): array {
    // TODO Week 2: Replace with real deposit release.
    $id = payments_insert_transaction_mock($bookingId, 'deposit_release', 0, null, 'Mock deposit release.');
    return ['success' => true, 'transaction_id' => $id];
}

function payments_capture_deposit_mock(int $bookingId, int $ownerUserId): array {
    // TODO Week 2: Replace with real damage/deposit capture.
    $id = payments_insert_transaction_mock($bookingId, 'deposit_capture', 0, $ownerUserId, 'Mock deposit capture for damage workflow.');
    return ['success' => true, 'transaction_id' => $id];
}

function payments_schedule_payout_mock(
    int $bookingId,
    int $ownerUserId,
    float $rentalAmountPln,
    float $platformFeePln,
    int $payoutDelayDays
): array {
    // TODO Week 2: Replace with real payout creation and processor payout references.
    $ownerPayout = calculate_owner_payout($rentalAmountPln, $platformFeePln);
    $scheduledFor = get_payout_schedule_date(date('Y-m-d'), $payoutDelayDays);
    $payoutId = Database::insert('payouts', [
        'owner_user_id' => $ownerUserId,
        'booking_id' => $bookingId,
        'amount_pln' => $ownerPayout,
        'payout_delay_days' => $payoutDelayDays,
        'scheduled_for' => $scheduledFor,
        'status' => 'scheduled',
        'processor' => 'mock',
        'processor_payout_id' => payments_mock_reference('payout', $bookingId),
    ]);

    return ['success' => true, 'payout_id' => $payoutId, 'scheduled_for' => $scheduledFor, 'amount_pln' => $ownerPayout];
}

function payments_release_payout_mock(int $payoutId): array {
    // TODO Week 2: Replace with real payout release and bank transfer status handling.
    Database::update('payouts', [
        'status' => 'completed',
        'released_at' => date('Y-m-d H:i:s'),
    ], 'id = :id', ['id' => $payoutId]);
    return ['success' => true, 'payout_id' => $payoutId];
}
