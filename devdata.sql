-- MySQL dump 10.13  Distrib 9.6.0, for Win64 (x86_64)
--
-- Host: localhost    Database: wholesale_bill_app
-- ------------------------------------------------------
-- Server version	9.6.0

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
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Himalaya',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(2,'Dabur',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(3,'Patanjali',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(4,'Colgate',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(5,'Parle',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(6,'Britannia',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(7,'Emami',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(8,'Marico',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(9,'Lifebuoy',1,'2026-07-14 03:12:31','2026-07-14 03:12:31'),(10,'Clinic Plus',1,'2026-07-14 03:12:31','2026-07-14 03:12:31'),(11,'Nestle',1,'2026-07-14 03:12:31','2026-07-14 03:12:31');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('wholesalebillapp-cache-5c785c036466adea360111aa28563bfd556b5fba','i:4;',1784276744),('wholesalebillapp-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1784276744;',1784276744);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Face Wash',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(2,'Soap',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(3,'Shampoo',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(4,'Skin Care',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(5,'Hair Oil',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(6,'Toothpaste',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(7,'Health',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(8,'Oral Care',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(9,'Biscuits',1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(10,'Snacks',1,'2026-07-14 03:12:31','2026-07-14 03:12:31');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_note_lines`
--

DROP TABLE IF EXISTS `credit_note_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_note_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `credit_note_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `qty` int unsigned NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_note_lines_credit_note_id_foreign` (`credit_note_id`),
  KEY `credit_note_lines_product_id_foreign` (`product_id`),
  CONSTRAINT `credit_note_lines_credit_note_id_foreign` FOREIGN KEY (`credit_note_id`) REFERENCES `credit_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_note_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_note_lines`
--

LOCK TABLES `credit_note_lines` WRITE;
/*!40000 ALTER TABLE `credit_note_lines` DISABLE KEYS */;
INSERT INTO `credit_note_lines` VALUES (1,1,15,'Parle-G Gold 500g','Parle','Biscuits',60.00,15,45.60,684.00,'2026-07-15 04:20:25','2026-07-15 04:20:25');
/*!40000 ALTER TABLE `credit_note_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_notes`
--

DROP TABLE IF EXISTS `credit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cn_no` int unsigned NOT NULL,
  `partner_id` bigint unsigned NOT NULL,
  `cn_date` date NOT NULL,
  `kind` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `credit_notes_cn_no_unique` (`cn_no`),
  KEY `credit_notes_partner_id_foreign` (`partner_id`),
  CONSTRAINT `credit_notes_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_notes`
--

LOCK TABLES `credit_notes` WRITE;
/*!40000 ALTER TABLE `credit_notes` DISABLE KEYS */;
INSERT INTO `credit_notes` VALUES (1,1,1,'2026-07-15','goods','damaged good returned',684.00,'2026-07-15 04:20:25','2026-07-15 04:20:25'),(2,2,1,'2026-07-15','amount','advance',10000.00,'2026-07-15 04:32:31','2026-07-15 04:32:31'),(3,3,1,'2025-09-14','amount','Rate difference on Sept supply',350.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(4,4,2,'2026-01-20','amount','Damaged goods settlement',780.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(5,5,2,'2026-05-11','amount','Scheme adjustment',500.00,'2026-07-15 23:58:04','2026-07-15 23:58:04');
/*!40000 ALTER TABLE `credit_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `held_bills`
--

DROP TABLE IF EXISTS `held_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `held_bills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `partner_id` bigint unsigned NOT NULL,
  `payload` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `held_bills_partner_id_foreign` (`partner_id`),
  CONSTRAINT `held_bills_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `held_bills`
--

LOCK TABLES `held_bills` WRITE;
/*!40000 ALTER TABLE `held_bills` DISABLE KEYS */;
INSERT INTO `held_bills` VALUES (4,1,'{\"lines\": [{\"mrp\": 35, \"qty\": 12, \"name\": \"Britannia Bourbon 150g\", \"rate\": 29.47, \"brand\": \"Britannia\", \"category\": \"Biscuits\", \"free_qty\": 0, \"product_id\": 20, \"manual_free\": true, \"manual_rate\": true, \"scheme_percent\": 4}, {\"mrp\": 60, \"qty\": 12, \"name\": \"Parle-G Gold 500g\", \"rate\": 45.6, \"brand\": \"Parle\", \"category\": \"Biscuits\", \"free_qty\": 1, \"product_id\": 15, \"manual_free\": false, \"manual_rate\": false, \"scheme_percent\": 5}, {\"mrp\": 40, \"qty\": 3, \"name\": \"Britannia Marie Gold 250g\", \"rate\": 35.09, \"brand\": \"Britannia\", \"category\": \"Biscuits\", \"free_qty\": 0, \"product_id\": 19, \"manual_free\": true, \"manual_rate\": true, \"scheme_percent\": 0}, {\"mrp\": 122, \"qty\": 3, \"name\": \"Colgate Strong Teeth 200g\", \"rate\": 112.96, \"brand\": \"Colgate\", \"category\": \"Toothpaste\", \"free_qty\": 0, \"product_id\": 12, \"manual_free\": true, \"manual_rate\": true, \"scheme_percent\": 0}]}','2026-07-14 10:28:57','2026-07-14 10:28:57');
/*!40000 ALTER TABLE `held_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_lines`
--

DROP TABLE IF EXISTS `invoice_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hsn_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `qty` int unsigned NOT NULL,
  `free_qty` int unsigned NOT NULL DEFAULT '0',
  `rate` decimal(10,2) NOT NULL,
  `scheme_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_inclusive` tinyint(1) NOT NULL DEFAULT '1',
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_lines_invoice_id_foreign` (`invoice_id`),
  KEY `invoice_lines_product_id_foreign` (`product_id`),
  CONSTRAINT `invoice_lines_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_lines`
--

LOCK TABLES `invoice_lines` WRITE;
/*!40000 ALTER TABLE `invoice_lines` DISABLE KEYS */;
INSERT INTO `invoice_lines` VALUES (1,1,15,'Parle-G Gold 500g','Parle','Biscuits','1905',60.00,24,2,45.60,5.00,18.00,1,1094.40,'2026-07-14 11:56:06','2026-07-14 11:56:06'),(2,1,19,'Britannia Marie Gold 250g','Britannia','Biscuits','1905',40.00,12,0,37.04,0.00,18.00,1,444.48,'2026-07-14 11:56:06','2026-07-14 11:56:06'),(3,2,28,'Clinic Plus Strong Shampoo 175ml','Clinic Plus','Shampoo','3305',115.00,3,0,104.55,0.00,18.00,1,313.65,'2026-07-14 21:04:53','2026-07-14 21:04:53'),(4,3,15,'Parle-G Gold 500g','Parle','Biscuits','1905',60.00,12,1,45.60,5.00,18.00,1,547.20,'2026-07-15 02:32:11','2026-07-15 02:32:11'),(5,4,15,'Parle-G Gold 500g','Parle','Biscuits','1905',60.00,170,14,45.60,5.00,18.00,1,7752.00,'2026-07-15 03:01:52','2026-07-15 03:01:52'),(6,5,18,'Britannia Good Day Cashew 200g','Britannia','Biscuits','1905',45.00,10,0,40.00,0.00,18.00,0,472.00,'2026-07-15 21:54:23','2026-07-15 21:54:23'),(7,6,4,'Himalaya Baby Powder 200g','Himalaya','Skin Care','3304',145.00,48,0,108.75,0.00,18.00,1,5220.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(8,6,10,'Patanjali Dant Kanti Toothpaste 200g','Patanjali','Toothpaste','3306',100.00,24,0,75.00,0.00,12.00,1,1800.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(9,6,16,'Parle Monaco Classic 200g','Parle','Biscuits','1905',40.00,12,0,30.00,0.00,18.00,1,360.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(10,6,19,'Britannia Marie Gold 250g','Britannia','Biscuits','1905',40.00,12,0,30.00,0.00,18.00,1,360.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(11,6,21,'Emami BoroPlus Cream 80ml','Emami','Skin Care','3304',105.00,48,0,78.75,0.00,18.00,1,3780.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(12,7,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo','3305',130.00,48,0,97.50,0.00,18.00,1,4680.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(13,7,4,'Himalaya Baby Powder 200g','Himalaya','Skin Care','3304',145.00,6,0,108.75,0.00,18.00,1,652.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(14,7,5,'Dabur Amla Hair Oil 275ml','Dabur','Hair Oil','3305',199.00,6,0,149.25,0.00,18.00,1,895.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(15,7,16,'Parle Monaco Classic 200g','Parle','Biscuits','1905',40.00,12,0,30.00,0.00,18.00,1,360.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(16,7,21,'Emami BoroPlus Cream 80ml','Emami','Skin Care','3304',105.00,24,0,78.75,0.00,18.00,1,1890.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(17,8,21,'Emami BoroPlus Cream 80ml','Emami','Skin Care','3304',105.00,12,0,78.75,0.00,18.00,1,945.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(18,8,24,'Hair & Care Damage Repair 200ml','Marico','Hair Oil','3305',120.00,24,0,90.00,0.00,18.00,1,2160.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(19,9,8,'Dabur Chyawanprash 500g','Dabur','Health','2106',215.00,24,0,161.25,0.00,12.00,1,3870.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(20,9,14,'Colgate ZigZag Toothbrush Medium','Colgate','Oral Care','9603',45.00,6,0,33.75,0.00,18.00,1,202.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(21,9,15,'Parle-G Gold 500g','Parle','Biscuits','1905',60.00,24,0,52.00,0.00,18.00,1,1248.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(22,9,16,'Parle Monaco Classic 200g','Parle','Biscuits','1905',40.00,12,0,30.00,0.00,18.00,1,360.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(23,9,24,'Hair & Care Damage Repair 200ml','Marico','Hair Oil','3305',120.00,24,0,90.00,0.00,18.00,1,2160.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(24,10,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo','3305',130.00,24,0,97.50,0.00,18.00,1,2340.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(25,10,8,'Dabur Chyawanprash 500g','Dabur','Health','2106',215.00,12,0,161.25,0.00,12.00,1,1935.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(26,11,1,'Himalaya Neem Face Wash 100ml','Himalaya','Face Wash','3304',165.00,24,0,123.75,0.00,18.00,1,2970.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(27,11,5,'Dabur Amla Hair Oil 275ml','Dabur','Hair Oil','3305',199.00,12,0,149.25,0.00,18.00,1,1791.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(28,11,10,'Patanjali Dant Kanti Toothpaste 200g','Patanjali','Toothpaste','3306',100.00,48,0,75.00,0.00,12.00,1,3600.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(29,11,17,'Parle Hide & Seek 200g','Parle','Biscuits','1905',50.00,48,0,37.50,0.00,18.00,1,1800.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(30,11,29,'Maggi Noodles 70g','Nestle','Snacks','1902',14.00,12,0,10.50,0.00,12.00,1,126.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(31,12,18,'Britannia Good Day Cashew 200g','Britannia','Biscuits','1905',45.00,12,0,33.75,0.00,18.00,0,477.90,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(32,12,21,'Emami BoroPlus Cream 80ml','Emami','Skin Care','3304',105.00,12,0,78.75,0.00,18.00,1,945.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(33,12,26,'Test Cream 50g','Dabur','Skin Care',NULL,99.00,24,0,74.25,0.00,18.00,1,1782.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(34,12,29,'Maggi Noodles 70g','Nestle','Snacks','1902',14.00,6,0,10.50,0.00,12.00,1,63.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(35,13,14,'Colgate ZigZag Toothbrush Medium','Colgate','Oral Care','9603',45.00,24,0,33.75,0.00,18.00,1,810.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(36,13,16,'Parle Monaco Classic 200g','Parle','Biscuits','1905',40.00,12,0,30.00,0.00,18.00,1,360.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(37,13,22,'Emami Navratna Oil 200ml','Emami','Hair Oil','3305',135.00,24,0,101.25,0.00,18.00,1,2430.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(38,14,7,'Dabur Honey 500g','Dabur','Health','0409',199.00,6,0,149.25,0.00,5.00,1,895.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(39,14,9,'Patanjali Kesh Kanti Hair Oil 120ml','Patanjali','Hair Oil','3305',130.00,24,0,97.50,0.00,18.00,1,2340.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(40,14,10,'Patanjali Dant Kanti Toothpaste 200g','Patanjali','Toothpaste','3306',100.00,6,0,75.00,0.00,12.00,1,450.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(41,14,26,'Test Cream 50g','Dabur','Skin Care',NULL,99.00,6,0,74.25,0.00,18.00,1,445.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(42,15,4,'Himalaya Baby Powder 200g','Himalaya','Skin Care','3304',145.00,24,0,108.75,0.00,18.00,1,2610.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(43,15,7,'Dabur Honey 500g','Dabur','Health','0409',199.00,24,0,149.25,0.00,5.00,1,3582.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(44,15,8,'Dabur Chyawanprash 500g','Dabur','Health','2106',215.00,48,0,161.25,0.00,12.00,1,7740.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(45,15,21,'Emami BoroPlus Cream 80ml','Emami','Skin Care','3304',105.00,6,0,78.75,0.00,18.00,1,472.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(46,15,24,'Hair & Care Damage Repair 200ml','Marico','Hair Oil','3305',120.00,48,0,90.00,0.00,18.00,1,4320.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(47,16,12,'Colgate Strong Teeth 200g','Colgate','Toothpaste','3306',122.00,12,0,91.50,0.00,18.00,1,1098.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(48,16,15,'Parle-G Gold 500g','Parle','Biscuits','1905',60.00,24,0,52.00,0.00,18.00,1,1248.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(49,16,22,'Emami Navratna Oil 200ml','Emami','Hair Oil','3305',135.00,24,0,101.25,0.00,18.00,1,2430.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(50,16,26,'Test Cream 50g','Dabur','Skin Care',NULL,99.00,12,0,74.25,0.00,18.00,1,891.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(51,16,29,'Maggi Noodles 70g','Nestle','Snacks','1902',14.00,12,0,10.50,0.00,12.00,1,126.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(52,17,12,'Colgate Strong Teeth 200g','Colgate','Toothpaste','3306',122.00,24,0,91.50,0.00,18.00,1,2196.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(53,17,20,'Britannia Bourbon 150g','Britannia','Biscuits','1905',35.00,6,0,26.25,0.00,18.00,1,157.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(54,18,13,'Colgate MaxFresh Blue 150g','Colgate','Toothpaste','3306',105.00,48,0,78.75,0.00,18.00,1,3780.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(55,18,17,'Parle Hide & Seek 200g','Parle','Biscuits','1905',50.00,6,0,37.50,0.00,18.00,1,225.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(56,19,13,'Colgate MaxFresh Blue 150g','Colgate','Toothpaste','3306',105.00,12,0,78.75,0.00,18.00,1,945.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(57,19,29,'Maggi Noodles 70g','Nestle','Snacks','1902',14.00,48,0,10.50,0.00,12.00,1,504.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(58,20,1,'Himalaya Neem Face Wash 100ml','Himalaya','Face Wash','3304',165.00,24,0,123.75,0.00,18.00,1,2970.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(59,20,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo','3305',130.00,6,0,97.50,0.00,18.00,1,585.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(60,20,13,'Colgate MaxFresh Blue 150g','Colgate','Toothpaste','3306',105.00,6,0,78.75,0.00,18.00,1,472.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(61,20,28,'Clinic Plus Strong Shampoo 175ml','Clinic Plus','Shampoo','3305',115.00,12,0,86.25,0.00,18.00,1,1035.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(62,20,29,'Maggi Noodles 70g','Nestle','Snacks','1902',14.00,24,0,10.50,0.00,12.00,1,252.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(63,21,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo','3305',130.00,48,0,97.50,0.00,18.00,1,4680.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(64,21,4,'Himalaya Baby Powder 200g','Himalaya','Skin Care','3304',145.00,24,0,108.75,0.00,18.00,1,2610.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(65,21,5,'Dabur Amla Hair Oil 275ml','Dabur','Hair Oil','3305',199.00,6,0,149.25,0.00,18.00,1,895.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(66,21,8,'Dabur Chyawanprash 500g','Dabur','Health','2106',215.00,12,0,161.25,0.00,12.00,1,1935.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(67,21,22,'Emami Navratna Oil 200ml','Emami','Hair Oil','3305',135.00,6,0,101.25,0.00,18.00,1,607.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(68,22,1,'Himalaya Neem Face Wash 100ml','Himalaya','Face Wash','3304',165.00,12,0,123.75,0.00,18.00,1,1485.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(69,22,22,'Emami Navratna Oil 200ml','Emami','Hair Oil','3305',135.00,12,0,101.25,0.00,18.00,1,1215.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(70,22,23,'Parachute Coconut Oil 250ml','Marico','Hair Oil','1513',146.00,24,0,109.50,0.00,5.00,1,2628.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(71,23,2,'Himalaya Purifying Neem Soap 125g','Himalaya','Soap','3401',45.00,12,0,33.75,0.00,18.00,1,405.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(72,23,13,'Colgate MaxFresh Blue 150g','Colgate','Toothpaste','3306',105.00,12,0,78.75,0.00,18.00,1,945.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(73,23,17,'Parle Hide & Seek 200g','Parle','Biscuits','1905',50.00,24,0,37.50,0.00,18.00,1,900.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(74,23,18,'Britannia Good Day Cashew 200g','Britannia','Biscuits','1905',45.00,12,0,33.75,0.00,18.00,0,477.90,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(75,24,6,'Dabur Red Toothpaste 200g','Dabur','Toothpaste','3306',145.00,24,0,108.75,0.00,12.00,1,2610.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(76,24,11,'Patanjali Aloe Vera Gel 150ml','Patanjali','Skin Care','3304',90.00,12,0,67.50,0.00,18.00,1,810.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(77,24,29,'Maggi Noodles 70g','Nestle','Snacks','1902',14.00,12,0,10.50,0.00,12.00,1,126.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(78,25,13,'Colgate MaxFresh Blue 150g','Colgate','Toothpaste','3306',105.00,24,0,78.75,0.00,18.00,1,1890.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(79,25,14,'Colgate ZigZag Toothbrush Medium','Colgate','Oral Care','9603',45.00,24,0,33.75,0.00,18.00,1,810.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(80,25,28,'Clinic Plus Strong Shampoo 175ml','Clinic Plus','Shampoo','3305',115.00,48,0,86.25,0.00,18.00,1,4140.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(81,26,1,'Himalaya Neem Face Wash 100ml','Himalaya','Face Wash','3304',165.00,24,0,123.75,0.00,18.00,1,2970.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(82,26,6,'Dabur Red Toothpaste 200g','Dabur','Toothpaste','3306',145.00,24,0,108.75,0.00,12.00,1,2610.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(83,26,21,'Emami BoroPlus Cream 80ml','Emami','Skin Care','3304',105.00,12,0,78.75,0.00,18.00,1,945.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(84,27,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo','3305',130.00,48,0,97.50,0.00,18.00,1,4680.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(85,27,6,'Dabur Red Toothpaste 200g','Dabur','Toothpaste','3306',145.00,48,0,108.75,0.00,12.00,1,5220.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(86,27,7,'Dabur Honey 500g','Dabur','Health','0409',199.00,48,0,149.25,0.00,5.00,1,7164.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(87,28,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo','3305',130.00,24,0,97.50,0.00,18.00,1,2340.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(88,28,9,'Patanjali Kesh Kanti Hair Oil 120ml','Patanjali','Hair Oil','3305',130.00,24,0,97.50,0.00,18.00,1,2340.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(89,28,20,'Britannia Bourbon 150g','Britannia','Biscuits','1905',35.00,48,0,26.25,0.00,18.00,1,1260.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(90,29,6,'Dabur Red Toothpaste 200g','Dabur','Toothpaste','3306',145.00,6,0,108.75,0.00,12.00,1,652.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(91,29,17,'Parle Hide & Seek 200g','Parle','Biscuits','1905',50.00,24,0,37.50,0.00,18.00,1,900.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(92,29,20,'Britannia Bourbon 150g','Britannia','Biscuits','1905',35.00,6,0,26.25,0.00,18.00,1,157.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(93,29,27,'Lifebuoy Total Soap 125g','Lifebuoy','Soap','3401',36.00,48,0,27.00,0.00,18.00,1,1296.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(94,30,5,'Dabur Amla Hair Oil 275ml','Dabur','Hair Oil','3305',199.00,24,0,149.25,0.00,18.00,1,3582.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(95,30,6,'Dabur Red Toothpaste 200g','Dabur','Toothpaste','3306',145.00,12,0,108.75,0.00,12.00,1,1305.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(96,30,13,'Colgate MaxFresh Blue 150g','Colgate','Toothpaste','3306',105.00,24,0,78.75,0.00,18.00,1,1890.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(97,30,28,'Clinic Plus Strong Shampoo 175ml','Clinic Plus','Shampoo','3305',115.00,48,0,86.25,0.00,18.00,1,4140.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(98,31,4,'Himalaya Baby Powder 200g','Himalaya','Skin Care','3304',145.00,48,0,108.75,0.00,18.00,1,5220.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(99,31,5,'Dabur Amla Hair Oil 275ml','Dabur','Hair Oil','3305',199.00,12,0,149.25,0.00,18.00,1,1791.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(100,31,15,'Parle-G Gold 500g','Parle','Biscuits','1905',60.00,24,0,52.00,0.00,18.00,1,1248.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(101,31,27,'Lifebuoy Total Soap 125g','Lifebuoy','Soap','3401',36.00,12,0,27.00,0.00,18.00,1,324.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(102,31,28,'Clinic Plus Strong Shampoo 175ml','Clinic Plus','Shampoo','3305',115.00,24,0,86.25,0.00,18.00,1,2070.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(103,32,5,'Dabur Amla Hair Oil 275ml','Dabur','Hair Oil','3305',199.00,12,0,149.25,0.00,18.00,1,1791.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(104,32,16,'Parle Monaco Classic 200g','Parle','Biscuits','1905',40.00,24,0,30.00,0.00,18.00,1,720.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(105,32,18,'Britannia Good Day Cashew 200g','Britannia','Biscuits','1905',45.00,6,0,33.75,0.00,18.00,0,238.95,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(106,33,2,'Himalaya Purifying Neem Soap 125g','Himalaya','Soap','3401',45.00,24,0,33.75,0.00,18.00,1,810.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(107,33,7,'Dabur Honey 500g','Dabur','Health','0409',199.00,12,0,149.25,0.00,5.00,1,1791.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(108,33,8,'Dabur Chyawanprash 500g','Dabur','Health','2106',215.00,12,0,161.25,0.00,12.00,1,1935.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(109,33,10,'Patanjali Dant Kanti Toothpaste 200g','Patanjali','Toothpaste','3306',100.00,24,0,75.00,0.00,12.00,1,1800.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(110,34,2,'Himalaya Purifying Neem Soap 125g','Himalaya','Soap','3401',45.00,24,0,33.75,0.00,18.00,1,810.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(111,34,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo','3305',130.00,12,0,97.50,0.00,18.00,1,1170.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(112,34,13,'Colgate MaxFresh Blue 150g','Colgate','Toothpaste','3306',105.00,12,0,78.75,0.00,18.00,1,945.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(113,34,26,'Test Cream 50g','Dabur','Skin Care',NULL,99.00,12,0,74.25,0.00,18.00,1,891.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(114,35,1,'Himalaya Neem Face Wash 100ml','Himalaya','Face Wash','3304',165.00,24,0,123.75,0.00,18.00,1,2970.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(115,35,8,'Dabur Chyawanprash 500g','Dabur','Health','2106',215.00,24,0,161.25,0.00,12.00,1,3870.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(116,35,24,'Hair & Care Damage Repair 200ml','Marico','Hair Oil','3305',120.00,12,0,90.00,0.00,18.00,1,1080.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(117,36,2,'Himalaya Purifying Neem Soap 125g','Himalaya','Soap','3401',45.00,24,0,33.75,0.00,18.00,1,810.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(118,36,12,'Colgate Strong Teeth 200g','Colgate','Toothpaste','3306',122.00,12,0,91.50,0.00,18.00,1,1098.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(119,36,19,'Britannia Marie Gold 250g','Britannia','Biscuits','1905',40.00,6,0,30.00,0.00,18.00,1,180.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(120,37,5,'Dabur Amla Hair Oil 275ml','Dabur','Hair Oil','3305',199.00,24,0,149.25,0.00,18.00,1,3582.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(121,37,12,'Colgate Strong Teeth 200g','Colgate','Toothpaste','3306',122.00,24,0,91.50,0.00,18.00,1,2196.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(122,37,14,'Colgate ZigZag Toothbrush Medium','Colgate','Oral Care','9603',45.00,48,0,33.75,0.00,18.00,1,1620.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(123,37,26,'Test Cream 50g','Dabur','Skin Care',NULL,99.00,12,0,74.25,0.00,18.00,1,891.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(124,38,5,'Dabur Amla Hair Oil 275ml','Dabur','Hair Oil','3305',199.00,12,0,149.25,0.00,18.00,1,1791.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(125,38,9,'Patanjali Kesh Kanti Hair Oil 120ml','Patanjali','Hair Oil','3305',130.00,24,0,97.50,0.00,18.00,1,2340.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(126,38,14,'Colgate ZigZag Toothbrush Medium','Colgate','Oral Care','9603',45.00,24,0,33.75,0.00,18.00,1,810.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(127,38,16,'Parle Monaco Classic 200g','Parle','Biscuits','1905',40.00,12,0,30.00,0.00,18.00,1,360.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(128,38,22,'Emami Navratna Oil 200ml','Emami','Hair Oil','3305',135.00,6,0,101.25,0.00,18.00,1,607.50,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(129,39,4,'Himalaya Baby Powder 200g','Himalaya','Skin Care','3304',145.00,12,0,108.75,0.00,18.00,1,1305.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(130,39,27,'Lifebuoy Total Soap 125g','Lifebuoy','Soap','3401',36.00,6,0,27.00,0.00,18.00,1,162.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(131,40,9,'Patanjali Kesh Kanti Hair Oil 120ml','Patanjali','Hair Oil','3305',130.00,12,0,97.50,0.00,18.00,1,1170.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(132,40,10,'Patanjali Dant Kanti Toothpaste 200g','Patanjali','Toothpaste','3306',100.00,12,0,75.00,0.00,12.00,1,900.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(133,40,21,'Emami BoroPlus Cream 80ml','Emami','Skin Care','3304',105.00,12,0,78.75,0.00,18.00,1,945.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(134,41,15,'Parle-G Gold 500g','Parle','Biscuits','1905',60.00,12,1,45.60,5.00,18.00,1,547.20,'2026-07-16 11:06:37','2026-07-16 11:06:37');
/*!40000 ALTER TABLE `invoice_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` int unsigned NOT NULL,
  `partner_id` bigint unsigned NOT NULL,
  `invoice_date` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `discount_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `round_off` decimal(6,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_no_unique` (`invoice_no`),
  KEY `invoices_partner_id_foreign` (`partner_id`),
  CONSTRAINT `invoices_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,1,1,'2026-07-14',1538.88,'percent',2.00,30.78,'cash discount',-0.10,1508.00,'2026-07-14 11:56:06','2026-07-14 11:56:06'),(2,2,2,'2026-07-15',313.65,NULL,0.00,0.00,NULL,0.35,314.00,'2026-07-14 21:04:53','2026-07-14 21:04:53'),(3,3,1,'2026-07-15',547.20,NULL,0.00,0.00,NULL,-0.20,547.00,'2026-07-15 02:32:11','2026-07-15 02:32:11'),(4,4,1,'2026-07-15',7752.00,NULL,0.00,0.00,NULL,0.00,7752.00,'2026-07-15 03:01:52','2026-07-15 03:01:52'),(5,5,1,'2026-07-16',472.00,NULL,0.00,0.00,NULL,0.00,472.00,'2026-07-15 21:54:23','2026-07-15 21:54:23'),(6,6,4,'2025-04-18',11520.00,NULL,0.00,0.00,NULL,0.00,11520.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(7,7,3,'2025-04-25',8478.00,NULL,0.00,0.00,NULL,0.00,8478.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(8,8,5,'2025-05-18',3105.00,NULL,0.00,0.00,NULL,0.00,3105.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(9,9,4,'2025-05-22',7840.50,NULL,0.00,0.00,NULL,0.50,7841.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(10,10,3,'2025-06-03',4275.00,NULL,0.00,0.00,NULL,0.00,4275.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(11,11,5,'2025-06-13',10287.00,NULL,0.00,0.00,NULL,0.00,10287.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(12,12,3,'2025-07-02',3267.90,NULL,0.00,0.00,NULL,0.10,3268.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(13,13,1,'2025-07-13',3600.00,NULL,0.00,0.00,NULL,0.00,3600.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(14,14,3,'2025-08-22',4131.00,NULL,0.00,0.00,NULL,0.00,4131.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(15,15,4,'2025-08-24',18724.50,NULL,0.00,0.00,NULL,0.50,18725.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(16,16,6,'2025-09-07',5793.00,NULL,0.00,0.00,NULL,0.00,5793.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(17,17,2,'2025-09-18',2353.50,NULL,0.00,0.00,NULL,0.50,2354.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(18,18,6,'2025-10-02',4005.00,NULL,0.00,0.00,NULL,0.00,4005.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(19,19,4,'2025-10-10',1449.00,NULL,0.00,0.00,NULL,0.00,1449.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(20,20,2,'2025-11-23',5314.50,NULL,0.00,0.00,NULL,0.50,5315.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(21,21,3,'2025-11-24',10728.00,NULL,0.00,0.00,NULL,0.00,10728.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(22,22,3,'2025-12-02',5328.00,NULL,0.00,0.00,NULL,0.00,5328.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(23,23,4,'2025-12-02',2727.90,NULL,0.00,0.00,NULL,0.10,2728.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(24,24,4,'2026-01-12',3546.00,NULL,0.00,0.00,NULL,0.00,3546.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(25,25,3,'2026-01-23',6840.00,NULL,0.00,0.00,NULL,0.00,6840.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(26,26,2,'2026-02-25',6525.00,NULL,0.00,0.00,NULL,0.00,6525.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(27,27,2,'2026-03-18',17064.00,NULL,0.00,0.00,NULL,0.00,17064.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(28,28,6,'2026-04-02',5940.00,NULL,0.00,0.00,NULL,0.00,5940.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(29,29,7,'2026-04-05',3006.00,NULL,0.00,0.00,NULL,0.00,3006.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(30,30,3,'2026-04-05',10917.00,NULL,0.00,0.00,NULL,0.00,10917.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(31,31,2,'2026-04-08',10653.00,NULL,0.00,0.00,NULL,0.00,10653.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(32,32,3,'2026-05-02',2749.95,NULL,0.00,0.00,NULL,0.05,2750.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(33,33,6,'2026-05-05',6336.00,NULL,0.00,0.00,NULL,0.00,6336.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(34,34,3,'2026-05-14',3816.00,NULL,0.00,0.00,NULL,0.00,3816.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(35,35,5,'2026-06-02',7920.00,NULL,0.00,0.00,NULL,0.00,7920.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(36,36,6,'2026-06-08',2088.00,NULL,0.00,0.00,NULL,0.00,2088.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(37,37,7,'2026-06-12',8289.00,NULL,0.00,0.00,NULL,0.00,8289.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(38,38,5,'2026-07-03',5908.50,NULL,0.00,0.00,NULL,0.50,5909.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(39,39,6,'2026-07-03',1467.00,NULL,0.00,0.00,NULL,0.00,1467.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(40,40,5,'2026-07-06',3015.00,NULL,0.00,0.00,NULL,0.00,3015.00,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(41,41,5,'2026-07-16',547.20,NULL,0.00,0.00,NULL,-0.20,547.00,'2026-07-16 11:06:37','2026-07-16 11:06:37');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inward_entries`
--

DROP TABLE IF EXISTS `inward_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inward_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inward_date` date NOT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inward_entries_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `inward_entries_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inward_entries`
--

LOCK TABLES `inward_entries` WRITE;
/*!40000 ALTER TABLE `inward_entries` DISABLE KEYS */;
INSERT INTO `inward_entries` VALUES (1,'2026-07-16',2,'weekly delivery','2026-07-15 21:13:10','2026-07-15 21:13:10'),(2,'2026-07-16',1,'scheme stock','2026-07-15 21:14:17','2026-07-15 21:14:17'),(3,'2026-06-20',3,'Mid-June replenishment','2026-07-15 23:58:05','2026-07-15 23:58:05'),(4,'2026-07-05',4,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(5,'2026-07-12',NULL,'Local cash purchase','2026-07-15 23:58:05','2026-07-15 23:58:05'),(6,'2026-07-17',2,NULL,'2026-07-16 20:24:37','2026-07-16 20:24:37'),(7,'2026-07-17',2,NULL,'2026-07-16 20:25:37','2026-07-16 20:25:37'),(8,'2026-07-17',NULL,NULL,'2026-07-16 20:36:00','2026-07-16 20:36:00');
/*!40000 ALTER TABLE `inward_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inward_lines`
--

DROP TABLE IF EXISTS `inward_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inward_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inward_entry_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int unsigned NOT NULL,
  `purchase_rate` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inward_lines_inward_entry_id_foreign` (`inward_entry_id`),
  KEY `inward_lines_product_id_foreign` (`product_id`),
  CONSTRAINT `inward_lines_inward_entry_id_foreign` FOREIGN KEY (`inward_entry_id`) REFERENCES `inward_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inward_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inward_lines`
--

LOCK TABLES `inward_lines` WRITE;
/*!40000 ALTER TABLE `inward_lines` DISABLE KEYS */;
INSERT INTO `inward_lines` VALUES (1,1,15,'Parle-G Gold 500g','Parle','Biscuits',50,37.20,'2026-07-15 21:13:10','2026-07-15 21:13:10'),(2,2,18,'Britannia Good Day Cashew 200g','Britannia','Biscuits',24,NULL,'2026-07-15 21:14:17','2026-07-15 21:14:17'),(3,3,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo',12,89.70,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(4,3,10,'Patanjali Dant Kanti Toothpaste 200g','Patanjali','Toothpaste',24,69.00,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(5,3,24,'Hair & Care Damage Repair 200ml','Marico','Hair Oil',24,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(6,4,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo',48,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(7,4,14,'Colgate ZigZag Toothbrush Medium','Colgate','Oral Care',24,31.05,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(8,4,15,'Parle-G Gold 500g','Parle','Biscuits',24,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(9,5,2,'Himalaya Purifying Neem Soap 125g','Himalaya','Soap',24,31.05,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(10,5,11,'Patanjali Aloe Vera Gel 150ml','Patanjali','Skin Care',12,62.10,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(11,5,21,'Emami BoroPlus Cream 80ml','Emami','Skin Care',12,72.45,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(12,6,15,'Parle-G Gold 500g','Parle','Biscuits',24,40.00,'2026-07-16 20:24:37','2026-07-16 20:24:37'),(13,7,17,'Parle Hide & Seek 200g','Parle','Biscuits',12,NULL,'2026-07-16 20:25:37','2026-07-16 20:25:37'),(14,7,15,'Parle-G Gold 500g','Parle','Biscuits',3,NULL,'2026-07-16 20:25:37','2026-07-16 20:25:37'),(15,8,16,'Parle Monaco Classic 200g','Parle','Biscuits',6,NULL,'2026-07-16 20:36:00','2026-07-16 20:36:00');
/*!40000 ALTER TABLE `inward_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_07_14_100001_create_brands_table',2),(5,'2026_07_14_100002_create_categories_table',2),(6,'2026_07_14_100003_create_products_table',2),(7,'2026_07_14_110001_rework_users_for_otp',3),(8,'2026_07_14_110002_create_rate_groups_table',3),(9,'2026_07_14_110003_create_partners_table',3),(10,'2026_07_14_110004_create_rate_slabs_table',3),(11,'2026_07_14_110005_create_otp_codes_table',3),(12,'2026_07_14_120001_add_show_prices_to_partners',4),(13,'2026_07_14_130001_add_details_to_partners',5),(14,'2026_07_14_140001_add_scheme_offer_to_rate_slabs',6),(15,'2026_07_14_150001_create_held_bills_table',7),(16,'2026_07_14_160001_create_invoices_tables',8),(17,'2026_07_14_170001_create_settings_table',9),(18,'2026_07_15_100001_create_payments_table',10),(19,'2026_07_15_053956_create_personal_access_tokens_table',11),(20,'2026_07_15_110001_create_orders_tables',12),(21,'2026_07_15_120001_create_credit_notes_tables',13),(22,'2026_07_15_130001_create_suppliers_table',14),(23,'2026_07_15_140001_create_inward_tables',15),(24,'2026_07_15_150001_create_supplier_ledger_tables',16);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_lines`
--

DROP TABLE IF EXISTS `order_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `qty` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_lines_order_id_foreign` (`order_id`),
  KEY `order_lines_product_id_foreign` (`product_id`),
  CONSTRAINT `order_lines_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_lines`
--

LOCK TABLES `order_lines` WRITE;
/*!40000 ALTER TABLE `order_lines` DISABLE KEYS */;
INSERT INTO `order_lines` VALUES (1,1,15,'Parle-G Gold 500g','Parle','Biscuits',60.00,24,'2026-07-15 02:07:23','2026-07-15 02:07:23'),(2,1,18,'Britannia Good Day Cashew 200g','Britannia','Biscuits',45.00,6,'2026-07-15 02:07:23','2026-07-15 02:07:23'),(3,2,15,'Parle-G Gold 500g','Parle','Biscuits',60.00,12,'2026-07-15 02:15:14','2026-07-15 02:15:14'),(4,3,15,'Parle-G Gold 500g','Parle','Biscuits',60.00,12,'2026-07-15 23:41:21','2026-07-15 23:41:21'),(5,4,24,'Hair & Care Damage Repair 200ml','Marico','Hair Oil',120.00,24,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(6,4,26,'Test Cream 50g','Dabur','Skin Care',99.00,24,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(7,5,22,'Emami Navratna Oil 200ml','Emami','Hair Oil',135.00,6,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(8,5,29,'Maggi Noodles 70g','Nestle','Snacks',14.00,6,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(9,6,3,'Himalaya Anti-Dandruff Shampoo 180ml','Himalaya','Shampoo',130.00,6,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(10,6,14,'Colgate ZigZag Toothbrush Medium','Colgate','Oral Care',45.00,24,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(11,7,13,'Colgate MaxFresh Blue 150g','Colgate','Toothpaste',105.00,3,'2026-07-16 20:48:32','2026-07-16 20:48:32'),(12,7,15,'Parle-G Gold 500g','Parle','Biscuits',60.00,12,'2026-07-16 20:48:32','2026-07-16 20:48:32'),(13,7,20,'Britannia Bourbon 150g','Britannia','Biscuits',35.00,3,'2026-07-16 20:48:32','2026-07-16 20:48:32'),(14,8,15,'Parle-G Gold 500g','Parle','Biscuits',60.00,3,'2026-07-16 21:17:03','2026-07-16 21:17:03'),(15,8,16,'Parle Monaco Classic 200g','Parle','Biscuits',40.00,3,'2026-07-16 21:17:03','2026-07-16 21:17:03');
/*!40000 ALTER TABLE `order_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `partner_id` bigint unsigned NOT NULL,
  `status` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_partner_id_foreign` (`partner_id`),
  KEY `orders_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `orders_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  CONSTRAINT `orders_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,'pending','urgent delivery please',NULL,'2026-07-15 02:07:23','2026-07-15 02:07:23'),(2,1,'invoiced','please send asap',3,'2026-07-15 02:15:14','2026-07-15 02:32:11'),(3,1,'pending','order via retailer website',NULL,'2026-07-15 23:41:21','2026-07-15 23:41:21'),(4,4,'pending','need before weekend please',NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(5,4,'pending',NULL,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(6,4,'cancelled','ordered by mistake',NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(7,1,'pending',NULL,NULL,'2026-07-16 20:48:32','2026-07-16 20:48:32'),(8,1,'pending',NULL,NULL,'2026-07-16 21:17:03','2026-07-16 21:17:03');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_codes`
--

DROP TABLE IF EXISTS `otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_codes_mobile_index` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_codes`
--

LOCK TABLES `otp_codes` WRITE;
/*!40000 ALTER TABLE `otp_codes` DISABLE KEYS */;
INSERT INTO `otp_codes` VALUES (1,'9999999999','517597','2026-07-14 03:57:31','2026-07-14 03:52:44','2026-07-14 03:52:31','2026-07-14 03:52:44'),(2,'6666666666','789426','2026-07-14 04:37:42','2026-07-14 04:32:48','2026-07-14 04:32:42','2026-07-14 04:32:48'),(3,'9999999999','771361','2026-07-14 04:38:58','2026-07-14 04:34:19','2026-07-14 04:33:58','2026-07-14 04:34:19'),(4,'9999999999','136058','2026-07-14 04:39:19','2026-07-14 04:34:25','2026-07-14 04:34:19','2026-07-14 04:34:25'),(5,'9999999999','154842','2026-07-14 04:40:28','2026-07-14 04:35:34','2026-07-14 04:35:28','2026-07-14 04:35:34'),(6,'6666666666','743042','2026-07-14 04:40:54',NULL,'2026-07-14 04:35:54','2026-07-14 04:35:54'),(7,'9999999999','709687','2026-07-14 04:41:03','2026-07-14 04:36:10','2026-07-14 04:36:03','2026-07-14 04:36:10'),(8,'9999999999','071666','2026-07-14 20:51:09','2026-07-14 20:46:14','2026-07-14 20:46:09','2026-07-14 20:46:14'),(9,'8888888888','839317','2026-07-15 00:20:19','2026-07-15 00:15:20','2026-07-15 00:15:19','2026-07-15 00:15:20'),(10,'8888888888','921026','2026-07-15 00:44:48','2026-07-15 00:39:48','2026-07-15 00:39:48','2026-07-15 00:39:48'),(11,'8888888888','297080','2026-07-15 01:53:46','2026-07-15 01:48:52','2026-07-15 01:48:46','2026-07-15 01:48:52'),(12,'8888888888','301576','2026-07-15 01:55:47','2026-07-15 01:50:52','2026-07-15 01:50:47','2026-07-15 01:50:52'),(13,'8888888888','540945','2026-07-15 02:12:23','2026-07-15 02:07:23','2026-07-15 02:07:23','2026-07-15 02:07:23'),(14,'7777777777','469126','2026-07-15 02:12:24','2026-07-15 02:07:24','2026-07-15 02:07:24','2026-07-15 02:07:24'),(15,'9999999999','551352','2026-07-15 20:40:01','2026-07-15 20:35:08','2026-07-15 20:35:01','2026-07-15 20:35:08'),(16,'8888888888','865982','2026-07-15 23:15:40','2026-07-15 23:14:58','2026-07-15 23:10:40','2026-07-15 23:14:58'),(17,'8888888888','312257','2026-07-15 23:19:58','2026-07-15 23:15:03','2026-07-15 23:14:58','2026-07-15 23:15:03'),(18,'8888888888','564888','2026-07-15 23:46:16','2026-07-15 23:41:16','2026-07-15 23:41:16','2026-07-15 23:41:16'),(19,'9999999999','608976','2026-07-16 00:12:27','2026-07-16 00:07:35','2026-07-16 00:07:27','2026-07-16 00:07:35'),(20,'9999999999','927619','2026-07-16 07:59:11','2026-07-16 07:54:16','2026-07-16 07:54:11','2026-07-16 07:54:16'),(21,'9999999999','973266','2026-07-16 10:19:02','2026-07-16 10:14:06','2026-07-16 10:14:02','2026-07-16 10:14:06'),(22,'9999999999','961262','2026-07-16 20:24:29','2026-07-16 20:19:33','2026-07-16 20:19:29','2026-07-16 20:19:33'),(23,'8888888888','019248','2026-07-16 20:52:29','2026-07-16 20:47:34','2026-07-16 20:47:29','2026-07-16 20:47:34'),(24,'8888888888','147396','2026-07-16 21:21:34','2026-07-16 21:16:41','2026-07-16 21:16:34','2026-07-16 21:16:41'),(25,'9999999999','670552','2026-07-16 21:22:14','2026-07-16 21:17:18','2026-07-16 21:17:14','2026-07-16 21:17:18'),(26,'8888888888','286508','2026-07-16 22:08:13','2026-07-16 22:03:19','2026-07-16 22:03:13','2026-07-16 22:03:19'),(27,'9999999999','129088','2026-07-16 22:09:51','2026-07-16 22:04:56','2026-07-16 22:04:51','2026-07-16 22:04:56'),(28,'8888888888','707401','2026-07-16 22:12:29','2026-07-16 22:07:33','2026-07-16 22:07:29','2026-07-16 22:07:33'),(29,'9999999999','775610','2026-07-16 22:14:39','2026-07-16 22:15:15','2026-07-16 22:09:39','2026-07-16 22:15:15'),(30,'9999999999','755019','2026-07-16 22:20:15','2026-07-16 22:15:23','2026-07-16 22:15:15','2026-07-16 22:15:23'),(31,'9999999999','272851','2026-07-16 22:20:23','2026-07-16 22:15:40','2026-07-16 22:15:23','2026-07-16 22:15:40'),(32,'9999999999','595646','2026-07-16 22:20:40','2026-07-16 22:18:07','2026-07-16 22:15:40','2026-07-16 22:18:07'),(33,'9999999999','646819','2026-07-16 22:23:07','2026-07-16 22:20:53','2026-07-16 22:18:07','2026-07-16 22:20:53'),(34,'9999999999','785536','2026-07-16 22:25:53','2026-07-16 22:22:08','2026-07-16 22:20:53','2026-07-16 22:22:08'),(35,'9999999999','875902','2026-07-16 22:27:08','2026-07-16 22:22:15','2026-07-16 22:22:08','2026-07-16 22:22:15'),(36,'9999999999','899023','2026-07-16 22:27:15','2026-07-16 22:22:27','2026-07-16 22:22:15','2026-07-16 22:22:27'),(37,'8888888888','991890','2026-07-17 02:59:44','2026-07-17 02:54:50','2026-07-17 02:54:44','2026-07-17 02:54:50'),(38,'8888888888','160346','2026-07-17 03:00:27','2026-07-17 02:55:31','2026-07-17 02:55:27','2026-07-17 02:55:31');
/*!40000 ALTER TABLE `otp_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partners`
--

DROP TABLE IF EXISTS `partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `firm_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gst_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate_group_id` bigint unsigned NOT NULL,
  `portal_access` tinyint(1) NOT NULL DEFAULT '1',
  `show_prices` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `partners_mobile_unique` (`mobile`),
  KEY `partners_rate_group_id_foreign` (`rate_group_id`),
  CONSTRAINT `partners_rate_group_id_foreign` FOREIGN KEY (`rate_group_id`) REFERENCES `rate_groups` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partners`
--

LOCK TABLES `partners` WRITE;
/*!40000 ALTER TABLE `partners` DISABLE KEYS */;
INSERT INTO `partners` VALUES (1,'ABC General Store','Ramesh','8888888888',NULL,NULL,NULL,1,1,1,1,'2026-07-14 03:36:06','2026-07-15 02:14:28'),(2,'Kirana Retail Store','Suresh','7777777777','27AFGHJTRRD7TN',NULL,'Sinnar',1,1,1,1,'2026-07-14 03:36:06','2026-07-14 21:17:25'),(3,'Test Store',NULL,'6666666666',NULL,NULL,NULL,1,1,1,1,'2026-07-14 04:31:03','2026-07-14 04:35:47'),(4,'Sharma Kirana & General','Rajesh Sharma','9000000001','27ABCPS1234F1Z5',NULL,'Main Road, Sinnar',1,1,1,1,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(5,'Mauli Super Shoppe','Sunita Patil','9000000002',NULL,NULL,'Shivaji Chowk, Sinnar',1,1,1,1,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(6,'Balaji Traders','Venkatesh Rao','9000000003','27AAACB9876K1Z2',NULL,'Market Yard, Nashik',1,1,0,1,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(7,'New Ganesh Stores','Ganesh More','9000000004',NULL,NULL,'Pune Road, Sinnar',1,0,1,1,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(8,'Old City Mart (Closed)','Imran Shaikh','9000000005',NULL,NULL,'Old Bazar, Sinnar',1,1,1,0,'2026-07-15 23:58:04','2026-07-15 23:58:04');
/*!40000 ALTER TABLE `partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `partner_id` bigint unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_partner_id_foreign` (`partner_id`),
  CONSTRAINT `payments_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'2026-07-15',500.00,'upi','TESTUTR1',NULL,'2026-07-14 23:31:25','2026-07-14 23:31:25'),(2,4,'2025-05-02',9216.00,'upi',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(3,5,'2025-06-01',3105.00,'cash','UTR843514',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(4,3,'2025-06-16',4275.00,'cheque','UTR998344',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(5,5,'2025-06-28',10287.00,'cheque',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(6,3,'2025-07-21',2941.20,'cash','UTR350381',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(7,1,'2025-07-24',3600.00,'upi','UTR610448',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(8,6,'2025-09-21',5213.70,'upi',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(9,2,'2025-10-01',1883.20,'upi','UTR216602',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(10,4,'2025-10-20',1449.00,'cash',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(11,2,'2025-11-28',4783.50,'upi','UTR773165',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(12,3,'2025-12-06',4262.40,'bank','UTR617164',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(13,4,'2025-12-15',2182.40,'upi',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(14,3,'2026-02-03',6156.00,'upi',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(15,2,'2026-03-13',5872.50,'upi','UTR770702',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(16,2,'2026-04-05',17064.00,'cash','UTR503653',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(17,7,'2026-04-21',2705.40,'cheque',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(18,2,'2026-04-23',10653.00,'upi',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(19,3,'2026-05-08',2750.00,'cash','UTR935507',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(20,6,'2026-05-24',5068.80,'cash',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(21,3,'2026-05-29',3434.40,'bank','UTR372182',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(22,5,'2026-06-14',7920.00,'cheque',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(23,6,'2026-06-21',2088.00,'cash','UTR731072',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(24,7,'2026-06-24',8289.00,'bank',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(25,5,'2026-07-15',5909.00,'upi',NULL,NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(26,6,'2026-07-08',1320.30,'upi','UTR433073',NULL,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(27,4,'2026-07-16',500.00,'upi',NULL,NULL,'2026-07-16 01:47:10','2026-07-16 01:47:10');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
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
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\Partner',1,'retailer','1f62d08b8f74406d0e5ca7acec2bf7102cb16b6019dcc8e83dd590d15f253b90','[\"*\"]','2026-07-15 00:15:20',NULL,'2026-07-15 00:15:20','2026-07-15 00:15:20'),(2,'App\\Models\\Partner',1,'retailer','cb2db7431ebe2e66dc43cc46a2b68af5b5d468945f0f98bdae7b3a7013bcc55b','[\"*\"]','2026-07-15 00:41:33',NULL,'2026-07-15 00:39:48','2026-07-15 00:41:33'),(3,'App\\Models\\Partner',1,'retailer','f8d4b15eb232f1802c377446f41897e5f1cad8b8726979c41356ade15925f0bd','[\"*\"]','2026-07-16 00:56:36',NULL,'2026-07-15 01:50:52','2026-07-16 00:56:36'),(4,'App\\Models\\Partner',1,'retailer','df277613b338eb717775ddb8c501668f1cc942834b1958344cdec1536d8215be','[\"*\"]','2026-07-15 02:07:24',NULL,'2026-07-15 02:07:23','2026-07-15 02:07:24'),(5,'App\\Models\\Partner',2,'retailer','79754f570c99ea11e94f13115547eeb7e3d1b8f0815b21b87df14f6bed705115','[\"*\"]','2026-07-15 02:07:26',NULL,'2026-07-15 02:07:24','2026-07-15 02:07:26'),(7,'App\\Models\\Partner',1,'retailer','9d2074fe928a723bc507e9cd3b641b7b8177775165be9814a277405c61ff7883','[\"*\"]','2026-07-15 23:41:21',NULL,'2026-07-15 23:41:16','2026-07-15 23:41:21'),(12,'App\\Models\\Partner',1,'retailer','641a21941e83b101bd0352bf5ab77c44cc06f0c36557d4176aa8a65e3100ceb2','[\"*\"]','2026-07-17 03:27:34',NULL,'2026-07-17 02:54:50','2026-07-17 03:27:34');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `hsn_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_inclusive` tinyint(1) NOT NULL DEFAULT '1',
  `track_stock` tinyint(1) NOT NULL DEFAULT '0',
  `stock_qty` int NOT NULL DEFAULT '0',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `rate_visible` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_barcode_unique` (`barcode`),
  KEY `products_brand_id_foreign` (`brand_id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Himalaya Neem Face Wash 100ml',1,1,'8901138511051',165.00,'3304',18.00,1,1,48,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(2,'Himalaya Purifying Neem Soap 125g',1,2,'8901138511068',45.00,'3401',18.00,1,1,168,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-15 23:58:05'),(3,'Himalaya Anti-Dandruff Shampoo 180ml',1,3,'8901138511075',130.00,'3305',18.00,1,1,60,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-15 23:58:05'),(4,'Himalaya Baby Powder 200g',1,4,'8901138511082',145.00,'3304',18.00,1,1,0,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(5,'Dabur Amla Hair Oil 275ml',2,5,'8901207010012',199.00,'3305',18.00,1,1,36,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(6,'Dabur Red Toothpaste 200g',2,6,'8901207010029',145.00,'3306',12.00,1,1,72,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(7,'Dabur Honey 500g',2,7,'8901207010036',199.00,'0409',5.00,1,1,0,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(8,'Dabur Chyawanprash 500g',2,7,'8901207010043',215.00,'2106',12.00,1,1,0,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(9,'Patanjali Kesh Kanti Hair Oil 120ml',3,5,'8904109410017',130.00,'3305',18.00,1,1,0,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(10,'Patanjali Dant Kanti Toothpaste 200g',3,6,'8904109410024',100.00,'3306',12.00,1,1,84,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-15 23:58:05'),(11,'Patanjali Aloe Vera Gel 150ml',3,4,'8904109410031',90.00,'3304',18.00,1,1,12,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-15 23:58:05'),(12,'Colgate Strong Teeth 200g',4,6,'8901314010018',122.00,'3306',18.00,1,1,96,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(13,'Colgate MaxFresh Blue 150g',4,6,'8901314010025',105.00,'3306',18.00,1,1,0,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(14,'Colgate ZigZag Toothbrush Medium',4,8,'8901314010032',45.00,'9603',18.00,1,1,144,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-15 23:58:05'),(15,'Parle-G Gold 500g',5,9,'8901719100017',60.00,'1905',18.00,1,1,264,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-16 20:25:37'),(16,'Parle Monaco Classic 200g',5,9,'8901719100024',40.00,'1905',18.00,1,1,6,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-16 20:36:00'),(17,'Parle Hide & Seek 200g',5,9,'8901719100031',50.00,'1905',18.00,1,1,12,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-16 20:25:37'),(18,'Britannia Good Day Cashew 200g',6,9,'8901063010013',45.00,'1905',18.00,0,1,164,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-15 22:51:54'),(19,'Britannia Marie Gold 250g',6,9,'8901063010020',40.00,'1905',18.00,1,1,0,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(20,'Britannia Bourbon 150g',6,9,'8901063010037',35.00,'1905',18.00,1,1,0,'products/qm64AcSEMXLoxUAAv9vTfYE5y07OLsc9U0A4xkWC.jpg',1,1,1,'2026-07-14 00:17:19','2026-07-14 02:52:51'),(21,'Emami BoroPlus Cream 80ml',7,4,'8901248010015',105.00,'3304',18.00,1,1,12,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-15 23:58:05'),(22,'Emami Navratna Oil 200ml',7,5,'8901248010022',135.00,'3305',18.00,1,1,40,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(23,'Parachute Coconut Oil 250ml',8,5,'8901088010016',146.00,'1513',5.00,1,1,84,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-14 00:17:19'),(24,'Hair & Care Damage Repair 200ml',8,5,'8901088010023',120.00,'3305',18.00,1,1,24,NULL,1,1,1,'2026-07-14 00:17:19','2026-07-15 23:58:05'),(25,'Test Soap 100g',7,2,'1234567890123',50.00,NULL,18.00,1,1,0,NULL,1,1,0,'2026-07-14 00:56:18','2026-07-14 02:53:41'),(26,'Test Cream 50g',2,4,NULL,99.00,NULL,18.00,1,1,0,'products/jnUOhp4U1rYTkGzO47PciiftcDXuXKvDlLrmagrg.webp',1,1,1,'2026-07-14 01:32:14','2026-07-14 01:32:14'),(27,'Lifebuoy Total Soap 125g',9,2,'8901030510014',36.00,'3401',18.00,1,1,100,NULL,1,1,1,'2026-07-14 03:12:31','2026-07-14 03:12:31'),(28,'Clinic Plus Strong Shampoo 175ml',10,3,'8901030510021',115.00,'3305',18.00,1,1,0,NULL,1,1,1,'2026-07-14 03:12:31','2026-07-14 03:12:31'),(29,'Maggi Noodles 70g',11,10,NULL,14.00,'1902',12.00,1,1,250,NULL,1,1,1,'2026-07-14 03:12:31','2026-07-14 03:12:31');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rate_groups`
--

DROP TABLE IF EXISTS `rate_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rate_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rate_groups_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_groups`
--

LOCK TABLES `rate_groups` WRITE;
/*!40000 ALTER TABLE `rate_groups` DISABLE KEYS */;
INSERT INTO `rate_groups` VALUES (1,'General','2026-07-14 03:36:06','2026-07-14 03:36:06');
/*!40000 ALTER TABLE `rate_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rate_slabs`
--

DROP TABLE IF EXISTS `rate_slabs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rate_slabs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `rate_group_id` bigint unsigned NOT NULL,
  `min_qty` int unsigned NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `scheme_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `offer_buy_qty` int unsigned DEFAULT NULL,
  `offer_free_qty` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rate_slabs_product_id_rate_group_id_min_qty_unique` (`product_id`,`rate_group_id`,`min_qty`),
  KEY `rate_slabs_rate_group_id_foreign` (`rate_group_id`),
  CONSTRAINT `rate_slabs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rate_slabs_rate_group_id_foreign` FOREIGN KEY (`rate_group_id`) REFERENCES `rate_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rate_slabs`
--

LOCK TABLES `rate_slabs` WRITE;
/*!40000 ALTER TABLE `rate_slabs` DISABLE KEYS */;
INSERT INTO `rate_slabs` VALUES (6,15,1,1,52.00,0.00,NULL,NULL,'2026-07-14 05:50:06','2026-07-14 05:50:06'),(7,15,1,6,50.00,2.00,NULL,NULL,'2026-07-14 05:50:06','2026-07-14 05:50:06'),(8,15,1,12,48.00,5.00,12,1,'2026-07-14 05:50:06','2026-07-14 05:50:06');
/*!40000 ALTER TABLE `rate_slabs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('0hD2j6el1ij28p6H6ABzK6mUdZ5D4z8oqKTP1eRz',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJvcjVaTTVDQ1B0elU1aVNYdXFORmtOVVVpU2pGblZkY05SQU9TUXFtIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274826),('1UrtoRPOZoTlSinSNVEfDAzr9pUPWeQgASeidrNx',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI4YTBOanVUaTBYaUVldnAySHRjNEx2VlYyMFJxTjgwQWVYVzF4eW1uIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280065),('2GfLSya1UQqt4JdyPFlDVhjL6LdY9qy0dDMEn2Gi',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiIxMFhva2ZHQXlZMGpuVEhURUMzb0kyOGpKem0wU0NudzBJZ3VPZzZHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279705),('2ImVKjcqosfz67bKo7kJV6BtVsbQe7ngab2JQPxT',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJLNUVRNVBQV1hUU1FJYnM4V01EeDdoaEE5enBLUElQZXhUcE1zVzNWIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277807),('3VFCJtGGKDv5JCoqRjn8aCXloPViDQ8pJqAnme6o',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJkVVhTZUpPeGRwR0hWWTdmVzFDZ0l4Vndlc1FtYjJiUzltUU94ZW04IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278305),('44WUpvCJVYdFRcwyHOTqbep9hxjEumioclk5XKIP',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJUc0VrY29IU0FxOXRDdEREM2MzNmdQcXhIWUlBc2tCd2xkc0xhY1BEIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784275275),('4F9HFYmk7DFOwwNOoNzRAaiUbdApQd9h4h6xeXmt',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJjcWJIMFd3VVFpUDRQVlFKYnZST1RHdmJpSzZjc1QySmJkUzV2OEo2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280361),('5VsWE7No5j8PGTKiHquaFDom2xytpnfA0WuPa6Xo',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJxSzRTczlMaFMzNUdlN2ExODFyUFJWSXhPRmRPd1ZxSUowQU5GbThNIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279317),('7GeLBR7jFXJQeUQ3d0vwPV880LJ0NVlno1KZmBsD',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJnMDJlb3B5QnJhUTB2cm9jUUdzT0V4WVREUlUzaFZpdER0dFVvNTRpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280040),('7m358fIb6GptR6qPE6uxlD1gJLBaRX48gr82foMz',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJzZWliV1lUem5XNG9zeTV5ZUxNcmNUNnFpZHdJcDJlNlc5aHZNOXM4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278278),('83uRyY0qN8ahRTB4mbs35sjoDsVWlU2APb8pNFXc',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJIUWdZVlJFQUJSekQwMjFBNXA0SWZNN2NnODliS0llNVJNNjhibXpKIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279590),('9MHUEYMyhAsbYYjKRdO8m4xmSANNdsYLn0IpQg8e',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiIzWHJVUnZLU1JJWFdUUXBmZndXWFpRbzFNdjN3SnlDZWZkMkVHMHExIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279811),('9MM7WX9mK5Dvpl3rKN7ZUxwFRWnH7YObWKqn97iw',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJacGVuR05FN0VLMUhiWGNpMlJUUUxRM3piTXgxUlFLOHE1MXVjSU9HIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278012),('AoEQ9weMWJe8HxcM8un9lTAhTCuxPCruqcDJnjMX',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJxVVNoM1VadjNIc083UmUwdEVINzljalZLdmxNZmVqeWhaRDRtRGZNIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277988),('BAcGIGvHkwlxJBElHB934jISicje90W5qsePVRrb',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJnTHp3VnhwZU9ZMFFlaDRTWGl2bjl5dE9rZUs4ZG1YcHYzb2dyeUxuIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280139),('BESIVskoqydOhrmiDrWx1yWTAkybCq0KicfTjVeC',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJmY2oxRDdaTnRBT1lwU2tCQjBYUGp5Z2pPQkdHYTRWY3dSc1JYY1dLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278653),('c2tEkbC1OA7ffvuuA6WGaNujdC2X5yUwthm6XBH4',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJlWm1FUm43eVptU2poWmd3R242MjFWSERhbHkwV0xlTnBYUEppM3FVIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277637),('CmiMBcPzwm3uGbSx3iNzhcwpUIlMzNgfAH5zZtbO',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJwbTlWN2FVMGNXV0s0eUZSZ0tNMVJqb25mcWZjNmNkbjJLTVI2Y0VSIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278471),('CVeAmmwCuUKMHC4B41u3xgF2BILhd2QvSouddN8v',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJyTDJNV29BQVliV2sydXQzUDRMNWZVQ3dyeHJZMGdFb2RHYldyRFk0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784276633),('d218bvrcRSqATSTlOj6ifjKT5DeAvIkRaG92l2OR',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJ5ZFhYNHI1Z09yTTFveXJJRUpNUUl2SEFvSml4bFp6UFg5ZEtXTk56IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274719),('DA6Rlm8Ccb0Rs2Yg8Z8YLesuj4WM5xvMtNiPiCLO',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJXNVNtOTRpUUlGV1NiODlhb2xFbkJZZjRPdzI1YUtSbDJkaHRUQXdlIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278473),('dHmMEjH1x1SK4tE2gSFZJewtsLkxiwhG8QhaO2OL',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJENVFuTDZkMUdBeVBVaUVJS3B6NUZFZmFFclpVdWc2aVZLME9waFlzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280225),('DIiqigcSmTj7x4Zzo7SkLZk9teQ6pWxGrJF5PIzB',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJoNG9VUGRYRkdsM1FISGNuZmpVSE5IbXdwQ2s1TUJ6b0J2V2pQR0E3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784275195),('EdoiLQyPpr5QCuFmMS1tj9P7V2c36U0aXno6y5Sw',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJPRElycmRYa2dodHZycTcxWGpObk5ueWJXR25yaXpwbVVZWDhNQXFrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277807),('ER1z6X4IdGO5uDc23jOEtkuY6jm1j8q7BEriRaW0',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJ4cnpzZ1RQbDJrdkRlZUtxdzdsSUw0YklJaDR6aHdLUmNSZnllMEsxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784276160),('exoLnBWXzwGr0JCWyJiJB3exYqTL4HGISU5E2gJf',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJuSlQxUVBSSmtsUzBydFRuV2dOQWRCZjhuUVlRU3lkOUpiSXRuNUFPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274341),('Fc2H1jJaIvrMb14e4GT4AfTugQSwmUaRzq0o2OMU',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJHNll3Z1htWkpPZjJXazZ0TVhTd1hrak1VbDJFNk1iMElPdmdZQkE2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280211),('Fc3CsYFg6TZxmIW2cZePBKB6IDNgeJZnqUcgDtNT',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJrM1paVG82bldCN3M2NkZqS0JhNE1OcTRjZ1c3NmhwcmxFMGFoYWp0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784275238),('fCCkbbBR0U88qCVHQkeyGG7NtqPp2HuLOCs3wkwa',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJPVWxrbllmSEM4ckNjeFUwM2RkRE42bXdBNG9KZm1pYmJBVmJOTWg0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277807),('fMC04mbSzLmsf1qK0c5jnRWF1jcXaI9Y35CQv7ZU',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJFUUdEYjgzNk5JMEQxTDJqVTVkcDBYV0FoYnZQcnQ0b2t3bkxmNzVqIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278471),('fmkpXB09U5Iy6TWa238SfjGuSSUVaMcTvG8C6iDP',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI0RHo2Q3J4bGp5Nmlva0k3QVNWUmJrYm1mRmZGN2pzWGR0YVVuSUp3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279592),('g8mI3DmQ4ciwrRagnP2MTwVHyXQ1tngBUN6f23Px',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI4OEUwbDFiVmo2R2J2Z2lHUXFMWG5hSGJtbE9hY3FleGxJTkxIeHFIIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278305),('gtfdvxZsDFzDrJtd674AySbmhKNVrY95we8c1W4P',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI4Nkcwc1hhaHBybDM2N1RuMzFoUlZ1eFFFNkY2WjA2NnFENkU5dGt5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277678),('HBwMbRQXasQMpPOA8ENyQlK8TPgf6X5JoYOjbyLT',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJKa3A2dUVLZ0RMSzBhWWp5QzAyQXBlS0VWMXZ5dFN0MUJqZFhwQ0JVIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278234),('IJaD7csNWyesY0OlncG2eUgpDi4Wdd3dTkJZBoWl',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiI1UFc3UlZvUlN5ZmdpTWhHTlB0SFZRTXVYaG5EMTZtM21pMlhXNGdHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280572),('INB8b40nwBEak9biOJpByABGBxirLi4uo0C9JADX',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJiS2lSamJ1M0FBWjY4SUpRVDRudGlwMlozU0xkMVFkZDNPaTJEbXhTIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278653),('JPMHnLsUllnoi1a3AvxxzN2YheKtwwZuUTsbHEmQ',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJRdktMRlpacUM3MTBXdW5JRVB4c0x3WTI1TGhybDA2a3FsbkFUMnNvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784276795),('kbhFGbjsEFiCoHlAr38BDbbvduylrBsTJYhjV6YH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI4cmFXWjNKZ2JDb1JFWjR5Rmh3VDJqTXpvV0Y5TERqUmZjRmRwZ2o2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277637),('kF7oagdpLPuk502ZTMf3zZaF0CiuOxRbqqLwwLFy',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI3MkZFRUVxTHhOUnlid1Q4NUhPWFUwSWFldEo1RE1YV2F6U3BiSnplIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280327),('kz1iMC4RAYewdGP1AKULmAF0to57Z8Qz6oot25ui',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJxVlFlNUtxbHQ4b1k2Z1g1MVdLbnZwQzNSVGd1cDN1VGF3M1NUM3JkIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274827),('L13QCXC0OaV2YbupMiKrjYvQ1pNvEXFh3hYtsvII',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJDRURPanRpeHhDY3hOOGhsUGdiOGJqMzhaVG1YZnZCMFdvUkZldGFVIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784341620),('LfngFCoNDMWKcz4IBH0U5JsXTUXRzFNS5acqOGen',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI5SjlLc1VmT0xVNzVtVVRsUEJMMjVycldXRkNJY0k2U2NUYUFzRHBzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279594),('LItvzMLt2u0xEuTsrL8iHU3yORpAWHhY3XjrQ6aC',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiI1YjlQaG9sMnNlcXU1ZHlFVjZZenJqUlZxbmZOSkxFd3dUcTlaV3BQIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274439),('lvN03epuq63amm7lLebkUhvJ0VohehuvabGPcPVS',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJRaWsxQXhqekNNajhOWnFSbE5oQ25IclYxUHdZVHBGbUhtQjVHcEhLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278012),('LY2lq4vNQN3dweSfO4oOKZ3thhKFZ5jZYazB396x',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJiZDd6QmJ5R080V3dXbUxYbTZtdGZrcjJpdGtZbEV4T0VOOVBnR2xrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278223),('NMBXo9feXuuy8joV5lEmTBfnctHasLLbYqTrouRd',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJ4ZkttV1BSYmcxZGE0VkxFYmhGN3VUNmVYNE1lR0FHQmZtbnVhNmxQIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280564),('Ofsba1oc6jUDbx7b1YjRVVgvbDVlY9Btzt8us8Fr',1,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJOb1NiSHVFQngzRlNrUFFQOTIyN2FTY3BNMndFbTZ3VW8zMzVZRHpsIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvcGFydG5lcnNcL2RhdGE/bGltaXQ9MjUmb2Zmc2V0PTAmc3RhdHVzPWFsbCIsInJvdXRlIjoicGFydG5lcnMuZGF0YSJ9LCJkZXZfb3RwXzk5OTk5OTk5OTkiOiI4OTkwMjMiLCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=',1784280572),('OhOyX9ReSVeiQ3SNLCOCEdisRIcU6oUDvT1qdGZE',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJPbUFxWU40ZzZhek1ZWFEyRUN6OXVxalFXd3Q5WG9NTmVndmZYVTZJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280479),('otdJRdapBVmEcH7cgVSghrbuJ9debWkDPyTZ4ezJ',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJXN3BLRzlWTzdZMjZZRTFKNk1ua3BVUnd3bWNaQnQya0dYZEs0VEtpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784275735),('ov39mzIGyn6uZkLB0K8uefDxSqDdZavSQGE23Kw1',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJjOVg3U21hWGo3WFEwR3c1Y0RESG9FR1FaUHk0Rm44Z21pdDJpQTRXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280063),('p6FODaUvRjvB1R8pxUwBvn0nFGuIuRSKvUYwPoUl',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJlUjF2RjRUWEpUdUswMzBCMFA2VWFFS0RkTWtSbTdKeU51TU15Szc4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278473),('PFmaVKTnKD11oFwEW4sDjPqPolgjEW5BupXs1u2u',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJPUTllaGJIb0pka1U0d0hISDRBTEFTTGlWYmV2ZTFxdmEzaEVPMkE0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280327),('PjVdpTCkCYsryQqzI15Hnls4kLLzaZdJc1Euywt0',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJkekNpTEwzMWxCZFF6YmFLRTZpQ0kwVGtMcVF3NlF5MnM4aHlUcTc1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274710),('PKGeo83hnyKXXdF8yqroXm4gM1z0PIAVxpkqbXAZ',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI2alloYU84WHU1QXBQWVl5Q0RUUzJsSk45QTZIM3JveGQ4YjBHUkxkIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279811),('PN9IjpjgO7NuNxjHxc9Zvv57hmK6Pf2F8K4gpq0v',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJLa1FRWUtMbUN1MTZ0ZW1jSGd0dHlVRmZqRjBUSUJTRmxGdEFOMm5hIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277702),('PqPLz1dyCrokE5k9BOibaqIEx9ZVXp6IcJ46DYK4',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ5TklwbEVjMnpHVGhqc0ZYcFNaUWVrOFptN2R0WlBSWjNxUnB5Z0pMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279905),('pvnHgzWs2BCOwXUbIkyx1pZ4XArXLmayNruG2WvK',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJkRDZISXZWSGl1MHFXNE5KZHptYXJ5clVqY29WYTZvbHo5alpnQUFHIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cHM6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvcHJvZHVjdHMifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1784341619),('q9eypI7CuiBBIFThoTTHb9LpXNUh8lHXn0Hdh1fl',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJBS3IxcGk5ZnV6RUs1RXNJZUVpT2tCc1dkZlR1Yjd1RjI0b1ViYk5KIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280139),('QcFsgKBtI55wLcTHna62d6UtCupy2Jx3BooKgDLS',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJVbTZCd0RMUUlVVXh6cDd1Y1JzSDhQSGVRMnhwajdTZ0hpclJFamFpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277682),('QeN2S0aUJJhpDTFq7uFUpHJMzzHI4XPi5lv0sL8i',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ6V254Zm9KWGtPVnR0cDNic2FqMDgzbXlYWnVha0dBR2lqM1ZjamlVIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280040),('qFDtbMkPuI7UR1uwYMiF95OWX9yKM5Nctcxvqpiv',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJJWldYQW5VRWpCTVNlSWNZSW5iUkppY0s1ZmYwSURzanZzaXNGTkNGIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784275377),('qRHKXdaCBHfwpomjjVtY3vvvUMf9wEBNCW8BP4o8',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJOcWhNa0RDZFlibENkODFzMUF0bEM3dko2UzU1ckJISU03Wnl1TXNKIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277988),('ReyeAm8FXv8liam3uVGsE4RTw04BI8xQbW2ThYZk',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI4MGlwWHNJMFdJTFlyMWpsUlNyRVhDOHdkcGV5TVhqUDBGUGhYSW0wIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278471),('rgpWc5tO5qnSllcTBIUKVVDRrvg9kXQGj6svFuMy',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJRZUk1T3dRQnFZaDJiNjJGNGVmSWNGTXBuaU9mUHN4dGwyc0l0cjAxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274341),('rnZ80COpKprAihiW91yVk7I6e3uEPhUJFeGSzPZH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJuN2c3TngxdWQwWnNYUWF1UUZabWE0SXE1TEQxYnM3SkoyOWlPNTR1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278225),('S71KVzEAQgQUg7HJq4GdMYwZfpdL8omTDE6w9Eae',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJyeTRHWU5qUktWdFBWT0pZNGNnUnFydzk2dHdtTG90QzcyakJsb0JLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279998),('SjcKlkp81qfGuyq7uy7FCLOqHMpvRukOpUL4wSsj',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJnajJOelYyQ2laSlZxa3ZGd1ZrTXZKUlZidk1XN25YTjhlWmhBYzN4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784276722),('SsZVjqyBbylzhA6Uv7n4Duotc8dxUZKKpUjilJve',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJPOXBaR3NQeFRycnh0bktDUm13SkRHVTdCb3JoVjRRcVpNdkg2SEJ5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280186),('THY0PUO8KZ1MZ6h3l5GKvHBPyuEpK2wSKOeXmzKI',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI5VnJjV0N2d0dVNkt6ZVByYzQ3OVg4OHN0N3pFdW5GN1E0aFYzUk1aIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279587),('TjVekWdGnePT2HPwnQ8AhzRhq3JFCO7DF4alaiar',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJxWnN2QnVHUjJiZzVHSDlBQmlIMHRjdEthSzJIR0pRS09TZ01xSW40IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280158),('TrdUcCeXMvqhPJIEzULygt5MCtARcZ53vm1ralOp',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJRMENEN2N1NHpsMmY2ZjJ3RzVwdkVJZ3NJOGpWNVdxVFhlaEFlemYyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280151),('TtOYyI2cTR6EY4l0svDhyUb168NzH6CZDHCatdbE',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJyZURyNWVZbnkzUjhrYzZYZlRtUnpWSUtNR0dNQnFnNXo3VlZYSlljIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280477),('u6H6xqnD1q8VuAcFXEYwCca9Pz43DuJ3x9kh8mzg',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ6WUV1Nkw3c3FrQTRnaVZCdGxSQ3VzWnRJT1g3aHhpMnlHY2t6WkdYIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279218),('ueNkmk77R4QSol23wkPbXz1Uv3hWayaWUWazT502',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJFTGxmSWVOb21jc0F6Y0czSnhWRUxSNzJ4aDc1MVhLUEo0WHJ2aklTIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL3JldGFpbGVyIiwicm91dGUiOiJyZXRhaWxlci5ob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278653),('UurIDjNe3UyTxhNNWqN1pkca8HjxoADnfEBoACSc',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJIVXlJMWhIT1ZjM050bEJYM1VNWVBvUzBqZ1NPUnhIRUJaSmJZQ0ZRIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277637),('uwyyQksU6nTyujWEzeJruRQEAZcpQKyDGn5awOAd',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJCRzNNamNIY1cxQUlxQTRPM1VpQmg3MkJObzlxQ0UxMUpGR3VINjE3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280480),('V3Mvm9NcCnt13Dh086f2f8zM2Ot5eQyUNXfCCq68',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJKclR2a09ydWZxSVdUUVVKdTVNUU03bHJaUXZhY0hhdzRlUlNRWFIzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280482),('V6ZVuKStWJ6bKjK2ncSto0ML3zDXKNkqN2YkqSIa',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJEY2RmQnNBRlNxdjU2NmJZaDNURjR1cVgzUmZIWjlrWGtnQkdLNUI4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278278),('VH8y7PZhSe0y4GKkPgrlK2LGCwgNMfkWWHcuKE9A',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJjVmFhVWZORjNRa09ET1hEcDYxQTlmeG1DUDRHQXRvd3U0ck5pMWhGIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279685),('vinOz7Qxalg4Dz70qgSx0qC0QRWrSPS8i3PFwHA4',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJQOFRRYVpoWHhJSmh4MnVzWjdiU25JRHFYWDlwYnB4MUx0QXN0UVFmIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784276159),('vJjhGb85aetoOrwDKp3A7ez76Bcme2aLNLcYAv8I',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJZcUVKOVBObVFxeXAxbzB2TjZlWEN5ZWVJazBsb3pQaTlIeGdXRjBNIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278346),('vL1JILtL4ASPCqm5CRoZ0mbvFBDu0XDIppdqP3mD',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiIyZk54ajZEYUc4Y1FmYVV2WVR5NDlxTWh6REF3Z25SVlhLRHgxdkdrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784276312),('VwOzim6kJQYGNXxHlIwKmlOH4Khu4Lv2OBMZnUiX',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJYY2NnZjhIZ0xFczgwN0psUXRHcjVWREw2aHh5WVlCZ2RHY2VvQmJaIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274497),('WB9OEHKVmRpRmHqMgXCThoH0IJB6eNd96rHZhCbX',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiI5M0k3enlDT0lIcjNZN2VkTUpMYlVQN2NJenJKZ0JXOUpJT3VFVDFuIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280221),('wuxeONJ93QBgPbHMb182aeZaSAPfCTSqiJrv5DJF',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJsRWVjeE9Tc3MyYllJaHMySVJDY1dLNkdVekFxbTE0ZGFlODdSTTNuIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278653),('XiTJd5TIFFhHlHkTIEFEOPDQQBvb54N7nSggFTvm',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJhZzI0WTNib1FHc2tmVDEydkE1d0JVOTlIaVROelcxRzVmRmNnRG5UIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784275871),('xmKTVmzXaNll7pTEwUEYinLp9pk54oh5Ulks0AMY',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJGTFdzQ2Z5VDVDdmJnbmJ0Q2Z5MFJQcXZ2Q3hkNmlFajl4Tm9DaGRUIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784276626),('xpcNnjfSfFLIAlxGsWj9AwrkuaSVXTh2p0hSm43k',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJtcXhrNTV1WnIwQ2R6cWJrdTA0UjFJbWtGWkdYTmRmMlA2bGY2N0p4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279998),('XqSYzegU4nYBzCgh14ZdqySrJa4QXi5ZgnyTUFi7',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJ0U2xzeFJ1a0wzQU1qdFhaQmpDclQ1U2tab2ZvakdvYjVtRlBGbFI3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280189),('XTp8cZtBfUAHQR8kYWJwtvAmqj3Xp7A10PKLbZvj',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJnMEpkd2dVd1ljdVgyb2ZVVndnYmd6b1FiWkdKWWg4VmZ5Q2s0dXd2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274822),('xuHHX1hA4K28uqZzdZJYm0WVO25C4AOQ6fWzgbOt',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJLUzl4TDBVZXA0dGtUOVRKSkplVVdMandMa1N0V1dTOHJkWDhkcHBaIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279197),('xX9WlHXVrfNhTq8SXjcCpy8tY9UUNgAqmmekBcjJ',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJQS2NYd0xjSDhVdGU1UEc2dm4zUkRLbmpyMk1Sb2V3YUJ2WHhvSXgzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280202),('xzE1Ft7rN74HGdyI1xAH1JrXp8maH3HgDKZyqztG',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJGa3U3cDltTjZwTlRjamp6cWpXanlZUkk4V2pBUnA5dlFod1lTY2NNIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784280170),('ynImul5CiVeJm84JT6wCBCzuiUiWUK6i26Fj1mdI',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJjWGE2VlBKZ2JMNlJadVlxbmwwRU0yTnBvYWMxV3JDSUFPNndza0FrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784274836),('YXiFb3FgAAN5fwSNjhlrN8zrQa9OxhDIb60yJkX0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI3dE40SlJITGNnbTBmV1lmZDBvdzdkeHNnQlh4Nmw0b3RjVDlhMXBXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784276320),('YxyBaEJj22AjTNjHosRJbCUu3WauHf9900QRPyTf',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJRT25tQldKVTZ1T3lPNU9vbGx1SXR6OTZoVUNVR0FFZ3dsNjFLcExXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3dob2xlc2FsZWJpbGxhcHAudGVzdFwvbWFuaWZlc3QuanNvbiIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1784275202),('z2KTP0fOHmymoOd18fDRJZD9JE1GLfwNVTMjoApH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI1SmphS2tXMWxVNXBTNVVZbnUxdFY3R3ZheGlWZUtiNWlPdFZpQWFjIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0LXJldGFpbGVyLmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784278473),('zusIIS7HMkBpWEl9q3WSs015qyJxPBgNQJq9GMcH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJkbmthYjFMZzVDTE9XYTBYNlBCSzhTZ0VDTldPR2R4V2VBTjVzajBJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784279257),('ZzVaRULBRHSgfLveoYEB0wrsrKM1ejY7hVoLT4GK',NULL,'127.0.0.1','Mozilla/5.0 (Linux; Android 15; Pixel 9) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36','eyJfdG9rZW4iOiJjb2tBNHVyZXg2YVBhanBvanNDTEpYNlRIZzRkblgzazRSZVNoWGdBIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93aG9sZXNhbGViaWxsYXBwLnRlc3RcL21hbmlmZXN0Lmpzb24iLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1784277826);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('allow_negative_stock','1','2026-07-15 02:46:35','2026-07-15 03:21:07'),('bank_account','9999999999','2026-07-14 21:11:28','2026-07-14 21:11:28'),('bank_holder','XYZ Wholesale Store','2026-07-14 21:11:28','2026-07-14 21:11:28'),('bank_ifsc','HDFC0000064','2026-07-14 21:11:28','2026-07-14 21:11:28'),('bank_name','HDFC Bank','2026-07-14 21:11:28','2026-07-14 21:11:28'),('firm_address',NULL,'2026-07-14 21:11:28','2026-07-14 21:11:28'),('firm_alt_mobile',NULL,'2026-07-14 21:11:28','2026-07-14 21:11:28'),('firm_gst','27AHHJKUYT09TN','2026-07-14 21:11:28','2026-07-14 21:14:50'),('firm_mobile','9999999999','2026-07-14 20:51:57','2026-07-14 20:51:57'),('firm_name','XYZ Wholesale Store','2026-07-14 20:51:57','2026-07-14 20:51:57'),('print_payment','0','2026-07-14 21:29:57','2026-07-14 21:29:57'),('print_projection','0','2026-07-14 21:29:57','2026-07-14 21:29:57'),('upi_id','9999999999@ybl','2026-07-14 21:11:28','2026-07-14 21:11:28');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_bills`
--

DROP TABLE IF EXISTS `supplier_bills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_bills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `bill_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bill_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_bills_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `supplier_bills_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_bills`
--

LOCK TABLES `supplier_bills` WRITE;
/*!40000 ALTER TABLE `supplier_bills` DISABLE KEYS */;
INSERT INTO `supplier_bills` VALUES (1,1,'TB-4521','2026-07-16',12500.00,'july stock','2026-07-15 21:41:52','2026-07-15 21:41:52'),(2,1,NULL,'2026-07-16',3000.00,NULL,'2026-07-15 21:42:28','2026-07-15 21:42:28'),(3,3,'NA-5025','2025-06-10',11000.00,'Opening season stock','2026-07-15 23:58:05','2026-07-15 23:58:05'),(4,3,'NA-2946','2025-09-05',16000.00,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(5,3,'NA-7567','2025-12-12',11000.00,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(6,3,'NA-6094','2026-03-08',8500.00,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(7,3,'NA-5416','2026-05-20',11000.00,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(8,3,'NA-8357','2026-07-02',11000.00,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(9,4,'PU-5152','2025-08-15',17000.00,'Opening season stock','2026-07-15 23:58:05','2026-07-15 23:58:05'),(10,4,'PU-4783','2026-01-10',22500.00,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(11,4,'PU-1545','2026-06-05',6000.00,NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05');
/*!40000 ALTER TABLE `supplier_bills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_payments`
--

DROP TABLE IF EXISTS `supplier_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_payments_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_payments`
--

LOCK TABLES `supplier_payments` WRITE;
/*!40000 ALTER TABLE `supplier_payments` DISABLE KEYS */;
INSERT INTO `supplier_payments` VALUES (1,1,'2026-07-16',10000.00,'upi','UTR12345',NULL,'2026-07-15 21:42:59','2026-07-15 21:42:59'),(2,3,'2025-09-24',16000.00,'upi','UTR920544',NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(3,3,'2025-12-27',11000.00,'upi','UTR148989',NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(4,3,'2026-06-12',11000.00,'bank','UTR601879',NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(5,4,'2025-09-12',17000.00,'bank','UTR544231',NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(6,4,'2026-01-31',22500.00,'cheque','UTR278965',NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05'),(7,4,'2026-06-15',6000.00,'upi','UTR890000',NULL,'2026-07-15 23:58:05','2026-07-15 23:58:05');
/*!40000 ALTER TABLE `supplier_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `firm_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gst_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_mobile_unique` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'ABC Stockist','Suresh','6666666666','27AHGPD0660QD',NULL,1,'2026-07-15 20:36:00','2026-07-15 20:36:00'),(2,'Parle Distributor',NULL,'5555555555',NULL,'Nashik',1,'2026-07-15 20:36:39','2026-07-15 20:36:39'),(3,'Nashik FMCG Distributors','Prakash Jain','9000000011','27AABCN4321Q1Z8','MIDC, Nashik',1,'2026-07-15 23:58:04','2026-07-15 23:58:04'),(4,'Pune Wholesale Agency','Deepak Kulkarni','9000000012',NULL,'Market Yard, Pune',1,'2026-07-15 23:58:05','2026-07-15 23:58:05');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_mobile_unique` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'XYZ Wholesale Store','9999999999','2026-07-14 03:36:06','2026-07-14 03:36:06');
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

-- Dump completed on 2026-07-18  8:23:27
