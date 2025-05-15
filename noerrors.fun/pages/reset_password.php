<h2>Скидання паролю</h2>

<form action="actions.php" method="post">
    <input type="hidden" name="action" value="reset_password">
    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

    <label for="password">Новий пароль:</label>
    <div class="password-container">
        <input type="password" name="password" id="password" placeholder="Введіть новий пароль" required>
        <span class="toggle-password" onclick="togglePassword('password')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5C7 5 3.1 8.3 1.5 12C3.1 15.7 7 19 12 19C17 19 20.9 15.7 22.5 12C20.9 8.3 17 5 12 5Z" 
                        stroke="#007BFF" stroke-width="2" fill="none"/>
                    <circle cx="12" cy="12" r="3" stroke="#007BFF" stroke-width="2" fill="none"/>
                </svg>
        </span>
    </div>

    <label for="confirm_password">Повторіть пароль:</label>
    <div class="password-container">
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Повторіть пароль" required>
        <span class="toggle-password" onclick="togglePassword('confirm_password')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5C7 5 3.1 8.3 1.5 12C3.1 15.7 7 19 12 19C17 19 20.9 15.7 22.5 12C20.9 8.3 17 5 12 5Z" 
                        stroke="#007BFF" stroke-width="2" fill="none"/>
                    <circle cx="12" cy="12" r="3" stroke="#007BFF" stroke-width="2" fill="none"/>
                </svg>
        </span>
    </div>

    <div><button type="submit">Зберегти новий пароль</button></div>
</form>

<div><a href="index.php?page=login">Повернутися до входу</a></div>

<?php
if (isset($_SESSION['message'])) {
    echo "<p style='color:red;'>" . $_SESSION['message'] . "</p>";
    unset($_SESSION['message']);
}
?>

<script src="/assets/js/login.js"></script>
