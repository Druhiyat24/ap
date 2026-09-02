<?php
// Validasi + RESERVASI hasil scan BPB untuk Kontrabon New.
//  1) BPB harus ada di bpb_new UNTUK supplier terpilih (ambil tgl_bpb & total).
//  2) BPB tidak boleh sudah dipakai di kontrabon tersimpan (ir_kontrabon_bpb).
//  3) BPB di-RESERVE (ir_kontrabon_bpb_reserve, no_bpb UNIQUE) supaya user/draft
//     lain tidak bisa memakai BPB yang sama. INSERT unik = race-safe.
// Balikan JSON.
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$user       = $_SESSION['username'] ?? '';
$no_bpb     = trim($_POST['no_bpb'] ?? '');
$supplier   = trim($_POST['supplier'] ?? '');
$unik       = trim($_POST['unik_code'] ?? '');
$excludeDoc = trim($_POST['exclude_doc'] ?? '');   // mode edit: abaikan BPB milik kontrabon ini sendiri
$noReserve  = !empty($_POST['no_reserve']);         // mode edit: jangan reserve
$now        = date('Y-m-d H:i:s');

if ($no_bpb === '')   { echo json_encode(['ok' => false, 'msg' => 'BPB is empty.']); exit; }
if ($supplier === '') { echo json_encode(['ok' => false, 'msg' => 'Please select the Supplier first.']); exit; }

// Lepas reservasi basi (draft yang ditinggal >24 jam) supaya BPB tidak terkunci selamanya.
mysqli_query($conn2, "DELETE FROM ir_kontrabon_bpb_reserve WHERE create_date < DATE_SUB(NOW(), INTERVAL 24 HOUR)");

$bpb_esc  = mysqli_real_escape_string($conn1, $no_bpb);
$supp_esc = mysqli_real_escape_string($conn1, $supplier);
$bpb_esc2 = mysqli_real_escape_string($conn2, $no_bpb);

// 1) Valid untuk supplier terpilih? DPP = SUM(qty*price), PPN = SUM(qty*price*tax/100),
//    Total = DPP + PPN (tax = persen). No PO = pono.
$r = mysqli_query($conn1, "SELECT no_bpb, MAX(supplier) supplier, GROUP_CONCAT(DISTINCT pono SEPARATOR ', ') no_po,
    MAX(tgl_bpb) tgl_bpb, MAX(curr) curr, MAX(is_invoiced) is_invoiced,
    ROUND(SUM(qty * price), 2) dpp,
    ROUND(SUM((qty * price) * (tax / 100)), 2) ppn,
    ROUND(SUM((qty * price) + ((qty * price) * (tax / 100))), 2) total
    FROM bpb_new WHERE no_bpb = '$bpb_esc' AND supplier = '$supp_esc' GROUP BY no_bpb LIMIT 1");

$is_retur = false;
if ($r && mysqli_num_rows($r) > 0) {
    $row = mysqli_fetch_assoc($r);
} else {
    // FALLBACK RETUR: nomor RO/BPPB dicari di bppb_new (untuk supplier terpilih).
    $rr_ = mysqli_query($conn1, "SELECT '$bpb_esc' no_bpb, MAX(supplier) supplier,
        GROUP_CONCAT(DISTINCT no_po SEPARATOR ', ') no_po, MAX(tgl_bppb) tgl_bpb, MAX(curr) curr,
        MAX(is_invoiced) is_invoiced,
        ROUND(SUM(qty * price), 2) dpp,
        ROUND(SUM((qty * price) * (tax / 100)), 2) ppn,
        ROUND(SUM((qty * price) + ((qty * price) * (tax / 100))), 2) total
        FROM bppb_new WHERE (no_ro = '$bpb_esc' OR no_bppb = '$bpb_esc') AND supplier = '$supp_esc' LIMIT 1");
    $tmp = ($rr_ && mysqli_num_rows($rr_) > 0) ? mysqli_fetch_assoc($rr_) : null;
    if ($tmp && $tmp['supplier'] !== null) {
        $row = $tmp;
        $is_retur = true;
    } else {
        // Ada untuk supplier lain? (bpb_new / bppb_new) -> pesan lebih jelas.
        $r2 = mysqli_query($conn1, "SELECT supplier FROM bpb_new WHERE no_bpb = '$bpb_esc'
            UNION SELECT supplier FROM bppb_new WHERE no_ro = '$bpb_esc' OR no_bppb = '$bpb_esc' LIMIT 1");
        if ($r2 && mysqli_num_rows($r2) > 0) {
            $other = mysqli_fetch_assoc($r2)['supplier'];
            echo json_encode(['ok' => false, 'msg' => "'$no_bpb' belongs to another supplier (" . $other . "), not the selected one."]);
        } else {
            echo json_encode(['ok' => false, 'msg' => "'$no_bpb' not found (BPB / RO)."]);
        }
        exit;
    }
}

// 2) Sudah dipakai di kontrabon TERSIMPAN? (doc_number ada di header, JOIN via unik_code)
$excCond = ($excludeDoc !== '') ? " AND (h.doc_number IS NULL OR h.doc_number <> '" . mysqli_real_escape_string($conn2, $excludeDoc) . "')" : '';
$rf = mysqli_query($conn2, "SELECT h.doc_number FROM ir_kontrabon_bpb b
    LEFT JOIN ir_kontrabon_h h ON h.unik_code = b.unik_code
    WHERE b.no_bpb = '$bpb_esc2' AND (h.status IS NULL OR h.status <> 'Cancel')$excCond LIMIT 1");
if ($rf && mysqli_num_rows($rf) > 0) {
    $dn = mysqli_fetch_assoc($rf)['doc_number'] ?? '';
    echo json_encode(['ok' => false, 'msg' => "BPB '$no_bpb' is already used in a saved invoice received" . ($dn ? " ($dn)" : '') . "."]);
    exit;
}

// 3) RESERVE (race-safe via UNIQUE no_bpb). Di mode edit (no_reserve) dilewati.
if (!$noReserve) {
    $ins = mysqli_query($conn2, "INSERT INTO ir_kontrabon_bpb_reserve (no_bpb, create_user, unik_code, nama_supp, create_date)
        VALUES ('$bpb_esc2', '" . mysqli_real_escape_string($conn2, $user) . "', '" . mysqli_real_escape_string($conn2, $unik) . "', '" . mysqli_real_escape_string($conn2, $supplier) . "', '$now')");
    if (!$ins) {
        // Gagal -> kemungkinan besar duplikat (sudah di-reserve). Cari siapa.
        $rr = mysqli_query($conn2, "SELECT create_user, unik_code FROM ir_kontrabon_bpb_reserve WHERE no_bpb = '$bpb_esc2' LIMIT 1");
        if ($rr && mysqli_num_rows($rr) > 0) {
            $ex = mysqli_fetch_assoc($rr);
            if ($ex['create_user'] === $user && $ex['unik_code'] === $unik) {
                echo json_encode(['ok' => false, 'msg' => "BPB '$no_bpb' is already scanned in this invoice received (a BPB can only be used once)."]);
            } else {
                echo json_encode(['ok' => false, 'msg' => "BPB '$no_bpb' is being used by another user (" . $ex['create_user'] . "). Not available."]);
            }
        } else {
            echo json_encode(['ok' => false, 'msg' => 'Failed to reserve BPB: ' . mysqli_error($conn2)]);
        }
        exit;
    }
}

// OK -> reserved untuk draft ini. RETUR selalu NEGATIF (mengurangi total kontrabon).
$sign = $is_retur ? -1 : 1;
echo json_encode([
    'ok'          => true,
    'no_bpb'      => $row['no_bpb'],
    'no_po'       => $row['no_po'],
    'supplier'    => $row['supplier'],
    'tgl_bpb'     => $row['tgl_bpb'],
    'dpp'         => $sign * (float) $row['dpp'],
    'ppn'         => $sign * (float) $row['ppn'],
    'total'       => $sign * (float) $row['total'],
    'curr'        => $row['curr'] ?: 'IDR',
    'is_invoiced' => $row['is_invoiced'],
    'is_retur'    => $is_retur,
]);
