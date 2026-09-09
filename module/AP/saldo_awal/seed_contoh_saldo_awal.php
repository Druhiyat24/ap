<?php
// ============================================================================
// SEED CONTOH saldo awal PPN Masukan (dijalankan dari CLI / browser sekali saja).
//
// Mengambil 100 item dengan saldo terbesar dari JURNAL LAMA (COA 1.52.04,
// tgl_journal < 2026-01-01) — yaitu justru bagian yang sengaja tidak lagi dibaca
// report — lalu memasukkannya ke tbl_ppn_saldo_awal sebagai contoh isi.
//
// Ditandai create_by = 'sample' supaya gampang dikenali & dihapus:
//     DELETE FROM tbl_ppn_saldo_awal WHERE create_by = 'sample';
// atau lewat tombol Clear All / hapus per baris di modal Beginning Balance.
//
// Aman diulang: baris 'sample' lama dihapus dulu, jadi tidak menumpuk.
// ============================================================================
include __DIR__ . '/../../../conn/conn.php';
date_default_timezone_set('Asia/Jakarta');
ini_set('max_execution_time', 0);

$AS_OF = '2026-01-01';
$LIMIT = 100;
$cli   = (php_sapi_name() === 'cli');
$br    = $cli ? "\n" : "<br>\n";

$e = function ($s) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $s); };

mysqli_query($conn2, "DELETE FROM tbl_ppn_saldo_awal WHERE create_by = 'sample'");
echo "baris contoh lama dihapus: " . mysqli_affected_rows($conn2) . $br;

// Saldo per item sebelum 2026-01-01, kunci sama dgn report (faktur, atau no_journal)
$sql = "SELECT
        MIN(no_journal) si_no,
        MIN(tgl_journal) si_date,
        MAX(COALESCE(supplier, '')) supplier,
        MAX(COALESCE(faktur_pajak, '')) faktur_pajak,
        MAX(tgl_faktur_pajak) tgl_faktur_pajak,
        MAX(COALESCE(profit_center, '')) profit_center,
        MAX(curr) curr, MAX(rate) rate,
        SUM(debit - credit) ocy,
        SUM((debit - credit) * rate) idr
    FROM tbl_list_journal
    WHERE no_coa = '1.52.04' AND tgl_journal < '" . $e($AS_OF) . "'
      /* dibatasi yang PUNYA nomor faktur: itu yang bentuknya paling mirip isi file
         saldo awal sungguhan, dan yang nanti bisa berpasangan dgn deduction GM.
         (baris lama tanpa faktur mayoritas jurnal reklas bernilai sangat besar,
          kurang cocok dijadikan contoh) */
      AND COALESCE(faktur_pajak, '') <> ''
    GROUP BY CASE WHEN COALESCE(faktur_pajak, '') <> '' THEN CONCAT('FP|', faktur_pajak)
                  ELSE CONCAT('JR|', no_journal) END
    HAVING SUM((debit - credit) * rate) <> 0
    ORDER BY ABS(SUM((debit - credit) * rate)) DESC
    LIMIT $LIMIT";

$q = mysqli_query($conn2, $sql);
if ($q === false) { echo "GAGAL: " . mysqli_error($conn2) . $br; exit(1); }

$now  = date('Y-m-d H:i:s');
$vals = [];
while ($r = mysqli_fetch_assoc($q)) {
    $rate = ((float) $r['rate']) > 0 ? (float) $r['rate'] : 1;
    $tfp  = (!empty($r['tgl_faktur_pajak']) && $r['tgl_faktur_pajak'] != '0000-00-00')
          ? "'" . $e($r['tgl_faktur_pajak']) . "'" : 'NULL';
    $sd   = (!empty($r['si_date']) && $r['si_date'] != '0000-00-00')
          ? "'" . $e($r['si_date']) . "'" : 'NULL';
    $vals[] = "('" . $e($r['si_no']) . "', $sd, '" . $e($r['supplier']) . "', '" . $e($r['faktur_pajak']) . "',
        $tfp, '" . $e($r['profit_center']) . "', '" . $e($r['curr'] ?: 'IDR') . "', $rate,
        " . (float) $r['ocy'] . ", " . (float) $r['idr'] . ", '" . $e($AS_OF) . "', 'Post', 'sample', '" . $e($now) . "')";
}
if (!$vals) { echo "tidak ada data lama yang bisa dijadikan contoh." . $br; exit; }

$ok = mysqli_query($conn2, "INSERT INTO tbl_ppn_saldo_awal
    (si_no, si_date, supplier, faktur_pajak, tgl_faktur_pajak, profit_center, curr, rate,
     amount_ocy, amount_idr, as_of, status, create_by, create_date) VALUES " . implode(',', $vals));
if ($ok === false) { echo "GAGAL insert: " . mysqli_error($conn2) . $br; exit(1); }

$sum = mysqli_fetch_assoc(mysqli_query($conn2,
    "SELECT COUNT(*) n, COALESCE(SUM(amount_idr), 0) idr FROM tbl_ppn_saldo_awal
     WHERE status = 'Post' AND as_of = '" . $e($AS_OF) . "'"));
echo "contoh dimasukkan : " . count($vals) . " baris" . $br;
echo "total saldo awal  : " . $sum['n'] . " baris / IDR " . number_format((float) $sum['idr'], 2) . $br;
