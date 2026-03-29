-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 04:33 AM
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
-- Database: `stayhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `arrival` date NOT NULL,
  `departure` date NOT NULL,
  `status` varchar(50) DEFAULT 'Paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `full_name`, `email`, `phone`, `room_name`, `total_price`, `arrival`, `departure`, `status`, `created_at`) VALUES
(1, 7, 'paulnatad', 'clarkgarnica45@gmail.com', '09265288297', 'StayHub Suite 1', 1600.00, '2026-03-15', '2026-03-16', 'Pending', '2026-03-15 08:37:21'),
(2, 7, 'paulnatad', 'clarkgarnica45@gmail.com', '09265288297', 'StayHub Suite 3', 2000.00, '2026-03-18', '2026-03-19', 'Pending', '2026-03-15 08:38:21'),
(3, 7, 'paulnatad', 'clarkgarnica45@gmail.com', '09265288297', 'StayHub Suite 4', 11000.00, '2026-03-15', '2026-03-20', 'Pending', '2026-03-15 08:49:35'),
(4, 7, 'paulnatad', 'clarkgarnica45@gmail.com', '09265288297', 'StayHub Suite 1', 11200.00, '2026-03-19', '2026-03-26', 'Paid', '2026-03-15 08:51:42'),
(5, 7, 'paulnatad', 'clarkgarnica45@gmail.com', '09265288297', 'StayHub Suite 5', 28800.00, '2026-03-16', '2026-03-28', 'Paid', '2026-03-15 08:53:27'),
(6, 7, 'paulnatad', 'clarkgarnica45@gmail.com', '09265288297', 'StayHub Suite 1', 16000.00, '2026-03-16', '2026-03-26', 'Paid', '2026-03-15 09:06:08'),
(7, 7, '', '', '', 'StayHub Suite 4', 8708.33, '2026-03-26', '2026-03-30', 'Pending', '2026-03-26 03:20:19'),
(8, 7, 'paulnatad', 'clarkgarnica45@gmail.com', '09265288297', 'StayHub Suite 4', 8708.33, '2026-03-26', '2026-03-30', 'Paid', '2026-03-26 03:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT 5.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `room_type`, `price`, `description`, `status`, `image`, `rating`) VALUES
(1, 'Room 1', 'Standard', 0.00, 'Default description', '', 'room1.jpg', 5.0),
(2, 'Room 2', 'Standard', 0.00, 'Default description', '', 'room2.jpg', 5.0),
(3, 'Room 3', 'Standard', 0.00, 'Default description', '', 'room3.jpg', 5.0),
(4, 'Room 4', 'Standard', 0.00, 'Default description', '', 'room4.jpg', 5.0),
(5, 'Room 5', 'Standard', 0.00, 'Default description', '', 'room5.jpg', 5.0),
(6, 'Room 6', 'Standard', 0.00, 'Default description', '', 'room6.jpg', 5.0),
(7, 'Room 7', 'Standard', 0.00, 'Default description', '', 'room7.jpg', 5.0),
(8, 'Room 8', 'Standard', 0.00, 'Default description', '', 'room8.jpg', 5.0),
(9, 'Room 9', 'Standard', 0.00, 'Default description', '', 'room9.jpg', 5.0),
(10, 'Room 10', 'Standard', 0.00, 'Default description', '', 'room10.jpg', 5.0),
(11, 'Room 11', 'Standard', 0.00, 'Default description', '', 'room11.jpg', 5.0),
(12, 'Room 12', 'Standard', 0.00, 'Default description', '', 'room12.jpg', 5.0),
(13, 'Room 13', 'Standard', 0.00, 'Default description', '', 'room13.jpg', 5.0),
(14, 'Room 14', 'Standard', 0.00, 'Default description', '', 'room14.jpg', 5.0),
(15, 'Room 15', 'Standard', 0.00, 'Default description', '', 'room15.jpg', 5.0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `status` enum('active','banned') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`) VALUES
(4, 'Admin', 'admin@stayhub.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'admin', 'active'),
(7, 'paulnatad', 'clarkgarnica45@gmail.com', 'b815df46d0dc1529a5f1e639e9fdcb26c43c57b815942d8a00d87e96bdcf3eff', 'customer', 'active'),
(8, 'paulnatad', 'laklak@gmail.com', 'b815df46d0dc1529a5f1e639e9fdcb26c43c57b815942d8a00d87e96bdcf3eff', 'customer', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
