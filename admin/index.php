<?php require __DIR__ . '/_top.php';
$counts = [
    'articles' => db()->query('SELECT COUNT(*) FROM articles')->fetchColumn(),
    'categories' => db()->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
    'tags' => db()->query('SELECT COUNT(*) FROM tags')->fetchColumn(),
    'appointments' => db()->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
];
?>
<div class="grid">
    <?php foreach ($counts as $key => $value): ?>
    <div class="card"><h3><?= ucfirst($key) ?></h3><p><?= (int) $value ?></p></div>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/_bottom.php'; ?>
