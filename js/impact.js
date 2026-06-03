document.addEventListener("DOMContentLoaded", () => {
  // Intersection Observer for reveal animations
  const revealEls = document.querySelectorAll(".reveal, .reveal-right");
  const revObs = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.add("in");
          // Optionally unobserve after revealing
          // revObs.unobserve(e.target);
        }
      });
    },
    { threshold: 0.15 }
  );

  revealEls.forEach((el) => revObs.observe(el));

  // Numeric counters
  const counters = document.querySelectorAll("[data-count]");
  const counterObs = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((e) => {
        if (!e.isIntersecting) return;
        const el = e.target;
        const target = parseFloat(el.dataset.target || "0");
        const duration = 2000;
        const startTime = performance.now();
        const start = 0;

        const step = (now) => {
          const p = Math.min((now - startTime) / duration, 1);
          // Easing function for smooth finish
          const ep = 1 - Math.pow(1 - p, 3); 
          const val = Math.floor(start + (target - start) * ep);
          el.textContent = val.toLocaleString();
          if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
        obs.unobserve(el);
      });
    },
    { threshold: 0.5 }
  );

  counters.forEach((c) => counterObs.observe(c));
});
