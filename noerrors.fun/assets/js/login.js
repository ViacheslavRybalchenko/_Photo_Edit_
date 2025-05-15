// Функція для перемикання видимості пароля
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    if (!passwordField) return;

    // Перемикає тип поля між "password" та "text"
    passwordField.type = passwordField.type === "password" ? "text" : "password";
}
