-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:3300
-- 產生時間： 2025-01-06 15:22:24
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
  `limit_money` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `credit_card`
--

CREATE TABLE `credit_card` (
  `customer_id` int(11) NOT NULL,
  `credit_card` int(11) NOT NULL,
  `credit_code` int(11) NOT NULL
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
(12, 'img/677be4d8cc893.jpeg');

-- --------------------------------------------------------

--
-- 資料表結構 `list_items`
--

CREATE TABLE `list_items` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quatity` int(11) NOT NULL,
  `note` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `list_liked`
--

CREATE TABLE `list_liked` (
  `customer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(13, '蘇李冠穎', 'asdlove5219', 'asdlove0826', 'active', '0909663797', 'a0917169636a@gmail.com', '0', 0, '2025-01-06 11:37:10', '賣家權限', ''),
(14, '123', '123', '123', 'active', '0970663797', 'asdlove0826@gmail.com', '0', 0, '2025-01-06 11:33:26', '賣家權限', ''),
(15, '葉大同', 'qqwe901018', '123', 'active', '0909090909', '111590016@ntut.edu.tw', '0', 0, '2025-01-06 11:11:01', '賣家權限', '');

-- --------------------------------------------------------

--
-- 資料表結構 `order_`
--

CREATE TABLE `order_` (
  `ID` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
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
  `Category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`product_id`, `Seller_id`, `Product_name`, `Price`, `num`, `Description`, `Publish_data`, `Mdata`, `Status`, `Is_deleted`, `Category`) VALUES
(11, 13, '123', 123, 123, '123', '2025-01-06 05:36:47', '2025-01-06 13:36:47', 0, 0, '123'),
(12, 13, 'asc', 1231, 11, '123', '2025-01-06 07:12:40', '2025-01-06 15:12:40', 0, 0, '男性用品');

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
-- 資料表結構 `seller_category`
--

CREATE TABLE `seller_category` (
  `seller_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
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
-- 資料表索引 `coupon`
--
ALTER TABLE `coupon`
  ADD PRIMARY KEY (`ID`);

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
-- 資料表索引 `seller_category`
--
ALTER TABLE `seller_category`
  ADD PRIMARY KEY (`seller_id`,`category_id`);

--
-- 資料表索引 `specified_product`
--
ALTER TABLE `specified_product`
  ADD PRIMARY KEY (`coupon_id`,`product_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `member`
--
ALTER TABLE `member`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order_`
--
ALTER TABLE `order_`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
