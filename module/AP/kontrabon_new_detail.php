<?php
// Detail 1 Kontrabon (header + invoice -> faktur -> BPB) untuk tombol "Show" di
// List Kontrabon. Balikan HTML untuk di-inject ke modal-body.
include '../../conn/conn.php';
header('Content-Type: text/html; charset=utf-8');

$doc = trim($_POST['doc_number'] ?? '');
if ($doc === '') { echo '<div class="text-danger p-3">Document number is empty.</div>'; exit; }

$e     = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES); };
$fmt   = function ($d) { return (!empty($d) && $d !== '0000-00-00') ? date('d-M-Y', strtotime($d)) : '-'; };
$fmtdt = function ($d) { return !empty($d) ? date('d-M-Y H:i', strtotime($d)) : '-'; };
$money = function ($v) { return number_format((float) $v, 2); };

$de = mysqli_real_escape_string($conn2, $doc);
$h  = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT * FROM ir_kontrabon_h WHERE doc_number = '$de' LIMIT 1"));
if (!$h) { echo '<div class="text-danger p-3">Invoice Received not found.</div>'; exit; }
$unik = mysqli_real_escape_string($conn2, $h['unik_code']);

// ===== Header info =====
// Status = MIRROR dari sisi Invoice Received (ir_invoice_supp_h). Kalau kontrabon sudah
// Cancel tampil Cancel; selain itu ikut status IR (Received / Post .. / Accepted ..).
$irq   = mysqli_query($conn2, "SELECT status FROM ir_invoice_supp_h WHERE doc_number = '$de' LIMIT 1");
$irSt  = ($irq && ($irr = mysqli_fetch_assoc($irq))) ? ($irr['status'] ?? '') : '';
$eSt   = (($h['status'] ?? '') === 'Cancel') ? 'Cancel' : ($irSt !== '' ? $irSt : ($h['status'] ?? 'Draft'));
$lcSt  = strtolower($eSt);
$stCls = ($lcSt === 'cancel') ? 'cancel' : (($lcSt === 'draft') ? 'draft' : (($lcSt === 'received') ? 'received' : 'process'));

// Amount Added in PV = NET nilai yg ditambahkan saat create PV (+ BPB tambahan, - RO).
// Kolom terpisah; total_amount asli tidak diubah. Grand Total = total_amount + add.
$addpv = (float) ($h['amount_add_pv'] ?? 0);
$grand = (float) $h['total_amount'] + $addpv;
$addCol = $addpv > 0 ? '#059669' : ($addpv < 0 ? '#dc2626' : '#64748b');
$addTxt = ($addpv >= 0 ? '+ ' : '- ') . number_format(abs($addpv), 2);
echo '<div class="kbd-info">'
    . '<div><span class="lbl">Document Number</span><span class="val">' . $e($h['doc_number']) . '</span></div>'
    . '<div><span class="lbl">Actual Document Receiving Date</span><span class="val">' . $fmt($h['document_date']) . '</span></div>'
    . '<div><span class="lbl">Invoice Received Date</span><span class="val">' . $fmt($h['kontrabon_date']) . '</span></div>'
    . '<div><span class="lbl">No Reff</span><span class="val">' . $e($h['no_reff']) . '</span></div>'
    . '<div><span class="lbl">Supplier</span><span class="val">' . $e($h['nama_supp']) . '</span></div>'
    . '<div><span class="lbl">Status</span><span class="val"><span class="kb-badge ' . $stCls . '">' . $e($eSt) . '</span></span></div>'
    . '<div><span class="lbl">Total Amount</span><span class="val">' . $money($h['total_amount']) . '</span></div>'
    . '<div><span class="lbl">Amount Added in PV</span><span class="val" style="color:' . $addCol . ';font-weight:600;">' . $addTxt . '</span></div>'
    . '<div><span class="lbl">Grand Total</span><span class="val" style="font-weight:700;">' . $money($grand) . '</span></div>'
    . '<div><span class="lbl">Created By</span><span class="val">' . $e($h['create_user']) . '</span></div>'
    . '<div><span class="lbl">Created Date</span><span class="val">' . $fmtdt($h['create_date']) . '</span></div>'
    . '<div class="kbd-desc-row"><span class="lbl">Description</span><span class="dval">' . (!empty($h['deskripsi']) ? $e($h['deskripsi']) : '-') . '</span></div>'
    . '</div>';

// ===== Invoices -> Faktur -> BPB =====
$invs = mysqli_query($conn2, "SELECT id, no_inv, tgl_inv, amount FROM ir_kontrabon_inv WHERE unik_code = '$unik' ORDER BY id");
if (!$invs || mysqli_num_rows($invs) === 0) { echo '<div class="text-muted p-2">No invoice.</div>'; mysqli_close($conn2); exit; }

while ($iv = mysqli_fetch_assoc($invs)) {
    // Nilai BPB yang DITAMBAHKAN saat create PV untuk invoice ini (keterangan "Added ...").
    // Amount invoice asli (ir_kontrabon_inv.amount) tidak diubah; ini tampil terpisah.
    $ivAddRow = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT COALESCE(SUM(total),0) s FROM ir_kontrabon_bpb WHERE inv_id = " . (int) $iv['id'] . " AND keterangan LIKE 'Added in Payment Voucher%'"));
    $ivAdd   = (float) ($ivAddRow['s'] ?? 0);
    $ivGrand = (float) $iv['amount'] + $ivAdd;
    echo '<div class="kbd-inv"><div class="kbd-inv-head">'
        . '<span class="kbd-inv-title"><i class="fa fa-file-text-o"></i> Invoice: <b>' . $e($iv['no_inv']) . '</b></span>'
        . '<span class="kbd-chip">Date: ' . $fmt($iv['tgl_inv']) . '</span>'
        . '<span class="kbd-chip">Amount: ' . $money($iv['amount']) . '</span>'
        . ($ivAdd != 0
            ? '<span class="kbd-chip" style="background:#ecfdf5;color:#059669;border-color:#a7f3d0;">Added in PV: +' . $money($ivAdd) . '</span>'
              . '<span class="kbd-chip" style="font-weight:700;">Grand Total: ' . $money($ivGrand) . '</span>'
            : '')
        . '</div>';

    $faks = mysqli_query($conn2, "SELECT * FROM ir_kontrabon_faktur WHERE inv_id = " . (int) $iv['id'] . " ORDER BY id");
    if ($faks && mysqli_num_rows($faks) > 0) {
        while ($fk = mysqli_fetch_assoc($faks)) {
            $stf = strtoupper($fk['status_faktur'] ?? '');
            $stfCls = $stf === 'APPROVED' ? 'badge-success' : ($stf ? 'badge-secondary' : '');
            echo '<div class="kbd-fak">'
                . '<div class="kbd-fak-head"><i class="fa fa-file-text-o"></i> Faktur: <b>' . $e($fk['no_faktur']) . '</b>'
                . ($stf ? ' <span class="badge ' . $stfCls . '">' . $e($stf) . '</span>' : '')
                . (!empty($fk['keterangan']) ? '<br><span style="font-size:11px; color:#64748b; font-weight:normal;">' . $e($fk['keterangan']) . '</span>' : '') . '</div>'
                . '<div class="faktur-meta">'
                . '<div><span class="fm-lbl">Supplier</span><span class="fm-val">' . ($fk['nama_supplier'] ? $e($fk['nama_supplier']) : '-') . '</span></div>'
                . '<div><span class="fm-lbl">NPWP Supplier</span><span class="fm-val">' . ($fk['npwp_supplier'] ? $e($fk['npwp_supplier']) : '-') . '</span></div>'
                . '<div><span class="fm-lbl">Tgl Faktur</span><span class="fm-val">' . $fmt($fk['tgl_faktur']) . '</span></div>'
                . '<div><span class="fm-lbl">DPP</span><span class="fm-val">' . $money($fk['dpp']) . '</span></div>'
                . '<div><span class="fm-lbl">PPN</span><span class="fm-val">' . $money($fk['ppn']) . '</span></div>'
                . '</div>';

            $bpbs = mysqli_query($conn2, "SELECT * FROM ir_kontrabon_bpb WHERE faktur_id = " . (int) $fk['id'] . " ORDER BY id");
            if ($bpbs && mysqli_num_rows($bpbs) > 0) {
                echo '<div style="overflow-x:auto;"><table class="kbd-bpb-table"><thead><tr>'
                    . '<th>#</th><th>No BPB</th><th>Tgl BPB</th><th>No PO</th><th>Supplier</th>'
                    . '<th class="text-right">DPP</th><th class="text-right">PPN</th><th class="text-right">Total</th>'
                    . '</tr></thead><tbody>';
                $bi = 0;
                while ($bp = mysqli_fetch_assoc($bpbs)) {
                    $bi++;
                    echo '<tr><td class="text-center">' . $bi . '</td>'
                        . '<td>' . $e($bp['no_bpb'])
                        . (!empty($bp['keterangan']) ? '<br><span style="font-size:11px; color:#64748b;">' . $e($bp['keterangan']) . '</span>' : '') . '</td>'
                        . '<td class="text-center">' . $fmt($bp['tgl_bpb']) . '</td>'
                        . '<td>' . ($bp['no_po'] ? $e($bp['no_po']) : '-') . '</td>'
                        . '<td>' . ($bp['supplier'] ? $e($bp['supplier']) : '-') . '</td>'
                        . '<td class="text-right">' . $money($bp['dpp']) . '</td>'
                        . '<td class="text-right">' . $money($bp['ppn']) . '</td>'
                        . '<td class="text-right">' . $money($bp['total']) . ' ' . $e($bp['curr']) . '</td></tr>';
                }
                echo '</tbody></table></div>';
            } else {
                echo '<div class="text-muted" style="font-size:12px; font-style:italic;">No BPB.</div>';
            }
            echo '</div>'; // kbd-fak
        }
    } else {
        echo '<div class="text-muted" style="font-size:12px; font-style:italic; padding:4px 2px;">No faktur.</div>';
    }
    echo '</div>'; // kbd-inv
}
mysqli_close($conn2);
