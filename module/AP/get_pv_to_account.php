<?php
include '../../conn/conn.php';

// Dipakai oleh create-paymentvoucher.php untuk mengisi ulang dropdown "To
// Account" tanpa reload halaman ketika For Payment / Supplier berubah.
// Sumber data-nya berbeda tergantung For Payment - sengaja dipertahankan
// sama persis seperti query lama yang sebelumnya tertanam di halaman:
// - Pemindah Bukuan Bank & Cicilan Pinjaman Bank -> rekening PERUSAHAAN (b_masterbank)
// - Lainnya & default (kosong)                   -> rekening SUPPLIER (master_supplier_bank), difilter nama_supp

$forpay = $_POST['forpay'] ?? '';
$nama_supp = $_POST['nama_supp'] ?? '';
$nama_supp_esc = mysqli_real_escape_string($conn2, $nama_supp);

// to_source (opsional) dikirim oleh create-paymentvoucher.php sejak For
// Payment-nya pakai master_cash_flow (id numerik, bukan teks lagi) supaya
// endpoint ini tidak perlu tahu id mana yang berarti "rekening perusahaan".
// Caller lama (create-paymentvoucher-exim.php, edit-paymentvoucher.php)
// tidak mengirim param ini sehingga tetap pakai perbandingan teks lama.
$to_source = $_POST['to_source'] ?? '';

$data = [];

// Supplier "PT. NIRWANA ALABARE GARMENT" adalah PT sendiri (internal), bukan
// supplier eksternal - tidak akan pernah ada rekeningnya di
// master_supplier_bank, jadi To Account-nya disamakan dengan From Account
// (rekening PERUSAHAAN di b_masterbank), sama seperti kategori Pemindahbukuan/
// Pinjaman Bank di bawah. Dicek lebih dulu supaya menang di atas to_source/
// forpay apapun yang dikirim caller.
$OWN_COMPANY_SUPPLIER = 'PT. NIRWANA ALABARE GARMENT';

$useCompanyAccount = (trim($nama_supp) === $OWN_COMPANY_SUPPLIER)
    ? true
    : (($to_source !== '')
        ? ($to_source === 'company')
        : ($forpay === 'Pemindah Bukuan Bank' || $forpay === 'Cicilan Pinjaman Bank'));

if ($useCompanyAccount) {
    $sql = mysqli_query($conn1, "select coa_name as bank, bank_account as akun from b_masterbank where status = 'Active' group by id");
    while ($row = mysqli_fetch_assoc($sql)) {
        $data[] = ['value' => $row['akun'], 'text' => $row['bank']];
    }
} else {
    if ($nama_supp === '' ) {
        $sql = mysqli_query($conn2, "SELECT ms.Supplier AS supplier, CONCAT(UPPER(TRIM(m.bank_name)),' ',UPPER(TRIM(m.bank_account))) AS bank, UPPER(TRIM(m.bank_account)) AS akun FROM master_supplier_bank m JOIN mastersupplier ms ON ms.id_supplier = m.id_supplier WHERE m.status = 'Active' AND m.tipe_sup = 'S' ORDER BY ms.Supplier, m.bank_name");
    } else {
        $sql = mysqli_query($conn2, "SELECT ms.Supplier AS supplier, CONCAT(UPPER(TRIM(m.bank_name)),' ',UPPER(TRIM(m.bank_account))) AS bank, UPPER(TRIM(m.bank_account)) AS akun FROM master_supplier_bank m JOIN mastersupplier ms ON ms.id_supplier = m.id_supplier WHERE m.status = 'Active' AND m.tipe_sup = 'S' AND ms.Supplier = '$nama_supp_esc' ORDER BY m.bank_name");
    }
    while ($row = mysqli_fetch_assoc($sql)) {
        $data[] = ['value' => $row['akun'], 'text' => $row['bank']];
    }
}

echo json_encode($data);
