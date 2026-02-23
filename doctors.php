<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/hms.php';

$db = Database::connection();

if (isset($_GET['delete'])) {
    $db->prepare('DELETE FROM doctors WHERE id = :id')->execute(['id' => (int)$_GET['delete']]);
    addFlash('Doctor removed successfully.');
    header('Location: doctors.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db->prepare('INSERT INTO doctors (name, specialization, phone, email, room_no) VALUES (:name, :specialization, :phone, :email, :room_no)')
        ->execute([
            'name' => trim($_POST['name']),
            'specialization' => trim($_POST['specialization']),
            'phone' => trim($_POST['phone']),
            'email' => trim($_POST['email']),
            'room_no' => trim($_POST['room_no']),
        ]);
    addFlash('Doctor added successfully.');
    header('Location: doctors.php');
    exit;
}

$doctors = $db->query('SELECT * FROM doctors ORDER BY id DESC')->fetchAll();

renderHeader('Doctors');
if ($flash = getFlash()): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
<section class="split-grid">
    <article class="form-card">
        <h2>Add Doctor</h2>
        <form method="post" class="grid-form">
            <input name="name" placeholder="Doctor Name" required>
            <input name="specialization" placeholder="Specialization" required>
            <input name="phone" placeholder="Phone" required>
            <input type="email" name="email" placeholder="Email">
            <input name="room_no" placeholder="Room Number">
            <button type="submit">Save Doctor</button>
        </form>
    </article>

    <article class="table-card">
        <h2>Doctor Directory</h2>
        <table>
            <thead><tr><th>ID</th><th>Name</th><th>Specialization</th><th>Phone</th><th>Room</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td><?= e((string)$doctor['id']) ?></td>
                    <td><?= e($doctor['name']) ?></td>
                    <td><?= e($doctor['specialization']) ?></td>
                    <td><?= e($doctor['phone']) ?></td>
                    <td><?= e($doctor['room_no']) ?></td>
                    <td><a class="danger-link" onclick="return confirm('Delete this doctor?')" href="?delete=<?= e((string)$doctor['id']) ?>">Delete</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </article>
</section>
<?php renderFooter(); ?>
