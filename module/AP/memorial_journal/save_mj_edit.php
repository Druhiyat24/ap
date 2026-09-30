<?php
// ============================================================================
// save_mj_edit.php — SIMPAN hasil edit Memorial Journal (SATU request, SATU
// transaksi) untuk halaman edit_memorial_journal_new.php.
//
// Menggantikan rangkaian lama copy_data_mj.php + insert_memorial_journal_edit.php
// yang dipanggil SATU AJAX PER BARIS dari edit-memorial-journal.php. Berkas lama
// TIDAK disentuh — halaman edit lama masih memakainya.
//
// Kenapa ditulis ulang, bukan ditambal:
//
//  1. TANPA TRANSAKSI. Alur lama menghapus seluruh baris lebih dulu
//     (copy_data_mj.php), baru mengirim N request insert. Dokumen terbesar di
//     database 672 baris -> 672 request. Kalau browser ditutup atau sebagian
//     request gagal di tengah, jurnalnya tinggal separuh, dan tidak ada yang
//     mengembalikan. Di sini: hapus + tulis ulang ada di dalam SATU transaksi,
//     gagal di mana pun = rollback penuh, dokumen kembali utuh.
//
//  2. BUKU SB1 TIDAK IKUT. Keempat jalur create (input, HRIS, upload, PPN)
//     menulis ke sb_memorial_journal + sb_list_journal + status_memorial_journal
//     kalau SB I dicentang. Alur edit lama tidak menyentuh satu pun, jadi buku SB
//     membeku di nilai lama sementara buku utama berubah. Di sini kedua buku
//     selalu ditulis ulang bersama-sama.
//
//  3. KURS SATU UNTUK SEMUA BARIS. Form lama punya satu field txt_rate global;
//     baris EUR ikut dikalikan kurs USD. Ini persis bug yang sudah diperbaiki di
//     jalur create lewat mj_resolve_rate(). Di sini helper yang sama dipakai
//     PER BARIS, jadi perbaikannya tidak hilang begitu dokumen diedit.
//
//  4. NO FAKTUR / TGL FAKTUR / SUPPLIER HILANG. Ketiganya hanya ada di
//     tbl_list_journal (keputusan user, lihat catatan di save_mj_upload_fix.php).
//     Form lama membaca baris dari tbl_memorial_journal yang tidak punya kolom
//     itu, lalu menimpa tbl_list_journal — sehingga nilainya terhapus setiap kali
//     dokumen diedit. Halaman baru memuat baris dari tbl_list_journal dan
//     mengirim ketiganya kembali ke sini.
//
//  5. STATUS DIPAKU 'Post'. Alur lama menulis status = "Post" apa adanya.
//     Di sini status dokumen dipertahankan apa adanya.
// ============================================================================

include '../../../conn/conn.php';
require_once __DIR__ . '/mj_rate_helper.php';
require_once __DIR__ . '/../closing_periode_guard.php';

session_start();
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');

$e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };

function balas($arr) { echo json_encode($arr); exit; }
function gagal($pesan, $kode = 'ERROR') { balas(['status' => 'error', 'kode' => $kode, 'message' => $pesan]); }

/* ========================================================================
   1. AMBIL & PERIKSA KIRIMAN
   ======================================================================== */

$no_mj   = trim((string) ($_POST['no_mj'] ?? ''));
$id_cmj  = trim((string) ($_POST['id_cmj'] ?? ''));
$sb1_on  = (string) ($_POST['fil_sb1'] ?? '') === '1';
$user    = $_SESSION['username'] ?? 'system';
$now     = date('Y-m-d H:i:s');

if ($no_mj === '') { gagal('Nomor journal tidak dikirim.'); }
if ($id_cmj === '') { gagal('Type journal wajib dipilih.'); }

$tgl_raw = trim((string) ($_POST['mj_date'] ?? ''));
$ts = $tgl_raw !== '' ? strtotime(str_replace('/', '-', $tgl_raw)) : false;
if (!$ts) { gagal('Tanggal journal tidak terbaca.'); }
$mj_date = date('Y-m-d', $ts);

// Baris dikirim sebagai SATU string JSON, bukan array input bernama. Dokumen
// 672 baris x 15 field = 10.080 nama input; JSON-nya jauh lebih ringan dan tidak
// menabrak max_input_vars (bawaan PHP 1000 - dokumen besar akan terpotong diam
// diam kalau dikirim sebagai array form biasa).
$baris = json_decode((string) ($_POST['baris'] ?? ''), true);
if (!is_array($baris) || count($baris) === 0) { gagal('Tidak ada baris journal yang dikirim.'); }

/* ---- Validasi tiap baris + saldo --------------------------------------
   Saldo diukur pada NILAI IDR, bukan nominal aslinya: journal bermata uang
   campuran tidak akan pernah balance pada nominal asli. Untuk journal yang
   seluruhnya IDR keduanya sama saja, karena kursnya 1.

   Kursnya diselesaikan DI SINI (sekali per baris) lalu disimpan ke $baris
   supaya loop penulisan di bawah tidak perlu mencarinya lagi. */
$totDebitIdr = 0.0; $totCreditIdr = 0.0; $totDebit = 0.0;
foreach ($baris as $i => $b) {
    $noBaris = $i + 1;
    if (trim((string) ($b['no_coa'] ?? '')) === '') { gagal('Baris ' . $noBaris . ': COA belum dipilih.'); }
    $pc = trim((string) ($b['profit_center'] ?? ''));
    if ($pc === '' || $pc === '-') { gagal('Baris ' . $noBaris . ': Profit Center belum dipilih.'); }

    $curr = strtoupper(trim((string) ($b['curr'] ?? 'IDR')));
    $kirim = (isset($b['rate']) && is_numeric($b['rate'])) ? (float) $b['rate'] : null;
    $rate  = mj_resolve_rate($conn2, $curr, $mj_date, $kirim);

    // Kurs 1 untuk mata uang asing membuat nilai IDR-nya meleset ribuan kali
    // lipat. Diperiksa DI SERVER juga, bukan cuma di halaman - endpoint ini
    // bisa dipanggil langsung.
    if ($curr !== 'IDR' && $rate <= 1) {
        gagal('Baris ' . $noBaris . ': kurs ' . $curr . ' masih ' . $rate . '. Isi kursnya lebih dulu.');
    }

    $d = (float) ($b['debit'] ?? 0);
    $c = (float) ($b['credit'] ?? 0);
    $baris[$i]['_rate'] = $rate;
    $totDebit     += $d;
    $totDebitIdr  += $d * $rate;
    $totCreditIdr += $c * $rate;
}
// Ambang 0,005 menyamai pembulatan 2 desimal yang dipakai di seluruh aplikasi -
// perbandingan float langsung (==) akan menolak dokumen yang sebenarnya balance.
if (abs($totDebitIdr - $totCreditIdr) >= 0.005) {
    gagal('Debit dan Credit tidak balance dalam IDR ('
        . number_format($totDebitIdr, 2) . ' vs ' . number_format($totCreditIdr, 2) . ').');
}
if ($totDebit <= 0) { gagal('Total journal masih nol.'); }

/* ========================================================================
   2. TRANSAKSI
   ======================================================================== */

mysqli_begin_transaction($conn2);

try {
    /* ---- 2a. Kunci dokumen & baca keadaannya sekarang ------------------
       FOR UPDATE: tanpa ini dua user yang membuka dokumen sama bisa sama-sama
       lolos ke tahap hapus-lalu-tulis, dan yang selesai belakangan menimpa
       pekerjaan yang pertama tanpa jejak. */
    $qDoc = mysqli_query($conn2, "select status, mj_date, create_by, create_date, post_by, post_date
        from tbl_memorial_journal where no_mj = '" . $e($no_mj) . "' order by id limit 1 for update");
    $doc = $qDoc ? mysqli_fetch_assoc($qDoc) : null;
    if (!$doc) { throw new Exception('Journal ' . $no_mj . ' tidak ditemukan (mungkin sudah dihapus user lain).'); }

    if (strcasecmp(trim((string) $doc['status']), 'Cancel') === 0) {
        throw new Exception('Journal ' . $no_mj . ' sudah di-cancel dan tidak bisa diedit.');
    }

    // Status, pembuat & tanggal buat DIPERTAHANKAN. Yang mengedit tercatat di
    // tbl_log_edit_mj / sb_log_edit_mj, jadi tidak perlu menimpa penulis asli.
    $status      = (string) $doc['status'];
    $create_by   = (string) $doc['create_by'];
    $create_date = (string) $doc['create_date'];
    $post_by     = (string) $doc['post_by'];
    $post_date   = ($doc['post_date'] !== null && $doc['post_date'] !== '0000-00-00 00:00:00')
                 ? "'" . $e($doc['post_date']) . "'" : 'NULL';
    $tgl_lama    = (string) $doc['mj_date'];

    /* ---- 2b. Closing periode: tanggal LAMA dan BARU --------------------
       Dua-duanya diperiksa. Memeriksa yang baru saja masih memungkinkan baris
       ditarik KELUAR dari periode yang sudah ditutup - saldo periode tertutup
       itu ikut berubah, padahal justru itu yang dilarang. */
    foreach ([['baru', $mj_date], ['lama', $tgl_lama]] as $cek) {
        $c = closing_check($conn2, $cek[1]);
        if (!$c['ok']) {
            $detail = $c['kode_periode'] !== ''
                ? 'Periode ' . $c['kode_periode'] . ' (' . date('M Y', strtotime($cek[1])) . ') sudah ditutup.'
                : 'Tanggal ' . date('d M Y', strtotime($cek[1])) . ' berada di periode yang sudah ditutup.';
            mysqli_rollback($conn2);
            balas(['status' => 'error', 'kode' => 'CLOSING',
                   'message' => 'Journal tidak bisa disimpan karena tanggal ' . $cek[0] . ' ada di periode tertutup.',
                   'detail' => $detail]);
        }
    }

    /* ---- 2c. Nama type untuk tbl_list_journal.type_journal ------------- */
    $qCmj = mysqli_query($conn2, "select nama_cmj from master_category_mj where id_cmj = '" . $e($id_cmj) . "' limit 1");
    $rCmj = $qCmj ? mysqli_fetch_assoc($qCmj) : null;
    if (!$rCmj) { throw new Exception('Type journal "' . $id_cmj . '" tidak dikenal.'); }
    $nama_cmj = (string) $rCmj['nama_cmj'];

    /* ---- 2d. Pasangan SB1 dokumen ini ----------------------------------
       Pasangannya TIDAK selalu tercatat di status_memorial_journal. Diukur ke
       data (4.440 dokumen):

         - 49 dokumen ber-awalan FX/ (jurnal selisih kurs, dibuat SCHEDULER)
           disalin OTOMATIS ke buku SB dgn NOMOR YANG SAMA, tanpa lewat
           verifikasi, dan TIDAK pernah tercatat di status_memorial_journal.
           Salinannya cuma ada di sb_list_journal - sb_memorial_journal kosong
           untuk seluruh 49-nya.
         - 1.620 dokumen GM/ warisan lama juga punya baris SB ber-nomor sama
           tanpa peta (dibuat insert_memorial_journal.php sebelum tabel peta
           ada).

       Kalau pasangannya dicari HANYA lewat peta, dokumen-dokumen itu dianggap
       tidak punya SB: baris SB lamanya tidak pernah ditulis ulang (membeku di
       nilai lama), dan kalau user mencentang SB I malah dibuatkan nomor BARU
       sehingga dokumennya punya dua salinan SB sekaligus.

       Urutan penentuannya:
         1. Ada peta        -> itu yang dipakai (paling sahih, alur verifikasi).
         2. Tidak ada peta, tapi ada baris SB ber-nomor SAMA -> pasangannya
            adalah nomor dokumen itu sendiri.
         3. Tidak dua-duanya -> memang belum punya pasangan. */
    $qSb = mysqli_query($conn2, "select no_mj_sb from status_memorial_journal
        where no_mj = '" . $e($no_mj) . "' order by id limit 1");
    $rSb = $qSb ? mysqli_fetch_assoc($qSb) : null;
    $no_mj_sb   = $rSb ? (string) $rSb['no_mj_sb'] : '';
    $sb1_punya_peta = ($no_mj_sb !== '');

    // Berapa baris SB yang benar-benar ada, per tabel. Dipakai dua kali:
    // untuk MENGENALI pasangan tanpa peta, dan untuk memutuskan tabel mana saja
    // yang boleh ditulis ulang.
    $hitungSb = function ($nomor) use ($conn2, $e) {
        if ($nomor === '') { return ['mj' => 0, 'list' => 0]; }
        $a = mysqli_fetch_assoc(mysqli_query($conn2,
            "select count(*) n from sb_memorial_journal where no_mj = '" . $e($nomor) . "'"));
        $b = mysqli_fetch_assoc(mysqli_query($conn2,
            "select count(*) n from sb_list_journal where no_journal = '" . $e($nomor) . "'"));
        return ['mj' => (int) $a['n'], 'list' => (int) $b['n']];
    };

    if (!$sb1_punya_peta) {
        $cek = $hitungSb($no_mj);
        if ($cek['mj'] > 0 || $cek['list'] > 0) { $no_mj_sb = $no_mj; }
    }
    $sb1_semula = ($no_mj_sb !== '');
    $sbAda = $hitungSb($no_mj_sb);

    // Tabel mana yang ditulis ulang. Untuk pasangan yang SUDAH ADA, hanya tabel
    // yang memang sudah berisi - dokumen FX tidak punya baris di
    // sb_memorial_journal sama sekali, dan membuatkannya sekarang berarti
    // memunculkan baris yang tidak pernah ada di laporan mana pun.
    // Untuk pasangan BARU (user baru mencentang), keduanya ditulis, sama
    // seperti jalur create.
    $tulisSbMj   = $sb1_semula ? ($sbAda['mj'] > 0)   : true;
    $tulisSbList = $sb1_semula ? ($sbAda['list'] > 0) : true;

    // Baru dicentang sekarang -> dokumen SB perlu nomor sendiri. Pola penomoran
    // disamakan dgn jalur create: urutan TERPISAH, dihitung dari sb_memorial_journal.
    if ($sb1_on && !$sb1_semula) {
        $prefix = 'GM/NAG/' . date('m', $ts) . date('y', $ts);
        $qMax = mysqli_query($conn2, "select max(cast(right(no_mj,5) as unsigned)) mx
            from sb_memorial_journal where no_mj like '" . $e($prefix) . "%'");
        $rMax = $qMax ? mysqli_fetch_assoc($qMax) : null;
        $no_mj_sb = $prefix . '/' . sprintf('%05d', (int) ($rMax['mx'] ?? 0) + 1);
        $tulisSbMj = true; $tulisSbList = true;
    }

    /* ---- 2e. Arsipkan yang lama ----------------------------------------
       Sama persis dgn yang dilakukan copy_data_mj.php untuk buku utama, plus
       kembarannya untuk buku SB yang selama ini terlewat.

       Kolom ditulis SATU PER SATU, tidak "select *": tbl_edit_mj dan sb_edit_mj
       punya update_by/update_date di tempat cancel_by/cancel_date, dan sb_edit_mj
       sama sekali tidak punya profit_center. "select *" di copy_data_mj.php lolos
       hanya karena jumlah kolomnya kebetulan sama - nilai cancel_by mendarat di
       update_by. Untuk sb_edit_mj jumlahnya BEDA (25 vs 26), jadi "select *"
       akan gagal. */
    $kolomEditMj = 'no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws,
        curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date,
        post_by, post_date, update_by, update_date';

    $q = "insert into tbl_edit_mj ($kolomEditMj, profit_center)
          select no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws,
                 curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date,
                 post_by, post_date, '" . $e($user) . "', '" . $e($now) . "', profit_center
          from tbl_memorial_journal where no_mj = '" . $e($no_mj) . "'";
    if (!mysqli_query($conn2, $q)) { throw new Exception('Gagal mengarsipkan tbl_memorial_journal: ' . mysqli_error($conn2)); }

    if (!mysqli_query($conn2, "insert into tbl_list_journal_cancel
            (select * from tbl_list_journal where no_journal = '" . $e($no_mj) . "')")) {
        throw new Exception('Gagal mengarsipkan tbl_list_journal: ' . mysqli_error($conn2));
    }

    if (!mysqli_query($conn2, "insert into tbl_log_edit_mj (no_mj, user_edit, tgl_edit)
            values ('" . $e($no_mj) . "', '" . $e($user) . "', '" . $e($now) . "')")) {
        throw new Exception('Gagal menulis log edit: ' . mysqli_error($conn2));
    }

    // Diarsipkan HANYA dari tabel yang memang berisi. Dokumen FX/ punya baris
    // di sb_list_journal saja - menjalankan arsip sb_memorial_journal untuknya
    // cuma menyalin nol baris, tapi log SB-nya jadi mencatat sesuatu yang tidak
    // pernah ada.
    if ($sb1_semula && $sbAda['mj'] > 0) {
        // sb_edit_mj TIDAK punya profit_center - nilainya memang tidak ikut
        // terarsip. Bentuk tabelnya begitu; yang penting angkanya utuh.
        $q = "insert into sb_edit_mj ($kolomEditMj, asal_data)
              select no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws,
                     curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date,
                     post_by, post_date, '" . $e($user) . "', '" . $e($now) . "', asal_data
              from sb_memorial_journal where no_mj = '" . $e($no_mj_sb) . "'";
        if (!mysqli_query($conn2, $q)) { throw new Exception('Gagal mengarsipkan sb_memorial_journal: ' . mysqli_error($conn2)); }

    }

    if ($sb1_semula && $sbAda['list'] > 0) {
        if (!mysqli_query($conn2, "insert into sb_list_journal_cancel
                (select * from sb_list_journal where no_journal = '" . $e($no_mj_sb) . "')")) {
            throw new Exception('Gagal mengarsipkan sb_list_journal: ' . mysqli_error($conn2));
        }
    }

    if ($sb1_semula && ($sbAda['mj'] > 0 || $sbAda['list'] > 0)) {
        if (!mysqli_query($conn2, "insert into sb_log_edit_mj (no_mj, user_edit, tgl_edit)
                values ('" . $e($no_mj_sb) . "', '" . $e($user) . "', '" . $e($now) . "')")) {
            throw new Exception('Gagal menulis log edit SB: ' . mysqli_error($conn2));
        }
    }

    /* ---- 2f. Hapus yang lama ------------------------------------------- */
    foreach ([
        "delete from tbl_memorial_journal where no_mj = '" . $e($no_mj) . "'",
        "delete from tbl_list_journal     where no_journal = '" . $e($no_mj) . "'",
    ] as $q) {
        if (!mysqli_query($conn2, $q)) { throw new Exception('Gagal menghapus baris lama: ' . mysqli_error($conn2)); }
    }

    // Buku SB dihapus kalau dokumennya PUNYA pasangan SB - termasuk ketika
    // centang SB I baru saja DILEPAS, supaya buku SB tidak meninggalkan salinan
    // yatim yang tidak lagi punya pasangan di buku utama.
    if ($sb1_semula) {
        $hapusSb = [];
        if ($sbAda['mj'] > 0)   { $hapusSb[] = "delete from sb_memorial_journal where no_mj = '" . $e($no_mj_sb) . "'"; }
        if ($sbAda['list'] > 0) { $hapusSb[] = "delete from sb_list_journal     where no_journal = '" . $e($no_mj_sb) . "'"; }
        foreach ($hapusSb as $q) {
            if (!mysqli_query($conn2, $q)) { throw new Exception('Gagal menghapus baris SB lama: ' . mysqli_error($conn2)); }
        }
    }

    /* ---- 2g. Tulis ulang seluruh baris ---------------------------------
       Multi-row INSERT per $CHUNK baris, pola yang sama dgn save_mj_ppn.php.
       Satu INSERT per baris untuk dokumen 672 baris berarti 672 perjalanan
       bolak-balik ke server database di dalam satu transaksi - lambat dan rawan
       timeout. */
    $CHUNK = 500;

    $HEAD_MJ = "INSERT INTO tbl_memorial_journal
        (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate,
         debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date,
         post_by, post_date, profit_center) VALUES ";
    $HEAD_JR = "INSERT INTO tbl_list_journal
        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
         reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, supplier, buyer, no_ws, curr, rate,
         debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
         approve_by, approve_date, cancel_by, cancel_date, profit_center) VALUES ";
    $HEAD_SMJ = "INSERT INTO sb_memorial_journal
        (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate,
         debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date,
         asal_data, profit_center) VALUES ";
    $HEAD_SJR = "INSERT INTO sb_list_journal
        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
         reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr,
         status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date,
         profit_center) VALUES ";

    $bufMj = []; $bufJr = []; $bufSmj = []; $bufSjr = [];
    $flush = function (&$buf, $head, $label) use ($conn2) {
        if (empty($buf)) { return; }
        $n = count($buf);
        $ok = mysqli_query($conn2, $head . implode(',', $buf));
        $buf = [];
        if ($ok === false) { throw new Exception('Gagal menyimpan ' . $label . ' (' . $n . ' baris): ' . mysqli_error($conn2)); }
    };

    // Nama COA & Cost Center di-cache: dokumen besar memakai COA yang sama
    // berulang-ulang (672 baris seringkali cuma belasan COA berbeda). Tanpa
    // cache, tiap baris memicu dua query lookup lagi.
    $cacheCoa = []; $cacheCc = [];
    $namaCoa = function ($coa) use ($conn2, &$cacheCoa, $e) {
        if (!array_key_exists($coa, $cacheCoa)) {
            $r = mysqli_query($conn2, "select nama_coa from mastercoa_v2 where no_coa = '" . $e($coa) . "' limit 1");
            $x = $r ? mysqli_fetch_assoc($r) : null;
            $cacheCoa[$coa] = $x ? (string) $x['nama_coa'] : '';
        }
        return $cacheCoa[$coa];
    };
    $namaCc = function ($cc) use ($conn2, &$cacheCc, $e) {
        if ($cc === '' || $cc === '-') { return ''; }
        if (!array_key_exists($cc, $cacheCc)) {
            $r = mysqli_query($conn2, "select cc_name from b_master_cc where no_cc = '" . $e($cc) . "' limit 1");
            $x = $r ? mysqli_fetch_assoc($r) : null;
            $cacheCc[$cc] = $x ? (string) $x['cc_name'] : '';
        }
        return $cacheCc[$cc];
    };

    // Tanggal opsional: kolomnya DATE NULL. Kosong / "-" / "0000-00-00" /
    // '1970-01-01' semuanya berarti TIDAK ADA TANGGAL dan disimpan sebagai NULL
    // literal - MySQL strict mode menolak '' untuk DATE, dan '0000-00-00'
    // menghasilkan tanggal palsu yang muncul di laporan.
    //
    // Hasil parsing dibandingkan sebagai TANGGAL, bukan diuji benar/salah:
    // strtotime('01-01-1970') di zona Asia/Jakarta bernilai -25200 (negatif,
    // jadi truthy) dan strtotime('0000-00-00') bernilai -62170009632. Penjaga
    // bergaya "$t ? ..." meloloskan keduanya dan menuliskannya kembali ke
    // database sebagai tanggal sungguhan.
    $tglAtauNull = function ($v) use ($e) {
        $v = trim((string) $v);
        if ($v === '' || $v === '-') { return 'NULL'; }
        $t = strtotime(str_replace('/', '-', $v));
        if ($t === false) { return 'NULL'; }
        $ymd = date('Y-m-d', $t);
        return ($ymd <= '1970-01-01') ? 'NULL' : "'" . $e($ymd) . "'";
    };

    $mjE     = $e($no_mj);
    $sbE     = $e($no_mj_sb);
    $tglE    = $e($mj_date);
    $cmjE    = $e($id_cmj);
    $namaCmjE= $e($nama_cmj);
    $stE     = $e($status);
    $cbE     = $e($create_by);
    $cdE     = $e($create_date);
    $pbE     = $e($post_by);

    foreach ($baris as $b) {
        $coa   = trim((string) ($b['no_coa'] ?? ''));
        $cc    = trim((string) ($b['no_costcenter'] ?? ''));
        $pc    = trim((string) ($b['profit_center'] ?? ''));
        $reff  = trim((string) ($b['no_reff'] ?? ''));
        $buyer = trim((string) ($b['buyer'] ?? ''));
        $ws    = trim((string) ($b['no_ws'] ?? ''));
        $curr  = strtoupper(trim((string) ($b['curr'] ?? 'IDR')));
        $ket   = trim((string) ($b['keterangan'] ?? ''));
        $fak   = trim((string) ($b['faktur_pajak'] ?? ''));
        $supp  = trim((string) ($b['supplier'] ?? ''));
        $deb   = (float) ($b['debit'] ?? 0);
        $cre   = (float) ($b['credit'] ?? 0);

        // Kurs sudah diselesaikan & divalidasi di tahap validasi di atas
        // (mj_resolve_rate per baris, memakai mata uang BARIS INI - bukan satu
        // kurs global seperti form edit lama yang membuat baris EUR ikut
        // dikalikan kurs USD). Dipakai ulang di sini, tidak dicari dua kali.
        $rate = (float) $b['_rate'];
        $debIdr    = $deb * $rate;
        $creIdr    = $cre * $rate;

        $coaE  = $e($coa);
        $ccE   = $e($cc);
        $pcE   = $e($pc);
        $reffE = $e($reff);
        $buyE  = $e($buyer);
        $wsE   = $e($ws);
        $curE  = $e($curr);
        $ketE  = $e($ket);
        $nCoaE = $e($namaCoa($coa));
        $nCcE  = $e($namaCc($cc));
        $reffD = $tglAtauNull($b['reff_date'] ?? '');
        $fakD  = $tglAtauNull($b['tgl_faktur_pajak'] ?? '');

        $bufMj[] = "('$mjE','$tglE','$cmjE','$coaE','$ccE','$reffE',$reffD,'$buyE','$wsE','$curE',$rate,
            $deb,$cre,$debIdr,$creIdr,'$ketE','$stE','$cbE','$cdE','$pbE',$post_date,'$pcE')";

        $bufJr[] = "('$mjE','$tglE','$namaCmjE','$coaE','$nCoaE','$ccE','$nCcE','$reffE',$reffD,
            '" . $e($fak) . "',$fakD,'" . $e($supp) . "','$buyE','$wsE','$curE',$rate,
            $deb,$cre,$debIdr,$creIdr,'$stE','$ketE','$cbE','$cdE','','','','','$pcE')";

        if ($sb1_on && $tulisSbMj) {
            $bufSmj[] = "('$sbE','$tglE','$cmjE','$coaE','$ccE','$reffE',$reffD,'$buyE','$wsE','$curE',$rate,
                $deb,$cre,$debIdr,$creIdr,'$ketE','$stE','$cbE','$cdE','Edit SB2','$pcE')";
        }
        if ($sb1_on && $tulisSbList) {
            $bufSjr[] = "('$sbE','$tglE','$namaCmjE','$coaE','$nCoaE','$ccE','$nCcE','$reffE',$reffD,
                '$buyE','$wsE','$curE',$rate,$deb,$cre,$debIdr,$creIdr,'$stE','$ketE','$cbE','$cdE','','','','','$pcE')";
        }

        if (count($bufMj) >= $CHUNK) {
            $flush($bufMj, $HEAD_MJ, 'tbl_memorial_journal');
            $flush($bufJr, $HEAD_JR, 'tbl_list_journal');
            if ($sb1_on) {
                $flush($bufSmj, $HEAD_SMJ, 'sb_memorial_journal');
                $flush($bufSjr, $HEAD_SJR, 'sb_list_journal');
            }
        }
    }

    $flush($bufMj, $HEAD_MJ, 'tbl_memorial_journal');
    $flush($bufJr, $HEAD_JR, 'tbl_list_journal');
    if ($sb1_on) {
        $flush($bufSmj, $HEAD_SMJ, 'sb_memorial_journal');
        $flush($bufSjr, $HEAD_SJR, 'sb_list_journal');
    }
    // \$flush() aman dipanggil untuk buffer kosong, jadi tabel yang tidak ditulis
    // (mis. sb_memorial_journal untuk dokumen FX) sekadar dilewati.

    /* ---- 2h. Peta SB1 di status_memorial_journal ------------------------
       Peta hanya dibuat untuk pasangan yang BENAR-BENAR baru. Dokumen FX/ dan
       GM/ warisan lama sengaja TIDAK dibuatkan peta: pasangannya memakai nomor
       yang sama dan selama ini memang tidak pernah tercatat di sini -
       menambahkannya akan memunculkan dokumen itu di alur verifikasi yang
       tidak pernah dilaluinya. */
    if ($sb1_on && !$sb1_semula) {
        if (!mysqli_query($conn2, "insert into status_memorial_journal
                (no_mj, mj_date, no_mj_sb, status, create_by, create_date)
                values ('$mjE','$tglE','$sbE','" . $e($status) . "','" . $e($user) . "','" . $e($now) . "')")) {
            throw new Exception('Gagal menulis peta SB1: ' . mysqli_error($conn2));
        }
    } elseif ($sb1_on && $sb1_semula) {
        // Tanggal dokumen bisa berubah saat edit; petanya ikut disesuaikan supaya
        // tidak menunjuk periode yang sudah tidak dipakai.
        mysqli_query($conn2, "update status_memorial_journal set mj_date = '$tglE'
            where no_mj = '$mjE'");
    } elseif (!$sb1_on && $sb1_semula) {
        if (!mysqli_query($conn2, "delete from status_memorial_journal where no_mj = '$mjE'")) {
            throw new Exception('Gagal melepas peta SB1: ' . mysqli_error($conn2));
        }
    }

    mysqli_commit($conn2);

    balas([
        'status'     => 'success',
        'no_journal' => $no_mj,
        'no_mj_sb'   => $sb1_on ? $no_mj_sb : '',
        'baris'      => count($baris),
    ]);

} catch (Exception $ex) {
    mysqli_rollback($conn2);
    gagal($ex->getMessage());
}
