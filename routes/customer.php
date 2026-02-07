<?php

use App\Controllers\Customer\AuthController;
use App\Middleware\CustomerAuth;

// ==========================================
// CUSTOMER AUTH ROUTES (Guest Only)
// ==========================================

// Login Form (Guest only)
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^account/login$#',
    'action' => function() {
        CustomerAuth::requireGuest(); // ← FIX: Pakai requireGuest() bukan guest()
        return AuthController::loginForm();
    }
];

// Login Process
$routes[] = [
    'method' => 'POST',
    'pattern' => '#^account/login$#',
    'action' => [AuthController::class, 'login']
];

// Register Form (Guest only)
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^account/register$#',
    'action' => function() {
        CustomerAuth::requireGuest(); // ← FIX: Pakai requireGuest() bukan guest()
        return AuthController::registerForm();
    }
];

// Register Process
$routes[] = [
    'method' => 'POST',
    'pattern' => '#^account/register$#',
    'action' => [AuthController::class, 'register']
];

// ==========================================
// CUSTOMER DASHBOARD (Protected)
// ==========================================

// Dashboard
$routes[] = [
    'method' => 'GET',
    'pattern' => '#^account/dashboard$#',
    'action' => function() {
        CustomerAuth::require(); // ← Protected route
        // Return dashboard view
        return view('account/dashboard');
    }
];

// Logout
$routes[] = [
    'method' => 'POST',
    'pattern' => '#^account/logout$#',
    'action' => [AuthController::class, 'logout']
];

// ==========================================
// ORDERS (Protected)
// ==========================================
use App\Controllers\Front\OrderController;

$routes[] = [
    'method' => 'GET',
    'pattern' => '#^account/orders$#',
    'action' => [OrderController::class, 'index']
];