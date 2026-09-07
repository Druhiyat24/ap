<?php
// ============================================================================
// Endpoint DataTables AJAX — REPORT FAKTUR PAJAK.
// Balikan JSON: { "data": [ [ ...13 kolom... ], ... ] } (array per baris,
// urutan kolom SAMA dgn header di report-faktur-pajak.php).
//
// Query-nya SALINAN PERSIS dari report-faktur-pajak.php (3 sumber di-UNION):
//   1. bpb_scan_faktur  -> modul scan faktur (lama)
//   2. bpb_faktur_inv   -> modul input faktur manual (lama)
//   3. bpb_new          -> jalur IR/Kontrabon (bpb_docinfo_guard.php), dikecualikan
//                          bila no_bpb-nya sudah ada di bpb_faktur_inv (hindari dobel)
// Filter strip/'0' dipasang di LUAR union supaya berlaku seragam utk ketiganya.
//
// Urutan 13 kolom:
//   0 Nomor FP | 1 Tanggal FP | 2 Nomor BPB | 3 Tanggal BPB | 4 Referensi
//   5 Nama Barang | 6 Harga | 7 Qty | 8 DPP | 9 Diskon | 10 PPN | 11 Total
//   12 Support Doc
// ============================================================================
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$date_now   = date('Y-m-d');
$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : $date_now;
$end_date   = !empty($_POST['end_date'])   ? date('Y-m-d', strtotime($_POST['end_date']))   : $date_now;

$sql = mysqli_query($conn2, "SELECT * FROM (
 (SELECT a.id,b.kd_no_faktur no_faktur, b.tgl_faktur,c.no_bpb,c.tgl_bpb,no_referensi,nama_item,price,qty,diskon,dpp,ppn,(dpp + ppn) total,'-' sup_doc FROM bpb_scan_faktur a inner join bpb_scan_faktur_h b on b.kd_no_faktur = a.kd_no_faktur inner join bpb_faktur_inv c on c.no_faktur = b.kd_no_faktur where b.tgl_faktur BETWEEN '$start_date' and '$end_date' order by no_faktur,no_bpb,id asc)
  UNION all
  select a.* from (SELECT a.id, a.no_faktur, a.tgl_faktur, a.no_bpb, a.tgl_bpb, '-' no_referensi, b.itemdesc, price, sum(qty) qty, 0 diskon, sum(qty * price) dpp, sum((qty * price) * tax/100) ppn, sum((qty * price) + ((qty * price) * tax/100)) total,'-' sup_doc from bpb_faktur_inv a INNER JOIN bpb_new b on b.no_bpb = a.no_bpb where tgl_faktur BETWEEN '$start_date' and '$end_date' GROUP BY a.no_faktur, b.no_bpb, b.itemdesc, b.price order by no_faktur,a.no_bpb,a.id asc) a LEFT JOIN bpb_scan_faktur b on b.kd_no_faktur = a.no_faktur where b.id is null and a.no_faktur != ''
  UNION ALL
  SELECT MIN(b.id) id, b.upt_no_faktur no_faktur, b.upt_tgl_faktur tgl_faktur, b.no_bpb, b.tgl_bpb, '-' no_referensi, b.itemdesc nama_item, b.price, SUM(b.qty) qty, 0 diskon, SUM(b.qty * b.price) dpp, SUM((b.qty * b.price) * b.tax/100) ppn, SUM((b.qty * b.price) + ((b.qty * b.price) * b.tax/100)) total, '-' sup_doc
  FROM bpb_new b LEFT JOIN bpb_faktur_inv f ON f.no_dok = b.upt_dok_faktur
  WHERE b.upt_no_faktur IS NOT NULL AND b.upt_no_faktur <> '' AND b.upt_no_faktur <> '-' AND f.no_bpb IS NULL AND b.upt_tgl_faktur BETWEEN '$start_date' and '$end_date'
  GROUP BY b.upt_no_faktur, b.no_bpb, b.itemdesc, b.price
 ) z
 WHERE TRIM(COALESCE(z.no_faktur,'')) NOT IN ('','-','0')");

$data = [];
if ($sql) {
    while ($r = mysqli_fetch_assoc($sql)) {
        $data[] = [
            $r['no_faktur'],                                                                 // 0
            !empty($r['tgl_faktur']) && $r['tgl_faktur'] !== '0000-00-00'
                ? date('d-M-Y', strtotime($r['tgl_faktur'])) : '-',                          // 1
            $r['no_bpb'],                                                                    // 2
            $r['tgl_bpb'],                                                                   // 3
            $r['no_referensi'],                                                              // 4
            $r['nama_item'],                                                                 // 5
            number_format((float) $r['price'], 2),                                           // 6
            number_format((float) $r['qty'], 2),                                             // 7
            number_format((float) $r['dpp'], 2),                                             // 8
            number_format((float) $r['diskon'], 2),                                          // 9
            number_format((float) $r['ppn'], 2),                                             // 10
            number_format((float) $r['total'], 2),                                           // 11
            $r['sup_doc'],                                                                   // 12
        ];
    }
}

$out = ['data' => $data];
if (!$sql) { $out['db_error'] = mysqli_error($conn2); }  // bantu debug kalau query gagal
echo json_encode($out);
