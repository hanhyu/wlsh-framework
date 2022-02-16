-- MySQL dump 10.13  Distrib 5.7.31, for Linux (x86_64)
--
-- Host: 172.23.0.5    Database: wlsh_log
-- ------------------------------------------------------
-- Server version	8.0.20

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE = @@TIME_ZONE */;
/*!40103 SET TIME_ZONE = '+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;

--
-- Table structure for table `router_log`
--

DROP TABLE IF EXISTS `router_log`;
/*!40101 SET @saved_cs_client = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `router_log`
(
    `id`          int unsigned                           NOT NULL AUTO_INCREMENT,
    `trace_id`    varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `level`       varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '级别：info|notice|warning|error',
    `req_method`  varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '请求方法',
    `req_uri`     varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '请求路由',
    `req_data`    json                                   NOT NULL COMMENT '请求参数',
    `req_ip`      varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '请求IP',
    `fd_time`     int                                    NOT NULL DEFAULT '0' COMMENT '收包时间，如果该时间不等于req_time值（相差大于1s）时，则说明当前请求量超过服务器处理能力。',
    `req_time`    int                                    NOT NULL DEFAULT '0' COMMENT '请求进入worker进程时间',
    `resp_time`   int                                    NOT NULL DEFAULT '0' COMMENT '响应时间',
    `resp_data`   json                                   NOT NULL COMMENT '响应内容',
    `create_time` timestamp                              NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '日志记录时间，注意：resp_time - req_time的时间差为worker处理服务时间，create_time - resp_time的时间差为异步记录日志处理时间',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `router_log`
--

/*!40000 ALTER TABLE `router_log`
    DISABLE KEYS */;
/*!40000 ALTER TABLE `router_log`
    ENABLE KEYS */;
/*!40103 SET TIME_ZONE = @OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;

-- Dump completed on 2021-02-07 10:39:23
