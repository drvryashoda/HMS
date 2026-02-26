<?php
require_once __DIR__ . '/app/bootstrap.php';
$meta = seo_meta('Contact', 'Contact the doctor clinic for medical consultation and support', ['doctor contact', 'clinic support']);
require __DIR__ . '/app/header.php';
?>
<section class="container section narrow">
<h1>Contact</h1>
<p>Email us at <?= e(load_config()['doctor_email']) ?> for enquiries and collaborations.</p>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
