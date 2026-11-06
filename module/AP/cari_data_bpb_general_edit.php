<?php
include '../../conn/conn.php';
header('Content-Type: application/json');
/* ============================================================================
   Update BPB - GENERAL : daftar dokumen BPB utk dipilih di form Create.
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen General ada LANGSUNG di tabel `bpb` (tidak punya tabel kepala
   seperti whs_inmaterial_fabric pada Fabric). GEN/IN maupun GEN/RI ikut
   semua - diperiksa ke data 8 Okt 2026, jurnal GEN/RI SEARAH dgn penerimaan
   (persediaan didebit, GR/IR dikredit, type 'AP - BPB'), jadi bukan retur
   akuntansi seperti GK/RO di Fabric.

   AWAS - kunci sambungan ke PO BEDA dari Fabric/Accessories:
     masteritem.id_gen  KOSONG (NULL) untuk SEMUA item GEN (4.858 dari 4.858),
     po_item.id_gen     justru berisi id_item-nya langsung.
   Jadi di sini dipakai  pi.id_gen = a.id_item .
     dgn pi.id_gen = masteritem.id_gen :     0 dari 4.816 baris dapat harga PO
     dgn pi.id_gen = a.id_item          : 4.812 dari 4.816 baris dapat harga PO
   Kalau kuncinya salah, kolom is_match selalu 0 - tombol "isi dari PO" dan
   ceklis "sembunyikan baris yang sudah cocok" mati tanpa pesan apa pun.
   ============================================================================ */

$nama_supp  = isset($_POST['nama_supp'])  ? $_POST['nama_supp']  : 'ALL';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date   = isset($_POST['end_date'])   ? $_POST['end_date']   : '';

$where = "a.bpbno_int LIKE 'GEN/%' AND IFNULL(a.cancel,'N') <> 'Y'"
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
   Itu TAUTOLOGI: `bpb.ppn` NULL untuk SELURUH 32.860 baris GEN (diperiksa
   6 Nov 2026 ke basis data uji), jadi ruas kiri selalu menjadi d.tax dan
   syaratnya selalu benar. Dua akibatnya:
     - badge "matches PO" tidak pernah memperhitungkan PPN sama sekali,
     - kolom PPN di layar sebenarnya memuat PAJAK PO, bukan PPN yang
       benar-benar terbukukan.
   Pemakai menemukannya 6 Nov 2026 pada GEN/IN/1026/03340: layar menulis PPN
   473.000 dan "matches PO", padahal jurnalnya tidak punya baris PPN.

   Yang dipakai sekarang: baris 1.52.07 di jurnal BPB dokumen itu - itulah
   PPN yang benar-benar dibukukan. Dibandingkan ke dpp x pajak PO.
   AMBANGNYA 0,05, dan angka itu DIUKUR bukan ditebak. Seluruh dokumen GEN
   yang sudah diapprove dan punya jurnal (11.266 dokumen):
       selisih 0            : 11.201
       selisih <= 0,01      :      5   <- pembulatan dua langkah
       selisih 1,19         :      1   <- memang meleset (GEN/IN/0325/00569)
       selisih > 1.000      :     59   <- memang meleset
   Jadi noise pembulatan berhenti di 0,01 dan selisih nyata mulai di 1,19.

   JANGAN diturunkan ke 0,01: selisih pembulatan yang PERSIS 0,01 terbaca
   sebagai 0,010000000009 dalam bilangan pecahan biner, sehingga `<= 0.01`
   menolaknya. Dua dokumen (GEN/IN/0926/02629, GEN/IN/0926/02661) pernah
   salah ditandai karena itu.
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
        LEFT JOIN po_item pi ON pi.id_po = d.id AND pi.id_jo = a.id_jo AND pi.id_gen = a.id_item AND pi.cancel = 'N'
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

   REVERSE SUDAH IKUT TERHITUNG, dan ini yang membuat `SUM(debit - credit)`
   dipilih daripada "ambil posting terakhir". Dokumen yang dibetulkan
   polanya begini (contoh nyata GEN/IN/0926/02671):

       AP - BPB          Dr 102.000     <- posting asli
       Reverse AP - BPB  Cr 102.000     <- dibalik
       AP - BPB          Dr 119.500     <- posting baru

   Baris reverse bertanda BERLAWANAN, jadi jumlah bersihnya = posting
   terakhir (119.500). Diuji ke SELURUH 203 dokumen GEN/GACC/GK yang pernah
   di-reverse: jumlah bersih SAMA PERSIS dgn batch sesudah reverse terakhir,
   selisih terbesar 0,00. Dokumen yang di-reverse dan TIDAK diposting ulang
   bersihnya 0 - juga benar, memang tidak ada yang terbukukan.

   Menjumlahkan lebih tahan banting daripada "ambil yang id-nya paling
   besar": tidak bergantung pada urutan id, dan tetap benar untuk
   Reverse Reverse (6 baris di GEN). Penjaganya ada di uji_ppn_bpb.php
   bagian 5 - kalau pola postingnya berubah, di situ ketahuannya.

   `LIKE '%BPB%'` sengaja ikut menangkap 'AP - BPB RETURN': untuk GEN tipe
   itu hanya milik GEN/OUT & GEN/RO, dan NOL dari keduanya masuk daftar ini
   (diperiksa 6 Nov 2026), tapi kalau suatu saat masuk, lebih baik ikut
   terhitung daripada diam-diam terlewat. */
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
           besarannya - sebaris dgn DPP & Qty yang juga ditampilkan positif.
           ARAH TANDA tetap diperiksa: dokumen RETUR dibukukan sbg KREDIT,
           penerimaan sbg DEBET; penerimaan yang malah berkredit bukan
           "cocok". Untuk GEN dokumen RETURN tidak pernah masuk daftar ini
           (0 dari seluruhnya), tapi ditulis sama dgn tiga salinan lain -
           di Fabric cabang GK/RO memang berkredit semua. */
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
