<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/hms.php';

$stats = [
    'Total Patients' => countTable('patients'),
    'Total Doctors' => countTable('doctors'),
    'Appointments' => countTable('appointments'),
    'Admissions' => countTable('admissions'),
    'Lab Orders' => countTable('lab_tests'),
    'Invoices' => countTable('billing'),
];

$recentAppointments = Database::connection()->query(
    'SELECT a.id, p.name AS patient_name, d.name AS doctor_name, a.appointment_date, a.status
     FROM appointments a
     JOIN patients p ON p.id = a.patient_id
     JOIN doctors d ON d.id = a.doctor_id
     ORDER BY a.appointment_date DESC
     LIMIT 5'
)->fetchAll();

renderHeader('Dashboard');
$flash = getFlash();
if ($flash):
?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>

<section class="stats-grid">
    <?php foreach ($stats as $label => $value): ?>
        <article class="stat-card">
            <h3><?= e($label) ?></h3>
            <p><?= e((string)$value) ?></p>
        </article>
    <?php endforeach; ?>
</section>

<section class="table-card">
    <h2>Recent Appointments</h2>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($recentAppointments as $appt): ?>
            <tr>
                <td><?= e((string)$appt['id']) ?></td>
                <td><?= e($appt['patient_name']) ?></td>
                <td><?= e($appt['doctor_name']) ?></td>
                <td><?= e($appt['appointment_date']) ?></td>
                <td><?= e(ucfirst($appt['status'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php renderFooter(); ?>
