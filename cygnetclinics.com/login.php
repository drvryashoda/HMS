<?php
require_once __DIR__ . '/includes/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (is_post()) {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $portal = sanitize($_POST['portal'] ?? '');

    if (!$email || !$password || !$portal) {
        flash('error', 'All fields are required.');
    } else {
        $user = authenticate($email, $password);
        if ($user && $user['role'] === $portal) {
            $_SESSION['user'] = $user;
            flash('success', 'Welcome back, ' . $user['name'] . '!');
            switch ($user['role']) {
                case 'admin':
                    redirect('/cygnetclinics.com/admin/dashboard.php');
                    break;
                case 'doctor':
                    redirect('/cygnetclinics.com/doctor/dashboard.php');
                    break;
                case 'receptionist':
                    redirect('/cygnetclinics.com/receptionist/dashboard.php');
                    break;
                case 'lab':
                    redirect('/cygnetclinics.com/lab/dashboard.php');
                    break;
                case 'pharmacy':
                    redirect('/cygnetclinics.com/pharmacy/dashboard.php');
                    break;
                default:
                    flash('error', 'Unsupported portal.');
            }
        } else {
            flash('error', 'Invalid credentials or portal selection.');
        }
    }
}

$success = flash('success');
$error = flash('error');
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<section class="section" id="login">
    <div class="container" style="max-width: 600px;">
        <div class="card">
            <h2>Secure Portal Login</h2>
            <?php if ($success): ?>
                <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error" data-timeout="5000"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post">
                <label for="email">Email address</label>
                <input type="email" name="email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <label for="portal">Portal</label>
                <select name="portal" id="portal" required>
                    <option value="">Select portal</option>
                    <option value="admin">Administrator</option>
                    <option value="doctor">Doctor</option>
                    <option value="receptionist">Receptionist</option>
                    <option value="lab">Lab</option>
                    <option value="pharmacy">Pharmacy</option>
                </select>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="/cygnetclinics.com/index.php" class="link-button">Back to website</a>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
