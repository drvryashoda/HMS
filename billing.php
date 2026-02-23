<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/hms.php';

$db = Database::connection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('INSERT INTO billing (patient_id, bill_date, consultation_fee, lab_fee, medicine_fee, room_fee, paid_status) VALUES (:patient_id, :bill_date, :consultation_fee, :lab_fee, :medicine_fee, :room_fee, :paid_status)')
        ->execute([
            'patient_id' => (int)$_POST['patient_id'],
            'bill_date' => $_POST['bill_date'],
            'consultation_fee' => (float)$_POST['consultation_fee'],
            'lab_fee' => (float)$_POST['lab_fee'],
            'medicine_fee' => (float)$_POST['medicine_fee'],
            'room_fee' => (float)$_POST['room_fee'],
            'paid_status' => $_POST['paid_status'],
        ]);
    addFlash('Invoice created.');
    header('Location: billing.php');
    exit;
}
$patients = $db->query('SELECT id,name FROM patients ORDER BY name')->fetchAll();
$rows = $db->query('SELECT b.*, p.name patient_name, (b.consultation_fee+b.lab_fee+b.medicine_fee+b.room_fee) total FROM billing b JOIN patients p ON p.id=b.patient_id ORDER BY b.id DESC')->fetchAll();
renderHeader('Billing');
if ($flash = getFlash()): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<section class="split-grid"><article class="form-card"><h2>Create Bill</h2><form method="post" class="grid-form">
<select name="patient_id" required><option value="">Patient</option><?php foreach ($patients as $p): ?><option value="<?= e((string)$p['id']) ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select>
<input type="date" name="bill_date" required>
<input type="number" step="0.01" name="consultation_fee" placeholder="Consultation Fee" required>
<input type="number" step="0.01" name="lab_fee" placeholder="Lab Fee" required>
<input type="number" step="0.01" name="medicine_fee" placeholder="Medicine Fee" required>
<input type="number" step="0.01" name="room_fee" placeholder="Room Fee" required>
<select name="paid_status"><option value="pending">Pending</option><option value="paid">Paid</option></select>
<button>Save Bill</button></form></article>
<article class="table-card"><h2>Invoices</h2><table><thead><tr><th>Patient</th><th>Date</th><th>Total</th><th>Status</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['patient_name']) ?></td><td><?= e($r['bill_date']) ?></td><td>$<?= e(number_format((float)$r['total'], 2)) ?></td><td><?= e(ucfirst($r['paid_status'])) ?></td></tr><?php endforeach; ?>
</tbody></table></article></section>
<?php renderFooter(); ?>
