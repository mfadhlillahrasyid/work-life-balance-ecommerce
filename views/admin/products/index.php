<?php $page_title = 'Products';
ob_start();
?>

<div class="max-w-full mx-auto">
    <div class="overflow-hidden">
        <div class="overflow-x-auto overscroll-x-auto flex flex-col gap-0 sm:gap-4">
            <div class="grid gap-3 sm:flex md:justify-between md:items-center">
                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2 items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 tracking-tight">Products</h2>
                    <p><?php breadcrumb(['Dashboard' => '/admin/dashboard', 'Products' => null]); ?></p>
                </div>
                <div class="hidden sm:block">
                    <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-neutral-600 bg-neutral-950 text-gray-100 hover:bg-neutral-800"
                        href="/admin/products/create">
                        Create New
                    </a>
                </div>
            </div>

            <?php if (!empty($_SESSION['admin_success'])): ?>
                <div class="text-sm text-green-700 bg-green-50 border border-green-200 px-4 py-3 rounded-lg">
                    <?= htmlspecialchars($_SESSION['admin_success']) ?>
                    <?php unset($_SESSION['admin_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['admin_error'])): ?>
                <div class="text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-3 rounded-lg">
                    <?= htmlspecialchars($_SESSION['admin_error']) ?>
                    <?php unset($_SESSION['admin_error']); ?>
                </div>
            <?php endif; ?>

            <div class="w-full relative overflow-x-auto bg-white shadow-xs rounded-xl border border-neutral-200">
                <table class="w-full text-xs sm:text-sm text-left">

                    <thead class="text-sm text-body bg-neutral-100 border-b border-default-medium">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-medium">
                                <div class="flex items-center">
                                    Images
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 font-medium">
                                <div class="flex items-center">
                                    Title
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 font-medium">
                                <div class="flex items-center">
                                    Category
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                <div class="flex items-center">
                                    Gender
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>

                            <th scope="col" class="px-6 py-3 font-medium">
                                <div class="flex items-center">
                                    Color Variant
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>

                            <th scope="col" class="px-6 py-3 font-medium">
                                <div class="flex items-center">
                                    Size Chart
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>

                            <th scope="col" class="px-6 py-3 font-medium">
                                <div class="flex items-center">
                                    Stock
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>

                            <th scope="col" class="px-6 py-3 font-medium">
                                <div class="flex items-center">
                                    Status
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>

                            <th scope="col" class="px-6 py-3 font-medium">
                                <div class="flex items-center">
                                    Created At
                                    <a href="#">
                                        <svg class="w-4 h-4 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4" />
                                        </svg>
                                    </a>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="12" class="px-6 py-12 text-center text-sm text-gray-400">
                                    <div class="flex flex-col gap-2 items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-12">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                        </svg>

                                        <span class="text-sm sm:text-base font-medium tracking-tight">Data Not
                                            Available</span>
                                    </div>

                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($products as $product): ?>
                            <?php
                            $colors = !empty($product['colors'])
                                ? array_values(array_filter(array_map('trim', explode(',', $product['colors']))))
                                : [];

                            $sizes = !empty($product['sizes'])
                                ? array_values(array_filter(array_map('trim', explode(',', $product['sizes']))))
                                : [];
                            $sizesVisible = array_slice($sizes, 0, 3);
                            $sizesRemain = count($sizes) - count($sizesVisible);
                            ?>

                            <tr class="bg-neutral-primary-soft text-neutral-600 border-b last:border-b-0">

                                <!-- Stacked Images -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $imagesPreview = !empty($product['images_preview'])
                                        ? array_values(array_filter(explode(',', $product['images_preview'])))
                                        : [];
                                    $totalImages = count($imagesPreview);
                                    ?>

                                    <div class="flex items-center">
                                        <?php foreach ($imagesPreview as $i => $img): ?>
                                            <img src="/storage/products/<?= htmlspecialchars($img) ?>"
                                                alt="<?= htmlspecialchars($product['title']) ?>"
                                                class="w-9 h-9 rounded-full object-cover border-2 border-white shadow <?= $i > 0 ? '-ml-3' : '' ?>"
                                                style="z-index: <?= 13 - $i ?>" loading="lazy">
                                        <?php endforeach; ?>

                                        <?php
                                        $variantCount = (int) $product['variant_count'];
                                        $remaining = $variantCount - $totalImages;
                                        ?>
                                        <?php if ($remaining > 0): ?>
                                            <div class="w-9 h-9 shrink-0 rounded-full -ml-3 flex items-center justify-center bg-gray-200 text-xs font-semibold text-gray-700 border-2 border-white shadow"
                                                style="z-index: 10">
                                                +<?= $remaining ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (empty($imagesPreview)): ?>
                                            <div
                                                class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 text-gray-400 text-xs border">
                                                —</div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Title + variant count & stock -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-800">
                                            <?= htmlspecialchars(
                                                mb_strlen($product['title'], 'UTF-8') > 35
                                                ? mb_substr($product['title'], 0, 60, 'UTF-8') . '...'
                                                : $product['title']
                                            ) ?>
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            <?= (int) $product['variant_count'] ?>
                                            variant<?= (int) $product['variant_count'] !== 1 ? 's' : '' ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center text-xs font-semibold text-gray-700 bg-gray-100 border border-gray-200 py-1 px-2 rounded-md">
                                        <?= htmlspecialchars($product['category_name'] ?? '-') ?>
                                    </span>
                                </td>

                                <!-- Gender -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center text-xs font-semibold text-gray-700 bg-gray-100 border border-gray-200 py-1 px-2 rounded-md">
                                        <?= htmlspecialchars($product['gender_name'] ?? '-') ?>
                                    </span>
                                </td>

                                <!-- Colors -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <?php if (!empty($colors)): ?>
                                            <?php foreach ($colors as $color): ?>
                                                <span
                                                    class="inline-flex items-center text-xs font-semibold text-gray-700 bg-gray-100 border border-gray-200 py-1 px-2 rounded-md capitalize">
                                                    <?= htmlspecialchars($color) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Sizes max 3 + remainder -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <?php if (!empty($sizesVisible)): ?>
                                            <?php foreach ($sizesVisible as $size): ?>
                                                <span
                                                    class="inline-flex items-center text-xs font-semibold text-gray-700 bg-gray-100 border border-gray-200 py-1 px-2 rounded-md uppercase">
                                                    <?= htmlspecialchars($size) ?>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if ($sizesRemain > 0): ?>
                                                <span
                                                    class="inline-flex items-center text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 py-1 px-2 rounded-md">
                                                    +<?= $sizesRemain ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Total Stock -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php $totalStock = (int) $product['total_stock']; ?>
                                    <span
                                        class="text-xs font-semibold <?= $totalStock === 0 ? 'text-red-600' : 'text-gray-700' ?>">
                                        <?= $totalStock ?> pcs
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ((int) $product['status'] === 1): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                class="size-3">
                                                <path
                                                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22ZM17.4571 9.45711L11 15.9142L6.79289 11.7071L8.20711 10.2929L11 13.0858L16.0429 8.04289L17.4571 9.45711Z" />
                                            </svg>
                                            Published
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-yellow-800 bg-yellow-100 border border-yellow-200 px-2 py-1 rounded-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                class="size-3">
                                                <path
                                                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" />
                                            </svg>
                                            Draft
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Created At -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= date('D, d M Y', strtotime($product['created_at'])) ?>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <a class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200"
                                            href="/admin/products/<?= htmlspecialchars($product['slug_uuid']) ?>/edit">
                                            Edit
                                        </a>
                                        <form method="POST"
                                            action="/admin/products/<?= htmlspecialchars($product['slug_uuid']) ?>/delete"
                                            class="inline">
                                            <button type="submit"
                                                class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-red-300 bg-red-100 text-red-800 hover:bg-red-200"
                                                onclick="return confirm('Hapus produk ini?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php include ROOT_PATH . '/views/components/pagination.php'; ?>

            <!-- Mobile FAB -->
            <div class="fixed bottom-2 right-2 block sm:hidden">
                <a class="p-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-neutral-600 bg-neutral-950 text-gray-100 hover:bg-neutral-800"
                    href="/admin/products/create">
                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14m-7 7V5" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>