/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: solar_land
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `username` varchar(60) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `module` varchar(40) NOT NULL,
  `table_name` varchar(60) DEFAULT NULL,
  `record_id` varchar(40) DEFAULT NULL,
  `record_label` varchar(120) DEFAULT NULL,
  `action` enum('CREATE','UPDATE','DELETE','EXPORT','LOGIN','LOGIN_FAILED','LOGOUT') NOT NULL,
  `field_name` varchar(80) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_module_record_id_index` (`module`,`record_id`),
  KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `audit_logs_action_index` (`action`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES
(1,NULL,'system','127.0.0.1','state','states','1','Gujarat','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(2,NULL,'system','127.0.0.1','city','cities','1','Bhavnagar','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(3,NULL,'system','127.0.0.1','taluka','talukas','1','Talaja','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(4,NULL,'system','127.0.0.1','taluka','talukas','2','Mahuva','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(5,NULL,'system','127.0.0.1','taluka','talukas','3','Bhavnagar','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(6,NULL,'system','127.0.0.1','city','cities','2','Amreli','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(7,NULL,'system','127.0.0.1','taluka','talukas','4','Rajula','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(8,NULL,'system','127.0.0.1','city','cities','3','Kutch','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(9,NULL,'system','127.0.0.1','taluka','talukas','5','Bhuj','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(10,NULL,'system','127.0.0.1','taluka','talukas','6','Rapar','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(11,NULL,'system','127.0.0.1','state','states','2','Maharashtra','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(12,NULL,'system','127.0.0.1','city','cities','4','Solapur','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(13,NULL,'system','127.0.0.1','taluka','talukas','7','Mohol','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(14,NULL,'system','127.0.0.1','taluka','talukas','8','Pandharpur','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(15,NULL,'system','127.0.0.1','city','cities','5','Pune','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(16,NULL,'system','127.0.0.1','taluka','talukas','9','Baramati','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(17,NULL,'system','127.0.0.1','state','states','3','Rajasthan','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(18,NULL,'system','127.0.0.1','city','cities','6','Jodhpur','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(19,NULL,'system','127.0.0.1','taluka','talukas','10','Phalodi','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(20,NULL,'system','127.0.0.1','city','cities','7','Bikaner','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(21,NULL,'system','127.0.0.1','taluka','talukas','11','Kolayat','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(22,NULL,'system','127.0.0.1','state','states','4','Karnataka','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(23,NULL,'system','127.0.0.1','city','cities','8','Tumakuru','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(24,NULL,'system','127.0.0.1','taluka','talukas','12','Pavagada','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(25,NULL,'system','127.0.0.1','state','states','5','Madhya Pradesh','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(26,NULL,'system','127.0.0.1','city','cities','9','Neemuch','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(27,NULL,'system','127.0.0.1','taluka','talukas','13','Jawad','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(28,NULL,'system','127.0.0.1','state','states','6','Andhra Pradesh','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(29,NULL,'system','127.0.0.1','city','cities','10','Kurnool','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(30,NULL,'system','127.0.0.1','taluka','talukas','14','Panyam','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(31,NULL,'system','127.0.0.1','setting','settings','1','land_ref.format','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(32,NULL,'system','127.0.0.1','setting','settings','2','land_ref.seq_pad','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(33,NULL,'system','127.0.0.1','setting','settings','3','land_ref.seq_scope','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(34,NULL,'system','127.0.0.1','user','user','1','superadmin','CREATE',NULL,NULL,'New record','2026-11-17 13:18:20'),
(35,NULL,'system','127.0.0.1','schedule','rent_schedules','1','BHV-GJ-BHV-TLJ-0001','UPDATE','rent_allocation',NULL,'0 unpaid instalments re-allocated from 2024-04-01','2026-11-17 13:18:21'),
(36,NULL,'system','127.0.0.1','schedule','rent_schedules','2','BHV-GJ-BHV-TLJ-0002','UPDATE','rent_allocation',NULL,'0 unpaid instalments re-allocated from 2024-11-20','2026-11-17 13:18:21'),
(37,NULL,'system','127.0.0.1','schedule','rent_schedules','3','BHV-GJ-BHV-TLJ-0003','UPDATE','rent_allocation',NULL,'0 unpaid instalments re-allocated from 2025-01-01','2026-11-17 13:18:21'),
(38,NULL,'system','127.0.0.1','schedule','rent_schedules','4','SLP-MH-SLP-MHL-0001','UPDATE','rent_allocation',NULL,'0 unpaid instalments re-allocated from 2025-01-10','2026-11-17 13:18:21'),
(39,NULL,'system','127.0.0.1','schedule','rent_schedules','1','LSE00001','UPDATE','rent_schedule',NULL,'29 unpaid instalments generated','2026-11-17 13:18:21'),
(40,NULL,'system','127.0.0.1','schedule','rent_schedules','2','LSE00002','UPDATE','rent_schedule',NULL,'58 unpaid instalments generated','2026-11-17 13:18:21'),
(41,NULL,'system','127.0.0.1','schedule','rent_schedules','3','LSE00003','UPDATE','rent_schedule',NULL,'29 unpaid instalments generated','2026-11-17 13:18:21');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` bigint(20) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  `code` varchar(5) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_state_id_code_unique` (`state_id`,`code`),
  UNIQUE KEY `cities_state_id_name_unique` (`state_id`,`name`),
  CONSTRAINT `cities_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES
(1,1,'Bhavnagar','BHV','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(2,1,'Amreli','AMR','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(3,1,'Kutch','KTC','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(4,2,'Solapur','SLP','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(5,2,'Pune','PUN','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(6,3,'Jodhpur','JDH','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(7,3,'Bikaner','BKN','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(8,4,'Tumakuru','TMK','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(9,5,'Neemuch','NMH','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(10,6,'Kurnool','KNL','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL);
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compliance_records`
--

DROP TABLE IF EXISTS `compliance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `compliance_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plot_id` bigint(20) unsigned NOT NULL,
  `mortgage_created` enum('Yes','No') NOT NULL DEFAULT 'No',
  `mortgage_date` date DEFAULT NULL,
  `lender_name` varchar(150) DEFAULT NULL,
  `cersai_reg_no` varchar(40) DEFAULT NULL,
  `original_docs_custody` enum('With company','With lender','With custodian') NOT NULL DEFAULT 'With company',
  `custodian_name` varchar(150) DEFAULT NULL,
  `docs_deposit_date` date DEFAULT NULL,
  `lender_noc_status` enum('Not required','Pending','Obtained') NOT NULL DEFAULT 'Not required',
  `pdc_open_items` smallint(5) unsigned NOT NULL DEFAULT 0,
  `target_closure_date` date DEFAULT NULL,
  `escalation_level` enum('L0 · None','L1 · Land head','L2 · CFO','L3 · MD') NOT NULL DEFAULT 'L0 · None',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compliance_records_plot_id_unique` (`plot_id`),
  CONSTRAINT `compliance_records_plot_id_foreign` FOREIGN KEY (`plot_id`) REFERENCES `plots` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compliance_records`
--

LOCK TABLES `compliance_records` WRITE;
/*!40000 ALTER TABLE `compliance_records` DISABLE KEYS */;
INSERT INTO `compliance_records` VALUES
(1,1,'Yes','2025-03-12','Sample Lender Bank','400012345678','With lender','Sample Lender Bank, Ahmedabad','2025-03-12','Obtained',2,'2027-01-17','L1 · Land head','Mutation of mortgage charge pending in 7/12.',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(2,2,'No',NULL,NULL,NULL,'With company','Corporate legal vault',NULL,'Not required',3,'2026-10-17','L2 · CFO','NA order copy and two heirship affidavits outstanding.',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL);
/*!40000 ALTER TABLE `compliance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plot_id` bigint(20) unsigned DEFAULT NULL,
  `documentable_type` varchar(80) NOT NULL,
  `documentable_id` bigint(20) unsigned NOT NULL,
  `doc_type` varchar(60) NOT NULL,
  `title` varchar(200) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(120) DEFAULT NULL,
  `size_kb` int(10) unsigned NOT NULL DEFAULT 0,
  `version_no` smallint(5) unsigned NOT NULL DEFAULT 1,
  `is_original_scan` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `uploaded_by` bigint(20) unsigned DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_uploaded_by_foreign` (`uploaded_by`),
  KEY `documents_documentable_type_documentable_id_index` (`documentable_type`,`documentable_id`),
  KEY `documents_plot_id_doc_type_title_index` (`plot_id`,`doc_type`,`title`),
  CONSTRAINT `documents_plot_id_foreign` FOREIGN KEY (`plot_id`) REFERENCES `plots` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `land_ref_sequences`
--

DROP TABLE IF EXISTS `land_ref_sequences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `land_ref_sequences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope_key` varchar(80) NOT NULL,
  `last_number` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `land_ref_sequences_scope_key_unique` (`scope_key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `land_ref_sequences`
--

LOCK TABLES `land_ref_sequences` WRITE;
/*!40000 ALTER TABLE `land_ref_sequences` DISABLE KEYS */;
INSERT INTO `land_ref_sequences` VALUES
(1,'BHV-GJ-BHV-TLJ-',3,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(2,'SLP-MH-SLP-MHL-',1,'2026-11-17 13:18:21','2026-11-17 13:18:21');
/*!40000 ALTER TABLE `land_ref_sequences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leases`
--

DROP TABLE IF EXISTS `leases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lease_no` varchar(20) NOT NULL,
  `lease_date` date DEFAULT NULL,
  `park_id` bigint(20) unsigned NOT NULL,
  `spv_id` bigint(20) unsigned NOT NULL,
  `plot_id` bigint(20) unsigned NOT NULL,
  `atl_date` date DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `lease_start_date` date NOT NULL,
  `lease_duration_months` smallint(5) unsigned NOT NULL,
  `lease_end_date` date NOT NULL,
  `total_lease_area` decimal(14,4) NOT NULL,
  `land_unit` enum('Acre','Hectare','Sq.m') NOT NULL DEFAULT 'Acre',
  `lease_area_acres` decimal(14,4) NOT NULL,
  `leased_capacity_mw` decimal(10,3) NOT NULL DEFAULT 0.000,
  `rent_type` enum('Per acre','Fixed annual') NOT NULL DEFAULT 'Per acre',
  `rent_rate` decimal(15,2) NOT NULL,
  `annual_rent` decimal(15,2) NOT NULL,
  `payment_frequency` enum('Annual','Half-yearly','Quarterly','Monthly') NOT NULL DEFAULT 'Annual',
  `escalation_pct` decimal(6,2) NOT NULL DEFAULT 0.00,
  `escalation_every_yrs` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `security_deposit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tds_applicable` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `tds_rate` decimal(5,2) NOT NULL DEFAULT 10.00,
  `gst_applicable` enum('Yes','No') NOT NULL DEFAULT 'No',
  `uni_number` varchar(40) DEFAULT NULL,
  `sro_doc_no` varchar(40) DEFAULT NULL,
  `agreement_no` varchar(40) DEFAULT NULL,
  `mutation_entry_no` varchar(40) DEFAULT NULL,
  `status` enum('Draft','Active','Expired','Terminated') NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leases_lease_no_unique` (`lease_no`),
  KEY `leases_plot_id_status_index` (`plot_id`,`status`),
  KEY `leases_capacity_idx` (`park_id`,`status`,`lease_start_date`,`lease_end_date`),
  KEY `leases_spv_id_index` (`spv_id`),
  CONSTRAINT `leases_park_id_foreign` FOREIGN KEY (`park_id`) REFERENCES `parks` (`id`),
  CONSTRAINT `leases_plot_id_foreign` FOREIGN KEY (`plot_id`) REFERENCES `plots` (`id`),
  CONSTRAINT `leases_spv_id_foreign` FOREIGN KEY (`spv_id`) REFERENCES `spvs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leases`
--

LOCK TABLES `leases` WRITE;
/*!40000 ALTER TABLE `leases` DISABLE KEYS */;
INSERT INTO `leases` VALUES
(1,'LSE00001','2024-03-22',1,1,1,'2024-01-18','2024-03-22','2024-04-01',348,'2053-03-31',100.0000,'Acre',100.0000,200.000,'Per acre',45000.00,4500000.00,'Annual',5.00,3,500000.00,'Yes',10.00,'No','UNI-GJ-24-008812','TLJ/1442/2024','LA-PGP10-001','ME-3381','Active',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(2,'LSE00002',NULL,1,1,2,NULL,NULL,'2024-11-20',348,'2053-11-19',12.5000,'Acre',12.5000,30.000,'Per acre',42000.00,525000.00,'Half-yearly',5.00,3,0.00,'Yes',10.00,'No',NULL,NULL,'LA-PGP10-002',NULL,'Active',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(3,'LSE00003',NULL,2,3,4,NULL,NULL,'2025-01-10',348,'2054-01-09',20.0000,'Acre',20.0000,20.000,'Per acre',38000.00,760000.00,'Annual',4.00,3,0.00,'Yes',10.00,'No',NULL,NULL,'LA-PGP12-001',NULL,'Active',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL);
/*!40000 ALTER TABLE `leases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_01_01_000100_create_location_and_setting_tables',1),
(5,'2026_01_01_000200_create_parties_table',1),
(6,'2026_01_01_000300_create_parks_and_spvs_tables',1),
(7,'2026_01_01_000400_create_plot_tables',1),
(8,'2026_01_01_000500_create_ownership_tables',1),
(9,'2026_01_01_000600_create_lease_and_rent_tables',1),
(10,'2026_01_01_000700_create_sub_lease_document_audit_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parks`
--

DROP TABLE IF EXISTS `parks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `parks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `park_code` varchar(6) NOT NULL,
  `park_name` varchar(150) NOT NULL,
  `state_id` bigint(20) unsigned NOT NULL,
  `city_id` bigint(20) unsigned NOT NULL,
  `taluka_id` bigint(20) unsigned NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `total_land_area` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `land_unit` enum('Acre','Hectare','Sq.m') NOT NULL DEFAULT 'Acre',
  `total_land_acres` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `installed_capacity_mw` decimal(10,3) NOT NULL DEFAULT 0.000,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parks_park_code_unique` (`park_code`),
  KEY `parks_state_id_foreign` (`state_id`),
  KEY `parks_city_id_foreign` (`city_id`),
  KEY `parks_taluka_id_foreign` (`taluka_id`),
  KEY `parks_status_index` (`status`),
  CONSTRAINT `parks_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `parks_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`),
  CONSTRAINT `parks_taluka_id_foreign` FOREIGN KEY (`taluka_id`) REFERENCES `talukas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parks`
--

LOCK TABLES `parks` WRITE;
/*!40000 ALTER TABLE `parks` DISABLE KEYS */;
INSERT INTO `parks` VALUES
(1,'BHV','Bhavnagar Solar Park',1,1,1,'Talaja–Sosiya road',1200.0000,'Acre',1200.0000,250.000,'Sample park for the 200 MW sub-lease example.','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(2,'SLP','Solapur Solar Park',2,4,7,NULL,260.0000,'Acre',260.0000,75.000,NULL,'Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL);
/*!40000 ALTER TABLE `parks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `parties`
--

DROP TABLE IF EXISTS `parties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `parties` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `party_code` varchar(20) NOT NULL,
  `party_name` varchar(150) NOT NULL,
  `party_type` enum('Landowner','Company','Sub-lessee','Other') NOT NULL,
  `contact_person` varchar(120) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `state_id` bigint(20) unsigned DEFAULT NULL,
  `city_id` bigint(20) unsigned DEFAULT NULL,
  `taluka_id` bigint(20) unsigned DEFAULT NULL,
  `pincode` varchar(6) DEFAULT NULL,
  `pan` varchar(10) DEFAULT NULL,
  `gst_no` varchar(15) DEFAULT NULL,
  `aadhaar_last4` char(4) DEFAULT NULL,
  `voter_id` varchar(30) DEFAULT NULL,
  `bank_account_no` text DEFAULT NULL,
  `bank_ifsc` varchar(11) DEFAULT NULL,
  `bank_name_branch` varchar(150) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parties_party_code_unique` (`party_code`),
  KEY `parties_state_id_foreign` (`state_id`),
  KEY `parties_city_id_foreign` (`city_id`),
  KEY `parties_taluka_id_foreign` (`taluka_id`),
  KEY `parties_party_type_status_index` (`party_type`,`status`),
  KEY `parties_party_name_index` (`party_name`),
  KEY `parties_pan_index` (`pan`),
  CONSTRAINT `parties_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `parties_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE SET NULL,
  CONSTRAINT `parties_taluka_id_foreign` FOREIGN KEY (`taluka_id`) REFERENCES `talukas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `parties`
--

LOCK TABLES `parties` WRITE;
/*!40000 ALTER TABLE `parties` DISABLE KEYS */;
INSERT INTO `parties` VALUES
(1,'PTY00001','Sample Solar Developer Pvt Ltd','Company','Project Head','9825012544',NULL,'At & Post',1,1,1,NULL,'AAKCS1234P','24AAKCS1234P1Z5',NULL,NULL,NULL,NULL,NULL,'Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(2,'PTY00002','Rameshbhai K. Patel','Landowner',NULL,'9825050548',NULL,'At & Post Trapaj',1,1,1,NULL,'AKLPP4821M',NULL,'4521',NULL,'eyJpdiI6IjV3a2VNZHZGK082Rzg2anNGK29TUXc9PSIsInZhbHVlIjoic1VNTFZuTnY2M296QUZaelV2eWZzZz09IiwibWFjIjoiMDM3NGIxODA2ZjNiMjIyN2MxMTcxY2ZlYTJkZTc0NTdhY2U4ZDIxMTg0MTBiNTFiYzk2NmZmYmY5ZGRhMmYxMiIsInRhZyI6IiJ9','BARB0TALAJA','Bank of Baroda, Talaja','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(3,'PTY00003','Savitaben R. Patel','Landowner',NULL,'9825096039',NULL,'At & Post Trapaj',1,1,1,NULL,'BXRPP1190D',NULL,'7310',NULL,'eyJpdiI6IkcyOHZoYTRZa0phOWQ5amM5VEZ2WkE9PSIsInZhbHVlIjoidlFIWUFwc3g3L1crUi9Xd1BHQWRKQT09IiwibWFjIjoiM2Y0OWRhYTMwOWVkYWI2MTc3M2VlNmM2NzhmMmE0ZDU4NzRhZTk0YmRhM2IyMjlkOWNlZTNmNzdmNjViNzM5MyIsInRhZyI6IiJ9','BARB0TALAJA','Bank of Baroda, Talaja','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(4,'PTY00004','Bhikhabhai N. Rathod','Landowner',NULL,'9825077324',NULL,'At & Post Trapaj',1,1,1,NULL,'BQRPR7710K',NULL,'9073',NULL,NULL,NULL,NULL,'Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(5,'PTY00005','Mahendrasinh J. Gohil','Landowner',NULL,'9825087368',NULL,'Darbar Gadh, Sosiya',1,1,1,NULL,'CJGPG2290H',NULL,NULL,NULL,'eyJpdiI6IkxSUXdYUmxsL0FPbVQ1d2xsMERJZFE9PSIsInZhbHVlIjoiQVJzbEZpVUlnWEh6VWhZSlIzeUpqZz09IiwibWFjIjoiZDg4ZGE0MTliOWQ5ZTZiMDMyMDgyM2YyZmM3ZThjZjY1MjQ4ZGNmZWM5MDdmOTgzMTgwMWE5ZDcxOWRlYmIxMyIsInRhZyI6IiJ9','SBIN0003355','SBI, Alang','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(6,'PTY00006','Pradyumansinh J. Gohil','Landowner',NULL,'9825035440',NULL,'Darbar Gadh, Sosiya',1,1,1,NULL,'CJGPG4417A',NULL,NULL,NULL,NULL,NULL,NULL,'Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(7,'PTY00007','Dattatray S. Jadhav','Landowner',NULL,'9825097136',NULL,'Jadhav Wasti, Penur',2,4,7,NULL,'AFJPJ5532Q',NULL,NULL,NULL,'eyJpdiI6IkFEM1A0N2hJMnMzaGlmZW9Bck1rTHc9PSIsInZhbHVlIjoicnd2RFFNcU9PcXFKUDBqNkFYcGNIZz09IiwibWFjIjoiYjI5NzA0YTZmNmUxZmNlMGQ5MTNlMjllNTY0YWUwYTE0NjE0ZGMzNmI1NTIxYTgzYjkxMGFiNmFkZmI5ZGE2OCIsInRhZyI6IiJ9','MAHB0000123','Bank of Maharashtra, Mohol','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(8,'PTY00008','Sunita D. Jadhav','Landowner',NULL,'9825070393',NULL,'Jadhav Wasti, Penur',2,4,7,NULL,'BHJPJ8841R',NULL,NULL,NULL,NULL,NULL,NULL,'Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(9,'PTY00009','Party A Power Traders LLP','Sub-lessee','Commercial Manager','9825094737',NULL,'At & Post',1,1,1,NULL,'AAPFA1111A',NULL,NULL,NULL,NULL,NULL,NULL,'Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(10,'PTY00010','Party B Industries Ltd','Sub-lessee','Energy Head','9825078582',NULL,'At & Post',1,1,1,NULL,'AABCB2222B',NULL,NULL,NULL,NULL,NULL,NULL,'Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(11,'PTY00011','Party C Green Energy Pvt Ltd','Sub-lessee','Director','9825062201',NULL,'At & Post',1,1,1,NULL,'AAKCC3333C',NULL,NULL,NULL,NULL,NULL,NULL,'Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(12,'PTY00012','Sample Grid Services LLP','Sub-lessee',NULL,'9825024371',NULL,'At & Post',2,4,7,NULL,'AAPFS3391L',NULL,NULL,NULL,NULL,NULL,NULL,'Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL);
/*!40000 ALTER TABLE `parties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plot_grid_connectivity`
--

DROP TABLE IF EXISTS `plot_grid_connectivity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plot_grid_connectivity` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plot_id` bigint(20) unsigned NOT NULL,
  `connectivity_mw` decimal(10,3) DEFAULT NULL,
  `substation` varchar(120) DEFAULT NULL,
  `aggregator` varchar(120) DEFAULT NULL,
  `tfr_confirmation` enum('Yes','No') NOT NULL DEFAULT 'No',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plot_grid_connectivity_plot_id_unique` (`plot_id`),
  CONSTRAINT `plot_grid_connectivity_plot_id_foreign` FOREIGN KEY (`plot_id`) REFERENCES `plots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plot_grid_connectivity`
--

LOCK TABLES `plot_grid_connectivity` WRITE;
/*!40000 ALTER TABLE `plot_grid_connectivity` DISABLE KEYS */;
INSERT INTO `plot_grid_connectivity` VALUES
(1,1,200.000,'Talaja 400 kV','Sample Aggregator A','Yes',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(2,4,20.000,'Mohol 132 kV','Sample Aggregator C','Yes',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21');
/*!40000 ALTER TABLE `plot_grid_connectivity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plot_ownership_shares`
--

DROP TABLE IF EXISTS `plot_ownership_shares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plot_ownership_shares` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ownership_version_id` bigint(20) unsigned NOT NULL,
  `party_id` bigint(20) unsigned NOT NULL,
  `share_percent` decimal(7,4) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plot_ownership_shares_ownership_version_id_party_id_unique` (`ownership_version_id`,`party_id`),
  KEY `plot_ownership_shares_party_id_foreign` (`party_id`),
  CONSTRAINT `plot_ownership_shares_ownership_version_id_foreign` FOREIGN KEY (`ownership_version_id`) REFERENCES `plot_ownership_versions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `plot_ownership_shares_party_id_foreign` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plot_ownership_shares`
--

LOCK TABLES `plot_ownership_shares` WRITE;
/*!40000 ALTER TABLE `plot_ownership_shares` DISABLE KEYS */;
INSERT INTO `plot_ownership_shares` VALUES
(1,1,2,50.0000,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(2,1,3,30.0000,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(3,1,4,20.0000,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(4,2,5,51.0000,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(5,2,6,49.0000,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(6,3,4,100.0000,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(7,4,7,50.0000,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(8,4,8,50.0000,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(9,5,4,60.0000,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21');
/*!40000 ALTER TABLE `plot_ownership_shares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plot_ownership_versions`
--

DROP TABLE IF EXISTS `plot_ownership_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plot_ownership_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `plot_id` bigint(20) unsigned NOT NULL,
  `version_no` smallint(5) unsigned NOT NULL,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `status` enum('Draft','Finalised','Superseded') NOT NULL DEFAULT 'Draft',
  `total_percent` decimal(7,4) NOT NULL DEFAULT 0.0000,
  `change_reason` varchar(255) DEFAULT NULL,
  `finalised_by` bigint(20) unsigned DEFAULT NULL,
  `finalised_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plot_ownership_versions_plot_id_version_no_unique` (`plot_id`,`version_no`),
  KEY `plot_ownership_versions_finalised_by_foreign` (`finalised_by`),
  KEY `plot_ownership_versions_plot_id_status_effective_from_index` (`plot_id`,`status`,`effective_from`),
  CONSTRAINT `plot_ownership_versions_finalised_by_foreign` FOREIGN KEY (`finalised_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `plot_ownership_versions_plot_id_foreign` FOREIGN KEY (`plot_id`) REFERENCES `plots` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plot_ownership_versions`
--

LOCK TABLES `plot_ownership_versions` WRITE;
/*!40000 ALTER TABLE `plot_ownership_versions` DISABLE KEYS */;
INSERT INTO `plot_ownership_versions` VALUES
(1,1,1,'2024-04-01',NULL,'Finalised',100.0000,NULL,NULL,'2026-11-17 13:18:21',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(2,2,1,'2024-11-20',NULL,'Finalised',100.0000,NULL,NULL,'2026-11-17 13:18:21',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(3,3,1,'2025-01-01',NULL,'Finalised',100.0000,NULL,NULL,'2026-11-17 13:18:21',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(4,4,1,'2025-01-10',NULL,'Finalised',100.0000,NULL,NULL,'2026-11-17 13:18:21',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(5,3,2,NULL,NULL,'Draft',60.0000,NULL,NULL,NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21');
/*!40000 ALTER TABLE `plot_ownership_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plots`
--

DROP TABLE IF EXISTS `plots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `plots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `land_ref_no` varchar(60) NOT NULL,
  `park_id` bigint(20) unsigned NOT NULL,
  `spv_id` bigint(20) unsigned NOT NULL,
  `state_id` bigint(20) unsigned NOT NULL,
  `city_id` bigint(20) unsigned NOT NULL,
  `taluka_id` bigint(20) unsigned NOT NULL,
  `village` varchar(100) NOT NULL,
  `survey_no` varchar(30) NOT NULL,
  `sub_survey_no` varchar(30) NOT NULL DEFAULT '',
  `total_area` decimal(14,4) NOT NULL,
  `land_unit` enum('Acre','Hectare','Sq.m') NOT NULL DEFAULT 'Acre',
  `area_acres` decimal(14,4) NOT NULL,
  `area_under_lease_acres` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `dlr_area_sqm` decimal(14,2) DEFAULT NULL,
  `rate_per_acre` decimal(15,2) NOT NULL DEFAULT 0.00,
  `increment_after_yrs` tinyint(3) unsigned NOT NULL DEFAULT 3,
  `increment_pct` decimal(6,2) NOT NULL DEFAULT 5.00,
  `title_report` enum('Yes','No') NOT NULL DEFAULT 'No',
  `title_dd_status` enum('Pending','In progress','Clear','Issue found') NOT NULL DEFAULT 'Pending',
  `lessor_capacity_verified` enum('Yes','No') NOT NULL DEFAULT 'No',
  `encumbrance_status` enum('Under verification','Nil','Encumbered') NOT NULL DEFAULT 'Under verification',
  `lien_details` text DEFAULT NULL,
  `na_application` enum('Yes','No') NOT NULL DEFAULT 'No',
  `na_status` enum('Not applied','Applied','Approved','Rejected') NOT NULL DEFAULT 'Not applied',
  `land_status` enum('Identified','ATL signed','Leased','Sub-leased','Mortgaged','Dropped') NOT NULL DEFAULT 'Identified',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plots_survey_unique` (`taluka_id`,`village`,`survey_no`,`sub_survey_no`),
  UNIQUE KEY `plots_land_ref_no_unique` (`land_ref_no`),
  KEY `plots_state_id_foreign` (`state_id`),
  KEY `plots_city_id_foreign` (`city_id`),
  KEY `plots_park_id_land_status_index` (`park_id`,`land_status`),
  KEY `plots_spv_id_index` (`spv_id`),
  CONSTRAINT `plots_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `plots_park_id_foreign` FOREIGN KEY (`park_id`) REFERENCES `parks` (`id`),
  CONSTRAINT `plots_spv_id_foreign` FOREIGN KEY (`spv_id`) REFERENCES `spvs` (`id`),
  CONSTRAINT `plots_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`),
  CONSTRAINT `plots_taluka_id_foreign` FOREIGN KEY (`taluka_id`) REFERENCES `talukas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plots`
--

LOCK TABLES `plots` WRITE;
/*!40000 ALTER TABLE `plots` DISABLE KEYS */;
INSERT INTO `plots` VALUES
(1,'BHV-GJ-BHV-TLJ-0001',1,1,1,1,1,'Trapaj','125','2',101.3100,'Acre',101.3100,100.0000,NULL,45000.00,3,5.00,'Yes','Clear','Yes','Nil',NULL,'Yes','Approved','Sub-leased',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(2,'BHV-GJ-BHV-TLJ-0002',1,1,1,1,1,'Sosiya','205','1',50600.0000,'Sq.m',12.5035,12.5000,NULL,42000.00,3,5.00,'Yes','Clear','Yes','Nil',NULL,'Yes','Approved','Leased',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(3,'BHV-GJ-BHV-TLJ-0003',1,2,1,1,1,'Trapaj','118','',8.2000,'Acre',8.2000,8.2000,NULL,45000.00,3,5.00,'Yes','In progress','Yes','Under verification','Crop loan with district co-op bank, closure letter awaited','Yes','Approved','ATL signed',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(4,'SLP-MH-SLP-MHL-0001',2,3,2,4,7,'Penur','43','1',8.1000,'Hectare',20.0155,20.0000,NULL,38000.00,3,4.00,'Yes','Clear','Yes','Nil',NULL,'Yes','Approved','Sub-leased',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL);
/*!40000 ALTER TABLE `plots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rent_allocations`
--

DROP TABLE IF EXISTS `rent_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rent_allocations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rent_schedule_id` bigint(20) unsigned NOT NULL,
  `party_id` bigint(20) unsigned NOT NULL,
  `share_percent` decimal(7,4) NOT NULL,
  `gross_amount` decimal(15,2) NOT NULL,
  `tds_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rent_allocations_rent_schedule_id_party_id_unique` (`rent_schedule_id`,`party_id`),
  KEY `rent_allocations_party_id_index` (`party_id`),
  CONSTRAINT `rent_allocations_party_id_foreign` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`),
  CONSTRAINT `rent_allocations_rent_schedule_id_foreign` FOREIGN KEY (`rent_schedule_id`) REFERENCES `rent_schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rent_allocations`
--

LOCK TABLES `rent_allocations` WRITE;
/*!40000 ALTER TABLE `rent_allocations` DISABLE KEYS */;
INSERT INTO `rent_allocations` VALUES
(1,1,2,50.0000,2250000.00,225000.00,2025000.00,2250000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(2,1,3,30.0000,1350000.00,135000.00,1215000.00,1350000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(3,1,4,20.0000,900000.00,90000.00,810000.00,900000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(4,2,2,50.0000,2250000.00,225000.00,2025000.00,2250000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(5,2,3,30.0000,1350000.00,135000.00,1215000.00,1350000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(6,2,4,20.0000,900000.00,90000.00,810000.00,900000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(7,3,2,50.0000,2250000.00,225000.00,2025000.00,1125000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(8,3,3,30.0000,1350000.00,135000.00,1215000.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(9,3,4,20.0000,900000.00,90000.00,810000.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(10,4,2,50.0000,2362500.00,236250.00,2126250.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(11,4,3,30.0000,1417500.00,141750.00,1275750.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(12,4,4,20.0000,945000.00,94500.00,850500.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(13,5,2,50.0000,2362500.00,236250.00,2126250.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(14,5,3,30.0000,1417500.00,141750.00,1275750.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(15,5,4,20.0000,945000.00,94500.00,850500.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(16,6,2,50.0000,2362500.00,236250.00,2126250.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(17,6,3,30.0000,1417500.00,141750.00,1275750.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(18,6,4,20.0000,945000.00,94500.00,850500.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(19,7,2,50.0000,2480625.00,248062.50,2232562.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(20,7,3,30.0000,1488375.00,148837.50,1339537.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(21,7,4,20.0000,992250.00,99225.00,893025.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(22,8,2,50.0000,2480625.00,248062.50,2232562.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(23,8,3,30.0000,1488375.00,148837.50,1339537.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(24,8,4,20.0000,992250.00,99225.00,893025.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(25,9,2,50.0000,2480625.00,248062.50,2232562.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(26,9,3,30.0000,1488375.00,148837.50,1339537.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(27,9,4,20.0000,992250.00,99225.00,893025.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(28,10,2,50.0000,2604656.25,260465.63,2344190.62,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(29,10,3,30.0000,1562793.75,156279.38,1406514.37,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(30,10,4,20.0000,1041862.50,104186.24,937676.26,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(31,11,2,50.0000,2604656.25,260465.63,2344190.62,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(32,11,3,30.0000,1562793.75,156279.38,1406514.37,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(33,11,4,20.0000,1041862.50,104186.24,937676.26,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(34,12,2,50.0000,2604656.25,260465.63,2344190.62,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(35,12,3,30.0000,1562793.75,156279.38,1406514.37,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(36,12,4,20.0000,1041862.50,104186.24,937676.26,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(37,13,2,50.0000,2734889.07,273488.91,2461400.16,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(38,13,3,30.0000,1640933.44,164093.34,1476840.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(39,13,4,20.0000,1093955.62,109395.56,984560.06,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(40,14,2,50.0000,2734889.07,273488.91,2461400.16,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(41,14,3,30.0000,1640933.44,164093.34,1476840.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(42,14,4,20.0000,1093955.62,109395.56,984560.06,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(43,15,2,50.0000,2734889.07,273488.91,2461400.16,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(44,15,3,30.0000,1640933.44,164093.34,1476840.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(45,15,4,20.0000,1093955.62,109395.56,984560.06,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(46,16,2,50.0000,2871633.52,287163.35,2584470.17,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(47,16,3,30.0000,1722980.11,172298.01,1550682.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(48,16,4,20.0000,1148653.40,114865.34,1033788.06,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(49,17,2,50.0000,2871633.52,287163.35,2584470.17,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(50,17,3,30.0000,1722980.11,172298.01,1550682.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(51,17,4,20.0000,1148653.40,114865.34,1033788.06,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(52,18,2,50.0000,2871633.52,287163.35,2584470.17,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(53,18,3,30.0000,1722980.11,172298.01,1550682.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(54,18,4,20.0000,1148653.40,114865.34,1033788.06,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(55,19,2,50.0000,3015215.19,301521.52,2713693.67,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(56,19,3,30.0000,1809129.11,180912.91,1628216.20,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(57,19,4,20.0000,1206086.08,120608.61,1085477.47,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(58,20,2,50.0000,3015215.19,301521.52,2713693.67,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(59,20,3,30.0000,1809129.11,180912.91,1628216.20,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(60,20,4,20.0000,1206086.08,120608.61,1085477.47,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(61,21,2,50.0000,3015215.19,301521.52,2713693.67,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(62,21,3,30.0000,1809129.11,180912.91,1628216.20,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(63,21,4,20.0000,1206086.08,120608.61,1085477.47,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(64,22,2,50.0000,3165975.95,316597.60,2849378.35,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(65,22,3,30.0000,1899585.57,189958.56,1709627.01,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(66,22,4,20.0000,1266390.38,126639.03,1139751.35,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(67,23,2,50.0000,3165975.95,316597.60,2849378.35,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(68,23,3,30.0000,1899585.57,189958.56,1709627.01,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(69,23,4,20.0000,1266390.38,126639.03,1139751.35,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(70,24,2,50.0000,3165975.95,316597.60,2849378.35,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(71,24,3,30.0000,1899585.57,189958.56,1709627.01,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(72,24,4,20.0000,1266390.38,126639.03,1139751.35,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(73,25,2,50.0000,3324274.75,332427.48,2991847.27,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(74,25,3,30.0000,1994564.85,199456.49,1795108.36,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(75,25,4,20.0000,1329709.90,132970.98,1196738.92,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(76,26,2,50.0000,3324274.75,332427.48,2991847.27,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(77,26,3,30.0000,1994564.85,199456.49,1795108.36,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(78,26,4,20.0000,1329709.90,132970.98,1196738.92,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(79,27,2,50.0000,3324274.75,332427.48,2991847.27,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(80,27,3,30.0000,1994564.85,199456.49,1795108.36,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(81,27,4,20.0000,1329709.90,132970.98,1196738.92,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(82,28,2,50.0000,3490488.49,349048.85,3141439.64,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(83,28,3,30.0000,2094293.09,209429.31,1884863.78,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(84,28,4,20.0000,1396195.39,139619.54,1256575.85,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(85,29,2,50.0000,3490488.49,349048.85,3141439.64,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(86,29,3,30.0000,2094293.09,209429.31,1884863.78,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(87,29,4,20.0000,1396195.39,139619.54,1256575.85,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(88,30,5,51.0000,133875.00,13387.50,120487.50,133875.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(89,30,6,49.0000,128625.00,12862.50,115762.50,128625.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(90,31,5,51.0000,133875.00,13387.50,120487.50,133875.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(91,31,6,49.0000,128625.00,12862.50,115762.50,128625.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(92,32,5,51.0000,133875.00,13387.50,120487.50,133875.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(93,32,6,49.0000,128625.00,12862.50,115762.50,128625.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(94,33,5,51.0000,133875.00,13387.50,120487.50,133875.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(95,33,6,49.0000,128625.00,12862.50,115762.50,128625.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(96,34,5,51.0000,133875.00,13387.50,120487.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(97,34,6,49.0000,128625.00,12862.50,115762.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(98,35,5,51.0000,133875.00,13387.50,120487.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(99,35,6,49.0000,128625.00,12862.50,115762.50,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(100,36,5,51.0000,140568.75,14056.88,126511.87,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(101,36,6,49.0000,135056.25,13505.62,121550.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(102,37,5,51.0000,140568.75,14056.88,126511.87,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(103,37,6,49.0000,135056.25,13505.62,121550.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(104,38,5,51.0000,140568.75,14056.88,126511.87,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(105,38,6,49.0000,135056.25,13505.62,121550.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(106,39,5,51.0000,140568.75,14056.88,126511.87,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(107,39,6,49.0000,135056.25,13505.62,121550.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(108,40,5,51.0000,140568.75,14056.88,126511.87,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(109,40,6,49.0000,135056.25,13505.62,121550.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(110,41,5,51.0000,140568.75,14056.88,126511.87,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(111,41,6,49.0000,135056.25,13505.62,121550.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(112,42,5,51.0000,147597.19,14759.72,132837.47,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(113,42,6,49.0000,141809.06,14180.91,127628.15,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(114,43,5,51.0000,147597.19,14759.72,132837.47,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(115,43,6,49.0000,141809.06,14180.91,127628.15,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(116,44,5,51.0000,147597.19,14759.72,132837.47,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(117,44,6,49.0000,141809.06,14180.91,127628.15,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(118,45,5,51.0000,147597.19,14759.72,132837.47,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(119,45,6,49.0000,141809.06,14180.91,127628.15,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(120,46,5,51.0000,147597.19,14759.72,132837.47,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(121,46,6,49.0000,141809.06,14180.91,127628.15,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(122,47,5,51.0000,147597.19,14759.72,132837.47,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(123,47,6,49.0000,141809.06,14180.91,127628.15,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(124,48,5,51.0000,154977.05,15497.71,139479.34,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(125,48,6,49.0000,148899.51,14889.95,134009.56,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(126,49,5,51.0000,154977.05,15497.71,139479.34,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(127,49,6,49.0000,148899.51,14889.95,134009.56,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(128,50,5,51.0000,154977.05,15497.71,139479.34,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(129,50,6,49.0000,148899.51,14889.95,134009.56,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(130,51,5,51.0000,154977.05,15497.71,139479.34,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(131,51,6,49.0000,148899.51,14889.95,134009.56,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(132,52,5,51.0000,154977.05,15497.71,139479.34,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(133,52,6,49.0000,148899.51,14889.95,134009.56,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(134,53,5,51.0000,154977.05,15497.71,139479.34,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(135,53,6,49.0000,148899.51,14889.95,134009.56,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(136,54,5,51.0000,162725.90,16272.59,146453.31,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(137,54,6,49.0000,156344.49,15634.45,140710.04,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(138,55,5,51.0000,162725.90,16272.59,146453.31,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(139,55,6,49.0000,156344.49,15634.45,140710.04,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(140,56,5,51.0000,162725.90,16272.59,146453.31,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(141,56,6,49.0000,156344.49,15634.45,140710.04,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(142,57,5,51.0000,162725.90,16272.59,146453.31,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(143,57,6,49.0000,156344.49,15634.45,140710.04,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(144,58,5,51.0000,162725.90,16272.59,146453.31,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(145,58,6,49.0000,156344.49,15634.45,140710.04,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(146,59,5,51.0000,162725.90,16272.59,146453.31,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(147,59,6,49.0000,156344.49,15634.45,140710.04,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(148,60,5,51.0000,170862.19,17086.22,153775.97,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(149,60,6,49.0000,164161.72,16416.17,147745.55,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(150,61,5,51.0000,170862.19,17086.22,153775.97,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(151,61,6,49.0000,164161.72,16416.17,147745.55,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(152,62,5,51.0000,170862.19,17086.22,153775.97,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(153,62,6,49.0000,164161.72,16416.17,147745.55,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(154,63,5,51.0000,170862.19,17086.22,153775.97,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(155,63,6,49.0000,164161.72,16416.17,147745.55,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(156,64,5,51.0000,170862.19,17086.22,153775.97,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(157,64,6,49.0000,164161.72,16416.17,147745.55,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(158,65,5,51.0000,170862.19,17086.22,153775.97,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(159,65,6,49.0000,164161.72,16416.17,147745.55,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(160,66,5,51.0000,179405.31,17940.53,161464.78,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(161,66,6,49.0000,172369.80,17236.98,155132.82,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(162,67,5,51.0000,179405.31,17940.53,161464.78,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(163,67,6,49.0000,172369.80,17236.98,155132.82,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(164,68,5,51.0000,179405.31,17940.53,161464.78,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(165,68,6,49.0000,172369.80,17236.98,155132.82,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(166,69,5,51.0000,179405.31,17940.53,161464.78,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(167,69,6,49.0000,172369.80,17236.98,155132.82,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(168,70,5,51.0000,179405.31,17940.53,161464.78,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(169,70,6,49.0000,172369.80,17236.98,155132.82,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(170,71,5,51.0000,179405.31,17940.53,161464.78,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(171,71,6,49.0000,172369.80,17236.98,155132.82,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(172,72,5,51.0000,188375.57,18837.56,169538.01,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(173,72,6,49.0000,180988.29,18098.83,162889.46,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(174,73,5,51.0000,188375.57,18837.56,169538.01,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(175,73,6,49.0000,180988.29,18098.83,162889.46,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(176,74,5,51.0000,188375.57,18837.56,169538.01,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(177,74,6,49.0000,180988.29,18098.83,162889.46,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(178,75,5,51.0000,188375.57,18837.56,169538.01,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(179,75,6,49.0000,180988.29,18098.83,162889.46,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(180,76,5,51.0000,188375.57,18837.56,169538.01,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(181,76,6,49.0000,180988.29,18098.83,162889.46,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(182,77,5,51.0000,188375.57,18837.56,169538.01,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(183,77,6,49.0000,180988.29,18098.83,162889.46,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(184,78,5,51.0000,197794.35,19779.44,178014.91,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(185,78,6,49.0000,190037.70,19003.77,171033.93,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(186,79,5,51.0000,197794.35,19779.44,178014.91,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(187,79,6,49.0000,190037.70,19003.77,171033.93,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(188,80,5,51.0000,197794.35,19779.44,178014.91,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(189,80,6,49.0000,190037.70,19003.77,171033.93,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(190,81,5,51.0000,197794.35,19779.44,178014.91,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(191,81,6,49.0000,190037.70,19003.77,171033.93,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(192,82,5,51.0000,197794.35,19779.44,178014.91,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(193,82,6,49.0000,190037.70,19003.77,171033.93,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(194,83,5,51.0000,197794.35,19779.44,178014.91,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(195,83,6,49.0000,190037.70,19003.77,171033.93,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(196,84,5,51.0000,207684.07,20768.41,186915.66,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(197,84,6,49.0000,199539.59,19953.96,179585.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(198,85,5,51.0000,207684.07,20768.41,186915.66,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(199,85,6,49.0000,199539.59,19953.96,179585.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(200,86,5,51.0000,207684.07,20768.41,186915.66,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(201,86,6,49.0000,199539.59,19953.96,179585.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(202,87,5,51.0000,207684.07,20768.41,186915.66,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(203,87,6,49.0000,199539.59,19953.96,179585.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(204,88,7,50.0000,380000.00,38000.00,342000.00,380000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(205,88,8,50.0000,380000.00,38000.00,342000.00,380000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(206,89,7,50.0000,380000.00,38000.00,342000.00,380000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(207,89,8,50.0000,380000.00,38000.00,342000.00,380000.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(208,90,7,50.0000,380000.00,38000.00,342000.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(209,90,8,50.0000,380000.00,38000.00,342000.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(210,91,7,50.0000,395200.00,39520.00,355680.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(211,91,8,50.0000,395200.00,39520.00,355680.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(212,92,7,50.0000,395200.00,39520.00,355680.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(213,92,8,50.0000,395200.00,39520.00,355680.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(214,93,7,50.0000,395200.00,39520.00,355680.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(215,93,8,50.0000,395200.00,39520.00,355680.00,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(216,94,7,50.0000,411008.00,41100.80,369907.20,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(217,94,8,50.0000,411008.00,41100.80,369907.20,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(218,95,7,50.0000,411008.00,41100.80,369907.20,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(219,95,8,50.0000,411008.00,41100.80,369907.20,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(220,96,7,50.0000,411008.00,41100.80,369907.20,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(221,96,8,50.0000,411008.00,41100.80,369907.20,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(222,97,7,50.0000,427448.32,42744.83,384703.49,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(223,97,8,50.0000,427448.32,42744.83,384703.49,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(224,98,7,50.0000,427448.32,42744.83,384703.49,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(225,98,8,50.0000,427448.32,42744.83,384703.49,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(226,99,7,50.0000,427448.32,42744.83,384703.49,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(227,99,8,50.0000,427448.32,42744.83,384703.49,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(228,100,7,50.0000,444546.26,44454.63,400091.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(229,100,8,50.0000,444546.25,44454.62,400091.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(230,101,7,50.0000,444546.26,44454.63,400091.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(231,101,8,50.0000,444546.25,44454.62,400091.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(232,102,7,50.0000,444546.26,44454.63,400091.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(233,102,8,50.0000,444546.25,44454.62,400091.63,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(234,103,7,50.0000,462328.11,46232.81,416095.30,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(235,103,8,50.0000,462328.10,46232.81,416095.29,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(236,104,7,50.0000,462328.11,46232.81,416095.30,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(237,104,8,50.0000,462328.10,46232.81,416095.29,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(238,105,7,50.0000,462328.11,46232.81,416095.30,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(239,105,8,50.0000,462328.10,46232.81,416095.29,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(240,106,7,50.0000,480821.23,48082.13,432739.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(241,106,8,50.0000,480821.22,48082.12,432739.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(242,107,7,50.0000,480821.23,48082.13,432739.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(243,107,8,50.0000,480821.22,48082.12,432739.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(244,108,7,50.0000,480821.23,48082.13,432739.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(245,108,8,50.0000,480821.22,48082.12,432739.10,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(246,109,7,50.0000,500054.08,50005.41,450048.67,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(247,109,8,50.0000,500054.07,50005.41,450048.66,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(248,110,7,50.0000,500054.08,50005.41,450048.67,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(249,110,8,50.0000,500054.07,50005.41,450048.66,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(250,111,7,50.0000,500054.08,50005.41,450048.67,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(251,111,8,50.0000,500054.07,50005.41,450048.66,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(252,112,7,50.0000,520056.24,52005.63,468050.61,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(253,112,8,50.0000,520056.24,52005.62,468050.62,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(254,113,7,50.0000,520056.24,52005.63,468050.61,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(255,113,8,50.0000,520056.24,52005.62,468050.62,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(256,114,7,50.0000,520056.24,52005.63,468050.61,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(257,114,8,50.0000,520056.24,52005.62,468050.62,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(258,115,7,50.0000,540858.49,54085.85,486772.64,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(259,115,8,50.0000,540858.49,54085.85,486772.64,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(260,116,7,50.0000,540858.49,54085.85,486772.64,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(261,116,8,50.0000,540858.49,54085.85,486772.64,0.00,'2026-11-17 13:18:21','2026-11-17 13:18:21');
/*!40000 ALTER TABLE `rent_allocations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rent_payments`
--

DROP TABLE IF EXISTS `rent_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rent_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_no` varchar(20) NOT NULL,
  `rent_allocation_id` bigint(20) unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `gross_amount` decimal(15,2) NOT NULL,
  `tds_deducted` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tds_challan_no` varchar(40) DEFAULT NULL,
  `net_paid` decimal(15,2) NOT NULL,
  `payment_mode` enum('NEFT','RTGS','Cheque','UPI','Other') NOT NULL DEFAULT 'NEFT',
  `utr_reference` varchar(60) NOT NULL,
  `sap_voucher_no` varchar(40) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rent_payments_payment_no_unique` (`payment_no`),
  KEY `rent_payments_rent_allocation_id_foreign` (`rent_allocation_id`),
  KEY `rent_payments_payment_date_index` (`payment_date`),
  KEY `rent_payments_utr_reference_index` (`utr_reference`),
  CONSTRAINT `rent_payments_rent_allocation_id_foreign` FOREIGN KEY (`rent_allocation_id`) REFERENCES `rent_allocations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rent_payments`
--

LOCK TABLES `rent_payments` WRITE;
/*!40000 ALTER TABLE `rent_payments` DISABLE KEYS */;
INSERT INTO `rent_payments` VALUES
(1,'PAY000001',1,'2024-04-04',2250000.00,225000.00,'CHL28445',2025000.00,'NEFT','UTIBR40112345','510002346',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(2,'PAY000002',2,'2024-04-04',1350000.00,135000.00,'CHL28446',1215000.00,'NEFT','UTIBR40112346','510002347',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(3,'PAY000003',3,'2024-04-04',900000.00,90000.00,'CHL28447',810000.00,'NEFT','UTIBR40112347','510002348',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(4,'PAY000004',88,'2024-11-23',133875.00,13387.50,'CHL28448',120487.50,'NEFT','UTIBR40112348','510002349',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(5,'PAY000005',89,'2024-11-23',128625.00,12862.50,'CHL28449',115762.50,'NEFT','UTIBR40112349','510002350',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(6,'PAY000006',204,'2025-01-13',380000.00,38000.00,'CHL28450',342000.00,'NEFT','UTIBR40112350','510002351',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(7,'PAY000007',205,'2025-01-13',380000.00,38000.00,'CHL28451',342000.00,'NEFT','UTIBR40112351','510002352',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(8,'PAY000008',4,'2025-04-04',2250000.00,225000.00,'CHL28452',2025000.00,'NEFT','UTIBR40112352','510002353',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(9,'PAY000009',5,'2025-04-04',1350000.00,135000.00,'CHL28453',1215000.00,'NEFT','UTIBR40112353','510002354',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(10,'PAY000010',6,'2025-04-04',900000.00,90000.00,'CHL28454',810000.00,'NEFT','UTIBR40112354','510002355',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(11,'PAY000011',90,'2025-05-23',133875.00,13387.50,'CHL28455',120487.50,'NEFT','UTIBR40112355','510002356',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(12,'PAY000012',91,'2025-05-23',128625.00,12862.50,'CHL28456',115762.50,'NEFT','UTIBR40112356','510002357',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(13,'PAY000013',92,'2025-11-23',133875.00,13387.50,'CHL28457',120487.50,'NEFT','UTIBR40112357','510002358',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(14,'PAY000014',93,'2025-11-23',128625.00,12862.50,'CHL28458',115762.50,'NEFT','UTIBR40112358','510002359',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(15,'PAY000015',206,'2026-01-13',380000.00,38000.00,'CHL28459',342000.00,'NEFT','UTIBR40112359','510002360',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(16,'PAY000016',207,'2026-01-13',380000.00,38000.00,'CHL28460',342000.00,'NEFT','UTIBR40112360','510002361',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(17,'PAY000017',7,'2026-04-04',1125000.00,112500.00,'CHL28461',1012500.00,'NEFT','UTIBR40112361','510002362',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(18,'PAY000018',94,'2026-05-23',133875.00,13387.50,'CHL28462',120487.50,'NEFT','UTIBR40112362','510002363',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(19,'PAY000019',95,'2026-05-23',128625.00,12862.50,'CHL28463',115762.50,'NEFT','UTIBR40112363','510002364',NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL);
/*!40000 ALTER TABLE `rent_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rent_schedules`
--

DROP TABLE IF EXISTS `rent_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rent_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lease_id` bigint(20) unsigned NOT NULL,
  `plot_id` bigint(20) unsigned NOT NULL,
  `installment_no` smallint(5) unsigned NOT NULL,
  `period_from` date NOT NULL,
  `period_to` date NOT NULL,
  `due_date` date NOT NULL,
  `rent_amount` decimal(15,2) NOT NULL,
  `tds_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_payable` decimal(15,2) NOT NULL,
  `ownership_version_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('Pending','Partially paid','Paid','On hold') NOT NULL DEFAULT 'Pending',
  `alert_sent` enum('Yes','No') NOT NULL DEFAULT 'No',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rent_schedules_lease_id_installment_no_unique` (`lease_id`,`installment_no`),
  KEY `rent_schedules_plot_id_foreign` (`plot_id`),
  KEY `rent_schedules_ownership_version_id_foreign` (`ownership_version_id`),
  KEY `rent_schedules_due_date_status_index` (`due_date`,`status`),
  CONSTRAINT `rent_schedules_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rent_schedules_ownership_version_id_foreign` FOREIGN KEY (`ownership_version_id`) REFERENCES `plot_ownership_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rent_schedules_plot_id_foreign` FOREIGN KEY (`plot_id`) REFERENCES `plots` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rent_schedules`
--

LOCK TABLES `rent_schedules` WRITE;
/*!40000 ALTER TABLE `rent_schedules` DISABLE KEYS */;
INSERT INTO `rent_schedules` VALUES
(1,1,1,1,'2024-04-01','2025-03-31','2024-04-01',4500000.00,450000.00,4050000.00,1,'Paid','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(2,1,1,2,'2025-04-01','2026-03-31','2025-04-01',4500000.00,450000.00,4050000.00,1,'Paid','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(3,1,1,3,'2026-04-01','2027-03-31','2026-04-01',4500000.00,450000.00,4050000.00,1,'Partially paid','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(4,1,1,4,'2027-04-01','2028-03-31','2027-04-01',4725000.00,472500.00,4252500.00,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(5,1,1,5,'2028-04-01','2029-03-31','2028-04-01',4725000.00,472500.00,4252500.00,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(6,1,1,6,'2029-04-01','2030-03-31','2029-04-01',4725000.00,472500.00,4252500.00,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(7,1,1,7,'2030-04-01','2031-03-31','2030-04-01',4961250.00,496125.00,4465125.00,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(8,1,1,8,'2031-04-01','2032-03-31','2031-04-01',4961250.00,496125.00,4465125.00,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(9,1,1,9,'2032-04-01','2033-03-31','2032-04-01',4961250.00,496125.00,4465125.00,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(10,1,1,10,'2033-04-01','2034-03-31','2033-04-01',5209312.50,520931.25,4688381.25,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(11,1,1,11,'2034-04-01','2035-03-31','2034-04-01',5209312.50,520931.25,4688381.25,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(12,1,1,12,'2035-04-01','2036-03-31','2035-04-01',5209312.50,520931.25,4688381.25,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(13,1,1,13,'2036-04-01','2037-03-31','2036-04-01',5469778.13,546977.81,4922800.32,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(14,1,1,14,'2037-04-01','2038-03-31','2037-04-01',5469778.13,546977.81,4922800.32,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(15,1,1,15,'2038-04-01','2039-03-31','2038-04-01',5469778.13,546977.81,4922800.32,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(16,1,1,16,'2039-04-01','2040-03-31','2039-04-01',5743267.03,574326.70,5168940.33,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(17,1,1,17,'2040-04-01','2041-03-31','2040-04-01',5743267.03,574326.70,5168940.33,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(18,1,1,18,'2041-04-01','2042-03-31','2041-04-01',5743267.03,574326.70,5168940.33,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(19,1,1,19,'2042-04-01','2043-03-31','2042-04-01',6030430.38,603043.04,5427387.34,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(20,1,1,20,'2043-04-01','2044-03-31','2043-04-01',6030430.38,603043.04,5427387.34,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(21,1,1,21,'2044-04-01','2045-03-31','2044-04-01',6030430.38,603043.04,5427387.34,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(22,1,1,22,'2045-04-01','2046-03-31','2045-04-01',6331951.90,633195.19,5698756.71,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(23,1,1,23,'2046-04-01','2047-03-31','2046-04-01',6331951.90,633195.19,5698756.71,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(24,1,1,24,'2047-04-01','2048-03-31','2047-04-01',6331951.90,633195.19,5698756.71,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(25,1,1,25,'2048-04-01','2049-03-31','2048-04-01',6648549.50,664854.95,5983694.55,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(26,1,1,26,'2049-04-01','2050-03-31','2049-04-01',6648549.50,664854.95,5983694.55,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(27,1,1,27,'2050-04-01','2051-03-31','2050-04-01',6648549.50,664854.95,5983694.55,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(28,1,1,28,'2051-04-01','2052-03-31','2051-04-01',6980976.97,698097.70,6282879.27,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(29,1,1,29,'2052-04-01','2053-03-31','2052-04-01',6980976.97,698097.70,6282879.27,1,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(30,2,2,1,'2024-11-20','2025-05-19','2024-11-20',262500.00,26250.00,236250.00,2,'Paid','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(31,2,2,2,'2025-05-20','2025-11-19','2025-05-20',262500.00,26250.00,236250.00,2,'Paid','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(32,2,2,3,'2025-11-20','2026-05-19','2025-11-20',262500.00,26250.00,236250.00,2,'Paid','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(33,2,2,4,'2026-05-20','2026-11-19','2026-05-20',262500.00,26250.00,236250.00,2,'Paid','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(34,2,2,5,'2026-11-20','2027-05-19','2026-11-20',262500.00,26250.00,236250.00,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(35,2,2,6,'2027-05-20','2027-11-19','2027-05-20',262500.00,26250.00,236250.00,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(36,2,2,7,'2027-11-20','2028-05-19','2027-11-20',275625.00,27562.50,248062.50,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(37,2,2,8,'2028-05-20','2028-11-19','2028-05-20',275625.00,27562.50,248062.50,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(38,2,2,9,'2028-11-20','2029-05-19','2028-11-20',275625.00,27562.50,248062.50,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(39,2,2,10,'2029-05-20','2029-11-19','2029-05-20',275625.00,27562.50,248062.50,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(40,2,2,11,'2029-11-20','2030-05-19','2029-11-20',275625.00,27562.50,248062.50,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(41,2,2,12,'2030-05-20','2030-11-19','2030-05-20',275625.00,27562.50,248062.50,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(42,2,2,13,'2030-11-20','2031-05-19','2030-11-20',289406.25,28940.63,260465.62,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(43,2,2,14,'2031-05-20','2031-11-19','2031-05-20',289406.25,28940.63,260465.62,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(44,2,2,15,'2031-11-20','2032-05-19','2031-11-20',289406.25,28940.63,260465.62,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(45,2,2,16,'2032-05-20','2032-11-19','2032-05-20',289406.25,28940.63,260465.62,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(46,2,2,17,'2032-11-20','2033-05-19','2032-11-20',289406.25,28940.63,260465.62,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(47,2,2,18,'2033-05-20','2033-11-19','2033-05-20',289406.25,28940.63,260465.62,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(48,2,2,19,'2033-11-20','2034-05-19','2033-11-20',303876.56,30387.66,273488.90,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(49,2,2,20,'2034-05-20','2034-11-19','2034-05-20',303876.56,30387.66,273488.90,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(50,2,2,21,'2034-11-20','2035-05-19','2034-11-20',303876.56,30387.66,273488.90,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(51,2,2,22,'2035-05-20','2035-11-19','2035-05-20',303876.56,30387.66,273488.90,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(52,2,2,23,'2035-11-20','2036-05-19','2035-11-20',303876.56,30387.66,273488.90,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(53,2,2,24,'2036-05-20','2036-11-19','2036-05-20',303876.56,30387.66,273488.90,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(54,2,2,25,'2036-11-20','2037-05-19','2036-11-20',319070.39,31907.04,287163.35,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(55,2,2,26,'2037-05-20','2037-11-19','2037-05-20',319070.39,31907.04,287163.35,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(56,2,2,27,'2037-11-20','2038-05-19','2037-11-20',319070.39,31907.04,287163.35,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(57,2,2,28,'2038-05-20','2038-11-19','2038-05-20',319070.39,31907.04,287163.35,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(58,2,2,29,'2038-11-20','2039-05-19','2038-11-20',319070.39,31907.04,287163.35,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(59,2,2,30,'2039-05-20','2039-11-19','2039-05-20',319070.39,31907.04,287163.35,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(60,2,2,31,'2039-11-20','2040-05-19','2039-11-20',335023.91,33502.39,301521.52,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(61,2,2,32,'2040-05-20','2040-11-19','2040-05-20',335023.91,33502.39,301521.52,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(62,2,2,33,'2040-11-20','2041-05-19','2040-11-20',335023.91,33502.39,301521.52,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(63,2,2,34,'2041-05-20','2041-11-19','2041-05-20',335023.91,33502.39,301521.52,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(64,2,2,35,'2041-11-20','2042-05-19','2041-11-20',335023.91,33502.39,301521.52,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(65,2,2,36,'2042-05-20','2042-11-19','2042-05-20',335023.91,33502.39,301521.52,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(66,2,2,37,'2042-11-20','2043-05-19','2042-11-20',351775.11,35177.51,316597.60,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(67,2,2,38,'2043-05-20','2043-11-19','2043-05-20',351775.11,35177.51,316597.60,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(68,2,2,39,'2043-11-20','2044-05-19','2043-11-20',351775.11,35177.51,316597.60,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(69,2,2,40,'2044-05-20','2044-11-19','2044-05-20',351775.11,35177.51,316597.60,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(70,2,2,41,'2044-11-20','2045-05-19','2044-11-20',351775.11,35177.51,316597.60,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(71,2,2,42,'2045-05-20','2045-11-19','2045-05-20',351775.11,35177.51,316597.60,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(72,2,2,43,'2045-11-20','2046-05-19','2045-11-20',369363.86,36936.39,332427.47,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(73,2,2,44,'2046-05-20','2046-11-19','2046-05-20',369363.86,36936.39,332427.47,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(74,2,2,45,'2046-11-20','2047-05-19','2046-11-20',369363.86,36936.39,332427.47,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(75,2,2,46,'2047-05-20','2047-11-19','2047-05-20',369363.86,36936.39,332427.47,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(76,2,2,47,'2047-11-20','2048-05-19','2047-11-20',369363.86,36936.39,332427.47,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(77,2,2,48,'2048-05-20','2048-11-19','2048-05-20',369363.86,36936.39,332427.47,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(78,2,2,49,'2048-11-20','2049-05-19','2048-11-20',387832.05,38783.21,349048.84,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(79,2,2,50,'2049-05-20','2049-11-19','2049-05-20',387832.05,38783.21,349048.84,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(80,2,2,51,'2049-11-20','2050-05-19','2049-11-20',387832.05,38783.21,349048.84,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(81,2,2,52,'2050-05-20','2050-11-19','2050-05-20',387832.05,38783.21,349048.84,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(82,2,2,53,'2050-11-20','2051-05-19','2050-11-20',387832.05,38783.21,349048.84,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(83,2,2,54,'2051-05-20','2051-11-19','2051-05-20',387832.05,38783.21,349048.84,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(84,2,2,55,'2051-11-20','2052-05-19','2051-11-20',407223.66,40722.37,366501.29,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(85,2,2,56,'2052-05-20','2052-11-19','2052-05-20',407223.66,40722.37,366501.29,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(86,2,2,57,'2052-11-20','2053-05-19','2052-11-20',407223.66,40722.37,366501.29,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(87,2,2,58,'2053-05-20','2053-11-19','2053-05-20',407223.66,40722.37,366501.29,2,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(88,3,4,1,'2025-01-10','2026-01-09','2025-01-10',760000.00,76000.00,684000.00,4,'Paid','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(89,3,4,2,'2026-01-10','2027-01-09','2026-01-10',760000.00,76000.00,684000.00,4,'Paid','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(90,3,4,3,'2027-01-10','2028-01-09','2027-01-10',760000.00,76000.00,684000.00,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(91,3,4,4,'2028-01-10','2029-01-09','2028-01-10',790400.00,79040.00,711360.00,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(92,3,4,5,'2029-01-10','2030-01-09','2029-01-10',790400.00,79040.00,711360.00,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(93,3,4,6,'2030-01-10','2031-01-09','2030-01-10',790400.00,79040.00,711360.00,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(94,3,4,7,'2031-01-10','2032-01-09','2031-01-10',822016.00,82201.60,739814.40,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(95,3,4,8,'2032-01-10','2033-01-09','2032-01-10',822016.00,82201.60,739814.40,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(96,3,4,9,'2033-01-10','2034-01-09','2033-01-10',822016.00,82201.60,739814.40,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(97,3,4,10,'2034-01-10','2035-01-09','2034-01-10',854896.64,85489.66,769406.98,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(98,3,4,11,'2035-01-10','2036-01-09','2035-01-10',854896.64,85489.66,769406.98,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(99,3,4,12,'2036-01-10','2037-01-09','2036-01-10',854896.64,85489.66,769406.98,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(100,3,4,13,'2037-01-10','2038-01-09','2037-01-10',889092.51,88909.25,800183.26,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(101,3,4,14,'2038-01-10','2039-01-09','2038-01-10',889092.51,88909.25,800183.26,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(102,3,4,15,'2039-01-10','2040-01-09','2039-01-10',889092.51,88909.25,800183.26,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(103,3,4,16,'2040-01-10','2041-01-09','2040-01-10',924656.21,92465.62,832190.59,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(104,3,4,17,'2041-01-10','2042-01-09','2041-01-10',924656.21,92465.62,832190.59,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(105,3,4,18,'2042-01-10','2043-01-09','2042-01-10',924656.21,92465.62,832190.59,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(106,3,4,19,'2043-01-10','2044-01-09','2043-01-10',961642.45,96164.25,865478.20,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(107,3,4,20,'2044-01-10','2045-01-09','2044-01-10',961642.45,96164.25,865478.20,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(108,3,4,21,'2045-01-10','2046-01-09','2045-01-10',961642.45,96164.25,865478.20,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(109,3,4,22,'2046-01-10','2047-01-09','2046-01-10',1000108.15,100010.82,900097.33,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(110,3,4,23,'2047-01-10','2048-01-09','2047-01-10',1000108.15,100010.82,900097.33,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(111,3,4,24,'2048-01-10','2049-01-09','2048-01-10',1000108.15,100010.82,900097.33,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(112,3,4,25,'2049-01-10','2050-01-09','2049-01-10',1040112.48,104011.25,936101.23,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(113,3,4,26,'2050-01-10','2051-01-09','2050-01-10',1040112.48,104011.25,936101.23,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(114,3,4,27,'2051-01-10','2052-01-09','2051-01-10',1040112.48,104011.25,936101.23,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(115,3,4,28,'2052-01-10','2053-01-09','2052-01-10',1081716.98,108171.70,973545.28,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(116,3,4,29,'2053-01-10','2054-01-09','2053-01-10',1081716.98,108171.70,973545.28,4,'Pending','No',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21');
/*!40000 ALTER TABLE `rent_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `module` varchar(40) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `can_add` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `can_export` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_id_module_unique` (`role_id`,`module`),
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES
(1,1,'state',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(2,1,'city',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(3,1,'taluka',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(4,1,'party',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(5,1,'park',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(6,1,'spv',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(7,1,'plot',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(8,1,'ownership',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(9,1,'grid',1,0,1,0,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(10,1,'lease',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(11,1,'schedule',1,0,1,0,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(12,1,'payment',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(13,1,'sublease',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(14,1,'compliance',1,1,1,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(15,1,'document',1,1,0,1,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(16,1,'report',1,0,0,0,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(17,1,'audit',1,0,0,0,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(18,1,'setting',1,0,1,0,0,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(19,2,'state',1,1,1,0,0,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(20,2,'city',1,1,1,0,0,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(21,2,'taluka',1,1,1,0,0,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(22,2,'party',1,1,1,0,0,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(23,2,'park',1,1,1,0,0,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(24,2,'spv',1,0,0,0,0,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(25,2,'plot',1,1,1,0,1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(26,2,'ownership',1,1,1,1,0,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(27,2,'grid',1,0,1,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(28,2,'lease',1,1,1,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(29,2,'schedule',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(30,2,'payment',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(31,2,'sublease',1,1,1,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(32,2,'compliance',1,1,1,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(33,2,'document',1,1,0,1,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(34,2,'report',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(35,2,'audit',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(36,2,'setting',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(37,3,'state',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(38,3,'city',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(39,3,'taluka',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(40,3,'party',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(41,3,'park',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(42,3,'spv',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(43,3,'plot',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(44,3,'ownership',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(45,3,'grid',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(46,3,'lease',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(47,3,'schedule',1,0,1,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(48,3,'payment',1,1,1,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(49,3,'sublease',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(50,3,'compliance',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(51,3,'document',1,1,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(52,3,'report',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(53,3,'audit',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(54,3,'setting',0,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(55,4,'state',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(56,4,'city',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(57,4,'taluka',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(58,4,'party',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(59,4,'park',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(60,4,'spv',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(61,4,'plot',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(62,4,'ownership',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(63,4,'grid',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(64,4,'lease',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(65,4,'schedule',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(66,4,'payment',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(67,4,'sublease',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(68,4,'compliance',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(69,4,'document',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(70,4,'report',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(71,4,'audit',1,0,0,0,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(72,4,'setting',1,0,0,0,0,'2026-11-17 13:18:20','2026-11-17 13:18:20');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_super` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'Super Admin','Full access. Manages users, roles and SPV access.',1,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(2,'Land Manager','Maintains survey numbers, landowners, leases, sub-leases, compliance, documents and location masters.',0,'2026-11-17 13:18:19','2026-11-17 13:18:19'),
(3,'Finance','Maintains the rent schedule and records landowner payments.',0,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(4,'Auditor','Read-only access to everything, with export.',0,'2026-11-17 13:18:20','2026-11-17 13:18:20');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(80) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
(1,'land_ref.format','{PARK}-{STATE}-{CITY}-{TALUKA}-{SEQ}','Land Reference Number format',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(2,'land_ref.seq_pad','4','Digits in the sequence',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(3,'land_ref.seq_scope','prefix','prefix = per park+location, park = one per park',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spvs`
--

DROP TABLE IF EXISTS `spvs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `spvs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `spv_code` varchar(12) NOT NULL,
  `spv_name` varchar(150) NOT NULL,
  `park_id` bigint(20) unsigned NOT NULL,
  `party_id` bigint(20) unsigned DEFAULT NULL,
  `cin` varchar(21) DEFAULT NULL,
  `incorporation_date` date DEFAULT NULL,
  `registered_address` varchar(255) DEFAULT NULL,
  `contact_person` varchar(120) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `technology` enum('Solar PV','Wind','Hybrid') NOT NULL DEFAULT 'Solar PV',
  `capacity_mwp` decimal(10,3) DEFAULT NULL,
  `project_status` enum('Development','Under construction','Commissioned') NOT NULL DEFAULT 'Development',
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spvs_spv_code_unique` (`spv_code`),
  KEY `spvs_park_id_foreign` (`park_id`),
  KEY `spvs_party_id_foreign` (`party_id`),
  CONSTRAINT `spvs_park_id_foreign` FOREIGN KEY (`park_id`) REFERENCES `parks` (`id`),
  CONSTRAINT `spvs_party_id_foreign` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spvs`
--

LOCK TABLES `spvs` WRITE;
/*!40000 ALTER TABLE `spvs` DISABLE KEYS */;
INSERT INTO `spvs` VALUES
(1,'PGP10','Sample SPV Ten Pvt Ltd',1,1,'U40106GJ2023PTC123456',NULL,NULL,NULL,NULL,NULL,'Solar PV',230.000,'Under construction','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(2,'PGP11','Sample SPV Eleven Pvt Ltd',1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Hybrid',20.000,'Development','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(3,'PGP12','Sample SPV Twelve Pvt Ltd',2,1,NULL,NULL,NULL,NULL,NULL,NULL,'Solar PV',75.000,'Development','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL);
/*!40000 ALTER TABLE `spvs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `states` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `code` char(2) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `states_name_unique` (`name`),
  UNIQUE KEY `states_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `states`
--

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES
(1,'Gujarat','GJ','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(2,'Maharashtra','MH','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(3,'Rajasthan','RJ','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(4,'Karnataka','KA','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(5,'Madhya Pradesh','MP','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(6,'Andhra Pradesh','AP','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL);
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sub_leases`
--

DROP TABLE IF EXISTS `sub_leases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sub_leases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sub_lease_no` varchar(20) NOT NULL,
  `lease_id` bigint(20) unsigned NOT NULL,
  `park_id` bigint(20) unsigned NOT NULL,
  `spv_id` bigint(20) unsigned NOT NULL,
  `party_id` bigint(20) unsigned NOT NULL,
  `capacity_mw` decimal(10,3) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `rent_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `rent_frequency` enum('Annual','Half-yearly','Quarterly','Monthly') NOT NULL DEFAULT 'Annual',
  `payment_terms` text DEFAULT NULL,
  `agreement_no` varchar(40) DEFAULT NULL,
  `lender_noc_obtained` enum('Yes','No') NOT NULL DEFAULT 'No',
  `status` enum('Draft','Active','Expired','Cancelled') NOT NULL DEFAULT 'Draft',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sub_leases_sub_lease_no_unique` (`sub_lease_no`),
  KEY `sub_leases_park_id_foreign` (`park_id`),
  KEY `sub_leases_spv_id_foreign` (`spv_id`),
  KEY `sub_leases_capacity_idx` (`lease_id`,`status`,`start_date`,`end_date`),
  KEY `sub_leases_party_id_index` (`party_id`),
  CONSTRAINT `sub_leases_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`id`),
  CONSTRAINT `sub_leases_park_id_foreign` FOREIGN KEY (`park_id`) REFERENCES `parks` (`id`),
  CONSTRAINT `sub_leases_party_id_foreign` FOREIGN KEY (`party_id`) REFERENCES `parties` (`id`),
  CONSTRAINT `sub_leases_spv_id_foreign` FOREIGN KEY (`spv_id`) REFERENCES `spvs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sub_leases`
--

LOCK TABLES `sub_leases` WRITE;
/*!40000 ALTER TABLE `sub_leases` DISABLE KEYS */;
INSERT INTO `sub_leases` VALUES
(1,'SUB00001',1,1,1,9,20.000,'2025-01-01','2053-03-31',2400000.00,'Annual','Annual in advance, 15 days credit','SLA-PGP10-001','Yes','Active',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(2,'SUB00002',1,1,1,10,30.000,'2025-04-01','2053-03-31',3600000.00,'Annual',NULL,'SLA-PGP10-002','Yes','Active',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(3,'SUB00003',1,1,1,11,50.000,'2025-06-01','2053-03-31',6000000.00,'Annual',NULL,'SLA-PGP10-003','Yes','Active',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL),
(4,'SUB00004',3,2,3,12,20.000,'2025-03-01','2054-01-09',800000.00,'Annual',NULL,'SLA-PGP12-001','Yes','Active',NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL);
/*!40000 ALTER TABLE `sub_leases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `talukas`
--

DROP TABLE IF EXISTS `talukas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `talukas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` bigint(20) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  `code` varchar(5) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `talukas_city_id_code_unique` (`city_id`,`code`),
  UNIQUE KEY `talukas_city_id_name_unique` (`city_id`,`name`),
  CONSTRAINT `talukas_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `talukas`
--

LOCK TABLES `talukas` WRITE;
/*!40000 ALTER TABLE `talukas` DISABLE KEYS */;
INSERT INTO `talukas` VALUES
(1,1,'Talaja','TLJ','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(2,1,'Mahuva','MHV','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(3,1,'Bhavnagar','BVN','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(4,2,'Rajula','RJL','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(5,3,'Bhuj','BHJ','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(6,3,'Rapar','RPR','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(7,4,'Mohol','MHL','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(8,4,'Pandharpur','PDP','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(9,5,'Baramati','BRM','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(10,6,'Phalodi','PHL','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(11,7,'Kolayat','KLY','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(12,8,'Pavagada','PVG','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(13,9,'Jawad','JWD','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(14,10,'Panyam','PNM','Active',NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL);
/*!40000 ALTER TABLE `talukas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_spv_access`
--

DROP TABLE IF EXISTS `user_spv_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_spv_access` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `spv_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_spv_access_user_id_spv_id_unique` (`user_id`,`spv_id`),
  KEY `user_spv_access_spv_id_foreign` (`spv_id`),
  CONSTRAINT `user_spv_access_spv_id_foreign` FOREIGN KEY (`spv_id`) REFERENCES `spvs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_spv_access_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_spv_access`
--

LOCK TABLES `user_spv_access` WRITE;
/*!40000 ALTER TABLE `user_spv_access` DISABLE KEYS */;
INSERT INTO `user_spv_access` VALUES
(1,2,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(2,2,2,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(3,2,3,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(4,3,1,'2026-11-17 13:18:20','2026-11-17 13:18:20'),
(5,4,1,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(6,4,2,'2026-11-17 13:18:21','2026-11-17 13:18:21'),
(7,4,3,'2026-11-17 13:18:21','2026-11-17 13:18:21');
/*!40000 ALTER TABLE `user_spv_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(150) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `failed_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Super Admin','superadmin','admin@example.com',NULL,'$2y$12$82yt5TGjcIGfA.Ke/6SbSeJImiM9Nyftk38LPx24FOK6yhzpe40tW',1,1,0,0,NULL,NULL,NULL,NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(2,'Priya Desai','priya.land','priya.land@example.com',NULL,'$2y$12$uvpXBNSckKxHh6ifzJOWaOR/0GUFg/TnccKWODjk5LESogZeG71Xu',2,1,0,0,NULL,NULL,NULL,NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(3,'Kunal Mehta','kunal.fin','kunal.fin@example.com',NULL,'$2y$12$VuhTMqmZzTpdq/oZ83Cfoe1qUAnu./FN1Fqi2GQzb7PiCmTxP0mG.',3,1,0,0,NULL,NULL,NULL,NULL,NULL,'2026-11-17 13:18:20','2026-11-17 13:18:20',NULL),
(4,'Anita Rao','anita.audit','anita.audit@example.com',NULL,'$2y$12$DzvOlS2Jxnffb6qRUDrgeuP3L0JRnam.z2ix26s55ilbSj4FLjB6a',4,1,0,0,NULL,NULL,NULL,NULL,NULL,'2026-11-17 13:18:21','2026-11-17 13:18:21',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'solar_land'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
