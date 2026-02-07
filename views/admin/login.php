<?php
$page_title = 'Admin Login';
$error = $_SESSION['admin_error'] ?? null;
unset($_SESSION['admin_error']);

ob_start();
?>
<div class="min-h-screen grid grid-cols-1 md:grid-cols-2">

    <!-- LEFT: Branding / Info -->
    <div class="hidden md:flex flex-col justify-center bg-black text-white p-12">
        <h1 class="text-4xl font-bold mb-4">
            Admin Panel
        </h1>
        <p class="text-gray-300 max-w-md">
            Secure access to manage content, projects, and system configuration.
            Authorized personnel only.
        </p>
    </div>

    <!-- RIGHT: Login Form -->
    <div class="flex flex-col items-center justify-center bg-gray-50">
        <form method="POST" action="/admin/login" class="w-full max-w-sm">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1">
                    <h2 class="text-lg font-semibold tracking-tight text-gray-900">Login to your account</h2>
                    <p class="text-sm text-gray-400">Enter your credentials below to access the dashboard</p>
                </div>

                <?php if ($error): ?>
                    <div
                        class="text-sm text-red-700 text-center py-2 rounded-lg font-medium tracking-tight bg-red-100 border border-red-300">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col gap-4 ">
                    <div class="flex flex-col gap-2">
                        <label class="block text-sm font-semibold">Email</label>
                        <input name="email" type="email" required placeholder="admin@email.com"
                            class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-white text-neutral-700">
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="block text-sm font-semibold">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                placeholder="input your password..."
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 pr-10 bg-white text-neutral-700">
                            <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                👁️
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="default-checkbox" type="checkbox"
                        class="w-4 h-4 border border-default-medium rounded-sm bg-neutral-secondary-medium focus:ring-2 focus:ring-offset-2 focus:ring-black">
                    <label for="default-checkbox" class="select-none ms-2 text-xs tracking-tight">Remember me</label>
                </div>

                <button class="w-full bg-black text-white py-2 rounded-lg hover:bg-gray-900 transition">
                    Login
                </button>

                <a href="/" class="inline-flex items-center gap-2 text-sm text-neutral-500 hover:text-indigo-600 transition-all duration-300 mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>

                    Back to Website
                </a>

            </div>
        </form>
    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/admin-auth.php';
