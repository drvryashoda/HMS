<?php
require_once __DIR__ . '/../includes/admin_header.php';

if (is_post()) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $role = sanitize($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || !$role || !$password) {
        flash('error', 'All fields are required.');
    } else {
        $statement = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $statement->execute(['email' => $email]);
        if ($statement->fetchColumn() > 0) {
            flash('error', 'Email already exists.');
        } else {
            insert('users', [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => $role,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            flash('success', ucfirst($role) . ' account created.');
        }
    }
    redirect('/cygnetclinics.com/admin/manage_staff.php');
}

if (isset($_GET['delete'])) {
    delete_row('users', (int) $_GET['delete']);
    flash('success', 'Account removed.');
    redirect('/cygnetclinics.com/admin/manage_staff.php');
}

$members = $pdo->query("SELECT * FROM users WHERE role <> 'admin' ORDER BY created_at DESC")->fetchAll();
$success = flash('success');
$error = flash('error');
?>
<h1>Team accounts</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error" data-timeout="5000"><?php echo $error; ?></div>
<?php endif; ?>
<div class="card">
    <h2>Create account</h2>
    <form method="post">
        <label for="name">Full name</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Email address</label>
        <input type="email" name="email" id="email" required>

        <label for="role">Role</label>
        <select name="role" id="role" required>
            <option value="">Select role</option>
            <option value="doctor">Doctor</option>
            <option value="receptionist">Receptionist</option>
            <option value="lab">Lab</option>
            <option value="pharmacy">Pharmacy</option>
        </select>

        <label for="password">Temporary password</label>
        <input type="text" name="password" id="password" required>

        <button class="btn btn-primary" type="submit">Create account</button>
    </form>
</div>
<div class="card">
    <h2>Existing staff</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($members as $member): ?>
            <tr>
                <td><?php echo $member['name']; ?></td>
                <td><?php echo $member['email']; ?></td>
                <td><?php echo ucfirst($member['role']); ?></td>
                <td><?php echo date('d M Y', strtotime($member['created_at'])); ?></td>
                <td>
                    <a class="link-button" href="?delete=<?php echo $member['id']; ?>" onclick="return confirm('Delete account?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($members)): ?>
            <tr>
                <td colspan="5">No staff accounts yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
