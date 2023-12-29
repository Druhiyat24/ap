<html>
<head>
    <title>Export Data CF InDirect </title>
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
    header("Content-Disposition: attachment; filename=cf-indirect-monthly.xls");
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
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PT NIRWANA ALABARE GARMENT</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LAPORAN ARUS KAS - METODE TIDAK LANGSUNG</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>STATEMENTS OF CASH FLOW - INDIRECT METHOD</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>UNTUK PERIODE YANG BERAKHIR PADA TANGGAL <?php echo $end_date; ?></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>FOR THE PERIODS ENDED <?php echo $end_date; ?></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>(Expressed in Rupiah, unless otherwise stated)</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <?php
             while($periode = mysqli_fetch_array($sql_periode)){
            echo '<th class = "text" style="text-align: right;vertical-align: middle;width: 10%;">'.$periode['periode'].'</th>';
        };
            ?>
            <th style="text-align: right;vertical-align: middle;width: 10%;"><b>YTD</b></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Laba (Rugi) Bersih</td>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,(saldo_awal_ - saldo_awal) saldo_awal,(saldo_jan_ - saldo_jan) saldo_jan,(saldo_feb_ - saldo_feb) saldo_feb,(saldo_mar_ - saldo_mar) saldo_mar,(saldo_apr_ - saldo_apr) saldo_apr,(saldo_may_ - saldo_may) saldo_may,(saldo_jun_ - saldo_jun) saldo_jun,(saldo_jul_ - saldo_jul) saldo_jul,(saldo_aug_ - saldo_aug) saldo_aug,(saldo_sep_ - saldo_sep) saldo_sep,(saldo_oct_ - saldo_oct) saldo_oct,(saldo_nov_ - saldo_nov) saldo_nov,(saldo_dec_ - saldo_dec) saldo_dec from (select '1' id,ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal_,sum(saldo_jan) saldo_jan_,sum(saldo_feb) saldo_feb_,sum(saldo_mar) saldo_mar_,sum(saldo_apr) saldo_apr_,sum(saldo_may) saldo_may_,sum(saldo_jun) saldo_jun_,sum(saldo_jul) saldo_jul_,sum(saldo_aug) saldo_aug_,sum(saldo_sep) saldo_sep_,sum(saldo_oct) saldo_oct_,sum(saldo_nov) saldo_nov_,sum(saldo_dec) saldo_dec_ from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
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
order by no_coa asc) a where a.id_ctg4 IN ('411','412','418','413','414','419','421','422','429','511','512','518','513','514','519','591','611','711','821','813','815','822','911','921') group by a.id_ctg4 ) a
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
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa  where no_coa IN ('8.52.01','8.52.02','8.53.01','8.54.01')
order by no_coa asc) a) a) a left JOIN

(select '1' id,ind_categori4 ind4,id_ctg4 idctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan * -1) saldo_jan,sum(saldo_feb * -1) saldo_feb,sum(saldo_mar * -1) saldo_mar,sum(saldo_apr * -1) saldo_apr,sum(saldo_may * -1) saldo_may,sum(saldo_jun * -1) saldo_jun,sum(saldo_jul * -1) saldo_jul,sum(saldo_aug * -1) saldo_aug,sum(saldo_sep * -1) saldo_sep,sum(saldo_oct * -1) saldo_oct,sum(saldo_nov * -1) saldo_nov,sum(saldo_dec * -1) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
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
order by no_coa asc) a where a.id_ctg4 IN ('431','432','438','433','434','439','814') group by a.id_ctg4 ) a) b on b.id = a.id");

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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                ';
            }elseif ($jmlper == '2') {
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format(($saldo_apr * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format(($saldo_apr * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format(($saldo_may * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format(($saldo_apr * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format(($saldo_may * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format(($saldo_jun * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format(($saldo_apr * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format(($saldo_may * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format(($saldo_jun * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format(($saldo_jul * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format(($saldo_apr * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format(($saldo_may * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format(($saldo_jun * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format(($saldo_jul * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format(($saldo_aug * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format(($saldo_apr * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format(($saldo_may * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format(($saldo_jun * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format(($saldo_jul * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format(($saldo_aug * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format(($saldo_sep * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format(($saldo_apr * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format(($saldo_may * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format(($saldo_jun * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format(($saldo_jul * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format(($saldo_aug * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format(($saldo_sep * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format(($saldo_oct * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format(($saldo_apr * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format(($saldo_may * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format(($saldo_jun * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format(($saldo_jul * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format(($saldo_aug * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format(($saldo_sep * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format(($saldo_oct * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format(($saldo_nov * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format(($saldo_jan * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format(($saldo_feb * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format(($saldo_mar * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format(($saldo_apr * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format(($saldo_may * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format(($saldo_jun * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format(($saldo_jul * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format(($saldo_aug * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_sep.'">'.number_format(($saldo_sep * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_oct.'">'.number_format(($saldo_oct * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_nov.'">'.number_format(($saldo_nov * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_dec.'">'.number_format(($saldo_dec * -1),2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format(($jumlah_ytd * -1),2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Net Income (Loss)</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penyesuaian Akumulasi Penyusutan Aset Tetap</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '19' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Accumulated Depreciation Of Fixed Asset Adjustment</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penyesuaian Laba Ditahan Tahun Lalu</td>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
                $totalcf_laba1 = 0;
                $total_laba1 = number_format($totalcf_laba1,2);
            echo '<td style="text-align: right;vertical-align: middle;width: 10%;">'.$total_laba1.'</td>';

        }; ?>
            <td style="text-align: right;vertical-align: middle;width: 10%;">
                <?php 
                $totalcf_laba1 = 0;
                $total_laba1 = number_format($totalcf_laba1,2);
                echo $total_laba1;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Previous Year Retained Earning Adjustment</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;"></td>
            <td style="text-align: right;vertical-align: middle;width: 10%;"></td>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<td style="text-align: right;vertical-align: middle;width: 10%;"></td>';

        }; ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i></i></td>
        </tr>
        <!-- Aktivitas Operasi -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Operasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Operating Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Piutang Dagang</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '20' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Piutang Dagang</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Piutang Lainnya</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '21' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Piutang Lainnya</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Persediaan</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '22' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Persediaan</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Biaya Dibayar Dimuka</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '23' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Biaya Dibayar Dimuka</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Aset Lain-Lain</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '24' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Aset Lain-Lain</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan (Penurunan) Utang Dagang</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '25' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Kenaikan (Penurunan) Utang Dagang</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan (Penurunan) Utang Lainnya</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '26' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Kenaikan (Penurunan) Utang Lainnya</i></td>
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
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang digunakan untuk aktivitas operasi</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id IN ('20','21','22','23','24','25','26') ORDER BY a.id asc");

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
                $jumlah_yth = $saldo_jan + $saldo_feb;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash flow used from operating activities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Investasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<td style="text-align: right;vertical-align: middle;width: 10%;"></td>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Investing Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembelian aset tetap</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '27' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Acquisition of Fixed Asset</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset Dalam Penyelesaian</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '28' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Asset In Construction</i></td>
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
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang diperoleh dari aktivitas investasi</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id IN ('27','28') ORDER BY a.id asc");

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
                $jumlah_yth = $saldo_jan + $saldo_feb;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash  flow  generated from investing activities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <!-- -->

         <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Pendanaan</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Financing Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan (Penurunan) Utang Bank</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '29' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Increase / (Decrease) Bank Loan</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan (Penurunan) Utang Direksi & Afiliasi</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id = '30' ORDER BY a.id asc");

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
                $jumlah_ytd = $saldo_jan + $saldo_feb;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</td>
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_ytd = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_ytd.'">'.number_format($jumlah_ytd,2).'</td>
                ';
            }else{

            }

        ?>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Increase / (Decrease) Shareholder & Affiliated Payable</i></td>
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
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang diperoleh dari aktivitas pendanaan</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id IN ('29','30') ORDER BY a.id asc");

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
                $jumlah_yth = $saldo_jan + $saldo_feb;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash  flow  generated from financing activities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kenaikan / (Penurunan) bersih kas dan setara kas</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((select id_ctg4,ind_categori4,-(saldo_jan_ - saldo_jan) saldo_jan,-(saldo_feb_ - saldo_feb) saldo_feb,-(saldo_mar_ - saldo_mar) saldo_mar,-(saldo_apr_ - saldo_apr) saldo_apr,-(saldo_may_ - saldo_may) saldo_may,-(saldo_jun_ - saldo_jun) saldo_jun,-(saldo_jul_ - saldo_jul) saldo_jul,-(saldo_aug_ - saldo_aug) saldo_aug,-(saldo_sep_ - saldo_sep) saldo_sep,-(saldo_oct_ - saldo_oct) saldo_oct,-(saldo_nov_ - saldo_nov) saldo_nov,-(saldo_dec_ - saldo_dec) saldo_dec from (select '1' id,ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal_,sum(saldo_jan) saldo_jan_,sum(saldo_feb) saldo_feb_,sum(saldo_mar) saldo_mar_,sum(saldo_apr) saldo_apr_,sum(saldo_may) saldo_may_,sum(saldo_jun) saldo_jun_,sum(saldo_jul) saldo_jul_,sum(saldo_aug) saldo_aug_,sum(saldo_sep) saldo_sep_,sum(saldo_oct) saldo_oct_,sum(saldo_nov) saldo_nov_,sum(saldo_dec) saldo_dec_ from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
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
order by no_coa asc) a where a.id_ctg4 IN ('411','412','418','413','414','419','421','422','429','511','512','518','513','514','519','591','611','711','821','813','814','815','822','911','921') group by a.id_ctg4 ) a
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
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa  where no_coa IN ('8.52.01','8.52.02','8.53.01','8.54.01')
order by no_coa asc) a) a) a left JOIN

(select '1' id,ind_categori4 ind4,id_ctg4 idctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan * -1) saldo_jan,sum(saldo_feb * -1) saldo_feb,sum(saldo_mar * -1) saldo_mar,sum(saldo_apr * -1) saldo_apr,sum(saldo_may * -1) saldo_may,sum(saldo_jun * -1) saldo_jun,sum(saldo_jul * -1) saldo_jul,sum(saldo_aug * -1) saldo_aug,sum(saldo_sep * -1) saldo_sep,sum(saldo_oct * -1) saldo_oct,sum(saldo_nov * -1) saldo_nov,sum(saldo_dec * -1) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
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
order by no_coa asc) a where a.id_ctg4 IN ('431','432','438','433','434','439') group by a.id_ctg4 ) a) b on b.id = a.id)
UNION
(select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id IN ('19','20','21','22','23','24','25','26','27','28','29','30') ORDER BY a.id asc)) a");

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
                $jumlah_yth = $saldo_jan + $saldo_feb;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '3') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '4') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '5') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may; 
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '6') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '7') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '8') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug;
                echo '
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jan.'">'.number_format($saldo_jan,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_feb.'">'.number_format($saldo_feb,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_mar.'">'.number_format($saldo_mar,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_apr.'">'.number_format($saldo_apr,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_may.'">'.number_format($saldo_may,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jun.'">'.number_format($saldo_jun,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_jul.'">'.number_format($saldo_jul,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$saldo_aug.'">'.number_format($saldo_aug,2).'</th>
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '9') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '10') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '11') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }elseif ($jmlper == '12') {
                $jumlah_yth = $saldo_jan + $saldo_feb + $saldo_mar + $saldo_apr + $saldo_may + $saldo_jun + $saldo_jul + $saldo_aug + $saldo_sep + $saldo_oct + $saldo_nov + $saldo_dec;
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
                <th style="text-align: right;vertical-align: middle;width: 10%;" value="'.$jumlah_yth.'">'.number_format($jumlah_yth,2).'</th>
                ';
            }else{

            }

        ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Net Increase / (Decrease) in cash and cash equivalent</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kas dan setara kas pada awal periode</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select id_ctg4,ind_categori4,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo ) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
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
        $saldo_feb_ = $saldo_jan_;
        $saldo_mar_ = $saldo_jan_;
        $saldo_apr_ = $saldo_jan_;
        $saldo_may_ = $saldo_jan_;
        $saldo_jun_ = $saldo_jan_;
        $saldo_jul_ = $saldo_jan_;
        $saldo_aug_ = $saldo_jan_;
        $saldo_sep_ = $saldo_jan_;
        $saldo_oct_ = $saldo_jan_;
        $saldo_nov_ = $saldo_jan_;
        $saldo_dec_ = $saldo_jan_;
        
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
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash and cash equivalent at the beginning of period</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kas dan setara kas pada akhir periode</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,id_ctg4,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((select id_ctg4,ind_categori4,-(saldo_jan_ - saldo_jan) saldo_jan,-(saldo_feb_ - saldo_feb) saldo_feb,-(saldo_mar_ - saldo_mar) saldo_mar,-(saldo_apr_ - saldo_apr) saldo_apr,-(saldo_may_ - saldo_may) saldo_may,-(saldo_jun_ - saldo_jun) saldo_jun,-(saldo_jul_ - saldo_jul) saldo_jul,-(saldo_aug_ - saldo_aug) saldo_aug,-(saldo_sep_ - saldo_sep) saldo_sep,-(saldo_oct_ - saldo_oct) saldo_oct,-(saldo_nov_ - saldo_nov) saldo_nov,-(saldo_dec_ - saldo_dec) saldo_dec from (select '1' id,ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal_,sum(saldo_jan) saldo_jan_,sum(saldo_feb) saldo_feb_,sum(saldo_mar) saldo_mar_,sum(saldo_apr) saldo_apr_,sum(saldo_may) saldo_may_,sum(saldo_jun) saldo_jun_,sum(saldo_jul) saldo_jul_,sum(saldo_aug) saldo_aug_,sum(saldo_sep) saldo_sep_,sum(saldo_oct) saldo_oct_,sum(saldo_nov) saldo_nov_,sum(saldo_dec) saldo_dec_ from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
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
order by no_coa asc) a where a.id_ctg4 IN ('411','412','418','413','414','419','421','422','429','511','512','518','513','514','519','591','611','711','821','813','815','822','911','921') group by a.id_ctg4 ) a
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
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa  where no_coa IN ('8.52.01','8.52.02','8.53.01','8.54.01')
order by no_coa asc) a) a) a left JOIN

(select '1' id,ind_categori4 ind4,id_ctg4 idctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan * -1) saldo_jan,sum(saldo_feb * -1) saldo_feb,sum(saldo_mar * -1) saldo_mar,sum(saldo_apr * -1) saldo_apr,sum(saldo_may * -1) saldo_may,sum(saldo_jun * -1) saldo_jun,sum(saldo_jul * -1) saldo_jul,sum(saldo_aug * -1) saldo_aug,sum(saldo_sep * -1) saldo_sep,sum(saldo_oct * -1) saldo_oct,sum(saldo_nov * -1) saldo_nov,sum(saldo_dec * -1) saldo_dec from (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
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
order by no_coa asc) a where a.id_ctg4 IN ('431','432','438','433','434','439','814') group by a.id_ctg4 ) a) b on b.id = a.id)
UNION
(select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec FROM (
SELECT*FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT*FROM (
SELECT id_indirect,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jan FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jan ON jan.id_indirect=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect2,((sum(debit_idr)-sum(credit_idr))*-1) saldo_feb FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) feb ON feb.id_indirect2=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect3,((sum(debit_idr)-sum(credit_idr))*-1) saldo_mar FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) mar ON mar.id_indirect3=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect4,((sum(debit_idr)-sum(credit_idr))*-1) saldo_apr FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) apr ON apr.id_indirect4=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect5,((sum(debit_idr)-sum(credit_idr))*-1) saldo_may FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) may ON may.id_indirect5=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect6,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jun FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jun ON jun.id_indirect6=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect7,((sum(debit_idr)-sum(credit_idr))*-1) saldo_jul FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) jul ON jul.id_indirect7=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect8,((sum(debit_idr)-sum(credit_idr))*-1) saldo_aug FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) aug ON aug.id_indirect8=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect9,((sum(debit_idr)-sum(credit_idr))*-1) saldo_sep FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) sep ON sep.id_indirect9=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect10,((sum(debit_idr)-sum(credit_idr))*-1) saldo_oct FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) oct ON oct.id_indirect10=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect11,((sum(debit_idr)-sum(credit_idr))*-1) saldo_nov FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) nov ON nov.id_indirect11=cf.id LEFT JOIN (
SELECT*FROM (
SELECT id_indirect id_indirect12,((sum(debit_idr)-sum(credit_idr))*-1) saldo_dec FROM (
SELECT*FROM (
SELECT no_coa coa_no,sum(ROUND(debit*rate,2)) debit_idr,sum(ROUND(credit*rate,2)) credit_idr FROM tbl_list_journal WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a INNER JOIN (
SELECT no_coa,id_indirect FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_indirect) a) des ON des.id_indirect12=cf.id) a where a.id IN ('19','20','21','22','23','24','25','26','27','28','29','30') ORDER BY a.id asc)
union
(select id_ctg4,ind_categori4,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
left join
(select nocoa coa_no, saldo saldo_awal,(saldo ) saldo_jan from 
(select no_coa nocoa,jan_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_feb from 
(select no_coa nocoa,feb_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_mar from 
(select no_coa nocoa,mar_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_apr from 
(select no_coa nocoa,apr_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_may from 
(select no_coa nocoa,may_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_jun from 
(select no_coa nocoa,jun_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_jul from 
(select no_coa nocoa,jul_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_aug from 
(select no_coa nocoa,aug_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_sep from 
(select no_coa nocoa,sep_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_oct from 
(select no_coa nocoa,oct_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_nov from 
(select no_coa nocoa,nov_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

(select nocoa coa_no, (saldo ) saldo_dec from 
(select no_coa nocoa,dec_2023 as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02'order by no_coa asc) saldo
left join
(select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '2023') group by no_coa) 
jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
order by no_coa asc) a where a.id_ctg4 = '111' group by a.id_ctg4)) a");

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
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash and cash equivalent at the end of period</i></b></th>
        </tr>
        
    </table>

</body>
</html>




