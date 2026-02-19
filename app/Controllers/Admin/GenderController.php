<?php
// app/Controllers/Admin/GenderController.php

namespace App\Controllers\Admin;

class GenderController
{
    // =========================================================================
    // INDEX
    // =========================================================================
    public static function index(): void
    {
        admin_auth();

        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');

        $sql    = "SELECT id, title, description, banner, slug_uuid, created_at
                   FROM genders
                   WHERE deleted_at IS NULL";
        $params = [];

        if ($search !== '') {
            $sql              .= " AND title LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $allGenders = $stmt->fetchAll();

        $pagination = paginate($allGenders, 10, $page);

        view('admin/genders/index', [
            'genders'    => $pagination['data'],
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

        view('admin/genders/create');
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
            redirect('/admin/genders/create');
        }

        // ── Cek duplikat slug ─────────────────────────────────────────────────
        $slug  = slugify($title);
        $check = db()->prepare("SELECT COUNT(*) FROM genders WHERE slug = :slug AND deleted_at IS NULL");
        $check->execute([':slug' => $slug]);
        if ((int) $check->fetchColumn() > 0) {
            flash('admin_error', 'Gender dengan title tersebut sudah ada.');
            redirect('/admin/genders/create');
        }

        // ── Upload banner (opsional) ──────────────────────────────────────────
        $banner = null;
        if (!empty($_FILES['banner']['name'])) {
            try {
                $banner = upload_image(
                    $_FILES['banner'],
                    ROOT_PATH . '/storage/banners',
                    5 // max 5MB
                );
            } catch (\Exception $e) {
                flash('admin_error', $e->getMessage());
                redirect('/admin/genders/create');
            }
        }

        // ── Insert ────────────────────────────────────────────────────────────
        $uuid     = uuid_v4();
        $slugUuid = $slug . '-' . $uuid;

        $stmt = db()->prepare("
            INSERT INTO genders (title, description, banner, slug, slug_uuid, created_at)
            VALUES (:title, :description, :banner, :slug, :slug_uuid, :created_at)
        ");

        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':banner'      => $banner,
            ':slug'        => $slug,
            ':slug_uuid'   => $slugUuid,
            ':created_at'  => date('Y-m-d H:i:s'),
        ]);

        flash('admin_success', 'Gender berhasil ditambahkan.');
        redirect('/admin/genders');
    }

    // =========================================================================
    // EDIT
    // =========================================================================
    public static function edit(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            SELECT id, title, description, banner, slug, slug_uuid
            FROM genders
            WHERE slug_uuid = :slug_uuid
              AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $gender = $stmt->fetch();

        if (!$gender) {
            flash('admin_error', 'Gender tidak ditemukan.');
            redirect('/admin/genders');
        }

        view('admin/genders/edit', compact('gender'));
    }

    // =========================================================================
    // UPDATE
    // =========================================================================
    public static function update(string $slugUuid): void
    {
        admin_auth();

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $removeBanner = ($_POST['remove_banner'] ?? '0') === '1';

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
            redirect('/admin/genders/' . $slugUuid . '/edit');
        }

        // ── Ambil data lama ───────────────────────────────────────────────────
        $stmt = db()->prepare("
            SELECT id, banner FROM genders
            WHERE slug_uuid = :slug_uuid AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $gender = $stmt->fetch();

        if (!$gender) {
            flash('admin_error', 'Gender tidak ditemukan.');
            redirect('/admin/genders');
        }

        // ── Cek duplikat slug (exclude diri sendiri) ──────────────────────────
        $newSlug = slugify($title);
        $check   = db()->prepare("
            SELECT COUNT(*) FROM genders
            WHERE slug = :slug AND id != :id AND deleted_at IS NULL
        ");
        $check->execute([':slug' => $newSlug, ':id' => $gender['id']]);
        if ((int) $check->fetchColumn() > 0) {
            flash('admin_error', 'Gender dengan title tersebut sudah ada.');
            redirect('/admin/genders/' . $slugUuid . '/edit');
        }

        // ── Handle banner ─────────────────────────────────────────────────────
        $bannerPath = $gender['banner']; // default: pakai banner lama

        // Hapus banner
        if ($removeBanner && $bannerPath !== null) {
            $oldFile = ROOT_PATH . '/storage/banners/' . $bannerPath;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            $bannerPath = null;
        }

        // Upload banner baru (replace jika ada yang lama)
        if (!empty($_FILES['banner']['name'])) {
            try {
                $newBanner = upload_image(
                    $_FILES['banner'],
                    ROOT_PATH . '/storage/banners',
                    5
                );

                // Hapus banner lama kalau ada
                if ($bannerPath !== null) {
                    $oldFile = ROOT_PATH . '/storage/banners/' . $bannerPath;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $bannerPath = $newBanner;

            } catch (\Exception $e) {
                flash('admin_error', $e->getMessage());
                redirect('/admin/genders/' . $slugUuid . '/edit');
            }
        }

        // ── Update (UUID tetap, slug boleh berubah) ───────────────────────────
        $uuidPart    = substr($slugUuid, -36);
        $newSlugUuid = $newSlug . '-' . $uuidPart;

        $stmt = db()->prepare("
            UPDATE genders
            SET title       = :title,
                description = :description,
                banner      = :banner,
                slug        = :slug,
                slug_uuid   = :slug_uuid,
                updated_at  = :updated_at
            WHERE id = :id
        ");

        $stmt->execute([
            ':title'       => $title,
            ':description' => $description,
            ':banner'      => $bannerPath,
            ':slug'        => $newSlug,
            ':slug_uuid'   => $newSlugUuid,
            ':updated_at'  => date('Y-m-d H:i:s'),
            ':id'          => $gender['id'],
        ]);

        flash('admin_success', 'Gender berhasil diupdate.');
        redirect('/admin/genders');
    }

    // =========================================================================
    // DESTROY (Soft Delete)
    // =========================================================================
    public static function destroy(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            UPDATE genders
            SET deleted_at = :deleted_at
            WHERE slug_uuid = :slug_uuid
              AND deleted_at IS NULL
        ");

        $stmt->execute([
            ':deleted_at' => date('Y-m-d H:i:s'),
            ':slug_uuid'  => $slugUuid,
        ]);

        if ($stmt->rowCount() > 0) {
            flash('admin_success', 'Gender berhasil dihapus.');
        } else {
            flash('admin_error', 'Gender tidak ditemukan.');
        }

        redirect('/admin/genders');
    }
}