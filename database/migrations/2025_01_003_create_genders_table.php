<?php

return "
    CREATE TABLE IF NOT EXISTS genders (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        banner       TEXT    DEFAULT NULL,
        title        TEXT    NOT NULL,
        description  TEXT    NOT NULL,
        slug         TEXT    NOT NULL UNIQUE,
        slug_uuid    TEXT    NOT NULL UNIQUE,
        created_at   TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),
        updated_at   TEXT    DEFAULT NULL,
        deleted_at   TEXT    DEFAULT NULL
    );

    CREATE INDEX IF NOT EXISTS idx_genders_slug      ON genders(slug);
    CREATE INDEX IF NOT EXISTS idx_genders_slug_uuid ON genders(slug_uuid);
    CREATE INDEX IF NOT EXISTS idx_genders_deleted_at ON genders(deleted_at);
";