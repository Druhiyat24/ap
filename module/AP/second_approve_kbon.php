<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon'] : '';
$approve_user = $_SESSION['username'] ?? '';

if ($no_kbon === '') {
    echo 'Error: no_kbon kosong';
    exit;
}

if ($approve_user === '') {
    echo 'Error: Session tidak valid, silahkan login ulang';
    exit;
}

// $querys = mysqli_query($conn1, "select finance, ap_apprv_kb2 from userpassword where username = '" . mysqli_real_escape_string($conn1, $approve_user) . "'");
// $rs = mysqli_fetch_assoc($querys);
$fin = '1';
$app2 = '1';

if ($fin != '1' || $app2 != '1') {
    echo 'Error: Anda tidak memiliki akses untuk Second Approval';
    exit;
}

$no_kbon_esc = mysqli_real_escape_string($conn2, $no_kbon);
$approve_user_esc = mysqli_real_escape_string($conn2, $approve_user);

$sqlCheck = mysqli_query($conn2, "select * from kontrabon where no_kbon = '$no_kbon_esc' limit 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);

if (!$rowCheck) {
    echo 'Error: No Kontrabon tidak ditemukan';
    exit;
}

if ($rowCheck['status'] !== 'FIRST APPROVED') {
    echo 'Error: Status saat ini bukan FIRST APPROVED, tidak bisa dilakukan Second Approval';
    exit;
}

$curr = $rowCheck['curr'];
$confirm_date = date("Y-m-d H:i:s");
$confirm_date2 = date("Y-m-d");
$status = 'SECOND APPROVED';

$sqlx = mysqli_query($conn1, "select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1, "select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
$rowy = mysqli_fetch_array($sqly);
$rate = $rowy['rate'];

$sql = mysqli_query($conn2, "select * from kontrabon where no_kbon = '$no_kbon_esc'");
$execute = null;

if ($curr == 'IDR') {
    while ($row = mysqli_fetch_assoc($sql)) {
        $po = $row['no_po'];
        $bpb = $row['no_bpb'];
        $kbon = $row['no_kbon'];
        $tglkbon = $row['tgl_kbon'];
        $supp_inv = $row['supp_inv'];
        $tgl_inv = $row['tgl_inv'];
        $no_faktur = $row['no_faktur'];
        $tgl_tempo = $row['tgl_tempo'];
        $pph_value = $row['pph_value'];

        $sql1 = "update kontrabon set confirm_user='$approve_user_esc', confirm_date='$confirm_date', second_approve_by='$approve_user_esc', second_approve_date='$confirm_date', status='$status', status_int='4' where no_kbon='$kbon'";
        $execute = mysqli_query($conn2, $sql1);

        mysqli_query($conn2, "update kontrabon_h set confirm_user='$approve_user_esc', confirm_date='$confirm_date', second_approve_user='$approve_user_esc', second_approve_date='$confirm_date', status='$status' where no_kbon='$kbon'");

        mysqli_query($conn2, "update potongan set status = '$status' where no_kbon = '$no_kbon_esc'");

        mysqli_query($conn2, "update return_kb set status = '$status' where no_kbon = '$no_kbon_esc'");

        mysqli_query($conn2, "update kontrabon_ftr set status = 'Cancel' where no_kbon = '$no_kbon_esc'");

        mysqli_query($conn2, "update kartu_hutang set no_kbon='$kbon', tgl_kbon='$tglkbon', supp_inv='$supp_inv', tgl_inv='$tgl_inv', no_faktur='$no_faktur', tgl_tempo='$tgl_tempo', create_date='$confirm_date2', pph='$pph_value' where no_kbon = '$no_kbon_esc'");

        mysqli_query($conn2, "update status set no_kbon='$kbon', tgl_kbon='$tglkbon' where no_kbon = '$no_kbon_esc'");

        mysqli_query($conn2, "update tbl_list_journal set approve_by='$approve_user_esc', approve_date='$confirm_date', status='$status' where no_journal='$no_kbon_esc'");
    }
} else {
    while ($row = mysqli_fetch_assoc($sql)) {
        $po = $row['no_po'];
        $bpb = $row['no_bpb'];
        $kbon = $row['no_kbon'];
        $tglkbon = $row['tgl_kbon'];
        $supp_inv = $row['supp_inv'];
        $tgl_inv = $row['tgl_inv'];
        $no_faktur = $row['no_faktur'];
        $tgl_tempo = $row['tgl_tempo'];
        $pph_value = $row['pph_value'];

        $sqlz = mysqli_query($conn2, "select credit_usd from kartu_hutang where no_bpb = '" . mysqli_real_escape_string($conn2, $bpb) . "' and no_po = '" . mysqli_real_escape_string($conn2, $po) . "' and no_kbon = '$kbon'");
        $rowz = mysqli_fetch_array($sqlz);
        $credit_usd = $rowz['credit_usd'];
        $credit_idr = $credit_usd * $rate;

        $sql1 = "update kontrabon set confirm_user='$approve_user_esc', confirm_date='$confirm_date', second_approve_by='$approve_user_esc', second_approve_date='$confirm_date', status='$status', status_int='4' where no_kbon='$kbon'";
        $execute = mysqli_query($conn2, $sql1);

        mysqli_query($conn2, "update kontrabon_h set confirm_user='$approve_user_esc', confirm_date='$confirm_date', second_approve_user='$approve_user_esc', second_approve_date='$confirm_date', status='$status' where no_kbon='$kbon'");

        mysqli_query($conn2, "update potongan set status = '$status' where no_kbon = '$no_kbon_esc'");

        mysqli_query($conn2, "update return_kb set status = '$status' where no_kbon = '$no_kbon_esc'");

        mysqli_query($conn2, "update kartu_hutang set no_kbon='$kbon', tgl_kbon='$tglkbon', supp_inv='$supp_inv', tgl_inv='$tgl_inv', no_faktur='$no_faktur', tgl_tempo='$tgl_tempo', create_date='$confirm_date2', pph='$pph_value', rate='$rate', credit_idr='$credit_idr' where no_kbon = '$no_kbon_esc'");

        mysqli_query($conn2, "update status set no_kbon='$kbon', tgl_kbon='$tglkbon' where no_kbon = '$no_kbon_esc'");

        mysqli_query($conn2, "update tbl_list_journal set approve_by='$approve_user_esc', approve_date='$confirm_date', status='$status' where no_journal='$no_kbon_esc'");
    }
}

if (!$execute) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

echo 'Data Berhasil Di Second Approve';

mysqli_close($conn2);
?>
