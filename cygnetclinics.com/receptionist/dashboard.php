<?php
$allowedRoles = ['receptionist'];
$portalTitle = 'Reception Portal';
$portalNav = [
    ['href' => '/cygnetclinics.com/receptionist/dashboard.php', 'label' => 'Dashboard'],
    ['href' => '/cygnetclinics.com/receptionist/patients.php', 'label' => 'Patients'],
    ['href' => '/cygnetclinics.com/receptionist/appointments.php', 'label' => 'Appointments'],
];
require_once __DIR__ . '/../includes/portal_header.php';

$totalPatients = $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
$todayAppointments = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE DATE(scheduled_at) = CURDATE()');
$todayAppointments->execute();
$todayCount = $todayAppointments->fetchColumn();
$pendingAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status IN ('scheduled','in-progress')")->fetchColumn();
?>
<h1>Welcome <?php echo htmlspecialchars($user['name']); ?></h1>
<div class="about-grid">
    <div class="card">
        <h2>Patients</h2>
        <p style="font-size:2rem;font-weight:700;"><?php echo $totalPatients; ?></p>
    </div>
    <div class="card">
        <h2>Appointments today</h2>
        <p style="font-size:2rem;font-weight:700;"><?php echo $todayCount; ?></p>
    </div>
    <div class="card">
        <h2>Pending appointments</h2>
        <p style="font-size:2rem;font-weight:700;"><?php echo $pendingAppointments; ?></p>
    </div>
</div>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
