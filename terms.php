<?php
require_once __DIR__ . '/app/bootstrap.php';
$meta = seo_meta('Terms and Conditions', 'Terms and conditions for using this doctor portfolio website', ['terms', 'conditions']);
require __DIR__ . '/app/header.php';
?>
<section class="container section narrow">
<h1>Terms and Conditions</h1>
<p>All published content is for educational purposes and does not replace in-person clinical diagnosis. Appointment requests are subject to confirmation.</p>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
