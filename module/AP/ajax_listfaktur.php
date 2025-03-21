<?php
include '../../conn/conn.php';
$ttl_dpp = 0;
$ttl_ppn = 0;
$gnd_ttl = 0; 
$no_dok = isset($_POST['no_dok']) ? $_POST['no_dok']: null;
$no_fak = isset($_POST['no_fak']) ? $_POST['no_fak']: null;
$tgl_fak = isset($_POST['tgl_fak']) ? $_POST['tgl_fak']: null;

    $sql = mysqli_query($conn1,"select nama_item,price,qty,ttl_harga,dpp,ppn from bpb_scan_faktur where kd_no_faktur = '$no_fak' GROUP BY nama_item,price,qty,ttl_harga,dpp,ppn"); 

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="">Item</th>
                            <th style="">Harga</th>
                            <th style="">Qty</th>
                            <th style="">Total</th> 
                            <th style="">DPP</th>
                            <th style="">PPN</th>                                                                                   
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$ttl_dpp += $row['dpp'];
			$ttl_ppn += $row['ppn'];
            $gnd_ttl += $row['ttl_harga'];
            $table .= '<tr>
                            <td style="" value="'.$row['nama_item'].'">'.$row['nama_item'].'</td>
                            <td style="text-align: right;" value="'.$row['price'].'">'.number_format($row['price'],2).'</td>  
                            <td style="text-align: right;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td>                            
                            <td style="text-align: right;" value="'.$row['ttl_harga'].'">'.number_format($row['ttl_harga'],2).'</td> 
                            <td style="text-align: right;" value="'.$row['dpp'].'">'.number_format($row['dpp'],2).'</td>                         
                            <td style="text-align: right;" value="'.$row['ppn'].'">'.number_format($row['ppn'],2).'</td>
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;

echo '<table width="100%" border="0" style="font-size:14px; font-weight: bold;">
    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total DPP
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($ttl_dpp,2).'
        </td>
    </tr> 
    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Total PPN
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($ttl_ppn,2).'
        </td>
    </tr> 
    <tr>
        <td width="70%">
            
        </td>
            
        <td >
            Grand Total
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($gnd_ttl,2).'
        </td>
    </tr>   
    
</table>';

?>