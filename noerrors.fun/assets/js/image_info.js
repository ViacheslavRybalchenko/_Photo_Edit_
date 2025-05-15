document.addEventListener("DOMContentLoaded", () => {
    // Завантажуємо всі коментарі
    function loadComments() {
        fetch(`/actions/get_comments.php?image_id=${imageId}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('comments-container');
                container.innerHTML = '';
                data.forEach(comment => {
                    const div = document.createElement('div');
                    div.className = 'comment';

                    // Форматуємо дату коментаря
                    const createdAt = new Date(comment.created_at);
                    const formattedDate = createdAt.toLocaleString('uk-UA', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });

                    div.innerHTML = `<strong>${comment.username} (${formattedDate}):</strong> ${comment.comment}`;
                    container.appendChild(div);
                });
            });
    }

    // Оновлюємо лічильник коментарів
    function updateCommentCount(imageId) {
        fetch(`/actions/get_comment_count.php?image_id=${imageId}`)
            .then(res => res.json())
            .then(data => {
                if (data.count !== undefined) {
                    const commentCountText = document.getElementById('comment-count');
                    commentCountText.textContent = `Коментарі: ${data.count}`;
                }
            })
            .catch(err => console.error('Помилка при оновленні лічильника коментарів:', err));
    }

    // Ініціалізація
    loadComments();

    // Обробка надсилання коментаря
    const submitBtn = document.getElementById('submit-comment');
    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            const textEl = document.getElementById('comment-text');
            const text = textEl.value.trim();
            if (!text) return;

            fetch('/actions/add_comment.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({image_id: imageId, comment: text})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('comments-container');
                    const div = document.createElement('div');
                    div.className = 'comment';
                    const datetime = new Date(data.created_at);
                    const formatted = `${datetime.toLocaleDateString()} ${datetime.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}`;
                    div.innerHTML = `<strong>${data.username}</strong> (${formatted}): ${text}`;
                    container.appendChild(div);
                    textEl.value = '';

                    updateCommentCount(imageId);
                } else {
                    alert('Помилка: ' + data.error);
                }
            });
        });
    }
});
