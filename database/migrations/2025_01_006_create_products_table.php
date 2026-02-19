<?php
// database/migrations/2025_01_005_create_products_table.php

return "
    CREATE TABLE IF NOT EXISTS products (
        id                  INTEGER PRIMARY KEY AUTOINCREMENT,
        title               TEXT    NOT NULL,
        slug                TEXT    NOT NULL UNIQUE,
        slug_uuid           TEXT    NOT NULL UNIQUE,
        product_category_id INTEGER NOT NULL,
        gender_id           INTEGER NOT NULL,
        description         TEXT    DEFAULT NULL,
        status              INTEGER NOT NULL DEFAULT 0,
        created_at          TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),
        updated_at          TEXT    DEFAULT NULL,
        deleted_at          TEXT    DEFAULT NULL,

        FOREIGN KEY (product_category_id) REFERENCES product_categories(id),
        FOREIGN KEY (gender_id) REFERENCES genders(id)
    );

    CREATE INDEX IF NOT EXISTS idx_products_slug                ON products(slug);
    CREATE INDEX IF NOT EXISTS idx_products_slug_uuid           ON products(slug_uuid);
    CREATE INDEX IF NOT EXISTS idx_products_product_category_id ON products(product_category_id);
    CREATE INDEX IF NOT EXISTS idx_products_gender_id           ON products(gender_id);
    CREATE INDEX IF NOT EXISTS idx_products_status              ON products(status);
    CREATE INDEX IF NOT EXISTS idx_products_deleted_at          ON products(deleted_at);
";