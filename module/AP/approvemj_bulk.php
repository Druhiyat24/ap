<?php
// ============================================================================
// approvemj_bulk.php — Approve/copy Memorial Journal ke SB secara BULK.
// ============================================================================
// KENAPA ADA: formverifikasimj.php dulu memanggil approvemj.php SATU KALI PER
// DOKUMEN secara berurutan (await di dalam for). Biaya terukur per dokumen:
//   ~189 ms  include conn.php (ikut membuka koneksi PostgreSQL ke 10.10.5.62
//                              yang TIDAK dipakai proses ini sama sekali)
//   ~ 84 ms  query penomoran  -> MONTH()/YEAR() = full scan 174rb baris
//   ~ 64 ms  baca tbl_memorial_journal -> TIDAK ADA index no_mj, full scan 110rb
//   ~  8 ms  cek status_memorial_journal -> TIDAK ADA index no_mj, full scan 15rb
//   => ~345 ms/dokumen. Centang 50 baris ~ 17 detik hanya overhead.
//
// Di sini semuanya dikerjakan sebagai HIMPUNAN, bukan per dokumen:
//   1x cek "sudah pernah di-approve" pakai WHERE no_mj IN (...)
//   1x query penomoran PER BULAN (bukan per dokumen), lalu dinaikkan di PHP
//   1x INSERT status_memorial_journal ... SELECT dari tabel peta
//   1x INSERT sb_memorial_journal ... JOIN peta  -> satu kali scan
//   1x INSERT sb_list_journal ... JOIN peta      -> pakai index no_journal
// Jumlah query jadi TETAP (~6), tidak lagi tumbuh mengikuti jumlah dokumen.
//
// CATATAN COLLATION (jebakan yang pernah bikin backfill 200x lebih lambat):
//   tbl_memorial_journal.no_mj & tbl_list_journal.no_journal = utf8mb4_general_ci
//   status_memorial_journal.no_mj & sb_memorial_journal.no_mj = latin1_swedish_ci
//   Tabel peta sengaja dibuat utf8mb4_general_ci supaya JOIN ke tbl_* tidak
//   memicu konversi di sisi KOLOM (yang akan mematikan pemakaian index).
//   Ke status_/sb_ hanya INSERT (tanpa JOIN), jadi konversi di situ tidak masalah.
//
// conn1 & conn2 terbukti menunjuk database yang SAMA (signalbit_bk_jul26), jadi
// seluruh proses memakai satu koneksi ($conn2) - temporary table hanya hidup di
// koneksi tempat ia dibuat, sehingga tidak boleh dicampur dua koneksi.
//
// Logika salin per dokumen DIPERTAHANKAN sama dengan approvemj.php (daftar kolom
// INSERT ... SELECT identik, hanya nomor SB diambil dari peta). approvemj.php
// sendiri TIDAK diubah.
//
// INPUT  (POST): items = JSON array [{no_mj, tgl_mj}], create_user
// OUTPUT (JSON): { ok, diminta, dibuat, dilewati, gagal, hasil[], ms }
// ============================================================================

include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

header('Content-Type: application/json');
$t0 = microtime(true);

$items       = json_decode(isset($_POST['items']) ? $_POST['items'] : '', true);
$create_user = isset($_POST['create_user']) ? $_POST['create_user'] : '';
$create_date = date('Y-m-d H:i:s');

$out = function ($arr) { echo json_encode($arr); exit; };
$e   = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };

if (!is_array($items) || count($items) === 0) {
    $out(['ok' => false, 'pesan' => 'No documents were submitted.', 'diminta' => 0,
          'dibuat' => 0, 'dilewati' => 0, 'gagal' => 0, 'hasil' => []]);
}

// ---------------------------------------------------------------------------
// 1. Rapikan input: buang duplikat & tanggal tidak valid.
// ---------------------------------------------------------------------------
$valid = [];   // no_mj => 'Y-m-d'
$hasil = [];
foreach ($items as $it) {
    $no_mj  = isset($it['no_mj']) ? trim((string) $it['no_mj']) : '';
    $tgl_in = isset($it['tgl_mj']) ? trim((string) $it['tgl_mj']) : '';
    if ($no_mj === '') { continue; }
    if (isset($valid[$no_mj])) { continue; }          // duplikat centang
    $ts = $tgl_in !== '' ? strtotime($tgl_in) : false;
    if (!$ts) {
        $hasil[] = ['no_mj' => $no_mj, 'status' => 'gagal', 'pesan' => 'Invalid MJ date'];
        continue;
    }
    $valid[$no_mj] = date('Y-m-d', $ts);
}
$diminta = count($items);
if (count($valid) === 0) {
    $out(['ok' => false, 'pesan' => 'No documents could be processed.', 'diminta' => $diminta,
          'dibuat' => 0, 'dilewati' => 0, 'gagal' => count($hasil), 'hasil' => $hasil]);
}

$inList = "'" . implode("','", array_map($e, array_keys($valid))) . "'";

// ---------------------------------------------------------------------------
// 2. Sudah pernah di-approve? SATU query utk semua (dulu: 1 query per dokumen).
//    Inilah penyebab utama "sudah diceklis tapi tidak terverifikasi" - dulu
//    dilewati DIAM-DIAM tanpa pesan apa pun.
// ---------------------------------------------------------------------------
$sudah = [];
$q = mysqli_query($conn2, "SELECT no_mj, no_mj_sb FROM status_memorial_journal WHERE no_mj IN ($inList)");
if ($q) { while ($r = mysqli_fetch_assoc($q)) { $sudah[$r['no_mj']] = $r['no_mj_sb']; } }

$proses = [];
foreach ($valid as $no_mj => $tgl) {
    if (isset($sudah[$no_mj])) {
        $hasil[] = ['no_mj' => $no_mj, 'no_mj_sb' => $sudah[$no_mj], 'status' => 'dilewati',
                    'pesan' => 'Already verified previously'];
        continue;
    }
    $proses[$no_mj] = $tgl;
}
if (count($proses) === 0) {
    $out(['ok' => true, 'diminta' => $diminta, 'dibuat' => 0,
          'dilewati' => count($sudah), 'gagal' => count($hasil) - count($sudah),
          'hasil' => $hasil, 'ms' => (int) ((microtime(true) - $t0) * 1000)]);
}

// ---------------------------------------------------------------------------
// 3. Penomoran: SEKALI per bulan, lalu dinaikkan di PHP.
//    Memakai rentang tanggal (bukan MONTH()/YEAR()) supaya bisa memanfaatkan
//    index kalau nanti index mj_date ditambahkan.
// ---------------------------------------------------------------------------
$seq = [];
$peta = [];   // no_mj => [no_mj_sb, tgl]
foreach ($proses as $no_mj => $tgl) {
    $mm = date('m', strtotime($tgl));
    $yy = date('y', strtotime($tgl));
    $key = $mm . $yy;
    if (!isset($seq[$key])) {
        $awal  = date('Y-m-01', strtotime($tgl));
        $akhir = date('Y-m-01', strtotime($awal . ' +1 month'));
        $r = mysqli_fetch_row(mysqli_query($conn2,
            "SELECT MAX(RIGHT(no_mj,5)) FROM sb_memorial_journal
              WHERE mj_date >= '$awal' AND mj_date < '$akhir'"));
        $seq[$key] = ($r && $r[0] !== null) ? (int) $r[0] : 0;
    }
    $seq[$key]++;
    $peta[$no_mj] = ['GM/NAG/' . $mm . $yy . '/' . str_pad($seq[$key], 5, '0', STR_PAD_LEFT), $tgl];
}

// ---------------------------------------------------------------------------
// 4. Tabel peta sementara (utf8mb4 - lihat catatan collation di atas).
// ---------------------------------------------------------------------------
mysqli_query($conn2, "DROP TEMPORARY TABLE IF EXISTS tmp_mj_map");
$ok = mysqli_query($conn2, "CREATE TEMPORARY TABLE tmp_mj_map (
        no_mj    VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        no_mj_sb VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
        mj_date  DATE NOT NULL,
        PRIMARY KEY (no_mj)
      ) ENGINE=MEMORY");
if (!$ok) {
    $out(['ok' => false, 'pesan' => 'Failed to create mapping table: ' . mysqli_error($conn2),
          'diminta' => $diminta, 'dibuat' => 0, 'dilewati' => count($sudah),
          'gagal' => count($proses), 'hasil' => $hasil]);
}

$vals = [];
foreach ($peta as $no_mj => $p) {
    $vals[] = "('" . $e($no_mj) . "','" . $e($p[0]) . "','" . $e($p[1]) . "')";
}
foreach (array_chunk($vals, 500) as $chunk) {
    if (!mysqli_query($conn2, "INSERT INTO tmp_mj_map (no_mj,no_mj_sb,mj_date) VALUES " . implode(',', $chunk))) {
        mysqli_query($conn2, "DROP TEMPORARY TABLE IF EXISTS tmp_mj_map");
        $out(['ok' => false, 'pesan' => 'Failed to populate mapping table: ' . mysqli_error($conn2),
              'diminta' => $diminta, 'dibuat' => 0, 'dilewati' => count($sudah),
              'gagal' => count($proses), 'hasil' => $hasil]);
    }
}

// ---------------------------------------------------------------------------
// 5. Tiga INSERT bulk. Daftar kolom SELECT identik dengan approvemj.php.
// ---------------------------------------------------------------------------
$cu = $e($create_user);
$cd = $e($create_date);

$q1 = mysqli_query($conn2, "INSERT INTO status_memorial_journal (no_mj, mj_date, no_mj_sb, status, create_by, create_date)
      SELECT m.no_mj, m.mj_date, m.no_mj_sb, 'Post', '$cu', '$cd' FROM tmp_mj_map m");
if (!$q1) {
    $err = mysqli_error($conn2);
    mysqli_query($conn2, "DROP TEMPORARY TABLE IF EXISTS tmp_mj_map");
    $out(['ok' => false, 'pesan' => 'Failed to write status: ' . $err, 'diminta' => $diminta,
          'dibuat' => 0, 'dilewati' => count($sudah), 'gagal' => count($proses), 'hasil' => $hasil]);
}

$q2 = mysqli_query($conn2, "INSERT INTO sb_memorial_journal (SELECT '' id, m.no_mj_sb, t.mj_date, t.id_cmj, t.no_coa, t.no_costcenter, t.no_reff, t.reff_date, t.buyer, t.no_ws, t.curr, t.rate, t.debit, t.credit, t.debit_idr, t.credit_idr, t.keterangan, t.status, t.create_by, t.create_date, t.post_by, t.post_date, t.cancel_by, t.cancel_date, 'Verifikasi SB2', t.profit_center
      FROM tbl_memorial_journal t JOIN tmp_mj_map m ON m.no_mj = t.no_mj)");
$err2 = $q2 ? '' : mysqli_error($conn2);

$q3 = mysqli_query($conn2, "INSERT INTO sb_list_journal (SELECT '' id, m.no_mj_sb, t.tgl_journal, t.type_journal, t.no_coa, t.nama_coa, t.no_costcenter, t.nama_costcenter, t.reff_doc, t.reff_date, t.buyer, t.no_ws, t.curr, t.rate, t.debit, t.credit, t.debit_idr, t.credit_idr, t.status, t.keterangan, t.create_by, t.create_date, t.approve_by, t.approve_date, t.cancel_by, t.cancel_date, t.profit_center
      FROM tbl_list_journal t JOIN tmp_mj_map m ON m.no_mj = t.no_journal)");
$err3 = $q3 ? '' : mysqli_error($conn2);

// ---------------------------------------------------------------------------
// 6. Verifikasi hasil dari DATA, bukan dari asumsi: nomor SB mana yang benar-
//    benar mendarat di sb_memorial_journal.
// ---------------------------------------------------------------------------
$mendarat = [];
$q = mysqli_query($conn2, "SELECT m.no_mj, m.no_mj_sb, COUNT(s.id) baris
     FROM tmp_mj_map m LEFT JOIN sb_memorial_journal s ON s.no_mj = m.no_mj_sb
     GROUP BY m.no_mj, m.no_mj_sb");
if ($q) { while ($r = mysqli_fetch_assoc($q)) { $mendarat[$r['no_mj']] = (int) $r['baris']; } }

$dibuat = 0; $gagal = 0;
foreach ($peta as $no_mj => $p) {
    $n = isset($mendarat[$no_mj]) ? $mendarat[$no_mj] : 0;
    if ($n > 0) {
        $dibuat++;
        $hasil[] = ['no_mj' => $no_mj, 'no_mj_sb' => $p[0], 'status' => 'ok', 'baris' => $n];
    } else {
        $gagal++;
        $hasil[] = ['no_mj' => $no_mj, 'no_mj_sb' => $p[0], 'status' => 'gagal',
                    'pesan' => $err2 !== '' ? $err2 : 'No rows found in tbl_memorial_journal for this number'];
    }
}

mysqli_query($conn2, "DROP TEMPORARY TABLE IF EXISTS tmp_mj_map");

echo json_encode([
    'ok'       => ($gagal === 0 && $err3 === ''),
    'diminta'  => $diminta,
    'dibuat'   => $dibuat,
    'dilewati' => count($sudah),
    'gagal'    => $gagal,
    'pesan'    => $err3 !== '' ? ('Failed to copy journal lines: ' . $err3) : '',
    'hasil'    => $hasil,
    'ms'       => (int) ((microtime(true) - $t0) * 1000),
]);

mysqli_close($conn2);
