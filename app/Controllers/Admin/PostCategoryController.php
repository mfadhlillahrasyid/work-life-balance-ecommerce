<?php

namespace App\Controllers\Admin;

class PostCategoryController
{
    public static function index()
    {
        admin_auth();

        $allCategories = json_read('post-categories.json');

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
            'admin/post-categories/index',
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
            'admin/post-categories/create'
        );
    }

    public static function store()
    {
        admin_auth();

        $categories = json_read('post-categories.json');

        $uuid = uuid_v4();

        $categories[] = [
            'id' => $uuid,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'slug' => slugify($_POST['title']),
            'slug_uuid' => slugify($_POST['title']) . '-' . $uuid,
            'created_at' => date('Y-m-d H:i:s'),
            'deleted_at' => null
        ];

        json_write('post-categories.json', $categories);

        header('Location: /admin/post-categories');
        exit;
    }

    public static function edit(string $slugUuid)
    {
        admin_auth();

        $uuid = uuid_from_slug($slugUuid);
        $categories = json_read('post-categories.json');
        $category = null;

        foreach ($categories as $c) {
            if (
                empty($c['deleted_at']) &&
                $c['id'] === $uuid
            ) {
                $category = $c;
                break;
            }
        }

        if (!$category) {
            $_SESSION['flash_error'] = 'Post category not found';
            return redirect('/admin/post-categories');
        }

        return view(
            'admin/post-categories/edit',
            compact('category')
        );
    }

    public static function update(string $slugUuid)
    {
        admin_auth();

        $uuid = uuid_from_slug($slugUuid);
        $categories = json_read('post-categories.json');
        $updated = false;

        foreach ($categories as &$c) {
            if (
                empty($c['deleted_at']) &&
                $c['id'] === $uuid
            ) {
                $c['title'] = $_POST['title'];
                $c['description'] = $_POST['description'];

                // slug boleh berubah, UUID TETAP
                $c['slug'] = slugify($_POST['title']);
                $c['slug_uuid'] = $c['slug'] . '-' . $uuid;

                $updated = true;
                break;
            }
        }

        if ($updated) {
            json_write('post-categories.json', $categories);
            $_SESSION['flash_success'] = 'Category updated';
        } else {
            $_SESSION['flash_error'] = 'Update failed';
        }

        return redirect('/admin/post-categories');
    }

    public static function destroy(string $slugUuid)
    {
        admin_auth();

        $categories = json_read('post-categories.json');
        $deleted = false;

        foreach ($categories as &$c) {
            if (
                empty($c['deleted_at']) &&
                $c['slug_uuid'] === $slugUuid
            ) {
                $c['deleted_at'] = date('Y-m-d H:i:s');
                $deleted = true;
                break;
            }
        }

        if ($deleted) {
            json_write('post-categories.json', $categories);
            $_SESSION['flash_success'] = 'Category deleted';
        } else {
            $_SESSION['flash_error'] = 'Category not found';
        }

        return redirect('/admin/post-categories');
    }

}
