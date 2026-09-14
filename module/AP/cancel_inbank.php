<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi = $_POST['no_bi'];
$status = 'Cancel';
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];



$sql = "update tbl_bankin_arcollection set cancel_by='$cancel_user',cancel_date='$cancel_date', status='$status' where doc_num='$no_bi'";
$query = mysqli_query($conn2,$sql);

$sql2 = "insert into tbl_list_journal_cancel (select * from tbl_list_journal where no_journal='$no_bi')";
$query2 = mysqli_query($conn2,$sql2);

$sql4 = "update b_reportbank set status='$status' where no_doc='$no_bi'";
$query4 = mysqli_query($conn2,$sql4);

if(!$query2) {
	die('Error: ' . mysqli_error($conn2));
}else{
	$sql3 = "Delete from tbl_list_journal where no_journal='$no_bi'";
	$query3 = mysqli_query($conn2,$sql3);
}

/* =========================
   LEPAS KEMBALI BANK OUT-NYA
   Saat Bank In dibuat lewat jalur AR Collection, Bank Out yang dipakai ditandai
   b_bankout_h.stat_bi='Y' supaya tidak dipakai dua kali. Dulu penandaan itu
   TIDAK PERNAH dikembalikan waktu Bank In-nya di-cancel, padahal dropdown Bank
   Out menyaring "stat_bi != 'Y'" - akibatnya nomor Bank Out itu hilang selamanya
   dari pilihan walau Bank In-nya sudah batal.
   Pelepasan hanya dilakukan kalau TIDAK ADA Bank In lain yang masih aktif
   memakai Bank Out tsb.
========================= */

$no_bi_esc = mysqli_real_escape_string($conn2, $no_bi);

$sqlbk = mysqli_query($conn2, "select distinct no_reff from b_bankin_none where no_bankin = '$no_bi_esc' and no_reff like 'BK/%'");

while ($rowbk = mysqli_fetch_assoc($sqlbk)) {

	$bk = mysqli_real_escape_string($conn2, $rowbk['no_reff']);

	mysqli_query($conn2, "
	update b_bankout_h b set b.stat_bi = 'N'
	where b.no_bankout = '$bk'
	  and not exists (
	      select 1 from b_bankin_none n
	      inner join tbl_bankin_arcollection a on a.doc_num = n.no_bankin
	      where n.no_reff = b.no_bankout and a.status != 'Cancel'
	  )");
}

mysqli_close($conn2);

?>