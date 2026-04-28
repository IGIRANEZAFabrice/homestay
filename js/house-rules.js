/* ═══════════════════════════════════════════════════════════
   house-rules.js
   • Scroll-reveal (IntersectionObserver + stagger delays)
   • Accordion
   • Parallax orbs (mousemove + scroll)
   • Canvas floating particles in the cancellation section
   • 3-D tilt on fact cards
═══════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  /* ── 1. SCROLL REVEAL ──────────────────────────────────── */
  const revealObs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el    = entry.target;
      const delay = parseInt(el.dataset.delay || '0', 10);
      setTimeout(() => el.classList.add('is-visible'), delay);
      revealObs.unobserve(el);
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

  document.querySelectorAll('[data-reveal]').forEach(el => revealObs.observe(el));


  /* ── 2. ACCORDION ──────────────────────────────────────── */
  document.querySelectorAll('.hr-acc-trigger').forEach(btn => {
    btn.addEventListener('click', () => {
      const item   = btn.closest('.hr-acc-item');
      const isOpen = item.classList.contains('is-open');

      /* close all */
      document.querySelectorAll('.hr-acc-item.is-open').forEach(i => i.classList.remove('is-open'));

      /* open clicked (toggle) */
      if (!isOpen) item.classList.add('is-open');
    });
  });

  /* Open first item by default */
  const firstItem = document.querySelector('.hr-acc-item');
  if (firstItem) firstItem.classList.add('is-open');


  /* ── 3. PARALLAX ORBS (mousemove) ──────────────────────── */
  const orbL = document.querySelector('.hr-facts__orb--l');
  const orbR = document.querySelector('.hr-facts__orb--r');

  if (orbL && orbR) {
    document.addEventListener('mousemove', e => {
      const mx = (e.clientX / window.innerWidth  - 0.5) * 30;
      const my = (e.clientY / window.innerHeight - 0.5) * 20;
      orbL.style.transform = `translate(${mx}px, ${my}px)`;
      orbR.style.transform = `translate(${-mx}px, ${-my}px)`;
    });
  }


  /* ── 4. 3-D TILT on fact cards ─────────────────────────── */
  document.querySelectorAll('.hr-fact-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const rect  = card.getBoundingClientRect();
      const x     = (e.clientX - rect.left) / rect.width  - 0.5;
      const y     = (e.clientY - rect.top)  / rect.height - 0.5;
      card.style.transform = `translateY(-6px) rotateX(${-y * 10}deg) rotateY(${x * 10}deg)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
      card.style.transition = 'transform .5s var(--ease-out)';
    });
    card.addEventListener('mouseenter', () => {
      card.style.transition = 'transform .1s linear';
    });
  });


  /* ── 5. CANVAS FLOATING PARTICLES (cancellation section) ── */
  const canvas = document.getElementById('cancelCanvas');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    let W, H, particles = [], animId;

    function resize() {
      W = canvas.width  = canvas.offsetWidth;
      H = canvas.height = canvas.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    /* Particle constructor */
    function Particle() {
      this.reset();
    }
    Particle.prototype.reset = function () {
      this.x    = Math.random() * W;
      this.y    = Math.random() * H;
      this.r    = Math.random() * 1.8 + 0.5;
      this.vx   = (Math.random() - 0.5) * 0.35;
      this.vy   = -(Math.random() * 0.5 + 0.15);
      this.life = 0;
      this.maxLife = Math.random() * 260 + 120;
      const palette = [
        'rgba(200,113,26,VAL)',
        'rgba(224,138,47,VAL)',
        'rgba(255,220,150,VAL)',
      ];
      this.color = palette[Math.floor(Math.random() * palette.length)];
    };
    Particle.prototype.draw = function () {
      const alpha = Math.sin((this.life / this.maxLife) * Math.PI) * 0.55;
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
      ctx.fillStyle = this.color.replace('VAL', alpha);
      ctx.fill();
    };
    Particle.prototype.update = function () {
      this.x    += this.vx;
      this.y    += this.vy;
      this.life++;
      if (this.life >= this.maxLife || this.y < -10) this.reset();
    };

    /* Init */
    const COUNT = 80;
    for (let i = 0; i < COUNT; i++) {
      const p = new Particle();
      p.life = Math.random() * p.maxLife; /* stagger initial lifetimes */
      particles.push(p);
    }

    /* Animate — only run when section is visible */
    let sectionVisible = false;
    const sectionObs = new IntersectionObserver(entries => {
      sectionVisible = entries[0].isIntersecting;
      if (sectionVisible && !animId) loop();
    }, { threshold: 0 });
    const cancelSection = document.getElementById('hrCancel');
    if (cancelSection) sectionObs.observe(cancelSection);

    function loop() {
      if (!sectionVisible) { animId = null; return; }
      animId = requestAnimationFrame(loop);
      ctx.clearRect(0, 0, W, H);
      particles.forEach(p => { p.update(); p.draw(); });
    }
  }


  /* ── 6. SCROLL PARALLAX on extenuate badge ─────────────── */
  const badge = document.querySelector('.hr-extenuate__badge');
  if (badge) {
    let rafPending = false;
    window.addEventListener('scroll', () => {
      if (rafPending) return;
      rafPending = true;
      requestAnimationFrame(() => {
        const rect   = badge.getBoundingClientRect();
        const center = rect.top + rect.height / 2 - window.innerHeight / 2;
        badge.style.transform = `translateY(${center * -0.05}px) rotate(${center * 0.008}deg)`;
        rafPending = false;
      });
    }, { passive: true });
  }


  /* ── 7. SMOOTH COUNTER for stat numbers ─────────────────── */
  /* (No numeric stats on this page, but if you add them later, call countUp(el, to)) */
  function countUp(el, to, duration = 1400) {
    let start     = null;
    const from    = 0;
    const step    = ts => {
      if (!start) start = ts;
      const t  = Math.min((ts - start) / duration, 1);
      const v  = Math.round(from + (to - from) * (1 - Math.pow(1 - t, 3)));
      el.textContent = v.toLocaleString();
      if (t < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }
  /* expose globally in case PHP templates call it */
  window.hrCountUp = countUp;

})();