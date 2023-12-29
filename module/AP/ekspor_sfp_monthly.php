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
        margin: 15px auto;
        border-style: none;
    }
    table th,
    table td{
        padding: 3px 8px;
 
    }
    a{
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
    }
    .text{
  mso-number-format:"\@";/force text/
}
    </style>
 
    <?php
    include '../../conn/conn.php';
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=sfp-monthly.xls");
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


    $sql_periode = mysqli_query($conn2,"select DATE_FORMAT(periode, '%M %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");


    $sql_jmlper = mysqli_query($conn2,"select COUNT(periode) jml_periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");

    $rowjmlper = mysqli_fetch_array($sql_jmlper);
    $jmlper = isset($rowjmlper['jml_periode']) ? $rowjmlper['jml_periode'] : 0;

    ?>
<!-- 
    <center>
        <h4>TRIAL BALANCE YEAR TO DATE <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center> -->
  <!--   STATUS: <?php echo $status; ?> -->
 
    <table style="width:70%;font-size:15px;" >
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>PT NIRWANA ALABARE GARMENT</b></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
        <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PT NIRWANA ALABARE GARMENT</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LAPORAN POSISI KEUANGAN</b></th>
            <?php
             $sql_periode2 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode2 = mysqli_fetch_array($sql_periode2)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
        <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>STATEMENTS OF FINANCIAL POSITION</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b><?php echo $end_date; ?></b></th>
            <?php
             $sql_periode2 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode2 = mysqli_fetch_array($sql_periode2)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
        <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i><?php echo $end_date; ?></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</b></th>
            <?php
             $sql_periode2 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode2 = mysqli_fetch_array($sql_periode2)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
        <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>(Expressed in Rupiah, unless otherwise stated)</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <!-- <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"><b>YTD <?php echo $end_date; ?></b></th> -->
            <?php
             while($periode = mysqli_fetch_array($sql_periode)){
            echo '<th class = "text" style="text-align: right;vertical-align: middle;width: 10%;">'.$periode['periode'].'</th>';
        };
            ?>
            <th style="text-align: right;vertical-align: middle;width: 10%;">YTD</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- aset - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>ASET</b></th>
            <?php
             $sql_periode2 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode2 = mysqli_fetch_array($sql_periode2)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
        <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>ASSETS</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <?php
             $sql_periode2 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode2 = mysqli_fetch_array($sql_periode2)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
        <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- aset_tetap - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>ASET LANCAR</b></th>
            <?php
             $sql_periode2 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode2 = mysqli_fetch_array($sql_periode2)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
        <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>CURRENT ASSETS</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kas dan bank</td>
                <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '111' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash on hand and in banks</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Piutang usaha</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '113' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Trade receivables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Piutang lain-lain</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '114' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other receivables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Persediaan</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '115' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Inventories</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Uang muka pembelian</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '116' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Advances</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Biaya dibayar dimuka</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '117' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Prepaid expenses</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pajak dibayar dimuka</td>
                <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '118' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Prepaid taxes</i></td>
        </tr>
         <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Aset Lancar</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 IN ('111','113','114','115','116','117','118') group by a.id_ctg4 ) a");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Current Assets</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- aset_tetap - end -->
        <!-- aset_tidak_tetap - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>ASET TIDAK LANCAR </b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>NONCURRENT ASSETS</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Investasi</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '112' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Investment</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Investasi pada entitas anak</td>
            <td style="text-align: right;vertical-align: middle;width: 10%;">0.00</td>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<td style="text-align: right;vertical-align: middle;width: 10%;">0.00</td>';

        }; ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Investment in subsidiary</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset tetap</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '121' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Fixed assets</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset takberwujud</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '122' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Intangible assets</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset pajak tangguhan</td>
            <td style="text-align: right;vertical-align: middle;width: 10%;">0.00</td>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<td style="text-align: right;vertical-align: middle;width: 10%;">0.00</td>';

        }; ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Deferred tax assets</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset lain-lain</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '129' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other assets</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Aset Tidak Lancar</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 IN ('112','121','122','129') group by a.id_ctg4 ) a");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Noncurrent Assets</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>JUMLAH ASET</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 IN ('111','113','114','115','116','117','118','112','121','122','129') group by a.id_ctg4 ) a");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>TOTAL ASSETS</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- aset_tidak_tetap - end -->
        <!-- aset - end -->

        <!-- liabilitas&ekuitas - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LIABILITAS DAN EKUITAS </b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>LIABILITIES AND EQUITY</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- liabilitas - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LIABILITAS JANGKA PENDEK</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>CURRENT LIABILITIES</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang bank jangka pendek</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '212' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Short-term bank loans</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang usaha</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '211' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Trade payables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang pajak</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '215' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Taxes payables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Biaya akrual</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '214' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Accrued expenses</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang PPN</td>
            <td style="text-align: right;vertical-align: middle;width: 10%;">0.00</td>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<td style="text-align: right;vertical-align: middle;width: 10%;">0.00</td>';

        }; ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>VAT Payable</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang lain-lain</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '219' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other payables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Uang Muka Penjualan</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '213' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Deferred Revenue</i></td>
        </tr>
         <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Liabilitas Jangka Pendek</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 IN ('212','211','215','214','219','213') group by a.id_ctg4 ) a");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Current Liabilities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- liabilitas_pendek - end -->
        <!-- liabilitas_panjang - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LIABILITAS JANGKA PANJANG</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>NONCURRENT LIABILITIES</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang bank jangka panjang</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '221' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Long-term bank loans</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Liabilitas imbalan pasca kerja</td>
            <td style="text-align: right;vertical-align: middle;width: 10%;">0.00</td>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<td style="text-align: right;vertical-align: middle;width: 10%;">0.00</td>';

        }; ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Post-employment benefits obligation</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Liabilitas Jangka Panjang</b></th>
           <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 IN ('221') group by a.id_ctg4 ) a");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Noncurrent Liabilities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- liabilitas_panjang - end -->
        <!-- ekuitas - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>EKUITAS</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>EQUITY</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Modal saham</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '311' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Capital Stock</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Tambahan Modal Disetor</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '312' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Paid in Capital</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Saldo laba di tahan</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '318' group by a.id_ctg4");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Retained earnings</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pendapatan Komprehensif Lain-lain</td>
            <td style="text-align: right;vertical-align: middle;width: 10%;">0.00</td>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;">0.00</th>';

        }; ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other Comprehensive Income</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Laba Tahun Berjalan</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa  where no_coa >= '3.40.01'
order by no_coa asc) a");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Profit of the year</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Ekuitas</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 IN ('311','312','318') group by a.id_ctg4 ) a
UNION
select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa  where no_coa >= '3.40.01'
order by no_coa asc) a) a");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Equity</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>
             <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>JUMLAH LIABILITAS DAN EKUITAS</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 IN ('212','211','215','214','219','213','221') group by a.id_ctg4 ) a
UNION
select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 IN ('311','312','318') group by a.id_ctg4 ) a
UNION
select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa  where no_coa >= '3.40.01'
order by no_coa asc) a) a");

        $row = mysqli_fetch_array($sql);
        $saldo_awal = isset($row['saldo_awal']) ? $row['saldo_awal'] : 0;
        $saldo_jan = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb = isset($row['saldo_feb']) ? $row['saldo_feb'] : 0;
        $saldo_mar = isset($row['saldo_mar']) ? $row['saldo_mar'] : 0;
        $saldo_apr = isset($row['saldo_apr']) ? $row['saldo_apr'] : 0;
        $saldo_may = isset($row['saldo_may']) ? $row['saldo_may'] : 0;
        $saldo_jun = isset($row['saldo_jun']) ? $row['saldo_jun'] : 0;
        $saldo_jul = isset($row['saldo_jul']) ? $row['saldo_jul'] : 0;
        $saldo_aug = isset($row['saldo_aug']) ? $row['saldo_aug'] : 0;
        $saldo_sep = isset($row['saldo_sep']) ? $row['saldo_sep'] : 0;
        $saldo_oct = isset($row['saldo_oct']) ? $row['saldo_oct'] : 0;
        $saldo_nov = isset($row['saldo_nov']) ? $row['saldo_nov'] : 0;
        $saldo_dec = isset($row['saldo_dec']) ? $row['saldo_dec'] : 0;

        $saldo_jan_ = isset($row['saldo_jan']) ? $row['saldo_jan'] : 0;
        $saldo_feb_ = isset($row['saldo_feb']) ? $row['saldo_feb'] : $saldo_jan;
        $saldo_mar_ = isset($row['saldo_mar']) ? $row['saldo_mar'] : $saldo_feb;
        $saldo_apr_ = isset($row['saldo_apr']) ? $row['saldo_apr'] : $saldo_mar;
        $saldo_may_ = isset($row['saldo_may']) ? $row['saldo_may'] : $saldo_apr;
        $saldo_jun_ = isset($row['saldo_jun']) ? $row['saldo_jun'] : $saldo_may;
        $saldo_jul_ = isset($row['saldo_jul']) ? $row['saldo_jul'] : $saldo_jun;
        $saldo_aug_ = isset($row['saldo_aug']) ? $row['saldo_aug'] : $saldo_jul;
        $saldo_sep_ = isset($row['saldo_sep']) ? $row['saldo_sep'] : $saldo_aug;
        $saldo_oct_ = isset($row['saldo_oct']) ? $row['saldo_oct'] : $saldo_sep;
        $saldo_nov_ = isset($row['saldo_nov']) ? $row['saldo_nov'] : $saldo_oct;
        $saldo_dec_ = isset($row['saldo_dec']) ? $row['saldo_dec'] : $saldo_nov;
        
        if ($jmlper == '1') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                ';
            }elseif ($jmlper == '2') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb_.'">'.number_format($saldo_feb_,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar_.'">'.number_format($saldo_mar_,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr_.'">'.number_format($saldo_apr_,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may_.'">'.number_format($saldo_may_,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun_.'">'.number_format($saldo_jun_,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul_.'">'.number_format($saldo_jul_,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug_.'">'.number_format($saldo_aug_,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep_.'">'.number_format($saldo_sep_,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_oct_.'">'.number_format($saldo_oct_,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_nov_.'">'.number_format($saldo_nov_,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_sep.'">'.number_format($saldo_sep,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_oct.'">'.number_format($saldo_oct,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_nov.'">'.number_format($saldo_nov,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_dec.'">'.number_format($saldo_dec,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px double; black;" value="'.$saldo_dec_.'">'.number_format($saldo_dec_,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>TOTAL LIABILITIES AND EQUITY</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- liabilitas&ekuitas - end -->
        
    </table>

</body>
</html>




