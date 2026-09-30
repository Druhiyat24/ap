<?php
// ============================================================================
// Isi MODAL DETAIL Memorial Journal.
//
// Mengembalikan SELURUH badan modal: panel info + Balance Summary per Profit
// Center + tabel baris jurnal. Dulu berkas ini hanya mengembalikan tabel detail
// sementara panel infonya diisi JavaScript dari baris daftar - itu keliru untuk
// dua kolom: profit_center BERBEDA di dalam satu dokumen pada 26% dokumen, dan
// keterangan pada 60% (ada satu dokumen dgn 613 keterangan untuk 613 baris).
// Menampilkannya sebagai satu nilai di header berarti memajang nilai dari satu
// baris acak seolah berlaku untuk seluruh jurnal. Keduanya kini jadi KOLOM.
//
// Kolom Reff / Reff Date / Buyer / WS DILEPAS: diukur ke produksi (38.671 baris
// jurnal 2026) masing-masing cuma terisi 21% / 0,6% / 4,1% / 0,2% baris, dan per
// dokumen Buyer kosong total di 98% dokumen, WS 99%, Reff 70% - empat kolom
// berisi tanda "-" dari atas ke bawah yang memakan hampir separuh lebar tabel.
//
// Tabelnya dijadikan DataTables (10 baris per halaman) oleh memorial-journal.php
// setelah HTML ini dipasang.
// ============================================================================
include '../../conn/conn.php';

$no_mj = isset($_POST['no_mj']) ? trim((string) $_POST['no_mj']) : '';
$mjEsc = mysqli_real_escape_string($conn1, $no_mj);

$H = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES); };
$N = function ($v) { return number_format((float) $v, 2); };

// ---- Baris jurnal -----------------------------------------------------------
// Nama COA & Cost Center diambil TERPISAH dari kodenya (bukan CONCAT seperti
// dulu) supaya bisa ditampilkan dua baris: kode tebal di atas, nama di bawah.
$sql = mysqli_query($conn1, "select a.no_coa, c.nama_coa, a.no_costcenter, d.cc_name,
        a.profit_center, a.keterangan, a.curr, a.debit, a.credit, a.debit_idr, a.credit_idr
    from tbl_memorial_journal a
    left join mastercoa_v2 c on c.no_coa = a.no_coa
    left join b_master_cc  d on d.no_cc  = a.no_costcenter
    where a.no_mj = '$mjEsc' and a.no_coa != ''");

// ---- Keterangan tingkat dokumen --------------------------------------------
$sqlDoc = mysqli_query($conn1, "select a.mj_date, b.nama_cmj, a.status,
        min(a.create_by) create_by, min(a.create_date) create_date
    from tbl_memorial_journal a
    left join master_category_mj b on b.id_cmj = a.id_cmj
    where a.no_mj = '$mjEsc' group by a.no_mj limit 1");
$doc = $sqlDoc ? mysqli_fetch_assoc($sqlDoc) : null;

// ---- Total per Profit Center ------------------------------------------------
// Dihitung di SINI, bukan di JavaScript: jumlahnya harus mencakup SELURUH baris
// dokumen, sedangkan DataTables cuma memegang halaman yang sedang tampil.
$sqlPc = mysqli_query($conn1, "select profit_center,
        sum(debit) debit, sum(credit) credit,
        sum(debit_idr) debit_idr, sum(credit_idr) credit_idr
    from tbl_memorial_journal where no_mj = '$mjEsc'
    group by profit_center order by profit_center");

$pcRows = [];
$tot = ['debit' => 0, 'credit' => 0, 'debit_idr' => 0, 'credit_idr' => 0];
while ($sqlPc && $p = mysqli_fetch_assoc($sqlPc)) {
    $pcRows[] = $p;
    foreach ($tot as $k => $_) { $tot[$k] += (float) $p[$k]; }
}
$tidakBalance = abs($tot['debit'] - $tot['credit']) >= 0.005;

// ---- Ikon (SVG sebaris; warnanya mengikuti currentColor) --------------------
$ikon = [
'kalender'  => '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
'label'     => '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.6 13.4 12 22l-9-9V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>',
'orang'     => '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
'centang'   => '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.1V12a10 10 0 1 1-5.9-9.1"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
'timbangan' => '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="3" x2="12" y2="21"></line><path d="M3 7h18"></path><path d="M6 7l-3 6a3 3 0 0 0 6 0z"></path><path d="M18 7l3 6a3 3 0 0 1-6 0z"></path></svg>',
'daftar'    => '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>',
];

// Kelas badge status diambil dari DAFTAR PUTIH, bukan dari nilai kolom apa
// adanya - status tak dikenal jatuh ke varian netral.
$statusTeks  = $doc ? (string) $doc['status'] : '';
$petaStatus  = ['post' => 'post', 'draft' => 'draft', 'cancel' => 'cancel'];
$kunciStatus = strtolower(trim($statusTeks));
$statusKelas = isset($petaStatus[$kunciStatus]) ? $petaStatus[$kunciStatus] : 'netral';

$tglDok = ($doc && !empty($doc['mj_date'])) ? date('d-M-Y', strtotime($doc['mj_date'])) : '-';
// create_by kosong = dokumen yang dibentuk proses otomatis (sync FX, import
// HRIS), bukan diketik orang - jadi "System", bukan tanda "-" yang terbaca
// seolah datanya hilang.
$oleh   = $doc ? trim((string) $doc['create_by']) : '';
$olehHtml = $H($oleh !== '' ? $oleh : 'System');
if ($doc && !empty($doc['create_date']) && strtotime($doc['create_date'])) {
    $olehHtml .= ' &middot; ' . $H(date('d-M-Y H:i', strtotime($doc['create_date'])));
}

// ---- Panel info + Balance Summary, BERDAMPINGAN -----------------------------
$out  = '<div class="mjd-atas">';

$out .= '<div class="mjd-panel mjd-info">'
      . '<div class="mjd-info-item"><span class="mjd-ico">' . $ikon['kalender'] . '</span>'
      .   '<span class="mjd-info-teks"><b>Date</b><span>' . $H($tglDok) . '</span></span></div>'
      . '<div class="mjd-info-item"><span class="mjd-ico">' . $ikon['label'] . '</span>'
      .   '<span class="mjd-info-teks"><b>Type</b><span>' . $H($doc ? $doc['nama_cmj'] : '-') . '</span></span></div>'
      . '<div class="mjd-info-item"><span class="mjd-ico">' . $ikon['orang'] . '</span>'
      .   '<span class="mjd-info-teks"><b>Created By</b><span>' . $olehHtml . '</span></span></div>'
      . '<div class="mjd-info-item"><span class="mjd-ico is-status">' . $ikon['centang'] . '</span>'
      .   '<span class="mjd-info-teks"><b>Status</b>'
      .   '<span><span class="mjb-badge ' . $statusKelas . '">' . $H($statusTeks) . '</span></span></span></div>'
      . '</div>';

$out .= '<div class="mjd-panel mjd-bal">'
      . '<span class="mjd-judul">' . $ikon['timbangan'] . ' Balance Summary</span>'
      . '<table class="mjd-bal-tbl"><thead><tr>'
      . '<th>Profit Center</th>'
      . '<th class="num is-deb">Debit</th><th class="num is-cre">Credit</th>'
      . '<th class="num is-deb">Debit IDR</th><th class="num is-cre">Credit IDR</th>'
      . '</tr></thead><tbody>';
foreach ($pcRows as $p) {
    $pc = trim((string) $p['profit_center']);
    $out .= '<tr>'
          . '<td><span class="mjd-pc-tag">' . $H($pc !== '' ? $pc : '-') . '</span></td>'
          . '<td class="num">' . $N($p['debit']) . '</td>'
          . '<td class="num">' . $N($p['credit']) . '</td>'
          . '<td class="num">' . $N($p['debit_idr']) . '</td>'
          . '<td class="num">' . $N($p['credit_idr']) . '</td>'
          . '</tr>';
}
$out .= '<tr class="mjd-bal-total">'
      . '<td>Grand Total</td>'
      . '<td class="num">' . $N($tot['debit']) . '</td>'
      . '<td class="num">' . $N($tot['credit']) . '</td>'
      . '<td class="num">' . $N($tot['debit_idr']) . '</td>'
      . '<td class="num">' . $N($tot['credit_idr']) . '</td>'
      . '</tr></tbody></table>';
$out .= $tidakBalance
      ? '<span class="mjb-badge cancel mjd-lencana" title="Debit and credit do not match">OUT OF BALANCE</span>'
      : '<span class="mjb-badge post mjd-lencana">Balanced</span>';
$out .= '</div></div>';

// ---- Tabel baris jurnal -----------------------------------------------------
$out .= '<span class="mjd-judul mjd-judul-tbl">' . $ikon['daftar'] . ' Journal Lines</span>';
$out .= '<table id="mytdmodal" class="mjd-tbl" style="width:100%"><thead><tr>'
      . '<th>COA</th><th>Cost Center</th><th>PC</th><th>Description</th>'
      . '<th class="num">Debit</th><th class="num">Credit</th>'
      . '<th class="num">Debit IDR</th><th class="num">Credit IDR</th>'
      . '</tr></thead><tbody>';

while ($sql && $row = mysqli_fetch_assoc($sql)) {
    $cc     = trim((string) $row['no_costcenter']);
    $ccNama = trim((string) $row['cc_name']);
    $pc     = trim((string) $row['profit_center']);
    // Nol diredupkan: tiap baris jurnal hanya berisi debit ATAU credit, sisi
    // lain selalu 0.00 - kalau sama pekatnya, angka yang benar-benar ada ikut
    // tenggelam di antara nol.
    $kDeb = ((float) $row['debit']  == 0) ? ' class="num nol"' : ' class="num"';
    $kCre = ((float) $row['credit'] == 0) ? ' class="num nol"' : ' class="num"';
    // Kolom IDR diperlakukan SAMA PERSIS dgn dua kolom di sebelahnya: angkanya
    // pekat, hanya nolnya yang diredupkan. Sebelumnya seluruh kolom IDR abu-abu
    // - untuk transaksi mata uang asing justru nilai IDR inilah yang dibaca.
    $kDebIdr = ((float) $row['debit_idr']  == 0) ? ' class="num nol"' : ' class="num"';
    $kCreIdr = ((float) $row['credit_idr'] == 0) ? ' class="num nol"' : ' class="num"';

    $out .= '<tr>'
          . '<td><span class="mjd-kode">' . $H($row['no_coa']) . '</span>'
          .     '<span class="mjd-nama">' . $H($row['nama_coa']) . '</span></td>'
          . '<td><span class="mjd-kode is-cc">' . $H($cc !== '' ? $cc : '-') . '</span>'
          .     '<span class="mjd-nama">' . $H($ccNama) . '</span></td>'
          . '<td>' . ($pc !== '' ? '<span class="mjd-pc-tag">' . $H($pc) . '</span>' : '') . '</td>'
          . '<td class="mjd-ket">' . $H($row['keterangan']) . '</td>'
          . '<td' . $kDeb . '>' . $N($row['debit']) . '</td>'
          . '<td' . $kCre . '>' . $N($row['credit']) . '</td>'
          . '<td' . $kDebIdr . '>' . $N($row['debit_idr']) . '</td>'
          . '<td' . $kCreIdr . '>' . $N($row['credit_idr']) . '</td>'
          . '</tr>';
}
$out .= '</tbody></table>';

echo $out;
