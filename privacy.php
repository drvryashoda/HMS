<?php
require_once __DIR__ . '/app/bootstrap.php';
$meta = seo_meta('Privacy Policy', 'Privacy policy for patient data and website communication', ['privacy policy', 'patient data']);
require __DIR__ . '/app/header.php';
?>
<section class="container section narrow">
<h1>Privacy Policy</h1>
<p>We collect only information required for appointment scheduling and communication. Personal details are never sold and are securely managed.</p>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
