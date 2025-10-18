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

$statement = $pdo->prepare('SELECT pr.*, p.name AS patient_name FROM prescriptions pr LEFT JOIN patients p ON p.id = pr.patient_id WHERE pr.doctor_id = :doctor ORDER BY pr.created_at DESC');
$statement->execute(['doctor' => $user['id']]);
$prescriptions = $statement->fetchAll();
?>
<h1>Prescriptions</h1>
<?php foreach ($prescriptions as $prescription): ?>
    <div class="card">
        <h2><?php echo $prescription['patient_name'] ?? 'Walk-in patient'; ?></h2>
        <p><strong>Date:</strong> <?php echo date('d M Y H:i', strtotime($prescription['created_at'])); ?></p>
        <?php if ($prescription['complaints']): ?>
            <p><strong>Complaints:</strong><br><?php echo nl2br($prescription['complaints']); ?></p>
        <?php endif; ?>
        <?php if ($prescription['diagnosis']): ?>
            <p><strong>Diagnosis:</strong><br><?php echo nl2br($prescription['diagnosis']); ?></p>
        <?php endif; ?>
        <?php if ($prescription['medications']): ?>
            <p><strong>Medications:</strong><br><?php echo nl2br($prescription['medications']); ?></p>
        <?php endif; ?>
        <?php if ($prescription['lab_tests']): ?>
            <p><strong>Lab tests:</strong><br><?php echo nl2br($prescription['lab_tests']); ?></p>
        <?php endif; ?>
        <?php if ($prescription['follow_up_date']): ?>
            <p><strong>Follow up:</strong> <?php echo date('d M Y', strtotime($prescription['follow_up_date'])); ?></p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php if (empty($prescriptions)): ?>
    <p>No prescriptions yet.</p>
<?php endif; ?>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
