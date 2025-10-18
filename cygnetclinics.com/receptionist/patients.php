<?php
$allowedRoles = ['receptionist'];
$portalTitle = 'Reception Portal';
$portalNav = [
    ['href' => '/cygnetclinics.com/receptionist/dashboard.php', 'label' => 'Dashboard'],
    ['href' => '/cygnetclinics.com/receptionist/patients.php', 'label' => 'Patients'],
    ['href' => '/cygnetclinics.com/receptionist/appointments.php', 'label' => 'Appointments'],
];
require_once __DIR__ . '/../includes/portal_header.php';

if (is_post()) {
    insert('patients', [
        'name' => sanitize($_POST['name'] ?? ''),
        'date_of_birth' => $_POST['date_of_birth'] ?: null,
        'gender' => sanitize($_POST['gender'] ?? ''),
        'contact_number' => sanitize($_POST['contact_number'] ?? ''),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    flash('success', 'Patient added.');
    redirect('/cygnetclinics.com/receptionist/patients.php');
}

if (isset($_GET['delete'])) {
    delete_row('patients', (int) $_GET['delete']);
    flash('success', 'Patient removed.');
    redirect('/cygnetclinics.com/receptionist/patients.php');
}

$patients = fetch_all('patients');
$success = flash('success');
?>
<h1>Patients</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<div class="card">
    <h2>Add patient</h2>
    <form method="post">
        <label for="name">Full name</label>
        <input type="text" name="name" id="name" required>

        <label for="date_of_birth">Date of birth</label>
        <input type="date" name="date_of_birth" id="date_of_birth">

        <label for="gender">Gender</label>
        <select name="gender" id="gender">
            <option value="">Select</option>
            <option value="female">Female</option>
            <option value="male">Male</option>
            <option value="other">Other</option>
        </select>

        <label for="contact_number">Contact number</label>
        <input type="text" name="contact_number" id="contact_number">

        <button class="btn btn-primary" type="submit">Save</button>
    </form>
</div>
<div class="card">
    <h2>Patient list</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Contact</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($patients as $patient): ?>
            <tr>
                <td><?php echo $patient['name']; ?></td>
                <td><?php echo $patient['date_of_birth']; ?></td>
                <td><?php echo ucfirst($patient['gender']); ?></td>
                <td><?php echo $patient['contact_number']; ?></td>
                <td><a class="link-button" href="?delete=<?php echo $patient['id']; ?>" onclick="return confirm('Delete patient?');">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($patients)): ?>
            <tr><td colspan="5">No patients recorded.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
