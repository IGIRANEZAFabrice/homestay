<?php
  $pageTitle = '404 - Path Not Found';
  include 'includes/header.php';
?>

<style>
  /* ── 404 REDESIGN ── */
  :root {
    --amber-404: #C47B2B;
    --dark-404: #3D2008;
    --bg-404: #FAF6F0;
  }

  #not-found-page {
    position: relative;
    min-height: calc(100vh - 80px); /* Adjust for nav height */
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--bg-404);
    overflow: hidden;
    padding-bottom: 120px; /* Space for hills */
  }

  /* Imigongo Background Pattern */
  #not-found-page::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0l30 30-30 30L0 30z' fill='%23C47B2B' fill-opacity='0.35' fill-rule='evenodd'/%3E%3C/svg%3E");
    background-size: 40px 40px;
    opacity: 0.35;
    z-index: 0;
  }

  .scene-container {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 520px;
    margin: 0 auto;
    text-align: center;
    padding: 0 20px;
  }

  /* 404 Number */
  .error-code {
    display: block;
    font-family: var(--font-display);
    font-size: clamp(3.5rem, 8vw, 5.5rem);
    font-weight: 700;
    color: var(--amber-404);
    letter-spacing: 0.15em;
    text-shadow: 2px 3px 0 rgba(61,32,8,0.15);
    margin-bottom: 1rem;
    line-height: 1;
  }

  /* Headline & Subtext */
  .error-title {
    font-family: var(--font-display);
    font-size: clamp(1.8rem, 4vw, 2.4rem);
    color: var(--dark-404);
    margin-bottom: 0.75rem;
    line-height: 1.2;
  }

  .error-desc {
    font-size: 1.05rem;
    color: var(--dark-404);
    opacity: 0.8;
    margin-bottom: 2.5rem;
  }

  /* Buttons */
  .error-btns {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
  }

  .btn-home {
    background: var(--amber-404);
    color: #fff;
    padding: 12px 28px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    text-decoration: none;
    border-radius: 4px;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease;
    border: none;
  }

  .btn-home::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    background: rgba(255,255,255,0.3);
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
    opacity: 0;
  }

  @media (prefers-reduced-motion: no-preference) {
    .btn-home:hover::after {
      animation: ripple 0.5s ease-out;
    }
  }

  @keyframes ripple {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
    100% { transform: translate(-50%, -50%) scale(5); opacity: 0; }
  }

  .btn-home:hover {
    transform: translateY(-2px);
  }

  .link-stays {
    color: var(--dark-404);
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    position: relative;
    padding-bottom: 2px;
  }

  .link-stays::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 1px;
    background: var(--dark-404);
    transition: width 0.3s ease;
  }

  .link-stays:hover::after {
    width: 100%;
  }

  /* Particles */
  .particle {
    position: absolute;
    width: 3px;
    height: 3px;
    background: var(--amber-404);
    border-radius: 50%;
    opacity: 0.5;
    pointer-events: none;
    z-index: 5;
  }

  @media (prefers-reduced-motion: no-preference) {
    .particle {
      animation: float-up 6s linear infinite;
    }
  }

  @keyframes float-up {
    0% { transform: translateY(0) translateX(0); opacity: 0; }
    20% { opacity: 0.5; }
    80% { opacity: 0.5; }
    100% { transform: translateY(-100vh) translateX(20px); opacity: 0; }
  }

  /* Scrolling Hills */
  .hills-container {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 120px;
    z-index: 5;
    pointer-events: none;
  }

  .hill-layer {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 200%;
    height: 100%;
    background-repeat: repeat-x;
    background-position: bottom left;
  }

  .hill-back {
    height: 100%;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 1000 120' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 120L150 40L300 90L450 20L600 80L750 30L900 100L1000 50V120H0z' fill='%23C8BBA8'/%3E%3C/svg%3E");
  }

  .hill-mid {
    height: 80%;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 1000 120' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 120L200 60L400 100L600 50L800 90L1000 40V120H0z' fill='%23B09880'/%3E%3C/svg%3E");
  }

  .hill-front {
    height: 60%;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 1000 120' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 120L250 80L500 110L750 70L1000 90V120H0z' fill='%238B7355'/%3E%3C/svg%3E");
  }

  @media (prefers-reduced-motion: no-preference) {
    .hill-back { animation: scroll-hill 30s linear infinite; }
    .hill-mid { animation: scroll-hill 20s linear infinite; }
    .hill-front { animation: scroll-hill 12s linear infinite; }
  }

  @keyframes scroll-hill {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }
</style>

<main id="not-found-page">
  <!-- Particles -->
  <div class="particle" style="left: 15%; bottom: 10%; animation-delay: 0s;"></div>
  <div class="particle" style="left: 45%; bottom: 5%; animation-delay: 1.5s;"></div>
  <div class="particle" style="left: 65%; bottom: 15%; animation-delay: 3s;"></div>
  <div class="particle" style="left: 85%; bottom: 8%; animation-delay: 0.5s;"></div>
  <div class="particle" style="left: 25%; bottom: 20%; animation-delay: 4.5s;"></div>

  <!-- Hills -->
  <div class="hills-container">
    <div class="hill-layer hill-back"></div>
    <div class="hill-layer hill-mid"></div>
    <div class="hill-layer hill-front"></div>
  </div>

  <div class="scene-container">
    <span class="error-code">404</span>

    <h1 class="error-title">Page Not Found</h1>
    <p class="error-desc">The page you are looking for doesn't exist or has been moved.</p>
    
    <div class="error-btns">
      <a href="<?php echo $baseLink('home'); ?>" class="btn-home">Return Home</a>
      <a href="<?php echo $baseLink('rooms'); ?>" class="link-stays">Explore our stays &rarr;</a>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
