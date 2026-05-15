<?php
include "../../../conn/conn.php";

$end_date = date("Y-m-d",strtotime($_POST['end_date'])) ?? date('Y-m-d');

$sql = mysqli_query($conn1,"
    select CONCAT(
        UPPER(SUBSTR(nama_bulan_singkat,1,1)),
        LOWER(SUBSTR(nama_bulan_singkat,2)),
        ' ', tahun
    ) bulan_tahun
    from dim_date
    where kode_tanggal between
        concat(year('$end_date'),lpad(month('$end_date'),2,'0'),'01')
        and concat(
            if(month('$end_date')+5>12,year('$end_date')+1,year('$end_date')),
            lpad(if(month('$end_date')+5>12,mod(month('$end_date')+5,12),month('$end_date')+5),2,'0'),
            '01'
        )
    group by bulan,tahun
    order by kode_tanggal asc
");

$data = [];
while($r = mysqli_fetch_assoc($sql)){
    $data[] = $r['bulan_tahun'];
}

echo json_encode($data);
