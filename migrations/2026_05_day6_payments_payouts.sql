-- Day 6 payments and payouts foundation for Kaptain One.
-- Adds a mock financial ledger, scheduled owner payouts, and owner payout preferences.
-- Idempotent and non-destructive: creates missing tables and adds missing booking columns only.

CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    transaction_type ENUM('rental_charge','deposit_hold','deposit_release','deposit_capture','payout_to_owner','platform_fee','refund_to_courier','chargeback','adjustment') NOT NULL,
    amount_pln DECIMAL(10,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'PLN',
    processor ENUM('payu','przelewy24','stripe','manual','mock') NOT NULL,
    processor_transaction_id VARCHAR(100) NULL,
    processor_response_json JSON NULL,
    status ENUM('pending','completed','failed','reversed') NOT NULL DEFAULT 'pending',
    related_transaction_id INT UNSIGNED NULL,
    initiated_by_user_id INT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    INDEX idx_transactions_booking_type (booking_id, transaction_type),
    INDEX idx_transactions_processor_id (processor_transaction_id),
    INDEX idx_transactions_status_created (status, created_at),
    INDEX idx_transactions_related (related_transaction_id),
    CONSTRAINT fk_transactions_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT,
    CONSTRAINT fk_transactions_related FOREIGN KEY (related_transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    CONSTRAINT fk_transactions_initiated_by FOREIGN KEY (initiated_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payouts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_user_id INT UNSIGNED NOT NULL,
    booking_id INT UNSIGNED NOT NULL,
    amount_pln DECIMAL(10,2) NOT NULL,
    payout_delay_days INT UNSIGNED NOT NULL,
    scheduled_for DATE NOT NULL,
    released_at TIMESTAMP NULL,
    status ENUM('scheduled','processing','completed','failed','held') NOT NULL DEFAULT 'scheduled',
    processor ENUM('payu','przelewy24','stripe','bank_transfer','manual','mock') NOT NULL,
    processor_payout_id VARCHAR(100) NULL,
    failure_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_payouts_owner_status_schedule (owner_user_id, status, scheduled_for),
    INDEX idx_payouts_booking (booking_id),
    INDEX idx_payouts_status_schedule (status, scheduled_for),
    CONSTRAINT fk_payouts_owner FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_payouts_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS owner_payout_preferences (
    user_id INT UNSIGNED PRIMARY KEY,
    default_payout_delay_days ENUM('1','3','7','14','30') NOT NULL DEFAULT '7',
    bank_account_iban VARCHAR(34) NULL,
    bank_account_holder_name VARCHAR(100) NULL,
    payout_method ENUM('bank_transfer','digital_wallet','manual') NOT NULL DEFAULT 'bank_transfer',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_owner_payout_preferences_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @add_payment_status := (
    SELECT IF(
        COUNT(*) = 0,
        "ALTER TABLE bookings ADD COLUMN payment_status ENUM('unpaid','deposit_held','fully_paid','refunded','disputed') NULL DEFAULT 'unpaid' AFTER owner_payout_pln",
        'SELECT 1'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'bookings'
      AND COLUMN_NAME = 'payment_status'
);
PREPARE stmt FROM @add_payment_status;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_total_paid := (
    SELECT IF(
        COUNT(*) = 0,
        "ALTER TABLE bookings ADD COLUMN total_paid_pln DECIMAL(10,2) NULL DEFAULT 0.00 AFTER payment_status",
        'SELECT 1'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'bookings'
      AND COLUMN_NAME = 'total_paid_pln'
);
PREPARE stmt FROM @add_total_paid;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
