<?php
// ============================================================================
// Daftar dokumen lampiran (req_dn_dok) yang masih aktif untuk SATU no_req.
// Dipakai modal "Documents" di request_debitnote.php — sekarang 1 request bisa
// punya banyak dokumen, jadi daftarnya perlu ditampilkan sebagai list, bukan
// cuma 1 file seperti UI lama.
// ============================================================================
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$e      = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
$no_req = trim($_POST['no_req'] ?? '');

$rows = [];
if ($no_req !== '') {
    $sql = mysqli_query($conn2, "select id, file_name, file_name_as, created_by, created_date
        from req_dn_dok where no_req = '" . $e($no_req) . "' and status is null order by id desc");
    while ($sql && $r = mysqli_fetch_assoc($sql)) {
        $rows[] = [
            'id'           => (int) $r['id'],
            'file_name'    => $r['file_name'],
            'file_name_as' => $r['file_name_as'],
            'created_by'   => $r['created_by'],
            'created_date' => !empty($r['created_date']) ? date('d-M-Y H:i', strtotime($r['created_date'])) : '',
        ];
    }
}

echo json_encode(['data' => $rows]);
