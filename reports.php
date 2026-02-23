<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/hms.php';

$db = Database::connection();
$income = (float)$db->query('SELECT COALESCE(SUM(consultation_fee+lab_fee+medicine_fee+room_fee),0) FROM billing')->fetchColumn();
$paid = (float)$db->query("SELECT COALESCE(SUM(consultation_fee+lab_fee+medicine_fee+room_fee),0) FROM billing WHERE paid_status='paid'")->fetchColumn();
$pending = $income - $paid;

$appointmentsByStatus = $db->query('SELECT status, COUNT(*) as total FROM appointments GROUP BY status')->fetchAll();
$admissionsByStatus = $db->query('SELECT status, COUNT(*) as total FROM admissions GROUP BY status')->fetchAll();

renderHeader('Reports');
?>
<section class="stats-grid">
    <article class="stat-card"><h3>Total Revenue</h3><p>$<?= e(number_format($income, 2)) ?></p></article>
    <article class="stat-card"><h3>Collected Revenue</h3><p>$<?= e(number_format($paid, 2)) ?></p></article>
    <article class="stat-card"><h3>Pending Revenue</h3><p>$<?= e(number_format($pending, 2)) ?></p></article>
</section>

<section class="split-grid">
    <article class="table-card">
        <h2>Appointments by Status</h2>
        <table><thead><tr><th>Status</th><th>Total</th></tr></thead><tbody>
        <?php foreach ($appointmentsByStatus as $row): ?><tr><td><?= e(ucfirst(str_replace('_', ' ', $row['status']))) ?></td><td><?= e((string)$row['total']) ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </article>
    <article class="table-card">
        <h2>Admissions by Status</h2>
        <table><thead><tr><th>Status</th><th>Total</th></tr></thead><tbody>
        <?php foreach ($admissionsByStatus as $row): ?><tr><td><?= e(ucfirst($row['status'])) ?></td><td><?= e((string)$row['total']) ?></td></tr><?php endforeach; ?>
        </tbody></table>
    </article>
</section>
<?php renderFooter(); ?>
