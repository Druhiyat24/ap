<?php
/* ============================================================================
   Sumber data DataTables untuk halaman daftar FTR DP (ftrdp.php).

   Kembaran ajx_ftrcbd.php - bedanya cuma tabel & kolom angkanya: FTR DP
   menampilkan Total PO / DP Amount / Balance, bukan SubTotal / Tax / Total.
   ============================================================================ */
include '../../conn/conn.php';
header('Content-Type: application/json');

$nama_supp = isset($_POST['nama_supp']) ? trim($_POST['nama_supp']) : 'ALL';
$status_f  = isset($_POST['status'])    ? trim($_POST['status'])    : 'ALL';
$start_in  = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
$end_in    = isset($_POST['end_date'])   ? trim($_POST['end_date'])   : '';
$user      = isset($_POST['user']) ? trim($_POST['user']) : '';

/* Tanggal kosong sengaja dijadikan 1970-01-01, persis seperti halaman lama:
   di sana date('Y-m-d', strtotime('')) memang menghasilkan tanggal itu, dan
   nilai itulah yg dipakai sbg penanda "tanpa batas tanggal". Ditulis tegas di
   sini supaya tidak bergantung pada perilaku strtotime('') antar versi PHP. */
$start_date = ($start_in === '') ? '1970-01-01' : date('Y-m-d', strtotime($start_in));
$end_date   = ($end_in   === '') ? '1970-01-01' : date('Y-m-d', strtotime($end_in));

$esc = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, $v); };

/* Isian filter SELALU yang berlaku. Saat halaman dibuka, From & To sudah
   berisi tanggal hari ini, jadi "hari ini saja" terbaca langsung dari
   isiannya. */
$syarat = array();
if ($nama_supp !== '' && $nama_supp !== 'ALL') {
    $syarat[] = "supp = '" . $esc($nama_supp) . "'";
}
if ($status_f !== '' && $status_f !== 'ALL') {
    $syarat[] = "status = '" . $esc($status_f) . "'";
}
/* Kedua tanggal 1970 = kedua isian tanggal dikosongkan user -> tanpa batas
   tanggal. Ini menirukan delapan cabang if/elseif di halaman lama. */
if (!($start_date === '1970-01-01' && $end_date === '1970-01-01')) {
    $syarat[] = "tgl_ftr_dp between '" . $esc($start_date) . "' and '" . $esc($end_date) . "'";
}
$where = empty($syarat) ? '' : ('where ' . implode(' and ', $syarat));

/* no_po dikumpulkan dgn GROUP_CONCAT, bukan diambil apa adanya: barisnya
   dikelompokkan per nomor FTR, jadi no_po polos hanya mengembalikan SALAH SATU
   PO-nya tanpa penanda apa pun kalau dokumennya memuat beberapa PO. */
$sql = mysqli_query($conn2, "select no_ftr_dp, tgl_ftr_dp, supp,
        GROUP_CONCAT(DISTINCT no_po ORDER BY no_po SEPARATOR ', ') as no_po,
        SUM(total) as total,
        SUM(dp_value) as dp,
        SUM(total - dp_value) as balance,
        curr, create_user, status, keterangan
    from ftr_dp
    $where
    group by no_ftr_dp
    order by tgl_ftr_dp desc, no_ftr_dp desc");

if (!$sql) {
    http_response_code(500);
    echo json_encode(array('data' => array(), 'error' => mysqli_error($conn2)));
    exit;
}

/* Hak akses ditanyakan SEKALI, bukan per baris. Kalau query-nya gagal atau
   username-nya tidak ketemu, $pur tetap kosong - artinya kolom Action dibiarkan
   kosong, bukan memunculkan tombol yang tidak berhak dipakai. */
$qhak = mysqli_query($conn1, "select Groupp, purchasing, approve_po from userpassword where username = '" . mysqli_real_escape_string($conn1, $user) . "'");
$rs    = $qhak ? mysqli_fetch_array($qhak) : null;
$group = isset($rs['Groupp'])     ? $rs['Groupp']     : '';
$pur   = isset($rs['purchasing']) ? $rs['purchasing'] : '';

$data = array();
while ($row = mysqli_fetch_assoc($sql)) {
    $no     = $row['no_ftr_dp'];
    $status = $row['status'];

    /* Tautan Pdf dirakit sekali - dipakai beberapa cabang di bawah. */
    $pdf = '<a class="ftl-mini is-pdf" target="_blank" title="Open the printable PDF"'
         . ' href="pdf_ftrdp.php?noftrdp=' . htmlspecialchars($no, ENT_QUOTES) . '">'
         . '<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Pdf</a>';

    $aksi = '';
    if ($pur == '1') {
        $aksi .= '<div class="ftl-act">';
        if ($status == 'draft' && $group != 'STAFF') {
            /* Pdf IKUT di sini: dokumen draft perlu bisa dicetak untuk
               diperiksa dulu sebelum di-approve. */
            $aksi .= '<a class="ftl-mini is-approve" href="javascript:void(0)" title="Approve this FTR">'
                   . '<i class="fa fa-paper-plane" aria-hidden="true"></i> Approve</a>'
                   . '<a class="ftl-mini is-cancel" href="javascript:void(0)" title="Cancel this FTR">'
                   . '<i class="fa fa-trash" aria-hidden="true"></i> Cancel</a>'
                   . '<a class="ftl-mini is-edit" title="Edit this draft"'
                   . ' href="edit_ftrdp.php?no=' . base64_encode($no) . '">'
                   . '<i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>'
                   . $pdf;
        } elseif ($status == 'draft' || $status == 'Approved') {
            $aksi .= $pdf;
        } elseif ($status == 'Cancel') {
            $aksi .= '<span class="ftl-badge"><i class="fa fa-ban" aria-hidden="true"></i> Canceled</span>';
        }
        $aksi .= '</div>';
    }

    /* Tiap nilai yg urutannya penting dikirim DUA RUPA: satu untuk ditampilkan,
       satu untuk diurutkan. Tanpa ini "01-Oct-2026" diurutkan sbg teks dan
       Januari 2027 mendarat di atas Oktober 2026. */
    $data[] = array(
        'no_ftr_dp'   => $no,
        'tgl_urut'    => $row['tgl_ftr_dp'],
        'tgl_tampil'  => !empty($row['tgl_ftr_dp']) ? date('d-M-Y', strtotime($row['tgl_ftr_dp'])) : '-',
        'supp'        => $row['supp'],
        'no_po'       => $row['no_po'],
        'total'       => number_format((float) $row['total'], 2),
        'total_n'     => (float) $row['total'],
        'dp'          => number_format((float) $row['dp'], 2),
        'dp_n'        => (float) $row['dp'],
        'balance'     => number_format((float) $row['balance'], 2),
        'balance_n'   => (float) $row['balance'],
        'curr'        => $row['curr'],
        'create_user' => $row['create_user'],
        'status'      => $status,
        'status_html' => '<span class="ftl-st is-' . strtolower($status) . '">' . htmlspecialchars($status, ENT_QUOTES) . '</span>',
        'keterangan'  => $row['keterangan'],
        'action'      => $aksi,
    );
}

echo json_encode(array('data' => $data));
