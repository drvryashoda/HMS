<?php
$allowedRoles = ['lab'];
$portalTitle = 'Lab Portal';
$portalNav = [
    ['href' => '/cygnetclinics.com/lab/dashboard.php', 'label' => 'Dashboard'],
    ['href' => '/cygnetclinics.com/lab/tests.php', 'label' => 'Tests'],
];
require_once __DIR__ . '/../includes/portal_header.php';

$patients = fetch_all('patients');

if (is_post() && isset($_POST['create_test'])) {
    insert('lab_tests', [
        'patient_id' => (int)($_POST['patient_id'] ?? 0) ?: null,
        'test_name' => sanitize($_POST['test_name'] ?? ''),
        'status' => 'pending',
        'results' => '',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    flash('success', 'Lab test added.');
    redirect('/cygnetclinics.com/lab/tests.php');
}

if (is_post() && isset($_POST['update_test'])) {
    $id = (int)($_POST['test_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    $results = sanitize($_POST['results'] ?? '');
    $completedAt = in_array($status, ['completed']) ? date('Y-m-d H:i:s') : null;
    $statement = $pdo->prepare('UPDATE lab_tests SET status = :status, results = :results, completed_at = :completed WHERE id = :id');
    $statement->execute([
        'status' => $status,
        'results' => $results,
        'completed' => $completedAt,
        'id' => $id
    ]);
    flash('success', 'Lab test updated.');
    redirect('/cygnetclinics.com/lab/tests.php');
}

$success = flash('success');
$tests = $pdo->query('SELECT lt.*, p.name AS patient_name FROM lab_tests lt LEFT JOIN patients p ON p.id = lt.patient_id ORDER BY lt.created_at DESC')->fetchAll();
?>
<h1>Laboratory tests</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<div class="card">
    <h2>Add new test</h2>
    <form method="post">
        <input type="hidden" name="create_test" value="1">
        <label for="patient_id">Patient</label>
        <select name="patient_id" id="patient_id">
            <option value="">Select patient</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?php echo $patient['id']; ?>"><?php echo $patient['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <label for="test_name">Test name</label>
        <input type="text" name="test_name" id="test_name" required>
        <button class="btn btn-primary" type="submit">Create test</button>
    </form>
</div>
<div class="card">
    <h2>Test queue</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Patient</th>
            <th>Test</th>
            <th>Status</th>
            <th>Results</th>
            <th>Updated</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tests as $test): ?>
            <tr>
                <td><?php echo $test['patient_name'] ?? 'Walk-in'; ?></td>
                <td><?php echo $test['test_name']; ?></td>
                <td><?php echo ucfirst($test['status']); ?></td>
                <td><?php echo nl2br($test['results']); ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="update_test" value="1">
                        <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">
                        <select name="status" required>
                            <?php foreach (['pending','in-progress','completed'] as $status): ?>
                                <option value="<?php echo $status; ?>" <?php if ($test['status'] === $status) echo 'selected'; ?>><?php echo ucfirst($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <textarea name="results" rows="3" placeholder="Result notes"><?php echo $test['results']; ?></textarea>
                        <button class="btn btn-primary" type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($tests)): ?>
            <tr><td colspan="5">No tests recorded.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/portal_footer.php'; ?>
