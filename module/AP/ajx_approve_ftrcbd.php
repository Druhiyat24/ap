<?php
/* ============================================================================
   Sumber data DataTables untuk HALAMAN PERSETUJUAN FTR CBD
   (approve_ftrcbd.php).

   Bedanya dgn ajx_ftrcbd.php: di sini status DIPAKU ke 'draft'. Pilihan
   Status di layar tidak bisa membukanya - dokumen yang sudah Approved atau
   Cancel memang tidak ada urusannya dgn halaman persetujuan.
   Tombolnya pun hanya Approve / Cancel / Pdf, tanpa Edit.
   ============================================================================ */
/* ============================================================================
   Sumber data DataTables untuk halaman daftar FTR CBD (ftrcbd.php).

   Sebelumnya baris-baris tabel dicetak langsung di dalam ftrcbd.php, sehingga
   setiap kali filter diubah SELURUH halaman dimuat ulang lewat POST. Sekarang
   hanya datanya yang ditarik, jadi filter terasa seketika dan bisa diberi
   penanda "sedang memuat".

   Pola & bentuk keluarannya mengikuti ajx_memorial-journal.php supaya kedua
   menu ini berperilaku sama.
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

/* Isian filter SELALU yang berlaku - tidak ada lagi perlakuan khusus untuk
   muat pertama. Saat halaman dibuka, From & To sudah berisi tanggal hari ini,
   jadi "hari ini saja" terbaca langsung dari isiannya dan user bisa melihat
   sendiri rentang mana yang sedang dipakai. */
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
    $syarat[] = "tgl_ftr_cbd between '" . $esc($start_date) . "' and '" . $esc($end_date) . "'";
}
/* Status DIPAKU: halaman persetujuan hanya mengurus dokumen draft.
   Ditaruh sesudah $syarat disusun supaya pilihan Status di layar tidak
   bisa menimpanya. */
$syarat[] = "status = 'draft'";
$where = 'where ' . implode(' and ', $syarat);

/* biaya_tambahan IKUT dijumlahkan di semua kombinasi filter. Di halaman lama
   satu cabang (Supplier tertentu + Status tertentu + tanpa rentang tanggal)
   memakai SUM(subtotal) dan SUM(total) polos tanpa biaya_tambahan, sehingga
   dokumen yang sama menampilkan Total berbeda hanya karena filternya berbeda. */
$sql = mysqli_query($conn2, "select no_ftr_cbd, tgl_ftr_cbd, supp,
        GROUP_CONCAT(DISTINCT no_po ORDER BY no_po SEPARATOR ', ') as no_po,
        SUM(subtotal + biaya_tambahan) as subtotal,
        SUM(tax) as tax,
        SUM(total + biaya_tambahan) as total,
        curr, create_user, status, keterangan
    from ftr_cbd
    $where
    group by no_ftr_cbd
    order by tgl_ftr_cbd desc, no_ftr_cbd desc");

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
    $no     = $row['no_ftr_cbd'];
    $status = $row['status'];

    /* Tautan Pdf dirakit sekali - dipakai beberapa cabang di bawah. */
    $pdf = '<a class="ftl-mini is-pdf" target="_blank" title="Open the printable PDF"'
         . ' href="pdf_ftrcbd.php?noftrcbd=' . htmlspecialchars($no, ENT_QUOTES) . '">'
         . '<i class="fa fa-file-pdf-o" aria-hidden="true"></i> Pdf</a>';

    $aksi = '';
    if ($pur == '1') {
        $aksi .= '<div class="ftl-act">';
        /* Semua baris di halaman ini pasti draft (lihat penyaring di atas).
           Approve tetap khusus non-STAFF; Cancel terbuka utk semua pemakai
           purchasing, sama seperti di halaman daftarnya. Edit TIDAK ada di
           sini - mengubah isi dokumen dikerjakan dari halaman daftar. */
        if ($group != 'STAFF') {
            $aksi .= '<a class="ftl-mini is-approve" href="javascript:void(0)" title="Approve this FTR">'
                   . '<i class="fa fa-paper-plane" aria-hidden="true"></i> Approve</a>';
        }
        $aksi .= '<a class="ftl-mini is-cancel" href="javascript:void(0)" title="Cancel this FTR">'
               . '<i class="fa fa-trash" aria-hidden="true"></i> Cancel</a>'
               . $pdf;
        $aksi .= '</div>';
    }

    /* Tiap nilai yg urutannya penting dikirim DUA RUPA: satu untuk ditampilkan,
       satu untuk diurutkan. Tanpa ini "01-Oct-2026" diurutkan sbg teks dan
       Januari 2027 mendarat di atas Oktober 2026. */
    $data[] = array(
        'no_ftr_cbd'  => $no,
        'tgl_urut'    => $row['tgl_ftr_cbd'],
        'tgl_tampil'  => !empty($row['tgl_ftr_cbd']) ? date('d-M-Y', strtotime($row['tgl_ftr_cbd'])) : '-',
        'supp'        => $row['supp'],
        'no_po'       => $row['no_po'],
        'subtotal'    => number_format((float) $row['subtotal'], 2),
        'subtotal_n'  => (float) $row['subtotal'],
        'tax'         => number_format((float) $row['tax'], 2),
        'tax_n'       => (float) $row['tax'],
        'total'       => number_format((float) $row['total'], 2),
        'total_n'     => (float) $row['total'],
        'curr'        => $row['curr'],
        'create_user' => $row['create_user'],
        'status'      => $status,
        'status_html' => '<span class="ftl-st is-' . strtolower($status) . '">' . htmlspecialchars($status, ENT_QUOTES) . '</span>',
        'keterangan'  => $row['keterangan'],
        'action'      => $aksi,
    );
}

echo json_encode(array('data' => $data));
