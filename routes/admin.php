<?php

global $routes;

use App\Controllers\Admin\PostCategoryController;
use App\Controllers\Admin\ProductCategoryController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\GenderController;
use App\Controllers\Admin\PostController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;


/*
|--------------------------------------------------------------------------
| ADMIN ROOT
|--------------------------------------------------------------------------
*/
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin$#',
    'action' => [AuthController::class, 'redirect']
];

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/login$#',
    'action' => [AuthController::class, 'showLogin']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/login$#',
    'action' => [AuthController::class, 'login']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/logout$#',
    'action' => [AuthController::class, 'logout']
];

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/dashboard$#',
    'action' => [DashboardController::class, 'index']
];

/*
|--------------------------------------------------------------------------
| POST CATEGORIES
|--------------------------------------------------------------------------
*/
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/post-categories$#',
    'action' => [PostCategoryController::class, 'index']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/post-categories/create$#',
    'action' => [PostCategoryController::class, 'create']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/post-categories$#',
    'action' => [PostCategoryController::class, 'store']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/post-categories/([a-z0-9\-]+)/edit$#',
    'action' => [PostCategoryController::class, 'edit']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/post-categories/([a-z0-9\-]+)/update$#',
    'action' => [PostCategoryController::class, 'update']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/post-categories/([a-z0-9\-]+)/delete$#',
    'action' => [PostCategoryController::class, 'destroy']
];

/*
|--------------------------------------------------------------------------
| GENDER CATEGORIES
|--------------------------------------------------------------------------
*/
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/genders$#',
    'action' => [GenderController::class, 'index']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/genders/create$#',
    'action' => [GenderController::class, 'create']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/genders$#',
    'action' => [GenderController::class, 'store']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/genders/([a-z0-9\-]+)/edit$#',
    'action' => [GenderController::class, 'edit']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/genders/([a-z0-9\-]+)/update$#',
    'action' => [GenderController::class, 'update']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/genders/([a-z0-9\-]+)/delete$#',
    'action' => [GenderController::class, 'destroy']
];


/*
|--------------------------------------------------------------------------
| PRODUCT CATEGORIES
|--------------------------------------------------------------------------
*/
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/product-categories$#',
    'action' => [ProductCategoryController::class, 'index']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/product-categories/create$#',
    'action' => [ProductCategoryController::class, 'create']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/product-categories$#',
    'action' => [ProductCategoryController::class, 'store']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/product-categories/([a-z0-9\-]+)/edit$#',
    'action' => [ProductCategoryController::class, 'edit']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/product-categories/([a-z0-9\-]+)/update$#',
    'action' => [ProductCategoryController::class, 'update']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/product-categories/([a-z0-9\-]+)/delete$#',
    'action' => [ProductCategoryController::class, 'destroy']
];

/*
|--------------------------------------------------------------------------
| POSTS
|--------------------------------------------------------------------------
*/
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/posts$#',
    'action' => [PostController::class, 'index']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/posts/create$#',
    'action' => [PostController::class, 'create']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/posts$#',
    'action' => [PostController::class, 'store']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/posts/([a-z0-9\-]+)/edit$#',
    'action' => [PostController::class, 'edit']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/posts/([a-z0-9\-]+)/update$#',
    'action' => [PostController::class, 'update']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/posts/([a-z0-9\-]+)/delete$#',
    'action' => [PostController::class, 'destroy']
];

/*
|--------------------------------------------------------------------------
| PRODUCTS
|--------------------------------------------------------------------------
*/
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/products$#',
    'action' => [ProductController::class, 'index']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/products/create$#',
    'action' => [ProductController::class, 'create']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/products$#',
    'action' => [ProductController::class, 'store']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^admin/products/([a-z0-9\-]+)/edit$#',
    'action' => [ProductController::class, 'edit']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/products/([a-z0-9\-]+)/update$#',
    'action' => [ProductController::class, 'update']
];

$routes[] = [
    'method' => 'POST',
    'pattern' => '#^admin/products/([a-z0-9\-]+)/delete$#',
    'action' => [ProductController::class, 'destroy']
];