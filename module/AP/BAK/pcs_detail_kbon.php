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
            <th rowspan="2">Currency</th>
            <th rowspan="2">Begining Balance</th>
            <th colspan="2">Addition</th>
            <th rowspan="2">Deduction</th>
            <th rowspan="2">Ending Balance</th>
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


       $data = mysqli_query($conn1,"select * from((select a.nama_supp, a.no_kbon, a.tgl_kbon,a.curr, if(a.balance = '0',if(a.curr = 'USD', sum(a.total + a.pph_fgn),sum(a.total + a.pph_idr)),0) as bayar, if(a.curr = 'USD', sum(a.total + a.pph_fgn - b.jml_potong),sum(a.total + a.pph_idr - b.jml_potong)) as totalori, if(a.curr = 'USD', sum(b.jml_potong),sum(b.jml_potong)) as jml_potong,if(a.curr = 'USD', sum(a.total + a.pph_fgn),0) as total_sdh_kbusd , if(a.curr = 'IDR', sum(a.total + a.pph_idr), sum((a.total + a.pph_fgn) * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon )))) as total_sdh_kbidr from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and a.nama_supp = '$nama_supp' and a.tgl_kbon between '$start_date' and '$end_date' GROUP BY a.no_kbon order by a.tgl_kbon asc) union (select a.nama_supp,a.no_kbon,a.tgl_kbon,a.curr,(a.amount + a.pph_value) as bayar,(a.total_kbon + a.pph_value - b.jml_potong) as totalori, b.jml_potong ,'0' as total_sdh_kbusd, '0' as total_sdh_kbidr from list_payment a inner join potongan b on b.no_kbon = a.no_kbon where a.tgl_kbon < '$start_date' and a.tgl_payment between '$start_date' and '$end_date' and a.nama_supp = '$nama_supp')) as b GROUP BY no_kbon order by b.nama_supp asc"); 
    

        $no = 1;
        $sa_akhir_ = 0;
        $kurang_ = 0;
        $sa_awal_ = 0;
        $tambah_ = 0;

        while($row = mysqli_fetch_array($data)){
            $namasupp = $row['nama_supp'];

            $start_date = date("Y-m-d",strtotime($_GET['start_date']));
    $tgl_kbon = $row['tgl_kbon'];

    $kurang = $row['bayar'];

    if($tgl_kbon < $start_date){
        $sawal = $row['totalori'];
    $tamhan = $row['jml_potong'];
    $sa_awal = $sawal + $tamhan;
    }else{
        $sa_awal = 0;
    }

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

    $sa_akhir = $sa_awal + ($tambah + $tambahan) - $kurang; 
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;


        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align:left;" value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style="text-align:left;" value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
            <td style="text-align:left;" value = "'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
            <td style="text-align:left;" value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="text-align:right;" value = "'.$sa_awal.'">'.number_format($sa_awal,2).'</td>
            <td style="text-align:right;" value = "'.$tambah.'">'.number_format($tambah,2).'</td> 
            <td style="text-align:right;" value = "'.$tambah.'">'.number_format($tambahan,2).'</td>         
            <td style="text-align:right;" value = "'.$kurang.'">'.number_format($kurang,2).'</td>
            <td style="text-align:right;" value = "'.$sa_akhir.'">'.number_format($sa_akhir,2).'</td>
             ';
        ?>
        <?php 
        }
        ?>
    </table>

</body>
</html>




