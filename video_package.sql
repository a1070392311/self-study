/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : file

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 03/12/2019 17:51:49
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for video_package
-- ----------------------------
DROP TABLE IF EXISTS `video_package`;
CREATE TABLE `video_package`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `face_img` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '封面图片',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '视频专辑名称',
  `create_time` int(13) NOT NULL COMMENT '专辑创建时间',
  `update_time` int(13) NOT NULL COMMENT '专辑最近修改时间',
  `video_count` int(3) NOT NULL DEFAULT 0 COMMENT '专辑包含的视频数量',
  `uid` int(11) NOT NULL COMMENT '专辑创建者id',
  `create_time_format` datetime NOT NULL COMMENT '时间类型不同',
  `update_time_format` datetime NOT NULL COMMENT '时间类型不同',
  `play_count` int(13) NOT NULL DEFAULT 0 COMMENT '本专辑共计播放次数',
  `type` int(2) NOT NULL DEFAULT 1 COMMENT '1:教程 ；2：电影；',
  `intro` varchar(600) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '简介',
  `show_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1:公开，2：非公开',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:未删除 1：已经软性删除，对外不显示',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
