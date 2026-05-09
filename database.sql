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

SET FOREIGN_KEY_CHECKS = 1;
