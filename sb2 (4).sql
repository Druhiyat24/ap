-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2022 at 12:01 PM
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
(1, 'GK/IN/0420/01057', 'DWT/0120/140/01972', 'DWT/0120/147', '2020-04-24', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM SCARLET', 'SCARLET', '66\"', '110.0000', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03'),
(2, 'GK/IN/0520/01145', 'DWT/0120/140/01972', 'DWT/0120/141', '2020-05-09', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM ROYAL', 'ROYAL', '66\"', '112.0000', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Invoiced', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03'),
(3, 'GK/IN/0420/01057', 'DWT/0120/140/01972', 'DWT/0120/140', '2020-04-24', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM SCARLET', 'SCARLET', '66\"', '110.0000', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03'),
(4, 'GK/IN/0520/01145', 'DWT/0120/140/01972', 'DWT/0220/171', '2020-05-09', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '47.2800', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Invoiced', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03'),
(5, 'GK/IN/0420/01057', 'DWT/0120/140/01972', 'DWT/0120/148', '2020-04-24', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM SQK NAVY', 'SQK NAVY', '66\"', '48.0000', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Waiting', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03'),
(6, 'GK/IN/0520/01145', 'DWT/0120/140/01972', 'DWT/0120/149', '2020-05-09', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '53.6800', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Invoiced', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03'),
(7, 'GK/IN/0520/01198', 'DWT/0120/140/01972', 'DWT/0220/171', '2020-05-14', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '7.7200', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Invoiced', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03'),
(8, 'GK/IN/0520/01145', 'DWT/0120/140/01972', 'DWT/0120/148', '2020-05-09', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '53.8200', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Invoiced', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03'),
(9, 'GK/IN/0520/01198', 'DWT/0120/140/01972', 'DWT/0120/149', '2020-05-14', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '1.3200', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Invoiced', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03'),
(10, 'GK/IN/0520/01198', 'DWT/0120/140/01972', 'DWT/0120/148', '2020-05-14', '2020-03-31', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'FABRIC KNIT WATER REPELLANT DOUBLE FACE 1  SIDE FLEECE 100% POLYESTER 66\" 260GSM PURPLE', 'PURPLE', '66\"', '1.1800', 'KGM', '9.1000', '0.0000', 'USD', 'indro', 'yulius', 'indro', '2022-02-03 10:24:49', 'Invoiced', 'GMF-PCH', 0, 'CBD', '2022-02-03 10:21:59', '2022-02-03 10:21:59', 'indro', 1, '2019-01-01', '2022-02-03');

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
(1, 'FTR/C/NAG/0222/00001', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', NULL, 'DWT/0120/140/01972', '2020-03-31', '-', '4959.50', '0.00', '4959.50', 'USD', '-', 'Approved', 'Invoiced', '2022-02-03 17:22:11', '2022-02-03 17:22:14', NULL, 'indro', '', NULL, '1');

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
(1, '-', NULL, 'DWT/0120/140/01972', '2020-03-31', 'SI/CBD/2022/02/00001', '2022-02-03', '-', '2022-02-03', '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-03', NULL, 'LP/CBD/NAG/0222/00001', '2022-02-03', 'USD', 'Payment CBD', '14347.00', NULL, '0.00', '2000.00', '0.00', '28694000.00', '1'),
(2, '-', NULL, 'DWT/0120/140/01972', '2020-03-31', 'SI/CBD/2022/02/00001', '2022-02-03', '-', '2022-02-03', '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-03', NULL, 'LP/CBD/NAG/0222/00002', '2022-02-03', 'USD', 'Payment CBD', '14347.00', NULL, '0.00', '2959.50', '0.00', '42459946.50', '1'),
(3, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-03', NULL, 'LP/CBD/NAG/0222/00001', '2022-02-03', 'USD', 'Selisih Kurs', '14347.00', NULL, '0.00', '0.00', '0.00', '0.00', '1'),
(4, '-', NULL, '-', NULL, '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-03', NULL, 'LP/CBD/NAG/0222/00002', '2022-02-03', 'USD', 'Selisih Kurs', '14347.00', NULL, '0.00', '0.00', '0.00', '0.00', '1'),
(5, 'GK/IN/0520/01145', '2020-05-09', 'DWT/0120/140/01972', '2020-03-31', 'SI/APR/2022/02/00004', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-03', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '2427.70', '0.00', '34830183.21', '0.00', '2'),
(6, 'GK/IN/0420/01057', '2020-04-24', 'DWT/0120/140/01972', '2020-03-31', '-', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-03', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '2438.80', '0.00', '34989463.60', '0.00', '2'),
(7, 'GK/IN/0520/01198', '2020-05-14', 'DWT/0120/140/01972', '2020-03-31', 'SI/APR/2022/02/00003', NULL, '-', NULL, '-', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '2022-02-03', NULL, '-', NULL, 'USD', 'Purchase', '14347.00', '2022-02-03', '93.00', '0.00', '1334299.69', '0.00', '2');

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
(1, 'SI/APR/2022/02/00001', '2022-02-03', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'GK/IN/0520/01198', 'DWT/0120/140/01972', '2020-05-14', '2020-03-31', '-', '2022-02-03', '93.00', '0.00', '93.00', NULL, '93.00', 'USD', 1, '2020-05-14', 0, '0.00', '0.00', '2022-02-03 10:38:36', '2022-02-03 10:38:36', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-03 10:38:36', '2022-02-03 10:40:25', 'indro', '2020-01-01', '2022-02-03', NULL),
(2, 'SI/APR/2022/02/00002', '2022-02-03', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'GK/IN/0520/01198', 'DWT/0120/140/01972', '2020-05-14', '2020-03-31', '-', '2022-02-03', '93.00', '0.00', '93.00', NULL, '2520.70', 'USD', 1, '2020-05-14', 0, '0.00', '0.00', '2022-02-03 10:40:42', '2022-02-03 10:40:42', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-03 10:40:42', '2022-02-03 10:46:03', 'indro', '2019-01-01', '2022-02-03', NULL),
(3, 'SI/APR/2022/02/00002', '2022-02-03', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'GK/IN/0520/01145', 'DWT/0120/140/01972', '2020-05-09', '2020-03-31', '-', '2022-02-03', '2427.70', '0.00', '2427.70', NULL, '2520.70', 'USD', 1, '2020-05-14', 0, '0.00', '0.00', '2022-02-03 10:40:43', '2022-02-03 10:40:43', 'Cancel', 1, 'indro', NULL, NULL, '2022-02-03 10:40:43', '2022-02-03 10:46:03', 'indro', '2019-01-01', '2022-02-03', NULL),
(4, 'SI/APR/2022/02/00003', '2022-02-03', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'GK/IN/0520/01198', 'DWT/0120/140/01972', '2020-05-14', '2020-03-31', '-', '2022-02-03', '93.00', '0.00', '93.00', NULL, '93.00', 'USD', 1, '2020-05-14', 0, '0.00', '0.00', '2022-02-03 10:46:36', '2022-02-03 10:46:36', 'draft', 2, 'indro', NULL, NULL, '2022-02-03 10:46:36', NULL, NULL, '2019-01-01', '2022-02-03', NULL),
(5, 'SI/APR/2022/02/00004', '2022-02-03', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'GK/IN/0520/01145', 'DWT/0120/140/01972', '2020-05-09', '2020-03-31', '-', '2022-02-03', '2427.70', '0.00', '2427.70', NULL, '2427.70', 'USD', 1, '2020-05-09', 0, '0.00', '0.00', '2022-02-03 10:46:58', '2022-02-03 10:46:58', 'draft', 2, 'indro', NULL, NULL, '2022-02-03 10:46:58', NULL, NULL, '2019-01-01', '2022-02-03', NULL);

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
(1, 'SI/CBD/2022/02/00001', '2022-02-03', 0, 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', 'FTR/C/NAG/0222/00001', 'DWT/0120/140/01972', '2020-03-31', '-', '2022-02-03', '4959.50', '0.00', '4959.50', 'USD', 1, '2022-02-03', 0, '0.00', '0.00', '2022-02-03 10:22:41', '2022-02-03 10:22:41', 'Approved', 4, 'indro', 'indro', '2022-02-03', '2022-02-03 10:22:41', NULL, NULL, '2022-02-03', '2022-02-03', '1');

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
(1, 'SI/APR/2022/02/00001', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-02-03', '2020-05-14', '93.00', '0.00', '0.00', '14347.00', '0.00', '0.00', '93.00', '0.00', 'USD', 'Cancel', 'indro', NULL, NULL, '2022-02-03 10:38:36', '2022-02-03 10:40:25', 'indro', '2022-02-03 10:38:36', '2022-02-03 10:38:36'),
(2, 'SI/APR/2022/02/00002', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-02-03', '2020-05-14', '2520.70', '0.00', '0.00', '14347.00', '0.00', '0.00', '2520.70', '0.00', 'USD', 'Cancel', 'indro', NULL, NULL, '2022-02-03 10:40:42', '2022-02-03 10:46:03', 'indro', '2022-02-03 10:40:42', '2022-02-03 10:40:42'),
(3, 'SI/APR/2022/02/00003', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-02-03', '2020-05-14', '93.00', '0.00', '0.00', '14347.00', '0.00', '0.00', '93.00', '0.00', 'USD', 'draft', 'indro', NULL, NULL, '2022-02-03 10:46:35', NULL, NULL, '2022-02-03 10:46:35', '2022-02-03 10:46:35'),
(4, 'SI/APR/2022/02/00004', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-02-03', '2020-05-09', '2427.70', '0.00', '0.00', '14347.00', '0.00', '0.00', '2427.70', '0.00', 'USD', 'draft', 'indro', NULL, NULL, '2022-02-03 10:46:58', NULL, NULL, '2022-02-03 10:46:58', '2022-02-03 10:46:58');

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
(1, 'DWT/0120/140/01972', '2020-03-31', 'SI/CBD/2022/02/00001', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '-', '-', '2022-02-03', '2022-02-03', '4959.50', '0.00', '0.00', '4959.50', '4959.5000', '0.00', 'USD', 'Approved', 'indro', 'indro', '2022-02-03', '2022-02-03 10:22:41', NULL, NULL, '2022-02-03 10:22:41', '2022-02-03 10:22:41');

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
(1, 'LP/CBD/NAG/0222/00001', '2022-02-03', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'SI/CBD/2022/02/00001', '2022-02-03', 'DWT/0120/140/01972', '2020-03-31', '4959.50', '2959.50', '2000.00', '2000.00', 'USD', NULL, '2022-02-03', '-', '2022-02-03 10:23:13', '2022-02-03 10:23:13', 'Approved', 5, 'indro', '2022-02-03 10:23:13', 'indro', '2022-02-03 10:23:17', NULL, NULL, NULL, NULL),
(2, 'LP/CBD/NAG/0222/00002', '2022-02-03', NULL, 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'SI/CBD/2022/02/00001', '2022-02-03', 'DWT/0120/140/01972', '2020-03-31', '4959.50', '0.00', '2959.50', '2959.50', 'USD', NULL, '2022-02-03', '-', '2022-02-03 10:23:36', '2022-02-03 10:23:36', 'Approved', 5, 'indro', '2022-02-03 10:23:36', 'indro', '2022-02-03 10:23:41', NULL, NULL, NULL, NULL);

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
(14, 'PAY/LP/CBD/NAG/0122/00004', '2022-01-25', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0122/00003', '2022-01-25', 'SI/CBD/2022/01/00003', '2022-01-25', 'USD', '4959.50', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '70424900.00', '4959.50', '14200.00', 'Paid', 'draft', 'indro', '2022-01-25 06:43:59'),
(15, 'PAY/LP/CBD/NAG/0122/00005', '2022-01-29', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0122/00004', '2022-01-29', 'SI/CBD/2022/01/00006', '2022-01-29', 'USD', '1000.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '14100000.00', '1000.00', '14100.00', 'Paid', 'draft', 'indro', '2022-01-29 09:53:22'),
(16, 'PAY/LP/CBD/NAG/0122/00005', '2022-01-29', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0122/00005', '2022-01-29', 'SI/CBD/2022/01/00006', '2022-01-29', 'USD', '365.00', 'TRANSFER', '442-244-2000', 'BANK NEGARA INDONESIA', 'IDR', '5146500.00', '365.00', '14100.00', 'Paid', 'draft', 'indro', '2022-01-29 09:53:22'),
(17, 'PAY/LP/CBD/NAG/0122/00006', '2022-01-29', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0122/00006', '2022-01-29', 'SI/CBD/2022/01/00007', '2022-01-29', 'USD', '15000.00', 'TRANSFER', '008-998-1982', 'BANK CENTRAL ASIA', 'USD', '0.00', '15000.00', '0.00', 'Paid', 'draft', 'indro', '2022-01-29 10:55:17'),
(18, 'PAY/LP/CBD/NAG/0122/00006', '2022-01-29', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0122/00007', '2022-01-29', 'SI/CBD/2022/01/00007', '2022-01-29', 'USD', '25469.10', 'TRANSFER', '008-998-1982', 'BANK CENTRAL ASIA', 'USD', '0.00', '25469.10', '0.00', 'Paid', 'draft', 'indro', '2022-01-29 10:55:17'),
(19, 'PAY/LP/CBD/NAG/0222/00007', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0222/00001', '2022-02-03', 'SI/CBD/2022/02/00001', '2022-02-03', 'USD', '2000.00', 'TRANSFER', '008-998-1982', 'BANK CENTRAL ASIA', 'USD', '0.00', '2000.00', '0.00', 'Paid', 'draft', 'indro', '2022-02-03 10:24:29'),
(20, 'PAY/LP/CBD/NAG/0222/00007', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', 'LP/CBD/NAG/0222/00002', '2022-02-03', 'SI/CBD/2022/02/00001', '2022-02-03', 'USD', '2959.50', 'TRANSFER', '008-998-1982', 'BANK CENTRAL ASIA', 'USD', '0.00', '2959.50', '0.00', 'Paid', 'draft', 'indro', '2022-02-03 10:24:29');

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
(1, 'SI/APR/2022/02/00001', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(2, 'SI/APR/2022/02/00002', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'Cancel'),
(3, 'SI/APR/2022/02/00003', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'draft'),
(4, 'SI/APR/2022/02/00004', '2022-02-03', 'INDO TAICHEN TEXTILE INDUSTRY,PT', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'draft');

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
  `total_update` decimal(16,2) DEFAULT NULL,
  `no_kbon` varchar(200) DEFAULT NULL
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
(155, 'indro', 'Indro', 'Create BPB Return', '2022-02-02 06:51:43', 'indro');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bppb_new`
--
ALTER TABLE `bppb_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ftr_cbd`
--
ALTER TABLE `ftr_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ftr_dp`
--
ALTER TABLE `ftr_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kartu_hutang`
--
ALTER TABLE `kartu_hutang`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kontrabon`
--
ALTER TABLE `kontrabon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kontrabon_cbd`
--
ALTER TABLE `kontrabon_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kontrabon_dp`
--
ALTER TABLE `kontrabon_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kontrabon_h`
--
ALTER TABLE `kontrabon_h`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kontrabon_h_cbd`
--
ALTER TABLE `kontrabon_h_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kontrabon_h_dp`
--
ALTER TABLE `kontrabon_h_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `list_payment`
--
ALTER TABLE `list_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `list_payment_cbd`
--
ALTER TABLE `list_payment_cbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `list_payment_dp`
--
ALTER TABLE `list_payment_dp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menurole`
--
ALTER TABLE `menurole`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payment_ftr`
--
ALTER TABLE `payment_ftr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_ftrcbd`
--
ALTER TABLE `payment_ftrcbd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payment_ftrdp`
--
ALTER TABLE `payment_ftrdp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengajuan_ftrcbd`
--
ALTER TABLE `pengajuan_ftrcbd`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
