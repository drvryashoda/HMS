<?php
require_once __DIR__ . '/../includes/admin_header.php';

if (is_post()) {
    $title = sanitize($_POST['title'] ?? '');
    $subtitle = sanitize($_POST['subtitle'] ?? '');
    $image = sanitize($_POST['image_url'] ?? '');
    $cta_label = sanitize($_POST['cta_label'] ?? '');
    $cta_link = sanitize($_POST['cta_link'] ?? '');

    insert('banners', [
        'title' => $title,
        'subtitle' => $subtitle,
        'image_url' => $image,
        'cta_label' => $cta_label,
        'cta_link' => $cta_link,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    flash('success', 'Banner created successfully.');
    redirect('/cygnetclinics.com/admin/manage_banners.php');
}

if (isset($_GET['delete'])) {
    delete_row('banners', (int) $_GET['delete']);
    flash('success', 'Banner removed.');
    redirect('/cygnetclinics.com/admin/manage_banners.php');
}

$banners = fetch_all('banners');
$success = flash('success');
?>
<h1>Homepage banners</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<div class="card">
    <h2>Add new banner</h2>
    <form method="post">
        <label for="title">Headline</label>
        <input type="text" name="title" id="title" required>

        <label for="subtitle">Subheading</label>
        <textarea name="subtitle" id="subtitle" rows="3" required></textarea>

        <label for="image_url">Background image URL</label>
        <input type="url" name="image_url" id="image_url" required>

        <label for="cta_label">CTA label</label>
        <input type="text" name="cta_label" id="cta_label">

        <label for="cta_link">CTA link</label>
        <input type="url" name="cta_link" id="cta_link">

        <button class="btn btn-primary" type="submit">Create banner</button>
    </form>
</div>

<div class="card">
    <h2>Existing banners</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Title</th>
            <th>Subtitle</th>
            <th>Image</th>
            <th>CTA</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($banners as $banner): ?>
            <tr>
                <td><?php echo $banner['title']; ?></td>
                <td><?php echo $banner['subtitle']; ?></td>
                <td><a href="<?php echo $banner['image_url']; ?>" target="_blank">View</a></td>
                <td><?php echo $banner['cta_label']; ?></td>
                <td><a class="link-button" href="?delete=<?php echo $banner['id']; ?>" onclick="return confirm('Delete this banner?');">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($banners)): ?>
            <tr>
                <td colspan="5">No banners yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
