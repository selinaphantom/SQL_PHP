-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:3300
-- 產生時間： 2025-01-07 15:16:37
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `cowpee`
--

-- --------------------------------------------------------

--
-- 資料表結構 `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `category`
--

INSERT INTO `category` (`category_id`, `name`) VALUES
(1, '女性用品'),
(2, '男性用品'),
(3, '電器用品'),
(4, '家具用品'),
(5, '3C產品'),
(6, '食品'),
(7, '模型'),
(8, '玩具'),
(9, '書籍'),
(10, '手機'),
(11, '衣裝');

-- --------------------------------------------------------

--
-- 資料表結構 `coupon`
--

CREATE TABLE `coupon` (
  `ID` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `type` int(11) NOT NULL,
  `discount` double NOT NULL,
  `limit_money` int(11) NOT NULL,
  `code` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `coupon`
--

INSERT INTO `coupon` (`ID`, `member_id`, `start_date`, `end_date`, `type`, `discount`, `limit_money`, `code`) VALUES
(1, 14, '2025-01-07 00:33:07', '2025-01-07 00:33:07', 12, 100, 0, '9963');

-- --------------------------------------------------------

--
-- 資料表結構 `credit_card`
--

CREATE TABLE `credit_card` (
  `customer_id` int(11) NOT NULL,
  `credit_card` int(16) NOT NULL,
  `credit_code` int(16) NOT NULL,
  `credit_card_expiry` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `image_product`
--

CREATE TABLE `image_product` (
  `ID` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `image_product`
--

INSERT INTO `image_product` (`ID`, `image_path`) VALUES
(10, 'img/677bce1c0354c.png'),
(11, 'img/677bce5f5541d.png'),
(12, 'img/677be4d8cc893.jpeg'),
(13, 'img/677c00a51975e.png'),
(14, 'img/677c0b0ee7752.png'),
(15, 'img/product_677c2317099b64.28626945.png'),
(16, 'img/product_677c8404c17410.59062610.png'),
(17, 'img/product_677c742ca58090.06858049.jpeg'),
(18, 'img/product_677c2348a2a4f1.61656715.png'),
(19, 'img/product_677c743ab85f38.95219406.jpeg'),
(20, 'img/product_677c744a345af1.50479948.jpeg'),
(21, 'img/677c77b0c7ca8.png');

-- --------------------------------------------------------

--
-- 資料表結構 `list_items`
--

CREATE TABLE `list_items` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quatity` int(11) NOT NULL,
  `note` varchar(255) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `list_items`
--

INSERT INTO `list_items` (`order_id`, `product_id`, `quatity`, `note`, `customer_id`) VALUES
(32, 17, 1, '', 13),
(33, 15, 2, '', 13),
(34, 16, 1, '', 13);

-- --------------------------------------------------------

--
-- 資料表結構 `list_liked`
--

CREATE TABLE `list_liked` (
  `customer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `list_liked`
--

INSERT INTO `list_liked` (`customer_id`, `seller_id`) VALUES
(14, 13);

-- --------------------------------------------------------

--
-- 資料表結構 `list_ratings`
--

CREATE TABLE `list_ratings` (
  `customer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `stars` int(11) NOT NULL,
  `rating_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `member`
--

CREATE TABLE `member` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `member_status` varchar(11) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `verified` varchar(255) NOT NULL,
  `permission` int(11) NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `authority` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `member`
--

INSERT INTO `member` (`ID`, `Name`, `username`, `password`, `member_status`, `phone`, `email`, `verified`, `permission`, `register_date`, `authority`, `token`) VALUES
(13, '往敏倫', 'asdlove5219', 'ASD', 'active', '0909663797', 'a0917169636a@gmail.com', '0', 0, '2025-01-07 01:34:05', '賣家權限', ''),
(14, '123', '123', '123', 'active', '0970663797', 'asdlove0826@gmail.com', '0', 0, '2025-01-06 11:33:26', '賣家權限', ''),
(15, '葉大同', 'qqwe901018', '123', 'active', '0909090909', '111590016@ntut.edu.tw', '0', 0, '2025-01-06 11:11:01', '賣家權限', ''),
(16, '11111', 'iop1234', '123asdzxc', 'active', '', 'david20031229@gmail.com', '0', 0, '2025-01-07 00:40:10', '', ''),
(17, '123', '12345678', 'llllllll', 'active', '0911283122', 'leo.o426@msa.hinet.net', '0', 0, '2025-01-07 00:42:40', '賣家權限', ''),
(18, '京城五', 'admin', '1234', 'active', '0909090901', 't1111590016@ntut.org.tw', '0', 0, '2025-01-07 14:12:29', '賣家權限', '');

-- --------------------------------------------------------

--
-- 資料表結構 `order_`
--

CREATE TABLE `order_` (
  `ID` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `coupon_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `ship_date` datetime NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `fee` int(11) NOT NULL,
  `taking_method` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `order_`
--

INSERT INTO `order_` (`ID`, `customer_id`, `coupon_id`, `seller_id`, `order_date`, `ship_date`, `payment_method`, `status`, `address`, `fee`, `taking_method`) VALUES
(1, 13, 0, 13, '2025-01-07 07:25:09', '2025-01-07 07:25:09', 'paypal', 0, 'Taiwan', 2, 'CowPee速送6小時'),
(2, 13, 0, 13, '2025-01-07 07:25:53', '2025-01-07 07:25:53', 'credit_card', 0, 'Taiwan', 123, 'CowPee速送6小時'),
(3, 13, 0, 0, '2025-01-07 07:26:16', '2025-01-07 07:26:16', 'credit_card', 0, 'Taiwan', 0, 'CowPee速送6小時'),
(4, 13, 0, 13, '2025-01-07 07:28:36', '2025-01-07 07:28:36', 'credit_card', 0, '新福十六街', 2, 'CowPee速送6小時'),
(5, 13, 0, 13, '2025-01-07 07:29:15', '2025-01-07 07:29:15', 'paypal', 0, 'Taiwan', 123, 'CowPee速送6小時'),
(6, 13, 0, 13, '2025-01-07 07:30:17', '2025-01-07 07:30:17', 'cash_on_delivery', 0, 'Taiwan', 2, 'family_mart'),
(7, 13, 0, 13, '2025-01-07 07:32:23', '2025-01-07 07:32:23', 'paypal', 0, 'Taiwan', 2, 'CowPee速送6小時'),
(8, 13, 0, 13, '2025-01-07 08:39:38', '2025-01-07 08:39:38', 'paypal', 0, 'Taiwan', 2, 'CowPee速送6小時'),
(9, 13, 0, 13, '2025-01-07 09:33:16', '2025-01-07 09:33:16', 'atm', 0, 'Taiwan', 2, 'CowPee速送6小時');

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `Seller_id` int(11) NOT NULL,
  `Product_name` varchar(255) NOT NULL,
  `Price` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Publish_data` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Mdata` datetime NOT NULL,
  `Status` int(11) NOT NULL,
  `Is_deleted` tinyint(1) NOT NULL,
  `Category_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`product_id`, `Seller_id`, `Product_name`, `Price`, `num`, `Description`, `Publish_data`, `Mdata`, `Status`, `Is_deleted`, `Category_id`) VALUES
(15, 13, '攻略A', 123, 122, '123', '2025-01-07 00:31:20', '2025-01-06 18:07:09', 0, 0, '4'),
(16, 13, '好吃的', 2, -7, '2', '2025-01-07 00:30:54', '2025-01-06 18:10:47', 0, 0, '11'),
(17, 13, '123', 123, 113, '我愛吃甜甜圈', '2025-01-06 22:45:01', '2025-01-06 18:18:33', 0, 0, '1'),
(18, 13, '123', 123333, 50, '123', '2025-01-06 18:42:07', '2025-01-06 18:22:29', 0, 0, '1'),
(19, 13, '123', 123, 123, '123', '2025-01-06 11:38:33', '2025-01-06 19:38:33', 0, 0, '11'),
(20, 13, '1231', 123, 123, '123', '2025-01-06 11:38:49', '2025-01-06 19:38:49', 0, 0, '1'),
(21, 13, '123', 123, 1, '123', '2025-01-06 17:39:12', '2025-01-07 01:39:12', 0, 0, '8');

-- --------------------------------------------------------

--
-- 資料表結構 `seller`
--

CREATE TABLE `seller` (
  `member_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `specified_product`
--

CREATE TABLE `specified_product` (
  `coupon_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`name`),
  ADD UNIQUE KEY `category_id` (`category_id`);

--
-- 資料表索引 `image_product`
--
ALTER TABLE `image_product`
  ADD PRIMARY KEY (`ID`);

--
-- 資料表索引 `list_items`
--
ALTER TABLE `list_items`
  ADD PRIMARY KEY (`order_id`,`product_id`);

--
-- 資料表索引 `list_liked`
--
ALTER TABLE `list_liked`
  ADD PRIMARY KEY (`customer_id`,`seller_id`);

--
-- 資料表索引 `list_ratings`
--
ALTER TABLE `list_ratings`
  ADD PRIMARY KEY (`customer_id`,`seller_id`);

--
-- 資料表索引 `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 資料表索引 `order_`
--
ALTER TABLE `order_`
  ADD PRIMARY KEY (`ID`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`,`Seller_id`);

--
-- 資料表索引 `seller`
--
ALTER TABLE `seller`
  ADD PRIMARY KEY (`member_id`);

--
-- 資料表索引 `specified_product`
--
ALTER TABLE `specified_product`
  ADD PRIMARY KEY (`coupon_id`,`product_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `list_items`
--
ALTER TABLE `list_items`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `member`
--
ALTER TABLE `member`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order_`
--
ALTER TABLE `order_`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
