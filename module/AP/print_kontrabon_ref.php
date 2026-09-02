<?php
// ============================================================================
// PRINT lembar Kontra Bon (kertas cetak sendiri). Nomor KB/NAG/<tahun>/NNNNN
// di-generate di kontrabon_new_ref.php. Halaman ini mencetak rentang nomor
// (from..to) yang SUDAH ada di ir_kontrabon_ref -> 3 lembar per A4 portrait.
// QR = No KB (buat scan No Reff saat buat Kontrabon). Tanggal KB = tanggal print.
// ============================================================================
include '../../conn/conn.php';

$year = preg_replace('/[^0-9]/', '', $_GET['year'] ?? date('Y'));
if ($year === '') $year = date('Y');
$from = (int) ($_GET['from'] ?? 0);
$to   = (int) ($_GET['to'] ?? 0);
if ($from < 1) $from = 1;
if ($to < $from) $to = $from;
if ($to - $from > 1000) $to = $from + 1000; // batas aman

$prefix     = "KB/NAG/$year/";
$prefix_esc = mysqli_real_escape_string($conn2, $prefix);
$q = mysqli_query($conn2, "SELECT ref_number FROM ir_kontrabon_ref
    WHERE ref_number LIKE '$prefix_esc%'
      AND CAST(SUBSTRING_INDEX(ref_number, '/', -1) AS UNSIGNED) BETWEEN $from AND $to
    ORDER BY CAST(SUBSTRING_INDEX(ref_number, '/', -1) AS UNSIGNED)");
$rows = [];
while ($q && ($r = mysqli_fetch_assoc($q))) $rows[] = $r['ref_number'];

$bln = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglPrint = (int) date('j') . ' ' . $bln[(int) date('n')] . ' ' . date('Y');
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES); };
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Print Kontra Bon <?= $e($prefix) ?></title>
<style>
  :root{ --navy:#16215c; }
  *{ box-sizing:border-box; }
  html,body{ margin:0; padding:0; background:#e9edf3; font-family:Arial, Helvetica, sans-serif; color:var(--navy); }
  .toolbar{ position:sticky; top:0; background:#fff; border-bottom:1px solid #d5dbe6; padding:10px 16px; display:flex; gap:10px; align-items:center; z-index:5; }
  .toolbar b{ color:#334155; font-size:14px; }
  .toolbar .sp{ flex:1; }
  .btn{ border:0; border-radius:8px; padding:8px 16px; font-size:13px; font-weight:700; cursor:pointer; }
  .btn-print{ background:var(--navy); color:#fff; }
  .btn-back{ background:#e2e8f0; color:#334155; text-decoration:none; }
  .sheet{ width:210mm; margin:14px auto; }
  .empty{ background:#fff; padding:40px; text-align:center; color:#64748b; border-radius:8px; }

  .bon{ background:#fff; border:2px solid var(--navy); border-radius:4px; min-height:88mm; padding:0; margin-bottom:2.5mm;
        page-break-inside:avoid; display:flex; flex-direction:column; }
  .bon:last-child{ margin-bottom:0; }
  /* ---- Header ---- */
  .bon-head{ display:flex; border-bottom:2px solid var(--navy); }
  .bh-logo{ width:34%; padding:2mm 5mm 1.5mm; border-right:2px solid var(--navy); }
  .bh-logo .nag{ font-size:26px; font-weight:900; letter-spacing:1px; line-height:1; }
  .bh-logo .co{ font-size:11px; font-weight:800; margin-top:1px; }
  .bh-logo .addr{ font-size:7.6px; color:#243a7a; line-height:1.25; margin-top:2px; font-weight:600; }
  .bh-title{ flex:1; display:flex; align-items:center; justify-content:center; }
  .bh-title span{ font-size:28px; font-weight:900; letter-spacing:1px; }
  .bh-meta{ width:30%; padding:2.5mm 5mm 2mm; }
  .bh-meta .row{ display:flex; align-items:baseline; margin-bottom:4mm; font-size:11px; }
  .bh-meta .lbl{ font-weight:800; width:74px; white-space:nowrap; }
  .bh-meta .col{ font-weight:800; margin:0 4px; }
  .bh-meta .val{ flex:1; border-bottom:1px solid var(--navy); padding-bottom:1px; font-weight:700; }
  /* ---- Body ---- */
  .bon-body{ flex:1; display:flex; }
  .bb-left{ width:60%; display:flex; align-items:center; gap:4mm; padding:3mm 5mm; border-right:2px solid var(--navy); }
  .bb-qr{ width:20mm; }
  .bb-qr canvas, .bb-qr img{ width:19mm !important; height:19mm !important; }
  .bb-fields{ flex:1; }
  .fld{ margin-bottom:6mm; }
  .fld:last-child{ margin-bottom:0; }
  .fld .k{ font-size:10px; font-weight:800; letter-spacing:.3px; }
  .fld .line{ border-bottom:1px solid var(--navy); height:16px; }
  .fld .line + .line{ margin-top:7px; }
  .fld.total .k{ margin-bottom:2px; }
  .fld.total .rp{ display:flex; align-items:flex-end; gap:4px; }
  .fld.total .rp b{ font-size:16px; }
  .fld.total .line{ flex:1; }
  .bb-right{ flex:1; padding:3mm 5mm; }
  .bb-right .k{ font-size:10px; font-weight:800; letter-spacing:.3px; margin-bottom:2mm; }
  .bb-right .line{ border-bottom:1px solid var(--navy); height:12px; margin-bottom:3mm; }
  /* ---- Footer ---- */
  .bon-foot{ display:flex; border-top:2px solid var(--navy); }
  .bf-ket{ flex:1; padding:3mm 5mm; border-right:2px solid var(--navy); }
  .bf-ket .k{ font-size:10px; font-weight:800; margin-bottom:2mm; }
  .bf-ket .dot{ border-bottom:1px dotted var(--navy); height:11px; margin-bottom:2mm; }
  .bf-sign{ width:26%; padding:2.5mm 5mm 2mm; text-align:center; }
  .bf-sign.mid{ border-right:2px solid var(--navy); }
  .bf-sign .k{ font-size:10px; font-weight:800; }
  .bf-sign .sig{ border-bottom:1px solid var(--navy); height:11mm; margin:2mm 2mm 0; }
  .bf-sign .nm{ display:flex; align-items:baseline; gap:4px; margin-top:2mm; font-size:9.5px; font-weight:700; }
  .bf-sign .nm .line{ flex:1; border-bottom:1px solid var(--navy); }

  @media print{
    html,body{ background:#fff; }
    .toolbar{ display:none; }
    .sheet{ margin:0 auto; width:auto; }
    @page{ size:A4 portrait; margin:8mm; }
  }
</style>
</head>
<body>
  <div class="toolbar">
    <b>Print Kontra Bon &mdash; <?= $e($prefix) ?> (<?= count($rows) ?> lembar)</b>
    <span class="sp"></span>
    <a class="btn btn-back" href="kontrabon_new_ref.php">&larr; Back</a>
    <button class="btn btn-print" onclick="window.print()">🖨 Print</button>
  </div>

  <div class="sheet">
    <?php if (empty($rows)): ?>
      <div class="empty">No generated numbers found in this range (<?= $e($prefix) ?><?= str_pad((string) $from, 5, '0', STR_PAD_LEFT) ?> &ndash; <?= str_pad((string) $to, 5, '0', STR_PAD_LEFT) ?>).</div>
    <?php else: foreach ($rows as $ref): ?>
      <div class="bon">
        <div class="bon-head">
          <div class="bh-logo">
            <div class="nag">NAG</div>
            <div class="co">PT. NIRWANA ALABARE GARMENT</div>
            <div class="addr">Jl. Raya Rancaekek &ndash; Majalaya No. 289,<br>Solokan Jeruk &ndash; Majalaya, West Java &ndash; Indonesia<br>Telp. +62 22 85962076 &nbsp;|&nbsp; info@ptnag.com</div>
          </div>
          <div class="bh-title"><span>KONTRA BON</span></div>
          <div class="bh-meta">
            <div class="row"><span class="lbl">No KB</span><span class="col">:</span><span class="val"><?= $e($ref) ?></span></div>
            <div class="row"><span class="lbl">Tanggal KB</span><span class="col">:</span><span class="val"><?= $e($tglPrint) ?></span></div>
          </div>
        </div>
        <div class="bon-body">
          <div class="bb-left">
            <div class="bb-qr" data-qr="<?= $e($ref) ?>"></div>
            <div class="bb-fields">
              <div class="fld"><div class="k">SUPPLIER</div><div class="line"></div><div class="line"></div></div>
              <div class="fld total"><div class="k">TOTAL</div><div class="rp"><b>Rp</b><div class="line"></div></div></div>
            </div>
          </div>
          <div class="bb-right">
            <div class="k">NOMOR TAGIHAN</div>
            <div class="line"></div><div class="line"></div><div class="line"></div><div class="line"></div>
          </div>
        </div>
        <div class="bon-foot">
          <div class="bf-ket">
            <div class="k">KETERANGAN</div>
            <div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="dot"></div>
          </div>
          <div class="bf-sign mid">
            <div class="k">DISERAHKAN OLEH</div>
            <div class="sig"></div>
            <div class="nm">Nama :<span class="line"></span></div>
          </div>
          <div class="bf-sign">
            <div class="k">DITERIMA OLEH</div>
            <div class="sig"></div>
            <div class="nm">Nama :<span class="line"></span></div>
          </div>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>

  <script src="../css/4.1.1/qrcode.min.js"></script>
  <script>
    document.querySelectorAll('[data-qr]').forEach(function (el) {
      new QRCode(el, { text: el.getAttribute('data-qr'), width: 90, height: 90, correctLevel: QRCode.CorrectLevel.M });
    });
  </script>
</body>
</html>
