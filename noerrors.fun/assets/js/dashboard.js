// Ініціалізація відображення назви вибраного файлу
function initFileNameDisplay(fileInputId, fileNameDivId, defaultText = 'Файл не вибрано') {
    const fileInput = document.getElementById(fileInputId);
    const fileNameDiv = document.getElementById(fileNameDivId);
    if (!fileInput || !fileNameDiv) return;
    fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) {
            const name = fileInput.files[0].name;
            fileNameDiv.textContent = name;
            fileNameDiv.title = name;
        } else {
            fileNameDiv.textContent = defaultText;
            fileNameDiv.title = '';
        }
    });
}

// Ініціалізація прев'ю фото при виборі з комп'ютера
function initUserPhotoPreview(fileInputId, previewImgId, fileNameDivId, defaultPhotoPath) {
    const fileInput = document.getElementById(fileInputId);
    const previewImg = document.getElementById(previewImgId);
    const fileNameDiv = document.getElementById(fileNameDivId);
    if (!fileInput || !previewImg || !fileNameDiv) return;
    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
            fileNameDiv.textContent = file.name;
            fileNameDiv.title = file.name;
        } else {
            previewImg.src = defaultPhotoPath;
            fileNameDiv.textContent = 'Файл не вибрано';
            fileNameDiv.title = '';
        }
    });
}

// Ініціалізація обнулення фото при натисканні кнопки "Скинути"
function initResetUserPhoto(formId, fileInputId, previewImgId, fileNameDivId, defaultPhotoPath = '/uploads/images/default_photo_user.png') {
    const form = document.getElementById(formId);
    if (!form) return;
    form.addEventListener('reset', function () {
        const fileInput = document.getElementById(fileInputId);
        const previewImg = document.getElementById(previewImgId);
        const fileNameDiv = document.getElementById(fileNameDivId);
        if (fileInput) fileInput.value = "";
        if (previewImg) {
            const defaultSrc = previewImg.getAttribute('data-default');
            previewImg.src = defaultSrc || defaultPhotoPath;
        }
        if (fileNameDiv) {
            fileNameDiv.textContent = 'Файл не вибрано';
            fileNameDiv.title = '';
        }
    });
}

// Ініціалізація взаємодії з панеллю серверних зображень
function initServerImagePanel() {
    const toggleButton = document.getElementById('toggle-image-panel');
    const imagePanel = document.getElementById('server-image-panel');
    if (toggleButton && imagePanel) {
        toggleButton.addEventListener('click', function () {
            imagePanel.classList.toggle('open');
        });
    }

    const serverImages = document.querySelectorAll('.selectable-server-image');
    const previewImg = document.getElementById('user-photo-preview');
    const photoPathInput = document.getElementById('photo_user_path');
    const selectedPhotoInput = document.getElementById('selected_server_photo');
    const fileInput = document.getElementById('photo-user-input');
    const fileNameDiv = document.getElementById('selected-file-name');

    serverImages.forEach(img => {
        img.addEventListener('click', function () {
            serverImages.forEach(i => i.classList.remove('selected'));
            this.classList.add('selected');
            const path = this.getAttribute('data-path');
            if (previewImg) previewImg.src = path;
            if (photoPathInput) photoPathInput.value = path;
            if (selectedPhotoInput) selectedPhotoInput.value = path;
            if (fileInput) fileInput.value = "";
            if (fileNameDiv) {
                fileNameDiv.textContent = path;
                fileNameDiv.title = path;
            }
        });
    });
}

// Виклик ініціалізаторів після завантаження DOM
document.addEventListener('DOMContentLoaded', function () {
    initFileNameDisplay('photo-user-input', 'selected-file-name');
    initUserPhotoPreview(
        'photo-user-input',
        'user-photo-preview',
        'selected-file-name',
        '/uploads/images/default_photo_user.png'
    );
    initResetUserPhoto('dashboard-form', 'photo-user-input', 'user-photo-preview', 'selected-file-name');
    initServerImagePanel();
});
