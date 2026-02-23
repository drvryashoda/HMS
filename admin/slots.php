<?php
require __DIR__ . '/_top.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['_csrf'] ?? '')) {
    if (($_POST['action'] ?? '') === 'add_single') {
        $date = $_POST['slot_date'] ?? '';
        $time = $_POST['slot_time'] ?? '';
        if ($date && $time) {
            $pdo->prepare('INSERT IGNORE INTO appointment_slots (slot_date, slot_time, is_booked, created_at) VALUES (:d, :t, 0, :c)')
                ->execute(['d' => $date, 't' => $time, 'c' => now()]);
        }
    }

    if (($_POST['action'] ?? '') === 'add_recurring') {
        $from = $_POST['from_date'] ?? '';
        $to = $_POST['to_date'] ?? '';
        $start = $_POST['start_time'] ?? '';
        $end = $_POST['end_time'] ?? '';
        $interval = max(5, (int) ($_POST['interval_min'] ?? 30));
        $weekdays = array_map('intval', $_POST['weekdays'] ?? []);

        if ($from && $to && $start && $end && $weekdays) {
            $current = strtotime($from);
            $until = strtotime($to);
            while ($current <= $until) {
                $weekday = (int) date('N', $current);
                if (in_array($weekday, $weekdays, true)) {
                    $cursor = strtotime(date('Y-m-d', $current) . ' ' . $start);
                    $endTs = strtotime(date('Y-m-d', $current) . ' ' . $end);
                    while ($cursor < $endTs) {
                        $slotDate = date('Y-m-d', $current);
                        $slotTime = date('H:i:s', $cursor);
                        $pdo->prepare('INSERT IGNORE INTO appointment_slots (slot_date, slot_time, is_booked, created_at) VALUES (:d, :t, 0, :c)')
                            ->execute(['d' => $slotDate, 't' => $slotTime, 'c' => now()]);
                        $cursor = strtotime('+' . $interval . ' minutes', $cursor);
                    }
                }
                $current = strtotime('+1 day', $current);
            }
        }
    }

    if (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare('DELETE FROM appointment_slots WHERE id = :id')->execute(['id' => (int) $_POST['id']]);
    }
}

$slots = $pdo->query('SELECT * FROM appointment_slots ORDER BY slot_date DESC, slot_time DESC LIMIT 300')->fetchAll();
$appointments = $pdo->query('SELECT a.*, s.slot_date, s.slot_time FROM appointments a JOIN appointment_slots s ON s.id = a.slot_id ORDER BY a.created_at DESC')->fetchAll();
?>
<h1>Appointment Calendar & Slots</h1>
<form method="post" class="card inline-form">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="add_single">
    <strong>Single Slot</strong>
    <input type="date" name="slot_date" required>
    <input type="time" name="slot_time" required>
    <button>Add Slot</button>
</form>

<form method="post" class="card form-grid" style="margin-top:16px;">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="add_recurring">
    <h3>Recurring Schedule Generator (Doctor-style)</h3>
    <div class="inline-form">
        <label>From <input type="date" name="from_date" required></label>
        <label>To <input type="date" name="to_date" required></label>
        <label>Start <input type="time" name="start_time" required></label>
        <label>End <input type="time" name="end_time" required></label>
        <label>Interval (min) <input type="number" name="interval_min" value="30" min="5" step="5" required></label>
    </div>
    <div class="inline-form">
        <?php foreach ([1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',7=>'Sun'] as $num => $name): ?>
            <label><input type="checkbox" name="weekdays[]" value="<?= $num ?>" <?= $num <= 5 ? 'checked' : '' ?>> <?= $name ?></label>
        <?php endforeach; ?>
    </div>
    <button>Create Recurring Slots</button>
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
