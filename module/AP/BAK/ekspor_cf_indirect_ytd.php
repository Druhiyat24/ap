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
    </style>
 
    <?php
    include '../../conn/conn.php';
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=cf-indirect-ytd.xls");
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
<!-- 
    <center>
        <h4>TRIAL BALANCE YEAR TO DATE <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center> -->
  <!--   STATUS: <?php echo $status; ?> -->
 
    <table style="width:70%;font-size:15px;" >
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>PT NIRWANA ALABARE GARMENT</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PT NIRWANA ALABARE GARMENT</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LAPORAN ARUS KAS - METODE TIDAK LANGSUNG</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>STATEMENTS OF CASH FLOW - INDIRECT METHOD</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>UNTUK PERIODE YANG BERAKHIR PADA TANGGAL <?php echo $end_date; ?></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>FOR THE PERIODS ENDED <?php echo $end_date; ?></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>(Expressed in Rupiah, unless otherwise stated)</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"><b><?php echo $end_date; ?>.</b></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Laba (Rugi) Bersih</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php

                $sql7 = mysqli_query($conn2,"select indname1,sum(credit_idr - debit_idr) total from (select * from 
(select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
on coa.no_coa = saldo.nocoa
left join
(select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
LEFT JOIN
(select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a where a.indname1 = 'LAPORAN LABA RUGI'");

                $row7 = mysqli_fetch_array($sql7);
                $total7 = isset($row7['total']) ? $row7['total'] : 0;
                $tot_jml9 = ($total7);
                $total_ = number_format(($total7),2);
                echo $total_; 
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Net Income (Loss)</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penyesuaian Akumulasi Penyusutan Aset Tetap</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql1 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '19'");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;
                $totalcf_1 = number_format($total1,2);
                echo $totalcf_1;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Accumulated Depreciation Of Fixed Asset Adjustment</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penyesuaian Laba Ditahan Tahun Lalu</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
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
            <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i></i></td>
        </tr>
        <!-- Aktivitas Operasi -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Operasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Operating Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Piutang Dagang</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql20 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '20'");

                $row20 = mysqli_fetch_array($sql20);
                $total20 = isset($row20['total']) ? $row20['total'] : 0;
                $totalcf_20 = number_format($total20,2);
                echo $totalcf_20;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Piutang Dagang</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Piutang Lainnya</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql21 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '21'");

                $row21 = mysqli_fetch_array($sql21);
                $total21 = isset($row21['total']) ? $row21['total'] : 0;
                $totalcf_21 = number_format($total21,2);
                echo $totalcf_21;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Piutang Lainnya</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Persediaan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql22 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '22'");

                $row22 = mysqli_fetch_array($sql22);
                $total22 = isset($row22['total']) ? $row22['total'] : 0;
                $totalcf_22 = number_format($total22,2);
                echo $totalcf_22;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Persediaan</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Biaya Dibayar Dimuka</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql23 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '23'");

                $row23 = mysqli_fetch_array($sql23);
                $total23 = isset($row23['total']) ? $row23['total'] : 0;
                $totalcf_23 = number_format($total23,2);
                echo $totalcf_23;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Biaya Dibayar Dimuka</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penurunan (Kenaikan) Aset Lain-Lain</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql24 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '24'");

                $row24 = mysqli_fetch_array($sql24);
                $total24 = isset($row24['total']) ? $row24['total'] : 0;
                $totalcf_24 = number_format($total24,2);
                echo $totalcf_24;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Penurunan (Kenaikan) Aset Lain-Lain</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan (Penurunan) Utang Dagang</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql25 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '25'");

                $row25 = mysqli_fetch_array($sql25);
                $total25 = isset($row25['total']) ? $row25['total'] : 0;
                $totalcf_25 = number_format($total25,2);
                echo $totalcf_25;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Kenaikan (Penurunan) Utang Dagang</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan (Penurunan) Utang Lainnya</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql26 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '26'");

                $row26 = mysqli_fetch_array($sql26);
                $total26 = isset($row26['total']) ? $row26['total'] : 0;
                $totalcf_26 = number_format($total26,2);
                echo $totalcf_26;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Kenaikan (Penurunan) Utang Lainnya</i></td>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang digunakan untuk aktivitas operasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sqljml4 = mysqli_query($conn2,"select id_indirect,ind_name, sum(total) total from (select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect IN ('20','21','22','23','24','25','26')) a ");

                $rowjml4 = mysqli_fetch_array($sqljml4);
                $totaljml4 = isset($rowjml4['total']) ? $rowjml4['total'] : 0;
                $totalcf_jml4 = number_format($totaljml4,2);
                echo $totalcf_jml4;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash flow used from operating activities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- -->

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Investasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Investing Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pembelian aset tetap</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $sql27 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '27'");

                $row27 = mysqli_fetch_array($sql27);
                $total27 = isset($row27['total']) ? $row27['total'] : 0;
                $totalcf_27 = number_format($total27,2);
                echo $totalcf_27; 
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Acquisition of Fixed Asset</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset Dalam Penyelesaian</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql28 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '28'");

                $row28 = mysqli_fetch_array($sql28);
                $total28 = isset($row28['total']) ? $row28['total'] : 0;
                $totalcf_28 = number_format($total28,2);
                echo $totalcf_28;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Asset In Construction</i></td>
        </tr>
        
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang diperoleh dari aktivitas investasi</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $sqljml5 = mysqli_query($conn2,"select id_indirect,ind_name, sum(total) total from (select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect IN ('27','28')) a ");

                $rowjml5 = mysqli_fetch_array($sqljml5);
                $totaljml5 = isset($rowjml5['total']) ? $rowjml5['total'] : 0;
                $totalcf_jml5 = number_format($totaljml5,2);
                echo $totalcf_jml5; 
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash  flow  generated from investing activities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <!-- -->

         <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus Kas dari Aktivitas Pendanaan</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash Flow from Financing Activities</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan (Penurunan) Utang Bank</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $sql29 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '29'");

                $row29 = mysqli_fetch_array($sql29);
                $total29 = isset($row29['total']) ? $row29['total'] : 0;
                $totalcf_29 = number_format($total29,2);
                echo $totalcf_29; 
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Increase / (Decrease) Bank Loan</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kenaikan (Penurunan) Utang Direksi & Afiliasi</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql30 = mysqli_query($conn2,"select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '30'");

                $row30 = mysqli_fetch_array($sql30);
                $total30 = isset($row30['total']) ? $row30['total'] : 0;
                $totalcf_30 = number_format($total30,2);
                echo $totalcf_30;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Increase / (Decrease) Shareholder & Affiliated Payable</i></td>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Arus kas yang diperoleh dari aktivitas pendanaan</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sqljml6 = mysqli_query($conn2,"select id_indirect,ind_name, sum(total) total from (select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join 
                    (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join 
                    (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect IN ('29','30')) a ");

                $rowjml6 = mysqli_fetch_array($sqljml6);
                $totaljml6 = isset($rowjml6['total']) ? $rowjml6['total'] : 0;
                $totalcf_jml6 = number_format($totaljml6,2);
                echo $totalcf_jml6;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash  flow  generated from financing activities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kenaikan / (Penurunan) bersih kas dan setara kas</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $totalcf_indirect = $tot_jml9 + $total1 + $totalcf_laba1 + $totaljml4 + $totaljml5 + $totaljml6;
                $total_jmlindir = number_format($totalcf_indirect,2);
                echo $total_jmlindir; 
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Net Increase / (Decrease) in cash and cash equivalent</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kas dan setara kas pada awal periode</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,saldo total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02' UNION select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 OR no_coa = '1.10.02' and $kata_filter > 0) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111'");

                $row = mysqli_fetch_array($sql);
                $totalind = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($totalind,2);
                echo $total_; 
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash and cash equivalent at the beginning of period</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Kas dan setara kas pada akhir periode</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php
                $totalcf_kas = $totalind + $totalcf_indirect;
                $total_jmlkas = number_format($totalcf_kas,2);
                echo $total_jmlkas; 
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Cash and cash equivalent at the end of period</i></b></th>
        </tr>
        
    </table>

</body>
</html>




