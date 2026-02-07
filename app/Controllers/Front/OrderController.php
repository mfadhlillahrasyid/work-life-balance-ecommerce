<?php

namespace App\Controllers\Front;

use App\Middleware\CustomerAuth;

class OrderController
{
    /**
     * Show order detail page (protected)
     * 
     * Route: GET /order/{order_id}
     */
    public static function show(string $orderId)
    {
        // Require authentication
        CustomerAuth::require();

        $customer = CustomerAuth::customer();
        $orders = json_read('orders.json') ?? [];

        // Find order
        $order = null;
        foreach ($orders as $o) {
            if ($o['order_id'] === $orderId) {
                $order = $o;
                break;
            }
        }

        if (!$order) {
            http_response_code(404);
            return view('errors/404');
        }

        // Check ownership
        if ($order['customer_uuid'] !== $customer['uuid']) {
            http_response_code(403);
            $_SESSION['error'] = 'Anda tidak memiliki akses ke pesanan ini';
            return redirect('/account/orders');
        }

        return view('front/order/show', [
            'order' => $order,
        ]);
    }

    /**
     * Upload payment proof
     * 
     * Route: POST /order/{order_id}/upload-payment
     */
    public static function uploadPayment(string $orderId)
    {
        // Require authentication
        CustomerAuth::require();

        $customer = CustomerAuth::customer();

        // Validate file
        if (empty($_FILES['payment_proof'])) {
            $_SESSION['error'] = 'Silakan upload bukti pembayaran';
            return redirect('/order/' . $orderId);
        }

        $file = $_FILES['payment_proof'];

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = 'Format file harus JPG atau PNG';
            return redirect('/order/' . $orderId);
        }

        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = 'Ukuran file maksimal 2MB';
            return redirect('/order/' . $orderId);
        }

        // Load orders
        $orders = json_read('orders.json') ?? [];
        $orderIndex = null;

        foreach ($orders as $index => $o) {
            if ($o['order_id'] === $orderId && $o['customer_uuid'] === $customer['uuid']) {
                $orderIndex = $index;
                break;
            }
        }

        if ($orderIndex === null) {
            $_SESSION['error'] = 'Pesanan tidak ditemukan';
            return redirect('/account/orders');
        }

        // Upload file
        $uploadDir = ROOT_PATH . '/storage/payments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $orderId . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $_SESSION['error'] = 'Gagal upload file';
            return redirect('/order/' . $orderId);
        }

        // Update order
        $orders[$orderIndex]['payment_proof'] = $filename;
        $orders[$orderIndex]['updated_at'] = date('Y-m-d H:i:s');
        
        json_write('orders.json', $orders);

        $_SESSION['success'] = 'Bukti pembayaran berhasil diupload! Pesanan Anda akan segera diproses.';
        return redirect('/order/' . $orderId);
    }

    /**
     * List customer orders
     * 
     * Route: GET /account/orders
     */
    public static function index()
    {
        // Require authentication
        CustomerAuth::require();

        $customer = CustomerAuth::customer();
        $orders = json_read('orders.json') ?? [];

        // Filter by customer
        $customerOrders = array_values(array_filter($orders, function($o) use ($customer) {
            return $o['customer_uuid'] === $customer['uuid'];
        }));

        // Sort by created_at DESC
        usort($customerOrders, function($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return view('account/orders/index', [
            'orders' => $customerOrders,
        ]);
    }
}