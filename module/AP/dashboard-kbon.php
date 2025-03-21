<?php
    include '../../conn/conn.php';
    ini_set('date.timezone', 'Asia/Jakarta');
    $insert_date = date("Y-m-d H:i:s");
    $value = '';
    $start_date = date("Y-m-d");
    $end_date = date("Y-m-d");    
    $bulan = date("m");  
    $tahun = date("Y");            

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

     $sql = mysqli_query($conn1,"select * from (select * from(
        (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,if(a.total > 0,(b.jml_potong - a.dp_value),(b.jml_potong - a.dp_value - a.pph_idr)) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d')  asc) 
            
        union (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,if(a.total > 0,(b.jml_potong - a.dp_value),(b.jml_potong - a.dp_value - a.pph_idr)) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d') asc)

        union (select a.nama_supp, a.no_kbon, a.tgl_kbon,DATE_FORMAT(b.create_date, '%Y-%m-%d') as create_date,DATEDIFF(b.tgl_tempo,b.tgl_kbon) as top, b.tgl_tempo, a.curr, a.total as bayar, a.total_bpb as total_ori, a.adjust as jml_potong from saldo_kbon_ap a left join kontrabon_h b on b.no_kbon = a.no_kbon)
            
        union (select a.nama_supp,a.no_kbon,c.tgl_kbon2 as tgl_kbon, DATE_FORMAT(c.create_date, '%Y-%m-%d') as create_date,DATEDIFF(c.tgl_tempo,c.tgl_kbon) as top, c.tgl_tempo,a.curr,(a.amount + a.pph_value) as bayar,(a.total_kbon + a.pph_value - b.jml_potong + c.dp_value) as totalori,if(c.total > 0,(b.jml_potong - c.dp_value),(b.jml_potong - c.dp_value - c.pph_idr)) as jml_potong  from list_payment a inner join potongan b on b.no_kbon = a.no_kbon left join kontrabon_h c on c.no_kbon = a.no_kbon where DATE_FORMAT(c.create_date, '%Y-%m-%d') < '$start_date' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and c.status !='CANCEL')
     ) as b GROUP BY no_kbon order by b.nama_supp asc) a left join 
         (select no_kbon no_journal, no_coa,nama_coa from kontrabon_h where no_coa != '') b on b.no_journal = a.no_kbon");


    $sa_akhir_  = 0;
    $kurang_    = 0;
    $sa_awal_   = 0;
    $tambah_    = 0;
    $tambahan_    = 0;
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

    $sqldel = "delete from dsb_ap_kbon";
    $querydel = mysqli_query($conn1,$sqldel);

    $sqldel2 = "ALTER TABLE dsb_ap_kbon AUTO_INCREMENT = 1;";
    $querydel2 = mysqli_query($conn1,$sqldel2);

   while($row = mysqli_fetch_array($sql)){
    $start_date = date("Y-m-d");
    $tgl_kbon = $row['create_date'];
    $no_kbon = $row['no_kbon'];
    $bbayar = $row['bayar'];
    // $kurang = $row['bayar'];
    $suppin2 = $row['nama_supp'];
    $kbonin = $row['no_kbon'];
    $tgl_kbonin = $row['tgl_kbon'];
    $duedate2 = $row['tgl_tempo'];
    $currin2 = $row['curr'];


    // $sqlap12 = mysqli_query($conn1,"select no_kbon, total from saldo_awal_kbon where no_kbon = '$no_kbon' and tgl_kbon < '$start_date' GROUP BY no_kbon");
    // $rowap12 = mysqli_fetch_array($sqlap12);
    // $no_bpbap12 = isset($rowap12['no_kbon']) ? $rowap12['no_kbon'] : null;

    $sqlcek_tot = mysqli_query($conn1,"select total from kontrabon_h where no_kbon = '$no_kbon'");
    $rowcek_tot = mysqli_fetch_array($sqlcek_tot);
    $tot_total = isset($rowcek_tot['total']) ? $rowcek_tot['total'] : 0;


    $sqllp = mysqli_query($conn1,"select no_kbon,tgl_kbon from list_payment where no_kbon = '$no_kbon' and  DATE_FORMAT(create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY no_kbon");
    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['no_kbon']) ? $rowlp['no_kbon'] : null;


    $sqllp2 = mysqli_query($conn1,"select no_kbon,tgl_kbon from list_payment where no_kbon = '$no_kbon' and  DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_kbon");
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

    if ($currin2 == 'IDR') {
        $rate = 1;
    }else{
        $rate = $jml_rate;
    }

    $sa_akhir = $sa_awal + ($tambah + $tambahan) - $kurang; 
    $saldo_akhir_idr = $sa_akhir * $rate;
    $saldo_akhir_idr_ += $saldo_akhir_idr;
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;
    $tambahan_ += $tambahan;

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
 

             $queryin2 = "INSERT INTO dsb_ap_kbon (nama_supp,no_kbon,tgl_kbon,due_date,curr,beg_balance,add_bpb,add_adj,deduction,end_balance,end_balance_idr,create_date,due_0,due_1,due_2,due_3,due_4,due_5,due_6,due_7,due_total,produe_0,produe_1,produe_2,produe_3,produe_4,produe_5,produe_6,produe_total,filter_awal,filter_akhir,no_coa,item_type1,item_type2,relasi) 
                        VALUES 
                    ('$suppin2', '$kbonin', '$tgl_kbonin', '$duedate2', '$currin2', '$sa_awal', '$tambah', '$tambahan', '$kurang', '$sa_akhir', '$saldo_akhir_idr', '$insert_date', '$due_current', '$due_1', '$due_2', '$due_3', '$due_4', '$due_5', '$due_6', '$due_7', '$tot_due', '$pro_due', '$pro_due0', '$pro_due1', '$pro_due2', '$pro_due3', '$pro_due4', '$pro_due5', '$tot_produe', '$start_date', '$end_date', '$no_coa', '$item_type1', '$item_type2', '$relasi')";

            $executein2 = mysqli_query($conn1,$queryin2);
            

}
}

?>