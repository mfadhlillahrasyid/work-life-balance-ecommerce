<?php
$page_title = htmlspecialchars($product['title']);
ob_start();
?>

<div class="border-b border-neutral-200">
    <div class="w-full">
        <div class="flex items-center justify-between w-full">
            <div class="p-4">
                <a href="<?= htmlspecialchars($backUrl) ?>"
                    class="flex items-center justify-center w-10 h-10 rounded-full shrink-0 bg-neutral-100 hover:bg-neutral-200 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>

                </a>
            </div>
            <div class="block p-4">
                <div class="flex justify-end">
                    <?php breadcrumb([
                        'Home' => '/',
                        'Shop' => '/shop',
                        $gender['title'] => '/shop/' . $gender['slug'],
                        $category['title'] => '/shop/' . $gender['slug'] . '?category[]=' . $category['slug'],
                        $product['title'] => null,
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="border-b border-neutral-200">
    <div class="max-w-[100rem] mx-auto border-x border-neutral-200">
        <div class="grid grid-cols-1 md:grid-cols-2 divide-x-0 sm:divide-x divide-neutral-200">
            <div>
                <div class="aspect-square bg-gray-100 overflow-hidden">
                    <img id="mainProductImage" src="/storage/products/<?= htmlspecialchars($product['images'][0]) ?>"
                        alt="<?= htmlspecialchars($product['title']) ?>"
                        class="w-full h-full object-cover transition duration-300">
                </div>

                <?php if (count($product['images']) > 1): ?>
                    <div class="relative my-1 border-b sm:border-b-0 border-neutral-200">

                        <!-- LEFT ARROW -->
                        <button type="button" data-thumb-prev
                            class="absolute left-4 sm:-left-4 top-1/2 -translate-y-1/2 z-10 bg-white hidden border rounded-full w-8 h-8 text-lg text-neutral-500 sm:flex items-center justify-center shadow">
                            &laquo;
                        </button>

                        <!-- RIGHT ARROW -->
                        <button type="button" data-thumb-next
                            class="absolute right-4 sm:-right-4 top-1/2 -translate-y-1/2 z-10 bg-white hidden border rounded-full w-8 h-8 text-lg text-neutral-500 sm:flex items-center justify-center shadow">
                            &raquo;
                        </button>

                        <div id="thumbnailContainer" class="flex gap-2 overflow-x-auto scroll-smooth p-1">
                            <?php foreach ($product['images'] as $index => $img): ?>
                                <img src="/storage/products/<?= htmlspecialchars($img) ?>" loading="lazy"
                                    data-full="/storage/products/<?= htmlspecialchars($img) ?>" class="w-20 h-20 sm:w-44 sm:h-44 aspect-square object-cover rounded-lg border cursor-pointer shrink-0 transition-all duration-100
                                        <?= $index === 0 ? 'ring-2 ring-black' : '' ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- INFO -->
            <div class="flex flex-col">
                <div class="border-b border-neutral-200 p-6 sm:p-8 flex flex-col gap-4">
                    <div class="w-full">
                        <span
                            class="inline-flex bg-neutral-100 border border-neutral-200 rounded-lg py-1 px-3 tracking-tight text-neutral-500 font-medium text-xs sm:text-sm">
                            <?= htmlspecialchars($category['title']) ?>
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-4xl tracking-tight font-bold">
                        <?= htmlspecialchars($product['title']) ?>
                    </h1>
                </div>

                <div class="border-b border-neutral-200 p-6 sm:p-8 flex items-center justify-between">
                    <p class="text-xl sm:text-2xl font-extrabold">
                        IDR
                        <?= number_format($product['price'], 0, ',', '.') ?>
                    </p>
                    <div class="flex items-center gap-2">
                        <p class="text-xs tracking-tight text-neutral-400">Stock :</p>
                        <p class="text-xl tracking-tight font-bold text-neutral-800">
                            <?= htmlspecialchars($product['stock']) ?>
                        </p>
                    </div>
                </div>

                <input type="hidden" id="productSlugUuid" value="<?= htmlspecialchars($product['slug_uuid']) ?>">
                <input type="hidden" id="selectedColor">
                <input type="hidden" id="selectedSize">

                <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-x sm:divide-y-0 divide-neutral-200 border-b border-neutral-200">
                    <?php if (!empty($product['colors'])): ?>
                        <div class="flex flex-col gap-3 p-6 sm:p-8">
                            <p class="text-base tracking-tight font-semibold">Color</p>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($product['colors'] as $c): ?>
                                    <button type="button" data-color="<?= htmlspecialchars($c) ?>"
                                        class="variant-color px-3 py-1 border rounded-lg text-sm capitalize hover:border-black transition">
                                        <?= htmlspecialchars($c) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($product['sizes'])): ?>
                        <div class="flex flex-col gap-3 p-6 sm:p-8">
                            <p class="text-base tracking-tight font-semibold">Size</p>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($product['sizes'] as $s): ?>
                                    <button type="button" data-size="<?= htmlspecialchars($s) ?>"
                                        class="variant-size px-3 py-1 border rounded-lg text-sm uppercase hover:border-black transition">
                                        <?= htmlspecialchars($s) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 p-4 border-b border-neutral-200">
                    <button type="submit" id="addToCartBtn"
                        class="flex-1 bg-black text-white py-4 rounded-xl text-sm sm:text-base font-medium hover:bg-neutral-800">
                        Add to Cart
                    </button>

                    <a href="https://wa.me/62XXXXXXXXXX" target="_blank"
                        class="flex-1 border border-black py-4 rounded-xl text-sm sm:text-base font-medium text-center hover:bg-neutral-100">
                        Hubungi Kami
                    </a>
                </div>

                <!-- DESCRIPTION -->
                <div class="flex flex-col gap-3 p-6 sm:p-8">
                    <p class="text-base sm:text-lg tracking-tight font-semibold">Descriptions</p>
                    <div class="prose max-w-none">
                        <?= $product['description'] ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/front.php';
