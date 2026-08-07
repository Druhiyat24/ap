<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

function q($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception(mysqli_error($conn));
    }
    return $result;
}

$pv_date =  date("Y-m-d",strtotime($_POST['pv_date']));
$nama_supp = $_POST['nama_supp'];
$sup_doc = $_POST['sup_doc'];
$ctb = $_POST['ctb'];
$pay_date =  date("Y-m-d",strtotime($_POST['pay_date']));
$pay_mth = $_POST['pay_mth'];
$curr = $_POST['curr'];
$forpay = $_POST['forpay'];
$pv_tax_type = $_POST['pv_tax_type'] ?? '';
// Menandai dokumen ini dibuat dari form mana (Regular/EXIM/FTR) - dipakai
// nanti supaya halaman edit tahu template mana yang harus dipakai, karena
// ketiganya beda alur/field walau nulis ke tabel yang sama.
$pv_form_type = $_POST['pv_form_type'] ?? 'Regular';
$frcc = $_POST['frcc'];
$tocc = $_POST['tocc'];
$ke = $_POST['ke'];
$dari = $_POST['dari'];
$no_cek = $_POST['no_cek'];
$cek_date = date("Y-m-d",strtotime($_POST['cek_date']));
$pesan = $_POST['pesan'];
$subtotal = $_POST['subtotal'];
$adjust = $_POST['adjust'];
$pph = $_POST['pph'];
$ppn = $_POST['ppn'];
$pilih_ppn = $_POST['pilih_ppn'];
$pilih_pph = $_POST['pilih_pph'];
$total = $_POST['total'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$status = "Draft";
$rat_pv = $_POST['rat_pv'];

$details = json_decode($_POST['details'] ?? '[]', true) ?: [];

mysqli_begin_transaction($conn2);

try {
    $pv_tax_type_esc = mysqli_real_escape_string($conn2, $pv_tax_type);
    $create_user_esc = mysqli_real_escape_string($conn2, $create_user);

    // Jangan pakai no_pv yang dikirim dari client (dihitung sekali saat
    // halaman dibuka) - kalau halaman sempat dibiarkan terbuka lama sambil
    // dokumen lain dibuat, nomor itu jadi basi dan bisa dobel. Nomor
    // digenerate ulang di sini, di dalam transaksi + FOR UPDATE, tepat saat
    // mau disimpan.
    $bln = date('m', strtotime($pv_date));
    $thn = date('y', strtotime($pv_date));
    $huruf = "PV/NAG/$bln$thn/";

    $sqlNum = q($conn2, "
        SELECT MAX(CAST(RIGHT(no_pv,5) AS UNSIGNED)) AS max_urut
        FROM tbl_pv_h
        WHERE no_pv LIKE '" . mysqli_real_escape_string($conn2, $huruf) . "%'
        FOR UPDATE
    ");
    $rowNum = mysqli_fetch_assoc($sqlNum);
    $urutan = (int) ($rowNum['max_urut'] ?? 0) + 1;
    $no_pv = $huruf . sprintf("%05d", $urutan);

    $pv_form_type_esc = mysqli_real_escape_string($conn2, $pv_form_type);

    q($conn2, "INSERT INTO tbl_pv_h (no_pv,pv_date,nama_supp,supp_doc,ctb,pay_date,pay_meth,curr,for_pay,pv_tax_type,pv_form_type, frm_akun,to_akun,ke,dari,no_cek, cek_date,deskripsi,subtotal,adjust,pph,ppn,total,outstanding,per_ppn,per_pph,rate,create_by,create_date,status)
        VALUES
        ('$no_pv', '$pv_date', '$nama_supp', '$sup_doc', '$ctb', '$pay_date', '$pay_mth', '$curr', '$forpay', '$pv_tax_type_esc', '$pv_form_type_esc', '$frcc', '$tocc', '$ke', '$dari', '$no_cek', '$cek_date', '$pesan', '$subtotal', '$adjust', '$pph', '$ppn', '$total', '$total', '$pilih_ppn', '$pilih_pph', '$rat_pv', '$create_user', '$create_date', '$status')");

    // Detail baris - bulk insert satu statement, bukan satu INSERT per baris
    // (dulu tiap baris kirim request AJAX terpisah ke insertpv.php, jadi
    // kalau salah satu gagal di tengah, sisanya sudah kadung tersimpan
    // tanpa rollback).
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
        $deskripsi = mysqli_real_escape_string($conn2, $d['deskripsi'] ?? '');
        $due_date = date("Y-m-d", strtotime($d['due_date'] ?? ''));
        $d_pph = mysqli_real_escape_string($conn2, $d['pph'] ?? 0);
        $idtax = mysqli_real_escape_string($conn2, $d['idtax'] ?? '');
        $d_ppn = mysqli_real_escape_string($conn2, $d['ppn'] ?? 0);
        $id_ppn = mysqli_real_escape_string($conn2, $d['id_ppn'] ?? '');
        $prof_ctr = mysqli_real_escape_string($conn2, $d['prof_ctr'] ?? '');

        $pvValues[] = "('$no_pv', '$no_coa', '$no_cc', '$no_ref', '$ref_date', '$deskripsi', '$amount', '$due_date', '$ded_add', '$d_pph', '$idtax', '$d_ppn', '$id_ppn', '$prof_ctr')";

        if ($no_ref !== '' && !isset($noRefsSeen[$no_ref])) {
            $noRefsSeen[$no_ref] = true;
        }
    }

    if (!empty($pvValues)) {
        q($conn2, "INSERT INTO tbl_pv (no_pv,coa,no_cc,reff_doc,reff_date,deskripsi,amount,due_date,ded_add,pph,id_pph,ppn,id_ppn,profit_center)
            VALUES " . implode(',', $pvValues));
    }

    // Efek samping per no_ref (Reff Doc) - update/pembersihan memo terkait,
    // sama seperti yang dulu dilakukan insertpv.php per baris.
    foreach (array_keys($noRefsSeen) as $no_ref) {
        q($conn2, "update memo_h set status='PAYMENT', no_pv='$no_pv' where nm_memo='$no_ref'");
        q($conn2, "delete from tbl_pv_memo_temp where no_memo = '$no_ref' and user = '$create_user_esc'");
        q($conn2, "UPDATE memo_h set no_pv = '$no_pv', status='PAYMENT DRAFT' where nm_memo = '$no_ref'");
        q($conn2, "delete from tbl_pv_ftr_temp where no_memo = '$no_ref' and user = '$create_user_esc'");
        // "UPDATE memo_h set status='Paid' where no_payment = '$no_ref'" -
        // memo_h tidak punya kolom no_payment sama sekali, query ini selalu
        // gagal ("Unknown column") dan bikin seluruh transaksi rollback
        // begitu ada baris detail dengan Reff Doc terisi. Baris ini memang
        // sengaja dikomentari juga di save_pv_exim.php (sumber pola ini).
    }

    q($conn2, "delete from supp_doc_temp");

    mysqli_commit($conn2);
    echo $no_pv;
} catch (Exception $e) {
    mysqli_rollback($conn2);
    echo "Error: " . $e->getMessage();
}

mysqli_close($conn2);
