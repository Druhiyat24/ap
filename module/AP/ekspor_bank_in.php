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
    header("Content-Disposition: attachment; filename=list-bank-in.xls");
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
            <th style="text-align: center;vertical-align: middle;">No Bank In</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">Source</th>
            <th style="text-align: center;vertical-align: middle;">Cash Flow Category</th>
            <th style="text-align: center;vertical-align: middle;">Curreny</th>
            <th style="text-align: center;vertical-align: middle;">Amount</th>
            <th style="text-align: center;vertical-align: middle;">Outstanding</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">Created By</th>
            <th style="text-align: center;vertical-align: middle;">Created Date</th>
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

        $nama_supp = isset($_GET['nama_supp']) ? trim($_GET['nama_supp']) : 'ALL';
        $cash_flow = isset($_GET['cash_flow']) ? trim($_GET['cash_flow']) : 'ALL';
        $bank      = isset($_GET['bank']) ? trim($_GET['bank']) : 'ALL';
        $akun      = isset($_GET['akun']) ? trim($_GET['akun']) : 'ALL';
        $status    = isset($_GET['status']) ? trim($_GET['status']) : 'ALL';
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));

        $conditions = ["DATE(date) BETWEEN '" . mysqli_real_escape_string($conn2, $start_date) . "' AND '" . mysqli_real_escape_string($conn2, $end_date) . "'"];
        if ($nama_supp !== '' && $nama_supp !== 'ALL') {
            $conditions[] = "customer = '" . mysqli_real_escape_string($conn2, $nama_supp) . "'";
        }
        if ($cash_flow !== '' && $cash_flow !== 'ALL') {
            $conditions[] = "id_cash_flow = '" . mysqli_real_escape_string($conn2, $cash_flow) . "'";
        }
        if ($bank !== '' && $bank !== 'ALL') {
            $conditions[] = "bank = '" . mysqli_real_escape_string($conn2, $bank) . "'";
        }
        if ($akun !== '' && $akun !== 'ALL') {
            $conditions[] = "akun = '" . mysqli_real_escape_string($conn2, $akun) . "'";
        }
        if ($status !== '' && $status !== 'ALL') {
            $conditions[] = "status = '" . mysqli_real_escape_string($conn2, $status) . "'";
        }
        $where = 'where ' . implode(' and ', $conditions);

        $sql = mysqli_query($conn2,"select a.doc_num, a.customer, a.date, a.ref_data, a.akun, a.bank, a.curr, a.amount, a.outstanding, a.status, a.deskripsi, a.create_by, a.create_date, a.approve_by, a.approve_date,
            cf.show_subcategory as cash_flow_category
            from tbl_bankin_arcollection a
            left join master_cash_flow cf on cf.id = a.id_cash_flow
            $where group by a.doc_num order by a.id asc");

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
            <td style=" text-align : left" value="'.$row['doc_num'].'">'.$row['doc_num'].'</td>
            <td style=" text-align : left" value="'.$row['date'].'">'.date("d-M-Y",strtotime($row['date'])).'</td>                                                                                             
            <td style=" text-align : left" value="'.$row['customer'].'">'.$row['customer'].'</td>
            <td style=" text-align : left" value="'.$row['cash_flow_category'].'">'.(!empty($row['cash_flow_category']) ? $row['cash_flow_category'] : '-').'</td>
            <td style=" text-align : left" value="'.$row['curr'].'">'.$row['curr'].'</td>
            <td style=" text-align : right" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
            <td style=" text-align : right" value="'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td>
            <td style=" text-align : left" value="'.$row['status'].'">'.$row['status'].'</td>
            <td style=" text-align : left" value="'.$row['create_by'].'">'.$row['create_by'].'</td>
            <td style=" text-align : left" value="'.$row['create_date'].'">'.$row['create_date'].'</td>
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




