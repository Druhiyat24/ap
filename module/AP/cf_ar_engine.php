<?php
/* ============================================================================
   MESIN PROYEKSI AR - SALINAN dari ar_dev.

   KENAPA ADA DI SINI. Cash Flow Projection memakai POTRET proyeksi AR
   (tbl_history_projection_*) selama ada potret yang periodenya menyelimuti
   rentang laporan. Kalau TIDAK ADA, dulu baris piutangnya nol begitu saja.
   Sejak 18 Nov 2026 (permintaan pemakai) periode yang belum punya potret
   DIHITUNG ULANG dgn mesin AR yang sama - tetapi TIDAK DISIMPAN: tidak ada
   satu pun baris yang ditulis ke tbl_history_projection_h/_det dari sini.
   Menyimpan potret AR tetap wewenang menu AR-nya sendiri.

   ISINYA SALINAN PERSIS, BUKAN TULISAN ULANG. Diambil apa adanya dari
   ar_dev/application/models/Model_report.php:
       baris  859 - 1232  (fungsi-fungsi pembantu)
       baris 1392 - 1540  (cari_projection_report_export)
   Sengaja tidak dirapikan sedikit pun. Alasannya: kalau nanti ar_dev
   memperbaiki rumus proyeksinya, selisihnya bisa dilihat sbg DIFF biasa.

   >>> PERINGATAN YANG HARUS DIBACA SEBELUM MENGUBAH APA PUN DI BERKAS INI:
   >>> ini SALINAN. Perbaikan rumus proyeksi AR tempatnya di ar_dev, lalu
   >>> disalin lagi ke sini. Memperbaiki di sini saja membuat angka Cash Flow
   >>> diam-diam berbeda dgn laporan AR, dan bedanya tidak akan kelihatan
   >>> sampai ada yang membandingkan dua laporan itu baris per baris.
   >>> Pemakai sudah diberi tahu risiko ini waktu memilih cara salin
   >>> (18 Nov 2026); pilihan lain yang ditawarkan adalah endpoint di ar_dev.

   CARA MENYALIN ULANG kalau ar_dev berubah: jalankan kembali pembuatnya,
   scratchpad/buat_cf_ar_engine.py - yang perlu disesuaikan cuma nomor
   barisnya kalau fungsi-fungsi itu bergeser.

   JEMBATANNYA. Berkas aslinya CodeIgniter dan memakai $this->db. Di sini
   disediakan tiruan seperlunya (query, escape_str, row, row_array,
   result_array) di atas koneksi mysqli biasa - jadi badan fungsinya tidak
   perlu disentuh sama sekali.
   ============================================================================ */

if (!class_exists('CfArHasil')) {

/** Tiruan hasil query CodeIgniter, seperlunya saja. */
class CfArHasil
{
    private $q;
    public function __construct($q) { $this->q = $q; }

    public function result_array()
    {
        $r = array();
        if (is_object($this->q)) {
            while ($b = mysqli_fetch_assoc($this->q)) { $r[] = $b; }
        }
        return $r;
    }

    public function row_array()
    {
        return is_object($this->q) ? (mysqli_fetch_assoc($this->q) ?: array()) : array();
    }

    /** CI mengembalikan OBJEK di sini, jadi tiruannya juga objek. */
    public function row()
    {
        if (!is_object($this->q)) { return null; }
        $b = mysqli_fetch_assoc($this->q);
        return $b ? (object) $b : null;
    }
}

/** Tiruan $this->db milik CodeIgniter, di atas mysqli. */
class CfArDb
{
    private $conn;
    public $salah = '';
    public function __construct($conn) { $this->conn = $conn; }

    public function query($sql)
    {
        $q = mysqli_query($this->conn, $sql);
        if ($q === false) { $this->salah = mysqli_error($this->conn); }
        return new CfArHasil($q);
    }

    public function escape_str($s)
    {
        return mysqli_real_escape_string($this->conn, (string) $s);
    }
}

class CfArEngine
{
    private $db;
    public function __construct($conn) { $this->db = new CfArDb($conn); }
    public function salah() { return $this->db->salah; }

/* ====================== MULAI SALINAN DARI ar_dev ======================
   Model_report.php baris 859-1232. JANGAN DIRAPIKAN - lihat peringatan di
   kepala berkas.
   ====================================================================== */

private function _tanggal_rate($type, $end_date = null)
{
    if ($type === 'daily') {
        return date('Y-m-d');
    }
    if ($type === 'monthly') {
        return date('Y-m-d', strtotime(date('Y-m-01', strtotime($end_date)) . ' -1 day'));
    }
    // weekly — periode Minggu s/d Sabtu, rate diambil dari Jumat
    return date('Y-m-d', strtotime($end_date . ' -8 day'));
}

// ── Helper: ambil rate HARIAN invoice dari masterrate berdasarkan type ──
// Fallback: rate HARIAN terdekat SEBELUM tanggal acuan kalau tanggal itu belum
// diinput (hari libur, atau tanggalnya belum lewat). Tanpa ini ratenya jatuh ke
// 1 dan seluruh baris USD terbaca seolah kursnya 1.
private function _rate_invoice($type, $end_date = null)
{
    $tgl = $this->db->escape_str($this->_tanggal_rate($type, $end_date));
    $r   = $this->db->query("SELECT rate FROM masterrate
                              WHERE v_codecurr = 'HARIAN' AND curr = 'USD'
                                AND tanggal <= '$tgl' AND rate > 0
                           ORDER BY tanggal DESC LIMIT 1")->row();
    return ($r && $r->rate) ? (float)$r->rate : 1;
}

// ── Helper: ganti acuan CURRENT_DATE() pada query invoice (sj_date/tgl_dn)
// dan alokasi/pembayaran (tgl_alk) supaya data yang ditarik mengikuti periode
// filter ($start/$end), bukan tanggal hari ini:
//   - sj_date / tgl_dn  → batas atas pakai $end
//   - tgl_alk           → pakai $start (tgl_alk < $start)
// Hanya berlaku untuk type weekly & monthly — daily tetap pakai CURRENT_DATE().
// Bagian rate, aging, readydue, dll TIDAK ikut diganti (tetap CURRENT_DATE()).
private function _apply_end_date_filter($sql, $start, $end, $type)
{
    if ($type === 'daily' || $type === 'weekly') {
        return $sql;
    }

    $start_lit = "''" . $this->db->escape_str($start) . "''";
    $end_lit   = "''" . $this->db->escape_str($end) . "''";

    $sql = str_replace(
        "sj_date between ''2022-05-01'' and CURRENT_DATE()",
        "sj_date between ''2022-05-01'' and " . $end_lit,
        $sql
    );
    $sql = str_replace(
        "tgl_dn between ''2022-05-01'' and CURRENT_DATE()",
        "tgl_dn between ''2022-05-01'' and " . $end_lit,
        $sql
    );
    $sql = str_replace(
        "tgl_alk between CURRENT_DATE() and CURRENT_DATE()",
        "tgl_alk < " . $start_lit,
        $sql
    );
    $sql = str_replace(
        "tgl_alk < CURRENT_DATE()",
        "tgl_alk < ''2000-01-01''",
        $sql
    );

    return $sql;
}

// ── Helper: ambil rate HARIAN debit note dari ap_masterrate berdasarkan type ──
// Tanggal acuan & fallback-nya sama persis dengan _rate_invoice().
private function _rate_dn($type, $end_date = null)
{
    $tgl = $this->db->escape_str($this->_tanggal_rate($type, $end_date));
    $r   = $this->db->query("SELECT rate FROM ap_masterrate
                              WHERE v_codecurr = 'HARIAN'
                                AND tanggal <= '$tgl' AND rate > 0
                           ORDER BY tanggal DESC LIMIT 1")->row();
    return ($r && $r->rate) ? (float)$r->rate : 1;
}

// Lampirkan breakdown "Receivable Amount" (Tax Base, VAT, Total Invoice, Income
// Tax Art 23, Collection Amount) ke tiap baris hasil projection - dihitung
// TERPISAH disini (bukan disisipkan lagi ke SQL raksasa cari_projection_report),
// biar query utamanya yang sudah sangat rapuh tidak makin berisiko.
//
// SELURUH kolomnya dikunci ke Invoice Amount, tidak ada satu pun yang diambil
// mentah dari tabel potongan:
//
//     Total Invoice = Invoice Amount x Rate
//     VAT           = Total Invoice x (vat / grand_total)     <- porsi PPN invoice itu
//     Tax Base      = Total Invoice - VAT                     <- sisanya, jadi pasti pas
//     PPh 23        = Tax Base x persentase mtax
//     Collection    = Total Invoice - PPh 23
//
// Dengan begitu Tax Base + VAT SELALU sama dengan Total Invoice, dan Total
// Invoice selalu sama dengan Invoice Amount x Rate. Rumus lama mengambil twot,
// vat, dan grand_total mentah-mentah dari tabel potongan - akibatnya angkanya
// tidak ikut berkurang waktu invoicenya dibayar sebagian, dan untuk invoice
// knitting kolom total_other (grand_total = twot + vat + total_other) hilang
// entah ke mana sehingga Tax Base + VAT tidak pernah pas.
//
// Tabel potongannya sekarang dipilih menurut profit_center-nya, BUKAN menurut
// urutan hasil UNION: 889 dari 890 invoice knitting punya baris nyangkut di
// tbl_invoice_pot dengan nilai yang berbeda, dan urutan hasil UNION ALL tidak
// dijamin MySQL. Invoice Manual dari tbl_invoice_nb_pot. Debit Note & saldo
// awal tidak punya baris potongan - tidak kena PPN/PPh (arahan eksplisit).
/**
 * Peta potongan per nomor invoice: berapa bagian PPNnya, dan berapa persen
 * PPh 23nya menurut master pajak.
 *
 * Satu pintu untuk sisi proyeksi MAUPUN sisi realisasi. Kolom Income Tax
 * Art 23 di kedua sisi harus lahir dari angka yang sama - kalau tidak,
 * kolom variancenya cuma memamerkan selisih buatan sendiri.
 */
private function peta_pph_invoice(array $no_invoices)
{
    if (empty($no_invoices)) { return array(); }

    $esc = array_map(function ($v) { return "'" . $this->db->escape_str($v) . "'"; }, $no_invoices);
    $in_list = implode(',', $esc);

    $pot = $this->db->query("
        SELECT bi.no_invoice, p.twot, p.vat, p.grand_total, bi.id_pph
        FROM tbl_book_invoice bi
        INNER JOIN tbl_invoice_pot p ON p.id_book_invoice = bi.id
        WHERE bi.no_invoice IN ($in_list) AND bi.profit_center <> 'NAK'
        UNION ALL
        SELECT bik.no_invoice, pk.twot, pk.vat, pk.grand_total, bik.id_pph
        FROM tbl_book_invoice bik
        INNER JOIN tbl_invoice_pot_knitting pk ON pk.id_book_invoice = bik.id
        WHERE bik.no_invoice IN ($in_list) AND bik.profit_center = 'NAK'
        UNION ALL
        SELECT nb.no_inv AS no_invoice, np.twot, np.vat, np.grand_total, nb.id_pph
        FROM tbl_invoice_nb nb
        INNER JOIN tbl_invoice_nb_pot np ON np.no_inv = nb.no_inv
        WHERE nb.no_inv IN ($in_list)
    ")->result_array();

    // Yang pertama ketemu dipakai - cabangnya sudah saling lepas lewat
    // profit_center, jadi satu nomor invoice tidak mungkin punya dua baris.
    $potMap = [];
    foreach ($pot as $p) {
        if (!isset($potMap[$p['no_invoice']])) { $potMap[$p['no_invoice']] = $p; }
    }

    $mtax = $this->db->query("SELECT idtax, percentage FROM mtax")->result_array();
    $pctMap = [];
    foreach ($mtax as $m) { $pctMap[$m['idtax']] = (float) $m['percentage']; }

    $peta = array();
    foreach ($potMap as $no => $p) {
        $peta[$no] = array(
            'twot'        => (float) $p['twot'],
            'vat'         => (float) $p['vat'],
            'grand_total' => (float) $p['grand_total'],
            'pct'         => ($p['id_pph'] !== null && isset($pctMap[$p['id_pph']]))
                                ? $pctMap[$p['id_pph']] : 0,
        );
    }
    return $peta;
}

/**
 * PPh 23 satu baris alokasi menurut INVOICEnya sendiri: bagian non-PPN dari
 * nilai yang dialokasikan, dikali persentase PPh invoice itu. Satuannya ikut
 * satuan $nilai, jadi tidak ada kurs yang dipakai di sini.
 */
private function pph_wajar_baris($peta, $no_invoice, $nilai)
{
    if (!isset($peta[$no_invoice])) { return null; }

    $p     = $peta[$no_invoice];
    $grand = $p['grand_total'];
    $vat   = ($grand != 0) ? round($nilai * ($p['vat'] / $grand), 2) : 0;

    return round(($nilai - $vat) * $p['pct'] / 100, 2);
}

/**
 * Peta Type + Bank & Acc No per nomor dokumen.
 *
 * DUA PERTANYAAN, SATU JAWABAN. Keduanya dijawab dari TABEL ASAL dokumen
 * itu, bukan dari pola nomornya: barisnya ada di tbl_debitnote_h berarti
 * Debit Note, ada di tbl_book_invoice / tbl_invoice_nb berarti Invoice.
 * Pola nomor ('%DN/%') cuma dipakai untuk saldo awal, yang memang tidak
 * punya tabel asal lain.
 *
 * Rekeningnya disimpan di tempat yang berbeda-beda - itu keadaan datanya,
 * bukan pilihan di sini:
 *   tbl_book_invoice.id_bank -> masterbank.id      (invoice booking)
 *   tbl_invoice_nb.bank      -> masterbank.id      (invoice manual, teks)
 *   tbl_debitnote_h.akun     -> masterbank.no_rek  (debit note)
 *   saldoawal_ar             -> tidak ada sama sekali
 *
 * Yang dikembalikan sudah berbentuk tampilan ("BANK CENTRAL ASIA -
 * 008-751-5757"), sebab tiga sumber di atas tidak punya satu kunci bersama
 * yang bisa dibawa ke mana-mana.
 *
 * Dipakai sisi PROYEKSI (dihitung saat laporan dibuka) dan sisi REALISASI
 * (untuk invoice yang alokasinya ada tapi tidak ikut terjaring dokumen
 * proyeksinya) - satu pintu, supaya dua layar tidak bisa menjawab beda.
 */
/**
 * Nama bank yang dipendekkan - "BANK CENTRAL ASIA" jadi "BCA".
 *
 * Diminta tim AR 5 Nov 2026: nama panjangnya memakan satu kolom penuh di
 * laporan yang kolomnya sudah banyak, dan yang dibaca orang memang
 * singkatannya.
 *
 * DUA LAPIS, sengaja:
 *   1. Daftar singkatan yang sudah baku. BCA/BNI/BRI kebetulan memang
 *      huruf depan tiap katanya, tapi CIMB Niaga tidak ("BCN" bukan nama
 *      yang dikenal siapa pun) - jadi tidak bisa diserahkan ke aturan.
 *   2. Baru kalau tidak ada di daftar: huruf depan tiap kata. Angka di
 *      belakang nama dibuang dulu - di master ada baris bernama
 *      "MANDIRI 130-0002077777", dan nomor rekening sudah punya kolomnya
 *      sendiri di sebelahnya.
 *
 * Nama yang tinggal SATU kata dikembalikan apa adanya: "MANDIRI" sudah
 * sependek yang bisa, dan "M" tidak memberi tahu apa pun.
 */
private function singkat_bank($nama)
{
    $nama = trim((string) $nama);
    if ($nama === '') { return ''; }

    $besar = strtoupper($nama);
    $baku = array(
        'CENTRAL ASIA' => 'BCA',   'NEGARA INDONESIA' => 'BNI',
        'RAKYAT INDONESIA' => 'BRI', 'TABUNGAN NEGARA' => 'BTN',
        'SYARIAH INDONESIA' => 'BSI', 'CIMB' => 'CIMB', 'MANDIRI' => 'MANDIRI',
        'PERMATA' => 'PERMATA', 'DANAMON' => 'DANAMON', 'MAYBANK' => 'MAYBANK',
        'BUKOPIN' => 'BUKOPIN', 'OCBC' => 'OCBC', 'PANIN' => 'PANIN',
        'HSBC' => 'HSBC', 'DBS' => 'DBS', 'UOB' => 'UOB',
    );
    foreach ($baku as $kunci => $singkat) {
        if (strpos($besar, $kunci) !== false) { return $singkat; }
    }

    // Kata yang bukan nama: bentuk badan usaha, dan potongan nomor rekening
    // yang terlanjur ikut tertulis di nama banknya.
    $buang = array('PT', 'TBK', '(PERSERO)', 'PERSERO');
    $kata = array();
    foreach (preg_split('/[\s,.]+/', $besar) as $k) {
        $k = trim($k);
        if ($k === '' || in_array($k, $buang, true)) { continue; }
        if (preg_match('/^[0-9\-]+$/', $k)) { continue; }
        $kata[] = $k;
    }
    if (!$kata) { return $besar; }
    if (count($kata) === 1) { return $kata[0]; }

    $singkat = '';
    foreach ($kata as $k) { $singkat .= substr($k, 0, 1); }
    return $singkat;
}

private function peta_bank_invoice(array $no_invoices)
{
    if (empty($no_invoices)) { return array(); }

    $esc = array_map(function ($v) { return "'" . $this->db->escape_str($v) . "'"; }, $no_invoices);
    $in_list = implode(',', $esc);

    // urut_sumber menentukan siapa yang menang kalau satu nomor kebetulan
    // ada di dua tabel - bukan urutan baris hasil, yang tidak dijamin MySQL.
    $baris = $this->db->query("
        SELECT bi.no_invoice, 'Invoice' AS doc_type, 1 AS urut_sumber,
               mb.nama_bank, mb.no_rek
        FROM tbl_book_invoice bi
        LEFT JOIN masterbank mb ON mb.id = bi.id_bank
        WHERE bi.no_invoice IN ($in_list)
        UNION ALL
        SELECT nb.no_inv, 'Invoice', 2, mb.nama_bank, mb.no_rek
        FROM tbl_invoice_nb nb
        LEFT JOIN masterbank mb ON mb.id = nb.bank
        WHERE nb.no_inv IN ($in_list)
        UNION ALL
        SELECT dn.no_dn, 'Debit Note', 3, mb.nama_bank, mb.no_rek
        FROM tbl_debitnote_h dn
        LEFT JOIN masterbank mb ON mb.no_rek = dn.akun
        WHERE dn.no_dn IN ($in_list)
    ")->result_array();

    $peta = array();
    foreach ($baris as $b) {
        $no = $b['no_invoice'];
        if (isset($peta[$no]) && $peta[$no]['_urut'] <= (int) $b['urut_sumber']) { continue; }

        $bank = '-';
        if ($b['nama_bank'] !== null && trim((string) $b['nama_bank']) !== '') {
            $bank = $this->singkat_bank($b['nama_bank']);
            if (trim((string) $b['no_rek']) !== '') { $bank .= ' - ' . trim($b['no_rek']); }
        }
        $peta[$no] = array('doc_type' => $b['doc_type'], 'bank_acc' => $bank,
                           '_urut' => (int) $b['urut_sumber']);
    }
    return $peta;
}

/**
 * Type & Bank satu baris, lengkap dengan cadangannya untuk dokumen yang
 * tidak ketemu di tabel mana pun - yaitu saldo awal. Di situ satu-satunya
 * penanda jenisnya ya nomor dokumennya sendiri, dan banknya memang tidak
 * pernah ada.
 */
private function jenis_bank_baris($peta, $no_invoice)
{
    if (isset($peta[$no_invoice])) {
        return array($peta[$no_invoice]['doc_type'], $peta[$no_invoice]['bank_acc']);
    }
    return array((strpos((string) $no_invoice, 'DN/') !== false) ? 'Debit Note' : 'Invoice', '-');
}

private function _attach_receivable_breakdown($rows)
{
    if (empty($rows)) return $rows;

    $no_invoices = array_values(array_unique(array_column($rows, 'no_invoice')));
    $potMap = $this->peta_pph_invoice($no_invoices);
    $bankMap = $this->peta_bank_invoice($no_invoices);

    foreach ($rows as &$r) {
        // Receivable Amount ditampilkan dalam IDR, beda dengan kolom Invoice
        // Amount yang tetap pakai mata uang asli invoicenya.
        //
        // Titik tolaknya SELALU Invoice Amount x Rate. Yang dicari ke tabel
        // potongan cuma satu: berapa bagian PPN dari invoice itu.
        $totalInv = (float) $r['amount_idr'];
        $vat      = 0;
        $pct      = 0;

        if (isset($potMap[$r['no_invoice']])) {
            $p     = $potMap[$r['no_invoice']];
            $grand = $p['grand_total'];

            if ($grand != 0) {
                $vat = round($totalInv * ($p['vat'] / $grand), 2);
            }
            $pct = $p['pct'];
        }
        // Dihitung sebagai SISA, bukan dari twot - supaya penjumlahannya pasti
        // kembali ke Total Invoice berapa pun pembulatannya, dan ongkos lain
        // (total_other di invoice knitting) tidak hilang di tengah jalan.
        $taxBase = $totalInv - $vat;
        $pph23   = round($taxBase * $pct / 100, 2);

        $r['tax_base']          = $taxBase;
        $r['tax_vat']           = $vat;
        $r['total_invoice']     = $totalInv;
        $r['income_tax_23']     = $pph23;
        $r['collection_amount'] = $totalInv - $pph23;

        // Type (Invoice / Debit Note) & Bank & Acc No - diminta tim AR
        // 5 Nov 2026. Ditempel DI SINI, bukan di query utamanya: query itu
        // satu string CONCAT berlapis yang dirakit jadi prepared statement,
        // dan menyisipkan dua kolom di tengahnya berarti menyentuh tiap
        // cabang UNION-nya. Di sini satu tempat, dan dua-duanya ikut
        // terpakai oleh layar MAUPUN unduhannya sekaligus.
        list($r['doc_type'], $r['bank_acc']) = $this->jenis_bank_baris($bankMap, $r['no_invoice']);

        // Kolom "Projected Cash Inflow" per tanggal (data1..dataN) disesuaikan
        // proporsional ke Collection Amount (sudah dipotong PPh), bukan lagi
        // Amount IDR mentah - biar total per tanggal nyambung ke Collection Amount.
        $amountIdr = (float) $r['amount_idr'];
        $factor    = (abs($amountIdr) > 0.000001) ? ($r['collection_amount'] / $amountIdr) : 0;
        foreach ($r as $key => $val) {
            if (preg_match('/^data\d+$/', $key)) {
                $r[$key] = (float) $val * $factor;
            }
        }
    }
    unset($r);

    return $rows;
}

/* ---------------------- Model_report.php baris 1392-1540 ---------------------- */

public function cari_projection_report_export($id_customer, $start, $end, $type = 'daily')
{
    // supaya GROUP_CONCAT panjang
    $this->db->query("SET SESSION group_concat_max_len = 1000000");

    // Ambil rate sesuai type — $end sebagai tanggal referensi untuk weekly/monthly
    $rate_inv = $this->_rate_invoice($type, $end);
    $rate_dn  = $this->_rate_dn($type, $end);

    // set parameter
    $this->db->query("SET @start = '$start'");
    $this->db->query("SET @end   = '$end'");

    // =========================
    // GENERATE KOLOM DINAMIS
    // =========================
    $sqlCols = "
    WITH RECURSIVE dates AS (
    SELECT @start AS tgl, 1 AS urut
    UNION ALL
    SELECT DATE_ADD(tgl, INTERVAL 1 DAY), urut + 1
    FROM dates
    WHERE tgl < @end
)

SELECT 
GROUP_CONCAT(
    CONCAT(
        'SUM(CASE WHEN COALESCE(b.duedate_update,a.duedate) = ''',
        tgl,
        ''' THEN ((CASE WHEN bay4.no_invoice4 IS NOT NULL THEN COALESCE(bay4.bayar_period,0) ELSE COALESCE(b.amount,total) END) * a.rate) WHEN COALESCE(b.duedate_update,a.duedate) NOT BETWEEN @start AND @end THEN (COALESCE((SELECT SUM(px.amount) FROM tbl_alokasi_detail px INNER JOIN tbl_alokasi py ON py.no_alk = px.no_alk WHERE px.no_ref = a.no_invoice AND px.status != ''CANCEL'' AND px.total != ''0'' AND py.tgl_alk = ''',
        tgl,
        '''),0) * a.rate) ELSE 0 END) AS data',
        urut
    )
) INTO @cols
FROM dates
    ";
    $this->db->query($sqlCols);

    // =========================
    // QUERY UTAMA (PAKAI CTE)
    // =========================
    $sqlMain = "
    SET @sql = CONCAT('WITH
invoice as (select * from (select id_customer, customer, no_invoice, inv_date, shipp, ''-'' vendor, ''-'' attn, duedate, top, curr, rate, total, eqv_idr, amount1 from (select  id_customer, customer, no_invoice, inv_date, shipp, duedate, top, curr, if(curr = ''IDR'', 1, rate) rate, sal_awl, tambah, bayar,  total, eqv_idr, amount1,amt_aging_0,amt_aging_1,amt_aging_2,amt_aging_3,amt_aging_4,amt_aging_5,amt_aging_6,amt_aging_7, tot_aging, readydue, hasil_bln1, hasil_bln2, hasil_bln3, hasil_bln4, hasil_bln5, hasil_bln6, tot_aging tot_jatem from (select a.*, CASE WHEN jml_bln1 > 0 AND Date(duedate) >= CURRENT_DATE() THEN eqv_idr ELSE 0 END AS hasil_bln1,
        CASE WHEN jml_bln2 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln2,
        CASE WHEN jml_bln3 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln3,
        CASE WHEN jml_bln4 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln4,
        CASE WHEN jml_bln5 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln5,
        CASE WHEN jml_bln6 > 0 THEN eqv_idr ELSE 0 END AS hasil_bln6,
        CASE WHEN total <= 0 THEN 0 WHEN Date(duedate) < CURRENT_DATE() THEN eqv_idr ELSE 0 END AS readydue,
        CASE WHEN total <= 0 THEN 0 ELSE eqv_idr END AS tot_aging2,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top <= 0 THEN eqv_idr ELSE 0 END AS amt_aging_0,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 0  AND diff_top <= 30  THEN eqv_idr ELSE 0 END AS amt_aging_1,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 30 AND diff_top <= 60  THEN eqv_idr ELSE 0 END AS amt_aging_2,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 60 AND diff_top <= 90  THEN eqv_idr ELSE 0 END AS amt_aging_3,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 90 AND diff_top <= 120 THEN eqv_idr ELSE 0 END AS amt_aging_4,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 120 AND diff_top <= 180 THEN eqv_idr ELSE 0 END AS amt_aging_5,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 180 AND diff_top <= 360 THEN eqv_idr ELSE 0 END AS amt_aging_6,
        CASE WHEN total <= 0 THEN 0 WHEN diff_top > 360 THEN eqv_idr ELSE 0 END AS amt_aging_7,
        CASE WHEN total <= 0 THEN 0 ELSE eqv_idr END AS tot_aging
        from (select a.*, ((sal_awl + tambah) - bayar) total, IF(curr = ''USD'',((sal_awl + COALESCE(tambah,0)) - COALESCE(bayar,0)) * rate,((sal_awl + COALESCE(tambah,0)) - COALESCE(bayar,0))) eqv_idr from (select no_invoice, kode_customer, customer, inv_date, id_customer, curr, top, amount1, duedate, bayar, bayar2, rate, shipp, diff_top, ready_due, jml_bln1, jml_bln2, jml_bln3, jml_bln4, jml_bln5, jml_bln6, IF(inv_date >= CURRENT_DATE(),0,COALESCE(amount1,0) - COALESCE(bayar2,0)) sal_awl, IF(inv_date >= CURRENT_DATE(),COALESCE(amount1,0) - COALESCE(bayar2,0),0) tambah from (SELECT profit_center, no_invoice,kode_customer, customer,inv_date,tgl_inv,id_customer,curr,top,amount,amount1,duedate,no_invoice1,coalesce(bayar,0) bayar,no_invoice2,bayar2,rate,shipp, diff_top,ready_due, IF(bln_due = fil_bln1 and thn_due = fil_thn1,amount1,''0'') jml_bln1,IF(bln_due = fil_bln2 and thn_due = fil_thn2,amount1,''0'') jml_bln2,IF(bln_due = fil_bln3 and thn_due = fil_thn3,amount1,''0'') jml_bln3,IF(bln_due = fil_bln4 and thn_due = fil_thn4,amount1,''0'') jml_bln4,IF(bln_due = fil_bln5 and thn_due = fil_thn5,amount1,''0'') jml_bln5,IF(bln_due = fil_bln6 and thn_due = fil_thn6,amount1,''0'') jml_bln6 from (SELECT profit_center, no_invoice,kode_customer, customer,inv_date,tgl_inv,id_customer,curr,top,amount,amount1,duedate,no_invoice1,bayar,no_invoice2,bayar2,rate,shipp, diff_top, bln_due, thn_due, LPAD(IF(fil_bln1 <= 12,fil_bln1,(fil_bln1 - 12)),2,0) fil_bln1,LPAD(IF(fil_bln2 <= 12,fil_bln2,(fil_bln2 - 12)),2,0) fil_bln2,LPAD(IF(fil_bln3 <= 12,fil_bln3,(fil_bln3 - 12)),2,0) fil_bln3,LPAD(IF(fil_bln4 <= 12,fil_bln4,(fil_bln4 - 12)),2,0) fil_bln4,LPAD(IF(fil_bln5 <= 12,fil_bln5,(fil_bln5 - 12)),2,0) fil_bln5, LPAD(IF(fil_bln6 <= 12,fil_bln6,(fil_bln6 - 12)),2,0) fil_bln6,LPAD(IF(fil_bln1 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn1,LPAD(IF(fil_bln2 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn2,LPAD(IF(fil_bln3 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn3, LPAD(IF(fil_bln4 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn4,LPAD(IF(fil_bln5 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn5, LPAD(IF(fil_bln6 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn6, ready_due from (SELECT profit_center, no_invoice,kode_customer,customer,inv_date,tgl_inv,id_customer,curr,top,amount,amount1,duedate,no_invoice1,CASE WHEN bayar > 0 AND profit_center = ''NAK'' THEN amount1 ELSE bayar END bayar,no_invoice2, CASE WHEN bayar2 > 0 AND profit_center = ''NAK'' THEN amount1 ELSE bayar2 END bayar2,rate,shipp,DATEDIFF(CURRENT_DATE(),duedate) diff_top, DATE_FORMAT(duedate,''%m'') bln_due, DATE_FORMAT(duedate,''%Y'') thn_due,DATE_FORMAT(CURRENT_DATE(),''%m'') fil_bln1,LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 1,2,0) fil_bln2, LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 2,2,0) fil_bln3,LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 3,2,0) fil_bln4,LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 4,2,0) fil_bln5,LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 5,2,0) fil_bln6, DATE_FORMAT(CURRENT_DATE(),''%Y'') fil_thn, IF(duedate <= CURRENT_DATE(),amount1,0) ready_due from 
            (SELECT distinct a.profit_center, a.no_invoice AS no_invoice, UPPER(b.supplier) AS customer, UPPER(b.supplier_code) kode_customer,a.sj_date inv_date,a.sj_date tgl_inv, b.Id_Supplier AS id_customer, a.curr,f.top,
                FORMAT((d.grand_total), 2) AS amount, if(a.curr = ''IDR'',round((d.grand_total),0),round((d.grand_total), 2)) AS amount1,if(h.kontrabon_date is null, DATE_ADD(DATE_FORMAT(a.sj_date, ''%Y-%m-%d''), INTERVAL f.top DAY) ,DATE_ADD(h.kontrabon_date, INTERVAL f.top DAY)) AS duedate,a.shipp
                FROM  tbl_book_invoice AS a INNER JOIN 
                mastersupplier AS b ON a.id_customer = b.id_supplier INNER JOIN 
                tbl_type AS c ON a.id_type = c.id_type INNER JOIN
                tbl_invoice_pot AS d ON a.id = d.id_book_invoice INNER JOIN
                tbl_master_top AS f ON a.id_top = f.id left join 
                tbl_duedate AS h ON a.id = h.id_invoice
                where a.sj_date between ''2022-05-01'' and CURRENT_DATE() and a.profit_center = ''NAG''
                UNION
                SELECT distinct a.profit_center, a.no_invoice AS no_invoice, UPPER(b.supplier) AS customer, UPPER(b.supplier_code) kode_customer,a.sj_date inv_date,a.sj_date tgl_inv, b.Id_Supplier AS id_customer, a.curr,f.top,
                FORMAT((d.grand_total), 2) AS amount, if(a.curr = ''IDR'',round((d.grand_total),0),round((d.grand_total), 2)) AS amount1,if(h.kontrabon_date is null, DATE_ADD(DATE_FORMAT(a.sj_date, ''%Y-%m-%d''), INTERVAL f.top DAY) ,DATE_ADD(h.kontrabon_date, INTERVAL f.top DAY)) AS duedate,a.shipp
                FROM  tbl_book_invoice AS a INNER JOIN 
                mastersupplier AS b ON a.id_customer = b.id_supplier INNER JOIN 
                tbl_type AS c ON a.id_type = c.id_type INNER JOIN
                tbl_invoice_pot_knitting AS d ON a.id = d.id_book_invoice INNER JOIN
                tbl_master_top AS f ON a.id_top = f.id left join 
                tbl_duedate AS h ON a.id = h.id_invoice
                where a.sj_date between ''2022-05-01'' and CURRENT_DATE() and a.profit_center = ''NAK''
                UNION
                SELECT distinct ''NAG'' profit_center, a.no_inv AS no_invoice, UPPER(b.supplier) AS customer, UPPER(b.supplier_code) kode_customer,DATE_FORMAT(e.sj_date, ''%Y-%m-%d'') AS inv_date,DATE_FORMAT(e.sj_date, ''%Y-%m-%d'') AS tgl_inv, b.Id_Supplier AS id_customer, e.curr,a.top,
                FORMAT((d.grand_total), 2) AS amount, if(e.curr = ''IDR'',round((d.grand_total),0),round((d.grand_total), 2)) AS amount1, if(h.kontrabon_date is null, DATE_ADD(DATE_FORMAT(e.sj_date, ''%Y-%m-%d''), INTERVAL a.top DAY) ,DATE_ADD(DATE_FORMAT(h.kontrabon_date, ''%Y-%m-%d''), INTERVAL a.top DAY)) AS duedate,a.shipp
                FROM  tbl_invoice_nb AS a INNER JOIN 
                mastersupplier AS b ON a.customer = b.id_supplier INNER JOIN 
                tbl_invoice_nb_pot AS d ON a.no_inv = d.no_inv INNER JOIN
                tbl_invoice_nb_detail as e on a.no_inv=e.no_inv left JOIN 
                tbl_duedate AS h ON a.id = h.id_invoice left join
                saldoawal_ar as g on g.no_invoice = a.no_inv
                where g.no_invoice is null and a.status != ''CANCEL'' and e.sj_date between ''2022-05-01'' and CURRENT_DATE()
                union                                                                     
                select ''NAG'' profit_center, no_invoice, customer, UPPER(b.supplier_code) kode_customer, inv_date, sj_date as tgl_inv,id_customer, curr, top, FORMAT((grand_total), 2) AS amount, if(curr = ''IDR'',round((grand_total),0),round((grand_total), 2)) AS amount1, due_date,shipp from saldoawal_ar a INNER JOIN mastersupplier AS b ON a.id_customer = b.id_supplier where no_invoice not like ''%DN/%'') inv LEFT JOIN
            (select a.no_ref as no_invoice1, sum(a.amount) as bayar from tbl_alokasi_detail a inner join tbl_alokasi b on b.no_alk = a.no_alk where a.status != ''CANCEL'' and b.tgl_alk between CURRENT_DATE() and CURRENT_DATE() and a.total != ''0'' group by a.no_ref) byr on byr.no_invoice1 = inv.no_invoice LEFT JOIN
            (select a.no_ref as no_invoice2, sum(a.amount) as bayar2 from tbl_alokasi_detail a inner join tbl_alokasi b on b.no_alk = a.no_alk where a.status != ''CANCEL'' and b.tgl_alk < CURRENT_DATE() and a.total != ''0'' group by a.no_ref) byr2 on byr2.no_invoice2 = inv.no_invoice JOIN
            (select IF((select id from tbl_tgl_tb where tgl_akhir = CURRENT_DATE()) != '''',(select rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = ''HARIAN''),(select rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = ''PAJAK'')) rate) rt) a) a) a) a) a) a WHERE 1=1) a
                        UNION
select id_customer, customer, no_invoice, inv_date, ''-'' shipp, supplier vendor, attn, duedate, top, from_curr curr, rate, ((amount1 - IFNULL(bayar2,0)) - IFNULL(bayar,0)) AS total, ((amount1 - IFNULL(bayar2,0)) - IFNULL(bayar,0)) * IFNULL(rate,1) AS eqv_idr, amount1 from (SELECT no_invoice,customer,attn,inv_date,tgl_inv,id_customer,from_curr,top,amount1, duedate, no_invoice1 , bayar, no_invoice2, bayar2, amount, supplier, rate, diff_top,ready_due, IF(bln_due = fil_bln1 and thn_due = fil_thn1,amount1,''0'') jml_bln1,IF(bln_due = fil_bln2 and thn_due = fil_thn2,amount1,''0'') jml_bln2,IF(bln_due = fil_bln3 and thn_due = fil_thn3,amount1,''0'') jml_bln3,IF(bln_due = fil_bln4 and thn_due = fil_thn4,amount1,''0'') jml_bln4,IF(bln_due = fil_bln5 and thn_due = fil_thn5,amount1,''0'') jml_bln5,IF(bln_due = fil_bln6 and thn_due = fil_thn6,amount1,''0'') jml_bln6 FROM (SELECT no_invoice,customer,attn,inv_date,tgl_inv,id_customer,from_curr,top,amount1, duedate, no_invoice1 , bayar, no_invoice2, bayar2, amount, supplier, rate, diff_top, bln_due, thn_due, LPAD(IF(fil_bln1 <= 12,fil_bln1,(fil_bln1 - 12)),2,0) fil_bln1,LPAD(IF(fil_bln2 <= 12,fil_bln2,(fil_bln2 - 12)),2,0) fil_bln2,LPAD(IF(fil_bln3 <= 12,fil_bln3,(fil_bln3 - 12)),2,0) fil_bln3,LPAD(IF(fil_bln4 <= 12,fil_bln4,(fil_bln4 - 12)),2,0) fil_bln4,LPAD(IF(fil_bln5 <= 12,fil_bln5,(fil_bln5 - 12)),2,0) fil_bln5, LPAD(IF(fil_bln6 <= 12,fil_bln6,(fil_bln6 - 12)),2,0) fil_bln6,LPAD(IF(fil_bln1 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn1,LPAD(IF(fil_bln2 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn2,LPAD(IF(fil_bln3 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn3, LPAD(IF(fil_bln4 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn4,LPAD(IF(fil_bln5 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn5, LPAD(IF(fil_bln6 <= 12,fil_thn,(fil_thn + 1)),4,0) fil_thn6, ready_due FROM (SELECT no_invoice,customer,attn,inv_date,tgl_inv,id_customer,from_curr,top,COALESCE(amount1,0) amount1, duedate, no_invoice1,COALESCE(bayar,0) bayar, no_invoice2,COALESCE(bayar2,0) bayar2, COALESCE(amount,0) amount,IF(supplier is null,''-'',supplier) supplier,IF(from_curr = ''IDR'',''1'',rate) rate,DATEDIFF(CURRENT_DATE(),duedate) diff_top, DATE_FORMAT(duedate,''%m'') bln_due, DATE_FORMAT(duedate,''%Y'') thn_due,DATE_FORMAT(CURRENT_DATE(),''%m'') fil_bln1,LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 1,2,0) fil_bln2, LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 2,2,0) fil_bln3,LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 3,2,0) fil_bln4,LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 4,2,0) fil_bln5,LPAD(DATE_FORMAT(CURRENT_DATE(),''%m'') + 5,2,0) fil_bln6, DATE_FORMAT(CURRENT_DATE(),''%Y'') fil_thn, IF(duedate <= CURRENT_DATE(),amount1,0) ready_due  from 
        (
            SELECT distinct a.no_dn AS no_invoice, UPPER(b.supplier) AS customer, a.attn,DATE_FORMAT(a.tgl_dn, ''%Y-%m-%d'') AS inv_date,DATE_FORMAT(a.tgl_dn, ''%Y-%m-%d'') AS tgl_inv, b.Id_Supplier AS id_customer, a.to_curr as from_curr,DATEDIFF(a.due_date,a.tgl_dn) top,
            FORMAT(a.eqv_curr, 2) AS amount, if(a.to_curr = ''USD'',a.eqv_curr,round(a.eqv_curr, 2)) AS amount1,a.due_date duedate
            FROM  tbl_debitnote_h AS a INNER JOIN 
            mastersupplier AS b ON a.customer = b.id_supplier left join
            saldoawal_ar as g on g.no_invoice = a.no_dn
            where g.no_invoice is null and a.status != ''CANCEL'' and a.tgl_dn between ''2022-05-01'' and CURRENT_DATE()
            union

            select no_invoice, customer, '''' attn, inv_date, sj_date as tgl_inv,id_customer, curr, top, FORMAT((grand_total), 2) AS amount, if(curr = ''IDR'',round((grand_total),0),round((grand_total), 2)) AS amount1, due_date from saldoawal_ar where no_invoice like ''%DN/%''
            ) inv LEFT JOIN
        
        (select a.no_ref as no_invoice1, sum(a.amount) as bayar from tbl_alokasi_detail a inner join tbl_alokasi b on b.no_alk = a.no_alk where a.status != ''CANCEL'' and b.tgl_alk between CURRENT_DATE() and CURRENT_DATE() and a.total != ''0'' group by a.no_ref) byr on byr.no_invoice1 = inv.no_invoice LEFT JOIN
        
        (select a.no_ref as no_invoice2, sum(a.amount) as bayar2 from tbl_alokasi_detail a inner join tbl_alokasi b on b.no_alk = a.no_alk where a.status != ''CANCEL'' and b.tgl_alk >= ''2022-05-01'' and b.tgl_alk < CURRENT_DATE() and a.total != ''0'' group by a.no_ref) byr2 on byr2.no_invoice2 = inv.no_invoice LEFT JOIN

        (select no_dn dnno,GROUP_CONCAT(DISTINCT supplier SEPARATOR '' | '') supplier from tbl_debitnote_det where supplier != ''-'' AND supplier != '''' GROUP BY no_dn
            ) l_supp on l_supp.dnno = inv.no_invoice JOIN

        (select IF((select id from tbl_tgl_tb where tgl_akhir = CURRENT_DATE()) != '''',(select rate from ap_masterrate where tanggal = CURRENT_DATE() and v_codecurr = ''HARIAN''),(select rate from ap_masterrate where tanggal = CURRENT_DATE() and v_codecurr = ''HARIAN'')) rate) rate) a) a order by no_invoice asc) a WHERE 1=1) a),
                
                duedate as (SELECT b.no_invoice, b.duedate_update, b.amount FROM tbl_duedate_update_det b INNER JOIN tbl_duedate_update_h a ON a.doc_number = b.doc_number
INNER JOIN (SELECT d.no_invoice, MAX(d.id) AS max_id FROM tbl_duedate_update_det d INNER JOIN tbl_duedate_update_h h ON h.doc_number = d.doc_number WHERE d.status = ''Y'' AND h.status <> ''CANCEL'' GROUP BY d.no_invoice ) x ON b.id = x.max_id)
                
                select a.id_customer, a.customer, a.no_invoice, a.inv_date, a.shipp, a.duedate, COALESCE(b.duedate_update,a.duedate) duedate_update, top, curr, rate, total, eqv_idr, COALESCE((SELECT type_so FROM tbl_book_invoice WHERE no_invoice = a.no_invoice LIMIT 1),(SELECT type_so FROM tbl_invoice_nb WHERE no_inv = a.no_invoice LIMIT 1),''-'') type_so, CASE WHEN bay4.no_invoice4 IS NOT NULL THEN COALESCE(bay4.bayar_period,0) ELSE COALESCE(b.amount,total) END amount, (CASE WHEN bay4.no_invoice4 IS NOT NULL THEN COALESCE(bay4.bayar_period,0) ELSE COALESCE(b.amount,total) END * rate) amount_idr, ', @cols, ' from invoice a LEFT JOIN duedate b on b.no_invoice  = a.no_invoice LEFT JOIN (select x.no_ref as no_invoice3, sum(x.amount) as bayar_before from tbl_alokasi_detail x inner join tbl_alokasi y on y.no_alk = x.no_alk where x.status != ''CANCEL'' and y.tgl_alk < ''',@start,''' and x.total != ''0'' group by x.no_ref) bay3 on bay3.no_invoice3 = a.no_invoice LEFT JOIN (select x.no_ref as no_invoice4, sum(x.amount) as bayar_period from tbl_alokasi_detail x inner join tbl_alokasi y on y.no_alk = x.no_alk where x.status != ''CANCEL'' and y.tgl_alk BETWEEN ''',@start,''' and ''',@end,''' and x.total != ''0'' group by x.no_ref) bay4 on bay4.no_invoice4 = a.no_invoice where (COALESCE(b.duedate_update,a.duedate) BETWEEN ''',@start,''' and ''',@end,''' OR bay4.no_invoice4 IS NOT NULL) and (a.amount1 - COALESCE(bay3.bayar_before,0)) > 0 GROUP BY a.no_invoice')";

    // Inject rate sesuai type ke sqlMain export
    $sqlMain = str_replace(
        "(select IF((select id from tbl_tgl_tb where tgl_akhir = CURRENT_DATE()) != '''',(select rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = ''HARIAN''),(select rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = ''PAJAK'')) rate) rt",
        "(SELECT " . $rate_inv . " rate) rt",
        $sqlMain
    );
    $sqlMain = str_replace(
        "(select IF((select id from tbl_tgl_tb where tgl_akhir = CURRENT_DATE()) != '''',(select rate from ap_masterrate where tanggal = CURRENT_DATE() and v_codecurr = ''HARIAN''),(select rate from ap_masterrate where tanggal = CURRENT_DATE() and v_codecurr = ''HARIAN'')) rate) rate)",
        "(SELECT " . $rate_dn . " rate) rate)",
        $sqlMain
    );

    // Ganti acuan CURRENT_DATE() pada query invoice (sj_date/tgl_dn) dan
    // alokasi/pembayaran (tgl_alk) supaya data yang ditarik mengikuti periode filter.
    $sqlMain = $this->_apply_end_date_filter($sqlMain, $start, $end, $type);

    $this->db->query($sqlMain);

    $getSql = $this->db->query("SELECT @sql AS sqlku")->row_array();
    $sqlFix = $getSql['sqlku'];
    $query  = $this->db->query($sqlFix);

    return $this->_attach_receivable_breakdown($query->result_array());
}

/* ======================= AKHIR SALINAN DARI ar_dev ======================= */

}

}
