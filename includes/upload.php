<?php
/**
 * Secure listing photo upload handling.
 */

require_once __DIR__ . '/functions.php';

function upload_ini_bytes(string $value): int {
    $value = trim($value);
    if ($value === '') {
        return 0;
    }
    $unit = strtolower($value[strlen($value) - 1]);
    $number = (float) $value;
    return match ($unit) {
        'g' => (int) ($number * 1024 * 1024 * 1024),
        'm' => (int) ($number * 1024 * 1024),
        'k' => (int) ($number * 1024),
        default => (int) $number,
    };
}

function request_exceeded_post_max(): bool {
    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    $postMax = upload_ini_bytes((string) ini_get('post_max_size'));
    return $postMax > 0 && $contentLength > $postMax;
}

function ensure_listing_upload_root(): bool {
    $root = dirname(__DIR__) . '/assets/uploads/listings';
    if (!is_dir($root) && !mkdir($root, 0755, true)) {
        error_log('Unable to create listing upload root: ' . $root);
        return false;
    }

    $htaccess = $root . '/.htaccess';
    $denyRule = "<FilesMatch \"\\.(php|phtml|phar|php3|php4|php5|php7|pl|py|sh|cgi)$\">\n    Deny from all\n</FilesMatch>\n";
    if (!file_exists($htaccess) && file_put_contents($htaccess, $denyRule) === false) {
        error_log('Unable to write listing upload .htaccess: ' . $htaccess);
        return false;
    }

    return is_writable($root);
}

function verified_image_mime(string $tmpPath): ?string {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $mimeA = function_exists('mime_content_type') ? mime_content_type($tmpPath) : null;
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeB = $finfo ? finfo_file($finfo, $tmpPath) : null;
    if ($finfo) {
        finfo_close($finfo);
    }

    if (!$mimeA || !$mimeB || $mimeA !== $mimeB || !in_array($mimeA, $allowed, true)) {
        return null;
    }
    return $mimeA;
}

function image_extension_from_mime(string $mime): string {
    return [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ][$mime] ?? 'jpg';
}

function resize_listing_image_if_possible(string $source, string $destination, string $mime): bool {
    if (!extension_loaded('gd')) {
        error_log('GD extension unavailable; storing original listing image.');
        return move_uploaded_file($source, $destination);
    }

    $size = getimagesize($source);
    if (!$size) {
        return false;
    }
    [$width, $height] = $size;
    $longest = max($width, $height);
    $scale = $longest > 1600 ? 1600 / $longest : 1;
    $newWidth = (int) round($width * $scale);
    $newHeight = (int) round($height * $scale);

    $create = [
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/png' => 'imagecreatefrompng',
        'image/webp' => 'imagecreatefromwebp',
    ][$mime] ?? null;
    if (!$create || !function_exists($create)) {
        return move_uploaded_file($source, $destination);
    }

    $src = $create($source);
    if (!$src) {
        return false;
    }
    $dst = imagecreatetruecolor($newWidth, $newHeight);
    if ($mime === 'image/png' || $mime === 'image/webp') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    $saved = match ($mime) {
        'image/png' => imagepng($dst, $destination, 6),
        'image/webp' => imagewebp($dst, $destination, 85),
        default => imagejpeg($dst, $destination, 85),
    };
    imagedestroy($src);
    imagedestroy($dst);
    @unlink($source);
    return $saved;
}

function upload_listing_photos(array $files, int $listingId, int $existingCount = 0): array {
    $result = ['paths' => [], 'errors' => []];
    if (request_exceeded_post_max()) {
        $result['errors'][] = 'Photo upload failed because the file is too large. The maximum size is 5MB per photo.';
        return $result;
    }
    if (empty($files['name']) || !is_array($files['name'])) {
        return $result;
    }
    if (!ensure_listing_upload_root()) {
        $result['errors'][] = 'Uploads are temporarily unavailable. Please try again later.';
        return $result;
    }

    $root = dirname(__DIR__) . '/assets/uploads/listings';
    $listingDir = $root . '/' . $listingId;
    if (!is_dir($listingDir) && !mkdir($listingDir, 0755, true)) {
        error_log('Unable to create listing upload directory: ' . $listingDir);
        $result['errors'][] = 'Uploads are temporarily unavailable. Please try again later.';
        return $result;
    }

    $remaining = max(0, 6 - $existingCount);
    foreach ($files['name'] as $idx => $name) {
        $errorCode = $files['error'][$idx] ?? UPLOAD_ERR_NO_FILE;
        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($remaining <= 0) {
            $result['errors'][] = 'You can upload up to 6 photos per listing.';
            break;
        }
        if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
            $result['errors'][] = 'Photo upload failed because the file is too large. The maximum size is 5MB per photo.';
            continue;
        }
        if ($errorCode !== UPLOAD_ERR_OK) {
            $result['errors'][] = 'One photo could not be uploaded. Please try again.';
            continue;
        }
        $tmp = $files['tmp_name'][$idx];
        if (($files['size'][$idx] ?? 0) > 5 * 1024 * 1024) {
            $result['errors'][] = 'Each photo must be 5MB or less.';
            continue;
        }
        $mime = verified_image_mime($tmp);
        if (!$mime) {
            $result['errors'][] = 'Only real JPEG, PNG, or WebP images are accepted.';
            continue;
        }
        $imageSize = getimagesize($tmp);
        if (!$imageSize || $imageSize[0] > 4000 || $imageSize[1] > 4000) {
            $result['errors'][] = 'Photos must be 4000px or smaller on each side.';
            continue;
        }

        $filename = bin2hex(random_bytes(16)) . '.' . image_extension_from_mime($mime);
        $destination = $listingDir . '/' . $filename;
        if (!resize_listing_image_if_possible($tmp, $destination, $mime)) {
            error_log('Unable to store uploaded listing image: ' . $destination);
            $result['errors'][] = 'One photo could not be saved.';
            continue;
        }

        $result['paths'][] = '/assets/uploads/listings/' . $listingId . '/' . $filename;
        $remaining--;
    }

    return $result;
}
