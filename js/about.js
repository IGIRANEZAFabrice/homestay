// About page interactions (reveals, scroll video, progress)
document.addEventListener("DOMContentLoaded", () => {
  // Scroll progress bar
  const progress = document.getElementById("scrollProgress");
  if (progress) {
    const updateProgress = () => {
      const scroll = window.scrollY;
      const height = document.documentElement.scrollHeight - window.innerHeight;
      const pct = height > 0 ? (scroll / height) * 100 : 0;
      progress.style.width = `${pct}%`;
    };
    updateProgress();
    window.addEventListener("scroll", updateProgress, { passive: true });
    window.addEventListener("resize", updateProgress);
  }

  // Reveal animations
  const revealEls = document.querySelectorAll(".reveal, .reveal-left, .reveal-right");
  if (revealEls.length) {
    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("visible");
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.2, rootMargin: "0px 0px -10%" },
    );
    revealEls.forEach((el) => obs.observe(el));
  }

  // Line stagger headline
  document.querySelectorAll(".manifesto-headline .line-stagger span").forEach((span) => {
    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            span.classList.add("in");
            obs.unobserve(span);
          }
        });
      },
      { threshold: 0.5 },
    );
    obs.observe(span);
  });

  // Scroll-driven video
  const video = document.getElementById("scrollVideo");
  const section = document.getElementById("videoSection");
  const words = document.querySelectorAll(".video-word");
  if (video && section) {
    let duration = 0;
    video.addEventListener("loadedmetadata", () => {
      duration = video.duration || 0;
    });
    const onScroll = () => {
      const rect = section.getBoundingClientRect();
      const total = rect.height - window.innerHeight;
      const offset = Math.min(Math.max(-rect.top, 0), total);
      const progress = total > 0 ? offset / total : 0;
      if (duration) video.currentTime = progress * duration;
      const idx = Math.min(words.length - 1, Math.floor(progress * words.length));
      words.forEach((w, i) => w.classList.toggle("active", i === idx));
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
    onScroll();
  }
});
