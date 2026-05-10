<?php
/**
 * Kaptain One - Utility Functions
 */

require_once __DIR__ . '/../config/config.php';

// Escape output
function e(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Get site config value
function site(string $key): string {
    global $SITE_CONFIG;
    return $SITE_CONFIG[$key] ?? '';
}

// Generate slug from string
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\d]+~', '', $text);
    $text = trim($text, '-');
    return strtolower($text);
}

// Format phone for display
function format_phone(string $phone): string {
    return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', preg_replace('/\D/', '', $phone));
}

// Generate CSRF token
function csrf_token(): string {
    global $SECURITY_CONFIG;
    if (empty($_SESSION[$SECURITY_CONFIG['csrf_token_name']])) {
        $_SESSION[$SECURITY_CONFIG['csrf_token_name']] = bin2hex(random_bytes(32));
    }
    return $_SESSION[$SECURITY_CONFIG['csrf_token_name']];
}

// Verify CSRF token
function verify_csrf(string $token): bool {
    global $SECURITY_CONFIG;
    try {
        if ($token === '') {
            return false;
        }

        $tokenName = $SECURITY_CONFIG['csrf_token_name'] ?? '';
        if ($tokenName === '' || empty($_SESSION[$tokenName]) || !is_string($_SESSION[$tokenName])) {
            return false;
        }

        return hash_equals($_SESSION[$tokenName], $token);
    } catch (Throwable $e) {
        error_log('CSRF verification failed: ' . $e->getMessage());
        return false;
    }
}

// Get page title
function page_title(string $pageTitle = ''): string {
    $site = site('name');
    return $pageTitle ? "{$pageTitle} | {$site}" : $site;
}

// Active nav class
function nav_active(string $page): string {
    $script = str_replace('\\', '/', $_SERVER['PHP_SELF'] ?? '');
    $current = basename($script, '.php');

    $map = [
        'services' => ['/services', '/services/'],
        'gig-workers' => ['/gig-workers.php'],
        'packages' => ['/packages.php', '/package.php'],
        'about' => ['/about.php'],
        'blog' => ['/blog', '/blog/'],
        'contact' => ['/contact.php']
    ];

    if (isset($map[$page])) {
        foreach ($map[$page] as $needle) {
            if (stripos($script, $needle) !== false) {
                return 'is-active';
            }
        }
    }

    return $current === $page ? 'is-active' : '';
}

// Truncate text
function truncate(string $text, int $length = 150): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

// Format date
function format_date(string $date, string $format = 'M j, Y'): string {
    return date($format, strtotime($date));
}

// Build base URL dynamically, local-safe
function base_url(): string {
    $configured = rtrim(site('url'), '/');

    if (!empty($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
        if (stripos($host, 'localhost') !== false || preg_match('/^(127\.0\.0\.1|192\.168\.|10\.|172\.(1[6-9]|2\d|3[0-1])\.)/', $host)) {
            $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
            $scriptDir = rtrim($scriptDir, '/.');

            if ($scriptDir === '') {
                return 'http://' . $host;
            }

            if (preg_match('#/(admin|auth|blog|dashboard|partners|services)(/.*)?$#', $scriptDir)) {
                $scriptDir = preg_replace('#/(admin|auth|blog|dashboard|partners|services)(/.*)?$#', '', $scriptDir);
                $scriptDir = rtrim($scriptDir, '/');
            }

            return 'http://' . $host . $scriptDir;
        }
    }

    return $configured;
}

// Asset URL
function asset(string $path): string {
    return base_url() . '/assets/' . ltrim($path, '/');
}

// Check if user is logged in
function is_logged_in(): bool {
    return isset($_SESSION['admin_id']) && $_SESSION['admin_id'] > 0;
}

// Redirect
function redirect(string $url): void {
    header("Location: {$url}");
    exit;
}

// Flash message
function flash(string $key): ?string {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function set_flash(string $key, string $message): void {
    $_SESSION['flash'][$key] = $message;
}
