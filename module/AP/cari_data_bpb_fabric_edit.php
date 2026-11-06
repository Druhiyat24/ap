<?php
/* ============================================================================
   Update BPB - FABRIC.  Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen kain punya tabel kepala sendiri: whs_inmaterial_fabric (+_det)
   utk PENERIMAAN (GK/IN) dan whs_bppb_h/whs_bppb_ro utk RETUR (GK/RO).
   Keduanya dibedakan, karena jurnal GK/RO arahnya TERBALIK dari GK/IN.
   (Bandingkan Accessories & General: dokumennya langsung di `bpb` dan
   RI-nya tetap dihitung sbg penerimaan.)
   ============================================================================ */
include '../../conn/conn.php';
$jenis = 'fabric';
header('Content-Type: application/json');

$nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : 'ALL';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

$whereIn = "a.status != 'Cancel' AND a.no_po IS NOT NULL AND a.no_po != ''";
$whereOut = "h.status != 'Cancel' AND r.price IS NOT NULL AND r.price > 0";
/* Accessories: dokumennya langsung di `bpb`. GACC/IN maupun GACC/RI
   sama-sama ikut - keduanya dijurnal sbg penerimaan. */
$whereAcc = "a.bpbno_int LIKE 'GACC/%' AND IFNULL(a.cancel,'N') <> 'Y'"
          . " AND a.pono IS NOT NULL AND a.pono != '' AND a.price > 0";

if ($nama_supp !== 'ALL' && $nama_supp !== '') {
    $nama_supp_esc = mysqli_real_escape_string($conn1, $nama_supp);
    $whereIn .= " AND a.supplier = '$nama_supp_esc'";
    $whereOut .= " AND h.tujuan = '$nama_supp_esc'";
    $whereAcc .= " AND ms.Supplier = '$nama_supp_esc'";
}

if (!empty($start_date) && !empty($end_date)) {
    $start_date_esc = mysqli_real_escape_string($conn1, $start_date);
    $end_date_esc = mysqli_real_escape_string($conn1, $end_date);
    $whereIn .= " AND a.tgl_dok BETWEEN '$start_date_esc' AND '$end_date_esc'";
    $whereOut .= " AND h.tgl_bppb BETWEEN '$start_date_esc' AND '$end_date_esc'";
    $whereAcc .= " AND a.bpbdate BETWEEN '$start_date_esc' AND '$end_date_esc'";
}

/* ---------------------------------------------------------------------------
   PPN: DIBACA DARI JURNAL, BUKAN DARI PO.

   Dulu di sini:
       IFNULL(b.ppn, d.tax) ppn_rate   (cabang GK/IN)
       IFNULL(r.ppn, d2.tax) ppn_rate  (cabang GK/RO)
   lalu dibandingkan ke d.tax / d2.tax. Itu TAUTOLOGI untuk setiap baris yang
   kolom ppn-nya kosong - dan kosongnya mayoritas: whs_inmaterial_fabric_det
   16.370 dari 18.288 NULL, whs_bppb_ro 489 dari 528 NULL. Akibatnya badge
   "matches PO" praktis tidak pernah memperhitungkan PPN, dan kolom PPN di
   layar sebenarnya memuat pajak PO.

   Ditemukan pemakai 6 Nov 2026 di varian General (GEN/IN/1026/03340); cacat
   yang sama ada di keempat salinan.

   Yang dipakai sekarang: baris 1.52.07 di jurnal BPB dokumen itu.
   AMBANGNYA 0,05, diukur bukan ditebak - noise pembulatan berhenti di 0,01,
   selisih nyata mulai di 1,19. JANGAN diturunkan ke 0,01: selisih yang
   PERSIS 0,01 terbaca 0,010000000009 dalam pecahan biner.
   --------------------------------------------------------------------------- */
$sql = mysqli_query($conn1, "SELECT t.no_dok, t.tgl_dok, t.supplier, t.no_po, MAX(t.curr) curr,
        ROUND(SUM(t.qty_good),2) qty,
        ROUND(SUM(t.qty_good * t.price),2) dpp,
        MAX(t.tax_po) tax_po,
        MIN(t.price_match) price_match
    FROM (
        SELECT a.no_dok, a.tgl_dok, a.supplier, IFNULL(a.no_po,'-') no_po,
            b.qty_good, IFNULL(b.price_update, b.price) price, b.curr,
            d.tax tax_po,
            CASE
                WHEN pi.price IS NULL OR d.tax IS NULL THEN 0
                WHEN ABS(IFNULL(b.price_update, b.price) - pi.price) < 0.0001 THEN 1
                ELSE 0
            END price_match
        FROM whs_inmaterial_fabric a
        INNER JOIN whs_inmaterial_fabric_det b ON b.no_dok = a.no_dok
        LEFT JOIN po_header d ON d.pono = a.no_po
        LEFT JOIN masteritem pm ON pm.id_item = b.id_item
        LEFT JOIN po_item pi ON pi.id_po = d.id AND pi.id_jo = b.id_jo AND pi.id_gen = pm.id_gen AND pi.cancel = 'N'
        WHERE $whereIn

        UNION ALL

        SELECT h.no_bppb no_dok, h.tgl_bppb tgl_dok, h.tujuan supplier, IFNULL(r.no_po,'-') no_po,
            r.qty_out qty_good, r.price, r.curr,
            d2.tax tax_po,
            CASE
                WHEN pi2.price IS NULL OR d2.tax IS NULL THEN 0
                WHEN ABS(r.price - pi2.price) < 0.0001 THEN 1
                ELSE 0
            END price_match
        FROM whs_bppb_h h
        INNER JOIN whs_bppb_ro r ON r.no_bppb = h.no_bppb
        LEFT JOIN po_header d2 ON d2.pono = r.no_po
        LEFT JOIN masteritem pm2 ON pm2.id_item = r.id_item
        LEFT JOIN po_item pi2 ON pi2.id_po = d2.id AND pi2.id_jo = r.id_jo AND pi2.id_gen = pm2.id_gen AND pi2.cancel = 'N'
        WHERE $whereOut
    ) t
    GROUP BY t.no_dok, t.tgl_dok, t.supplier, t.no_po
    ORDER BY t.tgl_dok DESC, t.no_dok DESC");
$baris = [];
while ($row = mysqli_fetch_assoc($sql)) { $baris[] = $row; }

/* PPN yang BENAR-BENAR terbukukan, satu query untuk seluruh daftar.
   Tidak dijadikan JOIN di query utama: tbl_list_journal 600 ribu baris lebih,
   dan daftar ini paling banyak beberapa ratus dokumen - menyaring lewat IN
   pada indeks `no_journal` jauh lebih murah daripada menggabung semuanya.

   REVERSE IKUT TERHITUNG, dan itu sebabnya dipakai SUM(debit - credit) dan
   bukan "ambil posting terakhir". Polanya: posting asli, lalu baris
   'Reverse AP - BPB' bertanda berlawanan, lalu posting baru - jadi jumlah
   bersihnya = posting terakhir. Diuji ke SELURUH 203 dokumen yang pernah
   di-reverse: sama persis dgn batch sesudah reverse terakhir, selisih
   terbesar 0,00. Yang di-reverse tanpa posting ulang bersihnya 0, dan itu
   juga benar.

   KHUSUS FABRIC, ARAH TANDA WAJIB diperiksa - bukan kehati-hatian kosong.
   Cabang kedua daftar ini (GK/RO, retur) dijurnal 'AP - BPB RETURN' dan
   PPN-nya dibukukan sbg KREDIT: di data uji seluruh 24 baris PPN GK/RO
   berkredit (Rp 82.256.037), nol berdebet. Kalau tandanya tidak diurus,
   SETIAP dokumen RO akan tertandai tidak cocok PO. Jadi yang dibandingkan
   besarannya, tapi tandanya tetap harus sesuai jenis dokumennya - retur
   berkredit, penerimaan berdebet. */
$jurPpn = [];     /* no_dok => nilai PPN bersih di jurnal (bertanda) */
$adaJur = [];     /* no_dok => dokumen ini sudah punya jurnal BPB */
$jurRetur = [];   /* no_dok => jurnalnya jenis RETURN */
if ($baris) {
    $nomor = [];
    foreach ($baris as $b) {
        $nomor[] = "'" . mysqli_real_escape_string($conn1, $b['no_dok']) . "'";
    }
    $in = implode(',', $nomor);
    $qj = mysqli_query($conn1, "SELECT no_journal,
                SUM(CASE WHEN no_coa = '1.52.07' THEN debit - credit ELSE 0 END) ppn,
                COUNT(*) n,
                MAX(type_journal LIKE '%RETURN%') retur
            FROM tbl_list_journal
            WHERE no_journal IN ($in) AND type_journal LIKE '%BPB%'
            GROUP BY no_journal");
    if ($qj) {
        while ($j = mysqli_fetch_assoc($qj)) {
            $jurPpn[$j['no_journal']]   = round((float) $j['ppn'], 2);
            $adaJur[$j['no_journal']]   = ((int) $j['n']) > 0;
            $jurRetur[$j['no_journal']] = ((int) $j['retur']) === 1;
        }
    }
}

$data = [];
foreach ($baris as $row) {
    $row['tgl_dok_fmt'] = !empty($row['tgl_dok']) ? date('d-M-Y', strtotime($row['tgl_dok'])) : '-';
    $dpp     = (float) $row['dpp'];
    $tax_po  = (float) $row['tax_po'];
    $ppn_po  = round($dpp * $tax_po / 100, 2);          /* yang SEHARUSNYA menurut PO */
    $sudah   = isset($adaJur[$row['no_dok']]);

    if ($sudah) {
        /* Sudah diapprove: yang ditampilkan PPN yang benar-benar dibukukan,
           besarannya - sebaris dgn DPP & Qty yang juga ditampilkan positif. */
        $ppnJur  = isset($jurPpn[$row['no_dok']]) ? $jurPpn[$row['no_dok']] : 0.0;
        $retur   = !empty($jurRetur[$row['no_dok']]);
        $arahOk  = (abs($ppnJur) <= 0.005) || (($ppnJur < 0) === $retur);
        $row['ppn']      = abs($ppnJur);
        $row['ppn_dari'] = 'jurnal';
        $ppn_cocok       = $arahOk && abs(abs($ppnJur) - $ppn_po) <= 0.05;
    } else {
        /* Belum diapprove - belum ada jurnal, jadi belum ada apa pun yang
           bisa berselisih dgn PO. Yang ditampilkan nilai menurut PO, yaitu
           yang AKAN dibukukan, dan PPN tidak ikut menentukan badge-nya. */
        $row['ppn']      = $ppn_po;
        $row['ppn_dari'] = 'po';
        $ppn_cocok       = true;
    }

    $row['ppn_po']   = $ppn_po;
    $row['total']    = round($dpp + $row['ppn'], 2);
    $row['is_match'] = (((int) $row['price_match']) === 1) && $ppn_cocok;
    $data[] = $row;
}

echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
?>
