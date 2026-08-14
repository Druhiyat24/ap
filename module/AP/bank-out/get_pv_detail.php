<?php
include '../../../conn/conn.php';

$no_pv = $_POST['no_pv'];
$type_pv = !empty($_POST['type_pv']) ? $_POST['type_pv'] : 'Biaya';
$no_pv_esc = mysqli_real_escape_string($conn2, $no_pv);

if ($type_pv === 'Biaya') {
    // id_cash_flow ikut ditarik supaya Bank Out bisa auto-isi Cash Flow
    // Category dari PV-nya langsung (lihat handler .chk_pv di create-out-bank.php).
    $sql = mysqli_query($conn2, "select kode_pc, CONCAT(id_pc,' - ',nama_pc) nama_pc, b.bank_account, b.bank_name, b.curr, b.b_code, a.id_cash_flow from tbl_pv_h a INNER JOIN b_masterbank b on b.bank_account = a.frm_akun INNER JOIN master_pc c on c.kode_pc = b.profit_center_bank where no_pv = '$no_pv_esc'");
} elseif ($type_pv === 'Installment') {
    // No_pv di sini adalah no_kbon_det (cicilan) - from_account/profit_center
    // disimpan di kontrabon_h induknya, bukan di baris cicilan.
    $sql = mysqli_query($conn2, "select h.profit_center kode_pc, CONCAT(pc.id_pc,' - ',pc.nama_pc) nama_pc, b.bank_account, b.bank_name, b.curr, b.b_code
        from kontrabon_h_installment_detail d
        inner join kontrabon_h h on h.no_kbon = d.no_kbon
        LEFT JOIN b_masterbank b on b.bank_account = h.from_account
        LEFT JOIN master_pc pc on pc.kode_pc = h.profit_center
        where d.no_kbon_det = '$no_pv_esc'");
} elseif ($type_pv === 'SaldoAwal') {
    $sql = mysqli_query($conn2, "select h.profit_center kode_pc, CONCAT(pc.id_pc,' - ',pc.nama_pc) nama_pc, b.bank_account, b.bank_name, b.curr, b.b_code
        from ap_saldo_payment_voucher h
        LEFT JOIN b_masterbank b on b.bank_account = h.from_account
        LEFT JOIN master_pc pc on pc.kode_pc = h.profit_center
        where h.no_kbon = '$no_pv_esc'");
} else {
    $table = $type_pv === 'DP' ? 'kontrabon_h_dp' : ($type_pv === 'CBD' ? 'kontrabon_h_cbd' : 'kontrabon_h');
    $sql = mysqli_query($conn2, "select h.profit_center kode_pc, CONCAT(pc.id_pc,' - ',pc.nama_pc) nama_pc, b.bank_account, b.bank_name, b.curr, b.b_code
        from $table h
        LEFT JOIN b_masterbank b on b.bank_account = h.from_account
        LEFT JOIN master_pc pc on pc.kode_pc = h.profit_center
        where h.no_kbon = '$no_pv_esc'");
}

$row = mysqli_fetch_assoc($sql);

echo json_encode([
    'account'        => $row['bank_account'] ?? '',
    'bank'           => $row['bank_name'] ?? '',
    'currency'       => $row['curr'] ?? '',
    'nama_pc'        => $row['nama_pc'] ?? '',
    'profit_center'  => $row['kode_pc'] ?? '',
    'b_code'         => $row['b_code'] ?? '',
    'id_cash_flow'   => $row['id_cash_flow'] ?? ''
]);
