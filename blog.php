<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/content.php';
$articles = published_articles(50, false);
$meta = seo_meta('Blog', 'Read latest doctor articles on wellness, treatment and prevention', ['health blog', 'doctor articles']);
require __DIR__ . '/app/header.php';
?>
<section class="container section">
<h1>Health Articles</h1>
<div class="grid">
<?php foreach ($articles as $article): ?>
<article class="card">
    <h2><a href="/article.php?slug=<?= e($article['slug']) ?>"><?= e($article['title']) ?></a></h2>
    <p><?= e($article['excerpt']) ?></p>
</article>
<?php endforeach; ?>
<?php if (!$articles): ?><p>No published posts yet.</p><?php endif; ?>
</div>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
