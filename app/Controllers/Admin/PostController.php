<?php

namespace App\Controllers\Admin;

class PostController
{
    public static function index()
    {
        admin_auth();

        $posts = json_read('posts.json');
        $categories = json_read('post-categories.json');

        if (!is_array($posts)) {
            $posts = [];
        }

        if (!is_array($categories)) {
            $categories = [];
        }

        return view(
            'admin/posts/index',
            compact('posts', 'categories')
        );
    }


    public static function create()
    {
        admin_auth();

        $categories = json_read('post-categories.json');

        return view(
            'admin/posts/create',
            compact('categories')
        );
    }

    public static function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect('/admin/posts');
        }

        admin_auth();

        $title = trim($_POST['title'] ?? '');
        $postCategoryId = $_POST['post_category_id'] ?? null;
        $content = $_POST['content'] ?? '';

        if ($title === '') {
            $_SESSION['error'] = 'Title is required';
            return redirect('/admin/posts/create');
        }

        if (!$postCategoryId) {
            $_SESSION['error'] = 'Category is required';
            return redirect('/admin/posts/create');
        }

        $banner = null;
        if (!empty($_FILES['banner']['name'])) {
            try {
                $banner = upload_image(
                    $_FILES['banner'],
                    ROOT_PATH . '/storage/banners',
                    2
                );
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                return redirect('/admin/posts/create');
            }
        }

        $posts = json_read('posts.json');
        if (!is_array($posts))
            $posts = [];

        $uuid = uuid_v4();
        $slug = slugify($title);
        $slugUuid = $slug . '-' . $uuid;

        $posts[] = [
            'id' => $uuid,
            'slug_uuid' => $slugUuid,
            'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            'slug' => $slug,
            'post_category_id' => $postCategoryId,
            'banner' => $banner,
            'content' => $content,
            'tags' => parse_tags($_POST['tags'] ?? ''),
            'status' => false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
            'deleted_at' => null
        ];

        json_write('posts.json', $posts);

        $_SESSION['success'] = 'Post created successfully';
        return redirect('/admin/posts');
    }


    public static function edit(string $slugUuid)
    {
        admin_auth();

        $posts = json_read('posts.json');
        $categories = json_read('post-categories.json');

        if (!is_array($posts))
            $posts = [];
        if (!is_array($categories))
            $categories = [];

        $post = null;
        foreach ($posts as $p) {
            if ($p['slug_uuid'] === $slugUuid) {
                $post = $p;
                break;
            }
        }

        if (!$post) {
            return redirect('/admin/posts');
        }

        // 🔥 MAP CATEGORY ID → TITLE (CONTROLLER)
        $currentCategoryName = null;
        foreach ($categories as $category) {
            if ($category['id'] === ($post['post_category_id'] ?? null)) {
                $currentCategoryName = $category['title'];
                break;
            }
        }

        return view(
            'admin/posts/edit',
            compact('post', 'categories', 'currentCategoryName')
        );
    }


    public static function update(string $slugUuid)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return redirect('/admin/posts');
        }

        admin_auth();

        $title = trim($_POST['title'] ?? '');
        $category = $_POST['post_category_id'] ?? null;
        $content = $_POST['content'] ?? '';

        if ($title === '') {
            $_SESSION['error'] = 'Title is required';
            return redirect('/admin/posts/' . $slugUuid . '/edit');
        }

        if (!$category) {
            $_SESSION['error'] = 'Category is required';
            return redirect('/admin/posts/' . $slugUuid . '/edit');
        }

        $posts = json_read('posts.json');
        if (!is_array($posts)) {
            return redirect('/admin/posts');
        }

        foreach ($posts as &$post) {
            if ($post['slug_uuid'] === $slugUuid) {

                // =====================
                // BASIC FIELDS
                // =====================
                $post['title'] = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                $post['slug'] = slugify($title);
                $post['post_category_id'] = $_POST['post_category_id'];
                $post['content'] = $content;
                $post['tags'] = parse_tags($_POST['tags'] ?? '');
                $post['status'] = isset($_POST['status']) ? true : false;
                $post['updated_at'] = date('Y-m-d H:i:s');

                // =====================
                // REMOVE BANNER
                // =====================
                if (
                    isset($_POST['remove_banner']) &&
                    $_POST['remove_banner'] === '1' &&
                    !empty($post['banner'])
                ) {
                    @unlink(ROOT_PATH . '/storage/banners/' . $post['banner']);
                    $post['banner'] = null;
                }

                // =====================
                // UPLOAD / REPLACE BANNER
                // =====================
                if (!empty($_FILES['banner']['name'])) {
                    try {
                        // hapus banner lama jika ada
                        if (!empty($post['banner'])) {
                            @unlink(ROOT_PATH . '/storage/banners/' . $post['banner']);
                        }

                        $post['banner'] = upload_image(
                            $_FILES['banner'],
                            ROOT_PATH . '/storage/banners',
                            2 // max 2MB
                        );
                    } catch (\Exception $e) {
                        $_SESSION['error'] = $e->getMessage();
                        return redirect('/admin/posts/' . $slugUuid . '/edit');
                    }
                }

                break;
            }
        }

        json_write('posts.json', $posts);

        $_SESSION['success'] = 'Post updated successfully';
        return redirect('/admin/posts');
    }

    public static function destroy(string $slugUuid)
    {
        admin_auth();

        $posts = json_read('posts.json');
        if (!is_array($posts)) {
            return redirect('/admin/posts');
        }

        foreach ($posts as &$post) {
            if ($post['slug_uuid'] === $slugUuid) {

                // hapus banner file (opsional tapi rapi)
                if (!empty($post['banner'])) {
                    @unlink(ROOT_PATH . '/storage/banners/' . $post['banner']);
                    $post['banner'] = null;
                }

                $post['deleted_at'] = date('Y-m-d H:i:s');
                break;
            }
        }

        json_write('posts.json', $posts);

        $_SESSION['success'] = 'Post deleted successfully';
        return redirect('/admin/posts');
    }

}


