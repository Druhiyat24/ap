<?php
// Endpoint khusus create-paymentvoucher-exim.php - simpan pilihan Supporting
// Document (modal "Choose Supporting Document") dalam SATU request.
//
// insertdoc.php (lama) hanya menghapus baris punya unik_code LAIN, tidak
// pernah menghapus baris punya sesi sendiri - jadi kalau user pilih PO lalu
// Save, lalu uncheck PO dan pilih SO lalu Save lagi, baris "PO" lama tidak
// pernah hilang (numpuk). Di sini baris milik unik_code sendiri SELALU
// dibersihkan dulu sebelum insert ulang sesuai checklist saat ini, supaya
// supp_doc_temp selalu mencerminkan checklist yang terakhir disimpan.
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
include '../../conn/conn.php';

header('Content-Type: application/json');

function sd_esc($conn, $v) {
    return mysqli_real_escape_string($conn, (string)$v);
}

$doc_number = sd_esc($conn2, $_POST['doc_number'] ?? '');
$unik_code = sd_esc($conn2, $_POST['unik_code'] ?? '');
$items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];

if (!is_array($items)) {
    $items = [];
}

mysqli_begin_transaction($conn2);

try {
    // bersihkan sesi lain (perilaku lama) sekaligus sesi sendiri, baru insert ulang
    if (!mysqli_query($conn2, "delete from supp_doc_temp where unik_code != '$unik_code'")) {
        throw new Exception('Gagal bersihkan supp_doc_temp sesi lain: ' . mysqli_error($conn2));
    }
    if (!mysqli_query($conn2, "delete from supp_doc_temp where unik_code = '$unik_code'")) {
        throw new Exception('Gagal bersihkan supp_doc_temp sesi sendiri: ' . mysqli_error($conn2));
    }

    foreach ($items as $data) {
        $data = sd_esc($conn2, $data);
        if ($data === '') {
            continue;
        }
        $q = "INSERT INTO supp_doc_temp (ref_doc,ket,unik_code) VALUES ('$doc_number', '$data', '$unik_code')";
        if (!mysqli_query($conn2, $q)) {
            throw new Exception('Gagal insert Supporting Document: ' . mysqli_error($conn2));
        }
    }

    $sql = mysqli_query($conn2, "select GROUP_CONCAT(ket) as sup_doc from (select * from supp_doc_temp where ket != '') supp_doc_temp");
    $row = mysqli_fetch_array($sql);
    $sup_doc = isset($row['sup_doc']) ? $row['sup_doc'] : '';

    mysqli_commit($conn2);
    echo json_encode(['success' => true, 'sup_doc' => $sup_doc]);
} catch (Exception $e) {
    mysqli_rollback($conn2);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn2);
