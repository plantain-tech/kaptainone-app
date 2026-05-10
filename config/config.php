<?php
/**
 * Kaptain One - Main Configuration
 * Update these values for your Hostinger environment
 */

// Database configuration
$DB_CONFIG = [
    'host'     => 'localhost',
    'database' => 'u123456789_kaptain',  // CHANGE THIS - your Hostinger DB name
    'username' => 'u123456789_admin',   // CHANGE THIS - your Hostinger DB user
    'password' => 'your_db_password',   // CHANGE THIS - your Hostinger DB password
    'charset'  => 'utf8mb4',
    'port'     => 3306
];

// Site configuration
$SITE_CONFIG = [
    'name'        => 'Kaptain One',
    'tagline'     => 'Warsaw E-Bike & Scooter Rentals for Couriers',
    'url'         => 'https://kaptainone.com',  // CHANGE THIS
    'email'       => 'kaptainonewayne@gmail.com',
    'phone'       => '+48 723-385-026',
    'phone_link'  => '+48723385026',
    'address'     => 'Warsaw, Poland',
    'timezone'    => 'Europe/Warsaw'
];

// Social links
$SOCIAL_LINKS = [
    'linkedin'  => '#',
    'instagram' => '#',
    'twitter'   => '#',
    'facebook'  => '#'
];

// Security
$SECURITY_CONFIG = [
    'session_lifetime' => 3600,  // 1 hour
    'csrf_token_name'  => 'kaptain_csrf_token',
    'max_login_attempts' => 5,
    'lockout_duration'   => 900  // 15 minutes
];

// Form spam protection (honeypot field name - should be hidden)
$SPAM_CONFIG = [
    'honeypot_field' => 'website_url',
    'min_time_seconds' => 3  // minimum time to fill form
];
