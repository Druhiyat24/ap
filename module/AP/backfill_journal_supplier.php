<?php
// ---------------------------------------------------------------------------
// backfill_journal_supplier.php — isi kolom `supplier` utk data jurnal LAMA.
//
// Cara kerja: bangun dulu SATU tabel pemetaan (no_journal -> supplier) dgn
// agregasi per tabel sumber, baru UPDATE jurnal lewat join ber-index.
// Menjodohkan tbl_list_journal (600rb+ baris) langsung ke bpb/bppb (600rb+
// baris) terlalu lambat, makanya dipisah begini.
//
// Aman diulang: hanya mengisi baris yg supplier-nya masih kosong.
// Progres ditulis ke __backfill_supplier.log supaya bisa dipantau walau
// koneksi HTTP-nya putus.
// ---------------------------------------------------------------------------
include '../../conn/conn.php';
set_time_limit(0);
ignore_user_abort(true);
header('Content-Type: text/plain; charset=utf-8');

$c   = $conn2;
$LOG = __DIR__ . '/__backfill_supplier.log';
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

lg("MULAI backfill supplier");

// --- 1) Tabel pemetaan no_journal -> supplier ------------------------------
mysqli_query($c, "DROP TABLE IF EXISTS tmp_jrn_supplier_map");
mysqli_query($c, "CREATE TABLE tmp_jrn_supplier_map (
    no_journal VARCHAR(150) NOT NULL PRIMARY KEY,
    supplier   VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

// Urutan = prioritas. INSERT IGNORE -> sumber pertama yg cocok yg dipakai.
lg("[1] Bangun tabel pemetaan");
step($c, 'kontrabon_h (PV)', "INSERT IGNORE INTO tmp_jrn_supplier_map
    SELECT CONVERT(no_kbon USING utf8mb4), nama_supp FROM kontrabon_h
     WHERE no_kbon <> '' AND nama_supp IS NOT NULL AND nama_supp NOT IN ('','-')");

step($c, 'b_bankout_h (Bank Out / PV / Payment)', "INSERT IGNORE INTO tmp_jrn_supplier_map
    SELECT no_bankout, nama_supp FROM b_bankout_h
     WHERE no_bankout <> '' AND nama_supp IS NOT NULL AND nama_supp NOT IN ('','-')");

step($c, 'memo_h (MEMO-EXIM)', "INSERT IGNORE INTO tmp_jrn_supplier_map
    SELECT CONVERT(h.nm_memo USING utf8mb4), m.Supplier FROM memo_h h
      JOIN mastersupplier m ON m.Id_Supplier = h.id_supplier
     WHERE h.nm_memo <> '' AND m.Supplier NOT IN ('','-')");

step($c, 'bpb.bpbno_int (AP - BPB)', "INSERT IGNORE INTO tmp_jrn_supplier_map
    SELECT CONVERT(b.bpbno_int USING utf8mb4), MIN(m.Supplier) FROM bpb b
      JOIN mastersupplier m ON m.Id_Supplier = b.id_supplier
     WHERE b.bpbno_int <> '' AND m.Supplier NOT IN ('','-')
     GROUP BY CONVERT(b.bpbno_int USING utf8mb4)");

step($c, 'tbl_bankin_arcollection (Bank In)', "INSERT IGNORE INTO tmp_jrn_supplier_map
    SELECT doc_num, customer FROM tbl_bankin_arcollection
     WHERE doc_num <> '' AND customer IS NOT NULL AND customer NOT IN ('','-')");

step($c, 'c_petty_cashout_h (Petty Cash Out)', "INSERT IGNORE INTO tmp_jrn_supplier_map
    SELECT CONVERT(no_pco USING utf8mb4), nama_supp FROM c_petty_cashout_h
     WHERE no_pco <> '' AND nama_supp IS NOT NULL AND nama_supp NOT IN ('','-')");

step($c, 'bppb.bppbno_int (AP - BPB RETURN)', "INSERT IGNORE INTO tmp_jrn_supplier_map
    SELECT p.bppbno_int, MIN(m.Supplier) FROM bppb p
      JOIN mastersupplier m ON m.Id_Supplier = p.id_supplier
     WHERE p.bppbno_int <> '' AND m.Supplier NOT IN ('','-')
     GROUP BY p.bppbno_int");

step($c, 'bpb_new (sisa BPB)', "INSERT IGNORE INTO tmp_jrn_supplier_map
    SELECT no_bpb, MIN(supplier) FROM bpb_new
     WHERE no_bpb <> '' AND supplier IS NOT NULL AND supplier NOT IN ('','-')
     GROUP BY no_bpb");

$n = mysqli_fetch_row(mysqli_query($c, "SELECT COUNT(*) FROM tmp_jrn_supplier_map"))[0];
lg("  -> pemetaan siap: " . number_format((float)$n) . " nomor dokumen");

// --- 2) Terapkan ke tabel jurnal -------------------------------------------
lg("[2] Terapkan ke tabel jurnal");
foreach (['tbl_list_journal', 'tbl_list_journal_cancel'] as $tbl) {
    $r = @mysqli_query($c, "SHOW COLUMNS FROM $tbl LIKE 'supplier'");
    if (!$r || mysqli_num_rows($r) == 0) { lg("  SKIP $tbl (kolom supplier belum ada)"); continue; }

    step($c, $tbl, "UPDATE $tbl a
        JOIN tmp_jrn_supplier_map x ON x.no_journal = a.no_journal
         SET a.supplier = x.supplier
       WHERE a.supplier IS NULL OR a.supplier = ''");

    $q = mysqli_query($c, "SELECT COUNT(*) t, SUM(supplier IS NOT NULL AND supplier <> '') s FROM $tbl");
    $x = mysqli_fetch_assoc($q);
    lg(sprintf("  -> %s: terisi %s dari %s baris (%.1f%%)", $tbl,
        number_format((float)$x['s']), number_format((float)$x['t']),
        $x['t'] > 0 ? $x['s'] / $x['t'] * 100 : 0));
}

mysqli_query($c, "DROP TABLE IF EXISTS tmp_jrn_supplier_map");
lg("SELESAI");
