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
    header("Content-Disposition: attachment; filename=master-cash-flow.xls");
    $nama_type =strtolower($_GET['nama_type']);
    $nama_group =strtolower($_GET['nama_group']);
    $Status =strtolower($_GET['Status']);
     ?>

    <center>
        <h4>DATA CASH FLOW <br/></h4>
    </center>
    Type: <?php echo ucwords($nama_type); ?>
    <br/>
    Group: <?php echo ucwords($nama_group); ?>
    <br/>
    Status: <?php echo ucwords($Status); ?>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">Type</th>
            <th style="text-align: center;vertical-align: middle;">Group</th>
            <th style="text-align: center;vertical-align: middle;">English Name</th>
            <th style="text-align: center;vertical-align: middle;">Indonesian Name</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        // $nama_supp=$_GET['nama_supp'];
        // $where =$_GET['where'];
        $nama_type =$_GET['nama_type'];
        $nama_group =$_GET['nama_group'];
        $Status =$_GET['Status'];
        $where =$_GET['where'];
        // menampilkan data pegawai
  
        $sql = mysqli_query($conn2,"select a.id,b.type,b.eng_name as grup,a.eng_name as cf_name,a.status,a.ind_name as cf_name2 from tbl_master_cashflow a inner join tbl_master_group_cf b on b.id=a.id_group $where");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align:left;" value = "'.$row['type'].'">'.$row['type'].'</td>
            <td style="text-align:left;" value = "'.$row['grup'].'">'.$row['grup'].'</td>
            <td style="text-align:left;" value = "'.$row['cf_name'].'">'.$row['cf_name'].'</td>
            <td style="text-align:left;" value = "'.$row['cf_name2'].'">'.$row['cf_name2'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




