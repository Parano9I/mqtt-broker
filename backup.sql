-- MySQL dump 10.13  Distrib 8.0.32, for Linux (x86_64)
--
-- Host: localhost    Database: mqtt-broker
-- ------------------------------------------------------
-- Server version	8.0.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'KrNU','Kremenchuk National University named after Mykhaylo Ostrogradskiy (KrNU) is a state non-commercial educational institution. KrNU is located in the city of Kremenchug, Ukraine.','2023-04-29 09:52:50','2023-04-29 09:52:50'),(2,'KhNU','Kharkiv National University V N Karazin (KhNU) is a state non-commercial educational institution. KhNU is located in the city of Kharkiv, Ukraine. KhNU is a member of the European University Association (EUA).','2023-04-29 09:52:50','2023-04-29 09:52:50');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups_users`
--

DROP TABLE IF EXISTS `groups_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups_users` (
  `user_id` bigint unsigned NOT NULL,
  `group_id` bigint unsigned NOT NULL,
  `role_id` smallint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `groups_users_group_id_foreign` (`group_id`),
  CONSTRAINT `groups_users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `groups_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups_users`
--

LOCK TABLES `groups_users` WRITE;
/*!40000 ALTER TABLE `groups_users` DISABLE KEYS */;
INSERT INTO `groups_users` VALUES (1,2,0,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(2,1,0,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(3,2,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(4,1,2,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(5,1,2,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(6,2,2,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(7,1,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(8,1,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(9,2,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(10,1,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(11,2,2,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(12,1,2,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(13,2,2,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(14,1,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(15,1,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(16,1,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(17,2,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(18,2,2,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(19,1,2,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(20,1,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(21,1,2,'2023-04-29 09:52:50','2023-04-29 09:52:50');
/*!40000 ALTER TABLE `groups_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2023_02_15_081818_add_role_to_users_table',1),(6,'2023_02_16_103610_create_organizations_table',1),(7,'2023_02_16_132227_create_groups_table',1),(8,'2023_02_16_132908_create_groups_users_table',1),(9,'2023_03_01_131430_create_topics_table',1),(10,'2023_03_06_103544_create_sensors_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizations`
--

LOCK TABLES `organizations` WRITE;
/*!40000 ALTER TABLE `organizations` DISABLE KEYS */;
INSERT INTO `organizations` VALUES (1,'Kris and Sons','Officiis aut ut enim voluptatem minima sit. Nobis ut occaecati illum non nam. Pariatur voluptatem occaecati iure ratione architecto aspernatur quis. Sequi occaecati itaque dolores.','2023-04-29 09:52:50','2023-04-29 09:52:50');
/*!40000 ALTER TABLE `organizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'access_token','584b6793f73d041d7c422a4a4871820dce74017684ab1ea5555cd6ac9a12d234','[\"*\"]','2023-04-29 09:53:28',NULL,'2023-04-29 09:53:15','2023-04-29 09:53:28'),(2,'App\\Models\\User',1,'access_token','806015fa35e591459e1cd95ecd02b7a39fb3db67bc905776730ae1680ed827b5','[\"*\"]','2023-04-29 10:49:37',NULL,'2023-04-29 10:40:47','2023-04-29 10:49:37'),(3,'App\\Models\\User',1,'access_token','57028167dba8550a7080806f2d0d799007856c5b15cbe8bfaad64624861f3172','[\"*\"]','2023-04-29 10:56:52',NULL,'2023-04-29 10:50:17','2023-04-29 10:56:52'),(4,'App\\Models\\User',1,'access_token','349b5b0e8a7a0e1ecfb7b01a7d9abef4eb8dc99bb82afe6eb4fcdbc4faf929f5','[\"*\"]','2023-04-29 13:32:45',NULL,'2023-04-29 11:12:26','2023-04-29 13:32:45'),(5,'App\\Models\\User',1,'access_token','1e58ccb44c0ad82cef703c8c460d8bab2762570b4177dd645e73bd29f7501e4a','[\"*\"]','2023-04-30 05:24:28',NULL,'2023-04-30 04:53:26','2023-04-30 05:24:28'),(6,'App\\Models\\User',1,'access_token','ed8930021e7a8a7928e9ad72e024e41b59a52086eaf619e15833c664d0a6ed07','[\"*\"]','2023-04-30 05:27:27',NULL,'2023-04-30 05:24:47','2023-04-30 05:27:27'),(7,'App\\Models\\User',1,'access_token','c680b2775c2b6c3129216f505140b5db4ac5ee68c9319bd6cfbc69cbca9b0f3c','[\"*\"]','2023-04-30 08:45:11',NULL,'2023-04-30 05:43:25','2023-04-30 08:45:11');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensors`
--

DROP TABLE IF EXISTS `sensors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sensors` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('online','offline') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offline',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `sensors_name_topic_id_unique` (`name`,`topic_id`),
  KEY `sensors_topic_id_foreign` (`topic_id`),
  CONSTRAINT `sensors_topic_id_foreign` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensors`
--

LOCK TABLES `sensors` WRITE;
/*!40000 ALTER TABLE `sensors` DISABLE KEYS */;
INSERT INTO `sensors` VALUES ('P28MqtchqJWhQFccg73c85',8,'air','A sensor that measures the quality of the air (e.g. humidity, air pollutants)','008dabf4dde55edc7c2d68ed87fad4d8962dd41b309146a3b662023b2f1ed199','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('J1zby3zgDxYUekgmXcHnWV',15,'air','A sensor that measures the quality of the air (e.g. humidity, air pollutants)','9a93371d610cc78d5f98fb062b971be87c18e55b4aa1effdde31cf3bf3b9e807','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('SNx9yURRkP2er1hdjmwjWn',2,'light','A sensor that measures the amount of light in lux','6a4e550ce0396ceea0fd2cc8eb82f9298e792b12fc1dc8b9efbc7937dd98200e','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('C8i4ty5aRGxkxb6GPivoSX',5,'light','A sensor that measures the amount of light in lux','cc3c028b41634824fe681e1f8ce182693d4348db1db2224c069f925056ec79b4','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('XTfz9UJBRFBVMSkECj7nA6',6,'light','A sensor that measures the amount of light in lux','43f6541910dbe9b650909b8e2884adabf8e0fa51c77f9243461ea22752b97590','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('2wwMYtZhJUDVyzdEf5UMwW',16,'light','A sensor that measures the amount of light in lux','8ea0ed5dccf8cc2e90de68d13faa7cc5ae40b960e1d745d3b4a460963413ff66','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('YKbfrFKuUCnqcjnzwU1NuK',15,'pressure','A sensor that measures the air pressure in kilopascals','cf3c0e2da6bcfb1d0602f4e739ceffbe33f629cc6c81deeed05f974ccc801825','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('RAYZZwUYNvPB5J2sdoNyWg',4,'temperature','A sensor that measures the temperature in Celsius degrees','cccfbdcd9588448802eda42c73fd5109bddf7335c94fb2b66fab75786fd99b14','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('ESn4tCi7FYEwdHH2oQdZHs',8,'temperature','A sensor that measures the temperature in Celsius degrees','f9926a6cab12f78870eb6960177f57ada8a0a7d086e81b5579714e7ac08bd6ce','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('QW3Vo8X7pdDiauVm2vzsse',12,'temperature','A sensor that measures the temperature in Celsius degrees','0b61d82ddee49ab2789e19f677a3b06e15821e9a2a9075907ef4e810cd1addd2','offline','2023-04-29 09:52:51','2023-04-29 09:52:51'),('RfWJxLSGJ7uhXFaXK4o2ih',14,'temperature','A sensor that measures the temperature in Celsius degrees','549325c289b073f1e52e32e8879a14c6ea872772358b478a72db8b91a899fa1e','offline','2023-04-29 09:52:51','2023-04-29 09:52:51');
/*!40000 ALTER TABLE `sensors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `topics`
--

DROP TABLE IF EXISTS `topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `topics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` bigint unsigned DEFAULT NULL,
  `_lft` int unsigned NOT NULL DEFAULT '0',
  `_rgt` int unsigned NOT NULL DEFAULT '0',
  `parent_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `topics_name_group_id_parent_id_unique` (`name`,`group_id`,`parent_id`),
  KEY `topics__lft__rgt_parent_id_index` (`_lft`,`_rgt`,`parent_id`),
  KEY `topics_group_id_foreign` (`group_id`),
  CONSTRAINT `topics_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `topics`
--

LOCK TABLES `topics` WRITE;
/*!40000 ALTER TABLE `topics` DISABLE KEYS */;
INSERT INTO `topics` VALUES (1,'Building 1',2,1,16,NULL,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(2,'First floor',NULL,2,7,1,'2023-04-29 09:52:50','2023-04-29 09:52:50'),(3,'1115',NULL,3,4,2,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(4,'1123',NULL,5,6,2,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(5,'Second floor',NULL,8,15,1,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(6,'1223',NULL,9,10,5,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(7,'1208',NULL,11,12,5,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(8,'1210',NULL,13,14,5,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(9,'Building 2',1,17,32,NULL,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(10,'First floor',NULL,18,23,9,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(11,'2101',NULL,19,20,10,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(12,'2112',NULL,21,22,10,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(13,'Second floor',NULL,24,31,9,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(14,'2205',NULL,25,26,13,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(15,'2239',NULL,27,28,13,'2023-04-29 09:52:51','2023-04-29 09:52:51'),(16,'2274',NULL,29,30,13,'2023-04-29 09:52:51','2023-04-29 09:52:51');
/*!40000 ALTER TABLE `topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Prof. Leola Purdy I','murray.gerson@example.net','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','TtsxNJVJFI','2023-04-29 09:52:50','2023-04-29 09:52:50','owner'),(2,'Mrs. Dorris Lebsack Sr.','janessa02@example.org','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','gRnmtaAKav','2023-04-29 09:52:50','2023-04-29 09:52:50','common'),(3,'April Bahringer PhD','alvina37@example.com','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','lscLtsX7ve','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(4,'Jonathon Hoeger','sdibbert@example.net','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','JNqnyH9FVD','2023-04-29 09:52:50','2023-04-29 09:52:50','common'),(5,'Adela Hackett','hgleason@example.com','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','P7adyknUKQ','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(6,'Mr. Rogers Feeney','orpha.shields@example.net','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','SpZglvAGnp','2023-04-29 09:52:50','2023-04-29 09:52:50','common'),(7,'Margarita Brown II','cloyd95@example.org','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','0fnO5fo0iT','2023-04-29 09:52:50','2023-04-29 09:52:50','common'),(8,'Delaney Simonis','pagac.bertram@example.com','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Zu7qsybOxI','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(9,'Dr. Rodrigo Wolf Sr.','waelchi.stephanie@example.net','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','iRpwOy0PTD','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(10,'Maryam Heidenreich','rmosciski@example.com','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','eYzcYhrrAb','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(11,'Miss Destiney Jacobson','armani95@example.org','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','twFCDapy8f','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(12,'Katelynn Weber','hanna27@example.net','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','RTb5poB870','2023-04-29 09:52:50','2023-04-29 09:52:50','common'),(13,'Gloria Lynch','joaquin66@example.com','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','sq9cY76D8S','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(14,'Pierce Keebler Jr.','mosciski.sydni@example.net','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','TtmAxdbxlb','2023-04-29 09:52:50','2023-04-29 09:52:50','common'),(15,'Elna Beer I','kfriesen@example.org','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','kVCkjRPQ7i','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(16,'Kiana Turcotte','tristian.pfeffer@example.org','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','di7fFylsBz','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(17,'Buster Denesik','aufderhar.alayna@example.com','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','kZgu8O4J3C','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(18,'Justice Leffler','freddy.casper@example.org','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','knhWz4nl8c','2023-04-29 09:52:50','2023-04-29 09:52:50','common'),(19,'Isabella Jones I','beier.jo@example.net','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','uyHURtMhn4','2023-04-29 09:52:50','2023-04-29 09:52:50','common'),(20,'Dr. Johnpaul Bashirian','mary72@example.org','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','VZ7VYdw7lN','2023-04-29 09:52:50','2023-04-29 09:52:50','admin'),(21,'Richmond Ward','joel66@example.com','2023-04-29 09:52:50','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','1GOBlMeIgC','2023-04-29 09:52:50','2023-04-29 09:52:50','common');
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

-- Dump completed on 2023-04-30 10:03:16
