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

  // Scroll-driven frame sequence
  const canvas = document.getElementById("scrollCanvas");
  const section = document.getElementById("videoSection");
  const words = document.querySelectorAll(".video-word");
  const dots = document.querySelectorAll(".vp-dot");
  
  if (canvas && section) {
    const ctx = canvas.getContext("2d");
    const frameCount = 30; // Total number of frames in the folder
    const images = [];
    let currentProgress = 0;
    let targetProgress = 0;
    const lerpAmount = 0.08; // Adjust for more/less smoothing

    // Preload images
    const currentFrame = (index) => `./img/home/about/ezgif-frame-${(index + 1).toString().padStart(3, "0")}.png`;

    for (let i = 0; i < frameCount; i++) {
      const img = new Image();
      img.src = currentFrame(i);
      images.push(img);
    }

    const resizeCanvas = () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    };

    const drawFrame = (image, opacity, frameIdx) => {
      if (!image || !image.complete) return;
      ctx.globalAlpha = opacity;
      
      const scale = Math.max(canvas.width / image.width, canvas.height / image.height);
      const zoomFactor = 1 + (frameIdx / frameCount) * 0.05; 
      const finalScale = scale * zoomFactor;

      const x = (canvas.width / 2) - (image.width / 2) * finalScale;
      const y = (canvas.height / 2) - (image.height / 2) * finalScale;
      ctx.drawImage(image, x, y, image.width * finalScale, image.height * finalScale);
    };

    const render = () => {
      // Linear interpolation for smooth scrolling
      currentProgress += (targetProgress - currentProgress) * lerpAmount;
      
      // Calculate float frame index for blending
      const exactFrame = currentProgress * (frameCount - 1);
      const frameIndex = Math.floor(exactFrame);
      const nextFrameIndex = Math.min(frameIndex + 1, frameCount - 1);
      const blendAmount = exactFrame - frameIndex;

      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.imageSmoothingEnabled = true;
      ctx.imageSmoothingQuality = 'high';

      // Draw base frame
      drawFrame(images[frameIndex], 1, frameIndex);
      
      // Draw next frame with blend opacity for smooth fade
      if (blendAmount > 0.01) {
        drawFrame(images[nextFrameIndex], blendAmount, nextFrameIndex);
      }

      // Update words and dots based on current progress
      const idx = Math.min(words.length - 1, Math.floor(currentProgress * words.length));
      words.forEach((w, i) => {
        if (w.classList.contains("active") !== (i === idx)) {
          w.classList.toggle("active", i === idx);
        }
      });
      dots.forEach((d, i) => {
        if (d.classList.contains("active") !== (i === idx)) {
          d.classList.toggle("active", i === idx);
        }
      });

      requestAnimationFrame(render);
    };

    const onScroll = () => {
      const rect = section.getBoundingClientRect();
      const total = rect.height - window.innerHeight;
      const offset = Math.min(Math.max(-rect.top, 0), total);
      targetProgress = total > 0 ? offset / total : 0;
    };

    // Initialize
    resizeCanvas();
    window.addEventListener("resize", resizeCanvas);
    window.addEventListener("scroll", onScroll, { passive: true });
    
    // Start animation loop
    onScroll();
    requestAnimationFrame(render);
  }
});
