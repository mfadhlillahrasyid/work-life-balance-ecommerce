<?php if (!isset($page_title))
    $page_title = 'Admin'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($page_title) ?> - WLB Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.css" rel="stylesheet" />

</head>

<body class="bg-gray-100 font-inter">

    <div class="flex min-h-screen">

        <?php require __DIR__ . '/../partials/admin/sidebar.php'; ?>

        <div class="flex-1">
            <?php require __DIR__ . '/../partials/admin/header.php'; ?>

            <main class="sm:ms-64 p-0 sm:p-6 max-w-full mx-auto mt-20 sm:mt-14 overflow-x-hidden">
                <?= $content ?>
            </main>
        </div>
    </div>

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

                <a href="/admin/logout" class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700">
                    Logout
                </a>
            </div>
        </div>
    </div>

    <script src="/assets/js/admin.js"></script>
    <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof CKEDITOR !== "undefined") {
                CKEDITOR.replace("content", {
                    height: 300,
                    toolbarGroups: [
                        { name: "basicstyles", groups: ["basicstyles"] },
                        { name: "paragraph", groups: ["list", "blocks"] },
                        { name: "links" },
                        { name: "styles" },
                        { name: "colors" },
                        { name: "clipboard", groups: ["clipboard", "undo"] },
                    ],
                    removeButtons: "Image,Flash,Table,HorizontalRule,SpecialChar"
                });

            }
        });
    </script>
</body>

</html>