-- phpMyAdmin SQL Dump
-- version 5.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tasklyDB`
--
CREATE DATABASE IF NOT EXISTS `tasklyDB` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `tasklyDB`;

-- --------------------------------------------------------
-- Table structure for table `tasks`
-- --------------------------------------------------------

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `task` varchar(255) NOT NULL,
  `importance` enum('important','normal','peu_important') NOT NULL,
  `description` text DEFAULT NULL,
  `deadLine` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `isDone` tinyint(1) NOT NULL DEFAULT 0,
  `isSchool` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `user_tokens`
-- --------------------------------------------------------

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------
-- Indexes
-- --------------------------------------------------------

ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_importance` (`importance`),
  ADD KEY `idx_due_date` (`deadLine`),
  ADD KEY `idx_user_id` (`user_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

-- --------------------------------------------------------
-- AUTO_INCREMENT
-- --------------------------------------------------------

ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `user_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- --------------------------------------------------------
-- Constraints
-- --------------------------------------------------------

ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

COMMIT;
