<?php

namespace App\Controllers\Front;

class ShopController
{
    public static function index()
    {
        // =========================
        // LOAD DATA
        // =========================
        $genders = json_read('genders.json') ?? [];
        $categories = json_read('product-categories.json') ?? [];
        $products = json_read('products.json') ?? [];

        // =========================
        // NORMALIZE QUERY STRING
        // =========================
        $qs = $_GET;

        $genderSlugs = array_values((array) ($qs['gender'] ?? []));
        $categorySlugs = array_values((array) ($qs['category'] ?? []));
        $sizes = array_values((array) ($qs['size'] ?? []));
        $colors = array_values((array) ($qs['color'] ?? []));
        $priceRanges = array_values((array) ($qs['price'] ?? []));
        $search = trim($qs['q'] ?? '');
        $sortBy = array_values((array) ($qs['sort'] ?? []));

        // =========================
        // BUILD MAPS
        // =========================
        $genderSlugToId = [];
        $categorySlugToId = [];
        $categoryIdToSlug = [];

        foreach ($genders as $g) {
            if (empty($g['deleted_at'])) {
                $genderSlugToId[$g['slug']] = $g['id'];
            }
        }

        foreach ($categories as $c) {
            if (empty($c['deleted_at'])) {
                $categorySlugToId[$c['slug']] = $c['id'];
                $categoryIdToSlug[$c['id']] = $c['slug'];
            }
        }

        // =========================
        // GET BASE PRODUCTS (ALL ACTIVE)
        // =========================
        $baseProducts = array_values(array_filter($products, function ($p) {
            return empty($p['deleted_at']) && !empty($p['status']);
        }));

        // =========================
        // FILTER PRODUCTS
        // =========================
        $filteredProducts = array_values(array_filter($baseProducts, function ($p) use ($genderSlugs, $genderSlugToId, $categorySlugs, $categorySlugToId, $sizes, $colors, $priceRanges, $search) {

            // GENDER
            if ($genderSlugs) {
                $allowedGenderIds = array_intersect_key(
                    $genderSlugToId,
                    array_flip($genderSlugs)
                );

                if (!in_array($p['gender_id'] ?? null, $allowedGenderIds, true)) {
                    return false;
                }
            }

            // CATEGORY
            if ($categorySlugs) {
                $allowedCategoryIds = array_intersect_key(
                    $categorySlugToId,
                    array_flip($categorySlugs)
                );

                if (!in_array($p['product_category_id'], $allowedCategoryIds, true)) {
                    return false;
                }
            }

            // SIZE
            if ($sizes && empty(array_intersect($p['sizes'] ?? [], $sizes))) {
                return false;
            }

            // COLOR
            if ($colors && empty(array_intersect($p['colors'] ?? [], $colors))) {
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

        // =========================
        // MAP PRODUCTS FOR VIEW
        // =========================
        $productsForView = array_map(function ($p) use ($categoryIdToSlug, $genders) {
            $genderSlug = null;
            foreach ($genders as $g) {
                if ($g['id'] === ($p['gender_id'] ?? null)) {
                    $genderSlug = $g['slug'];
                    break;
                }
            }

            return [
                'title' => $p['title'],
                'slug_uuid' => $p['slug_uuid'],
                'slug' => $p['slug'],
                'price' => $p['price'],
                'thumbnail' => $p['images'][0] ?? null,
                'gender' => $genderSlug,
                'category' => $categoryIdToSlug[$p['product_category_id']] ?? null,
                'sizes' => $p['sizes'] ?? [],
                'colors' => $p['colors'] ?? [],
                'created_at' => $p['created_at'] ?? '',
            ];
        }, $filteredProducts);

        // =========================
        // BUILD FILTER OPTIONS (DYNAMIC)
        // =========================
        $filterColors = [];
        $filterSizes = [];
        $usedCategorySlugs = [];
        $usedGenderIds = [];

        foreach ($baseProducts as $p) {
            // Colors
            foreach ($p['colors'] ?? [] as $c) {
                $filterColors[$c] = true;
            }

            // Sizes
            foreach ($p['sizes'] ?? [] as $s) {
                $filterSizes[$s] = true;
            }

            // Categories
            if (isset($categoryIdToSlug[$p['product_category_id']])) {
                $usedCategorySlugs[$categoryIdToSlug[$p['product_category_id']]] = true;
            }

            // Genders
            if (!empty($p['gender_id'])) {
                $usedGenderIds[$p['gender_id']] = true;
            }
        }

        $filterCategories = array_values(array_filter($categories, function ($c) use ($usedCategorySlugs) {
            return
                empty($c['deleted_at']) &&
                !empty($c['available']) &&
                isset($usedCategorySlugs[$c['slug']]);
        }));

        $filterGenders = array_values(array_filter($genders, function ($g) use ($usedGenderIds) {
            return
                empty($g['deleted_at']) &&
                isset($usedGenderIds[$g['id']]);
        }));

        $priceRangesOptions = [
            '0-100000' => 'Under Rp 100k',
            '100000-300000' => 'Rp 100k – 300k',
            '300000-999999999' => 'Above Rp 300k',
        ];

        // =========================
        // SORT PRODUCTS
        // =========================
        $sort = $sortBy[0] ?? '';

        if ($sort === 'newest') {
            usort($productsForView, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        } elseif ($sort === 'oldest') {
            usort($productsForView, fn($a, $b) => strcmp($a['created_at'], $b['created_at']));
        } elseif ($sort === 'price-asc') {
            usort($productsForView, fn($a, $b) => $a['price'] <=> $b['price']);
        } elseif ($sort === 'price-desc') {
            usort($productsForView, fn($a, $b) => $b['price'] <=> $a['price']);
        }

        // =========================
        // RETURN VIEW
        // =========================
        return view('front/shop/index', [
            'products' => $productsForView,

            // Filter Options
            'filterGenders' => $filterGenders,
            'filterCategories' => $filterCategories,
            'filterColors' => array_keys($filterColors),
            'filterSizes' => array_keys($filterSizes),
            'priceRanges' => $priceRangesOptions,

            // Active Filters
            'activeFilters' => [
                'gender' => $genderSlugs,
                'category' => $categorySlugs,
                'size' => $sizes,
                'color' => $colors,
                'price' => $priceRanges,
                'q' => $search,
                'sort' => $sortBy,
            ],

            // Filter Context
            'filterContext' => [],
            'baseUrl' => '/shop',
        ]);
    }

    public static function byGender(string $genderSlug)
    {
        // =========================
        // LOAD DATA
        // =========================
        $genders = json_read('genders.json') ?? [];
        $categories = json_read('product-categories.json') ?? [];
        $products = json_read('products.json') ?? [];

        // =========================
        // RESOLVE GENDER
        // =========================
        $gender = null;
        foreach ($genders as $g) {
            if ($g['slug'] === $genderSlug && empty($g['deleted_at'])) {
                $gender = $g;
                break;
            }
        }

        if (!$gender) {
            http_response_code(404);
            return view('errors/404');
        }

        $genderId = $gender['id'];

        // =========================
        // NORMALIZE QUERY STRING
        // =========================
        $qs = $_GET;

        $categorySlugs = array_values((array) ($qs['category'] ?? []));
        $sizes = array_values((array) ($qs['size'] ?? []));
        $colors = array_values((array) ($qs['color'] ?? []));
        $priceRanges = array_values((array) ($qs['price'] ?? []));
        $search = trim($qs['q'] ?? '');
        $sortBy = array_values((array) ($qs['sort'] ?? []));

        // =========================
        // BUILD CATEGORY MAPS
        // =========================
        $categorySlugToId = [];
        $categoryIdToSlug = [];

        foreach ($categories as $c) {
            if (empty($c['deleted_at'])) {
                $categorySlugToId[$c['slug']] = $c['id'];
                $categoryIdToSlug[$c['id']] = $c['slug'];
            }
        }

        // =========================
        // GET BASE PRODUCTS (GENDER LOCKED)
        // =========================
        $baseProducts = array_values(array_filter($products, function ($p) use ($genderId) {
            return
                ($p['gender_id'] ?? null) === $genderId &&
                empty($p['deleted_at']) &&
                !empty($p['status']);
        }));

        // =========================
        // FILTER PRODUCTS
        // =========================
        $filteredProducts = array_values(array_filter($baseProducts, function ($p) use ($categorySlugs, $categorySlugToId, $sizes, $colors, $priceRanges, $search) {

            // CATEGORY
            if ($categorySlugs) {
                $allowedCategoryIds = array_intersect_key(
                    $categorySlugToId,
                    array_flip($categorySlugs)
                );

                if (!in_array($p['product_category_id'], $allowedCategoryIds, true)) {
                    return false;
                }
            }

            // SIZE
            if ($sizes && empty(array_intersect($p['sizes'] ?? [], $sizes))) {
                return false;
            }

            // COLOR
            if ($colors && empty(array_intersect($p['colors'] ?? [], $colors))) {
                return false;
            }

            // PRICE RANGE
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

        // =========================
        // MAP PRODUCTS FOR VIEW
        // =========================
        $productsForView = array_map(function ($p) use ($categoryIdToSlug, $genderSlug) {
            return [
                'title' => $p['title'],
                'slug_uuid' => $p['slug_uuid'],
                'slug' => $p['slug'],
                'price' => $p['price'],
                'thumbnail' => $p['images'][0] ?? null,
                'gender' => $genderSlug,
                'category' => $categoryIdToSlug[$p['product_category_id']] ?? null,
                'sizes' => $p['sizes'] ?? [],
                'colors' => $p['colors'] ?? [],
                'created_at' => $p['created_at'] ?? '',
            ];
        }, $filteredProducts);

        // =========================
        // BUILD FILTER OPTIONS
        // =========================
        $filterColors = [];
        $filterSizes = [];
        $usedCategorySlugs = [];

        foreach ($baseProducts as $p) {
            foreach ($p['colors'] ?? [] as $c) {
                $filterColors[$c] = true;
            }

            foreach ($p['sizes'] ?? [] as $s) {
                $filterSizes[$s] = true;
            }

            if (isset($categoryIdToSlug[$p['product_category_id']])) {
                $usedCategorySlugs[$categoryIdToSlug[$p['product_category_id']]] = true;
            }
        }

        $filterCategories = array_values(array_filter($categories, function ($c) use ($usedCategorySlugs) {
            return
                empty($c['deleted_at']) &&
                !empty($c['available']) &&
                isset($usedCategorySlugs[$c['slug']]);
        }));

        $priceRangesOptions = [
            '0-100000' => 'Under Rp 100k',
            '100000-300000' => 'Rp 100k – 300k',
            '300000-999999999' => 'Above Rp 300k',
        ];

        // =========================
        // SORT
        // =========================
        $sort = $sortBy[0] ?? '';

        if ($sort === 'newest') {
            usort($productsForView, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        } elseif ($sort === 'oldest') {
            usort($productsForView, fn($a, $b) => strcmp($a['created_at'], $b['created_at']));
        } elseif ($sort === 'price-asc') {
            usort($productsForView, fn($a, $b) => $a['price'] <=> $b['price']);
        } elseif ($sort === 'price-desc') {
            usort($productsForView, fn($a, $b) => $b['price'] <=> $a['price']);
        }

        // =========================
        // RETURN VIEW
        // =========================
        return view('front/shop/shop_gender', [
            'gender' => $gender,
            'products' => $productsForView,

            // Filter Options
            'filterCategories' => $filterCategories,
            'filterColors' => array_keys($filterColors),
            'filterSizes' => array_keys($filterSizes),
            'priceRanges' => $priceRangesOptions,

            // Active Filters
            'activeFilters' => [
                'category' => $categorySlugs,
                'size' => $sizes,
                'color' => $colors,
                'price' => $priceRanges,
                'q' => $search,
                'sort' => $sortBy,
            ],

            // Filter Context
            'filterContext' => [
                'lockedGender' => $gender,
            ],
            'filterGenders' => [], // Hidden
            'baseUrl' => '/shop/' . $gender['slug'],
        ]);
    }

    public static function byCategoryOnly(string $categorySlug)
    {
        // =========================
        // LOAD DATA
        // =========================
        $categories = json_read('product-categories.json') ?? [];
        $products = json_read('products.json') ?? [];
        $genders = json_read('genders.json') ?? [];

        // =========================
        // RESOLVE CATEGORY
        // =========================
        $category = null;
        foreach ($categories as $c) {
            if (
                $c['slug'] === $categorySlug &&
                empty($c['deleted_at']) &&
                !empty($c['available'])
            ) {
                $category = $c;
                break;
            }
        }

        if (!$category) {
            http_response_code(404);
            return view('errors/404');
        }

        $categoryId = $category['id'];

        // =========================
        // NORMALIZE QUERY STRING
        // =========================
        $qs = $_GET;

        $genderSlugs = array_values((array) ($qs['gender'] ?? []));
        $sizes = array_values((array) ($qs['size'] ?? []));
        $colors = array_values((array) ($qs['color'] ?? []));
        $priceRanges = array_values((array) ($qs['price'] ?? []));
        $search = trim($qs['q'] ?? '');
        $sortBy = array_values((array) ($qs['sort'] ?? []));

        // =========================
        // BUILD GENDER MAPS
        // =========================
        $genderSlugToId = [];
        $genderIdToSlug = [];

        foreach ($genders as $g) {
            if (empty($g['deleted_at'])) {
                $genderSlugToId[$g['slug']] = $g['id'];
                $genderIdToSlug[$g['id']] = $g['slug'];
            }
        }

        // =========================
        // GET BASE PRODUCTS (CATEGORY LOCKED)
        // =========================
        $baseProducts = array_values(array_filter($products, function ($p) use ($categoryId) {
            return
                ($p['product_category_id'] ?? null) === $categoryId &&
                empty($p['deleted_at']) &&
                !empty($p['status']);
        }));

        // =========================
        // FILTER PRODUCTS
        // =========================
        $filteredProducts = array_values(array_filter($baseProducts, function ($p) use ($genderSlugs, $genderSlugToId, $sizes, $colors, $priceRanges, $search) {

            // GENDER (optional filter)
            if ($genderSlugs) {
                $allowedGenderIds = array_intersect_key(
                    $genderSlugToId,
                    array_flip($genderSlugs)
                );

                if (!in_array($p['gender_id'] ?? null, $allowedGenderIds, true)) {
                    return false;
                }
            }

            // SIZE
            if ($sizes && empty(array_intersect($p['sizes'] ?? [], $sizes))) {
                return false;
            }

            // COLOR
            if ($colors && empty(array_intersect($p['colors'] ?? [], $colors))) {
                return false;
            }

            // PRICE RANGE
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

        // =========================
        // MAP PRODUCTS FOR VIEW
        // =========================
        $productsForView = array_map(function ($p) use ($genderIdToSlug, $categorySlug) {
            $genderSlug = $genderIdToSlug[$p['gender_id'] ?? null] ?? null;

            return [
                'title' => $p['title'],
                'slug_uuid' => $p['slug_uuid'],
                'slug' => $p['slug'],
                'price' => $p['price'],
                'thumbnail' => $p['images'][0] ?? null,
                'gender' => $genderSlug,
                'category' => $categorySlug,
                'sizes' => $p['sizes'] ?? [],
                'colors' => $p['colors'] ?? [],
                'created_at' => $p['created_at'] ?? '',
            ];
        }, $filteredProducts);

        // =========================
        // BUILD FILTER OPTIONS
        // =========================
        $filterColors = [];
        $filterSizes = [];
        $usedGenderIds = [];

        foreach ($baseProducts as $p) {
            foreach ($p['colors'] ?? [] as $c) {
                $filterColors[$c] = true;
            }

            foreach ($p['sizes'] ?? [] as $s) {
                $filterSizes[$s] = true;
            }

            if (!empty($p['gender_id'])) {
                $usedGenderIds[$p['gender_id']] = true;
            }
        }

        $filterGenders = array_values(array_filter($genders, function ($g) use ($usedGenderIds) {
            return
                empty($g['deleted_at']) &&
                isset($usedGenderIds[$g['id']]);
        }));

        $priceRangesOptions = [
            '0-100000' => 'Under Rp 100k',
            '100000-300000' => 'Rp 100k – 300k',
            '300000-999999999' => 'Above Rp 300k',
        ];

        // =========================
        // SORT
        // =========================
        $sort = $sortBy[0] ?? '';

        if ($sort === 'newest') {
            usort($productsForView, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        } elseif ($sort === 'oldest') {
            usort($productsForView, fn($a, $b) => strcmp($a['created_at'], $b['created_at']));
        } elseif ($sort === 'price-asc') {
            usort($productsForView, fn($a, $b) => $a['price'] <=> $b['price']);
        } elseif ($sort === 'price-desc') {
            usort($productsForView, fn($a, $b) => $b['price'] <=> $a['price']);
        }

        // =========================
        // RETURN VIEW
        // =========================
        return view('front/shop/shop_category_only', [
            'category' => $category,
            'products' => $productsForView,

            // Filter Options
            'filterGenders' => $filterGenders,
            'filterColors' => array_keys($filterColors),
            'filterSizes' => array_keys($filterSizes),
            'priceRanges' => $priceRangesOptions,

            // Active Filters
            'activeFilters' => [
                'gender' => $genderSlugs,
                'size' => $sizes,
                'color' => $colors,
                'price' => $priceRanges,
                'q' => $search,
                'sort' => $sortBy,
            ],

            // Filter Context
            'filterContext' => [
                'lockedCategory' => $category,
            ],
            'filterCategories' => [], // Hidden (category locked)
            'baseUrl' => '/shop/category/' . $category['slug'],
        ]);
    }

    public static function byCategory(string $genderSlug, string $categorySlug)
    {
        // =========================
        // LOAD DATA
        // =========================
        $genders = json_read('genders.json') ?? [];
        $categories = json_read('product-categories.json') ?? [];
        $products = json_read('products.json') ?? [];

        // =========================
        // RESOLVE GENDER
        // =========================
        $gender = null;
        foreach ($genders as $g) {
            if ($g['slug'] === $genderSlug && empty($g['deleted_at'])) {
                $gender = $g;
                break;
            }
        }

        if (!$gender) {
            http_response_code(404);
            return view('errors/404');
        }

        // =========================
        // RESOLVE CATEGORY
        // =========================
        $category = null;
        foreach ($categories as $c) {
            if (
                $c['slug'] === $categorySlug &&
                empty($c['deleted_at']) &&
                !empty($c['available'])
            ) {
                $category = $c;
                break;
            }
        }

        if (!$category) {
            http_response_code(404);
            return view('errors/404');
        }

        $genderId = $gender['id'];
        $categoryId = $category['id'];

        // =========================
        // NORMALIZE QUERY STRING
        // =========================
        $qs = $_GET;

        $sizes = array_values((array) ($qs['size'] ?? []));
        $colors = array_values((array) ($qs['color'] ?? []));
        $priceRanges = array_values((array) ($qs['price'] ?? []));
        $search = trim($qs['q'] ?? '');
        $sortBy = array_values((array) ($qs['sort'] ?? []));

        // =========================
        // GET BASE PRODUCTS (DOUBLE LOCKED)
        // =========================
        $baseProducts = array_values(array_filter($products, function ($p) use ($genderId, $categoryId) {
            return
                ($p['gender_id'] ?? null) === $genderId &&
                ($p['product_category_id'] ?? null) === $categoryId &&
                empty($p['deleted_at']) &&
                !empty($p['status']);
        }));

        // =========================
        // FILTER PRODUCTS
        // =========================
        $filteredProducts = array_values(array_filter($baseProducts, function ($p) use ($sizes, $colors, $priceRanges, $search) {

            if ($sizes && empty(array_intersect($p['sizes'] ?? [], $sizes))) {
                return false;
            }

            if ($colors && empty(array_intersect($p['colors'] ?? [], $colors))) {
                return false;
            }

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

            if ($search && stripos($p['title'], $search) === false) {
                return false;
            }

            return true;
        }));

        // =========================
        // MAP PRODUCTS FOR VIEW
        // =========================
        $categoryIdToSlug = [];
        foreach ($categories as $c) {
            if (empty($c['deleted_at'])) {
                $categoryIdToSlug[$c['id']] = $c['slug'];
            }
        }

        $productsForView = array_map(function ($p) use ($categoryIdToSlug, $genderSlug) {
            return [
                'title' => $p['title'],
                'slug_uuid' => $p['slug_uuid'],
                'slug' => $p['slug'],
                'price' => $p['price'],
                'thumbnail' => $p['images'][0] ?? null,
                'gender' => $genderSlug,
                'category' => $categoryIdToSlug[$p['product_category_id']] ?? null,
                'sizes' => $p['sizes'] ?? [],
                'colors' => $p['colors'] ?? [],
                'created_at' => $p['created_at'] ?? '',
            ];
        }, $filteredProducts);

        // =========================
        // BUILD FILTER OPTIONS
        // =========================
        $filterColors = [];
        $filterSizes = [];

        foreach ($baseProducts as $p) {
            foreach ($p['colors'] ?? [] as $c) {
                $filterColors[$c] = true;
            }

            foreach ($p['sizes'] ?? [] as $s) {
                $filterSizes[$s] = true;
            }
        }

        $priceRangesOptions = [
            '0-100000' => 'Under Rp 100k',
            '100000-300000' => 'Rp 100k – 300k',
            '300000-999999999' => 'Above Rp 300k',
        ];

        // =========================
        // SORT
        // =========================
        $sort = $sortBy[0] ?? '';

        if ($sort === 'newest') {
            usort($productsForView, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        } elseif ($sort === 'oldest') {
            usort($productsForView, fn($a, $b) => strcmp($a['created_at'], $b['created_at']));
        } elseif ($sort === 'price-asc') {
            usort($productsForView, fn($a, $b) => $a['price'] <=> $b['price']);
        } elseif ($sort === 'price-desc') {
            usort($productsForView, fn($a, $b) => $b['price'] <=> $a['price']);
        }

        // =========================
        // RETURN VIEW
        // =========================
        return view('front/shop/shop_category', [
            'gender' => $gender,
            'category' => $category,
            'products' => $productsForView,

            // Filter Options
            'filterColors' => array_keys($filterColors),
            'filterSizes' => array_keys($filterSizes),
            'priceRanges' => $priceRangesOptions,

            // Active Filters
            'activeFilters' => [
                'size' => $sizes,
                'color' => $colors,
                'price' => $priceRanges,
                'q' => $search,
                'sort' => $sortBy,
            ],

            // Filter Context
            'filterContext' => [
                'lockedGender' => $gender,
                'lockedCategory' => $category,
            ],
            'filterGenders' => [],
            'filterCategories' => [],
            'baseUrl' => '/shop/' . $gender['slug'] . '/' . $category['slug'],
        ]);
    }

    public static function show(string $genderSlug, string $categorySlug, string $productSlugUuid)
    {
        // =========================
        // LOAD DATA
        // =========================
        $genders = json_read('genders.json') ?? [];
        $categories = json_read('product-categories.json') ?? [];
        $products = json_read('products.json') ?? [];

        // =========================
        // RESOLVE GENDER
        // =========================
        $gender = null;
        foreach ($genders as $g) {
            if ($g['slug'] === $genderSlug && empty($g['deleted_at'])) {
                $gender = $g;
                break;
            }
        }

        if (!$gender) {
            http_response_code(404);
            return view('errors/404');
        }

        // =========================
        // RESOLVE CATEGORY
        // =========================
        $category = null;
        foreach ($categories as $c) {
            if (
                $c['slug'] === $categorySlug &&
                empty($c['deleted_at']) &&
                !empty($c['available'])
            ) {
                $category = $c;
                break;
            }
        }

        if (!$category) {
            http_response_code(404);
            return view('errors/404');
        }

        // =========================
        // FIND PRODUCT
        // =========================
        $product = null;

        foreach ($products as $p) {
            if (
                ($p['slug_uuid'] ?? null) === $productSlugUuid &&
                ($p['gender_id'] ?? null) === $gender['id'] &&
                ($p['product_category_id'] ?? null) === $category['id'] &&
                empty($p['deleted_at']) &&
                !empty($p['status'])
            ) {
                $product = $p;
                break;
            }
        }

        if (!$product) {
            http_response_code(404);
            return view('errors/404');
        }

        // =========================
        // FORMAT PRODUCT FOR VIEW
        // =========================
        $productForView = [
            'uuid' => $product['id'],
            'title' => $product['title'],
            'slug_uuid' => $product['slug_uuid'],
            'slug' => $product['slug'],
            'price' => $product['price'],
            'description' => $product['description'],
            'images' => $product['images'] ?? [],
            'sizes' => $product['sizes'] ?? [],
            'colors' => $product['colors'] ?? [],
            'stock' => $product['stock'] ?? 0,

            'gender' => [
                'title' => $gender['title'],
                'slug' => $gender['slug'],
            ],

            'category' => [
                'title' => $category['title'],
                'slug' => $category['slug'],
            ],
        ];

        // =========================
        // RELATED PRODUCTS (OPTIONAL BUT STRONG)
        // =========================
        $related = array_values(array_filter($products, function ($p) use ($product, $gender, $category) {
            return
                $p['slug'] !== $product['slug'] &&
                ($p['gender_id'] ?? null) === $gender['id'] &&
                ($p['product_category_id'] ?? null) === $category['id'] &&
                empty($p['deleted_at']) &&
                !empty($p['status']);
        }));

        $related = array_slice($related, 0, 4);

        $relatedForView = array_map(function ($p) use ($gender, $category) {
            return [
                'title' => $p['title'],
                'slug' => $p['slug'],
                'price' => $p['price'],
                'thumbnail' => $p['images'][0] ?? null,
                'gender' => $gender['slug'],
                'category' => $category['slug'],
            ];
        }, $related);

        $backUrl = '/shop/' . $gender['slug'];

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $ref = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);

            // hanya terima referer dari shop gender
            if (preg_match('#^/shop/' . preg_quote($gender['slug'], '#') . '$#', $ref)) {
                $backUrl = $ref;
            }
        }

        // =========================
        // RETURN VIEW
        // =========================
        return view('front/shop/product_show', [
            'product' => $productForView,
            'gender' => $gender,
            'category' => $category,
            'relatedProducts' => $relatedForView,
            'backUrl' => $backUrl,
        ]);
    }

}