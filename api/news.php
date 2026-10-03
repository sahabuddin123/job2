<?php
// api/news.php - JSON API endpoint returning news titles and categories
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("
        SELECT n.title, c.category_name AS category
        FROM news n
        INNER JOIN category c ON n.category_id = c.category_id
        ORDER BY n.news_id ASC
    ");
    $news = $stmt->fetchAll();
    echo json_encode($news, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to fetch news data']);
}
?>
