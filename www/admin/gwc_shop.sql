/*
Navicat MySQL Data Transfer

Source Server         : wuzhilu
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : gwc_shop

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-12-19 13:33:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for back_base_department
-- ----------------------------
DROP TABLE IF EXISTS `back_base_department`;
CREATE TABLE `back_base_department` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `departmentName` varchar(100) NOT NULL COMMENT '部门名称',
  `description` varchar(200) DEFAULT NULL COMMENT '部门描述',
  `createTime` datetime NOT NULL,
  `createUserId` bigint(20) NOT NULL,
  `updateTime` datetime NOT NULL,
  `updateUserId` bigint(20) NOT NULL,
  `version` bigint(20) NOT NULL DEFAULT '1' COMMENT '版本号,从1开始递增',
  `enabled` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用（逻辑状态）  0：禁用，1启用',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '是否删除（物理状态）  0：已删除，1：未删除',
  PRIMARY KEY (`id`),
  KEY `normal_index` (`departmentName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of back_base_department
-- ----------------------------

-- ----------------------------
-- Table structure for back_base_menu
-- ----------------------------
DROP TABLE IF EXISTS `back_base_menu`;
CREATE TABLE `back_base_menu` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parentId` bigint(20) NOT NULL DEFAULT '0' COMMENT '父菜单,无父菜单默认为0',
  `menuName` varchar(50) NOT NULL COMMENT '菜单名称',
  `linkUrl` varchar(100) DEFAULT NULL COMMENT '菜单链接',
  `description` varchar(500) DEFAULT NULL COMMENT '菜单描述',
  `createTime` datetime NOT NULL,
  `createUserId` bigint(20) NOT NULL,
  `updateTime` datetime NOT NULL,
  `updateUserId` bigint(20) NOT NULL,
  `version` bigint(20) NOT NULL DEFAULT '1' COMMENT '版本号,从1开始递增',
  `enabled` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用（逻辑状态）  0：禁用，1启用',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '是否删除（物理状态）  0：已删除，1：未删除',
  PRIMARY KEY (`id`),
  KEY `normal_index` (`menuName`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of back_base_menu
-- ----------------------------

-- ----------------------------
-- Table structure for back_base_role
-- ----------------------------
DROP TABLE IF EXISTS `back_base_role`;
CREATE TABLE `back_base_role` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `roleName` varchar(255) DEFAULT NULL COMMENT '角色名称',
  `description` varchar(500) DEFAULT NULL COMMENT '描述',
  `createTime` datetime NOT NULL,
  `createUserId` bigint(20) NOT NULL,
  `updateTime` datetime NOT NULL,
  `updateUserId` bigint(20) NOT NULL,
  `version` bigint(20) NOT NULL DEFAULT '1' COMMENT '版本号,从1开始递增',
  `enabled` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用（逻辑状态）  0：禁用，1启用',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '是否删除（物理状态）  0：已删除，1：未删除',
  PRIMARY KEY (`id`),
  KEY `normal_index` (`roleName`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of back_base_role
-- ----------------------------

-- ----------------------------
-- Table structure for back_base_role_auth
-- ----------------------------
DROP TABLE IF EXISTS `back_base_role_auth`;
CREATE TABLE `back_base_role_auth` (
  `roleId` bigint(20) NOT NULL,
  `menuId` bigint(20) NOT NULL,
  `createTime` datetime NOT NULL,
  `createUserId` bigint(20) NOT NULL,
  `updateTime` datetime NOT NULL,
  `updateUserId` bigint(20) NOT NULL,
  `version` bigint(20) NOT NULL DEFAULT '1' COMMENT '版本号,从1开始递增',
  `enabled` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用（逻辑状态）  0：禁用，1启用',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '是否删除（物理状态）  0：已删除，1：未删除',
  PRIMARY KEY (`roleId`,`menuId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of back_base_role_auth
-- ----------------------------

-- ----------------------------
-- Table structure for back_base_user
-- ----------------------------
DROP TABLE IF EXISTS `back_base_user`;
CREATE TABLE `back_base_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userName` char(25) NOT NULL COMMENT '用户名',
  `password` varchar(100) NOT NULL COMMENT '登录密码',
  `realName` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `age` int(2) DEFAULT NULL COMMENT '年龄',
  `sex` int(1) NOT NULL DEFAULT '1' COMMENT '性别 0：女，1：男',
  `idCard` varchar(100) DEFAULT NULL COMMENT '身份证号',
  `phone` varchar(13) NOT NULL COMMENT '电话号码',
  `email` varchar(50) DEFAULT NULL COMMENT '电子邮件',
  `address` varchar(255) DEFAULT NULL COMMENT '住址',
  `workNumber` varchar(100) DEFAULT NULL COMMENT '工号',
  `departmentId` bigint(20) NOT NULL COMMENT '所属部门ID',
  `remark` text COMMENT '备注',
  `createTime` datetime NOT NULL,
  `createUserId` bigint(20) NOT NULL,
  `updateTime` datetime NOT NULL,
  `updateUserId` bigint(20) NOT NULL,
  `version` bigint(20) NOT NULL DEFAULT '1' COMMENT '版本号,从1开始递增',
  `enabled` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用（逻辑状态）  0：禁用，1启用',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '是否删除（物理状态）  0：已删除，1：未删除',
  PRIMARY KEY (`id`),
  KEY `normal_un_index` (`userName`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of back_base_user
-- ----------------------------

-- ----------------------------
-- Table structure for back_base_user_role
-- ----------------------------
DROP TABLE IF EXISTS `back_base_user_role`;
CREATE TABLE `back_base_user_role` (
  `userId` bigint(20) NOT NULL,
  `roleId` bigint(20) NOT NULL,
  `createTime` datetime NOT NULL,
  `createUserId` bigint(20) NOT NULL,
  `updateTime` datetime NOT NULL,
  `updateUserId` bigint(20) NOT NULL,
  `version` bigint(20) NOT NULL DEFAULT '1' COMMENT '版本号,从1开始递增',
  `enabled` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用（逻辑状态）  0：禁用，1启用',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '是否删除（物理状态）  0：已删除，1：未删除',
  PRIMARY KEY (`userId`,`roleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of back_base_user_role
-- ----------------------------

-- ----------------------------
-- Table structure for tp_account_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_account_log`;
CREATE TABLE `tp_account_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '账户变动log',
  `user_id` mediumint(8) unsigned NOT NULL COMMENT '用户id',
  `user_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户余额',
  `frozen_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额',
  `change_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '资金变动数（+ -）',
  `pay_points` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '积分变动（+ -）',
  `change_time` int(10) unsigned NOT NULL COMMENT '变动时间',
  `desc` varchar(255) DEFAULT '' COMMENT '描述',
  `order_sn` varchar(50) DEFAULT NULL COMMENT '订单编号',
  `order_id` int(10) DEFAULT NULL COMMENT '订单id',
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_account_log
-- ----------------------------
INSERT INTO `tp_account_log` VALUES ('1', '1', '451212.00', '121.00', '-122.00', '0.00', '1510395112', '余额减少', null, '1');
INSERT INTO `tp_account_log` VALUES ('2', '1', '451212.00', '121.00', '0.00', '-20.00', '1510395112', '积分减少', null, '1');

-- ----------------------------
-- Table structure for tp_ad
-- ----------------------------
DROP TABLE IF EXISTS `tp_ad`;
CREATE TABLE `tp_ad` (
  `ad_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '广告位置ID',
  `media_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '广告类型',
  `ad_name` varchar(60) NOT NULL DEFAULT '' COMMENT '广告名称',
  `ad_link` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `ad_code` text NOT NULL COMMENT '图片地址',
  `start_time` int(11) NOT NULL DEFAULT '0' COMMENT '投放时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `link_man` varchar(60) NOT NULL DEFAULT '' COMMENT '添加人',
  `link_email` varchar(60) NOT NULL DEFAULT '' COMMENT '添加人邮箱',
  `link_phone` varchar(60) NOT NULL DEFAULT '' COMMENT '添加人联系电话',
  `click_count` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '点击量',
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `orderby` smallint(6) DEFAULT '50' COMMENT '排序',
  `target` tinyint(1) DEFAULT '0' COMMENT '是否开启浏览器新窗口',
  `bgcolor` varchar(20) DEFAULT NULL COMMENT '背景颜色',
  PRIMARY KEY (`ad_id`),
  KEY `enabled` (`enabled`),
  KEY `position_id` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_ad
-- ----------------------------

-- ----------------------------
-- Table structure for tp_admin
-- ----------------------------
DROP TABLE IF EXISTS `tp_admin`;
CREATE TABLE `tp_admin` (
  `admin_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_name` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT 'email',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `ec_salt` varchar(10) DEFAULT NULL COMMENT '秘钥',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `last_login` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `nav_list` text NOT NULL COMMENT '权限',
  `lang_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'lang_type',
  `agency_id` smallint(5) unsigned NOT NULL COMMENT 'agency_id',
  `suppliers_id` smallint(5) unsigned DEFAULT '0' COMMENT 'suppliers_id',
  `todolist` longtext COMMENT 'todolist',
  `role_id` smallint(5) DEFAULT NULL COMMENT '角色id',
  PRIMARY KEY (`admin_id`),
  KEY `user_name` (`user_name`) USING BTREE,
  KEY `agency_id` (`agency_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_admin
-- ----------------------------
INSERT INTO `tp_admin` VALUES ('1', 'admin', 'ericssonon@163.com', '519475228fe35ad067744465c42a19b2', null, '1481596643', '1513661466', '127.0.0.1', '', '', '0', '0', null, '1');
INSERT INTO `tp_admin` VALUES ('2', 'bjgonghuo1', 'bj@163.com', '519475228fe35ad067744465c42a19b2', null, '1245044099', '0', '', '商品区列表|goods.php?act=list,订单列表|order.php?act=list,用户评论|comment_manage.php?act=list,会员列表|users.php?act=list,商店设置|shop_config.php?act=list_edit', '', '0', '1', '', '2');
INSERT INTO `tp_admin` VALUES ('3', 'shhaigonghuo1', 'shanghai@163.com', '519475228fe35ad067744465c42a19b2', null, '1245044202', '0', '', '商品区列表|goods.php?act=list,订单列表|order.php?act=list,用户评论|comment_manage.php?act=list,会员列表|users.php?act=list,商店设置|shop_config.php?act=list_edit', '', '0', '2', '', '2');
INSERT INTO `tp_admin` VALUES ('4', 'wyp001', 'wyp001@126.com', '519475228fe35ad067744465c42a19b2', null, '1456542538', '0', '', '', '', '0', '0', null, '2');
INSERT INTO `tp_admin` VALUES ('5', 'dengyunrui', 'dengyunrui@qq.com', '519475228fe35ad067744465c42a19b2', null, '1472610878', '1473055070', '183.14.137.252', '', '', '0', '0', null, '2');

-- ----------------------------
-- Table structure for tp_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_admin_log`;
CREATE TABLE `tp_admin_log` (
  `log_id` bigint(16) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `admin_id` int(10) DEFAULT NULL COMMENT '管理员id',
  `log_info` varchar(255) DEFAULT NULL COMMENT '日志描述',
  `log_ip` varchar(30) DEFAULT NULL COMMENT 'ip地址',
  `log_url` varchar(50) DEFAULT NULL COMMENT 'url',
  `log_time` int(10) DEFAULT NULL COMMENT '日志时间',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_admin_log
-- ----------------------------
INSERT INTO `tp_admin_log` VALUES ('1', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1511850732');
INSERT INTO `tp_admin_log` VALUES ('2', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1511917602');
INSERT INTO `tp_admin_log` VALUES ('3', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512003952');
INSERT INTO `tp_admin_log` VALUES ('4', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512009245');
INSERT INTO `tp_admin_log` VALUES ('5', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512090850');
INSERT INTO `tp_admin_log` VALUES ('6', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512119022');
INSERT INTO `tp_admin_log` VALUES ('7', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512176684');
INSERT INTO `tp_admin_log` VALUES ('8', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512443861');
INSERT INTO `tp_admin_log` VALUES ('9', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512521876');
INSERT INTO `tp_admin_log` VALUES ('10', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512528111');
INSERT INTO `tp_admin_log` VALUES ('11', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512608300');
INSERT INTO `tp_admin_log` VALUES ('12', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512627384');
INSERT INTO `tp_admin_log` VALUES ('13', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512696408');
INSERT INTO `tp_admin_log` VALUES ('14', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1512781196');
INSERT INTO `tp_admin_log` VALUES ('15', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513040507');
INSERT INTO `tp_admin_log` VALUES ('16', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513071465');
INSERT INTO `tp_admin_log` VALUES ('17', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513127532');
INSERT INTO `tp_admin_log` VALUES ('18', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513137143');
INSERT INTO `tp_admin_log` VALUES ('19', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513151054');
INSERT INTO `tp_admin_log` VALUES ('20', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513213725');
INSERT INTO `tp_admin_log` VALUES ('21', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513220117');
INSERT INTO `tp_admin_log` VALUES ('22', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513238223');
INSERT INTO `tp_admin_log` VALUES ('23', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513299615');
INSERT INTO `tp_admin_log` VALUES ('24', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513387423');
INSERT INTO `tp_admin_log` VALUES ('25', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513389834');
INSERT INTO `tp_admin_log` VALUES ('26', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513414032');
INSERT INTO `tp_admin_log` VALUES ('27', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513648136');
INSERT INTO `tp_admin_log` VALUES ('28', '1', '后台登录', '127.0.0.1', '/index.php/Admin/Admin/login', '1513661466');

-- ----------------------------
-- Table structure for tp_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `tp_admin_role`;
CREATE TABLE `tp_admin_role` (
  `role_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(30) DEFAULT NULL COMMENT '角色名称',
  `act_list` text COMMENT '权限列表',
  `role_desc` varchar(255) DEFAULT NULL COMMENT '角色描述',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_admin_role
-- ----------------------------
INSERT INTO `tp_admin_role` VALUES ('1', '超级管理员', 'all', '管理全站');
INSERT INTO `tp_admin_role` VALUES ('2', '编辑', '1,2,3,4,22,23,48,52,31,45,49,61,18,19,20,21,50,24,25,26,41,53,27,28,29,30,32,33,34,35,46,47', '违法接口');
INSERT INTO `tp_admin_role` VALUES ('4', '客服', '', '客服处理订单发货');

-- ----------------------------
-- Table structure for tp_ad_position
-- ----------------------------
DROP TABLE IF EXISTS `tp_ad_position`;
CREATE TABLE `tp_ad_position` (
  `position_id` int(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `position_name` varchar(60) NOT NULL DEFAULT '' COMMENT '广告位置名称',
  `ad_width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '广告位宽度',
  `ad_height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '广告位高度',
  `position_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '广告描述',
  `position_style` text NOT NULL COMMENT '模板',
  `is_open` tinyint(1) DEFAULT '0' COMMENT '0关闭1开启',
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=310 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_ad_position
-- ----------------------------
INSERT INTO `tp_ad_position` VALUES ('1', 'Index页面自动增加广告位 1 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('2', 'Index页面自动增加广告位 2 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('3', 'Index页面自动增加广告位 3 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('4', 'Index页面自动增加广告位 4 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('7', 'Index页面自动增加广告位 7 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('8', 'Index页面自动增加广告位 8 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('300', 'Index页面自动增加广告位 300 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('301', 'Index页面自动增加广告位 301 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('302', 'Index页面自动增加广告位 302 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('303', 'Index页面自动增加广告位 303 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('304', 'Index页面自动增加广告位 304 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('305', 'Index页面自动增加广告位 305 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('306', 'Index页面自动增加广告位 306 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('307', 'Index页面自动增加广告位 307 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('308', 'Index页面自动增加广告位 308 ', '0', '0', 'Index页面', '', '1');
INSERT INTO `tp_ad_position` VALUES ('309', 'Index页面自动增加广告位 309 ', '0', '0', 'Index页面', '', '1');

-- ----------------------------
-- Table structure for tp_area_region
-- ----------------------------
DROP TABLE IF EXISTS `tp_area_region`;
CREATE TABLE `tp_area_region` (
  `shipping_area_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '物流配置id',
  `region_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '地区id对应region表id',
  PRIMARY KEY (`shipping_area_id`,`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_area_region
-- ----------------------------

-- ----------------------------
-- Table structure for tp_article
-- ----------------------------
DROP TABLE IF EXISTS `tp_article`;
CREATE TABLE `tp_article` (
  `article_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `cat_id` smallint(5) NOT NULL DEFAULT '0' COMMENT '类别ID',
  `title` varchar(150) NOT NULL DEFAULT '' COMMENT '文章标题',
  `content` longtext NOT NULL COMMENT '文章内容',
  `author` varchar(30) NOT NULL DEFAULT '古玩头条' COMMENT '文章作者',
  `author_email` varchar(60) NOT NULL DEFAULT '' COMMENT '作者邮箱',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '关键字',
  `article_type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '文章类型',
  `is_open` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件地址',
  `open_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'open_type',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `description` mediumtext COMMENT '文章摘要',
  `click` int(11) DEFAULT '0' COMMENT '浏览量',
  `publish_time` int(11) DEFAULT '0' COMMENT '文章发布时间',
  `thumb` varchar(255) DEFAULT '' COMMENT '文章缩略图',
  `url` varchar(255) NOT NULL,
  `forward` int(6) unsigned NOT NULL DEFAULT '0' COMMENT '转发次数',
  PRIMARY KEY (`article_id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of tp_article
-- ----------------------------
INSERT INTO `tp_article` VALUES ('1', '4', '特大新闻头条', '&lt;p&gt;特大新闻头条特大新闻头条特大新闻头条特大新闻头条特大新闻头条特大新闻头条特大新闻头条特大新闻头条特大新闻头条特大新闻头条特大新闻头条特大新闻头条&lt;/p&gt;', '古玩头条', '', '头条', '2', '1', '1510107207', '', '0', '', '头条头条', '30000', '1510156800', '/Public/upload/article/2017/11-08/5a026830d92e9.jpg', '', '30');
INSERT INTO `tp_article` VALUES ('2', '6', '2017双十一促销 抢购', '&lt;p&gt;2017双十一促销 抢购2017双十一促销 抢购2017双十一促销 抢购2017双十一促销 抢购2017双十一促销 抢购&lt;/p&gt;', '古玩头条', '', '促销 抢购', '2', '1', '1510384499', '', '0', '', '2017双十一促销 抢购', '1286', '1510416000', '/Public/upload/article/2017/11-11/5a06a353097ab.jpg', '', '35');
INSERT INTO `tp_article` VALUES ('3', '1', 'banner图', '&lt;p&gt;banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图&lt;/p&gt;', '古玩头条', '', 'banner图', '2', '1', '1510384561', '', '0', '', '', '1188', '1510416000', '/Public/upload/article/2017/11-24/5a17f5fb34124.png', '', '35');
INSERT INTO `tp_article` VALUES ('4', '7', '好书法', '&lt;p&gt;好书法好书法好书法好书法&lt;/p&gt;', '古玩头条', '', '好书法', '2', '1', '1510907954', '', '0', '', '好书法好书法', '1264', '1510934400', '/Public/upload/article/2017/11-17/5a0ea0231a999.jpg', '', '49');
INSERT INTO `tp_article` VALUES ('5', '6', '古玩名画', '&lt;p&gt;古玩名画古玩名画古玩名画&lt;/p&gt;', '古玩头条', '', '古玩名画', '2', '1', '1510908027', '', '0', '', '古玩名画', '1188', '1510934400', '/Public/upload/article/2017/11-17/5a0ea0722ad74.jpg', '', '25');
INSERT INTO `tp_article` VALUES ('6', '2', '公告信息', '&lt;p&gt;公告信息公告信息公告信息公告信息公告信息公告信息公告信息公告信息公告信息&lt;/p&gt;', '古玩头条', '', '公告信息1111111111111111111111', '2', '1', '1511521372', '', '0', '公告信息', '公告信息公告信息公告信息公告信息', '1019', '1511539200', '', '', '34');
INSERT INTO `tp_article` VALUES ('7', '1', 'banner图', '&lt;p&gt;banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图&lt;/p&gt;', '古玩头条', '', 'banner图', '2', '1', '1510384561', '', '0', '', '', '1188', '1510416000', '/Public/upload/article/2017/11-24/5a17f5fb34124.png', '', '35');
INSERT INTO `tp_article` VALUES ('8', '1', 'banner图', '&lt;p&gt;banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图banner图&lt;/p&gt;', '古玩头条', '', 'banner图', '2', '1', '1510384561', '', '0', '', '', '1188', '1510416000', '/Public/upload/article/2017/11-24/5a17f5fb34124.png', '', '35');

-- ----------------------------
-- Table structure for tp_article_cat
-- ----------------------------
DROP TABLE IF EXISTS `tp_article_cat`;
CREATE TABLE `tp_article_cat` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `cat_name` varchar(20) DEFAULT NULL COMMENT '类别名称',
  `cat_type` smallint(6) DEFAULT '0' COMMENT '系统分组',
  `parent_id` smallint(6) DEFAULT NULL COMMENT '夫级ID',
  `show_in_nav` tinyint(1) DEFAULT '0' COMMENT '是否导航显示',
  `sort_order` smallint(6) DEFAULT '50' COMMENT '排序',
  `cat_desc` varchar(255) DEFAULT NULL COMMENT '分类描述',
  `keywords` varchar(30) DEFAULT NULL COMMENT '搜索关键词',
  `cat_alias` varchar(20) DEFAULT NULL COMMENT '别名',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_article_cat
-- ----------------------------
INSERT INTO `tp_article_cat` VALUES ('1', '轮播图管理', '0', '0', '1', '0', '轮播', '轮播', null);
INSERT INTO `tp_article_cat` VALUES ('2', '公告管理', '2', '0', '1', '0', '公告', '公告', null);
INSERT INTO `tp_article_cat` VALUES ('3', '头条管理', '0', '0', '1', '0', '头条', '头条', null);
INSERT INTO `tp_article_cat` VALUES ('4', '最新资讯', '0', '3', '0', '0', '最新资讯', '最新资讯', null);
INSERT INTO `tp_article_cat` VALUES ('6', '收藏故事', '0', '3', '0', '0', '收藏故事', '收藏故事', null);
INSERT INTO `tp_article_cat` VALUES ('7', '市场行情', '0', '3', '0', '0', '市场行情', '市场行情', null);
INSERT INTO `tp_article_cat` VALUES ('9', '鉴定知识', '0', '3', '0', '0', '鉴定知识', '鉴定知识', null);

-- ----------------------------
-- Table structure for tp_bid_order
-- ----------------------------
DROP TABLE IF EXISTS `tp_bid_order`;
CREATE TABLE `tp_bid_order` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `bid_price` decimal(20,9) NOT NULL,
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品id',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `upload_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作品修改时间或者上传时间',
  `is_remind` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '1已经发送',
  `remind_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '时间戳',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '1默认状态  2已付款  3已经发货 4已结收货成功 5已经评价 6订单自动取消 7 协议退款 8 协议退货 9 退货加退款 10出局了',
  `order_sn` varchar(100) DEFAULT NULL,
  `is_gernerorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1已经生成订单 ',
  PRIMARY KEY (`id`),
  KEY `USERID` (`user_id`) USING BTREE,
  KEY `goods_idanduoliadtime` (`goods_id`,`upload_time`),
  KEY `goods_idanddeletetime` (`goods_id`,`delete_time`) USING BTREE,
  KEY `delete` (`delete_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_bid_order
-- ----------------------------

-- ----------------------------
-- Table structure for tp_brand
-- ----------------------------
DROP TABLE IF EXISTS `tp_brand`;
CREATE TABLE `tp_brand` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '品牌表',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '品牌名称',
  `logo` varchar(80) NOT NULL DEFAULT '' COMMENT '品牌logo',
  `desc` text NOT NULL COMMENT '品牌描述',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '品牌地址',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `cat_name` varchar(128) DEFAULT '' COMMENT '品牌分类',
  `parent_cat_id` int(11) DEFAULT '0' COMMENT '分类id',
  `cat_id` int(10) DEFAULT '0' COMMENT '分类id',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '是否推荐',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_brand
-- ----------------------------

-- ----------------------------
-- Table structure for tp_cart
-- ----------------------------
DROP TABLE IF EXISTS `tp_cart`;
CREATE TABLE `tp_cart` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '购物车表',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `session_id` char(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'session',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品区id',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品区货号',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品区名称',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '本店价',
  `member_goods_price` decimal(10,2) DEFAULT '0.00' COMMENT '会员折扣价',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '购买数量',
  `spec_key` varchar(64) NOT NULL DEFAULT '' COMMENT '商品区规格key 对应tp_spec_goods_price 表',
  `spec_key_name` varchar(64) DEFAULT '' COMMENT '商品区规格组合名称',
  `bar_code` varchar(64) DEFAULT '' COMMENT '商品区条码',
  `selected` tinyint(1) DEFAULT '1' COMMENT '购物车选中状态',
  `add_time` int(11) DEFAULT '0' COMMENT '加入购物车的时间',
  `prom_type` tinyint(1) DEFAULT '0' COMMENT '0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠',
  `prom_id` int(11) DEFAULT '0' COMMENT '活动id',
  `sku` varchar(128) DEFAULT '' COMMENT 'sku',
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_cart
-- ----------------------------
INSERT INTO `tp_cart` VALUES ('3', '1', '', '1', '', '', '0.00', '0.00', '0.00', '1', '', '', '', '1', '1513236650', '0', '0', '');

-- ----------------------------
-- Table structure for tp_collectgoodmessagelist
-- ----------------------------
DROP TABLE IF EXISTS `tp_collectgoodmessagelist`;
CREATE TABLE `tp_collectgoodmessagelist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `start_time` int(11) unsigned NOT NULL,
  `is_send` tinyint(3) unsigned DEFAULT '0' COMMENT '1已经完成任务',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_collectgoodmessagelist
-- ----------------------------

-- ----------------------------
-- Table structure for tp_comment
-- ----------------------------
DROP TABLE IF EXISTS `tp_comment`;
CREATE TABLE `tp_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品区id',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT 'email邮箱',
  `username` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `content` text NOT NULL COMMENT '评论内容',
  `deliver_rank` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '物流评价等级',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `ip_address` varchar(15) NOT NULL DEFAULT '' COMMENT 'ip地址',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论用户',
  `img` text COMMENT '晒单图片',
  `order_id` mediumint(8) DEFAULT NULL COMMENT '订单id',
  `goods_rank` tinyint(1) DEFAULT NULL COMMENT '商品区评价等级',
  `service_rank` tinyint(1) DEFAULT NULL COMMENT '商家服务态度评价等级',
  PRIMARY KEY (`comment_id`),
  KEY `parent_id` (`parent_id`),
  KEY `id_value` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_comment
-- ----------------------------
INSERT INTO `tp_comment` VALUES ('1', '2', '1545644@qq.com', 'kuku', '哪款不能看', '0', '0', '上海', '0', '0', '0', null, null, null, null);

-- ----------------------------
-- Table structure for tp_config
-- ----------------------------
DROP TABLE IF EXISTS `tp_config`;
CREATE TABLE `tp_config` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `name` varchar(64) DEFAULT NULL COMMENT '配置的key键名',
  `value` varchar(512) DEFAULT NULL COMMENT '配置的val值',
  `inc_type` varchar(64) DEFAULT NULL COMMENT '配置分组',
  `desc` varchar(50) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_config
-- ----------------------------
INSERT INTO `tp_config` VALUES ('1', 'line_nums', '5', '', null);
INSERT INTO `tp_config` VALUES ('2', 'line_time1', '6', '', null);
INSERT INTO `tp_config` VALUES ('3', 'line_time2', '24', '', null);
INSERT INTO `tp_config` VALUES ('4', 'is_tuijian', '1', '', null);
INSERT INTO `tp_config` VALUES ('5', 'is_mark', '0', '', null);
INSERT INTO `tp_config` VALUES ('6', 'is_charge', '0', '', null);
INSERT INTO `tp_config` VALUES ('7', 'is_price', '0', '', null);
INSERT INTO `tp_config` VALUES ('8', 'is_editer', '1', '', null);
INSERT INTO `tp_config` VALUES ('9', 'record_no', '1', '', null);
INSERT INTO `tp_config` VALUES ('10', 'is_head', '0', '', null);
INSERT INTO `tp_config` VALUES ('11', 'low_price', '0', '', null);
INSERT INTO `tp_config` VALUES ('12', 'special', '0', '', null);
INSERT INTO `tp_config` VALUES ('13', 'income', '0', '', null);
INSERT INTO `tp_config` VALUES ('14', 'tixian_money', '300', '', null);
INSERT INTO `tp_config` VALUES ('15', 'period', '10', '', null);
INSERT INTO `tp_config` VALUES ('16', 'tixian_poundage', '16', '', null);
INSERT INTO `tp_config` VALUES ('17', 'tixian_style', '微信', '', null);
INSERT INTO `tp_config` VALUES ('18', 'sever_time', '1', '', null);
INSERT INTO `tp_config` VALUES ('19', 'server_type', '', '', null);
INSERT INTO `tp_config` VALUES ('20', 'line_anser', '120', '', null);
INSERT INTO `tp_config` VALUES ('21', 'is_kefu', '0', '', null);
INSERT INTO `tp_config` VALUES ('22', 'newfunction', '0', '', null);
INSERT INTO `tp_config` VALUES ('23', '__hash__', 'ce745851533d0d07d99f2e3868bd4135_db5db089c8fac647332db747feb0362b', '', null);
INSERT INTO `tp_config` VALUES ('24', 'sms_appkey', 'fegggggf', 'sms', null);
INSERT INTO `tp_config` VALUES ('25', 'sms_secretKey', 'dddddqfegghwef', 'sms', null);
INSERT INTO `tp_config` VALUES ('26', 'sms_product', '古玩城', 'sms', null);
INSERT INTO `tp_config` VALUES ('27', 'sms_templateCode', 'SMS_60930128', 'sms', null);
INSERT INTO `tp_config` VALUES ('28', 'regis_sms_enable', '1', 'sms', null);
INSERT INTO `tp_config` VALUES ('29', 'sms_time_out', '60', 'sms', null);
INSERT INTO `tp_config` VALUES ('30', 'freight_free', '200', 'shopping', null);
INSERT INTO `tp_config` VALUES ('31', 'point_rate', '100', 'shopping', null);
INSERT INTO `tp_config` VALUES ('32', 'auto_confirm_date', '5', 'shopping', null);
INSERT INTO `tp_config` VALUES ('33', 'reduce', '1', 'shopping', null);
INSERT INTO `tp_config` VALUES ('34', 'reg_integral', '10', 'basic', null);
INSERT INTO `tp_config` VALUES ('35', 'file_size', '5', 'basic', null);
INSERT INTO `tp_config` VALUES ('36', 'default_storage', '10', 'basic', null);
INSERT INTO `tp_config` VALUES ('37', 'warning_storage', '2', 'basic', null);
INSERT INTO `tp_config` VALUES ('38', 'tax', '0', 'basic', null);
INSERT INTO `tp_config` VALUES ('39', 'hot_keywords', '手机|电脑', 'basic', null);
INSERT INTO `tp_config` VALUES ('40', 'app_test', '0', 'basic', null);

-- ----------------------------
-- Table structure for tp_coupon
-- ----------------------------
DROP TABLE IF EXISTS `tp_coupon`;
CREATE TABLE `tp_coupon` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `name` varchar(50) NOT NULL COMMENT '优惠券名字',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发放类型 0面额模板1 按用户发放 2 注册 3 邀请 4 线下发放',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券金额',
  `condition` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用条件',
  `createnum` int(11) DEFAULT '0' COMMENT '发放数量',
  `send_num` int(11) DEFAULT '0' COMMENT '已领取数量',
  `use_num` int(11) DEFAULT '0' COMMENT '已使用数量',
  `send_start_time` int(11) DEFAULT NULL COMMENT '发放开始时间',
  `send_end_time` int(11) DEFAULT NULL COMMENT '发放结束时间',
  `use_start_time` int(11) DEFAULT NULL COMMENT '使用开始时间',
  `use_end_time` int(11) DEFAULT NULL COMMENT '使用结束时间',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_coupon
-- ----------------------------

-- ----------------------------
-- Table structure for tp_coupon_list
-- ----------------------------
DROP TABLE IF EXISTS `tp_coupon_list`;
CREATE TABLE `tp_coupon_list` (
  `id` int(8) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `cid` int(8) NOT NULL DEFAULT '0' COMMENT '优惠券 对应coupon表id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发放类型 1 按订单发放 2 注册 3 邀请 4 按用户发放',
  `uid` int(8) NOT NULL DEFAULT '0' COMMENT '用户id',
  `order_id` int(8) NOT NULL DEFAULT '0' COMMENT '订单id',
  `use_time` int(11) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `code` varchar(10) DEFAULT '' COMMENT '优惠券兑换码',
  `send_time` int(11) NOT NULL DEFAULT '0' COMMENT '发放时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_coupon_list
-- ----------------------------

-- ----------------------------
-- Table structure for tp_delivery_doc
-- ----------------------------
DROP TABLE IF EXISTS `tp_delivery_doc`;
CREATE TABLE `tp_delivery_doc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '发货单ID',
  `order_id` int(11) unsigned NOT NULL COMMENT '订单ID',
  `order_sn` varchar(64) NOT NULL COMMENT '订单编号',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `consignee` varchar(64) NOT NULL COMMENT '收货人',
  `zipcode` varchar(6) DEFAULT NULL COMMENT '邮编',
  `mobile` varchar(20) NOT NULL COMMENT '联系手机',
  `country` int(11) unsigned NOT NULL COMMENT '国ID',
  `province` int(11) unsigned NOT NULL COMMENT '省ID',
  `city` int(11) unsigned NOT NULL COMMENT '市ID',
  `district` int(11) unsigned NOT NULL COMMENT '区ID',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `shipping_code` varchar(32) DEFAULT NULL COMMENT '物流code',
  `shipping_name` varchar(64) DEFAULT NULL COMMENT '快递名称',
  `shipping_price` decimal(10,2) DEFAULT '0.00' COMMENT '运费',
  `invoice_no` varchar(255) NOT NULL COMMENT '物流单号',
  `tel` varchar(64) DEFAULT NULL COMMENT '座机电话',
  `note` text COMMENT '管理员添加的备注信息',
  `best_time` int(11) DEFAULT NULL COMMENT '友好收货时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='发货单';

-- ----------------------------
-- Records of tp_delivery_doc
-- ----------------------------

-- ----------------------------
-- Table structure for tp_fans_collect
-- ----------------------------
DROP TABLE IF EXISTS `tp_fans_collect`;
CREATE TABLE `tp_fans_collect` (
  `collect_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `fans_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '粉丝用户id',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`collect_id`),
  KEY `user_id` (`user_id`),
  KEY `fans_id` (`fans_id`),
  KEY `sss` (`user_id`,`fans_id`) USING BTREE,
  KEY `deletetime` (`delete_time`) USING BTREE,
  KEY `useridanddeletetime` (`user_id`,`delete_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8
/*!50100 PARTITION BY LINEAR HASH (collect_id)
PARTITIONS 5 */;

-- ----------------------------
-- Records of tp_fans_collect
-- ----------------------------
INSERT INTO `tp_fans_collect` VALUES ('136', '2', '1', '1513408368', null);
INSERT INTO `tp_fans_collect` VALUES ('137', '3', '1', '1513408763', null);
INSERT INTO `tp_fans_collect` VALUES ('2', '1', '2', '1510395112', null);
INSERT INTO `tp_fans_collect` VALUES ('138', '1', '1', '1513409088', null);

-- ----------------------------
-- Table structure for tp_feedback
-- ----------------------------
DROP TABLE IF EXISTS `tp_feedback`;
CREATE TABLE `tp_feedback` (
  `msg_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '默认自增ID',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '回复留言ID',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `msg_title` varchar(200) NOT NULL DEFAULT '' COMMENT '留言标题',
  `msg_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '留言类型 1:自定义 2：售前服务 3：售中服务 4：售后服务',
  `msg_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '处理状态',
  `msg_content` text NOT NULL COMMENT '留言内容',
  `msg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '留言时间',
  `message_img` varchar(255) NOT NULL DEFAULT '0',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0',
  `msg_area` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`msg_id`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='问题反馈表';

-- ----------------------------
-- Records of tp_feedback
-- ----------------------------
INSERT INTO `tp_feedback` VALUES ('1', '0', '1', 'jim', '问题', '2', '0', '有问题有问题有问题有问题有问题', '142244120', '0', '0', '0');
INSERT INTO `tp_feedback` VALUES ('2', '0', '2', 'any', '米努努的财务', '3', '0', '不是的而非', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for tp_file_sha1
-- ----------------------------
DROP TABLE IF EXISTS `tp_file_sha1`;
CREATE TABLE `tp_file_sha1` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sha1` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sha1` (`sha1`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_file_sha1
-- ----------------------------

-- ----------------------------
-- Table structure for tp_flash_sale
-- ----------------------------
DROP TABLE IF EXISTS `tp_flash_sale`;
CREATE TABLE `tp_flash_sale` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL COMMENT '活动标题',
  `goods_id` int(10) NOT NULL COMMENT '参团商品区ID',
  `price` float(10,2) NOT NULL COMMENT '活动价格',
  `goods_num` int(10) DEFAULT '1' COMMENT '商品区参加活动数',
  `buy_limit` int(11) NOT NULL DEFAULT '1' COMMENT '每人限购数',
  `buy_num` int(11) NOT NULL DEFAULT '0' COMMENT '已购买人数',
  `order_num` int(10) DEFAULT '0' COMMENT '已下单数',
  `description` text COMMENT '活动描述',
  `start_time` int(11) NOT NULL COMMENT '开始时间',
  `end_time` int(11) NOT NULL COMMENT '结束时间',
  `is_end` tinyint(1) DEFAULT '0' COMMENT '是否已结束',
  `goods_name` varchar(255) DEFAULT NULL COMMENT '商品区名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_flash_sale
-- ----------------------------

-- ----------------------------
-- Table structure for tp_forindex
-- ----------------------------
DROP TABLE IF EXISTS `tp_forindex`;
CREATE TABLE `tp_forindex` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) unsigned NOT NULL COMMENT '作品id',
  `datetime` int(11) unsigned NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  `is_over` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1已经执行成功',
  PRIMARY KEY (`id`),
  KEY `gid` (`goods_id`,`is_over`,`delete_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_forindex
-- ----------------------------

-- ----------------------------
-- Table structure for tp_friend_link
-- ----------------------------
DROP TABLE IF EXISTS `tp_friend_link`;
CREATE TABLE `tp_friend_link` (
  `link_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `link_name` varchar(255) NOT NULL DEFAULT '' COMMENT '链接名称',
  `link_url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `link_logo` varchar(255) NOT NULL DEFAULT '' COMMENT '链接logo',
  `orderby` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `is_show` tinyint(1) DEFAULT '1' COMMENT '是否显示',
  `target` tinyint(1) DEFAULT '1' COMMENT '是否新窗口打开',
  PRIMARY KEY (`link_id`),
  KEY `show_order` (`orderby`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_friend_link
-- ----------------------------

-- ----------------------------
-- Table structure for tp_goods
-- ----------------------------
DROP TABLE IF EXISTS `tp_goods`;
CREATE TABLE `tp_goods` (
  `goods_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品区id',
  `cat_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `extend_cat_id` int(11) DEFAULT '0' COMMENT '扩展分类id',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品区编号',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品区名称',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击数',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '品牌id',
  `store_count` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '库存数量',
  `comment_count` smallint(5) DEFAULT '0' COMMENT '商品区评论数',
  `weight` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品区重量克为单位',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `shop_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '本店价',
  `start_price` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '起拍价格',
  `cost_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品区成本价',
  `every_add_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '每次加价',
  `protect_price` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '保价',
  `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT '商品区关键词',
  `goods_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '商品区简单描述',
  `goods_content` text COMMENT '商品区详细描述',
  `original_img` varchar(255) NOT NULL DEFAULT '' COMMENT '商品区上传原始图',
  `is_real` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否为实物',
  `is_on_sale` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否上架',
  `is_spread` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否今日推广',
  `is_free_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否包邮0否1是',
  `endTime` int(11) NOT NULL DEFAULT '0' COMMENT '下架时间',
  `on_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品区上架时间',
  `sort` smallint(4) unsigned NOT NULL DEFAULT '50' COMMENT '商品区排序',
  `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `is_new` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否新品',
  `is_self` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否自营',
  `is_special_price` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否特价',
  `is_distribute` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否分销',
  `distribute_proportion` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销比例',
  `distribute_money` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销固定金额',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '是否热卖',
  `last_update` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `goods_type` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品区所属类型id，取值表goods_type的cat_id',
  `spec_type` smallint(5) DEFAULT '0' COMMENT '商品区规格类型，取值表goods_type的cat_id',
  `give_integral` mediumint(8) DEFAULT '0' COMMENT '购买商品区赠送积分',
  `exchange_integral` int(10) NOT NULL DEFAULT '0' COMMENT '积分兑换：0不参与积分兑换，积分和现金的兑换比例见后台配置',
  `suppliers_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '供货商ID',
  `sales_sum` int(11) DEFAULT '0' COMMENT '商品区销量',
  `prom_type` tinyint(1) DEFAULT '0' COMMENT '0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠',
  `prom_id` int(11) DEFAULT '0' COMMENT '优惠活动id',
  `commission` decimal(10,2) DEFAULT '0.00' COMMENT '佣金用于分销分成',
  `spu` varchar(128) DEFAULT '' COMMENT 'SPU',
  `sku` varchar(128) DEFAULT '' COMMENT 'SKU',
  `shipping_area_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '配送物流shipping_area_id,以逗号分隔',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '卖家id',
  `delete_time` int(11) DEFAULT NULL,
  `contact_mobile` varchar(20) DEFAULT NULL COMMENT '联系手机',
  `contact_wx` varchar(65) DEFAULT NULL COMMENT '联系微信',
  `reserveprice` decimal(16,6) DEFAULT NULL COMMENT '保留价格',
  `enableReturn` tinyint(1) DEFAULT '0' COMMENT '0代表不包退',
  `upload_time` int(11) NOT NULL DEFAULT '0' COMMENT '上传时间 修改时间',
  `weight_sale` int(3) NOT NULL DEFAULT '0',
  `is_assgin` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1分配1次 2就是2次',
  `is_toplatform` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0是正常买卖商品区  1是属于平台自有商品区 2平台的 且是精品3是拍卖圈的 且是平台的',
  `goods_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1商品区正常  2已被购买',
  `transaction_time` int(11) NOT NULL DEFAULT '0' COMMENT '成交时间',
  `is_remind` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1已经发送',
  `autoremindproductserver` tinyint(1) NOT NULL DEFAULT '0' COMMENT '2 已经提醒 ',
  `is_goods` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '精品 默认不是  1==》精品',
  `is_gernerorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1已经生成过订单',
  `is_heler_likeand` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'æϲ?',
  `is_upload` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '2 is modify',
  PRIMARY KEY (`goods_id`),
  KEY `goods_sn` (`goods_sn`),
  KEY `cat_id` (`cat_id`),
  KEY `last_update` (`last_update`),
  KEY `brand_id` (`brand_id`),
  KEY `goods_number` (`store_count`),
  KEY `goods_weight` (`weight`),
  KEY `sort_order` (`sort`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `endtime` (`endTime`) USING BTREE,
  KEY `goods_id` (`goods_id`) USING BTREE,
  KEY `deletetime` (`delete_time`) USING BTREE,
  KEY `uploadtime` (`upload_time`) USING BTREE,
  KEY `userendtimedaeletetime` (`endTime`,`user_id`,`delete_time`) USING BTREE,
  KEY `ot` (`on_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_goods
-- ----------------------------
INSERT INTO `tp_goods` VALUES ('7', '6', '0', '', '个梵蒂冈', '63', '0', '1', '0', '0', '0.00', '231.00', '0.00', '0.00', '0.00', '0.00', '', '', '功夫大使馆', 'upload\\goods/20171212\\21bd6b120bd4fa8cf18433bdbcc02aee.jpg', '1', '1', '1', '0', '1513360800', '0', '50', '0', '0', '0', '0', '1', '0', '0', '1', '1513326938', '0', '0', '0', '0', '0', '0', '0', '0', '0.00', '', '', '', '1', null, '15343719375', '', null, '0', '1513069280', '0', '0', '0', '2', '1513069280', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_goods` VALUES ('8', '6', '0', '', 'fsdfdsf', '56', '0', '1', '0', '0', '0.00', '100.00', '0.00', '0.00', '0.00', '0.00', '', '', 'dsfsdfs', 'upload\\goods/20171213\\4a0832bc6be5ed4f3405cd2689a852c8.jpg', '1', '1', '1', '0', '1513666432', '0', '50', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0.00', '', '', '', '2', null, '', '', null, '0', '1513145871', '0', '0', '0', '1', '1513069202', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_goods` VALUES ('9', '5', '0', '', 'fsdfasdf', '24', '0', '1', '0', '0', '0.00', '200.00', '0.00', '0.00', '0.00', '0.00', '', '', 'fsadfasf', 'upload\\goods/20171213\\07f1feb113667fb59b0b60218b3c2679.jpg', '1', '1', '1', '0', '1513626432', '0', '50', '1', '0', '1', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0.00', '', '', '', '1', null, '', '', null, '0', '1513146268', '0', '0', '0', '1', '1513069280', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_goods` VALUES ('10', '5', '0', '', '999', '12', '0', '5', '0', '0', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '', '', '88年999感冒灵', 'upload\\goods/20171213\\4e81ba7c51126b66e9d61f27f5b8edfa.png', '1', '1', '1', '0', '1513666132', '0', '50', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '200', '0', '1', '0', '0', '0.00', '', '', '', '1', null, '', '', null, '0', '1513146414', '0', '0', '0', '1', '1513069015', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_goods` VALUES ('11', '5', '0', '', '啧啧啧', '26', '0', '1', '0', '0', '0.00', '22.00', '0.00', '0.00', '0.00', '0.00', '', '', '对对对', 'upload\\goods/20171214\\74090ac2cf42f3e78b03e95a7ef4c389.jpg', '1', '1', '1', '0', '1513360800', '0', '50', '1', '0', '1', '0', '0', '20', '55', '1', '1513326958', '0', '0', '0', '0', '0', '0', '0', '0', '0.00', '', '', '', '2', null, '', '', null, '0', '1513238328', '0', '0', '0', '1', '1513069590', '0', '0', '0', '0', '0', '1');
INSERT INTO `tp_goods` VALUES ('12', '6', '0', '', 'fsadf', '4', '0', '1', '0', '0', '0.00', '1000.00', '0.00', '0.00', '0.00', '0.00', '', '', 'fsadfasdf', 'upload\\goods/20171214\\cfda559ce52d7201bca2cedac1285a83.png', '1', '1', '1', '0', '1513666432', '0', '50', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0.00', '', '', '', '2', null, '', '', null, '0', '1513245238', '0', '0', '0', '1', '1513069806', '0', '0', '0', '0', '0', '1');

-- ----------------------------
-- Table structure for tp_goods_attr
-- ----------------------------
DROP TABLE IF EXISTS `tp_goods_attr`;
CREATE TABLE `tp_goods_attr` (
  `goods_attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品区属性id自增',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品区id',
  `attr_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '属性id',
  `attr_value` text NOT NULL COMMENT '属性值',
  `attr_price` varchar(255) NOT NULL DEFAULT '' COMMENT '属性价格',
  PRIMARY KEY (`goods_attr_id`),
  KEY `goods_id` (`goods_id`),
  KEY `attr_id` (`attr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_goods_attr
-- ----------------------------

-- ----------------------------
-- Table structure for tp_goods_attribute
-- ----------------------------
DROP TABLE IF EXISTS `tp_goods_attribute`;
CREATE TABLE `tp_goods_attribute` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attr_name` varchar(60) NOT NULL DEFAULT '' COMMENT '属性名称',
  `type_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '属性分类id',
  `attr_index` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不需要检索 1关键字检索 2范围检索',
  `attr_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0唯一属性 1单选属性 2复选属性',
  `attr_input_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT ' 0 手工录入 1从列表中选择 2多行文本框',
  `attr_values` text NOT NULL COMMENT '可选值列表',
  `order` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '属性排序',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_goods_attribute
-- ----------------------------

-- ----------------------------
-- Table structure for tp_goods_category
-- ----------------------------
DROP TABLE IF EXISTS `tp_goods_category`;
CREATE TABLE `tp_goods_category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品区分类id',
  `name` varchar(90) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '商品区分类名称',
  `mobile_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '副分类名称',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '父id',
  `parent_id_path` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '家族图谱',
  `level` tinyint(1) DEFAULT '0' COMMENT '等级',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '50' COMMENT '顺序排序',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `image` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '分类图片',
  `gray_image` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '分类图片（暗）',
  `right_image` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '分类图片（亮）',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '是否推荐为热门分类',
  `cat_group` tinyint(1) DEFAULT '0' COMMENT '分类分组默认0',
  `commission_rate` tinyint(1) DEFAULT '0' COMMENT '分佣比例',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_goods_category
-- ----------------------------
INSERT INTO `tp_goods_category` VALUES ('1', '玉翠珠宝', '和田翡翠，琥珀蜜蜡，玛瑙水晶等', '0', '0_1', '1', '50', '1', '/Public/upload/category/2017/11-24/5a17f5828370e.png', '/Public/upload/category/2017/12-07/5a28f2c264c60.png', '/Public/upload/category/2017/12-07/5a28f2bd19bf9.png', '1', '0', '0');
INSERT INTO `tp_goods_category` VALUES ('2', '书画篆刻', '国画，书法，印章，印石等', '0', '0_2', '1', '50', '1', '/Public/upload/category/2017/11-24/5a17f59d90451.png', '/Public/upload/category/2017/12-07/5a28f2e457166.png', '/Public/upload/category/2017/12-07/5a28f2df01cec.png', '0', '0', '0');
INSERT INTO `tp_goods_category` VALUES ('3', '茶酒滋补', '茶，酒等', '0', '0_3', '1', '50', '1', '/Public/upload/category/2017/11-24/5a17f5ac14346.png', '/Public/upload/category/2017/12-07/5a28f2faddae0.png', '/Public/upload/category/2017/12-07/5a28f2f2ea051.png', '0', '0', '0');
INSERT INTO `tp_goods_category` VALUES ('4', '紫砂陶瓷', '瓷器，陶器，紫砂等', '0', '0_4', '1', '50', '1', '/Public/upload/category/2017/11-24/5a17f5b629d47.png', '/Public/upload/category/2017/12-07/5a28f30c3c4d0.png', '/Public/upload/category/2017/12-07/5a28f306b12ac.png', '0', '0', '0');
INSERT INTO `tp_goods_category` VALUES ('5', '工艺作品', '珠串，木雕，石雕，雕刻等', '0', '0_5', '1', '50', '1', '/Public/upload/category/2017/11-24/5a17f5c294b17.png', '/Public/upload/category/2017/12-07/5a28f32029893.png', '/Public/upload/category/2017/12-07/5a28f317d9339.png', '0', '0', '0');
INSERT INTO `tp_goods_category` VALUES ('6', '文玩杂物', '其他文玩珍品', '0', '0_6', '1', '50', '1', '/Public/upload/category/2017/11-24/5a17f5cc624af.png', '/Public/upload/category/2017/12-07/5a28f3319a92a.png', '/Public/upload/category/2017/12-07/5a28f32b1a8d5.png', '0', '0', '0');
INSERT INTO `tp_goods_category` VALUES ('7', '花鸟虫鱼', '鸟，鸟具，花鸟，猫狗，水族', '0', '0_7', '1', '50', '1', '/Public/upload/category/2017/11-24/5a17f5d85aa5b.png', '/Public/upload/category/2017/12-07/5a28f342cfcab.png', '/Public/upload/category/2017/12-07/5a28f33d7c771.png', '0', '0', '0');

-- ----------------------------
-- Table structure for tp_goods_collect
-- ----------------------------
DROP TABLE IF EXISTS `tp_goods_collect`;
CREATE TABLE `tp_goods_collect` (
  `collect_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `user_gooods_id` int(10) NOT NULL DEFAULT '0' COMMENT '改产品的用户id',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品区id',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`collect_id`),
  KEY `user_id` (`user_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50100 PARTITION BY LINEAR HASH (collect_id)
PARTITIONS 100 */;

-- ----------------------------
-- Records of tp_goods_collect
-- ----------------------------

-- ----------------------------
-- Table structure for tp_goods_consult
-- ----------------------------
DROP TABLE IF EXISTS `tp_goods_consult`;
CREATE TABLE `tp_goods_consult` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商品区咨询id',
  `goods_id` int(11) DEFAULT '0' COMMENT '商品区id',
  `username` varchar(32) CHARACTER SET utf8 DEFAULT '' COMMENT '网名',
  `add_time` int(11) DEFAULT '0' COMMENT '咨询时间',
  `consult_type` tinyint(1) DEFAULT '1' COMMENT '1 商品区咨询 2 支付咨询 3 配送 4 售后',
  `content` varchar(1024) CHARACTER SET utf8 DEFAULT '' COMMENT '咨询内容',
  `parent_id` int(11) DEFAULT '0' COMMENT '父id 用于管理员回复',
  `is_show` tinyint(1) DEFAULT '0' COMMENT '是否显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tp_goods_consult
-- ----------------------------

-- ----------------------------
-- Table structure for tp_goods_images
-- ----------------------------
DROP TABLE IF EXISTS `tp_goods_images`;
CREATE TABLE `tp_goods_images` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片id 自增',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品区id',
  `image_url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
  `delete_time` int(11) DEFAULT NULL,
  `rescourse_id` varchar(244) DEFAULT NULL,
  `enpiount` varchar(100) DEFAULT NULL,
  `image_url_remote` varchar(1000) DEFAULT NULL COMMENT '远程图片地址',
  `image_url_remote_expire` int(11) NOT NULL DEFAULT '0' COMMENT '有效期',
  `image_url_remote_nowater` varchar(1000) DEFAULT NULL COMMENT '没有水印的url',
  PRIMARY KEY (`img_id`),
  KEY `goods_id` (`goods_id`),
  KEY `deletetime` (`delete_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_goods_images
-- ----------------------------
INSERT INTO `tp_goods_images` VALUES ('16', '8', 'upload\\goods/20171213\\4a0832bc6be5ed4f3405cd2689a852c8.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('17', '9', 'upload\\goods/20171213\\07f1feb113667fb59b0b60218b3c2679.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('18', '10', 'upload\\goods/20171213\\4e81ba7c51126b66e9d61f27f5b8edfa.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('19', '10', 'upload\\goods/20171213\\52ded7c18b89b34c41c5aa1dfcb66a77.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('20', '10', 'upload\\goods/20171213\\a1f1bf98dc96156b79197b8f9b116ae3.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('21', '10', 'upload\\goods/20171213\\f8ac36078a20d8fdc28a6560ff978a3c.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('28', '12', 'upload\\goods/20171214\\cfda559ce52d7201bca2cedac1285a83.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('29', '12', 'upload\\goods/20171214\\1250b8b9c992d5b2b39f0e0f93b00e4a.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('30', '12', 'upload\\goods/20171214\\4607834fca9342943a2b4db9369d5e82.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('31', '12', 'upload\\goods/20171214\\aa1b4108b37640f36141f121f597b9b5.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('32', '12', 'upload\\goods/20171214\\5b1b7b62a2f5e143fa65078e7160319e.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('33', '12', 'upload\\goods/20171214\\5977c6ca8b8f2f840c6fe6a3b624d225.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('34', '12', 'upload\\goods/20171214\\6fd1f22f31875c6c83a3869d9905dcf0.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('35', '1', 'upload\\goods/20171212\\462b22438c58d4a8a0593428fe5a8729.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('36', '1', 'upload\\goods/20171212\\f1fd6d21f0042415bbfbf46e0f465dd0.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('37', '1', 'upload\\goods/20171212\\806822855c5e40abdea5ef500a3faebd.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('38', '1', 'upload\\goods/20171212\\2eb8bd6da730a9ad8db8f21ca1306a63.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('39', '1', 'upload\\goods/20171212\\462b22438c58d4a8a0593428fe5a8729.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('40', '1', 'upload\\goods/20171212\\f1fd6d21f0042415bbfbf46e0f465dd0.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('41', '1', 'upload\\goods/20171212\\806822855c5e40abdea5ef500a3faebd.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('42', '1', 'upload\\goods/20171212\\2eb8bd6da730a9ad8db8f21ca1306a63.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('43', '1', 'upload\\goods/20171212\\462b22438c58d4a8a0593428fe5a8729.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('44', '1', 'upload\\goods/20171212\\f1fd6d21f0042415bbfbf46e0f465dd0.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('45', '1', 'upload\\goods/20171212\\806822855c5e40abdea5ef500a3faebd.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('46', '1', 'upload\\goods/20171212\\2eb8bd6da730a9ad8db8f21ca1306a63.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('47', '1', 'upload\\goods/20171212\\f1fd6d21f0042415bbfbf46e0f465dd0.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('48', '1', 'upload\\goods/20171212\\806822855c5e40abdea5ef500a3faebd.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('49', '1', 'upload\\goods/20171215\\d892bfe2f2a0918c59f1ed87660dca66.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('50', '1', 'upload\\goods/20171215\\ae32ae4ea882824cc0f53ada6df6aba6.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('51', '1', 'upload\\goods/20171212\\f1fd6d21f0042415bbfbf46e0f465dd0.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('52', '1', 'upload\\goods/20171212\\806822855c5e40abdea5ef500a3faebd.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('53', '1', 'upload\\goods/20171215\\429091d8bed8bd6bff206237e8afeb67.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('54', '1', 'upload\\goods/20171215\\cc54c294ea120d24d08262c968439b68.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('55', '1', 'upload\\goods/20171212\\806822855c5e40abdea5ef500a3faebd.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('56', '1', 'upload\\goods/20171212\\2eb8bd6da730a9ad8db8f21ca1306a63.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('119', '7', 'upload\\goods/20171212\\21bd6b120bd4fa8cf18433bdbcc02aee.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('120', '7', 'upload\\goods/20171212\\cb1174eee5d3eccbb739d5dc8123e03e.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('121', '7', 'upload\\goods/20171215\\2a0b9edc80787a415c53e91a419b1a09.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('122', '7', 'upload\\goods/20171215\\f4691f126a6fa915eccfe0d49626c559.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('123', '7', 'upload\\goods/20171215\\e2aae1cdc06fabaf2990bb732d14b2ea.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('124', '7', 'upload\\goods/20171215\\7ea2ac26f593dd5cb471aeb34ff52cde.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('125', '7', 'upload\\goods/20171215\\89e89df5f0fb4d695df64cf1be2bd4f8.png', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('126', '11', 'upload\\goods/20171214\\74090ac2cf42f3e78b03e95a7ef4c389.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('127', '11', 'upload\\goods/20171214\\8cdf0b34e9e46f2329df1ec5dcc1acb1.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('128', '11', 'upload\\goods/20171214\\8cdf0b34e9e46f2329df1ec5dcc1acb1.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('129', '11', 'upload\\goods/20171214\\1924cc24dbc4cec3565951ee16684ea7.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('130', '11', 'upload\\goods/20171214\\9d9eead68a60e7802587b46f6f96ca18.jpg', null, null, null, null, '0', null);
INSERT INTO `tp_goods_images` VALUES ('131', '11', 'upload\\goods/20171214\\36e9eaa6c0949f9c9a3949b304446fdc.jpg', null, null, null, null, '0', null);

-- ----------------------------
-- Table structure for tp_goods_type
-- ----------------------------
DROP TABLE IF EXISTS `tp_goods_type`;
CREATE TABLE `tp_goods_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id自增',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '类型名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_goods_type
-- ----------------------------

-- ----------------------------
-- Table structure for tp_goods_visit
-- ----------------------------
DROP TABLE IF EXISTS `tp_goods_visit`;
CREATE TABLE `tp_goods_visit` (
  `visit_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `goods_id` int(11) NOT NULL COMMENT '商品区ID',
  `user_id` int(11) NOT NULL COMMENT '会员ID',
  `visittime` int(11) NOT NULL COMMENT '浏览时间',
  `cat_id` int(11) NOT NULL COMMENT '商品区分类ID',
  PRIMARY KEY (`goods_id`,`user_id`,`visit_id`),
  KEY `visit_id` (`visit_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='商品区浏览历史表';

-- ----------------------------
-- Records of tp_goods_visit
-- ----------------------------
INSERT INTO `tp_goods_visit` VALUES ('1', '1', '1', '145531564', '0');
INSERT INTO `tp_goods_visit` VALUES ('2', '1', '1', '145331564', '0');

-- ----------------------------
-- Table structure for tp_group_buy
-- ----------------------------
DROP TABLE IF EXISTS `tp_group_buy`;
CREATE TABLE `tp_group_buy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '团购ID',
  `title` varchar(255) NOT NULL COMMENT '活动名称',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品区ID',
  `price` decimal(10,2) NOT NULL COMMENT '团购价格',
  `goods_num` int(10) DEFAULT '0' COMMENT '商品区参团数',
  `buy_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品区已购买数',
  `order_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已下单人数',
  `virtual_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟购买数',
  `rebate` decimal(10,1) NOT NULL COMMENT '折扣',
  `intro` text COMMENT '本团介绍',
  `goods_price` decimal(10,2) NOT NULL COMMENT '商品区原价',
  `goods_name` varchar(200) NOT NULL COMMENT '商品区名称',
  `recommended` tinyint(1) unsigned NOT NULL COMMENT '是否推荐 0.未推荐 1.已推荐',
  `views` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='团购商品区表';

-- ----------------------------
-- Records of tp_group_buy
-- ----------------------------

-- ----------------------------
-- Table structure for tp_jobs
-- ----------------------------
DROP TABLE IF EXISTS `tp_jobs`;
CREATE TABLE `tp_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `r` (`reserved_at`),
  KEY `rq` (`queue`),
  KEY `av` (`available_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50100 PARTITION BY LINEAR HASH (id)
(PARTITION p0 ENGINE = InnoDB,
 PARTITION p1 ENGINE = InnoDB,
 PARTITION p2 ENGINE = InnoDB,
 PARTITION p3 ENGINE = InnoDB,
 PARTITION p4 ENGINE = InnoDB,
 PARTITION p5 ENGINE = InnoDB,
 PARTITION p6 ENGINE = InnoDB,
 PARTITION p7 ENGINE = InnoDB,
 PARTITION p8 ENGINE = InnoDB,
 PARTITION p9 ENGINE = InnoDB,
 PARTITION p10 ENGINE = InnoDB,
 PARTITION p11 ENGINE = InnoDB,
 PARTITION p12 ENGINE = InnoDB,
 PARTITION p13 ENGINE = InnoDB,
 PARTITION p14 ENGINE = InnoDB,
 PARTITION p15 ENGINE = InnoDB,
 PARTITION p16 ENGINE = InnoDB,
 PARTITION p17 ENGINE = InnoDB,
 PARTITION p18 ENGINE = InnoDB,
 PARTITION p19 ENGINE = InnoDB,
 PARTITION p20 ENGINE = InnoDB,
 PARTITION p21 ENGINE = InnoDB,
 PARTITION p22 ENGINE = InnoDB) */;

-- ----------------------------
-- Records of tp_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for tp_likegoodmessagelist
-- ----------------------------
DROP TABLE IF EXISTS `tp_likegoodmessagelist`;
CREATE TABLE `tp_likegoodmessagelist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL,
  `is_send` tinyint(2) unsigned DEFAULT '0' COMMENT '0 待发送  1已经发送成功',
  PRIMARY KEY (`id`),
  KEY `id` (`id`) USING BTREE,
  KEY `end_time` (`end_time`),
  KEY `start_time` (`start_time`),
  KEY `is_send` (`is_send`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
/*!50100 PARTITION BY KEY (id)
PARTITIONS 50 */;

-- ----------------------------
-- Records of tp_likegoodmessagelist
-- ----------------------------

-- ----------------------------
-- Table structure for tp_lottery_gifts
-- ----------------------------
DROP TABLE IF EXISTS `tp_lottery_gifts`;
CREATE TABLE `tp_lottery_gifts` (
  `lottery_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '奖品ID',
  `lottery_name` varchar(100) NOT NULL COMMENT '奖品名称',
  `lottery_content` text COMMENT '奖品描述',
  `lottery_msg` varchar(100) DEFAULT NULL COMMENT '中奖提示语',
  `lottery_type` tinyint(3) unsigned DEFAULT NULL COMMENT '奖品类型',
  `lottery_jifen` smallint(5) unsigned DEFAULT NULL COMMENT '奖励积分',
  `lottery_num` int(11) unsigned DEFAULT '1' COMMENT '奖品个数',
  `lottery_pr` char(10) NOT NULL COMMENT '概率',
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '是否空奖（0否，1是）',
  `ifshow` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态（0显示；1不显示）',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `updatetime` int(11) unsigned DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`lottery_id`),
  KEY `lottery_id` (`lottery_id`,`lottery_name`,`lottery_type`,`status`,`ifshow`,`addtime`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='抽奖-奖品表';

-- ----------------------------
-- Records of tp_lottery_gifts
-- ----------------------------
INSERT INTO `tp_lottery_gifts` VALUES ('1', '摇一摇赢大奖', '哇  我中奖啦！！哇  我中奖啦！！哇  我中奖啦！！哇  我中奖啦！！', '哇  我中奖啦！！', '1', '30', '3', '10%', '0', '0', '1510041428', null);

-- ----------------------------
-- Table structure for tp_lottery_logs
-- ----------------------------
DROP TABLE IF EXISTS `tp_lottery_logs`;
CREATE TABLE `tp_lottery_logs` (
  `lottery_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `lottery_id` int(11) unsigned NOT NULL COMMENT '奖品ID',
  `user_id` int(11) unsigned NOT NULL COMMENT '中奖用户ID',
  `addtime` int(11) unsigned NOT NULL COMMENT '中奖时间',
  `day` int(11) unsigned NOT NULL COMMENT '中奖日期（20170303）方便查询',
  PRIMARY KEY (`lottery_log_id`),
  KEY `lottery_log_id` (`lottery_log_id`,`lottery_id`,`user_id`,`addtime`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖日志表';

-- ----------------------------
-- Records of tp_lottery_logs
-- ----------------------------

-- ----------------------------
-- Table structure for tp_lottery_set
-- ----------------------------
DROP TABLE IF EXISTS `tp_lottery_set`;
CREATE TABLE `tp_lottery_set` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '每次抽奖消耗积分',
  `content` varchar(100) NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='积分抽奖设置表';

-- ----------------------------
-- Records of tp_lottery_set
-- ----------------------------
INSERT INTO `tp_lottery_set` VALUES ('1', 'spend_jifen', '20');

-- ----------------------------
-- Table structure for tp_lottery_types
-- ----------------------------
DROP TABLE IF EXISTS `tp_lottery_types`;
CREATE TABLE `tp_lottery_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '类型ID',
  `type_name` varchar(100) DEFAULT NULL COMMENT '类型名称',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否启用（1禁用）',
  PRIMARY KEY (`type_id`),
  KEY `type_id` (`type_id`,`type_name`,`addtime`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='抽奖-奖品类型表';

-- ----------------------------
-- Records of tp_lottery_types
-- ----------------------------
INSERT INTO `tp_lottery_types` VALUES ('1', '现金红包', '1', '1');
INSERT INTO `tp_lottery_types` VALUES ('2', '优惠券', '2', '1');
INSERT INTO `tp_lottery_types` VALUES ('3', '积分', '3', '1');

-- ----------------------------
-- Table structure for tp_lottery_users
-- ----------------------------
DROP TABLE IF EXISTS `tp_lottery_users`;
CREATE TABLE `tp_lottery_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '标识',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户ID',
  `lottery_id` int(11) unsigned NOT NULL COMMENT '奖品ID',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `status` tinyint(3) unsigned NOT NULL COMMENT '是否删除（0正常；1删除）',
  `day` int(11) unsigned NOT NULL COMMENT '中奖日期',
  `mobile` varchar(20) NOT NULL COMMENT '手机号码',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`user_id`,`lottery_id`,`addtime`,`status`,`day`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖，必中奖用户表';

-- ----------------------------
-- Records of tp_lottery_users
-- ----------------------------

-- ----------------------------
-- Table structure for tp_member
-- ----------------------------
DROP TABLE IF EXISTS `tp_member`;
CREATE TABLE `tp_member` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(13) NOT NULL COMMENT '电话号码',
  `realName` varchar(20) DEFAULT NULL COMMENT '客户姓名',
  `weixin` varchar(100) DEFAULT NULL COMMENT '客户微信号码',
  `sex` int(1) DEFAULT '0' COMMENT '客户性别 0 女，1 男',
  `headPicture` varchar(500) DEFAULT NULL COMMENT ' 客户微信头像',
  `memberStatus` int(2) NOT NULL COMMENT '1：新增客户 2：潜在客户 3：意向客户 4：准客户 5：合作客户 6：已认证客户 7：已放弃客户',
  `statusChangeReason` varchar(1000) DEFAULT NULL COMMENT '状态变化备注',
  `memberLevel` int(2) DEFAULT NULL COMMENT '客户等级 1：铜牌，2：银牌，3：金牌 4：钻石',
  `levelChangeReason` varchar(1000) DEFAULT NULL COMMENT '客户等级变更原因备注',
  `createTime` datetime NOT NULL,
  `createUserId` bigint(20) NOT NULL,
  `updateTime` datetime NOT NULL,
  `updateUserId` bigint(20) NOT NULL,
  `version` bigint(20) NOT NULL DEFAULT '1' COMMENT '版本号,从1开始递增',
  `enabled` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用（逻辑状态）  0：禁用，1启用',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '是否删除（物理状态）  0：已删除，1：未删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_unique_index` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_member
-- ----------------------------

-- ----------------------------
-- Table structure for tp_message
-- ----------------------------
DROP TABLE IF EXISTS `tp_message`;
CREATE TABLE `tp_message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '管理者id',
  `message` text NOT NULL COMMENT '站内信内容',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '个体消息：0，全体消息1',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT ' 系统消息：0，活动消息：1',
  `send_time` int(10) unsigned NOT NULL COMMENT '发送时间',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_message
-- ----------------------------
INSERT INTO `tp_message` VALUES ('1', '1', '大家好我是wuzhilu', '1', '0', '1513414055');
INSERT INTO `tp_message` VALUES ('2', '1', '我懒河南女孩', '1', '0', '1513415217');
INSERT INTO `tp_message` VALUES ('3', '1', '好好工作好不好', '0', '0', '1513417757');
INSERT INTO `tp_message` VALUES ('4', '1', '好久不见了哈', '1', '0', '1513417859');

-- ----------------------------
-- Table structure for tp_navigation
-- ----------------------------
DROP TABLE IF EXISTS `tp_navigation`;
CREATE TABLE `tp_navigation` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '前台导航表',
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '导航名称',
  `is_show` tinyint(1) DEFAULT '1' COMMENT '是否显示',
  `is_new` tinyint(1) DEFAULT '1' COMMENT '是否新窗口',
  `sort` smallint(6) DEFAULT '50' COMMENT '排序',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '链接地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tp_navigation
-- ----------------------------

-- ----------------------------
-- Table structure for tp_order
-- ----------------------------
DROP TABLE IF EXISTS `tp_order`;
CREATE TABLE `tp_order` (
  `order_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '订单编号',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态 0待确认；1已确认；2已收货；3已取消；4已完成；5已作废',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态 0:未发货;1已发货；2已收货；',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态 0:未支付;1已支付',
  `consignee` varchar(60) NOT NULL DEFAULT '' COMMENT '收货人',
  `country` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '国家',
  `province` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '省份',
  `city` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '城市',
  `district` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '县区',
  `twon` int(11) DEFAULT '0' COMMENT '乡镇',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `zipcode` varchar(60) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `mobile` varchar(60) NOT NULL DEFAULT '' COMMENT '手机',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮件',
  `shipping_code` varchar(32) NOT NULL DEFAULT '0' COMMENT '物流code',
  `shipping_name` varchar(120) NOT NULL DEFAULT '' COMMENT '物流名称',
  `pay_code` varchar(32) NOT NULL DEFAULT '' COMMENT '支付code',
  `pay_name` varchar(120) NOT NULL DEFAULT '' COMMENT '支付方式名称',
  `invoice_title` varchar(256) DEFAULT '' COMMENT '发票抬头',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品区总价',
  `shipping_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `user_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用余额',
  `coupon_price` decimal(10,2) DEFAULT '0.00' COMMENT '优惠券抵扣',
  `integral` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0:代表正常订单 1:代表积分订单',
  `integral_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用积分抵多少钱',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付款金额',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '订单总价',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下单时间',
  `cancel_time` int(10) NOT NULL DEFAULT '0' COMMENT '取消时间',
  `shipping_time` int(11) DEFAULT '0' COMMENT '最后新发货时间',
  `confirm_time` int(10) DEFAULT '0' COMMENT '收货确认时间',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `order_prom_id` smallint(6) NOT NULL DEFAULT '0' COMMENT '活动id',
  `order_prom_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '活动优惠金额',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格调整',
  `user_note` varchar(255) NOT NULL DEFAULT '' COMMENT '用户备注',
  `admin_note` varchar(255) DEFAULT '' COMMENT '管理员备注',
  `parent_sn` varchar(100) DEFAULT NULL COMMENT '父单单号',
  `is_distribut` tinyint(1) DEFAULT '0' COMMENT '是否已分成0未分成1已分成',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_sn` (`order_sn`),
  KEY `user_id` (`user_id`),
  KEY `order_status` (`order_status`),
  KEY `shipping_status` (`shipping_status`),
  KEY `pay_status` (`pay_status`),
  KEY `shipping_id` (`shipping_code`),
  KEY `pay_id` (`pay_code`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_order
-- ----------------------------
INSERT INTO `tp_order` VALUES ('1', '2017121410155545', '1', '0', '2', '1', 'wuzhilu', '0', '0', '0', '0', '0', '江桥万达广场', '', '15343719375', '', '0', '', '', '余额支付', '', '555.00', '0.00', '0.00', '0.00', '0', '0.00', '555.00', '555.00', '1513236654', '0', '0', '0', '1513236664', '0', '0.00', '0.00', '', '', null, '0');
INSERT INTO `tp_order` VALUES ('2', '2017121448101995', '1', '0', '1', '1', 'wuzhilu', '0', '0', '0', '0', '0', '江桥万达广场', '', '15343719375', '', '0', '', '', '积分支付', '', '0.00', '0.00', '0.00', '0.00', '1', '200.00', '0.00', '0.00', '1513237344', '0', '0', '0', '1513237347', '0', '0.00', '0.00', '', '', null, '0');

-- ----------------------------
-- Table structure for tp_order_action
-- ----------------------------
DROP TABLE IF EXISTS `tp_order_action`;
CREATE TABLE `tp_order_action` (
  `action_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `action_user` int(11) DEFAULT '0' COMMENT '操作人 0 为管理员操作',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '配送状态',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态',
  `action_note` varchar(255) NOT NULL DEFAULT '' COMMENT '操作备注',
  `log_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `status_desc` varchar(255) DEFAULT NULL COMMENT '状态描述',
  PRIMARY KEY (`action_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_order_action
-- ----------------------------
INSERT INTO `tp_order_action` VALUES ('1', '811', '1', '0', '0', '1', '订单付款成功', '1513152130', '付款成功');
INSERT INTO `tp_order_action` VALUES ('2', '811', '1', '0', '0', '1', '', '1513152130', 'pay');

-- ----------------------------
-- Table structure for tp_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `tp_order_goods`;
CREATE TABLE `tp_order_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id自增',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品区id',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '视频名称',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品区货号',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '购买数量',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `goods_price` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '本店价',
  `cost_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品区成本价',
  `member_goods_price` decimal(10,2) DEFAULT '0.00' COMMENT '会员折扣价',
  `give_integral` mediumint(8) DEFAULT '0' COMMENT '购买商品区赠送积分',
  `spec_key` varchar(128) NOT NULL DEFAULT '' COMMENT '商品区规格key',
  `spec_key_name` varchar(128) NOT NULL DEFAULT '' COMMENT '规格对应的中文名字',
  `bar_code` varchar(64) NOT NULL DEFAULT '' COMMENT '条码',
  `is_comment` tinyint(1) DEFAULT '0' COMMENT '是否评价',
  `prom_type` tinyint(1) DEFAULT '0' COMMENT '0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠',
  `prom_id` int(11) DEFAULT '0' COMMENT '活动id',
  `is_send` tinyint(1) DEFAULT '0' COMMENT '0未发货，1已发货，2已换货，3已退货',
  `delivery_id` int(11) DEFAULT '0' COMMENT '发货单ID',
  `sku` varchar(128) DEFAULT '' COMMENT 'sku',
  `upload_time` int(11) unsigned NOT NULL DEFAULT '0',
  `exchange_integral` int(11) unsigned DEFAULT '0' COMMENT '积分数量 默认为0',
  PRIMARY KEY (`rec_id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_order_goods
-- ----------------------------
INSERT INTO `tp_order_goods` VALUES ('1', '1', '1', '古玩书画', '', '1', '0.00', '555.00', '0.00', '0.00', '0', '', '', '', '0', '0', '0', '0', '0', '', '0', '0');
INSERT INTO `tp_order_goods` VALUES ('2', '2', '10', '999', '', '1', '0.00', '0.00', '0.00', '0.00', '0', '', '', '', '0', '0', '0', '0', '0', '', '0', '200');

-- ----------------------------
-- Table structure for tp_payment
-- ----------------------------
DROP TABLE IF EXISTS `tp_payment`;
CREATE TABLE `tp_payment` (
  `pay_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `pay_code` varchar(20) NOT NULL DEFAULT '' COMMENT '支付code',
  `pay_name` varchar(120) NOT NULL DEFAULT '' COMMENT '支付方式名称',
  `pay_fee` varchar(10) NOT NULL DEFAULT '0' COMMENT '手续费',
  `pay_desc` text NOT NULL COMMENT '描述',
  `pay_order` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'pay_coder',
  `pay_config` text NOT NULL COMMENT '配置',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '开启',
  `is_cod` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否货到付款',
  `is_online` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否在线支付',
  PRIMARY KEY (`pay_id`),
  UNIQUE KEY `pay_code` (`pay_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_payment
-- ----------------------------

-- ----------------------------
-- Table structure for tp_pick_up
-- ----------------------------
DROP TABLE IF EXISTS `tp_pick_up`;
CREATE TABLE `tp_pick_up` (
  `pickup_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自提点id',
  `pickup_name` varchar(255) NOT NULL COMMENT '自提点名称',
  `pickup_address` varchar(255) NOT NULL COMMENT '自提点地址',
  `pickup_phone` varchar(30) NOT NULL COMMENT '自提点电话',
  `pickup_contact` varchar(20) NOT NULL COMMENT '自提点联系人',
  `province_id` int(11) NOT NULL COMMENT '省id',
  `city_id` int(11) NOT NULL COMMENT '市id',
  `district_id` int(11) NOT NULL COMMENT '区id',
  `suppliersid` int(11) NOT NULL COMMENT '供应商id',
  PRIMARY KEY (`pickup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自提点表';

-- ----------------------------
-- Records of tp_pick_up
-- ----------------------------

-- ----------------------------
-- Table structure for tp_plugin
-- ----------------------------
DROP TABLE IF EXISTS `tp_plugin`;
CREATE TABLE `tp_plugin` (
  `code` varchar(13) DEFAULT NULL COMMENT '插件编码',
  `name` varchar(55) DEFAULT NULL COMMENT '中文名字',
  `version` varchar(255) DEFAULT NULL COMMENT '插件的版本',
  `author` varchar(30) DEFAULT NULL COMMENT '插件作者',
  `config` text COMMENT '配置信息',
  `config_value` text COMMENT '配置值信息',
  `desc` varchar(255) DEFAULT NULL COMMENT '插件描述',
  `status` tinyint(1) DEFAULT '0' COMMENT '是否启用',
  `type` varchar(50) DEFAULT NULL COMMENT '插件类型 payment支付 login 登陆 shipping物流',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `bank_code` text COMMENT '网银配置信息',
  `scene` tinyint(1) DEFAULT '0' COMMENT '使用场景 0 PC+手机 1 手机 2 PC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_plugin
-- ----------------------------
INSERT INTO `tp_plugin` VALUES ('alipay', 'PC端支付宝', '1.0', 'jy_pwn', 'a:6:{i:0;a:4:{s:4:\"name\";s:14:\"alipay_account\";s:5:\"label\";s:15:\"支付宝帐户\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:1;a:4:{s:4:\"name\";s:10:\"alipay_key\";s:5:\"label\";s:21:\"交易安全校验码\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:2;a:4:{s:4:\"name\";s:14:\"alipay_partner\";s:5:\"label\";s:17:\"合作者身份ID\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:3;a:4:{s:4:\"name\";s:18:\"alipay_private_key\";s:5:\"label\";s:6:\"秘钥\";s:4:\"type\";s:8:\"textarea\";s:5:\"value\";s:0:\"\";}i:4;a:4:{s:4:\"name\";s:17:\"alipay_pay_method\";s:5:\"label\";s:19:\" 选择接口类型\";s:4:\"type\";s:6:\"select\";s:6:\"option\";a:2:{i:0;s:24:\"使用担保交易接口\";i:1;s:30:\"使用即时到帐交易接口\";}}i:5;a:4:{s:4:\"name\";s:7:\"is_bank\";s:5:\"label\";s:18:\"是否开通网银\";s:4:\"type\";s:6:\"select\";s:6:\"option\";a:2:{i:0;s:3:\"否\";i:1;s:3:\"是\";}}}', null, 'PC端支付宝插件 ', '0', 'payment', 'logo.jpg', 'a:8:{s:12:\"招商银行\";s:9:\"CMB-DEBIT\";s:18:\"中国工商银行\";s:10:\"ICBC-DEBIT\";s:12:\"交通银行\";s:10:\"COMM-DEBIT\";s:18:\"中国建设银行\";s:9:\"CCB-DEBIT\";s:18:\"中国民生银行\";s:4:\"CMBC\";s:12:\"中国银行\";s:9:\"BOC-DEBIT\";s:18:\"中国农业银行\";s:3:\"ABC\";s:12:\"上海银行\";s:6:\"SHBANK\";}', '2');
INSERT INTO `tp_plugin` VALUES ('alipayMobile', '手机网站支付宝', '1.0', '宇宙人', 'a:6:{i:0;a:4:{s:4:\"name\";s:14:\"alipay_account\";s:5:\"label\";s:15:\"支付宝帐户\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:1;a:4:{s:4:\"name\";s:10:\"alipay_key\";s:5:\"label\";s:21:\"交易安全校验码\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:2;a:4:{s:4:\"name\";s:14:\"alipay_partner\";s:5:\"label\";s:17:\"合作者身份ID\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:3;a:4:{s:4:\"name\";s:18:\"alipay_private_key\";s:5:\"label\";s:6:\"秘钥\";s:4:\"type\";s:8:\"textarea\";s:5:\"value\";s:0:\"\";}i:4;a:4:{s:4:\"name\";s:17:\"alipay_pay_method\";s:5:\"label\";s:19:\" 选择接口类型\";s:4:\"type\";s:6:\"select\";s:6:\"option\";a:2:{i:0;s:24:\"使用担保交易接口\";i:1;s:30:\"使用即时到帐交易接口\";}}i:5;a:4:{s:4:\"name\";s:7:\"is_bank\";s:5:\"label\";s:18:\"是否开通网银\";s:4:\"type\";s:6:\"select\";s:6:\"option\";a:2:{i:0;s:3:\"否\";i:1;s:3:\"是\";}}}', null, '手机端网站支付宝 ', '0', 'payment', 'logo.jpg', 'N;', '1');
INSERT INTO `tp_plugin` VALUES ('cod', '到货付款', '1.0', 'IT宇宙人', 'a:1:{i:0;a:4:{s:4:\"name\";s:9:\"code_desc\";s:5:\"label\";s:12:\"配送描述\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}}', null, '货到付款插件 ', '0', 'payment', 'logo.jpg', 'N;', '0');
INSERT INTO `tp_plugin` VALUES ('tenpay', 'PC端财付通', '1.0', 'IT宇宙人', 'a:2:{i:0;a:4:{s:4:\"name\";s:7:\"partner\";s:5:\"label\";s:7:\"partner\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:1;a:4:{s:4:\"name\";s:3:\"key\";s:5:\"label\";s:3:\"key\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}}', null, 'PC端财付通插件 ', '0', 'payment', 'logo.jpg', 'N;', '2');
INSERT INTO `tp_plugin` VALUES ('unionpay', '银联在线支付', '1.0', '奇闻科技', 'a:4:{i:0;a:4:{s:4:\"name\";s:12:\"unionpay_mid\";s:5:\"label\";s:9:\"商户号\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:15:\"777290058130619\";}i:1;a:4:{s:4:\"name\";s:21:\"unionpay_cer_password\";s:5:\"label\";s:25:\" 商户私钥证书密码\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:6:\"000000\";}i:2;a:4:{s:4:\"name\";s:13:\"unionpay_user\";s:5:\"label\";s:19:\" 企业网银账号\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:12:\"123456789001\";}i:3;a:4:{s:4:\"name\";s:17:\"unionpay_password\";s:5:\"label\";s:19:\" 企业网银密码\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:6:\"789001\";}}', null, '银联在线支付插件 ', '0', 'payment', 'logo.jpg', 'N;', '0');
INSERT INTO `tp_plugin` VALUES ('weixin', '微信支付', '1.0', 'IT宇宙人', 'a:4:{i:0;a:4:{s:4:\"name\";s:5:\"appid\";s:5:\"label\";s:20:\"绑定支付的APPID\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:1;a:4:{s:4:\"name\";s:5:\"mchid\";s:5:\"label\";s:9:\"商户号\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:2;a:4:{s:4:\"name\";s:3:\"key\";s:5:\"label\";s:18:\"商户支付密钥\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:3;a:4:{s:4:\"name\";s:9:\"appsecret\";s:5:\"label\";s:57:\"公众帐号secert（仅JSAPI支付的时候需要配置)\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}}', null, 'PC端+微信公众号支付', '0', 'payment', 'logo.jpg', 'N;', '0');
INSERT INTO `tp_plugin` VALUES ('alipay', '支付宝快捷登陆', '1.0', '彭老师', 'a:2:{i:0;a:4:{s:4:\"name\";s:14:\"alipay_partner\";s:5:\"label\";s:17:\"合作者身份ID\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:1;a:4:{s:4:\"name\";s:10:\"alipay_key\";s:5:\"label\";s:15:\"安全检验码\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}}', null, '支付宝快捷登陆插件 ', '0', 'login', 'logo.jpg', 'N;', null);
INSERT INTO `tp_plugin` VALUES ('qq', 'QQ登陆', '1.0', '彭老师', 'a:2:{i:0;a:4:{s:4:\"name\";s:6:\"app_id\";s:5:\"label\";s:6:\"app_id\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}i:1;a:4:{s:4:\"name\";s:10:\"app_secret\";s:5:\"label\";s:10:\"app_secret\";s:4:\"type\";s:4:\"text\";s:5:\"value\";s:0:\"\";}}', null, 'QQ登陆插件 ', '0', 'login', 'logo.jpg', 'N;', null);
INSERT INTO `tp_plugin` VALUES ('bestexpress', '百世汇通', '1.0', 'bestexpress', '', null, '百世汇通插件 ', '0', 'shipping', 'logo.jpg', 'N;', null);
INSERT INTO `tp_plugin` VALUES ('shentong', '申通物流', '1.0', '宇宙人', '', null, '申通物流插件 ', '1', 'shipping', 'logo.jpg', 'N;', null);
INSERT INTO `tp_plugin` VALUES ('shunfeng', '顺丰物流', '1.0', 'shunfeng', '', null, '顺丰物流插件 ', '1', 'shipping', 'logo.jpg', 'N;', null);
INSERT INTO `tp_plugin` VALUES ('tiantian', '天天物流', '1.0', 'tiantian', '', null, '天天快递插件 ', '1', 'shipping', 'logo.jpg', 'N;', null);
INSERT INTO `tp_plugin` VALUES ('ztoexpress', '中通快递', '1.0', 'ztoexpress', '', null, '中通快递插件 ', '0', 'shipping', 'logo.jpg', 'N;', null);
INSERT INTO `tp_plugin` VALUES ('helloworld', 'HelloWorld插件', 'v1.2.0,v1.2.1,v1.2.2,v1.2.3', 'IT宇宙人', '', null, '适合v1.2.0 , v1.2.1', '0', 'function', 'logo.jpg', 'N;', null);

-- ----------------------------
-- Table structure for tp_prom_goods
-- ----------------------------
DROP TABLE IF EXISTS `tp_prom_goods`;
CREATE TABLE `tp_prom_goods` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '活动ID',
  `name` varchar(60) NOT NULL COMMENT '促销活动名称',
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '促销类型',
  `expression` varchar(100) NOT NULL COMMENT '优惠体现',
  `description` text COMMENT '活动描述',
  `start_time` int(11) NOT NULL COMMENT '活动开始时间',
  `end_time` int(11) NOT NULL COMMENT '活动结束时间',
  `is_close` tinyint(1) DEFAULT '0',
  `group` varchar(255) DEFAULT NULL COMMENT '适用范围',
  `prom_img` varchar(150) DEFAULT NULL COMMENT '活动宣传图片',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_prom_goods
-- ----------------------------

-- ----------------------------
-- Table structure for tp_prom_order
-- ----------------------------
DROP TABLE IF EXISTS `tp_prom_order`;
CREATE TABLE `tp_prom_order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL COMMENT '活动名称',
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '活动类型',
  `money` float(10,2) DEFAULT '0.00' COMMENT '最小金额',
  `expression` varchar(100) DEFAULT NULL COMMENT '优惠体现',
  `description` text COMMENT '活动描述',
  `start_time` int(11) DEFAULT NULL COMMENT '活动开始时间',
  `end_time` int(11) DEFAULT NULL COMMENT '活动结束时间',
  `is_close` tinyint(1) DEFAULT '0',
  `group` varchar(255) DEFAULT NULL COMMENT '适用范围',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_prom_order
-- ----------------------------

-- ----------------------------
-- Table structure for tp_rebate_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_rebate_log`;
CREATE TABLE `tp_rebate_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分成记录表',
  `user_id` int(11) DEFAULT '0' COMMENT '获佣用户',
  `buy_user_id` int(11) DEFAULT '0' COMMENT '购买人id',
  `nickname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '购买人名称',
  `order_sn` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '订单编号',
  `order_id` int(11) DEFAULT '0' COMMENT '订单id',
  `goods_price` decimal(10,2) DEFAULT '0.00' COMMENT '订单商品区总额',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '获佣金额',
  `level` tinyint(1) DEFAULT '1' COMMENT '获佣用户级别',
  `create_time` int(11) DEFAULT '0' COMMENT '分成记录生成时间',
  `confirm` int(11) DEFAULT '0' COMMENT '确定收货时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '0未付款,1已付款, 2等待分成(已收货) 3已分成, 4已取消',
  `confirm_time` int(11) DEFAULT '0' COMMENT '确定分成或者取消时间',
  `remark` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '如果是取消, 有取消备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_rebate_log
-- ----------------------------

-- ----------------------------
-- Table structure for tp_rechare_images
-- ----------------------------
DROP TABLE IF EXISTS `tp_rechare_images`;
CREATE TABLE `tp_rechare_images` (
  `rec_img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '图片id 自增',
  `rec_order_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品区id',
  `image_url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
  `delete_time` int(11) DEFAULT NULL,
  `rescourse_id` varchar(244) DEFAULT NULL,
  `enpiount` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`rec_img_id`),
  KEY `rec_img_id` (`rec_img_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_rechare_images
-- ----------------------------

-- ----------------------------
-- Table structure for tp_rechare_images_id
-- ----------------------------
DROP TABLE IF EXISTS `tp_rechare_images_id`;
CREATE TABLE `tp_rechare_images_id` (
  `rechare_images_id` int(10) NOT NULL AUTO_INCREMENT,
  `datetime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rechare_images_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_rechare_images_id
-- ----------------------------

-- ----------------------------
-- Table structure for tp_recharge
-- ----------------------------
DROP TABLE IF EXISTS `tp_recharge`;
CREATE TABLE `tp_recharge` (
  `order_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '会员ID',
  `nickname` varchar(50) DEFAULT NULL COMMENT '会员昵称',
  `order_sn` varchar(30) NOT NULL COMMENT '充值单号',
  `account` float(10,2) DEFAULT '0.00' COMMENT '充值金额',
  `ctime` int(11) DEFAULT NULL COMMENT '充值时间',
  `pay_time` int(11) DEFAULT NULL COMMENT '支付时间',
  `pay_code` varchar(20) DEFAULT NULL,
  `pay_name` varchar(80) DEFAULT NULL COMMENT '支付方式',
  `pay_status` tinyint(1) DEFAULT '0' COMMENT '充值状态0:待支付 1:充值成功 2:交易关闭',
  `pingzhengid` int(10) DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_recharge
-- ----------------------------

-- ----------------------------
-- Table structure for tp_region
-- ----------------------------
DROP TABLE IF EXISTS `tp_region`;
CREATE TABLE `tp_region` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `name` varchar(32) DEFAULT NULL COMMENT '地区名称',
  `level` tinyint(4) DEFAULT NULL COMMENT '地区等级 分省市县区',
  `parent_id` int(10) DEFAULT NULL COMMENT '父id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of tp_region
-- ----------------------------

-- ----------------------------
-- Table structure for tp_region2
-- ----------------------------
DROP TABLE IF EXISTS `tp_region2`;
CREATE TABLE `tp_region2` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `name` varchar(20) NOT NULL COMMENT '地区名称',
  `parent_id` int(11) DEFAULT NULL COMMENT '父id',
  `level` tinyint(1) DEFAULT NULL COMMENT '地区等级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of tp_region2
-- ----------------------------

-- ----------------------------
-- Table structure for tp_remittance
-- ----------------------------
DROP TABLE IF EXISTS `tp_remittance`;
CREATE TABLE `tp_remittance` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分销用户转账记录表',
  `user_id` int(11) DEFAULT '0' COMMENT '汇款的用户id',
  `bank_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '收款银行名称',
  `account_bank` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '银行账号',
  `account_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '开户人名称',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '汇款金额',
  `status` tinyint(1) DEFAULT '0' COMMENT '0汇款失败 1汇款成功',
  `create_time` int(11) DEFAULT '0' COMMENT '汇款时间',
  `remark` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '汇款备注',
  `admin_id` int(11) DEFAULT '0' COMMENT '管理员id',
  `withdrawals_id` int(11) DEFAULT '0' COMMENT '提现申请表id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_remittance
-- ----------------------------

-- ----------------------------
-- Table structure for tp_return_goods
-- ----------------------------
DROP TABLE IF EXISTS `tp_return_goods`;
CREATE TABLE `tp_return_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '退货申请表id自增',
  `order_id` int(11) DEFAULT '0' COMMENT '订单id',
  `order_sn` varchar(1024) CHARACTER SET utf8 DEFAULT '' COMMENT '订单编号',
  `goods_id` int(11) DEFAULT '0' COMMENT '商品区id',
  `type` tinyint(1) DEFAULT '0' COMMENT '0退货1换货',
  `reason` varchar(1024) CHARACTER SET utf8 DEFAULT '' COMMENT '退换货原因',
  `imgs` varchar(512) CHARACTER SET utf8 DEFAULT '' COMMENT '拍照图片路径',
  `addtime` int(11) DEFAULT '0' COMMENT '申请时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '0申请中1客服理中2已完成',
  `remark` varchar(1024) CHARACTER SET utf8 DEFAULT '' COMMENT '客服备注',
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `spec_key` varchar(64) CHARACTER SET utf8 DEFAULT '' COMMENT '商品区规格key 对应tp_spec_goods_price 表',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tp_return_goods
-- ----------------------------

-- ----------------------------
-- Table structure for tp_sendmessagelists
-- ----------------------------
DROP TABLE IF EXISTS `tp_sendmessagelists`;
CREATE TABLE `tp_sendmessagelists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sendmessagetime` int(11) unsigned DEFAULT '0',
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_sendmessagelists
-- ----------------------------

-- ----------------------------
-- Table structure for tp_shipping
-- ----------------------------
DROP TABLE IF EXISTS `tp_shipping`;
CREATE TABLE `tp_shipping` (
  `shipping_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '表 id',
  `shipping_code` varchar(20) NOT NULL DEFAULT '' COMMENT '快递代号',
  `shipping_name` varchar(120) NOT NULL DEFAULT '' COMMENT '快递名称',
  `shipping_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `insure` varchar(10) NOT NULL DEFAULT '0' COMMENT '保险',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启',
  PRIMARY KEY (`shipping_id`),
  KEY `shipping_code` (`shipping_code`,`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_shipping
-- ----------------------------

-- ----------------------------
-- Table structure for tp_shipping_area
-- ----------------------------
DROP TABLE IF EXISTS `tp_shipping_area`;
CREATE TABLE `tp_shipping_area` (
  `shipping_area_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `shipping_area_name` varchar(150) NOT NULL DEFAULT '' COMMENT '配送区域名称',
  `shipping_code` varchar(50) NOT NULL DEFAULT '0' COMMENT '物流id',
  `config` text NOT NULL COMMENT '配置首重续重等...序列化存储',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否默认',
  PRIMARY KEY (`shipping_area_id`),
  KEY `shipping_id` (`shipping_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_shipping_area
-- ----------------------------
INSERT INTO `tp_shipping_area` VALUES ('1', '全国其他地区', 'shentong', 'a:4:{s:12:\"first_weight\";s:4:\"1000\";s:13:\"second_weight\";s:4:\"2000\";s:5:\"money\";s:2:\"12\";s:9:\"add_money\";s:1:\"2\";}', null, '1');
INSERT INTO `tp_shipping_area` VALUES ('2', '全国其他地区', 'shunfeng', 'a:4:{s:12:\"first_weight\";s:4:\"1000\";s:13:\"second_weight\";s:4:\"2000\";s:5:\"money\";s:2:\"12\";s:9:\"add_money\";s:1:\"2\";}', null, '1');
INSERT INTO `tp_shipping_area` VALUES ('3', '全国其他地区', 'tiantian', 'a:4:{s:12:\"first_weight\";s:4:\"1000\";s:13:\"second_weight\";s:4:\"2000\";s:5:\"money\";s:2:\"12\";s:9:\"add_money\";s:1:\"2\";}', null, '1');

-- ----------------------------
-- Table structure for tp_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `tp_sms_log`;
CREATE TABLE `tp_sms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `mobile` varchar(11) DEFAULT '' COMMENT '手机号',
  `session_id` varchar(128) DEFAULT '' COMMENT 'session_id',
  `add_time` int(11) DEFAULT '0' COMMENT '发送时间',
  `code` varchar(10) DEFAULT '' COMMENT '验证码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tp_sms_log
-- ----------------------------

-- ----------------------------
-- Table structure for tp_spec
-- ----------------------------
DROP TABLE IF EXISTS `tp_spec`;
CREATE TABLE `tp_spec` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '规格表',
  `type_id` int(11) DEFAULT '0' COMMENT '规格类型',
  `name` varchar(55) DEFAULT NULL COMMENT '规格名称',
  `order` int(11) DEFAULT '50' COMMENT '排序',
  `search_index` tinyint(1) DEFAULT '1' COMMENT '是否需要检索：1是，0否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_spec
-- ----------------------------

-- ----------------------------
-- Table structure for tp_spec_goods_price
-- ----------------------------
DROP TABLE IF EXISTS `tp_spec_goods_price`;
CREATE TABLE `tp_spec_goods_price` (
  `goods_id` int(11) DEFAULT '0' COMMENT '商品区id',
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '规格键名',
  `key_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '规格键名中文',
  `price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `store_count` int(11) unsigned DEFAULT '10' COMMENT '库存数量',
  `bar_code` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '商品区条形码',
  `sku` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT 'SKU'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_spec_goods_price
-- ----------------------------

-- ----------------------------
-- Table structure for tp_spec_image
-- ----------------------------
DROP TABLE IF EXISTS `tp_spec_image`;
CREATE TABLE `tp_spec_image` (
  `goods_id` int(11) DEFAULT '0' COMMENT '商品区规格图片表id',
  `spec_image_id` int(11) DEFAULT '0' COMMENT '规格项id',
  `src` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '商品区规格图片路径'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tp_spec_image
-- ----------------------------

-- ----------------------------
-- Table structure for tp_spec_item
-- ----------------------------
DROP TABLE IF EXISTS `tp_spec_item`;
CREATE TABLE `tp_spec_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '规格项id',
  `spec_id` int(11) DEFAULT NULL COMMENT '规格id',
  `item` varchar(54) DEFAULT NULL COMMENT '规格项',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_spec_item
-- ----------------------------

-- ----------------------------
-- Table structure for tp_store_level
-- ----------------------------
DROP TABLE IF EXISTS `tp_store_level`;
CREATE TABLE `tp_store_level` (
  `store_level_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '店铺等级',
  `store_name` varchar(11) NOT NULL COMMENT '店铺名称',
  `store_cost` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '店铺费用',
  `line_nums` int(10) NOT NULL DEFAULT '0' COMMENT '在线商品区',
  `line_time1` int(11) NOT NULL DEFAULT '0' COMMENT '商品区预展(小时)',
  `line_time2` int(11) NOT NULL DEFAULT '0' COMMENT '展示时长（小时）',
  `is_tuijian` int(11) NOT NULL DEFAULT '0' COMMENT '商品区推荐 ',
  `is_mark` int(11) NOT NULL DEFAULT '0' COMMENT '认证标识',
  `is_change` int(11) NOT NULL DEFAULT '0' COMMENT '积分兑换',
  `is_price` int(11) NOT NULL DEFAULT '0' COMMENT '保留价',
  `is_editer` int(11) NOT NULL DEFAULT '0' COMMENT '流拍编辑',
  `last_time` int(11) NOT NULL COMMENT '流拍保留(小时)',
  `charges` int(11) NOT NULL DEFAULT '0' COMMENT '成交佣金 (百分比)',
  `fans` int(11) NOT NULL COMMENT '粉丝匹配',
  `products` smallint(6) NOT NULL DEFAULT '1' COMMENT '群发商品区(次/天)',
  `emails` smallint(6) NOT NULL DEFAULT '1' COMMENT '群发件数(件/次)',
  `yaoyao` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '摇一摇（次/天）',
  `is_head` tinyint(4) NOT NULL DEFAULT '0' COMMENT '头条推广',
  `low_price` tinyint(4) NOT NULL DEFAULT '0' COMMENT '天天特价',
  `special` tinyint(4) NOT NULL DEFAULT '0' COMMENT '活动专区',
  `income` tinyint(4) NOT NULL DEFAULT '0' COMMENT '推广收益',
  `tixian_money` int(11) NOT NULL COMMENT '提现金额(每次)',
  `period` int(11) NOT NULL COMMENT '到账周期',
  `tixian_poundage` int(11) NOT NULL COMMENT '提现手续费',
  `tixian_style` varchar(11) NOT NULL COMMENT '提现方式',
  `sever_time` int(11) NOT NULL DEFAULT '1' COMMENT '服务周期(年)',
  `sever_type` varchar(10) NOT NULL COMMENT '服务类型',
  `line_anser` int(11) NOT NULL COMMENT '在线回复(小时)',
  `is_kefu` tinyint(4) NOT NULL DEFAULT '0' COMMENT '专属客服',
  `new_function` tinyint(4) NOT NULL DEFAULT '0' COMMENT '体现新功能',
  PRIMARY KEY (`store_level_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_store_level
-- ----------------------------
INSERT INTO `tp_store_level` VALUES ('1', '游客商店', '0.00', '11', '6', '24', '1', '0', '0', '0', '1', '5', '11', '100', '1', '1', '0', '0', '0', '0', '0', '300', '10', '16', '微信', '1', '普通售后', '120', '0', '0');
INSERT INTO `tp_store_level` VALUES ('2', '普通商店', '1098.00', '22', '6', '24', '1', '0', '0', '0', '1', '5', '22', '100', '1', '1', '2', '0', '0', '0', '0', '300', '10', '16', '微信', '1', '普通售后', '120', '0', '0');
INSERT INTO `tp_store_level` VALUES ('3', '专业商店', '3098.00', '33', '6', '24', '1', '0', '0', '0', '1', '5', '33', '100', '1', '1', '3', '0', '0', '0', '0', '300', '10', '16', '微信', '1', '普通售后', '120', '0', '0');
INSERT INTO `tp_store_level` VALUES ('4', '豪华商店', '5098.00', '44', '6', '24', '1', '0', '0', '0', '1', '5', '44', '100', '1', '1', '4', '0', '0', '0', '0', '300', '10', '16', '微信', '1', '普通售后', '120', '0', '0');
INSERT INTO `tp_store_level` VALUES ('5', '企业商店', '9098.00', '5', '12', '24', '1', '0', '0', '0', '1', '5', '55', '100', '1', '1', '5', '0', '0', '0', '0', '300', '10', '16', '微信', '1', '普通售后', '120', '0', '0');

-- ----------------------------
-- Table structure for tp_suppliers
-- ----------------------------
DROP TABLE IF EXISTS `tp_suppliers`;
CREATE TABLE `tp_suppliers` (
  `suppliers_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '供应商ID',
  `suppliers_name` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商名称',
  `suppliers_desc` mediumtext NOT NULL COMMENT '供应商描述',
  `is_check` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '供应商状态',
  `suppliers_contacts` varchar(255) NOT NULL DEFAULT '' COMMENT '供应商联系人',
  `suppliers_phone` varchar(20) NOT NULL DEFAULT '' COMMENT '供应商名字',
  PRIMARY KEY (`suppliers_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_suppliers
-- ----------------------------

-- ----------------------------
-- Table structure for tp_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `tp_system_menu`;
CREATE TABLE `tp_system_menu` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '权限名字',
  `group` varchar(20) DEFAULT NULL COMMENT '所属分组',
  `right` text COMMENT '权限码(控制器+动作)',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '删除状态 1删除,0正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_system_menu
-- ----------------------------

-- ----------------------------
-- Table structure for tp_system_module
-- ----------------------------
DROP TABLE IF EXISTS `tp_system_module`;
CREATE TABLE `tp_system_module` (
  `mod_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `module` enum('top','menu','module') DEFAULT 'module',
  `level` tinyint(1) DEFAULT '3',
  `ctl` varchar(20) DEFAULT '',
  `act` varchar(30) DEFAULT '',
  `title` varchar(20) DEFAULT '',
  `visible` tinyint(1) DEFAULT '1',
  `parent_id` smallint(6) DEFAULT '0',
  `orderby` smallint(6) DEFAULT '50',
  `icon` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`mod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_system_module
-- ----------------------------

-- ----------------------------
-- Table structure for tp_third_users
-- ----------------------------
DROP TABLE IF EXISTS `tp_third_users`;
CREATE TABLE `tp_third_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `oauth` varchar(10) DEFAULT '' COMMENT '第三方来源 wx weibo alipay',
  `openid` varchar(100) DEFAULT NULL COMMENT '第三方唯一标示',
  `subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1订阅 ',
  `head_pic` varchar(400) DEFAULT NULL COMMENT '头像',
  `country` varchar(30) DEFAULT NULL,
  `province` int(6) DEFAULT '0' COMMENT '省份',
  `city` int(6) DEFAULT '0' COMMENT '市区',
  `district` int(6) DEFAULT '0' COMMENT '县',
  `email_validated` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否验证电子邮箱',
  `nickname` varchar(50) DEFAULT NULL COMMENT '第三方返回昵称',
  `sex` tinyint(2) DEFAULT NULL,
  `level` tinyint(1) DEFAULT '1' COMMENT '会员等级',
  `discount` decimal(10,2) DEFAULT '1.00' COMMENT '会员折扣，默认1不享受',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '消费累计额度',
  `is_lock` tinyint(1) DEFAULT '0' COMMENT '是否被锁定冻结',
  `is_distribut` tinyint(1) DEFAULT '0' COMMENT '是否为分销商 0 否 1 是',
  `first_leader` int(11) DEFAULT '0' COMMENT '第一个上级',
  `second_leader` int(11) DEFAULT '0' COMMENT '第二个上级',
  `third_leader` int(11) DEFAULT '0' COMMENT '第三个上级',
  `token` varchar(64) DEFAULT '' COMMENT '用于app 授权类似于session_id',
  `weixinnumber` varchar(64) DEFAULT NULL,
  `lastsynctime` int(11) NOT NULL DEFAULT '0' COMMENT '上次同步时间',
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `openid` (`openid`) USING HASH,
  KEY `userid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_third_users
-- ----------------------------

-- ----------------------------
-- Table structure for tp_topic
-- ----------------------------
DROP TABLE IF EXISTS `tp_topic`;
CREATE TABLE `tp_topic` (
  `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `topic_title` varchar(100) DEFAULT NULL COMMENT '专题标题',
  `topic_image` varchar(100) DEFAULT NULL COMMENT '专题封面',
  `topic_background_color` varchar(20) DEFAULT NULL COMMENT '专题背景颜色',
  `topic_background` varchar(100) DEFAULT NULL COMMENT '专题背景图',
  `topic_content` text COMMENT '专题详情',
  `topic_repeat` varchar(20) DEFAULT '' COMMENT '背景重复方式',
  `topic_state` tinyint(1) DEFAULT '1' COMMENT '专题状态1-草稿、2-已发布',
  `topic_margin_top` tinyint(3) DEFAULT '0' COMMENT '正文距顶部距离',
  `ctime` int(11) DEFAULT NULL COMMENT '专题创建时间',
  PRIMARY KEY (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_topic
-- ----------------------------

-- ----------------------------
-- Table structure for tp_track_record
-- ----------------------------
DROP TABLE IF EXISTS `tp_track_record`;
CREATE TABLE `tp_track_record` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键ID，自增长',
  `memberId` bigint(20) NOT NULL COMMENT '客户信息记录ID（tp_member:id）',
  `mobile` varchar(13) NOT NULL COMMENT '手机号码',
  `trackType` int(2) NOT NULL COMMENT '跟进方式 1、打电话，2、微信，3、QQ',
  `trackContent` varchar(500) NOT NULL COMMENT '跟进内容',
  `trackDate` datetime NOT NULL COMMENT '跟进日期',
  `nextTrackDate` datetime DEFAULT NULL COMMENT '下次跟进日期',
  `createTime` datetime NOT NULL COMMENT '跟进记录创建日期',
  `createUserId` bigint(20) NOT NULL COMMENT '跟进人员ID',
  `updateTime` datetime NOT NULL COMMENT '跟进记录更新日期',
  `updateUserId` bigint(20) NOT NULL COMMENT '跟进记录更新人员ID',
  `version` bigint(20) NOT NULL COMMENT '当前版本',
  `enabled` int(1) NOT NULL DEFAULT '1' COMMENT '是否启用 0、禁用，1、启用',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '是否删除 0、已删除，1、未删除',
  PRIMARY KEY (`id`),
  KEY `tp_track_mobile` (`mobile`) USING BTREE,
  KEY `tp_track_member_id` (`memberId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_track_record
-- ----------------------------

-- ----------------------------
-- Table structure for tp_users
-- ----------------------------
DROP TABLE IF EXISTS `tp_users`;
CREATE TABLE `tp_users` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `real_name` varchar(40) NOT NULL COMMENT '真实姓名',
  `user_name` varchar(40) NOT NULL COMMENT '用户名',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮件',
  `password` varchar(512) NOT NULL DEFAULT '' COMMENT '密码',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 保密 1 男 2 女',
  `birthday` int(11) NOT NULL DEFAULT '0' COMMENT '生日',
  `keep_days` int(11) NOT NULL DEFAULT '1' COMMENT '用户累计登陆天数',
  `user_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户金额',
  `frozen_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额',
  `distribut_money` decimal(10,2) DEFAULT '0.00' COMMENT '累积分佣金额',
  `pay_points` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消费积分',
  `address_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '默认收货地址',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `qq` varchar(20) NOT NULL COMMENT 'QQ',
  `mobile` varchar(20) NOT NULL COMMENT '手机号码',
  `mobile_validated` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否验证手机',
  `oauth` varchar(10) DEFAULT '' COMMENT '第三方来源 wx weibo alipay',
  `head_pic` varchar(400) DEFAULT NULL COMMENT '头像',
  `province` int(6) DEFAULT '0' COMMENT '省份',
  `city` int(6) DEFAULT '0' COMMENT '市区',
  `district` int(6) DEFAULT '0' COMMENT '县',
  `email_validated` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否验证电子邮箱',
  `nickname` varchar(50) DEFAULT NULL COMMENT '第三方返回昵称',
  `user_level` tinyint(1) DEFAULT '1' COMMENT '会员等级',
  `store_level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '店铺等级',
  `discount` decimal(10,2) DEFAULT '1.00' COMMENT '会员折扣，默认1不享受',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '消费累计额度',
  `is_lock` tinyint(1) DEFAULT '0' COMMENT '是否被锁定冻结',
  `is_distribut` tinyint(1) DEFAULT '0' COMMENT '是否为分销商 0 否 1 是',
  `first_leader` int(11) DEFAULT '0' COMMENT '第一个上级',
  `second_leader` int(11) DEFAULT '0' COMMENT '第二个上级',
  `token` varchar(64) DEFAULT '' COMMENT '用于app 授权类似于session_id',
  `usersingnature` varchar(100) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `user_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1代表个人 2代表公司 ',
  `fictitious` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1是虚拟用户 0是正常用户',
  `weight_sale` tinyint(3) NOT NULL DEFAULT '0',
  `is_give_freefans` int(11) NOT NULL DEFAULT '0' COMMENT '0没有赠送  1已经赠送 1次 2赠送2次',
  `is_give_freefans_datetime` int(11) NOT NULL DEFAULT '0' COMMENT '赠送粉丝时间',
  `image_url_remote_expire` int(11) NOT NULL DEFAULT '0',
  `third_leader` int(11) NOT NULL DEFAULT '0',
  `is_authentication` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0没有认证  1已经认证',
  `is_border` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1代表最近15天已经被使用过',
  PRIMARY KEY (`user_id`),
  KEY `email` (`email`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `head_pic` (`head_pic`(255)) USING BTREE,
  KEY `detatime` (`delete_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_users
-- ----------------------------
INSERT INTO `tp_users` VALUES ('1', '', '', '1228388018@qq.com', '519475228fe35ad067744465c42a19b2', '1', '0', '1', '1509719.00', '153.00', '0.00', '998000', '0', '1510107995', '0', '', '1228388018', '15343719375', '0', '', null, '0', '0', '0', '0', 'andy', '4', '5', '1.00', '1228.00', '0', '0', '0', '0', '', null, null, '1', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `tp_users` VALUES ('2', '', '', '153437611554@qq.com', '519475228fe35ad067744465c42a19b2', '0', '0', '1', '0.00', '0.00', '0.00', '0', '0', '1510128069', '0', '', '153437611554', '15343761155', '0', '', null, '0', '0', '0', '0', 'lili', '1', '1', '1.00', '0.00', '0', '0', '0', '0', '', null, null, '1', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `tp_users` VALUES ('3', '', '', '4745131454@qq.com', 'd5b399f5dee3b1917fe0903b9f6fd0a7', '0', '0', '1', '0.00', '0.00', '0.00', '0', '0', '1510389413', '0', '', '4745131454', '', '0', '', null, '0', '0', '0', '0', 'admin', '1', '1', '1.00', '0.00', '0', '0', '0', '0', '', null, null, '1', '0', '0', '0', '0', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for tp_users_20171014
-- ----------------------------
DROP TABLE IF EXISTS `tp_users_20171014`;
CREATE TABLE `tp_users_20171014` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `head_pic` varchar(255) NOT NULL COMMENT '头像地址',
  PRIMARY KEY (`id`),
  KEY `use_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_users_20171014
-- ----------------------------

-- ----------------------------
-- Table structure for tp_users_avatar
-- ----------------------------
DROP TABLE IF EXISTS `tp_users_avatar`;
CREATE TABLE `tp_users_avatar` (
  `int` mediumint(8) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `add_time` int(11) DEFAULT '0',
  `sex` tinyint(1) DEFAULT '0' COMMENT '0女 1男',
  PRIMARY KEY (`int`),
  UNIQUE KEY `url` (`url`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_users_avatar
-- ----------------------------

-- ----------------------------
-- Table structure for tp_user_address
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_address`;
CREATE TABLE `tp_user_address` (
  `address_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `consignee` varchar(60) NOT NULL DEFAULT '' COMMENT '收货人',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `country` int(11) NOT NULL DEFAULT '0' COMMENT '国家',
  `province` int(11) NOT NULL DEFAULT '0' COMMENT '省份',
  `city` int(11) NOT NULL DEFAULT '0' COMMENT '城市',
  `district` int(11) NOT NULL DEFAULT '0' COMMENT '地区',
  `twon` int(11) DEFAULT '0' COMMENT '乡镇',
  `address` varchar(120) NOT NULL DEFAULT '' COMMENT '地址',
  `zipcode` varchar(60) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `mobile` varchar(60) NOT NULL DEFAULT '' COMMENT '手机',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '默认收货地址',
  `is_pickup` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`address_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_user_address
-- ----------------------------

-- ----------------------------
-- Table structure for tp_user_bond
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_bond`;
CREATE TABLE `tp_user_bond` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(60) NOT NULL,
  `bond` decimal(10,2) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `datetime` int(11) NOT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_user_bond
-- ----------------------------

-- ----------------------------
-- Table structure for tp_user_level
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_level`;
CREATE TABLE `tp_user_level` (
  `level_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '会员等级表',
  `level_name` varchar(30) DEFAULT NULL COMMENT '会员等级名称',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '等级必要金额',
  `time_length` varchar(11) DEFAULT '0' COMMENT '时间长度',
  `discount` smallint(4) DEFAULT NULL COMMENT '折扣',
  `describe` varchar(200) DEFAULT NULL COMMENT '等级 描述',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_user_level
-- ----------------------------
INSERT INTO `tp_user_level` VALUES ('1', 'V0-v1', '0.00', '0个月', '100', '无活跃等级');
INSERT INTO `tp_user_level` VALUES ('2', 'V1-v2', '30000.00', '90天', '90', '每天登录平台1天，发布藏品+0.5天，发布三件以上+0.5天');
INSERT INTO `tp_user_level` VALUES ('3', 'V2-v3', '40000.00', '180天', '90', '每天登录平台1天，发布藏品+0.5天，发布三件以上+0.5天');
INSERT INTO `tp_user_level` VALUES ('4', 'V3-v4', '50000.00', '270天', '80', '每天登录平台1天，发布藏品+0.5天，发布三件以上+0.5天');
INSERT INTO `tp_user_level` VALUES ('5', 'V4-v5', '60000.00', '365天', '75', '每天登录平台1天，发布藏品+0.5天，发布三件以上+0.5天');

-- ----------------------------
-- Table structure for tp_user_message
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_message`;
CREATE TABLE `tp_user_message` (
  `rec_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `message_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消息id',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '系统消息0，活动消息',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '查看状态：0未查看，1已查看',
  PRIMARY KEY (`rec_id`),
  UNIQUE KEY `user_id_2` (`user_id`,`message_id`),
  KEY `user_id` (`user_id`),
  KEY `message_id` (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_user_message
-- ----------------------------
INSERT INTO `tp_user_message` VALUES ('12', '1', '1', '0', '1');
INSERT INTO `tp_user_message` VALUES ('13', '1', '2', '0', '1');
INSERT INTO `tp_user_message` VALUES ('14', '1', '3', '0', '1');
INSERT INTO `tp_user_message` VALUES ('15', '1', '4', '0', '1');

-- ----------------------------
-- Table structure for tp_user_pic
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_pic`;
CREATE TABLE `tp_user_pic` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `head_pic` varchar(600) NOT NULL COMMENT '头像',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1已经使用',
  `datatime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_user_pic
-- ----------------------------

-- ----------------------------
-- Table structure for tp_user_sendmessage
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_sendmessage`;
CREATE TABLE `tp_user_sendmessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `sendactivemessagecount` mediumint(5) unsigned NOT NULL DEFAULT '0' COMMENT '当天发送数量',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_user_sendmessage
-- ----------------------------

-- ----------------------------
-- Table structure for tp_user_verifty
-- ----------------------------
DROP TABLE IF EXISTS `tp_user_verifty`;
CREATE TABLE `tp_user_verifty` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户id',
  `name` varchar(255) NOT NULL COMMENT '用户名称 / 企业名称',
  `idcode` varchar(255) DEFAULT NULL COMMENT '身份证号码',
  `telephone` varchar(60) NOT NULL DEFAULT '' COMMENT '联系电话',
  `verifyIdcodefront` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证正面/企业营业执照',
  `verifyIdcodeback` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证反面',
  `verifyIdcodehold` varchar(255) NOT NULL DEFAULT '' COMMENT '手持身份证照片',
  `date_time` int(11) NOT NULL DEFAULT '0' COMMENT '认证时间',
  `identity_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '认证类型 1:个人，2:企业',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1未审核   2已审核 3 审核不通过',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_user_verifty
-- ----------------------------
INSERT INTO `tp_user_verifty` VALUES ('1', '2', 'Andy', '4513131248584', '1534512456', '', '', '', '0', '1', '1');
INSERT INTO `tp_user_verifty` VALUES ('4', '3', '炸弹是1', null, '15965548255', 'upload\\goods/20171212\\35e44780b90f03457dd419d349cb1e0b.png', '', '', '1513064128', '2', '1');

-- ----------------------------
-- Table structure for tp_withdrawals
-- ----------------------------
DROP TABLE IF EXISTS `tp_withdrawals`;
CREATE TABLE `tp_withdrawals` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '提现申请表',
  `user_id` int(11) DEFAULT '0' COMMENT '用户id',
  `create_time` int(11) DEFAULT '0' COMMENT '申请日期',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '提现金额',
  `bank_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '银行名称 如支付宝 微信 中国银行 农业银行等',
  `account_bank` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '银行账号',
  `account_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '银行账户名 可以是支付宝可以其他银行',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '提现备注',
  `status` tinyint(1) DEFAULT '0' COMMENT '提现状态0申请中1申请成功2申请失败',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_withdrawals
-- ----------------------------

-- ----------------------------
-- Table structure for tp_wx_img
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_img`;
CREATE TABLE `tp_wx_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `keyword` char(255) NOT NULL COMMENT '关键词',
  `desc` text NOT NULL COMMENT '简介',
  `pic` char(255) NOT NULL COMMENT '封面图片',
  `url` char(255) NOT NULL COMMENT '图文外链地址',
  `createtime` varchar(13) NOT NULL COMMENT '创建时间',
  `uptatetime` varchar(13) NOT NULL COMMENT '更新时间',
  `token` char(30) NOT NULL COMMENT 'token',
  `title` varchar(60) NOT NULL COMMENT '标题',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品区id',
  `goods_name` varchar(50) DEFAULT NULL COMMENT '商品区名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信图文';

-- ----------------------------
-- Records of tp_wx_img
-- ----------------------------

-- ----------------------------
-- Table structure for tp_wx_keyword
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_keyword`;
CREATE TABLE `tp_wx_keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `keyword` char(255) NOT NULL COMMENT '关键词',
  `pid` int(11) NOT NULL COMMENT '对应表ID',
  `token` varchar(60) NOT NULL COMMENT 'token',
  `type` varchar(30) DEFAULT 'TEXT' COMMENT '关键词操作类型',
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_wx_keyword
-- ----------------------------

-- ----------------------------
-- Table structure for tp_wx_menu
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_menu`;
CREATE TABLE `tp_wx_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `level` tinyint(1) DEFAULT '1' COMMENT '菜单级别',
  `name` varchar(50) NOT NULL COMMENT 'name',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `type` varchar(20) DEFAULT '' COMMENT '0 view 1 click',
  `value` varchar(255) DEFAULT NULL COMMENT 'value',
  `token` varchar(50) NOT NULL COMMENT 'token',
  `pid` int(11) DEFAULT '0' COMMENT '上级菜单',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_wx_menu
-- ----------------------------

-- ----------------------------
-- Table structure for tp_wx_msg
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_msg`;
CREATE TABLE `tp_wx_msg` (
  `msgid` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL COMMENT '系统用户ID',
  `titile` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `sendtime` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `issend` tinyint(1) DEFAULT '0' COMMENT '0未发送1成功2失败',
  `sendtype` tinyint(1) DEFAULT '1' COMMENT '0单人1所有',
  PRIMARY KEY (`msgid`),
  KEY `uid` (`admin_id`),
  KEY `createymd` (`sendtime`),
  KEY `fake_id` (`titile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tp_wx_msg
-- ----------------------------

-- ----------------------------
-- Table structure for tp_wx_news
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_news`;
CREATE TABLE `tp_wx_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `keyword` char(255) NOT NULL COMMENT 'keyword',
  `createtime` varchar(13) NOT NULL COMMENT '创建时间',
  `uptatetime` varchar(13) NOT NULL COMMENT '更新时间',
  `token` char(30) NOT NULL COMMENT 'token',
  `img_id` varchar(50) DEFAULT NULL COMMENT '图文组合id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信图文';

-- ----------------------------
-- Records of tp_wx_news
-- ----------------------------

-- ----------------------------
-- Table structure for tp_wx_text
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_text`;
CREATE TABLE `tp_wx_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `uname` varchar(90) NOT NULL COMMENT '用户名',
  `keyword` char(255) NOT NULL COMMENT '关键词',
  `precisions` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'precisions',
  `text` text NOT NULL COMMENT 'text',
  `createtime` varchar(13) NOT NULL COMMENT '创建时间',
  `updatetime` varchar(13) NOT NULL COMMENT '更新时间',
  `click` int(11) NOT NULL COMMENT '点击',
  `token` char(30) NOT NULL COMMENT 'token',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文本回复表';

-- ----------------------------
-- Records of tp_wx_text
-- ----------------------------

-- ----------------------------
-- Table structure for tp_wx_user
-- ----------------------------
DROP TABLE IF EXISTS `tp_wx_user`;
CREATE TABLE `tp_wx_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `uid` int(11) NOT NULL COMMENT 'uid',
  `wxname` varchar(60) NOT NULL COMMENT '公众号名称',
  `aeskey` varchar(256) NOT NULL DEFAULT '' COMMENT 'aeskey',
  `encode` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'encode',
  `appid` varchar(50) NOT NULL DEFAULT '' COMMENT 'appid',
  `appsecret` varchar(50) NOT NULL DEFAULT '' COMMENT 'appsecret',
  `wxid` varchar(64) NOT NULL COMMENT '公众号原始ID',
  `weixin` char(64) NOT NULL COMMENT '微信号',
  `headerpic` char(255) NOT NULL COMMENT '头像地址',
  `token` char(255) NOT NULL COMMENT 'token',
  `w_token` varchar(150) NOT NULL DEFAULT '' COMMENT '微信对接token',
  `create_time` int(11) NOT NULL COMMENT 'create_time',
  `updatetime` int(11) NOT NULL COMMENT 'updatetime',
  `tplcontentid` varchar(2) NOT NULL COMMENT '内容模版ID',
  `share_ticket` varchar(150) NOT NULL COMMENT '分享ticket',
  `share_dated` char(15) NOT NULL COMMENT 'share_dated',
  `authorizer_access_token` varchar(200) NOT NULL COMMENT 'authorizer_access_token',
  `authorizer_refresh_token` varchar(200) NOT NULL COMMENT 'authorizer_refresh_token',
  `authorizer_expires` char(10) NOT NULL COMMENT 'authorizer_expires',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型',
  `web_access_token` varchar(200) NOT NULL COMMENT ' 网页授权token',
  `web_refresh_token` varchar(200) NOT NULL COMMENT 'web_refresh_token',
  `web_expires` int(11) NOT NULL COMMENT '过期时间',
  `qr` varchar(200) NOT NULL COMMENT 'qr',
  `menu_config` text COMMENT '菜单',
  `wait_access` tinyint(1) DEFAULT '0' COMMENT '微信接入状态,0待接入1已接入',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE,
  KEY `uid_2` (`uid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信公共帐号';

-- ----------------------------
-- Records of tp_wx_user
-- ----------------------------
