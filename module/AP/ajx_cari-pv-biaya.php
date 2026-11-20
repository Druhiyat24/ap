<?php
/* ============================================================================
   Update Payment Date PV Biaya - MENCARI vouchernya.
   Dipanggil create-pvbiaya-payment-date.php lewat AJAX, balasannya JSON.

   SUMBER BARISNYA = cabang 'Biaya' milik Bank Out (bank-out/get_pv_ajax.php),
   bukan kontrabon/LP. Cara menghitung nilainya sama, tapi SYARAT STATUSNYA
   SENGAJA DILONGGARKAN - YANG DITOLAK HANYA CANCEL (pemakai 20 Nov 2026:
   "kalau update payment plan diubah saja, status draft masuk"):
     - status_pl TIDAK dibaca, dan Draft pun sudah boleh dijadwalkan - rencana
       bayar memang disusun sebelum approval selesai. Akibatnya daftar di sini
       BISA LEBIH PANJANG daripada yang sudah bisa dibayar lewat Bank Out; itu
       yang dikehendaki. Rencana hanyalah tanggal, tidak membayar apa pun.
     - SECOND APPROVED ikut masuk, dan itu memperbaiki cacat yang lebih besar
       daripada Draft: syarat lama `= 'Approved'` membuat PV yang sudah LULUS
       approval terakhir HILANG dari layar ini.
     - BANK OUT TIDAK DISENTUH (diminta pemakai) - syarat di sana tetap apa
       adanya; yang dilonggarkan cuma penjadwalan & proyeksi.
     - nilainya dihitung dari baris tbl_pv: (amount - ded_add) + ppn - pph
     - dikurangi yang sudah terbayar di b_bankout_det (type_pv 'Biaya',
       dokumen bank out yang belum dibatalkan)
   Dikelompokkan per (no_pv, profit_center) - itu juga pengelompokan Bank Out,
   karena satu PV bisa memuat baris dua profit center.

   AMBANGNYA >= 1 RUPIAH, BUKAN > 0 seperti di Bank Out. Alasannya diukur:
   dari 62 PV yang lolos "> 0" di produksi, 60 di antaranya sisanya DI BAWAH
   Rp 1 - totalnya Rp 0,08 - yaitu debu pembulatan dari pembayaran yang
   sebenarnya sudah lunas. Kalau ikut ditampilkan, layarnya penuh baris palsu
   dan yang benar-benar perlu dijadwalkan tenggelam. Pola ambang yang sama
   sudah dipakai perbaikan "Beban PV".

   PV YANG SUDAH PUNYA RENCANA TETAP MUNCUL (permintaan pemakai): rencananya
   memang harus bisa digeser. Tanggal yang sekarang berlaku ikut dikirim di
   'plan_sekarang' supaya layar bisa mengisinya lebih dulu, dan nomor
   dokumennya di 'plan_doc' supaya kelihatan dari mana asalnya.

   DP & CBD IKUT DIMUAT (18 Nov 2026, permintaan pemakai) - lihat
   pvb_dpcbd_lib.php. Barisnya bentuknya sama persis dgn baris Biaya, cuma
   'jenis'-nya beda, supaya layar & penyimpanannya tidak perlu tahu bedanya.
   Kategori arus kasnya TETAP (Uang Muka Supplier), karena dokumen DP/CBD
   tidak punya kolom kategori sendiri.

   KATEGORI ARUS KAS hanya DIBACA (tbl_pv_h.id_cash_flow). Layar ini tidak
   boleh mengubahnya - kolom itu dipakai juga Bank Out & Cash Flow
   Realisation, jadi satu salah klik bisa memindahkan nominal ke baris
   laporan yang salah tanpa jejak. Yang kosong cukup dikirim kosong.
   ============================================================================ */
session_start();
include '../../conn/conn.php';
require_once __DIR__ . '/pvb_dpcbd_lib.php';
header('Content-Type: application/json');

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

/* ---------------------------------------------------------------------------
   Tanggal dari layar datang sbg "16 Nov 2026". strtotime() membacanya, tapi
   kiriman yang tidak terbaca harus jatuh ke string kosong - BUKAN ke
   1970-01-01, karena itu akan menarik seluruh isi tabel diam-diam.
   --------------------------------------------------------------------------- */
function pvbYmd($teks)
{
    $teks = trim((string) $teks);
    if ($teks === '') { return ''; }
    $t = strtotime($teks);
    return $t === false ? '' : date('Y-m-d', $t);
}

$dasar      = (isset($_POST['dasar']) && $_POST['dasar'] === 'pv') ? 'pv' : 'tempo';
$start_date = pvbYmd(isset($_POST['start_date']) ? $_POST['start_date'] : '');
$end_date   = pvbYmd(isset($_POST['end_date'])   ? $_POST['end_date']   : '');
$supplier   = trim(isset($_POST['supplier']) ? (string) $_POST['supplier'] : '');

if ($start_date === '' || $end_date === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Please fill in both From and To.'));
    exit;
}
if ($start_date > $end_date) {
    echo json_encode(array('status' => 'error', 'message' => 'From cannot be later than To.'));
    exit;
}

$start_esc = mysqli_real_escape_string($conn2, $start_date);
$end_esc   = mysqli_real_escape_string($conn2, $end_date);

/* Saringan tanggal dipasang DI LUAR CTE total_pv, bukan di dalamnya: due_date
   ada di baris tbl_pv dan baru jadi satu nilai sesudah MAX() per grup, jadi
   menyaringnya di dalam akan memotong baris lain milik PV yang sama dan
   membuat nilai totalnya salah. */
$kolomTanggal = ($dasar === 'pv') ? 'a.pv_date' : 'a.due_date';
$saring = array("(a.total - COALESCE(b.total_pv,0)) >= 1");
$saring[] = "$kolomTanggal BETWEEN '$start_esc' AND '$end_esc'";
if ($supplier !== '') {
    $saring[] = "a.nama_supp = '" . mysqli_real_escape_string($conn2, $supplier) . "'";
}
$where = implode(' AND ', $saring);

/* Query-nya PINDAH ke pvb_dpcbd_lib.php (20 Nov 2026) supaya layar ini dan
   mesin proyeksi memakai hitungan sisa yang SAMA PERSIS - dua salinan query
   seperti itu cepat atau lambat berbeda, dan bedanya berupa uang. Di sana
   juga diperbaiki potongan pembayarannya: type_pv boleh NULL, karena kolom
   itu baru diisi sejak 1 Juli 2026. */
$sql = pvb_biaya_sql($where);

$q = mysqli_query($conn2, $sql);
if (!$q) {
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn2)));
    exit;
}

/* ---------------------------------------------------------------------------
   Keterangan pendamping, diambil sekali untuk seluruh hasil - bukan satu
   query per baris:
     $kategori  - nama kategori arus kas (hanya dibaca)
     $bank      - nama rekening, supaya kolom Bank terbaca manusia
     $prioritas - mastersupplier.payment_rating, utk warna baris
     $rencana   - rencana yang SEDANG BERLAKU, utk mengisi kolom Payment Plan
   --------------------------------------------------------------------------- */
$kategori = array();
$qk = mysqli_query($conn2, "select id, nama_subcategory from master_cash_flow");
if ($qk) { while ($r = mysqli_fetch_assoc($qk)) { $kategori[$r['id']] = $r['nama_subcategory']; } }

$bank = array();
$qb = mysqli_query($conn2, "select bank_account, bank_name from b_masterbank");
if ($qb) { while ($r = mysqli_fetch_assoc($qb)) { $bank[$r['bank_account']] = $r['bank_name']; } }

$prioritas = array();
$qp = mysqli_query($conn1, "select Supplier, payment_rating from mastersupplier
                            where payment_rating is not null and payment_rating <> ''");
if ($qp) { while ($r = mysqli_fetch_assoc($qp)) { $prioritas[$r['Supplier']] = strtoupper(trim($r['payment_rating'])); } }

/* Rencana berlaku = baris 'Y' milik dokumen yang belum dibatalkan. Kalau
   (karena apa pun) ada lebih dari satu, yang dipakai yang dokumennya paling
   baru - sama dgn aturan di Payment Plan AP. */
$rencana = array();
$qr = mysqli_query($conn2, "select d.no_pv, d.profit_center, d.plan_date, d.keterangan, h.doc_number
        from ap_pvbiaya_plan_det d
        inner join ap_pvbiaya_plan_h h on h.doc_number = d.doc_number
       where d.status = 'Y' and h.status <> 'CANCEL'
       order by h.id asc");
if ($qr) {
    while ($r = mysqli_fetch_assoc($qr)) {
        $rencana[$r['no_pv'] . '|' . $r['profit_center']] = $r;   // yang terakhir menang
    }
}

$baris = array();
while ($row = mysqli_fetch_assoc($q)) {
    $kunci = $row['no_pv'] . '|' . $row['profit_center'];
    $pl    = isset($rencana[$kunci]) ? $rencana[$kunci] : null;
    $idcf  = (int) $row['id_cash_flow'];
    $rate  = isset($prioritas[$row['nama_supp']]) ? $prioritas[$row['nama_supp']] : '';

    $baris[] = array(
        'jenis'         => 'Biaya',
        'no_pv'         => $row['no_pv'],
        'pv_date'       => $row['pv_date'],
        'due_date'      => $row['due_date'],
        'supplier'      => $row['nama_supp'],
        'profit_center' => $row['profit_center'],
        'curr'          => $row['curr'] !== null && $row['curr'] !== '' ? $row['curr'] : 'IDR',
        'sisa'          => (float) $row['sisa'],
        'akun'          => $row['frm_akun'],
        'bank'          => isset($bank[$row['frm_akun']]) ? $bank[$row['frm_akun']] : $row['frm_akun'],
        /* hanya dibaca - layar menampilkannya sbg teks mati */
        'kategori'      => ($idcf > 0 && isset($kategori[$idcf])) ? $kategori[$idcf] : '',
        'prioritas'     => in_array($rate, array('GREEN', 'YELLOW', 'RED'), true) ? $rate : '',
        'deskripsi'     => (string) $row['deskripsi'],
        'plan_sekarang' => $pl ? $pl['plan_date'] : '',
        /* Tanggal yang SUDAH DIPAKAI laporan proyeksi selama dokumen ini
           belum dijadwalkan: Kamis di minggu jatuh temponya, rumus yang
           sama persis dgn pvb_tanpa_rencana(). Dikirim supaya kotak
           rencana di layar menyarankan tanggal yang SAMA - kalau layar
           menyarankan tanggal lain, pemakai melihat dua tanggal untuk
           satu dokumen tanpa ada yang memberitahu mana yang berlaku. */
        'plan_auto'     => pvb_kamis($row['due_date']),
        'plan_doc'      => $pl ? $pl['doc_number'] : '',
        'plan_ket'      => $pl && $pl['keterangan'] !== null ? $pl['keterangan'] : '',
    );
}

/* ---------------------------------------------------------------------------
   DP & CBD. Saringannya sama dgn di atas (dasar tanggal, periode, supplier),
   dan hasilnya dibentuk persis spt baris Biaya - jadi layar tidak perlu tahu
   bedanya kecuali untuk menampilkan jenisnya.
   --------------------------------------------------------------------------- */
$idKtgDpCbd  = pvb_dpcbd_kategori($conn2);
$namaKtgDpCbd = isset($kategori[$idKtgDpCbd]) ? $kategori[$idKtgDpCbd] : '';

foreach (pvb_dpcbd_daftar($conn2, $dasar, $start_date, $end_date, $supplier) as $row) {
    $kunci = $row['no_pv'] . '|' . $row['profit_center'];
    $pl    = isset($rencana[$kunci]) ? $rencana[$kunci] : null;
    $rate  = isset($prioritas[$row['nama_supp']]) ? $prioritas[$row['nama_supp']] : '';

    $baris[] = array(
        'jenis'         => $row['jenis'],
        'no_pv'         => $row['no_pv'],
        'pv_date'       => $row['pv_date'],
        'due_date'      => $row['due_date'],
        'supplier'      => $row['nama_supp'],
        'profit_center' => $row['profit_center'],
        'curr'          => $row['curr'] !== null && $row['curr'] !== '' ? $row['curr'] : 'IDR',
        'sisa'          => (float) $row['sisa'],
        'akun'          => $row['frm_akun'],
        'bank'          => isset($bank[$row['frm_akun']]) ? $bank[$row['frm_akun']] : $row['frm_akun'],
        'kategori'      => $namaKtgDpCbd,
        'prioritas'     => in_array($rate, array('GREEN', 'YELLOW', 'RED'), true) ? $rate : '',
        'deskripsi'     => '',
        'plan_sekarang' => $pl ? $pl['plan_date'] : '',
        /* Tanggal yang SUDAH DIPAKAI laporan proyeksi selama dokumen ini
           belum dijadwalkan: Kamis di minggu jatuh temponya, rumus yang
           sama persis dgn pvb_tanpa_rencana(). Dikirim supaya kotak
           rencana di layar menyarankan tanggal yang SAMA - kalau layar
           menyarankan tanggal lain, pemakai melihat dua tanggal untuk
           satu dokumen tanpa ada yang memberitahu mana yang berlaku. */
        'plan_auto'     => pvb_kamis($row['due_date']),
        'plan_doc'      => $pl ? $pl['doc_number'] : '',
        'plan_ket'      => $pl && $pl['keterangan'] !== null ? $pl['keterangan'] : '',
    );
}

/* ---------------------------------------------------------------------------
   FTR CBD/DP bercara bayar CASH - tidak pernah jadi PV, jadi dijadwalkan
   pakai nomor FTR-nya sendiri. Kategorinya SAMA dgn jalur kontrabon:
   Pengeluaran Pembayaran Uang Muka Supplier.
   --------------------------------------------------------------------------- */
foreach (pvb_ftrcash_daftar($conn2, $dasar, $start_date, $end_date, $supplier) as $row) {
    $kunci = $row['no_pv'] . '|' . $row['profit_center'];
    $pl    = isset($rencana[$kunci]) ? $rencana[$kunci] : null;
    $rate  = isset($prioritas[$row['nama_supp']]) ? $prioritas[$row['nama_supp']] : '';

    $baris[] = array(
        'jenis'         => $row['jenis'],
        'no_pv'         => $row['no_pv'],
        'pv_date'       => $row['pv_date'],
        'due_date'      => $row['due_date'],
        'supplier'      => $row['nama_supp'],
        'profit_center' => $row['profit_center'],
        'curr'          => $row['curr'] !== null && $row['curr'] !== '' ? $row['curr'] : 'IDR',
        'sisa'          => (float) $row['sisa'],
        'akun'          => $row['frm_akun'],
        /* Kas kecil tidak ada di b_masterbank, jadi yang tampil nomor COA-nya
           sendiri - itu memang yang dikenali pemakai (1.01.01 KAS KECIL). */
        'bank'          => isset($bank[$row['frm_akun']]) ? $bank[$row['frm_akun']] : $row['frm_akun'],
        'kategori'      => $namaKtgDpCbd,
        'prioritas'     => in_array($rate, array('GREEN', 'YELLOW', 'RED'), true) ? $rate : '',
        'deskripsi'     => '',
        'plan_sekarang' => $pl ? $pl['plan_date'] : '',
        /* Tanggal yang SUDAH DIPAKAI laporan proyeksi selama dokumen ini
           belum dijadwalkan: Kamis di minggu jatuh temponya, rumus yang
           sama persis dgn pvb_tanpa_rencana(). Dikirim supaya kotak
           rencana di layar menyarankan tanggal yang SAMA - kalau layar
           menyarankan tanggal lain, pemakai melihat dua tanggal untuk
           satu dokumen tanpa ada yang memberitahu mana yang berlaku. */
        'plan_auto'     => pvb_kamis($row['due_date']),
        'plan_doc'      => $pl ? $pl['doc_number'] : '',
        'plan_ket'      => $pl && $pl['keterangan'] !== null ? $pl['keterangan'] : '',
    );
}

echo json_encode(array(
    'status' => 'success',
    'data'   => $baris,
    'jumlah' => count($baris),
));
