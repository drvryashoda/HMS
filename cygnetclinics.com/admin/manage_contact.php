<?php
require_once __DIR__ . '/../includes/admin_header.php';

if (is_post()) {
    $label = sanitize($_POST['label'] ?? '');
    $value = sanitize($_POST['value'] ?? '');
    if ($label && $value) {
        insert('contact_details', [
            'label' => $label,
            'value' => $value,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        flash('success', 'Contact detail added.');
    }
    redirect('/cygnetclinics.com/admin/manage_contact.php');
}

if (isset($_GET['delete'])) {
    delete_row('contact_details', (int) $_GET['delete']);
    flash('success', 'Contact detail removed.');
    redirect('/cygnetclinics.com/admin/manage_contact.php');
}

$details = fetch_all('contact_details');
$success = flash('success');
?>
<h1>Contact details</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<div class="card">
    <h2>Add detail</h2>
    <form method="post">
        <label for="label">Label</label>
        <input type="text" name="label" id="label" placeholder="Phone" required>

        <label for="value">Value</label>
        <input type="text" name="value" id="value" placeholder=" +91 98765 43210" required>

        <button class="btn btn-primary" type="submit">Add</button>
    </form>
</div>
<div class="card">
    <h2>Details</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Label</th>
            <th>Value</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($details as $detail): ?>
            <tr>
                <td><?php echo $detail['label']; ?></td>
                <td><?php echo $detail['value']; ?></td>
                <td><a class="link-button" href="?delete=<?php echo $detail['id']; ?>" onclick="return confirm('Delete detail?');">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($details)): ?>
            <tr>
                <td colspan="3">No contact details yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
