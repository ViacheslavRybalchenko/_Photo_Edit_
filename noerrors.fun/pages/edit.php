<?php
require_once 'http://localhost/photo_editing_app/includes/header.php';
require_once 'http://localhost/photo_editing_app/config/database.php';
require_once 'http://localhost/photo_editing_app/includes/functions.php';

if (!isset($_SESSION['user'])) {
    header("Location: http://localhost/photo_editing_app/pages/login.php");
    exit();
}

echo "<h2>Редагування фото</h2>";
?>

<div class="editor-container">
    <video id="camera" autoplay></video>
    <canvas id="canvas"></canvas>
    <input type="file" id="upload" accept="image/*">
    <button id="capture">Зробити фото</button>
    <button id="save">Зберегти фото</button>
</div>

<script src="http://localhost/photo_editing_app/js/script.js"></script>

<?php
require_once 'http://localhost/photo_editing_app/includes/footer.php';
?>
