-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 09:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_sys`
--

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `idno` int(11) NOT NULL,
  `ProjectName` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Groups` varchar(255) NOT NULL,
  `Department` varchar(255) NOT NULL,
  `Division` varchar(255) NOT NULL,
  `Vendor` varchar(255) NOT NULL,
  `ContractDate` varchar(255) NOT NULL,
  `EndDate` varchar(255) NOT NULL,
  `File` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `details`
--

CREATE TABLE `details` (
  `idno` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `Role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `details`
--

INSERT INTO `details` (`idno`, `username`, `password`, `Role`) VALUES
(1, 'hans', 'sayco', 'admin'),
(2, 'wew', 'wew', 'user'),
(3, 'hi', 'hello', 'user'),
(4, 'happy', 'day', 'user'),
(5, '', '', 'user'),
(6, 'hellow', 'wew', 'user'),
(7, '', '', 'user'),
(8, '', '', 'user'),
(9, 'asdasd', 'wew', 'user'),
(10, 'cjay', 'camposagrado', 'inventory manager');

-- --------------------------------------------------------

--
-- Table structure for table `peripherals`
--

CREATE TABLE `peripherals` (
  `Idno` int(255) NOT NULL,
  `Serial_num` varchar(255) NOT NULL,
  `Host` varchar(255) NOT NULL,
  `Model_num` varchar(255) NOT NULL,
  `Brand` varchar(255) NOT NULL,
  `Assignee_name` varchar(255) NOT NULL,
  `Groups` varchar(255) NOT NULL,
  `Department` varchar(255) NOT NULL,
  `Division` varchar(255) NOT NULL,
  `Position` varchar(255) NOT NULL,
  `Date` varchar(255) NOT NULL,
  `Status` varchar(255) NOT NULL,
  `Item` varchar(255) NOT NULL,
  `Mouse_SN` varchar(255) NOT NULL,
  `Adapter_SN` varchar(255) NOT NULL,
  `File` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `software`
--

CREATE TABLE `software` (
  `idno` int(255) NOT NULL,
  `Assignee_name` varchar(255) NOT NULL,
  `Serial_num` varchar(255) NOT NULL,
  `expiry` varchar(255) NOT NULL,
  `license` varchar(255) NOT NULL,
  `suppliers` varchar(255) NOT NULL,
  `receivers` varchar(255) NOT NULL,
  `software` varchar(255) NOT NULL,
  `groups` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `division` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `File` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`idno`);

--
-- Indexes for table `details`
--
ALTER TABLE `details`
  ADD PRIMARY KEY (`idno`);

--
-- Indexes for table `peripherals`
--
ALTER TABLE `peripherals`
  ADD PRIMARY KEY (`Idno`);

--
-- Indexes for table `software`
--
ALTER TABLE `software`
  ADD PRIMARY KEY (`idno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `idno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `details`
--
ALTER TABLE `details`
  MODIFY `idno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `peripherals`
--
ALTER TABLE `peripherals`
  MODIFY `Idno` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `software`
--
ALTER TABLE `software`
  MODIFY `idno` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
