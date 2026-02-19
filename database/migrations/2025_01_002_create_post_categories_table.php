<?php

return "
    CREATE TABLE IF NOT EXISTS post_categories (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        title        TEXT    NOT NULL,
        description  TEXT    NOT NULL,
        slug         TEXT    NOT NULL UNIQUE,
        slug_uuid    TEXT    NOT NULL UNIQUE,
        created_at   TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),
        updated_at   TEXT    DEFAULT NULL,
        deleted_at   TEXT    DEFAULT NULL
    );

    CREATE INDEX IF NOT EXISTS idx_post_categories_slug       ON post_categories(slug);
    CREATE INDEX IF NOT EXISTS idx_post_categories_slug_uuid  ON post_categories(slug_uuid);
    CREATE INDEX IF NOT EXISTS idx_post_categories_deleted_at ON post_categories(deleted_at);
";