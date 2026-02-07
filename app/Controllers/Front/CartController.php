<?php

namespace App\Controllers\Front;

class CartController
{
    public static function index()
    {
        $cart = $_SESSION['cart'] ?? [
            'items' => [],
            'total_qty' => 0,
        ];

        if (empty($cart['items'])) {
            return view('front/cart/index', [
                'items' => [],
                'subtotal' => 0,
            ]);
        }

        $products = json_read('products.json') ?? [];

        $items = [];
        $subtotal = 0;

        foreach ($cart['items'] as $item) {
            foreach ($products as $p) {
                if ($p['slug_uuid'] === $item['slug_uuid']) {

                    $lineSubtotal = $p['price'] * $item['qty'];
                    $subtotal += $lineSubtotal;

                    $items[] = [
                        'id' => md5($p['slug_uuid'] . $item['color'] . $item['size']), // ← PENTING
                        'slug_uuid' => $p['slug_uuid'],
                        'title' => $p['title'],
                        'price' => $p['price'],
                        'image' => $p['images'][0] ?? null,
                        'color' => $item['color'],
                        'size' => strtoupper($item['size']),
                        'qty' => $item['qty'],
                        'line_subtotal' => $lineSubtotal,
                    ];

                    break;
                }
            }
        }

        return view('front/cart/index', [
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }
    public static function add(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            json_response(['success' => false, 'message' => 'Invalid payload'], 400);
        }

        $slugUuid = $payload['slug_uuid'] ?? null;
        $color = $payload['color'] ?? null;
        $size = $payload['size'] ?? null;
        $qty = (int) ($payload['qty'] ?? 1);

        if (!$slugUuid || !$color || !$size) {
            json_response(['success' => false, 'message' => 'Missing variant'], 422);
        }

        $_SESSION['cart'] ??= [
            'items' => [],
            'total_qty' => 0,
        ];

        // Generate unique ID berdasarkan variant
        $itemId = md5($slugUuid . $color . $size);

        // Check apakah item dengan variant yang sama udah ada
        $itemFound = false;

        foreach ($_SESSION['cart']['items'] as &$item) {
            if ($item['id'] === $itemId) {
                // ITEM SUDAH ADA → INCREMENT QTY
                $item['qty'] += $qty;
                $itemFound = true;
                break;
            }
        }

        // Kalau item belum ada, tambahkan baru
        if (!$itemFound) {
            $_SESSION['cart']['items'][] = [
                'id' => $itemId,
                'slug_uuid' => $slugUuid,
                'color' => $color,
                'size' => $size,
                'qty' => $qty,
            ];
        }

        // Update total qty
        $_SESSION['cart']['total_qty'] = array_sum(
            array_column($_SESSION['cart']['items'], 'qty')
        );

        json_response([
            'success' => true,
            'total_qty' => $_SESSION['cart']['total_qty'],
            'is_increment' => $itemFound, // Bonus: frontend bisa tau increment atau new
        ]);
    }
    public static function updateQty()
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        $itemId = $payload['item_id'] ?? null;
        $qty = (int) ($payload['qty'] ?? 1);

        if (!$itemId || $qty < 1) {
            return json_response(['success' => false], 422);
        }

        // Update qty
        $itemFound = false;
        $targetSlugUuid = null;

        foreach ($_SESSION['cart']['items'] as &$item) {
            if ($item['id'] === $itemId) {
                $item['qty'] = $qty;
                $targetSlugUuid = $item['slug_uuid'];
                $itemFound = true;
                break;
            }
        }

        if (!$itemFound) {
            return json_response(['success' => false, 'message' => 'Item not found'], 404);
        }

        // Hitung subtotal (perlu products.json untuk dapetin price)
        $products = json_read('products.json') ?? [];
        $itemSubtotal = 0;
        $subtotal = 0;

        foreach ($_SESSION['cart']['items'] as $cartItem) {
            foreach ($products as $p) {
                if ($p['slug_uuid'] === $cartItem['slug_uuid']) {
                    $lineTotal = $p['price'] * $cartItem['qty'];
                    $subtotal += $lineTotal;

                    // Track item yang baru di-update
                    if ($cartItem['id'] === $itemId) {
                        $itemSubtotal = $lineTotal;
                    }
                    break;
                }
            }
        }

        $_SESSION['cart']['total_qty'] = array_sum(
            array_column($_SESSION['cart']['items'], 'qty')
        );

        return json_response([
            'success' => true,
            'total_qty' => $_SESSION['cart']['total_qty'],
            'subtotal' => $subtotal,
            'item_subtotal' => $itemSubtotal,
        ]);
    }
    public static function remove()
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $itemId = $payload['item_id'] ?? null;

        if (!$itemId) {
            return json_response(['success' => false], 422);
        }

        $beforeCount = count($_SESSION['cart']['items']);

        $_SESSION['cart']['items'] = array_values(array_filter(
            $_SESSION['cart']['items'],
            fn($item) => $item['id'] !== $itemId
        ));

        $afterCount = count($_SESSION['cart']['items']);

        // Validasi apakah item beneran ke-remove
        if ($beforeCount === $afterCount) {
            return json_response(['success' => false, 'message' => 'Item not found'], 404);
        }

        $_SESSION['cart']['total_qty'] = array_sum(
            array_column($_SESSION['cart']['items'], 'qty')
        );

        // Hitung ulang subtotal setelah remove
        $products = json_read('products.json') ?? [];
        $subtotal = 0;

        foreach ($_SESSION['cart']['items'] as $cartItem) {
            foreach ($products as $p) {
                if ($p['slug_uuid'] === $cartItem['slug_uuid']) {
                    $subtotal += $p['price'] * $cartItem['qty'];
                    break;
                }
            }
        }

        return json_response([
            'success' => true,
            'total_qty' => $_SESSION['cart']['total_qty'],
            'subtotal' => $subtotal,
        ]);
    }
}