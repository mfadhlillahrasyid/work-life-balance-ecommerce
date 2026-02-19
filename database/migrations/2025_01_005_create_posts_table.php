<?php

return "
    CREATE TABLE IF NOT EXISTS posts (
        id               INTEGER PRIMARY KEY AUTOINCREMENT,
        title            TEXT    NOT NULL,
        slug             TEXT    NOT NULL UNIQUE,
        slug_uuid        TEXT    NOT NULL UNIQUE,
        post_category_id INTEGER NOT NULL,
        banner           TEXT    DEFAULT NULL,
        content          TEXT    NOT NULL,
        tags             TEXT    DEFAULT NULL,
        status           INTEGER NOT NULL DEFAULT 0,
        created_at       TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),
        updated_at       TEXT    DEFAULT NULL,
        deleted_at       TEXT    DEFAULT NULL,

        FOREIGN KEY (post_category_id) REFERENCES post_categories(id)
    );

    CREATE INDEX IF NOT EXISTS idx_posts_slug             ON posts(slug);
    CREATE INDEX IF NOT EXISTS idx_posts_slug_uuid        ON posts(slug_uuid);
    CREATE INDEX IF NOT EXISTS idx_posts_post_category_id ON posts(post_category_id);
    CREATE INDEX IF NOT EXISTS idx_posts_status           ON posts(status);
    CREATE INDEX IF NOT EXISTS idx_posts_deleted_at       ON posts(deleted_at);
";