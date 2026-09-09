<?php
// ============================================================================
// Detail SATU nomor jurnal (SEMUA baris/COA yg dipakai dokumen itu) — dipakai
// popup di general_ledger.php saat "No Journal" diklik. Pola sama dgn popup
// detail dokumen di ppn_masukan_report.php (ajx_ppn_masukan_detail.php), tapi
// di sini TANPA filter faktur/upload — General Ledger cuma perlu menampilkan
// seluruh isi jurnalnya apa adanya.
//
// Input (GET/POST): no_journal
// Output JSON: { status, head:{...}, lines:[...], total:{...} }
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

function gld_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); exit; }

$no_journal = trim($_REQUEST['no_journal'] ?? '');
if ($no_journal === '') { gld_out(['status' => 'error', 'message' => 'Document number is empty.']); }

$eDoc = mysqli_real_escape_string($conn2, $no_journal);

$q = mysqli_query($conn2, "select no_journal, tgl_journal, type_journal, no_coa, nama_coa,
        no_costcenter, nama_costcenter, reff_doc, reff_date, coalesce(faktur_pajak,'') faktur_pajak,
        curr, rate, debit, credit, debit_idr, credit_idr,
        coalesce(keterangan,'') keterangan, coalesce(supplier,'') supplier,
        coalesce(profit_center,'') profit_center, coalesce(status,'') status
    from tbl_list_journal
    where no_journal = '$eDoc'
    order by no_coa, id");
if ($q === false) { gld_out(['status' => 'error', 'message' => mysqli_error($conn2)]); }

$lines = [];
$tot   = ['debit' => 0, 'credit' => 0, 'debit_idr' => 0, 'credit_idr' => 0];
$head  = null;
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
if (!$lines) { gld_out(['status' => 'error', 'message' => 'Journal detail not found for this document number.']); }

gld_out(['status' => 'success', 'head' => $head, 'lines' => $lines, 'total' => $tot]);
