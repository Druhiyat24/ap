<?php
// Endpoint AJAX ringan - dipanggil dari openTab() di financial_statement.php
// utk lazy-load 1 tab report (SFP/SPL/CF Direct/CF Indirect/Trial Balance)
// SAAT diklik, bukan semuanya sekaligus di setiap page load. Latar
// belakang: dulu halaman utama meng-include KELIMA tab report sekaligus
// (5x query berat) tiap kali Search diklik, padahal user cuma lihat 1 tab
// dalam satu waktu - bikin loading Search lama & halaman kosong-putih lama
// sebelum semua bagian muncul.
//
// Bootstrap session/koneksi DISALIN APA ADANYA dari module/header.php
// (TANPA bagian HTML shell-nya, krn ini cuma perlu return 1 fragment HTML
// tab, bukan halaman penuh) - file ini SENGAJA ditaruh di module/AP/ (folder
// yang sama dgn financial_statement.php) supaya path relatif
// '../../conn/conn.php' resolve persis sama (PHP resolve include relatif
// berdasar direktori entry-point script yang di-hit langsung oleh Apache,
// bukan direktori file yang berisi baris include-nya).
ini_set('memory_limit', '4096M');
set_time_limit(0);
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
include '../../conn/conn.php';
$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    http_response_code(401);
    exit('Unauthorized');
}

$tab = isset($_POST['tab']) ? $_POST['tab'] : '';
$fs_system = (($_POST['h_fs_system'] ?? '') === '1') ? '1' : '2';
$report_type = (($_POST['h_report_type'] ?? '') === 'ytd') ? 'ytd' : 'monthly';

$tabIncludes = include 'fs_tab_includes.php';

if (!isset($tabIncludes[$tab][$fs_system][$report_type])) {
    http_response_code(400);
    exit('Invalid tab request');
}

// PENTING: fs_ytd/statement_financial_position.php, statement_profit_loss.php,
// cashflow_direct.php, cashflow_indirect.php (FS2 YTD) TIDAK menghitung
// $bulan_awal/$bulan_akhir/$tahun_awal/$tahun_akhir sendiri - keempatnya
// diam-diam mengandalkan variabel itu SUDAH ada di scope PHP, hasil "warisan"
// dari fs_ytd/trial_balance_ytd.php yang dulu SELALU ikut di-include lebih
// dulu di request yang sama (kelima tab dulu dirender sekaligus). Sejak tab
// jadi lazy-load satu-satu (lihat fs_tab_includes.php/openTab() di
// financial_statement.php), tab manapun BISA jadi yang PERTAMA & SATU-
// SATUNYA yang jalan di request AJAX ini, jadi variabel itu perlu dihitung DI
// SINI dulu (persis logic aslinya di trial_balance_ytd.php baris ~237-257),
// SEBELUM include tab manapun - supaya SFP/SPL/CF Direct/CF Indirect FS2 YTD
// tidak lagi jatuh ke default "01 January 1970" (bulan_akhir/tahun_akhir
// kosong -> query tgl_akhir kosong -> strtotime('') -> epoch).
// Trial Balance & semua tab FS1/FS2 Monthly TIDAK butuh ini (masing2 sudah
// menghitung $start_date/$bulan_awal dkk. sendiri).
$date_now = date('Y-m-d');
$bulan_awal = date('m', strtotime($date_now));
$bulan_akhir = date('m', strtotime($date_now));
$tahun_awal = date('Y', strtotime($date_now));
$tahun_akhir = date('Y', strtotime($date_now));
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $bulan_awal = date('m', strtotime($_POST['start_date']));
    $bulan_akhir = date('m', strtotime($_POST['end_date']));
    $tahun_awal = date('Y', strtotime($_POST['start_date']));
    $tahun_akhir = date('Y', strtotime($_POST['end_date']));
}

include $tabIncludes[$tab][$fs_system][$report_type];
