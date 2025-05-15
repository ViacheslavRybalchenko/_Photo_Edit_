const video = document.querySelector('video');
const canvas = document.querySelector('canvas');
const snapButton = document.querySelector('#snap');

// Доступ до камери
navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream)
    .catch(err => console.error("Камера недоступна!", err));

snapButton.addEventListener('click', () => {
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const photo = canvas.toDataURL('image/png');
    fetch('save_photo.php', {
        method: 'POST',
        body: JSON.stringify({ image: photo }),
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(err => console.error("Помилка збереження!", err));
});
