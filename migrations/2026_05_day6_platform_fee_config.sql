-- Day 6 platform fee configuration for Kaptain One.
-- Adds admin-configurable platform fee tiers for owner payout timing.
-- Idempotent and non-destructive.

CREATE TABLE IF NOT EXISTS platform_fee_tiers (
    payout_delay_days INT UNSIGNED PRIMARY KEY,
    fee_percentage DECIMAL(5,2) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by_user_id INT UNSIGNED NULL,
    CONSTRAINT fk_platform_fee_tiers_updated_by
        FOREIGN KEY (updated_by_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO platform_fee_tiers (payout_delay_days, fee_percentage) VALUES
    (1, 15.00),
    (3, 10.00),
    (7, 7.00),
    (14, 5.00),
    (30, 3.00)
ON DUPLICATE KEY UPDATE
    fee_percentage = fee_percentage;
