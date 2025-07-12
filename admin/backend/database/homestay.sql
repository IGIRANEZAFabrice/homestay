-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2025 at 12:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homestay`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us_page`
--

CREATE TABLE `about_us_page` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `image` varchar(500) NOT NULL,
  `mission` text NOT NULL,
  `vision` text NOT NULL,
  `values` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(500) NOT NULL,
  `display_order` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `status` enum('active','inactive','draft') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `title`, `content`, `duration`, `price`, `image`, `display_order`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(4, 'A hands-on eco-organic farming experience', 'Get your hands dirty in the lush gardens of Virunga homestay as you experience the wonders of organic farming. Join us in planting, harvesting, and learning sustainable practices that maintain a healthy ecosystem. This hands-on activity offers a deeper appreciation for how the land is nurtured and provides insights into traditional farming methods that have been passed down through generations.', NULL, NULL, '1750842763_11.jpg', 1, 1, 'active', '2025-06-25 09:12:43', '2025-06-25 09:12:43'),
(5, 'Eco friendly cooking class with a local twist', 'Discover the rich flavors of Rwandan cuisine in a hands-on cooking class. You’ll learn to prepare local dishes such as ibihaza (pumpkin stew) and isombe (cassava leaves). Our expert chefs will guide you through the cooking process, providing tips on the use of local spices and ingredients. Afterward, you’ll enjoy the delicious locally sourced meal you have created.', NULL, NULL, '1750842921_12.jpg', 1, 1, 'active', '2025-06-25 09:15:21', '2025-06-25 09:15:21'),
(6, 'Eco friendly golden monkeys trekking', 'Venture into the lush bamboo forests of volcanoes national park to meet the golden monkeys, one of Rwanda’s most iconic and endangered species. Led by expert guides, you’ll embark on a trek to observe these playful primates in their natural habitat. This unforgettable experience provides an opportunity to learn about their behavior and conservation efforts aimed at protecting them.', NULL, NULL, '1750842963_13.jpg', 1, 1, 'active', '2025-06-25 09:16:03', '2025-06-25 09:16:03'),
(7, ' A majestic gorilla friendly trekking', 'One of the most sought-after wildlife experiences in the world, gorilla trekking organized at Virunga homestay offers you a chance to observe mountain gorillas up close in their natural environment. Accompanied by knowledgeable guides, trek through dense forests and rugged terrain to meet these incredible creatures. Your encounter with the gorillas will leave you with memories that last a lifetime and a deeper understanding of conservation efforts in the region.', NULL, NULL, '1750843016_11.jpg', 1, 1, 'active', '2025-06-25 09:16:56', '2025-06-25 09:16:56'),
(8, 'Eco Volcano trekking adventure', 'For those seeking adventure, hiking one of the volcanoes such as mount Karisimbi, mount Sabyinyo or mount Bisoke offers stunning views and a rewarding challenge. Trek through rainforests, volcanic craters, and high-altitude terrains while taking in breathtaking panoramas of the surrounding landscapes. It’s the perfect activity for nature lovers and adrenaline seekers alike.', NULL, NULL, '1750843073_12.jpg', 1, 1, 'active', '2025-06-25 09:17:53', '2025-06-25 09:17:53'),
(9, 'Eco tea and eco coffee tour', 'Rwanda is renowned for its high-quality tea and coffee, and at Virunga homestay, you can visit local plantations to see how these crops are cultivated and processed. Participate in a tasting session to sample the rich flavors of Rwandan tea and coffee, and learn about the impact of these industries on the local economy.', NULL, NULL, 'uploads/activities/1752273769_1026664.png', 1, 1, 'active', '2025-06-25 09:22:48', '2025-07-11 22:42:49'),
(10, 'Witness live painting inspired by nature', 'Engage yourself in the creative world of local artists with live painting sessions at Virunga homestay. Watch as artists bring their visions to life on canvas, and learn about the inspirations behind their work. You may even have the chance to try your hand at painting under the guidance of the artist. This activity is highly recommended to children.', NULL, NULL, 'uploads/activities/1752273760_7937129.png', 1, 1, 'active', '2025-06-25 09:39:12', '2025-07-11 22:42:40'),
(11, 'UMURAVA WOWE', 'musinga fr fr frf rf rf rr r frfr rfrf rfrfr', NULL, NULL, 'uploads/activities/1751047398_5547071.webp', 2, 1, 'active', '2025-06-27 18:03:18', '2025-06-27 18:04:28');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_activity_log`
--

INSERT INTO `admin_activity_log` (`id`, `user_id`, `action`, `description`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'logout', 'User logged out successfully', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-27 04:29:54'),
(2, 1, 'logout', 'User logged out successfully', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-27 05:21:03'),
(3, 1, 'logout', 'User logged out successfully', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-27 05:56:51'),
(4, 1, 'logout', 'User logged out successfully', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-27 06:32:09'),
(5, 1, 'update_service', 'Updated service: Umwana Muto', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 05:07:14'),
(6, 1, 'update_service', 'Updated service: Umwana Muto', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 05:07:36'),
(7, 1, 'delete_service', 'Deleted service: ', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 05:08:03'),
(8, 1, 'view_message', 'Viewed message from Uwimana Gabriel', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 06:34:09'),
(9, 1, 'view_message', 'Viewed message from Uwimana Gabriel', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 06:36:04'),
(10, 1, 'view_message', 'Viewed message from Uwimana Gabriel', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 06:38:00'),
(11, 1, 'view_message', 'Viewed message from Uwimana Gabriel', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 06:45:37'),
(12, 1, 'delete_message', 'Deleted message from Uwimana Gabriel (jessymusinga@gmail.com)', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 06:45:47'),
(13, 1, 'create_event', 'Created event: jdbkcs.', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 07:27:20'),
(14, 1, 'delete_event', 'Deleted event: jdbkcs.', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-28 07:27:27'),
(15, 1, 'create_activity', 'Created activity: dgjcvsihdkj', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-29 22:24:48'),
(16, 1, 'create_blog', 'Created blog post: dgchvdhivbd dsuhcvsdukjhc dsvckdujvycdus chdjsvckdjcvd dvsukc', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-29 22:26:40'),
(17, 1, 'update_hero_images_order', 'Updated hero images display order', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-29 23:23:49'),
(18, 1, 'create_activity', 'Created activity: fkldvnfb', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-02 11:58:16'),
(19, 1, 'delete_activity', 'Deleted activity: fkldvnfb', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-02 11:58:26'),
(20, 1, 'update_activity', 'Updated activity: umuhungu', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-02 11:58:35'),
(21, 1, 'delete_activity', 'Deleted activity: umuhungu', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-02 11:58:39'),
(22, 1, 'update_blog', 'Updated blog post: umuhungu', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-02 11:58:56'),
(23, 1, 'create_blog', 'Created blog post: djiekvfzdbvfdjhlvldfuivf dhsfieufvbdif', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-02 11:59:19'),
(24, 1, 'delete_blog', 'Deleted blog post: djiekvfzdbvfdjhlvldfuivf dhsfieufvbdif', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-02 11:59:33'),
(25, 1, 'update_blog', 'Updated blog post: umuhungu', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-02 11:59:41'),
(26, 1, 'update_event', 'Updated event: KWITA IZINA INGAGI', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-11 23:52:31'),
(27, 1, 'update_blog', 'Updated blog post: umuhungu', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-12 00:41:56'),
(28, 1, 'update_activity', 'Updated activity: Witness live painting inspired by nature', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-12 00:42:40'),
(29, 1, 'update_activity', 'Updated activity: Eco tea and eco coffee tour', NULL, NULL, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-12 00:42:49');

-- --------------------------------------------------------

--
-- Table structure for table `admin_api_log`
--

CREATE TABLE `admin_api_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `endpoint` varchar(255) NOT NULL,
  `method` varchar(10) NOT NULL,
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_data`)),
  `response_code` int(11) DEFAULT NULL,
  `response_time` decimal(10,3) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_attempts`
--

CREATE TABLE `admin_login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `message` varchar(255) DEFAULT NULL,
  `attempted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_login_attempts`
--

INSERT INTO `admin_login_attempts` (`id`, `username`, `ip_address`, `user_agent`, `success`, `message`, `attempted_at`) VALUES
(6, 'admin', '::1', 'curl/8.12.1', 0, 'Invalid password', '2025-06-27 04:00:33'),
(7, 'admin', '::1', 'curl/8.12.1', 1, 'Login successful', '2025-06-27 04:01:49'),
(8, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1, 'Login successful', '2025-06-27 04:27:32'),
(9, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1, 'Login successful', '2025-06-27 04:30:03'),
(10, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1, 'Login successful', '2025-06-27 05:21:20'),
(11, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1, 'Login successful', '2025-06-27 05:56:20'),
(12, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1, 'Login successful', '2025-06-27 06:31:19'),
(13, 'admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 1, 'Login successful', '2025-06-27 09:16:28');

-- --------------------------------------------------------

--
-- Table structure for table `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id` int(11) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_permissions`
--

INSERT INTO `admin_permissions` (`id`, `permission_name`, `description`, `category`, `created_at`, `updated_at`) VALUES
(1, 'manage_activities', 'Create, read, update, and delete activities', 'content', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(2, 'manage_blogs', 'Create, read, update, and delete blog posts', 'content', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(3, 'manage_cars', 'Create, read, update, and delete rental cars', 'content', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(4, 'manage_events', 'Create, read, update, and delete events', 'content', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(5, 'manage_rooms', 'Create, read, update, and delete rooms', 'content', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(6, 'manage_services', 'Create, read, update, and delete services', 'content', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(7, 'manage_reviews', 'Moderate and manage customer reviews', 'content', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(8, 'manage_hero_images', 'Manage homepage hero images', 'content', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(9, 'manage_homepage_about', 'Manage homepage about section', 'content', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(10, 'manage_messages', 'View and respond to contact messages', 'communication', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(11, 'view_dashboard', 'Access admin dashboard', 'system', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(12, 'manage_users', 'Create, read, update, and delete admin users', 'system', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(13, 'manage_settings', 'Modify application settings', 'system', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(14, 'view_logs', 'View system and activity logs', 'system', '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(15, 'manage_permissions', 'Grant and revoke user permissions', 'system', '2025-06-27 03:56:49', '2025-06-27 03:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `admin_security_log`
--

CREATE TABLE `admin_security_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_sessions`
--

CREATE TABLE `admin_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_sessions`
--

INSERT INTO `admin_sessions` (`id`, `user_id`, `token_hash`, `expires_at`, `ip_address`, `user_agent`, `last_activity`, `created_at`) VALUES
(1, 1, 'ec6f8f3fa8f6fa8c70352375cefc702916a47632e20e9daad3475469f302414f', '2025-06-28 04:01:49', '::1', 'curl/8.12.1', '2025-06-27 04:01:49', '2025-06-27 04:01:49'),
(4, 1, 'd42890aed077b71d81638f001ae39b33b68f1736173918921b85f0170d569662', '2025-06-28 05:21:20', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-27 05:21:20', '2025-06-27 05:21:20'),
(7, 1, '03e3c73f013ac89b1235ee81974409b018b26be1be68f992bce4d32b118542e3', '2025-06-28 09:16:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-06-27 09:16:28', '2025-06-27 09:16:28');

-- --------------------------------------------------------

--
-- Table structure for table `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_settings`
--

INSERT INTO `admin_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `category`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Virunga Homestay', 'string', 'Website name', 'general', 1, '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(2, 'site_description', 'Experience the beauty of Virunga National Park', 'string', 'Website description', 'general', 1, '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(3, 'contact_email', 'info@virungahomestay.com', 'string', 'Main contact email', 'contact', 1, '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(4, 'contact_phone', '+250 788 123 456', 'string', 'Main contact phone', 'contact', 1, '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(5, 'admin_email', 'admin@virungahomestay.com', 'string', 'Admin notification email', 'system', 0, '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(6, 'max_upload_size', '10485760', 'number', 'Maximum file upload size in bytes (10MB)', 'system', 0, '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(7, 'session_timeout', '86400', 'number', 'Session timeout in seconds (24 hours)', 'system', 0, '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(8, 'enable_registration', 'false', 'boolean', 'Allow new user registration', 'system', 0, '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(9, 'maintenance_mode', 'false', 'boolean', 'Enable maintenance mode', 'system', 0, '2025-06-27 03:56:49', '2025-06-27 03:56:49'),
(10, 'backup_frequency', '24', 'number', 'Database backup frequency in hours', 'system', 0, '2025-06-27 03:56:49', '2025-06-27 03:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$mt.pUZIGJ/6/rKBhYJ8xw.FZXTR779wfZc71oK8kOj4rjuboDZkee', 'admin@virungahomestay.com', 'System Administrator', 'super_admin', 'active', '2025-07-12 00:15:50', '2025-06-27 03:56:48', '2025-07-12 00:15:50');

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_permissions`
--

CREATE TABLE `admin_user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted` tinyint(1) NOT NULL DEFAULT 1,
  `granted_by` int(11) DEFAULT NULL,
  `granted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_user_permissions`
--

INSERT INTO `admin_user_permissions` (`id`, `user_id`, `permission_id`, `granted`, `granted_by`, `granted_at`) VALUES
(1, 1, 10, 1, NULL, '2025-06-27 03:56:49'),
(2, 1, 1, 1, NULL, '2025-06-27 03:56:49'),
(3, 1, 2, 1, NULL, '2025-06-27 03:56:49'),
(4, 1, 3, 1, NULL, '2025-06-27 03:56:49'),
(5, 1, 4, 1, NULL, '2025-06-27 03:56:49'),
(6, 1, 5, 1, NULL, '2025-06-27 03:56:49'),
(7, 1, 6, 1, NULL, '2025-06-27 03:56:49'),
(8, 1, 7, 1, NULL, '2025-06-27 03:56:49'),
(9, 1, 8, 1, NULL, '2025-06-27 03:56:49'),
(10, 1, 9, 1, NULL, '2025-06-27 03:56:49'),
(11, 1, 11, 1, NULL, '2025-06-27 03:56:49'),
(12, 1, 12, 1, NULL, '2025-06-27 03:56:49'),
(13, 1, 13, 1, NULL, '2025-06-27 03:56:49'),
(14, 1, 14, 1, NULL, '2025-06-27 03:56:49'),
(15, 1, 15, 1, NULL, '2025-06-27 03:56:49');

-- --------------------------------------------------------

--
-- Stand-in structure for view `admin_user_permissions_view`
-- (See below for the actual view)
--
CREATE TABLE `admin_user_permissions_view` (
`user_id` int(11)
,`username` varchar(100)
,`email` varchar(255)
,`full_name` varchar(255)
,`role` enum('super_admin','admin','moderator')
,`permission_name` varchar(100)
,`permission_description` text
,`permission_category` varchar(50)
,`granted` tinyint(1)
,`granted_at` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(500) NOT NULL,
  `content` longtext NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `status` enum('published','draft','archived') DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `image`, `content`, `slug`, `is_published`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(2, 'Hands Behind the Journey: The Human Face of Tourism in the Virunga Massif', 'uploads/blogs/1750850997_ec46ce1a.jpg', '<p>For every successful trek into the Virunga forests, there is a network of local support that makes the experience possible. Guides and porters many of whom come from neighboring communities are indispensable companions on these journeys. Their responsibilities go beyond navigating dense vegetation and managing slippery ascents. They are interpreters of the forest’s secrets, offering stories, ecological context, and insights that deepen visitors’ understanding of the region.These roles are often lifelines for families. In rural areas where employment is limited, working in tourism provides not just wages, but stability. The income sustains households, pays school fees, covers healthcare costs, and supports farming activities. For many, it is a dignified profession, built not only on knowledge of the terrain but on a growing pride in local heritage and environmental stewardship.</p><img src=\"uploads/blogs/1750850997_2bb80939.jpg\" alt=\"Blog image\" style=\"max-width: 100%; height: auto; margin: 20px 0;\">', '-ands-ehind-the-ourney-he-uman-ace-of-ourism-in-the-irunga-assif', 1, 'published', '2025-06-25 11:29:57', '2025-06-25 11:29:57', '2025-06-27 01:56:49'),
(3, 'A Visit to Handspun Hope “Umuzabibu Mwiza” in Musanze: Weaving Dignity and Empowerment with Virunga Ecotours', 'uploads/blogs/1750851289_80c1042b.jpg', '<p>Umuzabibu Mwiza is a faith based, non profit organization dedicated to empowering some of Rwanda’s most vulnerable women widows, survivors of trauma, and those once excluded from opportunity. Through meaningful employment in traditional textile arts, alongside spiritual guidance and emotional support, the organization offers a path to dignity and self sufficiency for over 209 women.</p><img src=\"uploads/blogs/1750851289_f329aebb.jpg\" alt=\"Blog image\" style=\"max-width: 100%; height: auto; margin: 20px 0;\">', '-isit-to-andspun-ope-muzabibu-wiza-in-usanze-eaving-ignity-and-mpowerment-with-irunga-cotours', 1, 'published', '2025-06-25 11:34:49', '2025-06-25 11:34:49', '2025-06-27 01:56:49'),
(17, 'umuhungu', 'uploads/blogs/1751228800_4172472.png', '<p>Heeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee heeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee heeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee</p>\r\n<h1>hdcvidyhcvikdhcsk</h1>\r\n<p><img src=\"../../../uploads/blog-content/1751228741_9450903.png\" alt=\"\" width=\"619\" height=\"348\"></p>', 'dgchvdhivbd-dsuhcvsdukjhc-dsvckdujvycdus-chdjsvckdjcvd-dvsukc', 1, 'draft', '2025-07-02 09:59:41', '2025-06-29 20:26:40', '2025-07-11 22:41:56');

-- --------------------------------------------------------

--
-- Table structure for table `blog_content_blocks`
--

CREATE TABLE `blog_content_blocks` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `content_type` enum('text','image','heading','quote') NOT NULL,
  `content_text` longtext DEFAULT NULL,
  `content_image` varchar(500) DEFAULT NULL,
  `image_alt_text` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_content_blocks`
--

INSERT INTO `blog_content_blocks` (`id`, `blog_id`, `content_type`, `content_text`, `content_image`, `image_alt_text`, `display_order`, `created_at`, `updated_at`) VALUES
(2, 2, 'text', 'For every successful trek into the Virunga forests, there is a network of local support that makes the experience possible. Guides and porters many of whom come from neighboring communities are indispensable companions on these journeys. Their responsibilities go beyond navigating dense vegetation and managing slippery ascents. They are interpreters of the forest’s secrets, offering stories, ecological context, and insights that deepen visitors’ understanding of the region.These roles are often lifelines for families. In rural areas where employment is limited, working in tourism provides not just wages, but stability. The income sustains households, pays school fees, covers healthcare costs, and supports farming activities. For many, it is a dignified profession, built not only on knowledge of the terrain but on a growing pride in local heritage and environmental stewardship.', NULL, NULL, 1, '2025-06-25 11:29:57', '2025-06-25 11:29:57'),
(3, 2, 'image', NULL, 'uploads/blogs/1750850997_2bb80939.jpg', NULL, 2, '2025-06-25 11:29:57', '2025-06-25 11:29:57'),
(4, 3, 'text', 'Umuzabibu Mwiza is a faith based, non profit organization dedicated to empowering some of Rwanda’s most vulnerable women widows, survivors of trauma, and those once excluded from opportunity. Through meaningful employment in traditional textile arts, alongside spiritual guidance and emotional support, the organization offers a path to dignity and self sufficiency for over 209 women.', NULL, NULL, 1, '2025-06-25 11:34:49', '2025-06-25 11:34:49'),
(5, 3, 'image', NULL, 'uploads/blogs/1750851289_f329aebb.jpg', NULL, 2, '2025-06-25 11:34:49', '2025-06-25 11:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `make` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `transmission` varchar(20) NOT NULL,
  `fuel_type` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `features` text DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_available` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `name`, `make`, `model`, `type`, `transmission`, `fuel_type`, `price`, `image`, `features`, `badge`, `status`, `created_at`, `is_available`, `updated_at`) VALUES
(4, 'land cruiser V8', NULL, NULL, '4x4', 'manual', 'petrol', 100.00, 'uploads/cars/1750852673_04df2d19-3845-4f81-abee-07e77e81c2ea.jpg', '[\"ac\",\"audio\",\"wifi\",\"bluetooth\",\"climate\",\"premium\"]', 'popular', 'active', '2025-06-25 11:57:53', 1, '2025-07-11 22:17:35'),
(5, 'tourism car', NULL, NULL, 'luxury', 'manual', 'diesel', 250.00, 'uploads/cars/1750852732_e86d631f-53e3-4400-9218-126eb094db55.jpg', '[]', '', 'active', '2025-06-25 11:58:52', 1, '2025-07-11 22:17:35');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT 'General Inquiry',
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `event_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `status` enum('active','inactive','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `image`, `description`, `event_date`, `location`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(5, 'KWITA IZINA INGAGI', 'uploads/events/1752270751_2974449.png', 'Kwita Izina is Rwanda\'s annual gorilla naming ceremony, inspired by a traditional Rwandan baby naming ceremony. It\'s a celebration of the country\'s commitment to conservation and sustainable tourism, specifically focused on protecting the endangered mountain gorilla population. The event involves naming newly-born gorillas and is a significant part of Rwanda\'s efforts to raise awareness and support for gorilla conservation.', '2025-09-30 09:10:00', 'Kigali, Rwanda', 1, 'active', '2025-06-25 10:51:34', '2025-07-11 21:52:31');

-- --------------------------------------------------------

--
-- Table structure for table `hero_images`
--

CREATE TABLE `hero_images` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `paragraph` text NOT NULL,
  `image` varchar(500) NOT NULL,
  `display_order` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_images`
--

INSERT INTO `hero_images` (`id`, `title`, `paragraph`, `image`, `display_order`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(3, 'first(one)', 'one', 'uploads/hero/1750842041_6.jpg', 1, 1, 'active', '2025-06-23 22:21:17', '2025-06-29 21:23:49'),
(4, 'two', 'two', 'uploads/hero/1750842048_5.jpg', 2, 1, 'active', '2025-06-23 22:21:17', '2025-06-29 21:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `homepage_about`
--

CREATE TABLE `homepage_about` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homepage_about`
--

INSERT INTO `homepage_about` (`id`, `title`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Your home away from', 'Virunga Homestay, situated in the heart of Musanze, Rwanda, presents a remarkable opportunity for travelers to connect with the area\'s breathtaking scenery and vibrant culture. The homestay offers comfortable and inviting rooms that blend traditional Rwandan elements with modern comforts, creating a restful environment for guests. Meals at Virunga are a treat, featuring authentic Rwandan cuisine made from fresh, locally sourced ingredients, with options for guests to engage in cooking sessions and learn about local culinary practices. A variety of activities await, including gorilla trekking in Volcanoes National Park, nature hikes through stunning landscapes, and cultural tours that provide insight into local traditions and crafts. With a strong commitment to sustainability, Virunga Homestay actively supports community initiatives and promotes responsible tourism, allowing guests to contribute to the local economy and conservation efforts during their stay. This combination of serene accommodations, delicious food, and enriching experiences makes Virunga Homestay a perfect choice for those looking to immerse themselves in the beauty and culture of Rwanda.', 'uploads/homeabout/1750766686_ea01b27e-58bc-4d8e-a0e4-7222d2670432 (1).jpg', '2025-06-23 22:41:10', '2025-07-02 10:46:46');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `review_content` text NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `status` enum('approved','pending','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `customer_name`, `rating`, `review_content`, `is_featured`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(4, 'IGIRANEZA Fabrice', 'IGIRANEZA Fabrice', 5, 'Greate service  !', 0, 1, 'approved', '2025-06-25 10:42:04', '2025-06-28 00:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(500) NOT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `title`, `description`, `image`, `status`, `created_at`, `updated_at`) VALUES
(6, 'INTORE ROOM', 'This room is named after the traditional Rwandan dancers, known for their energetic and symbolic performances. The name embodies the pride, strength, and heritage of Rwanda’s people, symbolizing the vibrancy and celebration of Rwandan culture and traditions.', '1750842212_room1.jpg', 'active', '2025-06-25 09:03:32', '2025-06-25 09:03:32'),
(7, 'Ingagi Room', 'Named after the majestic mountain gorillas, this room honors Rwanda’s role in conserving these endangered species. The mountain gorillas represent the country’s success in wildlife protection, particularly within the Volcanoes National Park.', '1750842251_room2.jpg', 'active', '2025-06-25 09:04:11', '2025-06-25 09:04:11');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(500) NOT NULL,
  `display_order` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `image`, `display_order`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(2, 'The unrivalled Travel Information Center', 'Virunga Homestay is not only a place to stay, but also an essential resource hub for travelers. Our homestay is the ideal choice for independent travelers such as solo travelers, local travels and group travelers seeking comprehensive travel information and a wide range of related services such as car hire, airport pick up and drop, park transfer. Whether you\'re planning your journey or exploring new destinations, we provide the support and resources you need for an unforgettable experience. We are here to assist you with insightful tips and practical assistance, making sure you have access to vital information like transportation options, local dining recommendations, and activity bookings. Our cozy accommodations are designed to be a home away from home, providing a welcoming atmosphere that encourages relaxation and social interaction.', 'uploads/services/1750841834_1.jpg', 1, 1, 'active', '2025-06-23 23:00:56', '2025-06-25 08:57:14'),
(3, 'Travel Information Center', 'Experience the essence of local cuisine at Virunga Homestay, where our expert cooks invite you to participate in engaging cooking sessions. Discover the art of preparing traditional dishes using organic ingredients freshly sourced from our farms. This interactive cooking lesson offers a deep dive into the area\'s vibrant culture and history. Under the guidance of our knowledgeable hosts, you\'ll explore the local culinary landscape safely, engage with community members, and uncover hidden culinary treasures not found on typical tourist itineraries. Support the celebration of traditional regional cuisine while receiving valuable tips from your guide to enhance your dining experience and travel journey. Brighten your travel experience with Virunga Homestay, where exceptional hospitality meets stunning natural landscapes, creating unforgettable memories during your journey.', 'uploads/services/1750841921_2.jpg', 1, 1, 'active', '2025-06-23 23:03:14', '2025-06-25 08:58:41');

-- --------------------------------------------------------

--
-- Structure for view `admin_user_permissions_view`
--
DROP TABLE IF EXISTS `admin_user_permissions_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `admin_user_permissions_view`  AS SELECT `u`.`id` AS `user_id`, `u`.`username` AS `username`, `u`.`email` AS `email`, `u`.`full_name` AS `full_name`, `u`.`role` AS `role`, `p`.`permission_name` AS `permission_name`, `p`.`description` AS `permission_description`, `p`.`category` AS `permission_category`, `up`.`granted` AS `granted`, `up`.`granted_at` AS `granted_at` FROM ((`admin_users` `u` left join `admin_user_permissions` `up` on(`u`.`id` = `up`.`user_id`)) left join `admin_permissions` `p` on(`up`.`permission_id` = `p`.`id`)) WHERE `u`.`status` = 'active' ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us_page`
--
ALTER TABLE `about_us_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activities_active_order` (`is_active`,`display_order`);

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_table_name` (`table_name`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_admin_activity_log_cleanup` (`created_at`);

--
-- Indexes for table `admin_api_log`
--
ALTER TABLE `admin_api_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_endpoint` (`endpoint`),
  ADD KEY `idx_method` (`method`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_admin_api_log_cleanup` (`created_at`);

--
-- Indexes for table `admin_login_attempts`
--
ALTER TABLE `admin_login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_attempted_at` (`attempted_at`),
  ADD KEY `idx_success` (`success`),
  ADD KEY `idx_admin_login_attempts_cleanup` (`attempted_at`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`),
  ADD KEY `idx_permission_name` (`permission_name`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `admin_security_log`
--
ALTER TABLE `admin_security_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_admin_security_log_cleanup` (`created_at`);

--
-- Indexes for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_token_hash` (`token_hash`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_admin_sessions_cleanup` (`expires_at`,`created_at`);

--
-- Indexes for table `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_is_public` (`is_public`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `admin_user_permissions`
--
ALTER TABLE `admin_user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_permission` (`user_id`,`permission_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_permission_id` (`permission_id`),
  ADD KEY `idx_granted_by` (`granted_by`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_blogs_published` (`is_published`,`published_at`);

--
-- Indexes for table `blog_content_blocks`
--
ALTER TABLE `blog_content_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_blog_content_blocks_order` (`blog_id`,`display_order`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_events_active_date` (`is_active`,`event_date`);

--
-- Indexes for table `hero_images`
--
ALTER TABLE `hero_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hero_images_active_order` (`is_active`,`display_order`);

--
-- Indexes for table `homepage_about`
--
ALTER TABLE `homepage_about`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reviews_featured` (`is_featured`,`is_active`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_services_active_order` (`is_active`,`display_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us_page`
--
ALTER TABLE `about_us_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `admin_api_log`
--
ALTER TABLE `admin_api_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_login_attempts`
--
ALTER TABLE `admin_login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `admin_security_log`
--
ALTER TABLE `admin_security_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_user_permissions`
--
ALTER TABLE `admin_user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `blog_content_blocks`
--
ALTER TABLE `blog_content_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `hero_images`
--
ALTER TABLE `hero_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `homepage_about`
--
ALTER TABLE `homepage_about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD CONSTRAINT `admin_activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_api_log`
--
ALTER TABLE `admin_api_log`
  ADD CONSTRAINT `admin_api_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_security_log`
--
ALTER TABLE `admin_security_log`
  ADD CONSTRAINT `admin_security_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admin_sessions`
--
ALTER TABLE `admin_sessions`
  ADD CONSTRAINT `admin_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_user_permissions`
--
ALTER TABLE `admin_user_permissions`
  ADD CONSTRAINT `admin_user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_user_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `admin_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_user_permissions_ibfk_3` FOREIGN KEY (`granted_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_content_blocks`
--
ALTER TABLE `blog_content_blocks`
  ADD CONSTRAINT `blog_content_blocks_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
