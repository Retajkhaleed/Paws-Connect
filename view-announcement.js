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

    // Select all textarea elements on the page (main comments and replies)
    document.querySelectorAll('textarea').forEach(textarea => {
        
        // Set the maximum allowed characters at the browser level to prevent typing beyond 500
        textarea.setAttribute('maxlength', '500'); 

        // Create a new div element to act as the visual character counter
        const counter = document.createElement('div');
        
        // Apply inline styling to position the counter and make it look professional
        counter.style.cssText = 'font-size: 12px; color: #888; text-align: right; margin-top: 5px;';
        
        // Set the initial display text for the counter
        counter.innerHTML = `0 / 500`;
        
        // Insert the counter element immediately after the current textarea in the DOM
        textarea.parentNode.insertBefore(counter, textarea.nextSibling);

        // Add an event listener that triggers every time the user types or pastes text
        textarea.addEventListener('input', function() {
            
            // Get the current number of characters in the textarea
            const length = this.value.length;
            
            // Update the counter text to show the current character count
            counter.innerHTML = `${length} / 500`;
            
            // Logical check: If the limit is reached (500 characters)
            if (length >= 500) {
                counter.style.color = '#f1642e'; // Change color to alert orange-red
                counter.innerHTML = `Limit reached! 500 / 500`;
            } 
            // Warning check: If the user is close to the limit (over 450 characters)
            else if (length > 450) {
                counter.style.color = '#f1642e'; // Change color to alert orange-red
            } 
            // Normal state: If the text is within a safe length
            else {
                counter.style.color = '#888'; // Keep the color neutral gray
            }
        });
    });
}); 
