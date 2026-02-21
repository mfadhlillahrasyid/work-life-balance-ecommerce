<?php
// database/migrations/2025_01_008_create_customers_table.php

return "
    CREATE TABLE IF NOT EXISTS customers (
        id            INTEGER PRIMARY KEY AUTOINCREMENT,
        fullname      TEXT    NOT NULL,
        slug          TEXT    NOT NULL UNIQUE,
        slug_uuid     TEXT    NOT NULL UNIQUE,
        email         TEXT    NOT NULL UNIQUE,
        password      TEXT    NOT NULL,
        profile_pics  TEXT    DEFAULT NULL,
        phone_number  TEXT    DEFAULT NULL,
        alamat_lengkap TEXT   DEFAULT NULL,
        provinsi      TEXT    DEFAULT NULL,
        kabupaten     TEXT    DEFAULT NULL,
        kota          TEXT    DEFAULT NULL,
        kode_pos      TEXT    DEFAULT NULL,
        created_at    TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),
        updated_at    TEXT    DEFAULT NULL,
        deleted_at    TEXT    DEFAULT NULL
    );

    CREATE INDEX IF NOT EXISTS idx_customers_email     ON customers(email);
    CREATE INDEX IF NOT EXISTS idx_customers_slug_uuid ON customers(slug_uuid);
    CREATE INDEX IF NOT EXISTS idx_customers_deleted_at ON customers(deleted_at);
";