<?php
/* ============================================================================
   Update Payment Date PV Biaya - MENCARI vouchernya.
   Dipanggil create-pvbiaya-payment-date.php lewat AJAX, balasannya JSON.

   SUMBER BARISNYA = cabang 'Biaya' milik Bank Out (bank-out/get_pv_ajax.php),
   bukan kontrabon/LP. Jadi "PV Biaya" di sini berarti hal yang SAMA PERSIS
   dgn yang bisa dibayar lewat Bank Out:
     - tbl_pv_h.status = 'Approved' DAN status_pl = 'SECOND APPROVED'
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

   KATEGORI ARUS KAS hanya DIBACA (tbl_pv_h.id_cash_flow). Layar ini tidak
   boleh mengubahnya - kolom itu dipakai juga Bank Out & Cash Flow
   Realisation, jadi satu salah klik bisa memindahkan nominal ke baris
   laporan yang salah tanpa jejak. Yang kosong cukup dikirim kosong.
   ============================================================================ */
session_start();
include '../../conn/conn.php';
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

$sql = "
with total_pv as (
  select b.profit_center,
         a.nama_supp, a.no_pv, a.pv_date, max(b.due_date) due_date, a.curr,
         a.frm_akun, a.id_cash_flow, a.deskripsi,
         (SUM(b.amount - b.ded_add)
          + SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100))
          - SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100))) total
    from tbl_pv_h a
    inner join tbl_pv b on b.no_pv = a.no_pv
   where a.status = 'Approved' and a.status_pl = 'SECOND APPROVED'
   group by a.no_pv, b.profit_center),
total_bk as (
  select a.profit_center, a.no_reff, sum(a.total) total_pv
    from b_bankout_det a
   inner join b_bankout_h b on b.no_bankout = a.no_bankout
   where b.status <> 'Cancel' and a.type_pv = 'Biaya'
   group by a.no_reff, a.profit_center)
select a.no_pv, a.pv_date, a.due_date, a.nama_supp, a.curr, a.profit_center,
       a.frm_akun, a.id_cash_flow, a.deskripsi,
       (a.total - COALESCE(b.total_pv,0)) sisa
  from total_pv a
  left join total_bk b on b.no_reff = a.no_pv and b.profit_center = a.profit_center
 where $where
 order by a.due_date asc, a.no_pv asc";

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
        'plan_doc'      => $pl ? $pl['doc_number'] : '',
        'plan_ket'      => $pl && $pl['keterangan'] !== null ? $pl['keterangan'] : '',
    );
}

echo json_encode(array(
    'status' => 'success',
    'data'   => $baris,
    'jumlah' => count($baris),
));
