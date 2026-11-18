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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
