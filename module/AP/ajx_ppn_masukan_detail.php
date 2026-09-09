<?php
// ============================================================================
// Detail satu DOKUMEN pada report SUB LEDGER - PPN MASUKAN.
// Dipanggil saat user meng-klik salah satu "Doc No" di kolom Deduction
// (kolom itu bisa berisi lebih dari satu nomor, di-concat koma).
//
// Yang ditampilkan: SEMUA baris jurnal dokumen tsb yang memakai NOMOR FAKTUR
// baris yang diklik — jadi terlihat pasangannya (mis. 2.52.03 debit vs 1.52.04
// credit), bukan seluruh isi GM yang bisa ribuan baris.
// Bonus: data mentah faktur dari tbl_ppn_masukan_upload kalau dokumennya memang
// hasil upload tab PPN Masukan.
//
// Input (GET/POST): no_journal, faktur
// Output JSON: { status, head:{...}, upload:{...}|null, lines:[...], total:{...} }
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

function ppn_det_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); exit; }

$no_journal = trim($_REQUEST['no_journal'] ?? '');
$faktur     = trim($_REQUEST['faktur'] ?? '');
if ($no_journal === '') { ppn_det_out(['status' => 'error', 'message' => 'Document number is empty.']); }

$eDoc = mysqli_real_escape_string($conn2, $no_journal);
$eFak = mysqli_real_escape_string($conn2, $faktur);

$whereFak = ($faktur !== '') ? " and coalesce(faktur_pajak, '') = '$eFak'" : '';
$q = mysqli_query($conn2, "select no_journal, tgl_journal, type_journal, no_coa, nama_coa,
        no_costcenter, nama_costcenter, reff_doc, reff_date, coalesce(faktur_pajak,'') faktur_pajak,
        tgl_faktur_pajak, curr, rate, debit, credit, debit_idr, credit_idr,
        coalesce(keterangan,'') keterangan, coalesce(supplier,'') supplier,
        coalesce(profit_center,'') profit_center, coalesce(status,'') status
    from tbl_list_journal
    where no_journal = '$eDoc' $whereFak
    order by no_coa, id");
if ($q === false) { ppn_det_out(['status' => 'error', 'message' => mysqli_error($conn2)]); }

$lines = [];
$tot = ['debit' => 0, 'credit' => 0, 'debit_idr' => 0, 'credit_idr' => 0];
$head = null;
while ($r = mysqli_fetch_assoc($q)) {
    if ($head === null) {
        $head = [
            'no_journal'    => $r['no_journal'],
            'tgl_journal'   => $r['tgl_journal'],
            'type_journal'  => $r['type_journal'],
            'faktur_pajak'  => $r['faktur_pajak'],
            'supplier'      => $r['supplier'],
            'profit_center' => $r['profit_center'],
            'status'        => $r['status'],
            'keterangan'    => $r['keterangan'],
        ];
    }
    $tot['debit']      += (float) $r['debit'];
    $tot['credit']     += (float) $r['credit'];
    $tot['debit_idr']  += (float) $r['debit_idr'];
    $tot['credit_idr'] += (float) $r['credit_idr'];
    $lines[] = $r;
}
if (!$lines) { ppn_det_out(['status' => 'error', 'message' => 'Journal detail not found for this document / invoice number.']); }

// Data mentah faktur (kalau dokumen ini hasil upload tab PPN Masukan)
$upload = null;
if ($faktur !== '') {
    $qu = mysqli_query($conn2, "select bulan, jenis, nama_penjual, npwp, no_faktur, tgl_faktur,
            dpp, dpp_nilai_lain, ppn, ppnbm, coalesce(faktur_diganti,'') faktur_diganti
        from tbl_ppn_masukan_upload
        where no_mj = '$eDoc' and no_faktur = '$eFak' limit 1");
    if ($qu) { $upload = mysqli_fetch_assoc($qu) ?: null; }
}

ppn_det_out(['status' => 'success', 'head' => $head, 'upload' => $upload, 'lines' => $lines, 'total' => $tot]);
