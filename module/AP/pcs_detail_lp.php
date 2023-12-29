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
    header("Content-Disposition: attachment; filename=KartuAP_DetailListPayment.xls");
    $nama_supp=$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>PAYABLE CARD STATEMENT <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th>No</th>
            <th>Nama Supplier</th>
            <th>List Payment Number</th>
            <th>List Payment Date</th>
            <th>Due Date</th>
            <th>Currency</th>
            <th>Begining Balance</th>
            <th>Addition</th>
            <th>Deduction</th>
            <th>Ending Balance</th>
            <th>Rate</th>
            <th>Ending Balance IDR</th>
            <th>no_coa</th>
            <th>nama_coa</th>
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
  
        $data = mysqli_query($conn1,"select * from (select * from(
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' GROUP BY no_payment order by create_date asc) union 
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY no_payment order by create_date asc) union 
        (select '1' as abc, nama_supp, no_payment, tgl_payment, DATE_FORMAT(create_date, '%Y-%m-%d') as create_dat,DATEDIFF(duedate,tgl_payment) as top, duedate, curr, total from saldo_lp_ap GROUP BY no_payment order by tgl_payment asc)) as b order by b.nama_supp asc) a left join 
                (select * from (select no_payment no_journal, no_coa,nama_coa from list_payment where no_coa != '' GROUP BY no_payment
union
select no_pay no_journal, no_coa,nama_coa from saldo_awal where no_coa != '') a) b on b.no_journal = a.no_payment");

        $no = 1;
        $sa_akhir_ = 0;
        $kurang_ = 0;
        $sa_awal_ = 0;
        $tambah_ = 0;
        $saldo_akhir_idr_ = 0;

        while($row = mysqli_fetch_array($data)){
            $namasupp = $row['nama_supp'];

    $tgl_kbon = $row['create_date'];
    $no_payment = $row['no_payment'];
    $currin3 = $row['curr'];

    // $sqllp = mysqli_query($conn1,"select list_payment_id,ttl_bayar from payment_ftr where list_payment_id = '$no_payment' and DATE_FORMAT(create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY list_payment_id
    //     union select a.no_reff, a.total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and DATE_FORMAT(b.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY a.no_reff");
    // $rowlp = mysqli_fetch_array($sqllp);
    // $no_lp = isset($rowlp['list_payment_id']) ? $rowlp['list_payment_id'] : null;


    // $sqllp2 = mysqli_query($conn1,"select list_payment_id,ttl_bayar from payment_ftr where list_payment_id = '$no_payment' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY list_payment_id  union select a.no_reff, a.total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and DATE_FORMAT(b.create_date, '%Y-%m-%d') < '$start_date' GROUP BY a.no_reff");
    // $rowlp2 = mysqli_fetch_array($sqllp2);
    // $no_lp2 = isset($rowlp2['list_payment_id']) ? $rowlp2['list_payment_id'] : null;

    // if($no_lp != null){
    //     $kurang = $row['total'];
    // }else{
    //     $kurang = 0;
    // }

    // if($no_lp2 != null){
    //     $bayar = $row['total'];
    // }else{
    //     $bayar = 0;
    // }

   $sqlcreff = mysqli_query($conn1,"select if(COUNT(a.no_reff) = '0',1,COUNT(no_reff)) as c_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where a.no_reff = '$no_payment' and b.bankout_date < '$end_date'");
    $rowcreff = mysqli_fetch_array($sqlcreff);
    $creff = isset($rowcreff['c_reff']) ? $rowcreff['c_reff'] : 1;

    // $sqllp = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and tgl_pelunasan between '$start_date' and '$end_date' GROUP BY list_payment_id
    //     union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date between '$start_date' and '$end_date' GROUP BY a.no_reff");
     $sqllp = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and DATE_FORMAT(create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and status != 'Cancel' GROUP BY list_payment_id
        union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date between '$start_date' and '$end_date' GROUP BY a.no_reff
        union select a.no_reff,a.total, 'CO' as kode,a.pph from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco where b.status != 'Cancel' and a.no_reff = '$no_payment' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff");

    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['list_payment_id']) ? $rowlp['list_payment_id'] : null;
    $kode = isset($rowlp['kode']) ? $rowlp['kode'] : null;
    $pph = isset($rowlp['pph']) ? $rowlp['pph'] : 0;
    $pph_h = $pph;
    


    // $sqllp2 = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and tgl_pelunasan < '$start_date' GROUP BY list_payment_id  union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date  < '$start_date' GROUP BY a.no_reff");
     $sqllp2 = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY list_payment_id  union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date < '$start_date' GROUP BY a.no_reff
         union select a.no_reff,a.total, 'CO' as kode,a.pph from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco where b.status != 'Cancel' and a.no_reff = '$no_payment' and a.tgl_pco < '$start_date' GROUP BY a.no_reff");

    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['list_payment_id']) ? $rowlp2['list_payment_id'] : null;
    $kode2 = isset($rowlp2['kode']) ? $rowlp2['kode'] : null;
    $pph2 = isset($rowlp2['pph']) ? $rowlp2['pph'] : 0;
    $pph_h2 = $pph2;

    if($no_lp != null && $kode == "OB"){
        $kurang_h = $rowlp['ttl_bayar'];
        
       if ($no_lp2 != null) {
         $kurang = $kurang_h;
        }else{

        $kurang = $kurang_h + $pph_h;
        }
        
    }elseif($no_lp != null && $kode == "P"){
        // $kurang = $rowlp['ttl_bayar'];
        $kurang = $row['total'];
    }elseif($no_lp != null && $kode == "CO"){
        // $kurang = $rowlp['ttl_bayar'];
        $kurang = $row['total'];
    }else{
        $kurang = 0;
    }

    if($no_lp2 != null && $kode2 == 'OB'){
        $bayar_h = $rowlp2['ttl_bayar'];
        $bayar = $bayar_h + $pph_h2;
    }elseif($no_lp2 != null && $kode2 == 'P'){
        // $bayar = $rowlp2['ttl_bayar'];
        $bayar = $row['total'];
    }elseif($no_lp2 != null && $kode2 == 'CO'){
        // $bayar = $rowlp2['ttl_bayar'];
        $bayar = $row['total'];
    }else{
        $bayar = 0;
    }



    if($tgl_kbon < $start_date){
        $sa_awal = $row['total'] - $bayar;
    }else{
        $sa_awal = 0;
    }

    if($tgl_kbon >= $start_date){
        $tambah = $row['total'] - $bayar;
    }else{
        $tambah = 0;
    }

    
    if ($currin3 == 'IDR') {
        $rate = 1;
    }else{
        $rate = $jml_rate;
    }


    $sa_akhir = $sa_awal + $tambah - $kurang; 
    $saldo_akhir_idr = $sa_akhir * $rate;
    $saldo_akhir_idr_ += $saldo_akhir_idr; 
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;

    if($sa_awal <= '0' and $tambah <= '0' and $kurang <= '0' and $sa_akhir <= '0'){
        echo '';
    }else{


        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align:left;" value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style="text-align:left;" value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td style="text-align:left;" value = "'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
            <td value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>
            <td style="text-align:left;" value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="text-align:right;" value = "'.$sa_awal.'">'.$sa_awal.'</td>
            <td style="text-align:right;" value = "'.$tambah.'">'.$tambah.'</td>         
            <td style="text-align:right;" value = "'.$kurang.'">'.$kurang.'</td>
            <td style="text-align:right;" value = "'.$sa_akhir.'">'.$sa_akhir.'</td>
            <td style="text-align:right;" value="'.$rate.'">'.$rate.'</td>
            <td style="text-align:right;" value="'.$saldo_akhir_idr.'">'.$saldo_akhir_idr.'</td>
            <td value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
            <td value="'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
             ';
         }
        ?>
        <?php 
        }
        ?>
    </table>

</body>
</html>




