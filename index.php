<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/content.php';

$latest = published_articles(3, false);
$featured = published_articles(6, true);
$meta = seo_meta('Home', 'Doctor portfolio with appointment booking and latest health insights', ['doctor portfolio', 'medical blog', 'appointments']);

require __DIR__ . '/app/header.php';
?>
<section class="hero">
    <div class="container hero-inner">
        <div>
            <p class="pill">Compassion. Precision. Care.</p>
            <h1>Personalized Healthcare for Every Stage of Life</h1>
            <p>Welcome to my medical portfolio where I share evidence-based wellness guidance, patient-first treatment philosophy, and accessible appointment booking.</p>
            <a class="btn" href="/appointments.php">Book Appointment</a>
        </div>
    </div>
</section>

<section class="container section">
    <h2>Latest Articles</h2>
    <div class="grid">
        <?php foreach ($latest as $article): ?>
            <article class="card">
                <h3><a href="/article.php?slug=<?= e($article['slug']) ?>"><?= e($article['title']) ?></a></h3>
                <p><?= e($article['excerpt']) ?></p>
            </article>
        <?php endforeach; ?>
        <?php if (!$latest): ?><p>No articles published yet.</p><?php endif; ?>
    </div>
</section>

<section class="container section">
    <h2>Featured Articles</h2>
    <div class="grid">
        <?php foreach ($featured as $article): ?>
            <article class="card">
                <h3><a href="/article.php?slug=<?= e($article['slug']) ?>"><?= e($article['title']) ?></a></h3>
                <p><?= e($article['excerpt']) ?></p>
            </article>
        <?php endforeach; ?>
        <?php if (!$featured): ?><p>No featured articles selected yet.</p><?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
