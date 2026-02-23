<?php
require_once __DIR__ . '/app/bootstrap.php';
$meta = seo_meta('About', 'Learn about the doctor background, specialization and care philosophy', ['about doctor', 'medical specialist']);
require __DIR__ . '/app/header.php';
?>
<section class="container section narrow">
<h1>About the Doctor</h1>
<p>I am a board-certified physician dedicated to preventive healthcare, chronic disease management, and patient education with a focus on ethical and compassionate outcomes.</p>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
