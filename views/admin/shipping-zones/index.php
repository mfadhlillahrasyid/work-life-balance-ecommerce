<?php $page_title = 'Shipping Zones';
ob_start();

?>

<div class="max-w-full mx-auto">
    <div class="overflow-hidden">
        <div class="overflow-x-auto overscroll-x-auto flex flex-col gap-0 sm:gap-4">
            <div class="grid gap-3 sm:flex md:justify-between md:items-center">
                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2 items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 tracking-tight">
                        Shipping Zones
                    </h2>
                    <p> <?php breadcrumb(['Dashboard' => '/admin/dashboard', 'Shipping Zones' => null]); ?></p>
                </div>

                <div>
                    <div class="inline-flex gap-x-2 hidden sm:block">
                        <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-neutral-600 bg-neutral-950 text-gray-100 hover:bg-neutral-800 focus:outline-hidden focus:bg-neutral-800 disabled:opacity-50 disabled:pointer-events-none"
                            href="/admin/shipping-zones/create">
                            Create New
                        </a>
                    </div>
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
                                    Zone
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
                                    Kurir
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
                                    Provinsi
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
                                    Ongkir
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
                        <?php if (empty($zones)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">
                                    Belum ada shipping zone.
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($zones as $zone): ?>
                            <tr class="bg-neutral-primary-soft text-neutral-600 border-b last:border-b-0">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($zone['icon'])): ?>
                                            <img src="/storage/icons/<?= htmlspecialchars($zone['icon']) ?>"
                                                alt="<?= htmlspecialchars($zone['kurir']) ?>"
                                                class="w-11 h-11 object-contain rounded border border-gray-100 shadow-lg shadow-neutral-400/30"
                                                loading="lazy">
                                        <?php else: ?>
                                            <div
                                                class="w-8 h-8 rounded border border-gray-200 bg-gray-50 flex items-center justify-center text-gray-400 text-xs">
                                                <img src="http://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg"
                                                    alt="">
                                            </div>
                                        <?php endif; ?>
                                        <span class="font-medium text-gray-800">
                                            <?= htmlspecialchars($zone['name']) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-xs bg-gray-100 border border-gray-200 text-gray-700 font-medium px-2 py-1 rounded-md">
                                        <?= htmlspecialchars($zone['kurir']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs text-gray-600">
                                        <?= count($zone['provinces']) ?> provinsi
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-800">
                                        Rp
                                        <?= number_format($zone['cost'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= date('D, d M Y', strtotime($zone['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <a class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none"
                                            href="/admin/shipping-zones/<?= $zone['slug_uuid'] ?>/edit">
                                            Edit
                                        </a>
                                        <form method="POST" action="/admin/shipping-zones/<?= $zone['slug_uuid'] ?>/delete"
                                            class="inline">
                                            <input type="hidden" name="id" value="<?= $zone['slug_uuid'] ?>">
                                            <button
                                                class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-red-300 bg-red-100 text-red-800 hover:bg-red-200 focus:outline-hidden focus:bg-red-200 disabled:opacity-50 disabled:pointer-events-none"
                                                onclick="return confirm('Delete this Category ?')">
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

            <div class="fixed bottom-2 right-2 inline-flex gap-x-2 block sm:hidden">
                <a class="p-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-neutral-600 bg-neutral-950 text-gray-100 hover:bg-neutral-800 focus:outline-hidden focus:bg-neutral-800 disabled:opacity-50 disabled:pointer-events-none"
                    href="/admin/shipping-zones/create">
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" fill="none" viewBox="0 0 24 24">
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
