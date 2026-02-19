<?php
// app/Controllers/Api/SearchController.php

namespace App\Controllers\Api;

class SearchController
{
    /**
     * Instant search API endpoint
     *
     * Route: GET /api/search?q={query}
     */
    public static function search(): void
    {
        header('Content-Type: application/json');

        $query = trim($_GET['q'] ?? '');

        if (strlen($query) < 2) {
            echo json_encode([
                'success' => false,
                'message' => 'Query too short (minimum 2 characters)',
                'products' => [],
            ]);
            return;
        }

        $stmt = db()->prepare("
            SELECT
                p.title,
                p.slug,
                p.slug_uuid,
                pc.slug   AS category_slug,
                g.slug    AS gender_slug,
                MIN(pv.price) AS price_from,
                (SELECT image FROM product_images
                 WHERE product_id = p.id
                 ORDER BY sort_order ASC LIMIT 1) AS thumbnail
            FROM products p
            LEFT JOIN product_categories pc ON pc.id = p.product_category_id
            LEFT JOIN genders g             ON g.id  = p.gender_id
            LEFT JOIN product_variants pv   ON pv.product_id = p.id
            WHERE p.deleted_at IS NULL
              AND p.status = 1
              AND p.title LIKE :query
            GROUP BY p.id
            LIMIT 10
        ");

        $stmt->execute([':query' => '%' . $query . '%']);
        $results = $stmt->fetchAll();

        $formatted = array_map(fn($r) => [
            'title' => $r['title'],
            'slug' => $r['slug'],
            'slug_uuid' => $r['slug_uuid'],
            'price_from' => (int) $r['price_from'],
            'thumbnail' => $r['thumbnail'],
            'category_slug' => $r['category_slug'] ?? 'uncategorized',
            'gender_slug' => $r['gender_slug'] ?? 'unisex',
        ], $results);

        echo json_encode([
            'success' => true,
            'query' => $query,
            'products' => $formatted,
            'count' => count($formatted),
        ]);
    }
}