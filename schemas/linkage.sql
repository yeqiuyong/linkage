--
-- Host: 127.0.0.1    Database: linkage
-- ------------------------------------------------------
-- Server version	5.1.50

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
-- Table structure for table `adminuser`
--
DROP TABLE IF EXISTS `linkage_adminuser`;
CREATE TABLE `linkage_adminuser` (
  `admin_id` INT(11) unsigned NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(32) NOT NULL DEFAULT '',
  `password` CHAR(64)  NOT NULL DEFAULT '',
  `name` VARCHAR(120)  DEFAULT '',
  `mobile` VARCHAR(30) DEFAULT '',
  `email` VARCHAR(70) DEFAULT '',
  `profile_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `token` VARCHAR(255) DEFAULT '',
  `loginip` VARCHAR(31) DEFAULT '127.0.0.1',
  `create_time` INT(11) NOT NULL DEFAULT 0,
  `update_time` INT(11) NOT NULL DEFAULT 0,
  `active` CHAR(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1000000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO linkage_adminuser ( `username`,`password`,`name`,`mobile`,`email`,`profile_id`,`token`,`loginip`,`create_time`,`update_time`,`active`)
VALUES ('admin','$2a$08$ItRuZG9iZHOdRwCObXAAaOAwvw0NzzDd/YrGsdgTRFKk8E4mr3uSy','叶秋永','1881655517','yeqiuyong@aliyun.com',1,'','127.0.0.1',1454660363,1454660363,'Y');

--
-- Table structure for table 'admin_profiles'
--
DROP TABLE IF EXISTS `linkage_admin_profile`;
CREATE TABLE `linkage_admin_profile` (
  `profile_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profile_name` VARCHAR(255) NOT NULL default '',
  PRIMARY KEY (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO linkage_admin_profile ( `profile_name`) VALUES ('超级管理员');
INSERT INTO linkage_admin_profile ( `profile_name`) VALUES ('管理员');

--
-- Table structure for table 'admin_pages_to_profiles'
--
DROP TABLE IF EXISTS `linkage_admin_page_to_profile`;
CREATE TABLE `linkage_admin_page_to_profile` (
  `profile_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_key` VARCHAR(255) NOT NULL DEFAULT '',
  UNIQUE KEY profile_page (profile_id, page_key),
  UNIQUE KEY page_profile (page_key, profile_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `admin_activity_log`
--
DROP TABLE IF EXISTS `linkage_admin_activity_log`;
CREATE TABLE `linkage_admin_activity_log` (
  `log_id` BIGINT(15) NOT NULL AUTO_INCREMENT,
  `access_date` INT(11) NOT NULL DEFAULT 0,
  `admin_id` INT(11) NOT NULL DEFAULT '0',
  `page_accessed` VARCHAR(80) NOT NULL DEFAULT '',
  `page_parameters` TEXT DEFAULT NULL ,
  `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
  `flagged` TINYINT NOT NULL default '0',
  `attention` VARCHAR(255) NOT NULL DEFAULT '',
  `gzpost` MEDIUMBLOB,
  PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table 'admin_menu'
--
DROP TABLE IF EXISTS `linkage_admin_menu`;
CREATE TABLE `linkage_admin_menu` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_key` VARCHAR(255) NOT NULL DEFAULT '',
  `language_key` VARCHAR(255) NOT NULL DEFAULT '',
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`menu_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table 'admin_pages'
--
DROP TABLE IF EXISTS `linkage_admin_page`;
CREATE TABLE `linkage_admin_page` (
  `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_key` VARCHAR(255) NOT NULL DEFAULT '',
  `language_key` VARCHAR(255) NOT NULL DEFAULT '',
  `main_page` VARCHAR(255) NOT NULL DEFAULT '',
  `page_params` VARCHAR(255) NOT NULL DEFAULT '',
  `menu_key` VARCHAR(255) NOT NULL DEFAULT '',
  `display_on_menu` TINYINT(4) NOT NULL DEFAULT 0,
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `clientuser`
--
DROP TABLE IF EXISTS `linkage_clientuser`;
CREATE TABLE `linkage_clientuser` (
  `user_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(32) NOT NULL DEFAULT '',
  `password` CHAR(64) NOT NULL DEFAULT '',
  `token` CHAR(64) DEFAULT NULL ,
  `name` VARCHAR(120) DEFAULT NULL,
  `mobile` VARCHAR(30) NOT NULL DEFAULT '',
  `gender` CHAR(2) DEFAULT 'M',
  `email` VARCHAR(70) DEFAULT NULL,
  `address` VARCHAR(500) DEFAULT NULL,
  `birthday` DATE DEFAULT NULL,
  `icon` VARCHAR(200) DEFAULT NULL,
  `identity_id` CHAR(16) DEFAULT NULL,
  `company_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `loginip` VARCHAR(31) NOT NULL DEFAULT '127.0.0.1',
  `create_time` INT(11) NOT NULL DEFAULT 0,
  `update_time` INT(11) NOT NULL DEFAULT 0,
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0 active;1 inactive;2 pending; 3 banned;4 deleted',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY (`username`),
  UNIQUE KEY (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=1000000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `role`
--
DROP TABLE IF EXISTS `linkage_role`;
CREATE TABLE `linkage_role` (
  `role_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rolename` VARCHAR(32) NOT NULL DEFAULT '',
  `memo` VARCHAR(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
insert into linkage_role ( `rolename`,`memo` ) values ('厂商管理员','发起物流委托流程的工厂，首个在平台注册的特定用户');
insert into linkage_role ( `rolename`,`memo` ) values ('厂商','发起物流委托流程的工厂');
insert into linkage_role ( `rolename`,`memo` ) values ('承运商管理员','承运商公司，首个在平台注册的特定承运商用户');
insert into linkage_role ( `rolename`,`memo` ) values ('承运商','承运商公司，由管理员通过邀请码在平台注册的承运商');
insert into linkage_role ( `rolename`,`memo` ) values ('司机','承运商公司指定司机');

--
-- Table structure for table `permission`
--
DROP TABLE IF EXISTS `linkage_permission`;
CREATE TABLE `linkage_permission` (
  `permission_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `permissionname` VARCHAR(32) NOT NULL DEFAULT '',
  `memo` VARCHAR(128) NOT NULL DEFAULT '',
  `buttons` VARCHAR(50) DEFAULT NULL,
  `checked` INT(11) DEFAULT NULL,
  `expanded` INT(11) NOT NULL DEFAULT '0',
  `icon_cls` VARCHAR(20) DEFAULT NULL,
  `leaf` INT(11) NOT NULL DEFAULT '0',
  `menu_code` VARCHAR(50) NOT NULL,
  `menu_config` VARCHAR(200) DEFAULT NULL,
  `menu_name` VARCHAR(50) NOT NULL,
  `parent_id` BIGINT(20) DEFAULT NULL,
  `sort_order` INT(11) NOT NULL,
  `url` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `user_role`
--
DROP TABLE IF EXISTS `linkage_user_role`;
CREATE TABLE `linkage_user_role` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `role_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `role_permission`
--
DROP TABLE IF EXISTS `linkage_role_permission`;
CREATE TABLE `linkage_role_permission` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `permission_id` INT(11) UNSIGNED NOT NULL,
  `role_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `company`
--
DROP TABLE IF EXISTS `linkage_company`;
CREATE TABLE `linkage_company` (
  `company_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120)  NOT NULL DEFAULT '企业名称',
  `code` CHAR(40) DEFAULT '' COMMENT '企业代号',
  `level` TINYINT(4)  DEFAULT 0 COMMENT '企业等级',
  `credit` INT(11)  DEFAULT 0 COMMENT '企业积分',
  `type` TINYINT(4) unsigned NOT NULL COMMENT '0厂商；1运营商',
  `contactor` VARCHAR(40)  NOT NULL DEFAULT '' COMMENT '联系人',
  `address` VARCHAR(40)  NOT NULL DEFAULT ''COMMENT '联系地址',
  `province` VARCHAR(40)  DEFAULT NULL,
  `city` VARCHAR(40)  DEFAULT NULL,
  `email` VARCHAR(70) DEFAULT NULL,
  `home_page` VARCHAR(120)  NOT NULL DEFAULT '网页',
  `service_phone_1` VARCHAR(30)  NOT NULL DEFAULT '' COMMENT '客户电话1',
  `service_phone_2` VARCHAR(30)  DEFAULT NULL COMMENT '客户电话2',
  `service_phone_3` VARCHAR(30)  DEFAULT NULL COMMENT '客户电话3',
  `service_phone_4` VARCHAR(30)  DEFAULT NULL COMMENT '客户电话4',
  `description` TEXT DEFAULT null COMMENT '企业简介',
  `remark` TEXT DEFAULT NULL COMMENT '备注',
  `logo` VARCHAR(64) DEFAULT '' COMMENT '企业logo',
  `create_time` INT(11) NOT NULL DEFAULT 0,
  `update_time` INT(11) NOT NULL DEFAULT 0,
  `create_by` INT(11) NOT NULL DEFAULT 0 COMMENT '创建人用户id',
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0 active;1 inactive;2 pending; 3 banned; 4 deleted',
  `version` INT(8) NOT NULL DEFAULT 0 COMMENT '每修改一次版本+1，app 端缓存所有企业信息，当检测到企业版本更新，则更新企业信息',
  PRIMARY KEY (`company_id`),
  UNIQUE KEY (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `user_role`
--
DROP TABLE IF EXISTS `linkage_car`;
CREATE TABLE `linkage_car` (
  `car_id`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comapny_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `driver_id` INT(11) UNSIGNED DEFAULT NULL ,
  `license` VARCHAR(16) NOT NULL COMMENT '车牌号',
  `car_type` VARCHAR(100) DEFAULT NULL COMMENT '车类型',
  `memo` VARCHAR(128) DEFAULT NULL COMMENT '其它说明',
  PRIMARY KEY (`car_id`),
  UNIQUE KEY (`license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `linkage_order`
--
DROP TABLE IF EXISTS `linkage_order`;
CREATE TABLE `linkage_order` (
  `order_id` CHAR(64)  NOT NULL DEFAULT '',
  `type` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '码头出口0,码头进口1,内陆柜2,自备柜3',
  `manufacture_id` INT(11) unsigned NOT NULL DEFAULT 0 COMMENT '订单委托厂商',
  `transporter_id` INT(11) unsigned NOT NULL DEFAULT 0 COMMENT '接单承运商',
  `manufacture_contact_name` VARCHAR(120) COMMENT '厂商联系人',
  `manufacture_contact_tel` VARCHAR(30) COMMENT '厂商联系电话',
  `transporter_contact_name` VARCHAR(120) COMMENT '承运商联系人',
  `transporter_contact_tel` VARCHAR(30) COMMENT '承运商联系电话',
  `take_address` VARCHAR(400) COMMENT '货柜接货地址',
  `take_time` INT(11) COMMENT '货柜接货时间',
  `delivery_address` VARCHAR(400) COMMENT '货柜收货地址',
  `delivery_time` INT(11) COMMENT '货柜收货时间',
  `cargos_rent_expire` INT(11) COMMENT '柜组到期日',
  `memo` TEXT COMMENT '特殊事项',
  `create_time` INT(11) NOT NULL DEFAULT 0 COMMENT '订单生成日期',
  `update_time` INT(11) NOT NULL DEFAULT 0 COMMENT '订单修改日期',
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '订单状态',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `linkage_order_export`
--
DROP TABLE IF EXISTS `linkage_order_export`;
CREATE TABLE `linkage_order_export` (
  `order_id` CHAR(64)  NOT NULL DEFAULT '',
  `so` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '',
  `customs_in` INT(11) COMMENT '禁关时间',
  `ship_company` VARCHAR(120) COMMENT '头程公司',
  `ship_name` VARCHAR(120) COMMENT '头程船名',
  `ship_schedule_no` VARCHAR(64) COMMENT '头程班次',
  `is_book_cargo` TINYINT(4) COMMENT '是否与头程越好货柜。0否；1是',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `linkage_order_import`
--
DROP TABLE IF EXISTS `linkage_order_import`;
CREATE TABLE `linkage_order_import` (
  `order_id` CHAR(64)  NOT NULL DEFAULT '',
  `bill_no` CHAR(64)  NOT NULL DEFAULT '提单号',
  `customs_broker` VARCHAR(120) COMMENT '报关行联系人',
  `customshouse_contact` VARCHAR(30) COMMENT '报关行联系电话',
  `cargo_company` VARCHAR(120) COMMENT '二柜公司',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `linkage_order_inland`
--
DROP TABLE IF EXISTS `linkage_order_inland`;
CREATE TABLE `linkage_order_inland` (
  `order_id` CHAR(64)  NOT NULL DEFAULT '',
  `customs_in` INT(11) COMMENT '禁关时间',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Table structure for table `linkage_order_self_cargo`
--
DROP TABLE IF EXISTS `linkage_order_self_cargo`;
CREATE TABLE `linkage_order_self_cargo` (
  `order_id` CHAR(64)  NOT NULL DEFAULT '',
  `is_customs_declare` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '是否须要报关。0不需要，1须要',
  `customs_in` INT(11) COMMENT '报关时间',
  `customs_out` INT(11) COMMENT '报关结束时间',
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `linkage_import_order_2_cargo`
--
DROP TABLE IF EXISTS `linkage_import_order_2_cargo`;
CREATE TABLE `linkage_import_order_2_cargo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` CHAR(64)  NOT NULL DEFAULT '订单号',
  `cargo_no` CHAR(64)  NOT NULL DEFAULT '柜号',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`order_id`, `cargo_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `linkage_driver_task`
--
DROP TABLE IF EXISTS `linkage_driver_task`;
CREATE TABLE `linkage_driver_task` (
  `task_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) UNSIGNED NOT NULL,
  `order_type` TINYINT(4) UNSIGNED NOT NULL COMMENT '码头出口0,码头进口1,内陆柜2,自备柜3',
  `driver_id` INT(11) UNSIGNED NOT NULL,
  `license` VARCHAR(16) DEFAULT '' COMMENT '车牌号',
  `cargo_no` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '货柜号',
  `cargo_type` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '货柜类型',
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0:未委派，1：委派司机，2：到港提柜，3：运柜出港，4：送达卸货(入口)/到达载柜（出口），5：返港还柜,6：拒绝任务',
  `is_accept` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '司机是否接受订单。0:拒绝，1:接受',
  `reject_reason` VARCHAR(400) COMMENT '拒绝接受任务理由',
  `memo` TEXT COMMENT '其它说明',
  `image` VARCHAR(400) COMMENT '司机拍照。图片链接地址',
  `create_time` INT(11) NOT NULL DEFAULT 0 COMMENT '任务生成日期',
  `update_time` INT(11) NOT NULL DEFAULT 0 COMMENT '任务修改日期',
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `linkage_driver_task_history`
--
DROP TABLE IF EXISTS `linkage_driver_task_history`;
CREATE TABLE `linkage_driver_task_history` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id` INT(11) UNSIGNED NOT NULL,
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0:未委派，1：委派司机，2：到港提柜，3：运柜出港，4：送达卸货(入口)/到达载柜（出口），5：返港还柜,6：拒绝任务',
  `memo` TEXT COMMENT '其它说明',
  `image` VARCHAR(400) COMMENT '司机拍照。图片链接地址',
  `create_time` INT(11) NOT NULL DEFAULT 0 COMMENT '任务生成日期',
  `update_time` INT(11) NOT NULL DEFAULT 0 COMMENT '任务修改日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `contact`
--
DROP TABLE IF EXISTS `linkage_contact`;
CREATE TABLE `linkage_contact` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120)  DEFAULT NULL,
  `telephone` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(70)  NOT NULL,
  `comments` TEXT NOT NULL,
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '是否处理。0:为处理；1:已处理;2 删除',
  `create_time` INT(11) NOT NULL DEFAULT 0 COMMENT '任务生成日期',
  `update_time` INT(11) NOT NULL DEFAULT 0 COMMENT '任务修改日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table 'linkage_banner'
--
DROP TABLE IF EXISTS `linkage_banner`;
CREATE TABLE `linkage_banner` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `banners_title` VARCHAR(64) NOT NULL DEFAULT '',
  `banners_url` VARCHAR(255) NOT NULL DEFAULT '',
  `banners_image` VARCHAR(64) NOT NULL DEFAULT '',
  `banners_group` VARCHAR(15) NOT NULL DEFAULT '',
  `banners_html_text` TEXT,
  `expires_date` INT(11) DEFAULT 0,
  `create_time` INT(11)  NOT NULL DEFAULT 0,
  `update_time` INT(11)  not NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT '1',
  `banners_open_new_windows` TINYINT(1) NOT NULL DEFAULT '1',
  `banners_on_ssl` TINYINT(1) NOT NULL DEFAULT '1',
  `banners_sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table 'linkage_notice'
--
DROP TABLE IF EXISTS `linkage_notice`;
CREATE TABLE `linkage_notice` (
  `id` INT(11) NOT NULL auto_increment,
  `type` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '信息类型。0：广告，1.招聘，2:公司通知',
  `image` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `link` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '广告链接',
  `title` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '消息标题',
  `description` TEXT DEFAULT NULL COMMENT '消息描述',
  `memo` TEXT(11) DEFAULT NULL COMMENT '消息详细描述',
  `client_type` TINYINT NOT NULL DEFAULT 0 COMMENT '0 所有人,1 厂商,2 承运商, 3 司机',
  `create_time` INT(11)  NOT NULL DEFAULT 0,
  `update_time` INT(11)  NOT NULL DEFAULT 0,
  `create_by` INT(11) NOT NULL DEFAULT 0 COMMENT '消息发布人id',
  `status` INT NOT NULL DEFAULT 0 COMMENT '0 启用，1 禁用，2删除',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table 'linkage_invite_code'
--
DROP TABLE IF EXISTS `linkage_invite_code`;
CREATE TABLE `linkage_invite_code` (
  `id` INT(11) NOT NULL auto_increment,
  `invite_code` CHAR(16) NOT NULL DEFAULT '' COMMENT '邀请码',
  `qr_code` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '二维码链接',
  `link` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '广告链接',
  `invite_id` INT(11) NOT NULL DEFAULT 0 COMMENT '邀请人id',
  `invite_name` VARCHAR(120) DEFAULT NULL COMMENT '邀请人姓名',
  `invite_mobile` VARCHAR(30) DEFAULT '' COMMENT '邀请人电话',
  `company_id` INT(11) DEFAULT NULL COMMENT '邀请人公司ID',
  `company_name` VARCHAR(120) DEFAULT NULL COMMENT '邀请人公司名称',
  `create_time` INT(11)  NOT NULL DEFAULT 0,
  `update_time` INT(11)  NOT NULL DEFAULT 0,
  `status` INT NOT NULL DEFAULT 0 COMMENT '0 有效，1 无效',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table 'linkage_user_favorite'
--
DROP TABLE IF EXISTS `linkage_user_favorite`;
CREATE TABLE `linkage_user_favorite` (
  `id` INT(11) NOT NULL auto_increment,
  `user_id` INT(11) NOT NULL DEFAULT 0 COMMENT '收藏者id',
  `company_id` INT(11) NOT NULL DEFAULT 0 COMMENT '收藏公司id',
  `create_time` INT(11)  NOT NULL DEFAULT 0,
  `update_time` INT(11)  NOT NULL DEFAULT 0,
  `status` INT NOT NULL DEFAULT 0 COMMENT '0 有效，1 无效,2 删除',
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`user_id`, `company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table 'linkage_user_sys_set'
--
DROP TABLE IF EXISTS `linkage_user_sys_set`;
CREATE TABLE `linkage_user_sys_set` (
  `user_id` INT(11) NOT NULL DEFAULT 0 COMMENT '用户id',
  `receive_sms` TINYINT(4) NOT NULL DEFAULT 1 COMMENT '0 不接收 ，1 接收',
  `receive_email` TINYINT(4)  NOT NULL DEFAULT 1 COMMENT '0 不接收 ，1 接收',
  `create_time` INT(11)  NOT NULL DEFAULT 0,
  `update_time` INT(11)  NOT NULL DEFAULT 0,
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table 'linkage_user_log'
--
DROP TABLE IF EXISTS `linkage_user_log`;
CREATE TABLE `linkage_user_log` (
  `id` INT(11) NOT NULL auto_increment,
  `user_id` INT(11) NOT NULL DEFAULT 0 COMMENT '收藏者id',
  `type` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0 登陆 ，1 登出',
  `create_time` INT(11)  NOT NULL DEFAULT 0,
  `platform` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0 ios, 1 iphone',
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
