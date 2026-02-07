<?php

global $routes;

use App\Controllers\Front\HomeController;
use App\Controllers\Front\CartController;
use App\Controllers\Front\ShopController;
use App\Controllers\Front\ArticleController;
use App\Controllers\Front\CheckoutController;
use App\Controllers\Front\OrderController;
use App\Controllers\Api\SearchController;

// ===============================
// FRONTEND ROUTES
// ===============================

// HOME
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^$#',
    'action' => [HomeController::class, 'index'],
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^api/search$#',
    'action' => [SearchController::class, 'search']
];


// HOME
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^shop$#',
    'action' => [ShopController::class, 'index'],
];

// Product by Gender
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^shop/([a-z0-9\-]+)$#',
    'action' => [ShopController::class, 'byGender'],
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^shop/category/([a-z0-9\-]+)$#',
    'action' => [ShopController::class, 'byCategoryOnly']
];

// product by Category
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^shop/([a-z0-9\-]+)/([a-z0-9\-]+)$#',
    'action' => [ShopController::class, 'byCategory'],
];

// product Show
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^shop/([a-z0-9\-]+)/([a-z0-9\-]+)/([a-z0-9\-]+)$#',
    'action' => [ShopController::class, 'show']
];

// Cart
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^cart$#',
    'action' => [CartController::class, 'index'],
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^cart/add$#',
    'action' => [CartController::class, 'add'],
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^cart/update$#',  // ← TAMBAHIN / di depan
    'action' => [CartController::class, 'updateQty'],
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^cart/remove$#',  // ← TAMBAHIN / di depan
    'action' => [CartController::class, 'remove'],
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^cart/clear$#',
    'action' => function() {
        unset($_SESSION['cart']);
        header('Location: /cart');
        exit;
    },
];

// ==========================================
// CHECKOUT ROUTES (PROTECTED!)
// ==========================================
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^checkout$#',
    'action' => [CheckoutController::class, 'index']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^checkout/process$#',
    'action' => [CheckoutController::class, 'process']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^checkout/calculate-shipping$#',
    'action' => [CheckoutController::class, 'calculateShipping']
];

// ==========================================
// ORDER ROUTES (PROTECTED!)
// ==========================================
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^order/([A-Z0-9\-]+)$#',
    'action' => [OrderController::class, 'show']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^order/([A-Z0-9\-]+)/upload-payment$#',
    'action' => [OrderController::class, 'uploadPayment']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^account/orders$#',
    'action' => [OrderController::class, 'index']
];

// Articles
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^articles$#',
    'action' => [ArticleController::class, 'index'],
];

// $routes[] = [
//     'method' => 'GET',
//     'pattern' => '#^blog/([a-z0-9\-]+)$#',
//     'action' => [BlogController::class, 'show'],
// ];
