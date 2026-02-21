<?php
// app/Controllers/Front/CheckoutController.php

namespace App\Controllers\Front;

use App\Middleware\CustomerAuth;

class CheckoutController
{
    // =========================================================================
    // INDEX — GET /checkout
    // =========================================================================
    public static function index(): void
    {
        CustomerAuth::require();

        $cart = $_SESSION['cart'] ?? ['items' => [], 'total_qty' => 0];

        if (empty($cart['items'])) {
            flash('error', 'Keranjang belanja kosong');
            redirect('/cart');
        }

        $customer = CustomerAuth::customer();

        // Ambil data customer dari SQLite
        $stmtC = db()->prepare("
            SELECT * FROM customers WHERE slug_uuid = :slug_uuid LIMIT 1
        ");
        $stmtC->execute([':slug_uuid' => $customer['slug_uuid']]);
        $customerData = $stmtC->fetch();

        // Ambil produk dan variant
        $slugUuids  = array_unique(array_column($cart['items'], 'slug_uuid'));
        $productMap = self::fetchProductMap($slugUuids);
        $variantMap = self::fetchVariantMap($slugUuids);

        $items    = [];
        $subtotal = 0;

        foreach ($cart['items'] as $cartItem) {
            $p = $productMap[$cartItem['slug_uuid']] ?? null;
            if (!$p) continue;

            $variantKey  = strtolower($cartItem['color']) . '_' . strtoupper($cartItem['size']);
            $variantData = $variantMap[$cartItem['slug_uuid']][$variantKey] ?? null;
            $price       = $variantData ? $variantData['price'] : 0;
            $lineTotal   = $price * $cartItem['qty'];
            $subtotal   += $lineTotal;

            $items[] = [
                'slug_uuid'  => $p['slug_uuid'],
                'title'      => $p['title'],
                'price'      => $price,
                'image'      => $p['thumbnail'],
                'color'      => $cartItem['color'],
                'size'       => strtoupper($cartItem['size']),
                'qty'        => $cartItem['qty'],
                'line_total' => $lineTotal,
            ];
        }

        view('front/checkout/index', [
            'items'    => $items,
            'subtotal' => $subtotal,
            'customer' => $customerData,
        ]);
    }

    // =========================================================================
    // PROCESS — POST /checkout/process
    // =========================================================================
    public static function process(): void
    {
        CustomerAuth::require();

        $customer = CustomerAuth::customer();
        $cart     = $_SESSION['cart'] ?? ['items' => [], 'total_qty' => 0];

        if (empty($cart['items'])) {
            flash('error', 'Keranjang belanja kosong');
            redirect('/cart');
        }

        // ── Form data ─────────────────────────────────────────────────────────
        $fullname      = trim($_POST['fullname']       ?? '');
        $phone         = trim($_POST['phone']          ?? '');
        $alamat        = trim($_POST['alamat']         ?? '');
        $provinsi      = trim($_POST['provinsi']       ?? '');
        $kabupaten     = trim($_POST['kabupaten']      ?? '');
        $kecamatan     = trim($_POST['kecamatan']      ?? '');
        $kodePos       = trim($_POST['kode_pos']       ?? '');
        $catatan       = trim($_POST['catatan']        ?? '');
        $ongkir        = (int) ($_POST['ongkir']       ?? 0);
        $paymentMethod = $_POST['payment_method']      ?? 'manual_transfer';

        // ── Validasi ──────────────────────────────────────────────────────────
        if (empty($fullname) || empty($phone) || empty($alamat) || empty($provinsi) || empty($kabupaten)) {
            flash('error', 'Data alamat pengiriman belum lengkap');
            redirect('/checkout');
        }

        // ── Build order items dari SQLite ─────────────────────────────────────
        $slugUuids  = array_unique(array_column($cart['items'], 'slug_uuid'));
        $productMap = self::fetchProductMap($slugUuids);
        $variantMap = self::fetchVariantMap($slugUuids);

        $orderItems = [];
        $subtotal   = 0;

        foreach ($cart['items'] as $cartItem) {
            $p = $productMap[$cartItem['slug_uuid']] ?? null;
            if (!$p) continue;

            $variantKey  = strtolower($cartItem['color']) . '_' . strtoupper($cartItem['size']);
            $variantData = $variantMap[$cartItem['slug_uuid']][$variantKey] ?? null;
            $price       = $variantData ? $variantData['price'] : 0;
            $lineTotal   = $price * $cartItem['qty'];
            $subtotal   += $lineTotal;

            $orderItems[] = [
                'product_slug_uuid' => $p['slug_uuid'],
                'product_title'     => $p['title'],
                'product_image'     => $p['thumbnail'],
                'price'             => $price,
                'qty'               => $cartItem['qty'],
                'color'             => $cartItem['color'],
                'size'              => strtoupper($cartItem['size']),
                'line_total'        => $lineTotal,
            ];
        }

        if (empty($orderItems)) {
            flash('error', 'Produk tidak valid, silakan ulangi pembelian');
            redirect('/cart');
        }

        $total   = $subtotal + $ongkir;
        $orderId = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        $now     = date('Y-m-d H:i:s');

        // ── Simpan order ke JSON (sementara, sampai orders table dibuat) ──────
        $order = [
            'order_id'       => $orderId,
            'customer_uuid'  => $customer['slug_uuid'],
            'customer_name'  => $fullname,
            'customer_email' => $customer['email'],
            'customer_phone' => $phone,

            'shipping_address' => [
                'fullname'   => $fullname,
                'phone'      => $phone,
                'alamat'     => $alamat,
                'provinsi'   => $provinsi,
                'kabupaten'  => $kabupaten,
                'kecamatan'  => $kecamatan,
                'kode_pos'   => $kodePos,
            ],

            'items'      => $orderItems,
            'item_count' => count($orderItems),

            'subtotal'      => $subtotal,
            'shipping_cost' => $ongkir,
            'total'         => $total,

            'payment_method'      => $paymentMethod,
            'payment_status'      => 'pending',
            'payment_proof'       => null,
            'payment_verified_at' => null,

            'status'       => 'waiting_payment',
            'notes'        => $catatan,
            'created_at'   => $now,
            'updated_at'   => null,
            'cancelled_at' => null,
        ];

        // TODO: Ganti ke SQLite setelah orders table dibuat
        $orders   = json_read('orders.json') ?? [];
        $orders[] = $order;
        json_write('orders.json', $orders);

        // ── Clear cart ────────────────────────────────────────────────────────
        $_SESSION['cart'] = ['items' => [], 'total_qty' => 0];

        flash('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
        redirect('/order/' . $orderId);
    }

    // =========================================================================
    // CALCULATE SHIPPING — POST /checkout/calculate-shipping
    // =========================================================================
    public static function calculateShipping(): void
    {
        // TODO: Integrate RajaOngkir API
        json_response([
            'success'       => true,
            'shipping_cost' => 25000,
            'courier'       => 'JNE REG',
            'etd'           => '3-5 hari',
        ]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================
    private static function fetchProductMap(array $slugUuids): array
    {
        if (empty($slugUuids)) return [];

        $placeholders = implode(',', array_fill(0, count($slugUuids), '?'));
        $stmt         = db()->prepare("
            SELECT p.slug_uuid, p.title,
                   (SELECT image FROM product_images
                    WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) AS thumbnail
            FROM products p
            WHERE p.slug_uuid IN ($placeholders)
              AND p.deleted_at IS NULL AND p.status = 1
        ");
        $stmt->execute($slugUuids);

        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $map[$row['slug_uuid']] = $row;
        }
        return $map;
    }

    private static function fetchVariantMap(array $slugUuids): array
    {
        if (empty($slugUuids)) return [];

        $placeholders = implode(',', array_fill(0, count($slugUuids), '?'));
        $stmt         = db()->prepare("
            SELECT p.slug_uuid, pv.color, pv.size, pv.price, pv.stock
            FROM product_variants pv
            JOIN products p ON p.id = pv.product_id
            WHERE p.slug_uuid IN ($placeholders)
        ");
        $stmt->execute($slugUuids);

        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $key           = strtolower($row['color']) . '_' . strtoupper($row['size']);
            $map[$row['slug_uuid']][$key] = [
                'price' => (int) $row['price'],
                'stock' => (int) $row['stock'],
            ];
        }
        return $map;
    }
}