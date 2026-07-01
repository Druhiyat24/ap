<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon_h = $_POST['no_kbon_h'];
$tgl_kbon_h = date("Y-m-d",strtotime($_POST['tgl_kbon_h']));
$nama_supp_h = $_POST['nama_supp_h'];
$no_faktur_h = $_POST['no_faktur_h'];
$supp_inv_h = $_POST['supp_inv_h'];
$tgl_inv_h = date("Y-m-d",strtotime($_POST['tgl_inv_h']));
$tgl_tempo_h = date("Y-m-d",strtotime($_POST['tgl_tempo_h']));
$curr_h = $_POST['curr_h'];
$create_date = date("Y-m-d H:i:s");
$post_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'draft';
$create_user_h = $_POST['create_user_h'];
$sub_h = $_POST['sub_h'];
$tax_h = $_POST['tax_h'];
$total_h = $_POST['total_h'];
$balance = $tax_h;
$profit_center = $_POST['profit_center'] ?? '';
$bank_account = isset($_POST['bank_account']) ? $_POST['bank_account'] : '';
$id_bank_account = ($bank_account !== '' && $bank_account !== '-') ? (int) $bank_account : 'NULL';
$from_account = mysqli_real_escape_string($conn2, isset($_POST['from_account']) ? $_POST['from_account'] : '');
$from_bank = mysqli_real_escape_string($conn2, isset($_POST['from_bank']) ? $_POST['from_bank'] : '');
$from_bank_curr = mysqli_real_escape_string($conn2, isset($_POST['from_bank_curr']) ? $_POST['from_bank_curr'] : '');
$item_type = mysqli_real_escape_string($conn2, isset($_POST['item_type']) ? $_POST['item_type'] : '');

// No_coa/nama_coa ditentukan dari Item Type yang dipilih user + area supplier
// (Lokal/Impor dari mastersupplier.area) - dipetakan lewat pv_mapping_jurnal_dp.
$no_coa = '-';
$nama_coa = '-';
if ($item_type !== '') {
    $sqlArea = mysqli_query($conn2, "select area from mastersupplier where Supplier = '" . mysqli_real_escape_string($conn2, $nama_supp_h) . "' limit 1");
    $rowArea = mysqli_fetch_assoc($sqlArea);
    $area = $rowArea['area'] ?? '';

    if ($area !== '') {
        $sqlCoa = mysqli_query($conn2, "select no_coa, nama_coa from pv_mapping_jurnal_dp where status = 'Y' and item_type = '$item_type' and area = '" . mysqli_real_escape_string($conn2, $area) . "' limit 1");
        $rowCoa = mysqli_fetch_assoc($sqlCoa);
        if ($rowCoa) {
            $no_coa = $rowCoa['no_coa'];
            $nama_coa = $rowCoa['nama_coa'];
        }
    }
}

// No_kbon digenerate ulang di sini (bukan memakai value dari form saat dibuka),
// supaya jendela race-condition antar 2 user yang save bersamaan diperkecil -
// sama seperti pola yang sudah dipakai di insertkbon_h_pv_regular/installment.php.
$sqlno = mysqli_query($conn1, "select CONCAT(
    'PV-AP/DP/$profit_center/',
    DATE_FORMAT(CURRENT_DATE(), '%Y'), '/',
    DATE_FORMAT(CURRENT_DATE(), '%m'), '/',
    LPAD(
        COALESCE(MAX(CAST(RIGHT(no_kbon, 5) AS UNSIGNED)), 0) + 1,
        5, '0'
    )
) nomor from kontrabon_h_dp WHERE YEAR(tgl_kbon) = YEAR('$tgl_kbon_h')");
$rowno = mysqli_fetch_array($sqlno);
$no_kbon_h = isset($rowno['nomor']) ? $rowno['nomor'] : $no_kbon_h;

$query = "INSERT INTO kontrabon_h_dp (no_kbon, tgl_kbon, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, dp_value, total, balance, curr, post_date, update_date, status, create_user, create_date, profit_center, id_bank_account, from_account, from_bank, from_bank_curr, item_type, no_coa, nama_coa)
VALUES
	('$no_kbon_h', '$tgl_kbon_h', '$nama_supp_h', '$no_faktur_h', '$supp_inv_h', '$tgl_inv_h', '$tgl_tempo_h', '$sub_h', '$tax_h', '$total_h', '$balance', '$curr_h', '$post_date', '$update_date', '$status', '$create_user_h', '$create_date', '$profit_center', $id_bank_account, '$from_account', '$from_bank', '$from_bank_curr', '$item_type', '$no_coa', '$nama_coa')";
$execute = mysqli_query($conn2,$query);

if($execute){
	echo 'Data Saved Successfully With Payment Voucher Number : '; echo $no_kbon_h;
}

mysqli_close($conn2);
?>
