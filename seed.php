<?php
// project_root/seed.php

declare(strict_types=1);

use PDO;
use PDOException;

// ─── Guard: hanya boleh jalan via CLI ────────────────────────────────────────
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Seeder hanya boleh dijalankan via CLI.' . PHP_EOL);
}

// ─── Guard: cek extension ────────────────────────────────────────────────────
if (!extension_loaded('pdo') || !extension_loaded('pdo_sqlite')) {
    exit("[ERROR] Extension 'pdo' atau 'pdo_sqlite' tidak aktif." . PHP_EOL);
}

// ─── Koneksi ─────────────────────────────────────────────────────────────────
$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    exit("[ERROR] database.sqlite tidak ditemukan. Jalankan migrate.php dulu." . PHP_EOL);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec('PRAGMA foreign_keys = ON;');
    $pdo->exec('PRAGMA journal_mode = WAL;');

} catch (PDOException $e) {
    exit("[ERROR] Koneksi gagal: " . $e->getMessage() . PHP_EOL);
}

// ─── Helper functions ─────────────────────────────────────────────────────────

/**
 * Load dan validasi JSON file
 */
function loadJson(string $path): array
{
    if (!file_exists($path)) {
        echo "[SKIP]  File tidak ditemukan: $path" . PHP_EOL;
        return [];
    }

    $content = file_get_contents($path);
    if ($content === false) {
        echo "[ERROR] Gagal membaca file: $path" . PHP_EOL;
        return [];
    }

    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "[ERROR] JSON tidak valid di $path: " . json_last_error_msg() . PHP_EOL;
        return [];
    }

    return is_array($data) ? $data : [];
}

/**
 * Seed satu tabel — skip jika sudah ada data
 */
function seedTable(PDO $pdo, string $table, callable $seeder): void
{
    $count = (int) $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();

    if ($count > 0) {
        echo "[SKIP]  Tabel '$table' sudah ada $count baris. Dilewati." . PHP_EOL;
        return;
    }

    echo "[SEED]  Tabel '$table'..." . PHP_EOL;

    try {
        $pdo->beginTransaction();
        $inserted = $seeder($pdo);
        $pdo->commit();
        echo "[OK]    $inserted baris berhasil di-insert ke '$table'." . PHP_EOL;

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "[ERROR] Gagal seed '$table': " . $e->getMessage() . PHP_EOL;
    }
}

// ─── Seeder: users ────────────────────────────────────────────────────────────
seedTable($pdo, 'users', function (PDO $pdo): int {
    $jsonPath = __DIR__ . '/database/users.json';
    $users    = loadJson($jsonPath);

    if (empty($users)) {
        echo "        Tidak ada data di users.json." . PHP_EOL;
        return 0;
    }

    $stmt = $pdo->prepare("
        INSERT INTO users (
            nama_lengkap,
            role,
            slug,
            slug_uuid,
            email,
            password,
            created_at,
            updated_at,
            deleted_at
        ) VALUES (
            :nama_lengkap,
            :role,
            :slug,
            :slug_uuid,
            :email,
            :password,
            :created_at,
            :updated_at,
            :deleted_at
        )
    ");

    $count = 0;

    foreach ($users as $index => $user) {
        // Validasi field wajib
        $required = ['nama_lengkap', 'roles', 'slug', 'slug_uuid', 'email', 'password'];
        foreach ($required as $field) {
            if (!isset($user[$field]) || $user[$field] === '') {
                echo "        [WARN] Baris #$index dilewati, field '$field' kosong." . PHP_EOL;
                continue 2;
            }
        }

        $stmt->execute([
            ':nama_lengkap' => trim($user['nama_lengkap']),
            ':role'         => trim($user['roles']),         // JSON pakai 'roles', kolom DB pakai 'role'
            ':slug'         => trim($user['slug']),
            ':slug_uuid'    => trim($user['slug_uuid']),
            ':email'        => strtolower(trim($user['email'])),
            ':password'     => $user['password'],            // Sudah bcrypt, jangan diubah
            ':created_at'   => $user['created_at'] ?? date('Y-m-d H:i:s'),
            ':updated_at'   => $user['updated_at'] ?? null,
            ':deleted_at'   => $user['deleted_at'] ?? null,
        ]);

        $count++;
    }

    return $count;
});

// ─── Summary ──────────────────────────────────────────────────────────────────
echo PHP_EOL . "Seeding selesai." . PHP_EOL;