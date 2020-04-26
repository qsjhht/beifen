/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : new

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-08-27 14:03:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for qs_dept
-- ----------------------------
DROP TABLE IF EXISTS `qs_dept`;
CREATE TABLE `qs_dept` (
  `dept_id` int(5) NOT NULL AUTO_INCREMENT,
  `dept_pid` int(5) DEFAULT NULL,
  `dept_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `function` varchar(255) DEFAULT NULL COMMENT '部门职能',
  `detail` tinytext,
  `remarks` varchar(255) DEFAULT NULL COMMENT '备注',
  `dept_level` tinyint(1) DEFAULT NULL,
  `dept_path` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`dept_id`)
) ENGINE=MyISAM AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of qs_dept
-- ----------------------------
INSERT INTO `qs_dept` VALUES ('101', '0', '安防部', '大厅一层 103', '安防的', '搞安防的asf', 'asdf', '0', '101');
INSERT INTO `qs_dept` VALUES ('102', '0', '消防部', '大厅二层 206', 'adsgasfdsadfsda', '搞消防的', '', '0', '102');
INSERT INTO `qs_dept` VALUES ('103', '0', '自控部', '大厅一层 110', 'fddddddddddd', '搞自控的', '', '0', '103');
INSERT INTO `qs_dept` VALUES ('104', '0', '巡查部', '大厅二层 201', 'adfgfdag', '巡查巡检的', '', '0', '104');
INSERT INTO `qs_dept` VALUES ('105', '0', '设备部', '大厅一层 115', 'adsfgafgagafd', '管理设备的', '', '0', '105');
INSERT INTO `qs_dept` VALUES ('106', '0', '档案部', '大厅二层 210', 'afddddddd', '管理档案的', '', '0', '106');
INSERT INTO `qs_dept` VALUES ('107', '0', '入廊企业部', '大厅二层 201', 'aaaaaaaaaaaaa', '入廊企业管理的', '', '0', '107');
INSERT INTO `qs_dept` VALUES ('108', '0', '人员管理部', '大厅一层 102', 'tsghfnherttr', '人员管理的', '', '0', '108');
INSERT INTO `qs_dept` VALUES ('118', '104', '修水管', '啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊', null, '啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊', '啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊啊', '0', '118');
