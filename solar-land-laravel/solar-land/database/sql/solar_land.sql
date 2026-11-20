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

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES
(1,NULL,'system','127.0.0.1','state','STATE_MASTER','1','Gujarat','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(2,NULL,'system','127.0.0.1','state','STATE_MASTER','2','Maharashtra','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(3,NULL,'system','127.0.0.1','state','STATE_MASTER','3','Rajasthan','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(4,NULL,'system','127.0.0.1','state','STATE_MASTER','4','Karnataka','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(5,NULL,'system','127.0.0.1','state','STATE_MASTER','5','Madhya Pradesh','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(6,NULL,'system','127.0.0.1','state','STATE_MASTER','6','Andhra Pradesh','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(7,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','1','Bhavnagar','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(8,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','2','Amreli','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(9,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','3','Kutch','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(10,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','4','Solapur','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(11,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','5','Pune','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(12,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','6','Jodhpur','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(13,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','7','Bikaner','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(14,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','8','Tumakuru','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(15,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','9','Neemuch','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(16,NULL,'system','127.0.0.1','district','DISTRICT_MASTER','10','Kurnool','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(17,NULL,'system','127.0.0.1','city','CITY_MASTER','1','Talaja','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(18,NULL,'system','127.0.0.1','city','CITY_MASTER','2','Mahuva','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(19,NULL,'system','127.0.0.1','city','CITY_MASTER','3','Bhavnagar','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(20,NULL,'system','127.0.0.1','city','CITY_MASTER','4','Rajula','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(21,NULL,'system','127.0.0.1','city','CITY_MASTER','5','Bhuj','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(22,NULL,'system','127.0.0.1','city','CITY_MASTER','6','Rapar','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(23,NULL,'system','127.0.0.1','city','CITY_MASTER','7','Mohol','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(24,NULL,'system','127.0.0.1','city','CITY_MASTER','8','Pandharpur','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(25,NULL,'system','127.0.0.1','city','CITY_MASTER','9','Solapur','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(26,NULL,'system','127.0.0.1','city','CITY_MASTER','10','Baramati','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(27,NULL,'system','127.0.0.1','city','CITY_MASTER','11','Phalodi','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(28,NULL,'system','127.0.0.1','city','CITY_MASTER','12','Kolayat','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(29,NULL,'system','127.0.0.1','city','CITY_MASTER','13','Pavagada','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(30,NULL,'system','127.0.0.1','city','CITY_MASTER','14','Jawad','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(31,NULL,'system','127.0.0.1','city','CITY_MASTER','15','Panyam','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(32,NULL,'system','127.0.0.1','user','user','1','superadmin','CREATE',NULL,NULL,'New record','2026-11-19 14:00:49'),
(33,2,'priya.land','10.20.4.27','city','CITY_MASTER','6','Rapar','CREATE',NULL,NULL,'New record','2026-10-13 11:02:00'),
(34,2,'priya.land','10.20.4.27','land','LAND_MASTER','2','PGP10-GJ-BHV-A2-2024','UPDATE','title_dd_status','Pending','In progress','2026-10-14 12:08:00'),
(35,3,'kunal.fin','10.20.6.42','schedule','RENT_SCHEDULE','7','RS0007','UPDATE','alert_sent_flag','No','Yes','2026-10-15 09:31:00'),
(36,2,'priya.land','10.20.4.27','compliance','COMPLIANCE_TRACKER','2','CMP002','UPDATE','escalation_level','L1 · Land head','L2 · CFO','2026-10-15 16:02:00'),
(37,1,'superadmin','10.20.4.11','user','USER_MASTER','3','kunal.fin','UPDATE','spv_access','PGP10, PGP12','PGP10','2026-10-16 09:20:00'),
(38,4,'anita.audit','10.20.8.5','land','LAND_MASTER',NULL,'Land Master','EXPORT',NULL,NULL,'4 rows to xlsx','2026-10-16 09:45:00'),
(39,3,'kunal.fin','10.20.6.42','session','SESSION','3','kunal.fin','LOGIN_FAILED','password',NULL,'Wrong password (1 of 5)','2026-10-16 10:04:00'),
(40,3,'kunal.fin','10.20.6.42','session','SESSION','3','kunal.fin','LOGIN',NULL,NULL,'Signed in','2026-10-16 10:05:00');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES
(1,1,1,'Talaja','Taluka',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(2,1,1,'Mahuva','Taluka',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(3,1,1,'Bhavnagar','City',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(4,1,2,'Rajula','Taluka',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(5,1,3,'Bhuj','Taluka',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(6,1,3,'Rapar','Taluka',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(7,2,4,'Mohol','Taluka',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(8,2,4,'Pandharpur','Taluka',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(9,2,4,'Solapur','City',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(10,2,5,'Baramati','Taluka',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(11,3,6,'Phalodi','Tehsil',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(12,3,7,'Kolayat','Tehsil',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(13,4,8,'Pavagada','Taluka',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(14,5,9,'Jawad','Tehsil',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(15,6,10,'Panyam','Tehsil',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL);
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `compliances` WRITE;
/*!40000 ALTER TABLE `compliances` DISABLE KEYS */;
INSERT INTO `compliances` VALUES
(1,3,'Yes','2025-03-12','Sample Lender Bank','400012345678','With lender','Sample Lender Bank, Ahmedabad','2025-03-12','Obtained',2,'2026-12-31','L1 · Land head','Mutation of mortgage charge pending in 7/12.',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(2,1,'No',NULL,NULL,NULL,'With company','Corporate legal vault','2024-04-05','Not required',3,'2026-09-30','L2 · CFO','NA order copy and two heirship affidavits outstanding.',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL);
/*!40000 ALTER TABLE `compliances` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES
(1,1,'Bhavnagar',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(2,1,'Amreli',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(3,1,'Kutch',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(4,2,'Solapur',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(5,2,'Pune',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(6,3,'Jodhpur',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(7,3,'Bikaner',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(8,4,'Tumakuru',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(9,5,'Neemuch',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(10,6,'Kurnool',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL);
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES
(1,1,'Survey',1,'7/12 extract','7/12 extract survey 112/2','712_112-2_Trapaj.pdf','documents/PGP10/PGP10-GJ-BHV-A1-2024/712_112-2_Trapaj.pdf',1840,'application/pdf',1,'Yes',2,NULL,NULL,'2024-02-02 11:30:00','2024-02-02 11:30:00',NULL),
(2,1,'Lease',1,'Registered lease deed','Lease deed LA-PGP10-001','LeaseDeed_LA-PGP10-001.pdf','documents/PGP10/PGP10-GJ-BHV-A1-2024/LeaseDeed_LA-PGP10-001.pdf',24210,'application/pdf',1,'Yes',2,NULL,NULL,'2024-03-25 15:02:00','2024-03-25 15:02:00',NULL),
(3,3,'Compliance',1,'CERSAI certificate','CERSAI registration 400012345678','CERSAI_400012345678.pdf','documents/PGP10/PGP10-GJ-BHV-B1-2024/CERSAI_400012345678.pdf',412,'application/pdf',1,'Yes',2,NULL,NULL,'2025-03-15 10:44:00','2025-03-15 10:44:00',NULL),
(4,3,'Sublease',1,'Sublease deed','Sublease deed SLA-PGP10-001','Sublease_SLA-PGP10-001_v2.pdf','documents/PGP10/PGP10-GJ-BHV-B1-2024/Sublease_SLA-PGP10-001_v2.pdf',31870,'application/pdf',2,'Yes',2,NULL,NULL,'2025-01-09 16:20:00','2025-01-09 16:20:00',NULL);
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `land_owners` WRITE;
/*!40000 ALTER TABLE `land_owners` DISABLE KEYS */;
INSERT INTO `land_owners` VALUES
(1,1,'Rameshbhai K. Patel',60.0000,'AKLPP4821M','XXXX-XXXX-4521','GJ/14/112/0388','eyJpdiI6InNXb210cDR2NzZOc1UralpUbDlqdmc9PSIsInZhbHVlIjoiY0MvcVlzS21FVmtIam1PT0thYW5PQT09IiwibWFjIjoiMzgzZGYxOTU1MjM1MzAxYjNjNDM3YzFkNWY4OWZkMDQ3NTIxZjc2MjQzMWZiMTIwYWI3N2JmMmQ5YjZiMzM3ZCIsInRhZyI6IiJ9','Bank of Baroda, Talaja',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(2,1,'Savitaben R. Patel',40.0000,'BXRPP1190D','XXXX-XXXX-7310','GJ/14/112/0389','eyJpdiI6Ii9sVlVFUGZtaWg5NFFmejZIOGJQYWc9PSIsInZhbHVlIjoiZi83QXZjL1BwMDA2aUw2ZzhlOVM4QT09IiwibWFjIjoiNGEyNmRlNzQ4NzRhNDU1MTY0YjFiZjJiZjY4ZGQ0ZDM5MTAyZDM3NDVmYTYwNzJmMjM0MzcyNjhhNjVkNGE3OCIsInRhZyI6IiJ9','Bank of Baroda, Talaja',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(3,2,'Bhikhabhai N. Rathod',100.0000,'BQRPR7710K','XXXX-XXXX-9073',NULL,NULL,NULL,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(4,3,'Mahendrasinh J. Gohil',51.0000,'CJGPG2290H','XXXX-XXXX-1186','GJ/14/205/1120','eyJpdiI6Ik9nMG8vdnI3MnIwano0Si9ENVF4WWc9PSIsInZhbHVlIjoiVTZzeGF2U3RGWXhEbTA0eVVoYkxPdz09IiwibWFjIjoiMmMyNmI0YWNlNGUyYTM3YmYxNDcwMzc0ZWFlODUxOGVmNmE5YTAwOTFiZmI0YWE0ZmMxYjg0ZWRiMzIxMDY4OSIsInRhZyI6IiJ9','SBI, Alang',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(5,3,'Pradyumansinh J. Gohil',25.0000,'CJGPG4417A','XXXX-XXXX-2290','GJ/14/205/1121','eyJpdiI6IjlSYis1U2RQekk2V2VaV1lNbHpSM0E9PSIsInZhbHVlIjoiNEFtWWRnQWhvM0tSRFh0UGdYMFFqZz09IiwibWFjIjoiMTI2N2U0OGI4ZGQwZWE3ZTg4NmFhODRmZGE2YmU3NjEyM2QyYWE4NjYxOWZjNmZmZGJkNzFhMmYzZDVjMjAzYyIsInRhZyI6IiJ9','SBI, Alang',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(6,3,'Rajendrasinh J. Gohil',24.0000,'CJGPG5528B','XXXX-XXXX-3361','GJ/14/205/1122','eyJpdiI6Ik1YenM3NWtheGNTZDhibS9sNkZLRnc9PSIsInZhbHVlIjoiRm8xUFlHUkVPQkkzUnA5VVd3ZTdmZz09IiwibWFjIjoiM2NiOTI3NjIxZWI0OGViNmM2MDM2MDdhMzRmODNmZDBiNTdlNjRiMWVjZDZiZjBkNzAyZDE1NDIyMDM4ODE1YSIsInRhZyI6IiJ9','SBI, Alang',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(7,4,'Dattatray S. Jadhav',50.0000,'AFJPJ5532Q','XXXX-XXXX-6620','MH/42/043/0071','eyJpdiI6Ild6eERiWlFjWlJ0ejkyZWlsdGorNHc9PSIsInZhbHVlIjoiS0VpK1NSalRxUFRjM09tTkdsQTFPUT09IiwibWFjIjoiZjdhMDgzMjJlMmM4MjFlMzdiZDY4MjA3NTNiZTMyZjViMTA1N2ZhMjkxNjUzNTQxNmE2MmQ1MjExOTcyZTc5ZSIsInRhZyI6IiJ9','Bank of Maharashtra, Mohol',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(8,4,'Sunita D. Jadhav',50.0000,'BHJPJ8841R','XXXX-XXXX-5507','MH/42/043/0072','eyJpdiI6Im15WHNpTk1vYmdxbzhlTHdlcHc4Ymc9PSIsInZhbHVlIjoiVFo1eG11OUR1WDBRTlRQQml0cVlYZz09IiwibWFjIjoiYjg1N2JkYzQyNGE5YWMzOGZhMzk4NTMzNjM3YWEyM2UxOWYwODBkNzhlODgwMmQ2ZTE5MmE2OTY1MzQzNjk1ZSIsInRhZyI6IiJ9','Bank of Maharashtra, Mohol',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL);
/*!40000 ALTER TABLE `land_owners` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `land_surveys` WRITE;
/*!40000 ALTER TABLE `land_surveys` DISABLE KEYS */;
INSERT INTO `land_surveys` VALUES
(1,1,1,1,1,'Trapaj','112/2',50586.00,12.50,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(2,2,1,1,1,'Trapaj','118',33184.00,8.20,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(3,3,1,1,1,'Sosiya','205/1',101171.00,25.00,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(4,3,1,1,1,'Sosiya','205/2',101171.00,25.00,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(5,3,1,1,1,'Sosiya','205/3',103829.00,25.66,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(6,3,1,1,1,'Sosiya','206',103829.00,25.66,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(7,4,2,4,7,'Penur','43/1',40468.00,10.00,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(8,4,2,4,7,'Penur','43/2',40469.00,10.00,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL);
/*!40000 ALTER TABLE `land_surveys` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `lands` WRITE;
/*!40000 ALTER TABLE `lands` DISABLE KEYS */;
INSERT INTO `lands` VALUES
(1,'PGP10-GJ-BHV-A1-2024',1,1,1,1,1,'A1',2024,50586.00,12.50,12.50,NULL,45000.00,3,5.00,'Yes','Clear','Yes','Nil',NULL,'Yes','Approved','Leased',5.000,'Talaja 220 kV','Sample Aggregator A','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(2,'PGP10-GJ-BHV-A2-2024',1,1,1,1,1,'A2',2024,33184.00,8.20,8.20,NULL,45000.00,3,5.00,'No','In progress','No','Under verification','Crop loan with district co-op bank, ₹2.4 lakh, closure letter awaited','No','Not applied','ATL signed',3.000,'Talaja 220 kV','Sample Aggregator A','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(3,'PGP10-GJ-BHV-B1-2024',1,1,1,1,1,'B1',2024,410000.00,101.31,100.00,NULL,42000.00,3,5.00,'Yes','Clear','Yes','Encumbered',NULL,'Yes','Approved','Mortgaged',200.000,'Sosiya 400 kV','Sample Aggregator B','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(4,'PGP12-MH-SLP-A1-2025',2,2,2,4,7,'A1',2025,80937.00,20.00,20.00,NULL,38000.00,3,4.00,'Yes','Clear','Yes','Nil',NULL,'Yes','Applied','Subleased',8.000,'Mohol 132 kV','Sample Aggregator C','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL);
/*!40000 ALTER TABLE `lands` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `leases` WRITE;
/*!40000 ALTER TABLE `leases` DISABLE KEYS */;
INSERT INTO `leases` VALUES
(1,1,'Rameshbhai K. Patel','AKLPP4821M',12.50,5.000,'Active','At & Post Trapaj',1,1,1,'364140','2024-01-18','2024-03-22','2024-04-01',348,'2053-03-31','UNI-GJ-24-008812','TLJ/1442/2024','LA-PGP10-001','ME-3381',45000.00,'Annual',562500.00,100000.00,5.00,3,'Yes',10.00,'No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(2,3,'Mahendrasinh J. Gohil','CJGPG2290H',100.00,200.000,'Active','Darbar Gadh, Sosiya',1,1,1,'364150','2024-07-02','2024-11-08','2024-11-20',348,'2053-11-19','UNI-GJ-24-019920','TLJ/3307/2024','LA-PGP10-002','ME-3519',42000.00,'Annual',4200000.00,500000.00,5.00,3,'Yes',10.00,'No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(3,4,'Dattatray S. Jadhav','AFJPJ5532Q',20.00,8.000,'Active','Jadhav Wasti, Penur',2,4,7,'413213','2024-10-10','2025-01-06','2025-01-10',348,'2054-01-09','UNI-MH-25-000417','MHL/88/2025','LA-PGP12-001','ME-1207',38000.00,'Annual',760000.00,0.00,4.00,3,'Yes',10.00,'No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL);
/*!40000 ALTER TABLE `leases` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_01_01_000100_create_location_tables',1),
(5,'2026_01_01_000200_create_park_spv_tables',1),
(6,'2026_01_01_000300_create_land_tables',1),
(7,'2026_01_01_000400_create_lease_tables',1),
(8,'2026_01_01_000500_create_sublease_compliance_document_audit_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `parks` WRITE;
/*!40000 ALTER TABLE `parks` DISABLE KEYS */;
INSERT INTO `parks` VALUES
(1,'BHV','Bhavnagar Solar Park',1,1,1,420.00,250.000,NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(2,'SLP','Solapur Solar Park',2,4,7,260.00,75.000,NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL);
/*!40000 ALTER TABLE `parks` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,1,1,1,'2024-04-04',337500.00,33750.00,'CHL28100',303750.00,'UTIBR2024040440112345','510004410',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(2,1,1,2,'2024-04-04',225000.00,22500.00,'CHL28101',202500.00,'UTIBR2024040440112346','510004411',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(3,30,3,4,'2024-11-23',2142000.00,214200.00,'CHL28102',1927800.00,'UTIBR2024112340112347','510004412',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(4,30,3,5,'2024-11-23',1050000.00,105000.00,'CHL28103',945000.00,'UTIBR2024112340112348','510004413',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(5,30,3,6,'2024-11-23',1008000.00,100800.00,'CHL28104',907200.00,'UTIBR2024112340112349','510004414',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(6,59,4,7,'2025-01-13',380000.00,38000.00,'CHL28105',342000.00,'UTIBR2025011340112350','510004415',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(7,59,4,8,'2025-01-13',380000.00,38000.00,'CHL28106',342000.00,'UTIBR2025011340112351','510004416',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(8,2,1,1,'2025-04-04',337500.00,33750.00,'CHL28107',303750.00,'UTIBR2025040440112352','510004417',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(9,2,1,2,'2025-04-04',225000.00,22500.00,'CHL28108',202500.00,'UTIBR2025040440112353','510004418',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(10,31,3,4,'2025-11-23',2142000.00,214200.00,'CHL28109',1927800.00,'UTIBR2025112340112354','510004419',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(11,31,3,5,'2025-11-23',1050000.00,105000.00,'CHL28110',945000.00,'UTIBR2025112340112355','510004420',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(12,31,3,6,'2025-11-23',1008000.00,100800.00,'CHL28111',907200.00,'UTIBR2025112340112356','510004421',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(13,60,4,7,'2026-01-13',380000.00,38000.00,'CHL28112',342000.00,'UTIBR2026011340112357','510004422',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(14,60,4,8,'2026-01-13',380000.00,38000.00,'CHL28113',342000.00,'UTIBR2026011340112358','510004423',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL);
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `rent_schedules` WRITE;
/*!40000 ALTER TABLE `rent_schedules` DISABLE KEYS */;
INSERT INTO `rent_schedules` VALUES
(1,1,1,'Apr 2024','2024-04-01',562500.00,56250.00,506250.00,'Paid','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(2,1,1,'Apr 2025','2025-04-01',562500.00,56250.00,506250.00,'Paid','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(3,1,1,'Apr 2026','2026-04-01',562500.00,56250.00,506250.00,'Pending','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(4,1,1,'Apr 2027','2027-04-01',590625.00,59063.00,531562.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(5,1,1,'Apr 2028','2028-04-01',590625.00,59063.00,531562.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(6,1,1,'Apr 2029','2029-04-01',590625.00,59063.00,531562.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(7,1,1,'Apr 2030','2030-04-01',620156.00,62016.00,558140.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(8,1,1,'Apr 2031','2031-04-01',620156.00,62016.00,558140.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(9,1,1,'Apr 2032','2032-04-01',620156.00,62016.00,558140.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(10,1,1,'Apr 2033','2033-04-01',651164.00,65116.00,586048.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(11,1,1,'Apr 2034','2034-04-01',651164.00,65116.00,586048.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(12,1,1,'Apr 2035','2035-04-01',651164.00,65116.00,586048.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(13,1,1,'Apr 2036','2036-04-01',683722.00,68372.00,615350.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(14,1,1,'Apr 2037','2037-04-01',683722.00,68372.00,615350.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(15,1,1,'Apr 2038','2038-04-01',683722.00,68372.00,615350.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(16,1,1,'Apr 2039','2039-04-01',717908.00,71791.00,646117.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(17,1,1,'Apr 2040','2040-04-01',717908.00,71791.00,646117.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(18,1,1,'Apr 2041','2041-04-01',717908.00,71791.00,646117.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(19,1,1,'Apr 2042','2042-04-01',753804.00,75380.00,678424.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(20,1,1,'Apr 2043','2043-04-01',753804.00,75380.00,678424.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(21,1,1,'Apr 2044','2044-04-01',753804.00,75380.00,678424.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(22,1,1,'Apr 2045','2045-04-01',791494.00,79149.00,712345.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(23,1,1,'Apr 2046','2046-04-01',791494.00,79149.00,712345.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(24,1,1,'Apr 2047','2047-04-01',791494.00,79149.00,712345.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(25,1,1,'Apr 2048','2048-04-01',831069.00,83107.00,747962.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(26,1,1,'Apr 2049','2049-04-01',831069.00,83107.00,747962.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(27,1,1,'Apr 2050','2050-04-01',831069.00,83107.00,747962.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(28,1,1,'Apr 2051','2051-04-01',872622.00,87262.00,785360.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(29,1,1,'Apr 2052','2052-04-01',872622.00,87262.00,785360.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(30,2,3,'Nov 2024','2024-11-20',4200000.00,420000.00,3780000.00,'Paid','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(31,2,3,'Nov 2025','2025-11-20',4200000.00,420000.00,3780000.00,'Paid','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(32,2,3,'Nov 2026','2026-11-20',4200000.00,420000.00,3780000.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(33,2,3,'Nov 2027','2027-11-20',4410000.00,441000.00,3969000.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(34,2,3,'Nov 2028','2028-11-20',4410000.00,441000.00,3969000.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(35,2,3,'Nov 2029','2029-11-20',4410000.00,441000.00,3969000.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(36,2,3,'Nov 2030','2030-11-20',4630500.00,463050.00,4167450.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(37,2,3,'Nov 2031','2031-11-20',4630500.00,463050.00,4167450.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(38,2,3,'Nov 2032','2032-11-20',4630500.00,463050.00,4167450.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(39,2,3,'Nov 2033','2033-11-20',4862025.00,486203.00,4375822.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(40,2,3,'Nov 2034','2034-11-20',4862025.00,486203.00,4375822.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(41,2,3,'Nov 2035','2035-11-20',4862025.00,486203.00,4375822.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(42,2,3,'Nov 2036','2036-11-20',5105126.00,510513.00,4594613.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(43,2,3,'Nov 2037','2037-11-20',5105126.00,510513.00,4594613.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(44,2,3,'Nov 2038','2038-11-20',5105126.00,510513.00,4594613.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(45,2,3,'Nov 2039','2039-11-20',5360383.00,536038.00,4824345.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(46,2,3,'Nov 2040','2040-11-20',5360383.00,536038.00,4824345.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(47,2,3,'Nov 2041','2041-11-20',5360383.00,536038.00,4824345.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(48,2,3,'Nov 2042','2042-11-20',5628402.00,562840.00,5065562.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(49,2,3,'Nov 2043','2043-11-20',5628402.00,562840.00,5065562.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(50,2,3,'Nov 2044','2044-11-20',5628402.00,562840.00,5065562.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(51,2,3,'Nov 2045','2045-11-20',5909822.00,590982.00,5318840.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(52,2,3,'Nov 2046','2046-11-20',5909822.00,590982.00,5318840.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(53,2,3,'Nov 2047','2047-11-20',5909822.00,590982.00,5318840.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(54,2,3,'Nov 2048','2048-11-20',6205313.00,620531.00,5584782.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(55,2,3,'Nov 2049','2049-11-20',6205313.00,620531.00,5584782.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(56,2,3,'Nov 2050','2050-11-20',6205313.00,620531.00,5584782.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(57,2,3,'Nov 2051','2051-11-20',6515579.00,651558.00,5864021.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(58,2,3,'Nov 2052','2052-11-20',6515579.00,651558.00,5864021.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(59,3,4,'Jan 2025','2025-01-10',760000.00,76000.00,684000.00,'Paid','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(60,3,4,'Jan 2026','2026-01-10',760000.00,76000.00,684000.00,'Paid','Yes',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(61,3,4,'Jan 2027','2027-01-10',760000.00,76000.00,684000.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(62,3,4,'Jan 2028','2028-01-10',790400.00,79040.00,711360.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(63,3,4,'Jan 2029','2029-01-10',790400.00,79040.00,711360.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(64,3,4,'Jan 2030','2030-01-10',790400.00,79040.00,711360.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(65,3,4,'Jan 2031','2031-01-10',822016.00,82202.00,739814.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(66,3,4,'Jan 2032','2032-01-10',822016.00,82202.00,739814.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(67,3,4,'Jan 2033','2033-01-10',822016.00,82202.00,739814.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(68,3,4,'Jan 2034','2034-01-10',854897.00,85490.00,769407.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(69,3,4,'Jan 2035','2035-01-10',854897.00,85490.00,769407.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(70,3,4,'Jan 2036','2036-01-10',854897.00,85490.00,769407.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(71,3,4,'Jan 2037','2037-01-10',889093.00,88909.00,800184.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(72,3,4,'Jan 2038','2038-01-10',889093.00,88909.00,800184.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(73,3,4,'Jan 2039','2039-01-10',889093.00,88909.00,800184.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(74,3,4,'Jan 2040','2040-01-10',924656.00,92466.00,832190.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(75,3,4,'Jan 2041','2041-01-10',924656.00,92466.00,832190.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(76,3,4,'Jan 2042','2042-01-10',924656.00,92466.00,832190.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(77,3,4,'Jan 2043','2043-01-10',961642.00,96164.00,865478.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(78,3,4,'Jan 2044','2044-01-10',961642.00,96164.00,865478.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(79,3,4,'Jan 2045','2045-01-10',961642.00,96164.00,865478.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(80,3,4,'Jan 2046','2046-01-10',1000108.00,100011.00,900097.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(81,3,4,'Jan 2047','2047-01-10',1000108.00,100011.00,900097.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(82,3,4,'Jan 2048','2048-01-10',1000108.00,100011.00,900097.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(83,3,4,'Jan 2049','2049-01-10',1040112.00,104011.00,936101.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(84,3,4,'Jan 2050','2050-01-10',1040112.00,104011.00,936101.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(85,3,4,'Jan 2051','2051-01-10',1040112.00,104011.00,936101.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(86,3,4,'Jan 2052','2052-01-10',1081717.00,108172.00,973545.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(87,3,4,'Jan 2053','2053-01-10',1081717.00,108172.00,973545.00,'Pending','No',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50');
/*!40000 ALTER TABLE `rent_schedules` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES
(1,1,'state',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(2,1,'district',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(3,1,'city',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(4,1,'park',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(5,1,'spv',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(6,1,'land',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(7,1,'survey',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(8,1,'farmer',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(9,1,'lease',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(10,1,'schedule',1,0,1,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(11,1,'payment',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(12,1,'sublease',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(13,1,'compliance',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(14,1,'doc',1,1,0,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(15,1,'report',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(16,1,'audit',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(17,2,'state',1,1,1,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(18,2,'district',1,1,1,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(19,2,'city',1,1,1,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(20,2,'park',1,1,1,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(21,2,'spv',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(22,2,'land',1,1,1,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(23,2,'survey',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(24,2,'farmer',1,1,1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(25,2,'lease',1,1,1,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(26,2,'schedule',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(27,2,'payment',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(28,2,'sublease',1,1,1,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(29,2,'compliance',1,1,1,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(30,2,'doc',1,1,0,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(31,2,'report',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(32,2,'audit',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(33,3,'state',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(34,3,'district',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(35,3,'city',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(36,3,'park',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(37,3,'spv',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(38,3,'land',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(39,3,'survey',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(40,3,'farmer',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(41,3,'lease',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(42,3,'schedule',1,0,1,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(43,3,'payment',1,1,1,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(44,3,'sublease',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(45,3,'compliance',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(46,3,'doc',1,1,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(47,3,'report',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(48,3,'audit',1,0,0,0,0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(49,4,'state',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(50,4,'district',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(51,4,'city',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(52,4,'park',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(53,4,'spv',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(54,4,'land',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(55,4,'survey',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(56,4,'farmer',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(57,4,'lease',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(58,4,'schedule',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(59,4,'payment',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(60,4,'sublease',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(61,4,'compliance',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(62,4,'doc',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(63,4,'report',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(64,4,'audit',1,0,0,0,1,'2026-11-19 14:00:49','2026-11-19 14:00:49');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'Super Admin','Full access. Manages users, roles and SPV access.',1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(2,'Land Manager','Creates and maintains land, lease, sublease, compliance, documents and location masters.',0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(3,'Finance','Maintains rent schedule and records payments.',0,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(4,'Auditor','Read-only access to everything, with export.',0,'2026-11-19 14:00:49','2026-11-19 14:00:49');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `spvs` WRITE;
/*!40000 ALTER TABLE `spvs` DISABLE KEYS */;
INSERT INTO `spvs` VALUES
(1,1,1,'PGP10','Sample SPV Ten Pvt Ltd','Solar PV',150.000,'Under construction',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(2,2,2,'PGP12','Sample SPV Twelve Pvt Ltd','Solar PV',75.000,'Development',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(3,1,1,'PGP11','Sample SPV Eleven Pvt Ltd','Hybrid',100.000,'Development',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL);
/*!40000 ALTER TABLE `spvs` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES
(1,'Gujarat','GJ',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(2,'Maharashtra','MH',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(3,'Rajasthan','RJ',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(4,'Karnataka','KA',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(5,'Madhya Pradesh','MP',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(6,'Andhra Pradesh','AP',NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL);
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `subleases` WRITE;
/*!40000 ALTER TABLE `subleases` DISABLE KEYS */;
INSERT INTO `subleases` VALUES
(1,2,3,'Sample EPC Co. Pvt Ltd','AAKCS7712P',60.00,100.000,2700000.00,'2025-01-01','2053-11-19','SLA-PGP10-001','Yes','Active',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(2,3,4,'Sample Grid Services LLP','AAPFS3391L',20.00,8.000,800000.00,'2025-03-01','2054-01-09','SLA-PGP12-001','No','Active',NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL);
/*!40000 ALTER TABLE `subleases` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `user_spv_access` WRITE;
/*!40000 ALTER TABLE `user_spv_access` DISABLE KEYS */;
INSERT INTO `user_spv_access` VALUES
(1,1,1,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(2,1,2,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(3,1,3,'2026-11-19 14:00:49','2026-11-19 14:00:49'),
(4,2,1,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(5,2,2,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(6,2,3,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(7,3,1,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(8,4,1,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(9,4,2,'2026-11-19 14:00:50','2026-11-19 14:00:50'),
(10,4,3,'2026-11-19 14:00:50','2026-11-19 14:00:50');
/*!40000 ALTER TABLE `user_spv_access` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Nikhil Shah','superadmin','nikhil.shah@example.com',NULL,'$2y$12$23A8XsCCHehNy39zBXlGbe236/Pe5HZWOcVOITfwjz9uzv.PBi2OS',1,1,0,0,'2026-10-16 09:12:00','10.20.4.11',NULL,NULL,NULL,'2026-11-19 14:00:49','2026-11-19 14:00:49',NULL),
(2,'Priya Desai','priya.land','priya.desai@example.com',NULL,'$2y$12$c/NGsz.pgU9Jj.IBpaK3le/Bipl71aWFL2YCQFlaZYZbU3x79ehgW',2,1,0,0,'2026-10-15 17:40:00','10.20.4.27',NULL,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(3,'Kunal Mehta','kunal.fin','kunal.mehta@example.com',NULL,'$2y$12$RFRShynaCKeJwUnzR/ZWmemR57YnyfkJCitd8xFghVSKXf2Xvy/Xm',3,1,0,0,'2026-10-16 10:05:00','10.20.6.42',NULL,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL),
(4,'Anita Rao','anita.audit','anita.rao@example.com',NULL,'$2y$12$oGIL.SWVdT2zFNg5jzB8GOa4RZrxpPmF4ryKmaPsaekIzl23mMxMW',4,1,0,0,'2026-10-09 11:20:00','10.20.8.5',NULL,NULL,NULL,'2026-11-19 14:00:50','2026-11-19 14:00:50',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

