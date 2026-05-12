-- Day 4 marketplace listings foundation.
-- Adds asset listing tables, migrates Day 2 Warsaw e-bike/scooter package seeds
-- into public listings, and marks the superseded equipment package rows inactive.
-- Safe to run more than once. Non-destructive: no DROP, DELETE, TRUNCATE, or data-removing ALTER.

CREATE TABLE IF NOT EXISTS listings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_user_id INT UNSIGNED NOT NULL,
    asset_type ENUM('ebike','scooter') NOT NULL,
    title VARCHAR(150) NOT NULL,
    brand VARCHAR(80) NULL,
    model VARCHAR(80) NULL,
    condition_grade ENUM('new','excellent','good','fair') NOT NULL DEFAULT 'good',
    weekly_price_pln DECIMAL(8,2) NOT NULL,
    monthly_price_pln DECIMAL(8,2) NULL,
    deposit_pln DECIMAL(8,2) NOT NULL DEFAULT 0,
    location_district VARCHAR(40) NOT NULL,
    location_lat DECIMAL(10,7) NULL,
    location_lng DECIMAL(10,7) NULL,
    description TEXT NULL,
    included_items VARCHAR(500) NULL,
    status ENUM('draft','active','paused','archived') NOT NULL DEFAULT 'draft',
    view_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_listings_browse (status, asset_type, location_district),
    INDEX idx_listings_owner (owner_user_id),
    UNIQUE KEY uniq_owner_listing_identity (owner_user_id, title, asset_type, location_district),
    CONSTRAINT fk_listings_owner FOREIGN KEY (owner_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listing_photos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_listing_photos_order (listing_id, sort_order),
    CONSTRAINT fk_listing_photos_listing FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listing_availability (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    available_from DATE NOT NULL,
    available_to DATE NULL,
    status ENUM('available','booked','blocked') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_listing_availability_lookup (listing_id, status, available_from),
    CONSTRAINT fk_listing_availability_listing FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO users (email, password_hash, role, auth_provider, is_active)
VALUES ('system-seed@kaptainone.local', NULL, 'gig_worker', 'system', 1);

INSERT IGNORE INTO user_roles (user_id, role)
SELECT id, 'asset_owner'
FROM users
WHERE email = 'system-seed@kaptainone.local';

SET @system_owner_id := (
    SELECT id
    FROM users
    WHERE email = 'system-seed@kaptainone.local'
    LIMIT 1
);

-- Ensure the Day 2 Warsaw package seeds exist before migrating them.
-- These package rows become inactive after the listings are created below.
INSERT INTO equipment_packages
(title, slug, category, short_description, description, included_items, weekly_price, monthly_price, deposit_amount, currency, availability_status, requirements, terms, sort_order, is_active)
VALUES
('Warsaw E-bike - Śródmieście', 'warsaw-e-bike-srodmiescie', 'E-bike Rental', 'E-bike rental in Śródmieście for Warsaw courier shifts.', 'A practical e-bike rental option for couriers working central Warsaw delivery zones.', 'E-bike, charger, lock, basic handoff check', 280.00, 1040.00, 400.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrated to listings table during Day 4 asset-owner listing work.', 70, 1),
('Warsaw E-bike - Mokotów', 'warsaw-e-bike-mokotow', 'E-bike Rental', 'E-bike rental in Mokotów for Warsaw courier shifts.', 'A work-ready e-bike rental option for couriers serving Mokotów and nearby delivery areas.', 'E-bike, charger, lock, basic handoff check', 320.00, 1190.00, 500.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrated to listings table during Day 4 asset-owner listing work.', 80, 1),
('Warsaw Scooter - Wola', 'warsaw-scooter-wola', 'Scooter Rental', 'Scooter rental in Wola for Warsaw courier shifts.', 'A scooter rental option for couriers who need efficient movement across Wola and central Warsaw.', 'Scooter, charger if electric, lock, basic handoff check', 380.00, 1410.00, 600.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrated to listings table during Day 4 asset-owner listing work.', 90, 1),
('Warsaw Scooter - Praga-Południe', 'warsaw-scooter-praga-poludnie', 'Scooter Rental', 'Scooter rental in Praga-Południe for Warsaw courier shifts.', 'A scooter rental option for couriers serving Praga-Południe and nearby delivery areas.', 'Scooter, charger if electric, lock, basic handoff check', 350.00, 1300.00, 600.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrated to listings table during Day 4 asset-owner listing work.', 100, 1)
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    category = VALUES(category),
    short_description = VALUES(short_description),
    description = VALUES(description),
    included_items = VALUES(included_items),
    weekly_price = VALUES(weekly_price),
    monthly_price = VALUES(monthly_price),
    deposit_amount = VALUES(deposit_amount),
    requirements = VALUES(requirements),
    sort_order = VALUES(sort_order);

INSERT INTO listings (
    owner_user_id,
    asset_type,
    title,
    condition_grade,
    weekly_price_pln,
    monthly_price_pln,
    deposit_pln,
    location_district,
    description,
    included_items,
    status
)
SELECT
    @system_owner_id,
    CASE
        WHEN p.category = 'Scooter Rental' THEN 'scooter'
        ELSE 'ebike'
    END,
    p.title,
    'good',
    p.weekly_price,
    NULLIF(p.monthly_price, 0),
    p.deposit_amount,
    CASE
        WHEN p.slug = 'warsaw-e-bike-srodmiescie' THEN 'Śródmieście'
        WHEN p.slug = 'warsaw-e-bike-mokotow' THEN 'Mokotów'
        WHEN p.slug = 'warsaw-scooter-wola' THEN 'Wola'
        WHEN p.slug = 'warsaw-scooter-praga-poludnie' THEN 'Praga-Południe'
        ELSE 'Warsaw'
    END,
    p.description,
    LEFT(p.included_items, 500),
    'active'
FROM equipment_packages p
WHERE p.slug IN (
    'warsaw-e-bike-srodmiescie',
    'warsaw-e-bike-mokotow',
    'warsaw-scooter-wola',
    'warsaw-scooter-praga-poludnie'
)
ON DUPLICATE KEY UPDATE
    weekly_price_pln = VALUES(weekly_price_pln),
    monthly_price_pln = VALUES(monthly_price_pln),
    deposit_pln = VALUES(deposit_pln),
    description = VALUES(description),
    included_items = VALUES(included_items),
    status = 'active';

-- Superseded by the listings table on 2026-05-11; kept for historical package data.
UPDATE equipment_packages
SET
    is_active = 0,
    availability_status = 'unavailable',
    terms = CASE
        WHEN terms LIKE '%Superseded by listings table on 2026-05-11%' THEN terms
        ELSE CONCAT(COALESCE(terms, ''), ' Superseded by listings table on 2026-05-11.')
    END
WHERE slug IN (
    'warsaw-e-bike-srodmiescie',
    'warsaw-e-bike-mokotow',
    'warsaw-scooter-wola',
    'warsaw-scooter-praga-poludnie'
);
