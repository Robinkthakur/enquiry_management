-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 29, 2026 at 02:26 PM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anuinmaw_anuinfoenquiry`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attribute_changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

CREATE TABLE `admissions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enquiry_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roll_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `admission_date` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `admission_no`, `enquiry_id`, `student_photo`, `roll_no`, `student_name`, `father_name`, `mobile`, `email`, `address`, `admission_date`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
('019eb096-b907-7149-baee-ae6e9bb81598', 'ADM-2026-00001', NULL, NULL, 'ROLL-2026-00001', 'Harnoor Kaur', 'Pritpal Singh', '8146289613', NULL, 'Guru Harkrishan Nagar', '2026-05-29', 'Active', NULL, '2026-06-09 21:12:08', '2026-06-09 21:12:08'),
('019eb0bb-5bf0-7396-af7b-f898f214c0f7', 'ADM-2026-00002', NULL, NULL, 'ROLL-2026-00002', 'Harshit Singh', 'Ajay Kumar', '9877262613', NULL, 'Guru Harkrishan Nagar,Khanna', '2026-05-19', 'Active', NULL, '2026-06-09 21:52:09', '2026-06-22 17:38:08'),
('019eee5f-4a6a-713e-823d-4bccdb64f759', 'ADM-2026-00003', NULL, NULL, 'ROLL-2026-00003', 'Rahul Sharma', 'Satish Kumar', '9914100675', NULL, 'Ward no. 22,Near Modi Street Khanna.', '2026-05-19', 'Active', NULL, '2026-06-21 21:08:02', '2026-06-21 21:08:02'),
('019eee62-1d48-70f1-bf4a-d553a580823c', 'ADM-2026-00004', NULL, NULL, 'ROLL-2026-00004', 'Kamal Aggarwal', 'Anil Aggarwal', '8284013868', NULL, '156,5-B, New Shastri Nagar,Mandi Gobindgarh.', '2026-05-11', 'Active', NULL, '2026-06-21 21:11:07', '2026-06-21 21:11:07'),
('019eee64-3067-7260-8e64-d73a6f435193', 'ADM-2026-00005', NULL, NULL, 'ROLL-2026-00005', 'Riya Matta', 'Jai Parkash', '9781481536', NULL, 'New Model Town Khanna.', '2026-05-07', 'Active', '2026-06-23 00:17:38', '2026-06-21 21:13:23', '2026-06-23 00:17:38'),
('019eee66-0320-7005-b748-fb9b33e575e3', 'ADM-2026-00006', NULL, NULL, 'ROLL-2026-00006', 'Amandeep Kaur', 'Jasvir Singh', '7696155917', NULL, 'Harbanspura', '2026-05-01', 'Active', NULL, '2026-06-21 21:15:23', '2026-06-21 21:15:23'),
('019eee67-7e55-71d0-a177-24f4f97628c5', 'ADM-2026-00007', NULL, NULL, 'ROLL-2026-00007', 'Ramandeep Kaur', 'Jasvir Singh', '76961559017', NULL, 'Harbanspura', '2026-05-01', 'Active', NULL, '2026-06-21 21:17:00', '2026-06-21 21:17:00'),
('019eee6c-787c-727b-9e77-015db0092d91', 'ADM-2026-00008', NULL, NULL, 'ROLL-2026-00008', 'Gursewak Singh', 'Sher Singh', '9877500054', NULL, 'Mandi Gobindgarh', '2026-02-11', 'Active', NULL, '2026-06-21 21:22:26', '2026-06-21 21:22:26'),
('019eee6f-5c49-713d-8713-0fd578f2c2b8', 'ADM-2026-00009', NULL, NULL, 'ROLL-2026-00009', 'Jaspreet', 'Charanjeet Singh', '9356093580', NULL, 'Ward no.- 25, Near New Krishna Hospital, M.K Road, Khanna.', '2025-09-15', 'Active', NULL, '2026-06-21 21:25:36', '2026-06-21 21:25:36'),
('019eee71-d7d3-7222-a913-8dd096e31df6', 'ADM-2026-00010', NULL, NULL, 'ROLL-2026-00010', 'Sukhpal Singh', 'Neterpal Singh', '8727006751', NULL, 'new Sant Nagar Gaushala Road,Mandigobindgarh', '2025-02-05', 'Active', NULL, '2026-06-21 21:28:18', '2026-06-21 21:28:18'),
('019eee74-24a7-714d-876b-c50617a254a5', 'ADM-2026-00011', NULL, NULL, 'ROLL-2026-00011', 'Yash Aerry', 'Bunty Sharma', '8054123735', NULL, 'Jagat Colony Lalheri Road', '2026-04-03', 'Active', NULL, '2026-06-21 21:30:49', '2026-06-21 21:30:49'),
('019eee79-5c6d-7012-96bc-9d6ec263b245', 'ADM-2026-00012', NULL, NULL, 'ROLL-2026-00012', 'Agrim', 'Manoj Kumar', '9877668969', NULL, 'Opposite Krishna Nagar 13', '2026-04-04', 'Active', NULL, '2026-06-21 21:36:31', '2026-06-21 21:36:31'),
('019ef2bf-ad0e-73e4-9be6-b1df6e5434d4', 'ADM-2026-00013', NULL, NULL, 'ROLL-2026-00013', 'Riya Matta', 'Jai Parkash', '9781481536', NULL, 'New Model Town Khanna.', '2026-05-07', 'Active', NULL, '2026-06-22 17:31:48', '2026-06-22 17:31:48'),
('019f0749-50c2-71df-8f11-caf81b947cc2', 'ADM-2026-00014', NULL, NULL, 'ROLL-2026-00014', 'Saira', 'muhammad Wasim', '9816490793', NULL, 'Model Town Khanna(H.P.)', '2026-06-26', 'Active', NULL, '2026-06-26 22:44:33', '2026-06-26 22:44:33'),
('019f074d-28cb-71ce-9599-8a6bea0cd7fc', 'ADM-2026-00015', NULL, NULL, 'ROLL-2026-00015', 'Dilpreet Kaur', 'Baljinder Singh', '9501163957', NULL, 'Village Shamgarh, samrala, Lyd.', '2026-06-24', 'Active', NULL, '2026-06-26 22:48:45', '2026-06-26 22:48:45'),
('019f074e-5aba-722f-acb0-7c16795c8754', 'ADM-2026-00016', NULL, NULL, 'ROLL-2026-00016', 'Siya Behl', 'Amarjit Kumar', '6239855576', NULL, 'AS college Colony Khanna', '2026-06-24', 'Active', NULL, '2026-06-26 22:50:03', '2026-06-26 22:50:03'),
('019f0750-4733-7167-a088-227978aa4ba6', 'ADM-2026-00017', NULL, NULL, 'ROLL-2026-00017', 'Parth Rattan', 'Rajiv Rattan', '8699388869', NULL, 'Op. Spring Dale Sch. khanna', '2026-06-23', 'Active', NULL, '2026-06-26 22:52:09', '2026-06-26 22:52:09'),
('019f0752-ac61-73cf-a351-a64b3725efa0', 'ADM-2026-00018', NULL, NULL, 'ROLL-2026-00018', 'Arleen Kaur', 'Gurdeep Singh', '6284510381', NULL, 'BS Enclave, Khanna Road Amloh', '2026-06-22', 'Active', NULL, '2026-06-26 22:54:46', '2026-06-26 22:54:46'),
('019f0754-4912-70e2-86ad-0829dd1814da', 'ADM-2026-00019', NULL, NULL, 'ROLL-2026-00019', 'Harkamalpreet Singh', 'Jaswinder Singh', '8427651534', NULL, 'Rajewal', '2026-06-18', 'Active', NULL, '2026-06-26 22:56:32', '2026-06-26 22:56:32'),
('019f0755-8232-7229-9bb6-36f607295808', 'ADM-2026-00020', NULL, NULL, 'ROLL-2026-00020', 'Isha', 'Kuldeep Kumar', '6284257212', NULL, 'Ward no.5 Amloh', '2026-06-16', 'Active', NULL, '2026-06-26 22:57:52', '2026-06-26 22:57:52'),
('019f0756-c484-73ce-9ce4-2aeff173a40e', 'ADM-2026-00021', NULL, NULL, 'ROLL-2026-00021', 'Navneet Singh', 'Balvir Singh', '9592166357', NULL, 'Faizullapur', '2026-06-15', 'Active', NULL, '2026-06-26 22:59:14', '2026-06-26 22:59:14'),
('019f0758-0827-725d-8381-525858db2eae', 'ADM-2026-00022', NULL, NULL, 'ROLL-2026-00022', 'Parminder Singh', 'Jagvir Singh', '9877030286', NULL, 'V.P.O. Rajewall(Rhono)', '2026-06-15', 'Active', NULL, '2026-06-26 23:00:37', '2026-06-26 23:00:37'),
('019f075a-0275-7373-9ec3-5c176f83228c', 'ADM-2026-00023', NULL, NULL, 'ROLL-2026-00023', 'Gagandeep `', 'Ashok kumar', '7986062262', NULL, 'G.H.K Khanna', '2026-06-15', 'Active', NULL, '2026-06-26 23:02:47', '2026-06-26 23:02:47'),
('019f075b-4a13-734e-92b3-fd70255e8ed6', 'ADM-2026-00024', NULL, NULL, 'ROLL-2026-00024', 'Deepanshu', 'Ramesh Kumar', '7508343462', NULL, 'Model Town khnna', '2026-06-15', 'Active', NULL, '2026-06-26 23:04:11', '2026-06-26 23:04:11'),
('019f076a-8296-7048-8fd6-66a26cc2963c', 'ADM-2026-00025', NULL, NULL, 'ROLL-2026-00025', 'Himmat Singh', 'Jasvinder Singh', '6280591984', NULL, 'VPO Manupur', '2026-06-15', 'Active', NULL, '2026-06-26 23:20:48', '2026-06-26 23:20:48'),
('019f076b-c0c6-7130-8f4c-1ccbc056bdf7', 'ADM-2026-00026', NULL, NULL, 'ROLL-2026-00026', 'Mehak', 'Manoj Kumar', '9781928056', NULL, 'VPO Kanech Sahenwal', '2026-06-27', 'Active', NULL, '2026-06-26 23:22:10', '2026-06-26 23:22:10'),
('019f076c-cba9-710e-b415-68070a7e202f', 'ADM-2026-00027', NULL, NULL, 'ROLL-2026-00027', 'Kunal', 'Sushil Kumar', '8360630796', NULL, 'Gandhi Nagar Mandi Gobindgarh', '2026-06-08', 'Active', NULL, '2026-06-26 23:23:18', '2026-06-26 23:23:18'),
('019f076e-c4fa-71bf-87d3-492200b253c6', 'ADM-2026-00028', NULL, NULL, 'ROLL-2026-00028', 'Arman Singh', 'Harjinder Singh', '9781208386', NULL, 'Gulmohar Nagar Khanna', '2026-06-09', 'Active', NULL, '2026-06-26 23:25:27', '2026-06-26 23:25:27'),
('019f076f-e848-7305-a54c-d469cb0c6ea6', 'ADM-2026-00029', NULL, NULL, 'ROLL-2026-00029', 'Nancy', 'Bintu Ram', '6280780961', NULL, 'Ward no.5 Azad Nagar Khanna', '2026-06-09', 'Active', NULL, '2026-06-26 23:26:42', '2026-06-26 23:26:42'),
('019f0771-2f2d-709e-8b31-8c5239fccea7', 'ADM-2026-00030', NULL, NULL, 'ROLL-2026-00030', 'Kiranjot Kaur', 'Taranjeet Singh', '9988162167', NULL, 'Jagat Colony Khanna', '2026-06-09', 'Active', NULL, '2026-06-26 23:28:05', '2026-06-26 23:28:05'),
('019f0772-3533-7060-acfb-3b63d8feea53', 'ADM-2026-00031', NULL, NULL, 'ROLL-2026-00031', 'Gursimar Kaur', 'Karamjit Singh', '9877639609', NULL, 'Village Badla', '2026-06-09', 'Active', NULL, '2026-06-26 23:29:13', '2026-06-26 23:29:13'),
('019f0773-6a9a-73d2-b12d-cc2dbb61aaba', 'ADM-2026-00032', NULL, NULL, 'ROLL-2026-00032', 'Arunjot Singh', 'Balwinder singh', '9877639609', NULL, 'Village Badla', '2026-06-09', 'Active', NULL, '2026-06-26 23:30:32', '2026-06-26 23:30:32'),
('019f0782-6bf2-720e-a12f-0c75508baf91', 'ADM-2026-00033', NULL, NULL, 'ROLL-2026-00033', 'Amandeep Kaur', 'Balwinder Singh', '8360103815', NULL, 'Village Lalheri', '2026-06-08', 'Active', NULL, '2026-06-26 23:46:55', '2026-06-26 23:46:55'),
('019f0783-b6cb-73a4-9120-3e3a90f401d0', 'ADM-2026-00034', NULL, NULL, 'ROLL-2026-00034', 'Simranjeet Kaur', 'Beant Singh', '7627852310', NULL, 'Village Manak Majra', '2026-06-08', 'Active', NULL, '2026-06-26 23:48:20', '2026-06-26 23:48:20'),
('019f0784-d14b-7281-bdd1-583db8321565', 'ADM-2026-00035', NULL, NULL, 'ROLL-2026-00035', 'Rajanpreet Kaur', 'Pal Singh', '6284824112', NULL, 'Village Aloona Majra', '2026-06-08', 'Active', NULL, '2026-06-26 23:49:32', '2026-06-26 23:49:32'),
('019f0785-c7ab-714e-8cda-965149d1d415', 'ADM-2026-00036', NULL, NULL, 'ROLL-2026-00036', 'Seema', 'Buta Muhhamad', '6280236861', NULL, 'Salana', '2026-06-08', 'Active', NULL, '2026-06-26 23:50:35', '2026-06-26 23:50:35'),
('019f0787-0f45-7173-852e-6afde1a270ee', 'ADM-2026-00037', NULL, NULL, 'ROLL-2026-00037', 'Ishaan Singla', 'Inderjeet Singla', '7658088644', NULL, 'Moti Nagar bhattian, Khanna', '2026-06-05', 'Active', NULL, '2026-06-26 23:51:59', '2026-06-26 23:51:59'),
('019f0788-580f-7227-8100-ad11f41b4f50', 'ADM-2026-00038', NULL, NULL, 'ROLL-2026-00038', 'Veerjot Kaur', 'Dhanwant Singh', '9779794073', NULL, 'Oppo. Lala Sarlkarumal School Nabha Colony Khanna', '2026-06-06', 'Active', NULL, '2026-06-26 23:53:23', '2026-06-26 23:53:23'),
('019f0789-78f3-7375-b00e-0f8640ee330c', 'ADM-2026-00039', NULL, NULL, 'ROLL-2026-00039', 'Kirandeep Kaur', 'Harwinder Singh', '8198817280', NULL, 'Vill Goh,teh Khanna', '2026-06-05', 'Active', NULL, '2026-06-26 23:54:37', '2026-06-26 23:54:37'),
('019f078a-eff8-7036-ba73-c3a27258a1d6', 'ADM-2026-00040', NULL, NULL, 'ROLL-2026-00040', 'Anamika', 'Ashok Kumar', '8544839101', NULL, 'New Model town Amloh Road Khanna ', '2026-06-04', 'Active', NULL, '2026-06-26 23:56:13', '2026-06-26 23:56:13'),
('019f078b-d19a-70d2-a3c6-213e1c0d4aff', 'ADM-2026-00041', NULL, NULL, 'ROLL-2026-00041', 'Simranjeet Kaur', 'Tarsem Singh ', '8146042703', NULL, 'Daudpur', '2026-06-04', 'Active', NULL, '2026-06-26 23:57:11', '2026-06-26 23:57:11'),
('019f078d-40ce-7010-a054-75268a945379', 'ADM-2026-00042', NULL, NULL, 'ROLL-2026-00042', 'Vanshika Chabbra', 'Ashish Chabbra', '9115312612', NULL, 'Pankaj Karyana Store Khanna', '2026-06-03', 'Active', NULL, '2026-06-26 23:58:45', '2026-06-26 23:58:45'),
('019f0790-e1f3-72f3-a07e-bcc7b601ce98', 'ADM-2026-00043', NULL, NULL, 'ROLL-2026-00043', 'Rashmi jassal', 'Vikramjit Singh', '7710702127', NULL, 'Sundar Nagar St. no.1Naer Botal wali Gali Khanna', '2026-06-03', 'Active', NULL, '2026-06-27 00:02:43', '2026-06-27 00:02:43'),
('019f0793-da74-7177-9836-88092ff7c152', 'ADM-2026-00044', NULL, NULL, 'ROLL-2026-00044', 'Gursimaran Sarhadi', 'Parveen Kumar', '9877806883', NULL, 'New Model Town Khanna', '2026-06-01', 'Active', NULL, '2026-06-27 00:05:58', '2026-06-27 00:05:58'),
('019f0797-0123-71d0-8f70-eb074589e1d7', 'ADM-2026-00045', NULL, NULL, 'ROLL-2026-00045', 'Eknoor Kaur', 'Sukhjeet Singh', '8054330070', NULL, 'Village Khanian', '2026-06-01', 'Active', NULL, '2026-06-27 00:09:24', '2026-06-27 00:09:24'),
('019f0798-e57a-73a7-8e48-4e10a5cada13', 'ADM-2026-00046', NULL, NULL, 'ROLL-2026-00046', 'Dishika ', 'Gautam Dhand', '6283531043', NULL, '#4 Opp. Water TAnky No.3 Radha Krishna Mandir Road Khanna', '2026-06-01', 'Active', NULL, '2026-06-27 00:11:28', '2026-06-27 00:11:28'),
('019f079a-8bdf-70be-9b9d-bdf5bf3b02d0', 'ADM-2026-00047', NULL, NULL, 'ROLL-2026-00047', 'Paramveer Singh', 'Sukhdev Singh', '7814847307', NULL, 'House No. 738 Street no 10 G.H.N. ,  Khanna', '2026-06-01', 'Active', NULL, '2026-06-27 00:13:16', '2026-06-27 00:13:16'),
('019f079b-eabf-7149-a84c-e6dcb4f5d0d2', 'ADM-2026-00048', NULL, NULL, 'ROLL-2026-00048', 'Harmeet ', 'Rakesh Kumar', '9779285193', NULL, 'Jargari Near Post office ', '2026-06-01', 'Active', NULL, '2026-06-27 00:14:46', '2026-06-27 00:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `admission_courses`
--

CREATE TABLE `admission_courses` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_slot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instructor_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_fee` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `final_fee` decimal(10,2) NOT NULL,
  `registration_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admission_courses`
--

INSERT INTO `admission_courses` (`id`, `admission_id`, `course_id`, `time_slot`, `instructor_id`, `total_fee`, `discount_amount`, `final_fee`, `registration_fee`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
('019ef8e5-0319-7038-9267-1eaf17aa8a17', '019ef2bf-ad0e-73e4-9be6-b1df6e5434d4', '019eafc7-8670-7251-995e-613109a482fb', '12:00 PM - 01:00 PM', '019eaaea-e4bb-722a-a779-5b85c176529d', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-24 03:40:18', '2026-06-24 03:40:18'),
('019f0744-a77b-718a-8ef7-6e15da3ca629', '019eee5f-4a6a-713e-823d-4bccdb64f759', '019eafcc-e1aa-7370-9b55-49c349b6c9c5', '12:00 PM - 02:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 10000.00, 0.00, 10000.00, 0.00, 'Active', NULL, '2026-06-26 22:39:27', '2026-06-26 22:39:27'),
('019f0749-50ca-73ad-890a-8fc2dcc03f87', '019f0749-50c2-71df-8f11-caf81b947cc2', '019ebfa1-2406-71db-80d6-f78766e18e68', '08:00 AM - 09:00 AM', '019eaaea-e4bb-722a-a779-5b85c176529d', 4000.00, 0.00, 4000.00, 0.00, 'Active', NULL, '2026-06-26 22:44:33', '2026-06-26 22:44:33'),
('019f074d-28d2-71a8-9637-84b6fcc8392e', '019f074d-28cb-71ce-9599-8a6bea0cd7fc', '019ebfa1-2406-71db-80d6-f78766e18e68', '08:00 AM - 09:30 AM', '019ee3d1-8295-7220-9186-d698248f36ed', 4500.00, 0.00, 4500.00, 0.00, 'Active', NULL, '2026-06-26 22:48:45', '2026-06-26 22:48:45'),
('019f074e-5abc-71ad-af72-065db96184ae', '019f074e-5aba-722f-acb0-7c16795c8754', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '04:00 PM - 06:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 22:50:03', '2026-06-26 22:50:03'),
('019f0750-4735-70fa-ae65-f74dd933b42b', '019f0750-4733-7167-a088-227978aa4ba6', '019eafcb-9309-702f-973a-77cee8247fba', '10:00 AM - 12:00 PM', '019ee3d1-8295-7220-9186-d698248f36ed', 4000.00, 0.00, 4000.00, 0.00, 'Active', NULL, '2026-06-26 22:52:09', '2026-06-26 22:52:09'),
('019f0750-4737-70e2-90c3-33d4761eaa4d', '019f0750-4733-7167-a088-227978aa4ba6', '019eafcb-db33-73ac-8e7d-9a384cb3d10c', '10:00 AM - 12:00 PM', '019ee3d1-8295-7220-9186-d698248f36ed', 4500.00, 0.00, 4500.00, 0.00, 'Active', NULL, '2026-06-26 22:52:09', '2026-06-26 22:52:09'),
('019f0752-ac63-7310-a8d9-6c25a3ebc916', '019f0752-ac61-73cf-a351-a64b3725efa0', '019eafc9-4af9-71d7-9763-9b3db682e3a0', '08:30 AM - 04:00 PM', '019ee3d1-8295-7220-9186-d698248f36ed', 30000.00, 0.00, 30000.00, 0.00, 'Active', NULL, '2026-06-26 22:54:46', '2026-06-26 22:54:46'),
('019f0752-ac64-73ad-9844-882fe080f889', '019f0752-ac61-73cf-a351-a64b3725efa0', '019eafcb-9309-702f-973a-77cee8247fba', '08:30 AM - 04:00 PM', '019ee3d1-8295-7220-9186-d698248f36ed', 4000.00, 0.00, 4000.00, 0.00, 'Active', NULL, '2026-06-26 22:54:46', '2026-06-26 22:54:46'),
('019f0752-ac66-7330-a3e8-464cec81c7a6', '019f0752-ac61-73cf-a351-a64b3725efa0', '019eafcb-db33-73ac-8e7d-9a384cb3d10c', '08:30 AM - 04:00 PM', '019ee3d1-8295-7220-9186-d698248f36ed', 4500.00, 0.00, 4500.00, 0.00, 'Active', NULL, '2026-06-26 22:54:46', '2026-06-26 22:54:46'),
('019f0754-4914-73d0-bf9c-bd160184be97', '019f0754-4912-70e2-86ad-0829dd1814da', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '08:00 AM - 10:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 22:56:32', '2026-06-26 22:56:32'),
('019f0755-8233-73f1-8378-cb9d4ebc8b35', '019f0755-8232-7229-9bb6-36f607295808', '019eafc7-8670-7251-995e-613109a482fb', '10:00 AM - 12:00 PM', '019eaaea-e4bb-722a-a779-5b85c176529d', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-26 22:57:52', '2026-06-26 22:57:52'),
('019f0756-c485-7221-8d85-fb688164eeff', '019f0756-c484-73ce-9ce4-2aeff173a40e', '019eafcf-1f47-73e4-aab4-ae817c01f99c', '08:00 AM - 10:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-26 22:59:14', '2026-06-26 22:59:14'),
('019f0758-0829-72a9-a698-e26829d0fc13', '019f0758-0827-725d-8381-525858db2eae', '019eafcf-1f47-73e4-aab4-ae817c01f99c', '08:00 AM - 10:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-26 23:00:37', '2026-06-26 23:00:37'),
('019f075a-0277-705b-85df-0532321b3cd3', '019f075a-0275-7373-9ec3-5c176f83228c', '019eafcc-e1aa-7370-9b55-49c349b6c9c5', '04:00 PM - 06:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 10000.00, 0.00, 10000.00, 0.00, 'Active', NULL, '2026-06-26 23:02:47', '2026-06-26 23:02:47'),
('019f075a-0279-7330-864c-027ba9008e9d', '019f075a-0275-7373-9ec3-5c176f83228c', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '04:00 PM - 06:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:02:47', '2026-06-26 23:02:47'),
('019f075b-4a14-7131-af2d-700827240230', '019f075b-4a13-734e-92b3-fd70255e8ed6', '019eafc7-8670-7251-995e-613109a482fb', '08:00 AM - 09:00 PM', '019eaaea-e4bb-722a-a779-5b85c176529d', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-26 23:04:11', '2026-06-26 23:04:11'),
('019f076a-829d-71e7-bad5-46390940ec46', '019f076a-8296-7048-8fd6-66a26cc2963c', '019eafc7-8670-7251-995e-613109a482fb', '08:00 AM - 09:00 PM', '019eaaea-e4bb-722a-a779-5b85c176529d', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-26 23:20:48', '2026-06-26 23:20:48'),
('019f076b-c0c7-717a-9635-29f5a5d71d1a', '019f076b-c0c6-7130-8f4c-1ccbc056bdf7', '019eafcb-01cd-72be-80c9-3334e02a3bda', '12:00 PM - 01:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-26 23:22:10', '2026-06-26 23:22:10'),
('019f076c-cbaa-72d6-91cf-a6917041a85e', '019f076c-cba9-710e-b415-68070a7e202f', '019eafcb-9309-702f-973a-77cee8247fba', '02:00 PM - 04:00 PM', '019ee3d1-8295-7220-9186-d698248f36ed', 4000.00, 0.00, 4000.00, 0.00, 'Active', NULL, '2026-06-26 23:23:18', '2026-06-26 23:23:18'),
('019f076d-9f9c-7393-9e03-217d652c9809', '019f076c-cba9-710e-b415-68070a7e202f', '019eafcb-db33-73ac-8e7d-9a384cb3d10c', '02:00 PM - 04:00 PM', '019ee3d1-8295-7220-9186-d698248f36ed', 4500.00, 0.00, 4500.00, 0.00, 'Active', NULL, '2026-06-26 23:24:12', '2026-06-26 23:24:12'),
('019f076e-c4fc-73ff-a6e8-e4f921eea105', '019f076e-c4fa-71bf-87d3-492200b253c6', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '02:00 PM - 04:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:25:27', '2026-06-26 23:25:27'),
('019f076f-e84b-72d6-8d5e-0609ca1b4947', '019f076f-e848-7305-a54c-d469cb0c6ea6', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '11:00 AM - 12:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:26:42', '2026-06-26 23:26:42'),
('019f0771-2f2f-720d-b002-a218557827dc', '019f0771-2f2d-709e-8b31-8c5239fccea7', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '08:00 AM - 10:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:28:05', '2026-06-26 23:28:05'),
('019f0772-3535-73ec-a1e1-cd1662dc2999', '019f0772-3533-7060-acfb-3b63d8feea53', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '08:00 AM - 10:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:29:13', '2026-06-26 23:29:13'),
('019f0773-6a9b-703c-9668-2192ecf7fde4', '019f0773-6a9a-73d2-b12d-cc2dbb61aaba', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '08:00 AM - 10:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:30:32', '2026-06-26 23:30:32'),
('019f0782-6bf8-721c-b417-ab0ab3f69daf', '019f0782-6bf2-720e-a12f-0c75508baf91', '019ebfa1-2406-71db-80d6-f78766e18e68', '10:00 AM - 11:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 1000.00, 0.00, 1000.00, 0.00, 'Active', NULL, '2026-06-26 23:46:55', '2026-06-26 23:46:55'),
('019f0783-b6cc-73c3-a702-4bc07cb183ed', '019f0783-b6cb-73a4-9120-3e3a90f401d0', '019ebfa1-2406-71db-80d6-f78766e18e68', '10:00 AM - 11:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 1000.00, 0.00, 1000.00, 0.00, 'Active', NULL, '2026-06-26 23:48:20', '2026-06-26 23:48:20'),
('019f0784-d14c-71fd-b4e4-c3ab8ff1ae4a', '019f0784-d14b-7281-bdd1-583db8321565', '019ebfa1-2406-71db-80d6-f78766e18e68', '10:00 AM - 11:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 1000.00, 0.00, 1000.00, 0.00, 'Active', NULL, '2026-06-26 23:49:32', '2026-06-26 23:49:32'),
('019f0785-c7ad-7395-96d5-88f95ecb0e0e', '019f0785-c7ab-714e-8cda-965149d1d415', '019ebfa1-2406-71db-80d6-f78766e18e68', '10:00 AM - 11:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 1000.00, 0.00, 1000.00, 0.00, 'Active', NULL, '2026-06-26 23:50:35', '2026-06-26 23:50:35'),
('019f0787-0f47-737e-9196-cb0ca3f1aa84', '019f0787-0f45-7173-852e-6afde1a270ee', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '12:00 PM - 01:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:51:59', '2026-06-26 23:51:59'),
('019f0788-5811-72a6-bd31-1b81ebb18ac9', '019f0788-580f-7227-8100-ad11f41b4f50', '019eafc7-8670-7251-995e-613109a482fb', '10:00 AM - 12:00 AM', '019eaaea-e4bb-722a-a779-5b85c176529d', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-26 23:53:23', '2026-06-26 23:53:23'),
('019f0789-78f5-7060-b24f-cf78344671e4', '019f0789-78f3-7375-b00e-0f8640ee330c', '019ebfa1-2406-71db-80d6-f78766e18e68', '10:00 AM - 11:00 AM', '019ee3d1-8295-7220-9186-d698248f36ed', 4500.00, 0.00, 4500.00, 0.00, 'Active', NULL, '2026-06-26 23:54:37', '2026-06-26 23:54:37'),
('019f078a-effa-712b-994e-f03a86b5b519', '019f078a-eff8-7036-ba73-c3a27258a1d6', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '11:00 AM - 12:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:56:13', '2026-06-26 23:56:13'),
('019f078b-d19c-723d-ac5c-04ecae57a72d', '019f078b-d19a-70d2-a3c6-213e1c0d4aff', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '09:00 AM - 10:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:57:11', '2026-06-26 23:57:11'),
('019f078d-40cf-73df-8d68-4dd6943f9280', '019f078d-40ce-7010-a054-75268a945379', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '11:00 AM - 12:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-26 23:58:45', '2026-06-26 23:58:45'),
('019f0790-e1fa-726a-8d6e-39a4ba48589c', '019f0790-e1f3-72f3-a07e-bcc7b601ce98', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '09:00 AM - 11:00 AM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-27 00:02:43', '2026-06-27 00:02:43'),
('019f0793-da7b-70c1-a333-1f173da1a3f9', '019f0793-da74-7177-9836-88092ff7c152', '019eafcb-9309-702f-973a-77cee8247fba', '03:00 PM - 04:00 PM', '019ee3d3-1231-71a8-90f4-8a15f2663da0', 4000.00, 0.00, 4000.00, 0.00, 'Active', NULL, '2026-06-27 00:05:58', '2026-06-27 00:06:13'),
('019f0797-0125-724d-8523-456901741097', '019f0797-0123-71d0-8f70-eb074589e1d7', '019eafcb-9309-702f-973a-77cee8247fba', '10:00 AM - 11:30 AM', '019ee3d1-8295-7220-9186-d698248f36ed', 4000.00, 0.00, 4000.00, 0.00, 'Active', NULL, '2026-06-27 00:09:24', '2026-06-27 00:09:24'),
('019f0797-0127-71b2-8ab5-bdfad759a173', '019f0797-0123-71d0-8f70-eb074589e1d7', '019eafcc-2b52-7221-a2e3-558d2e0753ce', '10:00 AM - 11:30 AM', '019ee3d1-8295-7220-9186-d698248f36ed', 12000.00, 0.00, 12000.00, 0.00, 'Active', NULL, '2026-06-27 00:09:24', '2026-06-27 00:09:24'),
('019f0798-e57c-71a3-b12a-2edc4d84cbdf', '019f0798-e57a-73a7-8e48-4e10a5cada13', '019eafcd-4c38-710e-95ba-3ef7890d49c7', '10:00 AM - 11:30 AM', '019ee3d4-0d9b-7032-9647-fcbfce6845aa', 18000.00, 0.00, 18000.00, 0.00, 'Active', NULL, '2026-06-27 00:11:28', '2026-06-27 00:11:28'),
('019f079a-8be0-7230-91b4-619fc488a0ee', '019f079a-8bdf-70be-9b9d-bdf5bf3b02d0', '019eafc7-8670-7251-995e-613109a482fb', '08:00 AM - 10:00 AM', '019eaaea-e4bb-722a-a779-5b85c176529d', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-27 00:13:16', '2026-06-27 00:13:16'),
('019f079b-eac1-71fb-b308-ec82c8b23bdd', '019f079b-eabf-7149-a84c-e6dcb4f5d0d2', '019eafc9-4af9-71d7-9763-9b3db682e3a0', '11:30 AM - 01:00 PM', '019ee3d1-8295-7220-9186-d698248f36ed', 27000.00, 0.00, 27000.00, 0.00, 'Active', NULL, '2026-06-27 00:14:46', '2026-06-27 00:14:46'),
('26a9c980-76a5-4d92-b280-b8e5c525f077', '019eee5f-4a6a-713e-823d-4bccdb64f759', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '09:00 AM - 01:37 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-21 21:08:02', '2026-06-21 21:08:02'),
('43206a4c-70f7-4d81-8c64-dae2942d7831', '019eee71-d7d3-7222-a913-8dd096e31df6', '019eafce-7c9c-73ee-98a2-216ddc97efd4', '10:00 AM - 12:00 PM', '019eaaea-e4bb-722a-a779-5b85c176529d', 12000.00, 0.00, 12000.00, 0.00, 'Active', NULL, '2026-06-21 21:28:18', '2026-06-21 21:28:18'),
('8126ff7a-abff-478c-96d0-307237f85d0e', '019eee66-0320-7005-b748-fb9b33e575e3', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '12:00 PM - 02:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-21 21:15:23', '2026-06-21 21:15:23'),
('8951e7f1-2f8f-477e-b9ee-39c126c65872', '019eb096-b907-7149-baee-ae6e9bb81598', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '9:00 to 11:00am', '019eaaea-e4bb-722a-a779-5b85c176529d', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-09 21:12:08', '2026-06-09 21:12:08'),
('8f1d29df-bb00-4a78-b178-f16cb75c30f2', '019eee62-1d48-70f1-bf4a-d553a580823c', '019eafcb-01cd-72be-80c9-3334e02a3bda', '12:59 PM - 01:00 PM', '019eaaea-e4bb-722a-a779-5b85c176529d', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-21 21:11:07', '2026-06-21 21:11:07'),
('937a6655-06b4-4d86-986f-c2c9b2d4711f', '019eee6f-5c49-713d-8713-0fd578f2c2b8', '019eafc9-4af9-71d7-9763-9b3db682e3a0', '08:00 AM - 10:00 AM', '019ee3d1-8295-7220-9186-d698248f36ed', 30000.00, 0.00, 30000.00, 0.00, 'Active', NULL, '2026-06-21 21:25:36', '2026-06-21 21:25:36'),
('96ac5953-9a3c-415b-973a-3695e87b293f', '019eee74-24a7-714d-876b-c50617a254a5', '019eafc9-4af9-71d7-9763-9b3db682e3a0', '11:00 AM - 12:00 PM', '019ee3d1-8295-7220-9186-d698248f36ed', 30000.00, 0.00, 30000.00, 0.00, 'Active', NULL, '2026-06-21 21:30:49', '2026-06-21 21:30:49'),
('9d57f5d0-d596-4521-a27f-820e7970eb63', '019ef2bf-ad0e-73e4-9be6-b1df6e5434d4', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '11:00 AM - 12:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-22 17:31:48', '2026-06-22 17:31:48'),
('a3644d15-76c9-4c0a-aa69-36fb78503507', '019eee64-3067-7260-8e64-d73a6f435193', '019eafc7-8670-7251-995e-613109a482fb', '11:00 AM - 12:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 6000.00, 0.00, 6000.00, 0.00, 'Active', NULL, '2026-06-21 21:13:23', '2026-06-22 17:31:48'),
('a576e893-2d35-46f1-9ac5-b092ca2d4b3d', '019eee67-7e55-71d0-a177-24f4f97628c5', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '12:00 PM - 02:46 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-21 21:17:00', '2026-06-21 21:17:00'),
('c0d2d25a-d840-401f-9c5a-2c460b15824b', '019eee79-5c6d-7012-96bc-9d6ec263b245', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '12:00 PM - 01:00 PM', '019ee3d2-5423-702e-b367-3945beb42855', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-21 21:36:31', '2026-06-21 21:36:31'),
('da90214a-17e3-4528-a8f1-ad721f4cb118', '019eee6c-787c-727b-9e77-015db0092d91', '019eafc9-4af9-71d7-9763-9b3db682e3a0', '10:00 AM - 11:00 AM', '019ee3d1-8295-7220-9186-d698248f36ed', 30000.00, 0.00, 30000.00, 0.00, 'Active', NULL, '2026-06-21 21:22:26', '2026-06-21 21:22:26'),
('ec2f8f53-a314-4106-a2bc-4a1a07e67c76', '019eb0bb-5bf0-7396-af7b-f898f214c0f7', '019eafc6-5c89-7108-87ac-f0f97cb0584f', '10:00 AM - 12:00 PM', '019eaaea-e4bb-722a-a779-5b85c176529d', 5000.00, 0.00, 5000.00, 0.00, 'Active', NULL, '2026-06-09 21:52:09', '2026-06-22 17:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_course_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('anu-infotech-cache-livewire-checksum-failures:162.214.66.67', 'i:1;', 1782308965),
('anu-infotech-cache-livewire-checksum-failures:162.214.66.67:timer', 'i:1782308965;', 1782308965),
('anu-infotech-cache-livewire-rate-limiter:d36bfff4bdcf7336546620e79a776e1042b63bf5', 'i:1;', 1782533290),
('anu-infotech-cache-livewire-rate-limiter:d36bfff4bdcf7336546620e79a776e1042b63bf5:timer', 'i:1782533290;', 1782533290),
('anu-infotech-cache-livewire-rate-limiter:fa06f86f2b313496c869156d727bb4b5056ecc33', 'i:1;', 1782716121),
('anu-infotech-cache-livewire-rate-limiter:fa06f86f2b313496c869156d727bb4b5056ecc33:timer', 'i:1782716121;', 1782716121),
('anu-infotech-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:22:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:12:\"courses.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:14:\"courses.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:14:\"courses.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"courses.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:14:\"enquiries.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:6;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:16:\"enquiries.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:16:\"enquiries.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:16:\"enquiries.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"admissions.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:6;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:17:\"admissions.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:17:\"admissions.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:17:\"admissions.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"attendance.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:6;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:17:\"attendance.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:6;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:9:\"fees.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:11:\"fees.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:10:\"holds.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:12:\"holds.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:17:\"certificates.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:19:\"certificates.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:12:\"reports.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:12:\"roles.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:5:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:9:\"Counselor\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:10:\"Accountant\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:10:\"Instructor\";s:1:\"c\";s:3:\"web\";}}}', 1782802462);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `certificate_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issue_date` date NOT NULL,
  `completion_date` date NOT NULL,
  `verification_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `support_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `description`, `support_email`, `mobile_no`, `logo`, `address`, `website`, `created_at`, `updated_at`) VALUES
(1, 'Anu Infotech', 'An ISO Certified Computer Institute & USA Based IT Company', 'support@anuinfotech.com', '+91 98887 58007, +91 75049 75050', 'branding/01KTNFSVWSX80HPZSP5V8949GX.png', 'SCF 25, Second Floor, GTB Market, Khanna, Pb - 141401', 'www.anuinfotech.com', '2026-06-08 18:40:58', '2026-06-08 19:06:03');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `duration_months` int(11) NOT NULL,
  `total_fee` decimal(10,2) NOT NULL,
  `registration_fee` decimal(10,2) NOT NULL,
  `certificate_fee` decimal(10,2) NOT NULL,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `description`, `duration_months`, `total_fee`, `registration_fee`, `certificate_fee`, `tax_percentage`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
('019eafb7-0200-7294-8e3d-a53b9dd45be8', 'Abc', 'Abc rvt', NULL, 3, 15000.00, 0.00, 0.00, 0.00, 'active', '2026-06-09 17:24:43', '2026-06-09 17:07:46', '2026-06-09 17:24:43'),
('019eafc6-5c89-7108-87ac-f0f97cb0584f', '101', 'Computer Basic', NULL, 2, 5000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:24:33', '2026-06-09 17:24:33'),
('019eafc7-8670-7251-995e-613109a482fb', '102', 'Computer Accounts', NULL, 3, 6000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:25:49', '2026-06-09 17:25:49'),
('019eafc8-992d-72bf-b168-b28eb61f164d', '103', 'Graphic Designing', NULL, 6, 18000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:26:59', '2026-06-09 17:26:59'),
('019eafc9-4af9-71d7-9763-9b3db682e3a0', '104', 'Web Designing', NULL, 6, 30000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:27:45', '2026-06-09 17:27:45'),
('019eafc9-e2a6-72af-b2e2-b1c74bf26b57', '105', 'Web Development', NULL, 3, 18000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:28:24', '2026-06-09 17:28:24'),
('019eafcb-01cd-72be-80c9-3334e02a3bda', '106', 'AutoCAD', '(Civil, Architecture, Mechnical)', 2, 6000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:29:37', '2026-06-09 17:29:37'),
('019eafcb-9309-702f-973a-77cee8247fba', '107', 'Language C', NULL, 1, 4000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:30:14', '2026-06-09 17:30:14'),
('019eafcb-db33-73ac-8e7d-9a384cb3d10c', '108', 'C++', NULL, 1, 4500.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:30:33', '2026-06-09 17:30:33'),
('019eafcc-2b52-7221-a2e3-558d2e0753ce', '109', 'Python', NULL, 2, 12000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:30:53', '2026-06-09 17:30:53'),
('019eafcc-79bd-72a0-9ad7-6fe9be1c32a0', '110', 'Java', NULL, 2, 12000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:31:13', '2026-06-09 17:31:13'),
('019eafcc-e1aa-7370-9b55-49c349b6c9c5', '111', 'Advanced Excel', NULL, 1, 10000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:31:40', '2026-06-09 17:31:40'),
('019eafcd-4c38-710e-95ba-3ef7890d49c7', '112', 'Digital Marketing', NULL, 3, 24000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:32:07', '2026-06-09 17:32:07'),
('019eafce-7c9c-73ee-98a2-216ddc97efd4', '113', 'AutoDesk 3Ds MAX', NULL, 2, 12000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:33:25', '2026-06-09 17:33:25'),
('019eafcf-1f47-73e4-aab4-ae817c01f99c', '114', 'CorelDRAW', NULL, 2, 6000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:34:07', '2026-06-09 17:34:07'),
('019eafcf-9f61-7362-8a46-3a3729e93570', '115', 'Adobe Photoshop', NULL, 2, 8000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:34:40', '2026-06-09 17:34:40'),
('019eafcf-e476-7184-8853-d9105cb29f49', '116', 'Adove Illustrator', NULL, 2, 8000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:34:57', '2026-06-09 17:34:57'),
('019eafd0-695e-72e6-a547-ab0632a69be0', '117', 'Typing', '(Punjabi, English)', 1, 3000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:35:31', '2026-06-09 17:35:31'),
('019eafd0-f14d-7078-99e6-5dd4b142d45b', '118', 'Data Structure', NULL, 2, 18000.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-09 17:36:06', '2026-06-09 17:36:06'),
('019ebfa1-2406-71db-80d6-f78766e18e68', '119', 'Industrial training', NULL, 1, 4500.00, 0.00, 0.00, 0.00, 'active', NULL, '2026-06-12 19:17:49', '2026-06-12 19:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enquiry_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `qualification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `enquiry_source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `follow_up_date` date DEFAULT NULL,
  `taken_by` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'New',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_courses`
--

CREATE TABLE `enquiry_courses` (
  `enquiry_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_timeline`
--

CREATE TABLE `enquiry_timeline` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enquiry_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_holds`
--

CREATE TABLE `fee_holds` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hold_from` date NOT NULL,
  `hold_to` date DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved_by` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_installments`
--

CREATE TABLE `fee_installments` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_course_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `installment_no` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_installments`
--

INSERT INTO `fee_installments` (`id`, `admission_id`, `admission_course_id`, `installment_no`, `due_date`, `amount`, `paid_amount`, `due_amount`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
('019eb096-b90d-727f-a642-f2ca13f00391', '019eb096-b907-7149-baee-ae6e9bb81598', '8951e7f1-2f8f-477e-b9ee-39c126c65872', 1, '2026-05-29', 0.00, 0.00, 0.00, 'Pending', NULL, '2026-06-09 21:12:08', '2026-06-09 21:12:08'),
('019eb096-b90d-727f-a642-f2ca14d103f3', '019eb096-b907-7149-baee-ae6e9bb81598', '8951e7f1-2f8f-477e-b9ee-39c126c65872', 2, '2026-06-28', 2500.00, 2500.00, 0.00, 'Paid', NULL, '2026-06-09 21:12:08', '2026-06-09 21:14:56'),
('019eb096-b90e-7106-bfbd-ad035d65cc8a', '019eb096-b907-7149-baee-ae6e9bb81598', '8951e7f1-2f8f-477e-b9ee-39c126c65872', 3, '2026-07-28', 2500.00, 2500.00, 0.00, 'Paid', NULL, '2026-06-09 21:12:08', '2026-06-09 21:15:38'),
('019eb0bb-5bf2-7039-a1a2-27fecf968fce', '019eb0bb-5bf0-7396-af7b-f898f214c0f7', 'ec2f8f53-a314-4106-a2bc-4a1a07e67c76', 1, '2026-05-19', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-09 21:52:09', '2026-06-09 22:16:55'),
('019eee5f-4a6e-72a5-b6df-ece32f46e890', '019eee5f-4a6a-713e-823d-4bccdb64f759', '26a9c980-76a5-4d92-b280-b8e5c525f077', 1, '2026-05-19', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-21 21:08:02', '2026-06-21 21:08:02'),
('019eee62-1d4a-7218-9d6f-66aa3dae25da', '019eee62-1d48-70f1-bf4a-d553a580823c', '8f1d29df-bb00-4a78-b178-f16cb75c30f2', 1, '2026-05-11', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-21 21:11:07', '2026-06-21 21:11:07'),
('019eee64-3069-728c-816f-70276da2dedf', '019eee64-3067-7260-8e64-d73a6f435193', 'a3644d15-76c9-4c0a-aa69-36fb78503507', 1, '2026-05-07', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-21 21:13:23', '2026-06-21 21:13:23'),
('019eee66-0322-73f2-bf0f-7a53fb009081', '019eee66-0320-7005-b748-fb9b33e575e3', '8126ff7a-abff-478c-96d0-307237f85d0e', 1, '2026-05-01', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-21 21:15:23', '2026-06-21 21:15:23'),
('019eee67-7e57-731e-9280-f82ded6f17e7', '019eee67-7e55-71d0-a177-24f4f97628c5', 'a576e893-2d35-46f1-9ac5-b092ca2d4b3d', 1, '2026-05-01', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-21 21:17:00', '2026-06-21 21:17:00'),
('019eee6c-7880-7235-90b3-2fe1731b4bba', '019eee6c-787c-727b-9e77-015db0092d91', 'da90214a-17e3-4528-a8f1-ad721f4cb118', 1, '2026-02-11', 30000.00, 0.00, 30000.00, 'Pending', NULL, '2026-06-21 21:22:26', '2026-06-21 21:22:26'),
('019eee6f-5c4e-71cb-8e96-f04e69a979b6', '019eee6f-5c49-713d-8713-0fd578f2c2b8', '937a6655-06b4-4d86-986f-c2c9b2d4711f', 1, '2025-09-15', 30000.00, 0.00, 30000.00, 'Pending', NULL, '2026-06-21 21:25:36', '2026-06-21 21:25:36'),
('019eee71-d7d6-72fe-a08f-cec6771f9e0c', '019eee71-d7d3-7222-a913-8dd096e31df6', '43206a4c-70f7-4d81-8c64-dae2942d7831', 1, '2025-02-05', 12000.00, 0.00, 12000.00, 'Pending', NULL, '2026-06-21 21:28:18', '2026-06-21 21:28:18'),
('019eee74-24a9-735d-8e10-ba2a0af2d133', '019eee74-24a7-714d-876b-c50617a254a5', '96ac5953-9a3c-415b-973a-3695e87b293f', 1, '2026-04-03', 30000.00, 0.00, 30000.00, 'Pending', NULL, '2026-06-21 21:30:49', '2026-06-21 21:30:49'),
('019eee79-5c6f-709f-876b-579d41698418', '019eee79-5c6d-7012-96bc-9d6ec263b245', 'c0d2d25a-d840-401f-9c5a-2c460b15824b', 1, '2026-04-04', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-21 21:36:31', '2026-06-21 21:36:31'),
('019ef2bf-ad11-72ac-8d0d-d840ef6e64ce', '019ef2bf-ad0e-73e4-9be6-b1df6e5434d4', '9d57f5d0-d596-4521-a27f-820e7970eb63', 1, '2026-05-07', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-22 17:31:48', '2026-06-22 17:31:48'),
('019ef8e5-031b-71a6-b385-0dd8dfe76883', '019ef2bf-ad0e-73e4-9be6-b1df6e5434d4', '019ef8e5-0319-7038-9267-1eaf17aa8a17', 1, '2026-05-07', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-24 03:40:18', '2026-06-24 03:40:18'),
('019f0744-a77d-700b-b366-34fda16cf9f5', '019eee5f-4a6a-713e-823d-4bccdb64f759', '019f0744-a77b-718a-8ef7-6e15da3ca629', 1, '2026-05-19', 10000.00, 0.00, 10000.00, 'Pending', NULL, '2026-06-26 22:39:27', '2026-06-26 22:39:27'),
('019f0749-50cc-706f-a06b-fd004ccca8aa', '019f0749-50c2-71df-8f11-caf81b947cc2', '019f0749-50ca-73ad-890a-8fc2dcc03f87', 1, '2026-06-26', 4000.00, 0.00, 4000.00, 'Pending', NULL, '2026-06-26 22:44:33', '2026-06-26 22:44:33'),
('019f074d-28d4-71ee-b3e3-89aef0577cec', '019f074d-28cb-71ce-9599-8a6bea0cd7fc', '019f074d-28d2-71a8-9637-84b6fcc8392e', 1, '2026-06-24', 4500.00, 0.00, 4500.00, 'Pending', NULL, '2026-06-26 22:48:45', '2026-06-26 22:48:45'),
('019f074e-5abd-70f0-ab98-327b11c0c670', '019f074e-5aba-722f-acb0-7c16795c8754', '019f074e-5abc-71ad-af72-065db96184ae', 1, '2026-06-24', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 22:50:03', '2026-06-26 22:50:03'),
('019f0750-4736-723b-b8a2-99a1091bba9d', '019f0750-4733-7167-a088-227978aa4ba6', '019f0750-4735-70fa-ae65-f74dd933b42b', 1, '2026-06-23', 4000.00, 0.00, 4000.00, 'Pending', NULL, '2026-06-26 22:52:09', '2026-06-26 22:52:09'),
('019f0750-4738-73d3-8bfc-a1003253f910', '019f0750-4733-7167-a088-227978aa4ba6', '019f0750-4737-70e2-90c3-33d4761eaa4d', 1, '2026-06-23', 4500.00, 0.00, 4500.00, 'Pending', NULL, '2026-06-26 22:52:09', '2026-06-26 22:52:09'),
('019f0752-ac64-73ad-9844-882fe004fd12', '019f0752-ac61-73cf-a351-a64b3725efa0', '019f0752-ac63-7310-a8d9-6c25a3ebc916', 1, '2026-06-22', 30000.00, 0.00, 30000.00, 'Pending', NULL, '2026-06-26 22:54:46', '2026-06-26 22:54:46'),
('019f0752-ac65-7242-9ebf-e0d5bebb1166', '019f0752-ac61-73cf-a351-a64b3725efa0', '019f0752-ac64-73ad-9844-882fe080f889', 1, '2026-06-22', 4000.00, 0.00, 4000.00, 'Pending', NULL, '2026-06-26 22:54:46', '2026-06-26 22:54:46'),
('019f0752-ac66-7330-a3e8-464cecf9c43c', '019f0752-ac61-73cf-a351-a64b3725efa0', '019f0752-ac66-7330-a3e8-464cec81c7a6', 1, '2026-06-22', 4500.00, 0.00, 4500.00, 'Pending', NULL, '2026-06-26 22:54:46', '2026-06-26 22:54:46'),
('019f0754-4916-7235-adfa-0ee459f92353', '019f0754-4912-70e2-86ad-0829dd1814da', '019f0754-4914-73d0-bf9c-bd160184be97', 1, '2026-06-18', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 22:56:32', '2026-06-26 22:56:32'),
('019f0755-8235-70c8-b127-c17c2a0bebf0', '019f0755-8232-7229-9bb6-36f607295808', '019f0755-8233-73f1-8378-cb9d4ebc8b35', 1, '2026-06-16', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-26 22:57:52', '2026-06-26 22:57:52'),
('019f0756-c487-72f9-8919-7360a32aabd8', '019f0756-c484-73ce-9ce4-2aeff173a40e', '019f0756-c485-7221-8d85-fb688164eeff', 1, '2026-06-15', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-26 22:59:14', '2026-06-26 22:59:14'),
('019f0758-082a-7036-85f6-750e71044864', '019f0758-0827-725d-8381-525858db2eae', '019f0758-0829-72a9-a698-e26829d0fc13', 1, '2026-06-15', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-26 23:00:37', '2026-06-26 23:00:37'),
('019f075a-0279-7330-864c-027ba80a5715', '019f075a-0275-7373-9ec3-5c176f83228c', '019f075a-0277-705b-85df-0532321b3cd3', 1, '2026-06-15', 10000.00, 0.00, 10000.00, 'Pending', NULL, '2026-06-26 23:02:47', '2026-06-26 23:02:47'),
('019f075a-027a-7086-92af-238791812648', '019f075a-0275-7373-9ec3-5c176f83228c', '019f075a-0279-7330-864c-027ba9008e9d', 1, '2026-06-15', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:02:47', '2026-06-26 23:02:47'),
('019f075b-4a16-70a5-9b86-f1febeace43a', '019f075b-4a13-734e-92b3-fd70255e8ed6', '019f075b-4a14-7131-af2d-700827240230', 1, '2026-06-15', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-26 23:04:11', '2026-06-26 23:04:11'),
('019f076a-82a0-706a-8222-fed9ec57a8fb', '019f076a-8296-7048-8fd6-66a26cc2963c', '019f076a-829d-71e7-bad5-46390940ec46', 1, '2026-06-15', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-26 23:20:48', '2026-06-26 23:20:48'),
('019f076b-c0c9-71f6-8837-f2ea6d60f401', '019f076b-c0c6-7130-8f4c-1ccbc056bdf7', '019f076b-c0c7-717a-9635-29f5a5d71d1a', 1, '2026-06-27', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-26 23:22:10', '2026-06-26 23:22:10'),
('019f076c-cbac-7345-aec8-4f367ca97888', '019f076c-cba9-710e-b415-68070a7e202f', '019f076c-cbaa-72d6-91cf-a6917041a85e', 1, '2026-06-08', 4000.00, 0.00, 4000.00, 'Pending', NULL, '2026-06-26 23:23:18', '2026-06-26 23:23:18'),
('019f076d-9f9e-70ce-93a4-fa307ecf30c8', '019f076c-cba9-710e-b415-68070a7e202f', '019f076d-9f9c-7393-9e03-217d652c9809', 1, '2026-06-08', 4500.00, 0.00, 4500.00, 'Pending', NULL, '2026-06-26 23:24:12', '2026-06-26 23:24:12'),
('019f076e-c4fe-73f5-bf5a-2764375abe6c', '019f076e-c4fa-71bf-87d3-492200b253c6', '019f076e-c4fc-73ff-a6e8-e4f921eea105', 1, '2026-06-09', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:25:27', '2026-06-26 23:25:27'),
('019f076f-e84d-7219-92fa-66a0dad059d4', '019f076f-e848-7305-a54c-d469cb0c6ea6', '019f076f-e84b-72d6-8d5e-0609ca1b4947', 1, '2026-06-09', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:26:42', '2026-06-26 23:26:42'),
('019f0771-2f30-72fa-bcd3-094ab25b90d5', '019f0771-2f2d-709e-8b31-8c5239fccea7', '019f0771-2f2f-720d-b002-a218557827dc', 1, '2026-06-09', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:28:06', '2026-06-26 23:28:06'),
('019f0772-3536-715b-b8af-d7f638481de5', '019f0772-3533-7060-acfb-3b63d8feea53', '019f0772-3535-73ec-a1e1-cd1662dc2999', 1, '2026-06-09', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:29:13', '2026-06-26 23:29:13'),
('019f0773-6a9d-71e5-8104-31e6517ffe0c', '019f0773-6a9a-73d2-b12d-cc2dbb61aaba', '019f0773-6a9b-703c-9668-2192ecf7fde4', 1, '2026-06-09', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:30:32', '2026-06-26 23:30:32'),
('019f0782-6bfa-7273-84a0-b63bbbd1c52a', '019f0782-6bf2-720e-a12f-0c75508baf91', '019f0782-6bf8-721c-b417-ab0ab3f69daf', 1, '2026-06-08', 1000.00, 0.00, 1000.00, 'Pending', NULL, '2026-06-26 23:46:55', '2026-06-26 23:46:55'),
('019f0783-b6ce-72dc-bddf-c2889f0c8b76', '019f0783-b6cb-73a4-9120-3e3a90f401d0', '019f0783-b6cc-73c3-a702-4bc07cb183ed', 1, '2026-06-08', 1000.00, 0.00, 1000.00, 'Pending', NULL, '2026-06-26 23:48:20', '2026-06-26 23:48:20'),
('019f0784-d14e-71c7-b03b-4b2f98c7c2c3', '019f0784-d14b-7281-bdd1-583db8321565', '019f0784-d14c-71fd-b4e4-c3ab8ff1ae4a', 1, '2026-06-08', 1000.00, 0.00, 1000.00, 'Pending', NULL, '2026-06-26 23:49:32', '2026-06-26 23:49:32'),
('019f0785-c7ae-710f-87e4-40a641a35b4f', '019f0785-c7ab-714e-8cda-965149d1d415', '019f0785-c7ad-7395-96d5-88f95ecb0e0e', 1, '2026-06-08', 1000.00, 0.00, 1000.00, 'Pending', NULL, '2026-06-26 23:50:35', '2026-06-26 23:50:35'),
('019f0787-0f48-72bc-b7b9-63346360fa18', '019f0787-0f45-7173-852e-6afde1a270ee', '019f0787-0f47-737e-9196-cb0ca3f1aa84', 1, '2026-06-05', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:51:59', '2026-06-26 23:51:59'),
('019f0788-5812-70b4-90df-b0013d84e2e9', '019f0788-580f-7227-8100-ad11f41b4f50', '019f0788-5811-72a6-bd31-1b81ebb18ac9', 1, '2026-06-06', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-26 23:53:23', '2026-06-26 23:53:23'),
('019f0789-78f6-7100-a60c-db6ceff01eee', '019f0789-78f3-7375-b00e-0f8640ee330c', '019f0789-78f5-7060-b24f-cf78344671e4', 1, '2026-06-05', 4500.00, 0.00, 4500.00, 'Pending', NULL, '2026-06-26 23:54:37', '2026-06-26 23:54:37'),
('019f078a-effb-7392-8f8a-3253bba5eb34', '019f078a-eff8-7036-ba73-c3a27258a1d6', '019f078a-effa-712b-994e-f03a86b5b519', 1, '2026-06-04', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:56:13', '2026-06-26 23:56:13'),
('019f078b-d19e-7249-8fbe-f195a71473e0', '019f078b-d19a-70d2-a3c6-213e1c0d4aff', '019f078b-d19c-723d-ac5c-04ecae57a72d', 1, '2026-06-04', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:57:11', '2026-06-26 23:57:11'),
('019f078d-40d1-72cb-a2da-3b2800e35b1b', '019f078d-40ce-7010-a054-75268a945379', '019f078d-40cf-73df-8d68-4dd6943f9280', 1, '2026-06-03', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-26 23:58:45', '2026-06-26 23:58:45'),
('019f0790-e1fc-73dc-80d5-e67bcdb1386e', '019f0790-e1f3-72f3-a07e-bcc7b601ce98', '019f0790-e1fa-726a-8d6e-39a4ba48589c', 1, '2026-06-03', 5000.00, 0.00, 5000.00, 'Pending', NULL, '2026-06-27 00:02:43', '2026-06-27 00:02:43'),
('019f0793-da7e-709b-a8e4-366db6cf9779', '019f0793-da74-7177-9836-88092ff7c152', '019f0793-da7b-70c1-a333-1f173da1a3f9', 1, '2026-06-01', 4000.00, 0.00, 4000.00, 'Pending', NULL, '2026-06-27 00:05:58', '2026-06-27 00:05:58'),
('019f0797-0126-717c-af88-5a7407f586b4', '019f0797-0123-71d0-8f70-eb074589e1d7', '019f0797-0125-724d-8523-456901741097', 1, '2026-06-01', 4000.00, 0.00, 4000.00, 'Pending', NULL, '2026-06-27 00:09:24', '2026-06-27 00:09:24'),
('019f0797-0128-70b5-97fd-a20bec76c6e1', '019f0797-0123-71d0-8f70-eb074589e1d7', '019f0797-0127-71b2-8ab5-bdfad759a173', 1, '2026-06-01', 12000.00, 0.00, 12000.00, 'Pending', NULL, '2026-06-27 00:09:24', '2026-06-27 00:09:24'),
('019f0798-e57d-723c-81fd-d61801e8e15d', '019f0798-e57a-73a7-8e48-4e10a5cada13', '019f0798-e57c-71a3-b12a-2edc4d84cbdf', 1, '2026-06-01', 18000.00, 0.00, 18000.00, 'Pending', NULL, '2026-06-27 00:11:28', '2026-06-27 00:11:28'),
('019f079a-8be2-7285-95e4-3adb0add7dd1', '019f079a-8bdf-70be-9b9d-bdf5bf3b02d0', '019f079a-8be0-7230-91b4-619fc488a0ee', 1, '2026-06-01', 6000.00, 0.00, 6000.00, 'Pending', NULL, '2026-06-27 00:13:16', '2026-06-27 00:13:16'),
('019f079b-eac3-7055-8e57-a64637507426', '019f079b-eabf-7149-a84c-e6dcb4f5d0d2', '019f079b-eac1-71fb-b308-ec82c8b23bdd', 1, '2026-06-01', 27000.00, 0.00, 27000.00, 'Pending', NULL, '2026-06-27 00:14:46', '2026-06-27 00:14:46');

-- --------------------------------------------------------

--
-- Table structure for table `fee_payments`
--

CREATE TABLE `fee_payments` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receipt_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admission_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fee_installment_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_payments`
--

INSERT INTO `fee_payments` (`id`, `receipt_no`, `admission_id`, `fee_installment_id`, `amount_paid`, `payment_method`, `transaction_reference`, `receipt_date`, `created_at`, `updated_at`) VALUES
('019eb099-499a-734a-baa3-bf60d90fa703', 'RCPT-2026-00001', '019eb096-b907-7149-baee-ae6e9bb81598', '019eb096-b90d-727f-a642-f2ca14d103f3', 2500.00, 'Cash', NULL, '2026-06-10', '2026-06-09 21:14:56', '2026-06-09 21:14:56'),
('019eb099-ecd4-7195-8f03-034a2e489141', 'RCPT-2026-00002', '019eb096-b907-7149-baee-ae6e9bb81598', '019eb096-b90e-7106-bfbd-ad035d65cc8a', 2500.00, 'Cash', NULL, '2026-06-10', '2026-06-09 21:15:38', '2026-06-09 21:15:38');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_03_095831_create_activity_log_table', 1),
(5, '2026_06_03_095831_create_permission_tables', 1),
(6, '2026_06_03_095832_create_student_system_tables', 1),
(7, '2026_06_07_160154_create_company_settings_table', 1),
(8, '2026_06_23_050610_create_admission_courses_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', '019eaae5-f86b-73b1-9b2c-f931087b8aac'),
(2, 'App\\Models\\User', '019eaae5-f86d-72e5-ae00-dfe5ea03cf4c'),
(3, 'App\\Models\\User', '019eaaea-e4bb-722a-a779-5b85c176529d'),
(4, 'App\\Models\\User', '019eaaea-e4bb-722a-a779-5b85c176529d'),
(6, 'App\\Models\\User', '019eaaea-e4bb-722a-a779-5b85c176529d'),
(6, 'App\\Models\\User', '019ee3d1-8295-7220-9186-d698248f36ed'),
(6, 'App\\Models\\User', '019ee3d2-5423-702e-b367-3945beb42855'),
(6, 'App\\Models\\User', '019ee3d3-1231-71a8-90f4-8a15f2663da0'),
(3, 'App\\Models\\User', '019ee3d4-0d9b-7032-9647-fcbfce6845aa'),
(6, 'App\\Models\\User', '019ee3d4-0d9b-7032-9647-fcbfce6845aa');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'courses.view', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(2, 'courses.create', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(3, 'courses.update', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(4, 'courses.delete', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(5, 'enquiries.view', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(6, 'enquiries.create', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(7, 'enquiries.update', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(8, 'enquiries.delete', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(9, 'admissions.view', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(10, 'admissions.create', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(11, 'admissions.update', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(12, 'admissions.delete', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(13, 'attendance.view', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(14, 'attendance.manage', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(15, 'fees.view', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(16, 'fees.manage', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(17, 'holds.view', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(18, 'holds.manage', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(19, 'certificates.view', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(20, 'certificates.manage', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(21, 'reports.view', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(22, 'roles.manage', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(2, 'Admin', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(3, 'Counselor', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(4, 'Accountant', 'web', '2026-06-08 18:40:57', '2026-06-08 18:40:57'),
(6, 'Instructor', 'web', '2026-06-08 19:01:12', '2026-06-08 19:01:12');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(1, 3),
(2, 3),
(3, 3),
(5, 3),
(6, 3),
(7, 3),
(9, 3),
(10, 3),
(11, 3),
(1, 4),
(2, 4),
(3, 4),
(9, 4),
(15, 4),
(16, 4),
(17, 4),
(18, 4),
(21, 4),
(1, 6),
(5, 6),
(9, 6),
(13, 6),
(14, 6);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('5EK3UPDS1D1WfqVqJ8cIf6OGcRYpqE6Iq6X97QSA', NULL, '49.156.97.60', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJZZkh0ODZSTW9GbXlCZ2pCRDhxdnlxaHpRQWc4ZWpmcFdGSWNPbzZPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9lbnF1aXJ5LmFudWluZm90ZWNoLmNvbVwvYWRtaW5cL2xvZ2luIiwicm91dGUiOiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwidXJsIjp7ImludGVuZGVkIjoiaHR0cHM6XC9cL2VucXVpcnkuYW51aW5mb3RlY2guY29tXC9zdHVkZW50In19', 1782716993),
('BATvLShHNs4W10hpCxHhhR0YGk8irbYIvSA5ZIdJ', NULL, '87.106.134.37', 'Mozilla/5.0 (compatible;)', 'eyJfdG9rZW4iOiJtNjR4VDVDbnZQWktGTWE5N0FHYkFoTVdIUFZNUnl0Y3o2aUZTYWY0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9lbnF1aXJ5LmFudWluZm90ZWNoLmNvbVwvYWRtaW5cL2xvZ2luIiwicm91dGUiOiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwidXJsIjp7ImludGVuZGVkIjoiaHR0cHM6XC9cL2VucXVpcnkuYW51aW5mb3RlY2guY29tXC9hZG1pbiJ9fQ==', 1782708732),
('BMCzgKSWznlBtw5AaHUKJ7rWE8hoqxBKbwkPPlix', NULL, '49.156.97.60', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJTUUplRDM5allyYzBtakFka3diVWY2a2U2ZE1XOVVpcjlWRHhMaERRIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9lbnF1aXJ5LmFudWluZm90ZWNoLmNvbVwvYWRtaW5cL2xvZ2luIiwicm91dGUiOiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwidXJsIjp7ImludGVuZGVkIjoiaHR0cHM6XC9cL2VucXVpcnkuYW51aW5mb3RlY2guY29tXC9zdHVkZW50In19', 1782717000),
('dMVLjOSzLwKvvXitmPlyjoD4No85KidpoNQ7Y9pe', '019eaae5-f86b-73b1-9b2c-f931087b8aac', '49.156.97.60', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIwZkRIcUVYYTBGcjljWVl2NEJTeXFlTFFHM0l3R3pWV3p5WTh5TW5vIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9lbnF1aXJ5LmFudWluZm90ZWNoLmNvbVwvYWRtaW5cL3JlcG9ydHMiLCJyb3V0ZSI6ImZpbGFtZW50LmFkbWluLnBhZ2VzLnJlcG9ydHMifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJ1cmwiOltdLCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6IjAxOWVhYWU1LWY4NmItNzNiMS05YjJjLWY5MzEwODdiOGFhYyIsInBhc3N3b3JkX2hhc2hfd2ViIjoiNjM5MzM5NDVkYWM5OTM4YTRkYzk3ZDY0NWEyNTFkNDljMDU0NWE0ZjdlODdmY2UwNDMyNzMzNjFiYzAxYWY1NSJ9', 1782716072),
('OVz4gNuetnncUnGr2hFMMXUK7b910LSPJVXd5str', NULL, '3.151.194.164', 'visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIxVjV1S2hzV0tlekQ5eVJwQzA0N0dmeHhHQ3ZCT1N4T3FDeUlkQ2JMIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cHM6XC9cL2VucXVpcnkuYW51aW5mb3RlY2guY29tXC9hZG1pbiJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2VucXVpcnkuYW51aW5mb3RlY2guY29tXC9hZG1pbiIsInJvdXRlIjoiZmlsYW1lbnQuYWRtaW4ucGFnZXMuZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1782688873),
('rPbORZfUoF0IHLAMVi4nsRaKl5XbtXQbC2BjycIe', NULL, '3.151.194.164', 'visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJVM2N4djVVN0s0T043OHVRNWkxRFhZSVM2SFZHTkQ5WVo2ZUJ0VjlpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9lbnF1aXJ5LmFudWluZm90ZWNoLmNvbVwvYWRtaW5cL2xvZ2luIiwicm91dGUiOiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1782688873),
('wfqGnLihV03D1aHsFP0jFYufGbA1DAHZc5PCaFyf', NULL, '3.151.194.164', 'visionheight.com/scan Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/126.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI2QzVxY2JTd1RuR3hNeko5M2wzblp1THZSbXZOYmlsNlMzSUFoMlVHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9lbnF1aXJ5LmFudWluZm90ZWNoLmNvbSIsInJvdXRlIjoiZ2VuZXJhdGVkOjp2RjVSVXVvU3FCV3p5OUlYIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1782688873),
('WxlR4ZeKX59A4LqeqLr9CRR6FxfEvsIiq0jYNsn3', NULL, '49.156.97.60', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIyRHRMeXhtZmg2Rll3N1I4cUdza04wbUt6Qks5YmJteERKVnl4elZwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9lbnF1aXJ5LmFudWluZm90ZWNoLmNvbVwvYWRtaW5cL2xvZ2luIiwicm91dGUiOiJmaWxhbWVudC5hZG1pbi5hdXRoLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwidXJsIjp7ImludGVuZGVkIjoiaHR0cHM6XC9cL2VucXVpcnkuYW51aW5mb3RlY2guY29tXC9hZG1pbiJ9fQ==', 1782716984),
('x5xhRImInADgJMAGc9F3noyiWpx8f37l2B5fHVGI', '019ee3d4-0d9b-7032-9647-fcbfce6845aa', '106.219.64.167', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ4Y1pmTDlCdFh1bzNZdUhzNnVrdkJXT2Q1alNXU1pmalE5dGJKSXRaIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOiIwMTllZTNkNC0wZDliLTcwMzItOTY0Ny1mY2JmY2U2ODQ1YWEiLCJwYXNzd29yZF9oYXNoX3dlYiI6IjFlMzE2MDE0NTAzMTJkYjk4MjRhM2M1NDlkZmRlMDU3NWM2ZTE5OTc5ZGRiNzUwNzQ1NjIwMTgzZjhlNGFhNTgiLCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2VucXVpcnkuYW51aW5mb3RlY2guY29tXC9hZG1pbiIsInJvdXRlIjoiZmlsYW1lbnQuYWRtaW4ucGFnZXMuZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1782716422);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
('019eaae5-f86b-73b1-9b2c-f931087b8aac', 'Super Admin', 'superadmin@anuinfotech.com', NULL, '$2y$12$KgsSrxR1xXTGFogwr8CyFehLqX/kOzaQPccWcYYfHKkJW8S5vwdgS', NULL, '2026-06-08 18:40:58', '2026-06-19 19:55:21'),
('019eaae5-f86d-72e5-ae00-dfe5ea03cf4c', 'Rakesh Shahi', 'admin@anuinfotech.com', NULL, '$2y$12$LrvoicY0WI8z8X/WFF1h4O9/ymPWBBOU.Y.SKT6QM0vGSdKePQ0pO', NULL, '2026-06-08 18:40:58', '2026-06-08 18:45:40'),
('019eaaea-e4bb-722a-a779-5b85c176529d', 'Khushpreet Singh', 'khushpreet@anuinfotech.com', NULL, '$2y$12$ziWvG874FNUbWgNbIfHsAOLrDxhteq2ICp170zA48XyiAl1loBDXm', NULL, '2026-06-08 18:46:21', '2026-06-08 18:46:21'),
('019ee3d1-8295-7220-9186-d698248f36ed', 'Rahul Wadhera', 'rahulwadhera@anuinfotech.com', NULL, '$2y$12$3wDJgwJ6y2AeKyUgYC.SWebEUor0uYnCbGOguaQXSY0fBP58Gtil.', NULL, '2026-06-19 19:56:59', '2026-06-19 19:56:59'),
('019ee3d2-5423-702e-b367-3945beb42855', 'Rahul', 'rahul_w@anuinfotech.com', NULL, '$2y$12$nEqH/v0a8pnlMfmwJwLyI.cLvTdKj07F7/ggnPBftj2BnRlDADtsW', NULL, '2026-06-19 19:57:52', '2026-06-19 19:57:52'),
('019ee3d3-1231-71a8-90f4-8a15f2663da0', 'China Bedi', 'Chinabedi@anuinfotech.com', NULL, '$2y$12$kk41VU/S8/hO4Fyeek14nukKTP14a8r1dy1ueQjdgdvjXHeJVE2Ye', NULL, '2026-06-19 19:58:41', '2026-06-19 19:58:41'),
('019ee3d4-0d9b-7032-9647-fcbfce6845aa', 'Darshveer kaur', 'darshveerkaur@anuinfotech.com', NULL, '$2y$12$j5YhZwIuFnb8D.qsVe6KwOAVtpT13MfnWKOOjvZl1JGm/bVd5qOAu', 'u932Kdjaop7RCP0Y9TUrhyira3musx9KdNdmGCNON8K8ueyubnHILgXy95jn', '2026-06-19 19:59:45', '2026-06-19 19:59:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `admissions`
--
ALTER TABLE `admissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admissions_admission_no_unique` (`admission_no`),
  ADD UNIQUE KEY `admissions_roll_no_unique` (`roll_no`),
  ADD KEY `admissions_enquiry_id_foreign` (`enquiry_id`);

--
-- Indexes for table `admission_courses`
--
ALTER TABLE `admission_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admission_courses_admission_id_foreign` (`admission_id`),
  ADD KEY `admission_courses_course_id_foreign` (`course_id`),
  ADD KEY `admission_courses_instructor_id_foreign` (`instructor_id`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollment_date_unique` (`admission_course_id`,`attendance_date`),
  ADD KEY `attendances_admission_id_index` (`admission_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificates_certificate_no_unique` (`certificate_no`),
  ADD UNIQUE KEY `certificates_verification_token_unique` (`verification_token`),
  ADD KEY `certificates_admission_id_foreign` (`admission_id`),
  ADD KEY `certificates_course_id_foreign` (`course_id`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_course_code_unique` (`course_code`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enquiries_enquiry_no_unique` (`enquiry_no`),
  ADD KEY `enquiries_taken_by_foreign` (`taken_by`);

--
-- Indexes for table `enquiry_courses`
--
ALTER TABLE `enquiry_courses`
  ADD PRIMARY KEY (`enquiry_id`,`course_id`),
  ADD KEY `enquiry_courses_course_id_foreign` (`course_id`);

--
-- Indexes for table `enquiry_timeline`
--
ALTER TABLE `enquiry_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enquiry_timeline_enquiry_id_foreign` (`enquiry_id`),
  ADD KEY `enquiry_timeline_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `fee_holds`
--
ALTER TABLE `fee_holds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_holds_admission_id_foreign` (`admission_id`),
  ADD KEY `fee_holds_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `fee_installments`
--
ALTER TABLE `fee_installments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_installments_admission_id_foreign` (`admission_id`),
  ADD KEY `fee_installments_admission_course_id_foreign` (`admission_course_id`);

--
-- Indexes for table `fee_payments`
--
ALTER TABLE `fee_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fee_payments_receipt_no_unique` (`receipt_no`),
  ADD KEY `fee_payments_admission_id_foreign` (`admission_id`),
  ADD KEY `fee_payments_fee_installment_id_foreign` (`fee_installment_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admissions`
--
ALTER TABLE `admissions`
  ADD CONSTRAINT `admissions_enquiry_id_foreign` FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `admission_courses`
--
ALTER TABLE `admission_courses`
  ADD CONSTRAINT `admission_courses_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admission_courses_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admission_courses_instructor_id_foreign` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_admission_course_id_foreign` FOREIGN KEY (`admission_course_id`) REFERENCES `admission_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD CONSTRAINT `enquiries_taken_by_foreign` FOREIGN KEY (`taken_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enquiry_courses`
--
ALTER TABLE `enquiry_courses`
  ADD CONSTRAINT `enquiry_courses_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enquiry_courses_enquiry_id_foreign` FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enquiry_timeline`
--
ALTER TABLE `enquiry_timeline`
  ADD CONSTRAINT `enquiry_timeline_enquiry_id_foreign` FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enquiry_timeline_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_holds`
--
ALTER TABLE `fee_holds`
  ADD CONSTRAINT `fee_holds_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_holds_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_installments`
--
ALTER TABLE `fee_installments`
  ADD CONSTRAINT `fee_installments_admission_course_id_foreign` FOREIGN KEY (`admission_course_id`) REFERENCES `admission_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_installments_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_payments`
--
ALTER TABLE `fee_payments`
  ADD CONSTRAINT `fee_payments_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_payments_fee_installment_id_foreign` FOREIGN KEY (`fee_installment_id`) REFERENCES `fee_installments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
