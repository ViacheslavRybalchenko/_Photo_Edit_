<h2>Відновлення паролю</h2>

<form action="actions.php" method="post">
    <input type="hidden" name="action" value="forgot_password">
    
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" placeholder="Введіть ваш email" required>

    <div><button type="submit">Надіслати інструкції</button></div>
</form>

<div><a href="index.php?page=login">Повернутися до входу</a></div>

<?php
if (isset($_SESSION['message'])) {
    echo "<p style='color:red;'>" . $_SESSION['message'] . "</p>";
    unset($_SESSION['message']);
}
?>
