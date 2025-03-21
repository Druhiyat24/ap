<div style="margin-left: 10px;margin-bottom: 10px;">
        <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        echo '<a target="_blank" href="pcs_detail_lp.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size:11px;"> EXCEL AP - LIST PAYMENT </i></button></a>';
        ?>
    </div> 

    <div class="tableFix" style="height: 350px;">        
<table id="datatable" class="table table-striped table-bordered text-nowrap" style="font-size: 12px;" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Nama Supplier</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">List Payment Number</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">List Payment Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;display: none;">TOP</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Due Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Currency</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Begining Balance</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Addition</th>
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
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Current</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">1-30</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">31-60</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">61-90</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">91-120</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">121-180</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">181-360</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">>360</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Total</th>
            <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Due</th>
            <?php 
                $end_date = 0;
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $end_date = date("Y-m-d",strtotime($_POST['end_date']));

                $sqlbulan = mysqli_query($conn1,"select kode_tanggal,bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun, CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)),LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun from dim_date where kode_tanggal BETWEEN CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01') and CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),LPAD(IF(MONTH('$end_date')+5 > 12,MOD((MONTH('$end_date')+5),12),(MONTH('$end_date')+5)),2,0),'01') GROUP BY bulan,tahun order by kode_tanggal asc");
                while($rowbulan = mysqli_fetch_array($sqlbulan)){
                    echo'<th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">'.$rowbulan['bulan_tahun'].'</th>';
                }
                }else{
                    echo'<th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>';
                } 
            ?>
            <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Total</th>
        </tr>
    </thead>
   
    <tbody>
<?php
    $value = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));
    $bulan = date("m",strtotime($_POST['end_date']));  
    $tahun = date("Y",strtotime($_POST['end_date']));                
    }

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

   if(empty($nama_supp) and empty($start_date) and empty($end_date)){
    echo '';
    }
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn1,"select * from(
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' GROUP BY no_payment order by create_date asc) union 
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY no_payment order by create_date asc) union 
        (select '1' as abc, nama_supp, no_payment, tgl_payment, DATE_FORMAT(create_date, '%Y-%m-%d') as create_dat,DATEDIFF(duedate,tgl_payment) as top, duedate, curr, total from saldo_lp_ap GROUP BY no_payment order by tgl_payment asc)) as b order by b.nama_supp asc");
    }
    else{
        $sql = mysqli_query($conn1,"select * from(select * from(
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' GROUP BY no_payment order by create_date asc) union 
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY no_payment order by create_date asc) union 
        (select '1' as abc, nama_supp, no_payment, tgl_payment, DATE_FORMAT(create_date, '%Y-%m-%d') as create_dat,DATEDIFF(duedate,tgl_payment) as top, duedate, curr, total from saldo_lp_ap GROUP BY no_payment order by tgl_payment asc)) as b order by b.nama_supp asc) a where a.nama_supp = '$nama_supp'");
    }

    $sa_akhir_  = 0;
    $kurang_    = 0;
    $sa_awal_   = 0;
    $tambah_    = 0;
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

    $sqldel = "delete from rpt_ap_lp where user_create = '$user'";
    $querydel = mysqli_query($conn1,$sqldel);

    $sqldel2 = "ALTER TABLE rpt_ap_lp";
    $querydel2 = mysqli_query($conn1,$sqldel2);

   while($row = mysqli_fetch_array($sql)){
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $tgl_lp = $row['create_date'];
    $no_payment = $row['no_payment'];
    $suppin3 = $row['nama_supp'];
    $paymentin = $row['no_payment'];
    $tgl_paymentin = $row['tgl_payment'];
    $due_date3 = $row['tgl_tempo'];
    $currin3 = $row['curr'];

    $sqlcreff = mysqli_query($conn1,"select if(COUNT(a.no_reff) = '0',1,COUNT(no_reff)) as c_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where a.no_reff = '$no_payment' and b.bankout_date < '$end_date'");
    $rowcreff = mysqli_fetch_array($sqlcreff);
    $creff = isset($rowcreff['c_reff']) ? $rowcreff['c_reff'] : 1;

    // $sqllp = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and tgl_pelunasan between '$start_date' and '$end_date' GROUP BY list_payment_id
    //     union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date between '$start_date' and '$end_date' GROUP BY a.no_reff");
    $sqllp = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and DATE_FORMAT(create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and status != 'Cancel' GROUP BY list_payment_id
        UNION
select no_lp,total_bayar, kode, '0' as pph from ap_payment where no_lp = '$no_payment' and tgl_payment between '$start_date' and '$end_date' GROUP BY no_lp
        union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date between '$start_date' and '$end_date' GROUP BY a.no_reff
        union select a.no_reff,a.total, 'CO' as kode,a.pph from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco where b.status != 'Cancel' and a.no_reff = '$no_payment' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff");

    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['list_payment_id']) ? $rowlp['list_payment_id'] : null;
    $kode = isset($rowlp['kode']) ? $rowlp['kode'] : null;
    $pph = isset($rowlp['pph']) ? $rowlp['pph'] : 0;
    $pph_h = $pph;
    


    // $sqllp2 = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and tgl_pelunasan < '$start_date' GROUP BY list_payment_id  union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date  < '$start_date' GROUP BY a.no_reff");
     $sqllp2 = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY list_payment_id 
     UNION
select no_lp,total_bayar, kode, '0' as pph from ap_payment where no_lp = '$no_payment' and tgl_payment < '$start_date' GROUP BY no_lp union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date < '$start_date' GROUP BY a.no_reff
         union select a.no_reff,a.total, 'CO' as kode,a.pph from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco where b.status != 'Cancel' and a.no_reff = '$no_payment' and a.tgl_pco < '$start_date' GROUP BY a.no_reff");

    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['list_payment_id']) ? $rowlp2['list_payment_id'] : null;
    $kode2 = isset($rowlp2['kode']) ? $rowlp2['kode'] : null;
    $pph2 = isset($rowlp2['pph']) ? $rowlp2['pph'] : 0;
    $pph_h2 = $pph2;

    if($no_lp != null && $kode == "OB"){
        $kurang_h = $rowlp['ttl_bayar'];

        if ($no_lp2 != null) {
         $kurang = $kurang_h + $pph_h;
        }else{

        $kurang = $kurang_h + $pph_h;
        }
        
        
    }elseif($no_lp != null && $kode == "P"){
        // $kurang = $rowlp['ttl_bayar'];
        if ($no_payment == 'LP/NAG/0424/03795') {
            $kurang = $rowlp['ttl_bayar'];
        }else{    
            $kurang = $row['total'];
        }
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


    if($tgl_lp < $start_date){
        $sa_awal = $row['total'] - $bayar;
    }else{
        $sa_awal = 0;
    }

    if($tgl_lp >= $start_date){
        $tambah = $row['total'] - $bayar;
    }else{
        $tambah = 0;
    }

    if ($currin3 == 'IDR') {
        $rate = 1;
    }elseif ($currin3 == 'CNY') {
        $rate = 2234.01;
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

    if($sa_awal == '0' and $tambah == '0' and $kurang == '0' and $sa_akhir == '0'){
        echo '';
    }else{

        if ($due_date3 > $end_date) {
            $due_current = $saldo_akhir_idr; 
        }else{
            $due_current = 0; 
        }
        $diff = (strtotime($end_date) - strtotime($due_date3));
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

        if ($due_date3 <= $end_date) { $pro_due = $saldo_akhir_idr; }else{ $pro_due = 0; }
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

        if ($due_date3 > $date_pro && $due_date3 < $date_pro1) { $pro_due0 = $saldo_akhir_idr; }else{ $pro_due0 = 0; }
        if ($due_date3 >= $date_pro1 && $due_date3 < $date_pro2) { $pro_due1 = $saldo_akhir_idr; }else{ $pro_due1 = 0; }
        if ($due_date3 >= $date_pro2 && $due_date3 < $date_pro3) { $pro_due2 = $saldo_akhir_idr; }else{ $pro_due2 = 0; }
        if ($due_date3 >= $date_pro3 && $due_date3 < $date_pro4) { $pro_due3 = $saldo_akhir_idr; }else{ $pro_due3 = 0; }
        if ($due_date3 >= $date_pro4 && $due_date3 < $date_pro5) { $pro_due4 = $saldo_akhir_idr; }else{ $pro_due4 = 0; }
        if ($due_date3 >= $date_pro5) { $pro_due5 = $saldo_akhir_idr; }else{ $pro_due5 = 0; }

        $tot_produe = $pro_due + $pro_due0 + $pro_due1 + $pro_due2 + $pro_due3 + $pro_due4 + $pro_due5;

        $ttl_pro_due += $pro_due;
        $ttl_pro_due0 += $pro_due0;
        $ttl_pro_due1 += $pro_due1;
        $ttl_pro_due2 += $pro_due2;
        $ttl_pro_due3 += $pro_due3;
        $ttl_pro_due4 += $pro_due4;
        $ttl_pro_due5 += $pro_due5;
        $ttl_tot_produe += $tot_produe;

        $sqlcoa = mysqli_query($conn1,"select a.no_journal, a.no_coa,a.nama_coa, b.item_type1,b.item_type2,b.relasi from (select no_payment no_journal, no_coa,nama_coa from list_payment where no_coa != '' and no_payment = '$no_payment' GROUP BY no_payment
union
select no_pay no_journal, no_coa,nama_coa from saldo_awal where no_coa != '' and no_pay = '$no_payment') a left join mastercoa_v2 b on b.no_coa = a.no_coa");
        $rowcoa = mysqli_fetch_array($sqlcoa);
        $no_coa = isset($rowcoa['no_coa']) ? $rowcoa['no_coa'] : null;
        $nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;
        $item_type1 = isset($rowcoa['item_type1']) ? $rowcoa['item_type1'] : null;
        $item_type2 = isset($rowcoa['item_type2']) ? $rowcoa['item_type2'] : null;
        $relasi = isset($rowcoa['relasi']) ? $rowcoa['relasi'] : null;
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td value="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td value="'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
            <td style="display: none;" value="'.$row['top'].'">'.$row['top'].' Days</td>
            <td value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>                            
            <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
            <td style="text-align:right;" value = "'.$sa_awal.'">'.number_format($sa_awal,2).'</td>
            <td style="text-align:right;" value = "'.$tambah.'">'.number_format($tambah,2).'</td>         
            <td style="text-align:right;" value = "'.$kurang.'">'.number_format($kurang,2).'</td>
            <td style="text-align:right;" value = "'.$sa_akhir.'">'.number_format($sa_akhir,2).'</td>
            <td style="text-align:right;" value="'.$rate.'">'.number_format($rate,2).'</td>
            <td style="text-align:right;" value="'.$saldo_akhir_idr.'">'.number_format($saldo_akhir_idr,2).'</td>
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

             $queryin = "INSERT INTO rpt_ap_lp (nama_supp,no_payment,tgl_payment,due_date,curr,beg_balance,addition,deduction,end_balance,end_balance_idr,create_date,due_0,due_1,due_2,due_3,due_4,due_5,due_6,due_7,due_total,produe_0,produe_1,produe_2,produe_3,produe_4,produe_5,produe_6,produe_total,filter_awal,filter_akhir,no_coa,item_type1,item_type2,relasi,user_create) 
                        VALUES 
                    ('$suppin3', '$paymentin', '$tgl_paymentin', '$due_date3', '$currin3', '$sa_awal', '$tambah', '$kurang', '$sa_akhir', '$saldo_akhir_idr', '$insert_date', '$due_current', '$due_1', '$due_2', '$due_3', '$due_4', '$due_5', '$due_6', '$due_7', '$tot_due', '$pro_due', '$pro_due0', '$pro_due1', '$pro_due2', '$pro_due3', '$pro_due4', '$pro_due5', '$tot_produe', '$start_date', '$end_date', '$no_coa', '$item_type1', '$item_type2', '$relasi','$user')";

            $executein = mysqli_query($conn1,$queryin);

            
        // } 

}
}
echo '
            <tr >
            <th colspan = "5" style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($sa_awal_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($tambah_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($kurang_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($sa_akhir_,2).'</th> 
            <th style="text-align: right;vertical-align: middle;"></th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($saldo_akhir_idr_,2).'</th>    
            <th colspan = "5" style="text-align: right;vertical-align: middle;"></th>  
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_current,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_1,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_2,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_3,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_4,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_5,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_6,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_7,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_tot_due,2).'</th>       
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due0,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due1,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due2,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due3,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due4,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due5,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_tot_produe,2).'</th>                                                                                                                                          
        </tr>';
?>                                                         
</tbody>                    
</table>
</div>
</br>