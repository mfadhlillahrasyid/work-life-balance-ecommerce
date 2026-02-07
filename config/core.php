<?php

function dispatch()
{
    global $routes;

    if (empty($routes)) {
        http_response_code(500);
        exit('Routes not registered');
    }

    // Ambil URI bersih
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = trim($uri, '/');

    $method = $_SERVER['REQUEST_METHOD'];

    // DEBUG - HAPUS SETELAH SELESAI
    if ($method === 'POST') {
        error_log("=== ROUTE DEBUG ===");
        error_log("Original URI: " . $_SERVER['REQUEST_URI']);
        error_log("Cleaned URI: " . $uri);
        error_log("Method: " . $method);
    }

    foreach ($routes as $route) {
        if ($route['method'] !== $method) {
            continue;
        }

        // DEBUG - HAPUS SETELAH SELESAI
        if ($method === 'POST') {
            error_log("Testing pattern: " . $route['pattern']);
            if (preg_match($route['pattern'], $uri)) {
                error_log("✓ MATCH!");
            } else {
                error_log("✗ NO MATCH");
            }
        }

        if (!preg_match($route['pattern'], $uri, $matches)) {
            continue;
        }

        array_shift($matches);

        $action = $route['action'];

        if (!is_callable($action)) {
            http_response_code(500);
            exit('Invalid route action');
        }

        return call_user_func_array($action, $matches);
    }

    // 404 FALLBACK
    http_response_code(404);

    if (str_starts_with($uri, 'admin')) {
        return view('errors/404', [
            'title' => 'Admin Page Not Found',
            'back_url' => '/admin/dashboard',
        ]);
    }

    return view('errors/404', [
        'title' => 'Page Not Found',
        'back_url' => '/',
    ]);
}

// ===============================
// VIEW RENDERER
// ===============================
if (!function_exists('view')) {
    function view(string $view, array $data = [])
    {
        extract($data);

        $path = __DIR__ . '/../views/' . $view . '.php';

        if (!file_exists($path)) {
            http_response_code(500);
            exit('View not found: ' . htmlspecialchars($view));
        }

        require $path;
    }
}

// ===============================
// REDIRECT HELPER
// ===============================
if (!function_exists('redirect')) {
    function redirect(string $to)
    {
        header('Location: ' . $to);
        exit;
    }
}

function flash(string $key, string $message): void
{
    $_SESSION[$key] = $message;
}


