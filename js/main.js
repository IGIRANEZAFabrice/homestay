document.addEventListener("DOMContentLoaded", () => {
  // Sticky nav color change on scroll
  const nav = document.getElementById("mainNav");
  if (nav) {
    const toggleScrolled = () => nav.classList.toggle("scrolled", window.scrollY > 40);
    toggleScrolled();
    window.addEventListener("scroll", toggleScrolled, { passive: true });
  }

  // Hamburger + mobile drawer
  const ham = document.getElementById("hamburger");
  const menu = document.getElementById("mobileMenu");
  if (ham && menu) {
    ham.addEventListener("click", () => {
      const isOpen = ham.classList.toggle("open");
      menu.classList.toggle("open", isOpen);
      ham.setAttribute("aria-expanded", String(isOpen));
      menu.setAttribute("aria-hidden", String(!isOpen));
    });

    menu.querySelectorAll("a").forEach((a) =>
      a.addEventListener("click", () => {
        ham.classList.remove("open");
        menu.classList.remove("open");
        ham.setAttribute("aria-expanded", "false");
        menu.setAttribute("aria-hidden", "true");
      }),
    );
  }

  // Mobile Services accordion
  const mobBtn = document.getElementById("mobServicesBtn");
  const mobPanel = document.getElementById("mobServicesPanel");
  if (mobBtn && mobPanel) {
    mobBtn.addEventListener("click", () => {
      const isOpen = mobBtn.classList.toggle("open");
      mobPanel.classList.toggle("open", isOpen);
      mobBtn.setAttribute("aria-expanded", String(isOpen));
    });

    mobPanel.querySelectorAll("a").forEach((a) =>
      a.addEventListener("click", () => {
        ham?.classList.remove("open");
        menu?.classList.remove("open");
      }),
    );
  }
});
