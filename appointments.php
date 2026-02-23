<?php
require_once __DIR__ . '/app/bootstrap.php';
$config = load_config();
$meta = seo_meta('Appointments', 'Book appointment slots with your preferred doctor schedule', ['doctor appointment', 'book slot']);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $error = 'Invalid request token.';
    } else {
        $slotId = (int) ($_POST['slot_id'] ?? 0);
        $name = trim($_POST['patient_name'] ?? '');
        $email = trim($_POST['patient_email'] ?? '');
        $phone = trim($_POST['patient_phone'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (!$slotId || !$name || !$email) {
            $error = 'Please complete all required fields.';
        } else {
            $pdo = db();
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('SELECT * FROM appointment_slots WHERE id = :id AND is_booked = 0 FOR UPDATE');
            $stmt->execute(['id' => $slotId]);
            $slot = $stmt->fetch();

            if (!$slot) {
                $pdo->rollBack();
                $error = 'Selected slot is no longer available.';
            } else {
                $ins = $pdo->prepare('INSERT INTO appointments (patient_name, patient_email, patient_phone, notes, slot_id, created_at) VALUES (:name, :email, :phone, :notes, :slot, :created_at)');
                $ins->execute([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'notes' => $notes,
                    'slot' => $slotId,
                    'created_at' => now(),
                ]);

                $pdo->prepare('UPDATE appointment_slots SET is_booked = 1 WHERE id = :id')->execute(['id' => $slotId]);
                $pdo->commit();

                $to = $config['doctor_email'];
                $subject = 'New Appointment Request - ' . $name;
                $body = "Patient: $name\nEmail: $email\nPhone: $phone\nDate: {$slot['slot_date']}\nTime: {$slot['slot_time']}\nNotes: $notes";
                $headers = 'From: noreply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
                @mail($to, $subject, $body, $headers);

                $message = 'Appointment booked successfully. The doctor has been notified.';
            }
        }
    }
}

$slots = db()->query('SELECT * FROM appointment_slots WHERE is_booked = 0 AND slot_date >= CURDATE() ORDER BY slot_date, slot_time')->fetchAll();
$slotsByDate = [];
foreach ($slots as $slot) {
    $slotsByDate[$slot['slot_date']][] = $slot;
}

require __DIR__ . '/app/header.php';
?>
<section class="container section">
<h1>Book an Appointment</h1>
<?php if ($message): ?><p class="alert success"><?= e($message) ?></p><?php endif; ?>
<?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>

<div class="appointment-layout">
    <div class="card">
        <h3>1. Pick a Day</h3>
        <div class="date-chip-wrap" id="date-chips">
            <?php foreach (array_keys($slotsByDate) as $i => $date): ?>
                <button type="button" class="date-chip<?= $i === 0 ? ' active' : '' ?>" data-date="<?= e($date) ?>"><?= e(date('D, M d', strtotime($date))) ?></button>
            <?php endforeach; ?>
            <?php if (!$slotsByDate): ?><p>No slots available right now.</p><?php endif; ?>
        </div>
    </div>

    <form method="post" class="card form-grid">
        <h3>2. Choose Time & Enter Details</h3>
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <select name="slot_id" id="slot-select" required>
            <?php foreach ($slotsByDate as $date => $dateSlots): ?>
                <optgroup label="<?= e($date) ?>" data-date-group="<?= e($date) ?>">
                    <?php foreach ($dateSlots as $slot): ?>
                        <option value="<?= (int) $slot['id'] ?>" data-date="<?= e($date) ?>"><?= e(substr($slot['slot_time'], 0, 5)) ?></option>
                    <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
        </select>
        <input name="patient_name" placeholder="Your Name" required>
        <input type="email" name="patient_email" placeholder="Your Email" required>
        <input name="patient_phone" placeholder="Phone Number">
        <textarea name="notes" placeholder="Symptoms / notes"></textarea>
        <button type="submit">Submit Appointment</button>
    </form>
</div>
</section>
<script>
const chips = document.querySelectorAll('.date-chip');
const select = document.getElementById('slot-select');
function filterSlots(date) {
  const options = select.querySelectorAll('option');
  let firstVisible = null;
  options.forEach(opt => {
    const show = opt.dataset.date === date;
    opt.hidden = !show;
    if (show && !firstVisible) firstVisible = opt;
  });
  if (firstVisible) firstVisible.selected = true;
}
chips.forEach(chip => {
  chip.addEventListener('click', () => {
    chips.forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    filterSlots(chip.dataset.date);
  });
});
if (chips[0]) filterSlots(chips[0].dataset.date);
</script>
<?php require __DIR__ . '/app/footer.php'; ?>
