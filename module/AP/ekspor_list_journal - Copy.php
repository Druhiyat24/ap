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
    header("Content-Disposition: attachment; filename=list-journal.xls");
    // $nama_supp =$_GET['nama_supp'];
    // $status =$_GET['status'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <center>
        <h4>DATA LIST JOURNAL <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
    </center>
  <!--   STATUS: <?php echo $status; ?> -->
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Journal</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">Type</th>
            <th style="text-align: center;vertical-align: middle;">Coa</th>
            <th style="text-align: center;vertical-align: middle;">Cost Center</th>
            <th style="text-align: center;vertical-align: middle;">Reff</th>
            <th style="text-align: center;vertical-align: middle;">Reff Date</th>
            <th style="text-align: center;vertical-align: middle;">Buyer</th>
            <th style="text-align: center;vertical-align: middle;">WS</th>
            <th style="text-align: center;vertical-align: middle;">curr</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">Remark</th>
            <th style="text-align: center;vertical-align: middle;">Create By</th>
            <th style="text-align: center;vertical-align: middle;">Create Date</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        // $nama_supp=$_GET['nama_supp'];
        // $status =$_GET['status'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
  

        $sql = mysqli_query($conn2,"select no_journal, tgl_journal, type_journal, no_coa, nama_coa, CONCAT(no_coa,' ',nama_coa) coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date from tbl_list_journal where tgl_journal between '$start_date' and '$end_date'");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){
            $reff_date = $row['reff_date'];
        if ($reff_date == '0000-00-00' || $reff_date == '1970-01-01' || $reff_date == '') {
            $Reffdate = '-'; 
        }else{
            $Reffdate = date("Y-m-d",strtotime($reff_date));
        }

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td  value = "'.$row['no_journal'].'">'.$row['no_journal'].'</td>
            <td  value = "'.$row['tgl_journal'].'">'.date("Y-m-d",strtotime($row['tgl_journal'])).'</td>
            <td  value = "'.$row['type_journal'].'">'.$row['type_journal'].'</td>
            <td  value = "'.$row['coa'].'">'.$row['coa'].'</td>
            <td  value = "'.$row['nama_costcenter'].'">'.$row['nama_costcenter'].'</td>
            <td  value = "'.$row['reff_doc'].'">'.$row['reff_doc'].'</td>
            <td  value = "'.$Reffdate.'">'.$Reffdate.'</td>
            <td  value = "'.$row['buyer'].'">'.$row['buyer'].'</td>
            <td  value = "'.$row['no_ws'].'">'.$row['no_ws'].'</td>
            <td  value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td  value="'.$row['debit'].'">'.$row['debit'].'</td>
            <td  value="'.$row['credit'].'">'.$row['credit'].'</td>
            <td  value = "'.$row['status'].'">'.$row['status'].'</td>
            <td  value = "'.$row['keterangan'].'">'.$row['keterangan'].'</td>
            <td  value = "'.$row['create_by'].'">'.$row['create_by'].'</td>
            <td  value = "'.$row['create_date'].'">'.$row['create_date'].'</td>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




