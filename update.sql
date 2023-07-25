-- INSERT INTO `cm_config` (`name`,`title`,`type`,`sort`,`group` ,`value` ,`extra` ,`describe` ,`status`, `create_time`, `update_time`, `admin_id`)
-- VALUES ('usdt_recharge_address', 'usdt充值地址' , 1, 0, 0, 'TMAdjy4u5v3qqJrBNyD7zrqyLNRNAvrd3k', '', '', 1, '1666212478', '1666212478', 1);

ALTER TABLE `cm_ms` ADD COLUMN   `admin_work_status` int(1) NOT NULL DEFAULT '1' COMMENT '管理员码商接单状态' AFTER `work_status`;

-- INSERT INTO `cm_config` (`name`,`title`,`type`,`sort`,`group` ,`value` ,`extra` ,`describe` ,`status`, `create_time`, `update_time`)
-- VALUES ('usdt_rate', 'usdt充值费率' , 1, 0, 0, '6.8', '', '', 1, '1666212478', '1666212478');

ALTER TABLE `cm_ewm_order` ADD `extra` TEXT NOT NULL COMMENT '附加信息' AFTER `callback_times` ;

ALTER TABLE `cm_orders_notify` ADD `content` text NOT NULL COMMENT '原始返回内容' AFTER `result` ;

ALTER TABLE `cm_daifu_orders` ADD `is_lock` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否锁定订单 1是 0否' ;

ALTER TABLE `cm_daifu_orders` ADD `chongzhen` tinyint(1) NOT NULL DEFAULT 0 COMMENT '冲正' ;
INSERT INTO `cm_pay_code` VALUES ('50', '', '数字人民币自动', 'CnyNumberAuto', '数字人民币自动', '1', '1677735345', '1678533632', '');
ALTER TABLE `cm_api` ADD COLUMN `min_amount` int(10) NOT NULL DEFAULT 0 COMMENT '商户下单请求最小金额';
ALTER TABLE `cm_api` ADD COLUMN `max_amount` int(10) NOT NULL DEFAULT 0 COMMENT '商户下单请求最大金额';
INSERT INTO `cm_pay_code` VALUES ('55', '', '聚合码', 'AggregateCode', '聚合码', '1', '1678886121', '1678886121', '');
ALTER TABLE `cm_api` ADD `is_notify_status` INT( 11 ) NOT NULL DEFAULT '1' COMMENT '是否支持回调' AFTER `max_amount` ;
ALTER TABLE `cm_ewm_pay_code` ADD `extra` TEXT NOT NULL COMMENT '其他信息';
INSERT INTO cm_pay_code VALUES ('52', null, '淘宝E卡', 'taoBaoEcard', '淘宝E卡', '1', '1666212478', '1666212478', '');
ALTER TABLE `cm_orders` CHANGE `remark` `remark` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL ;
ALTER TABLE `cm_ewm_pay_code` ADD `publicKey` TEXT NULL DEFAULT NULL COMMENT '公钥';
ALTER TABLE `cm_ewm_pay_code` ADD `privateKey` TEXT NULL DEFAULT NULL COMMENT '私钥';
INSERT INTO `cm_pay_code` VALUES ('56', '', '支付宝当面付', 'alipayF2F', '支付宝当面付', '1', '1679730340', '1679740220', '');
INSERT INTO `cm_pay_code` VALUES ('57', '', '支付宝手机网站', 'alipayWap', '支付宝手机网站', '1', '1679848964', '1679848964', '');
INSERT INTO `cm_pay_code` VALUES ('58', '', '支付宝个人转账', 'alipayTransfer', '支付宝个人转账', '1', '1679849012', '1679849012', '');
ALTER TABLE `cm_daifu_orders_transfer` ADD `balance` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `admin_id` ;
ALTER TABLE `cm_daifu_orders_transfer` ADD `is_query_balance` INT( 11 ) NOT NULL AFTER `balance` ;
ALTER TABLE `cm_daifu_orders_transfer` ADD `query_balance_url` VARCHAR( 2083 ) NOT NULL AFTER `is_query_balance` ;
ALTER TABLE `cm_daifu_orders_transfer` ADD `min_money` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `query_balance_url` ;
ALTER TABLE `cm_daifu_orders_transfer` ADD `max_money` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `min_money` ;
ALTER TABLE `cm_daifu_orders_transfer` ADD `weight` INT( 11 ) NOT NULL DEFAULT '1' AFTER `max_money` ;
ALTER TABLE `cm_orders_notify` ADD `content` text NOT NULL COMMENT '原始返回内容' AFTER `result` ;
INSERT INTO `cm_pay_code` VALUES ('59', '', '淘宝直付', 'taoBaoDirectPay', '淘宝直付', '1', '1679931309', '1680086166', '');
INSERT INTO `cm_pay_code` VALUES ('54', '', '小额支付宝扫码', 'alipayCodeSmall', '支付宝扫码小额', '1', '1677742947', '1677742947', '');

ALTER TABLE `cm_ewm_pay_code` ADD `remark` VARCHAR(255) NOT NULL COMMENT '备注';
ALTER TABLE `cm_ewm_order` ADD `cardAccount` VARCHAR( 2083 ) NOT NULL COMMENT '卡号' AFTER `cardKey` ;
INSERT INTO `cm_pay_code` VALUES ('61', '', '汇元易付卡', 'HuiYuanYiFuKa', '汇元易付卡', '1', '1680687742', '1680688118', '');
ALTER TABLE `cm_jdek_sale` ADD `pay_code` INT( 11 ) NOT NULL DEFAULT '43' COMMENT '通道id' AFTER `ms_id` ;
ALTER TABLE `cm_daifu_orders` ADD COLUMN `notify_params` text NOT NULL COMMENT '回调的请求参数' AFTER `notify_result` ;
INSERT INTO `cm_pay_code` VALUES ('60', '', '骏网', 'JunWeb', '骏网', '1', '1680331925', '1680331991', '');

ALTER TABLE `cm_banktobank_sms` ADD COLUMN `code_id` INT(11) NOT NULL DEFAULT 0 COMMENT '二维码ID' AFTER `order_id`;
ALTER TABLE `cm_banktobank_sms` ADD COLUMN `admin_id` INT(11) NOT NULL DEFAULT 0 COMMENT 'admin_id' AFTER `order_id`;
ALTER TABLE `cm_banktobank_sms` ADD COLUMN `balance` DECIMAL(11,3) NOT NULL DEFAULT 0 COMMENT '余额' AFTER `order_id`;
ALTER TABLE `cm_ewm_pay_code` ADD `account_type` INT( 11 ) NOT NULL DEFAULT '1' COMMENT '二维码类型1=解析2=上传' AFTER `remark` ;
ALTER TABLE `cm_ms` ADD COLUMN `disable_money` DECIMAL(11,3) NOT NULL DEFAULT 0 COMMENT '冻结余额' AFTER `money`;
ALTER TABLE `cm_ms_somebill` ADD COLUMN `type` varchar(20) NOT NULL DEFAULT 'enable' COMMENT '资金类型' AFTER `jl_class`;

ALTER TABLE `cm_orders` CHANGE `return_url` `return_url` VARCHAR( 520 ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '同步通知地址';
ALTER TABLE `cm_orders` CHANGE `notify_url` `notify_url` VARCHAR( 520 ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '异步通知地址';

ALTER TABLE `cm_ms`  modify column `money` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '用户余额';
-- alter table cm_orders modify column out_trade_no varchar(50);
-- alter table cm_orders modify column trade_no varchar(50);;
ALTER TABLE `cm_orders` CHANGE `trade_no` `trade_no` VARCHAR( 50 ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '交易订单号';
ALTER TABLE `cm_orders` CHANGE `out_trade_no` `out_trade_no` VARCHAR( 50 ) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '商户订单号';

ALTER TABLE `cm_ms` ADD COLUMN `cookie_token` varchar(100) NOT NULL COMMENT '客户端cookie token';
ALTER TABLE `cm_ms` ADD `team_work_status` INT( 11 ) NOT NULL DEFAULT '1' COMMENT '团队接单状态' AFTER `disable_money` ;
ALTER TABLE `cm_ms` ADD `firs_login` INT( 11 ) NOT NULL DEFAULT '0' COMMENT '是否修改了密码' AFTER `team_work_status` ;
INSERT INTO `cm_pay_code` VALUES ('51', '', '中额支付宝扫码', 'QianxinTransfer', '中额支付宝扫码', '1', '1675931119', '1675949381', '');
ALTER TABLE `cm_auth_group` MODIFY `rules` VARCHAR(2083);
-- ----------------------------
-- Table structure for cm_ms_channel
-- ----------------------------
CREATE TABLE `cm_ms_channel` (
                                 `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
                                 `ms_id` int(11) NOT NULL COMMENT '码商id',
                                 `pay_code_id` int(11) NOT NULL COMMENT '通道id',
                                 `min_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最小请求金额',
                                 `max_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最大请求金额',
                                 `status` tinyint(1) DEFAULT '1' COMMENT '开关',
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cm_ms_channel
-- ----------------------------
ALTER TABLE `cm_user` ADD `team_ids` VARCHAR( 2083 ) NOT NULL COMMENT '指定团队' AFTER `daifu_transfer_ids` ;
ALTER TABLE `cm_ewm_pay_code` ADD `yesterday_receiving_number` INT( 11 ) NOT NULL DEFAULT '0' AFTER `today_receiving_amount` ;
ALTER TABLE `cm_ewm_pay_code` ADD `yesterday_receiving_amount` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `yesterday_receiving_number` ;
ALTER TABLE `cm_daifu_orders` ADD `admin_id` INT( 11 ) NOT NULL COMMENT '所属管理' AFTER `chongzhen` ;


CREATE TABLE `cm_ms_balance_recharge` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
        `ms_id` int(11) NOT NULL COMMENT '码商id',
        `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
        `admin_id` int(11) NOT NULL COMMENT '所属管理',
        `reason` varchar(255) NOT NULL COMMENT '申请原因',
        `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
        `create_time` int(11) NOT NULL COMMENT '创建时间',
        `update_time` int(11) NOT NULL COMMENT '更新时间',
        PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;


ALTER TABLE `cm_ewm_order` ADD `uid` INT( 11 ) NOT NULL DEFAULT '0' COMMENT '商户id';


ALTER TABLE `cm_ms` ADD `daifu_money` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '代付余额' AFTER `money` ;

ALTER TABLE `cm_ewm_order` ADD `updateTime` INT(10) NULL DEFAULT NULL AFTER `add_time`;
ALTER TABLE `cm_ewm_order` ADD `xiaoka_id` INT(11) NOT NULL DEFAULT '0' COMMENT '销卡平台id' AFTER `updateTime`;

ALTER TABLE cm_ms ADD INDEX idx_money (money);
ALTER TABLE cm_ms ADD INDEX idx_cash_pledge (cash_pledge);

-- UPDATE cm_ms
-- SET cash_pledge = 2000.00
-- WHERE cash_pledge = 0.00;

ALTER TABLE `cm_daifu_orders` ADD `daifu_transfer_id` INT(11) NOT NULL DEFAULT '0' COMMENT '中转平台id' AFTER `daifu_transfer_name`;

ALTER TABLE `cm_ms_somebill` ADD `admin_id` INT(11) NOT NULL DEFAULT '1' COMMENT '平台id' AFTER `last_amount`;

ALTER TABLE `cm_notice` ADD `admin_id` INT(11) NOT NULL COMMENT '管理员id' AFTER `status`;

CREATE TABLE `cm_ms_read_notice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ms_id` int(11) NOT NULL COMMENT '码商ID',
  `notice_id` int(11) NOT NULL COMMENT '通知ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;


ALTER TABLE `cm_daifu_orders` ADD `notify_content` TEXT NOT NULL COMMENT '回调内容' AFTER `notify_result`;

-- ALTER TABLE cm_ewm_order ADD INDEX idx_gema_userid (gema_userid);
-- DROP INDEX idx_gema_userid ON cm_ewm_order;
-- CREATE INDEX idx_code_id ON cm_ewm_order (code_id);
-- CREATE INDEX idx_id ON cm_ewm_pay_code (id);
--
-- DROP INDEX idx_code_id ON cm_ewm_order;
-- DROP INDEX idx_id ON cm_ewm_pay_code;

ALTER TABLE cm_ewm_order ADD INDEX idx_add_time (add_time);
CREATE INDEX idx_code_id_id ON cm_ewm_order (code_id, id);
ALTER TABLE cm_daifu_orders ADD INDEX idx_create_time (create_time);

ALTER TABLE cm_ewm_pay_code ADD INDEX idx_ms_id (ms_id);
-- CREATE INDEX idx_pay_code ON cm_ewm_order (code_id, id);
ALTER TABLE cm_ms_somebill ADD INDEX idx_uid (uid);
ALTER TABLE cm_ms_somebill ADD INDEX idx_addtime (addtime);
ALTER TABLE cm_ms ADD INDEX idx_ms_userid (userid);
ALTER TABLE cm_ms ADD INDEX idx_ms_pid (pid);
ALTER TABLE cm_ms ADD INDEX idx_ms_admin_id (admin_id);
ALTER TABLE cm_daifu_orders ADD INDEX idx_daifu_admin_id (admin_id);
ALTER TABLE cm_ewm_order ADD INDEX idx_order_admin_id (admin_id);
ALTER TABLE cm_orders ADD INDEX idx_order_uid (uid);


ALTER TABLE `cm_daifu_orders` ADD COLUMN `transfer_chart2` varchar(2083) NOT NULL COMMENT '代付转账截图2' after `transfer_chart`;
ALTER TABLE `cm_daifu_orders` ADD COLUMN `transfer_chart3` varchar(2083) NOT NULL COMMENT '代付转账截图3' after `transfer_chart2`;
CREATE INDEX idx_ms_id_code_type_id ON cm_ms_rate (ms_id, code_type_id);
ALTER TABLE `cm_admin` ADD `security_code` VARCHAR(32) NOT NULL COMMENT '安全码' AFTER `daifu_auto_transfer`;


CREATE TABLE `cm_user_token` (
     `token` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token',
     `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
     `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
     `expiretime` bigint(16) DEFAULT NULL COMMENT '过期时间',
     PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员Token表';