let currentSlide = 0;
const slides = document.querySelector('.slides');

function nextSlide() {
  currentSlide = (currentSlide + 1) % 4;
  updateSlider();
}

function updateSlider() {
  slides.style.transform = `translateX(-${currentSlide * 25}%)`;
}

setInterval(nextSlide, 5000);
