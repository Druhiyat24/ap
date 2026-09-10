<?php
// ============================================================================
// Isi modal DETAIL Request Debit Note (dipanggil dari request_debitnote.php).
// Mengembalikan potongan HTML tabel item beserta Grand Total.
//
// Dirapikan mengikuti skin modal (.app-mod-tbl di app-skin-form.css):
//  - Sebelumnya '</tbody>' ditulis DI DALAM perulangan, jadi tag penutup itu
//    tercetak berulang tiap baris (HTML tidak sah).
//  - Grand Total dulu tabel terpisah tanpa gaya; kini jadi <tfoot> tabel yang
//    sama sehingga kolomnya lurus.
//  - Nilai di-escape (dulu dicetak mentah).
// ============================================================================
include '../../conn/conn.php';

$esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$no_req = isset($_POST['no_req']) ? $_POST['no_req'] : null;

$sql = mysqli_query($conn2, "select no_po,coalesce(no_bpb,'-') no_bpb,item,qty,price,(qty * price) total,attn,seasons,no_reff
    from req_dn where no_req = '" . mysqli_real_escape_string($conn2, $no_req) . "'");

$rows = [];
$tot_inv = 0;
$tot_qty = 0;
while ($sql && ($r = mysqli_fetch_assoc($sql))) {
    $tot_inv += (float) $r['total'];
    $tot_qty += (float) $r['qty'];
    $rows[] = $r;
}

if (!$rows) {
    echo '<div class="app-empty" style="padding:30px 0;"><i class="fa fa-inbox"></i>No item found for this request.</div>';
    exit;
}

echo '<div class="app-mod-sec">Items <span class="ftot-sub">' . count($rows) . ' rows</span></div>';
// .h6 = tinggi dikunci +- 6 baris; sisanya discroll DI DALAM tabel supaya
// modalnya tidak memanjang mengikuti jumlah item.
// Lebar tiap kolom ditetapkan eksplisit + table-layout:fixed supaya proporsinya
// tetap: kolom Item & Reff yang isinya panjang dapat jatah lebar, kolom angka &
// kode tidak melar. Tanpa ini kolom Item terjepit jadi 1-2 kata per baris.
echo '<div class="app-mod-scroll h6">'
   . '<table class="app-mod-tbl fixed-cols" style="min-width:1180px;"><thead><tr>'
   . '<th style="width:155px;">No PO</th>'
   . '<th style="width:140px;">No BPB</th>'
   . '<th style="width:225px;">Item</th>'
   . '<th class="num" style="width:75px;">Qty</th>'
   . '<th class="num" style="width:85px;">Price</th>'
   . '<th class="num" style="width:105px;">Total</th>'
   . '<th style="width:90px;">Attn</th>'
   . '<th style="width:150px;">Seasons</th>'
   . '<th style="width:155px;">Reff</th>'
   . '</tr></thead><tbody>';

foreach ($rows as $r) {
    echo '<tr>'
       // No PO: beberapa data isinya gabungan beberapa nomor PO dipisah koma
       // (bisa >35 karakter) - dibungkus (bukan nowrap+ellipsis) supaya nomornya
       // selalu tampil utuh, tidak terpotong "...".
       . '<td style="white-space:normal;word-break:break-word;">' . $esc($r['no_po']) . '</td>'
       . '<td class="nowrap">' . $esc($r['no_bpb']) . '</td>'
       . '<td style="white-space:normal;">' . $esc($r['item']) . '</td>'
       . '<td class="num">' . number_format((float) $r['qty'], 2) . '</td>'
       . '<td class="num">' . number_format((float) $r['price'], 4) . '</td>'
       . '<td class="num">' . number_format((float) $r['total'], 2) . '</td>'
       . '<td style="white-space:normal;word-break:break-word;">' . $esc($r['attn']) . '</td>'
       . '<td style="white-space:normal;word-break:break-word;">' . $esc($r['seasons']) . '</td>'
       . '<td style="white-space:normal;word-break:break-word;">' . $esc($r['no_reff']) . '</td>'
       . '</tr>';
}

// Angka total ditaruh TEPAT di bawah kolomnya masing-masing (Qty & Total),
// bukan digabung di satu sel.
echo '</tbody><tfoot><tr>'
   . '<th colspan="3">GRAND TOTAL</th>'
   . '<th class="num">' . number_format($tot_qty, 2) . '</th>'
   . '<th></th>'
   . '<th class="num">' . number_format($tot_inv, 2) . '</th>'
   . '<th colspan="3"></th>'
   . '</tr></tfoot></table></div>';
