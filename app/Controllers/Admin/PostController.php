<?php
// app/Controllers/Admin/PostController.php

namespace App\Controllers\Admin;

class PostController
{
    // =========================================================================
    // INDEX
    // =========================================================================
    public static function index(): void
    {
        admin_auth();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');

        $sql = "SELECT p.id, p.title, p.slug_uuid, p.status, p.banner,
               p.tags, p.created_at,
               p.content,
               pc.title AS category_name
        FROM posts p
        LEFT JOIN post_categories pc ON pc.id = p.post_category_id
        WHERE p.deleted_at IS NULL";
        $params = [];

        if ($search !== '') {
            $sql .= " AND p.title LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $allPosts = $stmt->fetchAll();

        $pagination = paginate($allPosts, 10, $page);

        view('admin/posts/index', [
            'posts' => $pagination['data'],
            'pagination' => $pagination['meta'],
            'search' => $search,
        ]);
    }

    // =========================================================================
    // CREATE
    // =========================================================================
    public static function create(): void
    {
        admin_auth();

        $categories = db()->query("
            SELECT id, title FROM post_categories
            WHERE deleted_at IS NULL
            ORDER BY title ASC
        ")->fetchAll();

        view('admin/posts/create', compact('categories'));
    }

    // =========================================================================
    // STORE
    // =========================================================================
    public static function store(): void
    {
        admin_auth();

        $title = trim($_POST['title'] ?? '');
        $postCategoryId = (int) ($_POST['post_category_id'] ?? 0);
        $content = $_POST['content'] ?? '';
        $tags = $_POST['tags'] ?? '';
        $status = isset($_POST['status']) ? 1 : 0;

        // ── Validasi ──────────────────────────────────────────────────────────
        $errors = [];

        if ($title === '') {
            $errors[] = 'Title wajib diisi.';
        }

        if ($postCategoryId === 0) {
            $errors[] = 'Category wajib dipilih.';
        }

        if ($content === '') {
            $errors[] = 'Content wajib diisi.';
        }

        if (!empty($errors)) {
            flash('admin_error', implode(' ', $errors));
            redirect('/admin/posts/create');
        }

        // ── Validasi category exist ───────────────────────────────────────────
        $check = db()->prepare("SELECT COUNT(*) FROM post_categories WHERE id = :id AND deleted_at IS NULL");
        $check->execute([':id' => $postCategoryId]);
        if ((int) $check->fetchColumn() === 0) {
            flash('admin_error', 'Category tidak valid.');
            redirect('/admin/posts/create');
        }

        // ── Upload banner ─────────────────────────────────────────────────────
        $banner = null;
        if (!empty($_FILES['banner']['name'])) {
            try {
                $banner = upload_image(
                    $_FILES['banner'],
                    ROOT_PATH . '/storage/banners',
                    2
                );
            } catch (\Exception $e) {
                flash('admin_error', $e->getMessage());
                redirect('/admin/posts/create');
            }
        }

        // ── Normalize tags → comma-separated ─────────────────────────────────
        $tagsNormalized = $tags !== '' ? implode(',', parse_tags($tags)) : null;

        // ── Insert ────────────────────────────────────────────────────────────
        $uuid = uuid_v4();
        $slug = slugify($title);
        $slugUuid = $slug . '-' . $uuid;

        $stmt = db()->prepare("
            INSERT INTO posts (title, slug, slug_uuid, post_category_id, banner, content, tags, status, created_at)
            VALUES (:title, :slug, :slug_uuid, :post_category_id, :banner, :content, :tags, :status, :created_at)
        ");

        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':slug_uuid' => $slugUuid,
            ':post_category_id' => $postCategoryId,
            ':banner' => $banner,
            ':content' => $content,
            ':tags' => $tagsNormalized,
            ':status' => $status,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);

        flash('admin_success', 'Post berhasil ditambahkan.');
        redirect('/admin/posts');
    }

    // =========================================================================
    // EDIT
    // =========================================================================
    public static function edit(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            SELECT id, title, slug, slug_uuid, post_category_id, banner, content, tags, status
            FROM posts
            WHERE slug_uuid = :slug_uuid AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $post = $stmt->fetch();

        if (!$post) {
            flash('admin_error', 'Post tidak ditemukan.');
            redirect('/admin/posts');
        }

        $categories = db()->query("
            SELECT id, title FROM post_categories
            WHERE deleted_at IS NULL
            ORDER BY title ASC
        ")->fetchAll();

        // Map category name untuk dropdown label
        $currentCategoryName = null;
        foreach ($categories as $category) {
            if ((int) $category['id'] === (int) $post['post_category_id']) {
                $currentCategoryName = $category['title'];
                break;
            }
        }

        view('admin/posts/edit', compact('post', 'categories', 'currentCategoryName'));
    }

    // =========================================================================
    // UPDATE
    // =========================================================================
    public static function update(string $slugUuid): void
    {
        admin_auth();

        $title = trim($_POST['title'] ?? '');
        $postCategoryId = (int) ($_POST['post_category_id'] ?? 0);
        $content = $_POST['content'] ?? '';
        $tags = trim($_POST['tags'] ?? '');
        $status = isset($_POST['status']) ? 1 : 0;
        $removeBanner = ($_POST['remove_banner'] ?? '0') === '1';

        // ── Validasi ──────────────────────────────────────────────────────────
        $errors = [];

        if ($title === '') {
            $errors[] = 'Title wajib diisi.';
        }

        if ($postCategoryId === 0) {
            $errors[] = 'Category wajib dipilih.';
        }

        if ($content === '') {
            $errors[] = 'Content wajib diisi.';
        }

        if (!empty($errors)) {
            flash('admin_error', implode(' ', $errors));
            redirect('/admin/posts/' . $slugUuid . '/edit');
        }

        // ── Ambil data lama ───────────────────────────────────────────────────
        $stmt = db()->prepare("
            SELECT id, banner FROM posts
            WHERE slug_uuid = :slug_uuid AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $post = $stmt->fetch();

        if (!$post) {
            flash('admin_error', 'Post tidak ditemukan.');
            redirect('/admin/posts');
        }

        // ── Handle banner ─────────────────────────────────────────────────────
        $bannerPath = $post['banner'];

        if ($removeBanner && $bannerPath !== null) {
            $oldFile = ROOT_PATH . '/storage/banners/' . $bannerPath;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            $bannerPath = null;
        }

        if (!empty($_FILES['banner']['name'])) {
            try {
                $newBanner = upload_image(
                    $_FILES['banner'],
                    ROOT_PATH . '/storage/banners',
                    2
                );

                if ($bannerPath !== null) {
                    $oldFile = ROOT_PATH . '/storage/banners/' . $bannerPath;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $bannerPath = $newBanner;

            } catch (\Exception $e) {
                flash('admin_error', $e->getMessage());
                redirect('/admin/posts/' . $slugUuid . '/edit');
            }
        }

        $tagsNormalized = $tags !== '' ? implode(',', parse_tags($tags)) : null;

        // ── Update ────────────────────────────────────────────────────────────
        $uuidPart = substr($slugUuid, -36);
        $newSlug = slugify($title);
        $newSlugUuid = $newSlug . '-' . $uuidPart;

        $stmt = db()->prepare("
            UPDATE posts
            SET title            = :title,
                slug             = :slug,
                slug_uuid        = :slug_uuid,
                post_category_id = :post_category_id,
                banner           = :banner,
                content          = :content,
                tags             = :tags,
                status           = :status,
                updated_at       = :updated_at
            WHERE id = :id
        ");

        $stmt->execute([
            ':title' => $title,
            ':slug' => $newSlug,
            ':slug_uuid' => $newSlugUuid,
            ':post_category_id' => $postCategoryId,
            ':banner' => $bannerPath,
            ':content' => $content,
            ':tags' => $tagsNormalized,
            ':status' => $status,
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $post['id'],
        ]);

        flash('admin_success', 'Post berhasil diupdate.');
        redirect('/admin/posts');
    }

    // =========================================================================
    // DESTROY (Soft Delete)
    // =========================================================================
    public static function destroy(string $slugUuid): void
    {
        admin_auth();

        // Soft delete saja — file banner TIDAK dihapus
        // Kalau nanti butuh hard delete, buat method terpisah
        $stmt = db()->prepare("
            UPDATE posts
            SET deleted_at = :deleted_at
            WHERE slug_uuid = :slug_uuid
              AND deleted_at IS NULL
        ");

        $stmt->execute([
            ':deleted_at' => date('Y-m-d H:i:s'),
            ':slug_uuid' => $slugUuid,
        ]);

        if ($stmt->rowCount() > 0) {
            flash('admin_success', 'Post berhasil dihapus.');
        } else {
            flash('admin_error', 'Post tidak ditemukan.');
        }

        redirect('/admin/posts');
    }
}