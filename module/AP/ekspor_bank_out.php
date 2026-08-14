<html>
<head>
    <title>Export Data List Journal </title>
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
    header("Content-Disposition: attachment; filename=list-bank-out.xls");
    // $nama_supp =$_GET['nama_supp'];
    // $status =$_GET['status'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

   <!--  <center> -->
        <h4>DATA BANK OUT <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    <!-- </center> -->
  <!--   STATUS: <?php echo $status; ?> -->
 
    <table style="width:100%;font-size:12px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Bank Out</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">Source</th>
            <th style="text-align: center;vertical-align: middle;">Cash Flow Category</th>
            <th style="text-align: center;vertical-align: middle;">Curreny</th>
            <th style="text-align: center;vertical-align: middle;">Amount</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
             <th style="text-align: center;vertical-align: middle;">Created By</th>
            <th style="text-align: center;vertical-align: middle;">Created Date</th>
            <th style="text-align: center;vertical-align: middle;">Approve By</th>
            <th style="text-align: center;vertical-align: middle;">Approve Date</th>
            <th style="text-align: center;vertical-align: middle;">Upload Doc By</th>
            <th style="text-align: center;vertical-align: middle;">Upload Doc Date</th>
            <!-- <th style="text-align: center;vertical-align: middle;">curr</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">Remark</th>
            <th style="text-align: center;vertical-align: middle;">Create By</th>
            <th style="text-align: center;vertical-align: middle;">Create Date</th> -->
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        // Backward compatible with existing links that send a complete WHERE clause.
        $where = isset($_GET['where']) ? $_GET['where'] : '';
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        if ($where === '') {
            $conditions = ["bankout_date BETWEEN '" . mysqli_real_escape_string($conn2, $start_date) . "' AND '" . mysqli_real_escape_string($conn2, $end_date) . "'"];
            foreach (['nama_supp', 'bank', 'akun', 'status'] as $field) {
                if (isset($_GET[$field]) && $_GET[$field] !== '' && $_GET[$field] !== 'ALL') {
                    $conditions[] = $field . " = '" . mysqli_real_escape_string($conn2, $_GET[$field]) . "'";
                }
            }
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }
        // menampilkan data pegawai
  

        $sql = mysqli_query($conn2,"select a.no_bankout,a.bankout_date,a.nama_supp,a.curr, a.amount, a.outstanding,IF(a.reff_doc = 'Payment','List Payment',a.reff_doc) as reff_doc, a.akun, a.bank,a.status,IF(a.deskripsi = '','-',a.deskripsi) as deskripsi,a.create_by,a.create_date,a.approve_by,a.approve_date, user_upload, tgl_upload,
            cf.show_subcategory as cash_flow_category
            from b_bankout_h a
            left join (select no_bankout nobank, created_by user_upload, MAX(created_date) tgl_upload from b_bankout_dok where status is null GROUP BY no_bankout) b on b.nobank = a.no_bankout
            left join master_cash_flow cf on cf.id = a.id_cash_flow
            $where");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){
            $approve_date = isset($row['approve_date']) ? $row['approve_date'] : null;
            $tglupload = isset($row['tgl_upload']) ? $row['tgl_upload'] : null;

            if ($approve_date == null) {
                $app_date = '-'; 
                $app_by = '-'; 
            }else{
                $app_date = date("d-M-Y",strtotime($approve_date));
                $app_by = $row['approve_by']; 
            }

            if ($tglupload == null) {
                $tgl_upload = '-'; 
                $user_upload = '-'; 
            }else{
                $tgl_upload = date("d-M-Y H:i:s",strtotime($tglupload));
                $user_upload = $row['user_upload']; 
            }

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style=" text-align : left" value="'.$row['no_bankout'].'">'.$row['no_bankout'].'</td>
            <td style=" text-align : left" value="'.$row['bankout_date'].'">'.date("d-M-Y",strtotime($row['bankout_date'])).'</td>
            <td style=" text-align : left" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style=" text-align : left" value="'.$row['cash_flow_category'].'">'.(!empty($row['cash_flow_category']) ? $row['cash_flow_category'] : '-').'</td>
            <td style=" text-align : left" value="'.$row['curr'].'">'.$row['curr'].'</td>
            <td style=" text-align : right" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
            <td style=" text-align : left" value="'.$row['status'].'">'.$row['status'].'</td>
            <td style=" text-align : left" value="'.$row['create_by'].'">'.$row['create_by'].'</td>
            <td style=" text-align : left" value="'.$row['create_date'].'">'.$row['create_date'].'</td>
            <td style=" text-align : left" value="'.$app_by.'">'.$app_by.'</td>
            <td style=" text-align : left" value="'.$app_date.'">'.$app_date.'</td>
            <td style=" text-align : left" value="'.$user_upload.'">'.$user_upload.'</td>
            <td style=" text-align : left" value="'.$tgl_upload.'">'.$tgl_upload.'</td>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




