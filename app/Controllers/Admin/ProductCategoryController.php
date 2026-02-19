<?php
// app/Controllers/Admin/ProductCategoryController.php

namespace App\Controllers\Admin;

class ProductCategoryController
{
    // =========================================================================
    // INDEX
    // =========================================================================
    public static function index(): void
    {
        admin_auth();

        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');

        $sql    = "SELECT id, title, description, icon, slug_uuid, available, created_at
                   FROM product_categories
                   WHERE deleted_at IS NULL";
        $params = [];

        if ($search !== '') {
            $sql              .= " AND title LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $allCategories = $stmt->fetchAll();

        $pagination = paginate($allCategories, 10, $page);

        view('admin/product-categories/index', [
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

        view('admin/product-categories/create');
    }

    // =========================================================================
    // STORE
    // =========================================================================
    public static function store(): void
    {
        admin_auth();

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $available   = isset($_POST['available']) && $_POST['available'] === '1' ? 1 : 1; // default available

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
            redirect('/admin/product-categories/create');
        }

        // ── Cek duplikat slug ─────────────────────────────────────────────────
        $slug  = slugify($title);
        $check = db()->prepare("SELECT COUNT(*) FROM product_categories WHERE slug = :slug AND deleted_at IS NULL");
        $check->execute([':slug' => $slug]);
        if ((int) $check->fetchColumn() > 0) {
            flash('admin_error', 'Category dengan title tersebut sudah ada.');
            redirect('/admin/product-categories/create');
        }

        // ── Upload icon (opsional) ────────────────────────────────────────────
        $icon = null;
        if (!empty($_FILES['icon']['name'])) {
            try {
                $icon = upload_image(
                    $_FILES['icon'],
                    ROOT_PATH . '/storage/icons',
                    2
                );
            } catch (\Exception $e) {
                flash('admin_error', $e->getMessage());
                redirect('/admin/product-categories/create');
            }
        }

        // ── Insert ────────────────────────────────────────────────────────────
        $uuid     = uuid_v4();
        $slugUuid = $slug . '-' . $uuid;

        $stmt = db()->prepare("
            INSERT INTO product_categories (title, description, icon, slug, slug_uuid, available, created_at)
            VALUES (:title, :description, :icon, :slug, :slug_uuid, :available, :created_at)
        ");

        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':icon'        => $icon,
            ':slug'        => $slug,
            ':slug_uuid'   => $slugUuid,
            ':available'   => $available,
            ':created_at'  => date('Y-m-d H:i:s'),
        ]);

        flash('admin_success', 'Category berhasil ditambahkan.');
        redirect('/admin/product-categories');
    }

    // =========================================================================
    // EDIT
    // =========================================================================
    public static function edit(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            SELECT id, title, description, icon, slug, slug_uuid, available
            FROM product_categories
            WHERE slug_uuid = :slug_uuid
              AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $category = $stmt->fetch();

        if (!$category) {
            flash('admin_error', 'Category tidak ditemukan.');
            redirect('/admin/product-categories');
        }

        view('admin/product-categories/edit', compact('category'));
    }

    // =========================================================================
    // UPDATE
    // =========================================================================
    public static function update(string $slugUuid): void
    {
        admin_auth();

        $title      = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $available  = isset($_POST['available']) && $_POST['available'] === '1' ? 1 : 0;
        $removeIcon = ($_POST['remove_icon'] ?? '0') === '1';

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
            redirect('/admin/product-categories/' . $slugUuid . '/edit');
        }

        // ── Ambil data lama ───────────────────────────────────────────────────
        $stmt = db()->prepare("
            SELECT id, icon FROM product_categories
            WHERE slug_uuid = :slug_uuid AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $category = $stmt->fetch();

        if (!$category) {
            flash('admin_error', 'Category tidak ditemukan.');
            redirect('/admin/product-categories');
        }

        // ── Cek duplikat slug (exclude diri sendiri) ──────────────────────────
        $newSlug = slugify($title);
        $check   = db()->prepare("
            SELECT COUNT(*) FROM product_categories
            WHERE slug = :slug AND id != :id AND deleted_at IS NULL
        ");
        $check->execute([':slug' => $newSlug, ':id' => $category['id']]);
        if ((int) $check->fetchColumn() > 0) {
            flash('admin_error', 'Category dengan title tersebut sudah ada.');
            redirect('/admin/product-categories/' . $slugUuid . '/edit');
        }

        // ── Handle icon ───────────────────────────────────────────────────────
        $iconPath = $category['icon'];

        // Hapus icon
        if ($removeIcon && $iconPath !== null) {
            $oldFile = ROOT_PATH . '/storage/icons/' . $iconPath;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            $iconPath = null;
        }

        // Upload icon baru
        if (!empty($_FILES['icon']['name'])) {
            try {
                $newIcon = upload_image(
                    $_FILES['icon'],
                    ROOT_PATH . '/storage/icons',
                    2
                );

                if ($iconPath !== null) {
                    $oldFile = ROOT_PATH . '/storage/icons/' . $iconPath;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $iconPath = $newIcon;

            } catch (\Exception $e) {
                flash('admin_error', $e->getMessage());
                redirect('/admin/product-categories/' . $slugUuid . '/edit');
            }
        }

        // ── Update (UUID tetap, slug boleh berubah) ───────────────────────────
        $uuidPart    = substr($slugUuid, -36);
        $newSlugUuid = $newSlug . '-' . $uuidPart;

        $stmt = db()->prepare("
            UPDATE product_categories
            SET title       = :title,
                description = :description,
                icon        = :icon,
                slug        = :slug,
                slug_uuid   = :slug_uuid,
                available   = :available,
                updated_at  = :updated_at
            WHERE id = :id
        ");

        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':icon'        => $iconPath,
            ':slug'        => $newSlug,
            ':slug_uuid'   => $newSlugUuid,
            ':available'   => $available,
            ':updated_at'  => date('Y-m-d H:i:s'),
            ':id'          => $category['id'],
        ]);

        flash('admin_success', 'Category berhasil diupdate.');
        redirect('/admin/product-categories');
    }

    // =========================================================================
    // DESTROY (Soft Delete)
    // =========================================================================
    public static function destroy(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            UPDATE product_categories
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

        redirect('/admin/product-categories');
    }
}