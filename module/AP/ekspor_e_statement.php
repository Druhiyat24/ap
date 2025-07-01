<html>
<head>
    <title>Export Data Ke Excel </title>
</head>
<body>
    <style type="text/css">
        body{
            font-family: sans-serif;
        }
        table{
            margin: 20px auto;
            border-collapse: collapse;
        }
        table th,
        table td{
            border: 1px solid #3c3c3c;
            padding: 3px 8px;

        }
        a{
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=data e-statement.xls");
    $thn_from =$_GET['thn_from'];
    $bln_from =$_GET['bln_from'];
    $thn_to = $_GET['thn_to'];;
    $bln_to = $_GET['bln_to'];; ?>

    <h4>DATA E STATEMENT</h4>
    PERIODE <?php echo date("F",strtotime($bln_from)); ?> <?php echo $thn_from; ?> - <?php echo date("F",strtotime($bln_to)); ?> <?php echo $thn_to; ?>
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">Bank</th>
            <th style="text-align: center;vertical-align: middle;">Account</th>
            <th style="text-align: center;vertical-align: middle;">Periode</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">Upload Date</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $thn_from =$_GET['thn_from'];
        $bln_from =$_GET['bln_from'];
        $thn_to = $_GET['thn_to'];;
        $bln_to = $_GET['bln_to'];;
        // menampilkan data pegawai


        $sql = mysqli_query($conn2,"select * from (select a.bank_name,a.bank_account,a.kode_tanggal,a.bulan_text,a.nama_bulan,a.tahun, b.file_name,b.file_name_as,b.created_date, b.id, IF(b.id is null,'Belum Upload','Sudah Upload') stat_upload, status_bank from (select bank_name,bank_account,kode_tanggal,bulan_text,nama_bulan,tahun, status_bank from (select bank_name,bank_account, status status_bank from b_masterbank) a join
            (select kode_tanggal, bulan_text, nama_bulan,tahun from dim_date GROUP BY tahun,nama_bulan order by kode_tanggal asc) b where b.kode_tanggal >= concat('$thn_from','$bln_from','01') and b.kode_tanggal <= concat('$thn_to','$bln_to','01')) a left join (select id,kode_tanggal,account_bank,file_name,file_name_as,created_date from b_estatement order by id desc
        ) b on b.kode_tanggal = a.kode_tanggal and b.account_bank = bank_account order by bank_account, b.id desc) a LEFT JOIN (select kode_tanggal tgl_kode, account_bank akun, MAX(created_date) tgl_upload from b_estatement GROUP BY kode_tanggal, account_bank) b on b.tgl_kode = a.kode_tanggal and b.akun = a.bank_account where status_bank != 'Deactive' OR stat_upload != 'Belum Upload'");

        // echo "select * from (select a.bank_name,a.bank_account,a.kode_tanggal,a.bulan_text,a.nama_bulan,a.tahun, b.file_name,b.file_name_as,b.created_date, b.id, IF(b.id is null,'Belum Upload','Sudah Upload') stat_upload, status_bank from (select bank_name,bank_account,kode_tanggal,bulan_text,nama_bulan,tahun, status_bank from (select bank_name,bank_account, status status_bank from b_masterbank) a join
        //     (select kode_tanggal, bulan_text, nama_bulan,tahun from dim_date GROUP BY tahun,nama_bulan order by kode_tanggal asc) b where b.kode_tanggal >= concat('$thn_from','$bln_from','01') and b.kode_tanggal <= concat('$thn_to','$bln_to','01')) a left join (select id,kode_tanggal,account_bank,file_name,file_name_as,created_date from b_estatement order by id desc
        // ) b on b.kode_tanggal = a.kode_tanggal and b.account_bank = bank_account order by bank_account, b.id desc) a LEFT JOIN (select kode_tanggal tgl_kode, account_bank akun, MAX(created_date) tgl_upload from b_estatement GROUP BY kode_tanggal, account_bank) b on b.tgl_kode = a.kode_tanggal and b.akun = a.bank_account where status_bank != 'Deactive' OR stat_upload != 'Belum Upload'";

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

            $akun = $row['bank_account'];
            $kode_tgl = $row['kode_tanggal'];
            $stat_upload = $row['stat_upload'];
            $tglupload = isset($row['tgl_upload']) ? $row['tgl_upload'] : null;

            if ($tglupload == null) {
                $tgl_upload = '-'; 
            }else{
                $tgl_upload = date("d-M-Y H:i:s",strtotime($tglupload));
            }

            echo '<tr style="font-size:12px;text-align:left;">
            <td >'.$no++.'</td>
            <td value = "'.$row['bank_name'].'">'.$row['bank_name'].'</td>
            <td value = "'.$row['bank_account'].'">'.$row['bank_account'].'</td>
            <td value = "'.$row['nama_bulan'].'">'.$row['nama_bulan'].'</td>
            <td value = "'.$row['stat_upload'].'">'.$row['stat_upload'].'</td>
            <td value = "'.$tgl_upload.'">'.$tgl_upload.'</td>
            ';

        }
        ?>
    </table>

</body>
</html>