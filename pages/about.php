<?php
require_once '../include/connection.php';

// Check if admin is logged in
$is_admin = false;
session_start();
if (isset($_SESSION['admin_user_id']) && !empty($_SESSION['admin_user_id'])) {
    $is_admin = true;
}

// Get sections data
$sections_query = "SELECT * FROM about_sections ORDER BY display_order ASC";
$sections_result = $conn->query($sections_query);
$sections = [];
if ($sections_result && $sections_result->num_rows > 0) {
    while ($row = $sections_result->fetch_assoc()) {
        $sections[$row['section_name']] = $row;
    }
}

// Get features data
$features_query = "SELECT * FROM about_features WHERE is_active = 1 ORDER BY display_order ASC";
$features_result = $conn->query($features_query);
$features = [];
if ($features_result && $features_result->num_rows > 0) {
    while ($row = $features_result->fetch_assoc()) {
        $features[] = $row;
    }
}

// Get guidelines data
$guidelines_query = "SELECT * FROM about_guidelines WHERE is_active = 1 ORDER BY display_order ASC";
$guidelines_result = $conn->query($guidelines_query);
$guidelines = [];
if ($guidelines_result && $guidelines_result->num_rows > 0) {
    while ($row = $guidelines_result->fetch_assoc()) {
        $guidelines[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Virunga Homestay - Authentic Rwandan Experience</title>
  
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/about.css">
    <link rel="stylesheet" href="../css/logo.css">
    <link rel="stylesheet" href="../css/rooms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    
    <?php include './include/header.php'; ?>
    <section class="rooms-hero">
        <h1><?php echo htmlspecialchars($sections['hero']['title'] ?? 'About Us'); ?></h1>
        <p><?php echo htmlspecialchars($sections['hero']['subtitle'] ?? 'About Virunga Homestay'); ?></p>
    </section>
    <main class="main-content">
        <div class="container">
            <section class="section fade-in">
                <div class="mission-grid" id="ourmission">
                    <div class="mission-text">
                        <h2 class="section-title"><?php echo htmlspecialchars($sections['mission']['title'] ?? 'Our Mission'); ?></h2>
                        <?php 
                        if (isset($sections['mission']['content'])) {
                            $paragraphs = explode("\n\n", $sections['mission']['content']);
                            foreach ($paragraphs as $paragraph) {
                                echo '<p>' . htmlspecialchars($paragraph) . '</p><br>';
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="mission-image">
                        <img src="<?php echo htmlspecialchars($sections['mission']['image_path'] ?? '../img/house.jpg'); ?>" alt="Our Mission" />
                    </div>
                </div>
            </section>

            <section id="whychoose" class="why-choose-section fade-in">
            
                <div class="section-header">
                    <h2 class="section-title"><?php echo htmlspecialchars($sections['why_choose']['title'] ?? 'Why Choose Virunga Homestay?'); ?></h2>
                    <p style="font-size: 1.2rem; color: #666; max-width: 800px; margin: 0 auto; line-height: 1.7;">
                        <?php echo htmlspecialchars($sections['why_choose']['subtitle'] ?? ''); ?>
                    </p>
                </div>
            </section>
                <div class="features-grid">
                    <?php foreach ($features as $feature): ?>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <?php
                            // Map titles to appropriate Font Awesome icons
                            $iconMap = [
                                'Authenticity and Warmth' => '<i class="fas fa-home"></i>',
                                'Personalized Hospitality' => '<i class="fas fa-heart"></i>',
                                'Insider Knowledge' => '<i class="fas fa-lightbulb"></i>',
                                'Culinary Journey' => '<i class="fas fa-utensils"></i>',
                                'Community Connections' => '<i class="fas fa-users"></i>',
                                'Tranquil Natural Setting' => '<i class="fas fa-tree"></i>',
                                'Safety and Trust' => '<i class="fas fa-shield-alt"></i>',
                                'Responsible Tourism' => '<i class="fas fa-leaf"></i>'
                            ];
                            
                            // Use mapped icon if available, otherwise use the icon from database
                            if (isset($iconMap[$feature['title']])) {
                                echo $iconMap[$feature['title']];
                            } else {
                                echo htmlspecialchars($feature['icon']);
                            }
                            ?>
                        </div>
                        <h3 class="feature-title"><?php echo htmlspecialchars($feature['title']); ?></h3>
                        <p class="feature-text">
                            <?php echo htmlspecialchars($feature['description']); ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="conclusion-text">
                    <p><?php echo htmlspecialchars($sections['why_choose']['content'] ?? ''); ?></p>
                </div>
            </section>

           <section class="parallax-section" style="width: 100%; height: 400px; position: relative; margin: 40px 0; background-image: url('../img/home.jpg'); background-attachment: fixed; background-position: center; background-repeat: no-repeat; background-size: cover;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4));">
                    <div style="position: relative; height: 100%; display: flex; align-items: center; justify-content: center; color: white; text-align: center; padding: 0 20px;">
                    <div>
                        <h2 style="font-size: 2.5rem; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Experience Local Cuisine</h2>
                        <p style="font-size: 1.2rem; max-width: 800px; margin: 0 auto; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">Savor authentic Rwandan dishes prepared with locally-sourced ingredients</p>
                    </div>
                    </div>
                </div>
            </section>


            <section class="guidelines-section fade-in">
                <div class="section-header">
                    <h2 class="section-title"><?php echo htmlspecialchars($sections['guidelines']['title'] ?? 'Homestay Guidelines'); ?></h2>
                    <p class="section-subtitle"><?php echo htmlspecialchars($sections['guidelines']['subtitle'] ?? 'To ensure a harmonious and enriching experience for all guests, we kindly ask you to observe the following guidelines during your stay with us.'); ?></p>
                </div>

                <div id="guidelines" class="guidelines-container">
                    <?php foreach ($guidelines as $guideline): ?>
                    <div class="guideline-item">
                        <div class="guideline-icon">
                            <?php
                            // Map titles to appropriate icons using Font Awesome instead of emojis
                            $iconMap = [
                                'Check-in & Check-out' => '<i class="fas fa-clock"></i>',
                                'Meals & Dining' => '<i class="fas fa-utensils"></i>',
                                'Security & Keys' => '<i class="fas fa-key"></i>',
                                'Housekeeping' => '<i class="fas fa-broom"></i>',
                                'Smoking Policy' => '<i class="fas fa-smoking-ban"></i>',
                                'Visitors' => '<i class="fas fa-users"></i>',
                                'Quiet Hours' => '<i class="fas fa-volume-down"></i>',
                                'Internet Access' => '<i class="fas fa-wifi"></i>',
                                'Maintain Hygiene' => '<i class="fas fa-soap"></i>',
                                'Follow House Rules' => '<i class="fas fa-clipboard-list"></i>',
                                'Manage Expectations' => '<i class="fas fa-balance-scale"></i>',
                                'Use Resources Wisely' => '<i class="fas fa-leaf"></i>',
                                'Be Considerate' => '<i class="fas fa-handshake"></i>',
                                'Embrace the Experience' => '<i class="fas fa-star"></i>'
                            ];
                            echo $iconMap[$guideline['title']] ?? '<i class="fas fa-clipboard"></i>'; // Default icon if title not found
                            ?>
                        </div>
                        <div class="guideline-content">
                            <h3 class="guideline-title"><?php echo htmlspecialchars($guideline['title']); ?></h3>
                            <p class="guideline-text"><?php echo nl2br(htmlspecialchars($guideline['content'])); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="guidelines-note">
                    <p>These guidelines are designed to create a respectful and enjoyable environment for everyone. We appreciate your cooperation and are always available to address any questions or concerns you may have during your stay. Our goal is to make your experience at Virunga Homestay as comfortable and memorable as possible.</p>
                </div>
            </section>
        </div>

        <section class="experience-section" style="background-image: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.55)), url('../img/dog.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; color: #fff;">
            <div class="container">
                <div class="experience-content fade-in">
                    <h2 class="experience-title"><?php echo htmlspecialchars($sections['experience']['title'] ?? 'More Than Just Accommodation'); ?></h2>
                    <p class="experience-text">
                        <?php echo htmlspecialchars($sections['experience']['subtitle'] ?? ''); ?>
                    </p>
                </div>
            </div>
        </section>

        <section class="cta-section fade-in">
            <div class="container">
                <h2 style="font-size: 2.5rem; color: #DA7D2F; margin-bottom: 30px;"><?php echo htmlspecialchars($sections['cta']['title'] ?? 'Ready for Your Authentic Experience?'); ?></h2>
                <p style="font-size: 1.2rem; color: #666; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
                    <?php echo htmlspecialchars($sections['cta']['subtitle'] ?? 'Join us for an unforgettable journey into the heart of Rwandan culture and hospitality.'); ?>
                </p>
                <a href="./room.php" class="cta-button">Book Your Stay</a>
            </div>
        </section>
    </main>
    <?php include 'include/footer.php'; ?>
    <script>
        // Smooth scrolling for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe all fade-in elements
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroSection = document.querySelector('.hero-section');
            const heroContent = document.querySelector('.hero-content');
            
            if (heroSection && scrolled < window.innerHeight) {
                heroContent.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });

        // Add interactive hover effects to guideline cards
        document.querySelectorAll('.guideline-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(-5px) scale(1)';
            });
        });

        // Dynamic color changes on scroll
        window.addEventListener('scroll', () => {
            const scrollPercent = window.scrollY / (document.body.scrollHeight - window.innerHeight);
            const hue = Math.floor(100 + (scrollPercent * 50)); // Green to blue-green spectrum
            
            document.documentElement.style.setProperty('--dynamic-color', `hsl(${hue}, 35%, 45%)`);
        });

        // Add some interactive particles on hover over hero section
        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            heroSection.addEventListener('mousemove', (e) => {
                const rect = heroSection.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                
                heroSection.style.background = `radial-gradient(circle at ${x}% ${y}%, rgba(218, 125, 47, 0.15) 0%, transparent 50%), linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 50%, #1f2937 100%)`;
            });
        }
    </script>
    <script src="../js/header.js"></script>
</body>
</html>