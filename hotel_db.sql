-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2025 at 06:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `billing_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `billing_type` enum('no-show-fee','cancellation-fee','extra-service','room-charge') NOT NULL DEFAULT 'room-charge',
  `status` enum('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  `billing_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_payments`
--

CREATE TABLE `bulk_payments` (
  `payment_id` int(10) UNSIGNED NOT NULL,
  `bulk_reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','paypal','cash') NOT NULL DEFAULT 'cash',
  `payment_type` enum('full','partial','arrival') NOT NULL DEFAULT 'arrival',
  `payment_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed','paid','success') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bulk_reservations`
--

CREATE TABLE `bulk_reservations` (
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `bulk_reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `group_name` varchar(200) NOT NULL,
  `num_persons` int(11) NOT NULL DEFAULT 1,
  `contact_person` varchar(100) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `num_standard_rooms` int(11) NOT NULL DEFAULT 0,
  `num_deluxe_rooms` int(11) NOT NULL DEFAULT 0,
  `num_residential_suites` int(11) NOT NULL DEFAULT 0,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `payment_plan` enum('full_credit','partial','on_arrival') NOT NULL DEFAULT 'on_arrival',
  `status` enum('booked','confirmed','cancelled','pending') NOT NULL DEFAULT 'booked',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `daily_breakfast` tinyint(1) NOT NULL DEFAULT 0,
  `welcome_packet` tinyint(1) NOT NULL DEFAULT 0,
  `cancelled_at` datetime DEFAULT NULL,
  `cancelled_by_scheduler` tinyint(1) DEFAULT 0,
  `cancellation_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest`
--

CREATE TABLE `guest` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('guest','manager','travel_agency','reservation_clerk') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest`
--

INSERT INTO `guest` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(6, 'Azha', 'azhafathi@gmail.com', '$2y$10$sXXwyH5tdHXWaWrz8Bp6q.bOFSPuLcb/frLhjJfpSvjqNR.Jgp/3C', 'guest'),
(7, 'fathima', 'fathima@gmail.com', '$2y$10$i/PIC.BpzwpQvnGVIrxI/e7fEfL3TrE1thui2pR4.7rFeJE2OUocu', 'manager'),
(8, 'ifaz', 'ifaz@gmail.com', '$2y$10$rj37f.5xXfYFnp/ijipF5eYNLjOtUqDE2g5Hkp84ih.M87/D6F.Pe', 'guest'),
(11, 'fareena', 'fareena@gmail.com', '$2y$10$2wW10oq7CKO3d7FgaVU3XedgOjqH3A.QUmEs1Oge9O3eX5.OdrcgW', 'guest'),
(12, 'Azka', 'Azka@gmail.com', '$2y$10$0lQQeMQZ859g8Q39uyWj7.LkVRmbcickdey.yQG04JySTQi18LL3m', 'guest'),
(13, 'Hajara', 'hajara@gmail.com', '$2y$10$L8n/FAYPYvl2oxYtwm0UO.77z6puYy71o0RT5OcHubG6Smm3MMT0i', 'guest'),
(14, 'Aqeel', 'aqeel@gmail.com', '$2y$10$pSgaoBbcZKVoudQLwYnDJemuJrfDnjcGSkqojNe8CatynyOjsp1HG', 'guest'),
(15, 'aashif', 'ahmed@gmail.com', '$2y$10$DTiXTkGFiyd6cYDGVsW0GOuJPakUzItx8bwJOni8O8l2wKoTH0adq', 'guest'),
(16, 'Afraz', 'Afraz@gmail.com', '$2y$10$WQ3btFU0BRfINpyt.t.LNOWXsfbGCBvhqleYA3Ht5oMgTZzFXhpGW', 'guest'),
(17, 'Azha', 'azhafathi10@gmail.com', '$2y$10$tQYj4G6MLiwvmVouRr75aemf2dd6bPB4yHuD28hAFIIMeC5obagPy', 'guest'),
(20, 'Afraaz', 'Afraaz@gmail.com', '$2y$10$z.cifyEp5TuOHIPcnHlIZOIYmLfaWsEigq99ggl70eElwONt2dCzO', 'reservation_clerk');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(10) UNSIGNED NOT NULL,
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','paypal','cash') NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `reservation_id`, `amount`, `payment_method`, `payment_date`, `status`) VALUES
(5, 39, 250000.00, 'credit_card', '2025-06-05 12:30:26', 'completed'),
(6, 40, 125000.00, 'credit_card', '2025-06-05 17:41:42', 'completed'),
(8, 42, 30000.00, 'credit_card', '2025-06-06 10:20:23', 'completed'),
(13, 52, 15000.00, 'credit_card', '2025-06-30 12:50:01', 'completed'),
(14, 53, 30000.00, 'cash', '2025-06-30 12:50:57', 'pending'),
(15, 54, 125000.00, 'credit_card', '2025-06-30 12:55:11', 'completed'),
(17, 56, 10000.00, 'credit_card', '2025-06-30 13:00:14', 'completed'),
(18, 57, 20000.00, 'cash', '2025-06-30 13:00:51', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(10) UNSIGNED NOT NULL,
  `bulk_reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `room_id` int(10) UNSIGNED NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `status` enum('booked','confirmed','cancelled','no_show','check_in','check_out') NOT NULL DEFAULT 'booked',
  `amount` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cancelled_at` datetime DEFAULT NULL,
  `cancelled_by_scheduler` tinyint(1) DEFAULT 0,
  `cancellation_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `bulk_reservation_id`, `user_id`, `room_id`, `customer_name`, `room_type`, `check_in_date`, `check_out_date`, `status`, `amount`, `created_at`, `cancelled_at`, `cancelled_by_scheduler`, `cancellation_reason`) VALUES
(9, NULL, 6, 2, 'ifaz ahmed', 'Double', '2025-06-27', '2025-06-30', 'booked', 75000, '2025-06-02 19:47:45', NULL, 0, NULL),
(11, NULL, 6, 1, 'infa zameer', 'Single', '2025-06-11', '2025-06-18', 'booked', 105000, '2025-06-02 20:01:54', NULL, 0, NULL),
(12, NULL, 6, 2, 'Ishra Zameer', 'Suite', '2025-06-15', '2025-06-16', 'booked', 25000, '2025-06-02 20:09:13', NULL, 0, NULL),
(21, NULL, 11, 3, 'fareena ahmed', 'Suite', '2025-06-20', '2025-06-29', 'booked', 405000, '2025-06-05 08:48:09', NULL, 0, NULL),
(22, NULL, 11, 3, 'fareena ahmed', 'Suite', '2025-06-20', '2025-06-29', 'booked', 405000, '2025-06-05 08:55:09', NULL, 0, NULL),
(34, NULL, 6, 2, 'fareena ahmed', 'Double', '2025-06-08', '2025-06-09', 'booked', 25000, '2025-06-05 10:19:13', NULL, 0, NULL),
(35, NULL, 8, 3, 'ifaz ahmed', 'Suite', '2025-06-16', '2025-06-20', 'booked', 180000, '2025-06-05 10:22:34', NULL, 0, NULL),
(39, NULL, 8, 2, 'azhiya Ahmed', 'Double', '2025-06-11', '2025-06-21', 'booked', 250000, '2025-06-05 10:30:26', NULL, 0, NULL),
(40, NULL, 6, 3, 'Azha Nasar', 'Suite', '2025-06-25', '2025-06-30', 'booked', 125000, '2025-06-05 15:41:42', NULL, 0, NULL),
(41, NULL, 15, 2, 'aashif ahmed', 'Double', '2025-06-24', '2025-06-30', 'booked', 90000, '2025-06-05 17:30:18', NULL, 0, NULL),
(42, NULL, 6, 1, 'Azzu fthi', 'Single', '2025-06-06', '2025-06-09', 'booked', 30000, '2025-06-06 08:20:23', NULL, 0, NULL),
(46, NULL, 6, 2, 'Anha azz', 'Double', '2025-06-10', '2025-06-18', 'booked', 120000, '2025-06-07 17:15:17', NULL, 0, NULL),
(47, NULL, 6, 3, 'hajara nasar', 'Suite', '2025-06-07', '2025-06-10', 'booked', 75000, '2025-06-07 17:23:25', NULL, 0, NULL),
(48, NULL, 8, 1, 'ippu ahmd', 'Single', '2025-06-30', '2025-07-03', 'booked', 30000, '2025-06-30 07:39:35', NULL, 0, NULL),
(49, NULL, 8, 1, 'Azha ahmed', 'Single', '2025-07-10', '2025-07-18', 'booked', 80000, '2025-06-30 07:41:17', NULL, 0, NULL),
(52, NULL, 8, 2, 'ishra zameer', 'Double', '2025-07-02', '2025-07-03', 'booked', 15000, '2025-06-30 10:50:01', NULL, 0, NULL),
(53, NULL, 8, 2, 'baskar  cad', 'Double', '2025-06-30', '2025-07-02', 'booked', 30000, '2025-06-30 10:50:57', NULL, 0, NULL),
(54, NULL, 8, 3, 'anhh haa', 'Suite', '2025-07-11', '2025-07-16', 'booked', 125000, '2025-06-30 10:55:11', NULL, 0, NULL),
(56, NULL, 8, 1, 'Az ha', 'Single', '2025-07-09', '2025-07-10', 'booked', 10000, '2025-06-30 11:00:14', NULL, 0, NULL),
(57, NULL, 8, 1, 'ifaz ahmed', 'Single', '2025-07-22', '2025-07-24', 'booked', 20000, '2025-06-30 11:00:51', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) UNSIGNED NOT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `room_type` enum('Single','Double','Suite') NOT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_type`, `status`) VALUES
(1, '101', 'Single', 'available'),
(2, '102', 'Double', 'available'),
(3, '103', 'Suite', 'available');

--
-- Indexes for dumped tables
--
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_percent DECIMAL(5,2) NOT NULL,
    valid_from DATE,
    valid_until DATE,
    max_uses INT DEFAULT NULL,
    times_used INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample coupons
INSERT INTO coupons (code, discount_percent, valid_until, max_uses) 
VALUES 
    ('SUMMER5', 5.00, '2025-12-31', 100),
    ('WELCOME10', 10.00, '2025-12-31', 50);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`billing_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `bulk_payments`
--
ALTER TABLE `bulk_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `bulk_reservation_id` (`bulk_reservation_id`);

--
-- Indexes for table `bulk_reservations`
--
ALTER TABLE `bulk_reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `bulk_reservation_id` (`bulk_reservation_id`);

--
-- Indexes for table `guest`
--
ALTER TABLE `guest`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `bulk_reservation_id` (`bulk_reservation_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `billing_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_payments`
--
ALTER TABLE `bulk_payments`
  MODIFY `payment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bulk_reservations`
--
ALTER TABLE `bulk_reservations`
  MODIFY `reservation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest`
--
ALTER TABLE `guest`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`) ON DELETE CASCADE;

--
-- Constraints for table `bulk_payments`
--
ALTER TABLE `bulk_payments`
  ADD CONSTRAINT `bulk_payments_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `bulk_reservations` (`reservation_id`) ON DELETE CASCADE;

--
-- Constraints for table `bulk_reservations`
--
ALTER TABLE `bulk_reservations`
  ADD CONSTRAINT `bulk_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `guest` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`reservation_id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `guest` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
