<?php

namespace App\Controllers\Front;

use App\Middleware\CustomerAuth;

class CheckoutController
{
    /**
     * Show checkout page (protected)
     * 
     * Route: GET /checkout
     */
    public static function index()
    {
        // Require authentication
        CustomerAuth::require();

        // Get cart
        $cart = $_SESSION['cart'] ?? ['items' => [], 'total_qty' => 0];

        if (empty($cart['items'])) {
            $_SESSION['error'] = 'Keranjang belanja kosong';
            return redirect('/cart');
        }

        // Load products for price calculation
        $products = json_read('products.json') ?? [];
        $customer = CustomerAuth::customer();

        // Calculate subtotal
        $items = [];
        $subtotal = 0;

        foreach ($cart['items'] as $cartItem) {
            foreach ($products as $p) {
                if ($p['slug_uuid'] === $cartItem['slug_uuid']) {
                    $lineTotal = $p['price'] * $cartItem['qty'];
                    $subtotal += $lineTotal;

                    $items[] = [
                        'slug_uuid' => $p['slug_uuid'],
                        'title' => $p['title'],
                        'price' => $p['price'],
                        'image' => $p['images'][0] ?? null,
                        'color' => $cartItem['color'],
                        'size' => strtoupper($cartItem['size']),
                        'qty' => $cartItem['qty'],
                        'line_total' => $lineTotal,
                    ];
                    break;
                }
            }
        }

        // Get customer data
        $customers = json_read('customers.json') ?? [];
        $customerData = null;

        foreach ($customers as $c) {
            if ($c['uuid'] === $customer['uuid']) {
                $customerData = $c;
                break;
            }
        }

        return view('front/checkout/index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'customer' => $customerData,
        ]);
    }

    /**
     * Process checkout and create order
     * 
     * Route: POST /checkout/process
     */
    public static function process()
    {
        // Require authentication
        CustomerAuth::require();

        $customer = CustomerAuth::customer();

        // Validate cart
        $cart = $_SESSION['cart'] ?? ['items' => [], 'total_qty' => 0];

        if (empty($cart['items'])) {
            $_SESSION['error'] = 'Keranjang belanja kosong';
            return redirect('/cart');
        }

        // Get form data
        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $provinsi = trim($_POST['provinsi'] ?? '');
        $kabupaten = trim($_POST['kabupaten'] ?? '');
        $kecamatan = trim($_POST['kecamatan'] ?? '');
        $kodePos = trim($_POST['kode_pos'] ?? '');
        $catatan = trim($_POST['catatan'] ?? '');

        // Shipping cost (manual input or from API)
        $ongkir = (int) ($_POST['ongkir'] ?? 0);

        // Payment method
        $paymentMethod = $_POST['payment_method'] ?? 'manual_transfer';

        // Validation
        if (empty($fullname) || empty($phone) || empty($alamat) || empty($provinsi) || empty($kabupaten)) {
            $_SESSION['error'] = 'Data alamat pengiriman belum lengkap';
            return redirect('/checkout');
        }

        // Load products
        $products = json_read('products.json') ?? [];

        // Build order items
        $orderItems = [];
        $subtotal = 0;

        foreach ($cart['items'] as $cartItem) {
            foreach ($products as $p) {
                if ($p['slug_uuid'] === $cartItem['slug_uuid']) {
                    $lineTotal = $p['price'] * $cartItem['qty'];
                    $subtotal += $lineTotal;

                    $orderItems[] = [
                        'product_slug_uuid' => $p['slug_uuid'],
                        'product_title' => $p['title'],
                        'product_image' => $p['images'][0] ?? null,
                        'price' => $p['price'],
                        'qty' => $cartItem['qty'],
                        'color' => $cartItem['color'],
                        'size' => $cartItem['size'],
                        'line_total' => $lineTotal,
                    ];
                    break;
                }
            }
        }

        // Calculate total
        $total = $subtotal + $ongkir;

        // Generate order ID
        $orderId = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
        $now = date('Y-m-d H:i:s');

        // Create order
        $order = [
            'order_id' => $orderId,
            'customer_uuid' => $customer['uuid'],
            'customer_name' => $fullname,
            'customer_email' => $customer['email'],
            'customer_phone' => $phone,

            // Shipping address
            'shipping_address' => [
                'fullname' => $fullname,
                'phone' => $phone,
                'alamat' => $alamat,
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'kecamatan' => $kecamatan,
                'kode_pos' => $kodePos,
            ],

            // Order items
            'items' => $orderItems,
            'item_count' => count($orderItems),

            // Pricing
            'subtotal' => $subtotal,
            'shipping_cost' => $ongkir,
            'total' => $total,

            // Payment
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending', // pending | paid | failed
            'payment_proof' => null,
            'payment_verified_at' => null,

            // Status
            'status' => 'waiting_payment', // waiting_payment | processing | shipped | delivered | cancelled
            'notes' => $catatan,

            // Timestamps
            'created_at' => $now,
            'updated_at' => null,
            'cancelled_at' => null,
        ];

        // Save order
        $orders = json_read('orders.json') ?? [];
        $orders[] = $order;
        json_write('orders.json', $orders);

        // Clear cart
        $_SESSION['cart'] = [
            'items' => [],
            'total_qty' => 0,
        ];

        // Redirect to payment page
        $_SESSION['success'] = 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.';
        return redirect('/order/' . $orderId);
    }

    /**
     * Calculate shipping cost (RajaOngkir integration - optional)
     * 
     * Route: POST /checkout/calculate-shipping
     */
    public static function calculateShipping()
    {
        // For now, return manual ongkir
        // TODO: Integrate with RajaOngkir API

        $destination = $_POST['kabupaten'] ?? '';
        $weight = (int) ($_POST['weight'] ?? 1000); // in grams

        // Dummy calculation (replace with real API call)
        $ongkir = 25000; // Default Rp 25k

        json_response([
            'success' => true,
            'shipping_cost' => $ongkir,
            'courier' => 'JNE REG',
            'etd' => '3-5 hari',
        ]);
    }
}