<?php

namespace App\Controllers\Admin;
require_once ROOT_PATH . '/config/util.php';
use Exception;

class ProductCategoryController
{

    public static function index()
    {
        admin_auth();

        $allCategories = json_read('product-categories.json');

        // ===== FILTER & SORT (PRODUCTION RULE) =====
        $allCategories = array_values(array_filter(
            $allCategories,
            fn($c) => empty($c['deleted_at'])
        ));

        usort(
            $allCategories,
            fn($a, $b) =>
            strtotime($b['created_at']) <=> strtotime($a['created_at'])
        );

        // ===== PAGINATION =====
        $page = (int) ($_GET['page'] ?? 1);

        $pagination = paginate($allCategories, 10, $page);

        return view(
            'admin/product-categories/index',
            [
                'categories' => $pagination['data'],
                'pagination' => $pagination['meta'],
            ]
        );
    }

    public static function create()
    {
        admin_auth();

        return view(
            'admin/product-categories/create'
        );
    }

    public static function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect('/admin/product-categories');
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            $_SESSION['error'] = 'Title is required';
            return redirect('/admin/product-categories/create');
        }

        // Upload icon
        $icon = null;
        if (!empty($_FILES['icon']['name'])) {
            try {
                $icon = upload_image(
                    $_FILES['icon'],
                    ROOT_PATH . '/storage/icons',
                    2
                );
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                return redirect('/admin/product-categories/create');
            }
        }

        // Database
        $categories = json_read('product-categories.json');
        if (!is_array($categories)) {
            $categories = [];
        }

        $uuid = uuid_v4();
        $slug = slugify($title);

        $categories[] = [
            'id' => $uuid,
            'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            'slug' => $slug,
            'slug_uuid' => $slug . '-' . $uuid,
            'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'icon' => $icon,
            'available' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'deleted_at' => null,
        ];

        json_write('product-categories.json', $categories);

        $_SESSION['success'] = 'Category created successfully';
        return redirect('/admin/product-categories');
    }

    public static function edit(string $slugUuid)
    {
        admin_auth();

        $uuid = uuid_from_slug($slugUuid);
        $categories = json_read('product-categories.json');

        $category = null;
        foreach ($categories as $item) {
            if ($item['id'] === $uuid && $item['deleted_at'] === null) {
                $category = $item;
                break;
            }
        }

        if (!$category) {
            $_SESSION['error'] = 'Category not found';
            return redirect('/admin/product-categories');
        }

        return view('admin/product-categories/edit', compact('category'));
    }


    public static function update(string $slugUuid)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect('/admin/product-categories');
        }


        $uuid = uuid_from_slug($slugUuid);

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $removeIcon = $_POST['remove_icon'] ?? '0';
        $available = isset($_POST['available']) && $_POST['available'] === '1';

        if ($title === '') {
            $_SESSION['error'] = 'Title is required';
            return redirect("/admin/product-categories/{$slugUuid}/edit");
        }

        $categories = json_read('product-categories.json');

        foreach ($categories as &$category) {
            if ($category['id'] !== $uuid)
                continue;

            // ICON REMOVE
            if ($removeIcon === '1' && !empty($category['icon'])) {
                $oldPath = ROOT_PATH . '/storage/icons/' . $category['icon'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
                $category['icon'] = null;
            }

            // ICON REPLACE
            if (!empty($_FILES['icon']['name'])) {
                try {
                    $newIcon = upload_image(
                        $_FILES['icon'],
                        ROOT_PATH . '/storage/icons',
                        2
                    );

                    if (!empty($category['icon'])) {
                        $oldPath = ROOT_PATH . '/storage/icons/' . $category['icon'];
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $category['icon'] = $newIcon;
                } catch (\Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                    return redirect("/admin/product-categories/{$slugUuid}/edit");
                }
            }

            // DATA UPDATE
            $slug = slugify($title);

            $category['title'] = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            $category['slug'] = $slug;
            $category['available'] = $available;
            $category['slug_uuid'] = $slug . '-' . $uuid;
            $category['description'] = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
            $category['updated_at'] = date('Y-m-d H:i:s');

            break;
        }

        json_write('product-categories.json', $categories);

        $_SESSION['success'] = 'Category updated successfully';
        return redirect('/admin/product-categories');
    }


    public static function destroy(string $slugUuid)
    {
        admin_auth();

        $uuid = uuid_from_slug($slugUuid);
        $categories = json_read('product-categories.json');

        foreach ($categories as &$category) {
            if ($category['id'] === $uuid) {
                $category['deleted_at'] = date('Y-m-d H:i:s');
                break;
            }
        }

        json_write('product-categories.json', $categories);

        $_SESSION['success'] = 'Category deleted';
        return redirect('/admin/product-categories');
    }
}