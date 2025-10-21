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
    $profit_center = $_GET['profit_center'];

    $sqlawal = mysqli_query($conn2,"select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal'");
    $rowawal = mysqli_fetch_array($sqlawal);
    $tgl_awal = isset($rowawal['tgl_awal']) ? $rowawal['tgl_awal'] : null;
    $start_date = date("d F Y",strtotime($tgl_awal));

    $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
    $rowakhir = mysqli_fetch_array($sqlakhir);
    $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
    $end_date = date("d F Y",strtotime($tgl_akhir));

    if ($profit_center == 'ALL') {
        $nama_pc = 'ALL';
    }elseif($profit_center == 'NAG'){
        $nama_pc = 'NIRWANA ALABARE GARMENT';
    }else{
        $nama_pc = 'NIRWANA ALABARE KNITTING';
    }

    ?>

        <h4>TRIAL BALANCE YEAR TO DATE <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    PROFIT CENTER: <?php echo $nama_pc; ?>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No coa</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">COA Name</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Category 1</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Category 2</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Category 3</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Category 4</th>
            <?php
            if ($profit_center == 'ALL') {
                echo '<th colspan="4">Nirwana Alabare Garment</th>';
                echo '<th colspan="4">Nirwana Alabare Knitting</th>';
                echo '<th colspan="4">Summary ALL</th>';
              }elseif ($profit_center == 'NAG') {
                echo '<th colspan="4">Nirwana Alabare Garment</th>';
              }else{
                echo '<th colspan="4">Nirwana Alabare Knitting</th>';
              }
            ?>
        </tr>
        <tr>
            <?php
            if ($profit_center == 'ALL') {
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
              }elseif ($profit_center == 'NAG') {
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
              }else{
                echo '<th>Beginning Balance</th>';
                echo '<th>Debit</th>';
                echo '<th>Credit</th>';
                echo '<th>Ending Balance</th>';
              }
            ?>
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
  

        $que = "select a.*,b.saldo_awal saldo_awal_nag, b.debit_idr debit_idr_nag, b.credit_idr credit_idr_nag, b.saldo_akhir saldo_akhir_nag, c.saldo_awal saldo_awal_nak, c.debit_idr debit_idr_nak, c.credit_idr credit_idr_nak, c.saldo_akhir saldo_akhir_nak, (b.saldo_awal + c.saldo_awal) saldo_awal_all, (b.debit_idr + c.debit_idr) debit_idr_all, (b.credit_idr + c.credit_idr) credit_idr_all, (b.saldo_akhir + c.saldo_akhir) saldo_akhir_all from 
(select no_coa, nama_coa, indname1, indname2, indname3, indname4 from mastercoa_v2 a left join (select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) b on b.id_ctg5A =a.id_ctg5 GROUP BY no_coa) a 
left join 
(select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAG' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAG' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) b on b.no_coa = a.no_coa
left join 
(select no_coa, saldo saldo_awal, debit_idr, credit_idr, (saldo + debit_idr - credit_idr) saldo_akhir from (select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr from (select no_coa ,$kata_filter as saldo from fs_saldo_awal_tb where profit_center = 'NAK' order by no_coa asc) a LEFT JOIN (select profit_center, no_coa, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') and profit_center = 'NAK' group by no_coa) e on e.no_coa = a.no_coa) a order by no_coa asc) c on c.no_coa = a.no_coa";

      $sql = mysqli_query($conn2,$que);

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
       <td style="text-align : left;" value = "'.$row['indname1'].'">'.$row['indname1'].'</td>
       <td style="text-align : left;" value = "'.$row['indname2'].'">'.$row['indname2'].'</td>
       <td style="text-align : left;" value = "'.$row['indname3'].'">'.$row['indname3'].'</td>
       <td style="text-align : left;" value = "'.$row['indname4'].'">'.$row['indname4'].'</td>';
        if ($profit_center == 'ALL') {
          echo '<td style=" text-align : right;" value="'.$row['saldo_awal_nag'].'">'.number_format($row['saldo_awal_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_nag'].'">'.number_format($row['debit_idr_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_nag'].'">'.number_format($row['credit_idr_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_nag'].'">'.number_format($row['saldo_akhir_nag'],2).'</td>

                <td style=" text-align : right;" value="'.$row['saldo_awal_nak'].'">'.number_format($row['saldo_awal_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_nak'].'">'.number_format($row['debit_idr_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_nak'].'">'.number_format($row['credit_idr_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_nak'].'">'.number_format($row['saldo_akhir_nak'],2).'</td>

                <td style=" text-align : right;" value="'.$row['saldo_awal_all'].'">'.number_format($row['saldo_awal_all'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_all'].'">'.number_format($row['debit_idr_all'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_all'].'">'.number_format($row['credit_idr_all'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_all'].'">'.number_format($row['saldo_akhir_all'],2).'</td>';
        }elseif ($profit_center == 'NAG') {
          echo '<td style=" text-align : right;" value="'.$row['saldo_awal_nag'].'">'.number_format($row['saldo_awal_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_nag'].'">'.number_format($row['debit_idr_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_nag'].'">'.number_format($row['credit_idr_nag'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_nag'].'">'.number_format($row['saldo_akhir_nag'],2).'</td>';
        }else{
          echo '<td style=" text-align : right;" value="'.$row['saldo_awal_nak'].'">'.number_format($row['saldo_awal_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['debit_idr_nak'].'">'.number_format($row['debit_idr_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['credit_idr_nak'].'">'.number_format($row['credit_idr_nak'],2).'</td>
                <td style=" text-align : right;" value="'.$row['saldo_akhir_nak'].'">'.number_format($row['saldo_akhir_nak'],2).'</td>';
        }
        echo '</tr>';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




