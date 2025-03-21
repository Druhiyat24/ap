<html>
<head>
    <title>Export Data General Ledger </title>
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
    header("Content-Disposition: attachment; filename=Data Faktur Pajak.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

     ?>

        <h4>DATA FAKTUR PAJAK<br/> DARI <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">Nomor FP</th>
            <th style="text-align: center;vertical-align: middle;">Tanggal FP</th>
            <th style="text-align: center;vertical-align: middle;">Nomor BPB</th>
            <th style="text-align: center;vertical-align: middle;">Tanggal BPB</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Referensi</th>
            <th style="text-align: center;vertical-align: middle;">Nama Barang</th>
            <th style="text-align: center;vertical-align: middle;">Harga</th>
            <th style="text-align: center;vertical-align: middle;">Qty</th>
            <th style="text-align: center;vertical-align: middle;">DPP</th>
            <th style="text-align: center;vertical-align: middle;">Diskon</th>
            <th style="text-align: center;vertical-align: middle;">PPN</th>
            <th style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: center;vertical-align: middle;">Support Doc</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

  
        $sql = mysqli_query($conn2,"SELECT a.id,b.nama_supp, b.kd_no_faktur no_faktur, b.tgl_faktur,c.no_bpb,c.tgl_bpb,no_referensi,nama_item,price,qty,diskon,dpp,ppn,(dpp + ppn) total,'-' sup_doc FROM bpb_scan_faktur a inner join bpb_scan_faktur_h b on b.kd_no_faktur = a.kd_no_faktur inner join bpb_faktur_inv c on c.no_faktur = b.kd_no_faktur where b.tgl_faktur BETWEEN '$start_date' and '$end_date' order by no_faktur,no_bpb,id asc");

        //SELECT a.id,b.no_faktur, b.tgl_faktur,c.no_bpb,c.tgl_bpb,no_referensi,nama_item,price,qty,diskon,dpp,ppn,(dpp + ppn) total,'-' sup_doc FROM bpb_scan_faktur a inner join bpb_scan_faktur_h b on b.kd_no_faktur = a.kd_no_faktur inner join (select no_faktur, GROUP_CONCAT(DISTINCT no_bpb) no_bpb, GROUP_CONCAT(DISTINCT tgl_bpb) tgl_bpb from bpb_faktur_inv GROUP BY no_faktur) c on c.no_faktur = b.kd_no_faktur where b.tgl_faktur BETWEEN '$start_date' and '$end_date' GROUP BY id order by id asc
        // SELECT a.id,b.kd_no_faktur no_faktur, b.tgl_faktur,c.no_bpb,c.tgl_bpb,no_referensi,nama_item,price,qty,diskon,dpp,ppn,(dpp + ppn) total,'-' sup_doc FROM bpb_scan_faktur a inner join bpb_scan_faktur_h b on b.kd_no_faktur = a.kd_no_faktur inner join bpb_faktur_inv c on c.no_faktur = b.kd_no_faktur where b.tgl_faktur BETWEEN '$start_date' and '$end_date' order by no_faktur,no_bpb,id asc


$no = 0;
    while($row2 = mysqli_fetch_array($sql)){
        $no++;
        echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$row2['no_faktur'].'">'.$row2['no_faktur'].'</td>
            <td style="text-align : left;" value = "'.$row2['tgl_faktur'].'">'.date("d-M-Y",strtotime($row2['tgl_faktur'])).'</td>
            <td style="text-align : left;" value = "'.$row2['no_bpb'].'">'.$row2['no_bpb'].'</td>
            <td style="text-align : left;" value = "'.$row2['tgl_bpb'].'">'.$row2['tgl_bpb'].'</td>
            <td style="text-align : left;" value = "'.$row2['nama_supp'].'">'.$row2['nama_supp'].'</td>
            <td style="text-align : left;" value = "'.$row2['no_referensi'].'">'.$row2['no_referensi'].'</td>
            <td style="text-align : left;" value = "'.$row2['nama_item'].'">'.$row2['nama_item'].'</td>
            <td style="text-align : right;" value = "'.$row2['price'].'">'.number_format($row2['price'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['qty'].'">'.number_format($row2['qty'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['dpp'].'">'.number_format($row2['dpp'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['diskon'].'">'.number_format($row2['diskon'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ppn'].'">'.number_format($row2['ppn'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['total'].'">'.number_format($row2['total'],2).'</td>
            <td style="text-align : left;" value = "'.$row2['sup_doc'].'">'.$row2['sup_doc'].'</td>
            </tr>
            ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




