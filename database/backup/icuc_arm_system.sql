-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2021 at 01:11 AM
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
-- Database: `icuc_arm_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--


CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `datetime_occured` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `staff_data_id` int(11) DEFAULT NULL,
  `arrival_time` time DEFAULT curtime(),
  `departure_time` time DEFAULT curtime(),
  `date_attended` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `staff_data_id`, `arrival_time`, `departure_time`, `date_attended`) VALUES
(1, 24, '06:19:09', '18:00:00', '2024-05-01'),
(2, 1, '07:59:06', '21:59:06', '2024-03-15'),
(4, 1, '06:04:10', '12:02:12', '2024-03-15'),
(5, 1, '06:04:10', '12:02:12', '2024-04-28'),
(6, 1, '06:04:10', '12:02:12', '2024-03-16');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_excuses`
--

CREATE TABLE `attendance_excuses` (
  `id` int(11) NOT NULL,
  `staff_data_id` int(11) DEFAULT NULL,
  `status` enum('1','2','3') DEFAULT NULL,
  `reason` text NOT NULL,
  `is_cancelled` tinyint(1) DEFAULT NULL,
  `date_created` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance_excuses`
--

INSERT INTO `attendance_excuses` (`id`, `staff_data_id`, `status`, `reason`, `is_cancelled`, `date_created`) VALUES
(1, 1, '2', 'I wasn&#039;t able to attend because of some problems i had to sort, so i wasn&#039;t able to attend', NULL, '2024-03-15'),
(2, 4, '1', 'I wasn&#039;t able to attend because i was sick and i also had to take my son to the hospital', NULL, '2024-03-17'),
(3, 1, '1', 'This is a sample excuse made for testing purposes', NULL, '2024-04-12'),
(4, 4, '1', 'This is a simple reason to offer', NULL, '2021-07-25'),
(5, 1, '2', 'I was absent because of some issues i met', NULL, '2021-07-25'),
(6, 23, '3', 'this is a simple reason i gave', NULL, '2021-07-25'),
(7, 23, '2', 'This is an excuse, today', NULL, '2021-07-25'),
(8, 23, '2', 'This is some excuse here...', NULL, '2021-07-25');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_holidays`
--

CREATE TABLE `attendance_holidays` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `is_recursive` enum('1','2') NOT NULL,
  `description` text NOT NULL,
  `date_created` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance_holidays`
--

INSERT INTO `attendance_holidays` (`id`, `name`, `date`, `is_recursive`, `description`, `date_created`) VALUES
(1, 'Idd Adhuha', '2024-07-15', '2', 'This is Idd Adhuha', '2024-03-15'),
(3, 'Some Holiday Here', '2003-02-28', '1', 'Some Description', '2024-04-30');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `time_created` time DEFAULT curtime(),
  `date_created` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `body`, `user_id`, `is_read`, `time_created`, `date_created`) VALUES
(2, 'This is a notification', 'This is a notification', 24, 1, '04:45:20', '2024-04-18'),
(3, 'Another Notification', 'This is another notification i created for test reasons', 18, 1, '04:45:20', '2024-03-16'),
(4, 'New Notification', 'This is a new notification made today.', 24, 1, '04:45:20', '2024-03-17'),
(5, 'Another New Notification', 'This is another new notification created by me', 24, 1, '04:45:20', '2024-03-17'),
(6, 'My Notification', 'This is my notification', 24, 1, '04:45:20', '2024-03-17'),
(7, 'This is another notification', 'I created a notification my self', 24, 1, '04:45:20', '2024-03-29'),
(8, 'All Staff Members Report', 'Please, all staff members, you must report today, right now', 38, 1, '04:45:20', '2024-03-29'),
(9, 'This is an alert', 'This is an alert', 24, 1, '04:45:20', '2024-04-11'),
(11, 'Some Notification', 'This is a notification i made by me', 24, 1, '04:45:20', '2024-04-29'),
(12, 'Notification #1', 'Notification #1', 24, 1, '04:45:20', '2024-04-29');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role`) VALUES
(1, 'System Administrator'),
(2, 'Staff Member');

-- --------------------------------------------------------

--
-- Table structure for table `staff_data`
--

CREATE TABLE `staff_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_data`
--

INSERT INTO `staff_data` (`id`, `user_id`, `first_name`, `last_name`, `date_created`) VALUES
(1, 45, 'Ismael', 'Swaleh', '2023-12-03 23:14:26'),
(4, 13, 'Mudebo', 'Awazi', '2023-12-09 19:41:08'),
(23, 14, 'Rayan', 'Magomu', '2021-07-25 10:28:02'),
(24, 42, 'Gilbert', 'Bukenya', '2021-07-25 10:28:30'),
(25, 17, 'Another', 'Human', '2021-07-25 10:28:53'),
(26, 24, 'Plexan', 'Tech', '2024-05-11 02:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `staff_data_images`
--

CREATE TABLE `staff_data_images` (
  `id` int(11) NOT NULL,
  `data_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_data_images`
--

INSERT INTO `staff_data_images` (`id`, `data_id`, `name`, `date_created`) VALUES
(4, 4, 'ICUC-65d7a1b80f600-2024_02_22.jpg', '2024-02-22 22:34:16'),
(11, 1, 'ICUC-65dac7ddc502a-2024_02_25.jpg', '2024-02-25 07:53:49'),
(12, 1, 'ICUC-65dac7f0ace4a-2024_02_25.jpg', '2024-02-25 07:54:08'),
(13, 1, 'ICUC-65dac7f0ea9d6-2024_02_25.jpg', '2024-02-25 07:54:08'),
(14, 1, 'ICUC-65dac7f11ba86-2024_02_25.jpg', '2024-02-25 07:54:09'),
(25, 4, 'ICUC-65db7c961fc7b-2024_02_25.jpg', '2024-02-25 20:44:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `uniqid` int(6) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `status` int(10) NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `dept_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `uniqid`, `phone_number`, `avatar`, `status`, `last_login`, `date_created`, `dept_id`, `role_id`) VALUES
(5, 'user3', 'user3@example.com', '$2y$10$a044.EOWUSHoZsN4rf.IgujtDQaSXAFV40qcMyQt.EVxHgvFYRihi', 343858, '0752260204', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:07:24', 7, 2),
(8, 'user5', 'user5@example.com', '$2y$10$1vVutChOoXtZ2tLjFWAoROwC6XWbp95JkO5MVJ6zCFL2qvO8PmW22', 445546, '0752260204', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', 9, 2),
(9, 'user6', 'user6@example.com', '$2y$10$/GCWmUIrvcqJk9nPHpI1M.891QYvFgBl4XiH2RDbPa3TjqzPs4Bdq', 676556, '1234567895', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', 8, 2),
(12, 'user9', 'user9@example.com', 'somepassword@2023', 153455, '0752260204', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', NULL, 2),
(13, 'user10', 'user10@example.com', 'password10', 676744, '1234567899', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', 7, 2),
(14, 'user11', 'user11@example.com', 'password11', 453643, '1234567800', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', 7, 2),
(15, 'user12', 'user12@example.com', 'password12', 865775, '1234567801', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', NULL, 2),
(16, 'user13', 'awamuds@gmail.com', 'password13', 456566, '1234567802', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', NULL, 2),
(17, 'user14', 'user14@example.com', 'password14', 764545, '1234567803', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', NULL, 2),
(18, 'user15', 'user15@example.com', 'password15', 564566, '1234567804', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', NULL, 2),
(19, 'user16', 'user16@example.com', 'password16', 865543, '1234567805', '', 0, '2023-12-19 15:22:12', '2023-11-28 05:11:32', NULL, 2),
(24, 'Ismael', 'swalehismael144@gmail.com', '$2y$10$I0f92f79OxU2dSMmxSY/mOgmTAR8.uq/FxlnyaDvt4PKa4DO1pa9y', 656434, '0776960402', 'ICUC-662ea2e14ad8f-2024_04_28.jpg', 1, '2021-07-25 00:11:19', '2023-11-28 05:19:13', 7, 2),
(34, 'Ismael1235', 'email@gmail.com', 'Ismael@2022', 888411, '0752260204', '', 0, '2023-12-19 15:22:12', '2023-12-14 14:36:37', NULL, 2),
(35, 'Rayan1122', 'rayan1122@gmail.com', 'rayan@2023', 908330, '0752260204', '', 0, '2023-12-19 15:22:12', '2023-12-14 14:38:37', NULL, 2),
(38, 'Ismael1212', 'swalehismael13k23@jfs.fdfj', '$2y$10$zp484o.Y3Jl7RI9tzTIYMuM3AilO4KabATIilM5/oJ3eIDf5KLGp2', 361077, '0752260204', '', 0, '2023-12-19 15:22:12', '2023-12-14 20:32:39', NULL, 2),
(41, 'someuser', 'someuser@gmail.com', '$2y$10$4wB2we.nO0DykeBLII67ke98TtwYBEvHMUHQMN4QhoONwhPhxmhMq', 965755, '0754345344', '', 0, '2023-12-19 15:22:12', '2023-12-14 20:59:46', NULL, 2),
(42, 'someuser1', 'someuser1@gmail.com', '$2y$10$aiXPpp/GbEb8zmMSdI9Ebe2DXOyKd05kBiAySV9ufVVgufPBUfLya', 676413, '0752260204', '', 0, '2023-12-19 15:22:12', '2023-12-14 21:00:27', NULL, 2),
(45, 'someuser111', 'someuser111@gmail.com', '$2y$10$xpUuH2zK9tA04NThoPYa/OQeOzD2hLEKUH.H2STQnob3HbDBv919.', 273021, '0752260204', '', 0, '2023-12-19 15:22:12', '2023-12-14 21:40:09', 7, 2),
(46, 'someuser123', 'someuser1234@gmail.com', '$2y$10$bYyPvFiY5FiKVxZ4f.44OeEh4JMpW2GPwR4ek49MopcMrjqUMlkUO', 167771, '0752260204', '', 0, '2023-12-19 15:22:12', '2023-12-14 21:41:22', NULL, 2),
(47, 'Ismael121212', 'swalehismael1212@gmail.com', '$2y$10$i6OlC89R1N6AwQWHHBNI8uK.hBInmw.tqJ1Ybt0tZSl9ATmoc3Mja', 868914, '0752260204', '', 0, NULL, '2023-12-24 19:59:15', NULL, 2),
(48, 'Ismael12222', 'swalehismael14412122@gmail.com', '$2y$10$JAyvg0miufiTX8zrRpOJleoYYabnLDqK/6VaZ0W.JDusk0gIFFjG.', 879002, '0752260204', '', 0, '0000-00-00 00:00:00', '2023-12-24 23:04:49', NULL, 2),
(49, 'somerandomuser', 'somerandomuser@gmail.com', '$2y$10$HdlbG8kYEr/ZblZbI/32EuJ1IG4U0gk.HBu5bcZOcoEokSlnutane', 110143, '0752260204', '', 0, '0000-00-00 00:00:00', '2023-12-24 23:06:07', NULL, 2),
(50, 'somerandomusername', 'somernaddfjJ@Jklf.dfl', '$2y$10$9UUiIIt0zhUHZZt32DVtgOwWlV2ONs26bUngfkQKJ6V..sY9ndNW6', 657448, '0756545234', '', 0, NULL, '2024-02-20 10:38:44', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_departments`
--

CREATE TABLE `user_departments` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `date_created` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_departments`
--

INSERT INTO `user_departments` (`id`, `name`, `description`, `date_created`) VALUES
(1, 'Health', 'This is  a sample description', '2024-04-25'),
(7, 'another dept', 'another dept', '2024-04-25'),
(8, 'Department Of Health', '', '2024-04-25'),
(9, 'Internal Affairs', 'This is the department of internal affairs', '2024-04-26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_data_id` (`staff_data_id`);

--
-- Indexes for table `attendance_excuses`
--
ALTER TABLE `attendance_excuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_staff_data_id` (`staff_data_id`);

--
-- Indexes for table `attendance_holidays`
--
ALTER TABLE `attendance_holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_data`
--
ALTER TABLE `staff_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `staff_data_images`
--
ALTER TABLE `staff_data_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_id` (`data_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `fk_dept_id` (`dept_id`);

--
-- Indexes for table `user_departments`
--
ALTER TABLE `user_departments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `attendance_excuses`
--
ALTER TABLE `attendance_excuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `attendance_holidays`
--
ALTER TABLE `attendance_holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff_data`
--
ALTER TABLE `staff_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `staff_data_images`
--
ALTER TABLE `staff_data_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `user_departments`
--
ALTER TABLE `user_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`staff_data_id`) REFERENCES `staff_data` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_excuses`
--
ALTER TABLE `attendance_excuses`
  ADD CONSTRAINT `fk_staff_data_id` FOREIGN KEY (`staff_data_id`) REFERENCES `staff_data` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `staff_data`
--
ALTER TABLE `staff_data`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_data_images`
--
ALTER TABLE `staff_data_images`
  ADD CONSTRAINT `fk_data_id` FOREIGN KEY (`data_id`) REFERENCES `staff_data` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `staff_data` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_dept_id` FOREIGN KEY (`dept_id`) REFERENCES `user_departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
