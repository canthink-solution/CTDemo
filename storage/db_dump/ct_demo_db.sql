-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 01, 2024 at 12:46 PM
-- Server version: 8.0.31
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ct_demo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `entity_address`
--

DROP TABLE IF EXISTS `entity_address`;
CREATE TABLE IF NOT EXISTS `entity_address` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `entity_id` bigint UNSIGNED DEFAULT NULL,
  `entity_address_type` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `state_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `postcode` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entity_files`
--

DROP TABLE IF EXISTS `entity_files`;
CREATE TABLE IF NOT EXISTS `entity_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `files_name` varchar(255) DEFAULT NULL,
  `files_original_name` varchar(255) DEFAULT NULL,
  `files_type` varchar(50) DEFAULT NULL,
  `files_mime` varchar(50) DEFAULT NULL,
  `files_extension` varchar(10) DEFAULT NULL,
  `files_size` int DEFAULT '0',
  `files_compression` tinyint(1) DEFAULT NULL COMMENT '1-full size only, 2-full size & compressed, 3-full size, compressed & thumbnail ',
  `files_folder` varchar(255) DEFAULT NULL,
  `files_path` varchar(255) DEFAULT NULL,
  `files_disk_storage` varchar(20) DEFAULT 'public' COMMENT 'Default : public',
  `files_path_is_url` tinyint(1) DEFAULT '0' COMMENT '0-No, 1-Yes (Default : 0)',
  `files_description` text,
  `entity_type` varchar(255) DEFAULT NULL,
  `entity_id` int DEFAULT NULL,
  `entity_file_type` varchar(255) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `entity_files`
--

INSERT INTO `entity_files` (`id`, `files_name`, `files_original_name`, `files_type`, `files_mime`, `files_extension`, `files_size`, `files_compression`, `files_folder`, `files_path`, `files_disk_storage`, `files_path_is_url`, `files_description`, `entity_type`, `entity_id`, `entity_file_type`, `user_id`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'public', 0, NULL, 'User_model', 1, 'PROFILE_HEADER_PHOTO', NULL, NULL, NULL),
(2, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'public', 0, NULL, 'User_model', 1, 'PROFILE_PHOTO', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `master_roles`
--

DROP TABLE IF EXISTS `master_roles`;
CREATE TABLE IF NOT EXISTS `master_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL,
  `role_status` tinyint DEFAULT NULL COMMENT '0-Inactive, 1-Active ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `master_roles`
--

INSERT INTO `master_roles` (`id`, `role_name`, `role_status`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 1, '2024-02-12 15:26:13', NULL),
(2, 'Administrator', 1, '2024-02-12 15:26:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_abilities`
--

DROP TABLE IF EXISTS `system_abilities`;
CREATE TABLE IF NOT EXISTS `system_abilities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_abilities`
--

INSERT INTO `system_abilities` (`id`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, '*', 'All abilities', '2024-02-13 15:18:10', NULL),
(2, 'directory-staff-view', 'Directory Staff View', '2024-02-13 15:18:10', NULL),
(3, 'directory-staff-add', 'Directory Staff Add', '2024-02-13 15:18:10', NULL),
(4, 'directory-staff-edit', 'Directory Staff Edit', '2024-02-13 15:18:10', NULL),
(5, 'directory-staff-delete', 'Directory Staff Delete', '2024-02-13 15:18:10', NULL),
(6, 'profile-view', 'Profile View', '2024-02-13 15:28:51', NULL),
(7, 'profile-edit', 'Profile Edit', '2024-02-13 15:28:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_audit_trails`
--

DROP TABLE IF EXISTS `system_audit_trails`;
CREATE TABLE IF NOT EXISTS `system_audit_trails` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Refer table users',
  `role_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Refer table master_role',
  `user_fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `event` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `table_name` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `url` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip_address` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_backup_db`
--

DROP TABLE IF EXISTS `system_backup_db`;
CREATE TABLE IF NOT EXISTS `system_backup_db` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `backup_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `backup_storage_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'local',
  `backup_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_permission`
--

DROP TABLE IF EXISTS `system_permission`;
CREATE TABLE IF NOT EXISTS `system_permission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int DEFAULT NULL COMMENT 'Refer table master_roles',
  `abilities_id` int DEFAULT NULL COMMENT 'Refer table system_abilities',
  `forbidden` tinyint(1) DEFAULT '1' COMMENT '0-No, 1-Yes (Default : 1)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_roles` (`role_id`),
  KEY `permission_abilities` (`abilities_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_permission`
--

INSERT INTO `system_permission` (`id`, `role_id`, `abilities_id`, `forbidden`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, '2024-02-13 15:24:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `user_preferred_name` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_gender` tinyint DEFAULT NULL COMMENT 'Refer constants file GenderStatus',
  `user_dob` date DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_status` tinyint DEFAULT '4' COMMENT '0-Inactive, 1-Active, 2-Suspended, 3-Deleted, 4-Unverified',
  `remember_token` varchar(255) DEFAULT NULL,
  `first_login` tinyint DEFAULT '1' COMMENT '0-No, 1-Yes',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `user_preferred_name`, `email`, `user_gender`, `user_dob`, `username`, `password`, `user_status`, `remember_token`, `first_login`, `email_verified_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Super Administrator', NULL, 'sadmin@pmnt.com.my', 1, '2024-02-18', 'superadmin', '$2a$12$4quQYbOwLfuwSy85lfJYMOXVrAEFqZPOy.Uq2XAaRXf0Y.gZvnnY2', 1, NULL, 0, NULL, '2024-02-18 12:12:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_login_attempt`
--

DROP TABLE IF EXISTS `users_login_attempt`;
CREATE TABLE IF NOT EXISTS `users_login_attempt` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Refer table users',
  `ip_address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `time` timestamp NULL DEFAULT NULL,
  `user_agent` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_login_history`
--

DROP TABLE IF EXISTS `users_login_history`;
CREATE TABLE IF NOT EXISTS `users_login_history` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Refer table users',
  `ip_address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `login_type` tinyint(1) DEFAULT '1' COMMENT '1-CREDENTIAL, 2-SOCIALITE, 3-TOKEN',
  `operating_system` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `browsers` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `time` timestamp NULL DEFAULT NULL,
  `user_agent` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_password_reset`
--

DROP TABLE IF EXISTS `users_password_reset`;
CREATE TABLE IF NOT EXISTS `users_password_reset` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Refer table users',
  `email` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_expired` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE IF NOT EXISTS `user_profile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL COMMENT 'Refer table users',
  `role_id` int DEFAULT NULL COMMENT 'Refer table master_roles',
  `is_main` tinyint(1) DEFAULT NULL COMMENT '0-No, 1-Yes',
  `profile_status` tinyint(1) DEFAULT NULL COMMENT '0-Inactive, 1-Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id`, `user_id`, `role_id`, `is_main`, `profile_status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, '2024-02-12 15:33:16', NULL, NULL),
(2, 1, 2, 0, 1, '2024-02-12 15:33:33', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `system_permission`
--
ALTER TABLE `system_permission`
  ADD CONSTRAINT `permission_abilities` FOREIGN KEY (`abilities_id`) REFERENCES `system_abilities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_roles` FOREIGN KEY (`role_id`) REFERENCES `master_roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
