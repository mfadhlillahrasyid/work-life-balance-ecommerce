<?php $page_title = 'WORK LIFE BALANCE | Your Everyday Lifestyle Goes on 🔥';
ob_start();
?>

<div class="border-b border-neutral-200">
    <div class="relative mx-auto w-full min-h-[70vh] sm:min-h-[90vh] items-center flex bg-cover bg-center bg-no-repeat z-0"
        style="background-image: url('https://insurgentclub.com/cdn/shop/files/insurgent_2026_the-idealist__banner_web_jpg.jpg?v=1769222982');">
        <div class="absolute inset-0 bg-black/30 z-10"></div>
    </div>
</div>

<div class="border-b border-neutral-200">
    <div class="max-w-full mx-auto border-x border-neutral-200">
        <?php if (!empty($genders)): ?>
            <section class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 p-2 gap-2 sm:p-4 sm:gap-4">
                <?php foreach ($genders as $gender): ?>
                    <a href="/shop/<?= htmlspecialchars($gender['slug']) ?>" class="relative overflow-hidden group rounded-xl">
                        <img src="/storage/banners/<?= htmlspecialchars($gender['banner']) ?>" alt="T-Shirt"
                            class="w-full aspect-video sm:aspect-square h-full scale-105 group-hover:scale-110 transition-all duration-300 object-cover">
                        <div class="absolute inset-0 bg-black/30 transition-all duration-300">
                        </div>
                        <div class="absolute inset-0 flex flex-col p-5 items-start justify-end text-white">
                            <h2 class="text-2xl sm:text-5xl tracking-tight uppercase font-bebas">
                                <?= htmlspecialchars($gender['title']) ?>
                            </h2>
                            <!-- <p class="text-sm mt-1 text-neutral-200 max-w-xs">Simpel tapi standout. Nyaman dipakai ke mana aja,
                                cocok buat gaya.</p> -->
                        </div>
                    </a>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <div class="m-4 px-4 py-10 border border-neutral-200 rounded-xl">
                <div class="flex flex-col items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-10">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                    </svg>
                    <p class="text-sm font-medium text-center text-gray-500">Website Sedang Dalam Maintenance</p>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<section class="py-16">
    <h1 class="text-4xl font-bold mb-4">Work Life Balance Apparel</h1>
    <p class="text-gray-600 max-w-xl">
        Apparel yang dirancang untuk gaya hidup seimbang antara kerja dan hidup.
    </p>
</section>

<section class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <?php foreach ($products as $product): ?>
        <div class="border rounded-lg p-4">
            <h3 class="font-semibold"><?= htmlspecialchars($product['title']) ?></h3>
            <p class="text-sm text-gray-500">Rp<?= number_format($product['price']) ?></p>
        </div>
    <?php endforeach; ?>
</section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/front.php';
