<html>
<head>
    <title>Export Data List Journal </title>
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
    include '../../conn/conn.php';
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=trial-balance-ytd.xls");
    // $nama_supp =$_GET['nama_supp'];
    // $status =$_GET['status'];
    $bulan_awal = date("m",strtotime($_GET['start_date']));
    $bulan_akhir = date("m",strtotime($_GET['end_date']));  
    $tahun_awal = date("Y",strtotime($_GET['start_date']));
    $tahun_akhir = date("Y",strtotime($_GET['end_date'])); 
    $kata_filter = $_GET['kata_filter'];

    $sqlawal = mysqli_query($conn2,"select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal'");
    $rowawal = mysqli_fetch_array($sqlawal);
    $tgl_awal = isset($rowawal['tgl_awal']) ? $rowawal['tgl_awal'] : null;
    $start_date = date("d F Y",strtotime($tgl_awal));

    $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
    $rowakhir = mysqli_fetch_array($sqlakhir);
    $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
    $end_date = date("d F Y",strtotime($tgl_akhir));

    ?>

    <center>
        <h4>TRIAL BALANCE YEAR TO DATE <br/> PERIODE <?php echo $kata_filter; ?> - <?php echo $end_date; ?></h4>
    </center>
  <!--   STATUS: <?php echo $status; ?> -->
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No coa</th>
            <th style="text-align: center;vertical-align: middle;">COA Name</th>
            <th style="text-align: center;vertical-align: middle;">Category 1</th>
            <th style="text-align: center;vertical-align: middle;">Category 2</th>
            <th style="text-align: center;vertical-align: middle;">Category 3</th>
            <th style="text-align: center;vertical-align: middle;">Category 4</th>
            <th style="text-align: center;vertical-align: middle;">Beginning Balance</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance</th>
        </tr>
        <?php 
        // koneksi database
        // include '../../conn/conn.php';
        // $nama_supp=$_GET['nama_supp'];
        // $status =$_GET['status'];
        $bulan_awal = date("m",strtotime($_GET['start_date']));
        $bulan_akhir = date("m",strtotime($_GET['end_date']));  
        $tahun_awal = date("Y",strtotime($_GET['start_date']));
        $tahun_akhir = date("Y",strtotime($_GET['end_date']));
        $kata_filter = $_GET['kata_filter'];
        // menampilkan data pegawai
  

        $sql = mysqli_query($conn2,"select * from 
(select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4 from mastercoa_v2 order by no_coa asc) coa
on coa.no_coa = saldo.nocoa
left join
(select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = coa.no_coa order by no_coa asc");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){
        $beg_balance = isset($row['saldo']) ? $row['saldo'] : 0;
        $credit_idr = isset($row['credit_idr']) ? $row['credit_idr'] : 0;
        $debit_idr = isset($row['debit_idr']) ? $row['debit_idr'] : 0;
        $saldoakhir = $beg_balance + $debit_idr - $credit_idr;
        $balance_idr = isset($row['balance_idr']) ? $row['balance_idr'] : null;

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align : center;" value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
            <td style="text-align : left;" value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
            <td style="text-align : left;" value = "'.$row['ind_categori1'].'">'.$row['ind_categori1'].'</td>
            <td style="text-align : left;" value = "'.$row['ind_categori2'].'">'.$row['ind_categori2'].'</td>
            <td style="text-align : left;" value = "'.$row['ind_categori3'].'">'.$row['ind_categori3'].'</td>
            <td style="text-align : left;" value = "'.$row['ind_categori4'].'">'.$row['ind_categori4'].'</td>
            <td style=" text-align : right;" value="'.$beg_balance.'">'.$beg_balance.'</td>
            <td style=" text-align : right;" value="'.$debit_idr.'">'.$debit_idr.'</td>
            <td style=" text-align : right;" value="'.$credit_idr.'">'.$credit_idr.'</td>
            <td style=" text-align : right;" value="'.$saldoakhir.'">'.$saldoakhir.'</td>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




