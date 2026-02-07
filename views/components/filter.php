<?php
function checked($key, $value)
{
    return isset($_GET[$key]) && in_array($value, (array) $_GET[$key]) ? 'checked' : '';
}

// Context awareness
$filterContext = $filterContext ?? [];
$lockedGender = $filterContext['lockedGender'] ?? null;
$lockedCategory = $filterContext['lockedCategory'] ?? null;

$showGenderFilter = empty($lockedGender) && !empty($filterGenders);
$showCategoryFilter = empty($lockedCategory) && !empty($filterCategories);

// Build form action URL
$baseUrl = $baseUrl ?? '/shop';

// Sort options
$sortOptions = [
    '' => 'Default',
    'newest' => 'Newest First',
    'oldest' => 'Oldest First',
    'price-asc' => 'Price: Low to High',
    'price-desc' => 'Price: High to Low',
];

$currentSort = $activeFilters['sort'][0] ?? '';
$currentSortLabel = $sortOptions[$currentSort] ?? 'Default';
?>

<!-- DESKTOP VERSION (>768px) -->
<div class="hidden sm:block">
    <form method="GET" action="<?= htmlspecialchars($baseUrl) ?>" class="space-y-6 sticky top-24 desktop-filter-form">
        <div class="flex flex-col bg-neutral-100 border border-neutral-200 rounded-xl">

            <div class="flex flex-col gap-3 border-b border-neutral-200 p-4">
                <label class="text-sm sm:text-base tracking-tight font-semibold">Search</label>
                <input type="text" name="q"
                    class="live-search-input w-full border border-neutral-200 rounded-lg py-2 px-3 text-sm text-left flex justify-between items-center bg-white focus:ring-2 focus:ring-black focus:outline-none"
                    value="<?= htmlspecialchars($activeFilters['q'] ?? '') ?>" placeholder="Search product...">
            </div>

            <div class="flex flex-col gap-3 border-b border-neutral-200 p-4">
                <label class="text-sm sm:text-base tracking-tight font-semibold">Sort By</label>
                <div class="relative z-40 sort-select-wrapper">
                    <button type="button"
                        class="sort-trigger w-full border border-neutral-200 rounded-lg cursor-pointer py-2 px-3 text-sm text-left flex justify-between items-center bg-white focus:ring-2 focus:ring-black focus:outline-none">
                        <span class="sort-label <?= $currentSort ? 'text-neutral-800' : 'text-neutral-500' ?>">
                            <?= htmlspecialchars($currentSortLabel) ?>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                        </svg>
                    </button>

                    <input type="hidden" name="sort[]" class="sort-input" value="<?= htmlspecialchars($currentSort) ?>">

                    <div
                        class="sort-dropdown absolute z-[9999] mt-2 w-full bg-white border border-neutral-200 rounded-lg shadow-lg max-h-60 overflow-auto hidden">
                        <?php foreach ($sortOptions as $value => $label): ?>
                            <div class="py-2 px-3 hover:bg-neutral-100 cursor-pointer sort-option <?= $value === $currentSort ? 'bg-neutral-50' : '' ?>"
                                data-value="<?= htmlspecialchars($value) ?>" data-label="<?= htmlspecialchars($label) ?>">
                                <p class="text-sm text-neutral-800"><?= htmlspecialchars($label) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php include __DIR__ . '/filter_fields.php'; ?>

            <div class="w-full">
                <div class="inline-flex items-center gap-2 p-4 w-full">
                    <button type="submit"
                        class="flex-1 bg-black text-white px-5 py-3 rounded-lg text-sm hover:bg-neutral-800 transition-all duration-200">
                        Filter
                    </button>
                    <a href="<?= htmlspecialchars($baseUrl) ?>"
                        class="flex-1 text-center border border-neutral-200 px-5 py-3 rounded-lg text-sm hover:bg-white transition-all duration-200">
                        Reset
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- MOBILE VERSION (<768px) -->
<div class="sm:hidden block">
    <button type="button"
        class="mobile-filter-trigger w-full border border-neutral-200 rounded-lg cursor-pointer py-2 px-3 text-xs tracking-tight text-left flex justify-between items-center bg-white focus:ring-2 focus:ring-black focus:outline-none">
        <span class="text-neutral-500">Filter Product</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
        </svg>
    </button>

    <!-- Modal Drawer -->
    <div
        class="mobile-filter-drawer fixed bottom-0 left-0 right-0 z-[999] w-full h-[30rem] bg-white overflow-hidden border-t border-neutral-200 transform translate-y-full transition-transform duration-300">
        <!-- Header -->
        <div class="border-b border-neutral-200 p-3 flex items-center justify-between bg-neutral-100">
            <h5 class="inline-flex items-center text-sm font-semibold tracking-tight">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="size-5 me-1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                </svg>
                Filter Products
            </h5>
            <button type="button"
                class="mobile-filter-close text-body bg-transparent hover:text-heading hover:bg-neutral-200 active:bg-neutral-300 transition-all duration-200 rounded-lg w-9 h-9 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="overflow-y-auto" style="max-height: calc(30rem - 60px);">
            <form method="GET" action="<?= htmlspecialchars($baseUrl) ?>" class="mobile-filter-form">
                <div class="flex flex-col bg-neutral-100">

                    <!-- SEARCH -->
                    <div class="flex flex-col gap-3 border-b border-neutral-200 p-4">
                        <label class="text-sm sm:text-base tracking-tight font-semibold">Search</label>
                        <input type="text" name="q"
                            class="live-search-input w-full border border-neutral-200 rounded-lg py-2 px-3 text-sm text-left flex justify-between items-center bg-white focus:ring-2 focus:ring-black focus:outline-none"
                            value="<?= htmlspecialchars($activeFilters['q'] ?? '') ?>" placeholder="Search product..."
                            class="w-full border px-3 py-2 rounded-lg text-sm bg-white focus:ring-2 focus:ring-black focus:border-transparent">
                    </div>

                    <!-- SORT BY -->
                    <div class="flex flex-col gap-3 border-b border-neutral-200 p-4">
                        <label class="text-sm sm:text-base tracking-tight font-semibold">Sort By</label>
                        <div class="relative z-40 sort-select-wrapper">
                            <button type="button"
                                class="sort-trigger w-full border border-neutral-200 rounded-lg cursor-pointer py-2 px-3 text-sm text-left flex justify-between items-center bg-white focus:ring-2 focus:ring-black focus:outline-none">
                                <span class="sort-label <?= $currentSort ? 'text-neutral-800' : 'text-neutral-500' ?>">
                                    <?= htmlspecialchars($currentSortLabel) ?>
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                </svg>
                            </button>

                            <input type="hidden" name="sort[]" class="sort-input"
                                value="<?= htmlspecialchars($currentSort) ?>">

                            <div
                                class="sort-dropdown absolute z-[9999] mt-2 w-full bg-white border border-neutral-200 rounded-lg shadow-lg max-h-60 overflow-auto hidden">
                                <?php foreach ($sortOptions as $value => $label): ?>
                                    <div class="py-2 px-3 hover:bg-neutral-100 cursor-pointer sort-option <?= $value === $currentSort ? 'bg-neutral-50' : '' ?>"
                                        data-value="<?= htmlspecialchars($value) ?>"
                                        data-label="<?= htmlspecialchars($label) ?>">
                                        <p class="text-sm text-neutral-800"><?= htmlspecialchars($label) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <?php include __DIR__ . '/filter_fields.php'; ?>

                    <!-- ACTION BUTTONS -->
                    <div class="w-full">
                        <div class="inline-flex items-center gap-2 p-4 w-full">
                            <button type="submit"
                                class="flex-1 bg-black text-white px-5 py-3 rounded-lg text-sm hover:bg-neutral-800 transition-all duration-200">
                                Filter
                            </button>
                            <a href="<?= htmlspecialchars($baseUrl) ?>"
                                class="flex-1 text-center border border-neutral-200 px-5 py-3 rounded-lg text-sm hover:bg-white transition-all duration-200">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Backdrop -->
    <div class="mobile-filter-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm z-[998] hidden"></div>
</div>