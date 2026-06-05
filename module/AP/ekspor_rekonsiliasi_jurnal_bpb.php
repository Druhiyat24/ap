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
    header("Content-Disposition: attachment; filename=Rekonsiliasi jOurnal - bpb.xls");
    include '../../conn/conn.php';
    $startdate = date("d F Y",strtotime($_GET['start_date']));
    $enddate = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4>REKONSILIASI JOURNAL - BPB<br/> PERIODE: <?php echo $startdate; ?> - <?php echo $enddate; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
                <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;">Bpb Date</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;">Bpb Number</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;">Supplier</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;">PO</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;">Type PO</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;">Status</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;">Curr</th>
                <th colspan="3" style="text-align: center;vertical-align: middle;">BPB</th>
                <th colspan="3" style="text-align: center;vertical-align: middle;">Journal</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;">Different Total</th>
        </tr>
        <tr>
                <th style="text-align: center;vertical-align: middle;">Dpp</th>
                <th style="text-align: center;vertical-align: middle;">Ppn</th>
                <th style="text-align: center;vertical-align: middle;">Total</th>
                <th style="text-align: center;vertical-align: middle;">Dpp</th>
                <th style="text-align: center;vertical-align: middle;">Ppn</th>
                <th style="text-align: center;vertical-align: middle;">Total</th>
                
            </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        $status = $_GET['status'];

        if ($status != 'ALL') {
            $status = "where status = '$status'";
        }else{
            $status = "";
        }


        $sql = mysqli_query($conn2,"WITH
bpb_sb as (select bpbno_int, bpbdate, supplier, ph.pono, phd.tipe_com, IF(bpb.confirm = 'Y', 'APPROVED', 'DRAFT') status, bpb.curr, round(SUM(((qty - COALESCE(qty_reject,0)) * if(phd.tipe_com = 'FOC',0,price)) + (((qty - COALESCE(qty_reject,0)) * if(phd.tipe_com = 'FOC',0,price)) * (COALESCE(ph.tax,0) /100))),4) as total,round(SUM(((qty - COALESCE(qty_reject,0)) * if(phd.tipe_com = 'FOC',0,price))),4) as dpp,round(SUM((((qty - COALESCE(qty_reject,0)) * if(phd.tipe_com = 'FOC',0,price)) * (COALESCE(ph.tax,0) /100))),4) as ppn from bpb inner join masteritem mi on bpb.id_item = mi.id_item inner join mastersupplier ms on bpb.id_supplier = ms.id_supplier left join po_header ph on bpb.pono = ph.pono left join po_header_draft phd on phd.id = ph.id_draft where bpbdate BETWEEN '$start_date' and '$end_date' and bpb.cancel != 'Y' and bpbno_int not like 'BPB/NAK%' and bpbno_int not like 'GB%' and bpbno_int not like 'SGG/IN%' and bpbno_int not like 'GG/IN%' and bpbno_int not like 'GM/IN%' group by bpbno_int),

bpb_knitting as (select no_bpb, tgl_bpb, supplier, no_po, tipe, IF(confirm = 'Y', 'APPROVED', 'DRAFT') status, a.curr, SUM(total) total, SUM(dpp) dpp, SUM(ppn) ppn from bpb_knitting a INNER JOIN (select bpbno_int, confirm from bpb where bpbdate BETWEEN '$start_date' and '$end_date' and cancel != 'Y' and (bpbno_int like 'BPB/NAK%' OR bpbno_int like 'GB/IN%' OR bpbno_int like 'SGG/IN%'  OR bpbno_int like 'GG/IN%'  OR bpbno_int like 'GM/IN%') GROUP BY bpbno_int) b on b.bpbno_int = a.no_bpb GROUP BY no_bpb),

bppb as (select bppbno_int, bppbdate, supplier, b.pono, tipe_com tipe, IF(confirm = 'Y', 'APPROVED', 'DRAFT') status, curr, (dpp + (dpp * (coalesce(tax,0)/100))) total,dpp,(dpp * (coalesce(tax,0)/100)) ppn from (select bppbno, bppbno_int, bppb.bppbdate, bppb.id_supplier, supplier, mattype, n_code_category, if(matclass like '%ACCESORIES%','ACCESORIES',mi.matclass) matclass, bppb.curr,bppb.username, bppb.dateinput, bppb.confirm, - SUM(((qty) * price)) as dpp,bpbno_ro from bppb inner join masteritem mi on bppb.id_item = mi.id_item inner join mastersupplier ms on bppb.id_supplier = ms.id_supplier where bppbdate BETWEEN '$start_date' and '$end_date' and cancel != 'Y' and bppbno_int not like 'OFC/%' group by bppbno_int) a left join (select bpbno,pono from bpb GROUP BY bpbno) b on b.bpbno = a.bpbno_ro left JOIN (select ph.pono,ph.tax, tipe_com from po_header ph left join po_header_draft phd on phd.id = ph.id_draft GROUP BY pono) c on c.pono = b.pono),

data_bpb as (select bpbno_int, bpbdate, supplier, COALESCE(pono,'-') pono, tipe_com, status, curr, round(total,4) total, round(dpp,4) dpp, round(ppn ,4) ppn from (select * from bpb_sb where total != 0
UNION ALL
select * from bpb_knitting where total != 0
UNION ALL
select * from bppb where total != 0) a),

data_jurnal as (SELECT no_journal, SUM(
            CASE 
                WHEN nama_coa LIKE 'GR/IR%' 
                     OR nama_coa LIKE 'PIUTANG LAIN-LAIN%'
                THEN (credit - debit)
                ELSE 0 
            END ) AS total, 
                        SUM(
            CASE 
                WHEN nama_coa LIKE 'PERSEDIAAN%' 
                     OR nama_coa LIKE 'BEBAN%'
                                         OR nama_coa LIKE 'MESIN%'
                THEN (debit - credit)
                ELSE 0 
            END ) AS dpp,
                        SUM(
            CASE 
                        
                WHEN nama_coa LIKE 'PAJAK%' 
                     OR nama_coa LIKE 'PENDAPATAN%'
                THEN (debit - credit)
                ELSE 0 
            END ) AS ppn FROM tbl_list_journal WHERE tgl_journal BETWEEN '$start_date' and '$end_date' AND type_journal LIKE '%AP - BPB%' GROUP BY no_journal )
                        
select a.*, ROUND(COALESCE(b.total,0),4) total_jurnal, ROUND(COALESCE(b.dpp,0),4) dpp_jurnal, ROUND(COALESCE(b.ppn,0),4) ppn_jurnal, ROUND(a.total - ROUND(COALESCE(b.total,0),4),4) diff_total, ROUND(a.dpp - ROUND(COALESCE(b.dpp,0),4),4) diff_dpp, ROUND(a.ppn - ROUND(COALESCE(b.ppn,0),4),4) diff_ppn from data_bpb a LEFT JOIN data_jurnal b on b.no_journal = a.bpbno_int $status
");



$no = 1;
while($row = mysqli_fetch_array($sql)){

    echo ' <tr style="font-size:12px;text-align:left;">
    <td style="text-align:center;">'.$no++.'</td>
    <td value="'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td> 
    <td value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>                          
    <td value="'.$row['supplier'].'">'.$row['supplier'].'</td>                            
    <td value="'.$row['pono'].'">'.$row['pono'].'</td> 
    <td value="'.$row['tipe_com'].'">'.$row['tipe_com'].'</td> 
    <td value="'.$row['status'].'">'.$row['status'].'</td> 
    <td value="'.$row['curr'].'">'.$row['curr'].'</td> 
    <td style="text-align:right;" value = "'.number_format($row['dpp'],4).'">'.number_format($row['dpp'],4).'</td>
    <td style="text-align:right;" value = "'.number_format($row['ppn'],4).'">'.number_format($row['ppn'],4).'</td>
    <td style="text-align:right;" value = "'.number_format($row['total'],4).'">'.number_format($row['total'],4).'</td>         
    <td style="text-align:right;" value = "'.number_format($row['dpp_jurnal'],4).'">'.number_format($row['dpp_jurnal'],4).'</td>
    <td style="text-align:right;" value = "'.number_format($row['ppn_jurnal'],4).'">'.number_format($row['ppn_jurnal'],4).'</td>
    <td style="text-align:right;" value = "'.number_format($row['total_jurnal'],4).'">'.number_format($row['total_jurnal'],4).'</td>
    <td style="text-align:right;" value = "'.number_format($row['diff_total'],4).'">'.number_format($row['diff_total'],4).'</td>
    ';

    ?>
    <?php 

}
?>
</table>

</body>
</html>




