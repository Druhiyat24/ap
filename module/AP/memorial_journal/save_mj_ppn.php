<?php
// ============================================================================
// save_mj_ppn.php — SIMPAN jurnal tab "PPN Masukan" (1 upload = 1 nomor GM).
//
// Baris jurnal dibentuk ppn_build_lines() (helper yang sama dgn preview):
//   DEBIT  2.52.03 rinci per faktur  |  CREDIT 1.52.04 SUM per supplier+faktur
//   baris retur (PPN negatif) sudah dibalik sisinya di helper.
// Tersimpan ke tbl_memorial_journal (GM) + tbl_list_journal (jurnal), lalu baris
// data full di tbl_ppn_masukan_upload ditandai status='Post' + diisi no_mj.
// Semua dalam 1 transaksi InnoDB (all-or-nothing). Respons JSON murni.
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../../conn/conn.php';
require_once __DIR__ . '/ppn_masukan_lines.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
ini_set('max_execution_time', 0);   // ribuan baris: jangan sampai timeout di tengah
ini_set('memory_limit', '1024M');
header('Content-Type: application/json; charset=utf-8');

function ppn_save_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); }

$user        = $_SESSION['username'] ?? 'system';
$create_date = date('Y-m-d H:i:s');
$e           = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) ($v ?? '')); };

$mj_date  = !empty($_POST['mj_date'])  ? date('Y-m-d', strtotime($_POST['mj_date'])) : date('Y-m-d');
// Type DIPAKSA ke kategori VAT — jurnal PPN Masukan selalu masuk kategori itu.
// Nilai kiriman form sengaja DIABAIKAN kalau VAT ada di master: dropdown yang
// dikunci di halaman gampang ditembus (POST langsung / hapus atribut disabled),
// jadi yang menentukan harus server. Kalau kategori VAT belum ada (migrasi belum
// dijalankan di server ini), pakai kiriman form seperti sebelumnya.
$id_cmj   = trim($_POST['id_cmj']   ?? '');
$qVatSv   = mysqli_query($conn1, "select id_cmj from master_category_mj where UPPER(TRIM(nama_cmj)) = 'VAT' order by id_cmj limit 1");
$rVatSv   = $qVatSv ? mysqli_fetch_assoc($qVatSv) : null;
if ($rVatSv && !empty($rVatSv['id_cmj'])) { $id_cmj = $rVatSv['id_cmj']; }
// Profit Center TIDAK lagi diambil dari form: sudah tersimpan per baris di
// staging (kolom terakhir file upload), dan satu upload boleh beda-beda PC.
// Ceklis "Include" -> jurnal kembar dibentuk juga di SB I (sama spt tab Manual/Upload).
$to_sb1   = (trim($_POST['to_sb1'] ?? '0') === '1');

if ($id_cmj === '') { ppn_save_out(['status' => 'error', 'message' => 'Type has not been selected.']); exit; }

// CLOSING PERIODE — dicek DI SERVER, bukan cuma startDate datepicker. Batas di
// UI gampang dilewati (POST langsung / ubah value lewat console), sedangkan
// jurnal yang masuk ke periode tertutup merusak buku yang sudah ditutup.
require_once __DIR__ . '/../closing_periode_guard.php';
$errClose = closing_error($conn2, $mj_date);
if ($errClose !== '') { ppn_save_out(['status' => 'error', 'message' => $errClose]); exit; }

$res = ppn_build_lines($conn2, $user, 'Temp');
if ($res === false || empty($res['detail'])) {
    ppn_save_out(['status' => 'error', 'message' => 'There is no uploaded data to save.']); exit;
}
if (!$res['balance']) {
    ppn_save_out(['status' => 'error', 'message' => 'Journal is not balanced (D ' . $res['tot_debit'] . ' vs C ' . $res['tot_credit'] . '). Cancelled.']); exit;
}

// Nama kategori MJ -> dipakai sbg type_journal di tbl_list_journal.
$rc = mysqli_fetch_assoc(mysqli_query($conn1, "select nama_cmj from master_category_mj where id_cmj = '" . mysqli_real_escape_string($conn1, $id_cmj) . "' limit 1"));
$nama_cmj = $rc['nama_cmj'] ?? 'OTHERS';

mysqli_begin_transaction($conn2);
$GLOBALS['__ppn_committed'] = false;
register_shutdown_function(function () use ($conn2) {
    if (empty($GLOBALS['__ppn_committed'])) { @mysqli_rollback($conn2); }
});

try {
    // ---- Nomor GM: GM/NAG/<mm><yy>/<00001> (prefix NAG, konsisten data lama) --
    $prefix = 'GM/NAG/' . date('m', strtotime($mj_date)) . date('y', strtotime($mj_date));
    $rm = mysqli_fetch_assoc(mysqli_query($conn2,
        "SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS mx FROM tbl_memorial_journal WHERE no_mj LIKE '" . $e($prefix) . "%'"));
    $no_mj = $prefix . '/' . sprintf('%05d', ((int) ($rm['mx'] ?? 0)) + 1);

    // ---- SB I (opsional, dari ceklis "Include") -----------------------------
    // Nomornya urut sendiri di sb_memorial_journal, lalu dipetakan ke no_mj utama
    // lewat status_memorial_journal — persis alur tab Manual / Upload Journal.
    $no_mj_sb = '';
    if ($to_sb1) {
        $rs = mysqli_fetch_assoc(mysqli_query($conn2,
            "SELECT MAX(CAST(RIGHT(no_mj,5) AS UNSIGNED)) AS mx FROM sb_memorial_journal WHERE no_mj LIKE '" . $e($prefix) . "%'"));
        $no_mj_sb = $prefix . '/' . sprintf('%05d', ((int) ($rs['mx'] ?? 0)) + 1);

        $qst = "INSERT INTO status_memorial_journal (no_mj, mj_date, no_mj_sb, status, create_by, create_date)
                VALUES ('" . $e($no_mj) . "', '" . $e($mj_date) . "', '" . $e($no_mj_sb) . "', 'Post', '" . $e($user) . "', '" . $e($create_date) . "')";
        if (mysqli_query($conn2, $qst) === false) { throw new Exception('Failed to save SB I status: ' . mysqli_error($conn2)); }
    }

    // ---- Tulis semua baris jurnal (detail 2.52.03 + grouped 1.52.04) --------
    // BULK: file bisa ribuan faktur -> 1 INSERT per baris (2-4 query/baris) terlalu
    // lambat & rawan timeout. Nilai dikumpulkan lalu dikirim MULTI-ROW per $CHUNK
    // baris. Tetap di dalam 1 transaksi, jadi gagal di tengah = rollback total dan
    // user tinggal klik Save lagi (staging masih 'Temp', belum dikonsumsi).
    $rows  = $res['lines'];   // urutan: debit lalu credit pasangannya (dari helper)
    $CHUNK = 500;

    $HEAD_MJ = "INSERT INTO tbl_memorial_journal
        (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate,
         debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center) VALUES ";
    $HEAD_JR = "INSERT INTO tbl_list_journal
        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
         reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, supplier, buyer, no_ws, curr, rate,
         debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
         approve_by, approve_date, cancel_by, cancel_date, profit_center) VALUES ";
    $HEAD_SMJ = "INSERT INTO sb_memorial_journal
        (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate,
         debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, asal_data, profit_center) VALUES ";
    $HEAD_SJR = "INSERT INTO sb_list_journal
        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
         reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr,
         status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) VALUES ";

    $bufMj = []; $bufJr = []; $bufSmj = []; $bufSjr = [];
    $flush = function (&$buf, $head, $label) use ($conn2) {
        if (empty($buf)) { return; }
        $ok = mysqli_query($conn2, $head . implode(',', $buf));
        $n = count($buf);
        $buf = [];
        if ($ok === false) { throw new Exception('Failed to save ' . $label . ' (' . $n . ' rows): ' . mysqli_error($conn2)); }
    };

    foreach ($rows as $r) {
        $ket = $r['keterangan'];   // otomatis "PPN - <SUPPLIER>" dari helper
        // PC selalu ikut baris — sudah divalidasi tidak boleh kosong saat upload.
        $pc_r = $r['profit_center'];
        $reffdt = !empty($r['tgl_faktur']) ? "'" . $e($r['tgl_faktur']) . "'" : "NULL";
        $tgl_fk = $reffdt;
        $deb = (float) $r['debit']; $cre = (float) $r['credit'];

        // Potongan yang dipakai berulang -> di-escape sekali saja per baris.
        $coa = $e($r['no_coa']); $nmcoa = $e($r['nama_coa']); $fk = $e($r['no_faktur']);
        $sup = $e($r['supplier']); $ketE = $e($ket); $pcE = $e($pc_r);

        $bufMj[] = "('" . $e($no_mj) . "','" . $e($mj_date) . "','" . $e($id_cmj) . "','$coa','-',
            '$fk',$reffdt,'-','-','IDR',1,$deb,$cre,$deb,$cre,'$ketE','Post','" . $e($user) . "','" . $e($create_date) . "','$pcE')";

        $bufJr[] = "('" . $e($no_mj) . "','" . $e($mj_date) . "','" . $e($nama_cmj) . "','$coa','$nmcoa','-','-',
            '$fk',$reffdt,'$fk',$tgl_fk,'$sup','-','-','IDR',1,$deb,$cre,$deb,$cre,'Post','$ketE',
            '" . $e($user) . "','" . $e($create_date) . "','','','','','$pcE')";

        // Kembaran di SB I (kolom sb_* tidak punya faktur_pajak/supplier -> tidak diisi).
        if ($to_sb1) {
            $bufSmj[] = "('" . $e($no_mj_sb) . "','" . $e($mj_date) . "','" . $e($id_cmj) . "','$coa','-',
                '$fk',$reffdt,'-','-','IDR',1,$deb,$cre,$deb,$cre,'$ketE','Post','" . $e($user) . "','" . $e($create_date) . "','Upload PPN Masukan','$pcE')";

            $bufSjr[] = "('" . $e($no_mj_sb) . "','" . $e($mj_date) . "','" . $e($nama_cmj) . "','$coa','$nmcoa','-','-',
                '$fk',$reffdt,'-','-','IDR',1,$deb,$cre,$deb,$cre,'Post','$ketE',
                '" . $e($user) . "','" . $e($create_date) . "','','','','','$pcE')";
        }

        if (count($bufMj) >= $CHUNK) {
            $flush($bufMj, $HEAD_MJ, 'GM');
            $flush($bufJr, $HEAD_JR, 'journal');
            if ($to_sb1) { $flush($bufSmj, $HEAD_SMJ, 'SB I (GM)'); $flush($bufSjr, $HEAD_SJR, 'SB I (journal)'); }
        }
    }
    // Sisa buffer terakhir.
    $flush($bufMj, $HEAD_MJ, 'GM');
    $flush($bufJr, $HEAD_JR, 'journal');
    if ($to_sb1) { $flush($bufSmj, $HEAD_SMJ, 'SB I (GM)'); $flush($bufSjr, $HEAD_SJR, 'SB I (journal)'); }

    // ---- Tandai data full: Temp -> Post + link ke nomor GM ------------------
    $up = "UPDATE tbl_ppn_masukan_upload SET status = 'Post', no_mj = '" . $e($no_mj) . "', mj_date = '" . $e($mj_date) . "'
           WHERE create_by = '" . $e($user) . "' AND status = 'Temp'";
    if (mysqli_query($conn2, $up) === false) { throw new Exception('Failed to update uploaded data: ' . mysqli_error($conn2)); }

    if (!mysqli_commit($conn2)) { throw new Exception('Commit failed.'); }
    $GLOBALS['__ppn_committed'] = true;

    ppn_save_out(['status' => 'success', 'no_mj' => $no_mj, 'no_mj_sb' => $no_mj_sb,
        'baris' => count($rows), 'tot_debit' => $res['tot_debit'], 'tot_credit' => $res['tot_credit']]);

} catch (Exception $ex) {
    mysqli_rollback($conn2);
    ppn_save_out(['status' => 'error', 'message' => $ex->getMessage() . ' Nothing was saved.']);
}
