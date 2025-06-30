<html>
<head>
    <title>Export Data SPL Monthly</title>
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
  header("Content-Disposition: attachment; filename=spl-monthly.xls");
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

    <table style="width:75%;font-size:14px;" >
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
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>LAPORAN LABA ATAU RUGI  DAN PENGHASILAN KOMPREHENSIF LAINNYA</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
            while($periode1 = mysqli_fetch_array($sql_periode1)){
                echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

            }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>STATEMENTS OF PROFIT OR LOSS AND OTHER COMPREHENSIVE INCOME</i></b></th>
        </tr>
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>UNTUK TAHUN YANG BERAKHIR PADA TANGGAL <?php echo $end_date; ?></b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
            while($periode1 = mysqli_fetch_array($sql_periode1)){
                echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

            }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>FOR THE YEARS ENDED <?php echo $end_date; ?></i></b></th>
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
            <th style="text-align: center;vertical-align: middle;width: 10%;"><b>YTD</b></th>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
        </tr>
        <!-- penjualan-kotor - start -->
        <tr>
            <th style="text-align: left;vertical-align: middle;width: 27%;"><b>PENJUALAN KOTOR</b></th>
            <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
            <?php
            $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
            while($periode1 = mysqli_fetch_array($sql_periode1)){
                echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

            }; ?>
            <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>GROSS SALES</i></b></th>
        </tr>
        <!-- LIST PENJUALAN KOTOR -->
        <?php
        $bulan = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];

// --- PENJUALAN KOTOR ---
        $total_penj_kotor = [];
        foreach ($bulan as $b) {
            $total_penj_kotor[$b] = 0;
        }
        $total_ytd_kotor = 0;

        $sql = mysqli_query($conn2,"select id,sub_kategori,(if(saldo_awal = 0, 0, - saldo_awal)) saldo_awal,(if(saldo_jan = 0, 0, - saldo_jan)) saldo_jan,(if(saldo_feb = 0, 0, - saldo_feb)) saldo_feb,(if(saldo_mar = 0, 0, - saldo_mar)) saldo_mar,(if(saldo_apr = 0, 0, - saldo_apr)) saldo_apr,(if(saldo_may = 0, 0, - saldo_may)) saldo_may,(if(saldo_jun = 0, 0, - saldo_jun)) saldo_jun,(if(saldo_jul = 0, 0, - saldo_jul)) saldo_jul,(if(saldo_aug = 0, 0, - saldo_aug)) saldo_aug,(if(saldo_sep = 0, 0, - saldo_sep)) saldo_sep,(if(saldo_oct = 0, 0, - saldo_oct)) saldo_oct,(if(saldo_nov = 0, 0, - saldo_nov)) saldo_nov,(if(saldo_dec = 0, 0, - saldo_dec)) saldo_dec,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('PENJUALAN KOTOR')) a left JOIN (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
            left join
            (select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
            (select no_coa nocoa,jan_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
            (select no_coa nocoa,feb_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
            (select no_coa nocoa,mar_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
            (select no_coa nocoa,apr_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
            (select no_coa nocoa,may_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
            (select no_coa nocoa,jun_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
            (select no_coa nocoa,jul_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
            (select no_coa nocoa,aug_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
            (select no_coa nocoa,sep_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
            (select no_coa nocoa,oct_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
            (select no_coa nocoa,nov_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

            (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
            (select no_coa nocoa,dec_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
            left join
            (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') group by no_coa) 
            jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
            order by no_coa asc) a group by a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by a.id asc");
while($row = mysqli_fetch_array($sql)) {
    $ytd = 0;
    echo '<tr>
    <td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>';

    for ($i = 0; $i < intval($jmlper); $i++) {
        $key = 'saldo_' . $bulan[$i];
        $val = isset($row[$key]) ? (float)$row[$key] : 0;
        $ytd += $val;
        $total_penj_kotor[$bulan[$i]] += $val;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$val.'">'.number_format(abs($val), 2).'</td>';
    }

    if ($jmlper > 1) {
        $total_ytd_kotor += $ytd;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$ytd.'">'.number_format(abs($ytd), 2).'</td>';
    }

    echo '<td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td></tr>';
}

echo '<tr><th style="width: 27%;"></th><th style="width: 10%;border-bottom: 1px solid black;"></th>';
for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="width: 10%;border-bottom: 1px solid black;"></th>';
}
echo '<th style="width: 27%;"></th></tr>';

echo '<tr><th style="text-align: left;vertical-align: middle;width: 27%;"><b>TOTAL PENJUALAN KOTOR</b></th>';
for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format(abs($total_penj_kotor[$bulan[$i]]), 2).'</th>';
}
if ($jmlper > 1) echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format(abs($total_ytd_kotor), 2).'</th>';
echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>GROSS SALES TOTAL</i></b></th></tr></tr>';
?>

<!-- RETURN PENJUALAN -->
<tr>
    <th style="text-align: left;vertical-align: middle;width: 27%;"><b>RETURN PENJUALAN</b></th>
    <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
    <?php
    $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
    while($periode1 = mysqli_fetch_array($sql_periode1)){
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

    }; ?>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>SALES RETUN</i></b></th>
</tr>

<?php
$total_return = [];
foreach ($bulan as $b) {
    $total_return[$b] = 0;
}
$total_ytd_return = 0;

$sql = mysqli_query($conn2,"select id,sub_kategori,(if(saldo_awal = 0, 0, - saldo_awal)) saldo_awal,(if(saldo_jan = 0, 0, - saldo_jan)) saldo_jan,(if(saldo_feb = 0, 0, - saldo_feb)) saldo_feb,(if(saldo_mar = 0, 0, - saldo_mar)) saldo_mar,(if(saldo_apr = 0, 0, - saldo_apr)) saldo_apr,(if(saldo_may = 0, 0, - saldo_may)) saldo_may,(if(saldo_jun = 0, 0, - saldo_jun)) saldo_jun,(if(saldo_jul = 0, 0, - saldo_jul)) saldo_jul,(if(saldo_aug = 0, 0, - saldo_aug)) saldo_aug,(if(saldo_sep = 0, 0, - saldo_sep)) saldo_sep,(if(saldo_oct = 0, 0, - saldo_oct)) saldo_oct,(if(saldo_nov = 0, 0, - saldo_nov)) saldo_nov,(if(saldo_dec = 0, 0, - saldo_dec)) saldo_dec,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('RETURN PENJUALAN')) a left JOIN (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
    left join
    (select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
    (select no_coa nocoa,jan_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
    (select no_coa nocoa,feb_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
    (select no_coa nocoa,mar_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
    (select no_coa nocoa,apr_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
    (select no_coa nocoa,may_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
    (select no_coa nocoa,jun_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
    (select no_coa nocoa,jul_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
    (select no_coa nocoa,aug_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
    (select no_coa nocoa,sep_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
    (select no_coa nocoa,oct_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
    (select no_coa nocoa,nov_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
    (select no_coa nocoa,dec_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
    order by no_coa asc) a group by a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by a.id asc");
while($row = mysqli_fetch_array($sql)) {
    $ytd = 0;
    echo '<tr><td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>';

    for ($i = 0; $i < intval($jmlper); $i++) {
        $key = 'saldo_' . $bulan[$i];
        $val = isset($row[$key]) ? (float)$row[$key] : 0;
        $ytd += $val;
        $total_return[$bulan[$i]] += $val;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$val.'">'.number_format($val, 2).'</td>';
    }

    if ($jmlper > 1) {
        $total_ytd_return += $ytd;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$ytd.'">'.number_format($ytd, 2).'</td>';
    }

    echo '<td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td></tr>';
}


echo '<tr>
<th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';
}

echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>';

echo '<tr><th style="text-align: left;vertical-align: middle;width: 27%;"><b>TOTAL RETURN PENJUALAN</b></th>';
for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_return[$bulan[$i]], 2).'</th>';
}
if ($jmlper > 1) echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_ytd_return, 2).'</th>';
echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>SALES RETURN TOTAL</i></b></th></tr>';
?>

<!-- POTONGAN PENJUALAN -->
<tr>
    <th style="text-align: left;vertical-align: middle;width: 27%;"><b>POTONGAN PENJUALAN</b></th>
    <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
    <?php
    $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
    while($periode1 = mysqli_fetch_array($sql_periode1)){
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

    }; ?>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>SALES DISCOUNT</i></b></th>
</tr>

<?php
$total_diskon = [];
foreach ($bulan as $b) {
    $total_diskon[$b] = 0;
}
$total_ytd_diskon = 0;

$sql = mysqli_query($conn2,"select id,sub_kategori,(if(saldo_awal = 0, 0, - saldo_awal)) saldo_awal,(if(saldo_jan = 0, 0, - saldo_jan)) saldo_jan,(if(saldo_feb = 0, 0, - saldo_feb)) saldo_feb,(if(saldo_mar = 0, 0, - saldo_mar)) saldo_mar,(if(saldo_apr = 0, 0, - saldo_apr)) saldo_apr,(if(saldo_may = 0, 0, - saldo_may)) saldo_may,(if(saldo_jun = 0, 0, - saldo_jun)) saldo_jun,(if(saldo_jul = 0, 0, - saldo_jul)) saldo_jul,(if(saldo_aug = 0, 0, - saldo_aug)) saldo_aug,(if(saldo_sep = 0, 0, - saldo_sep)) saldo_sep,(if(saldo_oct = 0, 0, - saldo_oct)) saldo_oct,(if(saldo_nov = 0, 0, - saldo_nov)) saldo_nov,(if(saldo_dec = 0, 0, - saldo_dec)) saldo_dec,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('POTONGAN PENJUALAN')) a left JOIN (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
    left join
    (select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
    (select no_coa nocoa,jan_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
    (select no_coa nocoa,feb_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
    (select no_coa nocoa,mar_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
    (select no_coa nocoa,apr_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
    (select no_coa nocoa,may_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
    (select no_coa nocoa,jun_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
    (select no_coa nocoa,jul_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
    (select no_coa nocoa,aug_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
    (select no_coa nocoa,sep_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
    (select no_coa nocoa,oct_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
    (select no_coa nocoa,nov_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
    (select no_coa nocoa,dec_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
    order by no_coa asc) a group by a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by a.id asc");
while($row = mysqli_fetch_array($sql)) {
    $ytd = 0;
    echo '<tr><td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>';

    for ($i = 0; $i < intval($jmlper); $i++) {
        $key = 'saldo_' . $bulan[$i];
        $val = isset($row[$key]) ? (float)$row[$key] : 0;
        $ytd += $val;
        $total_diskon[$bulan[$i]] += $val;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$val.'">'.number_format($val, 2).'</td>';
    }

    if ($jmlper > 1) {
        $total_ytd_diskon += $ytd;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$ytd.'">'.number_format($ytd, 2).'</td>';
    }

    echo '<td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td></tr>';
}

echo '<tr>
<th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';
}

echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>';

echo '<tr><th style="text-align: left;vertical-align: middle;width: 27%;"><b>TOTAL POTONGAN PENJUALAN</b></th>';
for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_diskon[$bulan[$i]], 2).'</th>';
}
if ($jmlper > 1) echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_ytd_diskon, 2).'</th>';
echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>SALES DISCOUNT TOTAL</i></b></th></tr>';


echo '<tr>
<th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';
}

echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>';
?>


<!-- PENJUALAN BERSIH -->
<tr><th style="text-align: left;vertical-align: middle;width: 27%;"><b>PENJUALAN BERSIH</b></th>
    <?php
    for ($i = 0; $i < intval($jmlper); $i++) {
        $net = $total_penj_kotor[$bulan[$i]] + $total_return[$bulan[$i]] + $total_diskon[$bulan[$i]];
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($net, 2).'</th>';
    }
    if ($jmlper > 1) {
        $total_net = $total_ytd_kotor + $total_ytd_return + $total_ytd_diskon;
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_net, 2).'</th>';
    }
    ?>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>NET SALES</i></b></th></tr>

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

    <!-- potongan-penjualan - end -->

    <!-- beban-pokok-penjualan - start -->
    <tr>
        <th style="text-align: left;vertical-align: middle;width: 27%;"><b>BEBAN POKOK PENJUALAN</b></th>
        <th style="text-align: right;vertical-align: middle;width: 10%;"></th>
        <?php
        $sql_periode1 = mysqli_query($conn2,"select DATE_FORMAT(periode, '%b %Y') periode from (select tgl_awal periode from tbl_tgl_tb where tgl_awal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir')) a");
        while($periode1 = mysqli_fetch_array($sql_periode1)){
            echo '<th style="text-align: right;vertical-align: middle;width: 10%;"></th>';

        }; ?>
        <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>COST OF GOODS SOLD</i></b></th>
    </tr>

    <?php
$total_beban_jual = [];
foreach ($bulan as $b) {
    $total_beban_jual[$b] = 0;
}
$total_ytd_beban_jual = 0;

$sql = mysqli_query($conn2,"select id,sub_kategori,(if(saldo_awal = 0, 0, - saldo_awal)) saldo_awal,(if(saldo_jan = 0, 0, - saldo_jan)) saldo_jan,(if(saldo_feb = 0, 0, - saldo_feb)) saldo_feb,(if(saldo_mar = 0, 0, - saldo_mar)) saldo_mar,(if(saldo_apr = 0, 0, - saldo_apr)) saldo_apr,(if(saldo_may = 0, 0, - saldo_may)) saldo_may,(if(saldo_jun = 0, 0, - saldo_jun)) saldo_jun,(if(saldo_jul = 0, 0, - saldo_jul)) saldo_jul,(if(saldo_aug = 0, 0, - saldo_aug)) saldo_aug,(if(saldo_sep = 0, 0, - saldo_sep)) saldo_sep,(if(saldo_oct = 0, 0, - saldo_oct)) saldo_oct,(if(saldo_nov = 0, 0, - saldo_nov)) saldo_nov,(if(saldo_dec = 0, 0, - saldo_dec)) saldo_dec,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN POKOK PENJUALAN')) a left JOIN (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
    left join
    (select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
    (select no_coa nocoa,jan_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
    (select no_coa nocoa,feb_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
    (select no_coa nocoa,mar_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
    (select no_coa nocoa,apr_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
    (select no_coa nocoa,may_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
    (select no_coa nocoa,jun_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
    (select no_coa nocoa,jul_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
    (select no_coa nocoa,aug_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
    (select no_coa nocoa,sep_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
    (select no_coa nocoa,oct_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
    (select no_coa nocoa,nov_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
    (select no_coa nocoa,dec_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
    order by no_coa asc) a group by a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by a.id asc");
while($row = mysqli_fetch_array($sql)) {
    $ytd = 0;
    echo '<tr><td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>';

    for ($i = 0; $i < intval($jmlper); $i++) {
        $key = 'saldo_' . $bulan[$i];
        $val = isset($row[$key]) ? (float)$row[$key] : 0;
        $ytd += $val;
        $total_beban_jual[$bulan[$i]] += $val;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$val.'">'.number_format($val, 2).'</td>';
    }

    if ($jmlper > 1) {
        $total_ytd_beban_jual += $ytd;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$ytd.'">'.number_format($ytd, 2).'</td>';
    }

    echo '<td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td></tr>';
}

echo '<tr>
<th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';
}

echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>';

echo '<tr><th style="text-align: left;vertical-align: middle;width: 27%;"><b>HARGA POKOK PENJUALAN</b></th>';
for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_beban_jual[$bulan[$i]], 2).'</th>';
}
if ($jmlper > 1) echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_ytd_beban_jual, 2).'</th>';
echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>COST OF GOODS SOLD</i></b></th>
</tr>';
?>

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

<tr><th style="text-align: left;vertical-align: middle;width: 27%;"><b>LABA / (RUGI) KOTOR</b></th>
    <?php
    for ($i = 0; $i < intval($jmlper); $i++) {
        $net = $total_penj_kotor[$bulan[$i]] + $total_return[$bulan[$i]] + $total_diskon[$bulan[$i]] + $total_beban_jual[$bulan[$i]];
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($net, 2).'</th>';
    }
    if ($jmlper > 1) {
        $total_net = $total_ytd_kotor + $total_ytd_return + $total_ytd_diskon + $total_ytd_beban_jual;
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_net, 2).'</th>';
    }
    ?>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>GROSS PROFIT / (LOSS)</i></b></th>
</tr>


<tr>
    <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
    <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>

<!-- beban-lainnya - end -->

<?php
$total_beban_lainnya = [];
foreach ($bulan as $b) {
    $total_beban_lainnya[$b] = 0;
}
$total_ytd_beban_lainnya = 0;

$sql = mysqli_query($conn2,"select id,sub_kategori,(if(saldo_awal = 0, 0, - saldo_awal)) saldo_awal,(if(saldo_jan = 0, 0, - saldo_jan)) saldo_jan,(if(saldo_feb = 0, 0, - saldo_feb)) saldo_feb,(if(saldo_mar = 0, 0, - saldo_mar)) saldo_mar,(if(saldo_apr = 0, 0, - saldo_apr)) saldo_apr,(if(saldo_may = 0, 0, - saldo_may)) saldo_may,(if(saldo_jun = 0, 0, - saldo_jun)) saldo_jun,(if(saldo_jul = 0, 0, - saldo_jul)) saldo_jul,(if(saldo_aug = 0, 0, - saldo_aug)) saldo_aug,(if(saldo_sep = 0, 0, - saldo_sep)) saldo_sep,(if(saldo_oct = 0, 0, - saldo_oct)) saldo_oct,(if(saldo_nov = 0, 0, - saldo_nov)) saldo_nov,(if(saldo_dec = 0, 0, - saldo_dec)) saldo_dec,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN LAINNYA')) a left JOIN (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
    left join
    (select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
    (select no_coa nocoa,jan_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
    (select no_coa nocoa,feb_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
    (select no_coa nocoa,mar_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
    (select no_coa nocoa,apr_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
    (select no_coa nocoa,may_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
    (select no_coa nocoa,jun_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
    (select no_coa nocoa,jul_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
    (select no_coa nocoa,aug_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
    (select no_coa nocoa,sep_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
    (select no_coa nocoa,oct_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
    (select no_coa nocoa,nov_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
    (select no_coa nocoa,dec_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
    order by no_coa asc) a group by a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by a.id asc");
while($row = mysqli_fetch_array($sql)) {
    $ytd = 0;
    echo '<tr><td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>';

    for ($i = 0; $i < intval($jmlper); $i++) {
        $key = 'saldo_' . $bulan[$i];
        $val = isset($row[$key]) ? (float)$row[$key] : 0;
        $ytd += $val;
        $total_beban_lainnya[$bulan[$i]] += $val;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$val.'">'.number_format($val, 2).'</td>';
    }

    if ($jmlper > 1) {
        $total_ytd_beban_lainnya += $ytd;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$ytd.'">'.number_format($ytd, 2).'</td>';
    }

    echo '<td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td></tr>';
}

echo '<tr>
<th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';
}

echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>';
?>

<tr><th style="text-align: left;vertical-align: middle;width: 27%;"><b>LABA / (RUGI) SEBELUM BUNGA DAN PAJAK</b></th>
    <?php
    for ($i = 0; $i < intval($jmlper); $i++) {
        $net = $total_penj_kotor[$bulan[$i]] + $total_return[$bulan[$i]] + $total_diskon[$bulan[$i]] + $total_beban_jual[$bulan[$i]] + $total_beban_lainnya[$bulan[$i]];
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($net, 2).'</th>';
    }
    if ($jmlper > 1) {
        $total_net = $total_ytd_kotor + $total_ytd_return + $total_ytd_diskon + $total_ytd_beban_jual + $total_ytd_beban_lainnya;
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_net, 2).'</th>';
    }
    ?>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PROFIT / (LOSS) BEFORE INTEREST AND TAX</i></b></th>
</tr>


<tr>
    <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
    <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>


<!-- laba/rugi - sebelum pajak -->

<?php
$total_beban_bunga = [];
foreach ($bulan as $b) {
    $total_beban_bunga[$b] = 0;
}
$total_ytd_beban_bunga = 0;

$sql = mysqli_query($conn2,"select 
  a.id,
  a.sub_kategori,
  a.sub_kategori_eng,
IF(t.saldo_awal = 0, 0, -t.saldo_awal) AS saldo_awal,
  IF(t.saldo_jan   = 0, 0, -t.saldo_jan) AS saldo_jan,
  IF(t.saldo_feb   = 0, 0, -t.saldo_feb) AS saldo_feb,
  IF(t.saldo_mar   = 0, 0, -t.saldo_mar) AS saldo_mar,
  IF(t.saldo_apr   = 0, 0, -t.saldo_apr) AS saldo_apr,
  IF(t.saldo_may   = 0, 0, -t.saldo_may) AS saldo_may,
  IF(t.saldo_jun   = 0, 0, -t.saldo_jun) AS saldo_jun,
  IF(t.saldo_jul   = 0, 0, -t.saldo_jul) AS saldo_jul,
  IF(t.saldo_aug   = 0, 0, -t.saldo_aug) AS saldo_aug,
  IF(t.saldo_sep   = 0, 0, -t.saldo_sep) AS saldo_sep,
  IF(t.saldo_oct   = 0, 0, -t.saldo_oct) AS saldo_oct,
  IF(t.saldo_nov   = 0, 0, -t.saldo_nov) AS saldo_nov,
  IF(t.saldo_dec   = 0, 0, -t.saldo_dec) AS saldo_dec
FROM fs_kategori_laporan a
LEFT JOIN master_coa_ctg4 c
  ON c.ind_name = a.sub_kategori
LEFT JOIN (
  SELECT 
    id_ctg4,
    SUM(saldo_awal) saldo_awal,
    SUM(saldo_jan)   saldo_jan,
    SUM(saldo_feb)   saldo_feb,
    SUM(saldo_mar)   saldo_mar,
    SUM(saldo_apr)   saldo_apr,
    SUM(saldo_may)   saldo_may,
    SUM(saldo_jun)   saldo_jun,
    SUM(saldo_jul)   saldo_jul,
    SUM(saldo_aug)   saldo_aug,
    SUM(saldo_sep)   saldo_sep,
    SUM(saldo_oct)   saldo_oct,
    SUM(saldo_nov)   saldo_nov,
    SUM(saldo_dec)   saldo_dec
  FROM (
    SELECT 
      coa.id_ctg4,
      sa.jan_$tahun_akhir AS saldo_awal,
      COALESCE(sa.jan_$tahun_akhir + j.jan_diff, sa.jan_$tahun_akhir) AS saldo_jan,
      COALESCE(sa.feb_$tahun_akhir + j.feb_diff, sa.feb_$tahun_akhir) AS saldo_feb,
      -- lanjutkan sama untuk setiap bulan:
      COALESCE(sa.mar_$tahun_akhir + j.mar_diff, sa.mar_$tahun_akhir) AS saldo_mar,
      COALESCE(sa.apr_$tahun_akhir + j.apr_diff, sa.apr_$tahun_akhir) AS saldo_apr,
      COALESCE(sa.may_$tahun_akhir + j.may_diff, sa.may_$tahun_akhir) AS saldo_may,
      COALESCE(sa.jun_$tahun_akhir + j.jun_diff, sa.jun_$tahun_akhir) AS saldo_jun,
      COALESCE(sa.jul_$tahun_akhir + j.jul_diff, sa.jul_$tahun_akhir) AS saldo_jul,
      COALESCE(sa.aug_$tahun_akhir + j.aug_diff, sa.aug_$tahun_akhir) AS saldo_aug,
      COALESCE(sa.sep_$tahun_akhir + j.sep_diff, sa.sep_$tahun_akhir) AS saldo_sep,
      COALESCE(sa.oct_$tahun_akhir + j.oct_diff, sa.oct_$tahun_akhir) AS saldo_oct,
      COALESCE(sa.nov_$tahun_akhir + j.nov_diff, sa.nov_$tahun_akhir) AS saldo_nov,
      COALESCE(sa.dec_$tahun_akhir + j.dec_diff, sa.dec_$tahun_akhir) AS saldo_dec
    FROM mastercoa_v2 coa
    LEFT JOIN saldo_awal_tb sa
      ON sa.no_coa = coa.no_coa
    LEFT JOIN (
      SELECT
        no_coa,
        SUM(CASE WHEN MONTH(tgl_journal) = 1 THEN debit*rate - credit*rate END) AS jan_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 2 THEN debit*rate - credit*rate END) AS feb_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 3 THEN debit*rate - credit*rate END) AS mar_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 4 THEN debit*rate - credit*rate END) AS apr_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 5 THEN debit*rate - credit*rate END) AS may_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 6 THEN debit*rate - credit*rate END) AS jun_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 7 THEN debit*rate - credit*rate END) AS jul_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 8 THEN debit*rate - credit*rate END) AS aug_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 9 THEN debit*rate - credit*rate END) AS sep_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 10 THEN debit*rate - credit*rate END) AS oct_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 11 THEN debit*rate - credit*rate END) AS nov_diff,
        SUM(CASE WHEN MONTH(tgl_journal) = 12 THEN debit*rate - credit*rate END) AS dec_diff
      FROM tbl_list_journal
      WHERE YEAR(tgl_journal) = $tahun_akhir
      GROUP BY no_coa
    ) j ON j.no_coa = coa.no_coa
    WHERE coa.id_ctg2 = '8'
  ) sub
  GROUP BY id_ctg4
) t ON t.id_ctg4 = c.id_ctg4
WHERE a.status = 'Y'
  AND a.kategori = 'BEBAN BUNGA'
ORDER BY a.id;
");
while($row = mysqli_fetch_array($sql)) {
    $ytd = 0;
    echo '<tr><td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>';

    for ($i = 0; $i < intval($jmlper); $i++) {
        $key = 'saldo_' . $bulan[$i];
        $val = isset($row[$key]) ? (float)$row[$key] : 0;
        $ytd += $val;
        $total_beban_bunga[$bulan[$i]] += $val;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$val.'">'.number_format($val, 2).'</td>';
    }

    if ($jmlper > 1) {
        $total_ytd_beban_bunga += $ytd;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$ytd.'">'.number_format($ytd, 2).'</td>';
    }

    echo '<td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td></tr>';
}

echo '<tr>
<th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';
}

echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>';
?>

<tr><th style="text-align: left;vertical-align: middle;width: 27%;"><b>LABA / (RUGI) SEBELUM PAJAK</b></th>
    <?php
    for ($i = 0; $i < intval($jmlper); $i++) {
        $net = $total_penj_kotor[$bulan[$i]] + $total_return[$bulan[$i]] + $total_diskon[$bulan[$i]] + $total_beban_jual[$bulan[$i]] + $total_beban_lainnya[$bulan[$i]] + $total_beban_bunga[$bulan[$i]];
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($net, 2).'</th>';
    }
    if ($jmlper > 1) {
        $total_net = $total_ytd_kotor + $total_ytd_return + $total_ytd_diskon + $total_ytd_beban_jual + $total_ytd_beban_lainnya + $total_ytd_beban_bunga;
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_net, 2).'</th>';
    }
    ?>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>PROFIT / (LOSS) BEFORE TAX</i></b></th>
</tr>


<tr>
    <th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
    <th style="text-align: right;vertical-align: middle;width: 13%;"></th>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>

<!-- -->
<?php
$total_beban_pajak = [];
foreach ($bulan as $b) {
    $total_beban_pajak[$b] = 0;
}
$total_ytd_beban_pajak = 0;

$sql = mysqli_query($conn2,"select id,sub_kategori,(if(saldo_awal = 0, 0, - saldo_awal)) saldo_awal,(if(saldo_jan = 0, 0, - saldo_jan)) saldo_jan,(if(saldo_feb = 0, 0, - saldo_feb)) saldo_feb,(if(saldo_mar = 0, 0, - saldo_mar)) saldo_mar,(if(saldo_apr = 0, 0, - saldo_apr)) saldo_apr,(if(saldo_may = 0, 0, - saldo_may)) saldo_may,(if(saldo_jun = 0, 0, - saldo_jun)) saldo_jun,(if(saldo_jul = 0, 0, - saldo_jul)) saldo_jul,(if(saldo_aug = 0, 0, - saldo_aug)) saldo_aug,(if(saldo_sep = 0, 0, - saldo_sep)) saldo_sep,(if(saldo_oct = 0, 0, - saldo_oct)) saldo_oct,(if(saldo_nov = 0, 0, - saldo_nov)) saldo_nov,(if(saldo_dec = 0, 0, - saldo_dec)) saldo_dec,sub_kategori_eng from (select id,ref,sub_kategori,sub_kategori_eng from fs_kategori_laporan where status = 'Y' and kategori in ('BEBAN PAJAK')) a left JOIN (select ind_categori4,id_ctg4,sum(saldo_awal) saldo_awal,sum(saldo_jan) saldo_jan,sum(saldo_feb) saldo_feb,sum(saldo_mar) saldo_mar,sum(saldo_apr) saldo_apr,sum(saldo_may) saldo_may,sum(saldo_jun) saldo_jun,sum(saldo_jul) saldo_jul,sum(saldo_aug) saldo_aug,sum(saldo_sep) saldo_sep,sum(saldo_oct) saldo_oct,sum(saldo_nov) saldo_nov,sum(saldo_dec) saldo_dec from (select no_coa,nama_coa,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4,saldo_awal,saldo_jan,saldo_feb,saldo_mar,saldo_apr,saldo_may,saldo_jun,saldo_jul,saldo_aug,saldo_sep,saldo_oct,saldo_nov,saldo_dec from 
    (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg4 from mastercoa_v2 order by no_coa asc) coa
    left join
    (select nocoa coa_no, saldo saldo_awal,(saldo + coalesce((debit_idr - credit_idr),0)) saldo_jan from 
    (select no_coa nocoa,jan_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '01' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jan on jan.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_feb from 
    (select no_coa nocoa,feb_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '02' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) feb on feb.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_mar from 
    (select no_coa nocoa,mar_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '03' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) mar on mar.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_apr from 
    (select no_coa nocoa,apr_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '04' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) apr on apr.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_may from 
    (select no_coa nocoa,may_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '05' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) may on may.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jun from 
    (select no_coa nocoa,jun_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '06' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jun on jun.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_jul from 
    (select no_coa nocoa,jul_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '07' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) jul on jul.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_aug from 
    (select no_coa nocoa,aug_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '08' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) aug on aug.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_sep from 
    (select no_coa nocoa,sep_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '09' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) sep on sep.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_oct from 
    (select no_coa nocoa,oct_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '10' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) oct on oct.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_nov from 
    (select no_coa nocoa,nov_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '11' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) nov on nov.coa_no = coa.no_coa left join

    (select nocoa coa_no, (saldo + coalesce((debit_idr - credit_idr),0)) saldo_dec from 
    (select no_coa nocoa,dec_$tahun_akhir as saldo from saldo_awal_tb order by no_coa asc) saldo
    left join
    (select no_coa coa_no,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr  from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') and (select tgl_akhir from tbl_tgl_tb where bulan = '12' and tahun = '$tahun_akhir') group by no_coa) 
    jnl on jnl.coa_no = saldo.nocoa order by nocoa asc) des on des.coa_no = coa.no_coa 
    order by no_coa asc) a group by a.id_ctg4) b on b.ind_categori4 = a.sub_kategori order by a.id asc");
while($row = mysqli_fetch_array($sql)) {
    $ytd = 0;
    echo '<tr><td style="text-align: left;vertical-align: middle;width: 27%;">'.$row['sub_kategori'].'</td>';

    for ($i = 0; $i < intval($jmlper); $i++) {
        $key = 'saldo_' . $bulan[$i];
        $val = isset($row[$key]) ? (float)$row[$key] : 0;
        $ytd += $val;
        $total_beban_pajak[$bulan[$i]] += $val;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$val.'">'.number_format($val, 2).'</td>';
    }

    if ($jmlper > 1) {
        $total_ytd_beban_pajak += $ytd;
        echo '<td style="text-align: right;vertical-align: middle;width: 10%;" value="'.$ytd.'">'.number_format($ytd, 2).'</td>';
    }

    echo '<td style="text-align: right;vertical-align: middle;width: 27%;">'.$row['sub_kategori_eng'].'</td></tr>';
}

echo '<tr>
<th style="text-align: left;vertical-align: middle;width: 27%;"><b></b></th>
<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';

for ($i = 0; $i < intval($jmlper); $i++) {
    echo '<th style="text-align: right;vertical-align: middle;width: 10%;border-bottom: 1px solid black;"></th>';
}

echo '<th style="text-align: right;vertical-align: middle;width: 27%;"><b><i></i></b></th>
</tr>';
?>

<tr><th style="text-align: left;vertical-align: middle;width: 27%;"><b>LABA / (RUGI) BERSIH</b></th>
    <?php
    for ($i = 0; $i < intval($jmlper); $i++) {
        $net = $total_penj_kotor[$bulan[$i]] + $total_return[$bulan[$i]] + $total_diskon[$bulan[$i]] + $total_beban_jual[$bulan[$i]] + $total_beban_lainnya[$bulan[$i]] + $total_beban_bunga[$bulan[$i]] + $total_beban_pajak[$bulan[$i]];
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($net, 2).'</th>';
    }
    if ($jmlper > 1) {
        $total_net = $total_ytd_kotor + $total_ytd_return + $total_ytd_diskon + $total_ytd_beban_jual + $total_ytd_beban_lainnya + $total_ytd_beban_bunga + $total_ytd_beban_pajak;
        echo '<th style="text-align: right;vertical-align: middle;width: 10%;">'.number_format($total_net, 2).'</th>';
    }
    ?>
    <th style="text-align: right;vertical-align: middle;width: 27%;"><b><i>NET INCOME / (LOSS)</i></b></th>
</tr>

</table>

</body>
</html>




