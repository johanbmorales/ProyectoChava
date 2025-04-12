document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebarNav = document.getElementById('sidebarNav');
    const closeMenu = document.getElementById('closeMenu');
    const overlay = document.getElementById('overlay');
    const mainContent = document.querySelector('.main-content');

    menuToggle.addEventListener('click', function() {
        sidebarNav.classList.add('open');
        overlay.classList.add('open');
        if (mainContent) mainContent.classList.add('shifted');
    });

    closeMenu.addEventListener('click', function() {
        sidebarNav.classList.remove('open');
        overlay.classList.remove('open');
        if (mainContent) mainContent.classList.remove('shifted');
    });

    overlay.addEventListener('click', function() {
        sidebarNav.classList.remove('open');
        overlay.classList.remove('open');
        if (mainContent) mainContent.classList.remove('shifted');
    });

    // Cerrar menú al hacer clic en un enlace (opcional)
    const navLinks = document.querySelectorAll('.nav-list li a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                sidebarNav.classList.remove('open');
                overlay.classList.remove('open');
                if (mainContent) mainContent.classList.remove('shifted');
            }
        });
    });
});