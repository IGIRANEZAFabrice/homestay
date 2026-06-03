document.addEventListener("DOMContentLoaded", () => {
  // Hero slider (original hero)
  const slides = document.querySelectorAll(".slide");
  const dots = document.querySelectorAll(".slide-dot");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  let current = 0;
  const DURATION = 6000;
  let startTime;
  let rafId;

  const goTo = (idx) => {
    if (!slides.length) return;
    slides[current].classList.remove("active");
    dots[current].classList.remove("active");
    current = (idx + slides.length) % slides.length;
    slides[current].classList.add("active");
    dots[current].classList.add("active");
    resetTimer();
  };

  const resetTimer = () => {
    cancelAnimationFrame(rafId);
    startTime = performance.now();
    tick();
  };

  const tick = (now = performance.now()) => {
    const elapsed = now - startTime;
    if (elapsed >= DURATION) {
      goTo(current + 1);
    } else {
      rafId = requestAnimationFrame(tick);
    }
  };

  if (slides.length && dots.length && prevBtn && nextBtn) {
    prevBtn.addEventListener("click", () => goTo(current - 1));
    nextBtn.addEventListener("click", () => goTo(current + 1));
    dots.forEach((d, i) => d.addEventListener("click", () => goTo(i)));
    document.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft") goTo(current - 1);
      if (e.key === "ArrowRight") goTo(current + 1);
    });
    resetTimer();
  }

  // Rooms/Why reveal on scroll
  const revealEls = [
    { id: "roomsLabel", cls: "in" },
    { id: "roomsHeading", cls: "in" },
    { id: "roomsActions", cls: "in" },
    { id: "whyHeading", cls: "in" },
  ];

  const revObs = new IntersectionObserver(
    (entries) => entries.forEach((e) => e.isIntersecting && e.target.classList.add("in")),
    { threshold: 0.25 },
  );
  revealEls.forEach(({ id }) => {
    const el = document.getElementById(id);
    if (el) revObs.observe(el);
  });
  document.querySelectorAll("[data-reveal]").forEach((el) => revObs.observe(el));

  const roomCards = document.querySelectorAll(".room-card");
  const roomObs = new IntersectionObserver(
    (entries) => entries.forEach((e) => e.isIntersecting && e.target.classList.add("in")),
    { threshold: 0.15 },
  );
  roomCards.forEach((c) => roomObs.observe(c));



  // Metric counters in about card
  const counters = document.querySelectorAll("[data-count]");
  const counterObs = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((e) => {
        if (!e.isIntersecting) return;
        const el = e.target;
        const target = parseFloat(el.dataset.target || "0");
        const suffix = el.dataset.suffix || "";
        const duration = 1200;
        const startTime = performance.now();
        const start = 0;
        const step = (now) => {
          const p = Math.min((now - startTime) / duration, 1);
          const val = Math.floor(start + (target - start) * p);
          el.textContent = `${val}${suffix}`;
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
        obs.unobserve(el);
      });
    },
    { threshold: 0.5 },
  );
  counters.forEach((c) => counterObs.observe(c));
});
