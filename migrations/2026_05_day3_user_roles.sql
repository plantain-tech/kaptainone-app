-- Day 3 multi-role foundation for Kaptain One.
-- Adds user_roles so one account can be a gig_worker, asset_owner, and/or admin.
-- Non-destructive: keeps the legacy users.role column and existing admin_users table unchanged.

CREATE TABLE IF NOT EXISTS user_roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    role ENUM('gig_worker','asset_owner','admin') NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_role (user_id, role),
    INDEX idx_user_roles_user_id (user_id),
    INDEX idx_user_roles_role (role),
    CONSTRAINT fk_user_roles_user
        FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backfill all current app users from the legacy users.role column.
INSERT IGNORE INTO user_roles (user_id, role)
SELECT id, COALESCE(role, 'gig_worker')
FROM users
WHERE role IN ('gig_worker', 'admin');

-- Mirror legacy admin_users into users by email so the new role system can represent admins too.
-- This does not replace admin_users or change the existing admin login flow.
INSERT IGNORE INTO users (email, password_hash, role, auth_provider, is_active, email_verified_at, last_login)
SELECT
    email,
    password_hash,
    'admin',
    'email',
    is_active,
    NULL,
    last_login
FROM admin_users
WHERE email IS NOT NULL AND email <> '';

-- Grant admin role to the mirrored admin user records.
INSERT IGNORE INTO user_roles (user_id, role)
SELECT u.id, 'admin'
FROM users u
JOIN admin_users a ON a.email = u.email
WHERE a.is_active = 1;
