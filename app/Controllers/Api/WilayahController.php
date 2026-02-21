<?php
// app/Controllers/Api/WilayahController.php

namespace App\Controllers\Api;

class WilayahController
{
    /**
     * Baca JSON file dengan strip BOM jika ada
     */
    private static function readJson(string $path): array
    {
        if (!file_exists($path))
            return [];

        $content = file_get_contents($path);

        // Strip UTF-8 BOM jika ada
        $content = ltrim($content, "\xEF\xBB\xBF");

        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    /**
     * GET /api/wilayah/provinces
     */
    public static function provinces(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $path = ROOT_PATH . '/database/wilayah/provinces.json';
        $data = self::readJson($path);

        if (empty($data)) {
            echo json_encode(['success' => false, 'data' => []]);
            return;
        }

        usort($data, fn($a, $b) => strcmp($a['name'], $b['name']));

        echo json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * GET /api/wilayah/regencies?province_id=32
     */
    public static function regencies(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $provinceId = trim($_GET['province_id'] ?? '');

        if ($provinceId === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'province_id required',
                'data' => []
            ]);
            return;
        }

        $file = ROOT_PATH . '/database/wilayah/regencies/' . $provinceId . '.json';

        if (!file_exists($file)) {
            echo json_encode([
                'success' => true,
                'data' => []
            ]);
            return;
        }

        $data = self::readJson($file);

        if (!is_array($data)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid data format',
                'data' => []
            ]);
            return;
        }

        usort($data, fn($a, $b) => strcmp($a['name'], $b['name']));

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    }
}