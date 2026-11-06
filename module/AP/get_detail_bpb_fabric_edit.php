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

$no_bpb = isset($_GET['no_bpb']) ? $_GET['no_bpb'] : '';
$no_bpb_esc = mysqli_real_escape_string($conn1, $no_bpb);

$result = ['ppn' => 0, 'items' => []];

/* Accessories tidak punya tabel kepala - dokumennya langsung di `bpb`, dan
   GACC/IN maupun GACC/RI sama-sama diperlakukan sbg PENERIMAAN (jurnalnya
   searah). Bentuk kolom keluarannya dibuat
   SAMA PERSIS dgn cabang Fabric supaya pengolahan di bawah tidak berubah. */
$headerCheck = mysqli_query($conn1, "SELECT 1 FROM whs_inmaterial_fabric WHERE no_dok = '$no_bpb_esc' LIMIT 1");
$isPenerimaan = $headerCheck && mysqli_num_rows($headerCheck) > 0;

if ($isPenerimaan) {
    $sql = mysqli_query($conn1, "SELECT b.id_jo, tmpjo.kpno no_ws, c.id_item, c.itemdesc desc_item,
            SUM(b.qty_good) qty, b.unit, IFNULL(b.price_update, b.price) price, b.curr, 
            d.tax po_ppn, po_match.po_price, po_match.po_curr
        FROM whs_inmaterial_fabric a
        INNER JOIN whs_inmaterial_fabric_det b ON b.no_dok = a.no_dok
        INNER JOIN masteritem c ON c.id_item = b.id_item
        LEFT JOIN po_header d ON IFNULL(d.pono,'') = IFNULL(a.no_po,'')
        LEFT JOIN (
            SELECT po.pono, pi.id_jo, pm.id_item, pi.curr po_curr, pi.price po_price
            FROM po_header po
            INNER JOIN po_item pi ON pi.id_po = po.id
            INNER JOIN masteritem pm ON pm.id_gen = pi.id_gen
            WHERE pi.cancel = 'N'
        ) po_match ON IFNULL(po_match.pono,'') = IFNULL(a.no_po,'') AND IFNULL(po_match.id_jo,0) = IFNULL(b.id_jo,0) AND IFNULL(po_match.id_item,0) = IFNULL(c.id_item,0)
        LEFT JOIN (SELECT id_jo, kpno, styleno FROM act_costing ac INNER JOIN so ON ac.id = so.id_cost INNER JOIN jo_det jod ON so.id = jod.id_so GROUP BY id_jo) tmpjo ON tmpjo.id_jo = b.id_jo
        WHERE a.no_dok = '$no_bpb_esc'
        GROUP BY b.id_jo, b.id_item");
} else {
    $sql = mysqli_query($conn1, "SELECT r.id_jo, tmpjo.kpno no_ws, c.id_item, c.itemdesc desc_item,
            SUM(r.qty_out) qty, r.satuan unit, r.price, r.curr, 
            d.tax po_ppn, po_match.po_price, po_match.po_curr
        FROM whs_bppb_h h
        INNER JOIN whs_bppb_ro r ON r.no_bppb = h.no_bppb
        INNER JOIN masteritem c ON c.id_item = r.id_item
        LEFT JOIN po_header d ON IFNULL(d.pono,'') = IFNULL(r.no_po,'')
        LEFT JOIN (
            SELECT po.pono, pi.id_jo, pm.id_item, pi.curr po_curr, pi.price po_price
            FROM po_header po
            INNER JOIN po_item pi ON pi.id_po = po.id
            INNER JOIN masteritem pm ON pm.id_gen = pi.id_gen
            WHERE pi.cancel = 'N'
        ) po_match ON IFNULL(po_match.pono,'') = IFNULL(r.no_po,'') AND IFNULL(po_match.id_jo,0) = IFNULL(r.id_jo,0) AND IFNULL(po_match.id_item,0) = IFNULL(c.id_item,0)
        LEFT JOIN (SELECT id_jo, kpno, styleno FROM act_costing ac INNER JOIN so ON ac.id = so.id_cost INNER JOIN jo_det jod ON so.id = jod.id_so GROUP BY id_jo) tmpjo ON tmpjo.id_jo = r.id_jo
        WHERE h.no_bppb = '$no_bpb_esc'
        GROUP BY r.id_jo, r.id_item");
}


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
        IFNULL(
          (SELECT ROUND(SUM(b.qty_good * IFNULL(b.price_update, b.price)),4)
             FROM whs_inmaterial_fabric_det b WHERE b.no_dok = '$no_bpb_esc'),
          (SELECT ROUND(SUM(r.qty_out * r.price),4)
             FROM whs_bppb_ro r WHERE r.no_bppb = '$no_bpb_esc')) dpp,
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
