<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$no_bpb = $_POST['no_bpb'];
$approve_user = $_POST['approve_user'];
$confirm_date = date("Y-m-d H:i:s");

if(isset($no_bpb)){
$sql = "update maintain_bpb_h set status = 'APPROVED', approve_by = '$approve_user', approve_date = '$confirm_date' where no_maintain = '$no_dok'";
$execute = mysqli_query($conn2,$sql);

$sql2 = "insert into tbl_list_journal (id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center) select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, status, keterangan, create_by, create_date, '$approve_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$no_bpb'";
$execute2 = mysqli_query($conn2,$sql2);

$sql3 = "update bpb set confirm = 'N', confirm_by = '', confirm_date = '', status_maintain = null where bpbno_int = '$no_bpb'";
$execute3 = mysqli_query($conn2,$sql3);

// Log perubahan data (dashboard) - reverse approve bppb FG/OUT, cuma buat FG/OUT.
// Ambil total (qty x price) yang lagi approved dulu, per (so_number + product item),
// sebelum di-reverse ke draft (confirm='N').
if (substr($no_bpb, 0, 6) == 'FG/OUT') {
	// PENTING: $conn2 itu resource dari mysql_connect() (bukan mysqli_connect()),
	// jadi harus pakai fungsi mysql_* (bukan mysqli_*) - kalau tercampur, mysqli_query()
	// balikin NULL diam-diam (gagal tanpa error) dan datanya nggak pernah ke-insert.
	$cekfgrev = mysql_query("
		SELECT a.so_no, e.product_item, a.curr, SUM(c.qty) qty_grup, SUM(c.qty * c.price) total_grup, MAX(c.price) price_grup
		FROM bppb c
		INNER JOIN so_det b ON b.id = c.id_so_det
		INNER JOIN so a ON a.id = b.id_so
		INNER JOIN act_costing d ON d.id = a.id_cost
		INNER JOIN masterproduct e ON e.id = d.id_product
		WHERE c.bppbno_int = '$no_bpb'
		GROUP BY a.so_no, e.product_item
	", $conn2);
	while ($datafgrev = mysql_fetch_array($cekfgrev)) {
		$so_number_log    = mysql_real_escape_string($datafgrev['so_no'], $conn2);
		$product_item_log = mysql_real_escape_string($datafgrev['product_item'], $conn2);
		$curr_log   = mysql_real_escape_string($datafgrev['curr'], $conn2);
		$qty_grup   = $datafgrev['qty_grup'];
		$price_grup = $datafgrev['price_grup'];
		$total_grup = $datafgrev['total_grup'];

		$sql_log = "INSERT INTO tbl_data_change_log (doc_number, ref_number ,so_number,product_item,source_table,action,field_name,qty_old,qty_new,price_old,price_new,total_old,total_new,curr,profit_center,created_by,created_at)
			VALUES ('$no_dok', '$no_bpb','$so_number_log','$product_item_log','bppb','Reverse SJ','total','$qty_grup','0','$price_grup','0','$total_grup','0','$curr_log','NAG','$approve_user','$confirm_date')";
		mysql_query($sql_log, $conn2);
	}
}

$sql4 = "update bppb set confirm = 'N', confirm_by = '', confirm_date = '', status_maintain = null where bppbno_int = '$no_bpb'";
$execute4 = mysqli_query($conn2,$sql4);

$sql5 = "update whs_inmaterial_fabric set status = 'Pending', approved_by = '', approved_date = '' where no_dok = '$no_bpb'";
$execute5 = mysqli_query($conn2,$sql5);

$sql6 = "update whs_bppb_h set status = 'Pending', approved_by = '', approved_date = '' where no_bppb = '$no_bpb'";
$execute6 = mysqli_query($conn2,$sql6);

$sql7 = "UPDATE bpb SET status_bpb = 'draft', confirm_administrasi = '' WHERE no_bpb = '$no_bpb'";
$execute7 = pg_query($conn4, $sql7);

$sql8 = "UPDATE gp_penerimaan_greige_h SET status_bpb = 'draft', confirm_administrasi = '' WHERE no_bpb = '$no_bpb'";
$execute8 = pg_query($conn4, $sql8);

$sql9 = "UPDATE bpb_celup SET status_bpb = 'draft', confirm_administrasi = '' WHERE no_bpb = '$no_bpb'";
$execute9 = pg_query($conn4, $sql9);

$sql10 = "delete from bpb_knitting where no_bpb = '$no_bpb'";
$execute10 = mysqli_query($conn2,$sql10);

$sql11 = "delete from tbl_tamb_bpb2 where no_bpb = '$no_bpb'";
$execute11 = mysqli_query($conn2,$sql11);

}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Approve';
}

mysqli_close($conn2);

?>