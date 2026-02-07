<?php

namespace App\Controllers\Front;

class HomeController
{
    public static function index()
    {
        // 1️⃣ Ambil data genders
        $genders = json_read('genders.json');

        if (!is_array($genders)) {
            $genders = [];
        }

        // 2️⃣ Filter: hanya yang tidak dihapus
        $genders = array_values(array_filter($genders, function ($gender) {
            return empty($gender['deleted_at']);
        }));

        // 3️⃣ Mapping ke data contract FRONT
        $genders = array_map(function ($gender) {
            return [
                'id' => $gender['id'],
                'title' => $gender['title'],
                'slug' => $gender['slug'],
            ];
        }, $genders);

        // 4️⃣ Sorting alfabet (UX > ego)
        usort($genders, function ($b, $a) {
            return strcmp($a['title'], $b['title']);
        });

        // 5️⃣ Render view
        return view('front/home', [
            'genders' => $genders,
        ]);
    }


}