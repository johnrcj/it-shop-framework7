/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : conpang

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2021-12-14 01:04:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_admin
-- ----------------------------
DROP TABLE IF EXISTS `t_admin`;
CREATE TABLE `t_admin` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `user_id` varchar(50) DEFAULT NULL COMMENT 'User ID',
  `password` varchar(100) DEFAULT NULL COMMENT 'Pasword of User ID',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_alarm
-- ----------------------------
DROP TABLE IF EXISTS `t_alarm`;
CREATE TABLE `t_alarm` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID of t_user table',
  `content` varchar(100) DEFAULT NULL COMMENT 'content of alarm',
  `type` int(2) DEFAULT NULL COMMENT 'type of alarm',
  `new_flag` int(1) DEFAULT NULL,
  `reg_time` datetime DEFAULT NULL COMMENT 'Registered time',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_app
-- ----------------------------
DROP TABLE IF EXISTS `t_app`;
CREATE TABLE `t_app` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `field_name` varchar(50) DEFAULT NULL,
  `field_value` text,
  `reg_time` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_bank
-- ----------------------------
DROP TABLE IF EXISTS `t_bank`;
CREATE TABLE `t_bank` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_category
-- ----------------------------
DROP TABLE IF EXISTS `t_category`;
CREATE TABLE `t_category` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `title` varchar(50) DEFAULT NULL COMMENT 'Title of Category',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_notice
-- ----------------------------
DROP TABLE IF EXISTS `t_notice`;
CREATE TABLE `t_notice` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `title` varchar(200) DEFAULT NULL COMMENT 'Title of notice',
  `content` text COMMENT 'Content of notice',
  `image` varchar(255) DEFAULT NULL COMMENT 'Attended image of notice',
  `reg_time` datetime DEFAULT NULL COMMENT 'Registered time',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_qna
-- ----------------------------
DROP TABLE IF EXISTS `t_qna`;
CREATE TABLE `t_qna` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `user_id` int(11) DEFAULT NULL COMMENT 'Uid of t_user table asked',
  `title` varchar(100) DEFAULT NULL COMMENT 'Asked title',
  `content` text COMMENT 'Asked content',
  `answer_content` text COMMENT 'Answered content',
  `images` text COMMENT 'Images that user has attached. Max count is 5, seperate by '';''',
  `answer_image` varchar(255) DEFAULT NULL COMMENT 'Images that manager has attached.',
  `reg_time` datetime DEFAULT NULL COMMENT 'Registered time',
  `answer_time` datetime DEFAULT NULL COMMENT 'Answered time',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_refund
-- ----------------------------
DROP TABLE IF EXISTS `t_refund`;
CREATE TABLE `t_refund` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `voucher_id` int(11) DEFAULT NULL COMMENT 'Uid of t_voucher table',
  `bank_id` int(11) DEFAULT NULL COMMENT 'Bank name',
  `account_number` varchar(100) DEFAULT NULL COMMENT 'Account name',
  `payment` varchar(100) DEFAULT NULL COMMENT 'payment: credit card or kk pay',
  `approval` tinyint(1) DEFAULT NULL COMMENT 'Flag of approval',
  `reg_time` datetime DEFAULT NULL COMMENT 'Registered time',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_searchkey
-- ----------------------------
DROP TABLE IF EXISTS `t_searchkey`;
CREATE TABLE `t_searchkey` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Uinqu ID',
  `keyword` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reg_time` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_terms
-- ----------------------------
DROP TABLE IF EXISTS `t_terms`;
CREATE TABLE `t_terms` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `title` varchar(200) DEFAULT NULL COMMENT 'Title of terms',
  `content` text COMMENT 'Content of terms',
  `kind` int(2) DEFAULT NULL,
  `reg_time` datetime DEFAULT NULL COMMENT 'Registered time',
  `mod_time` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_user
-- ----------------------------
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email address',
  `kk_email` varchar(100) DEFAULT NULL COMMENT 'email address of kakao',
  `apple_email` varchar(100) DEFAULT NULL COMMENT 'Apple email address',
  `name` varchar(100) DEFAULT NULL COMMENT 'User name',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Handphone',
  `password` varchar(100) DEFAULT NULL COMMENT 'Password of user',
  `user_type` tinyint(2) DEFAULT NULL COMMENT 'Type of user',
  `dev_type` tinyint(2) DEFAULT NULL COMMENT 'Device type, 1: android, 2: iOS',
  `status` int(2) DEFAULT NULL COMMENT 'Status of user, 0: normal, -1: withdraw, -2: deleted',
  `voucher_alarm` int(1) unsigned zerofill DEFAULT NULL COMMENT 'Flag of event receiving',
  `voucher_alarm_time` varchar(20) DEFAULT NULL COMMENT 'Voucher alarm time',
  `register_auto` int(1) DEFAULT NULL COMMENT 'flag to automatic recognition',
  `withdraw_time` datetime DEFAULT NULL COMMENT 'Withdrew time',
  `fcm_token` varchar(200) DEFAULT NULL COMMENT 'Token',
  `reg_time` datetime DEFAULT NULL COMMENT 'Registered time',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_voucher
-- ----------------------------
DROP TABLE IF EXISTS `t_voucher`;
CREATE TABLE `t_voucher` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `user_id` int(11) DEFAULT NULL COMMENT 'Uid of t_user table',
  `barcode` varchar(100) DEFAULT NULL COMMENT 'Barcode string',
  `name` varchar(100) DEFAULT NULL COMMENT 'Name of voucher',
  `image` varchar(255) DEFAULT NULL COMMENT 'image of voucher captured',
  `category_id` int(11) DEFAULT NULL COMMENT 'Uid of t_category table',
  `expire_date` date DEFAULT NULL COMMENT 'Expire time of voucher',
  `where_use` varchar(100) DEFAULT NULL COMMENT 'Content of where use',
  `memo` varchar(100) DEFAULT NULL COMMENT 'Memo content',
  `price` decimal(11,0) DEFAULT NULL COMMENT 'Price of voucher',
  `use_end` tinyint(1) DEFAULT NULL,
  `refund` tinyint(1) DEFAULT NULL COMMENT 'Flag if it refunded set 1 else 0',
  `reg_time` datetime DEFAULT NULL COMMENT 'Registered time',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_warning
-- ----------------------------
DROP TABLE IF EXISTS `t_warning`;
CREATE TABLE `t_warning` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique ID',
  `content` text COMMENT 'Content of warning',
  `kind` int(2) DEFAULT NULL COMMENT 'kind of warning',
  `reg_time` datetime DEFAULT NULL COMMENT 'Registered time',
  `mod_time` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
