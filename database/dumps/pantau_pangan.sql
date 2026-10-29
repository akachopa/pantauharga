/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.7.2-MariaDB, for osx10.20 (arm64)
--
-- Host: 127.0.0.1    Database: pantau_pangan
-- ------------------------------------------------------
-- Server version	11.7.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `access_change_logs`
--

DROP TABLE IF EXISTS `access_change_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `access_change_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `subject_user_id` bigint(20) unsigned NOT NULL,
  `change_type` varchar(60) NOT NULL,
  `before_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_json`)),
  `after_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`after_json`)),
  `reason` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  KEY `subject_user_id` (`subject_user_id`),
  CONSTRAINT `access_change_logs_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`),
  CONSTRAINT `access_change_logs_ibfk_2` FOREIGN KEY (`subject_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_change_logs`
--

LOCK TABLES `access_change_logs` WRITE;
/*!40000 ALTER TABLE `access_change_logs` DISABLE KEYS */;
INSERT INTO `access_change_logs` VALUES
(1,NULL,6,'v2_scope_migration',NULL,'{\"source\": \"point_assignments and internal template\", \"review_required\": true}','Migrasi oleh DBA; review sebelum aplikasi dibuka kembali','2026-10-28 20:57:56'),
(2,NULL,1,'v2_scope_migration',NULL,'{\"source\": \"point_assignments and internal template\", \"review_required\": true}','Migrasi oleh DBA; review sebelum aplikasi dibuka kembali','2026-10-28 20:57:56'),
(3,NULL,2,'v2_scope_migration',NULL,'{\"source\": \"point_assignments and internal template\", \"review_required\": true}','Migrasi oleh DBA; review sebelum aplikasi dibuka kembali','2026-10-28 20:57:56'),
(4,NULL,3,'v2_scope_migration',NULL,'{\"source\": \"point_assignments and internal template\", \"review_required\": true}','Migrasi oleh DBA; review sebelum aplikasi dibuka kembali','2026-10-28 20:57:56'),
(5,NULL,4,'v2_scope_migration',NULL,'{\"source\": \"point_assignments and internal template\", \"review_required\": true}','Migrasi oleh DBA; review sebelum aplikasi dibuka kembali','2026-10-28 20:57:56'),
(6,NULL,5,'v2_scope_migration',NULL,'{\"source\": \"point_assignments and internal template\", \"review_required\": true}','Migrasi oleh DBA; review sebelum aplikasi dibuka kembali','2026-10-28 20:57:56');
/*!40000 ALTER TABLE `access_change_logs` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bu_access_change_logs BEFORE UPDATE ON access_change_logs FOR EACH ROW
BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Access audit is append-only'; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bd_access_change_logs BEFORE DELETE ON access_change_logs FOR EACH ROW
BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Access audit is append-only'; END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `action_plans`
--

DROP TABLE IF EXISTS `action_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('tinggi','sedang','rendah') NOT NULL DEFAULT 'sedang',
  `status` enum('antrean','dalam_proses','selesai','terlambat') NOT NULL DEFAULT 'antrean',
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `progress_pct` int(11) NOT NULL DEFAULT 0,
  `target_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `evidence_path` varchar(255) DEFAULT NULL,
  `is_demo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `policy_case_id` bigint(20) unsigned DEFAULT NULL,
  `target_point_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `fk_actions_policy_case` (`policy_case_id`),
  KEY `fk_actions_target_point` (`target_point_id`),
  CONSTRAINT `action_plans_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_actions_policy_case` FOREIGN KEY (`policy_case_id`) REFERENCES `policy_cases` (`id`),
  CONSTRAINT `fk_actions_target_point` FOREIGN KEY (`target_point_id`) REFERENCES `monitoring_points` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_plans`
--

LOCK TABLES `action_plans` WRITE;
/*!40000 ALTER TABLE `action_plans` DISABLE KEYS */;
INSERT INTO `action_plans` VALUES
(1,'Operasi Pasar Murah Beras SPHP di Karang Tinggi','Koordinasi dengan Bulog Kanwil Bengkulu untuk intervensi harga beras medium.','tinggi','antrean',2,'2026-10-31',20,NULL,NULL,NULL,1,'2026-10-28 20:57:56',NULL,NULL),
(2,'Penyaluran Stok Minyak Goreng Curah Talang Empat','Fasilitasi pasokan langsung dari distributor ke pedagang pasar rakyat.','sedang','dalam_proses',2,'2026-10-30',50,NULL,NULL,NULL,1,'2026-10-28 20:57:56',NULL,NULL),
(3,'Inspeksi Ketersediaan Pasokan Cabai Merah Taba Penanjung','Cek rantai distribusi pasokan cabai dari petani Rejang Lebong dan Kepahiang.','tinggi','dalam_proses',3,'2026-10-29',70,NULL,NULL,NULL,1,'2026-10-28 20:57:56',NULL,NULL),
(4,'Verifikasi Akurasi Timbangan dan Harga Telur Ayam Pondok Kelapa','Pemeriksaan rutin berkala kesesuaian harga dan tera timbangan.','rendah','selesai',4,'2026-10-25',100,NULL,NULL,NULL,1,'2026-10-28 20:57:56',NULL,NULL),
(5,'Evaluasi Keterlambatan Laporan Pangan Kecamatan Semidang Lagan','Pemberian pendampingan teknis pelaporan input harga kepada petugas kecamatan.','sedang','terlambat',1,'2026-10-24',35,NULL,NULL,NULL,1,'2026-10-28 20:57:56',NULL,NULL),
(6,'Distribusi Sembako Bersubsidi','Penyaluran beras dan gula murah','tinggi','dalam_proses',NULL,NULL,40,NULL,NULL,NULL,1,'2026-10-28 21:27:40',NULL,NULL),
(7,'Distribusi Sembako Bersubsidi','Penyaluran beras dan gula murah','tinggi','dalam_proses',NULL,NULL,40,NULL,NULL,NULL,1,'2026-10-28 22:22:28',NULL,NULL),
(8,'Distribusi Sembako Bersubsidi','Penyaluran beras dan gula murah','tinggi','dalam_proses',NULL,NULL,40,NULL,NULL,NULL,1,'2026-10-28 22:22:50',NULL,NULL),
(9,'Distribusi Sembako Bersubsidi','Penyaluran beras dan gula murah','tinggi','dalam_proses',NULL,NULL,40,NULL,NULL,NULL,1,'2026-10-28 22:36:38',NULL,NULL),
(10,'Distribusi Sembako Bersubsidi','Penyaluran beras dan gula murah','tinggi','dalam_proses',NULL,NULL,40,NULL,NULL,NULL,0,'2026-10-28 23:37:28',NULL,NULL),
(11,'Distribusi Sembako Bersubsidi','Penyaluran beras dan gula murah','tinggi','dalam_proses',NULL,NULL,40,NULL,NULL,NULL,0,'2026-10-28 23:37:39',NULL,NULL),
(12,'Distribusi Sembako Bersubsidi','Penyaluran beras dan gula murah','tinggi','dalam_proses',NULL,NULL,40,NULL,NULL,NULL,0,'2026-10-28 23:56:57',NULL,NULL),
(13,'Distribusi Sembako Bersubsidi','Penyaluran beras dan gula murah','tinggi','dalam_proses',NULL,NULL,40,NULL,NULL,NULL,0,'2026-10-29 00:19:57',NULL,NULL);
/*!40000 ALTER TABLE `action_plans` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bi_policy_action BEFORE INSERT ON action_plans FOR EACH ROW
BEGIN
 IF NEW.policy_case_id IS NOT NULL AND NOT EXISTS(
 SELECT 1 FROM policy_cases c JOIN policy_evaluation_results e ON e.id=c.result_id
 WHERE c.id=NEW.policy_case_id AND c.status IN ('approved','in_progress') AND e.is_demo=NEW.is_demo
 AND e.point_id=NEW.target_point_id) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Policy action requires approved case and matching target'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bu_policy_action BEFORE UPDATE ON action_plans FOR EACH ROW
BEGIN
 IF NOT (NEW.policy_case_id<=>OLD.policy_case_id) OR NOT (NEW.target_point_id<=>OLD.target_point_id) OR NEW.is_demo<>OLD.is_demo THEN
 IF OLD.policy_case_id IS NOT NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Policy action linkage immutable'; END IF;
 IF NEW.policy_case_id IS NOT NULL AND NOT EXISTS(
 SELECT 1 FROM policy_cases c JOIN policy_evaluation_results e ON e.id=c.result_id
 WHERE c.id=NEW.policy_case_id AND c.status IN ('approved','in_progress') AND e.is_demo=NEW.is_demo AND e.point_id=NEW.target_point_id) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Approved matching case required'; END IF;
 END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `app_menus`
--

DROP TABLE IF EXISTS `app_menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_menus` (
  `id` bigint(20) unsigned NOT NULL,
  `code` varchar(60) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_menus`
--

LOCK TABLES `app_menus` WRITE;
/*!40000 ALTER TABLE `app_menus` DISABLE KEYS */;
INSERT INTO `app_menus` VALUES
(1,'dashboard','Dashboard',1),
(2,'prices','Input Harga dan Tabulasi',2),
(3,'master','Master Data',3),
(4,'policies','Paket Kebijakan',4),
(5,'actions','Tindak Lanjut',5),
(6,'reports','Laporan',6),
(7,'settings','Pengaturan',7),
(8,'radar','Radar',8),
(9,'supply','Rantai Pasok',9);
/*!40000 ALTER TABLE `app_menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `actor_id` bigint(20) unsigned NOT NULL,
  `entity_type` varchar(60) NOT NULL,
  `entity_id` bigint(20) unsigned NOT NULL,
  `action` varchar(60) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES
(1,1,'community_price_report',10,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Mohon perbaiki data.\"}','2026-10-28 23:37:21'),
(2,1,'community_price_report',11,'submitted',NULL,'{\"status\":\"pending\",\"reported_price\":876543}','2026-10-28 23:37:27'),
(3,1,'community_price_report',13,'review_reviewed','{\"status\":\"pending\"}','{\"status\":\"reviewed\",\"review_note\":null}','2026-10-28 23:37:28'),
(4,1,'community_price_report',14,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Mohon perbaiki data.\"}','2026-10-28 23:37:39'),
(5,1,'community_price_report',15,'submitted',NULL,'{\"status\":\"pending\",\"reported_price\":876543}','2026-10-28 23:37:39'),
(6,1,'community_price_report',17,'review_reviewed','{\"status\":\"pending\"}','{\"status\":\"reviewed\",\"review_note\":null}','2026-10-28 23:37:39'),
(7,1,'community_price_report',18,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Perbaiki harga.\"}','2026-10-28 23:48:09'),
(8,1,'community_price_report',19,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Perbaiki harga.\"}','2026-10-28 23:49:36'),
(9,1,'community_price_report',20,'submitted',NULL,'{\"status\":\"pending\",\"reported_price\":876543}','2026-10-28 23:49:36'),
(10,1,'community_price_report',22,'review_reviewed','{\"status\":\"pending\"}','{\"status\":\"reviewed\",\"review_note\":null}','2026-10-28 23:49:36'),
(11,1,'community_price_report',23,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Perbaiki harga.\"}','2026-10-28 23:52:15'),
(12,1,'community_price_report',24,'submitted',NULL,'{\"status\":\"pending\",\"reported_price\":876543}','2026-10-28 23:52:16'),
(13,1,'community_price_report',26,'review_reviewed','{\"status\":\"pending\"}','{\"status\":\"reviewed\",\"review_note\":null}','2026-10-28 23:52:16'),
(14,1,'community_price_report',27,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Perbaiki harga.\"}','2026-10-28 23:55:14'),
(15,1,'community_price_report',28,'submitted',NULL,'{\"status\":\"pending\",\"reported_price\":876543}','2026-10-28 23:55:14'),
(16,1,'community_price_report',30,'review_reviewed','{\"status\":\"pending\"}','{\"status\":\"reviewed\",\"review_note\":null}','2026-10-28 23:55:14'),
(17,1,'community_price_report',31,'submitted',NULL,'{\"status\":\"pending\",\"reported_price\":876543}','2026-10-28 23:56:56'),
(18,1,'community_price_report',33,'review_reviewed','{\"status\":\"pending\"}','{\"status\":\"reviewed\",\"review_note\":null}','2026-10-28 23:56:56'),
(19,1,'community_price_report',34,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Mohon perbaiki data.\"}','2026-10-28 23:56:56'),
(20,1,'community_price_report',35,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Perbaiki harga.\"}','2026-10-28 23:56:57'),
(21,1,'community_price_report',36,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Perbaiki harga.\"}','2026-10-29 00:18:50'),
(22,1,'community_price_report',37,'submitted',NULL,'{\"status\":\"pending\",\"reported_price\":876543}','2026-10-29 00:18:50'),
(23,1,'community_price_report',39,'review_reviewed','{\"status\":\"pending\"}','{\"status\":\"reviewed\",\"review_note\":null}','2026-10-29 00:18:50'),
(24,1,'community_price_report',40,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Perbaiki harga.\"}','2026-10-29 00:19:57'),
(25,1,'community_price_report',41,'submitted',NULL,'{\"status\":\"pending\",\"reported_price\":876543}','2026-10-29 00:19:57'),
(26,1,'community_price_report',43,'review_reviewed','{\"status\":\"pending\"}','{\"status\":\"reviewed\",\"review_note\":null}','2026-10-29 00:19:57'),
(27,1,'community_price_report',44,'submitted',NULL,'{\"status\":\"pending\",\"reported_price\":876543}','2026-10-29 00:26:30'),
(28,1,'community_price_report',46,'review_reviewed','{\"status\":\"pending\"}','{\"status\":\"reviewed\",\"review_note\":null}','2026-10-29 00:26:30'),
(29,1,'community_price_report',47,'review_returned','{\"status\":\"pending\"}','{\"status\":\"returned\",\"review_note\":\"Perbaiki harga.\"}','2026-10-29 00:26:30');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `businesses`
--

DROP TABLE IF EXISTS `businesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `businesses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `owner_name` varchar(150) NOT NULL,
  `business_type` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `region_id` (`region_id`),
  CONSTRAINT `businesses_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `businesses`
--

LOCK TABLES `businesses` WRITE;
/*!40000 ALTER TABLE `businesses` DISABLE KEYS */;
INSERT INTO `businesses` VALUES
(1,2,'BIZ-01','UD Sembako Berkah Utama','H. Ahmad Fauzi','Grosir dan Eceran Pangan','081399887766','Pasar Karang Tinggi Kios No. 12',1,'2026-10-28 20:57:56'),
(2,5,'BIZ-02','Toko Sumber Rezeki Taba Penanjung','Hj. Rohana','Kelontong dan Bahan Pokok','081277665544','Jl. Pasar Taba Penanjung No. 4',1,'2026-10-28 20:57:56'),
(3,3,'BIZ-03','Distributor Minyak Curah Talang Empat','Budi Santoso','Agen / Distributor','081188223344','Kembang Seri, Talang Empat',1,'2026-10-28 20:57:56'),
(4,4,'BIZ-04','Kios Cabai & Sayur Segar Bu Lina','Lina Marlina','Pedagang Sayur & Cabai','085266778899','Pasar Pondok Kelapa Blok C',1,'2026-10-28 20:57:56'),
(5,2,'BIZ-05','Agen Telur Ayam Sejahtera Sentosa','Hendra Gunawan','Agen Petelur & Distribusi','081344556677','Karang Tinggi',1,'2026-10-28 20:57:56');
/*!40000 ALTER TABLE `businesses` ENABLE KEYS */;
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
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('pantau-pangan-cache-9ce337473a123aea628ad56b6e9bebf4','i:5;',1793230889),
('pantau-pangan-cache-9ce337473a123aea628ad56b6e9bebf4:timer','i:1793230889;',1793230889);
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
  `expiration` bigint(20) NOT NULL,
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
-- Table structure for table `commodities`
--

DROP TABLE IF EXISTS `commodities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `commodities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `base_unit` varchar(20) NOT NULL DEFAULT 'kg',
  `icon` varchar(20) NOT NULL DEFAULT '?',
  `heir_category` varchar(100) DEFAULT NULL,
  `reference_price` decimal(16,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commodities`
--

LOCK TABLES `commodities` WRITE;
/*!40000 ALTER TABLE `commodities` DISABLE KEYS */;
INSERT INTO `commodities` VALUES
(1,'BERAS_MEDIUM','Beras Medium','Pangan Pokok','kg','🍚',NULL,14500.00,1,'2026-10-28 20:57:56'),
(2,'BERAS_PREMIUM','Beras Premium','Pangan Pokok','kg','🌾',NULL,16000.00,1,'2026-10-28 20:57:56'),
(3,'GULA_PASIR','Gula Pasir','Sembako','kg','🧂',NULL,18000.00,1,'2026-10-28 20:57:56'),
(4,'MINYAK_GORENG','Minyak Goreng Curah','Minyak Curah','liter','🍾',NULL,18500.00,1,'2026-10-28 20:57:56'),
(5,'CABAI_MERAH','Cabai Merah Keriting','Hortikultura','kg','🌶️',NULL,48000.00,1,'2026-10-28 20:57:56'),
(6,'CABAI_RAWIT','Cabai Rawit Hijau','Hortikultura','kg','🌶️',NULL,42000.00,1,'2026-10-28 20:57:56'),
(7,'TELUR_AYAM','Telur Ayam Ras','Peternakan','kg','🥚',NULL,28000.00,1,'2026-10-28 20:57:56'),
(8,'BAWANG_MERAH','Bawang Merah Lokal','Hortikultura','kg','🧅',NULL,36000.00,1,'2026-10-28 20:57:56'),
(9,'BAWANG_PUTIH','Bawang Putih Honan','Hortikultura','kg','🧄',NULL,38000.00,1,'2026-10-28 20:57:56'),
(10,'DAGING_SAPI','Daging Sapi Murni','Peternakan','kg','🥩',NULL,135000.00,1,'2026-10-28 20:57:56'),
(11,'DAGING_AYAM','Daging Ayam Broiler','Peternakan','kg','🍗',NULL,34000.00,1,'2026-10-28 20:57:56'),
(12,'TEPUNG_TERIGU','Tepung Terigu Segitiga','Pangan Olahan','kg','📦',NULL,12500.00,1,'2026-10-28 20:57:56'),
(15,'TBS_SAWIT','TBS Kelapa Sawit','Perkebunan','kg','🌴',NULL,NULL,1,'2026-10-28 23:56:34');
/*!40000 ALTER TABLE `commodities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_price_reports`
--

DROP TABLE IF EXISTS `community_price_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `community_price_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `commodity_id` bigint(20) unsigned NOT NULL,
  `market_id` bigint(20) unsigned NOT NULL,
  `reported_price` decimal(16,2) NOT NULL,
  `official_price` decimal(16,2) DEFAULT NULL,
  `reporter_name` varchar(120) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `note` varchar(500) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `review_note` varchar(500) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `community_price_reports_market_id_foreign` (`market_id`),
  KEY `community_price_reports_commodity_id_status_index` (`commodity_id`,`status`),
  KEY `community_price_reports_created_at_index` (`created_at`),
  CONSTRAINT `community_price_reports_commodity_id_foreign` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`),
  CONSTRAINT `community_price_reports_market_id_foreign` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_price_reports`
--

LOCK TABLES `community_price_reports` WRITE;
/*!40000 ALTER TABLE `community_price_reports` DISABLE KEYS */;
INSERT INTO `community_price_reports` VALUES
(10,8,1,18500.00,18000.00,'Warga Gap Returned',NULL,'Harga di kios berbeda dari sistem.','returned','Mohon perbaiki data.','2026-10-28 16:37:21','2026-10-28 16:37:21'),
(14,8,1,18500.00,18000.00,'Warga Gap Returned',NULL,'Harga di kios berbeda dari sistem.','returned','Mohon perbaiki data.','2026-10-28 16:37:39','2026-10-28 16:37:39'),
(34,8,1,18500.00,18000.00,'Warga Gap Returned',NULL,'Harga di kios berbeda dari sistem.','returned','Mohon perbaiki data.','2026-10-28 16:56:56','2026-10-28 16:56:56');
/*!40000 ALTER TABLE `community_price_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `data_sources`
--

DROP TABLE IF EXISTS `data_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_sources` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `source_type` enum('petugas_pasar','mitra','instansi','simulation') NOT NULL DEFAULT 'petugas_pasar',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_sources`
--

LOCK TABLES `data_sources` WRITE;
/*!40000 ALTER TABLE `data_sources` DISABLE KEYS */;
INSERT INTO `data_sources` VALUES
(1,'SRC_PETUGAS','Petugas Pengawas Pasar Disperindag','petugas_pasar','2026-10-28 20:57:56'),
(2,'SRC_MITRA','Pelaku Usaha / Mitra Pasar','mitra','2026-10-28 20:57:56'),
(3,'SRC_DKP','Dinas Ketahanan Pangan Bengkulu Tengah','instansi','2026-10-28 20:57:56'),
(4,'SRC_SIMULASI','Sistem Evaluasi Simulasi Kebijakan','simulation','2026-10-28 20:57:56'),
(5,'SRC_SP2KP','SP2KP Kemendag (Provinsi Bengkulu)','instansi','2026-10-28 23:24:54');
/*!40000 ALTER TABLE `data_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `markets`
--

DROP TABLE IF EXISTS `markets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `markets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` bigint(20) unsigned NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` text DEFAULT NULL,
  `market_days` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `region_id` (`region_id`),
  CONSTRAINT `markets_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `markets`
--

LOCK TABLES `markets` WRITE;
/*!40000 ALTER TABLE `markets` DISABLE KEYS */;
INSERT INTO `markets` VALUES
(1,2,'MKT-KT-01','Pasar Karang Tinggi','Jl. Lintas Bengkulu-Kepahiang Km. 28, Karang Tinggi','Senin, Kamis',-3.73245000,102.43120000,1,'2026-10-28 20:57:56'),
(2,5,'MKT-TP-01','Pasar Taba Penanjung','Jl. Raya Taba Penanjung, Bengkulu Tengah','Selasa, Sabtu',-3.69820000,102.47890000,1,'2026-10-28 20:57:56'),
(3,3,'MKT-TE-01','Pasar Talang Empat','Kembang Seri, Talang Empat','Rabu, Minggu',-3.78450000,102.39210000,1,'2026-10-28 20:57:56'),
(4,4,'MKT-PK-01','Pasar Pondok Kelapa','Jl. Lintas Barat Sumatera, Pondok Kelapa','Setiap Hari',-3.71230000,102.28900000,1,'2026-10-28 20:57:56'),
(5,2,'MKT-SIM-A','Pasar Simulasi A','Kawasan Sentra Pangan Simulasi Renah Semanek','Setiap Hari',-3.73500000,102.43500000,1,'2026-10-28 20:57:56'),
(6,3,'MKT-SIM-B','Pasar Simulasi B','Kawasan Distribusi Simulasi Ujung Karang','Setiap Hari',-3.78000000,102.39900000,1,'2026-10-28 20:57:56'),
(7,8,'MKT-PASA-3386','Pasar Uji Coba Bang Haji','Bang Haji, Bengkulu Tengah','Rabu',NULL,NULL,1,'2026-10-28 21:27:40'),
(8,8,'MKT-PASA-7121','Pasar Uji Coba Bang Haji','Bang Haji, Bengkulu Tengah','Rabu',NULL,NULL,1,'2026-10-28 22:22:28'),
(9,8,'MKT-PASA-3304','Pasar Uji Coba Bang Haji','Bang Haji, Bengkulu Tengah','Rabu',NULL,NULL,1,'2026-10-28 22:22:50'),
(10,8,'MKT-PASA-596','Pasar Uji Coba Bang Haji','Bang Haji, Bengkulu Tengah','Rabu',NULL,NULL,1,'2026-10-28 22:44:24'),
(11,8,'MKT-PASA-6015','Pasar Uji Coba Bang Haji','Bang Haji, Bengkulu Tengah','Rabu',NULL,NULL,1,'2026-10-29 06:37:28'),
(12,8,'MKT-PASA-9249','Pasar Uji Coba Bang Haji','Bang Haji, Bengkulu Tengah','Rabu',NULL,NULL,1,'2026-10-29 06:37:39'),
(13,8,'MKT-PASA-8024','Pasar Uji Coba Bang Haji','Bang Haji, Bengkulu Tengah','Rabu',NULL,NULL,1,'2026-10-29 06:56:57'),
(14,8,'MKT-PASA-923','Pasar Uji Coba Bang Haji','Bang Haji, Bengkulu Tengah','Rabu',NULL,NULL,1,'2026-10-29 07:19:57');
/*!40000 ALTER TABLE `markets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_permissions`
--

DROP TABLE IF EXISTS `menu_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_permissions` (
  `menu_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`menu_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `menu_permissions_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `app_menus` (`id`),
  CONSTRAINT `menu_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_permissions`
--

LOCK TABLES `menu_permissions` WRITE;
/*!40000 ALTER TABLE `menu_permissions` DISABLE KEYS */;
INSERT INTO `menu_permissions` VALUES
(2,1),
(2,2),
(2,3),
(3,4),
(3,5),
(9,5),
(1,6),
(8,6),
(6,7),
(5,8),
(5,9),
(4,10),
(1,11),
(2,12),
(2,13),
(2,14),
(3,15),
(3,16),
(4,17),
(4,18),
(4,19),
(4,20),
(5,21),
(6,22),
(7,23),
(7,24);
/*!40000 ALTER TABLE `menu_permissions` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2026_10_28_161950_create_community_price_reports_table',1),
(2,'0001_01_01_000001_create_cache_table',2),
(3,'2026_10_28_232952_add_assignment_and_evidence_columns',3),
(4,'2026_10_28_234644_create_supply_chain_tables',4),
(5,'2026_10_28_234645_seed_radar_and_supply_menus',5),
(6,'2026_10_28_235615_add_tbs_sawit_commodity',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monitoring_points`
--

DROP TABLE IF EXISTS `monitoring_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `monitoring_points` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `market_id` bigint(20) unsigned DEFAULT NULL,
  `region_id` bigint(20) unsigned NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `point_type` enum('pasar_tradisional','toko_kelontong','agen_distributor') NOT NULL DEFAULT 'pasar_tradisional',
  `is_demo` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `market_id` (`market_id`),
  KEY `region_id` (`region_id`),
  CONSTRAINT `monitoring_points_ibfk_1` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `monitoring_points_ibfk_2` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monitoring_points`
--

LOCK TABLES `monitoring_points` WRITE;
/*!40000 ALTER TABLE `monitoring_points` DISABLE KEYS */;
INSERT INTO `monitoring_points` VALUES
(1,1,2,'PT-01','Pasar Karang Tinggi (Titik Utama)','pasar_tradisional',0,1,'2026-10-28 20:57:56'),
(2,1,2,'PT-02','Kios Sembako Bu Siti - Pasar Karang Tinggi','toko_kelontong',0,1,'2026-10-28 20:57:56'),
(3,2,5,'PT-03','Pasar Taba Penanjung (Titik Utama)','pasar_tradisional',0,1,'2026-10-28 20:57:56'),
(4,2,5,'PT-04','Toko Berkah Mandiri - Taba Penanjung','toko_kelontong',0,1,'2026-10-28 20:57:56'),
(5,3,3,'PT-05','Pasar Talang Empat (Titik Utama)','pasar_tradisional',0,1,'2026-10-28 20:57:56'),
(6,4,4,'PT-06','Pasar Pondok Kelapa (Titik Utama)','pasar_tradisional',0,1,'2026-10-28 20:57:56'),
(7,5,2,'PT-07','Pasar Simulasi A (Titik Pantau Harian)','pasar_tradisional',1,1,'2026-10-28 20:57:56'),
(8,6,3,'PT-08','Pasar Simulasi B (Titik Pantau Harian)','pasar_tradisional',1,1,'2026-10-28 20:57:56'),
(9,NULL,2,'PT-09','Gudang Distributor Pangan Karang Tinggi','agen_distributor',0,1,'2026-10-28 20:57:56'),
(10,NULL,4,'PT-10','Agen Minyak Goreng & Sembako Pondok Kelapa','agen_distributor',0,1,'2026-10-28 20:57:56'),
(11,NULL,6,'PT-11','Sentra Pengumpul Hasil Bumi Pondok Kubang','agen_distributor',0,1,'2026-10-28 20:57:56');
/*!40000 ALTER TABLE `monitoring_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` bigint(20) unsigned NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `region_id` (`region_id`),
  CONSTRAINT `organizations_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` VALUES
(1,1,'DISPERINDAG','Dinas Perdagangan, Perindustrian dan Koperasi Kab. Bengkulu Tengah','Komplek Perkantoran Renah Semanek, Karang Tinggi','2026-10-28 20:57:56'),
(2,1,'DKP','Dinas Ketahanan Pangan dan Perikanan Bengkulu Tengah','Karang Tinggi, Bengkulu Tengah','2026-10-28 20:57:56');
/*!40000 ALTER TABLE `organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partner_accounts`
--

DROP TABLE IF EXISTS `partner_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `partner_accounts` (
  `user_id` bigint(20) unsigned NOT NULL,
  `business_id` bigint(20) unsigned NOT NULL,
  `linked_by` bigint(20) unsigned DEFAULT NULL,
  `linked_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`business_id`),
  KEY `business_id` (`business_id`),
  KEY `linked_by` (`linked_by`),
  CONSTRAINT `partner_accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `partner_accounts_ibfk_2` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  CONSTRAINT `partner_accounts_ibfk_3` FOREIGN KEY (`linked_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partner_accounts`
--

LOCK TABLES `partner_accounts` WRITE;
/*!40000 ALTER TABLE `partner_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `partner_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(80) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(1,'report.create','Input laporan harga','2026-10-28 20:57:56'),
(2,'report.verify','Verifikasi laporan pangan','2026-10-28 20:57:56'),
(3,'correction.review','Tinjau koreksi masyarakat','2026-10-28 20:57:56'),
(4,'point.manage','Kelola titik pantau','2026-10-28 20:57:56'),
(5,'supply.manage','Kelola pasokan','2026-10-28 20:57:56'),
(6,'analytics.read','Baca analitik','2026-10-28 20:57:56'),
(7,'report.export','Ekspor laporan','2026-10-28 20:57:56'),
(8,'action.assign','Tugaskan tindak lanjut','2026-10-28 20:57:56'),
(9,'action.update','Perbarui tindak lanjut','2026-10-28 20:57:56'),
(10,'alerts.validate','Validasi peringatan dini','2026-10-28 20:57:56'),
(11,'dashboard.view','Buka dashboard sesuai cakupan','2026-10-28 20:57:56'),
(12,'prices.view','Lihat tabulasi harga dan stok','2026-10-28 20:57:56'),
(13,'report.edit_draft','Ubah draft sendiri sesuai cakupan','2026-10-28 20:57:56'),
(14,'report.revise','Usulkan revisi resmi oleh tim internal','2026-10-28 20:57:56'),
(15,'master.view','Lihat master data','2026-10-28 20:57:56'),
(16,'master.manage','Kelola master data','2026-10-28 20:57:56'),
(17,'policy.view','Lihat paket kebijakan','2026-10-28 20:57:56'),
(18,'policy.manage','Kelola draft rule kebijakan','2026-10-28 20:57:56'),
(19,'policy.publish','Publikasikan versi rule','2026-10-28 20:57:56'),
(20,'policy.approve','Setujui kasus rekomendasi kebijakan','2026-10-28 20:57:56'),
(21,'action.view','Lihat tindak lanjut','2026-10-28 20:57:56'),
(22,'reports.view','Buka menu laporan','2026-10-28 20:57:56'),
(23,'access.manage','Kelola template izin dan cakupan pengguna','2026-10-28 20:57:56'),
(24,'audit.view','Lihat audit aktivitas','2026-10-28 20:57:56');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `point_assignments`
--

DROP TABLE IF EXISTS `point_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `point_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `point_id` bigint(20) unsigned NOT NULL,
  `business_id` bigint(20) unsigned DEFAULT NULL,
  `starts_on` date NOT NULL,
  `ends_on` date DEFAULT NULL,
  `report_days` varchar(60) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `point_id` (`point_id`),
  CONSTRAINT `point_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `point_assignments_ibfk_2` FOREIGN KEY (`point_id`) REFERENCES `monitoring_points` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `point_assignments`
--

LOCK TABLES `point_assignments` WRITE;
/*!40000 ALTER TABLE `point_assignments` DISABLE KEYS */;
INSERT INTO `point_assignments` VALUES
(1,2,1,NULL,'2026-01-01',NULL,NULL,'2026-10-28 20:57:56'),
(2,2,2,NULL,'2026-01-01',NULL,NULL,'2026-10-28 20:57:56'),
(3,2,7,NULL,'2026-01-01',NULL,NULL,'2026-10-28 20:57:56'),
(4,3,3,NULL,'2026-01-01',NULL,NULL,'2026-10-28 20:57:56'),
(5,3,4,NULL,'2026-01-01',NULL,NULL,'2026-10-28 20:57:56'),
(6,3,8,NULL,'2026-01-01',NULL,NULL,'2026-10-28 20:57:56'),
(7,6,2,NULL,'2026-01-01',NULL,NULL,'2026-10-28 20:57:56');
/*!40000 ALTER TABLE `point_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policy_case_events`
--

DROP TABLE IF EXISTS `policy_case_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policy_case_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) unsigned NOT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `event_type` varchar(60) NOT NULL,
  `note` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `case_id` (`case_id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `policy_case_events_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `policy_cases` (`id`),
  CONSTRAINT `policy_case_events_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_case_events`
--

LOCK TABLES `policy_case_events` WRITE;
/*!40000 ALTER TABLE `policy_case_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_case_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policy_cases`
--

DROP TABLE IF EXISTS `policy_cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policy_cases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `result_id` bigint(20) unsigned NOT NULL,
  `dedupe_key` varchar(190) NOT NULL,
  `status` enum('pending','approved','rejected','in_progress','closed') NOT NULL DEFAULT 'pending',
  `requested_by` bigint(20) unsigned DEFAULT NULL,
  `decided_by` bigint(20) unsigned DEFAULT NULL,
  `decided_at` datetime DEFAULT NULL,
  `decision_note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `result_id` (`result_id`),
  UNIQUE KEY `dedupe_key` (`dedupe_key`),
  KEY `requested_by` (`requested_by`),
  KEY `decided_by` (`decided_by`),
  CONSTRAINT `policy_cases_ibfk_1` FOREIGN KEY (`result_id`) REFERENCES `policy_evaluation_results` (`id`),
  CONSTRAINT `policy_cases_ibfk_2` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  CONSTRAINT `policy_cases_ibfk_3` FOREIGN KEY (`decided_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_cases`
--

LOCK TABLES `policy_cases` WRITE;
/*!40000 ALTER TABLE `policy_cases` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_cases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policy_evaluation_results`
--

DROP TABLE IF EXISTS `policy_evaluation_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policy_evaluation_results` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `run_id` bigint(20) unsigned NOT NULL,
  `point_id` bigint(20) unsigned NOT NULL,
  `commodity_id` bigint(20) unsigned NOT NULL,
  `outcome` enum('matched','not_matched','insufficient_data') NOT NULL,
  `evidence_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`evidence_snapshot`)),
  `is_demo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `run_id` (`run_id`,`point_id`,`commodity_id`),
  KEY `point_id` (`point_id`),
  KEY `commodity_id` (`commodity_id`),
  CONSTRAINT `policy_evaluation_results_ibfk_1` FOREIGN KEY (`run_id`) REFERENCES `policy_evaluation_runs` (`id`),
  CONSTRAINT `policy_evaluation_results_ibfk_2` FOREIGN KEY (`point_id`) REFERENCES `monitoring_points` (`id`),
  CONSTRAINT `policy_evaluation_results_ibfk_3` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_evaluation_results`
--

LOCK TABLES `policy_evaluation_results` WRITE;
/*!40000 ALTER TABLE `policy_evaluation_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_evaluation_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policy_evaluation_runs`
--

DROP TABLE IF EXISTS `policy_evaluation_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policy_evaluation_runs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version_id` bigint(20) unsigned NOT NULL,
  `evaluated_at` datetime NOT NULL,
  `data_cutoff_at` datetime NOT NULL,
  `engine_version` varchar(60) NOT NULL,
  `mode` enum('test','live') NOT NULL DEFAULT 'test',
  `status` enum('running','completed','failed') NOT NULL,
  `definition_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`definition_snapshot`)),
  `error_message` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `version_id` (`version_id`),
  CONSTRAINT `policy_evaluation_runs_ibfk_1` FOREIGN KEY (`version_id`) REFERENCES `policy_versions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_evaluation_runs`
--

LOCK TABLES `policy_evaluation_runs` WRITE;
/*!40000 ALTER TABLE `policy_evaluation_runs` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_evaluation_runs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policy_packages`
--

DROP TABLE IF EXISTS `policy_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policy_packages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint(20) unsigned NOT NULL,
  `code` varchar(80) NOT NULL,
  `name` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `organization_id` (`organization_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `policy_packages_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`),
  CONSTRAINT `policy_packages_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_packages`
--

LOCK TABLES `policy_packages` WRITE;
/*!40000 ALTER TABLE `policy_packages` DISABLE KEYS */;
INSERT INTO `policy_packages` VALUES
(1,1,'DEMO-STABILISASI-1','SIMULASI Stabilisasi harga dan pasokan','Kenaikan harga DAN stok rendah; semua rekomendasi membutuhkan persetujuan',NULL,'2026-10-28 20:57:56'),
(2,2,'DEMO-STABILISASI-2','SIMULASI Stabilisasi harga dan pasokan','Kenaikan harga DAN stok rendah; semua rekomendasi membutuhkan persetujuan',NULL,'2026-10-28 20:57:56');
/*!40000 ALTER TABLE `policy_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policy_rule_conditions`
--

DROP TABLE IF EXISTS `policy_rule_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policy_rule_conditions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) unsigned NOT NULL,
  `metric` enum('price_change_pct','stock_days','report_overdue_hours','shipment_delay_hours') NOT NULL,
  `comparator` enum('gt','gte','lt','lte','eq') NOT NULL,
  `threshold` decimal(16,4) NOT NULL,
  `baseline` enum('previous_observation','days_ago','none') NOT NULL,
  `lookback_days` int(11) NOT NULL DEFAULT 0,
  `max_age_hours` int(11) NOT NULL DEFAULT 48,
  `min_observations` int(11) NOT NULL DEFAULT 1,
  `price_type` enum('retail','wholesale','producer') NOT NULL DEFAULT 'retail',
  `aggregation` enum('point_latest','point_average') NOT NULL DEFAULT 'point_latest',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `policy_rule_conditions_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `policy_rule_groups` (`id`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`lookback_days` >= 0 and `max_age_hours` > 0 and `min_observations` > 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`metric` <> 'price_change_pct' or `baseline` <> 'none' and `min_observations` >= 2)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_rule_conditions`
--

LOCK TABLES `policy_rule_conditions` WRITE;
/*!40000 ALTER TABLE `policy_rule_conditions` DISABLE KEYS */;
INSERT INTO `policy_rule_conditions` VALUES
(1,1,'price_change_pct','gt',12.5000,'days_ago',7,48,2,'retail','point_latest'),
(2,2,'price_change_pct','gt',10.0000,'days_ago',7,48,2,'retail','point_latest'),
(4,1,'stock_days','lt',3.0000,'none',0,48,1,'retail','point_latest'),
(5,2,'stock_days','lt',3.0000,'none',0,48,1,'retail','point_latest');
/*!40000 ALTER TABLE `policy_rule_conditions` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bi_policy_rule_conditions BEFORE INSERT ON policy_rule_conditions FOR EACH ROW
BEGIN
 IF (SELECT v.state FROM policy_rule_groups g JOIN policy_versions v ON v.id=g.version_id WHERE g.id=NEW.group_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bu_policy_rule_conditions BEFORE UPDATE ON policy_rule_conditions FOR EACH ROW
BEGIN
 IF (SELECT v.state FROM policy_rule_groups g JOIN policy_versions v ON v.id=g.version_id WHERE g.id=OLD.group_id)<>'draft' OR (SELECT v.state FROM policy_rule_groups g JOIN policy_versions v ON v.id=g.version_id WHERE g.id=NEW.group_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bd_policy_rule_conditions BEFORE DELETE ON policy_rule_conditions FOR EACH ROW
BEGIN
 IF (SELECT v.state FROM policy_rule_groups g JOIN policy_versions v ON v.id=g.version_id WHERE g.id=OLD.group_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `policy_rule_groups`
--

DROP TABLE IF EXISTS `policy_rule_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policy_rule_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version_id` bigint(20) unsigned NOT NULL,
  `parent_group_id` bigint(20) unsigned DEFAULT NULL,
  `logic` enum('AND','OR') NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`version_id`),
  KEY `version_id` (`version_id`),
  KEY `parent_group_id` (`parent_group_id`,`version_id`),
  CONSTRAINT `policy_rule_groups_ibfk_1` FOREIGN KEY (`version_id`) REFERENCES `policy_versions` (`id`),
  CONSTRAINT `policy_rule_groups_ibfk_2` FOREIGN KEY (`parent_group_id`, `version_id`) REFERENCES `policy_rule_groups` (`id`, `version_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_rule_groups`
--

LOCK TABLES `policy_rule_groups` WRITE;
/*!40000 ALTER TABLE `policy_rule_groups` DISABLE KEYS */;
INSERT INTO `policy_rule_groups` VALUES
(1,1,NULL,'AND',0),
(2,2,NULL,'AND',0);
/*!40000 ALTER TABLE `policy_rule_groups` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bi_policy_rule_groups BEFORE INSERT ON policy_rule_groups FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=NEW.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bu_policy_rule_groups BEFORE UPDATE ON policy_rule_groups FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=OLD.version_id)<>'draft' OR (SELECT state FROM policy_versions WHERE id=NEW.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bd_policy_rule_groups BEFORE DELETE ON policy_rule_groups FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=OLD.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `policy_targets`
--

DROP TABLE IF EXISTS `policy_targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policy_targets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version_id` bigint(20) unsigned NOT NULL,
  `region_id` bigint(20) unsigned DEFAULT NULL,
  `market_id` bigint(20) unsigned DEFAULT NULL,
  `point_id` bigint(20) unsigned DEFAULT NULL,
  `commodity_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `version_id` (`version_id`),
  KEY `region_id` (`region_id`),
  KEY `market_id` (`market_id`),
  KEY `point_id` (`point_id`),
  KEY `commodity_id` (`commodity_id`),
  CONSTRAINT `policy_targets_ibfk_1` FOREIGN KEY (`version_id`) REFERENCES `policy_versions` (`id`),
  CONSTRAINT `policy_targets_ibfk_2` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`),
  CONSTRAINT `policy_targets_ibfk_3` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`),
  CONSTRAINT `policy_targets_ibfk_4` FOREIGN KEY (`point_id`) REFERENCES `monitoring_points` (`id`),
  CONSTRAINT `policy_targets_ibfk_5` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`),
  CONSTRAINT `CONSTRAINT_1` CHECK ((`region_id` is not null) + (`market_id` is not null) + (`point_id` is not null) = 1)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_targets`
--

LOCK TABLES `policy_targets` WRITE;
/*!40000 ALTER TABLE `policy_targets` DISABLE KEYS */;
INSERT INTO `policy_targets` VALUES
(1,1,1,NULL,NULL,NULL),
(2,2,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `policy_targets` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bi_policy_targets BEFORE INSERT ON policy_targets FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=NEW.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bu_policy_targets BEFORE UPDATE ON policy_targets FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=OLD.version_id)<>'draft' OR (SELECT state FROM policy_versions WHERE id=NEW.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bd_policy_targets BEFORE DELETE ON policy_targets FOR EACH ROW
BEGIN
 IF (SELECT state FROM policy_versions WHERE id=OLD.version_id)<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Edit only draft policy definitions'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `policy_versions`
--

DROP TABLE IF EXISTS `policy_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `policy_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint(20) unsigned NOT NULL,
  `version_no` int(11) NOT NULL,
  `state` enum('draft','published','retired') NOT NULL DEFAULT 'draft',
  `severity` enum('info','warning','critical') NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime DEFAULT NULL,
  `cooldown_hours` int(11) NOT NULL DEFAULT 24,
  `recommendation` text NOT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 1,
  `published_by` bigint(20) unsigned DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `frozen_definition` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`frozen_definition`)),
  `is_demo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `package_id` (`package_id`,`version_no`),
  KEY `published_by` (`published_by`),
  CONSTRAINT `policy_versions_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `policy_packages` (`id`),
  CONSTRAINT `policy_versions_ibfk_2` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`version_no` > 0 and `cooldown_hours` >= 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`requires_approval` = 1),
  CONSTRAINT `CONSTRAINT_3` CHECK (`valid_until` is null or `valid_until` > `valid_from`),
  CONSTRAINT `CONSTRAINT_4` CHECK (`state` = 'draft' or `published_at` is not null and `frozen_definition` is not null)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policy_versions`
--

LOCK TABLES `policy_versions` WRITE;
/*!40000 ALTER TABLE `policy_versions` DISABLE KEYS */;
INSERT INTO `policy_versions` VALUES
(1,1,1,'draft','warning','2026-01-01 00:00:00',NULL,24,'Koordinasi pasar uji gap',1,NULL,NULL,NULL,1),
(2,2,1,'draft','warning','2026-01-01 00:00:00',NULL,24,'Verifikasi lapangan dan koordinasi pemasok; bukan otorisasi anggaran',1,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `policy_versions` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bu_policy_versions BEFORE UPDATE ON policy_versions FOR EACH ROW
BEGIN
 IF OLD.state<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published policy immutable; create a new version'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER bd_policy_versions BEFORE DELETE ON policy_versions FOR EACH ROW
BEGIN
 IF OLD.state<>'draft' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Published policy cannot be deleted'; END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `regions`
--

DROP TABLE IF EXISTS `regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `regions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `level` enum('provinsi','kabupaten','kecamatan','desa') NOT NULL DEFAULT 'kecamatan',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `regions_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regions`
--

LOCK TABLES `regions` WRITE;
/*!40000 ALTER TABLE `regions` DISABLE KEYS */;
INSERT INTO `regions` VALUES
(1,NULL,'BENGKULU_TENGAH','Kabupaten Bengkulu Tengah','kabupaten','2026-10-28 20:57:56'),
(2,1,'KARANG_TINGGI','Karang Tinggi','kecamatan','2026-10-28 20:57:56'),
(3,1,'TALANG_EMPAT','Talang Empat','kecamatan','2026-10-28 20:57:56'),
(4,1,'PONDOK_KELAPA','Pondok Kelapa','kecamatan','2026-10-28 20:57:56'),
(5,1,'TABA_PENANJUNG','Taba Penanjung','kecamatan','2026-10-28 20:57:56'),
(6,1,'PONDOK_KUBANG','Pondok Kubang','kecamatan','2026-10-28 20:57:56'),
(7,1,'PEMATANG_TIGA','Pematang Tiga','kecamatan','2026-10-28 20:57:56'),
(8,1,'BANG_HAJI','Bang Haji','kecamatan','2026-10-28 20:57:56'),
(9,1,'SEMIDANG_LAGAN','Semidang Lagan','kecamatan','2026-10-28 20:57:56');
/*!40000 ALTER TABLE `regions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_items`
--

DROP TABLE IF EXISTS `report_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) unsigned NOT NULL,
  `commodity_id` bigint(20) unsigned NOT NULL,
  `price` decimal(16,2) NOT NULL,
  `stock` decimal(16,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(20) NOT NULL DEFAULT 'kg',
  `price_change_pct` decimal(8,2) NOT NULL DEFAULT 0.00,
  `price_change_amount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`),
  KEY `commodity_id` (`commodity_id`),
  CONSTRAINT `report_items_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_items_ibfk_2` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_items`
--

LOCK TABLES `report_items` WRITE;
/*!40000 ALTER TABLE `report_items` DISABLE KEYS */;
INSERT INTO `report_items` VALUES
(1,1,1,14500.00,250.00,'kg',-2.03,-300.00,'Harga normal, stok memadai','2026-10-28 20:57:56'),
(2,1,3,18000.00,120.00,'kg',1.69,300.00,'Sedikit naik dari distributor','2026-10-28 20:57:56'),
(3,1,4,18500.00,90.00,'liter',-1.60,-300.00,'Minyak curah lancar','2026-10-28 20:57:56'),
(4,1,5,48000.00,12.00,'kg',4.35,2000.00,'Stok terbatas','2026-10-28 20:57:56'),
(5,1,7,28000.00,80.00,'kg',0.00,0.00,'Stabil','2026-10-28 20:57:56'),
(6,1,8,36000.00,45.00,'kg',-1.50,-500.00,'Stabil','2026-10-28 20:57:56'),
(7,2,1,14800.00,180.00,'kg',0.00,0.00,'Beras medium stabil','2026-10-28 20:57:56'),
(8,2,3,18200.00,95.00,'kg',1.10,200.00,'Gula pasir','2026-10-28 20:57:56'),
(9,2,5,52000.00,8.00,'kg',12.50,5000.00,'Lonjakan harga cabai','2026-10-28 20:57:56'),
(10,2,7,28500.00,60.00,'kg',1.79,500.00,'Telur ayam','2026-10-28 20:57:56'),
(11,3,1,14400.00,300.00,'kg',-1.37,-200.00,'Beras cukup','2026-10-28 20:57:56'),
(12,3,4,18200.00,110.00,'liter',-2.15,-400.00,'Minyak lancar','2026-10-28 20:57:56'),
(13,3,5,46000.00,25.00,'kg',2.22,1000.00,'Cabai merah','2026-10-28 20:57:56'),
(14,4,1,14500.00,200.00,'kg',0.00,0.00,'Simulasi','2026-10-28 20:57:56'),
(15,4,5,50000.00,5.00,'kg',15.00,6500.00,'Simulasi kenaikan ekstrim cabai','2026-10-28 20:57:56'),
(16,5,1,15200.00,150.00,'kg',0.00,0.00,'Pengujian input data','2026-10-28 21:27:40'),
(17,6,1,15200.00,150.00,'kg',0.00,0.00,'Pengujian input data','2026-10-28 22:22:50'),
(18,7,1,15200.00,150.00,'kg',0.00,0.00,'Pengujian input data','2026-10-28 22:46:45'),
(19,8,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 15:58:12'),
(20,9,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 15:58:46'),
(21,10,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 16:03:41'),
(22,11,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 16:17:11'),
(23,12,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 16:23:47'),
(24,13,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 16:24:02'),
(25,14,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 16:24:37'),
(26,15,1,14500.00,0.00,'kg',3.57,500.00,'Impor SP2KP Kemendag — Beras Medium','2026-10-28 23:24:54'),
(27,16,1,15200.00,150.00,'kg',0.00,0.00,'Pengujian input data','2026-10-28 23:25:03'),
(28,17,1,15100.00,40.00,'kg',-98.47,-972554.00,'Draft uji gap','2026-10-29 06:37:21'),
(29,18,1,16111.00,55.00,'kg',0.00,0.00,'Impor CSV','2026-10-29 06:37:21'),
(30,19,1,15200.00,150.00,'kg',0.00,0.00,'Pengujian input data','2026-10-29 06:37:28'),
(31,20,1,15100.00,40.00,'kg',-98.47,-972554.00,'Draft uji gap','2026-10-29 06:37:38'),
(32,21,1,16111.00,55.00,'kg',0.00,0.00,'Impor CSV','2026-10-29 06:37:38'),
(33,22,1,15200.00,150.00,'kg',0.00,0.00,'Pengujian input data','2026-10-29 06:37:39'),
(34,23,1,14375.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Beras Medium','2026-10-29 06:41:25'),
(35,23,2,16500.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Beras Premium','2026-10-29 06:41:25'),
(36,23,3,18000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Gula Pasir Curah','2026-10-29 06:41:25'),
(37,23,10,160000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Daging Sapi Paha Belakang','2026-10-29 06:41:25'),
(38,23,11,35000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Daging Ayam Ras','2026-10-29 06:41:25'),
(39,23,7,25600.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Telur Ayam Ras','2026-10-29 06:41:25'),
(40,23,12,13000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Tepung Terigu','2026-10-29 06:41:25'),
(41,23,5,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Merah Keriting','2026-10-29 06:41:25'),
(42,23,6,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Rawit Merah','2026-10-29 06:41:25'),
(43,23,8,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Merah','2026-10-29 06:41:25'),
(44,23,9,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Putih Honan','2026-10-29 06:41:25'),
(45,24,1,14000.00,0.00,'kg',-2.61,-375.00,'Impor SP2KP Kemendag — Beras Medium','2026-10-29 06:41:25'),
(46,24,2,16500.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Beras Premium','2026-10-29 06:41:25'),
(47,24,3,18000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Gula Pasir Curah','2026-10-29 06:41:25'),
(48,24,10,160000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Daging Sapi Paha Belakang','2026-10-29 06:41:25'),
(49,24,11,33000.00,0.00,'kg',-5.71,-2000.00,'Impor SP2KP Kemendag — Daging Ayam Ras','2026-10-29 06:41:25'),
(50,24,7,25600.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Telur Ayam Ras','2026-10-29 06:41:25'),
(51,24,12,13000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Tepung Terigu','2026-10-29 06:41:25'),
(52,24,5,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Merah Keriting','2026-10-29 06:41:25'),
(53,24,6,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Rawit Merah','2026-10-29 06:41:25'),
(54,24,8,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Merah','2026-10-29 06:41:25'),
(55,24,9,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Putih Honan','2026-10-29 06:41:25'),
(56,25,1,14000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Beras Medium','2026-10-29 06:41:25'),
(57,25,2,16500.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Beras Premium','2026-10-29 06:41:25'),
(58,25,3,18000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Gula Pasir Curah','2026-10-29 06:41:25'),
(59,25,10,160000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Daging Sapi Paha Belakang','2026-10-29 06:41:25'),
(60,25,11,33000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Daging Ayam Ras','2026-10-29 06:41:25'),
(61,25,7,25600.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Telur Ayam Ras','2026-10-29 06:41:25'),
(62,25,12,13000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Tepung Terigu','2026-10-29 06:41:25'),
(63,25,5,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Merah Keriting','2026-10-29 06:41:25'),
(64,25,6,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Rawit Merah','2026-10-29 06:41:25'),
(65,25,8,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Merah','2026-10-29 06:41:25'),
(66,25,9,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Putih Honan','2026-10-29 06:41:25'),
(67,26,1,14000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Beras Medium','2026-10-29 06:41:25'),
(68,26,2,16500.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Beras Premium','2026-10-29 06:41:25'),
(69,26,3,18000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Gula Pasir Curah','2026-10-29 06:41:25'),
(70,26,10,160000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Daging Sapi Paha Belakang','2026-10-29 06:41:25'),
(71,26,11,33000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Daging Ayam Ras','2026-10-29 06:41:25'),
(72,26,7,25600.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Telur Ayam Ras','2026-10-29 06:41:25'),
(73,26,12,13000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Tepung Terigu','2026-10-29 06:41:25'),
(74,26,5,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Merah Keriting','2026-10-29 06:41:25'),
(75,26,6,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Rawit Merah','2026-10-29 06:41:25'),
(76,26,8,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Merah','2026-10-29 06:41:25'),
(77,26,9,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Putih Honan','2026-10-29 06:41:25'),
(78,27,1,14000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Beras Medium','2026-10-29 06:41:25'),
(79,27,2,16500.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Beras Premium','2026-10-29 06:41:25'),
(80,27,3,18000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Gula Pasir Curah','2026-10-29 06:41:25'),
(81,27,10,160000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Daging Sapi Paha Belakang','2026-10-29 06:41:25'),
(82,27,11,33000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Daging Ayam Ras','2026-10-29 06:41:25'),
(83,27,7,25600.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Telur Ayam Ras','2026-10-29 06:41:25'),
(84,27,12,13000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Tepung Terigu','2026-10-29 06:41:25'),
(85,27,5,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Merah Keriting','2026-10-29 06:41:25'),
(86,27,6,80000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Cabai Rawit Merah','2026-10-29 06:41:25'),
(87,27,8,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Merah','2026-10-29 06:41:25'),
(88,27,9,30000.00,0.00,'kg',0.00,0.00,'Impor SP2KP Kemendag — Bawang Putih Honan','2026-10-29 06:41:25'),
(89,28,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 23:49:35'),
(90,29,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 23:52:15'),
(91,30,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 23:55:14'),
(92,31,1,15100.00,40.00,'kg',-98.47,-972554.00,'Draft uji gap','2026-10-29 06:56:56'),
(93,32,1,16111.00,55.00,'kg',0.00,0.00,'Impor CSV','2026-10-29 06:56:56'),
(94,33,1,15200.00,150.00,'kg',0.00,0.00,'Pengujian input data','2026-10-29 06:56:57'),
(95,34,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-28 23:56:58'),
(96,35,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-29 00:18:49'),
(97,36,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-29 00:19:56'),
(98,37,1,15200.00,150.00,'kg',0.00,0.00,'Pengujian input data','2026-10-29 07:19:57'),
(99,38,1,987654.00,10.00,'kg',0.00,0.00,NULL,'2026-10-29 00:26:29');
/*!40000 ALTER TABLE `report_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_series`
--

DROP TABLE IF EXISTS `report_series`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_series` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `point_id` bigint(20) unsigned NOT NULL,
  `observed_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `point_id` (`point_id`),
  CONSTRAINT `report_series_ibfk_1` FOREIGN KEY (`point_id`) REFERENCES `monitoring_points` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_series`
--

LOCK TABLES `report_series` WRITE;
/*!40000 ALTER TABLE `report_series` DISABLE KEYS */;
INSERT INTO `report_series` VALUES
(1,1,'2026-10-27 08:30:00','2026-10-28 20:57:56'),
(2,3,'2026-10-27 09:00:00','2026-10-28 20:57:56'),
(3,5,'2026-10-27 09:30:00','2026-10-28 20:57:56'),
(4,7,'2026-10-27 10:00:00','2026-10-28 20:57:56'),
(5,1,'2026-10-27 00:00:00','2026-10-28 21:27:40'),
(6,1,'2026-10-27 00:00:00','2026-10-28 22:22:50'),
(7,1,'2026-10-27 00:00:00','2026-10-28 22:46:45'),
(8,1,'2026-10-27 15:00:00','2026-10-28 22:58:12'),
(9,1,'2026-10-27 15:00:00','2026-10-28 22:58:46'),
(10,1,'2026-10-27 15:00:00','2026-10-28 23:03:41'),
(11,1,'2026-10-27 15:00:00','2026-10-28 23:17:11'),
(12,1,'2026-10-27 15:00:00','2026-10-28 23:23:47'),
(13,1,'2026-10-27 15:00:00','2026-10-28 23:24:02'),
(14,1,'2026-10-27 15:00:00','2026-10-28 23:24:37'),
(15,1,'2026-10-27 08:00:00','2026-10-28 23:24:54'),
(16,1,'2026-10-27 00:00:00','2026-10-28 23:25:03'),
(17,1,'2026-10-28 00:00:00','2026-10-29 06:37:21'),
(18,1,'2026-10-28 08:00:00','2026-10-29 06:37:21'),
(19,1,'2026-10-27 00:00:00','2026-10-29 06:37:28'),
(20,1,'2026-10-28 00:00:00','2026-10-29 06:37:38'),
(21,1,'2026-10-28 08:00:00','2026-10-29 06:37:38'),
(22,1,'2026-10-27 00:00:00','2026-10-29 06:37:39'),
(23,9,'2026-10-21 08:00:00','2026-10-29 06:41:25'),
(24,9,'2026-10-22 08:00:00','2026-10-29 06:41:25'),
(25,9,'2026-10-23 08:00:00','2026-10-29 06:41:25'),
(26,9,'2026-10-26 08:00:00','2026-10-29 06:41:25'),
(27,9,'2026-10-27 08:00:00','2026-10-29 06:41:25'),
(28,1,'2026-10-27 15:00:00','2026-10-29 06:49:35'),
(29,1,'2026-10-27 15:00:00','2026-10-29 06:52:15'),
(30,1,'2026-10-27 15:00:00','2026-10-29 06:55:14'),
(31,1,'2026-10-28 00:00:00','2026-10-29 06:56:56'),
(32,1,'2026-10-28 08:00:00','2026-10-29 06:56:56'),
(33,1,'2026-10-27 00:00:00','2026-10-29 06:56:57'),
(34,1,'2026-10-27 15:00:00','2026-10-29 06:56:58'),
(35,1,'2026-10-27 15:00:00','2026-10-29 07:18:49'),
(36,1,'2026-10-27 15:00:00','2026-10-29 07:19:56'),
(37,1,'2026-10-27 00:00:00','2026-10-29 07:19:57'),
(38,1,'2026-10-27 15:00:00','2026-10-29 07:26:29');
/*!40000 ALTER TABLE `report_series` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `series_id` bigint(20) unsigned NOT NULL,
  `source_id` bigint(20) unsigned NOT NULL,
  `status` enum('draft','submitted','approved','returned','rejected','superseded') NOT NULL DEFAULT 'draft',
  `submitted_by` bigint(20) unsigned NOT NULL,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `previous_report_id` bigint(20) unsigned DEFAULT NULL,
  `version_no` int(11) NOT NULL DEFAULT 1,
  `is_demo` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `evidence_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `series_id` (`series_id`),
  KEY `source_id` (`source_id`),
  KEY `submitted_by` (`submitted_by`),
  KEY `verified_by` (`verified_by`),
  KEY `previous_report_id` (`previous_report_id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `report_series` (`id`),
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`source_id`) REFERENCES `data_sources` (`id`),
  CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `reports_ibfk_4` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`),
  CONSTRAINT `reports_ibfk_5` FOREIGN KEY (`previous_report_id`) REFERENCES `reports` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES
(1,1,1,'approved',2,4,'2026-10-27 10:24:00',NULL,1,0,'Data terpantau stabil di Pasar Karang Tinggi',NULL,'2026-10-28 20:57:56'),
(2,2,1,'approved',3,4,'2026-10-27 10:41:00',NULL,1,0,'Pasokan cabai agak menurun karena hujan di sentra produksi',NULL,'2026-10-28 20:57:56'),
(3,3,1,'approved',2,4,'2026-10-27 11:00:00',NULL,1,0,'Stok beras dan minyak aman terkendali',NULL,'2026-10-28 20:57:56'),
(4,4,4,'submitted',2,NULL,NULL,NULL,1,1,'Data pemantauan simulasi harian',NULL,'2026-10-28 20:57:56'),
(5,5,1,'submitted',1,NULL,NULL,NULL,1,0,'Pengujian input data',NULL,'2026-10-28 21:27:40'),
(6,6,1,'submitted',1,NULL,NULL,NULL,1,0,'Pengujian input data',NULL,'2026-10-28 22:22:50'),
(7,7,1,'submitted',1,NULL,NULL,NULL,1,0,'Pengujian input data',NULL,'2026-10-28 22:46:45'),
(8,8,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 15:58:12'),
(9,9,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 15:58:46'),
(10,10,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 16:03:41'),
(11,11,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 16:17:11'),
(12,12,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 16:23:47'),
(13,13,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 16:24:02'),
(14,14,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 16:24:37'),
(15,15,5,'submitted',1,NULL,NULL,NULL,1,0,'Hasil crawling SP2KP Kemendag tanggal 2026-10-27',NULL,'2026-10-28 23:24:54'),
(16,16,1,'submitted',1,NULL,NULL,NULL,1,0,'Pengujian input data',NULL,'2026-10-28 23:25:03'),
(17,17,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji gap',NULL,'2026-10-29 06:37:21'),
(18,18,1,'submitted',2,NULL,NULL,NULL,1,0,'Impor CSV template harga',NULL,'2026-10-29 06:37:21'),
(19,19,1,'submitted',1,NULL,NULL,NULL,1,0,'Pengujian input data',NULL,'2026-10-29 06:37:28'),
(20,20,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji gap',NULL,'2026-10-29 06:37:38'),
(21,21,1,'submitted',2,NULL,NULL,NULL,1,0,'Impor CSV template harga',NULL,'2026-10-29 06:37:38'),
(22,22,1,'submitted',1,NULL,NULL,NULL,1,0,'Pengujian input data',NULL,'2026-10-29 06:37:39'),
(23,23,5,'submitted',1,NULL,NULL,NULL,1,0,'Hasil crawling SP2KP Kemendag tanggal 2026-10-21',NULL,'2026-10-29 06:41:25'),
(24,24,5,'submitted',1,NULL,NULL,NULL,1,0,'Hasil crawling SP2KP Kemendag tanggal 2026-10-22',NULL,'2026-10-29 06:41:25'),
(25,25,5,'submitted',1,NULL,NULL,NULL,1,0,'Hasil crawling SP2KP Kemendag tanggal 2026-10-23',NULL,'2026-10-29 06:41:25'),
(26,26,5,'submitted',1,NULL,NULL,NULL,1,0,'Hasil crawling SP2KP Kemendag tanggal 2026-10-26',NULL,'2026-10-29 06:41:25'),
(27,27,5,'submitted',1,NULL,NULL,NULL,1,0,'Hasil crawling SP2KP Kemendag tanggal 2026-10-27',NULL,'2026-10-29 06:41:25'),
(28,28,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 23:49:35'),
(29,29,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 23:52:15'),
(30,30,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 23:55:14'),
(31,31,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji gap',NULL,'2026-10-29 06:56:56'),
(32,32,1,'submitted',2,NULL,NULL,NULL,1,0,'Impor CSV template harga',NULL,'2026-10-29 06:56:56'),
(33,33,1,'submitted',1,NULL,NULL,NULL,1,0,'Pengujian input data',NULL,'2026-10-29 06:56:57'),
(34,34,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-28 23:56:58'),
(35,35,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-29 00:18:49'),
(36,36,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-29 00:19:56'),
(37,37,1,'submitted',1,NULL,NULL,NULL,1,0,'Pengujian input data',NULL,'2026-10-29 07:19:57'),
(38,38,1,'draft',2,NULL,NULL,NULL,1,0,'Draft uji halaman publik',NULL,'2026-10-29 00:26:29');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES
(1,1),
(1,2),
(1,3),
(1,4),
(1,5),
(1,6),
(1,7),
(1,8),
(1,9),
(1,10),
(1,11),
(2,11),
(3,11),
(4,11),
(5,11),
(1,12),
(2,12),
(3,12),
(4,12),
(5,12),
(1,13),
(2,13),
(5,13),
(1,14),
(2,14),
(1,15),
(2,15),
(3,15),
(4,15),
(5,15),
(1,16),
(2,16),
(1,17),
(2,17),
(3,17),
(4,17),
(1,18),
(1,19),
(1,20),
(4,20),
(1,21),
(2,21),
(3,21),
(4,21),
(1,22),
(2,22),
(3,22),
(4,22),
(1,23),
(1,24);
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
  `code` varchar(60) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'admin','Administrator','2026-10-28 20:57:56'),
(2,'operator','Operator Lapangan','2026-10-28 20:57:56'),
(3,'verifikator','Verifikator Dinas','2026-10-28 20:57:56'),
(4,'pimpinan','Pimpinan / Kadis','2026-10-28 20:57:56'),
(5,'mitra','Mitra Usaha','2026-10-28 20:57:56');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schema_migrations`
--

DROP TABLE IF EXISTS `schema_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `schema_migrations` (
  `version` varchar(60) NOT NULL,
  `applied_at` datetime NOT NULL DEFAULT current_timestamp(),
  `notes` varchar(500) NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schema_migrations`
--

LOCK TABLES `schema_migrations` WRITE;
/*!40000 ALTER TABLE `schema_migrations` DISABLE KEYS */;
INSERT INTO `schema_migrations` VALUES
('2026_10_27_v2','2026-10-28 20:57:57','7 menus; per-user permissions; scoped access; policy packages; verifier updated');
/*!40000 ALTER TABLE `schema_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supply_links`
--

DROP TABLE IF EXISTS `supply_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supply_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) unsigned NOT NULL,
  `point_id` bigint(20) unsigned NOT NULL,
  `commodity_id` bigint(20) unsigned DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `supply_links_business_id_foreign` (`business_id`),
  KEY `supply_links_point_id_foreign` (`point_id`),
  KEY `supply_links_commodity_id_foreign` (`commodity_id`),
  CONSTRAINT `supply_links_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`),
  CONSTRAINT `supply_links_commodity_id_foreign` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`),
  CONSTRAINT `supply_links_point_id_foreign` FOREIGN KEY (`point_id`) REFERENCES `monitoring_points` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supply_links`
--

LOCK TABLES `supply_links` WRITE;
/*!40000 ALTER TABLE `supply_links` DISABLE KEYS */;
INSERT INTO `supply_links` VALUES
(1,1,1,1,'Relasi uji gap lengkap',1,'2026-10-28 16:48:09'),
(2,1,1,1,'Relasi uji gap lengkap',1,'2026-10-28 16:49:36'),
(3,1,1,1,'Relasi uji gap lengkap',1,'2026-10-28 16:52:16'),
(4,1,1,1,'Relasi uji gap lengkap',1,'2026-10-28 16:55:14'),
(5,1,1,1,'Relasi uji gap lengkap',1,'2026-10-28 16:56:57'),
(6,1,1,1,'Relasi uji gap lengkap',1,'2026-10-28 17:18:50'),
(7,1,1,1,'Relasi uji gap lengkap',1,'2026-10-28 17:19:57'),
(8,1,1,1,'Relasi uji gap lengkap',1,'2026-10-28 17:26:30');
/*!40000 ALTER TABLE `supply_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supply_shipments`
--

DROP TABLE IF EXISTS `supply_shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supply_shipments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `supply_link_id` bigint(20) unsigned NOT NULL,
  `scheduled_on` date NOT NULL,
  `quantity` decimal(14,2) NOT NULL,
  `unit` varchar(32) NOT NULL DEFAULT 'kg',
  `status` enum('planned','in_transit','delivered','cancelled') NOT NULL DEFAULT 'planned',
  `notes` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `supply_shipments_supply_link_id_foreign` (`supply_link_id`),
  CONSTRAINT `supply_shipments_supply_link_id_foreign` FOREIGN KEY (`supply_link_id`) REFERENCES `supply_links` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supply_shipments`
--

LOCK TABLES `supply_shipments` WRITE;
/*!40000 ALTER TABLE `supply_shipments` DISABLE KEYS */;
INSERT INTO `supply_shipments` VALUES
(1,1,'2026-10-30',250.00,'kg','planned','Kiriman uji','2026-10-28 16:48:09'),
(2,2,'2026-10-30',250.00,'kg','planned','Kiriman uji','2026-10-28 16:49:36'),
(3,3,'2026-10-30',250.00,'kg','planned','Kiriman uji','2026-10-28 16:52:16'),
(4,4,'2026-10-30',250.00,'kg','planned','Kiriman uji','2026-10-28 16:55:14'),
(5,5,'2026-10-30',250.00,'kg','planned','Kiriman uji','2026-10-28 16:56:57'),
(6,6,'2026-10-30',250.00,'kg','planned','Kiriman uji','2026-10-28 17:18:50'),
(7,7,'2026-10-30',250.00,'kg','planned','Kiriman uji','2026-10-28 17:19:57'),
(8,8,'2026-10-30',250.00,'kg','planned','Kiriman uji','2026-10-28 17:26:30');
/*!40000 ALTER TABLE `supply_shipments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_data_scopes`
--

DROP TABLE IF EXISTS `user_data_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_data_scopes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `scope_type` enum('organization','region','market','point') NOT NULL,
  `organization_id` bigint(20) unsigned DEFAULT NULL,
  `region_id` bigint(20) unsigned DEFAULT NULL,
  `market_id` bigint(20) unsigned DEFAULT NULL,
  `point_id` bigint(20) unsigned DEFAULT NULL,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime DEFAULT NULL,
  `granted_by` bigint(20) unsigned DEFAULT NULL,
  `revoked_by` bigint(20) unsigned DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `reason` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `permission_id` (`permission_id`),
  KEY `organization_id` (`organization_id`),
  KEY `region_id` (`region_id`),
  KEY `market_id` (`market_id`),
  KEY `point_id` (`point_id`),
  KEY `granted_by` (`granted_by`),
  KEY `revoked_by` (`revoked_by`),
  KEY `user_id` (`user_id`,`permission_id`,`revoked_at`,`valid_from`),
  CONSTRAINT `user_data_scopes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_data_scopes_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`),
  CONSTRAINT `user_data_scopes_ibfk_3` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`),
  CONSTRAINT `user_data_scopes_ibfk_4` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`),
  CONSTRAINT `user_data_scopes_ibfk_5` FOREIGN KEY (`market_id`) REFERENCES `markets` (`id`),
  CONSTRAINT `user_data_scopes_ibfk_6` FOREIGN KEY (`point_id`) REFERENCES `monitoring_points` (`id`),
  CONSTRAINT `user_data_scopes_ibfk_7` FOREIGN KEY (`granted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `user_data_scopes_ibfk_8` FOREIGN KEY (`revoked_by`) REFERENCES `users` (`id`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`valid_until` is null or `valid_until` > `valid_from`),
  CONSTRAINT `CONSTRAINT_2` CHECK (`scope_type` = 'organization' and `organization_id` is not null and `region_id` is null and `market_id` is null and `point_id` is null or `scope_type` = 'region' and `organization_id` is null and `region_id` is not null and `market_id` is null and `point_id` is null or `scope_type` = 'market' and `organization_id` is null and `region_id` is null and `market_id` is not null and `point_id` is null or `scope_type` = 'point' and `organization_id` is null and `region_id` is null and `market_id` is null and `point_id` is not null)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_data_scopes`
--

LOCK TABLES `user_data_scopes` WRITE;
/*!40000 ALTER TABLE `user_data_scopes` DISABLE KEYS */;
INSERT INTO `user_data_scopes` VALUES
(1,2,11,'point',NULL,NULL,NULL,1,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(2,2,15,'point',NULL,NULL,NULL,1,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(3,2,12,'point',NULL,NULL,NULL,1,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(4,2,1,'point',NULL,NULL,NULL,1,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(5,2,13,'point',NULL,NULL,NULL,1,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(6,2,14,'point',NULL,NULL,NULL,1,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(7,2,11,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(8,2,15,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(9,2,12,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(10,2,1,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(11,2,13,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(12,2,14,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(13,2,11,'point',NULL,NULL,NULL,7,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(14,2,15,'point',NULL,NULL,NULL,7,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(15,2,12,'point',NULL,NULL,NULL,7,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(16,2,1,'point',NULL,NULL,NULL,7,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(17,2,13,'point',NULL,NULL,NULL,7,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(18,2,14,'point',NULL,NULL,NULL,7,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(19,3,11,'point',NULL,NULL,NULL,3,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(20,3,15,'point',NULL,NULL,NULL,3,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(21,3,12,'point',NULL,NULL,NULL,3,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(22,3,1,'point',NULL,NULL,NULL,3,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(23,3,13,'point',NULL,NULL,NULL,3,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(24,3,14,'point',NULL,NULL,NULL,3,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(25,3,11,'point',NULL,NULL,NULL,4,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(26,3,15,'point',NULL,NULL,NULL,4,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(27,3,12,'point',NULL,NULL,NULL,4,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(28,3,1,'point',NULL,NULL,NULL,4,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(29,3,13,'point',NULL,NULL,NULL,4,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(30,3,14,'point',NULL,NULL,NULL,4,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(31,3,11,'point',NULL,NULL,NULL,8,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(32,3,15,'point',NULL,NULL,NULL,8,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(33,3,12,'point',NULL,NULL,NULL,8,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(34,3,1,'point',NULL,NULL,NULL,8,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(35,3,13,'point',NULL,NULL,NULL,8,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(36,3,14,'point',NULL,NULL,NULL,8,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(37,6,11,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(38,6,15,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(39,6,12,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(40,6,1,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(41,6,13,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(42,6,14,'point',NULL,NULL,NULL,2,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi penugasan v1'),
(64,2,8,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(65,3,8,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(66,5,8,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(67,4,8,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(68,2,9,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(69,3,9,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(70,5,9,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(71,4,9,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(72,2,21,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(73,3,21,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(74,5,21,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(75,4,21,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(76,2,10,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(77,3,10,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(78,5,10,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(79,4,10,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(80,2,6,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(81,3,6,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(82,5,6,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(83,4,6,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(84,2,3,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(85,3,3,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(86,5,3,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(87,4,3,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(88,2,11,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(89,3,11,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(90,5,11,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(91,4,11,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(92,2,15,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(93,3,15,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(94,5,15,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(95,4,15,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(96,2,20,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(97,3,20,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(98,5,20,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(99,4,20,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(100,2,17,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(101,3,17,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(102,5,17,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(103,4,17,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(104,2,12,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(105,3,12,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(106,5,12,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(107,4,12,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(108,2,7,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(109,3,7,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(110,5,7,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(111,4,7,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(112,2,2,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(113,3,2,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(114,5,2,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(115,4,2,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(116,2,22,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(117,3,22,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(118,5,22,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin'),
(119,4,22,'organization',1,NULL,NULL,NULL,'2026-01-01 00:00:00',NULL,NULL,NULL,NULL,'Migrasi cakupan internal; tinjau oleh admin');
/*!40000 ALTER TABLE `user_data_scopes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permission_overrides`
--

DROP TABLE IF EXISTS `user_permission_overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_permission_overrides` (
  `user_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `effect` enum('allow','deny') NOT NULL,
  `changed_by` bigint(20) unsigned NOT NULL,
  `reason` varchar(500) NOT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `user_permission_overrides_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_permission_overrides_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`),
  CONSTRAINT `user_permission_overrides_ibfk_3` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permission_overrides`
--

LOCK TABLES `user_permission_overrides` WRITE;
/*!40000 ALTER TABLE `user_permission_overrides` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_permission_overrides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES
(1,1),
(2,2),
(3,2),
(4,3),
(5,4),
(6,5);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organization_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_demo` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_internal` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `organization_id` (`organization_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,1,'Administrator Dinas','admin@pantaupangan.go.id','$2y$12$mjV2UManzpCceDBhuoKvTOc.e.InqfZNxYXbye2c3D2PjYxdV7vjy','081234567890','198501012010011001',1,0,NULL,'2026-10-28 20:57:56','2026-10-28 14:17:41',1),
(2,1,'Operator Lapangan A (Karang Tinggi)','operator@pantaupangan.go.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','081234567891','199203152015021002',1,0,NULL,'2026-10-28 20:57:56','2026-10-28 20:57:56',1),
(3,1,'Operator Lapangan B (Taba Penanjung)','operator2@pantaupangan.go.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','081234567892','199407202018011003',1,0,NULL,'2026-10-28 20:57:56','2026-10-28 20:57:56',1),
(4,1,'Verifikator Disperindag','verifikator@pantaupangan.go.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','081234567893','198005122005011004',1,0,NULL,'2026-10-28 20:57:56','2026-10-28 20:57:56',1),
(5,1,'Kepala Dinas / Pimpinan','pimpinan@pantaupangan.go.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','081234567894','197509181999031005',1,0,NULL,'2026-10-28 20:57:56','2026-10-28 20:57:56',1),
(6,NULL,'Mitra Usaha Sembako Berkah','mitra@pantaupangan.go.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','081234567895',NULL,1,0,NULL,'2026-10-28 20:57:56','2026-10-28 20:57:56',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `v_effective_user_permissions`
--

DROP TABLE IF EXISTS `v_effective_user_permissions`;
/*!50001 DROP VIEW IF EXISTS `v_effective_user_permissions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_effective_user_permissions` AS SELECT
 1 AS `user_id`,
  1 AS `permission_id`,
  1 AS `code` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_region_ancestors`
--

DROP TABLE IF EXISTS `v_region_ancestors`;
/*!50001 DROP VIEW IF EXISTS `v_region_ancestors`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_region_ancestors` AS SELECT
 1 AS `descendant_id`,
  1 AS `ancestor_id` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_user_visible_menus`
--

DROP TABLE IF EXISTS `v_user_visible_menus`;
/*!50001 DROP VIEW IF EXISTS `v_user_visible_menus`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `v_user_visible_menus` AS SELECT
 1 AS `user_id`,
  1 AS `menu_id`,
  1 AS `code`,
  1 AS `name`,
  1 AS `sort_order` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `verification_events`
--

DROP TABLE IF EXISTS `verification_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `verification_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) unsigned NOT NULL,
  `actor_id` bigint(20) unsigned NOT NULL,
  `decision` varchar(20) NOT NULL,
  `note` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `verification_events_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE,
  CONSTRAINT `verification_events_ibfk_2` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `verification_events`
--

LOCK TABLES `verification_events` WRITE;
/*!40000 ALTER TABLE `verification_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `verification_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'pantau_pangan'
--

--
-- Dumping routines for database 'pantau_pangan'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_can_point` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_can_point`(p_user BIGINT UNSIGNED,p_code VARCHAR(80),p_point BIGINT UNSIGNED) RETURNS tinyint(1)
    READS SQL DATA
    SQL SECURITY INVOKER
BEGIN
 IF NOT fn_has_permission(p_user,p_code) THEN RETURN FALSE; END IF;
 RETURN EXISTS(SELECT 1 FROM user_data_scopes s JOIN permissions perm ON perm.id=s.permission_id
 JOIN users u ON u.id=s.user_id JOIN organizations org ON org.id=u.organization_id
 JOIN monitoring_points pt ON pt.id=p_point
 WHERE s.user_id=p_user AND perm.code=p_code AND s.revoked_at IS NULL
 AND s.valid_from<=CURRENT_TIMESTAMP AND (s.valid_until IS NULL OR s.valid_until>CURRENT_TIMESTAMP)
 AND EXISTS(SELECT 1 FROM v_region_ancestors WHERE descendant_id=pt.region_id AND ancestor_id=org.region_id)
 AND ((s.scope_type='organization' AND s.organization_id=u.organization_id)
 OR (s.scope_type='region' AND EXISTS(SELECT 1 FROM v_region_ancestors WHERE descendant_id=pt.region_id AND ancestor_id=s.region_id))
 OR (s.scope_type='market' AND s.market_id=pt.market_id)
 OR (s.scope_type='point' AND s.point_id=pt.id)));
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_has_permission` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_has_permission`(p_user BIGINT UNSIGNED,p_code VARCHAR(80)) RETURNS tinyint(1)
    READS SQL DATA
    SQL SECURITY INVOKER
BEGIN
 RETURN EXISTS(SELECT 1 FROM v_effective_user_permissions WHERE user_id=p_user AND code=p_code);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_decide_policy_case` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_decide_policy_case`(IN p_case BIGINT UNSIGNED,IN p_actor BIGINT UNSIGNED,IN p_decision VARCHAR(20),IN p_note TEXT)
    SQL SECURITY INVOKER
BEGIN
 DECLARE v_status VARCHAR(20); DECLARE v_point BIGINT UNSIGNED; DECLARE v_outcome VARCHAR(30);
 DECLARE v_mode VARCHAR(10); DECLARE v_demo BOOLEAN; DECLARE v_demo_user BOOLEAN; DECLARE v_requester BIGINT UNSIGNED;
 DECLARE v_version_state VARCHAR(20);
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION;
 SELECT c.status,e.point_id,e.outcome,r.mode,e.is_demo,c.requested_by,v.state INTO v_status,v_point,v_outcome,v_mode,v_demo,v_requester,v_version_state
 FROM policy_cases c JOIN policy_evaluation_results e ON e.id=c.result_id JOIN policy_evaluation_runs r ON r.id=e.run_id JOIN policy_versions v ON v.id=r.version_id
 WHERE c.id=p_case FOR UPDATE;
 IF v_status IS NULL OR v_status<>'pending' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Pending case required'; END IF;
 IF NOT fn_can_point(p_actor,'policy.approve',v_point) OR p_actor=v_requester OR NOT EXISTS(SELECT 1 FROM users WHERE id=p_actor AND is_internal=TRUE) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Approval scope denied or self approval'; END IF;
 IF p_decision IS NULL OR p_decision NOT IN ('approved','rejected') OR p_note IS NULL OR CHAR_LENGTH(TRIM(p_note))=0 THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Decision and note required'; END IF;
 IF p_decision='approved' AND (v_outcome<>'matched' OR v_mode<>'live' OR v_version_state<>'published') THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Only matched live evaluation can be approved'; END IF;
 SELECT is_demo INTO v_demo_user FROM users WHERE id=p_actor;
 IF v_demo<>v_demo_user THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Demo and production must remain separate'; END IF;
 UPDATE policy_cases SET status=p_decision,decided_by=p_actor,decided_at=CURRENT_TIMESTAMP,decision_note=p_note WHERE id=p_case;
 INSERT INTO policy_case_events(case_id,actor_id,event_type,note) VALUES(p_case,p_actor,p_decision,p_note);
 COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_verify_report` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_verify_report`(IN p_report BIGINT UNSIGNED,IN p_actor BIGINT UNSIGNED,IN p_decision VARCHAR(20),IN p_note TEXT)
    SQL SECURITY INVOKER
BEGIN
 DECLARE v_series BIGINT UNSIGNED; DECLARE v_lock BIGINT UNSIGNED; DECLARE v_author BIGINT UNSIGNED;
 DECLARE v_prev BIGINT UNSIGNED; DECLARE v_current BIGINT UNSIGNED; DECLARE v_version INT; DECLARE v_demo BOOLEAN;
 DECLARE v_status VARCHAR(20); DECLARE v_point BIGINT UNSIGNED; DECLARE v_observed DATETIME;
 DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
 START TRANSACTION;
 IF NOT fn_has_permission(p_actor,'report.verify') OR NOT EXISTS(SELECT 1 FROM users WHERE id=p_actor AND is_internal=TRUE) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Internal user with verify permission required'; END IF;
 IF p_decision IS NULL OR p_decision NOT IN ('approved','returned','rejected') OR p_note IS NULL OR CHAR_LENGTH(TRIM(p_note))=0 THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Decision and note required'; END IF;
 SELECT series_id INTO v_series FROM reports WHERE id=p_report;
 IF v_series IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Report not found'; END IF;
 SELECT id,point_id,observed_at INTO v_lock,v_point,v_observed FROM report_series WHERE id=v_series FOR UPDATE;
 SELECT status,submitted_by,previous_report_id,version_no,is_demo INTO v_status,v_author,v_prev,v_version,v_demo FROM reports WHERE id=p_report FOR UPDATE;
 IF NOT fn_can_point(p_actor,'report.verify',v_point) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Verification target outside scope'; END IF;
 IF v_status<>'submitted' OR v_author=p_actor THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Report not submitted or self approval forbidden'; END IF;
 IF p_decision='approved' THEN
 IF NOT fn_can_point(v_author,'report.create',v_point) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Author input permission or point scope revoked'; END IF;
 IF NOT EXISTS(SELECT 1 FROM report_items WHERE report_id=p_report) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Empty report'; END IF;
 IF v_demo=FALSE AND EXISTS(SELECT 1 FROM reports r JOIN data_sources ds ON ds.id=r.source_id JOIN report_series s ON s.id=r.series_id JOIN monitoring_points p ON p.id=s.point_id
 WHERE r.id=p_report AND (ds.source_type='simulation' OR p.is_demo=TRUE)) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Simulation cannot become official data'; END IF;
 SET v_current=NULL;
 SELECT id INTO v_current FROM reports WHERE series_id=v_series AND status='approved' FOR UPDATE;
 IF v_current IS NULL AND (v_prev IS NOT NULL OR v_version<>1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Initial approved version must be version 1'; END IF;
 IF v_current IS NOT NULL THEN
 IF v_prev IS NULL OR v_prev<>v_current OR NOT EXISTS(SELECT 1 FROM reports WHERE id=v_current AND version_no=v_version-1 AND is_demo=v_demo) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Revision must reference current approved version'; END IF;
 IF NOT fn_can_point(v_author,'report.revise',v_point) OR NOT EXISTS(SELECT 1 FROM users WHERE id=v_author AND is_internal=TRUE) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Only internal operator proposes official revisions'; END IF;
 IF EXISTS(SELECT 1 FROM report_items oldi WHERE oldi.report_id=v_current AND NOT EXISTS(SELECT 1 FROM report_items newi WHERE newi.report_id=p_report AND newi.commodity_id=oldi.commodity_id)) THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Revision must include full previous commodity set'; END IF;
 UPDATE reports SET status='superseded' WHERE id=v_current;
 END IF;
 END IF;
 UPDATE reports SET status=p_decision,verified_by=IF(p_decision='approved',p_actor,NULL),verified_at=IF(p_decision='approved',CURRENT_TIMESTAMP,NULL) WHERE id=p_report;
 INSERT INTO verification_events(report_id,actor_id,decision,note) VALUES(p_report,p_actor,p_decision,p_note);
 INSERT INTO audit_logs(actor_id,entity_type,entity_id,action,old_values,new_values)
 VALUES(p_actor,'reports',p_report,p_decision,JSON_OBJECT('status',v_status),JSON_OBJECT('status',p_decision));
 COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `v_effective_user_permissions`
--

/*!50001 DROP VIEW IF EXISTS `v_effective_user_permissions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_effective_user_permissions` AS select distinct `u`.`id` AS `user_id`,`p`.`id` AS `permission_id`,`p`.`code` AS `code` from (`users` `u` join `permissions` `p`) where `u`.`is_active` = 1 and !exists(select 1 from `user_permission_overrides` `o` where `o`.`user_id` = `u`.`id` and `o`.`permission_id` = `p`.`id` and `o`.`effect` = 'deny' limit 1) and (exists(select 1 from (`user_roles` `ur` join `role_permissions` `rp` on(`rp`.`role_id` = `ur`.`role_id`)) where `ur`.`user_id` = `u`.`id` and `rp`.`permission_id` = `p`.`id` limit 1) or exists(select 1 from `user_permission_overrides` `o` where `o`.`user_id` = `u`.`id` and `o`.`permission_id` = `p`.`id` and `o`.`effect` = 'allow' limit 1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_region_ancestors`
--

/*!50001 DROP VIEW IF EXISTS `v_region_ancestors`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_region_ancestors` AS with recursive tree as (select `regions`.`id` AS `descendant_id`,`regions`.`id` AS `ancestor_id`,`regions`.`parent_id` AS `parent_id`,0 AS `depth` from `regions` union all select `t`.`descendant_id` AS `descendant_id`,`r`.`id` AS `id`,`r`.`parent_id` AS `parent_id`,`t`.`depth` + 1 AS `t.depth+1` from (`tree` `t` join `regions` `r` on(`r`.`id` = `t`.`parent_id`)) where `t`.`depth` < 16)select `tree`.`descendant_id` AS `descendant_id`,`tree`.`ancestor_id` AS `ancestor_id` from `tree` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_user_visible_menus`
--

/*!50001 DROP VIEW IF EXISTS `v_user_visible_menus`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_user_visible_menus` AS select distinct `e`.`user_id` AS `user_id`,`m`.`id` AS `menu_id`,`m`.`code` AS `code`,`m`.`name` AS `name`,`m`.`sort_order` AS `sort_order` from ((`v_effective_user_permissions` `e` join `menu_permissions` `mp` on(`mp`.`permission_id` = `e`.`permission_id`)) join `app_menus` `m` on(`m`.`id` = `mp`.`menu_id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-10-29  7:29:35
