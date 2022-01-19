# passport 数据库

CREATE DATABASE passport DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

use passport;

DROP TABLE IF EXISTS `t_passport_role`;
CREATE TABLE `t_passport_role` (
                                   `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                   `name` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名称',
                                   `admin` tinyint(4) NOT NULL DEFAULT '0' COMMENT '超级管理员 {0：否；1：是；}',
                                   `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                   `sort` int(10) NOT NULL DEFAULT '99' COMMENT '排序（正序）',
                                   `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                   `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                   PRIMARY KEY (`id`),
                                   KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

DROP TABLE IF EXISTS `t_passport_user`;
CREATE TABLE `t_passport_user` (
                                   `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                   `name` varchar(50) NOT NULL DEFAULT '' COMMENT '姓名',
                                   `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
                                   `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
                                   `position` varchar(50) NOT NULL DEFAULT '' COMMENT '职位',
                                   `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
                                   `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                   `root` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'ROOT 用户 {0：否；1：是；}',
                                   `sort` int(10) NOT NULL DEFAULT '99' COMMENT '排序（正序）',
                                   `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                   `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                   PRIMARY KEY (`id`),
                                   KEY `idx_name` (`name`),
                                   KEY `idx_email` (`email`),
                                   KEY `idx_mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

DROP TABLE IF EXISTS `t_passport_menu`;
CREATE TABLE `t_passport_menu` (
                                   `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                   `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级ID {0：顶级菜单；}',
                                   `name` varchar(20) NOT NULL DEFAULT '' COMMENT '菜单名称',
                                   `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单图标',
                                   `url` varchar(50) NOT NULL DEFAULT '' COMMENT '路由',
                                   `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                   `sort` int(10) NOT NULL DEFAULT '99' COMMENT '排序（正序）',
                                   `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                   `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                   PRIMARY KEY (`id`),
                                   KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='菜单表';

DROP TABLE IF EXISTS `t_passport_permission`;
CREATE TABLE `t_passport_permission` (
                                         `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                         `name` varchar(50) NOT NULL DEFAULT '' COMMENT '权限名称',
                                         `url` varchar(2000) NOT NULL DEFAULT '' COMMENT '路由（多个之间用英文分号隔开）',
                                         `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                         `sort` int(10) NOT NULL DEFAULT '99' COMMENT '排序（正序）',
                                         `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                         `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                         PRIMARY KEY (`id`),
                                         KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

DROP TABLE IF EXISTS `t_passport_role_permission`;
CREATE TABLE `t_passport_role_permission` (
                                              `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                              `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',
                                              `permission_id` int(10) unsigned NOT NULL COMMENT '权限ID',
                                              `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                              `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                              `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                              PRIMARY KEY (`id`),
                                              KEY `idx_role_id` (`role_id`),
                                              KEY `idx_permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色权限表';

INSERT INTO `t_passport_role_permission` VALUES (1, 2, 1, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_permission` VALUES (2, 2, 5, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_permission` VALUES (3, 2, 11, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_permission` VALUES (4, 2, 16, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_permission` VALUES (5, 2, 21, 1, '2022-01-19 11:30:10', '2022-01-19 11:30:10');
INSERT INTO `t_passport_role_permission` VALUES (6, 2, 26, 1, '2022-01-19 11:30:10', '2022-01-19 11:30:10');
INSERT INTO `t_passport_role_permission` VALUES (7, 2, 31, 1, '2022-01-19 11:30:10', '2022-01-19 11:30:10');
INSERT INTO `t_passport_role_permission` VALUES (8, 2, 34, 1, '2022-01-19 11:30:10', '2022-01-19 11:30:10');
INSERT INTO `t_passport_role_permission` VALUES (9, 2, 38, 1, '2022-01-19 11:30:10', '2022-01-19 11:30:10');

DROP TABLE IF EXISTS `t_passport_role_menu`;
CREATE TABLE `t_passport_role_menu` (
                                        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                        `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',
                                        `menu_id` int(10) unsigned NOT NULL COMMENT '菜单ID',
                                        `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                        `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                        `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                        PRIMARY KEY (`id`),
                                        KEY `idx_role_id` (`role_id`),
                                        KEY `idx_menu_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色菜单表';

INSERT INTO `t_passport_role_menu` VALUES (1, 2, 2, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_menu` VALUES (2, 2, 3, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_menu` VALUES (3, 2, 4, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_menu` VALUES (4, 2, 5, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_menu` VALUES (5, 2, 7, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_menu` VALUES (6, 2, 8, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_menu` VALUES (7, 2, 9, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_menu` VALUES (8, 2, 11, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');
INSERT INTO `t_passport_role_menu` VALUES (9, 2, 12, 1, '2022-01-19 11:30:10', '2022-01-19 11:29:22');

DROP TABLE IF EXISTS `t_passport_user_role`;
CREATE TABLE `t_passport_user_role` (
                                        `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                        `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
                                        `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',
                                        `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                        `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                        `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                        PRIMARY KEY (`id`),
                                        KEY `idx_user_id` (`user_id`),
                                        KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户角色表';

-- 菜单基础数据
INSERT INTO `t_passport_menu` VALUES (1, 0, '权限管理', 'fa fa-tasks', '', 1, 99, '2020-08-31 21:12:15', '2020-08-31 21:11:51');
INSERT INTO `t_passport_menu` VALUES (2, 1, '管理员', 'fa fa-users', '/view/user/search', 1, 99, '2020-09-08 10:20:42', '2020-08-31 21:21:10');
INSERT INTO `t_passport_menu` VALUES (3, 1, '角色', 'fa fa-user', '/view/role/search', 1, 99, '2020-08-31 21:28:26', '2020-08-31 21:21:53');
INSERT INTO `t_passport_menu` VALUES (4, 1, '权限', 'fa fa-ban', '/view/permission/search', 1, 99, '2020-09-02 12:28:02', '2020-08-31 21:23:09');
INSERT INTO `t_passport_menu` VALUES (5, 1, '菜单', 'fa fa-bars', '/view/menu/search', 1, 99, '2020-09-01 12:09:49', '2020-09-01 12:09:49');
INSERT INTO `t_passport_menu` VALUES (6, 0, '内容管理', 'fa fa-book', '', 1, 99, '2020-08-31 21:12:15', '2020-08-31 21:11:51');
INSERT INTO `t_passport_menu` VALUES (7, 6, '文章', 'fa fa-file', '/view/article/search', 1, 99, '2020-09-08 10:20:42', '2020-08-31 21:21:10');
INSERT INTO `t_passport_menu` VALUES (8, 6, '标签', 'fa fa-tags', '/view/tag/search', 1, 99, '2021-11-24 12:36:34', '2021-11-24 12:36:34');
INSERT INTO `t_passport_menu` VALUES (9, 6, '评论', 'fa fa-comments', '/view/comment/search', 1, 99, '2021-11-24 12:36:34', '2021-11-24 12:36:34');
INSERT INTO `t_passport_menu` VALUES (10, 0, '用户管理', 'fa fa-user', '', 1, 99, '2020-08-31 21:12:15', '2020-08-31 21:11:51');
INSERT INTO `t_passport_menu` VALUES (11, 10, '用户', 'fa fa-user-circle', '/view/account/search', 1, 99, '2021-11-24 12:36:34', '2021-11-24 12:36:34');
INSERT INTO `t_passport_menu` VALUES (12, 10, '行为日志', 'fa fa-camera', '/view/action_log/search', 1, 99, '2021-12-23 08:24:35', '2021-12-23 08:24:35');

-- 角色基础数据
INSERT INTO `t_passport_role` VALUES (1, '超级管理员', 1, 1, 1, '2020-09-04 14:26:32', '2020-09-02 19:45:21');
INSERT INTO `t_passport_role` VALUES (2, '游客', 0, 1, 99, '2022-01-19 11:30:10', '2022-01-19 11:29:22');

-- 权限基础数据
INSERT INTO `t_passport_permission` VALUES (1, '管理员列表', '/view/user/search;/v1/user/search', 1, 3, '2020-09-11 14:32:25', '2020-09-08 19:38:52');
INSERT INTO `t_passport_permission` VALUES (2, '管理员创建', '/view/user/create;/v1/user/create;/v1/role/select', 1, 4, '2020-09-11 14:32:28', '2020-09-09 09:41:15');
INSERT INTO `t_passport_permission` VALUES (3, '管理员更新', '/view/user/update;/v1/user/update;/v1/user/find;/v1/role/select', 1, 5, '2020-09-11 14:32:31', '2020-09-09 09:42:16');
INSERT INTO `t_passport_permission` VALUES (4, '管理员', '/view/user/search;/v1/user/search;/view/user/create;/v1/user/create;/view/user/update;/v1/user/update;/v1/user/find;/v1/user/delete;/v1/role/select', 1, 7, '2020-09-11 14:32:36', '2020-09-09 09:44:46');
INSERT INTO `t_passport_permission` VALUES (5, '菜单列表', '/view/menu/search;/v1/menu/search', 1, 8, '2020-09-11 14:32:39', '2020-09-09 09:46:14');
INSERT INTO `t_passport_permission` VALUES (6, '菜单创建', '/view/menu/create;/v1/menu/create', 1, 9, '2020-09-11 14:32:42', '2020-09-09 09:46:43');
INSERT INTO `t_passport_permission` VALUES (7, '菜单更新', '/view/menu/update;/v1/menu/update;/v1/menu/find', 1, 10, '2020-09-11 14:32:45', '2020-09-09 09:48:49');
INSERT INTO `t_passport_permission` VALUES (8, '管理员删除', '/v1/user/delete', 1, 6, '2020-09-11 14:32:47', '2020-09-09 09:50:17');
INSERT INTO `t_passport_permission` VALUES (9, '菜单删除', '/v1/menu/delete', 1, 11, '2020-09-11 14:32:50', '2020-09-09 09:54:30');
INSERT INTO `t_passport_permission` VALUES (10, '菜单', '/view/menu/search;/v1/menu/search;/view/menu/create;/v1/menu/create;/view/menu/update;/v1/menu/update;/v1/menu/find;/v1/menu/delete', 1, 12, '2020-09-11 14:32:52', '2020-09-09 09:58:56');
INSERT INTO `t_passport_permission` VALUES (11, '权限列表', '/view/permission/search;/v1/permission/search', 1, 13, '2020-09-11 14:32:54', '2020-09-09 10:00:26');
INSERT INTO `t_passport_permission` VALUES (12, '权限创建', '/view/permission/create;/v1/permission/create', 1, 14, '2020-09-11 14:32:57', '2020-09-09 10:01:03');
INSERT INTO `t_passport_permission` VALUES (13, '权限更新', '/view/permission/update;/v1/permission/update;/v1/permission/find', 1, 15, '2020-09-11 14:33:00', '2020-09-09 10:01:49');
INSERT INTO `t_passport_permission` VALUES (14, '权限删除', '/v1/permission/delete', 1, 16, '2020-09-11 14:33:03', '2020-09-09 10:02:22');
INSERT INTO `t_passport_permission` VALUES (15, '权限', '/view/permission/search;/v1/permission/search;/view/permission/create;/v1/permission/create;/view/permission/update;/v1/permission/update;/v1/permission/find;/v1/permission/delete', 1, 17, '2020-09-11 14:33:07', '2020-09-09 10:03:20');
INSERT INTO `t_passport_permission` VALUES (16, '角色列表', '/view/role/search;/v1/role/search', 1, 18, '2020-09-11 14:33:10', '2020-09-09 10:03:57');
INSERT INTO `t_passport_permission` VALUES (17, '角色创建', '/view/role/create;/v1/role/create;/v1/permission/select;/v1/menu/select', 1, 19, '2020-09-11 14:33:13', '2020-09-09 10:04:45');
INSERT INTO `t_passport_permission` VALUES (18, '角色更新', '/view/role/update;/v1/role/update;/v1/role/find;/v1/permission/select;/v1/menu/select', 1, 20, '2020-09-11 14:33:15', '2020-09-09 10:05:13');
INSERT INTO `t_passport_permission` VALUES (19, '角色删除', '/v1/role/delete', 1, 21, '2020-09-11 14:33:19', '2020-09-09 10:05:37');
INSERT INTO `t_passport_permission` VALUES (20, '角色', '/view/role/search;/v1/role/search;/view/role/create;/v1/role/create;/view/role/update;/v1/role/update;/v1/role/find;/v1/role/delete;/v1/permission/select;/v1/menu/select', 1, 22, '2020-09-11 14:33:22', '2020-09-09 10:15:25');
INSERT INTO `t_passport_permission` VALUES (21, '文章列表', '/view/article/search;/v1/article/search', 1, 23, '2020-12-09 10:36:16', '2020-12-09 10:36:16');
INSERT INTO `t_passport_permission` VALUES (22, '文章创建', '/view/article/create;/v1/article/create;/v1/tag/select;/v1/img/upload', 1, 24, '2022-01-19 13:24:58', '2020-12-09 10:37:13');
INSERT INTO `t_passport_permission` VALUES (23, '文章更新', '/view/article/update;/v1/article/update;/v1/article/find;/v1/tag/select;/v1/img/upload', 1, 25, '2022-01-19 05:25:18', '2020-12-09 10:38:34');
INSERT INTO `t_passport_permission` VALUES (24, '文章删除', '/v1/article/delete', 1, 26, '2020-12-09 10:39:14', '2020-12-09 10:39:14');
INSERT INTO `t_passport_permission` VALUES (25, '文章', '/view/article/search;/v1/article/search;/view/article/create;/v1/article/create;/view/article/update;/v1/article/update;/v1/article/find;/v1/article/delete;/v1/tag/select;/v1/img/upload', 1, 27, '2022-01-19 05:25:22', '2020-12-09 10:40:49');
INSERT INTO `t_passport_permission` VALUES (26, '标签列表', '/view/tag/search;/v1/tag/search', 1, 28, '2022-01-19 10:24:42', '2022-01-19 10:24:42');
INSERT INTO `t_passport_permission` VALUES (27, '标签创建', '/view/tag/create;/v1/tag/create', 1, 29, '2022-01-19 02:26:39', '2022-01-19 10:25:33');
INSERT INTO `t_passport_permission` VALUES (28, '标签更新', '/view/tag/update;/v1/tag/update;/v1/tag/find', 1, 30, '2022-01-19 10:26:45', '2022-01-19 10:26:45');
INSERT INTO `t_passport_permission` VALUES (29, '标签删除', '/v1/tag/delete', 1, 31, '2022-01-19 10:27:18', '2022-01-19 10:27:18');
INSERT INTO `t_passport_permission` VALUES (30, '标签', '/view/tag/search;/v1/tag/search;/view/tag/create;/v1/tag/create;/view/tag/update;/v1/tag/update;/v1/tag/find;/v1/tag/delete', 1, 32, '2022-01-19 02:28:18', '2022-01-19 10:28:02');
INSERT INTO `t_passport_permission` VALUES (31, '评论列表', '/view/comment/search;/v1/comment/search', 1, 33, '2022-01-19 02:38:20', '2022-01-19 10:30:01');
INSERT INTO `t_passport_permission` VALUES (32, '评论审核通过、删除', '/v1/comment/update_status', 1, 34, '2022-01-19 02:38:22', '2022-01-19 10:38:04');
INSERT INTO `t_passport_permission` VALUES (33, '评论', '/view/comment/search;/v1/comment/search;/v1/comment/update_status', 1, 35, '2022-01-19 02:41:46', '2022-01-19 10:41:35');
INSERT INTO `t_passport_permission` VALUES (34, '用户列表', '/view/account/search;/v1/account/search', 1, 36, '2022-01-19 02:45:36', '2022-01-19 10:43:06');
INSERT INTO `t_passport_permission` VALUES (35, '用户封禁、解封', '/v1/account/update_status', 1, 37, '2022-01-19 02:45:39', '2022-01-19 10:44:16');
INSERT INTO `t_passport_permission` VALUES (36, '用户备注', '/v1/account/update_mark_field', 1, 38, '2022-01-19 02:45:42', '2022-01-19 10:44:55');
INSERT INTO `t_passport_permission` VALUES (37, '用户', '/view/account/search;/v1/account/search;/v1/account/update_status;/v1/account/update_mark_field', 1, 39, '2022-01-19 02:45:45', '2022-01-19 10:45:26');
INSERT INTO `t_passport_permission` VALUES (38, '行为日志列表', '/view/action_log/search;/v1/action_log/search', 1, 99, '2022-01-19 10:47:10', '2022-01-19 10:47:10');

-- 管理员基础数据
INSERT INTO `t_passport_user` VALUES (1, 'admin', 'admin@gmail.com', '18311413962', '技术负责人', '8e306fa6f8966e4cb9fd523868ec4698', 1, 1, 1, '2020-09-06 12:54:51', '2020-08-27 17:08:08');
INSERT INTO `t_passport_user` VALUES (2, '游客', 'demo@gmail.com', '17788889999', '游客', '8e306fa6f8966e4cb9fd523868ec4698', 1, 0, 99, '2022-01-19 13:45:05', '2022-01-19 11:01:26');
-- 用户角色基础数据
INSERT INTO `t_passport_user_role` VALUES (1, 1, 1, 1, '2020-09-06 10:26:31', '2020-09-06 08:37:29');
INSERT INTO `t_passport_user_role` VALUES (2, 2, 2, 1, '2022-01-19 13:45:05', '2022-01-19 13:45:05');

# content 数据库

CREATE DATABASE content DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

use content;

DROP TABLE IF EXISTS `t_content_article`;
CREATE TABLE `t_content_article` (
                                     `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                     `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文章标题',
                                     `desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '文章描述',
                                     `cover_img_id`int(10) NOT NULL DEFAULT '0' COMMENT '文章封面图片ID',
                                     `content` text NOT NULL default '' comment '文章内容',
                                     `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                     `read_count` int(10) NOT NULL DEFAULT '0' COMMENT '阅读数',
                                     `sort` int(10) NOT NULL DEFAULT '99' COMMENT '排序（正序）',
                                     `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                     `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章表';

CREATE TABLE `t_content_special` (
                                     `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                     `title` varchar(255) NOT NULL DEFAULT '' COMMENT '专栏标题',
                                     `desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '专栏描述',
                                     `cover_img_id`int(10) NOT NULL DEFAULT '0' COMMENT '专栏封面图片ID',
                                     `content` text NOT NULL default '' comment '专栏内容',
                                     `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                     `read_count` int(10) NOT NULL DEFAULT '0' COMMENT '阅读数',
                                     `sort` int(10) NOT NULL DEFAULT '99' COMMENT '排序（正序）',
                                     `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                     `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                     PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='专栏表';

DROP TABLE IF EXISTS `t_content_img`;
CREATE TABLE `t_content_img` (
                                 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                 `origin_url` varchar(1000) NOT NULL DEFAULT '' COMMENT '图片原地址',
                                 `local_url` varchar(1000) NOT NULL DEFAULT '' COMMENT '图片本地地址（图片缓存到本地，加速用）',
                                 `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                 `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                 `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                 PRIMARY KEY (`id`),
                                 KEY `idx_origin_url` (`origin_url`),
                                 KEY `idx_local_url` (`local_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='图片表';

DROP TABLE IF EXISTS `t_content_tag`;
CREATE TABLE `t_content_tag` (
                                 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                 `name` varchar(255) NOT NULL DEFAULT '' COMMENT '标签名称',
                                 `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '类型 {1：文章；}',
                                 `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                 `sort` int(10) NOT NULL DEFAULT '99' COMMENT '排序（正序）',
                                 `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                 `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                 PRIMARY KEY (`id`),
                                 KEY `idx_name` (`name`),
                                 KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='标签表';

DROP TABLE IF EXISTS `t_content_tag_relation`;
CREATE TABLE `t_content_tag_relation` (
                                 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                 `tag_id` int(10) NOT NULL DEFAULT '0' COMMENT '标签ID',
                                 `third_id` int(10) NOT NULL DEFAULT '0' COMMENT '第三方ID',
                                 `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '类型 {1：文章；}',
                                 `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                 `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                 `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                 PRIMARY KEY (`id`),
                                 KEY `idx_tag_id` (`tag_id`),
                                 KEY `idx_third_id` (`third_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='标签关系表';

DROP TABLE IF EXISTS `t_content_comment`;
CREATE TABLE `t_content_comment` (
                                     `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                     `account_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
                                     `third_id` int(10) NOT NULL DEFAULT '0' COMMENT '第三方ID（比如文章ID）',
                                     `third_type` int(10) NOT NULL DEFAULT '0' COMMENT '第三方类型 {1：文章；}',
                                     `reply_id` int(10) NOT NULL DEFAULT '0' COMMENT '回复的评论ID',
                                     `comment_id` int(10) NOT NULL DEFAULT '0' COMMENT '原评论ID（当 type = 2 时，该 ID = reply_id，也就是原评论的 ID；当 type = 3 时，该 ID = 原评论的 ID）',
                                     `content` text NOT NULL default '' comment '评论内容',
                                     `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：删除；1：正常；}',
                                     `audit` tinyint(4) NOT NULL DEFAULT '0' COMMENT '审核状态 {-1：违规；0：待审核；1：正常；}',
                                     `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '类型 {1：普通评论；2：回复；3：艾特回复；}',
                                     `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                     `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                     PRIMARY KEY (`id`),
                                     KEY `idx_account_id` (`account_id`),
                                     KEY `idx_third_id` (`third_id`),
                                     KEY `idx_reply_id` (`reply_id`),
                                     KEY `idx_comment_id` (`comment_id`),
                                     KEY `idx_ctime` (`ctime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='评论表';

# user 数据库

CREATE DATABASE user DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

use user;

DROP TABLE IF EXISTS `t_user_account`;
CREATE TABLE `t_user_account` (
                                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
                                  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
                                  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
                                  `avatar_img_id` int(10) NOT NULL DEFAULT '0' COMMENT '头像图片ID',
                                  `password` varchar(50) NOT NULL DEFAULT '' COMMENT '密码',
                                  `access_token` varchar(50) NOT NULL DEFAULT '' COMMENT 'Token',
                                  `access_token_expire` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT 'Token过期时间',
                                  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态 {-1：禁用；1：正常；}',
                                  `mark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
                                  `last_active_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00' COMMENT '最近活跃时间',
                                  `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                  PRIMARY KEY (`id`),
                                  KEY `idx_nickname` (`nickname`),
                                  KEY `idx_email` (`email`),
                                  KEY `idx_mobile` (`mobile`),
                                  KEY `idx_password` (`password`),
                                  KEY `idx_mark` (`mark`),
                                  KEY `idx_access_token` (`access_token`),
                                  KEY `idx_last_active_time` (`last_active_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

DROP TABLE IF EXISTS `t_user_oauth`;
CREATE TABLE `t_user_oauth` (
                                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                `account_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
                                `oauth_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '第三方登录类型 {1：GitHub；2：QQ 登录；}',
                                `oauth_id` varchar(255) NOT NULL DEFAULT '' COMMENT '第三方登录ID',
                                `token` varchar(255) NOT NULL DEFAULT '' COMMENT '密码凭证',
                                `avatar_img_id`int(10) NOT NULL DEFAULT '0' COMMENT '头像图片ID',
                                `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                PRIMARY KEY (`id`),
                                KEY `idx_account_id` (`account_id`),
                                KEY `idx_oauth_type` (`oauth_type`),
                                KEY `idx_oauth_id` (`oauth_id`),
                                KEY `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='第三方登录表';

DROP TABLE IF EXISTS `t_user_action_log`;
CREATE TABLE `t_user_action_log` (
                                     `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
                                     `account_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID（0 为游客）',
                                     `third_id` int(10) NOT NULL DEFAULT '0' COMMENT '第三方ID（比如文章ID）',
                                     `type` int(10) NOT NULL DEFAULT '0' COMMENT '行为类型',
                                     `snapshot` varchar(2000) NOT NULL DEFAULT '' COMMENT '快照内容',
                                     `ip` varchar(50) NOT NULL DEFAULT '' COMMENT '客户端IP地址',
                                     `addr` varchar(500) NOT NULL DEFAULT '' COMMENT '客户端IP对应的地址',
                                     `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
                                     `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                                     PRIMARY KEY (`id`),
                                     KEY `idx_account_id` (`account_id`),
                                     KEY `idx_third_id` (`third_id`),
                                     KEY `idx_type` (`type`),
                                     KEY `idx_ip` (`ip`),
                                     KEY `idx_addr` (`addr`),
                                     KEY `idx_ctime` (`ctime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户行为日志表';