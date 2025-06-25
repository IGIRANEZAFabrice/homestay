// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    document.querySelector(this.getAttribute("href")).scrollIntoView({
      behavior: "smooth",
    });
  });
});

// Intersection Observer for animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = "1";
      entry.target.style.transform = "translateY(0)";
    }
  });
}, observerOptions);

// Observe elements for animation
document
  .querySelectorAll(".stat-card, .room-card, .attraction-card")
  .forEach((el) => {
    el.style.opacity = "0";
    el.style.transform = "translateY(20px)";
    el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    observer.observe(el);
  });

// Add scroll effect to hero section
window.addEventListener("scroll", () => {
  const scrolled = window.pageYOffset;
  const hero = document.querySelector(".hero-section");
  if (hero) {
    hero.style.transform = `translateY(${scrolled * 0.5}px)`;
  }
});

// Animate stats numbers
function animateStats() {
  const statNumbers = document.querySelectorAll(".stat-number");
  statNumbers.forEach((stat) => {
    const target = stat.textContent;
    const numericTarget = parseInt(target.replace(/\D/g, ""));
    const suffix = target.replace(/\d/g, "");
    let count = 0;
    const increment = numericTarget / 100;

    const timer = setInterval(() => {
      count += increment;
      if (count >= numericTarget) {
        count = numericTarget;
        clearInterval(timer);
      }
      stat.textContent = Math.floor(count) + suffix;
    }, 20);
  });
}

// Trigger stats animation when section is visible
const statsSection = document.querySelector(".stats-section");
const statsObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        animateStats();
        statsObserver.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.5 }
);

if (statsSection) {
  statsObserver.observe(statsSection);
}

// Add loading animation
window.addEventListener("load", () => {
  document.body.style.opacity = "1";
});

// Mobile menu toggle (if needed for future header)
let lastScrollTop = 0;
window.addEventListener("scroll", () => {
  const currentScroll =
    window.pageYOffset || document.documentElement.scrollTop;

  // Add shadow to floating WhatsApp on scroll
  const floatingWhatsApp = document.querySelector(".floating-whatsapp");
  if (currentScroll > 100) {
    floatingWhatsApp.style.boxShadow = "0 15px 40px rgba(37, 211, 102, 0.4)";
  } else {
    floatingWhatsApp.style.boxShadow = "0 10px 30px rgba(37, 211, 102, 0.3)";
  }

  lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
});

// Add click analytics tracking (placeholder)
document
  .querySelectorAll(".cta-button, .btn-primary, .floating-whatsapp")
  .forEach((button) => {
    button.addEventListener("click", () => {
      // Analytics tracking can be added here
      console.log("WhatsApp booking button clicked");
    });
  });

// Preload critical images
const imageUrls = [
  'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><rect fill="%23228B22" width="1200" height="800"/></svg>',
];

imageUrls.forEach((url) => {
  const img = new Image();
  img.src = url;
});

const loadMoreBtn = document.getElementById("loadMoreBtn");
const experiencesGrid = document.getElementById("experiencesGrid");
let isLoading = false;

if (loadMoreBtn) {
  loadMoreBtn.addEventListener("click", () => {
    if (isLoading) return;

    isLoading = true;
    loadMoreBtn.classList.add("loading");

    // Simulate loading delay
    setTimeout(() => {
      const hiddenCards = experiencesGrid.querySelectorAll(
        ".experience-card.hidden"
      );

      if (hiddenCards.length > 0) {
        // Show hidden cards with staggered animation
        hiddenCards.forEach((card, index) => {
          setTimeout(() => {
            card.classList.remove("hidden");
            card.style.animationDelay = `${index * 0.1}s`;
          }, index * 100);
        });

        // Hide the load more button after showing all cards
        setTimeout(() => {
          loadMoreBtn.style.opacity = "0";
          loadMoreBtn.style.transform = "translateY(20px)";
          setTimeout(() => {
            loadMoreBtn.style.display = "none";
          }, 300);
        }, hiddenCards.length * 100 + 500);
      }

      loadMoreBtn.classList.remove("loading");
      isLoading = false;
    }, 1500);
  });
}

// Testimonial/Review Modal Logic
document.addEventListener("DOMContentLoaded", function () {
  const reviewModal = document.getElementById("reviewModal");
  const openReviewModalBtn = document.getElementById("openReviewModalBtn");
  const closeReviewModalBtn = document.getElementById("closeReviewModalBtn");
  const reviewForm = document.getElementById("reviewForm");
  const reviewsScroller = document.getElementById("reviewsScroller");

  // Function to open modal
  if (openReviewModalBtn) {
    openReviewModalBtn.addEventListener("click", function () {
      console.log("Review modal button clicked"); // Debug log
      reviewModal.style.display = "block";
      reviewModal.classList.add("show");
      document.body.style.overflow = "hidden"; // Prevent background scrolling
    });
  }

  // Function to close modal
  if (closeReviewModalBtn) {
    closeReviewModalBtn.addEventListener("click", function () {
      reviewModal.style.display = "none";
      reviewModal.classList.remove("show");
      document.body.style.overflow = "auto"; // Restore background scrolling
    });
  }

  // Close modal if user clicks outside of it
  if (reviewModal) {
    reviewModal.addEventListener("click", function (event) {
      if (event.target === reviewModal) {
        reviewModal.style.display = "none";
        reviewModal.classList.remove("show");
        document.body.style.overflow = "auto";
      }
    });
  }

  // Close modal with Escape key
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && reviewModal.style.display === "block") {
      reviewModal.style.display = "none";
      reviewModal.classList.remove("show");
      document.body.style.overflow = "auto";
    }
  });

  // Star rating functionality
  const starLabels = document.querySelectorAll("#reviewRatingInput .star-label");
  const reviewerRatingInput = document.getElementById("reviewerRating");

  starLabels.forEach((star, index) => {
    star.addEventListener("click", function () {
      const rating = index + 1;
      reviewerRatingInput.value = rating;
      
      // Update star display
      starLabels.forEach((s, i) => {
        if (i < rating) {
          s.classList.add("selected");
        } else {
          s.classList.remove("selected");
        }
      });
    });
  });
});

// === Dynamic Reviews AJAX ===
document.addEventListener("DOMContentLoaded", function () {
  // Fetch and render reviews from the database
  function renderStars(rating) {
    let html = "";
    for (let i = 1; i <= 5; i++) {
      html += `<span class="star ${i <= rating ? "filled" : "empty"}">★</span>`;
    }
    return html;
  }
  
  function escapeHTML(str) {
    return str.replace(/[&<>'"/]/g, function (s) {
      return {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        "'": "&#39;",
        '"': "&quot;",
        "/": "&#x2F;",
      }[s];
    });
  }
  
  function loadReviews() {
    fetch("admin/pages/reviews_api.php")
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          const scroller = document.getElementById("reviewsScroller");
          scroller.innerHTML = "";
          data.reviews.forEach((r) => {
            const card = document.createElement("div");
            card.className = "review-card";
            card.innerHTML = `
              <h4>${escapeHTML(r.name)}</h4>
              <div class="star-rating">${renderStars(r.rating)}</div>
              <p>"${escapeHTML(r.review_content)}"</p>
            `;
            scroller.appendChild(card);
          });
          duplicateReviewsForScrolling();
        }
      })
      .catch(error => {
        console.error("Error loading reviews:", error);
      });
  }
  
  // Submit review via AJAX
  const reviewForm = document.getElementById("reviewForm");
  if (reviewForm) {
    reviewForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(reviewForm);
      fetch("admin/pages/reviews_api.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            loadReviews();
            document.getElementById("reviewModal").style.display = "none";
            reviewForm.reset();
            document.getElementById("reviewerRating").value = "0";
            document
              .querySelectorAll("#reviewRatingInput .star-label")
              .forEach((s) => s.classList.remove("selected"));
            alert("Thank you for your review!");
          } else {
            alert("Failed to submit review.");
          }
        })
        .catch(error => {
          console.error("Error submitting review:", error);
          alert("Error submitting review. Please try again.");
        });
    });
  }
  
  loadReviews();
});

// Hero Slider
document.addEventListener("DOMContentLoaded", function () {
  const slides = document.querySelectorAll(".hero-slide");
  const dots = document.querySelectorAll(".slider-dot");
  const prevBtn = document.querySelector(".slider-control.prev");
  const nextBtn = document.querySelector(".slider-control.next");
  let currentSlide = 0;
  let slideInterval;

  // Function to show a specific slide
  function showSlide(index) {
    // Remove active class from all slides and dots
    slides.forEach((slide) => slide.classList.remove("active"));
    dots.forEach((dot) => dot.classList.remove("active"));

    // Add active class to current slide and dot
    slides[index].classList.add("active");
    dots[index].classList.add("active");

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
  prevBtn.addEventListener("click", () => {
    stopSlideInterval();
    prevSlide();
    startSlideInterval();
  });

  nextBtn.addEventListener("click", () => {
    stopSlideInterval();
    nextSlide();
    startSlideInterval();
  });

  // Event listeners for dots
  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      stopSlideInterval();
      showSlide(index);
      startSlideInterval();
    });
  });

  // Start the slider
  startSlideInterval();

  // Pause slider when hovering over controls
  const controls = document.querySelector(".hero-slider-controls");
  controls.addEventListener("mouseenter", stopSlideInterval);
  controls.addEventListener("mouseleave", startSlideInterval);

  // Room Details Modal Functionality
  const roomDetailsModal = document.getElementById("roomDetailsModal");
  const closeRoomDetailsModalBtn = document.getElementById(
    "closeRoomDetailsModalBtn"
  );
  const roomModalTitle = document.getElementById("roomModalTitle");
  const roomModalDescription = document.getElementById("roomModalDescription");
  const readMoreButtons = document.querySelectorAll(".read-more-btn");

  // Function to open room details modal
  function openRoomDetailsModal(title, description) {
    roomModalTitle.textContent = title;
    roomModalDescription.textContent = description;
    roomDetailsModal.style.display = "block";
    roomDetailsModal.classList.add("show");
    document.body.style.overflow = "hidden"; // Prevent background scrolling
  }

  // Function to close room details modal
  function closeRoomDetailsModal() {
    roomDetailsModal.style.display = "none";
    roomDetailsModal.classList.remove("show");
    document.body.style.overflow = "auto"; // Restore background scrolling
  }

  // Add click event listeners to all "Read More" buttons
  readMoreButtons.forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const title = this.getAttribute("data-room-title");
      const description = this.getAttribute("data-room-description");
      openRoomDetailsModal(title, description);
    });
  });

  // Close modal when close button is clicked
  if (closeRoomDetailsModalBtn) {
    closeRoomDetailsModalBtn.addEventListener("click", closeRoomDetailsModal);
  }

  // Close modal when clicking outside the modal content
  if (roomDetailsModal) {
    roomDetailsModal.addEventListener("click", function (e) {
      if (e.target === roomDetailsModal) {
        closeRoomDetailsModal();
      }
    });
  }

  // Close modal with Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && roomDetailsModal.style.display === "block") {
      closeRoomDetailsModal();
    }
  });
});
