<!-- SHARED FILTER FIELDS (Gender, Category, Size, Color, Price) -->

<!-- GENDER FILTER (Hidden if locked) -->
<?php if ($showGenderFilter): ?>
<div class="flex flex-col gap-3 border-b border-neutral-200 p-4">
    <label class="text-sm sm:text-base tracking-tight font-semibold">Gender</label>
    <div class="flex flex-wrap gap-1">
        <?php foreach ($filterGenders as $g): ?>
            <label class="cursor-pointer" data-redirect-type="gender" data-redirect-value="<?= htmlspecialchars($g['slug']) ?>">
                <input 
                    type="checkbox" 
                    name="gender[]" 
                    value="<?= htmlspecialchars($g['slug']) ?>" 
                    class="hidden peer filter-gender"
                    <?= checked('gender', $g['slug']) ?>
                >
                <span class="px-3 py-1.5 rounded-lg bg-white text-sm font-medium border hover:border-indigo-200 hover:bg-indigo-100 hover:text-indigo-600 peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition-all duration-200 capitalize">
                    <?= htmlspecialchars($g['title']) ?>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- CATEGORY FILTER (Hidden if locked) -->
<?php if ($showCategoryFilter): ?>
<div class="flex flex-col gap-3 border-b border-neutral-200 p-4">
    <label class="text-sm sm:text-base tracking-tight font-semibold">Category</label>
    <div class="flex flex-wrap gap-1">
        <?php foreach ($filterCategories as $c): ?>
            <label class="cursor-pointer" data-redirect-type="category" data-redirect-value="<?= htmlspecialchars($c['slug']) ?>">
                <input 
                    type="checkbox" 
                    name="category[]" 
                    value="<?= htmlspecialchars($c['slug']) ?>" 
                    class="hidden peer filter-category"
                    <?= checked('category', $c['slug']) ?>
                >
                <span class="px-3 py-1.5 rounded-lg bg-white text-sm font-medium border hover:border-indigo-200 hover:bg-indigo-100 hover:text-indigo-600 peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition-all duration-200 capitalize">
                    <?= htmlspecialchars($c['title']) ?>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- SIZE FILTER -->
<?php if (!empty($filterSizes)): ?>
<div class="flex flex-col gap-3 border-b border-neutral-200 p-4">
    <label class="text-sm sm:text-base tracking-tight font-semibold">Size</label>
    <div class="flex flex-wrap gap-1">
        <?php foreach ($filterSizes as $size): ?>
            <label class="cursor-pointer">
                <input 
                    type="checkbox" 
                    name="size[]" 
                    value="<?= htmlspecialchars($size) ?>" 
                    class="hidden peer"
                    <?= checked('size', $size) ?>
                >
                <span class="px-3 py-1.5 rounded-lg bg-white text-sm font-medium border hover:border-indigo-200 hover:bg-indigo-100 hover:text-indigo-600 uppercase peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition-all duration-200">
                    <?= htmlspecialchars($size) ?>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- COLOR FILTER -->
<?php if (!empty($filterColors)): ?>
<div class="flex flex-col gap-3 border-b border-neutral-200 p-4">
    <label class="text-sm sm:text-base tracking-tight font-semibold">Color</label>
    <div class="flex flex-wrap gap-1">
        <?php foreach ($filterColors as $color): ?>
            <label class="cursor-pointer">
                <input 
                    type="checkbox" 
                    name="color[]" 
                    value="<?= htmlspecialchars($color) ?>" 
                    class="hidden peer"
                    <?= checked('color', $color) ?>
                >
                <span class="px-3 py-1.5 rounded-lg bg-white text-sm font-medium border hover:border-indigo-200 hover:bg-indigo-100 hover:text-indigo-600 capitalize peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition-all duration-200">
                    <?= htmlspecialchars(ucfirst($color)) ?>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- PRICE RANGE -->
<div class="flex flex-col gap-3 border-b border-neutral-200 p-4">
    <label class="text-sm sm:text-base tracking-tight font-semibold">Price Range</label>
    <div class="flex flex-wrap gap-x-1 gap-y-3">
        <?php foreach ($priceRanges as $value => $label): ?>
            <label class="cursor-pointer">
                <input 
                    type="checkbox" 
                    name="price[]" 
                    value="<?= htmlspecialchars($value) ?>" 
                    class="hidden peer"
                    <?= checked('price', $value) ?>
                >
                <span class="px-3 py-1.5 rounded-lg bg-white text-sm font-medium border hover:border-indigo-200 hover:bg-indigo-100 hover:text-indigo-600 peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition-all duration-200">
                    <?= htmlspecialchars($label) ?>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
</div>