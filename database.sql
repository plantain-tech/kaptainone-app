-- Kaptain One Database Schema
-- Run this on your Hostinger MySQL database

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Admin users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog categories
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog posts
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT,
    featured_image VARCHAR(255),
    category_id INT UNSIGNED,
    author_id INT UNSIGNED,
    meta_title VARCHAR(200),
    meta_description VARCHAR(300),
    status ENUM('draft', 'published') DEFAULT 'draft',
    published_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Partner inquiries
CREATE TABLE IF NOT EXISTS partner_inquiries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    company_name VARCHAR(100),
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(50),
    business_type VARCHAR(50),
    city VARCHAR(100),
    country VARCHAR(100),
    website VARCHAR(255),
    message TEXT,
    ip_address VARCHAR(45),
    status VARCHAR(20) DEFAULT 'new',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo requests
CREATE TABLE IF NOT EXISTS demo_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(50),
    company_size VARCHAR(20),
    service_interest VARCHAR(50),
    contact_method VARCHAR(20) DEFAULT 'email',
    message TEXT,
    ip_address VARCHAR(45),
    status VARCHAR(20) DEFAULT 'new',
    scheduled_date DATETIME,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(100),
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    status VARCHAR(20) DEFAULT 'new',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site settings
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gig worker users
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255),
    role ENUM('gig_worker', 'admin') DEFAULT 'gig_worker',
    auth_provider VARCHAR(30) DEFAULT 'email',
    is_active BOOLEAN DEFAULT TRUE,
    email_verified_at DATETIME,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role),
    INDEX idx_users_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    role ENUM('gig_worker','asset_owner','admin') NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_role (user_id, role),
    INDEX idx_user_roles_user_id (user_id),
    INDEX idx_user_roles_role (role),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    full_name VARCHAR(120) NOT NULL,
    phone VARCHAR(50),
    city VARCHAR(100) DEFAULT 'Warsaw',
    country VARCHAR(100) DEFAULT 'Poland',
    preferred_language VARCHAR(40) DEFAULT 'English',
    preferred_pickup_district VARCHAR(40) NULL,
    default_pickup_notes TEXT NULL,
    work_type VARCHAR(60),
    preferred_platforms TEXT,
    driver_license_status VARCHAR(60),
    national_id VARCHAR(80),
    address VARCHAR(255),
    emergency_contact VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_profiles_city (city)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS oauth_accounts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    provider VARCHAR(30) NOT NULL,
    provider_user_id VARCHAR(255) NOT NULL,
    provider_email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_provider_user (provider, provider_user_id),
    INDEX idx_oauth_email (provider_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS equipment_packages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    category VARCHAR(80) NOT NULL,
    description TEXT,
    short_description TEXT,
    image VARCHAR(255),
    included_items TEXT,
    weekly_price DECIMAL(10,2) DEFAULT 0,
    monthly_price DECIMAL(10,2) DEFAULT 0,
    deposit_amount DECIMAL(10,2) DEFAULT 0,
    currency VARCHAR(10) DEFAULT 'PLN',
    availability_status ENUM('available', 'limited', 'waitlist', 'unavailable') DEFAULT 'available',
    requirements TEXT,
    terms TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_packages_active (is_active),
    INDEX idx_packages_category (category),
    INDEX idx_packages_availability (availability_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    FOREIGN KEY (owner_user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listing_photos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_listing_photos_order (listing_id, sort_order),
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS listing_availability (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    available_from DATE NOT NULL,
    available_to DATE NULL,
    status ENUM('available','booked','blocked') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_listing_availability_lookup (listing_id, status, available_from),
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    payment_status ENUM('unpaid','deposit_held','fully_paid','refunded','disputed') NULL DEFAULT 'unpaid',
    total_paid_pln DECIMAL(10,2) NULL DEFAULT 0.00,
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
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE RESTRICT,
    FOREIGN KEY (courier_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE RESTRICT
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
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL
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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (related_booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    FOREIGN KEY (related_listing_id) REFERENCES listings(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT,
    FOREIGN KEY (related_transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    FOREIGN KEY (initiated_by_user_id) REFERENCES users(id) ON DELETE SET NULL
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
    FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS owner_payout_preferences (
    user_id INT UNSIGNED PRIMARY KEY,
    default_payout_delay_days ENUM('1','3','7','14','30') NOT NULL DEFAULT '7',
    bank_account_iban VARCHAR(34) NULL,
    bank_account_holder_name VARCHAR(100) NULL,
    payout_method ENUM('bank_transfer','digital_wallet','manual') NOT NULL DEFAULT 'bank_transfer',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS platform_fee_tiers (
    payout_delay_days INT UNSIGNED PRIMARY KEY,
    fee_percentage DECIMAL(5,2) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by_user_id INT UNSIGNED NULL,
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

CREATE TABLE IF NOT EXISTS equipment_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    package_id INT UNSIGNED,
    name VARCHAR(160) NOT NULL,
    type VARCHAR(80),
    serial_number VARCHAR(120),
    status ENUM('available', 'assigned', 'maintenance', 'retired') DEFAULT 'available',
    assigned_user_id INT UNSIGNED,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES equipment_packages(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_items_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leasing_applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    package_id INT UNSIGNED NOT NULL,
    status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'cancelled') DEFAULT 'submitted',
    applicant_message TEXT,
    admin_notes TEXT,
    submitted_at DATETIME,
    reviewed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES equipment_packages(id) ON DELETE CASCADE,
    INDEX idx_applications_user (user_id),
    INDEX idx_applications_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leased_equipment (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    package_id INT UNSIGNED,
    equipment_item_id INT UNSIGNED,
    application_id INT UNSIGNED,
    start_date DATE,
    expected_return_date DATE,
    payment_cycle ENUM('weekly', 'monthly') DEFAULT 'weekly',
    status ENUM('active', 'pending_pickup', 'returned', 'overdue', 'maintenance') DEFAULT 'pending_pickup',
    support_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES equipment_packages(id) ON DELETE SET NULL,
    FOREIGN KEY (equipment_item_id) REFERENCES equipment_items(id) ON DELETE SET NULL,
    FOREIGN KEY (application_id) REFERENCES leasing_applications(id) ON DELETE SET NULL,
    INDEX idx_leased_user (user_id),
    INDEX idx_leased_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS support_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED,
    subject VARCHAR(160),
    message TEXT NOT NULL,
    status ENUM('new', 'open', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_support_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (password: admin123)
INSERT IGNORE INTO admin_users (username, email, password_hash, full_name) VALUES 
('admin', 'admin@kaptainone.com', '$2y$10$KRYC4Odx1XUQ307wO3ee2.m0kz0lmruX.1RDCYprrER0nSCBcRR7i', 'Administrator');

-- Mirror the default admin into app users for the multi-role authorization model.
INSERT IGNORE INTO users (email, password_hash, role, auth_provider, is_active)
SELECT email, password_hash, 'admin', 'email', is_active
FROM admin_users
WHERE email = 'admin@kaptainone.com';

INSERT IGNORE INTO user_roles (user_id, role)
SELECT id, role
FROM users
WHERE role IN ('gig_worker', 'admin');

INSERT IGNORE INTO users (email, password_hash, role, auth_provider, is_active)
VALUES ('system-seed@kaptainone.local', NULL, 'gig_worker', 'system', 1);

INSERT IGNORE INTO user_roles (user_id, role)
SELECT id, 'asset_owner'
FROM users
WHERE email = 'system-seed@kaptainone.local';

-- Insert default categories
INSERT IGNORE INTO blog_categories (name, slug, description) VALUES
('Courier Equipment Insights', 'courier-equipment-insights', 'News and practical updates for Warsaw courier equipment rentals'),
('Service Updates', 'service-updates', 'New features and service announcements'),
('Courier Tips', 'courier-tips', 'Practical advice for Warsaw delivery couriers');

-- Insert default settings
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'Kaptain One'),
('site_tagline', 'Warsaw E-Bike & Scooter Rentals for Couriers'),
('contact_email', 'kaptainonewayne@gmail.com'),
('contact_phone', '+48 723-385-026'),
('contact_address', 'Warsaw, Poland');

-- Seed gig worker equipment packages
INSERT IGNORE INTO equipment_packages
(title, slug, category, short_description, description, included_items, weekly_price, monthly_price, deposit_amount, currency, availability_status, requirements, terms, sort_order, is_active)
VALUES
('E-bike Starter Package', 'e-bike-starter-package', 'E-bike Leasing', 'A practical e-bike setup for Warsaw couriers starting delivery work fast.', 'A complete starter package for delivery workers who need reliable electric mobility for Uber Eats, Bolt Food, Glovo, Wolt, Stuart, and Pyszne.pl work.', 'E-bike, charger, phone holder, lock, basic maintenance check', 189.00, 690.00, 800.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed leasing agreement, refundable deposit.', 'Weekly or monthly leasing subject to approval and equipment availability.', 10, 1),
('Delivery Gear Starter Package', 'delivery-gear-starter-package', 'Delivery Gear', 'Core delivery accessories for food and parcel couriers.', 'A focused gear kit for couriers who already have transport but need professional delivery equipment.', 'Insulated delivery backpack, phone holder, gloves, reflective safety elements', 49.00, 169.00, 200.00, 'PLN', 'available', 'Valid ID and active or planned courier platform account.', 'Gear must be returned in working condition or replacement cost may apply.', 20, 1),
('Winter Courier Package', 'winter-courier-package', 'Seasonal Protection', 'Cold-weather protection for riders working through Warsaw winter.', 'Designed for couriers who need safer, warmer, more comfortable winter delivery shifts.', 'Wind jacket, winter gloves, neck warmer, wind protective goggles, thermal rain cover', 59.00, 199.00, 250.00, 'PLN', 'limited', 'Valid ID and courier work plan.', 'Seasonal availability may be limited during peak winter demand.', 30, 1),
('Rain Protection Package', 'rain-protection-package', 'Seasonal Protection', 'Water-resistant gear for rainy courier shifts.', 'A compact kit to keep couriers prepared for wet weather and changing conditions.', 'Rain jacket, rain trousers, waterproof phone pouch, backpack rain cover', 39.00, 139.00, 150.00, 'PLN', 'available', 'Valid ID and signed equipment terms.', 'Equipment is leased for courier work and must be returned clean and complete.', 40, 1),
('Professional Courier Package', 'professional-courier-package', 'Courier Bundle', 'A fuller work-ready bundle for serious delivery workers.', 'A polished courier setup with transport accessories, safety gear, and weather protection for regular gig work.', 'Delivery backpack, helmet, phone holder, gloves, goggles, rain gear, reflective safety accessories', 89.00, 319.00, 450.00, 'PLN', 'available', 'Valid ID, platform work plan, deposit, signed leasing agreement.', 'Package contents may vary based on size and availability.', 50, 1),
-- Archived on 2026-05-11: car/chauffeur package is out of MVP scope. Kept inactive for historical reference.
('Vehicle / Car Leasing Package', 'vehicle-car-leasing-package', 'Vehicle Leasing', 'Vehicle leasing pathway for rideshare and private driver candidates.', 'A future-ready vehicle leasing package for Bolt, Uber, private chauffeur, and tourist support work.', 'Vehicle leasing consultation, document checklist, provider matching, onboarding support', 0.00, 0.00, 0.00, 'PLN', 'unavailable', 'Driver license, work eligibility, provider approval, deposit subject to vehicle partner terms.', 'Pricing and deposit are confirmed after provider review.', 60, 0),
('Warsaw E-bike - Śródmieście', 'warsaw-e-bike-srodmiescie', 'E-bike Rental', 'E-bike rental in Śródmieście for Warsaw courier shifts.', 'A practical e-bike rental option for couriers working central Warsaw delivery zones.', 'E-bike, charger, lock, basic handoff check', 280.00, 1040.00, 400.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrate to listings table after Day 3 asset-owner listing work.', 70, 1),
('Warsaw E-bike - Mokotów', 'warsaw-e-bike-mokotow', 'E-bike Rental', 'E-bike rental in Mokotów for Warsaw courier shifts.', 'A work-ready e-bike rental option for couriers serving Mokotów and nearby delivery areas.', 'E-bike, charger, lock, basic handoff check', 320.00, 1190.00, 500.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrate to listings table after Day 3 asset-owner listing work.', 80, 1),
('Warsaw Scooter - Wola', 'warsaw-scooter-wola', 'Scooter Rental', 'Scooter rental in Wola for Warsaw courier shifts.', 'A scooter rental option for couriers who need efficient movement across Wola and central Warsaw.', 'Scooter, charger if electric, lock, basic handoff check', 380.00, 1410.00, 600.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrate to listings table after Day 3 asset-owner listing work.', 90, 1),
('Warsaw Scooter - Praga-Południe', 'warsaw-scooter-praga-poludnie', 'Scooter Rental', 'Scooter rental in Praga-Południe for Warsaw courier shifts.', 'A scooter rental option for couriers serving Praga-Południe and nearby delivery areas.', 'Scooter, charger if electric, lock, basic handoff check', 350.00, 1300.00, 600.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed rental terms.', 'Sample package seed for MVP display; migrate to listings table after Day 3 asset-owner listing work.', 100, 1);

SET @system_owner_id := (
    SELECT id
    FROM users
    WHERE email = 'system-seed@kaptainone.local'
    LIMIT 1
);

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

SET FOREIGN_KEY_CHECKS = 1;
