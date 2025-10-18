<?php
$allowedRoles = ['pharmacy'];
$portalTitle = 'Pharmacy Portal';
$portalNav = [
    ['href' => '/cygnetclinics.com/pharmacy/dashboard.php', 'label' => 'Dashboard'],
    ['href' => '/cygnetclinics.com/pharmacy/prescriptions.php', 'label' => 'Prescriptions'],
];
require_once __DIR__ . '/../includes/portal_header.php';

if (is_post()) {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    $notes = sanitize($_POST['dispense_notes'] ?? '');
    $statement = $pdo->prepare('UPDATE pharmacy_orders SET status = :status, notes = :notes, updated_at = :updated WHERE id = :id');
    $statement->execute([
        'status' => $status,
        'notes' => $notes,
        'updated' => date('Y-m-d H:i:s'),
        'id' => $orderId
    ]);
    flash('success', 'Order updated.');
    redirect('/cygnetclinics.com/pharmacy/prescriptions.php');
}

$success = flash('success');
$orders = $pdo->query('SELECT o.*, p.medications, p.follow_up_date, pt.name AS patient_name, u.name AS doctor_name FROM pharmacy_orders o LEFT JOIN prescriptions p ON p.id = o.prescription_id LEFT JOIN patients pt ON pt.id = p.patient_id LEFT JOIN users u ON u.id = p.doctor_id ORDER BY o.created_at DESC')->fetchAll();
?>
<h1>Prescription dispensing</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<table class="table">
    <thead>
    <tr>
        <th>Patient</th>
        <th>Prescribed by</th>
        <th>Medications</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo $order['patient_name'] ?? 'Walk-in'; ?></td>
            <td><?php echo $order['doctor_name']; ?></td>
            <td><?php echo nl2br($order['medications']); ?></td>
            <td><?php echo ucfirst($order['status']); ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="status">
                        <option value="pending" <?php if ($order['status'] === 'pending') echo 'selected'; ?>>Pending</option>
                        <option value="dispensed" <?php if ($order['status'] === 'dispensed') echo 'selected'; ?>>Dispensed</option>
                        <option value="on-hold" <?php if ($order['status'] === 'on-hold') echo 'selected'; ?>>On hold</option>
                    </select>
                    <textarea name="dispense_notes" rows="2" placeholder="Notes"><?php echo $order['notes']; ?></textarea>
                    <button class="btn btn-primary" type="submit">Update</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($orders)): ?>
        <tr><td colspan="5">No prescriptions pending.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
