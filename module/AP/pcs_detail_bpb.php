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
    header("Content-Disposition: attachment; filename=KartuAP_DetailBPB.xls");
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
            <th>Bpb Number</th>
            <th>Bpb Date</th>
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

        $data = mysqli_query($conn1,"select * from (select * from(((select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '2022-04-14' and '$start_date' and b.tipe_sup != 'D' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com = 'REGULAR' and bpbdate between '2022-04-14' and '$start_date' and b.tipe_sup != 'D' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '2022-04-14' and '$start_date' and b.tipe_sup != 'D' group by a.bpbno_int order by bpbdate asc)union (
        select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '2022-04-14' and '$start_date' and c.tipe_sup != 'D' group by bppbno_int))
union

(select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '$start_date' and '$end_date' and b.tipe_sup != 'D' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com  = 'REGULAR' and bpbdate between '$start_date' and '$end_date' and b.tipe_sup != 'D' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '$start_date' and '$end_date' and b.tipe_sup != 'D' group by a.bpbno_int order by bpbdate asc) union (select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '$start_date' and '$end_date' and c.tipe_sup != 'D' group by bppbno_int)

union(select a.nama_supp, a.no_bpb, a.tgl_bpb,c.jml_pterms as top, DATE_ADD(b.bpbdate, INTERVAL c.jml_pterms DAY) as due_date, a.curr, a.total from saldo_bpb_ap a left join bpb b on b.bpbno_int = a.no_bpb INNER JOIN po_header c on c.pono = b.pono group by a.no_bpb)

union (select nama_supp, no_bpb, tgl_bpb, top, duedate, curr, total from tbl_tamb_bpb)
union (select nama_supp, no_bpb, tgl_bpb, top, duedate, curr, total from tbl_tamb_bpb2 where tgl_bpb between '$start_date' and '$end_date')) as b order by b.Supplier asc) a left join 

(select * from (select no_journal,no_coa,nama_coa from tbl_list_journal where credit != '' and type_journal = 'AP - BPB' and no_coa != '1.52.07' and (no_journal like '%/IN/%' OR no_journal like '%/RI/%')
union

select no_journal,no_coa,nama_coa from tbl_list_journal where debit != '' and type_journal = 'AP - BPB RETURN' and no_coa != '1.52.07' and (no_journal like '%/OUT/%' OR no_journal like '%/RO/%')) a) b on b. no_journal = a.bpbno_int");
        
        $no = 1;
        $sa_akhir_ = 0;
        $kurang_ = 0;
        $sa_awal_ = 0;
        $tambah_ = 0;

        while($row = mysqli_fetch_array($data)){
            $namasupp = $row['Supplier'];

            $start_date = date("Y-m-d",strtotime($_GET['start_date']));
    $tgl_bpb = $row['bpbdate'];
    $no_bpb = $row['bpbno_int'];
    $bbayar = $row['total'];
    $currin = $row['curr'];

     $sqldate = mysqli_query($conn1,"select a.bppbno_int,b.jml_pterms as top,DATE_ADD(a.bppbdate, INTERVAL b.jml_pterms DAY) as due_date from bppb a left join bpb d on d.bpbno = a.bpbno_ro left JOIN po_header b on b.pono = d.pono inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.bppbno_int = '$no_bpb' and a.bpbno_ro != '' group by bppbno_int");
    $rowdate = mysqli_fetch_array($sqldate);
    $bppbno_int = isset($rowdate['bppbno_int']) ? $rowdate['bppbno_int'] : null;
    $due_date = isset($rowdate['due_date']) ? $rowdate['due_date'] : null;
    $jml_pterms = isset($rowdate['jml_pterms']) ? $rowdate['jml_pterms'] : null;

    if ($bbayar > 0) {
    if ($no_bpb == 'WIP/IN/0522/01805') {
           $jml_tax = 11;
        }else{
    $jml_tax = 0;
}
    $sqllp = mysqli_query($conn1,"select a.no_bpb,a.tgl_bpb from kontrabon a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bpb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and a.status != 'Cancel' GROUP BY a.no_bpb
        union
select no_doc, tgl_doc from tbl_tamb_ap where no_doc = '$no_bpb' and tgl_pay between '$start_date' and '$end_date' GROUP BY no_doc");
    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['no_bpb']) ? $rowlp['no_bpb'] : null;


    $sqllp2 = mysqli_query($conn1,"select a.no_bpb,a.tgl_bpb,d.tgl_kbon2 from kontrabon a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bpb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') < '$start_date' and a.status != 'Cancel' GROUP BY a.no_bpb
        union
select no_doc, tgl_doc, tgl_doc tgl_doc2 from tbl_tamb_ap where no_doc = '$no_bpb' and tgl_pay < '$start_date' GROUP BY no_doc");
    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['no_bpb']) ? $rowlp2['no_bpb'] : null;
    $tgl = isset($rowlp2['tgl_kbon2']) ? $rowlp2['tgl_kbon2'] : null;
}else{
    if ($no_bpb == 'GEN/RO/0722/00606' || $no_bpb == 'GEN/RO/0722/00623') {
     $jml_tax = 0;  
    }else{
    $sqltax = mysqli_query($conn1,"select a.bppbno_int, IF(po_header.tax is null,0,po_header.tax) as tax from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier INNER JOIN masteritem on masteritem.id_item = a.id_item right join bpb on bpb.bpbno = a.bpbno_ro left JOIN po_header on po_header.pono = bpb.pono where a.bppbno_int = '$no_bpb' GROUP BY a.bppbno_int");
    $rowtax = mysqli_fetch_array($sqltax);
    $jml_tax = isset($rowtax['tax']) ? $rowtax['tax'] : 0;
    }

    $sqllp = mysqli_query($conn1,"select a.no_bppb,a.tgl_bppb from bppb_new a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bppb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and a.status != 'Cancel' GROUP BY a.no_bppb
        union
select no_doc, tgl_doc from tbl_tamb_ap where no_doc = '$no_bpb' and tgl_pay between '$start_date' and '$end_date' GROUP BY no_doc");
    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['no_bppb']) ? $rowlp['no_bppb'] : null;

    $sqllp2 = mysqli_query($conn1,"select a.no_bppb,a.tgl_bppb,d.tgl_kbon2 from bppb_new a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bppb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') < '$start_date' and a.status != 'Cancel' GROUP BY a.no_bppb
        union
select no_doc, tgl_doc, tgl_doc tgl_doc2 from tbl_tamb_ap where no_doc = '$no_bpb' and tgl_pay < '$start_date' GROUP BY no_doc");
    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['no_bppb']) ? $rowlp2['no_bppb'] : null;
    $tgl = isset($rowlp2['tgl_kbon2']) ? $rowlp2['tgl_kbon2'] : null;
}

    if($bppbno_int != null){
        $due_date_h = $due_date;
        $top_h = $jml_pterms;
    }else{
        $due_date_h = $row['due_date'];
        $top_h = $row['top'];
    }

    if($no_lp != null){
        $kurang = ($row['total'] + ($row['total'] * $jml_tax/100));
    }else{
        $kurang = 0;
    }

    if($no_lp2 != null){
        $bayar = ($row['total'] + ($row['total'] * $jml_tax/100));
    }else{
        $bayar = 0;
    }

    if($tgl_bpb < $start_date){
        $sa_awal = ($row['total'] + ($row['total'] * $jml_tax/100)) - $bayar;
    }
        else{
        $sa_awal = 0;
    }


    if($tgl_bpb >= $start_date and $tgl < $start_date){
        $tambah = ($row['total'] + ($row['total'] * $jml_tax/100)) - $bayar;
    }elseif($tgl_bpb >= $start_date){
        $tambah = ($row['total'] + ($row['total'] * $jml_tax/100));
    }else{
        $tambah = 0;
    }


    if ($currin == 'IDR') {
        $rate = 1;
    }else{
        $rate = $jml_rate;
    }


    $sa_akhir = $sa_awal + $tambah - $kurang; 
    $saldo_akhir_idr = $sa_akhir * $rate;

    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;
    if($sa_awal == '0' and $tambah == '0' and $kurang == '0' and $sa_akhir == '0'){
        echo '';
    }else{
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td value = "'.$row['Supplier'].'">'.$row['Supplier'].'</td>
            <td value="'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>
            <td value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td> 
            <td value="'.$due_date_h.'">'.date("d-M-Y",strtotime($due_date_h)).'</td>                          
            <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
            <td style="text-align:right;" value = "'.$sa_awal.'">'.$sa_awal.'</td>
            <td style="text-align:right;" value = "'.$tambah.'">'.$tambah.'</td>         
            <td style="text-align:right;" value = "'.$kurang.'">'.$kurang.'</td>
            <td style="text-align:right;" value = "'.$sa_akhir.'">'.$sa_akhir.'</td>
            <td style="text-align:right;" value="'.$rate.'">'.$rate.'</td>
            <td style="text-align:right;" value="'.$saldo_akhir_idr.'">'.$saldo_akhir_idr.'</td>
            <td value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
            <td value="'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
             ';
            
        // }

}
        ?>
        <?php 
        }
        // echo '
        //     <tr >
        //     <th colspan = "6" style="text-align: center;vertical-align: middle;">Total</th>
        //     <th style="text-align: right;vertical-align: middle;">'.number_format($sa_awal_,2).'</th>
        //     <th style="text-align: right;vertical-align: middle;">'.number_format($tambah_,2).'</th>
        //     <th style="text-align: right;vertical-align: middle;">'.number_format($kurang_,2).'</th>
        //     <th style="text-align: right;vertical-align: middle;">'.number_format($sa_akhir_,2).'</th>                                                                            
        // </tr>';
        ?>
    </table>

</body>
</html>




