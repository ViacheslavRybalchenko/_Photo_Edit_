<aside class="overlay-list">
<?php
$overlayDir = __DIR__ . '/../uploads/overlay_images';
$overlayUrl = '/uploads/overlay_images';

if (is_dir($overlayDir)) {
    $files = scandir($overlayDir);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo '<img src="' . $overlayUrl . '/' . htmlspecialchars($file) . '" alt="Overlay" onclick="addOverlay(this.src)">';
        }
    }
} else {
    echo '<p>Немає зображень для накладання.</p>';
}
?>
</aside>
