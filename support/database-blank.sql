-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 12, 2008 at 04:29 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `mismgis`
--

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `name_1` varchar(255) default NULL,
  `name_2` varchar(255) default NULL,
  `address_1` varchar(255) default NULL,
  `address_2` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `state` varchar(255) default NULL,
  `zip` varchar(255) default NULL,
  `phone` varchar(255) default NULL,
  `chinese_phone` varchar(255) default NULL,
  `spanish_phone` varchar(255) default NULL,
  `other_phone` varchar(255) default NULL,
  `fax` varchar(255) default NULL,
  `website` varchar(255) default NULL,
  `contact_name` varchar(255) default NULL,
  `contact_email` varchar(255) default NULL,
  `description` text,
  `category_1` varchar(255) default NULL,
  `category_2` varchar(255) default NULL,
  `clubhouse_certified` varchar(255) default NULL,
  `clubhouse_training_base` varchar(255) default NULL,
  `id` int(11) NOT NULL auto_increment,
  `longitude` float(10,7) default NULL,
  `latitude` float(10,7) default NULL,
  `geocoding_accuracy` varchar(255) default NULL,
  `geocoding_status_code` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=257 ;
