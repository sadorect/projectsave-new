document.addEventListener('DOMContentLoaded', function() {
    const completeButton = document.getElementById('mark-complete');
    
    if (completeButton) {
        completeButton.addEventListener('click', function() {
            const lessonId = this.dataset.lessonId;
            
            fetch(`/learn/lessons/${lessonId}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update progress bar
                document.querySelector('.progress-bar').style.width = `${data.progress}%`;
                document.querySelector('.progress-text').textContent = `${data.progress}% Complete`;
                
                // Update button state
                completeButton.disabled = true;
                completeButton.textContent = 'Completed';
            });
        });
    }
});
