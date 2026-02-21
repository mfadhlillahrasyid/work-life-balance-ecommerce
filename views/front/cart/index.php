<?php
$page_title = 'Shopping Cart';
$cart = $_SESSION['cart'] ?? ['items' => [], 'total_qty' => 0];
ob_start();
?>

<div class="border-b border-neutral-200">
    <div class="w-full">
        <div class="grid grid-cols-1 md:grid-cols-5 items-center w-full">
            <div class="p-4 md:col-span-1">
                <div class="flex items-center justify-between">
                    <a href="/shop"
                        class="flex items-center justify-center w-10 h-10 rounded-full shrink-0 bg-neutral-100 hover:bg-neutral-200 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <h3 class="font-bebas tracking-tight text-3xl block sm:hidden">
                        Shopping Cart
                    </h3>
                </div>
            </div>
            <div class="border-l border-neutral-200 md:col-span-4 hidden sm:block">
                <div class="p-4 flex justify-between items-center w-full">
                    <h3 class="font-bebas tracking-tight text-3xl">
                        Shopping Cart
                    </h3>
                    <div class="flex justify-end truncate w-20">
                        <?php breadcrumb(['Home' => '/', 'Cart' => null]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-full mx-auto gap-4 p-4">
    <?php if (empty($cart['items'])): ?>
        <div class="border rounded-xl p-8 text-center">
            <div class="flex flex-col items-center justify-center gap-4 h-[19rem] sm:h-[40rem]">
                <div class="block">
                    <img src="assets/images/cart.png" class="w-28 sm:w-44" alt="">
                </div>
                <div class="flex flex-col gap-2">
                    <h3 class="text-neutral-800 font-bold text-xl sm:text-3xl tracking-tight">Your cart is empty.</h3>
                    <p class="text-neutral-500 text-sm sm:text-base">Add something to make me Happy :)</p>
                </div>
                <div class="w-full">
                    <a href="/shop"
                        class="inline-block bg-black text-white px-6 py-4 rounded-xl text-sm font-medium hover:bg-neutral-800 active:scale-95 transition-all duration-300">
                        Back to Shop
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- ITEMS -->
            <div class="md:col-span-3 space-y-4 ">
                <div class="relative overflow-x-auto rounded-xl border border-neutral-200">
                    <table class="w-full text-sm text-left text-sm sm:text-base">
                        <thead class="text-sm sm:text-base bg-neutral-100 border-b border-neutral-200">
                            <tr>
                                <th scope="col" class="p-4 font-medium whitespace-nowrap">
                                    #
                                </th>
                                <th scope="col" class="p-4 font-medium whitespace-nowrap">
                                    Product Name
                                </th>
                                <th scope="col" class="p-4 font-medium whitespace-nowrap">
                                    Color & Variant
                                </th>
                                <th scope="col" class="p-4 font-medium whitespace-nowrap">
                                    Qty
                                </th>
                                <th scope="col" class="p-4 font-medium whitespace-nowrap">
                                    Price
                                </th>
                                <th scope="col" class="p-4 font-medium whitespace-nowrap">
                                    Total
                                </th>
                                <th scope="col" class="p-4 font-medium whitespace-nowrap">
                                    Option
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr class="bg-neutral-primary border-b border-neutral-200 last:border-b-0">
                                    <td scope="row" class="p-4 whitespace-nowrap">
                                        <div class="w-12 aspect-square object-cover rounded-lg overflow-hidden">
                                            <?php if ($item['image']): ?>
                                                <img src="/storage/products/<?= htmlspecialchars($item['image']) ?>"
                                                    class="w-full h-full object-cover" loading="lazy">
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td scope="row" class="p-4 font-semibold tracking-tight text-sm sm:text-base whitespace-nowrap">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </td>
                                    <td scope="row" class="p-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-2 items-center">
                                            <span
                                                class="bg-neutral-100 py-1 px-2 rounded-md border border-neutral-200 capitalize text-sm">
                                                <?= htmlspecialchars($item['color']) ?>
                                            </span>
                                            <span
                                                class="bg-neutral-100 py-1 px-2 rounded-md border border-neutral-200 capitalize text-sm">
                                                <?= htmlspecialchars($item['size']) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td scope="row" class="p-4 whitespace-nowrap">
                                        <div class="inline-flex items-center border rounded-full overflow-hidden p-1">
                                            <button type="button"
                                                class="qty-minus w-8 h-8 flex items-center justify-center text-sm sm:text-base font-semibold bg-black/20 hover:bg-indigo-600 text-white rounded-full transition-all duration-300"
                                                data-item-id="<?= htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                −
                                            </button>

                                            <input type="number" min="1" value="<?= (int) ($item['qty'] ?? 1) ?>"
                                                data-item-id="<?= htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                                class="cart-qty text-center w-8 ml-3 text-sm sm:text-base outline-none"
                                                readonly />

                                            <button type="button"
                                                class="qty-plus w-8 h-8 flex items-center justify-center text-sm sm:text-base font-semibold bg-black/20 hover:bg-indigo-600 text-white rounded-full transition-all duration-300"
                                                data-item-id="<?= htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td scope="row" class="p-4 font-semibold text-base whitespace-nowrap">
                                        IDR <?= number_format($item['price'], 0, ',', '.') ?>
                                    </td>
                                    <td scope="row" class="p-4 font-semibold text-base whitespace-nowrap"
                                        data-line-subtotal="<?= htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                        IDR
                                        <?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?>
                                    </td>
                                    <td scope="row" class="p-4 whitespace-nowrap">
                                        <button
                                            class="cart-remove text-red-500 text-sm w-8 h-8 rounded-full hover:bg-red-100 hover:border-red-200 flex items-center justify-center transition-all duration-300"
                                            data-item-id="<?= htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor" class="size-5 text-red-600">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>

                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- SUMMARY -->
            <div class="p-6 sm:p-8 h-fit border border-neutral-200 rounded-xl shadow-2xl shadow-neutral-700/10 sticky top-24">
                <div class="flex flex-col gap-4">
                    <h3 class="font-semibold text-lg sm:text-2xl tracking-tight">Order Summary</h3>
                    <div class="flex justify-between">
                        <span>Total Items</span>
                        <span id="totalItemsCount"><?= array_sum(array_column($items, 'qty')) ?></span>
                    </div>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Subtotal</span>
                        <span id="cartSubtotal">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>
                    <a href="/cart/checkout" class="block bg-black text-white text-center py-3 rounded-lg">
                        Proceed to Checkout
                    </a>
                    <?php if (empty($_SESSION['customer'])): ?>
                        <p class="text-xs text-gray-500 mt-2 text-center">
                            * Login required to checkout
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/front.php';
