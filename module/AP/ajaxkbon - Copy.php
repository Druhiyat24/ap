<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$pph = 0;
$total = 0;
$jml_return = 0;
$jml_potong = 0;
$tax_return = 0; 
$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon']: null;

$sql = mysqli_query($conn2,"select bpb_new.no_bpb, bpb_new.pono, bpb_new.tgl_bpb, SUM(bpb_new.qty * bpb_new.price) as sub, SUM((bpb_new.qty * bpb_new.price) * (bpb_new.tax / 100)) as tax, (bppb_new.qty * bppb_new.price) * (bpb_new.tax / 100) as tax_return, SUM((bpb_new.qty * bpb_new.price) + ((bpb_new.qty * bpb_new.price) * (bpb_new.tax / 100))) - kontrabon.pph_value as total, kontrabon.pph_value as pph, kontrabon.dp_value as cbddp, bpb_new.supplier, potongan.jml_return, potongan.jml_potong from bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb inner join potongan on potongan.no_kbon = kontrabon.no_kbon left join bppb_new on bppb_new.no_bpb = bpb_new.no_bpb where kontrabon.no_kbon = '$no_kbon' group by bpb_new.no_bpb");	

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
			while ($row = mysqli_fetch_assoc($sql)) {
			$sub += $row['sub'];
			$tax += $row['tax'];
            $tax_return += $row['tax_return'];
            $pph += $row['pph'];
            $sqll = mysqli_query($conn2,"select jml_return, jml_potong from potongan where no_kbon = '$no_kbon' group by no_kbon");    
            $rowl = mysqli_fetch_assoc($sqll);
            $jml_return = $rowl['jml_return'];
            $jml_potong = $rowl['jml_potong'];
            $jml_dp = $row['cbddp'];
            $potong = $jml_return + $tax_return - $jml_potong;
			$total = $sub + $tax - $jml_dp - $pph - $potong;
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
            -'.number_format($potong,2).'
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