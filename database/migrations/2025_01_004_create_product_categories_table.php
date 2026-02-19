<?php

return "
    CREATE TABLE IF NOT EXISTS product_categories (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        title        TEXT    NOT NULL,
        description  TEXT    NOT NULL,
        icon         TEXT    DEFAULT NULL,
        slug         TEXT    NOT NULL UNIQUE,
        slug_uuid    TEXT    NOT NULL UNIQUE,
        available    INTEGER NOT NULL DEFAULT 1,
        created_at   TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),
        updated_at   TEXT    DEFAULT NULL,
        deleted_at   TEXT    DEFAULT NULL
    );

    CREATE INDEX IF NOT EXISTS idx_product_categories_slug       ON product_categories(slug);
    CREATE INDEX IF NOT EXISTS idx_product_categories_slug_uuid  ON product_categories(slug_uuid);
    CREATE INDEX IF NOT EXISTS idx_product_categories_available  ON product_categories(available);
    CREATE INDEX IF NOT EXISTS idx_product_categories_deleted_at ON product_categories(deleted_at);
";