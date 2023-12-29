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
    header("Content-Disposition: attachment; filename=master-cost-center.xls");
    $nama_ctg2 =strtolower($_GET['nama_ctg2']);
    $Status =strtolower($_GET['Status']);
     ?>

    <center>
        <h4>DATA CHART OF ACCOUNT <br/></h4>
    </center>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Cost Center</th>
            <th style="text-align: center;vertical-align: middle;">Cost Center Name</th>
            <th style="text-align: center;vertical-align: middle;">No Dept</th>
            <th style="text-align: center;vertical-align: middle;">Dept Name</th>
            <th style="text-align: center;vertical-align: middle;">Group 1</th>
            <th style="text-align: center;vertical-align: middle;">Group 2</th>
            <th style="text-align: center;vertical-align: middle;">Profit Center</th>
            <th style="text-align: center;vertical-align: middle;">status</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        // $nama_supp=$_GET['nama_supp'];
        // $where =$_GET['where'];
        $nama_ctg2 =$_GET['nama_ctg2'];
        $Status =$_GET['Status'];
        $where =$_GET['where'];
        // menampilkan data pegawai
  
        $sql = mysqli_query($conn2,"select id,no_cc,cc_name,id_cc,group1,group2,group21,profit_center,id_pc,status from b_master_cc $where");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td value = "'.$row['no_cc'].'">'.$row['no_cc'].'</td>
            <td value = "'.$row['cc_name'].'">'.$row['cc_name'].'</td>
            <td value = "'.$row['id_cc'].'">'.$row['id_cc'].'</td>
            <td value = "'.$row['group1'].'">'.$row['group1'].'</td>
            <td value = "'.$row['group2'].'">'.$row['group2'].'</td>
            <td value = "'.$row['group21'].'">'.$row['group21'].'</td>
            <td value = "'.$row['profit_center'].'">'.$row['profit_center'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




