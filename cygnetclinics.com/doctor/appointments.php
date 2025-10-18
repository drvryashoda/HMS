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

if (is_post()) {
    $appointmentId = (int)($_POST['appointment_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    $statement = $pdo->prepare('UPDATE appointments SET status = :status, notes = :notes WHERE id = :id AND doctor_id = :doctor');
    $statement->execute([
        'status' => $status,
        'notes' => $notes,
        'id' => $appointmentId,
        'doctor' => $user['id']
    ]);
    flash('success', 'Appointment updated.');
    redirect('/cygnetclinics.com/doctor/appointments.php');
}

$success = flash('success');
$appointments = $pdo->prepare('SELECT a.*, p.name AS patient_name, p.contact_number FROM appointments a LEFT JOIN patients p ON p.id = a.patient_id WHERE a.doctor_id = :doctor ORDER BY a.scheduled_at DESC');
$appointments->execute(['doctor' => $user['id']]);
$list = $appointments->fetchAll();
?>
<h1>Appointments</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<table class="table">
    <thead>
    <tr>
        <th>Patient</th>
        <th>Scheduled</th>
        <th>Status</th>
        <th>Notes</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($list as $appointment): ?>
        <tr>
            <td>
                <strong><?php echo $appointment['patient_name'] ?? 'Walk-in'; ?></strong><br>
                <small><?php echo $appointment['contact_number'] ?? ''; ?></small>
            </td>
            <td><?php echo date('d M Y H:i', strtotime($appointment['scheduled_at'])); ?></td>
            <td><?php echo ucfirst($appointment['status']); ?></td>
            <td><?php echo nl2br($appointment['notes']); ?></td>
            <td>
                <details>
                    <summary>Update</summary>
                    <form method="post" style="margin-top: 1rem;">
                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                        <label>Status</label>
                        <select name="status" required>
                            <?php foreach (['scheduled','in-progress','completed','cancelled'] as $status): ?>
                                <option value="<?php echo $status; ?>" <?php if ($appointment['status'] === $status) echo 'selected'; ?>><?php echo ucfirst($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label>Notes</label>
                        <textarea name="notes" rows="3"><?php echo $appointment['notes']; ?></textarea>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </details>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($list)): ?>
        <tr><td colspan="5">No appointments.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
