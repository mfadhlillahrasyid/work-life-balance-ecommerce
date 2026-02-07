<?php $page_title = 'My Account';
ob_start();
?>

<div class="min-h-full">
    <div class="border-b border-neutral-200">
        <div class="w-full">
            <div class="grid grid-cols-1 md:grid-cols-5 items-center w-full">
                <div class="p-4 md:col-span-1 border-b sm:border-b-0 border-neutral-200">
                    <a href="/"
                        class="flex items-center justify-center w-10 h-10 rounded-full shrink-0 bg-neutral-100 hover:bg-neutral-200 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>

                    </a>
                </div>
                <div class="border-l border-neutral-200 md:col-span-4">
                    <div class="p-4 flex justify-between items-center w-full">
                        <h3 class="font-bebas tracking-tight text-3xl">
                            Customer Dashboard
                        </h3>
                        <div class="flex justify-end">
                            <?php breadcrumb(['Home' => '/', 'Dashboard' => null]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <main>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="block">
                <a href="#" onclick="openLogoutModal(event)"
                    class="inline-flex items-center w-full p-2 hover:bg-red-100 rounded-lg hover:text-red-800 transition-all duration-200"
                    role="menuitem">
                    Sign out
                </a>
            </div>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/customer.php';