<?php
include '../conn/conn.php';

$total = 0;
$tahun = date("Y"); 
$filter = isset($_POST['filter']) ? $_POST['filter'] : null;

function acc_format($angka){
    if ($angka < 0) {
        return '<span style="color:red;">(Rp. '.number_format(abs($angka),2).')</span>';
    } else {
        return 'Rp. '.number_format($angka,2);
    }
}

$sql = mysqli_query($conn1,"
    SELECT a.nama_coa, SUM($filter) total 
    FROM b_trial_balance_$tahun a 
    INNER JOIN mastercoa_v2 b ON b.no_coa = a.no_coa 
    WHERE ind_categori5 IN ('BANK','KAS')  and $filter > 0
    GROUP BY a.no_coa 
    ORDER BY a.nama_coa ASC
"); 

$table = '
<table id="mytdmodal" class="table table-striped table-bordered" width="100%" style="font-size:12px;">
<thead style="background-color:#1E90FF; color:white;">
<tr>
    <th width="5%">#</th>
    <th style="text-align:left;">Nama COA</th>
    <th style="text-align:right;">Amount</th>
</tr>
</thead>
<tbody>
';

while ($row = mysqli_fetch_assoc($sql)) {

    $total += $row['total'];

    $table .= '
    <tr>
        <td><i class="fa fa-dot-circle-o"></i></td>
        <td style="text-align:left;">'.$row['nama_coa'].'</td>
        <td style="text-align:right;">'.acc_format($row['total']).'</td>
    </tr>';
}

$table .= '
<tr style="background:#f2f2f2; font-weight:bold;">
    <td colspan="2" style="text-align:center">TOTAL</td>
    <td style="text-align:right;">'.acc_format($total).'</td>
</tr>
</tbody>
</table>
';

echo $table;
?>
