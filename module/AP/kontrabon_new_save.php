<?php
// Simpan Kontrabon New: header + invoice + faktur + BPB, lalu tandai reference
// jadi 'Used'. SEMUA dalam 1 TRANSAKSI -> all-or-nothing (kalau ada 1 gagal,
// rollback total, tidak ada data setengah tersimpan). BPB per faktur di-BULK
// INSERT (1 query multi-row) supaya cepat walau banyak.
// Struktur nested dari client:
//   invoices = [{no_inv,tgl_inv,amount,
//                fakturs:[{no_faktur,tgl_faktur,bpbs:[{no_bpb,tgl_bpb,total,curr}]}]}]
session_start();
include '../../conn/conn.php';
require_once __DIR__ . '/bpb_docinfo_guard.php';
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Jakarta'); // WIB (server PHP default = Europe/Berlin)

$user = $_SESSION['username'] ?? '';
$now  = date('Y-m-d H:i:s');
$e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
$d = function ($v) { return !empty($v) ? "'" . date('Y-m-d', strtotime($v)) . "'" : "NULL"; };

$doc   = trim($_POST['doc_number'] ?? '');
$unik  = trim($_POST['unik_code'] ?? '');
$reff  = trim($_POST['no_reff'] ?? '');
$supp  = trim($_POST['nama_supp'] ?? '');
$desc  = trim($_POST['pesan'] ?? '');
$total = (float) str_replace(',', '', $_POST['total_amount'] ?? '0');
// Document Date (diisi user) + Invoice Received Date (kontrabon_date = SELALU Rabu dari Document Date).
$docDate = !empty($_POST['document_date']) ? date('Y-m-d', strtotime($_POST['document_date']))
    : (!empty($_POST['kontrabon_date']) ? date('Y-m-d', strtotime($_POST['kontrabon_date'])) : date('Y-m-d'));
$addWed = (3 - (int) date('w', strtotime($docDate)) + 7) % 7;   // w: 0=Min..3=Rabu..6=Sab
$tgl = date('Y-m-d', strtotime("$docDate +$addWed days"));
$invoices = json_decode($_POST['invoices'] ?? '[]', true);

if ($supp === '') { echo json_encode(['ok' => false, 'msg' => 'Supplier is required.']); exit; }
if ($reff === '') { echo json_encode(['ok' => false, 'msg' => 'Reference number is required.']); exit; }
if (!is_array($invoices) || count($invoices) === 0) { echo json_encode(['ok' => false, 'msg' => 'No invoice to save.']); exit; }

$nInv = 0; $nFak = 0; $nBpb = 0; $fail = null;

mysqli_begin_transaction($conn2);

// 1) Kunci reference: hanya boleh kalau masih Available (atomik dalam transaksi).
$updReff = mysqli_query($conn2, "UPDATE ir_kontrabon_ref SET status = 'Used'
    WHERE ref_number = '" . $e($reff) . "' AND status = 'Available'");
if (!$updReff) { $fail = 'reff: ' . mysqli_error($conn2); }
elseif (mysqli_affected_rows($conn2) === 0) { $fail = "Reference '$reff' is not available (maybe already used/cancelled)."; }

// 1b) Nomor dokumen MELANJUT dari urutan Invoice Received (ir_invoice_supp_h),
//     format IR/NAG/YYYY/MM/NNNNN. Karena mirror juga menulis ke tabel itu,
//     max-nya mencakup dokumen IR-input maupun Kontrabon -> nomor selalu nyambung.
if (!$fail) {
    $rm = mysqli_query($conn2, "SELECT MAX(CAST(SUBSTRING_INDEX(doc_number, '/', -1) AS UNSIGNED)) AS mx FROM ir_invoice_supp_h");
    $next = 1;
    if ($rm && ($rw = mysqli_fetch_assoc($rm))) $next = ((int) $rw['mx']) + 1;
    $doc = 'IR/NAG/' . date('Y') . '/' . date('m') . '/' . str_pad($next, 5, '0', STR_PAD_LEFT);
}

// 1c) Guard terakhir: pastikan BPB belum dipakai di kontrabon tersimpan.
if (!$fail) {
    $allBpb = [];
    foreach ($invoices as $iv) foreach (($iv['fakturs'] ?? []) as $fk) foreach (($fk['bpbs'] ?? []) as $bp) {
        $nb = trim($bp['no_bpb'] ?? ''); if ($nb !== '') $allBpb[$nb] = true;
    }
    if ($allBpb) {
        $inList = implode(',', array_map(function ($x) use ($e) { return "'" . $e($x) . "'"; }, array_keys($allBpb)));
        $rc = mysqli_query($conn2, "SELECT b.no_bpb, h.doc_number FROM ir_kontrabon_bpb b
            LEFT JOIN ir_kontrabon_h h ON h.unik_code = b.unik_code
            WHERE b.no_bpb IN ($inList) AND (h.status IS NULL OR h.status <> 'Cancel') LIMIT 1");
        if ($rc && mysqli_num_rows($rc) > 0) {
            $dup = mysqli_fetch_assoc($rc);
            $fail = "BPB '" . $dup['no_bpb'] . "' is already used in saved invoice received " . ($dup['doc_number'] ?? '') . ".";
        }
    }
}

// 1d) Guard: No Invoice belum dipakai untuk SUPPLIER yang sama (kecuali Cancel).
if (!$fail) {
    $allInv = [];
    foreach ($invoices as $iv) { $n = trim($iv['no_inv'] ?? ''); if ($n !== '') $allInv[$n] = true; }
    if ($allInv) {
        $inList = implode(',', array_map(function ($x) use ($e) { return "'" . $e($x) . "'"; }, array_keys($allInv)));
        $rc = mysqli_query($conn2, "SELECT i.no_inv, h.doc_number FROM ir_kontrabon_inv i
            JOIN ir_kontrabon_h h ON h.unik_code = i.unik_code
            WHERE i.no_inv IN ($inList) AND h.nama_supp = '" . $e($supp) . "' AND (h.status IS NULL OR h.status <> 'Cancel') LIMIT 1");
        if ($rc && mysqli_num_rows($rc) > 0) {
            $dup = mysqli_fetch_assoc($rc);
            $fail = "Invoice '" . $dup['no_inv'] . "' is already used for supplier '$supp' in invoice received " . ($dup['doc_number'] ?? '') . ".";
        }
    }
}

// (no_bpb, no_inv, tgl_inv, no_faktur, tgl_faktur) per BPB -> dipakai update bpb_new
// (agar No/Tgl Invoice & Faktur muncul di laporan_pembelian). Diterapkan setelah commit.
$bnUpd = [];

// 2) Header
if (!$fail) {
    $ok = mysqli_query($conn2, "INSERT INTO ir_kontrabon_h
        (doc_number, kontrabon_date, document_date, unik_code, no_reff, nama_supp, deskripsi, total_amount, status, create_user, create_date)
        VALUES ('" . $e($doc) . "', '$tgl', '$docDate', '" . $e($unik) . "', '" . $e($reff) . "', '" . $e($supp) . "', '" . $e($desc) . "', $total, 'Draft', '" . $e($user) . "', '$now')");
    if (!$ok) $fail = 'header: ' . mysqli_error($conn2);
}

// 3) Invoice -> Faktur -> BPB
if (!$fail) {
    foreach ($invoices as $iv) {
        $noInv = trim($iv['no_inv'] ?? '');
        if ($noInv === '') continue;
        $amt   = (float) str_replace(',', '', (string) ($iv['amount'] ?? '0'));
        $okInv = mysqli_query($conn2, "INSERT INTO ir_kontrabon_inv
            (unik_code, doc_number, no_inv, tgl_inv, amount, create_user, create_date)
            VALUES ('" . $e($unik) . "', '" . $e($doc) . "', '" . $e($noInv) . "', " . $d($iv['tgl_inv'] ?? '') . ", $amt, '" . $e($user) . "', '$now')");
        if (!$okInv) { $fail = 'invoice: ' . mysqli_error($conn2); break; }
        $invId = (int) mysqli_insert_id($conn2);
        $nInv++;

        if (empty($iv['fakturs']) || !is_array($iv['fakturs'])) continue;
        foreach ($iv['fakturs'] as $fk) {
            $noFak = trim($fk['no_faktur'] ?? '');
            if ($noFak === '') continue;
            $dpp = (float) ($fk['dpp'] ?? 0); $ppn = (float) ($fk['ppn'] ?? 0); $ppnbm = (float) ($fk['ppnbm'] ?? 0);
            $okFak = mysqli_query($conn2, "INSERT INTO ir_kontrabon_faktur
                (inv_id, unik_code, no_faktur, tgl_faktur, nama_supplier, npwp_supplier, pembeli, npwp_pembeli, dpp, ppn, ppnbm, status_faktur, create_user, create_date)
                VALUES ($invId, '" . $e($unik) . "', '" . $e($noFak) . "', " . $d($fk['tgl_faktur'] ?? '') . ",
                    '" . $e($fk['nama_supplier'] ?? '') . "', '" . $e($fk['npwp_supplier'] ?? '') . "', '" . $e($fk['pembeli'] ?? '') . "', '" . $e($fk['npwp_pembeli'] ?? '') . "',
                    $dpp, $ppn, $ppnbm, '" . $e($fk['status'] ?? '') . "', '" . $e($user) . "', '$now')");
            if (!$okFak) { $fail = 'faktur: ' . mysqli_error($conn2); break 2; }
            $fakId = (int) mysqli_insert_id($conn2);
            $nFak++;

            // BULK insert BPB (1 query multi-row)
            if (!empty($fk['bpbs']) && is_array($fk['bpbs'])) {
                $vals = [];
                foreach ($fk['bpbs'] as $bp) {
                    $noBpb = trim($bp['no_bpb'] ?? '');
                    if ($noBpb === '') continue;
                    $tot = (float) ($bp['total'] ?? 0);
                    $bdpp = (float) ($bp['dpp'] ?? 0);
                    $bppn = (float) ($bp['ppn'] ?? 0);
                    $cur = trim($bp['curr'] ?? '');
                    $vals[] = "($fakId, $invId, '" . $e($unik) . "', '" . $e($noBpb) . "', '" . $e($bp['no_po'] ?? '') . "', '" . $e($bp['supplier'] ?? '') . "', " . $d($bp['tgl_bpb'] ?? '') . ", $tot, $bdpp, $bppn, '" . $e($cur) . "', '" . $e($user) . "', '$now')";
                    $bnUpd[] = [$noBpb, $noInv, $iv['tgl_inv'] ?? '', $noFak, $fk['tgl_faktur'] ?? ''];
                }
                if ($vals) {
                    $okBpb = mysqli_query($conn2, "INSERT INTO ir_kontrabon_bpb
                        (faktur_id, inv_id, unik_code, no_bpb, no_po, supplier, tgl_bpb, total, dpp, ppn, curr, create_user, create_date)
                        VALUES " . implode(',', $vals));
                    if (!$okBpb) { $fail = 'bpb: ' . mysqli_error($conn2); break 2; }
                    $nBpb += count($vals);
                }
            }
        }
    }
}

// 4) MIRROR header + invoice ke tabel Invoice Received (ir_invoice_supp_h/supp)
//    supaya downstream IR jalan & penomoran nyambung. Faktur/BPB TIDAK di-mirror
//    (IR tidak punya level itu). Semua dalam transaksi yang sama.
if (!$fail) {
    $okH = mysqli_query($conn2, "INSERT INTO ir_invoice_supp_h
        (doc_number, tgl_penerimaan, no_reff, nama_supp, total_amount, deskripsi, status, updated_at, created_by, created_date, unik_code)
        VALUES ('" . $e($doc) . "', '$tgl', '" . $e($reff) . "', '" . $e($supp) . "', $total, '" . $e($desc) . "', 'Received', '$now', '" . $e($user) . "', '$now', '" . $e($unik) . "')");
    if (!$okH) $fail = 'IR header: ' . mysqli_error($conn2);
}
if (!$fail) {
    $vals = [];
    foreach ($invoices as $iv) {
        $noInv = trim($iv['no_inv'] ?? ''); if ($noInv === '') continue;
        $amt = (float) str_replace(',', '', (string) ($iv['amount'] ?? '0'));
        $vals[] = "('" . $e($doc) . "', '" . $e($noInv) . "', " . $d($iv['tgl_inv'] ?? '') . ", $amt, 'Y', '" . $e($user) . "', '$now')";
    }
    if ($vals && !mysqli_query($conn2, "INSERT INTO ir_invoice_supp
        (doc_number, no_invoice, tgl_invoice, amount, status, created_by, created_date) VALUES " . implode(',', $vals)))
        $fail = 'IR invoice: ' . mysqli_error($conn2);
}

if ($fail) {
    mysqli_rollback($conn2);
    echo json_encode(['ok' => false, 'msg' => 'Save failed (rolled back): ' . $fail]);
    exit;
}

mysqli_commit($conn2);

// Bersihkan draft & reservasi BPB milik user (sudah menjadi kontrabon final).
mysqli_query($conn2, "DELETE FROM ir_kontrabon_temp WHERE create_user = '" . $e($user) . "'");
mysqli_query($conn2, "DELETE FROM ir_kontrabon_bpb_reserve WHERE create_user = '" . $e($user) . "'");

// Update bpb_new agar No/Tgl Invoice & Faktur per BPB muncul di laporan_pembelian &
// laporan_pembelian_global (mereka baca upt_* dari bpb_new). Pola sama dgn menu
// "Update Dokumen Invoice/Faktur" (insert_bpb_fakturinv.php).
foreach ($bnUpd as $u) {
    if (($u[0] ?? '') === '') continue;
    // GUARD: strip "-" tidak menimpa No/Tgl Invoice/Faktur yg sudah terisi real.
    bpbnew_apply_docinfo($conn2, $u[0], $doc, $u[1], $d($u[2]), $u[3], $d($u[4]));
}

echo json_encode([
    'ok' => true,
    'msg' => "Invoice Received $doc saved: $nInv invoice(s), $nFak faktur, $nBpb BPB. Reference '$reff' marked as Used.",
    'doc_number' => $doc,
]);
mysqli_close($conn2);
