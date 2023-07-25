-- MySQL dump 10.13  Distrib 5.6.51, for Linux (x86_64)
--
-- Host: localhost    Database: www_kzk_com
-- ------------------------------------------------------
-- Server version	5.6.51-log

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
-- Table structure for table `cm_action_log`
--

DROP TABLE IF EXISTS `cm_action_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_action_log` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '执行会员id',
  `module` varchar(30) NOT NULL DEFAULT 'admin' COMMENT '模块',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '行为',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '执行的URL',
  `ip` char(30) NOT NULL DEFAULT '' COMMENT '执行行为者ip',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='行为日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_action_log`
--

LOCK TABLES `cm_action_log` WRITE;
/*!40000 ALTER TABLE `cm_action_log` DISABLE KEYS */;
INSERT INTO `cm_action_log` VALUES (1,1,'admin','登录','管理员admin登录成功','/admin/login/login.html','194.5.82.161',1,1,1676870928,1676870928),(2,1,'admin','修改','管理员ID1密码修改','/admin/site/changePwd','194.5.82.161',1,1,1676870958,1676870958),(3,1,'admin','新增','新增菜单,name =>余额管理','/admin/menu/menuAdd','194.5.82.161',1,1,1676871414,1676871414),(4,1,'admin','新增','新增菜单,name =>USDT充值','/admin/menu/menuAdd','194.5.82.161',1,1,1676871437,1676871437),(5,1,'admin','新增','新增菜单,name =>获取充值列表','/admin/menu/menuAdd','194.5.82.161',1,1,1676871459,1676871459),(6,1,'admin','新增','新增菜单,name =>充值USDT','/admin/menu/menuAdd','194.5.82.161',1,1,1676871479,1676871479),(7,1,'admin','新增','新增菜单,name =>余额明细','/admin/menu/menuAdd','194.5.82.161',1,1,1676871497,1676871497),(8,1,'admin','新增','新增菜单,name =>获取余额明细','/admin/menu/menuAdd','194.5.82.161',1,1,1676871519,1676871519);
/*!40000 ALTER TABLE `cm_action_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_admin`
--

DROP TABLE IF EXISTS `cm_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_admin` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `leader_id` mediumint(8) NOT NULL DEFAULT '1',
  `username` varchar(20) DEFAULT '0',
  `nickname` varchar(40) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `email` varchar(80) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `agent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '代理id',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `google_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'googleç¶æ1 ç»å® 0æªç»å®',
  `google_secret_key` varchar(100) NOT NULL DEFAULT '' COMMENT 'ç®¡çågoogleç§é¥',
  `balance` decimal(10,2) NOT NULL COMMENT '余额',
  `rate` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '费率',
  `daifu_auto_transfer` tinyint(10) NOT NULL DEFAULT '0' COMMENT '自动中转代付',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_admin`
--

LOCK TABLES `cm_admin` WRITE;
/*!40000 ALTER TABLE `cm_admin` DISABLE KEYS */;
INSERT INTO `cm_admin` VALUES (1,0,'admin','admin','46965619e637d537349928f51836a64e','13333333333','admin@163.com',1,0,1552016220,1676870958,1,'fhaUXh8ItJoZxtSDM2/9/TbcvXv837QKGadzXMQ517U=',200.00,0.000,0);
/*!40000 ALTER TABLE `cm_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_admin_bill`
--

DROP TABLE IF EXISTS `cm_admin_bill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_admin_bill` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NOT NULL COMMENT 'admin ID',
  `jl_class` int(11) NOT NULL COMMENT '流水类别：1充值                                                                                                           ',
  `info` varchar(225) NOT NULL COMMENT '说明',
  `addtime` varchar(225) NOT NULL COMMENT '添加时间',
  `jc_class` varchar(225) NOT NULL COMMENT '分+ 或-',
  `amount` float(10,2) NOT NULL COMMENT '金额',
  `pre_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '变化前',
  `last_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '变化后',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='admin 账单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_admin_bill`
--

LOCK TABLES `cm_admin_bill` WRITE;
/*!40000 ALTER TABLE `cm_admin_bill` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_admin_bill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_admin_rate`
--

DROP TABLE IF EXISTS `cm_admin_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_admin_rate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '码商id',
  `code_type_id` int(11) NOT NULL DEFAULT '0' COMMENT '支付编码id',
  `rate` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT '费率',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_admin_rate`
--

LOCK TABLES `cm_admin_rate` WRITE;
/*!40000 ALTER TABLE `cm_admin_rate` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_admin_rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_admin_recharge_record`
--

DROP TABLE IF EXISTS `cm_admin_recharge_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_admin_recharge_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NOT NULL COMMENT 'admin ID',
  `usdt_address` varchar(255) NOT NULL COMMENT 'USDT地址',
  `usdt_num` decimal(10,2) NOT NULL COMMENT 'usdt 数量',
  `amount` decimal(10,2) NOT NULL COMMENT '金额',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `status` tinyint(1) NOT NULL COMMENT '0支付中 1支付到账',
  `from_usdt_address` varchar(255) NOT NULL DEFAULT '' COMMENT '来自转账的地址',
  `transaction_id` varchar(255) NOT NULL DEFAULT '' COMMENT '交易ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='admin 充值记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_admin_recharge_record`
--

LOCK TABLES `cm_admin_recharge_record` WRITE;
/*!40000 ALTER TABLE `cm_admin_recharge_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_admin_recharge_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_api`
--

DROP TABLE IF EXISTS `cm_api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_api` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) DEFAULT NULL COMMENT '商户id',
  `key` varchar(32) DEFAULT NULL COMMENT 'API验证KEY',
  `sitename` varchar(30) NOT NULL,
  `domain` varchar(100) NOT NULL COMMENT '商户验证域名',
  `daily` decimal(12,3) NOT NULL DEFAULT '20000.000' COMMENT '日限访问（超过就锁）',
  `secretkey` text NOT NULL COMMENT '商户请求RSA私钥',
  `auth_ips` text NOT NULL,
  `role` int(4) NOT NULL COMMENT '角色1-普通商户,角色2-特约商户',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商户API状态,0-禁用,1-锁,2-正常',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `is_verify_sign` int(11) DEFAULT '1' COMMENT '是否验证sign 1 验证 0 不验证',
  PRIMARY KEY (`id`),
  UNIQUE KEY `api_domain_unique` (`id`,`domain`,`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_api`
--

LOCK TABLES `cm_api` WRITE;
/*!40000 ALTER TABLE `cm_api` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_api` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_article`
--

DROP TABLE IF EXISTS `cm_article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_article` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章ID',
  `author` char(20) NOT NULL DEFAULT 'admin' COMMENT '作者',
  `title` char(40) NOT NULL DEFAULT '' COMMENT '文章名称',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `content` text NOT NULL COMMENT '文章内容',
  `cover_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '封面图片id',
  `file_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件id',
  `img_ids` varchar(200) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '数据状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_index` (`id`,`title`,`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='文章表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_article`
--

LOCK TABLES `cm_article` WRITE;
/*!40000 ALTER TABLE `cm_article` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_article` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_auth_group`
--

DROP TABLE IF EXISTS `cm_auth_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_auth_group` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `module` varchar(20) NOT NULL DEFAULT '' COMMENT '用户组所属模块',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '用户组名称',
  `describe` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(1000) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='权限组表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_auth_group`
--

LOCK TABLES `cm_auth_group` WRITE;
/*!40000 ALTER TABLE `cm_auth_group` DISABLE KEYS */;
INSERT INTO `cm_auth_group` VALUES (1,1,'','超级管理员','拥有至高无上的权利',1,'超级权限',1541001599,1538323200),(2,2,'','代理专用','代理专用',1,'1,121,125,201,202,203',1668709984,1538323200),(3,0,'','编辑','负责编辑文章和站点公告',1,'1,15,16,17,32',1544360098,1540381656),(4,0,'','对账专用','对账专用',1,'61,67,68,149,84,85,86,87,89,90,92,93,150,104,147,94,95,96,97,146,105,111,144,145,117,118,121,125,126,128,129,130,131,134,140,143,148,151',1663261861,1660592422),(5,0,'','网站管理员','网站管理员',1,'1,182,212,298,300,301,2,3,6,7,299,26,28,29,30,31,254,61,62,63,64,65,66,127,141,142,195,205,209,216,67,68,69,70,132,133,149,191,192,204,207,208,84,85,86,87,89,90,92,93,150,210,88,99,100,103,160,147,173,174,175,176,177,105,111,184,185,186,187,206,144,145,217,218,272,273,274,275,276,277,297,321,117,118,128,129,130,131,134,136,140,148,151,157,166,167,168,169,294,170,171,172,178,179,180,181,211,278,219,121,197,155,159,196,213,214,215,222,223,292,224,225,293,226,227,255,228,229,286,230,231,284,287,232,233,288,234,289,235,236,237,257,258,290,262,263,264,268,269,291,302,303,304,306,307,308,315,316,317,220,125,156,158,188,189,190,238,239,281,240,241,282,242,243,256,244,245,246,247,280,248,249,279,283,250,251,285,252,253,259,260,261,265,266,267,270,271,305,309,310,311,312,313,314,318,319,320,221,193,194,198,199,200,322,323,324,325,326,327',1676871534,1667650678),(6,0,'','支付宝uid','支付宝uid',-1,'153,155,156',1667654462,1667654260),(7,0,'','支付宝UID','支付宝UID',-1,'',1667979589,1667838671),(8,0,'','客服','客服',1,'1,182,2,10,11,12,13,14,183,26,27,28',1667979374,1667926306),(9,0,'','代付分组','代付分组',1,'1,182,61,62,63,64,65,66,127,141,142,67,68,69,70,132,133,149,84,85,86,87,89,90,92,93,150,88,103,160,147,173,174,175,176,177,105,111,184,185,186,187,144,145,117,118,128,129,130,131,134,136,140,148,151,157,166,167,168,169,170,171,172,178,179,180,181',1668168946,1668168897),(10,0,'','客服开户','客服开户专用',1,'1,182,2,10,11,12,13,183,26,28',1675621439,1675621348);
/*!40000 ALTER TABLE `cm_auth_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_auth_group_access`
--

DROP TABLE IF EXISTS `cm_auth_group_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户组id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组授权表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_auth_group_access`
--

LOCK TABLES `cm_auth_group_access` WRITE;
/*!40000 ALTER TABLE `cm_auth_group_access` DISABLE KEYS */;
INSERT INTO `cm_auth_group_access` VALUES (2,2,1,1567687331,1567687331),(3,4,1,1660885986,1660885986),(4,5,1,1667650710,1667650710),(5,5,1,1667756368,1667756368),(6,5,1,1667800407,1667800407),(7,5,1,1667809778,1667809778),(8,5,1,1667817980,1667817980),(9,5,1,1667821175,1667821175),(10,5,1,1667838784,1667838784),(11,5,1,1667841046,1667841046),(12,5,1,1667893671,1667893671),(13,5,1,1667908451,1667908451),(14,5,1,1667908622,1667908622),(15,5,1,1667916007,1667916007),(16,8,1,1667926468,1667926468),(18,5,1,1667973646,1667973646),(17,5,1,1667979552,1667979552),(19,5,1,1668006362,1668006362),(20,5,1,1668065408,1668065408),(29,5,1,1668273831,1668273831),(48,5,1,1669302209,1669302209),(47,5,1,1669312528,1669312528),(21,5,1,1674645473,1674645473),(22,5,1,1674715756,1674715756),(23,5,1,1674752795,1674752795),(24,5,1,1674753198,1674753198),(25,5,1,1674894841,1674894841),(26,5,1,1674895340,1674895340),(27,5,1,1674909374,1674909374),(28,5,1,1674929290,1674929290),(30,5,1,1675147453,1675147453),(31,5,1,1675148294,1675148294),(32,5,1,1675165963,1675165963),(33,5,1,1675175372,1675175372),(34,5,1,1675236209,1675236209),(35,5,1,1675240497,1675240497),(36,5,1,1675322874,1675322874),(37,5,1,1675430878,1675430878),(38,5,1,1675435436,1675435436),(39,5,1,1675603117,1675603117),(40,10,1,1675621513,1675621513),(41,5,1,1675667059,1675667059),(42,5,1,1675762886,1675762886),(43,5,1,1675934722,1675934722),(44,5,1,1675954810,1675954810),(45,5,1,1675963346,1675963346),(46,5,1,1675964847,1675964847);
/*!40000 ALTER TABLE `cm_auth_group_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_balance`
--

DROP TABLE IF EXISTS `cm_balance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_balance` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `enable` decimal(12,3) unsigned DEFAULT '0.000' COMMENT '可用余额(已结算金额)',
  `disable` decimal(12,3) unsigned DEFAULT '0.000' COMMENT '冻结金额(待结算金额)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '账户状态 1正常 0禁止操作',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_index` (`id`,`uid`) USING BTREE,
  UNIQUE KEY `uid_index` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户资产表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_balance`
--

LOCK TABLES `cm_balance` WRITE;
/*!40000 ALTER TABLE `cm_balance` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_balance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_balance_cash`
--

DROP TABLE IF EXISTS `cm_balance_cash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_balance_cash` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `cash_no` varchar(80) NOT NULL COMMENT '取现记录单号',
  `amount` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT '取现金额',
  `account` int(2) NOT NULL DEFAULT '0' COMMENT '取现账户（关联商户结算账户表）',
  `remarks` varchar(255) NOT NULL COMMENT '取现说明',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '取现状态',
  `create_time` int(10) unsigned NOT NULL COMMENT '申请时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '处理时间',
  `commission` decimal(11,3) NOT NULL DEFAULT '0.000' COMMENT 'æç°æç»­è´¹',
  `audit_remarks` varchar(255) DEFAULT NULL COMMENT 'å®¡æ ¸å¤æ³¨',
  `bank_name` varchar(32) DEFAULT NULL COMMENT '开户行',
  `bank_number` varchar(32) DEFAULT NULL COMMENT '卡号',
  `bank_realname` varchar(32) DEFAULT NULL COMMENT '姓名',
  `voucher` varchar(255) DEFAULT NULL COMMENT '跑分平台凭证',
  `voucher_time` int(11) DEFAULT '0' COMMENT '凭证上传时间',
  `channel_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道编号 ',
  `cash_file` varchar(255) NOT NULL DEFAULT '' COMMENT 'è½¬æ¬¾å­è¯',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '提现方式  0:银行卡  1:usdt',
  `withdraw_usdt_address` varchar(255) NOT NULL DEFAULT '' COMMENT 'usdt提款地址',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_index` (`id`,`uid`,`cash_no`,`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户账户取现记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_balance_cash`
--

LOCK TABLES `cm_balance_cash` WRITE;
/*!40000 ALTER TABLE `cm_balance_cash` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_balance_cash` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_balance_change`
--

DROP TABLE IF EXISTS `cm_balance_change`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_balance_change` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `type` varchar(20) NOT NULL DEFAULT 'enable' COMMENT '资金类型',
  `preinc` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '变动前金额',
  `increase` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '增加金额',
  `reduce` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '减少金额',
  `suffixred` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '变动后金额',
  `remarks` varchar(255) NOT NULL COMMENT '资金变动说明',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `is_flat_op` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'æ¯å¦åå°äººå·¥è´¦å',
  `order_no` varchar(255) DEFAULT NULL,
  `type_reason` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `change_index` (`id`,`uid`,`type`,`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户资产变动记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_balance_change`
--

LOCK TABLES `cm_balance_change` WRITE;
/*!40000 ALTER TABLE `cm_balance_change` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_balance_change` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_bank`
--

DROP TABLE IF EXISTS `cm_bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_bank` (
  `bank_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(50) NOT NULL DEFAULT '' COMMENT '银行名称',
  `bank_color` varchar(200) NOT NULL DEFAULT '' COMMENT '银行App展示渐变色',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '银行网银地址',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '银行logo',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '银行状态0为启用，1为禁用',
  `create_user` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_user` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `is_maintain` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否维护',
  `maintain_start` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '维护开始时间',
  `maintain_end` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '维护结束时间',
  PRIMARY KEY (`bank_id`) USING BTREE,
  KEY `status` (`is_del`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='接受的在线提现银行表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_bank`
--

LOCK TABLES `cm_bank` WRITE;
/*!40000 ALTER TABLE `cm_bank` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_bank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_banker`
--

DROP TABLE IF EXISTS `cm_banker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_banker` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '银行ID',
  `name` varchar(80) NOT NULL COMMENT '银行名称',
  `remarks` varchar(140) NOT NULL COMMENT '备注',
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认账户,0-不默认,1-默认',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '银行可用性',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `bank_code` varchar(32) DEFAULT NULL COMMENT '银行编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=utf8mb4 COMMENT='系统支持银行列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_banker`
--

LOCK TABLES `cm_banker` WRITE;
/*!40000 ALTER TABLE `cm_banker` DISABLE KEYS */;
INSERT INTO `cm_banker` VALUES (2,'工商银行','工商银行',1,1,1535983287,1649746591,'ICBC'),(3,'农业银行','农业银行',1,1,1535983287,1649746588,'ABC'),(4,'招商银行','',1,1,1535983287,1649746523,'CMB'),(5,'中国民生','',1,1,1535983287,1649746601,'CMBC'),(6,'建设银行 ','中国建设银行',1,1,1535983287,1650077959,'CCB'),(7,'兴业银行','',1,1,1535983287,1649746607,'CIB'),(9,'中国光大','',1,1,1535983287,1649746612,'CEB'),(10,'邮政银行','中国邮政储蓄银行',1,1,1535983287,1650077901,'PSBC   '),(11,'中国银行','',1,1,1535983287,1649746619,'BOC'),(12,'平安银行','',1,1,1535983287,1649746622,'PAB'),(13,'中国农业','',1,1,1535983287,1649746628,'ABC'),(14,'北京银行','',1,1,1535983287,1649746631,'BOB'),(15,'上海浦东发展银行','',1,1,1535983287,1649746636,'SPDB'),(16,'宁波银行','',1,1,1535983287,1649746639,'NBCB'),(17,'中信银行','',1,1,1535983287,1649746645,'CITIC'),(18,'华夏银行','',1,1,1535983287,1649746649,'HXB'),(19,'交通银行','',1,1,1535983287,1649746672,'COMM'),(21,'桂林银行','',1,1,1584005500,1649747087,'GUILIN'),(23,'山西省农村信用社','',1,1,1649747351,1668686683,'SXNXS'),(24,'辽宁省农村信用社','',1,1,1649747369,1668686471,'LNNX'),(25,'吉林省农村信用社','',1,1,1649747453,1668686485,'JLNX'),(26,'黑龙江省农村信用社','',1,1,1649747472,1668687388,'HLJNX'),(27,'江苏省农村信用社','',1,1,1649747483,1668687372,'JSNX'),(28,'浙江省农村信用社','',1,1,1649747496,1668687356,'ZJNX'),(29,'安徽省农村信用社','',1,1,1649747509,1668687345,'AHNX'),(30,'福建省农村信用社','',1,1,1649747524,1668687335,'FJNX'),(31,'江西省农村信用社','',1,1,1649747535,1668687326,'JXNX'),(32,'山东省农村信用社','',1,1,1649747550,1668687316,'SDNX'),(33,'河南省农村信用社','',1,1,1649747576,1668687307,'HNNX'),(34,'湖北省农村信用社','',1,1,1649747592,1668687288,'HBNX'),(35,'湖南省农村信用社','',1,1,1649747604,1668687249,'HNNX'),(36,'广东省农村信用社','',1,1,1649747618,1668687236,'GDNX'),(37,'海南省农村信用社','',1,1,1649747637,1668687224,'HNNX'),(38,'四川省农村信用社','',1,1,1649747653,1668687213,'SCNX'),(39,'贵州省农村信用社','',1,1,1649747664,1668687199,'GZNX'),(40,'云南省农村信用社','',1,1,1649747688,1668687140,'YNNX'),(41,'陕西省农村信用社','',1,1,1649747709,1668687131,'SXNXS'),(42,'甘肃省农村信用社','',1,1,1649747721,1668687125,'GSNX'),(43,'青海省农村信用社','',1,1,1649747737,1668687116,'QHNX'),(44,'内蒙古自治区农村信用社','',1,1,1649747752,1668687101,'NMGXYS'),(45,'广西壮族自治区农村信用社','',1,1,1649747767,1668687094,'GXNX'),(46,'西藏自治区农村信用社','',1,1,1649747785,1668687080,'XZZZQNX'),(47,'宁夏省农村信用社','',1,1,1649747799,1668687064,'NXNX'),(48,'新疆省农村信用社','',1,1,1649747821,1668687048,'XJNX'),(49,'广发银行','',1,1,1649781066,1668687651,'GF'),(50,'江苏银行','',1,1,1649916222,1668687032,'JSYH'),(51,'上海农村商业银行','',1,1,1650904706,1650904706,'SH'),(52,'广州农村商业银行','',1,1,1651016002,1651016002,'GZNCYH'),(53,'上海银行','',1,1,1651311322,1651311322,'SHYH'),(54,'北京农商银行','',1,1,1652941556,1652941556,'BJ'),(55,'常熟农商银行','1',1,1,1653234121,1668687882,'CHSH'),(56,'贵州银行','',1,1,1653368462,1653368462,'GZYH'),(57,'浙江民泰商业银行','1',1,1,1653417166,1668687837,'ZJMTSYYH'),(58,'无锡农村商业银行','',1,1,1653779866,1668687869,'WXNCSYYH'),(59,'哈尔滨银行','',0,1,1654786615,1668687842,'HRB'),(60,'温州银行','',1,1,1654997353,1668687787,'WZH'),(61,'广西自治区农村信用社','',0,1,1658250089,1668687830,'GXZZQ'),(62,'重庆农村商业银行','',0,1,1658497181,1659494557,'cqncsyyh'),(63,'重庆农村商业银行','',1,1,1658497320,1668687803,'CQNS'),(64,'广发银行','',0,1,1658582124,1668687815,'HFYH'),(65,'河北省农村信用社','',1,1,1658668837,1668687001,'HBNX'),(66,'广西农村信用社联合社','',1,1,1658836334,1658836334,'GXNX'),(67,'渤海银行','',0,1,1659024770,1659024770,'BH'),(68,'长沙银行','',0,1,1659153466,1668608302,'CSYH2'),(69,'中原台州银行','',1,1,1659439442,1668687900,'ZYTZYH'),(70,'中原银行','',0,1,1659439502,1668687881,'ZYYH'),(71,'恒丰银行','',0,1,1659509748,1659509748,'HF'),(72,'武汉农村商业银行','',1,1,1659858186,1668688044,'WHNCSYYH'),(73,'南京银行','',1,1,1660008360,1668687968,'JNYH'),(74,'贵阳银行','',1,1,1660056716,1668686984,'GY'),(75,'湖北银行','',0,1,1660232869,1668686974,'HBYH'),(76,'长沙银行','',1,1,1660418670,1668686952,'ZHSH'),(77,'河北银行','',1,1,1660551385,1668686937,'HBYH'),(78,'东莞农村商业银行','',0,1,1660967148,1668686919,'DGNCSYYH'),(79,'浦发银行','',0,1,1661565589,1668686889,'PFYH'),(81,'汇丰银行','',1,1,1661822767,1668608281,'HFYHA'),(83,'上饶银行','',1,1,1662199939,1668687925,'SRYHA'),(84,'广州银行','',1,1,1662283514,1668688019,'GZHH'),(85,'微商银行','',1,1,1662357046,1668687929,'WSYH'),(86,'河北银行','',1,1,1662867273,1668687919,'HBYH'),(87,'晋商银行','',1,1,1663226846,1663226846,'JSYH'),(88,'莱商银行','',1,1,1663239106,1668071312,'LSHYH'),(89,'宁夏银行','',1,1,1663321499,1668687907,'NXX'),(90,'河北省农村信用社','',1,1,1663321548,1668608263,'HBNCXYS'),(91,'花旗银行','',1,1,1663549033,1668608191,'GWHQYH'),(92,'鼎业村镇银行','',0,1,1664000381,1668608231,'DYCZYH'),(93,'深圳龙岗鼎业村镇银行','',1,1,1664001008,1668687994,'SHZH'),(94,'东莞银行','',1,1,1664243296,1668608207,'SZDWYH'),(95,'九江银行','',1,1,1664284379,1668071288,'JJYH'),(96,'支付宝','支付宝',0,1,1668055324,1668055324,'ZFB'),(97,'云南农村信用社','云南农村信用社',0,1,1668071146,1668071146,'YNNCXYS'),(98,'农村合作信用社','农村合作信用社',0,1,1668071181,1668071181,'NCHZXYS'),(99,'昆明农联社','昆明农联社',0,1,1668082219,1668082219,'KMNLS'),(100,'微信','微信',0,1,1668592275,1668608480,'WX'),(101,'台州银行','台州银行',0,1,1668616223,1668616223,'ZGTZYH'),(103,'蒙商银行','',1,1,1668676976,1668676976,'PERB'),(104,'长春经开融丰村镇银行','',1,1,1668678922,1668678922,'CCRFCB'),(106,'包商银行','',1,1,1668679059,1668679059,'BSB'),(107,'保定银行','',1,1,1668679271,1668679271,'BOB'),(108,'北京农村商业银行','',1,1,1668679409,1668679409,'BJRCB'),(109,'阳光村镇银行','',1,1,1668679429,1668679429,'YGCZYH'),(110,'北京商业银行','',1,1,1668679450,1668679450,'BCCB'),(111,'本溪银行','',1,1,1668679507,1668679507,'BOBZ'),(112,'渤海银行','',1,1,1668679558,1668679558,'CBHB'),(113,'沧州银行','',1,1,1668679579,1668679579,'BOCZ'),(114,'常熟农商银行','',1,1,1668679599,1668679599,'CSRCB'),(115,'成都农商银行','',1,1,1668679696,1668679696,'CDRCB'),(116,'成都银行','',1,1,1668679719,1668679719,'CDCB'),(117,'稠州银行','',1,1,1668679768,1668679768,'CZCB'),(118,'达州银行','',1,1,1668679792,1668679792,'BODZ'),(119,'大连农商银行','',1,1,1668679816,1668679816,'DLRCB'),(120,'大连银行','',1,1,1668679839,1668679839,'DLB'),(121,'丹东银行','',1,1,1668679859,1668679859,'丹东银行'),(122,'东莞农村商业银行','',1,1,1668679885,1668679885,'DRCB'),(123,'东亚银行','',1,1,1668679909,1668679909,'HKBEA'),(124,'东营银行','',1,1,1668679932,1668679932,'DYCCB'),(126,'抚顺银行','',1,1,1668680017,1668680017,'FSCB'),(127,'阜新银行','',1,1,1668680036,1668680036,'BOFX'),(128,'富滇银行','',1,1,1668680060,1668680060,'FDB'),(130,'甘肃银行','',1,1,1668680115,1668680115,'GSBANK'),(131,'赣州银行','',1,1,1668680142,1668680142,'GZB'),(132,'广东发展银行','',1,1,1668680165,1668680165,'CGB'),(133,'广东华兴银行','',1,1,1668680185,1668680185,'GHB'),(134,'广东南粤银行','',1,1,1668680248,1668680248,'GDNY'),(135,'广东农商银行','',1,1,1668680284,1668680284,'GZRCB'),(136,'广东农信','',1,1,1668680305,1668680305,'GDRC'),(138,'广东省农村信用社联合社','',1,1,1668680353,1668680353,'GDRCC'),(139,'广西北部湾银行','',1,1,1668680456,1668680456,'BGB'),(140,'乐山商业银行','',1,1,1668680480,1668680480,'LSCCB'),(141,'广西农信银行','',1,1,1668680539,1668687535,'GXNX'),(142,'广州农商银行','',1,1,1668680577,1668680577,'GRCB'),(143,'贵阳银行','',1,1,1668680598,1668680598,'GYCB'),(144,'贵州省农村信用社联合社','',1,1,1668680621,1668680621,'GZRCU'),(145,'贵州银行','',1,1,1668680651,1668680651,'ZYCBANK'),(146,'桂林国民银行','',1,1,1668680672,1668680672,'GLGM'),(147,'哈尔滨银行','',1,1,1668680706,1668680706,'HRBANK'),(148,'海南农信','',1,1,1668680729,1668680729,'HNB'),(149,'海南银行','',1,1,1668680759,1668680759,'HNBANK'),(150,'邯郸银行','',1,1,1668680800,1668680800,'HDBANK'),(151,'汉口银行','',1,1,1668680819,1668680819,'HKB'),(152,'杭州联合银行','',1,1,1668680839,1668680839,'URCB'),(153,'杭州银行','',1,1,1668680863,1668680863,'HZCB'),(154,'河北银行','',1,1,1668680884,1668680884,'BHB'),(156,'黑龙江农信','',1,1,1668680922,1668680922,'HLJRCU'),(157,'恒丰银行','',1,1,1668680944,1668680944,'EGBANK'),(158,'河北农信银行','',1,1,1668680994,1668680994,'HEBNX'),(159,'湖北省农信社','',1,1,1668681016,1668681016,'HURCB'),(160,'湖北银行','',1,1,1668681036,1668681036,'HBC'),(162,'华融湘江银行','',1,1,1668681127,1668681127,'HRXJB'),(163,'华夏银行','',1,1,1668681148,1668681148,'HXB'),(164,'桦甸惠民村镇银行','',1,1,1668681171,1668681171,'HDHMB'),(165,'黄河农信银行','',1,1,1668681189,1668681189,'HHNX'),(166,'吉林农信银行','',1,1,1668681284,1668681284,'JLRCU'),(167,'吉林银行','',1,1,1668681305,1668681305,'JLBANK'),(168,'济宁银行','',1,1,1668681326,1668681326,'BOJN'),(169,'江门农商银行','',1,1,1668681355,1668681355,'JMRCB'),(170,'江南农村商业银行','',1,1,1668681376,1668681376,'JNRCB'),(171,'江苏农商银行','',1,1,1668681394,1668681394,'JRCB'),(173,'江苏省农村信用社联合社','',1,1,1668681442,1668681442,'JSRCU'),(174,'江苏银行','',1,1,1668681509,1668681509,'JSBC'),(176,'江西银行','',1,1,1668681557,1668681557,'NCB'),(177,'金华银行','',1,1,1668681577,1668681577,'JHBANK'),(178,'锦州银行','',1,1,1668681596,1668681596,'BOJZ'),(179,'晋城银行','',1,1,1668681614,1668681614,'JCCB'),(180,'昆仑银行','',1,1,1668682291,1668682291,'KLB'),(181,'昆山农商银行','',1,1,1668682311,1668682311,'KSRB'),(182,'兰州银行','',1,1,1668682348,1668682348,'LZYH'),(183,'廊坊银行','',1,1,1668682367,1668682367,'LANGFB'),(184,'唐山银行','',1,1,1668682388,1668682388,'TSB'),(186,'辽阳银行','',1,1,1668682424,1668682424,'LYCB'),(187,'临商银行','',1,1,1668682453,1668682453,'LSBC'),(188,'柳州银行','',1,1,1668682483,1668682483,'LZCCB'),(189,'龙江银行','',1,1,1668682504,1668682504,'LJBANK'),(190,'洛阳银行','',1,1,1668682526,1668682526,'LYBANK'),(191,'绵阳商业银行','',1,1,1668682544,1668682544,'MYCC'),(192,'民泰银行','',1,1,1668682563,1668682563,'MTBANK'),(193,'南海农商银行','',1,1,1668682584,1668682584,'NRCB'),(194,'南京银行','',1,1,1668682614,1668682614,'NJCB'),(195,'宁夏黄河农村商业银行','',1,1,1668682631,1668682631,'NXRCU'),(196,'宁夏银行','',1,1,1668682653,1668682653,'NXBANK'),(197,'齐鲁银行','',1,1,1668682670,1668682670,'QLBANK'),(198,'齐商银行','',1,1,1668682692,1668682692,'QSB'),(199,'青岛农商银行','',1,1,1668682714,1668682714,'QRCB'),(200,'青岛银行','',1,1,1668682736,1668682736,'QDCCB'),(201,'青海银行','',1,1,1668682754,1668682754,'QHRC'),(202,'日照银行','',1,1,1668682771,1668682771,'RZB'),(203,'厦门国际银行','',1,1,1668682788,1668682788,'XMIB'),(204,'厦门国际银行','',1,1,1668682789,1668682789,'XMIB'),(205,'厦门银行','',1,1,1668682806,1668682806,'BOXM'),(207,'山西农信','',1,1,1668682843,1668682843,'SRCU'),(208,'上海农商银行','',1,1,1668682875,1668682875,'SRCBANK'),(209,'上海银行','',1,1,1668682893,1668682893,'SHBANK'),(210,'上虞农商银行','',1,1,1668682912,1668682912,'SYCB'),(211,'绍兴银行','',1,1,1668682932,1668682932,'SXCB'),(212,'深圳福田银座村镇银行','',1,1,1668682949,1668682949,'FTYZB'),(213,'云南农信银行','',1,1,1668682970,1668682970,'YNRCC'),(214,'深圳农商银行','',1,1,1668682993,1668682993,'SRCB'),(215,'深圳前海微众银行','',1,1,1668683010,1668683010,'WEBANK'),(216,'盛京银行','',1,1,1668683030,1668683030,'SJBANK'),(217,'石嘴山银行','',1,1,1668683125,1668683125,'SZSCCB'),(218,'顺德农村商业银行','',1,1,1668683144,1668683144,'SDEBANK'),(220,'四川天府银行','',1,1,1668683190,1668683190,'PWEB'),(221,'苏州农商银行','',1,1,1668683213,1668683213,'WJRCB'),(222,'苏州银行','',1,1,1668683232,1668683232,'BOSZ'),(223,'台州银行','',1,1,1668683255,1668683255,'TZCB'),(224,'太仓农商银行','',1,1,1668683304,1668683304,'TCRCB'),(225,'泰安银行','',1,1,1668683349,1668683349,'TACCB'),(226,'承德银行','',1,1,1668683370,1668683370,'CDBANK'),(227,'泰隆银行','',1,1,1668683404,1668683404,'ZJTLCB'),(228,'天津滨海农村商业银行','',1,1,1668683427,1668683427,'TJBHB'),(229,'天津农商银行','',1,1,1668683452,1668683452,'TRCB'),(230,'天津银行','',1,1,1668683611,1668683611,'TCCB'),(231,'威海银行','',1,1,1668683640,1668683640,'WHCCB'),(232,'微信固码','',1,1,1668683674,1668683674,'WSGM'),(233,'温州银行','',1,1,1668683695,1668683695,'WZCB'),(234,'五常惠民村镇银行','',1,1,1668683716,1668683716,'WCHMB'),(235,'武汉农商银行','',1,1,1668683749,1668683749,'WHRCB'),(236,'西安银行','',1,1,1668683766,1668683766,'XABANK'),(237,'萧山农商银行','',1,1,1668683785,1668683785,'ZJXSBANK'),(238,'邢台银行','',1,1,1668683811,1668683811,'XTB'),(239,'烟台银行','',1,1,1668683830,1668683830,'YTBANK'),(240,'阳泉商业银行','',1,1,1668683850,1668683850,'YQCCB'),(241,'银座村镇银行','',1,1,1668683868,1668683868,'YZBANK'),(242,'营口银行','',1,1,1668683887,1668683887,'BOYK'),(243,'枣庄银行','',1,1,1668683915,1668683915,'ZZB'),(244,'张家港农商银行','',1,1,1668683934,1668683934,'RCBOZ'),(245,'张家口银行','',1,1,1668683972,1668683972,'ZJKCCB'),(246,'长安银行','',1,1,1668683998,1668683998,'CCAB'),(247,'长江商业银行','',1,1,1668684022,1668684022,'JSCCB'),(248,'长沙农商银行','',1,1,1668684041,1668684041,'CRCB'),(249,'长沙银行','',1,1,1668684207,1668684207,'CSCB'),(250,'浙江农村信用合作社','',1,1,1668684226,1668684226,'ZJNX'),(251,'鞍山银行','',1,1,1668684264,1668684264,'ASBANK'),(252,'浙江网商银行','',1,1,1668684283,1668684283,'MYBANK'),(253,'浙商银行','',1,1,1668684333,1668684333,'CZBANK'),(254,'郑州银行','',1,1,1668684352,1668684352,'ZZBANK'),(255,'中银富登村镇银行','',1,1,1668684369,1668684369,'BOCFTB'),(256,'长春经开融丰村镇银行','',1,1,1668684472,1668684472,'CCRFCB'),(257,'中原银行','',1,1,1668684489,1668684489,'ZYBANK'),(258,'重庆农村商业银行','',1,1,1668684514,1668684514,'CRCBANK'),(259,'重庆三峡银行','',1,1,1668684527,1668684527,'CCQTGB'),(260,'重庆银行','',1,1,1668684548,1668684548,'CQBANK'),(261,'珠海华润银行','',1,1,1668684565,1668684565,'CRBANK'),(262,'紫金农商银行','',1,1,1668684582,1668684582,'ZJRCBANK'),(263,'自贡银行','',1,1,1668684597,1668684597,'ZGCCB'),(264,'南阳村镇银行','',1,1,1668684665,1668684665,'NYCBANK'),(265,'衡水银行','',1,1,1668684682,1668684682,'HSB'),(266,'平顶山银行','',1,1,1668684701,1668684701,'PDSB'),(268,'内蒙古银行','',1,1,1668684739,1668684739,'BOIMC'),(269,'惠水恒升村镇银行','',1,1,1668684756,1668684756,'HSHSB'),(270,'朝阳银行','',1,1,1668684772,1668684772,'CYCB'),(271,'江西农商银行','',1,1,1668684793,1668684793,'JXNXS'),(272,'贵阳农商银行','',1,1,1668684869,1668684869,'GYNSH'),(273,'泉州银行','',1,1,1668684887,1668684887,'QZCCB'),(274,'安图农商村镇银行','',1,1,1668684904,1668684904,'ATCZB'),(275,'融兴村镇银行','',1,1,1668684930,1668684930,'RXVB'),(276,'宁波通商银行','',1,1,1668684950,1668684950,'NCBANK'),(277,'天津宁河村镇银行','',1,1,1668684971,1668684971,'NINGHEB'),(278,'广州增城长江村镇银行','',1,1,1668684993,1668684993,'ZCCJB'),(279,'鄞州银行','',1,1,1668685012,1668685012,'BEEB'),(280,'海丰农商银行','',1,1,1668685029,1668685029,'GFRCB'),(281,'上饶银行','',1,1,1668685048,1668685048,'SRBANK'),(282,'黄梅农村商业银行','',1,1,1668685065,1668685065,'HMRCB'),(284,'密山农商银行','',1,1,1668685100,1668685100,'MSRCB'),(285,'义乌联合村镇银行','',1,1,1668685116,1668685116,'ZJYURB'),(286,'广东普宁汇成村镇银行','',1,1,1668685132,1668685132,'GDPHCB'),(287,'浙江诸暨联合村镇银行','',1,1,1668685160,1668685160,'ZJURB'),(288,'浙江农商银行','',1,1,1668685194,1668685194,'ZJRCB'),(289,'永州农村商业银行','',1,1,1668685215,1668685215,'HNNXS'),(290,'潮州农商银行','',1,1,1668685235,1668685235,'GRCBANK'),(291,'义乌农商银行','',1,1,1668685258,1668685258,'YRCBANK'),(292,'海口联合农商银行','',1,1,1668685275,1668685275,'UNITEDB'),(293,'瑞丰银行','',1,1,1668685298,1668685298,'BORF'),(294,'山西银行','',1,1,1668685318,1668685318,'SHXIBANK'),(295,'海峡银行','',1,1,1668685338,1668685338,'FJHXBANK'),(296,'东莞市商业银行','东莞市商业银行',0,1,1668837444,1668837444,'DWSYYH'),(297,'河北省农村信用社','河北省农村信用社',0,1,1669554974,1669554974,'HBNCXYS');
/*!40000 ALTER TABLE `cm_banker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_banktobank_sms`
--

DROP TABLE IF EXISTS `cm_banktobank_sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_banktobank_sms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) NOT NULL COMMENT '来短信的号码',
  `context` text COMMENT '短信内容',
  `ip` varchar(255) DEFAULT NULL,
  `ms_id` int(11) DEFAULT NULL COMMENT '码商id',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '短信自动回调订单ID',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_banktobank_sms`
--

LOCK TABLES `cm_banktobank_sms` WRITE;
/*!40000 ALTER TABLE `cm_banktobank_sms` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_banktobank_sms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_config`
--

DROP TABLE IF EXISTS `cm_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_config` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置标题',
  `type` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `value` text NOT NULL COMMENT '配置值',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置选项',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '配置说明',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `admin_id` int(11) NOT NULL DEFAULT '1' COMMENT '所属管理员ID',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM AUTO_INCREMENT=1197 DEFAULT CHARSET=utf8 COMMENT='基本配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_config`
--

LOCK TABLES `cm_config` WRITE;
/*!40000 ALTER TABLE `cm_config` DISABLE KEYS */;
INSERT INTO `cm_config` VALUES (1,'seo_title','网站标题',1,1,0,'三方','','',1,1378898976,1585677353,1),(8,'email_port','SMTP端口号',1,8,1,'2','1:25,2:465','如：一般为 25 或 465',1,1378898976,1545131349,1),(2,'seo_description','网站描述',2,3,0,'','','网站搜索引擎描述，优先级低于SEO模块',1,1378898976,1585677353,1),(3,'seo_keywords','网站关键字',2,4,0,'三方','','网站搜索引擎关键字，优先级低于SEO模块',1,1378898976,1585677353,1),(4,'app_index_title','首页标题',1,2,0,'三方','','',1,1378898976,1585677353,1),(5,'app_domain','网站域名',1,5,0,'','','网站域名',1,1378898976,1585677353,1),(6,'app_copyright','版权信息',2,6,0,'三方','','版权信息',1,1378898976,1585677353,1),(7,'email_host','SMTP服务器',3,7,1,'2','1:smtp.163.com,2:smtp.aliyun.com,3:smtp.qq.com','如：smtp.163.com',1,1378898976,1569507595,1),(9,'send_email','发件人邮箱',1,9,1,'12345@qq.com','','',1,1378898976,1569507595,1),(10,'send_nickname','发件人昵称',1,10,1,'','','',1,1378898976,1569507595,1),(11,'email_password','邮箱密码',1,11,1,'xxxxxx','','',1,1378898976,1569507595,1),(12,'rsa_public_key','平台数据公钥',2,6,0,'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxV1hB4NP1NFgEM0mrx34z8gJMPBIhvDjAJcnMozk3jmUY9PkB7lZyfD6Fb+Xq21jIPX5zF4ggeYoK5keUH6TW9eJEr5JOqDl2YgKAdLfxLuJ4r8X1S3wflVp2/BURIbP1VGh6qNAxS3o8miL7x5BZ+jOhs4/LCq8YkncZioui5eAQ+/BoE++uM5IeSWZEVf8JsGo+MrOG2E/eOqetrB08Tm68igM6OMbKr05HKupcZm63zzDIHRJGKRjvdFjVoVznGsAC3phyh3bzYrjxykH00mLyw39/77MiBMp/uWVMh6wwiAjY2B25IKXXGCd0JSYvlpJWtCKbxlcAGDWSWkS0wIDAQAB','','平台数据公钥（RSA 2048）',1,1378898976,1585677353,1),(13,'rsa_private_key','平台数据私钥',2,6,0,'MIIEpAIBAAKCAQEAxV1hB4NP1NFgEM0mrx34z8gJMPBIhvDjAJcnMozk3jmUY9PkB7lZyfD6Fb+Xq21jIPX5zF4ggeYoK5keUH6TW9eJEr5JOqDl2YgKAdLfxLuJ4r8X1S3wflVp2/BURIbP1VGh6qNAxS3o8miL7x5BZ+jOhs4/LCq8YkncZioui5eAQ+/BoE++uM5IeSWZEVf8JsGo+MrOG2E/eOqetrB08Tm68igM6OMbKr05HKupcZm63zzDIHRJGKRjvdFjVoVznGsAC3phyh3bzYrjxykH00mLyw39/77MiBMp/uWVMh6wwiAjY2B25IKXXGCd0JSYvlpJWtCKbxlcAGDWSWkS0wIDAQABAoIBAFeeoB/8vOlHVrW+zii6Tqa4MNRoKFq4AJ9Xe5BmmojJ2UYEYNzI/cK4V95l44i4lGSirxZ6x0XEDxtj6+BigTsp0fHfRpVfrwtG6OJsYultNMbUfVkn/venJcr9w/t0OjqC9jY76dpgCmXr4gvzS6g848tXLxaFloKwNcepfGZ9wQb8Kt+5ONzn3BUcczu4DhuWfkt6oQ4j1KPl0UIdLZ7tevG1guUUr15p6VGsvQtMh4U7Lct/+0XUp4chut6fvoAIbEHnAE8rkAZBjrICwsYKNANNBEgVhtn5sK12RVZdUEd3vBWry9YOk1dgsEmi+chqQFlD18bO5/phIXEpK4kCgYEA7mugHzBcr53tSJVwh4IkyXQOs+gW5wSqbjHhTafN29w4qOJ9ZAxELogz4gQ25Yn95l1gpOY0cyH5x6QHsPFuJJBJp9sEiGplYSsCalK1qJaQewvAMd1Ctqk5A67QHgE/4xh+id9l+e1a9SKNqg3X3X1QdLddzwoq0i1Oj407KnUCgYEA0+rLqcJC0swSIchWpWLKQ/kgu093CXVvDoTugWPuHi4Ua49/9nPv0zSjMX5GXzGZ7CAQca/Gwg24R6bvc8wgwe9OYf8/ILQ3XUHmZJIHMXD/HuZqBMn/Swu62MJalOYTOsKp4hxNvxJkZPpku6gr5C611LaOsbE6iQDyeqmtzycCgYAeVGClNxDDYnK6BhCvnFWzrujj6AVp1AUeSYggydT9QBGRImbTIGBYDwmSmfil0J0U/hH6SDKp5suQowQ7dSsOybAlA06bT/Wfm8oN3oGvdZ/hl0gWz8/ZzsMq/cUJ3BzVdds7DMk7Nv+YKZId7O7mBTgD8QOk/+UcoZjZ2ByLtQKBgQCPP99OMJfVQMdc+LzBbWdGzYf3tj7EMRLSYL+MzY0v73w0PTuF0FckkSdjlHVjcfcXa5FSGD0l/fo8zTZ+M1VNY0O78LuuksP+EUb5YtDj9fsu2xh9hkJBa3txfOeYUXJcPSxzQSi46Wjd7XjcdVC+HWkikgkhSqlD5VUD3+Ey7wKBgQDtarpiVV19/IWiRbKy7rKJcG1HnezqfoA7outJK6yG7ne1vTjkGD/BLTSJm032htPFRmrwxhDOz0EilCjCz+ID2iPWKzhiZpf5yZ/qoFrFdofNWhLyAzNzxDhAZbcVG6ebjkMfHj84sChenGk31HfuplMD0GBe8DlC7UGerxCu1A==','','平台数据私钥（RSA 2048）',1,1378898976,1585677353,1),(16,'logo','ç«ç¹LOGO',4,6,0,'','','ä¸ä¼ ç«ç¹logo',1,1378898976,1576391324,1),(14,'withdraw_fee','提现手续费',1,6,0,'5','','提现手续费',1,1378898976,1585677353,1),(15,'thrid_url_gumapay','åºå®ç è¯·æ±å°å',1,6,0,'http://45.207.58.203//index.php','','åºå®ç ç¬¬ä¸æ¹apiè¯·æ±å°å',1,1378898976,1585677353,1),(18,'auth_key','éä¿¡ç§é¥',1,7,0,'XforgXQl2746FBIT','','ä¸è·å¹³å°éä¿¡ç§é¥',1,1378898976,1585677353,1),(19,'four_noticy_time','四方通知时间',1,8,0,'201','','四方码商回调通知时间(单位分钟)',1,1378898976,1585677353,1),(20,'max_withdraw_limit','提现最大金额',0,0,0,'600000000','','',1,0,1585677353,1),(21,'min_withdraw_limit','提现最小金额',0,0,0,'99','','',1,0,1585677353,1),(22,'balance_cash_type','提现申请类型',3,0,0,'2','1:选择账号,2:手动填写账号','',1,0,1585677353,1),(23,'request_pay_type','发起支付订单类型',3,0,0,'2','1:平台订单号,2:下游订单号','',1,0,1584606747,1),(24,'notify_ip','回调ip',0,54,0,'','','',1,0,1585677353,1),(25,'is_single_handling_charge','是否开启单笔手续费',3,51,0,'1','1:开启,0:不开启','',1,0,1585677353,1),(26,'whether_open_daifu','是否开启代付',3,50,0,'1','1:开启,2:不开启','',1,0,1585677353,1),(27,'index_view_path','前台模板',3,0,0,'view','view:默认,baisha:白沙,view1:版本2','',1,0,1585833746,1),(28,'is_open_channel_fund','渠道资金是否开启',3,0,0,'0','0:关闭,1:开启','',1,0,0,1),(29,'is_paid_select_channel','提现审核选择渠道',3,0,0,'1','0:不选择,1:选择','',1,0,0,1),(30,'balance_cash_adminlist','提现列表url',0,0,0,'/api/withdraw/getAdminList','','',1,0,0,1),(31,'balance_cash_revocation','提现撤回url',0,0,0,'/api/withdraw/revocation','','',1,0,0,1),(32,'daifu_notify_ip','代付回调ip白名单',1,0,0,'127.0.0.1','','',1,0,0,1),(33,'daifu_host','代付接口地址',1,0,0,'','','',1,0,0,1),(34,'daifu_key','跑分密钥',1,0,0,'3e9c1885afa5920909f9b9aa2907cf19','','',1,0,0,1),(35,'daifu_notify_url','回调地址',1,0,0,'','','',1,0,0,1),(36,'transfer_ip_list','中转ip白名单',2,0,0,'127.0.0.1','','多个使用逗号隔开',1,0,0,1),(37,'proxy_debug','是否开启中转回调',3,0,0,'1','1:开启,0:不开启','',1,0,0,1),(38,'orginal_host','中转回调地址',0,0,0,'http://45.207.58.203/zz.php','','',1,0,0,1),(39,'daifu_admin_id','代付admin_id',1,0,0,'5','','',1,0,0,1),(40,'is_channel_statistics','是否开启渠道统计',3,0,0,'0','1:开启,0:不开启','',1,0,0,1),(41,'admin_view_path','后台模板',3,0,0,'view','view:默认,baisha:白沙','',1,0,1585833746,1),(42,'index_domain_white_list','前台域名白名单',1,0,0,'','','如https://www.baidu.com/ 请输入www.baidu.com',1,0,0,1),(43,'pay_domain_white_list','下单域名白名单',0,0,0,'','','如https://www.baidu.com/ 请输入www.baidu.com',1,0,0,1),(44,'admin_domain_white_list','后台域名白名单',0,0,0,'','','如https://www.baidu.com/ 请输入www.baidu.com',1,0,0,1),(1111,'global_tgbot_token','全 局机器人token唯一标识',1,0,0,'1673522495:AAE6-JDXf3z5ZSk7pFoLkwR6XYzkv_jMg_g','','',1,0,0,1),(1112,'tg_order_warning_robot_token','订单报警机器人token',0,0,0,'1673522495:AAE6-JDXf3z5ZSk7pFoLkwR6XYzkv_jMg_g','','',1,0,0,1),(1113,'tg_order_warning_rebot_in_chat','订单机器人所在群组',0,0,0,'-449166252','','',1,0,0,1),(1114,'withdraw_usdt_rate','ustd下发手续费',1,6,0,'0','','',1,0,0,1),(1115,'daifu_ms_id','代付码商ID',1,0,0,'','','',1,1657884061,1657884061,1),(1116,'daifu_tgbot_token','代付机器人token',1,0,0,'5488115037:AAHCWwtjhGtj3ZcrYUTD4815pAPjtOk2bvc','','',1,1660383105,1660383105,1),(1117,'daifu_min_amount','代付最小金额',1,0,0,'1','','',1,1661930871,1661930871,1),(1118,'daifu_max_amount','代付最大金额',1,0,0,'500000','','',1,1661930895,1661930895,1),(1120,'daifu_err_reason','代付失败原因',6,0,3,'收款账户与户名不符,收款卡问题请更换卡再提交,支付中断,转账失败,收款方账户异常,银行维护,手机号对应多个绑定支付宝,收款账号未实名,收款账号收到支付宝风控','','',1,0,0,1),(1121,'thrid_url_uid','UID中转地址',1,6,0,'http://45.207.58.203/uid.php','','UID中转地址',1,1378898976,1378898976,1),(1141,'sys_enable_recharge','系统启用充值',3,0,0,'1','1:开启,0:不开启','',1,1666212478,1666212478,1),(1196,'sys_operating_password','设置操作口令',256,0,0,'1552c03e78d38d5005d4ce7b8018addf','','',1,1676870947,1676870947,1);
/*!40000 ALTER TABLE `cm_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_dafiu_account`
--

DROP TABLE IF EXISTS `cm_dafiu_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_dafiu_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_name` varchar(45) DEFAULT NULL,
  `money` decimal(10,2) DEFAULT NULL,
  `controller` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_dafiu_account`
--

LOCK TABLES `cm_dafiu_account` WRITE;
/*!40000 ALTER TABLE `cm_dafiu_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_dafiu_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_daifu_orders`
--

DROP TABLE IF EXISTS `cm_daifu_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_daifu_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `notify_url` varchar(500) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `bank_number` varchar(45) DEFAULT NULL,
  `bank_owner` varchar(45) DEFAULT NULL,
  `bank_id` int(10) DEFAULT NULL,
  `bank_name` varchar(45) DEFAULT NULL,
  `out_trade_no` varchar(45) DEFAULT NULL,
  `trade_no` varchar(45) DEFAULT NULL,
  `body` varchar(45) DEFAULT NULL,
  `subject` varchar(45) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `service_charge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `single_service_charge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单笔手续费',
  `notify_result` text COMMENT '回调返回内容 SUCCESS为成功 其他为失败',
  `ms_id` int(10) NOT NULL DEFAULT '0' COMMENT 'ms_id',
  `error_reason` varchar(255) NOT NULL DEFAULT '' COMMENT 'reson',
  `finish_time` int(10) NOT NULL DEFAULT '0' COMMENT 'finish_time',
  `matching_time` int(10) NOT NULL DEFAULT '0' COMMENT 'matching_time',
  `df_bank_id` int(10) NOT NULL DEFAULT '0' COMMENT 'df_bank_id',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `send_notify_times` int(1) NOT NULL DEFAULT '0' COMMENT '代付订单回调次数',
  `transfer_chart` varchar(2083) NOT NULL COMMENT '代付转账截图',
  `is_to_channel` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否中转订单',
  `daifu_transfer_name` varchar(255) NOT NULL COMMENT '代付中转通道名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_daifu_orders`
--

LOCK TABLES `cm_daifu_orders` WRITE;
/*!40000 ALTER TABLE `cm_daifu_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_daifu_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_daifu_orders_transfer`
--

DROP TABLE IF EXISTS `cm_daifu_orders_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_daifu_orders_transfer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '平台名称',
  `api_url` varchar(2083) DEFAULT NULL COMMENT '请求url',
  `merchant_id` varchar(255) DEFAULT NULL COMMENT '商户号',
  `api_key` varchar(255) DEFAULT NULL COMMENT '通道密钥',
  `controller` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `notify_url` varchar(2083) DEFAULT NULL,
  `notify_ips` text,
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '中转平台余额（上游查询）',
  `is_query_balance` tinyint(11) NOT NULL DEFAULT '0' COMMENT '是否查询余额',
  `query_balance_url` varchar(2083) NOT NULL COMMENT '查询余额接口地址',
  `min_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最小金额',
  `max_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最大请求金额',
  `weight` int(11) NOT NULL DEFAULT '1' COMMENT '权重',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_daifu_orders_transfer`
--

LOCK TABLES `cm_daifu_orders_transfer` WRITE;
/*!40000 ALTER TABLE `cm_daifu_orders_transfer` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_daifu_orders_transfer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_deposite_card`
--

DROP TABLE IF EXISTS `cm_deposite_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_deposite_card` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1表示可使用状态，0表示禁止状态',
  `bank_id` int(10) NOT NULL DEFAULT '0' COMMENT '银行卡ID',
  `bank_account_username` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡用户名',
  `bank_account_number` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡账号',
  `bank_account_address` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡地址',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'df_bank_id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值卡信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_deposite_card`
--

LOCK TABLES `cm_deposite_card` WRITE;
/*!40000 ALTER TABLE `cm_deposite_card` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_deposite_card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_deposite_orders`
--

DROP TABLE IF EXISTS `cm_deposite_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_deposite_orders` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `p_admin_id` mediumint(8) DEFAULT NULL COMMENT '跑分平台管理员id',
  `trade_no` varchar(255) NOT NULL DEFAULT '' COMMENT '申请充值订单号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0表示正在申请 1成功 2表示失败',
  `bank_id` int(10) NOT NULL DEFAULT '0' COMMENT '银行卡ID',
  `bank_account_username` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡用户名',
  `bank_account_number` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡账号',
  `bank_account_address` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡地址',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `recharge_account` varchar(64) DEFAULT NULL COMMENT '充值账号',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `card_id` int(11) DEFAULT NULL COMMENT '充值银行卡id',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='申请充值信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_deposite_orders`
--

LOCK TABLES `cm_deposite_orders` WRITE;
/*!40000 ALTER TABLE `cm_deposite_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_deposite_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_ewm_block_ip`
--

DROP TABLE IF EXISTS `cm_ewm_block_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_ewm_block_ip` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ms_id` int(11) NOT NULL COMMENT '码商 ',
  `block_visite_ip` varchar(100) NOT NULL COMMENT '拉黑的ip',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_ewm_block_ip`
--

LOCK TABLES `cm_ewm_block_ip` WRITE;
/*!40000 ALTER TABLE `cm_ewm_block_ip` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_ewm_block_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_ewm_order`
--

DROP TABLE IF EXISTS `cm_ewm_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_ewm_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `add_time` int(10) DEFAULT NULL,
  `order_no` varchar(100) DEFAULT NULL COMMENT '订单号',
  `order_price` decimal(10,2) DEFAULT NULL COMMENT '订单价格',
  `status` int(11) DEFAULT '0',
  `gema_userid` int(11) DEFAULT '0' COMMENT '所属用户',
  `qr_image` text,
  `pay_time` int(10) DEFAULT NULL COMMENT '支付时间',
  `code_id` int(10) DEFAULT NULL,
  `order_pay_price` decimal(10,2) DEFAULT NULL COMMENT '实际支付价格',
  `gema_username` varchar(45) DEFAULT NULL COMMENT '个码用户名',
  `note` varchar(45) DEFAULT NULL,
  `out_trade_no` varchar(200) DEFAULT NULL,
  `code_type` int(10) DEFAULT NULL,
  `bonus_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_back` int(1) NOT NULL DEFAULT '0',
  `is_upload_credentials` int(1) NOT NULL DEFAULT '0',
  `credentials` varchar(500) DEFAULT NULL,
  `sure_ip` varchar(45) NOT NULL DEFAULT '0',
  `is_handle` int(1) DEFAULT '0',
  `visite_ip` varchar(32) DEFAULT NULL COMMENT '访问ip',
  `visite_area` varchar(200) DEFAULT NULL COMMENT '访问区域',
  `visite_time` int(11) DEFAULT NULL COMMENT '访问时间',
  `merchant_order_no` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '支付平台下游商户商户订单号',
  `visite_clientos` varchar(50) DEFAULT NULL COMMENT '访问设备',
  `grab_a_single_type` int(11) NOT NULL DEFAULT '1' COMMENT '抢单类型 1 抢单扣余额 2 抢单不扣余额',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `notify_result` text COMMENT '回调返回内容成功为 SUCCESS',
  `visite_token` varchar(255) DEFAULT NULL COMMENT '访问token',
  `notify_url` varchar(255) DEFAULT NULL COMMENT '回调地址',
  `member_id` int(11) DEFAULT NULL COMMENT '支付商户id',
  `pay_username` varchar(255) NOT NULL DEFAULT '' COMMENT '付款人姓名',
  `pay_user_name` varchar(20) NOT NULL COMMENT '商户上报的支付用户名',
  `sure_order_role` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1：用户完成订单  2：管理员完成订单',
  `name_abnormal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '姓名不符合',
  `money_abnormal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '金额不符合',
  `cardKey` varchar(2083) NOT NULL COMMENT '卡密',
  `legality` int(11) NOT NULL DEFAULT '1' COMMENT '订单合法1：合法，其他不合法',
  `ms_callback_ip` varchar(255) NOT NULL COMMENT '码商监控回调ip',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_no` (`order_no`) USING BTREE,
  KEY `search` (`order_no`,`gema_username`,`status`,`add_time`) USING BTREE,
  KEY `gema_userid_index` (`gema_userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_ewm_order`
--

LOCK TABLES `cm_ewm_order` WRITE;
/*!40000 ALTER TABLE `cm_ewm_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_ewm_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_ewm_pay_code`
--

DROP TABLE IF EXISTS `cm_ewm_pay_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_ewm_pay_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ms_id` int(11) NOT NULL DEFAULT '0' COMMENT '属于哪个用户',
  `status` int(1) DEFAULT '0' COMMENT '是否正常使用　０表示正常，１表示禁用',
  `account_name` varchar(50) NOT NULL DEFAULT '' COMMENT '收款账户',
  `bank_name` varchar(2083) NOT NULL DEFAULT '' COMMENT '开户行',
  `account_number` varchar(255) NOT NULL DEFAULT '' COMMENT '收款号码',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `user_name` varchar(45) NOT NULL DEFAULT '' COMMENT '用户名',
  `bonus_points` tinyint(3) NOT NULL DEFAULT '0' COMMENT '提成1000分之一',
  `success_order_num` int(10) NOT NULL DEFAULT '0' COMMENT '支付成功笔数',
  `updated_at` int(11) NOT NULL COMMENT '最后更新时间',
  `is_lock` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否锁定',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `forbidden_reason` varchar(32) DEFAULT NULL COMMENT '禁用原因',
  `order_today_all` smallint(5) NOT NULL DEFAULT '0' COMMENT '今日单量',
  `failed_order_num` smallint(5) NOT NULL DEFAULT '0' COMMENT '二维码失败次数',
  `code_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '二维码类型',
  `image_url` varchar(520) NOT NULL COMMENT '图片地址',
  `limit__total` decimal(10,2) NOT NULL,
  `bank_four_code` int(11) NOT NULL COMMENT '银行卡后四位',
  `min_money` decimal(10,2) NOT NULL COMMENT '最小请求金额',
  `max_money` decimal(10,2) NOT NULL COMMENT '最大请求金额',
  `deleteTime` int(11) NOT NULL COMMENT '删除时间',
  `balance` decimal(10,2) NOT NULL COMMENT '余额',
  `expiration_time` int(11) NOT NULL COMMENT '失效时间',
  `pay_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单笔收款金额',
  `api_pay_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '请求金额',
  `today_receiving_number` int(11) NOT NULL DEFAULT '0' COMMENT '今日成功总数',
  `today_receiving_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '今日成功金额',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='码商二维码表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_ewm_pay_code`
--

LOCK TABLES `cm_ewm_pay_code` WRITE;
/*!40000 ALTER TABLE `cm_ewm_pay_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_ewm_pay_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_gemapay_code`
--

DROP TABLE IF EXISTS `cm_gemapay_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_gemapay_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '属于哪个用户',
  `type` int(1) DEFAULT NULL COMMENT '1表示微信，２表示支付宝，３表示云散付，４表示百付通',
  `qr_image` varchar(255) DEFAULT NULL COMMENT '二维码地址',
  `last_used_time` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0' COMMENT '是否正常使用　０表示正常，１表示禁用',
  `last_online_time` int(11) DEFAULT NULL COMMENT '最后一次在线的时间',
  `pay_status` int(11) DEFAULT NULL COMMENT '０表示未使用，１表示使用占用中',
  `limit_money` decimal(10,2) DEFAULT NULL,
  `paying_num` int(10) DEFAULT NULL COMMENT '正在支付的数量',
  `user_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_gemapay_code`
--

LOCK TABLES `cm_gemapay_code` WRITE;
/*!40000 ALTER TABLE `cm_gemapay_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_gemapay_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_jdek_sale`
--

DROP TABLE IF EXISTS `cm_jdek_sale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_jdek_sale` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(10) NOT NULL,
  `merchantId` varchar(255) NOT NULL,
  `signkey` varchar(255) NOT NULL,
  `encryptionkey` varchar(255) NOT NULL,
  `status` tinyint(10) NOT NULL DEFAULT '0',
  `ms_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_jdek_sale`
--

LOCK TABLES `cm_jdek_sale` WRITE;
/*!40000 ALTER TABLE `cm_jdek_sale` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_jdek_sale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_jobs`
--

DROP TABLE IF EXISTS `cm_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_jobs` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_jobs`
--

LOCK TABLES `cm_jobs` WRITE;
/*!40000 ALTER TABLE `cm_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_menu`
--

DROP TABLE IF EXISTS `cm_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_menu` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(100) NOT NULL DEFAULT '100' COMMENT '排序',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `module` char(20) NOT NULL DEFAULT '' COMMENT '模块',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `is_hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `icon` char(30) NOT NULL DEFAULT '' COMMENT '图标',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=328 DEFAULT CHARSET=utf8 COMMENT='基本菜单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_menu`
--

LOCK TABLES `cm_menu` WRITE;
/*!40000 ALTER TABLE `cm_menu` DISABLE KEYS */;
INSERT INTO `cm_menu` VALUES (1,0,100,'控制台','admin','/',0,'console',1,1544365211,1539583897),(2,0,100,'系统设置','admin','Site',0,'set',1,1663831801,1539583897),(3,2,100,'基本设置','admin','Site',0,'set-fill',1,1663831811,1539583897),(4,3,100,'网站设置','admin','Site/website',0,'',1,1663831847,1539583897),(5,3,100,'邮件服务','admin','Site/email',0,'',1,1663831823,1539583897),(6,3,100,'行为日志','admin','Log/index',0,'flag',1,1540563678,1540563678),(7,6,100,'获取日志列表','admin','Log/getList',1,'',1,1540566783,1540566783),(8,6,100,'删除日志','admin','Log/logDel',1,'',1,1540566819,1540566819),(9,6,100,'清空日志','admin','Log/logClean',1,'',1,1540566849,1540566849),(10,2,100,'权限设置','admin','Admin',0,'set-sm',1,1539584897,1539583897),(11,10,100,'管理员设置','admin','Admin/index',0,'',1,1539584897,1539583897),(12,11,100,'获取管理员列表','admin','Admin/userList',1,'user',1,1540485169,1540484869),(13,11,100,'新增管理员','admin','Admin/userAdd',1,'user',1,1540485182,1540485125),(14,11,100,'编辑管理员','admin','Admin/userEdit',1,'user',1,1540485199,1540485155),(15,11,100,'删除管理员','admin','AdminuserDel',1,'user',1,1540485310,1540485310),(16,10,100,'角色管理','admin','Admin/group',0,'',1,1539584897,1539583897),(17,16,100,'获取角色列表','admin','Admin/groupList',1,'',1,1540485432,1540485432),(18,16,100,'新增权限组','admin','Admin/groupAdd',1,'',1,1540485531,1540485488),(19,16,100,'编辑权限组','admin','Admin/groupEdit',1,'',1,1540485515,1540485515),(20,16,100,'删除权限组','admin','Admin/groupDel',1,'',1,1540485570,1540485570),(21,10,100,'菜单管理','admin','Menu/index',0,'',1,1539584897,1539583897),(22,21,100,'获取菜单列表','admin','Menu/getList',1,'',1,1540485652,1540485632),(23,21,100,'新增菜单','admin','Menu/menuAdd',1,'',1,1540534094,1540534094),(24,21,100,'编辑菜单','admin','Menu/menuEdit',1,'',1,1540534133,1540534133),(25,21,100,'删除菜单','admin','Menu/menuDel',1,'',1,1540534133,1540534133),(26,2,100,'我的设置','admin','Admin/profile',0,'',1,1540486245,1539583897),(27,26,100,'基本资料','admin','Site/profile',0,'',1,1663831867,1539583897),(28,26,100,'修改密码','admin','Site/changePwd',0,'',1,1663831887,1539583897),(29,0,100,'支付设置','admin','Pay',0,'senior',1,1540483267,1539583897),(30,29,100,'支付产品','admin','Pay/index',0,'',1,1539584897,1539583897),(31,30,100,'获取支付产品列表','admin','Pay/getCodeList',1,'',1,1545461560,1545458869),(32,30,100,'新增支付产品','admin','Pay/addCode',1,'',1,1545461705,1545458888),(33,30,100,'编辑支付产品','admin','Pay/editCode',1,'',1,1545461713,1545458915),(34,30,100,'删除产品','admin','Pay/delCode',1,'',1,1545461745,1545458935),(35,29,100,'支付渠道','admin','Pay/channel',0,'',1,1539584897,1539583897),(36,35,100,'获取渠道列表','admin','Pay/getChannelList',1,'',1,1545461798,1545458953),(37,35,100,'新增渠道','admin','Pay/addChannel',1,'',1,1545461856,1545458977),(38,35,100,'编辑渠道','admin','Pay/editChannel',1,'',1,1545461863,1545458992),(39,35,100,'删除渠道','admin','Pay/delChannel',1,'',1,1545461870,1545459004),(40,29,100,'渠道账户','admin','Pay/account',0,'',-1,1667587295,1545459058),(41,40,100,'获取渠道账户列表','admin','Pay/getAccountList',1,'',-1,1667587657,1545459152),(42,40,100,'新增账户','admin','Pay/addAccount',1,'',-1,1667587652,1545459180),(43,40,100,'编辑账户','admin','Pay/editAccount',1,'',-1,1667587650,1545459194),(44,40,100,'删除账户','admin','Pay/delAccount',1,'',-1,1667587643,1545459205),(45,29,100,'银行管理','admin','Pay/bank',0,'',1,1540822566,1540822549),(46,45,100,'获取银行列表','admin','Pay/getBankList',1,'',1,1545462167,1545459107),(47,45,100,'新增银行','admin','Pay/addBank',1,'',1,1545462178,1545459243),(48,45,100,'编辑银行','admin','Pay/editBank',1,'',1,1545462220,1545459262),(49,45,100,'删除银行','admin','Pay/delBank',1,'',1,1545462230,1545459277),(50,0,100,'内容管理','admin','Article',0,'template',-1,1666788045,1539583897),(51,50,100,'文章管理','admin','Article/index',0,'',-1,1666788037,1539583897),(52,51,100,'获取文章列表','admin','Article/getList',1,'lis',-1,1667587272,1540484939),(53,51,100,'新增文章','admin','Article/add',1,'',-1,1667587269,1540486058),(54,51,100,'编辑文章','admin','Article/edit',1,'',-1,1667587263,1540486097),(55,51,100,'删除文章','admin','Article/del',1,'',-1,1667587265,1545459431),(56,50,100,'公告管理','admin','Article/notice',0,'',-1,1666788041,1539583897),(57,56,100,'获取公告列表','admin','Article/getNoticeList',1,'',-1,1667587260,1545459334),(58,56,100,'新增公告','admin','Article/addNotice',1,'',-1,1667587257,1545459346),(59,56,100,'编辑公告','admin','Article/editNotice',1,'',-1,1667587254,1545459368),(60,56,100,'删除公告','admin','Article/delNotice',1,'',-1,1667587251,1545459385),(61,0,100,'商户管理','admin','User',0,'user',1,1539584897,1539583897),(62,61,100,'商户列表','admin','User/index',0,'',1,1539584897,1539583897),(63,62,100,'获取商户列表','admin','User/getList',1,'',1,1540486400,1540486400),(64,62,100,'新增商户','admin','User/add',1,'',1,1540533973,1540533973),(65,62,100,'商户修改','admin','User/edit',1,'',1,1540533993,1540533993),(66,62,100,'删除商户','admin','User/del',1,'',1,1545462902,1545459408),(67,61,100,'提现记录','admin','Balance/paid',0,'',1,1539584897,1539583897),(68,67,100,'获取提现记录','admin','Balance/paidList',1,'',1,1545462677,1545458822),(69,67,100,'提现编辑','admin','Balance/editPaid',1,'',1,1545462708,1545458822),(70,67,100,'提现删除','admin','Balance/delPaid',1,'',1,1545462715,1545458822),(71,61,100,'商户账户','admin','Account/index',0,'',-1,1667587320,1539583897),(80,71,100,'商户账户列表','admin','Account/getList',1,'',-1,1667587638,1545459501),(81,71,100,'新增商户账户','admin','Account/add',1,'',-1,1667587636,1545459501),(82,71,100,'编辑商户账户','admin','Account/edit',1,'',-1,1667587633,1545459501),(83,71,100,'删除商户账户','admin','Account/del',1,'',-1,1667587631,1545459501),(84,61,100,'商户资金','admin','Balance/index',0,'',1,1539584897,1539583897),(85,84,100,'商户资金列表','admin','Balance/getList',1,'',1,1545462951,1545459501),(86,84,100,'商户资金明细','admin','Balance/details',1,'',1,1545462997,1545459501),(87,84,100,'获取商户资金明细','admin','Balance/getDetails',1,'',1,1545462997,1545459501),(88,61,100,'商户API','admin','Api/index',0,'',1,1539584897,1539583897),(89,87,100,'商户API列表','admin','Api/getList',1,'',1,1545463054,1545459501),(90,87,100,'编辑商户API','admin','Api/edit',1,'',1,1545463065,1545459501),(91,61,100,'商户认证','admin','User/auth',0,'',-1,1667587801,1542882201),(92,90,100,'商户认证列表','admin','getlist',1,'',1,1545459501,1545459501),(93,90,100,'编辑商户认证','admin','getlist',1,'',1,1545459501,1545459501),(94,0,100,'订单管理','admin','Orders',0,'form',1,1539584897,1539583897),(95,94,100,'交易列表','admin','Orders/index',0,'',1,1539584897,1539583897),(96,95,100,'获取交易列表','admin','Orders/getList',1,'',1,1545463214,1539583897),(97,94,100,'交易详情','admin','Orders/details',1,'',1,1545463268,1545459549),(98,94,100,'退款列表','admin','Orders/refund',0,'',-1,1667587969,1539583897),(99,61,100,'商户订单统计','admin','Orders/user',0,'',1,1669739646,1539583897),(100,99,100,'获取商户统计','admin','Orders/userList',1,'',1,1539584897,1539583897),(101,94,100,'渠道统计','admin','Orders/channel',0,'',-1,1667589056,1539583897),(102,101,100,'获取渠道统计','admin','Orders/channelList',1,'',-1,1667589114,1539583897),(103,61,100,'商户统计','admin','User/cal',0,'',1,1667587837,1581872080),(104,61,100,'商户资金记录','admin','Balance/change',0,'',-1,1667587808,1583999358),(105,0,100,'代付管理','admin','DaifuOrders',0,'form',1,1667587616,1581082458),(111,105,100,'订单列表','admin','DaifuOrders/index',0,'',1,1581082501,1581082501),(113,105,100,'充值银行卡','admin','DaifuOrders/depositecard',0,'',-1,1667587602,1585315597),(114,105,100,'充值列表','admin','deposite_order/index',0,'',-1,1667587605,1585329451),(115,94,100,'渠道资金','admin','Channel/fundIndex',0,'',-1,1667589059,1587199882),(116,2,100,'代付设置','admin','daifu_orders/setting',0,'',1,1588083379,1588083251),(117,0,100,'码商管理','admin','Ms',0,'senior',1,1540483267,1539583897),(118,117,100,'码商列表','admin','Ms/index',0,'',1,1539584897,1539583897),(121,219,100,'卡转卡','admin','Ms/payCodes',0,'',1,1673105612,0),(125,220,100,'卡卡订单','admin','Ms/orders',0,'',1,1673105714,1539584897),(126,117,100,'码商流水','admin','Ms/bills',0,'',-1,1667588043,1539584897),(127,62,100,'商户余额','admin','balance/changeList',1,'',1,1646069652,1646069652),(128,117,100,'码商列表2','admin','ms/getmslist',1,'',1,1646069778,1646069778),(129,117,100,'获取二维码了表','admin','ms/getPaycodesLists',1,'',1,1646069908,1646069908),(130,117,100,'获取订单列表','admin','ms/getOrdersList',1,'',1,1646069976,1646069976),(131,117,100,'获取码商流水','admin','ms/getBillsList',1,'',1,1646070033,1646070033),(132,67,100,'提现详情','admin','balance/details_tixian',1,'',1,1646070236,1646070236),(133,67,100,'处理提现','admin','balance/deal',1,'',1,1646070403,1646070403),(134,117,100,'编辑码商','admin','ms/edit',1,'',1,1646070586,1646070586),(135,2,100,'确认命令','admin','api/checkOpCommand',0,'',1,1658660543,1646070809),(136,117,100,'确认订单','admin','ms/issueOrder',1,'',1,1646070895,1646070895),(137,94,100,'补单','admin','orders/budanDetails',1,'',1,1646071307,1646071307),(138,94,100,'补单发送','admin','orders/update',1,'',1,1646071417,1646071417),(139,94,100,'补发通知','admin','orders/subnotify',1,'',1,1646071466,1646071466),(140,117,100,'操作流水','admin','ms/changeBalance',1,'',1,1646136901,1646136901),(141,62,100,'商户列表2','admin','user/getList',1,'',1,1646137050,1646137050),(142,62,100,'增减商户资金','admin','balance/changeBalance',1,'',1,1646148840,1646148840),(143,117,100,'异常订单','admin','Ms/abnormalOrders',0,'',-1,1667588104,1657521636),(144,105,100,'获取代付订单列表','admin','DaifuOrders/getOrdersList',1,'',1,1660748092,1660747004),(145,105,100,'代付订单导出','admin','DaifuOrders/exportOrder',1,'',1,1661027103,1661027103),(146,94,100,'导出订单列表','admin','Orders/exportOrder',1,'',1,1661027277,1661027277),(147,61,100,'导出商户资金列表','admin','Balance/exportBalance',1,'',1,1661156354,1661027453),(148,117,100,'导出码商订单','admin','ms/exportOrder',1,'',1,1661155160,1661155160),(149,67,100,'导出提现记录','admin','Balance/exportBalanceCash',1,'',1,1661351933,1661156604),(150,84,100,'导出商户资金','admin','balance/exportBalanceChange',1,'',1,1661157427,1661157427),(151,117,100,'导出码商流水','admin','ms/exportMsBills',1,'',1,1662285094,1662285094),(152,0,100,'卡转卡','admin','kzk',0,'rmb',-1,1673106776,1667589143),(153,0,100,'支付宝UID','admin','alipayUid',0,'rmb',-1,1673106773,1667630719),(155,219,100,'支付宝UID列表','admin','Ms/uidList',0,'',1,1673105691,1667630938),(156,220,100,'支付宝UID订单','admin','Ms/uidOrder',0,'',1,1673105724,1667631244),(157,117,100,'添加码商','admin','Ms/add',1,'',1,1667651170,1667651170),(158,156,100,'获取支付宝UID订单','admin','Ms/getuidOrdersList',1,'',1,1667654546,1667654546),(159,155,100,'获取支付宝UID列表','admin','Ms/getuidLists',1,'',1,1667654622,1667654622),(160,103,100,'获取商户总统计','admin','User/calList',1,'',1,1667657168,1667657168),(166,117,100,'删除码商','admin','Ms/del',1,'',1,1667755769,1667755769),(167,117,100,'设置码商接单状态','admin','Ms/editMsJdStatus',1,'',1,1667755991,1667755991),(168,117,100,'码商费率设置','admin','Ms/assign_channels',1,'',1,1667756073,1667756073),(169,117,100,'码商流水管理','admin','Ms/bills',0,'',1,1675489737,1667756148),(170,117,100,'设置码商权重','admin','Ms/editMsWeight',1,'',1,1667756199,1667756199),(171,117,100,'配置码商白名单','admin','Ms/changeWhiteIp',1,'',1,1667756269,1667756269),(172,117,100,'白名单校验口令','admin','api/checkOpCommand',1,'',1,1667756515,1667756515),(173,61,100,'标记商户异常','admin','User/mark_abnormal',1,'',1,1667756669,1667756669),(174,61,100,'商户通道管理','admin','User/codes',1,'',1,1667756710,1667756710),(175,61,100,'商户分润设置','admin','User/profit',1,'',1,1667756760,1667756760),(176,61,100,'商户代付分成设置','admin','User/daifuProfit',1,'',1,1667756814,1667756814),(177,61,100,'重置密钥','admin','Api/resetKey',1,'',1,1667757548,1667757548),(178,117,100,'删除码商收款账号','admin','Ms/delPayCode',1,'',1,1667757757,1667757757),(179,117,100,'拉黑码商订单ip','admin','Ms/blockIp',1,'',1,1667757857,1667757857),(180,117,100,'码商订单退款','admin','Ms/refundOrder',1,'',1,1667757901,1667757901),(181,117,100,'订单姓名不符','admin','Ms/abnormalOrderSave',1,'',1,1667757955,1667757955),(182,1,100,'绑定谷歌验证器','admin','admin/blndGoogle',1,'',1,1667836671,1667836671),(183,11,100,'授权权限','admin','Admin/userAuth',1,'',1,1667979516,1667979359),(184,111,100,'订单指定码商','admin','DaifuOrders/appoint_ms',1,'',1,1667990975,1667990975),(185,111,100,'强制补单','admin','DaifuOrders/auditSuccess',1,'',1,1667991108,1667991108),(186,111,100,'关闭代付订单','admin','DaifuOrders/auditError',1,'',1,1667991229,1667991229),(187,111,100,'搜索代付订单','admin','DaifuOrders/searchOrderMoney',1,'',1,1668009119,1668009119),(188,156,100,'码商订单补发通知','admin','Ms/subnotify',1,'',1,1668328588,1668328588),(189,156,100,'码商订单强制补单','admin','Ms/issueOrder',1,'',1,1668328723,1668328723),(190,156,100,'订单统计栏搜索联动','admin','Ms/searchMsOrderMoney',1,'',1,1668418806,1668418806),(191,67,100,'处理提现a','admin','balance/getAuditSwitch',1,'',1,1668527015,1668527015),(192,67,100,'处理成功','admin','balance/deal',1,'',1,1668527154,1668527154),(193,221,100,'支付宝UID商户报表','admin','Ms/uidshstatic',0,'rmb',1,1673106093,1668536532),(194,193,100,'获取uid商户报表','admin','Ms/getuidshstatic',1,'',1,1668536593,1668536593),(195,62,100,'清空google权限','admin','user/clearGoogleAuth',1,'',1,1668543842,1668543842),(196,155,100,'码子数量统计','admin','Ms/uidCodeListCount',1,'',1,1668604528,1668604528),(197,121,100,'卡转卡码子统计','admin','Ms/kzkCodeListCount',1,'',1,1668604571,1668604571),(198,221,100,'UID收款帐户统计','admin','Ms/uidStatic',0,'',1,1673106121,1668622148),(199,221,100,'卡转卡收款帐户统计','admin','Ms/kzkStatic',0,'',1,1673106081,1668622197),(200,199,100,'获取收款帐户统计','admin','Ms/get_ewm_static',1,'',1,1668622280,1668622280),(201,0,100,'网站报表','admin','baobiao',0,'',1,1668708555,1668708513),(202,201,100,'网站统计','admin','Admin/getUserListStat',1,'',1,1668709044,1668708585),(203,201,100,'网站列表','admin','Admin/userListStat',0,'',1,1668708814,1668708814),(204,67,100,'处理提现请求','admin','balance/handle',1,'',1,1668788375,1668788375),(205,62,100,'查看密钥','admin','user/view_secret',1,'',1,1668790344,1668790344),(206,111,100,'代付补发通知','admin','DaifuOrders/add_notify',1,'',1,1668835448,1668835448),(207,67,100,'处理体现获取通道','admin','balance/select_channel',1,'',1,1668844902,1668838908),(208,67,100,'驳回提现','admin','Balance/rebut',1,'',1,1668845068,1668845068),(209,62,100,'商户资金导出','admin','Balance/exportBalanceChange',1,'',1,1668852110,1668852110),(210,84,100,'账户资金统计联动','admin','Balance/searchBalanceCal',1,'',1,1668853348,1668853348),(211,117,100,'重置码商google','admin','ms/clearGoogleAuth',1,'',1,1668856710,1668856710),(212,1,100,'测试收银台','admin','Index/testpay',1,'',1,1668858384,1668858384),(213,155,100,'测试UID','admin','Ms/testuid',1,'',1,1668858442,1668858442),(214,155,100,'uid测试支付','admin','Ms/testuidpay',1,'',1,1668858472,1668858472),(215,155,100,'修改码子状态','admin','Ms/disactiveCode',1,'',1,1668858510,1668858510),(216,62,100,'重置密钥2','admin','admin/api/resetKey',1,'',1,1668953631,1668953631),(217,105,100,'代付统计','admin','DaifuOrders/userStats',0,'',1,1669569410,1669568859),(218,217,100,'获取用户代付统计','admin','DaifuOrders/getUserStats',1,'',1,1669568918,1669568918),(219,0,100,'通道管理','admin','ms',0,'diamond',1,1673105530,1673105530),(220,0,100,'通道订单','admin','Ms',0,'rmb',1,1673105556,1673105556),(221,0,100,'通道统计','admin','ms',0,'template-1',1,1673106065,1673105760),(222,219,100,'抖音红包列表','admin','Ms/douyinGroupRedList',0,'',1,1673106324,1673106324),(223,222,100,'获取抖音群红包列表','admin','Ms/getdouyinGroupRedLists',1,'',1,1673106345,1673106345),(224,219,100,'微信群红包列表','admin','Ms/wechatGroupRedList',0,'',1,1673106363,1673106363),(225,224,100,'获取微信群红包列表','admin','Ms/getwechatGroupRedList',1,'',1,1673106381,1673106381),(226,219,100,'支付宝扫码列表','admin','Ms/alipaycodeList',0,'',1,1673106398,1673106398),(227,226,100,'获取支付宝列表','admin','Ms/getalipaycodeLists',1,'',1,1673106419,1673106419),(228,219,100,'微信扫码列表','admin','Ms/wechatCodeList',0,'',1,1673106440,1673106440),(229,228,100,'获取微信扫码列表','admin','Ms/getwechatCodeLists',1,'',1,1673106538,1673106538),(230,219,100,'数字人民币列表','admin','Ms/CnyNumberList',0,'',1,1673106558,1673106558),(231,230,100,'获取数字人民币列表','admin','Ms/getCnyNumberLists',1,'',1,1673106583,1673106583),(232,219,100,'京东E卡列表','admin','Ms/JDECardList',0,'',1,1673106629,1673106629),(233,232,100,'获取京东E卡列表','admin','Ms/getJDECardLists',1,'',1,1673106650,1673106650),(234,219,100,'QQ面对面红包列表','admin','Ms/QQFaceRedList',0,'',1,1673106671,1673106671),(235,219,100,'获取QQ面对面红包列表','admin','Ms/getQQFaceRedLists',1,'',1,1673106688,1673106688),(236,219,100,'小程序商品码列表','admin','Ms/AppletProductsList',0,'',1,1673106706,1673106706),(237,236,100,'获取小程序商品码列表','admin','Ms/getAppletProductsLists',1,'',1,1673106723,1673106723),(238,220,100,'抖音红包订单','admin','Ms/douyinGroupRedOrder',0,'',1,1673106792,1673106792),(239,238,100,'获取抖音红包订单','admin','Ms/getdouyinGroupRedOrdersList',1,'',1,1673106809,1673106809),(240,220,100,'微信群红包订单','admin','Ms/wechatGroupRedOrder',0,'',1,1673106829,1673106829),(241,240,100,'获取微信群红包订单','admin','Ms/getwechatGroupRedOrdersList',1,'',1,1673106847,1673106847),(242,220,100,'支付宝扫码订单','admin','Ms/alipayCodeOrder',0,'',1,1673106892,1673106892),(243,242,100,'获取支付宝扫码订单','admin','Ms/getalipayCodeOrdersList',1,'',1,1673106911,1673106911),(244,220,100,'微信扫码订单','admin','Ms/wechatCodeOrder',0,'',1,1673106929,1673106929),(245,244,100,'获取微信扫码订单','admin','Ms/getwechatCodeOrdersList',1,'',1,1673106949,1673106949),(246,220,100,'数字人民币订单','admin','Ms/CnyNumberOrder',0,'',1,1673106964,1673106964),(247,246,100,'获取数字人民币订单','admin','Ms/getCnyNumberOrdersList',1,'',1,1673106981,1673106981),(248,220,100,'京东E卡订单','admin','Ms/JDECardOrder',0,'',1,1673106997,1673106997),(249,248,100,'获取京东E卡订单','admin','Ms/getJDECardOrdersList',1,'',1,1673107020,1673107020),(250,220,100,'QQ面对面红包订单','admin','Ms/QQFaceRedOrder',0,'',1,1673107038,1673107038),(251,250,100,'获取qq面对面红包订单','admin','Ms/getAQQFaceRedOrdersList',1,'',1,1673107068,1673107068),(252,220,100,'小程序商品码订单','admin','Ms/AppletProductsOrder',0,'',1,1673107085,1673107085),(253,252,100,'获取小程序商品码订单','admin','Ms/getAppletProductsOrdersList',1,'',1,1673107104,1673107104),(254,30,100,'支付产品配置','admin','pay/sys_paycode',1,'',1,1673107272,1673107272),(255,226,100,'支付宝扫码搜索统计','admin','Ms/alipayCodeListCount',1,'',1,1673942121,1673942121),(256,242,100,'支付宝扫码订单搜索统计','admin','Ms/searchMsAlipayCodeOrderMoney',1,'',1,1673942164,1673942164),(257,219,100,'淘宝现金红包列表','admin','Ms/taoBaoMoneyRedList',0,'',1,1674894686,1674894686),(258,257,100,'获取淘宝现金红包列表','admin','Ms/gettaoBaoMoneyRedLists',1,'',1,1674894719,1674894719),(259,220,100,'淘宝现金红包订单','admin','Ms/taoBaoMoneyRedOrder',0,'',1,1674894752,1674894752),(260,259,100,'获取淘宝现金红包订单','admin','Ms/gettaoBaoMoneyRedOrdersList',1,'',1,1674894792,1674894772),(261,259,100,'淘宝现金红包订单搜索统计','admin','Ms/searchMstaoBaoMoneyRedOrderMoney',1,'',1,1674894811,1674894811),(262,219,100,'支付宝转账码列表','admin','Ms/alipayTransferCodeList',0,'',1,1674894956,1674894956),(263,262,100,'获取支付宝转账码列表','admin','Ms/getalipayTransferCodeLists',1,'',1,1674894975,1674894975),(264,262,100,'支付宝转账码列表搜索统计','admin','Ms/alipayTransferCodeListCount',1,'',1,1674895001,1674895001),(265,220,100,'支付宝转账码订单','admin','Ms/alipayTransferCodeOrder',0,'',1,1674895027,1674895027),(266,265,100,'获取支付宝转账码订单','admin','Ms/getalipayTransferCodeOrdersList',1,'',1,1674895046,1674895046),(267,265,100,'支付宝转账码订单搜索统计','admin','Ms/searchMsalipayTransferCodeOrderMoney',1,'',1,1674895067,1674895067),(268,219,100,'支付宝小荷包列表','admin','Ms/alipaySmallPurseList',0,'',1,1675148062,1675148062),(269,268,100,'获取支付宝小荷包列表','admin','Ms/getalipaySmallPurseLists',1,'',1,1675148088,1675148088),(270,220,100,'支付宝小荷包订单','admin','Ms/alipaySmallPurseOrder',0,'',1,1675148124,1675148124),(271,270,100,'获取支付宝小荷包订单','admin','Ms/getalipaySmallPurseOrdersList',1,'',1,1675148153,1675148153),(272,105,100,'中转管理','admin','DaifuOrders/TransferList',0,'',1,1675240748,1675240748),(273,272,100,'获取中转平台列表','admin','DaifuOrders/getTransferList',1,'',1,1675240781,1675240781),(274,272,100,'编辑中转平台信息','admin','DaifuOrders/editDaifuTransfer',1,'',1,1675240811,1675240811),(275,272,100,'添加代付中转','admin','DaifuOrders/addtransferlist',1,'',1,1675240833,1675240833),(276,272,100,'获取中转列表','admin','DaifuOrders/getTransferDfChannel',1,'',1,1675241653,1675241653),(277,272,100,'提交中转','admin','DaifuOrders/dfTransfer',1,'',1,1675241721,1675241721),(278,117,100,'一键停工','admin','Ms/closeMsWork',1,'',1,1675245411,1675245411),(279,248,100,'E卡订单页统计','admin','Ms/searchMsJDECardOrderMoney',1,'',1,1675344200,1675344200),(280,246,100,'数字人民币订单页统计','admin','Ms/searchMsCnyNumberOrderMoney',1,'',1,1675344247,1675344247),(281,238,100,'抖音红包订单搜索统计','admin','Ms/searchMsDouyinGroupRedOrderMoney',1,'',1,1675348741,1675348741),(282,240,100,'微信群红包订单搜索统计','admin','Ms/searchMsWechatGroupRedOrderMoney',1,'',1,1675348775,1675348775),(283,248,100,'京东E卡订单搜索统计','admin','Ms/searchMsJDECardOrderMoney',1,'',1,1675348813,1675348813),(284,230,100,'数字人民币订单搜索统计','admin','Ms/searchMsCnyNumberOrderMoney',1,'',1,1675348849,1675348849),(285,250,100,'QQ面对面红包订单搜索统计','admin','Ms/searchMsQQFaceRedOrderMoney',1,'',1,1675348913,1675348913),(286,228,100,'微信扫码列表搜索统计','admin','Ms/wechatCodeListCount',1,'',1,1675348988,1675348988),(287,230,100,'数字人民币列表搜索统计','admin','ms/CnyNumberListCount',1,'',1,1675349012,1675349012),(288,232,100,'京东E卡列表搜索统计','admin','Ms/JDECardListCount',1,'',1,1675349043,1675349043),(289,234,100,'QQ面对面红包列表搜索统计','admin','Ms/QQFaceRedListCount',1,'',1,1675349064,1675349064),(290,257,100,'淘宝现金红包搜索统计','admin','Ms/taoBaoMoneyRedListCount',1,'',1,1675349083,1675349083),(291,268,100,'支付宝小荷包列表搜索统计','admin','Ms/alipaySmallPurseListCount',1,'',1,1675349101,1675349101),(292,222,100,'抖音红包搜索统计','admin','Ms/douyinGroupRedListCount',1,'',1,1675349135,1675349135),(293,224,100,'微信群红包列表搜索统计','admin','Ms/wechatGroupRedListCount',1,'',1,1675349153,1675349153),(294,169,100,'码商流水搜索联动','admin','ms/searchBalanceCal',1,'',1,1675531272,1675531272),(295,117,100,'码商统计','admin','Ms/stats',0,'',1,1675532397,1675532397),(296,295,100,'获取码商统计','admin','Ms/getMsStats',1,'',1,1675532422,1675532422),(297,272,100,'开启自动中转','admin','DaifuOrders/editAutoTransferStatus',1,'',1,1675677277,1675677277),(298,1,100,'管理余额校验','admin','Admin/adminBalance',1,'',1,1675773034,1675773034),(299,3,100,'系统配置','admin','Site/sys_config',0,'',1,1675787630,1675787630),(300,1,100,'设置口令','admin','admin/setCommand',1,'',1,1675873467,1675873467),(301,1,100,'管理密令','admin','admin/adminCommand',1,'',1,1675873512,1675873512),(302,219,100,'支付宝工作证列表','admin','Ms/alipayWorkCardList',0,'',1,1675954424,1675954424),(303,302,100,'获取支付宝工作证列表','admin','Ms/getalipayWorkCardLists',1,'',1,1675954485,1675954485),(304,302,100,'支付宝工作证列表搜索统计','admin','ms/alipayWorkCardListCount',1,'',1,1675954552,1675954552),(305,270,100,'支付宝小荷包订单搜索统计','admin','Ms/searchMsalipaySmallPurseOrderMoney',1,'',1,1675954700,1675954700),(306,219,100,'仟信好友转账列表','admin','Ms/QianxinTransferList',0,'',1,1675955477,1675955477),(307,306,100,'获取仟信好友转账列表','admin','Ms/getQianxinTransferLists',1,'',1,1675955527,1675955527),(308,306,100,'仟信好友转账列表搜索统计','admin','Ms/QianxinTransferListCount',1,'',1,1675955576,1675955576),(309,220,100,'支付宝工作证订单','admin','Ms/alipayWorkCardOrder',0,'',1,1675955629,1675955629),(310,309,100,'获取支付宝工作证订单列表','admin','Ms/getalipayWorkCardOrdersList',1,'',1,1675955685,1675955685),(311,309,100,'支付宝工作证订单搜索统计','admin','Ms/searchMsalipayWorkCardOrderMoney',1,'',1,1675955755,1675955755),(312,220,100,'仟信好友转账订单','admin','Ms/QianxinTransferOrder',0,'',1,1675955795,1675955795),(313,312,100,'获取仟信好友转账订单','admin','Ms/getQianxinTransferOrdersList',1,'',1,1675955847,1675955847),(314,312,100,'仟信好友转账订单搜索统计','admin','Ms/searchMsQianxinTransferOrderMoney',1,'',1,1675955889,1675955889),(315,219,100,'支付宝口令红包列表','admin','Ms/alipayPassRedList',0,'',1,1676011883,1676011883),(316,315,100,'获取支付宝口令红包列表','admin','Ms/getalipayPassRedLists',1,'',1,1676011926,1676011926),(317,315,100,'支付宝口令红包列表搜索统计','admin','Ms/alipayPassRedListCount',1,'',1,1676011963,1676011963),(318,220,100,'支付宝口令红包订单','admin','Ms/alipayPassRedOrder',0,'',1,1676012114,1676012114),(319,318,100,'获取支付宝口令红包订单','admin','Ms/getalipayPassRedOrdersList',1,'',1,1676013643,1676012146),(320,318,100,'支付宝口令红包订单搜索统计','admin','Ms/searchMsalipayPassRedOrderMoney',1,'',1,1676013656,1676012182),(321,272,100,'代付查询余额','admin','DaifuOrders/query_balance',1,'',1,1676095958,1676095958),(322,0,100,'余额管理','admin','admin',0,'',1,1676871414,1676871414),(323,322,100,'USDT充值','admin','Admin/usdtRecharge',0,'',1,1676871437,1676871437),(324,323,100,'获取充值列表','admin','Admin/getRechargeList',1,'',1,1676871459,1676871459),(325,322,100,'充值USDT','admin','Admin/rechargeAdd',1,'',1,1676871479,1676871479),(326,322,100,'余额明细','admin','Admin/adminBill',0,'',1,1676871497,1676871497),(327,326,100,'获取余额明细','admin','admin/getBillList',1,'',1,1676871519,1676871519);
/*!40000 ALTER TABLE `cm_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_ms`
--

DROP TABLE IF EXISTS `cm_ms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_ms` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '上级ID',
  `account` varchar(255) NOT NULL DEFAULT '0' COMMENT '用户账号',
  `mobile` char(20) NOT NULL COMMENT '用户手机号',
  `u_yqm` varchar(225) NOT NULL COMMENT '邀请码',
  `username` varchar(255) NOT NULL DEFAULT '',
  `login_pwd` varchar(225) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `login_salt` char(5) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户余额',
  `reg_date` int(11) NOT NULL COMMENT '注册时间',
  `reg_ip` varchar(20) NOT NULL COMMENT '注册IP',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户锁定  1 不锁  0拉黑  -1 删除',
  `activate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否激活 1-已激活 0-未激活',
  `use_grade` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户等级',
  `tg_num` int(11) NOT NULL COMMENT '总推人数',
  `rz_st` int(1) NOT NULL DEFAULT '0' COMMENT '资料完善状态，1OK2no',
  `zsy` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '总收益',
  `add_admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '添加该用户的管理元id',
  `work_status` int(1) NOT NULL DEFAULT '0',
  `security_salt` varchar(225) NOT NULL,
  `security_pwd` varchar(225) NOT NULL,
  `token` varchar(45) DEFAULT NULL,
  `is_allow_work` tinyint(1) DEFAULT '0' COMMENT '是否被禁止工作',
  `last_online_time` int(11) DEFAULT NULL,
  `tg_level` tinyint(1) DEFAULT NULL COMMENT 'ç¨·ä»£çç­çº§,ç³»ç»ç»éè¯·ç æ³¨åçç¨·ä¸º1,çº§ä¾æ¬¡ç±»æ¨',
  `updatetime` int(11) DEFAULT NULL COMMENT 'ä¿®æ¹æ¶é´',
  `google_status` int(11) DEFAULT '0' COMMENT 'googleå¯é¥ç¶æ',
  `google_secretkey` varchar(100) DEFAULT NULL COMMENT 'å¯é¥',
  `auth_ips` varchar(255) DEFAULT '' COMMENT 'ç¨·è®¿é®ç½åå',
  `blocking_reason` varchar(100) DEFAULT NULL COMMENT 'å»ç»ååå ',
  `cash_pledge` decimal(10,2) NOT NULL COMMENT '押金',
  `payment_amount_limit` decimal(10,2) NOT NULL COMMENT '可完成金额上线',
  `bank_rate` float(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '代付费率',
  `deposit_floating_money` decimal(8,2) NOT NULL COMMENT '码商押金浮动金额',
  `tg_group_id` varchar(50) NOT NULL DEFAULT '' COMMENT '当前码商tg群id',
  `ms_secret` varchar(50) NOT NULL DEFAULT '' COMMENT '当前码商tg群secret',
  `weight` int(11) NOT NULL DEFAULT '1' COMMENT '码商权重',
  `level` int(11) NOT NULL DEFAULT '1' COMMENT '等级',
  `admin_id` int(11) NOT NULL COMMENT '所属管理员ID',
  `is_daifu` int(4) NOT NULL DEFAULT '1' COMMENT '是否允许代付',
  `min_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最小接单金额',
  `max_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最大接单金额',
  `monitor_ip` text NOT NULL COMMENT '监控回调ip',
  `disable_money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户冻结余额余额',
  PRIMARY KEY (`userid`) USING BTREE,
  KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_ms`
--

LOCK TABLES `cm_ms` WRITE;
/*!40000 ALTER TABLE `cm_ms` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_ms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_ms_rate`
--

DROP TABLE IF EXISTS `cm_ms_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_ms_rate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ms_id` int(11) NOT NULL DEFAULT '0' COMMENT '码商id',
  `code_type_id` int(11) NOT NULL DEFAULT '0' COMMENT '支付编码id',
  `rate` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT '费率',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_ms_rate`
--

LOCK TABLES `cm_ms_rate` WRITE;
/*!40000 ALTER TABLE `cm_ms_rate` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_ms_rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_ms_somebill`
--

DROP TABLE IF EXISTS `cm_ms_somebill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_ms_somebill` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(11) NOT NULL COMMENT '会员ID',
  `jl_class` int(11) NOT NULL COMMENT '流水类别：1佣金2团队奖励3充值4提现5订单匹                                                                                                             配 6平台操作 7关闭订单',
  `type` varchar(20) NOT NULL DEFAULT 'enable' COMMENT 'enable 可用余额 disable 冻结余额',
  `info` varchar(225) NOT NULL COMMENT '说明',
  `addtime` varchar(225) NOT NULL COMMENT '事件时间',
  `jc_class` varchar(225) NOT NULL COMMENT '分+ 或-',
  `num` float(10,2) NOT NULL COMMENT '币量',
  `pre_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '变化前',
  `last_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'åå¨变化后¢',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='码商流水账单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_ms_somebill`
--

LOCK TABLES `cm_ms_somebill` WRITE;
/*!40000 ALTER TABLE `cm_ms_somebill` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_ms_somebill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_ms_white_ip`
--

DROP TABLE IF EXISTS `cm_ms_white_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_ms_white_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ms_id` int(11) NOT NULL COMMENT '码商的id',
  `md5_ip` varchar(50) NOT NULL COMMENT '码商ip白名单MD5值',
  PRIMARY KEY (`id`),
  KEY `ms_id` (`ms_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_ms_white_ip`
--

LOCK TABLES `cm_ms_white_ip` WRITE;
/*!40000 ALTER TABLE `cm_ms_white_ip` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_ms_white_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_notice`
--

DROP TABLE IF EXISTS `cm_notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_notice` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL,
  `author` varchar(30) DEFAULT NULL COMMENT '作者',
  `content` text NOT NULL COMMENT '公告内容',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '公告状态,0-不展示,1-展示',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_notice`
--

LOCK TABLES `cm_notice` WRITE;
/*!40000 ALTER TABLE `cm_notice` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_orders`
--

DROP TABLE IF EXISTS `cm_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_orders` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `puid` mediumint(8) NOT NULL DEFAULT '0' COMMENT '代理ID',
  `uid` mediumint(8) NOT NULL COMMENT '商户id',
  `trade_no` varchar(30) NOT NULL COMMENT '交易订单号',
  `out_trade_no` varchar(30) NOT NULL COMMENT '商户订单号',
  `subject` varchar(64) NOT NULL COMMENT '商品标题',
  `body` varchar(256) NOT NULL COMMENT '商品描述信息',
  `channel` varchar(30) NOT NULL COMMENT '交易方式(wx_native)',
  `cnl_id` int(3) NOT NULL COMMENT '支付通道ID',
  `extra` text COMMENT '特定渠道发起时额外参数',
  `amount` decimal(12,3) unsigned NOT NULL COMMENT '订单金额,单位是元,12-9保留3位小数',
  `income` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '实付金额',
  `user_in` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT '商户收入',
  `agent_in` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '代理收入',
  `platform_in` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '平台收入',
  `currency` varchar(3) NOT NULL DEFAULT 'CNY' COMMENT '三位货币代码,人民币:CNY',
  `client_ip` varchar(32) NOT NULL COMMENT '客户端IP',
  `return_url` varchar(128) NOT NULL COMMENT '同步通知地址',
  `notify_url` varchar(128) NOT NULL COMMENT '异步通知地址',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单状态:0-已取消-1-待付款，2-已付款',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `bd_remarks` varchar(455) NOT NULL,
  `visite_time` int(10) NOT NULL DEFAULT '0' COMMENT 'è®¿é®æ¶é´',
  `real_need_amount` decimal(12,3) NOT NULL COMMENT 'éè¦ç¨·æ¯ä»éé¢',
  `image_url` varchar(445) NOT NULL COMMENT 'éè¦ç¨·æ¯ä»éé¢',
  `request_log` varchar(445) NOT NULL COMMENT 'log',
  `visite_show_time` int(10) NOT NULL DEFAULT '0' COMMENT 'å è½½å®æ¶é´',
  `request_elapsed_time` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '请求时间',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no_index` (`out_trade_no`,`trade_no`,`uid`,`channel`) USING BTREE,
  UNIQUE KEY `trade_no_index` (`trade_no`) USING BTREE,
  KEY `stat` (`cnl_id`,`create_time`) USING BTREE,
  KEY `stat1` (`cnl_id`,`status`,`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交易订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_orders`
--

LOCK TABLES `cm_orders` WRITE;
/*!40000 ALTER TABLE `cm_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_orders_notify`
--

DROP TABLE IF EXISTS `cm_orders_notify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_orders_notify` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `is_status` int(3) unsigned NOT NULL DEFAULT '404',
  `result` varchar(300) NOT NULL DEFAULT '' COMMENT '请求相响应',
  `times` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '请求次数',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交易订单通知表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_orders_notify`
--

LOCK TABLES `cm_orders_notify` WRITE;
/*!40000 ALTER TABLE `cm_orders_notify` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_orders_notify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_pay_account`
--

DROP TABLE IF EXISTS `cm_pay_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_pay_account` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '账号ID',
  `cnl_id` bigint(10) NOT NULL COMMENT '所属渠道ID',
  `co_id` text NOT NULL COMMENT '支持的方式(有多个)',
  `name` varchar(30) NOT NULL COMMENT '渠道账户名称',
  `rate` decimal(4,3) NOT NULL COMMENT '渠道账户费率',
  `urate` decimal(4,3) NOT NULL DEFAULT '0.998',
  `grate` decimal(4,3) NOT NULL DEFAULT '0.998',
  `daily` decimal(12,3) NOT NULL COMMENT '当日限额',
  `single` decimal(12,3) NOT NULL COMMENT '单笔限额',
  `timeslot` text NOT NULL COMMENT '交易时间段',
  `param` text NOT NULL COMMENT '账户配置参数,json字符串',
  `remarks` varchar(128) DEFAULT NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '账户状态,0-停止使用,1-开放使用',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `max_deposit_money` decimal(12,3) NOT NULL,
  `min_deposit_money` decimal(12,3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=211 DEFAULT CHARSET=utf8mb4 COMMENT='支付渠道账户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_pay_account`
--

LOCK TABLES `cm_pay_account` WRITE;
/*!40000 ALTER TABLE `cm_pay_account` DISABLE KEYS */;
INSERT INTO `cm_pay_account` VALUES (193,33,'30','卡转卡',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1666212550,1666212550,10000.000,0.000),(194,33,'31','支付宝UID',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',-1,1667648386,1668269145,10000.000,0.000),(195,34,'32','支付宝UID',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"0:0\",\"end\":\"0:0\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1667655982,1668268669,10000.000,0.000),(196,35,'32','支付宝uid',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1668269245,1668269245,10000.000,0.000),(197,36,'39','支付宝扫码',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1673186081,1673186081,10000.000,0.000),(198,37,'43','京东E卡',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1674756109,1674756109,10000.000,0.000),(199,38,'45','QQ面对面红包',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"0:0\",\"end\":\"0:0\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1674885284,1674885300,200.000,0.000),(200,39,'46','淘宝现金红包',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1674894506,1674894506,200.000,0.000),(201,40,'47','支付宝转账码',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1674894568,1674894568,10000.000,0.000),(202,41,'40','数字人民币',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1674988351,1674988351,10000.000,0.000),(203,42,'34','支付宝小荷包',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1675147984,1675147984,10000.000,0.000),(204,43,'36','微信群红包',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"0:0\",\"end\":\"0:0\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1675585975,1675852827,10000.000,1.000),(205,45,'33','支付宝uid小额',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1675614878,1675614878,10000.000,0.000),(206,46,'32','支付宝UID大额',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1675675376,1675675376,10000.000,0.000),(207,47,'41','抖音群红包',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1675853338,1675853338,10000.000,0.000),(208,48,'48','支付宝工作证',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1675949314,1675949314,10000.000,0.000),(209,49,'51','仟信好友转账',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1675949374,1675949374,10000.000,0.000),(210,50,'37','支付宝口令红包',0.000,1.000,0.998,10000.000,10000.000,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}','备注',1,1675963545,1675963545,10000.000,0.000);
/*!40000 ALTER TABLE `cm_pay_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_pay_channel`
--

DROP TABLE IF EXISTS `cm_pay_channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_pay_channel` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '渠道ID',
  `name` varchar(30) NOT NULL COMMENT '支付渠道名称',
  `action` varchar(30) NOT NULL COMMENT '控制器名称,如:Wxpay用于分发处理支付请求',
  `urate` decimal(4,3) NOT NULL DEFAULT '0.998' COMMENT '默认商户分成',
  `grate` decimal(4,3) NOT NULL DEFAULT '0.998' COMMENT '默认代理分成',
  `timeslot` text NOT NULL COMMENT '交易时间段',
  `return_url` varchar(255) NOT NULL COMMENT '同步地址',
  `notify_url` varchar(255) NOT NULL COMMENT '异步地址',
  `remarks` varchar(128) DEFAULT NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '渠道状态,0-停止使用,1-开放使用',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `notify_ips` varchar(445) NOT NULL,
  `ia_allow_notify` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'æ¸ éæ¯å¦åè®¸åè°',
  `channel_fund` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '渠道资金',
  `wirhdraw_charge` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '提现手续费',
  `tg_group_id` varchar(50) NOT NULL DEFAULT '' COMMENT '当前渠tg群id',
  `channel_secret` varchar(50) NOT NULL DEFAULT '' COMMENT '渠道密钥',
  `limit_moneys` varchar(255) NOT NULL DEFAULT '' COMMENT '固定金额 不填写默认不限制',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COMMENT='支付渠道表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_pay_channel`
--

LOCK TABLES `cm_pay_channel` WRITE;
/*!40000 ALTER TABLE `cm_pay_channel` DISABLE KEYS */;
INSERT INTO `cm_pay_channel` VALUES (33,'卡转卡','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://xxxx/api/notify/notify/channel/GumaV2Pay','http://xxxx//api/notify/notify/channel/GumaV2Pay','1',1,1666212536,1666212536,'127.0.0.1',1,0.000,0.000,'','cf1276eccede652bddf81be87ce1fd9b',''),(34,'支付宝UID','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://ta.sxwzrj.cn/api/notify/notify/channel/GumaV2Pay','http://ta.sxwzrj.cn/api/notify/notify/channel/GumaV2Pay','1',-1,1667648360,1668269255,'127.0.0.1',1,0.000,0.000,'','2f316ece078407b8d1a1245c9a91d6d6',''),(35,'UID','GumaV2Pay',1.000,0.998,'{\"start\":\"0:0\",\"end\":\"0:0\"}','http://194.41.36.175/api/notify/notify/channel/GumaV2Pay','http://194.41.36.175/api/notify/notify/channel/GumaV2Pay','1',1,1668269190,1668325863,'194.41.36.175',1,0.000,0.000,'','814431ca424f1cef7dc001cf751ed96d',''),(36,'支付宝扫码','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1673186063,1673186063,'43.225.47.46',1,0.000,0.000,'','9c3561911157273bc9b4f6eb115d3c77',''),(37,'京东E卡','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1674756077,1674756077,'43.225.47.46',1,0.000,0.000,'','78a427d1ca4bc7fee4569ec4f7780452',''),(38,'QQ面对面红包','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1674885277,1674885277,'43.225.47.46',1,0.000,0.000,'','a287039910892620a5bfaaf24e8b81ab',''),(39,'淘宝现金红包','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1674894483,1674894483,'43.225.47.46',1,0.000,0.000,'','6f5f5ec80b54f7a4e0a3c035a3686b31',''),(40,'支付宝转账码','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1674894547,1674894547,'43.225.47.46',1,0.000,0.000,'','b8f485965a318ce3e69a043ad3e73c26',''),(41,'数字人民币','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1674988342,1674988342,'43.225.47.46',1,0.000,0.000,'','484562f41cc610dddcaa951e4a966b37',''),(42,'支付宝小荷包','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1675147971,1675147971,'43.225.47.46',1,0.000,0.000,'','abbe5739c47aedb56ada02cd6e2b16b1',''),(43,'微信群红包','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1675585950,1675585950,'43.225.47.46',1,0.000,0.000,'','5c5604fd0567dc7d6f7137b854dfd3e5',''),(44,'微信群红包','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',-1,1675606131,1675606248,'43.225.47.46',1,0.000,0.000,'','91d560e17508664bf58b9afd90f7ceaf',''),(45,'支付宝uid小额','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1675614865,1675614865,'43.225.47.46',1,0.000,0.000,'','210508664044492fee10c478a5d976b3',''),(46,'支付宝UID大额','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1675675360,1675675360,'43.225.47.46',1,0.000,0.000,'','8f56b9d2d2a2818713933b60f4d5180c',''),(47,'抖音群红包','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1675853329,1675853329,'43.225.47.46',1,0.000,0.000,'','aa11e41f008930706ec61b3cbd6b3fb5',''),(48,'支付宝工作证','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1675949305,1675949305,'43.225.47.46',1,0.000,0.000,'','afefb75fbba85929e33a889b5775b270',''),(49,'仟信好友转账','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1675949364,1675949364,'43.225.47.46',1,0.000,0.000,'','5ad9e70ea5c7b0bd3450654f05288bb9',''),(50,'支付宝口令红包','GumaV2Pay',1.000,0.998,'{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','http://43.225.47.46/api/notify/notify/channel/GumaV2Pay','1',1,1675963533,1675963533,'43.225.47.46',1,0.000,0.000,'','8955943e7e024ea739f00118cffaf22a','');
/*!40000 ALTER TABLE `cm_pay_channel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_pay_channel_change`
--

DROP TABLE IF EXISTS `cm_pay_channel_change`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_pay_channel_change` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` mediumint(8) NOT NULL COMMENT '渠道ID',
  `preinc` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '变动前金额',
  `increase` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '增加金额',
  `reduce` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '减少金额',
  `suffixred` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '变动后金额',
  `remarks` varchar(255) NOT NULL COMMENT '资金变动说明',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `is_flat_op` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否后台人工账变',
  `status` varchar(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='渠道资金变动记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_pay_channel_change`
--

LOCK TABLES `cm_pay_channel_change` WRITE;
/*!40000 ALTER TABLE `cm_pay_channel_change` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_pay_channel_change` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_pay_channel_price_weight`
--

DROP TABLE IF EXISTS `cm_pay_channel_price_weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_pay_channel_price_weight` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '渠道ID',
  `pay_code_id` bigint(10) NOT NULL DEFAULT '0' COMMENT '支付产品id',
  `cnl_id` bigint(10) NOT NULL DEFAULT '0' COMMENT '支付渠道id',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `cnl_weight` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '渠道权重值',
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='支付产品下列渠道在固定金额下的权重';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_pay_channel_price_weight`
--

LOCK TABLES `cm_pay_channel_price_weight` WRITE;
/*!40000 ALTER TABLE `cm_pay_channel_price_weight` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_pay_channel_price_weight` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_pay_code`
--

DROP TABLE IF EXISTS `cm_pay_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_pay_code` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '渠道ID',
  `cnl_id` text,
  `name` varchar(30) NOT NULL COMMENT '支付方式名称',
  `code` varchar(30) NOT NULL COMMENT '支付方式代码,如:wx_native,qq_native,ali_qr;',
  `remarks` varchar(128) DEFAULT NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '方式状态,0-停止使用,1-开放使用',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `cnl_weight` varchar(255) NOT NULL COMMENT 'å½åpaycodeå¯¹åºæ¸ éæé',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=utf8mb4 COMMENT='交易方式表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_pay_code`
--

LOCK TABLES `cm_pay_code` WRITE;
/*!40000 ALTER TABLE `cm_pay_code` DISABLE KEYS */;
INSERT INTO `cm_pay_code` VALUES (30,'33','卡转卡','kzk','卡转卡',1,1666212478,1666790612,''),(32,'46','支付宝UID大额','alipayUid','支付宝UID',1,1668268619,1675675385,'{\"34\":\"100\"}'),(33,'45','支付宝uid小额','alipayUidSmall','支付宝uid小额',1,1668488678,1675614886,''),(34,'42','支付宝小荷包','alipaySmallPurse','支付宝小荷包',1,1670150109,1675147995,''),(35,'','支付宝UID转账','alipayUidTransfer','支付宝UID转账',1,1670828399,1670868195,''),(36,'43','微信群红包','wechatGroupRed','微信群红包',1,1670828681,1675585985,''),(37,'50','支付宝口令红包','alipayPassRed','支付宝口令红包',1,1670828732,1675963553,''),(38,'','支付宝零花钱','alipayPinMoney','支付宝零花钱',0,1670828777,1670829094,''),(39,'36','支付宝扫码','alipayCode','支付宝扫码',1,1670829031,1673186090,''),(40,'41','数字人民币','CnyNumber','数字人民币',1,1670829073,1674988359,''),(41,'47','抖音群红包','douyinGroupRed','抖音群红包',1,1672044867,1675853345,''),(42,'','微信扫码','wechatCode','微信扫码',1,1672129935,1672580963,''),(43,'37','京东E卡','JDECard','京东E卡',1,1672386772,1674756122,''),(44,'','小程序商品码','AppletProducts','小程序商品码',1,1672829029,1672839420,''),(45,'38','QQ面对面红包','QQFaceRed','QQ面对面红包',1,1672903308,1674885308,''),(46,'39','淘宝现金红包','taoBaoMoneyRed','淘宝现金红包',1,1673678427,1674894515,''),(47,'40','支付宝转账码','alipayTransferCode','支付宝转账码',1,1673678427,1674894578,''),(48,'48','支付宝工作证','alipayWorkCard','支付宝工作证',1,1673969458,1675949327,''),(51,'49','仟信好友转账','QianxinTransfer','仟信好友转账',1,1675931119,1675949381,''),(255,NULL,'代付','daifu','代付',1,1666212478,1666212478,'');
/*!40000 ALTER TABLE `cm_pay_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_shop`
--

DROP TABLE IF EXISTS `cm_shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '店铺名称',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '店铺类型',
  `onlinedate` int(11) DEFAULT NULL COMMENT '最后在线时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1在线，2不在线，3停止健康，4停用',
  `password` varchar(45) DEFAULT NULL,
  `token` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_shop`
--

LOCK TABLES `cm_shop` WRITE;
/*!40000 ALTER TABLE `cm_shop` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_shop` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_tg_query_order_records`
--

DROP TABLE IF EXISTS `cm_tg_query_order_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_tg_query_order_records` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `order_no` char(40) NOT NULL COMMENT '查询的订单号',
  `tg_message_id` char(30) NOT NULL COMMENT '查单的消息ID',
  `tg_group_id` char(30) NOT NULL COMMENT '查单的群组ID',
  `success` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单成功 1是 0否',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33870 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_tg_query_order_records`
--

LOCK TABLES `cm_tg_query_order_records` WRITE;
/*!40000 ALTER TABLE `cm_tg_query_order_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_tg_query_order_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_transaction`
--

DROP TABLE IF EXISTS `cm_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_transaction` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) DEFAULT NULL COMMENT '商户id',
  `order_no` varchar(80) DEFAULT NULL COMMENT '交易订单号',
  `amount` decimal(12,3) DEFAULT NULL COMMENT '交易金额',
  `platform` tinyint(1) DEFAULT NULL COMMENT '交易平台:1-支付宝,2-微信',
  `platform_number` varchar(200) DEFAULT NULL COMMENT '交易平台交易流水号',
  `status` tinyint(1) DEFAULT NULL COMMENT '交易状态',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_index` (`order_no`,`platform`,`uid`,`amount`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交易流水表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_transaction`
--

LOCK TABLES `cm_transaction` WRITE;
/*!40000 ALTER TABLE `cm_transaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_user`
--

DROP TABLE IF EXISTS `cm_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_user` (
  `uid` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '商户uid',
  `puid` mediumint(8) NOT NULL DEFAULT '0',
  `account` varchar(50) NOT NULL COMMENT '商户邮件',
  `username` varchar(30) NOT NULL COMMENT '商户名称',
  `auth_code` varchar(32) DEFAULT NULL COMMENT '8位安全码，注册时发送跟随邮件',
  `password` varchar(50) NOT NULL COMMENT '商户登录密码',
  `phone` varchar(250) NOT NULL COMMENT '手机号',
  `qq` varchar(250) NOT NULL COMMENT 'QQ',
  `is_agent` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '代理商',
  `is_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '验证账号',
  `is_verify_phone` tinyint(1) NOT NULL DEFAULT '0' COMMENT '验证手机',
  `is_daifu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许代付',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商户状态,0-未激活,1-使用中,2-禁用',
  `is_hide_withdrawal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏提现',
  `withdrawal_charge` decimal(10,2) NOT NULL DEFAULT '5.00' COMMENT '提现手续费',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `is_need_google_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'æ¯å¦éè¦googleéªè¯ 0 ä¸éè¦  1 éè¦',
  `google_account` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'æ¯å¦éè¦googleéªè¯ 0 ä¸éè¦  1 éè¦',
  `auth_login_ips` varchar(255) NOT NULL DEFAULT '' COMMENT 'ç»å½é´æip',
  `is_verify_bankaccount` enum('1','0') NOT NULL DEFAULT '0' COMMENT '是否审核银行卡账户',
  `google_secret_key` varchar(100) NOT NULL DEFAULT '0' COMMENT 'googleç§é¥',
  `last_online_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后在线时间',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '·æåç»å½æ¶é´',
  `pao_ms_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '如果跑分出码对于码商的ids,逗号拼接',
  `is_can_df_from_index` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许前端发起代付0=》不允许 1=》允许',
  `mch_secret` varchar(50) NOT NULL DEFAULT '' COMMENT '商户tg密钥',
  `tg_group_id` varchar(50) NOT NULL DEFAULT '' COMMENT '商户群组id',
  `mark_abnormal` int(10) DEFAULT '0',
  `admin_id` int(11) NOT NULL COMMENT '所属管理员ID',
  `daifu_transfer_ids` varchar(2083) NOT NULL COMMENT '指定中转平台',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `user_name_unique` (`account`,`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_user`
--

LOCK TABLES `cm_user` WRITE;
/*!40000 ALTER TABLE `cm_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_user_account`
--

DROP TABLE IF EXISTS `cm_user_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_user_account` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `bank_id` mediumint(8) NOT NULL DEFAULT '1' COMMENT '开户行(关联银行表)',
  `account` varchar(250) NOT NULL COMMENT '开户号',
  `address` varchar(250) NOT NULL COMMENT '开户所在地',
  `remarks` varchar(250) NOT NULL COMMENT '备注',
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认账户,0-不默认,1-默认',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `account_name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户结算账户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_user_account`
--

LOCK TABLES `cm_user_account` WRITE;
/*!40000 ALTER TABLE `cm_user_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_user_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_user_auth`
--

DROP TABLE IF EXISTS `cm_user_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_user_auth` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `realname` varchar(30) NOT NULL DEFAULT '1' COMMENT '开户行(关联银行表)',
  `sfznum` varchar(18) NOT NULL COMMENT '开户号',
  `card` text NOT NULL COMMENT '认证详情',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户认证信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_user_auth`
--

LOCK TABLES `cm_user_auth` WRITE;
/*!40000 ALTER TABLE `cm_user_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_user_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_user_daifuprofit`
--

DROP TABLE IF EXISTS `cm_user_daifuprofit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_user_daifuprofit` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `service_rate` decimal(4,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '费率',
  `service_charge` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '单笔手续费',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户代付费率表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_user_daifuprofit`
--

LOCK TABLES `cm_user_daifuprofit` WRITE;
/*!40000 ALTER TABLE `cm_user_daifuprofit` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_user_daifuprofit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_user_padmin`
--

DROP TABLE IF EXISTS `cm_user_padmin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_user_padmin` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '·ID',
  `p_admin_id` mediumint(8) NOT NULL COMMENT 'è·å¹³å°ç®¡çåid',
  `p_admin_appkey` varchar(255) NOT NULL DEFAULT '' COMMENT 'è·å¹³å°çç®¡çåappkeyç§é¥',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1æ­£å¸¸ 0ç¦æ­¢æä½',
  `create_time` int(10) unsigned NOT NULL COMMENT 'å»ºæ¶é´',
  `update_time` int(10) unsigned NOT NULL COMMENT 'æ´æ°æ¶é´',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_user_padmin`
--

LOCK TABLES `cm_user_padmin` WRITE;
/*!40000 ALTER TABLE `cm_user_padmin` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_user_padmin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_user_pay_code`
--

DROP TABLE IF EXISTS `cm_user_pay_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_user_pay_code` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '·ID',
  `co_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'æ¯ä»pay_codeä¸»é®ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:å¼å¯ 0:å³é­',
  `create_time` int(10) unsigned NOT NULL COMMENT 'å»ºæ¶é´',
  `update_time` int(10) unsigned NOT NULL COMMENT 'æ´æ°æ¶é´',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='·æ¯ä»æ¸ éè¡¨å³èpay_code';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_user_pay_code`
--

LOCK TABLES `cm_user_pay_code` WRITE;
/*!40000 ALTER TABLE `cm_user_pay_code` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_user_pay_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_user_pay_code_appoint`
--

DROP TABLE IF EXISTS `cm_user_pay_code_appoint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_user_pay_code_appoint` (
  `appoint_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) NOT NULL COMMENT '用户',
  `pay_code_id` int(11) NOT NULL COMMENT '支付代码',
  `cnl_id` int(11) NOT NULL COMMENT '指定渠道',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`appoint_id`),
  KEY `where` (`pay_code_id`,`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_user_pay_code_appoint`
--

LOCK TABLES `cm_user_pay_code_appoint` WRITE;
/*!40000 ALTER TABLE `cm_user_pay_code_appoint` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_user_pay_code_appoint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_user_profit`
--

DROP TABLE IF EXISTS `cm_user_profit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_user_profit` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `cnl_id` int(10) unsigned NOT NULL,
  `urate` decimal(4,3) unsigned NOT NULL DEFAULT '0.000',
  `grate` decimal(4,3) unsigned NOT NULL DEFAULT '0.000',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `single_handling_charge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单笔手续费',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户分润表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_user_profit`
--

LOCK TABLES `cm_user_profit` WRITE;
/*!40000 ALTER TABLE `cm_user_profit` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_user_profit` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-02-20 13:42:57
