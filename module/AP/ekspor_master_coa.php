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
    header("Content-Disposition: attachment; filename=master-chart-of-account.xls");
    $nama_ctg2 =strtolower($_GET['nama_ctg2']);
    $nama_ctg5 =strtolower($_GET['nama_ctg5']);
    $Status =strtolower($_GET['Status']);
     ?>

    <center>
        <h4>DATA CHART OF ACCOUNT <br/></h4>
    </center>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No COA</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">COA Name</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;">Category 1</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;">Category 2</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;">Category 3</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;">Category 4</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;">Category 5</th>
            <th colspan="4" style="text-align: center;vertical-align: middle;">Category Cash Flow Direct</th>
            <th colspan="2" style="text-align: center;vertical-align: middle;">Category Cash Flow Indirect</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Status</th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;">Indonesia</th>
            <th style="text-align: center;vertical-align: middle;">English</th>
            <th style="text-align: center;vertical-align: middle;">Indonesia</th>
            <th style="text-align: center;vertical-align: middle;">English</th>
            <th style="text-align: center;vertical-align: middle;">Indonesia</th>
            <th style="text-align: center;vertical-align: middle;">English</th>
            <th style="text-align: center;vertical-align: middle;">Indonesia</th>
            <th style="text-align: center;vertical-align: middle;">English</th>
            <th style="text-align: center;vertical-align: middle;">Indonesia</th>
            <th style="text-align: center;vertical-align: middle;">English</th>
            <th style="text-align: center;vertical-align: middle;">Debit (Indonesia)</th>
            <th style="text-align: center;vertical-align: middle;">Debit (English)</th>
            <th style="text-align: center;vertical-align: middle;">Credit (Indonesia)</th>
            <th style="text-align: center;vertical-align: middle;">Credit (English)</th>
            <th style="text-align: center;vertical-align: middle;">Indonesia</th>
            <th style="text-align: center;vertical-align: middle;">English</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        // $nama_supp=$_GET['nama_supp'];
        // $where =$_GET['where'];
        $nama_ctg2 =$_GET['nama_ctg2'];
        $nama_ctg5 =$_GET['nama_ctg5'];
        $Status =$_GET['Status'];
        $where =$_GET['where'];
        // menampilkan data pegawai
  
        $sql = mysqli_query($conn2,"select * from ((select no_coa,id_direct_debit,id_direct_credit,id_indirect,nama_coa,id_ctg5,id_ctg2,status from mastercoa_v2) coa INNER JOIN

(select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
left join

(select id,ind_name as idndirdebit, eng_name as engdirdebit from tbl_master_cashflow) dirdebit on dirdebit.id = coa.id_direct_debit left join

(select id,ind_name as idndircredit, eng_name as engdircredit from tbl_master_cashflow) dircredit on dircredit.id = coa.id_direct_credit left join

(select id,ind_name as idnindir, eng_name as engindir from tbl_master_cashflow) indir on indir.id = coa.id_indirect) $where");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){
            $idndirdebit = isset($row['idndirdebit']) ? $row['idndirdebit'] : "NA";
            $engdirdebit = isset($row['engdirdebit']) ? $row['engdirdebit'] : "NA";
            $idndircredit = isset($row['idndircredit']) ? $row['idndircredit'] : "NA";
            $engdircredit = isset($row['engdircredit']) ? $row['engdircredit'] : "NA";
            $idnindir = isset($row['idnindir']) ? $row['idnindir'] : "NA";
            $engindir = isset($row['engindir']) ? $row['engindir'] : "NA";

        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
            <td value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
            <td value = "'.$row['indname1'].'">'.$row['indname1'].'</td>
            <td value = "'.$row['engname1'].'">'.$row['engname1'].'</td>
            <td value = "'.$row['indname2'].'">'.$row['indname2'].'</td>
            <td value = "'.$row['engname2'].'">'.$row['engname2'].'</td>
            <td value = "'.$row['indname3'].'">'.$row['indname3'].'</td>
            <td value = "'.$row['engname3'].'">'.$row['engname3'].'</td>
            <td value = "'.$row['indname4'].'">'.$row['indname4'].'</td>
            <td value = "'.$row['engname4'].'">'.$row['engname4'].'</td>
            <td value = "'.$row['indname5'].'">'.$row['indname5'].'</td>
            <td value = "'.$row['engname5'].'">'.$row['engname5'].'</td>
            <td value = "'.$row['idndirdebit'].'">'.$idndirdebit.'</td>
            <td value = "'.$row['engdirdebit'].'">'.$engdirdebit.'</td>
            <td value = "'.$row['idndircredit'].'">'.$idndircredit.'</td>
            <td value = "'.$row['engdircredit'].'">'.$engdircredit.'</td>
            <td value = "'.$row['idnindir'].'">'.$idnindir.'</td>
            <td value = "'.$row['engindir'].'">'.$engindir.'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
             ';
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




