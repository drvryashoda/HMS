<?php
$allowedRoles = ['lab'];
$portalTitle = 'Lab Portal';
$portalNav = [
    ['href' => '/cygnetclinics.com/lab/dashboard.php', 'label' => 'Dashboard'],
    ['href' => '/cygnetclinics.com/lab/tests.php', 'label' => 'Tests'],
];
require_once __DIR__ . '/../includes/portal_header.php';

$pending = $pdo->query("SELECT COUNT(*) FROM lab_tests WHERE status = 'pending'")->fetchColumn();
$processing = $pdo->query("SELECT COUNT(*) FROM lab_tests WHERE status = 'in-progress'")->fetchColumn();
$completed = $pdo->query("SELECT COUNT(*) FROM lab_tests WHERE status = 'completed'")->fetchColumn();
?>
<h1>Laboratory overview</h1>
<div class="about-grid">
    <div class="card">
        <h2>Pending</h2>
        <p style="font-size:2rem;font-weight:700;"><?php echo $pending; ?></p>
    </div>
    <div class="card">
        <h2>In progress</h2>
        <p style="font-size:2rem;font-weight:700;"><?php echo $processing; ?></p>
    </div>
    <div class="card">
        <h2>Completed</h2>
        <p style="font-size:2rem;font-weight:700;"><?php echo $completed; ?></p>
    </div>
</div>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
