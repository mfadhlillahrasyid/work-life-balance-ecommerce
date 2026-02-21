<?php
// database/migrations/2025_01_009_create_shipping_zones_table.php

return "
    CREATE TABLE IF NOT EXISTS shipping_zones (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        slug        TEXT    NOT NULL UNIQUE,
        slug_uuid   TEXT    NOT NULL UNIQUE,
        name        TEXT    NOT NULL,
        kurir       TEXT    NOT NULL DEFAULT 'JNE',
        icon        TEXT    DEFAULT NULL,
        provinces   TEXT    NOT NULL DEFAULT '[]',
        cost        INTEGER NOT NULL DEFAULT 0,
        created_at  TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),
        updated_at  TEXT    DEFAULT NULL
    );

    CREATE INDEX IF NOT EXISTS idx_shipping_zones_name ON shipping_zones(name);
";