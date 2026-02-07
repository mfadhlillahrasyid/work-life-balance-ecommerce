<?php
$page_title = 'Apparel for ' . htmlspecialchars($gender['title']);
ob_start();
?>

<div class="border-b-0 sm:border-b border-neutral-200">
    <div class="w-full">
        <div class="grid grid-cols-1 md:grid-cols-5 items-center w-full">
            <div class="p-4 md:col-span-1 border-b sm:border-b-0 border-neutral-200">
                <div class="flex items-center justify-between">
                    <a href="/"
                        class="flex items-center justify-center w-10 h-10 rounded-full shrink-0 bg-neutral-100 hover:bg-neutral-200 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <h3 class="font-bebas tracking-tight text-3xl block sm:hidden">
                        <?= htmlspecialchars($gender['title']) ?> Product
                    </h3>
                </div>
            </div>
            <div class="border-l border-neutral-200 md:col-span-4 hidden sm:block">
                <div class="p-4 flex justify-between items-center w-full">
                    <h3 class="font-bebas tracking-tight text-3xl">
                        <?= htmlspecialchars($gender['title']) ?> Product
                    </h3>
                    <div class="flex justify-end">
                        <?php breadcrumb(['Home' => '/', 'Shop' => '/shop', 'Men' => null]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-full mx-auto p-4">
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

        <!-- FILTER (COL SPAN 1) -->
        <aside class="md:col-span-1">
            <?php include __DIR__ . '/../../components/filter.php'; ?>
        </aside>

        <!-- KATALOG (COL SPAN 3) -->
        <section class="md:col-span-5">
            <?php if (empty($products)): ?>
                <div class="text-center">
                    <p class="text-gray-500">No products found matching your filters.</p>
                    <a href="/shop" class="inline-block mt-4 text-black font-medium hover:underline">
                        Clear all filters
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                    <?php foreach ($products as $p): ?>
                        <a href="/shop/<?= htmlspecialchars($p['gender']) ?>/<?= htmlspecialchars($p['category']) ?>/<?= htmlspecialchars($p['slug_uuid']) ?>"
                            data-product-card data-title="<?= strtolower($p['title']) ?>" data-price="<?= $p['price'] ?>"
                            class="group flex flex-col gap-3">

                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                <?php if (!empty($p['thumbnail']) && file_exists(ROOT_PATH . '/storage/products/' . $p['thumbnail'])): ?>
                                    <img src="/storage/products/<?= htmlspecialchars($p['thumbnail']) ?>"
                                        alt="<?= htmlspecialchars($p['title']) ?>"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                                        No Image
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex flex-col">
                                <h3
                                    class="textsm sm:text-base tracking-tight font-semibold group-hover:text-indigo-600 transition-all duration-300 line-clamp-1">
                                    <?= htmlspecialchars($p['title']) ?>
                                </h3>
                                <p class="text-xs sm:text-sm text-gray-600">
                                    Rp
                                    <?= number_format($p['price'], 0, ',', '.') ?>
                                </p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>
</div>


<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/front.php';