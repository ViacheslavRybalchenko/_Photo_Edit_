document.addEventListener('DOMContentLoaded', () => {
    // Обробка лайків/дизлайків
    document.querySelectorAll('.like-section').forEach(section => {
        const imageId = section.dataset.imageId;
        const likeBtn = section.querySelector('.like-btn');
        const dislikeBtn = section.querySelector('.dislike-btn');
        const likeCountSpan = section.querySelector('.like-count');
        const dislikeCountSpan = section.querySelector('.dislike-count');

        let userVote = null;

        function updateButtonStyles() {
            likeBtn.classList.toggle('voted', userVote === 1);
            dislikeBtn.classList.toggle('voted', userVote === -1);
        }

        function vote(value) {
            fetch('/actions/like_handler.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({
                    image_id: imageId,
                    like_value: value
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    likeCountSpan.textContent = data.like_count;
                    dislikeCountSpan.textContent = data.dislike_count;
                    userVote = value;
                    updateButtonStyles();
                }
            })
            .catch(err => console.error('Fetch error:', err));
        }

        likeBtn.addEventListener('click', () => {
            if (userVote === 1) {
                userVote = null;
                likeCountSpan.textContent = parseInt(likeCountSpan.textContent) - 1;
            } else {
                vote(1);
            }
            updateButtonStyles();
        });

        dislikeBtn.addEventListener('click', () => {
            if (userVote === -1) {
                userVote = null;
                dislikeCountSpan.textContent = parseInt(dislikeCountSpan.textContent) - 1;
            } else {
                vote(-1);
            }
            updateButtonStyles();
        });
    });

    // Камера і скріншоти
    const video = document.getElementById('video');
    const noCamera = document.getElementById('no-camera');
    const screenshotBtn = document.getElementById('screenshot-btn');

    if (video && screenshotBtn) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
                screenshotBtn.disabled = false;
            })
            .catch(() => {
                if (noCamera) noCamera.style.display = 'block';
            });

        screenshotBtn.addEventListener('click', () => {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataURL = canvas.toDataURL('image/jpeg');

            fetch('/actions/save_screenshot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ image: dataURL }),
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Скріншот збережено!');
                    location.reload();
                } else {
                    alert('Помилка: ' + data.error);
                }
            })
            .catch(error => console.error('Fetch error:', error));
        });
    }
});
