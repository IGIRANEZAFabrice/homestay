-- SQL Script for About Us Page Content Management (For updating another database)

-- Check if tables exist and create them if they don't

-- Table for About Page Sections
CREATE TABLE IF NOT EXISTS `about_sections` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `section_name` VARCHAR(50) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` TEXT,
  `content` TEXT,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `display_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for About Page Features
CREATE TABLE IF NOT EXISTS `about_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for About Page Guidelines
CREATE TABLE IF NOT EXISTS `about_guidelines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default data for about_sections if not exists
INSERT IGNORE INTO `about_sections` (`section_name`, `title`, `subtitle`, `content`, `image_path`, `display_order`, `is_active`) VALUES
('hero', 'About Us', 'About Virunga Homestay', NULL, NULL, 1, 1),
('mission', 'Our Mission', 'At Virunga Homestay, we are dedicated to providing authentic, immersive experiences that connect travelers with the heart of Rwanda.', 'Virunga Homestay offers travellers an authentic cultural immersion beyond ordinary tourism. Guests engage in traditional ceremonies, learn age-old Rwandan recipes, and share meaningful conversations that deepen their understanding of Rwandan life and values.\n\nEach homestay is carefully selected to provide both modern comfort and a genuine glimpse into Rwanda\'s vibrant heritage, whether in peaceful villages or lively towns. We prioritise safety and warm hospitality to ensure guests feel completely at home.\n\nOur mission is to promote sustainable tourism by empowering local communities, training women and youth in hospitality and tourism skills, and creating livelihood opportunities that reduce rural-to-urban migration.', '../assets/images/about/mission.jpg', 2, 1),
('why_choose', 'Why Choose Virunga Homestay?', 'Virunga Homestay offers a unique fusion of authentic local hospitality and professional tourist services in the heart of Musanze, the gateway to the majestic Virunga Massif. More than just accommodation, it serves as a trusted Tourist Information Centre, staffed by accredited bilingual specialists who provide tailored, reliable guidance to travelers navigating the rich cultural and natural landscapes of Rwanda, Uganda, and the Democratic Republic of Congo.', 'In essence, Virunga Homestay represents an ideal choice for travelers seeking an authentic, professionally supported, and culturally rich experience in Musanze. It combines heartfelt hospitality with expert tourist assistance, ensuring every visit to the Virunga Massif is memorable, meaningful, and responsibly engaged.', NULL, 3, 1),
('responsible_guest', 'Be a Responsible Guest', 'Staying at Virunga Homestay means becoming part of a Rwandan home and community. Here\'s how to make the most of your authentic experience.', NULL, NULL, 4, 1),
('more_than_accommodation', 'More Than Just Accommodation', 'Virunga Homestay is more than just a place to stay—it\'s a gateway to authentic experiences and meaningful connections.', NULL, NULL, 5, 1),
('community_connection', 'Community Connection', NULL, 'At Virunga Homestay, we believe that true travel experiences come from meaningful connections with local communities. Our hosts are not just accommodation providers—they are cultural ambassadors, eager to share their knowledge, traditions, and way of life with you.\n\nThrough community-based tourism initiatives, we create opportunities for cultural exchange that benefit both travelers and locals. From participating in traditional cooking classes to joining community conservation efforts, your stay contributes to the sustainable development of the region.', 'assets/images/about/community-connection.jpg', 6, 1),
('sustainable_tourism', 'Sustainable Tourism', NULL, 'We are committed to responsible tourism practices that minimize environmental impact and maximize positive contributions to local communities. From employing local staff to sourcing ingredients from nearby farms, we strive to create a sustainable tourism model that preserves the natural and cultural heritage of the Virunga region.\n\nBy choosing Virunga Homestay, you are supporting our efforts to promote sustainable tourism and contribute to the conservation of the magnificent Virunga ecosystem. Together, we can ensure that future generations can continue to experience the beauty and wonder of this unique region.', 'assets/images/about/sustainable-tourism.jpg', 7, 1),
('cta', 'Ready for Your Authentic Experience?', 'Join us for an unforgettable journey into the heart of Rwandan culture and hospitality.', NULL, NULL, 8, 1);

-- Insert default data for about_features if not exists
INSERT IGNORE INTO `about_features` (`icon`, `title`, `description`, `display_order`) VALUES
('🏠', 'Authenticity and Warmth', 'Unlike conventional hotels, Virunga Homestay is integrated within a local family residence, offering guests a welcoming, informal atmosphere that fosters genuine human connection. This intimate setting creates a sense of belonging and comfort that travelers often seek but seldom find in larger, impersonal establishments.', 1),
('❤️', 'Personalized Hospitality', 'Hosts are deeply committed to personalized guest care, extending hospitality with thoughtful gestures such as lending bicycles to explore scenic routes or preparing nourishing home-cooked meals. This attentiveness reflects a hospitality philosophy centered on genuine kindness and cultural immersion.', 2),
('🗺️', 'Insider Knowledge', 'Gain unparalleled access to insider knowledge about Musanze and the Virunga Massif region. Hosts share hidden gems, cultural narratives, and authentic experiences not found in guidebooks, enabling visitors to connect more meaningfully with local traditions, landscapes, and communities.', 3),
('🍽️', 'Culinary Journey', 'Enjoy freshly prepared, traditional Rwandan dishes made from local ingredients, often with the opportunity to participate in cooking and learn recipes passed down through generations. This culinary immersion enriches the cultural experience and creates lasting memories through taste and tradition.', 4),
('🤝', 'Community Connections', 'The homestay environment encourages social engagement among guests and hosts alike, fostering a warm community atmosphere. Travelers often leave with enduring friendships and a deeper appreciation of the cultural tapestry that defines the Virunga region.', 5),
('🌿', 'Tranquil Natural Setting', 'Located away from urban noise, Virunga Homestay provides a peaceful retreat ideal for rest and reflection after days spent exploring the surrounding wilderness. The limited number of rooms ensures a quiet ambiance and personalized attention.', 6),
('🛡️', 'Safety and Trust', 'Guest safety and wellbeing are paramount at Virunga Homestay. The hosts\' strong community reputation and integrity assure travelers a secure and respectful environment throughout their stay.', 7),
('🌍', 'Responsible Tourism', 'Selecting Virunga Homestay contributes directly to the economic resilience of local families surrounding the protected Virunga ecosystem. This sustainable livelihood support encourages cultural preservation and community development, aligning travelers\' enjoyment with positive social impact.', 8);

-- Insert default data for about_guidelines if not exists
INSERT IGNORE INTO `about_guidelines` (`title`, `content`, `display_order`) VALUES
('Maintain Hygiene', 'Unlike hotels, Virunga Homestays have dedicated family members, not housekeeping staff. Please keep your room and common areas clean at all times and dispose of rubbish responsibly in the provided bins.', 1),
('Follow House Rules', 'Your cooperation ensures comfort for everyone:\n- Inform your host about late returns or overnight stays\n- Give advance notice if eating outside\n- Ask permission before smoking or consuming alcohol\n- Get approval for parties or gatherings', 2),
('Manage Expectations', 'Meals follow a fixed daily menu rather than hotel-style selection. Discuss allergies or dietary needs with your host ahead of time. Note that room service isn\'t available as hosts personally care for guests.', 3),
('Use Resources Wisely', 'Respect the amenities provided. Turn off taps, lights, and appliances when not in use to avoid wastage and maintain sustainability within the homestay environment.', 4),
('Be Considerate', 'Your behavior reflects on your hosts. Avoid activities that may disturb neighbors, and if issues arise, seek your host\'s assistance in resolving them calmly and respectfully.', 5),
('Embrace the Experience', 'You\'ll experience genuine hospitality and daily life. Maintain dignity throughout your stay while enjoying this unique opportunity to connect with Rwandan culture and community.', 6);