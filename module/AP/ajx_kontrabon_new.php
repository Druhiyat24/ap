<?php
// ============================================================================
// AJAX source untuk List Kontrabon (DataTables client-side, pola app: balikin
// {data:[...], footer:{amount}}). Status = MIRROR IR (ir_invoice_supp_h),
// Edit/Cancel dikunci jadi badge "Locked" kalau IR sudah bergerak dari 'Received'
// atau ada transfer aktif (TFTA/TATP/TPTF non-Cancel).
// ============================================================================
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$val = function ($k, $def = 'ALL') { return isset($_POST[$k]) && trim($_POST[$k]) !== '' ? trim($_POST[$k]) : $def; };
$esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

$fSupp   = $val('nama_supp');
$fStatus = $val('status');
$fStart  = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$fEnd    = !empty($_POST['end_date'])   ? date('Y-m-d', strtotime($_POST['end_date']))   : date('Y-m-d');

// Effective status = COALESCE(IR, kontrabon).
$eff = "COALESCE(ih.status, kh.status)";

$where = "WHERE COALESCE(kh.document_date, kh.kontrabon_date) BETWEEN '" . mysqli_real_escape_string($conn2, $fStart) . "' AND '" . mysqli_real_escape_string($conn2, $fEnd) . "'";
if ($fSupp !== 'ALL')   { $where .= " AND kh.nama_supp = '" . mysqli_real_escape_string($conn2, $fSupp) . "'"; }
if ($fStatus !== 'ALL') { $where .= " AND $eff = '" . mysqli_real_escape_string($conn2, $fStatus) . "'"; }

// Tabel transfer mungkin belum ada di DB lokal -> deteksi dulu (fallback 0).
$tt = mysqli_query($conn2, "SHOW TABLES LIKE 'ir_trans_invoice_supp'");
$transExpr = ($tt && mysqli_num_rows($tt) > 0)
    ? "(SELECT COUNT(*) FROM ir_trans_invoice_supp t WHERE t.doc_number = kh.doc_number AND t.status <> 'Cancel')"
    : "0";

$res = mysqli_query($conn2, "SELECT kh.doc_number, kh.kontrabon_date, kh.document_date, kh.no_reff, kh.nama_supp, kh.total_amount, kh.amount_add_pv,
        kh.status AS kb_status, kh.create_user, kh.create_date, ih.status AS ir_status,
        $transExpr AS trans_cnt,
        (SELECT COUNT(*) FROM ir_kontrabon_bpb b WHERE b.unik_code = kh.unik_code) AS bpb_cnt
    FROM ir_kontrabon_h kh
    LEFT JOIN ir_invoice_supp_h ih ON ih.doc_number = kh.doc_number
    $where ORDER BY kh.id DESC");

$data = [];
$sum  = 0.0;
while ($res && ($r = mysqli_fetch_assoc($res))) {
    $doc = $r['doc_number'];
    $kb  = $r['kb_status'] ?? 'Draft';
    $eStat = ($kb === 'Cancel') ? 'Cancel' : (!empty($r['ir_status']) ? $r['ir_status'] : $kb);
    $lc  = strtolower($eStat);
    $bcls = ($lc === 'cancel') ? 'cancel' : (($lc === 'draft') ? 'draft' : (($lc === 'received') ? 'received' : 'process'));
    $canModify = ($eStat === 'Draft' || $eStat === 'Received') && (int) ($r['trans_cnt'] ?? 0) === 0;

    // Kelengkapan IR: yang menghalangi transfer HANYA BPB. Nomor faktur boleh berupa
    // strip "-" (artinya tanpa nomor faktur), jadi faktur tidak dijadikan syarat.
    // (IR Cancel tidak diberi notif.)
    $bpbCnt = (int) ($r['bpb_cnt'] ?? 0);
    $incompleteNote = '';
    if ($eStat !== 'Cancel' && $bpbCnt === 0) {
        $incompleteNote = '<small class="kb-incomplete" style="display:block;color:#dc2626;font-weight:600;font-size:10px;margin-top:3px;">'
            . '<i class="fa fa-exclamation-triangle"></i> IR belum ada BPB</small>';
    }

    // Status badge
    $statusHtml = '<span class="kb-badge ' . $bcls . '">' . $esc($eStat) . '</span>';

    // Action
    $act = '<button type="button" class="btn btn-info btn-act show-kb" data-doc="' . $esc($doc) . '"><i class="fa fa-eye"></i> Show</button>';
    if ($eStat !== 'Cancel') {
        if ($canModify) {
            $act .= '<a href="edit_kontrabon_new.php?doc=' . rawurlencode($doc) . '" class="btn btn-warning btn-act"><i class="fa fa-pencil"></i> Edit</a>';
            $act .= '<button type="button" class="btn btn-danger btn-act cancel-kb" data-doc="' . $esc($doc) . '"><i class="fa fa-ban"></i> Cancel</button>';
        } else {
            $act .= '<span class="kb-locked" title="Already used in the Invoice Received flow (' . $esc($eStat) . ') — can no longer be edited or cancelled.">'
                 . '<span class="lk"><i class="fa fa-lock"></i> Locked</span><small>in IR process</small></span>';
        }
    }

    $sum   += (float) $r['total_amount'];
    $addpv  = (float) ($r['amount_add_pv'] ?? 0);
    $grand  = (float) $r['total_amount'] + $addpv;
    $data[] = [
        'doc_number'     => '<span class="kb-doc"><i class="fa fa-file-text-o"></i>' . $esc($doc) . '</span>' . $incompleteNote,
        'document_date'  => !empty($r['document_date']) ? date('d-M-Y', strtotime($r['document_date'])) : '-',
        'kontrabon_date' => !empty($r['kontrabon_date']) ? date('d-M-Y', strtotime($r['kontrabon_date'])) : '-',
        'no_reff'        => $esc($r['no_reff'] ?? ''),
        'nama_supp'      => $esc($r['nama_supp'] ?? ''),
        'total_amount'   => '<span class="kb-amt">' . number_format((float) $r['total_amount'], 2) . '</span>',
        'amount_add_pv'  => '<span class="kb-amt" style="color:' . ($addpv > 0 ? '#059669' : ($addpv < 0 ? '#dc2626' : '#94a3b8')) . ';">' . ($addpv >= 0 ? '+' : '-') . number_format(abs($addpv), 2) . '</span>',
        'grand_total'    => '<span class="kb-amt">' . number_format($grand, 2) . '</span>',
        'status'         => $statusHtml,
        'create_user'    => '<span class="kb-user">' . $esc($r['create_user'] ?? '') . '</span>',
        'create_date'    => !empty($r['create_date']) ? date('d-M-Y H:i', strtotime($r['create_date'])) : '-',
        'action'         => '<div class="act-wrap">' . $act . '</div>',
    ];
}

echo json_encode(['data' => $data, 'footer' => ['amount' => number_format($sum, 2)]]);
