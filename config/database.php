<?php
// config/database.php

// use PDO;
// use PDOException;

// ─── SQLite PDO Connection (Singleton) ───────────────────────────────────────
if (!function_exists('db')) {
    function db(): PDO
    {
        static $pdo = null;

        if ($pdo === null) {
            $dbPath = ROOT_PATH . '/database/database.sqlite';

            if (!file_exists($dbPath)) {
                http_response_code(500);
                exit('[ERROR] Database tidak ditemukan. Jalankan migrate.php terlebih dahulu.');
            }

            try {
                $pdo = new PDO('sqlite:' . $dbPath, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);

                $pdo->exec('PRAGMA journal_mode = WAL;');
                $pdo->exec('PRAGMA foreign_keys = ON;');

            } catch (PDOException $e) {
                http_response_code(500);
                exit('[ERROR] Koneksi database gagal: ' . $e->getMessage());
            }
        }

        return $pdo;
    }
}

// ─── JSON Helpers (Legacy - hapus bertahap setelah semua controller migrasi) ──
if (!function_exists('resolve_db_path')) {
    function resolve_db_path(string $file): string
    {
        if (
            str_starts_with($file, '/') ||
            preg_match('/^[A-Z]:\\\\/i', $file)
        ) {
            return $file;
        }

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