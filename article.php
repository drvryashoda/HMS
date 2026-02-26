<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/content.php';

$slug = $_GET['slug'] ?? '';
$article = article_with_terms($slug);
if (!$article) {
    http_response_code(404);
    echo 'Article not found';
    exit;
}

$meta = seo_meta(
    $article['seo_title'] ?: $article['title'],
    $article['seo_description'] ?: ($article['excerpt'] ?: substr(strip_tags($article['body']), 0, 150)),
    array_map(fn($t) => $t['name'], $article['tags'])
);

require __DIR__ . '/app/header.php';
?>
<section class="container section narrow article-detail">
    <article>
        <?php if (!empty($article['featured_image'])): ?><img class="article-hero" src="<?= e($article['featured_image']) ?>" alt="<?= e($article['title']) ?>"><?php endif; ?>
        <h1><?= e($article['title']) ?></h1>
        <p><?= date('F d, Y', strtotime($article['created_at'])) ?></p>
        <div><?= nl2br(e($article['body'])) ?></div>
        <?php if ($article['categories']): ?>
        <p><strong>Categories:</strong> <?= e(implode(', ', array_column($article['categories'], 'name'))) ?></p>
        <?php endif; ?>
        <?php if ($article['tags']): ?>
        <p><strong>Tags:</strong> <?= e(implode(', ', array_column($article['tags'], 'name'))) ?></p>
        <?php endif; ?>
    </article>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
