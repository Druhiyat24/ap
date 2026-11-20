<?php
/* ============================================================================
   DAFTAR AKUN KAS & BANK untuk laporan Cash Flow Projection.

   DIPAKAI BERSAMA oleh mesin laporannya (ajx_cash_flow_projection.php) dan
   oleh pengisi dropdown Bank di layar (ajx_cf_bank.php). Sengaja satu berkas:
   kalau dropdown punya daftarnya sendiri, cepat atau lambat isinya berbeda
   dari laporannya - persis cacat yang baru saja ditemukan antara laporan ini
   dan Report Cash Flow Realisation (akun kas ditulis mati di satu tempat,
   dibaca dari master di tempat lain).

   TIDAK meng-include conn.php: koneksinya dikirim sebagai argumen, supaya
   berkas ini aman di-include dari mana pun. conn/parser-php-version.php
   TIDAK berpagar function_exists, jadi meng-include conn dua kali fatal.
   ============================================================================ */

/* Kunci akun. Sumbernya menulis rekening dgn gaya berbeda-beda -
   "008-997-1979", "BCA - 0089971979", "Bank Central Asia - 008-997-1979" -
   dan ketiganya harus jatuh ke akun yang sama, jadi yang dipakai angkanya
   saja. COA kas (1.01.01) dikenali lebih dulu dan dipakai apa adanya. */
if (!function_exists('cfpKunci')) {
    function cfpKunci($v)
    {
        $v = trim((string) $v);
        if ($v === '') { return ''; }
        if (preg_match('/^(\d\.\d{2}\.\d{2})/', $v, $m)) { return $m[1]; }
        $angka = preg_replace('/\D/', '', $v);
        return $angka === '' ? strtoupper($v) : $angka;
    }
}

/* Saringan Bank di layar -> LARIK kunci rekening. Larik kosong = semua
   rekening. Sejak 19 Nov 2026 saringannya bisa memilih BEBERAPA rekening
   sekaligus, jadi nilainya dikirim sbg daftar dipisah titik koma.

   Dipakai juga untuk membaca NAMA rekening yang dibekukan di potret
   (ap_cf_history_h.bank), yang isinya label lengkap dipisah '; ' -
   cfpKunci() mengambil angkanya saja, jadi label maupun kunci sama-sama
   jatuh ke kunci yang benar tanpa perlu dua jalur. */
if (!function_exists('cfpDaftarKunciBank')) {
    function cfpDaftarKunciBank($v)
    {
        if (is_array($v)) { $v = implode(';', $v); }
        $v = trim((string) $v);
        if ($v === '' || strtoupper($v) === 'ALL' || strcasecmp($v, 'All Banks') === 0) { return array(); }
        $keluar = array();
        foreach (preg_split('/[;,]/', $v) as $bagian) {
            $bagian = trim($bagian);
            if ($bagian === '' || strtoupper($bagian) === 'ALL') { continue; }
            $k = (string) cfpKunci($bagian);
            if ($k !== '' && !in_array($k, $keluar, true)) { $keluar[] = $k; }
        }
        return $keluar;
    }
}

/* Ikut tidaknya satu akun menurut saringan Profit Center & Bank di layar. */
if (!function_exists('cfpIkut')) {
    function cfpIkut($a, $pcPil, $kunciBank)
    {
        /* $kunciBank boleh LARIK (beberapa rekening, sejak 19 Nov 2026) atau
           TEKS (satu rekening, bentuk lama). Dua-duanya diterima supaya
           pemanggil lama tidak ikut rusak diam-diam.
           Dibandingkan sbg TEKS - kunci rekening yang isinya angka murni
           gampang berubah jadi integer waktu dipakai sbg kunci array. */
        if (is_array($kunciBank)) {
            if ($kunciBank && !in_array((string) $a['kunci'], $kunciBank, true)) { return false; }
        } elseif ($kunciBank !== '' && (string) $a['kunci'] !== (string) $kunciBank) {
            return false;
        }
        if ($pcPil !== 'ALL' && $pcPil !== '' && strtoupper($a['pc']) !== $pcPil) { return false; }
        return true;
    }
}

/* ----------------------------------------------------------------------------
   Seluruh akun bank + kas, berkunci cfpKunci().

   BANK  : b_masterbank status Active. Status Loan dibaca dari bank_loan -
           BUKAN ditebak dari tanda saldo. Limitnya dari fac_limit; di Excel
           angkanya ditulis mati (IDR 50 M, USD 500.000) dan isi fac_limit
           memang sama.

   KAS   : mastercoa_v2 ind_categori5 = 'KAS', POS SILANG dikecualikan -
           query yang SAMA PERSIS dgn cfrGetAllAccounts() milik Report Cash
           Flow Realisation. Dulu dua akun ditulis mati di mesin (1.01.01 &
           1.01.11) dgn alasan "hanya kas kecil operasional"; akibatnya KAS
           BESAR dan tiga KAS KECIL KANTOR tidak pernah ikut, padahal KAS
           KECIL KANTOR 1 saja isinya Rp 2,9 M.

   PROFIT CENTER akun kas tidak ada di master mana pun (mastercoa_v2 tidak
   punya kolomnya, c_report_pettycash juga tidak), jadi hanya diturunkan dari
   NAMANYA: ada kata GARMENT -> NAG, KNITTING -> NAK. Sisanya dibiarkan
   KOSONG = tidak terikat profit center: ikut waktu saringan PC = ALL, dan
   namanya dikembalikan lewat $kasTanpaPc supaya pemanggilnya bisa memberi
   tahu kalau saringan PC menyembunyikannya. Menebak profit center-nya
   berarti memindahkan uang ke perusahaan yang salah - jauh lebih sulit
   ketahuan daripada selisih yang berdiri sendiri.
   ---------------------------------------------------------------------------- */
function cf_daftar_akun($conn, &$kasTanpaPc = null)
{
    $akun = array();
    $kasTanpaPc = array();

    $q = mysqli_query($conn, "select bank_name, bank_account, curr, profit_center_bank, bank_loan, fac_limit
                                from b_masterbank where status = 'Active' order by id");
    while ($q && ($r = mysqli_fetch_assoc($q))) {
        $k = cfpKunci($r['bank_account']);
        if ($k === '') { continue; }
        $akun[$k] = array(
            'kunci' => $k,
            'label' => trim($r['bank_name']) . ' - ' . trim($r['bank_account']),
            'curr'  => (strtoupper(trim($r['curr'])) === 'USD') ? 'USD' : 'IDR',
            'pc'    => trim($r['profit_center_bank']),
            'loan'  => (trim($r['bank_loan']) === 'Yes'),
            'limit' => (float) $r['fac_limit'],
            'kas'   => false,
        );
    }

    $q = mysqli_query($conn, "select no_coa, nama_coa from mastercoa_v2
                               where ind_categori5 = 'KAS' and nama_coa <> 'POS SILANG'
                               order by no_coa");
    while ($q && ($r = mysqli_fetch_assoc($q))) {
        $k = cfpKunci($r['no_coa']);
        if ($k === '') { continue; }
        $nama = strtoupper($r['nama_coa']);
        $pc = (strpos($nama, 'GARMENT') !== false) ? 'NAG'
            : ((strpos($nama, 'KNITTING') !== false) ? 'NAK' : '');
        $akun[$k] = array(
            'kunci' => $k,
            'label' => trim($r['no_coa']) . ' ' . trim($r['nama_coa']),
            'curr'  => 'IDR',
            'pc'    => $pc,
            'loan'  => false,
            'limit' => 0,
            'kas'   => true,
        );
        if ($pc === '') { $kasTanpaPc[] = trim($r['nama_coa']); }
    }

    return $akun;
}


/* ----------------------------------------------------------------------------
   SARINGAN POTRET -> DAFTAR AKUN UNTUK REPORT CASH FLOW REALISATION.

   Laporan realisasi tidak mengenal nomor dokumen maupun saringan Profit
   Center; yang dikenalnya cuma rentang tanggal dan DAFTAR AKUN. Jadi
   saringan potret diterjemahkan di sini - sekali, di satu tempat.

   Dicocokkan lewat cfpKunci() terhadap daftar akun milik laporan
   realisasinya sendiri (cfrGetAllAccounts), bukan terhadap daftar lain yang
   kebetulan mirip: nilai yang dikirim harus PERSIS seperti yang dikenali
   laporan itu, sedangkan potret menyimpan NAMA rekening.

   Kembaliannya ARRAY KOSONG kalau potretnya All PC + All Banks - laporan
   realisasi menafsirkan kosong sebagai "semua akun", dan alamatnya jadi
   jauh lebih pendek.
   ---------------------------------------------------------------------------- */
function cf_akun_realisasi($conn, $pcPotret, $bankPotret)
{
    $pc   = strtoupper(trim((string) $pcPotret));
    $bank = trim((string) $bankPotret);
    $pcPil = ($pc === '' || $pc === 'ALL' || $pc === 'ALL PC') ? 'ALL' : $pc;
    $kunciBank = cfpDaftarKunciBank($bank);       /* kosong = semua rekening */
    $bankSemua = !$kunciBank;

    if ($pcPil === 'ALL' && $bankSemua) { return array(); }

    include_once __DIR__ . '/cfr_functions.php';
    $peta = cf_daftar_akun($conn);

    $akun = array();
    foreach (cfrGetAllAccounts($conn) as $a) {
        $k = cfpKunci($a['account']);
        if (!isset($peta[$k])) { continue; }
        if (!cfpIkut($peta[$k], $pcPil, $kunciBank)) { continue; }
        $akun[] = $a['account'];
    }
    return $akun;
}
