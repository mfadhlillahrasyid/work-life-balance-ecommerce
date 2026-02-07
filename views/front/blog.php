<?php
require_once __DIR__ . '/../../config/database.php';

$posts = array_filter(
    json_read('posts.json'),
    fn($p) => $p['status'] === true && empty($p['deleted_at'])
);

$page_title = 'Blog - WLB Apparel';

ob_start();
?>

<?php breadcrumb(['Home', 'Blog']); ?>

<h1 class="text-2xl font-bold mb-6">Blog</h1>

<div class="space-y-6">
    <?php foreach ($posts as $post): ?>
        <article>
            <h2 class="text-xl font-semibold">
                <a href="/blog/<?= $post['slug'] ?>" class="hover:underline">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
            </h2>
        </article>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/front.php';
