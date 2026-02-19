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
                        <img src="/storage/banners/<?= htmlspecialchars($gender['banner']) ?>"
                            alt="T-Shirt"
                            class="w-full aspect-video sm:aspect-square h-full scale-105 group-hover:scale-110 transition-all duration-300 object-cover">
                        <div class="absolute inset-0 bg-black/30 transition-all duration-300">
                        </div>
                        <div class="absolute inset-0 flex flex-col p-5 items-start justify-end text-white">
                            <h2 class="text-2xl sm:text-5xl tracking-tight uppercase font-bebas">
                                <?= htmlspecialchars($gender['title']) ?></h2>
                            <!-- <p class="text-sm mt-1 text-neutral-200 max-w-xs">Simpel tapi standout. Nyaman dipakai ke mana aja,
                                cocok buat gaya.</p> -->
                        </div>
                    </a>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <p class="text-sm text-gray-500">Belum ada kategori tersedia.</p>
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
