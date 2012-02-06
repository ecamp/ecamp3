-- MySQL dump 10.13  Distrib 5.5.9, for osx10.6 (i386)
--
-- Host: localhost    Database: eCamp3dev
-- ------------------------------------------------------
-- Server version	5.5.9

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
-- Table structure for table `camps`
--

DROP TABLE IF EXISTS `camps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `camps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator_id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(32) NOT NULL,
  `title` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_name_unique` (`group_id`,`name`),
  UNIQUE KEY `owner_name_unique` (`owner_id`,`name`),
  KEY `camps_creator_id_idx` (`creator_id`),
  KEY `camps_owner_id_idx` (`owner_id`),
  KEY `camps_group_id_idx` (`group_id`),
  CONSTRAINT `camps_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`),
  CONSTRAINT `camps_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `camps_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `camps`
--

LOCK TABLES `camps` WRITE;
/*!40000 ALTER TABLE `camps` DISABLE KEYS */;
INSERT INTO `camps` VALUES (3,7,7,NULL,'2011-11-23 10:29:06','2011-11-23 10:29:06','asdfasdf','asdf'),(4,7,7,NULL,'2011-11-25 12:53:57','2011-11-25 12:53:57','asdfqwer','asdf'),(6,7,NULL,249,'2011-11-26 20:18:59','2011-11-26 22:01:04','sola12','SoLa 2012'),(7,7,7,NULL,'2011-11-26 21:14:26','2011-11-26 21:14:26','mycamp','My Camp');
/*!40000 ALTER TABLE `camps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `days`
--

DROP TABLE IF EXISTS `days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subcamp_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `dayOffset` int(11) NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `offset_subcamp_idx` (`dayOffset`,`subcamp_id`),
  KEY `days_subcamp_id_idx` (`subcamp_id`),
  CONSTRAINT `days_ibfk_1` FOREIGN KEY (`subcamp_id`) REFERENCES `periods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `days`
--

LOCK TABLES `days` WRITE;
/*!40000 ALTER TABLE `days` DISABLE KEYS */;
/*!40000 ALTER TABLE `days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_instances`
--

DROP TABLE IF EXISTS `event_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_instances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `subcamp_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `minOffset` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_instances_event_id_idx` (`event_id`),
  KEY `event_instances_subcamp_id_idx` (`subcamp_id`),
  CONSTRAINT `event_instances_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  CONSTRAINT `event_instances_ibfk_2` FOREIGN KEY (`subcamp_id`) REFERENCES `subcamps` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_instances`
--

LOCK TABLES `event_instances` WRITE;
/*!40000 ALTER TABLE `event_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_instances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `camp_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `title` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `events_camp_id_idx` (`camp_id`),
  KEY `events_user_id_idx` (`user_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`),
  CONSTRAINT `events_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(64) NOT NULL,
  `imageMime` varchar(32) DEFAULT NULL,
  `imageData` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_parent_name_unique` (`parent_id`,`name`),
  KEY `groups_parent_id_idx` (`parent_id`),
  KEY `group_name_idx` (`name`),
  CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=711 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,NULL,'2011-03-15 20:00:40','2011-03-15 20:00:43','pbs','Pfadi Bewegung Schweiz',NULL,NULL),(2,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','aargau','Pfadi Aargau',NULL,NULL),(3,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','adler','Adler Aarau',NULL,NULL),(4,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','baregg','Baregg Baden',NULL,NULL),(5,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','bighorn','Pfadi Big Horn Lengnau AG',NULL,NULL),(6,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','blaustein','Blaustein Gränichen',NULL,NULL),(7,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','burghorn','Pfadi Burghorn Wettingen',NULL,NULL),(8,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','schoeftle','Pfadi Schöftle Schöftland',NULL,NULL),(9,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','erdmaennli','Erdmännli Wallbach',NULL,NULL),(10,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','habsburg','Habsburg Brugg AG',NULL,NULL),(11,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','hallwyl','Pfadi Hallwyl, Seetal (Seengen)',NULL,NULL),(12,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','heitersberg','Heitersberg Oberrohrdorf',NULL,NULL),(13,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','hochwacht','Hochwacht Baden',NULL,NULL),(14,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','jonen','Pfadi Jonen',NULL,NULL),(15,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','gofers','Gofers Lenzburg',NULL,NULL),(16,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','riko','Riko Spreitenbach',NULL,NULL),(17,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','wohle','Wohle',NULL,NULL),(18,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','rothburg','Rothburg Rothrist/Aarburg',NULL,NULL),(19,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','sins','Sins',NULL,NULL),(20,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','sodales','Sodales Oberrohrdorf',NULL,NULL),(21,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgeorg_aarau','St. Georg Aarau',NULL,NULL),(22,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgeorg_safenwil','St.Georg Safenwil',NULL,NULL),(23,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','stpeter','St. Peter Nussbaumen',NULL,NULL),(24,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','thierstein','Thierstein Stein AG',NULL,NULL),(25,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','vindonissa','Vindonissa Brugg AG',NULL,NULL),(26,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','barracuda','Barracuda (Wildegg)',NULL,NULL),(27,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta','PTA Aargau',NULL,NULL),(28,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','zofingen','Zofingen',NULL,NULL),(29,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','rymenzburg','Rymenzburg',NULL,NULL),(30,29,'2011-03-15 20:11:00','2011-03-15 20:11:00','kulm','Rymenzburg Kulm',NULL,NULL),(31,29,'2011-03-15 20:11:00','2011-03-15 20:11:00','von_flue','Von Flüe Menziken',NULL,NULL),(32,29,'2011-03-15 20:11:00','2011-03-15 20:11:00','wyna','Rymenzburg Wyna',NULL,NULL),(33,2,'2011-03-15 20:11:00','2011-03-15 20:11:00','alphacentauri','Alpha Centauri Mutschellen',NULL,NULL),(34,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','bern','Pfadi Kanton Bern',NULL,NULL),(35,34,'2011-03-15 20:11:00','2011-03-15 20:11:00','baeretatze','Bäretatze',NULL,NULL),(36,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','auguet','Auguet Muri-Gümligen',NULL,NULL),(37,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta','PTA Bern',NULL,NULL),(38,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','fliehburg','Fliehburg Rüfenacht BE',NULL,NULL),(39,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','frienisberg','Pfadi Abteilung Frienisberg',NULL,NULL),(40,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','riedburg','Riedburg Bern',NULL,NULL),(41,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','chatzestyg','Chatzestyg Bolligen-Ittigen',NULL,NULL),(42,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','steibruch','Steibruch Ostermundigen',NULL,NULL),(43,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','falkenstein','Pfadi Falkenstein Köniz',NULL,NULL),(44,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','aaredrache','Aaredrache Bern',NULL,NULL),(45,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','grauholz','Grauholz',NULL,NULL),(46,35,'2011-03-15 20:11:00','2011-03-15 20:11:00','waerrenfels','Wärrenfels Belp',NULL,NULL),(47,34,'2011-03-15 20:11:00','2011-03-15 20:11:00','oberland','Oberland',NULL,NULL),(48,47,'2011-03-15 20:11:00','2011-03-15 20:11:00','muelistei','Pfadi Mülistei, Stettlen-Vechigen',NULL,NULL),(49,47,'2011-03-15 20:11:00','2011-03-15 20:11:00','kyburg','Verband Pfadi Kyburg Thun',NULL,NULL),(50,49,'2011-03-15 20:11:00','2011-03-15 20:11:00','nuenenen','Pfadi Nünenen Uetendorf',NULL,NULL),(51,49,'2011-03-15 20:11:00','2011-03-15 20:11:00','berchtold','Ritter Berchtold Thun',NULL,NULL),(52,49,'2011-03-15 20:11:00','2011-03-15 20:11:00','drachenburg','Drachenburg Steffisburg',NULL,NULL),(53,47,'2011-03-15 20:11:00','2011-03-15 20:11:00','stchristopherus','St.Christophorus Brienz-Meiring',NULL,NULL),(54,47,'2011-03-15 20:11:00','2011-03-15 20:11:00','buebebaerg','Stärn vo Buebebärg Spiez und Umgebung',NULL,NULL),(55,47,'2011-03-15 20:11:00','2011-03-15 20:11:00','virus','Virus Thun',NULL,NULL),(56,47,'2011-03-15 20:11:00','2011-03-15 20:11:00','unspunne','Pfadiabteilung Unspunne Interlaken',NULL,NULL),(57,47,'2011-03-15 20:11:00','2011-03-15 20:11:00','wendelsee','Pfadi Wendelsee Hünibach-Hilterfingen-Oberhofen',NULL,NULL),(58,34,'2011-03-15 20:11:00','2011-03-15 20:11:00','sense','Sense-Seeland',NULL,NULL),(59,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','aarewacht','Pfadi Aarewacht Lyss',NULL,NULL),(60,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','aquila','Pfadi Aquila Aarberg',NULL,NULL),(61,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','rakataiana','Rakataiana Biel',NULL,NULL),(62,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','buchsi','Buchsi Münchenbuchsee',NULL,NULL),(63,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','gottstatt','Pfadi Gottstatt Orpund',NULL,NULL),(64,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','hasenburg','Hasenburg Täuffelen-Ins',NULL,NULL),(65,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','jura','Jura Biel',NULL,NULL),(66,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','mistral','Lindenburg-Mistral Laupen BE',NULL,NULL),(67,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','tornado','Lindenburg-Tornado Neuenegg',NULL,NULL),(68,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','orion','Orion Biel',NULL,NULL),(69,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','schekka','Schekka Schönbühl-Jegenstorf',NULL,NULL),(70,58,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta_biel','PTA Biel',NULL,NULL),(71,34,'2011-03-15 20:11:00','2011-03-15 20:11:00','oberaargau','Untere Emme-Oberaargau',NULL,NULL),(72,71,'2011-03-15 20:11:00','2011-03-15 20:11:00','burgdorf','Burgdorf-Stadt',NULL,NULL),(73,71,'2011-03-15 20:11:00','2011-03-15 20:11:00','dreilinden','Drei Linden',NULL,NULL),(74,71,'2011-03-15 20:11:00','2011-03-15 20:11:00','landshut','Landshut Bätterkinden',NULL,NULL),(75,71,'2011-03-15 20:11:00','2011-03-15 20:11:00','lubra','Lubra Sumiswald',NULL,NULL),(76,71,'2011-03-15 20:11:00','2011-03-15 20:11:00','siwa','Siwa Roggwil Wynau Aarwangen',NULL,NULL),(77,71,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgeorg','St. Georg Burgdorf - Lyssach',NULL,NULL),(78,71,'2011-03-15 20:11:00','2011-03-15 20:11:00','herzogenbuchsee','Pfadi Herzogenbuchsee',NULL,NULL),(79,71,'2011-03-15 20:11:00','2011-03-15 20:11:00','turmfalken','Pfadi Turmfalken Kirchberg',NULL,NULL),(80,34,'2011-03-15 20:11:00','2011-03-15 20:11:00','zytglogge','Bezirk Zytglogge',NULL,NULL),(81,80,'2011-03-15 20:11:00','2011-03-15 20:11:00','badenpowell','Baden-Powell Bern',NULL,NULL),(82,80,'2011-03-15 20:11:00','2011-03-15 20:11:00','zoltan_kodaly','Zoltan Kodaly (ungarische Pfadi)',NULL,NULL),(83,80,'2011-03-15 20:11:00','2011-03-15 20:11:00','windroesli','Korps Windrösli',NULL,NULL),(84,83,'2011-03-15 20:11:00','2011-03-15 20:11:00','frisco','Pfadi Frisco',NULL,NULL),(85,83,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjosef','St. Josef Köniz',NULL,NULL),(86,83,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmarien','St. Marien Bern',NULL,NULL),(87,83,'2011-03-15 20:11:00','2011-03-15 20:11:00','schwarzenburg','Schwarzenburg',NULL,NULL),(88,34,'2011-03-15 20:11:00','2011-03-15 20:11:00','obereemme','Obere Emme',NULL,NULL),(89,88,'2011-03-15 20:11:00','2011-03-15 20:11:00','chutze','Chutze Aaretal',NULL,NULL),(90,88,'2011-03-15 20:11:00','2011-03-15 20:11:00','worb','Worb',NULL,NULL),(91,88,'2011-03-15 20:11:00','2011-03-15 20:11:00','langnau','Hochwacht Langnau',NULL,NULL),(92,88,'2011-03-15 20:11:00','2011-03-15 20:11:00','kuonolf','Kuonolf Konolfingen',NULL,NULL),(93,34,'2011-03-15 20:11:00','2011-03-15 20:11:00','schwyzerstaern','Corps Schwyzerstärn',NULL,NULL),(94,93,'2011-03-15 20:11:00','2011-03-15 20:11:00','inka','Inka Bremgarten b.Bern',NULL,NULL),(95,93,'2011-03-15 20:11:00','2011-03-15 20:11:00','maya','Maya Bern',NULL,NULL),(96,93,'2011-03-15 20:11:00','2011-03-15 20:11:00','sparta','Sparta Bern',NULL,NULL),(97,93,'2011-03-15 20:11:00','2011-03-15 20:11:00','tuareg','Tuareg Bern-Kehrsatz',NULL,NULL),(98,93,'2011-03-15 20:11:00','2011-03-15 20:11:00','wiking','Wiking',NULL,NULL),(99,34,'2011-03-15 20:11:00','2011-03-15 20:11:00','patria','Pfadicorps Patria Bern',NULL,NULL),(100,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','basel','Pfadi Region Basel',NULL,NULL),(101,100,'2011-03-15 20:11:00','2011-03-15 20:11:00','johanniter','Johanniter',NULL,NULL),(102,101,'2011-03-15 20:11:00','2011-03-15 20:11:00','angenstein','Angenstein Aesch',NULL,NULL),(103,101,'2011-03-15 20:11:00','2011-03-15 20:11:00','blauen','Blauen Basel',NULL,NULL),(104,101,'2011-03-15 20:11:00','2011-03-15 20:11:00','rychestei','Rychestei Arlesheim',NULL,NULL),(105,101,'2011-03-15 20:11:00','2011-03-15 20:11:00','sunnebaerg','Pfadiabteilung Sunnebärg',NULL,NULL),(106,101,'2011-03-15 20:11:00','2011-03-15 20:11:00','waldchutz','Waldchutz Biel-Benken BL',NULL,NULL),(107,101,'2011-03-15 20:11:00','2011-03-15 20:11:00','3tannen','3 Tannen',NULL,NULL),(108,101,'2011-03-15 20:11:00','2011-03-15 20:11:00','schnaeggebaerg','Schnäggebärg (Oberwil)',NULL,NULL),(109,100,'2011-03-15 20:11:00','2011-03-15 20:11:00','laufental','Laufental',NULL,NULL),(110,109,'2011-03-15 20:11:00','2011-03-15 20:11:00','helvetia','Helvetia Laufen',NULL,NULL),(111,109,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmartin','St.Martin Laufen',NULL,NULL),(112,109,'2011-03-15 20:11:00','2011-03-15 20:11:00','birrsprung','Birssprung',NULL,NULL),(113,100,'2011-03-15 20:11:00','2011-03-15 20:11:00','rheinbund','Rheinbund',NULL,NULL),(114,113,'2011-03-15 20:11:00','2011-03-15 20:11:00','rheinfelden1','Pfadi Rheinfelden',NULL,NULL),(115,113,'2011-03-15 20:11:00','2011-03-15 20:11:00','thierstein','Thierstein Möhlin',NULL,NULL),(116,113,'2011-03-15 20:11:00','2011-03-15 20:11:00','rheinfelden2','Rheinfelden',NULL,NULL),(117,113,'2011-03-15 20:11:00','2011-03-15 20:11:00','propatria','Pro Patria Basel',NULL,NULL),(118,100,'2011-03-15 20:11:00','2011-03-15 20:11:00','raurica','Raurica',NULL,NULL),(119,118,'2011-03-15 20:11:00','2011-03-15 20:11:00','adler','Pfadi Adler Pratteln',NULL,NULL),(120,118,'2011-03-15 20:11:00','2011-03-15 20:11:00','farnsburg','Farnsburg',NULL,NULL),(121,118,'2011-03-15 20:11:00','2011-03-15 20:11:00','liestal','Liestal',NULL,NULL),(122,118,'2011-03-15 20:11:00','2011-03-15 20:11:00','rinau','Rinau Kaiseraugst',NULL,NULL),(123,118,'2011-03-15 20:11:00','2011-03-15 20:11:00','gelterkinden','Gelterkinden-Sissach',NULL,NULL),(124,118,'2011-03-15 20:11:00','2011-03-15 20:11:00','waldenburgertal','Waldenburgertal Niederdorf',NULL,NULL),(125,100,'2011-03-15 20:11:00','2011-03-15 20:11:00','zytroeseli','Zytröseli',NULL,NULL),(126,125,'2011-03-15 20:11:00','2011-03-15 20:11:00','bischofstein','Bischofstein Basel',NULL,NULL),(127,125,'2011-03-15 20:11:00','2011-03-15 20:11:00','falkenstein','Falkenstein Basel',NULL,NULL),(128,125,'2011-03-15 20:11:00','2011-03-15 20:11:00','gutenfels','Gutenfels Basel',NULL,NULL),(129,125,'2011-03-15 20:11:00','2011-03-15 20:11:00','schalberg','Schalberg Basel',NULL,NULL),(130,125,'2011-03-15 20:11:00','2011-03-15 20:11:00','ramstein','Ramstein Basel',NULL,NULL),(131,125,'2011-03-15 20:11:00','2011-03-15 20:11:00','schenkenberg','Schenkenberg Basel',NULL,NULL),(132,100,'2011-03-15 20:11:00','2011-03-15 20:11:00','kpk','KPK Basel',NULL,NULL),(133,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stalban','St.Alban Basel',NULL,NULL),(134,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','baerenfels','Pfadi Bärenfels 1941',NULL,NULL),(135,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stbenno','Pfadi St. Benno',NULL,NULL),(136,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','blauenstein','Blauenstein Basel',NULL,NULL),(137,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stbrandan','St.Brandan Basel',NULL,NULL),(138,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stfridolin','St.Fridolin Allschwil',NULL,NULL),(139,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stheinrich','St.Heinrich',NULL,NULL),(140,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stleodegar','St. Leodegar',NULL,NULL),(141,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmartin','St.Martin Basel',NULL,NULL),(142,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmauritius','St.Mauritius Dornach',NULL,NULL),(143,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','moenchsberg','Mönchsberg Pfeffingen',NULL,NULL),(144,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','muencheinstein','Münchenstein',NULL,NULL),(145,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stragnachar','St. Ragnachar, Riehen',NULL,NULL),(146,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','sturs','Pfadi St. Urs Basel',NULL,NULL),(147,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','wildenstein','Wildenstein Oberwil',NULL,NULL),(148,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','stnikolaus','Pfadi Reinach St.Nikolaus',NULL,NULL),(149,132,'2011-03-15 20:11:00','2011-03-15 20:11:00','koinos','Pfadi Koinos',NULL,NULL),(150,100,'2011-03-15 20:11:00','2011-03-15 20:11:00','basilisk','Basilisk',NULL,NULL),(151,150,'2011-03-15 20:11:00','2011-03-15 20:11:00','dalbedoor','Dalbedoor Basel',NULL,NULL),(152,150,'2011-03-15 20:11:00','2011-03-15 20:11:00','rieche','Mäitlipfadi Rieche',NULL,NULL),(153,150,'2011-03-15 20:11:00','2011-03-15 20:11:00','herzberg','Herzberg Muttenz',NULL,NULL),(154,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','fribourg','Association Scouts Fribourgeois',NULL,NULL),(155,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','bulle','Scouts Bullois',NULL,NULL),(156,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','veveyz','Scouts Veveyse',NULL,NULL),(157,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','domdidier','Domdidier',NULL,NULL),(158,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','marly','Foucauld Marly',NULL,NULL),(159,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','murist','La Molière Murist',NULL,NULL),(160,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','stnicolas','St-Nicolas Fribourg',NULL,NULL),(161,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','stpierre','St-Pierre Fribourg',NULL,NULL),(162,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','straphael','St-Raphaël Romont FR',NULL,NULL),(163,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','glane','Villars sur Glâne',NULL,NULL),(164,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','vully','Vully',NULL,NULL),(165,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','xroi','X-Roi Fribourg',NULL,NULL),(166,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','maggenberg','Pfadi Maggenberg Freiburg',NULL,NULL),(167,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','stpeterundpaul','St.Peter und Paul Düdingen',NULL,NULL),(168,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','andromeda','Pfadi Andromeda Murten',NULL,NULL),(169,154,'2011-03-15 20:11:00','2011-03-15 20:11:00','troubardours','Les Troubadours Grolley',NULL,NULL),(170,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','geneve','Scouts Genevois',NULL,NULL),(171,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','flambeaux','Flambeaux Genève sistiert',NULL,NULL),(172,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','dunant','Henri Dunant Genève',NULL,NULL),(173,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','meyrin','Rhône-Jura Meyrin',NULL,NULL),(174,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','onex','Nouveau Monde Onex',NULL,NULL),(175,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','saintexupery','Antoine de Saint-Exupéry Vandœuvres',NULL,NULL),(176,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','durandal','Durandal Bernex',NULL,NULL),(177,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','benoit','Bénoît-de-Ponverre Bernex',NULL,NULL),(178,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','grandeourse','Grande Ourse Genève',NULL,NULL),(179,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','terreneuve','Terre-Neuve Genève',NULL,NULL),(180,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','montbrillant','Montbrillant Genève',NULL,NULL),(181,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','bourgdefour','Bourg de Four Genève',NULL,NULL),(182,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','chene','Chêne-Bourg',NULL,NULL),(183,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','roset','Michel Roset Genève',NULL,NULL),(184,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','rousseau','Jean-Jacques Rousseau Vernier',NULL,NULL),(185,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','perceval','Perceval Geneve',NULL,NULL),(186,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','lignon','Aire-Lignon Le Lignon (dissout)',NULL,NULL),(187,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','lancy','Philibert Berthelier Lancy',NULL,NULL),(188,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','carouge','Sainte-Croix Carouge',NULL,NULL),(189,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','tanganyika','Tanganyika',NULL,NULL),(190,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjoseph','St-Joseph Genève',NULL,NULL),(191,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','siksandor','Sik Sandor Collex',NULL,NULL),(192,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','jeanpiaget','Jean Piaget Genève',NULL,NULL),(193,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','bonivard','Bonivard Genève',NULL,NULL),(194,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','clantatanka','Clan Tatanka sistiert',NULL,NULL),(195,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','troinex','Scouts Troinex',NULL,NULL),(196,170,'2011-03-15 20:11:00','2011-03-15 20:11:00','clanroutier','Clan Routier GSICC',NULL,NULL),(198,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','glarus','Pfadi Glarus',NULL,NULL),(199,198,'2011-03-15 20:11:00','2011-03-15 20:11:00','glaernisch','Glärnisch Glarus',NULL,NULL),(200,198,'2011-03-15 20:11:00','2011-03-15 20:11:00','windegg','Pfadi Windegg Niederurnen',NULL,NULL),(201,198,'2011-03-15 20:11:00','2011-03-15 20:11:00','rauti','Rauti Näfels',NULL,NULL),(202,198,'2011-03-15 20:11:00','2011-03-15 20:11:00','toedi','Tödi Glarus',NULL,NULL),(203,198,'2011-03-15 20:11:00','2011-03-15 20:11:00','kaerpf','Kärpf Schwanden GL',NULL,NULL),(204,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','graubuenden','Pfadi Graubünden',NULL,NULL),(205,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','poschiavo','APE Poschiavo',NULL,NULL),(206,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','amedes','Amedes Domat/Ems',NULL,NULL),(207,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','capricorn','Battasendas Capricorn',NULL,NULL),(208,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','maitlapfadi_chur','Chur Maitlapfadi',NULL,NULL),(209,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','falkenstein','Falkenstein Landquart',NULL,NULL),(210,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','kobra','Kobra Larein Pragg-Jenaz',NULL,NULL),(211,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','libertas','Libertas St.Moritz',NULL,NULL),(212,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','ramoz','Ramoz Arosa',NULL,NULL),(213,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','rhaetikon','Rhätikon Schiers',NULL,NULL),(214,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','samedan','Battasendas Samedan',NULL,NULL),(215,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','davos','Pfadi Davos',NULL,NULL),(216,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','stluzi','St.Luzi Chur',NULL,NULL),(217,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','surses','Surses Albula',NULL,NULL),(218,204,'2011-03-15 20:11:00','2011-03-15 20:11:00','chur','Chur',NULL,NULL),(219,218,'2011-03-15 20:11:00','2011-03-15 20:11:00','falkenstein','Falkenstein Chur',NULL,NULL),(220,218,'2011-03-15 20:11:00','2011-03-15 20:11:00','ruchenberg','Ruchenberg Chur',NULL,NULL),(221,218,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta_ortenstein','PTA Ortenstein Chur',NULL,NULL),(222,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','jura','Association du Scoutisme Jurassien',NULL,NULL),(223,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','alpha_stfrancois','Alpha St-François Bienne',NULL,NULL),(224,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','erquel','Erguel St-Imier',NULL,NULL),(225,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','leroc_stgeorges','Le Roc St-Georges Bienne',NULL,NULL),(226,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','notredame','Notre-Dame de la Rte Delémont',NULL,NULL),(227,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','moutier','Perceval Moutier',NULL,NULL),(228,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','pierre_pertuis','Pierre-Pertuis Tavannes',NULL,NULL),(229,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgermain','St-Germain Courroux',NULL,NULL),(230,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','stlouis','St-Louis Saignelegier',NULL,NULL),(231,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmichel','St-Michel Delémont',NULL,NULL),(232,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','stnicolas','St-Nicolas de Flüe Vicques',NULL,NULL),(233,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','stpierre','St-Pierre Porrentruy',NULL,NULL),(234,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','courrendlin','St-Paul Courrendlin',NULL,NULL),(235,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','fahy','St. Paul Fahy',NULL,NULL),(236,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','neuveville','Tour de Rive La Neuveville',NULL,NULL),(237,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmaurice','St-Maurice Glovelier',NULL,NULL),(238,222,'2011-03-15 20:11:00','2011-03-15 20:11:00','smt_jura','SMT Jura',NULL,NULL),(239,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','luzern','Pfadi Luzern',NULL,NULL),(240,239,'2011-03-15 20:11:00','2011-03-15 20:11:00','luleu','Luzerner Leu',NULL,NULL),(241,240,'2011-03-15 20:11:00','2011-03-15 20:11:00','stanton','St. Anton Luzern',NULL,NULL),(242,240,'2011-03-15 20:11:00','2011-03-15 20:11:00','barfuesser','Pfadi Barfüesser Lozärn',NULL,NULL),(243,240,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjosef','St.Josef Luzern',NULL,NULL),(244,240,'2011-03-15 20:11:00','2011-03-15 20:11:00','stkarl','St. Karl Luzern',NULL,NULL),(245,240,'2011-03-15 20:11:00','2011-03-15 20:11:00','stleodegar','St.Leodegar Luzern',NULL,NULL),(246,240,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmichael','St.Michael Luzern',NULL,NULL),(247,240,'2011-03-15 20:11:00','2011-03-15 20:11:00','stpaul','St.Paul Luzern',NULL,NULL),(248,240,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjohannes','St.Johannes Luzern',NULL,NULL),(249,239,'2011-03-15 20:11:00','2011-03-15 20:11:00','musegg','Musegg',NULL,NULL),(250,249,'2011-03-15 20:11:00','2011-03-15 20:11:00','luegisland','Luegisland Luzern',NULL,NULL),(251,249,'2011-03-15 20:11:00','2011-03-15 20:11:00','noelliturm','Pfadi Musegg Abteilung Nölliturm Luzern',NULL,NULL),(252,249,'2011-03-15 20:11:00','2011-03-15 20:11:00','zytturm','Pfadi Musegg Abteilung Zyturm',NULL,NULL),(253,249,'2011-03-15 20:11:00','2011-03-15 20:11:00','ufertrupp','Ufertrupp Luzern',NULL,NULL),(254,239,'2011-03-15 20:11:00','2011-03-15 20:11:00','wasserturm','Wasserturm',NULL,NULL),(255,254,'2011-03-15 20:11:00','2011-03-15 20:11:00','bergtrupp','Bergtrupp Ebikon',NULL,NULL),(257,254,'2011-03-15 20:11:00','2011-03-15 20:11:00','dreilinden','Dreilinden Luzern',NULL,NULL),(258,254,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmichael','St.Michael Luzern',NULL,NULL),(259,254,'2011-03-15 20:11:00','2011-03-15 20:11:00','seppel','Seppel Luzern',NULL,NULL),(260,239,'2011-03-15 20:11:00','2011-03-15 20:11:00','santenberg','Santenberg',NULL,NULL),(261,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','baeseris','Bäseris Hildisrieden',NULL,NULL),(262,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','berona','Berona Beromünster',NULL,NULL),(263,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','johanniter','Johanniter Reiden',NULL,NULL),(264,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','malteser','Mädchenpfadi Malteser Reiden',NULL,NULL),(265,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','sthedwig','St.Hedwig Sursee',NULL,NULL),(266,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','stkarl','St.Karl Rickenbach',NULL,NULL),(267,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmartin','St.Martin Sursee',NULL,NULL),(268,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','stpeterundpaul','St.Peter und Paul Willisau',NULL,NULL),(269,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','sttheodul','St.Theodul Neudorf',NULL,NULL),(270,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','wartensee','Wartensee Neuenkirch',NULL,NULL),(271,260,'2011-03-15 20:11:00','2011-03-15 20:11:00','delta','Delta Ettiswil, Alberswil, Kottwil',NULL,NULL),(272,239,'2011-03-15 20:11:00','2011-03-15 20:11:00','seetal','Seetal',NULL,NULL),(273,272,'2011-03-15 20:11:00','2011-03-15 20:11:00','bruderklaus','Bruder Klaus Root',NULL,NULL),(275,272,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjakob','St.Jakob Eschenbach LU',NULL,NULL),(276,272,'2011-03-15 20:11:00','2011-03-15 20:11:00','peterundpaul','Peter und Paul Inwil',NULL,NULL),(277,272,'2011-03-15 20:11:00','2011-03-15 20:11:00','winkelried','Winkelried Rothenburg',NULL,NULL),(278,272,'2011-03-15 20:11:00','2011-03-15 20:11:00','johanniter','Johanniter Hohenrein',NULL,NULL),(279,272,'2011-03-15 20:11:00','2011-03-15 20:11:00','rain','Pfadi Rain',NULL,NULL),(280,272,'2011-03-15 20:11:00','2011-03-15 20:11:00','ballwil','Pfadi Ballwil',NULL,NULL),(281,239,'2011-03-15 20:11:00','2011-03-15 20:11:00','sonnenberg','Sonnenberg',NULL,NULL),(282,281,'2011-03-15 20:11:00','2011-03-15 20:11:00','habsburg','Habsburg Meggen',NULL,NULL),(283,281,'2011-03-15 20:11:00','2011-03-15 20:11:00','haexebach','Häxebach Meggen',NULL,NULL),(284,281,'2011-03-15 20:11:00','2011-03-15 20:11:00','marcha','Marcha Malters',NULL,NULL),(285,281,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgallus','St.Gallus Kriens',NULL,NULL),(286,281,'2011-03-15 20:11:00','2011-03-15 20:11:00','straphael','St.Raphael Horw',NULL,NULL),(287,239,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta_tannenberg','PTA-Tannenberg',NULL,NULL),(289,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','neuchatel','Association du Scoutisme Neuchâtelois',NULL,NULL),(290,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','bevaix','Abbaye Bevaix',NULL,NULL),(291,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','boudry','Areuse Marfaux Boudry',NULL,NULL),(292,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','baslac','Groupe Scout Baslac',NULL,NULL),(293,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','stnorbert','St-Norbert Neuchâtel',NULL,NULL),(294,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','altair','Altair',NULL,NULL),(295,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','boquetin','Bouquetin Neuchâtel',NULL,NULL),(296,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','durandal','Durandal Cernier',NULL,NULL),(297,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','lacolombe','La-Colombe Colombier NE',NULL,NULL),(298,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','larochelle','La Rochelle La-Chaux-de-Fonds',NULL,NULL),(300,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','perchettes','Perchettes Auvernier',NULL,NULL),(302,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','tichodrome','Tichodrome - Le Locle',NULL,NULL),(303,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','sthubert','St-Hubert La Chaux-de-Fonds',NULL,NULL),(304,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','stlous','St-Louis Peseux',NULL,NULL),(305,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','stpaul','St-Paul Le Locle',NULL,NULL),(306,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','troisraisses','Trois Raisses Fleurier',NULL,NULL),(307,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','troisetoiles','Trois-Etoiles Les Verrieres',NULL,NULL),(308,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','valtra','Valtra Buttes',NULL,NULL),(309,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','vieux-castel','Vieux-Castel La Chaux-de-Fonds',NULL,NULL),(311,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','letison','Le Tison',NULL,NULL),(312,289,'2011-03-15 20:11:00','2011-03-15 20:11:00','flambeaux','Flambeaux Val-de-Travers',NULL,NULL),(313,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgallen-appenzell','Pfadi Kantonalverband St. Gallen - Appenzell',NULL,NULL),(314,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','hospiz','Hospiz',NULL,NULL),(315,314,'2011-03-15 20:11:00','2011-03-15 20:11:00','falkenstein','Falkenstein St. Gallen',NULL,NULL),(316,314,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta_oberberg','PTA Oberberg St. Gallen',NULL,NULL),(317,314,'2011-03-15 20:11:00','2011-03-15 20:11:00','ramschwag','Ramschwag St. Gallen',NULL,NULL),(318,314,'2011-03-15 20:11:00','2011-03-15 20:11:00','rappenstein','Rappenstein St. Gallen',NULL,NULL),(319,314,'2011-03-15 20:11:00','2011-03-15 20:11:00','aetschberg','Aetschberg St. Gallen',NULL,NULL),(320,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','gallus','Gallus St. Gallen',NULL,NULL),(321,320,'2011-03-15 20:11:00','2011-03-15 20:11:00','erlach','Erlach St. Gallen',NULL,NULL),(322,320,'2011-03-15 20:11:00','2011-03-15 20:11:00','fontana','Fontana St. Gallen',NULL,NULL),(323,320,'2011-03-15 20:11:00','2011-03-15 20:11:00','helveter','Helveter St. Gallen',NULL,NULL),(324,320,'2011-03-15 20:11:00','2011-03-15 20:11:00','juerg_jenatsch','Jürg Jenatsch St. Gallen',NULL,NULL),(325,320,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmartin','St.Martin St. Gallen',NULL,NULL),(326,320,'2011-03-15 20:11:00','2011-03-15 20:11:00','stotmar','St. Otmar St. Gallen',NULL,NULL),(327,320,'2011-03-15 20:11:00','2011-03-15 20:11:00','peterundpaul','Peter+Paul St. Gallen',NULL,NULL),(328,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','alvier','Alvier Buchs SG',NULL,NULL),(329,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','attila','Attila Teufen AR',NULL,NULL),(330,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','baden-powell','Baden-Powell Oberuzwil',NULL,NULL),(331,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','grimmenstein','Grimmenstein St. Margreten',NULL,NULL),(332,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','altenstein','Altenstein Heiden',NULL,NULL),(333,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','heimat','Heimat Uzwil SG',NULL,NULL),(334,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','helfenberg','Helfenberg-Oberberg Gossau SG',NULL,NULL),(335,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','hof','Pfadi Hof Wil SG',NULL,NULL),(336,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','Ital-reding','Ital-Reding Bad Ragaz',NULL,NULL),(337,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','juvalta','Juvalta Eggersriet',NULL,NULL),(338,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','kamor','Kamor Rüthi SG',NULL,NULL),(339,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','landegg','Landegg Flawil',NULL,NULL),(340,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','lido','Lido Uzwil',NULL,NULL),(341,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','lowenburg','Löwenburg Zuzwil SG',NULL,NULL),(342,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','freudenberg','Freudenberg St. Gallen',NULL,NULL),(343,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','stanna','St. Anna Montlingen',NULL,NULL),(344,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','maurena','Maurena Appenzell',NULL,NULL),(345,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','mittelrheintal','Mittelrheintal Heerbrugg',NULL,NULL),(346,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','oberrhi','Oberrhi Sargans-Wartau',NULL,NULL),(347,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','propatria','Pro Patria St.Gallen',NULL,NULL),(348,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','rorschach','Rorschach',NULL,NULL),(349,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','sanktluzius','Sankt Luzius Walenstadt',NULL,NULL),(350,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','seebuebe','Seebuebe Goldach',NULL,NULL),(351,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','speicher','Speicher',NULL,NULL),(352,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgeorg_gossau','St. Georg Gossau-Niederwil SG',NULL,NULL),(353,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgeorg_uzwil','St. Georg Uzwil',NULL,NULL),(354,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjustus','St. Justus Flums',NULL,NULL),(355,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','stlaurentius','St. Laurentius Flawil',NULL,NULL),(356,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmichael','St. Michael Altstätten SG',NULL,NULL),(357,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','stoswald','St. Oswald Sargans',NULL,NULL),(358,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','trogen','Trogen',NULL,NULL),(359,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','urstein','Urstein Herisau',NULL,NULL),(360,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','thur','Pfadfinder Thur Wil SG',NULL,NULL),(361,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','wolfensberg','Wolfensberg Degersheim',NULL,NULL),(362,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','yberg','Yberg Wattwil',NULL,NULL),(363,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','dufour','General Dufour Rapperswil-Jona',NULL,NULL),(364,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','eschenbach','Eschenbach SG',NULL,NULL),(365,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','linth','Linth Uznach',NULL,NULL),(366,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','wildmannli','Wildmannli',NULL,NULL),(367,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','regulastein','Regulastein Gommiswald',NULL,NULL),(368,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','oberhelfenschwil','Oberhelfenschwil, Ruedberg',NULL,NULL),(369,313,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmartin','St. Martin Mels',NULL,NULL),(370,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','schaffhausen','Pfadi Kantonalverband Schaffhausen',NULL,NULL),(371,370,'2011-03-15 20:11:00','2011-03-15 20:11:00','beringen','Beringen',NULL,NULL),(372,370,'2011-03-15 20:11:00','2011-03-15 20:11:00','hallau','Hallau-Wilchingen',NULL,NULL),(373,370,'2011-03-15 20:11:00','2011-03-15 20:11:00','laufen','Laufen',NULL,NULL),(374,370,'2011-03-15 20:11:00','2011-03-15 20:11:00','neunkirch','Neunkirch',NULL,NULL),(375,370,'2011-03-15 20:11:00','2011-03-15 20:11:00','neuhausen','Neuhausen',NULL,NULL),(377,370,'2011-03-15 20:11:00','2011-03-15 20:11:00','steinamrhein','Stein am Rhein',NULL,NULL),(378,370,'2011-03-15 20:11:00','2011-03-15 20:11:00','feuerthalen','Feuerthalen',NULL,NULL),(379,370,'2011-03-15 20:11:00','2011-03-15 20:11:00','thayngen','Thayngen',NULL,NULL),(381,370,'2011-03-15 20:11:00','2011-03-15 20:11:00','schaffhausen','Schaffhausen',NULL,NULL),(382,381,'2011-03-15 20:11:00','2011-03-15 20:11:00','guetli','Pfadiabteilung Güetli Schaffhausen',NULL,NULL),(383,381,'2011-03-15 20:11:00','2011-03-15 20:11:00','marsupilami','Marsupilami Schaffhausen',NULL,NULL),(384,381,'2011-03-15 20:11:00','2011-03-15 20:11:00','seewadel','Seewadel Schaffhausen',NULL,NULL),(385,381,'2011-03-15 20:11:00','2011-03-15 20:11:00','niklausen','Abteilung Niklausen',NULL,NULL),(386,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','solothurn','Pfadi Kanton Solothurn',NULL,NULL),(387,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','bettlach','Bettlach',NULL,NULL),(388,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','pabla','PABLA',NULL,NULL),(389,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','bipp','Bipp-Wiedlisbach',NULL,NULL),(390,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','falkenstein','Falkenstein Balsthal',NULL,NULL),(391,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','gerlafingen','Gerlafingen',NULL,NULL),(392,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','goesgen','Gösgen-Niedergösgen',NULL,NULL),(393,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','grenchen','Pfadi Grenchen',NULL,NULL),(394,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','lutschka','Lutschka Grenchen',NULL,NULL),(395,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','oltner_pfadfinderinnen','Oltner Pfadfinderinnenabteilung',NULL,NULL),(396,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','pag_grenchen','PAG Grenchen',NULL,NULL),(397,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','stadtolten','Pfadi Abteilung Stadt Olten',NULL,NULL),(398,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','solothurn_PASS','Pfadi Abteilung Stadt Solothurn PASS',NULL,NULL),(399,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','luterbach','Luterbach',NULL,NULL),(400,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','schoenenwerd','Schönenwerd',NULL,NULL),(401,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgallus','St.Gallus Wangen',NULL,NULL),(402,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','speuz','Speuz Erlinsbach',NULL,NULL),(403,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','oensingen','Oensingen',NULL,NULL),(404,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjoerg','St.Jörg Kleinlützel',NULL,NULL),(405,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','froburg','Pfadi Froburg-Olten',NULL,NULL),(406,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmartin','St.Martin Laupersdorf',NULL,NULL),(407,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','sturs','St. Urs Solothurn',NULL,NULL),(408,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','stwendel','St.Wendel Dulliken',NULL,NULL),(409,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','thierstein','Thierstein Breitenbach',NULL,NULL),(410,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','weissenstein','Weissenstein Solothurn',NULL,NULL),(411,386,'2011-03-15 20:11:00','2011-03-15 20:11:00','zuchwil','Zuchwil',NULL,NULL),(412,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','schwyz','Pfadi Kanton Schwyz',NULL,NULL),(413,412,'2011-03-15 20:11:00','2011-03-15 20:11:00','goldau','Arth Goldau',NULL,NULL),(414,412,'2011-03-15 20:11:00','2011-03-15 20:11:00','lachen','Lachen SZ',NULL,NULL),(415,412,'2011-03-15 20:11:00','2011-03-15 20:11:00','schwyz_maepfa','Schwyz Mädchen',NULL,NULL),(416,412,'2011-03-15 20:11:00','2011-03-15 20:11:00','schwyz_bupfa','Schwyz Buben',NULL,NULL),(417,412,'2011-03-15 20:11:00','2011-03-15 20:11:00','hoefe','Pfadi Höfe',NULL,NULL),(418,412,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmeinrad','St.Meinrad Einsiedeln',NULL,NULL),(419,412,'2011-03-15 20:11:00','2011-03-15 20:11:00','rothenthurm','Rothenthurm',NULL,NULL),(420,412,'2011-03-15 20:11:00','2011-03-15 20:11:00','brunnen','Pfadi Brunnen',NULL,NULL),(421,412,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta','PTA Kanton Schwyz',NULL,NULL),(422,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','thurgau','Pfadi Thurgau',NULL,NULL),(423,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','arbor-felix','Pfadi Arbor-Felix',NULL,NULL),(424,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','seebachtal','Seebachtal',NULL,NULL),(425,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','buerglen','Pfadi Bürglen',NULL,NULL),(426,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','feuerpfeil','Feuerpfeil Müllheim-Wigoltg.',NULL,NULL),(427,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','hinterthurgau','Pfadi Hinterthurgau',NULL,NULL),(428,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','libelle','Pfadi Libelle Steckborn',NULL,NULL),(429,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','olymp','Pfadi Olymp Romanshorn',NULL,NULL),(430,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','orion','Orion Aadorf',NULL,NULL),(431,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','turmfalke','Turmfalke und Rhy',NULL,NULL),(432,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','seemoeve','Seemöve Kreuzlingen',NULL,NULL),(433,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','seesturm','Seesturm Neukirch-Egnach',NULL,NULL),(434,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','sturmvogel','Sturmvogel Kreuzlingen',NULL,NULL),(435,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta_summervogel','PTA Summervogel Kreuzlingen 3',NULL,NULL),(436,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','weinfelden','Weinfelden',NULL,NULL),(437,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','quivelda','Quivelda Weinfelden',NULL,NULL),(438,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','bischofberg','Bischofberg-Waldkirch-Bischofszell',NULL,NULL),(439,438,'2011-03-15 20:11:00','2011-03-15 20:11:00','bischofszell','Bischofszell',NULL,NULL),(440,438,'2011-03-15 20:11:00','2011-03-15 20:11:00','waldkirch','Waldkirch',NULL,NULL),(441,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','falkenstein','Falkenstein Sulgen-Kradolf-Amriswil',NULL,NULL),(442,422,'2011-03-15 20:11:00','2011-03-15 20:11:00','frauenfeld','Corps Pfadi Frauenfeld',NULL,NULL),(443,442,'2011-03-15 20:11:00','2011-03-15 20:11:00','stnikolaus','Pfadi St. Nikolaus Frauenfeld',NULL,NULL),(444,442,'2011-03-15 20:11:00','2011-03-15 20:11:00','helfenberg','Helfenberg Frauenfeld',NULL,NULL),(445,442,'2011-03-15 20:11:00','2011-03-15 20:11:00','neuburg','Pfadi Neuburg Frauenfeld',NULL,NULL),(446,442,'2011-03-15 20:11:00','2011-03-15 20:11:00','tannegg','Tannegg Frauenfeld',NULL,NULL),(447,442,'2011-03-15 20:11:00','2011-03-15 20:11:00','wellenberg','Wellenberg Frauenfeld',NULL,NULL),(448,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','ticino_aeec','AEEC - Associazione Esploratrici e Esploratori Cattolici',NULL,NULL),(449,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','sanpietro','Burot Castel San Pietro',NULL,NULL),(450,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','lugano','Ceresio Lugano',NULL,NULL),(451,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','eoc','EOC Raggio di Sole',NULL,NULL),(452,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','fortesinfide','Fortes in Fide Chiasso',NULL,NULL),(453,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','tenero','Scaut Tenero',NULL,NULL),(454,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','solduno','La Rocca Solduno',NULL,NULL),(455,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','losone','La Torre Losone',NULL,NULL),(456,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','locarno','Fregera Locarno',NULL,NULL),(457,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','minusio','Madonna delle Grazie Minusio',NULL,NULL),(458,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','gravesano','Gravesano-Lamone',NULL,NULL),(459,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','gordola','Mons. Bacciarini Gordola',NULL,NULL),(460,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','crocedaro','Motto della Croce Daro',NULL,NULL),(461,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','faido','Piumogna Acqua-Felice Faido',NULL,NULL),(462,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','biasca','San Carlo Biasca',NULL,NULL),(463,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','caslano','San Cristoforo Caslano',NULL,NULL),(464,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','sementina','San Felice Sementina',NULL,NULL),(465,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','viganello','San Gottardo Viganello',NULL,NULL),(466,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','sanmartino','San Martino Chiasso',NULL,NULL),(467,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','arogno','AEEC S. Michele Arogno',NULL,NULL),(468,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','bellinzona','San Michele Bellinzona',NULL,NULL),(469,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','giubiasco','San Rocco Giubiasco',NULL,NULL),(470,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','stabio','San Rocco Stabio',NULL,NULL),(471,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','breganzona','San Sebastiano Breganzona',NULL,NULL),(472,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','canobbio','San Siro Canobbio',NULL,NULL),(473,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','balerna','Groupe Scout S. Vittore Balerna',NULL,NULL),(474,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','tesserete','AEEC Santo Stefano Tesserete',NULL,NULL),(475,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','bodio','Sassi Grossi Bodio',NULL,NULL),(476,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','vallemaggia','Sassifraga Vallemaggia',NULL,NULL),(477,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','massagno','Tre Pini Massagno',NULL,NULL),(478,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','agno','Sezione Esploratori San Giorgio Agno',NULL,NULL),(479,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','cureglia','San Christofero Cureglia',NULL,NULL),(480,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','stantonio','St. Antonino',NULL,NULL),(481,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','foulard','Foulard',NULL,NULL),(482,448,'2011-03-15 20:11:00','2011-03-15 20:11:00','stambrogio','AEEC St.Ambrogio Claro',NULL,NULL),(483,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','ticino_aget','AGET - Associazione Giovani Esploratori Ticinesi',NULL,NULL),(484,483,'2011-03-15 20:11:00','2011-03-15 20:11:00','bellinzona','Bellinzona',NULL,NULL),(485,483,'2011-03-15 20:11:00','2011-03-15 20:11:00','lugano','Lugano',NULL,NULL),(486,483,'2011-03-15 20:11:00','2011-03-15 20:11:00','mendrisio','Mendrisio',NULL,NULL),(487,483,'2011-03-15 20:11:00','2011-03-15 20:11:00','rancate','Rancate',NULL,NULL),(488,483,'2011-03-15 20:11:00','2011-03-15 20:11:00','roveredo','aget Roveredo',NULL,NULL),(489,483,'2011-03-15 20:11:00','2011-03-15 20:11:00','vedeggio','Vedeggio',NULL,NULL),(490,483,'2011-03-15 20:11:00','2011-03-15 20:11:00','gambarogno','Gambarogno',NULL,NULL),(491,483,'2011-03-15 20:11:00','2011-03-15 20:11:00','malcantone','Malcantone',NULL,NULL),(492,483,'2011-03-15 20:11:00','2011-03-15 20:11:00','locarno','Locarno',NULL,NULL),(493,492,'2011-03-15 20:11:00','2011-03-15 20:11:00','aget_ascona','Aget Ascona',NULL,NULL),(494,492,'2011-03-15 20:11:00','2011-03-15 20:11:00','aget_locarno','Aget Locarno',NULL,NULL),(495,492,'2011-03-15 20:11:00','2011-03-15 20:11:00','aget_minusio','Aget Minusio',NULL,NULL),(496,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','uri','Pfadi Uri',NULL,NULL),(497,496,'2011-03-15 20:11:00','2011-03-15 20:11:00','donbosco','Don Bosco Schattdorf',NULL,NULL),(498,496,'2011-03-15 20:11:00','2011-03-15 20:11:00','kroenten','Krönten Erstfeld',NULL,NULL),(499,496,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmartin','Pfadi St.Martin Altdorf UR',NULL,NULL),(500,496,'2011-03-15 20:11:00','2011-03-15 20:11:00','stauffacherin','Stauffacherin Altdorf UR',NULL,NULL),(501,496,'2011-03-15 20:11:00','2011-03-15 20:11:00','willhelmtell','Wilhelm Tell Bürgen',NULL,NULL),(502,496,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta','PTA Uri',NULL,NULL),(503,496,'2011-03-15 20:11:00','2011-03-15 20:11:00','seedorf','Scouting Seedorf',NULL,NULL),(504,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','unterwalden','Pfadi Unterwalden',NULL,NULL),(505,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','bruderklaus','Bruder Klaus Sarnen',NULL,NULL),(506,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','donbosco','Don Bosco Hergiswil',NULL,NULL),(507,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','isenringen','Isenringen Beckenried',NULL,NULL),(508,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','schnitzturm','Schnitzturm Stansstad',NULL,NULL),(509,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','steugen','St.Eugen Engelberg',NULL,NULL),(510,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','stlaurentius','St.Laurentius',NULL,NULL),(511,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmartin','Pfadi St. Martin Buochs',NULL,NULL),(512,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','strochus','St.Rochus Büren-Oberdorf',NULL,NULL),(513,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','winkelried','Winkelried Stans-Ennetmoos',NULL,NULL),(514,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','wolfschlucht','Wolfschlucht Wolfenschiessen',NULL,NULL),(515,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta_kunterbunt','PTA Kunterbunt',NULL,NULL),(516,504,'2011-03-15 20:11:00','2011-03-15 20:11:00','giswil','Giswil',NULL,NULL),(517,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','valais','Association du Scoutisme Valaisan',NULL,NULL),(518,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','christroi','Christ-Roi Flanthey',NULL,NULL),(519,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','derborence','Derborence Conthey',NULL,NULL),(520,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','smt','SMT Valais',NULL,NULL),(521,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','notredame','Notre Dame Bagnes Le Chable',NULL,NULL),(522,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stbarhelemy','St-Barhélémy Evionnaz',NULL,NULL),(523,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stbernhard','St-Bernard de Menthon Martigny',NULL,NULL),(524,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stchristophe','St-Christophe Vétroz',NULL,NULL),(525,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stdidier','St-Didier Collombey',NULL,NULL),(526,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stfelix','St-Felix Saxon',NULL,NULL),(527,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','monthey','St-Georges Monthey',NULL,NULL),(528,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','sion','St-Georges Sion',NULL,NULL),(529,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgermain','St-Germain Saviese',NULL,NULL),(530,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','sthippolyte','St-Hippolyte Vouvry',NULL,NULL),(531,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stignace','St-Ignace Vernayaz',NULL,NULL),(532,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjeanbaptiste','St-Jean-Baptiste Ardon',NULL,NULL),(533,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjeanbosco','St-Jean-Bosco Bramois',NULL,NULL),(534,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmartin','St-Martin Leytron',NULL,NULL),(535,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmaurice','St-Maurice',NULL,NULL),(536,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stpancrace','St-Pancrace Grimisuat',NULL,NULL),(537,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stsymphorien','St-Symphorien Fully',NULL,NULL),(538,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stvictor','St-Victor Sierre',NULL,NULL),(539,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stchistopherus','St.Christophorus Siders',NULL,NULL),(540,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmauritius','St.Mauritius Visp',NULL,NULL),(541,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','stsebastian','St-Sebastian Brig',NULL,NULL),(542,517,'2011-03-15 20:11:00','2011-03-15 20:11:00','wiwanni','Wiwanni Visp',NULL,NULL),(543,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','vaude','Association du Scoutisme Vaudois',NULL,NULL),(544,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','grandeourse','Grande Ourse Bussigny',NULL,NULL),(545,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','comtevert','Comte-Vert Moudon',NULL,NULL),(546,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','covatannaz','Covatannaz',NULL,NULL),(547,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','lecroisee','La Croisée',NULL,NULL),(548,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','delaharpe','De la Harpe Rolle',NULL,NULL),(549,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','envauz','En Vaux Lutry',NULL,NULL),(550,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','flambeux_lausanne','Flambeaux Evangile Lausanne',NULL,NULL),(551,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','flambeux_vevey','Flambeaux Evangile Vevey',NULL,NULL),(552,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','grosdevaud','Gros de Vaud Echallens',NULL,NULL),(553,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','lacbleu','Lac Bleu La Tour-de-Peilz',NULL,NULL),(554,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','majordavel','Major-Davel',NULL,NULL),(555,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','montbenon','Brigade de Montbenon (Lausanne)',NULL,NULL),(556,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','noirmont-gland','Noirmont-Gland Begnins',NULL,NULL),(557,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','notredame','Notre Dame Lausanne',NULL,NULL),(558,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','orbe','Orbe Union',NULL,NULL),(559,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','pierredegruins','Pierre de Gruins Gryon',NULL,NULL),(560,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','plantour','Plantour Aigle',NULL,NULL),(561,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','quatrevents','Quatre-Vents, Pully',NULL,NULL),(562,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','sacre-coeur','Sacré-Coeur Lausanne',NULL,NULL),(563,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','saleuscex','Saleuscex Montreux',NULL,NULL),(564,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','savabelin','Sauvabelin Lausanne',NULL,NULL),(566,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','scanavin','Scanavin Vevey',NULL,NULL),(567,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','stpaul','St-Paul Lausanne',NULL,NULL),(568,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','grandchene','Grand Chê;ne Yverdon-les-Bains',NULL,NULL),(569,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','stredempteur','St-Rédempteur Lausanne',NULL,NULL),(570,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','morges','Tribu du Grand Lac Morges',NULL,NULL),(571,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','cossenay','Venoge La Sarraz-Cossenay',NULL,NULL),(572,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','vieux_mazel','Vieux-Mazel Vevey',NULL,NULL),(574,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','laroseliere','La Roselière Yverdon-les-Bains',NULL,NULL),(575,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','marchairuz','Marchairuz Valleé de Joux',NULL,NULL),(576,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','cossonay','Flambeaux Evangile Cossonay',NULL,NULL),(577,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','lacote','Flambeaux Evangile La Côte',NULL,NULL),(578,543,'2011-03-15 20:11:00','2011-03-15 20:11:00','nyon','Flambeaux Evangile Nyon',NULL,NULL),(579,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','zug','Pfadi Kanton Zug',NULL,NULL),(580,579,'2011-03-15 20:11:00','2011-03-15 20:11:00','allenwinden','Allenwinden',NULL,NULL),(581,579,'2011-03-15 20:11:00','2011-03-15 20:11:00','baar','Baar',NULL,NULL),(582,579,'2011-03-15 20:11:00','2011-03-15 20:11:00','hue','Pfadi Hue',NULL,NULL),(583,579,'2011-03-15 20:11:00','2011-03-15 20:11:00','morgarten','Pfadi Morgarten',NULL,NULL),(584,579,'2011-03-15 20:11:00','2011-03-15 20:11:00','cham','Pfadi Winkelried Cham - Steinhausen - Hagendorn',NULL,NULL),(585,579,'2011-03-15 20:11:00','2011-03-15 20:11:00','menzingen','Pfadi Menzingen',NULL,NULL),(586,579,'2011-03-15 20:11:00','2011-03-15 20:11:00','zytturm','Zytturm Zug',NULL,NULL),(587,586,'2011-03-15 20:11:00','2011-03-15 20:11:00','guthirt','Gut Hirt Zug',NULL,NULL),(588,586,'2011-03-15 20:11:00','2011-03-15 20:11:00','pfadfinderinnen_zug','Pfadfinderinnen Zug',NULL,NULL),(589,586,'2011-03-15 20:11:00','2011-03-15 20:11:00','zug','Pfadi Zug',NULL,NULL),(590,586,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjohannes','St. Johannes Zug',NULL,NULL),(591,579,'2011-03-15 20:11:00','2011-03-15 20:11:00','schwan','Schwan',NULL,NULL),(592,1,'2011-03-15 20:11:00','2011-03-15 20:11:00','zuerich','Pfadi Zürich',NULL,NULL),(593,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','flamberg','Flamberg Zürich',NULL,NULL),(594,593,'2011-03-15 20:11:00','2011-03-15 20:11:00','birmensdorf','Birmensdorf',NULL,NULL),(595,593,'2011-03-15 20:11:00','2011-03-15 20:11:00','flamberg','Pfadi Flamberg Zürich',NULL,NULL),(596,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','kaschaf','Kaschaf',NULL,NULL),(597,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','glockenhof','Glockenhof',NULL,NULL),(598,597,'2011-03-15 20:11:00','2011-03-15 20:11:00','gryfensee','Gryfensee Wallisellen',NULL,NULL),(599,597,'2011-03-15 20:11:00','2011-03-15 20:11:00','hadlaub','Hadlaub',NULL,NULL),(600,597,'2011-03-15 20:11:00','2011-03-15 20:11:00','hutten','Hutten von Gloggi',NULL,NULL),(601,597,'2011-03-15 20:11:00','2011-03-15 20:11:00','laegern','Lägern Dielsdorf',NULL,NULL),(602,597,'2011-03-15 20:11:00','2011-03-15 20:11:00','seascouts','Sea Scouts Zurich',NULL,NULL),(603,597,'2011-03-15 20:11:00','2011-03-15 20:11:00','wildert','Wildert Volketswil',NULL,NULL),(604,597,'2011-03-15 20:11:00','2011-03-15 20:11:00','manegg','Abteilung Manegg',NULL,NULL),(605,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','hanswaldmann','Hans Waldmann',NULL,NULL),(606,605,'2011-03-15 20:11:00','2011-03-15 20:11:00','altberg','Pfadi Altberg',NULL,NULL),(607,605,'2011-03-15 20:11:00','2011-03-15 20:11:00','albis','Pfadi Albis &amp; Felsenegg',NULL,NULL),(608,605,'2011-03-15 20:11:00','2011-03-15 20:11:00','limmattal','Limmattal Dietikon',NULL,NULL),(609,605,'2011-03-15 20:11:00','2011-03-15 20:11:00','rudolfbrun','Rudolf Brun Zürich',NULL,NULL),(610,605,'2011-03-15 20:11:00','2011-03-15 20:11:00','saeuliamt','Pfadiabteilung Säuliamt',NULL,NULL),(611,605,'2011-03-15 20:11:00','2011-03-15 20:11:00','vennes','Pfadi Vennes Zürich',NULL,NULL),(612,605,'2011-03-15 20:11:00','2011-03-15 20:11:00','waltertell','Walter Tell Zürich',NULL,NULL),(613,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','hochwacht','Hochwacht',NULL,NULL),(614,613,'2011-03-15 20:11:00','2011-03-15 20:11:00','h2o','H2O (Horgen-Oberrieden-Hirzel)',NULL,NULL),(615,613,'2011-03-15 20:11:00','2011-03-15 20:11:00','fabulus','Fabulus (Pfadi für Gehörlose)',NULL,NULL),(616,613,'2011-03-15 20:11:00','2011-03-15 20:11:00','chopfholz','Pfadi Chopfholz',NULL,NULL),(617,613,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta','PTA Hochwacht',NULL,NULL),(618,613,'2011-03-15 20:11:00','2011-03-15 20:11:00','troya','Troya Thalwil-Langnau',NULL,NULL),(620,613,'2011-03-15 20:11:00','2011-03-15 20:11:00','waedenswil','Pfadi Wädenswil-Richterswil-Au',NULL,NULL),(621,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','landenberg','Landenberg',NULL,NULL),(622,621,'2011-03-15 20:11:00','2011-03-15 20:11:00','altburg','Altburg Regensdorf',NULL,NULL),(623,621,'2011-03-15 20:11:00','2011-03-15 20:11:00','alt-regensberg','Alt-Regensberg Regensdorf',NULL,NULL),(624,621,'2011-03-15 20:11:00','2011-03-15 20:11:00','dietlikon','Dietlikon-Wangen-Brüttisellen',NULL,NULL),(625,621,'2011-03-15 20:11:00','2011-03-15 20:11:00','gryfenberg','Gryfenberg Zürich',NULL,NULL),(626,621,'2011-03-15 20:11:00','2011-03-15 20:11:00','landskron','Landskron Bassersdorf',NULL,NULL),(627,621,'2011-03-15 20:11:00','2011-03-15 20:11:00','opfikova','Pfadi Opfikova',NULL,NULL),(628,621,'2011-03-15 20:11:00','2011-03-15 20:11:00','sarolta_rakoczi','Sarolta-Rakoczi',NULL,NULL),(629,621,'2011-03-15 20:11:00','2011-03-15 20:11:00','ruemlang','Rümlang-Oberglatt',NULL,NULL),(630,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','oberland','Oberland',NULL,NULL),(631,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','effretikon','Effretikon',NULL,NULL),(632,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','moechaltdorf','Mönchaltdorf',NULL,NULL),(633,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','paprika','Paprika',NULL,NULL),(634,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','buetschgi','Bütschgi Züri Oberland',NULL,NULL),(635,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','pfaeffikon','Pfadi Pfäffiko und Umgebung',NULL,NULL),(636,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','hinwil','Hinwil',NULL,NULL),(637,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','visavis','Vis-àvis Gossau-Grüningen',NULL,NULL),(638,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','fram_uster_greifensee','Fram Uster-Greifensee',NULL,NULL),(639,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','uster_greifensee','Uster-Greifensee',NULL,NULL),(640,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','walda_bauma','Pfadi Wald-Bauma',NULL,NULL),(641,630,'2011-03-15 20:11:00','2011-03-15 20:11:00','sirius','Pfadi Sirius Wetzikon-Bäretswil',NULL,NULL),(642,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','pfannenstil','Pfannenstil',NULL,NULL),(643,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','chelle','Chelle Zumikon-Egg',NULL,NULL),(644,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','ratatouille','Ratatouille (Männedorf-Uetikon-Oetwil)',NULL,NULL),(645,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','kuesnacht','Küsnacht-Erlenbach',NULL,NULL),(646,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','meilen','Meilen-Herrliberg',NULL,NULL),(647,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','moergenstaern','Morgenstärn Zollikon',NULL,NULL),(648,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','olymp','Olymp (Stäfa-Hombrechtikon)',NULL,NULL),(649,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','wulp','Wulp (Mädchen Küsnacht-Erlenbach)',NULL,NULL),(650,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','zollikon','Zollikon',NULL,NULL),(651,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','heureka','Heureka Zumikon',NULL,NULL),(652,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','muur','Pfadi Muur',NULL,NULL),(653,642,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta','Pfadi Trotz Allem am Pfannenstil',NULL,NULL),(654,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','seldwyla','Pfadi-Region Seldwyla',NULL,NULL),(655,654,'2011-03-15 20:11:00','2011-03-15 20:11:00','pulacha','Pulacha Bülach',NULL,NULL),(656,654,'2011-03-15 20:11:00','2011-03-15 20:11:00','rhenania','Rhenania Eglisau-Glattfelden',NULL,NULL),(657,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','stgeorg','St. Georg Zürich',NULL,NULL),(658,657,'2011-03-15 20:11:00','2011-03-15 20:11:00','glattal','Glattal',NULL,NULL),(659,658,'2011-03-15 20:11:00','2011-03-15 20:11:00','lepanto','Pfadfinderabteilung Lepanto Zürich-Schwamendingen',NULL,NULL),(660,658,'2011-03-15 20:11:00','2011-03-15 20:11:00','morea','Morea Zürich-Oerlikon',NULL,NULL),(661,658,'2011-03-15 20:11:00','2011-03-15 20:11:00','stfelix','St.Felix Regensdorf',NULL,NULL),(662,658,'2011-03-15 20:11:00','2011-03-15 20:11:00','stjakob','Pfadi St.Jakob Dübendorf',NULL,NULL),(663,658,'2011-03-15 20:11:00','2011-03-15 20:11:00','stluzi','Pfadi St.Luzi, Zürich-Affoltern',NULL,NULL),(664,658,'2011-03-15 20:11:00','2011-03-15 20:11:00','winkelried','Winkelried Wallisellen',NULL,NULL),(665,657,'2011-03-15 20:11:00','2011-03-15 20:11:00','limmat','Limmat',NULL,NULL),(666,665,'2011-03-15 20:11:00','2011-03-15 20:11:00','laupen','Laupen Oberengstringen',NULL,NULL),(667,665,'2011-03-15 20:11:00','2011-03-15 20:11:00','morgarten','Morgarten-Erlach Zürich',NULL,NULL),(668,665,'2011-03-15 20:11:00','2011-03-15 20:11:00','murten','Murten Zürich-Albisrieden',NULL,NULL),(669,665,'2011-03-15 20:11:00','2011-03-15 20:11:00','sempach','Sempach Zürich-Altstetten',NULL,NULL),(670,665,'2011-03-15 20:11:00','2011-03-15 20:11:00','stmauritius','St.Mauritius-Nansen Zürich',NULL,NULL),(671,665,'2011-03-15 20:11:00','2011-03-15 20:11:00','stulrich','St.Ulrich Dietikon',NULL,NULL),(672,665,'2011-03-15 20:11:00','2011-03-15 20:11:00','uro','Pfadi URO',NULL,NULL),(673,657,'2011-03-15 20:11:00','2011-03-15 20:11:00','uto','Uto',NULL,NULL),(674,673,'2011-03-15 20:11:00','2011-03-15 20:11:00','attinghausen','Attinghausen Zürich',NULL,NULL),(675,673,'2011-03-15 20:11:00','2011-03-15 20:11:00','friesen','Friesen Zürich',NULL,NULL),(676,673,'2011-03-15 20:11:00','2011-03-15 20:11:00','rotach','Rotach Zürich',NULL,NULL),(677,673,'2011-03-15 20:11:00','2011-03-15 20:11:00','zueriberg','Züriberg Zürich',NULL,NULL),(678,673,'2011-03-15 20:11:00','2011-03-15 20:11:00','reding','Reding',NULL,NULL),(679,657,'2011-03-15 20:11:00','2011-03-15 20:11:00','uetliberg','Üetliberg',NULL,NULL),(680,679,'2011-03-15 20:11:00','2011-03-15 20:11:00','aqua','Agua Zürich',NULL,NULL),(681,679,'2011-03-15 20:11:00','2011-03-15 20:11:00','albis','Albis Bonstetten',NULL,NULL),(682,679,'2011-03-15 20:11:00','2011-03-15 20:11:00','limmattal','Limmattal Schlieren-Dietikon',NULL,NULL),(683,679,'2011-03-15 20:11:00','2011-03-15 20:11:00','maepfa_saeuliamt','Maitlipfadi Säuliamt',NULL,NULL),(684,592,'2011-03-15 20:11:00','2011-03-15 20:11:00','winterthur','Pfadi Region Winterthur',NULL,NULL),(685,684,'2011-03-15 20:11:00','2011-03-15 20:11:00','irchel','Ring Irchel',NULL,NULL),(686,685,'2011-03-15 20:11:00','2011-03-15 20:11:00','andelfingen','Andelfingen',NULL,NULL),(687,685,'2011-03-15 20:11:00','2011-03-15 20:11:00','elgg','Elgg',NULL,NULL),(688,685,'2011-03-15 20:11:00','2011-03-15 20:11:00','heidegg','Heidegg Embrach',NULL,NULL),(689,685,'2011-03-15 20:11:00','2011-03-15 20:11:00','wart','Pfadi Wart in Neftenbach, Pfungen, Dättlikon, Buch am Irchel',NULL,NULL),(690,684,'2011-03-15 20:11:00','2011-03-15 20:11:00','auriga','Ring Auriga',NULL,NULL),(691,690,'2011-03-15 20:11:00','2011-03-15 20:11:00','diviko','Diviko Winterthur',NULL,NULL),(692,690,'2011-03-15 20:11:00','2011-03-15 20:11:00','gallispitz','Gallispitz Winterthur',NULL,NULL),(693,690,'2011-03-15 20:11:00','2011-03-15 20:11:00','orion','Orion Wiesendangen',NULL,NULL),(694,690,'2011-03-15 20:11:00','2011-03-15 20:11:00','seuzach','Seuzach',NULL,NULL),(695,684,'2011-03-15 20:11:00','2011-03-15 20:11:00','suso','Korps Suso',NULL,NULL),(696,695,'2011-03-15 20:11:00','2011-03-15 20:11:00','goldenberg','Pfadi Goldenberg Winterthur',NULL,NULL),(697,695,'2011-03-15 20:11:00','2011-03-15 20:11:00','hartmannen','Hartmannen Winterthur',NULL,NULL),(698,695,'2011-03-15 20:11:00','2011-03-15 20:11:00','hohenlandenberg','Pfadi Hohenlandenberg Winterthur',NULL,NULL),(699,695,'2011-03-15 20:11:00','2011-03-15 20:11:00','neuburger','Neuburger/Wartenseer Winterthur',NULL,NULL),(700,684,'2011-03-15 20:11:00','2011-03-15 20:11:00','polaris','Ring Polaris',NULL,NULL),(701,700,'2011-03-15 20:11:00','2011-03-15 20:11:00','avalon','Avalon Winterthur',NULL,NULL),(702,700,'2011-03-15 20:11:00','2011-03-15 20:11:00','nepomuk','Nepomuk Winterthur',NULL,NULL),(703,700,'2011-03-15 20:11:00','2011-03-15 20:11:00','pta_atlantis','PTA Atlantis Winterthur',NULL,NULL),(704,700,'2011-03-15 20:11:00','2011-03-15 20:11:00','waldmann','Waldmann Winterthur',NULL,NULL),(705,684,'2011-03-15 20:11:00','2011-03-15 20:11:00','antares','Ring Antares',NULL,NULL),(706,705,'2011-03-15 20:11:00','2011-03-15 20:11:00','bubenberg','Pfadiabteilung Bubenberg',NULL,NULL),(707,705,'2011-03-15 20:11:00','2011-03-15 20:11:00','dunant','Dunant Winterthur',NULL,NULL),(708,705,'2011-03-15 20:11:00','2011-03-15 20:11:00','eschenberg','Eschenberg Winterthur',NULL,NULL),(709,705,'2011-03-15 20:11:00','2011-03-15 20:11:00','fontana','Fontana Winterthur',NULL,NULL),(710,272,'2011-03-15 20:11:00','2011-03-15 20:11:00','emmenbruecke','Pfadi Emmenbrücke',NULL,NULL);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logins`
--

DROP TABLE IF EXISTS `logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(64) NOT NULL,
  `pwResetKey` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `logins_user_id_uniq` (`user_id`),
  CONSTRAINT `logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logins`
--

LOCK TABLES `logins` WRITE;
/*!40000 ALTER TABLE `logins` DISABLE KEYS */;
INSERT INTO `logins` VALUES (4,7,'2011-11-20 17:25:15','2011-12-13 19:15:52','c381b4abad33cadabff41f217e4e57a30cbc558bf5d8f5e228a3992961caa93c','80a1a998294ae102df23dece1d27e1baeb8c13a342fbcc30f7025b09e07035c1',NULL),(5,8,'2011-11-20 17:46:38','2011-11-20 17:46:38','d3cee21f42b06ff6cdc26b56fabee8013da1cef208460adf8f6ecda7ca2169a0','1b03e64a9ad341910240cd7475a9ab82a104066be4da1e6c8860c5cea0a638af',NULL),(14,20,'2011-11-30 18:30:02','2011-11-30 18:30:02','9e6925506b3f59169ad32a5c79f79db8940403be7df0b2b77054b142b75d4273','f11f76b422d9af9d1a3f476c10ab1c215f45404105f05b14dfd8acde2b139e26',NULL),(15,21,'2012-01-16 18:44:23','2012-01-16 18:44:23','bc236d3af824e9391eb835de36f9453e7d4a212cd5a0f3d11d480cfb15ea8536','045043ad69bedd87fdae7928410e460d9d6f9bf1d3d4a0eec2c2903d0f8d9757',NULL),(16,24,'2012-01-17 16:51:42','2012-01-17 16:51:42','fbaa9b3531bec5c7aa383e6a1724f5edd97a4fe55637400cd444f0a109f4f900','0fa2f05b0d525153c30e6d3a253c0a394537e60cbe06a84a8ce5cbd000fc93ec',NULL),(17,25,'2012-01-17 16:56:13','2012-01-17 16:56:13','b80b6e51b6911d5add584d59e2c42ef6307d5e92ac15e8dcc84120626b43844e','113a5bccb85702db5a3d25021e0f94e71e94c19fe4846d459c00db933a821739',NULL);
/*!40000 ALTER TABLE `logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `periods`
--

DROP TABLE IF EXISTS `periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `camp_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `start` date NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `periods_camp_id_idx` (`camp_id`),
  CONSTRAINT `periods_ibfk_1` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periods`
--

LOCK TABLES `periods` WRITE;
/*!40000 ALTER TABLE `periods` DISABLE KEYS */;
INSERT INTO `periods` VALUES (3,3,'2011-11-23 10:29:06','2011-11-23 10:29:06','2011-11-28',3),(4,4,'2011-11-25 12:53:57','2011-11-25 12:53:57','2011-11-01',10),(6,6,'2011-11-26 20:18:59','2011-11-26 20:18:59','2011-11-28',6),(7,7,'2011-11-26 21:14:26','2011-11-26 21:14:26','2011-11-14',6);
/*!40000 ALTER TABLE `periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_contents`
--

DROP TABLE IF EXISTS `plugin_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `text` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plugin_contents_plugin_id_idx` (`plugin_id`),
  CONSTRAINT `plugin_contents_ibfk_1` FOREIGN KEY (`plugin_id`) REFERENCES `plugins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_contents`
--

LOCK TABLES `plugin_contents` WRITE;
/*!40000 ALTER TABLE `plugin_contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_headers`
--

DROP TABLE IF EXISTS `plugin_headers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_headers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `text` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plugin_headers_plugin_id_idx` (`plugin_id`),
  CONSTRAINT `plugin_headers_ibfk_1` FOREIGN KEY (`plugin_id`) REFERENCES `plugins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_headers`
--

LOCK TABLES `plugin_headers` WRITE;
/*!40000 ALTER TABLE `plugin_headers` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_headers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugins`
--

DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `pluginName` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `plugins_event_id_idx` (`event_id`),
  CONSTRAINT `plugins_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugins`
--

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcamps`
--

DROP TABLE IF EXISTS `subcamps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subcamps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `period_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subcamps_period_id_idx` (`period_id`),
  CONSTRAINT `subcamps_ibfk_1` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcamps`
--

LOCK TABLES `subcamps` WRITE;
/*!40000 ALTER TABLE `subcamps` DISABLE KEYS */;
/*!40000 ALTER TABLE `subcamps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_camps`
--

DROP TABLE IF EXISTS `user_camps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_camps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `camp_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `role` int(11) NOT NULL,
  `requestedRole` int(11) DEFAULT NULL,
  `invitationAccepted` tinyint(1) NOT NULL,
  `requestAcceptedBy_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_camp_unique` (`user_id`,`camp_id`),
  KEY `user_camps_user_id_idx` (`user_id`),
  KEY `user_camps_camp_id_idx` (`camp_id`),
  KEY `user_camps_requestAcceptedBy_id_idx` (`requestAcceptedBy_id`),
  CONSTRAINT `user_camps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_camps_ibfk_2` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`),
  CONSTRAINT `user_camps_ibfk_3` FOREIGN KEY (`requestAcceptedBy_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_camps`
--

LOCK TABLES `user_camps` WRITE;
/*!40000 ALTER TABLE `user_camps` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_camps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `role` int(11) NOT NULL,
  `requestedRole` int(11) DEFAULT NULL,
  `invitationAccepted` tinyint(1) NOT NULL,
  `requestAcceptedBy_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_group_unique` (`user_id`,`group_id`),
  KEY `user_groups_user_id_idx` (`user_id`),
  KEY `user_groups_group_id_idx` (`group_id`),
  KEY `user_groups_requestAcceptedBy_id_idx` (`requestAcceptedBy_id`),
  CONSTRAINT `user_groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  CONSTRAINT `user_groups_ibfk_3` FOREIGN KEY (`requestAcceptedBy_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_groups`
--

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
INSERT INTO `user_groups` VALUES (1,7,249,'2011-11-26 18:54:13','2011-11-26 18:54:13',10,NULL,1,8);
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_relationships`
--

DROP TABLE IF EXISTS `user_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_relationships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_to_unique` (`from_id`,`to_id`),
  KEY `user_relationships_from_id_idx` (`from_id`),
  KEY `user_relationships_to_id_idx` (`to_id`),
  CONSTRAINT `user_relationships_ibfk_1` FOREIGN KEY (`from_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_relationships_ibfk_2` FOREIGN KEY (`to_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_relationships`
--

LOCK TABLES `user_relationships` WRITE;
/*!40000 ALTER TABLE `user_relationships` DISABLE KEYS */;
INSERT INTO `user_relationships` VALUES (4,8,7,'2011-11-26 23:23:50','2011-11-26 23:23:50',1);
/*!40000 ALTER TABLE `user_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `username` varchar(32) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `activationCode` varchar(64) DEFAULT NULL,
  `scoutname` varchar(32) DEFAULT NULL,
  `firstname` varchar(32) DEFAULT NULL,
  `surname` varchar(32) DEFAULT NULL,
  `street` varchar(32) DEFAULT NULL,
  `zipcode` varchar(16) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `homeNr` varchar(16) DEFAULT NULL,
  `mobilNr` varchar(16) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `ahv` varchar(32) DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `jsPersNr` varchar(16) DEFAULT NULL,
  `jsEdu` varchar(16) DEFAULT NULL,
  `pbsEdu` varchar(16) DEFAULT NULL,
  `imageMime` varchar(32) DEFAULT NULL,
  `imageData` longtext,
  `state` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_uniq` (`username`),
  UNIQUE KEY `users_email_uniq` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (7,'2011-11-20 17:25:15','2011-11-20 17:25:18','qwerasdf','qwer@asdf.ch',NULL,'qwerasdf','qwer','asdf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N;','Activated','User'),(8,'2011-11-20 17:46:38','2011-11-20 17:46:40','wertsdfg','wert@sdfg.ch',NULL,'wertsdfg','wert','sdfg',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N;','Activated','User'),(20,'2011-11-30 18:30:02','2011-11-30 18:30:25','asdflkjh','asdf@lkjh.ch',NULL,'asdfasdf','asdfasdf','asdfasdf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N;','Activated','User'),(21,'2012-01-16 18:44:23','2012-01-16 18:44:23','asdf234','asdf@lkj.com','9eac0eda49711d67efdc2edf85a4ac3d','asdf','asdfqwer','qwerasdf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N;','NonRegistered','User'),(22,'2012-01-17 16:29:54','2012-01-17 16:35:28','sdfgdfgh','zxcv@asdf.at',NULL,'asdf','asdfzxcv','zxcvasdf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N;','NonRegistered','User'),(23,'2012-01-17 16:44:09','2012-01-17 16:44:09','dfghdfgh','dfgh@fghj.at',NULL,'asdf','asdf','asdf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N;','NonRegistered','User'),(24,'2012-01-17 16:51:42','2012-01-17 16:51:42','poiuqwerpoiu','poiu@poiuqwer.ch','f8ff09d5ca11b00d3369c224e2ffb814','asdf','asfd','asdf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N;','NonRegistered','User'),(25,'2012-01-17 16:56:13','2012-01-17 16:56:15','asdfasdfasdf','asdf@asdfasdf.ch',NULL,'asdf','asdf','asdf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'N;','Activated','User');
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

-- Dump completed on 2012-02-06 23:19:29
