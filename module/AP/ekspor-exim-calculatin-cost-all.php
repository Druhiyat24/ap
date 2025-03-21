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
    header("Content-Disposition: attachment; filename=Exim calculatin cost Report.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4>EXIM CALCULATION COST REPORT <br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Memo</th>
            <th style="text-align: center;vertical-align: middle;">Memo Date</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Buyer</th>
            <th style="text-align: center;vertical-align: middle;">Season</th>
            <th style="text-align: center;vertical-align: middle;">No Invoice</th>
            <th style="text-align: center;vertical-align: middle;">Invoice Date</th>
            <th style="text-align: center;vertical-align: middle;">Qty</th>
            <th style="text-align: center;vertical-align: middle;">PPJK/Customs Brokerage</th>
            <th style="text-align: center;vertical-align: middle;">Storage</th>
            <th style="text-align: center;vertical-align: middle;">Forwarder/Shipping line</th>
            <th style="text-align: center;vertical-align: middle;">Insurance</th>
            <th style="text-align: center;vertical-align: middle;">Disperindag</th>
            <th style="text-align: center;vertical-align: middle;">PPN</th>
            <th style="text-align: center;vertical-align: middle;">Custom Bond</th>
            <th style="text-align: center;vertical-align: middle;">Iuran APKB</th>
            <th style="text-align: center;vertical-align: middle;">Courier</th>
            <th style="text-align: center;vertical-align: middle;">PDRI</th>
            <th style="text-align: center;vertical-align: middle;">VAT</th>
            <th style="text-align: center;vertical-align: middle;">Total</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


        $sql = mysqli_query($conn2,"select a.id_h, a.nm_memo, tgl_memo, nama_supp, buyer, season_desc, id_book, no_invoice, tgl_invoice, a.qty, round(ttl_ctg1 / b.qty,2) ttl_ctg1, round(ttl_ctg2 / b.qty,2) ttl_ctg2, round(ttl_ctg3 / b.qty,2) ttl_ctg3, round(ttl_ctg4 / b.qty,2) ttl_ctg4, round(ttl_ctg5 / b.qty,2) ttl_ctg5, round(ttl_ctg6 / b.qty,2) ttl_ctg6, round(ttl_ctg7 / b.qty,2) ttl_ctg7, round(ttl_ctg8 / b.qty,2) ttl_ctg8, round(ttl_ctg9 / b.qty,2) ttl_ctg9, round(ttl_ctg10 / b.qty,2) ttl_ctg10, round(ttl_vat / b.qty,2) ttl_vat from (select a.*, round(qty * ctg1,2) ttl_ctg1, round(qty * ctg2,2) ttl_ctg2, round(qty * ctg3,2) ttl_ctg3, round(qty * ctg4,2) ttl_ctg4, round(qty * ctg5,2) ttl_ctg5, round(qty * ctg6,2) ttl_ctg6, round(qty * ctg7,2) ttl_ctg7, round(qty * ctg8,2) ttl_ctg8, round(qty * ctg9,2) ttl_ctg9, round(qty * ctg10,2) ttl_ctg10, round(qty * vat,2) ttl_vat from (SELECT a.*, season_desc, qty, DATE(sj_date) tgl_invoice FROM (select a.id_h,a.nm_memo, a.tgl_memo, b.Supplier nama_supp, c.Supplier buyer, d.id_book, d.no_invoice from memo_h a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier INNER JOIN mastersupplier c on c.id_supplier = a.id_buyer INNER JOIN memo_inv d on d.id_h = a.id_h where jns_inv = 'INVOICE' and a.status != 'CANCEL') a left join (select a.*,if(sn.season_desc is null, '-', sn.season_desc) season_desc from (select b.no_invoice,a.id_book_invoice, a.sj_date, a.so_number,sum(qty) qty from tbl_invoice_detail a INNER JOIN tbl_book_invoice b on b.id = a.id_book_invoice where b.status != 'Cancel' GROUP BY a.id_book_invoice) a inner join so on so.so_no = a.so_number left join masterseason sn on sn.id_season = so.id_season GROUP BY a.id_book_invoice) b on b.id_book_invoice = a.id_book) a left join (select a.*, COALESCE(b.biaya,0) vat from (SELECT
          id_h,
          Coalesce(MAX(CASE WHEN id_ctg = '1' THEN biaya END),0) AS ctg1,
          Coalesce(MAX(CASE WHEN id_ctg = '2' THEN biaya END),0) AS ctg2,
          Coalesce(MAX(CASE WHEN id_ctg = '3' THEN biaya END),0) AS ctg3,
          Coalesce(MAX(CASE WHEN id_ctg = '4' THEN biaya END),0) AS ctg4,
          Coalesce(MAX(CASE WHEN id_ctg = '5' THEN biaya END),0) AS ctg5,
          Coalesce(MAX(CASE WHEN id_ctg = '6' THEN biaya END),0) AS ctg6,
          Coalesce(MAX(CASE WHEN id_ctg = '7' THEN biaya END),0) AS ctg7,
          Coalesce(MAX(CASE WHEN id_ctg = '8' THEN biaya END),0) AS ctg8,
          Coalesce(MAX(CASE WHEN id_ctg = '9' THEN biaya END),0) AS ctg9,
          Coalesce(MAX(CASE WHEN id_ctg = '10' THEN biaya END),0) AS ctg10
          FROM (select id_h, id_ctg, nm_ctg, round(SUM(biaya),2) biaya from memo_det where cancel = 'N' and nm_sub_ctg != 'VAT' GROUP BY id_h, id_ctg) a
          GROUP BY id_h) a LEFT JOIN (select id_h, round(SUM(biaya),2) biaya from memo_det where cancel = 'N' and nm_sub_ctg = 'VAT' GROUP BY id_h) b on b.id_h = a.id_h) b on b.id_h = a.id_h) a INNER JOIN (SELECT id_h, nm_memo, SUM(qty) qty FROM (select a.id_h,a.nm_memo, a.tgl_memo, b.Supplier nama_supp, c.Supplier buyer, d.id_book from memo_h a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier INNER JOIN mastersupplier c on c.id_supplier = a.id_buyer INNER JOIN memo_inv d on d.id_h = a.id_h where jns_inv = 'INVOICE' and a.status != 'CANCEL') a left join (select a.*,if(sn.season_desc is null, '-', sn.season_desc) season_desc from (select b.no_invoice,a.id_book_invoice, a.so_number,sum(qty) qty from tbl_invoice_detail a INNER JOIN tbl_book_invoice b on b.id = a.id_book_invoice where b.status != 'Cancel' GROUP BY a.id_book_invoice) a inner join so on so.so_no = a.so_number left join masterseason sn on sn.id_season = so.id_season GROUP BY a.id_book_invoice) b on b.id_book_invoice = a.id_book GROUP BY id_h) b on b.nm_memo = a.nm_memo where tgl_memo BETWEEN '$start_date' and '$end_date'");

        $total_qty =0;
        $total_ctg1 =0;
        $total_ctg2 =0;
        $total_ctg3 =0;
        $total_ctg4 =0;
        $total_ctg5 =0;
        $total_ctg6 =0;
        $total_ctg7 =0;
        $total_ctg8 =0;
        $total_ctg9 =0;
        $total_ctg10 =0;
        $total_vat =0;
        $total_biaya_all =0;
        $no = 0;
        while($row2 = mysqli_fetch_array($sql)){
            $no++;
            $tgl_invoice = $row2['tgl_invoice'];
            if ($tgl_invoice == '0000-00-00' || $tgl_invoice == '1970-01-01' || $tgl_invoice == null) {
                $tglinvoice = '-'; 
            }else{
                $tglinvoice = date("Y-m-d",strtotime($tgl_invoice));
            }
            $biaya_total = $row2['ttl_ctg1'] + $row2['ttl_ctg2'] + $row2['ttl_ctg3'] + $row2['ttl_ctg4'] + $row2['ttl_ctg5'] + $row2['ttl_ctg6'] + $row2['ttl_ctg7'] + $row2['ttl_ctg8'] + $row2['ttl_ctg9'] + $row2['ttl_ctg10'] + $row2['ttl_vat'];

            $total_qty += $row2['qty'];
            $total_ctg1 += $row2['ttl_ctg1'];
            $total_ctg2 += $row2['ttl_ctg2'];
            $total_ctg3 += $row2['ttl_ctg3'];
            $total_ctg4 += $row2['ttl_ctg4'];
            $total_ctg5 += $row2['ttl_ctg5'];
            $total_ctg6 += $row2['ttl_ctg6'];
            $total_ctg7 += $row2['ttl_ctg7'];
            $total_ctg8 += $row2['ttl_ctg8'];
            $total_ctg9 += $row2['ttl_ctg9'];
            $total_ctg10 += $row2['ttl_ctg10'];
            $total_vat += $row2['ttl_vat'];
            $total_biaya_all += $biaya_total;

            echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
            <td style="text-align : left;" value = "'.$row2['nm_memo'].'">'.$row2['nm_memo'].'</td>
            <td style="width: 100px;" value = "'.$row2['tgl_memo'].'">'.date("d-M-Y",strtotime($row2['tgl_memo'])).'</td>
            <td style="text-align : left;" value = "'.$row2['nama_supp'].'">'.$row2['nama_supp'].'</td>
            <td style="text-align : left;" value = "'.$row2['buyer'].'">'.$row2['buyer'].'</td>
            <td style="text-align : left;" value = "'.$row2['season_desc'].'">'.$row2['season_desc'].'</td>
            <td style="text-align : left;" value = "'.$row2['no_invoice'].'">'.$row2['no_invoice'].'</td>
            <td style="width: 100px;" value = "'.$row2['tgl_invoice'].'">'.$tglinvoice.'</td>
            <td style="text-align : right;" value = "'.$row2['qty'].'">'.number_format($row2['qty'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg1'].'">'.number_format($row2['ttl_ctg1'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg2'].'">'.number_format($row2['ttl_ctg2'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg3'].'">'.number_format($row2['ttl_ctg3'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg4'].'">'.number_format($row2['ttl_ctg4'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg5'].'">'.number_format($row2['ttl_ctg5'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg6'].'">'.number_format($row2['ttl_ctg6'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg7'].'">'.number_format($row2['ttl_ctg7'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg8'].'">'.number_format($row2['ttl_ctg8'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg9'].'">'.number_format($row2['ttl_ctg9'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_ctg10'].'">'.number_format($row2['ttl_ctg10'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['ttl_vat'].'">'.number_format($row2['ttl_vat'],2).'</td>
            <td style="text-align : right;" value = "'.$biaya_total.'">'.number_format($biaya_total,2).'</td>
            </tr>
            ';

            ?>
            <?php 

        }
        ?>
    </table>

</body>
</html>




