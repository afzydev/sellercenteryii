-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2015 at 12:52 PM
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
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `route` varchar(256) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `data` text,
  `icon` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `parent`, `route`, `order`, `data`, `icon`) VALUES
(1, 'Admin', NULL, '/admin/assignment/index', 0, NULL, 'fa fa-user'),
(2, 'Roles', 1, '/admin/role/index', NULL, NULL, 'fa fa-circle-o'),
(3, 'Assignment', 1, '/admin/assignment/index', NULL, NULL, 'fa fa-circle-o'),
(4, 'Routes', 1, '/admin/route/index', NULL, NULL, 'fa fa-circle-o'),
(5, 'Rules', 1, '/admin/rule/index', NULL, NULL, 'fa fa-circle-o'),
(6, 'Menu', 1, '/admin/menu/index', NULL, NULL, 'fa fa-circle-o'),
(7, 'User Manage', 1, '/user/index', NULL, NULL, 'fa fa-circle-o'),
(8, 'Gii', NULL, '/gii/default/index', 1, NULL, 'fa fa-laptop'),
(9, 'Debug', NULL, '/debug/default/index', 2, NULL, 'fa fa-bug'),
(10, 'Permission', 1, '/admin/permission/index', NULL, NULL, 'fa fa-circle-o');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `menu` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
