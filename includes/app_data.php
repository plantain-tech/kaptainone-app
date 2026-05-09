<?php
/**
 * Shared app data helpers for packages, applications, and dashboard summaries.
 */

require_once __DIR__ . '/db.php';

function package_status_label(string $status): string {
    return ucwords(str_replace('_', ' ', $status));
}

function application_status_label(string $status): string {
    return ucwords(str_replace('_', ' ', $status));
}

function get_active_packages(): array {
    return Database::fetchAll("SELECT * FROM equipment_packages WHERE is_active = 1 ORDER BY sort_order, title");
}

function get_package_by_slug(string $slug): ?array {
    return Database::fetch("SELECT * FROM equipment_packages WHERE slug = ? AND is_active = 1", [$slug]);
}

function get_package_by_id(int $id): ?array {
    return Database::fetch("SELECT * FROM equipment_packages WHERE id = ?", [$id]);
}
