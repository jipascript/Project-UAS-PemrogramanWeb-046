-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2025 at 05:52 AM
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
-- Create database
--

CREATE DATABASE IF NOT EXISTS `merona_shop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `merona_shop`;

--
-- Database: `merona_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `activity`, `timestamp`, `ip_address`) VALUES
(1, 1, 'User logged in', '2025-07-25 11:13:29', '127.0.0.1'),
(2, 2, 'User logged in', '2025-07-25 11:15:27', '127.0.0.1'),
(3, 2, 'User logged out', '2025-07-25 11:15:56', '127.0.0.1'),
(4, 1, 'User logged in', '2025-07-25 11:16:23', '127.0.0.1'),
(5, 2, 'User logged in', '2025-07-25 11:26:09', '127.0.0.1'),
(6, 2, 'User logged out', '2025-07-25 11:31:38', '127.0.0.1'),
(7, 1, 'User logged in', '2025-07-25 11:31:51', '127.0.0.1'),
(8, 1, 'User logged out', '2025-07-25 11:32:34', '127.0.0.1'),
(9, 2, 'User logged in', '2025-07-25 11:32:54', '127.0.0.1'),
(10, 2, 'Added product \'Floral Summer Blouse\' to cart', '2025-07-25 11:33:09', '127.0.0.1'),
(11, 2, 'Added product \'Elegant Midi Dress\' to cart', '2025-07-25 11:33:11', '127.0.0.1'),
(12, 2, 'User logged in', '2025-07-25 11:40:45', '127.0.0.1'),
(13, 1, 'User logged in', '2025-07-25 11:42:07', '127.0.0.1'),
(14, 1, 'User logged out', '2025-07-25 11:46:28', '127.0.0.1'),
(15, 2, 'User logged in', '2025-07-25 11:46:44', '127.0.0.1'),
(16, 2, 'User logged out', '2025-07-25 11:47:12', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 2, 1, 1, '2025-07-25 11:33:09'),
(2, 2, 2, 1, '2025-07-25 11:33:11');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Atasan', 'Koleksi atasan wanita trendy dan fashionable', '2025-07-25 11:09:46', '2025-07-25 11:09:46'),
(2, 'Bawahan', 'Koleksi bawahan wanita modern dan stylish', '2025-07-25 11:09:46', '2025-07-25 11:09:46'),
(3, 'Dress', 'Koleksi dress elegant untuk berbagai acara', '2025-07-25 11:09:46', '2025-07-25 11:09:46'),
(4, 'Outer', 'Koleksi jaket dan cardigan untuk gaya kasual', '2025-07-25 11:09:46', '2025-07-25 11:09:46'),
(5, 'Aksesoris', 'Koleksi aksesoris fashion pelengkap outfit', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(6, 'Sepatu', 'Koleksi sepatu wanita untuk berbagai gaya', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(7, 'Tas', 'Koleksi tas wanita trendy dan fungsional', '2025-07-25 12:00:00', '2025-07-25 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `account_name` varchar(255) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `proof_image` varchar(255) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `status` varchar(50) DEFAULT 'active',
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock`, `status`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'Floral Summer Blouse', 'Blouse musim panas dengan motif bunga yang cantik', 239000.00, 50, 'active', 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=400&fit=crop&crop=center', '2025-07-25 11:09:46', '2025-07-25 11:45:47'),
(2, 3, 'Elegant Midi Dress', 'Dress midi elegant untuk acara formal', 399000.00, 30, 'active', 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=400&fit=crop&crop=center', '2025-07-25 11:09:46', '2025-07-25 11:45:47'),
(3, 2, 'High-waist Denim Jeans', 'Jeans denim high-waist yang trendy', 299000.00, 40, 'active', 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=400&h=400&fit=crop&crop=center', '2025-07-25 11:09:46', '2025-07-25 11:45:47'),
(4, 4, 'Casual Cardigan', 'Cardigan casual untuk gaya santai', 189000.00, 25, 'active', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=400&fit=crop&crop=center', '2025-07-25 11:09:46', '2025-07-25 11:45:47'),
(5, 1, 'Striped Cotton Shirt', 'Kemeja katun bergaris untuk gaya kasual sehari-hari', 189000.00, 35, 'active', 'https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?w=400&h=400&fit=crop&crop=center', '2025-07-25 11:45:47', '2025-07-25 11:45:47'),
(6, 3, 'Floral Maxi Dress', 'Dress maxi bermotif bunga untuk acara santai', 329000.00, 25, 'active', 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=400&fit=crop&crop=center', '2025-07-25 11:45:47', '2025-07-25 11:45:47'),
(7, 2, 'Black Skinny Jeans', 'Celana jeans hitam dengan potongan skinny fit', 279000.00, 40, 'active', 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=400&h=400&fit=crop&crop=center', '2025-07-25 11:45:47', '2025-07-25 11:45:47'),
(8, 4, 'Knit Sweater', 'Sweater rajut hangat untuk musim dingin', 245000.00, 20, 'active', 'https://images.unsplash.com/photo-1576871337622-98d48d1cf531?w=400&h=400&fit=crop&crop=center', '2025-07-25 11:45:47', '2025-07-25 11:45:47'),
(9, 1, 'White Button Shirt', 'Kemeja putih polos untuk gaya formal dan kasual', 199000.00, 45, 'active', 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=400&h=400&fit=crop&crop=center', '2025-07-25 11:45:47', '2025-07-25 11:45:47'),
(10, 3, 'Summer Sundress', 'Dress musim panas ringan dan nyaman', 289000.00, 30, 'active', 'https://plus.unsplash.com/premium_photo-1664871747896-58ad6d4240b7?q=80&w=387&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', '2025-07-25 11:45:47', '2025-07-25 11:51:43'),
(11, 1, 'Silk Satin Blouse', 'Blouse satin sutra untuk tampilan mewah', 459000.00, 25, 'active', 'https://images.unsplash.com/photo-1564557287817-3785e38ec1f5?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(12, 2, 'Wide Leg Trousers', 'Celana panjang potongan lebar yang nyaman', 359000.00, 35, 'active', 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(13, 3, 'Evening Gown', 'Gaun malam elegan untuk acara spesial', 899000.00, 15, 'active', 'https://images.unsplash.com/photo-1566479179817-c06eabbe0806?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(14, 4, 'Denim Jacket', 'Jaket denim klasik untuk gaya kasual', 399000.00, 30, 'active', 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(15, 5, 'Pearl Necklace', 'Kalung mutiara untuk tampilan elegan', 299000.00, 50, 'active', 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(16, 6, 'High Heel Pumps', 'Sepatu hak tinggi untuk tampilan profesional', 459000.00, 25, 'active', 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(17, 7, 'Crossbody Bag', 'Tas selempang praktis untuk aktivitas sehari-hari', 259000.00, 40, 'active', 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(18, 1, 'V-neck Sweater', 'Sweater v-neck hangat dan stylish', 299000.00, 45, 'active', 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(19, 2, 'Pleated Skirt', 'Rok pleated feminin untuk tampilan manis', 229000.00, 40, 'active', 'https://images.unsplash.com/photo-1583496661160-fb5886a13d45?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00'),
(20, 3, 'Cocktail Dress', 'Dress cocktail untuk acara semi-formal', 549000.00, 20, 'active', 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=400&h=400&fit=crop&crop=center', '2025-07-25 12:00:00', '2025-07-25 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `review`, `rating`, `created_at`) VALUES
(1, 2, 1, 'Blouse yang sangat cantik! Bahannya adem dan nyaman dipakai. Sangat puas dengan pembelian ini.', 5, '2025-07-25 12:35:00'),
(2, 4, 1, 'Kualitas bagus, tapi ukurannya sedikit kekecilan. Overall masih bagus.', 4, '2025-07-25 12:40:00'),
(3, 5, 2, 'Dress yang elegant banget! Cocok untuk acara formal. Highly recommended!', 5, '2025-07-25 12:45:00'),
(4, 6, 3, 'Jeansnya comfortable dan modelnya trendy. Worth the price!', 4, '2025-07-25 12:50:00'),
(5, 7, 4, 'Cardigan yang cozy dan hangat. Perfect untuk cuaca dingin.', 5, '2025-07-25 12:55:00'),
(6, 9, 5, 'Kemeja bergaris yang stylish. Bahannya katun asli dan breathable.', 4, '2025-07-25 13:00:00'),
(7, 2, 6, 'Maxi dress yang flowy dan feminine. Suka banget dengan motif bunganya!', 5, '2025-07-25 13:05:00'),
(8, 4, 15, 'Kalung mutiara yang elegan. Packaging juga rapi banget.', 5, '2025-07-25 13:10:00'),
(9, 5, 16, 'Sepatu heels yang comfortable untuk dipakai seharian. Kualitas bagus!', 4, '2025-07-25 13:15:00'),
(10, 6, 17, 'Tas crossbody yang praktis dan stylish. Banyak compartment untuk organize barang.', 4, '2025-07-25 13:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `about` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `logo`, `contact_email`, `phone`, `address`, `about`, `updated_at`) VALUES
(1, 'Merona Fashion', 'logo.png', 'info@merona.com', '+62 812-3456-7890', 'Jl. Fashion Street No. 123, Jakarta', 'Merona adalah toko fashion online yang menyediakan pakaian wanita trendy dan berkualitas.', '2025-07-25 11:09:46');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `transaction_code` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `shipping_name` varchar(255) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_city` varchar(255) DEFAULT NULL,
  `shipping_postal_code` varchar(10) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'customer',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@merona.com', NULL, NULL, '$2a$12$dfaspQe3Jk.eh5qjSYq6weOqznFSB63vjyIhZybZO4xEEVEuBQmIa', 'admin', '2025-07-25 11:09:46', '2025-07-25 11:09:46'),
(2, 'Demo Customer', 'customer@merona.com', NULL, NULL, '$2a$12$bmt2qwntDpQsOHplD4zG1e6yMKDyPITS2FebRsze0knDoweejACfa', 'customer', '2025-07-25 11:09:46', '2025-07-25 11:09:46'),
(3, 'Alice Johnson', 'alice@merona.com', '+62 812-0000-0001', 'Jl. Mawar No. 10, Jakarta Selatan', '$2a$12$dfaspQe3Jk.eh5qjSYq6weOqznFSB63vjyIhZybZO4xEEVEuBQmIa', 'admin', '2025-07-25 12:10:00', '2025-07-25 12:10:00'),
(4, 'Bob Smith', 'bob@customer.com', '+62 812-0000-0002', 'Jl. Melati No. 20, Surabaya', '$2a$12$bmt2qwntDpQsOHplD4zG1e6yMKDyPITS2FebRsze0knDoweejACfa', 'customer', '2025-07-25 12:15:00', '2025-07-25 12:15:00'),
(5, 'Charlie Brown', 'charlie@shopper.com', '+62 812-0000-0003', 'Jl. Anggrek No. 30, Bandung', '$2a$12$bmt2qwntDpQsOHplD4zG1e6yMKDyPITS2FebRsze0knDoweejACfa', 'customer', '2025-07-25 12:18:00', '2025-07-25 12:18:00'),
(6, 'Diana Prince', 'diana@fashion.com', '+62 812-0000-0004', 'Jl. Dahlia No. 40, Yogyakarta', '$2a$12$bmt2qwntDpQsOHplD4zG1e6yMKDyPITS2FebRsze0knDoweejACfa', 'customer', '2025-07-25 12:20:00', '2025-07-25 12:20:00'),
(7, 'Eva Martinez', 'eva@customer.com', '+62 812-0000-0005', 'Jl. Tulip No. 50, Medan', '$2a$12$bmt2qwntDpQsOHplD4zG1e6yMKDyPITS2FebRsze0knDoweejACfa', 'customer', '2025-07-25 12:22:00', '2025-07-25 12:22:00'),
(8, 'Frank Wilson', 'frank@admin.com', '+62 812-0000-0006', 'Jl. Kenanga No. 60, Bali', '$2a$12$dfaspQe3Jk.eh5qjSYq6weOqznFSB63vjyIhZybZO4xEEVEuBQmIa', 'admin', '2025-07-25 12:25:00', '2025-07-25 12:25:00'),
(9, 'Grace Lee', 'grace@shopper.com', '+62 812-0000-0007', 'Jl. Sakura No. 70, Makassar', '$2a$12$bmt2qwntDpQsOHplD4zG1e6yMKDyPITS2FebRsze0knDoweejACfa', 'customer', '2025-07-25 12:28:00', '2025-07-25 12:28:00'),
(10, 'Henry Taylor', 'henry@customer.com', '+62 812-0000-0008', 'Jl. Bougenville No. 80, Palembang', '$2a$12$bmt2qwntDpQsOHplD4zG1e6yMKDyPITS2FebRsze0knDoweejACfa', 'customer', '2025-07-25 12:30:00', '2025-07-25 12:30:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_code` (`transaction_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
