<?php
// app/Controllers/Admin/PostCategoryController.php

namespace App\Controllers\Admin;

class PostCategoryController
{
    // =========================================================================
    // INDEX
    // =========================================================================
    public static function index(): void
    {
        admin_auth();

        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');

        $sql    = "SELECT id, title, description, slug_uuid, created_at
                   FROM post_categories
                   WHERE deleted_at IS NULL";
        $params = [];

        if ($search !== '') {
            $sql           .= " AND title LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $allCategories = $stmt->fetchAll();

        $pagination = paginate($allCategories, 10, $page);

        view('admin/post-categories/index', [
            'categories' => $pagination['data'],
            'pagination' => $pagination['meta'],
            'search'     => $search,
        ]);
    }

    // =========================================================================
    // CREATE
    // =========================================================================
    public static function create(): void
    {
        admin_auth();

        view('admin/post-categories/create');
    }

    // =========================================================================
    // STORE
    // =========================================================================
    public static function store(): void
    {
        admin_auth();

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // ── Validasi ──────────────────────────────────────────────────────────
        $errors = [];

        if ($title === '') {
            $errors[] = 'Title wajib diisi.';
        }

        if ($description === '') {
            $errors[] = 'Description wajib diisi.';
        }

        if (!empty($errors)) {
            flash('admin_error', implode(' ', $errors));
            redirect('/admin/post-categories/create');
        }

        // ── Cek duplikat slug ─────────────────────────────────────────────────
        $slug = slugify($title);
        $check = db()->prepare("SELECT COUNT(*) FROM post_categories WHERE slug = :slug AND deleted_at IS NULL");
        $check->execute([':slug' => $slug]);
        if ((int) $check->fetchColumn() > 0) {
            flash('admin_error', 'Category dengan title tersebut sudah ada.');
            redirect('/admin/post-categories/create');
        }

        // ── Insert ────────────────────────────────────────────────────────────
        $uuid     = uuid_v4();
        $slugUuid = $slug . '-' . $uuid;

        $stmt = db()->prepare("
            INSERT INTO post_categories (title, description, slug, slug_uuid, created_at)
            VALUES (:title, :description, :slug, :slug_uuid, :created_at)
        ");

        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':slug'        => $slug,
            ':slug_uuid'   => $slugUuid,
            ':created_at'  => date('Y-m-d H:i:s'),
        ]);

        flash('admin_success', 'Category berhasil ditambahkan.');
        redirect('/admin/post-categories');
    }

    // =========================================================================
    // EDIT
    // =========================================================================
    public static function edit(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            SELECT id, title, description, slug, slug_uuid
            FROM post_categories
            WHERE slug_uuid = :slug_uuid
              AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $category = $stmt->fetch();

        if (!$category) {
            flash('admin_error', 'Category tidak ditemukan.');
            redirect('/admin/post-categories');
        }

        view('admin/post-categories/edit', compact('category'));
    }

    // =========================================================================
    // UPDATE
    // =========================================================================
    public static function update(string $slugUuid): void
    {
        admin_auth();

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // ── Validasi ──────────────────────────────────────────────────────────
        $errors = [];

        if ($title === '') {
            $errors[] = 'Title wajib diisi.';
        }

        if ($description === '') {
            $errors[] = 'Description wajib diisi.';
        }

        if (!empty($errors)) {
            flash('admin_error', implode(' ', $errors));
            redirect('/admin/post-categories/' . $slugUuid . '/edit');
        }

        // ── Ambil category ────────────────────────────────────────────────────
        $stmt = db()->prepare("
            SELECT id FROM post_categories
            WHERE slug_uuid = :slug_uuid AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $category = $stmt->fetch();

        if (!$category) {
            flash('admin_error', 'Category tidak ditemukan.');
            redirect('/admin/post-categories');
        }

        // ── Cek duplikat slug (exclude diri sendiri) ──────────────────────────
        $newSlug = slugify($title);
        $check   = db()->prepare("
            SELECT COUNT(*) FROM post_categories
            WHERE slug = :slug AND id != :id AND deleted_at IS NULL
        ");
        $check->execute([':slug' => $newSlug, ':id' => $category['id']]);
        if ((int) $check->fetchColumn() > 0) {
            flash('admin_error', 'Category dengan title tersebut sudah ada.');
            redirect('/admin/post-categories/' . $slugUuid . '/edit');
        }

        // ── Update (UUID tetap, slug boleh berubah) ───────────────────────────
        $uuidPart    = substr($slugUuid, -36);
        $newSlugUuid = $newSlug . '-' . $uuidPart;

        $stmt = db()->prepare("
            UPDATE post_categories
            SET title       = :title,
                description = :description,
                slug        = :slug,
                slug_uuid   = :slug_uuid,
                updated_at  = :updated_at
            WHERE id = :id
        ");

        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':slug'        => $newSlug,
            ':slug_uuid'   => $newSlugUuid,
            ':updated_at'  => date('Y-m-d H:i:s'),
            ':id'          => $category['id'],
        ]);

        flash('admin_success', 'Category berhasil diupdate.');
        redirect('/admin/post-categories');
    }

    // =========================================================================
    // DESTROY (Soft Delete)
    // =========================================================================
    public static function destroy(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            UPDATE post_categories
            SET deleted_at = :deleted_at
            WHERE slug_uuid = :slug_uuid
              AND deleted_at IS NULL
        ");

        $stmt->execute([
            ':deleted_at' => date('Y-m-d H:i:s'),
            ':slug_uuid'  => $slugUuid,
        ]);

        if ($stmt->rowCount() > 0) {
            flash('admin_success', 'Category berhasil dihapus.');
        } else {
            flash('admin_error', 'Category tidak ditemukan.');
        }

        redirect('/admin/post-categories');
    }
}