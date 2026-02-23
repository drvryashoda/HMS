<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/hms.php';

$db = Database::connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('INSERT INTO admissions (patient_id, ward, bed_no, admit_date, discharge_date, diagnosis, status) VALUES (:patient_id, :ward, :bed_no, :admit_date, :discharge_date, :diagnosis, :status)')
        ->execute([
            'patient_id' => (int)$_POST['patient_id'],
            'ward' => trim($_POST['ward']),
            'bed_no' => trim($_POST['bed_no']),
            'admit_date' => $_POST['admit_date'],
            'discharge_date' => $_POST['discharge_date'] ?: null,
            'diagnosis' => trim($_POST['diagnosis']),
            'status' => $_POST['status'],
        ]);
    addFlash('Admission recorded.');
    header('Location: admissions.php');
    exit;
}

$patients = $db->query('SELECT id, name FROM patients ORDER BY name')->fetchAll();
$rows = $db->query('SELECT a.*, p.name AS patient_name FROM admissions a JOIN patients p ON p.id=a.patient_id ORDER BY a.id DESC')->fetchAll();

renderHeader('Admissions');
if ($flash = getFlash()): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<section class="split-grid">
<article class="form-card">
    <h2>New Admission</h2>
    <form method="post" class="grid-form">
        <select name="patient_id" required><option value="">Patient</option><?php foreach ($patients as $p): ?><option value="<?= e((string)$p['id']) ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select>
        <input name="ward" placeholder="Ward" required>
        <input name="bed_no" placeholder="Bed #" required>
        <input type="date" name="admit_date" required>
        <input type="date" name="discharge_date">
        <select name="status"><option value="admitted">Admitted</option><option value="discharged">Discharged</option></select>
        <textarea name="diagnosis" placeholder="Diagnosis"></textarea>
        <button>Save Admission</button>
    </form>
</article>
<article class="table-card">
<h2>Admission History</h2>
<table><thead><tr><th>Patient</th><th>Ward/Bed</th><th>Admit</th><th>Discharge</th><th>Status</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['patient_name']) ?></td><td><?= e($r['ward'] . ' / ' . $r['bed_no']) ?></td><td><?= e($r['admit_date']) ?></td><td><?= e((string)$r['discharge_date']) ?></td><td><?= e(ucfirst($r['status'])) ?></td></tr><?php endforeach; ?>
</tbody></table>
</article></section>
<?php renderFooter(); ?>
