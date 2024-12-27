-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2021 at 06:10 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fpldata`
--

-- --------------------------------------------------------

--
-- Table structure for table `fpl_accounts`
--

CREATE TABLE `fpl_accounts` (
  `Account_no` varchar(20) NOT NULL,
  `Date_Received` date DEFAULT NULL,
  `Due_Date` date DEFAULT NULL,
  `Update_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fpl_accounts`
--

INSERT INTO `fpl_accounts` (`Account_no`, `Date_Received`, `Due_Date`, `Update_Date`) VALUES
('191498179', NULL, NULL, NULL),
('209754001', NULL, NULL, NULL),
('279177166', NULL, NULL, NULL),
('4989455375', NULL, NULL, NULL),
('7230929007', NULL, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
