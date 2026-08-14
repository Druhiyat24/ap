<?php
// Endpoint khusus edit-paymentvoucher.php - UPDATE header + replace semua
// baris detail untuk SATU no_pv yang sudah ada (bukan bikin baris baru
// seperti insertpv_h.php). Sengaja dipisah dari insertpv_h.php karena
// insertpv_h.php selalu generate no_pv baru di dalam transaksinya sendiri -
// tidak cocok dipakai untuk edit dokumen yang no_pv-nya sudah tetap.
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

function q($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception(mysqli_error($conn));
    }
    return $result;
}

$no_pv = $_POST['no_pv'] ?? '';
$pv_date = date("Y-m-d", strtotime($_POST['pv_date']));
$nama_supp = $_POST['nama_supp'];
$sup_doc = $_POST['sup_doc'];
$ctb = $_POST['ctb'];
$pay_date = date("Y-m-d", strtotime($_POST['pay_date']));
$pay_mth = $_POST['pay_mth'];
$curr = $_POST['curr'];
$forpay = $_POST['forpay'];
$id_cash_flow = !empty($_POST['id_cash_flow']) ? (int) $_POST['id_cash_flow'] : null;
$pv_tax_type = $_POST['pv_tax_type'] ?? '';
$frcc = $_POST['frcc'];
$tocc = $_POST['tocc'];
$ke = $_POST['ke'];
$dari = $_POST['dari'];
$no_cek = $_POST['no_cek'];
$cek_date = date("Y-m-d", strtotime($_POST['cek_date']));
$pesan = $_POST['pesan'];
$subtotal = $_POST['subtotal'];
$adjust = $_POST['adjust'];
$pph = $_POST['pph'];
$ppn = $_POST['ppn'];
$pilih_ppn = $_POST['pilih_ppn'];
$pilih_pph = $_POST['pilih_pph'];
$total = $_POST['total'];
$update_user = $_POST['create_user'] ?? '';
$update_date = date("Y-m-d H:i:s");
$rat_pv = $_POST['rat_pv'];

$details = json_decode($_POST['details'] ?? '[]', true) ?: [];

if ($no_pv === '') {
    echo 'Error: No Payment Voucher tidak valid.';
    exit;
}

mysqli_begin_transaction($conn2);

try {
    $no_pv_esc = mysqli_real_escape_string($conn2, $no_pv);
    $update_user_esc = mysqli_real_escape_string($conn2, $update_user);

    // Hanya PV berstatus Draft yang boleh diedit - sama seperti aturan lama
    // di copy_data.php, supaya dokumen yang sudah Approved/Cancel/Closed
    // tidak bisa diam-diam berubah lewat form edit.
    $sqlStatus = q($conn2, "select status from tbl_pv_h where no_pv = '$no_pv_esc' FOR UPDATE");
    $rowStatus = mysqli_fetch_assoc($sqlStatus);
    if (!$rowStatus) {
        throw new Exception('Payment Voucher tidak ditemukan.');
    }
    if ($rowStatus['status'] !== 'Draft') {
        throw new Exception('Payment Voucher berstatus "' . $rowStatus['status'] . '" tidak bisa diedit.');
    }

    // Arsipkan versi lama sebelum ditimpa - riwayat edit sebelumnya juga
    // disimpan lewat cara ini (lihat copy_data.php). Kolom disebutkan
    // eksplisit (bukan SELECT *) karena tbl_edit_pv_h ternyata kolomnya
    // sudah tidak sinkron lagi dengan tbl_pv_h (kurang pv_form_type,
    // status_pl, status_pvl) - SELECT * bikin "Column count doesn't match
    // value count". id juga sengaja dikecualikan karena auto_increment
    // PRIMARY KEY sendiri di tabel arsip, bukan disalin dari sumbernya.
    q($conn2, "INSERT INTO tbl_log_edit_pv (no_pv,user_edit,tgl_edit) VALUES ('$no_pv_esc', '$update_user_esc', '$update_date')");
    q($conn2, "insert into tbl_edit_pv_h (no_pv,pv_date,nama_supp,supp_doc,ctb,pay_date,pay_meth,curr,for_pay,pv_tax_type,frm_akun,to_akun,ke,dari,no_cek,cek_date,deskripsi,subtotal,adjust,pph,ppn,total,outstanding,per_ppn,per_pph,rate,create_by,create_date,status,approve_by,approve_date,cancel_by,cancel_date,update_by,update_date,user_reverse,reverse_date,notes_reverse)
        select no_pv,pv_date,nama_supp,supp_doc,ctb,pay_date,pay_meth,curr,for_pay,pv_tax_type,frm_akun,to_akun,ke,dari,no_cek,cek_date,deskripsi,subtotal,adjust,pph,ppn,total,outstanding,per_ppn,per_pph,rate,create_by,create_date,status,approve_by,approve_date,cancel_by,cancel_date,update_by,update_date,user_reverse,reverse_date,notes_reverse
        from tbl_pv_h where no_pv = '$no_pv_esc'");
    q($conn2, "insert into tbl_edit_pv (no_pv,coa,no_cc,reff_doc,reff_date,faktur_pajak,tgl_faktur_pajak,deskripsi,amount,due_date,ded_add,pph,id_pph,ppn,id_ppn,profit_center)
        select no_pv,coa,no_cc,reff_doc,reff_date,faktur_pajak,tgl_faktur_pajak,deskripsi,amount,due_date,ded_add,pph,id_pph,ppn,id_ppn,profit_center
        from tbl_pv where no_pv = '$no_pv_esc'");

    $pv_tax_type_esc = mysqli_real_escape_string($conn2, $pv_tax_type);
    $id_cash_flow_sql = $id_cash_flow !== null ? $id_cash_flow : 'NULL';

    q($conn2, "UPDATE tbl_pv_h SET
        pv_date = '$pv_date',
        nama_supp = '$nama_supp',
        supp_doc = '$sup_doc',
        ctb = '$ctb',
        pay_date = '$pay_date',
        pay_meth = '$pay_mth',
        curr = '$curr',
        for_pay = '$forpay',
        id_cash_flow = $id_cash_flow_sql,
        pv_tax_type = '$pv_tax_type_esc',
        frm_akun = '$frcc',
        to_akun = '$tocc',
        ke = '$ke',
        dari = '$dari',
        no_cek = '$no_cek',
        cek_date = '$cek_date',
        deskripsi = '$pesan',
        subtotal = '$subtotal',
        adjust = '$adjust',
        pph = '$pph',
        ppn = '$ppn',
        total = '$total',
        outstanding = '$total',
        per_ppn = '$pilih_ppn',
        per_pph = '$pilih_pph',
        rate = '$rat_pv',
        update_by = '$update_user_esc',
        update_date = '$update_date'
        WHERE no_pv = '$no_pv_esc'");

    // Baris detail lama dibuang semua, diganti baris yang dikirim sekarang -
    // lebih sederhana & aman daripada coba diff/match baris satu-satu.
    q($conn2, "delete from tbl_pv where no_pv = '$no_pv_esc'");

    $pvValues = [];
    $noRefsSeen = [];

    foreach ($details as $d) {
        $amount = (float) ($d['amount'] ?? 0);
        $ded_add = (float) ($d['ded_add'] ?? 0);

        if ($amount == 0 && $ded_add == 0) {
            continue;
        }

        $no_coa = mysqli_real_escape_string($conn2, $d['no_coa'] ?? '');
        $no_cc = mysqli_real_escape_string($conn2, $d['no_cc'] ?? '');
        $prof_ctr_check = trim($d['prof_ctr'] ?? '');

        if ($no_coa === '' || $no_coa === '-') {
            throw new Exception('COA wajib diisi.');
        }
        if ($prof_ctr_check === '' || $prof_ctr_check === '-') {
            throw new Exception('Profit Center wajib diisi.');
        }

        $sqlWajibCc = q($conn1, "select no_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn1, $no_coa) . "'
            and (support_gen_adm = 'Y' OR support_prod = 'Y' OR prod = 'Y' OR support_sell = 'Y')");
        if (mysqli_num_rows($sqlWajibCc) > 0 && ($no_cc === '-' || $no_cc === '')) {
            throw new Exception('COA ' . $no_coa . ' wajib isi Cost Center.');
        }

        $no_ref = mysqli_real_escape_string($conn2, $d['no_ref'] ?? '');
        $ref_date = date("Y-m-d", strtotime($d['ref_date'] ?? ''));
        $faktur_pajak = trim($d['faktur_pajak'] ?? '');
        // Wajib diisi (tidak boleh kosong/strip) khusus untuk COA 1.52.04.
        if ($no_coa === '1.52.04' && ($faktur_pajak === '' || $faktur_pajak === '-')) {
            throw new Exception('COA 1.52.04 wajib isi Faktur Pajak.');
        }
        $faktur_pajak = mysqli_real_escape_string($conn2, $faktur_pajak);
        $tgl_faktur_pajak_raw = trim($d['tgl_faktur_pajak'] ?? '');
        if ($no_coa === '1.52.04' && $tgl_faktur_pajak_raw === '') {
            throw new Exception('COA 1.52.04 wajib isi Tgl Faktur Pajak.');
        }
        $tgl_faktur_pajak = $tgl_faktur_pajak_raw !== '' ? "'" . date("Y-m-d", strtotime($tgl_faktur_pajak_raw)) . "'" : 'NULL';
        $deskripsi = mysqli_real_escape_string($conn2, $d['deskripsi'] ?? '');
        $due_date = date("Y-m-d", strtotime($d['due_date'] ?? ''));
        $d_pph = mysqli_real_escape_string($conn2, $d['pph'] ?? 0);
        $idtax = mysqli_real_escape_string($conn2, $d['idtax'] ?? '');
        $d_ppn = mysqli_real_escape_string($conn2, $d['ppn'] ?? 0);
        $id_ppn = mysqli_real_escape_string($conn2, $d['id_ppn'] ?? '');
        $prof_ctr = mysqli_real_escape_string($conn2, $d['prof_ctr'] ?? '');

        $pvValues[] = "('$no_pv_esc', '$no_coa', '$no_cc', '$no_ref', '$ref_date', '$faktur_pajak', $tgl_faktur_pajak, '$deskripsi', '$amount', '$due_date', '$ded_add', '$d_pph', '$idtax', '$d_ppn', '$id_ppn', '$prof_ctr')";

        if ($no_ref !== '' && !isset($noRefsSeen[$no_ref])) {
            $noRefsSeen[$no_ref] = true;
        }
    }

    if (empty($pvValues)) {
        throw new Exception('Minimal harus ada satu baris detail.');
    }

    q($conn2, "INSERT INTO tbl_pv (no_pv,coa,no_cc,reff_doc,reff_date,faktur_pajak,tgl_faktur_pajak,deskripsi,amount,due_date,ded_add,pph,id_pph,ppn,id_ppn,profit_center)
        VALUES " . implode(',', $pvValues));

    foreach (array_keys($noRefsSeen) as $no_ref) {
        q($conn2, "update memo_h set status='PAYMENT', no_pv='$no_pv_esc' where nm_memo='$no_ref'");
        q($conn2, "delete from tbl_pv_memo_temp where no_memo = '$no_ref' and user = '$update_user_esc'");
        q($conn2, "UPDATE memo_h set no_pv = '$no_pv_esc', status='PAYMENT DRAFT' where nm_memo = '$no_ref'");
        q($conn2, "delete from tbl_pv_ftr_temp where no_memo = '$no_ref' and user = '$update_user_esc'");
        // "UPDATE memo_h set status='Paid' where no_payment = '$no_ref'" -
        // memo_h tidak punya kolom no_payment sama sekali, query ini selalu
        // gagal ("Unknown column") dan bikin seluruh transaksi rollback
        // begitu ada baris detail dengan Reff Doc terisi.
    }

    mysqli_commit($conn2);
    echo $no_pv;
} catch (Exception $e) {
    mysqli_rollback($conn2);
    echo "Error: " . $e->getMessage();
}

mysqli_close($conn2);
