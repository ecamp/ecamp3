
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
DROP TABLE IF EXISTS `days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `days` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `period_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `dayOffset` int(11) NOT NULL,
  `notes` longtext COLLATE utf8_unicode_ci NOT NULL,
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
  `minOffset` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FCB23E5471F7E88B` (`event_id`),
  KEY `IDX_FCB23E54EC8B7ADE` (`period_id`),
  CONSTRAINT `FK_FCB23E5471F7E88B` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  CONSTRAINT `FK_FCB23E54EC8B7ADE` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `event_instances` WRITE;
/*!40000 ALTER TABLE `event_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_instances` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `camp_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `title` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5387574A77075ABB` (`camp_id`),
  KEY `IDX_5387574AA76ED395` (`user_id`),
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
  CONSTRAINT `FK_89CD63203DA5256D` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`),
  CONSTRAINT `FK_89CD6320727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `groups` (`id`),
  CONSTRAINT `FK_89CD6320ED442CF4` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`)
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
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `method` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `params` longtext COLLATE utf8_unicode_ci,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `exec_time` datetime DEFAULT NULL,
  `error_message` longtext COLLATE utf8_unicode_ci,
  `context_me` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `context_user` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `context_camp` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `context_group` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES ('c997b962','2012-07-08 20:23:22','2012-07-09 20:43:04','CoreApi\\Service\\RegisterService','SendActivationCode','a:2:{i:0;O:24:\"CoreApi\\Entity\\EntityRef\":2:{s:5:\"class\";s:19:\"CoreApi\\Entity\\User\";s:2:\"id\";s:8:\"f99090e7\";}i:1;s:64:\"f292371b795008075ad05fca8fead3e8240937f41a6f37c3e249ceed32224d38\";}','SUCCEEDED','2012-07-09 20:43:04','',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
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
INSERT INTO `logins` VALUES ('2db183b','f3c4b878','2012-07-04 00:11:33','2012-07-04 00:11:33','f1b8d4dc22265b8cb967540115aecc12a1c32a4382af648d4a69642f8c79efe1','81643c180615e2b8e4da5df2606a97e87e4f9881c2a3d9ea1be97c6ce5fba558',NULL),('6d883c74','f99090e7','2012-07-08 20:23:22','2012-07-08 20:23:22','d5af4082f19cdc7ac1b5c70956b52f6216ffe7d1cd0e9941df23b9cfcb5c5bbb','b81e9785a4fa72354afcfe90fae32b4477363c11ca6f939f9d47dcceefe3fd76',NULL),('b640c6c4','8f368eb2','2012-07-03 23:42:17','2012-07-03 23:42:17','0ee5574d9065c8bf7be876bf84f56c21bbbce0dc6339906c229f011f686987d1','82503a89d7beba4b77c7ed8438c84560a4f65eb05cfc1f31f36f0842130d2c01',NULL),('cc35c0e8','f121e841','2012-07-04 00:05:06','2012-07-04 00:05:06','e70ae60578c3b2e69d96c6632f4ad2ff9f047c9d041abe5f62bef31c8ff8334c','3bcc6c60ac0763fa84770c5f878f40eb5fec68ad30ce56a0556ea078a87ae0e7',NULL),('d98ca5cc','b625ad70','2012-07-04 00:06:57','2012-07-04 00:06:57','605a32b55b54e03dfeca1ffb015def75f3951d0f0d7aba7bed1e065631337ec6','57bf5c73393b58efbc03af1993c85252dce7fa2c35788fcd9a13e1806a79f3cd',NULL);
/*!40000 ALTER TABLE `logins` ENABLE KEYS */;
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
  `duration` int(11) NOT NULL,
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
DROP TABLE IF EXISTS `plugins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `event_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `pluginName` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EC85F67171F7E88B` (`event_id`),
  CONSTRAINT `FK_EC85F67171F7E88B` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
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
INSERT INTO `uid` VALUES ('2db183b','CoreApi\\Entity\\Login'),('63423cac','CoreApi\\Entity\\Job'),('6d883c74','CoreApi\\Entity\\Login'),('7b1a8034','CoreApi\\Entity\\Job'),('7e9ef9ab','CoreApi\\Entity\\Job'),('8f368eb2','CoreApi\\Entity\\User'),('abbe5dd0','CoreApi\\Entity\\Job'),('b625ad70','CoreApi\\Entity\\User'),('b640c6c4','CoreApi\\Entity\\Login'),('c997b962','CoreApi\\Entity\\Job'),('cc35c0e8','CoreApi\\Entity\\Login'),('d98ca5cc','CoreApi\\Entity\\Login'),('f121e841','CoreApi\\Entity\\User'),('f3c4b878','CoreApi\\Entity\\User'),('f99090e7','CoreApi\\Entity\\User');
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
  CONSTRAINT `FK_DFD490BD77075ABB` FOREIGN KEY (`camp_id`) REFERENCES `camps` (`id`),
  CONSTRAINT `FK_DFD490BDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_DFD490BDB07C3E84` FOREIGN KEY (`requestAcceptedBy_id`) REFERENCES `users` (`id`)
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
  CONSTRAINT `FK_953F224DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_953F224DB07C3E84` FOREIGN KEY (`requestAcceptedBy_id`) REFERENCES `users` (`id`),
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
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_to_unique` (`from_id`,`to_id`),
  KEY `IDX_2376C5F778CED90B` (`from_id`),
  KEY `IDX_2376C5F730354A65` (`to_id`),
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
INSERT INTO `users` VALUES ('8f368eb2',NULL,'2012-07-03 23:42:16','2012-07-03 23:44:15','liseur','linlsiej@lnf.ch',NULL,'lisher','lihcpmo','liho3e8h',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Activated','User'),('b625ad70',NULL,'2012-07-04 00:06:57','2012-07-04 00:06:57','osihler','lsisli@lishr.ch','58d098b001c827409467ae2d892ad5563275e1fda99852b231bb1bff818534d2','asdi4h','lisdjuh','laherl',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Registered','User'),('f121e841',NULL,'2012-07-04 00:05:06','2012-07-04 00:05:06','uli3aser','lisj@okueh.ch','05856b7005b8df53ab86b557507e9418a2f72648683b9ee9941acd370984ec2b','iebfil','pihejr','pihw',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Registered','User'),('f3c4b878',NULL,'2012-07-04 00:11:33','2012-07-04 00:11:33','asdrlij','sidjr@lsidhf.ch','85f071f235c3c90d5ae36a89aed816ac260fa0b377fc5ed64c9603669e8b95fd','lsihdr','lsidhn','oishjpo',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Registered','User'),('f99090e7',NULL,'2012-07-08 20:23:22','2012-07-08 20:23:22','soienrl','lsuer@luhser.ch','f292371b795008075ad05fca8fead3e8240937f41a6f37c3e249ceed32224d38','as;idrj',';ojer','kluaehir',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Registered','User');
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

