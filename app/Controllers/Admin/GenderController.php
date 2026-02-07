<?php

namespace App\Controllers\Admin;

class GenderController
{
    public static function index()
    {
        admin_auth();

        $allGenders = json_read('genders.json');

        // ===== FILTER & SORT (PRODUCTION RULE) =====
        $allGenders = array_values(array_filter(
            $allGenders,
            fn($c) => empty($c['deleted_at'])
        ));

        usort(
            $allGenders,
            fn($a, $b) =>
            strtotime($b['created_at']) <=> strtotime($a['created_at'])
        );

        // ===== PAGINATION =====
        $page = (int) ($_GET['page'] ?? 1);

        $pagination = paginate($allGenders, 10, $page);

        return view(
            'admin/genders/index',
            [
                'genders' => $pagination['data'],
                'pagination' => $pagination['meta'],
            ]
        );
    }

    public static function create()
    {
        admin_auth();

        return view(
            'admin/genders/create'
        );
    }

    public static function store()
    {
        admin_auth();

        $genders = json_read('genders.json');

        $uuid = uuid_v4();

        $genders[] = [
            'id' => $uuid,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'slug' => slugify($_POST['title']),
            'slug_uuid' => slugify($_POST['title']) . '-' . $uuid,
            'created_at' => date('Y-m-d H:i:s'),
            'deleted_at' => null
        ];

        json_write('genders.json', $genders);


        $_SESSION['success'] = 'Category created successfully';
        return redirect('/admin/genders');
    }

    public static function edit(string $slugUuid)
    {
        admin_auth();

        $uuid = uuid_from_slug($slugUuid);
        $genders = json_read('genders.json');
        $gender = null;

        foreach ($genders as $c) {
            if (
                empty($c['deleted_at']) &&
                $c['id'] === $uuid
            ) {
                $gender = $c;
                break;
            }
        }

        if (!$gender) {
            $_SESSION['flash_error'] = 'Gender not found';
            return redirect('/admin/genders');
        }

        return view(
            'admin/genders/edit',
            compact('gender')
        );
    }

    public static function update(string $slugUuid)
    {
        admin_auth();

        $uuid = uuid_from_slug($slugUuid);
        $genders = json_read('genders.json');
        $updated = false;

        foreach ($genders as &$c) {
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
            json_write('genders.json', $genders);
            $_SESSION['flash_success'] = 'Gender updated';
        } else {
            $_SESSION['flash_error'] = 'Update failed';
        }

        return redirect('/admin/genders');
    }

    public static function destroy(string $slugUuid)
    {
        admin_auth();

        $genders = json_read('genders.json');
        $deleted = false;

        foreach ($genders as &$c) {
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
            json_write('genders.json', $genders);
            $_SESSION['flash_success'] = 'Category deleted';
        } else {
            $_SESSION['flash_error'] = 'Category not found';
        }

        return redirect('/admin/genders');
    }

}
