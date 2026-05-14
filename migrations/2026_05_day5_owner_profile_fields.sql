-- Day 5 owner profile fields for Kaptain One.
-- Adds owner pickup preferences to the existing user_profiles table.
-- Idempotent and non-destructive: no rows are deleted or rewritten.

SET @add_preferred_pickup_district := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE user_profiles ADD COLUMN preferred_pickup_district VARCHAR(40) NULL AFTER preferred_language',
        'SELECT 1'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'user_profiles'
      AND COLUMN_NAME = 'preferred_pickup_district'
);
PREPARE stmt FROM @add_preferred_pickup_district;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_default_pickup_notes := (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE user_profiles ADD COLUMN default_pickup_notes TEXT NULL AFTER preferred_pickup_district',
        'SELECT 1'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'user_profiles'
      AND COLUMN_NAME = 'default_pickup_notes'
);
PREPARE stmt FROM @add_default_pickup_notes;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
