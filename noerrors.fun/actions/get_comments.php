<?php
require_once __DIR__ . '/../config/database.php';

$image_id = $_GET['image_id'] ?? null;
if (!$image_id || !is_numeric($image_id)) {
    echo json_encode([]);
    exit;
}

$pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Додаємо поле created_at до вибірки
$stmt = $pdo->prepare("
    SELECT c.comment, c.created_at, u.username
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.image_id = :id
    ORDER BY c.created_at ASC
");

$stmt->execute([':id' => $image_id]);

// Повертаємо масив у JSON-форматі
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);

