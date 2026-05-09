# Kaptain One - Premium Transportation Platform

Phase 1 marketing website built with PHP + MySQL for Hostinger shared hosting.

## Project Root

Use this folder as the deployable project root:

```text
C:\Users\Raider GE76\.openclaw\workspace\kaptain-one\
```

This project now uses a **root-based structure**.
Do **not** deploy an old nested `public_html/` copy.

## Final Deploy Structure

Upload the contents of this project so your Hostinger server `public_html/` looks like this:

```text
public_html/
├── .htaccess
├── index.php
├── about.php
├── contact.php
├── solutions.php
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── dashboard.php
│   ├── blog/
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── delete.php
│   └── submissions/
│       ├── partners.php
│       └── demos.php
├── assets/
│   ├── css/
│   │   ├── main.css
│   │   ├── responsive.css
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── blog/
│   ├── index.php
│   └── post.php
├── config/
│   ├── config.php
│   └── database.php
├── includes/
│   ├── contact_handler.php
│   ├── data.php
│   ├── db.php
│   ├── footer.php
│   ├── form_handler.php
│   ├── functions.php
│   ├── header.php
│   └── layout.php
├── partners/
│   ├── become-a-partner.php
│   └── request-a-demo.php
├── services/
│   ├── index.php
│   ├── airport-transfers.php
│   ├── executive-transportation.php
│   ├── corporate-travel.php
│   ├── hourly-chauffeur.php
│   ├── event-transportation.php
│   └── vip-concierge.php
└── database.sql
```

## What To Upload

Upload these from the local project root:

```text
.htaccess
index.php
about.php
contact.php
solutions.php
admin/
assets/
blog/
config/
includes/
partners/
services/
```

## Database

Do not rely on `database.sql` as a runtime file.
Import it into MySQL using phpMyAdmin.

### Steps
1. Open Hostinger hPanel
2. Create a MySQL database
3. Open phpMyAdmin
4. Select the new database
5. Import `database.sql`

## Config

Edit:

```text
config/config.php
```

Update these values:

```php
$DB_CONFIG = [
    'host'     => 'localhost',
    'database' => 'your_hostinger_database_name',
    'username' => 'your_hostinger_database_user',
    'password' => 'your_hostinger_database_password',
    'charset'  => 'utf8mb4',
    'port'     => 3306,
];

$SITE_CONFIG = [
    'name'        => 'Kaptain One',
    'tagline'     => 'Premium Ground Transportation',
    'url'         => 'https://yourdomain.com',
    'email'       => 'hello@yourdomain.com',
    'phone'       => '+1 (702) 555-0148',
    'phone_link'  => '+17025550148',
    'address'     => 'Las Vegas, NV',
    'timezone'    => 'America/Los_Angeles',
];
```

## Local Development

For Laragon/local testing, use:

```php
'url' => 'http://localhost/kaptain-one'
```

## Admin Login

Default admin account is seeded by `database.sql`.
Change it after first deployment.

Default login:

```text
Username: admin
Password: admin123
```

## Hostinger Deployment Checklist

### 1. Prepare files
- Confirm you are using the root project files
- Confirm there is no nested `public_html/` deployment copy

### 2. Upload to Hostinger
- Open Hostinger File Manager
- Go to server `public_html/`
- Upload all root project files and folders into `public_html/`

### 3. Create database
- Create MySQL database in hPanel
- Create DB user
- Note database name, username, password

### 4. Import schema
- Open phpMyAdmin
- Select your database
- Import `database.sql`

### 5. Update config
- Edit `config/config.php`
- Set DB credentials
- Set live site URL

### 6. Verify file permissions
Typical safe defaults:
- files: `644`
- folders: `755`

### 7. Test pages
Check:
- `/`
- `/about.php`
- `/solutions.php`
- `/services/index.php`
- `/partners/become-a-partner.php`
- `/blog/index.php`
- `/admin/`

### 8. Test forms
- partner form
- demo form
- admin login

## Notes

- This is **Phase 1 only**: marketing site + forms + blog + lightweight admin.
- No booking engine, payment flow, customer dashboard, or driver portal yet.
- The homepage services section has been rebuilt with a premium dark executive card layout.

## Current Key Files Modified Recently

```text
index.php
assets/css/main.css
assets/css/responsive.css
includes/functions.php
includes/header.php
```

## Final Recommendation

Before going live:
- update DB credentials
- update site URL
- test forms locally
- import database cleanly
- change admin password immediately
