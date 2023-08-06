-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2023-08-07 01:37:21
-- 服务器版本： 5.6.48
-- PHP 版本： 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `www_sys_com`
--

-- --------------------------------------------------------

--
-- 表的结构 `cm_action_log`
--

CREATE TABLE `cm_action_log` (
  `id` bigint(10) UNSIGNED NOT NULL COMMENT '主键',
  `uid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT '执行会员id',
  `module` varchar(30) NOT NULL DEFAULT 'admin' COMMENT '模块',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '行为',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '执行的URL',
  `ip` char(30) NOT NULL DEFAULT '' COMMENT '执行行为者ip',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `admin_id` int(11) NOT NULL COMMENT '管理员id',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '执行行为的时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='行为日志表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `cm_action_log`
--

INSERT INTO `cm_action_log` (`id`, `uid`, `module`, `action`, `describe`, `url`, `ip`, `status`, `admin_id`, `update_time`, `create_time`) VALUES
(1, 1, 'admin', '登录', '管理员admin登录成功', '/admin/login/login.html', '127.0.0.1', 1, 1, 1690975227, 1690975227),
(2, 1, 'admin', '编辑', '编辑菜单,name =>代付管理', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975310, 1690975310),
(3, 1, 'admin', '编辑', '编辑菜单,name =>码商管理', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975315, 1690975315),
(4, 1, 'admin', '编辑', '编辑菜单,name =>网站报表', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975319, 1690975319),
(5, 1, 'admin', '编辑', '编辑菜单,name =>通道管理', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975322, 1690975322),
(6, 1, 'admin', '编辑', '编辑菜单,name =>通道订单', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975325, 1690975325),
(7, 1, 'admin', '编辑', '编辑菜单,name =>通道统计', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975335, 1690975335),
(8, 1, 'admin', '编辑', '编辑菜单,name =>余额管理', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975338, 1690975338),
(9, 1, 'admin', '编辑', '编辑菜单,name =>商户管理', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975387, 1690975387),
(10, 1, 'admin', '编辑', '编辑菜单,name =>订单管理', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975392, 1690975392),
(11, 1, 'admin', '编辑', '编辑菜单,name =>支付设置', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975410, 1690975410),
(12, 1, 'admin', '编辑', '编辑菜单,name =>公告管理', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690975420, 1690975420),
(13, 1, 'admin', '编辑', '编辑菜单,name =>基本设置', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690976054, 1690976054),
(14, 1, 'admin', '编辑', '编辑菜单,name =>我的设置', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690976065, 1690976065),
(15, 1, 'admin', '编辑', '编辑菜单,name =>代付设置', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690976069, 1690976069),
(16, 1, 'admin', '编辑', '编辑菜单,name =>确认命令', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1690976073, 1690976073),
(17, 1, 'admin', '新增', '新增菜单,name =>系统管理', '/admin/menu/menuAdd', '127.0.0.1', 1, 1, 1690976167, 1690976167),
(18, 1, 'admin', '删除', '删除菜单,where:id=2', '/admin/menu/menuDel?id=2', '127.0.0.1', 1, 1, 1690976220, 1690976220),
(19, 1, 'admin', '删除', '删除菜单,where:id=29', '/admin/menu/menuDel?id=29', '127.0.0.1', 1, 1, 1690976223, 1690976223),
(20, 1, 'admin', '删除', '删除菜单,where:id=61', '/admin/menu/menuDel?id=61', '127.0.0.1', 1, 1, 1690976225, 1690976225),
(21, 1, 'admin', '删除', '删除菜单,where:id=94', '/admin/menu/menuDel?id=94', '127.0.0.1', 1, 1, 1690976227, 1690976227),
(22, 1, 'admin', '删除', '删除菜单,where:id=105', '/admin/menu/menuDel?id=105', '127.0.0.1', 1, 1, 1690976229, 1690976229),
(23, 1, 'admin', '删除', '删除菜单,where:id=117', '/admin/menu/menuDel?id=117', '127.0.0.1', 1, 1, 1690976231, 1690976231),
(24, 1, 'admin', '删除', '删除菜单,where:id=201', '/admin/menu/menuDel?id=201', '127.0.0.1', 1, 1, 1690976233, 1690976233),
(25, 1, 'admin', '删除', '删除菜单,where:id=219', '/admin/menu/menuDel?id=219', '127.0.0.1', 1, 1, 1690976235, 1690976235),
(26, 1, 'admin', '删除', '删除菜单,where:id=220', '/admin/menu/menuDel?id=220', '127.0.0.1', 1, 1, 1690976237, 1690976237),
(27, 1, 'admin', '删除', '删除菜单,where:id=221', '/admin/menu/menuDel?id=221', '127.0.0.1', 1, 1, 1690976239, 1690976239),
(28, 1, 'admin', '删除', '删除菜单,where:id=322', '/admin/menu/menuDel?id=322', '127.0.0.1', 1, 1, 1690976242, 1690976242),
(29, 1, 'admin', '删除', '删除菜单,where:id=453', '/admin/menu/menuDel?id=453', '127.0.0.1', 1, 1, 1690976244, 1690976244),
(30, 1, 'admin', '登录', '管理员admin登录成功', '/admin/login/login.html', '127.0.0.1', 1, 1, 1691216720, 1691216720),
(31, 1, 'admin', '新增', '新增菜单,name =>Web列表', '/admin/menu/menuAdd', '127.0.0.1', 1, 1, 1691217701, 1691217701),
(32, 1, 'admin', '编辑', '编辑菜单,name =>系统管理', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1691217735, 1691217735),
(33, 1, 'admin', '编辑', '编辑菜单,name =>Web列表', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1691217752, 1691217752),
(34, 1, 'admin', '登录', '管理员admin登录成功', '/admin/login/login.html', '127.0.0.1', 1, 1, 1691305073, 1691305073),
(35, 1, 'admin', '登录', '管理员admin登录成功', '/admin/login/login.html', '127.0.0.1', 1, 1, 1691306184, 1691306184),
(36, 1, 'admin', '编辑', '编辑菜单,name =>加分', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1691326509, 1691326509),
(37, 1, 'admin', '新增', '新增菜单,name =>加分记录', '/admin/menu/menuAdd', '127.0.0.1', 1, 1, 1691326715, 1691326715),
(38, 1, 'admin', '编辑', '编辑菜单,name =>加分记录', '/admin/menu/menuEdit', '127.0.0.1', 1, 1, 1691326749, 1691326749),
(39, 1, 'admin', '新增', '新增菜单,name =>清理登录限制', '/admin/menu/menuAdd', '127.0.0.1', 1, 1, 1691327819, 1691327819);

-- --------------------------------------------------------

--
-- 表的结构 `cm_admin`
--

CREATE TABLE `cm_admin` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `leader_id` mediumint(8) NOT NULL DEFAULT '1',
  `username` varchar(20) DEFAULT '0',
  `nickname` varchar(40) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `email` varchar(80) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `agent_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '代理id',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `google_status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'googleç¶æ1 ç»å® 0æªç»å®',
  `google_secret_key` varchar(100) NOT NULL DEFAULT '' COMMENT 'ç®¡çågoogleç§é¥',
  `balance` decimal(10,2) NOT NULL COMMENT '余额',
  `rate` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '费率',
  `daifu_auto_transfer` tinyint(10) NOT NULL DEFAULT '0' COMMENT '自动中转代付',
  `security_code` varchar(32) NOT NULL COMMENT '安全码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员信息';

--
-- 转存表中的数据 `cm_admin`
--

INSERT INTO `cm_admin` (`id`, `leader_id`, `username`, `nickname`, `password`, `phone`, `email`, `status`, `agent_id`, `create_time`, `update_time`, `google_status`, `google_secret_key`, `balance`, `rate`, `daifu_auto_transfer`, `security_code`) VALUES
(1, 0, 'admin', 'admin', '46965619e637d537349928f51836a64e', '13333333333', 'admin@163.com', 1, 0, 1552016220, 1691306184, 0, 'EeO8Vz9nbkd8o1zpQjr7An2hvBFHq+fM4i/oXlWL07g=', '10000000.00', '0.000', 0, '9cbf8a4dcb8e30682b927f352d6559a0');

-- --------------------------------------------------------

--
-- 表的结构 `cm_admin_bill`
--

CREATE TABLE `cm_admin_bill` (
  `id` int(11) NOT NULL COMMENT 'id',
  `admin_id` int(11) NOT NULL COMMENT 'admin ID',
  `jl_class` int(11) NOT NULL COMMENT '流水类别：1充值                                                                                                           ',
  `info` varchar(225) NOT NULL COMMENT '说明',
  `addtime` varchar(225) NOT NULL COMMENT '添加时间',
  `jc_class` varchar(225) NOT NULL COMMENT '分+ 或-',
  `amount` float(10,2) NOT NULL COMMENT '金额',
  `pre_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '变化前',
  `last_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '变化后',
  `username` varchar(255) NOT NULL,
  `web_url` varchar(2083) NOT NULL DEFAULT '0',
  `user_ip` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='admin 账单' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `cm_admin_bill`
--

INSERT INTO `cm_admin_bill` (`id`, `admin_id`, `jl_class`, `info`, `addtime`, `jc_class`, `amount`, `pre_amount`, `last_amount`, `username`, `web_url`, `user_ip`) VALUES
(3, 2, 2, '后台管理员账变', '1691326455', '+', 100.00, '590.00', '690.00', 'admin001', 'http://ta.sanfang.com/admin.html', '');

-- --------------------------------------------------------

--
-- 表的结构 `cm_admin_rate`
--

CREATE TABLE `cm_admin_rate` (
  `id` int(11) UNSIGNED NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '码商id',
  `code_type_id` int(11) NOT NULL DEFAULT '0' COMMENT '支付编码id',
  `rate` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT '费率',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_admin_recharge_record`
--

CREATE TABLE `cm_admin_recharge_record` (
  `id` int(11) NOT NULL COMMENT 'id',
  `admin_id` int(11) NOT NULL COMMENT 'admin ID',
  `usdt_address` varchar(255) NOT NULL COMMENT 'USDT地址',
  `usdt_num` decimal(10,2) NOT NULL COMMENT 'usdt 数量',
  `amount` decimal(10,2) NOT NULL COMMENT '金额',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `status` tinyint(1) NOT NULL COMMENT '0支付中 1支付到账',
  `from_usdt_address` varchar(255) NOT NULL DEFAULT '' COMMENT '来自转账的地址',
  `transaction_id` varchar(255) NOT NULL DEFAULT '' COMMENT '交易ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='admin 充值记录' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `cm_api`
--

CREATE TABLE `cm_api` (
  `id` bigint(10) NOT NULL,
  `uid` mediumint(8) DEFAULT NULL COMMENT '商户id',
  `key` varchar(32) DEFAULT NULL COMMENT 'API验证KEY',
  `sitename` varchar(30) NOT NULL,
  `domain` varchar(100) NOT NULL COMMENT '商户验证域名',
  `daily` decimal(12,3) NOT NULL DEFAULT '20000.000' COMMENT '日限访问（超过就锁）',
  `secretkey` text NOT NULL COMMENT '商户请求RSA私钥',
  `auth_ips` text NOT NULL,
  `role` int(4) NOT NULL COMMENT '角色1-普通商户,角色2-特约商户',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商户API状态,0-禁用,1-锁,2-正常',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `is_verify_sign` int(11) DEFAULT '1' COMMENT '是否验证sign 1 验证 0 不验证',
  `min_amount` int(10) NOT NULL DEFAULT '0' COMMENT 'å•†æˆ·ä¸‹å•è¯·æ±‚æœ€å°é‡‘é¢',
  `max_amount` int(10) NOT NULL DEFAULT '0' COMMENT 'å•†æˆ·ä¸‹å•è¯·æ±‚æœ€å¤§é‡‘é¢',
  `is_notify_status` int(11) NOT NULL DEFAULT '1' COMMENT 'æ˜¯å¦æ”¯æŒå›žè°ƒ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户信息表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_article`
--

CREATE TABLE `cm_article` (
  `id` bigint(10) UNSIGNED NOT NULL COMMENT '文章ID',
  `author` char(20) NOT NULL DEFAULT 'admin' COMMENT '作者',
  `title` char(40) NOT NULL DEFAULT '' COMMENT '文章名称',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `content` text NOT NULL COMMENT '文章内容',
  `cover_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '封面图片id',
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文件id',
  `img_ids` varchar(200) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '数据状态',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章表' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `cm_auth_group`
--

CREATE TABLE `cm_auth_group` (
  `id` bigint(10) UNSIGNED NOT NULL COMMENT '用户组id,自增主键',
  `uid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `module` varchar(20) NOT NULL DEFAULT '' COMMENT '用户组所属模块',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '用户组名称',
  `describe` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(2083) DEFAULT NULL,
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限组表';

--
-- 转存表中的数据 `cm_auth_group`
--

INSERT INTO `cm_auth_group` (`id`, `uid`, `module`, `name`, `describe`, `status`, `rules`, `update_time`, `create_time`) VALUES
(1, 1, '', '超级管理员', '拥有至高无上的权利', 1, '超级权限', 1541001599, 1538323200),
(2, 2, '', '代理专用', '代理专用', 1, '1,121,125,201,202,203', 1668709984, 1538323200),
(3, 0, '', '编辑', '负责编辑文章和站点公告', 1, '1,15,16,17,32', 1544360098, 1540381656),
(4, 0, '', '对账专用', '对账专用', 1, '61,67,68,149,84,85,86,87,89,90,92,93,150,104,147,94,95,96,97,146,105,111,144,145,117,118,121,125,126,128,129,130,131,134,140,143,148,151', 1663261861, 1660592422),
(5, 0, '', '网站管理员', '网站管理员', 1, '1,182,212,298,300,301,463,2,3,6,7,299,26,28,29,30,31,254,390,391,400,37,61,62,63,64,65,66,127,141,142,195,205,209,216,67,68,69,70,132,133,149,191,192,204,207,208,84,85,86,87,89,90,92,93,150,210,99,100,443,103,160,147,173,174,175,176,177,460,105,111,184,185,186,187,206,363,366,370,399,144,145,217,218,464,272,273,274,275,276,277,297,321,379,380,459,361,362,117,118,368,371,387,392,401,435,442,444,446,462,128,129,130,131,134,136,140,148,151,157,166,167,168,169,294,367,170,171,172,178,179,180,181,211,278,295,296,461,328,364,365,430,431,432,433,219,121,197,155,159,196,213,214,215,222,223,292,224,225,293,226,227,255,228,229,286,230,231,284,287,232,233,288,234,289,235,236,237,257,258,290,262,263,264,268,269,291,302,303,304,306,307,308,315,316,317,329,331,445,330,332,333,334,341,342,343,348,349,350,355,356,357,372,373,374,381,382,383,393,394,395,402,403,404,405,406,410,411,412,416,417,418,419,420,424,425,426,447,448,449,220,125,156,158,188,189,190,238,239,281,240,241,282,242,243,256,244,245,246,247,280,248,249,279,283,378,434,250,251,285,347,252,253,259,260,261,265,266,267,270,271,305,309,310,311,312,313,314,318,319,320,335,336,337,338,339,340,344,345,346,351,352,353,354,358,359,360,369,375,376,377,384,385,386,388,389,396,397,398,407,408,409,413,414,415,421,422,423,427,428,429,450,451,452', 1690351357, 1667650678);

-- --------------------------------------------------------

--
-- 表的结构 `cm_auth_group_access`
--

CREATE TABLE `cm_auth_group_access` (
  `uid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
  `group_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户组id',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组授权表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_balance`
--

CREATE TABLE `cm_balance` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `enable` decimal(12,3) UNSIGNED DEFAULT '0.000' COMMENT '可用余额(已结算金额)',
  `disable` decimal(12,3) UNSIGNED DEFAULT '0.000' COMMENT '冻结金额(待结算金额)',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '账户状态 1正常 0禁止操作',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户资产表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_balance_cash`
--

CREATE TABLE `cm_balance_cash` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `cash_no` varchar(80) NOT NULL COMMENT '取现记录单号',
  `amount` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT '取现金额',
  `account` int(2) NOT NULL DEFAULT '0' COMMENT '取现账户（关联商户结算账户表）',
  `remarks` varchar(255) NOT NULL COMMENT '取现说明',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '取现状态',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '申请时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '处理时间',
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
  `withdraw_usdt_address` varchar(255) NOT NULL DEFAULT '' COMMENT 'usdt提款地址'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户账户取现记录';

-- --------------------------------------------------------

--
-- 表的结构 `cm_balance_change`
--

CREATE TABLE `cm_balance_change` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `type` varchar(20) NOT NULL DEFAULT 'enable' COMMENT '资金类型',
  `preinc` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '变动前金额',
  `increase` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '增加金额',
  `reduce` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '减少金额',
  `suffixred` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '变动后金额',
  `remarks` varchar(255) NOT NULL COMMENT '资金变动说明',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `is_flat_op` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'æ¯å¦åå°äººå·¥è´¦å',
  `order_no` varchar(255) DEFAULT NULL,
  `type_reason` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户资产变动记录表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_bank`
--

CREATE TABLE `cm_bank` (
  `bank_id` int(10) UNSIGNED NOT NULL,
  `bank_name` varchar(50) NOT NULL DEFAULT '' COMMENT '银行名称',
  `bank_color` varchar(200) NOT NULL DEFAULT '' COMMENT '银行App展示渐变色',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '银行网银地址',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '银行logo',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '银行状态0为启用，1为禁用',
  `create_user` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `update_user` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `is_maintain` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否维护',
  `maintain_start` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '维护开始时间',
  `maintain_end` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '维护结束时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='接受的在线提现银行表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `cm_banker`
--

CREATE TABLE `cm_banker` (
  `id` bigint(10) NOT NULL COMMENT '银行ID',
  `name` varchar(80) NOT NULL COMMENT '银行名称',
  `remarks` varchar(140) NOT NULL COMMENT '备注',
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认账户,0-不默认,1-默认',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '银行可用性',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `bank_code` varchar(32) DEFAULT NULL COMMENT '银行编码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统支持银行列表';

--
-- 转存表中的数据 `cm_banker`
--

INSERT INTO `cm_banker` (`id`, `name`, `remarks`, `default`, `status`, `create_time`, `update_time`, `bank_code`) VALUES
(2, '工商银行', '工商银行', 1, 1, 1535983287, 1649746591, 'ICBC'),
(3, '农业银行', '农业银行', 1, 1, 1535983287, 1649746588, 'ABC'),
(4, '招商银行', '', 1, 1, 1535983287, 1649746523, 'CMB'),
(5, '中国民生', '', 1, 1, 1535983287, 1649746601, 'CMBC'),
(6, '建设银行 ', '中国建设银行', 1, 1, 1535983287, 1650077959, 'CCB'),
(7, '兴业银行', '', 1, 1, 1535983287, 1649746607, 'CIB'),
(9, '中国光大', '', 1, 1, 1535983287, 1649746612, 'CEB'),
(10, '邮政银行', '中国邮政储蓄银行', 1, 1, 1535983287, 1650077901, 'PSBC   '),
(11, '中国银行', '', 1, 1, 1535983287, 1649746619, 'BOC'),
(12, '平安银行', '', 1, 1, 1535983287, 1649746622, 'PAB'),
(13, '中国农业', '', 1, 1, 1535983287, 1649746628, 'ABC'),
(14, '北京银行', '', 1, 1, 1535983287, 1649746631, 'BOB'),
(15, '上海浦东发展银行', '', 1, 1, 1535983287, 1649746636, 'SPDB'),
(16, '宁波银行', '', 1, 1, 1535983287, 1649746639, 'NBCB'),
(17, '中信银行', '', 1, 1, 1535983287, 1649746645, 'CITIC'),
(18, '华夏银行', '', 1, 1, 1535983287, 1649746649, 'HXB'),
(19, '交通银行', '', 1, 1, 1535983287, 1649746672, 'COMM'),
(21, '桂林银行', '', 1, 1, 1584005500, 1649747087, 'GUILIN'),
(23, '山西省农村信用社', '', 1, 1, 1649747351, 1668686683, 'SXNXS'),
(24, '辽宁省农村信用社', '', 1, 1, 1649747369, 1668686471, 'LNNX'),
(25, '吉林省农村信用社', '', 1, 1, 1649747453, 1668686485, 'JLNX'),
(26, '黑龙江省农村信用社', '', 1, 1, 1649747472, 1668687388, 'HLJNX'),
(27, '江苏省农村信用社', '', 1, 1, 1649747483, 1668687372, 'JSNX'),
(28, '浙江省农村信用社', '', 1, 1, 1649747496, 1668687356, 'ZJNX'),
(29, '安徽省农村信用社', '', 1, 1, 1649747509, 1668687345, 'AHNX'),
(30, '福建省农村信用社', '', 1, 1, 1649747524, 1668687335, 'FJNX'),
(31, '江西省农村信用社', '', 1, 1, 1649747535, 1668687326, 'JXNX'),
(32, '山东省农村信用社', '', 1, 1, 1649747550, 1668687316, 'SDNX'),
(33, '河南省农村信用社', '', 1, 1, 1649747576, 1668687307, 'HNNX'),
(34, '湖北省农村信用社', '', 1, 1, 1649747592, 1668687288, 'HBNX'),
(35, '湖南省农村信用社', '', 1, 1, 1649747604, 1668687249, 'HNNX'),
(36, '广东省农村信用社', '', 1, 1, 1649747618, 1668687236, 'GDNX'),
(37, '海南省农村信用社', '', 1, 1, 1649747637, 1668687224, 'HNNX'),
(38, '四川省农村信用社', '', 1, 1, 1649747653, 1668687213, 'SCNX'),
(39, '贵州省农村信用社', '', 1, 1, 1649747664, 1668687199, 'GZNX'),
(40, '云南省农村信用社', '', 1, 1, 1649747688, 1668687140, 'YNNX'),
(41, '陕西省农村信用社', '', 1, 1, 1649747709, 1668687131, 'SXNXS'),
(42, '甘肃省农村信用社', '', 1, 1, 1649747721, 1668687125, 'GSNX'),
(43, '青海省农村信用社', '', 1, 1, 1649747737, 1668687116, 'QHNX'),
(44, '内蒙古自治区农村信用社', '', 1, 1, 1649747752, 1668687101, 'NMGXYS'),
(45, '广西壮族自治区农村信用社', '', 1, 1, 1649747767, 1668687094, 'GXNX'),
(46, '西藏自治区农村信用社', '', 1, 1, 1649747785, 1668687080, 'XZZZQNX'),
(47, '宁夏省农村信用社', '', 1, 1, 1649747799, 1668687064, 'NXNX'),
(48, '新疆省农村信用社', '', 1, 1, 1649747821, 1668687048, 'XJNX'),
(49, '广发银行', '', 1, 1, 1649781066, 1668687651, 'GF'),
(50, '江苏银行', '', 1, 1, 1649916222, 1668687032, 'JSYH'),
(51, '上海农村商业银行', '', 1, 1, 1650904706, 1650904706, 'SH'),
(52, '广州农村商业银行', '', 1, 1, 1651016002, 1651016002, 'GZNCYH'),
(53, '上海银行', '', 1, 1, 1651311322, 1651311322, 'SHYH'),
(54, '北京农商银行', '', 1, 1, 1652941556, 1652941556, 'BJ'),
(55, '常熟农商银行', '1', 1, 1, 1653234121, 1668687882, 'CHSH'),
(56, '贵州银行', '', 1, 1, 1653368462, 1653368462, 'GZYH'),
(57, '浙江民泰商业银行', '1', 1, 1, 1653417166, 1668687837, 'ZJMTSYYH'),
(58, '无锡农村商业银行', '', 1, 1, 1653779866, 1668687869, 'WXNCSYYH'),
(59, '哈尔滨银行', '', 0, 1, 1654786615, 1668687842, 'HRB'),
(60, '温州银行', '', 1, 1, 1654997353, 1668687787, 'WZH'),
(61, '广西自治区农村信用社', '', 0, 1, 1658250089, 1668687830, 'GXZZQ'),
(62, '重庆农村商业银行', '', 0, 1, 1658497181, 1659494557, 'cqncsyyh'),
(63, '重庆农村商业银行', '', 1, 1, 1658497320, 1668687803, 'CQNS'),
(64, '广发银行', '', 0, 1, 1658582124, 1668687815, 'HFYH'),
(65, '河北省农村信用社', '', 1, 1, 1658668837, 1668687001, 'HBNX'),
(66, '广西农村信用社联合社', '', 1, 1, 1658836334, 1658836334, 'GXNX'),
(67, '渤海银行', '', 0, 1, 1659024770, 1659024770, 'BH'),
(68, '长沙银行', '', 0, 1, 1659153466, 1668608302, 'CSYH2'),
(69, '中原台州银行', '', 1, 1, 1659439442, 1668687900, 'ZYTZYH'),
(70, '中原银行', '', 0, 1, 1659439502, 1668687881, 'ZYYH'),
(71, '恒丰银行', '', 0, 1, 1659509748, 1659509748, 'HF'),
(72, '武汉农村商业银行', '', 1, 1, 1659858186, 1668688044, 'WHNCSYYH'),
(73, '南京银行', '', 1, 1, 1660008360, 1668687968, 'JNYH'),
(74, '贵阳银行', '', 1, 1, 1660056716, 1668686984, 'GY'),
(75, '湖北银行', '', 0, 1, 1660232869, 1668686974, 'HBYH'),
(76, '长沙银行', '', 1, 1, 1660418670, 1668686952, 'ZHSH'),
(77, '河北银行', '', 1, 1, 1660551385, 1668686937, 'HBYH'),
(78, '东莞农村商业银行', '', 0, 1, 1660967148, 1668686919, 'DGNCSYYH'),
(79, '浦发银行', '', 0, 1, 1661565589, 1668686889, 'PFYH'),
(81, '汇丰银行', '', 1, 1, 1661822767, 1668608281, 'HFYHA'),
(83, '上饶银行', '', 1, 1, 1662199939, 1668687925, 'SRYHA'),
(84, '广州银行', '', 1, 1, 1662283514, 1668688019, 'GZHH'),
(85, '微商银行', '', 1, 1, 1662357046, 1668687929, 'WSYH'),
(86, '河北银行', '', 1, 1, 1662867273, 1668687919, 'HBYH'),
(87, '晋商银行', '', 1, 1, 1663226846, 1663226846, 'JSYH'),
(88, '莱商银行', '', 1, 1, 1663239106, 1668071312, 'LSHYH'),
(89, '宁夏银行', '', 1, 1, 1663321499, 1668687907, 'NXX'),
(90, '河北省农村信用社', '', 1, 1, 1663321548, 1668608263, 'HBNCXYS'),
(91, '花旗银行', '', 1, 1, 1663549033, 1668608191, 'GWHQYH'),
(92, '鼎业村镇银行', '', 0, 1, 1664000381, 1668608231, 'DYCZYH'),
(93, '深圳龙岗鼎业村镇银行', '', 1, 1, 1664001008, 1668687994, 'SHZH'),
(94, '东莞银行', '', 1, 1, 1664243296, 1668608207, 'SZDWYH'),
(95, '九江银行', '', 1, 1, 1664284379, 1668071288, 'JJYH'),
(96, '支付宝', '支付宝', 0, 1, 1668055324, 1668055324, 'ZFB'),
(97, '云南农村信用社', '云南农村信用社', 0, 1, 1668071146, 1668071146, 'YNNCXYS'),
(98, '农村合作信用社', '农村合作信用社', 0, 1, 1668071181, 1668071181, 'NCHZXYS'),
(99, '昆明农联社', '昆明农联社', 0, 1, 1668082219, 1668082219, 'KMNLS'),
(100, '微信', '微信', 0, 1, 1668592275, 1668608480, 'WX'),
(101, '台州银行', '台州银行', 0, 1, 1668616223, 1668616223, 'ZGTZYH'),
(103, '蒙商银行', '', 1, 1, 1668676976, 1668676976, 'PERB'),
(104, '长春经开融丰村镇银行', '', 1, 1, 1668678922, 1668678922, 'CCRFCB'),
(106, '包商银行', '', 1, 1, 1668679059, 1668679059, 'BSB'),
(107, '保定银行', '', 1, 1, 1668679271, 1668679271, 'BOB'),
(108, '北京农村商业银行', '', 1, 1, 1668679409, 1668679409, 'BJRCB'),
(109, '阳光村镇银行', '', 1, 1, 1668679429, 1668679429, 'YGCZYH'),
(110, '北京商业银行', '', 1, 1, 1668679450, 1668679450, 'BCCB'),
(111, '本溪银行', '', 1, 1, 1668679507, 1668679507, 'BOBZ'),
(112, '渤海银行', '', 1, 1, 1668679558, 1668679558, 'CBHB'),
(113, '沧州银行', '', 1, 1, 1668679579, 1668679579, 'BOCZ'),
(114, '常熟农商银行', '', 1, 1, 1668679599, 1668679599, 'CSRCB'),
(115, '成都农商银行', '', 1, 1, 1668679696, 1668679696, 'CDRCB'),
(116, '成都银行', '', 1, 1, 1668679719, 1668679719, 'CDCB'),
(117, '稠州银行', '', 1, 1, 1668679768, 1668679768, 'CZCB'),
(118, '达州银行', '', 1, 1, 1668679792, 1668679792, 'BODZ'),
(119, '大连农商银行', '', 1, 1, 1668679816, 1668679816, 'DLRCB'),
(120, '大连银行', '', 1, 1, 1668679839, 1668679839, 'DLB'),
(121, '丹东银行', '', 1, 1, 1668679859, 1668679859, '丹东银行'),
(122, '东莞农村商业银行', '', 1, 1, 1668679885, 1668679885, 'DRCB'),
(123, '东亚银行', '', 1, 1, 1668679909, 1668679909, 'HKBEA'),
(124, '东营银行', '', 1, 1, 1668679932, 1668679932, 'DYCCB'),
(126, '抚顺银行', '', 1, 1, 1668680017, 1668680017, 'FSCB'),
(127, '阜新银行', '', 1, 1, 1668680036, 1668680036, 'BOFX'),
(128, '富滇银行', '', 1, 1, 1668680060, 1668680060, 'FDB'),
(130, '甘肃银行', '', 1, 1, 1668680115, 1668680115, 'GSBANK'),
(131, '赣州银行', '', 1, 1, 1668680142, 1668680142, 'GZB'),
(132, '广东发展银行', '', 1, 1, 1668680165, 1668680165, 'CGB'),
(133, '广东华兴银行', '', 1, 1, 1668680185, 1668680185, 'GHB'),
(134, '广东南粤银行', '', 1, 1, 1668680248, 1668680248, 'GDNY'),
(135, '广东农商银行', '', 1, 1, 1668680284, 1668680284, 'GZRCB'),
(136, '广东农信', '', 1, 1, 1668680305, 1668680305, 'GDRC'),
(138, '广东省农村信用社联合社', '', 1, 1, 1668680353, 1668680353, 'GDRCC'),
(139, '广西北部湾银行', '', 1, 1, 1668680456, 1668680456, 'BGB'),
(140, '乐山商业银行', '', 1, 1, 1668680480, 1668680480, 'LSCCB'),
(141, '广西农信银行', '', 1, 1, 1668680539, 1668687535, 'GXNX'),
(142, '广州农商银行', '', 1, 1, 1668680577, 1668680577, 'GRCB'),
(143, '贵阳银行', '', 1, 1, 1668680598, 1668680598, 'GYCB'),
(144, '贵州省农村信用社联合社', '', 1, 1, 1668680621, 1668680621, 'GZRCU'),
(145, '贵州银行', '', 1, 1, 1668680651, 1668680651, 'ZYCBANK'),
(146, '桂林国民银行', '', 1, 1, 1668680672, 1668680672, 'GLGM'),
(147, '哈尔滨银行', '', 1, 1, 1668680706, 1668680706, 'HRBANK'),
(148, '海南农信', '', 1, 1, 1668680729, 1668680729, 'HNB'),
(149, '海南银行', '', 1, 1, 1668680759, 1668680759, 'HNBANK'),
(150, '邯郸银行', '', 1, 1, 1668680800, 1668680800, 'HDBANK'),
(151, '汉口银行', '', 1, 1, 1668680819, 1668680819, 'HKB'),
(152, '杭州联合银行', '', 1, 1, 1668680839, 1668680839, 'URCB'),
(153, '杭州银行', '', 1, 1, 1668680863, 1668680863, 'HZCB'),
(154, '河北银行', '', 1, 1, 1668680884, 1668680884, 'BHB'),
(156, '黑龙江农信', '', 1, 1, 1668680922, 1668680922, 'HLJRCU'),
(157, '恒丰银行', '', 1, 1, 1668680944, 1668680944, 'EGBANK'),
(158, '河北农信银行', '', 1, 1, 1668680994, 1668680994, 'HEBNX'),
(159, '湖北省农信社', '', 1, 1, 1668681016, 1668681016, 'HURCB'),
(160, '湖北银行', '', 1, 1, 1668681036, 1668681036, 'HBC'),
(162, '华融湘江银行', '', 1, 1, 1668681127, 1668681127, 'HRXJB'),
(163, '华夏银行', '', 1, 1, 1668681148, 1668681148, 'HXB'),
(164, '桦甸惠民村镇银行', '', 1, 1, 1668681171, 1668681171, 'HDHMB'),
(165, '黄河农信银行', '', 1, 1, 1668681189, 1668681189, 'HHNX'),
(166, '吉林农信银行', '', 1, 1, 1668681284, 1668681284, 'JLRCU'),
(167, '吉林银行', '', 1, 1, 1668681305, 1668681305, 'JLBANK'),
(168, '济宁银行', '', 1, 1, 1668681326, 1668681326, 'BOJN'),
(169, '江门农商银行', '', 1, 1, 1668681355, 1668681355, 'JMRCB'),
(170, '江南农村商业银行', '', 1, 1, 1668681376, 1668681376, 'JNRCB'),
(171, '江苏农商银行', '', 1, 1, 1668681394, 1668681394, 'JRCB'),
(173, '江苏省农村信用社联合社', '', 1, 1, 1668681442, 1668681442, 'JSRCU'),
(174, '江苏银行', '', 1, 1, 1668681509, 1668681509, 'JSBC'),
(176, '江西银行', '', 1, 1, 1668681557, 1668681557, 'NCB'),
(177, '金华银行', '', 1, 1, 1668681577, 1668681577, 'JHBANK'),
(178, '锦州银行', '', 1, 1, 1668681596, 1668681596, 'BOJZ'),
(179, '晋城银行', '', 1, 1, 1668681614, 1668681614, 'JCCB'),
(180, '昆仑银行', '', 1, 1, 1668682291, 1668682291, 'KLB'),
(181, '昆山农商银行', '', 1, 1, 1668682311, 1668682311, 'KSRB'),
(182, '兰州银行', '', 1, 1, 1668682348, 1668682348, 'LZYH'),
(183, '廊坊银行', '', 1, 1, 1668682367, 1668682367, 'LANGFB'),
(184, '唐山银行', '', 1, 1, 1668682388, 1668682388, 'TSB'),
(186, '辽阳银行', '', 1, 1, 1668682424, 1668682424, 'LYCB'),
(187, '临商银行', '', 1, 1, 1668682453, 1668682453, 'LSBC'),
(188, '柳州银行', '', 1, 1, 1668682483, 1668682483, 'LZCCB'),
(189, '龙江银行', '', 1, 1, 1668682504, 1668682504, 'LJBANK'),
(190, '洛阳银行', '', 1, 1, 1668682526, 1668682526, 'LYBANK'),
(191, '绵阳商业银行', '', 1, 1, 1668682544, 1668682544, 'MYCC'),
(192, '民泰银行', '', 1, 1, 1668682563, 1668682563, 'MTBANK'),
(193, '南海农商银行', '', 1, 1, 1668682584, 1668682584, 'NRCB'),
(194, '南京银行', '', 1, 1, 1668682614, 1668682614, 'NJCB'),
(195, '宁夏黄河农村商业银行', '', 1, 1, 1668682631, 1668682631, 'NXRCU'),
(196, '宁夏银行', '', 1, 1, 1668682653, 1668682653, 'NXBANK'),
(197, '齐鲁银行', '', 1, 1, 1668682670, 1668682670, 'QLBANK'),
(198, '齐商银行', '', 1, 1, 1668682692, 1668682692, 'QSB'),
(199, '青岛农商银行', '', 1, 1, 1668682714, 1668682714, 'QRCB'),
(200, '青岛银行', '', 1, 1, 1668682736, 1668682736, 'QDCCB'),
(201, '青海银行', '', 1, 1, 1668682754, 1668682754, 'QHRC'),
(202, '日照银行', '', 1, 1, 1668682771, 1668682771, 'RZB'),
(203, '厦门国际银行', '', 1, 1, 1668682788, 1668682788, 'XMIB'),
(204, '厦门国际银行', '', 1, 1, 1668682789, 1668682789, 'XMIB'),
(205, '厦门银行', '', 1, 1, 1668682806, 1668682806, 'BOXM'),
(207, '山西农信', '', 1, 1, 1668682843, 1668682843, 'SRCU'),
(208, '上海农商银行', '', 1, 1, 1668682875, 1668682875, 'SRCBANK'),
(209, '上海银行', '', 1, 1, 1668682893, 1668682893, 'SHBANK'),
(210, '上虞农商银行', '', 1, 1, 1668682912, 1668682912, 'SYCB'),
(211, '绍兴银行', '', 1, 1, 1668682932, 1668682932, 'SXCB'),
(212, '深圳福田银座村镇银行', '', 1, 1, 1668682949, 1668682949, 'FTYZB'),
(213, '云南农信银行', '', 1, 1, 1668682970, 1668682970, 'YNRCC'),
(214, '深圳农商银行', '', 1, 1, 1668682993, 1668682993, 'SRCB'),
(215, '深圳前海微众银行', '', 1, 1, 1668683010, 1668683010, 'WEBANK'),
(216, '盛京银行', '', 1, 1, 1668683030, 1668683030, 'SJBANK'),
(217, '石嘴山银行', '', 1, 1, 1668683125, 1668683125, 'SZSCCB'),
(218, '顺德农村商业银行', '', 1, 1, 1668683144, 1668683144, 'SDEBANK'),
(220, '四川天府银行', '', 1, 1, 1668683190, 1668683190, 'PWEB'),
(221, '苏州农商银行', '', 1, 1, 1668683213, 1668683213, 'WJRCB'),
(222, '苏州银行', '', 1, 1, 1668683232, 1668683232, 'BOSZ'),
(223, '台州银行', '', 1, 1, 1668683255, 1668683255, 'TZCB'),
(224, '太仓农商银行', '', 1, 1, 1668683304, 1668683304, 'TCRCB'),
(225, '泰安银行', '', 1, 1, 1668683349, 1668683349, 'TACCB'),
(226, '承德银行', '', 1, 1, 1668683370, 1668683370, 'CDBANK'),
(227, '泰隆银行', '', 1, 1, 1668683404, 1668683404, 'ZJTLCB'),
(228, '天津滨海农村商业银行', '', 1, 1, 1668683427, 1668683427, 'TJBHB'),
(229, '天津农商银行', '', 1, 1, 1668683452, 1668683452, 'TRCB'),
(230, '天津银行', '', 1, 1, 1668683611, 1668683611, 'TCCB'),
(231, '威海银行', '', 1, 1, 1668683640, 1668683640, 'WHCCB'),
(232, '微信固码', '', 1, 1, 1668683674, 1668683674, 'WSGM'),
(233, '温州银行', '', 1, 1, 1668683695, 1668683695, 'WZCB'),
(234, '五常惠民村镇银行', '', 1, 1, 1668683716, 1668683716, 'WCHMB'),
(235, '武汉农商银行', '', 1, 1, 1668683749, 1668683749, 'WHRCB'),
(236, '西安银行', '', 1, 1, 1668683766, 1668683766, 'XABANK'),
(237, '萧山农商银行', '', 1, 1, 1668683785, 1668683785, 'ZJXSBANK'),
(238, '邢台银行', '', 1, 1, 1668683811, 1668683811, 'XTB'),
(239, '烟台银行', '', 1, 1, 1668683830, 1668683830, 'YTBANK'),
(240, '阳泉商业银行', '', 1, 1, 1668683850, 1668683850, 'YQCCB'),
(241, '银座村镇银行', '', 1, 1, 1668683868, 1668683868, 'YZBANK'),
(242, '营口银行', '', 1, 1, 1668683887, 1668683887, 'BOYK'),
(243, '枣庄银行', '', 1, 1, 1668683915, 1668683915, 'ZZB'),
(244, '张家港农商银行', '', 1, 1, 1668683934, 1668683934, 'RCBOZ'),
(245, '张家口银行', '', 1, 1, 1668683972, 1668683972, 'ZJKCCB'),
(246, '长安银行', '', 1, 1, 1668683998, 1668683998, 'CCAB'),
(247, '长江商业银行', '', 1, 1, 1668684022, 1668684022, 'JSCCB'),
(248, '长沙农商银行', '', 1, 1, 1668684041, 1668684041, 'CRCB'),
(249, '长沙银行', '', 1, 1, 1668684207, 1668684207, 'CSCB'),
(250, '浙江农村信用合作社', '', 1, 1, 1668684226, 1668684226, 'ZJNX'),
(251, '鞍山银行', '', 1, 1, 1668684264, 1668684264, 'ASBANK'),
(252, '浙江网商银行', '', 1, 1, 1668684283, 1668684283, 'MYBANK'),
(253, '浙商银行', '', 1, 1, 1668684333, 1668684333, 'CZBANK'),
(254, '郑州银行', '', 1, 1, 1668684352, 1668684352, 'ZZBANK'),
(255, '中银富登村镇银行', '', 1, 1, 1668684369, 1668684369, 'BOCFTB'),
(256, '长春经开融丰村镇银行', '', 1, 1, 1668684472, 1668684472, 'CCRFCB'),
(257, '中原银行', '', 1, 1, 1668684489, 1668684489, 'ZYBANK'),
(258, '重庆农村商业银行', '', 1, 1, 1668684514, 1668684514, 'CRCBANK'),
(259, '重庆三峡银行', '', 1, 1, 1668684527, 1668684527, 'CCQTGB'),
(260, '重庆银行', '', 1, 1, 1668684548, 1668684548, 'CQBANK'),
(261, '珠海华润银行', '', 1, 1, 1668684565, 1668684565, 'CRBANK'),
(262, '紫金农商银行', '', 1, 1, 1668684582, 1668684582, 'ZJRCBANK'),
(263, '自贡银行', '', 1, 1, 1668684597, 1668684597, 'ZGCCB'),
(264, '南阳村镇银行', '', 1, 1, 1668684665, 1668684665, 'NYCBANK'),
(265, '衡水银行', '', 1, 1, 1668684682, 1668684682, 'HSB'),
(266, '平顶山银行', '', 1, 1, 1668684701, 1668684701, 'PDSB'),
(268, '内蒙古银行', '', 1, 1, 1668684739, 1668684739, 'BOIMC'),
(269, '惠水恒升村镇银行', '', 1, 1, 1668684756, 1668684756, 'HSHSB'),
(270, '朝阳银行', '', 1, 1, 1668684772, 1668684772, 'CYCB'),
(271, '江西农商银行', '', 1, 1, 1668684793, 1668684793, 'JXNXS'),
(272, '贵阳农商银行', '', 1, 1, 1668684869, 1668684869, 'GYNSH'),
(273, '泉州银行', '', 1, 1, 1668684887, 1668684887, 'QZCCB'),
(274, '安图农商村镇银行', '', 1, 1, 1668684904, 1668684904, 'ATCZB'),
(275, '融兴村镇银行', '', 1, 1, 1668684930, 1668684930, 'RXVB'),
(276, '宁波通商银行', '', 1, 1, 1668684950, 1668684950, 'NCBANK'),
(277, '天津宁河村镇银行', '', 1, 1, 1668684971, 1668684971, 'NINGHEB'),
(278, '广州增城长江村镇银行', '', 1, 1, 1668684993, 1668684993, 'ZCCJB'),
(279, '鄞州银行', '', 1, 1, 1668685012, 1668685012, 'BEEB'),
(280, '海丰农商银行', '', 1, 1, 1668685029, 1668685029, 'GFRCB'),
(281, '上饶银行', '', 1, 1, 1668685048, 1668685048, 'SRBANK'),
(282, '黄梅农村商业银行', '', 1, 1, 1668685065, 1668685065, 'HMRCB'),
(284, '密山农商银行', '', 1, 1, 1668685100, 1668685100, 'MSRCB'),
(285, '义乌联合村镇银行', '', 1, 1, 1668685116, 1668685116, 'ZJYURB'),
(286, '广东普宁汇成村镇银行', '', 1, 1, 1668685132, 1668685132, 'GDPHCB'),
(287, '浙江诸暨联合村镇银行', '', 1, 1, 1668685160, 1668685160, 'ZJURB'),
(288, '浙江农商银行', '', 1, 1, 1668685194, 1668685194, 'ZJRCB'),
(289, '永州农村商业银行', '', 1, 1, 1668685215, 1668685215, 'HNNXS'),
(290, '潮州农商银行', '', 1, 1, 1668685235, 1668685235, 'GRCBANK'),
(291, '义乌农商银行', '', 1, 1, 1668685258, 1668685258, 'YRCBANK'),
(292, '海口联合农商银行', '', 1, 1, 1668685275, 1668685275, 'UNITEDB'),
(293, '瑞丰银行', '', 1, 1, 1668685298, 1668685298, 'BORF'),
(294, '山西银行', '', 1, 1, 1668685318, 1668685318, 'SHXIBANK'),
(295, '海峡银行', '', 1, 1, 1668685338, 1668685338, 'FJHXBANK'),
(296, '东莞市商业银行', '东莞市商业银行', 0, 1, 1668837444, 1668837444, 'DWSYYH'),
(297, '河北省农村信用社', '河北省农村信用社', 0, 1, 1669554974, 1669554974, 'HBNCXYS');

-- --------------------------------------------------------

--
-- 表的结构 `cm_banktobank_sms`
--

CREATE TABLE `cm_banktobank_sms` (
  `id` int(11) UNSIGNED NOT NULL,
  `phone` varchar(255) NOT NULL COMMENT '来短信的号码',
  `context` text COMMENT '短信内容',
  `ip` varchar(255) DEFAULT NULL,
  `ms_id` int(11) DEFAULT NULL COMMENT '码商id',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '短信自动回调订单ID',
  `create_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_config`
--

CREATE TABLE `cm_config` (
  `id` bigint(10) UNSIGNED NOT NULL COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置标题',
  `type` int(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '配置类型',
  `sort` smallint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `group` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '配置分组',
  `value` text NOT NULL COMMENT '配置值',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置选项',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '配置说明',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `admin_id` int(11) NOT NULL DEFAULT '1' COMMENT '所属管理员ID'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='基本配置表';

--
-- 转存表中的数据 `cm_config`
--

INSERT INTO `cm_config` (`id`, `name`, `title`, `type`, `sort`, `group`, `value`, `extra`, `describe`, `status`, `create_time`, `update_time`, `admin_id`) VALUES
(1, 'seo_title', '网站标题', 1, 1, 0, '三方', '', '', 1, 1378898976, 1585677353, 1),
(8, 'email_port', 'SMTP端口号', 1, 8, 1, '2', '1:25,2:465', '如：一般为 25 或 465', 1, 1378898976, 1545131349, 1),
(2, 'seo_description', '网站描述', 2, 3, 0, '', '', '网站搜索引擎描述，优先级低于SEO模块', 1, 1378898976, 1585677353, 1),
(3, 'seo_keywords', '网站关键字', 2, 4, 0, '三方', '', '网站搜索引擎关键字，优先级低于SEO模块', 1, 1378898976, 1585677353, 1),
(4, 'app_index_title', '首页标题', 1, 2, 0, '三方', '', '', 1, 1378898976, 1585677353, 1),
(5, 'app_domain', '网站域名', 1, 5, 0, '', '', '网站域名', 1, 1378898976, 1585677353, 1),
(6, 'app_copyright', '版权信息', 2, 6, 0, '三方', '', '版权信息', 1, 1378898976, 1585677353, 1),
(7, 'email_host', 'SMTP服务器', 3, 7, 1, '2', '1:smtp.163.com,2:smtp.aliyun.com,3:smtp.qq.com', '如：smtp.163.com', 1, 1378898976, 1569507595, 1),
(9, 'send_email', '发件人邮箱', 1, 9, 1, '12345@qq.com', '', '', 1, 1378898976, 1569507595, 1),
(10, 'send_nickname', '发件人昵称', 1, 10, 1, '', '', '', 1, 1378898976, 1569507595, 1),
(11, 'email_password', '邮箱密码', 1, 11, 1, 'xxxxxx', '', '', 1, 1378898976, 1569507595, 1),
(12, 'rsa_public_key', '平台数据公钥', 2, 6, 0, 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxV1hB4NP1NFgEM0mrx34z8gJMPBIhvDjAJcnMozk3jmUY9PkB7lZyfD6Fb+Xq21jIPX5zF4ggeYoK5keUH6TW9eJEr5JOqDl2YgKAdLfxLuJ4r8X1S3wflVp2/BURIbP1VGh6qNAxS3o8miL7x5BZ+jOhs4/LCq8YkncZioui5eAQ+/BoE++uM5IeSWZEVf8JsGo+MrOG2E/eOqetrB08Tm68igM6OMbKr05HKupcZm63zzDIHRJGKRjvdFjVoVznGsAC3phyh3bzYrjxykH00mLyw39/77MiBMp/uWVMh6wwiAjY2B25IKXXGCd0JSYvlpJWtCKbxlcAGDWSWkS0wIDAQAB', '', '平台数据公钥（RSA 2048）', 1, 1378898976, 1585677353, 1),
(13, 'rsa_private_key', '平台数据私钥', 2, 6, 0, 'MIIEpAIBAAKCAQEAxV1hB4NP1NFgEM0mrx34z8gJMPBIhvDjAJcnMozk3jmUY9PkB7lZyfD6Fb+Xq21jIPX5zF4ggeYoK5keUH6TW9eJEr5JOqDl2YgKAdLfxLuJ4r8X1S3wflVp2/BURIbP1VGh6qNAxS3o8miL7x5BZ+jOhs4/LCq8YkncZioui5eAQ+/BoE++uM5IeSWZEVf8JsGo+MrOG2E/eOqetrB08Tm68igM6OMbKr05HKupcZm63zzDIHRJGKRjvdFjVoVznGsAC3phyh3bzYrjxykH00mLyw39/77MiBMp/uWVMh6wwiAjY2B25IKXXGCd0JSYvlpJWtCKbxlcAGDWSWkS0wIDAQABAoIBAFeeoB/8vOlHVrW+zii6Tqa4MNRoKFq4AJ9Xe5BmmojJ2UYEYNzI/cK4V95l44i4lGSirxZ6x0XEDxtj6+BigTsp0fHfRpVfrwtG6OJsYultNMbUfVkn/venJcr9w/t0OjqC9jY76dpgCmXr4gvzS6g848tXLxaFloKwNcepfGZ9wQb8Kt+5ONzn3BUcczu4DhuWfkt6oQ4j1KPl0UIdLZ7tevG1guUUr15p6VGsvQtMh4U7Lct/+0XUp4chut6fvoAIbEHnAE8rkAZBjrICwsYKNANNBEgVhtn5sK12RVZdUEd3vBWry9YOk1dgsEmi+chqQFlD18bO5/phIXEpK4kCgYEA7mugHzBcr53tSJVwh4IkyXQOs+gW5wSqbjHhTafN29w4qOJ9ZAxELogz4gQ25Yn95l1gpOY0cyH5x6QHsPFuJJBJp9sEiGplYSsCalK1qJaQewvAMd1Ctqk5A67QHgE/4xh+id9l+e1a9SKNqg3X3X1QdLddzwoq0i1Oj407KnUCgYEA0+rLqcJC0swSIchWpWLKQ/kgu093CXVvDoTugWPuHi4Ua49/9nPv0zSjMX5GXzGZ7CAQca/Gwg24R6bvc8wgwe9OYf8/ILQ3XUHmZJIHMXD/HuZqBMn/Swu62MJalOYTOsKp4hxNvxJkZPpku6gr5C611LaOsbE6iQDyeqmtzycCgYAeVGClNxDDYnK6BhCvnFWzrujj6AVp1AUeSYggydT9QBGRImbTIGBYDwmSmfil0J0U/hH6SDKp5suQowQ7dSsOybAlA06bT/Wfm8oN3oGvdZ/hl0gWz8/ZzsMq/cUJ3BzVdds7DMk7Nv+YKZId7O7mBTgD8QOk/+UcoZjZ2ByLtQKBgQCPP99OMJfVQMdc+LzBbWdGzYf3tj7EMRLSYL+MzY0v73w0PTuF0FckkSdjlHVjcfcXa5FSGD0l/fo8zTZ+M1VNY0O78LuuksP+EUb5YtDj9fsu2xh9hkJBa3txfOeYUXJcPSxzQSi46Wjd7XjcdVC+HWkikgkhSqlD5VUD3+Ey7wKBgQDtarpiVV19/IWiRbKy7rKJcG1HnezqfoA7outJK6yG7ne1vTjkGD/BLTSJm032htPFRmrwxhDOz0EilCjCz+ID2iPWKzhiZpf5yZ/qoFrFdofNWhLyAzNzxDhAZbcVG6ebjkMfHj84sChenGk31HfuplMD0GBe8DlC7UGerxCu1A==', '', '平台数据私钥（RSA 2048）', 1, 1378898976, 1585677353, 1),
(16, 'logo', 'ç«ç¹LOGO', 4, 6, 0, '', '', 'ä¸ä¼ ç«ç¹logo', 1, 1378898976, 1576391324, 1),
(14, 'withdraw_fee', '提现手续费', 1, 6, 0, '5', '', '提现手续费', 1, 1378898976, 1585677353, 1),
(15, 'thrid_url_gumapay', 'åºå®ç è¯·æ±å°å', 1, 6, 0, 'http://45.207.58.203//index.php', '', 'åºå®ç ç¬¬ä¸æ¹apiè¯·æ±å°å', 1, 1378898976, 1585677353, 1),
(18, 'auth_key', 'éä¿¡ç§é¥', 1, 7, 0, 'XforgXQl2746FBIT', '', 'ä¸è·å¹³å°éä¿¡ç§é¥', 1, 1378898976, 1585677353, 1),
(19, 'four_noticy_time', '四方通知时间', 1, 8, 0, '201', '', '四方码商回调通知时间(单位分钟)', 1, 1378898976, 1585677353, 1),
(20, 'max_withdraw_limit', '提现最大金额', 0, 0, 0, '600000000', '', '', 1, 0, 1585677353, 1),
(21, 'min_withdraw_limit', '提现最小金额', 0, 0, 0, '99', '', '', 1, 0, 1585677353, 1),
(22, 'balance_cash_type', '提现申请类型', 3, 0, 0, '2', '1:选择账号,2:手动填写账号', '', 1, 0, 1585677353, 1),
(23, 'request_pay_type', '发起支付订单类型', 3, 0, 0, '2', '1:平台订单号,2:下游订单号', '', 1, 0, 1584606747, 1),
(24, 'notify_ip', '回调ip', 0, 54, 0, '', '', '', 1, 0, 1585677353, 1),
(25, 'is_single_handling_charge', '是否开启单笔手续费', 3, 51, 0, '1', '1:开启,0:不开启', '', 1, 0, 1585677353, 1),
(26, 'whether_open_daifu', '是否开启代付', 3, 50, 0, '1', '1:开启,2:不开启', '', 1, 0, 1585677353, 1),
(27, 'index_view_path', '前台模板', 3, 0, 0, 'view', 'view:默认,baisha:白沙,view1:版本2', '', 1, 0, 1585833746, 1),
(28, 'is_open_channel_fund', '渠道资金是否开启', 3, 0, 0, '0', '0:关闭,1:开启', '', 1, 0, 0, 1),
(29, 'is_paid_select_channel', '提现审核选择渠道', 3, 0, 0, '1', '0:不选择,1:选择', '', 1, 0, 0, 1),
(30, 'balance_cash_adminlist', '提现列表url', 0, 0, 0, '/api/withdraw/getAdminList', '', '', 1, 0, 0, 1),
(31, 'balance_cash_revocation', '提现撤回url', 0, 0, 0, '/api/withdraw/revocation', '', '', 1, 0, 0, 1),
(32, 'daifu_notify_ip', '代付回调ip白名单', 1, 0, 0, '127.0.0.1', '', '', 1, 0, 0, 1),
(33, 'daifu_host', '代付接口地址', 1, 0, 0, '', '', '', 1, 0, 0, 1),
(34, 'daifu_key', '跑分密钥', 1, 0, 0, '3e9c1885afa5920909f9b9aa2907cf19', '', '', 1, 0, 0, 1),
(35, 'daifu_notify_url', '回调地址', 1, 0, 0, '', '', '', 1, 0, 0, 1),
(36, 'transfer_ip_list', '中转ip白名单', 2, 0, 0, '127.0.0.1', '', '多个使用逗号隔开', 1, 0, 0, 1),
(37, 'proxy_debug', '是否开启中转回调', 3, 0, 0, '1', '1:开启,0:不开启', '', 1, 0, 0, 1),
(38, 'orginal_host', '中转回调地址', 0, 0, 0, 'http://8.217.66.247/zz.php', '', '', 1, 0, 0, 1),
(39, 'daifu_admin_id', '代付admin_id', 1, 0, 0, '5', '', '', 1, 0, 0, 1),
(40, 'is_channel_statistics', '是否开启渠道统计', 3, 0, 0, '0', '1:开启,0:不开启', '', 1, 0, 0, 1),
(41, 'admin_view_path', '后台模板', 3, 0, 0, 'view', 'view:默认,baisha:白沙', '', 1, 0, 1585833746, 1),
(42, 'index_domain_white_list', '前台域名白名单', 1, 0, 0, '', '', '如https://www.baidu.com/ 请输入www.baidu.com', 1, 0, 0, 1),
(43, 'pay_domain_white_list', '下单域名白名单', 0, 0, 0, '', '', '如https://www.baidu.com/ 请输入www.baidu.com', 1, 0, 0, 1),
(44, 'admin_domain_white_list', '后台域名白名单', 0, 0, 0, '', '', '如https://www.baidu.com/ 请输入www.baidu.com', 1, 0, 0, 1),
(1111, 'global_tgbot_token', '全 局机器人token唯一标识', 1, 0, 0, '1673522495:AAE6-JDXf3z5ZSk7pFoLkwR6XYzkv_jMg_g', '', '', 1, 0, 0, 1),
(1112, 'tg_order_warning_robot_token', '订单报警机器人token', 0, 0, 0, '1673522495:AAE6-JDXf3z5ZSk7pFoLkwR6XYzkv_jMg_g', '', '', 1, 0, 0, 1),
(1113, 'tg_order_warning_rebot_in_chat', '订单机器人所在群组', 0, 0, 0, '-449166252', '', '', 1, 0, 0, 1),
(1114, 'withdraw_usdt_rate', 'ustd下发手续费', 1, 6, 0, '0', '', '', 1, 0, 0, 1),
(1115, 'daifu_ms_id', '代付码商ID', 1, 0, 0, '', '', '', 1, 1657884061, 1657884061, 1),
(1116, 'daifu_tgbot_token', '代付机器人token', 1, 0, 0, '5488115037:AAHCWwtjhGtj3ZcrYUTD4815pAPjtOk2bvc', '', '', 1, 1660383105, 1660383105, 1),
(1117, 'daifu_min_amount', '代付最小金额', 1, 0, 0, '1', '', '', 1, 1661930871, 1661930871, 1),
(1118, 'daifu_max_amount', '代付最大金额', 1, 0, 0, '500000', '', '', 1, 1661930895, 1661930895, 1),
(1120, 'daifu_err_reason', '代付失败原因', 6, 0, 3, '收款账户与户名不符,收款卡问题请更换卡再提交,支付中断,转账失败,收款方账户异常,银行维护,手机号对应多个绑定支付宝,收款账号未实名,收款账号收到支付宝风控', '', '', 1, 0, 0, 1),
(1121, 'thrid_url_uid', 'UID中转地址', 1, 6, 0, 'http://45.207.58.203/uid.php', '', 'UID中转地址', 1, 1378898976, 1378898976, 1),
(1141, 'sys_enable_recharge', '系统启用充值', 3, 0, 0, '1', '1:开启,0:不开启', '', 1, 1666212478, 1666212478, 1),
(1196, 'sys_operating_password', '设置操作口令', 256, 0, 0, '1552c03e78d38d5005d4ce7b8018addf', '', '', 1, 1676870947, 1676870947, 1),
(1197, 'sys_operating_password', '设置操作口令', 256, 0, 0, '69b54760b8ca3a4083b044a432bdd23a', '', '', 1, 1676898910, 1676898910, 2),
(1198, 'sys_operating_password', '设置操作口令', 256, 0, 0, 'f23d2bfc9303b224c280c42aa8982de7', '', '', 1, 1676960462, 1676960462, 3),
(1199, 'money_is_float', '订单金额浮动(1为开启，2为关闭)', 41, 0, 0, '1', '', '', 1, 1676965244, 1677145462, 3),
(1200, 'usdt_recharge_address', 'usdt充值地址', 1, 0, 0, 'TMAdjy4u5v3qqJrBNyD7zrqyLNRNAvrd3k', '', '', 1, 1666212478, 1666212478, 1),
(1201, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '2000', '', '', 1, 1677137508, 1677310661, 3),
(1202, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1677137508, 1677310661, 3),
(1203, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1677137508, 1677310661, 3),
(1204, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1677137508, 1677310661, 3),
(1205, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1677137508, 1677310661, 3),
(1206, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1677137508, 1677310661, 3),
(1207, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1677137508, 1677310661, 3),
(1208, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1677137508, 1677310661, 3),
(1209, 'cashier_address', '收银台服务器地址', 256, 0, 0, '2', '0:香港云-【ip：45.207.58.203】,1:国内-【ip：183.131.85.59】,2:香港物理-【ip：43.225.47.56】- 推荐,3:新加坡-【ip：97.74.94.29】', '', 1, 1677137508, 1677310661, 3),
(1210, 'max_ewm_num', '最大二维码数量', 41, 0, 0, '200', '', '', 1, 1677138676, 1677145462, 3),
(1211, 'sys_operating_password', '设置操作口令', 256, 0, 0, 'fdac3771e58892b926740266a55fdad6', '', '', 1, 1677237145, 1677237145, 7),
(1212, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '1000', '', '', 1, 1677238348, 1677238348, 7),
(1213, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0.3', '', '', 1, 1677238348, 1677238348, 7),
(1214, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '00', '', '', 1, 1677238348, 1677238348, 7),
(1215, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '09', '', '', 1, 1677238348, 1677238348, 7),
(1216, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1677238348, 1677238348, 7),
(1217, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1677238348, 1677238348, 7),
(1218, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1677238348, 1677238348, 7),
(1219, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1677238348, 1677238348, 7),
(1220, 'cashier_address', '收银台服务器地址', 256, 0, 0, '2', '0:香港云-【ip：45.207.58.203】,1:国内-【ip：183.131.85.59】,2:香港物理-【ip：43.225.47.56】- 推荐,3:新加坡-【ip：97.74.94.29】', '', 1, 1677238348, 1677238348, 7),
(1221, 'sys_operating_password', '设置操作口令', 256, 0, 0, '65bb86549756830caa529e032f829eb2', '', '', 1, 1677338715, 1677338715, 9),
(1222, 'order_invalid_time', '订单超时时间(分钟)', 30, 0, 0, '5', '', '', 1, 1677671656, 1680856747, 9),
(1223, 'get_pay_name_type', '获取支付用户姓名方式', 30, 0, 0, '1', '', '', 1, 1677671656, 1680856747, 9),
(1224, 'is_pay_name', '支付页面是否提交姓名', 30, 0, 0, '2', '1:开启,2:关闭', '', 1, 1677671656, 1680856747, 9),
(1225, 'order_amount_float_range', '订单浮动金额个数', 30, 0, 0, '20', '', '', 1, 1677671656, 1680856747, 9),
(1226, 'order_amount_float_no_top', '订单金额不上浮', 30, 0, 0, '1', '1:开启,2:关闭', '', 1, 1677671656, 1680856747, 9),
(1227, 'money_is_float', '订单金额浮动', 30, 0, 0, '1', '1:开启,2:关闭', '', 1, 1677671656, 1680856747, 9),
(1228, 'verify_ms_callback_ip', '验证码商回调ip', 30, 0, 0, '2', '1:开启,2:关闭', '', 1, 1677671656, 1680856747, 9),
(1229, 'max_ewm_num', '最大二维码数量', 30, 0, 0, '20', '', '', 1, 1677671656, 1680856747, 9),
(1230, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '2000', '', '', 1, 1677688910, 1681023672, 9),
(1231, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1677688910, 1681023672, 9),
(1232, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1677688910, 1681023672, 9),
(1233, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1677688910, 1681023672, 9),
(1234, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1677688910, 1681023672, 9),
(1235, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1677688910, 1681023672, 9),
(1236, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1677688910, 1681023672, 9),
(1237, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1677688910, 1681023672, 9),
(1238, 'cashier_address', '收银台服务器地址', 256, 0, 0, '0', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】,9:https专属线路-【', '', 1, 1677688910, 1681023672, 9),
(1239, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1678534398, 1681023672, 9),
(1240, 'usdt_recharge_address', 'usdtå……å€¼åœ°å€', 1, 0, 0, 'TMAdjy4u5v3qqJrBNyD7zrqyLNRNAvrd3k', '', '', 1, 1666212478, 1666212478, 1),
(1241, 'usdt_rate', 'usdtå……å€¼è´¹çŽ‡', 1, 0, 0, '6.8', '', '', 1, 1666212478, 1666212478, 1),
(1242, 'sys_code_type', '系统派单方式', 256, 0, 0, '1', '0:顺序轮询,1:随机权重分配', '', 1, 1679199714, 1681023672, 9),
(1243, 'usdt_recharge_address', 'usdtå……å€¼åœ°å€', 1, 0, 0, 'TMAdjy4u5v3qqJrBNyD7zrqyLNRNAvrd3k', '', '', 1, 1666212478, 1666212478, 1),
(1244, 'usdt_rate', 'usdtå……å€¼è´¹çŽ‡', 1, 0, 0, '6.8', '', '', 1, 1666212478, 1666212478, 1),
(1245, 'money_is_float', '订单金额浮动', 43, 0, 0, '2', '1:开启,2:关闭', '', 1, 1679676241, 1679676241, 10),
(1246, 'order_invalid_time', '订单超时时间(分钟)', 43, 0, 0, '10', '', '', 1, 1679676241, 1679676241, 10),
(1247, 'is_pay_pass', '确认收款是否需要确认安全码', 43, 0, 0, '1', '1:开启,2:关闭', '', 1, 1679676241, 1679676241, 10),
(1248, 'is_amount_lock', '是否锁码', 43, 0, 0, '2', '1:开启,2:关闭', '', 1, 1679676241, 1679676241, 10),
(1249, 'ms_show_card', '码商后台是否展示卡密', 43, 0, 0, '1', '1:开启,2:关闭', '', 1, 1679676241, 1679676241, 10),
(1250, 'max_ewm_num', '最大二维码数量', 43, 0, 0, '20', '', '', 1, 1679676241, 1679676241, 10),
(1251, 'cardKey_order_view', '卡密上传后显示订单', 43, 0, 0, '2', '1:开启,2:关闭', '', 1, 1679676241, 1679676241, 10),
(1252, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1679680736, 1679886636, 10),
(1253, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '2000', '', '', 1, 1679680736, 1679886636, 10),
(1254, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1679680736, 1679886636, 10),
(1255, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1679680736, 1679886636, 10),
(1256, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1679680736, 1679886636, 10),
(1257, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1679680736, 1679886636, 10),
(1258, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1679680736, 1679886636, 10),
(1259, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1679680736, 1679886636, 10),
(1260, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1679680736, 1679886636, 10),
(1261, 'cashier_address', '收银台服务器地址', 256, 0, 0, '1', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：47.242.51.211】-【阿里云】,2:线路2-【ip：43.225.47.56】-【56】,3:线路3-【ip：97.74.94.29】-【29】,4:线路4-【ip：43.225.47.59】-【56】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】,8:线路8-【ip：45.207.', '', 1, 1679680736, 1679886636, 10),
(1262, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1679680736, 1679886636, 10),
(1263, 'usdt_recharge_address', 'usdtå……å€¼åœ°å€', 1, 0, 0, 'TMAdjy4u5v3qqJrBNyD7zrqyLNRNAvrd3k', '', '', 1, 1666212478, 1666212478, 1),
(1264, 'usdt_rate', 'usdtå……å€¼è´¹çŽ‡', 1, 0, 0, '6.8', '', '', 1, 1666212478, 1666212478, 1),
(1265, 'sys_operating_password', '设置操作口令', 256, 0, 0, '8a6f2805b4515ac12058e79e66539be9', '', '', 1, 1680244484, 1680244484, 11),
(1266, 'money_is_float', '订单金额浮动', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680244675, 1681874242, 11),
(1267, 'is_pay_pass', '确认收款是否需要确认安全码', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680244675, 1681874242, 11),
(1268, 'order_invalid_time', '订单超时时间(分钟)', 40, 0, 0, '5', '', '', 1, 1680244675, 1681874242, 11),
(1269, 'is_show_not_success', '自动刷新时是否只显示未支付订单', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680244675, 1681874242, 11),
(1270, 'is_confirm_box', '确认收款时是否确认信息', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680244675, 1681874242, 11),
(1271, 'max_ewm_num', '最大二维码数量', 40, 0, 0, '20', '', '', 1, 1680244675, 1681874242, 11),
(1272, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1680325060, 1680445616, 17),
(1273, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '2000', '', '', 1, 1680325060, 1680445616, 17),
(1274, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680325060, 1680445616, 17),
(1275, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680325060, 1680445616, 17),
(1276, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680325060, 1680445616, 17),
(1277, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680325060, 1680445616, 17),
(1278, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680325060, 1680445616, 17),
(1279, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680325060, 1680445616, 17),
(1280, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1680325060, 1680445616, 17),
(1281, 'cashier_address', '收银台服务器地址', 256, 0, 0, '2', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.88.133】-【阿里云】,3:线路3-【ip：47.243.86.51】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:国内专线-【ip：43.228.69.11】-【11】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】,9:https专属线路-【', '', 1, 1680325060, 1680445616, 17),
(1282, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1680325060, 1680445616, 17),
(1283, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1680325060, 1680445616, 17),
(1284, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1680349934, 1681833982, 14),
(1285, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '2000', '', '', 1, 1680349934, 1681833982, 14),
(1286, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680349934, 1681833982, 14),
(1287, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680349934, 1681833982, 14),
(1288, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680349934, 1681833982, 14),
(1289, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680349934, 1681833982, 14),
(1290, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680349934, 1681833982, 14),
(1291, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680349934, 1681833982, 14),
(1292, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1680349934, 1681833982, 14),
(1293, 'cashier_address', '收银台服务器地址', 256, 0, 0, '3', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云香港】,3:线路3-【ip：8.217.66.247】-【阿里云备用香港】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云杭州】,8:阿里云国内-【ip：47.96.133.197】-【阿里云杭州】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:', '', 1, 1680349934, 1681833982, 14),
(1294, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1680349934, 1681833982, 14),
(1295, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1680349934, 1681833982, 14),
(1296, 'money_is_float', '订单金额浮动', 56, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680352020, 1681482603, 14),
(1297, 'is_amount_lock', '锁码', 56, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680352020, 1681482603, 14),
(1298, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1680442052, 1681969631, 12),
(1299, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '2000', '', '', 1, 1680442052, 1681969631, 12),
(1300, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680442052, 1681969631, 12),
(1301, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680442052, 1681969631, 12),
(1302, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680442052, 1681969631, 12),
(1303, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680442052, 1681969631, 12),
(1304, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680442052, 1681969631, 12),
(1305, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680442052, 1681969631, 12),
(1306, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1680442052, 1681969631, 12),
(1307, 'cashier_address', '收银台服务器地址', 256, 0, 0, '1', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云香港】,3:线路3-【ip：8.217.66.247】-【阿里云备用香港】,11:上海线路-【ip：47.103.22.166】-【阿里云上海】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云杭州】,8:阿里云国内-【ip：47.96.133.197】-【阿里云杭州】,5:线路5-【ip：43.225.47.61】-【5', '', 1, 1680442052, 1681969631, 12),
(1308, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1680442052, 1681969631, 12),
(1309, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1680442052, 1681969631, 12),
(1310, 'is_amount_lock', '锁码', 59, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680461237, 1680598104, 20),
(1311, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1680478562, 1681747723, 21),
(1312, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '2000', '', '', 1, 1680478562, 1681747723, 21),
(1313, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680478562, 1681747723, 21),
(1314, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680478562, 1681747723, 21),
(1315, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680478562, 1681747723, 21),
(1316, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680478562, 1681747723, 21),
(1317, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680478562, 1681747723, 21),
(1318, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680478562, 1681747723, 21),
(1319, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1680478562, 1681747723, 21),
(1320, 'cashier_address', '收银台服务器地址', 256, 0, 0, '4', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云香港】,3:线路3-【ip：8.217.66.247】-【阿里云备用香港】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云杭州】,8:阿里云国内-【ip：47.96.133.197】-【阿里云杭州】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:', '', 1, 1680478562, 1681747723, 21),
(1321, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1680478562, 1681747723, 21),
(1322, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '2', '2:关闭,1:开启', '', 1, 1680478562, 1681747723, 21),
(1323, 'money_is_float', '订单金额浮动', 45, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680525950, 1680586496, 21),
(1324, 'is_amount_lock', '是否锁码', 45, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680525950, 1680586496, 21),
(1325, 'max_ewm_num', '最大二维码数量', 45, 0, 0, '1000000', '', '', 1, 1680525950, 1680586496, 21),
(1326, 'is_amount_lock', '锁码', 59, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680530906, 1680606917, 23),
(1327, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1680537107, 1681292255, 11),
(1328, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '1000', '', '', 1, 1680537107, 1681292255, 11),
(1329, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680537107, 1681292255, 11),
(1330, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680537107, 1681292255, 11),
(1331, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680537107, 1681292255, 11),
(1332, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680537107, 1681292255, 11),
(1333, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680537107, 1681292255, 11),
(1334, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680537107, 1681292255, 11),
(1335, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1680537107, 1681292255, 11),
(1336, 'cashier_address', '收银台服务器地址', 256, 0, 0, '8', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云香港】,3:线路3-【ip：8.217.66.247】-【阿里云备用香港】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云杭州】,8:阿里云国内-【ip：47.96.133.197】-【阿里云杭州】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:', '', 1, 1680537107, 1681292255, 11),
(1337, 'sys_code_type', '系统派单方式', 256, 0, 0, '1', '0:顺序轮询,1:随机权重分配', '', 1, 1680537107, 1681292255, 11),
(1338, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1680537107, 1681292255, 11),
(1339, 'pay_template', '支付页面模板', 59, 0, 0, '2', '1:模板-1,2:模板-核销模式', '', 1, 1680598104, 1680598104, 20),
(1340, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1680600257, 1680600257, 20),
(1341, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '2000', '', '', 1, 1680600257, 1680600257, 20),
(1342, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680600257, 1680600257, 20),
(1343, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680600257, 1680600257, 20),
(1344, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680600257, 1680600257, 20),
(1345, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680600257, 1680600257, 20),
(1346, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680600257, 1680600257, 20),
(1347, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680600257, 1680600257, 20),
(1348, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1680600257, 1680600257, 20),
(1349, 'cashier_address', '收银台服务器地址', 256, 0, 0, '0', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:国内专线-【ip：43.228.69.11】-【11】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】,9:https专属线路-【非', '', 1, 1680600257, 1680600257, 20),
(1350, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1680600257, 1680600257, 20),
(1351, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1680600257, 1680600257, 20),
(1352, 'pay_template', '支付页面模板', 59, 0, 0, '2', '1:模板-1,2:模板-核销模式', '', 1, 1680606087, 1680606917, 23),
(1353, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1680606244, 1680606870, 23),
(1354, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '2000', '', '', 1, 1680606244, 1680606870, 23),
(1355, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680606244, 1680606870, 23),
(1356, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680606244, 1680606870, 23),
(1357, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680606244, 1680606870, 23),
(1358, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680606244, 1680606870, 23),
(1359, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680606244, 1680606870, 23),
(1360, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680606244, 1680606870, 23),
(1361, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1680606244, 1680606870, 23),
(1362, 'cashier_address', '收银台服务器地址', 256, 0, 0, '0', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:国内专线-【ip：43.228.69.11】-【11】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】,9:https专属线路-【非', '', 1, 1680606244, 1680606870, 23),
(1363, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1680606244, 1680606870, 23),
(1364, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1680606244, 1680606870, 23),
(1365, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1680610630, 1681023672, 9),
(1366, 'order_invalid_time', '订单超时时间(分钟)', 39, 0, 0, '5', '', '', 1, 1680657187, 1681757426, 21),
(1367, 'money_is_float', '订单金额浮动', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680657187, 1681757426, 21),
(1368, 'is_pay_name', '支付页面是否提交姓名', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680657187, 1681757426, 21),
(1369, 'order_amount_float_no_top', '订单金额不上浮', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680657187, 1681757426, 21),
(1370, 'pay_template', '支付页面模板', 39, 0, 0, '1', '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】', '', 1, 1680657187, 1681757426, 21),
(1371, 'order_amount_float_range', '订单浮动金额个数', 39, 0, 0, '10', '', '', 1, 1680657187, 1681757426, 21),
(1372, 'api_min_money', '收款码收款最小金额', 39, 0, 0, '0', '', '', 1, 1680657187, 1681757426, 21),
(1373, 'api_max_money', '收款码收款最大金额', 39, 0, 0, '0', '', '', 1, 1680657187, 1681757426, 21),
(1374, 'max_ewm_num', '最大二维码数量', 39, 0, 0, '200000', '', '', 1, 1680657187, 1681757426, 21),
(1375, 'money_is_float', '订单金额浮动', 34, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680745322, 1680745322, 21),
(1376, 'max_ewm_num', '最大二维码数量', 34, 0, 0, '20000000', '', '', 1, 1680745322, 1680745322, 21),
(1377, 'money_is_float', '订单金额浮动', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680764977, 1681215719, 25),
(1378, 'is_pay_pass', '确认收款是否需要确认安全码', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680764977, 1681215719, 25),
(1379, 'order_invalid_time', '订单超时时间(分钟)', 40, 0, 0, '5', '', '', 1, 1680764977, 1681215719, 25),
(1380, 'is_show_not_success', '自动刷新时是否只显示未支付订单', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680764977, 1681215719, 25),
(1381, 'is_confirm_box', '确认收款时是否确认信息', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680764977, 1681215719, 25),
(1382, 'max_ewm_num', '最大二维码数量', 40, 0, 0, '20', '', '', 1, 1680764977, 1681215719, 25),
(1383, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1680812510, 1681365541, 25),
(1384, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '100', '', '', 1, 1680812510, 1681365541, 25),
(1385, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680812510, 1681365541, 25),
(1386, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680812510, 1681365541, 25),
(1387, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680812510, 1681365541, 25),
(1388, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1680812510, 1681365541, 25),
(1389, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1680812510, 1681365541, 25),
(1390, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1680812510, 1681365541, 25),
(1391, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1680812510, 1681365541, 25),
(1392, 'one_notify_address', '第一次回调线路', 256, 0, 0, '1', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680812510, 1681365541, 25),
(1393, 'two_notify_address', '第二次回调线路', 256, 0, 0, '1', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680812510, 1681365541, 25),
(1394, 'three_notify_address', '第三次回调线路', 256, 0, 0, '1', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680812510, 1681365541, 25),
(1395, 'cashier_address', '收银台服务器地址', 256, 0, 0, '1', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云香港】,3:线路3-【ip：8.217.66.247】-【阿里云备用香港】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云杭州】,8:阿里云国内-【ip：47.96.133.197】-【阿里云杭州】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:', '', 1, 1680812510, 1681365541, 25),
(1396, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1680812510, 1681365541, 25),
(1397, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1680812510, 1681365541, 25),
(1398, 'one_notify_address', '第一次回调线路', 256, 0, 0, '1', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680843729, 1681364182, 12),
(1399, 'two_notify_address', '第二次回调线路', 256, 0, 0, '1', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680843729, 1681364182, 12),
(1400, 'three_notify_address', '第三次回调线路', 256, 0, 0, '1', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680843729, 1681364182, 12),
(1401, 'api_min_money', '收款码收款最小金额', 33, 0, 0, '300', '', '', 1, 1680847032, 1680847032, 28),
(1402, 'api_max_money', '收款码收款最大金额', 33, 0, 0, '5000', '', '', 1, 1680847032, 1680847032, 28),
(1403, 'is_show_name', '收银台展示收款人姓名', 33, 0, 0, '2', '1:启用,2:关闭', '', 1, 1680847032, 1680847032, 28),
(1404, 'is_h5', '是否启用支付宝H5', 33, 0, 0, '2', '1:启用,2:关闭', '', 1, 1680847032, 1680847032, 28),
(1405, 'money_is_float', '订单金额浮动', 33, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680847032, 1680847032, 28),
(1406, 'is_pay_name', '支付页面是否提交姓名', 33, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680847032, 1680847032, 28),
(1407, 'order_amount_float_range', '订单浮动金额个数', 33, 0, 0, '50', '', '', 1, 1680847032, 1680847032, 28),
(1408, 'order_amount_float_no_top', '订单金额不上浮', 33, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680847032, 1680847032, 28),
(1409, 'max_ewm_num', '最大二维码数量', 33, 0, 0, '50', '', '', 1, 1680847032, 1680847032, 28),
(1410, 'order_invalid_time', '订单超时时间(分钟)', 39, 0, 0, '10', '', '', 1, 1680847367, 1680847367, 28),
(1411, 'money_is_float', '订单金额浮动', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680847367, 1680847367, 28),
(1412, 'is_pay_name', '支付页面是否提交姓名', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1680847367, 1680847367, 28),
(1413, 'order_amount_float_no_top', '订单金额不上浮', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680847367, 1680847367, 28),
(1414, 'pay_template', '支付页面模板', 39, 0, 0, '1', '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】', '', 1, 1680847367, 1680847367, 28),
(1415, 'order_amount_float_range', '订单浮动金额个数', 39, 0, 0, '50', '', '', 1, 1680847367, 1680847367, 28),
(1416, 'api_min_money', '收款码收款最小金额', 39, 0, 0, '0', '', '', 1, 1680847367, 1680847367, 28),
(1417, 'api_max_money', '收款码收款最大金额', 39, 0, 0, '0', '', '', 1, 1680847367, 1680847367, 28),
(1418, 'max_ewm_num', '最大二维码数量', 39, 0, 0, '50', '', '', 1, 1680847367, 1680847367, 28),
(1419, 'money_close_code', '余额1w禁用收款码', 30, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680855550, 1680856747, 9),
(1420, 'kzk_pay_template', '收银台模板', 30, 0, 0, '3', '1:原始模板,2:最新模板,3:不要支付宝提示模板', '', 1, 1680855550, 1680856747, 9),
(1421, 'one_notify_address', '第一次回调线路', 256, 0, 0, '99999', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680867159, 1681287609, 21),
(1422, 'two_notify_address', '第二次回调线路', 256, 0, 0, '99999', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680867159, 1681287609, 21),
(1423, 'three_notify_address', '第三次回调线路', 256, 0, 0, '99999', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680867159, 1681287609, 21),
(1424, 'one_notify_address', '第一次回调线路', 256, 0, 0, '3', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680867170, 1681309374, 14),
(1425, 'two_notify_address', '第二次回调线路', 256, 0, 0, '3', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680867170, 1681309374, 14),
(1426, 'three_notify_address', '第三次回调线路', 256, 0, 0, '3', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1680867170, 1681309374, 14),
(1427, 'one_notify_address', '第一次回调线路', 256, 0, 0, '0', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】,9:https专属线路-【', '', 1, 1680942014, 1681023672, 9),
(1428, 'two_notify_address', '第二次回调线路', 256, 0, 0, '0', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】,9:https专属线路-【', '', 1, 1680942014, 1681023672, 9),
(1429, 'three_notify_address', '第三次回调线路', 256, 0, 0, '0', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】,9:https专属线路-【', '', 1, 1680942014, 1681023672, 9),
(1430, 'money_is_float', '订单金额浮动', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680952338, 1680952338, 21),
(1431, 'is_pay_pass', '确认收款是否需要确认安全码', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680952338, 1680952338, 21),
(1432, 'order_invalid_time', '订单超时时间(分钟)', 40, 0, 0, '10', '', '', 1, 1680952338, 1680952338, 21),
(1433, 'is_show_not_success', '自动刷新时是否只显示未支付订单', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680952338, 1680952338, 21),
(1434, 'is_confirm_box', '确认收款时是否确认信息', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1680952338, 1680952338, 21),
(1435, 'max_ewm_num', '最大二维码数量', 40, 0, 0, '2000000', '', '', 1, 1680952338, 1680952338, 21),
(1436, 'money_is_float', '订单金额浮动', 50, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681028599, 1681057931, 21),
(1437, 'order_amount_float_no_top', '订单金额不上浮', 50, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681028599, 1681057931, 21),
(1438, 'order_invalid_time', '订单超时时间(分钟)', 39, 0, 0, '10', '', '', 1, 1681034194, 1681138457, 12),
(1439, 'money_is_float', '订单金额浮动', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681034194, 1681138457, 12),
(1440, 'is_pay_name', '支付页面是否提交姓名', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681034194, 1681138457, 12),
(1441, 'order_amount_float_no_top', '订单金额不上浮', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681034194, 1681138457, 12),
(1442, 'pay_template', '支付页面模板', 39, 0, 0, '1', '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】', '', 1, 1681034194, 1681138457, 12),
(1443, 'order_amount_float_range', '订单浮动金额个数', 39, 0, 0, '20', '', '', 1, 1681034194, 1681138457, 12),
(1444, 'ms_code_money_status', '码商设置收款码金额区间', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681034194, 1681138457, 12),
(1445, 'api_min_money', '收款码收款最小金额', 39, 0, 0, '0', '', '', 1, 1681034194, 1681138457, 12),
(1446, 'api_max_money', '收款码收款最大金额', 39, 0, 0, '0', '', '', 1, 1681034194, 1681138457, 12),
(1447, 'max_ewm_num', '最大二维码数量', 39, 0, 0, '100', '', '', 1, 1681034194, 1681138457, 12),
(1448, 'money_is_float', '订单金额浮动', 45, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681035126, 1681035126, 12),
(1449, 'is_amount_lock', '是否锁码', 45, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681035126, 1681035126, 12),
(1450, 'max_ewm_num', '最大二维码数量', 45, 0, 0, '20', '', '', 1, 1681035126, 1681035126, 12),
(1451, 'channel_show', '是否展示通道', 50, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681057724, 1681057931, 21),
(1452, 'channel_show', '是否展示通道', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681102986, 1681138457, 12),
(1453, 'is_pay_name', '支付页面是否提交姓名', 58, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681125203, 1681125203, 12),
(1454, 'money_is_float', '订单金额浮动', 58, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681125203, 1681125203, 12),
(1455, 'channel_show', '是否展示通道', 58, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681125203, 1681125203, 12),
(1456, 'ms_code_money_status', '码商设置收款码金额区间', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681193520, 1681215719, 25),
(1457, 'api_min_money', '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)', 40, 0, 0, '', '', '', 1, 1681193520, 1681215719, 25),
(1458, 'api_max_money', '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)', 40, 0, 0, '0', '', '', 1, 1681193520, 1681215719, 25),
(1459, 'channel_show', '是否展示通道', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681193520, 1681215719, 25),
(1460, 'money_is_float', '订单金额浮动', 50, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681198817, 1681211155, 24),
(1461, 'order_amount_float_no_top', '订单金额不上浮', 50, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681198817, 1681211155, 24),
(1462, 'channel_show', '是否展示通道', 50, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681198817, 1681211155, 24),
(1463, 'order_amount_float_range', '订单浮动金额个数', 40, 0, 0, '20', '', '', 1, 1681204911, 1681215719, 25),
(1464, 'order_amount_float_no_top', '订单金额不上浮', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681204911, 1681215719, 25),
(1465, 'one_notify_address', '第一次回调线路', 256, 0, 0, '1', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1681206724, 1681292255, 11),
(1466, 'two_notify_address', '第二次回调线路', 256, 0, 0, '3', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1681206724, 1681292255, 11),
(1467, 'three_notify_address', '第三次回调线路', 256, 0, 0, '4', '99999:回调服务器1,11:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云】,3:线路3-【ip：8.217.66.247】-【阿里云备用】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:线路7-【ip：45.207.58.73】-【203】', '', 1, 1681206724, 1681292255, 11),
(1468, 'disable_ms_money_status', '码商进单冻结余额（正在跑量的时候切勿操作，否则后果自负！！！）', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1681206724, 1681292255, 11),
(1469, 'order_amount_float_range', '订单浮动金额个数', 50, 0, 0, '20', '', '', 1, 1681211155, 1681211155, 24),
(1470, 'order_invalid_time', '订单超时时间(分钟)', 45, 0, 0, '10', '', '', 1, 1681214488, 1681216563, 27),
(1471, 'money_is_float', '订单金额浮动', 45, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681214488, 1681216563, 27),
(1472, 'is_amount_lock', '是否锁码', 45, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681214488, 1681216563, 27),
(1473, 'max_ewm_num', '最大二维码数量', 45, 0, 0, '200', '', '', 1, 1681214488, 1681216563, 27),
(1474, 'channel_show', '是否展示通道', 45, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681214488, 1681216563, 27),
(1475, 'disable_ms_money_status', '码商进单冻结余额（正在跑量的时候切勿操作，否则后果自负！！！）', 256, 0, 0, '2', '2:关闭,1:开启', '', 1, 1681216956, 1681969631, 12),
(1476, 'order_invalid_time', '订单超时时间(分钟)', 32, 0, 0, '10', '', '', 1, 1681220453, 1681220453, 27),
(1477, 'is_pay_name', '支付页面是否提交姓名', 32, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681220453, 1681220453, 27),
(1478, 'money_is_float', '订单金额浮动', 32, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681220453, 1681220453, 27),
(1479, 'is_h5', '是否启用支付宝H5', 32, 0, 0, '2', '1:启用,2:关闭', '', 1, 1681220453, 1681220453, 27),
(1480, 'is_show_name', '收银台展示收款人姓名', 32, 0, 0, '2', '1:启用,2:关闭', '', 1, 1681220453, 1681220453, 27),
(1481, 'order_amount_float_range', '订单浮动金额个数', 32, 0, 0, '20', '', '', 1, 1681220453, 1681220453, 27),
(1482, 'order_amount_float_no_top', '订单金额不上浮', 32, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681220453, 1681220453, 27),
(1483, 'ms_code_money_status', '码商设置收款码金额区间', 32, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681220453, 1681220453, 27),
(1484, 'api_min_money', '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)', 32, 0, 0, '0', '', '', 1, 1681220453, 1681220453, 27),
(1485, 'api_max_money', '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)', 32, 0, 0, '0', '', '', 1, 1681220453, 1681220453, 27),
(1486, 'max_ewm_num', '最大二维码数量', 32, 0, 0, '200', '', '', 1, 1681220453, 1681220453, 27),
(1487, 'channel_show', '是否展示通道', 32, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681220453, 1681220453, 27),
(1488, 'order_amount_float_range', '订单浮动金额个数', 40, 0, 0, '20', '', '', 1, 1681264108, 1681874242, 11),
(1489, 'order_amount_float_no_top', '订单金额不上浮', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681264108, 1681874242, 11),
(1490, 'ms_code_money_status', '码商设置收款码金额区间', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681264108, 1681874242, 11),
(1491, 'api_min_money', '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)', 40, 0, 0, '0', '', '', 1, 1681264108, 1681874242, 11),
(1492, 'api_max_money', '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)', 40, 0, 0, '0', '', '', 1, 1681264108, 1681874242, 11),
(1493, 'channel_show', '是否展示通道', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681264108, 1681874242, 11),
(1496, 'disable_ms_money_status', '码商进单冻结余额（正在跑量的时候切勿操作，否则后果自负！！！）', 256, 0, 0, '2', '2:关闭,1:开启', '', 1, 1681309374, 1681833982, 14),
(1494, 'disable_ms_money_status', '码商进单冻结余额（正在跑量的时候切勿操作，否则后果自负！！！）', 256, 0, 0, '2', '2:关闭,1:开启', '', 1, 1681286302, 1681365541, 25),
(1495, 'disable_ms_money_status', '码商进单冻结余额（正在跑量的时候切勿操作，否则后果自负！！！）', 256, 0, 0, '2', '2:关闭,1:开启', '', 1, 1681287609, 1681747723, 21),
(1497, 'order_invalid_time', '订单超时时间(分钟)', 39, 0, 0, '2', '', '', 1, 1681377220, 1681378108, 29),
(1498, 'money_is_float', '订单金额浮动', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681377220, 1681378108, 29),
(1499, 'is_pay_name', '支付页面是否提交姓名', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681377220, 1681378108, 29),
(1500, 'order_amount_float_no_top', '订单金额不上浮', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681377220, 1681378108, 29),
(1501, 'pay_template', '支付页面模板', 39, 0, 0, '1', '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】', '', 1, 1681377220, 1681378108, 29),
(1502, 'order_amount_float_range', '订单浮动金额个数', 39, 0, 0, '0', '', '', 1, 1681377220, 1681378108, 29),
(1503, 'ms_code_money_status', '码商设置收款码金额区间', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681377220, 1681378108, 29),
(1504, 'api_min_money', '收款码收款最小金额', 39, 0, 0, '0', '', '', 1, 1681377220, 1681378108, 29),
(1505, 'api_max_money', '收款码收款最大金额', 39, 0, 0, '0', '', '', 1, 1681377220, 1681378108, 29),
(1506, 'max_ewm_num', '最大二维码数量', 39, 0, 0, '20', '', '', 1, 1681377220, 1681378108, 29),
(1507, 'channel_show', '是否展示通道', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681377220, 1681378108, 29),
(1508, 'user_withdraw_google_code_on', '商户提现开启google验证码', 256, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681390634, 1681969631, 12),
(1509, 'user_withdraw_google_code_on', '商户提现开启google验证码', 256, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681456724, 1681747723, 21),
(1510, 'is_pay_name', '支付页面是否提交姓名', 42, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681465104, 1681465104, 12),
(1511, 'money_is_float', '订单金额浮动', 42, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681465104, 1681465104, 12),
(1512, 'order_invalid_time', '订单超时时间(分钟)', 42, 0, 0, '10', '', '', 1, 1681465104, 1681465104, 12),
(1513, 'pay_template', '支付模板', 42, 0, 0, '1', '1:默认模板,2:gif引导', '', 1, 1681465104, 1681465104, 12),
(1514, 'max_ewm_num', '最大二维码数量', 42, 0, 0, '20', '', '', 1, 1681465104, 1681465104, 12),
(1515, 'channel_show', '是否展示通道', 42, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681465104, 1681465104, 12),
(1516, 'user_withdraw_google_code_on', '商户提现开启google验证码', 256, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681481219, 1681833982, 14),
(1517, 'order_invalid_time', '订单超时时间(分钟)', 56, 0, 0, '10', '', '', 1, 1681482603, 1681482603, 14),
(1518, 'channel_show', '是否展示通道', 56, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681482603, 1681482603, 14),
(1519, 'order_invalid_time', '订单超时时间(分钟)', 57, 0, 0, '10', '', '', 1, 1681634442, 1681634526, 33),
(1520, 'money_is_float', '订单金额浮动', 57, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634442, 1681634526, 33),
(1521, 'is_amount_lock', '锁码', 57, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634442, 1681634526, 33),
(1522, 'order_reason', '订单商品名称(用逗号隔开随机出1个)', 57, 0, 0, '护肤品,生活品,苹果,香蕉', '', '', 1, 1681634442, 1681634526, 33),
(1523, 'channel_show', '是否展示通道', 57, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634442, 1681634526, 33),
(1524, 'pay_template', '支付页面模板', 59, 0, 0, '1', '1:模板-1,2:模板-核销模式', '', 1, 1681634511, 1681634511, 33),
(1525, 'order_invalid_time', '订单超时时间(分钟)', 59, 0, 0, '10', '', '', 1, 1681634511, 1681634511, 33),
(1526, 'is_amount_lock', '锁码', 59, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634511, 1681634511, 33),
(1527, 'channel_show', '是否展示通道', 59, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634511, 1681634511, 33),
(1528, 'order_invalid_time', '订单超时时间(分钟)', 58, 0, 0, '10', '', '', 1, 1681634520, 1681634520, 33),
(1529, 'is_pay_name', '支付页面是否提交姓名', 58, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634520, 1681634520, 33),
(1530, 'money_is_float', '订单金额浮动', 58, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634520, 1681634520, 33),
(1531, 'channel_show', '是否展示通道', 58, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634520, 1681634520, 33),
(1532, 'order_invalid_time', '订单超时时间(分钟)', 56, 0, 0, '10', '', '', 1, 1681634531, 1681634531, 33),
(1533, 'money_is_float', '订单金额浮动', 56, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634531, 1681634531, 33),
(1534, 'is_amount_lock', '锁码', 56, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634531, 1681634531, 33),
(1535, 'channel_show', '是否展示通道', 56, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634531, 1681634531, 33),
(1536, 'order_invalid_time', '订单超时时间(分钟)', 55, 0, 0, '10', '', '', 1, 1681634539, 1681634539, 33),
(1537, 'is_h5', '是否启用支付宝H5', 55, 0, 0, '2', '1:启用,2:关闭', '', 1, 1681634539, 1681634539, 33),
(1538, 'is_wechat', '支持微信', 55, 0, 0, '1', '1:支持,2:不支持', '', 1, 1681634539, 1681634539, 33),
(1539, 'is_pay_name', '支付页面是否提交姓名', 55, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634539, 1681634539, 33),
(1540, 'money_is_float', '订单金额浮动', 55, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634539, 1681634539, 33),
(1541, 'channel_show', '是否展示通道', 55, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634539, 1681634539, 33),
(1542, 'money_is_float', '订单金额浮动', 50, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634547, 1681634547, 33),
(1543, 'order_amount_float_range', '订单浮动金额个数', 50, 0, 0, '20', '', '', 1, 1681634547, 1681634547, 33),
(1544, 'order_amount_float_no_top', '订单金额不上浮', 50, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634547, 1681634547, 33),
(1545, 'channel_show', '是否展示通道', 50, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634547, 1681634547, 33);
INSERT INTO `cm_config` (`id`, `name`, `title`, `type`, `sort`, `group`, `value`, `extra`, `describe`, `status`, `create_time`, `update_time`, `admin_id`) VALUES
(1546, 'channel_show', '是否展示通道', 51, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634553, 1681634553, 33),
(1547, 'channel_show', '是否展示通道', 48, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634559, 1681634559, 33),
(1548, 'order_invalid_time', '订单超时时间(分钟)', 46, 0, 0, '10', '', '', 1, 1681634567, 1681634567, 33),
(1549, 'money_is_float', '订单金额浮动', 46, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634567, 1681634567, 33),
(1550, 'channel_show', '是否展示通道', 46, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634567, 1681634567, 33),
(1551, 'is_amount_lock', '是否锁码', 47, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634574, 1681634574, 33),
(1552, 'order_invalid_time', '订单超时时间(分钟)', 47, 0, 0, '10', '', '', 1, 1681634574, 1681634574, 33),
(1553, 'money_is_float', '订单金额浮动', 47, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634574, 1681634574, 33),
(1554, 'channel_show', '是否展示通道', 47, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634574, 1681634574, 33),
(1555, 'money_is_float', '订单金额浮动', 44, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634594, 1681634594, 33),
(1556, 'ewm_is_pay_money', '二维码是否有固定支付金额（1为有，2为没有）', 44, 0, 0, '1', '1:存在,2:没有', '', 1, 1681634594, 1681634594, 33),
(1557, 'is_pay_name', '支付页面是否提交姓名', 44, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634594, 1681634594, 33),
(1558, 'order_invalid_time', '订单超时时间(分钟)', 44, 0, 0, '10', '', '', 1, 1681634594, 1681634594, 33),
(1559, 'max_ewm_num', '最大二维码数量', 44, 0, 0, '20', '', '', 1, 1681634594, 1681634594, 33),
(1560, 'channel_show', '是否展示通道', 44, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634594, 1681634594, 33),
(1561, 'money_is_float', '订单金额浮动', 43, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634601, 1681634601, 33),
(1562, 'order_invalid_time', '订单超时时间(分钟)', 43, 0, 0, '10', '', '', 1, 1681634601, 1681634601, 33),
(1563, 'is_pay_pass', '确认收款是否需要确认安全码', 43, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634601, 1681634601, 33),
(1564, 'is_amount_lock', '是否锁码', 43, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634601, 1681634601, 33),
(1565, 'ms_show_card', '码商后台是否展示卡密', 43, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634601, 1681634601, 33),
(1566, 'max_ewm_num', '最大二维码数量', 43, 0, 0, '20', '', '', 1, 1681634601, 1681634601, 33),
(1567, 'cardKey_order_view', '卡密上传后显示订单', 43, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634601, 1681634601, 33),
(1568, 'channel_show', '是否展示通道', 43, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634601, 1681634601, 33),
(1569, 'is_pay_name', '支付页面是否提交姓名', 42, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634612, 1681634612, 33),
(1570, 'money_is_float', '订单金额浮动', 42, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634612, 1681634612, 33),
(1571, 'order_invalid_time', '订单超时时间(分钟)', 42, 0, 0, '10', '', '', 1, 1681634612, 1681634612, 33),
(1572, 'pay_template', '支付模板', 42, 0, 0, '1', '1:默认模板,2:gif引导', '', 1, 1681634612, 1681634612, 33),
(1573, 'max_ewm_num', '最大二维码数量', 42, 0, 0, '20', '', '', 1, 1681634612, 1681634612, 33),
(1574, 'channel_show', '是否展示通道', 42, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634612, 1681634612, 33),
(1575, 'max_ewm_num', '最大二维码数量', 38, 0, 0, '20', '', '', 1, 1681634620, 1681634620, 33),
(1576, 'channel_show', '是否展示通道', 38, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634620, 1681634620, 33),
(1577, 'order_invalid_time', '订单超时时间(分钟)', 37, 0, 0, '10', '', '', 1, 1681634628, 1681634628, 33),
(1578, 'money_is_float', '订单金额浮动', 37, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634628, 1681634628, 33),
(1579, 'is_amount_lock', '是否锁码', 37, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634628, 1681634628, 33),
(1580, 'is_pay_name', '支付页面是否提交姓名', 37, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634628, 1681634628, 33),
(1581, 'max_ewm_num', '最大二维码数量', 37, 0, 0, '20', '', '', 1, 1681634628, 1681634628, 33),
(1582, 'channel_show', '是否展示通道', 37, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634628, 1681634628, 33),
(1583, 'order_invalid_time', '订单超时时间(分钟)', 30, 0, 0, '10', '', '', 1, 1681634639, 1681634855, 33),
(1584, 'get_pay_name_type', '获取支付用户姓名方式', 30, 0, 0, '1', '', '', 1, 1681634639, 1681634855, 33),
(1585, 'is_pay_name', '支付页面是否提交姓名', 30, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634639, 1681634855, 33),
(1586, 'order_amount_float_range', '订单浮动金额个数', 30, 0, 0, '20', '', '', 1, 1681634639, 1681634855, 33),
(1587, 'order_amount_float_no_top', '订单金额不上浮', 30, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634639, 1681634855, 33),
(1588, 'money_is_float', '订单金额浮动', 30, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634639, 1681634855, 33),
(1589, 'verify_ms_callback_ip', '验证码商回调ip', 30, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634639, 1681634855, 33),
(1590, 'money_close_code', '余额1w禁用收款码', 30, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634639, 1681634855, 33),
(1591, 'kzk_pay_template', '收银台模板', 30, 0, 0, '2', '1:原始模板,2:最新模板,3:不要支付宝提示模板', '', 1, 1681634639, 1681634855, 33),
(1592, 'max_ewm_num', '最大二维码数量', 30, 0, 0, '20', '', '', 1, 1681634639, 1681634855, 33),
(1593, 'channel_show', '是否展示通道', 30, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634639, 1681634855, 33),
(1594, 'money_is_float', '订单金额浮动', 52, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634648, 1681634648, 33),
(1595, 'order_invalid_time', '订单超时时间(分钟)', 52, 0, 0, '10', '', '', 1, 1681634648, 1681634648, 33),
(1596, 'is_pay_pass', '确认收款是否需要确认安全码', 52, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634648, 1681634648, 33),
(1597, 'is_amount_lock', '是否锁码', 52, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634648, 1681634648, 33),
(1598, 'cardKey_order_view', '卡密上传后显示订单', 52, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634648, 1681634648, 33),
(1599, 'channel_show', '是否展示通道', 52, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634648, 1681634648, 33),
(1600, 'order_invalid_time', '订单超时时间(分钟)', 34, 0, 0, '10', '', '', 1, 1681634728, 1681634728, 33),
(1601, 'money_is_float', '订单金额浮动', 34, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634728, 1681634728, 33),
(1602, 'max_ewm_num', '最大二维码数量', 34, 0, 0, '20', '', '', 1, 1681634728, 1681634728, 33),
(1603, 'channel_show', '是否展示通道', 34, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634728, 1681634728, 33),
(1604, 'money_is_float', '订单金额浮动', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634799, 1681634802, 33),
(1605, 'order_amount_float_range', '订单浮动金额个数', 40, 0, 0, '20', '', '', 1, 1681634799, 1681634802, 33),
(1606, 'order_amount_float_no_top', '订单金额不上浮', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634799, 1681634802, 33),
(1607, 'is_pay_pass', '确认收款是否需要确认安全码', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634799, 1681634802, 33),
(1608, 'order_invalid_time', '订单超时时间(分钟)', 40, 0, 0, '10', '', '', 1, 1681634799, 1681634802, 33),
(1609, 'is_show_not_success', '自动刷新时是否只显示未支付订单', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634799, 1681634802, 33),
(1610, 'is_confirm_box', '确认收款时是否确认信息', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634799, 1681634803, 33),
(1611, 'ms_code_money_status', '码商设置收款码金额区间', 40, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634799, 1681634803, 33),
(1612, 'api_min_money', '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)', 40, 0, 0, '0', '', '', 1, 1681634799, 1681634803, 33),
(1613, 'api_max_money', '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)', 40, 0, 0, '0', '', '', 1, 1681634799, 1681634803, 33),
(1614, 'max_ewm_num', '最大二维码数量', 40, 0, 0, '20', '', '', 1, 1681634799, 1681634803, 33),
(1615, 'channel_show', '是否展示通道', 40, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634799, 1681634803, 33),
(1616, 'order_invalid_time', '订单超时时间(分钟)', 36, 0, 0, '10', '', '', 1, 1681634827, 1681634831, 33),
(1617, 'money_is_float', '订单金额浮动', 36, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634827, 1681634831, 33),
(1618, 'is_amount_lock', '是否锁码', 36, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681634827, 1681634831, 33),
(1619, 'max_ewm_num', '最大二维码数量', 36, 0, 0, '20', '', '', 1, 1681634827, 1681634831, 33),
(1620, 'channel_show', '是否展示通道', 36, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681634827, 1681634831, 33),
(1621, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1681635102, 1681636070, 33),
(1622, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '1000', '', '', 1, 1681635102, 1681636070, 33),
(1623, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1681635102, 1681636070, 33),
(1624, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1681635102, 1681636070, 33),
(1625, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1681635102, 1681636070, 33),
(1626, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1681635102, 1681636070, 33),
(1627, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1681635102, 1681636070, 33),
(1628, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1681635102, 1681636070, 33),
(1629, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1681635102, 1681636070, 33),
(1630, 'cashier_address', '收银台服务器地址', 256, 0, 0, '3', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云香港】,3:线路3-【ip：8.217.66.247】-【阿里云备用香港】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云杭州】,8:阿里云国内-【ip：47.96.133.197】-【阿里云杭州】,5:线路5-【ip：43.225.47.61】-【56】,6:线路6-【ip：45.207.58.35】-【203】,7:', '', 1, 1681635102, 1681636070, 33),
(1631, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1681635102, 1681636070, 33),
(1632, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1681635102, 1681636070, 33),
(1633, 'disable_ms_money_status', '码商进单冻结余额（正在跑量的时候切勿操作，否则后果自负！！！）', 256, 0, 0, '2', '2:关闭,1:开启', '', 1, 1681635102, 1681636070, 33),
(1634, 'user_withdraw_google_code_on', '商户提现开启google验证码', 256, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681635102, 1681636070, 33),
(1635, 'order_invalid_time', '订单超时时间(分钟)', 39, 0, 0, '3', '', '', 1, 1681637471, 1681649283, 33),
(1636, 'money_is_float', '订单金额浮动', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681637471, 1681649283, 33),
(1637, 'is_pay_name', '支付页面是否提交姓名', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681637471, 1681649283, 33),
(1638, 'initialization_h5', '初始化页面跳转支付宝', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681637471, 1681649283, 33),
(1639, 'order_amount_float_no_top', '订单金额不上浮', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681637471, 1681649283, 33),
(1640, 'pay_template', '支付页面模板', 39, 0, 0, '1', '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】', '', 1, 1681637471, 1681649283, 33),
(1641, 'order_amount_float_range', '订单浮动金额个数', 39, 0, 0, '20', '', '', 1, 1681637471, 1681649283, 33),
(1642, 'ms_code_money_status', '码商设置收款码金额区间', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681637471, 1681649283, 33),
(1643, 'api_min_money', '收款码收款最小金额', 39, 0, 0, '1000', '', '', 1, 1681637471, 1681649283, 33),
(1644, 'api_max_money', '收款码收款最大金额', 39, 0, 0, '20000', '', '', 1, 1681637471, 1681649283, 33),
(1645, 'max_ewm_num', '最大二维码数量', 39, 0, 0, '20', '', '', 1, 1681637471, 1681649283, 33),
(1646, 'channel_show', '是否展示通道', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681637471, 1681649283, 33),
(1647, 'order_invalid_time', '订单超时时间(分钟)', 54, 0, 0, '3', '', '', 1, 1681637517, 1681649312, 33),
(1648, 'money_is_float', '订单金额浮动', 54, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681637517, 1681649312, 33),
(1649, 'is_pay_name', '支付页面是否提交姓名', 54, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681637517, 1681649312, 33),
(1650, 'order_amount_float_no_top', '订单金额不上浮', 54, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681637517, 1681649312, 33),
(1651, 'pay_template', '支付页面模板', 54, 0, 0, '1', '1:模板-1,2:模板-2【支付宝加好友转账】', '', 1, 1681637517, 1681649312, 33),
(1652, 'ms_code_money_status', '码商设置收款码金额区间', 54, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681637517, 1681649312, 33),
(1653, 'api_min_money', '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)', 54, 0, 0, '200', '', '', 1, 1681637517, 1681649312, 33),
(1654, 'api_max_money', '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)', 54, 0, 0, '1000', '', '', 1, 1681637517, 1681649312, 33),
(1655, 'channel_show', '是否展示通道', 54, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681637517, 1681649312, 33),
(1656, 'order_invalid_time', '订单超时时间(分钟)', 32, 0, 0, '10', '', '', 1, 1681637590, 1681637611, 33),
(1657, 'is_pay_name', '支付页面是否提交姓名', 32, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681637590, 1681637611, 33),
(1658, 'money_is_float', '订单金额浮动', 32, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681637590, 1681637611, 33),
(1659, 'is_h5', '是否启用支付宝H5', 32, 0, 0, '2', '1:启用,2:关闭', '', 1, 1681637590, 1681637611, 33),
(1660, 'is_show_name', '收银台展示收款人姓名', 32, 0, 0, '2', '1:启用,2:关闭', '', 1, 1681637590, 1681637611, 33),
(1661, 'order_amount_float_range', '订单浮动金额个数', 32, 0, 0, '20', '', '', 1, 1681637590, 1681637611, 33),
(1662, 'order_amount_float_no_top', '订单金额不上浮', 32, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681637590, 1681637611, 33),
(1663, 'ms_code_money_status', '码商设置收款码金额区间', 32, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681637590, 1681637611, 33),
(1664, 'api_min_money', '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)', 32, 0, 0, '0', '', '', 1, 1681637590, 1681637611, 33),
(1665, 'api_max_money', '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)', 32, 0, 0, '0', '', '', 1, 1681637590, 1681637611, 33),
(1666, 'max_ewm_num', '最大二维码数量', 32, 0, 0, '20', '', '', 1, 1681637590, 1681637611, 33),
(1667, 'channel_show', '是否展示通道', 32, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681637590, 1681637611, 33),
(1668, 'order_invalid_time', '订单超时时间(分钟)', 45, 0, 0, '5', '', '', 1, 1681656321, 1681656321, 33),
(1669, 'money_is_float', '订单金额浮动', 45, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681656321, 1681656321, 33),
(1670, 'is_amount_lock', '是否锁码', 45, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681656321, 1681656321, 33),
(1671, 'max_ewm_num', '最大二维码数量', 45, 0, 0, '20', '', '', 1, 1681656321, 1681656321, 33),
(1672, 'channel_show', '是否展示通道', 45, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681656321, 1681656321, 33),
(1673, 'initialization_h5', '初始化页面跳转支付宝', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681741935, 1681757426, 21),
(1674, 'ms_code_money_status', '码商设置收款码金额区间', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681741935, 1681757426, 21),
(1675, 'channel_show', '是否展示通道', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681741935, 1681757426, 21),
(1676, 'order_invalid_time', '订单超时时间(分钟)', 41, 0, 0, '10', '', '', 1, 1681816855, 1681816855, 24),
(1677, 'money_is_float', '订单金额浮动', 41, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681816855, 1681816855, 24),
(1678, 'is_amount_lock', '锁码', 41, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681816855, 1681816855, 24),
(1679, 'max_ewm_num', '最大二维码数量', 41, 0, 0, '20', '', '', 1, 1681816855, 1681816855, 24),
(1680, 'channel_show', '是否展示通道', 41, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681816855, 1681816855, 24),
(1681, 'order_invalid_time', '订单超时时间(分钟)', 41, 0, 0, '5', '', '', 1, 1681839943, 1681844334, 34),
(1682, 'money_is_float', '订单金额浮动', 41, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681839943, 1681844334, 34),
(1683, 'is_amount_lock', '锁码', 41, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681839943, 1681844334, 34),
(1684, 'max_ewm_num', '最大二维码数量', 41, 0, 0, '20', '', '', 1, 1681839943, 1681844334, 34),
(1685, 'channel_show', '是否展示通道', 41, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681839943, 1681844334, 34),
(1686, 'order_invalid_time', '订单超时时间(分钟)', 39, 0, 0, '10', '', '', 1, 1681840895, 1681843597, 34),
(1687, 'money_is_float', '订单金额浮动', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681840895, 1681843597, 34),
(1688, 'is_pay_name', '支付页面是否提交姓名', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681840895, 1681843597, 34),
(1689, 'initialization_h5', '初始化页面跳转支付宝', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681840895, 1681843597, 34),
(1690, 'order_amount_float_no_top', '订单金额不上浮', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681840895, 1681843597, 34),
(1691, 'pay_template', '支付页面模板', 39, 0, 0, '1', '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】', '', 1, 1681840895, 1681843597, 34),
(1692, 'order_amount_float_range', '订单浮动金额个数', 39, 0, 0, '20', '', '', 1, 1681840895, 1681843597, 34),
(1693, 'ms_code_money_status', '码商设置收款码金额区间', 39, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681840895, 1681843597, 34),
(1694, 'api_min_money', '收款码收款最小金额', 39, 0, 0, '0', '', '', 1, 1681840895, 1681843597, 34),
(1695, 'api_max_money', '收款码收款最大金额', 39, 0, 0, '0', '', '', 1, 1681840895, 1681843597, 34),
(1696, 'max_ewm_num', '最大二维码数量', 39, 0, 0, '20', '', '', 1, 1681840895, 1681843597, 34),
(1697, 'channel_show', '是否展示通道', 39, 0, 0, '1', '1:开启,2:关闭', '', 1, 1681840895, 1681843597, 34),
(1698, 'no_orders', '日切开关，开启后无法进单', 256, 0, 0, '1', '1:关闭,2:开启', '', 1, 1681844142, 1681892489, 34),
(1699, 'ms_order_min_money', '码商保底接单金额', 256, 0, 0, '0', '', '', 1, 1681844142, 1681892489, 34),
(1700, 'ms_rate_night_add', '码商夜间费率加成', 256, 0, 0, '0', '', '', 1, 1681844142, 1681892489, 34),
(1701, 'ms_rate_night_add_start_time_h', '码商夜间费率开始', 256, 0, 0, '0', '', '', 1, 1681844142, 1681892489, 34),
(1702, 'ms_rate_night_add_end_time_h', '码商夜间费率结束', 256, 0, 0, '0', '', '', 1, 1681844142, 1681892489, 34),
(1703, 'ms_df_night_add', '码商代付夜间费率加成', 256, 0, 0, '0', '', '', 1, 1681844142, 1681892489, 34),
(1704, 'ms_df_night_add_start_time_h', '码商代付夜间费率开始', 256, 0, 0, '0', '', '', 1, 1681844142, 1681892489, 34),
(1705, 'ms_df_night_add_end_time_h', '码商代付夜间费率结束', 256, 0, 0, '0', '', '', 1, 1681844142, 1681892489, 34),
(1706, 'ms_disable_money', '码商代付冻结余额启用', 256, 0, 0, '0', '0:不开启,1:开启', '', 1, 1681844142, 1681892489, 34),
(1707, 'cashier_address', '收银台服务器地址', 256, 0, 0, '5', '0:线路1-【ip：45.207.58.203】-【203】,1:推荐线路-【ip：8.210.16.62】-【阿里云香港】,3:线路3-【ip：8.217.66.247】-【阿里云备用香港】,11:上海线路-【ip：47.103.22.166】-【阿里云上海】,2:线路2-【ip：43.225.47.56】-【56】,4:腾讯云-【ip：175.24.200.93】-【腾讯云杭州】,8:阿里云国内-【ip：47.96.133.197】-【阿里云杭州】,5:线路5-【ip：43.225.47.61】-【5', '', 1, 1681844142, 1681892489, 34),
(1708, 'sys_code_type', '系统派单方式', 256, 0, 0, '0', '0:顺序轮询,1:随机权重分配', '', 1, 1681844142, 1681892489, 34),
(1709, 'permit_ms_huafen_money', '码商给下级划分余额', 256, 0, 0, '1', '2:关闭,1:开启', '', 1, 1681844142, 1681892489, 34),
(1710, 'disable_ms_money_status', '码商进单冻结余额（正在跑量的时候切勿操作，否则后果自负！！！）', 256, 0, 0, '2', '2:关闭,1:开启', '', 1, 1681844142, 1681892489, 34),
(1711, 'user_withdraw_google_code_on', '商户提现开启google验证码', 256, 0, 0, '2', '1:开启,2:关闭', '', 1, 1681844142, 1681892489, 34);

-- --------------------------------------------------------

--
-- 表的结构 `cm_dafiu_account`
--

CREATE TABLE `cm_dafiu_account` (
  `id` int(10) UNSIGNED NOT NULL,
  `channel_name` varchar(45) DEFAULT NULL,
  `money` decimal(10,2) DEFAULT NULL,
  `controller` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_daifu_orders`
--

CREATE TABLE `cm_daifu_orders` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `is_lock` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否锁定订单 1是 0否',
  `chongzhen` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'å†²æ­£'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_daifu_orders_transfer`
--

CREATE TABLE `cm_daifu_orders_transfer` (
  `id` int(11) UNSIGNED NOT NULL,
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
  `weight` int(11) NOT NULL DEFAULT '1' COMMENT '权重'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_deposite_card`
--

CREATE TABLE `cm_deposite_card` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `status` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态,1表示可使用状态，0表示禁止状态',
  `bank_id` int(10) NOT NULL DEFAULT '0' COMMENT '银行卡ID',
  `bank_account_username` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡用户名',
  `bank_account_number` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡账号',
  `bank_account_address` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡地址',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT 'df_bank_id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值卡信息';

-- --------------------------------------------------------

--
-- 表的结构 `cm_deposite_orders`
--

CREATE TABLE `cm_deposite_orders` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `p_admin_id` mediumint(8) DEFAULT NULL COMMENT '跑分平台管理员id',
  `trade_no` varchar(255) NOT NULL DEFAULT '' COMMENT '申请充值订单号',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0表示正在申请 1成功 2表示失败',
  `bank_id` int(10) NOT NULL DEFAULT '0' COMMENT '银行卡ID',
  `bank_account_username` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡用户名',
  `bank_account_number` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡账号',
  `bank_account_address` varchar(255) NOT NULL DEFAULT '' COMMENT '银行卡地址',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `recharge_account` varchar(64) DEFAULT NULL COMMENT '充值账号',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `card_id` int(11) DEFAULT NULL COMMENT '充值银行卡id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='申请充值信息';

-- --------------------------------------------------------

--
-- 表的结构 `cm_ewm_block_ip`
--

CREATE TABLE `cm_ewm_block_ip` (
  `id` int(11) UNSIGNED NOT NULL,
  `ms_id` int(11) NOT NULL COMMENT '码商 ',
  `block_visite_ip` varchar(100) NOT NULL COMMENT '拉黑的ip',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_ewm_order`
--

CREATE TABLE `cm_ewm_order` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `ms_callback_ip` varchar(255) NOT NULL COMMENT '码商监控回调ip'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `cm_ewm_pay_code`
--

CREATE TABLE `cm_ewm_pay_code` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `extra` text NOT NULL COMMENT 'å…¶ä»–ä¿¡æ¯',
  `publicKey` text COMMENT 'å…¬é’¥',
  `privateKey` text COMMENT 'ç§é’¥',
  `remark` varchar(255) NOT NULL COMMENT 'å¤‡æ³¨'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='码商二维码表' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `cm_gemapay_code`
--

CREATE TABLE `cm_gemapay_code` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT '属于哪个用户',
  `type` int(1) DEFAULT NULL COMMENT '1表示微信，２表示支付宝，３表示云散付，４表示百付通',
  `qr_image` varchar(255) DEFAULT NULL COMMENT '二维码地址',
  `last_used_time` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '0' COMMENT '是否正常使用　０表示正常，１表示禁用',
  `last_online_time` int(11) DEFAULT NULL COMMENT '最后一次在线的时间',
  `pay_status` int(11) DEFAULT NULL COMMENT '０表示未使用，１表示使用占用中',
  `limit_money` decimal(10,2) DEFAULT NULL,
  `paying_num` int(10) DEFAULT NULL COMMENT '正在支付的数量',
  `user_name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_jdek_sale`
--

CREATE TABLE `cm_jdek_sale` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` tinyint(10) NOT NULL,
  `merchantId` varchar(255) NOT NULL,
  `signkey` varchar(255) NOT NULL,
  `encryptionkey` varchar(255) NOT NULL,
  `status` tinyint(10) NOT NULL DEFAULT '0',
  `ms_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_jobs`
--

CREATE TABLE `cm_jobs` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_menu`
--

CREATE TABLE `cm_menu` (
  `id` bigint(10) UNSIGNED NOT NULL COMMENT '文档ID',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(100) NOT NULL DEFAULT '100' COMMENT '排序',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `module` char(20) NOT NULL DEFAULT '' COMMENT '模块',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `is_hide` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `icon` char(30) NOT NULL DEFAULT '' COMMENT '图标',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='基本菜单表';

--
-- 转存表中的数据 `cm_menu`
--

INSERT INTO `cm_menu` (`id`, `pid`, `sort`, `name`, `module`, `url`, `is_hide`, `icon`, `status`, `update_time`, `create_time`) VALUES
(1, 0, 100, '控制台', 'admin', '/', 0, 'console', 1, 1544365211, 1539583897),
(2, 0, 100, '系统设置', 'admin', 'Site', 0, 'set', 1, 1690976220, 1539583897),
(3, 2, 100, '基本设置', 'admin', 'Site', 1, 'set-fill', 1, 1690976054, 1539583897),
(4, 3, 100, '网站设置', 'admin', 'Site/website', 0, '', 1, 1663831847, 1539583897),
(5, 3, 100, '邮件服务', 'admin', 'Site/email', 0, '', 1, 1663831823, 1539583897),
(6, 3, 100, '行为日志', 'admin', 'Log/index', 0, 'flag', 1, 1540563678, 1540563678),
(7, 6, 100, '获取日志列表', 'admin', 'Log/getList', 1, '', 1, 1540566783, 1540566783),
(8, 6, 100, '删除日志', 'admin', 'Log/logDel', 1, '', 1, 1540566819, 1540566819),
(9, 6, 100, '清空日志', 'admin', 'Log/logClean', 1, '', 1, 1540566849, 1540566849),
(10, 2, 100, '权限设置', 'admin', 'Admin', 0, 'set-sm', 1, 1539584897, 1539583897),
(11, 10, 100, '管理员设置', 'admin', 'Admin/index', 0, '', 1, 1539584897, 1539583897),
(12, 11, 100, '获取管理员列表', 'admin', 'Admin/userList', 1, 'user', 1, 1540485169, 1540484869),
(13, 11, 100, '新增管理员', 'admin', 'Admin/userAdd', 1, 'user', 1, 1540485182, 1540485125),
(14, 11, 100, '编辑管理员', 'admin', 'Admin/userEdit', 1, 'user', 1, 1540485199, 1540485155),
(15, 11, 100, '删除管理员', 'admin', 'AdminuserDel', 1, 'user', 1, 1540485310, 1540485310),
(16, 10, 100, '角色管理', 'admin', 'Admin/group', 0, '', 1, 1539584897, 1539583897),
(17, 16, 100, '获取角色列表', 'admin', 'Admin/groupList', 1, '', 1, 1540485432, 1540485432),
(18, 16, 100, '新增权限组', 'admin', 'Admin/groupAdd', 1, '', 1, 1540485531, 1540485488),
(19, 16, 100, '编辑权限组', 'admin', 'Admin/groupEdit', 1, '', 1, 1540485515, 1540485515),
(20, 16, 100, '删除权限组', 'admin', 'Admin/groupDel', 1, '', 1, 1540485570, 1540485570),
(21, 10, 100, '菜单管理', 'admin', 'Menu/index', 0, '', 1, 1539584897, 1539583897),
(22, 21, 100, '获取菜单列表', 'admin', 'Menu/getList', 1, '', 1, 1540485652, 1540485632),
(23, 21, 100, '新增菜单', 'admin', 'Menu/menuAdd', 1, '', 1, 1540534094, 1540534094),
(24, 21, 100, '编辑菜单', 'admin', 'Menu/menuEdit', 1, '', 1, 1540534133, 1540534133),
(25, 21, 100, '删除菜单', 'admin', 'Menu/menuDel', 1, '', 1, 1540534133, 1540534133),
(26, 2, 100, '我的设置', 'admin', 'Admin/profile', 1, '', 1, 1690976065, 1539583897),
(27, 26, 100, '基本资料', 'admin', 'Site/profile', 0, '', 1, 1663831867, 1539583897),
(28, 26, 100, '修改密码', 'admin', 'Site/changePwd', 0, '', 1, 1663831887, 1539583897),
(29, 0, 100, '支付设置', 'admin', 'Pay', 1, 'senior', -1, 1690976223, 1539583897),
(30, 29, 100, '支付产品', 'admin', 'Pay/index', 0, '', 1, 1539584897, 1539583897),
(31, 30, 100, '获取支付产品列表', 'admin', 'Pay/getCodeList', 1, '', 1, 1545461560, 1545458869),
(32, 30, 100, '新增支付产品', 'admin', 'Pay/addCode', 1, '', 1, 1545461705, 1545458888),
(33, 30, 100, '编辑支付产品', 'admin', 'Pay/editCode', 1, '', 1, 1545461713, 1545458915),
(34, 30, 100, '删除产品', 'admin', 'Pay/delCode', 1, '', 1, 1545461745, 1545458935),
(35, 29, 100, '支付渠道', 'admin', 'Pay/channel', 0, '', 1, 1539584897, 1539583897),
(36, 35, 100, '获取渠道列表', 'admin', 'Pay/getChannelList', 1, '', 1, 1545461798, 1545458953),
(37, 35, 100, '新增渠道', 'admin', 'Pay/addChannel', 1, '', 1, 1545461856, 1545458977),
(38, 35, 100, '编辑渠道', 'admin', 'Pay/editChannel', 1, '', 1, 1545461863, 1545458992),
(39, 35, 100, '删除渠道', 'admin', 'Pay/delChannel', 1, '', 1, 1545461870, 1545459004),
(40, 29, 100, '渠道账户', 'admin', 'Pay/account', 0, '', -1, 1667587295, 1545459058),
(41, 40, 100, '获取渠道账户列表', 'admin', 'Pay/getAccountList', 1, '', -1, 1667587657, 1545459152),
(42, 40, 100, '新增账户', 'admin', 'Pay/addAccount', 1, '', -1, 1667587652, 1545459180),
(43, 40, 100, '编辑账户', 'admin', 'Pay/editAccount', 1, '', -1, 1667587650, 1545459194),
(44, 40, 100, '删除账户', 'admin', 'Pay/delAccount', 1, '', -1, 1667587643, 1545459205),
(45, 29, 100, '银行管理', 'admin', 'Pay/bank', 0, '', 1, 1540822566, 1540822549),
(46, 45, 100, '获取银行列表', 'admin', 'Pay/getBankList', 1, '', 1, 1545462167, 1545459107),
(47, 45, 100, '新增银行', 'admin', 'Pay/addBank', 1, '', 1, 1545462178, 1545459243),
(48, 45, 100, '编辑银行', 'admin', 'Pay/editBank', 1, '', 1, 1545462220, 1545459262),
(49, 45, 100, '删除银行', 'admin', 'Pay/delBank', 1, '', 1, 1545462230, 1545459277),
(50, 0, 100, '内容管理', 'admin', 'Article', 0, 'template', -1, 1666788045, 1539583897),
(51, 50, 100, '文章管理', 'admin', 'Article/index', 0, '', -1, 1666788037, 1539583897),
(52, 51, 100, '获取文章列表', 'admin', 'Article/getList', 1, 'lis', -1, 1667587272, 1540484939),
(53, 51, 100, '新增文章', 'admin', 'Article/add', 1, '', -1, 1667587269, 1540486058),
(54, 51, 100, '编辑文章', 'admin', 'Article/edit', 1, '', -1, 1667587263, 1540486097),
(55, 51, 100, '删除文章', 'admin', 'Article/del', 1, '', -1, 1667587265, 1545459431),
(56, 50, 100, '公告管理', 'admin', 'Article/notice', 0, '', -1, 1666788041, 1539583897),
(57, 56, 100, '获取公告列表', 'admin', 'Article/getNoticeList', 1, '', -1, 1667587260, 1545459334),
(58, 56, 100, '新增公告', 'admin', 'Article/addNotice', 1, '', -1, 1667587257, 1545459346),
(59, 56, 100, '编辑公告', 'admin', 'Article/editNotice', 1, '', -1, 1667587254, 1545459368),
(60, 56, 100, '删除公告', 'admin', 'Article/delNotice', 1, '', -1, 1667587251, 1545459385),
(61, 0, 100, '商户管理', 'admin', 'User', 1, 'user', -1, 1690976225, 1539583897),
(62, 61, 100, '商户列表', 'admin', 'User/index', 0, '', 1, 1539584897, 1539583897),
(63, 62, 100, '获取商户列表', 'admin', 'User/getList', 1, '', 1, 1540486400, 1540486400),
(64, 62, 100, '新增商户', 'admin', 'User/add', 1, '', 1, 1540533973, 1540533973),
(65, 62, 100, '商户修改', 'admin', 'User/edit', 1, '', 1, 1540533993, 1540533993),
(66, 62, 100, '删除商户', 'admin', 'User/del', 1, '', 1, 1545462902, 1545459408),
(67, 61, 100, '提现记录', 'admin', 'Balance/paid', 0, '', 1, 1539584897, 1539583897),
(68, 67, 100, '获取提现记录', 'admin', 'Balance/paidList', 1, '', 1, 1545462677, 1545458822),
(69, 67, 100, '提现编辑', 'admin', 'Balance/editPaid', 1, '', 1, 1545462708, 1545458822),
(70, 67, 100, '提现删除', 'admin', 'Balance/delPaid', 1, '', 1, 1545462715, 1545458822),
(71, 61, 100, '商户账户', 'admin', 'Account/index', 0, '', -1, 1667587320, 1539583897),
(80, 71, 100, '商户账户列表', 'admin', 'Account/getList', 1, '', -1, 1667587638, 1545459501),
(81, 71, 100, '新增商户账户', 'admin', 'Account/add', 1, '', -1, 1667587636, 1545459501),
(82, 71, 100, '编辑商户账户', 'admin', 'Account/edit', 1, '', -1, 1667587633, 1545459501),
(83, 71, 100, '删除商户账户', 'admin', 'Account/del', 1, '', -1, 1667587631, 1545459501),
(84, 61, 100, '商户资金', 'admin', 'Balance/index', 0, '', 1, 1539584897, 1539583897),
(85, 84, 100, '商户资金列表', 'admin', 'Balance/getList', 1, '', 1, 1545462951, 1545459501),
(86, 84, 100, '商户资金明细', 'admin', 'Balance/details', 1, '', 1, 1545462997, 1545459501),
(87, 84, 100, '获取商户资金明细', 'admin', 'Balance/getDetails', 1, '', 1, 1545462997, 1545459501),
(88, 61, 100, '商户API', 'admin', 'Api/index', 0, '', -1, 1680186924, 1539583897),
(89, 87, 100, '商户API列表', 'admin', 'Api/getList', 1, '', 1, 1545463054, 1545459501),
(90, 87, 100, '编辑商户API', 'admin', 'Api/edit', 1, '', 1, 1545463065, 1545459501),
(91, 61, 100, '商户认证', 'admin', 'User/auth', 0, '', -1, 1667587801, 1542882201),
(92, 90, 100, '商户认证列表', 'admin', 'getlist', 1, '', 1, 1545459501, 1545459501),
(93, 90, 100, '编辑商户认证', 'admin', 'getlist', 1, '', 1, 1545459501, 1545459501),
(94, 0, 100, '订单管理', 'admin', 'Orders', 1, 'form', -1, 1690976227, 1539583897),
(95, 94, 100, '交易列表', 'admin', 'Orders/index', 0, '', 1, 1539584897, 1539583897),
(96, 95, 100, '获取交易列表', 'admin', 'Orders/getList', 1, '', 1, 1545463214, 1539583897),
(97, 94, 100, '交易详情', 'admin', 'Orders/details', 1, '', 1, 1545463268, 1545459549),
(98, 94, 100, '退款列表', 'admin', 'Orders/refund', 0, '', -1, 1667587969, 1539583897),
(99, 61, 100, '商户订单统计', 'admin', 'Orders/user', 0, '', 1, 1669739646, 1539583897),
(100, 99, 100, '获取商户统计', 'admin', 'Orders/userList', 1, '', 1, 1539584897, 1539583897),
(101, 94, 100, '渠道统计', 'admin', 'Orders/channel', 0, '', -1, 1667589056, 1539583897),
(102, 101, 100, '获取渠道统计', 'admin', 'Orders/channelList', 1, '', -1, 1667589114, 1539583897),
(103, 61, 100, '商户统计', 'admin', 'User/cal', 0, '', 1, 1667587837, 1581872080),
(104, 61, 100, '商户资金记录', 'admin', 'Balance/change', 0, '', -1, 1667587808, 1583999358),
(105, 0, 100, '代付管理', 'admin', 'DaifuOrders', 1, 'form', -1, 1690976229, 1581082458),
(111, 105, 100, '订单列表', 'admin', 'DaifuOrders/index', 0, '', 1, 1581082501, 1581082501),
(113, 105, 100, '充值银行卡', 'admin', 'DaifuOrders/depositecard', 0, '', -1, 1667587602, 1585315597),
(114, 105, 100, '充值列表', 'admin', 'deposite_order/index', 0, '', -1, 1667587605, 1585329451),
(115, 94, 100, '渠道资金', 'admin', 'Channel/fundIndex', 0, '', -1, 1667589059, 1587199882),
(116, 2, 100, '代付设置', 'admin', 'daifu_orders/setting', 1, '', 1, 1690976069, 1588083251),
(117, 0, 100, '码商管理', 'admin', 'Ms', 1, 'senior', -1, 1690976231, 1539583897),
(118, 117, 100, '码商列表', 'admin', 'Ms/index', 0, '', 1, 1539584897, 1539583897),
(121, 219, 100, '卡转卡列表', 'admin', 'Ms/payCodes', 0, '', 1, 1683894278, 0),
(125, 220, 100, '卡卡订单', 'admin', 'Ms/orders', 0, '', 1, 1673105714, 1539584897),
(126, 117, 100, '码商流水', 'admin', 'Ms/bills', 0, '', -1, 1667588043, 1539584897),
(127, 62, 100, '商户余额', 'admin', 'balance/changeList', 1, '', 1, 1646069652, 1646069652),
(128, 117, 100, '码商列表2', 'admin', 'ms/getmslist', 1, '', 1, 1646069778, 1646069778),
(129, 117, 100, '获取二维码了表', 'admin', 'ms/getPaycodesLists', 1, '', 1, 1646069908, 1646069908),
(130, 117, 100, '获取订单列表', 'admin', 'ms/getOrdersList', 1, '', 1, 1646069976, 1646069976),
(131, 117, 100, '获取码商流水', 'admin', 'ms/getBillsList', 1, '', 1, 1646070033, 1646070033),
(132, 67, 100, '提现详情', 'admin', 'balance/details_tixian', 1, '', 1, 1646070236, 1646070236),
(133, 67, 100, '处理提现', 'admin', 'balance/deal', 1, '', 1, 1646070403, 1646070403),
(134, 117, 100, '编辑码商', 'admin', 'ms/edit', 1, '', 1, 1646070586, 1646070586),
(135, 2, 100, '确认命令', 'admin', 'api/checkOpCommand', 1, '', 1, 1690976073, 1646070809),
(136, 117, 100, '确认订单', 'admin', 'ms/issueOrder', 1, '', 1, 1646070895, 1646070895),
(137, 94, 100, '补单', 'admin', 'orders/budanDetails', 1, '', 1, 1646071307, 1646071307),
(138, 94, 100, '补单发送', 'admin', 'orders/update', 1, '', 1, 1646071417, 1646071417),
(139, 94, 100, '补发通知', 'admin', 'orders/subnotify', 1, '', 1, 1646071466, 1646071466),
(140, 117, 100, '操作流水', 'admin', 'ms/changeBalance', 1, '', 1, 1646136901, 1646136901),
(141, 62, 100, '商户列表2', 'admin', 'user/getList', 1, '', 1, 1646137050, 1646137050),
(142, 62, 100, '增减商户资金', 'admin', 'balance/changeBalance', 1, '', 1, 1646148840, 1646148840),
(143, 117, 100, '异常订单', 'admin', 'Ms/abnormalOrders', 0, '', -1, 1667588104, 1657521636),
(144, 105, 100, '获取代付订单列表', 'admin', 'DaifuOrders/getOrdersList', 1, '', 1, 1660748092, 1660747004),
(145, 105, 100, '代付订单导出', 'admin', 'DaifuOrders/exportOrder', 1, '', 1, 1661027103, 1661027103),
(146, 94, 100, '导出订单列表', 'admin', 'Orders/exportOrder', 1, '', 1, 1661027277, 1661027277),
(147, 61, 100, '导出商户资金列表', 'admin', 'Balance/exportBalance', 1, '', 1, 1661156354, 1661027453),
(148, 117, 100, '导出码商订单', 'admin', 'ms/exportOrder', 1, '', 1, 1661155160, 1661155160),
(149, 67, 100, '导出提现记录', 'admin', 'Balance/exportBalanceCash', 1, '', 1, 1661351933, 1661156604),
(150, 84, 100, '导出商户资金', 'admin', 'balance/exportBalanceChange', 1, '', 1, 1661157427, 1661157427),
(151, 117, 100, '导出码商流水', 'admin', 'ms/exportMsBills', 1, '', 1, 1662285094, 1662285094),
(152, 0, 100, '卡转卡', 'admin', 'kzk', 0, 'rmb', -1, 1673106776, 1667589143),
(153, 0, 100, '支付宝UID', 'admin', 'alipayUid', 0, 'rmb', -1, 1673106773, 1667630719),
(155, 219, 100, '支付宝UID列表', 'admin', 'Ms/uidList', 0, '', 1, 1673105691, 1667630938),
(156, 220, 100, '支付宝UID订单', 'admin', 'Ms/uidOrder', 0, '', 1, 1673105724, 1667631244),
(157, 117, 100, '添加码商', 'admin', 'Ms/add', 1, '', 1, 1667651170, 1667651170),
(158, 156, 100, '获取支付宝UID订单', 'admin', 'Ms/getuidOrdersList', 1, '', 1, 1667654546, 1667654546),
(159, 155, 100, '获取支付宝UID列表', 'admin', 'Ms/getuidLists', 1, '', 1, 1667654622, 1667654622),
(160, 103, 100, '获取商户总统计', 'admin', 'User/calList', 1, '', 1, 1667657168, 1667657168),
(166, 117, 100, '删除码商', 'admin', 'Ms/del', 1, '', 1, 1667755769, 1667755769),
(167, 117, 100, '设置码商接单状态', 'admin', 'Ms/editMsJdStatus', 1, '', 1, 1667755991, 1667755991),
(168, 117, 100, '码商费率设置', 'admin', 'Ms/assign_channels', 1, '', 1, 1667756073, 1667756073),
(169, 117, 100, '码商流水管理', 'admin', 'Ms/bills', 0, '', 1, 1675489737, 1667756148),
(170, 117, 100, '设置码商权重', 'admin', 'Ms/editMsWeight', 1, '', 1, 1667756199, 1667756199),
(171, 117, 100, '配置码商白名单', 'admin', 'Ms/changeWhiteIp', 1, '', 1, 1667756269, 1667756269),
(172, 117, 100, '白名单校验口令', 'admin', 'api/checkOpCommand', 1, '', 1, 1667756515, 1667756515),
(173, 61, 100, '标记商户异常', 'admin', 'User/mark_abnormal', 1, '', 1, 1667756669, 1667756669),
(174, 61, 100, '商户通道管理', 'admin', 'User/codes', 1, '', 1, 1667756710, 1667756710),
(175, 61, 100, '商户分润设置', 'admin', 'User/profit', 1, '', 1, 1667756760, 1667756760),
(176, 61, 100, '商户代付分成设置', 'admin', 'User/daifuProfit', 1, '', 1, 1667756814, 1667756814),
(177, 61, 100, '重置密钥', 'admin', 'Api/resetKey', 1, '', 1, 1667757548, 1667757548),
(178, 117, 100, '删除码商收款账号', 'admin', 'Ms/delPayCode', 1, '', 1, 1667757757, 1667757757),
(179, 117, 100, '拉黑码商订单ip', 'admin', 'Ms/blockIp', 1, '', 1, 1667757857, 1667757857),
(180, 117, 100, '码商订单退款', 'admin', 'Ms/refundOrder', 1, '', 1, 1667757901, 1667757901),
(181, 117, 100, '订单姓名不符', 'admin', 'Ms/abnormalOrderSave', 1, '', 1, 1667757955, 1667757955),
(182, 1, 100, '绑定谷歌验证器', 'admin', 'admin/blndGoogle', 1, '', 1, 1667836671, 1667836671),
(183, 11, 100, '授权权限', 'admin', 'Admin/userAuth', 1, '', 1, 1667979516, 1667979359),
(184, 111, 100, '订单指定码商', 'admin', 'DaifuOrders/appoint_ms', 1, '', 1, 1667990975, 1667990975),
(185, 111, 100, '强制补单', 'admin', 'DaifuOrders/auditSuccess', 1, '', 1, 1667991108, 1667991108),
(186, 111, 100, '关闭代付订单', 'admin', 'DaifuOrders/auditError', 1, '', 1, 1667991229, 1667991229),
(187, 111, 100, '搜索代付订单', 'admin', 'DaifuOrders/searchOrderMoney', 1, '', 1, 1668009119, 1668009119),
(188, 156, 100, '码商订单补发通知', 'admin', 'Ms/subnotify', 1, '', 1, 1668328588, 1668328588),
(189, 156, 100, '码商订单强制补单', 'admin', 'Ms/issueOrder', 1, '', 1, 1668328723, 1668328723),
(190, 156, 100, '订单统计栏搜索联动', 'admin', 'Ms/searchMsOrderMoney', 1, '', 1, 1668418806, 1668418806),
(191, 67, 100, '处理提现a', 'admin', 'balance/getAuditSwitch', 1, '', 1, 1668527015, 1668527015),
(192, 67, 100, '处理成功', 'admin', 'balance/deal', 1, '', 1, 1668527154, 1668527154),
(193, 221, 100, '支付宝UID商户报表', 'admin', 'Ms/uidshstatic', 0, 'rmb', 1, 1673106093, 1668536532),
(194, 193, 100, '获取uid商户报表', 'admin', 'Ms/getuidshstatic', 1, '', 1, 1668536593, 1668536593),
(195, 62, 100, '清空google权限', 'admin', 'user/clearGoogleAuth', 1, '', 1, 1668543842, 1668543842),
(196, 155, 100, '码子数量统计', 'admin', 'Ms/uidCodeListCount', 1, '', 1, 1668604528, 1668604528),
(197, 121, 100, '卡转卡码子统计', 'admin', 'Ms/kzkCodeListCount', 1, '', 1, 1668604571, 1668604571),
(198, 221, 100, 'UID收款帐户统计', 'admin', 'Ms/uidStatic', 0, '', 1, 1673106121, 1668622148),
(199, 221, 100, '卡转卡收款帐户统计', 'admin', 'Ms/kzkStatic', 0, '', 1, 1673106081, 1668622197),
(200, 199, 100, '获取收款帐户统计', 'admin', 'Ms/get_ewm_static', 1, '', 1, 1668622280, 1668622280),
(201, 0, 100, '网站报表', 'admin', 'baobiao', 1, '', -1, 1690976233, 1668708513),
(202, 201, 100, '网站统计', 'admin', 'Admin/getUserListStat', 1, '', 1, 1668709044, 1668708585),
(203, 201, 100, '网站列表', 'admin', 'Admin/userListStat', 0, '', 1, 1668708814, 1668708814),
(204, 67, 100, '处理提现请求', 'admin', 'balance/handle', 1, '', 1, 1668788375, 1668788375),
(205, 62, 100, '查看密钥', 'admin', 'user/view_secret', 1, '', 1, 1668790344, 1668790344),
(206, 111, 100, '代付补发通知', 'admin', 'DaifuOrders/add_notify', 1, '', 1, 1668835448, 1668835448),
(207, 67, 100, '处理体现获取通道', 'admin', 'balance/select_channel', 1, '', 1, 1668844902, 1668838908),
(208, 67, 100, '驳回提现', 'admin', 'Balance/rebut', 1, '', 1, 1668845068, 1668845068),
(209, 62, 100, '商户资金导出', 'admin', 'Balance/exportBalanceChange', 1, '', 1, 1668852110, 1668852110),
(210, 84, 100, '账户资金统计联动', 'admin', 'Balance/searchBalanceCal', 1, '', 1, 1668853348, 1668853348),
(211, 117, 100, '重置码商google', 'admin', 'ms/clearGoogleAuth', 1, '', 1, 1668856710, 1668856710),
(212, 1, 100, '测试收银台', 'admin', 'Index/testpay', 1, '', 1, 1668858384, 1668858384),
(213, 155, 100, '测试UID', 'admin', 'Ms/testuid', 1, '', 1, 1668858442, 1668858442),
(214, 155, 100, 'uid测试支付', 'admin', 'Ms/testuidpay', 1, '', 1, 1668858472, 1668858472),
(215, 155, 100, '修改码子状态', 'admin', 'Ms/disactiveCode', 1, '', 1, 1668858510, 1668858510),
(216, 62, 100, '重置密钥2', 'admin', 'admin/api/resetKey', 1, '', 1, 1668953631, 1668953631),
(217, 105, 100, '代付统计', 'admin', 'DaifuOrders/userStats', 0, '', 1, 1669569410, 1669568859),
(218, 217, 100, '获取用户代付统计', 'admin', 'DaifuOrders/getUserStats', 1, '', 1, 1669568918, 1669568918),
(219, 0, 100, '通道管理', 'admin', 'ms', 1, 'diamond', -1, 1690976235, 1673105530),
(220, 0, 100, '通道订单', 'admin', 'Ms', 1, 'rmb', -1, 1690976237, 1673105556),
(221, 0, 100, '通道统计', 'admin', 'ms', 1, 'template-1', -1, 1690976239, 1673105760),
(222, 219, 100, '抖音红包列表', 'admin', 'Ms/douyinGroupRedList', 0, '', 1, 1673106324, 1673106324),
(223, 222, 100, '获取抖音群红包列表', 'admin', 'Ms/getdouyinGroupRedLists', 1, '', 1, 1673106345, 1673106345),
(224, 219, 100, '微信群红包列表', 'admin', 'Ms/wechatGroupRedList', 0, '', 1, 1673106363, 1673106363),
(225, 224, 100, '获取微信群红包列表', 'admin', 'Ms/getwechatGroupRedList', 1, '', 1, 1673106381, 1673106381),
(226, 219, 100, '支付宝扫码列表', 'admin', 'Ms/alipaycodeList', 0, '', 1, 1673106398, 1673106398),
(227, 226, 100, '获取支付宝列表', 'admin', 'Ms/getalipaycodeLists', 1, '', 1, 1673106419, 1673106419),
(228, 219, 100, '微信扫码列表', 'admin', 'Ms/wechatCodeList', 0, '', 1, 1673106440, 1673106440),
(229, 228, 100, '获取微信扫码列表', 'admin', 'Ms/getwechatCodeLists', 1, '', 1, 1673106538, 1673106538),
(230, 219, 100, '数字人民币列表', 'admin', 'Ms/CnyNumberList', 0, '', 1, 1673106558, 1673106558),
(231, 230, 100, '获取数字人民币列表', 'admin', 'Ms/getCnyNumberLists', 1, '', 1, 1673106583, 1673106583),
(232, 219, 100, '京东E卡列表', 'admin', 'Ms/JDECardList', 0, '', 1, 1673106629, 1673106629),
(233, 232, 100, '获取京东E卡列表', 'admin', 'Ms/getJDECardLists', 1, '', 1, 1673106650, 1673106650),
(234, 219, 100, 'QQ面对面红包列表', 'admin', 'Ms/QQFaceRedList', 0, '', 1, 1673106671, 1673106671),
(235, 219, 100, '获取QQ面对面红包列表', 'admin', 'Ms/getQQFaceRedLists', 1, '', 1, 1673106688, 1673106688),
(236, 219, 100, '小程序商品码列表', 'admin', 'Ms/AppletProductsList', 0, '', 1, 1673106706, 1673106706),
(237, 236, 100, '获取小程序商品码列表', 'admin', 'Ms/getAppletProductsLists', 1, '', 1, 1673106723, 1673106723),
(238, 220, 100, '抖音红包订单', 'admin', 'Ms/douyinGroupRedOrder', 0, '', 1, 1673106792, 1673106792),
(239, 238, 100, '获取抖音红包订单', 'admin', 'Ms/getdouyinGroupRedOrdersList', 1, '', 1, 1673106809, 1673106809),
(240, 220, 100, '微信群红包订单', 'admin', 'Ms/wechatGroupRedOrder', 0, '', 1, 1673106829, 1673106829),
(241, 240, 100, '获取微信群红包订单', 'admin', 'Ms/getwechatGroupRedOrdersList', 1, '', 1, 1673106847, 1673106847),
(242, 220, 100, '支付宝扫码订单', 'admin', 'Ms/alipayCodeOrder', 0, '', 1, 1673106892, 1673106892),
(243, 242, 100, '获取支付宝扫码订单', 'admin', 'Ms/getalipayCodeOrdersList', 1, '', 1, 1673106911, 1673106911),
(244, 220, 100, '微信扫码订单', 'admin', 'Ms/wechatCodeOrder', 0, '', 1, 1673106929, 1673106929),
(245, 244, 100, '获取微信扫码订单', 'admin', 'Ms/getwechatCodeOrdersList', 1, '', 1, 1673106949, 1673106949),
(246, 220, 100, '数字人民币订单', 'admin', 'Ms/CnyNumberOrder', 0, '', 1, 1673106964, 1673106964),
(247, 246, 100, '获取数字人民币订单', 'admin', 'Ms/getCnyNumberOrdersList', 1, '', 1, 1673106981, 1673106981),
(248, 220, 100, '京东E卡订单', 'admin', 'Ms/JDECardOrder', 0, '', 1, 1673106997, 1673106997),
(249, 248, 100, '获取京东E卡订单', 'admin', 'Ms/getJDECardOrdersList', 1, '', 1, 1673107020, 1673107020),
(250, 220, 100, 'QQ面对面红包订单', 'admin', 'Ms/QQFaceRedOrder', 0, '', 1, 1681635727, 1673107038),
(251, 250, 100, '获取qq面对面红包订单', 'admin', 'Ms/getQQFaceRedOrdersList', 1, '', 1, 1681635734, 1673107068),
(252, 220, 100, '小程序商品码订单', 'admin', 'Ms/AppletProductsOrder', 0, '', 1, 1673107085, 1673107085),
(253, 252, 100, '获取小程序商品码订单', 'admin', 'Ms/getAppletProductsOrdersList', 1, '', 1, 1673107104, 1673107104),
(254, 30, 100, '支付产品配置', 'admin', 'pay/sys_paycode', 1, '', 1, 1673107272, 1673107272),
(255, 226, 100, '支付宝扫码搜索统计', 'admin', 'Ms/alipayCodeListCount', 1, '', 1, 1673942121, 1673942121),
(256, 242, 100, '支付宝扫码订单搜索统计', 'admin', 'Ms/searchMsAlipayCodeOrderMoney', 1, '', 1, 1673942164, 1673942164),
(257, 219, 100, '超级大额支付宝扫码列表', 'admin', 'Ms/taoBaoMoneyRedList', 0, '', 1, 1684423840, 1674894686),
(258, 257, 100, '获取超级大额支付宝扫码列表', 'admin', 'Ms/gettaoBaoMoneyRedLists', 1, '', 1, 1684423851, 1674894719),
(259, 220, 100, '超级大额支付宝扫码订单', 'admin', 'Ms/taoBaoMoneyRedOrder', 0, '', 1, 1684423896, 1674894752),
(260, 259, 100, '获取超级大额支付宝扫码订单', 'admin', 'Ms/gettaoBaoMoneyRedOrdersList', 1, '', 1, 1684423905, 1674894772),
(261, 259, 100, '超级大额支付宝扫码订单搜索统计', 'admin', 'Ms/searchMstaoBaoMoneyRedOrderMoney', 1, '', 1, 1684423917, 1674894811),
(262, 219, 100, '支付宝转账码列表', 'admin', 'Ms/alipayTransferCodeList', 0, '', 1, 1674894956, 1674894956),
(263, 262, 100, '获取支付宝转账码列表', 'admin', 'Ms/getalipayTransferCodeLists', 1, '', 1, 1674894975, 1674894975),
(264, 262, 100, '支付宝转账码列表搜索统计', 'admin', 'Ms/alipayTransferCodeListCount', 1, '', 1, 1674895001, 1674895001),
(265, 220, 100, '支付宝转账码订单', 'admin', 'Ms/alipayTransferCodeOrder', 0, '', 1, 1674895027, 1674895027),
(266, 265, 100, '获取支付宝转账码订单', 'admin', 'Ms/getalipayTransferCodeOrdersList', 1, '', 1, 1674895046, 1674895046),
(267, 265, 100, '支付宝转账码订单搜索统计', 'admin', 'Ms/searchMsalipayTransferCodeOrderMoney', 1, '', 1, 1674895067, 1674895067),
(268, 219, 100, '支付宝小荷包列表', 'admin', 'Ms/alipaySmallPurseList', 0, '', 1, 1675148062, 1675148062),
(269, 268, 100, '获取支付宝小荷包列表', 'admin', 'Ms/getalipaySmallPurseLists', 1, '', 1, 1675148088, 1675148088),
(270, 220, 100, '支付宝小荷包订单', 'admin', 'Ms/alipaySmallPurseOrder', 0, '', 1, 1675148124, 1675148124),
(271, 270, 100, '获取支付宝小荷包订单', 'admin', 'Ms/getalipaySmallPurseOrdersList', 1, '', 1, 1675148153, 1675148153),
(272, 105, 100, '中转管理', 'admin', 'DaifuOrders/TransferList', 0, '', 1, 1675240748, 1675240748),
(273, 272, 100, '获取中转平台列表', 'admin', 'DaifuOrders/getTransferList', 1, '', 1, 1675240781, 1675240781),
(274, 272, 100, '编辑中转平台信息', 'admin', 'DaifuOrders/editDaifuTransfer', 1, '', 1, 1675240811, 1675240811),
(275, 272, 100, '添加代付中转', 'admin', 'DaifuOrders/addtransferlist', 1, '', 1, 1675240833, 1675240833),
(276, 272, 100, '获取中转列表', 'admin', 'DaifuOrders/getTransferDfChannel', 1, '', 1, 1675241653, 1675241653),
(277, 272, 100, '提交中转', 'admin', 'DaifuOrders/dfTransfer', 1, '', 1, 1675241721, 1675241721),
(278, 117, 100, '一键停工', 'admin', 'Ms/closeMsWork', 1, '', 1, 1675245411, 1675245411),
(279, 248, 100, 'E卡订单页统计', 'admin', 'Ms/searchMsJDECardOrderMoney', 1, '', 1, 1675344200, 1675344200),
(280, 246, 100, '数字人民币订单页统计', 'admin', 'Ms/searchMsCnyNumberOrderMoney', 1, '', 1, 1675344247, 1675344247),
(281, 238, 100, '抖音红包订单搜索统计', 'admin', 'Ms/searchMsDouyinGroupRedOrderMoney', 1, '', 1, 1675348741, 1675348741),
(282, 240, 100, '微信群红包订单搜索统计', 'admin', 'Ms/searchMsWechatGroupRedOrderMoney', 1, '', 1, 1675348775, 1675348775),
(283, 248, 100, '京东E卡订单搜索统计', 'admin', 'Ms/searchMsJDECardOrderMoney', 1, '', 1, 1675348813, 1675348813),
(284, 230, 100, '数字人民币订单搜索统计', 'admin', 'Ms/searchMsCnyNumberOrderMoney', 1, '', 1, 1675348849, 1675348849),
(285, 250, 100, 'QQ面对面红包订单搜索统计', 'admin', 'Ms/searchMsQQFaceRedOrderMoney', 1, '', 1, 1675348913, 1675348913),
(286, 228, 100, '微信扫码列表搜索统计', 'admin', 'Ms/wechatCodeListCount', 1, '', 1, 1675348988, 1675348988),
(287, 230, 100, '数字人民币列表搜索统计', 'admin', 'ms/CnyNumberListCount', 1, '', 1, 1675349012, 1675349012),
(288, 232, 100, '京东E卡列表搜索统计', 'admin', 'Ms/JDECardListCount', 1, '', 1, 1675349043, 1675349043),
(289, 234, 100, 'QQ面对面红包列表搜索统计', 'admin', 'Ms/QQFaceRedListCount', 1, '', 1, 1675349064, 1675349064),
(290, 257, 100, '淘宝现金红包搜索统计', 'admin', 'Ms/taoBaoMoneyRedListCount', 1, '', 1, 1675349083, 1675349083),
(291, 268, 100, '支付宝小荷包列表搜索统计', 'admin', 'Ms/alipaySmallPurseListCount', 1, '', 1, 1675349101, 1675349101),
(292, 222, 100, '抖音红包搜索统计', 'admin', 'Ms/douyinGroupRedListCount', 1, '', 1, 1675349135, 1675349135),
(293, 224, 100, '微信群红包列表搜索统计', 'admin', 'Ms/wechatGroupRedListCount', 1, '', 1, 1675349153, 1675349153),
(294, 169, 100, '码商流水搜索联动', 'admin', 'ms/searchBalanceCal', 1, '', 1, 1675531272, 1675531272),
(295, 117, 100, '码商统计', 'admin', 'Ms/stats', 0, '', 1, 1675532397, 1675532397),
(296, 295, 100, '获取码商统计', 'admin', 'Ms/getMsStats', 1, '', 1, 1675532422, 1675532422),
(297, 272, 100, '开启自动中转', 'admin', 'DaifuOrders/editAutoTransferStatus', 1, '', 1, 1675677277, 1675677277),
(298, 1, 100, '管理余额校验', 'admin', 'Admin/adminBalance', 1, '', 1, 1675773034, 1675773034),
(299, 3, 100, '系统配置', 'admin', 'Site/sys_config', 0, '', 1, 1675787630, 1675787630),
(300, 1, 100, '设置口令', 'admin', 'admin/setCommand', 1, '', 1, 1675873467, 1675873467),
(301, 1, 100, '管理密令', 'admin', 'admin/adminCommand', 1, '', 1, 1675873512, 1675873512),
(302, 219, 100, '支付宝工作证列表', 'admin', 'Ms/alipayWorkCardList', 0, '', 1, 1675954424, 1675954424),
(303, 302, 100, '获取支付宝工作证列表', 'admin', 'Ms/getalipayWorkCardLists', 1, '', 1, 1675954485, 1675954485),
(304, 302, 100, '支付宝工作证列表搜索统计', 'admin', 'ms/alipayWorkCardListCount', 1, '', 1, 1675954552, 1675954552),
(305, 270, 100, '支付宝小荷包订单搜索统计', 'admin', 'Ms/searchMsalipaySmallPurseOrderMoney', 1, '', 1, 1675954700, 1675954700),
(306, 219, 100, '中额支付宝扫码列表', 'admin', 'Ms/QianxinTransferList', 0, '', 1, 1683105045, 1675955477),
(307, 306, 100, '获取中额支付宝扫码列表', 'admin', 'Ms/getQianxinTransferLists', 1, '', 1, 1683105054, 1675955527),
(308, 306, 100, '中额支付宝扫码列表搜索统计', 'admin', 'Ms/QianxinTransferListCount', 1, '', 1, 1683105063, 1675955576),
(309, 220, 100, '支付宝工作证订单', 'admin', 'Ms/alipayWorkCardOrder', 0, '', 1, 1675955629, 1675955629),
(310, 309, 100, '获取支付宝工作证订单列表', 'admin', 'Ms/getalipayWorkCardOrdersList', 1, '', 1, 1675955685, 1675955685),
(311, 309, 100, '支付宝工作证订单搜索统计', 'admin', 'Ms/searchMsalipayWorkCardOrderMoney', 1, '', 1, 1675955755, 1675955755),
(312, 220, 100, '中额支付宝扫码订单', 'admin', 'Ms/QianxinTransferOrder', 0, '', 1, 1683105074, 1675955795),
(313, 312, 100, '获取中额支付宝扫码订单', 'admin', 'Ms/getQianxinTransferOrdersList', 1, '', 1, 1683105081, 1675955847),
(314, 312, 100, '中额支付宝扫码订单搜索统计', 'admin', 'Ms/searchMsQianxinTransferOrderMoney', 1, '', 1, 1683105089, 1675955889),
(315, 219, 100, '支付宝口令红包列表', 'admin', 'Ms/alipayPassRedList', 0, '', 1, 1676011883, 1676011883),
(316, 315, 100, '获取支付宝口令红包列表', 'admin', 'Ms/getalipayPassRedLists', 1, '', 1, 1676011926, 1676011926),
(317, 315, 100, '支付宝口令红包列表搜索统计', 'admin', 'Ms/alipayPassRedListCount', 1, '', 1, 1676011963, 1676011963),
(318, 220, 100, '支付宝口令红包订单', 'admin', 'Ms/alipayPassRedOrder', 0, '', 1, 1676012114, 1676012114),
(319, 318, 100, '获取支付宝口令红包订单', 'admin', 'Ms/getalipayPassRedOrdersList', 1, '', 1, 1676013643, 1676012146),
(320, 318, 100, '支付宝口令红包订单搜索统计', 'admin', 'Ms/searchMsalipayPassRedOrderMoney', 1, '', 1, 1676013656, 1676012182),
(321, 272, 100, '代付查询余额', 'admin', 'DaifuOrders/query_balance', 1, '', 1, 1676095958, 1676095958),
(322, 0, 100, '余额管理', 'admin', 'admin', 1, '', -1, 1690976242, 1676871414),
(323, 322, 100, 'USDT充值', 'admin', 'Admin/usdtRecharge', 0, '', 1, 1676871437, 1676871437),
(324, 323, 100, '获取充值列表', 'admin', 'Admin/getRechargeList', 1, '', 1, 1676871459, 1676871459),
(325, 322, 100, '充值USDT', 'admin', 'Admin/rechargeAdd', 1, '', 1, 1676871479, 1676871479),
(326, 322, 100, '余额明细', 'admin', 'Admin/adminBill', 0, '', 1, 1676871497, 1676871497),
(327, 326, 100, '获取余额明细', 'admin', 'admin/getBillList', 1, '', 1, 1676871519, 1676871519),
(328, 117, 100, '强制关闭码商接单', 'admin', 'Ms/change_admin_work_status', 1, '', 1, 1677067107, 1677067107),
(329, 219, 100, '支付宝手机网站列表', 'admin', 'Ms/alipayWapList', 0, '', 1, 1680185835, 1680185835),
(330, 219, 100, '获取支付宝手机网站列表', 'admin', 'Ms/getalipayWapLists', 1, '', 1, 1680185855, 1680185855),
(331, 329, 100, '支付宝手机网站列表搜索统计', 'admin', 'ms/alipayWapListCount', 1, '', 1, 1680185875, 1680185875),
(332, 219, 100, '支付宝当面付列表', 'admin', 'Ms/alipayF2FList', 0, '', 1, 1680185891, 1680185891),
(333, 332, 100, '获取支付宝当面付列表', 'admin', 'Ms/getalipayF2FLists', 1, '', 1, 1680185911, 1680185911),
(334, 332, 100, '支付宝当面付列表搜索统计', 'admin', 'Ms/alipayF2FListCount', 1, '', 1, 1680185929, 1680185929),
(335, 220, 100, '支付宝手机网站订单', 'admin', 'Ms/alipayWapOrder', 0, '', 1, 1680185957, 1680185957),
(336, 335, 100, '支付宝手机网站订单搜索统计', 'admin', 'Ms/searchMsalipayWapOrderMoney', 1, '', 1, 1680185984, 1680185984),
(337, 335, 100, '获取支付宝手机网站订单列表', 'admin', 'Ms/getalipayWapOrdersList', 1, '', 1, 1680186022, 1680186022),
(338, 220, 100, '支付宝当面付订单', 'admin', 'Ms/alipayF2FOrder', 0, '', 1, 1680186056, 1680186056),
(339, 338, 100, '获取支付宝当面付订单列表', 'admin', 'Ms/getalipayF2FOrdersList', 1, '', 1, 1680186078, 1680186078),
(340, 338, 100, '支付宝当面付订单搜索统计', 'admin', 'Ms/searchMsalipayF2FOrderMoney', 1, '', 1, 1680186102, 1680186102),
(341, 219, 100, '淘宝直付列表', 'admin', 'Ms/taoBaoDirectPayList', 0, '', 1, 1680348984, 1680348984),
(342, 341, 100, '获取淘宝直付列表', 'admin', 'Ms/gettaoBaoDirectPayLists', 1, '', 1, 1680349049, 1680349049),
(343, 341, 100, '淘宝直付列表搜索统计', 'admin', 'Ms/taoBaoDirectPayListCount', 1, '', 1, 1680349116, 1680349116),
(344, 220, 100, '淘宝直付订单', 'admin', 'Ms/taoBaoDirectPayOrder', 0, '', 1, 1680349200, 1680349200),
(345, 344, 100, '获取淘宝直付订单', 'admin', 'Ms/gettaoBaoDirectPayOrdersList', 1, '', 1, 1680349261, 1680349261),
(346, 344, 100, '淘宝直付订单搜索统计', 'admin', 'Ms/searchMstaoBaoDirectPayOrderMoney', 1, '', 1, 1680349315, 1680349315),
(347, 250, 100, 'admin', 'admin', 'ms/getQQFaceRedOrdersList', 1, '', 1, 1680533121, 1680533121),
(348, 219, 100, '数字人民币自动列表', 'admin', 'Ms/CnyNumberAutoList', 0, '', 1, 1681055658, 1681055658),
(349, 348, 100, '数字人民币zidong列表搜索统计', 'admin', 'ms/CnyNumberAutoListCount', 1, '', 1, 1681055682, 1681055682),
(350, 348, 100, '获取数字人民币自动列表', 'admin', 'Ms/getCnyNumberAutoLists', 1, '', 1, 1681055707, 1681055707),
(351, 220, 100, '数字人民币自动订单', 'admin', 'Ms/CnyNumberAutoOrder', 0, '', 1, 1681055750, 1681055750),
(352, 351, 100, '获取数字人民币自动订单', 'admin', 'Ms/getCnyNumberAutoOrdersList', 1, '', 1, 1681055769, 1681055769),
(353, 351, 100, '数字人民币自动订单页统计', 'admin', 'Ms/searchMsCnyNumberAutoOrderMoney', 1, '', 1, 1681055787, 1681055787),
(354, 220, 100, '码商订单详情', 'admin', 'Ms/details', 1, '', 1, 1681570110, 1681570110),
(355, 219, 100, '小额支付宝扫码列表', 'admin', 'Ms/alipayCodeSmallList', 0, '', 1, 1681635504, 1681635504),
(356, 355, 100, '获取小额支付宝扫码列表', 'admin', 'Ms/getalipayCodeSmallLists', 1, '', 1, 1681635522, 1681635522),
(357, 355, 100, '小额支付宝扫码搜索统计', 'admin', 'Ms/alipayCodeSmallListCount', 1, '', 1, 1681635542, 1681635542),
(358, 220, 100, '小额支付宝扫码订单', 'admin', 'Ms/alipayCodeSmallOrder', 0, '', 1, 1681635564, 1681635564),
(359, 358, 100, '获取小额支付宝扫码订单', 'admin', 'Ms/getalipayCodeSmallOrdersList', 1, '', 1, 1681635587, 1681635587),
(360, 358, 100, '小额支付宝扫码订单搜索统计', 'admin', 'Ms/searchMsalipayCodeSmallOrderMoney', 1, '', 1, 1681635605, 1681635605),
(361, 105, 100, '码商团队统计', 'admin', 'DaifuOrders/teamStats', 0, '', 1, 1682532071, 1682532071),
(362, 361, 100, '获取码商团队统计列表', 'admin', 'DaifuOrders/getTeamStats', 1, '', 1, 1682532088, 1682532088),
(363, 111, 100, '锁定代付订单', 'admin', 'DaifuOrders/lock_df_operation', 1, '', 1, 1682843257, 1682843257),
(364, 117, 100, '团队统计', 'admin', 'Ms/msTeamStats', 0, '', 1, 1682950903, 1682950903),
(365, 364, 100, '获取团队统计', 'admin', 'Ms/getMsTeamStats', 1, '', 1, 1682950934, 1682950934),
(366, 111, 100, '代付订单详情', 'admin', 'DaifuOrders/details', 1, '', 1, 1683182379, 1683182379),
(367, 169, 100, '调整码商冻结余额', 'admin', 'Ms/changeDisableBalance', 1, '', 1, 1683182460, 1683182460),
(368, 118, 100, '解冻码商冻结余额', 'admin', 'Ms/toEnableMoney', 1, '', 1, 1683182536, 1683182536),
(369, 220, 100, '查看码商上级', 'admin', 'Ms/viewSuperiorMs', 1, '', 1, 1683211948, 1683211948),
(370, 111, 100, '代付冲正', 'admin', 'DaifuOrders/chongzhen', 1, '', 1, 1683212063, 1683212063),
(371, 118, 100, '修改团队接单状态', 'admin', 'Ms/editTeamStatus', 1, '', 1, 1683212173, 1683212173),
(372, 219, 100, '骏网列表', 'admin', 'Ms/JunWebList', 0, '', 1, 1683274156, 1683274156),
(373, 372, 100, '获取骏网列表', 'admin', 'Ms/getJunWebLists', 1, '', 1, 1683274180, 1683274180),
(374, 372, 100, '骏网列表搜索统计', 'admin', 'Ms/JunWebListCount', 1, '', 1, 1683274203, 1683274203),
(375, 220, 100, '骏网订单', 'admin', 'Ms/JunWebOrder', 0, '', 1, 1683274237, 1683274237),
(376, 375, 100, '获取骏网订单', 'admin', 'Ms/getJunWebOrdersList', 1, '', 1, 1683274260, 1683274260),
(377, 375, 100, '骏网订单页统计', 'admin', 'Ms/searchMsJunWebOrderMoney', 1, '', 1, 1683274282, 1683274282),
(378, 248, 100, '上传卡密', 'admin', 'Ms/updateCardKey', 1, '', 1, 1683274458, 1683274458),
(379, 272, 100, '获取代付中转模板', 'admin', 'DaifuOrders/searchConfig', 1, '', 1, 1683283823, 1683283823),
(380, 272, 100, '补发代付中转', 'admin', 'DaifuOrders/dfTransferBf', 1, '', 1, 1683284686, 1683284686),
(381, 219, 100, '汇元易付卡列表', 'admin', 'Ms/HuiYuanYiFuKaList', 0, '', 1, 1683306877, 1683306877),
(382, 381, 100, '获取汇元易付卡列表', 'admin', 'Ms/getHuiYuanYiFuKaLists', 1, '', 1, 1683306923, 1683306923),
(383, 381, 100, '汇元易付卡列表搜索统计', 'admin', 'Ms/HuiYuanYiFuKaListCount', 1, '', 1, 1683306965, 1683306965),
(384, 220, 100, '汇元易付卡订单', 'admin', 'Ms/HuiYuanYiFuKaOrder', 0, '', 1, 1683307061, 1683307061),
(385, 384, 100, '获取汇元易付卡订单', 'admin', 'Ms/getHuiYuanYiFuKaOrdersList', 1, '', 1, 1683307102, 1683307102),
(386, 384, 100, '汇元易付卡订单搜索统计', 'admin', 'Ms/searchMsHuiYuanYiFuKaOrderMoney', 1, '', 1, 1683307174, 1683307174),
(387, 118, 100, '码商通道设置', 'admin', 'Ms/MschannelList', 1, '', 1, 1683361155, 1683361155),
(388, 220, 100, '关闭订单', 'admin', 'Ms/closeOrder', 1, '', 1, 1683394348, 1683394348),
(389, 220, 100, '查看码商二维码', 'admin', 'Ms/decodeImagePath', 1, '', 1, 1683483751, 1683483751),
(390, 30, 100, '一键调整码商费率', 'admin', 'Pay/sysMsRate', 1, '', 1, 1683627898, 1683627898),
(391, 30, 100, '清空所有码商费率', 'admin', 'Pay/sysNullRate', 1, '', 1, 1683627931, 1683627931),
(392, 118, 100, '修改码商代付状态', 'admin', 'Ms/editDaifuStatus', 1, '', 1, 1683785157, 1683785157),
(393, 219, 100, '支付宝零花钱列表', 'admin', 'Ms/alipayPinMoneyList', 0, '', 1, 1683791573, 1683791573),
(394, 393, 100, '获取支付宝零花钱列表', 'admin', 'Ms/getalipayPinMoneyLists', 1, '', 1, 1683794019, 1683794019),
(395, 393, 100, '支付宝零花钱列表搜索统计', 'admin', 'Ms/alipayPinMoneyListCount', 1, '', 1, 1683794072, 1683794072),
(396, 220, 100, '支付宝零花钱订单', 'admin', 'Ms/alipayPinMoneyOrder', 0, '', 1, 1683794107, 1683794107),
(397, 396, 100, '获取支付宝零花钱订单', 'admin', 'Ms/getalipayPinMoneyOrdersList', 1, '', 1, 1683794268, 1683794147),
(398, 396, 100, '支付宝零花钱订单搜索统计', 'admin', 'Ms/searchMsalipayPinMoneyOrderMoney', 1, '', 1, 1683794191, 1683794191),
(399, 111, 100, '检测代付订单状态', 'admin', 'DaifuOrders/orderInfo', 1, '', 1, 1683880736, 1683880736),
(400, 30, 100, '设置支付产品开关', 'admin', 'Pay/channelShowSwitch', 1, '', 1, 1683900263, 1683900263),
(401, 118, 100, '码商移分', 'admin', 'Ms/yifen', 1, '', 1, 1684056861, 1684056861),
(402, 219, 100, '收款码回收站', 'admin', 'Ms/recycle', 1, '', 1, 1684070958, 1684070958),
(403, 219, 100, '获取码商回收站列表', 'admin', 'Ms/getRecycleList', 1, '', 1, 1684070985, 1684070985),
(404, 219, 100, '支付宝个人转账列表', 'admin', 'Ms/alipayTransferList', 0, '', 1, 1684230081, 1684230081),
(405, 404, 100, '获取支付宝个人转账列表', 'admin', 'Ms/getalipayTransferLists', 1, '', 1, 1684230114, 1684230114),
(406, 404, 100, '支付宝个人转账列表搜索统计', 'admin', 'Ms/alipayTransferListCount', 1, '', 1, 1684230161, 1684230161),
(407, 220, 100, '支付宝个人转账订单', 'admin', 'Ms/alipayTransferOrder', 0, '', 1, 1684230236, 1684230236),
(408, 407, 100, '获取支付宝个人转账订单列表', 'admin', 'Ms/getalipayTransferOrdersList', 1, '', 1, 1684230333, 1684230333),
(409, 407, 100, '支付宝个人转账订单搜索统计', 'admin', 'Ms/searchMsalipayTransferOrderMoney', 1, '', 1, 1684230366, 1684230366),
(410, 219, 100, '钉钉群收款列表', 'admin', 'Ms/DingDingGroupList', 0, '', 1, 1684490957, 1684490957),
(411, 410, 100, '获取钉钉群收款列表', 'admin', 'Ms/getDingDingGroupLists', 1, '', 1, 1684490997, 1684490997),
(412, 410, 100, '钉钉群收款列表搜索统计', 'admin', 'Ms/DingDingGroupListCount', 1, '', 1, 1684491055, 1684491055),
(413, 220, 100, '钉钉群收款订单', 'admin', 'Ms/DingDingGroupOrder', 0, '', 1, 1684491103, 1684491103),
(414, 413, 100, '获取钉钉群收款订单列表', 'admin', 'Ms/getDingDingGroupOrdersList', 1, '', 1, 1684491151, 1684491151),
(415, 413, 100, '钉钉群收款订单搜索统计', 'admin', 'Ms/searchMsDingDingGroupOrderMoney', 1, '', 1, 1684491184, 1684491184),
(416, 219, 100, '在线收款码', 'admin', 'Ms/onlineCode', 1, '', 1, 1684581641, 1684581641),
(417, 219, 100, '获取在线收款码', 'admin', 'Ms/getOnlineCode', 1, '', 1, 1684581674, 1684581674),
(418, 219, 100, '乐付天宏卡列表', 'admin', 'Ms/LeFuTianHongKaList', 0, '', 1, 1684586536, 1684586536),
(419, 418, 100, '获取乐付天宏卡列表', 'admin', 'Ms/getLeFuTianHongKaLists', 1, '', 1, 1684586598, 1684586598),
(420, 418, 100, '乐付天宏卡列表搜索统计', 'admin', 'Ms/LeFuTianHongKaListCount', 1, '', 1, 1684586651, 1684586651),
(421, 220, 100, '乐付天宏卡订单', 'admin', 'Ms/LeFuTianHongKaOrder', 0, '', 1, 1684586769, 1684586769),
(422, 421, 100, '获取乐付天宏卡订单列表', 'admin', 'Ms/getLeFuTianHongKaOrdersList', 1, '', 1, 1684586815, 1684586815),
(423, 421, 100, '乐付天宏卡订单搜索统计', 'admin', 'Ms/searchMsLeFuTianHongKaOrderMoney', 1, '', 1, 1684586850, 1684586850),
(424, 219, 100, '聚合码列表', 'admin', 'Ms/AggregateCodeList', 0, '', 1, 1684649897, 1684649897),
(425, 424, 100, '获取聚合码列表', 'admin', 'Ms/getAggregateCodeLists', 1, '', 1, 1684649932, 1684649932),
(426, 424, 100, '聚合码列表搜索统计', 'admin', 'Ms/AggregateCodeListCount', 1, '', 1, 1684649984, 1684649984),
(427, 220, 100, '聚合码订单', 'admin', 'Ms/AggregateCodeOrder', 0, '', 1, 1684650068, 1684650068),
(428, 427, 100, '获取聚合码订单列表', 'admin', 'Ms/getAggregateCodeOrdersList', 1, '', 1, 1684650109, 1684650109),
(429, 427, 100, '聚合码订单搜索统计', 'admin', 'Ms/searchMsAggregateCodeOrderMoney', 1, '', 1, 1684650141, 1684650141),
(430, 117, 100, '充值管理', 'admin', 'Ms/balanceRecharge', 0, '', 1, 1684939755, 1684939755),
(431, 430, 100, '获取码商充值列表', 'admin', 'Ms/getBalanceRechargeList', 1, '', 1, 1684939814, 1684939814),
(432, 430, 100, '通过码商充值', 'admin', 'Ms/balanceRechargeAgree', 1, '', 1, 1684939852, 1684939852),
(433, 430, 100, '拒绝码商充值', 'admin', 'Ms/balanceRechargeRefuse', 1, '', 1, 1684939921, 1684939921),
(434, 248, 100, '重新核销', 'admin', 'Ms/repeatWriteOff', 1, '', 1, 1685032045, 1685032045),
(435, 118, 100, '解除全部冻结', 'admin', 'Ms/unfreeze', 1, '', 1, 1685032045, 1685032045),
(436, 219, 100, '微信黄金红包列表', 'admin', 'Ms/wechatGoldRedList', 0, '', 1, 1685032045, 1685032045),
(437, 436, 100, '获取微信黄金红包列表', 'admin', 'Ms/getwechatGoldRedLists', 1, '', 1, 1685032045, 1685032045),
(438, 436, 100, '获取微信黄金红包列表搜索统计', 'admin', 'Ms/wechatGoldRedListCount', 1, '', 1, 1685032045, 1685032045),
(439, 220, 100, '微信黄金红包订单', 'admin', 'Ms/wechatGoldRedOrder', 0, '', 1, 1685032045, 1685032045),
(440, 439, 100, '获取微信黄金红包订单列表', 'admin', 'Ms/getwechatGoldRedOrdersLists', 1, '', 1, 1685032045, 1685032045),
(441, 439, 100, '获取微信黄金红包订单搜索统计', 'admin', 'Ms/wechatGoldRedOrderMoney', 1, '', 1, 1685032045, 1685032045),
(442, 118, 100, '解冻冻结余额到余额', 'admin', 'Ms/enableToMoney', 1, '', 1, 1685288272, 1685288272),
(443, 99, 100, '导出商户订单统计', 'admin', 'Orders/exportUserCal', 1, '', 1, 1685341604, 1685341604),
(444, 118, 100, '一键修改码商押金', 'admin', 'Ms/batchsetdeposit', 1, '', 1, 1685373262, 1685373262),
(445, 329, 100, '测试手机网站下单', 'admin', 'Ms/testPaymentAlipayWap', 1, '', 1, 1685429167, 1685429167),
(446, 118, 100, '导出码商列表', 'admin', 'Ms/exportMsList', 1, '', 1, 1685526782, 1685526782),
(447, 219, 100, 'QQ扫码列表', 'admin', 'Ms/qqCodeList', 0, '', 1, 1685032045, 1685032045),
(448, 447, 100, '获取QQ扫码列表', 'admin', 'Ms/getQqCodeLists', 1, '', 1, 1685032045, 1685032045),
(449, 447, 100, '获取QQ扫码列表搜索统计', 'admin', 'Ms/qqCodeListCount', 1, '', 1, 1685032045, 1685032045),
(450, 220, 100, 'QQ扫码订单', 'admin', 'Ms/qqCodeOrder', 0, '', 1, 1685032045, 1685032045),
(451, 450, 100, '获取QQ扫码订单列表', 'admin', 'Ms/getQqCodeOrdersList', 1, '', 1, 1685032045, 1685032045),
(452, 450, 100, '获取QQ扫码订单搜索统计', 'admin', 'Ms/searchMsQqCodeOrderMoney', 1, '', 1, 1685032045, 1685032045),
(453, 0, 100, '公告管理', 'admin', 'Article/notice', 1, '', -1, 1690976244, 1685856303),
(454, 453, 100, '公告列表', 'admin', 'Article/notice', 0, '', 1, 1685856303, 1685856303),
(455, 453, 100, '获取公告列表', 'admin', 'Article/getNoticeList', 1, '', 1, 1685856303, 1685856303),
(456, 453, 100, '新增公告', 'admin', 'Article/addNotice', 1, '', 1, 1685856303, 1685856303),
(457, 453, 100, '编辑公告', 'admin', 'Article/editNotice', 1, '', 1, 1685856303, 1685856303),
(458, 453, 100, '删除公告', 'admin', 'Article/delNotice', 1, '', 1, 1685856303, 1685856303),
(459, 272, 100, '删除代付中转', 'admin', 'DaifuOrders/delDaifuTransfer', 1, '', 1, 1685894967, 1685894967),
(460, 61, 100, '资金明细', 'admin', 'Balance/details', 0, '', 1, 1685894967, 1685894967),
(461, 295, 100, '搜索码商统计', 'admin', 'Ms/searchMsStats', 1, '', 1, 1686486067, 1686486067),
(462, 118, 100, '清理码商代付限制', 'admin', 'Ms/clear_df_ver', 1, '', 1, 1689322317, 1689322317),
(463, 1, 100, '安全码设置', 'admin', 'admin/security_code', 1, '', 1, 1689332691, 1689332691),
(464, 217, 100, '导出代付统计', 'admin', 'DaifuOrders/exportDaifuStats', 1, '', 1, 1690351320, 1690351320),
(465, 272, 100, '通道测试', 'admin', 'DaifuOrders/channelTest', 1, '', 1, 1690351320, 1690351320),
(466, 0, 100, '系统管理', 'admin', 'Manage', 0, 'user', 1, 1691217735, 1690976167),
(467, 466, 100, '加分', 'admin', 'Manage/webList', 0, '', 1, 1691326509, 1691217701),
(468, 466, 100, '加分记录', 'admin', 'Manage/userList', 0, '', 1, 1691326749, 1691326715),
(469, 466, 100, '清理登录限制', 'admin', 'Manage/record', 0, '', 1, 1691327819, 1691327819);

-- --------------------------------------------------------

--
-- 表的结构 `cm_ms`
--

CREATE TABLE `cm_ms` (
  `userid` int(10) UNSIGNED NOT NULL,
  `pid` int(11) UNSIGNED NOT NULL COMMENT '上级ID',
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
  `admin_work_status` int(1) NOT NULL DEFAULT '1' COMMENT '管理员码商接单状态',
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
  `bank_rate` float(4,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '代付费率',
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
  `disable_money` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户冻结余额余额'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `cm_ms_rate`
--

CREATE TABLE `cm_ms_rate` (
  `id` int(11) UNSIGNED NOT NULL,
  `ms_id` int(11) NOT NULL DEFAULT '0' COMMENT '码商id',
  `code_type_id` int(11) NOT NULL DEFAULT '0' COMMENT '支付编码id',
  `rate` decimal(10,3) NOT NULL DEFAULT '1.000' COMMENT '费率',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_ms_somebill`
--

CREATE TABLE `cm_ms_somebill` (
  `id` int(11) NOT NULL COMMENT 'id',
  `uid` int(11) NOT NULL COMMENT '会员ID',
  `jl_class` int(11) NOT NULL COMMENT '流水类别：1佣金2团队奖励3充值4提现5订单匹                                                                                                             配 6平台操作 7关闭订单',
  `type` varchar(20) NOT NULL DEFAULT 'enable' COMMENT 'enable 可用余额 disable 冻结余额',
  `info` varchar(225) NOT NULL COMMENT '说明',
  `addtime` varchar(225) NOT NULL COMMENT '事件时间',
  `jc_class` varchar(225) NOT NULL COMMENT '分+ 或-',
  `num` float(10,2) NOT NULL COMMENT '币量',
  `pre_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '变化前',
  `last_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'åå¨变化后¢'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='码商流水账单' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `cm_ms_white_ip`
--

CREATE TABLE `cm_ms_white_ip` (
  `id` int(11) NOT NULL,
  `ms_id` int(11) NOT NULL COMMENT '码商的id',
  `md5_ip` varchar(50) NOT NULL COMMENT '码商ip白名单MD5值'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `cm_notice`
--

CREATE TABLE `cm_notice` (
  `id` bigint(10) NOT NULL,
  `title` varchar(80) NOT NULL,
  `author` varchar(30) DEFAULT NULL COMMENT '作者',
  `content` text NOT NULL COMMENT '公告内容',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '公告状态,0-不展示,1-展示',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_orders`
--

CREATE TABLE `cm_orders` (
  `id` bigint(10) NOT NULL COMMENT '订单id',
  `puid` mediumint(8) NOT NULL DEFAULT '0' COMMENT '代理ID',
  `uid` mediumint(8) NOT NULL COMMENT '商户id',
  `trade_no` varchar(30) NOT NULL COMMENT '交易订单号',
  `out_trade_no` varchar(30) NOT NULL COMMENT '商户订单号',
  `subject` varchar(64) NOT NULL COMMENT '商品标题',
  `body` varchar(256) NOT NULL COMMENT '商品描述信息',
  `channel` varchar(30) NOT NULL COMMENT '交易方式(wx_native)',
  `cnl_id` int(3) NOT NULL COMMENT '支付通道ID',
  `extra` text COMMENT '特定渠道发起时额外参数',
  `amount` decimal(12,3) UNSIGNED NOT NULL COMMENT '订单金额,单位是元,12-9保留3位小数',
  `income` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '实付金额',
  `user_in` decimal(12,3) NOT NULL DEFAULT '0.000' COMMENT '商户收入',
  `agent_in` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '代理收入',
  `platform_in` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '平台收入',
  `currency` varchar(3) NOT NULL DEFAULT 'CNY' COMMENT '三位货币代码,人民币:CNY',
  `client_ip` varchar(32) NOT NULL COMMENT '客户端IP',
  `return_url` varchar(128) NOT NULL COMMENT '同步通知地址',
  `notify_url` varchar(128) NOT NULL COMMENT '异步通知地址',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单状态:0-已取消-1-待付款，2-已付款',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `bd_remarks` varchar(455) NOT NULL,
  `visite_time` int(10) NOT NULL DEFAULT '0' COMMENT 'è®¿é®æ¶é´',
  `real_need_amount` decimal(12,3) NOT NULL COMMENT 'éè¦ç¨·æ¯ä»éé¢',
  `image_url` varchar(445) NOT NULL COMMENT 'éè¦ç¨·æ¯ä»éé¢',
  `request_log` varchar(445) NOT NULL COMMENT 'log',
  `visite_show_time` int(10) NOT NULL DEFAULT '0' COMMENT 'å è½½å®æ¶é´',
  `request_elapsed_time` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '请求时间',
  `remark` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交易订单表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_orders_notify`
--

CREATE TABLE `cm_orders_notify` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `is_status` int(3) UNSIGNED NOT NULL DEFAULT '404',
  `result` varchar(300) NOT NULL DEFAULT '' COMMENT '请求相响应',
  `content` text NOT NULL COMMENT '原始返回内容',
  `times` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '请求次数',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交易订单通知表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_pay_account`
--

CREATE TABLE `cm_pay_account` (
  `id` bigint(10) NOT NULL COMMENT '账号ID',
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
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `max_deposit_money` decimal(12,3) NOT NULL,
  `min_deposit_money` decimal(12,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付渠道账户表';

--
-- 转存表中的数据 `cm_pay_account`
--

INSERT INTO `cm_pay_account` (`id`, `cnl_id`, `co_id`, `name`, `rate`, `urate`, `grate`, `daily`, `single`, `timeslot`, `param`, `remarks`, `status`, `create_time`, `update_time`, `max_deposit_money`, `min_deposit_money`) VALUES
(193, 33, '30', '卡转卡', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1666212550, 1666212550, '10000.000', '0.000'),
(194, 33, '31', '支付宝UID', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', -1, 1667648386, 1668269145, '10000.000', '0.000'),
(195, 34, '32', '支付宝UID', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"0:0\",\"end\":\"0:0\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1667655982, 1668268669, '10000.000', '0.000'),
(196, 35, '32', '支付宝uid', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1668269245, 1668269245, '10000.000', '0.000'),
(197, 36, '39', '支付宝扫码', '0.000', '1.000', '0.998', '500000000.000', '500000.000', '{\"start\":\"0:0\",\"end\":\"0:0\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1673186081, 1681364485, '500000.000', '1.000'),
(198, 37, '43', '京东E卡', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1674756109, 1674756109, '10000.000', '0.000'),
(199, 38, '45', 'QQ面对面红包', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"0:0\",\"end\":\"0:0\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1674885284, 1674885300, '200.000', '0.000'),
(200, 39, '46', '淘宝现金红包', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1674894506, 1674894506, '200.000', '0.000'),
(201, 40, '47', '支付宝转账码', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1674894568, 1674894568, '10000.000', '0.000'),
(202, 41, '40', '数字人民币', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1674988351, 1674988351, '10000.000', '0.000'),
(203, 42, '34', '支付宝小荷包', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1675147984, 1675147984, '10000.000', '0.000'),
(204, 43, '36', '微信群红包', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"0:0\",\"end\":\"0:0\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1675585975, 1675852827, '10000.000', '1.000'),
(205, 45, '33', '支付宝uid小额', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1675614878, 1675614878, '10000.000', '0.000'),
(206, 46, '32', '支付宝UID大额', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1675675376, 1675675376, '10000.000', '0.000'),
(207, 47, '41', '抖音群红包', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1675853338, 1675853338, '10000.000', '0.000'),
(208, 48, '48', '支付宝工作证', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1675949314, 1675949314, '10000.000', '0.000'),
(209, 49, '51', '仟信好友转账', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1675949374, 1675949374, '10000.000', '0.000'),
(210, 50, '37', '支付宝口令红包', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1675963545, 1675963545, '10000.000', '0.000'),
(211, 51, '56', '支付宝当面付', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1680283052, 1680283052, '10000.000', '0.000'),
(212, 52, '56', '支付宝当面付', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1680283702, 1680283702, '10000.000', '0.000'),
(213, 53, '59', '淘宝直付', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1680348581, 1680348581, '10000.000', '0.000'),
(214, 54, '50,52', '数字人民币', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"0:0\",\"end\":\"0:0\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1680626131, 1680626987, '10000.000', '0.000'),
(215, 55, '50', '数字人民币自动', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1680627177, 1680627177, '10000.000', '0.000'),
(216, 56, '58', '支付宝个人转账', '0.000', '1.000', '0.998', '500000000.000', '5000000.000', '{\"start\":\"0:0\",\"end\":\"0:0\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1681127239, 1681316709, '5000000.000', '1.000'),
(217, 57, '54', '支付宝扫码小额', '0.000', '1.000', '0.998', '10000.000', '10000.000', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', '{&quot;mch_id&quot;:&quot;商户支付号&quot;,&quot;mch_key&quot;:&quot;商户支付KEY&quot;,&quot;app_id&quot;:&quot;商户应用号&quot;,&quot;app_key&quot;:&quot;应用KEY&quot;}', '备注', 1, 1681635214, 1681635214, '10000.000', '0.000');

-- --------------------------------------------------------

--
-- 表的结构 `cm_pay_channel`
--

CREATE TABLE `cm_pay_channel` (
  `id` bigint(10) NOT NULL COMMENT '渠道ID',
  `name` varchar(30) NOT NULL COMMENT '支付渠道名称',
  `action` varchar(30) NOT NULL COMMENT '控制器名称,如:Wxpay用于分发处理支付请求',
  `urate` decimal(4,3) NOT NULL DEFAULT '0.998' COMMENT '默认商户分成',
  `grate` decimal(4,3) NOT NULL DEFAULT '0.998' COMMENT '默认代理分成',
  `timeslot` text NOT NULL COMMENT '交易时间段',
  `return_url` varchar(255) NOT NULL COMMENT '同步地址',
  `notify_url` varchar(255) NOT NULL COMMENT '异步地址',
  `remarks` varchar(128) DEFAULT NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '渠道状态,0-停止使用,1-开放使用',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `notify_ips` varchar(445) NOT NULL,
  `ia_allow_notify` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'æ¸ éæ¯å¦åè®¸åè°',
  `channel_fund` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '渠道资金',
  `wirhdraw_charge` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '提现手续费',
  `tg_group_id` varchar(50) NOT NULL DEFAULT '' COMMENT '当前渠tg群id',
  `channel_secret` varchar(50) NOT NULL DEFAULT '' COMMENT '渠道密钥',
  `limit_moneys` varchar(255) NOT NULL DEFAULT '' COMMENT '固定金额 不填写默认不限制'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付渠道表';

--
-- 转存表中的数据 `cm_pay_channel`
--

INSERT INTO `cm_pay_channel` (`id`, `name`, `action`, `urate`, `grate`, `timeslot`, `return_url`, `notify_url`, `remarks`, `status`, `create_time`, `update_time`, `notify_ips`, `ia_allow_notify`, `channel_fund`, `wirhdraw_charge`, `tg_group_id`, `channel_secret`, `limit_moneys`) VALUES
(33, '卡转卡', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://xxxx/api/notify/notify/channel/GumaV2Pay', 'http://xxxx//api/notify/notify/channel/GumaV2Pay', '1', 1, 1666212536, 1666212536, '127.0.0.1', 1, '0.000', '0.000', '', 'cf1276eccede652bddf81be87ce1fd9b', ''),
(34, '支付宝UID', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://ta.sxwzrj.cn/api/notify/notify/channel/GumaV2Pay', 'http://ta.sxwzrj.cn/api/notify/notify/channel/GumaV2Pay', '1', -1, 1667648360, 1668269255, '127.0.0.1', 1, '0.000', '0.000', '', '2f316ece078407b8d1a1245c9a91d6d6', ''),
(35, 'UID', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"0:0\",\"end\":\"0:0\"}', 'http://194.41.36.175/api/notify/notify/channel/GumaV2Pay', 'http://194.41.36.175/api/notify/notify/channel/GumaV2Pay', '1', 1, 1668269190, 1668325863, '194.41.36.175', 1, '0.000', '0.000', '', '814431ca424f1cef7dc001cf751ed96d', ''),
(36, '支付宝扫码', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1673186063, 1673186063, '43.225.47.46', 1, '0.000', '0.000', '', '9c3561911157273bc9b4f6eb115d3c77', ''),
(37, '京东E卡', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1674756077, 1674756077, '43.225.47.46', 1, '0.000', '0.000', '', '78a427d1ca4bc7fee4569ec4f7780452', ''),
(38, 'QQ面对面红包', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1674885277, 1674885277, '43.225.47.46', 1, '0.000', '0.000', '', 'a287039910892620a5bfaaf24e8b81ab', ''),
(39, '淘宝现金红包', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1674894483, 1674894483, '43.225.47.46', 1, '0.000', '0.000', '', '6f5f5ec80b54f7a4e0a3c035a3686b31', ''),
(40, '支付宝转账码', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1674894547, 1674894547, '43.225.47.46', 1, '0.000', '0.000', '', 'b8f485965a318ce3e69a043ad3e73c26', ''),
(41, '数字人民币', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1674988342, 1674988342, '43.225.47.46', 1, '0.000', '0.000', '', '484562f41cc610dddcaa951e4a966b37', ''),
(42, '支付宝小荷包', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1675147971, 1675147971, '43.225.47.46', 1, '0.000', '0.000', '', 'abbe5739c47aedb56ada02cd6e2b16b1', ''),
(43, '微信群红包', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1675585950, 1675585950, '43.225.47.46', 1, '0.000', '0.000', '', '5c5604fd0567dc7d6f7137b854dfd3e5', ''),
(44, '微信群红包', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', -1, 1675606131, 1675606248, '43.225.47.46', 1, '0.000', '0.000', '', '91d560e17508664bf58b9afd90f7ceaf', ''),
(45, '支付宝uid小额', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1675614865, 1675614865, '43.225.47.46', 1, '0.000', '0.000', '', '210508664044492fee10c478a5d976b3', ''),
(46, '支付宝UID大额', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1675675360, 1675675360, '43.225.47.46', 1, '0.000', '0.000', '', '8f56b9d2d2a2818713933b60f4d5180c', ''),
(47, '抖音群红包', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1675853329, 1675853329, '43.225.47.46', 1, '0.000', '0.000', '', 'aa11e41f008930706ec61b3cbd6b3fb5', ''),
(48, '支付宝工作证', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1675949305, 1675949305, '43.225.47.46', 1, '0.000', '0.000', '', 'afefb75fbba85929e33a889b5775b270', ''),
(49, '仟信好友转账', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1675949364, 1675949364, '43.225.47.46', 1, '0.000', '0.000', '', '5ad9e70ea5c7b0bd3450654f05288bb9', ''),
(50, '支付宝口令红包', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.46/api/notify/notify/channel/GumaV2Pay', '1', 1, 1675963533, 1675963533, '43.225.47.46', 1, '0.000', '0.000', '', '8955943e7e024ea739f00118cffaf22a', ''),
(51, '支付宝当面付', 'AlipayWapPay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.56/api/notify/notify/channel/AlipayWapPay', 'http://43.225.47.56/api/notify/notify/channel/AlipayWapPay', '1', -1, 1680283041, 1680283664, '127.0.0.1', 1, '0.000', '0.000', '', 'ae7e0dbd072cb0d1c154d8b040ecf303', ''),
(52, '支付宝当面付', 'AlipayF2FPay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.56/api/notify/notify/channel/AlipayF2FPay', 'http://43.225.47.56/api/notify/notify/channel/AlipayF2FPay', '1', 1, 1680283685, 1680283685, '127.0.0.1', 1, '0.000', '0.000', '', 'ad5a20025888d89b09920bb52bfbd8b6', ''),
(53, '淘宝直付', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.56/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.56/api/notify/notify/channel/GumaV2Pay', '1', 1, 1680348561, 1680348561, '127.0.0.1', 1, '0.000', '0.000', '', '3c28c27d4d82747aeedbdbab7b6cc60b', ''),
(54, '数字人民币自动', 'Gumav2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.56/api/notify/notify/channel/Gumav2Pay', 'http://43.225.47.56/api/notify/notify/channel/Gumav2Pay', '1', -1, 1680626108, 1680627137, '127.0.0.1', 1, '0.000', '0.000', '', 'a99a5f86d25ff7e41557641231dc5fa1', ''),
(55, '数字人民币自动', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.56/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.56/api/notify/notify/channel/GumaV2Pay', '1', 1, 1680627163, 1680627163, '127.0.0.1', 1, '0.000', '0.000', '', 'c05d295e8df6a5d0ed88093a9426ed24', ''),
(56, '支付宝个人转账', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.56/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.56/api/notify/notify/channel/GumaV2Pay', '1', 1, 1681127232, 1681127232, '127.0.0.1', 1, '0.000', '0.000', '', 'f3d394e5f834fce04b6e5fef9051fba5', ''),
(57, '支付宝扫码小额', 'GumaV2Pay', '1.000', '0.998', '{\"start\":\"00:00:00\",\"end\":\"23:59:59\"}', 'http://43.225.47.56/api/notify/notify/channel/GumaV2Pay', 'http://43.225.47.56/api/notify/notify/channel/GumaV2Pay', '1', 1, 1681635198, 1681635198, '127.0.0.1', 1, '0.000', '0.000', '', 'aa496bfa13b2c714ef354d0191ab7985', '');

-- --------------------------------------------------------

--
-- 表的结构 `cm_pay_channel_change`
--

CREATE TABLE `cm_pay_channel_change` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `channel_id` mediumint(8) NOT NULL COMMENT '渠道ID',
  `preinc` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '变动前金额',
  `increase` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '增加金额',
  `reduce` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '减少金额',
  `suffixred` decimal(12,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '变动后金额',
  `remarks` varchar(255) NOT NULL COMMENT '资金变动说明',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `is_flat_op` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否后台人工账变',
  `status` varchar(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='渠道资金变动记录';

-- --------------------------------------------------------

--
-- 表的结构 `cm_pay_channel_price_weight`
--

CREATE TABLE `cm_pay_channel_price_weight` (
  `id` bigint(10) NOT NULL COMMENT '渠道ID',
  `pay_code_id` bigint(10) NOT NULL DEFAULT '0' COMMENT '支付产品id',
  `cnl_id` bigint(10) NOT NULL DEFAULT '0' COMMENT '支付渠道id',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `cnl_weight` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '渠道权重值',
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付产品下列渠道在固定金额下的权重' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `cm_pay_code`
--

CREATE TABLE `cm_pay_code` (
  `id` bigint(10) NOT NULL COMMENT '渠道ID',
  `cnl_id` text,
  `name` varchar(30) NOT NULL COMMENT '支付方式名称',
  `code` varchar(30) NOT NULL COMMENT '支付方式代码,如:wx_native,qq_native,ali_qr;',
  `remarks` varchar(128) DEFAULT NULL COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '方式状态,0-停止使用,1-开放使用',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `cnl_weight` varchar(255) NOT NULL COMMENT 'å½åpaycodeå¯¹åºæ¸ éæé'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交易方式表';

--
-- 转存表中的数据 `cm_pay_code`
--

INSERT INTO `cm_pay_code` (`id`, `cnl_id`, `name`, `code`, `remarks`, `status`, `create_time`, `update_time`, `cnl_weight`) VALUES
(30, '33', '卡转卡', 'kzk', '卡转卡', 1, 1666212478, 1666790612, ''),
(32, '46', '支付宝UID大额', 'alipayUid', '支付宝UID', 1, 1668268619, 1675675385, '{\"34\":\"100\"}'),
(33, '45', '支付宝uid小额', 'alipayUidSmall', '支付宝uid小额', 1, 1668488678, 1675614886, ''),
(34, '42', '支付宝小荷包', 'alipaySmallPurse', '支付宝小荷包', 1, 1670150109, 1675147995, ''),
(35, '', '支付宝UID转账', 'alipayUidTransfer', '支付宝UID转账', 1, 1670828399, 1670868195, ''),
(36, '43', '微信群红包', 'wechatGroupRed', '微信群红包', 1, 1670828681, 1675585985, ''),
(37, '50', '支付宝口令红包', 'alipayPassRed', '支付宝口令红包', 1, 1670828732, 1675963553, ''),
(38, '', '支付宝零花钱', 'alipayPinMoney', '支付宝零花钱', 0, 1670828777, 1670829094, ''),
(39, '36', '支付宝扫码', 'alipayCode', '支付宝扫码', 1, 1670829031, 1673186090, ''),
(40, '41', '数字人民币', 'CnyNumber', '数字人民币', 1, 1670829073, 1674988359, ''),
(41, '47', '抖音群红包', 'douyinGroupRed', '抖音群红包', 1, 1672044867, 1675853345, ''),
(42, '', '微信扫码', 'wechatCode', '微信扫码', 1, 1672129935, 1672580963, ''),
(43, '37', '京东E卡', 'JDECard', '京东E卡', 1, 1672386772, 1674756122, ''),
(44, '', '小程序商品码', 'AppletProducts', '小程序商品码', 1, 1672829029, 1672839420, ''),
(45, '38', 'QQ面对面红包', 'QQFaceRed', 'QQ面对面红包', 1, 1672903308, 1674885308, ''),
(46, '39', '淘宝现金红包', 'taoBaoMoneyRed', '淘宝现金红包', 1, 1673678427, 1674894515, ''),
(47, '40', '支付宝转账码', 'alipayTransferCode', '支付宝转账码', 1, 1673678427, 1674894578, ''),
(48, '48', '支付宝工作证', 'alipayWorkCard', '支付宝工作证', 1, 1673969458, 1675949327, ''),
(50, '55', '数字人民币自动', 'CnyNumberAuto', '数字人民币自动', 1, 1677735345, 1680627192, ''),
(51, '49', '仟信好友转账', 'QianxinTransfer', '仟信好友转账', 1, 1675931119, 1675949381, ''),
(52, NULL, 'æ·˜å®Eå¡', 'taoBaoEcard', 'æ·˜å®Eå¡', 1, 1666212478, 1666212478, ''),
(54, '57', '支付宝扫码小额', 'alipayCodeSmall', '支付宝扫码小额', 1, 1677742947, 1681635224, ''),
(55, '', '聚合码', 'AggregateCode', '聚合码', 1, 1678886121, 1680185549, ''),
(56, '52', '支付宝当面付', 'alipayF2F', '支付宝当面付', 1, 1679730340, 1680283710, ''),
(57, '', '支付宝手机网站', 'alipayWap', '支付宝手机网站', 1, 1679848964, 1680185580, ''),
(58, '56', '支付宝个人转账', 'alipayTransfer', '支付宝个人转账', 1, 1679849012, 1681127247, ''),
(59, '53', '淘宝直付', 'taoBaoDirectPay', '淘宝直付', 1, 1679931309, 1680348587, ''),
(62, '', '乐付天宏卡', 'LeFuTianHongKa', '乐付天宏卡', 1, 1684413726, 1684413726, ''),
(63, '', '钉钉群收款', 'DingDingGroup', '钉钉群收款', 1, 1684481388, 1684494608, ''),
(255, NULL, '代付', 'daifu', '代付', 1, 1666212478, 1666212478, '');

-- --------------------------------------------------------

--
-- 表的结构 `cm_shop`
--

CREATE TABLE `cm_shop` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '店铺名称',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '店铺类型',
  `onlinedate` int(11) DEFAULT NULL COMMENT '最后在线时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1在线，2不在线，3停止健康，4停用',
  `password` varchar(45) DEFAULT NULL,
  `token` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店铺';

--
-- 转存表中的数据 `cm_shop`
--

INSERT INTO `cm_shop` (`id`, `uid`, `name`, `type`, `onlinedate`, `status`, `password`, `token`) VALUES
(1, 63, '小陈中心', 1, NULL, 1, NULL, NULL),
(2, 245, '给个机会', 1, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `cm_tg_query_order_records`
--

CREATE TABLE `cm_tg_query_order_records` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `order_no` char(40) NOT NULL COMMENT '查询的订单号',
  `tg_message_id` char(30) NOT NULL COMMENT '查单的消息ID',
  `tg_group_id` char(30) NOT NULL COMMENT '查单的群组ID',
  `success` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单成功 1是 0否',
  `create_time` int(11) NOT NULL COMMENT '创建时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_transaction`
--

CREATE TABLE `cm_transaction` (
  `id` bigint(10) NOT NULL,
  `uid` mediumint(8) DEFAULT NULL COMMENT '商户id',
  `order_no` varchar(80) DEFAULT NULL COMMENT '交易订单号',
  `amount` decimal(12,3) DEFAULT NULL COMMENT '交易金额',
  `platform` tinyint(1) DEFAULT NULL COMMENT '交易平台:1-支付宝,2-微信',
  `platform_number` varchar(200) DEFAULT NULL COMMENT '交易平台交易流水号',
  `status` tinyint(1) DEFAULT NULL COMMENT '交易状态',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='交易流水表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_user`
--

CREATE TABLE `cm_user` (
  `uid` mediumint(8) NOT NULL COMMENT '商户uid',
  `puid` mediumint(8) NOT NULL DEFAULT '0',
  `account` varchar(50) NOT NULL COMMENT '商户邮件',
  `username` varchar(30) NOT NULL COMMENT '商户名称',
  `auth_code` varchar(32) DEFAULT NULL COMMENT '8位安全码，注册时发送跟随邮件',
  `password` varchar(50) NOT NULL COMMENT '商户登录密码',
  `phone` varchar(250) NOT NULL COMMENT '手机号',
  `qq` varchar(250) NOT NULL COMMENT 'QQ',
  `is_agent` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '代理商',
  `is_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '验证账号',
  `is_verify_phone` tinyint(1) NOT NULL DEFAULT '0' COMMENT '验证手机',
  `is_daifu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否允许代付',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商户状态,0-未激活,1-使用中,2-禁用',
  `is_hide_withdrawal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏提现',
  `withdrawal_charge` decimal(10,2) NOT NULL DEFAULT '5.00' COMMENT '提现手续费',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
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
  `daifu_transfer_ids` varchar(2083) NOT NULL COMMENT '指定中转平台'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户信息表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_user_account`
--

CREATE TABLE `cm_user_account` (
  `id` bigint(10) NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `bank_id` mediumint(8) NOT NULL DEFAULT '1' COMMENT '开户行(关联银行表)',
  `account` varchar(250) NOT NULL COMMENT '开户号',
  `address` varchar(250) NOT NULL COMMENT '开户所在地',
  `remarks` varchar(250) NOT NULL COMMENT '备注',
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认账户,0-不默认,1-默认',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `account_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户结算账户表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_user_auth`
--

CREATE TABLE `cm_user_auth` (
  `id` bigint(10) NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `realname` varchar(30) NOT NULL DEFAULT '1' COMMENT '开户行(关联银行表)',
  `sfznum` varchar(18) NOT NULL COMMENT '开户号',
  `card` text NOT NULL COMMENT '认证详情',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户认证信息表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_user_daifuprofit`
--

CREATE TABLE `cm_user_daifuprofit` (
  `id` bigint(10) NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `service_rate` decimal(4,3) UNSIGNED NOT NULL DEFAULT '0.000' COMMENT '费率',
  `service_charge` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '单笔手续费',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户代付费率表';

-- --------------------------------------------------------

--
-- 表的结构 `cm_user_padmin`
--

CREATE TABLE `cm_user_padmin` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '·ID',
  `p_admin_id` mediumint(8) NOT NULL COMMENT 'è·å¹³å°ç®¡çåid',
  `p_admin_appkey` varchar(255) NOT NULL DEFAULT '' COMMENT 'è·å¹³å°çç®¡çåappkeyç§é¥',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1æ­£å¸¸ 0ç¦æ­¢æä½',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT 'å»ºæ¶é´',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT 'æ´æ°æ¶é´'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cm_user_pay_code`
--

CREATE TABLE `cm_user_pay_code` (
  `id` bigint(10) NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '·ID',
  `co_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'æ¯ä»pay_codeä¸»é®ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:å¼å¯ 0:å³é­',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT 'å»ºæ¶é´',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT 'æ´æ°æ¶é´'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='·æ¯ä»æ¸ éè¡¨å³èpay_code';

-- --------------------------------------------------------

--
-- 表的结构 `cm_user_pay_code_appoint`
--

CREATE TABLE `cm_user_pay_code_appoint` (
  `appoint_id` int(11) NOT NULL COMMENT 'ID',
  `uid` int(11) NOT NULL COMMENT '用户',
  `pay_code_id` int(11) NOT NULL COMMENT '支付代码',
  `cnl_id` int(11) NOT NULL COMMENT '指定渠道',
  `createtime` int(11) NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `cm_user_profit`
--

CREATE TABLE `cm_user_profit` (
  `id` bigint(10) NOT NULL,
  `uid` mediumint(8) NOT NULL COMMENT '商户ID',
  `cnl_id` int(10) UNSIGNED NOT NULL,
  `urate` decimal(4,3) UNSIGNED NOT NULL DEFAULT '0.000',
  `grate` decimal(4,3) UNSIGNED NOT NULL DEFAULT '0.000',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `single_handling_charge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单笔手续费'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户分润表';

--
-- 转储表的索引
--

--
-- 表的索引 `cm_action_log`
--
ALTER TABLE `cm_action_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_admin`
--
ALTER TABLE `cm_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- 表的索引 `cm_admin_bill`
--
ALTER TABLE `cm_admin_bill`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `cm_admin_rate`
--
ALTER TABLE `cm_admin_rate`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_admin_recharge_record`
--
ALTER TABLE `cm_admin_recharge_record`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `cm_api`
--
ALTER TABLE `cm_api`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_domain_unique` (`id`,`domain`,`uid`) USING BTREE;

--
-- 表的索引 `cm_article`
--
ALTER TABLE `cm_article`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `article_index` (`id`,`title`,`status`) USING BTREE;

--
-- 表的索引 `cm_auth_group`
--
ALTER TABLE `cm_auth_group`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_balance`
--
ALTER TABLE `cm_balance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cash_index` (`id`,`uid`) USING BTREE,
  ADD UNIQUE KEY `uid_index` (`uid`);

--
-- 表的索引 `cm_balance_cash`
--
ALTER TABLE `cm_balance_cash`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cash_index` (`id`,`uid`,`cash_no`,`status`) USING BTREE;

--
-- 表的索引 `cm_balance_change`
--
ALTER TABLE `cm_balance_change`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `change_index` (`id`,`uid`,`type`,`status`) USING BTREE;

--
-- 表的索引 `cm_bank`
--
ALTER TABLE `cm_bank`
  ADD PRIMARY KEY (`bank_id`) USING BTREE,
  ADD KEY `status` (`is_del`) USING BTREE;

--
-- 表的索引 `cm_banker`
--
ALTER TABLE `cm_banker`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_banktobank_sms`
--
ALTER TABLE `cm_banktobank_sms`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_config`
--
ALTER TABLE `cm_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `group` (`group`);

--
-- 表的索引 `cm_dafiu_account`
--
ALTER TABLE `cm_dafiu_account`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_daifu_orders`
--
ALTER TABLE `cm_daifu_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`,`out_trade_no`);

--
-- 表的索引 `cm_daifu_orders_transfer`
--
ALTER TABLE `cm_daifu_orders_transfer`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_deposite_card`
--
ALTER TABLE `cm_deposite_card`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_deposite_orders`
--
ALTER TABLE `cm_deposite_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`) USING BTREE;

--
-- 表的索引 `cm_ewm_block_ip`
--
ALTER TABLE `cm_ewm_block_ip`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_ewm_order`
--
ALTER TABLE `cm_ewm_order`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `order_no` (`order_no`) USING BTREE,
  ADD KEY `search` (`order_no`,`gema_username`,`status`,`add_time`) USING BTREE,
  ADD KEY `gema_userid_index` (`gema_userid`);

--
-- 表的索引 `cm_ewm_pay_code`
--
ALTER TABLE `cm_ewm_pay_code`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `cm_gemapay_code`
--
ALTER TABLE `cm_gemapay_code`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_jdek_sale`
--
ALTER TABLE `cm_jdek_sale`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_jobs`
--
ALTER TABLE `cm_jobs`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_menu`
--
ALTER TABLE `cm_menu`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_ms`
--
ALTER TABLE `cm_ms`
  ADD PRIMARY KEY (`userid`) USING BTREE,
  ADD KEY `username` (`username`) USING BTREE;

--
-- 表的索引 `cm_ms_rate`
--
ALTER TABLE `cm_ms_rate`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_ms_somebill`
--
ALTER TABLE `cm_ms_somebill`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `cm_ms_white_ip`
--
ALTER TABLE `cm_ms_white_ip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ms_id` (`ms_id`);

--
-- 表的索引 `cm_notice`
--
ALTER TABLE `cm_notice`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_orders`
--
ALTER TABLE `cm_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_no_index` (`out_trade_no`,`trade_no`,`uid`,`channel`) USING BTREE,
  ADD UNIQUE KEY `trade_no_index` (`trade_no`) USING BTREE,
  ADD KEY `stat` (`cnl_id`,`create_time`) USING BTREE,
  ADD KEY `stat1` (`cnl_id`,`status`,`create_time`) USING BTREE;

--
-- 表的索引 `cm_orders_notify`
--
ALTER TABLE `cm_orders_notify`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- 表的索引 `cm_pay_account`
--
ALTER TABLE `cm_pay_account`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_pay_channel`
--
ALTER TABLE `cm_pay_channel`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_pay_channel_change`
--
ALTER TABLE `cm_pay_channel_change`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_pay_channel_price_weight`
--
ALTER TABLE `cm_pay_channel_price_weight`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- 表的索引 `cm_pay_code`
--
ALTER TABLE `cm_pay_code`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_shop`
--
ALTER TABLE `cm_shop`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_tg_query_order_records`
--
ALTER TABLE `cm_tg_query_order_records`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_transaction`
--
ALTER TABLE `cm_transaction`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_index` (`order_no`,`platform`,`uid`,`amount`) USING BTREE;

--
-- 表的索引 `cm_user`
--
ALTER TABLE `cm_user`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `user_name_unique` (`account`,`uid`) USING BTREE;

--
-- 表的索引 `cm_user_account`
--
ALTER TABLE `cm_user_account`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_user_auth`
--
ALTER TABLE `cm_user_auth`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_user_daifuprofit`
--
ALTER TABLE `cm_user_daifuprofit`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_user_padmin`
--
ALTER TABLE `cm_user_padmin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`) USING BTREE;

--
-- 表的索引 `cm_user_pay_code`
--
ALTER TABLE `cm_user_pay_code`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `cm_user_pay_code_appoint`
--
ALTER TABLE `cm_user_pay_code_appoint`
  ADD PRIMARY KEY (`appoint_id`),
  ADD KEY `where` (`pay_code_id`,`uid`);

--
-- 表的索引 `cm_user_profit`
--
ALTER TABLE `cm_user_profit`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cm_action_log`
--
ALTER TABLE `cm_action_log`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=40;

--
-- 使用表AUTO_INCREMENT `cm_admin`
--
ALTER TABLE `cm_admin`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `cm_admin_bill`
--
ALTER TABLE `cm_admin_bill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id', AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `cm_admin_rate`
--
ALTER TABLE `cm_admin_rate`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_admin_recharge_record`
--
ALTER TABLE `cm_admin_recharge_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id';

--
-- 使用表AUTO_INCREMENT `cm_api`
--
ALTER TABLE `cm_api`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_article`
--
ALTER TABLE `cm_article`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章ID';

--
-- 使用表AUTO_INCREMENT `cm_auth_group`
--
ALTER TABLE `cm_auth_group`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键', AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `cm_balance`
--
ALTER TABLE `cm_balance`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_balance_cash`
--
ALTER TABLE `cm_balance_cash`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_balance_change`
--
ALTER TABLE `cm_balance_change`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_bank`
--
ALTER TABLE `cm_bank`
  MODIFY `bank_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_banker`
--
ALTER TABLE `cm_banker`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '银行ID', AUTO_INCREMENT=298;

--
-- 使用表AUTO_INCREMENT `cm_banktobank_sms`
--
ALTER TABLE `cm_banktobank_sms`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_config`
--
ALTER TABLE `cm_config`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置ID', AUTO_INCREMENT=1712;

--
-- 使用表AUTO_INCREMENT `cm_dafiu_account`
--
ALTER TABLE `cm_dafiu_account`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_daifu_orders`
--
ALTER TABLE `cm_daifu_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_daifu_orders_transfer`
--
ALTER TABLE `cm_daifu_orders_transfer`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_deposite_card`
--
ALTER TABLE `cm_deposite_card`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_deposite_orders`
--
ALTER TABLE `cm_deposite_orders`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_ewm_block_ip`
--
ALTER TABLE `cm_ewm_block_ip`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_ewm_order`
--
ALTER TABLE `cm_ewm_order`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_ewm_pay_code`
--
ALTER TABLE `cm_ewm_pay_code`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_gemapay_code`
--
ALTER TABLE `cm_gemapay_code`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_jdek_sale`
--
ALTER TABLE `cm_jdek_sale`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_menu`
--
ALTER TABLE `cm_menu`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文档ID', AUTO_INCREMENT=470;

--
-- 使用表AUTO_INCREMENT `cm_ms`
--
ALTER TABLE `cm_ms`
  MODIFY `userid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_ms_rate`
--
ALTER TABLE `cm_ms_rate`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_ms_somebill`
--
ALTER TABLE `cm_ms_somebill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id';

--
-- 使用表AUTO_INCREMENT `cm_ms_white_ip`
--
ALTER TABLE `cm_ms_white_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_notice`
--
ALTER TABLE `cm_notice`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_orders`
--
ALTER TABLE `cm_orders`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '订单id';

--
-- 使用表AUTO_INCREMENT `cm_orders_notify`
--
ALTER TABLE `cm_orders_notify`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_pay_account`
--
ALTER TABLE `cm_pay_account`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '账号ID', AUTO_INCREMENT=218;

--
-- 使用表AUTO_INCREMENT `cm_pay_channel`
--
ALTER TABLE `cm_pay_channel`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '渠道ID', AUTO_INCREMENT=58;

--
-- 使用表AUTO_INCREMENT `cm_pay_channel_change`
--
ALTER TABLE `cm_pay_channel_change`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_pay_channel_price_weight`
--
ALTER TABLE `cm_pay_channel_price_weight`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '渠道ID';

--
-- 使用表AUTO_INCREMENT `cm_pay_code`
--
ALTER TABLE `cm_pay_code`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT COMMENT '渠道ID', AUTO_INCREMENT=256;

--
-- 使用表AUTO_INCREMENT `cm_shop`
--
ALTER TABLE `cm_shop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `cm_tg_query_order_records`
--
ALTER TABLE `cm_tg_query_order_records`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33870;

--
-- 使用表AUTO_INCREMENT `cm_transaction`
--
ALTER TABLE `cm_transaction`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_user`
--
ALTER TABLE `cm_user`
  MODIFY `uid` mediumint(8) NOT NULL AUTO_INCREMENT COMMENT '商户uid';

--
-- 使用表AUTO_INCREMENT `cm_user_account`
--
ALTER TABLE `cm_user_account`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_user_auth`
--
ALTER TABLE `cm_user_auth`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_user_daifuprofit`
--
ALTER TABLE `cm_user_daifuprofit`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_user_padmin`
--
ALTER TABLE `cm_user_padmin`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_user_pay_code`
--
ALTER TABLE `cm_user_pay_code`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `cm_user_pay_code_appoint`
--
ALTER TABLE `cm_user_pay_code_appoint`
  MODIFY `appoint_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- 使用表AUTO_INCREMENT `cm_user_profit`
--
ALTER TABLE `cm_user_profit`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
