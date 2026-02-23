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

                $upd = $pdo->prepare('UPDATE appointment_slots SET is_booked = 1 WHERE id = :id');
                $upd->execute(['id' => $slotId]);
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

require __DIR__ . '/app/header.php';
?>
<section class="container section narrow">
<h1>Book an Appointment</h1>
<?php if ($message): ?><p class="alert success"><?= e($message) ?></p><?php endif; ?>
<?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="card form-grid">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <label>Select Slot</label>
    <select name="slot_id" required>
        <option value="">Choose available slot</option>
        <?php foreach ($slots as $slot): ?>
            <option value="<?= (int) $slot['id'] ?>"><?= e($slot['slot_date'] . ' ' . substr($slot['slot_time'], 0, 5)) ?></option>
        <?php endforeach; ?>
    </select>
    <input name="patient_name" placeholder="Your Name" required>
    <input type="email" name="patient_email" placeholder="Your Email" required>
    <input name="patient_phone" placeholder="Phone Number">
    <textarea name="notes" placeholder="Symptoms / notes"></textarea>
    <button type="submit">Submit Appointment</button>
</form>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
