<html>
<head>
    <title>Export Data Ke Excel </title>
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
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=KartuAP_Summary.xls");
    $nama_supp=$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>PAYABLE CARD STATEMENT <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama Supplier</th>
            <th colspan="7">Original Currency</th>
            <th rowspan="2">rate</th>
            <th rowspan="2">Ending Balance IDR</th>                                                                            
        </tr>
        <tr>
            <th>Curr</th>
            <th>Beginning balance</th>
            <th>Addition</th>
            <th>Deduction Adj/DP</th>
            <th>Deduction LP</th>
            <th>Adjustment</th>
            <th>Ending Balance</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        $bulan = date("m",strtotime($_GET['end_date']));  
        $tahun = date("Y",strtotime($_GET['end_date']));
        // menampilkan data

        $sql_tanggal = mysqli_query($conn1,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan' and tahun = '$tahun'");
    $row_tanggal = mysqli_fetch_array($sql_tanggal);
    $tgl_akhir = isset($row_tanggal['tgl_akhir']) ? $row_tanggal['tgl_akhir'] : 1;

    if ($tgl_akhir == $end_date) {
        $sql_rate = mysqli_query($conn1,"select v_codecurr, tanggal, rate from masterrate where tanggal = '$end_date' and v_codecurr = 'HARIAN'");
    }else{
        $sql_rate = mysqli_query($conn1,"select v_codecurr, tanggal, rate from masterrate where tanggal = '$end_date' and v_codecurr = 'PAJAK'");
    }
    
    $row_rate = mysqli_fetch_array($sql_rate);
    $jml_rate = isset($row_rate['rate']) ? $row_rate['rate'] : 1;
  
        $data = mysqli_query($conn1,"select Supplier, curr, saldo_awal,addition, deduction_adj, deduction_lp, adjustment, (saldo_awal + addition + deduction_adj + deduction_lp + adjustment) saldo_akhir from (select Supplier,curr,(sal_awal_bpb + sal_awal_kbon + sal_awal_lp) saldo_awal, add_bpb addition, adj_kbon deduction_adj,ded_lp deduction_lp, ((add_lp - ded_kbon) + (add_kbon - ded_bpb)) adjustment from (select Supplier, supp.curr, COALESCE(sal_awal_bpb,0) sal_awal_bpb, COALESCE(add_bpb,0) add_bpb, COALESCE(ded_bpb,0) ded_bpb, COALESCE(sal_akhir_bpb,0) sal_akhir_bpb, COALESCE(sal_awal_kbon,0) sal_awal_kbon, COALESCE(add_kbon,0) add_kbon, COALESCE(adj_kbon,0) adj_kbon, COALESCE(ded_kbon,0) ded_kbon, COALESCE(sal_akhir_kbon,0) sal_akhir_kbon, COALESCE(sal_awal_lp,0) sal_awal_lp, COALESCE(add_lp,0) add_lp, COALESCE(ded_lp * -1,0) ded_lp, COALESCE(sal_akhir_lp,0) sal_akhir_lp from (select DISTINCT b.Supplier,a.curr from bpb a inner join mastersupplier b on b.Id_Supplier = a.id_supplier where a.curr != '' order by b.Supplier asc) supp 
left join
(select nama_supp supp_bpb, curr, sum(beg_balance) sal_awal_bpb,sum(addition) add_bpb,sum(deduction) ded_bpb,sum(end_balance) sal_akhir_bpb from rpt_ap_bpb GROUP BY curr,nama_supp) bpb on bpb.supp_bpb = supp.Supplier and bpb.curr = supp.curr
left join
(select nama_supp supp_kbon, curr, sum(beg_balance) sal_awal_kbon,sum(add_bpb) add_kbon,sum(add_adj) adj_kbon,sum(deduction) ded_kbon,sum(end_balance) sal_akhir_kbon from rpt_ap_kbon GROUP BY curr,nama_supp) kbon on kbon.supp_kbon = supp.Supplier and kbon.curr = supp.curr
left join
(select nama_supp supp_lp, curr, sum(beg_balance) sal_awal_lp,sum(addition) add_lp,sum(deduction) ded_lp,sum(end_balance) sal_akhir_lp from rpt_ap_lp GROUP BY curr,nama_supp) lp on lp.supp_lp = supp.Supplier and lp.curr = supp.curr) a) a");

    $no = 1;
    $saldo_akhir_idr    = 0;
    $saldo_akhir_idr_   = 0;
    $saldo_awal_        = 0;
    $addition_          = 0;
    $deduction_adj_     = 0;
    $deduction_lp_      = 0;
    $adjustment_        = 0;
    $saldo_akhir_       = 0;

        while($row = mysqli_fetch_array($data)){
            $saldo_awal = $row['saldo_awal'];
    $curr_sum = $row['curr'];
    $addition = $row['addition'];
    $deduction_adj = $row['deduction_adj'];
    $deduction_lp = $row['deduction_lp'];
    $adjustment = $row['adjustment'];
    $saldo_akhir = $row['saldo_akhir'];

    if ($curr_sum == 'IDR') {
        $rate = 1;
    }else{
        $rate = $jml_rate;
    }

    $saldo_akhir_idr = $saldo_akhir * $rate;
    $saldo_akhir_idr_ += $saldo_akhir_idr; 
    $saldo_awal_ += $saldo_awal;
    $addition_ += $addition;
    $deduction_adj_ += $deduction_adj;
    $deduction_lp_ += $deduction_lp;
    $adjustment_ += $adjustment;
    $saldo_akhir_ += $saldo_akhir; 


    if($saldo_awal == '0' and $addition == '0' and $deduction_adj == '0' and $deduction_lp == '0' and $adjustment == '0'){
        echo '';
    }else{


        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align:left;" value = "'.$row['Supplier'].'">'.$row['Supplier'].'</td>
            <td style="text-align:left;" value="'.$row['curr'].'">'.$row['curr'].'</td>                         
            <td style="text-align:right;" value = "'.$saldo_awal.'">'.$saldo_awal.'</td>
            <td style="text-align:right;" value = "'.$addition.'">'.$addition.'</td>         
            <td style="text-align:right;" value = "'.$deduction_adj.'">'.$deduction_adj.'</td>
            <td style="text-align:right;" value = "'.$deduction_lp.'">'.$deduction_lp.'</td>
            <td style="text-align:right;" value = "'.$adjustment.'">'.$adjustment.'</td>
            <td style="text-align:right;" value = "'.$saldo_akhir.'">'.$saldo_akhir.'</td>
            <td style="text-align:right;" value="'.$rate.'">'.$rate.'</td>
            <td style="text-align:right;" value="'.$saldo_akhir_idr.'">'.$saldo_akhir_idr.'</td>
             ';
         }
        ?>
        <?php 
        }
        ?>
    </table>

</body>
</html>




