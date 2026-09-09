<?php
// ---------------------------------------------------------------------------
// backfill_journal_faktur.php — isi faktur_pajak/tgl_faktur_pajak utk baris
// jurnal AP-Kontrabon (GR/IR + PPN Masukan) LAMA yang masih kosong.
//
// Sumber: bpb_new.upt_no_faktur (jalur utama, sama spt bpb_docinfo_guard.php
// yang dipakai kode berjalan sekarang) -> fallback upt_no_faktur2 (jalur
// input faktur terpisah/lama) -> bppb_new (BPB retur/RO).
// Dicocokkan lewat tbl_list_journal.reff_doc = no_bpb (AP-Kontrabon).
//
// Set-based via tabel pemetaan sementara (spt backfill_journal_supplier.php):
// menjodohkan tbl_list_journal langsung ke bpb_new tanpa tabel perantara
// lambat karena keduanya besar. Aman diulang: hanya mengisi baris yg masih
// kosong, dan hanya no_bpb yg TIDAK ambigu (1 nilai faktur unik).
// ---------------------------------------------------------------------------
include '../../conn/conn.php';
set_time_limit(0);
ignore_user_abort(true);
header('Content-Type: text/plain; charset=utf-8');

$c   = $conn2;
$LOG = __DIR__ . '/__backfill_faktur.log';
@unlink($LOG);
function lg($m) { global $LOG; file_put_contents($LOG, date('H:i:s') . "  $m\n", FILE_APPEND); echo "$m\n"; }
function step($c, $label, $sql) {
    $t0 = microtime(true);
    $ok = mysqli_query($c, $sql);
    lg(sprintf("  %-46s %s", $label,
        $ok ? mysqli_affected_rows($c) . " baris (" . round(microtime(true) - $t0, 1) . "s)"
            : "GAGAL: " . mysqli_error($c)));
    return $ok;
}

lg("MULAI backfill faktur_pajak (AP - Kontrabon: GR/IR + PPN Masukan)");

// --- 1) Tabel pemetaan no_bpb -> faktur (hanya no_bpb yg TIDAK ambigu) ------
mysqli_query($c, "DROP TABLE IF EXISTS tmp_jrn_faktur_map");
mysqli_query($c, "CREATE TABLE tmp_jrn_faktur_map (
    no_bpb      VARCHAR(255) NOT NULL PRIMARY KEY,
    no_faktur   VARCHAR(50)  NOT NULL,
    tgl_faktur  DATE         NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

lg("[1] Bangun tabel pemetaan (no_bpb -> faktur, hanya yg tidak ambigu)");
step($c, 'bpb_new (utama+fallback faktur2)', "INSERT INTO tmp_jrn_faktur_map (no_bpb, no_faktur, tgl_faktur)
    SELECT no_bpb,
           MIN(COALESCE(NULLIF(upt_no_faktur,''), NULLIF(upt_no_faktur2,''))),
           MIN(NULLIF(COALESCE(NULLIF(upt_tgl_faktur,'0000-00-00'), NULLIF(upt_tgl_faktur2,'0000-00-00')), '0000-00-00'))
    FROM bpb_new
    WHERE COALESCE(NULLIF(upt_no_faktur,''), NULLIF(upt_no_faktur2,'')) IS NOT NULL
    GROUP BY no_bpb
    HAVING COUNT(DISTINCT COALESCE(NULLIF(upt_no_faktur,''), NULLIF(upt_no_faktur2,''))) = 1");

// bppb_new (retur) beda charset (utf8 vs utf8mb4 di reff_doc) -> CONVERT spy
// indeks no_ro/no_bppb tetap terpakai (lihat catatan collation di memory).
step($c, 'bppb_new (retur, no_ro)', "INSERT IGNORE INTO tmp_jrn_faktur_map (no_bpb, no_faktur, tgl_faktur)
    SELECT CONVERT(no_ro USING utf8mb4), MIN(upt_no_faktur), MIN(NULLIF(upt_tgl_faktur,'0000-00-00'))
    FROM bppb_new
    WHERE upt_no_faktur IS NOT NULL AND upt_no_faktur <> '' AND no_ro IS NOT NULL AND no_ro <> ''
    GROUP BY CONVERT(no_ro USING utf8mb4)
    HAVING COUNT(DISTINCT upt_no_faktur) = 1");
step($c, 'bppb_new (retur, no_bppb)', "INSERT IGNORE INTO tmp_jrn_faktur_map (no_bpb, no_faktur, tgl_faktur)
    SELECT CONVERT(no_bppb USING utf8mb4), MIN(upt_no_faktur), MIN(NULLIF(upt_tgl_faktur,'0000-00-00'))
    FROM bppb_new
    WHERE upt_no_faktur IS NOT NULL AND upt_no_faktur <> '' AND no_bppb IS NOT NULL AND no_bppb <> ''
    GROUP BY CONVERT(no_bppb USING utf8mb4)
    HAVING COUNT(DISTINCT upt_no_faktur) = 1");

$n = mysqli_fetch_row(mysqli_query($c, "SELECT COUNT(*) FROM tmp_jrn_faktur_map"))[0];
lg("  -> pemetaan siap: " . number_format((float)$n) . " nomor BPB/RO");

// --- 2) Terapkan ke tbl_list_journal (+ _cancel) ----------------------------
lg("[2] Terapkan ke jurnal (type_journal = 'AP - Kontrabon', faktur masih kosong)");
foreach (['tbl_list_journal', 'tbl_list_journal_cancel'] as $tbl) {
    $r = @mysqli_query($c, "SHOW TABLES LIKE '$tbl'");
    if (!$r || mysqli_num_rows($r) == 0) { lg("  SKIP $tbl (tabel belum ada)"); continue; }

    step($c, $tbl, "UPDATE $tbl a
        JOIN tmp_jrn_faktur_map x ON x.no_bpb = a.reff_doc
         SET a.faktur_pajak = x.no_faktur, a.tgl_faktur_pajak = x.tgl_faktur
       WHERE a.type_journal = 'AP - Kontrabon'
         AND (a.faktur_pajak IS NULL OR a.faktur_pajak = '')");

    $q = mysqli_query($c, "SELECT COUNT(*) t, SUM(faktur_pajak IS NOT NULL AND faktur_pajak <> '') s
        FROM $tbl WHERE type_journal = 'AP - Kontrabon'");
    $x = mysqli_fetch_assoc($q);
    lg(sprintf("  -> %s: terisi %s dari %s baris AP-Kontrabon (%.1f%%)", $tbl,
        number_format((float)$x['s']), number_format((float)$x['t']),
        $x['t'] > 0 ? $x['s'] / $x['t'] * 100 : 0));
}

// --- 3) BILLED (PPN Masukan) khusus: PV LAMA sebelum fitur "pecah per faktur"
// (commit f5fc3e8) menyimpan reff_doc = NOMOR FAKTUR PAJAK itu sendiri secara
// langsung (satu baris konsolidasi per PV, bukan reff_doc = no_bpb). Filter
// nama_coa disamakan persis dgn pv_ppn_group_faktur.php (bukan hardcode kode
// COA) supaya konsisten dgn logika live. Hanya disalin bila reff_doc SUDAH
// tervalidasi sebagai pola nomor faktur pajak Indonesia yg valid -- baris yg
// polanya tidak dikenali (typo, RO/BPB number, gabungan multi-faktur dgn
// koma) TIDAK disentuh, dibiarkan kosong utk ditinjau manual.
lg("[3] BILLED lama: reff_doc = faktur pajak langsung (bukan no_bpb)");
foreach (['tbl_list_journal', 'tbl_list_journal_cancel'] as $tbl) {
    $r = @mysqli_query($c, "SHOW TABLES LIKE '$tbl'");
    if (!$r || mysqli_num_rows($r) == 0) continue;

    step($c, $tbl, "UPDATE $tbl a
         SET a.faktur_pajak = a.reff_doc
       WHERE a.type_journal = 'AP - Kontrabon'
         AND a.nama_coa LIKE '%PPN MASUKAN%' AND a.nama_coa NOT LIKE '%UNBILLED%'
         AND (a.faktur_pajak IS NULL OR a.faktur_pajak = '')
         AND a.reff_doc NOT IN ('', '-')
         AND a.reff_doc NOT LIKE '%,%' AND a.reff_doc NOT LIKE '% %' AND a.reff_doc NOT LIKE '%/%'
         AND (a.reff_doc REGEXP '^[0-9]{2,3}\.?[0-9]{0,3}-[0-9]{2}\.[0-9]{6,9}$'
              OR a.reff_doc REGEXP '^[0-9]{15,17}$')");

    $q = mysqli_query($c, "SELECT COUNT(*) t, SUM(faktur_pajak IS NOT NULL AND faktur_pajak <> '') s
        FROM $tbl WHERE type_journal = 'AP - Kontrabon'");
    $x = mysqli_fetch_assoc($q);
    lg(sprintf("  -> %s: terisi %s dari %s baris AP-Kontrabon (%.1f%%) [sesudah langkah 3]", $tbl,
        number_format((float)$x['s']), number_format((float)$x['t']),
        $x['t'] > 0 ? $x['s'] / $x['t'] * 100 : 0));
}

mysqli_query($c, "DROP TABLE IF EXISTS tmp_jrn_faktur_map");
lg("SELESAI");
