<?php
$allowedRoles = ['doctor'];
$portalTitle = 'Doctor Portal';
$portalNav = [
    ['href' => '/cygnetclinics.com/doctor/dashboard.php', 'label' => 'Dashboard'],
    ['href' => '/cygnetclinics.com/doctor/appointments.php', 'label' => 'Appointments'],
    ['href' => '/cygnetclinics.com/doctor/prescription_create.php', 'label' => 'New prescription'],
    ['href' => '/cygnetclinics.com/doctor/prescriptions.php', 'label' => 'My prescriptions'],
];
require_once __DIR__ . '/../includes/portal_header.php';

$statement = $pdo->prepare('SELECT a.*, p.name AS patient_name FROM appointments a LEFT JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = :doctor ORDER BY a.scheduled_at ASC LIMIT 10');
$statement->execute(['doctor' => $user['id']]);
$appointments = $statement->fetchAll();

$recentPrescriptions = $pdo->prepare('SELECT pr.*, p.name AS patient_name FROM prescriptions pr LEFT JOIN patients p ON p.id = pr.patient_id WHERE pr.doctor_id = :doctor ORDER BY pr.created_at DESC LIMIT 5');
$recentPrescriptions->execute(['doctor' => $user['id']]);
$prescriptions = $recentPrescriptions->fetchAll();
?>
<h1>Welcome Dr. <?php echo htmlspecialchars($user['name']); ?></h1>
<div class="about-grid">
    <div class="card">
        <h2>Upcoming appointments</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Patient</th>
                <th>Scheduled</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo $appointment['patient_name'] ?? 'Walk-in'; ?></td>
                    <td><?php echo date('d M Y H:i', strtotime($appointment['scheduled_at'])); ?></td>
                    <td><?php echo ucfirst($appointment['status']); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($appointments)): ?>
                <tr><td colspan="3">No appointments scheduled.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h2>Recent prescriptions</h2>
        <ul>
            <?php foreach ($prescriptions as $prescription): ?>
                <li>
                    <strong><?php echo $prescription['patient_name'] ?? 'Walk-in'; ?></strong>
                    <span> &middot; <?php echo date('d M Y H:i', strtotime($prescription['created_at'])); ?></span>
                </li>
            <?php endforeach; ?>
            <?php if (empty($prescriptions)): ?>
                <li>No prescriptions yet.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
