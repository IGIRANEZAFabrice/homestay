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

  // Scroll driver for stacked hero
  const heroPin = document.getElementById("hero-pin");
  const centerImg = document.getElementById("centerImg");
  const heroTitle = document.getElementById("heroTitle");
  const heroSub = document.getElementById("heroSub");
  const wrapper = document.getElementById("stackWrapper");
  const sideImgs = [0, 1, 2, 3, 4, 5, 6].map((i) => document.getElementById(`sImg${i}`));
  const ROTS = [-6, 5, -4, 7, -2, 6, -5];

  const eio = (p) => (p < 0.5 ? 2 * p * p : -1 + (4 - 2 * p) * p);

  setTimeout(() => {
    heroTitle.classList.add("appeared");
    sideImgs.forEach((img, i) =>
      setTimeout(() => img.classList.add("appeared"), i * 130),
    );
  }, 120);

  setTimeout(() => {
    sideImgs.forEach((img) => {
      img.style.transition = "none";
    });
  }, 1200);

  let ticking = false;
  function onScroll() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
      ticking = false;

      const pinTop = heroPin.getBoundingClientRect().top;
      const pinH = heroPin.offsetHeight;
      const viewW = window.innerWidth;
      const viewH = window.innerHeight;

      const scrolled = -pinTop;
      const total = pinH - viewH;
      const p = Math.min(Math.max(scrolled / total, 0), 1);
      const ep = eio(p);

      const wRect = wrapper.getBoundingClientRect();
      const imgW = wRect.width * 0.92;
      const imgH = wRect.height * 0.92;
      const scaleNeeded = Math.max(viewW / imgW, viewH / imgH) * 1.02;
      const scale = 1 + (scaleNeeded - 1) * ep;

      const imgCX = wRect.left + wRect.width * 0.04 + imgW / 2;
      const imgCY = wRect.top + wRect.height * 0.04 + imgH / 2;
      const tx = (viewW / 2 - imgCX) * ep;
      const ty = (viewH / 2 - imgCY) * ep;

      centerImg.style.transform = `translate(${tx}px,${ty}px) scale(${scale})`;
      centerImg.style.borderRadius = `${8 * (1 - ep)}px`;

      sideImgs.forEach((img, i) => {
        if (!img.classList.contains("appeared")) return;
        const thumbCY = 24 + i * 62 + 26;
        const thumbCX = viewW - 24 - 26;

        const iRect = img.getBoundingClientRect();
        const iCX = iRect.left + iRect.width / 2;
        const iCY = iRect.top + iRect.height / 2;

        const dx = (thumbCX - iCX) * ep;
        const dy = (thumbCY - iCY) * ep;
        const sc = 1 - ep * 0.62;

        const fadeP = Math.min(p / 0.5, 1);
        img.style.opacity = 1 - fadeP;
        img.style.transform = `translate(${dx}px,${dy}px) scale(${sc}) rotate(${ROTS[i]}deg)`;
      });

      heroSub.classList.toggle("show", p > 0.04);
      heroTitle.style.opacity = Math.max(0, 1 - ep * 1.5).toString();
    });
  }

  window.addEventListener("scroll", onScroll, { passive: true });
  window.addEventListener("resize", onScroll);
  onScroll();

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
