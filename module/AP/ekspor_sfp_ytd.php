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
    </style>
 
    <?php
    include '../../conn/conn.php';
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=sfp-ytd.xls");
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
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LAPORAN POSISI KEUANGAN</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>STATEMENTS OF FINANCIAL POSITION</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b><?php echo $end_date; ?></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i><?php echo $end_date; ?></i></b></th>
        </tr>

        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>(Expressed in Rupiah, unless otherwise stated)</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"><b>YTD <?php echo $end_date; ?></b></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- aset - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>ASET</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>ASSETS</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- aset_tetap - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>ASET LANCAR</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>CURRENT ASSETS</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Kas dan bank</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Cash on hand and in banks</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Piutang usaha</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '113'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Trade receivables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Piutang lain-lain</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '114'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other receivables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Persediaan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '115'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Inventories</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Uang muka pembelian</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '116'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Advances</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Biaya dibayar dimuka</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '117'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Prepaid expenses</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pajak dibayar dimuka</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '118'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Prepaid taxes</i></td>
        </tr>
         <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Aset Lancar</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('111','113','114','115','116','117','118')) a");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Current Assets</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- aset_tetap - end -->
        <!-- aset_tidak_tetap - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>ASET TIDAK LANCAR </b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>NONCURRENT ASSETS</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Investasi</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '112'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Investment</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Investasi pada entitas anak</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">0.00</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Investment in subsidiary</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset tetap</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '121'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Fixed assets</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset takberwujud</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '122'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Intangible assets</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset pajak tangguhan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">0.00</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Deferred tax assets</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Aset lain-lain</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '129'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other assets</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Aset Tidak Lancar</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('112','121','122','129')) a");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Noncurrent Assets</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>JUMLAH ASET</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px double; black;">
                <?php 
                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('111','113','114','115','116','117','118')) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;

                $sql2 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('112','121','122','129')) a");

                $row2 = mysqli_fetch_array($sql2);
                $total2 = isset($row2['total']) ? $row2['total'] : 0;
                $jum_total = $total1 + $total2;

                $total_ = number_format($jum_total,2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>TOTAL ASSETS</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- aset_tidak_tetap - end -->
        <!-- aset - end -->

        <!-- liabilitas&ekuitas - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LIABILITAS DAN EKUITAS </b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>LIABILITIES AND EQUITY</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- liabilitas - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LIABILITAS JANGKA PENDEK</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>CURRENT LIABILITIES</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang bank jangka pendek</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '212'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Short-term bank loans</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang usaha</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '211'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Trade payables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang pajak</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '215'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Taxes payables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Biaya akrual</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '214'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Accrued expenses</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang PPN</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">0.00</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>VAT Payable</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang lain-lain</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '219'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other payables</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Uang Muka Penjualan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '213'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Deferred Revenue</i></td>
        </tr>
         <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Liabilitas Jangka Pendek</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('212','211','215','214','219','213')) a");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Current Liabilities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- liabilitas_pendek - end -->
        <!-- liabilitas_panjang - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LIABILITAS JANGKA PANJANG</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>NONCURRENT LIABILITIES</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Utang bank jangka panjang</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '221'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Long-term bank loans</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Liabilitas imbalan pasca kerja</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">0.00</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Post-employment benefits obligation</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Liabilitas Jangka Panjang</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('221')) a");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Noncurrent Liabilities</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- liabilitas_panjang - end -->
        <!-- ekuitas - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>EKUITAS</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>EQUITY</i></b></th>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Modal saham</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '311'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Capital Stock</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Tambahan Modal Disetor</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '312'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Paid in Capital</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Saldo laba di tahan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '318'");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Retained earnings</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Pendapatan Komprehensif Lain-lain</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">0.00</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Other Comprehensive Income</i></td>
        </tr>
        <tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">Laba Tahun Berjalan</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,no_coa,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa where no_coa >= '3.40.01' order by no_coa asc) a group by a.id_ctg4) a) a");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;
                $total_ = number_format($total,2);
                echo $total_;
                ?>
            </td>
            <td style="text-align: right;vertical-align: middle;width: 27%;"><i>Profit of the year</i></td>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>Jumlah Ekuitas</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;">
                <?php 
                $sql = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('311','312','318')) a");

                $row = mysqli_fetch_array($sql);
                $total = isset($row['total']) ? $row['total'] : 0;

                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,no_coa,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa where no_coa >= '3.40.01' order by no_coa asc) a group by a.id_ctg4) a) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;
                $jum_total = $total + $total1;
                $total_ = number_format($jum_total,2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>Total Equity</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px solid black;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>JUMLAH LIABILITAS DAN EKUITAS</b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;border-bottom: 1px double; black;">
                <?php 
                $sql1 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('212','211','215','214','219','213')) a");

                $row1 = mysqli_fetch_array($sql1);
                $total1 = isset($row1['total']) ? $row1['total'] : 0;

                $sql2 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('221')) a");

                $row2 = mysqli_fetch_array($sql2);
                $total2 = isset($row2['total']) ? $row2['total'] : 0;

                $sql3 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 IN ('311','312','318')) a");

                $row3 = mysqli_fetch_array($sql3);
                $total3 = isset($row3['total']) ? $row3['total'] : 0;

                $sql4 = mysqli_query($conn2,"select sum(total) as total from (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,no_coa,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
                    (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
                    left join
                    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
                    on coa.no_coa = saldo.nocoa
                    left join
                    (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
                    jnl on jnl.coa_no = coa.no_coa where no_coa >= '3.40.01' order by no_coa asc) a group by a.id_ctg4) a) a");

                $row4 = mysqli_fetch_array($sql4);
                $total4 = isset($row4['total']) ? $row4['total'] : 0;
                $jum_total = $total1 + $total2 + $total3 + $total4;

                $total_ = number_format($jum_total,2);
                echo $total_;
                ?>
            </th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>TOTAL LIABILITIES AND EQUITY</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
            <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- liabilitas&ekuitas - end -->
        
    </table>

</body>
</html>




