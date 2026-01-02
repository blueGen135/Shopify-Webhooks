-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 12, 2025 at 06:21 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u544857502_FS9Vy`
--

-- --------------------------------------------------------

--
-- Table structure for table `shopify_customers`
--

CREATE TABLE `shopify_customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shopify_customer_id` bigint(20) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `verified_email` tinyint(1) NOT NULL DEFAULT 0,
  `state` varchar(255) DEFAULT NULL,
  `orders_count` int(11) NOT NULL DEFAULT 0,
  `total_spent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) DEFAULT NULL,
  `accepts_marketing` tinyint(1) NOT NULL DEFAULT 0,
  `addresses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`addresses`)),
  `default_address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`default_address`)),
  `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_response`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shopify_customers`
--

INSERT INTO `shopify_customers` (`id`, `shopify_customer_id`, `first_name`, `last_name`, `email`, `phone`, `verified_email`, `state`, `orders_count`, `total_spent`, `currency`, `accepts_marketing`, `addresses`, `default_address`, `raw_response`, `created_at`, `updated_at`) VALUES
(1, 9686469116217, 'Raj', 'K', 'abap.pal@gmail.com', '+918958985898', 1, 'disabled', 12, 83454.32, 'INR', 0, '[{\"id\":11581102358841,\"customer_id\":9686469116217,\"first_name\":\"rajinder\",\"last_name\":\"pal\",\"company\":null,\"address1\":\"Fattowal Road\",\"address2\":null,\"city\":\"Fattowal\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146113\",\"phone\":null,\"name\":\"rajinder pal\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":true},{\"id\":11515506229561,\"customer_id\":9686469116217,\"first_name\":\"Raaj\",\"last_name\":\"P\",\"company\":null,\"address1\":\"Model Town Police Station Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raaj P\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11515499315513,\"customer_id\":9686469116217,\"first_name\":\"Raaj\",\"last_name\":\"k\",\"company\":null,\"address1\":\"Mehta Park Road Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raaj k\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11515489911097,\"customer_id\":9686469116217,\"first_name\":\"Raaj\",\"last_name\":\"k\",\"company\":null,\"address1\":\"Model Town Road\",\"address2\":null,\"city\":\"Urmar Tanda\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"144204\",\"phone\":null,\"name\":\"Raaj k\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11515479425337,\"customer_id\":9686469116217,\"first_name\":\"Raak\",\"last_name\":\"k\",\"company\":null,\"address1\":\"Hotel Presidency Bus Stand Road Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raak k\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11514350731577,\"customer_id\":9686469116217,\"first_name\":\"Raaj\",\"last_name\":\"K\",\"company\":null,\"address1\":\"Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raaj K\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11514319896889,\"customer_id\":9686469116217,\"first_name\":\"Raj\",\"last_name\":\"K\",\"company\":null,\"address1\":\"Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raj K\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false}]', '{\"id\":11581102358841,\"customer_id\":9686469116217,\"first_name\":\"rajinder\",\"last_name\":\"pal\",\"company\":null,\"address1\":\"Fattowal Road\",\"address2\":null,\"city\":\"Fattowal\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146113\",\"phone\":null,\"name\":\"rajinder pal\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":true}', '{\"id\":9686469116217,\"created_at\":\"2025-11-22T12:39:57-05:00\",\"updated_at\":\"2025-12-11T02:50:29-05:00\",\"first_name\":\"Raj\",\"last_name\":\"K\",\"orders_count\":12,\"state\":\"disabled\",\"total_spent\":\"83454.32\",\"last_order_id\":6804979777849,\"note\":null,\"verified_email\":true,\"multipass_identifier\":null,\"tax_exempt\":false,\"tags\":\"\",\"last_order_name\":\"#1026\",\"email\":\"abap.pal@gmail.com\",\"phone\":\"+918958985898\",\"currency\":\"INR\",\"addresses\":[{\"id\":11581102358841,\"customer_id\":9686469116217,\"first_name\":\"rajinder\",\"last_name\":\"pal\",\"company\":null,\"address1\":\"Fattowal Road\",\"address2\":null,\"city\":\"Fattowal\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146113\",\"phone\":null,\"name\":\"rajinder pal\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":true},{\"id\":11515506229561,\"customer_id\":9686469116217,\"first_name\":\"Raaj\",\"last_name\":\"P\",\"company\":null,\"address1\":\"Model Town Police Station Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raaj P\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11515499315513,\"customer_id\":9686469116217,\"first_name\":\"Raaj\",\"last_name\":\"k\",\"company\":null,\"address1\":\"Mehta Park Road Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raaj k\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11515489911097,\"customer_id\":9686469116217,\"first_name\":\"Raaj\",\"last_name\":\"k\",\"company\":null,\"address1\":\"Model Town Road\",\"address2\":null,\"city\":\"Urmar Tanda\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"144204\",\"phone\":null,\"name\":\"Raaj k\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11515479425337,\"customer_id\":9686469116217,\"first_name\":\"Raak\",\"last_name\":\"k\",\"company\":null,\"address1\":\"Hotel Presidency Bus Stand Road Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raak k\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11514350731577,\"customer_id\":9686469116217,\"first_name\":\"Raaj\",\"last_name\":\"K\",\"company\":null,\"address1\":\"Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raaj K\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false},{\"id\":11514319896889,\"customer_id\":9686469116217,\"first_name\":\"Raj\",\"last_name\":\"K\",\"company\":null,\"address1\":\"Model Town\",\"address2\":null,\"city\":\"Hoshiarpur\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146001\",\"phone\":null,\"name\":\"Raj K\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":false}],\"tax_exemptions\":[],\"email_marketing_consent\":{\"state\":\"not_subscribed\",\"opt_in_level\":\"single_opt_in\",\"consent_updated_at\":null},\"sms_marketing_consent\":{\"state\":\"not_subscribed\",\"opt_in_level\":\"single_opt_in\",\"consent_updated_at\":null,\"consent_collected_from\":\"SHOPIFY\"},\"admin_graphql_api_id\":\"gid:\\/\\/shopify\\/Customer\\/9686469116217\",\"default_address\":{\"id\":11581102358841,\"customer_id\":9686469116217,\"first_name\":\"rajinder\",\"last_name\":\"pal\",\"company\":null,\"address1\":\"Fattowal Road\",\"address2\":null,\"city\":\"Fattowal\",\"province\":\"Punjab\",\"country\":\"India\",\"zip\":\"146113\",\"phone\":null,\"name\":\"rajinder pal\",\"province_code\":\"PB\",\"country_code\":\"IN\",\"country_name\":\"India\",\"default\":true}}', '2025-12-11 16:02:19', '2025-12-11 16:02:19'),
(2, 7204104732985, 'Russell', 'Winfield', 'russel.winfield@example.com', '+16135550135', 1, 'disabled', 4, 6656.19, 'INR', 0, '[{\"id\":9416957591865,\"customer_id\":7204104732985,\"first_name\":\"Russell\",\"last_name\":\"Winfield\",\"company\":\"Company Name\",\"address1\":\"105 Victoria St\",\"address2\":null,\"city\":\"Toronto\",\"province\":null,\"country\":\"Canada\",\"zip\":\"M5C1N7\",\"phone\":null,\"name\":\"Russell Winfield\",\"province_code\":null,\"country_code\":\"CA\",\"country_name\":\"Canada\",\"default\":true}]', '{\"id\":9416957591865,\"customer_id\":7204104732985,\"first_name\":\"Russell\",\"last_name\":\"Winfield\",\"company\":\"Company Name\",\"address1\":\"105 Victoria St\",\"address2\":null,\"city\":\"Toronto\",\"province\":null,\"country\":\"Canada\",\"zip\":\"M5C1N7\",\"phone\":null,\"name\":\"Russell Winfield\",\"province_code\":null,\"country_code\":\"CA\",\"country_name\":\"Canada\",\"default\":true}', '{\"id\":7204104732985,\"created_at\":\"2023-08-22T06:03:36-04:00\",\"updated_at\":\"2023-08-22T06:03:42-04:00\",\"first_name\":\"Russell\",\"last_name\":\"Winfield\",\"orders_count\":4,\"state\":\"disabled\",\"total_spent\":\"6656.19\",\"last_order_id\":5520766959929,\"note\":\"This customer is created with most available fields\",\"verified_email\":true,\"multipass_identifier\":null,\"tax_exempt\":false,\"tags\":\"VIP\",\"last_order_name\":\"#1009\",\"email\":\"russel.winfield@example.com\",\"phone\":\"+16135550135\",\"currency\":\"INR\",\"addresses\":[{\"id\":9416957591865,\"customer_id\":7204104732985,\"first_name\":\"Russell\",\"last_name\":\"Winfield\",\"company\":\"Company Name\",\"address1\":\"105 Victoria St\",\"address2\":null,\"city\":\"Toronto\",\"province\":null,\"country\":\"Canada\",\"zip\":\"M5C1N7\",\"phone\":null,\"name\":\"Russell Winfield\",\"province_code\":null,\"country_code\":\"CA\",\"country_name\":\"Canada\",\"default\":true}],\"tax_exemptions\":[],\"email_marketing_consent\":{\"state\":\"not_subscribed\",\"opt_in_level\":\"single_opt_in\",\"consent_updated_at\":null},\"sms_marketing_consent\":{\"state\":\"not_subscribed\",\"opt_in_level\":\"unknown\",\"consent_updated_at\":null,\"consent_collected_from\":\"OTHER\"},\"admin_graphql_api_id\":\"gid:\\/\\/shopify\\/Customer\\/7204104732985\",\"default_address\":{\"id\":9416957591865,\"customer_id\":7204104732985,\"first_name\":\"Russell\",\"last_name\":\"Winfield\",\"company\":\"Company Name\",\"address1\":\"105 Victoria St\",\"address2\":null,\"city\":\"Toronto\",\"province\":null,\"country\":\"Canada\",\"zip\":\"M5C1N7\",\"phone\":null,\"name\":\"Russell Winfield\",\"province_code\":null,\"country_code\":\"CA\",\"country_name\":\"Canada\",\"default\":true}}', '2025-12-11 16:02:19', '2025-12-11 16:02:19'),
(3, 7204104700217, 'Ayumu', 'Hirano', 'ayumu.hirano@example.com', '+16135550127', 1, 'disabled', 1, 64.31, 'INR', 0, '[]', NULL, '{\"id\":7204104700217,\"created_at\":\"2023-08-22T06:03:36-04:00\",\"updated_at\":\"2025-12-10T01:33:18-05:00\",\"first_name\":\"Ayumu\",\"last_name\":\"Hirano\",\"orders_count\":1,\"state\":\"disabled\",\"total_spent\":\"64.31\",\"last_order_id\":6802945605945,\"note\":null,\"verified_email\":true,\"multipass_identifier\":null,\"tax_exempt\":false,\"tags\":\"\",\"last_order_name\":\"#1020\",\"email\":\"ayumu.hirano@example.com\",\"phone\":\"+16135550127\",\"currency\":\"INR\",\"addresses\":[],\"tax_exemptions\":[],\"email_marketing_consent\":{\"state\":\"not_subscribed\",\"opt_in_level\":\"single_opt_in\",\"consent_updated_at\":null},\"sms_marketing_consent\":{\"state\":\"not_subscribed\",\"opt_in_level\":\"single_opt_in\",\"consent_updated_at\":null,\"consent_collected_from\":\"OTHER\"},\"admin_graphql_api_id\":\"gid:\\/\\/shopify\\/Customer\\/7204104700217\"}', '2025-12-11 16:02:19', '2025-12-11 16:02:19'),
(4, 7204104667449, 'Karine', 'Ruby', 'karine.ruby@example.com', '+16135550142', 1, 'disabled', 2, 5850.00, 'INR', 0, '[]', NULL, '{\"id\":7204104667449,\"created_at\":\"2023-08-22T06:03:36-04:00\",\"updated_at\":\"2025-12-11T01:49:44-05:00\",\"first_name\":\"Karine\",\"last_name\":\"Ruby\",\"orders_count\":2,\"state\":\"disabled\",\"total_spent\":\"5850.00\",\"last_order_id\":6804953334073,\"note\":null,\"verified_email\":true,\"multipass_identifier\":null,\"tax_exempt\":false,\"tags\":\"\",\"last_order_name\":\"#1025\",\"email\":\"karine.ruby@example.com\",\"phone\":\"+16135550142\",\"currency\":\"INR\",\"addresses\":[],\"tax_exemptions\":[],\"email_marketing_consent\":{\"state\":\"not_subscribed\",\"opt_in_level\":\"single_opt_in\",\"consent_updated_at\":null},\"sms_marketing_consent\":{\"state\":\"not_subscribed\",\"opt_in_level\":\"single_opt_in\",\"consent_updated_at\":null,\"consent_collected_from\":\"OTHER\"},\"admin_graphql_api_id\":\"gid:\\/\\/shopify\\/Customer\\/7204104667449\"}', '2025-12-11 16:02:19', '2025-12-11 16:02:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shopify_customers`
--
ALTER TABLE `shopify_customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shopify_customers_shopify_customer_id_unique` (`shopify_customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shopify_customers`
--
ALTER TABLE `shopify_customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
