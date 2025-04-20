-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 20, 2025 at 05:16 PM
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
-- Database: `shopnow`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `submitted_at`) VALUES
(1, 'Jane Doe', 'jane@example.com', 'Hello, I have a question!', '2025-04-11 20:16:03');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`) VALUES
(3, 2, 40.97, 'pending', '2025-04-11 21:10:44'),
(4, 3, 24.98, 'pending', '2025-04-11 21:25:25'),
(5, 3, 8.99, 'pending', '2025-04-11 21:26:01'),
(6, 4, 57.97, 'pending', '2025-04-11 22:11:18'),
(7, 5, 72.95, 'pending', '2025-04-15 10:12:19'),
(8, 6, 12.99, 'pending', '2025-04-16 16:30:50');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_at_purchase`) VALUES
(4, 3, 2, 2, 15.99),
(5, 3, 3, 1, 8.99),
(6, 4, 2, 1, 15.99),
(7, 4, 3, 1, 8.99),
(8, 5, 3, 1, 8.99),
(9, 6, 5, 1, 25.99),
(10, 6, 2, 2, 15.99),
(11, 7, 2, 4, 15.99),
(12, 7, 3, 1, 8.99),
(13, 8, 3, 1, 12.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `category` varchar(100) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock_quantity`, `category`, `image_url`, `created_at`, `updated_at`) VALUES
(2, 'Kitchen Knife Set', 'A set of high-quality kitchen knives', 15.99, 40, 'kitchen', 'assets/images/kitchen_knife_set.jpg', '2025-04-11 20:16:31', '2025-04-15 10:12:19'),
(3, 'Bath Towel Set', 'Soft and absorbent bath towels', 8.99, 95, 'bathroom', 'assets/images/bath_towel_set.jpg', '2025-04-11 20:16:31', '2025-04-16 16:30:50'),
(4, 'Cleaning Supplies Kit', 'Essential cleaning tools and solutions', 12.99, 75, 'cleaning', 'assets/images/cleaning_supplies_kit.jpg', '2025-04-11 20:16:31', '2025-04-11 20:16:31'),
(5, 'Desk Organizer', 'Keep your desk tidy and organized', 25.99, 29, 'office', 'assets/images/desk_organizer.jpg', '2025-04-11 20:16:31', '2025-04-11 22:11:18'),
(6, 'Food Storage Containers', 'Durable containers for food storage', 20.99, 60, 'kitchen', 'assets/images/food_storage_containers.jpg', '2025-04-11 20:16:31', '2025-04-11 20:16:31'),
(7, 'Shower Caddy', 'Convenient storage for shower essentials', 18.99, 40, 'bathroom', 'assets/images/shower_caddy.jpg', '2025-04-11 20:16:31', '2025-04-11 20:16:31'),
(8, 'Coffee Maker', 'Brew your perfect cup of coffee', 29.99, 25, 'kitchen', 'assets/images/coffee_maker.jpg', '2025-04-11 20:16:31', '2025-04-11 20:16:31'),
(9, 'Kitchen Weight Scale', 'Accurate scale for kitchen use', 14.99, 80, 'kitchen', 'assets/images/kitchen_weight_scale.jpg', '2025-04-11 20:16:31', '2025-04-11 20:16:31'),
(10, 'Vacuum Cleaner', 'Powerful vacuum for deep cleaning', 89.99, 15, 'cleaning', 'assets/images/vacuum_cleaner.jpg', '2025-04-11 20:16:31', '2025-04-11 20:16:31'),
(11, 'Desk Lamp', 'Adjustable light for your workspace', 19.99, 45, 'office', 'assets/images/desk_lamp.jpg', '2025-04-11 20:16:31', '2025-04-11 20:16:31'),
(12, 'Cutting Board Set', 'Durable cutting boards for meal prep', 12.99, 70, 'kitchen', 'assets/images/cutting_board.jpg', '2025-04-11 20:16:31', '2025-04-11 20:16:31'),
(13, 'Bath Mat', 'Non-slip mat for bathroom safety', 9.99, 90, 'bathroom', 'assets/images/bath_mat.jpg', '2025-04-11 20:16:31', '2025-04-11 20:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `phone`, `address`, `created_at`, `updated_at`, `profile_image`) VALUES
(2, 'Nemis Ruparel', 'nemisruparel07@gmail.com', '$2y$10$jreZEqXZe6xE6GZF9RnV3ee9NpL5Mw02dZ/zWCptFt0TNCeFpamG.', NULL, NULL, '2025-04-11 20:58:49', '2025-04-11 20:58:49', NULL),
(3, 'Nemis Ruparel', 'mitparmar9750@gmail.com', '$2y$10$1KuLm7Oq/tIKFiGQm4.6NueJO0u3gP8.ez.SPcJ3tJ7gpfOuhy7ou', NULL, NULL, '2025-04-11 21:15:30', '2025-04-11 21:15:30', NULL),
(4, 'Mit Parmar', 'mitparmar@gmail.com', '$2y$10$DoKEndK75x6P3yPg4vmcDuBlFgU9mR4czCss59K4U8z0Ppgycmcoe', NULL, NULL, '2025-04-11 21:54:34', '2025-04-11 21:54:34', NULL),
(5, 'mit', 'mit01@gmail.com', '$2y$10$tLyTJavoHL69uFLnE2vEMuZFqWy6f23bylSccPKXJ7se50polnl36', NULL, NULL, '2025-04-15 10:11:11', '2025-04-15 10:11:11', NULL),
(6, 'xyz', 'xyz@a.a', '$2y$10$AqVmUBkbooRBLYkcNXADluy5yHMm8fwRNxAmzWC3HnzU2fb3I.Hky', NULL, NULL, '2025-04-16 16:29:05', '2025-04-16 16:29:05', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
