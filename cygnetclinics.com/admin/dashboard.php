<?php
require_once __DIR__ . '/../includes/admin_header.php';

$totalPatients = $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
$totalAppointments = $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
$totalPrescriptions = $pdo->query('SELECT COUNT(*) FROM prescriptions')->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role <> 'admin'")->fetchColumn();
?>
<h1>Administrator Dashboard</h1>
<div class="dashboard-nav">
    <a class="link-button" href="/cygnetclinics.com/admin/manage_banners.php">Manage home banners</a>
    <a class="link-button" href="/cygnetclinics.com/admin/manage_services.php">Manage services</a>
    <a class="link-button" href="/cygnetclinics.com/admin/manage_blogs.php">Manage blog posts</a>
    <a class="link-button" href="/cygnetclinics.com/admin/manage_staff.php">Manage staff</a>
    <a class="link-button" href="/cygnetclinics.com/admin/manage_contact.php">Contact details</a>
</div>
<div class="about-grid">
    <div class="card">
        <h3>Total patients</h3>
        <p style="font-size: 2rem; font-weight: 700;"><?php echo $totalPatients; ?></p>
    </div>
    <div class="card">
        <h3>Active appointments</h3>
        <p style="font-size: 2rem; font-weight: 700;"><?php echo $totalAppointments; ?></p>
    </div>
    <div class="card">
        <h3>Prescriptions</h3>
        <p style="font-size: 2rem; font-weight: 700;"><?php echo $totalPrescriptions; ?></p>
    </div>
    <div class="card">
        <h3>Team members</h3>
        <p style="font-size: 2rem; font-weight: 700;"><?php echo $totalUsers; ?></p>
    </div>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
