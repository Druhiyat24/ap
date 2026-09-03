<?php
// ============================================================================
// insertkbon_bulk_edit.php — SIMPAN EDIT PV Regular sebagai REVISI (-REV_NN)
// dalam SATU transaksi InnoDB (all-or-nothing), respons JSON murni.
//
// Alur (mirror insert_kontrabon_edit_all.php, TAPI baca dari PAYLOAD form —
// header + items — bukan dari tabel staging ap_edit_*):
//   1) hitung nomor revisi  = "<old>-REV_NN"  (NN = MAX suffix + 1)
//   2) jurnal_balik         = reverse SEMUA jurnal PV lama (debit<->credit) di
//                             bawah no_journal = <old> (self-balancing)
//   3) tandai baris PV lama status='Updated' (kontrabon_h/kontrabon/kontrabon_ftr/
//                             return_kb/potongan)
//   4) INSERT revisi memakai LOGIKA & FORMULA JURNAL yg IDENTIK dgn create
//      (insertkbon_core.php) + edit-all -> header journals + per-BPB + per-RO +
//      kontrabon/return_kb/potongan rows + faktur ke bpb_new/bppb_new.
//
// Karena formula jurnal identik create, jurnal revisi (no_journal = <rev>) PASTI
// balance: Sum(debit_idr) = Sum(credit_idr) untuk type LIKE 'AP - Kontrabon%'.
//
// Payload (POST):
//   edit_no_kbon = string  (nomor PV lama)
//   header       = JSON object (field SAMA dgn headerData create/edit)
//   items        = JSON array  (tiap elemen field SAMA dgn item create/edit)
// Respons (JSON): {ok:true, no_kbon:<rev>} | {ok:false, msg, failed?}
// ============================================================================
// API: respons WAJIB JSON murni — matikan display_errors + ob_start supaya notice
// PHP tak ikut tercetak (kalau ikut, jQuery dataType:'json' gagal parse).
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../conn/conn.php';
require_once __DIR__ . '/bpb_docinfo_guard.php';
date_default_timezone_set('Asia/Jakarta');
ini_set('date.timezone', 'Asia/Jakarta');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');
header('Content-Type: application/json; charset=utf-8');

function pv_edit_out($arr) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($arr); }

$edit_no_kbon = isset($_POST['edit_no_kbon']) ? trim($_POST['edit_no_kbon']) : '';
$headerData   = json_decode($_POST['header'] ?? '', true);
$items        = json_decode($_POST['items'] ?? '[]', true);

if ($edit_no_kbon === '')                                { pv_edit_out(['ok' => false, 'msg' => 'edit_no_kbon (nomor PV lama) kosong.']); exit; }
if (!is_array($headerData) || empty($headerData))        { pv_edit_out(['ok' => false, 'msg' => 'Payload header tidak valid.']); exit; }
if (!is_array($items))                                   { $items = []; }

// Nilai uang bisa kosong / berkoma -> jadikan float dulu (hindari non-numeric PHP8).
$numf = function ($v) { return (float) str_replace(',', '', (string) ($v ?? 0)); };
$e    = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) ($v ?? '')); };

// Safety net: kalau script mati (fatal/timeout) SEBELUM commit -> rollback.
$GLOBALS['__pv_edit_committed'] = false;
register_shutdown_function(function () use ($conn2) {
    if (empty($GLOBALS['__pv_edit_committed'])) { @mysqli_rollback($conn2); }
});

// ----------------------------------------------------------------------------
// Helper: INSERT 1 baris jurnal 'AP - Kontrabon' (kolom & konstanta SAMA persis
// dgn create/edit-all). Dipakai untuk SEMUA baris jurnal revisi.
// ----------------------------------------------------------------------------
function pvj($conn2, $kode, $create_date, $no_coa, $nama_coa, $no_cc, $nama_cc,
             $reff_doc, $reff_date, $curr, $rate, $debit, $credit, $debit_idr, $credit_idr,
             $keter, $create_user, $profit_center) {
    $esc = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
    if ($no_cc === '' || $no_cc === null)     { $no_cc = '-'; }
    if ($nama_cc === '' || $nama_cc === null) { $nama_cc = '-'; }
    $sql = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
        VALUES
        ('" . $esc($kode) . "', '" . $esc($create_date) . "', 'AP - Kontrabon', '" . $esc($no_coa) . "', '" . $esc($nama_coa) . "', '" . $esc($no_cc) . "', '" . $esc($nama_cc) . "', '" . $esc($reff_doc) . "', '" . $esc($reff_date) . "', '-', '-', '" . $esc($curr) . "', '" . $esc($rate) . "', '" . $esc($debit) . "', '" . $esc($credit) . "', '" . $esc($debit_idr) . "', '" . $esc($credit_idr) . "', 'Draft', '" . $esc($keter) . "', '" . $esc($create_user) . "', '" . $esc($create_date) . "', '', '', '', '', '" . $esc($profit_center) . "')";
    return mysqli_query($conn2, $sql);
}

mysqli_begin_transaction($conn2);

// ---- 0) HEADER dari payload -------------------------------------------------
$no_kbon_h    = $edit_no_kbon;                      // PV lama (authoritative)
$tgl_kbon_h   = date("Y-m-d", strtotime($headerData['tgl_kbon_h'] ?? 'now'));
$tgl_kbon_s   = date("Y-m-d", strtotime($headerData['tgl_kbon_s'] ?? ($headerData['tgl_kbon_h'] ?? 'now')));
$no_po_h      = $headerData['no_po_h'] ?? '';
$nama_supp_h  = $headerData['nama_supp_h'] ?? '';
$no_faktur_h  = $headerData['no_faktur_h'] ?? '';
$supp_inv_h   = $headerData['supp_inv_h'] ?? '';
$tgl_inv_h    = date("Y-m-d", strtotime($headerData['tgl_inv_h'] ?? 'now'));
$tgl_tempo_h  = date("Y-m-d", strtotime($headerData['tgl_tempo_h'] ?? 'now'));
$pph_h        = $numf($headerData['pph_h'] ?? 0);
$curr_h       = $headerData['curr_h'] ?? 'IDR';
if ($curr_h === '') { $curr_h = 'IDR'; }
$create_date  = date("Y-m-d H:i:s");
$status       = 'draft';
$create_user_h = $headerData['create_user_h'] ?? '';
$sub_h        = $numf($headerData['sub_h']      ?? 0);
$tax_h        = $numf($headerData['tax_h']      ?? 0);
$dp_h         = $numf($headerData['dp_h']       ?? 0);
$total_h      = $numf($headerData['total_h']    ?? 0);
$balance      = $total_h;
$jml_return   = $numf($headerData['jml_return'] ?? 0);
$lr_kurs1     = $numf($headerData['lr_kurs']    ?? 0);
$s_qty1       = $numf($headerData['s_qty']      ?? 0);
$s_harga1     = $numf($headerData['s_harga']    ?? 0);
$materai1     = $numf($headerData['materai']    ?? 0);
$pot_beli1    = $numf($headerData['pot_beli']   ?? 0);
$ekspedisi1   = $numf($headerData['ekspedisi']  ?? 0);
$moq1         = $numf($headerData['moq']        ?? 0);
$jml_potong1  = $numf($headerData['jml_potong'] ?? 0);
$potongan_ppn = $numf($headerData['potongan_ppn'] ?? 0);
$potongan_pph = $numf($headerData['potongan_pph'] ?? 0);
$mattype        = $headerData['mattype'] ?? '';
$matclass       = $headerData['matclass'] ?? '';
$n_code_category = $headerData['n_code_category'] ?? '';
$cus_ctg        = $headerData['cus_ctg'] ?? '';
$profit_center  = $headerData['profit_center'] ?? '';
$ir_number      = $headerData['ir_number'] ?? '';
$bank_account_raw = $headerData['bank_account'] ?? '';
$id_bank_account  = ($bank_account_raw !== '' && $bank_account_raw !== '-') ? (int) $bank_account_raw : 'NULL';
$from_account   = $headerData['from_account'] ?? '';
$from_bank      = $headerData['from_bank'] ?? '';
$from_bank_curr = $headerData['from_bank_curr'] ?? '';

$keter = 'KONTRABON ' . $nama_supp_h;
$oe = $e($no_kbon_h);

// ---- 1) NOMOR REVISI = "<base>-REV_NN" -------------------------------------
// $no_kbon_h = PV yang SEDANG diedit (mis. ...01856-REV_01). Untuk penomoran, pakai
// nomor DASAR (buang akhiran -REV_NN, termasuk yg terlanjur nested) supaya edit ulang
// menghasilkan <base>-REV_02, BUKAN <base>-REV_01-REV_01. NN = MAX revisi base + 1.
$base_kbon = preg_replace('/(-REV_\d+)+$/i', '', $no_kbon_h);
$obe = $e($base_kbon);
$rnn = mysqli_query($conn2, "SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(no_kbon, '-REV_', -1) AS UNSIGNED)), 0) + 1 AS nn
    FROM kontrabon_h WHERE no_kbon LIKE '" . $obe . "-REV\\_%'");
$rownn = $rnn ? mysqli_fetch_assoc($rnn) : null;
$nn = $rownn ? (int) $rownn['nn'] : 1;
if ($nn < 1) { $nn = 1; }
$kode = $base_kbon . '-REV_' . str_pad((string) $nn, 2, '0', STR_PAD_LEFT);
$ke = $e($kode);

// ---- 2) JURNAL BALIK: reverse SEMUA jurnal PV lama (debit<->credit) ---------
// (identik insert_kontrabon_edit_all.php; self-balancing, di bawah no_journal=<old>)
$jurnal_balik = mysqli_query($conn2, "INSERT into tbl_list_journal (id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center) select '', no_journal, '" . $e($create_date) . "' tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, status, keterangan, create_by, create_date, '" . $e($create_user_h) . "' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '" . $oe . "'");
if ($jurnal_balik === false) {
    mysqli_rollback($conn2);
    pv_edit_out(['ok' => false, 'msg' => 'Gagal membuat jurnal balik PV lama. Tidak ada yang tersimpan.']);
    exit;
}

// ---- 3) TANDAI PV LAMA 'Updated' -------------------------------------------
$okOld = true;
$okOld = mysqli_query($conn2, "update kontrabon      set status = 'Updated' where no_kbon = '" . $oe . "'") && $okOld;
$okOld = mysqli_query($conn2, "update kontrabon_h    set status = 'Updated' where no_kbon = '" . $oe . "'") && $okOld;
$okOld = mysqli_query($conn2, "update kontrabon_ftr  set status = 'Updated' where no_kbon = '" . $oe . "'") && $okOld;
$okOld = mysqli_query($conn2, "update return_kb      set status = 'Updated' where no_kbon = '" . $oe . "'") && $okOld;
$okOld = mysqli_query($conn2, "update potongan       set status = 'Updated' where no_kbon = '" . $oe . "'") && $okOld;
if (!$okOld) {
    mysqli_rollback($conn2);
    pv_edit_out(['ok' => false, 'msg' => 'Gagal menandai PV lama sebagai Updated. Tidak ada yang tersimpan.']);
    exit;
}

// ---- 4) INSERT REVISI: potongan + header journals + kontrabon_h -------------
$ttl_kbon = (($sub_h + $lr_kurs1 + $s_qty1 + $s_harga1 + $materai1 + $ekspedisi1 + $moq1) - $pot_beli1) + $tax_h - $jml_return;

$queryss = "INSERT INTO potongan (no_kbon, tgl_kbon, nama_supp, jml_return, lr_kurs, s_qty, s_harga, materai, pot_beli, ekspedisi, moq, jml_potong, potongan_ppn, potongan_pph, status)
    VALUES
    ('" . $ke . "','" . $e($tgl_kbon_h) . "', '" . $e($nama_supp_h) . "', '$jml_return', '$lr_kurs1', '$s_qty1', '$s_harga1', '$materai1', '$pot_beli1', '$ekspedisi1', '$moq1', '$jml_potong1', '$potongan_ppn', '$potongan_pph', '$status')";
if (mysqli_query($conn2, $queryss) === false) {
    mysqli_rollback($conn2);
    pv_edit_out(['ok' => false, 'msg' => 'Gagal menyimpan potongan revisi. Tidak ada yang tersimpan.']);
    exit;
}

// Rate header (PAJAK) — identik create/edit-all.
if ($curr_h != 'IDR') {
    $sqlx = mysqli_query($conn1, "select ROUND(rate,2) as rate , tanggal FROM masterrate where tanggal = '" . $e($create_date) . "' and v_codecurr = 'PAJAK'");
    $rowx = $sqlx ? mysqli_fetch_array($sqlx) : null;
    $h_rate = isset($rowx['rate']) ? $rowx['rate'] : 0;
    if ($h_rate == 0) {
        $sqly = mysqli_query($conn1, "select ROUND(rate,2) as rate , tanggal FROM masterrate where id = (select max(id) as id FROM masterrate where v_codecurr = 'PAJAK') and v_codecurr = 'PAJAK'");
        $rowy = $sqly ? mysqli_fetch_array($sqly) : null;
        $rate = isset($rowy['rate']) ? $rowy['rate'] : 1;
    } else {
        $rate = $h_rate;
    }
} else {
    $rate = 1;
}

$idr_total_h = $ttl_kbon * $rate;
$idr_tax_h   = $tax_h * $rate;

if ($profit_center == 'NAG') { $no_cc = 'DEP24SUB001'; $nama_cc = 'MANAGEMENT FACTORY'; }
else                        { $no_cc = 'DEPNK01SUB001'; $nama_cc = 'KNITTING PRODUCTION'; }

// -- Beban potongan header (kurs / selisih qty / selisih harga / materai /
//    ekspedisi / moq / potongan beli). Sign & COA identik create/edit-all.
$beban = [
    ['v' => $lr_kurs1,   'coa' => '8.52.02', 'nm' => 'LABA / (RUGI) SELISIH KURS BELUM TEREALISASI', 'inv' => false],
    ['v' => $s_qty1,     'coa' => '5.97.03', 'nm' => 'BEBAN SELISIH KUANTITAS',                       'inv' => false],
    ['v' => $s_harga1,   'coa' => '5.97.02', 'nm' => 'BEBAN SELISIH HARGA',                           'inv' => false],
    ['v' => $materai1,   'coa' => '5.97.99', 'nm' => 'BEBAN PABRIK LAINNYA',                          'inv' => false],
    ['v' => $ekspedisi1, 'coa' => '5.84.03', 'nm' => 'BEBAN EKSPEDISI ANGKUTAN',                      'inv' => false],
    ['v' => $moq1,       'coa' => '5.97.99', 'nm' => 'BEBAN PABRIK LAINNYA',                          'inv' => false],
    ['v' => $pot_beli1,  'coa' => '5.97.02', 'nm' => 'BEBAN SELISIH HARGA',                           'inv' => true],  // inverted sign
];
foreach ($beban as $b) {
    $v = (float) $b['v'];
    if ($v == 0) { continue; }
    $mag = abs($v) * $rate;
    if (!$b['inv']) {
        // normal: positif -> debit, negatif -> credit
        if ($v >= 1) { pvj($conn2, $kode, $create_date, $b['coa'], $b['nm'], $no_cc, $nama_cc, '-', '', $curr_h, $rate, $v,        '0',       $mag, '0',  $keter, $create_user_h, $profit_center); }
        else         { pvj($conn2, $kode, $create_date, $b['coa'], $b['nm'], $no_cc, $nama_cc, '-', '', $curr_h, $rate, '0',       abs($v),   '0',  $mag, $keter, $create_user_h, $profit_center); }
    } else {
        // potongan beli: positif -> credit, negatif -> debit
        if ($v >= 1) { pvj($conn2, $kode, $create_date, $b['coa'], $b['nm'], $no_cc, $nama_cc, '-', '', $curr_h, $rate, '0',       $v,        '0',  $mag, $keter, $create_user_h, $profit_center); }
        else         { pvj($conn2, $kode, $create_date, $b['coa'], $b['nm'], $no_cc, $nama_cc, '-', '', $curr_h, $rate, abs($v),   '0',       $mag, '0',  $keter, $create_user_h, $profit_center); }
    }
}

// -- Payable (kbn_credit) : credit = ttl_kbon --------------------------------
$sqlcoa = mysqli_query($conn1, "SELECT no_coa, nama_coa from mastercoa_v2 where cus_ctg like '%" . mysqli_real_escape_string($conn1, $cus_ctg) . "%' and mattype like '%" . mysqli_real_escape_string($conn1, $mattype) . "%' and matclass like '%" . mysqli_real_escape_string($conn1, $matclass) . "%' and n_code_category like '%" . mysqli_real_escape_string($conn1, $n_code_category) . "%' and inv_type like '%kbn_credit%' Limit 1");
$rowcoa = $sqlcoa ? mysqli_fetch_array($sqlcoa) : null;
$no_coa_cre = $rowcoa['no_coa'] ?? '';
$nama_coa_cre = $rowcoa['nama_coa'] ?? '';
pvj($conn2, $kode, $create_date, $no_coa_cre, $nama_coa_cre, '-', '-', '-', '', $curr_h, $rate, '0', $ttl_kbon, '0', $idr_total_h, $keter, $create_user_h, $profit_center);

// -- DP (debit dp_h to payable COA) ------------------------------------------
if ($dp_h != 0) {
    $dp_h_idr = $dp_h * $rate;
    pvj($conn2, $kode, $create_date, $no_coa_cre, $nama_coa_cre, '-', '-', '-', '', $curr_h, $rate, $dp_h, '0', $dp_h_idr, '0', $keter, $create_user_h, $profit_center);
}

// -- PPN header (debit tax_h) ------------------------------------------------
if ($tax_h >= 1) {
    $sqlcoa3 = mysqli_query($conn1, "SELECT no_coa, nama_coa from mastercoa_v2 where inv_type like '%PPN KBN%' Limit 1");
    $rowcoa3 = $sqlcoa3 ? mysqli_fetch_array($sqlcoa3) : null;
    $no_coa_ppn = $rowcoa3['no_coa'] ?? '';
    $nama_coa_ppn = $rowcoa3['nama_coa'] ?? '';
    pvj($conn2, $kode, $create_date, $no_coa_ppn, $nama_coa_ppn, '-', '-', $no_faktur_h, '', $curr_h, $rate, $tax_h, '0', $idr_tax_h, '0', $keter, $create_user_h, $profit_center);
}

// -- kontrabon_h (revisi) ----------------------------------------------------
$unik_code_new = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789'), 0, 16);
$pph_h1 = $pph_h * $rate;
if ($curr_h == 'IDR') {
    $qh = "INSERT INTO kontrabon_h ( no_kbon, tgl_kbon, no_po, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, pph_idr, rate, total, dp_value, balance, curr, post_date, update_date, status, create_user, create_date, tgl_kbon2, unik_code,no_coa,nama_coa, profit_center, ir_number, id_bank_account, from_account, from_bank, from_bank_curr)
        VALUES
        ('" . $ke . "', '" . $e($tgl_kbon_h) . "', '" . $e($no_po_h) . "', '" . $e($nama_supp_h) . "', '" . $e($no_faktur_h) . "', '" . $e($supp_inv_h) . "', '" . $e($tgl_inv_h) . "', '" . $e($tgl_tempo_h) . "', '$sub_h', '$tax_h', '$pph_h', '1', '$total_h', '$dp_h', '$balance', '" . $e($curr_h) . "', '" . $e($create_date) . "', '" . $e($create_date) . "', '$status', '" . $e($create_user_h) . "', '" . $e($create_date) . "', '" . $e($tgl_kbon_s) . "', '" . $e($unik_code_new) . "', '" . $e($no_coa_cre) . "', '" . $e($nama_coa_cre) . "', '" . $e($profit_center) . "', '" . $e($ir_number) . "', $id_bank_account, '" . $e($from_account) . "', '" . $e($from_bank) . "', '" . $e($from_bank_curr) . "')";
} else {
    $qh = "INSERT INTO kontrabon_h ( no_kbon, tgl_kbon, no_po, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, pph_idr, rate, pph_fgn, total, dp_value, balance, curr, post_date, update_date, status, create_user, create_date, tgl_kbon2, unik_code,no_coa,nama_coa, profit_center, ir_number, id_bank_account, from_account, from_bank, from_bank_curr)
        VALUES
        ('" . $ke . "', '" . $e($tgl_kbon_h) . "', '" . $e($no_po_h) . "', '" . $e($nama_supp_h) . "', '" . $e($no_faktur_h) . "', '" . $e($supp_inv_h) . "', '" . $e($tgl_inv_h) . "', '" . $e($tgl_tempo_h) . "', '$sub_h', '$tax_h', '$pph_h1', '$rate', '$pph_h', '$total_h', '$dp_h', '$balance', '" . $e($curr_h) . "', '" . $e($create_date) . "', '" . $e($create_date) . "', '$status', '" . $e($create_user_h) . "', '" . $e($create_date) . "', '" . $e($tgl_kbon_s) . "', '" . $e($unik_code_new) . "', '" . $e($no_coa_cre) . "', '" . $e($nama_coa_cre) . "', '" . $e($profit_center) . "', '" . $e($ir_number) . "', $id_bank_account, '" . $e($from_account) . "', '" . $e($from_bank) . "', '" . $e($from_bank_curr) . "')";
}
if (mysqli_query($conn2, $qh) === false) {
    mysqli_rollback($conn2);
    pv_edit_out(['ok' => false, 'msg' => 'Gagal menyimpan header revisi (kontrabon_h). Tidak ada yang tersimpan.']);
    exit;
}

// Log edit (best-effort; tak fatal kalau tabel/skema beda).
@mysqli_query($conn2, "INSERT INTO ap_edit_kontrabon_log (no_kbon, no_kbon_new, created_by, created_date) VALUES ('" . $oe . "', '" . $ke . "', '" . $e($create_user_h) . "', '" . $e($create_date) . "')");

// ---- 5) PER-ITEM: BPB & RO (kontrabon / return_kb / kontrabon_ftr + jurnal) --
$failed = [];
foreach ($items as $idx => $it) {
    if (!is_array($it)) { continue; }

    $no_bpb   = trim((string) ($it['no_bpb']   ?? ''));
    $no_ro    = trim((string) ($it['no_ro']    ?? ''));
    $no_bppb  = trim((string) ($it['no_bppb']  ?? ''));
    $no_po    = (string) ($it['no_po']  ?? '');
    $tgl_bpb  = ($it['tgl_bpb'] ?? '') !== '' ? date("Y-m-d", strtotime($it['tgl_bpb'])) : $tgl_kbon_h;
    $tgl_po   = ($it['tgl_po']  ?? '') !== '' ? date("Y-m-d", strtotime($it['tgl_po']))  : $tgl_kbon_h;
    $tgl_kbon = ($it['tgl_kbon'] ?? '') !== '' ? date("Y-m-d", strtotime($it['tgl_kbon'])) : $tgl_kbon_h;
    $tgl_inv  = ($it['tgl_inv'] ?? '') !== '' ? date("Y-m-d", strtotime($it['tgl_inv'])) : $tgl_inv_h;
    $tgl_tempo = ($it['tgl_tempo'] ?? '') !== '' ? date("Y-m-d", strtotime($it['tgl_tempo'])) : $tgl_tempo_h;
    $jurnal   = $it['jurnal'] ?? '0';
    $nama_supp = $it['nama_supp'] ?? $nama_supp_h;
    $supp_inv = $it['supp_inv'] ?? $supp_inv_h;
    $curr     = $it['curr'] ?? $curr_h; if ($curr === '') { $curr = 'IDR'; }
    $ceklist  = $it['ceklist'] ?? '1';
    $create_user = $it['create_user'] ?? $create_user_h;
    $sum_sub  = $numf($it['sum_sub'] ?? 0);
    $sum_tax  = $numf($it['sum_tax'] ?? 0);
    $sum_dp   = $numf($it['sum_dp']  ?? 0);
    $sum_pph  = $numf($it['sum_pph'] ?? 0);
    $sum_total = $sum_sub - $sum_pph + $sum_tax;
    $sum_dpp  = $sum_sub + $sum_tax;
    $ttl_ro   = $numf($it['ttl_ro'] ?? 0);
    $idtax    = $it['idtax'] ?? '0';
    $pph      = $it['pph'] ?? '0';
    $start_date = ($it['start_date'] ?? '') !== '' ? date("Y-m-d", strtotime($it['start_date'])) : $tgl_kbon_h;
    $end_date   = ($it['end_date']   ?? '') !== '' ? date("Y-m-d", strtotime($it['end_date']))   : $tgl_kbon_h;
    $it_mattype  = $it['mattype'] ?? '';
    $it_matclass = $it['matclass'] ?? '';
    $it_ncode    = $it['n_code_category'] ?? '';
    $it_cusctg   = $it['cus_ctg'] ?? '';
    $keter_it = 'KONTRABON ' . $nama_supp;

    // FTR (CBD/DP) fields
    $no_ftr     = trim((string) ($it['no_ftr'] ?? ''));
    $no_po_ftr  = $it['no_po_ftr'] ?? '';
    $tgl_po_ftr = ($it['tgl_po_ftr'] ?? '') !== '' ? date("Y-m-d", strtotime($it['tgl_po_ftr'])) : $tgl_kbon_h;
    $no_pi_ftr  = $it['no_pi_ftr'] ?? '';
    $ttl_ftr    = $numf($it['ttl_ftr'] ?? 0);
    $curr_ftr   = $it['curr_ftr'] ?? 'IDR'; if ($curr_ftr === '') { $curr_ftr = 'IDR'; }
    $pv_ftr     = $it['pv_ftr'] ?? '';
    $bankout_ftr = $it['bankout_ftr'] ?? '';
    $bankoutdate_ftr = ($it['bankoutdate_ftr'] ?? '') !== '' ? date("Y-m-d", strtotime($it['bankoutdate_ftr'])) : $tgl_kbon_h;
    $coa_ftr    = $it['coa_ftr'] ?? '';

    // No Faktur / Tgl Faktur per baris (dari input form).
    $no_faktur = trim((string) ($it['no_faktur'] ?? $no_faktur_h));
    $no_faktur_in = trim((string) ($it['no_faktur_in'] ?? ''));
    $tgl_faktur = '';
    $faktur_found = false;
    if ($no_faktur_in !== '') { $no_faktur = $no_faktur_in; $faktur_found = true; }
    $tgl_faktur_in = trim((string) ($it['tgl_faktur_in'] ?? ''));
    if ($tgl_faktur_in !== '' && $tgl_faktur_in !== '-') {
        $ts = strtotime($tgl_faktur_in);
        if ($ts) { $tgl_faktur = date('Y-m-d', $ts); $faktur_found = true; }
    }
    $fp_esc  = $e($no_faktur);
    $tfp_sql = ($tgl_faktur !== '') ? "'" . $e($tgl_faktur) . "'" : "NULL";

    $rate_it = 1;
    if ($curr != 'IDR') {
        $sqlx = mysqli_query($conn1, "select ROUND(rate,2) as rate , tanggal FROM masterrate where tanggal = '" . $e($tgl_bpb) . "' and v_codecurr = 'PAJAK'");
        $rowx = $sqlx ? mysqli_fetch_array($sqlx) : null;
        $h_rate = isset($rowx['rate']) ? $rowx['rate'] : 0;
        if ($h_rate == 0) {
            $sqly = mysqli_query($conn1, "select ROUND(rate,2) as rate , tanggal FROM masterrate where id = (select max(id) as id FROM masterrate where v_codecurr = 'PAJAK') and v_codecurr = 'PAJAK'");
            $rowy = $sqly ? mysqli_fetch_array($sqly) : null;
            $rate_it = isset($rowy['rate']) ? $rowy['rate'] : 1;
        } else {
            $rate_it = $h_rate;
        }
    }

    // ---------------- BPB block ----------------
    if ($no_bpb !== '' && $no_ro === '') {

        // kontrabon_ftr (CBD/DP) bila baris membawa FTR.
        if ($no_ftr !== '') {
            $qftr = "INSERT INTO kontrabon_ftr (no_kbon, tgl_kbon, nama_supp, no_ftr, no_po, tgl_po,no_pi, curr, total_ftr, no_pv, no_bankout, tgl_bankout, no_coa, status, created_by, created_date)
                VALUES
                ('" . $ke . "', '" . $e($tgl_kbon) . "', '" . $e($nama_supp) . "', '" . $e($no_ftr) . "', '" . $e($no_po_ftr) . "', '" . $e($tgl_po_ftr) . "', '" . $e($no_pi_ftr) . "', '" . $e($curr_ftr) . "', '$ttl_ftr', '" . $e($pv_ftr) . "', '" . $e($bankout_ftr) . "', '" . $e($bankoutdate_ftr) . "', '" . $e($coa_ftr) . "', '$status', '" . $e($create_user) . "', '" . $e($create_date) . "')";
            @mysqli_query($conn2, $qftr);
            $sqlcoa_ftr = mysqli_query($conn1, "SELECT no_coa, nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn1, $coa_ftr) . "' Limit 1");
            $rowcoa_ftr = $sqlcoa_ftr ? mysqli_fetch_array($sqlcoa_ftr) : null;
            if ($rowcoa_ftr) {
                $rate_ftr = 1;
                if ($curr_ftr != 'IDR') {
                    $sqlftr = mysqli_query($conn1, "select ROUND(rate,2) as rate , tanggal FROM masterrate where tanggal = '" . $e($bankoutdate_ftr) . "' and v_codecurr = 'PAJAK' and curr = '" . mysqli_real_escape_string($conn1, $curr_ftr) . "'");
                    $rowftr = $sqlftr ? mysqli_fetch_array($sqlftr) : null;
                    $rate_ftr = isset($rowftr['rate']) ? $rowftr['rate'] : 1;
                }
                $ttl_ftr_idr = $ttl_ftr * $rate_ftr;
                pvj($conn2, $kode, $create_date, $rowcoa_ftr['no_coa'], $rowcoa_ftr['nama_coa'], '-', '-', $no_ftr, '', $curr_ftr, $rate_ftr, '0', $ttl_ftr, '0', $ttl_ftr_idr, $keter_it, $create_user, $profit_center);
            }
        }

        // kontrabon row
        $qk = "INSERT INTO kontrabon (no_kbon, tgl_kbon, id_jurnal, nama_supp, no_faktur, no_bpb, no_po, tgl_bpb,tgl_po, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, idtax, pph_code, pph_value, total, dp_value, curr, ceklist, post_date, update_date, status, status_int, create_user, create_date, start_date, end_date)
            VALUES
            ('" . $ke . "', '" . $e($tgl_kbon) . "', '" . $e($jurnal) . "', '" . $e($nama_supp) . "', '" . $fp_esc . "', '" . $e($no_bpb) . "', '" . $e($no_po) . "', '" . $e($tgl_bpb) . "', '" . $e($tgl_po) . "', '" . $e($supp_inv) . "', '" . $e($tgl_inv) . "', '" . $e($tgl_tempo) . "', '$sum_sub', '$sum_tax', '" . $e($idtax) . "', '" . $e($pph) . "', '$sum_pph', '$sum_total', '$sum_dp', '" . $e($curr) . "', '" . $e($ceklist) . "', '" . $e($create_date) . "', '" . $e($create_date) . "', '$status', '2', '" . $e($create_user) . "', '" . $e($create_date) . "', '" . $e($start_date) . "', '" . $e($end_date) . "')";
        $ok_k = mysqli_query($conn2, $qk);
        if ($ok_k === false) { $failed[] = $no_bpb; continue; }

        if ($tgl_faktur !== '') {
            @mysqli_query($conn2, "UPDATE kontrabon SET tgl_faktur = '" . $e($tgl_faktur) . "' WHERE no_kbon = '" . $ke . "' AND no_bpb = '" . $e($no_bpb) . "'");
        }
        if ($no_ro !== '') {
            @mysqli_query($conn2, "update bppb_new set is_invoiced = 'Invoiced', no_kbon = '" . $ke . "' where no_ro= '" . $e($no_ro) . "'");
        }

        $idr_sub = $sum_dpp * $rate_it;
        $idr_tax = $sum_tax * $rate_it;

        $sqlcoaB = mysqli_query($conn1, "SELECT no_coa, nama_coa from mastercoa_v2 where cus_ctg like '%" . mysqli_real_escape_string($conn1, $it_cusctg) . "%' and mattype like '%" . mysqli_real_escape_string($conn1, $it_mattype) . "%' and matclass like '%" . mysqli_real_escape_string($conn1, $it_matclass) . "%' and n_code_category like '%" . mysqli_real_escape_string($conn1, $it_ncode) . "%' and inv_type like '%bpb_credit%' Limit 1");
        $rowcoaB = $sqlcoaB ? mysqli_fetch_array($sqlcoaB) : null;
        $no_coa_deb = $rowcoaB['no_coa'] ?? '';
        $nama_coa_deb = $rowcoaB['nama_coa'] ?? '';

        // GR/IR debit sum_dpp
        pvj($conn2, $kode, $create_date, $no_coa_deb, $nama_coa_deb, '-', '-', $no_bpb, $tgl_bpb, $curr, $rate_it, $sum_dpp, '0', $idr_sub, '0', $keter_it, $create_user, $profit_center);

        // PPN MASUKAN credit sum_tax
        if ($sum_tax > 0) {
            $sqlcoa3B = mysqli_query($conn1, "SELECT no_coa, nama_coa from mastercoa_v2 where inv_type like '%PPN MASUKAN%' Limit 1");
            $rowcoa3B = $sqlcoa3B ? mysqli_fetch_array($sqlcoa3B) : null;
            $no_coa_ppnB = $rowcoa3B['no_coa'] ?? '';
            $nama_coa_ppnB = $rowcoa3B['nama_coa'] ?? '';
            pvj($conn2, $kode, $create_date, $no_coa_ppnB, $nama_coa_ppnB, '-', '-', $no_bpb, $tgl_bpb, $curr, $rate_it, '0', $sum_tax, '0', $idr_tax, $keter_it, $create_user, $profit_center);
        }

        // Faktur pajak pada baris jurnal BPB ini
        if ($faktur_found) {
            @mysqli_query($conn2, "UPDATE tbl_list_journal SET faktur_pajak = '" . $fp_esc . "', tgl_faktur_pajak = $tfp_sql WHERE no_journal = '" . $ke . "' AND reff_doc = '" . $e($no_bpb) . "' AND type_journal = 'AP - Kontrabon'");
        }

        // bpb_new: No/Tgl Faktur (strip-guard) + No/Tgl Invoice header.
        $dok_bpb = ($ir_number !== '' && $ir_number !== '-') ? $ir_number : $kode;
        $inv_tgl_sql = (!empty($tgl_inv_h) && $supp_inv_h !== '' && $supp_inv_h !== '-') ? "'" . $e($tgl_inv_h) . "'" : 'NULL';
        bpbnew_apply_docinfo($conn2, $no_bpb, $dok_bpb, $supp_inv_h, $inv_tgl_sql, $no_faktur, $tfp_sql);

        // is_invoiced + kartu_hutang + status (mirror create)
        @mysqli_query($conn2, "update bpb_new set is_invoiced = 'Invoiced' where no_bpb= '" . $e($no_bpb) . "'");
        @mysqli_query($conn2, "update kartu_hutang set no_kbon='" . $ke . "' where no_bpb = '" . $e($no_bpb) . "' and no_po='" . $e($no_po) . "'");
        @mysqli_query($conn2, "update status set no_kbon='" . $ke . "',tgl_kbon = '" . $e($tgl_kbon) . "' where no_bpb = '" . $e($no_bpb) . "'");
    }

    // ---------------- RO block ----------------
    if ($no_ro !== '') {
        $qro = "INSERT INTO return_kb (no_kbon, no_ro, no_bpbrtn, total_ro, status)
            VALUES
            ('" . $ke . "', '" . $e($no_ro) . "', '" . $e($no_bppb) . "', '$ttl_ro', '$status')";
        $ok_ro = mysqli_query($conn2, $qro);
        if ($ok_ro === false) { $failed[] = ($no_bppb !== '' ? $no_bppb : $no_ro); continue; }

        @mysqli_query($conn2, "update bppb_new set no_kbon = '" . $ke . "' where no_bppb = '" . $e($no_bppb) . "'");

        // bppb_new: No/Tgl Faktur (strip-guard) + No/Tgl Invoice header.
        $ret_dok     = ($ir_number !== '' && $ir_number !== '-') ? $ir_number : $kode;
        $ret_inv_tgl = (!empty($tgl_inv_h) && $supp_inv !== '' && $supp_inv !== '-') ? "'" . $e($tgl_inv) . "'" : 'NULL';
        bppbnew_apply_docinfo($conn2, $no_bppb, $ret_dok, $supp_inv, $ret_inv_tgl, $no_faktur, $tfp_sql);

        $idr_ttl_ro = $ttl_ro * $rate_it;

        // tax_ro dari bppb_new (identik create)
        $sqlbppb = mysqli_query($conn1, "select tgl_bppb,tax,round(sum((qty*price) * tax / 100),2) tax_ro from bppb_new where no_bppb = '" . mysqli_real_escape_string($conn1, $no_bppb) . "' GROUP BY no_bppb");
        $rowbppb = $sqlbppb ? mysqli_fetch_array($sqlbppb) : null;
        $tgl_bppb   = isset($rowbppb['tgl_bppb']) ? $rowbppb['tgl_bppb'] : $tgl_bpb;
        $val_tax_ro = isset($rowbppb['tax']) ? $rowbppb['tax'] : 0;
        $tax_ro     = isset($rowbppb['tax_ro']) ? $rowbppb['tax_ro'] : 0;
        $idr_tax_ro = $tax_ro * $rate_it;

        $sqlcoaR = mysqli_query($conn1, "SELECT no_coa, nama_coa from mastercoa_v2 where cus_ctg like '%" . mysqli_real_escape_string($conn1, $it_cusctg) . "%' and mattype like '%" . mysqli_real_escape_string($conn1, $it_mattype) . "%' and matclass like '%" . mysqli_real_escape_string($conn1, $it_matclass) . "%' and n_code_category like '%" . mysqli_real_escape_string($conn1, $it_ncode) . "%' and inv_type like '%bpb_credit%' Limit 1");
        $rowcoaR = $sqlcoaR ? mysqli_fetch_array($sqlcoaR) : null;
        $no_coa_debR = $rowcoaR['no_coa'] ?? '';
        $nama_coa_debR = $rowcoaR['nama_coa'] ?? '';

        // GR/IR credit ttl_ro
        pvj($conn2, $kode, $create_date, $no_coa_debR, $nama_coa_debR, '-', '-', $no_bppb, $tgl_bppb, $curr, $rate_it, '0', $ttl_ro, '0', $idr_ttl_ro, $keter_it, $create_user, $profit_center);

        if ($val_tax_ro > 0) {
            // PPN UNBILLED (1.52.07) debit tax_ro
            pvj($conn2, $kode, $create_date, '1.52.07', 'PAJAK DIBAYAR DIMUKA PPN MASUKAN (UNBILLED)', '-', '-', $no_bppb, $tgl_bppb, $curr, $rate_it, $tax_ro, '0', $idr_tax_ro, '0', $keter_it, $create_user, $profit_center);
            // PPN MASUKAN (1.52.04) credit tax_ro
            pvj($conn2, $kode, $create_date, '1.52.04', 'PAJAK DIBAYAR DIMUKA PPN MASUKAN', '-', '-', $no_bppb, $tgl_bppb, $curr, $rate_it, '0', $tax_ro, '0', $idr_tax_ro, $keter_it, $create_user, $profit_center);
        }

        if ($faktur_found) {
            @mysqli_query($conn2, "UPDATE tbl_list_journal SET faktur_pajak = '" . $fp_esc . "', tgl_faktur_pajak = $tfp_sql WHERE no_journal = '" . $ke . "' AND reff_doc = '" . $e($no_bppb) . "' AND type_journal = 'AP - Kontrabon'");
        }
    }
}

if (!empty($failed)) {
    mysqli_rollback($conn2);
    pv_edit_out(['ok' => false, 'failed' => $failed,
        'msg' => count($failed) . ' baris gagal disimpan. SEMUA dibatalkan (rollback) — tidak ada yang tersimpan.']);
    exit;
}

// ---- 6) COMMIT -------------------------------------------------------------
if (!mysqli_commit($conn2)) {
    mysqli_rollback($conn2);
    pv_edit_out(['ok' => false, 'msg' => 'Commit gagal. Tidak ada yang tersimpan.']);
    exit;
}
$GLOBALS['__pv_edit_committed'] = true;
pv_edit_out(['ok' => true, 'no_kbon' => $kode]);
