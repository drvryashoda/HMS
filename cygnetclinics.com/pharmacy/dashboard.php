<?php
$allowedRoles = ['pharmacy'];
$portalTitle = 'Pharmacy Portal';
$portalNav = [
    ['href' => '/cygnetclinics.com/pharmacy/dashboard.php', 'label' => 'Dashboard'],
    ['href' => '/cygnetclinics.com/pharmacy/prescriptions.php', 'label' => 'Prescriptions'],
];
require_once __DIR__ . '/../includes/portal_header.php';

$pending = $pdo->query("SELECT COUNT(*) FROM pharmacy_orders WHERE status = 'pending'")->fetchColumn();
$dispensed = $pdo->query("SELECT COUNT(*) FROM pharmacy_orders WHERE status = 'dispensed'")->fetchColumn();
?>
<h1>Pharmacy overview</h1>
<div class="about-grid">
    <div class="card">
        <h2>Pending orders</h2>
        <p style="font-size:2rem;font-weight:700;"><?php echo $pending; ?></p>
    </div>
    <div class="card">
        <h2>Dispensed</h2>
        <p style="font-size:2rem;font-weight:700;"><?php echo $dispensed; ?></p>
    </div>
</div>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
