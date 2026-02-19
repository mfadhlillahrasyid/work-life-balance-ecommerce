<?php
// database/migrations/2025_01_006_create_product_images_table.php

return "
    CREATE TABLE IF NOT EXISTS product_images (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        image      TEXT    NOT NULL,
        sort_order INTEGER NOT NULL DEFAULT 0,
        created_at TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),

        FOREIGN KEY (product_id) REFERENCES products(id)
    );

    CREATE INDEX IF NOT EXISTS idx_product_images_product_id ON product_images(product_id);
";