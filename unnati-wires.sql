-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025 at 01:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `unnati-wires`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `user_roll` varchar(50) NOT NULL,
  `salt` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`email`, `password`, `user_type`, `user_roll`, `salt`) VALUES
('admin@unnati.com', '366252a58be7185b1aa036e4a40f28e6cbbecc0e49bc8ac4e9a54a5c0211cef8', 'Admin', 'owner', '8c1cf323382d2c2e7a90218fe17d1810'),
('factory@unnati.com', '2018145d9952641d911298a538e888369a0693bbb9b864df8d0a959e0cb56220', 'Factory', 'owner', 'd6c44ef35c3938962752282e8649881f'),
('store@unnati.com', 'd1a2bfa517f8a05cac3c679aee130034113ce74d3e8ab652536a29b97e9a98b6', 'Store', 'owner', 'a4e542c5c0a5c1d313d112ccc364bc25'),
('vendor@unnati.com', '5cb2db83125d45af59746f7fae535411a727b12b9c255b347ba5407fd85903c3', 'Vendor', 'owner', 'bc1bd02f21cc2fa4694623a17a411ea7');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
