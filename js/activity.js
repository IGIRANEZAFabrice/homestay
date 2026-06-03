document.addEventListener('DOMContentLoaded', () => {
  const page = document.getElementById('activity-page');
  if (!page) return;

  const slider = document.getElementById('activitiesSlider');
  const btnLeft = document.getElementById('slideLeft');
  const btnRight = document.getElementById('slideRight');

  if (slider && btnLeft && btnRight) {
    const scrollAmount = 320; // Card width + gap

    btnLeft.addEventListener('click', () => {
      slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });

    btnRight.addEventListener('click', () => {
      slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });

    // Optional: Hide/Show arrows based on scroll position
    const toggleArrows = () => {
      btnLeft.style.opacity = slider.scrollLeft <= 0 ? '0.3' : '1';
      btnLeft.style.pointerEvents = slider.scrollLeft <= 0 ? 'none' : 'auto';
      
      const isEnd = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 10;
      btnRight.style.opacity = isEnd ? '0.3' : '1';
      btnRight.style.pointerEvents = isEnd ? 'none' : 'auto';
    };

    slider.addEventListener('scroll', toggleArrows);
    window.addEventListener('resize', toggleArrows);
    toggleArrows(); // Initial check
  }

  /* ── Gallery Logic ── */
  const galleryEl = document.getElementById("gallery");
  if (galleryEl) {
    const slides = galleryEl.querySelectorAll(".slide"); 
    const ring = document.getElementById("ring"); 
    const dotsEl = document.getElementById("dots"); 
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const pauseBtn = document.getElementById("pauseBtn"); 
    const pauseIcon = document.getElementById("pauseIcon"); 
    const pauseLabel = document.getElementById("pauseLabel"); 

    const DURATION = 9000; // 10 seconds per slide 
    const CIRCUMFERENCE = 95; // stroke-dasharray value (2π × 15.1) 

    let current = 0; 
    let paused = false; 
    let startTime = null; 
    let rafId = null; 
    let dots = []; 

    /* ── Build dot buttons ── */ 
    slides.forEach((_, i) => { 
      const d = document.createElement("button"); 
      d.className = "dot" + (i === 0 ? " active" : ""); 
      d.setAttribute("aria-label", "Go to slide " + (i + 1)); 
      d.addEventListener("click", () => goTo(i)); 
      dotsEl.appendChild(d); 
      dots.push(d); 
    }); 

    /* ── Click on any slide to open it ── */ 
    slides.forEach((s, i) => s.addEventListener("click", () => goTo(i))); 

    /* ── Prev / Next ── */ 
    if (prevBtn) prevBtn.addEventListener("click", () => goTo(current - 1)); 
    if (nextBtn) nextBtn.addEventListener("click", () => goTo(current + 1)); 

    /* ── Pause / Play toggle ── */ 
    if (pauseBtn) {
      pauseBtn.addEventListener("click", () => { 
        paused = !paused; 
        if (paused) { 
          cancelAnimationFrame(rafId); 
          pauseIcon.className = "fa-solid fa-play"; 
          pauseLabel.textContent = "Play"; 
        } else { 
          pauseIcon.className = "fa-solid fa-pause"; 
          pauseLabel.textContent = "Pause"; 
          startTime = null; // resume from where we left off 
          rafId = requestAnimationFrame(tick); 
        } 
      }); 
    }

    /* ── Switch to a specific slide ── */ 
    function goTo(idx) { 
      slides[current].classList.remove("active"); 
      dots[current].classList.remove("active"); 
      current = ((idx % slides.length) + slides.length) % slides.length; 
      slides[current].classList.add("active"); 
      dots[current].classList.add("active"); 
      resetTimer(); 
    } 

    /* ── Reset the countdown ring ── */ 
    function resetTimer() { 
      cancelAnimationFrame(rafId); 
      startTime = null; 
      if (ring) ring.style.strokeDashoffset = CIRCUMFERENCE; 
      if (!paused) rafId = requestAnimationFrame(tick); 
    } 

    /* ── Animation loop for the progress ring ── */ 
    function tick(ts) { 
      if (!startTime) startTime = ts; 
      const elapsed = ts - startTime; 
      const progress = Math.min(elapsed / DURATION, 1); 
      if (ring) ring.style.strokeDashoffset = CIRCUMFERENCE * (1 - progress); 

      if (progress < 1) { 
        rafId = requestAnimationFrame(tick); 
      } else { 
        goTo(current + 1); // auto-advance 
      } 
    } 

    /* ── Lazy Loading with Intersection Observer ── */
    const observerOptions = {
      root: null,
      rootMargin: '100px',
      threshold: 0.1
    };

    const galleryObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const galleryWrap = entry.target.querySelector('.gallery-wrap');
          const loader = entry.target.querySelector('#galleryLoader');
          const lazyImages = entry.target.querySelectorAll('.lazy-slide-img');

          // Load all images in the gallery
          lazyImages.forEach(img => {
            if (img.dataset.src) {
              img.src = img.dataset.src;
              img.removeAttribute('data-src');
            }
          });

          // Show gallery, hide loader
          if (galleryWrap) galleryWrap.style.display = 'block';
          if (loader) loader.style.display = 'none';

          // Start the timer once visible
          resetTimer();

          // Stop observing once loaded
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    galleryObserver.observe(document.getElementById('gallerySection'));
  }
});
