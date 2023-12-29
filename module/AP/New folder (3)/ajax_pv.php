<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$no_pv = isset($_POST['no_pv']) ? $_POST['no_pv']: null;

$sql = mysqli_query($conn1,"select a.no_pv,b.nama_coa, if(a.reff_doc = '','-',a.reff_doc) as reff_doc,a.reff_date,if(a.deskripsi = '','-',a.deskripsi) as deskripsi,a.amount,a.ded_add,a.due_date from tbl_pv a inner join mastercoa_v2 b on b.no_coa = a.coa where no_pv = '$no_pv' and amount != '0' OR no_pv = '$no_pv' and ded_add != '0' order by a.id asc");

	$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 11px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:20%;">Coa Name</th>
                            <th style="width:15%;">Reff Doc</th>
                            <th style="width:11%;">Reff Date</th>                                                                                
                            <th style="width:20%;">Deskripsi</th>
                            <th style="width:10%;">Amount</th>
                            <th style="width:13%;">Deduction/Addition</th>
                            <th style="width:11%;">Due Date</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			$duedate = $row['due_date'];
            $reffdate = $row['reff_date'];
            if ($duedate == '' || $duedate == '1970-01-01') { 
             $due_date = '-';
            }else{
                $due_date = date("d-M-Y",strtotime($row['due_date'])); 
            } 
            if ($reffdate == '' || $reffdate == '1970-01-01') { 
             $reff_date = '-';
            }else{
                $reff_date = date("d-M-Y",strtotime($row['reff_date'])); 
            } 
            $table .= '<tr>                       
                            <td style="" value="'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
                            <td style="" value="'.$row['reff_doc'].'">'.$row['reff_doc'].'</td> 
                            <td style="" value="'.$reff_date.'">'.$reff_date.'</td>                                                                      
                            <td style="" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>                            
                            <td style="text-align: right" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
                            <td style="text-align: right" value="'.$row['ded_add'].'">'.number_format($row['ded_add'],2).'</td>                            
                            <td style="" value="'.$due_date.'">'.$due_date.'</td>
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;

$sql2 = mysqli_query($conn1,"select no_pv,subtotal,adjust,pph,ppn,total from tbl_pv_h where no_pv = '$no_pv'");
$row2 = mysqli_fetch_assoc($sql2);

echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Total Without Tax
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($row2['subtotal'],2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            Addition/Deduction
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($row2['adjust'],2).'
        </td>       
    </tr> 
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            PPN
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            '.number_format($row2['ppn'],2).'
        </td>       
    </tr>   
    <tr>
        <td width="70%">
            
        </td>
            
        <td>
            PPH
        </td>
<td style="width:1%">:</td>
        <td style="text-align:right">
            - '.number_format($row2['pph'],2).'
        </td>       
    </tr>     

   <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Total 
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($row2['total'],2).'
        </td>
</tr>   
    
</table>';

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
?>