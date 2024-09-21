-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 21, 2024 at 03:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `invenpro`
--

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE IF NOT EXISTS `branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` char(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `branch`
--

TRUNCATE TABLE `branch`;
--
-- Dumping data for table `branch`
--

INSERT IGNORE INTO `branch` (`id`, `name`, `address`, `phone_number`, `created_at`, `updated_at`) VALUES
(1, 'Main Branch', '123 Main St, City', '555-1234', '2024-09-18 13:42:09', '2024-09-18 13:42:09'),
(2, 'West Branch', '456 West St, City', '555-5678', '2024-09-18 13:42:09', '2024-09-18 13:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `category`
--

TRUNCATE TABLE `category`;
--
-- Dumping data for table `category`
--

INSERT IGNORE INTO `category` (`id`, `name`) VALUES
(1, 'Electronics'),
(2, 'Groceries'),
(3, 'Dairy'),
(4, 'Fruits'),
(5, 'Grains'),
(6, 'Organic'),
(7, 'Smartphones');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE IF NOT EXISTS `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` char(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employee_fk_role_id` (`role_id`),
  KEY `employee_fk_branch_id` (`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `employee`
--

TRUNCATE TABLE `employee`;
--
-- Dumping data for table `employee`
--

INSERT IGNORE INTO `employee` (`id`, `email`, `password`, `role_id`, `branch_id`, `full_name`, `phone_number`, `created_at`, `updated_at`) VALUES
(2, 'test', 'test', 1, 1, 'Pamudu Sarasith', '1234567890', '2024-09-18 13:50:31', '2024-09-18 13:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE IF NOT EXISTS `inventory` (
  `branch_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_no` varchar(255) NOT NULL,
  `quantity` float NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`branch_id`,`product_id`,`batch_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `inventory`
--

TRUNCATE TABLE `inventory`;
--
-- Dumping data for table `inventory`
--

INSERT IGNORE INTO `inventory` (`branch_id`, `product_id`, `batch_no`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, '101', 10, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(1, 1, '102', 5, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(1, 2, '201', 50, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(1, 3, '301', 100, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(1, 5, '501', 15, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(1, 5, '502', 10, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(2, 2, '202', 30, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(2, 3, '302', 50, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(2, 4, '401', 30, '2024-09-19 02:07:32', '2024-09-19 02:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_fk_category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `permission`
--

TRUNCATE TABLE `permission`;
--
-- Dumping data for table `permission`
--

INSERT IGNORE INTO `permission` (`id`, `name`, `category_id`) VALUES
(1, 'View Dashboard', 1),
(2, 'Add product', 2);

-- --------------------------------------------------------

--
-- Table structure for table `permission_category`
--

CREATE TABLE IF NOT EXISTS `permission_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `permission_category`
--

TRUNCATE TABLE `permission_category`;
--
-- Dumping data for table `permission_category`
--

INSERT IGNORE INTO `permission_category` (`id`, `name`) VALUES
(1, 'Dashboard'),
(2, 'Products');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `measure_unit` enum('kg','l','items') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `product`
--

TRUNCATE TABLE `product`;
--
-- Dumping data for table `product`
--

INSERT IGNORE INTO `product` (`id`, `name`, `description`, `measure_unit`, `created_at`, `updated_at`) VALUES
(1, 'Apple iPhone 14', 'Latest Apple smartphone with 5G support', 'items', '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(2, 'Bananas', 'Fresh organic bananas', 'kg', '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(3, 'Milk', 'Organic whole milk', 'l', '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(4, 'Rice', 'Long grain basmati rice', 'kg', '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(5, 'Samsung Galaxy S21', 'Premium Android smartphone with AMOLED display', 'items', '2024-09-19 02:07:32', '2024-09-19 02:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `product_batch`
--

CREATE TABLE IF NOT EXISTS `product_batch` (
  `batch_no` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `manufacture_date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`batch_no`,`product_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `product_batch`
--

TRUNCATE TABLE `product_batch`;
--
-- Dumping data for table `product_batch`
--

INSERT IGNORE INTO `product_batch` (`batch_no`, `product_id`, `price`, `manufacture_date`, `expire_date`, `created_at`, `updated_at`) VALUES
('101', 1, 999.99, '2024-01-01', NULL, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
('102', 1, 1050.00, '2024-02-01', NULL, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
('201', 2, 1.50, '2024-03-01', '2024-04-01', '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
('202', 2, 1.50, '2024-03-10', '2024-04-10', '2024-09-19 02:07:32', '2024-09-19 12:40:15'),
('301', 3, 1.20, '2024-02-15', '2024-04-15', '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
('302', 3, 1.30, '2024-03-01', '2024-05-01', '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
('401', 4, 0.80, '2024-03-01', '2025-03-01', '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
('501', 5, 850.00, '2024-04-01', NULL, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
('502', 5, 900.00, '2024-05-01', NULL, '2024-09-19 02:07:32', '2024-09-19 02:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE IF NOT EXISTS `product_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`product_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `product_category`
--

TRUNCATE TABLE `product_category`;
--
-- Dumping data for table `product_category`
--

INSERT IGNORE INTO `product_category` (`product_id`, `category_id`, `created_at`) VALUES
(1, 1, '2024-09-19 02:07:32'),
(1, 7, '2024-09-19 02:07:32'),
(2, 2, '2024-09-19 02:07:32'),
(2, 4, '2024-09-19 02:07:32'),
(2, 6, '2024-09-19 02:07:32'),
(3, 2, '2024-09-19 02:07:32'),
(3, 3, '2024-09-19 02:07:32'),
(3, 6, '2024-09-19 02:07:32'),
(4, 5, '2024-09-19 02:07:32'),
(5, 1, '2024-09-19 02:07:32'),
(5, 7, '2024-09-19 02:07:32');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `role`
--

TRUNCATE TABLE `role`;
--
-- Dumping data for table `role`
--

INSERT IGNORE INTO `role` (`id`, `name`) VALUES
(1, 'Branch Manager');

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE IF NOT EXISTS `role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `role_permission`
--

TRUNCATE TABLE `role_permission`;
--
-- Dumping data for table `role_permission`
--

INSERT IGNORE INTO `role_permission` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `threshold`
--

CREATE TABLE IF NOT EXISTS `threshold` (
  `product_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `threshold` float NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`product_id`,`branch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `threshold`
--

TRUNCATE TABLE `threshold`;
--
-- Dumping data for table `threshold`
--

INSERT IGNORE INTO `threshold` (`product_id`, `branch_id`, `threshold`, `created_at`, `updated_at`) VALUES
(1, 1, 10, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(2, 1, 50, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(2, 2, 30, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(3, 1, 100, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(3, 2, 50, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(4, 2, 30, '2024-09-19 02:07:32', '2024-09-19 02:07:32'),
(5, 1, 15, '2024-09-19 02:07:32', '2024-09-19 02:07:32');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_fk_branch_id` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  ADD CONSTRAINT `employee_fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `inventory_ibfk_3` FOREIGN KEY (`batch_no`) REFERENCES `product_batch` (`batch_no`);

--
-- Constraints for table `permission`
--
ALTER TABLE `permission`
  ADD CONSTRAINT `permission_fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `permission_category` (`id`);

--
-- Constraints for table `product_batch`
--
ALTER TABLE `product_batch`
  ADD CONSTRAINT `product_batch_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `product_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_fk_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`),
  ADD CONSTRAINT `role_permission_fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);

--
-- Constraints for table `threshold`
--
ALTER TABLE `threshold`
  ADD CONSTRAINT `threshold_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `threshold_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
