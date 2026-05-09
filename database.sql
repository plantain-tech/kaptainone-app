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

CREATE TABLE IF NOT EXISTS user_profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    full_name VARCHAR(120) NOT NULL,
    phone VARCHAR(50),
    city VARCHAR(100) DEFAULT 'Warsaw',
    country VARCHAR(100) DEFAULT 'Poland',
    preferred_language VARCHAR(40) DEFAULT 'English',
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

-- Insert default categories
INSERT IGNORE INTO blog_categories (name, slug, description) VALUES
('Industry Insights', 'industry-insights', 'News and trends in luxury transportation'),
('Service Updates', 'service-updates', 'New features and service announcements'),
('Travel Tips', 'travel-tips', 'Expert advice for business and luxury travel');

-- Insert default settings
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'Kaptain One'),
('site_tagline', 'Premium Ground Transportation'),
('contact_email', 'kaptainonewayne@gmail.com'),
('contact_phone', '+48 723-385-026'),
('contact_address', 'Warsaw, Poland');

-- Seed gig worker equipment packages
INSERT IGNORE INTO equipment_packages
(title, slug, category, short_description, description, included_items, weekly_price, monthly_price, deposit_amount, currency, availability_status, requirements, terms, sort_order)
VALUES
('E-bike Starter Package', 'e-bike-starter-package', 'E-bike Leasing', 'A practical e-bike setup for Warsaw couriers starting delivery work fast.', 'A complete starter package for delivery workers who need reliable electric mobility for Uber Eats, Bolt Food, Glovo, Wolt, Stuart, and Pyszne.pl work.', 'E-bike, charger, phone holder, lock, basic maintenance check', 189.00, 690.00, 800.00, 'PLN', 'available', 'Valid ID, Warsaw pickup availability, signed leasing agreement, refundable deposit.', 'Weekly or monthly leasing subject to approval and equipment availability.', 10),
('Delivery Gear Starter Package', 'delivery-gear-starter-package', 'Delivery Gear', 'Core delivery accessories for food and parcel couriers.', 'A focused gear kit for couriers who already have transport but need professional delivery equipment.', 'Insulated delivery backpack, phone holder, gloves, reflective safety elements', 49.00, 169.00, 200.00, 'PLN', 'available', 'Valid ID and active or planned courier platform account.', 'Gear must be returned in working condition or replacement cost may apply.', 20),
('Winter Courier Package', 'winter-courier-package', 'Seasonal Protection', 'Cold-weather protection for riders working through Warsaw winter.', 'Designed for couriers who need safer, warmer, more comfortable winter delivery shifts.', 'Wind jacket, winter gloves, neck warmer, wind protective goggles, thermal rain cover', 59.00, 199.00, 250.00, 'PLN', 'limited', 'Valid ID and courier work plan.', 'Seasonal availability may be limited during peak winter demand.', 30),
('Rain Protection Package', 'rain-protection-package', 'Seasonal Protection', 'Water-resistant gear for rainy courier shifts.', 'A compact kit to keep couriers prepared for wet weather and changing conditions.', 'Rain jacket, rain trousers, waterproof phone pouch, backpack rain cover', 39.00, 139.00, 150.00, 'PLN', 'available', 'Valid ID and signed equipment terms.', 'Equipment is leased for courier work and must be returned clean and complete.', 40),
('Professional Courier Package', 'professional-courier-package', 'Courier Bundle', 'A fuller work-ready bundle for serious delivery workers.', 'A polished courier setup with transport accessories, safety gear, and weather protection for regular gig work.', 'Delivery backpack, helmet, phone holder, gloves, goggles, rain gear, reflective safety accessories', 89.00, 319.00, 450.00, 'PLN', 'available', 'Valid ID, platform work plan, deposit, signed leasing agreement.', 'Package contents may vary based on size and availability.', 50),
('Vehicle / Car Leasing Package', 'vehicle-car-leasing-package', 'Vehicle Leasing', 'Vehicle leasing pathway for rideshare and private driver candidates.', 'A future-ready vehicle leasing package for Bolt, Uber, private chauffeur, and tourist support work.', 'Vehicle leasing consultation, document checklist, provider matching, onboarding support', 0.00, 0.00, 0.00, 'PLN', 'waitlist', 'Driver license, work eligibility, provider approval, deposit subject to vehicle partner terms.', 'Pricing and deposit are confirmed after provider review.', 60);

SET FOREIGN_KEY_CHECKS = 1;
