<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$pph = 0;
$h_pph = 0;
$total = 0;
$jml_return = 0;
$jml_potong = 0;
$tax_return = 0; 
$ppn_return = 0;
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;

$sql = mysqli_query($conn2,"   (select a.no_bpb,a.no_po pono,a.tgl_bpb,a.subtotal sub, a.tax, '0' as tax_return,a.total,a.pph_value as pph,a.dp_value as cbddp,a.nama_supp supplier,b.jml_return, b.jml_potong from kontrabon a inner join potongan b on b.no_kbon = a.no_kbon where a.no_kbon = '$no_kbon' group by a.no_bpb )
    
    union
        (select bppb_new.no_bppb as no_bpb, bppb_new.no_po, bppb_new.tgl_bppb as tgl_bpb, SUM(-(bppb_new.qty * bppb_new.price)) as sub, '0' as tax, SUM(-(bppb_new.qty * bppb_new.price) * (bppb_new.tax / 100)) as tax_return, SUM(-((bppb_new.qty * bppb_new.price) + ((bppb_new.qty * bppb_new.price) * (bppb_new.tax / 100)))) as total, '0' as pph, '0' as cbddp, bppb_new.supplier, potongan.jml_return, potongan.jml_potong from bppb_new inner join return_kb on return_kb.no_bpbrtn = bppb_new.no_bppb inner join potongan on potongan.no_kbon = return_kb.no_kbon  where return_kb.no_kbon = '$no_kbon' and bppb_new.status != 'Cancel' group by bppb_new.no_bppb)");   

$sqll12 = mysqli_query($conn2,"select sum(pph_value) as pph from kontrabon where no_kbon = '$no_kbon' ");    
$rowl12 = mysqli_fetch_assoc($sqll12);

$sqll13 = mysqli_query($conn2,"select dp_value from kontrabon_h where no_kbon = '$no_kbon' ");    
$rowl13 = mysqli_fetch_assoc($sqll13);


$sqll15 = mysqli_query($conn2,"select sum((qty * price) * (tax / 100)) as tax_return from bppb_new  where no_kbon = '$no_kbon' ");    
$rowl15 = mysqli_fetch_assoc($sqll15);

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">No BPB</th>
                            <th style="width:100px;">No PO</th>
                            <th style="width:100px;">Tgl BPB</th>                                                                                
                            <th style="width:50px;">Subtotal</th>
                            <th style="width:50px;">Tax</th>
                            <th style="width:50px;">PPh</th>                            
                            <th style="width:50px;">Total</th>                            
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            $ppn_return += $rowl15['tax_return'];
            while ($row = mysqli_fetch_assoc($sql)) {
            $sub += $row['sub'];
            $tax += $row['tax'];
            $tax_return += $row['tax_return'];
            $pph = $rowl12['pph'];
            $h_pph = $pph - $ppn_return;
            $sqll = mysqli_query($conn2,"select jml_return, jml_potong from potongan where no_kbon = '$no_kbon' group by no_kbon");    
            $rowl = mysqli_fetch_assoc($sqll);
            $jml_return = $rowl['jml_return'];
            $jml_potong = $rowl['jml_potong'];
            $jml_dp = $rowl13['dp_value'];
            if ($jml_potong >= 0) {
                $potong = $jml_potong;
            }else{
            $potong = abs($jml_potong);
        }
            $total = $sub + ($tax - $ppn_return) - $jml_dp - $pph - $potong;
            $table .= '<tr>                       
                            <td style="width:100px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row['pono'].'">'.$row['pono'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                            <td style="width:50px;text-align:right;" value="'.$row['sub'].'">'.number_format($row['sub'],2).'</td>                            
                            <td style="width:50px;text-align:right;" value="'.$row['tax'].'">'.number_format($row['tax'],2).'</td>
                            <td style="width:50px;text-align:right;" value="'.$row['pph'].'">-'.number_format($row['pph'],2).'</td>                            
                            <td style="width:50px;text-align:right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>                            
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;

echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Subtotal
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($sub,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Tax (PPn) 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($tax,2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Tax (PPh) 
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            -'.number_format($pph,2).'
        </td>       
    </tr>

        <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Potongan
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($potong,2).'
        </td>       
    </tr> 

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            CBD / DP
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            -'.number_format($jml_dp,2).'
        </td>       
    </tr>   

    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total 
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($total,2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPn): '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax (PPh): -'.number_format($pph,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>