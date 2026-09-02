<?php
// ============================================================================
// AJAX source untuk Document Handover (DataTables client-side: balikin {data:[]}).
// type = invoice  -> ir_invoice_supp_h + ir_trans_invoice_supp (query invoice_received.php)
// type = bpb      -> ir_trans_bpb (query bpb_received.php)
// Kolom seragam: no_doc, tgl, supplier, total, status, keterangan, user (+ no_reff/id_type
// untuk modal detail). Query di-copy VERBATIM dari halaman aslinya supaya identik.
// ============================================================================
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$e   = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
$type = (($_POST['type'] ?? 'invoice') === 'bpb') ? 'bpb' : 'invoice';
$supp = trim($_POST['nama_supp'] ?? 'ALL');
$stat = trim($_POST['status'] ?? 'ALL');
$ket  = trim($_POST['keterangan'] ?? 'ALL');
$sd   = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$ed   = !empty($_POST['end_date'])   ? date('Y-m-d', strtotime($_POST['end_date']))   : date('Y-m-d');

$dateCol = ($type === 'bpb') ? 'tgl_transfer' : 'tgl_penerimaan';
$conds = ["$dateCol BETWEEN '" . $e($sd) . "' AND '" . $e($ed) . "'"];
if ($supp !== 'ALL' && $supp !== '') $conds[] = "nama_supp = '" . $e($supp) . "'";
if ($stat !== 'ALL' && $stat !== '') $conds[] = "status = '" . $e($stat) . "'";
if ($ket  !== 'ALL' && $ket  !== '') $conds[] = "keterangan = '" . $e($ket) . "'";
$where = 'WHERE ' . implode(' AND ', $conds);

if ($type === 'bpb') {
    $sql = "select * from (select *,CASE
        WHEN status like '%Approved%' THEN 'Accept From Warehouse To Accounting'
        WHEN status = 'Transfer' THEN 'Transfer From Warehouse To Accounting'
        WHEN status = 'Cancel' THEN 'Cancel From Warehouse To Accounting'
        END as keterangan from (select no_transfer,tgl_transfer,no_bpb,tgl_bpb,nama_supp,no_po,curr,SUM(total) total,created_by,created_at,CONCAT(created_by,' (',created_at,')') create_user from ir_trans_bpb GROUP BY no_transfer) a inner join (select no_transfer no_trans,CASE
        WHEN s_post > 0 THEN 'Transfer'
        WHEN s_cancel > 0 and s_approved = 0 THEN 'Cancel'
        WHEN s_cancel = 0 and s_approved > 0 THEN 'Approved'
        WHEN s_cancel > 0 and s_approved > 0 THEN 'Approved Partial'
        END as status from (select a.no_transfer,COALESCE(s_post,0) s_post,COALESCE(s_cancel,0) s_cancel, COALESCE(s_approved,0) s_approved from (select no_transfer from ir_trans_bpb GROUP BY no_transfer) a left join
        (select no_transfer,COUNT(status) s_post from ir_trans_bpb where status = 'Transfer' GROUP BY no_transfer) b on b.no_transfer = a.no_transfer LEFT JOIN
        (select no_transfer,COUNT(status) s_cancel from ir_trans_bpb where status = 'Cancel' GROUP BY no_transfer) c on c.no_transfer = a.no_transfer LEFT JOIN
        (select no_transfer,COUNT(status) s_approved from ir_trans_bpb where status = 'Approved' GROUP BY no_transfer) d on d.no_transfer = a.no_transfer) a) b on b.no_trans = a.no_transfer) a $where";
} else {
    $sql = "select * from (select 'IR' id,doc_number,tgl_penerimaan,nama_supp,total_amount, IF(status != 'Cancel','Received','Cancel')status, CONCAT(created_by,' (',created_date,')') create_user, 'Received Invoice' keterangan, COALESCE(no_reff,'-') no_reff from ir_invoice_supp_h
        UNION
        select id,a.no_trans,tgl_trans,nama_supp,amount,status,create_user,CASE
        WHEN nama_trans = 'TFTA' and status like '%Approved%' THEN 'Accept From Finance To Accounting'
        WHEN nama_trans = 'TFTA' and status = 'Post' THEN 'Transfer From Finance To Accounting'
        WHEN nama_trans = 'TFTA' and status = 'Cancel' THEN 'Cancel From Finance To Accounting'
        WHEN nama_trans = 'TATP' and status like '%Approved%' THEN 'Accept From Accounting To Purchasing'
        WHEN nama_trans = 'TATP' and status = 'Post' THEN 'Transfer From Accounting To Purchasing'
        WHEN nama_trans = 'TATP' and status = 'Cancel' THEN 'Cancel From Accounting To Purchasing'
        WHEN nama_trans = 'TPTF' and status like '%Approved%' THEN 'Accept From Purchasing To Finance'
        WHEN nama_trans = 'TPTF' and status = 'Post' THEN 'Transfer From Purchasing To Finance'
        WHEN nama_trans = 'TPTF' and status = 'Cancel' THEN 'Cancel From Purchasing To Finance'
        END as keterangan, '-' from (select 'TF' id,nama_trans,no_trans,tgl_trans,nama_supp,SUM(amount) amount,CONCAT(created_by,' (',created_date,')') create_user from ir_trans_invoice_supp GROUP BY no_trans) a inner join (select no_trans,CASE
        WHEN s_post > 0 THEN 'Post'
        WHEN s_cancel > 0 and s_approved = 0 THEN 'Cancel'
        WHEN s_cancel = 0 and s_approved > 0 THEN 'Approved'
        WHEN s_cancel > 0 and s_approved > 0 THEN 'Approved Partial'
        END as status from (select a.no_trans,COALESCE(s_post,0) s_post,COALESCE(s_cancel,0) s_cancel, COALESCE(s_approved,0) s_approved from (select no_trans from ir_trans_invoice_supp GROUP BY no_trans) a left join
        (select no_trans,COUNT(status) s_post from ir_trans_invoice_supp where status = 'Post' GROUP BY no_trans) b on b.no_trans = a.no_trans LEFT JOIN
        (select no_trans,COUNT(status) s_cancel from ir_trans_invoice_supp where status = 'Cancel' GROUP BY no_trans) c on c.no_trans = a.no_trans LEFT JOIN
        (select no_trans,COUNT(status) s_approved from ir_trans_invoice_supp where status = 'Approved' GROUP BY no_trans) d on d.no_trans = a.no_trans) a) b on b.no_trans = a.no_trans) a $where";
}

$res = mysqli_query($conn2, $sql);
$data = [];
$esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES); };
while ($res && ($r = mysqli_fetch_assoc($res))) {
    if ($type === 'bpb') {
        $doc = $r['no_transfer']; $tgl = $r['tgl_transfer']; $amt = $r['total']; $reff = '-';
        $action = ''; // BPB tidak punya aksi PDF per baris (identik bpb_received.php)
    } else {
        $doc = $r['doc_number'];  $tgl = $r['tgl_penerimaan']; $amt = $r['total_amount']; $reff = $r['no_reff'] ?? '-';
        // IR -> PDF kontrabon; transfer antar bagian (TF) -> PDF transfer (identik invoice_received.php)
        $pdf = (($r['id'] ?? '') === 'IR') ? 'pdf_kontrabon_inv.php' : 'pdf_transf_inv.php';
        $action = '<a href="' . $pdf . '?doc_number=' . urlencode($doc) . '" target="_blank">'
                . '<button style="border-radius:6px" type="button" class="btn-xs btn-success">'
                . '<i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right:10px;padding-left:5px;"> Pdf</i></button></a>';
    }
    $data[] = [
        'no_doc'     => $esc($doc),
        'tgl'        => (!empty($tgl) && $tgl !== '0000-00-00') ? date('d-M-Y', strtotime($tgl)) : '-',
        'tgl_raw'    => $esc($tgl),
        'supplier'   => $esc($r['nama_supp']),
        'total'      => number_format((float) $amt, 2),
        'status'     => $esc($r['status']),
        'keterangan' => $esc($r['keterangan']),
        'user'       => $esc($r['create_user']),
        'no_reff'    => $esc($reff),
        'action'     => $action,
    ];
}
echo json_encode(['data' => $data]);
