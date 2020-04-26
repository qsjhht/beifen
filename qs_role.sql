/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : new

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-08-27 14:04:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for qs_role
-- ----------------------------
DROP TABLE IF EXISTS `qs_role`;
CREATE TABLE `qs_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(32) DEFAULT NULL,
  `role_auth_ac` text,
  `role_auth_ids` varchar(255) DEFAULT NULL,
  `role_auth_names` varchar(255) DEFAULT NULL,
  `role_detail` tinytext,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of qs_role
-- ----------------------------
INSERT INTO `qs_role` VALUES ('8', 'aaaa', 'Visual-index,Visual-bim,Enterprise-index,Eqpt-ledger,Patrol-live,Patrol-setting,Patrol-manage,Patrol-log', '101,102,110,117,118,119,120,106,', '大数据,bim,设备台账,巡查巡检实况,路线规划,任务管理,任务日志,教育培训,', 'vvv');
INSERT INTO `qs_role` VALUES ('10', '圣达菲', 'Eqpt-ledger,Eqpt-cancel,Eqpt-disassemble,Service-index,Replace-index,Eqpt-plan,Eqpt-forms,Video-liveplay,Video-replay', '110,111,112,113,114,115,116,124,125,', '设备台账,销账处理,设备拆解,设备维修,设备更换,维护计划,维护报表,实时视频,视频回放,', '打算范德萨发');
INSERT INTO `qs_role` VALUES ('9', 'asd', 'Eqpt-ledger,Eqpt-cancel,Eqpt-forms', '110,111,116,', '设备台账,销账处理,维护报表,', 'asdasd');
