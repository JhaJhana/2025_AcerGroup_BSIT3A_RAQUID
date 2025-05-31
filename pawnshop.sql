-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 08:12 AM
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
-- Database: `pawnshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `birth_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `mobile_number`, `birth_date`, `created_at`, `profile_picture`) VALUES
(1, 'Bea Ramos', 'Ramos@gmail.com', '$2y$10$KP5a5P2BKaDyu5lzs3ozsejXQ//k7t23IMv0s.U5Sk.XBBtSvrate', '123456789', '2025-05-01', '2025-05-20 17:30:40', 'uploads/profile_pictures/682d8e445c483.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirm_password` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `birth_date` date NOT NULL,
  `role` enum('super_admin','admin','appraiser') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `karat` varchar(10) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `name`, `description`, `category`, `karat`, `price`, `image`, `created_at`) VALUES
(1, 1, 'Necklace', '22k gold necklace with intricate floral design', 'necklace', '22k', 3500.00, 'necklace.jpg', '2025-05-21 16:04:43');

-- --------------------------------------------------------

--
-- Table structure for table `sangla_requests`
--

CREATE TABLE `sangla_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(50) NOT NULL,
  `grams` decimal(10,2) NOT NULL,
  `remarks` text,
  `photo` varchar(255),
  `orig_price` decimal(10,2) NOT NULL,
  `branch` varchar(50) NOT NULL,
  `status` enum('pending','appraised','accepted','rejected') NOT NULL DEFAULT 'pending',
  `appraised_value` decimal(10,2),
  `for_marketplace` tinyint(1) NOT NULL DEFAULT 0,
  `marketplace_status` enum('pending','approved','rejected') DEFAULT NULL,
  `marketplace_price` decimal(10,2) DEFAULT NULL,
  `marketplace_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sangla_user_id` (`user_id`),
  CONSTRAINT `fk_sangla_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pawned_items`
--

CREATE TABLE `pawned_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sangla_request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pawn_ticket_no` varchar(20) NOT NULL,
  `loan_amount` decimal(10,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `date_pawned` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('active','redeemed','forfeited') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pawned_sangla_id` (`sangla_request_id`),
  KEY `fk_pawned_user_id` (`user_id`),
  CONSTRAINT `fk_pawned_sangla_id` FOREIGN KEY (`sangla_request_id`) REFERENCES `sangla_requests` (`id`),
  CONSTRAINT `fk_pawned_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interest_payments`
--

CREATE TABLE `interest_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pawned_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_type` enum('interest','redemption') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_payment_pawned_id` (`pawned_item_id`),
  KEY `fk_payment_user_id` (`user_id`),
  CONSTRAINT `fk_payment_pawned_id` FOREIGN KEY (`pawned_item_id`) REFERENCES `pawned_items` (`id`),
  CONSTRAINT `fk_payment_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_cart_user_id` (`user_id`),
  KEY `fk_cart_product_id` (`product_id`),
  CONSTRAINT `fk_cart_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('sangla','tubo','payment','system') NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_notification_user_id` (`user_id`),
  CONSTRAINT `fk_notification_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appraisal_history`
--

CREATE TABLE `appraisal_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sangla_request_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `appraised_value` decimal(10,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_appraisal_sangla_id` (`sangla_request_id`),
  KEY `fk_appraisal_admin_id` (`admin_id`),
  CONSTRAINT `fk_appraisal_sangla_id` FOREIGN KEY (`sangla_request_id`) REFERENCES `sangla_requests` (`id`),
  CONSTRAINT `fk_appraisal_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`