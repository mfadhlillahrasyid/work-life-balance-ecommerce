<?php
$page_title = 'Create Products';
ob_start();
?>

<div class="max-w-full mx-auto">
    <div class="overflow-hidden">
        <div class="overflow-x-auto overscroll-x-auto flex flex-col gap-4">
            <div class="grid gap-3 sm:flex md:justify-between md:items-center px-6 sm:p-0">
                <div class="flex flex-col sm:flex-row justify-between gap-1 sm:gap-2 items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 tracking-tight">Add Product</h2>
                    <p><?php breadcrumb(['Dashboard' => '/admin/dashboard', 'Products' => '/admin/products', 'Create' => null]); ?>
                    </p>
                </div>
            </div>

            <form method="POST" action="/admin/products" enctype="multipart/form-data"
                class="bg-white p-5 rounded-none sm:rounded-xl border border-neutral-200">

                <div class="flex flex-col gap-4 sm:gap-6">

                    <!-- ═══════════════════════════════════════════════════════
                         IMAGES
                    ════════════════════════════════════════════════════════ -->
                    <div class="flex flex-col gap-2">
                        <label class="block text-sm font-semibold">Images</label>

                        <div id="imagesDropzone"
                            class="relative w-full border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">

                            <input type="file" name="images[]" data-images-input
                                accept="image/jpeg,image/jpg,image/png,image/webp" multiple class="hidden">

                            <!-- Placeholder -->
                            <div
                                class="images-placeholder flex flex-col items-center justify-center gap-3 text-center cursor-pointer py-12 px-4">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 16v-8m0 0l-3 3m3-3l3 3M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1" />
                                </svg>
                                <div>
                                    <p class="text-sm"><span class="font-semibold text-gray-800">Click to upload</span>
                                        <span class="text-gray-500">or drag & drop</span></p>
                                    <p class="text-xs text-gray-400 mt-1">JPG / PNG / WEBP • Max 5MB each</p>
                                </div>
                            </div>

                            <!-- Preview -->
                            <div class="images-preview hidden p-4">
                                <div class="flex flex-wrap gap-3" data-images-container>
                                    <button type="button" data-upload-btn
                                        class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 hover:border-indigo-400 hover:text-indigo-500 hover:bg-indigo-50 transition-all bg-white">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500">💡 Gambar pertama akan digunakan sebagai thumbnail utama</p>
                    </div>

                    <!-- ═══════════════════════════════════════════════════════
                         TITLE, CATEGORY, GENDER
                    ════════════════════════════════════════════════════════ -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="block text-sm font-semibold">Title</label>
                            <input type="text" name="title" required placeholder="Product title..."
                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                        </div>

                        <!-- Product Category Dropdown -->
                        <div class="relative z-40 flex flex-col gap-2" data-select>
                            <label class="block text-sm font-semibold">Product Category</label>
                            <button type="button"
                                class="select-trigger w-full border rounded-lg py-2 px-3 text-sm flex justify-between items-center bg-neutral-50">
                                <span class="select-label text-gray-500">Choose Category</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </button>
                            <input type="hidden" name="product_category_id" class="select-input" required>
                            <div
                                class="select-dropdown absolute z-50 mt-2 top-16 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden">
                                <?php foreach ($categories as $category): ?>
                                    <div class="select-option py-2 px-3 hover:bg-gray-100 cursor-pointer"
                                        data-value="<?= htmlspecialchars($category['id']) ?>"
                                        data-label="<?= htmlspecialchars($category['title']) ?>">
                                        <p class="text-sm text-gray-800"><?= htmlspecialchars($category['title']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Gender Dropdown -->
                        <div class="relative z-30 flex flex-col gap-2" data-select>
                            <label class="block text-sm font-semibold">Gender</label>
                            <button type="button"
                                class="select-trigger w-full border rounded-lg py-2 px-3 text-sm flex justify-between items-center bg-neutral-50">
                                <span class="select-label text-gray-500">Choose Gender</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </button>
                            <input type="hidden" name="gender_id" class="select-input" required>
                            <div
                                class="select-dropdown absolute z-50 mt-2 top-16 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden">
                                <?php foreach ($genders as $gender): ?>
                                    <div class="select-option px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                        data-value="<?= htmlspecialchars($gender['id']) ?>"
                                        data-label="<?= htmlspecialchars($gender['title']) ?>">
                                        <p class="text-sm text-gray-800"><?= htmlspecialchars($gender['title']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- ═══════════════════════════════════════════════════════
                         VARIANTS TABLE
                    ════════════════════════════════════════════════════════ -->
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-0 sm:items-center justify-between">
                            <div>
                                <label class="block text-sm font-semibold">Variants</label>
                                <p class="text-xs text-gray-400 mt-0.5">Tambahkan kombinasi warna, ukuran, harga, dan
                                    stok</p>
                            </div>
                            <button type="button" id="addVariantBtn"
                                class="inline-flex items-center gap-2 text-xs font-medium px-3 py-2 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="size-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Variant
                            </button>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Color</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Size</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Price (Rp)
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Stock</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">SKU Preview
                                        </th>
                                        <th class="px-4 py-3 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody id="variantsBody">
                                    <!-- Row pertama default -->
                                    <tr class="variant-row border-b border-gray-100 last:border-b-0">
                                        <td class="px-4 py-3">
                                            <input type="text" name="variants[0][color]" placeholder="Black"
                                                class="variant-color w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none capitalize">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="variants[0][size]" placeholder="M"
                                                class="variant-size w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none uppercase">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="variants[0][price]" placeholder="550000" min="0"
                                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="variants[0][stock]" placeholder="0" min="0"
                                                value="0"
                                                class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="sku-preview text-xs text-gray-400 font-mono">-</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <button type="button"
                                                class="remove-variant-btn w-6 h-6 flex items-center justify-center rounded-md text-gray-400 hover:bg-red-100 hover:text-red-600 transition-colors"
                                                title="Hapus variant">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p class="text-xs text-gray-400">💡 SKU di-generate otomatis dari title + color + size</p>
                    </div>

                    <!-- ═══════════════════════════════════════════════════════
                         DESCRIPTION
                    ════════════════════════════════════════════════════════ -->
                    <div class="flex flex-col gap-2">
                        <label class="block text-sm font-semibold text-gray-800">Description</label>
                        <textarea name="description" id="content" rows="10"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-indigo-600 focus:ring focus:ring-indigo-200 text-sm"
                            placeholder="Write your descriptions here..."></textarea>
                    </div>

                    <!-- ═══════════════════════════════════════════════════════
                         ACTIONS
                    ════════════════════════════════════════════════════════ -->
                    <div class="flex justify-end items-center gap-2">
                        <a href="/admin/products"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200">
                            Cancel
                        </a>
                        <button type="submit"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-neutral-900 bg-neutral-900 text-neutral-100 hover:bg-neutral-800">
                            Create Product
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const variantsBody = document.getElementById("variantsBody");
        const addVariantBtn = document.getElementById("addVariantBtn");
        const titleInput = document.querySelector("input[name='title']");

        // ── Ambil title untuk SKU preview ──────────────────────────────────────
        function getTitleAbbr() {
            const title = titleInput?.value?.trim() ?? '';
            const words = title.toUpperCase().split(' ').filter(Boolean);
            return words.slice(0, 2).map(w => w.substring(0, 3)).join('');
        }

        // ── Generate SKU preview ───────────────────────────────────────────────
        function generateSku(row) {
            const color = row.querySelector('.variant-color')?.value?.trim().toUpperCase().substring(0, 3) ?? '';
            const size = row.querySelector('.variant-size')?.value?.trim().toUpperCase() ?? '';
            const titleAbbr = getTitleAbbr();
            const preview = row.querySelector('.sku-preview');

            if (color && size && titleAbbr) {
                preview.textContent = `WLB-${titleAbbr}-${color}-${size}`;
                preview.classList.remove('text-gray-400');
                preview.classList.add('text-indigo-600');
            } else {
                preview.textContent = '-';
                preview.classList.add('text-gray-400');
                preview.classList.remove('text-indigo-600');
            }
        }

        // ── Re-index semua variant rows ────────────────────────────────────────
        function reindexRows() {
            variantsBody.querySelectorAll('.variant-row').forEach((row, i) => {
                row.querySelectorAll('input[name]').forEach(input => {
                    input.name = input.name.replace(/variants\[\d+\]/, `variants[${i}]`);
                });
            });
        }

        // ── Attach events ke satu row ──────────────────────────────────────────
        function attachRowEvents(row) {
            // SKU preview update
            row.querySelectorAll('.variant-color, .variant-size').forEach(input => {
                input.addEventListener('input', () => generateSku(row));
            });

            // Force uppercase pada size
            row.querySelector('.variant-size')?.addEventListener('input', function () {
                const pos = this.selectionStart;
                this.value = this.value.toUpperCase();
                this.setSelectionRange(pos, pos);
            });

            // Remove row
            row.querySelector('.remove-variant-btn')?.addEventListener('click', () => {
                // Minimal 1 row harus ada
                if (variantsBody.querySelectorAll('.variant-row').length <= 1) {
                    return;
                }
                row.remove();
                reindexRows();
            });
        }

        // ── Attach ke row pertama yang sudah ada ───────────────────────────────
        variantsBody.querySelectorAll('.variant-row').forEach(row => attachRowEvents(row));

        // ── Update SKU saat title berubah ──────────────────────────────────────
        titleInput?.addEventListener('input', () => {
            variantsBody.querySelectorAll('.variant-row').forEach(row => generateSku(row));
        });

        // ── Add Variant Button ─────────────────────────────────────────────────
        addVariantBtn.addEventListener('click', () => {
            const index = variantsBody.querySelectorAll('.variant-row').length;
            const newRow = document.createElement('tr');
            newRow.className = 'variant-row border-b border-gray-100 last:border-b-0';
            newRow.innerHTML = `
            <td class="px-4 py-3">
                <input type="text" name="variants[${index}][color]" placeholder="Black"
                    class="variant-color w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none capitalize">
            </td>
            <td class="px-4 py-3">
                <input type="text" name="variants[${index}][size]" placeholder="M"
                    class="variant-size w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none uppercase">
            </td>
            <td class="px-4 py-3">
                <input type="number" name="variants[${index}][price]" placeholder="550000" min="0"
                    class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
            </td>
            <td class="px-4 py-3">
                <input type="number" name="variants[${index}][stock]" placeholder="0" min="0" value="0"
                    class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
            </td>
            <td class="px-4 py-3">
                <span class="sku-preview text-xs text-gray-400 font-mono">-</span>
            </td>
            <td class="px-4 py-3">
                <button type="button"
                    class="remove-variant-btn w-6 h-6 flex items-center justify-center rounded-md text-gray-400 hover:bg-red-100 hover:text-red-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </td>
        `;

            variantsBody.appendChild(newRow);
            attachRowEvents(newRow);

            // Focus ke input color row baru
            newRow.querySelector('.variant-color')?.focus();
        });
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>