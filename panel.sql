-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 27, 2021 at 09:42 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vexpanel`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(111) NOT NULL,
  `from_id` bigint(111) NOT NULL,
  `announcement` text NOT NULL,
  `time_sent` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(111) NOT NULL,
  `ptero_domain` varchar(128) NOT NULL,
  `ptero_api` varchar(128) NOT NULL,
  `licenseKey` varchar(128) NOT NULL,
  `guildId` bigint(128) NOT NULL,
  `importantInfoId` bigint(128) NOT NULL,
  `mainTheme` int(111) NOT NULL,
  `siteMaintenance` int(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `config`
--



-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(128) NOT NULL,
  `product_ram` int(111) NOT NULL,
  `product_cpu` int(111) NOT NULL,
  `product_disk` int(111) NOT NULL,
  `product_stock` int(111) NOT NULL,
  `product_price` int(111) NOT NULL,
  `egg_id` int(11) NOT NULL,
  `nest_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--



-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `id` int(111) NOT NULL,
  `server_name` varchar(128) NOT NULL,
  `server_uid` int(111) NOT NULL,
  `server_ram` int(111) NOT NULL,
  `server_cpu` int(111) NOT NULL,
  `server_disksp` int(111) NOT NULL,
  `server_alloc` int(111) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_id` bigint(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `servers`
--



-- --------------------------------------------------------

--
-- Table structure for table `srv_nodes`
--

CREATE TABLE `srv_nodes` (
  `id` int(111) NOT NULL,
  `node_name` varchar(128) NOT NULL,
  `node_id` int(111) NOT NULL,
  `node_slots` int(111) NOT NULL,
  `node_servers` int(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `srv_nodes`
--



-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(111) NOT NULL,
  `user_uid` bigint(111) NOT NULL,
  `staff_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff`
--



-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(111) NOT NULL,
  `ticket_name` varchar(128) NOT NULL,
  `ticket_owner` bigint(111) NOT NULL,
  `ticket_uid` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tickets`
--



-- --------------------------------------------------------

--
-- Table structure for table `ticket_msgs`
--

CREATE TABLE `ticket_msgs` (
  `id` int(111) NOT NULL,
  `ticket_msg` varchar(255) NOT NULL,
  `ticket_id` varchar(128) NOT NULL,
  `msg_from` varchar(128) NOT NULL,
  `msg_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ticket_msgs`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(111) NOT NULL,
  `discord_usr` varchar(128) NOT NULL,
  `discord_id` bigint(111) NOT NULL,
  `user_email` varchar(128) NOT NULL,
  `first_ip` text NOT NULL,
  `last_ip` text NOT NULL,
  `ptero_user` varchar(128) NOT NULL,
  `ptero_pwd` varchar(128) NOT NULL,
  `ptero_uid` int(111) NOT NULL,
  `minutes_idle` int(111) NOT NULL,
  `coins` int(111) NOT NULL,
  `last_seen` bigint(111) NOT NULL,
  `ram` int(111) NOT NULL,
  `cpu` int(111) NOT NULL,
  `disk_space` int(111) NOT NULL,
  `server_slots` int(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--


--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `srv_nodes`
--
ALTER TABLE `srv_nodes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_msgs`
--
ALTER TABLE `ticket_msgs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `srv_nodes`
--
ALTER TABLE `srv_nodes`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ticket_msgs`
--
ALTER TABLE `ticket_msgs`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
