<?php
require __DIR__ . '/_top.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['_csrf'] ?? '')) {
    if (($_POST['action'] ?? '') === 'add') {
        $date = $_POST['slot_date'] ?? '';
        $time = $_POST['slot_time'] ?? '';
        if ($date && $time) {
            $pdo->prepare('INSERT INTO appointment_slots (slot_date, slot_time, is_booked, created_at) VALUES (:d, :t, 0, :c)')
                ->execute(['d' => $date, 't' => $time, 'c' => now()]);
        }
    }

    if (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare('DELETE FROM appointment_slots WHERE id = :id')->execute(['id' => (int) $_POST['id']]);
    }
}

$slots = $pdo->query('SELECT * FROM appointment_slots ORDER BY slot_date DESC, slot_time DESC')->fetchAll();
$appointments = $pdo->query('SELECT a.*, s.slot_date, s.slot_time FROM appointments a JOIN appointment_slots s ON s.id = a.slot_id ORDER BY a.created_at DESC')->fetchAll();
?>
<h1>Appointment Slots</h1>
<form method="post" class="card inline-form">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="add">
    <input type="date" name="slot_date" required>
    <input type="time" name="slot_time" required>
    <button>Add Slot</button>
</form>

<table class="table"><tr><th>Date</th><th>Time</th><th>Status</th><th>Action</th></tr>
<?php foreach ($slots as $s): ?>
<tr><td><?= e($s['slot_date']) ?></td><td><?= e(substr($s['slot_time'], 0, 5)) ?></td><td><?= $s['is_booked'] ? 'Booked' : 'Open' ?></td>
<td><form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $s['id'] ?>"><button>Delete</button></form></td></tr>
<?php endforeach; ?></table>

<h2>Booked Appointments</h2>
<table class="table"><tr><th>Name</th><th>Email</th><th>Phone</th><th>Slot</th><th>Notes</th></tr>
<?php foreach ($appointments as $a): ?>
<tr>
<td><?= e($a['patient_name']) ?></td><td><?= e($a['patient_email']) ?></td><td><?= e($a['patient_phone']) ?></td><td><?= e($a['slot_date'] . ' ' . substr($a['slot_time'], 0, 5)) ?></td><td><?= e($a['notes']) ?></td>
</tr>
<?php endforeach; ?></table>
<?php require __DIR__ . '/_bottom.php'; ?>
