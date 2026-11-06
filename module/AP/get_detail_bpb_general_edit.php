<?php
include '../../conn/conn.php';
header('Content-Type: application/json');
/* ============================================================================
   Update BPB - GENERAL : rincian satu dokumen BPB (modal di form Create).
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen General ada LANGSUNG di tabel `bpb`; GEN/IN maupun GEN/RI
   diperlakukan sama (keduanya penerimaan secara jurnal).

   AWAS - sambungan ke PO memakai  pi.id_gen = bpb.id_item , BUKAN lewat
   masteritem.id_gen seperti Fabric/Accessories. masteritem.id_gen kosong
   untuk semua item GEN; po_item.id_gen berisi id_item-nya langsung.
   Kolom is_locked (harga & PPN sudah sama dgn PO) bergantung pada ini.
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
        SELECT po.pono, pi.id_jo, pi.id_gen id_item, pi.curr po_curr, pi.price po_price
        FROM po_header po
        INNER JOIN po_item pi ON pi.id_po = po.id
        WHERE pi.cancel = 'N'
    ) po_match ON IFNULL(po_match.pono,'') = IFNULL(a.pono,'') AND IFNULL(po_match.id_jo,0) = IFNULL(a.id_jo,0) AND IFNULL(po_match.id_item,0) = IFNULL(a.id_item,0)
    LEFT JOIN (SELECT id_jo, kpno, styleno FROM act_costing ac INNER JOIN so ON ac.id = so.id_cost INNER JOIN jo_det jod ON so.id = jod.id_so GROUP BY id_jo) tmpjo ON tmpjo.id_jo = a.id_jo
    WHERE a.bpbno_int = '$no_bpb_esc' AND IFNULL(a.cancel,'N') <> 'Y'
    GROUP BY a.id_jo, a.id_item");

/* ---------------------------------------------------------------------------
   PPN SEKARANG = yang BENAR-BENAR terbukukan di jurnal, bukan pajak PO.

   Dulu kolom ini diisi IFNULL(a.ppn, d.tax) lalu dibandingkan ke d.tax -
   selalu sama, karena `bpb.ppn` NULL untuk SELURUH baris GEN. Akibatnya
   setiap baris dianggap sudah cocok PO pada sisi PPN dan ikut terkunci
   (is_locked), sehingga PPN yang sebenarnya meleset tidak bisa dibetulkan
   dari sini. Keterangan lengkapnya di cari_data_bpb_general_edit.php.

   Jurnal menyimpan SATU baris PPN (1.52.07) per dokumen, jadi tarifnya
   diturunkan sekali dari nilai itu dibagi DPP dokumennya, lalu dipakai untuk
   semua itemnya - memang satu tarif untuk satu dokumen. */
$ppnTerbukukan = null;   /* null = dokumen belum punya jurnal */
$ppnCocokDok   = true;   /* nilai PPN jurnal sejalan dgn pajak PO? */
$qd = mysqli_query($conn1, "SELECT
        (SELECT ROUND(SUM((qty - IFNULL(qty_reject,0)) * price),4) FROM bpb
          WHERE bpbno_int = '$no_bpb_esc' AND IFNULL(cancel,'N') <> 'Y') dpp,
        (SELECT COUNT(*) FROM tbl_list_journal
          WHERE no_journal = '$no_bpb_esc' AND type_journal LIKE '%BPB%') n_jurnal,
        (SELECT IFNULL(ROUND(SUM(debit - credit),4),0) FROM tbl_list_journal
          WHERE no_journal = '$no_bpb_esc' AND type_journal LIKE '%BPB%'
            AND no_coa = '1.52.07') ppn_jurnal");
if ($qd && ($d = mysqli_fetch_assoc($qd))) {
    if ((int) $d['n_jurnal'] > 0) {
        $dppDok = (float) $d['dpp'];
        $ppnTerbukukan = ($dppDok > 0)
            ? round(((float) $d['ppn_jurnal']) / $dppDok * 100, 2)
            : 0.0;
        $ppnJur = round((float) $d['ppn_jurnal'], 2);
        $dppDua = round($dppDok, 2);
    }
}

/* Kecocokan PPN dinilai dari NILAInya (rupiah), bukan dari tarifnya.
   Membandingkan tarif menyesatkan: pada GEN/IN/0325/00569 tarif jurnalnya
   membulat jadi 11,00 - sama dgn pajak PO - padahal nilainya meleset 1,19.
   Kalau modal memakai tarif sementara daftar memakai nilai, barisnya
   ditandai "tidak match" di daftar TAPI terkunci di modal, jadi justru tidak
   bisa dibetulkan. Ambang 0,05 sama dgn di cari_data_bpb_general_edit.php -
   alasan angkanya ada di sana. */

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

    if ($ppnTerbukukan !== null && $row['po_ppn'] !== null) {
        $ppnCocokDok = abs($ppnJur - round($dppDua * ((float) $row['po_ppn']) / 100, 2)) <= 0.05;
    } elseif ($ppnTerbukukan === null) {
        /* Belum diapprove: belum ada yang bisa berselisih. */
        $ppnCocokDok = true;
    } else {
        $ppnCocokDok = false;   /* ada jurnal tapi PO tanpa pajak terbaca */
    }

    $priceMatches = $row['po_price'] !== null && abs((float) $row['price'] - (float) $row['po_price']) < 0.0001;
    $row['is_locked'] = $priceMatches && $ppnCocokDok;

    $result['items'][] = $row;
}

echo json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE);
?>
