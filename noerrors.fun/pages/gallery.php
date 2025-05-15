<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : null;

// Pagination
$page = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Total count for pagination
$total_stmt = $pdo->query("SELECT COUNT(*) FROM images");
$total_images = $total_stmt->fetchColumn();
$total_pages = ceil($total_images / $limit);

// Get images
$stmt = $pdo->prepare("
    SELECT 
        images.id,
        images.image_path,
        images.user_id,
        images.created_at,
        users.username,
        (SELECT COUNT(*) FROM comments WHERE comments.image_id = images.id) AS comment_count,
        (SELECT COUNT(*) FROM likes WHERE likes.image_id = images.id AND likes.like_value = 1) AS like_count,
        (SELECT COUNT(*) FROM likes WHERE likes.image_id = images.id AND likes.like_value = -1) AS dislike_count
    FROM images
    JOIN users ON images.user_id = users.id
    ORDER BY images.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Галерея</title>
    <link rel="stylesheet" href="/assets/css/gallery.css">
</head>
<body>
<div class="gallery-container">
    <?php if ($logged_in): ?>
        <div class="camera-block">
            <video id="video" autoplay></video>
            <div class="no-camera" id="no-camera" style="display: none;">Веб-камера відсутня</div>
            <button id="screenshot-btn" disabled>Зберегти скріншот</button>
        </div>
    <?php endif; ?>

    <div class="gallery-grid">
        <?php foreach ($images as $img): ?>
            <div class="image-card">
                <a href="index.php?page=image_info&id=<?= $img['id'] ?>">
                    <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="Зображення">
                </a>
                <div class="image-info">
                    <div>Користувач: <?= htmlspecialchars($img['username']) ?></div>
                    <div>Дата: <?= htmlspecialchars($img['created_at']) ?></div>
                    <div>Коментарі: <?= (int) $img['comment_count'] ?></div>
                    <div class="like-section" data-image-id="<?= $img['id'] ?>">
                        <button class="like-btn" <?= $logged_in ? '' : 'disabled' ?>>👍 <span class="like-count"><?= (int) $img['like_count'] ?></span></button>
                        |
                        <button class="dislike-btn" <?= $logged_in ? '' : 'disabled' ?>>👎 <span class="dislike-count"><?= (int) $img['dislike_count'] ?></span></button>
                    </div>
                    <?php if ($logged_in && $img['user_id'] == $user_id): ?>
                        <a href="index.php?page=editor&id=<?= $img['id'] ?>" class="edit-btn">Редагувати</a>
                    <?php else: ?>
                        <button class="edit-btn" disabled>Редагувати</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?page=gallery&page_num=<?= $i ?>" <?= $i === $page ? 'style="font-weight:bold;background:#0056b3"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Глобальні змінні -->
<script>
    const GALLERY_LOGGED_IN = <?= json_encode($logged_in) ?>;
    const GALLERY_USER_ID = <?= json_encode($user_id) ?>;
</script>
<script src="/assets/js/gallery.js"></script>
</body>
</html>
