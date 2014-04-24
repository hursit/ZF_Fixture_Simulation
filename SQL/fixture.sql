-- phpMyAdmin SQL Dump
-- version 4.1.13
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 24, 2014 at 11:25 PM
-- Server version: 5.5.35-0ubuntu0.12.04.2
-- PHP Version: 5.3.10-1ubuntu3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `is`
--
CREATE DATABASE IF NOT EXISTS `fixture` DEFAULT CHARACTER SET utf8 COLLATE utf8_turkish_ci;
USE `fixture`;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_team_id` int(11) NOT NULL,
  `second_team_id` int(11) NOT NULL,
  `first_team_goal` int(11) NOT NULL,
  `second_team_goal` int(11) NOT NULL,
  `status` varchar(10) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'not play',
  `week` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=133 ;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `first_team_id`, `second_team_id`, `first_team_goal`, `second_team_goal`, `status`, `week`) VALUES
(121, 14, 15, 1, 0, 'played', 1),
(122, 15, 14, 2, 1, 'played', 4),
(123, 13, 16, 2, 2, 'played', 1),
(124, 16, 13, 0, 1, 'played', 4),
(125, 14, 13, 4, 0, 'played', 2),
(126, 13, 14, 7, 4, 'played', 5),
(127, 15, 16, 2, 3, 'played', 2),
(128, 16, 15, 4, 1, 'played', 5),
(129, 14, 16, 1, 2, 'played', 3),
(130, 16, 14, 4, 2, 'played', 6),
(131, 15, 13, 2, 3, 'played', 3),
(132, 13, 15, 0, 1, 'played', 6);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(40) COLLATE utf8_turkish_ci NOT NULL,
  `password` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `email`, `password`) VALUES
(1, 'a@a.com', 'a');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE IF NOT EXISTS `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) COLLATE utf8_turkish_ci NOT NULL,
  `strength` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=17 ;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `strength`) VALUES
(13, 'FenerbahÃ§e', 80),
(14, 'Trabzonspor', 60),
(15, 'BeÅŸiktaÅŸ', 50),
(16, 'Rize Spor', 50);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
