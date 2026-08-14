<?php include '../header.php' ?>

<style type="text/css">
    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    #cfr-table {
        font-size: 11px;
        white-space: nowrap;
    }
    #cfr-table td, #cfr-table th {
        padding: 3px 8px;
    }
    #cfr-table td.num {
        text-align: right;
    }
    /* Freeze Descriptions/Projection/Realisation/Variance (kolom 1-4) supaya
       tetap kelihatan saat scroll horizontal - sengaja dikecualikan dari sel
       yang punya colspan (baris judul section/spacer), karena sel itu
       melebar ke seluruh baris jadi tidak cocok di-freeze sebagian. */
    #cfr-table th:nth-child(-n+4),
    #cfr-table td:not([colspan]):nth-child(-n+4) {
        position: sticky;
        background: #fff;
        z-index: 2;
    }
    #cfr-table th:nth-child(1), #cfr-table td:not([colspan]):nth-child(1) { left: 0; width: 220px; min-width: 220px; }
    #cfr-table th:nth-child(2), #cfr-table td:not([colspan]):nth-child(2) { left: 220px; width: 90px; min-width: 90px; }
    #cfr-table th:nth-child(3), #cfr-table td:not([colspan]):nth-child(3) { left: 310px; width: 90px; min-width: 90px; }
    #cfr-table th:nth-child(4), #cfr-table td:not([colspan]):nth-child(4) { left: 400px; width: 90px; min-width: 90px; }
    #cfr-table thead th:nth-child(-n+4) { z-index: 3; background: #1E3A8A; }
    #cfr-table tr.grand-row td:not([colspan]):nth-child(-n+4) { background: #eef2f9; }
    /* Baris kedua header (nama-nama akun bank/kas) child-nya sendiri dihitung
       dari 1 lagi oleh browser (rowspan dari baris pertama tidak ikut
       dihitung) - jadi tanpa override ini, 4 kolom bank/kas PERTAMA malah
       ketimpa jadi ikut freeze mengikuti aturan th:nth-child(-n+4) di atas. */
    #cfr-table thead tr.account-header-row th {
        position: static;
        width: auto;
        min-width: 90px;
        left: auto;
        z-index: auto;
    }
    #cfr-table tr.section-title td {
        font-weight: bold;
        text-decoration: underline;
        color: #1E3A8A;
    }
    #cfr-table tr.total-row td {
        font-weight: bold;
        border-top: 1px solid #333;
        border-bottom: 1px solid #333;
    }
    #cfr-table tr.grand-row td {
        font-weight: bold;
        background: #eef2f9;
    }
    #cfr-table tr.disbursement-header td {
        font-weight: bold;
        color: #b91c1c;
    }
</style>

<?php
require __DIR__ . '/cfr_functions.php';


// Default From/To = hari ini (bukan awal bulan) - dan laporan langsung tampil
// begitu halaman pertama kali dibuka (belum klik Search), pakai tanggal hari
// ini itu sebagai filter awal.
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$end_date   = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? date('Y-m-d', strtotime($_POST['end_date'])) : date('Y-m-d');
$doSearch   = true;
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-line-chart" aria-hidden="true"></i> REPORT CASH FLOW REALISATION</h5>
        </div>
        <div class="card-body p-3">
            <form id="form-data" method="post">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" value="<?= date('d-m-Y', strtotime($start_date)); ?>" placeholder="Start Date" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" value="<?= date('d-m-Y', strtotime($end_date)); ?>" placeholder="End Date" autocomplete="off">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" id="submit" class="btn btn-info btn-sm me-2">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        <button type="button" id="btnExportExcel" class="btn btn-success btn-sm ml-2">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow border-0 mt-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <?php if (!$doSearch) { ?>
                    <p class="text-muted mb-0">Pilih periode lalu klik Search untuk menampilkan laporan.</p>
                <?php } else {

                // =========================
                // HITUNG SEMUA DATA REPORT (akun, saldo awal/akhir, kategori,
                // realisasi, penerimaan/pelunasan pinjaman bank dedicated)
                // =========================
                extract(cfrComputeReportData($conn2, $start_date, $end_date));
                ?>

                <div class="mb-3">
                    <div><b>PT NIRWANA ALABARE GARMENT</b></div>
                    <div><b>CASH FLOW REALISATION</b></div>
                    <div>FOR PERIOD <?= strtoupper(date('d M Y', strtotime($start_date))); ?> - <?= strtoupper(date('d M Y', strtotime($end_date))); ?></div>
                </div>

                <table id="cfr-table" class="table table-bordered table-sm">
                    <thead class="table-gradient">
                        <tr>
                            <th rowspan="2" style="text-align:left;">DESCRIPTIONS</th>
                            <th rowspan="2">PROJECTION</th>
                            <th rowspan="2">REALISATION</th>
                            <th rowspan="2">VARIANCE</th>
                            <th colspan="<?= $colspanAccounts; ?>">REALISATION BY BANK</th>
                        </tr>
                        <tr class="account-header-row">
                            <?php foreach ($accounts as $acc) { ?>
                                <th><?= htmlspecialchars($acc['label']); ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="grand-row">
                            <td>SALDO AWAL KAS DAN BANK</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalBegin); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($beginBalance[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>

                        <tr class="section-title"><td colspan="<?= 4 + $colspanAccounts; ?>">CASH RECEIPTS :</td></tr>

                        <?php
                        $grandReceiptTotal = 0;
                        $subtotalReceiptByAct = [];
                        foreach (['OPERATING ACTIVITIES' => '1', 'INVESTING ACTIVITIES' => '2', 'FINANCING ACTIVITIES' => '3'] as $act => $actNo) {
                            $rows = $categories['Cash In'][$act];
                            if (empty($rows)) continue;
                            echo '<tr class="section-title"><td colspan="' . (4 + $colspanAccounts) . '">' . $actNo . '. ' . $act . ' :</td></tr>';
                            $actTotal = 0;
                            foreach ($rows as $row) {
                                $total = cfrRowTotal($realisasi, $row['id'], true);
                                $actTotal += $total;
                                echo '<tr><td style="padding-left:30px;">' . $row['display_seq'] . ' ' . htmlspecialchars($row['nama_subcategory']) . '</td>';
                                echo '<td class="num">-</td><td class="num">' . cfrFmt($total) . '</td><td class="num">-</td>';
                                foreach ($accounts as $acc) {
                                    echo '<td class="num">' . cfrFmt(cfrRowValue($realisasi, $row['id'], $acc['account'], true)) . '</td>';
                                }
                                echo '</tr>';
                            }
                            echo '<tr class="total-row"><td>TOTAL CASH RECEIPT FROM ' . $act . '</td>';
                            echo '<td class="num">-</td><td class="num">' . cfrFmt($actTotal) . '</td><td class="num">-</td>';
                            foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach ($rows as $row) {
                                    $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], true);
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            }
                            echo '</tr><tr><td colspan="' . (4 + $colspanAccounts) . '">&nbsp;</td></tr>';
                            $subtotalReceiptByAct[$act] = $actTotal;
                            $grandReceiptTotal += $actTotal;
                        }
                        ?>

                        <tr class="total-row">
                            <td>TOTAL CASH RECEIPTS</td>
                            <td class="num">-</td><td class="num"><?= cfrFmt($grandReceiptTotal); ?></td><td class="num">-</td>
                            <?php foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach ($categories['Cash In'] as $rows) {
                                    foreach ($rows as $row) {
                                        $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], true);
                                    }
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            } ?>
                        </tr>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>

                        <tr class="disbursement-header"><td colspan="<?= 4 + $colspanAccounts; ?>">CASH DISBURSEMENT:</td></tr>

                        <?php
                        $grandDisbTotal = 0;
                        $subtotalDisbByAct = [];
                        foreach (['OPERATING ACTIVITIES' => '1', 'INVESTING ACTIVITIES' => '2', 'FINANCING ACTIVITIES' => '3'] as $act => $actNo) {
                            $rows = $categories['Cash Out'][$act];
                            if (empty($rows)) continue;
                            echo '<tr class="section-title"><td colspan="' . (4 + $colspanAccounts) . '">' . $actNo . '. ' . $act . ':</td></tr>';
                            $actTotal = 0;
                            foreach ($rows as $row) {
                                $total = cfrRowTotal($realisasi, $row['id'], false);
                                $actTotal += $total;
                                echo '<tr><td style="padding-left:30px;">' . $row['display_seq'] . ' ' . htmlspecialchars($row['nama_subcategory']) . '</td>';
                                echo '<td class="num">-</td><td class="num">' . cfrFmt($total) . '</td><td class="num">-</td>';
                                foreach ($accounts as $acc) {
                                    echo '<td class="num">' . cfrFmt(cfrRowValue($realisasi, $row['id'], $acc['account'], false)) . '</td>';
                                }
                                echo '</tr>';
                            }
                            echo '<tr class="total-row"><td>TOTAL CASH DISBURSEMENT FROM ' . $act . '</td>';
                            echo '<td class="num">-</td><td class="num">' . cfrFmt($actTotal) . '</td><td class="num">-</td>';
                            foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach ($rows as $row) {
                                    $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], false);
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            }
                            echo '</tr><tr><td colspan="' . (4 + $colspanAccounts) . '">&nbsp;</td></tr>';
                            $subtotalDisbByAct[$act] = $actTotal;
                            $grandDisbTotal += $actTotal;
                        }
                        ?>

                        <tr class="total-row">
                            <td>TOTAL CASH DISBURSEMENT</td>
                            <td class="num">-</td><td class="num"><?= cfrFmt($grandDisbTotal); ?></td><td class="num">-</td>
                            <?php foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach ($categories['Cash Out'] as $rows) {
                                    foreach ($rows as $row) {
                                        $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], false);
                                    }
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            } ?>
                        </tr>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>

                        <tr class="section-title"><td colspan="<?= 4 + $colspanAccounts; ?>">SUBTOTAL ACTIVITIES</td></tr>
                        <?php foreach (['OPERATING ACTIVITIES', 'INVESTING ACTIVITIES', 'FINANCING ACTIVITIES'] as $act) {
                            $subTotal = ($subtotalReceiptByAct[$act] ?? 0) + ($subtotalDisbByAct[$act] ?? 0);
                            echo '<tr><td style="padding-left:20px;">' . strtoupper($act) . '</td>';
                            echo '<td class="num">-</td><td class="num">' . cfrFmt($subTotal) . '</td><td class="num">-</td>';
                            foreach ($accounts as $acc) {
                                $accTotal = 0;
                                foreach (($categories['Cash In'][$act] ?? []) as $row) {
                                    $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], true);
                                }
                                foreach (($categories['Cash Out'][$act] ?? []) as $row) {
                                    $accTotal += cfrRowValue($realisasi, $row['id'], $acc['account'], false);
                                }
                                echo '<td class="num">' . cfrFmt($accTotal) . '</td>';
                            }
                            echo '</tr>';
                        } ?>
                        <tr><td colspan="<?= 4 + $colspanAccounts; ?>">&nbsp;</td></tr>

                        <tr class="grand-row">
                            <td>SALDO AKHIR KAS DAN BANK</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalEnd); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($endBalance[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script type="text/javascript">
    function cfrToYmd(dmy) {
        if (!dmy) return '';
        let p = dmy.split('-'); // [dd, mm, yyyy]
        return `${p[2]}-${p[1]}-${p[0]}`;
    }

    document.getElementById('btnExportExcel').addEventListener('click', function () {
        let sd = cfrToYmd(document.getElementById('start_date').value);
        let ed = cfrToYmd(document.getElementById('end_date').value);

        Swal.fire({
            title: 'Sedang export...',
            text: 'Mohon tunggu, jangan tutup halaman ini.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`ekspor_cashflow_realisation.php?start_date=${sd}&end_date=${ed}`)
            .then(async (res) => {
                const contentType = res.headers.get('Content-Type') || '';
                if (contentType.indexOf('json') !== -1) {
                    const data = await res.json();
                    Swal.fire({ icon: 'warning', title: 'Oops...', text: data.error || 'Export gagal.' });
                    return;
                }
                const blob = await res.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Report Cash Flow Realisation.xlsx';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                Swal.close();
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Export gagal, coba lagi.' });
            });
    });

    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });
</script>

</body>
</html>
