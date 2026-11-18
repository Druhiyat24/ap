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
            'kolom' => array(
                array('Supplier', 'supplier', 't'),
                array('Payment Voucher No', 'no_kbon', 't'),
                array('PV Date', 'tgl_kbon', 'd'),
                array('Payment Term', 'term', 't'),
                array('Due Date', 'duedate', 'd'),
                array('Payment Plan Date', 'tgl_proyeksi', 'd'),
                array('Relationship', 'relasi', 't'),
                array('Item Type', 'item_type', 't'),
                array('Flexibility Category', 'flexibility', 't'),
                array('Currency', 'curr', 't'),
                array('Invoice Amount', 'invoice_amount', 'n'),
                array('Rate', 'rate', 'n'),
                array('Tax Base', 'tax_base', 'n'),
                array('VAT', 'vat', 'n'),
                array('Total Invoice', 'total_invoice', 'n'),
                array('Income Tax Art 23', 'pph', 'n'),
                array('Payment Amount', 'payment_amount', 'n'),
                array('Projected Cash Out', 'outstanding', 'n'),
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
       Dokumen potret AR yang SAMA dgn yang dipakai mesin (nomornya dikirim
       lewat $cfg['ar_doc']), disaring dgn tanggal jatuh tempo yang sama. */
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
            $hasil['PV_EXP'][] = array(
                'no_pv' => $r['no_pv'], 'supplier' => $r['nama_supplier'], 'kategori' => $r['kategori'],
                'curr' => $r['curr'], 'amount' => (float) $r['amount'], 'rate' => $kurs,
                'amount_idr' => abs((float) $r['amount']) * $kurs, 'tanggal' => cfs_tgl($r['tanggal']),
                'pc' => ($r['pc_source'] !== '' ? $r['pc_source'] : $r['pc_tujuan']),
                'akun' => $akunIni,
                'sumber' => 'Cash Flow Projection (Other)',
                'rate_simpan' => (float) $r['rate'], 'idr_simpan' => (float) $r['amount_idr'],
            );
        }
    }

    /* ---- PV Biaya: rencana bayar, masuk ke lembar PV EXP yang sama ---- */
    $q = mysqli_query($conn, "show tables like 'ap_pvbiaya_plan_det'");
    if ($q && mysqli_num_rows($q)) {
        $ktgNama = array();
        foreach ($cfg['ktg'] as $id => $k) { $ktgNama[(int) $id] = $k['nama']; }
        $q = mysqli_query($conn, "
            select d.no_pv, d.plan_date, d.curr, d.amount, d.profit_center,
                   p.nama_supp, p.frm_akun, p.id_cash_flow
              from ap_pvbiaya_plan_det d
              inner join ap_pvbiaya_plan_h h on h.doc_number = d.doc_number and h.status <> 'CANCEL'
              left  join tbl_pv_h p on p.no_pv = d.no_pv
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
                'sumber' => 'Update Payment Date PV Biaya',
                'rate_simpan' => null, 'idr_simpan' => null,
            );
        }
    }

    /* ----------------------------------------------------------- PV AP
       Barisnya TIDAK diambil ulang dari basis data: dipakai keluaran mesin
       proyeksi AP yang sudah dipanggil, jadi lembar ini pasti sejalan dgn
       baris Utang Usaha di laporannya. */
    $peta = isset($cfg['ap_peta']) ? $cfg['ap_peta'] : array();
    foreach ($cfg['ap_baris'] as $b) {
        $no  = isset($b['no_kbon']) ? $b['no_kbon'] : '';
        $inf = isset($peta[$no]) ? $peta[$no] : null;
        $hasil['PV_AP'][] = array(
            'supplier' => html_entity_decode(isset($b['supplier']) ? $b['supplier'] : '', ENT_QUOTES),
            'no_kbon' => html_entity_decode($no, ENT_QUOTES),
            'tgl_kbon' => cfs_tgl(isset($b['tgl_kbon']) ? $b['tgl_kbon'] : ''),
            'term' => isset($b['term']) ? $b['term'] : '',
            'duedate' => cfs_tgl(isset($b['duedate']) ? $b['duedate'] : ''),
            'tgl_proyeksi' => cfs_tgl(isset($b['tgl_proyeksi']) ? $b['tgl_proyeksi'] : ''),
            'relasi' => html_entity_decode(isset($b['relasi']) ? $b['relasi'] : '', ENT_QUOTES),
            'item_type' => isset($b['item_type']) ? $b['item_type'] : '',
            'flexibility' => isset($b['flexibility']) ? $b['flexibility'] : '',
            'curr' => isset($b['curr']) ? $b['curr'] : '',
            'invoice_amount' => cfs_angka(isset($b['invoice_amount']) ? $b['invoice_amount'] : 0),
            'rate' => cfs_angka(isset($b['rate']) ? $b['rate'] : 0),
            'tax_base' => cfs_angka(isset($b['tax_base']) ? $b['tax_base'] : 0),
            'vat' => cfs_angka(isset($b['vat']) ? $b['vat'] : 0),
            'total_invoice' => cfs_angka(isset($b['total_invoice']) ? $b['total_invoice'] : 0),
            'pph' => cfs_angka(isset($b['pph']) ? $b['pph'] : 0),
            'payment_amount' => cfs_angka(isset($b['payment_amount']) ? $b['payment_amount'] : 0),
            'outstanding' => cfs_angka(isset($b['outstanding']) ? $b['outstanding'] : 0),
            'pc' => $inf ? (string) $inf['profit_center'] : '',
            'akun' => $inf ? (string) $inf['from_account'] : '',
        );
    }

    return $hasil;
}
