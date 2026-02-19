<?php

return "
    CREATE TABLE IF NOT EXISTS users (
        id           INTEGER PRIMARY KEY AUTOINCREMENT,
        nama_lengkap TEXT    NOT NULL,
        role         TEXT    NOT NULL DEFAULT 'admin',
        slug         TEXT    NOT NULL UNIQUE,
        slug_uuid    TEXT    NOT NULL UNIQUE,
        email        TEXT    NOT NULL UNIQUE,
        password     TEXT    NOT NULL,
        created_at   TEXT    NOT NULL DEFAULT (datetime('now', 'localtime')),
        updated_at   TEXT    DEFAULT NULL,
        deleted_at   TEXT    DEFAULT NULL
    );

    CREATE INDEX IF NOT EXISTS idx_users_email     ON users(email);
    CREATE INDEX IF NOT EXISTS idx_users_slug      ON users(slug);
    CREATE INDEX IF NOT EXISTS idx_users_slug_uuid ON users(slug_uuid);
    CREATE INDEX IF NOT EXISTS idx_users_deleted_at ON users(deleted_at);
";