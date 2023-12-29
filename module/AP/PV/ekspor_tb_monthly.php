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
    header("Content-Disposition: attachment; filename=trial-balance-monthly.xls");
    // $nama_supp =$_GET['nama_supp'];
    // $status =$_GET['status'];
    $bulan_awal = date("m",strtotime($_GET['start_date']));
    $bulan_akhir = date("m",strtotime($_GET['end_date']));  
    $tahun_awal = date("Y",strtotime($_GET['start_date']));
    $tahun_akhir = date("Y",strtotime($_GET['end_date'])); 

    $sqlawal = mysqli_query($conn2,"select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal'");
    $rowawal = mysqli_fetch_array($sqlawal);
    $tgl_awal = isset($rowawal['tgl_awal']) ? $rowawal['tgl_awal'] : null;
    $start_date = date("d F Y",strtotime($tgl_awal));

    $sqlakhir = mysqli_query($conn2,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir'");
    $rowakhir = mysqli_fetch_array($sqlakhir);
    $tgl_akhir = isset($rowakhir['tgl_akhir']) ? $rowakhir['tgl_akhir'] : null;
    $end_date = date("d F Y",strtotime($tgl_akhir));


    $sql_periode = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");

    ?>

    <center>
        <h4>TRIAL BALANCE MONTHLY <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
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
            <th style="text-align: center;vertical-align: middle;">Des 2022</th>
            <?php
             while($periode = mysqli_fetch_array($sql_periode)){
            echo '<th style="text-align: center;vertical-align: middle;">'.$periode['periode'].'</th>';
        };
            ?>
            <th style="text-align: center;vertical-align: middle;">YTD</th>
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
        // menampilkan data pegawai
  

       $sql = mysqli_query($conn2,"select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc");


    $sql_jmlper = mysqli_query($conn2,"select COUNT(periode) jml_periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");

    $rowjmlper = mysqli_fetch_array($sql_jmlper);
    $jmlper = isset($rowjmlper['jml_periode']) ? $rowjmlper['jml_periode'] : 0;

        $saldoakhir = 0;

        $no = 1;

        while($row = mysqli_fetch_array($sql)){
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align : center;" value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
            <td style="text-align : left;" value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
            <td style="text-align : left;" value = "'.$row['ind_categori1'].'">'.$row['ind_categori1'].'</td>
            <td style="text-align : left;" value = "'.$row['ind_categori2'].'">'.$row['ind_categori2'].'</td>
            <td style="text-align : left;" value = "'.$row['ind_categori3'].'">'.$row['ind_categori3'].'</td>
            <td style="text-align : left;" value = "'.$row['ind_categori4'].'">'.$row['ind_categori4'].'</td>
            <td style=" text-align : right;" value="'.$saldo_awal.'">'.number_format($saldo_awal,2).'</td>
            ';
            if ($jmlper == '1') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style=" text-align : right;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style=" text-align : right;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                ';
            }else{

            }
            echo '</tr>';
        
    }
        ?>
    </table>

</body>
</html>




