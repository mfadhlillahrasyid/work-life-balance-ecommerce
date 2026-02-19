<?php
// database/migrations/2025_01_007_create_product_variants_table.php

return "
    CREATE TABLE IF NOT EXISTS product_variants (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        color      TEXT    NOT NULL,
        size       TEXT    NOT NULL,
        price      INTEGER NOT NULL DEFAULT 0,
        stock      INTEGER NOT NULL DEFAULT 0,
        sku        TEXT    DEFAULT NULL UNIQUE,
        created_at TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),
        updated_at TEXT    DEFAULT NULL,

        FOREIGN KEY (product_id) REFERENCES products(id),
        UNIQUE(product_id, color, size)
    );

    CREATE INDEX IF NOT EXISTS idx_product_variants_product_id ON product_variants(product_id);
    CREATE INDEX IF NOT EXISTS idx_product_variants_sku        ON product_variants(sku);
    CREATE INDEX IF NOT EXISTS idx_product_variants_color      ON product_variants(color);
    CREATE INDEX IF NOT EXISTS idx_product_variants_size       ON product_variants(size);
";