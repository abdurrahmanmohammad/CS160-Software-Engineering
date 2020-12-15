-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 23, 2020 at 02:42 AM
-- Server version: 5.7.30
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `OSD_Database`
--

-- --------------------------------------------------------

--
-- Table structure for table `Accounts`
--

CREATE TABLE `Accounts` (
  `email` varchar(50) NOT NULL,
  `password` char(60) NOT NULL,
  `account_type` varchar(10) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `phone` char(10) NOT NULL,
  `address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Accounts`
--

INSERT INTO `Accounts` (`email`, `password`, `account_type`, `firstname`, `lastname`, `phone`, `address`) VALUES
('admin@admin.com', '$2y$10$yHviNODBzjL6sswMbNy4NeuJSm1uZ6cpuvLjWWOsojEZzAosq2I9q', 'admin', 'admin', 'admin', 'admin', 'admin'),
('customer@customer.com', '$2y$10$3cn222OJ.GxEh8iML0F.D.IJnUIPZkCan.LvRfWKTB2sZv8ZOg76O', 'customer', 'Customer', 'Customer', '1010101012', '929 Alegre Place San Jose CA 95126 United States');

-- --------------------------------------------------------

--
-- Table structure for table `Carts`
--

CREATE TABLE `Carts` (
  `email` varchar(50) NOT NULL,
  `itemID` char(4) NOT NULL,
  `multiplicity` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `itemID` char(4) NOT NULL,
  `category` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Categories`
--

INSERT INTO `Categories` (`itemID`, `category`) VALUES
('1010', 'Notebooks'),
('1010', 'Paper products'),
('1111', 'Desk supplies'),
('1234', 'Desk supplies'),
('1234', 'Paper products'),
('2222', 'Desk supplies'),
('2949', 'Paper products'),
('2949', 'Printing supplies'),
('3333', 'Desk supplies'),
('3888', 'Printing supplies'),
('3944', 'Printing supplies'),
('4444', 'Desk supplies'),
('4828', 'Printing supplies'),
('5555', 'Desk supplies'),
('6666', 'Desk supplies'),
('7777', 'Desk supplies'),
('8888', 'Desk supplies'),
('9492', 'Printing supplies'),
('9999', 'Notebooks'),
('9999', 'Paper products');

-- --------------------------------------------------------

--
-- Table structure for table `Inventory`
--

CREATE TABLE `Inventory` (
  `itemID` char(4) NOT NULL,
  `warehouse` char(1) NOT NULL,
  `quantity` smallint(6) NOT NULL DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Inventory`
--

INSERT INTO `Inventory` (`itemID`, `warehouse`, `quantity`, `last_update`) VALUES
('1010', 'A', 18, '2020-11-23 01:46:13'),
('1010', 'B', 20, '2020-11-23 01:33:34'),
('1111', 'A', 19, '2020-11-23 01:47:33'),
('1111', 'B', 20, '2020-11-23 01:21:30'),
('1234', 'A', 20, '2020-11-23 01:34:51'),
('1234', 'B', 20, '2020-11-23 01:34:51'),
('2222', 'A', 20, '2020-11-23 01:23:38'),
('2222', 'B', 20, '2020-11-23 01:23:38'),
('2949', 'A', 12, '2020-11-23 02:08:30'),
('2949', 'B', 20, '2020-11-23 01:36:13'),
('3333', 'A', 20, '2020-11-23 01:27:01'),
('3333', 'B', 20, '2020-11-23 01:27:01'),
('3888', 'A', 19, '2020-11-23 01:46:13'),
('3888', 'B', 20, '2020-11-23 01:40:28'),
('3944', 'A', 17, '2020-11-23 01:46:13'),
('3944', 'B', 20, '2020-11-23 01:38:59'),
('4444', 'A', 20, '2020-11-23 01:28:00'),
('4444', 'B', 20, '2020-11-23 01:28:00'),
('4828', 'A', 20, '2020-11-23 01:39:24'),
('4828', 'B', 20, '2020-11-23 01:39:24'),
('5555', 'A', 20, '2020-11-23 01:28:35'),
('5555', 'B', 20, '2020-11-23 01:28:35'),
('6666', 'A', 20, '2020-11-23 01:29:57'),
('6666', 'B', 20, '2020-11-23 01:29:57'),
('7777', 'A', 20, '2020-11-23 01:30:39'),
('7777', 'B', 20, '2020-11-23 01:30:39'),
('8888', 'A', 20, '2020-11-23 01:31:28'),
('8888', 'B', 20, '2020-11-23 01:31:28'),
('9492', 'A', 20, '2020-11-23 01:37:45'),
('9492', 'B', 20, '2020-11-23 01:37:45'),
('9999', 'A', 20, '2020-11-23 01:32:28'),
('9999', 'B', 20, '2020-11-23 01:32:28');

-- --------------------------------------------------------

--
-- Table structure for table `Items`
--

CREATE TABLE `Items` (
  `itemID` char(4) NOT NULL,
  `title` varchar(50) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `weight` decimal(8,2) NOT NULL,
  `description` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Items`
--

INSERT INTO `Items` (`itemID`, `title`, `price`, `weight`, `description`) VALUES
('1010', 'Composition Notebook', '3.44', '8.34', 'High quality, highly durable composition notebook for sale perfect for daily office and home use!'),
('1111', 'Pens', '2.50', '4.35', 'High quality, highly durable pens for sale perfect for daily office and home use!'),
('1234', 'Post Its', '2.33', '2.43', 'High quality, highly durable post its for sale perfect for daily office and home use!'),
('2222', 'Pencils', '1.34', '3.99', 'High quality, highly durable pencils for sale perfect for daily office and home use!'),
('2949', 'Printer Paper', '16.43', '8.45', 'High quality, highly durable printer paper for sale perfect for daily office and home use!'),
('3333', 'Expo Markers', '2.34', '9.23', 'High quality, highly durable Expo markers for sale perfect for daily office and home use!'),
('3888', 'Magenta Ink Cartridge', '0.23', '19.33', 'High quality, highly durable magenta ink for sale perfect for daily office and home use!'),
('3944', 'Cyan Ink Cartridge', '0.23', '23.33', 'High quality, highly durable cyan ink for sale perfect for daily office and home use!'),
('4444', 'Highlighters', '3.54', '12.34', 'High quality, highly durable highlighters for sale perfect for daily office and home use!'),
('4828', 'Yellow Ink Cartridge', '0.23', '23.45', 'Yellow Ink Cartridge'),
('5555', 'Paper clips', '1.23', '3.45', 'High quality, highly durable paper clips for sale perfect for daily office and home use!'),
('6666', 'Scotch Tape', '2.34', '1.22', 'High quality, highly durable tape for sale perfect for daily office and home use!'),
('7777', 'Rubber bands', '0.34', '2.33', 'High quality, highly durable rubber bands for sale perfect for daily office and home use!'),
('8888', 'Erasers', '2.34', '4.55', 'High quality, highly durable erasers for sale perfect for daily office and home use!'),
('9492', 'Black Ink Cartridge', '0.23', '23.33', 'High quality, highly durable black ink for sale perfect for daily office and home use!'),
('9999', 'Spiral notebooks', '4.55', '15.76', 'High quality, highly durable spiral notebooks for sale perfect for daily office and home use!');

-- --------------------------------------------------------

--
-- Table structure for table `Orders`
--

CREATE TABLE `Orders` (
  `orderID` char(8) NOT NULL,
  `email` varchar(50) NOT NULL,
  `grand_total` decimal(8,2) NOT NULL,
  `order_total` decimal(8,2) NOT NULL,
  `shipping_cost` decimal(8,2) NOT NULL,
  `order_weight` decimal(8,2) NOT NULL,
  `shipping_option` varchar(8) NOT NULL,
  `address` varchar(100) NOT NULL,
  `date_placed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delivered` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Orders`
--

INSERT INTO `Orders` (`orderID`, `email`, `grand_total`, `order_total`, `shipping_cost`, `order_weight`, `shipping_option`, `address`, `date_placed`, `delivered`) VALUES
('GA0ydtYw', 'customer@customer.com', '27.80', '7.80', '20.00', '106.00', 'Option4', '333 Sunol Street San Jose CA 95126 United States', '2020-11-23 02:20:31', 0),
('U27MIk3t', 'customer@customer.com', '22.50', '2.50', '20.00', '4.35', 'Option3', '2211 North 1st Street San Jose CA 95131 United States', '2020-11-23 02:20:34', 0),
('wcCGGxr1', 'customer@customer.com', '131.44', '131.44', '0.00', '67.60', 'Option1', '344 Tully Road San Jose CA 95111 United States', '2020-11-23 02:20:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `Pictures`
--

CREATE TABLE `Pictures` (
  `itemID` char(4) NOT NULL,
  `directory` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Pictures`
--

INSERT INTO `Pictures` (`itemID`, `directory`) VALUES
('1010', '../Pictures/Composition Notebook1010(0).jpg'),
('1010', '../Pictures/Composition Notebook1010(1).jpg'),
('1111', '../Pictures/Pens1111(0).jpg'),
('1111', '../Pictures/Pens1111(1).jpg'),
('1111', '../Pictures/Pens1111(2).jpg'),
('1234', '../Pictures/Post Its1234(0).jpg'),
('1234', '../Pictures/Post Its1234(1).jpg'),
('2222', '../Pictures/Pencils2222(0).jpg'),
('2222', '../Pictures/Pencils2222(1).jpg'),
('2222', '../Pictures/Pencils2222(2).jpg'),
('2222', '../Pictures/Pencils2222(3).jpg'),
('2949', '../Pictures/Printer Paper2949(0).jpg'),
('2949', '../Pictures/Printer Paper2949(1).jpg'),
('2949', '../Pictures/Printer Paper2949(2).jpg'),
('3333', '../Pictures/Expo Markers3333(0).jpg'),
('3333', '../Pictures/Expo Markers3333(1).jpg'),
('3888', '../Pictures/Magenta Ink Cartridge3888(0).jpg'),
('3888', '../Pictures/Magenta Ink Cartridge3888(1).jpg'),
('3944', '../Pictures/Cyan Ink Cartridge3944(0).jpg'),
('4444', '../Pictures/Highlighters4444(0).jpg'),
('4828', '../Pictures/Yellow Ink Cartridge4828(0).jpg'),
('4828', '../Pictures/Yellow Ink Cartridge4828(1).jpg'),
('5555', '../Pictures/Paper clips5555(0).jpg'),
('5555', '../Pictures/Paper clips5555(1).jpg'),
('5555', '../Pictures/Paper clips5555(2).jpg'),
('6666', '../Pictures/Scotch Tape6666(0).jpg'),
('7777', '../Pictures/Rubber bands7777(0).jpg'),
('7777', '../Pictures/Rubber bands7777(1).jpg'),
('7777', '../Pictures/Rubber bands7777(2).jpg'),
('8888', '../Pictures/Erasers8888(0).jpg'),
('9492', '../Pictures/Black Ink Cartridge9492(0).jpg'),
('9492', '../Pictures/Black Ink Cartridge9492(1).jpg'),
('9999', '../Pictures/Spiral notebooks9999(0).jpg'),
('9999', '../Pictures/Spiral notebooks9999(1).jpg');

-- --------------------------------------------------------

--
-- Table structure for table `Purchases`
--

CREATE TABLE `Purchases` (
  `orderID` char(8) NOT NULL,
  `itemID` char(4) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `multiplicity` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Purchases`
--

INSERT INTO `Purchases` (`orderID`, `itemID`, `price`, `multiplicity`) VALUES
('GA0ydtYw', '1010', '3.44', 2),
('GA0ydtYw', '3888', '0.23', 1),
('GA0ydtYw', '3944', '0.23', 3),
('U27MIk3t', '1111', '2.50', 1),
('wcCGGxr1', '2949', '16.43', 8);

-- --------------------------------------------------------

--
-- Table structure for table `Transactions`
--

CREATE TABLE `Transactions` (
  `orderID` char(8) NOT NULL,
  `order_total` smallint(6) NOT NULL,
  `card_holder` varchar(50) NOT NULL,
  `credit_card` varchar(16) NOT NULL,
  `card_month` char(2) NOT NULL,
  `card_year` char(2) NOT NULL,
  `cvc` char(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Transactions`
--

INSERT INTO `Transactions` (`orderID`, `order_total`, `card_holder`, `credit_card`, `card_month`, `card_year`, `cvc`) VALUES
('GA0ydtYw', 28, 'Customer', '2929292929292929', '12', '22', '222'),
('U27MIk3t', 22, 'Customer', '2892982892982892', '01', '22', '222'),
('wcCGGxr1', 131, 'Customer', '1992290290290290', '04', '23', '333');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Accounts`
--
ALTER TABLE `Accounts`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `Carts`
--
ALTER TABLE `Carts`
  ADD PRIMARY KEY (`email`,`itemID`);

--
-- Indexes for table `Categories`
--
ALTER TABLE `Categories`
  ADD PRIMARY KEY (`itemID`,`category`);

--
-- Indexes for table `Inventory`
--
ALTER TABLE `Inventory`
  ADD PRIMARY KEY (`itemID`,`warehouse`);

--
-- Indexes for table `Items`
--
ALTER TABLE `Items`
  ADD PRIMARY KEY (`itemID`);

--
-- Indexes for table `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`orderID`);

--
-- Indexes for table `Pictures`
--
ALTER TABLE `Pictures`
  ADD PRIMARY KEY (`itemID`,`directory`);

--
-- Indexes for table `Purchases`
--
ALTER TABLE `Purchases`
  ADD PRIMARY KEY (`orderID`,`itemID`);

--
-- Indexes for table `Transactions`
--
ALTER TABLE `Transactions`
  ADD PRIMARY KEY (`orderID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
