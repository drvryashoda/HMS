<?php
require_once __DIR__ . '/../includes/admin_header.php';

if (is_post()) {
    insert('services', [
        'name' => sanitize($_POST['name'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    flash('success', 'Service saved.');
    redirect('/cygnetclinics.com/admin/manage_services.php');
}

if (isset($_GET['delete'])) {
    delete_row('services', (int) $_GET['delete']);
    flash('success', 'Service removed.');
    redirect('/cygnetclinics.com/admin/manage_services.php');
}

$services = fetch_all('services');
$success = flash('success');
?>
<h1>Clinical services</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<div class="card">
    <h2>Add service</h2>
    <form method="post">
        <label for="name">Service name</label>
        <input type="text" name="name" id="name" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4" required></textarea>

        <button class="btn btn-primary" type="submit">Add service</button>
    </form>
</div>
<div class="card">
    <h2>Existing services</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($services as $service): ?>
            <tr>
                <td><?php echo $service['name']; ?></td>
                <td><?php echo $service['description']; ?></td>
                <td><a class="link-button" href="?delete=<?php echo $service['id']; ?>" onclick="return confirm('Delete this service?');">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($services)): ?>
            <tr>
                <td colspan="3">No services added yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
