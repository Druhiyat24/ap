<?php
// Endpoint khusus edit-paymentvoucher-exim.php - UPDATE header + replace
// semua baris detail untuk SATU no_pv yang sudah ada. Mirror dari
// save_pv_exim.php (create-mode, selalu INSERT baru), tapi di sini
// meng-UPDATE record yang sudah ada dan no_pv-nya TETAP SAMA.

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
$pv_tax_type = pv_esc($conn2, $_POST['pv_tax_type'] ?? '');
$update_user = pv_esc($conn2, $_POST['create_user'] ?? '');
$update_date = date('Y-m-d H:i:s');

if ($no_pv === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No Payment Voucher tidak valid.']);
    exit;
}

mysqli_begin_transaction($conn2);

try {
    if ($pv_tax_type === '') {
        throw new Exception('Payment Voucher Type wajib diisi.');
    }

    $sqlStatus = mysqli_query($conn2, "select status from tbl_pv_h where no_pv = '$no_pv' FOR UPDATE");
    $rowStatus = mysqli_fetch_assoc($sqlStatus);
    if (!$rowStatus) {
        throw new Exception('Payment Voucher tidak ditemukan.');
    }
    if ($rowStatus['status'] !== 'Draft') {
        throw new Exception('Payment Voucher berstatus "' . $rowStatus['status'] . '" tidak bisa diedit.');
    }

    // Arsipkan versi lama sebelum ditimpa, sama seperti alur edit Regular.
    // Kolom disebutkan eksplisit (bukan SELECT *) karena tbl_edit_pv_h
    // kolomnya sudah tidak sinkron lagi dengan tbl_pv_h (kurang
    // pv_form_type, status_pl, status_pvl) - SELECT * bikin "Column count
    // doesn't match value count". id juga dikecualikan karena auto_increment
    // PRIMARY KEY sendiri di tabel arsip, bukan disalin dari sumbernya.
    if (!mysqli_query($conn2, "INSERT INTO tbl_log_edit_pv (no_pv,user_edit,tgl_edit) VALUES ('$no_pv', '$update_user', '$update_date')")) {
        throw new Exception('Gagal mencatat log edit: ' . mysqli_error($conn2));
    }
    if (!mysqli_query($conn2, "insert into tbl_edit_pv_h (no_pv,pv_date,nama_supp,supp_doc,ctb,pay_date,pay_meth,curr,for_pay,pv_tax_type,frm_akun,to_akun,ke,dari,no_cek,cek_date,deskripsi,subtotal,adjust,pph,ppn,total,outstanding,per_ppn,per_pph,rate,create_by,create_date,status,approve_by,approve_date,cancel_by,cancel_date,update_by,update_date,user_reverse,reverse_date,notes_reverse)
        select no_pv,pv_date,nama_supp,supp_doc,ctb,pay_date,pay_meth,curr,for_pay,pv_tax_type,frm_akun,to_akun,ke,dari,no_cek,cek_date,deskripsi,subtotal,adjust,pph,ppn,total,outstanding,per_ppn,per_pph,rate,create_by,create_date,status,approve_by,approve_date,cancel_by,cancel_date,update_by,update_date,user_reverse,reverse_date,notes_reverse
        from tbl_pv_h where no_pv = '$no_pv'")) {
        throw new Exception('Gagal arsip header lama: ' . mysqli_error($conn2));
    }
    if (!mysqli_query($conn2, "insert into tbl_edit_pv (no_pv,coa,no_cc,reff_doc,reff_date,deskripsi,amount,due_date,ded_add,pph,id_pph,ppn,id_ppn,profit_center)
        select no_pv,coa,no_cc,reff_doc,reff_date,deskripsi,amount,due_date,ded_add,pph,id_pph,ppn,id_ppn,profit_center
        from tbl_pv where no_pv = '$no_pv'")) {
        throw new Exception('Gagal arsip detail lama: ' . mysqli_error($conn2));
    }

    $query = "UPDATE tbl_pv_h SET
        pv_date = '$pv_date',
        nama_supp = '$nama_supp',
        supp_doc = '$sup_doc',
        ctb = '$ctb',
        pay_date = '$pay_date',
        pay_meth = '$pay_mth',
        curr = '$curr',
        for_pay = '$forpay',
        pv_tax_type = '$pv_tax_type',
        frm_akun = '$frcc',
        to_akun = '$tocc',
        ke = '$ke',
        dari = '$dari',
        no_cek = '$no_cek',
        cek_date = '$cek_date',
        deskripsi = '$pesan',
        subtotal = '$subtotal',
        adjust = '$adjust',
        pph = '$pph_h',
        ppn = '$ppn_h',
        total = '$total',
        outstanding = '$total',
        per_ppn = '$pilih_ppn',
        per_pph = '$pilih_pph',
        rate = '$rat_pv',
        update_by = '$update_user',
        update_date = '$update_date'
        WHERE no_pv = '$no_pv'";

    if (!mysqli_query($conn2, $query)) {
        throw new Exception('Gagal update header PV: ' . mysqli_error($conn2));
    }

    if (!mysqli_query($conn2, "delete from tbl_pv where no_pv = '$no_pv'")) {
        throw new Exception('Gagal hapus detail lama: ' . mysqli_error($conn2));
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
            if ($no_coa === '' || $no_coa === '-') {
                throw new Exception("COA pada baris ke-$rowNum wajib diisi.");
            }
            if ($prof_ctr === '' || $prof_ctr === '-') {
                throw new Exception("Profit Center pada baris ke-$rowNum wajib diisi.");
            }

            $sqlWajibCc = mysqli_query($conn1, "select no_coa from mastercoa_v2 where no_coa = '" . pv_esc($conn1, $no_coa) . "'
                and (support_gen_adm = 'Y' OR support_prod = 'Y' OR prod = 'Y' OR support_sell = 'Y')");
            if (mysqli_num_rows($sqlWajibCc) > 0 && ($no_cc === '-' || $no_cc === '')) {
                throw new Exception("COA $no_coa pada baris ke-$rowNum wajib isi Cost Center.");
            }

            $q = "INSERT INTO tbl_pv (no_pv,coa,no_cc,reff_doc,reff_date,deskripsi,amount,due_date,ded_add,pph,id_pph,ppn,id_ppn,profit_center)
            VALUES
                ('$no_pv', '$no_coa', '$no_cc', '$no_ref', '$ref_date', '$deskripsi', '$amount', '$due_date', '$ded_add', '$pph','$idtax', '$ppn','$id_ppn','$prof_ctr')";
            if (!mysqli_query($conn2, $q)) {
                throw new Exception("Gagal insert baris detail ke-$rowNum: " . mysqli_error($conn2));
            }
        }

        if ($no_ref !== '') {
            mysqli_query($conn2, "update memo_h set status='PAYMENT', no_pv='$no_pv' where nm_memo='$no_ref'");
            mysqli_query($conn2, "delete from tbl_pv_memo_temp where no_memo = '$no_ref' and user = '$update_user'");
            mysqli_query($conn2, "UPDATE memo_h set no_pv = '$no_pv', status='PAYMENT DRAFT' where nm_memo = '$no_ref'");
            mysqli_query($conn2, "delete from tbl_pv_ftr_temp where no_memo = '$no_ref' and user = '$update_user'");
        }
    }

    mysqli_commit($conn2);
    echo json_encode(['success' => true, 'no_pv' => $no_pv]);
} catch (Exception $e) {
    mysqli_rollback($conn2);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn2);
