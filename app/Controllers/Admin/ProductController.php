<?php
// app/Controllers/Admin/ProductController.php

namespace App\Controllers\Admin;

class ProductController
{
    // =========================================================================
    // INDEX
    // =========================================================================
    public static function index(): void
    {
        admin_auth();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');

        // Ganti query index
        $sql = "SELECT p.id, p.title, p.slug_uuid, p.status, p.created_at,
               pc.title AS category_name,
               g.title  AS gender_name,
               (SELECT GROUP_CONCAT(image) FROM (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 3)) AS images_preview,
               (SELECT GROUP_CONCAT(DISTINCT color) FROM product_variants WHERE product_id = p.id) AS colors,
               (SELECT GROUP_CONCAT(DISTINCT size)  FROM product_variants WHERE product_id = p.id) AS sizes,
               (SELECT COUNT(*) FROM product_variants WHERE product_id = p.id) AS variant_count,
               (SELECT SUM(stock) FROM product_variants WHERE product_id = p.id) AS total_stock
        FROM products p
        LEFT JOIN product_categories pc ON pc.id = p.product_category_id
        LEFT JOIN genders g ON g.id = p.gender_id
        WHERE p.deleted_at IS NULL";
        $params = [];

        if ($search !== '') {
            $sql .= " AND p.title LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $allProducts = $stmt->fetchAll();

        $pagination = paginate($allProducts, 10, $page);

        view('admin/products/index', [
            'products' => $pagination['data'],
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
            SELECT id, title FROM product_categories
            WHERE deleted_at IS NULL AND available = 1
            ORDER BY title ASC
        ")->fetchAll();

        $genders = db()->query("
            SELECT id, title FROM genders
            WHERE deleted_at IS NULL
            ORDER BY title ASC
        ")->fetchAll();

        view('admin/products/create', compact('categories', 'genders'));
    }

    // =========================================================================
    // STORE
    // =========================================================================
    public static function store(): void
    {
        admin_auth();

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int) ($_POST['product_category_id'] ?? 0);
        $genderId = (int) ($_POST['gender_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status = isset($_POST['status']) ? 1 : 0;

        // ── Validasi ──────────────────────────────────────────────────────────
        $errors = [];

        if ($title === '') {
            $errors[] = 'Title wajib diisi.';
        }

        if ($categoryId === 0) {
            $errors[] = 'Category wajib dipilih.';
        }

        if ($genderId === 0) {
            $errors[] = 'Gender wajib dipilih.';
        }

        // Validasi variants
        $variants = self::parseVariantsFromPost();
        if (empty($variants)) {
            $errors[] = 'Minimal 1 variant wajib ditambahkan.';
        }

        foreach ($variants as $i => $v) {
            if ($v['color'] === '') {
                $errors[] = "Variant #" . ($i + 1) . ": Color wajib diisi.";
            }
            if ($v['size'] === '') {
                $errors[] = "Variant #" . ($i + 1) . ": Size wajib diisi.";
            }
            if ($v['price'] <= 0) {
                $errors[] = "Variant #" . ($i + 1) . ": Price harus lebih dari 0.";
            }
            if ($v['stock'] < 0) {
                $errors[] = "Variant #" . ($i + 1) . ": Stock tidak boleh negatif.";
            }
        }

        // Cek duplikat color+size di input
        $variantKeys = array_map(fn($v) => $v['color'] . '_' . $v['size'], $variants);
        if (count($variantKeys) !== count(array_unique($variantKeys))) {
            $errors[] = 'Terdapat duplikat kombinasi Color + Size.';
        }

        if (!empty($errors)) {
            flash('admin_error', implode(' ', $errors));
            redirect('/admin/products/create');
        }

        // ── Validasi category & gender exist ─────────────────────────────────
        $checkCat = db()->prepare("SELECT COUNT(*) FROM product_categories WHERE id = :id AND deleted_at IS NULL AND available = 1");
        $checkCat->execute([':id' => $categoryId]);
        if ((int) $checkCat->fetchColumn() === 0) {
            flash('admin_error', 'Category tidak valid.');
            redirect('/admin/products/create');
        }

        $checkGen = db()->prepare("SELECT COUNT(*) FROM genders WHERE id = :id AND deleted_at IS NULL");
        $checkGen->execute([':id' => $genderId]);
        if ((int) $checkGen->fetchColumn() === 0) {
            flash('admin_error', 'Gender tidak valid.');
            redirect('/admin/products/create');
        }

        // ── Upload images ─────────────────────────────────────────────────────
        $uploadedImages = self::uploadProductImages();
        if (isset($uploadedImages['error'])) {
            flash('admin_error', $uploadedImages['error']);
            redirect('/admin/products/create');
        }

        // ── Insert product ────────────────────────────────────────────────────
        $uuid = uuid_v4();
        $slug = slugify($title);
        $slugUuid = $slug . '-' . $uuid;

        $pdo = db();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                INSERT INTO products (title, slug, slug_uuid, product_category_id, gender_id, description, status, created_at)
                VALUES (:title, :slug, :slug_uuid, :product_category_id, :gender_id, :description, :status, :created_at)
            ");

            $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':slug_uuid' => $slugUuid,
                ':product_category_id' => $categoryId,
                ':gender_id' => $genderId,
                ':description' => $description,
                ':status' => $status,
                ':created_at' => date('Y-m-d H:i:s'),
            ]);

            $productId = (int) $pdo->lastInsertId();

            // ── Insert images ─────────────────────────────────────────────────
            $imgStmt = $pdo->prepare("
                INSERT INTO product_images (product_id, image, sort_order, created_at)
                VALUES (:product_id, :image, :sort_order, :created_at)
            ");

            foreach ($uploadedImages as $order => $image) {
                $imgStmt->execute([
                    ':product_id' => $productId,
                    ':image' => $image,
                    ':sort_order' => $order,
                    ':created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // ── Insert variants ───────────────────────────────────────────────
            $varStmt = $pdo->prepare("
                INSERT INTO product_variants (product_id, color, size, price, stock, sku, created_at)
                VALUES (:product_id, :color, :size, :price, :stock, :sku, :created_at)
            ");

            foreach ($variants as $v) {
                $sku = self::generateSku($title, $v['color'], $v['size']);
                $varStmt->execute([
                    ':product_id' => $productId,
                    ':color' => $v['color'],
                    ':size' => $v['size'],
                    ':price' => $v['price'],
                    ':stock' => $v['stock'],
                    ':sku' => $sku,
                    ':created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $pdo->commit();

        } catch (\Exception $e) {
            $pdo->rollBack();

            // Hapus file yang sudah terupload kalau insert gagal
            foreach ($uploadedImages as $image) {
                $file = ROOT_PATH . '/storage/products/' . $image;
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            flash('admin_error', 'Gagal menyimpan produk: ' . $e->getMessage());
            redirect('/admin/products/create');
        }

        flash('admin_success', 'Produk berhasil ditambahkan.');
        redirect('/admin/products');
    }

    // =========================================================================
    // EDIT
    // =========================================================================
    public static function edit(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            SELECT id, title, slug, slug_uuid, product_category_id, gender_id, description, status
            FROM products
            WHERE slug_uuid = :slug_uuid AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $product = $stmt->fetch();

        if (!$product) {
            flash('admin_error', 'Produk tidak ditemukan.');
            redirect('/admin/products');
        }

        // Ambil images
        $images = db()->prepare("
            SELECT id, image, sort_order FROM product_images
            WHERE product_id = :product_id
            ORDER BY sort_order ASC
        ");
        $images->execute([':product_id' => $product['id']]);
        $productImages = $images->fetchAll();

        // Ambil variants
        $variants = db()->prepare("
            SELECT id, color, size, price, stock, sku
            FROM product_variants
            WHERE product_id = :product_id
            ORDER BY color ASC, size ASC
        ");
        $variants->execute([':product_id' => $product['id']]);
        $productVariants = $variants->fetchAll();

        $categories = db()->query("
            SELECT id, title FROM product_categories
            WHERE deleted_at IS NULL AND available = 1
            ORDER BY title ASC
        ")->fetchAll();

        $genders = db()->query("
            SELECT id, title FROM genders
            WHERE deleted_at IS NULL
            ORDER BY title ASC
        ")->fetchAll();

        // Map current names untuk dropdown
        $currentCategoryName = null;
        foreach ($categories as $c) {
            if ((int) $c['id'] === (int) $product['product_category_id']) {
                $currentCategoryName = $c['title'];
                break;
            }
        }

        $currentGenderName = null;
        foreach ($genders as $g) {
            if ((int) $g['id'] === (int) $product['gender_id']) {
                $currentGenderName = $g['title'];
                break;
            }
        }

        view('admin/products/edit', compact(
            'product',
            'productImages',
            'productVariants',
            'categories',
            'genders',
            'currentCategoryName',
            'currentGenderName'
        ));
    }

    // =========================================================================
    // UPDATE
    // =========================================================================
    public static function update(string $slugUuid): void
    {
        admin_auth();

        $title = trim($_POST['title'] ?? '');
        $categoryId = (int) ($_POST['product_category_id'] ?? 0);
        $genderId = (int) ($_POST['gender_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status = isset($_POST['status']) ? 1 : 0;

        // ── Validasi ──────────────────────────────────────────────────────────
        $errors = [];

        if ($title === '') {
            $errors[] = 'Title wajib diisi.';
        }

        if ($categoryId === 0) {
            $errors[] = 'Category wajib dipilih.';
        }

        if ($genderId === 0) {
            $errors[] = 'Gender wajib dipilih.';
        }

        // Validasi variants
        $variants = self::parseVariantsFromPost();
        if (empty($variants)) {
            $errors[] = 'Minimal 1 variant wajib ada.';
        }

        foreach ($variants as $i => $v) {
            if ($v['color'] === '') {
                $errors[] = "Variant #" . ($i + 1) . ": Color wajib diisi.";
            }
            if ($v['size'] === '') {
                $errors[] = "Variant #" . ($i + 1) . ": Size wajib diisi.";
            }
            if ($v['price'] <= 0) {
                $errors[] = "Variant #" . ($i + 1) . ": Price harus lebih dari 0.";
            }
            if ($v['stock'] < 0) {
                $errors[] = "Variant #" . ($i + 1) . ": Stock tidak boleh negatif.";
            }
        }

        if (!empty($errors)) {
            flash('admin_error', implode(' ', $errors));
            redirect('/admin/products/' . $slugUuid . '/edit');
        }

        // ── Ambil product ─────────────────────────────────────────────────────
        $stmt = db()->prepare("
            SELECT id FROM products
            WHERE slug_uuid = :slug_uuid AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':slug_uuid' => $slugUuid]);
        $product = $stmt->fetch();

        if (!$product) {
            flash('admin_error', 'Produk tidak ditemukan.');
            redirect('/admin/products');
        }

        $productId = $product['id'];

        // ── Upload images baru ────────────────────────────────────────────────
        $uploadedImages = self::uploadProductImages();
        if (isset($uploadedImages['error'])) {
            flash('admin_error', $uploadedImages['error']);
            redirect('/admin/products/' . $slugUuid . '/edit');
        }

        $pdo = db();
        $pdo->beginTransaction();

        try {
            // ── Update product ────────────────────────────────────────────────
            $uuidPart = substr($slugUuid, -36);
            $newSlug = slugify($title);
            $newSlugUuid = $newSlug . '-' . $uuidPart;

            $pdo->prepare("
                UPDATE products
                SET title               = :title,
                    slug                = :slug,
                    slug_uuid           = :slug_uuid,
                    product_category_id = :product_category_id,
                    gender_id           = :gender_id,
                    description         = :description,
                    status              = :status,
                    updated_at          = :updated_at
                WHERE id = :id
            ")->execute([
                        ':title' => $title,
                        ':slug' => $newSlug,
                        ':slug_uuid' => $newSlugUuid,
                        ':product_category_id' => $categoryId,
                        ':gender_id' => $genderId,
                        ':description' => $description,
                        ':status' => $status,
                        ':updated_at' => date('Y-m-d H:i:s'),
                        ':id' => $productId,
                    ]);

            // ── Hapus images yang di-request ──────────────────────────────────
            $removeImageIds = $_POST['remove_images'] ?? [];
            if (!empty($removeImageIds)) {
                foreach ($removeImageIds as $imgId) {
                    $imgId = (int) $imgId;
                    $imgStmt = $pdo->prepare("SELECT image FROM product_images WHERE id = :id AND product_id = :product_id");
                    $imgStmt->execute([':id' => $imgId, ':product_id' => $productId]);
                    $imgRow = $imgStmt->fetch();

                    if ($imgRow) {
                        $file = ROOT_PATH . '/storage/products/' . $imgRow['image'];
                        if (file_exists($file)) {
                            unlink($file);
                        }
                        $pdo->prepare("DELETE FROM product_images WHERE id = :id")->execute([':id' => $imgId]);
                    }
                }
            }

            // ── Insert images baru ────────────────────────────────────────────
            if (!empty($uploadedImages)) {
                // Ambil max sort_order yang ada
                $maxOrder = (int) $pdo->prepare("SELECT COALESCE(MAX(sort_order), -1) FROM product_images WHERE product_id = :id")
                    ->execute([':id' => $productId]);
                $maxOrder = (int) $pdo->query("SELECT COALESCE(MAX(sort_order), -1) FROM product_images WHERE product_id = $productId")->fetchColumn();

                $imgStmt = $pdo->prepare("
                    INSERT INTO product_images (product_id, image, sort_order, created_at)
                    VALUES (:product_id, :image, :sort_order, :created_at)
                ");

                foreach ($uploadedImages as $i => $image) {
                    $imgStmt->execute([
                        ':product_id' => $productId,
                        ':image' => $image,
                        ':sort_order' => $maxOrder + 1 + $i,
                        ':created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            // ── Sync variants ─────────────────────────────────────────────────
            // Hapus semua variant lama, insert ulang
            // Ini approach paling simple & safe untuk dynamic rows
            $pdo->prepare("DELETE FROM product_variants WHERE product_id = :id")->execute([':id' => $productId]);

            $varStmt = $pdo->prepare("
                INSERT INTO product_variants (product_id, color, size, price, stock, sku, created_at)
                VALUES (:product_id, :color, :size, :price, :stock, :sku, :created_at)
            ");

            foreach ($variants as $v) {
                $sku = self::generateSku($title, $v['color'], $v['size']);
                $varStmt->execute([
                    ':product_id' => $productId,
                    ':color' => $v['color'],
                    ':size' => $v['size'],
                    ':price' => $v['price'],
                    ':stock' => $v['stock'],
                    ':sku' => $sku,
                    ':created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $pdo->commit();

        } catch (\Exception $e) {
            $pdo->rollBack();

            // Hapus file baru yang sudah terupload kalau update gagal
            foreach ($uploadedImages as $image) {
                $file = ROOT_PATH . '/storage/products/' . $image;
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            flash('admin_error', 'Gagal mengupdate produk: ' . $e->getMessage());
            redirect('/admin/products/' . $slugUuid . '/edit');
        }

        flash('admin_success', 'Produk berhasil diupdate.');
        redirect('/admin/products');
    }

    // =========================================================================
    // DESTROY (Soft Delete)
    // =========================================================================
    public static function destroy(string $slugUuid): void
    {
        admin_auth();

        $stmt = db()->prepare("
            UPDATE products
            SET deleted_at = :deleted_at
            WHERE slug_uuid = :slug_uuid
              AND deleted_at IS NULL
        ");

        $stmt->execute([
            ':deleted_at' => date('Y-m-d H:i:s'),
            ':slug_uuid' => $slugUuid,
        ]);

        if ($stmt->rowCount() > 0) {
            flash('admin_success', 'Produk berhasil dihapus.');
        } else {
            flash('admin_error', 'Produk tidak ditemukan.');
        }

        redirect('/admin/products');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Parse variants dari $_POST
     * Expects: variants[0][color], variants[0][size], variants[0][price], variants[0][stock]
     */
    private static function parseVariantsFromPost(): array
    {
        $raw = $_POST['variants'] ?? [];
        $variants = [];

        if (!is_array($raw)) {
            return [];
        }

        foreach ($raw as $v) {
            $color = strtolower(trim($v['color'] ?? ''));
            $size = strtoupper(trim($v['size'] ?? ''));
            $price = (int) ($v['price'] ?? 0);
            $stock = (int) ($v['stock'] ?? 0);

            // Skip row kosong
            if ($color === '' && $size === '' && $price === 0) {
                continue;
            }

            $variants[] = compact('color', 'size', 'price', 'stock');
        }

        return $variants;
    }

    /**
     * Upload multiple product images
     * Return array of filenames atau ['error' => '...']
     */
    private static function uploadProductImages(): array
    {
        $uploaded = [];

        if (empty($_FILES['images']['name'][0])) {
            return $uploaded;
        }

        foreach ($_FILES['images']['name'] as $i => $name) {
            if (empty($name)) {
                continue;
            }

            try {
                $uploaded[] = upload_image(
                    [
                        'name' => $name,
                        'tmp_name' => $_FILES['images']['tmp_name'][$i],
                        'size' => $_FILES['images']['size'][$i],
                        'type' => $_FILES['images']['type'][$i],
                        'error' => $_FILES['images']['error'][$i],
                    ],
                    ROOT_PATH . '/storage/products',
                    5 // max 5MB per image
                );
            } catch (\Exception $e) {
                // Hapus yang sudah terupload sebelumnya
                foreach ($uploaded as $file) {
                    $path = ROOT_PATH . '/storage/products/' . $file;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
                return ['error' => $e->getMessage()];
            }
        }

        return $uploaded;
    }

    /**
     * Generate SKU dari title, color, size
     * Format: WLB-{TITLE_ABBR}-{COLOR}-{SIZE}
     * Contoh: WLB-HOODIE-BLK-M
     */
    private static function generateSku(string $title, string $color, string $size): string
    {
        $words = explode(' ', strtoupper($title));
        $titleAbbr = implode('', array_map(fn($w) => substr($w, 0, 3), array_slice($words, 0, 2)));
        $colorAbbr = strtoupper(substr($color, 0, 3));

        return 'WLB-' . $titleAbbr . '-' . $colorAbbr . '-' . strtoupper($size);
    }
}