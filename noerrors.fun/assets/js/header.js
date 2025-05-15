// Перемикає клас .active для меню при кліку на бургер
function toggleMenu() {
    const menu = document.getElementById('menu');
    menu.classList.toggle('active');
}

// Закриває меню при кліку поза ним
document.addEventListener('click', function(event) {
    const menu = document.getElementById('menu');
    const burger = document.querySelector('.burger');

    if (!menu.contains(event.target) && !burger.contains(event.target)) {
        menu.classList.remove('active');
    }
});

// Закриває меню при натисканні клавіші Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const menu = document.getElementById('menu');
        menu.classList.remove('active');
    }
});
