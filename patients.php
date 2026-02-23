<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/hms.php';

$db = Database::connection();

if (isset($_GET['delete'])) {
    $stmt = $db->prepare('DELETE FROM patients WHERE id = :id');
    $stmt->execute(['id' => (int)$_GET['delete']]);
    addFlash('Patient deleted successfully.');
    header('Location: patients.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare('INSERT INTO patients (name, gender, date_of_birth, phone, email, address, blood_group) VALUES (:name, :gender, :dob, :phone, :email, :address, :blood)');
    $stmt->execute([
        'name' => trim($_POST['name']),
        'gender' => $_POST['gender'],
        'dob' => $_POST['date_of_birth'],
        'phone' => trim($_POST['phone']),
        'email' => trim($_POST['email']),
        'address' => trim($_POST['address']),
        'blood' => trim($_POST['blood_group']),
    ]);
    addFlash('Patient added successfully.');
    header('Location: patients.php');
    exit;
}

$patients = $db->query('SELECT * FROM patients ORDER BY id DESC')->fetchAll();

renderHeader('Patients');
if ($flash = getFlash()): ?>
    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
<section class="split-grid">
    <article class="form-card">
        <h2>Add Patient</h2>
        <form method="post" class="grid-form">
            <input name="name" placeholder="Full Name" required>
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <input type="date" name="date_of_birth" required>
            <input name="phone" placeholder="Phone" required>
            <input type="email" name="email" placeholder="Email">
            <input name="blood_group" placeholder="Blood Group (e.g. O+)">
            <textarea name="address" placeholder="Address"></textarea>
            <button type="submit">Save Patient</button>
        </form>
    </article>

    <article class="table-card">
        <h2>Patient Directory</h2>
        <table>
            <thead><tr><th>ID</th><th>Name</th><th>Gender</th><th>Phone</th><th>Blood</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?= e((string)$patient['id']) ?></td>
                    <td><?= e($patient['name']) ?></td>
                    <td><?= e(ucfirst($patient['gender'])) ?></td>
                    <td><?= e($patient['phone']) ?></td>
                    <td><?= e($patient['blood_group']) ?></td>
                    <td><a class="danger-link" onclick="return confirm('Delete this patient?')" href="?delete=<?= e((string)$patient['id']) ?>">Delete</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </article>
</section>
<?php renderFooter(); ?>
