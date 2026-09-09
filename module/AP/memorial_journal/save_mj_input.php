<?php
include '../../../conn/conn.php';
require_once __DIR__ . '/mj_rate_helper.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

mysqli_begin_transaction($conn2);

try {

    /* =========================
   HEADER DATA
========================= */

    $mj_date = date('Y-m-d', strtotime($_POST['mj_date']));

    // CLOSING PERIODE — dicek DI SERVER, bukan cuma di datepicker. Jurnal tidak
    // boleh masuk ke periode yang bukunya sudah ditutup.
    require_once __DIR__ . '/../closing_periode_guard.php';
    $errClose = closing_error($conn2, $mj_date);
    if ($errClose !== '') { throw new Exception($errClose); }
    $mj_type = $_POST['mj_type'];
    $profit_center = $_POST['profit_center'];
    $description = $_POST['pesan'];
    $rate = $_POST['rate_mj'] ?? 1;
    $fil_sb1 = $_POST['fil_sb1'];
    $rate = str_replace(',', '', $rate);


    $bulan = date('m', strtotime($mj_date));
    $tahun = date('y', strtotime($mj_date));
    $status = "Post";
    $user = $_SESSION['username'] ?? 'system';
    $create_date = date("Y-m-d H:i:s");

    $sqlcmj = mysqli_query($conn2, "select nama_cmj from master_category_mj where id_cmj = '$mj_type'");
    $rowcmj = mysqli_fetch_array($sqlcmj);
    $nama_cmj = $rowcmj['nama_cmj'];


    /* =========================
   GENERATE NO JOURNAL
========================= */

    $prefix = "GM/NAG/" . $bulan . $tahun;

    $sql = mysqli_query($conn2, "
SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
FROM tbl_memorial_journal
WHERE no_mj LIKE '$prefix%'
");

    $row = mysqli_fetch_assoc($sql);

    $urutan = ($row['max_urut'] ?? 0) + 1;

    $no_mj = $prefix . "/" . sprintf("%05d", $urutan);


    $sql_sb = mysqli_query($conn2, "
SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS max_urut
FROM sb_memorial_journal
WHERE no_mj LIKE '$prefix%'
");

    $row_sb = mysqli_fetch_assoc($sql_sb);

    $urutan_sb = ($row_sb['max_urut'] ?? 0) + 1;

    $no_mj_sb = $prefix . "/" . sprintf("%05d", $urutan_sb);

    if ($fil_sb1 == '1') {

    mysqli_query($conn2, "
INSERT INTO status_memorial_journal
(
no_mj, mj_date, no_mj_sb, status, create_by, create_date
)
VALUES
('$no_mj', '$mj_date', '$no_mj_sb', 'Post', '$user', '$create_date')
");
    }

    /* =========================*/


    $coa      = $_POST['nomor_coa'];
    $pc       = $_POST['prof_ctr'];
    $cc       = $_POST['nomor_cc'];
    $reff     = $_POST['reff'];
    $reffdate = $_POST['reffdate'];
    // No Faktur / Tgl Faktur: HANYA disimpan ke tbl_list_journal (permintaan
    // user) - TIDAK ke tbl_memorial_journal, sb_memorial_journal, atau
    // sb_list_journal. Kolomnya (faktur_pajak, tgl_faktur_pajak) memang cuma
    // ada di tbl_list_journal.
    $faktur     = $_POST['faktur'] ?? [];
    $fakturdate = $_POST['fakturdate'] ?? [];
    $buyer    = $_POST['buyer'];
    $ws       = $_POST['no_ws'];
    $curr     = $_POST['currenc'];
    $debit    = $_POST['txt_debit'];
    $credit   = $_POST['txt_credit'];
    $ket      = $_POST['keterangan'];

    $totalRow = count($coa);

    for ($i = 0; $i < $totalRow; $i++) {

        if ($coa[$i] == '-' || $coa[$i] == '') {
            continue;
        }

        $coa_i   = $coa[$i];
        $pc_i    = $pc[$i];
        $cc_i    = $cc[$i];
        $reff_i  = $reff[$i];
        $buyer_i = $buyer[$i];
        $ws_i    = $ws[$i];
        $curr_i  = $curr[$i];
        $ket_   = $ket[$i];
        if ($ket_ == '' || $ket_ == null) {
            $ket_i = $description;
        }else{
            $ket_i = $ket_;
        }

        $debit_i  = floatval($debit[$i]);
        $credit_i = floatval($credit[$i]);

        $reffdate_i = '';

        if (!empty($reffdate[$i])) {
            $reffdate_i = date('Y-m-d', strtotime($reffdate[$i]));
        }

        // No Faktur/Tgl Faktur baris ini - null-safe, mengikuti pola berkas
        // upload (strip "-"/kosong -> NULL, bukan '0000-00-00').
        $faktur_i = mysqli_real_escape_string($conn2, trim((string) ($faktur[$i] ?? '')));
        $fakturdate_raw = trim((string) ($fakturdate[$i] ?? ''));
        $fakturdate_sql = 'NULL';
        if ($fakturdate_raw !== '' && $fakturdate_raw !== '-') {
            $ts_fak = strtotime($fakturdate_raw);
            if ($ts_fak) { $fakturdate_sql = "'" . date('Y-m-d', $ts_fak) . "'"; }
        }


        $sqlcoa = mysqli_query($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$coa_i'");
        $rowcoa = mysqli_fetch_array($sqlcoa);
        $nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

        $sqlcc = mysqli_query($conn2, "select cc_name from b_master_cc where no_cc = '$cc_i'");
        $rowcc = mysqli_fetch_array($sqlcc);
        $nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;



        /* =========================
       HITUNG EQV IDR
    ========================== */

        // KURS: diambil dari TANGGAL JURNAL memakai kurs PAJAK untuk mata uang
        // BARIS INI. Sebelumnya dipakai $rate tunggal dari $_POST['rate_mj'],
        // yang diisi getRate() dengan lookup USD saja — sehingga baris ber-mata-uang
        // lain (mis. EUR) ikut dikalikan kurs USD. Nilai kiriman tetap dihormati
        // $_POST['rate_mj'] SENGAJA TIDAK dioper sebagai override: isinya bukan kurs
        // ketikan manual, melainkan hasil lookup USD otomatis milik getRate(). Kalau
        // dioper, baris EUR akan tetap memakai kurs USD — persis bug yang diperbaiki.
        // Lihat mj_rate_helper.php.
        $rate_det   = mj_resolve_rate($conn2, $curr_i, $mj_date, null);
        $debit_idr  = $debit_i * $rate_det;
        $credit_idr = $credit_i * $rate_det;


        mysqli_query($conn2, "
INSERT INTO tbl_memorial_journal
(
no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center
)
VALUES
('$no_mj', '$mj_date', '$mj_type', '$coa_i', '$cc_i', '$reff_i', '$reffdate_i', '$buyer_i', '$ws_i', '$curr_i', '$rate_det', '$debit_i', '$credit_i', '$debit_idr', '$credit_idr', '$ket_i', '$status', '$user', '$create_date', '$pc_i')
");


        mysqli_query($conn2, "
INSERT INTO tbl_list_journal
(
no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center
)
VALUES
('$no_mj', '$mj_date', '$nama_cmj', '$coa_i', '$nama_coa', '$cc_i', '$nama_cc', '$reff_i', '$reffdate_i', '$faktur_i', $fakturdate_sql, '$buyer_i', '$ws_i', '$curr_i', '$rate_det', '$debit_i', '$credit_i', '$debit_idr', '$credit_idr', '$status', '$ket_i', '$user', '$create_date', '', '', '', '', '$pc_i')
");

        if ($fil_sb1 == '1') {

            mysqli_query($conn2, "
INSERT INTO sb_memorial_journal
(
no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, asal_data, profit_center
)
VALUES
('$no_mj_sb', '$mj_date', '$mj_type', '$coa_i', '$cc_i', '$reff_i', '$reffdate_i', '$buyer_i', '$ws_i', '$curr_i', '$rate_det', '$debit_i', '$credit_i', '$debit_idr', '$credit_idr', '$ket_i', '$status', '$user', '$create_date', 'Input SB2', '$pc_i')
");


   mysqli_query($conn2, "
INSERT INTO sb_list_journal
(
no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center
)
VALUES
('$no_mj_sb', '$mj_date', '$nama_cmj', '$coa_i', '$nama_coa', '$cc_i', '$nama_cc', '$reff_i', '$reffdate_i', '$buyer_i', '$ws_i', '$curr_i', '$rate_det', '$debit_i', '$credit_i', '$debit_idr', '$credit_idr', '$status', '$ket_i', '$user', '$create_date', '', '', '', '', '$pc_i')
");


        }
    }


    /* =========================
   COMMIT
========================= */

    mysqli_commit($conn2);

    echo json_encode([
        'status' => 'success',
        'no_journal' => $no_mj
    ]);
} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
