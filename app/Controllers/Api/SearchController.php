<?php

namespace App\Controllers\Api;

class SearchController
{
    /**
     * Instant search API endpoint
     * 
     * Route: GET /api/search?q={query}
     * 
     * @return void
     */
    public static function search()
    {
        header('Content-Type: application/json');

        // Get query parameter
        $query = trim($_GET['q'] ?? '');

        // Validate query
        if (empty($query) || strlen($query) < 2) {
            echo json_encode([
                'success' => false,
                'message' => 'Query too short (minimum 2 characters)',
                'products' => []
            ]);
            return;
        }

        // Load products
        $products = json_read('products.json') ?? [];
        $categories = json_read('product-categories.json') ?? [];
        $genders = json_read('genders.json') ?? [];

        // Build maps
        $categoryMap = [];
        foreach ($categories as $cat) {
            if (empty($cat['deleted_at'])) {
                $categoryMap[$cat['id']] = $cat['slug'];
            }
        }

        $genderMap = [];
        foreach ($genders as $g) {
            if (empty($g['deleted_at'])) {
                $genderMap[$g['id']] = $g['slug'];
            }
        }

        // Search products
        $results = [];
        $queryLower = strtolower($query);

        foreach ($products as $product) {
            // Skip inactive products
            if (!empty($product['deleted_at']) || empty($product['status'])) {
                continue;
            }

            // Search in title
            if (stripos($product['title'], $query) !== false) {
                $results[] = [
                    'title' => $product['title'],
                    'slug_uuid' => $product['slug_uuid'],
                    'slug' => $product['slug'],
                    'price' => $product['price'],
                    'thumbnail' => $product['images'][0] ?? null,
                    'category' => $categoryMap[$product['product_category_id']] ?? 'uncategorized',
                    'gender' => $genderMap[$product['gender_id'] ?? null] ?? 'unisex',
                ];

                // Limit to 10 results
                if (count($results) >= 10) {
                    break;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'query' => $query,
            'products' => $results,
            'count' => count($results)
        ]);
    }
}