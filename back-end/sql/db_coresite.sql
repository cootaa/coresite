/*
 Navicat Premium Data Transfer

 Source Server         : coresite
 Source Server Type    : MySQL
 Source Server Version : 50616
 Source Host           : cota-test.mysql.rds.aliyuncs.com:4237
 Source Schema         : cn_com_coresite

 Target Server Type    : MySQL
 Target Server Version : 50616
 File Encoding         : 65001

 Date: 22/03/2024 09:51:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for chat
-- ----------------------------
DROP TABLE IF EXISTS `chat`;
CREATE TABLE `chat` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `project_id` bigint(20) NOT NULL COMMENT '项目id',
  `user_id` bigint(20) NOT NULL COMMENT '用户id',
  `nickname` char(16) NOT NULL COMMENT '昵称',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `message` varchar(255) NOT NULL COMMENT '聊天信息',
  `add_date` datetime(6) NOT NULL COMMENT '保存日期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8 COMMENT='聊天信息列表';

-- ----------------------------
-- Table structure for project
-- ----------------------------
DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` bigint(20) NOT NULL COMMENT '主键id',
  `name` char(36) NOT NULL COMMENT '项目名称',
  `alias` char(2) DEFAULT NULL COMMENT '项目别名',
  `theme` char(8) NOT NULL COMMENT '主题颜色',
  `owner` bigint(20) NOT NULL COMMENT '管理员id',
  `members` varchar(255) DEFAULT NULL COMMENT '成员id集',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目表';

-- ----------------------------
-- Table structure for tb_chat
-- ----------------------------
DROP TABLE IF EXISTS `tb_chat`;
CREATE TABLE `tb_chat` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `project_id` int(8) DEFAULT NULL COMMENT '项目id',
  `user_id` int(8) DEFAULT NULL COMMENT '用户id',
  `message` text COMMENT '信息内容',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8 COMMENT='聊天内容表';

-- ----------------------------
-- Table structure for tb_discussion
-- ----------------------------
DROP TABLE IF EXISTS `tb_discussion`;
CREATE TABLE `tb_discussion` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `group_id` int(8) DEFAULT NULL COMMENT '组织id',
  `creator_id` int(8) DEFAULT NULL COMMENT '创建者id',
  `title` varchar(255) DEFAULT NULL COMMENT '讨论标题',
  `content` text COMMENT '讨论内容',
  `status` int(8) DEFAULT '1' COMMENT '状态 0：已删除  1：在线',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_discussion_comment
-- ----------------------------
DROP TABLE IF EXISTS `tb_discussion_comment`;
CREATE TABLE `tb_discussion_comment` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `discussion_id` int(8) DEFAULT NULL COMMENT '讨论id',
  `user_id` int(8) DEFAULT NULL COMMENT '评论人id',
  `comment` varchar(5000) DEFAULT NULL COMMENT '评论内容',
  `status` int(8) DEFAULT '1' COMMENT '状态 0：已删除 1：在线',
  `create_time` int(10) DEFAULT NULL COMMENT '评论时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_file
-- ----------------------------
DROP TABLE IF EXISTS `tb_file`;
CREATE TABLE `tb_file` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `folder_id` int(8) DEFAULT NULL COMMENT '所属文件夹id',
  `name` varchar(255) DEFAULT NULL COMMENT '文件名',
  `type` varchar(255) DEFAULT NULL COMMENT '文件类型（系统判断结果）',
  `format` varchar(255) DEFAULT NULL COMMENT '文件格式（后缀）',
  `size` int(8) DEFAULT NULL COMMENT '文件大小',
  `url` varchar(255) DEFAULT NULL COMMENT '文件url',
  `creator_id` int(8) DEFAULT NULL COMMENT '上传者id',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=383 DEFAULT CHARSET=utf8 COMMENT='文件表';

-- ----------------------------
-- Table structure for tb_folder
-- ----------------------------
DROP TABLE IF EXISTS `tb_folder`;
CREATE TABLE `tb_folder` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `project_id` int(8) DEFAULT NULL COMMENT '项目id',
  `name` varchar(255) DEFAULT NULL COMMENT '文件夹名称',
  `parent_id` int(8) DEFAULT NULL COMMENT '上级文件夹id',
  `creator_id` int(8) DEFAULT NULL COMMENT '创建者id',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COMMENT='文件夹表';

-- ----------------------------
-- Table structure for tb_group
-- ----------------------------
DROP TABLE IF EXISTS `tb_group`;
CREATE TABLE `tb_group` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '组织名称',
  `icon` varchar(255) DEFAULT NULL COMMENT '组织标志',
  `status` int(10) NOT NULL DEFAULT '1' COMMENT '组织状态：0：关闭状态 1：开启状态',
  `creator_id` int(8) DEFAULT NULL COMMENT '组织创建者id',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `close_time` int(10) DEFAULT NULL COMMENT '关闭时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COMMENT='组织表';

-- ----------------------------
-- Table structure for tb_group_user
-- ----------------------------
DROP TABLE IF EXISTS `tb_group_user`;
CREATE TABLE `tb_group_user` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `group_id` int(8) DEFAULT NULL COMMENT '组织/团队id',
  `user_id` int(8) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8 COMMENT='组织成员表';

-- ----------------------------
-- Table structure for tb_notice
-- ----------------------------
DROP TABLE IF EXISTS `tb_notice`;
CREATE TABLE `tb_notice` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `group_id` int(8) DEFAULT NULL COMMENT '组织id',
  `creator_id` int(8) DEFAULT NULL COMMENT '创建者id',
  `content` varchar(255) DEFAULT NULL COMMENT '内容',
  `status` int(8) DEFAULT '1' COMMENT '状态 0：已删除  1：存在',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tb_project
-- ----------------------------
DROP TABLE IF EXISTS `tb_project`;
CREATE TABLE `tb_project` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `group_id` int(8) DEFAULT NULL COMMENT '所属组织/团队id',
  `name` varchar(255) DEFAULT NULL COMMENT '项目名称',
  `icon` varchar(255) DEFAULT NULL COMMENT '项目标志',
  `creator_id` int(8) DEFAULT NULL COMMENT '项目创建者id',
  `status` int(8) NOT NULL DEFAULT '1' COMMENT '项目状态 0：已关闭  1：开启',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `close_time` int(11) DEFAULT NULL COMMENT '关闭时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='项目表';

-- ----------------------------
-- Table structure for tb_project_user
-- ----------------------------
DROP TABLE IF EXISTS `tb_project_user`;
CREATE TABLE `tb_project_user` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `group_id` int(8) DEFAULT NULL COMMENT '组织/团队id',
  `project_id` int(8) DEFAULT NULL COMMENT '项目id',
  `user_id` int(8) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8 COMMENT='项目成员表';

-- ----------------------------
-- Table structure for tb_user
-- ----------------------------
DROP TABLE IF EXISTS `tb_user`;
CREATE TABLE `tb_user` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL COMMENT '登陆用户名',
  `password` varchar(255) DEFAULT NULL COMMENT '登陆密码',
  `nickname` varchar(255) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `bio` varchar(255) DEFAULT NULL COMMENT '个人简介',
  `setting` varchar(255) DEFAULT NULL COMMENT '用户设置',
  `limited` int(1) DEFAULT '0' COMMENT '限制登录',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `username` char(64) NOT NULL COMMENT '用户名，必须是邮箱地址',
  `password` char(16) NOT NULL COMMENT '密码，16位',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `nickname` char(16) DEFAULT NULL COMMENT '昵称',
  `city` char(36) DEFAULT NULL COMMENT '城市',
  `bio` varchar(255) DEFAULT NULL COMMENT '自我介绍',
  `token` varchar(255) DEFAULT NULL COMMENT '用户登录token',
  `invition_code` char(12) DEFAULT NULL COMMENT '邀请码，12位',
  `invition_notes` char(30) DEFAULT NULL COMMENT '邀请码备注，填写邀请谁',
  `invition_from` int(11) NOT NULL COMMENT '邀请人id',
  `projects` varchar(255) DEFAULT NULL COMMENT '项目id集',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='用户表';

SET FOREIGN_KEY_CHECKS = 1;
