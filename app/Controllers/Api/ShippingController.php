<?php
// app/Controllers/Api/ShippingController.php

namespace App\Controllers\Api;

class ShippingController
{
    /**
     * GET /api/shipping/cost?province_id=32
     */
    public static function cost(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $provinceId = (int) ($_GET['province_id'] ?? 0);

        if (!$provinceId) {
            echo json_encode(['success' => false, 'message' => 'province_id required', 'data' => []]);
            return;
        }

        // Ambil semua zones
        $zones = db()->query("SELECT * FROM shipping_zones ORDER BY cost ASC")->fetchAll();

        $matched = [];

        foreach ($zones as $zone) {
            $provinces = json_decode($zone['provinces'], true) ?? [];

            if (in_array($provinceId, $provinces, true)) {
                $matched[] = [
                    'id'    => (int) $zone['id'],
                    'name'  => $zone['name'],
                    'kurir' => $zone['kurir'],
                    'icon' => $zone['icon'] ?? null,
                    'cost'  => (int) $zone['cost'],
                ];
            }
        }

        echo json_encode([
            'success' => true,
            'data'    => $matched,
        ]);
    }
}