<?php
// views/admin/shipping-zones/edit.php
$page_title = 'Edit Shipping Zone';
ob_start();
?>

<div class="max-w-full mx-auto">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row gap-1 sm:gap-2 items-start sm:items-center px-6 sm:p-0">
            <h2 class="text-xl font-semibold text-gray-800 tracking-tight">Edit Shipping Zone</h2>
            <p><?php breadcrumb(['Dashboard' => '/admin/dashboard', 'Shipping Zones' => '/admin/shipping-zones', 'Edit' => null]); ?>
            </p>
        </div>

        <?php if (!empty($_SESSION['admin_error'])): ?>
            <div class="text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-3 rounded-lg">
                <?= htmlspecialchars($_SESSION['admin_error']) ?>
                <?php unset($_SESSION['admin_error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/shipping-zones/<?= $zone['slug_uuid'] ?>/update" enctype="multipart/form-data"
            class="bg-white p-5 rounded-none sm:rounded-xl border border-neutral-200">
            <div class="flex flex-col gap-6">

                <!-- Name, Kurir, Cost -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold">Nama Zone <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="<?= htmlspecialchars($zone['name']) ?>"
                            class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold">Kurir <span class="text-red-500">*</span></label>
                        <input type="text" name="kurir" required value="<?= htmlspecialchars($zone['kurir']) ?>"
                            class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-semibold">Ongkir (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="cost" required min="0" value="<?= (int) $zone['cost'] ?>"
                            class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-neutral-50 text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                    </div>
                </div>

                <!-- Icon -->
                <div class="flex flex-col gap-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Icon
                    </label>

                    <div id="dropzone" data-existing-icon="<?= htmlspecialchars($zone['icon'] ?? '') ?>"
                        class="relative group flex items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 cursor-pointer hover:bg-indigo-50 hover:border-indigo-600 transition-all duration-300">

                        <input type="file" id="iconInput" name="icon" accept="image/*" class="hidden">

                        <!-- SIGNAL KE BACKEND -->
                        <input type="hidden" name="remove_icon" id="removeIcon" value="0">

                        <!-- Placeholder -->
                        <div id="placeholder" class="text-sm text-gray-500">
                            Drop image or click
                        </div>

                        <!-- Loading -->
                        <div id="loading" class="hidden absolute inset-0 flex items-center justify-center bg-white/70">
                            <div class="animate-spin w-8 h-8 border-2 border-gray-300 border-t-gray-700 rounded-full">
                            </div>
                        </div>

                        <!-- Preview -->
                        <div id="previewWrapper" class="hidden absolute inset-0">
                            <img id="previewImage" class="w-full h-full object-cover rounded-lg">
                            <button type="button" id="removeImage"
                                class="absolute top-2 right-2 w-6 h-6 bg-black/60 hover:bg-red-500 text-white rounded-md text-sm flex items-center justify-center transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Provinces -->
                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold">Provinsi <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <button type="button" id="selectAll" class="text-xs text-indigo-600 hover:underline">Pilih
                                Semua</button>
                            <span class="text-xs text-gray-300">|</span>
                            <button type="button" id="deselectAll" class="text-xs text-gray-500 hover:underline">Batal
                                Semua</button>
                        </div>
                    </div>

                    <div
                        class="border border-gray-200 rounded-lg p-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        <?php foreach ($provinces as $prov): ?>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="provinces[]" value="<?= (int) $prov['id'] ?>"
                                    class="province-checkbox w-5 h-5 border border-default-medium rounded-md bg-neutral-secondary-medium focus:ring-2 focus:ring-offset-2 focus:ring-black"
                                    <?= in_array((int) $prov['id'], $zone['provinces']) ? 'checked' : '' ?>>
                                <span class="text-xs sm:text-sm text-gray-700 group-hover:text-indigo-700 capitalize">
                                    <?= htmlspecialchars(ucwords(strtolower($prov['name']))) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end items-center gap-2">
                    <a href="/admin/shipping-zones"
                        class="py-2 px-3 text-sm font-medium rounded-lg border border-gray-300 bg-gray-100 text-gray-800 hover:bg-gray-200">
                        Cancel
                    </a>
                    <button type="submit"
                        class="py-2 px-3 text-sm font-medium rounded-lg border border-neutral-900 bg-neutral-900 text-neutral-100 hover:bg-neutral-800">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('selectAll')?.addEventListener('click', () => {
        document.querySelectorAll('.province-checkbox').forEach(cb => cb.checked = true);
    });
    document.getElementById('deselectAll')?.addEventListener('click', () => {
        document.querySelectorAll('.province-checkbox').forEach(cb => cb.checked = false);
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/admin.php';
?>