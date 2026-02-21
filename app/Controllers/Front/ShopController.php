<?php
// app/Controllers/Front/ShopController.php

namespace App\Controllers\Front;

class ShopController
{
    // =========================================================================
    // SHARED HELPERS
    // =========================================================================

    /**
     * Ambil semua produk aktif dari SQLite dengan JOIN lengkap
     * Return flat array siap di-filter di PHP (sama seperti JSON lama)
     */
    private static function fetchBaseProducts(
        ?int $lockedGenderId = null,
        ?int $lockedCategoryId = null
    ): array {
        $sql = "
            SELECT
                p.id,
                p.title,
                p.slug,
                p.slug_uuid,
                p.description,
                p.created_at,
                p.product_category_id,
                p.gender_id,
                pc.slug AS category_slug,
                g.slug  AS gender_slug,
                (SELECT image FROM product_images
                 WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) AS thumbnail,
                (SELECT GROUP_CONCAT(DISTINCT color) FROM product_variants WHERE product_id = p.id) AS colors,
                (SELECT GROUP_CONCAT(DISTINCT size)  FROM product_variants WHERE product_id = p.id) AS sizes,
                MIN(pv.price) AS price_from,
                SUM(pv.stock) AS total_stock
            FROM products p
            LEFT JOIN product_categories pc ON pc.id = p.product_category_id
            LEFT JOIN genders g             ON g.id  = p.gender_id
            LEFT JOIN product_variants pv   ON pv.product_id = p.id
            WHERE p.deleted_at IS NULL
              AND p.status = 1
        ";

        $params = [];

        if ($lockedGenderId !== null) {
            $sql .= " AND p.gender_id = :gender_id";
            $params[':gender_id'] = $lockedGenderId;
        }

        if ($lockedCategoryId !== null) {
            $sql .= " AND p.product_category_id = :category_id";
            $params[':category_id'] = $lockedCategoryId;
        }

        $sql .= " GROUP BY p.id";

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Normalize: colors dan sizes jadi array (sama seperti struktur JSON lama)
        return array_map(function ($p) {
            $p['colors'] = !empty($p['colors'])
                ? array_values(array_filter(array_map('trim', explode(',', $p['colors']))))
                : [];

            $p['sizes'] = !empty($p['sizes'])
                ? array_values(array_filter(array_map('trim', explode(',', $p['sizes']))))
                : [];

            $p['price'] = (int) $p['price_from'];
            $p['stock'] = (int) $p['total_stock'];

            return $p;
        }, $rows);
    }

    /**
     * Filter products — logic sama persis dengan versi JSON lama
     */
    private static function filterProducts(
        array $products,
        array $genderSlugs,
        array $categorySlugs,
        array $sizes,
        array $colors,
        array $priceRanges,
        string $search
    ): array {
        return array_values(array_filter($products, function ($p) use ($genderSlugs, $categorySlugs, $sizes, $colors, $priceRanges, $search) {
            // GENDER
            if ($genderSlugs && !in_array($p['gender_slug'], $genderSlugs, true)) {
                return false;
            }

            // CATEGORY
            if ($categorySlugs && !in_array($p['category_slug'], $categorySlugs, true)) {
                return false;
            }

            // SIZE
            if ($sizes && empty(array_intersect($p['sizes'], $sizes))) {
                return false;
            }

            // COLOR
            if ($colors && empty(array_intersect($p['colors'], $colors))) {
                return false;
            }

            // PRICE RANGE (multi OR)
            if ($priceRanges) {
                $priceMatch = false;
                foreach ($priceRanges as $range) {
                    [$min, $max] = array_pad(explode('-', $range), 2, null);
                    if (
                        ($min === null || $p['price'] >= (int) $min) &&
                        ($max === null || $p['price'] <= (int) $max)
                    ) {
                        $priceMatch = true;
                        break;
                    }
                }
                if (!$priceMatch)
                    return false;
            }

            // SEARCH
            if ($search && stripos($p['title'], $search) === false) {
                return false;
            }

            return true;
        }));
    }

    /**
     * Map product ke contract yang dikirim ke view
     */
    private static function mapForView(array $products): array
    {
        return array_map(fn($p) => [
            'title' => $p['title'],
            'slug_uuid' => $p['slug_uuid'],
            'slug' => $p['slug'],
            'price' => $p['price'],
            'thumbnail' => $p['thumbnail'],
            'gender' => $p['gender_slug'],
            'category' => $p['category_slug'],
            'sizes' => $p['sizes'],
            'colors' => $p['colors'],
            'created_at' => $p['created_at'],
        ], $products);
    }

    /**
     * Sort products — logic sama persis dengan versi JSON lama
     */
    private static function sortProducts(array &$products, string $sort): void
    {
        match ($sort) {
            'newest' => usort($products, fn($a, $b) => strcmp($b['created_at'], $a['created_at'])),
            'oldest' => usort($products, fn($a, $b) => strcmp($a['created_at'], $b['created_at'])),
            'price-asc' => usort($products, fn($a, $b) => $a['price'] <=> $b['price']),
            'price-desc' => usort($products, fn($a, $b) => $b['price'] <=> $a['price']),
            default => null,
        };
    }

    /**
     * Build filter options dari base products
     */
    private static function buildFilterOptions(array $baseProducts): array
    {
        $filterColors = [];
        $filterSizes = [];
        $usedCategorySlugs = [];
        $usedGenderSlugs = [];

        foreach ($baseProducts as $p) {
            foreach ($p['colors'] as $c) {
                $filterColors[$c] = true;
            }
            foreach ($p['sizes'] as $s) {
                $filterSizes[$s] = true;
            }
            if (!empty($p['category_slug'])) {
                $usedCategorySlugs[$p['category_slug']] = true;
            }
            if (!empty($p['gender_slug'])) {
                $usedGenderSlugs[$p['gender_slug']] = true;
            }
        }

        return [
            'colors' => array_keys($filterColors),
            'sizes' => array_keys($filterSizes),
            'categorySlugs' => array_keys($usedCategorySlugs),
            'genderSlugs' => array_keys($usedGenderSlugs),
        ];
    }

    private static function priceRangeOptions(): array
    {
        return [
            '0-100000' => 'Under Rp 100k',
            '100000-300000' => 'Rp 100k – 300k',
            '300000-999999999' => 'Above Rp 300k',
        ];
    }

    private static function parseQs(): array
    {
        $qs = $_GET;
        return [
            'genderSlugs' => array_values((array) ($qs['gender'] ?? [])),
            'categorySlugs' => array_values((array) ($qs['category'] ?? [])),
            'sizes' => array_values((array) ($qs['size'] ?? [])),
            'colors' => array_values((array) ($qs['color'] ?? [])),
            'priceRanges' => array_values((array) ($qs['price'] ?? [])),
            'search' => trim($qs['q'] ?? ''),
            'sortBy' => array_values((array) ($qs['sort'] ?? [])),
        ];
    }

    // =========================================================================
    // INDEX — /shop
    // =========================================================================
    public static function index(): void
    {
        $qs = self::parseQs();

        $baseProducts = self::fetchBaseProducts();
        $filteredProducts = self::filterProducts(
            $baseProducts,
            $qs['genderSlugs'],
            $qs['categorySlugs'],
            $qs['sizes'],
            $qs['colors'],
            $qs['priceRanges'],
            $qs['search']
        );

        $productsForView = self::mapForView($filteredProducts);
        self::sortProducts($productsForView, $qs['sortBy'][0] ?? '');

        $opts = self::buildFilterOptions($baseProducts);

        // Ambil filter categories & genders dari SQLite berdasarkan yang dipakai
        $filterCategories = [];
        if (!empty($opts['categorySlugs'])) {
            $placeholders = implode(',', array_fill(0, count($opts['categorySlugs']), '?'));
            $stmt = db()->prepare("
                SELECT id, title, slug, icon, description
                FROM product_categories
                WHERE slug IN ($placeholders)
                  AND deleted_at IS NULL AND available = 1
                ORDER BY title ASC
            ");
            $stmt->execute($opts['categorySlugs']);
            $filterCategories = $stmt->fetchAll();
        }

        $filterGenders = [];
        if (!empty($opts['genderSlugs'])) {
            $placeholders = implode(',', array_fill(0, count($opts['genderSlugs']), '?'));
            $stmt = db()->prepare("
                SELECT id, title, slug FROM genders
                WHERE slug IN ($placeholders) AND deleted_at IS NULL
                ORDER BY title ASC
            ");
            $stmt->execute($opts['genderSlugs']);
            $filterGenders = $stmt->fetchAll();
        }

        view('front/shop/index', [
            'products' => $productsForView,
            'filterGenders' => $filterGenders,
            'filterCategories' => $filterCategories,
            'filterColors' => $opts['colors'],
            'filterSizes' => $opts['sizes'],
            'priceRanges' => self::priceRangeOptions(),
            'activeFilters' => [
                'gender' => $qs['genderSlugs'],
                'category' => $qs['categorySlugs'],
                'size' => $qs['sizes'],
                'color' => $qs['colors'],
                'price' => $qs['priceRanges'],
                'q' => $qs['search'],
                'sort' => $qs['sortBy'],
            ],
            'filterContext' => [],
            'baseUrl' => '/shop',
        ]);
    }

    // =========================================================================
    // BY GENDER — /shop/{gender}
    // =========================================================================
    public static function byGender(string $genderSlug): void
    {
        $stmt = db()->prepare("SELECT id, title, slug, banner FROM genders WHERE slug = :slug AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([':slug' => $genderSlug]);
        $gender = $stmt->fetch();

        if (!$gender) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $qs = self::parseQs();

        $baseProducts = self::fetchBaseProducts(lockedGenderId: (int) $gender['id']);
        $filteredProducts = self::filterProducts(
            $baseProducts,
            [],
            $qs['categorySlugs'],
            $qs['sizes'],
            $qs['colors'],
            $qs['priceRanges'],
            $qs['search']
        );

        $productsForView = self::mapForView($filteredProducts);
        self::sortProducts($productsForView, $qs['sortBy'][0] ?? '');

        $opts = self::buildFilterOptions($baseProducts);

        $filterCategories = [];
        if (!empty($opts['categorySlugs'])) {
            $placeholders = implode(',', array_fill(0, count($opts['categorySlugs']), '?'));
            $stmt = db()->prepare("
                SELECT id, title, slug FROM product_categories
                WHERE slug IN ($placeholders) AND deleted_at IS NULL AND available = 1
                ORDER BY title ASC
            ");
            $stmt->execute($opts['categorySlugs']);
            $filterCategories = $stmt->fetchAll();
        }

        view('front/shop/shop_gender', [
            'gender' => $gender,
            'products' => $productsForView,
            'filterCategories' => $filterCategories,
            'filterColors' => $opts['colors'],
            'filterSizes' => $opts['sizes'],
            'priceRanges' => self::priceRangeOptions(),
            'activeFilters' => [
                'category' => $qs['categorySlugs'],
                'size' => $qs['sizes'],
                'color' => $qs['colors'],
                'price' => $qs['priceRanges'],
                'q' => $qs['search'],
                'sort' => $qs['sortBy'],
            ],
            'filterContext' => ['lockedGender' => $gender],
            'filterGenders' => [],
            'baseUrl' => '/shop/' . $gender['slug'],
        ]);
    }

    // =========================================================================
    // BY CATEGORY ONLY — /shop/category/{category}
    // =========================================================================
    public static function byCategoryOnly(string $categorySlug): void
    {
        $stmt = db()->prepare("SELECT id, title, slug FROM product_categories WHERE slug = :slug AND deleted_at IS NULL AND available = 1 LIMIT 1");
        $stmt->execute([':slug' => $categorySlug]);
        $category = $stmt->fetch();

        if (!$category) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $qs = self::parseQs();

        $baseProducts = self::fetchBaseProducts(lockedCategoryId: (int) $category['id']);
        $filteredProducts = self::filterProducts(
            $baseProducts,
            $qs['genderSlugs'],
            [],
            $qs['sizes'],
            $qs['colors'],
            $qs['priceRanges'],
            $qs['search']
        );

        $productsForView = self::mapForView($filteredProducts);
        self::sortProducts($productsForView, $qs['sortBy'][0] ?? '');

        $opts = self::buildFilterOptions($baseProducts);

        $filterGenders = [];
        if (!empty($opts['genderSlugs'])) {
            $placeholders = implode(',', array_fill(0, count($opts['genderSlugs']), '?'));
            $stmt = db()->prepare("
                SELECT id, title, slug FROM genders
                WHERE slug IN ($placeholders) AND deleted_at IS NULL
                ORDER BY title ASC
            ");
            $stmt->execute($opts['genderSlugs']);
            $filterGenders = $stmt->fetchAll();
        }

        view('front/shop/shop_category_only', [
            'category' => $category,
            'products' => $productsForView,
            'filterGenders' => $filterGenders,
            'filterColors' => $opts['colors'],
            'filterSizes' => $opts['sizes'],
            'priceRanges' => self::priceRangeOptions(),
            'activeFilters' => [
                'gender' => $qs['genderSlugs'],
                'size' => $qs['sizes'],
                'color' => $qs['colors'],
                'price' => $qs['priceRanges'],
                'q' => $qs['search'],
                'sort' => $qs['sortBy'],
            ],
            'filterContext' => ['lockedCategory' => $category],
            'filterCategories' => [],
            'baseUrl' => '/shop/category/' . $category['slug'],
        ]);
    }

    // =========================================================================
    // BY CATEGORY — /shop/{gender}/{category}
    // =========================================================================
    public static function byCategory(string $genderSlug, string $categorySlug): void
    {
        $stmtG = db()->prepare("SELECT id, title, slug FROM genders WHERE slug = :slug AND deleted_at IS NULL LIMIT 1");
        $stmtG->execute([':slug' => $genderSlug]);
        $gender = $stmtG->fetch();

        if (!$gender) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $stmtC = db()->prepare("SELECT id, title, slug FROM product_categories WHERE slug = :slug AND deleted_at IS NULL AND available = 1 LIMIT 1");
        $stmtC->execute([':slug' => $categorySlug]);
        $category = $stmtC->fetch();

        if (!$category) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $qs = self::parseQs();

        $baseProducts = self::fetchBaseProducts(
            lockedGenderId: (int) $gender['id'],
            lockedCategoryId: (int) $category['id']
        );
        $filteredProducts = self::filterProducts(
            $baseProducts,
            [],
            [],
            $qs['sizes'],
            $qs['colors'],
            $qs['priceRanges'],
            $qs['search']
        );

        $productsForView = self::mapForView($filteredProducts);
        self::sortProducts($productsForView, $qs['sortBy'][0] ?? '');

        $opts = self::buildFilterOptions($baseProducts);

        view('front/shop/shop_category', [
            'gender' => $gender,
            'category' => $category,
            'products' => $productsForView,
            'filterColors' => $opts['colors'],
            'filterSizes' => $opts['sizes'],
            'priceRanges' => self::priceRangeOptions(),
            'activeFilters' => [
                'size' => $qs['sizes'],
                'color' => $qs['colors'],
                'price' => $qs['priceRanges'],
                'q' => $qs['search'],
                'sort' => $qs['sortBy'],
            ],
            'filterContext' => ['lockedGender' => $gender, 'lockedCategory' => $category],
            'filterGenders' => [],
            'filterCategories' => [],
            'baseUrl' => '/shop/' . $gender['slug'] . '/' . $category['slug'],
        ]);
    }

    // =========================================================================
    // SHOW — /shop/{gender}/{category}/{product}
    // =========================================================================
    public static function show(string $genderSlug, string $categorySlug, string $productSlugUuid): void
    {
        $stmtG = db()->prepare("SELECT id, title, slug FROM genders WHERE slug = :slug AND deleted_at IS NULL LIMIT 1");
        $stmtG->execute([':slug' => $genderSlug]);
        $gender = $stmtG->fetch();

        if (!$gender) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $stmtC = db()->prepare("SELECT id, title, slug FROM product_categories WHERE slug = :slug AND deleted_at IS NULL AND available = 1 LIMIT 1");
        $stmtC->execute([':slug' => $categorySlug]);
        $category = $stmtC->fetch();

        if (!$category) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        // Ambil product
        $stmtP = db()->prepare("
            SELECT p.id, p.title, p.slug, p.slug_uuid, p.description, p.created_at,
                   p.product_category_id, p.gender_id
            FROM products p
            WHERE p.slug_uuid          = :slug_uuid
              AND p.gender_id          = :gender_id
              AND p.product_category_id = :category_id
              AND p.deleted_at IS NULL
              AND p.status = 1
            LIMIT 1
        ");
        $stmtP->execute([
            ':slug_uuid' => $productSlugUuid,
            ':gender_id' => $gender['id'],
            ':category_id' => $category['id'],
        ]);
        $product = $stmtP->fetch();

        if (!$product) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        // Ambil images
        $imgStmt = db()->prepare("SELECT image FROM product_images WHERE product_id = :id ORDER BY sort_order ASC");
        $imgStmt->execute([':id' => $product['id']]);
        $images = array_column($imgStmt->fetchAll(), 'image');

        // Ambil variants
        $varStmt = db()->prepare("SELECT color, size, price, stock FROM product_variants WHERE product_id = :id ORDER BY color ASC, size ASC");
        $varStmt->execute([':id' => $product['id']]);
        $variants = $varStmt->fetchAll();

        // Build colors, sizes, price_from, total_stock dari variants
        $colors = array_values(array_unique(array_column($variants, 'color')));
        $sizes = array_values(array_unique(array_column($variants, 'size')));
        $priceFrom = !empty($variants) ? min(array_column($variants, 'price')) : 0;
        $stock = array_sum(array_column($variants, 'stock'));

        $productForView = [
            'id' => $product['id'],
            'title' => $product['title'],
            'slug_uuid' => $product['slug_uuid'],
            'slug' => $product['slug'],
            'price' => (int) $priceFrom,
            'description' => $product['description'],
            'images' => $images,
            'sizes' => $sizes,
            'colors' => $colors,
            'stock' => (int) $stock,
            'variants' => $variants, // untuk variant picker di JS
            'gender' => ['title' => $gender['title'], 'slug' => $gender['slug']],
            'category' => ['title' => $category['title'], 'slug' => $category['slug']],
        ];

        // Related products
        $relStmt = db()->prepare("
            SELECT p.title, p.slug, p.slug_uuid,
                   MIN(pv.price) AS price_from,
                   (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) AS thumbnail
            FROM products p
            LEFT JOIN product_variants pv ON pv.product_id = p.id
            WHERE p.slug_uuid          != :slug_uuid
              AND p.gender_id          = :gender_id
              AND p.product_category_id = :category_id
              AND p.deleted_at IS NULL
              AND p.status = 1
            GROUP BY p.id
            ORDER BY p.created_at DESC
            LIMIT 4
        ");
        $relStmt->execute([
            ':slug_uuid' => $productSlugUuid,
            ':gender_id' => $gender['id'],
            ':category_id' => $category['id'],
        ]);

        $relatedForView = array_map(fn($r) => [
            'title' => $r['title'],
            'slug' => $r['slug'],
            'slug_uuid' => $r['slug_uuid'],
            'price' => (int) $r['price_from'],
            'thumbnail' => $r['thumbnail'],
            'gender' => $gender['slug'],
            'category' => $category['slug'],
        ], $relStmt->fetchAll());

        // Back URL
        $backUrl = '/shop/' . $gender['slug'];
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $ref = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
            if (preg_match('#^/shop/' . preg_quote($gender['slug'], '#') . '$#', $ref)) {
                $backUrl = $ref;
            }
        }

        view('front/shop/product_show', [
            'product' => $productForView,
            'gender' => $gender,
            'category' => $category,
            'relatedProducts' => $relatedForView,
            'backUrl' => $backUrl,
        ]);
    }
}