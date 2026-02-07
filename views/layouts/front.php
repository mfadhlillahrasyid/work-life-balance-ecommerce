<?php if (!isset($page_title))
    $page_title = 'Work Life Balance Apparel';

$cartQty = $_SESSION['cart']['total_qty'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-neutral-50 font-inter text-neutral-700">

    <div class="sticky top-0 z-50 max-w-full">
        <?php require __DIR__ . '/../partials/front/header.php'; ?>
    </div>

    <main class="relative">
        <?= $content ?>

        <a href="/cart" class="fixed bottom-6 right-6 z-50 group sm:hidden">

            <div class="relative flex items-center justify-center
                w-16 h-16 rounded-xl
                bg-neutral-100 border border-neutral-200 text-neutral-800
                shadow-lg group-hover:bg-indigo-100 hover:border-indigo-300
                hover:scale-105 transition-transform">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" stroke-width="2"
                    class="size-9 transition-all duration-200 group-hover:text-indigo-700 <?= is_active_group('/cart') ? 'text-indigo-800' : 'text-neutral-900' ?>">
                    <path
                        d="M6.00488 9H19.9433L20.4433 7H8.00488V5H21.7241C22.2764 5 22.7241 5.44772 22.7241 6C22.7241 6.08176 22.7141 6.16322 22.6942 6.24254L20.1942 16.2425C20.083 16.6877 19.683 17 19.2241 17H5.00488C4.4526 17 4.00488 16.5523 4.00488 16V4H2.00488V2H5.00488C5.55717 2 6.00488 2.44772 6.00488 3V9ZM6.00488 23C4.90031 23 4.00488 22.1046 4.00488 21C4.00488 19.8954 4.90031 19 6.00488 19C7.10945 19 8.00488 19.8954 8.00488 21C8.00488 22.1046 7.10945 23 6.00488 23ZM18.0049 23C16.9003 23 16.0049 22.1046 16.0049 21C16.0049 19.8954 16.9003 19 18.0049 19C19.1095 19 20.0049 19.8954 20.0049 21C20.0049 22.1046 19.1095 23 18.0049 23Z">
                    </path>
                </svg>

                <?php if ($cartQty > 0): ?>
                    <!-- Badge -->
                    <span class="absolute -top-1 -right-1
                       min-w-[1.25rem] h-5 px-1
                       flex items-center justify-center
                       rounded-full
                       bg-red-600 text-xs font-bold text-white
                       cart-badge-ping">
                        <?= $cartQty ?>
                    </span>
                <?php endif; ?>

            </div>
        </a>

    </main>

    <footer class="border-t">
        <div class="max-w-full px-6 py-6 text-sm text-gray-500">
            © <?= date('Y') ?> Work Life Balance Apparel
        </div>
    </footer>
    <script src="/assets/js/front.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
</body>

</html>