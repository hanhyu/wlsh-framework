-- MySQL dump 10.13  Distrib 8.0.12, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: baseFrame
-- ------------------------------------------------------
-- Server version	8.0.12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `frame_system_backup`
--

DROP TABLE IF EXISTS `frame_system_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `frame_system_backup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(50) NOT NULL DEFAULT '0',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0',
  `file_md5` char(32) NOT NULL DEFAULT '0',
  `crt_dt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='数据库备份文件信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frame_system_backup`
--

LOCK TABLES `frame_system_backup` WRITE;
/*!40000 ALTER TABLE `frame_system_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `frame_system_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frame_system_menu`
--

DROP TABLE IF EXISTS `frame_system_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `frame_system_menu` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '0',
  `icon` varchar(50) NOT NULL DEFAULT '0',
  `url` varchar(50) NOT NULL DEFAULT '0',
  `upID` tinyint(4) NOT NULL DEFAULT '0',
  `level` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='主菜单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frame_system_menu`
--

LOCK TABLES `frame_system_menu` WRITE;
/*!40000 ALTER TABLE `frame_system_menu` DISABLE KEYS */;
INSERT INTO `frame_system_menu` VALUES (5,'日志中心','&#xe6b8;','view/log',0,1),(7,'系统管理','&#xe6b8;','view/system',0,1),(16,'主菜单设置','&#xe6b8;','view/system/menu_list.html',7,2),(20,'系统用户设置','&#xe6b8;','view/system/user_list.html',7,2),(21,'swoole日志','&#xe6b8;','view/logSwoole/swoole_list.html',5,2),(25,'更新服务器数据','&#xe6a2;','view/system/pull.html',7,2),(26,'数据库备份/恢复','&#xe6a2;','view/system/db_list.html',7,2),(27,'前端平台管理','&#xe6a2;','view/admin',0,1),(28,'平台用户设置','&#xe6a2;','view/admin/user_list.html',27,2),(31,'测试用例123','123456','123',5,2);
/*!40000 ALTER TABLE `frame_system_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frame_system_user`
--

DROP TABLE IF EXISTS `frame_system_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `frame_system_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '0',
  `pwd` char(64) NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户状态：默认10启用、20禁用',
  `cTime` datetime DEFAULT NULL,
  `remark` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frame_system_user`
--

LOCK TABLES `frame_system_user` WRITE;
/*!40000 ALTER TABLE `frame_system_user` DISABLE KEYS */;
INSERT INTO `frame_system_user` VALUES (1,'ceshi001','$2y$10$Ih54W3EORACrFQSXpRyWfO.150mfV4MK4lTis.sMwRNKLTCGeLa9O',10,'2018-10-25 01:59:38','0'),(2,'face789','$2y$10$k2uqI5g.NkVggZbjb48/gu7axGMLRftjdAy4FCris2zwxuE4xmWFW',10,'2018-10-10 01:47:34','旷视测试用'),(5,'34fwe43','$2y$10$Z4i7LIkTcI2XdebNzjLTkebEjpnvasaPEOHZsMoLLFrlJGvlPncEC',10,'2018-12-06 17:56:16','0'),(6,'sdfw3434','$2y$10$2Ouage0iGPAlQC7LI5jjvuuyh.VAVamb.vKFGfoXoYdmoX1tRsyV.',20,'2018-12-06 17:56:56','123'),(7,'ceshi12','$2y$10$1Wc8OGChPqCw800PVwnyu.aM8uSVdmn/sq2dUZWq/kntwUkQMN7fi',10,'2018-12-06 18:22:04','123'),(8,'ceshi003','$2y$10$TArksPTaF0oTpHHSu8hoNuJvf6UAJTU1dw/7y498zezIodL6685Z2',10,'2018-12-06 20:46:08','0'),(9,'ceshi004','$2y$10$5wrx57Z7cjVnJCSHNojTN.m.AnIiRtPCNn6BBtHZ0PBQfqOqLTbjm',10,'2018-12-06 20:53:04','0'),(10,'ceshi005','$2y$10$HYwj.upR3Fk2QbLYzzP4buRl2ILO5zLW9OFdOEw30FjCB9R5A7LVa',10,'2018-12-06 21:04:13','0'),(11,'ceshi006','$2y$10$h7SS5Jl2qxOSso1lT2NIYeqSaaU/sc8XOPWvE2SndtouQoY.ZRYUG',10,'2018-12-06 21:06:55','0'),(12,'ceshi007','$2y$10$dkWcjXX9OaJLN4sM0bU13.VnJ7c3M2aF2xonfSsPN7qvlOFwfD1kG',20,'2018-12-06 21:14:37','0434'),(14,'ceshi123','$2y$10$f4lQb6NyUEnEKl9fSpoc3uoIP7gKhaV8m.FwQwBpCHon0xfhLucZi',10,'2018-12-06 22:18:42','123456');
/*!40000 ALTER TABLE `frame_system_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frame_system_user_log`
--

DROP TABLE IF EXISTS `frame_system_user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `frame_system_user_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `lTime` timestamp NULL DEFAULT NULL,
  `eTime` timestamp NULL DEFAULT NULL,
  `lip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frame_system_user_log`
--

LOCK TABLES `frame_system_user_log` WRITE;
/*!40000 ALTER TABLE `frame_system_user_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `frame_system_user_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-12-12 16:03:30
