<?php
  $pageTitle = 'Virunga Homestay - Community Activities';
  $pageCss = ['page-hero.css', 'room-cards.css', 'activity.css'];
  $pageHeroKey = 'activity';
  $pageScripts = ['activity.js'];
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>

<main id="activity-page">
  <section class="activity-intro">
    <div class="section-container">
      <div class="activity-intro__content">
        <div class="section-label">Professional Positioning Statement</div>
        <h2 class="section-heading activity-intro__title">Top curated experiences designed by experts</h2>
        <p class="activity-intro__body">
          Discover our signature collection of immersive experiences, or allow us to craft a personalized journey tailored to your interests, pace, and sense of adventure in the Virunga region.
        </p>
      </div>
    </div>
  </section>

  <section id="activities">
    <div class="activities-bg"></div>
    <div class="activities-overlay"></div>
    <div class="section-container">
      <?php
      require_once __DIR__ . '/../config/db.php';
      
      $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
      $categoryTitle = "Curated Community Encounters";
      
      if ($categoryId > 0) {
          $stmtCat = $conn->prepare("SELECT title FROM home_experience WHERE id = ?");
          $stmtCat->bind_param("i", $categoryId);
          $stmtCat->execute();
          $resCat = $stmtCat->get_result();
          if ($rowCat = $resCat->fetch_assoc()) {
              $categoryTitle = htmlspecialchars($rowCat['title']) . " Experiences";
          }
      }
      ?>
      <div class="section-label">Community Experiences</div>
      <div class="activities-header">
        <h2 class="section-heading"><?php echo $categoryTitle; ?></h2>
        <p class="section-sub">
          The same warm moments you saw on Home, now expanded with more ways to explore.
        </p>
      </div>

      <div class="activities-slider-wrapper">
        <button class="slider-arrow slider-arrow--left" id="slideLeft" aria-label="Scroll left">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        
        <div class="activities-grid slider-container" id="activitiesSlider">
          <?php
          $sqlAct = "SELECT * FROM activities WHERE status = 'active'";
          if ($categoryId > 0) {
              $sqlAct .= " AND category_id = $categoryId";
          }
          $sqlAct .= " ORDER BY display_order ASC, id DESC";
          $resAct = $conn->query($sqlAct);

          if ($resAct && $resAct->num_rows > 0) {
              while ($rowAct = $resAct->fetch_assoc()) {
                  $imgVal = !empty($rowAct['image']) ? $rowAct['image'] : 'services/2.jpg';
                  if (strpos($imgVal, 'http') === 0) {
                      $image = $imgVal;
                  } else {
                      $imgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($imgVal, '/'));
                      if (strpos($imgVal, '/') === false) {
                          $imgVal = 'activities/' . $imgVal;
                      }
                      $image = './img/' . $imgVal;
                  }
                  $image = htmlspecialchars($image);
                  
                  $title = htmlspecialchars($rowAct['title']);
                  $tag = htmlspecialchars($rowAct['tag']);
                  $duration = htmlspecialchars($rowAct['duration'] ?? '');
                  $detailLink = $baseLink('activitydetails') . (strpos($baseLink('activitydetails'), '?') !== false ? '&' : '?') . 'id=' . $rowAct['id'];

                  echo '
                  <a class="activity-card" href="' . $detailLink . '" style="background-image: url(\'' . $image . '\');">
                     <div class="activity-card__top-tag">' . $duration . '</div>
                     <div class="activity-card__overlay"></div>
                     <div class="activity-card__content">
                       <h3 class="activity-card__title">' . $title . '</h3>
                       <div class="activity-card__button">BOOK NOW</div>
                     </div>
                   </a>';
               }
           }
           ?>
           
           <!-- Custom Itinerary Card -->
             <a class="activity-card activity-card--custom" href="https://wa.me/250784513435" target="_blank" style="background-image: url('./img/hero/paint.jpg');">
               <div class="activity-card__top-tag">CUSTOM TRIPS</div>
               <div class="activity-card__overlay"></div>
               <div class="activity-card__content">
                 <h3 class="activity-card__title activity-card__title--large">Craft Your Own Experience</h3>
                 <div class="activity-card__button">Contact Us</div>
               </div>
             </a>
        </div>

        <button class="slider-arrow slider-arrow--right" id="slideRight" aria-label="Scroll right">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </section>

  <section class="signature-collection">
    <div class="section-container">
      <header class="signature-header">
        <span class="signature-label">Virunga Homestay</span>
        <h2 class="signature-title">Tailor-Made Experiences</h2>
        <p class="signature-desc">
          You can choose and contact us for any of the following spiritual, cultural, wildlife, culinary, wellness, and scenic experiences.
        </p>
      </header>

      <div class="signature-grid">
        <div class="signature-category">
          <h3 class="category-title">Primate & Wildlife Signature Experiences</h3>
          <ul class="experience-list">
            <li>
              <span class="exp-title">1. The Great Primates Immersion Ritual</span>
              <span class="exp-overview">A deep-soul connection with the forest's majestic guardians. This transformative experience takes you into the misty realms of the Virunga volcanoes, where the ancient bond between humans and primates is felt in the silence of the forest. Guided by expert naturalists, you will learn to read the signs of the woods, understanding the complex social structures and emotional lives of these noble creatures.</span>
            </li>
            <li>
              <span class="exp-title">2. The Twin Lakes & Gorilla Horizon Expedition</span>
              <span class="exp-overview">Panoramic vistas meeting the wild spirit of the primates. This expedition combines the breathtaking beauty of the Burera and Ruhondo twin lakes with the rugged terrain of the gorilla highlands. You will traverse scenic ridges that offer sweeping views of the entire volcanic chain, learning about the geological forces that shaped this unique landscape. The journey is a feast for the senses, blending the tranquility of the water with the raw energy of the mountains.</span>
            </li>
            <li>
              <span class="exp-title">3. The Birdsong Discovery Walk</span>
              <span class="exp-overview">An auditory tapestry of rare species in their natural habitat. Immerse yourself in the complex melodies of the Virunga's avian residents, from the iridescent sunbirds to the elusive Albertine Rift endemics. This walk is a lesson in mindfulness, as you learn to distinguish individual calls and understand the seasonal rhythms of the forest. Guided by a specialist birder, you will discover the hidden stories behind each song.</span>
            </li>
          </ul>
        </div>

        <!-- Volcano & Adventure -->
        <div class="signature-category">
          <h3 class="category-title">Volcano & Adventure Experiences</h3>
          <ul class="experience-list">
            <li>
              <span class="exp-title">4. The Sunrise Panorama Experience</span>
              <span class="exp-overview">Witnessing the birth of light over the volcanic horizon. Begin your day with a pre-dawn hike to a high-altitude viewpoint, where you can watch the first rays of the sun illuminate the peaks of the Virunga chain. This experience is a quiet, meditative celebration of nature's beauty, offering a moment of profound peace and clarity as the landscape reveals itself.</span>
            </li>
            <li>
              <span class="exp-title">5. The Forest Canopy Immersion Walk</span>
              <span class="exp-overview">A bird's-eye perspective on the ancient afro-montane forest. Traverse high-altitude bridges and walkways that take you deep into the heart of the forest canopy, far above the ground. This experience offers a unique perspective on the vertical complexity of the woods, allowing you to observe life in the upper branches that is usually hidden from view.</span>
            </li>
            <li>
              <span class="exp-title">6. The Twin Lakes Scenic Journey</span>
              <span class="exp-overview">A serene voyage across the azure waters of the Twin Lakes. Board a traditional wooden canoe and glide across the mirror-like surfaces of Lakes Burera and Ruhondo, surrounded by the dramatic backdrop of the Virunga volcanoes. This peaceful journey offers a unique perspective on the landscape, with opportunities to observe local fishermen and discover the legends that shroud these ancient waters.</span>
            </li>
          </ul>
        </div>

        <!-- Culinary & Sensory -->
        <div class="signature-category">
          <h3 class="category-title">Culinary & Sensory Experiences</h3>
          <ul class="experience-list">
            <li>
              <span class="exp-title">7. The Farm-to-Table Harvest Experience</span>
              <span class="exp-overview">Connecting with the soil through seasonal bounty gathering. Join local farmers in the fertile volcanic fields to harvest the freshest seasonal produce, from sweet potatoes to vibrant greens. This experience is a celebration of the land's generosity and a lesson in the rhythms of agricultural life. The journey concludes with a meal prepared using the very ingredients you gathered.</span>
            </li>
            <li>
              <span class="exp-title">8. The Virunga Market-to-Table Journey</span>
              <span class="exp-overview">A sensory exploration from bustling stalls to your plate. Navigate the vibrant, colorful markets of Musanze with a local guide, learning how to select the finest local ingredients and spices. This journey is a dive into the heart of community life, where the aromas of fresh produce and the sounds of bartering create a lively atmosphere.</span>
            </li>
            <li>
              <span class="exp-title">9. The Volcano Honey Harvesting Experience</span>
              <span class="exp-overview">A rare encounter with the wild bees of the Virunga. Join local beekeepers in the high-altitude forests to learn the ancient art of harvesting organic honey from traditional hives. This experience offers a fascinating look at the vital role of bees in the volcanic ecosystem, concluding with a tasting of the pure, medicinal honey directly from the comb.</span>
            </li>
            <li>
              <span class="exp-title">10. The Coffee Roasting Masterclass</span>
              <span class="exp-overview">From bean to cup, learn the alchemy of Rwandan coffee. This in-depth workshop takes you through the entire process of coffee production, from harvesting and processing to the delicate art of roasting. You will have the chance to roast your own batch of beans, learning how time and temperature affect the final flavor profile.</span>
            </li>
            <li>
              <span class="exp-title">11. The Rwandan Tea & Honey Tasting Ritual</span>
              <span class="exp-overview">A delicate balance of mountain teas and wild forest honey. Discover the subtle elegance of Rwanda's high-altitude teas, paired with the complex flavors of honey gathered from the wild forests of the Virunga. This ritual is a study in harmony, as you explore how the different types of tea and honey complement each other.</span>
            </li>
            <li>
              <span class="exp-title">12. The Traditional Banana Brewing Experience</span>
              <span class="exp-overview">Discover the ancient secrets of Rwanda's social spirit. Join local experts in the traditional process of brewing banana beer, a staple of Rwandan social and ceremonial life for centuries. From the careful selection of the right bananas to the rhythmic mashing and fermentation process, you will be immersed in a craft that is as much about community as it is about the final drink.</span>
            </li>
            <li>
              <span class="exp-title">13. The Fireside Dessert & Herbal Tea Evening</span>
              <span class="exp-overview">Sweet conclusions and soothing infusions by the hearth. Wind down your day with a curated selection of traditional Rwandan desserts and hand-blended herbal teas, served by the warmth of a crackling fire. This evening ritual is designed for relaxation and reflection, offering a chance to share stories and connect with fellow travelers.</span>
            </li>
            <li>
              <span class="exp-title">14. The Ancestral Kitchen Ritual</span>
              <span class="exp-overview">Sacred techniques passed down through generations of hosts. Step into a traditional kitchen and learn the time-honored methods of food preparation that have sustained Rwandan families for centuries. This experience is a deep dive into the cultural heart of the home, where every tool and technique has a story to tell.</span>
            </li>
            <li>
              <span class="exp-title">15. The Royal Rwanda Table</span>
              <span class="exp-overview">A multi-course banquet celebrating the nation's culinary heritage. This grand dining experience is a showcase of the very best in Rwandan gastronomy, featuring a sequence of dishes that highlight the diversity of flavors and ingredients from across the country. Served in an elegant and intimate setting, the Royal Rwanda Table is a culinary journey that honors the traditions of the past.</span>
            </li>
          </ul>
        </div>

        <!-- Cultural & Community -->
        <div class="signature-category">
          <h3 class="category-title">Cultural & Community Immersions</h3>
          <ul class="experience-list">
            <li>
              <span class="exp-title">16. The Firelight Storytelling Salon</span>
              <span class="exp-overview">Myths and legends shared under the vast African sky. Gather around a roaring fire to hear the ancient stories and oral histories of the Rwandan people, told by elders and master storytellers. This experience is a window into the soul of the nation, where the wisdom of the past is passed down through the power of the spoken word.</span>
            </li>
            <li>
              <span class="exp-title">17. The Traditional Dance & Drum Ceremony</span>
              <span class="exp-overview">The heartbeat of Rwanda expressed through movement. Witness the raw power and grace of traditional Rwandan dance, accompanied by the hypnotic rhythms of the sacred drums. This ceremony is a vibrant celebration of the nation's cultural identity, where every movement and beat has a deep historical meaning.</span>
            </li>
            <li>
              <span class="exp-title">18. The Living Language Encounter</span>
              <span class="exp-overview">Learn the poetry and grace of Kinyarwanda. This interactive workshop introduces you to the basics of Rwanda's national language, focusing on the greetings, expressions, and cultural nuances that define Kinyarwanda. You will learn about the history and development of the language and its role as a powerful unifying force in the country.</span>
            </li>
            <li>
              <span class="exp-title">19. The Sacred Soil Farming Ritual</span>
              <span class="exp-overview">Understanding the deep bond between the land and its people. Participate in the traditional farming rituals that have sustained the people of the Virunga for generations. From the blessing of the seeds to the rhythmic songs of the harvest, you will be immersed in a world where agriculture is a sacred act of connection with the earth.</span>
            </li>
            <li>
              <span class="exp-title">20. The Botanical Wisdom Walk</span>
              <span class="exp-overview">Discovering the medicinal secrets of the forest's flora. Guided by a traditional healer or botanical expert, you will explore the forest to identify the various plants and herbs used in traditional Rwandan medicine. This walk is a journey into the ancient wisdom of the natural world, where every leaf and root has a potential healing property.</span>
            </li>
            <li>
              <span class="exp-title">21. The Virunga Oral Traditions Salon</span>
              <span class="exp-overview">Preserving history through the power of the spoken word. This intimate salon brings together elders and scholars to share the oral histories, proverbs, and traditional knowledge of the Virunga region. This is a rare opportunity to hear the stories that are not found in history books, gaining a deeper understanding of the cultural tapestry of the mountains.</span>
            </li>
            <li>
              <span class="exp-title">22. The Village Market Discovery</span>
              <span class="exp-overview">Immerse yourself in the vibrant pulse of local commerce. Navigate the bustling stalls of the Musanze village market, where the aromas of freshly roasted coffee mingle with the colors of handwoven baskets and fresh produce. This guided experience offers an authentic glimpse into daily life, where bargaining is an art form and every purchase supports local artisans.</span>
            </li>
            <li>
              <span class="exp-title">23. The Artisan Craft Encounter</span>
              <span class="exp-overview">Witness the creation of heirloom-quality handcrafts. Visit the workshops of skilled local artisans who continue centuries-old traditions in pottery, weaving, and woodcarving. Learn about the symbolism embedded in each design and try your hand at these crafts, creating your own piece of Rwandan heritage to take home.</span>
            </li>
            <li>
              <span class="exp-title">24. The Traditional Games Experience</span>
              <span class="exp-overview">Playful competitions connecting generations past and present. Join local communities in traditional Rwandan games that have been enjoyed for generations, from the strategic board game of Omweso to the athletic trials of herd boys. These games offer fun-filled insights into the values of wit, agility, and community spirit that define Rwandan culture.</span>
            </li>
          </ul>
        </div>

        <!-- Water & Scenic -->
        <div class="signature-category">
          <h3 class="category-title">Water & Scenic Experiences</h3>
          <ul class="experience-list">
            <li>
              <span class="exp-title">25. The Lakeside Sundowner Experience</span>
              <span class="exp-overview">Toasting the day's end by the tranquil shores of Ruhondo. Enjoy a private cocktail or mocktail session on the shores of Lake Ruhondo as the sky turns into a canvas of orange and pink. This experience is a celebration of the beauty of the Rwandan evening, offering a quiet and elegant space to reflect on your day.</span>
            </li>
            <li>
              <span class="exp-title">26. The Reflection by Water Ritual</span>
              <span class="exp-overview">Meditative moments inspired by the stillness of the lakes. Spend time in quiet reflection by the edge of the water, guided by the natural rhythms of the lakes. This ritual is designed to help you find inner peace and clarity, using the stillness of the water as a source of inspiration.</span>
            </li>
            <li>
              <span class="exp-title">27. The Island Picnic Adventure</span>
              <span class="exp-overview">A secluded feast on the hidden gems of the Twin Lakes. Escape to a private island for a curated picnic lunch, featuring local delicacies and refreshing beverages. Surrounded by the gentle lap of the water and the songs of lake birds, this experience offers a perfect blend of relaxation and discovery in one of the region's most picturesque settings.</span>
            </li>
          </ul>
        </div>

        <!-- Wellness & Reflection -->
        <div class="signature-category">
          <h3 class="category-title">Wellness & Reflection Experiences</h3>
          <ul class="experience-list">
            <li>
              <span class="exp-title">28. The Forest Breathing & Meditation Walk</span>
              <span class="exp-overview">Finding inner peace in the silence of the ancient woods. This guided walk focuses on the practice of forest bathing, where the goal is to fully immerse yourself in the sights, sounds, and smells of the woods. Through guided breathing exercises and silent meditation, you will learn to connect with the forest on a deeper level.</span>
            </li>
            <li>
              <span class="exp-title">29. The Night Under the Stars Experience</span>
              <span class="exp-overview">Cosmic wonders viewed from the slopes of the volcanoes. Spend an evening under the vast, unpolluted skies of the Virunga, guided by an expert astronomer or storyteller. You will learn about the constellations and myths that have guided travelers for centuries, gaining a new perspective on our place in the universe.</span>
            </li>
            <li>
              <span class="exp-title">30. The Fireside Reflection Rituals</span>
              <span class="exp-overview">Introspective evenings guided by the crackling fire. Join fellow travelers for a series of guided reflection sessions by the hearth, where the focus is on sharing experiences and finding meaning in your journey. These rituals are designed to foster connection and community, providing a supportive space for dialogue.</span>
            </li>
          </ul>
        </div>

        <!-- Family & Special -->
        <div class="signature-category">
          <h3 class="category-title">Family & Special Interest Experiences</h3>
          <ul class="experience-list">
            <li>
              <span class="exp-title">31. The Junior Explorer Discovery Experience</span>
              <span class="exp-overview">Inspiring the next generation of conservationists. This interactive program is designed specifically for younger travelers, focusing on the wonders of the natural world and the importance of conservation. Through guided nature walks and animal tracking workshops, children will gain a deeper understanding of the environment.</span>
            </li>
            <li>
              <span class="exp-title">32. The Virunga Memory Book Journey</span>
              <span class="exp-overview">Documenting your personal odyssey in a handcrafted journal. This creative experience invites you to create a personalized record of your travels in the Virunga, using traditional bookbinding techniques and locally sourced materials. Guided by an artisan, you will learn how to document your journey through writing and sketching.</span>
            </li>
            <li>
              <span class="exp-title">33. The Family Cooking Adventure</span>
              <span class="exp-overview">Creating delicious memories with loved ones in the kitchen. Bring the whole family together for a fun and interactive cooking experience where you'll learn to prepare traditional Rwandan dishes using fresh, local ingredients. From rolling the perfect brochette to mastering the art of plantain fritters, this hands-on adventure is designed to delight every age and create lasting culinary memories.</span>
            </li>
            <li>
              <span class="exp-title">34. The Traditional Games Experience</span>
              <span class="exp-overview">Playful competitions connecting generations past and present. Join local communities in traditional Rwandan games that have been enjoyed for generations, from the strategic board game of Omweso to the athletic trials of herd boys. These games offer fun-filled insights into the values of wit, agility, and community spirit that define Rwandan culture.</span>
            </li>
          </ul>
        </div>

        <!-- Private & Celebratory -->
        <div class="signature-category">
          <h3 class="category-title">Private & Celebratory Experiences</h3>
          <ul class="experience-list">
            <li>
              <span class="exp-title">35. The Romantic Fireside Evening</span>
              <span class="exp-overview">An intimate evening of warmth and connection. Curl up beside a crackling fire on the slopes of the Virunga volcanoes with your loved one, enjoying a curated selection of fine Rwandan wines and artisan chocolates. The evening is enhanced by the soft glow of lanterns and the whisper of the forest, creating an unforgettably romantic atmosphere.</span>
            </li>
            <li>
              <span class="exp-title">36. The Private Celebration Dinner</span>
              <span class="exp-overview">A bespoke dining experience for life's milestone moments. Whether marking an anniversary, birthday, or special achievement, this private dinner is crafted exclusively for you and your guests. Set in a stunning location with panoramic volcano views, the evening features a customized multi-course menu celebrating the finest Rwandan flavors and the occasion being honored.</span>
            </li>
            <li>
              <span class="exp-title">37. The Anniversary Storytelling Dinner</span>
              <span class="exp-overview">A deep-dive into your shared journey amidst the magic of the Virunga. This intimate dinner includes a personalized storytelling session where we weave your own milestones into the rich tapestry of local legends. Surrounded by the scent of wild jasmine and the soft flicker of candlelight, you'll celebrate your anniversary with a menu designed to evoke your most cherished memories.</span>
            </li>
            <li>
              <span class="exp-title">38. The Honeymoon Welcome Ritual</span>
              <span class="exp-overview">Begin your forever in the heart of Africa. Newlyweds are greeted with a romantic welcome ritual featuring rose petal arrangements, sparkling wine, and a candlelit dinner under the African stars. This exclusive experience includes a couples' massage using local botanicals and a private sunrise breakfast overlooking the lakes—a fitting start to a life of adventure together.</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="activity-gallery" id="gallerySection">
    <div class="section-container">
      <header class="signature-header">
        <span class="signature-label">Virunga Homestay</span>
        <h2 class="signature-title">Our Experiences Recap</h2>
        <p class="signature-desc">
          Take a visual journey through the moments that define our boutique stay. From misty volcano mornings to the warmth of village encounters, these are the memories waiting for you.
        </p>
      </header>
      
      <div id="galleryLoader" style="text-align: center; padding: 40px;">
        <i class="fa-solid fa-spinner fa-spin fa-2xl" style="color: var(--color-primary);"></i>
      </div>

      <div class="gallery-wrap" style="display: none;"> 
        <div class="gallery" id="gallery"> 
          <?php
          $galleryDir = __DIR__ . '/../img/gallery/';
          $images = glob($galleryDir . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
          
          if ($images) {
              foreach ($images as $index => $imagePath) {
                  $fileName = basename($imagePath);
                  $label = ucwords(str_replace(['-', '_', '.jpg', '.jpeg', '.png', '.webp'], [' ', ' ', '', '', '', ''], $fileName));
                  $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                  $activeClass = ($index === 0) ? 'active' : '';
                  
                  echo '
                  <div class="slide ' . $activeClass . '" data-label="' . htmlspecialchars($label) . '"> 
                    <img data-src="./img/gallery/' . htmlspecialchars($fileName) . '" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="' . htmlspecialchars($label) . '" class="lazy-slide-img" /> 
                    <div class="slide-overlay"></div> 
                    <div class="slide-num"><span>' . $num . '</span></div> ';
                  
                  if ($index === 0) {
                      echo '
                    <div class="progress-ring"> 
                      <svg viewBox="0 0 36 36" width="36" height="36"> 
                        <circle class="ring-bg" cx="18" cy="18" r="15" /> 
                        <circle class="ring-fill" id="ring" cx="18" cy="18" r="15" /> 
                      </svg> 
                    </div> ';
                  }
                  
                  echo '</div>';
              }
          }
          ?>
        </div> 
        <!-- /.gallery --> 
   
        <!-- Dot indicators --> 
        <div class="dots" id="dots"></div> 
   
        <!-- Controls --> 
        <div class="controls"> 
          <button class="ctrl-btn" id="prevBtn"> 
            <i class="fa-solid fa-chevron-left"></i> Prev 
          </button> 
          <button class="ctrl-btn" id="pauseBtn"> 
            <i class="fa-solid fa-pause" id="pauseIcon"></i> 
            <span id="pauseLabel">Pause</span> 
          </button> 
          <button class="ctrl-btn" id="nextBtn"> 
            Next <i class="fa-solid fa-chevron-right"></i> 
          </button> 
        </div> 
      </div> 
    </div>
  </section>

</main>

<?php include 'includes/footer.php'; ?>

