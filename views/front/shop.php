<?php
require_once __DIR__ . '/../../config/database.php';

$categorySlug = $_GET['category'] ?? '';

$categories = json_read('product-categories.json');
$products = json_read('products.json');

$category = array_values(array_filter($categories, fn($c) => $c['slug'] === $categorySlug))[0] ?? null;

$page_title = 'Shop - ' . ($category['title'] ?? '');

ob_start();
?>

<?php breadcrumb(['Home', 'Shop', $category['title'] ?? '']); ?>

<h1 class="text-2xl font-bold mb-6"><?= htmlspecialchars($category['title'] ?? '') ?></h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <?php foreach ($products as $product): ?>
        <?php if ($product['product_categories_id'] == ($category['id'] ?? null)): ?>
            <div class="border rounded p-4">
                <h3><?= htmlspecialchars($product['title']) ?></h3>
                <p class="text-sm">Rp<?= number_format($product['price']) ?></p>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/front.php';
