<?php $page_title = 'Posts';
ob_start();

?>

<div class="max-w-full mx-auto">
    <div class="overflow-hidden">
        <div class="overflow-x-auto overscroll-x-auto flex flex-col gap-0 sm:gap-4">
            <div class="grid gap-3 sm:flex md:justify-between md:items-center">
                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2 items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 tracking-tight">
                        Posts
                    </h2>
                    <p> <?php breadcrumb(['Dashboard' => '/admin/dashboard', 'Posts' => null]); ?></p>
                </div>

                <div>
                    <div class="inline-flex gap-x-2 hidden sm:block">
                        <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-neutral-600 bg-neutral-950 text-gray-100 hover:bg-neutral-800 focus:outline-hidden focus:bg-neutral-800 disabled:opacity-50 disabled:pointer-events-none"
                            href="/admin/posts/create">
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
                                    Post Title
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
                            <th scope="col" class="px-6 py-4 font-medium">
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
                            <th scope="col" class="px-6 py-4 font-medium">
                                <div class="flex items-center">
                                    Content
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
                        <?php if (empty($posts)): ?>
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

                        <?php foreach ($posts as $post): ?>
                            <tr class="bg-neutral-primary-soft text-neutral-600 border-b last:border-b-0">

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <?php if (!empty($post['banner'])): ?>
                                            <img src="/storage/banners/<?= htmlspecialchars($post['banner']) ?>"
                                                alt="<?= htmlspecialchars($post['title']) ?>"
                                                class="w-11 h-11 object-cover aspect-square rounded-md border shadow-lg shadow-neutral-400/30"
                                                loading="lazy">
                                        <?php else: ?>
                                            <div
                                                class="w-10 h-10 flex items-center justify-center rounded-md bg-gray-100 text-gray-400 text-xs border">
                                                -
                                            </div>
                                        <?php endif; ?>

                                        <span><?= htmlspecialchars(
                                            mb_strlen($post['title'], 'UTF-8') > 50
                                            ? mb_substr($post['title'], 0, 50, 'UTF-8') . '...'
                                            : $post['title']
                                        ) ?></span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-xs text-gray-600 bg-gray-100 border border-gray-200 font-semibold py-1 px-2 rounded-md">
                                        <?= htmlspecialchars($post['category_name'] ?? '-') ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ((int) $post['status'] === 1): ?>
                                        <span
                                            class="inline-flex items-center rounded-md bg-indigo-100 border border-indigo-300 px-2 py-1 text-xs font-semibold text-indigo-700">
                                            Published
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center rounded-md bg-yellow-100 border border-yellow-300 px-2 py-1 text-xs font-semibold text-yellow-800">
                                            Draft
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-gray-500">
                                        <?= htmlspecialchars(
                                            mb_strimwidth(strip_tags($post['content'] ?? ''), 0, 80, '...'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= date('D, d M Y', strtotime($post['created_at'])) ?>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        <a class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200"
                                            href="/admin/posts/<?= htmlspecialchars($post['slug_uuid']) ?>/edit">
                                            Edit
                                        </a>
                                        <form method="post"
                                            action="/admin/posts/<?= htmlspecialchars($post['slug_uuid']) ?>/delete"
                                            class="inline">
                                            <button type="submit"
                                                class="py-1.5 px-2.5 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-red-300 bg-red-100 text-red-800 hover:bg-red-200"
                                                onclick="return confirm('Hapus post ini?')">
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

            <!-- <?php include ROOT_PATH . '/views/components/pagination.php'; ?> -->

            <div class="fixed bottom-2 right-2 inline-flex gap-x-2 block sm:hidden">
                <a class="p-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-neutral-600 bg-neutral-950 text-gray-100 hover:bg-neutral-800 focus:outline-hidden focus:bg-neutral-800 disabled:opacity-50 disabled:pointer-events-none"
                    href="/admin/posts/create">
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
