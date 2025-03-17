document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtns = document.querySelectorAll('.chapter-dropdown-btn');
    const dropdowns = document.querySelectorAll('.chapter-dropdown');
    const chapterSearch = document.getElementById('chapterSearch');
    const chapterLinks = document.querySelectorAll('.chapter-list a');

    dropdownBtns.forEach(function(dropdownBtn, index) {
        const dropdown = dropdowns[index]; 


        dropdownBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            dropdown.classList.toggle('active');
        });
    });

    document.addEventListener('click', function(event) {
        dropdowns.forEach(function(dropdown) {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    });

    chapterSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        chapterLinks.forEach(function(link) {
            const chapterText = link.textContent.toLowerCase();
            if (chapterText.includes(searchTerm)) {
                link.style.display = '';
            } else {
                link.style.display = 'none';
            }
        });
    });

    dropdownBtns.forEach(function(dropdownBtn) {
        dropdownBtn.addEventListener('click', function() {
            const activeChapter = document.querySelector('.chapter-list a.active');
            if (activeChapter) {
                setTimeout(() => {
                    activeChapter.scrollIntoView({ block: 'center' });
                }, 0);
            }
        });
    });
});
