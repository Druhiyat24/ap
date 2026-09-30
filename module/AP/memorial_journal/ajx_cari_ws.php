<?php
// ============================================================================
// ajx_cari_ws.php — pencarian nomor WS untuk select2 di edit_memorial_journal_new.php
//
// KENAPA DICARI, BUKAN DIPRABUAT:
// Daftar WS punya 6.517 baris, tapi kolom no_ws cuma terisi di 51 dari 38.119
// baris journal 2026 (0,13%). Halaman edit LAMA menulis seluruh 6.517 opsi itu
// ke SETIAP baris journal - untuk dokumen 672 baris jadi 4,4 juta elemen
// <option> hanya untuk kolom yang hampir tidak pernah dipakai. Itu penyumbang
// terbesar lambatnya halaman edit lama.
// ============================================================================

include '../../../conn/conn.php';
header('Content-Type: application/json');

$q = trim((string) ($_GET['q'] ?? ''));

// Di bawah 2 huruf tidak dicari: select2 sudah dipasang minimumInputLength 2,
// tapi endpoint-nya tetap dijaga sendiri supaya pemanggilan langsung tanpa kata
// kunci tidak menarik seluruh tabel.
if (strlen($q) < 2) { echo json_encode([]); exit; }

$esc = mysqli_real_escape_string($conn1, $q);

// LIMIT 50: select2 hanya menampilkan sebagian, dan pengguna mempersempit
// dengan mengetik lagi. Tanpa batas, kata kunci pendek seperti "20" menarik
// ribuan baris tiap ketikan.
$sql = "select distinct a.kpno as id, a.kpno as text
        from act_costing a
        inner join mastersupplier b on b.id_supplier = a.id_buyer
        where a.cost_date >= '2022-01-01' and a.kpno like '%" . $esc . "%'
        order by a.kpno limit 50";

$res  = mysqli_query($conn1, $sql);
$data = [];
while ($res && $r = mysqli_fetch_assoc($res)) {
    $data[] = ['id' => $r['id'], 'text' => $r['text']];
}

echo json_encode($data);
