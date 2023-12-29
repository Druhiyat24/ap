<html>
<head>
    <title>Export Data CF Direct </title>
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
    header("Content-Disposition: attachment; filename=cf-direct-monthly.xls");
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
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PT NIRWANA ALABARE GARMENT</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LAPORAN ARUS KAS - METODE LANGSUNG</b></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>STATEMENTS OF CASH FLOW - DIRECT METHOD</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>UNTUK PERIODE YANG BERAKHIR PADA TANGGAL <?php echo $end_date; ?></b></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>FOR THE PERIODS ENDED <?php echo $end_date; ?></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</b></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
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
        <!-- Aktivitas Operasi -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Operasi</b></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Operating Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan Dari Pelanggan</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '1'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Receipt From Customer</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Kepada Pemasok</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '2'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment To Supplier</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Kepada Pemasok Lain-Lain</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '3'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment For Other Vendors</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Kepada Karyawan</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '4'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment To Employees</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan Dari Karyawan</td>
           <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '9'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Receipt From Employees</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Ke Pemerintah Bukan Pajak</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '6'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment To Government Non Tax</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Pajak</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '5'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Tax Payment</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran Bunga</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '8'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment For Interest</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan Dari Bunga Bank</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '7'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Receipt From Bank Interest</i></td>
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
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id IN ('1','2','3','4','5','6','7','8','9')");

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
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Investasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Investing Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembelian aset tetap</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '11'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Acquisition of Fixed Asset</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan dari penjualan aset tetap</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '12'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Proceeds from sale of fixed assets</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penambahan investasi pada instrumen keuangan</td>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
                $totalcf_inves1 = 0;
                $total_inves1 = number_format($totalcf_inves1,2);
            echo '<td style="text-align: right;vertical-align: middle;width: 10%;">'.$total_inves1.'</td>';

        }; ?>
            <td style="text-align: right;vertical-align: middle;width: 10%;">
                <?php 
                $totalcf_inves1 = 0;
                $total_inves1 = number_format($totalcf_inves1,2);
                echo $total_inves1;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Addition of investment in financial instrument</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penjualan investasi pada instrumen keuangan</td>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
                  $totalcf_inves2 = 0;
                $total_inves2 = number_format($totalcf_inves2,2);
            echo '<td style="text-align: right;vertical-align: middle;width: 10%;">'.$total_inves2.'</td>';

        }; ?>
            <td style="text-align: right;vertical-align: middle;width: 10%;">
                <?php 
                $totalcf_inves2 = 0;
                $total_inves2 = number_format($totalcf_inves2,2);
                echo $total_inves2;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Sale of investment in financial instrument</i></td>
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
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang digunakan untuk aktivitas investasi</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id IN ('11','12')");

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
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash  flow  used from investing activities</i></b></th>
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
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penerimaan pinjaman</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '17'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Proceeds from loans</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembayaran pinjaman</td>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id = '18'");

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
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Payment of loans</i></td>
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
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id IN ('17','18')");

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
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kenaikan / (Penurunan) bersih kas dan setara kas</b></th>
            <?php 
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id IN ('1','2','3','4','5','6','7','8','9','11','12','17','18')");

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
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
             while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
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
                $sql = mysqli_query($conn2,"select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select id,ind_name,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from ((
SELECT id,ind_name,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jnl ON jnl.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) feb ON feb.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) mar ON mar.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) apr ON apr.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) may ON may.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jun ON jun.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) jul ON jul.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) aug ON aug.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) sep ON sep.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) oct ON oct.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) nov ON nov.id_direct_credit=cf.id LEFT JOIN (
SELECT id_direct_credit,sum(credit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_cre WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_credit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_credit) des ON des.id_direct_credit=cf.id) UNION (
SELECT id,ind_name,-saldo_jan,-saldo_feb,-saldo_mar,-saldo_apr,-saldo_may,-saldo_jun,-saldo_jul,-saldo_aug,-saldo_sep,-saldo_oct,-saldo_nov,-saldo_dec FROM (
SELECT id,ind_name FROM tbl_master_cashflow) cf LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jan FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='01' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jnl ON jnl.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_feb FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='02' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) feb ON feb.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_mar FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='03' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) mar ON mar.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_apr FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='04' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) apr ON apr.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_may FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='05' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) may ON may.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jun FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='06' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jun ON jun.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_jul FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='07' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) jul ON jul.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_aug FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='08' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) aug ON aug.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_sep FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='09' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) sep ON sep.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_oct FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='10' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) oct ON oct.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_nov FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='11' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) nov ON nov.id_direct_debit=cf.id LEFT JOIN (
SELECT id_direct_debit,sum(debit_idr) saldo_dec FROM (
SELECT*FROM (
SELECT*FROM (
select * from view_cf_direct_monthly_deb WHERE tgl_journal BETWEEN (
SELECT tgl_awal FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') AND (
SELECT tgl_akhir FROM tbl_tgl_tb WHERE bulan='12' AND tahun='2023') GROUP BY id) a WHERE a.asal IN ('Bank','Cash','Petty Cash')) a INNER JOIN (
SELECT no_coa,id_direct_debit FROM mastercoa_v2) b ON b.no_coa=a.coa_no) a GROUP BY a.id_direct_debit) des ON des.id_direct_debit=cf.id ORDER BY cf.id ASC)) a where a.id IN ('1','2','3','4','5','6','7','8','9','11','12','17','18')
UNION
select id_ctg4,ind_categori4,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
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
order by no_coa asc) a where a.id_ctg4 = '111' group by a.id_ctg4 ) a");

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




