<?php
// UPDATE (edit) Kontrabon: ganti seluruh detail (invoice/faktur/BPB) kontrabon
// yang sudah ada. Dalam 1 TRANSAKSI: hapus detail lama -> guard keunikan (BPB &
// invoice, mengecualikan kontrabon ini sendiri karena sudah dihapus) -> insert
// ulang -> update header. All-or-nothing. no_reff & unik_code TIDAK berubah;
// reff tetap Used. SUPPLIER BOLEH berubah, tapi wajib ada di master DAN cocok
// dgn semua BPB pada payload (guard 1b & 1c). PROFIT CENTER juga boleh
// berubah - dan kalau berubah, NOMOR DOKUMENnya ikut berubah (lihat bagian
// "PROFIT CENTER" di bawah).
session_start();
include '../../conn/conn.php';
require_once __DIR__ . '/bpb_docinfo_guard.php';
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Jakarta'); // WIB

$user = $_SESSION['username'] ?? '';
$now  = date('Y-m-d H:i:s');
$e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
$d = function ($v) { return !empty($v) ? "'" . date('Y-m-d', strtotime($v)) . "'" : "NULL"; };

$doc   = trim($_POST['doc_number'] ?? '');
$desc  = trim($_POST['pesan'] ?? '');
$total = (float) str_replace(',', '', $_POST['total_amount'] ?? '0');
// Document Date (diisi user) + Invoice Received Date (kontrabon_date = SELALU Rabu dari Document Date).
$docDate = !empty($_POST['document_date']) ? date('Y-m-d', strtotime($_POST['document_date']))
    : (!empty($_POST['kontrabon_date']) ? date('Y-m-d', strtotime($_POST['kontrabon_date'])) : date('Y-m-d'));
$addWed = (3 - (int) date('w', strtotime($docDate)) + 7) % 7;   // w: 0=Min..3=Rabu..6=Sab
$tgl = date('Y-m-d', strtotime("$docDate +$addWed days"));
$invoices = json_decode($_POST['invoices'] ?? '[]', true);

if ($doc === '') { echo json_encode(['ok' => false, 'msg' => 'Document number is required.']); exit; }
if (!is_array($invoices) || count($invoices) === 0) { echo json_encode(['ok' => false, 'msg' => 'No invoice to save.']); exit; }

$h = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT unik_code, no_reff, nama_supp, status FROM ir_kontrabon_h WHERE doc_number = '" . $e($doc) . "' LIMIT 1"));
if (!$h) { echo json_encode(['ok' => false, 'msg' => 'Invoice Received not found.']); exit; }
if (($h['status'] ?? '') === 'Cancel') { echo json_encode(['ok' => false, 'msg' => 'Cancelled invoice received cannot be edited.']); exit; }

// GUARD: kalau dokumen IR-nya sudah dipakai transaksi lain (status bergerak dari
// 'Received', atau ada transfer TFTA/TATP/TPTF aktif), kontrabon TIDAK boleh diedit
// karena akan men-desync nilai transfer/approval di hilir.
$ihq = mysqli_query($conn2, "SELECT status FROM ir_invoice_supp_h WHERE doc_number = '" . $e($doc) . "' LIMIT 1");
$irStatus = ($ihq && ($ihr = mysqli_fetch_assoc($ihq))) ? ($ihr['status'] ?? null) : null;
if ($irStatus !== null && !in_array($irStatus, ['Received', 'Cancel'], true)) {
    echo json_encode(['ok' => false, 'msg' => "Cannot edit: the Invoice Received document is already in process (status: $irStatus). It has been used in another transaction."]); exit;
}
$tcq = mysqli_query($conn2, "SELECT COUNT(*) c FROM ir_trans_invoice_supp WHERE doc_number = '" . $e($doc) . "' AND status <> 'Cancel'");
if ($tcq && ($tcr = mysqli_fetch_assoc($tcq)) && (int) $tcr['c'] > 0) {
    echo json_encode(['ok' => false, 'msg' => 'Cannot edit: this document has already been used in another transaction (transfer/approval). Reverse that transaction first.']); exit;
}

$unik = $h['unik_code'];
$supp = $h['nama_supp'];

// Supplier BOLEH diganti lewat form edit. Alasannya: satu perusahaan kadang
// punya 2 record di mastersupplier dgn susunan nama berbeda (mis.
// "CV. MITRA EKA PERKASA" vs "MITRA EKA PERKASA, CV") dan BPB-nya cuma
// terdaftar di salah satunya. Divalidasi 2 lapis: (a) harus ada di master,
// (b) harus cocok dgn semua BPB pada payload (lihat guard 1b/1c di bawah).
$suppNew = trim($_POST['nama_supp'] ?? '');
if ($suppNew !== '' && $suppNew !== $supp) {
    $ms = mysqli_query($conn2, "SELECT 1 FROM mastersupplier WHERE Supplier = '" . $e($suppNew) . "' LIMIT 1");
    if (!$ms || mysqli_num_rows($ms) === 0) {
        echo json_encode(['ok' => false, 'msg' => "Supplier '$suppNew' is not registered in the supplier master."]); exit;
    }
    $supp = $suppNew;
}

// ============================================================================
// PROFIT CENTER - BOLEH DIGANTI, DAN NOMOR DOKUMEN IKUT BERUBAH
//
// Profit center dokumen tersimpan DI NOMORNYA sendiri (IR/<PC>/YYYY/MM/NNNNN) -
// tidak ada kolomnya di ir_kontrabon_h. Jadi begitu profit center diganti,
// nomornya HARUS ikut berubah; kalau tidak, nomor dan isi dokumen saling
// bertentangan dan penjaga BPB di bawah akan selalu menolak.
//
// Yang ditukar HANYA segmen ke-2. NOMOR URUTnya TETAP - deret IR memang satu
// untuk semua profit center (lihat create_kontrabon_new.php), jadi pergantian
// ini tidak bisa menyerobot nomor milik dokumen lain. Tetap diperiksa juga
// supaya tidak pernah ada dua dokumen bernomor sama (kolomnya tidak ber-indeks
// unik, jadi basis data tidak akan menahannya sendiri).
//
// Kode yang dikirim browser TIDAK dipercaya: wajib ada di master_pc & Active.
// Edit hanya dibolehkan selama IR masih 'Received' dan belum dipakai transfer
// (dua penjaga di atas), jadi tidak ada dokumen hilir yang ikut berubah arti.
// ============================================================================
$bagianDoc = explode('/', $doc);
$pcLama  = isset($bagianDoc[1]) ? strtoupper(trim($bagianDoc[1])) : '';
$pcKirim = strtoupper(trim($_POST['profit_center'] ?? ''));
$pcAktif = $pcLama;   // profit center yang dipakai menjaga BPB
$docBaru = $doc;      // tetap sama dgn $doc kalau profit center tidak berubah

if ($pcKirim !== '' && $pcLama !== '' && $pcKirim !== $pcLama) {
    $qpc = mysqli_query($conn1, "SELECT kode_pc FROM master_pc
        WHERE kode_pc = '" . mysqli_real_escape_string($conn1, $pcKirim) . "'
          AND status = 'Active' LIMIT 1");
    if (!$qpc || mysqli_num_rows($qpc) === 0) {
        echo json_encode(['ok' => false, 'msg' => "Profit center '$pcKirim' is not registered (or no longer active) in the profit center master."]); exit;
    }
    $bagianBaru = $bagianDoc;
    $bagianBaru[1] = $pcKirim;
    $docBaru = implode('/', $bagianBaru);

    foreach (['ir_kontrabon_h', 'ir_invoice_supp_h'] as $tCek) {
        $qada = mysqli_query($conn2, "SELECT doc_number FROM $tCek WHERE doc_number = '" . $e($docBaru) . "' LIMIT 1");
        if ($qada && mysqli_num_rows($qada) > 0) {
            echo json_encode(['ok' => false, 'msg' => "Cannot change the profit center: document number $docBaru already exists. Nothing was changed."]); exit;
        }
    }
    $pcAktif = $pcKirim;
}

$nInv = 0; $nFak = 0; $nBpb = 0; $fail = null;
$nGanti = 0;    // berapa baris yang nomornya ikut diganti
$bnUpd = []; // (no_bpb, no_inv, tgl_inv, no_faktur, tgl_faktur) -> update bpb_new setelah commit
mysqli_begin_transaction($conn2);

// 0a) PROFIT CENTER BERUBAH -> ganti nomor dokumen di SEMUA tabel yang
//     menyimpannya, dikerjakan paling awal supaya sisa berkas ini bekerja dgn
//     nomor baru tanpa perlu tahu-menahu. Semua tabel ini InnoDB, jadi ikut
//     dibatalkan bersama kalau ada yang gagal di bawah.
//     Daftar tabelnya dari information_schema - tidak ada kolom lain di basis
//     data ini yang menyimpan nomor IR.
$docLama = $doc;
if ($docBaru !== $doc) {
    $kolomNomor = [
        ['ir_kontrabon_h',        'doc_number'],
        ['ir_kontrabon_inv',      'doc_number'],
        ['ir_invoice_supp_h',     'doc_number'],
        ['ir_invoice_supp',       'doc_number'],
        ['ir_kontrabon_temp',     'doc_number'],   // draf
        ['ir_trans_invoice_supp', 'doc_number'],   // transfer lama / batal
        ['ir_reverse',            'doc_number'],   // riwayat reverse
        ['bpb_new',               'upt_dok_inv'],  // penunjuk balik dari BPB
        ['bppb_new',              'upt_dok_inv'],  // idem, untuk retur
    ];
    foreach ($kolomNomor as $kn) {
        if (!mysqli_query($conn2, "UPDATE `" . $kn[0] . "` SET `" . $kn[1] . "` = '" . $e($docBaru) . "'
            WHERE `" . $kn[1] . "` = '" . $e($docLama) . "'")) {
            $fail = 'renumber ' . $kn[0] . ': ' . mysqli_error($conn2); break;
        }
        $nGanti += (int) mysqli_affected_rows($conn2);
    }
    if (!$fail) { $doc = $docBaru; }
}

// 0) Hapus detail lama kontrabon ini (+ invoice mirror IR utk doc ini)
if (!$fail) foreach (['ir_kontrabon_inv', 'ir_kontrabon_faktur', 'ir_kontrabon_bpb'] as $t) {
    if (!mysqli_query($conn2, "DELETE FROM $t WHERE unik_code = '" . $e($unik) . "'")) { $fail = "clear $t: " . mysqli_error($conn2); break; }
}
if (!$fail && !mysqli_query($conn2, "DELETE FROM ir_invoice_supp WHERE doc_number = '" . $e($doc) . "'")) $fail = 'clear IR invoice: ' . mysqli_error($conn2);

// 1) Guard BPB: tidak dobel dalam payload + tidak dipakai kontrabon lain
$allBpb = [];
if (!$fail) {
    foreach ($invoices as $iv) foreach (($iv['fakturs'] ?? []) as $fk) foreach (($fk['bpbs'] ?? []) as $bp) {
        $nb = trim($bp['no_bpb'] ?? '');
        if ($nb === '') continue;
        if (isset($allBpb[$nb])) { $fail = "BPB '$nb' appears more than once in this invoice received."; break 3; }
        $allBpb[$nb] = true;
    }
}
// Guard PROFIT CENTER. Semua BPB yang masuk harus milik profit center dokumen
// ini. Yang dipakai $pcAktif - profit center SESUDAH perubahan (kalau diganti) -
// dan nilainya sudah dicocokkan ke master_pc di atas, jadi POST langsung tetap
// tidak bisa menyelundupkan kode sembarangan.
// Nomor RETUR tidak ada di bpb_new (adanya di bppb_new yang TIDAK punya kolom
// profit_center), jadi retur otomatis tidak ikut terjaring di sini - itu batas
// yang bisa dijangkau datanya, bukan kelalaian.
if (!$fail && $allBpb) {
    $pcDoc = $pcAktif;
    if ($pcDoc !== '') {
        $inListPc = implode(',', array_map(function ($x) use ($conn1) {
            return "'" . mysqli_real_escape_string($conn1, $x) . "'";
        }, array_keys($allBpb)));
        $rpc = mysqli_query($conn1, "SELECT no_bpb, MAX(profit_center) pc FROM bpb_new
            WHERE no_bpb IN ($inListPc) GROUP BY no_bpb
            HAVING MAX(profit_center) <> '" . mysqli_real_escape_string($conn1, $pcDoc) . "' LIMIT 1");
        if ($rpc && mysqli_num_rows($rpc) > 0) {
            $bad = mysqli_fetch_assoc($rpc);
            $pcBad = ($bad['pc'] === '' || $bad['pc'] === null) ? '(empty)' : $bad['pc'];
            $fail = "BPB '" . $bad['no_bpb'] . "' belongs to profit center " . $pcBad
                . ", not " . $pcDoc . ".";
        }
    }
}

if (!$fail && $allBpb) {
    $inList = implode(',', array_map(function ($x) use ($e) { return "'" . $e($x) . "'"; }, array_keys($allBpb)));
    $rc = mysqli_query($conn2, "SELECT b.no_bpb, h.doc_number FROM ir_kontrabon_bpb b
        LEFT JOIN ir_kontrabon_h h ON h.unik_code = b.unik_code
        WHERE b.no_bpb IN ($inList) AND (h.status IS NULL OR h.status <> 'Cancel') LIMIT 1");
    if ($rc && mysqli_num_rows($rc) > 0) { $dup = mysqli_fetch_assoc($rc); $fail = "BPB '" . $dup['no_bpb'] . "' is already used in saved invoice received " . ($dup['doc_number'] ?? '') . "."; }
}

// 1b) Guard supplier vs BPB (payload): nama supplier di header WAJIB sama dgn
//     supplier tiap BPB. Inilah yang MENOLAK kalau supplier diganti sesudah
//     BPB terisi. Pengaman yang sama ada di form; ini lapis server-nya.
if (!$fail) {
    foreach ($invoices as $iv) foreach (($iv['fakturs'] ?? []) as $fk) foreach (($fk['bpbs'] ?? []) as $bp) {
        $nb = trim($bp['no_bpb'] ?? ''); if ($nb === '') continue;
        $bs = trim($bp['supplier'] ?? '');
        if ($bs !== '' && $bs !== $supp) {
            $fail = "Supplier does not match: BPB '$nb' belongs to '$bs', not '$supp'. Remove that BPB first, or set the supplier back.";
            break 3;
        }
    }
}

// 1c) Cek ulang langsung ke sumbernya (bpb_new / bppb_new) supaya guard di atas
//     tidak bisa ditembus dari sisi klien. Hanya menolak bila nomornya KETEMU
//     tapi terdaftar atas supplier lain (BPB retur/RO ada di bppb_new).
if (!$fail && $allBpb) {
    $inList = implode(',', array_map(function ($x) use ($e) { return "'" . $e($x) . "'"; }, array_keys($allBpb)));
    $rs = mysqli_query($conn2, "SELECT no_bpb, supplier FROM bpb_new WHERE no_bpb IN ($inList)
        UNION SELECT no_ro   no_bpb, supplier FROM bppb_new WHERE no_ro   IN ($inList)
        UNION SELECT no_bppb no_bpb, supplier FROM bppb_new WHERE no_bppb IN ($inList)");
    $own = [];
    while ($rs && $x = mysqli_fetch_assoc($rs)) {
        $sv = trim((string) $x['supplier']);
        if ($sv !== '') $own[$x['no_bpb']][$sv] = true;
    }
    foreach ($own as $nb => $sups) {
        if (!isset($sups[$supp])) {
            $fail = "Supplier does not match: BPB '$nb' is registered for " . implode(' / ', array_keys($sups)) . ", not '$supp'.";
            break;
        }
    }
}
// 2) Guard invoice: tidak dobel dalam payload + tidak dipakai supplier sama di kontrabon lain
// No Invoice strip ("-") = penanda "tanpa nomor invoice", boleh dipakai berkali-kali
// (baik dalam 1 dokumen maupun lintas dokumen) - tidak pernah dianggap dobel.
$allInv = [];
if (!$fail) {
    foreach ($invoices as $iv) {
        $n = trim($iv['no_inv'] ?? ''); if ($n === '' || preg_match('/^-+$/', $n)) continue;
        if (isset($allInv[$n])) { $fail = "Invoice '$n' appears more than once."; break; }
        $allInv[$n] = true;
    }
}
if (!$fail && $allInv) {
    $inList = implode(',', array_map(function ($x) use ($e) { return "'" . $e($x) . "'"; }, array_keys($allInv)));
    $rc = mysqli_query($conn2, "SELECT i.no_inv, h.doc_number FROM ir_kontrabon_inv i
        JOIN ir_kontrabon_h h ON h.unik_code = i.unik_code
        WHERE i.no_inv IN ($inList) AND h.nama_supp = '" . $e($supp) . "' AND (h.status IS NULL OR h.status <> 'Cancel') LIMIT 1");
    if ($rc && mysqli_num_rows($rc) > 0) { $dup = mysqli_fetch_assoc($rc); $fail = "Invoice '" . $dup['no_inv'] . "' is already used for supplier '$supp' in invoice received " . ($dup['doc_number'] ?? '') . "."; }
}

// 3) Insert ulang detail
if (!$fail) {
    foreach ($invoices as $iv) {
        $noInv = trim($iv['no_inv'] ?? ''); if ($noInv === '') continue;
        $amt = (float) str_replace(',', '', (string) ($iv['amount'] ?? '0'));
        $okInv = mysqli_query($conn2, "INSERT INTO ir_kontrabon_inv (unik_code, doc_number, no_inv, tgl_inv, amount, create_user, create_date)
            VALUES ('" . $e($unik) . "', '" . $e($doc) . "', '" . $e($noInv) . "', " . $d($iv['tgl_inv'] ?? '') . ", $amt, '" . $e($user) . "', '$now')");
        if (!$okInv) { $fail = 'invoice: ' . mysqli_error($conn2); break; }
        $invId = (int) mysqli_insert_id($conn2); $nInv++;

        if (empty($iv['fakturs']) || !is_array($iv['fakturs'])) continue;
        foreach ($iv['fakturs'] as $fk) {
            $noFak = trim($fk['no_faktur'] ?? ''); if ($noFak === '') continue;
            $dpp = (float) ($fk['dpp'] ?? 0); $ppn = (float) ($fk['ppn'] ?? 0); $ppnbm = (float) ($fk['ppnbm'] ?? 0);
            $okFak = mysqli_query($conn2, "INSERT INTO ir_kontrabon_faktur
                (inv_id, unik_code, no_faktur, tgl_faktur, nama_supplier, npwp_supplier, pembeli, npwp_pembeli, dpp, ppn, ppnbm, status_faktur, create_user, create_date)
                VALUES ($invId, '" . $e($unik) . "', '" . $e($noFak) . "', " . $d($fk['tgl_faktur'] ?? '') . ",
                    '" . $e($fk['nama_supplier'] ?? '') . "', '" . $e($fk['npwp_supplier'] ?? '') . "', '" . $e($fk['pembeli'] ?? '') . "', '" . $e($fk['npwp_pembeli'] ?? '') . "',
                    $dpp, $ppn, $ppnbm, '" . $e($fk['status'] ?? '') . "', '" . $e($user) . "', '$now')");
            if (!$okFak) { $fail = 'faktur: ' . mysqli_error($conn2); break 2; }
            $fakId = (int) mysqli_insert_id($conn2); $nFak++;

            if (!empty($fk['bpbs']) && is_array($fk['bpbs'])) {
                $vals = [];
                foreach ($fk['bpbs'] as $bp) {
                    $noBpb = trim($bp['no_bpb'] ?? ''); if ($noBpb === '') continue;
                    $tot = (float) ($bp['total'] ?? 0); $bdpp = (float) ($bp['dpp'] ?? 0); $bppn = (float) ($bp['ppn'] ?? 0); $cur = trim($bp['curr'] ?? '');
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

// 4) Update header
if (!$fail) {
    if (!mysqli_query($conn2, "UPDATE ir_kontrabon_h SET kontrabon_date = '$tgl', document_date = '$docDate', nama_supp = '" . $e($supp) . "', deskripsi = '" . $e($desc) . "', total_amount = $total WHERE doc_number = '" . $e($doc) . "'"))
        $fail = 'header: ' . mysqli_error($conn2);
}

// 5) MIRROR ke IR: update header + re-insert invoice (rebuild penuh)
if (!$fail) {
    if (!mysqli_query($conn2, "UPDATE ir_invoice_supp_h SET tgl_penerimaan = '$tgl', nama_supp = '" . $e($supp) . "', deskripsi = '" . $e($desc) . "', total_amount = $total, updated_at = '$now' WHERE doc_number = '" . $e($doc) . "'"))
        $fail = 'IR header: ' . mysqli_error($conn2);
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

if ($fail) { mysqli_rollback($conn2); echo json_encode(['ok' => false, 'msg' => 'Update failed (rolled back): ' . $fail]); exit; }
mysqli_commit($conn2);

// Update bpb_new (upt_* dipakai laporan_pembelian): set No/Tgl Invoice & Faktur per BPB.
foreach ($bnUpd as $u) {
    if (($u[0] ?? '') === '') continue;
    // GUARD: strip "-" tidak menimpa No/Tgl Invoice/Faktur yg sudah terisi real.
    bpbnew_apply_docinfo($conn2, $u[0], $doc, $u[1], $d($u[2]), $u[3], $d($u[4]));
}

$pesanOk = "Invoice Received $doc updated: $nInv invoice(s), $nFak faktur, $nBpb BPB.";
if ($docBaru !== $docLama) {
    // Dipakai halaman edit untuk MEMBERITAHU pemakai bahwa nomornya berubah.
    $pesanOk = "Profit center changed to $pcAktif, so this document was renumbered."
        . " $nInv invoice(s), $nFak faktur, $nBpb BPB saved, $nGanti row(s) renumbered.";
}
echo json_encode([
    'ok'         => true,
    'msg'        => $pesanOk,
    'doc_number' => $doc,
    'doc_lama'   => $docLama,
    'renamed'    => ($docBaru !== $docLama),
    'pc'         => $pcAktif,
]);
mysqli_close($conn2);
