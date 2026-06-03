<?php
  $pageTitle = '404 - Page Not Found';
  $pageCss = ['page-hero.css'];
  include 'includes/header.php';
?>

<section class="not-found" style="padding: 100px 0; text-align: center;">
    <div class="section-container">
        <h1 style="font-size: 4rem; color: var(--color-primary); margin-bottom: 20px;">404</h1>
        <h2 style="font-size: 2rem; margin-bottom: 30px;">Oops! Page Not Found</h2>
        <p style="font-size: 1.1rem; color: var(--color-text-2); margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>
        <a href="<?php echo $baseLink('home'); ?>" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-house"></i> Return Home
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
