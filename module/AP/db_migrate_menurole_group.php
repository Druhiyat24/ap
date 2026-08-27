<?php
// Migrasi ADITIF (display-only): isi menurole.menu_group untuk SEMUA menu
// mengikuti section navbar atas (Master / AP / Bank / Cash / Accounting /
// Cost Accounting / Exim / Reverse). Mapping diturunkan dari penempatan asli
// di header.php (strpos($id,'N') per section) + konvensi nama menu.
//
// HANYA menyentuh kolom menu_group (tampilan modal Manage User Role). Key
// `menu`, status, dan hak akses TIDAK diubah. Idempoten - aman diulang.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

$groups = [
    'Master' => [49],
    'AP' => [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,30,31,32,33,
             57,58,59,60,61,66,67,68,69,70,71,72,73,75,76,77,78,79,80,86,87,89,91,
             109,113,114,115,116,119,130,131],
    'Bank' => [36,37,41,42,43,62,63,81,117,118],
    'Cash' => [38,39,44,45,46,47],
    'Accounting' => [40,50,51,52,53,54,55,56,64,65,82,88,90,104,105,106,107,108,132,133,134],
    'Cost Accounting' => [85,110,111,112],
    'Exim' => [83,84],
    'Reverse' => [34,74,92,93,94,95,96,97,98,99,100,101,102,103,120,121,122,123,124,126,127,128,129],
];

$total = 0;
foreach ($groups as $grp => $ids) {
    $grp_esc = mysqli_real_escape_string($conn2, $grp);
    $idList = implode(',', array_map('intval', $ids));
    $ok = mysqli_query($conn2, "UPDATE menurole SET menu_group = '$grp_esc' WHERE id IN ($idList)");
    $n = $ok ? mysqli_affected_rows($conn2) : 0;
    $total += count($ids);
    echo $ok ? "OK: $grp -> " . count($ids) . " id (rows updated=$n)\n"
             : "ERR $grp: " . mysqli_error($conn2) . "\n";
}

echo "\nTotal id dipetakan: $total\n";
echo "\n=== Cek menu yang BELUM punya group (selain id 35 Development) ===\n";
$r = mysqli_query($conn2, "SELECT id, menu FROM menurole WHERE (menu_group IS NULL OR menu_group = '') AND id != 35 ORDER BY id");
$sisa = 0;
while ($x = mysqli_fetch_assoc($r)) { echo "  id={$x['id']}  '{$x['menu']}'\n"; $sisa++; }
if ($sisa === 0) echo "  (semua sudah ter-group)\n";

echo "\n=== Ringkasan jumlah per group ===\n";
$r = mysqli_query($conn2, "SELECT COALESCE(menu_group,'(kosong)') g, COUNT(*) c FROM menurole GROUP BY menu_group ORDER BY c DESC");
while ($x = mysqli_fetch_assoc($r)) echo "  " . str_pad($x['g'],18) . " : {$x['c']}\n";
mysqli_close($conn2);
