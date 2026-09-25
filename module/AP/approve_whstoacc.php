<?php
// ============================================================================
// Accept Transfer BPB / Surat Jalan (Warehouse -> Accounting).
//
// `no_bpb` boleh berupa SATU nilai (pemanggil lama) atau ARRAY (batch).
// Dulu halaman pemanggil mengirim satu request PER BARIS di dalam .each(),
// lalu langsung window.location begitu respons PERTAMA datang - sehingga
// seluruh request yang masih berjalan dibatalkan browser dan hanya sebagian
// baris yang tersimpan (kasus TBPB/NAG/0926/01749: 35 dari 84 baris).
// Sekarang seluruh nomor dikirim sekali dan diproses dalam SATU statement.
// ============================================================================
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json');

$no_dok       = isset($_POST['no_dok']) ? trim((string) $_POST['no_dok']) : '';
$approve_user = isset($_POST['approve_user']) ? trim((string) $_POST['approve_user']) : '';
$confirm_date = date('Y-m-d H:i:s');

// Normalkan jadi array, buang yang kosong & duplikat.
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
        set status = 'Approved',
            approved_by = '" . mysqli_real_escape_string($conn2, $approve_user) . "',
            approved_date = '" . mysqli_real_escape_string($conn2, $confirm_date) . "'
        where no_transfer = '$dok_esc' and no_bpb IN (" . implode(',', $in) . ")";

$execute = mysqli_query($conn2, $sql);

if (!$execute) {
    echo json_encode(['ok' => false, 'pesan' => 'Gagal menyimpan: ' . mysqli_error($conn2), 'diubah' => 0]);
    exit;
}

// Dihitung ULANG dari database, bukan dari affected_rows: baris yang statusnya
// sudah 'Approved' tidak terhitung oleh affected_rows padahal hasil akhirnya
// benar. Angka inilah yang dipakai halaman untuk melapor ke user.
$cek = mysqli_query($conn2, "select count(*) n from ir_trans_bpb
    where no_transfer = '$dok_esc' and no_bpb IN (" . implode(',', $in) . ") and status = 'Approved'");
$sudah = $cek ? (int) mysqli_fetch_assoc($cek)['n'] : 0;
$diminta = count($list);

echo json_encode([
    'ok'      => ($sudah === $diminta),
    'diubah'  => $sudah,
    'diminta' => $diminta,
    'pesan'   => $sudah === $diminta
        ? "Accept berhasil: $sudah dari $diminta baris."
        : "Accept BELUM lengkap: baru $sudah dari $diminta baris.",
]);

mysqli_close($conn2);
