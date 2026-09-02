<?php
// Detail 1 BPB (dari bpb_new) untuk tombol "Show" di Create Kontrabon.
// Balikan HTML (untuk di-inject ke modal-body). Header + baris item + total.
include '../../conn/conn.php';
header('Content-Type: text/html; charset=utf-8');

$no_bpb   = trim($_POST['no_bpb'] ?? '');
$supplier = trim($_POST['supplier'] ?? '');
if ($no_bpb === '') { echo '<div class="text-danger p-3">BPB is empty.</div>'; exit; }

$where = "no_bpb = '" . mysqli_real_escape_string($conn1, $no_bpb) . "'";
if ($supplier !== '') $where .= " AND supplier = '" . mysqli_real_escape_string($conn1, $supplier) . "'";

$q = mysqli_query($conn1, "SELECT ws, itemdesc, color, size, qty, uom, price, tax, curr,
    tgl_bpb, tgl_po, supplier, pono, top, confirm1, confirm2
    FROM bpb_new WHERE $where ORDER BY id");
if (!$q || mysqli_num_rows($q) === 0) { echo '<div class="text-danger p-3">BPB not found.</div>'; exit; }

$rows = [];
while ($r = mysqli_fetch_assoc($q)) $rows[] = $r;
$h = $rows[0];

$fmt = function ($d) { return (!empty($d) && $d !== '0000-00-00') ? date('d-M-Y', strtotime($d)) : '-'; };
$esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES); };
$ponos = array_values(array_unique(array_filter(array_map(function ($r) { return $r['pono']; }, $rows))));
$noPo  = $ponos ? implode(', ', $ponos) : '-';

$sub = 0; $tax = 0; $lines = '';
foreach ($rows as $r) {
    $qty = (float) $r['qty']; $price = (float) $r['price'];
    $line = $qty * $price; $sub += $line; $tax += $line * ((float) $r['tax'] / 100);
    $prc = number_format($price, 2);
    $lines .= '<tr>'
        . '<td>' . $esc($r['ws']) . '</td>'
        . '<td>' . $esc($r['itemdesc'] . ' (' . $r['color'] . ' ' . $r['size'] . ')') . '</td>'
        . '<td class="text-right">' . number_format($qty, 2) . '</td>'
        . '<td class="text-center">' . $esc($r['uom']) . '</td>'
        . '<td class="text-right">' . $prc . '</td>'
        . '<td class="text-right">' . number_format($line, 2) . '</td>'
        . '</tr>';
}
$total = $sub + $tax;
$curr  = $esc($h['curr'] ?: 'IDR');
$top   = ($h['top'] !== null && $h['top'] !== '') ? $esc($h['top']) . ' Days' : '-';

echo '<div class="bpb-info">'
    . '<div><span class="lbl">Tgl BPB</span><span class="val">' . $fmt($h['tgl_bpb']) . '</span></div>'
    . '<div><span class="lbl">No PO</span><span class="val">' . $esc($noPo) . '</span></div>'
    . '<div><span class="lbl">Supplier</span><span class="val">' . $esc($h['supplier']) . '</span></div>'
    . '<div><span class="lbl">TOP</span><span class="val">' . $top . '</span></div>'
    . '<div><span class="lbl">Currency</span><span class="val">' . $curr . '</span></div>'
    . '<div><span class="lbl">Confirm By (GMF)</span><span class="val">' . $esc($h['confirm1'] ?: '-') . '</span></div>'
    . '<div><span class="lbl">Confirm By (PCH)</span><span class="val">' . $esc($h['confirm2'] ?: '-') . '</span></div>'
    . '<div><span class="lbl">Tgl PO</span><span class="val">' . $fmt($h['tgl_po']) . '</span></div>'
    . '</div>';

echo '<div style="overflow-x:auto;"><table class="bpb-detail-table">'
    . '<thead><tr>'
    . '<th>WS #</th><th style="text-align:left;">Material</th>'
    . '<th class="text-right">Qty</th><th>UOM</th>'
    . '<th class="text-right">Price</th><th class="text-right">SubTotal (' . $curr . ')</th>'
    . '</tr></thead><tbody>' . $lines . '</tbody></table></div>';

echo '<div class="bpb-totals">'
    . '<div class="row-t"><span>Subtotal</span><span>' . number_format($sub, 2) . '</span></div>'
    . '<div class="row-t"><span>Tax (PPn)</span><span>' . number_format($tax, 2) . '</span></div>'
    . '<div class="row-t total"><span>Total</span><span class="amt">' . number_format($total, 2) . '</span></div>'
    . '</div>';
mysqli_close($conn1);
