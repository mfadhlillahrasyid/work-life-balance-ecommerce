<?php
// ===================================
// GLOBAL ENTRY POINT (ROOT)
// ===================================
define('ROOT_PATH', __DIR__);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// load core
require_once __DIR__ . '/config/core.php';
require_once __DIR__ . '/config/util.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once ROOT_PATH . '/config/pagination.php';

// Middleware
require_once __DIR__ . '/app/Middleware/CustomerAuth.php';

require_once __DIR__ . '/app/Controllers/Front/HomeController.php';
require_once __DIR__ . '/app/Controllers/Front/ShopController.php';
require_once __DIR__ . '/app/Controllers/Front/CartController.php';
require_once __DIR__ . '/app/Controllers/Front/CheckoutController.php';
require_once __DIR__ . '/app/Controllers/Front/OrderController.php';
require_once __DIR__ . '/app/Controllers/Front/ArticleController.php';

require_once __DIR__ . '/app/Controllers/Api/SearchController.php';

require_once __DIR__ . '/app/Controllers/Customer/DashboardController.php';
require_once __DIR__ . '/app/Controllers/Customer/AuthController.php';

require_once __DIR__ . '/app/Controllers/Admin/AuthController.php';
require_once __DIR__ . '/app/Controllers/Admin/UserController.php';
require_once __DIR__ . '/app/Controllers/Admin/DashboardController.php';
require_once __DIR__ . '/app/Controllers/Admin/PostCategoryController.php';
require_once __DIR__ . '/app/Controllers/Admin/ProductCategoryController.php';
require_once __DIR__ . '/app/Controllers/Admin/ProductController.php';
require_once __DIR__ . '/app/Controllers/Admin/GenderController.php';
require_once __DIR__ . '/app/Controllers/Admin/PostController.php';

// load routes
require_once __DIR__ . '/routes/web.php';
require_once __DIR__ . '/routes/customer.php';
require_once __DIR__ . '/routes/admin.php';

// run router
dispatch();
