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


-- Volcando estructura de base de datos para db_crm_mypes
CREATE DATABASE IF NOT EXISTS `db_crm_mypes` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_crm_mypes`;

-- Volcando estructura para tabla db_crm_mypes.activities
CREATE TABLE IF NOT EXISTS `activities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `opportunity_id` int unsigned DEFAULT NULL,
  `activity_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `activity_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activities_company_index` (`company_id`),
  KEY `activities_user_index` (`user_id`),
  KEY `activities_lead_index` (`lead_id`),
  KEY `activities_customer_index` (`customer_id`),
  KEY `activities_opportunity_index` (`opportunity_id`),
  CONSTRAINT `activities_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `activities_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `activities_lead_fk` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  CONSTRAINT `activities_opportunity_fk` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunities` (`id`),
  CONSTRAINT `activities_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.activities: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.companies
CREATE TABLE IF NOT EXISTS `companies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `favicon_id` int DEFAULT NULL,
  `logo_id` int DEFAULT NULL,
  `terms_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `privacy_policies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
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
  KEY `FK1_favicon_sf` (`favicon_id`) USING BTREE,
  KEY `FK2_logo-sf` (`logo_id`) USING BTREE,
  CONSTRAINT `FK1_favicon_sf` FOREIGN KEY (`favicon_id`) REFERENCES `storage_files` (`id`),
  CONSTRAINT `FK2_logo-sf` FOREIGN KEY (`logo_id`) REFERENCES `storage_files` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.companies: ~2 rows (aproximadamente)
INSERT INTO `companies` (`id`, `name`, `favicon_id`, `logo_id`, `terms_conditions`, `privacy_policies`, `host`, `host_client`, `status`, `mailer_name`, `mailer_password`, `mailer_username`, `mailer_host`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(4, 'Sodexo ', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2026-03-18 15:48:30', '2026-03-18 15:48:30', NULL),
	(5, 'La Positiva', NULL, NULL, 'omar davis sequen coonad', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2026-03-18 15:48:57', '2026-03-20 13:28:52', NULL);

-- Volcando estructura para tabla db_crm_mypes.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `assigned_user_id` int unsigned DEFAULT NULL,
  `customer_type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'PERSON',
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `document_type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `document_number` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `whatsapp` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `source` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_company_id_index` (`company_id`),
  KEY `customers_assigned_user_id_index` (`assigned_user_id`),
  KEY `customers_document_number_index` (`document_number`),
  CONSTRAINT `customers_assigned_user_fk` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `customers_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.customers: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.customer_contacts
CREATE TABLE IF NOT EXISTS `customer_contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `customer_id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `whatsapp` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_general_ci,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_contacts_company_index` (`company_id`),
  KEY `customer_contacts_customer_index` (`customer_id`),
  CONSTRAINT `customer_contacts_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `customer_contacts_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.customer_contacts: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.leads
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `assigned_user_id` int unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `whatsapp` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `source` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `interest` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estimated_value` decimal(12,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `lead_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NEW',
  `converted` tinyint(1) NOT NULL DEFAULT '0',
  `converted_customer_id` int unsigned DEFAULT NULL,
  `converted_at` datetime DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leads_company_index` (`company_id`),
  KEY `leads_assigned_user_index` (`assigned_user_id`),
  KEY `leads_status_index` (`lead_status`),
  KEY `leads_converted_customer_fk` (`converted_customer_id`),
  CONSTRAINT `leads_assigned_user_fk` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `leads_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `leads_converted_customer_fk` FOREIGN KEY (`converted_customer_id`) REFERENCES `customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.leads: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.opportunities
CREATE TABLE IF NOT EXISTS `opportunities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `pipeline_id` int unsigned NOT NULL,
  `pipeline_stage_id` int unsigned NOT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `assigned_user_id` int unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `estimated_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `probability` decimal(5,2) DEFAULT NULL,
  `expected_close_date` date DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `lost_reason` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lost_notes` text COLLATE utf8mb4_general_ci,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `opportunities_company_index` (`company_id`),
  KEY `opportunities_pipeline_index` (`pipeline_id`),
  KEY `opportunities_stage_index` (`pipeline_stage_id`),
  KEY `opportunities_lead_index` (`lead_id`),
  KEY `opportunities_customer_index` (`customer_id`),
  KEY `opportunities_user_index` (`assigned_user_id`),
  CONSTRAINT `opportunities_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `opportunities_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `opportunities_lead_fk` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  CONSTRAINT `opportunities_pipeline_fk` FOREIGN KEY (`pipeline_id`) REFERENCES `pipelines` (`id`),
  CONSTRAINT `opportunities_stage_fk` FOREIGN KEY (`pipeline_stage_id`) REFERENCES `pipeline_stages` (`id`),
  CONSTRAINT `opportunities_user_fk` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.opportunities: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.permissions
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

-- Volcando datos para la tabla db_crm_mypes.permissions: ~2 rows (aproximadamente)
INSERT INTO `permissions` (`id`, `name`, `permission`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(6, 'Admin.', 'administrator', 1, '2025-06-12 17:31:00', '2025-06-12 17:32:31', NULL),
	(7, 'Colab.', 'collaborator', 1, '2025-06-12 17:31:11', '2025-06-12 17:32:43', NULL);

-- Volcando estructura para tabla db_crm_mypes.pipelines
CREATE TABLE IF NOT EXISTS `pipelines` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pipelines_company_index` (`company_id`),
  CONSTRAINT `pipelines_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.pipelines: ~0 rows (aproximadamente)
INSERT INTO `pipelines` (`id`, `company_id`, `name`, `description`, `is_default`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 4, 'Ventas', 'Pipeline principal de ventas', 1, 1, '2026-08-13 20:29:59', '2026-08-13 20:29:59', NULL);

-- Volcando estructura para tabla db_crm_mypes.pipeline_stages
CREATE TABLE IF NOT EXISTS `pipeline_stages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `pipeline_id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stage_key` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `position` int NOT NULL DEFAULT '0',
  `probability` decimal(5,2) DEFAULT NULL,
  `is_won` tinyint(1) NOT NULL DEFAULT '0',
  `is_lost` tinyint(1) NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pipeline_stages_company_index` (`company_id`),
  KEY `pipeline_stages_pipeline_index` (`pipeline_id`),
  CONSTRAINT `pipeline_stages_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `pipeline_stages_pipeline_fk` FOREIGN KEY (`pipeline_id`) REFERENCES `pipelines` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.pipeline_stages: ~0 rows (aproximadamente)
INSERT INTO `pipeline_stages` (`id`, `company_id`, `pipeline_id`, `name`, `stage_key`, `position`, `probability`, `is_won`, `is_lost`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, 'Nuevo', 'NEW', 1, 10.00, 0, 0, 1, '2026-08-13 20:30:23', '2026-08-13 20:30:23', NULL),
	(2, 1, 1, 'Contactado', 'CONTACTED', 2, 20.00, 0, 0, 1, '2026-08-13 20:30:23', '2026-08-13 20:30:23', NULL),
	(3, 1, 1, 'Interesado', 'INTERESTED', 3, 40.00, 0, 0, 1, '2026-08-13 20:30:23', '2026-08-13 20:30:23', NULL),
	(4, 1, 1, 'Cotización', 'QUOTATION', 4, 60.00, 0, 0, 1, '2026-08-13 20:30:23', '2026-08-13 20:30:23', NULL),
	(5, 1, 1, 'Negociación', 'NEGOTIATION', 5, 80.00, 0, 0, 1, '2026-08-13 20:30:23', '2026-08-13 20:30:23', NULL),
	(6, 1, 1, 'Ganado', 'WON', 6, 100.00, 1, 0, 1, '2026-08-13 20:30:23', '2026-08-13 20:30:23', NULL),
	(7, 1, 1, 'Perdido', 'LOST', 7, 0.00, 0, 1, 1, '2026-08-13 20:30:23', '2026-08-13 20:30:23', NULL);

-- Volcando estructura para tabla db_crm_mypes.products_services
CREATE TABLE IF NOT EXISTS `products_services` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'PRODUCT',
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `code` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_services_company_idx` (`company_id`),
  KEY `products_services_code_idx` (`code`),
  CONSTRAINT `products_services_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.products_services: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.quotations
CREATE TABLE IF NOT EXISTS `quotations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `opportunity_id` int unsigned DEFAULT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `created_by` int unsigned NOT NULL,
  `assigned_user_id` int unsigned DEFAULT NULL,
  `quotation_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `quotation_date` date NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'PEN',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quotation_status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'DRAFT',
  `notes` text COLLATE utf8mb4_general_ci,
  `terms_conditions` text COLLATE utf8mb4_general_ci,
  `sent_at` datetime DEFAULT NULL,
  `accepted_at` datetime DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotations_number_unique` (`company_id`,`quotation_number`),
  KEY `quotations_company_idx` (`company_id`),
  KEY `quotations_opportunity_idx` (`opportunity_id`),
  KEY `quotations_customer_idx` (`customer_id`),
  KEY `quotations_lead_idx` (`lead_id`),
  KEY `quotations_created_by_fk` (`created_by`),
  KEY `quotations_assigned_user_fk` (`assigned_user_id`),
  CONSTRAINT `quotations_assigned_user_fk` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `quotations_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `quotations_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `quotations_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `quotations_lead_fk` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  CONSTRAINT `quotations_opportunity_fk` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.quotations: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.quotation_items
CREATE TABLE IF NOT EXISTS `quotation_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` int unsigned NOT NULL,
  `product_service_id` int unsigned DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT '1.00',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_items_quotation_idx` (`quotation_id`),
  KEY `quotation_items_product_service_idx` (`product_service_id`),
  CONSTRAINT `quotation_items_product_service_fk` FOREIGN KEY (`product_service_id`) REFERENCES `products_services` (`id`),
  CONSTRAINT `quotation_items_quotation_fk` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.quotation_items: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.quotation_status_history
CREATE TABLE IF NOT EXISTS `quotation_status_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `quotation_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `previous_status` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `changed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_status_history_company_idx` (`company_id`),
  KEY `quotation_status_history_quotation_idx` (`quotation_id`),
  KEY `quotation_status_history_user_idx` (`user_id`),
  KEY `quotation_status_history_changed_at_idx` (`changed_at`),
  CONSTRAINT `quotation_status_history_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `quotation_status_history_quotation_fk` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`),
  CONSTRAINT `quotation_status_history_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.quotation_status_history: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.roles: ~2 rows (aproximadamente)
INSERT INTO `roles` (`id`, `name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(5, 'Administrador', 1, '2025-06-12 17:27:11', '2025-06-12 17:27:11', NULL),
	(6, 'invitado', 1, '2025-06-12 17:27:26', '2025-06-12 17:27:26', NULL);

-- Volcando estructura para tabla db_crm_mypes.role_permission
CREATE TABLE IF NOT EXISTS `role_permission` (
  `role_id` int unsigned NOT NULL,
  `permission_id` int unsigned NOT NULL,
  `permission` int DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`role_id`,`permission_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.role_permission: ~3 rows (aproximadamente)
INSERT INTO `role_permission` (`role_id`, `permission_id`, `permission`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(5, 6, 1, '2025-06-12 17:35:28', '2025-06-12 17:35:28', NULL),
	(5, 7, 1, '2025-06-12 17:35:40', '2025-06-12 17:35:40', NULL),
	(6, 7, 1, '2025-06-12 17:35:52', '2025-06-12 17:35:52', NULL);

-- Volcando estructura para tabla db_crm_mypes.storage_files
CREATE TABLE IF NOT EXISTS `storage_files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_id` int DEFAULT NULL,
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

-- Volcando datos para la tabla db_crm_mypes.storage_files: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.tasks
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `assigned_user_id` int unsigned NOT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `lead_id` int unsigned DEFAULT NULL,
  `customer_id` int unsigned DEFAULT NULL,
  `opportunity_id` int unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `priority` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'MEDIUM',
  `due_date` datetime DEFAULT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` datetime DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_company_index` (`company_id`),
  KEY `tasks_assigned_user_index` (`assigned_user_id`),
  KEY `tasks_created_by_index` (`created_by`),
  KEY `tasks_due_date_index` (`due_date`),
  KEY `tasks_completed_index` (`completed`),
  KEY `tasks_lead_fk` (`lead_id`),
  KEY `tasks_customer_fk` (`customer_id`),
  KEY `tasks_opportunity_fk` (`opportunity_id`),
  CONSTRAINT `tasks_assigned_user_fk` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tasks_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `tasks_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `tasks_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `tasks_lead_fk` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`),
  CONSTRAINT `tasks_opportunity_fk` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.tasks: ~0 rows (aproximadamente)

-- Volcando estructura para tabla db_crm_mypes.users
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

-- Volcando datos para la tabla db_crm_mypes.users: ~2 rows (aproximadamente)
INSERT INTO `users` (`id`, `foto_id`, `name`, `paternal_surname`, `maternal_surname`, `username`, `email`, `password`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(38, 1383, 'Brian Arturo', 'Coronado', 'Nizama', 'brian', 'omarsc@gmail.com', '$2y$10$xgmK7Nlc34AR1WmqwVn8teNNBPvw6.9byqeStOT7Ay8PJj.07B1JC', 1, '2025-06-12 17:34:03', '2025-07-10 17:31:15', NULL),
	(39, 1382, 'Nicolas', 'Cotrina', 'Llontop', 'nico', 'stafano@gmail.com', '$2y$10$xgmK7Nlc34AR1WmqwVn8teNNBPvw6.9byqeStOT7Ay8PJj.07B1JC', 1, '2025-06-18 18:08:03', '2025-07-10 17:20:24', NULL);

-- Volcando estructura para tabla db_crm_mypes.user_company_role
CREATE TABLE IF NOT EXISTS `user_company_role` (
  `user_id` int unsigned NOT NULL,
  `company_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`,`role_id`,`company_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla db_crm_mypes.user_company_role: ~2 rows (aproximadamente)
INSERT INTO `user_company_role` (`user_id`, `company_id`, `role_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(38, 4, 5, '2025-06-14 08:57:52', '2026-03-18 15:48:39', NULL),
	(39, 5, 6, '2025-06-18 18:08:51', '2026-03-18 15:48:47', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
