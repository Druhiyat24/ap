

<?php 

            // menghubungkan dengan koneksi
            $koneksi = mysqli_connect("localhost","root","","signalbit_erp");
            
            // menghubungkan dengan library excel reader
            include "excel_reader.php";
            
            
            // upload file xls
            $target = basename($_FILES['fileexcel']['name']) ;
            move_uploaded_file($_FILES['fileexcel']['tmp_name'], $target);
            
            // beri permisi agar file xls dapat di baca
            chmod($_FILES['fileexcel']['name'],0777);
            
            // mengambil isi file xls
            $data = new Spreadsheet_Excel_Reader($_FILES['fileexcel']['name'],false);
            // menghitung jumlah baris data yang ada
            $jumlah_baris = $data->rowcount($sheet_index=0);
            
            // jumlah default data yang berhasil di import
            
            for ($i=2; $i<=$jumlah_baris; $i++){
            
                // menangkap data dan memasukkan ke variabel sesuai dengan kolumnya masing-masing
                $nama_barang     	= $data->val($i, 1);
                $deskripsi_barang   = $data->val($i, 2);
                $jenis_barang  		= $data->val($i, 3);
                $harga_barang		= $data->val($i, 4);
            
                if($nama_barang != "" && $deskripsi_barang != "" && $jenis_barang != "" && $harga_barang != "")
                {
                    // input data ke database (table barang)
                    mysqli_query($koneksi,"INSERT into barang values('','$nama_barang','$deskripsi_barang','$jenis_barang', '$harga_barang')");
                }
            }
            
            // hapus kembali file .xls yang di upload tadi
            unlink($_FILES['fileexcel']['name']);
            
            // alihkan halaman ke index.php
            header("location:coba-upload.php");
            ?>