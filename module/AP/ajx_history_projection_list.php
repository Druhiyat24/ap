<?php
/* ============================================================================
   AP HISTORY PROJECTION - daftar dokumen potret.
   Pasangannya ap_history_projection.php.

   BERKAS INI HANYA MEMBACA. Pembatalan dokumen ada di berkas terpisah
   (batal_history_projection.php) - diminta pemakai 6 Nov 2026 menyusul,
   mengikuti History Projection di ar_dev.

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

/* Saringan Type (diminta pemakai 6 Nov 2026). 'ALL' = tidak menyaring.
   Nilainya dipilih dari daftar tetap, bukan diteruskan apa adanya ke SQL -
   isian yang tidak dikenal diperlakukan sbg ALL, bukan ditempelkan. */
$type = isset($_POST['type']) ? strtolower(trim((string) $_POST['type'])) : 'all';
if (!in_array($type, array('all', 'daily', 'weekly', 'monthly'), true)) { $type = 'all'; }

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
$fType  = ($type === 'all')
        ? ''
        : "AND h.type = '" . mysqli_real_escape_string($conn2, $type) . "'";

$sql = mysqli_query($conn2, "
    SELECT h.doc_number, h.periode_dari, h.periode_sampai, h.nama_supp, h.type,
           h.per_tanggal, h.jml_baris, h.total_idr, h.status, h.created_by,
           h.created_at,
           (SELECT COUNT(*) FROM ap_projection_history_det d
             WHERE d.doc_number = h.doc_number) baris_nyata
    FROM ap_projection_history_h h
    WHERE DATE(h.created_at) BETWEEN '$fMulai' AND '$fAkhir'
      $fType
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
    /* Susunan kolomnya mengikuti History Projection di ar_dev: periode
       dipecah jadi FROM dan TO (dulu satu kolom gabungan), Type jadi lencana
       berwarna, dan jumlah barisnya disebut "Total Voucher" - padanan
       "Total Invoice" di AR.

       Kolom Supplier DIPERTAHANKAN walau AR tidak punya padanannya: potret
       AP bisa disaring per supplier, jadi tanpa kolom itu dua dokumen
       periode sama yang beda supplier tampak persis sama di daftar. */
    $data[] = array(
        'no'         => $no,
        'doc_number' => hpjAman($row['doc_number']),
        'periode_dari'   => hpjTampil($row['periode_dari']),
        'periode_sampai' => hpjTampil($row['periode_sampai']),
        'type'       => hpjAman(strtolower((string) $row['type'])),
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
        'type'    => $type,
    ),
), JSON_INVALID_UTF8_SUBSTITUTE);
