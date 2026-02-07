<?php
// data datang dari controller
// $category = [...]

$page_title = 'Edit Post Category';

ob_start();

?>

<div class="max-w-full mx-auto">
    <div class="overflow-hidden">
        <div class="overflow-x-auto overscroll-x-auto flex flex-col gap-4">
            <div class="grid gap-3 sm:flex md:justify-between md:items-center">
                <div class="flex flex-col sm:flex-row justify-between gap-1 sm:gap-2 items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 tracking-tight">
                        Edit Categories
                    </h2>
                    <p> <?php breadcrumb(
                        [
                            'Dashboard' => '/admin/dashboard',
                            'Product Categories' => '/admin/product-categories',
                            'Edit' => null,
                        ]
                    ); ?></p>
                </div>
            </div>

            <form method="POST" action="/admin/product-categories/<?= $category['slug_uuid'] ?>/update"
                class="bg-white p-5 rounded-2xl border border-neutral-200">

                <div class="flex flex-col gap-4 sm:gap-6">

                    <div class="flex items-center justify-between border rounded-lg px-4 py-3 bg-gray-50">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Category Availability</p>
                            <p class="text-xs text-gray-500">
                                Disable this category without deleting it
                            </p>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="available" value="1" class="sr-only peer"
                                <?= !empty($category['available']) ? 'checked' : '' ?>>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer
                                peer-checked:bg-indigo-600
                                peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100
                                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                after:bg-white after:border after:rounded-full after:h-5 after:w-5
                                after:transition-all
                                peer-checked:after:translate-x-5">
                            </div>
                        </label>
                    </div>


                    <div class="flex flex-col gap-2">
                        <label class="block text-sm font-semibold">Title</label>
                        <input type="text" name="title" required placeholder="Write Post Title..."
                            value="<?= htmlspecialchars($category['title']) ?>"
                            class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Icon
                            </label>

                            <div id="dropzone" data-existing-icon="<?= htmlspecialchars($category['icon'] ?? '') ?>"
                                class="relative group flex items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 cursor-pointer hover:bg-indigo-50 hover:border-indigo-600 transition-all duration-300">

                                <input type="file" id="iconInput" name="icon" accept="image/*" class="hidden">

                                <!-- SIGNAL KE BACKEND -->
                                <input type="hidden" name="remove_icon" id="removeIcon" value="0">

                                <!-- Placeholder -->
                                <div id="placeholder" class="text-sm text-gray-500">
                                    Drop image or click
                                </div>

                                <!-- Loading -->
                                <div id="loading"
                                    class="hidden absolute inset-0 flex items-center justify-center bg-white/70">
                                    <div
                                        class="animate-spin w-8 h-8 border-2 border-gray-300 border-t-gray-700 rounded-full">
                                    </div>
                                </div>

                                <!-- Preview -->
                                <div id="previewWrapper" class="hidden absolute inset-0">
                                    <img id="previewImage" class="w-full h-full object-cover rounded-lg">
                                    <button type="button" id="removeImage"
                                        class="absolute top-2 right-2 w-6 h-6 bg-black/60 hover:bg-red-500 text-white rounded-md text-sm flex items-center justify-center transition-all duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-semibold">Description</label>
                            <textarea name="description" rows="7" placeholder="Write Post Description..."
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none"><?= htmlspecialchars($category['description']) ?></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end items-center gap-2">
                        <a href="/admin/product-categories"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg  border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none">
                            Cancel
                        </a>
                        <button type="submit"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg  border border-neutral-900 bg-neutral-900 text-neutral-100 hover:bg-neutral-800 focus:outline-hidden focus:bg-neutral-800 disabled:opacity-50 disabled:pointer-events-none">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
