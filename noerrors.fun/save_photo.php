<?php
session_start();
require_once 'http://localhost/photo_editing_app/config/database.php';

$data = json_decode(file_get_contents("php://input"));
$image = $data->image;
$image = str_replace('data:image/png;base64,', '', $image);
$image = str_replace(' ', '+', $image);
$file = 'uploads/' . uniqid() . '.png';

if (file_put_contents($file, base64_decode($image))) {
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $stmt = $pdo->prepare("INSERT INTO photos (user_id, image_path) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $file]);

    echo json_encode(["message" => "Фото збережено!", "path" => $file]);
} else {
    echo json_encode(["message" => "Помилка збереження"]);
}
?>