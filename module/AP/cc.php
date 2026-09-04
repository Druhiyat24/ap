<?php
include '../../conn/conn.php';

header('Content-Type: application/json; charset=utf-8');

$q   = isset($_GET['q'])   ? $_GET['q']   : '';
$pc  = isset($_GET['pc'])  ? $_GET['pc']  : '';
$coa = isset($_GET['coa']) ? $_GET['coa'] : '';
$q   = mysqli_real_escape_string($conn1, $q);
$pc  = mysqli_real_escape_string($conn1, $pc);
$coa = mysqli_real_escape_string($conn1, $coa);

// Cost Center dibatasi 2 lapis (sama seperti getCostCenter.php di Memorial Journal):
//  1) Profit Center baris ini  -> b_master_cc.id_pc = kode_pc
//  2) GRUP COA baris ini       -> b_master_cc.group2 IN (grup dari mastercoa_v2)
// Kalau Profit Center ATAU COA belum dipilih, jangan tampilkan Cost Center apa pun
// dulu - supaya user tidak bisa memilih kombinasi COA/PC/CC yang salah.
if ($pc === '' || $coa === '') {
    echo json_encode([]);
    exit;
}

// Grup yang diizinkan untuk COA ini (flag di mastercoa_v2).
$rg = mysqli_fetch_assoc(mysqli_query($conn1,
    "SELECT TRIM(BOTH ',' FROM CONCAT(
        IF(support_gen_adm='Y','''SUPPORTING GENERAL & ADMINISTRATION'',',''),
        IF(support_prod='Y','''SUPPORTING PRODUCTION'',',''),
        IF(prod='Y','''PRODUCTION'',',''),
        IF(support_sell='Y','''SUPPORTING SELLING'',','')
     )) AS groups
     FROM mastercoa_v2 WHERE no_coa = '$coa'"
));
$groups = ($rg && isset($rg['groups'])) ? $rg['groups'] : '';

// COA tanpa grup (COA neraca) = tidak wajib & tidak punya Cost Center yang valid.
if ($groups === '') {
    echo json_encode([]);
    exit;
}

$sql = mysqli_query($conn1,
    "SELECT
        no_cc AS id,
        CONCAT(no_cc,' - ',cc_name) AS text
     FROM b_master_cc
     WHERE id_pc = '$pc'
       AND status = 'Active'
       AND group2 IN ($groups)
       AND (no_cc LIKE '%$q%' OR cc_name LIKE '%$q%')
     ORDER BY no_cc"
);

$data = array();
while ($sql && $r = mysqli_fetch_assoc($sql)) {
    $data[] = $r;
}

echo json_encode($data);
