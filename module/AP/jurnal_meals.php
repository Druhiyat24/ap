<?php
date_default_timezone_set('Asia/Jakarta');

// ==========================
// CONFIG DATABASE

$db1_host = "10.10.5.111";
$db1_user = "root";
$db1_pass = "95*76s^SAl8a";
$db1_name = "hris_nag";

$db2_host = "10.10.5.12";
$db2_user = "root";
$db2_pass = "ERP@S19n4lB1t";
$db2_name = "signalbit_erp";

// ==========================
// TANGGAL H-1
// ==========================
$tanggal = date('Y-m-d', strtotime('-1 day'));

// ==========================
// KONEKSI DATABASE
// ==========================
$conn1 = mysqli_connect($db1_host, $db1_user, $db1_pass, $db1_name);
$conn2 = mysqli_connect($db2_host, $db2_user, $db2_pass, $db2_name);

if (!$conn1 || !$conn2) {
    die("Koneksi database gagal");
}

mysqli_set_charset($conn1, "utf8");
mysqli_set_charset($conn2, "utf8");

mysqli_query($conn1, "SET lc_time_names = 'id_ID'");

// ==========================
// CEK DATA SUDAH ADA ATAU BELUM
// ==========================
$cek = mysqli_query($conn2, "SELECT COUNT(*) as total FROM tbl_list_journal WHERE tgl_journal = '$tanggal' and type_journal = 'MEALS'");
$dataCek = mysqli_fetch_assoc($cek);

$sqlno = mysqli_query($conn2,"select LPAD(COALESCE(MAX(CAST(RIGHT(no_mj, 5) AS UNSIGNED)), 0) + 1,5,'0') no_mj from tbl_memorial_journal where MONTH(mj_date) = MONTH('$tanggal') and YEAR(mj_date) = YEAR('$tanggal')");
$rowno = mysqli_fetch_array($sqlno);
$urutan = $rowno['no_mj'];
$bln =  date("m",strtotime($tanggal));
$thn =  date("y",strtotime($tanggal));
$huruf = "GM/NAG/$bln$thn/";
$kodepay = $huruf . sprintf("%05s", $urutan);

if ($dataCek['total'] > 0) {
    echo "Data tanggal $tanggal sudah ada.\n";
    exit;
}

// ==========================
// QUERY SUMBER
// ==========================
$query = "
WITH
coa as (select no_coa, nama_coa from mastercoa_v2),

cost_center as (select no_cc, cc_name, coa_meals, id_pc from b_master_cc where status != 'Deactive'),

data_meals as (SELECT SUBSTR(a.tanggal,1,7) AS periode, a.tanggal, COALESCE((SELECT d.sub_dept_id FROM department_all d WHERE d.sub_dept_id = a.sub_dept LIMIT 1), 'Unknown') AS sub_dept_id, COALESCE((SELECT d.sub_dept_name FROM department_all d WHERE d.department_id = a.dept AND d.sub_dept_id = a.sub_dept LIMIT 1), 'No Sub Dept') AS sub_dept_name, 'NON STAFF' AS staff_non_staff, COALESCE(a.non_staff,0) AS jumlah_karyawan, CASE WHEN a.keterangan = 'TAKJIL' THEN 5000 ELSE 8000 END AS harga, COALESCE(a.non_staff,0) * CASE WHEN a.keterangan = 'TAKJIL' THEN 5000 ELSE 8000 END AS jumlah, a.keterangan AS shift FROM estimasi_anggaran_makan a WHERE a.tanggal BETWEEN '$tanggal' AND '$tanggal' 
UNION ALL 
SELECT SUBSTR(a.tanggal,1,7) AS periode, a.tanggal, COALESCE((SELECT d.sub_dept_id FROM department_all d WHERE d.sub_dept_id = a.sub_dept LIMIT 1), 'Unknown' ) AS sub_dept_id, COALESCE((SELECT d.sub_dept_name FROM department_all d WHERE d.department_id = a.dept AND d.sub_dept_id = a.sub_dept LIMIT 1), 'No Sub Dept' ) AS sub_dept_name, 'STAFF' AS staff_non_staff, COALESCE(a.staff,0) AS jumlah_karyawan, CASE WHEN a.keterangan = 'TAKJIL' THEN 5000 ELSE 10000 END AS harga, COALESCE(a.staff,0) * CASE WHEN a.keterangan = 'TAKJIL' THEN 5000 ELSE 10000 END AS jumlah, a.keterangan AS shift FROM estimasi_anggaran_makan a WHERE a.tanggal BETWEEN '$tanggal' AND '$tanggal' ORDER BY   tanggal,shift,   sub_dept_id, sub_dept_name, staff_non_staff),

debit as (select '' id, '$kodepay' no_journal, tanggal tgl_journal, 'MEALS' type_journal, no_coa, nama_coa, no_cc no_costcenter, cc_name nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, 'IDR' curr, 1 rate, sum(jumlah) debit, 0 credit, sum(jumlah) debit_idr, 0 credit_idr, 'Post' status, CONCAT('BIAYA KONSUMSI ',staff_non_staff,' DEPT ', cc_name,' ', sum(jumlah_karyawan), ' ORANG (',UPPER(DATE_FORMAT('$tanggal', '%W %d %M %Y')),')') keterangan, '' create_by, CURRENT_TIMESTAMP() create_date, '' approve_by,  CURRENT_TIMESTAMP() approve_date, '' cancel_by, '' cancel_date,  CURRENT_TIMESTAMP() created_at,  CURRENT_TIMESTAMP() updated_at, id_pc profit_center from data_meals a INNER JOIN cost_center b on b.no_cc = a.sub_dept_id INNER JOIN coa c on c.no_coa = b.coa_meals GROUP BY no_cc, no_coa, id_pc),

credit as (select '' id, '$kodepay' no_journal, tanggal tgl_journal, 'MEALS' type_journal, '2.51.23' no_coa, 'BIAYA YANG MASIH HARUS DIBAYAR - KONSUMSI' nama_coa, '-' no_costcenter, '-' nama_costcenter, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, 'IDR' curr, 1 rate, 0 debit, sum(jumlah) credit, 0 debit_idr, sum(jumlah) credit_idr, 'Post' status, CONCAT('BIAYA KONSUMSI ', sum(jumlah_karyawan), ' ORANG (',UPPER(DATE_FORMAT('$tanggal', '%W %d %M %Y')),')') keterangan, '' create_by, CURRENT_TIMESTAMP() create_date, '' approve_by,  CURRENT_TIMESTAMP() approve_date, '' cancel_by, '' cancel_date,  CURRENT_TIMESTAMP() created_at,  CURRENT_TIMESTAMP() updated_at, id_pc profit_center from data_meals a INNER JOIN cost_center b on b.no_cc = a.sub_dept_id INNER JOIN coa c on c.no_coa = b.coa_meals GROUP BY id_pc)

select * from debit UNION ALL select * from credit

";

$result = mysqli_query($conn1, $query);

$query2 = "
WITH
coa as (select no_coa, nama_coa from mastercoa_v2),

cost_center as (select no_cc, cc_name, coa_meals, id_pc from b_master_cc where status != 'Deactive'),

data_meals as (SELECT SUBSTR(a.tanggal,1,7) AS periode, a.tanggal, COALESCE((SELECT d.sub_dept_id FROM department_all d WHERE d.sub_dept_id = a.sub_dept LIMIT 1), 'Unknown') AS sub_dept_id, COALESCE((SELECT d.sub_dept_name FROM department_all d WHERE d.department_id = a.dept AND d.sub_dept_id = a.sub_dept LIMIT 1), 'No Sub Dept') AS sub_dept_name, 'NON STAFF' AS staff_non_staff, COALESCE(a.non_staff,0) AS jumlah_karyawan, CASE WHEN a.keterangan = 'TAKJIL' THEN 5000 ELSE 8000 END AS harga, COALESCE(a.non_staff,0) * CASE WHEN a.keterangan = 'TAKJIL' THEN 5000 ELSE 8000 END AS jumlah, a.keterangan AS shift FROM estimasi_anggaran_makan a WHERE a.tanggal BETWEEN '$tanggal' AND '$tanggal' 
UNION ALL 
SELECT SUBSTR(a.tanggal,1,7) AS periode, a.tanggal, COALESCE((SELECT d.sub_dept_id FROM department_all d WHERE d.sub_dept_id = a.sub_dept LIMIT 1), 'Unknown' ) AS sub_dept_id, COALESCE((SELECT d.sub_dept_name FROM department_all d WHERE d.department_id = a.dept AND d.sub_dept_id = a.sub_dept LIMIT 1), 'No Sub Dept' ) AS sub_dept_name, 'STAFF' AS staff_non_staff, COALESCE(a.staff,0) AS jumlah_karyawan, CASE WHEN a.keterangan = 'TAKJIL' THEN 5000 ELSE 10000 END AS harga, COALESCE(a.staff,0) * CASE WHEN a.keterangan = 'TAKJIL' THEN 5000 ELSE 10000 END AS jumlah, a.keterangan AS shift FROM estimasi_anggaran_makan a WHERE a.tanggal BETWEEN '$tanggal' AND '$tanggal' ORDER BY   tanggal,shift,   sub_dept_id, sub_dept_name, staff_non_staff),

debit as (select '' id, '$kodepay' no_mj , tanggal mj_date, 'CMJ020' id_cmj, no_coa, no_cc no_costcenter, '-' no_reff, '' reff_date, '-' buyer, '-' no_ws, 'IDR' curr, 1 rate, sum(jumlah) debit, 0 credit, sum(jumlah) debit_idr, 0 credit_idr, 'Post' status, CONCAT('BIAYA KONSUMSI ',staff_non_staff,' DEPT ', cc_name,' ', sum(jumlah_karyawan), ' ORANG (',UPPER(DATE_FORMAT('$tanggal', '%W %d %M %Y')),')') keterangan, '' create_by, CURRENT_TIMESTAMP() create_date, '' post_by,  CURRENT_TIMESTAMP() post_date, '' cancel_by, '' cancel_date, id_pc profit_center from data_meals a INNER JOIN cost_center b on b.no_cc = a.sub_dept_id INNER JOIN coa c on c.no_coa = b.coa_meals GROUP BY no_cc, no_coa, id_pc),

credit as (select '' id, '$kodepay' no_mj, tanggal mj_date, 'CMJ020' id_cmj, '2.51.23' no_coa, '-' no_costcenter, '-' no_reff, '' reff_date, '-' buyer, '-' no_ws, 'IDR' curr, 1 rate, 0 debit, sum(jumlah) credit, 0 debit_idr, sum(jumlah) credit_idr, 'Post' status, CONCAT('BIAYA KONSUMSI ', sum(jumlah_karyawan), ' ORANG (',UPPER(DATE_FORMAT('$tanggal', '%W %d %M %Y')),')') keterangan, '' create_by, CURRENT_TIMESTAMP() create_date, '' post_by,  CURRENT_TIMESTAMP() post_date, '' cancel_by, '' cancel_date, id_pc profit_center from data_meals a INNER JOIN cost_center b on b.no_cc = a.sub_dept_id INNER JOIN coa c on c.no_coa = b.coa_meals GROUP BY id_pc)

select * from debit UNION ALL select * from credit

";

$result2 = mysqli_query($conn1, $query2);

if (!$result) {
    file_put_contents("log_error.txt", mysqli_error($conn1), FILE_APPEND);
    die("Query gagal");
}

if (!$result2) {
    file_put_contents("log_error.txt", mysqli_error($conn1), FILE_APPEND);
    die("Query gagal");
}

// ==========================
// INSERT KE SERVER TUJUAN
// ==========================
mysqli_begin_transaction($conn2);

while ($row = mysqli_fetch_assoc($result)) {

    // Hapus id kalau auto increment
    unset($row['id']);

    $columns = implode(",", array_keys($row));

    $escaped_values = [];

    foreach ($row as $value) {

        // Jika NULL atau string kosong → jadikan NULL
        if ($value === null || $value === '') {
            $escaped_values[] = "NULL";
        }

        // Jika numeric → jangan pakai tanda kutip
        elseif (is_numeric($value)) {
            $escaped_values[] = $value;
        }

        // Selain itu → escape dan pakai tanda kutip
        else {
            $escaped_values[] = "'" . mysqli_real_escape_string($conn2, $value) . "'";
        }
    }

    $values = implode(",", $escaped_values);

    $insert = "INSERT INTO tbl_list_journal ($columns) VALUES ($values)";

    if (!mysqli_query($conn2, $insert)) {

    echo "<pre>";
    var_dump(mysqli_errno($conn2));
    var_dump(mysqli_error($conn2));
    echo "</pre>";

    exit;
}

}


while ($row2 = mysqli_fetch_assoc($result2)) {

    // Hapus id kalau auto increment
    unset($row2['id']);

    $columns2 = implode(",", array_keys($row2));

    $escaped_values2 = [];

    foreach ($row2 as $value2) {

        // Jika NULL atau string kosong → jadikan NULL
        if ($value2 === null || $value2 === '') {
            $escaped_values2[] = "NULL";
        }

        // Jika numeric → jangan pakai tanda kutip
        elseif (is_numeric($value2)) {
            $escaped_values2[] = $value2;
        }

        // Selain itu → escape dan pakai tanda kutip
        else {
            $escaped_values2[] = "'" . mysqli_real_escape_string($conn2, $value2) . "'";
        }
    }

    $values2 = implode(",", $escaped_values2);

    $insert2 = "INSERT INTO tbl_memorial_journal ($columns2) VALUES ($values2)";

    if (!mysqli_query($conn2, $insert2)) {

    echo "<pre>";
    var_dump(mysqli_errno($conn2));
    var_dump(mysqli_error($conn2));
    echo "</pre>";

    exit;
}

}


mysqli_commit($conn2);

echo "Sukses insert data tanggal $tanggal\n";

mysqli_close($conn1);
mysqli_close($conn2);
?>
