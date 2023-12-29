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
        <h4>DATA BANK IN <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    <!-- </center> -->
  <!--   STATUS: <?php echo $status; ?> -->
 
    <table style="width:100%;font-size:12px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Bank Out</th>
            <th style="text-align: center;vertical-align: middle;">Source</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">Curreny</th>
            <th style="text-align: center;vertical-align: middle;">Amount</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">Approve By</th>
            <th style="text-align: center;vertical-align: middle;">Approve Date</th>
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
        // $nama_supp=$_GET['nama_supp'];
        $where =$_GET['where'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
  

        $sql = mysqli_query($conn2,"select no_bankout,bankout_date,nama_supp,curr, amount, outstanding,IF(reff_doc = 'Payment','List Payment',reff_doc) as reff_doc, akun, bank,status,IF(deskripsi = '','-',deskripsi) as deskripsi,approve_by,approve_date from b_bankout_h $where");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){
            $approve_date = isset($row['approve_date']) ? $row['approve_date'] : null;

            if ($approve_date == null) {
                $app_date = '-'; 
                $app_by = '-'; 
            }else{
                $app_date = date("d-M-Y",strtotime($approve_date));
                $app_by = $row['approve_by']; 
            }

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style=" text-align : left" value="'.$row['no_bankout'].'">'.$row['no_bankout'].'</td>
            <td style=" text-align : left" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style=" text-align : left" value="'.$row['bankout_date'].'">'.date("d-M-Y",strtotime($row['bankout_date'])).'</td>                                                                                             
            <td style=" text-align : left" value="'.$row['curr'].'">'.$row['curr'].'</td>
            <td style=" text-align : right" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
            <td style=" text-align : left" value="'.$row['status'].'">'.$row['status'].'</td>
            <td style=" text-align : left" value="'.$app_by.'">'.$app_by.'</td>
            <td style=" text-align : left" value="'.$app_date.'">'.$app_date.'</td>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




