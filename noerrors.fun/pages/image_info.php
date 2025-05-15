<?php
require_once __DIR__ . '/../config/database.php';
session_start();
echo "<link rel='stylesheet' href='/assets/css/image_info.css'>";

$image_id = $_GET['id'] ?? null;

if (!$image_id || !is_numeric($image_id)) {
    echo "<p>Невірний запит</p>";
    return;
}

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare("
    SELECT images.*, users.username,
    (SELECT COUNT(*) FROM comments WHERE image_id = images.id) AS comment_count,
    (SELECT COUNT(*) FROM likes WHERE image_id = images.id AND like_value = 1) AS like_count,
    (SELECT COUNT(*) FROM likes WHERE image_id = images.id AND like_value = -1) AS dislike_count
    FROM images
    JOIN users ON images.user_id = users.id
    WHERE images.id = :id
");
$stmt->execute([':id' => $image_id]);
$image = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$image) {
    echo "<p>Зображення не знайдено</p>";
    return;
}
?>

<div class="image-detail-container">
    <!-- Блок 1: Зображення -->
    <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="">

    <!-- Блок 2: Інформація -->
    <div class="image-meta">
        <p>Користувач: <?= htmlspecialchars($image['username']) ?></p>
        <p>Дата: <?= htmlspecialchars($image['created_at']) ?></p>
        <p id="comment-count">Коментарі: <?= (int) $image['comment_count'] ?></p>
        <p>👍 <?= (int) $image['like_count'] ?> | 👎 <?= (int) $image['dislike_count'] ?></p>
    </div>

    <!-- Блок 3: Коментарі -->
    <div class="comments" id="comments-container"></div>

    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="comment-form">
        <textarea id="comment-text" placeholder="Напишіть коментар..."></textarea>
        <button id="submit-comment">Додати коментар</button>
    </div>
    <?php endif; ?>

    <!-- Блок 4: Повернення до галереї -->
    <a href="index.php?page=gallery" class="back-link">← Повернутися до галереї</a>
</div>

<script>
    const imageId = <?= (int)$image_id ?>;
</script>
<script src="/assets/js/image_info.js"></script>
