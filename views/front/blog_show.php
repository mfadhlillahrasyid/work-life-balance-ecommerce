<?php
require_once __DIR__ . '/../../config/database.php';

$slug = $_GET['slug'] ?? '';
$posts = json_read('posts.json');

$post = array_values(array_filter($posts, fn($p) => $p['slug'] === $slug))[0] ?? null;

if (!$post) {
    http_response_code(404);
    exit('Post not found');
}

$related = array_filter($posts, function ($p) use ($post) {
    return $p['post_categories_id'] === $post['post_categories_id']
        && $p['slug'] !== $post['slug'];
});

$page_title = $post['title'];

ob_start();
?>

<?php breadcrumb(['Home', 'Blog', $post['title']]); ?>

<article class="prose max-w-none">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <?= $post['content'] ?>
</article>

<section class="mt-10">
    <h3 class="font-semibold mb-4">Related Posts</h3>
    <ul class="list-disc pl-6">
        <?php foreach ($related as $r): ?>
            <li>
                <a href="/blog/<?= $r['slug'] ?>" class="underline">
                    <?= htmlspecialchars($r['title']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/front.php';
