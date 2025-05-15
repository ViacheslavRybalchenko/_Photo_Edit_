let baseImage;
let hasChanges = false;

function initEditor() {
    const canvas = document.getElementById('editor-canvas');
    const ctx = canvas.getContext('2d');
    
    const container = document.querySelector('.editor-container');
    const imagePath = container.dataset.imagePath;
    const imageId = container.dataset.imageId || null;

    // Завантаження базового зображення
    baseImage = new Image();
    baseImage.src = imagePath;
    baseImage.onload = function () {
        resizeCanvasToImage(baseImage);
        drawBaseImage();
    };

    // Зберігаємо ID і шлях у глобальні змінні для інших функцій
    window.editorImagePath = imagePath;
    window.editorImageId = imageId;

    updateSaveButtonState();
}

function resizeCanvasToImage(image) {
    const canvas = document.getElementById('editor-canvas');
    const maxWidth = 640;
    const maxHeight = 480;

    const aspectRatio = image.width / image.height;
    let newWidth = image.width;
    let newHeight = image.height;

    if (newWidth > maxWidth) {
        newWidth = maxWidth;
        newHeight = newWidth / aspectRatio;
    }
    if (newHeight > maxHeight) {
        newHeight = maxHeight;
        newWidth = newHeight * aspectRatio;
    }

    canvas.width = Math.round(newWidth);
    canvas.height = Math.round(newHeight);
}

function drawBaseImage() {
    const canvas = document.getElementById('editor-canvas');
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(baseImage, 0, 0, canvas.width, canvas.height);
}

function addOverlay(src) {
    const canvas = document.getElementById('editor-canvas');
    const ctx = canvas.getContext('2d');
    const overlay = new Image();
    overlay.src = src;
    overlay.onload = function () {
        const width = canvas.width * 0.2;
        const height = canvas.height * 0.2;
        ctx.drawImage(overlay, canvas.width - width - 10, canvas.height - height - 10, width, height);
        hasChanges = true;
        updateSaveButtonState();
    };
}

function saveEditedImage() {
    if (!hasChanges) {
        alert('Немає змін для збереження.');
        return;
    }

    const canvas = document.getElementById('editor-canvas');
    const dataURL = canvas.toDataURL('image/jpeg');

    fetch('/actions/save_edited_image.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            image: dataURL,
            original_path: window.editorImagePath,
            image_id: window.editorImageId
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Зображення збережено успішно!');
                hasChanges = false;
                updateSaveButtonState();
            } else {
                alert('Помилка збереження: ' + data.error);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Щось пішло не так...');
        });
}

function resetOverlays() {
    drawBaseImage();
    hasChanges = false;
    updateSaveButtonState();
}

function updateSaveButtonState() {
    const saveButton = document.querySelector('.editor-buttons button[onclick="saveEditedImage()"]');
    if (saveButton) {
        saveButton.disabled = !hasChanges;
    }
}

document.addEventListener('DOMContentLoaded', initEditor);
