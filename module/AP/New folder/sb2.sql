-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2022 at 11:56 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sb2`
--

-- --------------------------------------------------------

--
-- Table structure for table `bpb_new`
--

CREATE TABLE `bpb_new` (
  `id` int(11) NOT NULL,
  `no_bpb` varchar(255) DEFAULT NULL,
  `pono` varchar(255) DEFAULT NULL,
  `ws` varchar(255) DEFAULT NULL,
  `tgl_bpb` date DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `itemdesc` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `qty` decimal(16,4) DEFAULT NULL,
  `uom` varchar(255) DEFAULT NULL,
  `price` decimal(16,4) DEFAULT NULL,
  `tax` decimal(16,4) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm1` varchar(255) DEFAULT NULL,
  `confirm2` varchar(255) DEFAULT NULL,
  `confirm_date` timestamp NULL DEFAULT NULL,
  `is_invoiced` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `top` int(11) DEFAULT NULL,
  `pterms` varchar(255) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  `update_user` varchar(255) DEFAULT NULL,
  `ceklist` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `bpb_new`
--

INSERT INTO `bpb_new` (`id`, `no_bpb`, `pono`, `ws`, `tgl_bpb`, `tgl_po`, `supplier`, `itemdesc`, `color`, `size`, `qty`, `uom`, `price`, `tax`, `curr`, `create_user`, `confirm1`, `confirm2`, `confirm_date`, `is_invoiced`, `status`, `top`, `pterms`, `create_date`, `update_date`, `update_user`, `ceklist`, `start_date`, `end_date`) VALUES
(11, 'GK/IN/0420/01057', 'DWT/0120/140/01972', 'DWT/0120/147', '2020-04-24', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM SCARLET', 'SCARLET', '66\"', '110.0000', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(12, 'GK/IN/0520/01145', 'DWT/0120/140/01972', 'DWT/0120/141', '2020-05-09', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM ROYAL', 'ROYAL', '66\"', '112.0000', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(13, 'GK/IN/0520/01198', 'DWT/0120/140/01972', 'DWT/0220/171', '2020-05-14', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '7.7200', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(14, 'GK/IN/0420/01057', 'DWT/0120/140/01972', 'DWT/0120/140', '2020-04-24', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM SCARLET', 'SCARLET', '66\"', '110.0000', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(15, 'GK/IN/0520/01145', 'DWT/0120/140/01972', 'DWT/0220/171', '2020-05-09', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '47.2800', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(16, 'GK/IN/0520/01198', 'DWT/0120/140/01972', 'DWT/0120/149', '2020-05-14', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '1.3200', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(17, 'GK/IN/0420/01057', 'DWT/0120/140/01972', 'DWT/0120/148', '2020-04-24', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM SQK NAVY', 'SQK NAVY', '66\"', '48.0000', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(18, 'GK/IN/0520/01198', 'DWT/0120/140/01972', 'DWT/0120/148', '2020-05-14', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '1.1800', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(19, 'GK/IN/0520/01145', 'DWT/0120/140/01972', 'DWT/0120/149', '2020-05-09', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '53.6800', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(20, 'GK/IN/0520/01145', 'DWT/0120/140/01972', 'DWT/0120/148', '2020-05-09', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '53.8200', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-04 00:35:55', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-04 00:35:22', '2022-02-04 00:35:22', 'indro', 1, '2020-01-01', '2022-02-04'),
(21, 'GEN/IN/1219/00276', 'PO/0220/00147', '', '2020-02-27', '2020-02-27', 'MEKAR JAYA', 'KERTAS STENSIL SAKURA', 'RANDOM', 'FOLIO', '2.0000', 'RIM', '31500.0000', '0.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 01:04:40', 'Waiting', 'GMF-PCH', 30, 'DP - Partial shipment allowed', '2022-02-04 01:04:33', '2022-02-04 01:04:33', 'indro', 1, '2019-01-01', '2022-02-04'),
(22, 'GEN/IN/1219/00276', 'PO/0220/00147', '', '2020-02-27', '2020-02-27', 'MEKAR JAYA', 'KARTON DUPLEX', '-', '-', '400.0000', 'RIM', '4000.0000', '0.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 01:04:40', 'Waiting', 'GMF-PCH', 30, 'DP - Partial shipment allowed', '2022-02-04 01:04:33', '2022-02-04 01:04:33', 'indro', 1, '2019-01-01', '2022-02-04'),
(23, 'GEN/IN/1219/00276', 'PO/0220/00147', '', '2020-02-27', '2020-02-27', 'MEKAR JAYA', 'ISI CUTTER KENKO L-150', '-', '-', '12.0000', 'PACK', '6000.0000', '0.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 01:04:40', 'Waiting', 'GMF-PCH', 30, 'DP - Partial shipment allowed', '2022-02-04 01:04:33', '2022-02-04 01:04:33', 'indro', 1, '2019-01-01', '2022-02-04'),
(24, 'GK/IN/0120/00096', 'SML/1219/019/02246', 'SML/1219/019', '2020-01-13', '2019-12-21', 'Singa Global Textile 2,PT', 'FABRIC KNIT SINGLE JERSEY 30S 100% COTTON 72\" 150GSM APRICOT', 'APRICOT', '72\"', '468.8500', 'KGM', '104545.4500', '0.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 01:58:30', 'Invoiced', 'GMF-PCH', 0, 'DP - Complete quantity', '2022-02-04 01:58:21', '2022-02-04 01:58:21', 'indro', 1, '2019-01-01', '2022-02-04'),
(25, 'GK/IN/0120/00052', 'SML/1219/019/02246', 'SML/1219/019', '2020-01-08', '2019-12-21', 'Singa Global Textile 2,PT', 'FABRIC KNIT SINGLE JERSEY 30S 100% COTTON 72\" 150GSM APRICOT', 'APRICOT', '72\"', '0.7000', 'KGM', '104545.4500', '0.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 01:58:30', 'Waiting', 'GMF-PCH', 0, 'DP - Complete quantity', '2022-02-04 01:58:21', '2022-02-04 01:58:21', 'indro', 1, '2019-01-01', '2022-02-04'),
(26, 'GK/IN/0120/00096', 'SML/1219/019/02246', 'SML/1219/019', '2020-01-13', '2019-12-21', 'Singa Global Textile 2,PT', 'FABRIC KNIT RIB 1X1 COTTON SPANDEX 95% COTTON 5% SPANDEX 68\" 235 GSM APRICOT', 'APRICOT', '68\"', '24.4500', 'KGM', '110000.0000', '0.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 01:58:30', 'Invoiced', 'GMF-PCH', 0, 'DP - Complete quantity', '2022-02-04 01:58:21', '2022-02-04 01:58:21', 'indro', 1, '2019-01-01', '2022-02-04'),
(27, 'GACC/IN/0120/00095', 'C/AVI/01305', 'AVI/1019/058', '2020-01-13', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL JAPAN/ENGLISH', '-', '-', '393.0000', 'PCS', '250.0000', '0.0000', 'IDR', 'indro', 'yulius', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', 0, 'Credit - Complete PO 30 KONTRABON', '2022-02-04 02:18:08', '2022-02-04 02:18:08', 'indro', 1, '2020-01-01', '2022-02-04'),
(28, 'GACC/IN/0120/00095', 'C/AVI/01305', 'AVI/1019/059', '2020-01-13', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL KOREA/CHINA', '-', '-', '107.0000', 'PCS', '250.0000', '0.0000', 'IDR', 'indro', 'yulius', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', 0, 'Credit - Complete PO 30 KONTRABON', '2022-02-04 02:18:08', '2022-02-04 02:18:08', 'indro', 1, '2020-01-01', '2022-02-04'),
(29, 'GACC/IN/0120/00095', 'C/AVI/01305', 'AVI/1019/061', '2020-01-13', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL KOREA/CHINA', '-', '-', '78.0000', 'PCS', '250.0000', '0.0000', 'IDR', 'indro', 'yulius', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', 0, 'Credit - Complete PO 30 KONTRABON', '2022-02-04 02:18:08', '2022-02-04 02:18:08', 'indro', 1, '2020-01-01', '2022-02-04'),
(30, 'GACC/IN/0120/00095', 'C/AVI/01305', 'AVI/1019/064', '2020-01-13', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL KOREA/CHINA', '-', '-', '9394.0000', 'PCS', '250.0000', '0.0000', 'IDR', 'indro', 'yulius', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', 0, 'Credit - Complete PO 30 KONTRABON', '2022-02-04 02:18:08', '2022-02-04 02:18:08', 'indro', 1, '2020-01-01', '2022-02-04'),
(31, 'GACC/IN/0122/00003', 'PTX/1221/037/05383', 'PTX/1221/037', '2022-01-03', '2021-12-21', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING STICKER', '-', '-', '298.0000', 'PCS', '0.0094', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(32, 'GACC/IN/0122/00026', 'C/PTX/1221/05367', 'PTX/1221/036', '2022-01-04', '2021-12-20', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING CARE LABEL', '-', '-', '6087.0000', 'PCS', '0.0110', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(33, 'GACC/IN/0122/00118', 'C/PTX/1221/05570', 'PTX/1221/037', '2022-01-11', '2021-12-29', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING MAIN LABEL S MITRED LABEL MALCFL', '-', 'S', '518.0000', 'PCS', '0.0173', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(34, 'GACC/IN/0122/00141', 'PTX/1221/037/05383', 'PTX/1221/037', '2022-01-11', '2021-12-21', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING STICKER', '-', '-', '318.0000', 'PCS', '0.0200', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(35, 'GACC/IN/0122/00003', 'PTX/1221/037/05383', 'PTX/1221/037', '2022-01-03', '2021-12-21', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING LABEL TAFFETA WHITE', 'WHITE', '-', '6436.0000', 'PCS', '0.0015', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(36, 'GACC/IN/0122/00026', 'C/PTX/1221/05367', 'PTX/1221/037', '2022-01-04', '2021-12-20', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING CARE LABEL', '-', '-', '6396.0000', 'PCS', '0.0110', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(37, 'GACC/IN/0122/00118', 'C/PTX/1221/05570', 'PTX/1221/037', '2022-01-11', '2021-12-29', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING MAIN LABEL M MITRED LABEL MALCFL', '-', 'M', '1813.0000', 'PCS', '0.0173', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(38, 'GACC/IN/0122/00141', 'PTX/1221/037/05383', 'PTX/1221/037', '2022-01-11', '2021-12-21', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING STICKER CARTON SHIPPING STICKER', '-', '-', '222.0000', 'PCS', '0.0127', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(39, 'GACC/IN/0122/00141', 'PTX/1221/037/05383', 'PTX/1221/037', '2022-01-11', '2021-12-21', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING STICKER CARTON ID', '-', '-', '242.0000', 'PCS', '0.0200', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(40, 'GACC/IN/0122/00118', 'C/PTX/1221/05570', 'PTX/1221/037', '2022-01-11', '2021-12-29', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING MAIN LABEL L MITRED LABEL MALCFL', '-', 'L', '1660.0000', 'PCS', '0.0173', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(41, 'GACC/IN/0122/00118', 'C/PTX/1221/05570', 'PTX/1221/037', '2022-01-11', '2021-12-29', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING MAIN LABEL XL MITRED LABEL MALCFL', '-', 'XL', '1344.0000', 'PCS', '0.0173', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 03:57:50', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 03:57:37', '2022-02-04 03:57:37', 'indro', 1, '2020-01-01', '2022-02-04'),
(42, 'GACC/IN/0122/00274', 'PTX/1221/037/00117', 'PTX/1221/037', '2022-01-20', '2022-01-10', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING STICKER CARTON ID', '-', '-', '242.0000', 'PCS', '0.0200', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 10:29:29', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 10:29:20', '2022-02-04 10:29:20', 'indro', 1, '2019-01-01', '2022-02-04'),
(43, 'GACC/IN/0122/00275', 'C/PTX/1221/05570', 'PTX/1221/037', '2022-01-20', '2021-12-29', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING MAIN LABEL XS MITRED LABEL MALCFL', '-', 'XS', '518.0000', 'PCS', '0.0173', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 10:29:29', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 10:29:20', '2022-02-04 10:29:20', 'indro', 1, '2019-01-01', '2022-02-04'),
(44, 'GACC/IN/0122/00216', 'PTX/1221/036/00225', 'PTX/1221/036', '2022-01-19', '2022-01-13', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING MAIN LABEL XL END FOLD MALBWL', '-', 'XL', '674.0000', 'PCS', '0.0185', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 10:29:29', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 10:29:20', '2022-02-04 10:29:20', 'indro', 1, '2019-01-01', '2022-02-04'),
(45, 'GACC/IN/0122/00275', 'C/PTX/1221/05570', 'PTX/1221/037', '2022-01-20', '2021-12-29', 'ALMINDO PRATAMA CV', 'ACCESORIES SEWING MAIN LABEL 4XL MITRED LABEL MALCFL', '-', '4XL', '25.0000', 'PCS', '0.0173', '0.0000', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 10:29:29', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-02-04 10:29:20', '2022-02-04 10:29:20', 'indro', 1, '2019-01-01', '2022-02-04');

-- --------------------------------------------------------

--
-- Table structure for table `bppb_new`
--

CREATE TABLE `bppb_new` (
  `id` int(11) NOT NULL,
  `no_bppb` varchar(255) DEFAULT NULL,
  `no_ro` varchar(255) DEFAULT NULL,
  `tgl_bppb` date DEFAULT NULL,
  `no_bpb` varchar(100) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `no_kbon` varchar(100) DEFAULT NULL,
  `itemdesc` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `qty` decimal(16,4) DEFAULT NULL,
  `uom` varchar(255) DEFAULT NULL,
  `price` decimal(16,4) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm1` varchar(255) DEFAULT NULL,
  `confirm2` varchar(255) DEFAULT NULL,
  `confirm_date` timestamp NULL DEFAULT NULL,
  `is_invoiced` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  `update_user` varchar(255) DEFAULT NULL,
  `ceklist` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `bppb_new`
--

INSERT INTO `bppb_new` (`id`, `no_bppb`, `no_ro`, `tgl_bppb`, `no_bpb`, `supplier`, `no_kbon`, `itemdesc`, `color`, `size`, `qty`, `uom`, `price`, `curr`, `create_user`, `confirm1`, `confirm2`, `confirm_date`, `is_invoiced`, `status`, `create_date`, `update_date`, `update_user`, `ceklist`) VALUES
(1, 'GACC/RO/0621/02186', 'A07045', '2021-06-29', 'GACC/IN/0621/02041', 'Singa Global Textile 2,PT', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '283.0000', 'PCS', '0.0000', 'IDR', 'indro', 'ibrahim', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', '2022-02-04 02:18:57', '2022-02-04 02:18:57', 'indro', 1),
(2, 'GACC/RO/0621/02186', 'A07045', '2021-06-29', 'GACC/IN/0621/02041', 'Singa Global Textile 2,PT', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '283.0000', 'PCS', '0.0000', 'IDR', 'indro', 'ibrahim', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', '2022-02-04 02:18:57', '2022-02-04 02:18:57', 'indro', 1),
(3, 'GACC/RO/0621/02186', 'A07045', '2021-06-29', 'GACC/IN/0621/02041', 'Singa Global Textile 2,PT', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '121.0000', 'PCS', '0.0000', 'IDR', 'indro', 'ibrahim', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', '2022-02-04 02:18:57', '2022-02-04 02:18:57', 'indro', 1),
(4, 'GACC/RO/0621/02186', 'A07045', '2021-06-29', 'GACC/IN/0621/02041', 'Singa Global Textile 2,PT', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '270.0000', 'PCS', '0.0000', 'IDR', 'indro', 'ibrahim', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', '2022-02-04 02:18:57', '2022-02-04 02:18:57', 'indro', 1),
(5, 'GACC/RO/0621/02186', 'A07045', '2021-06-29', 'GACC/IN/0621/02041', 'Singa Global Textile 2,PT', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '270.0000', 'PCS', '0.0000', 'IDR', 'indro', 'ibrahim', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', '2022-02-04 02:18:57', '2022-02-04 02:18:57', 'indro', 1),
(6, 'GACC/RO/0621/02186', 'A07045', '2021-06-29', 'GACC/IN/0621/02041', 'Singa Global Textile 2,PT', NULL, 'ACCESORIES SEWING MAIN LABEL', '-', '-', '106.0000', 'PCS', '0.0000', 'IDR', 'indro', 'ibrahim', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', '2022-02-04 02:18:57', '2022-02-04 02:18:57', 'indro', 1),
(7, 'GACC/RO/0621/02186', 'A07045', '2021-06-29', 'GACC/IN/0621/02041', 'Singa Global Textile 2,PT', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '213.0000', 'PCS', '0.0000', 'IDR', 'indro', 'ibrahim', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', '2022-02-04 02:18:57', '2022-02-04 02:18:57', 'indro', 1),
(8, 'GACC/RO/0721/02612', 'A07045', '2021-07-26', 'GACC/IN/0621/02041', 'Singa Global Textile 2,PT', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '140.0000', 'PCS', '0.0000', 'IDR', 'indro', 'ibrahim', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', '2022-02-04 02:18:57', '2022-02-04 02:18:57', 'indro', 1),
(9, 'GACC/RO/0721/02612', 'A07045', '2021-07-26', 'GACC/IN/0621/02041', 'Singa Global Textile 2,PT', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '212.0000', 'PCS', '0.0000', 'IDR', 'indro', 'ibrahim', '', '0000-00-00 00:00:00', 'Waiting', 'GMF', '2022-02-04 02:18:57', '2022-02-04 02:18:57', 'indro', 1),
(10, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '273.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(11, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '73.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(12, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL 3XL', '-', '3XL', '52.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(13, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '43.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(14, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL 3XL', '-', '3XL', '43.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(15, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING MAIN LABEL', '-', '-', '1088.0000', 'PCS', '1630.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(16, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL XXL', '-', 'XXL', '168.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(17, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING MAIN LABEL', '-', '-', '1088.0000', 'PCS', '1630.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(18, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL XL', '-', 'XL', '336.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(19, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING WOVEN LABEL', '-', '-', '441.0000', 'PCS', '255.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(20, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL XL', '-', 'XL', '305.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(21, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING MAIN LABEL', '-', '-', '1050.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(22, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL L', '-', 'L', '346.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(23, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL M', '-', 'M', '210.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(24, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL M', '-', 'M', '210.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(25, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL S', '-', 'S', '73.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(26, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING WOVEN LABEL', '-', '-', '1932.0000', 'PCS', '155.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(27, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING WOVEN LABEL', '-', '-', '1071.0000', 'PCS', '90.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(28, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING WOVEN LABEL', '-', '-', '1077.0000', 'PCS', '90.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(29, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '78.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(30, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '215.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(31, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '310.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(32, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '241.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(33, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '221.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(34, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL 3XL', '-', '3XL', '52.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(35, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '43.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(36, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL XXL', '-', 'XXL', '142.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(37, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING MAIN LABEL', '-', '-', '1226.0000', 'PCS', '1630.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(38, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL XXL', '-', 'XXL', '142.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(39, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING WOVEN LABEL', '-', '-', '1155.0000', 'PCS', '255.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(40, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL XL', '-', 'XL', '304.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(41, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING MAIN LABEL', '-', '-', '1050.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(42, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL L', '-', 'L', '305.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(43, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL L', '-', 'L', '305.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(44, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL M', '-', 'M', '252.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(45, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL S', '-', 'S', '85.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(46, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL S', '-', 'S', '73.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(47, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING WOVEN LABEL', '-', '-', '1076.0000', 'PCS', '90.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(48, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING WOVEN LABEL', '-', '-', '1113.0000', 'PCS', '90.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(49, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '64.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(50, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '268.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(51, 'SJ-A00013-R', 'A01066', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', NULL, 'ACCESORIES SEWING SIZE LABEL', '-', '-', '268.0000', 'PCS', '220.0000', 'IDR', 'indro', 'yulius', 'indro', '2022-02-04 03:45:26', 'Waiting', 'GMF-PCH', '2022-02-04 03:30:11', '2022-02-04 03:30:11', 'indro', 1),
(52, 'GACC/RO/1221/03998', 'A08779', '2021-12-10', 'GACC/IN/1221/03990', 'ALMINDO PRATAMA CV', 'SI/APR/2022/02/00007', 'ACCESORIES PACKING STICKER', '-', '-', '107.0000', 'PCS', '0.0094', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 04:06:37', 'Invoiced', 'GMF-PCH', '2022-02-04 04:06:30', '2022-02-04 04:06:30', 'indro', 1),
(53, 'GACC/RO/1221/04117', 'A09059', '2021-12-27', 'GACC/IN/1221/04280', 'ALMINDO PRATAMA CV', NULL, 'ACCESORIES PACKING HANGTAG MLIT1', '-', '-', '2214.0000', 'PCS', '0.0170', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 04:06:37', 'Waiting', 'GMF-PCH', '2022-02-04 04:06:30', '2022-02-04 04:06:30', 'indro', 1),
(54, 'GACC/RO/1221/04000', 'A08624', '2021-12-10', 'GACC/IN/1121/03796', 'ALMINDO PRATAMA CV', NULL, 'ACCESORIES PACKING STICKER CARTON', '-', '-', '159.0000', 'PCS', '0.0200', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 10:17:54', 'Waiting', 'GMF-PCH', '2022-02-04 10:17:48', '2022-02-04 10:17:48', 'indro', 1),
(55, 'GACC/RO/1221/03999', 'A08679', '2021-12-10', 'GACC/IN/1121/03859', 'ALMINDO PRATAMA CV', NULL, 'ACCESORIES PACKING STICKER CARTON', '-', '-', '159.0000', 'PCS', '0.0200', 'USD', 'indro', 'ibrahim', 'indro', '2022-02-04 10:17:54', 'Waiting', 'GMF-PCH', '2022-02-04 10:17:48', '2022-02-04 10:17:48', 'indro', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ftr_cbd`
--

CREATE TABLE `ftr_cbd` (
  `id` int(11) NOT NULL,
  `no_ftr_cbd` varchar(255) DEFAULT NULL,
  `tgl_ftr_cbd` date DEFAULT NULL,
  `supp` varchar(255) DEFAULT NULL,
  `id_po` int(11) DEFAULT NULL,
  `no_po` varchar(255) DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `no_pi` varchar(255) DEFAULT NULL,
  `subtotal` decimal(16,2) DEFAULT NULL,
  `tax` decimal(16,2) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_invoiced` varchar(255) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `confirm_date` datetime DEFAULT NULL,
  `cancel_date` datetime DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `kb_inv` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `ftr_cbd`
--

INSERT INTO `ftr_cbd` (`id`, `no_ftr_cbd`, `tgl_ftr_cbd`, `supp`, `id_po`, `no_po`, `tgl_po`, `no_pi`, `subtotal`, `tax`, `total`, `curr`, `keterangan`, `status`, `is_invoiced`, `create_date`, `confirm_date`, `cancel_date`, `create_user`, `confirm_user`, `cancel_user`, `kb_inv`) VALUES
(2, 'FTR/C/NAG/0222/00001', '2022-02-04', 'INDO TAICHEN TEXTILE INDUSTRY,PT', NULL, 'DWT/0120/140/01972', '2020-03-31', '-', '4959.50', '0.00', '4959.50', 'USD', '', 'Approved', 'Invoiced', '2022-02-04 07:35:35', '2022-02-04 07:35:38', NULL, 'indro', '', NULL, '1'),
(3, 'FTR/C/NAG/0222/00002', '2022-02-04', 'INDO TAICHEN TEXTILE INDUSTRY,PT', NULL, 'C/DWT/0320/01214', '2020-03-05', '-', '281.60', '0.00', '281.60', 'USD', '', 'Approved', 'Invoiced', '2022-02-04 09:20:44', '2022-02-04 09:21:25', NULL, 'indro', '', NULL, '1'),
(4, 'FTR/C/NAG/0222/00003', '2022-02-04', 'INDO TAICHEN TEXTILE INDUSTRY,PT', NULL, 'DWT/1219/163/00423', '2020-01-28', '-', '77.00', '0.00', '77.00', 'USD', '', 'Waiting', 'Waiting', '2022-02-04 10:15:56', '2022-02-04 10:16:00', NULL, 'indro', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ftr_dp`
--

CREATE TABLE `ftr_dp` (
  `id` int(11) NOT NULL,
  `no_ftr_dp` varchar(255) DEFAULT NULL,
  `tgl_ftr_dp` date DEFAULT NULL,
  `supp` varchar(255) DEFAULT NULL,
  `id_po` int(11) DEFAULT NULL,
  `no_po` varchar(255) DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `no_pi` varchar(255) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `dp` varchar(255) DEFAULT NULL,
  `dp_value` decimal(16,2) DEFAULT NULL,
  `balance` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_invoiced` varchar(255) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `confirm_date` datetime DEFAULT NULL,
  `cancel_date` datetime DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `kb_inv` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `ftr_dp`
--

INSERT INTO `ftr_dp` (`id`, `no_ftr_dp`, `tgl_ftr_dp`, `supp`, `id_po`, `no_po`, `tgl_po`, `no_pi`, `total`, `dp`, `dp_value`, `balance`, `curr`, `keterangan`, `status`, `is_invoiced`, `create_date`, `confirm_date`, `cancel_date`, `create_user`, `confirm_user`, `cancel_user`, `kb_inv`) VALUES
(1, 'FTR/D/NAG/0222/00001', '2022-02-04', 'MEKAR JAYA', NULL, 'PO/0220/00147', '2020-02-27', '-', '2137750.00', '50', '1068875.00', '1068875.00', 'IDR', '', 'Approved', 'Invoiced', '2022-02-04 08:07:35', '2022-02-04 08:07:46', NULL, 'indro', '', NULL, '1'),
(2, 'FTR/D/NAG/0222/00002', '2022-02-04', 'Singa Global Textile 2,PT', NULL, 'SML/1219/019/02246', '2019-12-21', '-', '51154543.35', '50', '25577271.68', '25577271.68', 'IDR', '', 'Approved', 'Invoiced', '2022-02-04 08:58:40', '2022-02-04 08:58:44', NULL, 'indro', '', NULL, '1'),
(3, 'FTR/D/NAG/0222/00003', '2022-02-04', 'Singa Global Textile 2,PT', NULL, 'SML/1219/016/02244', '2019-12-21', '-', '43341816.35', '45', '19503817.36', '23837998.99', 'IDR', '', 'Approved', 'Invoiced', '2022-02-04 09:23:49', '2022-02-04 09:23:58', NULL, 'indro', '', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `kartu_hutang`
--

CREATE TABLE `kartu_hutang` (
  `id` int(5) NOT NULL,
  `no_bpb` varchar(50) DEFAULT NULL,
  `tgl_bpb` date DEFAULT NULL,
  `no_po` varchar(50) DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `no_kbon` varchar(100) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `supp_inv` varchar(100) DEFAULT NULL,
  `tgl_inv` date DEFAULT NULL,
  `no_faktur` varchar(100) DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `nama_supp` varchar(500) DEFAULT NULL,
  `create_date` date DEFAULT NULL,
  `pph` decimal(16,2) DEFAULT NULL,
  `no_payment` varchar(50) DEFAULT NULL,
  `tgl_payment` date DEFAULT NULL,
  `curr` varchar(20) DEFAULT NULL,
  `ket` varchar(50) DEFAULT NULL,
  `rate` decimal(16,2) DEFAULT NULL,
  `tgl_rate` date DEFAULT NULL,
  `credit_usd` decimal(16,2) DEFAULT NULL,
  `debit_usd` decimal(16,2) DEFAULT NULL,
  `credit_idr` decimal(16,2) DEFAULT NULL,
  `debit_idr` decimal(16,2) DEFAULT NULL,
  `cek` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kartu_hutang`
--

INSERT INTO `kartu_hutang` (`id`, `no_bpb`, `tgl_bpb`, `no_po`, `tgl_po`, `no_kbon`, `tgl_kbon`, `supp_inv`, `tgl_inv`, `no_faktur`, `tgl_tempo`, `nama_supp`, `create_date`, `pph`, `no_payment`, `tgl_payment`, `curr`, `ket`, `rate`, `tgl_rate`, `credit_usd`, `debit_usd`, `credit_idr`, `debit_idr`, `cek`) VALUES
(8, 'GK/IN/0420/01057', '2020-04-24', 'DWT/0120/140/01972', '2020-03-31', '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-04', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '2438.80', '0.00', '34989463.60', '0.00', '2'),
(9, 'GK/IN/0520/01145', '2020-05-09', 'DWT/0120/140/01972', '2020-03-31', '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-04', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '2427.70', '0.00', '34830183.21', '0.00', '2'),
(10, 'GK/IN/0520/01198', '2020-05-14', 'DWT/0120/140/01972', '2020-03-31', '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-04', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '93.00', '0.00', '1334299.69', '0.00', '2'),
(15, '-', NULL, 'DWT/0120/140/01972', '2020-03-31', 'SI/CBD/2022/02/00001', '2022-02-04', '-', '2022-02-04', '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-04', NULL, 'LP/CBD/NAG/0222/00002', '2022-02-04', 'USD', 'Payment CBD', '14347.00', NULL, '0.00', '2000.00', '0.00', '28694000.00', '1'),
(16, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-04', NULL, 'LP/CBD/NAG/0222/00002', '2022-02-04', 'USD', 'Selisih Kurs', '14347.00', NULL, '0.00', '0.00', '0.00', '0.00', '1'),
(17, '-', NULL, 'DWT/0120/140/01972', '2020-03-31', 'SI/CBD/2022/02/00001', '2022-02-04', '-', '2022-02-04', '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-04', NULL, 'LP/CBD/NAG/0222/00003', '2022-02-04', 'USD', 'Payment CBD', '14347.00', NULL, '0.00', '1459.50', '0.00', '20939446.50', '1'),
(18, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-04', NULL, 'LP/CBD/NAG/0222/00003', '2022-02-04', 'USD', 'Selisih Kurs', '14347.00', NULL, '0.00', '0.00', '0.00', '0.00', '1'),
(21, '-', NULL, 'DWT/0120/140/01972', '2020-03-31', 'SI/CBD/2022/02/00001', '2022-02-04', '-', '2022-02-04', '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-04', NULL, 'LP/CBD/NAG/0222/00001', '2022-02-04', 'USD', 'Payment CBD', '14200.00', NULL, '0.00', '1500.00', '0.00', '21300000.00', '1'),
(22, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-04', NULL, 'LP/CBD/NAG/0222/00001', '2022-02-04', 'USD', 'Selisih Kurs', '14200.00', NULL, '0.00', '0.00', '0.00', '220500.00', '1'),
(23, 'GEN/IN/1219/00276', '2020-02-27', 'PO/0220/00147', '2020-02-27', '-', NULL, '-', NULL, '-', NULL, 'MEKAR JAYA', '2022-02-04', NULL, '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '1735000.00', '0.00', '2'),
(24, '-', NULL, 'PO/0220/00147', '2020-02-27', 'SI/DP/2022/02/00001', '2022-02-04', '-', '2022-02-04', '-', NULL, 'MEKAR JAYA', '2022-02-04', NULL, 'LP/DP/NAG/0222/00001', '2022-02-04', 'IDR', 'Payment DP', NULL, NULL, '0.00', '0.00', '0.00', '500000.00', '1'),
(25, '-', NULL, 'PO/0220/00147', '2020-02-27', 'SI/DP/2022/02/00001', '2022-02-04', '-', '2022-02-04', '-', NULL, 'MEKAR JAYA', '2022-02-04', NULL, 'LP/DP/NAG/0222/00002', '2022-02-04', 'IDR', 'Payment DP', NULL, NULL, '0.00', '0.00', '0.00', '568875.00', '1'),
(26, 'GK/IN/0120/00096', '2020-01-13', 'SML/1219/019/02246', '2019-12-21', 'SI/APR/2022/02/00004', '2022-02-04', '-', '2022-02-04', '-', '2020-01-13', 'Singa Global Textile 2,PT', '2022-02-04', '0.00', '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '51705634.23', '0.00', '2'),
(27, 'GK/IN/0120/00052', '2020-01-08', 'SML/1219/019/02246', '2019-12-21', '-', NULL, '-', NULL, '-', NULL, 'Singa Global Textile 2,PT', '2022-02-04', NULL, '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '73181.82', '0.00', '2'),
(28, '-', NULL, 'SML/1219/019/02246', '2019-12-21', 'SI/DP/2022/02/00002', '2022-02-04', '-', '2022-02-04', '-', NULL, 'Singa Global Textile 2,PT', '2022-02-04', NULL, 'LP/DP/NAG/0222/00003', '2022-02-04', 'IDR', 'Payment DP', NULL, NULL, '0.00', '0.00', '0.00', '15000000.00', '1'),
(29, '-', NULL, 'SML/1219/019/02246', '2019-12-21', 'SI/DP/2022/02/00002', '2022-02-04', '-', '2022-02-04', '-', NULL, 'Singa Global Textile 2,PT', '2022-02-04', NULL, 'LP/DP/NAG/0222/00004', '2022-02-04', 'IDR', 'Payment DP', NULL, NULL, '0.00', '0.00', '0.00', '10577271.68', '1'),
(30, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'Singa Global Textile 2,PT', '2022-02-04', NULL, 'LP/NAG/0222/00002', '2022-02-04', 'IDR', 'Payment', NULL, NULL, '0.00', '0.00', '0.00', '10000000.00', '3'),
(31, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'Singa Global Textile 2,PT', '2022-02-04', NULL, 'LP/NAG/0222/00003', '2022-02-04', 'IDR', 'Payment', NULL, NULL, '0.00', '0.00', '0.00', '5000000.00', '3'),
(32, 'GACC/IN/0122/00003', '2022-01-03', 'PTX/1221/037/05383', '2021-12-21', '-', NULL, '-', NULL, '-', NULL, 'ALMINDO PRATAMA CV', '2022-02-04', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '12.46', '0.00', '178694.75', '0.00', '2'),
(33, 'GACC/IN/0122/00026', '2022-01-04', 'C/PTX/1221/05367', '2021-12-20', '-', NULL, '-', NULL, '-', NULL, 'ALMINDO PRATAMA CV', '2022-02-04', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '137.31', '0.00', '1970029.61', '0.00', '2'),
(34, 'GACC/IN/0122/00118', '2022-01-11', 'C/PTX/1221/05570', '2021-12-29', '-', NULL, '-', NULL, '-', NULL, 'ALMINDO PRATAMA CV', '2022-02-04', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '92.30', '0.00', '1324163.54', '0.00', '2'),
(35, 'GACC/IN/0122/00141', '2022-01-11', 'PTX/1221/037/05383', '2021-12-21', 'SI/APR/2022/02/00007', '2022-02-04', '-', '2022-02-04', '-', '2022-02-10', 'ALMINDO PRATAMA CV', '2022-02-04', '0.00', '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '14.02', '0.00', '201144.94', '0.00', '2'),
(36, 'GACC/IN/0122/00275', '2022-01-20', 'C/PTX/1221/05570', '2021-12-29', '-', NULL, '-', NULL, '-', NULL, 'ALMINDO PRATAMA CV', '2022-02-04', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '9.39', '0.00', '134774.28', '0.00', '2'),
(37, 'GACC/IN/0122/00216', '2022-01-19', 'PTX/1221/036/00225', '2022-01-13', '-', NULL, '-', NULL, '-', NULL, 'ALMINDO PRATAMA CV', '2022-02-04', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '12.47', '0.00', '178892.74', '0.00', '2'),
(38, 'GACC/IN/0122/00274', '2022-01-20', 'PTX/1221/037/00117', '2022-01-10', '-', NULL, '-', NULL, '-', NULL, 'ALMINDO PRATAMA CV', '2022-02-04', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '4.84', '0.00', '69439.48', '0.00', '2');

-- --------------------------------------------------------

--
-- Table structure for table `kontrabon`
--

CREATE TABLE `kontrabon` (
  `id` int(11) NOT NULL,
  `no_kbon` varchar(255) NOT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `nama_supp` varchar(255) DEFAULT NULL,
  `no_faktur` varchar(255) DEFAULT NULL,
  `no_bpb` varchar(255) DEFAULT NULL,
  `no_po` varchar(255) DEFAULT NULL,
  `tgl_bpb` date DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `supp_inv` varchar(255) DEFAULT NULL,
  `tgl_inv` date DEFAULT NULL,
  `subtotal` decimal(16,2) DEFAULT NULL,
  `tax` decimal(16,2) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `balance` decimal(16,2) DEFAULT NULL,
  `dp_value` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `ceklist` int(11) DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `idtax` int(11) DEFAULT NULL,
  `pph_code` decimal(16,2) DEFAULT NULL,
  `pph_value` decimal(16,2) DEFAULT NULL,
  `post_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_int` int(2) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `confirm_date` date DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `cancel_date` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `lp_inv` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `kontrabon`
--

INSERT INTO `kontrabon` (`id`, `no_kbon`, `tgl_kbon`, `id_jurnal`, `nama_supp`, `no_faktur`, `no_bpb`, `no_po`, `tgl_bpb`, `tgl_po`, `supp_inv`, `tgl_inv`, `subtotal`, `tax`, `total`, `balance`, `dp_value`, `curr`, `ceklist`, `tgl_tempo`, `idtax`, `pph_code`, `pph_value`, `post_date`, `update_date`, `status`, `status_int`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `start_date`, `end_date`, `lp_inv`) VALUES
(13, 'SI/APR/2022/02/00001', '2022-02-04', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'GK/IN/0520/01198', 'DWT/0120/140/01972', '2020-05-14', '2020-03-31', '-', '2022-02-04', '93.00', '0.00', '93.00', NULL, '2520.70', 'USD', 1, '2020-05-14', 0, '0.00', '0.00', '2022-02-04 01:02:16', '2022-02-04 01:02:16', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-04 01:02:16', '2022-02-04 01:02:46', 'indro', '2020-01-01', '2022-02-04', NULL),
(14, 'SI/APR/2022/02/00001', '2022-02-04', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'GK/IN/0520/01145', 'DWT/0120/140/01972', '2020-05-09', '2020-03-31', '-', '2022-02-04', '2427.70', '0.00', '2427.70', NULL, '2520.70', 'USD', 1, '2020-05-14', 0, '0.00', '0.00', '2022-02-04 01:02:16', '2022-02-04 01:02:16', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-04 01:02:16', '2022-02-04 01:02:46', 'indro', '2020-01-01', '2022-02-04', NULL),
(15, 'SI/APR/2022/02/00002', '2022-02-04', 0, 'Singa Global Textile 2,PT', '-', 'GK/IN/0120/00052', 'SML/1219/019/02246', '2020-01-08', '2019-12-21', '-', '2022-02-04', '73181.82', '0.00', '73181.82', NULL, '73181.82', 'IDR', 1, '2020-01-13', 0, '0.00', '0.00', '2022-02-04 02:01:57', '2022-02-04 02:01:57', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-04 02:01:57', '2022-02-04 02:07:48', 'indro', '2019-01-01', '2022-02-04', NULL),
(16, 'SI/APR/2022/02/00003', '2022-02-04', 0, 'Singa Global Textile 2,PT', '-', 'GK/IN/0120/00096', 'SML/1219/019/02246', '2020-01-13', '2019-12-21', '-', '2022-02-04', '51705634.23', '0.00', '51705634.23', NULL, '25577271.68', 'IDR', 1, '2020-01-13', 0, '0.00', '0.00', '2022-02-04 02:08:09', '2022-02-04 02:08:09', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-04 02:08:09', '2022-02-04 02:14:35', 'indro', '2020-01-01', '2022-02-04', NULL),
(17, 'SI/APR/2022/02/00004', '2022-02-04', 0, 'Singa Global Textile 2,PT', '-', 'GK/IN/0120/00096', 'SML/1219/019/02246', '2020-01-13', '2019-12-21', '-', '2022-02-04', '51705634.23', '0.00', '51705634.23', NULL, '25577271.68', 'IDR', 1, '2020-01-13', 0, '0.00', '0.00', '2022-02-04 02:28:56', '2022-02-04 02:28:56', 'Approved', 4, 'indro', 'indro', '2022-02-04', '2022-02-04 02:28:56', NULL, NULL, '2020-01-01', '2022-02-04', '1'),
(18, 'SI/APR/2022/02/00005', '2022-02-04', 0, 'ALMINDO PRATAMA CV', '-', 'GACC/IN/0122/00118', 'C/PTX/1221/05570', '2022-01-11', '2021-12-29', '-', '2022-02-04', '92.30', '0.00', '92.30', NULL, '0.00', 'USD', 1, '2022-02-10', 0, '0.00', '0.00', '2022-02-04 09:03:53', '2022-02-04 09:03:53', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-04 09:03:53', '2022-02-04 09:47:32', 'indro', '2019-01-01', '2022-02-04', NULL),
(21, 'SI/APR/2022/02/00006', '2022-02-04', 0, 'ALMINDO PRATAMA CV', '-', 'GACC/IN/0122/00141', 'PTX/1221/037/05383', '2022-01-11', '2021-12-21', '-', '2022-02-04', '14.02', '0.00', '14.02', NULL, '0.00', 'USD', 1, '2022-02-10', 0, '0.00', '0.00', '2022-02-04 09:53:54', '2022-02-04 09:53:54', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-04 09:53:54', '2022-02-04 09:54:37', 'indro', '2020-01-01', '2022-02-04', NULL),
(24, 'SI/APR/2022/02/00007', '2022-02-04', 0, 'ALMINDO PRATAMA CV', '-', 'GACC/IN/0122/00141', 'PTX/1221/037/05383', '2022-01-11', '2021-12-21', '-', '2022-02-04', '14.02', '0.00', '14.02', NULL, '0.00', 'USD', 1, '2022-02-10', 0, '0.00', '0.00', '2022-02-04 10:13:51', '2022-02-04 10:13:51', 'Approved', 4, 'indro', 'indro', '2022-02-04', '2022-02-04 10:13:51', NULL, NULL, '2020-01-01', '2022-02-04', NULL),
(27, 'SI/APR/2022/02/00008', '2022-02-04', 0, 'ALMINDO PRATAMA CV', '-', 'GACC/IN/0122/00216', 'PTX/1221/036/00225', '2022-01-19', '2022-01-13', '-', '2022-02-04', '12.47', '0.00', '12.47', NULL, '0.00', 'USD', 1, '2022-02-19', 0, '0.00', '0.00', '2022-02-04 10:46:15', '2022-02-04 10:46:15', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-04 10:46:15', '2022-02-04 10:49:26', 'indro', '2019-01-01', '2022-02-04', NULL),
(32, 'SI/APR/2022/02/00009', '2022-02-07', 0, 'ALMINDO PRATAMA CV', '-', 'GACC/IN/0122/00003', 'PTX/1221/037/05383', '2022-01-03', '2021-12-21', '-', '2022-02-07', '12.46', '0.00', '12.46', NULL, '0.00', 'USD', 1, '2022-02-19', 0, '0.00', '0.00', '2022-02-07 10:46:58', '2022-02-07 10:46:58', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-07 10:46:58', '2022-02-08 01:22:10', 'indro', '2019-01-01', '2022-02-07', NULL),
(34, 'SI/APR/2022/02/00010', '2022-02-07', 0, 'ALMINDO PRATAMA CV', '-', 'GACC/IN/0122/00274', 'PTX/1221/037/00117', '2022-01-20', '2022-01-10', '-', '2022-02-07', '4.84', '0.00', '4.84', NULL, '0.00', 'USD', 1, '2022-02-19', 0, '0.00', '0.00', '2022-02-07 10:54:50', '2022-02-07 10:54:50', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-07 10:54:50', '2022-02-08 01:22:12', 'indro', '2019-01-01', '2022-02-07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kontrabon_cbd`
--

CREATE TABLE `kontrabon_cbd` (
  `id` int(11) NOT NULL,
  `no_kbon` varchar(255) NOT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `nama_supp` varchar(255) DEFAULT NULL,
  `no_faktur` varchar(255) DEFAULT NULL,
  `no_cbd` varchar(255) DEFAULT NULL,
  `no_po` varchar(255) DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `supp_inv` varchar(255) DEFAULT NULL,
  `tgl_inv` date DEFAULT NULL,
  `subtotal` decimal(16,2) DEFAULT NULL,
  `tax` decimal(16,2) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `ceklist` int(11) DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `idtax` int(11) DEFAULT NULL,
  `pph_code` decimal(16,2) DEFAULT NULL,
  `pph_value` decimal(16,2) DEFAULT NULL,
  `post_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_int` int(5) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `confirm_date` date DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `cancel_date` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `lp_inv` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `kontrabon_cbd`
--

INSERT INTO `kontrabon_cbd` (`id`, `no_kbon`, `tgl_kbon`, `id_jurnal`, `nama_supp`, `no_faktur`, `no_cbd`, `no_po`, `tgl_po`, `supp_inv`, `tgl_inv`, `subtotal`, `tax`, `total`, `curr`, `ceklist`, `tgl_tempo`, `idtax`, `pph_code`, `pph_value`, `post_date`, `update_date`, `status`, `status_int`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `start_date`, `end_date`, `lp_inv`) VALUES
(2, 'SI/CBD/2022/02/00001', '2022-02-04', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'FTR/C/NAG/0222/00001', 'DWT/0120/140/01972', '2020-03-31', '-', '2022-02-04', '4959.50', '0.00', '4959.50', 'USD', 1, '2022-02-04', 0, '0.00', '0.00', '2022-02-04 00:36:38', '2022-02-04 00:36:38', 'Approved', 4, 'indro', 'indro', '2022-02-04', '2022-02-04 00:36:38', NULL, NULL, '2022-02-04', '2022-02-04', '1'),
(3, 'SI/CBD/2022/02/00002', '2022-02-04', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'FTR/C/NAG/0222/00002', 'C/DWT/0320/01214', '2020-03-05', '-', '2022-02-04', '281.60', '0.00', '281.60', 'USD', 1, '2022-02-04', 0, '0.00', '0.00', '2022-02-04 02:29:38', '2022-02-04 02:29:38', 'Approved', 4, 'indro', 'indro', '2022-02-04', '2022-02-04 02:29:38', NULL, NULL, '2022-02-04', '2022-02-04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kontrabon_dp`
--

CREATE TABLE `kontrabon_dp` (
  `id` int(11) NOT NULL,
  `no_kbon` varchar(255) NOT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `nama_supp` varchar(255) DEFAULT NULL,
  `no_faktur` varchar(255) DEFAULT NULL,
  `no_dp` varchar(255) DEFAULT NULL,
  `no_po` varchar(255) DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `supp_inv` varchar(255) DEFAULT NULL,
  `tgl_inv` date DEFAULT NULL,
  `subtotal` decimal(16,2) DEFAULT NULL,
  `dp_value` decimal(16,2) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `ceklist` int(11) DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `idtax` int(11) DEFAULT NULL,
  `pph_code` decimal(16,2) DEFAULT NULL,
  `pph_value` decimal(16,2) DEFAULT NULL,
  `post_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_int` int(5) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `confirm_date` date DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `cancel_date` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `lp_inv` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `kontrabon_dp`
--

INSERT INTO `kontrabon_dp` (`id`, `no_kbon`, `tgl_kbon`, `id_jurnal`, `nama_supp`, `no_faktur`, `no_dp`, `no_po`, `tgl_po`, `supp_inv`, `tgl_inv`, `subtotal`, `dp_value`, `total`, `curr`, `ceklist`, `tgl_tempo`, `idtax`, `pph_code`, `pph_value`, `post_date`, `update_date`, `status`, `status_int`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `start_date`, `end_date`, `lp_inv`) VALUES
(1, 'SI/DP/2022/02/00001', '2022-02-04', 0, 'MEKAR JAYA', '-', 'FTR/D/NAG/0222/00001', 'PO/0220/00147', '2020-02-27', '-', '2022-02-04', '2137750.00', '1068875.00', '0.00', 'IDR', 1, '2022-02-04', NULL, NULL, '0.00', '2022-02-04 01:09:22', '2022-02-04 01:09:22', 'Approved', 4, 'indro', 'indro', '2022-02-04', '2022-02-04 01:09:22', NULL, NULL, '2019-01-01', '2022-02-04', '1'),
(2, 'SI/DP/2022/02/00002', '2022-02-04', 0, 'Singa Global Textile 2,PT', '-', 'FTR/D/NAG/0222/00002', 'SML/1219/019/02246', '2019-12-21', '-', '2022-02-04', '51154543.35', '25577271.68', '0.00', 'IDR', 1, '2022-02-04', NULL, NULL, '0.00', '2022-02-04 01:59:12', '2022-02-04 01:59:12', 'Approved', 4, 'indro', 'indro', '2022-02-04', '2022-02-04 01:59:12', NULL, NULL, '2022-02-04', '2022-02-04', '1'),
(3, 'SI/DP/2022/02/00003', '2022-02-04', 0, 'Singa Global Textile 2,PT', '-', 'FTR/D/NAG/0222/00003', 'SML/1219/016/02244', '2019-12-21', '-', '2022-02-04', '43341816.35', '19503817.36', '0.00', 'IDR', 1, '2022-02-04', NULL, NULL, '0.00', '2022-02-04 02:48:01', '2022-02-04 02:48:01', 'Approved', 4, 'indro', 'indro', '2022-02-04', '2022-02-04 02:48:01', NULL, NULL, '2022-02-04', '2022-02-04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kontrabon_h`
--

CREATE TABLE `kontrabon_h` (
  `id` int(11) NOT NULL,
  `no_kbon` varchar(255) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `no_po` varchar(200) DEFAULT NULL,
  `nama_supp` varchar(255) DEFAULT NULL,
  `no_faktur` varchar(255) DEFAULT NULL,
  `supp_inv` varchar(255) DEFAULT NULL,
  `tgl_inv` date DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `subtotal` decimal(16,2) DEFAULT NULL,
  `tax` decimal(16,2) DEFAULT NULL,
  `pph_idr` decimal(16,2) DEFAULT NULL,
  `rate` decimal(16,2) DEFAULT NULL,
  `pph_fgn` decimal(16,2) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `dp_value` decimal(16,2) DEFAULT NULL,
  `balance` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `confirm_date` date DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `cancel_date` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `post_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `kontrabon_h`
--

INSERT INTO `kontrabon_h` (`id`, `no_kbon`, `tgl_kbon`, `no_po`, `nama_supp`, `no_faktur`, `supp_inv`, `tgl_inv`, `tgl_tempo`, `subtotal`, `tax`, `pph_idr`, `rate`, `pph_fgn`, `total`, `dp_value`, `balance`, `curr`, `status`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `post_date`, `update_date`) VALUES
(9, 'SI/APR/2022/02/00001', '2022-02-04', 'DWT/0120/140/01972', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-02-04', '2020-05-14', '2520.70', '0.00', '0.00', '14347.00', '0.00', '0.00', '2520.70', '0.00', 'USD', 'Cancel', 'indro', NULL, NULL, '2022-02-04 01:02:16', '2022-02-04 01:02:46', 'indro', '2022-02-04 01:02:16', '2022-02-04 01:02:16'),
(10, 'SI/APR/2022/02/00002', '2022-02-04', 'SML/1219/019/02246', 'Singa Global Textile 2,PT', '-', '-', '2022-02-04', '2020-01-13', '73181.82', '0.00', '0.00', '1.00', NULL, '0.00', '73181.82', '0.00', 'IDR', 'Cancel', 'indro', NULL, NULL, '2022-02-04 02:01:57', '2022-02-04 02:07:48', 'indro', '2022-02-04 02:01:57', '2022-02-04 02:01:57'),
(11, 'SI/APR/2022/02/00003', '2022-02-04', 'SML/1219/019/02246', 'Singa Global Textile 2,PT', '-', '-', '2022-02-04', '2020-01-13', '51705634.23', '0.00', '0.00', '1.00', NULL, '26128362.55', '25577271.68', '26128362.55', 'IDR', 'Cancel', 'indro', NULL, NULL, '2022-02-04 02:08:09', '2022-02-04 02:14:35', 'indro', '2022-02-04 02:08:09', '2022-02-04 02:08:09'),
(12, 'SI/APR/2022/02/00004', '2022-02-04', 'SML/1219/019/02246', 'Singa Global Textile 2,PT', '-', '-', '2022-02-04', '2020-01-13', '51705634.23', '0.00', '0.00', '1.00', NULL, '26128362.55', '25577271.68', '11128362.55', 'IDR', 'Approved', 'indro', 'indro', '2022-02-04', '2022-02-04 02:28:56', NULL, NULL, '2022-02-04 02:28:56', '2022-02-04 02:28:56'),
(13, 'SI/APR/2022/02/00005', '2022-02-04', 'C/PTX/1221/05570', 'ALMINDO PRATAMA CV', '-', '-', '2022-02-04', '2022-02-10', '92.30', '0.00', '0.00', '14347.00', '0.00', '53.65', '0.00', '53.65', 'USD', 'Cancel', 'indro', NULL, NULL, '2022-02-04 09:03:53', '2022-02-04 09:47:32', 'indro', '2022-02-04 09:03:53', '2022-02-04 09:03:53'),
(14, 'SI/APR/2022/02/00006', '2022-02-04', 'PTX/1221/037/05383', 'ALMINDO PRATAMA CV', '-', '-', '2022-02-04', '2022-02-10', '14.02', '0.00', '0.00', '14347.00', '0.00', '0.00', '0.00', '0.00', 'USD', 'Cancel', 'indro', NULL, NULL, '2022-02-04 09:53:54', '2022-02-04 09:54:37', 'indro', '2022-02-04 09:53:54', '2022-02-04 09:53:54'),
(15, 'SI/APR/2022/02/00007', '2022-02-04', 'PTX/1221/037/05383', 'ALMINDO PRATAMA CV', '-', '-', '2022-02-04', '2022-02-10', '14.02', '0.00', '0.00', '14347.00', '0.00', '0.00', '0.00', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-02-04', '2022-02-04 10:13:51', NULL, NULL, '2022-02-04 10:13:51', '2022-02-04 10:13:51'),
(16, 'SI/APR/2022/02/00008', '2022-02-04', 'PTX/1221/036/00225', 'ALMINDO PRATAMA CV', '-', '-', '2022-02-04', '2022-02-19', '12.47', '0.00', '0.00', '14347.00', '0.00', '0.00', '0.00', '0.00', 'USD', 'Cancel', 'indro', NULL, NULL, '2022-02-04 10:46:15', '2022-02-04 10:49:26', 'indro', '2022-02-04 10:46:15', '2022-02-04 10:46:15'),
(17, 'SI/APR/2022/02/00009', '2022-02-07', 'PTX/1221/037/05383', 'ALMINDO PRATAMA CV', '-', '-', '2022-02-07', '2022-02-19', '12.46', '0.00', '0.00', '14376.00', '0.00', '9.28', '0.00', '9.28', 'USD', 'Cancel', 'indro', NULL, NULL, '2022-02-07 10:46:58', '2022-02-08 01:22:10', 'indro', '2022-02-07 10:46:58', '2022-02-07 10:46:58'),
(18, 'SI/APR/2022/02/00010', '2022-02-07', 'PTX/1221/037/00117', 'ALMINDO PRATAMA CV', '-', '-', '2022-02-07', '2022-02-19', '4.84', '0.00', '0.00', '14376.00', '0.00', '0.00', '0.00', '0.00', 'USD', 'Cancel', 'indro', NULL, NULL, '2022-02-07 10:54:50', '2022-02-08 01:22:12', 'indro', '2022-02-07 10:54:50', '2022-02-07 10:54:50');

-- --------------------------------------------------------

--
-- Table structure for table `kontrabon_h_cbd`
--

CREATE TABLE `kontrabon_h_cbd` (
  `id` int(11) NOT NULL,
  `no_po` varchar(50) DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `no_kbon` varchar(255) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `nama_supp` varchar(255) DEFAULT NULL,
  `no_faktur` varchar(255) DEFAULT NULL,
  `supp_inv` varchar(255) DEFAULT NULL,
  `tgl_inv` date DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `subtotal` decimal(16,2) DEFAULT NULL,
  `tax` decimal(16,2) DEFAULT NULL,
  `pph` decimal(16,2) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `amount_update` decimal(16,4) DEFAULT NULL,
  `balance` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `confirm_date` date DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `cancel_date` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `post_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `kontrabon_h_cbd`
--

INSERT INTO `kontrabon_h_cbd` (`id`, `no_po`, `tgl_po`, `no_kbon`, `tgl_kbon`, `nama_supp`, `no_faktur`, `supp_inv`, `tgl_inv`, `tgl_tempo`, `subtotal`, `tax`, `pph`, `total`, `amount_update`, `balance`, `curr`, `status`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `post_date`, `update_date`) VALUES
(2, 'DWT/0120/140/01972', '2020-03-31', 'SI/CBD/2022/02/00001', '2022-02-04', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-02-04', '2022-02-04', '4959.50', '0.00', '0.00', '4959.50', '4020.7000', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-02-04', '2022-02-04 00:36:38', NULL, NULL, '2022-02-04 00:36:38', '2022-02-04 00:36:38'),
(3, 'C/DWT/0320/01214', '2020-03-05', 'SI/CBD/2022/02/00002', '2022-02-04', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-02-04', '2022-02-04', '281.60', '0.00', '0.00', '281.60', '281.6000', '281.60', 'USD', 'Approved', 'indro', 'indro', '2022-02-04', '2022-02-04 02:29:38', NULL, NULL, '2022-02-04 02:29:38', '2022-02-04 02:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `kontrabon_h_dp`
--

CREATE TABLE `kontrabon_h_dp` (
  `id` int(11) NOT NULL,
  `no_kbon` varchar(255) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `nama_supp` varchar(255) DEFAULT NULL,
  `no_faktur` varchar(255) DEFAULT NULL,
  `supp_inv` varchar(255) DEFAULT NULL,
  `tgl_inv` date DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `subtotal` decimal(16,2) DEFAULT NULL,
  `dp_value` decimal(16,2) DEFAULT NULL,
  `pph` decimal(16,2) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `balance` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `confirm_date` date DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `cancel_date` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `post_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `kontrabon_h_dp`
--

INSERT INTO `kontrabon_h_dp` (`id`, `no_kbon`, `tgl_kbon`, `nama_supp`, `no_faktur`, `supp_inv`, `tgl_inv`, `tgl_tempo`, `subtotal`, `dp_value`, `pph`, `total`, `balance`, `curr`, `status`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `post_date`, `update_date`) VALUES
(1, 'SI/DP/2022/02/00001', '2022-02-04', 'MEKAR JAYA', '-', '-', '2022-02-04', '2022-02-04', '2137750.00', '1068875.00', NULL, '1068875.00', '0.00', 'IDR', 'Approved', 'indro', 'indro', '2022-02-04', '2022-02-04 01:09:22', NULL, NULL, '2022-02-04 01:09:22', '2022-02-04 01:09:22'),
(2, 'SI/DP/2022/02/00002', '2022-02-04', 'Singa Global Textile 2,PT', '-', '-', '2022-02-04', '2022-02-04', '51154543.35', '25577271.68', NULL, '25577271.68', '0.00', 'IDR', 'Approved', 'indro', 'indro', '2022-02-04', '2022-02-04 01:59:12', NULL, NULL, '2022-02-04 01:59:12', '2022-02-04 01:59:12'),
(3, 'SI/DP/2022/02/00003', '2022-02-04', 'Singa Global Textile 2,PT', '-', '-', '2022-02-04', '2022-02-04', '43341816.35', '19503817.36', NULL, '19503817.36', '19503817.36', 'IDR', 'Approved', 'indro', 'indro', '2022-02-04', '2022-02-04 02:48:01', NULL, NULL, '2022-02-04 02:48:01', '2022-02-04 02:48:01');

-- --------------------------------------------------------

--
-- Table structure for table `list_payment`
--

CREATE TABLE `list_payment` (
  `id` int(11) NOT NULL,
  `no_payment` varchar(255) NOT NULL,
  `tgl_payment` date DEFAULT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `nama_supp` varchar(255) DEFAULT NULL,
  `no_bpb` varchar(50) DEFAULT NULL,
  `tgl_bpb` date DEFAULT NULL,
  `no_po` varchar(50) DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `pph_value` decimal(16,2) DEFAULT NULL,
  `no_kbon` varchar(255) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `total_kbon` decimal(16,2) DEFAULT NULL,
  `outstanding` decimal(16,2) DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `top` varchar(255) DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `post_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_int` int(5) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `confirm_date` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `cancel_date` timestamp NULL DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `list_payment`
--

INSERT INTO `list_payment` (`id`, `no_payment`, `tgl_payment`, `id_jurnal`, `nama_supp`, `no_bpb`, `tgl_bpb`, `no_po`, `tgl_po`, `pph_value`, `no_kbon`, `tgl_kbon`, `total_kbon`, `outstanding`, `amount`, `curr`, `top`, `tgl_tempo`, `memo`, `post_date`, `update_date`, `status`, `status_int`, `create_user`, `create_date`, `confirm_user`, `confirm_date`, `cancel_user`, `cancel_date`, `start_date`, `end_date`) VALUES
(1, 'LP/NAG/0222/00001', '2022-02-04', NULL, 'Singa Global Textile 2,PT', 'GK/IN/0120/00096', '2020-01-13', 'SML/1219/019/02246', '2019-12-21', '0.00', 'SI/APR/2022/02/00004', '2022-02-04', '26128362.55', '26128362.55', '10000000.00', 'IDR', '-753', '2020-01-13', '-', '2022-02-04 02:50:59', '2022-02-04 02:50:59', 'Cancel', 1, 'indro', '2022-02-04 02:50:59', NULL, NULL, 'indro', '2022-02-04 02:52:55', NULL, NULL),
(2, 'LP/NAG/0222/00002', '2022-02-04', NULL, 'Singa Global Textile 2,PT', 'GK/IN/0120/00096', '2020-01-13', 'SML/1219/019/02246', '2019-12-21', '0.00', 'SI/APR/2022/02/00004', '2022-02-04', '26128362.55', '16128362.55', '10000000.00', 'IDR', '-753', '2020-01-13', '-', '2022-02-04 02:56:33', '2022-02-04 02:56:33', 'Closed', 5, 'indro', '2022-02-04 02:56:33', 'indro', '2022-02-04 02:56:58', NULL, NULL, NULL, NULL),
(3, 'LP/NAG/0222/00003', '2022-02-04', NULL, 'Singa Global Textile 2,PT', 'GK/IN/0120/00096', '2020-01-13', 'SML/1219/019/02246', '2019-12-21', '0.00', 'SI/APR/2022/02/00004', '2022-02-04', '26128362.55', '11128362.55', '5000000.00', 'IDR', '-753', '2020-01-13', '', '2022-02-04 02:56:55', '2022-02-04 02:56:55', 'Closed', 5, 'indro', '2022-02-04 02:56:55', 'indro', '2022-02-04 02:56:59', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `list_payment_cbd`
--

CREATE TABLE `list_payment_cbd` (
  `id` int(11) NOT NULL,
  `no_payment` varchar(255) NOT NULL,
  `tgl_payment` date DEFAULT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `nama_supp` varchar(255) DEFAULT NULL,
  `no_kbon` varchar(255) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `no_po` varchar(50) DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `total_kbon` decimal(16,2) DEFAULT NULL,
  `outstanding` decimal(16,2) DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `amount_update` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `top` varchar(255) DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `post_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_int` int(5) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `confirm_date` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `cancel_date` timestamp NULL DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `list_payment_cbd`
--

INSERT INTO `list_payment_cbd` (`id`, `no_payment`, `tgl_payment`, `id_jurnal`, `nama_supp`, `no_kbon`, `tgl_kbon`, `no_po`, `tgl_po`, `total_kbon`, `outstanding`, `amount`, `amount_update`, `curr`, `top`, `tgl_tempo`, `memo`, `post_date`, `update_date`, `status`, `status_int`, `create_user`, `create_date`, `confirm_user`, `confirm_date`, `cancel_user`, `cancel_date`, `start_date`, `end_date`) VALUES
(3, 'LP/CBD/NAG/0222/00001', '2022-02-04', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'SI/CBD/2022/02/00001', '2022-02-04', 'DWT/0120/140/01972', '2020-03-31', '4959.50', '3459.50', '1500.00', '1500.00', 'USD', NULL, '2022-02-04', '', '2022-02-04 00:37:04', '2022-02-04 00:37:04', 'Approved', 5, 'indro', '2022-02-04 00:37:04', 'indro', '2022-02-04 00:37:07', NULL, NULL, NULL, NULL),
(4, 'LP/CBD/NAG/0222/00002', '2022-02-04', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'SI/CBD/2022/02/00001', '2022-02-04', 'DWT/0120/140/01972', '2020-03-31', '4959.50', '1459.50', '2000.00', '2000.00', 'USD', NULL, '2022-02-04', '', '2022-02-04 00:37:23', '2022-02-04 00:37:23', 'Approved', 5, 'indro', '2022-02-04 00:37:23', 'indro', '2022-02-04 00:37:26', NULL, NULL, NULL, NULL),
(5, 'LP/CBD/NAG/0222/00003', '2022-02-04', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'SI/CBD/2022/02/00001', '2022-02-04', 'DWT/0120/140/01972', '2020-03-31', '4959.50', '0.00', '1459.50', '1459.50', 'USD', NULL, '2022-02-04', '', '2022-02-04 00:37:50', '2022-02-04 00:37:50', 'Approved', 5, 'indro', '2022-02-04 00:37:50', 'indro', '2022-02-04 00:37:53', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `list_payment_dp`
--

CREATE TABLE `list_payment_dp` (
  `id` int(11) NOT NULL,
  `no_payment` varchar(255) NOT NULL,
  `tgl_payment` date DEFAULT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `nama_supp` varchar(255) DEFAULT NULL,
  `no_po` varchar(100) DEFAULT NULL,
  `tgl_po` date DEFAULT NULL,
  `pph_value` decimal(16,2) DEFAULT NULL,
  `no_kbon` varchar(255) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `total_kbon` decimal(16,2) DEFAULT NULL,
  `outstanding` decimal(16,2) DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT NULL,
  `curr` varchar(255) DEFAULT NULL,
  `top` varchar(255) DEFAULT NULL,
  `tgl_tempo` date DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `post_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_int` int(5) DEFAULT NULL,
  `create_user` varchar(255) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `confirm_user` varchar(255) DEFAULT NULL,
  `confirm_date` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(255) DEFAULT NULL,
  `cancel_date` timestamp NULL DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `list_payment_dp`
--

INSERT INTO `list_payment_dp` (`id`, `no_payment`, `tgl_payment`, `id_jurnal`, `nama_supp`, `no_po`, `tgl_po`, `pph_value`, `no_kbon`, `tgl_kbon`, `total_kbon`, `outstanding`, `amount`, `curr`, `top`, `tgl_tempo`, `memo`, `post_date`, `update_date`, `status`, `status_int`, `create_user`, `create_date`, `confirm_user`, `confirm_date`, `cancel_user`, `cancel_date`, `start_date`, `end_date`) VALUES
(1, 'LP/DP/NAG/0222/00001', '2022-02-04', NULL, 'MEKAR JAYA', 'PO/0220/00147', '2020-02-27', '0.00', 'SI/DP/2022/02/00001', '2022-02-04', '1068875.00', '568875.00', '500000.00', 'IDR', NULL, '2022-02-04', '-', '2022-02-04 01:14:50', '2022-02-04 01:14:50', 'Closed', 5, 'indro', '2022-02-04 01:14:50', 'indro', '2022-02-04 01:14:56', NULL, NULL, NULL, NULL),
(2, 'LP/DP/NAG/0222/00002', '2022-02-04', NULL, 'MEKAR JAYA', 'PO/0220/00147', '2020-02-27', '0.00', 'SI/DP/2022/02/00001', '2022-02-04', '1068875.00', '0.00', '568875.00', 'IDR', NULL, '2022-02-04', '-', '2022-02-04 01:15:25', '2022-02-04 01:15:25', 'Approved', 5, 'indro', '2022-02-04 01:15:25', 'indro', '2022-02-04 01:15:28', NULL, NULL, NULL, NULL),
(3, 'LP/DP/NAG/0222/00003', '2022-02-04', NULL, 'Singa Global Textile 2,PT', 'SML/1219/019/02246', '2019-12-21', '0.00', 'SI/DP/2022/02/00002', '2022-02-04', '25577271.68', '10577271.68', '15000000.00', 'IDR', NULL, '2022-02-04', '', '2022-02-04 01:59:39', '2022-02-04 01:59:39', 'Approved', 5, 'indro', '2022-02-04 01:59:39', 'indro', '2022-02-04 01:59:42', NULL, NULL, NULL, NULL),
(4, 'LP/DP/NAG/0222/00004', '2022-02-04', NULL, 'Singa Global Textile 2,PT', 'SML/1219/019/02246', '2019-12-21', '0.00', 'SI/DP/2022/02/00002', '2022-02-04', '25577271.68', '0.00', '10577271.68', 'IDR', NULL, '2022-02-04', '', '2022-02-04 01:59:58', '2022-02-04 01:59:58', 'Approved', 5, 'indro', '2022-02-04 01:59:58', 'indro', '2022-02-04 02:00:00', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menurole`
--

CREATE TABLE `menurole` (
  `id` int(11) NOT NULL,
  `menu` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `menurole`
--

INSERT INTO `menurole` (`id`, `menu`) VALUES
(1, 'Approve BPB'),
(2, 'Verifikasi BPB'),
(3, 'Create BPB'),
(4, 'FTR'),
(5, 'Create FTR'),
(6, 'Kontrabon'),
(7, 'Create Kontrabon'),
(8, 'List Payment'),
(9, 'Create List payment'),
(10, 'Payment'),
(11, 'Create Payment'),
(12, 'Maintain FTR'),
(13, 'Create Maintain FTR'),
(14, 'Maintain Kontrabon'),
(15, 'Create Maintain Kontrabon'),
(16, 'Maintain List Payment'),
(17, 'Create Maintain List Payment'),
(18, 'Report'),
(19, 'Approve BPB Return'),
(20, 'verifikasi BPB Return'),
(21, 'Create BPB Return'),
(22, 'Closing Payment');

-- --------------------------------------------------------

--
-- Table structure for table `payment_ftr`
--

CREATE TABLE `payment_ftr` (
  `id` int(11) NOT NULL,
  `payment_ftr_id` varchar(100) DEFAULT NULL,
  `tgl_pelunasan` date DEFAULT NULL,
  `nama_supp` varchar(100) DEFAULT NULL,
  `list_payment_id` varchar(30) DEFAULT NULL,
  `tgl_list_payment` date DEFAULT NULL,
  `no_kbon` varchar(30) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `valuta_ftr` varchar(10) DEFAULT NULL,
  `ttl_bayar` decimal(16,2) DEFAULT NULL,
  `cara_bayar` varchar(30) DEFAULT NULL,
  `account` varchar(30) DEFAULT NULL,
  `bank` varchar(30) DEFAULT NULL,
  `valuta_bayar` varchar(10) DEFAULT NULL,
  `nominal` decimal(16,2) DEFAULT NULL,
  `nominal_fgn` decimal(16,2) DEFAULT NULL,
  `rate` decimal(16,2) DEFAULT NULL,
  `keterangan` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `create_user` varchar(100) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `closed_date` timestamp NULL DEFAULT NULL,
  `closed_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment_ftr`
--

INSERT INTO `payment_ftr` (`id`, `payment_ftr_id`, `tgl_pelunasan`, `nama_supp`, `list_payment_id`, `tgl_list_payment`, `no_kbon`, `tgl_kbon`, `valuta_ftr`, `ttl_bayar`, `cara_bayar`, `account`, `bank`, `valuta_bayar`, `nominal`, `nominal_fgn`, `rate`, `keterangan`, `status`, `create_user`, `create_date`, `closed_date`, `closed_by`) VALUES
(1, 'PAY/LP/NAG/0222/00001', '2022-02-04', 'Singa Global Textile 2,PT', 'LP/NAG/0222/00002', '2022-02-04', 'SI/APR/2022/02/00004', '2022-02-04', 'IDR', '10000000.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '10000000.00', '0.00', '0.00', 'Closed', 'draft', 'indro', '2022-02-04 03:00:31', '2022-02-07 04:44:05', 'indro'),
(2, 'PAY/LP/NAG/0222/00001', '2022-02-04', 'Singa Global Textile 2,PT', 'LP/NAG/0222/00003', '2022-02-04', 'SI/APR/2022/02/00004', '2022-02-04', 'IDR', '5000000.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '5000000.00', '0.00', '0.00', 'Closed', 'draft', 'indro', '2022-02-04 03:00:31', '2022-02-07 04:44:05', 'indro');

-- --------------------------------------------------------

--
-- Table structure for table `payment_ftrcbd`
--

CREATE TABLE `payment_ftrcbd` (
  `id` int(11) NOT NULL,
  `payment_ftr_id` varchar(100) DEFAULT NULL,
  `tgl_pelunasan` date DEFAULT NULL,
  `nama_supp` varchar(100) DEFAULT NULL,
  `list_payment_id` varchar(30) DEFAULT NULL,
  `tgl_list_payment` date DEFAULT NULL,
  `no_kbon` varchar(30) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `valuta_ftr` varchar(10) DEFAULT NULL,
  `ttl_bayar` decimal(16,2) DEFAULT NULL,
  `cara_bayar` varchar(30) DEFAULT NULL,
  `account` varchar(30) DEFAULT NULL,
  `bank` varchar(30) DEFAULT NULL,
  `valuta_bayar` varchar(10) DEFAULT NULL,
  `nominal` decimal(16,2) DEFAULT NULL,
  `nominal_fgn` decimal(16,2) DEFAULT NULL,
  `rate` decimal(16,2) DEFAULT NULL,
  `keterangan` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `create_user` varchar(100) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `closed_date` timestamp NULL DEFAULT NULL,
  `closed_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment_ftrcbd`
--

INSERT INTO `payment_ftrcbd` (`id`, `payment_ftr_id`, `tgl_pelunasan`, `nama_supp`, `list_payment_id`, `tgl_list_payment`, `no_kbon`, `tgl_kbon`, `valuta_ftr`, `ttl_bayar`, `cara_bayar`, `account`, `bank`, `valuta_bayar`, `nominal`, `nominal_fgn`, `rate`, `keterangan`, `status`, `create_user`, `create_date`, `closed_date`, `closed_by`) VALUES
(23, 'PAY/LP/CBD/NAG/0222/00001', '2022-02-04', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0222/00002', '2022-02-04', 'SI/CBD/2022/02/00001', '2022-02-04', 'USD', '2000.00', 'TRANSFER', '008-998-1982', 'BANK CENTRAL ASIA', 'USD', '0.00', '2000.00', '0.00', 'Paid', 'draft', 'indro', '2022-02-04 00:54:09', '2022-02-07 08:16:21', 'indro'),
(24, 'PAY/LP/CBD/NAG/0222/00001', '2022-02-04', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0222/00003', '2022-02-04', 'SI/CBD/2022/02/00001', '2022-02-04', 'USD', '1459.50', 'TRANSFER', '008-998-1982', 'BANK CENTRAL ASIA', 'USD', '0.00', '1459.50', '0.00', 'Paid', 'draft', 'indro', '2022-02-04 00:54:09', '2022-02-07 08:16:21', 'indro'),
(26, 'PAY/LP/CBD/NAG/0222/00002', '2022-02-04', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0222/00001', '2022-02-04', 'SI/CBD/2022/02/00001', '2022-02-04', 'USD', '1500.00', 'TRANSFER', '008-997-1979', 'BANK CENTRAL ASIA', 'IDR', '21300000.00', '1500.00', '14200.00', 'Paid', 'draft', 'indro', '2022-02-04 01:01:30', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_ftrdp`
--

CREATE TABLE `payment_ftrdp` (
  `id` int(11) NOT NULL,
  `payment_ftr_id` varchar(100) DEFAULT NULL,
  `tgl_pelunasan` date DEFAULT NULL,
  `nama_supp` varchar(100) DEFAULT NULL,
  `list_payment_id` varchar(30) DEFAULT NULL,
  `tgl_list_payment` date DEFAULT NULL,
  `no_kbon` varchar(30) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `valuta_ftr` varchar(10) DEFAULT NULL,
  `ttl_bayar` decimal(16,2) DEFAULT NULL,
  `cara_bayar` varchar(30) DEFAULT NULL,
  `account` varchar(30) DEFAULT NULL,
  `bank` varchar(30) DEFAULT NULL,
  `valuta_bayar` varchar(10) DEFAULT NULL,
  `nominal` decimal(16,2) DEFAULT NULL,
  `nominal_fgn` decimal(16,2) DEFAULT NULL,
  `rate` decimal(16,2) DEFAULT NULL,
  `keterangan` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `create_user` varchar(100) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `closed_date` timestamp NULL DEFAULT NULL,
  `closed_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment_ftrdp`
--

INSERT INTO `payment_ftrdp` (`id`, `payment_ftr_id`, `tgl_pelunasan`, `nama_supp`, `list_payment_id`, `tgl_list_payment`, `no_kbon`, `tgl_kbon`, `valuta_ftr`, `ttl_bayar`, `cara_bayar`, `account`, `bank`, `valuta_bayar`, `nominal`, `nominal_fgn`, `rate`, `keterangan`, `status`, `create_user`, `create_date`, `closed_date`, `closed_by`) VALUES
(1, 'PAY/LP/DP/NAG/0222/00001', '2022-02-04', 'MEKAR JAYA', 'LP/DP/NAG/0222/00001', '2022-02-04', 'SI/DP/2022/02/00001', '2022-02-04', 'IDR', '500000.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '500000.00', '0.00', '0.00', 'Closed', 'draft', 'indro', '2022-02-04 01:45:01', '2022-02-07 08:48:46', 'indro'),
(2, 'PAY/LP/DP/NAG/0222/00002', '2022-02-04', 'MEKAR JAYA', 'LP/DP/NAG/0222/00002', '2022-02-04', 'SI/DP/2022/02/00001', '2022-02-04', 'IDR', '568875.00', 'TRANSFER', '008-997-1979', 'BANK CENTRAL ASIA', 'IDR', '568875.00', '0.00', '0.00', 'Paid', 'draft', 'indro', '2022-02-04 01:46:09', NULL, NULL),
(3, 'PAY/LP/DP/NAG/0222/00003', '2022-02-04', 'Singa Global Textile 2,PT', 'LP/DP/NAG/0222/00003', '2022-02-04', 'SI/DP/2022/02/00002', '2022-02-04', 'IDR', '15000000.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '15000000.00', '0.00', '0.00', 'Paid', 'draft', 'indro', '2022-02-04 02:00:24', NULL, NULL),
(4, 'PAY/LP/DP/NAG/0222/00003', '2022-02-04', 'Singa Global Textile 2,PT', 'LP/DP/NAG/0222/00004', '2022-02-04', 'SI/DP/2022/02/00002', '2022-02-04', 'IDR', '10577271.68', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '10577271.68', '0.00', '0.00', 'Paid', 'draft', 'indro', '2022-02-04 02:00:24', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_ftrcbd`
--

CREATE TABLE `pengajuan_ftrcbd` (
  `id` int(5) NOT NULL,
  `no_cbd` varchar(50) DEFAULT NULL,
  `tgl_cbd` date DEFAULT NULL,
  `no_po` varchar(50) DEFAULT NULL,
  `nama_supp` varchar(200) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `curr` varchar(20) DEFAULT NULL,
  `tgl_pengajuan` date DEFAULT NULL,
  `nama_pengaju` varchar(100) DEFAULT NULL,
  `pesan` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `approved_user` varchar(50) DEFAULT NULL,
  `tgl_approved` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(50) DEFAULT NULL,
  `tgl_cancel` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengajuan_ftrcbd`
--

INSERT INTO `pengajuan_ftrcbd` (`id`, `no_cbd`, `tgl_cbd`, `no_po`, `nama_supp`, `total`, `curr`, `tgl_pengajuan`, `nama_pengaju`, `pesan`, `status`, `approved_user`, `tgl_approved`, `cancel_user`, `tgl_cancel`) VALUES
(1, 'FTR/C/NAG/0222/00003', '2022-02-04', 'DWT/1219/163/00423', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '77.00', 'USD', '2022-02-04', 'indro', 'batal', 'Waiting', '-', NULL, '-', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_ftrdp`
--

CREATE TABLE `pengajuan_ftrdp` (
  `id` int(5) NOT NULL,
  `no_cbd` varchar(50) DEFAULT NULL,
  `tgl_cbd` date DEFAULT NULL,
  `no_po` varchar(50) DEFAULT NULL,
  `nama_supp` varchar(200) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `dp` decimal(16,2) DEFAULT NULL,
  `curr` varchar(20) DEFAULT NULL,
  `tgl_pengajuan` date DEFAULT NULL,
  `nama_pengaju` varchar(100) DEFAULT NULL,
  `pesan` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `approved_user` varchar(50) DEFAULT NULL,
  `tgl_approved` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(50) DEFAULT NULL,
  `tgl_cancel` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_kb`
--

CREATE TABLE `pengajuan_kb` (
  `id` int(5) NOT NULL,
  `no_kbon` varchar(50) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `no_bpb` varchar(50) DEFAULT NULL,
  `nama_supp` varchar(100) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `tgl_pengajuan` date DEFAULT NULL,
  `nama_pengaju` varchar(100) DEFAULT NULL,
  `pesan` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `approved_user` varchar(50) DEFAULT NULL,
  `tgl_approved` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(50) DEFAULT NULL,
  `tgl_cancel` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_kb_cbd`
--

CREATE TABLE `pengajuan_kb_cbd` (
  `id` int(5) NOT NULL,
  `no_kbon` varchar(50) DEFAULT NULL,
  `no_dp` varchar(50) DEFAULT NULL,
  `nama_supp` varchar(100) DEFAULT NULL,
  `tgl_pengajuan` date DEFAULT NULL,
  `nama_pengaju` varchar(100) DEFAULT NULL,
  `pesan` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `approved_user` varchar(50) DEFAULT NULL,
  `tgl_approved` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(50) DEFAULT NULL,
  `tgl_cancel` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_kb_dp`
--

CREATE TABLE `pengajuan_kb_dp` (
  `id` int(5) NOT NULL,
  `no_kbon` varchar(50) DEFAULT NULL,
  `no_dp` varchar(50) DEFAULT NULL,
  `nama_supp` varchar(100) DEFAULT NULL,
  `tgl_pengajuan` date DEFAULT NULL,
  `nama_pengaju` varchar(100) DEFAULT NULL,
  `pesan` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `approved_user` varchar(50) DEFAULT NULL,
  `tgl_approved` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(50) DEFAULT NULL,
  `tgl_cancel` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_payment`
--

CREATE TABLE `pengajuan_payment` (
  `id` int(5) NOT NULL,
  `no_payment` varchar(50) DEFAULT NULL,
  `tgl_payment` date DEFAULT NULL,
  `no_kbon` varchar(50) DEFAULT NULL,
  `nama_supp` varchar(200) DEFAULT NULL,
  `total_kbon` decimal(16,2) DEFAULT NULL,
  `total_amount` decimal(16,2) DEFAULT NULL,
  `tgl_pengajuan` date DEFAULT NULL,
  `nama_pengaju` varchar(100) DEFAULT NULL,
  `pesan` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `approved_user` varchar(50) DEFAULT NULL,
  `tgl_approved` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(50) DEFAULT NULL,
  `tgl_cancel` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_paymentcbd`
--

CREATE TABLE `pengajuan_paymentcbd` (
  `id` int(5) NOT NULL,
  `no_payment` varchar(50) DEFAULT NULL,
  `tgl_payment` date DEFAULT NULL,
  `no_kbon` varchar(50) DEFAULT NULL,
  `nama_supp` varchar(200) DEFAULT NULL,
  `total_kbon` decimal(16,2) DEFAULT NULL,
  `total_amount` decimal(16,2) DEFAULT NULL,
  `tgl_pengajuan` date DEFAULT NULL,
  `nama_pengaju` varchar(100) DEFAULT NULL,
  `pesan` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `approved_user` varchar(50) DEFAULT NULL,
  `tgl_approved` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(50) DEFAULT NULL,
  `tgl_cancel` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_paymentdp`
--

CREATE TABLE `pengajuan_paymentdp` (
  `id` int(5) NOT NULL,
  `no_payment` varchar(50) DEFAULT NULL,
  `tgl_payment` date DEFAULT NULL,
  `no_kbon` varchar(50) DEFAULT NULL,
  `nama_supp` varchar(200) DEFAULT NULL,
  `total_kbon` decimal(16,2) DEFAULT NULL,
  `total_amount` decimal(16,2) DEFAULT NULL,
  `tgl_pengajuan` date DEFAULT NULL,
  `nama_pengaju` varchar(100) DEFAULT NULL,
  `pesan` varchar(500) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `approved_user` varchar(50) DEFAULT NULL,
  `tgl_approved` timestamp NULL DEFAULT NULL,
  `cancel_user` varchar(50) DEFAULT NULL,
  `tgl_cancel` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `potongan`
--

CREATE TABLE `potongan` (
  `id` int(11) NOT NULL,
  `no_kbon` varchar(100) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `nama_supp` varchar(320) DEFAULT NULL,
  `jml_return` decimal(16,2) DEFAULT NULL,
  `lr_kurs` decimal(16,2) DEFAULT NULL,
  `s_qty` decimal(16,2) DEFAULT NULL,
  `s_harga` decimal(16,2) DEFAULT NULL,
  `materai` decimal(16,2) DEFAULT NULL,
  `pot_beli` decimal(16,2) DEFAULT NULL,
  `ekspedisi` decimal(16,2) DEFAULT NULL,
  `moq` decimal(16,2) DEFAULT NULL,
  `jml_potong` decimal(16,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `potongan`
--

INSERT INTO `potongan` (`id`, `no_kbon`, `tgl_kbon`, `nama_supp`, `jml_return`, `lr_kurs`, `s_qty`, `s_harga`, `materai`, `pot_beli`, `ekspedisi`, `moq`, `jml_potong`, `status`) VALUES
(10, 'SI/APR/2022/02/00001', '2022-02-04', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(11, 'SI/APR/2022/02/00002', '2022-02-04', 'Singa Global Textile 2,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(12, 'SI/APR/2022/02/00003', '2022-02-04', 'Singa Global Textile 2,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(13, 'SI/APR/2022/02/00004', '2022-02-04', 'Singa Global Textile 2,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Approved'),
(14, 'SI/APR/2022/02/00005', '2022-02-04', 'ALMINDO PRATAMA CV', '38.65', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(15, 'SI/APR/2022/02/00006', '2022-02-04', 'ALMINDO PRATAMA CV', '14.02', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(16, 'SI/APR/2022/02/00007', '2022-02-04', 'ALMINDO PRATAMA CV', '14.02', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Approved'),
(17, 'SI/APR/2022/02/00008', '2022-02-04', 'ALMINDO PRATAMA CV', '12.47', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(18, 'SI/APR/2022/02/00009', '2022-02-07', 'ALMINDO PRATAMA CV', '3.18', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(19, 'SI/APR/2022/02/00010', '2022-02-07', 'ALMINDO PRATAMA CV', '4.84', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel');

-- --------------------------------------------------------

--
-- Table structure for table `return_kb`
--

CREATE TABLE `return_kb` (
  `id` int(11) NOT NULL,
  `no_kbon` varchar(200) DEFAULT NULL,
  `no_ro` varchar(200) DEFAULT NULL,
  `total_ro` decimal(16,2) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `return_kb`
--

INSERT INTO `return_kb` (`id`, `no_kbon`, `no_ro`, `total_ro`, `status`) VALUES
(1, 'SI/APR/2022/02/00005', 'A08779', '1.01', 'Cancel'),
(2, 'SI/APR/2022/02/00005', '', '0.00', 'Cancel'),
(3, 'SI/APR/2022/02/00005', 'A09059', '37.64', 'Cancel'),
(4, 'SI/APR/2022/02/00006', 'A09059', '13.01', 'Cancel'),
(5, 'SI/APR/2022/02/00006', 'A08779', '1.01', 'Cancel'),
(6, 'SI/APR/2022/02/00007', 'A08779', '1.01', 'draft'),
(7, 'SI/APR/2022/02/00007', 'A09059', '13.01', 'draft'),
(8, 'SI/APR/2022/02/00008', 'A08679', '3.18', 'Cancel'),
(9, 'SI/APR/2022/02/00008', 'A08624', '3.18', 'Cancel'),
(10, 'SI/APR/2022/02/00008', 'A09059', '6.11', 'Cancel'),
(11, 'SI/APR/2022/02/00009', 'A08679', '3.18', 'Cancel'),
(12, 'SI/APR/2022/02/00010', 'A08624', '3.18', 'Cancel'),
(13, 'SI/APR/2022/02/00010', 'A09059', '1.66', 'Cancel');

-- --------------------------------------------------------

--
-- Table structure for table `ttd`
--

CREATE TABLE `ttd` (
  `id` int(11) NOT NULL,
  `create_by` varchar(100) DEFAULT NULL,
  `confirm1` varchar(100) DEFAULT NULL,
  `confirm2` varchar(100) DEFAULT NULL,
  `approve_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ttd`
--

INSERT INTO `ttd` (`id`, `create_by`, `confirm1`, `confirm2`, `approve_by`) VALUES
(1, 'name1', 'Mandy', 'Willy Fernandez', 'Syenni Santosa');

-- --------------------------------------------------------

--
-- Table structure for table `ttl_bppb`
--

CREATE TABLE `ttl_bppb` (
  `id` int(11) NOT NULL,
  `no_ro` varchar(100) DEFAULT NULL,
  `no_bppb` varchar(200) DEFAULT NULL,
  `bppbdate` date DEFAULT NULL,
  `no_bpb` varchar(200) DEFAULT NULL,
  `supp` varchar(300) DEFAULT NULL,
  `total` decimal(16,2) DEFAULT NULL,
  `terpakai` decimal(16,2) DEFAULT NULL,
  `no_kbon` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ttl_bppb`
--

INSERT INTO `ttl_bppb` (`id`, `no_ro`, `no_bppb`, `bppbdate`, `no_bpb`, `supp`, `total`, `terpakai`, `no_kbon`) VALUES
(1, 'A01066', 'SJ-A00013-R', '2020-02-28', 'GACC/IN/1219/01059', 'PT. NATIONAL LABEL', '8314030.00', NULL, NULL),
(2, 'A09059', 'GACC/RO/1221/04117', '2021-12-27', 'GACC/IN/1221/04280', 'ALMINDO PRATAMA CV', '37.64', NULL, NULL),
(3, 'A08779', 'GACC/RO/1221/03998', '2021-12-10', 'GACC/IN/1221/03990', 'ALMINDO PRATAMA CV', '1.01', NULL, NULL),
(4, 'A08624', 'GACC/RO/1221/04000', '2021-12-10', 'GACC/IN/1121/03796', 'ALMINDO PRATAMA CV', '3.18', NULL, NULL),
(5, 'A08679', 'GACC/RO/1221/03999', '2021-12-10', 'GACC/IN/1121/03859', 'ALMINDO PRATAMA CV', '3.18', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `useraccess`
--

CREATE TABLE `useraccess` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `menu` varchar(100) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT NULL,
  `create_user` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `useraccess`
--

INSERT INTO `useraccess` (`id`, `username`, `fullname`, `menu`, `create_date`, `create_user`) VALUES
(96, 'indro', 'Indro', 'Create List payment', '2022-01-10 02:08:47', 'indro'),
(98, 'indro', 'Indro', 'List Payment', '2022-01-10 02:08:47', 'indro'),
(101, 'indro', 'Indro', 'Create Kontrabon', '2022-01-10 02:08:47', 'indro'),
(112, 'indro', 'Indro', 'verifikasi BPB Return', '2022-01-10 02:08:47', 'indro'),
(114, 'ronald', 'RONALD HARSANTO', 'Kontrabon', '2022-01-10 04:53:03', 'indro'),
(115, 'ronald', 'RONALD HARSANTO', 'Approve BPB', '2022-01-10 04:53:03', 'indro'),
(116, 'ronald', 'RONALD HARSANTO', 'Payment', '2022-01-10 04:53:03', 'indro'),
(117, 'ronald', 'RONALD HARSANTO', 'FTR', '2022-01-10 04:53:03', 'indro'),
(118, 'ronald', 'RONALD HARSANTO', 'List Payment', '2022-01-10 04:53:03', 'indro'),
(119, 'ronald', 'RONALD HARSANTO', 'Maintain Kontrabon', '2022-01-10 04:53:03', 'indro'),
(120, 'ronald', 'RONALD HARSANTO', 'Maintain FTR', '2022-01-10 04:53:03', 'indro'),
(121, 'ronald', 'RONALD HARSANTO', 'Maintain List Payment', '2022-01-10 04:53:03', 'indro'),
(122, 'ronald', 'RONALD HARSANTO', 'Approve BPB Return', '2022-01-10 04:53:03', 'indro'),
(123, 'ronald', 'RONALD HARSANTO', 'Report', '2022-01-10 04:53:03', 'indro'),
(136, 'indro', 'Indro', 'FTR', '2022-02-02 06:28:12', 'indro'),
(137, 'indro', 'Indro', 'Verifikasi BPB', '2022-02-02 06:28:12', 'indro'),
(138, 'indro', 'Indro', 'Create FTR', '2022-02-02 06:28:12', 'indro'),
(139, 'indro', 'Indro', 'Create BPB', '2022-02-02 06:28:12', 'indro'),
(140, 'indro', 'Indro', 'Kontrabon', '2022-02-02 06:28:12', 'indro'),
(144, 'indro', 'Indro', 'Payment', '2022-02-02 06:28:12', 'indro'),
(145, 'indro', 'Indro', 'Approve BPB', '2022-02-02 06:32:54', 'indro'),
(146, 'indro', 'Indro', 'Create Maintain Kontrabon', '2022-02-02 06:51:43', 'indro'),
(147, 'indro', 'Indro', 'Create Payment', '2022-02-02 06:51:43', 'indro'),
(148, 'indro', 'Indro', 'Maintain FTR', '2022-02-02 06:51:43', 'indro'),
(149, 'indro', 'Indro', 'Create Maintain FTR', '2022-02-02 06:51:43', 'indro'),
(150, 'indro', 'Indro', 'Maintain List Payment', '2022-02-02 06:51:43', 'indro'),
(151, 'indro', 'Indro', 'Maintain Kontrabon', '2022-02-02 06:51:43', 'indro'),
(152, 'indro', 'Indro', 'Create Maintain List Payment', '2022-02-02 06:51:43', 'indro'),
(153, 'indro', 'Indro', 'Report', '2022-02-02 06:51:43', 'indro'),
(154, 'indro', 'Indro', 'Approve BPB Return', '2022-02-02 06:51:43', 'indro'),
(155, 'indro', 'Indro', 'Create BPB Return', '2022-02-02 06:51:43', 'indro'),
(156, 'indro', 'Indro', 'Closing Payment', '2022-02-07 02:30:18', 'indro');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bpb_new`
--
ALTER TABLE `bpb_new`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `bpb_no_bpb_idx` (`no_bpb`) USING BTREE,
  ADD KEY `bpb_pono_idx` (`pono`) USING BTREE,
  ADD KEY `bpb_ws_idx` (`ws`) USING BTREE,
  ADD KEY `bpb_tgl_bpb_idx` (`tgl_bpb`) USING BTREE,
  ADD KEY `bpb_supplier_idx` (`supplier`) USING BTREE,
  ADD KEY `bpb_itemdesc_idx` (`itemdesc`) USING BTREE,
  ADD KEY `bpb_color_idx` (`color`) USING BTREE,
  ADD KEY `bpb_size_idx` (`size`) USING BTREE,
  ADD KEY `bpb_qty_idx` (`qty`) USING BTREE,
  ADD KEY `bpb_uom_idx` (`uom`) USING BTREE,
  ADD KEY `bpb_price_idx` (`price`) USING BTREE,
  ADD KEY `bpb_tax_idx` (`tax`) USING BTREE,
  ADD KEY `bpb_curr_idx` (`curr`) USING BTREE,
  ADD KEY `bpb_confirm1_idx` (`confirm1`) USING BTREE,
  ADD KEY `bpb_confirm2_idx` (`confirm2`) USING BTREE,
  ADD KEY `bpb_is_invoiced_idx` (`is_invoiced`) USING BTREE,
  ADD KEY `bpb_status_idx` (`status`) USING BTREE,
  ADD KEY `bpb_top_idx` (`top`) USING BTREE,
  ADD KEY `bpb_tgl_po_idx` (`tgl_po`) USING BTREE,
  ADD KEY `bpb_pterms_idx` (`pterms`) USING BTREE;

--
-- Indexes for table `bppb_new`
--
ALTER TABLE `bppb_new`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `bpb_no_bpb_idx` (`no_bppb`) USING BTREE,
  ADD KEY `bpb_pono_idx` (`no_ro`) USING BTREE,
  ADD KEY `bpb_tgl_bpb_idx` (`tgl_bppb`) USING BTREE,
  ADD KEY `bpb_supplier_idx` (`supplier`) USING BTREE,
  ADD KEY `bpb_itemdesc_idx` (`itemdesc`) USING BTREE,
  ADD KEY `bpb_color_idx` (`color`) USING BTREE,
  ADD KEY `bpb_size_idx` (`size`) USING BTREE,
  ADD KEY `bpb_qty_idx` (`qty`) USING BTREE,
  ADD KEY `bpb_uom_idx` (`uom`) USING BTREE,
  ADD KEY `bpb_price_idx` (`price`) USING BTREE,
  ADD KEY `bpb_curr_idx` (`curr`) USING BTREE,
  ADD KEY `bpb_confirm1_idx` (`confirm1`) USING BTREE,
  ADD KEY `bpb_confirm2_idx` (`confirm2`) USING BTREE,
  ADD KEY `bpb_is_invoiced_idx` (`is_invoiced`) USING BTREE,
  ADD KEY `bpb_status_idx` (`status`) USING BTREE;

--
-- Indexes for table `ftr_cbd`
--
ALTER TABLE `ftr_cbd`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `cbd_no_ftr_cbd_idx` (`no_ftr_cbd`) USING BTREE,
  ADD KEY `cbd_tgl_ftr_cbd_idx` (`tgl_ftr_cbd`) USING BTREE,
  ADD KEY `cbd_supp_idx` (`supp`) USING BTREE,
  ADD KEY `cbd_no_po_idx` (`no_po`) USING BTREE,
  ADD KEY `cbd_tgl_po_idx` (`tgl_po`) USING BTREE,
  ADD KEY `cbd_no_pi_idx` (`no_pi`) USING BTREE,
  ADD KEY `cbd_keterangan_idx` (`keterangan`) USING BTREE,
  ADD KEY `cbd_status_idx` (`status`) USING BTREE,
  ADD KEY `cbd_create_user_idx` (`create_user`) USING BTREE,
  ADD KEY `cbd_confirm_user_idx` (`confirm_user`) USING BTREE,
  ADD KEY `cbd_cancel_user_idx` (`cancel_user`) USING BTREE,
  ADD KEY `cbd_create_date_idx` (`create_date`) USING BTREE,
  ADD KEY `cbd_confirm_date_idx` (`confirm_date`) USING BTREE,
  ADD KEY `cbd_cancel_date_idx` (`cancel_date`) USING BTREE,
  ADD KEY `cbd_subtotal_idx` (`subtotal`) USING BTREE,
  ADD KEY `cbd_tax_idx` (`tax`) USING BTREE,
  ADD KEY `cbd_total_idx` (`total`) USING BTREE,
  ADD KEY `cbd_curr_idx` (`curr`) USING BTREE,
  ADD KEY `cbd_is_invoiced_idx` (`is_invoiced`) USING BTREE,
  ADD KEY `cbd_id_po_idx` (`id_po`) USING BTREE;

--
-- Indexes for table `ftr_dp`
--
ALTER TABLE `ftr_dp`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `dp_no_ftr_dp_idx` (`no_ftr_dp`) USING BTREE,
  ADD KEY `dp_tgl_ftr_dp_idx` (`tgl_ftr_dp`) USING BTREE,
  ADD KEY `dp_supp_idx` (`supp`) USING BTREE,
  ADD KEY `dp_no_po_idx` (`no_po`) USING BTREE,
  ADD KEY `dp_tgl_po_idx` (`tgl_po`) USING BTREE,
  ADD KEY `dp_no_pi_idx` (`no_pi`) USING BTREE,
  ADD KEY `dp_dp_idx` (`dp`) USING BTREE,
  ADD KEY `dp_dp_value_idx` (`dp_value`) USING BTREE,
  ADD KEY `dp_total_idx` (`total`) USING BTREE,
  ADD KEY `dp_curr_idx` (`curr`) USING BTREE,
  ADD KEY `dp_keterangan_idx` (`keterangan`) USING BTREE,
  ADD KEY `dp_status_idx` (`status`) USING BTREE,
  ADD KEY `dp_create_date_idx` (`create_date`) USING BTREE,
  ADD KEY `dp_confirm_date_idx` (`confirm_date`) USING BTREE,
  ADD KEY `dp_cancel_date_idx` (`cancel_date`) USING BTREE,
  ADD KEY `dp_create_user_idx` (`create_user`) USING BTREE,
  ADD KEY `dp_confirm_user_idx` (`confirm_user`) USING BTREE,
  ADD KEY `dp_cancel_user_idx` (`cancel_user`) USING BTREE,
  ADD KEY `dp_balance_idx` (`balance`) USING BTREE,
  ADD KEY `dp_is_invoiced_idx` (`is_invoiced`) USING BTREE,
  ADD KEY `dp_id_po_idx` (`id_po`) USING BTREE;

--
-- Indexes for table `kartu_hutang`
--
ALTER TABLE `kartu_hutang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kontrabon`
--
ALTER TABLE `kontrabon`
  ADD PRIMARY KEY (`id`,`no_kbon`) USING BTREE,
  ADD KEY `kbon_no_kbon_idx` (`no_kbon`) USING BTREE,
  ADD KEY `kbon_tgl_kbon_idx` (`tgl_kbon`) USING BTREE,
  ADD KEY `kbon_id_jurnal_idx` (`id_jurnal`) USING BTREE,
  ADD KEY `kbon_nama_supp_idx` (`nama_supp`) USING BTREE,
  ADD KEY `kbon_no_faktur_idx` (`no_faktur`) USING BTREE,
  ADD KEY `kbon_no_bpb_idx` (`no_bpb`) USING BTREE,
  ADD KEY `kbon_no_po_idx` (`no_po`) USING BTREE,
  ADD KEY `kbon_tgl_bpb_idx` (`tgl_bpb`) USING BTREE,
  ADD KEY `kbon_supp_inv_idx` (`supp_inv`) USING BTREE,
  ADD KEY `kbon_tgl_inv_idx` (`tgl_inv`) USING BTREE,
  ADD KEY `kbon_subtotal_idx` (`subtotal`) USING BTREE,
  ADD KEY `kbon_tax_idx` (`tax`) USING BTREE,
  ADD KEY `kbon_total_idx` (`total`) USING BTREE,
  ADD KEY `kbon_curr_idx` (`curr`) USING BTREE,
  ADD KEY `kbon_ceklist_idx` (`ceklist`) USING BTREE,
  ADD KEY `kbon_tgl_tempo_idx` (`tgl_tempo`) USING BTREE,
  ADD KEY `kbon_pph_code_idx` (`pph_code`) USING BTREE,
  ADD KEY `kbon_pph_value_idx` (`pph_value`) USING BTREE,
  ADD KEY `kbon_post_date_idx` (`post_date`) USING BTREE,
  ADD KEY `kbon_update_date_idx` (`update_date`) USING BTREE,
  ADD KEY `kbon_status_idx` (`status`) USING BTREE,
  ADD KEY `kbon_create_user_idx` (`create_user`) USING BTREE,
  ADD KEY `kbon_confirm_user_idx` (`confirm_user`) USING BTREE,
  ADD KEY `kbon_confirm_date_idx` (`confirm_date`) USING BTREE,
  ADD KEY `kbon_create_date_idx` (`create_date`) USING BTREE,
  ADD KEY `kbon_start_date_idx` (`start_date`) USING BTREE,
  ADD KEY `kbon_end_date_idx` (`end_date`) USING BTREE,
  ADD KEY `kbon_cancel_date_idx` (`cancel_date`) USING BTREE,
  ADD KEY `kbon_cancel_user_idx` (`cancel_user`) USING BTREE,
  ADD KEY `kbon_idtax_idx` (`idtax`) USING BTREE;

--
-- Indexes for table `kontrabon_cbd`
--
ALTER TABLE `kontrabon_cbd`
  ADD PRIMARY KEY (`id`,`no_kbon`) USING BTREE,
  ADD KEY `kbon_no_kbon_idx` (`no_kbon`) USING BTREE,
  ADD KEY `kbon_tgl_kbon_idx` (`tgl_kbon`) USING BTREE,
  ADD KEY `kbon_id_jurnal_idx` (`id_jurnal`) USING BTREE,
  ADD KEY `kbon_nama_supp_idx` (`nama_supp`) USING BTREE,
  ADD KEY `kbon_no_faktur_idx` (`no_faktur`) USING BTREE,
  ADD KEY `kbon_no_po_idx` (`no_po`) USING BTREE,
  ADD KEY `kbon_tgl_bpb_idx` (`tgl_po`) USING BTREE,
  ADD KEY `kbon_supp_inv_idx` (`supp_inv`) USING BTREE,
  ADD KEY `kbon_tgl_inv_idx` (`tgl_inv`) USING BTREE,
  ADD KEY `kbon_subtotal_idx` (`subtotal`) USING BTREE,
  ADD KEY `kbon_tax_idx` (`tax`) USING BTREE,
  ADD KEY `kbon_total_idx` (`total`) USING BTREE,
  ADD KEY `kbon_curr_idx` (`curr`) USING BTREE,
  ADD KEY `kbon_ceklist_idx` (`ceklist`) USING BTREE,
  ADD KEY `kbon_tgl_tempo_idx` (`tgl_tempo`) USING BTREE,
  ADD KEY `kbon_pph_code_idx` (`pph_code`) USING BTREE,
  ADD KEY `kbon_pph_value_idx` (`pph_value`) USING BTREE,
  ADD KEY `kbon_post_date_idx` (`post_date`) USING BTREE,
  ADD KEY `kbon_update_date_idx` (`update_date`) USING BTREE,
  ADD KEY `kbon_status_idx` (`status`) USING BTREE,
  ADD KEY `kbon_create_user_idx` (`create_user`) USING BTREE,
  ADD KEY `kbon_confirm_user_idx` (`confirm_user`) USING BTREE,
  ADD KEY `kbon_confirm_date_idx` (`confirm_date`) USING BTREE,
  ADD KEY `kbon_create_date_idx` (`create_date`) USING BTREE,
  ADD KEY `kbon_start_date_idx` (`start_date`) USING BTREE,
  ADD KEY `kbon_end_date_idx` (`end_date`) USING BTREE,
  ADD KEY `kbon_cancel_date_idx` (`cancel_date`) USING BTREE,
  ADD KEY `kbon_cancel_user_idx` (`cancel_user`) USING BTREE,
  ADD KEY `kbon_idtax_idx` (`idtax`) USING BTREE,
  ADD KEY `kbon_no_cbd_idx` (`no_cbd`) USING BTREE;

--
-- Indexes for table `kontrabon_dp`
--
ALTER TABLE `kontrabon_dp`
  ADD PRIMARY KEY (`id`,`no_kbon`) USING BTREE,
  ADD KEY `kbon_no_kbon_idx` (`no_kbon`) USING BTREE,
  ADD KEY `kbon_tgl_kbon_idx` (`tgl_kbon`) USING BTREE,
  ADD KEY `kbon_id_jurnal_idx` (`id_jurnal`) USING BTREE,
  ADD KEY `kbon_nama_supp_idx` (`nama_supp`) USING BTREE,
  ADD KEY `kbon_no_faktur_idx` (`no_faktur`) USING BTREE,
  ADD KEY `kbon_no_po_idx` (`no_po`) USING BTREE,
  ADD KEY `kbon_tgl_bpb_idx` (`tgl_po`) USING BTREE,
  ADD KEY `kbon_supp_inv_idx` (`supp_inv`) USING BTREE,
  ADD KEY `kbon_tgl_inv_idx` (`tgl_inv`) USING BTREE,
  ADD KEY `kbon_subtotal_idx` (`subtotal`) USING BTREE,
  ADD KEY `kbon_tax_idx` (`dp_value`) USING BTREE,
  ADD KEY `kbon_total_idx` (`total`) USING BTREE,
  ADD KEY `kbon_curr_idx` (`curr`) USING BTREE,
  ADD KEY `kbon_ceklist_idx` (`ceklist`) USING BTREE,
  ADD KEY `kbon_tgl_tempo_idx` (`tgl_tempo`) USING BTREE,
  ADD KEY `kbon_pph_code_idx` (`pph_code`) USING BTREE,
  ADD KEY `kbon_pph_value_idx` (`pph_value`) USING BTREE,
  ADD KEY `kbon_post_date_idx` (`post_date`) USING BTREE,
  ADD KEY `kbon_update_date_idx` (`update_date`) USING BTREE,
  ADD KEY `kbon_status_idx` (`status`) USING BTREE,
  ADD KEY `kbon_create_user_idx` (`create_user`) USING BTREE,
  ADD KEY `kbon_confirm_user_idx` (`confirm_user`) USING BTREE,
  ADD KEY `kbon_confirm_date_idx` (`confirm_date`) USING BTREE,
  ADD KEY `kbon_create_date_idx` (`create_date`) USING BTREE,
  ADD KEY `kbon_start_date_idx` (`start_date`) USING BTREE,
  ADD KEY `kbon_end_date_idx` (`end_date`) USING BTREE,
  ADD KEY `kbon_cancel_date_idx` (`cancel_date`) USING BTREE,
  ADD KEY `kbon_cancel_user_idx` (`cancel_user`) USING BTREE,
  ADD KEY `kbon_idtax_idx` (`idtax`) USING BTREE,
  ADD KEY `kbon_no_dp_idx` (`no_dp`) USING BTREE;

--
-- Indexes for table `kontrabon_h`
--
ALTER TABLE `kontrabon_h`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `kbonh_no_kbon_idx` (`no_kbon`) USING BTREE,
  ADD KEY `kbonh_tgl_kbon_idx` (`tgl_kbon`) USING BTREE,
  ADD KEY `kbonh_nama_supp_idx` (`nama_supp`) USING BTREE,
  ADD KEY `kbonh_no_faktur_idx` (`no_faktur`) USING BTREE,
  ADD KEY `kbonh_supp_inv_idx` (`supp_inv`) USING BTREE,
  ADD KEY `kbonh_tgl_inv_idx` (`tgl_inv`) USING BTREE,
  ADD KEY `kbonh_tgl_tempo_idx` (`tgl_tempo`) USING BTREE,
  ADD KEY `kbonh_subtotal_idx` (`subtotal`) USING BTREE,
  ADD KEY `kbonh_tax_idx` (`tax`) USING BTREE,
  ADD KEY `kbonh_pph_idx` (`pph_idr`) USING BTREE,
  ADD KEY `kbonh_total_idx` (`total`) USING BTREE,
  ADD KEY `kbonh_status_idx` (`status`) USING BTREE,
  ADD KEY `kbonh_curr_idx` (`curr`) USING BTREE;

--
-- Indexes for table `kontrabon_h_cbd`
--
ALTER TABLE `kontrabon_h_cbd`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `kbonh_no_kbon_idx` (`no_kbon`) USING BTREE,
  ADD KEY `kbonh_tgl_kbon_idx` (`tgl_kbon`) USING BTREE,
  ADD KEY `kbonh_nama_supp_idx` (`nama_supp`) USING BTREE,
  ADD KEY `kbonh_no_faktur_idx` (`no_faktur`) USING BTREE,
  ADD KEY `kbonh_supp_inv_idx` (`supp_inv`) USING BTREE,
  ADD KEY `kbonh_tgl_inv_idx` (`tgl_inv`) USING BTREE,
  ADD KEY `kbonh_tgl_tempo_idx` (`tgl_tempo`) USING BTREE,
  ADD KEY `kbonh_subtotal_idx` (`subtotal`) USING BTREE,
  ADD KEY `kbonh_tax_idx` (`tax`) USING BTREE,
  ADD KEY `kbonh_pph_idx` (`pph`) USING BTREE,
  ADD KEY `kbonh_total_idx` (`total`) USING BTREE,
  ADD KEY `kbonh_status_idx` (`status`) USING BTREE,
  ADD KEY `kbonh_curr_idx` (`curr`) USING BTREE;

--
-- Indexes for table `kontrabon_h_dp`
--
ALTER TABLE `kontrabon_h_dp`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `kbonh_no_kbon_idx` (`no_kbon`) USING BTREE,
  ADD KEY `kbonh_tgl_kbon_idx` (`tgl_kbon`) USING BTREE,
  ADD KEY `kbonh_nama_supp_idx` (`nama_supp`) USING BTREE,
  ADD KEY `kbonh_no_faktur_idx` (`no_faktur`) USING BTREE,
  ADD KEY `kbonh_supp_inv_idx` (`supp_inv`) USING BTREE,
  ADD KEY `kbonh_tgl_inv_idx` (`tgl_inv`) USING BTREE,
  ADD KEY `kbonh_tgl_tempo_idx` (`tgl_tempo`) USING BTREE,
  ADD KEY `kbonh_subtotal_idx` (`subtotal`) USING BTREE,
  ADD KEY `kbonh_tax_idx` (`dp_value`) USING BTREE,
  ADD KEY `kbonh_pph_idx` (`pph`) USING BTREE,
  ADD KEY `kbonh_total_idx` (`total`) USING BTREE,
  ADD KEY `kbonh_status_idx` (`status`) USING BTREE,
  ADD KEY `kbonh_curr_idx` (`curr`) USING BTREE;

--
-- Indexes for table `list_payment`
--
ALTER TABLE `list_payment`
  ADD PRIMARY KEY (`id`,`no_payment`) USING BTREE,
  ADD KEY `payment_no_payment_idx` (`no_payment`) USING BTREE,
  ADD KEY `payment_tgl_payment_idx` (`tgl_payment`) USING BTREE,
  ADD KEY `payment_id_jurnal_idx` (`id_jurnal`) USING BTREE,
  ADD KEY `payment_nama_supp_idx` (`nama_supp`) USING BTREE,
  ADD KEY `payment_no_kbon_idx` (`no_kbon`) USING BTREE,
  ADD KEY `payment_tgl_kbon_idx` (`tgl_kbon`) USING BTREE,
  ADD KEY `payment_curr_idx` (`curr`) USING BTREE,
  ADD KEY `payment_top_idx` (`top`) USING BTREE,
  ADD KEY `payment_tgl_tempo_idx` (`tgl_tempo`) USING BTREE,
  ADD KEY `payment_memo_idx` (`memo`) USING BTREE,
  ADD KEY `payment_post_date_idx` (`post_date`) USING BTREE,
  ADD KEY `payment_update_date_idx` (`update_date`) USING BTREE,
  ADD KEY `payment_status_idx` (`status`) USING BTREE,
  ADD KEY `payment_create_user_idx` (`create_user`) USING BTREE,
  ADD KEY `payment_create_date_idx` (`create_date`) USING BTREE,
  ADD KEY `payment_confirm_user_idx` (`confirm_user`) USING BTREE,
  ADD KEY `payment_confirm_date_idx` (`confirm_date`) USING BTREE,
  ADD KEY `payment_cancel_user_idx` (`cancel_user`) USING BTREE,
  ADD KEY `payment_cancel_date_idx` (`cancel_date`) USING BTREE,
  ADD KEY `payment_start_date_idx` (`start_date`) USING BTREE,
  ADD KEY `payment_end_date_idx` (`end_date`) USING BTREE,
  ADD KEY `payment_total_kbon_idx` (`total_kbon`) USING BTREE,
  ADD KEY `payment_outstanding_idx` (`outstanding`) USING BTREE,
  ADD KEY `payment_amount_idx` (`amount`) USING BTREE;

--
-- Indexes for table `list_payment_cbd`
--
ALTER TABLE `list_payment_cbd`
  ADD PRIMARY KEY (`id`,`no_payment`) USING BTREE,
  ADD KEY `payment_no_payment_idx` (`no_payment`) USING BTREE,
  ADD KEY `payment_tgl_payment_idx` (`tgl_payment`) USING BTREE,
  ADD KEY `payment_id_jurnal_idx` (`id_jurnal`) USING BTREE,
  ADD KEY `payment_nama_supp_idx` (`nama_supp`) USING BTREE,
  ADD KEY `payment_no_kbon_idx` (`no_kbon`) USING BTREE,
  ADD KEY `payment_tgl_kbon_idx` (`tgl_kbon`) USING BTREE,
  ADD KEY `payment_curr_idx` (`curr`) USING BTREE,
  ADD KEY `payment_top_idx` (`top`) USING BTREE,
  ADD KEY `payment_tgl_tempo_idx` (`tgl_tempo`) USING BTREE,
  ADD KEY `payment_memo_idx` (`memo`) USING BTREE,
  ADD KEY `payment_post_date_idx` (`post_date`) USING BTREE,
  ADD KEY `payment_update_date_idx` (`update_date`) USING BTREE,
  ADD KEY `payment_status_idx` (`status`) USING BTREE,
  ADD KEY `payment_create_user_idx` (`create_user`) USING BTREE,
  ADD KEY `payment_create_date_idx` (`create_date`) USING BTREE,
  ADD KEY `payment_confirm_user_idx` (`confirm_user`) USING BTREE,
  ADD KEY `payment_confirm_date_idx` (`confirm_date`) USING BTREE,
  ADD KEY `payment_cancel_user_idx` (`cancel_user`) USING BTREE,
  ADD KEY `payment_cancel_date_idx` (`cancel_date`) USING BTREE,
  ADD KEY `payment_start_date_idx` (`start_date`) USING BTREE,
  ADD KEY `payment_end_date_idx` (`end_date`) USING BTREE,
  ADD KEY `payment_total_kbon_idx` (`total_kbon`) USING BTREE,
  ADD KEY `payment_outstanding_idx` (`outstanding`) USING BTREE,
  ADD KEY `payment_amount_idx` (`amount`) USING BTREE;

--
-- Indexes for table `list_payment_dp`
--
ALTER TABLE `list_payment_dp`
  ADD PRIMARY KEY (`id`,`no_payment`) USING BTREE,
  ADD KEY `payment_no_payment_idx` (`no_payment`) USING BTREE,
  ADD KEY `payment_tgl_payment_idx` (`tgl_payment`) USING BTREE,
  ADD KEY `payment_id_jurnal_idx` (`id_jurnal`) USING BTREE,
  ADD KEY `payment_nama_supp_idx` (`nama_supp`) USING BTREE,
  ADD KEY `payment_no_kbon_idx` (`no_kbon`) USING BTREE,
  ADD KEY `payment_tgl_kbon_idx` (`tgl_kbon`) USING BTREE,
  ADD KEY `payment_curr_idx` (`curr`) USING BTREE,
  ADD KEY `payment_top_idx` (`top`) USING BTREE,
  ADD KEY `payment_tgl_tempo_idx` (`tgl_tempo`) USING BTREE,
  ADD KEY `payment_memo_idx` (`memo`) USING BTREE,
  ADD KEY `payment_post_date_idx` (`post_date`) USING BTREE,
  ADD KEY `payment_update_date_idx` (`update_date`) USING BTREE,
  ADD KEY `payment_status_idx` (`status`) USING BTREE,
  ADD KEY `payment_create_user_idx` (`create_user`) USING BTREE,
  ADD KEY `payment_create_date_idx` (`create_date`) USING BTREE,
  ADD KEY `payment_confirm_user_idx` (`confirm_user`) USING BTREE,
  ADD KEY `payment_confirm_date_idx` (`confirm_date`) USING BTREE,
  ADD KEY `payment_cancel_user_idx` (`cancel_user`) USING BTREE,
  ADD KEY `payment_cancel_date_idx` (`cancel_date`) USING BTREE,
  ADD KEY `payment_start_date_idx` (`start_date`) USING BTREE,
  ADD KEY `payment_end_date_idx` (`end_date`) USING BTREE,
  ADD KEY `payment_total_kbon_idx` (`total_kbon`) USING BTREE,
  ADD KEY `payment_outstanding_idx` (`outstanding`) USING BTREE,
  ADD KEY `payment_amount_idx` (`amount`) USING BTREE;

--
-- Indexes for table `menurole`
--
ALTER TABLE `menurole`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_ftr`
--
ALTER TABLE `payment_ftr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_ftrcbd`
--
ALTER TABLE `payment_ftrcbd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_ftrdp`
--
ALTER TABLE `payment_ftrdp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_ftrcbd`
--
ALTER TABLE `pengajuan_ftrcbd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_ftrdp`
--
ALTER TABLE `pengajuan_ftrdp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_kb`
--
ALTER TABLE `pengajuan_kb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_kb_cbd`
--
ALTER TABLE `pengajuan_kb_cbd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_kb_dp`
--
ALTER TABLE `pengajuan_kb_dp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_payment`
--
ALTER TABLE `pengajuan_payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_paymentcbd`
--
ALTER TABLE `pengajuan_paymentcbd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengajuan_paymentdp`
--
ALTER TABLE `pengajuan_paymentdp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `potongan`
--
ALTER TABLE `potongan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `return_kb`
--
ALTER TABLE `return_kb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ttd`
--
ALTER TABLE `ttd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ttl_bppb`
--
ALTER TABLE `ttl_bppb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `useraccess`
--
ALTER TABLE `useraccess`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bpb_new`
--
ALTER TABLE `bpb_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `bppb_new`
--
ALTER TABLE `bppb_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `ftr_cbd`
--
ALTER TABLE `ftr_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ftr_dp`
--
ALTER TABLE `ftr_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kartu_hutang`
--
ALTER TABLE `kartu_hutang`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `kontrabon`
--
ALTER TABLE `kontrabon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `kontrabon_cbd`
--
ALTER TABLE `kontrabon_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kontrabon_dp`
--
ALTER TABLE `kontrabon_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kontrabon_h`
--
ALTER TABLE `kontrabon_h`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `kontrabon_h_cbd`
--
ALTER TABLE `kontrabon_h_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kontrabon_h_dp`
--
ALTER TABLE `kontrabon_h_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `list_payment`
--
ALTER TABLE `list_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `list_payment_cbd`
--
ALTER TABLE `list_payment_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `list_payment_dp`
--
ALTER TABLE `list_payment_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menurole`
--
ALTER TABLE `menurole`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payment_ftr`
--
ALTER TABLE `payment_ftr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payment_ftrcbd`
--
ALTER TABLE `payment_ftrcbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `payment_ftrdp`
--
ALTER TABLE `payment_ftrdp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengajuan_ftrcbd`
--
ALTER TABLE `pengajuan_ftrcbd`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengajuan_ftrdp`
--
ALTER TABLE `pengajuan_ftrdp`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_kb`
--
ALTER TABLE `pengajuan_kb`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_kb_cbd`
--
ALTER TABLE `pengajuan_kb_cbd`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_kb_dp`
--
ALTER TABLE `pengajuan_kb_dp`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_payment`
--
ALTER TABLE `pengajuan_payment`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_paymentcbd`
--
ALTER TABLE `pengajuan_paymentcbd`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_paymentdp`
--
ALTER TABLE `pengajuan_paymentdp`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `potongan`
--
ALTER TABLE `potongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `return_kb`
--
ALTER TABLE `return_kb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ttd`
--
ALTER TABLE `ttd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ttl_bppb`
--
ALTER TABLE `ttl_bppb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `useraccess`
--
ALTER TABLE `useraccess`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
