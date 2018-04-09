-- MySQL dump 10.13  Distrib 5.7.21, for Linux (x86_64)
--
-- Host: localhost    Database: hexaa
-- ------------------------------------------------------
-- Server version	5.7.21

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
-- Table structure for table `attribute_spec`
--

DROP TABLE IF EXISTS `attribute_spec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute_spec` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `maintainer` enum('user','manager','admin') COLLATE utf8_unicode_ci DEFAULT NULL,
  `syntax` enum('string','base64') COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_multivalue` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attribute_spec`
--

LOCK TABLES `attribute_spec` WRITE;
/*!40000 ALTER TABLE `attribute_spec` DISABLE KEYS */;
INSERT INTO `attribute_spec` (`id`, `uri`, `name`, `description`, `maintainer`, `syntax`, `is_multivalue`, `created_at`, `updated_at`) VALUES (1,'test:attribute:footsize','Foot size','Foot size in Hungarian foot size unit','user','string',NULL,'2018-03-29 07:14:04','2018-03-29 07:14:04'),(2,'test:attribute:rsakey','RSA key','RSA public key of a user','user','string',1,'2018-03-29 07:14:05','2018-03-29 07:14:05'),(3,'test:attribute:confRoomNumber','Room # of the VO',NULL,'manager','string',NULL,'2018-03-29 07:14:05','2018-03-29 07:14:05'),(4,'test:attribute:coffeeFlavour','Favourite coffee of the principal',NULL,'user','string',NULL,'2018-03-29 07:14:05','2018-03-29 07:14:05');
/*!40000 ALTER TABLE `attribute_spec` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attribute_value_organization`
--

DROP TABLE IF EXISTS `attribute_value_organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute_value_organization` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `organization_id` bigint(20) DEFAULT NULL,
  `attribute_spec_id` bigint(20) DEFAULT NULL,
  `value` longblob,
  `is_default` tinyint(1) DEFAULT NULL,
  `loa` bigint(20) DEFAULT NULL,
  `loa_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `organization_id_idx` (`organization_id`),
  KEY `attribute_spec_id_idx` (`attribute_spec_id`),
  CONSTRAINT `FK_15CA3D602113FD3F` FOREIGN KEY (`attribute_spec_id`) REFERENCES `attribute_spec` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_15CA3D6032C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attribute_value_organization`
--

LOCK TABLES `attribute_value_organization` WRITE;
/*!40000 ALTER TABLE `attribute_value_organization` DISABLE KEYS */;
INSERT INTO `attribute_value_organization` (`id`, `organization_id`, `attribute_spec_id`, `value`, `is_default`, `loa`, `loa_date`, `created_at`, `updated_at`) VALUES (1,1,3,'L206',NULL,0,'2018-03-29 07:14:06','2018-03-29 07:14:06','2018-03-29 07:14:06'),(2,2,3,'L207',NULL,0,'2018-03-29 07:14:06','2018-03-29 07:14:06','2018-03-29 07:14:06');
/*!40000 ALTER TABLE `attribute_value_organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attribute_value_principal`
--

DROP TABLE IF EXISTS `attribute_value_principal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attribute_value_principal` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `principal_id` bigint(20) DEFAULT NULL,
  `attribute_spec_id` bigint(20) DEFAULT NULL,
  `value` longblob,
  `loa` bigint(20) DEFAULT NULL,
  `loa_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `principal_id_idx` (`principal_id`),
  KEY `attribute_spec_id_idx` (`attribute_spec_id`),
  CONSTRAINT `FK_81013BFF2113FD3F` FOREIGN KEY (`attribute_spec_id`) REFERENCES `attribute_spec` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_81013BFF474870EE` FOREIGN KEY (`principal_id`) REFERENCES `principal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attribute_value_principal`
--

LOCK TABLES `attribute_value_principal` WRITE;
/*!40000 ALTER TABLE `attribute_value_principal` DISABLE KEYS */;
INSERT INTO `attribute_value_principal` (`id`, `principal_id`, `attribute_spec_id`, `value`, `loa`, `loa_date`, `created_at`, `updated_at`) VALUES (1,1,1,'44',0,'2018-03-29 07:14:05','2018-03-29 07:14:05','2018-03-29 07:14:05'),(2,1,2,'rsa pubkey goes here',0,'2018-03-29 07:14:06','2018-03-29 07:14:06','2018-03-29 07:14:06'),(3,1,2,'wow, here is another rsa pubkey',0,'2018-03-29 07:14:06','2018-03-29 07:14:06','2018-03-29 07:14:06');
/*!40000 ALTER TABLE `attribute_value_principal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entitlement`
--

DROP TABLE IF EXISTS `entitlement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitlement` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  UNIQUE KEY `name_service` (`name`,`service_id`),
  KEY `service_id_idx` (`service_id`),
  CONSTRAINT `FK_FA448021ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entitlement`
--

LOCK TABLES `entitlement` WRITE;
/*!40000 ALTER TABLE `entitlement` DISABLE KEYS */;
INSERT INTO `entitlement` (`id`, `service_id`, `name`, `description`, `uri`, `created_at`, `updated_at`) VALUES (1,1,'Permission 1','Permission 1 of this awesome test service!','some:entitlement:prefix:hexaa:1:perm1','2018-03-29 07:14:08','2018-03-29 07:14:08'),(2,1,'Permission 2',NULL,'some:entitlement:prefix:hexaa:1:perm2','2018-03-29 07:14:08','2018-03-29 07:14:08'),(3,1,'Permission 3',NULL,'some:entitlement:prefix:hexaa:1:perm3','2018-03-29 07:14:08','2018-03-29 07:14:08'),(4,2,'Permission 4','Permission 4 of this disabled test service!','some:entitlement:prefix:hexaa:2:perm4','2018-03-29 07:14:08','2018-03-29 07:14:08');
/*!40000 ALTER TABLE `entitlement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entitlement_pack`
--

DROP TABLE IF EXISTS `entitlement_pack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitlement_pack` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `type` enum('private','public') COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_service` (`name`,`service_id`),
  KEY `service_id_idx` (`service_id`),
  CONSTRAINT `FK_42C3B29CED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entitlement_pack`
--

LOCK TABLES `entitlement_pack` WRITE;
/*!40000 ALTER TABLE `entitlement_pack` DISABLE KEYS */;
INSERT INTO `entitlement_pack` (`id`, `service_id`, `name`, `description`, `type`, `created_at`, `updated_at`) VALUES (1,1,'Entitlement Package 1','this is a short desc.','private','2018-03-29 07:14:09','2018-03-29 07:14:09'),(2,1,'Entitlement Package 2',NULL,'public','2018-03-29 07:14:09','2018-03-29 07:14:10'),(3,2,'Entitlement Package 3','this is a short desc.','private','2018-03-29 07:14:09','2018-03-29 07:14:10');
/*!40000 ALTER TABLE `entitlement_pack` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entitlement_pack_entitlement`
--

DROP TABLE IF EXISTS `entitlement_pack_entitlement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entitlement_pack_entitlement` (
  `entitlementpack_id` bigint(20) NOT NULL,
  `entitlement_id` bigint(20) NOT NULL,
  PRIMARY KEY (`entitlementpack_id`,`entitlement_id`),
  KEY `IDX_A927A4232ACB9CAD` (`entitlementpack_id`),
  KEY `IDX_A927A42315FCF4DF` (`entitlement_id`),
  CONSTRAINT `FK_A927A42315FCF4DF` FOREIGN KEY (`entitlement_id`) REFERENCES `entitlement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_A927A4232ACB9CAD` FOREIGN KEY (`entitlementpack_id`) REFERENCES `entitlement_pack` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entitlement_pack_entitlement`
--

LOCK TABLES `entitlement_pack_entitlement` WRITE;
/*!40000 ALTER TABLE `entitlement_pack_entitlement` DISABLE KEYS */;
INSERT INTO `entitlement_pack_entitlement` (`entitlementpack_id`, `entitlement_id`) VALUES (1,1),(1,2),(1,3),(2,1),(3,4);
/*!40000 ALTER TABLE `entitlement_pack_entitlement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hook`
--

DROP TABLE IF EXISTS `hook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) DEFAULT NULL,
  `organization_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('user_added','user_removed','attribute_change') COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `lastCallMessage` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_service` (`name`,`service_id`),
  UNIQUE KEY `name_organization` (`name`,`organization_id`),
  KEY `IDX_A4584355ED5CA9E6` (`service_id`),
  KEY `IDX_A458435532C8A3DE` (`organization_id`),
  CONSTRAINT `FK_A458435532C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_A4584355ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hook`
--

LOCK TABLES `hook` WRITE;
/*!40000 ALTER TABLE `hook` DISABLE KEYS */;
/*!40000 ALTER TABLE `hook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invitation`
--

DROP TABLE IF EXISTS `invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invitation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) DEFAULT NULL,
  `organization_id` bigint(20) DEFAULT NULL,
  `service_id` bigint(20) DEFAULT NULL,
  `inviter_id` bigint(20) DEFAULT NULL,
  `emails` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `statuses` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `display_names` mediumtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `landing_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `do_redirect` tinyint(1) DEFAULT NULL,
  `as_manager` tinyint(1) DEFAULT NULL,
  `message` longtext COLLATE utf8_unicode_ci,
  `counter` bigint(20) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `principal_limit` bigint(20) DEFAULT NULL,
  `reinvite_count` bigint(20) DEFAULT NULL,
  `last_reinvite_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F11D61A2D60322AC` (`role_id`),
  KEY `inviter_id_idx` (`inviter_id`),
  KEY `organization_id_idx` (`organization_id`),
  KEY `service_id_idx` (`service_id`),
  CONSTRAINT `FK_F11D61A232C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_F11D61A2B79F4F04` FOREIGN KEY (`inviter_id`) REFERENCES `principal` (`id`),
  CONSTRAINT `FK_F11D61A2D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_F11D61A2ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invitation`
--

LOCK TABLES `invitation` WRITE;
/*!40000 ALTER TABLE `invitation` DISABLE KEYS */;
/*!40000 ALTER TABLE `invitation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `link`
--

DROP TABLE IF EXISTS `link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `organization_id` bigint(20) DEFAULT NULL,
  `service_id` bigint(20) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updatedAt` datetime NOT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organization_service` (`organization_id`,`service_id`),
  KEY `organization_id_idx` (`organization_id`),
  KEY `service_id_idx` (`service_id`),
  CONSTRAINT `FK_36AC99F132C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_36AC99F1ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `link`
--

LOCK TABLES `link` WRITE;
/*!40000 ALTER TABLE `link` DISABLE KEYS */;
INSERT INTO `link` (`id`, `organization_id`, `service_id`, `status`, `updatedAt`, `createdAt`) VALUES (1,1,1,'accepted','2018-03-29 07:14:10','2018-03-29 07:14:10'),(2,2,1,'accepted','2018-03-29 07:14:11','2018-03-29 07:14:10'),(3,3,1,'pending','2018-03-29 07:14:11','2018-03-29 07:14:11');
/*!40000 ALTER TABLE `link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `link_entitlement`
--

DROP TABLE IF EXISTS `link_entitlement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `link_entitlement` (
  `link_id` int(11) NOT NULL,
  `entitlement_id` bigint(20) NOT NULL,
  PRIMARY KEY (`link_id`,`entitlement_id`),
  KEY `IDX_37C40C72ADA40271` (`link_id`),
  KEY `IDX_37C40C7215FCF4DF` (`entitlement_id`),
  CONSTRAINT `FK_37C40C7215FCF4DF` FOREIGN KEY (`entitlement_id`) REFERENCES `entitlement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_37C40C72ADA40271` FOREIGN KEY (`link_id`) REFERENCES `link` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `link_entitlement`
--

LOCK TABLES `link_entitlement` WRITE;
/*!40000 ALTER TABLE `link_entitlement` DISABLE KEYS */;
/*!40000 ALTER TABLE `link_entitlement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `link_entitlement_pack`
--

DROP TABLE IF EXISTS `link_entitlement_pack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `link_entitlement_pack` (
  `link_id` int(11) NOT NULL,
  `entitlement_pack_id` bigint(20) NOT NULL,
  PRIMARY KEY (`link_id`,`entitlement_pack_id`),
  KEY `IDX_59EBC87ADA40271` (`link_id`),
  KEY `IDX_59EBC8746D6DBD6` (`entitlement_pack_id`),
  CONSTRAINT `FK_59EBC8746D6DBD6` FOREIGN KEY (`entitlement_pack_id`) REFERENCES `entitlement_pack` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_59EBC87ADA40271` FOREIGN KEY (`link_id`) REFERENCES `link` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `link_entitlement_pack`
--

LOCK TABLES `link_entitlement_pack` WRITE;
/*!40000 ALTER TABLE `link_entitlement_pack` DISABLE KEYS */;
INSERT INTO `link_entitlement_pack` (`link_id`, `entitlement_pack_id`) VALUES (1,1),(2,1),(3,1);
/*!40000 ALTER TABLE `link_entitlement_pack` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `linker_token`
--

DROP TABLE IF EXISTS `linker_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `linker_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expiresAt` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `IDX_EB3DE738ADA40271` (`link_id`),
  CONSTRAINT `FK_EB3DE738ADA40271` FOREIGN KEY (`link_id`) REFERENCES `link` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `linker_token`
--

LOCK TABLES `linker_token` WRITE;
/*!40000 ALTER TABLE `linker_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `linker_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `principal_id` bigint(20) DEFAULT NULL,
  `service_id` bigint(20) DEFAULT NULL,
  `organization_id` bigint(20) DEFAULT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `admin` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `principal_idx` (`principal_id`),
  KEY `tag_idx` (`tag`),
  KEY `service_id_idx` (`service_id`),
  KEY `organization_id_idx` (`organization_id`),
  CONSTRAINT `FK_1DD3995032C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1DD39950474870EE` FOREIGN KEY (`principal_id`) REFERENCES `principal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1DD39950ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` (`id`, `principal_id`, `service_id`, `organization_id`, `tag`, `title`, `message`, `created_at`, `updated_at`, `admin`) VALUES (1,1,1,NULL,'service','New Service created','A new service named testService1 has been created','2018-03-29 07:14:01','2018-03-29 07:14:01',0),(2,1,2,NULL,'service','New Service created','A new service named testService2 has been created','2018-03-29 07:14:01','2018-03-29 07:14:01',0),(3,1,NULL,1,'organization','New Organization created','employee@project.local has created a new organization named testOrg1','2018-03-29 07:14:01','2018-03-29 07:14:01',0),(4,1,NULL,2,'organization','New Organization created','employee@project.local has created a new organization named testOrg2','2018-03-29 07:14:02','2018-03-29 07:14:02',0),(5,1,NULL,3,'organization','New Organization created','employee@project.local has created a new organization named testOrg3','2018-03-29 07:14:02','2018-03-29 07:14:02',0),(6,6,NULL,1,'organization_manager','Organization management changed','employee@server.hexaa.eu is now a manager of organization testOrg1','2018-03-29 07:14:03','2018-03-29 07:14:03',0),(7,6,NULL,2,'organization_manager','Organization management changed','employee@server.hexaa.eu is now a manager of organization testOrg2','2018-03-29 07:14:03','2018-03-29 07:14:03',0),(8,6,NULL,3,'organization_manager','Organization management changed','employee@server.hexaa.eu is now a manager of organization testOrg3','2018-03-29 07:14:03','2018-03-29 07:14:03',0),(9,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg1','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(10,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg1','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(11,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg1','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(12,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg1','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(13,1,NULL,1,'organization_manager','Organization management changed','testOrg1: New members added: student@project.local, student@project.nolocal, employee@project.nolocal, student@server.hexaa.eu, no members removed. ','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(14,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg2','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(15,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg2','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(16,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg2','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(17,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg2','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(18,1,NULL,2,'organization_manager','Organization management changed','testOrg2: New members added: student@project.local, student@project.nolocal, employee@project.nolocal, student@server.hexaa.eu, no members removed. ','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(19,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg3','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(20,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg3','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(21,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg3','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(22,1,NULL,NULL,'organization_member','Organization membership changed','You are now a member of organizationtestOrg3','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(23,1,NULL,3,'organization_manager','Organization management changed','testOrg3: New members added: student@project.local, student@project.nolocal, employee@project.nolocal, student@server.hexaa.eu, no members removed. ','2018-03-29 07:14:04','2018-03-29 07:14:04',0),(24,1,1,NULL,'service','Connected attributes changed','employee@project.localhas modified the attributes of Service testService1: New attributes requested: Foot size, RSA key, Room # of the VO, no attributes removed. ','2018-03-29 07:14:05','2018-03-29 07:14:05',0),(25,NULL,1,NULL,'service_manager','EntitlementPack entitlements changed','testService1::Entitlement Package 1: New entitlements added: Permission 1, Permission 2, Permission 3, no entitlements removed. ','2018-03-29 07:14:09','2018-03-29 07:14:09',0),(26,NULL,1,NULL,'service_manager','EntitlementPack entitlements changed','testService1::Entitlement Package 2: New entitlements added: Permission 1, no entitlements removed. ','2018-03-29 07:14:10','2018-03-29 07:14:10',0),(27,NULL,2,NULL,'service_manager','EntitlementPack entitlements changed','testService2::Entitlement Package 3: New entitlements added: Permission 4, no entitlements removed. ','2018-03-29 07:14:10','2018-03-29 07:14:10',0),(28,NULL,1,1,'organization_entitlement_pack','Entitlement package connected','An entitlement pack Entitlement Package 1 has been connected to organization testOrg1','2018-03-29 07:14:10','2018-03-29 07:14:10',0),(29,NULL,1,2,'organization_entitlement_pack','Entitlement package connected','An entitlement pack Entitlement Package 1 has been connected to organization testOrg2','2018-03-29 07:14:11','2018-03-29 07:14:11',0),(30,NULL,1,3,'organization_service','Organization linked to service','employee@project.local has created a link between service testService1 and organization testOrg3','2018-03-29 07:14:11','2018-03-29 07:14:11',0);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization`
--

DROP TABLE IF EXISTS `organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `default_role_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `isolate_members` tinyint(1) DEFAULT NULL,
  `isolate_role_members` tinyint(1) DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `UNIQ_C1EE637C248673E9` (`default_role_id`),
  CONSTRAINT `FK_C1EE637C248673E9` FOREIGN KEY (`default_role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization`
--

LOCK TABLES `organization` WRITE;
/*!40000 ALTER TABLE `organization` DISABLE KEYS */;
INSERT INTO `organization` (`id`, `default_role_id`, `name`, `isolate_members`, `isolate_role_members`, `url`, `description`, `created_at`, `updated_at`) VALUES (1,NULL,'testOrg1',0,0,NULL,'Ez a szervezet teszteléshez készült. Jól tesztelve is lesz vele az alkalmazás.','2018-03-29 07:14:01','2018-03-29 07:14:04'),(2,NULL,'testOrg2',0,0,NULL,'Lórum ipse nem jó és nem csapzott. Itt a zsonna jadzaga az, hogy egy vallámos zátlan pendő bités a mozásának üzékét előidézve hirtelen igen csomott áhságot pedzhetik vagy a szőkedésben videk fodráz vétlő, lilés hirtelen tovább növelve a szőkedést küblent parníthatja elő. A szükségbe fejős baság esetén a szőkedés szakás nadéka esetén nem tud “kifelé” csavúrnia, mivel a szotykodos göbölyű ezt jáncázja. Az egyik a restív, amikor a híző bridás ságosnál tinos folyákat pattogat. Itt a szellőnek az az amara, hogy a várhatóan lassan tovább földi gyornotos turpadást zöntömörje. Ha ugyanis tovább kold a baság, olyan pans ólkodik létre, amely már egy zatott vozatnál jobban nem tudja fusnia a kliumot, és cúgos módon parníthat elő hítást.','2018-03-29 07:14:02','2018-03-29 07:14:04'),(3,NULL,'testOrg3',0,0,NULL,'Ebben a szervezetben nincs benne az adminunk, csak pár másik emberke.','2018-03-29 07:14:02','2018-03-29 07:14:04');
/*!40000 ALTER TABLE `organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_manager`
--

DROP TABLE IF EXISTS `organization_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization_manager` (
  `organization_id` bigint(20) NOT NULL,
  `principal_id` bigint(20) NOT NULL,
  PRIMARY KEY (`organization_id`,`principal_id`),
  KEY `IDX_37F6ADFA32C8A3DE` (`organization_id`),
  KEY `IDX_37F6ADFA474870EE` (`principal_id`),
  CONSTRAINT `FK_37F6ADFA32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_37F6ADFA474870EE` FOREIGN KEY (`principal_id`) REFERENCES `principal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_manager`
--

LOCK TABLES `organization_manager` WRITE;
/*!40000 ALTER TABLE `organization_manager` DISABLE KEYS */;
INSERT INTO `organization_manager` (`organization_id`, `principal_id`) VALUES (1,1),(1,6),(2,1),(2,6),(3,1),(3,6);
/*!40000 ALTER TABLE `organization_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_principal`
--

DROP TABLE IF EXISTS `organization_principal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization_principal` (
  `organization_id` bigint(20) NOT NULL,
  `principal_id` bigint(20) NOT NULL,
  PRIMARY KEY (`organization_id`,`principal_id`),
  KEY `IDX_1897565D32C8A3DE` (`organization_id`),
  KEY `IDX_1897565D474870EE` (`principal_id`),
  CONSTRAINT `FK_1897565D32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1897565D474870EE` FOREIGN KEY (`principal_id`) REFERENCES `principal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_principal`
--

LOCK TABLES `organization_principal` WRITE;
/*!40000 ALTER TABLE `organization_principal` DISABLE KEYS */;
INSERT INTO `organization_principal` (`organization_id`, `principal_id`) VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(3,1),(3,2),(3,3),(3,4),(3,5),(3,6);
/*!40000 ALTER TABLE `organization_principal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_security_domain`
--

DROP TABLE IF EXISTS `organization_security_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization_security_domain` (
  `organization_id` bigint(20) NOT NULL,
  `securitydomain_id` int(11) NOT NULL,
  PRIMARY KEY (`organization_id`,`securitydomain_id`),
  KEY `IDX_C3DE18CB32C8A3DE` (`organization_id`),
  KEY `IDX_C3DE18CB5CF4CC57` (`securitydomain_id`),
  CONSTRAINT `FK_C3DE18CB32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C3DE18CB5CF4CC57` FOREIGN KEY (`securitydomain_id`) REFERENCES `security_domain` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_security_domain`
--

LOCK TABLES `organization_security_domain` WRITE;
/*!40000 ALTER TABLE `organization_security_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `organization_security_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organization_tag`
--

DROP TABLE IF EXISTS `organization_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organization_tag` (
  `organization_id` bigint(20) NOT NULL,
  `tag_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`organization_id`,`tag_id`),
  KEY `IDX_904E86032C8A3DE` (`organization_id`),
  KEY `IDX_904E860BAD26311` (`tag_id`),
  CONSTRAINT `FK_904E86032C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_904E860BAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`name`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organization_tag`
--

LOCK TABLES `organization_tag` WRITE;
/*!40000 ALTER TABLE `organization_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `organization_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_token`
--

DROP TABLE IF EXISTS `personal_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `masterkey_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token_expire` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_token`
--

LOCK TABLES `personal_token` WRITE;
/*!40000 ALTER TABLE `personal_token` DISABLE KEYS */;
INSERT INTO `personal_token` (`id`, `token`, `masterkey_name`, `token_expire`, `created_at`, `updated_at`) VALUES (1,'77735060c8df13c6b7bc2aca4a04dd4660858b48c78e856629fe6172233f629f','defaultMasterKey','2018-03-29 08:14:11','2018-03-29 07:14:01','2018-03-29 07:14:11');
/*!40000 ALTER TABLE `personal_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `principal`
--

DROP TABLE IF EXISTS `principal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `principal` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `token_id` bigint(20) DEFAULT NULL,
  `fedid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fedid` (`fedid`),
  UNIQUE KEY `UNIQ_20A08C5B41DEE7B9` (`token_id`),
  KEY `token_idx` (`token_id`),
  CONSTRAINT `FK_20A08C5B41DEE7B9` FOREIGN KEY (`token_id`) REFERENCES `personal_token` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `principal`
--

LOCK TABLES `principal` WRITE;
/*!40000 ALTER TABLE `principal` DISABLE KEYS */;
INSERT INTO `principal` (`id`, `token_id`, `fedid`, `email`, `display_name`, `created_at`, `updated_at`) VALUES (1,1,'employee@project.local','employee@project.local','Employee Employee','2018-03-29 07:14:01','2018-03-29 07:14:01'),(2,NULL,'student@project.local','student@project.local','Student Student','2018-03-29 07:14:02','2018-03-29 07:14:02'),(3,NULL,'student@project.nolocal','student@project.nolocal','Student Student','2018-03-29 07:14:02','2018-03-29 07:14:02'),(4,NULL,'employee@project.nolocal','employee@project.nolocal','Employee Employee','2018-03-29 07:14:03','2018-03-29 07:14:03'),(5,NULL,'student@server.hexaa.eu','student@server.hexaa.eu','Student Student','2018-03-29 07:14:03','2018-03-29 07:14:03'),(6,NULL,'employee@server.hexaa.eu','employee@server.hexaa.eu','Employee Employee','2018-03-29 07:14:03','2018-03-29 07:14:03');
/*!40000 ALTER TABLE `principal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `organization_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_organization` (`name`,`organization_id`),
  KEY `organization_id_idx` (`organization_id`),
  CONSTRAINT `FK_57698A6A32C8A3DE` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` (`id`, `organization_id`, `name`, `description`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES (1,1,'Test role 1','Teszt szerepkör 1',NULL,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(2,1,'Test role 2',NULL,NULL,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(3,2,'Test role 3, which has a pretty long and tedious name','Lórum ipse nem jó és nem csapzott. Itt a zsonna jadzaga az, hogy egy vallámos zátlan pendő',NULL,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_entitlement`
--

DROP TABLE IF EXISTS `role_entitlement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_entitlement` (
  `role_id` bigint(20) NOT NULL,
  `entitlement_id` bigint(20) NOT NULL,
  PRIMARY KEY (`role_id`,`entitlement_id`),
  KEY `IDX_C813D8A8D60322AC` (`role_id`),
  KEY `IDX_C813D8A815FCF4DF` (`entitlement_id`),
  CONSTRAINT `FK_C813D8A815FCF4DF` FOREIGN KEY (`entitlement_id`) REFERENCES `entitlement` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C813D8A8D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_entitlement`
--

LOCK TABLES `role_entitlement` WRITE;
/*!40000 ALTER TABLE `role_entitlement` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_entitlement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_principal`
--

DROP TABLE IF EXISTS `role_principal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_principal` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) DEFAULT NULL,
  `principal_id` bigint(20) DEFAULT NULL,
  `expiration` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_principal` (`role_id`,`principal_id`),
  KEY `role_id_idx` (`role_id`),
  KEY `principal_id_idx` (`principal_id`),
  CONSTRAINT `FK_71545F87474870EE` FOREIGN KEY (`principal_id`) REFERENCES `principal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_71545F87D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_principal`
--

LOCK TABLES `role_principal` WRITE;
/*!40000 ALTER TABLE `role_principal` DISABLE KEYS */;
INSERT INTO `role_principal` (`id`, `role_id`, `principal_id`, `expiration`, `created_at`, `updated_at`) VALUES (1,1,2,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(2,1,1,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(3,1,3,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(4,1,5,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(5,1,6,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(6,2,3,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(7,2,4,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(8,2,6,NULL,'2018-03-29 07:14:07','2018-03-29 07:14:07'),(9,3,2,NULL,'2018-03-29 07:14:08','2018-03-29 07:14:08'),(10,3,1,NULL,'2018-03-29 07:14:08','2018-03-29 07:14:08'),(11,3,3,NULL,'2018-03-29 07:14:08','2018-03-29 07:14:08'),(12,3,5,NULL,'2018-03-29 07:14:08','2018-03-29 07:14:08'),(13,3,6,NULL,'2018-03-29 07:14:08','2018-03-29 07:14:08');
/*!40000 ALTER TABLE `role_principal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_domain`
--

DROP TABLE IF EXISTS `security_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `scoped_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_domain`
--

LOCK TABLES `security_domain` WRITE;
/*!40000 ALTER TABLE `security_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `logoPath` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `hookKey` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `entityid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `org_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enable_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `org_short_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `org_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `org_description` longtext COLLATE utf8_unicode_ci,
  `priv_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `priv_description` longtext COLLATE utf8_unicode_ci,
  `enabled` tinyint(1) DEFAULT NULL,
  `min_loa` bigint(20) DEFAULT NULL,
  `privacy_policy_set_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service`
--

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
INSERT INTO `service` (`id`, `logoPath`, `name`, `hookKey`, `entityid`, `url`, `description`, `org_name`, `enable_token`, `org_short_name`, `org_url`, `org_description`, `priv_url`, `priv_description`, `enabled`, `min_loa`, `privacy_policy_set_at`, `created_at`, `updated_at`) VALUES (1,NULL,'testService1','69c38e8ef25dcf5a3af040aac2f33ffaa775352507ac0a98f9f43db81b3d8c89287edb67d353a818f3a23b78d1cc45a1151accf880ad9a2317afe3de5c02daa8','https://example.com/ssp',NULL,'Ez a szolgáltatás teszteléshez készült. Írok hozzá egy kis leírást, de ez még nem lesz túl hosszú.',NULL,'157979f5-5f42-4184-a31a-c7627702eebe',NULL,NULL,NULL,NULL,NULL,1,0,NULL,'2018-03-29 07:14:01','2018-03-29 07:14:01'),(2,NULL,'testService2','d410f7e9db74c3f6b7d40bbc3a1a342dead0d01eb5a42ba7d366cc5d4c854e2b17f7792d2f80e194d2a410122036a32c4131f487945f7deb118533a4d37a1e10','https://example.com/ssp',NULL,'Lórum ipse nem jó és nem csapzott. Itt a zsonna jadzaga az, hogy egy vallámos zátlan pendő bités a mozásának üzékét előidézve hirtelen igen csomott áhságot pedzhetik vagy a szőkedésben videk fodráz vétlő, lilés hirtelen tovább növelve a szőkedést küblent parníthatja elő. A szükségbe fejős baság esetén a szőkedés szakás nadéka esetén nem tud “kifelé” csavúrnia, mivel a szotykodos göbölyű ezt jáncázja. Az egyik a restív, amikor a híző bridás ságosnál tinos folyákat pattogat. Itt a szellőnek az az amara, hogy a várhatóan lassan tovább földi gyornotos turpadást zöntömörje. Ha ugyanis tovább kold a baság, olyan pans ólkodik létre, amely már egy zatott vozatnál jobban nem tudja fusnia a kliumot, és cúgos módon parníthat elő hítást.',NULL,'a7ce655e-7cd8-4dd7-9f2d-2901fecc48f0',NULL,NULL,NULL,NULL,NULL,0,0,NULL,'2018-03-29 07:14:01','2018-03-29 07:14:01');
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_attribute_spec`
--

DROP TABLE IF EXISTS `service_attribute_spec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_attribute_spec` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `attribute_spec_id` bigint(20) DEFAULT NULL,
  `service_id` bigint(20) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_attribute_spec` (`service_id`,`attribute_spec_id`),
  KEY `attribute_spec_id_idx` (`attribute_spec_id`),
  KEY `service_id_idx` (`service_id`),
  CONSTRAINT `FK_9880EE002113FD3F` FOREIGN KEY (`attribute_spec_id`) REFERENCES `attribute_spec` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_9880EE00ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_attribute_spec`
--

LOCK TABLES `service_attribute_spec` WRITE;
/*!40000 ALTER TABLE `service_attribute_spec` DISABLE KEYS */;
INSERT INTO `service_attribute_spec` (`id`, `attribute_spec_id`, `service_id`, `is_public`, `created_at`, `updated_at`) VALUES (1,1,1,0,'2018-03-29 07:14:05','2018-03-29 07:14:05'),(2,2,1,1,'2018-03-29 07:14:05','2018-03-29 07:14:05'),(3,3,1,0,'2018-03-29 07:14:05','2018-03-29 07:14:05');
/*!40000 ALTER TABLE `service_attribute_spec` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_attribute_value_organization`
--

DROP TABLE IF EXISTS `service_attribute_value_organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_attribute_value_organization` (
  `attributevalueorganization_id` bigint(20) NOT NULL,
  `service_id` bigint(20) NOT NULL,
  PRIMARY KEY (`attributevalueorganization_id`,`service_id`),
  KEY `IDX_D586256626CBFBC2` (`attributevalueorganization_id`),
  KEY `IDX_D5862566ED5CA9E6` (`service_id`),
  CONSTRAINT `FK_D586256626CBFBC2` FOREIGN KEY (`attributevalueorganization_id`) REFERENCES `attribute_value_organization` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_D5862566ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_attribute_value_organization`
--

LOCK TABLES `service_attribute_value_organization` WRITE;
/*!40000 ALTER TABLE `service_attribute_value_organization` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_attribute_value_organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_attribute_value_principal`
--

DROP TABLE IF EXISTS `service_attribute_value_principal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_attribute_value_principal` (
  `attributevalueprincipal_id` bigint(20) NOT NULL,
  `service_id` bigint(20) NOT NULL,
  PRIMARY KEY (`attributevalueprincipal_id`,`service_id`),
  KEY `IDX_DB237D0A819C4D7D` (`attributevalueprincipal_id`),
  KEY `IDX_DB237D0AED5CA9E6` (`service_id`),
  CONSTRAINT `FK_DB237D0A819C4D7D` FOREIGN KEY (`attributevalueprincipal_id`) REFERENCES `attribute_value_principal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_DB237D0AED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_attribute_value_principal`
--

LOCK TABLES `service_attribute_value_principal` WRITE;
/*!40000 ALTER TABLE `service_attribute_value_principal` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_attribute_value_principal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_principal`
--

DROP TABLE IF EXISTS `service_principal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_principal` (
  `service_id` bigint(20) NOT NULL,
  `principal_id` bigint(20) NOT NULL,
  PRIMARY KEY (`service_id`,`principal_id`),
  KEY `IDX_7831B611ED5CA9E6` (`service_id`),
  KEY `IDX_7831B611474870EE` (`principal_id`),
  CONSTRAINT `FK_7831B611474870EE` FOREIGN KEY (`principal_id`) REFERENCES `principal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_7831B611ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_principal`
--

LOCK TABLES `service_principal` WRITE;
/*!40000 ALTER TABLE `service_principal` DISABLE KEYS */;
INSERT INTO `service_principal` (`service_id`, `principal_id`) VALUES (1,1),(2,1);
/*!40000 ALTER TABLE `service_principal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_security_domain`
--

DROP TABLE IF EXISTS `service_security_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_security_domain` (
  `service_id` bigint(20) NOT NULL,
  `securitydomain_id` int(11) NOT NULL,
  PRIMARY KEY (`service_id`,`securitydomain_id`),
  KEY `IDX_B34798FAED5CA9E6` (`service_id`),
  KEY `IDX_B34798FA5CF4CC57` (`securitydomain_id`),
  CONSTRAINT `FK_B34798FA5CF4CC57` FOREIGN KEY (`securitydomain_id`) REFERENCES `security_domain` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_B34798FAED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_security_domain`
--

LOCK TABLES `service_security_domain` WRITE;
/*!40000 ALTER TABLE `service_security_domain` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_security_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_tag`
--

DROP TABLE IF EXISTS `service_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_tag` (
  `service_id` bigint(20) NOT NULL,
  `tag_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`service_id`,`tag_id`),
  KEY `IDX_21D9C4F4ED5CA9E6` (`service_id`),
  KEY `IDX_21D9C4F4BAD26311` (`tag_id`),
  CONSTRAINT `FK_21D9C4F4BAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`name`),
  CONSTRAINT `FK_21D9C4F4ED5CA9E6` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_tag`
--

LOCK TABLES `service_tag` WRITE;
/*!40000 ALTER TABLE `service_tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-03-29  7:19:40
