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
    header("Content-Disposition: attachment; filename=KartuAP_DetailKontrabon.xls");
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
            <th rowspan="2">Kontrabon Number</th>
            <th rowspan="2">Kontrabon Date</th>
            <th rowspan="2" >Due Date</th>
            <th rowspan="2">Currency</th>
            <th rowspan="2">Begining Balance</th>
            <th colspan="2">Addition</th>
            <th rowspan="2">Deduction</th>
            <th rowspan="2">Ending Balance</th>
            <th rowspan="2">no_coa</th>
            <th rowspan="2">nama_coa</th>
        </tr>
        <tr>
            <th>BPB</th>
            <th>Adjustment</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
        
        $data = mysqli_query($conn1,"select * from (select * from(
        (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,(b.jml_potong - a.dp_value) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d')  asc) 
            
        union (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,(b.jml_potong - a.dp_value) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d') asc)

        union (select a.nama_supp, a.no_kbon, a.tgl_kbon,DATE_FORMAT(b.create_date, '%Y-%m-%d') as create_date,DATEDIFF(b.tgl_tempo,b.tgl_kbon) as top, b.tgl_tempo, a.curr, a.total as bayar, a.total_bpb as total_ori, a.adjust as jml_potong from saldo_kbon_ap a left join kontrabon_h b on b.no_kbon = a.no_kbon)
            
        union (select a.nama_supp,a.no_kbon,c.tgl_kbon2 as tgl_kbon, DATE_FORMAT(c.create_date, '%Y-%m-%d') as create_date,DATEDIFF(c.tgl_tempo,c.tgl_kbon) as top, c.tgl_tempo,a.curr,(a.amount + a.pph_value) as bayar,(a.total_kbon + a.pph_value - b.jml_potong + c.dp_value) as totalori,(b.jml_potong - c.dp_value) as jml_potong  from list_payment a inner join potongan b on b.no_kbon = a.no_kbon left join kontrabon_h c on c.no_kbon = a.no_kbon where DATE_FORMAT(c.create_date, '%Y-%m-%d') < '$start_date' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and c.status !='CANCEL')
     ) as b GROUP BY no_kbon order by b.nama_supp asc) a left join 
         (select no_kbon no_journal, no_coa,nama_coa from kontrabon_h where no_coa != '') b on b.no_journal = a.no_kbon");


        $no = 1;
        $sa_akhir_ = 0;
        $kurang_ = 0;
        $sa_awal_ = 0;
        $tambah_ = 0;

        while($row = mysqli_fetch_array($data)){
            $namasupp = $row['nama_supp'];

    $start_date = date("Y-m-d",strtotime($_GET['start_date']));
    $tgl_kbon = $row['create_date'];
    $no_kbon = $row['no_kbon'];
    $bbayar = $row['bayar'];
    // $kurang = $row['bayar'];

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

    $sa_akhir = $sa_awal + ($tambah + $tambahan) - $kurang; 

    if($sa_awal == '0' and $tambah == '0' and $tambahan == '0' and $kurang == '0' and $sa_akhir == '0'){
        echo '';
    }else{


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




