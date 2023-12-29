<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$create_user = $_POST['create_user'];
$aktivitas = "Create Cash Out";
$from_ip = $_POST['from_ip'];
$create_date = date("Y-m-d H:i:s");
$doc_num = $_POST['doc_num'];
$doc_date =  date("Y-m-d",strtotime($_POST['doc_date']));
$bulan =  date("m",strtotime($_POST['doc_date']));
$tahun =  date("y",strtotime($_POST['doc_date']));
$pesan = $_POST['pesan'];
$total = $_POST['total'];
$type_co = $_POST['type_co'];
$coa_akun = "1.01.03";
$nama_coa = "KAS BESAR";
$curr = "IDR";





// echo $doc_number;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $no_ref;
// echo "< -- >";
// echo $ref_date;

$sqlnkb = mysqli_query($conn2,"select max(no_co) from c_cash_out where YEAR(tgl_co) = YEAR('$doc_date') AND MONTH(tgl_co) = MONTH('$doc_date')");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kodeBarang = $rownkb['max(no_co)'];
 $urutan = (int) substr($kodeBarang, 14, 5);
 $urutan++;
 $bln = $bulan;
 $thn = $tahun;
 $huruf = substr($doc_num,0,7);
 $kode = $huruf ."/". $bln."".$thn ."/". sprintf("%05s", $urutan);

$sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM c_report_pettycash where akun = '$coa_akun'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from c_report_pettycash where id = '$maxid'");
$rowy = mysqli_fetch_array($sqly);
$balance = $rowy['balance'];
$balance2 = $balance - $total;



$query = "INSERT INTO tbl_log_cash (nama_user,activitas,from_pc,log_date,doc_num,doc_date) 
VALUES 
	('$create_user', '$aktivitas', '$from_ip', '$create_date', '$kode', '$doc_date')";

$queryss = "INSERT INTO c_report_pettycash (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance, status) 
VALUES 
	('$doc_date', '$kode', '$pesan', '$coa_akun', '', '', '$curr', '0','$total', '$balance2', 'Draft')";

$executes = mysqli_query($conn2,$queryss);

$execute = mysqli_query($conn2,$query);


$queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$doc_date', '$type_co', '$coa_akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '0', '$total', '0', '$total', 'Draft', '$pesan', '$create_user', '$create_date', '', '', '', '')";

$executess2 = mysqli_query($conn2,$queryss2);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	echo 'Data Saved Successfully With No Cash Out '; echo $kode;
}

mysqli_close($conn2);
?>