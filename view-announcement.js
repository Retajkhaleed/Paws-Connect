document.addEventListener('DOMContentLoaded', function() {
    const commentsList = document.getElementById('commentsList');
    const urlParams = new URLSearchParams(window.location.search);
    const annIDFromURL = urlParams.get('id');
    const commentForm = document.getElementById('commentForm');

    if (commentForm && annIDFromURL) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const commentText = document.getElementById('newComment').value;
            
            fetch(window.location.href, { 
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `comment_text=${encodeURIComponent(commentText)}&announcement_id=${annIDFromURL}` 
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload(); 
                } else {
                    alert('Failed to post comment: ' + data.message);
                }
            })
            .catch(error => console.error('Error posting comment:', error));
        });
    }

    commentsList.addEventListener('click', function(e) {
        if (e.target.classList.contains('reply-btn')) {
            e.preventDefault(); 
            const thread = e.target.closest('.main-comment-thread');
            
            if (thread) {
                const form = thread.querySelector('.reply-form');
                
                document.querySelectorAll('.reply-form').forEach(f => {
                    if (f !== form) f.style.display = 'none';
                });
                
                if (form) {
                    form.style.display = form.style.display === 'none' ? 'block' : 'none';
                }
            }
        }
    });

    commentsList.addEventListener('submit', function(e) {
        if (e.target.classList.contains('reply-form')) {
            e.preventDefault(); 
            
            const form = e.target;
            const parentId = form.querySelector('input[name="parent_id"]').value;
            const replyText = form.querySelector('textarea[name="reply_text"]').value;
            
            if (!replyText.trim()) {
                alert('Reply cannot be empty.');
                return;
            }

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `reply_text=${encodeURIComponent(replyText)}&parent_id=${parentId}&announcement_id=${annIDFromURL}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload(); 
                } else {
                    alert('Failed to post reply: ' + data.message);
                }
            })
            .catch(error => console.error('Error posting reply:', error));
        }
    });


}); 
