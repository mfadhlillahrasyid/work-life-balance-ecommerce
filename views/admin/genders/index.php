<?php $page_title = 'Genders';
ob_start();

?>

<div class="max-w-full mx-auto">
    <div class="overflow-hidden">
        <div class="overflow-x-auto overscroll-x-auto flex flex-col gap-0 sm:gap-4">
            <div class="grid gap-3 sm:flex md:justify-between md:items-center">
                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2 items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 tracking-tight">
                        Genders
                    </h2>
                    <p> <?php breadcrumb(['Dashboard' => '/admin/dashboard', 'Genders' => null]); ?></p>
                </div>

                <div>
                    <div class="inline-flex gap-x-2 hidden sm:block">
                        <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-neutral-600 bg-neutral-950 text-gray-100 hover:bg-neutral-800 focus:outline-hidden focus:bg-neutral-800 disabled:opacity-50 disabled:pointer-events-none"
                            href="/admin/genders/create">
                            Create New
                        </a>
                    </div>
                </div>
            </div>

            <div class="w-full relative overflow-x-auto bg-white shadow-xs rounded-xl border border-neutral-200">
                <table class="w-full text-xs sm:text-sm text-left">
                    <thead class="text-sm text-body bg-neutral-100 border-b border-default-medium">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-medium">
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
                            <th scope="col" class="px-6 py-4 font-medium">
                                <div class="flex items-center">
                                    Description
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
                        <?php foreach ($genders as $gender): ?>
                            <tr class="bg-neutral-primary-soft text-neutral-600 border-b last:border-b-0">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <?php if (!empty($gender['banner'])): ?>
                                            <img src="/storage/banners/<?= htmlspecialchars($gender['banner']) ?>"
                                                alt="<?= htmlspecialchars($gender['title']) ?>"
                                                class="w-11 h-11 object-cover aspect-square rounded-md border shadow-lg shadow-neutral-400/30"
                                                loading="lazy">
                                        <?php else: ?>
                                            <div
                                                class="w-10 h-10 flex items-center justify-center rounded-md bg-gray-100 text-gray-400 text-xs border">
                                                —
                                            </div>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($gender['title']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= htmlspecialchars(
                                        mb_strlen($gender['description'], 'UTF-8') > 50
                                        ? mb_substr($gender['description'], 0, 80, 'UTF-8') . '...'
                                        : $gender['description']
                                    ) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= date('D, d M Y', strtotime($gender['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <a class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none"
                                            href="/admin/genders/<?= $gender['slug_uuid'] ?>/edit">
                                            Edit
                                        </a>
                                        <form method="post" action="/admin/genders/<?= $gender['slug_uuid'] ?>/delete"
                                            class="inline">
                                            <input type="hidden" name="id" value="<?= $gender['slug_uuid'] ?>">
                                            <button
                                                class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-red-300 bg-red-100 text-red-800 hover:bg-red-200 focus:outline-hidden focus:bg-red-200 disabled:opacity-50 disabled:pointer-events-none"
                                                onclick="return confirm('Delete this Gender ?')">
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
                    href="/admin/genders/create">
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
