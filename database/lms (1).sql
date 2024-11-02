-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2024 at 05:59 AM
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
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `created_at`) VALUES
(1, 'វិទ្យសាស្រ្តកុំព្យូទ័រ', '2024-10-12 02:35:58'),
(2, 'វិទ្យាសាស្រ្តសត្វ', '2024-10-12 02:46:49'),
(3, 'អគ្គីសនី', '2024-10-12 02:53:32');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fromDate` date DEFAULT NULL,
  `toDate` date DEFAULT NULL,
  `total_days` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_send` datetime NOT NULL DEFAULT current_timestamp(),
  `comment` varchar(100) NOT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `user_id`, `fromDate`, `toDate`, `total_days`, `reason`, `status`, `approved_by`, `department_id`, `updated_at`, `date_send`, `comment`, `note`) VALUES
(119, 8, '2023-10-01', '2024-10-03', 3, 'go to home', 'អនុញ្ញាត', 6, 1, '2024-10-21 03:36:56', '2024-10-16 21:43:30', 'oo', NULL),
(123, 8, '2024-10-16', '2024-10-17', 2, 'go to home', 'អនុញ្ញាត', 6, 1, '2024-10-18 10:57:37', '2024-10-16 23:12:19', 'no no', NULL),
(126, 8, '2024-10-25', '2024-10-27', 3, 'g', 'អនុញ្ញាត', 6, 2, '2024-10-18 08:40:34', '2024-10-17 07:01:16', 'ដថាថ', NULL),
(134, 10, '2024-10-18', '2024-10-20', 3, 'sdad', 'កំពុងរងចាំ', NULL, 2, '2024-10-18 11:02:18', '2024-10-18 18:02:18', '', NULL),
(135, 10, '2024-09-10', '2024-10-02', 3, 'dd', 'កំពុងរងចាំ', NULL, 2, '2024-10-21 03:25:27', '2024-10-18 18:02:44', '', NULL),
(136, 8, '2024-10-20', '2024-10-22', 3, 'go to home', 'បោះបង់', NULL, 1, '2024-10-21 01:06:43', '2024-10-20 22:16:35', '', NULL),
(137, 8, '2025-01-21', '2025-01-23', 3, 'dd', 'អនុញ្ញាត', NULL, 1, '2024-10-20 16:36:47', '2024-10-20 23:35:17', 'ok ok', NULL),
(138, 8, '2024-10-21', '2024-11-02', 3, 'go to n home', 'បោះបង់', NULL, 1, '2024-10-21 05:01:28', '2024-10-21 11:58:38', '', NULL),
(139, 8, '2024-10-23', '2024-10-23', 3, 'go to home', 'បដិសេធ', NULL, 1, '2024-10-23 10:33:37', '2024-10-22 09:10:22', 'no no', NULL),
(140, 8, '2024-11-08', '2024-11-09', 2, 'gg', 'កំពុងរងចាំ', NULL, 1, '2024-10-23 10:39:03', '2024-10-23 17:39:03', '', NULL),
(141, 8, '2024-11-30', '2024-12-02', 3, '222', 'កំពុងរងចាំ', NULL, 1, '2024-10-23 13:42:57', '2024-10-23 20:42:57', '', NULL),
(142, 8, '2025-01-02', '2025-01-04', 3, 'fsgsgsrgtsrtgfs', 'កំពុងរងចាំ', NULL, 1, '2024-10-23 13:43:35', '2024-10-23 20:43:35', '', NULL),
(143, 8, '2024-11-14', '2024-11-15', 2, 'go', 'កំពុងរងចាំ', NULL, 1, '2024-10-23 14:18:40', '2024-10-23 21:18:40', '', NULL),
(144, 8, '2024-11-16', '2024-11-17', 2, 'rfrse', 'កំពុងរងចាំ', NULL, 1, '2024-10-23 17:13:18', '2024-10-24 00:13:18', '', NULL),
(145, 8, '2024-11-26', '2024-11-27', 2, 'ddada', 'កំពុងរងចាំ', NULL, 1, '2024-10-23 17:14:56', '2024-10-24 00:14:56', '', NULL),
(146, 8, '2025-02-26', '2025-02-28', 3, '23123', 'កំពុងរងចាំ', NULL, 1, '2024-10-23 17:18:40', '2024-10-24 00:18:40', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `telegram_data`
--

CREATE TABLE `telegram_data` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `chat_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `telegram_data`
--

INSERT INTO `telegram_data` (`id`, `token`, `chat_id`, `created_at`) VALUES
(1, '7134281925:AAGKHJmwYGOsVOtNhBci74FZedERUlSMfgE', '-4587418804', '2024-10-23 14:33:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','staff','admin') NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`user_id`, `first_name`, `last_name`, `username`, `password`, `role`, `department_id`, `email`, `phone_number`, `image_url`, `created_at`, `status`) VALUES
(4, 'ម៉េត', 'តុលា', 'admin', '$2y$10$oCIzkeXm5gMSW9Qugc./xuZ5Wh0mrrL7R61HWSGFyEQUh.4.7RK6.', 'admin', 2, 'bongtola618@gmail.com', '85599216433', '../../uploads/photo_2024-04-24_01-06-04.jpg', '2024-09-10 05:36:25', 1),
(6, 'ម៉េត', 'តុលា', 'staff', '$2y$10$hjpJfrxjnvV83hx4re7AL.wkFoSaYwph6C0kA1HrxS80ZkMCy1JxW', 'staff', 2, 'bongtola615@gmail.com', '០99216433', '../../uploads/photo_2024-04-24_01-06-04.jpg', '2024-09-10 08:56:07', 1),
(8, 'ម៉េត', 'តុលា', 'user', '$2y$10$n6COqMY.tkSXT0usUImx0eb/A7w0P8MyF/9/1NmTiDvfnGrNo82Wq', 'user', 1, 'bongtola619@gmail.com', '8559921643322', '../../uploads/photo_2024-04-24_01-06-04.jpg', '2024-09-11 08:05:58', 1),
(10, 'ហួ', 'ចដ្ឋា', 'chantha', '$2y$10$aRhaqNtlOKlj9eiElpu72uoU53RkewY7Blvz4MM6A2tbIgpxKSTTW', 'user', 2, 'bongtola61a8@gmail.com', '8559921643311', '../../uploads/photo_2024-04-24_01-06-04.jpg', '2024-09-19 05:06:54', 1),
(19, 'MET', 'TOLA', 'user2', '$2y$10$0qkF7avRLtN7blHY93nDO.77uRG1GEwoGnf7iUUf5TMz8H68S1cOS', 'user', 1, 'bonseu31423@gmail.com', '85599216433222', '../../uploads/399250250_1151738312877468_4928043359205886634_n.jpg', '2024-10-17 06:46:57', 1),
(20, 'MET', 'TOLA', 'user3', '$2y$10$0w0aKCd8hRoDOI5UcmMSgeuCj3MwnF6uRe14Xtxa2eas0.B1T76.S', 'user', 1, 'bontola6124238@gmail.com', '85599216433222', '../../uploads/1.jpg', '2024-10-17 06:53:44', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `user_id` (`user_id`) USING BTREE,
  ADD KEY `idx_department_id` (`department_id`);

--
-- Indexes for table `telegram_data`
--
ALTER TABLE `telegram_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `telegram_data`
--
ALTER TABLE `telegram_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `fk_leave_requests_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`user_id`),
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `user_info` (`user_id`);

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `fk_user_info_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
