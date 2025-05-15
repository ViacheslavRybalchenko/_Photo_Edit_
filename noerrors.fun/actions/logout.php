<?php
session_start();
session_unset(); // Видаляє всі змінні сесії
session_destroy(); // Завершує сесію

// Перенаправлення на головну сторінку після виходу
header("Location: /index.php");
exit;
?>
