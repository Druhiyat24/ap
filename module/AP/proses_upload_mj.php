

<?php 
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();
$user = $_SESSION['username'];
$kode=$_GET['kodepay'];
$periode=$_GET['periode'];


            // menghubungkan dengan koneksi
            $koneksi = mysqli_connect("10.10.5.2","root","ERP@S19n4lB1t","signalbit_erp");
            
            // menghubungkan dengan library excel reader
            include "excel_reader.php";
            
            
            // upload file xls

            $target = basename($_FILES['fileexcel']['name']) ;
            move_uploaded_file($_FILES['fileexcel']['tmp_name'], $target);

            //  $uploads_dir = "history/";
            // $target = basename($_FILES['fileexcel']['name']) ;
            //  move_uploaded_file($_FILES['fileexcel']['tmp_name'], "$uploads_dir/$target");
            
            // beri permisi agar file xls dapat di baca
            chmod($_FILES['fileexcel']['name'],0777);
            
            // mengambil isi file xls
            $data = new Spreadsheet_Excel_Reader($_FILES['fileexcel']['name'],false);
            // menghitung jumlah baris data yang ada
            $jumlah_baris = $data->rowcount($sheet_index=0);
            
            // jumlah default data yang berhasil di import
            
            for ($i=2; $i<=$jumlah_baris; $i++){
            
                $angka = substr($kode,12,5);
                $bln =  date("m",strtotime($periode));
                $thn =  date("y",strtotime($periode));
                $huruf = "GM/NAG/$bln$thn";
                // menangkap data dan memasukkan ke variabel sesuai dengan kolumnya masing-masing
                $no_mj = $data->val($i, 1);
                $mjno = $angka + $no_mj;
                $kode_mj = $huruf ."/". sprintf("%05s", $mjno);
                // $date_mj = date('Y-m-d');
                $mj_date =  date("Y-m-d",strtotime($data->val($i, 2)));

                $sqlz = mysqli_query($conn2,"select IF(rate like ',',ROUND(rate,2),rate) as rate , tanggal  FROM masterrate where tanggal = '$mj_date' and v_codecurr = 'PAJAK'");
                $rowz = mysqli_fetch_array($sqlz);
                $ratez = $rowz['rate'];
                if ($ratez != '') {
                    $rates = $ratez;
                }else{

                    $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
                    $rowx = mysqli_fetch_array($sqlx);
                    $maxid = $rowx['id'];

                    $sqly = mysqli_query($conn2,"select IF(rate like ',',ROUND(rate,2),rate) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'PAJAK'");
                    $rowy = mysqli_fetch_array($sqly);
                    $rates = $rowy['rate']; 
                }

                $id_cmj = $data->val($i, 3);
                $no_coa = $data->val($i, 4);
                $no_costcenter = $data->val($i, 5);
                $no_reff =  $data->val($i, 6);
                $reff_date = date("Y-m-d",strtotime($data->val($i, 7)));
                $buyer = $data->val($i, 8);
                $no_ws = $data->val($i, 9);
                $curr = $data->val($i, 10);
                $debit = $data->val($i, 11);
                $credit =$data->val($i, 12);
                if ($curr == 'IDR') {
                    $rate = 1;
                    $debit_idr = $debit;
                    $credit_idr = $credit;
                }else{
                    $rate = $rates;
                    $debit_idr = $debit * $rate;
                    $credit_idr = $credit * $rate;
                }
                $keterangan = $data->val($i, 13);
                $status = "Post";
                $create_user = $user;
                $create_date = date("Y-m-d H:i:s");
            
                if($no_mj != "" && $mj_date != "" && $id_cmj != "" && $debit != "" || $no_mj != "" && $mj_date != "" && $id_cmj != "" && $credit != "")
                {
                    // input data ke database (table barang)
                    mysqli_query($koneksi,"INSERT into tbl_memorial_journal_temp values('','$kode_mj','$mj_date','$id_cmj', '$no_coa','$no_costcenter','$no_reff','$reff_date', '$buyer','$no_ws','$curr','$rate', '$debit','$credit','$debit_idr','$credit_idr', '$keterangan','$status','$create_user', '$create_date','','','','')");
                }
            }

            // mysqli_query($koneksi,"INSERT into  tbl_log_upload values('','$create_user','$periode')");
            
            // hapus kembali file .xls yang di upload tadi
            unlink($_FILES['fileexcel']['name']);
            
            // alihkan halaman ke index.php
            header("location:upload-memorial-journal.php");
            ?>