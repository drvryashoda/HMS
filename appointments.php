<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/hms.php';

$db = Database::connection();

if (isset($_GET['complete'])) {
    $db->prepare("UPDATE appointments SET status = 'completed' WHERE id = :id")->execute(['id' => (int)$_GET['complete']]);
    addFlash('Appointment marked completed.');
    header('Location: appointments.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason, status) VALUES (:patient_id, :doctor_id, :appointment_date, :reason, :status)')
        ->execute([
            'patient_id' => (int)$_POST['patient_id'],
            'doctor_id' => (int)$_POST['doctor_id'],
            'appointment_date' => $_POST['appointment_date'],
            'reason' => trim($_POST['reason']),
            'status' => $_POST['status'],
        ]);
    addFlash('Appointment scheduled.');
    header('Location: appointments.php');
    exit;
}

$patients = $db->query('SELECT id, name FROM patients ORDER BY name')->fetchAll();
$doctors = $db->query('SELECT id, name, specialization FROM doctors ORDER BY name')->fetchAll();
$appointments = $db->query('SELECT a.*, p.name AS patient_name, d.name AS doctor_name FROM appointments a JOIN patients p ON p.id=a.patient_id JOIN doctors d ON d.id=a.doctor_id ORDER BY a.appointment_date DESC')->fetchAll();

renderHeader('Appointments');
if ($flash = getFlash()): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
<section class="split-grid">
    <article class="form-card">
        <h2>Schedule Appointment</h2>
        <form method="post" class="grid-form">
            <select name="patient_id" required>
                <option value="">Select patient</option>
                <?php foreach ($patients as $p): ?><option value="<?= e((string)$p['id']) ?>"><?= e($p['name']) ?></option><?php endforeach; ?>
            </select>
            <select name="doctor_id" required>
                <option value="">Select doctor</option>
                <?php foreach ($doctors as $d): ?><option value="<?= e((string)$d['id']) ?>"><?= e($d['name'] . ' - ' . $d['specialization']) ?></option><?php endforeach; ?>
            </select>
            <input type="datetime-local" name="appointment_date" required>
            <select name="status"><option value="scheduled">Scheduled</option><option value="in_progress">In Progress</option></select>
            <textarea name="reason" placeholder="Reason for visit" required></textarea>
            <button type="submit">Create Appointment</button>
        </form>
    </article>

    <article class="table-card">
        <h2>Appointment Queue</h2>
        <table>
            <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($appointments as $appt): ?>
                <tr>
                    <td><?= e($appt['patient_name']) ?></td>
                    <td><?= e($appt['doctor_name']) ?></td>
                    <td><?= e($appt['appointment_date']) ?></td>
                    <td><?= e(ucfirst(str_replace('_', ' ', $appt['status']))) ?></td>
                    <td>
                        <?php if ($appt['status'] !== 'completed'): ?>
                            <a href="?complete=<?= e((string)$appt['id']) ?>">Mark complete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </article>
</section>
<?php renderFooter(); ?>
