let currentSlide = 0;
const slides = document.querySelectorAll('.slide');

function showSlide(index) {
    // Sembunyikan semua slaid
    slides.forEach(slide => slide.classList.remove('active'));
    
    // Logik looping (pergi ke awal jika dah habis, atau ke hujung jika patah balik)
    currentSlide = (index + slides.length) % slides.length;
    
    // Tunjukkan slaid yang dipilih
    slides[currentSlide].classList.add('active');
}

function changeSlide(direction) {
    showSlide(currentSlide + direction);
}