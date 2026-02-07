<?php

namespace App\Controllers\Admin;

class ProductController
{
    public static function index()
    {
        admin_auth();

        $products = json_read('products.json') ?? [];
        $categories = json_read('product-categories.json') ?? [];
        $genders = json_read('genders.json') ?? [];

        // map category & gender
        $categoryMap = [];
        foreach ($categories as $c) {
            $categoryMap[$c['id']] = $c['title'];
        }

        $genderMap = [];
        foreach ($genders as $g) {
            $genderMap[$g['id']] = $g['title'];
        }

        $products = array_values(array_filter($products, fn($p) => empty($p['deleted_at'])));

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $total = count($products);
        $offset = ($page - 1) * $perPage;

        $products = array_slice($products, $offset, $perPage);

        return view(
            'admin/products/index',
            compact('products', 'categoryMap', 'genderMap', 'page', 'total')
        );
    }

    public static function create()
    {
        admin_auth();

        $categories = json_read('product-categories.json') ?? [];
        $genders = json_read('genders.json') ?? [];

        $categories = array_values(array_filter($categories, function ($c) {
            return empty($c['deleted_at']) && !empty($c['available']);
        }));

        return view(
            'admin/products/create',
            compact('categories', 'genders')
        );
    }

    public static function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect('/admin/products');
        }

        admin_auth();

        $title = trim($_POST['title'] ?? '');
        $categoryId = $_POST['product_category_id'] ?? null;
        $genderId = $_POST['gender_id'] ?? null;

        if ($title === '' || !$categoryId || !$genderId) {
            $_SESSION['error'] = 'Title, Category & Gender are required';
            return redirect('/admin/products/create');
        }

        $images = [];
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $i => $name) {
                try {
                    $images[] = upload_image(
                        [
                            'name' => $name,
                            'tmp_name' => $_FILES['images']['tmp_name'][$i],
                            'size' => $_FILES['images']['size'][$i],
                            'type' => $_FILES['images']['type'][$i],
                            'error' => $_FILES['images']['error'][$i],
                        ],
                        ROOT_PATH . '/storage/products',
                        2
                    );
                } catch (\Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                    return redirect('/admin/products/create');
                }
            }
        }

        $products = json_read('products.json') ?? [];

        $uuid = uuid_v4();
        $slug = slugify($title);

        $products[] = [
            'id' => $uuid,
            'slug_uuid' => $slug . '-' . $uuid,

            'title' => htmlspecialchars($title, ENT_QUOTES),
            'slug' => $slug,

            'product_category_id' => $categoryId,
            'gender_id' => $genderId,

            'images' => $images,
            'colors' => parse_csv($_POST['colors'] ?? ''),
            'sizes' => parse_csv($_POST['size'] ?? ''),
            'price' => (int) $_POST['price'],
            'stock' => (int) $_POST['stock'],
            'description' => $_POST['description'],

            'status' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
            'deleted_at' => null
        ];

        json_write('products.json', $products);

        $_SESSION['success'] = 'Product created successfully';
        return redirect('/admin/products');
    }

    public static function edit(string $slugUuid)
    {
        admin_auth();

        $products = json_read('products.json') ?? [];
        $allCategories = json_read('product-categories.json') ?? [];
        $allGenders = json_read('genders.json') ?? [];

        foreach ($products as $product) {
            if ($product['slug_uuid'] !== $slugUuid) {
                continue;
            }
            $categories = array_values(array_filter(
                $allCategories,
                fn($c) =>
                empty($c['deleted_at']) &&
                (
                    !empty($c['available']) ||
                    $c['id'] === ($product['product_category_id'] ?? null)
                )
            ));

            $genders = array_values(array_filter(
                $allGenders,
                fn($g) => empty($g['deleted_at'])
            ));

            $currentCategoryName = null;
            foreach ($categories as $c) {
                if ($c['id'] === ($product['product_category_id'] ?? null)) {
                    $currentCategoryName = $c['title'];
                    break;
                }
            }

            $currentGenderName = null;
            foreach ($genders as $g) {
                if ($g['id'] === ($product['gender_id'] ?? null)) {
                    $currentGenderName = $g['title'];
                    break;
                }
            }

            return view(
                'admin/products/edit',
                compact(
                    'product',
                    'categories',
                    'genders',
                    'currentCategoryName',
                    'currentGenderName'
                )
            );
        }

        return redirect('/admin/products');
    }

    public static function update(string $slugUuid)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect('/admin/products');
        }

        admin_auth();

        $products = json_read('products.json') ?? [];

        foreach ($products as &$product) {
            if ($product['slug_uuid'] === $slugUuid) {

                $product['title'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
                $product['slug'] = slugify($_POST['title']);

                $product['product_category_id'] = $_POST['product_category_id'];
                $product['gender_id'] = $_POST['gender_id'];

                $product['colors'] = parse_csv($_POST['colors'] ?? '');
                $product['sizes'] = parse_csv($_POST['size'] ?? '');
                $product['price'] = (int) $_POST['price'];
                $product['stock'] = (int) $_POST['stock'];
                $product['description'] = $_POST['description'];
                $product['status'] = isset($_POST['status']);
                $product['updated_at'] = date('Y-m-d H:i:s');

                if (!empty($_POST['remove_images'])) {
                    foreach ($_POST['remove_images'] as $img) {
                        $path = ROOT_PATH . '/storage/products/' . $img;
                        if (file_exists($path)) {
                            @unlink($path);
                        }

                        $product['images'] = array_values(
                            array_filter(
                                $product['images'],
                                fn($i) => $i !== $img
                            )
                        );
                    }
                }

                if (!empty($_FILES['images']['name'][0])) {
                    foreach ($_FILES['images']['name'] as $i => $name) {
                        $product['images'][] = upload_image(
                            [
                                'name' => $name,
                                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                                'size' => $_FILES['images']['size'][$i],
                                'type' => $_FILES['images']['type'][$i],
                                'error' => $_FILES['images']['error'][$i],
                            ],
                            ROOT_PATH . '/storage/products',
                            2
                        );
                    }
                }

                break;
            }
        }

        json_write('products.json', $products);
        $_SESSION['success'] = 'Product updated successfully';
        return redirect('/admin/products');
    }

    public static function destroy(string $slugUuid)
    {
        admin_auth();

        $products = json_read('products.json') ?? [];

        foreach ($products as &$product) {
            if ($product['slug_uuid'] === $slugUuid) {
                $product['deleted_at'] = date('Y-m-d H:i:s');
                break;
            }
        }

        json_write('products.json', $products);
        return redirect('/admin/products');
    }
}
