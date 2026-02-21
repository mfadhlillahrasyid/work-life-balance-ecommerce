<?php
$page_title = 'Checkout';
ob_start();
?>

<div class="border-b border-neutral-200">
    <div class="w-full">
        <div class="grid grid-cols-1 md:grid-cols-5 items-center w-full">
            <div class="p-4 md:col-span-1">
                <div class="flex items-center justify-between">
                    <a href="/cart"
                        class="flex items-center justify-center w-10 h-10 rounded-full shrink-0 bg-neutral-100 hover:bg-neutral-200 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <h3 class="font-bebas tracking-tight text-3xl block sm:hidden">
                        Checkout
                    </h3>
                </div>
            </div>
            <div class="border-l border-neutral-200 md:col-span-4 hidden sm:block">
                <div class="p-4 flex justify-between items-center w-full">
                    <h3 class="font-bebas tracking-tight text-3xl">
                        Checkout
                    </h3>
                    <div class="flex justify-end">
                        <?php breadcrumb(['Home' => '/', 'Cart' => '/cart', 'Checkout' => null]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-full mx-auto">

    <!-- Flash -->
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 px-4 py-3 rounded-lg">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/checkout/process" id="checkoutForm">
        <div class="grid grid-cols-1 lg:grid-cols-3">

            <!-- ═══════════════════════════════════════════════════════════════
                 KIRI — SHIPPING FORM (2/3)
            ════════════════════════════════════════════════════════════════ -->
            <div class="lg:col-span-2 flex flex-col gap-6 border-r border-neutral-200">

                <div class="flex flex-col divide-y divide-neutral-200">
                    <!-- Shipping Address -->
                    <div class="flex flex-col gap-6 p-6 sm:p-8">
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <span
                                class="w-6 h-6 rounded-full bg-neutral-900 text-white text-xs flex items-center justify-center font-bold">1</span>
                            Alamat Pengiriman
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 relative">
                            <!-- Fullname -->
                            <div class="flex flex-col gap-2">
                                <label class="block text-sm font-semibold">Nama Lengkap <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="fullname" required
                                    value="<?= htmlspecialchars($customer['fullname'] ?? '') ?>"
                                    placeholder="Nama penerima"
                                    class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-white text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                            </div>

                            <!-- Phone -->
                            <div class="flex flex-col gap-2">
                                <label class="block text-sm font-semibold">Nomor HP <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="phone" required
                                    value="<?= htmlspecialchars($customer['phone_number'] ?? '') ?>"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-white text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                            </div>

                            <!-- Alamat -->
                            <div class="flex flex-col gap-2 sm:col-span-2">
                                <label class="block text-sm font-semibold">Alamat Lengkap <span
                                        class="text-red-500">*</span></label>
                                <textarea name="alamat" required rows="6"
                                    placeholder="Jl. Contoh No. 123, RT/RW, Kelurahan..."
                                    class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-white text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none resize-none"><?= htmlspecialchars($customer['alamat_lengkap'] ?? '') ?></textarea>
                            </div>

                            <!-- Provinsi -->
                            <div class="flex flex-col gap-2" data-wilayah-select="provinsi">
                                <label class="block text-sm font-semibold">
                                    Provinsi <span class="text-red-500">*</span>
                                </label>

                                <!-- Hidden inputs yang dikirim ke server -->
                                <input type="hidden" name="provinsi" id="provinsiValue" required>
                                <input type="hidden" name="provinsi_id" id="provinsiId">

                                <!-- Trigger button -->
                                <button type="button" id="provinsiTrigger"
                                    class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-white text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none flex justify-between items-center">
                                    <span id="provinsiLabel" class="text-neutral-400">Pilih Provinsi</span>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="size-4 text-neutral-400 transition-transform" fill="none"
                                        viewBox="0 0 24 24" id="provinsiChevron" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>

                                <!-- Dropdown -->
                                <div id="provinsiDropdown"
                                    class="hidden absolute z-50 w-full mt-1 bg-white border border-neutral-200 rounded-xl shadow-xl overflow-hidden"
                                    style="max-height: 280px;">
                                    <!-- Search -->
                                    <div class="p-2 border-b border-neutral-100 sticky top-0 bg-white">
                                        <input type="text" id="provinsiSearch" placeholder="Cari provinsi..."
                                            class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg bg-neutral-50 focus:ring-2 focus:ring-neutral-900 focus:outline-none">
                                    </div>
                                    <!-- Options -->
                                    <div id="provinsiOptions" class="overflow-y-auto" style="max-height: 220px;"></div>
                                </div>
                            </div>

                            <!-- Kabupaten/Kota -->
                            <div class="flex flex-col gap-2" data-wilayah-select="kabupaten">
                                <label class="block text-sm font-semibold">
                                    Kabupaten / Kota <span class="text-red-500">*</span>
                                </label>

                                <input type="hidden" name="kabupaten" id="kabupatenValue" required>
                                <input type="hidden" name="kabupaten_id" id="kabupatenId">

                                <button type="button" id="kabupatenTrigger" disabled
                                    class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-white text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none flex justify-between items-center` transition-all opacity-50 cursor-not-allowed">
                                    <span id="kabupatenLabel" class="text-neutral-400">Pilih provinsi dulu</span>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="size-4 text-neutral-400 transition-transform" fill="none"
                                        viewBox="0 0 24 24" id="kabupatenChevron" stroke-width="2"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>

                                <div id="kabupatenDropdown"
                                    class="hidden absolute z-40 w-full mt-1 bg-white border border-neutral-200 rounded-xl shadow-xl overflow-hidden"
                                    style="max-height: 280px;">
                                    <div class="p-2 border-b border-neutral-100 sticky top-0 bg-white">
                                        <input type="text" id="kabupatenSearch" placeholder="Cari kabupaten / kota..."
                                            class="w-full text-sm px-3 py-2 border border-neutral-200 rounded-lg bg-neutral-50 focus:ring-2 focus:ring-neutral-900 focus:outline-none">
                                    </div>
                                    <div id="kabupatenOptions" class="overflow-y-auto" style="max-height: 220px;"></div>
                                </div>
                            </div>

                            <!-- Kecamatan -->
                            <div class="flex flex-col gap-2">
                                <label class="block text-sm font-semibold">Kecamatan</label>
                                <input type="text" name="kecamatan" placeholder="Coblong"
                                    class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-white text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                            </div>

                            <!-- Kode Pos -->
                            <div class="flex flex-col gap-2">
                                <label class="block text-sm font-semibold">Kode Pos</label>
                                <input type="text" name="kode_pos"
                                    value="<?= htmlspecialchars($customer['kode_pos'] ?? '') ?>" placeholder="40132"
                                    class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-white text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Pengiriman -->
                    <!-- Pengiriman -->
                    <div class="flex flex-col gap-6 p-6 sm:p-8">
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <span
                                class="w-6 h-6 rounded-full bg-neutral-900 text-white text-xs flex items-center justify-center font-bold">2</span>
                            Pengiriman
                        </h2>

                        <!-- State containers -->
                        <div id="shippingPlaceholder"
                            class="text-sm bg-red-50 border border-red-200 text-neutral-400 p-4 flex items-center gap-2 justify-center rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-7 text-red-500" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            <span class="text-sm font-medium trackin-tight text-red-500">Pilih provinsi terlebih dahulu
                                untuk melihat opsi pengiriman</span>
                        </div>
                        <div id="shippingLoading" class="hidden text-sm text-neutral-400 py-2 flex items-center gap-2">
                            <div
                                class="w-4 h-4 border-2 border-neutral-300 border-t-neutral-700 rounded-full animate-spin">
                            </div>
                            Memuat opsi pengiriman...
                        </div>
                        <div id="shippingEmpty"
                            class="hidden text-sm text-yellow-600 bg-yellow-50 border border-yellow-200 px-4 py-3 rounded-lg">
                            Belum ada zona pengiriman untuk provinsi ini. Hubungi kami untuk info ongkir.
                        </div>
                        <div id="shippingOptions" class="flex flex-col gap-3"></div>

                        <!-- Hidden inputs untuk submit -->
                        <input type="hidden" name="ongkir" id="ongkirInput" value="0">
                        <input type="hidden" name="shipping_zone_id" id="shippingZoneIdInput" value="">
                    </div>

                    <!-- Payment Method -->
                    <div class="flex flex-col gap-6 p-6 sm:p-8">
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <span
                                class="w-6 h-6 rounded-full bg-neutral-900 text-white text-xs flex items-center justify-center font-bold">3</span>
                            Metode Pembayaran
                        </h2>

                        <div class="flex flex-col gap-3">
                            <!-- Manual Transfer -->
                            <label
                                class="flex items-start gap-3 p-4 border border-neutral-200 rounded-xl cursor-pointer hover:border-neutral-900 transition-colors has-[:checked]:border-neutral-900 has-[:checked]:bg-neutral-50">
                                <input type="radio" name="payment_method" value="manual_transfer" checked
                                    class="mt-0.5 accent-neutral-900">
                                <div>
                                    <p class="text-sm font-semibold text-neutral-900">Transfer Bank Manual</p>
                                    <p class="text-xs text-neutral-500 mt-0.5">BCA / Mandiri / BRI — konfirmasi setelah
                                        transfer</p>
                                </div>
                            </label>

                            <!-- COD (disabled untuk sementara) -->
                            <label
                                class="flex items-start gap-3 p-4 border border-neutral-200 rounded-xl cursor-not-allowed opacity-50">
                                <input type="radio" name="payment_method" value="cod" disabled class="mt-0.5">
                                <div>
                                    <p class="text-sm font-semibold text-neutral-400">COD (Cash on Delivery)</p>
                                    <p class="text-xs text-neutral-400 mt-0.5">Segera hadir</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="flex flex-col gap-6 p-6 sm:p-8">
                        <h2 class="text-base font-bold text-neutral-900 flex items-center gap-2">
                            <span
                                class="w-6 h-6 rounded-full bg-neutral-900 text-white text-xs flex items-center justify-center font-bold">4</span>
                            Catatan Order <span class="text-xs font-normal text-neutral-400">(opsional)</span>
                        </h2>
                        <textarea name="catatan" rows="7" placeholder="Warna plastik, request khusus, dll..."
                            class="w-full border text-sm tracking-tight rounded-lg px-3 py-2 bg-white text-neutral-700 focus:ring-2 focus:ring-indigo-700 focus:outline-none resize-none"></textarea>
                    </div>
                </div>

            </div>

            <!-- ═══════════════════════════════════════════════════════════════
                 KANAN — ORDER SUMMARY (1/3)
            ════════════════════════════════════════════════════════════════ -->
            <div class="lg:col-span-1 bg-white">
                <div class="p-6 sticky top-20">

                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-bold text-neutral-900">Order Summary</h2>
                        <a href="/cart"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline transition-colors">
                            Edit Cart
                        </a>
                    </div>

                    <!-- Items list -->
                    <div class="flex flex-col divide-y divide-neutral-100">
                        <?php foreach ($items as $item): ?>
                            <div class="flex items-center gap-3 py-3">
                                <!-- Thumbnail -->
                                <div
                                    class="w-20 h-20 shrink-0 rounded-lg overflow-hidden border border-neutral-200 bg-neutral-50">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="/storage/products/<?= htmlspecialchars($item['image']) ?>"
                                            alt="<?= htmlspecialchars($item['title']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-neutral-400 text-xs">—
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-semibold text-neutral-800 leading-snug line-clamp-2">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span
                                            class="text-xs text-neutral-500 bg-neutral-100 border border-neutral-200 px-2 py-0.5 rounded capitalize">
                                            <?= htmlspecialchars($item['color']) ?>
                                        </span>
                                        <span
                                            class="text-xs text-neutral-500 bg-neutral-100 border border-neutral-200 px-2 py-0.5 rounded uppercase">
                                            <?= htmlspecialchars($item['size']) ?>
                                        </span>
                                        <span class="text-xs font-bold text-neutral-400">× <?= (int) $item['qty'] ?></span>
                                    </div>
                                </div>

                                <!-- Price -->
                                <p class="text-xs sm:text-sm font-bold text-neutral-900 shrink-0">
                                    Rp <?= number_format($item['line_total'], 0, ',', '.') ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Totals -->
                    <div class="mt-4 flex flex-col bg-neutral-100 rounded-lg border border-neutral-200">
                        <div class="p-4 flex flex-col gap-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-500">Subtotal</span>
                                <span class="font-semibold text-neutral-900">Rp <span
                                        id="summarySubtotal"><?= number_format($subtotal, 0, ',', '.') ?></span></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-neutral-500">Ongkir</span>
                                <span class="font-semibold text-neutral-900">Rp <span
                                        id="summaryOngkir">25.000</span></span>
                            </div>
                        </div>

                        <div class="flex justify-between text-sm font-bold border-t border-neutral-200 pt-2.5 mt-1 p-4">
                            <span class="text-neutral-900 text-sm sm:text-base">Total</span>
                            <span class="text-neutral-900 text-base sm:text-lg">Rp <span
                                    id="summaryTotal"><?= number_format($subtotal + 25000, 0, ',', '.') ?></span></span>
                        </div>
                    </div>

                    <input type="hidden" id="subtotalValue" value="<?= (int) $subtotal ?>">

                    <!-- Submit -->
                    <button type="submit"
                        class="mt-5 w-full py-3 px-4 bg-neutral-900 hover:bg-neutral-800 text-white text-sm sm:text-base font-semibold rounded-xl transition-colors">
                        Buat Pesanan
                    </button>

                    <p class="text-xs text-neutral-400 text-center mt-3">
                        Dengan melanjutkan, kamu menyetujui syarat & ketentuan kami
                    </p>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        // ═══════════════════════════════════════════════════════
        // WILAYAH DROPDOWN
        // ═══════════════════════════════════════════════════════
        let selectedProvinceId = null;

        const provinsiTrigger = document.getElementById('provinsiTrigger');
        const provinsiDropdown = document.getElementById('provinsiDropdown');
        const provinsiSearch = document.getElementById('provinsiSearch');
        const provinsiOptions = document.getElementById('provinsiOptions');
        const provinsiLabel = document.getElementById('provinsiLabel');
        const provinsiChevron = document.getElementById('provinsiChevron');
        const provinsiValue = document.getElementById('provinsiValue');
        const provinsiId = document.getElementById('provinsiId');

        const kabupatenTrigger = document.getElementById('kabupatenTrigger');
        const kabupatenDropdown = document.getElementById('kabupatenDropdown');
        const kabupatenSearch = document.getElementById('kabupatenSearch');
        const kabupatenOptions = document.getElementById('kabupatenOptions');
        const kabupatenLabel = document.getElementById('kabupatenLabel');
        const kabupatenChevron = document.getElementById('kabupatenChevron');
        const kabupatenValue = document.getElementById('kabupatenValue');
        const kabupatenId = document.getElementById('kabupatenId');

        function toTitleCase(str) {
            return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
        }

        function renderOptions(container, items, onSelect, searchQuery = '') {
            const query = searchQuery.toLowerCase().trim();
            const filtered = query ? items.filter(i => i.name.toLowerCase().includes(query)) : items;

            if (filtered.length === 0) {
                container.innerHTML = `<div class="px-4 py-6 text-center text-sm text-neutral-400">Tidak ditemukan</div>`;
                return;
            }

            container.innerHTML = filtered.map(item => `
            <button type="button" data-id="${item.id}" data-name="${item.name}"
                class="wilayah-option w-full text-left px-4 py-2.5 text-sm text-neutral-700
                    hover:bg-indigo-50 hover:text-indigo-700 transition-colors flex items-center justify-between">
                <span>${toTitleCase(item.name)}</span>
            </button>
        `).join('');

            container.querySelectorAll('.wilayah-option').forEach(btn => {
                btn.addEventListener('click', () => onSelect(btn.dataset.id, btn.dataset.name));
            });
        }

        function openDropdown(trigger, dropdown, chevron, searchEl) {
            const rect = trigger.getBoundingClientRect();
            dropdown.style.position = 'fixed';
            dropdown.style.top = (rect.bottom + 4) + 'px';
            dropdown.style.left = rect.left + 'px';
            dropdown.style.width = rect.width + 'px';
            dropdown.style.zIndex = '9999';
            dropdown.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            setTimeout(() => searchEl?.focus(), 50);
        }

        function closeDropdown(dropdown, chevron) {
            dropdown.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }

        function closeAll() {
            closeDropdown(provinsiDropdown, provinsiChevron);
            closeDropdown(kabupatenDropdown, kabupatenChevron);
        }

        let provincesCache = [];
        let regenciesCache = {};

        async function loadProvinces() {
            if (provincesCache.length > 0) return provincesCache;
            try {
                const res = await fetch('/api/wilayah/provinces');
                const data = await res.json();
                provincesCache = data.data ?? [];
            } catch { provincesCache = []; }
            return provincesCache;
        }

        async function loadRegencies(provinceId) {
            if (regenciesCache[provinceId]) return regenciesCache[provinceId];
            try {
                const res = await fetch(`/api/wilayah/regencies?province_id=${provinceId}`);
                const data = await res.json();
                regenciesCache[provinceId] = data.data ?? [];
            } catch { regenciesCache[provinceId] = []; }
            return regenciesCache[provinceId];
        }

        function onSelectProvince(id, name) {
            selectedProvinceId = id;

            provinsiLabel.textContent = toTitleCase(name);
            provinsiLabel.classList.remove('text-neutral-400');
            provinsiLabel.classList.add('text-neutral-900', 'font-medium');
            provinsiTrigger.classList.add('border-indigo-400');
            provinsiValue.value = toTitleCase(name);
            provinsiId.value = id;

            closeDropdown(provinsiDropdown, provinsiChevron);

            // Reset kabupaten
            kabupatenLabel.textContent = 'Pilih Kabupaten / Kota';
            kabupatenLabel.classList.add('text-neutral-400');
            kabupatenLabel.classList.remove('text-neutral-900', 'font-medium');
            kabupatenValue.value = '';
            kabupatenId.value = '';
            kabupatenTrigger.disabled = false;
            kabupatenTrigger.classList.remove('opacity-50', 'cursor-not-allowed');

            // ← TRIGGER SHIPPING LOAD
            loadShippingZones(id);
        }

        function onSelectKabupaten(id, name) {
            kabupatenLabel.textContent = toTitleCase(name);
            kabupatenLabel.classList.remove('text-neutral-400');
            kabupatenLabel.classList.add('text-neutral-900', 'font-medium');
            kabupatenTrigger.classList.add('border-indigo-400');
            kabupatenValue.value = toTitleCase(name);
            kabupatenId.value = id;
            closeDropdown(kabupatenDropdown, kabupatenChevron);
        }

        provinsiTrigger.addEventListener('click', async () => {
            const isOpen = !provinsiDropdown.classList.contains('hidden');
            closeAll();
            if (isOpen) return;
            openDropdown(provinsiTrigger, provinsiDropdown, provinsiChevron, provinsiSearch);
            const provinces = await loadProvinces();
            renderOptions(provinsiOptions, provinces, onSelectProvince, provinsiSearch.value);
        });

        provinsiSearch.addEventListener('input', async () => {
            const provinces = await loadProvinces();
            renderOptions(provinsiOptions, provinces, onSelectProvince, provinsiSearch.value);
        });

        kabupatenTrigger.addEventListener('click', async () => {
            if (!selectedProvinceId) return;
            const isOpen = !kabupatenDropdown.classList.contains('hidden');
            closeAll();
            if (isOpen) return;
            const regencies = await loadRegencies(selectedProvinceId);
            openDropdown(kabupatenTrigger, kabupatenDropdown, kabupatenChevron, kabupatenSearch);
            renderOptions(kabupatenOptions, regencies, onSelectKabupaten, kabupatenSearch.value);
        });

        kabupatenSearch.addEventListener('input', async () => {
            if (!selectedProvinceId) return;
            const regencies = await loadRegencies(selectedProvinceId);
            renderOptions(kabupatenOptions, regencies, onSelectKabupaten, kabupatenSearch.value);
        });

        document.addEventListener('click', (e) => {
            if (!provinsiTrigger.contains(e.target) && !provinsiDropdown.contains(e.target)) closeDropdown(provinsiDropdown, provinsiChevron);
            if (!kabupatenTrigger.contains(e.target) && !kabupatenDropdown.contains(e.target)) closeDropdown(kabupatenDropdown, kabupatenChevron);
        });

        // ═══════════════════════════════════════════════════════
        // SHIPPING ZONES
        // ═══════════════════════════════════════════════════════
        const summaryOngkir = document.getElementById('summaryOngkir');
        const summaryTotal = document.getElementById('summaryTotal');
        const ongkirInput = document.getElementById('ongkirInput');
        const shippingZoneInput = document.getElementById('shippingZoneIdInput');
        const shippingOptions = document.getElementById('shippingOptions');
        const shippingLoading = document.getElementById('shippingLoading');
        const shippingEmpty = document.getElementById('shippingEmpty');
        const shippingPlaceholder = document.getElementById('shippingPlaceholder');
        const subtotal = parseInt(document.getElementById('subtotalValue')?.value ?? 0);

        function formatRupiah(n) {
            return parseInt(n).toLocaleString('id-ID');
        }

        function updateTotal(cost, zoneId = '') {
            summaryOngkir.textContent = formatRupiah(cost);
            summaryTotal.textContent = formatRupiah(subtotal + cost);
            ongkirInput.value = cost;
            shippingZoneInput.value = zoneId;
        }

        async function loadShippingZones(provinceId) {
            shippingPlaceholder?.classList.add('hidden');
            shippingOptions.innerHTML = '';
            shippingEmpty?.classList.add('hidden');
            shippingLoading?.classList.remove('hidden');

            try {
                const res = await fetch(`/api/shipping/cost?province_id=${provinceId}`);
                const data = await res.json();

                shippingLoading?.classList.add('hidden');

                if (!data.success || data.data.length === 0) {
                    shippingEmpty?.classList.remove('hidden');
                    updateTotal(0);
                    return;
                }

                shippingOptions.innerHTML = data.data.map((zone, i) => `
                <label class="flex items-center gap-3 p-4 border-2 border-neutral-200 rounded-xl cursor-pointer
                    hover:border-neutral-300 hover:bg-white transition-colors has-[:checked]:border-indigo-200 has-[:checked]:bg-indigo-50">
                    <input type="radio" name="_shipping_display" value="${zone.id}"
                        data-cost="${zone.cost}" data-zone-id="${zone.id}"
                        class="shipping-zone-radio shrink-0 text-indigo-800 border"
                        ${i === 0 ? 'checked' : 'border-indigo-800'}>
                    <div class="flex items-center gap-3 flex-1">
                        ${zone.icon
                        ? `<img src="/storage/icons/${zone.icon}" alt="${zone.kurir}" class="w-11 h-11 object-contain rounded-sm has-[:checked]:bg-indigo-200 shrink-0">`
                        : `<div class="w-10 h-10 rounded border border-gray-200 bg-gray-50 flex items-center justify-center text-xs text-gray-500 font-bold shrink-0">${zone.kurir.substring(0, 3).toUpperCase()}</div>`
                    }
                        <div class="flex-1 min-w-0">
                            <p class="text-sm tracking-tight font-semibold text-neutral-900">${zone.kurir}</p>
                            <p class="text-xs tracking-tight text-neutral-500">${zone.name}</p>
                        </div>
                        <p class="text-sm font-bold tracking-tight text-neutral-900 shrink-0">Rp ${formatRupiah(zone.cost)}</p>
                    </div>
                </label>
            `).join('');

                // Auto-select first
                const first = data.data[0];
                updateTotal(first.cost, first.id);

                // Listen change
                document.querySelectorAll('.shipping-zone-radio').forEach(radio => {
                    radio.addEventListener('change', () => {
                        updateTotal(parseInt(radio.dataset.cost), radio.dataset.zoneId);
                    });
                });

            } catch {
                shippingLoading?.classList.add('hidden');
                shippingOptions.innerHTML = `<p class="text-sm text-red-500">Gagal memuat opsi pengiriman</p>`;
            }
        }
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/front.php';
?>