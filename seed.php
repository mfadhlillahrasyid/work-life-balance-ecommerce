<?php
// project_root/seed.php

declare(strict_types=1);

require_once __DIR__ . '/config/util.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Seeder hanya boleh dijalankan via CLI.\n");
}

if (!extension_loaded('pdo') || !extension_loaded('pdo_sqlite')) {
    exit("[ERROR] Extension 'pdo' atau 'pdo_sqlite' tidak aktif.\n");
}

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    exit("[ERROR] database.sqlite tidak ditemukan. Jalankan migrate.php dulu.\n");
}

try {
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec('PRAGMA foreign_keys = ON;');
    $pdo->exec('PRAGMA journal_mode = WAL;');

} catch (PDOException $e) {
    exit("[ERROR] Koneksi gagal: " . $e->getMessage() . "\n");
}

/**
 * Seed satu tabel (skip jika sudah ada data)
 */
function seedTable(PDO $pdo, string $table, callable $seeder): void
{
    $count = (int) $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();

    if ($count > 0) {
        echo "[SKIP]  Tabel '$table' sudah ada $count baris.\n";
        return;
    }

    echo "[SEED]  $table...\n";

    try {
        $pdo->beginTransaction();
        $inserted = $seeder($pdo);
        $pdo->commit();
        echo "[OK]    $inserted baris berhasil di-insert.\n";

    } catch (Throwable $e) {
        $pdo->rollBack();
        echo "[ERROR] $table gagal: " . $e->getMessage() . "\n";
    }
}

/**
 * Registry Seeder
 */
$seeders = [
    'users'             => __DIR__ . '/database/seeding/seed_users.php',
    'shipping_zones'    => __DIR__ . '/database/seeding/seed_shipping_zones.php',
];

/**
 * CLI Argument Handling
 */
$target = $argv[1] ?? 'all';

if ($target === 'all') {

    foreach ($seeders as $table => $file) {

        if (!file_exists($file)) {
            echo "[SKIP]  Seeder file tidak ditemukan: $file\n";
            continue;
        }

        $seeder = require $file;

        if (!is_callable($seeder)) {
            echo "[ERROR] Seeder $file tidak valid.\n";
            continue;
        }

        seedTable($pdo, $table, $seeder);
    }

} else {

    if (!isset($seeders[$target])) {
        echo "[ERROR] Seeder '$target' tidak ditemukan.\n";
        exit;
    }

    $file = $seeders[$target];

    if (!file_exists($file)) {
        echo "[ERROR] File seeder tidak ditemukan: $file\n";
        exit;
    }

    $seeder = require $file;

    if (!is_callable($seeder)) {
        echo "[ERROR] Seeder file tidak mengembalikan callable.\n";
        exit;
    }

    seedTable($pdo, $target, $seeder);
}

echo "\nSeeding selesai.\n";