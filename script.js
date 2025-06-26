document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });
    }
    
    // File Upload Preview
    const fileInputs = document.querySelectorAll('.file-upload input[type="file"]');
    fileInputs.forEach(input => {
        const container = input.closest('.file-upload');
        const fileName = container.querySelector('.file-name');
        
        input.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            }
        });
    });
    
    // Car Image Gallery
    const mainImage = document.querySelector('.main-image img');
    const thumbnails = document.querySelectorAll('.thumbnail img');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                mainImage.src = this.src;
            });
        });
    }
    
    // Modal Handling
    const modal = document.querySelector('.modal');
    const modalClose = document.querySelector('.modal-close');
    const modalTriggers = document.querySelectorAll('[data-modal-target]');
    
    if (modal && modalClose) {
        modalClose.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
    
    if (modalTriggers.length > 0) {
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', function() {
                const target = this.getAttribute('data-modal-target');
                const modal = document.querySelector(target);
                if (modal) {
                    modal.style.display = 'flex';
                }
            });
        });
    }
    
    // Toggle Favorite
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const carId = this.getAttribute('data-car-id');
            const isFavorite = this.classList.contains('active');
            
            fetch('toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `car_id=${carId}&action=${isFavorite ? 'remove' : 'add'}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('active');
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.className = isFavorite ? 'far fa-heart' : 'fas fa-heart';
                    }
                }
            });
        });
    });
    
    // Mark as Sold
    const soldButtons = document.querySelectorAll('.mark-sold-btn');
    soldButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to mark this car as sold?')) {
                e.preventDefault();
            }
        });
    });
});
