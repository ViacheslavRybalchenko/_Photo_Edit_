<?php
// Перевірка авторизації
session_start();
echo "<link rel='stylesheet' href='/assets/css/editor.css'>";
if (!isset($_SESSION['user_id'])) {
    echo "<p>Будь ласка, увійдіть у систему, щоб редагувати зображення.</p>";
    exit;
}

// Підключення до БД
require_once __DIR__ . '/../config/database.php';

// Отримання ID зображення з URL
$imagePath = null;
$imageId = null;
if (isset($_GET['id'])) {
    $imageId = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT image_path FROM images WHERE id = ?");
    $stmt->execute([$imageId]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($image && file_exists(__DIR__ . '/../' . ltrim($image['image_path'], '/'))) {
        $imagePath = $image['image_path'];
    }
}

if (!$imagePath) {
    echo "<p style='color:red; text-align:center;'>Зображення не знайдено або не вказано.</p>";
    $imagePath = '/uploads/images/default_photo_user.png';
}
?>

<div class="editor-container"
     data-image-path="<?= htmlspecialchars($imagePath) ?>"
     data-image-id="<?= $imageId !== null ? (int)$imageId : '' ?>">
    
    <div class="main-editor">
        <div class="image-wrapper">
            <canvas id="editor-canvas"></canvas>
        </div>
        <div class="editor-buttons">
            <button class="save-button" onclick="saveEditedImage()" disabled>💾 Зберегти накладання</button>
            <button class="reset-button" onclick="resetOverlays()">🔄 Відмінити накладання</button>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/aside.php'; ?>
</div>

<!-- Підключення JS -->
<script src="/assets/js/editor.js"></script>
