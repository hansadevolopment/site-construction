-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2023 at 05:44 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `seelan_finance`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_type`
--

CREATE TABLE `account_type` (
  `at_id` int(11) NOT NULL,
  `at_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `account_type`
--

INSERT INTO `account_type` (`at_id`, `at_name`) VALUES
(1, 'Assets'),
(2, 'Expenses'),
(3, 'Liabilities'),
(4, 'Income'),
(5, 'Equity');

-- --------------------------------------------------------

--
-- Table structure for table `acc_type`
--

CREATE TABLE `acc_type` (
  `acc_id` int(11) NOT NULL,
  `short_name` varchar(5) NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `acc_type`
--

INSERT INTO `acc_type` (`acc_id`, `short_name`, `name`) VALUES
(1, 'Dr', 'Debit'),
(2, 'Cr', 'Credit');

-- --------------------------------------------------------

--
-- Table structure for table `controll_accounts`
--

CREATE TABLE `controll_accounts` (
  `ca_id` int(11) NOT NULL,
  `ma_id` int(11) NOT NULL,
  `ca_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `controll_accounts`
--

INSERT INTO `controll_accounts` (`ca_id`, `ma_id`, `ca_name`) VALUES
(1010001, 101, 'Land'),
(1010002, 101, 'Vehicle'),
(1010003, 101, 'Equipment');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genaral_ledger`
--

CREATE TABLE `genaral_ledger` (
  `gl_log_id` int(11) NOT NULL,
  `gl_entry_id` int(11) NOT NULL,
  `gl_entry_sub_id` int(11) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `sa_id` bigint(20) NOT NULL,
  `sa_name` varchar(30) NOT NULL,
  `remark` varchar(200) NOT NULL,
  `type` char(1) NOT NULL,
  `amount` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entry`
--

CREATE TABLE `journal_entry` (
  `je_id` int(11) NOT NULL,
  `je_date` date NOT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `debit_amount` decimal(18,2) NOT NULL,
  `credit_amount` decimal(18,2) NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entry_detail`
--

CREATE TABLE `journal_entry_detail` (
  `jed_id` int(11) NOT NULL,
  `je_id` int(11) NOT NULL,
  `sa_id` bigint(20) NOT NULL,
  `sa_name` varchar(30) NOT NULL,
  `acc_type_id` int(11) NOT NULL,
  `amount` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `main_account`
--

CREATE TABLE `main_account` (
  `ma_id` int(11) NOT NULL,
  `at_id` int(11) NOT NULL,
  `ma_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `main_account`
--

INSERT INTO `main_account` (`ma_id`, `at_id`, `ma_name`) VALUES
(101, 1, 'Fixed Assets'),
(102, 1, 'Current Assets');

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_accounts`
--

CREATE TABLE `sub_accounts` (
  `sa_id` bigint(20) NOT NULL,
  `ca_id` int(11) NOT NULL,
  `sa_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sub_accounts`
--

INSERT INTO `sub_accounts` (`sa_id`, `ca_id`, `sa_name`) VALUES
(101000100001, 1010001, 'Colombo Land'),
(101000100002, 1010001, 'Galle Land');

-- --------------------------------------------------------

--
-- Table structure for table `tmp_journal_entry`
--

CREATE TABLE `tmp_journal_entry` (
  `tmp_je_id` int(11) NOT NULL,
  `je_id` varchar(25) NOT NULL,
  `je_date` datetime NOT NULL,
  `remark` varchar(200) DEFAULT NULL,
  `sa_id` bigint(20) NOT NULL,
  `sa_name` varchar(30) NOT NULL,
  `acc_id` int(11) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `saved_by` int(11) NOT NULL,
  `saved_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tmp_journal_entry`
--

INSERT INTO `tmp_journal_entry` (`tmp_je_id`, `je_id`, `je_date`, `remark`, `sa_id`, `sa_name`, `acc_id`, `amount`, `saved_by`, `saved_on`) VALUES
(1, '#Auto#', '2023-01-08 00:00:00', 'First Remark # 1', 101000100001, 'Colombo Land', 1, '5000.00', 1, '2023-01-08 07:53:09'),
(2, '#Auto#', '2023-01-08 00:00:00', 'First Remark # 1', 101000100002, 'Galle Land', 2, '5000.00', 1, '2023-01-08 07:53:51'),
(3, '#Auto#', '2023-01-08 00:00:00', 'First Remark # 1', 101000100001, 'Colombo Land', 2, '125000.00', 1, '2023-01-08 13:48:21'),
(4, '#Auto#', '2023-01-08 00:00:00', 'First Remark # 1', 101000100002, 'Galle Land', 1, '125000.00', 1, '2023-01-08 13:59:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
(1, 'Hansa Jayaratne', 'charanahansa@gmail.com', NULL, '$2y$10$mOyAoTKkPoDdUcNDslec8.UTYUV6KSyYeyixLzUoOx7yGHKqJsebW', NULL, '2022-12-25 06:35:30', '2022-12-25 06:35:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_type`
--
ALTER TABLE `account_type`
  ADD PRIMARY KEY (`at_id`);

--
-- Indexes for table `acc_type`
--
ALTER TABLE `acc_type`
  ADD PRIMARY KEY (`acc_id`);

--
-- Indexes for table `controll_accounts`
--
ALTER TABLE `controll_accounts`
  ADD PRIMARY KEY (`ca_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `genaral_ledger`
--
ALTER TABLE `genaral_ledger`
  ADD PRIMARY KEY (`gl_log_id`);

--
-- Indexes for table `journal_entry`
--
ALTER TABLE `journal_entry`
  ADD PRIMARY KEY (`je_id`);

--
-- Indexes for table `journal_entry_detail`
--
ALTER TABLE `journal_entry_detail`
  ADD PRIMARY KEY (`jed_id`);

--
-- Indexes for table `main_account`
--
ALTER TABLE `main_account`
  ADD PRIMARY KEY (`ma_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `sub_accounts`
--
ALTER TABLE `sub_accounts`
  ADD PRIMARY KEY (`sa_id`);

--
-- Indexes for table `tmp_journal_entry`
--
ALTER TABLE `tmp_journal_entry`
  ADD PRIMARY KEY (`tmp_je_id`);

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
-- AUTO_INCREMENT for table `account_type`
--
ALTER TABLE `account_type`
  MODIFY `at_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `acc_type`
--
ALTER TABLE `acc_type`
  MODIFY `acc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `controll_accounts`
--
ALTER TABLE `controll_accounts`
  MODIFY `ca_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1010004;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genaral_ledger`
--
ALTER TABLE `genaral_ledger`
  MODIFY `gl_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entry`
--
ALTER TABLE `journal_entry`
  MODIFY `je_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entry_detail`
--
ALTER TABLE `journal_entry_detail`
  MODIFY `jed_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `main_account`
--
ALTER TABLE `main_account`
  MODIFY `ma_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_accounts`
--
ALTER TABLE `sub_accounts`
  MODIFY `sa_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101000100003;

--
-- AUTO_INCREMENT for table `tmp_journal_entry`
--
ALTER TABLE `tmp_journal_entry`
  MODIFY `tmp_je_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
