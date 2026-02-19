<?php
// project_root/migrate.php

declare(strict_types=1);

use PDO;
use PDOException;

// ─── Guard: pastikan hanya jalan via CLI ────────────────────────────────────
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Migration hanya boleh dijalankan via CLI.');
}

// ─── Guard: cek extension PDO & pdo_sqlite ──────────────────────────────────
if (!extension_loaded('pdo')) {
    exit("[ERROR] Extension 'pdo' tidak aktif. Aktifkan di php.ini.\n");
}

if (!extension_loaded('pdo_sqlite')) {
    exit("[ERROR] Extension 'pdo_sqlite' tidak aktif. Aktifkan di php.ini.\n");
}

// ─── Setup path ─────────────────────────────────────────────────────────────
$dbDir  = __DIR__ . '/database';
$dbPath = $dbDir . '/database.sqlite';

if (!is_dir($dbDir) && !mkdir($dbDir, 0755, true) && !is_dir($dbDir)) {
    exit("[ERROR] Gagal membuat direktori database: $dbDir\n");
}

// ─── Koneksi PDO ────────────────────────────────────────────────────────────
try {
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Aktifkan WAL mode & foreign keys untuk SQLite
    $pdo->exec('PRAGMA journal_mode = WAL;');
    $pdo->exec('PRAGMA foreign_keys = ON;');

} catch (PDOException $e) {
    exit("[ERROR] Koneksi database gagal: " . $e->getMessage() . "\n");
}

// ─── Buat tabel migrations tracker ──────────────────────────────────────────
$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        migration    TEXT    NOT NULL UNIQUE,
        migrated_at  TEXT    NOT NULL
    )
");

// ─── Baca migration files ────────────────────────────────────────────────────
$migrationsPath = __DIR__ . '/database/migrations';

if (!is_dir($migrationsPath)) {
    exit("[ERROR] Folder migrations tidak ditemukan: $migrationsPath\n");
}

$migrationFiles = glob($migrationsPath . '/*.php');

if (empty($migrationFiles)) {
    exit("[INFO] Tidak ada migration file ditemukan di: $migrationsPath\n");
}

sort($migrationFiles);

// ─── Jalankan migrations ─────────────────────────────────────────────────────
$ranCount = 0;

foreach ($migrationFiles as $file) {
    $migrationName = basename($file);

    // Cek apakah sudah pernah dijalankan
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
    $stmt->execute([$migrationName]);

    if ((int) $stmt->fetchColumn() > 0) {
        echo "[SKIP]  $migrationName (sudah pernah dijalankan)\n";
        continue;
    }

    // Ambil SQL dari file migration
    $sql = require $file;

    // Guard: pastikan file return string SQL yang valid
    if (!is_string($sql) || trim($sql) === '') {
        echo "[ERROR] $migrationName tidak me-return SQL string yang valid. Dilewati.\n";
        continue;
    }

    // Jalankan dalam transaction agar atomic
    try {
        $pdo->beginTransaction();

        $pdo->exec($sql);

        $insert = $pdo->prepare(
            "INSERT INTO migrations (migration, migrated_at) VALUES (?, datetime('now'))"
        );
        $insert->execute([$migrationName]);

        $pdo->commit();

        echo "[OK]    $migrationName\n";
        $ranCount++;

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "[ERROR] $migrationName gagal: " . $e->getMessage() . "\n";
        echo "        Migration dihentikan untuk mencegah state tidak konsisten.\n";
        exit(1);
    }
}

// ─── Summary ─────────────────────────────────────────────────────────────────
echo "\n";
echo "Migration selesai. $ranCount migration baru dijalankan.\n";