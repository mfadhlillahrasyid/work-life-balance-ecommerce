<?php
// app/Controllers/Front/CartController.php

namespace App\Controllers\Front;

class CartController
{
    // =========================================================================
    // SHARED: Ambil price dan image dari SQLite berdasarkan slug_uuid
    // =========================================================================
    private static function fetchProductData(array $slugUuids): array
    {
        if (empty($slugUuids)) return [];

        $placeholders = implode(',', array_fill(0, count($slugUuids), '?'));

        $stmt = db()->prepare("
            SELECT p.slug_uuid,
                   p.title,
                   (SELECT image FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) AS thumbnail
            FROM products p
            WHERE p.slug_uuid IN ($placeholders)
              AND p.deleted_at IS NULL
              AND p.status = 1
        ");
        $stmt->execute($slugUuids);

        // Key by slug_uuid untuk lookup O(1)
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['slug_uuid']] = $row;
        }
        return $result;
    }

    /**
     * Ambil harga spesifik per variant (color + size)
     * Return map: slug_uuid => [color_size => price]
     */
    private static function fetchVariantPrices(array $slugUuids): array
    {
        if (empty($slugUuids)) return [];

        $placeholders = implode(',', array_fill(0, count($slugUuids), '?'));

        $stmt = db()->prepare("
            SELECT p.slug_uuid, pv.color, pv.size, pv.price, pv.stock
            FROM product_variants pv
            JOIN products p ON p.id = pv.product_id
            WHERE p.slug_uuid IN ($placeholders)
        ");
        $stmt->execute($slugUuids);

        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $key = strtolower($row['color']) . '_' . strtoupper($row['size']);
            $result[$row['slug_uuid']][$key] = [
                'price' => (int) $row['price'],
                'stock' => (int) $row['stock'],
            ];
        }
        return $result;
    }

    // =========================================================================
    // INDEX
    // =========================================================================
    public static function index(): void
    {
        $cart = $_SESSION['cart'] ?? ['items' => [], 'total_qty' => 0];

        if (empty($cart['items'])) {
            view('front/cart/index', ['items' => [], 'subtotal' => 0]);
            return;
        }

        $slugUuids    = array_unique(array_column($cart['items'], 'slug_uuid'));
        $productMap   = self::fetchProductData($slugUuids);
        $variantMap   = self::fetchVariantPrices($slugUuids);

        $items    = [];
        $subtotal = 0;

        foreach ($cart['items'] as $item) {
            $p = $productMap[$item['slug_uuid']] ?? null;
            if (!$p) continue;

            $variantKey  = strtolower($item['color']) . '_' . strtoupper($item['size']);
            $variantData = $variantMap[$item['slug_uuid']][$variantKey] ?? null;
            $price       = $variantData ? $variantData['price'] : 0;

            $lineSubtotal = $price * $item['qty'];
            $subtotal    += $lineSubtotal;

            $items[] = [
                'id'            => $item['id'],
                'slug_uuid'     => $item['slug_uuid'],
                'title'         => $p['title'],
                'price'         => $price,
                'image'         => $p['thumbnail'],
                'color'         => $item['color'],
                'size'          => strtoupper($item['size']),
                'qty'           => $item['qty'],
                'line_subtotal' => $lineSubtotal,
            ];
        }

        view('front/cart/index', [
            'items'    => $items,
            'subtotal' => $subtotal,
        ]);
    }

    // =========================================================================
    // ADD
    // =========================================================================
    public static function add(): void
    {
        $payload  = json_decode(file_get_contents('php://input'), true);

        if (!$payload) {
            json_response(['success' => false, 'message' => 'Invalid payload'], 400);
            return;
        }

        $slugUuid = $payload['slug_uuid'] ?? null;
        $color    = strtolower(trim($payload['color'] ?? ''));
        $size     = strtoupper(trim($payload['size'] ?? ''));
        $qty      = max(1, (int) ($payload['qty'] ?? 1));

        if (!$slugUuid || !$color || !$size) {
            json_response(['success' => false, 'message' => 'Missing variant'], 422);
            return;
        }

        // Validasi variant exist dan cek stok
        $stmt = db()->prepare("
            SELECT pv.price, pv.stock
            FROM product_variants pv
            JOIN products p ON p.id = pv.product_id
            WHERE p.slug_uuid = :slug_uuid
              AND LOWER(pv.color) = :color
              AND UPPER(pv.size)  = :size
              AND p.deleted_at IS NULL
              AND p.status = 1
            LIMIT 1
        ");
        $stmt->execute([
            ':slug_uuid' => $slugUuid,
            ':color'     => $color,
            ':size'      => $size,
        ]);
        $variant = $stmt->fetch();

        if (!$variant) {
            json_response(['success' => false, 'message' => 'Variant tidak tersedia'], 422);
            return;
        }

        if ((int) $variant['stock'] <= 0) {
            json_response(['success' => false, 'message' => 'Stok habis'], 422);
            return;
        }

        // Session cart
        $_SESSION['cart'] ??= ['items' => [], 'total_qty' => 0];

        $itemId    = md5($slugUuid . $color . $size);
        $itemFound = false;

        foreach ($_SESSION['cart']['items'] as &$item) {
            if ($item['id'] === $itemId) {
                $newQty = $item['qty'] + $qty;
                // Jangan exceed stok
                $item['qty'] = min($newQty, (int) $variant['stock']);
                $itemFound   = true;
                break;
            }
        }
        unset($item);

        if (!$itemFound) {
            $_SESSION['cart']['items'][] = [
                'id'        => $itemId,
                'slug_uuid' => $slugUuid,
                'color'     => $color,
                'size'      => $size,
                'qty'       => min($qty, (int) $variant['stock']),
            ];
        }

        $_SESSION['cart']['total_qty'] = array_sum(
            array_column($_SESSION['cart']['items'], 'qty')
        );

        json_response([
            'success'      => true,
            'total_qty'    => $_SESSION['cart']['total_qty'],
            'is_increment' => $itemFound,
        ]);
    }

    // =========================================================================
    // UPDATE QTY
    // =========================================================================
    public static function updateQty(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $itemId  = $payload['item_id'] ?? null;
        $qty     = (int) ($payload['qty'] ?? 1);

        if (!$itemId || $qty < 1) {
            json_response(['success' => false], 422);
            return;
        }

        $itemFound = false;

        foreach ($_SESSION['cart']['items'] as &$item) {
            if ($item['id'] === $itemId) {
                $item['qty'] = $qty;
                $itemFound   = true;
                break;
            }
        }
        unset($item);

        if (!$itemFound) {
            json_response(['success' => false, 'message' => 'Item not found'], 404);
            return;
        }

        // Hitung ulang subtotal dari SQLite
        $slugUuids  = array_unique(array_column($_SESSION['cart']['items'], 'slug_uuid'));
        $variantMap = self::fetchVariantPrices($slugUuids);

        $subtotal     = 0;
        $itemSubtotal = 0;

        foreach ($_SESSION['cart']['items'] as $cartItem) {
            $variantKey = strtolower($cartItem['color']) . '_' . strtoupper($cartItem['size']);
            $price      = $variantMap[$cartItem['slug_uuid']][$variantKey]['price'] ?? 0;
            $lineTotal  = $price * $cartItem['qty'];
            $subtotal  += $lineTotal;

            if ($cartItem['id'] === $itemId) {
                $itemSubtotal = $lineTotal;
            }
        }

        $_SESSION['cart']['total_qty'] = array_sum(
            array_column($_SESSION['cart']['items'], 'qty')
        );

        json_response([
            'success'       => true,
            'total_qty'     => $_SESSION['cart']['total_qty'],
            'subtotal'      => $subtotal,
            'item_subtotal' => $itemSubtotal,
        ]);
    }

    // =========================================================================
    // REMOVE
    // =========================================================================
    public static function remove(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true);
        $itemId  = $payload['item_id'] ?? null;

        if (!$itemId) {
            json_response(['success' => false], 422);
            return;
        }

        $beforeCount = count($_SESSION['cart']['items']);

        $_SESSION['cart']['items'] = array_values(array_filter(
            $_SESSION['cart']['items'],
            fn($item) => $item['id'] !== $itemId
        ));

        if (count($_SESSION['cart']['items']) === $beforeCount) {
            json_response(['success' => false, 'message' => 'Item not found'], 404);
            return;
        }

        $_SESSION['cart']['total_qty'] = array_sum(
            array_column($_SESSION['cart']['items'], 'qty')
        );

        // Hitung ulang subtotal
        $subtotal = 0;

        if (!empty($_SESSION['cart']['items'])) {
            $slugUuids  = array_unique(array_column($_SESSION['cart']['items'], 'slug_uuid'));
            $variantMap = self::fetchVariantPrices($slugUuids);

            foreach ($_SESSION['cart']['items'] as $cartItem) {
                $variantKey = strtolower($cartItem['color']) . '_' . strtoupper($cartItem['size']);
                $price      = $variantMap[$cartItem['slug_uuid']][$variantKey]['price'] ?? 0;
                $subtotal  += $price * $cartItem['qty'];
            }
        }

        json_response([
            'success'   => true,
            'total_qty' => $_SESSION['cart']['total_qty'],
            'subtotal'  => $subtotal,
        ]);
    }
}