<?php

function slugify($text)
{
    return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), '-');
}

function breadcrumb(array $items)
{
    echo '<nav class="text-xs sm:text-xs text-gray-500">';
    echo '<ol class="flex flex-wrap gap-1">';

    $lastKey = array_key_last($items);

    foreach ($items as $label => $url) {
        echo '<li class="flex items-center  gap-1">';

        if ($url && $label !== $lastKey) {
            echo '<a href="' . htmlspecialchars($url) . '" class="hover:underline text-gray-600">';
            echo htmlspecialchars($label);
            echo '</a>';
            echo '<span>/</span>';
        } else {
            echo '<span class="text-gray-600 truncate w-20 sm:w-full">';
            echo htmlspecialchars($label);
            echo '</span>';
        }

        echo '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}

// ===============================
// NAV BY CATEGORIES
// ===============================
function get_nav_categories(): array
{
    if (isset($GLOBALS['_nav_categories'])) {
        return $GLOBALS['_nav_categories'];
    }

    $stmt = db()->query("
        SELECT id, title, slug, icon, description
        FROM product_categories
        WHERE deleted_at IS NULL
          AND available = 1
        ORDER BY title ASC
    ");

    $GLOBALS['_nav_categories'] = $stmt->fetchAll();
    return $GLOBALS['_nav_categories'];
}

// ===============================
// NAV BY GENDER
// ===============================
function get_nav_genders(): array
{
    if (isset($GLOBALS['_nav_genders'])) {
        return $GLOBALS['_nav_genders'];
    }

    $stmt = db()->query("
        SELECT id, title, slug, banner
        FROM genders
        WHERE deleted_at IS NULL
        ORDER BY title ASC
    ");

    $GLOBALS['_nav_genders'] = $stmt->fetchAll();
    return $GLOBALS['_nav_genders'];
}

// ===============================
// NAV ACTIVE HELPERS
// ===============================
if (!function_exists('current_path')) {
    function current_path(): string
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        return $uri === '' ? '/' : '/' . $uri;
    }
}

if (!function_exists('is_active_exact')) {
    function is_active_exact(string $path): string
    {
        return current_path() === $path ? 'bg-gray-100 font-semibold' : '';
    }
}

if (!function_exists('is_active_group')) {
    // function is_active_group(string $prefix): string
    // {
    //     return str_starts_with(current_path(), $prefix)
    //         ? 'bg-gray-100 font-semibold'
    //         : '';
    // }

    // helper
    function is_active_group(string $prefix)
    {
        return str_starts_with(current_path(), $prefix);
    }

}

// ===============================
// UUID v4
// ===============================
if (!function_exists('uuid_v4')) {
    function uuid_v4(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff)
        );
    }
}

// ===============================
// SLUG + UUID
// ===============================
if (!function_exists('slug_uuid')) {
    function slug_uuid(string $title): string
    {
        return slugify($title) . '-' . uuid_v4();
    }
}

if (!function_exists('uuid_from_slug')) {
    function uuid_from_slug(string $slugUuid): string
    {
        $parts = explode('-', $slugUuid);
        return implode('-', array_slice($parts, -5));
    }
}

function format_date(
    string $date,
    string $format = 'D, d M Y'
): string {
    if (empty($date)) {
        return '-';
    }

    $timestamp = strtotime($date);

    if ($timestamp === false) {
        return '-';
    }

    return date($format, $timestamp);
}

function upload_image(
    array $file,
    string $targetDir,
    int $maxSizeMB = 2,
    array $allowedMime = ['image/jpeg', 'image/png', 'image/webp']
): string {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Upload failed');
    }

    // Size validation
    $maxBytes = $maxSizeMB * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        throw new Exception("Image too large (max {$maxSizeMB}MB)");
    }

    // MIME validation (REAL mime, not extension)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMime, true)) {
        throw new Exception('Invalid image format');
    }

    // Extension mapping
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $extension = $extensions[$mime] ?? null;
    if (!$extension) {
        throw new Exception('Unsupported image type');
    }

    // Ensure directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Generate safe filename
    $filename = uniqid('img_', true) . '.' . $extension;
    $destination = rtrim($targetDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to save uploaded file');
    }

    return $filename;
}

function parse_tags(string $tags): array
{
    return array_values(array_filter(array_map(
        fn($tag) => strtolower(trim($tag)),
        explode(',', $tags)
    )));
}

function parse_csv(string $value): array
{
    return array_values(array_filter(array_map(
        fn($item) => strtolower(trim($item)),
        explode(',', $value)
    )));
}

function format_currency(float $amount, string $currency = 'IDR'): string
{
    return match ($currency) {
        'IDR' => 'IDR ' . number_format($amount, 0, ',', '.'),
        'USD' => '$' . number_format($amount, 2, '.', ','),
        default => number_format($amount)
    };
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
