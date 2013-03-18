
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
DROP TABLE IF EXISTS `camp_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `camp_types` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `camp_types` WRITE;
/*!40000 ALTER TABLE `camp_types` DISABLE KEYS */;
INSERT INTO `camp_types` VALUES ('00764960','2013-03-17 15:36:02','2013-03-17 15:36:02','J+S Jugendsport','jugendsport'),('59526376','2013-03-17 15:36:02','2013-03-17 15:36:02','J+S Ausbildung','ausbildung'),('71558608','2013-03-17 15:36:02','2013-03-17 15:36:02','J+S Kindersport','kindersport');
/*!40000 ALTER TABLE `camp_types` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `camps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `camps` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `creator_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `group_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `visibility` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'public',
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_name_unique` (`group_id`,`name`),
  UNIQUE KEY `owner_name_unique` (`owner_id`,`name`),
  KEY `IDX_3D166BE561220EA6` (`creator_id`),
  KEY `IDX_3D166BE57E3C61F9` (`owner_id`),
  KEY `IDX_3D166BE5FE54D947` (`group_id`),
  CONSTRAINT `FK_3D166BE5FE54D947` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  CONSTRAINT `FK_3D166BE561220EA6` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_3D166BE57E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `camps` WRITE;
/*!40000 ALTER TABLE `camps` DISABLE KEYS */;
/*!40000 ALTER TABLE `camps` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `days` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `period_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `dayOffset` int(11) NOT NULL,
  `notes` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `offset_period_idx` (`dayOffset`,`period_id`),
  KEY `IDX_EBE4FC66EC8B7ADE` (`period_id`),
  CONSTRAINT `FK_EBE4FC66EC8B7ADE` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `days` WRITE;
/*!40000 ALTER TABLE `days` DISABLE KEYS */;
/*!40000 ALTER TABLE `days` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_categories` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `camp_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `numberingStyle` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `eventType_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_621D9F4777075ABB` (`camp_id`),
  KEY `IDX_621D9F47C15B25DE` (`eventType_id`),
  CONSTRAINT `FK_621D9F47C15B25DE` FOREIGN KEY (`eventType_id`) REFERENCES `event_types` (`id`),
  CONSTRAINT `FK_621D9F4777075ABB` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_categories` WRITE;
/*!40000 ALTER TABLE `event_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_instances` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `event_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `period_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `minOffsetStart` int(11) NOT NULL,
  `minOffsetEnd` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FCB23E5471F7E88B` (`event_id`),
  KEY `IDX_FCB23E54EC8B7ADE` (`period_id`),
  CONSTRAINT `FK_FCB23E54EC8B7ADE` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`),
  CONSTRAINT `FK_FCB23E5471F7E88B` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_instances` WRITE;
/*!40000 ALTER TABLE `event_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_instances` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_prototypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_prototypes` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_prototypes` WRITE;
/*!40000 ALTER TABLE `event_prototypes` DISABLE KEYS */;
INSERT INTO `event_prototypes` VALUES ('11133262','2013-03-17 15:36:02','2013-03-17 15:36:02','Ausbildung',1),('32939798','2013-03-17 15:36:02','2013-03-17 15:36:02','Aktivität',1),('41185680','2013-03-17 15:36:02','2013-03-17 15:36:02','Wanderung',1),('70978687','2013-03-17 15:36:02','2013-03-17 15:36:02','Sportblock',1);
/*!40000 ALTER TABLE `event_prototypes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_resps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_resps` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `event_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `userCamp_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B8577DD671F7E88B` (`event_id`),
  KEY `IDX_B8577DD624DB9F99` (`userCamp_id`),
  CONSTRAINT `FK_B8577DD624DB9F99` FOREIGN KEY (`userCamp_id`) REFERENCES `user_camps` (`id`),
  CONSTRAINT `FK_B8577DD671F7E88B` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_resps` WRITE;
/*!40000 ALTER TABLE `event_resps` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_resps` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_templates` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `medium` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `filename` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `eventPrototype_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prototype_medium_unique` (`eventPrototype_id`,`medium`),
  KEY `IDX_E9BD43B3C67345B7` (`medium`),
  KEY `IDX_E9BD43B3C89C3AF1` (`eventPrototype_id`),
  CONSTRAINT `FK_E9BD43B3C89C3AF1` FOREIGN KEY (`eventPrototype_id`) REFERENCES `event_prototypes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E9BD43B3C67345B7` FOREIGN KEY (`medium`) REFERENCES `media` (`name`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_templates` WRITE;
/*!40000 ALTER TABLE `event_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_templates` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_type_event_prototypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_type_event_prototypes` (
  `eventtype_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `eventprototype_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`eventtype_id`,`eventprototype_id`),
  KEY `IDX_71ECB636EE61C42` (`eventtype_id`),
  KEY `IDX_71ECB636D0ADE109` (`eventprototype_id`),
  CONSTRAINT `FK_71ECB636D0ADE109` FOREIGN KEY (`eventprototype_id`) REFERENCES `event_prototypes` (`id`),
  CONSTRAINT `FK_71ECB636EE61C42` FOREIGN KEY (`eventtype_id`) REFERENCES `event_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_type_event_prototypes` WRITE;
/*!40000 ALTER TABLE `event_type_event_prototypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_type_event_prototypes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_types` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `defaultColor` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `defaultNumberingStyle` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `campType_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_182B381CA75B389` (`campType_id`),
  CONSTRAINT `FK_182B381CA75B389` FOREIGN KEY (`campType_id`) REFERENCES `camp_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_types` WRITE;
/*!40000 ALTER TABLE `event_types` DISABLE KEYS */;
INSERT INTO `event_types` VALUES ('00445058','2013-03-17 15:36:02','2013-03-17 15:36:02','Ausbildung','ff0000','1','59526376'),('63982270','2013-03-17 15:36:02','2013-03-17 15:36:02','Sonstige','0000ff','I','71558608'),('66211292','2013-03-17 15:36:02','2013-03-17 15:36:02','Sonstige','0000ff','I','59526376'),('76473604','2013-03-17 15:36:02','2013-03-17 15:36:02','Lageraktivität','ff0000','a','71558608'),('78976194','2013-03-17 15:36:02','2013-03-17 15:36:02','Sonstige','0000ff','I','00764960'),('87445036','2013-03-17 15:36:02','2013-03-17 15:36:02','Lagersport','00ff00','1','00764960'),('92449710','2013-03-17 15:36:02','2013-03-17 15:36:02','Lageraktivität','ff0000','a','00764960'),('98686904','2013-03-17 15:36:02','2013-03-17 15:36:02','Lagersport','00ff00','1','71558608');
/*!40000 ALTER TABLE `event_types` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `camp_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prototype_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `title` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5387574A77075ABB` (`camp_id`),
  KEY `IDX_5387574AA76ED395` (`user_id`),
  KEY `IDX_5387574A25998077` (`prototype_id`),
  CONSTRAINT `FK_5387574A25998077` FOREIGN KEY (`prototype_id`) REFERENCES `event_prototypes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5387574A77075ABB` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`),
  CONSTRAINT `FK_5387574AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `grouprequests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grouprequests` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `requester_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `motivation` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_89CD63203DA5256D` (`image_id`),
  KEY `IDX_89CD6320727ACA70` (`parent_id`),
  KEY `IDX_89CD6320ED442CF4` (`requester_id`),
  CONSTRAINT `FK_89CD6320ED442CF4` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_89CD63203DA5256D` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  CONSTRAINT `FK_89CD6320727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `grouprequests` WRITE;
/*!40000 ALTER TABLE `grouprequests` DISABLE KEYS */;
/*!40000 ALTER TABLE `grouprequests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_F06D39703DA5256D` (`image_id`),
  UNIQUE KEY `group_parent_name_unique` (`parent_id`,`name`),
  KEY `IDX_F06D3970727ACA70` (`parent_id`),
  KEY `group_name_idx` (`name`),
  CONSTRAINT `FK_F06D39703DA5256D` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  CONSTRAINT `FK_F06D3970727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `imageMime` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `imageData` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:object)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logins` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `pwResetKey` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_613D7A4A76ED395` (`user_id`),
  CONSTRAINT `FK_613D7A4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `logins` WRITE;
/*!40000 ALTER TABLE `logins` DISABLE KEYS */;
INSERT INTO `logins` VALUES ('241295bd','8d7fabde','2013-03-17 15:48:29','2013-03-17 15:48:29','$2y$10$49defdd9032a52b4a6fbcu6b8fkpXuv164AT8M4TL4iAI1QOANmz2','49defdd9032a52b4a6fbc05f152d219a',NULL),('29e91642','7dd53350','2013-03-17 16:06:22','2013-03-17 16:06:22','$2y$10$b35fb416a757b83bd5b1cuXCcdl6hjgB58eCZbPMQyJV3UqEvuJ2q','b35fb416a757b83bd5b1c1d1cfbe30e9',NULL),('734a1eca','d1b747b','2013-03-17 15:53:14','2013-03-17 15:53:14','$2y$10$c046e53ff011f6e831481eZZKCjVFyLanxC.rDPY8xJda0.L/5sQe','c046e53ff011f6e831481f7b43bb0f76',NULL),('9cffc1e','2de20f49','2013-03-17 16:04:54','2013-03-17 16:04:54','$2y$10$247968a5c4647e27e2b89uBX9YExJxLofRZBBy.BdExI1zvb7vHB2','247968a5c4647e27e2b8947b51043acc',NULL),('aa8ec370','33ec6c80','2013-03-17 15:50:56','2013-03-17 15:50:56','$2y$10$28b07a25f88c94e92c394uLP96GcABZ8WU7fzsi6NwJDo3cCZuLBi','28b07a25f88c94e92c39427c5c925693',NULL),('af22502f','368cee54','2013-03-17 15:57:00','2013-03-17 15:57:00','$2y$10$c38321f2f9d6a7312892duPCZ88hhGvQbLPIuy/G/U9jL5Mtm2iD6','c38321f2f9d6a7312892d379fbb66ea5',NULL);
/*!40000 ALTER TABLE `logins` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES ('mobile'),('print'),('web');
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periods` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `camp_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `start` date NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_671798A277075ABB` (`camp_id`),
  CONSTRAINT `FK_671798A277075ABB` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `periods` WRITE;
/*!40000 ALTER TABLE `periods` DISABLE KEYS */;
/*!40000 ALTER TABLE `periods` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugin_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_instances` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `event_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `pluginPrototype_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4A3237C071F7E88B` (`event_id`),
  KEY `IDX_4A3237C0A46D7BCB` (`pluginPrototype_id`),
  CONSTRAINT `FK_4A3237C0A46D7BCB` FOREIGN KEY (`pluginPrototype_id`) REFERENCES `plugin_prototypes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_4A3237C071F7E88B` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugin_instances` WRITE;
/*!40000 ALTER TABLE `plugin_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_instances` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugin_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_positions` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `container` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `eventTemplate_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pluginPrototype_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plugin_template_unique` (`pluginPrototype_id`,`eventTemplate_id`),
  KEY `IDX_E68AD2D5B8E3A938` (`eventTemplate_id`),
  KEY `IDX_E68AD2D5A46D7BCB` (`pluginPrototype_id`),
  CONSTRAINT `FK_E68AD2D5A46D7BCB` FOREIGN KEY (`pluginPrototype_id`) REFERENCES `plugin_prototypes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_E68AD2D5B8E3A938` FOREIGN KEY (`eventTemplate_id`) REFERENCES `event_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugin_positions` WRITE;
/*!40000 ALTER TABLE `plugin_positions` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_positions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugin_prototypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_prototypes` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plugin_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `maxInstances` smallint(6) DEFAULT NULL,
  `defaultInstances` smallint(6) NOT NULL,
  `minInstances` smallint(6) NOT NULL,
  `config` longtext COLLATE utf8_unicode_ci NOT NULL,
  `eventPrototype_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_13F08D4BEC942BCF` (`plugin_id`),
  KEY `IDX_13F08D4BC89C3AF1` (`eventPrototype_id`),
  CONSTRAINT `FK_13F08D4BC89C3AF1` FOREIGN KEY (`eventPrototype_id`) REFERENCES `event_prototypes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_13F08D4BEC942BCF` FOREIGN KEY (`plugin_id`) REFERENCES `plugins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugin_prototypes` WRITE;
/*!40000 ALTER TABLE `plugin_prototypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_prototypes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugindata_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugindata_content` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plugin_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `text` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_30B461CEC942BCF` (`plugin_id`),
  CONSTRAINT `FK_30B461CEC942BCF` FOREIGN KEY (`plugin_id`) REFERENCES `plugin_instances` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugindata_content` WRITE;
/*!40000 ALTER TABLE `plugindata_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugindata_content` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugindata_header`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugindata_header` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plugin_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `text` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C2FD67DBEC942BCF` (`plugin_id`),
  CONSTRAINT `FK_C2FD67DBEC942BCF` FOREIGN KEY (`plugin_id`) REFERENCES `plugin_instances` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugindata_header` WRITE;
/*!40000 ALTER TABLE `plugindata_header` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugindata_header` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `uid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uid` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `uid` WRITE;
/*!40000 ALTER TABLE `uid` DISABLE KEYS */;
INSERT INTO `uid` VALUES ('241295bd','CoreApi\\Entity\\Login'),('29e91642','CoreApi\\Entity\\Login'),('2de20f49','CoreApi\\Entity\\User'),('33ec6c80','CoreApi\\Entity\\User'),('368cee54','CoreApi\\Entity\\User'),('734a1eca','CoreApi\\Entity\\Login'),('7dd53350','CoreApi\\Entity\\User'),('8d7fabde','CoreApi\\Entity\\User'),('9cffc1e','CoreApi\\Entity\\Login'),('aa8ec370','CoreApi\\Entity\\Login'),('af22502f','CoreApi\\Entity\\Login'),('d1b747b','CoreApi\\Entity\\User');
/*!40000 ALTER TABLE `uid` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_camps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_camps` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `camp_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `role` int(11) NOT NULL,
  `requestedRole` int(11) DEFAULT NULL,
  `invitationAccepted` tinyint(1) NOT NULL,
  `requestAcceptedBy_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_camp_unique` (`user_id`,`camp_id`),
  KEY `IDX_DFD490BDA76ED395` (`user_id`),
  KEY `IDX_DFD490BD77075ABB` (`camp_id`),
  KEY `IDX_DFD490BDB07C3E84` (`requestAcceptedBy_id`),
  CONSTRAINT `FK_DFD490BDB07C3E84` FOREIGN KEY (`requestAcceptedBy_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_DFD490BD77075ABB` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`),
  CONSTRAINT `FK_DFD490BDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_camps` WRITE;
/*!40000 ALTER TABLE `user_camps` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_camps` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `group_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `role` int(11) NOT NULL,
  `requestedRole` int(11) DEFAULT NULL,
  `invitationAccepted` tinyint(1) NOT NULL,
  `requestAcceptedBy_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_group_unique` (`user_id`,`group_id`),
  KEY `IDX_953F224DA76ED395` (`user_id`),
  KEY `IDX_953F224DFE54D947` (`group_id`),
  KEY `IDX_953F224DB07C3E84` (`requestAcceptedBy_id`),
  CONSTRAINT `FK_953F224DB07C3E84` FOREIGN KEY (`requestAcceptedBy_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_953F224DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_953F224DFE54D947` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_relationships` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `from_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `to_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `counterpart` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_to_unique` (`from_id`,`to_id`),
  UNIQUE KEY `UNIQ_2376C5F7564F5C9F` (`counterpart`),
  KEY `IDX_2376C5F778CED90B` (`from_id`),
  KEY `IDX_2376C5F730354A65` (`to_id`),
  CONSTRAINT `FK_2376C5F7564F5C9F` FOREIGN KEY (`counterpart`) REFERENCES `user_relationships` (`id`),
  CONSTRAINT `FK_2376C5F730354A65` FOREIGN KEY (`to_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_2376C5F778CED90B` FOREIGN KEY (`from_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_relationships` WRITE;
/*!40000 ALTER TABLE `user_relationships` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_relationships` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `username` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activationCode` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scoutname` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `surname` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `homeNr` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobilNr` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `ahv` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `jsPersNr` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jsEdu` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbsEdu` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`),
  UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
  UNIQUE KEY `UNIQ_1483A5E93DA5256D` (`image_id`),
  CONSTRAINT `FK_1483A5E93DA5256D` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('2de20f49',NULL,'2013-03-17 16:04:54','2013-03-17 16:04:56','kugserasd','lasduh@kugs.ch',NULL,'aslih','ukgsf','uhrjt',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activated','User'),('33ec6c80',NULL,'2013-03-17 15:50:55','2013-03-17 15:52:06','asdfqwertukyh','lisasdfhfl@luhse.ch',NULL,'liuhf','liuhlsijet','iuhouklsher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activated','User'),('368cee54',NULL,'2013-03-17 15:56:59','2013-03-17 15:57:02','kugser','luh@kugs.ch',NULL,'aslih','ukgsf','uhrjt',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activated','User'),('7dd53350',NULL,'2013-03-17 16:06:22','2013-03-17 16:06:24','asfyg','kuygs@kuygs.ch',NULL,'auykhe','ulsj','l8uhseri',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activated','User'),('8d7fabde',NULL,'2013-03-17 15:48:29','2013-03-17 15:48:29','asdflihlei','lishfl@luhse.ch','8a7bcabb7f9193a0690eb3b7d4843fb8f5e3d5d725cc02c7f87de373f4919d15','liuhf','liuhlsijet','iuhouklsher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Registered','User'),('d1b747b',NULL,'2013-03-17 15:53:14','2013-03-17 15:53:16','asdfqtukyh','sdfhfl@lue.ch',NULL,'liuhf','liuhlsijet','iuhouklsher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activated','User');
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

