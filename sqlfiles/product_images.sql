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
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `src` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `src`, `created_at`, `updated_at`) VALUES
(107, 27, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/Main_b9e0da7f-db89-4d41-83f0-7f417b02831d.jpg?v=1692698618', '2025-12-09 14:53:48', '2025-12-09 14:53:48'),
(108, 28, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/Main_52f8e304-92d9-4a36-82af-50df8fe31c69.jpg?v=1692698618', '2025-12-09 14:53:48', '2025-12-09 14:53:48'),
(149, 109, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/campust-black-1.jpg?v=1765214916', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(150, 109, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/campus-gray-1.jpg?v=1765214916', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(151, 109, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/campust-blue-1.jpg?v=1765214916', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(152, 109, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/campust-black-2.jpg?v=1765214915', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(153, 109, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/campust-blue-2.jpg?v=1765214916', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(154, 109, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/campust-gray-2.jpg?v=1765214916', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(159, 132, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/casual-shoe-black.jpg?v=1765214926', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(160, 132, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/casual-shoes-brown_c1cd3226-195e-46be-b26f-23cdf1de3bed.jpg?v=1765214926', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(161, 132, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/casual-shoe-1.jpg?v=1765214926', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(162, 6, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/pocket-watch-on-black.jpg?v=1693102585', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(163, 7, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/young-woman-exercising-in-workout-clothes-against-brick-wall.jpg?v=1693102548', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(164, 7, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/young-woman-standing-in-yoga-pose.jpg?v=1693102548', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(165, 7, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/young-woman-against-exposed-brick-doing-yoga.jpg?v=1693102548', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(166, 129, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/cross-flex-shoes_b5ed7e06-b185-4a7b-af55-44bfbc625d54.jpg?v=1765214925', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(167, 8, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/elephant-earrings.jpg?v=1693102627', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(168, 8, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/galaxy-earrings.jpg?v=1693102627', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(169, 8, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/boho-earrings.jpg?v=1693102627', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(170, 106, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/eva-2.jpg?v=1765214914', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(171, 106, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/eva-1.jpg?v=1765214914', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(172, 114, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/Running-Shoes-1.jpg?v=1765214918', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(173, 97, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/olive-2.jpg?v=1765214909', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(174, 9, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/back-view-mens-grey-long-sleeve.jpg?v=1693102559', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(175, 9, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/man-working-at-desk.jpg?v=1693102559', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(176, 9, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/back-of-red-plaid-shirt.jpg?v=1693102559', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(177, 48, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/StrideX-Performance-Runner-2.jpg?v=1764947583', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(178, 90, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/pexels-craytive-1464625.jpg?v=1765214906', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(179, 46, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/pvtaeApV.jpg?v=1764946793', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(180, 52, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/cross-flex-shoes.jpg?v=1764955765', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(181, 49, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/mark-casual-shoes-6.jpg?v=1764953126', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(182, 55, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/mark-casual-shoes-2.jpg?v=1765041142', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(183, 126, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/mark-casual-shoes-6_3f56e24f-1eb3-48f6-9946-196544d0a17e.jpg?v=1765214924', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(184, 126, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/mark-casual-shoes-5.jpg?v=1765214923', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(185, 126, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/mark-casual-shoes-4.jpg?v=1765214923', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(186, 126, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/mark-casual-shoes-3.jpg?v=1765214924', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(187, 126, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/mark-casual-shoes-2_87a412a0-01bc-4383-b742-3522aa8255a0.jpg?v=1765214924', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(188, 126, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/mark-casual-shoes-1.jpg?v=1765214923', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(189, 88, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/cas.jpg?v=1765214905', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(190, 10, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/boots-in-autumn-leaves.jpg?v=1693102593', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(191, 10, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/hiker-looks-down-at-boots-and-leaves.jpg?v=1693102593', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(192, 10, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/steel-toed-boots.jpg?v=1693102594', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(193, 11, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/light-up-shoes-men.jpg?v=1693102569', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(194, 11, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/close-up-of-a-hand-in-a-jean-pocket.jpg?v=1693102569', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(195, 11, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/mens-fashion-hand-in-jeans-pocket_1f956703-f3de-41aa-adeb-bffe5f5f3de5.jpg?v=1693102569', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(196, 12, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/purple-gemstone-necklace.jpg?v=1693102614', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(197, 12, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/womens-gold-necklace.jpg?v=1693102614', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(198, 13, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/a-close-up-of-luxury-watch.jpg?v=1693102587', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(199, 124, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/Oxfords-Casual-Shoes.jpg?v=1765214922', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(200, 14, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/but-do-i-like-it.jpg?v=1693102542', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(201, 82, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/vans.jpg?v=1765214900', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(202, 112, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/Runningshoes4_1_0d2852c8-b53e-47a3-8750-80c3e02578a2.jpg?v=1765214917', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(203, 112, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/RunningShoes4_88127836-8a88-430d-b1d6-d3d00dbff032.jpg?v=1765214917', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(204, 15, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/leather-jacket-and-tea.jpg?v=1693102535', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(205, 118, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/Retro-Business-Classic-Dress-Shoes.jpg?v=1765214920', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(206, 16, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/wedding-vows-and-wedding-rings.jpg?v=1693102632', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(207, 16, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/couple-embraces.jpg?v=1693102632', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(208, 16, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/married-couple-holding-hands.jpg?v=1693102632', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(209, 17, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/mens-fashion-hand-in-jeans-pocket.jpg?v=1693102577', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(210, 17, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/mens-fashion-denim-and-tshirt.jpg?v=1693102577', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(211, 17, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/casual-urban-mens-fashion-jeans.jpg?v=1693102577', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(212, 18, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/man-rolls-up-sleeves.jpg?v=1693102554', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(213, 18, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/mens-fashion-close-up-patterned-shirt-red-wine.jpg?v=1693102554', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(214, 18, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/fashionable-mens-shirt-modeled.jpg?v=1693102554', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(215, 19, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/boots-on-blue.jpg?v=1693102598', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(216, 19, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/boots-on-a-couch.jpg?v=1693102598', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(217, 19, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/heels-and-flowers.jpg?v=1693102598', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(218, 20, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/blank-1886008_640.png?v=1693102520', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(219, 20, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/blank-1886001_640.png?v=1693102520', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(220, 20, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/blank-1886013_640.png?v=1693102520', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(221, 20, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/blank-1886007_640.png?v=1693102520', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(222, 21, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/anchor-bracelet-mens.jpg?v=1693102618', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(223, 21, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/stacked-bracelets-set.jpg?v=1693102618', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(224, 21, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/chakra-bracelet-product-shot.jpg?v=1693102618', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(225, 22, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/necklace-2.jpg?v=1693102581', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(226, 23, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/necklace-3.jpg?v=1693102583', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(227, 135, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/StrideX-Performance-Runner-1.jpg?v=1765214928', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(228, 135, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/StrideX-Performance-Runner-3.jpg?v=1765214927', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(229, 135, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/StrideX-Performance-Runner-2_be4d7737-2dbe-4f79-8c17-290fa39a756c.jpg?v=1765214927', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(230, 24, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/cobalt-blue-t-shirt.jpg?v=1693102638', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(231, 24, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/purple-t-shirt.jpg?v=1693102638', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(232, 24, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/green-t-shirt.jpg?v=1693102638', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(233, 24, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/red-t-shirt.jpg?v=1693102638', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(234, 25, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/white-tshirt-template.jpg?v=1693102564', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(235, 25, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/casual-fashion-serious-leap.jpg?v=1693102564', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(236, 25, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/mens-fashion-close-up-shirt-tucked-in-leaning.jpg?v=1693102564', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(237, 26, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/professional-woman.jpg?v=1693102533', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(238, 26, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/fitness-woman-touching-hair.jpg?v=1693102533', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(239, 303, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/Screenshot2025-11-24224359.png?v=1765346245', '2025-12-10 16:16:42', '2025-12-10 16:16:42'),
(240, 80, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/av.jpg?v=1765214903', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(241, 1, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/woman-outside-brownstone.jpg?v=1693102543', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(242, 84, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/gray-shoes.jpg?v=1765214898', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(243, 81, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/nk.jpg?v=1765214901', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(244, 2, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/man-models-fall-fashion-in-park.jpg?v=1693102539', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(245, 3, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/mens-anchor-bracelet.jpg?v=1693102623', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(246, 5, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/womens-blouse-sleeve-detail.jpg?v=1693102573', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(247, 140, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/close-up-of-watch-signing-under-neon-lights.jpg?v=1693102589', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(248, 5, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/mens-fashion-close-up-shirt-tucked-in-leaning_1.jpg?v=1693102573', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(249, 3, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/man-woman-holding-hands.jpg?v=1693102623', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(250, 5, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/wearing-sneakers-on-road.jpg?v=1693102573', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(251, 84, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/black-shorts-shoes.jpg?v=1765214898', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(252, 3, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/holding-hands.jpg?v=1693102623', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(253, 2, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/mens-fashion-model-autumn-season.jpg?v=1693102539', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(254, 84, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/blue-shorts-shoes.jpg?v=1765214899', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(255, 84, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/gray-short-shoes.jpg?v=1765214898', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(256, 84, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/orange-sports-shoes.jpg?v=1765214898', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(257, 84, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/files/shorts-shoes-red.jpg?v=1765214899', '2025-12-11 12:18:13', '2025-12-11 12:18:13'),
(258, 2, 'https://cdn.shopify.com/s/files/1/0819/5614/3417/products/mens-casual-fashion-denim-and-sneakers.jpg?v=1693102539', '2025-12-11 12:18:13', '2025-12-11 12:18:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
