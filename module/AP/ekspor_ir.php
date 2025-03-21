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
    header("Content-Disposition: attachment; filename=invoiced received report.xls");
    $nama_supp =$_GET['nama_supp'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

        <h4>DATA RECEIVED INVOICE <?php echo $nama_supp; ?><br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">Supplier Invoice No</th>
            <th style="text-align: center;vertical-align: middle;">No Kontrabon</th>
            <th style="text-align: center;vertical-align: middle;">No Reff</th>
            <th style="text-align: center;vertical-align: middle;">Supplier Name</th>
            <th style="text-align: center;vertical-align: middle;">Amount</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">SI Date</th>
            <th style="text-align: center;vertical-align: middle;">IR Date</th>
            <th style="text-align: center;vertical-align: middle;">BPB Date</th>
            <th style="text-align: center;vertical-align: middle;">BPB Create</th>
            <th style="text-align: center;vertical-align: middle;">BPB-A Date</th>
            <th style="text-align: center;vertical-align: middle;">BPB-R Date</th>
            <th style="text-align: center;vertical-align: middle;">FTA Date</th>
            <th style="text-align: center;vertical-align: middle;">ARF Date</th>
            <th style="text-align: center;vertical-align: middle;">ATP Date</th>
            <th style="text-align: center;vertical-align: middle;">PRA Date</th>
            <th style="text-align: center;vertical-align: middle;">PTF Date</th>
            <th style="text-align: center;vertical-align: middle;">FRP Date</th>
            <th style="text-align: center;vertical-align: middle;">KB Date</th>
            <th style="text-align: center;vertical-align: middle;">LP Date</th>
            <th style="text-align: center;vertical-align: middle;">LP Approve Date</th>
            <th style="text-align: center;vertical-align: middle;">PAY Date</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
  

        $sql = mysqli_query($conn2,"select * from (select no_invoice,COALESCE(no_reff,'-') no_reff,a.doc_number,b.nama_supp,a.amount,b.status,tgl_invoice,tgl_penerimaan,tfta_date,receive_acc_date,tatp_date,receive_pch_date,tptf_date,receive_fin_date from ir_invoice_supp a inner join ir_invoice_supp_h b on b.doc_number = a.doc_number where tgl_invoice between '$start_date' and '$end_date' group by a.id) a left join (select max(bpbdate) bpbdate,max(dateinput) dateinput,max(confirm_date) confirm_date,upt_no_inv,max(trf_date) trf_date,b.supplier from bpb a inner join mastersupplier b on b.id_supplier  = a.id_supplier where upt_no_inv is not null and upt_no_inv != '-' GROUP BY upt_no_inv,supplier) b on b.upt_no_inv = a.no_invoice and b.supplier = a.nama_supp left join
(select supplier supp,upt_no_inv,MAX(tgl_kbon) tgl_kbon,MAX(tgl_payment) tgl_payment,MAX(tgl_approve_lp) tgl_approve_lp,MAX(bankout_date) bankout_date from (select a.*,b.no_kbon,b.tgl_kbon,b.create_date created_kbon,c.no_payment,c.tgl_payment,c.create_date created_lp,DATE_FORMAT(c.confirm_date, '%Y-%m-%d') tgl_approve_lp, d.no_bankout, d.bankout_date  from (select no_bpb,supplier,upt_no_inv from bpb_new where upt_no_inv is not null and upt_no_inv != '-' GROUP BY no_bpb) a left join 
(select * from kontrabon where status != 'Cancel') b on b.no_bpb = a.no_bpb left join 
(select * from list_payment where status != 'Cancel') c on c.no_kbon = b.no_kbon left join 
(select * from (select a.no_bankout,bankout_date,no_reff from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where no_reff like '%LP/NAG%' and b.status != 'Cancel'
UNION
select a.no_pco,b.tgl_pco,no_reff from c_petty_cashout_det a inner join c_petty_cashout_h b on a.no_pco = b.no_pco where no_reff like '%LP/NAG%' and b.status != 'Cancel'
UNION
select payment_ftr_id,tgl_pelunasan,list_payment_id from payment_ftr where list_payment_id like '%LP/NAG%' and status != 'Cancel') a GROUP BY no_reff order by bankout_date desc) d on d.no_reff = c.no_payment GROUP BY no_bpb) a GROUP BY supplier,upt_no_inv) c on c.upt_no_inv = a.no_invoice and c.supp = a.nama_supp");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

            if ($row['tfta_date'] == null || $row['tfta_date'] == '') { $tfta_date = '-'; }else{ $tfta_date = date("d-M-Y",strtotime($row['tfta_date']));}

        if ($row['receive_acc_date'] == null || $row['receive_acc_date'] == '') { $receive_acc_date = '-'; }else{ $receive_acc_date = date("d-M-Y",strtotime($row['receive_acc_date']));}

        if ($row['tatp_date'] == null || $row['tatp_date'] == '') { $tatp_date = '-'; }else{ $tatp_date = date("d-M-Y",strtotime($row['tatp_date']));}

        if ($row['receive_pch_date'] == null || $row['receive_pch_date'] == '') { $receive_pch_date = '-'; }else{ $receive_pch_date = date("d-M-Y",strtotime($row['receive_pch_date']));}

        if ($row['tptf_date'] == null || $row['tptf_date'] == '') { $tptf_date = '-'; }else{ $tptf_date = date("d-M-Y",strtotime($row['tptf_date']));}

        if ($row['receive_fin_date'] == null || $row['receive_fin_date'] == '') { $receive_fin_date = '-'; }else{ $receive_fin_date = date("d-M-Y",strtotime($row['receive_fin_date']));}

        if ($row['bpbdate'] == null || $row['bpbdate'] == '') { $bpbdate = '-'; }else{ $bpbdate = date("d-M-Y",strtotime($row['bpbdate']));}

        if ($row['confirm_date'] == null || $row['confirm_date'] == '') { $confirm_date = '-'; }else{ $confirm_date = date("d-M-Y",strtotime($row['confirm_date']));}

        if ($row['trf_date'] == null || $row['trf_date'] == '') { $trf_date = '-'; }else{ $trf_date = date("d-M-Y",strtotime($row['trf_date']));}

        if ($row['tgl_kbon'] == null || $row['tgl_kbon'] == '') { $tgl_kbon = '-'; }else{ $tgl_kbon = date("d-M-Y",strtotime($row['tgl_kbon']));}

        if ($row['tgl_payment'] == null || $row['tgl_payment'] == '') { $tgl_payment = '-'; }else{ $tgl_payment = date("d-M-Y",strtotime($row['tgl_payment']));}

        if ($row['bankout_date'] == null || $row['bankout_date'] == '') { $bankout_date = '-'; }else{ $bankout_date = date("d-M-Y",strtotime($row['bankout_date']));}

        if ($row['dateinput'] == null || $row['dateinput'] == '') { $dateinput = '-'; }else{ $dateinput = date("d-M-Y",strtotime($row['dateinput']));}

        if ($row['tgl_approve_lp'] == null || $row['tgl_approve_lp'] == '') { $tgl_approve_lp = '-'; }else{ $tgl_approve_lp = date("d-M-Y",strtotime($row['tgl_approve_lp']));}

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align: left;" value = "'.$row['no_invoice'].'">'.$row['no_invoice'].'</td>
            <td style="text-align: left;" value = "'.$row['doc_number'].'">'.$row['doc_number'].'</td>
            <td value = "'.$row['no_reff'].'">'.$row['no_reff'].'</td>
            <td style="text-align: left;" value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style="text-align: right;" value = "'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
            <td style="text-align: left;" value = "'.$row['status'].'">'.$row['status'].'</td>
            <td value = "'.$row['tgl_invoice'].'">'.date("d-M-Y",strtotime($row['tgl_invoice'])).'</td>
            <td value = "'.$row['tgl_penerimaan'].'">'.date("d-M-Y",strtotime($row['tgl_penerimaan'])).'</td>
            <td value = "">'.$bpbdate.'</td>
            <td value = "">'.$dateinput.'</td>
            <td value = "">'.$confirm_date.'</td>
            <td value = "">'.$trf_date.'</td>
            <td value = "'.$tfta_date.'">'.$tfta_date.'</td>
            <td value = "'.$receive_acc_date.'">'.$receive_acc_date.'</td>
            <td value = "'.$tatp_date.'">'.$tatp_date.'</td>
            <td value = "'.$receive_pch_date.'">'.$receive_pch_date.'</td>
            <td value = "'.$tptf_date.'">'.$tptf_date.'</td>
            <td value = "'.$receive_fin_date.'">'.$receive_fin_date.'</td>
            <td value = "'.$tgl_kbon.'">'.$tgl_kbon.'</td>
            <td value = "'.$tgl_payment.'">'.$tgl_payment.'</td>
            <td value = "'.$tgl_approve_lp.'">'.$tgl_approve_lp.'</td>
            <td value = "'.$bankout_date.'">'.$bankout_date.'</td>
            </tr>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




