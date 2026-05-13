-- Day 5 booking request flow for Kaptain One.
-- Adds booking requests, append-only booking audit events, and in-app notifications.
-- Idempotent and non-destructive: creates tables only when missing; no rows are deleted.

CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    courier_user_id INT UNSIGNED NOT NULL,
    owner_user_id INT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    rental_days INT UNSIGNED NOT NULL,
    weekly_price_snapshot_pln DECIMAL(8,2) NOT NULL,
    deposit_snapshot_pln DECIMAL(8,2) NOT NULL,
    total_amount_pln DECIMAL(10,2) NOT NULL,
    platform_fee_pln DECIMAL(8,2) NOT NULL DEFAULT 0,
    owner_payout_pln DECIMAL(8,2) NOT NULL DEFAULT 0,
    courier_message TEXT NULL,
    owner_response_message TEXT NULL,
    status ENUM('requested','approved','rejected','cancelled_by_courier','cancelled_by_owner','active','completed','disputed','expired') NOT NULL DEFAULT 'requested',
    status_changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_bookings_listing_status (listing_id, status),
    INDEX idx_bookings_courier_status (courier_user_id, status),
    INDEX idx_bookings_owner_status (owner_user_id, status),
    INDEX idx_bookings_status_requested (status, requested_at),
    CONSTRAINT fk_bookings_listing FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_courier FOREIGN KEY (courier_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_owner FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS booking_events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    event_type ENUM('requested','approved','rejected','cancelled_by_courier','cancelled_by_owner','auto_expired','marked_active','marked_completed','disputed','note_added') NOT NULL,
    actor_user_id INT UNSIGNED NULL,
    previous_status VARCHAR(40) NULL,
    new_status VARCHAR(40) NULL,
    payload_json JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_booking_events_booking_created (booking_id, created_at),
    INDEX idx_booking_events_actor_created (actor_user_id, created_at),
    CONSTRAINT fk_booking_events_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_events_actor FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    type ENUM('booking_requested','booking_approved','booking_rejected','booking_cancelled','booking_reminder','system') NOT NULL,
    related_booking_id INT UNSIGNED NULL,
    related_listing_id INT UNSIGNED NULL,
    title VARCHAR(120) NOT NULL,
    body VARCHAR(400) NOT NULL,
    link_url VARCHAR(255) NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notifications_user_read_created (user_id, read_at, created_at),
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_notifications_booking FOREIGN KEY (related_booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    CONSTRAINT fk_notifications_listing FOREIGN KEY (related_listing_id) REFERENCES listings(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
