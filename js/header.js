// Mobile Menu Functionality
document.addEventListener("DOMContentLoaded", function () {
  const mobileMenuToggle = document.getElementById("mobileMenuToggle");
  const navMenu = document.querySelector(".nav-menu");
  const body = document.body;
  const dropdowns = document.querySelectorAll(".dropdown");

  // Toggle mobile menu
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", function () {
      this.classList.toggle("active");
      navMenu.classList.toggle("active");
      body.style.overflow = navMenu.classList.contains("active")
        ? "hidden"
        : "auto";
    });
  }

  // Handle dropdowns
  dropdowns.forEach((dropdown) => {
    const toggle = dropdown.querySelector(".dropdown-toggle");

    // Toggle dropdown on click
    toggle.addEventListener("click", function (e) {
      e.preventDefault();

      // Close other dropdowns
      dropdowns.forEach((other) => {
        if (other !== dropdown) {
          other.classList.remove("active");
        }
      });

      dropdown.classList.toggle("active");
    });
  });

  // Close dropdowns when clicking outside
  document.addEventListener("click", function (e) {
    if (!e.target.closest(".dropdown")) {
      dropdowns.forEach((dropdown) => {
        dropdown.classList.remove("active");
      });
    }
  });

  // Close mobile menu when clicking on a link
  const navLinks = document.querySelectorAll(".nav-link");
  navLinks.forEach((link) => {
    link.addEventListener("click", function () {
      if (navMenu.classList.contains("active")) {
        mobileMenuToggle.classList.remove("active");
        navMenu.classList.remove("active");
        body.style.overflow = "auto";
      }
    });
  });

  // Close mobile menu when clicking outside
  document.addEventListener("click", function (e) {
    if (
      navMenu.classList.contains("active") &&
      !navMenu.contains(e.target) &&
      !mobileMenuToggle.contains(e.target)
    ) {
      mobileMenuToggle.classList.remove("active");
      navMenu.classList.remove("active");
      body.style.overflow = "auto";
    }
  });

  // Handle window resize
  window.addEventListener("resize", function () {
    if (window.innerWidth > 768) {
      mobileMenuToggle.classList.remove("active");
      navMenu.classList.remove("active");
      body.style.overflow = "auto";
    }
  });
});

// Newsletter form functionality
document.addEventListener("DOMContentLoaded", function () {
  const newsletterForm = document.querySelector(".newsletter-form");

  if (newsletterForm) {
    newsletterForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const emailInput = this.querySelector('input[type="email"]');
      const email = emailInput.value;

      if (email) {
        // Show success message
        const button = this.querySelector("button");
        const originalText = button.textContent;
        button.textContent = "Subscribed!";
        button.style.background = "#27ae60";

        // Reset form
        emailInput.value = "";

        // Reset button after 3 seconds
        setTimeout(() => {
          button.textContent = originalText;
          button.style.background = "";
        }, 3000);

        // Here you would typically send the email to your server
        console.log("Newsletter subscription:", email);
      }
    });
  }
});

// Smooth scrolling for anchor links
document.addEventListener("DOMContentLoaded", function () {
  const anchorLinks = document.querySelectorAll('a[href^="#"]');

  anchorLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      const href = this.getAttribute("href");

      if (href !== "#") {
        const targetElement = document.querySelector(href);

        if (targetElement) {
          e.preventDefault();

          const headerHeight = document.querySelector(".header").offsetHeight;
          const targetPosition = targetElement.offsetTop - headerHeight;

          window.scrollTo({
            top: targetPosition,
            behavior: "smooth",
          });
        }
      }
    });
  });
});
