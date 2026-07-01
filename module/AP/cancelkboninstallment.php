<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

// Twin of cancelkbon.php - BPB/RO/FTR/jurnal untuk Installment disimpan di
// tabel yang sama seperti Regular (tidak dipecah per cicilan), jadi efek
// cancelnya sama. Bedanya: cancel di sini juga membatalkan SEMUA baris anak
// di kontrabon_h_installment_detail, supaya cancel salah satu cicilan otomatis
// membatalkan seluruh cicilan lain pada Payment Voucher yang sama.

$no_kbon = $_POST['no_kbon'];
$cancel_user = $_POST['cancel_user'];
$cancel_date = date("Y-m-d H:i:s");
$status_int = 1;

if (isset($no_kbon)) {
    $sql = "update bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb set bpb_new.is_invoiced = 'Waiting' where kontrabon.no_kbon = '$no_kbon'";
    $execute = mysqli_query($conn2, $sql);

    $sqlx = mysqli_query($conn1, "select no_journal from tbl_list_journal where no_journal='$no_kbon' and type_journal like '%Reverse%'");
    $rowx = mysqli_fetch_array($sqlx);
    $filter_jurnal = isset($rowx['no_journal']) ? $rowx['no_journal'] : null;

    if ($filter_jurnal == null) {
        $jurnal_balik = mysqli_query($conn2, "INSERT into tbl_list_journal select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, status, keterangan, create_by, create_date, '$cancel_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$no_kbon'");
    }
} else {
    die('Error: ' . mysqli_error());
}

if ($execute) {
    $query = mysqli_query($conn2, "update kontrabon set status = 'Cancel', status_int = '$status_int', cancel_user = '$cancel_user', cancel_date = '$cancel_date' where no_kbon = '$no_kbon'");
    $query2 = mysqli_query($conn2, "update kontrabon_h set status = 'Cancel', cancel_user = '$cancel_user', cancel_date = '$cancel_date' where no_kbon = '$no_kbon'");
    $query3 = mysqli_query($conn2, "update potongan set status = 'Cancel' where no_kbon = '$no_kbon'");

    $query4 = mysqli_query($conn2, "update bppb_new set is_invoiced = 'Waiting', no_kbon = null where no_kbon = '$no_kbon'");

    $query5 = mysqli_query($conn2, "update kontrabon_ftr set status = 'Cancel' where no_kbon = '$no_kbon'");

    $sql111 = "update kartu_hutang set no_kbon='-' where no_kbon = '$no_kbon'";
    $query111 = mysqli_query($conn2, $sql111);

    $sqlac = "update status set no_kbon = null where no_kbon = '$no_kbon'";
    $queryac = mysqli_query($conn2, $sqlac);

    $sql59 = "update return_kb set status='Cancel' where no_kbon = '$no_kbon'";
    $query59 = mysqli_query($conn2, $sql59);

    // Cancel SEMUA baris cicilan (anak) yang menempel ke Payment Voucher induk ini.
    $query6 = mysqli_query($conn2, "update kontrabon_h_installment_detail set status = 'Cancel' where no_kbon = '$no_kbon'");
}

echo 'Data Berhasil Di Cancel';

mysqli_close($conn2);
?>
