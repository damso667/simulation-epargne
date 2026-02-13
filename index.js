// Menu hamburger toggle
const btn = document.querySelector('.btn1');
const sectionHamburger = document.querySelector('.section-hamburger');

btn.addEventListener('click', function() {
    btn.classList.toggle('active');
    sectionHamburger.classList.toggle('active');
});

// Fermer le menu en cliquant sur un lien
const menuLinks = document.querySelectorAll('.section-hamburger a');
menuLinks.forEach(link => {
    link.addEventListener('click', function() {
        btn.classList.remove('active');
        sectionHamburger.classList.remove('active');
    });
});

// Smooth scroll pour les liens d'ancrage
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Animation au scroll pour les cartes tarifs
const observerOptions = {
    threshold: 0.2,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '0';
            entry.target.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                entry.target.style.transition = 'all 0.6s ease-out';
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, 100);
            
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('.cartes-tarifs').forEach(card => {
    observer.observe(card);
});

// Parallax effect pour la section parallax
window.addEventListener('scroll', function() {
    const parallax = document.querySelector('.parallax');
    const scrolled = window.pageYOffset;
    const parallaxOffset = parallax.offsetTop;
    const speed = 0.5;
    
    if (scrolled > parallaxOffset - window.innerHeight && scrolled < parallaxOffset + parallax.offsetHeight) {
        parallax.style.backgroundPositionY = (scrolled - parallaxOffset) * speed + 'px';
    }
});