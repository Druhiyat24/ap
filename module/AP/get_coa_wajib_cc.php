<?php
include '../../conn/conn.php';

$data = [];

$sql = "
    SELECT no_coa 
    FROM mastercoa_v2 
    WHERE support_gen_adm = 'Y' 
       OR support_prod = 'Y' 
       OR prod = 'Y' 
       OR support_sell = 'Y'
";

$q = mysqli_query($conn1, $sql);

while($r = mysqli_fetch_assoc($q)){
    $data[] = $r['no_coa'];
}

header('Content-Type: application/json');
echo json_encode($data);
