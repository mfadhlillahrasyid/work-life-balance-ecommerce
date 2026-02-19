<?php
// Get navigation data (cached globally)
$navCategories = get_nav_categories();
$navGenders = get_nav_genders();
$currentPath = current_path();

$cartQty = $_SESSION['cart']['total_qty'] ?? 0;
?>

<header class="bg-neutral-50 border-b border-neutral-200">
    <!-- Desktiop Navigation -->
    <nav aria-label="Global" class="mx-auto flex max-w-full items-center justify-between p-4">
        <?php include __DIR__ . '/../../components/mobile_search.php'; ?>
        <div class="flex lg:flex-1">
            <a href="/" class="-m-1.5 p-1.5">
                <span class="sr-only">Your Company</span>
                <img src="/assets/images/logo-new.png" alt="Work Life Balance Apparel Logo" class="h-6 sm:h-8 w-auto" />
            </a>
        </div>
        <div class="flex lg:hidden">
            <button type="button" command="show-modal" commandfor="mobile-menu"
                class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-neutral-700">
                <span class="sr-only">Open main menu</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-7">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25" />
                </svg>

            </button>
        </div>
        <div class="hidden sm:flex flex-row items-center gap-4">

            <div class="hidden lg:flex lg:gap-x-7">
                <div class="relative" id="shopMenuWrapper">
                    <button type="button" id="shopMenuButton"
                        class="flex items-center gap-x-2 text-sm/6 font-semibold text-neutral-900 relative">
                        Shop
                        <svg id="shopMenuIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" class="size-3 text-neutral-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div id="shopDropdown"
                        class="absolute left-14 -translate-x-1/2 top-14 pt-2 opacity-0 invisible transition-all duration-200 ease-out">
                        <div
                            class="bg-neutral-50 border border-neutral-200 rounded-xl shadow-xl border border-neutral-200 overflow-hidden w-[600px]">
                            <div class="grid grid-cols-2 divide-x divide-neutral-200">
                                <div class="p-3">
                                    <h3 class="text-xs font-semibold text-neutral-500 capitalize px-3 py-2 mb-4">
                                        By
                                        Category</h3>
                                    <div class="space-y-1">
                                        <?php if (!empty($navCategories)): ?>
                                            <?php foreach ($navCategories as $cat): ?>
                                                <a href="/shop/category/<?= htmlspecialchars($cat['slug']) ?>"
                                                    class="flex items-center gap-3 p-1 rounded-lg hover:bg-black transition-colors group">
                                                    <?php if (!empty($cat['icon']) && file_exists(ROOT_PATH . '/storage/icons/' . $cat['icon'])): ?>
                                                        <div
                                                            class="flex-shrink-0 w-10 h-10 bg-neutral-100 rounded-md flex items-center justify-center group-hover:bg-white transition-colors overflow-hidden">
                                                            <img src="/storage/icons/<?= htmlspecialchars($cat['icon']) ?>"
                                                                alt="<?= htmlspecialchars($cat['title']) ?>"
                                                                class="w-full aspect-square object-cover">
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="flex-1 min-w-0">
                                                        <p
                                                            class="text-sm font-semibold text-neutral-900 capitalize group-hover:text-neutral-300 transition-all duration-200">
                                                            <?= htmlspecialchars($cat['title']) ?>
                                                        </p>
                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <h3 class="text-xs font-semibold text-neutral-500 capitalize px-3 py-2 mb-4">
                                        By
                                        Gender</h3>
                                    <div class="space-y-1 pb-4">
                                        <?php if (!empty($navGenders)): ?>
                                            <?php foreach ($navGenders as $gender): ?>
                                                <a href="/shop/<?= htmlspecialchars($gender['slug']) ?>"
                                                    class="block p-3 rounded-lg hover:bg-black transition-colors group">
                                                    <p
                                                        class="text-sm font-semibold text-neutral-900 capitalize group-hover:text-white transition-colors">
                                                        <?= htmlspecialchars($gender['title']) ?> Collection
                                                    </p>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="pt-4 border-t border-neutral-100">
                                        <a href="/shop"
                                            class="flex items-center justify-between p-3 rounded-lg hover:bg-indigo-700 transition-colors group">
                                            <span
                                                class="text-sm font-semibold text-neutral-900 group-hover:text-white transition-colors">View
                                                All
                                                Products</span>
                                            <svg class="w-4 h-4 text-neutral-600 group-hover:text-white transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="#" class="text-sm/6 font-semibold text-neutral-900">Collection</a>
                <a href="#" class="text-sm/6 font-semibold text-neutral-900">Our Store</a>
                <a href="/articles" class="text-sm/6 font-semibold text-neutral-900">Articles</a>
            </div>

            <div class="mx-4 w-px h-6 bg-neutral-300"></div>

            <div class="flex flex-row gap-3 items-center">
                <?php if (!empty($_SESSION['customer'])): ?>
                    <a href="/account/dashboard"
                        class="group hidden lg:flex lg:flex-1 lg:justify-center items-center w-12 h-12 border rounded-lg shrink-0 rounded-lg shrink-0 transition-all duration-200 cursor-pointer hover:bg-indigo-100 hover:border-indigo-300 <?= is_active_group('/account/dashboard') ? 'bg-indigo-100 border-indigo-300 text-white' : 'bg-neutral-100 border-neutral-200' ?>">
                        <div class="text-sm/6 font-semibold text-neutral-900">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="size-6 transition-all duration-200 group-hover:text-indigo-700 <?= is_active_group('/account/dashboard') ? 'text-indigo-800' : 'text-neutral-900' ?>">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                            </svg>
                        </div>
                    </a>
                <?php else: ?>
                    <a href="/account/login"
                        class="group hidden lg:flex lg:flex-1 lg:justify-center items-center w-12 h-12 border rounded-lg shrink-0 rounded-lg shrink-0 transition-all duration-200 cursor-pointer hover:bg-indigo-100 hover:border-indigo-300 <?= is_active_group('/account/login') ? 'bg-indigo-100 border-indigo-300 text-white' : 'bg-neutral-100 border-neutral-200' ?>">
                        <div class="text-sm/6 font-semibold text-neutral-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-neutral-900 group-hover:text-indigo-700 transition-all duration-200">
                                <path
                                    d="M20 22H4V20C4 17.2386 6.23858 15 9 15H15C17.7614 15 20 17.2386 20 20V22ZM12 13C8.68629 13 6 10.3137 6 7C6 3.68629 8.68629 1 12 1C15.3137 1 18 3.68629 18 7C18 10.3137 15.3137 13 12 13Z">
                                </path>
                            </svg>
                        </div>
                    </a>
                <?php endif; ?>
                <button type="button" id="searchTriggerDesktop"
                    class="group hidden lg:flex lg:flex-1 lg:justify-center items-center w-12 h-12 border rounded-lg shrink-0 rounded-lg shrink-0 transition-all duration-200 cursor-pointer hover:bg-indigo-100 hover:border-indigo-300">
                    <div class="text-sm/6 font-semibold text-neutral-900">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="size-6 text-neutral-900 group-hover:text-indigo-700 transition-all duration-200">
                            <path
                                d="M18.031 16.6168L22.3137 20.8995L20.8995 22.3137L16.6168 18.031C15.0769 19.263 13.124 20 11 20C6.032 20 2 15.968 2 11C2 6.032 6.032 2 11 2C15.968 2 20 6.032 20 11C20 13.124 19.263 15.0769 18.031 16.6168ZM16.0247 15.8748C17.2475 14.6146 18 12.8956 18 11C18 7.1325 14.8675 4 11 4C7.1325 4 4 7.1325 4 11C4 14.8675 7.1325 18 11 18C12.8956 18 14.6146 17.2475 15.8748 16.0247L16.0247 15.8748Z">
                            </path>
                        </svg>
                    </div>
                </button>
                <a href="/cart"
                    class="group relative hidden lg:flex lg:flex-1 lg:justify-center items-center w-12 h-12 border rounded-lg shrink-0 rounded-lg shrink-0 transition-all duration-200 cursor-pointer hover:bg-indigo-100 hover:border-indigo-300 <?= is_active_group('/cart') ? 'bg-indigo-100 border-indigo-300 text-white' : 'bg-neutral-100 border-neutral-200' ?>">
                    <div class="text-sm/6 font-semibold text-neutral-900">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="size-6 transition-all duration-200 group-hover:text-indigo-700 <?= is_active_group('/cart') ? 'text-indigo-800' : 'text-neutral-900' ?>">
                            <path
                                d="M9 6C9 4.34315 10.3431 3 12 3C13.6569 3 15 4.34315 15 6H9ZM7 6H4C3.44772 6 3 6.44772 3 7V21C3 21.5523 3.44772 22 4 22H20C20.5523 22 21 21.5523 21 21V7C21 6.44772 20.5523 6 20 6H17C17 3.23858 14.7614 1 12 1C9.23858 1 7 3.23858 7 6ZM9 10C9 11.6569 10.3431 13 12 13C13.6569 13 15 11.6569 15 10H17C17 12.7614 14.7614 15 12 15C9.23858 15 7 12.7614 7 10H9Z">
                            </path>
                        </svg>
                    </div>
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
                </a>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <el-dialog>
        <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
            <div tabindex="0" class="fixed inset-0 focus:outline-none">
                <el-dialog-panel
                    class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white p-4 sm:max-w-sm sm:ring-1 sm:ring-neutral-900/10">
                    <div class="flex items-center justify-between">
                        <?php include __DIR__ . '/../../components/mobile_search.php'; ?>
                        <a href="/" class="-m-1.5 p-1.5">
                            <span class="sr-only">Your Company</span>
                            <img src="/assets/images/logo-new.png" alt="" class="h-6 w-auto" />
                        </a>
                        <button type="button" command="close" commandfor="mobile-menu"
                            class="-m-2.5 rounded-md p-2.5 text-neutral-700">
                            <span class="sr-only">Close menu</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                data-slot="icon" aria-hidden="true" class="size-7">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-neutral-500/10">
                            <div class="space-y-2 py-6 px-2">
                                <div class="">
                                    <button type="button" command="--toggle" commandfor="products"
                                        class="flex w-full items-center justify-between rounded-lg py-2 pr-3.5 pl-3 text-base/7 font-semibold text-neutral-900 hover:bg-neutral-50">
                                        Shop
                                        <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                            class="size-5 flex-none in-aria-expanded:rotate-180">
                                            <path
                                                d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                                                clip-rule="evenodd" fill-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <el-disclosure id="products" hidden class="mt-2 block space-y-2">
                                        <h3 class="text-xs py-2 pr-3 pl-6 font-semibold text-neutral-500 capitalize">
                                            By
                                            Category</h3>
                                        <?php if (!empty($navCategories)): ?>
                                            <?php foreach ($navCategories as $cat): ?>
                                                <a href="/shop/category/<?= htmlspecialchars($cat['slug']) ?>"
                                                    class="block rounded-lg py-2 pr-3 pl-6 text-sm/7 font-semibold text-neutral-900 hover:bg-neutral-50">
                                                    <?= htmlspecialchars($cat['title']) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <h3 class="text-xs py-2 pr-3 pl-6 font-semibold text-neutral-500 capitalize">
                                            By
                                            Gender</h3>
                                        <?php if (!empty($navGenders)): ?>
                                            <?php foreach ($navGenders as $gender): ?>
                                                <a href="/shop/<?= htmlspecialchars($gender['slug']) ?>"
                                                    class="block rounded-lg py-2 pr-3 pl-6 text-sm/7 font-semibold text-neutral-900 hover:bg-neutral-50">
                                                    <?= htmlspecialchars($gender['title']) ?> Collection
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </el-disclosure>
                                </div>
                                <a href="#"
                                    class=" block rounded-lg px-3 py-2 text-base/7 font-semibold text-neutral-900 hover:bg-neutral-50">Features</a>
                                <a href="#"
                                    class=" block rounded-lg px-3 py-2 text-base/7 font-semibold text-neutral-900 hover:bg-neutral-50">Marketplace</a>
                                <a href="#"
                                    class=" block rounded-lg px-3 py-2 text-base/7 font-semibold text-neutral-900 hover:bg-neutral-50">Company</a>
                            </div>
                            <div class="py-6">
                                <a href="#"
                                    class=" block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-neutral-900 hover:bg-neutral-50">Log
                                    in</a>
                            </div>
                        </div>
                    </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>
</header>

<!-- Algolia-Style Search Modal -->
<div id="searchModal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
    <div id="searchBackdrop" class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <div id="searchPanel"
            class="mx-auto max-w-2xl transform divide-y divide-neutral-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-neutral-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="searchInput"
                    class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-neutral-900 placeholder:text-neutral-400 focus:ring-0 text-xs sm:text-sm"
                    placeholder="Search products..." autocomplete="off">
                <button type="button" id="searchClose"
                    class="absolute right-4 top-3.5 text-neutral-400 hover:text-neutral-500">
                    <span class="text-xs font-medium">ESC</span>
                </button>
            </div>
            <div id="searchResults" class="max-h-96 overflow-y-auto p-2"></div>
            <div
                class="flex items-center justify-between px-4 py-3 text-xs text-neutral-500 border-t border-neutral-100">
                <span>Powered by Work Life Balance</span>
            </div>
        </div>
    </div>
</div>