-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2020 at 08:13 AM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 5.6.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sample`
--

-- --------------------------------------------------------

--
-- Table structure for table `companyprofile`
--

CREATE TABLE `companyprofile` (
  `id` varchar(255) NOT NULL,
  `dateedited` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cName` varchar(255) DEFAULT NULL,
  `cUsername` varchar(255) DEFAULT NULL,
  `sicon` varchar(250) DEFAULT NULL,
  `cEmailid` varchar(255) DEFAULT NULL,
  `cNumber` bigint(150) DEFAULT NULL,
  `curl` varchar(200) DEFAULT NULL,
  `cLocation` varchar(255) DEFAULT NULL,
  `cStage` varchar(255) DEFAULT NULL,
  `cBusiness` varchar(255) DEFAULT NULL,
  `cIndustry` varchar(255) DEFAULT NULL,
  `csy` int(20) DEFAULT NULL,
  `cVision` text NOT NULL,
  `cMission` text,
  `oname` varchar(255) DEFAULT NULL,
  `yexp` int(11) DEFAULT NULL,
  `mexp` int(11) DEFAULT NULL,
  `omoney` int(11) DEFAULT NULL,
  `prefcur` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `companyprofile`
--

INSERT INTO `companyprofile` (`id`, `dateedited`, `cName`, `cUsername`, `sicon`, `cEmailid`, `cNumber`, `curl`, `cLocation`, `cStage`, `cBusiness`, `cIndustry`, `csy`, `cVision`, `cMission`, `oname`, `yexp`, `mexp`, `omoney`, `prefcur`) VALUES
('temulo', '2020-01-06 09:29:26', 'ABC Advisor Private Limited', 'John Doe', 'https://d39mpljf7pwm48.cloudfront.net/icon/logo.png', 'John@abc.in', 9876543210, 'www.abc.in', 'Kalyansberg', 'Prototype Ready', 'Services', 'Real Estate', 2005, '\"To transform the Real estate industry by empowering and delivering for positive change and innovation that rediscovers the quality of services. To have a customer centric approach with highest level of satisfaction coupled with pro-active and', '\"To transform the Real estate industry by empowering and delivering for positive change and innovation that rediscovers the quality of services. To have a customer centric approach with highest level of satisfaction coupled with pro-active and', 'Amit Choudhury', 10, 5, 4000000, 'Rs');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `name` varchar(20) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `pass` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`name`, `contact`, `email`, `pass`) VALUES
('Gaurav Singh', '8898292895', 'gaurav20297@gmail.com', 'guru'),
('Manoj Singh', '8898292893', 'manoj12@gmail.com', 'manoj'),
('saurabh singh', '8898292894', 'saurabh@gmail.com', 'sau'),
('saurabh singh', '8898292892', 'manoj@gmail.com', 'abc');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companyprofile`
--
ALTER TABLE `companyprofile`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
