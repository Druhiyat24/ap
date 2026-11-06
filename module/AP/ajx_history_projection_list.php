<?php
/* ============================================================================
   AP HISTORY PROJECTION - daftar dokumen potret.
   Pasangannya ap_history_projection.php.

   HANYA MEMBACA. Menu ini tidak mengubah apa pun (diminta pemakai 6 Nov
   2026) - tidak ada simpan, tidak ada batal. Tabelnya sudah menyediakan
   kolom status/cancel_by/cancel_date kalau suatu saat Cancel diperlukan
   (AR punya), tapi sengaja belum dipakai.

   Saringannya tanggal DIBUAT-nya dokumen, bukan periode laporannya - sama
   dgn History Projection di ar_dev. Dokumen periode Oktober bisa saja dibuat
   bulan Desember, dan yang dicari orang biasanya "yang saya simpan kemarin".
   ============================================================================ */

include '../../conn/conn.php';
header('Content-Type: application/json');

function hpjTgl($v)
{
    $v = trim((string) $v);
    if ($v === '') { return ''; }
    $t = strtotime(str_replace('/', '-', $v));
    return $t ? date('Y-m-d', $t) : '';
}

$mulai = hpjTgl(isset($_POST['start_date']) ? $_POST['start_date'] : '');
$akhir = hpjTgl(isset($_POST['end_date'])   ? $_POST['end_date']   : '');

if ($mulai === '' || $akhir === '') {
    echo json_encode(array('error' => 'Please fill in both the From and To dates.'));
    exit;
}
if ($mulai > $akhir) {
    echo json_encode(array('error' => 'The From date must not be later than the To date.'));
    exit;
}

/* Tabelnya BOLEH BELUM ADA - migrasinya dijalankan terpisah di produksi.
   Tanpa penjaga ini, layarnya mati total dgn pesan SQL mentah; dgn penjaga
   ini pemakai dapat kalimat yang bisa ditindaklanjuti. */
$ada = mysqli_query($conn2, "SHOW TABLES LIKE 'ap_projection_history_h'");
if (!$ada || mysqli_num_rows($ada) === 0) {
    echo json_encode(array('error' => 'The history table does not exist yet. '
        . 'Run db_migrate_LIVE_20261106_GABUNGAN.sql first.'));
    exit;
}

$fMulai = mysqli_real_escape_string($conn2, $mulai);
$fAkhir = mysqli_real_escape_string($conn2, $akhir);

$sql = mysqli_query($conn2, "
    SELECT h.doc_number, h.periode_dari, h.periode_sampai, h.nama_supp, h.type,
           h.per_tanggal, h.jml_baris, h.total_idr, h.status, h.created_by,
           h.created_at,
           (SELECT COUNT(*) FROM ap_projection_history_det d
             WHERE d.doc_number = h.doc_number) baris_nyata
    FROM ap_projection_history_h h
    WHERE DATE(h.created_at) BETWEEN '$fMulai' AND '$fAkhir'
    ORDER BY h.created_at DESC, h.id DESC");

if ($sql === false) {
    echo json_encode(array('error' => mysqli_error($conn2)));
    exit;
}

function hpjAngka($v) { return number_format((float) $v, 2, '.', ','); }
function hpjTampil($v)
{
    return (!empty($v) && $v !== '0000-00-00') ? date('d M Y', strtotime($v)) : '-';
}
function hpjAman($v) { return htmlspecialchars((string) $v, ENT_QUOTES); }

$data = array();
$no = 0;
$totalSemua = 0.0;
while ($row = mysqli_fetch_assoc($sql)) {
    $no++;
    $totalSemua += (float) $row['total_idr'];
    $data[] = array(
        'no'         => $no,
        'doc_number' => hpjAman($row['doc_number']),
        'periode'    => hpjTampil($row['periode_dari']) . ' s/d ' . hpjTampil($row['periode_sampai']),
        'type'       => hpjAman(ucfirst((string) $row['type'])),
        'supplier'   => hpjAman($row['nama_supp'] === '' ? 'ALL' : $row['nama_supp']),
        'per_tanggal'=> hpjTampil($row['per_tanggal']),
        /* Jumlah baris DIHITUNG ULANG dari tabel rinciannya, bukan dibaca
           dari kolom jml_baris. Kalau keduanya berbeda berarti ada rincian
           yang hilang - dan itu harus kelihatan, bukan tertutup angka yang
           tersimpan di kepala. */
        'baris'      => (int) $row['baris_nyata'],
        'baris_kepala' => (int) $row['jml_baris'],
        'utuh'       => ((int) $row['baris_nyata'] === (int) $row['jml_baris']),
        'total'      => hpjAngka($row['total_idr']),
        'created_by' => hpjAman($row['created_by']),
        'created_at' => !empty($row['created_at'])
                        ? date('d M Y H:i', strtotime($row['created_at'])) : '-',
    );
}

echo json_encode(array(
    'data' => $data,
    'info' => array(
        'dokumen' => count($data),
        'total'   => hpjAngka($totalSemua),
    ),
), JSON_INVALID_UTF8_SUBSTITUTE);
