<?php
header('Content-Type: application/json');

// Include koneksi database
include '../../conn/conn.php';

// Ambil parameter POST
$data = isset($_POST['data']) ? $_POST['data'] : null;

// Query SQL
$sql = "select a.no_bppb,a.id_item, a.no_ws, id_roll, a.curr curr_out, price_in price_out, b.curr, price_idr price_upt, (price_in - price_idr) diff from (select a.*, b.no_ws from whs_bppb_det a INNER JOIN whs_bppb_h b on b.no_bppb = a.no_bppb where tgl_bppb >= '2024-12-01') a left join (
select * from (select no_barcode, curr, price price_idr from ca_saldoawal_fabric
UNION
select no_barcode, curr, round((price + COALESCE(price_barang,0)),4) price_idr from (select dok_asal, tgl_dok_asal, a.no_dok, a.tgl_dok, id_jo, id_item, no_barcode, if(a.curr = '' OR a.curr is null, 'IDR', a.curr) curr, IF(rate is null,1,rate) rate, if(tipe_com ='FOC' OR tipe_com ='BUYER','0',if(price_update is null,price, price_update)) price, price_barang, qty_aktual, tipe_com, type_pch from (select CASE
        WHEN b.no_dok is null THEN a.no_dok
        ELSE b.no_dok
    END AS dok_asal, CASE
        WHEN b.tgl_dok is null THEN a.tgl_dok
        ELSE b.tgl_dok
    END AS tgl_dok_asal, a.no_dok, a.tgl_dok, a.id_jo, a.id_item, a.no_barcode, if(b.curr is null, a.curr, b.curr) curr, if(b.price is null, a.price, b.price) price, if(b.price_barang is null, a.price_barang, b.price_barang) price_barang, price_update, a.qty_aktual, b.no_dok asal, b.tgl_dok tgl_asal, b.no_barcode barcode_asal, b.no_barcode_old from (select * from (select curr, b.no_dok, if(a.tgl_dok is null, c.tgl_mut, a.tgl_dok) tgl_dok, b.id_jo, b.id_item, b.no_barcode, price, price_update, price_barang, qty_aktual, no_barcode_old from whs_lokasi_inmaterial b LEFT JOIN whs_inmaterial_fabric_det a on b.no_dok = a.no_dok and a.id_item = b.id_item and a.id_jo = b.id_jo left join whs_mut_lokasi_h c on c.no_mut = b.no_dok where b.status = 'Y' GROUP BY no_barcode) a where tgl_dok >= '2024-12-01') a 
LEFT JOIN
(select curr,b.no_dok, a.tgl_dok, b.id_jo, b.id_item, b.no_barcode, if(price_update is null,price, price_update) price, price_barang, qty_aktual, no_barcode_old from whs_lokasi_inmaterial b LEFT JOIN whs_inmaterial_fabric_det a on b.no_dok = a.no_dok and a.id_item = b.id_item and a.id_jo = b.id_jo where b.status = 'Y' GROUP BY no_barcode) b on b.no_barcode = a.no_barcode_old ) a LEFT JOIN (select a.no_dok, type_pch, UPPER(z.tipe_com) tipe_com from whs_inmaterial_fabric a LEFT join po_header po on po.pono = a.no_po LEFT join po_header_draft z on z.id = po.id_draft where tgl_dok >= '2024-12-01' GROUP BY a.no_dok) b on b.no_dok = a.dok_asal left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.tgl_dok_asal and cr.curr = a.curr) a) a) b on b.no_barcode = a.id_roll where (price_in - price_idr) != 0";

$result = $conn1->query($sql);

// Jika query gagal
if (!$result) {
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    exit;
}

// Ambil data dan ubah ke format JSON
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Output data
echo json_encode($data);

// Tutup koneksi
$conn1->close();
?>

