<?php
/* ============================================================================
   DATA SUMBER CASH FLOW PROJECTION - lembar pendukung di Excel.

   Berkas acuan (D:\File Project\Cash Flow Projection Report.xlsx) bukan cuma
   lembar angkanya: ada lembar rincian sumbernya - AR Existing, Book Transfer,
   Other Inflow, PV EXP, PV AP, Balance, Master CF. Lembar itulah yang dipakai
   orang untuk menjawab "angka ini dari mana".

   DUA HAL YANG DITENTUKAN DI SINI, DI SATU TEMPAT:
     1. cf_sumber_lembar() - nama lembar, warna tab, dan susunan kolomnya
     2. cf_kumpul_sumber() - mengambil barisnya dari sumber yang SAMA dgn
        yang dipakai mesin proyeksi, pada saat yang sama

   DIPANGGIL WAKTU SAVE HISTORY, BUKAN WAKTU EKSPOR. Potret harus tetap
   memperlihatkan keadaan saat Save ditekan; kalau rinciannya dibangun ulang
   waktu Excel diunduh, isinya bisa tidak lagi cocok dgn angka yang dibekukan
   - dan selisih seperti itu paling lama ketahuannya. Jadi barisnya ikut
   disimpan ke ap_cf_history_src, dan ekspor tinggal membacanya.

   YANG TIDAK ADA DI SINI, DAN ALASANNYA:
     - "AR Projection" (lembar terpisah di berkas acuan) TIDAK dibuat:
       sumbernya proyeksi AR yang belum berwujud invoice, dan sistem ini
       tidak menyimpannya. Yang kita punya cuma potret AR berisi invoice
       yang sudah ada - itu yang jadi lembar "AR Existing".
     - "Cash Flow Daily" & "Cash Flow Weekly and Monthly" tidak jadi dua
       lembar: satu potret hanya punya satu bentuk, dan bentuk itulah yang
       ditulis di lembar "Projection".
     - "Balance" & "Master CF" dibuang atas permintaan pemakai (17 Nov
       2026). Tidak ada yang hilang: saldo awal tiap rekening sudah
       dibekukan di ap_cf_bank_setting per nomor dokumen, dan susunan
       kategorinya memang sudah jadi label baris di potretnya sendiri.
   ============================================================================ */

/**
 * Susunan lembar pendukung. Tiap kolom: array(judul, nama field, jenis).
 * Jenis: 't' teks, 'n' angka, 'd' tanggal (disimpan yyyy-mm-dd).
 */
function cf_sumber_lembar()
{
    return array(
        'AR_EXISTING' => array(
            'judul' => 'AR Existing', 'warna' => 'CCFFCC',
            'catatan' => 'From the saved AR collection projection used by this report.',
            'kolom' => array(
                /* Nomor potret asalnya, atau "-" kalau angkanya dihitung ulang
                   karena periode ini belum punya potret AR. Tanpa kolom ini,
                   dua berkas Excel yang angkanya berbeda tidak bisa
                   dijelaskan bedanya dari mana. */
                array('Source Doc', 'sumber_doc', 't'),
                array('Customer', 'customer', 't'),
                array('No Invoice', 'no_invoice', 't'),
                array('Invoice Date', 'inv_date', 'd'),
                array('Due Date', 'duedate', 'd'),
                array('Due Date Actual', 'duedate_update', 'd'),
                array('TOP', 'top', 'n'),
                array('Curr', 'curr', 't'),
                array('Total', 'amount', 'n'),
                array('Rate', 'rate', 'n'),
                array('Total IDR', 'amount_idr', 'n'),
                array('Payment', 'collection_amount', 'n'),
                array('Type', 'doc_type', 't'),
                array('Bank & Acc No', 'bank_acc', 't'),
                array('Profit Center', 'profit_center', 't'),
            ),
        ),
        'BOOK_TRANSFER' => array(
            'judul' => 'Book Transfer', 'warna' => 'BDD7EE',
            'catatan' => 'Cash Flow Projection (Other) - book transfer lines.',
            'kolom' => array(
                /* Nomor dokumen Projection (Other) asalnya - permintaan pemakai
                   19 Nov 2026, supaya tiap baris di lembar ini bisa ditelusuri
                   balik ke dokumen yang membuatnya. */
                array('Source Doc', 'sumber_doc', 't'),
                array('Source', 'akun_source', 't'),
                array('Destination', 'akun_tujuan', 't'),
                array('Currency', 'curr', 't'),
                array('Amount', 'amount', 'n'),
                array('Rate', 'rate', 'n'),
                array('Equivalent IDR', 'amount_idr', 'n'),
                array('Amount Received', 'amount_tujuan', 'n'),
                array('Equivalent IDR Received', 'idr_tujuan', 'n'),
                array('Received Date', 'tanggal', 'd'),
                array('PC Source', 'pc_source', 't'),
                array('PC Destination', 'pc_tujuan', 't'),
                array('Status', 'intra_inter', 't'),
                array('Stored Rate', 'rate_simpan', 'n'),
                array('Stored IDR', 'idr_simpan', 'n'),
            ),
        ),
        'OTHER_INFLOW' => array(
            'judul' => 'Other Inflow', 'warna' => 'CCFFCC',
            'catatan' => 'Cash Flow Projection (Other) - other inflow lines.',
            'kolom' => array(
                /* Lihat keterangan kolom yang sama di lembar Book Transfer. */
                array('Source Doc', 'sumber_doc', 't'),
                array('Customer Name', 'nama_supplier', 't'),
                array('Income Category', 'kategori', 't'),
                array('Currency', 'curr', 't'),
                array('Amount', 'amount', 'n'),
                array('Rate', 'rate', 'n'),
                array('Equivalent IDR', 'amount_idr', 'n'),
                array('Received Date', 'tanggal', 'd'),
                array('PC', 'pc', 't'),
                array('Bank', 'akun', 't'),
                array('Stored Rate', 'rate_simpan', 'n'),
                array('Stored IDR', 'idr_simpan', 'n'),
            ),
        ),
        'PV_EXP' => array(
            'judul' => 'PV EXP', 'warna' => 'C00000',
            'catatan' => 'Planned expense payments (Update Payment Date PV Biaya) plus PV Expense lines from Cash Flow Projection (Other).',
            'kolom' => array(
                array('Payment Voucher No', 'no_pv', 't'),
                array('Supplier Name', 'supplier', 't'),
                array('Cost Category', 'kategori', 't'),
                array('Currency', 'curr', 't'),
                array('Amount', 'amount', 'n'),
                array('Rate', 'rate', 'n'),
                array('Equivalent IDR', 'amount_idr', 'n'),
                array('Due Date', 'tanggal', 'd'),
                array('PC', 'pc', 't'),
                array('Bank', 'akun', 't'),
                array('Source', 'sumber', 't'),
                array('Stored Rate', 'rate_simpan', 'n'),
                array('Stored IDR', 'idr_simpan', 'n'),
            ),
        ),
        'PV_AP' => array(
            'judul' => 'PV AP', 'warna' => 'C00000',
            'catatan' => 'From AP Payment Projection - the same engine that feeds the trade payable line.',
            /* SUSUNANNYA MENIRU BERKAS ACUAN "Cash Flow Projection Report.xlsx"
               lembar PV AP (diminta pemakai 19 Nov 2026: "itu harus sama").
               Yang disamakan SUSUNANNYA, bukan isinya: acuan memuat SELURUH
               utang yang belum lunas (272 baris, termasuk yang jatuh temponya
               di luar periode), sedangkan lembar ini hanya memuat yang tanggal
               bayarnya jatuh DI DALAM periode - supaya jumlahnya tetap sama
               dgn baris Utang Usaha di laporannya. Itu keputusan pemakai pada
               tanggal yang sama. Akibatnya kolom Outstanding selalu 0: tidak
               ada baris yang nilainya tidak tertampung di kolom tanggal. */
            'kolom' => array(
                array('Nama Supplier', 'supplier', 't'),
                array('LP Number', 'no_kbon', 't'),
                array('LP Date', 'tgl_kbon', 'd'),
                array('Due Date', 'duedate', 'd'),
                array('Creator', 'creator', 't'),
                array('Curr', 'curr', 't'),
                array('Amount in OCY', 'invoice_amount', 'n'),
                array('Amount in IDR', 'total_invoice', 'n'),
                array('Relationship', 'relasi', 't'),
                array('Status Supplier', 'status_supplier', 't'),
                /* Penanda: diganti SATU KOLOM PER TANGGAL PROYEKSI yang
                   benar-benar terpakai - lihat cf_sumber_kolom(). Acuan
                   memakai 4 kolom tanggal karena laporannya mingguan; di sini
                   jumlahnya mengikuti tanggal yang betul-betul ada isinya,
                   jadi tidak melahirkan 30 kolom kosong pada laporan harian. */
                array('@TANGGAL', '', '@'),
                array('Outstanding', 'outstanding_sisa', 'n'),
                array('Days Deffered', 'days_deffered', 'n'),
                array('Alert', 'alert', 't'),
                array('Remarks', 'remarks', 't'),
                array('PC', 'pc', 't'),
                array('Bank', 'akun', 't'),
            ),
        ),
    );
}

/**
 * Kurs yang BENAR-BENAR DIPAKAI LAPORAN untuk sebuah rekening.
 *
 * Mesin proyeksi menaruh nilai dalam MATA UANG REKENINGNYA, lalu seluruh
 * laporan dirupiahkan dgn satu kurs: kurs laporan. Jadi nilai rupiah sebuah
 * baris = amount x (rekeningnya USD ? kurs laporan : 1) - BUKAN amount_idr
 * yang tersimpan di barisnya, yang bisa memakai kurs lain.
 */
function cfs_kurs($akun, $namaAkun, $rate)
{
    $k = function_exists('cfpKunci') ? cfpKunci($namaAkun) : '';
    if ($k === '' || !isset($akun[$k])) { return 1.0; }
    return (strtoupper($akun[$k]['curr']) === 'USD' && $rate > 0) ? (float) $rate : 1.0;
}

/** Angka berformat dari mesin AP ("1,234.00") kembali jadi angka. */
function cfs_angka($v)
{
    if ($v === null || $v === '' || $v === '-') { return 0.0; }
    return (float) str_replace(',', '', (string) $v);
}

/** Tanggal apa pun bentuknya -> yyyy-mm-dd, atau '' kalau bukan tanggal. */
function cfs_tgl($v)
{
    $v = trim((string) $v);
    if ($v === '' || $v === '-' || strpos($v, '0000-00-00') === 0) { return ''; }
    $t = strtotime($v);
    return $t ? date('Y-m-d', $t) : '';
}

/** Kolom yang BENAR-BENAR ada di sebuah tabel. Skema AR di sini dan di
 *  produksi tidak sama persis (doc_type & bank_acc hanya ada di produksi),
 *  jadi kolomnya ditanyakan dulu - bukan diasumsikan. */
function cfs_kolom_tabel($conn, $tabel)
{
    $ada = array();
    $q = mysqli_query($conn, "show columns from `" . $tabel . "`");
    while ($q && ($r = mysqli_fetch_assoc($q))) { $ada[$r['Field']] = true; }
    return $ada;
}

/* Nama PC pendek spt acuan: NAG -> GARMENT, NAK -> KNITTING. Diambil dari
   master_pc (kata terakhir nama_pc), bukan ditulis mati - kalau nanti ada PC
   ketiga, namanya ikut sendiri. */
function cfs_peta_pc($conn)
{
    $peta = array();
    $q = mysqli_query($conn, "select kode_pc, nama_pc from master_pc");
    while ($q && ($r = mysqli_fetch_assoc($q))) {
        $kata = preg_split('/\s+/', trim((string) $r['nama_pc']));
        $peta[strtoupper(trim($r['kode_pc']))] = strtoupper(end($kata));
    }
    return $peta;
}

/* Rekening spt acuan: "BCA - 0089971979" - singkatan bank + nomor tanpa
   tanda hubung. Nama bank yang tidak dikenali dipakai apa adanya, jadi
   rekening baru tidak pernah tampil kosong. */
function cfs_peta_bank($conn)
{
    $singkat = array(
        'CENTRAL ASIA' => 'BCA', 'NEGARA INDONESIA' => 'BNI',
        'RAKYAT INDONESIA' => 'BRI', 'TABUNGAN NEGARA' => 'BTN',
        'SYARIAH INDONESIA' => 'BSI', 'CIMB' => 'CIMB', 'MANDIRI' => 'MANDIRI',
        'PERMATA' => 'PERMATA', 'DANAMON' => 'DANAMON', 'MAYBANK' => 'MAYBANK',
        'BUKOPIN' => 'BUKOPIN', 'OCBC' => 'OCBC', 'PANIN' => 'PANIN',
        'HSBC' => 'HSBC', 'DBS' => 'DBS', 'UOB' => 'UOB',
    );
    $peta = array();
    $q = mysqli_query($conn, "select bank_account, bank_name from b_masterbank");
    while ($q && ($r = mysqli_fetch_assoc($q))) {
        $besar = strtoupper((string) $r['bank_name']);
        $nama  = trim((string) $r['bank_name']);
        foreach ($singkat as $kunci => $pendek) {
            if (strpos($besar, $kunci) !== false) { $nama = $pendek; break; }
        }
        $peta[trim($r['bank_account'])] = $nama . ' - ' . str_replace('-', '', trim($r['bank_account']));
    }
    return $peta;
}

/**
 * Susunan kolom AKHIR sebuah lembar: sama dgn cf_sumber_lembar(), kecuali
 * penanda '@TANGGAL' yang dijabarkan jadi satu kolom per tanggal proyeksi
 * yang BENAR-BENAR terpakai di barisnya.
 *
 * Sengaja dari barisnya sendiri, bukan dari kolom laporan: potret Weekly &
 * Monthly menyimpan kolom per REKENING, bukan per tanggal, jadi kalau diambil
 * dari sana lembar ini akan kehilangan kolom tanggalnya sama sekali.
 */
function cf_sumber_kolom($def, $baris)
{
    $kolom = array();
    foreach ($def['kolom'] as $k) {
        if ($k[0] !== '@TANGGAL') { $kolom[] = $k; continue; }

        $tgl = array();
        foreach ($baris as $b) {
            if (!empty($b['tgl_proyeksi'])) { $tgl[$b['tgl_proyeksi']] = true; }
        }
        ksort($tgl);
        foreach (array_keys($tgl) as $t) {
            $kolom[] = array(date('j M Y', strtotime($t)), 'd_' . $t, 'n');
        }
    }
    return $kolom;
}

/**
 * Mengumpulkan seluruh baris sumber.
 *
 * $cfg: mulai, akhir, rate, akun, saldo, setel, ktg, ap_baris, ap_peta, ar_doc
 */
function cf_kumpul_sumber($conn, $cfg)
{
    $mulai = $cfg['mulai'];
    $akhir = $cfg['akhir'];
    $rate  = (float) $cfg['rate'];
    $hasil = array('AR_EXISTING' => array(), 'BOOK_TRANSFER' => array(), 'OTHER_INFLOW' => array(),
                   'PV_EXP' => array(), 'PV_AP' => array());

    /* ---------------------------------------------------------- AR Existing
       DUA SUMBER, satu bentuk keluaran:
         - ada potret  -> dibaca dari tbl_history_projection_det, dokumen yang
                          SAMA dgn yang dipakai mesin ($cfg['ar_doc']);
         - tidak ada   -> dipakai baris hasil hitung ulang yang sudah dibuat
                          mesinnya ($cfg['ar_baris']), BUKAN dihitung lagi di
                          sini - menghitung dua kali berarti 66 detik lagi,
                          dan hasilnya belum tentu sama kalau datanya berubah
                          di sela keduanya.
       Kolom Source Doc diisi nomor potretnya, atau "-" kalau dihitung ulang. */
    $arDoc = !empty($cfg['ar_doc']) ? (string) $cfg['ar_doc'] : '-';
    if (!empty($cfg['ar_doc'])) {
        $ada = cfs_kolom_tabel($conn, 'tbl_history_projection_det');
        if ($ada) {
            $e = mysqli_real_escape_string($conn, $cfg['ar_doc']);
            $q = mysqli_query($conn, "select * from tbl_history_projection_det
                                       where doc_number = '$e'
                                         and coalesce(duedate_update, duedate) between '$mulai' and '$akhir'
                                       order by coalesce(duedate_update, duedate) asc, id asc");
            while ($q && ($r = mysqli_fetch_assoc($q))) {
                $hasil['AR_EXISTING'][] = array(
                    'sumber_doc'     => $arDoc,
                    'customer'       => isset($r['customer']) ? $r['customer'] : '',
                    'no_invoice'     => isset($r['no_invoice']) ? $r['no_invoice'] : '',
                    'inv_date'       => cfs_tgl(isset($r['inv_date']) ? $r['inv_date'] : ''),
                    'duedate'        => cfs_tgl(isset($r['duedate']) ? $r['duedate'] : ''),
                    'duedate_update' => cfs_tgl(isset($r['duedate_update']) ? $r['duedate_update'] : ''),
                    'top'            => isset($r['top']) ? (float) $r['top'] : 0,
                    'curr'           => isset($r['curr']) ? $r['curr'] : '',
                    'amount'         => isset($r['amount']) ? (float) $r['amount'] : 0,
                    'rate'           => isset($r['rate']) ? (float) $r['rate'] : 0,
                    'amount_idr'     => isset($r['amount_idr']) ? (float) $r['amount_idr'] : 0,
                    /* Uang yang benar-benar diterima - sama dgn rumus mesinnya:
                       amount_idr x (collection_amount / total_invoice). */
                    'collection_amount' => (isset($r['total_invoice']) && (float) $r['total_invoice'] != 0)
                        ? ((float) $r['amount_idr'] * (float) $r['collection_amount'] / (float) $r['total_invoice'])
                        : (isset($r['amount_idr']) ? (float) $r['amount_idr'] : 0),
                    'doc_type'       => isset($r['doc_type']) ? $r['doc_type'] : 'Invoice',
                    'bank_acc'       => isset($r['bank_acc']) ? $r['bank_acc'] : '',
                    'profit_center'  => isset($r['profit_center']) ? $r['profit_center'] : '',
                );
            }
        }
    } elseif (!empty($cfg['ar_baris'])) {
        foreach ($cfg['ar_baris'] as $r) {
            $tgl = (string) (isset($r['duedate_update']) && $r['duedate_update']
                             ? $r['duedate_update'] : (isset($r['duedate']) ? $r['duedate'] : ''));
            if ($tgl === '' || $tgl < $mulai || $tgl > $akhir) { continue; }
            $ti  = isset($r['total_invoice']) ? (float) $r['total_invoice'] : 0;
            $ai  = isset($r['amount_idr']) ? (float) $r['amount_idr'] : 0;
            $col = isset($r['collection_amount']) ? (float) $r['collection_amount'] : 0;
            $hasil['AR_EXISTING'][] = array(
                'sumber_doc'     => '-',
                'customer'       => isset($r['customer']) ? $r['customer'] : '',
                'no_invoice'     => isset($r['no_invoice']) ? $r['no_invoice'] : '',
                'inv_date'       => cfs_tgl(isset($r['inv_date']) ? $r['inv_date'] : ''),
                'duedate'        => cfs_tgl(isset($r['duedate']) ? $r['duedate'] : ''),
                'duedate_update' => cfs_tgl(isset($r['duedate_update']) ? $r['duedate_update'] : ''),
                'top'            => isset($r['top']) ? (float) $r['top'] : 0,
                'curr'           => isset($r['curr']) ? $r['curr'] : '',
                'amount'         => isset($r['amount']) ? (float) $r['amount'] : 0,
                'rate'           => isset($r['rate']) ? (float) $r['rate'] : 0,
                'amount_idr'     => $ai,
                /* Rumusnya sama persis dgn jalur potret. */
                'collection_amount' => ($ti != 0) ? ($ai * $col / $ti) : $ai,
                'doc_type'       => isset($r['doc_type']) ? $r['doc_type'] : 'Invoice',
                'bank_acc'       => isset($r['bank_acc']) ? $r['bank_acc'] : '',
                'profit_center'  => isset($r['profit_center']) ? $r['profit_center'] : '',
            );
        }
    }

    /* ------------------------------- Book Transfer / Other Inflow / PV EXP
       Satu tabel, tiga jenis - dipisah ke lembarnya masing-masing. */
    $q = mysqli_query($conn, "select * from ap_cf_projection
                               where status <> 'Cancel' and tanggal between '$mulai' and '$akhir'
                               order by tanggal asc, id asc");
    while ($q && ($r = mysqli_fetch_assoc($q))) {
        $jenis = strtoupper(trim($r['jenis']));
        if ($jenis === 'BOOK TRANSFER') {
            /* Dua sisi, dua rekening, dua kurs: yang keluar dinilai dgn kurs
               rekening ASAL, yang masuk dgn kurs rekening TUJUAN - persis
               seperti yang dilakukan mesinnya. */
            $kAsal = cfs_kurs($cfg['akun'], $r['akun_source'], $rate);
            $kTuju = cfs_kurs($cfg['akun'], $r['akun_tujuan'], $rate);
            $terima = ($r['amount_tujuan'] !== null && (float) $r['amount_tujuan'] != 0)
                    ? abs((float) $r['amount_tujuan']) : abs((float) $r['amount']);
            $hasil['BOOK_TRANSFER'][] = array(
                'sumber_doc' => (string) $r['no_doc'],
                'akun_source' => $r['akun_source'], 'akun_tujuan' => $r['akun_tujuan'],
                'curr' => $r['curr'], 'amount' => (float) $r['amount'], 'rate' => $kAsal,
                'amount_idr' => abs((float) $r['amount']) * $kAsal,
                'amount_tujuan' => $terima, 'idr_tujuan' => $terima * $kTuju,
                'tanggal' => cfs_tgl($r['tanggal']),
                'pc_source' => $r['pc_source'], 'pc_tujuan' => $r['pc_tujuan'],
                'intra_inter' => $r['intra_inter'],
                'rate_simpan' => (float) $r['rate'], 'idr_simpan' => (float) $r['amount_idr'],
            );
        } elseif ($jenis === 'OTHER INFLOW') {
            $akunIni = ($r['akun_tujuan'] !== '' ? $r['akun_tujuan'] : $r['akun_source']);
            $kurs = cfs_kurs($cfg['akun'], $akunIni, $rate);
            $hasil['OTHER_INFLOW'][] = array(
                'sumber_doc' => (string) $r['no_doc'],
                'nama_supplier' => $r['nama_supplier'], 'kategori' => $r['kategori'],
                'curr' => $r['curr'], 'amount' => (float) $r['amount'], 'rate' => $kurs,
                'amount_idr' => abs((float) $r['amount']) * $kurs, 'tanggal' => cfs_tgl($r['tanggal']),
                'pc' => ($r['pc_tujuan'] !== '' ? $r['pc_tujuan'] : $r['pc_source']),
                'akun' => $akunIni,
                'rate_simpan' => (float) $r['rate'], 'idr_simpan' => (float) $r['amount_idr'],
            );
        } else {
            $akunIni = ($r['akun_source'] !== '' ? $r['akun_source'] : $r['akun_tujuan']);
            $kurs = cfs_kurs($cfg['akun'], $akunIni, $rate);
            /* TIDAK pakai kolom baru di lembar ini (permintaan pemakai 19 Nov
               2026: "untuk PV Expense simpan saja di nomor PV"). Baris dari
               Projection (Other) memang tidak punya nomor PV - kolom itu
               selama ini KOSONG - jadi diisi nomor dokumennya sendiri.
               Baris rencana bayar di bawah sudah punya nomor PV-nya. */
            $hasil['PV_EXP'][] = array(
                'no_pv' => (($r['no_pv'] !== null && $r['no_pv'] !== '') ? $r['no_pv'] : (string) $r['no_doc']),
                'supplier' => $r['nama_supplier'], 'kategori' => $r['kategori'],
                'curr' => $r['curr'], 'amount' => (float) $r['amount'], 'rate' => $kurs,
                'amount_idr' => abs((float) $r['amount']) * $kurs, 'tanggal' => cfs_tgl($r['tanggal']),
                'pc' => ($r['pc_source'] !== '' ? $r['pc_source'] : $r['pc_tujuan']),
                'akun' => $akunIni,
                'sumber' => 'Cash Flow Projection (Other)',
                'rate_simpan' => (float) $r['rate'], 'idr_simpan' => (float) $r['amount_idr'],
            );
        }
    }

    /* ---- Rencana bayar (PV Biaya + DP + CBD): lembar PV EXP yang sama ----
       Potongan SQL-nya dari pvb_dpcbd_lib.php, persis yang dipakai mesin
       proyeksinya - kalau dua tempat ini berbeda, lembar rinciannya akan
       menyalahkan angka di laporannya sendiri. */
    $q = mysqli_query($conn, "show tables like 'ap_pvbiaya_plan_det'");
    if ($q && mysqli_num_rows($q)) {
        require_once __DIR__ . '/pvb_dpcbd_lib.php';
        $src = pvb_plan_sumber($conn);
        $ktgNama = array();
        foreach ($cfg['ktg'] as $id => $k) { $ktgNama[(int) $id] = $k['nama']; }
        $q = mysqli_query($conn, "
            select d.no_pv, d.plan_date, d.curr, d.amount, d.profit_center,
                   {$src['jenis']} jenis, {$src['supp']} nama_supp,
                   {$src['akun']} frm_akun, {$src['ktg']} id_cash_flow
              from ap_pvbiaya_plan_det d
              inner join ap_pvbiaya_plan_h h on h.doc_number = d.doc_number and h.status <> 'CANCEL'
              {$src['join']}
             where d.status = 'Y' and d.plan_date between '$mulai' and '$akhir'
             order by d.plan_date asc, d.id asc");
        while ($q && ($r = mysqli_fetch_assoc($q))) {
            $id = (int) $r['id_cash_flow'];
            /* Kursnya ikut REKENING PEMBAYARNYA, bukan kolom curr barisnya -
               itulah yang dipakai mesin waktu menaruh nilainya. */
            $kurs = cfs_kurs($cfg['akun'], (string) $r['frm_akun'], $rate);
            $hasil['PV_EXP'][] = array(
                'no_pv' => $r['no_pv'], 'supplier' => (string) $r['nama_supp'],
                'kategori' => isset($ktgNama[$id]) ? $ktgNama[$id] : '(no cash flow category)',
                'curr' => $r['curr'], 'amount' => (float) $r['amount'], 'rate' => $kurs,
                'amount_idr' => abs((float) $r['amount']) * $kurs,
                'tanggal' => cfs_tgl($r['plan_date']),
                'pc' => (string) $r['profit_center'], 'akun' => (string) $r['frm_akun'],
                'sumber' => 'Update Payment Date - ' . (string) $r['jenis'],
                'rate_simpan' => null, 'idr_simpan' => null,
            );
        }
    }

    /* ----------------------------------------------------------- PV AP
       Barisnya TIDAK diambil ulang dari basis data: dipakai keluaran mesin
       proyeksi AP yang sudah dipanggil, jadi lembar ini pasti sejalan dgn
       baris Utang Usaha di laporannya. */
    $peta = isset($cfg['ap_peta']) ? $cfg['ap_peta'] : array();

    /* Status Supplier = mastersupplier.payment_rating, sama dgn warna
       prioritas yang dipakai layar Payment Plan. Diambil SEKALI untuk seluruh
       hasil, bukan satu query per baris. */
    $petaPc   = cfs_peta_pc($conn);
    $petaBank = cfs_peta_bank($conn);

    $rating = array();
    $qr = mysqli_query($conn, "select Supplier, payment_rating from mastersupplier
                                where payment_rating is not null and payment_rating <> ''");
    while ($qr && ($r = mysqli_fetch_assoc($qr))) {
        $rating[strtoupper(trim($r['Supplier']))] = strtoupper(trim($r['payment_rating']));
    }

    foreach ($cfg['ap_baris'] as $b) {
        $no   = isset($b['no_kbon']) ? $b['no_kbon'] : '';
        $inf  = isset($peta[$no]) ? $peta[$no] : null;
        $supp = html_entity_decode(isset($b['supplier']) ? $b['supplier'] : '', ENT_QUOTES);
        $rt   = isset($rating[strtoupper(trim($supp))]) ? $rating[strtoupper(trim($supp))] : '';
        $due  = cfs_tgl(isset($b['duedate']) ? $b['duedate'] : '');
        $proy = cfs_tgl(isset($b['tgl_proyeksi']) ? $b['tgl_proyeksi'] : '');

        /* Days Deffered = berapa hari pembayarannya mundur dari jatuh tempo.
           Di acuan selalu bilangan bulat positif untuk yang sudah lewat. */
        $hari = null;
        if ($due !== '' && $proy !== '') {
            $hari = (int) round((strtotime($proy) - strtotime($due)) / 86400);
        }

        /* Alert diturunkan dari Status Supplier - DIUKUR dari acuan: 22 dari
           23 baris CRITICAL suppliernya Red, dan seluruh ON-QUEUE suppliernya
           Yellow/Green. Jadi tidak perlu diketik tangan. */
        $alert = '';
        if ($rt === 'RED') { $alert = 'CRITICAL: PRODUCTION AT RISK'; }
        elseif ($rt !== '') { $alert = 'ON-QUEUE'; }

        $baris = array(
            'supplier' => $supp,
            'no_kbon' => html_entity_decode($no, ENT_QUOTES),
            'tgl_kbon' => cfs_tgl(isset($b['tgl_kbon']) ? $b['tgl_kbon'] : ''),
            'duedate' => $due,
            'creator' => $inf && isset($inf['create_user']) ? (string) $inf['create_user'] : '',
            'curr' => isset($b['curr']) ? $b['curr'] : '',
            'invoice_amount' => cfs_angka(isset($b['invoice_amount']) ? $b['invoice_amount'] : 0),
            'total_invoice' => cfs_angka(isset($b['total_invoice']) ? $b['total_invoice'] : 0),
            'relasi' => html_entity_decode(isset($b['relasi']) ? $b['relasi'] : '', ENT_QUOTES),
            'status_supplier' => $rt === '' ? '' : ucfirst(strtolower($rt)),
            /* Dipakai cf_sumber_kolom() untuk menyusun kolom tanggalnya. */
            'tgl_proyeksi' => $proy,
            /* SELALU 0 di sini - lihat keterangan susunan kolom di atas. */
            'outstanding_sisa' => 0,
            'days_deffered' => $hari,
            'alert' => $alert,
            'remarks' => '',
            'pc' => ($inf && isset($petaPc[strtoupper(trim($inf['profit_center']))]))
                    ? $petaPc[strtoupper(trim($inf['profit_center']))]
                    : ($inf ? (string) $inf['profit_center'] : ''),
            'akun' => ($inf && isset($petaBank[trim($inf['from_account'])]))
                    ? $petaBank[trim($inf['from_account'])]
                    : ($inf ? (string) $inf['from_account'] : ''),
        );
        /* Nilai kasnya ditaruh di kolom tanggalnya sendiri, persis acuan. */
        if ($proy !== '') {
            $baris['d_' . $proy] = cfs_angka(isset($b['outstanding']) ? $b['outstanding'] : 0);
        }
        $hasil['PV_AP'][] = $baris;
    }

    return $hasil;
}
