<!DOCTYPE html>
<?php
session_start();
include '../../conn/conn.php';
$user = $_SESSION['username'];
$pl_number = $_GET['pl_number'];
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('pcre.backtrack_limit', '10000000');
?>

<?php
$pl_number_esc = mysqli_real_escape_string($conn2, $pl_number);

$sqlH = mysqli_query($conn2, "SELECT h.pl_number, h.pl_date, h.deskripsi, h.created_by, h.created_date,
    h.from_account, UPPER(b.beneficiary_name) AS beneficiary_name,
    UPPER(b.curr) AS bank_curr,
    UPPER(b.bank_name) AS bank_name,
    UPPER(b.bank_account) AS bank_account, b.curr AS from_bank_curr
    FROM pv_payment_list_h h
    LEFT JOIN b_masterbank b ON b.bank_account = h.from_account
    WHERE h.pl_number = '$pl_number_esc'");
$rowH = mysqli_fetch_assoc($sqlH);
// $plFromAccountDisplay = !empty($rowH['from_account'])
//     ? $rowH['from_account'] . (!empty($rowH['from_bank_name']) ? ' - ' . $rowH['from_bank_name'] : '')
//     : '-';

    $beneficiary_name = $rowH['beneficiary_name'] ?? '-';
    $bank_curr = $rowH['bank_curr'] ?? '-';
    $bank_name = $rowH['bank_name'] ?? '-';
    $bank_account = $rowH['bank_account'] ?? '-';

// Ambil due date + bank tujuan per PV - sumbernya beda tergantung type_pv:
// Regular/Installment/DP/CBD lewat id_bank_account (sama persis pola
// pdf_pv_regular.php dkk), Biaya lewat tbl_pv_h.to_akun (nomor rekening
// langsung, dicocokkan ke master_supplier_bank.bank_account - lihat
// pdf_payvoucher.php).
function getDueDateAndBank($conn2, $type_pv, $no_kbon)
{
    $no_kbon_esc = mysqli_real_escape_string($conn2, $no_kbon);
    $bank = ['beneficiary_name' => '', 'bank_name' => '', 'bank_account' => '', 'bank_currency' => ''];

    if ($type_pv === 'Biaya') {
        $sql = mysqli_query($conn2, "select max(b.due_date) as due_date, a.to_akun from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv where a.no_pv = '$no_kbon_esc' group by a.no_pv");
        $row = mysqli_fetch_assoc($sql);
        $tgl_tempo = $row['due_date'] ?? null;

        if (!empty($row['to_akun'])) {
            $sqlBank = mysqli_query($conn2, "select * from (select beneficiary_name, bank_name, bank_account, bank_currency from master_supplier_bank where bank_account = '" . mysqli_real_escape_string($conn2, $row['to_akun']) . "'
UNION
select beneficiary_name, upper(bank_name) bank_name, bank_account, curr bank_currency from b_masterbank where bank_account = '" . mysqli_real_escape_string($conn2, $row['to_akun']) . "') t limit 1");
            $rowBank = mysqli_fetch_assoc($sqlBank);
            if ($rowBank) {
                $bank = $rowBank;
            }
        }

        return ['tgl_tempo' => $tgl_tempo, 'bank' => $bank];
    }

    if ($type_pv === 'Installment') {
        $sqlDet = mysqli_query($conn2, "select no_kbon, tgl_tempo from kontrabon_h_installment_detail where no_kbon_det = '$no_kbon_esc'");
        $rowDet = mysqli_fetch_assoc($sqlDet);
        $tgl_tempo = $rowDet['tgl_tempo'] ?? null;
        $parentKbon = $rowDet['no_kbon'] ?? null;

        $idBankAccount = null;
        $sqlBank = mysqli_query($conn2, "select id_bank_account from kontrabon_h where no_kbon = '" . mysqli_real_escape_string($conn2, $parentKbon) . "' and create_date > '2026-06-30'");
        $rowBank = mysqli_fetch_assoc($sqlBank);
        $idBankAccount = $rowBank['id_bank_account'] ?? null;
    } elseif ($type_pv === 'SaldoAwal') {
        // No PV saldo awal (migrasi sebelum go-live) sumbernya bukan
        // kontrabon_h, tapi ap_saldo_payment_voucher - beberapa no_kbon
        // saldo awal kebetulan sama dengan no_kbon lama di kontrabon_h
        // (created < 1 Juli 2026, id_bank_account-nya kosong), jadi jangan
        // sampai salah ambil dari situ.
        $sql = mysqli_query($conn2, "select tgl_tempo, id_bank_supplier from ap_saldo_payment_voucher where no_kbon = '$no_kbon_esc'");
        $row = mysqli_fetch_assoc($sql);
        $tgl_tempo = $row['tgl_tempo'] ?? null;
        $idBankAccount = $row['id_bank_supplier'] ?? null;
    } else {
        $table = $type_pv === 'DP' ? 'kontrabon_h_dp' : ($type_pv === 'CBD' ? 'kontrabon_h_cbd' : 'kontrabon_h');
        $sql = mysqli_query($conn2, "select tgl_tempo, id_bank_account from $table where no_kbon = '$no_kbon_esc' and create_date > '2026-06-30'");
        $row = mysqli_fetch_assoc($sql);
        $tgl_tempo = $row['tgl_tempo'] ?? null;
        $idBankAccount = $row['id_bank_account'] ?? null;
    }

    if (!empty($idBankAccount)) {
        $sqlBankDet = mysqli_query($conn2, "select beneficiary_name, bank_name, bank_account, bank_currency from master_supplier_bank where id = '" . mysqli_real_escape_string($conn2, $idBankAccount) . "'");
        $rowBankDet = mysqli_fetch_assoc($sqlBankDet);
        if ($rowBankDet) {
            $bank = $rowBankDet;
        }
    }

    return ['tgl_tempo' => $tgl_tempo, 'bank' => $bank];
}

// Rekening perusahaan sendiri (From Account) yang dipakai bayar PV ini -
// cuma ada di kontrabon-family (Regular/Installment/DP/CBD), Biaya tidak
// punya field ini sama sekali (form Biaya beda, lihat tbl_pv_h).
function getFromAccountInfo($conn2, $type_pv, $no_kbon)
{
    $no_kbon_esc = mysqli_real_escape_string($conn2, $no_kbon);
    $from = ['from_account' => '', 'from_bank' => '', 'from_bank_curr' => ''];

    if ($type_pv === 'Biaya') {
        $sql = mysqli_query($conn2, "select frm_akun from_account, b.bank_name from_bank, b.curr from_bank_curr from tbl_pv_h a LEFT JOIN b_masterbank b on b.bank_account = a.frm_akun where no_pv = '$no_kbon_esc'");
    }elseif ($type_pv === 'Installment') {
        $sqlDet = mysqli_query($conn2, "select no_kbon from kontrabon_h_installment_detail where no_kbon_det = '$no_kbon_esc'");
        $rowDet = mysqli_fetch_assoc($sqlDet);
        $parentKbon = $rowDet['no_kbon'] ?? null;
        if (empty($parentKbon)) {
            return $from;
        }
        $sql = mysqli_query($conn2, "select from_account, from_bank, from_bank_curr from kontrabon_h where no_kbon = '" . mysqli_real_escape_string($conn2, $parentKbon) . "' and create_date > '2026-06-30'");
    } elseif ($type_pv === 'SaldoAwal') {
        // Sama seperti getDueDateAndBank(): saldo awal harus dari
        // ap_saldo_payment_voucher, bukan kontrabon_h (lihat catatan di sana).
        $sql = mysqli_query($conn2, "select from_account, from_bank, from_bank_curr from ap_saldo_payment_voucher where no_kbon = '$no_kbon_esc'");
    } else {
        $table = $type_pv === 'DP' ? 'kontrabon_h_dp' : ($type_pv === 'CBD' ? 'kontrabon_h_cbd' : 'kontrabon_h');
        $sql = mysqli_query($conn2, "select from_account, from_bank, from_bank_curr from $table where no_kbon = '$no_kbon_esc' and create_date > '2026-06-30'");
    }

    $row = mysqli_fetch_assoc($sql);
    if ($row) {
        $from = $row;
    }

    return $from;
}

// Urut per Supplier dulu supaya PV dari supplier yang sama jatuh berurutan -
// dibutuhkan untuk rowspan Supplier/Total Amount/Bank di tabel PDF.
$sqlDet = mysqli_query($conn2, "select id, type_pv, no_kbon, tgl_kbon, nama_supp, curr, total, deskripsi, profit_center from pv_payment_list_det where pl_number = '$pl_number_esc' and status != 'Cancel' order by no_kbon asc");

$rows = [];
while ($d = mysqli_fetch_assoc($sqlDet)) {
    $extra = getDueDateAndBank($conn2, $d['type_pv'], $d['no_kbon']);
    $bank = $extra['bank'];
    $from = getFromAccountInfo($conn2, $d['type_pv'], $d['no_kbon']);

    $rows[] = [
        'no_pv'         => $d['no_kbon'],
        'tgl_kbon'      => $d['tgl_kbon'],
        'tgl_tempo'     => $extra['tgl_tempo'],
        'curr'          => $d['curr'],
        'total'         => (float) $d['total'],
        'nama_supp'     => $d['nama_supp'],
        'bank'          => $bank,
        'deskripsi'     => $d['deskripsi'],
        'profit_center' => !empty($d['profit_center']) && $d['profit_center'] !== '-' ? $d['profit_center'] : '-',
        'from'          => $from,
    ];
}

// Hitung rowspan & subtotal per group Supplier (rows sudah terurut per nama_supp).
$groups = [];
foreach ($rows as $idx => $r) {
    $lastGroup = count($groups) - 1;
    if ($lastGroup >= 0 && $groups[$lastGroup]['nama_supp'] === $r['nama_supp']) {
        $groups[$lastGroup]['rowspan']++;
        $groups[$lastGroup]['total'] += $r['total'];
        $groups[$lastGroup]['end'] = $idx;
    } else {
        $groups[] = [
            'nama_supp' => $r['nama_supp'],
            'rowspan'   => 1,
            'total'     => $r['total'],
            'curr'      => $r['curr'],
            'bank'      => $r['bank'],
            'start'     => $idx,
            'end'       => $idx,
        ];
    }
}

$rowToGroup = [];
foreach ($groups as $g) {
    $rowToGroup[$g['start']] = $g;
}

$grandTotal = 0;
foreach ($rows as $r) {
    $grandTotal += $r['total'];
}
$grandTotalCurr = !empty($rows) ? $rows[0]['curr'] : 'IDR';

// Total per Profit Center untuk NOTE di bawah TTD - kode PC ditampilkan
// sebagai nama divisinya, bukan kode mentah (NAG = Garment, NAK = Knitting).
function pcLabel($code)
{
    if ($code === 'NAG') {
        return 'Garment';
    }
    if ($code === 'NAK') {
        return 'Knitting';
    }
    return $code;
}

$pcTotals = [];
foreach ($rows as $r) {
    $key = $r['profit_center'] . '|' . $r['curr'];
    if (!isset($pcTotals[$key])) {
        $pcTotals[$key] = ['label' => pcLabel($r['profit_center']), 'curr' => $r['curr'], 'total' => 0];
    }
    $pcTotals[$key]['total'] += $r['total'];
}
ksort($pcTotals);

ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        @page * {
            margin-top: 0.5cm;
            margin-bottom: 0.5cm;
            margin-left: 1cm;
            margin-right: 1cm;
        }

        table { margin: auto; }
        td, th { padding: 1px; text-align: left }
        h1 { text-align: center }
        th { text-align: center; padding: 2px; }

        .header {
            width: 100%;
            height: 20px;
            padding-top: 0;
            margin-bottom: 0px;
        }

        .title {
            font-size: 30px;
            font-weight: bold;
            text-align: left;
            margin-top: -90px;
        }

        .horizontal {
            height: 0;
            width: 100%;
            border: 1px solid #000000;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .td1 {
            border: 1px solid black;
            border-top: none;
            border-bottom: none;
        }
    </style>

    <title>PAYMENT LIST</title>
</head>
<body>

    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <img src="../../images/img-01.png" style="heigh:70px; width:80px;">
                </td>
                <td class="title">
                    PT.NIRWANA ALABARE GARMENT
                    <div style="font-size:12px;line-height:9">
                        Jl. Raya Rancaekek – Majalaya No. 289 Desa Solokan Jeruk Kecamatan Solokan Jeruk, <br />Kabupaten Bandung 40382 <br />Telp. 022-85962081
                    </div>
                </td>
            </tr>
        </table>
        &nbsp;
        <div class="horizontal"></div>
    </div>

    <hr />
    <table width="100%">
        <tr>
            <td><h5>PAYMENT LIST : <?php echo htmlspecialchars($rowH['pl_number']); ?></h5></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right"><h5>DATE : <?php echo !empty($rowH['pl_date']) ? date('d M Y', strtotime($rowH['pl_date'])) : '-'; ?></h5></td>
        </tr>
    </table>
    <hr />
    <table style="font-size:11px;">
    <thead>
        <tr>
            <td style="text-align:left;width: 30%;padding-bottom: 10px;"><b>BENEFICIARY NAME :</b></td>
            <td style="text-align:left;width: 20%;padding-bottom: 10px;"><b>BANK CURRENCY :</b></td>
            <td style="text-align:left;width: 25%;padding-bottom: 10px;"><b>BANK NAME :</b></td>
            <td style="text-align:left;width: 25%;padding-bottom: 10px;"><b>BANK ACCOUNT :</b></td>              
      </tr>

      <tbody>
        <tr>          
    <td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
    <?= $beneficiary_name ?>   
    </td>
    <td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
    <?= $bank_curr ?>  
    </td>
    <td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
        <?= $bank_name ?>
    </td>
    <td style="text-align:left;padding-top: -15px;padding-bottom: -10px;">
    <?= $bank_account ?>  
    </td>
   
</tr>



</tbody>
</table>
<hr />


    <table border="1" cellspacing="0" style="width:100%;font-size:7.6pt;border-spacing:2px;">
        <tr>
            <th rowspan="2" style="border: 1px solid black;">No PV</th>
            <th rowspan="2" style="border: 1px solid black;">PV Date</th>
            <th rowspan="2" style="border: 1px solid black;">Duedate</th>
            <th rowspan="2" style="border: 1px solid black;">Curr</th>
            <th rowspan="2" style="border: 1px solid black;">Amount</th>
            <th rowspan="2" style="border: 1px solid black;">Profit Center</th>
         <!--    <th rowspan="2" style="border: 1px solid black;">From Account</th> -->
            <th rowspan="2" style="border: 1px solid black;">Description</th>
            <th rowspan="2" style="border: 1px solid black;">Supplier</th>
            <th rowspan="2" style="border: 1px solid black;">Total Amount</th>
            <th colspan="4" style="border: 1px solid black;">To Account</th>
        </tr>
        <tr>
            <th style="border: 1px solid black;">Beneficiary Name</th>
            <th style="border: 1px solid black;">Bank Currency</th>
            <th style="border: 1px solid black;">Bank Name</th>
            <th style="border: 1px solid black;">Bank Account</th>
        </tr>
        <tbody>
            <?php foreach ($rows as $idx => $r) {
                $from = $r['from'];
                $fromStr = !empty($from['from_account'])
                    ? htmlspecialchars($from['from_account'])
                    : '-';
                ?>
               <!--  ? htmlspecialchars($from['from_bank'] . ' - ' . $from['from_account']) -->
                <tr>
                    <td style="text-align:left;vertical-align:top;"><?php echo htmlspecialchars($r['no_pv']); ?></td>
                    <td style="text-align:left;vertical-align:top;"><?php echo !empty($r['tgl_kbon']) ? date('d M Y', strtotime($r['tgl_kbon'])) : '-'; ?></td>
                    <td style="text-align:left;vertical-align:top;"><?php echo !empty($r['tgl_tempo']) ? date('d M Y', strtotime($r['tgl_tempo'])) : '-'; ?></td>
                    <td style="text-align:left;vertical-align:top;"><?php echo htmlspecialchars($r['curr']); ?></td>
                    <td style="text-align:right;vertical-align:top;"><?php echo number_format($r['total'], 2); ?></td>
                    <td style="text-align:left;vertical-align:top;"><?php echo htmlspecialchars($r['profit_center']); ?></td>
                   <!--  <td style="text-align:left;vertical-align:top;"><?php echo $fromStr; ?></td> -->
                    <td style="text-align:left;vertical-align:top;"><?php echo !empty($r['deskripsi']) ? htmlspecialchars($r['deskripsi']) : '-'; ?></td>
                    <?php if (isset($rowToGroup[$idx])) {
                        $g = $rowToGroup[$idx];
                        $bank = $g['bank'];
                        $bankHtml = 'Beneficiary Name : ' . htmlspecialchars($bank['beneficiary_name'] ?? '') . '<br>'
                            . 'Bank Account Number : ' . htmlspecialchars($bank['bank_account'] ?? '') . '<br>'
                            . 'Name Of The Bank : ' . htmlspecialchars($bank['bank_name'] ?? '') . '<br>'
                            . 'Bank Account Currency : ' . htmlspecialchars($bank['bank_currency'] ?? '');
                        ?>
                        <td style="text-align:left;vertical-align:top;" rowspan="<?php echo $g['rowspan']; ?>"><?php echo strtoupper(htmlspecialchars($g['nama_supp'])); ?></td>
                        <td style="text-align:right;vertical-align:top;" rowspan="<?php echo $g['rowspan']; ?>"><?php echo number_format($g['total'], 2); ?></td>
                        <td style="text-align:left;vertical-align:top;" rowspan="<?php echo $g['rowspan']; ?>"><?php echo strtoupper(htmlspecialchars($bank['beneficiary_name'])); ?></td>
                        <td style="text-align:left;vertical-align:top;" rowspan="<?php echo $g['rowspan']; ?>"><?php echo htmlspecialchars($bank['bank_currency']); ?></td>
                        <td style="text-align:left;vertical-align:top;" rowspan="<?php echo $g['rowspan']; ?>"><?php echo htmlspecialchars($bank['bank_name']); ?></td>
                        <td style="text-align:left;vertical-align:top;" rowspan="<?php echo $g['rowspan']; ?>"><?php echo htmlspecialchars($bank['bank_account']); ?></td>
                        <!-- <td style="text-align:left;vertical-align:top;" rowspan="<?php echo $g['rowspan']; ?>"><?php echo $bankHtml; ?></td> -->
                    <?php } ?>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="4" style="text-align:center;height: 25px;"><b>Grand Total</b></td>
                <td style="text-align:right;"><b><?php echo number_format($grandTotal, 2); ?></b></td>
                <td colspan="9"></td>
            </tr>
        </tbody>
    </table>

    <!--TTD_SPACER-->

    <div style="margin-bottom: 2.54cm; page-break-inside: avoid; text-align:left;">
    <table style="page-break-inside: avoid; width:60%; margin:0 auto;"
           cellpadding="0" cellspacing="0" border="1">
            <tr>
                <th style="font-size:12px">Created By : </th>
                <th style="font-size:12px">Checked By : </th>
                <th colspan="2" style="font-size:12px">Approved By : </th>
            </tr>
            <tr>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
            </tr>
            <tr>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
            </tr>
            <tr>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
            </tr>
            <tr>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
            </tr>
            <tr>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
                <td class="td1">&nbsp;</td>
            </tr>
            <tr>
                <td style="font-size:12px;text-align:center; width: 150px;"><?php echo htmlspecialchars($rowH['created_by']); ?></td>
                <td style="font-size:12px;text-align:center; width: 150px;"><?php
                $sql1 = mysqli_query($conn2, "select confirm1 from ttd");
                $rows1 = mysqli_fetch_array($sql1);
                echo $rows1['confirm1'];
                ?></td>
                <td style="font-size:12px;text-align:center; width: 150px;"><?php
                $sql2 = mysqli_query($conn2, "select confirm2 from ttd");
                $rows2 = mysqli_fetch_array($sql2);
                echo $rows2['confirm2'];
                ?></td>
                <td style="font-size:12px;text-align:center; width: 150px;"><?php
                $sql3 = mysqli_query($conn2, "select approve_by from ttd");
                $rows3 = mysqli_fetch_array($sql3);
                echo $rows3['approve_by'];
                ?></td>
            </tr>

            <tr>
                <td style="font-size:12px;text-align:center;">AP Staff</td>
                <td style="font-size:12px;text-align:center">Supervisor</td>
                <td style="font-size:12px;text-align:center">Finance Manager</td>
                <td style="font-size:12px;text-align:center">Director</td>
            </tr>
        </table>
        <br />

        <table style="page-break-inside: avoid; font-size:11px;" border="0">
            <tr>
                <td style="font-weight: bold">NOTE :</td>
            </tr>
            <tr>
                <td>Payment List Number : <?php echo htmlspecialchars($rowH['pl_number']); ?></td>
            </tr>
            <?php foreach ($pcTotals as $pc) { ?>
            <tr>
                <td>Total <?php echo htmlspecialchars($pc['label']) . ' : ' . htmlspecialchars($pc['curr']) . ' ' . number_format($pc['total'], 2); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td style="font-weight: bold">Total Payment List : <?php echo $grandTotalCurr . ' ' . number_format($grandTotal, 2); ?></td>
            </tr>
        </table>
    </div>

</body>

</html>

<?php
$html = ob_get_clean();
$html_parts = explode('<!--TTD_SPACER-->', $html, 2);
$html_before_ttd = $html_parts[0];
$html_ttd = isset($html_parts[1]) ? $html_parts[1] : '';

require_once __DIR__ . '/../../mpdf8/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/../../mpdf8/tmp',
    'format' => 'A4-L',
    'margin_left' => 6,
    'margin_right' => 6,
    'margin_top' => 6,
]);

$mpdf->WriteHTML($html_before_ttd);

$sisaHalaman = $mpdf->h - $mpdf->bMargin - $mpdf->y;
$estimasiTinggiTtd = 75;
$jarakTtd = max(5, min(20, $sisaHalaman - $estimasiTinggiTtd));

$mpdf->WriteHTML('<div style="height:' . $jarakTtd . 'mm;"></div>');
$mpdf->WriteHTML($html_ttd);
$mpdf->Output();
exit;
?>
