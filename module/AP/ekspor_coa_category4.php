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
    header("Content-Disposition: attachment; filename=coa-category4.xls");
     ?>

    <center>
        <h4>DATA COA CATEGORY 4 <br/></h4>
    </center>
    
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">ID Category</th>
            <th style="text-align: center;vertical-align: middle;">Indonesian Name</th>
            <th style="text-align: center;vertical-align: middle;">English Name</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        // $nama_supp=$_GET['nama_supp'];
        // $where =$_GET['where'];
        $where =$_GET['where'];
        // menampilkan data pegawai
  
        $sql = mysqli_query($conn2,"select id_ctg4,ind_name,eng_name from master_coa_ctg4 $where");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td value = "'.$row['id_ctg4'].'">'.$row['id_ctg4'].'</td>
            <td value = "'.$row['ind_name'].'">'.$row['ind_name'].'</td>
            <td value = "'.$row['eng_name'].'">'.$row['eng_name'].'</td>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




