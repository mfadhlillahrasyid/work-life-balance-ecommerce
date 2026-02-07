<?php
$page_title = 'Create Products';

ob_start();

?>

<div class="max-w-full mx-auto">
    <div class="overflow-hidden">
        <div class="overflow-x-auto overscroll-x-auto flex flex-col gap-4">
            <div class="grid gap-3 sm:flex md:justify-between md:items-center px-6 sm:p-0">
                <div class="flex flex-col sm:flex-row justify-between gap-1 sm:gap-2 items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 tracking-tight">
                        Add Product
                    </h2>
                    <p>
                        <?php breadcrumb(
                            [
                                'Dashboard' => '/admin/dashboard',
                                'Products' => '/admin/products',
                                'Create' => null,
                            ]
                        ); ?>
                    </p>
                </div>
            </div>

            <form method="POST" action="/admin/products/" enctype="multipart/form-data"
                class="bg-white p-5 rounded-none sm:rounded-xl border border-neutral-200">

                <div class="flex flex-col gap-4 sm:gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="block text-sm font-semibold">Images</label>

                        <div id="imagesDropzone"
                            class="relative w-full border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">

                            <!-- FILE INPUT -->
                            <input type="file" name="images[]" data-images-input
                                accept="image/jpeg,image/jpg,image/png,image/webp" multiple class="hidden">

                            <!-- PLACEHOLDER -->
                            <div
                                class="images-placeholder flex flex-col items-center justify-center gap-3 text-center cursor-pointer py-12 px-4">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 16v-8m0 0l-3 3m3-3l3 3M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1" />
                                </svg>
                                <div>
                                    <p class="text-sm">
                                        <span class="font-semibold text-gray-800">Click to upload</span>
                                        <span class="text-gray-500"> or drag & drop</span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">JPG / PNG / WEBP • Max 2MB each</p>
                                </div>
                            </div>

                            <!-- PREVIEW -->
                            <div class="images-preview hidden p-4">
                                <div class="flex flex-wrap gap-3" data-images-container>

                                    <!-- UPLOAD BUTTON (always first) -->
                                    <button type="button" data-upload-btn
                                        class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 hover:border-blue-400 hover:text-blue-500 hover:bg-blue-50 transition-all bg-white">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>

                                    <!-- EXISTING IMAGES (hanya di EDIT mode, di-render dari PHP) -->
                                    <?php if (!empty($product['images'])): ?>
                                        <?php foreach ($product['images'] as $img): ?>
                                            <div class="relative group" data-existing-image="<?= htmlspecialchars($img) ?>">
                                                <img src="/storage/products/<?= htmlspecialchars($img) ?>" alt="Product Image"
                                                    class="w-24 h-24 object-cover rounded-lg border border-gray-200">

                                                <button type="button"
                                                    class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-6 h-6 flex items-center justify-center rounded-full text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity shadow-lg"
                                                    data-remove-existing="<?= htmlspecialchars($img) ?>" title="Remove image">
                                                    ✕
                                                </button>

                                                <input type="hidden" name="existing_images[]"
                                                    value="<?= htmlspecialchars($img) ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 mt-2">
                            💡 First image will be used as the main product image
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-semibold">Title</label>
                            <input type="text" name="title" required placeholder="Write Post Title..."
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>

                        <div class="relative z-40 flex flex-col gap-2" data-select>
                            <label class="block text-sm font-semibold">Product Category</label>

                            <button type="button" class="select-trigger w-full border rounded-lg py-2 px-3 text-sm
                                    flex justify-between items-center bg-neutral-50">
                                <span class="select-label text-gray-500">Choose Category</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </button>

                            <input type="hidden" name="product_category_id" class="select-input" required>

                            <div class="select-dropdown absolute z-50 mt-2 top-16 w-full bg-white
                                    border border-gray-200 rounded-lg shadow-lg hidden">
                                <?php foreach ($categories as $category): ?>
                                    <?php if (!empty($category['deleted_at']))
                                        continue; ?>
                                    <div class="select-option py-2 px-3 hover:bg-gray-100 cursor-pointer text-sm text-gray-800"
                                        data-value="<?= $category['id'] ?>"
                                        data-label="<?= htmlspecialchars($category['title']) ?>">
                                        <?= htmlspecialchars($category['title']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="relative z-40 flex flex-col gap-2" data-select>
                            <label class="block text-sm font-semibold">Gender</label>

                            <button type="button" class="select-trigger w-full border rounded-lg py-2 px-3 text-sm
        flex justify-between items-center bg-neutral-50">
                                <span class="select-label text-gray-500">Choose Gender</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </button>

                            <input type="hidden" name="gender_id" class="select-input" required>

                            <div class="select-dropdown absolute z-50 mt-2 top-16 w-full bg-white
                                    border border-gray-200 rounded-lg shadow-lg hidden">
                                <?php foreach ($genders as $gender): ?>
                                    <?php if (!empty($gender['deleted_at']))
                                        continue; ?>
                                    <div class="select-option px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm text-gray-800"
                                        data-value="<?= $gender['id'] ?>"
                                        data-label="<?= htmlspecialchars($gender['title']) ?>">
                                        <?= htmlspecialchars($gender['title']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-6">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-3">
                                <label class="block text-sm font-semibold">Color Variant</label>
                                <span class="block text-xs text-neutral-400">Separate with Commas | Example : Black,
                                    White, Red etc.</span>
                            </div>
                            <input type="text" name="colors" required placeholder="Write Post Colors..."
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>

                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-3">
                                <label class="block text-sm font-semibold">Size Chart</label>
                                <span class="block text-xs text-neutral-400">Separate with Commas | Example : S, M, L,
                                    ...</span>
                            </div>
                            <input type="text" name="size" required placeholder="Write Post Size..."
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-semibold">Pricelist</label>
                            <input type="number" name="price" required placeholder="100,000"
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-semibold">Stock Product</label>
                            <input type="number" name="stock" required placeholder="100,000"
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="description" class="block text-sm font-semibold text-gray-800">
                            Description
                        </label>

                        <textarea name="description" id="content" rows="10"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-indigo-600 focus:ring focus:ring-indigo-200 text-sm"
                            placeholder="Write your descriptions here..."><?= htmlspecialchars($post['description'] ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-end items-center gap-2">
                        <a href="/admin/products"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg  border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none">
                            Cancel
                        </a>
                        <button type="submit"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg  border border-neutral-900 bg-neutral-900 text-neutral-100 hover:bg-neutral-800 focus:outline-hidden focus:bg-neutral-800 disabled:opacity-50 disabled:pointer-events-none">
                            Create
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
