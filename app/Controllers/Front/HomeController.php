<?php
// app/Controllers/Front/HomeController.php

namespace App\Controllers\Front;

class HomeController
{
    public static function index(): void
    {
        // Ambil genders dari SQLite
        $genders = db()->query("
            SELECT id, title, slug, banner
            FROM genders
            WHERE deleted_at IS NULL
            ORDER BY title ASC
        ")->fetchAll();

        // Ambil featured products (published, ada stock)
        $featuredProducts = db()->query("
            SELECT p.id, p.title, p.slug_uuid,
                   pc.title AS category_name,
                   (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) AS thumbnail,
                   MIN(pv.price) AS price_from,
                   SUM(pv.stock) AS total_stock
            FROM products p
            LEFT JOIN product_categories pc ON pc.id = p.product_category_id
            LEFT JOIN product_variants pv ON pv.product_id = p.id
            WHERE p.deleted_at IS NULL AND p.status = 1
            GROUP BY p.id
            HAVING total_stock > 0
            ORDER BY p.created_at DESC
            LIMIT 8
        ")->fetchAll();

        view('front/home', [
            'genders'         => $genders,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}