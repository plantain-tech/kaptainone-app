<?php
/**
 * Booking state machine for Kaptain One marketplace rentals.
 *
 * IMPORTANT: Do not update bookings.status directly from feature code.
 * All status changes must go through booking_transition() so the audit log stays complete.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

function booking_terminal_statuses(): array {
    return ['rejected', 'cancelled_by_courier', 'cancelled_by_owner', 'completed', 'expired', 'disputed'];
}

function booking_event_type_for_status(string $status, string $actorRole): string {
    $map = [
        'requested' => 'requested',
        'approved' => 'approved',
        'rejected' => 'rejected',
        'cancelled_by_courier' => 'cancelled_by_courier',
        'cancelled_by_owner' => 'cancelled_by_owner',
        'active' => 'marked_active',
        'completed' => 'marked_completed',
        'disputed' => 'disputed',
        'expired' => 'auto_expired',
    ];
    return $map[$status] ?? 'note_added';
}

function booking_actor_role(array $booking, ?int $actorUserId, ?string $override = null): string {
    if ($override && in_array($override, ['courier', 'owner', 'admin', 'system'], true)) {
        return $override;
    }
    if ($actorUserId === null) {
        return 'system';
    }
    if ((int) $booking['owner_user_id'] === $actorUserId) {
        return 'owner';
    }
    if ((int) $booking['courier_user_id'] === $actorUserId) {
        return 'courier';
    }
    $roles = get_user_roles($actorUserId);
    return in_array('admin', $roles, true) ? 'admin' : 'unknown';
}

function booking_can_transition(string $from, string $to, string $actorRole): bool {
    if ($from === $to || in_array($from, booking_terminal_statuses(), true)) {
        return false;
    }

    if ($actorRole === 'admin' && !in_array($from, booking_terminal_statuses(), true) && $to === 'disputed') {
        return true;
    }

    $allowed = [
        'requested' => [
            'owner' => ['approved', 'rejected'],
            'courier' => ['cancelled_by_courier'],
            'admin' => ['approved', 'rejected', 'expired', 'disputed'],
            'system' => ['expired'],
        ],
        'approved' => [
            'courier' => ['cancelled_by_courier'],
            'owner' => ['cancelled_by_owner'],
            'admin' => ['cancelled_by_owner', 'disputed'],
            'system' => ['active'],
        ],
        'active' => [
            'owner' => ['disputed'],
            'courier' => ['disputed'],
            'admin' => ['completed', 'disputed'],
            'system' => ['completed'],
        ],
    ];

    return in_array($to, $allowed[$from][$actorRole] ?? [], true);
}

function booking_fetch(int $bookingId): ?array {
    return Database::fetch(
        "SELECT b.*, l.title AS listing_title, l.asset_type, l.location_district, l.owner_user_id AS listing_owner_id,
                (SELECT file_path FROM listing_photos lp WHERE lp.listing_id = l.id ORDER BY lp.sort_order, lp.id LIMIT 1) AS photo_path
         FROM bookings b
         JOIN listings l ON l.id = b.listing_id
         WHERE b.id = ?",
        [$bookingId]
    );
}

function booking_transition(int $bookingId, string $newStatus, ?int $actorUserId, array $payload = []): array {
    try {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? FOR UPDATE");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$booking) {
            $pdo->rollBack();
            return ['success' => false, 'error' => 'Booking not found.'];
        }

        $actorRole = booking_actor_role($booking, $actorUserId, $payload['actor_role'] ?? null);
        $expectedStatus = trim((string) ($payload['expected_status'] ?? ''));
        if ($expectedStatus !== '' && $booking['status'] !== $expectedStatus) {
            $pdo->rollBack();
            return ['success' => false, 'error' => 'This booking has already been updated. Please refresh and try again.'];
        }

        if (!booking_can_transition($booking['status'], $newStatus, $actorRole)) {
            $pdo->rollBack();
            return ['success' => false, 'error' => 'This booking cannot be moved to that status.'];
        }

        $fields = [
            'status' => $newStatus,
            'status_changed_at' => date('Y-m-d H:i:s'),
        ];
        if (in_array($newStatus, ['approved', 'rejected'], true)) {
            $fields['responded_at'] = date('Y-m-d H:i:s');
            $fields['owner_response_message'] = trim((string) ($payload['owner_response_message'] ?? '')) ?: null;
        }
        if ($newStatus === 'cancelled_by_owner') {
            $fields['owner_response_message'] = trim((string) ($payload['owner_response_message'] ?? '')) ?: null;
        }

        Database::update('bookings', $fields, 'id = :id', ['id' => $bookingId]);
        booking_insert_event($bookingId, booking_event_type_for_status($newStatus, $actorRole), $actorUserId, $booking['status'], $newStatus, $payload);

        $pdo->commit();
        return ['success' => true, 'booking_id' => $bookingId, 'status' => $newStatus];
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Booking transition failed: ' . $e->getMessage());
        return ['success' => false, 'error' => 'Unable to update booking right now.'];
    }
}

function booking_create_request(array $listing, int $courierUserId, array $data): array {
    try {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        $bookingId = Database::insert('bookings', [
            'listing_id' => (int) $listing['id'],
            'courier_user_id' => $courierUserId,
            'owner_user_id' => (int) $listing['owner_user_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'rental_days' => (int) $data['rental_days'],
            'weekly_price_snapshot_pln' => (float) $listing['weekly_price_pln'],
            'deposit_snapshot_pln' => (float) $listing['deposit_pln'],
            'total_amount_pln' => (float) $data['total_amount_pln'],
            'courier_message' => trim((string) ($data['courier_message'] ?? '')) ?: null,
            'status' => 'requested',
            'status_changed_at' => date('Y-m-d H:i:s'),
        ]);

        booking_insert_event($bookingId, 'requested', $courierUserId, null, 'requested', [
            'courier_message' => trim((string) ($data['courier_message'] ?? '')),
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'total_amount_pln' => $data['total_amount_pln'],
        ]);

        $pdo->commit();
        return ['success' => true, 'booking_id' => $bookingId];
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Create booking request failed: ' . $e->getMessage());
        return ['success' => false, 'error' => 'Unable to send this booking request right now.'];
    }
}

function booking_insert_event(int $bookingId, string $eventType, ?int $actorUserId, ?string $previousStatus, ?string $newStatus, array $payload = []): void {
    $payloadJson = $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
    Database::insert('booking_events', [
        'booking_id' => $bookingId,
        'event_type' => $eventType,
        'actor_user_id' => $actorUserId,
        'previous_status' => $previousStatus,
        'new_status' => $newStatus,
        'payload_json' => $payloadJson,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255) ?: null,
    ]);
}

function booking_get_allowed_actions(array $booking, array $currentUser): array {
    $actions = [];
    $userId = (int) ($currentUser['id'] ?? 0);
    $actorRole = booking_actor_role($booking, $userId, has_role('admin') ? 'admin' : null);
    foreach (['approved', 'rejected', 'cancelled_by_courier', 'cancelled_by_owner', 'disputed'] as $target) {
        if (booking_can_transition($booking['status'], $target, $actorRole)) {
            $actions[] = $target;
        }
    }
    return $actions;
}

function booking_price_breakdown(float $weeklyPrice, float $deposit, string $startDate, string $endDate): ?array {
    try {
        $start = new DateTimeImmutable($startDate);
        $end = new DateTimeImmutable($endDate);
    } catch (Throwable $e) {
        return null;
    }
    $days = (int) $start->diff($end)->days;
    if ($days < 1 || $days > 30) {
        return null;
    }
    $rental = round(($days / 7) * $weeklyPrice, 2);
    return [
        'days' => $days,
        'rental_pln' => $rental,
        'deposit_pln' => round($deposit, 2),
        'total_pln' => round($rental + $deposit, 2),
    ];
}

function booking_status_label(string $status): string {
    return ucwords(str_replace('_', ' ', $status));
}

function booking_relative_time(string $date): string {
    $timestamp = strtotime($date);
    $diff = time() - $timestamp;
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    return floor($diff / 86400) . ' days ago';
}
