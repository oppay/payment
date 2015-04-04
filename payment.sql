CREATE DATABASE  IF NOT EXISTS `payment` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_persian_ci */;
USE `payment`;
-- MySQL dump 10.13  Distrib 5.6.13, for Win32 (x86)
--
-- Host: localhost    Database: payment
-- ------------------------------------------------------
-- Server version	5.6.15

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `purchase`
--

DROP TABLE IF EXISTS `purchase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gateway` varchar(15) COLLATE utf8_persian_ci DEFAULT NULL,
  `amount` int(10) unsigned DEFAULT NULL,
  `token` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `receipId` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `traceId` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `requestCode` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `redirectCode` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  `verifyCode` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase`
--

LOCK TABLES `purchase` WRITE;
/*!40000 ALTER TABLE `purchase` DISABLE KEYS */;
INSERT INTO `purchase` VALUES (1,'Saman',1000,NULL,NULL,NULL,NULL,NULL,NULL),(2,'Saman',1000,'AV0xNTOQ1vZN3V+jm1bkSvne+JJ5Mq',NULL,NULL,NULL,NULL,NULL),(3,'Saman',1000,'AV0xNTOQ1vZN3V+jm1bkSmmxwcx3pm',NULL,NULL,NULL,NULL,NULL),(4,'Saman',1000,'AV0xNTOQ1vZN3V+jm1bkSvgQvYC7n7',NULL,NULL,NULL,NULL,NULL),(5,'Saman',1000,'AV0xNTOQ1vZN3V+jm1bkSo0bsGnNiJ',NULL,NULL,NULL,NULL,NULL),(6,'Saman',1000,'AV0xNTOQ1vZN3V+jm1bkSmijlXn7jC',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `purchase` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-04-05  1:28:03
