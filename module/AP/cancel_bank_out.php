<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi        = $_POST['no_pay'] ?? '';
$cancel_user  = $_POST['update_user'] ?? '';
$cancel_date  = date("Y-m-d H:i:s");
$status       = 'Cancel';

if ($no_bi === '') {
    echo "Error: no_bankout kosong";
    exit;
}

/* Dilolos-kan SEKALI di sini, jadi seluruh query di bawah yang memakai
   '$no_bi' ikut aman tanpa perlu diubah satu per satu. Nilainya hanya
   dipakai sebagai nilai SQL, tidak pernah ditampilkan balik. */
$no_bi       = mysqli_real_escape_string($conn2, $no_bi);
$cancel_user = mysqli_real_escape_string($conn2, $cancel_user);

$sql = "UPDATE b_bankout_h 
        SET cancel_by='$cancel_user', cancel_date='$cancel_date', status='$status' 
        WHERE no_bankout='$no_bi'";
$query = mysqli_query($conn2, $sql);

if (!$query) {
    echo "Error (b_bankout_h): " . mysqli_error($conn2);
    exit;
}

/* Sudah pernah dibuatkan jurnal balik? Pemeriksaannya TIDAK boleh menyaring
   status: jurnal balik mewarisi status jurnal aslinya, jadi untuk dokumen
   yang sudah di-approve statusnya 'Approved'/'Post', bukan 'Draft'. Dgn
   saringan status='Draft' yg lama, jurnal balik yang sudah ada tidak
   terlihat - dan pembatalan kedua akan menggandakannya. */
$sqlx = mysqli_query($conn1, "
    SELECT no_journal
    FROM tbl_list_journal
    WHERE no_journal='$no_bi'
      AND type_journal LIKE '%Reverse%'
    LIMIT 1
");
$rowx = mysqli_fetch_assoc($sqlx);
$filter_jurnal = $rowx['no_journal'] ?? null;

if ($filter_jurnal === null) {
    /* ====================================================================
       JURNAL BALIK - dulu hanya dibuat untuk baris berstatus 'Draft'.
       Akibatnya: bank out yang SUDAH DI-APPROVE (status jurnalnya
       'Approved' / 'Post') dibatalkan tanpa jurnal balik sama sekali,
       sementara baris aslinya TIDAK pernah dihapus dan TIDAK pernah
       diberi status 'Cancel'. General Ledger hanya menyingkirkan baris
       berstatus 'Cancel' (lihat ajx_get_data_gl.php), jadi dokumen yang
       sudah dibatalkan tetap terhitung penuh di buku besar.
       Ketahuan dari BK/BCA1979/NAG/1026/00276 (dibatalkan 21 Okt 2026,
       jurnalnya masih hidup 4 baris berstatus Approved).

       Saringan statusnya DIBUANG - persis seperti cancelkbon.php, yang
       memang tidak pernah menyaring status. Baris 'Reverse' dikecualikan
       supaya jurnal balik tidak ikut dibalik lagi.
       ==================================================================== */
    $jurnal_balik = mysqli_query($conn2, "
        INSERT INTO tbl_list_journal
        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
         reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
         debit, credit, debit_idr, credit_idr,
         status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date,
         created_at, updated_at, profit_center, supplier)
        SELECT
            no_journal,
            '$cancel_date' AS tgl_journal,
            CONCAT('Reverse ', type_journal) AS type_journal,
            no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date,
            faktur_pajak, tgl_faktur_pajak,
            buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr,
            status, keterangan, create_by, create_date,
            '$cancel_user' AS approve_by,
            CURRENT_TIMESTAMP() AS approve_date,
            cancel_by, cancel_date, created_at, updated_at, profit_center, supplier
        FROM tbl_list_journal
        WHERE no_journal='$no_bi'
          AND type_journal NOT LIKE '%Reverse%'
    ");
}

$sql2 = "INSERT INTO tbl_list_journal_cancel 
         SELECT * FROM tbl_list_journal WHERE no_journal='$no_bi'";
mysqli_query($conn2, $sql2);

mysqli_query($conn2, "UPDATE b_reportbank SET status='$status' WHERE no_doc='$no_bi'");
mysqli_query($conn2, "UPDATE b_bankout_det SET for_balance='0' WHERE no_bankout='$no_bi'");
mysqli_query($conn2, "UPDATE status SET no_pay='', tgl_pay='' WHERE no_pay='$no_bi'");
mysqli_query($conn2, "
    UPDATE tbl_pv_h 
    INNER JOIN b_bankout_det ON b_bankout_det.no_reff = tbl_pv_h.no_pv
    SET tbl_pv_h.outstanding = tbl_pv_h.total
    WHERE b_bankout_det.no_bankout = '$no_bi'
");
mysqli_query($conn2, "
    UPDATE list_payment 
    INNER JOIN b_bankout_det ON b_bankout_det.no_reff = list_payment.no_payment
    SET list_payment.status = 'Closed'
    WHERE b_bankout_det.no_bankout = '$no_bi'
");
mysqli_query($conn2, "
    UPDATE memo_h 
    SET no_bankout='', status='PAYMENT DRAFT' 
    WHERE no_bankout='$no_bi'
");

echo "OK";
mysqli_close($conn2);
exit;
?>
