
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
  `id` varchar(255) NOT NULL,
  `creator_id` varchar(255) NOT NULL,
  `owner_id` varchar(255) DEFAULT NULL,
  `group_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(32) NOT NULL,
  `title` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_name_unique` (`group_id`,`name`),
  UNIQUE KEY `owner_name_unique` (`owner_id`,`name`),
  KEY `IDX_3D166BE561220EA6` (`creator_id`),
  KEY `IDX_3D166BE57E3C61F9` (`owner_id`),
  KEY `IDX_3D166BE5FE54D947` (`group_id`),
  CONSTRAINT `FK_3D166BE561220EA6` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_3D166BE57E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_3D166BE5FE54D947` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `camps` WRITE;
/*!40000 ALTER TABLE `camps` DISABLE KEYS */;
INSERT INTO `camps` VALUES ('','14fcb010','14fcb010',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00','test','test');
/*!40000 ALTER TABLE `camps` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `days` (
  `id` varchar(255) NOT NULL,
  `period_id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `dayOffset` int(11) NOT NULL,
  `notes` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `offset_period_idx` (`dayOffset`,`period_id`),
  KEY `IDX_EBE4FC66EC8B7ADE` (`period_id`),
  CONSTRAINT `FK_EBE4FC66EC8B7ADE` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `days` WRITE;
/*!40000 ALTER TABLE `days` DISABLE KEYS */;
/*!40000 ALTER TABLE `days` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_instances` (
  `id` varchar(255) NOT NULL,
  `event_id` varchar(255) NOT NULL,
  `period_id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `minOffsetStart` int(11) NOT NULL,
  `minOffsetEnd` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FCB23E5471F7E88B` (`event_id`),
  KEY `IDX_FCB23E54EC8B7ADE` (`period_id`),
  CONSTRAINT `FK_FCB23E5471F7E88B` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  CONSTRAINT `FK_FCB23E54EC8B7ADE` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_instances` WRITE;
/*!40000 ALTER TABLE `event_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_instances` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_prototypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_prototypes` (
  `id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_prototypes` WRITE;
/*!40000 ALTER TABLE `event_prototypes` DISABLE KEYS */;
INSERT INTO `event_prototypes` VALUES ('1','0000-00-00 00:00:00','0000-00-00 00:00:00','Lagersport',1);
/*!40000 ALTER TABLE `event_prototypes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `event_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_templates` (
  `id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `filename` varchar(128) NOT NULL,
  `eventPrototype_id` varchar(255) NOT NULL,
  `medium` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prototype_medium_unique` (`eventPrototype_id`,`medium`),
  KEY `IDX_E9BD43B3C89C3AF1` (`eventPrototype_id`),
  KEY `IDX_E9BD43B3C67345B7` (`medium`),
  CONSTRAINT `FK_E9BD43B3C67345B7` FOREIGN KEY (`medium`) REFERENCES `media` (`name`) ON DELETE CASCADE,
  CONSTRAINT `FK_E9BD43B3C89C3AF1` FOREIGN KEY (`eventPrototype_id`) REFERENCES `event_prototypes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_templates` WRITE;
/*!40000 ALTER TABLE `event_templates` DISABLE KEYS */;
INSERT INTO `event_templates` VALUES ('1','0000-00-00 00:00:00','0000-00-00 00:00:00','prototype_lagersport.phtml/notab','1','web');
/*!40000 ALTER TABLE `event_templates` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` varchar(255) NOT NULL,
  `camp_id` varchar(255) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `prototype_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `title` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5387574A77075ABB` (`camp_id`),
  KEY `IDX_5387574AA76ED395` (`user_id`),
  KEY `IDX_5387574A25998077` (`prototype_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`prototype_id`) REFERENCES `event_prototypes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_5387574A77075ABB` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`),
  CONSTRAINT `FK_5387574AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES ('453ac999','',NULL,'1','2012-11-20 19:39:38','2012-11-20 19:39:38','aeb415d118387d691e3e8272302dfa42');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `grouprequests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grouprequests` (
  `id` varchar(255) NOT NULL,
  `parent_id` varchar(255) DEFAULT NULL,
  `image_id` varchar(255) DEFAULT NULL,
  `requester_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(64) NOT NULL,
  `motivation` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_89CD63203DA5256D` (`image_id`),
  KEY `IDX_89CD6320727ACA70` (`parent_id`),
  KEY `IDX_89CD6320ED442CF4` (`requester_id`),
  CONSTRAINT `FK_89CD63203DA5256D` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  CONSTRAINT `FK_89CD6320727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `groups` (`id`),
  CONSTRAINT `FK_89CD6320ED442CF4` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `grouprequests` WRITE;
/*!40000 ALTER TABLE `grouprequests` DISABLE KEYS */;
/*!40000 ALTER TABLE `grouprequests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` varchar(255) NOT NULL,
  `parent_id` varchar(255) DEFAULT NULL,
  `image_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_F06D39703DA5256D` (`image_id`),
  UNIQUE KEY `group_parent_name_unique` (`parent_id`,`name`),
  KEY `IDX_F06D3970727ACA70` (`parent_id`),
  KEY `group_name_idx` (`name`),
  CONSTRAINT `FK_F06D39703DA5256D` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  CONSTRAINT `FK_F06D3970727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `imageMime` varchar(32) NOT NULL,
  `imageData` longtext NOT NULL COMMENT '(DC2Type:object)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logins` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(64) NOT NULL,
  `pwResetKey` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_613D7A4A76ED395` (`user_id`),
  CONSTRAINT `FK_613D7A4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `logins` WRITE;
/*!40000 ALTER TABLE `logins` DISABLE KEYS */;
INSERT INTO `logins` VALUES ('9a6af61','14fcb010','2012-09-14 21:32:17','2012-09-14 21:32:17','17f024a9a0fa8ed52784f4dffdd918d9635f742ae7168ddd0f11a3d36040fa4e','be8a6d0e2add5b1f5bb390c8c55e02a313b37518d3c3eabaa85c6aae3d8b62ef',NULL);
/*!40000 ALTER TABLE `logins` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  `id` varchar(255) NOT NULL,
  `camp_id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `start` date NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`),
  KEY `IDX_671798A277075ABB` (`camp_id`),
  CONSTRAINT `FK_671798A277075ABB` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `periods` WRITE;
/*!40000 ALTER TABLE `periods` DISABLE KEYS */;
/*!40000 ALTER TABLE `periods` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugin_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_instances` (
  `id` varchar(255) NOT NULL,
  `event_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `pluginPrototype_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4A3237C071F7E88B` (`event_id`),
  KEY `IDX_4A3237C0A46D7BCB` (`pluginPrototype_id`),
  CONSTRAINT `FK_4A3237C071F7E88B` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_4A3237C0A46D7BCB` FOREIGN KEY (`pluginPrototype_id`) REFERENCES `plugin_prototypes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugin_instances` WRITE;
/*!40000 ALTER TABLE `plugin_instances` DISABLE KEYS */;
INSERT INTO `plugin_instances` VALUES ('341c98aa','453ac999','2012-11-20 19:39:38','2012-11-20 19:39:38','1'),('481f8ff5','453ac999','2012-11-20 19:39:38','2012-11-20 19:39:38','2'),('f5e15ef','453ac999','2012-11-20 19:39:38','2012-11-20 19:39:38','1');
/*!40000 ALTER TABLE `plugin_instances` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugin_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_positions` (
  `id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `container` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  `eventTemplate_id` varchar(255) NOT NULL,
  `pluginPrototype_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plugin_template_unique` (`pluginPrototype_id`,`eventTemplate_id`),
  KEY `IDX_E68AD2D5B8E3A938` (`eventTemplate_id`),
  KEY `IDX_E68AD2D5A46D7BCB` (`pluginPrototype_id`),
  CONSTRAINT `FK_E68AD2D5A46D7BCB` FOREIGN KEY (`pluginPrototype_id`) REFERENCES `plugin_prototypes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `plugin_positions_ibfk_1` FOREIGN KEY (`eventTemplate_id`) REFERENCES `event_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugin_positions` WRITE;
/*!40000 ALTER TABLE `plugin_positions` DISABLE KEYS */;
INSERT INTO `plugin_positions` VALUES ('1','0000-00-00 00:00:00','0000-00-00 00:00:00','left',0,'1','1'),('2','0000-00-00 00:00:00','0000-00-00 00:00:00','right',0,'1','2');
/*!40000 ALTER TABLE `plugin_positions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugin_prototypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_prototypes` (
  `id` varchar(255) NOT NULL,
  `plugin_id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `maxInstances` smallint(6) DEFAULT NULL,
  `defaultInstances` smallint(6) NOT NULL,
  `minInstances` smallint(6) NOT NULL,
  `config` longtext NOT NULL,
  `eventPrototype_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_13F08D4BEC942BCF` (`plugin_id`),
  KEY `IDX_13F08D4BC89C3AF1` (`eventPrototype_id`),
  CONSTRAINT `FK_13F08D4BC89C3AF1` FOREIGN KEY (`eventPrototype_id`) REFERENCES `event_prototypes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_13F08D4BEC942BCF` FOREIGN KEY (`plugin_id`) REFERENCES `plugins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugin_prototypes` WRITE;
/*!40000 ALTER TABLE `plugin_prototypes` DISABLE KEYS */;
INSERT INTO `plugin_prototypes` VALUES ('1','6afbd7b8','0000-00-00 00:00:00','0000-00-00 00:00:00',1,NULL,2,0,'','1'),('2','ed42308','0000-00-00 00:00:00','0000-00-00 00:00:00',1,1,1,1,'','1');
/*!40000 ALTER TABLE `plugin_prototypes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugindata_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugindata_content` (
  `id` varchar(255) NOT NULL,
  `plugin_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `text` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_30B461CEC942BCF` (`plugin_id`),
  CONSTRAINT `plugindata_content_ibfk_1` FOREIGN KEY (`plugin_id`) REFERENCES `plugin_instances` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugindata_content` WRITE;
/*!40000 ALTER TABLE `plugindata_content` DISABLE KEYS */;
INSERT INTO `plugindata_content` VALUES ('5ed1cfac','f5e15ef','2012-11-20 19:39:38','2012-11-20 19:39:38','hello world'),('e355a7f','341c98aa','2012-11-20 19:39:38','2012-11-20 19:39:38','hello world');
/*!40000 ALTER TABLE `plugindata_content` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugindata_header`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugindata_header` (
  `id` varchar(255) NOT NULL,
  `plugin_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `text` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C2FD67DBEC942BCF` (`plugin_id`),
  CONSTRAINT `plugindata_header_ibfk_1` FOREIGN KEY (`plugin_id`) REFERENCES `plugin_instances` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugindata_header` WRITE;
/*!40000 ALTER TABLE `plugindata_header` DISABLE KEYS */;
INSERT INTO `plugindata_header` VALUES ('315dbede','481f8ff5','2012-11-20 19:39:38','2012-11-20 19:39:38','hello world i am a header');
/*!40000 ALTER TABLE `plugindata_header` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `plugins` WRITE;
/*!40000 ALTER TABLE `plugins` DISABLE KEYS */;
INSERT INTO `plugins` VALUES ('6afbd7b8','2012-09-14 22:24:47','2012-09-14 22:24:47','Content'),('ed42308','2012-09-14 22:24:47','2012-09-14 22:24:47','Header');
/*!40000 ALTER TABLE `plugins` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `uid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uid` (
  `id` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `uid` WRITE;
/*!40000 ALTER TABLE `uid` DISABLE KEYS */;
INSERT INTO `uid` VALUES ('14fcb010','CoreApi\\Entity\\User'),('315dbede','Plugin\\Header\\Entity\\Header'),('341c98aa','CoreApi\\Entity\\PluginInstance'),('3831a289','Plugin\\Content\\Entity\\Content'),('453ac999','CoreApi\\Entity\\Event'),('481f8ff5','CoreApi\\Entity\\PluginInstance'),('5ed1cfac','Plugin\\Content\\Entity\\Content'),('6afbd7b8','CoreApi\\Entity\\Plugin'),('7771aa10','CoreApi\\Entity\\Event'),('9a6af61','CoreApi\\Entity\\Login'),('c1c9cc','Plugin\\Header\\Entity\\Header'),('e355a7f','Plugin\\Content\\Entity\\Content'),('ed42308','CoreApi\\Entity\\Plugin'),('f5e15ef','CoreApi\\Entity\\PluginInstance');
/*!40000 ALTER TABLE `uid` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_camps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_camps` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `camp_id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `role` int(11) NOT NULL,
  `requestedRole` int(11) DEFAULT NULL,
  `invitationAccepted` tinyint(1) NOT NULL,
  `requestAcceptedBy_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_camp_unique` (`user_id`,`camp_id`),
  KEY `IDX_DFD490BDA76ED395` (`user_id`),
  KEY `IDX_DFD490BD77075ABB` (`camp_id`),
  KEY `IDX_DFD490BDB07C3E84` (`requestAcceptedBy_id`),
  CONSTRAINT `FK_DFD490BD77075ABB` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`),
  CONSTRAINT `FK_DFD490BDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_DFD490BDB07C3E84` FOREIGN KEY (`requestAcceptedBy_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_camps` WRITE;
/*!40000 ALTER TABLE `user_camps` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_camps` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `group_id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `role` int(11) NOT NULL,
  `requestedRole` int(11) DEFAULT NULL,
  `invitationAccepted` tinyint(1) NOT NULL,
  `requestAcceptedBy_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_group_unique` (`user_id`,`group_id`),
  KEY `IDX_953F224DA76ED395` (`user_id`),
  KEY `IDX_953F224DFE54D947` (`group_id`),
  KEY `IDX_953F224DB07C3E84` (`requestAcceptedBy_id`),
  CONSTRAINT `FK_953F224DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_953F224DB07C3E84` FOREIGN KEY (`requestAcceptedBy_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_953F224DFE54D947` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_relationships` (
  `id` varchar(255) NOT NULL,
  `from_id` varchar(255) NOT NULL,
  `to_id` varchar(255) NOT NULL,
  `counterpart` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_to_unique` (`from_id`,`to_id`),
  UNIQUE KEY `UNIQ_2376C5F7564F5C9F` (`counterpart`),
  KEY `IDX_2376C5F778CED90B` (`from_id`),
  KEY `IDX_2376C5F730354A65` (`to_id`),
  CONSTRAINT `FK_2376C5F730354A65` FOREIGN KEY (`to_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_2376C5F7564F5C9F` FOREIGN KEY (`counterpart`) REFERENCES `user_relationships` (`id`),
  CONSTRAINT `FK_2376C5F778CED90B` FOREIGN KEY (`from_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_relationships` WRITE;
/*!40000 ALTER TABLE `user_relationships` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_relationships` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` varchar(255) NOT NULL,
  `image_id` varchar(255) DEFAULT NULL,
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
  `state` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`),
  UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
  UNIQUE KEY `UNIQ_1483A5E93DA5256D` (`image_id`),
  CONSTRAINT `FK_1483A5E93DA5256D` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('14fcb010',NULL,'2012-09-14 21:32:17','2012-09-14 21:32:20','tester','test@test.com',NULL,'test','test','test',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activated','User');
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

