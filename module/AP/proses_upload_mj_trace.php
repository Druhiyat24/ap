<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$user = $_SESSION['username'];

$kode=$_GET['kodepay'];
$kode_sb=$_GET['kodepay_sb'];
$periode=$_GET['periode'];

// koneksi LIVE
$koneksi = mysqli_connect("10.10.5.12","root","ERP@S19n4lB1t","signalbit_erp");

// library excel reader
include "excel_reader.php";

// upload file
$target = basename($_FILES['fileexcel']['name']);
move_uploaded_file($_FILES['fileexcel']['tmp_name'], $target);

chmod($_FILES['fileexcel']['name'],0777);

// baca file excel
$data = new Spreadsheet_Excel_Reader($_FILES['fileexcel']['name'],false);

// jumlah baris
$jumlah_baris = $data->rowcount($sheet_index=0);

$success = 0;
$failed = 0;
$errors = [];

for ($i=2; $i<=$jumlah_baris; $i++){

    echo "Processing row : $i <br>";

    $angka = substr($kode,12,5);
    $angka_sb = substr($kode_sb,12,5);

    $bln =  date("m",strtotime($periode));
    $thn =  date("y",strtotime($periode));

    $huruf = "GM/NAG/$bln$thn";

    $no_mj = $data->val($i, 1);

    $mjno = $angka + $no_mj;
    $mjno_sb = $angka_sb + $no_mj;

    $kode_mj = $huruf ."/". sprintf("%05s", $mjno);
    $kode_mj_sb = $huruf ."/". sprintf("%05s", $mjno_sb);

    $mj_date =  date("Y-m-d",strtotime($data->val($i, 2)));

    $sqlz = mysqli_query($conn2,"select IF(rate like ',',ROUND(rate,2),rate) as rate , tanggal  
                                 FROM masterrate 
                                 where tanggal = '$mj_date' 
                                 and v_codecurr = 'PAJAK'");

    $rowz = mysqli_fetch_array($sqlz);
    $ratez = isset($rowz['rate']) ? $rowz['rate'] : '';

    if ($ratez != '') {
        $rates = $ratez;
    }else{

        $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
        $rowx = mysqli_fetch_array($sqlx);
        $maxid = $rowx['id'];

        $sqly = mysqli_query($conn2,"select IF(rate like ',',ROUND(rate,2),rate) as rate 
                                     FROM masterrate 
                                     where id = '$maxid' 
                                     and v_codecurr = 'PAJAK'");
        $rowy = mysqli_fetch_array($sqly);

        $rates = $rowy['rate'];
    }

    $id_cmj = $data->val($i, 3);
    $no_coa = $data->val($i, 4);
    $profit_center = $data->val($i, 5);
    $no_costcenter = $data->val($i, 6);
    $no_reff =  $data->val($i, 7);
    $reff_date = date("Y-m-d",strtotime($data->val($i, 8)));
    $buyer = $data->val($i, 9);
    $no_ws = $data->val($i, 10);
    $curr = $data->val($i, 11);

    $debit = $data->val($i, 12);
    $credit =$data->val($i, 13);

    if ($curr == 'IDR') {
        $rate = 1;
        $debit_idr = $debit;
        $credit_idr = $credit;
    }else{
        $rate = $rates;
        $debit_idr = $debit * $rate;
        $credit_idr = $credit * $rate;
    }

    $keterangan = $data->val($i, 14);
    $status = "Post";
    $fil_data = $data->val($i, 15);

    $create_user = $user;
    $create_date = date("Y-m-d H:i:s");

    $sql_pc = mysqli_query($conn2,"select kode_pc from master_pc where id_pc = '$profit_center' GROUP BY id_pc");
    $row_pc = mysqli_fetch_array($sql_pc);
    $kode_pc = isset($row_pc['kode_pc']) ? $row_pc['kode_pc'] : '';

    if($no_mj != "" && $mj_date != "" && $id_cmj != "" && ($debit != "" || $credit != ""))
    {

        $query1 = "INSERT into tbl_memorial_journal_temp 
                   values('','$kode_mj','$mj_date','$id_cmj', '$no_coa','$no_costcenter','$no_reff','$reff_date',
                   '$buyer','$no_ws','$curr','$rate', '$debit','$credit','$debit_idr','$credit_idr',
                   '$keterangan','$status','$create_user', '$create_date','','','','','$kode_pc')";

        $result = mysqli_query($koneksi,$query1);

        if(!$result){

            $failed++;

            $errors[] = "
            Row : $i
            Kode MJ : $kode_mj
            COA : $no_coa
            Debit : $debit
            Credit : $credit
            ERROR : ".mysqli_error($koneksi);

            continue;
        }

        if ($fil_data == 'YES') {

            $query2 = "INSERT into sb_memorial_journal_temp 
                       values('','$kode_mj_sb','$mj_date','$id_cmj', '$no_coa','$no_costcenter','$no_reff','$reff_date',
                       '$buyer','$no_ws','$curr','$rate', '$debit','$credit','$debit_idr','$credit_idr',
                       '$keterangan','$status','$create_user', '$create_date','','','','','$kode_pc')";

            mysqli_query($koneksi,$query2);

            $sqlx = mysqli_query($conn2,"select no_mj FROM status_memorial_journal where no_mj = '$kode_mj'");
            $rowx = mysqli_fetch_array($sqlx);
            $no_mj_cek = isset($rowx['no_mj']) ? $rowx['no_mj'] : '-';

            if ($no_mj_cek == '-') {

                $query_sb = "INSERT INTO status_memorial_journal 
                             (no_mj, mj_date, no_mj_sb, status, create_by, create_date) 
                             VALUES 
                             ('$kode_mj', '$mj_date', '$kode_mj_sb', 'Post', '$create_user', '$create_date')";

                mysqli_query($koneksi,$query_sb);
            }
        }

        $success++;
    }
}

// hapus file excel
unlink($_FILES['fileexcel']['name']);

echo "<hr>";
echo "<h2>IMPORT SELESAI</h2>";
echo "Total Baris : $jumlah_baris <br>";
echo "Berhasil : $success <br>";
echo "Gagal : $failed <br>";

if(!empty($errors)){
    echo "<h3>DETAIL ERROR</h3>";
    echo "<pre>";
    print_r($errors);
}

?>
