<?php
// Endpoint khusus create-paymentvoucher-exim.php - simpan header tbl_pv_h
// dan SEMUA baris detail tbl_pv dalam SATU request + SATU transaction.
// Kalau ada satu baris gagal, semuanya di-rollback (tidak ada PV header
// nyantol tanpa detail, atau detail yang ke-insert sebagian).
//
// Sengaja dibuat sebagai file baru, bukan mengubah insertpv.php/insertpv_h.php
// langsung - dua file itu dipakai bersama oleh create-paymentvoucher.php,
// create-paymentvoucher-ftr.php, edit-paymentvoucher.php, dll.

session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

header('Content-Type: application/json');

function pv_esc($conn, $v) {
    return mysqli_real_escape_string($conn, (string)$v);
}

function pv_date_or($v) {
    $ts = strtotime((string)$v);
    return $ts ? date('Y-m-d', $ts) : '1970-01-01';
}

$rowsRaw = isset($_POST['rows']) ? json_decode($_POST['rows'], true) : null;

if (!is_array($rowsRaw) || count($rowsRaw) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Tidak ada baris detail untuk disimpan.']);
    exit;
}

$no_pv = pv_esc($conn2, $_POST['no_pv'] ?? '');
$rat_pv = pv_esc($conn2, $_POST['rat_pv'] ?? '');
$pv_date = pv_date_or($_POST['pv_date'] ?? '');
$nama_supp = pv_esc($conn2, $_POST['nama_supp'] ?? '');
$sup_doc = pv_esc($conn2, $_POST['sup_doc'] ?? '');
$ctb = pv_esc($conn2, $_POST['ctb'] ?? '');
$pay_date = pv_date_or($_POST['pay_date'] ?? '');
$pay_mth = pv_esc($conn2, $_POST['pay_mth'] ?? '');
$curr = pv_esc($conn2, $_POST['curr'] ?? '');
$forpay = pv_esc($conn2, $_POST['forpay'] ?? '');
$frcc = pv_esc($conn2, $_POST['frcc'] ?? '');
$tocc = pv_esc($conn2, $_POST['tocc'] ?? '');
$ke = pv_esc($conn2, $_POST['ke'] ?? '');
$dari = pv_esc($conn2, $_POST['dari'] ?? '');
$no_cek = pv_esc($conn2, $_POST['no_cek'] ?? '');
$cek_date = pv_date_or($_POST['cek_date'] ?? '');
$pesan = pv_esc($conn2, $_POST['pesan'] ?? '');
$subtotal = pv_esc($conn2, $_POST['subtotal'] ?? 0);
$adjust = pv_esc($conn2, $_POST['adjust'] ?? 0);
$pph_h = pv_esc($conn2, $_POST['pph'] ?? 0);
$ppn_h = pv_esc($conn2, $_POST['ppn'] ?? 0);
$total = pv_esc($conn2, $_POST['total'] ?? 0);
$pilih_ppn = pv_esc($conn2, $_POST['pilih_ppn'] ?? '');
$pilih_pph = pv_esc($conn2, $_POST['pilih_pph'] ?? '');
$create_user = pv_esc($conn2, $_POST['create_user'] ?? '');
$create_date = date('Y-m-d H:i:s');
$status = 'Draft';

mysqli_begin_transaction($conn2);

try {
    $query = "INSERT INTO tbl_pv_h (no_pv,pv_date,nama_supp,supp_doc,ctb,pay_date,pay_meth,curr,for_pay, frm_akun,to_akun,ke,dari,no_cek, cek_date,deskripsi,subtotal,adjust,pph,ppn,total,outstanding,per_ppn,per_pph,rate,create_by,create_date,status)
    VALUES
        ('$no_pv', '$pv_date', '$nama_supp', '$sup_doc', '$ctb', '$pay_date', '$pay_mth', '$curr', '$forpay', '$frcc', '$tocc', '$ke', '$dari', '$no_cek', '$cek_date', '$pesan', '$subtotal', '$adjust', '$pph_h', '$ppn_h', '$total', '$total', '$pilih_ppn', '$pilih_pph', '$rat_pv', '$create_user', '$create_date', '$status')";

    if (!mysqli_query($conn2, $query)) {
        throw new Exception('Gagal insert header PV: ' . mysqli_error($conn2));
    }

    foreach ($rowsRaw as $i => $r) {
        $rowNum = $i + 1;

        $no_coa = pv_esc($conn2, $r['no_coa'] ?? '');
        $prof_ctr = pv_esc($conn2, $r['prof_ctr'] ?? '');
        $no_cc = pv_esc($conn2, $r['no_cc'] ?? '');
        $no_ref = pv_esc($conn2, $r['no_ref'] ?? '');
        $deskripsi = pv_esc($conn2, $r['deskripsi'] ?? '');
        $amount = pv_esc($conn2, $r['amount'] ?? 0);
        $due_date = pv_date_or($r['due_date'] ?? '');
        $ded_add = pv_esc($conn2, $r['ded_add'] ?? 0);
        $pph = pv_esc($conn2, $r['pph'] ?? 0);
        $idtax = pv_esc($conn2, $r['idtax'] ?? '');
        $ppn = pv_esc($conn2, $r['ppn'] ?? 0);
        $id_ppn = pv_esc($conn2, $r['id_ppn'] ?? '');
        $ref_date = '1970-01-01';

        if ($amount != '0' || $ded_add != '0') {
            $q = "INSERT INTO tbl_pv (no_pv,coa,no_cc,reff_doc,reff_date,deskripsi,amount,due_date,ded_add,pph,id_pph,ppn,id_ppn,profit_center)
            VALUES
                ('$no_pv', '$no_coa', '$no_cc', '$no_ref', '$ref_date', '$deskripsi', '$amount', '$due_date', '$ded_add', '$pph','$idtax', '$ppn','$id_ppn','$prof_ctr')";
            if (!mysqli_query($conn2, $q)) {
                throw new Exception("Gagal insert baris detail ke-$rowNum: " . mysqli_error($conn2));
            }
        }

        $q = "update memo_h set status='PAYMENT', no_pv='$no_pv' where nm_memo='$no_ref'";
        if (!mysqli_query($conn2, $q)) {
            throw new Exception("Gagal update status memo baris ke-$rowNum: " . mysqli_error($conn2));
        }

        $q = "delete from tbl_pv_memo_temp where no_memo = '$no_ref' and user = '$create_user'";
        if (!mysqli_query($conn2, $q)) {
            throw new Exception("Gagal hapus tbl_pv_memo_temp baris ke-$rowNum: " . mysqli_error($conn2));
        }

        $q = "UPDATE memo_h set no_pv = '$no_pv', status='PAYMENT DRAFT' where nm_memo = '$no_ref'";
        if (!mysqli_query($conn2, $q)) {
            throw new Exception("Gagal update status PAYMENT DRAFT baris ke-$rowNum: " . mysqli_error($conn2));
        }

        $q = "delete from tbl_pv_ftr_temp where no_memo = '$no_ref' and user = '$create_user'";
        if (!mysqli_query($conn2, $q)) {
            throw new Exception("Gagal hapus tbl_pv_ftr_temp baris ke-$rowNum: " . mysqli_error($conn2));
        }

        // $q = "UPDATE memo_h set status='Paid' where no_payment = '$no_ref'";
        // if (!mysqli_query($conn2, $q)) {
        //     throw new Exception("Gagal update status Paid baris ke-$rowNum: " . mysqli_error($conn2));
        // }
    }

    if (!mysqli_query($conn2, "delete from supp_doc_temp")) {
        throw new Exception('Gagal bersihkan supp_doc_temp: ' . mysqli_error($conn2));
    }

    mysqli_commit($conn2);
    echo json_encode(['success' => true, 'no_pv' => $no_pv]);
} catch (Exception $e) {
    mysqli_rollback($conn2);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn2);
