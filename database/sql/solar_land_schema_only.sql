/*M!999999\- enable the sandbox mode */ 

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
  `record_id` varchar(60) DEFAULT NULL,
  `record_label` varchar(120) DEFAULT NULL,
  `action` enum('CREATE','UPDATE','DELETE','EXPORT','LOGIN','LOGIN_FAILED','LOGOUT') NOT NULL,
  `field_name` varchar(80) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `audit_logs_module_record_label_index` (`module`,`record_label`),
  KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `audit_logs_action_index` (`action`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` bigint(20) unsigned NOT NULL,
  `district_id` bigint(20) unsigned NOT NULL,
  `city_name` varchar(80) NOT NULL,
  `city_type` enum('Taluka','Tehsil','City') NOT NULL DEFAULT 'Taluka',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cities_district_id_city_name_unique` (`district_id`,`city_name`),
  KEY `cities_state_id_foreign` (`state_id`),
  CONSTRAINT `cities_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  CONSTRAINT `cities_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `compliances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `compliances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `land_id` bigint(20) unsigned NOT NULL,
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
  `escalation_level` varchar(30) NOT NULL DEFAULT 'L0 · None',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compliances_land_id_unique` (`land_id`),
  CONSTRAINT `compliances_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `districts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` bigint(20) unsigned NOT NULL,
  `district_name` varchar(80) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `districts_state_id_district_name_unique` (`state_id`,`district_name`),
  CONSTRAINT `districts_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `land_id` bigint(20) unsigned NOT NULL,
  `linked_entity_type` enum('Land','Survey','Farmer','Lease','Sublease','Compliance','Payment') NOT NULL DEFAULT 'Land',
  `linked_entity_id` bigint(20) unsigned NOT NULL,
  `doc_type` varchar(60) NOT NULL,
  `doc_title` varchar(200) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size_kb` int(10) unsigned NOT NULL DEFAULT 0,
  `mime_type` varchar(120) DEFAULT NULL,
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
  KEY `documents_land_id_linked_entity_type_linked_entity_id_index` (`land_id`,`linked_entity_type`,`linked_entity_id`),
  KEY `documents_land_id_doc_type_doc_title_index` (`land_id`,`doc_type`,`doc_title`),
  CONSTRAINT `documents_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`),
  CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `land_owners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `land_owners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `land_id` bigint(20) unsigned NOT NULL,
  `owner_name` varchar(150) NOT NULL,
  `ownership_pct` decimal(7,4) NOT NULL,
  `owner_pan` varchar(10) DEFAULT NULL,
  `aadhaar` varchar(20) DEFAULT NULL,
  `voter_id` varchar(30) DEFAULT NULL,
  `bank_ac` text DEFAULT NULL,
  `bank_branch` varchar(150) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `land_owners_land_id_owner_pan_index` (`land_id`,`owner_pan`),
  CONSTRAINT `land_owners_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `land_surveys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `land_surveys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `land_id` bigint(20) unsigned NOT NULL,
  `state_id` bigint(20) unsigned NOT NULL,
  `district_id` bigint(20) unsigned NOT NULL,
  `city_id` bigint(20) unsigned NOT NULL,
  `village` varchar(100) NOT NULL,
  `survey_plot_no` varchar(30) NOT NULL,
  `area_sqm` decimal(14,2) NOT NULL,
  `area_acres` decimal(14,2) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `land_surveys_land_id_village_survey_plot_no_unique` (`land_id`,`village`,`survey_plot_no`),
  KEY `land_surveys_state_id_foreign` (`state_id`),
  KEY `land_surveys_district_id_foreign` (`district_id`),
  KEY `land_surveys_city_id_foreign` (`city_id`),
  CONSTRAINT `land_surveys_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `land_surveys_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  CONSTRAINT `land_surveys_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`),
  CONSTRAINT `land_surveys_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `land_ref_no` varchar(60) NOT NULL,
  `park_id` bigint(20) unsigned DEFAULT NULL,
  `spv_id` bigint(20) unsigned NOT NULL,
  `state_id` bigint(20) unsigned NOT NULL,
  `district_id` bigint(20) unsigned NOT NULL,
  `city_id` bigint(20) unsigned NOT NULL,
  `parcel_code` varchar(10) NOT NULL,
  `ref_year` smallint(5) unsigned NOT NULL,
  `area_sqm_712` decimal(14,2) NOT NULL,
  `area_acres_712` decimal(14,2) NOT NULL,
  `area_under_lease_acres` decimal(14,2) NOT NULL,
  `dlr_sqm` decimal(14,2) DEFAULT NULL,
  `rate_per_acre` decimal(15,2) NOT NULL,
  `increment_after_yrs` tinyint(3) unsigned NOT NULL DEFAULT 3,
  `increment_pct` decimal(6,2) NOT NULL DEFAULT 5.00,
  `title_report` enum('Yes','No') NOT NULL DEFAULT 'No',
  `title_dd_status` enum('Pending','In progress','Clear','Issue found') NOT NULL DEFAULT 'Pending',
  `lessor_capacity_verified` enum('Yes','No') NOT NULL DEFAULT 'No',
  `encumbrance_status` enum('Under verification','Nil','Encumbered') NOT NULL DEFAULT 'Under verification',
  `lien_details` text DEFAULT NULL,
  `na_application` enum('Yes','No') NOT NULL DEFAULT 'No',
  `na_status` enum('Not applied','Applied','Approved','Rejected') NOT NULL DEFAULT 'Not applied',
  `land_status` enum('Identified','ATL signed','Leased','Subleased','Mortgaged','Dropped') NOT NULL DEFAULT 'Identified',
  `connectivity_mw` decimal(10,3) DEFAULT NULL,
  `substation` varchar(120) DEFAULT NULL,
  `aggregator` varchar(120) DEFAULT NULL,
  `tfr_confirmation` enum('Yes','No') NOT NULL DEFAULT 'No',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lands_land_ref_no_unique` (`land_ref_no`),
  KEY `lands_state_id_foreign` (`state_id`),
  KEY `lands_district_id_foreign` (`district_id`),
  KEY `lands_city_id_foreign` (`city_id`),
  KEY `lands_spv_id_land_status_index` (`spv_id`,`land_status`),
  KEY `lands_park_id_index` (`park_id`),
  CONSTRAINT `lands_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `lands_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  CONSTRAINT `lands_park_id_foreign` FOREIGN KEY (`park_id`) REFERENCES `parks` (`id`),
  CONSTRAINT `lands_spv_id_foreign` FOREIGN KEY (`spv_id`) REFERENCES `spvs` (`id`),
  CONSTRAINT `lands_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `land_id` bigint(20) unsigned NOT NULL,
  `lessor_name` varchar(200) NOT NULL,
  `lessor_pan` varchar(10) DEFAULT NULL,
  `leased_area_acres` decimal(14,2) NOT NULL,
  `leased_capacity_mw` decimal(10,3) NOT NULL DEFAULT 0.000,
  `lease_status` enum('Draft','Active','Expired','Terminated') NOT NULL DEFAULT 'Active',
  `lessor_address` varchar(255) DEFAULT NULL,
  `lessor_state_id` bigint(20) unsigned NOT NULL,
  `lessor_district_id` bigint(20) unsigned NOT NULL,
  `lessor_city_id` bigint(20) unsigned NOT NULL,
  `lessor_pincode` char(6) DEFAULT NULL,
  `atl_date` date DEFAULT NULL,
  `lease_date` date DEFAULT NULL,
  `lease_start_date` date NOT NULL,
  `lease_duration_mths` smallint(5) unsigned NOT NULL,
  `lease_end_date` date NOT NULL,
  `uni_number` varchar(40) DEFAULT NULL,
  `sro_doc_no` varchar(40) DEFAULT NULL,
  `agreement_no` varchar(40) DEFAULT NULL,
  `mutation_entry_no` varchar(40) DEFAULT NULL,
  `rent_rate_per_acre` decimal(15,2) NOT NULL,
  `rent_frequency` enum('Annual','Half-yearly','Quarterly','Monthly') NOT NULL DEFAULT 'Annual',
  `rent_amount` decimal(15,2) NOT NULL,
  `security_deposit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `escalation_pct` decimal(6,2) NOT NULL DEFAULT 0.00,
  `escalation_every_yrs` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `tds_applicable` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `tds_rate` decimal(5,2) NOT NULL DEFAULT 10.00,
  `gst_applicable` enum('Yes','No') NOT NULL DEFAULT 'No',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leases_lessor_state_id_foreign` (`lessor_state_id`),
  KEY `leases_lessor_district_id_foreign` (`lessor_district_id`),
  KEY `leases_lessor_city_id_foreign` (`lessor_city_id`),
  KEY `leases_land_id_lease_status_index` (`land_id`,`lease_status`),
  CONSTRAINT `leases_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`),
  CONSTRAINT `leases_lessor_city_id_foreign` FOREIGN KEY (`lessor_city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `leases_lessor_district_id_foreign` FOREIGN KEY (`lessor_district_id`) REFERENCES `districts` (`id`),
  CONSTRAINT `leases_lessor_state_id_foreign` FOREIGN KEY (`lessor_state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `parks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `parks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `park_code` varchar(6) NOT NULL,
  `park_name` varchar(150) NOT NULL,
  `state_id` bigint(20) unsigned NOT NULL,
  `district_id` bigint(20) unsigned NOT NULL,
  `city_id` bigint(20) unsigned NOT NULL,
  `total_area_acres` decimal(14,2) DEFAULT NULL,
  `installed_capacity_mw` decimal(10,3) NOT NULL DEFAULT 0.000,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parks_park_code_unique` (`park_code`),
  KEY `parks_state_id_foreign` (`state_id`),
  KEY `parks_district_id_foreign` (`district_id`),
  KEY `parks_city_id_foreign` (`city_id`),
  CONSTRAINT `parks_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `parks_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  CONSTRAINT `parks_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` bigint(20) unsigned NOT NULL,
  `land_id` bigint(20) unsigned NOT NULL,
  `farmer_id` bigint(20) unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `gross_amount` decimal(15,2) NOT NULL,
  `tds_deducted` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tds_challan_no` varchar(40) DEFAULT NULL,
  `net_paid` decimal(15,2) NOT NULL,
  `utr_reference` varchar(60) NOT NULL,
  `sap_voucher_no` varchar(40) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_land_id_foreign` (`land_id`),
  KEY `payments_farmer_id_foreign` (`farmer_id`),
  KEY `payments_schedule_id_farmer_id_index` (`schedule_id`,`farmer_id`),
  KEY `payments_payment_date_index` (`payment_date`),
  CONSTRAINT `payments_farmer_id_foreign` FOREIGN KEY (`farmer_id`) REFERENCES `land_owners` (`id`),
  CONSTRAINT `payments_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`),
  CONSTRAINT `payments_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `rent_schedules` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rent_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rent_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lease_id` bigint(20) unsigned NOT NULL,
  `land_id` bigint(20) unsigned NOT NULL,
  `due_month` varchar(10) NOT NULL,
  `due_date` date NOT NULL,
  `rent_amount` decimal(15,2) NOT NULL,
  `tds_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_payable` decimal(15,2) NOT NULL,
  `payment_status` enum('Pending','Partial Payment','Paid','On hold') NOT NULL DEFAULT 'Pending',
  `alert_sent_flag` enum('Yes','No') NOT NULL DEFAULT 'No',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rent_schedules_land_id_foreign` (`land_id`),
  KEY `rent_schedules_lease_id_due_date_index` (`lease_id`,`due_date`),
  KEY `rent_schedules_due_date_payment_status_index` (`due_date`,`payment_status`),
  CONSTRAINT `rent_schedules_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`),
  CONSTRAINT `rent_schedules_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `spvs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `spvs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` bigint(20) unsigned NOT NULL,
  `park_id` bigint(20) unsigned NOT NULL,
  `spv_code` varchar(12) NOT NULL,
  `spv_name` varchar(150) NOT NULL,
  `technology` enum('Solar PV','Wind','Hybrid') NOT NULL DEFAULT 'Solar PV',
  `capacity_mwp` decimal(10,3) DEFAULT NULL,
  `status` enum('Development','Under construction','Commissioned') NOT NULL DEFAULT 'Development',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spvs_spv_code_unique` (`spv_code`),
  KEY `spvs_state_id_foreign` (`state_id`),
  KEY `spvs_park_id_index` (`park_id`),
  CONSTRAINT `spvs_park_id_foreign` FOREIGN KEY (`park_id`) REFERENCES `parks` (`id`),
  CONSTRAINT `spvs_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `states` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_name` varchar(80) NOT NULL,
  `state_code` char(2) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `states_state_name_unique` (`state_name`),
  UNIQUE KEY `states_state_code_unique` (`state_code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subleases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subleases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lease_id` bigint(20) unsigned NOT NULL,
  `land_id` bigint(20) unsigned NOT NULL,
  `sublessee_name` varchar(150) NOT NULL,
  `sublessee_pan` varchar(10) DEFAULT NULL,
  `sublease_area_acres` decimal(14,2) NOT NULL,
  `capacity_mw` decimal(10,3) NOT NULL DEFAULT 0.000,
  `sublease_rent` decimal(15,2) DEFAULT NULL,
  `sublease_start` date NOT NULL,
  `sublease_end` date NOT NULL,
  `agreement_no` varchar(40) DEFAULT NULL,
  `lender_noc_obtained` enum('Yes','No') NOT NULL DEFAULT 'No',
  `status` enum('Draft','Active','Terminated') NOT NULL DEFAULT 'Active',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subleases_land_id_foreign` (`land_id`),
  KEY `subleases_capacity_idx` (`lease_id`,`status`,`sublease_start`,`sublease_end`),
  CONSTRAINT `subleases_land_id_foreign` FOREIGN KEY (`land_id`) REFERENCES `lands` (`id`),
  CONSTRAINT `subleases_lease_id_foreign` FOREIGN KEY (`lease_id`) REFERENCES `leases` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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

