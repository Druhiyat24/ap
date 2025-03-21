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
    header("Content-Disposition: attachment; filename=Other Receivable Material Report.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4>OTHER RECEIVABLE MATERIAL REPORT <br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No PO</th>
            <th style="text-align: center;vertical-align: middle;">No BPB</th>
            <th style="text-align: center;vertical-align: middle;">BPB Date</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Buyer</th>
            <th style="text-align: center;vertical-align: middle;">No Req</th>
            <th style="text-align: center;vertical-align: middle;">Req Date</th>
            <th style="text-align: center;vertical-align: middle;">No DN</th>
            <th style="text-align: center;vertical-align: middle;">DN Date</th>
            <th style="text-align: center;vertical-align: middle;">Begining Balance</th>
            <th style="text-align: center;vertical-align: middle;">Addition</th>
            <th style="text-align: center;vertical-align: middle;">Deduction (ALK)</th>
            <th style="text-align: center;vertical-align: middle;">Deduction (GM)</th>
            <th style="text-align: center;vertical-align: middle;">Forex Gain / (Loss)</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


        $sql = mysqli_query($conn2,"SELECT pono, bpbno_int, bpbdate, supplier, buyer,no_req, tgl_req, no_dn, tgl_dn, curr, round(COALESCE(saldo_awal,0) - COALESCE(ded_alk_bfr,0) - COALESCE(tot_gm_before,0),2) saldo_awal, coalesce(tambah,0) tambah, COALESCE(ded_alk,0) ded_alk, COALESCE(tot_gm,0) ded_gm, COALESCE(ded_fgl,0) ded_fgl, round(COALESCE(saldo_awal,0) - COALESCE(ded_alk_bfr,0)  - COALESCE(tot_gm_before,0) - COALESCE(tot_gm,0) + COALESCE(tambah,0) - (COALESCE(ded_alk,0) + COALESCE(ded_fgl,0)),2) saldo_akhir from (select a.pono, a.bpbno_int, a.bpbdate, b.supplier, buyer, curr, round(if(a.curr = 'IDR', sum(qty * price), (sum(qty * price) * rate)),2) saldo_awal, 0 tambah from bpb a 
                    inner join mastersupplier b on b.id_supplier = a.id_supplier 
                    left join (select id_jo,kpno,styleno, b.supplier buyer from act_costing ac inner join so on ac.id=so.id_cost 
                        inner join jo_det jod on so.id=jod.id_so inner join mastersupplier b on b.id_supplier = ac.id_buyer  group by id_jo) c on c. id_jo = a.id_jo 
                                                INNER JOIN (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) rt on rt.tanggal = a.bpbdate
                    left join po_header ph on a.pono = ph.pono
                    left join po_header_draft phd on phd.id = ph.id_draft where phd.tipe_com = 'Buyer' and a.bpbdate > '2024-10-01' and a.bpbdate < '$start_date' and a.cancel != 'Y' and a.confirm = 'Y' GROUP BY a.bpbno_int
                    UNION
                    select a.pono, a.bpbno_int, a.bpbdate, b.supplier, buyer, curr, 0 saldo_awal, round(if(a.curr = 'IDR', sum(qty * price), (sum(qty * price) * rate)),2) tambah from bpb a 
                    inner join mastersupplier b on b.id_supplier = a.id_supplier 
                    left join (select id_jo,kpno,styleno, b.supplier buyer from act_costing ac inner join so on ac.id=so.id_cost 
                        inner join jo_det jod on so.id=jod.id_so inner join mastersupplier b on b.id_supplier = ac.id_buyer  group by id_jo) c on c. id_jo = a.id_jo 
                                        INNER JOIN (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) rt on rt.tanggal = a.bpbdate
                    left join po_header ph on a.pono = ph.pono
                    left join po_header_draft phd on phd.id = ph.id_draft where phd.tipe_com = 'Buyer' and bpbdate >= '2024-10-01' and a.bpbdate >= '$start_date' and a.bpbdate <= '$end_date' and a.cancel != 'Y' and a.confirm = 'Y' GROUP BY a.bpbno_int) a 
                LEFT JOIN
                (select no_bpb,curr_alk,curr_bpb,sum(if(curr_alk = curr_bpb,total_alk,total_alk2)) ded_alk, sum(if(curr_alk = 'IDR' and curr_bpb = 'IDR',0,(tot_dn - total_alk2))) ded_fgl from (select no_bpb, curr_alk, curr_bpb, total_alk,total_alk2,tot_dn from (SELECT DISTINCT g.no_alk,g.tgl_alk, c.nm_memo no_bpb, g.curr curr_alk, h.curr curr_bpb, g.rate, IF(h.curr != 'IDR',(c.value * rate),value) tot_dn,c.amount amount_dn,(c.amount * g.rate) total_alk2,if(f.amount > c.amount,IF(e.from_curr = 'USD',(c.value * rate),c.value),(f.amount * rate)) total_alk, f.amount amount from (select id,no_dn,SUM(value) value, SUM(amount) amount,nm_memo from tbl_debitnote_det where nm_memo != '' and id_memo_det = '' GROUP BY no_dn,nm_memo) c INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN (select * from tbl_alokasi_detail where coa = '1.34.05') f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk INNER JOIN (select bpbno_int,curr from bpb a inner join po_header b on b.pono = a.pono INNER JOIN po_header_draft c on c.id = b.id_draft where c.tipe_com = 'Buyer' and bpbdate >= '2024-10-01' GROUP BY bpbno_int) h on h.bpbno_int = c.nm_memo where g.tgl_alk BETWEEN '$start_date' and '$end_date' and g.status != 'Cancel' and c.nm_memo is not null  order by c.nm_memo asc) a) a GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int
                LEFT JOIN
                (select no_bpb no_bpb_bfr, (ded_alk + ded_fgl) ded_alk_bfr from (select no_bpb,curr_alk,curr_bpb,sum(if(curr_alk = curr_bpb,total_alk,total_alk2)) ded_alk, sum(if(curr_alk = 'IDR' and curr_bpb = 'IDR',0,(tot_dn - total_alk2))) ded_fgl from (select no_bpb, curr_alk, curr_bpb, total_alk,total_alk2,tot_dn from (SELECT DISTINCT g.no_alk,g.tgl_alk, c.nm_memo no_bpb, g.curr curr_alk, h.curr curr_bpb, g.rate, IF(h.curr != 'IDR',(c.value * rate),value) tot_dn,c.amount amount_dn,(c.amount * g.rate) total_alk2,if(f.amount > c.amount,IF(e.from_curr = 'USD',(c.value * rate),c.value),(f.amount * rate)) total_alk, f.amount amount from (select id,no_dn,SUM(value) value, SUM(amount) amount,nm_memo from tbl_debitnote_det where nm_memo != '' and id_memo_det = '' GROUP BY no_dn,nm_memo) c INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN (select * from tbl_alokasi_detail where coa = '1.34.05') f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk INNER JOIN (select bpbno_int,curr from bpb a inner join po_header b on b.pono = a.pono INNER JOIN po_header_draft c on c.id = b.id_draft where c.tipe_com = 'Buyer' and bpbdate >= '2024-10-01' GROUP BY bpbno_int) h on h.bpbno_int = c.nm_memo where g.tgl_alk < '$start_date' and g.status != 'Cancel' and c.nm_memo is not null  order by c.nm_memo asc) a) a GROUP BY no_bpb) a) c on c.no_bpb_bfr = a.bpbno_int
                LEFT JOIN
                (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm from tbl_list_journal where no_coa = '1.34.05' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc) d on d.reff_doc = a.bpbno_int
                LEFT JOIN
                (select reff_doc reff_before, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm_before from tbl_list_journal where no_coa = '1.34.05' and no_journal like '%GM/%' and tgl_journal >= '2024-10-01' and tgl_journal < '$start_date' GROUP BY reff_doc) e on e.reff_before = a.bpbno_int
                LEFT JOIN
                (select GROUP_CONCAT(no_req) no_req, GROUP_CONCAT(tgl_req) tgl_req,GROUP_CONCAT(no_dn) no_dn, GROUP_CONCAT(tgl_dn) tgl_dn, no_bpb from (select a.no_req,a.tgl_req,c.no_dn,c.tgl_dn,b.no_bpb from req_dn_h a left join req_dn b on b.no_req = a.no_req left join tbl_debitnote_h c on c.no_dn = a.no_dn where a.status != 'Cancel' and no_bpb is not null GROUP BY b.no_bpb) a GROUP BY no_bpb ) f on f.no_bpb = a.bpbno_int");

$ttl_beg =0;
$ttl_add =0;
$ttl_ded =0;
$ttl_for =0;
$sisa_dn =0;
$sisa_dn_bfr =0;
$ttl_end =0;
$no = 0;
$ttl_mj =0;
$ost_dn = 0;
while($row2 = mysqli_fetch_array($sql)){
    $no++;
    $saldo_awal = isset($row2['saldo_awal']) ? $row2['saldo_awal'] : 0;
    $tambah = isset($row2['tambah']) ? $row2['tambah'] : 0;
    $ded_alk = isset($row2['ded_alk']) ? $row2['ded_alk'] : 0;
    $ded_gm = isset($row2['ded_gm']) ? $row2['ded_gm'] : 0;
    $ded_fgl = isset($row2['ded_fgl']) ? $row2['ded_fgl'] : 0;
    $saldo_akhir = isset($row2['saldo_akhir']) ? $row2['saldo_akhir'] : 0;


    $ttl_beg +=$saldo_awal;
    $ttl_add +=$tambah;
    $ttl_ded +=$ded_alk;
    $ttl_mj  +=$ded_gm;
    $ttl_for +=$ded_fgl;
    $ttl_end +=$saldo_akhir;

    if ($saldo_awal == 0 && $tambah == 0) {
            // code...
    }else{
        echo ' <tr style="font-size:12px;text-align:center;">
        <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
        <td style="text-align : left;" value = "'.$row2['pono'].'">'.$row2['pono'].'</td>
        <td style="text-align : left;" value = "'.$row2['bpbno_int'].'">'.$row2['bpbno_int'].'</td>
        <td style="width: 100px;" value = "'.$row2['bpbdate'].'">'.date("d-M-Y",strtotime($row2['bpbdate'])).'</td>
        <td style="text-align : left;" value = "'.$row2['supplier'].'">'.$row2['supplier'].'</td>
        <td style="text-align : left;" value = "'.$row2['buyer'].'">'.$row2['buyer'].'</td>
        <td style="text-align : left;" value = "'.$row2['no_req'].'">'.$row2['no_req'].'</td>
        <td style="text-align : left;" value = "'.$row2['tgl_req'].'">'.$row2['tgl_req'].'</td>
        <td style="text-align : left;" value = "'.$row2['no_dn'].'">'.$row2['no_dn'].'</td>
        <td style="text-align : left;" value = "'.$row2['tgl_dn'].'">'.$row2['tgl_dn'].'</td>
        <td style="text-align : right;" value = "'.$saldo_awal.'">'.number_format($saldo_awal,2).'</td>
        <td style="text-align : right;" value = "'.$row2['tambah'].'">'.number_format($row2['tambah'],2).'</td>
        <td style="text-align : right;" value = "'.$row2['ded_alk'].'">'.number_format($row2['ded_alk'],2).'</td>
        <td style="text-align : right;" value = "'.$ded_gm.'">'.number_format($ded_gm,2).'</td>
        <td style="text-align : right;" value = "'.$ded_fgl.'">'.number_format($ded_fgl,2).'</td>
        <td style="text-align : right;" value = "'.$saldo_akhir.'">'.number_format($saldo_akhir,2).'</td>
        </tr>
        ';
}

?>
<?php 

}
?>
</table>

</body>
</html>




