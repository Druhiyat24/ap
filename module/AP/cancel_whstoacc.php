<?php
// ============================================================================
// Cancel Transfer BPB / Surat Jalan (Warehouse -> Accounting).
//
// `no_bpb` boleh berupa SATU nilai (dipakai maintain-bpb.php) atau ARRAY
// (batch dari form_approve_bpb.php / form_approve_sj.php). Lihat catatan
// lengkap soal kenapa dijadikan batch di approve_whstoacc.php.
// ============================================================================
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json');

$no_dok       = isset($_POST['no_dok']) ? trim((string) $_POST['no_dok']) : '';
$approve_user = isset($_POST['approve_user']) ? trim((string) $_POST['approve_user']) : '';
$confirm_date = date('Y-m-d H:i:s');

$raw  = isset($_POST['no_bpb']) ? $_POST['no_bpb'] : [];
$list = [];
foreach ((is_array($raw) ? $raw : [$raw]) as $v) {
    $v = trim((string) $v);
    if ($v !== '') { $list[$v] = true; }
}
$list = array_keys($list);

if ($no_dok === '' || !$list) {
    echo json_encode(['ok' => false, 'pesan' => 'Nomor dokumen atau daftar BPB kosong.', 'diubah' => 0]);
    exit;
}

$dok_esc = mysqli_real_escape_string($conn2, $no_dok);
$in = [];
foreach ($list as $v) { $in[] = "'" . mysqli_real_escape_string($conn2, $v) . "'"; }

$sql = "update ir_trans_bpb
        set status = 'Cancel',
            cancel_by = '" . mysqli_real_escape_string($conn2, $approve_user) . "',
            cancel_date = '" . mysqli_real_escape_string($conn2, $confirm_date) . "'
        where no_transfer = '$dok_esc' and no_bpb IN (" . implode(',', $in) . ")";

$execute = mysqli_query($conn2, $sql);

// CATATAN: di versi lama ada statement kedua yang mengosongkan bpb.stat_trf:
//   update bpb a INNER JOIN ir_trans_bpb b ON b.no_bpb = a.bpbno_int
//   SET a.stat_trf = null where b.no_transfer = '$no_transfer'
// Variabel $no_transfer TIDAK PERNAH didefinisikan di berkas ini (yang ada
// $no_dok), sehingga kondisinya selalu no_transfer = '' dan TIDAK ADA satu
// baris pun yang tersentuh - diverifikasi ke produksi: 0 baris ir_trans_bpb
// yang no_transfer-nya kosong. Jadi statement itu memang mati sejak awal.
// Sengaja TIDAK dihidupkan di sini: WHERE-nya per no_transfer (bukan per
// no_bpb), jadi membatalkan SATU BPB akan ikut mereset stat_trf SELURUH BPB
// dalam transfer tsb. Perlu keputusan user dulu sebelum diaktifkan.

if (!$execute) {
    echo json_encode(['ok' => false, 'pesan' => 'Gagal menyimpan: ' . mysqli_error($conn2), 'diubah' => 0]);
    exit;
}

$cek = mysqli_query($conn2, "select count(*) n from ir_trans_bpb
    where no_transfer = '$dok_esc' and no_bpb IN (" . implode(',', $in) . ") and status = 'Cancel'");
$sudah = $cek ? (int) mysqli_fetch_assoc($cek)['n'] : 0;
$diminta = count($list);

echo json_encode([
    'ok'      => ($sudah === $diminta),
    'diubah'  => $sudah,
    'diminta' => $diminta,
    'pesan'   => $sudah === $diminta
        ? "Cancel berhasil: $sudah dari $diminta baris."
        : "Cancel BELUM lengkap: baru $sudah dari $diminta baris.",
]);

mysqli_close($conn2);
