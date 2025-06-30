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

        <?php 
        $bulan_awal = date("m",strtotime($_GET['start_date']));
        $bulan_akhir = date("m",strtotime($_GET['end_date']));  
        $tahun_awal = date("Y",strtotime($_GET['start_date']));
        $tahun_akhir = date("Y",strtotime($_GET['end_date'])); 
        $kata_filter = $_GET['kata_filter'];

        $sql_nets = mysqli_query($conn2,"select id,sub_kategori,- sum(total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('PENJUALAN KOTOR','RETURN PENJUALAN','POTONGAN PENJUALAN')) a left JOIN
         (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
         (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
         left join
         (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
         on coa.no_coa = saldo.nocoa
         left join
         (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
         jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $row_nets = mysqli_fetch_array($sql_nets);
        $penjualan_bersih = isset($row_nets['total']) ? $row_nets['total'] : 0;

        $sql = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('PENJUALAN KOTOR')) a left JOIN
         (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
         (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
         left join
         (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
         on coa.no_coa = saldo.nocoa
         left join
         (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
         jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql2 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('RETURN PENJUALAN')) a left JOIN
         (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
         (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
         left join
         (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
         on coa.no_coa = saldo.nocoa
         left join
         (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
         jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql3 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('POTONGAN PENJUALAN')) a left JOIN
         (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
         (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
         left join
         (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
         on coa.no_coa = saldo.nocoa
         left join
         (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
         jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql4 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN POKOK PENJUALAN')) a left JOIN
         (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
         (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
         left join
         (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
         on coa.no_coa = saldo.nocoa
         left join
         (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
         jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql5 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN LAINNYA')) a left JOIN
         (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
         (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
         left join
         (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
         on coa.no_coa = saldo.nocoa
         left join
         (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
         jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc");

        $sql6 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN BUNGA')) a 
            left join (select id_ctg2,id_ctg4,ind_name ind4 from master_coa_ctg4 where id_ctg2 = '8') c on c.ind4 = a.sub_kategori left JOIN
            (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
            (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
            on coa.no_coa = saldo.nocoa
            left join
            (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where id_ctg2 = '8' GROUP BY a.id_ctg4) b on b.id_ctg4 = c.id_ctg4 order by id asc");

        $sql7 = mysqli_query($conn2,"select id,sub_kategori,- (total) total,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN PAJAK')) a left JOIN
         (select id_ctg2,id_ctg4,ind_categori4,((saldo + debit) - credit) total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from 
         (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
         left join
         (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
         on coa.no_coa = saldo.nocoa
         left join
         (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa) 
         jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a GROUP BY a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by id asc"); 

        $no = 01;
        $total_penjualan_kotor = 0;
        while($row = mysqli_fetch_array($sql)){
            $penjualan_kotor = isset($row['total']) ? $row['total'] : 0;
            $per_penjualan_kotor = number_format(($penjualan_kotor / $penjualan_bersih * 100),2);
            if ($penjualan_kotor > 0) {
                $penjualan_kotor = number_format($penjualan_kotor,2);
            }else{
                $penjualan_kotor = '('.number_format(abs($penjualan_kotor),2).')';
            }

            $total_penjualan_kotor += isset($row['total']) ? $row['total'] : 0;
            if ($total_penjualan_kotor > 0) {
                $total_penjualan_kotor_ = number_format($total_penjualan_kotor,2);
            }else{
                $total_penjualan_kotor_ = '('.number_format(abs($total_penjualan_kotor),2).')';
            }

            echo '<tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">'.$penjualan_kotor.'</td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_penjualan_kotor.'%</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td>
            </tr>';
        }
        echo '<tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">TOTAL PENJUALAN KOTOR</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_penjualan_kotor_.'</th>
        <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($total_penjualan_kotor / $penjualan_bersih * 100),2).'%</th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">GROSS SALES TOTAL</th> 
        </tr>
        <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;">RETURN PENJUALAN</th>
        <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">SALES RETURN</th>
        </tr>
        ';

        $total_retur_penjualan = 0;
        $total_retur_penjualan_ = 0;
        while($row2 = mysqli_fetch_array($sql2)){
            $retur_penjualan = isset($row2['total']) ? $row2['total'] : 0;
            $per_retur_penjualan = number_format(($retur_penjualan / $penjualan_bersih * 100),2);
            if ($retur_penjualan > 0) {
                $retur_penjualan = number_format($retur_penjualan,2);
            }else{
                $retur_penjualan = '('.number_format(abs($retur_penjualan),2).')';
            }

            $total_retur_penjualan += isset($row2['total']) ? $row2['total'] : 0;
            if ($total_retur_penjualan > 0) {
                $total_retur_penjualan_ = number_format($total_retur_penjualan,2);
            }else{
                $total_retur_penjualan_ = '('.number_format(abs($total_retur_penjualan),2).')';
            }

            echo '<tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row2['sub_kategori'].'</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">'.$retur_penjualan.'</td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_retur_penjualan.'%</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row2['sub_kategori_eng'].'</td>
            </tr>';
        }

        echo '<tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">TOTAL RETURN PENJUALAN</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_retur_penjualan_.'</th>
        <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($total_retur_penjualan / $penjualan_bersih * 100),2).'%</th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">SALES RETURN TOTAL</th> 
        </tr>
        <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;">POTONGAN PENJUALAN</th>
        <td style="text-align: right;vertical-align: middle;width: 16%;"></td>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">SALES DISCOUNT</th>
        </tr>';

        $total_potongan_penjualan = 0;
        $total_potongan_penjualan_ = 0;
        while($row3 = mysqli_fetch_array($sql3)){
            $potongan_penjualan = isset($row3['total']) ? $row3['total'] : 0;
            $per_potongan_penjualan = number_format(($potongan_penjualan / $penjualan_bersih * 100),2);
            if ($potongan_penjualan > 0) {
                $potongan_penjualan = number_format($potongan_penjualan,2);
            }else{
                $potongan_penjualan = '('.number_format(abs($potongan_penjualan),2).')';
            }

            $total_potongan_penjualan += isset($row3['total']) ? $row3['total'] : 0;
            if ($total_potongan_penjualan > 0) {
                $total_potongan_penjualan_ = number_format($total_potongan_penjualan,2);
            }else{
                $total_potongan_penjualan_ = '('.number_format(abs($total_potongan_penjualan),2).')';
            }

            echo '<tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row3['sub_kategori'].'</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">'.$potongan_penjualan.'</td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_potongan_penjualan.'%</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row3['sub_kategori_eng'].'</td>
            </tr>';
        }

        if ($penjualan_bersih > 0) {
            $penjualan_bersih_ = number_format($penjualan_bersih,2);
        }else{
            $penjualan_bersih_ = '('.number_format(abs($penjualan_bersih),2).')';
        }

        echo '<tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">TOTAL POTONGAN PENJUALAN</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_potongan_penjualan_.'</th>
        <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($total_potongan_penjualan / $penjualan_bersih * 100),2).'%</th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">SALES DISCOUNT TOTAL</th> 
        </tr>
        <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">PENJUALAN BERSIH</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;"">'.$penjualan_bersih_.'</th>
        <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;"">100%</th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">NET SALES</th>
        </tr>
        <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">BEBAN POKOK PENJUALAN</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">COST OF GOODS SOLD</th>
        </tr>';

        $total_beban_pokok_penjualan = 0;
        $total_beban_pokok_penjualan_ = 0;
        while($row4 = mysqli_fetch_array($sql4)){
            $beban_pokok_penjualan = isset($row4['total']) ? $row4['total'] : 0;
            $per_beban_pokok_penjualan = number_format(($beban_pokok_penjualan / $penjualan_bersih * 100),2);
            if ($beban_pokok_penjualan > 0) {
                $beban_pokok_penjualan = number_format($beban_pokok_penjualan,2);
            }else{
                $beban_pokok_penjualan = '('.number_format(abs($beban_pokok_penjualan),2).')';
            }

            $total_beban_pokok_penjualan += isset($row4['total']) ? $row4['total'] : 0;
            if ($total_beban_pokok_penjualan > 0) {
                $total_beban_pokok_penjualan_ = number_format($total_beban_pokok_penjualan,2);
            }else{
                $total_beban_pokok_penjualan_ = '('.number_format(abs($total_beban_pokok_penjualan),2).')';
            }

            echo '<tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row4['sub_kategori'].'</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">'.$beban_pokok_penjualan.'</td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_beban_pokok_penjualan.'%</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row4['sub_kategori_eng'].'</td>
            </tr>';
        }

        $laba_rugi_kotor = $penjualan_bersih + $total_beban_pokok_penjualan;
        if ($laba_rugi_kotor > 0) {
            $laba_rugi_kotor_ = number_format($laba_rugi_kotor,2);
        }else{
            $laba_rugi_kotor_ = '('.number_format(abs($laba_rugi_kotor),2).')';
        }

        echo '<tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">HARGA POKOK PENJUALAN</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$total_beban_pokok_penjualan_.'</th>
        <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($total_beban_pokok_penjualan / $penjualan_bersih * 100),2).'%</th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">COST OF GOODS SOLD</th> 
        </tr>
        <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">LABA RUGI KOTOR</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;"">'.$laba_rugi_kotor_.'</th>
        <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;"">'.number_format((( $penjualan_bersih + $total_beban_pokok_penjualan) / $penjualan_bersih * 100),2).'%</th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">GROSS PROFIT / (LOSS)</th>
        </tr>';

        $total_beban_lainnya = 0;
        $total_beban_lainnya_ = 0;
        while($row5 = mysqli_fetch_array($sql5)){
            $beban_lainnya = isset($row5['total']) ? $row5['total'] : 0;
            $per_beban_lainnya = number_format(($beban_lainnya / $penjualan_bersih * 100),2);
            if ($beban_lainnya > 0) {
                $beban_lainnya = number_format($beban_lainnya,2);
            }else{
                $beban_lainnya = '('.number_format(abs($beban_lainnya),2).')';
            }

            $total_beban_lainnya += isset($row5['total']) ? $row5['total'] : 0;
            if ($total_beban_lainnya > 0) {
                $total_beban_lainnya_ = number_format($total_beban_lainnya,2);
            }else{
                $total_beban_lainnya_ = '('.number_format(abs($total_beban_lainnya),2).')';
            }

            echo '<tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row5['sub_kategori'].'</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">'.$beban_lainnya.'</td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_beban_lainnya.'%</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row5['sub_kategori_eng'].'</td>
            </tr>';
        }

        $laba_rugi_sbl_bunga = $total_beban_lainnya + $laba_rugi_kotor;
        if ($laba_rugi_sbl_bunga > 0) {
            $laba_rugi_sbl_bunga_ = number_format($laba_rugi_sbl_bunga,2);
        }else{
            $laba_rugi_sbl_bunga_ = '('.number_format(abs($laba_rugi_sbl_bunga),2).')';
        }

        echo '<tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">LABA / (RUGI) SEBELUM BUNGA DAN PAJAK</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$laba_rugi_sbl_bunga_.'</th>
        <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($laba_rugi_sbl_bunga / $penjualan_bersih * 100),2).'%</th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">PROFIT / (LOSS) BEFORE INTEREST AND TAX</th> 
        </tr>
        <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>';

        $total_beban_bunga = 0;
        $total_beban_bunga_ = 0;
        while($row6 = mysqli_fetch_array($sql6)){
            $beban_bunga = isset($row6['total']) ? $row6['total'] : 0;
            $per_beban_bunga = number_format(($beban_bunga / $penjualan_bersih * 100),2);
            if ($beban_bunga > 0) {
                $beban_bunga = number_format($beban_bunga,2);
            }else{
                $beban_bunga = '('.number_format(abs($beban_bunga),2).')';
            }

            $total_beban_bunga += isset($row6['total']) ? $row6['total'] : 0;
            if ($total_beban_bunga > 0) {
                $total_beban_bunga_ = number_format($total_beban_bunga,2);
            }else{
                $total_beban_bunga_ = '('.number_format(abs($total_beban_bunga),2).')';
            }

            echo '<tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row6['sub_kategori'].'</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">'.$beban_bunga.'</td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_beban_bunga.'%</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row6['sub_kategori_eng'].'</td>
            </tr>';
        }

        $laba_rugi_sbl_pajak = $laba_rugi_sbl_bunga + $total_beban_bunga;
        if ($laba_rugi_sbl_pajak > 0) {
            $laba_rugi_sbl_pajak_ = number_format($laba_rugi_sbl_pajak,2);
        }else{
            $laba_rugi_sbl_pajak_ = '('.number_format(abs($laba_rugi_sbl_pajak),2).')';
        }

        echo '<tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">LABA / (RUGI) SEBELUM PAJAK</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$laba_rugi_sbl_pajak_.'</th>
        <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($laba_rugi_sbl_pajak / $penjualan_bersih * 100),2).'%</th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">PROFIT / (LOSS) BEFORE TAX</th> 
        </tr>
        <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>';

        $total_beban_pajak = 0;
        $total_beban_pajak_ = 0;
        while($row7 = mysqli_fetch_array($sql7)){
            $beban_pajak = isset($row7['total']) ? $row7['total'] : 0;
            $per_beban_pajak = number_format(($beban_pajak / $penjualan_bersih * 100),2);
            if ($beban_pajak > 0) {
                $beban_pajak = number_format($beban_pajak,2);
            }else{
                $beban_pajak = '('.number_format(abs($beban_pajak),2).')';
            }

            $total_beban_pajak += isset($row7['total']) ? $row7['total'] : 0;
            if ($total_beban_pajak > 0) {
                $total_beban_pajak_ = number_format($total_beban_pajak,2);
            }else{
                $total_beban_pajak_ = '('.number_format(abs($total_beban_pajak),2).')';
            }

            echo '<tr>
            <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row7['sub_kategori'].'</td>
            <td style="text-align: right;vertical-align: middle;width: 16%;">'.$beban_pajak.'</td>
            <td style="text-align: right;vertical-align: middle;width: 7%;">'.$per_beban_pajak.'%</td>
            <td style="text-align: right;vertical-align: middle;width: 27%;">'.$row7['sub_kategori_eng'].'</td>
            </tr>';
        }

        $laba_rugi_bersih = $laba_rugi_sbl_pajak + $total_beban_pajak;
        if ($laba_rugi_bersih > 0) {
            $laba_rugi_bersih_ = number_format($laba_rugi_bersih,2);
        }else{
            $laba_rugi_bersih_ = '('.number_format(abs($laba_rugi_bersih),2).')';
        }

        echo '<tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
        <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 7%;"></th>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <tr style="line-height: 40px;">
        <th style="text-align: left;vertical-align: middle;width: 27%;">LABA / (RUGI) BERSIH</th>
        <th style="text-align: right;vertical-align: middle;width: 16%;border-top:3px solid #000000;">'.$laba_rugi_bersih_.'</th>
        <th style="text-align: right;vertical-align: middle;width: 7%;border-top:3px solid #000000;">'.number_format(($laba_rugi_bersih / $penjualan_bersih * 100),2).'%</th>
        <th style="text-align: right;vertical-align: middle;width: 27%;">NET INCOME / (LOSS)</th> 
        </tr>';

        ?>

    </table>

</body>
</html>




