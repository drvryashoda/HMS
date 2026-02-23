<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/content.php';

$latest = published_articles(3, false);
$featured = published_articles(6, true);
$meta = seo_meta('Home', 'Doctor portfolio with appointment booking and latest health insights', ['doctor portfolio', 'medical blog', 'appointments']);

require __DIR__ . '/app/header.php';
?>
<section class="hero modern-hero">
    <div class="container hero-inner">
        <div>
            <p class="pill">Compassion. Precision. Care.</p>
            <h1>Personalized Healthcare for Every Stage of Life</h1>
            <p>Evidence-based care, simplified appointments, and practical health education from your trusted physician.</p>
            <div class="hero-actions">
                <a class="btn" href="/appointments.php">Book Appointment</a>
                <a class="btn ghost" href="/blog.php">Read Articles</a>
            </div>
        </div>
        <div class="hero-stat card">
            <h3>Why patients choose us</h3>
            <ul>
                <li>Preventive-first treatment plans</li>
                <li>Flexible slots and online requests</li>
                <li>Clear guidance and follow-up care</li>
            </ul>
        </div>
    </div>
</section>

<section class="container section">
    <div class="section-head"><h2>Latest Articles</h2><a href="/blog.php">View all</a></div>
    <div class="grid">
        <?php foreach ($latest as $article): ?>
            <article class="card article-card">
                <?php if (!empty($article['featured_image'])): ?><img src="<?= e($article['featured_image']) ?>" alt="<?= e($article['title']) ?>"><?php endif; ?>
                <div class="article-body">
                    <h3><a href="/article.php?slug=<?= e($article['slug']) ?>"><?= e($article['title']) ?></a></h3>
                    <p><?= e($article['excerpt']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (!$latest): ?><p>No articles published yet.</p><?php endif; ?>
    </div>
</section>

<section class="container section">
    <div class="section-head"><h2>Featured Articles</h2><a href="/blog.php">Explore blog</a></div>
    <div class="grid">
        <?php foreach ($featured as $article): ?>
            <article class="card article-card">
                <?php if (!empty($article['featured_image'])): ?><img src="<?= e($article['featured_image']) ?>" alt="<?= e($article['title']) ?>"><?php endif; ?>
                <div class="article-body">
                    <h3><a href="/article.php?slug=<?= e($article['slug']) ?>"><?= e($article['title']) ?></a></h3>
                    <p><?= e($article['excerpt']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (!$featured): ?><p>No featured articles selected yet.</p><?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
