<?php
/* Alat bersama menu Cash > Projection > Other.
   Dipakai halaman daftar, formulir, dan penyimpannya.
   Berkas ini tidak include conn.php - pemanggilnya yang sudah punya koneksi. */


/* Daftar akun untuk dropdown Source & Destination.
   Bank dari b_masterbank (tabel yang dipakai menu Bank, bukan masterbank).
   Kas per COA, daftarnya sama dgn dropdown Account di Create Petty Cash. */
function cfp_daftar_akun($conn)
{
    $akun = array();

    // nama pendek PC: NAG -> GARMENT, NAK -> KNITTING
    $pc = array();
    $q = mysqli_query($conn, "select kode_pc, nama_pc from master_pc where status = 'Active'");
    while ($r = mysqli_fetch_assoc($q)) {
        $kata = explode(' ', trim($r['nama_pc']));
        $pc[$r['kode_pc']] = strtoupper(end($kata));
    }

    // akun bank
    $q = mysqli_query($conn, "select bank_name, bank_account, curr, profit_center_bank
                              from b_masterbank where status = 'Active'
                              order by bank_name, bank_account");
    while ($r = mysqli_fetch_assoc($q)) {
        $label = trim($r['bank_name']) . ' - ' . trim($r['bank_account']);
        $kode  = $r['profit_center_bank'];
        $akun[$label] = array(
            'label'   => $label,
            'curr'    => $r['curr'] ? strtoupper($r['curr']) : 'IDR',
            'kode_pc' => $kode,
            'nama_pc' => isset($pc[$kode]) ? $pc[$kode] : $kode,
        );
    }

    // akun kas - per COA, yang ditandai kode_cash. Selalu IDR.
    // PC-nya cuma dua: yang knitting milik NAK, selain itu NAG. Dikenali
    // dari nomor 1.01.11 atau dari namanya, biar kas knitting baru ikut
    // terbaca. Cocok dgn isi tbl_list_journal.
    $q = mysqli_query($conn, "select no_coa, nama_coa from mastercoa_v2
                              where kode_cash is not null and kode_cash <> ''
                                and status = 'Active' order by no_coa");
    while ($r = mysqli_fetch_assoc($q)) {
        $no_coa = trim($r['no_coa']);
        $label  = $no_coa . ' ' . trim($r['nama_coa']);
        $knit   = ($no_coa == '1.01.11' || strpos(strtoupper($r['nama_coa']), 'KNITTING') !== false);
        $kode   = $knit ? 'NAK' : 'NAG';
        $akun[$label] = array(
            'label'   => $label,
            'curr'    => 'IDR',
            'kode_pc' => $kode,
            'nama_pc' => isset($pc[$kode]) ? $pc[$kode] : $kode,
        );
    }

    return $akun;
}


/* DULU ADA cfp_daftar_bank() - daftar yang sama tapi lima akun kas dibuang,
   dipakai tab Other Inflow & PV Expense. Dihapus 23 Nov 2026 atas permintaan
   pemakai: "akun kas nya satukan dengan bank ya di dropdown". Uang yang
   mendarat di kas tetap uang perusahaan, dan laporan proyeksinya memang
   sudah menghitung akun kas - dropdown yang membuangnya membuat baris itu
   tidak bisa dicatat sama sekali.

   Dihapus, bukan dibiarkan mengembalikan daftar penuh: nama fungsinya akan
   berbohong, dan dua fungsi yang isinya sama persis cepat atau lambat
   dipakai bergantian tanpa ada yang sadar bedanya sudah hilang. */


/* Daftar supplier untuk dropdown Supplier Name di tab PV Expense.
   Balikannya id => nama. Hanya tipe 'S' (supplier, bukan customer/lainnya)
   dan yang masih aktif - proyeksi ini untuk pembayaran yang akan datang. */
function cfp_daftar_supplier($conn)
{
    $daftar = array();
    $q = mysqli_query($conn, "select Id_Supplier, Supplier from mastersupplier
                              where tipe_sup = 'S'
                                and (non_aktif is null or non_aktif <> '1')
                                and trim(Supplier) <> ''
                              order by Supplier");
    while ($r = mysqli_fetch_assoc($q)) {
        $daftar[(string) $r['Id_Supplier']] = trim($r['Supplier']);
    }
    return $daftar;
}


/* Daftar customer untuk dropdown Customer Name di tab Other Inflow.
   Tabelnya SAMA dgn supplier (mastersupplier) - yang membedakan hanya
   tipe_sup: 'C' customer, 'S' supplier. Balikannya id => nama. */
function cfp_daftar_customer($conn)
{
    $daftar = array();
    $q = mysqli_query($conn, "select Id_Supplier, Supplier from mastersupplier
                              where tipe_sup = 'C'
                                and (non_aktif is null or non_aktif <> '1')
                                and trim(Supplier) <> ''
                              order by Supplier");
    while ($r = mysqli_fetch_assoc($q)) {
        $daftar[(string) $r['Id_Supplier']] = trim($r['Supplier']);
    }
    return $daftar;
}


/* Nama satu supplier ATAU customer dari id-nya - keduanya tinggal di
   mastersupplier, jadi satu fungsi cukup. Sengaja TIDAK menyaring tipe
   maupun aktif/tidak: dropdown-nya sudah membatasi pilihan, sedangkan
   dokumen lama yang namanya belakangan dinonaktifkan harus tetap bisa
   disimpan ulang. */
function cfp_nama_master($conn, $id)
{
    $id = (int) $id;
    if ($id <= 0) { return ''; }
    $r = mysqli_fetch_assoc(mysqli_query($conn,
        "select Supplier from mastersupplier where Id_Supplier = $id limit 1"));
    return $r ? trim($r['Supplier']) : '';
}


/* Kategori arus kas dari master. $jenis: 'Cash In' atau 'Cash Out'.

   SUMBERNYA master_cash_flow - master yang SUDAH ADA dan punya menunya
   sendiri (Master Cash Flow). Dulu halaman ini memakai tabel sendiri
   ap_cf_category berisi salinan 54 kategori dari lembar Excel; ternyata
   master_cash_flow sudah memuat semuanya (malah 2 lebih banyak: Selisih
   Kurs). Salinan itu DIBUANG - kalau dibiarkan, kategori yang ditambah
   atau dinonaktifkan lewat menu Master Cash Flow tidak akan pernah ikut
   ke sini, persis cacat yang dulu terjadi pada ind_categori4.

   Penyaringnya sama dgn dropdown Bank In/Bank Out yang membaca master yang
   sama: status 'Y' saja.

   URUTANNYA SENGAJA TIDAK memakai "order by nama_category" seperti Bank In.
   Diurutkan menurut abjad, FINANCING naik ke paling atas - padahal dropdown
   di halaman ini selama ini OPERATING dulu (Utang Usaha di urutan pertama,
   yang paling sering dipakai). FIELD() mengunci urutan kelompoknya supaya
   isi dropdown-nya persis seperti sebelumnya. Kelompok baru yang belum
   terdaftar di FIELD() bernilai 0; dibuat jadi 9 supaya jatuh ke bawah,
   bukan melompat ke atas.

   Yang dikembalikan tetap nama_subcategory - itu yang selama ini
   tersimpan di ap_cf_projection.kategori, jadi dokumen lama tetap cocok. */
function cfp_daftar_kategori($conn, $jenis)
{
    $daftar = array();
    $j = mysqli_real_escape_string($conn, $jenis);
    $urut = "FIELD(nama_category, 'OPERATING ACTIVITIES', 'INVESTING ACTIVITIES', 'FINANCING ACTIVITIES')";
    $q = mysqli_query($conn, "select nama_subcategory from master_cash_flow
                              where type_cashflow = '$j' and status = 'Y'
                              order by IF($urut = 0, 9, $urut) asc, urutan asc");
    while ($r = mysqli_fetch_assoc($q)) {
        $daftar[] = $r['nama_subcategory'];
    }
    return $daftar;
}


/* Kurs PAJAK untuk satu tanggal.
   Dicari kurs terakhir yang tanggalnya <= tanggal dokumen, bukan tanggal
   persis: ini proyeksi, tanggalnya di depan dan masterrate belum terisi. */
function cfp_kurs_pajak($conn, $tanggal, $curr = 'USD')
{
    $hasil = array('rate' => 1, 'tanggal' => $tanggal, 'mundur' => false, 'ketemu' => true);
    if (strtoupper($curr) === 'IDR') {
        return $hasil;
    }

    $tgl  = date('Y-m-d', strtotime($tanggal));
    $curr = mysqli_real_escape_string($conn, strtoupper($curr));
    $sql  = "select rate, tanggal from masterrate
             where curr = '$curr' and v_codecurr = 'PAJAK' and tanggal <= '$tgl'
             order by tanggal desc limit 1";
    $r = mysqli_fetch_assoc(mysqli_query($conn, $sql));

    if ($r) {
        $hasil['rate']    = (float) $r['rate'];
        $hasil['tanggal'] = $r['tanggal'];
        $hasil['mundur']  = ($r['tanggal'] != $tgl);
    } else {
        $hasil['ketemu'] = false;
    }
    return $hasil;
}


/* Awalan nomor dokumen per jenis - ketiganya sudah dipastikan pemakai.
   PV Expense memakai EXP: di laporan, kolom "Payment Voucher No" memang
   diisi nomor dokumen ini, bukan nomor PV yang lain. */
function cfp_awalan_doc($jenis)
{
    if ($jenis == 'BOOK TRANSFER') { return 'BTR'; }
    if ($jenis == 'OTHER INFLOW')  { return 'OTH'; }
    if ($jenis == 'PV EXPENSE')    { return 'EXP'; }
    return 'CFP';
}


/* Nomor dokumen: BTR/NAG/1126/00001.
   Bulan-tahun ikut tanggal dokumen, nomor urut per awalan + PC + bulan. */
function cfp_no_doc_berikut($conn, $jenis, $kode_pc, $tanggal)
{
    $awalan = cfp_awalan_doc($jenis) . '/' . strtoupper($kode_pc) . '/' . date('my', strtotime($tanggal)) . '/';
    $cari   = mysqli_real_escape_string($conn, $awalan);

    $r = mysqli_fetch_assoc(mysqli_query($conn,
        "select max(no_doc) as maks from ap_cf_projection where no_doc like '$cari%'"));

    $urut = 0;
    if ($r && $r['maks']) {
        $urut = (int) substr($r['maks'], strlen($awalan));
    }
    return $awalan . str_pad($urut + 1, 5, '0', STR_PAD_LEFT);
}


/* Hitung satu baris Book Transfer. $kurs = kurs PAJAK USD tanggal itu.
   Amount selalu dalam mata uang akun Source, lalu dihitung nilai rupiah
   dan nilai dolarnya. Rupiah ke rupiah tidak punya sisi dolar - diisi 0.
   Yang mendarat di akun tujuan tinggal pilih salah satunya. */
function cfp_hitung_transfer($amount, $kurs, $curr_source, $curr_tujuan)
{
    $amount        = (float) $amount;
    $rate          = (float) $kurs;
    $rupiah        = (strtoupper($curr_source) == 'IDR');
    $tujuan_rupiah = (strtoupper($curr_tujuan) == 'IDR');

    $amount_idr = $rupiah ? $amount : $amount * $rate;

    $amount_usd = 0;
    if (!$rupiah) {
        $amount_usd = $amount;
    } elseif (!$tujuan_rupiah && $rate > 0) {
        $amount_usd = $amount / $rate;
    }

    $amount_tujuan = $tujuan_rupiah ? $amount_idr : $amount_usd;

    return array(
        'amount_idr'    => round($amount_idr, 2),
        'amount_usd'    => round($amount_usd, 2),
        'amount_tujuan' => round($amount_tujuan, 2),
    );
}


/* PC sama = Intra PC, beda = Inter PC. */
function cfp_intra_inter($pc_source, $pc_tujuan)
{
    return ($pc_source != '' && $pc_source == $pc_tujuan) ? 'Intra PC' : 'Inter PC';
}


/* ----------------------------------------------------------------------
   JENIS LAPORAN entri Projection Other: WEEKLY atau MONTHLY.

   Dibedakan sejak 20 Nov 2026 (permintaan pemakai): Weekly dan Monthly
   punya pilihannya sendiri-sendiri, formulirnya sama. DAILY menyusul
   20 Nov 2026, jadi sekarang ada TIGA; apa pun yang di luar ketiganya jatuh
   ke WEEKLY - itu nilai bawaan kolomnya, dan dokumen tidak boleh lahir
   tanpa jenis.

   Satu pintu untuk SEMUA pembacanya (layar Create, ketiga penyimpan,
   ketiga pengubah, dan daftar) supaya tidak ada yang menuliskan ejaan
   sendiri - 'Weekly' vs 'WEEKLY' cukup untuk membuat saringan luput tanpa
   satu pun pesan galat.
   ---------------------------------------------------------------------- */
function cfp_type_report($v)
{
    $v = strtoupper(trim((string) $v));
    return in_array($v, array('WEEKLY', 'MONTHLY', 'DAILY'), true) ? $v : 'WEEKLY';
}


/* ----------------------------------------------------------------------
   POTONGAN SQL: entri Projection Other yang BOLEH ikut sebuah laporan.

   Permintaan pemakai 20 Nov 2026: laporan proyeksi cash flow hanya mengambil
   entri yang jenisnya SAMA dgn type laporannya - Weekly mengambil Weekly,
   Monthly mengambil Monthly, Daily mengambil Daily. Type yang di luar ketiganya
   tidak mengambil apa pun: lebih baik kosong daripada diam-diam memakai entri
   jenis lain, yang akan membuat angkanya terlihat benar padahal isinya rencana
   periode yang berbeda.

   Dipakai mesin laporan DAN pengumpul lembar sumber Excel, supaya angka di
   tabel dan rincian di Excel mustahil berbeda.

   KOLOMNYA DITANYAKAN DULU: migrasi type_report dijalankan terpisah di
   produksi, dan menyaring kolom yang belum ada membuat SELURUH laporan mati.
   Selama kolomnya belum ada, perilakunya persis seperti sebelum pemisahan
   ini - semua entri ikut, apa pun type-nya.
   ---------------------------------------------------------------------- */
function cfp_saring_type_report($conn, $type, $alias = '')
{
    $q = mysqli_query($conn, "show columns from `ap_cf_projection` like 'type_report'");
    if (!$q || !mysqli_num_rows($q)) { return ''; }

    $p = ($alias === '') ? '' : $alias . '.';
    $t = strtoupper(trim((string) $type));
    if (!in_array($t, array('WEEKLY', 'MONTHLY', 'DAILY'), true)) {
        /* Type yang tidak dikenal tidak mengambil apa-apa. */
        return ' and 1 = 0';
    }
    return " and {$p}type_report = '" . mysqli_real_escape_string($conn, $t) . "'";
}
