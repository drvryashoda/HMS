document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.hero-slide');
    let index = 0;

    if (slides.length) {
        slides[0].classList.add('active');
        setInterval(() => {
            slides[index].classList.remove('active');
            index = (index + 1) % slides.length;
            slides[index].classList.add('active');
        }, 6000);
    }

    const flash = document.querySelector('.alert[data-timeout]');
    if (flash) {
        setTimeout(() => flash.remove(), Number(flash.dataset.timeout));
    }
});
