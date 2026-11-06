<?php
include '../../conn/conn.php';
header('Content-Type: application/json');
/* ============================================================================
   Update BPB - ACCESSORIES : rincian satu dokumen BPB (modal di form Create).
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen aksesoris ada LANGSUNG di tabel `bpb`; GACC/IN maupun GACC/RI
   diperlakukan sama (keduanya penerimaan secara jurnal).

   Sambungan ke PO lewat masteritem.id_gen - utk item GACC kolom itu terisi.
   (Bandingkan dgn General yang memakai po_item.id_gen = bpb.id_item.)
   ============================================================================ */

$no_bpb = isset($_GET['no_bpb']) ? $_GET['no_bpb'] : '';
$no_bpb_esc = mysqli_real_escape_string($conn1, $no_bpb);

$result = ['ppn' => 0, 'items' => []];

$sql = mysqli_query($conn1, "SELECT a.id_jo, tmpjo.kpno no_ws, c.id_item, c.itemdesc desc_item,
        SUM(a.qty - IFNULL(a.qty_reject,0)) qty, a.unit, a.price, a.curr, 
        d.tax po_ppn, po_match.po_price, po_match.po_curr
    FROM bpb a
    INNER JOIN masteritem c ON c.id_item = a.id_item
    LEFT JOIN po_header d ON IFNULL(d.pono,'') = IFNULL(a.pono,'')
    LEFT JOIN (
        SELECT po.pono, pi.id_jo, pm.id_item, pi.curr po_curr, pi.price po_price
        FROM po_header po
        INNER JOIN po_item pi ON pi.id_po = po.id
        INNER JOIN masteritem pm ON pm.id_gen = pi.id_gen
        WHERE pi.cancel = 'N'
    ) po_match ON IFNULL(po_match.pono,'') = IFNULL(a.pono,'') AND IFNULL(po_match.id_jo,0) = IFNULL(a.id_jo,0) AND IFNULL(po_match.id_item,0) = IFNULL(c.id_item,0)
    LEFT JOIN (SELECT id_jo, kpno, styleno FROM act_costing ac INNER JOIN so ON ac.id = so.id_cost INNER JOIN jo_det jod ON so.id = jod.id_so GROUP BY id_jo) tmpjo ON tmpjo.id_jo = a.id_jo
    WHERE a.bpbno_int = '$no_bpb_esc' AND IFNULL(a.cancel,'N') <> 'Y'
    GROUP BY a.id_jo, a.id_item");

/* ---------------------------------------------------------------------------
   PPN SEKARANG = yang BENAR-BENAR terbukukan di jurnal, bukan pajak PO.

   Dulu kolom ini diisi IFNULL(<tabel>.ppn, d.tax) lalu dibandingkan ke
   d.tax - selalu sama untuk baris yang kolom ppn-nya kosong, dan kosongnya
   mayoritas. Akibatnya setiap baris dianggap sudah cocok PO pada sisi PPN
   dan ikut TERKUNCI (is_locked), sehingga PPN yang sebenarnya meleset justru
   tidak bisa dibetulkan dari sini. Keterangan lengkapnya di berkas
   cari_data_bpb_*_edit.php pasangannya.

   Jurnal menyimpan nilai PPN per DOKUMEN (COA 1.52.07), jadi tarifnya
   diturunkan sekali dari nilai itu dibagi DPP dokumennya, lalu dipakai untuk
   semua itemnya - memang satu tarif untuk satu dokumen.

   Kecocokannya dinilai dari NILAI (rupiah), bukan dari tarif. Membandingkan
   tarif menyesatkan: pada GEN/IN/0325/00569 tarif jurnalnya membulat jadi
   11,00 - sama dgn pajak PO - padahal nilainya meleset 1,19. Kalau modal
   memakai tarif sementara daftarnya memakai nilai, baris yang ditandai
   "tidak match" di daftar malah terkunci di sini. */
$ppnTerbukukan = null;   /* null = dokumen belum punya jurnal */
$ppnCocokDok   = true;
$ppnJur = 0.0; $dppDua = 0.0; $returDok = false;
$qd = mysqli_query($conn1, "SELECT
        (SELECT ROUND(SUM((qty - IFNULL(qty_reject,0)) * price),4) FROM bpb
          WHERE bpbno_int = '$no_bpb_esc' AND IFNULL(cancel,'N') <> 'Y') dpp,
        (SELECT COUNT(*) FROM tbl_list_journal
          WHERE no_journal = '$no_bpb_esc' AND type_journal LIKE '%BPB%') n_jurnal,
        (SELECT IFNULL(MAX(type_journal LIKE '%RETURN%'),0) FROM tbl_list_journal
          WHERE no_journal = '$no_bpb_esc' AND type_journal LIKE '%BPB%') retur,
        (SELECT IFNULL(ROUND(SUM(debit - credit),4),0) FROM tbl_list_journal
          WHERE no_journal = '$no_bpb_esc' AND type_journal LIKE '%BPB%'
            AND no_coa = '1.52.07') ppn_jurnal");
if ($qd && ($d = mysqli_fetch_assoc($qd))) {
    if ((int) $d['n_jurnal'] > 0) {
        $dppDok        = (float) $d['dpp'];
        $ppnJur        = round((float) $d['ppn_jurnal'], 2);
        $dppDua        = round($dppDok, 2);
        $returDok      = ((int) $d['retur']) === 1;
        /* Retur membukukan PPN sbg KREDIT, jadi tarifnya diambil dari
           besarannya - tarif negatif tidak ada artinya di dropdown. */
        $ppnTerbukukan = ($dppDok > 0) ? round(abs($ppnJur) / $dppDok * 100, 2) : 0.0;
    }
}

while ($row = mysqli_fetch_assoc($sql)) {
    /* Belum diapprove (belum ada jurnal): yang berlaku tarif PO, yaitu yang
       AKAN dibukukan - jadi tidak ada yang berselisih, dan barisnya memang
       boleh terkunci kalau harganya juga sudah sama. */
    $row['ppn'] = ($ppnTerbukukan !== null)
        ? $ppnTerbukukan
        : ($row['po_ppn'] !== null ? (float) $row['po_ppn'] : 0.0);

    $result['ppn'] = $row['ppn'];
    $row['id'] = $row['id_jo'] . '-' . $row['id_item'];
    $row['qty'] = round((float) $row['qty'], 2);

    if ($ppnTerbukukan === null) {
        $ppnCocokDok = true;
    } elseif ($row['po_ppn'] !== null) {
        $arahOk = (abs($ppnJur) <= 0.005) || (($ppnJur < 0) === $returDok);
        $ppnCocokDok = $arahOk
            && abs(abs($ppnJur) - round($dppDua * ((float) $row['po_ppn']) / 100, 2)) <= 0.05;
    } else {
        $ppnCocokDok = false;   /* ada jurnal tapi pajak PO tidak terbaca */
    }

    $priceMatches = $row['po_price'] !== null && abs((float) $row['price'] - (float) $row['po_price']) < 0.0001;
    $row['is_locked'] = $priceMatches && $ppnCocokDok;

    $result['items'][] = $row;
}

echo json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE);
?>
