-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para db_arquitectura_base
CREATE DATABASE IF NOT EXISTS `db_arquitectura_base` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_arquitectura_base`;

-- Volcando estructura para tabla db_arquitectura_base.companies
CREATE TABLE IF NOT EXISTS `companies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `favicon_id` int DEFAULT NULL,
  `logo_id` int DEFAULT NULL,
  `terms_conditions` longtext COLLATE utf8mb4_general_ci,
  `privacy_policies` longtext COLLATE utf8mb4_general_ci,
  `host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `host_client` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `mailer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mailer_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mailer_username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mailer_host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK1_favicon_sf` (`favicon_id`),
  KEY `FK2_logo-sf` (`logo_id`),
  CONSTRAINT `FK1_favicon_sf` FOREIGN KEY (`favicon_id`) REFERENCES `storage_files` (`id`),
  CONSTRAINT `FK2_logo-sf` FOREIGN KEY (`logo_id`) REFERENCES `storage_files` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_arquitectura_base.companies: ~2 rows (aproximadamente)
DELETE FROM `companies`;
INSERT INTO `companies` (`id`, `name`, `favicon_id`, `logo_id`, `terms_conditions`, `privacy_policies`, `host`, `host_client`, `status`, `mailer_name`, `mailer_password`, `mailer_username`, `mailer_host`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(4, 'Sodexo ', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2026-03-18 15:48:30', '2026-03-18 15:48:30', NULL),
	(5, 'La Positiva', NULL, NULL, 'omar davis sequen coonad', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2026-03-18 15:48:57', '2026-03-20 13:28:52', NULL);

-- Volcando estructura para tabla db_arquitectura_base.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `permission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_arquitectura_base.permissions: ~2 rows (aproximadamente)
DELETE FROM `permissions`;
INSERT INTO `permissions` (`id`, `name`, `permission`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(6, 'Admin.', 'administrator', 1, '2025-06-12 17:31:00', '2025-06-12 17:32:31', NULL),
	(7, 'Colab.', 'collaborator', 1, '2025-06-12 17:31:11', '2025-06-12 17:32:43', NULL);

-- Volcando estructura para tabla db_arquitectura_base.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_arquitectura_base.roles: ~2 rows (aproximadamente)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(5, 'Administrador', 1, '2025-06-12 17:27:11', '2025-06-12 17:27:11', NULL),
	(6, 'invitado', 1, '2025-06-12 17:27:26', '2025-06-12 17:27:26', NULL);

-- Volcando estructura para tabla db_arquitectura_base.role_permission
CREATE TABLE IF NOT EXISTS `role_permission` (
  `role_id` int unsigned NOT NULL,
  `permission_id` int unsigned NOT NULL,
  `permission` int DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`role_id`,`permission_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_arquitectura_base.role_permission: ~3 rows (aproximadamente)
DELETE FROM `role_permission`;
INSERT INTO `role_permission` (`role_id`, `permission_id`, `permission`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(5, 6, 1, '2025-06-12 17:35:28', '2025-06-12 17:35:28', NULL),
	(5, 7, 1, '2025-06-12 17:35:40', '2025-06-12 17:35:40', NULL),
	(6, 7, 1, '2025-06-12 17:35:52', '2025-06-12 17:35:52', NULL);

-- Volcando estructura para tabla db_arquitectura_base.storage_files
CREATE TABLE IF NOT EXISTS `storage_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  ` company_id` int DEFAULT NULL,
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `type` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `size_b` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `size` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `format` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `embedded` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `folder` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `uri` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `bucket` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `upload_file_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `uploaded_file` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1384 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_arquitectura_base.storage_files: ~0 rows (aproximadamente)
DELETE FROM `storage_files`;

-- Volcando estructura para tabla db_arquitectura_base.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `foto_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `paternal_surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `maternal_surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `users_FK1` (`foto_id`) USING BTREE,
  CONSTRAINT `users_FK1` FOREIGN KEY (`foto_id`) REFERENCES `storage_files` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_arquitectura_base.users: ~2 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `foto_id`, `name`, `paternal_surname`, `maternal_surname`, `username`, `email`, `password`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(38, 1383, 'Brian Arturo', 'Coronado', 'Nizama', 'brian', 'omarsc@gmail.com', '$2y$10$xgmK7Nlc34AR1WmqwVn8teNNBPvw6.9byqeStOT7Ay8PJj.07B1JC', 1, '2025-06-12 17:34:03', '2025-07-10 17:31:15', NULL),
	(39, 1382, 'Nicolas', 'Cotrina', 'Llontop', 'nico', 'stafano@gmail.com', '$2y$10$xgmK7Nlc34AR1WmqwVn8teNNBPvw6.9byqeStOT7Ay8PJj.07B1JC', 1, '2025-06-18 18:08:03', '2025-07-10 17:20:24', NULL);

-- Volcando estructura para tabla db_arquitectura_base.user_company_role
CREATE TABLE IF NOT EXISTS `user_company_role` (
  `user_id` int unsigned NOT NULL,
  `company_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`,`role_id`,`company_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_arquitectura_base.user_company_role: ~2 rows (aproximadamente)
DELETE FROM `user_company_role`;
INSERT INTO `user_company_role` (`user_id`, `company_id`, `role_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(38, 4, 5, '2025-06-14 08:57:52', '2026-03-18 15:48:39', NULL),
	(39, 5, 6, '2025-06-18 18:08:51', '2026-03-18 15:48:47', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
