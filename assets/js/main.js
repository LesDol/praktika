/**
 * Main JavaScript file for the College Videos Platform
 */

document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.classList.add('fade-in');
    });
    
    // Handle video tag input
    const tagInputs = document.querySelectorAll('input[name="tags"]');
    tagInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Convert to lowercase, remove extra spaces
            this.value = this.value.toLowerCase().replace(/\s*,\s*/g, ',');
        });
    });
    
    // Confirm video deletion
    const deleteButtons = document.querySelectorAll('form[onsubmit*="confirm"]');
    deleteButtons.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm(form.getAttribute('onsubmit').replace('return confirm(\'', '').replace('\')', ''))) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Active navigation links
    const currentPath = window.location.href;
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(link => {
        if (currentPath.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
    
    // Handle YouTube URL conversion for embedded videos
    const videoPlayer = document.querySelector('video source');
    if (videoPlayer) {
        const videoUrl = videoPlayer.getAttribute('src');
        
        // If YouTube URL, convert to embedded format
        if (videoUrl.includes('youtube.com/watch?v=') || videoUrl.includes('youtu.be/')) {
            let videoId = '';
            
            if (videoUrl.includes('youtube.com/watch?v=')) {
                videoId = videoUrl.split('v=')[1].split('&')[0];
            } else if (videoUrl.includes('youtu.be/')) {
                videoId = videoUrl.split('youtu.be/')[1];
            }
            
            if (videoId) {
                // Replace video player with YouTube iframe
                const videoContainer = videoPlayer.parentElement.parentElement;
                videoContainer.innerHTML = `
                    <iframe 
                        width="100%" 
                        height="100%" 
                        src="https://www.youtube.com/embed/${videoId}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                `;
            }
        }
    }
}); 