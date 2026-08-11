<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$no_pv = isset($_POST['no_pv']) ? $_POST['no_pv']: null;

$sql = mysqli_query($conn1,"select a.no_pv,concat(b.no_coa,' ',b.nama_coa) nama_coa,IF(c.cc_name is null,'-',CONCAT(c.no_cc, ' - ', c.cc_name)) cc_name, if(a.reff_doc = '','-',a.reff_doc) as reff_doc,a.reff_date,if(a.deskripsi = '','-',a.deskripsi) as deskripsi,a.amount,a.ded_add,a.due_date, nama_pc from tbl_pv a inner join mastercoa_v2 b on b.no_coa = a.coa left join b_master_cc c on c.no_cc = a.no_cc LEFT JOIN master_pc pc on pc.kode_pc = a.profit_center where no_pv = '$no_pv' and amount != '0' OR no_pv = '$no_pv' and ded_add != '0' order by a.id asc");

$table = '<div style="overflow-x: auto;">
    <table id="mytdmodal" class="table table-hover table-bordered pv-detail-table" cellspacing="0" width="100%" style="text-align:center; min-width: 1000px;">
                    <thead>
                        <tr>                       
                            <th style="width:15%;">Coa Name</th>
                            <th style="width:12%;">Profit Center</th>
                            <th style="width:12%;">Cost Center</th>
                            <th style="width:11%;">Reff Doc</th>
                            <th style="width:10%;">Reff Date</th>                                               
                            <th style="width:18%;">Deskripsi</th>
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
                            <td style="" value="'.$row['nama_pc'].'">'.$row['nama_pc'].'</td>
                            <td style="" value="'.$row['cc_name'].'">'.$row['cc_name'].'</td>
                            <td style="" value="'.$row['reff_doc'].'">'.$row['reff_doc'].'</td> 
                            <td style="" value="'.$reff_date.'">'.$reff_date.'</td>                                                                      
                            <td style="" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>                            
                            <td style="text-align: right" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
                            <td style="text-align: right" value="'.$row['ded_add'].'">'.number_format($row['ded_add'],2).'</td>                            
                            <td style="" value="'.$due_date.'">'.$due_date.'</td>
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table> </div>';

echo $table;

$sql2 = mysqli_query($conn1,"select no_pv,subtotal,adjust,pph,ppn,total,status,create_by,create_date,approve_by,approve_date,cancel_by,cancel_date,update_by,update_date from tbl_pv_h where no_pv = '$no_pv'");
$row2 = mysqli_fetch_assoc($sql2);

echo '<table class="pv-detail-totals" width="100%" border="0">

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
             '.number_format($row2['pph'],2).'
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

// Riwayat - dibuat/disetujui/dibatalkan oleh siapa & kapan. Datanya sudah
// lama tersimpan di tbl_pv_h (diisi oleh insertpv_h.php/update_pv_h.php,
// approvepv.php, cancelpv.php) - di sini cuma ditampilkan.
function fmt_log_date($v) {
    if (empty($v) || strtotime($v) <= 0 || date('Y-m-d', strtotime($v)) === '1970-01-01') {
        return null;
    }
    return date('d-M-Y H:i', strtotime($v));
}

// icon + warna per jenis aksi, dipakai buat timeline di bawah.
$logMeta = [
    'Created'     => ['icon' => 'fa-plus-circle',    'color' => '#2563eb'],
    'Approved'    => ['icon' => 'fa-check-circle',   'color' => '#16a34a'],
    'Last Edited' => ['icon' => 'fa-pencil',         'color' => '#f59e0b'],
    'Cancelled'   => ['icon' => 'fa-times-circle',   'color' => '#dc2626'],
];

$logRows = [];
$createdAt = fmt_log_date($row2['create_date'] ?? null);
if (!empty($row2['create_by']) || $createdAt) {
    $logRows[] = ['Created', $row2['create_by'] ?? '-', $createdAt ?? '-'];
}
$approvedAt = fmt_log_date($row2['approve_date'] ?? null);
if (!empty($row2['approve_by']) || $approvedAt) {
    $logRows[] = ['Approved', $row2['approve_by'] ?? '-', $approvedAt ?? '-'];
}
$updatedAt = fmt_log_date($row2['update_date'] ?? null);
if (!empty($row2['update_by']) || $updatedAt) {
    $logRows[] = ['Last Edited', $row2['update_by'] ?? '-', $updatedAt ?? '-'];
}
$cancelledAt = fmt_log_date($row2['cancel_date'] ?? null);
if (!empty($row2['cancel_by']) || $cancelledAt) {
    $logRows[] = ['Cancelled', $row2['cancel_by'] ?? '-', $cancelledAt ?? '-'];
}

if (!empty($logRows)) {
    echo '<div class="pv-detail-log mt-3" style="border-top:1px solid #e2e8f0; padding-top:14px;">
        <div style="font-weight:700; font-size:12px; color:#1e3a8a; margin-bottom:12px; letter-spacing:.03em; text-transform:uppercase;"><i class="fa fa-history"></i> History</div>
        <div class="pv-timeline">';
    $lastIdx = count($logRows) - 1;
    foreach ($logRows as $i => $lr) {
        [$action, $by, $when] = $lr;
        $meta = $logMeta[$action] ?? ['icon' => 'fa-circle', 'color' => '#64748b'];
        $isLast = ($i === $lastIdx);
        echo '<div class="pv-timeline-item" style="display:flex; gap:12px; ' . ($isLast ? '' : 'padding-bottom:16px;') . '">
            <div style="display:flex; flex-direction:column; align-items:center;">
                <div style="width:28px; height:28px; border-radius:50%; background:' . $meta['color'] . '; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; box-shadow:0 2px 6px rgba(0,0,0,.15); flex-shrink:0;"><i class="fa ' . $meta['icon'] . '"></i></div>'
                . ($isLast ? '' : '<div style="flex:1; width:2px; background:#e2e8f0; margin-top:2px;"></div>') .
            '</div>
            <div style="padding-top:3px;">
                <div style="font-size:12px; font-weight:700; color:#0f172a;">' . htmlspecialchars($action) . '</div>
                <div style="font-size:11px; color:#64748b;">by <strong style="color:#334155;">' . htmlspecialchars($by) . '</strong> &middot; ' . htmlspecialchars($when) . '</div>
            </div>
        </div>';
    }
    echo '</div>
    </div>';
}
?>
