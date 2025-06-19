<html>
<head>
    <title>Export Data PV </title>
</head>
<body>
    <style type="text/css">
        body {
          font-family: Calibri, Arial, sans-serif;
      }
      h4 {
          font-family: Calibri, Arial, sans-serif;
      }
      table{
        margin: 20px auto;
        border-collapse: collapse;
    }
    table, th, td {
        font-family: Calibri, Arial, sans-serif;
        font-size: 12px;
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
header("Content-Disposition: attachment; filename=List payment voucher.xls");
$start_date = date("d F Y",strtotime($_GET['start_date']));
$end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

<h4>DATA PAYMENT VOUCHER <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

<table style="width:100%;font-size:12px;" border="1" >
    <tr>
        <th style="text-align: center;vertical-align: middle;">No</th>
        <th style="text-align: center;vertical-align: middle;">No Payment Voucher</th>
        <th style="text-align: center;vertical-align: middle;">Payment Voucher Date</th>
        <th style="text-align: center;vertical-align: middle;">Payment To</th>
        <th style="text-align: center;vertical-align: middle;">For Payment</th>
        <th style="text-align: center;vertical-align: middle;">Supporting Doc</th>
        <th style="text-align: center;vertical-align: middle;">Charge To Buyer</th>
        <th style="text-align: center;vertical-align: middle;">Payment Date</th>
        <th style="text-align: center;vertical-align: middle;">Payment Method</th>
        <th style="text-align: center;vertical-align: middle;">Curreny</th>
        <th style="text-align: center;vertical-align: middle;">From Account</th>
        <th style="text-align: center;vertical-align: middle;">To Account</th>
        <th style="text-align: center;vertical-align: middle;">No Cheque</th>
        <th style="text-align: center;vertical-align: middle;">Cheque Date</th>
        <th style="text-align: center;vertical-align: middle;">Reff Doc</th>
        <th style="text-align: center;vertical-align: middle;">Reff Doc Date</th>
        <th style="text-align: center;vertical-align: middle;">Total Without Tax</th>
        <th style="text-align: center;vertical-align: middle;">Incoming Tax</th>
        <th style="text-align: center;vertical-align: middle;">Value Added Tax</th>
        <th style="text-align: center;vertical-align: middle;">Grand Total</th>
        <th style="text-align: center;vertical-align: middle;">Outstanding</th>
        <th style="text-align: center;vertical-align: middle;">Description</th>
        <th style="text-align: center;vertical-align: middle;">Status</th>
        <th style="text-align: center;vertical-align: middle;">Created By</th>
        <th style="text-align: center;vertical-align: middle;">Created Date</th>
        <th style="text-align: center;vertical-align: middle;">Approve By</th>
        <th style="text-align: center;vertical-align: middle;">Approve Date</th>
        <?php 
        include '../../conn/conn.php';
        $where =$_GET['where'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

        $sql = mysqli_query($conn2,"select a.no_pv, pv_date, nama_supp, for_pay, supp_doc, ctb, pay_date, pay_meth, curr, frm_akun, to_akun, IFNULL(no_cek,'-') no_cek, IF(no_cek = '' ,'-',cek_date) cek_date, reff_doc, reff_date, (subtotal + adjust) subtotal, pph, ppn, total, outstanding, deskripsi, status, create_by, create_date, approve_by, approve_date from tbl_pv_h a LEFT JOIN (SELECT no_pv, GROUP_CONCAT(DISTINCT reff_doc ORDER BY id ASC SEPARATOR ', ') AS reff_doc, GROUP_CONCAT(DISTINCT CASE WHEN reff_date = '1970-01-01' THEN '-' ELSE reff_date END ORDER BY id ASC SEPARATOR ', ') AS reff_date FROM ( SELECT no_pv, IF(reff_doc = '' OR reff_doc = '-', '-', reff_doc) AS reff_doc, IF(reff_date = '1970-01-01', '-', reff_date) AS reff_date, id FROM tbl_pv WHERE reff_doc != '-' and reff_doc != '' GROUP BY no_pv, reff_doc, reff_date, id ) a GROUP BY no_pv) b on b.no_pv = a.no_pv $where");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){
            $approve_date = isset($row['approve_date']) ? $row['approve_date'] : null;
            $cekdate = isset($row['cek_date']) ? $row['cek_date'] : null;

            if ($approve_date == null) {
                $app_date = '-'; 
                $app_by = '-'; 
            }else{
                $app_date = date("d-M-Y",strtotime($approve_date));
                $app_by = $row['approve_by']; 
            }

            if ($cekdate == '-') {
                $cek_date = '-'; 
                $no_cek = '-'; 
            }else{
                $cek_date = date("d-M-Y",strtotime($cekdate));
                $no_cek = $row['no_cek']; 
            }

            echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style=" text-align : left" value="'.$row['no_pv'].'">'.$row['no_pv'].'</td>
            <td style=" text-align : left" value="'.$row['pv_date'].'">'.date("d-M-Y",strtotime($row['pv_date'])).'</td>
            <td style=" text-align : left" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style=" text-align : left" value="'.$row['for_pay'].'">'.$row['for_pay'].'</td>
            <td style=" text-align : left" value="'.$row['supp_doc'].'">'.$row['supp_doc'].'</td>
            <td style=" text-align : left" value="'.$row['ctb'].'">'.$row['ctb'].'</td>
            <td style=" text-align : left" value="'.$row['pay_date'].'">'.date("d-M-Y",strtotime($row['pay_date'])).'</td>
            <td style=" text-align : left" value="'.$row['pay_meth'].'">'.$row['pay_meth'].'</td>
            <td style=" text-align : left" value="'.$row['curr'].'">'.$row['curr'].'</td>
            <td style=" text-align : left" value="'.$row['frm_akun'].'">'.$row['frm_akun'].'</td>
            <td style=" text-align : left" value="'.$row['to_akun'].'">'.$row['to_akun'].'</td>
            <td style=" text-align : left" value="'.$no_cek.'">'.$no_cek.'</td>
            <td style=" text-align : left" value="'.$cek_date.'">'.$cek_date.'</td>
            <td style=" text-align : left" value="'.$row['reff_doc'].'">'.$row['reff_doc'].'</td>
            <td style=" text-align : left" value="'.$row['reff_date'].'">'.$row['reff_date'].'</td>
            <td style=" text-align : right" value="'.$row['subtotal'].'">'.number_format($row['subtotal'],2).'</td>
            <td style=" text-align : right" value="'.$row['pph'].'">'.number_format($row['pph'],2).'</td>
            <td style=" text-align : right" value="'.$row['ppn'].'">'.number_format($row['ppn'],2).'</td>
            <td style=" text-align : right" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>
            <td style=" text-align : right" value="'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td>
            <td style=" text-align : left" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
            <td style=" text-align : left" value="'.$row['status'].'">'.$row['status'].'</td>
            <td style=" text-align : left" value="'.$row['create_by'].'">'.$row['create_by'].'</td>
            <td style=" text-align : left" value="'.$row['create_date'].'">'.$row['create_date'].'</td>
            <td style=" text-align : left" value="'.$app_by.'">'.$app_by.'</td>
            <td style=" text-align : left" value="'.$app_date.'">'.$app_date.'</td>
            </tr> ';

            ?>
            <?php 

        }
        ?>
    </table>

</body>
</html>




