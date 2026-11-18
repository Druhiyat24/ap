<?php
/* ============================================================================
   EXECUTIVE SUMMARY satu potret - dipakai tab kedua di cf_history_detail.php.

   TIDAK MEMBACA DATABASE SENDIRI. Berkas ini meng-include
   ajx_cf_history_detail.php dalam mode pustaka (CFH_PUSTAKA) lalu
   menyerahkan matriksnya ke cf_exec_ringkasan(). Jadi angka di tab
   Executive Summary mustahil berbeda dgn angka di tab Projection: sumbernya
   satu, cuma disusun lain. Pola yang sama dgn ekspor Excel-nya.

   Sesi dibuka DI SINI, sebelum include - dalam mode pustaka berkas yang
   di-include tidak memanggil session_start() sendiri.
   ============================================================================ */
session_start();
header('Content-Type: application/json');

define('CFH_PUSTAKA', 1);
$_POST['doc_number'] = isset($_POST['doc_number']) ? $_POST['doc_number'] : '';

ob_start();
include __DIR__ . '/ajx_cf_history_detail.php';
$jawab = json_decode(trim((string) ob_get_clean()), true);

if (!is_array($jawab) || !isset($jawab['status']) || $jawab['status'] !== 'success') {
    echo json_encode(array('status' => 'error',
        'message' => (is_array($jawab) && isset($jawab['message'])) ? $jawab['message'] : 'Cannot read this document.'));
    exit;
}

include __DIR__ . '/cf_exec_lib.php';
echo json_encode(cf_exec_ringkasan($jawab), JSON_INVALID_UTF8_SUBSTITUTE);
