<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="icon" href="/assets/images/favicon.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.css" rel="stylesheet" />
</head>

<body class="bg-neutral-50 font-inter text-neutral-700">
    <div class="sticky top-0 z-50 max-w-full">
        <?php require __DIR__ . '/../partials/front/header.php'; ?>
    </div>

    <main>
        <?= $content ?>
    </main>

    <!-- LOGOUT CONFIRM MODAL -->
    <div id="logoutModal" onclick="closeLogoutModal()"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4" onclick="event.stopPropagation()">
            <div class="p-4 border-b">
                <h3 class="text-lg font-semibold tracking-tight text-gray-900">
                    Logout Confirmation
                </h3>
            </div>

            <div class="p-4 text-sm text-gray-600">
                Are you sure you want to exit the admin dashboard?
            </div>

            <div class="flex justify-end gap-2 p-4 border-t">
                <button type="button" onclick="closeLogoutModal()"
                    class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-100">
                    Back
                </button>

                <form action="/account/logout" method="POST">
                    <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700">
                        Logout
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script src="/assets/js/customer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
</body>

</html>