<?php
// ============================================================================
// PREVIEW READ-ONLY (DRY-RUN) selisih kurs: bandingkan selisih LAMA (net x PAJAK)
// vs BARU (pakai IDR jurnal / rate manual) per tanggal per akun bank USD.
// TIDAK MENULIS APA PUN ke database — murni SELECT untuk review sebelum scheduler
// dijalankan. Params: ?acc=<bank_account> (default semua USD) & ?end=YYYY-MM-DD.
// ============================================================================
include '../../conn/conn.php';
$conn = $conn2;
$MIN = '2026-08-01';                              // = FXSK_MIN_DATE
$end = !empty($_GET['end']) ? date('Y-m-d', strtotime($_GET['end'])) : date('Y-m-d', strtotime('yesterday'));
$accFilter = $_GET['acc'] ?? '';

function rateOnOrBefore($conn, $codecurr, $date) {
    $de = mysqli_real_escape_string($conn, $date);
    $r = mysqli_fetch_assoc(mysqli_query($conn, "select rate from masterrate where v_codecurr='$codecurr' and curr='USD' and tanggal<='$de' order by tanggal desc limit 1"));
    return $r ? (float) $r['rate'] : 1.0;
}

// Daftar akun USD (atau 1 akun kalau ?acc diisi)
$accWhere = "status='Active' and curr='USD'";
if ($accFilter !== '') $accWhere = "bank_account='" . mysqli_real_escape_string($conn, $accFilter) . "'";
$accts = [];
$ra = mysqli_query($conn, "select bank_account, bank_name, id_coa from b_masterbank where $accWhere order by bank_account");
while ($x = mysqli_fetch_assoc($ra)) $accts[] = $x;

echo "<!doctype html><meta charset='utf-8'><style>
body{font-family:Segoe UI,Arial,sans-serif;font-size:13px;margin:18px;color:#1f2937}
h2{color:#1e3a8a} h3{margin:18px 0 6px}
table{border-collapse:collapse;width:100%;margin-bottom:8px} th,td{border:1px solid #e5e7eb;padding:5px 8px;text-align:right;white-space:nowrap}
th{background:#1e3a8a;color:#fff} td.l{text-align:left}
tr.diff{background:#fef2f2} tr.diff td{font-weight:600}
.tag{background:#fee2e2;color:#b91c1c;border-radius:4px;padding:1px 6px;font-size:11px}
.muted{color:#6b7280}
</style>";
echo "<h2>Preview Selisih Kurs — LAMA (PAJAK) vs BARU (rate jurnal)</h2>";
echo "<p class='muted'>Read-only, tidak menulis DB. Periode: <b>$MIN</b> s.d. <b>$end</b>. Baris merah = nilai berubah setelah fix.</p>";

function fmt($n){ return number_format((float)$n, 2); }

foreach ($accts as $acc) {
    $account = $acc['bank_account'];
    $accEsc = mysqli_real_escape_string($conn, $account);
    $bankCoa = $acc['id_coa'];

    // Map IDR jurnal per no_doc (baris bank)
    $jrnIdr = [];
    if ($bankCoa !== '') {
        $bcE = mysqli_real_escape_string($conn, $bankCoa);
        $rj = mysqli_query($conn, "select no_journal, debit_idr, credit_idr from tbl_list_journal where no_coa='$bcE' and status!='Cancel'");
        while ($x = mysqli_fetch_assoc($rj)) $jrnIdr[$x['no_journal']] = (float)$x['debit_idr'] - (float)$x['credit_idr'];
    }

    // Saldo awal + transaksi (non-FX)
    $rb = mysqli_fetch_assoc(mysqli_query($conn, "select amount from b_saldoawal_bank where account='$accEsc'"));
    $runningNative = $rb ? (float)$rb['amount'] : 0.0;
    $txByDate = [];
    $rt = mysqli_query($conn, "select transaksi_date, no_doc, debit, credit from b_reportbank where akun='$accEsc' and status!='Cancel' and no_doc not like 'FX/%' and transaksi_date<='" . mysqli_real_escape_string($conn,$end) . "' order by transaksi_date asc, id asc");
    while ($x = mysqli_fetch_assoc($rt)) $txByDate[$x['transaksi_date']][] = $x;

    // Native sebelum MIN numpuk dulu
    foreach ($txByDate as $date => $txs) { if ($date >= $MIN) break; foreach ($txs as $t) $runningNative += (float)$t['debit'] - (float)$t['credit']; }
    $seedRate = rateOnOrBefore($conn, 'HARIAN', date('Y-m-d', strtotime($MIN . ' -1 day')));
    $openingIdr = $runningNative * $seedRate;

    echo "<h3>" . htmlspecialchars($account . ' — ' . $acc['bank_name']) . " <span class='muted'>(COA $bankCoa)</span></h3>";
    echo "<table><tr><th class='l'>Tanggal</th><th>Saldo USD</th><th>Kurs PAJAK</th><th>Kurs HARIAN</th><th>Selisih LAMA</th><th>Selisih BARU</th><th>Beda</th><th class='l'>Transaksi rate-manual hari itu</th></tr>";

    $cursor = strtotime($MIN); $endTs = strtotime($end);
    $totOld = 0; $totNew = 0; $nDiff = 0;
    while ($cursor <= $endTs) {
        $d = date('Y-m-d', $cursor); $cursor = strtotime('+1 day', $cursor);
        $pajak = rateOnOrBefore($conn, 'PAJAK', $d);
        $bookOld = $openingIdr; $bookNew = $openingIdr; $manualDocs = [];
        if (isset($txByDate[$d])) {
            foreach ($txByDate[$d] as $t) {
                $net = (float)$t['debit'] - (float)$t['credit'];
                $runningNative += $net;
                $bookOld += $net * $pajak;
                if (isset($jrnIdr[$t['no_doc']])) {
                    $bookNew += $jrnIdr[$t['no_doc']];
                    $impliedRate = $net != 0 ? $jrnIdr[$t['no_doc']] / $net : 0;
                    if (abs(round($impliedRate,4) - round($pajak,4)) > 0.01) $manualDocs[] = $t['no_doc'] . ' (rate ' . number_format($impliedRate,2) . ' vs PAJAK ' . number_format($pajak,2) . ')';
                } else {
                    $bookNew += $net * $pajak;
                }
            }
        }
        $harian = rateOnOrBefore($conn, 'HARIAN', $d);
        $market = $runningNative * $harian;
        $selOld = round($market - $bookOld, 2);
        $selNew = round($market - $bookNew, 2);
        $openingIdr = $market; // crystallize (sama utk lama/baru)
        $beda = round($selNew - $selOld, 2);
        if ($selOld == 0 && $selNew == 0) continue; // tak dijurnal (skip tampil)
        if ($beda != 0) $nDiff++;
        $totOld += $selOld; $totNew += $selNew;
        $cls = $beda != 0 ? " class='diff'" : "";
        echo "<tr$cls><td class='l'>$d</td><td>" . fmt($runningNative) . "</td><td>" . fmt($pajak) . "</td><td>" . fmt($harian) . "</td><td>" . fmt($selOld) . "</td><td>" . fmt($selNew) . "</td><td>" . ($beda!=0?"<span class='tag'>":"") . fmt($beda) . ($beda!=0?"</span>":"") . "</td><td class='l muted'>" . htmlspecialchars(implode('; ', $manualDocs)) . "</td></tr>";
    }
    echo "<tr style='background:#f3f4f6;font-weight:700'><td class='l'>TOTAL (dijurnalkan)</td><td colspan=3></td><td>" . fmt($totOld) . "</td><td>" . fmt($totNew) . "</td><td>" . fmt($totNew-$totOld) . "</td><td class='l'>$nDiff tanggal berubah</td></tr>";
    echo "</table>";
}
echo "<p class='muted'>Catatan: 'Selisih' = saldo pasar (HARIAN) − saldo buku. Selisih>0 = untung (COA bank debit), &lt;0 = rugi. Angka ini yang akan jadi jurnal FX kalau scheduler dijalankan.</p>";
