<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");


// $sqlnkb = mysqli_query($conn2,"select no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, post_by, post_date, update_by, update_date from tbl_memorial_journal_temp");

// while($row= mysqli_fetch_assoc($sqlnkb)) { 
//    $no_mj = $row['no_mj'];
//    $id_cmj = $row['id_cmj'];
//    $mj_date = $row['mj_date'];
//    $no_coa = $row['no_coa'];  
//    $no_costcenter = $row['no_costcenter'];
//    $no_reff = $row['no_reff'];
//    $reff_date = $row['reff_date'];
//    $buyer = $row['buyer'];
//    $no_ws = $row['no_ws'];
//    $curr = $row['curr'];
//    $rate = $row['rate'];
//    $debit = $row['debit'];
//    $credit = $row['credit'];
//    $debit_idr = $row['debit_idr'];
//    $credit_idr = $row['credit_idr'];
//    $status = $row['status'];
//    $keterangan = $row['keterangan'];
//    $create_by = $row['create_by'];
//    $create_date = $row['create_date'];


// $sqlcmj = mysqli_query($conn1,"select nama_cmj from master_category_mj where id_cmj = '$id_cmj'");
// $rowcmj = mysqli_fetch_array($sqlcmj);
// $nama_cmj = $rowcmj['nama_cmj'];

// $sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
// $rowcoa = mysqli_fetch_array($sqlcoa);
// $nama_coa = $rowcoa['nama_coa'];

// $sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$no_costcenter'");
// $rowcc = mysqli_fetch_array($sqlcc);
// $nama_cc = $rowcc['cc_name'];



//    $queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
// VALUES 
//    ('$no_mj', '$mj_date', '$nama_cmj', '$no_coa', '$nama_coa', '$no_costcenter', '$nama_cc', '$no_reff', '$reff_date', '$buyer', '$no_ws', '$curr', '$rate', '$debit', '$credit', '$debit_idr', '$credit_idr', '$status', '$keterangan', '$create_by', '$create_date', '', '', '', '')";

// $executess = mysqli_query($conn2,$queryss);

// }

// $sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM c_report_pettycash where akun = '$coa_akun'");
// $rowx = mysqli_fetch_array($sqlx);
// $maxid = $rowx['id'];

// $sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from c_report_pettycash where id = '$maxid'");
// $rowy = mysqli_fetch_array($sqly);
// $balance = $rowy['balance'];
// $balance2 = $balance + $total;

$query_jnrl = "INSERT INTO tbl_list_journal (select '',a.no_mj, a.mj_date, b.nama_cmj, a.no_coa,c.nama_coa, a.no_costcenter, d.cc_name, a.no_reff, a.reff_date, a.buyer, a.no_ws, a.curr, a.rate, a.debit, a.credit, a.debit_idr, a.credit_idr, a.status, a.keterangan, a.create_by, a.create_date, a.post_by, a.post_date, a.update_by, a.update_date from tbl_memorial_journal_temp a left join master_category_mj b on b.id_cmj = a.id_cmj left join mastercoa_v2 c on c.no_coa = a.no_coa left join b_master_cc d on d.no_cc = a.no_costcenter)";
$execute_jnrl = mysqli_query($conn2,$query_jnrl);

$query = "INSERT INTO tbl_memorial_journal (select '', no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, post_by, post_date, update_by, update_date from tbl_memorial_journal_temp)";
$execute = mysqli_query($conn2,$query);



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	// echo 'Data Saved Successfully With No '; echo $no_mj;
   $sql = "DELETE from tbl_memorial_journal_temp";
   $update = mysqli_query($conn2,$sql);
}

mysqli_close($conn2);
?>