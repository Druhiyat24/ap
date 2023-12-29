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
    header("Content-Disposition: attachment; filename=KartuAP_Global.xls");
    $nama_supp=$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>PAYABLE CARD STATEMENT <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th rowspan="2" style="text-align: center;">No </th>
            <th rowspan="2" style="text-align: center;">Supplier </th>
            <th colspan="4" style="text-align: center;">Foreign Currency</th>
            <th  colspan="4" style="text-align: center;">Equivalent IDR</th>                                          
        </tr>
         <tr>
            <th style="text-align: center;">Begining Balance</th>
            <th style="text-align: center;">Addition</th>
            <th style="text-align: center;">Deduction</th>
            <th style="text-align: center;">Ending Balance</th>
            <th style="text-align: center;">Begining Balance</th>
            <th style="text-align: center;">Addition</th>
            <th style="text-align: center;">Deduction</th>
            <th style="text-align: center;">Ending Balance</th> 
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
        if($nama_supp = 'ALL'){
        $data = mysqli_query($conn1,"select * from 
(select Supplier as namasupplier from mastersupplier where tipe_sup = 'S') ms
left join 

(select supplier,tgl_pay,sum(if(valuta = 'USD',total ,'0')) as totalusd,sum(if(valuta = 'IDR',total ,total * (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pay))) as totalidr, valuta from saldo_awal  GROUP BY supplier) a on ms.namasupplier = a.supplier

left join 
(select b.Supplier, if(curr = 'USD', round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2),0) as bpbusd, if(curr = 'IDR', round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2),round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * (a.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate)))) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * (a.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate)))) * (c.tax /100)))),2)) as bpbidr from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.confirm = 'y' and status_retur = 'N' and bpbdate between '$start_date' and '$end_date' group by a.Id_Supplier
 ) c
on ms.namasupplier = c.Supplier

left join 
(select a.nama_supp as Supplier,sum(a.jml_potong) potongori, if(jml_potong > '0',if(b.curr = 'USD',sum(a.jml_potong),0),0) as plususd,if(jml_potong < '0',if(b.curr = 'USD',sum(a.jml_potong),0),0) as minusd, if(jml_potong > '0',if(b.curr = 'IDR',sum(a.jml_potong),sum(a.jml_potong * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon)))),0) as plusidr, if(jml_potong < '0',if(b.curr = 'IDR',sum(a.jml_potong),sum(a.jml_potong * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon)))),0) as minidr  from potongan a INNER JOIN kontrabon_h b on b.no_kbon = a.no_kbon where jml_potong != '0' and a.tgl_kbon between '$start_date' and '$end_date' GROUP BY a.nama_supp
 ) d
on ms.namasupplier = d.Supplier

left join 
(select  mastersupplier.Supplier as Supplier, bppb.curr as curr, round(sum(bppb.qty * bppb.price),2) as rtnori, round(if(bppb.curr = 'IDR',sum(bppb.qty * bppb.price),sum(bppb.qty * (bppb.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = bppb.bppbdate ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = bppb.bppbdate ))))),2) as rtnidr from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier where cancel != 'Y' and bppb.bpbno_ro != '' and bppb.bppbdate between '$start_date' and '$end_date' group by mastersupplier.Supplier
 ) e
on ms.namasupplier = e.Supplier


left join 
(select nama_supp, if(valuta_bayar = 'IDR',sum(nominal),sum(nominal_fgn * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pelunasan ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pelunasan )))) as nominal, sum(nominal_fgn) as nominal_fgn from payment_ftr where tgl_pelunasan between '$start_date' and '$end_date' GROUP BY nama_supp
 ) f
on ms.namasupplier = f.nama_supp");
    }else{
       $data = mysqli_query($conn1,"select * from 
(select Supplier as namasupplier from mastersupplier where tipe_sup = 'S' and Supplier = '$nama_supp') ms
left join 

(select supplier,tgl_pay,sum(if(valuta = 'USD',total ,'0')) as totalusd,sum(if(valuta = 'IDR',total ,total * (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pay))) as totalidr, valuta from saldo_awal  and supplier = '$nama_supp' GROUP BY supplier) a on ms.namasupplier = a.supplier

left join 
(select b.Supplier, if(curr = 'USD', round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2),0) as bpbusd, if(curr = 'IDR', round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2),round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * (a.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate)))) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * (a.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.bpbdate)))) * (c.tax /100)))),2)) as bpbidr from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.confirm = 'y' and status_retur = 'N' and b.Supplier = '$nama_supp' and bpbdate between '$start_date' and '$end_date' group by a.Id_Supplier
 ) c
on ms.namasupplier = c.Supplier


left join 
(select a.nama_supp as Supplier,sum(a.jml_potong) potongori, if(jml_potong > '0',if(b.curr = 'USD',sum(a.jml_potong),0),0) as plususd,if(jml_potong < '0',if(b.curr = 'USD',sum(a.jml_potong),0),0) as minusd, if(jml_potong > '0',if(b.curr = 'IDR',sum(a.jml_potong),sum(a.jml_potong * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon)))),0) as plusidr, if(jml_potong < '0',if(b.curr = 'IDR',sum(a.jml_potong),sum(a.jml_potong * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = a.tgl_kbon)))),0) as minidr  from potongan a INNER JOIN kontrabon_h b on b.no_kbon = a.no_kbon where jml_potong != '0' and a.nama_supp = '$nama_supp' and a.tgl_kbon between '$start_date' and '$end_date' GROUP BY a.nama_supp
 ) d
on ms.namasupplier = d.Supplier

left join 
(select  mastersupplier.Supplier as Supplier, bppb.curr as curr, round(sum(bppb.qty * bppb.price),2) as rtnori, round(if(bppb.curr = 'IDR',sum(bppb.qty * bppb.price),sum(bppb.qty * (bppb.price * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = bppb.bppbdate ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = bppb.bppbdate ))))),2) as rtnidr from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier where cancel != 'Y' and bppb.bpbno_ro != '' and mastersupplier.Supplier = '$nama_supp' and bppb.bppbdate between '$start_date' and '$end_date' group by mastersupplier.Supplier
 ) e
on ms.namasupplier = e.Supplier

left join 
(select nama_supp, if(valuta_bayar = 'IDR',sum(nominal),sum(nominal_fgn * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pelunasan ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_pelunasan )))) as nominal, sum(nominal_fgn) as nominal_fgn from payment_ftr where tgl_pelunasan between '$start_date' and '$end_date' and nama_supp = '$nama_supp' GROUP BY nama_supp
 ) f
on ms.namasupplier = f.nama_supp"); 
    }

        $no = 1;
        $totalusd = 0;
        $totalidr = 0;
        $total_bpbidr = 0;
        $total_bpbusd = 0;
        $total_rtnidr = 0;
        $total_rtnusd = 0;
        $plususd = 0;
        $minusd = 0;
        $plusidr = 0;
        $minidr = 0;
        $nom = 0;
        $nom_fgn = 0;
        $nominal = 0;
        $nominal_fgn = 0;
        $penambahanusd = 0;
        $penambahanidr = 0;
        $balanceusd = 0;
        $balanceidr = 0;

        while($row = mysqli_fetch_array($data)){
    $namasupp = isset($row['namasupplier']) ? $row['namasupplier'] : null; 
    $totalusd = isset($row['totalusd']) ? $row['totalusd'] : 0; 
    $totalidr = isset($row['totalidr']) ? $row['totalidr'] : 0;

    $total_bpbidr = isset($row['bpbidr']) ? $row['bpbidr'] : 0;
    $total_bpbusd = isset($row['bpbusd']) ? $row['bpbusd'] : 0;

    $total_rtnidr = isset($row['rtnidr']) ? $row['rtnidr'] : 0;
    $total_rtnusd = isset($row['rtnori']) ? $row['rtnori'] : 0;

    $plususd = isset($row['plususd']) ? $row['plususd'] : 0; 
    $minusd = isset($row['minusd']) ? abs($row['minusd']) : 0;
    $plusidr = isset($row['plusidr']) ? $row['plusidr'] : 0;
    $minidr = isset($row['minidr']) ? abs($row['minidr']) : 0;

    $nom = isset($row['nominal']) ? $row['nominal'] : 0;
    $nom_fgn = isset($row['nominal_fgn']) ? $row['nominal_fgn'] : 0; 

    $nominal = $nom + $minidr + $total_rtnidr;
    $nominal_fgn = $nom_fgn + $minusd + $total_rtnusd; 

    $penambahanusd = $total_bpbusd + $plususd;
    $penambahanidr = $total_bpbidr + $plusidr;
    $balanceusd = $penambahanusd + $totalusd - $nominal_fgn;
    $balanceidr = $penambahanidr + $totalidr - $nominal;

        if($totalusd == '0' and $nominal_fgn == '0' and $penambahanusd == '0' and $totalidr == '0' and $nominal == '0' and $penambahanidr == '0'){
        echo '';
    }else{
        echo '<tr style="font-size:12px;text-align:center;">
            <td style="text-align: center;">'.$no++.'</td>
            <td style="text-align: left;" value = "'.$namasupp.'">'.$namasupp.'</td>
            <td style="text-align:right;" value = "'.$totalusd.'">'.number_format($totalusd,2).'</td>
            <td style="text-align:right;" value = "'.$penambahanusd.'">'.number_format($penambahanusd,2).'</td>         
            <td style="text-align:right;" value = "'.$nominal_fgn.'">'.number_format($nominal_fgn,2).'</td>
            <td style="text-align:right;" value = "'.$balanceusd.'">'.number_format($balanceusd,2).'</td>
            <td style="text-align:right;" value = "'.$totalidr.'">'.number_format($totalidr,2).'</td>
            <td style="text-align:right;" value = "'.$penambahanidr.'">'.number_format($penambahanidr,2).'</td>
            <td style="text-align:right;" value = "'.$nominal.'">'.number_format($nominal,2).'</td>         
            <td style="text-align:right;" value = "'.$balanceidr.'">'.number_format($balanceidr,2).'</td>
             ';
         }
        ?>
        <?php 
        }
        ?>
    </table>

</body>
</html>




