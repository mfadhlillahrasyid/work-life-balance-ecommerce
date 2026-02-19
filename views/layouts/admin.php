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
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
    <script src="/assets/tinymce/js/tinymce/tinymce.min.js"></script>
    <!-- <script src="https://cdn.tiny.cloud/1/hxg2s74rz0j3b5nuvdfw6db9ovcekysr7hos85l9bz3a3rp5/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script> -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Initialize TinyMCE on textarea with id="content"
            if (typeof tinymce !== "undefined") {
                tinymce.init({
                    selector: '#content', // Target textarea with id="content"

                    // Height
                    height: 400,

                    // Menubar
                    menubar: false,

                    // Plugins
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'table', 'help', 'wordcount'
                    ],

                    // Content style
                    content_style: 'body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size:14px }',

                    // Paste settings (clean paste dari Word/Google Docs)
                    paste_as_text: false,
                    paste_enable_default_filters: true,

                    // Branding
                    branding: false, // Remove "Powered by TinyMCE"

                    // Promotion (remove upgrade message)
                    promotion: false,

                    // Setup callback (optional)
                    setup: function (editor) {
                        editor.on('init', function () {
                            console.log('TinyMCE initialized successfully');
                        });
                    }
                });
            }
        });
    </script>
</body>

</html>