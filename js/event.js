// Load More functionality
const eventsPerPage = 6; // Show 6 events initially
let currentlyShown = eventsPerPage;

function initializeLoadMore() {
  const allEvents = document.querySelectorAll(".event-card");
  const loadMoreBtn = document.querySelector(".load-more-btn");

  // Initially hide events beyond the first batch
  allEvents.forEach((event, index) => {
    if (index >= eventsPerPage) {
      event.classList.add("hidden");
    }
  });

  // Hide load more button if there aren't enough events
  if (allEvents.length <= eventsPerPage) {
    loadMoreBtn.classList.add("hidden");
  }

  // Add click event to load more button
  loadMoreBtn.addEventListener("click", () => {
    const hiddenEvents = document.querySelectorAll(".event-card.hidden");

    // Show next batch of events
    for (let i = 0; i < eventsPerPage && i < hiddenEvents.length; i++) {
      hiddenEvents[i].classList.remove("hidden");
    }

    currentlyShown += eventsPerPage;

    // Hide button if all events are shown
    if (currentlyShown >= allEvents.length) {
      loadMoreBtn.classList.add("hidden");
    }
  });
}

// Countdown Timer Function
function updateCountdown() {
  const cards = document.querySelectorAll(".event-card[data-event-date]");

  cards.forEach((card) => {
    const eventDate = new Date(card.getAttribute("data-event-date")).getTime();
    const now = new Date().getTime();
    const distance = eventDate - now;

    const countdown = card.querySelector(".countdown");
    const status = card.querySelector(".event-status");

    if (distance < 0) {
      // Event has passed
      countdown.innerHTML =
        '<div class="countdown-title">Event has ended</div>';
      countdown.classList.add("expired");
      status.textContent = "Event Ended";
      card.classList.add("ended");
    } else {
      // Calculate time units
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor(
        (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
      );
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // Update countdown display
      card.querySelector(".days").textContent = days
        .toString()
        .padStart(2, "0");
      card.querySelector(".hours").textContent = hours
        .toString()
        .padStart(2, "0");
      card.querySelector(".minutes").textContent = minutes
        .toString()
        .padStart(2, "0");
      card.querySelector(".seconds").textContent = seconds
        .toString()
        .padStart(2, "0");

      // Update status based on time remaining
      if (days > 7) {
        status.textContent = "Upcoming Event";
        card.classList.remove("started", "soon");
        card.classList.add("upcoming");
      } else if (days > 0) {
        status.textContent = "Starting Soon";
        card.classList.remove("upcoming", "started");
        card.classList.add("soon");
      } else if (hours > 0 || minutes > 0) {
        status.textContent = "Starting Today";
        card.classList.remove("upcoming", "soon");
        card.classList.add("started");
      }
    }
  });
}

// Add interactive animations
function initializeAnimations() {
  const cards = document.querySelectorAll(".event-card");

  cards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-15px) scale(1.02)";
    });

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0) scale(1)";
    });
  });

  // Add smooth scrolling and fade-in animations
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

  cards.forEach((card, index) => {
    card.style.opacity = "0";
    card.style.transform = "translateY(50px)";
    card.style.transition = `opacity 0.6s ease ${
      index * 0.1
    }s, transform 0.6s ease ${index * 0.1}s`;
    observer.observe(card);
  });
}

// Initialize everything when the DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  initializeLoadMore();
  initializeAnimations();
  updateCountdown();
  setInterval(updateCountdown, 1000);
});
