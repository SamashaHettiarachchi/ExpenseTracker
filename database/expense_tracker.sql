-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 03:31 PM
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
-- Database: `expense_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `monthly_limit` decimal(10,2) NOT NULL,
  `month` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-circle',
  `color` varchar(20) DEFAULT '#6c757d',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `color`, `created_at`) VALUES
(1, 'Food & Dining', 'fa-utensils', '#e74c3c', '2025-12-09 17:49:25'),
(2, 'Transportation', 'fa-car', '#3498db', '2025-12-09 17:49:25'),
(3, 'Shopping', 'fa-shopping-cart', '#9b59b6', '2025-12-09 17:49:25'),
(4, 'Entertainment', 'fa-film', '#f39c12', '2025-12-09 17:49:25'),
(5, 'Bills & Utilities', 'fa-file-invoice-dollar', '#e67e22', '2025-12-09 17:49:25'),
(6, 'Healthcare', 'fa-medkit', '#1abc9c', '2025-12-09 17:49:25'),
(7, 'Education', 'fa-graduation-cap', '#34495e', '2025-12-09 17:49:25'),
(8, 'Travel', 'fa-plane', '#16a085', '2025-12-09 17:49:25'),
(9, 'Personal Care', 'fa-heart', '#e91e63', '2025-12-09 17:49:25'),
(10, 'Other', 'fa-ellipsis-h', '#95a5a6', '2025-12-09 17:49:25');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `expense_date` date NOT NULL,
  `payment_method` enum('cash','card','upi','bank_transfer') DEFAULT 'cash',
  `receipt_image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `category_id`, `title`, `amount`, `description`, `expense_date`, `payment_method`, `receipt_image`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Lunch at Restaurant', 850.00, 'Team lunch meeting', '2025-12-09', 'card', NULL, 'approved', '2025-12-09 17:49:25', '2025-12-09 17:49:25'),
(2, 2, 2, 'Uber Ride', 250.00, 'Office to home', '2025-12-09', 'upi', NULL, 'approved', '2025-12-09 17:49:25', '2025-12-09 17:49:25'),
(3, 2, 3, 'Online Shopping', 2500.00, 'Clothes and accessories', '2025-12-08', 'card', NULL, 'approved', '2025-12-09 17:49:25', '2025-12-09 17:49:25'),
(4, 2, 5, 'Electricity Bill', 1200.00, 'Monthly electricity bill', '2025-12-07', 'bank_transfer', NULL, 'approved', '2025-12-09 17:49:25', '2025-12-09 17:49:25'),
(5, 2, 4, 'Movie Tickets', 600.00, 'Weekend movie', '2025-12-06', 'cash', NULL, 'approved', '2025-12-09 17:49:25', '2025-12-09 17:49:25'),
(7, 1, 1, 'Business Lunchn', 1500.00, 'Client meeting at restaurant', '2025-12-10', 'card', NULL, 'approved', '2025-12-10 04:26:51', '2025-12-10 11:37:10'),
(8, 1, 2, 'Taxi to Airport', 450.00, 'Business trip transportation', '2025-12-10', 'cash', NULL, 'approved', '2025-12-10 04:26:51', '2025-12-10 04:26:51'),
(9, 1, 3, 'Office Supplies', 3200.00, 'Printer and stationery', '2025-12-09', 'card', NULL, 'approved', '2025-12-10 04:26:51', '2025-12-10 04:26:51'),
(10, 1, 5, 'Internet Bill', 999.00, 'Monthly broadband', '2025-12-08', 'bank_transfer', NULL, 'approved', '2025-12-10 04:26:51', '2025-12-10 04:26:51'),
(11, 1, 8, 'Health Insurance', 5000.00, 'Quarterly premium', '2025-12-05', 'bank_transfer', NULL, 'approved', '2025-12-10 04:26:51', '2025-12-10 04:26:51'),
(12, 1, 6, 'Gym Membership', 2500.00, 'Annual membership', '2025-12-03', 'card', NULL, 'approved', '2025-12-10 04:26:51', '2025-12-10 04:26:51'),
(13, 1, 4, 'Concert Tickets', 1800.00, 'Weekend entertainment', '2025-11-30', 'upi', NULL, 'approved', '2025-12-10 04:26:51', '2025-12-10 04:26:51'),
(14, 2, 6, 'swd,s', 200.00, 'mdkwms', '2025-12-10', 'card', 'img_693917bc48ec07.19249332_1765349308.pdf', 'approved', '2025-12-10 06:48:28', '2025-12-10 06:48:28'),
(15, 2, 10, 'gbioj', 1000.00, 'bjkj', '2025-12-10', 'upi', NULL, 'approved', '2025-12-10 11:49:01', '2025-12-10 11:49:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT 'default-avatar.png',
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `profile_pic`, `role`, `created_at`) VALUES
(1, 'Admin User', 'admin@expense.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default-avatar.png', 'admin', '2025-12-09 17:49:25'),
(2, 'John Doe', 'user@expense.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default-avatar.png', 'user', '2025-12-09 17:49:25');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_category_expenses`
-- (See below for the actual view)
--
CREATE TABLE `v_category_expenses` (
`user_id` int(11)
,`category_name` varchar(50)
,`category_color` varchar(20)
,`total_amount` decimal(32,2)
,`expense_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_monthly_expenses`
-- (See below for the actual view)
--
CREATE TABLE `v_monthly_expenses` (
`user_id` int(11)
,`year` int(4)
,`month` int(2)
,`total_amount` decimal(32,2)
,`total_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure for view `v_category_expenses`
--
DROP TABLE IF EXISTS `v_category_expenses`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_category_expenses`  AS SELECT `e`.`user_id` AS `user_id`, `c`.`name` AS `category_name`, `c`.`color` AS `category_color`, sum(`e`.`amount`) AS `total_amount`, count(0) AS `expense_count` FROM (`expenses` `e` join `categories` `c` on(`e`.`category_id` = `c`.`id`)) WHERE `e`.`status` = 'approved' GROUP BY `e`.`user_id`, `c`.`id`, `c`.`name`, `c`.`color` ;

-- --------------------------------------------------------

--
-- Structure for view `v_monthly_expenses`
--
DROP TABLE IF EXISTS `v_monthly_expenses`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_monthly_expenses`  AS SELECT `expenses`.`user_id` AS `user_id`, year(`expenses`.`expense_date`) AS `year`, month(`expenses`.`expense_date`) AS `month`, sum(`expenses`.`amount`) AS `total_amount`, count(0) AS `total_count` FROM `expenses` WHERE `expenses`.`status` = 'approved' GROUP BY `expenses`.`user_id`, year(`expenses`.`expense_date`), month(`expenses`.`expense_date`) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_category_month` (`user_id`,`category_id`,`month`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_month` (`month`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_expense_date` (`expense_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
