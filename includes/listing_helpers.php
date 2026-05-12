<?php
/**
 * Listing helpers for the Warsaw e-bike/scooter marketplace.
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function warsaw_districts(): array {
    return [
        'Bemowo', 'Białołęka', 'Bielany', 'Mokotów', 'Ochota', 'Praga-Południe',
        'Praga-Północ', 'Rembertów', 'Śródmieście', 'Targówek', 'Ursus', 'Ursynów',
        'Wawer', 'Wesoła', 'Wilanów', 'Włochy', 'Wola', 'Żoliborz'
    ];
}

function listing_asset_label(string $assetType): string {
    return $assetType === 'scooter' ? 'Scooter' : 'E-bike';
}

function listing_condition_label(string $condition): string {
    $labels = [
        'new' => 'New',
        'excellent' => 'Excellent',
        'good' => 'Good',
        'fair' => 'Fair',
    ];
    return $labels[$condition] ?? 'Good';
}

function listing_status_label(string $status): string {
    return ucfirst(str_replace('_', ' ', $status));
}

function listing_monthly_price(array $listing): float {
    if (!empty($listing['monthly_price_pln'])) {
        return (float) $listing['monthly_price_pln'];
    }
    return round(((float) $listing['weekly_price_pln']) * 3.7, 2);
}

function listing_primary_photo(?array $listing): ?string {
    if (!$listing || empty($listing['primary_photo'])) {
        return null;
    }
    return $listing['primary_photo'];
}

function get_listing(int $listingId): ?array {
    return Database::fetch(
        "SELECT l.*, u.email AS owner_email, p.full_name AS owner_name,
                (SELECT file_path FROM listing_photos lp WHERE lp.listing_id = l.id ORDER BY lp.sort_order, lp.id LIMIT 1) AS primary_photo,
                (SELECT COUNT(*) FROM listing_photos lp WHERE lp.listing_id = l.id) AS photo_count
         FROM listings l
         JOIN users u ON u.id = l.owner_user_id
         LEFT JOIN user_profiles p ON p.user_id = u.id
         WHERE l.id = ?",
        [$listingId]
    );
}

function get_listing_photos(int $listingId): array {
    return Database::fetchAll(
        "SELECT * FROM listing_photos WHERE listing_id = ? ORDER BY sort_order, id",
        [$listingId]
    );
}

function user_can_manage_listing(array $listing, ?array $user): bool {
    if (!$user) {
        return false;
    }
    if ((int) $listing['owner_user_id'] === (int) $user['id']) {
        return true;
    }
    return function_exists('has_role') && has_role('admin');
}

function validate_listing_input(array $data, bool $publishing, bool $requiresPhoto): array {
    $errors = [];
    $districts = warsaw_districts();
    $assetTypes = ['ebike', 'scooter'];
    $conditions = ['new', 'excellent', 'good', 'fair'];

    if (!in_array($data['asset_type'] ?? '', $assetTypes, true)) {
        $errors['asset_type'] = 'Choose e-bike or scooter';
    }
    if (strlen(trim($data['title'] ?? '')) < 4 || strlen(trim($data['title'] ?? '')) > 150) {
        $errors['title'] = 'Enter a clear listing title';
    }
    if (!in_array($data['condition_grade'] ?? '', $conditions, true)) {
        $errors['condition_grade'] = 'Choose a condition';
    }
    $weekly = (float) ($data['weekly_price_pln'] ?? 0);
    if ($weekly < 50 || $weekly > 1500) {
        $errors['weekly_price_pln'] = 'Weekly price must be between 50 and 1500 PLN';
    }
    $deposit = (float) ($data['deposit_pln'] ?? -1);
    if ($deposit < 0 || $deposit > 5000) {
        $errors['deposit_pln'] = 'Deposit must be between 0 and 5000 PLN';
    }
    if (!in_array($data['location_district'] ?? '', $districts, true)) {
        $errors['location_district'] = 'Choose a Warsaw district';
    }
    $description = trim($data['description'] ?? '');
    if (strlen($description) < 20) {
        $errors['description'] = 'Add a short useful description';
    }
    if (strlen($description) > 2000) {
        $errors['description'] = 'Description must be 2000 characters or less';
    }
    if (strlen(trim($data['included_items'] ?? '')) > 500) {
        $errors['included_items'] = 'Included items must be 500 characters or less';
    }
    if ($publishing && $requiresPhoto) {
        $errors['photos'] = 'Add at least one photo before publishing';
    }

    return $errors;
}

function normalize_listing_payload(array $data, int $ownerUserId, string $status): array {
    $weekly = round((float) $data['weekly_price_pln'], 2);
    $monthly = trim((string) ($data['monthly_price_pln'] ?? ''));

    return [
        'owner_user_id' => $ownerUserId,
        'asset_type' => $data['asset_type'],
        'title' => trim($data['title']),
        'brand' => trim($data['brand'] ?? '') ?: null,
        'model' => trim($data['model'] ?? '') ?: null,
        'condition_grade' => $data['condition_grade'],
        'weekly_price_pln' => $weekly,
        'monthly_price_pln' => $monthly === '' ? round($weekly * 3.7, 2) : round((float) $monthly, 2),
        'deposit_pln' => round((float) $data['deposit_pln'], 2),
        'location_district' => $data['location_district'],
        'description' => trim($data['description']),
        'included_items' => trim($data['included_items'] ?? '') ?: null,
        'status' => $status,
    ];
}

function fetch_owner_listings(int $ownerUserId, string $status = 'all', string $sort = 'updated_desc'): array {
    $where = ['owner_user_id = ?'];
    $params = [$ownerUserId];
    if (in_array($status, ['active', 'paused', 'draft', 'archived'], true)) {
        $where[] = 'status = ?';
        $params[] = $status;
    }
    $order = $sort === 'created_desc' ? 'created_at DESC' : 'updated_at DESC';

    return Database::fetchAll(
        "SELECT l.*,
                (SELECT file_path FROM listing_photos lp WHERE lp.listing_id = l.id ORDER BY lp.sort_order, lp.id LIMIT 1) AS primary_photo
         FROM listings l
         WHERE " . implode(' AND ', $where) . "
         ORDER BY {$order}",
        $params
    );
}

function count_uploaded_files(array $files): int {
    if (empty($files['name']) || !is_array($files['name'])) {
        return 0;
    }
    $count = 0;
    foreach ($files['name'] as $idx => $name) {
        if ($name !== '' && ($files['error'][$idx] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $count++;
        }
    }
    return $count;
}
