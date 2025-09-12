-- SQL Script for house_rules table

CREATE TABLE `house_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `section` varchar(50) NOT NULL DEFAULT 'general',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data for house_rules table
INSERT INTO `house_rules` (`title`, `icon`, `content`, `section`, `display_order`, `is_active`) VALUES
('Breakfast', 'fas fa-utensils', 'The breakfast is served from 6:00 AM to 8:00 AM.<br><span class="highlight">Note: Hosts can offer a complimentary light breakfast at their discretion. All other meals, including a full breakfast, might incur an additional cost, if offered. Meals and any additional payment should be arranged directly with your host.</span>', 'general', 1, 1),
('Quiet Hours', 'fas fa-volume-mute', 'Observe quiet hours (starting from 10:00 PM to 7:00 AM). Respect the host\'s and other guests\' right to peace and quiet during specified hours. Avoid loud noise or music that may disturb others at any time.', 'general', 2, 1),
('No Smoking & Drug Use', 'fas fa-smoking-ban', 'Do not smoke or use drugs in the homestay or on the property. If smoking is allowed, only smoke in designated outdoor areas and dispose of cigarette butts appropriately.', 'general', 3, 1),
('Pets', 'fas fa-paw', 'Check with the host beforehand if you wish to bring a pet with you.', 'general', 4, 1),
('Check-In & Check-Out', 'fas fa-sign-in-alt', 'Arrive at the homestay at the agreed-upon check-in time, or inform the host ahead of time if you will be late. Leave the property on time at check-out. Extra cost of 50% of the current cost for the room applies when the check-out time is not respected as agreed upon.', 'general', 5, 1),
('Curfew', 'fas fa-bed', 'Curfew from 10:30 PM until 5:00 AM. Please remain within the premises during curfew hours to maintain a peaceful environment for all guests.', 'general', 6, 1),
('Guests & Visitors', 'fas fa-user-friends', 'Do not bring additional guests or visitors onto the property without the host\'s permission. Ensure that any visitors or guests you do bring follow the house rules.', 'general', 7, 1),
('Respect Privacy', 'fas fa-user-secret', 'Respect the privacy of the host and other guests staying at the homestay. Do not enter other guests\' rooms without permission, and ensure that any shared spaces are left tidy after use.', 'general', 8, 1);

-- Create table for cancellation policy
CREATE TABLE `cancellation_policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `section` varchar(50) NOT NULL DEFAULT 'refund',
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data for cancellation_policy table
INSERT INTO `cancellation_policy` (`title`, `icon`, `content`, `section`, `display_order`, `is_active`) VALUES
('Refund Schedule', 'fas fa-money-bill-wave', '<ul><li><strong>Full Refund (100%)</strong> – Cancel 7 days or more before your check-in date.</li><li><strong>Partial Refund (50%)</strong> – Cancel 3 to 6 days before check-in.</li><li><strong>No Refund</strong> – Cancel less than 48 hours before check-in.</li><li><strong>No-Show</strong> – If you do not arrive on your scheduled check-in date, it will be treated as a no-show with no refund issued.</li></ul>', 'refund', 1, 1),
('Important Notes', 'fas fa-info-circle', '<ul><li>Unused inclusions in your booking (e.g. meals, activities) are non-refundable.</li><li>Refunds will be processed within 7 working days to the original payment account.</li><li>Discounted or promotional bookings are non-refundable.</li><li>Refunds apply only to the basic room tariff. Convenience fees and taxes are non-refundable.</li></ul>', 'refund', 2, 1),
('Extenuating Circumstances Policy', 'fas fa-exclamation-circle', '<p>If you are unable to arrive due to unavoidable situations such as natural disasters, security incidents, sudden government policy changes, or pandemics:</p><ul><li>You may reschedule your booking within 6 months at no cancellation charge.</li><li>You must inform us at least 48 hours before your check-in date.</li><li>Rescheduling is subject to availability.</li><li>One reschedule is free; any additional reschedule incurs a 20% rescheduling fee of the basic tariff.</li><li>Rescheduling is valid only for the same homestay location – Virunga Homestay, Musanze, Rwanda.</li></ul>', 'refund', 3, 1);

-- Create table for info cards
CREATE TABLE `house_info_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(100) NOT NULL,
  `content` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data for house_info_cards table
INSERT INTO `house_info_cards` (`icon`, `content`, `display_order`, `is_active`) VALUES
('fas fa-door-open', 'Check-In: 12:00 PM', 1, 1),
('fas fa-door-closed', 'Check-Out: 10:00 PM', 2, 1),
('fas fa-moon', 'Quiet Hours: 10:00 PM - 7:00 AM', 3, 1),
('fas fa-user-shield', 'Respect Privacy Policy', 4, 1),
('fas fa-smoking-ban', 'No Smoking', 5, 1),
('fas fa-dog', 'No Pets', 6, 1),
('fas fa-user-slash', 'No Unapproved Guests', 7, 1),
('fas fa-shield-alt', 'Guest Liable for Damage', 8, 1),
('fas fa-car', 'Free Parking', 9, 1),
('fas fa-wifi', 'Free Wifi', 10, 1),
('fas fa-tshirt', 'Laundry Service', 11, 1),
('fas fa-key', 'Lock Room, Not Responsible for Loss', 12, 1);