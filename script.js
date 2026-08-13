document.addEventListener('DOMContentLoaded', function() {
    // ===== Плавный скролл для якорных ссылок =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ===== Кнопка "Наверх" (опционально) =====
    // Можно добавить позже

    // ===== Анимация появления карточек при скролле =====
    const cards = document.querySelectorAll('.portfolio__card, .price-card, .utp__item');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    // ===== Консольный привет =====
    console.log('🚀 MVA Labs — Multiply Your Value by AI');
    console.log('📧 hello@mvalabs.ru');
});