<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

function fmv_esc($conn, $v) {
    return mysqli_real_escape_string($conn, (string)$v);
}

$no_bpb = fmv_esc($conn2, $_POST['no_bpb']);
$no_bpb_asal = fmv_esc($conn2, $_POST['no_bpb_asal']);
$ceklist = fmv_esc($conn2, $_POST['ceklist']);
$create_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'GMF';
$create_user = fmv_esc($conn2, $_POST['create_user']);
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));
$invoiced = 'Waiting';
$confirm_date = '0000-00-00 00:00:00';
$tgl_po = fmv_esc($conn2, $_POST['tgl_po']);
$pterms = fmv_esc($conn2, $_POST['pterms']);


$sql = mysqli_query($conn2,"select no_bpb, no_po, '-' no_ws, tgl_bpb, '' tgl_po, c.id_supplier, a.supplier, nama_barang, '-' color, '-' size, jumlah_bpb qty,satuan uom, price, round(ppn/dpp * 100,0) tax, curr, '-' create_user, 'ibrahim' confirm1, b.id_item from bpb_knitting a inner join masteritem b on b.goods_code = a.id_item INNER JOIN (select DISTINCT id_supplier, supplier from mastersupplier where tipe_sup = 'S' GROUP BY supplier) c on c.supplier = a.supplier where a.no_bpb = '$no_bpb'");	


while($row= mysqli_fetch_assoc($sql)) {
	$nama_supp = fmv_esc($conn2, $row['supplier']);
	$no_po = fmv_esc($conn2, $row['no_po']);
	$ws = fmv_esc($conn2, $row['no_ws']);
	$curr = fmv_esc($conn2, $row['curr']);
	$tgl_bpb = fmv_esc($conn2, $row['tgl_bpb']);
	$top = $pterms;
	$item = fmv_esc($conn2, $row['nama_barang']);
	$color = '-';
	$size = '-';
	$qty = fmv_esc($conn2, $row['qty']);
	$uom = fmv_esc($conn2, $row['uom']);
	$price = fmv_esc($conn2, $row['price']);
	$confirm1 = fmv_esc($conn2, $row['confirm1']);
	$tax = fmv_esc($conn2, $row['tax']);
	$id_item = fmv_esc($conn2, $row['id_item']);
	$id_supplier = fmv_esc($conn2, $row['id_supplier']);


$query = mysqli_query($conn2,"INSERT INTO bpb_new (no_bpb, pono, ws, tgl_bpb, tgl_po, supplier, itemdesc, color, size, qty, uom, price, tax, curr, create_user, confirm1, confirm2, confirm_date, is_invoiced, status, top, pterms, create_date, update_date, update_user, ceklist, start_date, end_date, id_item, id_supplier, profit_center)
VALUES
	('$no_bpb', '$no_po', '$ws', '$tgl_bpb', '$tgl_po', '$nama_supp', '$item', '$color', '$size', '$qty', '$uom', '$price', '$tax', '$curr', '$create_user', '$confirm1', '','$confirm_date','$invoiced', '$status', '$top', '-', '$create_date', '$update_date', '$create_user', '$ceklist', '$start_date', '$end_date', '$id_item', '$id_supplier', 'NAK') ");

// Ambil error LANGSUNG setelah tiap query, sebelum query berikutnya jalan -
// sebelumnya mysqli_error() dipanggil di paling akhir (setelah 3 query
// jalan semua), jadi yang ke-tangkap selalu error query TERAKHIR (yang
// kebetulan sukses/kosong), bukan error query bpb_new yang sebenarnya gagal.
if (!$query) {
    die('Error insert bpb_new: ' . mysqli_error($conn2));
}

$query23 = mysqli_query($conn2,"INSERT INTO bpb_ri (bpb1, bpb2)
VALUES
	('$no_bpb', '$no_bpb_asal') ");

if (!$query23) {
    die('Error insert bpb_ri: ' . mysqli_error($conn2));
}

$sqla = "update bpb set ap_inv='1' where bpbno_int='$no_bpb'";
$querya = mysqli_query($conn2,$sqla);

if (!$querya) {
    die('Error update bpb: ' . mysqli_error($conn2));
}

}

mysqli_close($conn2);

?>