<html>
<head>
    <title>Export Data General Ledger </title>
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
    header("Content-Disposition: attachment; filename=Prepaid Tax Report.xls");
    include '../../conn/conn.php';
    $startdate = date("d F Y",strtotime($_GET['start_date']));
    $enddate = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4>PREPAID TAX REPORT<br/> PERIODE: <?php echo $startdate; ?> - <?php echo $enddate; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
                <th style="text-align: center;vertical-align: middle;">No</th>
                <th style="text-align: center;vertical-align: middle;">Date</th>
                <th style="text-align: center;vertical-align: middle;">Journal No</th>
                <th style="text-align: center;vertical-align: middle;">Supplier Inv No</th>
                <th style="text-align: center;vertical-align: middle;">Description</th>
                <th style="text-align: center;vertical-align: middle;">Supplier Name</th>
                <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                <th style="text-align: center;vertical-align: middle;">Beginning Balance</th>
                <th style="text-align: center;vertical-align: middle;">Addition (Purchase)</th>
                <th style="text-align: center;vertical-align: middle;">Deduction (SI)</th>
                <th style="text-align: center;vertical-align: middle;">Deduction (GM)</th>
                <th style="text-align: center;vertical-align: middle;">Ending Balance</th>
                <th style="text-align: center;vertical-align: middle;">Remarks</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        $profit_center = $_GET['profit_center'];
        $no_coa = $_GET['no_coa'];

        if ($profit_center != 'ALL') {
            $profit_center = "where kode_pc = '$profit_center'";
        }else{
            $profit_center = "";
        }


        $sql = mysqli_query($conn2,"WITH

bpb as (select bpbno_int, a.id_supplier, b.supplier from bpb a inner join mastersupplier b on b.id_supplier = a.id_supplier where bpbdate >= '2025-01-01' and cancel != 'Y'
UNION
select bppbno_int, a.id_supplier, b.supplier from bppb a inner join mastersupplier b on b.id_supplier = a.id_supplier where bppbdate >= '2025-01-01' and cancel != 'Y'),

saldo_awal as (select tgl_journal, no_journal, no_kbon, deskripsi, supplier, profit_center kode_pc, CONCAT(id_pc,' - ',nama_pc) profit_center, round(total,2) total from acc_saldo_awal_prepaid_tax a INNER JOIN master_pc b on b.kode_pc = a.profit_center where a.no_coa = '$no_coa'),

data_in as (select tgl_journal, no_journal, '-' no_kbon, a.keterangan, c.supplier, kode_pc, CONCAT(id_pc,' - ',nama_pc) profit_center, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal a INNER JOIN master_pc b on b.kode_pc = a.profit_center INNER JOIN bpb c on c.bpbno_int = a.no_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%AP - BPB%' GROUP BY no_journal),

data_in_before as (select tgl_journal, no_journal, '-' no_kbon, a.keterangan, c.supplier, kode_pc, CONCAT(id_pc,' - ',nama_pc) profit_center, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal a INNER JOIN master_pc b on b.kode_pc = a.profit_center INNER JOIN bpb c on c.bpbno_int = a.no_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%AP - BPB%' GROUP BY no_journal),

data_out as (select reff_doc, no_coa, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%AP - Kontrabon%' GROUP BY reff_doc),

data_out_before as (select reff_doc, no_coa, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%AP - Kontrabon%' GROUP BY reff_doc),

data_gm as (select reff_doc, no_coa, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%OTHERS%' and no_journal like '%GM%' GROUP BY reff_doc),

data_gm_before as (select reff_doc, no_coa, ROUND(sum((debit * rate) - (credit * rate)),2) total from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and no_coa = '$no_coa' and type_journal like '%OTHERS%' and no_journal like '%GM%' GROUP BY reff_doc),

saldo_in as (select tgl_journal, no_journal, no_kbon, deskripsi, supplier, kode_pc, profit_center, sum(saldo_awal) saldo_awal, sum(total_in) total_in from (select tgl_journal, no_journal, no_kbon, deskripsi, supplier, kode_pc, profit_center, total saldo_awal, 0 total_in from saldo_awal
UNION ALL
select tgl_journal, no_journal, no_kbon, keterangan, supplier, kode_pc, profit_center, total saldo_awal, 0 total_in from data_in_before
UNION ALL
select tgl_journal, no_journal, no_kbon, keterangan, supplier, kode_pc, profit_center, 0 saldo_awal, total total_in from data_in) a GROUP BY no_journal),

saldo_out as (select reff_doc, no_coa, sum(total_out_before) total_out_before, sum(total_out) total_out , sum(total_gm_before) total_gm_before, sum(total_gm) total_gm from (select reff_doc, no_coa, 0 total_out_before, total total_out, 0 total_gm_before, 0 total_gm from data_out
UNION ALL
select reff_doc, no_coa, total total_out_before, 0 total_out, 0 total_gm_before, 0 total_gm from data_out_before
UNION ALL
select reff_doc, no_coa, 0 total_out_before, 0 total_out, 0 total_gm_before, total total_gm from data_gm
UNION ALL
select reff_doc, no_coa, 0 total_out_before, 0 total_out, total total_gm_before, 0 total_gm from data_gm_before) a GROUP BY reff_doc),

mutasi as (select tgl_journal, no_journal, no_kbon, deskripsi, supplier, kode_pc, profit_center, (saldo_awal + COALESCE(total_out_before,0) + COALESCE(total_gm_before,0)) saldo_awal, total_in, COALESCE(total_out,0) total_out, COALESCE(total_gm,0) total_gm from saldo_in a left join saldo_out b on b.reff_doc = a.no_journal)

select tgl_journal, no_journal, no_kbon, deskripsi, supplier, kode_pc, profit_center, saldo_awal, total_in, total_out, total_gm, (saldo_awal + total_in + total_out + total_gm) saldo_akhir, '' remark from mutasi $profit_center
");



$no = 1;
while($row = mysqli_fetch_array($sql)){

    echo ' <tr style="font-size:12px;text-align:left;">
    <td style="text-align:center;">'.$no++.'</td>
    <td value="'.$row['tgl_journal'].'">'.date("d-M-Y",strtotime($row['tgl_journal'])).'</td>                          
    <td value="'.$row['no_journal'].'">'.$row['no_journal'].'</td> 
    <td value="'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>                            
    <td value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td> 
    <td value="'.$row['supplier'].'">'.$row['supplier'].'</td> 
    <td value="'.$row['profit_center'].'">'.$row['profit_center'].'</td> 
    <td style="text-align:right;" value = "'.number_format($row['saldo_awal'],2).'">'.number_format($row['saldo_awal'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['total_in'],2).'">'.number_format($row['total_in'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['total_out'],2).'">'.number_format($row['total_out'],2).'</td>         
    <td style="text-align:right;" value = "'.number_format($row['total_gm'],2).'">'.number_format($row['total_gm'],2).'</td>
    <td style="text-align:right;" value = "'.number_format($row['saldo_akhir'],2).'">'.number_format($row['saldo_akhir'],2).'</td>
    <td value="'.$row['remark'].'">'.$row['remark'].'</td> 
    ';

    ?>
    <?php 

}
?>
</table>

</body>
</html>




