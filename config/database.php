<?php

if (!function_exists('resolve_db_path')) {
    function resolve_db_path(string $file): string
    {
        // Kalau sudah absolute path → pakai langsung
        if (
            str_starts_with($file, '/') ||                 // Linux
            preg_match('/^[A-Z]:\\\\/i', $file)            // Windows
        ) {
            return $file;
        }

        // Kalau cuma nama file → arahkan ke root/database
        return ROOT_PATH . '/database/' . ltrim($file, '/');
    }
}

if (!function_exists('json_read')) {
    function json_read(string $file): array
    {
        $path = resolve_db_path($file);

        if (!file_exists($path)) {
            return [];
        }

        $data = json_decode(file_get_contents($path), true);

        return is_array($data) ? $data : [];
    }
}

if (!function_exists('json_write')) {
    function json_write(string $file, array $data): void
    {
        $path = resolve_db_path($file);
        $dir  = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(
            $path,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }
}
