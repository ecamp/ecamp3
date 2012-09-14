
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
DROP TABLE IF EXISTS `event_prototype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_prototype` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_prototype` WRITE;
/*!40000 ALTER TABLE `event_prototype` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_prototype` ENABLE KEYS */;
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
  CONSTRAINT `FK_5387574A25998077` FOREIGN KEY (`prototype_id`) REFERENCES `event_prototype` (`id`) ON DELETE CASCADE,
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
INSERT INTO `logins` VALUES ('84b7a5d3','dd75f323','2012-09-14 22:38:03','2012-09-14 22:38:03','6bc42e59b98eacb3bc377a3624a350312cb395717a68c0578845bd72175b014d','11534044f1204d899faa9693f3582a6b0503e678cac2f9f6c86617dcda312366',NULL),('eca56f8f','f586890b','2012-09-14 22:05:55','2012-09-14 22:05:55','a2f2e455d03365d5d1c3158ec81bff4f7a66b523380de5140c4486d95bc82710','9886acbbf387b552809b2a3c335a6231426afc619aa8c017664e5b604e9dc22a',NULL);
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
DROP TABLE IF EXISTS `plugin_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_contents` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plugin_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `text` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_14325198EC942BCF` (`plugin_id`),
  CONSTRAINT `FK_14325198EC942BCF` FOREIGN KEY (`plugin_id`) REFERENCES `plugins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugin_contents` WRITE;
/*!40000 ALTER TABLE `plugin_contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_contents` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugin_headers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_headers` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plugin_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `text` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_35A1D865EC942BCF` (`plugin_id`),
  CONSTRAINT `FK_35A1D865EC942BCF` FOREIGN KEY (`plugin_id`) REFERENCES `plugins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugin_headers` WRITE;
/*!40000 ALTER TABLE `plugin_headers` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugin_headers` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `pluginconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pluginconfig` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `prototype_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `pluginName` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `maxInstances` smallint(6) DEFAULT NULL,
  `defaultInstances` smallint(6) NOT NULL,
  `minInstances` smallint(6) NOT NULL,
  `config` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prototype_plugin_unique` (`prototype_id`,`pluginName`),
  KEY `IDX_5C56E32225998077` (`prototype_id`),
  CONSTRAINT `FK_5C56E32225998077` FOREIGN KEY (`prototype_id`) REFERENCES `event_prototype` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `pluginconfig` WRITE;
/*!40000 ALTER TABLE `pluginconfig` DISABLE KEYS */;
/*!40000 ALTER TABLE `pluginconfig` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `event_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `pluginName` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `pluginConfig_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EC85F67171F7E88B` (`event_id`),
  KEY `IDX_EC85F671282281AF` (`pluginConfig_id`),
  CONSTRAINT `FK_EC85F671282281AF` FOREIGN KEY (`pluginConfig_id`) REFERENCES `pluginconfig` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_EC85F67171F7E88B` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `template_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_map` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `prototype_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `medium` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prototype_medium_unique` (`prototype_id`,`medium`),
  KEY `IDX_3DC6239925998077` (`prototype_id`),
  CONSTRAINT `FK_3DC6239925998077` FOREIGN KEY (`prototype_id`) REFERENCES `event_prototype` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `template_map` WRITE;
/*!40000 ALTER TABLE `template_map` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_map` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `template_map_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `template_map_item` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `container` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `templateMap_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pluginConfig_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plugin_template_unique` (`pluginConfig_id`,`templateMap_id`),
  KEY `IDX_D365B2F8ED876501` (`templateMap_id`),
  KEY `IDX_D365B2F8282281AF` (`pluginConfig_id`),
  CONSTRAINT `FK_D365B2F8282281AF` FOREIGN KEY (`pluginConfig_id`) REFERENCES `pluginconfig` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_D365B2F8ED876501` FOREIGN KEY (`templateMap_id`) REFERENCES `template_map` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `template_map_item` WRITE;
/*!40000 ALTER TABLE `template_map_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_map_item` ENABLE KEYS */;
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
INSERT INTO `uid` VALUES ('84b7a5d3','CoreApi\\Entity\\Login'),('dd75f323','CoreApi\\Entity\\User'),('eca56f8f','CoreApi\\Entity\\Login'),('f586890b','CoreApi\\Entity\\User');
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
INSERT INTO `users` VALUES ('dd75f323',NULL,'2012-09-14 22:38:03','2012-09-14 22:38:04','aslieh','lihu@lkauhse.ch',NULL,'aslihr','wioeh','lihasf',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activated','User'),('f586890b',NULL,'2012-09-14 22:05:55','2012-09-14 22:05:57','kusehrl','lushe@luh.ch',NULL,'asdr','liuhe','ihjsif',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activated','User');
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

