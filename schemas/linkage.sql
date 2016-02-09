-- MySQL dump 10.13  Distrib 5.1.50, for apple-darwin10.4.0 (i386)
--
-- Host: 127.0.0.1    Database: invo
-- ------------------------------------------------------
-- Server version	5.1.50

--
-- Table structure for table `adminuser`
--
DROP TABLE IF EXISTS `linkage_adminuser`;
CREATE TABLE `linkage_adminuser` (
  `admin_id` int(11) unsigned NOT NULL AUTO_INCREMENT ,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` char(64)  NOT NULL DEFAULT '',
  `name` varchar(120)  DEFAULT '',
  `mobile` varchar(30) DEFAULT '',
  `email` varchar(70) DEFAULT '',
  `profile_id` int(11) unsigned NOT NULL DEFAULT 0,
  `token` VARCHAR(255) DEFAULT '',
  `loginip` varchar(31) DEFAULT '127.0.0.1',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `active` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
insert into linkage_adminuser ( `username`,`password`,`name`,`mobile`,`email`,`profile_id`,`token`,`loginip`,`create_time`,`update_time`,`active`)
values ('admin','$2a$08$ItRuZG9iZHOdRwCObXAAaOAwvw0NzzDd/YrGsdgTRFKk8E4mr3uSy','叶秋永','1881655517','yeqiuyong@aliyun.com',1,'','127.0.0.1',1454660363,1454660363,'Y');

--
-- Table structure for table 'admin_profiles'
--
DROP TABLE IF EXISTS `linkage_admin_profile`;
CREATE TABLE `linkage_admin_profile` (
  `profile_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `profile_name` varchar(255) NOT NULL default '',
  PRIMARY KEY (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
insert into linkage_admin_profile ( `profile_name`) values ('超级管理员');
insert into linkage_admin_profile ( `profile_name`) values ('管理员');

--
-- Table structure for table 'admin_pages_to_profiles'
--
DROP TABLE IF EXISTS `linkage_admin_page_to_profile`;
CREATE TABLE `linkage_admin_page_to_profile` (
  `profile_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_key` varchar(255) NOT NULL default '',
  UNIQUE KEY profile_page (profile_id, page_key),
  UNIQUE KEY page_profile (page_key, profile_id)
) ENGINE=InnoDB;

--
-- Table structure for table `admin_activity_log`
--
DROP TABLE IF EXISTS `linkage_admin_activity_log`;
CREATE TABLE `linkage_admin_activity_log` (
  `log_id` bigint(15) NOT NULL auto_increment,
  `access_date` int(11) NOT NULL DEFAULT 0,
  `admin_id` int(11) NOT NULL default '0',
  `page_accessed` varchar(80) NOT NULL default '',
  `page_parameters` text,
  `ip_address` varchar(45) NOT NULL default '',
  `flagged` tinyint NOT NULL default '0',
  `attention` varchar(255) NOT NULL default '',
  `gzpost` mediumblob,
  PRIMARY KEY  (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table 'admin_menu'
--
DROP TABLE IF EXISTS `linkage_admin_menu`;
CREATE TABLE `linkage_admin_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_key` VARCHAR(255) NOT NULL DEFAULT '',
  `language_key` VARCHAR(255) NOT NULL DEFAULT '',
  `main_page` varchar(255) NOT NULL default '',
  `page_params` varchar(255) NOT NULL default '',
  `menu_key` varchar(255) NOT NULL default '',
  `display_on_menu` char(1) NOT NULL default 'N',
  `sort_order` int(11) NOT NULL default 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `clientuser`
--
DROP TABLE IF EXISTS `linkage_clientuser`;
CREATE TABLE `linkage_clientuser` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` char(64) NOT NULL DEFAULT '',
  `token` char(64) NOT NULL DEFAULT '',
  `name` varchar(120) DEFAULT NULL,
  `mobile` varchar(30) NOT NULL DEFAULT '',
  `gender` char(2) DEFAULT 'M',
  `email` varchar(70) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `birthday` DATE DEFAULT NULL,
  `icon` varchar(200) DEFAULT NULL,
  `identity_id` char(16) DEFAULT NULL,
  `company_id` int(11) unsigned DEFAULT NULL ,
  `loginip` varchar(31) NOT NULL DEFAULT '127.0.0.1',
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `active` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `role`
--
DROP TABLE IF EXISTS `linkage_role`;
CREATE TABLE `linkage_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rolename` varchar(32) NOT NULL DEFAULT '',
  `memo` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `permission`
--
DROP TABLE IF EXISTS `linkage_permission`;
CREATE TABLE `linkage_permission` (
  `permission_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `permissionname` varchar(32) NOT NULL DEFAULT '',
  `memo` varchar(128) NOT NULL DEFAULT '',
  `buttons` varchar(50) DEFAULT NULL,
  `checked` int(11) DEFAULT NULL,
  `expanded` int(11) NOT NULL DEFAULT '0',
  `icon_cls` varchar(20) DEFAULT NULL,
  `leaf` int(11) NOT NULL DEFAULT '0',
  `menu_code` varchar(50) NOT NULL,
  `menu_config` varchar(200) DEFAULT NULL,
  `menu_name` varchar(50) NOT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `sort_order` int(11) NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- Table structure for table `user_role`
--
DROP TABLE IF EXISTS `linkage_user_role`;
CREATE TABLE `linkage_user_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=561 DEFAULT CHARSET=utf8;

--
-- Table structure for table `role_permission`
--
DROP TABLE IF EXISTS `linkage_role_permission`;
CREATE TABLE `linkage_role_permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=561 DEFAULT CHARSET=utf8;


--
-- Table structure for table `company`
--
DROP TABLE IF EXISTS `linkage_company`;
CREATE TABLE `linkage_company` (
  `company_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(70)  NOT NULL DEFAULT '',
  `telephone` varchar(30)  NOT NULL DEFAULT '',
  `address` varchar(40)  NOT NULL DEFAULT '',
  `city` varchar(40)  DEFAULT NULL,
  `memo` varchar(128) DEFAULT NULL,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `contact`
--
DROP TABLE IF EXISTS `linkage_contact`;
CREATE TABLE `linkage_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(70)  NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `email` varchar(70)  NOT NULL,
  `comments` text  NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-04-10 20:53:38
