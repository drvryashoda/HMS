<?php
require_once __DIR__ . '/../includes/admin_header.php';

if (is_post()) {
    insert('blog_posts', [
        'title' => sanitize($_POST['title'] ?? ''),
        'content' => sanitize($_POST['content'] ?? ''),
        'published_at' => $_POST['published_at'] ?? date('Y-m-d'),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    flash('success', 'Blog post published.');
    redirect('/cygnetclinics.com/admin/manage_blogs.php');
}

if (isset($_GET['delete'])) {
    delete_row('blog_posts', (int) $_GET['delete']);
    flash('success', 'Blog post removed.');
    redirect('/cygnetclinics.com/admin/manage_blogs.php');
}

$blogs = fetch_all('blog_posts');
$success = flash('success');
?>
<h1>Blog posts</h1>
<?php if ($success): ?>
    <div class="alert alert-success" data-timeout="5000"><?php echo $success; ?></div>
<?php endif; ?>
<div class="card">
    <h2>Create blog post</h2>
    <form method="post">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" required>

        <label for="content">Content</label>
        <textarea name="content" id="content" rows="6" required></textarea>

        <label for="published_at">Publish date</label>
        <input type="date" name="published_at" id="published_at" value="<?php echo date('Y-m-d'); ?>" required>

        <button class="btn btn-primary" type="submit">Publish</button>
    </form>
</div>

<div class="card">
    <h2>Published articles</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Title</th>
            <th>Published</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($blogs as $blog): ?>
            <tr>
                <td><?php echo $blog['title']; ?></td>
                <td><?php echo date('d M Y', strtotime($blog['published_at'])); ?></td>
                <td><a class="link-button" href="?delete=<?php echo $blog['id']; ?>" onclick="return confirm('Delete this post?');">Delete</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($blogs)): ?>
            <tr>
                <td colspan="3">No articles yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
