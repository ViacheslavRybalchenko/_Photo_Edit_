<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$image_id = $_GET['image_id'] ?? null;

if (!$image_id || !is_numeric($image_id)) {
    echo json_encode(['error' => 'Invalid image_id']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM comments WHERE image_id = ?");
    $stmt->execute([$image_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['count' => (int)$row['count']]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error']);
}
