<?php
// ============================================================================
// Sumber AJAX DataTables untuk LIST REQUEST DEBIT NOTE (request_debitnote.php).
//
// Dulu tabelnya dirender langsung di halaman (POST -> reload penuh). Dipindah ke
// AJAX supaya: Search tidak memuat ulang halaman, overlay loading bisa muncul,
// dan markup barisnya cuma dibentuk di SATU tempat (di sini).
//
// Data pengenal baris (no_req / tgl / supplier / nama berkas) ditempel sebagai
// atribut data-* di <tr> lewat createdRow di halaman, BUKAN lagi lewat kolom
// tersembunyi yang dibaca td:eq(8)/td:eq(9) — kolom yang di-hide DataTables
// dihapus dari DOM, jadi cara lama itu tidak bisa dipakai lagi.
//
// Balikan: { data: [ {...}, ... ], total: n }
// ============================================================================
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$val = function ($k, $def = 'ALL') { return isset($_POST[$k]) && trim($_POST[$k]) !== '' ? trim($_POST[$k]) : $def; };

$nama_supp = $val('nama_supp');
$status    = $val('status');
// Hak akses Edit DISAMAKAN dgn Create (dicek sekali di request_debitnote.php,
// dikirim lewat parameter ini) — endpoint ajax ini sendiri tidak mulai session,
// jadi tidak query useraccess ulang di sini, sama seperti gerbang Create yang
// sudah ada (cuma dicek di level halaman, bukan di tiap baris ajax).
$canEdit = !empty($_POST['can_edit']) && $_POST['can_edit'] == '1';
$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$end_date   = !empty($_POST['end_date'])   ? date('Y-m-d', strtotime($_POST['end_date']))   : date('Y-m-d');

$e  = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
$dF = $e($start_date);
$dT = $e($end_date);

// Filter dipertahankan persis seperti versi lama (kombinasi ALL / bukan ALL).
$where = "where tgl_req between '$dF' and '$dT'";
if ($nama_supp !== 'ALL') { $where .= " and nama_supp = '" . $e($nama_supp) . "'"; }
if ($status    !== 'ALL') { $where .= " and a.status = '" . $e($status) . "'"; }

$sql = mysqli_query($conn2, "select a.no_req,tgl_req,nama_supp,total_amount,deskripsi,a.status,
        CONCAT(a.created_by,' (',a.created_date,')') create_user,
        if(no_dn is null,'-',no_dn) no_dn,
        coalesce(b.doc_count,0) doc_count
    from req_dn_h a
    left join (select no_req, COUNT(*) doc_count from req_dn_dok where status is null GROUP BY no_req) b on b.no_req = a.no_req
    $where GROUP BY a.id ORDER BY a.id DESC");

if ($sql === false) {
    echo json_encode(['data' => [], 'error' => mysqli_error($conn2)]);
    exit;
}

$tip  = ' data-toggle="tooltip" data-placement="top" ';
$data = [];

while ($row = mysqli_fetch_assoc($sql)) {
    $st        = $row['status'];
    $docCount  = (int) $row['doc_count'];
    $noReq     = $row['no_req'];

    // Badge status: Cancel -> merah, Processed -> hijau, sisanya (Post dll) -> amber.
    $lc   = strtolower($st);
    $bcls = ($lc === 'cancel') ? 'cancel' : (($lc === 'processed') ? 'done' : 'process');

    // Kolom Action: tombol ikon ringkas, dipisah kelompok DOKUMEN | REQUEST.
    // Pakai CLASS (bukan id) karena satu halaman berisi banyak baris — id ganda
    // itu HTML tidak sah dan bikin delegasi event gampang salah sasaran.
    //
    // Dulu tombol upload otomatis DIGANTI jadi preview/delete begitu SATU
    // dokumen ada, jadi tidak bisa upload dokumen kedua tanpa hapus yg pertama
    // dulu. Sekarang SATU tombol "Documents" selalu ada (baik 0 atau banyak
    // dokumen) yg membuka modal berisi daftar dokumen + form upload tambahan.
    $act = '';
    if ($st === 'Cancel') {
        $act = '<span class="kb-locked" title="This request has been cancelled."><span class="lk"><i class="fa fa-ban"></i> Cancelled</span></span>';
    } else {
        $docBadge = $docCount > 0 ? ' <span class="badge badge-light" style="margin-left:3px;">' . $docCount . '</span>' : '';
        $act .= '<button type="button" class="btn btn-warning js-manage-doc"' . $tip . 'title="Manage documents (' . $docCount . ')"><i class="fa fa-file-pdf-o"></i>' . $docBadge . '</button>';
        $act .= '<span class="act-sep"></span>';
        $act .= '<a href="pdf_req_dn.php?no_req=' . rawurlencode($noReq) . '" target="_blank" class="btn btn-success"' . $tip . 'title="Print request (PDF)"><i class="fa fa-print"></i></a>';
        // Edit cuma boleh selama status masih "Post" — begitu "Processed", Debit
        // Note sudah dibuat dari request ini di modul lain, jadi item-nya
        // dikunci (tidak boleh diutak-atik lagi lewat form edit).
        if ($st === 'Post' && $canEdit) {
            $act .= '<button type="button" class="btn btn-primary js-edit-req"' . $tip . 'title="Edit this request (add/remove BPB)"><i class="fa fa-pencil"></i></button>';
        }
        if ($st === 'Post') {
            $act .= '<button type="button" class="btn btn-danger js-cancel-req"' . $tip . 'title="Cancel this request"><i class="fa fa-trash"></i></button>';
        }
    }

    $data[] = [
        'no_req'       => '<span class="kb-doc js-detail" style="cursor:pointer;" title="Click to see the detail"><i class="fa fa-file-text-o"></i>' . $esc($noReq) . '</span>',
        'tgl_req'      => !empty($row['tgl_req']) ? date('d-M-Y', strtotime($row['tgl_req'])) : '-',
        'nama_supp'    => $esc($row['nama_supp']),
        'total_amount' => '<span class="kb-amt">' . number_format((float) $row['total_amount'], 2) . '</span>',
        'status'       => '<span class="kb-badge ' . $bcls . '">' . $esc($st) . '</span>',
        'no_dn'        => $esc($row['no_dn']),
        'create_user'  => '<span class="kb-user">' . $esc($row['create_user']) . '</span>',
        'action'       => '<div class="act-icons">' . $act . '</div>',
        // Dipakai createdRow utk menempel data-* di <tr> (bukan jadi kolom tampil).
        '_no_req'      => $noReq,
        '_tgl'         => !empty($row['tgl_req']) ? date('d-M-Y', strtotime($row['tgl_req'])) : '',
        '_supp'        => $row['nama_supp'],
        '_status'      => $st,
        '_user'        => $row['create_user'],
        '_amount'      => number_format((float) $row['total_amount'], 2),
        '_no_dn'       => $row['no_dn'],
    ];
}

echo json_encode(['data' => $data, 'total' => count($data)]);
