<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/hms.php';

$db = Database::connection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('INSERT INTO pharmacy (patient_id, medicine, quantity, unit_price, issue_date) VALUES (:patient_id, :medicine, :quantity, :unit_price, :issue_date)')
        ->execute([
            'patient_id' => (int)$_POST['patient_id'],
            'medicine' => trim($_POST['medicine']),
            'quantity' => (int)$_POST['quantity'],
            'unit_price' => (float)$_POST['unit_price'],
            'issue_date' => $_POST['issue_date'],
        ]);
    addFlash('Pharmacy issue added.');
    header('Location: pharmacy.php');
    exit;
}
$patients = $db->query('SELECT id,name FROM patients ORDER BY name')->fetchAll();
$rows = $db->query('SELECT ph.*, p.name patient_name, (ph.quantity*ph.unit_price) total FROM pharmacy ph JOIN patients p ON p.id=ph.patient_id ORDER BY ph.id DESC')->fetchAll();
renderHeader('Pharmacy');
if ($flash = getFlash()): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<section class="split-grid"><article class="form-card"><h2>Issue Medicine</h2><form method="post" class="grid-form">
<select name="patient_id" required><option value="">Patient</option><?php foreach ($patients as $p): ?><option value="<?= e((string)$p['id']) ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select>
<input name="medicine" placeholder="Medicine" required>
<input type="number" min="1" name="quantity" placeholder="Quantity" required>
<input type="number" min="0" step="0.01" name="unit_price" placeholder="Unit Price" required>
<input type="date" name="issue_date" required>
<button>Save</button></form></article>
<article class="table-card"><h2>Medicine Log</h2><table><thead><tr><th>Patient</th><th>Medicine</th><th>Qty</th><th>Unit</th><th>Total</th><th>Date</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['patient_name']) ?></td><td><?= e($r['medicine']) ?></td><td><?= e((string)$r['quantity']) ?></td><td>$<?= e(number_format((float)$r['unit_price'], 2)) ?></td><td>$<?= e(number_format((float)$r['total'], 2)) ?></td><td><?= e($r['issue_date']) ?></td></tr><?php endforeach; ?>
</tbody></table></article></section>
<?php renderFooter(); ?>
