-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2022 at 11:41 AM
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
  `qty` decimal(16,2) DEFAULT NULL,
  `uom` varchar(255) DEFAULT NULL,
  `price` decimal(16,2) DEFAULT NULL,
  `tax` decimal(16,2) DEFAULT NULL,
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
(589, 'GK/IN/0520/01218', 'DWT/0120/018/02323', 'DWT/0120/018', '2020-05-20', '2020-04-30', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM SCARLET', 'SCARLET', '66\"', '45.80', 'KGM', '9.10', '0.00', 'USD', 'indro', 'yulius', 'indro', '2022-01-13 09:29:30', 'Invoiced', 'GMF-PCH', 0, 'Credit - Complete quantity', '2022-01-13 09:25:43', '2022-01-13 09:25:43', 'indro', 1, '2020-01-01', '2022-01-13'),
(590, 'GK/IN/0320/00689', 'DWT/0120/046/00741', 'DWT/0120/046', '2020-03-10', '2020-02-13', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM DARK BOTTLE', 'DARK BOTTLE', '66\"', '250.16', 'KGM', '9.10', '0.00', 'USD', 'indro', 'yulius', 'indro', '2022-01-13 09:59:29', 'Invoiced', 'GMF-PCH', 0, 'Credit - Complete quantity', '2022-01-13 09:59:25', '2022-01-13 09:59:25', 'indro', 1, '2020-01-01', '2022-01-13'),
(591, 'GACC/IN/1221/04411', 'PTX/1021/032/05453', 'PTX/1021/032', '2021-12-31', '2021-12-22', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING DUCT TAPE 2 INCH', '-', '2 INCH', '16.00', 'ROLL', '1.35', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-13 10:05:16', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-13 10:05:09', '2022-01-13 10:05:09', 'indro', 1, '2021-01-01', '2022-01-13'),
(592, 'GACC/IN/0121/00500', 'SGT/0121/021/00449', 'SGT/0121/021', '2021-01-22', '2021-01-28', 'Singa Global Textile 2,PT', 'ACCESORIES PACKING LOCK PIN 5 INC CLEAR', 'CLEAR', '5 INC', '116.00', 'PCS', '1.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-13 10:32:53', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-13 10:32:42', '2022-01-13 10:32:42', 'indro', 1, '2021-01-01', '2022-01-13'),
(593, 'GACC/IN/1221/04464', 'C/PTX/1121/04730', 'PTX/1121/035', '2021-12-23', '2021-11-20', 'PT.SAMJIN BROTHREAD INDONESIA', 'ACCESORIES SEWING SEWING THREAD SPUN 100% POLYESTER 40/2 LIGHT BLUE', 'LIGHT BLUE', '40/2', '452.00', 'CNS', '0.70', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-13 10:40:35', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-13 10:40:28', '2022-01-13 10:40:28', 'indro', 1, '2020-01-01', '2022-01-13'),
(594, 'GACC/IN/1221/04464', 'C/PTX/1121/04730', 'PTX/1021/033', '2021-12-23', '2021-11-20', 'PT.SAMJIN BROTHREAD INDONESIA', 'ACCESORIES SEWING SEWING THREAD SPUN 100% POLYESTER 40/2 LIGHT BLUE', 'LIGHT BLUE', '40/2', '16.00', 'CNS', '0.70', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-13 10:40:35', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-13 10:40:28', '2022-01-13 10:40:28', 'indro', 1, '2020-01-01', '2022-01-13'),
(595, 'GACC/IN/1221/04464', 'C/PTX/1121/04730', 'PTX/1121/034', '2021-12-23', '2021-11-20', 'PT.SAMJIN BROTHREAD INDONESIA', 'ACCESORIES SEWING SEWING THREAD SPUN 100% POLYESTER 40/2 LIGHT BLUE', 'LIGHT BLUE', '40/2', '632.00', 'CNS', '0.70', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-13 10:40:35', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-13 10:40:28', '2022-01-13 10:40:28', 'indro', 1, '2020-01-01', '2022-01-13'),
(596, 'GACC/IN/1221/04410', 'C/PTX/1221/05463', 'PTX/1221/036', '2021-12-31', '2021-12-23', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING DUCT TAPE', '-', '-', '10.00', 'PCS', '1.35', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-14 06:15:18', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-14 06:15:12', '2022-01-14 06:15:12', 'indro', 1, '2021-01-01', '2022-01-14'),
(597, 'GACC/IN/1221/04410', 'C/PTX/1221/05463', 'PTX/1221/037', '2021-12-31', '2021-12-23', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING DUCT TAPE', '-', '-', '8.00', 'PCS', '1.35', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-14 06:15:18', 'Invoiced', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-14 06:15:12', '2022-01-14 06:15:12', 'indro', 1, '2021-01-01', '2022-01-14'),
(598, 'GACC/IN/1221/04409', 'PTX/1221/036/05382', 'PTX/1221/036', '2021-12-31', '2021-12-21', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING STICKER', '-', '-', '318.00', 'PCS', '0.02', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-14 09:54:08', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-14 09:54:02', '2022-01-14 09:54:02', 'indro', 1, '2021-01-01', '2022-01-14'),
(599, 'GACC/IN/1221/04409', 'PTX/1221/036/05382', 'PTX/1221/036', '2021-12-31', '2021-12-21', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING STICKER CARTON', '-', '-', '282.00', 'PCS', '0.02', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-14 09:54:08', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-14 09:54:02', '2022-01-14 09:54:02', 'indro', 1, '2021-01-01', '2022-01-14'),
(600, 'GACC/IN/1221/04409', 'PTX/1221/036/05382', 'PTX/1221/036', '2021-12-31', '2021-12-21', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING STICKER', '-', '-', '280.00', 'PCS', '0.01', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-14 09:54:08', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-14 09:54:02', '2022-01-14 09:54:02', 'indro', 1, '2021-01-01', '2022-01-14'),
(601, 'GACC/IN/1221/04409', 'PTX/1221/036/05382', 'PTX/1221/036', '2021-12-31', '2021-12-21', 'ALMINDO PRATAMA CV', 'ACCESORIES PACKING STICKER CARTON SHIPPING STICKER', '-', '-', '268.00', 'PCS', '0.01', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-14 09:54:08', 'Waiting', 'GMF-PCH', 30, 'Credit - Complete quantity', '2022-01-14 09:54:02', '2022-01-14 09:54:02', 'indro', 1, '2021-01-01', '2022-01-14'),
(602, 'GK/IN/0120/00393', 'DWT/1219/163/00423', 'DWT/1219/163', '2020-01-31', '2020-01-28', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT FRENCH TERRY 100% POLYESTER 58\" 220GSM BLACK', 'BLACK', '58\"', '11.82', 'KGM', '7.00', '0.00', 'USD', 'indro', 'yulius', 'indro', '2022-01-17 01:04:36', 'Invoiced', 'GMF-PCH', 0, 'CBD', '2022-01-17 01:04:30', '2022-01-17 01:04:30', 'indro', 1, '2020-01-01', '2022-01-17'),
(603, 'GK/IN/0420/00949', 'C/DWT/0320/01214', 'DWT/0120/107', '2020-04-07', '2020-03-05', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT POLY TOWEL FLEECE 1 SIDE ANTI PILLING 100% POLYESTER 60\" 100GSM SQK NAVY', 'SQK NAVY', '60\"', '18.00', 'KGM', '8.80', '0.00', 'USD', 'indro', 'yulius', 'indro', '2022-01-17 03:40:01', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-01-17 03:38:57', '2022-01-17 03:38:57', 'indro', 1, '2020-01-01', '2022-01-17'),
(604, 'GK/IN/0420/00949', 'C/DWT/0320/01214', 'DWT/0120/107', '2020-04-07', '2020-03-05', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT POLY TOWEL FLEECE 1 SIDE ANTI PILLING 100% POLYESTER 60\" 100GSM BLACK', 'BLACK', '60\"', '14.00', 'KGM', '8.80', '0.00', 'USD', 'indro', 'yulius', 'indro', '2022-01-17 03:40:01', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-01-17 03:38:57', '2022-01-17 03:38:57', 'indro', 1, '2020-01-01', '2022-01-17'),
(605, 'GEN/IN/1219/00276', 'PO/0220/00147', '', '2020-02-27', '2020-02-27', 'MEKAR JAYA', 'KERTAS STENSIL SAKURA', 'RANDOM', 'FOLIO', '2.00', 'RIM', '31500.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-24 02:10:16', 'Invoiced', 'GMF-PCH', 30, 'DP - Partial shipment allowed', '2022-01-24 02:09:45', '2022-01-24 02:09:45', 'indro', 1, '2020-01-01', '2022-01-24'),
(606, 'GEN/IN/1219/00276', 'PO/0220/00147', '', '2020-02-27', '2020-02-27', 'MEKAR JAYA', 'KARTON DUPLEX', '-', '-', '400.00', 'RIM', '4000.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-24 02:10:16', 'Invoiced', 'GMF-PCH', 30, 'DP - Partial shipment allowed', '2022-01-24 02:09:45', '2022-01-24 02:09:45', 'indro', 1, '2020-01-01', '2022-01-24'),
(607, 'GEN/IN/1219/00276', 'PO/0220/00147', '', '2020-02-27', '2020-02-27', 'MEKAR JAYA', 'ISI CUTTER KENKO L-150', '-', '-', '12.00', 'PACK', '6000.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-24 02:10:16', 'Invoiced', 'GMF-PCH', 30, 'DP - Partial shipment allowed', '2022-01-24 02:09:45', '2022-01-24 02:09:45', 'indro', 1, '2020-01-01', '2022-01-24'),
(608, 'GACC/IN/0520/03097', 'AVI/0420/012/02208', 'AVI/0420/012', '2020-05-18', '2020-04-22', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL KOREA/CHINA', '-', '-', '236.00', 'PCS', '250.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-24 10:33:35', 'Waiting', 'GMF-PCH', 0, 'Credit - Complete quantity', '2022-01-24 10:32:50', '2022-01-24 10:32:50', 'indro', 1, '2019-01-01', '2022-01-24'),
(609, 'GACC/IN/0520/03097', 'AVI/0420/012/02208', 'AVI/0420/012', '2020-05-18', '2020-04-22', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL JAPAN/ENGLISH', '-', '-', '236.00', 'PCS', '250.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-24 10:33:35', 'Waiting', 'GMF-PCH', 0, 'Credit - Complete quantity', '2022-01-24 10:32:50', '2022-01-24 10:32:50', 'indro', 1, '2019-01-01', '2022-01-24'),
(610, 'GACC/IN/0120/00292', 'C/AVI/01307', 'AVI/1019/062', '2020-01-21', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL KOREA/CHINA', '-', '-', '1400.00', 'PCS', '250.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-24 10:33:35', 'Invoiced', 'GMF-PCH', 0, 'Credit - Complete quantity', '2022-01-24 10:33:24', '2022-01-24 10:33:24', 'indro', 1, '2019-01-01', '2022-01-24'),
(611, 'GACC/IN/0120/00292', 'C/AVI/01307', 'AVI/1019/062', '2020-01-21', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL JAPAN/ENGLISH', '-', '-', '1400.00', 'PCS', '250.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-24 10:33:35', 'Invoiced', 'GMF-PCH', 0, 'Credit - Complete quantity', '2022-01-24 10:33:24', '2022-01-24 10:33:24', 'indro', 1, '2019-01-01', '2022-01-24'),
(612, 'GACC/IN/0120/00311', 'C/AVI/01305', 'AVI/1019/068', '2020-01-22', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES PACKING HANGTAG', '-', '-', '9422.00', 'PCS', '480.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-25 01:15:49', 'Waiting', 'GMF-PCH', 0, 'Credit - Complete PO 30 KONTRABON', '2022-01-25 01:15:44', '2022-01-25 01:15:44', 'indro', 1, '2020-01-01', '2022-01-25'),
(613, 'GACC/IN/0120/00291', 'C/AVI/01307', 'AVI/1019/062', '2020-01-21', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL KOREA/CHINA', '-', '-', '12780.00', 'PCS', '250.00', '10.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-25 01:15:49', 'Waiting', 'GMF-PCH', 0, 'Credit - Complete quantity', '2022-01-25 01:15:44', '2022-01-25 01:15:44', 'indro', 1, '2020-01-01', '2022-01-25'),
(614, 'GACC/IN/0120/00291', 'C/AVI/01307', 'AVI/1019/062', '2020-01-21', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES SEWING CARE LABEL JAPAN/ENGLISH', '-', '-', '13000.00', 'PCS', '250.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-25 01:15:49', 'Waiting', 'GMF-PCH', 0, 'Credit - Complete quantity', '2022-01-25 01:15:44', '2022-01-25 01:15:44', 'indro', 1, '2020-01-01', '2022-01-25'),
(615, 'GK/IN/0420/01057', 'DWT/0120/140/01972', 'DWT/0120/147', '2020-04-24', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM SCARLET', 'SCARLET', '66\"', '110.00', 'KGM', '9.10', '0.00', 'USD', 'indro', 'yulius', 'indro', '2022-01-25 06:37:47', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-01-25 06:35:46', '2022-01-25 06:35:46', 'indro', 1, '2020-01-01', '2022-01-25'),
(616, 'GK/IN/0520/01145', 'DWT/0120/140/01972', 'DWT/0120/141', '2020-05-09', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM ROYAL', 'ROYAL', '66\"', '112.00', 'KGM', '9.10', '0.00', 'USD', 'indro', 'yulius', 'indro', '2022-01-25 06:37:47', 'Invoiced', 'GMF-PCH', 0, 'CBD', '2022-01-25 06:35:46', '2022-01-25 06:35:46', 'indro', 1, '2020-01-01', '2022-01-25'),
(617, 'GK/IN/0520/01198', 'DWT/0120/140/01972', 'DWT/0220/171', '2020-05-14', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '7.72', 'KGM', '9.10', '0.00', 'USD', 'indro', 'yulius', 'indro', '2022-01-25 06:37:47', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-01-25 06:35:46', '2022-01-25 06:35:46', 'indro', 1, '2020-01-01', '2022-01-25'),
(618, 'GACC/IN/0120/00378', 'C/AVI/01307', 'AVI/1019/062', '2020-01-21', '2019-11-06', 'Ricky Kobayashi', 'ACCESORIES SEWING MAIN LABEL', '-', '-', '22454.00', 'PCS', '800.00', '0.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-27 03:06:02', 'Waiting', 'GMF-PCH', 0, 'Credit - Complete quantity', '2022-01-27 03:05:57', '2022-01-27 03:05:57', 'indro', 1, '2020-01-01', '2022-01-27');

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
  `qty` decimal(16,2) DEFAULT NULL,
  `uom` varchar(255) DEFAULT NULL,
  `price` decimal(16,2) DEFAULT NULL,
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
(50, 'SJ-A00066-R', 'A03989', '2020-05-19', 'GACC/IN/0520/03097', 'Ricky Kobayashi', 'SI/APR/2022/01/00009', 'ACCESORIES SEWING CARE LABEL KOREA/CHINA', '-', '-', '236.00', 'PCS', '250.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-24 10:32:01', 'Invoiced', 'GMF-PCH', '2022-01-24 10:31:07', '2022-01-24 10:31:07', 'indro', 1),
(51, 'SJ-A00066-R', 'A03989', '2020-05-19', 'GACC/IN/0520/03097', 'Ricky Kobayashi', 'SI/APR/2022/01/00009', 'ACCESORIES SEWING CARE LABEL JAPAN/ENGLISH', '-', '-', '236.00', 'PCS', '250.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-24 10:32:01', 'Invoiced', 'GMF-PCH', '2022-01-24 10:31:07', '2022-01-24 10:31:07', 'indro', 1),
(52, 'SJ-A00065-R', 'A03988', '2020-05-19', 'GACC/IN/0520/03096', 'Ricky Kobayashi', NULL, 'ACCESORIES SEWING MAIN LABEL', '-', '-', '236.00', 'PCS', '800.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-25 01:16:52', 'Waiting', 'GMF-PCH', '2022-01-25 01:16:48', '2022-01-25 01:16:48', 'indro', 1),
(53, 'GACC/RO/0122/00049', 'A09205', '2022-01-05', 'GACC/IN/0122/00003', 'ALMINDO PRATAMA CV', NULL, 'ACCESORIES SEWING LABEL TAFFETA WHITE', 'WHITE', '-', '6436.00', 'PCS', '0.00', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-27 03:04:36', 'Waiting', 'GMF-PCH', '2022-01-26 06:46:53', '2022-01-26 06:46:53', 'indro', 1),
(54, 'GACC/RO/0122/00050', 'A09205', '2022-01-05', 'GACC/IN/0122/00003', 'ALMINDO PRATAMA CV', NULL, 'ACCESORIES PACKING STICKER', '-', '-', '115.00', 'PCS', '0.01', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-27 03:04:36', 'Waiting', 'GMF-PCH', '2022-01-26 06:46:53', '2022-01-26 06:46:53', 'indro', 1),
(55, 'GACC/RO/0122/00004', 'A09076', '2022-01-03', 'GACC/IN/1221/04297', 'ALMINDO PRATAMA CV', NULL, 'ACCESORIES PACKING STICKER', '-', '-', '100.00', 'PCS', '0.01', 'USD', 'indro', 'ibrahim', 'indro', '2022-01-27 03:04:35', 'Waiting', 'GMF-PCH', '2022-01-26 06:48:44', '2022-01-26 06:48:44', 'indro', 1),
(56, 'SJ-A00064-R', 'A03987', '2020-05-19', 'GACC/IN/0520/03095', 'Ricky Kobayashi', NULL, 'ACCESORIES SEWING MAIN LABEL', '-', '-', '236.00', 'PCS', '800.00', 'IDR', 'indro', 'yulius', 'indro', '2022-01-27 03:04:35', 'Waiting', 'GMF-PCH', '2022-01-27 03:04:27', '2022-01-27 03:04:27', 'indro', 1);

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
  `cancel_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `ftr_cbd`
--

INSERT INTO `ftr_cbd` (`id`, `no_ftr_cbd`, `tgl_ftr_cbd`, `supp`, `id_po`, `no_po`, `tgl_po`, `no_pi`, `subtotal`, `tax`, `total`, `curr`, `keterangan`, `status`, `is_invoiced`, `create_date`, `confirm_date`, `cancel_date`, `create_user`, `confirm_user`, `cancel_user`) VALUES
(60, 'FTR/C/NAG/0122/00001', '2022-01-17', 'INDO TAICHEN TEXTILE INDUSTRY,PT', NULL, 'DWT/1219/163/00423', '2020-01-28', '-', '77.00', '0.00', '77.00', 'USD', '', 'Approved', 'Invoiced', '2022-01-17 08:03:48', '2022-01-17 08:03:58', NULL, 'indro', '', NULL),
(61, 'FTR/C/NAG/0122/00002', '2022-01-17', 'INDO TAICHEN TEXTILE INDUSTRY,PT', NULL, 'C/DWT/0320/01214', '2020-03-05', '-', '281.60', '0.00', '281.60', 'USD', '', 'Approved', 'Invoiced', '2022-01-17 10:14:05', '2022-01-17 10:14:07', NULL, 'indro', '', NULL),
(62, 'FTR/C/NAG/0122/00003', '2022-01-25', 'INDO TAICHEN TEXTILE INDUSTRY,PT', NULL, 'DWT/0120/140/01972', '2020-03-31', '-', '4959.50', '0.00', '4959.50', 'USD', '-', 'Approved', 'Invoiced', '2022-01-25 13:36:18', '2022-01-25 13:38:11', NULL, 'indro', '', NULL);

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
  `cancel_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `ftr_dp`
--

INSERT INTO `ftr_dp` (`id`, `no_ftr_dp`, `tgl_ftr_dp`, `supp`, `id_po`, `no_po`, `tgl_po`, `no_pi`, `total`, `dp`, `dp_value`, `balance`, `curr`, `keterangan`, `status`, `is_invoiced`, `create_date`, `confirm_date`, `cancel_date`, `create_user`, `confirm_user`, `cancel_user`) VALUES
(80, 'FTR/D/NAG/0122/00001', '2022-01-24', 'MEKAR JAYA', NULL, 'PO/0220/00147', '2020-02-27', '-', '2137750.00', '50', '1068875.00', '1068875.00', 'IDR', '', 'Approved', 'Invoiced', '2022-01-24 09:10:08', '2022-01-24 09:10:22', NULL, 'indro', '', NULL);

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
(180, 'GK/IN/0520/01218', '2020-05-20', 'DWT/0120/018/02323', '2020-04-30', 'SI/APR/2022/01/00001', '2022-01-13', 'coba2', '2022-01-13', 'coba123', '2020-05-20', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-13', '41.68', '-', NULL, 'USD', 'Purchase', '14323.00', '2022-01-11', '416.78', '0.00', '5969539.94', '0.00', '2'),
(183, 'GK/IN/0320/00689', '2020-03-10', 'DWT/0120/046/00741', '2020-02-13', 'SI/APR/2022/01/00003', '2022-01-13', '-', '2022-01-13', '-', '2020-03-10', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-13', '227.65', '-', NULL, 'USD', 'Purchase', '14323.00', '2022-01-11', '2276.46', '0.00', '32605736.58', '0.00', '2'),
(184, 'GACC/IN/1221/04411', '2021-12-31', 'PTX/1021/032/05453', '2021-12-22', 'SI/APR/2022/01/00002', '2022-01-13', '-', '2022-01-13', '-', '2022-01-30', 'ALMINDO PRATAMA CV', '2022-01-13', '2.16', '-', NULL, 'USD', 'Purchase', '14323.00', '2022-01-11', '21.60', '0.00', '309376.80', '0.00', '2'),
(185, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'ALMINDO PRATAMA CV', '2022-01-13', NULL, 'LP/NAG/0122/00002', '2022-01-13', 'USD', 'Payment', '14000.00', NULL, '0.00', '19.44', '0.00', '272160.00', '3'),
(186, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'ALMINDO PRATAMA CV', '2022-01-13', NULL, 'LP/NAG/0122/00002', '2022-01-13', 'USD', 'Selisih Kurs', '14000.00', NULL, '0.00', '0.00', '0.00', '6279.12', '3'),
(187, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-13', NULL, 'LP/NAG/0122/00001', '2022-01-13', 'USD', 'Payment', '14500.00', NULL, '0.00', '375.10', '0.00', '5438950.00', '3'),
(188, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-13', NULL, 'LP/NAG/0122/00001', '2022-01-13', 'USD', 'Selisih Kurs', '14500.00', NULL, '0.00', '0.00', '66392.70', '0.00', '3'),
(189, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-13', NULL, 'LP/NAG/0122/00003', '2022-01-13', 'USD', 'Payment', '14500.00', NULL, '0.00', '1000.00', '0.00', '14500000.00', '3'),
(190, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-13', NULL, 'LP/NAG/0122/00003', '2022-01-13', 'USD', 'Selisih Kurs', '14500.00', NULL, '0.00', '0.00', '177000.00', '0.00', '3'),
(191, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-13', NULL, 'LP/NAG/0122/00004', '2022-01-13', 'USD', 'Payment', '14000.00', NULL, '0.00', '1033.81', '0.00', '14473340.00', '3'),
(192, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-13', NULL, 'LP/NAG/0122/00004', '2022-01-13', 'USD', 'Selisih Kurs', '14000.00', NULL, '0.00', '0.00', '0.00', '333920.63', '3'),
(193, 'GACC/IN/0121/00500', '2021-01-22', 'SGT/0121/021/00449', '2021-01-28', 'SI/APR/2022/01/00004', '2022-01-13', 'coba2', '2022-01-13', 'coba123', '2021-02-21', 'Singa Global Textile 2,PT', '2022-01-13', '11.60', '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '116.00', '0.00', '2'),
(194, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'Singa Global Textile 2,PT', '2022-01-13', NULL, 'LP/NAG/0122/00005', '2022-01-13', 'IDR', 'Payment', NULL, NULL, '0.00', '0.00', '0.00', '104.40', '3'),
(195, 'GACC/IN/1221/04464', '2021-12-23', 'C/PTX/1121/04730', '2021-11-20', 'SI/APR/2022/01/00005', '2022-01-13', '-', '2022-01-13', '-', '2022-01-22', 'PT.SAMJIN BROTHREAD INDONESIA', '2022-01-13', '77.00', '-', NULL, 'USD', 'Purchase', '14323.00', '2022-01-11', '770.00', '0.00', '11028710.00', '0.00', '2'),
(196, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'PT.SAMJIN BROTHREAD INDONESIA', '2022-01-13', NULL, 'LP/NAG/0122/00006', '2022-01-13', 'USD', 'Payment', '14323.00', NULL, '0.00', '693.00', '0.00', '9925839.00', '3'),
(197, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'PT.SAMJIN BROTHREAD INDONESIA', '2022-01-13', NULL, 'LP/NAG/0122/00006', '2022-01-13', 'USD', 'Selisih Kurs', '14323.00', NULL, '0.00', '0.00', '0.00', '0.00', '3'),
(198, 'GACC/IN/1221/04410', '2021-12-31', 'C/PTX/1221/05463', '2021-12-23', 'SI/APR/2022/01/00006', '2022-01-14', '-', '2022-01-14', '-', '2022-01-30', 'ALMINDO PRATAMA CV', '2022-01-13', '0.61', '-', NULL, 'USD', 'Purchase', '14311.00', '2022-01-14', '24.30', '0.00', '347757.30', '0.00', '2'),
(199, 'GACC/IN/1221/04409', '2021-12-31', 'PTX/1221/036/05382', '2021-12-21', '-', NULL, '-', NULL, '-', NULL, 'ALMINDO PRATAMA CV', '2022-01-14', NULL, '-', NULL, 'USD', 'Purchase', '14311.00', '2022-01-14', '17.48', '0.00', '250156.28', '0.00', '2'),
(200, 'GK/IN/0120/00393', '2020-01-31', 'DWT/1219/163/00423', '2020-01-28', 'SI/APR/2022/01/00007', '2022-01-17', 'coba2', '2022-01-17', 'coba123', '2020-01-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-17', '2.07', '-', NULL, 'USD', 'Purchase', '14311.00', '2022-01-14', '82.74', '0.00', '1184092.14', '0.00', '2'),
(202, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-17', NULL, 'LP/NAG/0122/00007', '2022-01-17', 'USD', 'Payment', '14100.00', NULL, '0.00', '3.67', '0.00', '51747.00', '3'),
(203, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-17', NULL, 'LP/NAG/0122/00007', '2022-01-17', 'USD', 'Selisih Kurs', '14100.00', NULL, '0.00', '0.00', '0.00', '774.37', '3'),
(204, '-', NULL, 'C/DWT/0320/01214', '2020-03-05', 'SI/CBD/2022/01/00002', '2022-01-17', 'coba2', '2022-01-17', 'coba123', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-17', NULL, 'LP/CBD/NAG/0122/00002', '2022-01-17', 'USD', 'Payment CBD', '14000.00', NULL, '0.00', '281.60', '0.00', '3942400.00', '1'),
(205, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-17', NULL, 'LP/CBD/NAG/0122/00002', '2022-01-17', 'USD', 'Selisih Kurs', '14000.00', NULL, '0.00', '0.00', '0.00', '87577.60', '1'),
(206, 'GK/IN/0420/00949', '2020-04-07', 'C/DWT/0320/01214', '2020-03-05', '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-17', NULL, '-', NULL, 'USD', 'Purchase', '14311.00', '2022-01-14', '281.60', '0.00', '4029977.60', '0.00', '2'),
(207, '-', NULL, 'DWT/1219/163/00423', '2020-01-28', 'SI/CBD/2022/01/00001', '2022-01-17', 'coba2', '2022-01-17', 'coba123', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-17', NULL, 'LP/CBD/NAG/0122/00001', '2022-01-17', 'USD', 'Payment CBD', '14000.00', NULL, '0.00', '77.00', '0.00', '1078000.00', '1'),
(208, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-17', NULL, 'LP/CBD/NAG/0122/00001', '2022-01-17', 'USD', 'Selisih Kurs', '14000.00', NULL, '0.00', '0.00', '0.00', '23947.00', '1'),
(209, 'GEN/IN/1219/00276', '2020-02-27', 'PO/0220/00147', '2020-02-27', 'SI/APR/2022/01/00008', '2022-01-24', '-', '2022-01-24', '-', '2020-03-28', 'MEKAR JAYA', '2022-01-24', '34700.00', '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '1735000.00', '0.00', '2'),
(210, '-', NULL, 'PO/0220/00147', '2020-02-27', 'SI/DP/2022/01/00001', '2022-01-24', '-', '2022-01-24', '-', NULL, 'MEKAR JAYA', '2022-01-24', NULL, 'LP/DP/NAG/0122/00001', '2022-01-24', 'IDR', 'Payment DP', NULL, NULL, '0.00', '0.00', '0.00', '1068875.00', '1'),
(211, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'MEKAR JAYA', '2022-01-24', NULL, 'LP/NAG/0122/00008', '2022-01-24', 'IDR', 'Payment', NULL, NULL, '0.00', '0.00', '0.00', '631425.00', '3'),
(212, 'GACC/IN/0520/03097', '2020-05-18', 'AVI/0420/012/02208', '2020-04-22', '-', NULL, '-', NULL, '-', NULL, 'Ricky Kobayashi', '2022-01-24', NULL, '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '118000.00', '0.00', '2'),
(213, 'GACC/IN/0120/00292', '2020-01-21', 'C/AVI/01307', '2019-11-06', 'SI/APR/2022/01/00009', '2022-01-25', 'asal', '2022-01-25', 'asal', '2020-05-18', 'Ricky Kobayashi', '2022-01-25', '70000.00', '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '700000.00', '0.00', '2'),
(214, 'GACC/IN/0120/00311', '2020-01-22', 'C/AVI/01305', '2019-11-06', '-', NULL, '-', NULL, '-', NULL, 'Ricky Kobayashi', '2022-01-25', NULL, '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '4522560.00', '0.00', '2'),
(215, 'GACC/IN/0120/00291', '2020-01-21', 'C/AVI/01307', '2019-11-06', '-', NULL, '-', NULL, '-', NULL, 'Ricky Kobayashi', '2022-01-25', NULL, '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '6445000.00', '0.00', '2'),
(216, 'GK/IN/0420/01057', '2020-04-24', 'DWT/0120/140/01972', '2020-03-31', '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-25', NULL, '-', NULL, 'USD', 'Purchase', '14327.00', '2022-01-25', '1001.00', '0.00', '14341327.00', '0.00', '2'),
(217, 'GK/IN/0520/01198', '2020-05-14', 'DWT/0120/140/01972', '2020-03-31', '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-25', NULL, '-', NULL, 'USD', 'Purchase', '14327.00', '2022-01-25', '70.25', '0.00', '1006500.40', '0.00', '2'),
(218, 'GK/IN/0520/01145', '2020-05-09', 'DWT/0120/140/01972', '2020-03-31', 'SI/APR/2022/01/00010', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-25', NULL, '-', NULL, 'USD', 'Purchase', '14327.00', '2022-01-25', '1019.20', '0.00', '14602078.40', '0.00', '2'),
(219, '-', NULL, 'DWT/0120/140/01972', '2020-03-31', 'SI/CBD/2022/01/00003', '2022-01-25', '-', '2022-01-25', '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-25', NULL, 'LP/CBD/NAG/0122/00003', '2022-01-25', 'USD', 'Payment CBD', '14200.00', NULL, '0.00', '4959.50', '0.00', '70424900.00', '1'),
(220, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-01-25', NULL, 'LP/CBD/NAG/0122/00003', '2022-01-25', 'USD', 'Selisih Kurs', '14200.00', NULL, '0.00', '0.00', '0.00', '629856.50', '1'),
(221, 'GACC/IN/0120/00378', '2020-01-21', 'C/AVI/01307', '2019-11-06', '-', NULL, '-', NULL, '-', NULL, 'Ricky Kobayashi', '2022-01-27', NULL, '-', NULL, 'IDR', 'Purchase', NULL, NULL, '0.00', '0.00', '17963200.00', '0.00', '2');

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
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `kontrabon`
--

INSERT INTO `kontrabon` (`id`, `no_kbon`, `tgl_kbon`, `id_jurnal`, `nama_supp`, `no_faktur`, `no_bpb`, `no_po`, `tgl_bpb`, `tgl_po`, `supp_inv`, `tgl_inv`, `subtotal`, `tax`, `total`, `balance`, `dp_value`, `curr`, `ceklist`, `tgl_tempo`, `idtax`, `pph_code`, `pph_value`, `post_date`, `update_date`, `status`, `status_int`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `start_date`, `end_date`) VALUES
(474, 'SI/APR/2022/01/00001', '2022-01-13', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'coba123', 'GK/IN/0520/01218', 'DWT/0120/018/02323', '2020-05-20', '2020-04-30', 'coba2', '2022-01-13', '416.78', '0.00', '375.10', NULL, '0.00', 'USD', 1, '2020-05-20', 3, '10.00', '41.68', '2022-01-13 09:30:18', '2022-01-13 09:30:18', 'Approved', 4, 'indro', 'indro', '2022-01-13', '2022-01-13 09:30:18', NULL, NULL, '2020-01-01', '2022-01-13'),
(475, 'SI/APR/2022/01/00002', '2022-01-13', 0, 'ALMINDO PRATAMA CV', '-', 'GACC/IN/1221/04411', 'PTX/1021/032/05453', '2021-12-31', '2021-12-22', '-', '2022-01-13', '21.60', '0.00', '19.44', NULL, '0.00', 'USD', 1, '2022-01-30', 3, '10.00', '2.16', '2022-01-13 10:06:28', '2022-01-13 10:06:28', 'Approved', 4, 'indro', 'indro', '2022-01-13', '2022-01-13 10:06:28', NULL, NULL, '2020-01-01', '2022-01-13'),
(476, 'SI/APR/2022/01/00003', '2022-01-13', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'GK/IN/0320/00689', 'DWT/0120/046/00741', '2020-03-10', '2020-02-13', '-', '2022-01-13', '2276.46', '0.00', '2048.81', NULL, '0.00', 'USD', 1, '2020-03-10', 3, '10.00', '227.65', '2022-01-13 10:22:38', '2022-01-13 10:22:38', 'Approved', 4, 'indro', 'indro', '2022-01-13', '2022-01-13 10:22:38', NULL, NULL, '2020-01-01', '2022-01-13'),
(477, 'SI/APR/2022/01/00004', '2022-01-13', 0, 'Singa Global Textile 2,PT', 'coba123', 'GACC/IN/0121/00500', 'SGT/0121/021/00449', '2021-01-22', '2021-01-28', 'coba2', '2022-01-13', '116.00', '0.00', '104.40', NULL, '0.00', 'IDR', 1, '2021-02-21', 3, '10.00', '11.60', '2022-01-13 10:33:48', '2022-01-13 10:33:48', 'Approved', 4, 'indro', 'indro', '2022-01-13', '2022-01-13 10:33:48', NULL, NULL, '2020-01-01', '2022-01-13'),
(478, 'SI/APR/2022/01/00005', '2022-01-13', 0, 'PT.SAMJIN BROTHREAD INDONESIA', '-', 'GACC/IN/1221/04464', 'C/PTX/1121/04730', '2021-12-23', '2021-11-20', '-', '2022-01-13', '770.00', '0.00', '693.00', NULL, '0.00', 'USD', 1, '2022-01-22', 3, '10.00', '77.00', '2022-01-13 10:57:36', '2022-01-13 10:57:36', 'Approved', 4, 'indro', 'indro', '2022-01-13', '2022-01-13 10:57:36', NULL, NULL, '2020-01-01', '2022-01-13'),
(479, 'SI/APR/2022/01/00006', '2022-01-14', 0, 'ALMINDO PRATAMA CV', '-', 'GACC/IN/1221/04410', 'C/PTX/1221/05463', '2021-12-31', '2021-12-23', '-', '2022-01-14', '24.30', '0.00', '23.69', NULL, '0.00', 'USD', 1, '2022-01-30', 4, '2.50', '0.61', '2022-01-14 06:16:22', '2022-01-14 06:16:22', 'Approved', 4, 'indro', 'indro', '2022-01-14', '2022-01-14 06:16:22', NULL, NULL, '2020-01-01', '2022-01-14'),
(480, 'SI/APR/2022/01/00007', '2022-01-17', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'coba123', 'GK/IN/0120/00393', 'DWT/1219/163/00423', '2020-01-31', '2020-01-28', 'coba2', '2022-01-17', '82.74', '0.00', '80.67', NULL, '77.00', 'USD', 1, '2020-01-31', 4, '2.50', '2.07', '2022-01-17 03:09:35', '2022-01-17 03:09:35', 'Approved', 4, 'indro', 'indro', '2022-01-17', '2022-01-17 03:09:35', NULL, NULL, '2020-01-01', '2022-01-17'),
(481, 'SI/APR/2022/01/00008', '2022-01-24', 0, 'MEKAR JAYA', '-', 'GEN/IN/1219/00276', 'PO/0220/00147', '2020-02-27', '2020-02-27', '-', '2022-01-24', '1735000.00', '0.00', '1700300.00', NULL, '1068875.00', 'IDR', 1, '2020-03-28', 6, '2.00', '34700.00', '2022-01-24 10:16:57', '2022-01-24 10:16:57', 'Approved', 4, 'indro', 'indro', '2022-01-24', '2022-01-24 10:16:57', NULL, NULL, '2020-01-01', '2022-01-24'),
(482, 'SI/APR/2022/01/00009', '2022-01-25', 0, 'Ricky Kobayashi', 'asal', 'GACC/IN/0120/00292', 'C/AVI/01307', '2020-01-21', '2019-11-06', 'asal', '2022-01-25', '700000.00', '0.00', '630000.00', NULL, '0.00', 'IDR', 1, '2020-05-18', 3, '10.00', '70000.00', '2022-01-25 00:52:16', '2022-01-25 00:52:16', 'Approved', 4, 'indro', 'indro', '2022-01-25', '2022-01-25 00:52:16', NULL, NULL, '2019-01-01', '2022-01-25'),
(484, 'SI/APR/2022/01/00010', '2022-01-25', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'GK/IN/0520/01145', 'DWT/0120/140/01972', '2020-05-09', '2020-03-31', '-', '2022-01-25', '1019.20', '0.00', '917.28', NULL, '917.28', 'USD', 1, '2020-05-14', 3, '10.00', '101.92', '2022-01-25 06:45:33', '2022-01-25 06:45:33', 'draft', 2, 'indro', NULL, NULL, '2022-01-25 06:45:33', NULL, NULL, '2020-01-01', '2022-01-25'),
(485, 'SI/APR/2022/01/00011', '2022-01-27', 0, 'Ricky Kobayashi', '-', 'GACC/IN/0120/00291', 'C/AVI/01307', '2020-01-21', '2019-11-06', '-', '2022-01-27', '6445000.00', '319500.00', '6120000.00', NULL, '0.00', 'IDR', 1, '2020-05-18', 3, '10.00', '644500.00', '2022-01-27 07:31:43', '2022-01-27 07:31:43', 'Cancel', 1, 'indro', NULL, NULL, '2022-01-27 07:31:43', '2022-01-28 10:23:44', 'indro', '2020-01-01', '2022-01-27'),
(487, 'SI/APR/2022/01/00012', '2022-01-27', 0, 'Ricky Kobayashi', '-', 'GACC/IN/0120/00378', 'C/AVI/01307', '2020-01-21', '2019-11-06', '-', '2022-01-27', '17963200.00', '0.00', '17963200.00', NULL, '0.00', 'IDR', 1, '2020-05-18', 0, '0.00', '0.00', '2022-01-27 10:49:10', '2022-01-27 10:49:10', 'Cancel', 1, 'indro', NULL, NULL, '2022-01-27 10:49:10', '2022-01-28 10:23:40', 'indro', '2020-01-01', '2022-01-27');

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
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `kontrabon_cbd`
--

INSERT INTO `kontrabon_cbd` (`id`, `no_kbon`, `tgl_kbon`, `id_jurnal`, `nama_supp`, `no_faktur`, `no_cbd`, `no_po`, `tgl_po`, `supp_inv`, `tgl_inv`, `subtotal`, `tax`, `total`, `curr`, `ceklist`, `tgl_tempo`, `idtax`, `pph_code`, `pph_value`, `post_date`, `update_date`, `status`, `status_int`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `start_date`, `end_date`) VALUES
(53, 'SI/CBD/2022/01/00001', '2022-01-17', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'coba123', 'FTR/C/NAG/0122/00001', 'DWT/1219/163/00423', '2020-01-28', 'coba2', '2022-01-17', '77.00', '0.00', '77.00', 'USD', 1, '2022-01-17', 0, '0.00', '0.00', '2022-01-17 01:06:37', '2022-01-17 01:06:37', 'Approved', 4, 'indro', 'indro', '2022-01-17', '2022-01-17 01:06:37', NULL, NULL, '2020-01-01', '2022-01-17'),
(54, 'SI/CBD/2022/01/00002', '2022-01-17', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'coba123', 'FTR/C/NAG/0122/00002', 'C/DWT/0320/01214', '2020-03-05', 'coba2', '2022-01-17', '281.60', '0.00', '281.60', 'USD', 1, '2022-01-17', 0, '0.00', '0.00', '2022-01-17 03:14:26', '2022-01-17 03:14:26', 'Approved', 4, 'indro', 'indro', '2022-01-17', '2022-01-17 03:14:26', NULL, NULL, '2022-01-17', '2022-01-17'),
(55, 'SI/CBD/2022/01/00003', '2022-01-25', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'FTR/C/NAG/0122/00003', 'DWT/0120/140/01972', '2020-03-31', '-', '2022-01-25', '4959.50', '0.00', '4959.50', 'USD', 1, '2022-01-25', 0, '0.00', '0.00', '2022-01-25 06:38:45', '2022-01-25 06:38:45', 'Approved', 4, 'indro', 'indro', '2022-01-25', '2022-01-25 06:38:45', NULL, NULL, '2020-01-01', '2022-01-25');

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
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `kontrabon_dp`
--

INSERT INTO `kontrabon_dp` (`id`, `no_kbon`, `tgl_kbon`, `id_jurnal`, `nama_supp`, `no_faktur`, `no_dp`, `no_po`, `tgl_po`, `supp_inv`, `tgl_inv`, `subtotal`, `dp_value`, `total`, `curr`, `ceklist`, `tgl_tempo`, `idtax`, `pph_code`, `pph_value`, `post_date`, `update_date`, `status`, `status_int`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `start_date`, `end_date`) VALUES
(55, 'SI/DP/2022/01/00001', '2022-01-24', 0, 'MEKAR JAYA', '-', 'FTR/D/NAG/0122/00001', 'PO/0220/00147', '2020-02-27', '-', '2022-01-24', '2137750.00', '1068875.00', '0.00', 'IDR', 1, '2022-01-24', NULL, NULL, '0.00', '2022-01-24 02:10:59', '2022-01-24 02:10:59', 'Approved', 4, 'indro', 'indro', '2022-01-24', '2022-01-24 02:10:59', NULL, NULL, '2020-01-01', '2022-01-24');

-- --------------------------------------------------------

--
-- Table structure for table `kontrabon_h`
--

CREATE TABLE `kontrabon_h` (
  `id` int(11) NOT NULL,
  `no_kbon` varchar(255) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
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

INSERT INTO `kontrabon_h` (`id`, `no_kbon`, `tgl_kbon`, `nama_supp`, `no_faktur`, `supp_inv`, `tgl_inv`, `tgl_tempo`, `subtotal`, `tax`, `pph_idr`, `rate`, `pph_fgn`, `total`, `dp_value`, `balance`, `curr`, `status`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `post_date`, `update_date`) VALUES
(243, 'SI/APR/2022/01/00001', '2022-01-13', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'coba123', 'coba2', '2022-01-13', '2020-05-20', '416.78', '0.00', '596982.64', '14323.00', '41.68', '375.10', '0.00', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-01-13', '2022-01-13 09:30:18', NULL, NULL, '2022-01-13 09:30:18', '2022-01-13 09:30:18'),
(244, 'SI/APR/2022/01/00002', '2022-01-13', 'ALMINDO PRATAMA CV', '-', '-', '2022-01-13', '2022-01-30', '21.60', '0.00', '30937.68', '14323.00', '2.16', '19.44', '0.00', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-01-13', '2022-01-13 10:06:28', NULL, NULL, '2022-01-13 10:06:28', '2022-01-13 10:06:28'),
(245, 'SI/APR/2022/01/00003', '2022-01-13', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-01-13', '2020-03-10', '2276.46', '0.00', '3260630.95', '14323.00', '227.65', '2033.81', '0.00', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-01-13', '2022-01-13 10:22:38', NULL, NULL, '2022-01-13 10:22:38', '2022-01-13 10:22:38'),
(246, 'SI/APR/2022/01/00004', '2022-01-13', 'Singa Global Textile 2,PT', 'coba123', 'coba2', '2022-01-13', '2021-02-21', '116.00', '0.00', '11.60', '1.00', NULL, '104.40', '0.00', '0.00', 'IDR', 'Approved', 'indro', 'indro', '2022-01-13', '2022-01-13 10:33:48', NULL, NULL, '2022-01-13 10:33:48', '2022-01-13 10:33:48'),
(247, 'SI/APR/2022/01/00005', '2022-01-13', 'PT.SAMJIN BROTHREAD INDONESIA', '-', '-', '2022-01-13', '2022-01-22', '770.00', '0.00', '1102871.00', '14323.00', '77.00', '693.00', '0.00', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-01-13', '2022-01-13 10:57:36', NULL, NULL, '2022-01-13 10:57:36', '2022-01-13 10:57:36'),
(248, 'SI/APR/2022/01/00006', '2022-01-14', 'ALMINDO PRATAMA CV', '-', '-', '2022-01-14', '2022-01-30', '24.30', '0.00', '8729.71', '14311.00', '0.61', '23.69', '0.00', '23.69', 'USD', 'Approved', 'indro', 'indro', '2022-01-14', '2022-01-14 06:16:22', NULL, NULL, '2022-01-14 06:16:22', '2022-01-14 06:16:22'),
(249, 'SI/APR/2022/01/00007', '2022-01-17', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'coba123', 'coba2', '2022-01-17', '2020-01-31', '82.74', '0.00', '29623.77', '14311.00', '2.07', '3.67', '77.00', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-01-17', '2022-01-17 03:09:35', NULL, NULL, '2022-01-17 03:09:35', '2022-01-17 03:09:35'),
(250, 'SI/APR/2022/01/00008', '2022-01-24', 'MEKAR JAYA', '-', '-', '2022-01-24', '2020-03-28', '1735000.00', '0.00', '34700.00', '1.00', NULL, '631425.00', '1068875.00', '0.00', 'IDR', 'Approved', 'indro', 'indro', '2022-01-24', '2022-01-24 10:16:57', NULL, NULL, '2022-01-24 10:16:57', '2022-01-24 10:16:57'),
(251, 'SI/APR/2022/01/00009', '2022-01-25', 'Ricky Kobayashi', 'asal', 'asal', '2022-01-25', '2020-05-18', '700000.00', '0.00', '70000.00', '1.00', NULL, '562000.00', '0.00', '562000.00', 'IDR', 'Approved', 'indro', 'indro', '2022-01-25', '2022-01-25 00:52:16', NULL, NULL, '2022-01-25 00:52:16', '2022-01-25 00:52:16'),
(252, 'SI/APR/2022/01/00010', '2022-01-25', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-01-25', '2020-05-14', '1019.20', '0.00', '1460207.84', '14327.00', '101.92', '0.00', '917.28', '0.00', 'USD', 'draft', 'indro', NULL, NULL, '2022-01-25 06:45:33', NULL, NULL, '2022-01-25 06:45:33', '2022-01-25 06:45:33'),
(253, 'SI/APR/2022/01/00011', '2022-01-27', 'Ricky Kobayashi', '-', '-', '2022-01-27', '2020-05-18', '6445000.00', '319500.00', '644500.00', '1.00', NULL, '5931200.00', '0.00', '5931200.00', 'IDR', 'Cancel', 'indro', NULL, NULL, '2022-01-27 07:31:43', '2022-01-28 10:23:44', 'indro', '2022-01-27 07:31:43', '2022-01-27 07:31:43'),
(254, 'SI/APR/2022/01/00012', '2022-01-27', 'Ricky Kobayashi', '-', '-', '2022-01-27', '2020-05-18', '17963200.00', '0.00', '0.00', '1.00', NULL, '17774400.00', '0.00', '17774400.00', 'IDR', 'Cancel', 'indro', NULL, NULL, '2022-01-27 10:49:10', '2022-01-28 10:23:40', 'indro', '2022-01-27 10:49:10', '2022-01-27 10:49:10');

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

INSERT INTO `kontrabon_h_cbd` (`id`, `no_po`, `tgl_po`, `no_kbon`, `tgl_kbon`, `nama_supp`, `no_faktur`, `supp_inv`, `tgl_inv`, `tgl_tempo`, `subtotal`, `tax`, `pph`, `total`, `balance`, `curr`, `status`, `create_user`, `confirm_user`, `confirm_date`, `create_date`, `cancel_date`, `cancel_user`, `post_date`, `update_date`) VALUES
(55, 'DWT/1219/163/00423', '2020-01-28', 'SI/CBD/2022/01/00001', '2022-01-17', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'coba123', 'coba2', '2022-01-17', '2022-01-17', '77.00', '0.00', '0.00', '77.00', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-01-17', '2022-01-17 01:06:40', NULL, NULL, '2022-01-17 01:06:40', '2022-01-17 01:06:40'),
(56, 'C/DWT/0320/01214', '2020-03-05', 'SI/CBD/2022/01/00002', '2022-01-17', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'coba123', 'coba2', '2022-01-17', '2022-01-17', '281.60', '0.00', '0.00', '281.60', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-01-17', '2022-01-17 03:14:26', NULL, NULL, '2022-01-17 03:14:26', '2022-01-17 03:14:26'),
(57, 'DWT/0120/140/01972', '2020-03-31', 'SI/CBD/2022/01/00003', '2022-01-25', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-01-25', '2022-01-25', '4959.50', '0.00', '0.00', '4959.50', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-01-25', '2022-01-25 06:38:45', NULL, NULL, '2022-01-25 06:38:45', '2022-01-25 06:38:45');

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
(55, 'SI/DP/2022/01/00001', '2022-01-24', 'MEKAR JAYA', '-', '-', '2022-01-24', '2022-01-24', '2137750.00', '1068875.00', NULL, '1068875.00', '0.00', 'IDR', 'Approved', 'indro', 'indro', '2022-01-24', '2022-01-24 02:10:59', NULL, NULL, '2022-01-24 02:10:59', '2022-01-24 02:10:59');

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
(154, 'LP/NAG/0122/00001', '2022-01-13', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'GK/IN/0520/01218', '2020-05-20', 'DWT/0120/018/02323', '2020-04-30', '0.00', 'SI/APR/2022/01/00001', '2022-01-13', '375.10', '0.00', '375.10', 'USD', '-603', '2020-05-20', '-', '2022-01-13 09:30:58', '2022-01-13 09:30:58', 'Approved', 5, 'indro', '2022-01-13 09:30:58', 'indro', '2022-01-13 09:31:02', NULL, NULL, NULL, NULL),
(155, 'LP/NAG/0122/00002', '2022-01-13', NULL, 'ALMINDO PRATAMA CV', 'GACC/IN/1221/04411', '2021-12-31', 'PTX/1021/032/05453', '2021-12-22', '0.00', 'SI/APR/2022/01/00002', '2022-01-13', '19.44', '0.00', '19.44', 'USD', '17', '2022-01-30', '-', '2022-01-13 10:06:58', '2022-01-13 10:06:58', 'Approved', 5, 'indro', '2022-01-13 10:06:58', 'indro', '2022-01-13 10:07:01', NULL, NULL, NULL, NULL),
(156, 'LP/NAG/0122/00003', '2022-01-13', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'GK/IN/0320/00689', '2020-03-10', 'DWT/0120/046/00741', '2020-02-13', '0.00', 'SI/APR/2022/01/00003', '2022-01-13', '2033.81', '1033.81', '1000.00', 'USD', '-674', '2020-03-10', '-', '2022-01-13 10:23:24', '2022-01-13 10:23:24', 'Approved', 5, 'indro', '2022-01-13 10:23:24', 'indro', '2022-01-13 10:23:27', NULL, NULL, NULL, NULL),
(157, 'LP/NAG/0122/00004', '2022-01-13', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'GK/IN/0320/00689', '2020-03-10', 'DWT/0120/046/00741', '2020-02-13', '0.00', 'SI/APR/2022/01/00003', '2022-01-13', '2033.81', '0.00', '1033.81', 'USD', '-674', '2020-03-10', '-', '2022-01-13 10:23:47', '2022-01-13 10:23:47', 'Approved', 5, 'indro', '2022-01-13 10:23:47', 'indro', '2022-01-13 10:23:50', NULL, NULL, NULL, NULL),
(158, 'LP/NAG/0122/00005', '2022-01-13', NULL, 'Singa Global Textile 2,PT', 'GACC/IN/0121/00500', '2021-01-22', 'SGT/0121/021/00449', '2021-01-28', '0.00', 'SI/APR/2022/01/00004', '2022-01-13', '104.40', '0.00', '104.40', 'IDR', '-326', '2021-02-21', '-', '2022-01-13 10:35:02', '2022-01-13 10:35:02', 'Approved', 5, 'indro', '2022-01-13 10:35:02', 'indro', '2022-01-13 10:35:14', NULL, NULL, NULL, NULL),
(159, 'LP/NAG/0122/00006', '2022-01-13', NULL, 'PT.SAMJIN BROTHREAD INDONESIA', 'GACC/IN/1221/04464', '2021-12-23', 'C/PTX/1121/04730', '2021-11-20', '0.00', 'SI/APR/2022/01/00005', '2022-01-13', '693.00', '0.00', '693.00', 'USD', '9', '2022-01-22', '-', '2022-01-13 10:58:04', '2022-01-13 10:58:04', 'Approved', 5, 'indro', '2022-01-13 10:58:04', 'indro', '2022-01-13 10:58:07', NULL, NULL, NULL, NULL),
(160, 'LP/NAG/0122/00007', '2022-01-17', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'GK/IN/0120/00393', '2020-01-31', 'DWT/1219/163/00423', '2020-01-28', '0.00', 'SI/APR/2022/01/00007', '2022-01-17', '3.67', '0.00', '3.67', 'USD', '-717', '2020-01-31', '', '2022-01-17 03:09:54', '2022-01-17 03:09:54', 'Approved', 5, 'indro', '2022-01-17 03:09:54', 'indro', '2022-01-17 03:09:57', NULL, NULL, NULL, NULL),
(161, 'LP/NAG/0122/00008', '2022-01-24', NULL, 'MEKAR JAYA', 'GEN/IN/1219/00276', '2020-02-27', 'PO/0220/00147', '2020-02-27', '0.00', 'SI/APR/2022/01/00008', '2022-01-24', '631425.00', '0.00', '631425.00', 'IDR', '-667', '2020-03-28', '-', '2022-01-24 10:19:00', '2022-01-24 10:19:00', 'Approved', 5, 'indro', '2022-01-24 10:19:00', 'indro', '2022-01-24 10:19:04', NULL, NULL, NULL, NULL);

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
(47, 'LP/CBD/NAG/0122/00001', '2022-01-17', NULL, 'Ricky Kobayashi', 'SI/CBD/2022/01/00001', '2022-01-17', '	C/AVI/01307', '2020-01-28', '6000000.00', '0.00', '6000000.00', '6000000.00', 'USD', NULL, '2022-01-17', 'bayar', '2022-01-17 01:07:19', '2022-01-17 01:07:19', 'Approved', 5, 'indro', '2022-01-17 01:07:19', 'indro', '2022-01-17 01:07:23', NULL, NULL, NULL, NULL),
(48, 'LP/CBD/NAG/0122/00002', '2022-01-17', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'SI/CBD/2022/01/00002', '2022-01-17', 'C/DWT/0320/01214', '2020-03-05', '281.60', '0.00', '281.60', '281.60', 'USD', NULL, '2022-01-17', '-', '2022-01-17 03:14:54', '2022-01-17 03:14:54', 'Approved', 5, 'indro', '2022-01-17 03:14:54', 'indro', '2022-01-17 03:14:58', NULL, NULL, NULL, NULL),
(49, 'LP/CBD/NAG/0122/00003', '2022-01-25', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'SI/CBD/2022/01/00003', '2022-01-25', 'DWT/0120/140/01972', '2020-03-31', '4959.50', '0.00', '4959.50', '4042.22', 'USD', NULL, '2022-01-25', '-', '2022-01-25 06:43:18', '2022-01-25 06:43:18', 'Approved', 5, 'indro', '2022-01-25 06:43:18', 'indro', '2022-01-25 06:43:23', NULL, NULL, NULL, NULL);

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
(52, 'LP/DP/NAG/0122/00001', '2022-01-24', NULL, 'MEKAR JAYA', 'PO/0220/00147', '2020-02-27', '0.00', 'SI/DP/2022/01/00001', '2022-01-24', '1068875.00', '0.00', '1068875.00', 'IDR', NULL, '2022-01-24', '', '2022-01-24 04:21:06', '2022-01-24 04:21:06', 'Approved', 5, 'indro', '2022-01-24 04:21:06', 'indro', '2022-01-24 04:21:11', NULL, NULL, NULL, NULL);

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
(21, 'Create BPB Return');

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
  `create_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment_ftr`
--

INSERT INTO `payment_ftr` (`id`, `payment_ftr_id`, `tgl_pelunasan`, `nama_supp`, `list_payment_id`, `tgl_list_payment`, `no_kbon`, `tgl_kbon`, `valuta_ftr`, `ttl_bayar`, `cara_bayar`, `account`, `bank`, `valuta_bayar`, `nominal`, `nominal_fgn`, `rate`, `keterangan`, `status`, `create_user`, `create_date`) VALUES
(49, 'PAY/LP/NAG/0122/00001', '2022-01-13', 'ALMINDO PRATAMA CV', 'LP/NAG/0122/00002', '2022-01-13', 'SI/APR/2022/01/00002', '2022-01-13', 'USD', '19.44', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '272160.00', '19.44', '14000.00', 'Paid', 'draft', 'indro', '2022-01-13 10:10:11'),
(50, 'PAY/LP/NAG/0122/00002', '2022-01-13', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/NAG/0122/00001', '2022-01-13', 'SI/APR/2022/01/00001', '2022-01-13', 'USD', '375.10', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '5438950.00', '375.10', '14500.00', 'Paid', 'draft', 'indro', '2022-01-13 10:24:24'),
(51, 'PAY/LP/NAG/0122/00002', '2022-01-13', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/NAG/0122/00003', '2022-01-13', 'SI/APR/2022/01/00003', '2022-01-13', 'USD', '1000.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '14500000.00', '1000.00', '14500.00', 'Paid', 'draft', 'indro', '2022-01-13 10:24:24'),
(52, 'PAY/LP/NAG/0122/00003', '2022-01-13', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/NAG/0122/00004', '2022-01-13', 'SI/APR/2022/01/00003', '2022-01-13', 'USD', '1033.81', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '14473340.00', '1033.81', '14000.00', 'Paid', 'draft', 'indro', '2022-01-13 10:25:13'),
(53, 'PAY/LP/NAG/0122/00004', '2022-01-13', 'Singa Global Textile 2,PT', 'LP/NAG/0122/00005', '2022-01-13', 'SI/APR/2022/01/00004', '2022-01-13', 'IDR', '104.40', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '104.40', '0.00', '0.00', 'Paid', 'draft', 'indro', '2022-01-13 10:36:33'),
(54, 'PAY/LP/NAG/0122/00005', '2022-01-13', 'PT.SAMJIN BROTHREAD INDONESIA', 'LP/NAG/0122/00006', '2022-01-13', 'SI/APR/2022/01/00005', '2022-01-13', 'USD', '693.00', 'TRANSFER', '008-998-1982', 'BANK CENTRAL ASIA', 'USD', '0.00', '693.00', '0.00', 'Paid', 'draft', 'indro', '2022-01-13 10:58:50'),
(55, 'PAY/LP/NAG/0122/00006', '2022-01-17', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/NAG/0122/00007', '2022-01-17', 'SI/APR/2022/01/00007', '2022-01-17', 'USD', '3.67', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '51747.00', '3.67', '14100.00', 'Paid', 'draft', 'indro', '2022-01-17 03:10:52'),
(56, 'PAY/LP/NAG/0122/00007', '2022-01-24', 'MEKAR JAYA', 'LP/NAG/0122/00008', '2022-01-24', 'SI/APR/2022/01/00008', '2022-01-24', 'IDR', '631425.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '631425.00', '0.00', '0.00', 'Paid', 'draft', 'indro', '2022-01-24 10:19:26');

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
  `create_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment_ftrcbd`
--

INSERT INTO `payment_ftrcbd` (`id`, `payment_ftr_id`, `tgl_pelunasan`, `nama_supp`, `list_payment_id`, `tgl_list_payment`, `no_kbon`, `tgl_kbon`, `valuta_ftr`, `ttl_bayar`, `cara_bayar`, `account`, `bank`, `valuta_bayar`, `nominal`, `nominal_fgn`, `rate`, `keterangan`, `status`, `create_user`, `create_date`) VALUES
(12, 'PAY/LP/CBD/NAG/0122/00002', '2022-01-17', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0122/00002', '2022-01-17', 'SI/CBD/2022/01/00002', '2022-01-17', 'USD', '281.60', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '3942400.00', '281.60', '14000.00', 'Paid', 'draft', 'indro', '2022-01-17 03:36:52'),
(13, 'PAY/LP/CBD/NAG/0122/00003', '2022-01-17', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0122/00001', '2022-01-17', 'SI/CBD/2022/01/00001', '2022-01-17', 'USD', '77.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '1078000.00', '77.00', '14000.00', 'Paid', 'draft', 'indro', '2022-01-17 10:28:38'),
(14, 'PAY/LP/CBD/NAG/0122/00004', '2022-01-25', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0122/00003', '2022-01-25', 'SI/CBD/2022/01/00003', '2022-01-25', 'USD', '4959.50', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '70424900.00', '4959.50', '14200.00', 'Paid', 'draft', 'indro', '2022-01-25 06:43:59');

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
  `create_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payment_ftrdp`
--

INSERT INTO `payment_ftrdp` (`id`, `payment_ftr_id`, `tgl_pelunasan`, `nama_supp`, `list_payment_id`, `tgl_list_payment`, `no_kbon`, `tgl_kbon`, `valuta_ftr`, `ttl_bayar`, `cara_bayar`, `account`, `bank`, `valuta_bayar`, `nominal`, `nominal_fgn`, `rate`, `keterangan`, `status`, `create_user`, `create_date`) VALUES
(4, 'PAY/LP/DP/NAG/0122/00001', '2022-01-24', 'MEKAR JAYA', 'LP/DP/NAG/0122/00001', '2022-01-24', 'SI/DP/2022/01/00001', '2022-01-24', 'IDR', '1068875.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '1068875.00', '0.00', '0.00', 'Paid', 'draft', 'indro', '2022-01-24 04:22:38');

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
(67, 'SI/APR/2022/01/00001', '2022-01-13', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Approved'),
(68, 'SI/APR/2022/01/00002', '2022-01-13', 'ALMINDO PRATAMA CV', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Approved'),
(69, 'SI/APR/2022/01/00003', '2022-01-13', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '0.00', '-5.00', '-5.00', '-5.00', '10.00', '-5.00', '-5.00', '0.00', '-15.00', 'Approved'),
(70, 'SI/APR/2022/01/00004', '2022-01-13', 'Singa Global Textile 2,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Approved'),
(71, 'SI/APR/2022/01/00005', '2022-01-13', 'PT.SAMJIN BROTHREAD INDONESIA', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Approved'),
(72, 'SI/APR/2022/01/00006', '2022-01-14', 'ALMINDO PRATAMA CV', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Approved'),
(73, 'SI/APR/2022/01/00007', '2022-01-17', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Approved'),
(74, 'SI/APR/2022/01/00008', '2022-01-24', 'MEKAR JAYA', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Approved'),
(75, 'SI/APR/2022/01/00009', '2022-01-25', 'Ricky Kobayashi', '118000.00', '-50000.00', '100000.00', '0.00', '0.00', '0.00', '0.00', '0.00', '50000.00', 'Approved'),
(76, 'SI/APR/2022/01/00010', '2022-01-25', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'draft'),
(77, 'SI/APR/2022/01/00011', '2022-01-27', 'Ricky Kobayashi', '188800.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(78, 'SI/APR/2022/01/00012', '2022-01-27', 'Ricky Kobayashi', '188800.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel');

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
  `total` decimal(16,2) DEFAULT NULL,
  `total_update` decimal(16,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(93, 'indro', 'Indro', 'Verifikasi BPB', '2022-01-10 02:08:03', 'indro'),
(95, 'indro', 'Indro', 'Kontrabon', '2022-01-10 02:08:47', 'indro'),
(96, 'indro', 'Indro', 'Create List payment', '2022-01-10 02:08:47', 'indro'),
(97, 'indro', 'Indro', 'Create FTR', '2022-01-10 02:08:47', 'indro'),
(98, 'indro', 'Indro', 'List Payment', '2022-01-10 02:08:47', 'indro'),
(99, 'indro', 'Indro', 'FTR', '2022-01-10 02:08:47', 'indro'),
(100, 'indro', 'Indro', 'Create BPB', '2022-01-10 02:08:47', 'indro'),
(101, 'indro', 'Indro', 'Create Kontrabon', '2022-01-10 02:08:47', 'indro'),
(102, 'indro', 'Indro', 'Payment', '2022-01-10 02:08:47', 'indro'),
(103, 'indro', 'Indro', 'Create Maintain FTR', '2022-01-10 02:08:47', 'indro'),
(104, 'indro', 'Indro', 'Create Maintain Kontrabon', '2022-01-10 02:08:47', 'indro'),
(105, 'indro', 'Indro', 'Maintain FTR', '2022-01-10 02:08:47', 'indro'),
(106, 'indro', 'Indro', 'Create Payment', '2022-01-10 02:08:47', 'indro'),
(107, 'indro', 'Indro', 'Maintain Kontrabon', '2022-01-10 02:08:47', 'indro'),
(108, 'indro', 'Indro', 'Maintain List Payment', '2022-01-10 02:08:47', 'indro'),
(109, 'indro', 'Indro', 'Create Maintain List Payment', '2022-01-10 02:08:47', 'indro'),
(110, 'indro', 'Indro', 'Report', '2022-01-10 02:08:47', 'indro'),
(112, 'indro', 'Indro', 'verifikasi BPB Return', '2022-01-10 02:08:47', 'indro'),
(113, 'indro', 'Indro', 'Create BPB Return', '2022-01-10 02:08:47', 'indro'),
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
(127, 'indro', 'Indro', 'Approve BPB', '2022-01-12 01:07:27', 'indro'),
(128, 'indro', 'Indro', 'Approve BPB Return', '2022-01-24 10:31:49', 'indro');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=619;

--
-- AUTO_INCREMENT for table `bppb_new`
--
ALTER TABLE `bppb_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `ftr_cbd`
--
ALTER TABLE `ftr_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `ftr_dp`
--
ALTER TABLE `ftr_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `kartu_hutang`
--
ALTER TABLE `kartu_hutang`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT for table `kontrabon`
--
ALTER TABLE `kontrabon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=489;

--
-- AUTO_INCREMENT for table `kontrabon_cbd`
--
ALTER TABLE `kontrabon_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `kontrabon_dp`
--
ALTER TABLE `kontrabon_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `kontrabon_h`
--
ALTER TABLE `kontrabon_h`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `kontrabon_h_cbd`
--
ALTER TABLE `kontrabon_h_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `kontrabon_h_dp`
--
ALTER TABLE `kontrabon_h_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `list_payment`
--
ALTER TABLE `list_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `list_payment_cbd`
--
ALTER TABLE `list_payment_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `list_payment_dp`
--
ALTER TABLE `list_payment_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `menurole`
--
ALTER TABLE `menurole`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payment_ftr`
--
ALTER TABLE `payment_ftr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `payment_ftrcbd`
--
ALTER TABLE `payment_ftrcbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payment_ftrdp`
--
ALTER TABLE `payment_ftrdp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengajuan_ftrcbd`
--
ALTER TABLE `pengajuan_ftrcbd`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `pengajuan_ftrdp`
--
ALTER TABLE `pengajuan_ftrdp`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pengajuan_kb`
--
ALTER TABLE `pengajuan_kb`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `pengajuan_kb_cbd`
--
ALTER TABLE `pengajuan_kb_cbd`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pengajuan_kb_dp`
--
ALTER TABLE `pengajuan_kb_dp`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pengajuan_payment`
--
ALTER TABLE `pengajuan_payment`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pengajuan_paymentcbd`
--
ALTER TABLE `pengajuan_paymentcbd`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pengajuan_paymentdp`
--
ALTER TABLE `pengajuan_paymentdp`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `potongan`
--
ALTER TABLE `potongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `ttd`
--
ALTER TABLE `ttd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ttl_bppb`
--
ALTER TABLE `ttl_bppb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `useraccess`
--
ALTER TABLE `useraccess`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
