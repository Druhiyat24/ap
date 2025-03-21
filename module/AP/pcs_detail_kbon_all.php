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
    .text {
          mso-number-format: "\@";
          /*force text*/
        }
    </style>
 
    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=KartuAP_DetailKontrabon.xls");
    $nama_supp=$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

        <h4>PAYABLE CARD STATEMENT <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Nama Supplier</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Kontrabon Number</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Kontrabon Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Due Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Currency</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Begining Balance</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Addition</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Rate</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance IDR</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">COA No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">COA Name</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Item Type 1</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Item Type 2</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Relationship</th>
            <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th>    
            <th colspan="9" style="text-align: center;vertical-align: middle;background-color: #98FB98;">Account Payable Aging Based on Due Date</th>      
            <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th> 
            <th colspan="8" style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Account Payable Based on Due Date Projection</th>                                                                      
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">BPB</th>
            <th style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Adjustment</th> 
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Current</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;"><p class="text">1-30</p></th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">31-60</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">61-90</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">91-120</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">121-180</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">181-360</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">>360</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Total</th>
            <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Due</th>
            <?php 
                include '../../conn/conn.php';
                $end_date = date("Y-m-d",strtotime($_GET['end_date']));

                $sqlbulan = mysqli_query($conn1,"select kode_tanggal,bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun, CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)),LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun from dim_date where kode_tanggal BETWEEN CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01') and CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),LPAD(IF(MONTH('$end_date')+5 > 12,MOD((MONTH('$end_date')+5),12),(MONTH('$end_date')+5)),2,0),'01') GROUP BY bulan,tahun order by kode_tanggal asc");
                while($rowbulan = mysqli_fetch_array($sqlbulan)){
                    echo'<th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">'.$rowbulan['bulan_tahun'].'</th>';
                }
                
            ?>
            <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Total</th>
        </tr>
        <?php 
        // koneksi database
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
        (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,if(a.total > 0,(b.jml_potong - a.dp_value),(b.jml_potong - a.dp_value - a.pph_idr)) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d')  asc) 
            
        union (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,if(a.total > 0,(b.jml_potong - a.dp_value),(b.jml_potong - a.dp_value - a.pph_idr)) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d') asc)

        union (select a.nama_supp, a.no_kbon, a.tgl_kbon,DATE_FORMAT(b.create_date, '%Y-%m-%d') as create_date,DATEDIFF(b.tgl_tempo,b.tgl_kbon) as top, b.tgl_tempo, a.curr, a.total as bayar, a.total_bpb as total_ori, a.adjust as jml_potong from saldo_kbon_ap a left join kontrabon_h b on b.no_kbon = a.no_kbon)
            
        union (select a.nama_supp,a.no_kbon,c.tgl_kbon2 as tgl_kbon, DATE_FORMAT(c.create_date, '%Y-%m-%d') as create_date,DATEDIFF(c.tgl_tempo,c.tgl_kbon) as top, c.tgl_tempo,a.curr,(a.amount + a.pph_value) as bayar,(a.total_kbon + a.pph_value - b.jml_potong + c.dp_value) as totalori,if(c.total > 0,(b.jml_potong - c.dp_value),(b.jml_potong - c.dp_value - c.pph_idr)) as jml_potong  from list_payment a inner join potongan b on b.no_kbon = a.no_kbon left join kontrabon_h c on c.no_kbon = a.no_kbon where DATE_FORMAT(c.create_date, '%Y-%m-%d') < '$start_date' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and c.status !='CANCEL')
     ) as b GROUP BY no_kbon order by b.nama_supp asc) a left join 
         (select no_kbon no_journal, no_coa,nama_coa from kontrabon_h where no_coa != '') b on b.no_journal = a.no_kbon");


        $no = 1;
        $sa_akhir_ = 0;
        $kurang_ = 0;
        $sa_awal_ = 0;
        $tambah_ = 0;
        $saldo_akhir_idr_ = 0;
        $ttl_due_current = 0;
        $ttl_due_1 = 0;
        $ttl_due_2 = 0;
        $ttl_due_3 = 0;
        $ttl_due_4 = 0;
        $ttl_due_5 = 0;
        $ttl_due_6 = 0;
        $ttl_due_7 = 0;
        $ttl_tot_due = 0;

        $ttl_pro_due = 0;
        $ttl_pro_due0 = 0;
        $ttl_pro_due1 = 0;
        $ttl_pro_due2 = 0;
        $ttl_pro_due3 = 0;
        $ttl_pro_due4 = 0;
        $ttl_pro_due5 = 0;
        $ttl_tot_produe = 0;

        while($row = mysqli_fetch_array($data)){
            $namasupp = $row['nama_supp'];

    $start_date = date("Y-m-d",strtotime($_GET['start_date']));
    $tgl_kbon = $row['create_date'];
    $no_kbon = $row['no_kbon'];
    $bbayar = $row['bayar'];
    $duedate2 = $row['tgl_tempo'];
    // $kurang = $row['bayar'];
    $currin2 = $row['curr'];

    $sqlcek_tot = mysqli_query($conn1,"select total from kontrabon_h where no_kbon = '$no_kbon'");
    $rowcek_tot = mysqli_fetch_array($sqlcek_tot);
    $tot_total = isset($rowcek_tot['total']) ? $rowcek_tot['total'] : 0;


    // $sqlap12 = mysqli_query($conn1,"select no_kbon, total from saldo_awal_kbon where no_kbon = '$no_kbon' and tgl_kbon < '$start_date' GROUP BY no_kbon");
    // $rowap12 = mysqli_fetch_array($sqlap12);
    // $no_bpbap12 = isset($rowap12['no_kbon']) ? $rowap12['no_kbon'] : null;

    $sqllp = mysqli_query($conn1,"select no_kbon,tgl_kbon from list_payment where no_kbon = '$no_kbon' and DATE_FORMAT(create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY no_kbon");
    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['no_kbon']) ? $rowlp['no_kbon'] : null;


    $sqllp2 = mysqli_query($conn1,"select no_kbon,tgl_kbon from list_payment where no_kbon = '$no_kbon' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_kbon");
    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['no_kbon']) ? $rowlp2['no_kbon'] : null;

// if($tot_total != 0){

    if($no_lp != null){
        $kurang = $row['bayar'];
    }else{
        $kurang = 0;
    }

    if($no_lp2 != null){
        $bayar = $row['bayar'];
    }else{
        $bayar = 0;
    }

    if($bbayar == '0' and $tgl_kbon < $start_date){
         $sa_awal = $row['totalori'] + $row['jml_potong'] - $bayar;
    }
    elseif($tgl_kbon < $start_date){
        $sa_awal = $row['bayar'] - $bayar;
    }
        else{
        $sa_awal = 0;
    }


    // if($tgl_kbon < $start_date ){
    //     $sawal = $row['totalori'];
    // $tamhan = $row['jml_potong'];
    // $sa_awal = $sawal + $tamhan;
    // }else{
    //     $sa_awal = 0;
    // }

    if($tgl_kbon >= $start_date){
        $tambah = $row['totalori'];
    }else{
        $tambah = 0;
    }

    if($tgl_kbon >= $start_date){
        $tambahan = $row['jml_potong'];
    }else{
        $tambahan = 0;
    }

// }else{
//     $kurang = 0;
//     $bayar = 0;
//     $sa_awal = 0;
//     $tambah = 0;
//     $tambahan = 0; 
// }

    if ($currin2 == 'IDR') {
        $rate = 1;
    }elseif ($currin2 == 'CNY') {
        $rate = 2234.01;
    }else{
        $rate = $jml_rate;
    }

    $sa_akhir = $sa_awal + ($tambah + $tambahan) - $kurang; 
    $saldo_akhir_idr = $sa_akhir * $rate;
    $saldo_akhir_idr_ += $saldo_akhir_idr; 

    if($sa_awal == '0' and $tambah == '0' and $tambahan == '0' and $kurang == '0' and $sa_akhir == '0'){
        echo '';
    }else{

        if ($duedate2 > $end_date) {
            $due_current = $saldo_akhir_idr; 
        }else{
            $due_current = 0; 
        }
        $diff = (strtotime($end_date) - strtotime($duedate2));
        $aging_days = floor($diff/ (60*60*24));
        if ($aging_days >= 0 && $aging_days < 31) { $due_1 = $saldo_akhir_idr; }else{ $due_1 = 0; }
        if ($aging_days > 30 && $aging_days < 61) { $due_2 = $saldo_akhir_idr; }else{ $due_2 = 0; }
        if ($aging_days > 60 && $aging_days < 91) { $due_3 = $saldo_akhir_idr; }else{ $due_3 = 0; }
        if ($aging_days > 90 && $aging_days < 121) { $due_4 = $saldo_akhir_idr; }else{ $due_4 = 0; }
        if ($aging_days > 120 && $aging_days < 181) { $due_5 = $saldo_akhir_idr; }else{ $due_5 = 0; }
        if ($aging_days > 180 && $aging_days < 361) { $due_6 = $saldo_akhir_idr; }else{ $due_6 = 0; }
        if ($aging_days > 360) { $due_7 = $saldo_akhir_idr; }else{ $due_7 = 0; }
        $tot_due = $due_current + $due_1 + $due_2 + $due_3 + $due_4 + $due_5 + $due_6 + $due_7;
        $ttl_due_current += $due_current;
        $ttl_due_1 += $due_1;
        $ttl_due_2 += $due_2;
        $ttl_due_3 += $due_3;
        $ttl_due_4 += $due_4;
        $ttl_due_5 += $due_5;
        $ttl_due_6 += $due_6;
        $ttl_due_7 += $due_7;
        $ttl_tot_due += $tot_due;

        if ($duedate2 <= $end_date) { $pro_due = $saldo_akhir_idr; }else{ $pro_due = 0; }
        $pro_thn = date("Y",strtotime($end_date));
        $pro_bln = date("m",strtotime($end_date));
        $pro_hri = date("d",strtotime($end_date));
        $date_pro = $pro_thn . '-' . $pro_bln . '-' . $pro_hri;
        $probln1 = $pro_bln + 1;
        $probln2 = $pro_bln + 2;
        $probln3 = $pro_bln + 3;
        $probln4 = $pro_bln + 4;
        $probln5 = $pro_bln + 5;
        if ($probln1 > 12) { $bln1 = fmod($probln1, 12); $prothn1 = $pro_thn + 1; }else{ $bln1 = $probln1; $prothn1 = $pro_thn; }
        if ($probln2 > 12) { $bln2 = fmod($probln2, 12); $prothn2 = $pro_thn + 1; }else{ $bln2 = $probln2; $prothn2 = $pro_thn; }
        if ($probln3 > 12) { $bln3 = fmod($probln3, 12); $prothn3 = $pro_thn + 1; }else{ $bln3 = $probln3; $prothn3 = $pro_thn; }
        if ($probln4 > 12) { $bln4 = fmod($probln4, 12); $prothn4 = $pro_thn + 1; }else{ $bln4 = $probln4; $prothn4 = $pro_thn; }
        if ($probln5 > 12) { $bln5 = fmod($probln5, 12); $prothn5 = $pro_thn + 1; }else{ $bln5 = $probln5; $prothn5 = $pro_thn; }
        $pro_bln1 = sprintf("%02s", $bln1);
        $pro_bln2 = sprintf("%02s", $bln2);
        $pro_bln3 = sprintf("%02s", $bln3);
        $pro_bln4 = sprintf("%02s", $bln4);
        $pro_bln5 = sprintf("%02s", $bln5);
        $date_pro1 = $prothn1 . '-' . $pro_bln1 . '-' . '01';
        $date_pro2 = $prothn2 . '-' . $pro_bln2 . '-' . '01';
        $date_pro3 = $prothn3 . '-' . $pro_bln3 . '-' . '01';
        $date_pro4 = $prothn4 . '-' . $pro_bln4 . '-' . '01';
        $date_pro5 = $prothn5 . '-' . $pro_bln5 . '-' . '01';

        if ($duedate2 > $date_pro && $duedate2 < $date_pro1) { $pro_due0 = $saldo_akhir_idr; }else{ $pro_due0 = 0; }
        if ($duedate2 >= $date_pro1 && $duedate2 < $date_pro2) { $pro_due1 = $saldo_akhir_idr; }else{ $pro_due1 = 0; }
        if ($duedate2 >= $date_pro2 && $duedate2 < $date_pro3) { $pro_due2 = $saldo_akhir_idr; }else{ $pro_due2 = 0; }
        if ($duedate2 >= $date_pro3 && $duedate2 < $date_pro4) { $pro_due3 = $saldo_akhir_idr; }else{ $pro_due3 = 0; }
        if ($duedate2 >= $date_pro4 && $duedate2 < $date_pro5) { $pro_due4 = $saldo_akhir_idr; }else{ $pro_due4 = 0; }
        if ($duedate2 >= $date_pro5) { $pro_due5 = $saldo_akhir_idr; }else{ $pro_due5 = 0; }

        $tot_produe = $pro_due + $pro_due0 + $pro_due1 + $pro_due2 + $pro_due3 + $pro_due4 + $pro_due5;

        $ttl_pro_due += $pro_due;
        $ttl_pro_due0 += $pro_due0;
        $ttl_pro_due1 += $pro_due1;
        $ttl_pro_due2 += $pro_due2;
        $ttl_pro_due3 += $pro_due3;
        $ttl_pro_due4 += $pro_due4;
        $ttl_pro_due5 += $pro_due5;
        $ttl_tot_produe += $tot_produe;

        $sqlcoa = mysqli_query($conn1,"select a.no_kbon no_journal, a.no_coa,a.nama_coa, b.item_type1,b.item_type2,b.relasi from kontrabon_h a left join mastercoa_v2 b on b.no_coa = a.no_coa where a.no_kbon = '$no_kbon'");
        $rowcoa = mysqli_fetch_array($sqlcoa);
        $no_coa = isset($rowcoa['no_coa']) ? $rowcoa['no_coa'] : null;
        $nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;
        $item_type1 = isset($rowcoa['item_type1']) ? $rowcoa['item_type1'] : null;
        $item_type2 = isset($rowcoa['item_type2']) ? $rowcoa['item_type2'] : null;
        $relasi = isset($rowcoa['relasi']) ? $rowcoa['relasi'] : null;


        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align:left;" value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style="text-align:left;" value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
            <td style="text-align:left;" value = "'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
            <td value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>
            <td style="text-align:left;" value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="text-align:right;" value = "'.$sa_awal.'">'.$sa_awal.'</td>
            <td style="text-align:right;" value = "'.$tambah.'">'.$tambah.'</td> 
            <td style="text-align:right;" value = "'.$tambah.'">'.$tambahan.'</td>         
            <td style="text-align:right;" value = "'.$kurang.'">'.$kurang.'</td>
            <td style="text-align:right;" value = "'.$sa_akhir.'">'.$sa_akhir.'</td>
            <td style="text-align:right;" value="'.$rate.'">'.$rate.'</td>
            <td style="text-align:right;" value="'.$saldo_akhir_idr.'">'.$saldo_akhir_idr.'</td>
            <td value="'.$no_coa.'">'.$no_coa.'</td>
            <td value="'.$nama_coa.'">'.$nama_coa.'</td>
            <td value="'.$item_type1.'">'.$item_type1.'</td>
            <td value="'.$item_type2.'">'.$item_type2.'</td>
            <td value="'.$relasi.'">'.$relasi.'</td>
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;</td>
            <td style="text-align:right;" value="'.$due_current.'">'.number_format($due_current,2).'</td>
            <td style="text-align:right;" value="'.$due_1.'">'.number_format($due_1,2).'</td>
            <td style="text-align:right;" value="'.$due_2.'">'.number_format($due_2,2).'</td>
            <td style="text-align:right;" value="'.$due_3.'">'.number_format($due_3,2).'</td>
            <td style="text-align:right;" value="'.$due_4.'">'.number_format($due_4,2).'</td>
            <td style="text-align:right;" value="'.$due_5.'">'.number_format($due_5,2).'</td>
            <td style="text-align:right;" value="'.$due_6.'">'.number_format($due_6,2).'</td>
            <td style="text-align:right;" value="'.$due_7.'">'.number_format($due_7,2).'</td>
            <td style="text-align:right;" value="'.$tot_due.'">'.number_format($tot_due,2).'</td>
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;</td>
            <td style="text-align:right;" value="'.$pro_due.'">'.number_format($pro_due,2).'</td>
            <td style="text-align:right;" value="'.$pro_due0.'">'.number_format($pro_due0,2).'</td>
            <td style="text-align:right;" value="'.$pro_due1.'">'.number_format($pro_due1,2).'</td>
            <td style="text-align:right;" value="'.$pro_due2.'">'.number_format($pro_due2,2).'</td>
            <td style="text-align:right;" value="'.$pro_due3.'">'.number_format($pro_due3,2).'</td>
            <td style="text-align:right;" value="'.$pro_due4.'">'.number_format($pro_due4,2).'</td>
            <td style="text-align:right;" value="'.$pro_due5.'">'.number_format($pro_due5,2).'</td>
            <td style="text-align:right;" value="'.$tot_produe.'">'.number_format($tot_produe,2).'</td>
             ';
         }
        ?>
        <?php 
        }
        ?>
    </table>

</body>
</html>




