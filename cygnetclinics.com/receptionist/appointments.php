<?php
$allowedRoles = ['receptionist'];
$portalTitle = 'Reception Portal';
$portalNav = [
    ['href' => '/cygnetclinics.com/receptionist/dashboard.php', 'label' => 'Dashboard'],
    ['href' => '/cygnetclinics.com/receptionist/patients.php', 'label' => 'Patients'],
    ['href' => '/cygnetclinics.com/receptionist/appointments.php', 'label' => 'Appointments'],
];
require_once __DIR__ . '/../includes/portal_header.php';

$patients = fetch_all('patients');
$doctors = $pdo->prepare("SELECT id, name FROM users WHERE role = 'doctor'");
$doctors->execute();
$doctorList = $doctors->fetchAll();

if (is_post()) {
    $scheduled = str_replace('T', ' ', $_POST['scheduled_at'] ?? date('Y-m-d H:i'));
    insert('appointments', [
        'patient_id' => (int)($_POST['patient_id'] ?? 0) ?: null,
        'doctor_id' => (int)($_POST['doctor_id'] ?? 0) ?: null,
        'scheduled_at' => $scheduled,
        'status' => 'scheduled',
        'notes' => sanitize($_POST['notes'] ?? ''),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    flash('success', 'Appointment created.');
    redirect('/cygnetclinics.com/receptionist/appointments.php');
}

if (isset($_GET['delete'])) {
    delete_row('appointments', (int) $_GET['delete']);
    flash('success', 'Appointment removed.');
    redirect('/cygnetclinics.com/receptionist/appointments.php');
}

$appointments = $pdo->query('SELECT a.*, p.name AS patient_name, u.name AS doctor_name FROM appointments a LEFT JOIN patients p ON p.id = a.patient_id LEFT JOIN users u ON u.id = a.doctor_id ORDER BY a.scheduled_at DESC')->fetchAll();
$success = flash('success');
?>
<h1>Appointments</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<div class="card">
    <h2>Book appointment</h2>
    <form method="post">
        <label for="patient_id">Patient</label>
        <select name="patient_id" id="patient_id">
            <option value="">Walk-in</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?php echo $patient['id']; ?>"><?php echo $patient['name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="doctor_id">Doctor</label>
        <select name="doctor_id" id="doctor_id">
            <option value="">Any doctor</option>
            <?php foreach ($doctorList as $doctor): ?>
                <option value="<?php echo $doctor['id']; ?>"><?php echo $doctor['name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="scheduled_at">Scheduled date & time</label>
        <input type="datetime-local" name="scheduled_at" id="scheduled_at" required>

        <label for="notes">Notes</label>
        <textarea name="notes" id="notes" rows="3"></textarea>

        <button class="btn btn-primary" type="submit">Save appointment</button>
    </form>
</div>
<div class="card">
    <h2>Appointment list</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Scheduled</th>
            <th>Status</th>
            <th>Notes</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($appointments as $appointment): ?>
            <tr>
                <td><?php echo $appointment['patient_name'] ?? 'Walk-in'; ?></td>
                <td><?php echo $appointment['doctor_name'] ?? 'Unassigned'; ?></td>
                <td><?php echo date('d M Y H:i', strtotime($appointment['scheduled_at'])); ?></td>
                <td><?php echo ucfirst($appointment['status']); ?></td>
                <td><?php echo nl2br($appointment['notes']); ?></td>
                <td><a class="link-button" href="?delete=<?php echo $appointment['id']; ?>" onclick="return confirm('Delete appointment?');">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($appointments)): ?>
            <tr><td colspan="6">No appointments scheduled.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
