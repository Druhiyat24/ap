<?php
header('Content-Type: application/json');
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

/* ============================================================================
   COPY SALDO FABRIC BARCODE

   Menyalin saldo akhir satu periode menjadi saldo awal periode berikutnya.

   DUA PERBAIKAN (4 Nov 2026), keduanya diuji banding ke naskah lama:

   1. PEMETAAN BARCODE - penyaring status baris TUJUAN yang hilang.
      Dulu penyaringnya cuma `a.status = 'Y'`, yaitu status whs_mut_lokasi,
      sementara baris tujuannya di whs_lokasi_inmaterial tidak diperiksa.
      Akibatnya mutasi PINDAH RAK yang sah bisa menghidupkan kembali barcode
      hasil mutasi yang sudah DIBATALKAN. Kasus nyata: F126124 (lot
      05666/10/24, BPB GK/RI/0125/00090) berubah jadi F206086 pada periode
      2026-10-01, gara-gara MT/0826/00002 (pindah rak, 3 Agu 2026, status Y)
      bertemu baris F206086 dari MT/0825/00028 yang berstatus N.
      Dua berkas lain yang menelusuri rantai barcode (get_data_bppb_fabric.php,
      update_data_outfabric.php) memang sudah memakai whs_lokasi_inmaterial
      .status = 'Y' - penyaring itulah yang kurang di sini.

   2. YANG DISIMPAN = BARCODE ASLI, bukan hasil pemetaan.
      Kolom keluarannya dulu `barcode_mapping no_barcode`, sekarang
      `no_barcode`. Pemetaannya TETAP dipakai di dalam perhitungan - untuk
      mengelompokkan dan mencocokkan mutasi keluar - hanya nilai yang
      DISIMPAN yang kembali ke barcode aslinya, supaya tidak bergeser tiap
      bulan.

   KECEPATAN: 368 detik -> 7,7 detik pada data Jul 2026 (19.776 baris).
      Naskah lama memakai SATU perintah raksasa. MariaDB tidak berbagi
      materialisasi CTE, jadi UNION di CTE `pemasukan` membangun ULANG
      seluruh saldo_awal dan trx_in untuk tiap cabang, dan semua tabel
      turunan itu disambung tanpa kunci (Using join buffer, BNL join) -
      rencana eksekusinya menaksir puluhan miliar baris.
      Sekarang tiap bagian berat dimaterialkan ke tabel sementara BERINDEKS
      lebih dulu, memakai naskah SQL yang sama persis, sehingga barisnya
      identik menurut konstruksi.

      PENTING - tabel sementara dibuat dengan CREATE ... AS SELECT supaya
      collation kolomnya DIWARISI dari kolom sumbernya. Jangan sekali-kali
      mendeklarasikan kolomnya dengan charset tetap: kalau collation-nya
      beda, MariaDB tidak boleh memakai indeksnya dan sambungannya jatuh
      lagi ke BNL. Percobaan pertama memakai latin7/latin1 yang dihardcode
      dan hasilnya 94 detik; dengan collation yang cocok jadi 1,6 detik.

   TIDAK PERLU UBAH BASIS DATA. Indeks pada whs_saldo_awal_nilai_persediaan
      (tgl_periode) sempat diuji: hanya menghemat 0,9 detik dari 7,7 - tidak
      sebanding dengan DDL pada tabel 220 ribu baris, jadi tidak dipakai.
   ============================================================================ */

$start_date = $_POST['start_date'] ?? '';
$end_date   = $_POST['end_date']   ?? '';

if (empty($start_date) || empty($end_date)) {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tanggal tidak lengkap.']);
    exit;
}

// Validasi: start harus awal bulan, end harus akhir bulan yang sama
$sd = DateTime::createFromFormat('Y-m-d', $start_date);
$ed = DateTime::createFromFormat('Y-m-d', $end_date);

if (!$sd || !$ed) {
    echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid.']);
    exit;
}

$isFirstDay = $sd->format('d') === '01';
$isLastDay  = $ed->format('d') === $ed->format('t');
$sameMonth  = $sd->format('Y-m') === $ed->format('Y-m');

if (!$isFirstDay || !$isLastDay || !$sameMonth) {
    echo json_encode(['status' => 'error', 'message' => 'Filter harus dari awal bulan ke akhir bulan.']);
    exit;
}

$sd_esc  = mysqli_real_escape_string($conn1, $start_date);
$ed_esc  = mysqli_real_escape_string($conn1, $end_date);
// Periode berikutnya = awal bulan setelah start_date
$next_period = date('Y-m-d', strtotime('+1 month', strtotime($start_date)));
$np_esc  = mysqli_real_escape_string($conn1, $next_period);

/* ---- Tahap 1..6: bangun tabel sementara berindeks -------------------------
   Belum menyentuh data sungguhan sama sekali. Kalau ada yang gagal di sini,
   tabel whs_saldo_awal_nilai_persediaan masih utuh. */
$tahap = array();

$tahap[] = array('tmp_map', <<<SQL_TMP_MAP_1
DROP TEMPORARY TABLE IF EXISTS tmp_map
SQL_TMP_MAP_1
);

$tahap[] = array('tmp_map', <<<SQL_TMP_MAP_2
CREATE TEMPORARY TABLE tmp_map AS
            select idbpb_det, no_barcode from whs_mut_lokasi a
            INNER JOIN whs_lokasi_inmaterial b on b.no_barcode_old = a.idbpb_det
            where a.status = 'Y' and b.status = 'Y' GROUP BY no_barcode
SQL_TMP_MAP_2
);

$tahap[] = array('tmp_map', <<<SQL_TMP_MAP_3
ALTER TABLE tmp_map ADD KEY idbpb_det (idbpb_det)
SQL_TMP_MAP_3
);

$tahap[] = array('tmp_saldo_awal', <<<SQL_TMP_SALDO_AWAL_4
DROP TEMPORARY TABLE IF EXISTS tmp_saldo_awal
SQL_TMP_SALDO_AWAL_4
);

$tahap[] = array('tmp_saldo_awal', <<<SQL_TMP_SALDO_AWAL_5
CREATE TEMPORARY TABLE tmp_saldo_awal AS select a.no_barcode, IFNULL(CASE
    WHEN map.no_barcode = 'F244111' THEN 'F246063'
    WHEN map.no_barcode = 'F246105' THEN 'F246785'
    WHEN map.no_barcode = 'F244115' THEN 'F246065'
    WHEN map.no_barcode = 'F244107' THEN 'F246061'
    WHEN map.no_barcode = 'F244108' THEN 'F246062'
    WHEN map.no_barcode = 'F244112' THEN 'F246064'
    WHEN map.no_barcode = 'F246099' THEN 'F246779'
    WHEN map.no_barcode = 'F246100' THEN 'F246780'
    WHEN map.no_barcode = 'F246101' THEN 'F246781'
    WHEN map.no_barcode = 'F246102' THEN 'F246782'
    WHEN map.no_barcode = 'F246103' THEN 'F246783'
    WHEN map.no_barcode = 'F246104' THEN 'F246784'
    WHEN map.no_barcode = 'F246106' THEN 'F246786'
    WHEN map.no_barcode = 'F245995' THEN 'F249329'
    WHEN map.no_barcode = 'F245996' THEN 'F249330'
    WHEN map.no_barcode = 'F245997' THEN 'F249331'
    ELSE map.no_barcode END ,a.no_barcode) barcode_mapping, a.no_dok, a.tgl_dok, a.supplier, a.kode_lok, a.style, a.no_lot, a.no_roll, id_jo, a.id_item, b.goods_code, b.itemdesc, satuan, ws, price, rate, ROUND(sum(qty),4) saldo_awal_qty, ROUND(IF(qty > 0,(price * rate)/count(a.no_barcode),0),4) saldo_awal_price, (qty * (price * rate)) saldo_awal_total from whs_saldo_awal_nilai_persediaan a INNER JOIN masteritem b on b.id_item = a.id_item LEFT JOIN tmp_map map on map.idbpb_det = a.no_barcode where tgl_periode = (SELECT MAX(tgl_periode) FROM whs_saldo_awal_nilai_persediaan WHERE tgl_periode <= '$sd_esc') GROUP BY a.no_barcode
SQL_TMP_SALDO_AWAL_5
);

$tahap[] = array('tmp_saldo_awal', <<<SQL_TMP_SALDO_AWAL_6
ALTER TABLE tmp_saldo_awal ADD KEY no_barcode (no_barcode)
SQL_TMP_SALDO_AWAL_6
);

$tahap[] = array('tmp_trx_in_fix', <<<SQL_TMP_TRX_IN_FIX_7
DROP TEMPORARY TABLE IF EXISTS tmp_trx_in_fix
SQL_TMP_TRX_IN_FIX_7
);

$tahap[] = array('tmp_trx_in_fix', <<<SQL_TMP_TRX_IN_FIX_8
CREATE TEMPORARY TABLE tmp_trx_in_fix AS
        WITH trx_in as (select b.no_barcode, IFNULL(map.no_barcode,b.no_barcode) barcode_mapping, a.no_dok, a.tgl_dok, a.supplier, b.kode_lok, tmpjo.styleno style, b.no_lot, b.no_roll, b.id_jo, b.id_item, mi.goods_code, mi.itemdesc, b.satuan, kpno no_ws, type_pch, qty_sj, COALESCE(IFNULL(np_curr_rev,np_curr),'-') curr, ROUND(COALESCE(IFNULL(np_price_rev,np_price),0),4) price, (qty_sj * (COALESCE(IFNULL(np_price_rev,np_price),0))) total_price, np_tgl_in, IFNULL(rate,1) rate from whs_inmaterial_fabric a INNER JOIN whs_lokasi_inmaterial b on b.no_dok = a.no_dok INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo, kpno, styleno from act_costing ac inner join so on ac.id = so.id_cost inner join jo_det jod on so.id = jod.id_so group by id_jo) tmpjo on tmpjo.id_jo = b.id_jo LEFT JOIN (select tanggal, curr curr_rate, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = b.np_tgl_in and cr.curr_rate = COALESCE(IFNULL(b.np_curr_rev,b.np_curr),'-') LEFT JOIN tmp_map map on map.idbpb_det = b.no_barcode where a.tgl_dok BETWEEN '$sd_esc' and '$ed_esc' and b.status = 'Y'),
             trx_in_detail as (SELECT
    no_barcode, barcode_mapping, no_dok, tgl_dok, supplier, kode_lok, style, no_lot, no_roll, id_jo, id_item, goods_code, itemdesc, satuan, no_ws,
    SUM(CASE WHEN type_pch='Pembelian Lokal' THEN qty_sj ELSE 0 END) AS in_lokal_qty,
    CASE WHEN SUM(CASE WHEN type_pch='Pembelian Lokal' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch='Pembelian Lokal' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch='Pembelian Lokal' THEN 1 END),4) ELSE 0 END AS in_lokal_price,
    SUM(CASE WHEN type_pch='Pembelian Lokal' THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_lokal_total,
    SUM(CASE WHEN type_pch='Pembelian Impor' THEN qty_sj ELSE 0 END) AS in_impor_qty,
    CASE WHEN SUM(CASE WHEN type_pch='Pembelian Impor' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch='Pembelian Impor' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch='Pembelian Impor' THEN 1 END),4) ELSE 0 END AS in_impor_price,
    SUM(CASE WHEN type_pch='Pembelian Impor' THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_impor_total,
    SUM(CASE WHEN type_pch IN ('Pengembalian dari Subkontraktor CMT', 'Pengembalian dari Subkontraktor Jasa') THEN qty_sj ELSE 0 END) AS in_subcont_qty,
    CASE WHEN SUM(CASE WHEN type_pch IN ('Pengembalian dari Subkontraktor CMT', 'Pengembalian dari Subkontraktor Jasa') THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch IN ('Pengembalian dari Subkontraktor CMT', 'Pengembalian dari Subkontraktor Jasa') THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch IN ('Pengembalian dari Subkontraktor CMT', 'Pengembalian dari Subkontraktor Jasa') THEN 1 END),4) ELSE 0 END AS in_subcont_price,
    SUM(CASE WHEN type_pch IN ('Pengembalian dari Subkontraktor CMT', 'Pengembalian dari Subkontraktor Jasa') THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_subcont_total,
    SUM(CASE WHEN type_pch='Pengembalian dari Produksi' THEN qty_sj ELSE 0 END) AS in_produksi_qty,
    CASE WHEN SUM(CASE WHEN type_pch='Pengembalian dari Produksi' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch='Pengembalian dari Produksi' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch='Pengembalian dari Produksi' THEN 1 END),4) ELSE 0 END AS in_produksi_price,
    SUM(CASE WHEN type_pch='Pengembalian dari Produksi' THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_produksi_total,
    SUM(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN qty_sj ELSE 0 END) AS in_sample_qty,
    CASE WHEN SUM(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN 1 END),4) ELSE 0 END AS in_sample_price,
    SUM(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_sample_total
    FROM trx_in GROUP BY no_barcode),
             trx_in_fix as (select *, (in_lokal_qty + in_impor_qty + in_subcont_qty + in_produksi_qty + in_sample_qty) jumlah_in_qty, ROUND((in_lokal_total + in_impor_total + in_subcont_total + in_produksi_total + in_sample_total) / (in_lokal_qty + in_impor_qty + in_subcont_qty + in_produksi_qty + in_sample_qty),4) jumlah_in_price, (in_lokal_total + in_impor_total + in_subcont_total + in_produksi_total + in_sample_total) jumlah_in_total from trx_in_detail)
        SELECT * FROM trx_in_fix
SQL_TMP_TRX_IN_FIX_8
);

$tahap[] = array('tmp_trx_in_fix', <<<SQL_TMP_TRX_IN_FIX_9
ALTER TABLE tmp_trx_in_fix ADD KEY no_barcode (no_barcode)
SQL_TMP_TRX_IN_FIX_9
);

$tahap[] = array('tmp_trx_out_fix', <<<SQL_TMP_TRX_OUT_FIX_10
DROP TEMPORARY TABLE IF EXISTS tmp_trx_out_fix
SQL_TMP_TRX_OUT_FIX_10
);

$tahap[] = array('tmp_trx_out_fix', <<<SQL_TMP_TRX_OUT_FIX_11
CREATE TEMPORARY TABLE tmp_trx_out_fix AS
        WITH trx_out as (select CASE
    WHEN id_roll = 'F229331' THEN 'F246048'
    WHEN id_roll = 'F238451' THEN 'F246050'
    ELSE id_roll END
    id_roll, id_jo, id_item, CASE
    WHEN a.jenis_pengeluaran IS NULL THEN '-'
    WHEN a.jenis_pengeluaran = 'penjualan' AND sg.supplier IS NULL THEN 'Sales Nongroup'
    WHEN a.jenis_pengeluaran = 'penjualan' AND sg.supplier IS NOT NULL THEN 'Sales Group'
    ELSE a.jenis_pengeluaran
    END type_pch, (COALESCE(qty_out,0)) qty_sj, COALESCE(IFNULL(np_curr_rev,np_curr),'-') curr, ROUND(COALESCE(IFNULL(np_price_rev,np_price),0),4) price, (qty_out * (COALESCE(IFNULL(np_price_rev,np_price),0))) total_price, np_tgl_in, IFNULL(rate,1) rate from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb LEFT JOIN (select tanggal, curr curr_rate, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = b.np_tgl_in and cr.curr_rate = COALESCE(IFNULL(b.np_curr_rev,b.np_curr),'-') left join (select id_supplier, supplier from ca_sales_group) sg on sg.supplier = a.tujuan where tgl_bppb BETWEEN '$sd_esc' and '$ed_esc' and a.status != 'Cancel' and b.status = 'Y'),
             trx_out_detail as (SELECT
    id_roll, id_jo, id_item,
    SUM(CASE WHEN type_pch='Pemakaian Produksi' THEN qty_sj ELSE 0 END) AS out_prod_qty,
    CASE WHEN SUM(CASE WHEN type_pch='Pemakaian Produksi' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch='Pemakaian Produksi' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch='Pemakaian Produksi' THEN 1 END),4) ELSE 0 END AS out_prod_price,
    SUM(CASE WHEN type_pch='Pemakaian Produksi' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_prod_total,
    SUM(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN qty_sj ELSE 0 END) AS out_subcont_qty,
    CASE WHEN SUM(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN 1 END),4) ELSE 0 END AS out_subcont_price,
    SUM(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_subcont_total,
    SUM(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN qty_sj ELSE 0 END) AS out_lokal_qty,
    CASE WHEN SUM(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN 1 END),4) ELSE 0 END AS out_lokal_price,
    SUM(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_lokal_total,
    SUM(CASE WHEN type_pch='Retur Pembelian Impor' THEN qty_sj ELSE 0 END) AS out_impor_qty,
    CASE WHEN SUM(CASE WHEN type_pch='Retur Pembelian Impor' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch='Retur Pembelian Impor' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch='Retur Pembelian Impor' THEN 1 END),4) ELSE 0 END AS out_impor_price,
    SUM(CASE WHEN type_pch='Retur Pembelian Impor' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_impor_total,
    SUM(CASE WHEN type_pch='Pemakaian Sample Room' THEN qty_sj ELSE 0 END) AS out_sample_qty,
    CASE WHEN SUM(CASE WHEN type_pch='Pemakaian Sample Room' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch='Pemakaian Sample Room' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch='Pemakaian Sample Room' THEN 1 END),4) ELSE 0 END AS out_sample_price,
    SUM(CASE WHEN type_pch='Pemakaian Sample Room' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_sample_total,
    SUM(CASE WHEN type_pch='Sales Nongroup' THEN qty_sj ELSE 0 END) AS out_salnongroup_qty,
    CASE WHEN SUM(CASE WHEN type_pch='Sales Nongroup' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch='Sales Nongroup' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch='Sales Nongroup' THEN 1 END),4) ELSE 0 END AS out_salnongroup_price,
    SUM(CASE WHEN type_pch='Sales Nongroup' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_salnongroup_total,
    SUM(CASE WHEN type_pch='Sales Group' THEN qty_sj ELSE 0 END) AS out_salgroup_qty,
    CASE WHEN SUM(CASE WHEN type_pch='Sales Group' THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch='Sales Group' THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch='Sales Group' THEN 1 END),4) ELSE 0 END AS out_salgroup_price,
    SUM(CASE WHEN type_pch='Sales Group' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_salgroup_total,
    SUM(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN qty_sj ELSE 0 END) AS out_other_qty,
    CASE WHEN SUM(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN qty_sj ELSE 0 END) > 0 THEN ROUND(SUM(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN (price * rate) ELSE 0 END) / COUNT(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN 1 END),4) ELSE 0 END AS out_other_price,
    SUM(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_other_total
    FROM trx_out GROUP BY id_roll),
             trx_out_fix as (select *, (out_prod_qty + out_subcont_qty + out_lokal_qty + out_impor_qty + out_sample_qty + out_salnongroup_qty + out_salgroup_qty + out_other_qty) jumlah_out_qty, ROUND((out_prod_total + out_subcont_total + out_lokal_total + out_impor_total + out_sample_total + out_salnongroup_total + out_salgroup_total + out_other_total) / (out_prod_qty + out_subcont_qty + out_lokal_qty + out_impor_qty + out_sample_qty + out_salnongroup_qty + out_salgroup_qty + out_other_qty),4) jumlah_out_price, (out_prod_total + out_subcont_total + out_lokal_total + out_impor_total + out_sample_total + out_salnongroup_total + out_salgroup_total + out_other_total) jumlah_out_total from trx_out_detail)
        SELECT * FROM trx_out_fix
SQL_TMP_TRX_OUT_FIX_11
);

$tahap[] = array('tmp_trx_out_fix', <<<SQL_TMP_TRX_OUT_FIX_12
ALTER TABLE tmp_trx_out_fix ADD KEY id_roll (id_roll)
SQL_TMP_TRX_OUT_FIX_12
);

$tahap[] = array('tmp_pemasukan_fix', <<<SQL_TMP_PEMASUKAN_FIX_13
DROP TEMPORARY TABLE IF EXISTS tmp_pemasukan_fix
SQL_TMP_PEMASUKAN_FIX_13
);

$tahap[] = array('tmp_pemasukan_fix', <<<SQL_TMP_PEMASUKAN_FIX_14
CREATE TEMPORARY TABLE tmp_pemasukan_fix AS
        WITH pemasukan as (select a.no_barcode, a.barcode_mapping, a.no_dok, a.tgl_dok, a.supplier, a.kode_lok, a.style, a.no_lot, a.no_roll, a.id_jo, a.id_item, a.goods_code, a.itemdesc, a.satuan, a.ws no_ws, COALESCE(saldo_awal_qty,0) saldo_awal_qty, COALESCE(saldo_awal_price,0) saldo_awal_price, COALESCE(saldo_awal_total,0) saldo_awal_total, COALESCE(in_lokal_qty,0) in_lokal_qty, COALESCE(in_lokal_price,0) in_lokal_price, COALESCE(in_lokal_total,0) in_lokal_total, COALESCE(in_impor_qty,0) in_impor_qty, COALESCE(in_impor_price,0) in_impor_price, COALESCE(in_impor_total,0) in_impor_total, COALESCE(in_subcont_qty,0) in_subcont_qty, COALESCE(in_subcont_price,0) in_subcont_price, COALESCE(in_subcont_total,0) in_subcont_total, COALESCE(in_produksi_qty,0) in_produksi_qty, COALESCE(in_produksi_price,0) in_produksi_price, COALESCE(in_produksi_total,0) in_produksi_total, COALESCE(in_sample_qty,0) in_sample_qty, COALESCE(in_sample_price,0) in_sample_price, COALESCE(in_sample_total,0) in_sample_total, COALESCE(jumlah_in_qty,0) jumlah_in_qty, COALESCE(jumlah_in_price,0) jumlah_in_price, COALESCE(jumlah_in_total,0) jumlah_in_total from tmp_saldo_awal a left join tmp_trx_in_fix b on b.no_barcode = a.no_barcode
    UNION ALL
    select a.no_barcode, a.barcode_mapping, a.no_dok, a.tgl_dok, a.supplier, a.kode_lok, a.style, a.no_lot, a.no_roll, a.id_jo, a.id_item, a.goods_code, a.itemdesc, a.satuan, a.no_ws, COALESCE(saldo_awal_qty,0) saldo_awal_qty, COALESCE(saldo_awal_price,0) saldo_awal_price, COALESCE(saldo_awal_total,0) saldo_awal_total, COALESCE(in_lokal_qty,0) in_lokal_qty, COALESCE(in_lokal_price,0) in_lokal_price, COALESCE(in_lokal_total,0) in_lokal_total, COALESCE(in_impor_qty,0) in_impor_qty, COALESCE(in_impor_price,0) in_impor_price, COALESCE(in_impor_total,0) in_impor_total, COALESCE(in_subcont_qty,0) in_subcont_qty, COALESCE(in_subcont_price,0) in_subcont_price, COALESCE(in_subcont_total,0) in_subcont_total, COALESCE(in_produksi_qty,0) in_produksi_qty, COALESCE(in_produksi_price,0) in_produksi_price, COALESCE(in_produksi_total,0) in_produksi_total, COALESCE(in_sample_qty,0) in_sample_qty, COALESCE(in_sample_price,0) in_sample_price, COALESCE(in_sample_total,0) in_sample_total, COALESCE(jumlah_in_qty,0) jumlah_in_qty, COALESCE(jumlah_in_price,0) jumlah_in_price, COALESCE(jumlah_in_total,0) jumlah_in_total from tmp_trx_in_fix a left join tmp_saldo_awal b on b.no_barcode = a.no_barcode where b.no_barcode IS NULL),
             Pemasukan_fix as (SELECT no_barcode, barcode_mapping, no_dok, tgl_dok, supplier, kode_lok, style, no_lot, no_roll, id_jo, id_item, goods_code, itemdesc, satuan, no_ws,
    COALESCE(SUM(saldo_awal_qty),0) AS saldo_awal_qty, COALESCE(SUM(saldo_awal_total),0) AS saldo_awal_total, IF(SUM(saldo_awal_qty)=0, 0, SUM(saldo_awal_total)/SUM(saldo_awal_qty)) AS saldo_awal_price,
    COALESCE(SUM(in_lokal_qty),0) AS in_lokal_qty, COALESCE(SUM(in_lokal_total),0) AS in_lokal_total, IF(SUM(in_lokal_qty)=0, 0, SUM(in_lokal_total)/SUM(in_lokal_qty)) AS in_lokal_price,
    COALESCE(SUM(in_impor_qty),0) AS in_impor_qty, COALESCE(SUM(in_impor_total),0) AS in_impor_total, IF(SUM(in_impor_qty)=0, 0, SUM(in_impor_total)/SUM(in_impor_qty)) AS in_impor_price,
    COALESCE(SUM(in_subcont_qty),0) AS in_subcont_qty, COALESCE(SUM(in_subcont_total),0) AS in_subcont_total, IF(SUM(in_subcont_qty)=0, 0, SUM(in_subcont_total)/SUM(in_subcont_qty)) AS in_subcont_price,
    COALESCE(SUM(in_produksi_qty),0) AS in_produksi_qty, COALESCE(SUM(in_produksi_total),0) AS in_produksi_total, IF(SUM(in_produksi_qty)=0, 0, SUM(in_produksi_total)/SUM(in_produksi_qty)) AS in_produksi_price,
    COALESCE(SUM(in_sample_qty),0) AS in_sample_qty, COALESCE(SUM(in_sample_total),0) AS in_sample_total, IF(SUM(in_sample_qty)=0, 0, SUM(in_sample_total)/SUM(in_sample_qty)) AS in_sample_price,
    COALESCE(SUM(jumlah_in_qty),0) AS jumlah_in_qty, COALESCE(SUM(jumlah_in_total),0) AS jumlah_in_total, IF(SUM(jumlah_in_qty)=0, 0, SUM(jumlah_in_total)/SUM(jumlah_in_qty)) AS jumlah_in_price
    FROM pemasukan GROUP BY barcode_mapping)
        SELECT * FROM Pemasukan_fix
SQL_TMP_PEMASUKAN_FIX_14
);

$tahap[] = array('tmp_pemasukan_fix', <<<SQL_TMP_PEMASUKAN_FIX_15
ALTER TABLE tmp_pemasukan_fix ADD KEY barcode_mapping (barcode_mapping)
SQL_TMP_PEMASUKAN_FIX_15
);

$tahap[] = array('tmp_pengeluaran_fix', <<<SQL_TMP_PENGELUARAN_FIX_16
DROP TEMPORARY TABLE IF EXISTS tmp_pengeluaran_fix
SQL_TMP_PENGELUARAN_FIX_16
);

$tahap[] = array('tmp_pengeluaran_fix', <<<SQL_TMP_PENGELUARAN_FIX_17
CREATE TEMPORARY TABLE tmp_pengeluaran_fix AS SELECT id_roll, id_jo, id_item,
    COALESCE(SUM(out_prod_qty),0) AS out_prod_qty, COALESCE(SUM(out_prod_total),0) AS out_prod_total, IF(SUM(out_prod_qty)=0, 0, SUM(out_prod_total)/SUM(out_prod_qty)) AS out_prod_price,
    COALESCE(SUM(out_subcont_qty),0) AS out_subcont_qty, COALESCE(SUM(out_subcont_total),0) AS out_subcont_total, IF(SUM(out_subcont_qty)=0, 0, SUM(out_subcont_total)/SUM(out_subcont_qty)) AS out_subcont_price,
    COALESCE(SUM(out_lokal_qty),0) AS out_lokal_qty, COALESCE(SUM(out_lokal_total),0) AS out_lokal_total, IF(SUM(out_lokal_qty)=0, 0, SUM(out_lokal_total)/SUM(out_lokal_qty)) AS out_lokal_price,
    COALESCE(SUM(out_impor_qty),0) AS out_impor_qty, COALESCE(SUM(out_impor_total),0) AS out_impor_total, IF(SUM(out_impor_qty)=0, 0, SUM(out_impor_total)/SUM(out_impor_qty)) AS out_impor_price,
    COALESCE(SUM(out_sample_qty),0) AS out_sample_qty, COALESCE(SUM(out_sample_total),0) AS out_sample_total, IF(SUM(out_sample_qty)=0, 0, SUM(out_sample_total)/SUM(out_sample_qty)) AS out_sample_price,
    COALESCE(SUM(out_salnongroup_qty),0) AS out_salnongroup_qty, COALESCE(SUM(out_salnongroup_total),0) AS out_salnongroup_total, IF(SUM(out_salnongroup_qty)=0, 0, SUM(out_salnongroup_total)/SUM(out_salnongroup_qty)) AS out_salnongroup_price,
    COALESCE(SUM(out_salgroup_qty),0) AS out_salgroup_qty, COALESCE(SUM(out_salgroup_total),0) AS out_salgroup_total, IF(SUM(out_salgroup_qty)=0, 0, SUM(out_salgroup_total)/SUM(out_salgroup_qty)) AS out_salgroup_price,
    COALESCE(SUM(out_other_qty),0) AS out_other_qty, COALESCE(SUM(out_other_total),0) AS out_other_total, IF(SUM(out_other_qty)=0, 0, SUM(out_other_total)/SUM(out_other_qty)) AS out_other_price,
    COALESCE(SUM(jumlah_out_qty),0) AS jumlah_out_qty, COALESCE(SUM(jumlah_out_total),0) AS jumlah_out_total, IF(SUM(jumlah_out_qty)=0, 0, SUM(jumlah_out_total)/SUM(jumlah_out_qty)) AS jumlah_out_price
    FROM tmp_trx_out_fix GROUP BY id_roll
SQL_TMP_PENGELUARAN_FIX_17
);

$tahap[] = array('tmp_pengeluaran_fix', <<<SQL_TMP_PENGELUARAN_FIX_18
ALTER TABLE tmp_pengeluaran_fix ADD KEY id_roll (id_roll)
SQL_TMP_PENGELUARAN_FIX_18
);

foreach ($tahap as $t) {
    if (!mysqli_query($conn1, $t[1])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Gagal menyiapkan ' . $t[0] . ': ' . mysqli_error($conn1)
                . '. Tidak ada data yang diubah.'
        ]);
        exit;
    }
}

/* ---- Tahap 7: ganti isi periode berikutnya, dalam satu transaksi ----------
   Semua tabelnya InnoDB. Dibungkus transaksi supaya tidak mungkin ada
   keadaan "periode lama sudah terhapus tapi yang baru gagal masuk". */
mysqli_query($conn1, 'START TRANSACTION');

$hapus = mysqli_query($conn1, "DELETE FROM whs_saldo_awal_nilai_persediaan WHERE tgl_periode = '$np_esc'");
if (!$hapus) {
    mysqli_query($conn1, 'ROLLBACK');
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data periode lama: ' . mysqli_error($conn1) . '. Tidak ada data yang diubah.']);
    exit;
}

$sql = <<<SQL_AKHIR
INSERT INTO whs_saldo_awal_nilai_persediaan
WITH
adjustment as (select no_barcode_mapping, qty, price, total from whs_adjust_nilai_persediaan where tgl_periode BETWEEN '$sd_esc' and '$ed_esc'),

mutasi as (select a.*, COALESCE(out_prod_qty,0) out_prod_qty, COALESCE(out_prod_total,0) out_prod_total, COALESCE(out_prod_price,0) out_prod_price, COALESCE(out_subcont_qty,0) out_subcont_qty, COALESCE(out_subcont_total,0) out_subcont_total, COALESCE(out_subcont_price,0) out_subcont_price, COALESCE(out_lokal_qty,0) out_lokal_qty, COALESCE(out_lokal_total,0) out_lokal_total, COALESCE(out_lokal_price,0) out_lokal_price, COALESCE(out_impor_qty,0) out_impor_qty, COALESCE(out_impor_total,0) out_impor_total, COALESCE(out_impor_price,0) out_impor_price, COALESCE(out_sample_qty,0) out_sample_qty, COALESCE(out_sample_total,0) out_sample_total, COALESCE(out_sample_price,0) out_sample_price, COALESCE(out_salnongroup_qty,0) out_salnongroup_qty, COALESCE(out_salnongroup_total,0) out_salnongroup_total, COALESCE(out_salnongroup_price,0) out_salnongroup_price, COALESCE(out_salgroup_qty,0) out_salgroup_qty, COALESCE(out_salgroup_total,0) out_salgroup_total, COALESCE(out_salgroup_price,0) out_salgroup_price, COALESCE(out_other_qty,0) out_other_qty, COALESCE(out_other_total,0) out_other_total, COALESCE(out_other_price,0) out_other_price, COALESCE(jumlah_out_qty,0) jumlah_out_qty, COALESCE(jumlah_out_total,0) jumlah_out_total, COALESCE(jumlah_out_price,0) jumlah_out_price, COALESCE(qty,0) qty_adjust, COALESCE(total,0) total_adjust, COALESCE(price,0) price_adjust from tmp_pemasukan_fix a left join tmp_pengeluaran_fix b on b.id_roll = a.barcode_mapping left join adjustment c on c.no_barcode_mapping = a.barcode_mapping),

data_mutasi as (select *, COALESCE(saldo_awal_qty + jumlah_in_qty - jumlah_out_qty - qty_adjust,0) saldo_akhir_qty, COALESCE(saldo_awal_total + jumlah_in_total - jumlah_out_total - total_adjust,0) saldo_akhir_total, COALESCE((saldo_awal_total + jumlah_in_total - jumlah_out_total - total_adjust) / (saldo_awal_qty + jumlah_in_qty - jumlah_out_qty - qty_adjust),0) saldo_akhir_price from mutasi)

select '', no_barcode, no_dok, tgl_dok, supplier, kode_lok, id_jo, no_ws ws, style, id_item, no_lot, no_roll, satuan, ROUND(saldo_akhir_qty,2) qty, 'IDR' curr, saldo_akhir_price price, 1 rate, '$np_esc' tgl_periode from data_mutasi where ROUND(saldo_akhir_qty,2) != 0
SQL_AKHIR;

$result = mysqli_query($conn1, $sql);

if ($result) {
    $affected = mysqli_affected_rows($conn1);
    mysqli_query($conn1, 'COMMIT');
    echo json_encode([
        'status'  => 'ok',
        'message' => "Copy saldo berhasil. $affected baris diinsert untuk periode $next_period."
    ]);
} else {
    $pesan = mysqli_error($conn1);
    mysqli_query($conn1, 'ROLLBACK');
    echo json_encode([
        'status'  => 'error',
        'message' => 'Query gagal: ' . $pesan . '. Tidak ada data yang diubah.'
    ]);
}

/* Tabel sementara ikut hilang saat koneksi ditutup, tapi dibuang eksplisit
   supaya tidak menumpuk kalau koneksinya dipakai ulang. */
foreach (array('tmp_map', 'tmp_saldo_awal', 'tmp_trx_in_fix', 'tmp_trx_out_fix', 'tmp_pemasukan_fix', 'tmp_pengeluaran_fix') as $t) {
    mysqli_query($conn1, "DROP TEMPORARY TABLE IF EXISTS `$t`");
}
