<?php
require __DIR__ . '/_top.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['_csrf'] ?? '')) {
    if (!empty($_POST['name'])) {
        $name = trim($_POST['name']);
        $stmt = db()->prepare('INSERT INTO categories (name, slug, created_at) VALUES (:name, :slug, :created_at)');
        $stmt->execute(['name' => $name, 'slug' => generate_slug($name), 'created_at' => now()]);
    }
    if (!empty($_POST['delete_id'])) {
        $stmt = db()->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => (int) $_POST['delete_id']]);
    }
}

$items = db()->query('SELECT * FROM categories ORDER BY created_at DESC')->fetchAll();
?>
<h1>Categories</h1>
<form method="post" class="card inline-form">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <input name="name" placeholder="Category name" required>
    <button>Add Category</button>
</form>
<table class="table"><tr><th>Name</th><th>Action</th></tr>
<?php foreach ($items as $item): ?>
<tr><td><?= e($item['name']) ?></td><td>
<form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="delete_id" value="<?= (int) $item['id'] ?>"><button>Delete</button></form>
</td></tr>
<?php endforeach; ?></table>
<?php require __DIR__ . '/_bottom.php'; ?>
