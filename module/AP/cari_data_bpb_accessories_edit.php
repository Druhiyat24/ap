<?php
include '../../conn/conn.php';
header('Content-Type: application/json');
/* ============================================================================
   Update BPB - ACCESSORIES : daftar dokumen BPB utk dipilih di form Create.
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen aksesoris ada LANGSUNG di tabel `bpb` (tidak punya tabel kepala
   seperti whs_inmaterial_fabric pada Fabric). GACC/IN maupun GACC/RI ikut
   semua - diperiksa ke produksi 8 Okt 2026, jurnal GACC/RI SEARAH dgn
   penerimaan, jadi bukan retur akuntansi seperti GK/RO di Fabric.

   Sambungan ke PO lewat masteritem.id_gen (bukan id_item langsung seperti
   di General): utk item GACC kolom itu TERISI - diperiksa 8 Okt 2026, 9.446
   dari 9.446 baris dapat harga PO lewat jalur ini, sedangkan lewat id_item
   hasilnya 0.
   ============================================================================ */

$nama_supp  = isset($_POST['nama_supp'])  ? $_POST['nama_supp']  : 'ALL';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date   = isset($_POST['end_date'])   ? $_POST['end_date']   : '';

$where = "a.bpbno_int LIKE 'GACC/%' AND IFNULL(a.cancel,'N') <> 'Y'"
       . " AND a.pono IS NOT NULL AND a.pono != '' AND a.price > 0";

if ($nama_supp !== 'ALL' && $nama_supp !== '') {
    $nama_supp_esc = mysqli_real_escape_string($conn1, $nama_supp);
    $where .= " AND ms.Supplier = '$nama_supp_esc'";
}

if (!empty($start_date) && !empty($end_date)) {
    $start_date_esc = mysqli_real_escape_string($conn1, $start_date);
    $end_date_esc   = mysqli_real_escape_string($conn1, $end_date);
    $where .= " AND a.bpbdate BETWEEN '$start_date_esc' AND '$end_date_esc'";
}

/* ---------------------------------------------------------------------------
   PPN: DIBACA DARI JURNAL, BUKAN DARI PO.

   Dulu di sini:
       IFNULL(a.ppn, d.tax) ppn_rate
       ... ABS(IFNULL(a.ppn,d.tax) - d.tax) < 0.0001 ...
   Itu TAUTOLOGI: `bpb.ppn` praktis tidak pernah diisi (164.399 baris GACC (4 terisi)), jadi ruas kiri
   selalu menjadi d.tax dan syaratnya selalu benar. Dua akibatnya:
     - badge "matches PO" tidak pernah memperhitungkan PPN sama sekali,
     - kolom PPN di layar sebenarnya memuat PAJAK PO, bukan PPN yang
       benar-benar terbukukan.
   Ditemukan pemakai 6 Nov 2026 di varian General (GEN/IN/1026/03340), dan
   cacat yang sama ada di keempat salinan. Dampak terukur di GACC: 1 dari 7.384 dokumen sejak 2025.

   Yang dipakai sekarang: baris 1.52.07 di jurnal BPB dokumen itu - itulah
   PPN yang benar-benar dibukukan. Dibandingkan ke dpp x pajak PO.

   AMBANGNYA 0,05, diukur bukan ditebak. Dari 11.266 dokumen ber-jurnal:
   noise pembulatan berhenti di 0,01 (5 dokumen), lalu melompat ke 1,19 (1)
   dan >1.000 (59). JANGAN diturunkan ke 0,01: selisih yang PERSIS 0,01
   terbaca 0,010000000009 dalam pecahan biner, sehingga `<= 0.01` menolaknya.
   --------------------------------------------------------------------------- */
$sql = mysqli_query($conn1, "SELECT t.no_dok, t.tgl_dok, t.supplier, t.no_po, MAX(t.curr) curr,
        ROUND(SUM(t.qty_good),2) qty,
        ROUND(SUM(t.qty_good * t.price),2) dpp,
        MAX(t.tax_po) tax_po,
        MIN(t.price_match) price_match
    FROM (
        SELECT a.bpbno_int no_dok, a.bpbdate tgl_dok, ms.Supplier supplier, IFNULL(a.pono,'-') no_po,
            (a.qty - IFNULL(a.qty_reject,0)) qty_good, a.price, a.curr,
            d.tax tax_po,
            CASE
                WHEN pi.price IS NULL OR d.tax IS NULL THEN 0
                WHEN ABS(a.price - pi.price) < 0.0001 THEN 1
                ELSE 0
            END price_match
        FROM bpb a
        INNER JOIN mastersupplier ms ON ms.Id_Supplier = a.id_supplier
        LEFT JOIN po_header d ON d.pono = a.pono
        LEFT JOIN masteritem pm ON pm.id_item = a.id_item
        LEFT JOIN po_item pi ON pi.id_po = d.id AND pi.id_jo = a.id_jo AND pi.id_gen = pm.id_gen AND pi.cancel = 'N'
        WHERE $where
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
   juga benar. Menjumlahkan tidak bergantung urutan id, jadi tetap benar
   untuk 'Reverse Reverse AP - BPB' juga.

   ARAH TANDA ikut diperiksa: dokumen RETUR dibukukan sbg KREDIT (nilai
   bersihnya negatif), penerimaan sbg DEBET. Yang dibandingkan besarannya,
   tapi tandanya harus sesuai jenis dokumennya - kalau penerimaan malah
   berkredit, itu bukan "cocok". */
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
