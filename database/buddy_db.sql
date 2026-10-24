-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2026 at 07:39 PM
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
-- Database: `buddy_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `friend_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `friend_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address1` varchar(150) DEFAULT NULL,
  `address2` varchar(150) DEFAULT NULL,
  `address3` varchar(150) DEFAULT NULL,
  `address4` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`friend_id`, `user_id`, `photo`, `friend_name`, `gender`, `phone_no`, `email`, `address1`, `address2`, `address3`, `address4`) VALUES
(5001, 1002, 'friend5001.jpg', 'Daniel Raj', 'Male', '+6013-2345678', 'daniel.raj@email.com', 'No. 21', 'Jalan PJS 5/3', '46150 Petaling Jaya', 'Selangor'),
(5002, 1002, 'friend5002.jpg', 'Faizal Bin Kamal', 'Male', '+6014-4567890', 'faizal.kamal@email.com', 'No. 34', 'Jalan SS9/6', '47300 Petaling Jaya', 'Selangor'),
(5003, 1002, 'friend5003.jpg', 'Haniff Bin Salleh', 'Male', '+6016-0123456', 'haniff.salleh@email.com', 'No. 55', 'Jalan USJ 5/6', '47610 Subang Jaya', 'Selangor'),
(5004, 1004, 'friend5004.jpg', 'Rashid Bin Omar', 'Male', '+6013-4567890', 'rashid.omar@email.com', 'No. 8', 'Jalan Tropicana', '47400 Petaling Jaya', 'Selangor'),
(5005, 1004, 'friend5005.jpg', 'Rosli Bin Ahmad', 'Male', '+6014-0123456', 'rosli.ahmad@email.com', 'No. 5', 'Jalan SL 9/1', '43000 Bandar Sungai Long', 'Selangor'),
(5006, 1005, 'friend5006.jpg', 'Kenny Lim', 'Male', '+6013-0123456', 'kenny.lim@email.com', 'No. 56', 'Jalan SS21/62', '47400 Damansara Utama', 'Selangor'),
(5007, 1006, 'friend5007.jpg', 'Izham Bin Musa', 'Male', '+6013-4567890', 'izham.musa@email.com', 'No. 78', 'Jalan SS7/1', '47301 Kelana Jaya', 'Selangor'),
(5008, 1006, 'friend5008.jpg', 'Grace Wong', 'Female', '+6014-7890123', 'grace.wong@email.com', 'Damai Service Apartment', 'Wangsa Maju', '53300 Wangsa Maju', 'Kuala Lumpur'),
(5009, 1006, 'friend5009.jpg', 'Priya A/P Kumar', 'Female', '+6012-7890123', 'priya.kumar@email.com', 'Taman Desa Utama', 'Ampang', '68000 Ampang', 'Selangor'),
(5010, 1008, 'friend5010.jpg', 'Fazrul Bin Hashim', 'Male', '+6012-8901234', 'fazrul.hashim@email.com', 'No. 33', 'Jalan USJ 2/1', '47600 Subang Jaya', 'Selangor'),
(5011, 1008, 'friend5011.jpg', 'Shamir Bin Bakar', 'Male', '+6015-2345678', 'shamir.bakar@email.com', 'No. 29', 'Jalan SS2/55', '47300 Petaling Jaya', 'Selangor'),
(5012, 1009, 'friend5012.jpg', 'Lily Tan', 'Female', '+6013-9012345', 'lily.tan@email.com', 'Parc Regency', 'Seputeh', '58200 Seputeh', 'Kuala Lumpur'),
(5013, 1010, 'friend5013.jpg', 'Adam Bin Malik', 'Male', '+6012-3456789', 'adam.malik@email.com', 'No. 12', 'Jalan SS2/72', '47300 Petaling Jaya', 'Selangor'),
(5014, 1010, 'friend5014.jpg', 'Samantha Tan', 'Female', '+6016-1234567', 'samantha.tan@email.com', 'The Oval Residence', 'Setia Alam', '40170 Setia Alam', 'Selangor'),
(5015, 1010, 'friend5015.jpg', 'James Chua', 'Male', '+6013-8901234', 'james.chua@email.com', 'No. 92', 'Jalan SS20/10', '47400 Damansara Kim', 'Selangor'),
(5016, 1012, 'friend5016.jpg', 'Wong Chee Seng', 'Male', '+6014-6789012', 'wong.seng@email.com', 'No. 19', 'Jalan TTDI 3/2', '60000 Taman Tun Dr Ismail', 'Kuala Lumpur'),
(5017, 1012, 'friend5017.jpg', 'Syed Hussein', 'Male', '+6014-6789013', 'syed.hussein@email.com', 'No. 20', 'Jalan TTDI 3/3', '60000 Taman Tun Dr Ismail', 'Kuala Lumpur'),
(5018, 1013, 'friend5018.jpg', 'Yasmin Binti Fadzil', 'Female', '+6013-7890123', 'yasmin.fadzil@email.com', 'Sri Hartamas Condo', 'Mont Kiara', '50480 Mont Kiara', 'Kuala Lumpur'),
(5019, 1014, 'friend5019.jpg', 'Sophia Tan', 'Female', '+6013-3456789', 'sophia.tan@email.com', 'Residensi Prima', 'Setapak', '53000 Setapak', 'Kuala Lumpur'),
(5020, 1014, 'friend5020.jpg', 'Azhar Bin Ghani', 'Male', '+6014-8901234', 'azhar.ghani@email.com', 'No. 7', 'Jalan PJU 8/3', '47820 Damansara Perdana', 'Selangor'),
(5021, 1014, 'friend5021.jpg', 'Nurul Izzah', 'Female', '+6014-9012345', 'nurul.izzah@email.com', 'Sunway Residence', 'Sunway', '47500 Sunway', 'Selangor'),
(5022, 1015, 'friend5022.jpg', 'Emma Wong', 'Female', '+6012-9876543', 'emma.wong@email.com', 'Block A-3-5, Condo Mutiara', 'Bukit Jalil', '57000 Bukit Jalil', 'Kuala Lumpur'),
(5023, 1015, 'friend5023.jpg', 'Aina Binti Yusof', 'Female', '+6014-3456789', 'aina.yusof@email.com', 'Kiara Residence', 'Taman Tun Dr Ismail', '60000 Taman Tun Dr Ismail', 'Kuala Lumpur'),
(5024, 1017, 'friend5024.jpg', 'Maya A/P Krishnan', 'Female', '+6014-1234567', 'maya.krishnan@email.com', 'Suria Residence', 'Cheras', '56000 Cheras', 'Kuala Lumpur'),
(5025, 1018, 'friend5025.jpg', 'Mei Ling Tan', 'Female', '+6012-5678901', 'meiling.tan@email.com', 'Apartment Sri Cempaka', 'Cheras', '56000 Cheras', 'Kuala Lumpur'),
(5026, 1018, 'friend5026.jpg', 'Farid Bin Azman', 'Male', '+6015-6789012', 'farid.azman@email.com', 'No. 88', 'Jalan Setia 2/1', '43000 Taman Setia Indah', 'Selangor'),
(5027, 1019, 'friend5027.jpg', 'Zara A/P Ravi', 'Female', '+6013-1234567', 'zara.ravi@email.com', 'Taman Midah', 'Cheras', '56000 Cheras', 'Kuala Lumpur'),
(5028, 1019, 'friend5028.jpg', 'Rachel Lim', 'Female', '+6014-5678901', 'rachel.lim@email.com', 'Palace Court', 'Bukit Bintang', '55100 Bukit Bintang', 'Kuala Lumpur'),
(5029, 1019, 'friend5029.jpg', 'Zhang Lurang', 'Male', '+6016-2223334', 'lurangzhao@email.com', 'Vista Residence', 'Ampang', '68000 Ampang', 'Selangor'),
(5030, 1020, 'friend5030.jpg', 'Jasmine Low', 'Female', '+6013-5678901', 'jasmine.low@email.com', 'Avenue Ampang', 'Ampang', '68000 Ampang', 'Selangor');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `role`) VALUES
(1001, 'Ahmad Faizal Bin Abdullah', 'ahmadlah@email.com', '!234dmiN', 'admin'),
(1002, 'Siti Nurhaliza Binti Mohamed', 'sitin@email.com', 'Siti123!', 'user'),
(1003, 'Muhammad Ikhwan Bin Razak', 'ikhwanr@email.com', 'Ikhwan123!', 'user'),
(1004, 'Nur Aisyah Binti Hassan', 'aisyahh@email.com', 'Aisyah123!', 'user'),
(1005, 'Tan Wei Ming', 'tanwm@email.com', 'Tan123!', 'user'),
(1006, 'Kavitha A/P Ramasamy', 'kavithar@email.com', 'Kavitha123!', 'user'),
(1007, 'Mohd Firdaus Bin Ismail', 'firdausi@email.com', 'Firdaus123!', 'user'),
(1008, 'Sarah Binti Ahmad', 'saraha@email.com', 'Sarah123!', 'user'),
(1009, 'Lim Xin Hui', 'limxh@email.com', 'Lim123!', 'user'),
(1010, 'Raj Kumar A/L Subramaniam', 'rajk@email.com', 'Raj123!', 'user'),
(1011, 'Nurul Huda Binti Zainal', 'nurulhz@email.com', 'Nurul123!', 'user'),
(1012, 'Wong Chee Meng', 'wongcm@email.com', 'Wong123!', 'user'),
(1013, 'Farah Diana Binti Malik', 'farahdm@email.com', 'Farah123!', 'user'),
(1014, 'Hafiz Bin Rosli', 'hafizr@email.com', 'Hafiz123!', 'user'),
(1015, 'Christine Wong Mei Ling', 'christinew@email.com', 'Christine123!', 'user'),
(1016, 'Muhammad Ali Bin Osman', 'alio@email.com', 'Ali123!', 'user'),
(1017, 'Nadia Binti Abdullah', 'nadiaa@email.com', 'Nadia123!', 'user'),
(1018, 'Goh Soo Chin', 'gohsc@email.com', 'Goh123!', 'user'),
(1019, 'Rina Binti Sulaiman', 'rinas@email.com', 'Rina123!', 'user'),
(1020, 'Ahmad Faiz Bin Rahman', 'ahmadfr@email.com', 'Ahmad123!', 'user'),
(1021, 'Sarah Tan Mei Ling', 'sarahtan@email.com', 'Sarah123!', 'admin'),
(1022, 'Soo Win Cee', 'sqc123@email.com', '!234zxcV', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`friend_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `friend_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5031;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1023;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
