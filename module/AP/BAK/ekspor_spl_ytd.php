<html>
<head>
    <title>Export Data SPL YTD</title>
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
    header("Content-Disposition: attachment; filename=spl-ytd.xls");
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
 
    <table style="width:75%;font-size:14px;" >
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>PT NIRWANA ALABARE GARMENT</b></th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PT NIRWANA ALABARE GARMENT</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LAPORAN LABA ATAU RUGI  DAN PENGHASILAN KOMPREHENSIF LAINNYA</b></th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>STATEMENTS OF PROFIT OR LOSS AND OTHER COMPREHENSIVE INCOME</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>UNTUK TAHUN YANG BERAKHIR PADA TANGGAL <?php echo $end_date; ?></b></th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>FOR THE YEARS ENDED <?php echo $end_date; ?></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</b></th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>(Expressed in Rupiah, unless otherwise stated)</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: center;vertical-align: middle;width: 14%;"><b>YTD <?php echo $end_date; ?></b></th>
            <th style="text-align: center;vertical-align: middle;width: 7%;">Persentage</th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- penjualan-kotor - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>PENJUALAN KOTOR</b></th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>GROSS SALES</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penjualan Pakaian Jadi Ekspor</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('411','412','418','413','414','419')) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;

                $sql2 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('421','422','429')) a");

                $row2 = mysqli_fetch_array($sql2);
                $total2 = isset($row2['total']) ? $row2['total'] : 0;

                $sql3 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('431','432','438','433','434','439')) a");

                $row3 = mysqli_fetch_array($sql3);
                $total3 = isset($row3['total']) ? $row3['total'] : 0;

                $jum_total1 = $total1 + $total2 - $total3;

                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '411'");

                $row = mysqli_fetch_array($sql);
                $total411 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($total411),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot411 = $total411/$jum_total1 * 100;
            echo number_format($percen_tot411,2);  echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Garment Export Sales</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penjualan Pakaian Jadi Lokal</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '412'");

                $row = mysqli_fetch_array($sql);
                $total412 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($total412),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot412 = $total412/$jum_total1 * 100;
            echo number_format($percen_tot412,2);  echo ' %';  
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Garment Local Sales</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penjualan Lainnya</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '418'");

                $row = mysqli_fetch_array($sql);
                $total418 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($total418),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot418 = $total418/$jum_total1 * 100;
            echo number_format(abs($percen_tot418),2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other Sales</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penjualan Jasa Jahit Pakaian Jadi Ekspor</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '413'");

                $row = mysqli_fetch_array($sql);
                $total413 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($total413),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot413 = $total413/$jum_total1 * 100;
            echo number_format($percen_tot413,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Export CMT Service Revenue</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penjualan Jasa Jahit Pakaian Jadi Lokal</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '414'");

                $row = mysqli_fetch_array($sql);
                $total414 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($total414),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot414 = $total414/$jum_total1 * 100;
            echo number_format($percen_tot414,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Local CMT Service Revenue</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Penjualan Jasa Lainnya</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;border-bottom: 1px solid black;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '419'");

                $row = mysqli_fetch_array($sql);
                $total419 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($total419),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;border-bottom: 1px solid black;">
                <?php 
            $percen_tot419 = $total419/$jum_total1 * 100;
            echo number_format($percen_tot419,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other Service Revenue</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>TOTAL PENJUALAN KOTOR</b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('411','412','418','413','414','419')) a");

                $row = mysqli_fetch_array($sql);
                $totalgr1 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($totalgr1),2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_totgr1 = $totalgr1/$jum_total1 * 100;
            echo number_format($percen_totgr1,2); echo ' %'; 
            ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>GROSS SALES TOTAL</i></b></th>
        </tr>
        
        <!-- penjualan-kotor - end -->

        <!-- return-penjualan - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>RETURN PENJUALAN</b></th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>SALES RETUN</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Retur Penjualan Pakaian Jadi Ekspor</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '421'");

                $row = mysqli_fetch_array($sql);
                $total421 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total421,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot421 = $total421/$jum_total1 * 100;
            echo number_format($percen_tot421,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Garment Export Sales Return</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Retur Penjualan Pakaian Jadi Lokal</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '422'");

                $row = mysqli_fetch_array($sql);
                $total422 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total422,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot422 = $total422/$jum_total1 * 100;
            echo number_format($percen_tot422,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Garment Export Local Return</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Retur Penjualan Lainnya</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;border-bottom: 1px solid black;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '429'");

                $row = mysqli_fetch_array($sql);
                $total429 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total429,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;border-bottom: 1px solid black;">
                <?php 
            $percen_tot429 = $total429/$jum_total1 * 100;
            echo number_format($percen_tot429,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other Sales Return</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>TOTAL RETURN PENJUALAN</b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('421','422','429')) a");

                $row = mysqli_fetch_array($sql);
                $totalgr2 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($totalgr2,2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_totgr2 = $totalgr2/$jum_total1 * 100;
            echo number_format($percen_totgr2,2); echo ' %'; 
            ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>SALES RETURN TOTAL</i></b></th>
        </tr>
        
        <!-- return-penjualan - end -->

        <!-- potongan-penjualan - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>POTONGAN PENJUALAN</b></th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>SALES DISCOUNT</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Potongan Penjualan Pakaian Jadi Ekspor</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '431'");

                $row = mysqli_fetch_array($sql);
                $total431 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total431,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot431 = $total431/$jum_total1 * 100;
            echo number_format($percen_tot431,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Garment Export Sales Discount</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Potongan Penjualan Pakaian Jadi Lokal</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '432'");

                $row = mysqli_fetch_array($sql);
                $total432 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total432,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot432 = $total432/$jum_total1 * 100;
            echo number_format($percen_tot432,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Garment Local Sales Discount</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Potongan Penjualan Lainnya</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '438'");

                $row = mysqli_fetch_array($sql);
                $total438 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total438,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot438 = $total438/$jum_total1 * 100;
            echo number_format($percen_tot438,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other Sales Discount</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Potongan Penjualan Jasa Jahit Pakaian Jadi Ekspor</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '433'");

                $row = mysqli_fetch_array($sql);
                $total433 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total433,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot433 = $total433/$jum_total1 * 100;
            echo number_format($percen_tot433,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Export Cmt Service Revenue Discount</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Potongan Penjualan Jasa Jahit Pakaian Jadi Lokal</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '434'");

                $row = mysqli_fetch_array($sql);
                $total434 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total434,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot434 = $total434/$jum_total1 * 100;
            echo number_format($percen_tot434,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Local Cmt Service Revenue Discount</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Potongan Penjualan Jasa Lainnya</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;border-bottom: 1px solid black;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '439'");

                $row = mysqli_fetch_array($sql);
                $total439 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total439,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;border-bottom: 1px solid black;">
                <?php 
            $percen_tot439 = $total439/$jum_total1 * 100;
            echo number_format($percen_tot439,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other Cmt Service Revenue Discount</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>TOTAL POTONGAN PENJUALAN</b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('431','432','438','433','434','439')) a");

                $row = mysqli_fetch_array($sql);
                $totalgr3 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($totalgr3,2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_totgr3 = $totalgr3/$jum_total1 * 100;
            echo number_format($percen_totgr3,2); echo ' %'; 
            ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>SALES DISCOUNT TOTAL</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>PENJUALAN BERSIH</b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;">
                <?php 
                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('411','412','418','413','414','419')) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;

                $sql2 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('421','422','429')) a");

                $row2 = mysqli_fetch_array($sql2);
                $total2 = isset($row2['total']) ? $row2['total'] : 0;

                $sql3 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('431','432','438','433','434','439')) a");

                $row3 = mysqli_fetch_array($sql3);
                $total3 = isset($row3['total']) ? $row3['total'] : 0;

                $jum_total = $total1 + $total2 - $total3;

                $total_ = number_format(abs($jum_total),2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"><?php 
            $percen_tot = $jum_total/$jum_total * 100;
            echo number_format($percen_tot,2); echo ' %'; 
            ?></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>NET SALES</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        
        <!-- potongan-penjualan - end -->

        <!-- beban-pokok-penjualan - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>BEBAN POKOK PENJUALAN</b></th>
            <th style="text-align: right;vertical-align: middle;width: 14%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>COST OF GOODS SOLD</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Harga Pokok Penjualan Pakaian Jadi Ekspor</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('411','412','418','413','414','419')) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;

                $sql2 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('421','422','429')) a");

                $row2 = mysqli_fetch_array($sql2);
                $total2 = isset($row2['total']) ? $row2['total'] : 0;

                $sql3 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('431','432','438','433','434','439')) a");

                $row3 = mysqli_fetch_array($sql3);
                $total3 = isset($row3['total']) ? $row3['total'] : 0;

                $jum_total2 = $total1 + $total2 - $total3;

                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '511'");

                $row = mysqli_fetch_array($sql);
                $total511 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total511 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot511 = $total511/$jum_total2 * 100;
            echo number_format($percen_tot511,2);  echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>cost of good sold for export sales</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Harga Pokok Penjualan Pakaian Jadi Lokal</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '512'");

                $row = mysqli_fetch_array($sql);
                $total512 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total512 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot512 = $total512/$jum_total2 * 100;
            echo number_format($percen_tot512,2);  echo ' %';  
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Cost of good sold for local sales</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Harga Pokok Penjualan Lainnya</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '518'");

                $row = mysqli_fetch_array($sql);
                $total518 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total518 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot518 = $total518/$jum_total2 * 100;
            echo number_format($percen_tot518,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Cost of good sold for other sales</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Harga Pokok Penjualan Jasa Jahit Pakaian Jadi Ekspor</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '513'");

                $row = mysqli_fetch_array($sql);
                $total513 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total513 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot513 = $total513/$jum_total2 * 100;
            echo number_format($percen_tot513,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Cost of revenue for export CMT service</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Harga Pokok Penjualan Jasa Jahit Pakaian Jadi Lokal</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '514'");

                $row = mysqli_fetch_array($sql);
                $total514 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total514 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot514 = $total514/$jum_total2 * 100;
            echo number_format($percen_tot514,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Cost of revenue for local cmt service</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Harga Pokok Penjualan Jasa Jahit Lainnya</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '519'");

                $row = mysqli_fetch_array($sql);
                $total519 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total519 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot519 = $total519/$jum_total2 * 100;
            echo number_format($percen_tot519,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Cost of revenue for other service</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Beban Penyesuaian Persediaan</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;border-bottom: 1px solid black;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '591'");

                $row = mysqli_fetch_array($sql);
                $total591 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total591 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;border-bottom: 1px solid black;">
                <?php 
            $percen_tot591 = $total591/$jum_total2 * 100;
            echo number_format($percen_tot591,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Inventory adjustment expense</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>HARGA POKOK PENJUALAN</b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('511','512','518','513','514','519','591')) a");

                $row = mysqli_fetch_array($sql);
                $totalgr4 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($totalgr4 * -1),2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_totgr4 = $totalgr4/$jum_total2 * 100;
            echo number_format($percen_totgr4,2); echo ' %'; 
            ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>COST OF GOODS SOLD</i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LABA / (RUGI) KOTOR</b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;">
                <?php 
                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('411','412','418','413','414','419')) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;

                $sql2 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('421','422','429')) a");

                $row2 = mysqli_fetch_array($sql2);
                $total2 = isset($row2['total']) ? $row2['total'] : 0;

                $sql3 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('431','432','438','433','434','439')) a");

                $row3 = mysqli_fetch_array($sql3);
                $total3 = isset($row3['total']) ? $row3['total'] : 0;
                $sql4 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('511','512','518','513','514','519','591')) a");

                $row = mysqli_fetch_array($sql);
                $total4 = isset($row['total']) ? $row['total'] : 0;

                $jum_total2 = $total1 + $total2 - $total3;
                $jum_total3 = $total1 + $total2 - $total3 + $total4;

                $total_ = number_format(abs($jum_total3),2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"><?php 
            $percen_tot2 = $jum_total3/$jum_total2 * 100;
            echo number_format($percen_tot2,2); echo ' %'; 
            ?></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>GROSS PROFIT / (LOSS)</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        
        <!-- beban-pokok-penjualan - end -->

        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Beban penjualan</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '611'");

                $row = mysqli_fetch_array($sql);
                $total611 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total611 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot611 = $total611/$jum_total2 * 100;
            echo number_format($percen_tot611,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Selling expenses</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Beban administrasi dan umum</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;border-bottom: 1px solid black;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '711'");

                $row = mysqli_fetch_array($sql);
                $total711 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total711 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;border-bottom: 1px solid black;">
                <?php 
            $percen_tot711 = $total711/$jum_total2 * 100;
            echo number_format($percen_tot711,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>General and administrative expenses</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LABA / (RUGI) SEBELUM BUNGA DAN PAJAK</b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('611','711')) a");

                $row = mysqli_fetch_array($sql);
                $totalgr5 = isset($row['total']) ? $row['total'] : 0;

                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('411','412','418','413','414','419')) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;

                $sql2 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('421','422','429')) a");

                $row2 = mysqli_fetch_array($sql2);
                $total2 = isset($row2['total']) ? $row2['total'] : 0;

                $sql3 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('431','432','438','433','434','439')) a");

                $row3 = mysqli_fetch_array($sql3);
                $total3 = isset($row3['total']) ? $row3['total'] : 0;
                $sql4 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('511','512','518','513','514','519','591')) a");

                $row = mysqli_fetch_array($sql);
                $total4 = isset($row['total']) ? $row['total'] : 0;

                $jum_total4 = $total1 + $total2 - $total3;
                $jum_total5 = $total1 + $total2 - $total3 + $total4 + $totalgr5;
                $total_ = number_format(abs($jum_total5),2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_totgr5 = $jum_total5/$jum_total2 * 100;
            echo number_format($percen_totgr5,2); echo ' %'; 
            ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PROFIT / (LOSS) BEFORE INTEREST AND TAX</i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>


        <!-- laba/rugi - sebelum pajak -->
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Beban bunga</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '821'");

                $row = mysqli_fetch_array($sql);
                $total821 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total821 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot821 = $total821/$jum_total2 * 100;
            echo number_format($percen_tot821,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Interest expense</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pendapatan bunga</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '1000'");

                $row = mysqli_fetch_array($sql);
                $total1000 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($total1000),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot1000 = $total1000/$jum_total2 * 100;
            echo number_format($percen_tot1000,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Interest income</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Laba / (Rugi) Selisih Kurs</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,sum(total) as total,eng_categori4 from(select nocoa,id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select nocoa,id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select nocoa,id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.nocoa) a where a.nocoa IN ('8.52.01','8.52.02')) a ");

                $row = mysqli_fetch_array($sql);
                $total_laba = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total_laba * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot_laba = $total_laba/$jum_total2 * 100;
            echo number_format($percen_tot_laba,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Forex exchange gain / loss</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Laba / (Rugi) Penjualan Aset Tetap</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '2000'");

                $row = mysqli_fetch_array($sql);
                $total2000 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total2000 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot2000 = $total2000/$jum_total2 * 100;
            echo number_format($percen_tot2000,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Gain / loss of FA sold</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Laba / (Rugi) Disposisi Aset Tetap</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '3000'");

                $row = mysqli_fetch_array($sql);
                $total3000 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total3000 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot3000 = $total3000/$jum_total2 * 100;
            echo number_format($percen_tot3000,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Gain / loss of FA disposal</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pendapatan lain-lain</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '813'");

                $row = mysqli_fetch_array($sql);
                $total811 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($total811),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot811 = $total811/$jum_total2 * 100;
            echo number_format($percen_tot811,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other income</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Beban lain-lain</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select ind_categori4,sum(total) as total,eng_categori4 from(select nocoa,id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select nocoa,id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select nocoa,id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.nocoa) a where a.nocoa IN ('8.53.01','8.54.01')) a");

                $row = mysqli_fetch_array($sql);
                $total_blain = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total_blain * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot_blain = $total_blain/$jum_total2 * 100;
            echo number_format($percen_tot_blain,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other expense</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pendapatan sewa</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '815'");

                $row = mysqli_fetch_array($sql);
                $total815 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(abs($total815),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot815 = $total815/$jum_total2 * 100;
            echo number_format($percen_tot815,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Rent income</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Beban administrasi bank</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;border-bottom: 1px solid black;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '822'");

                $row = mysqli_fetch_array($sql);
                $total822 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format(($total822 * -1),2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;border-bottom: 1px solid black;">
                <?php 
            $percen_tot822 = $total822/$jum_total2 * 100;
            echo number_format($percen_tot822,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Bank adm expense</i></td>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LABA / (RUGI) SEBELUM BUNGA DAN PAJAK</b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('611','711')) a");

                $row = mysqli_fetch_array($sql);
                $totalgr5 = isset($row['total']) ? $row['total'] : 0;

                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('411','412','418','413','414','419')) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;

                $sql2 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('421','422','429')) a");

                $row2 = mysqli_fetch_array($sql2);
                $total2 = isset($row2['total']) ? $row2['total'] : 0;

                $sql3 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('431','432','438','433','434','439')) a");

                $row3 = mysqli_fetch_array($sql3);
                $total3 = isset($row3['total']) ? $row3['total'] : 0;
                $sql4 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('511','512','518','513','514','519','591')) a");

                $row4 = mysqli_fetch_array($sql4);
                $total4 = isset($row4['total']) ? $row4['total'] : 0;

                $sql5 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('821','813','815','822')) a");

                $row5 = mysqli_fetch_array($sql5);
                $total5 = isset($row5['total']) ? $row5['total'] : 0;

                $sql6 = mysqli_query($conn2,"select ind_categori4,sum(total) as total,eng_categori4 from(select nocoa,id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select nocoa,id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select nocoa,id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.nocoa) a where a.nocoa IN ('8.52.01','8.52.02','8.53.01','8.54.01')) a");

                $row6 = mysqli_fetch_array($sql6);
                $total6 = isset($row6['total']) ? $row6['total'] : 0;

                $jum_total6 = $total1 + $total2 - $total3;
                $jum_total7 = $total1 + $total2 - $total3 + $total4 + $totalgr5 + $total5 + $total6;
                $total_ = number_format(abs($jum_total7),2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_totgr6 = $jum_total7/$jum_total2 * 100;
            echo number_format($percen_totgr6,2); echo ' %'; 
            ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PROFIT / (LOSS) BEFORE INTEREST AND TAX</i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>

        <!-- -->
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pajak Penghasilan Badan Kini</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '911'");

                $row = mysqli_fetch_array($sql);
                $total911 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total911,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_tot911 = $total911/$jum_total2 * 100;
            echo number_format($percen_tot911,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Current corporate income tax</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pajak Penghasilan Badan Tangguhan</td>
            <td style="text-align: right;vertical-align: middle;width: 14%;border-bottom: 1px solid black;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '921'");

                $row = mysqli_fetch_array($sql);
                $total921 = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total921,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 7%;border-bottom: 1px solid black;">
                <?php 
            $percen_tot921 = $total921/$jum_total2 * 100;
            echo number_format($percen_tot921,2); echo ' %'; 
            ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Deffered corporate income tax</i></td>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LABA / (RUGI) SEBELUM BUNGA DAN PAJAK</b></th>
            <th style="text-align: right;vertical-align: middle;width: 13%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('611','711')) a");

                $row = mysqli_fetch_array($sql);
                $totalgr5 = isset($row['total']) ? $row['total'] : 0;

                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('411','412','418','413','414','419')) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;

                $sql2 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('421','422','429')) a");

                $row2 = mysqli_fetch_array($sql2);
                $total2 = isset($row2['total']) ? $row2['total'] : 0;

                $sql3 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('431','432','438','433','434','439')) a");

                $row3 = mysqli_fetch_array($sql3);
                $total3 = isset($row3['total']) ? $row3['total'] : 0;
                $sql4 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('511','512','518','513','514','519','591')) a");

                $row4 = mysqli_fetch_array($sql4);
                $total4 = isset($row4['total']) ? $row4['total'] : 0;

                $sql5 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('821','811','815','822')) a");

                $row5 = mysqli_fetch_array($sql5);
                $total5 = isset($row5['total']) ? $row5['total'] : 0;

                $sql6 = mysqli_query($conn2,"select ind_categori4,sum(total) as total,eng_categori4 from(select nocoa,id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select nocoa,id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select nocoa,id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '2023') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '2023') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.nocoa) a where a.nocoa IN ('8.52.01','8.52.02','8.53.01','8.54.01')) a");

                $row6 = mysqli_fetch_array($sql6);
                $total6 = isset($row6['total']) ? $row6['total'] : 0;

                $sql7 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('911','921')) a");

                $row7 = mysqli_fetch_array($sql7);
                $total7 = isset($row7['total']) ? $row7['total'] : 0;

                $jum_total8 = $total1 + $total2 - $total3;
                $jum_total9 = $total1 + $total2 - $total3 + $total4 + $totalgr5 + $total5 + $total6 + $total7;
                $total_ = number_format(abs($jum_total9),2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 7%;">
                <?php 
            $percen_totgr7 = $jum_total9/$jum_total2 * 100;
            echo number_format($percen_totgr7,2); echo ' %'; 
            ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PROFIT / (LOSS) BEFORE INTEREST AND TAX</i></b></th>
        </tr>
        
    </table>

</body>
</html>




