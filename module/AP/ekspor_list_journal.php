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

<!--     <center> -->
        <h4>DATA LIST JOURNAL <br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
  <!--   </center> -->
  <!--   STATUS: <?php echo $status; ?> -->
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Journal</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">Type</th>
            <th style="text-align: center;vertical-align: middle;">Modul Asal</th>
            <th style="text-align: center;vertical-align: middle;">No Coa</th>
            <th style="text-align: center;vertical-align: middle;">Coa Name</th>
            <th style="text-align: center;vertical-align: middle;">Cost Center</th>
            <th style="text-align: center;vertical-align: middle;">Reff</th>
            <th style="text-align: center;vertical-align: middle;">Reff Date</th>
            <th style="text-align: center;vertical-align: middle;">Buyer</th>
            <th style="text-align: center;vertical-align: middle;">WS</th>
            <th style="text-align: center;vertical-align: middle;">curr</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Debit IDR</th>
            <th style="text-align: center;vertical-align: middle;">Credit IDR</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">Remark</th>
            <th style="text-align: center;vertical-align: middle;">Create By</th>
            <th style="text-align: center;vertical-align: middle;">Create Date</th>
            <th style="text-align: center;vertical-align: middle;">CF Direct Debit</th>
            <th style="text-align: center;vertical-align: middle;">CF Direct Credit</th>
            <th style="text-align: center;vertical-align: middle;">CF Indirect</th>
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        // $nama_supp=$_GET['nama_supp'];
        // $status =$_GET['status'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
  

        $sql = mysqli_query($conn2,"select * from (select id,no_journal, tgl_journal, type_journal, no_coa, nama_coa, CONCAT(no_coa,' ',nama_coa) coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, Round(debit,2) debit, Round(credit,2) credit, ROUND(debit * rate,2) debit_idr, ROUND(credit * rate,2) credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal between '$start_date' and '$end_date' and no_journal not like '%KKK%' 
union
select DISTINCT '' id,no_journal, tgl_journal, type_journal, no_coa, nama_coa, CONCAT(no_coa,' ',nama_coa) coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, Round(debit,2) debit, Round(credit,2) credit, ROUND(debit * rate,2) debit_idr, ROUND(credit * rate,2) credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date,case when no_journal like '%/ALK/%' then 'AR'
                        when no_journal like '%L/NAG%' then 'AR'
                                                when no_journal like '%E/NAG%' then 'AR'
                                                when no_journal like '%/INM/%' then 'AR'
                                                when no_journal like '%/IN/%' then 'AP'
                                                when no_journal like '%/RI/%' then 'AP'
                                                when no_journal like '%/RO/%' then 'AP'
                                                when no_journal like '%/OUT/%' then 'AP'
                                                when no_journal like '%SI/APR/%' then 'AP'
                                                when no_journal like '%BM/%' then 'Bank'
                                                when no_journal like '%BK/%' then 'Bank'
                                                when no_journal like '%RCO/%' then 'Cash'
                                                when no_journal like '%RCI/%' then 'Cash'
                                                when no_journal like '%KKK/%' then 'Petty Cash'
                                                when no_journal like '%KKM/%' then 'Petty Cash'
                                                when no_journal like '%GM/%' then 'GM'
                        end asal from tbl_list_journal where tgl_journal between '$start_date' and '$end_date' and no_journal like '%KKK%') a left JOIN
(select no_coa coa, id_direct_debit, id_direct_credit, id_indirect from mastercoa_v2) b on b.coa = a.no_coa
left JOIN
(select id,ind_name as idndirdebit, eng_name as engdirdebit from tbl_master_cashflow) dirdebit on dirdebit.id = b.id_direct_debit left join

(select id,ind_name as idndircredit, eng_name as engdircredit from tbl_master_cashflow) dircredit on dircredit.id = b.id_direct_credit left join

(select id,ind_name as idnindir, eng_name as engindir from tbl_master_cashflow) indir on indir.id = b.id_indirect
");

        $no = 1;

        while($row = mysqli_fetch_array($sql)){
            $reff_date = $row['reff_date'];
            $coa = $row['no_coa'];
            $no_journal = $row['no_journal'];
            $idndirdebit = isset($row['idndirdebit']) ? $row['idndirdebit'] : "-";
            $idndircredit = isset($row['idndircredit']) ? $row['idndircredit'] : "-";
            $idnindir = isset($row['idnindir']) ? $row['idnindir'] : "-";
            $debit_idr = $row['debit_idr']; 
            $credit_idr = $row['credit_idr'];

        if(strpos($no_journal, 'GM/') !== false) {
            if ($coa == '8.52.02') {
            $debit = $row['debit']; 
            $credit = $row['credit'];
            }else{
            $debit = $row['debit']; 
            $credit = $row['credit'];
            }
        }else{
            if ($coa == '8.52.02') {
            $debit = 0; 
            $credit = 0; 
            }else{
            $debit = $row['debit']; 
            $credit = $row['credit'];
            }
        }

        if ($reff_date == '0000-00-00' || $reff_date == '1970-01-01' || $reff_date == '') {
            $Reffdate = '-'; 
        }else{
            $Reffdate = date("Y-m-d",strtotime($reff_date));
        }

        if ($debit_idr == '0' && $credit_idr == '0') {
                    echo '';
                 }else{   

        echo '<tr style="font-size:12px;text-align:left;">
            <td >'.$no++.'</td>
            <td  value = "'.$row['no_journal'].'">'.$row['no_journal'].'</td>
            <td  value = "'.$row['tgl_journal'].'">'.date("Y-m-d",strtotime($row['tgl_journal'])).'</td>
            <td  value = "'.$row['type_journal'].'">'.$row['type_journal'].'</td>
            <td  value = "'.$row['asal'].'">'.$row['asal'].'</td>
            <td  value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
            <td  value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
            <td  value = "'.$row['nama_costcenter'].'">'.$row['nama_costcenter'].'</td>
            <td  value = "'.$row['reff_doc'].'">'.$row['reff_doc'].'</td>
            <td  value = "'.$Reffdate.'">'.$Reffdate.'</td>
            <td  value = "'.$row['buyer'].'">'.$row['buyer'].'</td>
            <td  value = "'.$row['no_ws'].'">'.$row['no_ws'].'</td>
            <td  value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td  value="'.$debit.'">'.$debit.'</td>
            <td  value="'.$credit.'">'.$credit.'</td>
            <td  value="'.$row['debit_idr'].'">'.$row['debit_idr'].'</td>
            <td  value="'.$row['credit_idr'].'">'.$row['credit_idr'].'</td>
            <td  value = "'.$row['status'].'">'.$row['status'].'</td>
            <td  value = "'.$row['keterangan'].'">'.$row['keterangan'].'</td>
            <td  value = "'.$row['create_by'].'">'.$row['create_by'].'</td>
            <td  value = "'.$row['create_date'].'">'.$row['create_date'].'</td>

            <td  value = "'.$idndirdebit.'">'.$idndirdebit.'</td>
            <td  value = "'.$idndircredit.'">'.$idndircredit.'</td>
            <td  value = "'.$idnindir.'">'.$idnindir.'</td>
             ';
         }
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




