// Hero Slider
document.addEventListener('DOMContentLoaded', function() {
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.slider-dot');
  const prevBtn = document.querySelector('.slider-control.prev');
  const nextBtn = document.querySelector('.slider-control.next');
  let currentSlide = 0;
  let slideInterval;

  // Function to show a specific slide
  function showSlide(index) {
    // Remove active class from all slides and dots
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    // Add active class to current slide and dot
    slides[index].classList.add('active');
    dots[index].classList.add('active');
    
    currentSlide = index;
  }

  // Function to show next slide
  function nextSlide() {
    let next = currentSlide + 1;
    if (next >= slides.length) {
      next = 0;
    }
    showSlide(next);
  }

  // Function to show previous slide
  function prevSlide() {
    let prev = currentSlide - 1;
    if (prev < 0) {
      prev = slides.length - 1;
    }
    showSlide(prev);
  }

  // Start automatic sliding
  function startSlideInterval() {
    slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
  }

  // Stop automatic sliding
  function stopSlideInterval() {
    clearInterval(slideInterval);
  }

  // Event listeners for controls
  prevBtn.addEventListener('click', () => {
    stopSlideInterval();
    prevSlide();
    startSlideInterval();
  });

  nextBtn.addEventListener('click', () => {
    stopSlideInterval();
    nextSlide();
    startSlideInterval();
  });

  // Event listeners for dots
  dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
      stopSlideInterval();
      showSlide(index);
      startSlideInterval();
    });
  });

  // Start the slider
  startSlideInterval();

  // Pause slider when hovering over controls
  const controls = document.querySelector('.hero-slider-controls');
  controls.addEventListener('mouseenter', stopSlideInterval);
  controls.addEventListener('mouseleave', startSlideInterval);
}); 