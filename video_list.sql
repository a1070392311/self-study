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

 Date: 03/12/2019 17:51:43
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for video_list
-- ----------------------------
DROP TABLE IF EXISTS `video_list`;
CREATE TABLE `video_list`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `packageid` int(11) NOT NULL COMMENT '专辑id',
  `sortid` int(11) NOT NULL COMMENT '排序id',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '单集视频名称',
  `intro` varchar(600) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '视频介绍',
  `play_count` int(13) DEFAULT NULL COMMENT '播放次数',
  `face_img` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '视频封面',
  `show_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1:公开，2：非公开',
  `is_delete` varchar(1) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '0:未删除 1：已经软性删除，对外不显示',
  `video_source_name` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '视频上传后源文件名称',
  `video_source_ext` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '视频上传后源文件文件格式',
  `video_name` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '视频对外名称',
  `video_ext` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '视频对外格式',
  `transcode` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0：未转码  1：转码完成  2：正在转码  4：转码失败',
  `hls_status` tinyint(1) NOT NULL COMMENT '0：未切片   1：切片完成   2：正在切片 4：切片失败',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
