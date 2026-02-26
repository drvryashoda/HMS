<?php

function get_setting(string $key, ?string $default = null): ?string
{
    $stmt = db()->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
    $stmt->execute(['key' => $key]);
    $value = $stmt->fetchColumn();
    return $value !== false ? $value : $default;
}

function published_articles(int $limit = 6, bool $featured = false): array
{
    $sql = 'SELECT * FROM articles WHERE status = "published"';
    if ($featured) {
        $sql .= ' AND featured = 1';
    }
    $sql .= ' ORDER BY created_at DESC LIMIT :lim';
    $stmt = db()->prepare($sql);
    $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function article_with_terms(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM articles WHERE slug = :slug AND status = "published" LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $article = $stmt->fetch();
    if (!$article) {
        return null;
    }

    $catStmt = db()->prepare('SELECT c.* FROM categories c INNER JOIN article_category ac ON c.id = ac.category_id WHERE ac.article_id = :id');
    $catStmt->execute(['id' => $article['id']]);
    $tagStmt = db()->prepare('SELECT t.* FROM tags t INNER JOIN article_tag atg ON t.id = atg.tag_id WHERE atg.article_id = :id');
    $tagStmt->execute(['id' => $article['id']]);

    $article['categories'] = $catStmt->fetchAll();
    $article['tags'] = $tagStmt->fetchAll();
    return $article;
}
