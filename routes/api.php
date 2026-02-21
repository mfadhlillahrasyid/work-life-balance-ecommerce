<?php

global $routes;

use App\Controllers\Api\WilayahController;
use App\Controllers\Api\SearchController;
use App\Controllers\Api\ShippingController;


$routes[] = [
    'method' => 'GET',
    'pattern' => '#^api/search$#',
    'action' => [SearchController::class, 'search']
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^api/wilayah/provinces$#',
    'action' => [WilayahController::class, 'provinces'],
];

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^api/wilayah/regencies$#',
    'action' => [WilayahController::class, 'regencies'],
];

$routes[] = [
    'method'  => 'GET',
    'pattern' => '#^api/shipping/cost$#',
    'action'  => [ShippingController::class, 'cost'],
];