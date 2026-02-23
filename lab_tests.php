<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/hms.php';

$db = Database::connection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('INSERT INTO lab_tests (patient_id, test_name, ordered_date, result, status) VALUES (:patient_id, :test_name, :ordered_date, :result, :status)')
        ->execute([
            'patient_id' => (int)$_POST['patient_id'],
            'test_name' => trim($_POST['test_name']),
            'ordered_date' => $_POST['ordered_date'],
            'result' => trim($_POST['result']),
            'status' => $_POST['status'],
        ]);
    addFlash('Lab order saved.');
    header('Location: lab_tests.php');
    exit;
}
$patients = $db->query('SELECT id,name FROM patients ORDER BY name')->fetchAll();
$rows = $db->query('SELECT l.*, p.name patient_name FROM lab_tests l JOIN patients p ON p.id=l.patient_id ORDER BY l.id DESC')->fetchAll();
renderHeader('Lab Tests');
if ($flash = getFlash()): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<section class="split-grid"><article class="form-card"><h2>Order Test</h2>
<form method="post" class="grid-form">
<select name="patient_id" required><option value="">Patient</option><?php foreach ($patients as $p): ?><option value="<?= e((string)$p['id']) ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select>
<input name="test_name" placeholder="Test Name" required>
<input type="date" name="ordered_date" required>
<select name="status"><option value="pending">Pending</option><option value="completed">Completed</option></select>
<textarea name="result" placeholder="Result notes"></textarea>
<button>Save Test</button></form></article>
<article class="table-card"><h2>Test Records</h2><table><thead><tr><th>Patient</th><th>Test</th><th>Date</th><th>Status</th><th>Result</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['patient_name']) ?></td><td><?= e($r['test_name']) ?></td><td><?= e($r['ordered_date']) ?></td><td><?= e(ucfirst($r['status'])) ?></td><td><?= e($r['result']) ?></td></tr><?php endforeach; ?>
</tbody></table></article></section>
<?php renderFooter(); ?>
