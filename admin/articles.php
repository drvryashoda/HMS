<?php
require __DIR__ . '/_top.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['_csrf'] ?? '')) {
    if (($_POST['action'] ?? '') === 'create') {
        $title = trim($_POST['title'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $seoTitle = trim($_POST['seo_title'] ?? '');
        $seoDescription = trim($_POST['seo_description'] ?? '');
        $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
        $featured = !empty($_POST['featured']) ? 1 : 0;
        $slug = generate_slug($title);

        $stmt = $pdo->prepare('INSERT INTO articles (title, slug, excerpt, body, seo_title, seo_description, status, featured, created_at, updated_at) VALUES (:title, :slug, :excerpt, :body, :seo_title, :seo_description, :status, :featured, :created_at, :updated_at)');
        $stmt->execute([
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt,
            'body' => $body,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'status' => $status,
            'featured' => $featured,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $articleId = (int) $pdo->lastInsertId();
        foreach (($_POST['category_ids'] ?? []) as $catId) {
            $pdo->prepare('INSERT INTO article_category (article_id, category_id) VALUES (:aid, :cid)')->execute(['aid' => $articleId, 'cid' => (int) $catId]);
        }
        foreach (($_POST['tag_ids'] ?? []) as $tagId) {
            $pdo->prepare('INSERT INTO article_tag (article_id, tag_id) VALUES (:aid, :tid)')->execute(['aid' => $articleId, 'tid' => (int) $tagId]);
        }
    }

    if (($_POST['action'] ?? '') === 'delete' && !empty($_POST['delete_id'])) {
        $pdo->prepare('DELETE FROM articles WHERE id = :id')->execute(['id' => (int) $_POST['delete_id']]);
    }
}

$articles = $pdo->query('SELECT * FROM articles ORDER BY created_at DESC')->fetchAll();
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$tags = $pdo->query('SELECT * FROM tags ORDER BY name')->fetchAll();
?>
<h1>Articles</h1>
<form method="post" class="card form-grid">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">
    <input name="title" placeholder="Article Title" required>
    <textarea name="excerpt" placeholder="Short excerpt"></textarea>
    <textarea name="body" placeholder="Full article body" required></textarea>
    <input name="seo_title" placeholder="SEO title (optional)">
    <textarea name="seo_description" placeholder="SEO description (optional)"></textarea>
    <label><input type="checkbox" name="featured" value="1"> Mark as featured</label>
    <select name="status"><option value="draft">Draft</option><option value="published">Published</option></select>
    <label>Categories</label>
    <select multiple name="category_ids[]">
        <?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
    </select>
    <label>Tags</label>
    <select multiple name="tag_ids[]">
        <?php foreach ($tags as $t): ?><option value="<?= (int) $t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?>
    </select>
    <button>Create Article</button>
</form>

<table class="table"><tr><th>Title</th><th>Status</th><th>Featured</th><th>Action</th></tr>
<?php foreach ($articles as $a): ?>
<tr>
<td><?= e($a['title']) ?></td><td><?= e($a['status']) ?></td><td><?= $a['featured'] ? 'Yes' : 'No' ?></td>
<td><form method="post"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="delete_id" value="<?= (int) $a['id'] ?>"><button>Delete</button></form></td>
</tr>
<?php endforeach; ?></table>
<?php require __DIR__ . '/_bottom.php'; ?>
