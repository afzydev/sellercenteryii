-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2015 at 02:17 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `shop_oms`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_assignment`
--

CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('Admin', '1', 1450353347),
('Manager', '1', 1450353347),
('Super Admin', '1', 1450353347),
('User Management', '1', 1450353347),
('User Management', '2', 1450353356);

-- --------------------------------------------------------

--
-- Table structure for table `auth_item`
--

CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('/*', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/admin/*', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/assignment/*', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/assignment/assign', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/assignment/index', 2, NULL, NULL, NULL, 1450352656, 1450352656),
('/admin/assignment/search', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/assignment/view', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/default/*', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/default/index', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/menu/*', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/menu/create', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/menu/delete', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/menu/index', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/menu/update', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/menu/view', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/permission/*', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/permission/assign', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/permission/create', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/permission/delete', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/permission/index', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/permission/search', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/permission/update', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/permission/view', 2, NULL, NULL, NULL, 1450352657, 1450352657),
('/admin/role/*', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/role/assign', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/role/create', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/role/delete', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/role/index', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/role/search', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/role/update', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/role/view', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/route/*', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/route/assign', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/route/create', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/route/index', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/route/search', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/rule/*', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/rule/create', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/rule/delete', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/rule/index', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/rule/update', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/admin/rule/view', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/debug/*', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/debug/default/*', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/debug/default/db-explain', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/debug/default/download-mail', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/debug/default/index', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/debug/default/toolbar', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/debug/default/view', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/gii/*', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/gii/default/*', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/gii/default/action', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/gii/default/diff', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/gii/default/index', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/gii/default/preview', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/gii/default/view', 2, NULL, NULL, NULL, 1450352658, 1450352658),
('/site/*', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/site/error', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/site/index', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/site/login', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/site/logout', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/user/*', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/user/create', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/user/delete', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/user/index', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/user/update', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('/user/view', 2, NULL, NULL, NULL, 1450352659, 1450352659),
('Admin', 1, NULL, 'Admin Rule', NULL, 1450352597, 1450352855),
('Manager', 1, NULL, 'Manager Rule', NULL, 1450353024, 1450353024),
('Seller', 2, NULL, 'Seller Rule', NULL, 1450357350, 1450357350),
('Super Admin', 2, NULL, 'Super Admin Rule', NULL, 1450353233, 1450353503),
('User Management', 2, NULL, 'Manager Rule', NULL, 1450353082, 1450353325);

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_child`
--

CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('Super Admin', '/*'),
('Super Admin', '/admin/*'),
('Super Admin', '/admin/assignment/*'),
('Super Admin', '/admin/assignment/assign'),
('Super Admin', '/admin/assignment/index'),
('Super Admin', '/admin/assignment/search'),
('Super Admin', '/admin/assignment/view'),
('Super Admin', '/admin/default/*'),
('Super Admin', '/admin/default/index'),
('Super Admin', '/admin/menu/*'),
('Super Admin', '/admin/menu/create'),
('Super Admin', '/admin/menu/delete'),
('Super Admin', '/admin/menu/index'),
('Super Admin', '/admin/menu/update'),
('Super Admin', '/admin/menu/view'),
('Super Admin', '/admin/permission/*'),
('Super Admin', '/admin/permission/assign'),
('Super Admin', '/admin/permission/create'),
('Super Admin', '/admin/permission/delete'),
('Super Admin', '/admin/permission/index'),
('Super Admin', '/admin/permission/search'),
('Super Admin', '/admin/permission/update'),
('Super Admin', '/admin/permission/view'),
('Super Admin', '/admin/role/*'),
('Super Admin', '/admin/role/assign'),
('Super Admin', '/admin/role/create'),
('Super Admin', '/admin/role/delete'),
('Super Admin', '/admin/role/index'),
('Super Admin', '/admin/role/search'),
('Super Admin', '/admin/role/update'),
('Super Admin', '/admin/role/view'),
('Super Admin', '/admin/route/*'),
('Super Admin', '/admin/route/assign'),
('Super Admin', '/admin/route/create'),
('Super Admin', '/admin/route/index'),
('Super Admin', '/admin/route/search'),
('Super Admin', '/admin/rule/*'),
('Super Admin', '/admin/rule/create'),
('Super Admin', '/admin/rule/delete'),
('Super Admin', '/admin/rule/index'),
('Super Admin', '/admin/rule/update'),
('Super Admin', '/admin/rule/view'),
('Manager', '/debug/*'),
('Super Admin', '/debug/*'),
('User Management', '/debug/*'),
('Manager', '/debug/default/*'),
('Super Admin', '/debug/default/*'),
('User Management', '/debug/default/*'),
('Manager', '/debug/default/db-explain'),
('Super Admin', '/debug/default/db-explain'),
('User Management', '/debug/default/db-explain'),
('Manager', '/debug/default/download-mail'),
('Super Admin', '/debug/default/download-mail'),
('User Management', '/debug/default/download-mail'),
('Manager', '/debug/default/index'),
('Super Admin', '/debug/default/index'),
('User Management', '/debug/default/index'),
('Manager', '/debug/default/toolbar'),
('Super Admin', '/debug/default/toolbar'),
('User Management', '/debug/default/toolbar'),
('Manager', '/debug/default/view'),
('Super Admin', '/debug/default/view'),
('User Management', '/debug/default/view'),
('Manager', '/gii/*'),
('Super Admin', '/gii/*'),
('User Management', '/gii/*'),
('Manager', '/gii/default/*'),
('Super Admin', '/gii/default/*'),
('User Management', '/gii/default/*'),
('Manager', '/gii/default/action'),
('Super Admin', '/gii/default/action'),
('User Management', '/gii/default/action'),
('Manager', '/gii/default/diff'),
('Super Admin', '/gii/default/diff'),
('User Management', '/gii/default/diff'),
('Manager', '/gii/default/index'),
('Super Admin', '/gii/default/index'),
('User Management', '/gii/default/index'),
('Manager', '/gii/default/preview'),
('Super Admin', '/gii/default/preview'),
('User Management', '/gii/default/preview'),
('Manager', '/gii/default/view'),
('Super Admin', '/gii/default/view'),
('User Management', '/gii/default/view'),
('Manager', '/site/*'),
('Seller', '/site/*'),
('Super Admin', '/site/*'),
('User Management', '/site/*'),
('Manager', '/site/error'),
('Seller', '/site/error'),
('Super Admin', '/site/error'),
('User Management', '/site/error'),
('Manager', '/site/index'),
('Seller', '/site/index'),
('Super Admin', '/site/index'),
('User Management', '/site/index'),
('Manager', '/site/login'),
('Seller', '/site/login'),
('Super Admin', '/site/login'),
('User Management', '/site/login'),
('Manager', '/site/logout'),
('Seller', '/site/logout'),
('Super Admin', '/site/logout'),
('User Management', '/site/logout'),
('Manager', '/user/*'),
('Super Admin', '/user/*'),
('User Management', '/user/*'),
('Manager', '/user/create'),
('Super Admin', '/user/create'),
('User Management', '/user/create'),
('Manager', '/user/delete'),
('Super Admin', '/user/delete'),
('User Management', '/user/delete'),
('Manager', '/user/index'),
('Super Admin', '/user/index'),
('User Management', '/user/index'),
('Manager', '/user/update'),
('Super Admin', '/user/update'),
('User Management', '/user/update'),
('Manager', '/user/view'),
('Super Admin', '/user/view'),
('User Management', '/user/view'),
('Super Admin', 'User Management');

-- --------------------------------------------------------

--
-- Table structure for table `auth_rule`
--

CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `auth_rule`
--

INSERT INTO `auth_rule` (`name`, `data`, `created_at`, `updated_at`) VALUES
('Admin Rule', 'O:19:"app\\rules\\AdminRule":3:{s:4:"name";s:10:"Admin Rule";s:9:"createdAt";i:1450352534;s:9:"updatedAt";i:1450352534;}', 1450352534, 1450352534),
('Manager Rule', 'O:21:"app\\rules\\ManagerRule":3:{s:4:"name";s:12:"Manager Rule";s:9:"createdAt";i:1450353007;s:9:"updatedAt";i:1450353007;}', 1450353007, 1450353007),
('Seller Rule', 'O:20:"app\\rules\\SellerRule":3:{s:4:"name";s:11:"Seller Rule";s:9:"createdAt";i:1450357305;s:9:"updatedAt";i:1450357305;}', 1450357305, 1450357305),
('Super Admin Rule', 'O:24:"app\\rules\\SuperAdminRule":3:{s:4:"name";s:16:"Super Admin Rule";s:9:"createdAt";i:1450353172;s:9:"updatedAt";i:1450353172;}', 1450353172, 1450353172);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `route` varchar(256) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `parent`, `route`, `order`, `data`) VALUES
(1, 'Admin', NULL, '/admin/assignment/index', 0, NULL),
(2, 'Roles', 1, '/admin/role/index', NULL, NULL),
(3, 'Assignment', 1, '/admin/assignment/index', NULL, NULL),
(4, 'Routes', 1, '/admin/route/index', NULL, NULL),
(5, 'Rules', 1, '/admin/rule/index', NULL, NULL),
(6, 'Menu', 1, '/admin/menu/index', NULL, NULL),
(7, 'User Manage', 1, '/user/index', NULL, NULL),
(8, 'Gii', NULL, '/gii/default/index', 1, NULL),
(9, 'Debug', NULL, '/debug/default/index', 2, NULL),
(10, 'Permission', 1, '/admin/permission/index', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1450345151),
('m130524_201442_init', 1450345154),
('m140506_102106_rbac_init', 1450352131),
('m140602_111327_create_menu_table', 1450352044);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'nTq8nI-MFQmZahBimlNOC2I_4DugYCEK', '$2y$13$4eoYQIlXkPjrn7GSv3Ueue4IfOI0ZldcX4.EA.auiM7SIKxyok4ba', NULL, 'jitendra.kumar@girnarsoft.com', 10, 0, 2015),
(2, 'test', 'lihqJQ9OjBjDb757GQ77bjWc8J-OH8Az', '$2y$13$G6pAtXwwsrcIPUACx41V6eTGqonu0SrYvqauA./kMoN9TsJwxb3/u', 'qexg-abrHUVxYA92xV_H8mDKauFsLh-5_1424243975', 'test@girnarsoft.com', 10, 0, 2015),
(3, 'Preet', 'KUdyINtnxjKhVzDf4Soi8Ch9NwS5Vy3f', '$2y$13$6uijI8aJxH0MjEf7aU..ruupptwebXSVwPCBccG2xrx0jdAhO9Vyi', NULL, 'preet.saxena1@girnarsoft.com', 10, 2015, 2015);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `menu` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
