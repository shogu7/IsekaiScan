document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('chapterSearch');
    const chapterGrid = document.getElementById('chapterGrid');
    const chapterCards = chapterGrid.querySelectorAll('.chapter-card');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        chapterCards.forEach(card => {
            const chapterNumber = card.querySelector('.chapter-number').textContent.toLowerCase();
            
            if (searchTerm === '' || chapterNumber.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    const favoriteBtn = document.querySelector('.favorite-btn');
    
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            const mangaId = this.dataset.mangaId;
            
            fetch('add_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `manga_id=${mangaId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('active');
                    
                    const icon = this.querySelector('i');
                    if (data.isFavorite) {
                        icon.classList.remove('fa-bookmark-o');
                        icon.classList.add('fa-bookmark');
                        this.innerHTML = '<i class="fas fa-bookmark"></i> Retirer des favoris';
                    } else {
                        icon.classList.remove('fa-bookmark');
                        icon.classList.add('fa-bookmark-o');
                        this.innerHTML = '<i class="far fa-bookmark"></i> Ajouter aux favoris';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
    
    if ('IntersectionObserver' in window) {
        const lazyLoadObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    card.classList.add('visible');
                    observer.unobserve(card);
                }
            });
        });
        
        chapterCards.forEach(card => {
            lazyLoadObserver.observe(card);
        });
    } else {
        chapterCards.forEach(card => {
            card.classList.add('visible');
        });
    }
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const fadeElements = document.querySelectorAll('.manga-info-container, .chapters-section');
    
    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                fadeObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    fadeElements.forEach(element => {
        fadeObserver.observe(element);
    });
});