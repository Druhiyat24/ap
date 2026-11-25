<?php include '../header.php' ?>

<style type="text/css">
    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    #fxap-detail td, #fxap-detail th,
    #fxap-doc td, #fxap-doc th {
        padding: 4px 10px;
        font-size: 12.5px;
        white-space: nowrap;
    }
    #fxap-detail td.num, #fxap-doc td.num { text-align: right; }
    #fxap-detail td.num.negative, #fxap-doc td.num.negative { color: #c00000; }
    .fxap-tag { font-size: 11px; padding: 1px 6px; border-radius: 3px; }
    .fxap-loss { background: #fdecea; color: #c00000; }
    .fxap-gain { background: #e8f5e9; color: #1b5e20; }
</style>

<?php
require __DIR__ . '/fxap_functions.php';

$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-01');
$end_date   = isset($_POST['end_date'])   && $_POST['end_date']   !== '' ? date('Y-m-d', strtotime($_POST['end_date']))   : date('Y-m-d');
$action     = isset($_POST['fxap_action']) ? $_POST['fxap_action'] : '';

$hasil      = null;
$postResult = null;

if ($action === 'preview') {
    $hasil = fxApHitung($conn1, $start_date, $end_date);
} elseif ($action === 'post') {
    // Dihitung ULANG dari database, bukan dari angka yang dikirim form -
    // supaya tidak bisa diubah lewat request, dan supaya transaksi yang masuk
    // di antara Preview dan Post tetap ikut terhitung.
    $hasil      = fxApHitung($conn1, $start_date, $end_date);
    $postResult = fxApPost($conn1, $conn2, $hasil, $user);
}

$jmlCoaAktif = 0;
$qAktif = mysqli_query($conn1, "select count(*) n from mastercoa_v2 where forex_ap = 'Y'");
if ($qAktif && ($rAktif = mysqli_fetch_assoc($qAktif))) { $jmlCoaAktif = (int) $rAktif['n']; }
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-exchange" aria-hidden="true"></i> AP FOREX REVALUATION - AUTO JOURNAL</h5>
        </div>
        <div class="card-body p-3">
            <p class="text-muted mb-3">
                Revalues outstanding <b>USD accounts payable</b> per date, per profit center, per COA.
                Book value (opening + journal IDR movements) is compared against market value
                (USD balance &times; that date&#39;s KMK tax rate); the difference is posted to
                <b>8.52.02</b>. Each date &times; profit center becomes one journal
                (<code>FX-AP/&lt;PC&gt;/&lt;mmyy&gt;/&lt;00001&gt;</code>). Re-running the same period is safe -
                an existing journal is rewritten under the same number, never duplicated.
                Click <b>Preview</b> first and check the figures before posting.
            </p>
            <form id="form-fxap" method="post">
                <input type="hidden" name="fxap_action" id="fxap_action" value="preview">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" value="<?= htmlspecialchars(date('d-m-Y', strtotime($start_date))); ?>" placeholder="Start Date" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" value="<?= htmlspecialchars(date('d-m-Y', strtotime($end_date))); ?>" placeholder="End Date" autocomplete="off">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" id="btnPreview" class="btn btn-info btn-sm me-2">
                            <i class="fa fa-search" aria-hidden="true"></i> Preview
                        </button>
                    </div>
                    <div class="col-md-5 d-flex align-items-end">
                        <small class="text-muted">
                            <?= $jmlCoaAktif; ?> COA included. The list comes from <b>Master COA</b>
                            (field <i>AP Forex Revaluation</i>) - tick a COA there to add it, no code change needed.
                        </small>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!fxApSiap($conn1)) { ?>
    <div class="alert alert-danger mt-4 mb-0">
        <b>Not installed yet.</b> Column <code>mastercoa_v2.forex_ap</code> is missing, so no account can be
        revalued. Run <code>db_migrate_LIVE_20261124_forex_ap_coa.sql</code> on this database first.
    </div>
    <?php } ?>

    <?php if ($postResult !== null) { ?>
    <div class="card shadow border-0 mt-4">
        <div class="card-body p-4">
            <?php if (!$postResult['ok']) { ?>
                <div class="alert alert-danger mb-0">Posting failed, everything was rolled back: <?= htmlspecialchars($postResult['message']); ?></div>
            <?php } elseif (empty($postResult['dokumen'])) { ?>
                <div class="alert alert-warning mb-0">No difference found in this period - nothing was posted.</div>
            <?php } else { ?>
                <div class="alert alert-success">
                    <b><?= count($postResult['dokumen']); ?> journal(s) posted.</b>
                </div>
                <table class="table table-bordered table-sm" id="fxap-doc">
                    <thead class="table-gradient">
                        <tr><th>Date</th><th>Profit Center</th><th>Journal No.</th><th>Lines</th><th>Net Difference (IDR)</th><th>Result</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postResult['dokumen'] as $c) { ?>
                        <tr>
                            <td><?= date('d-M-Y', strtotime($c['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($c['pc']); ?></td>
                            <td><?= htmlspecialchars($c['doc']); ?></td>
                            <td class="num"><?= $c['baris']; ?></td>
                            <td class="num <?= $c['net'] < 0 ? 'negative' : ''; ?>"><?= number_format($c['net'], 2); ?></td>
                            <td><?= $c['aksi'] === 'update' ? 'Rewritten (already existed)' : 'New'; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
    </div>
    <?php } ?>

    <?php if ($hasil !== null && $postResult === null) {
        $jmlDok = count($hasil['dokumen']);
        $totalNet = 0;
        foreach ($hasil['dokumen'] as $dk) { $totalNet += $dk['net']; }

        // Tanggal yang periodenya sudah Closed - dihitung, ditampilkan, tapi
        // tidak bisa diposting. Ditunjukkan terang-terangan supaya tidak
        // terlihat seperti "tidak ada selisih".
        $tutup = array(); $netTutup = 0;
        foreach ($hasil['baris'] as $b) {
            if (!empty($b['periode_tutup'])) {
                $tutup[substr($b['tanggal'], 0, 7)] = true;
                $netTutup += $b['selisih'];
            }
        }
    ?>
    <div class="card shadow border-0 mt-4">
        <div class="card-body p-4">
            <?php if (!empty($tutup)) { ?>
            <div class="alert alert-warning">
                <b>Closed accounting period.</b>
                <?= number_format($netTutup, 2); ?> IDR of difference falls in
                <?= implode(', ', array_map(function ($b) { return date('F Y', strtotime($b . '-01')); }, array_keys($tutup))); ?>,
                which is <b>Closed</b> in <code>tbl_closing_periode</code>. Those rows are shown below for reference
                but will <b>not</b> be posted. Reopen the period first if the journal is needed there.
            </div>
            <?php } ?>
            <div class="mb-3">
                <?php if ($jmlDok === 0) { ?>
                    No postable difference found in this period.
                <?php } else { ?>
                    <b><?= $jmlDok; ?></b> journal(s) will be created from <b><?= count($hasil['baris']); ?></b> account lines.
                    Combined net difference <b><?= number_format($totalNet, 2); ?></b> IDR
                    (<?= $totalNet > 0 ? 'net loss - debit 8.52.02' : 'net gain - credit 8.52.02'; ?>).
                    <br>
                    <small class="text-muted">
                        Opening balance taken from the AP Report as at
                        <b><?= date('d-M-Y', strtotime($hasil['per_tanggal'])); ?></b>,
                        valued at rate <b><?= number_format($hasil['rate_awal'], 2); ?></b>.
                    </small>
                <?php } ?>
            </div>

            <?php if ($jmlDok > 0) { ?>
            <h6 class="mt-4 mb-2"><b>Journals to be created</b></h6>
            <div class="table-responsive" style="max-height: 35vh; overflow-y: auto;">
                <table id="fxap-doc" class="table table-bordered table-sm mb-0">
                    <thead class="table-gradient">
                        <tr><th>Date</th><th>Profit Center</th><th>COA Lines</th><th>Net Difference (IDR)</th><th>8.52.02</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hasil['dokumen'] as $dk) { ?>
                        <tr>
                            <td><?= date('d-M-Y', strtotime($dk['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($dk['pc']); ?></td>
                            <td class="num"><?= count($dk['baris']); ?></td>
                            <td class="num <?= $dk['net'] < 0 ? 'negative' : ''; ?>"><?= number_format($dk['net'], 2); ?></td>
                            <td>
                                <?php if ($dk['net'] > 0) { ?>
                                    <span class="fxap-tag fxap-loss">Debit (loss)</span>
                                <?php } else { ?>
                                    <span class="fxap-tag fxap-gain">Credit (gain)</span>
                                <?php } ?>
                            </td>
                            <td><?= $dk['dokumen_ada'] ? 'Rewrite ' . htmlspecialchars($dk['dokumen_ada']) : 'New'; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>

            <?php if (count($hasil['baris']) > 0) { ?>
            <h6 class="mt-4 mb-2"><b>Calculation detail</b> <small class="text-muted">(same layout as the accounting worksheet)</small></h6>
            <div class="table-responsive" style="max-height: 45vh; overflow-y: auto;">
                <table id="fxap-detail" class="table table-bordered table-sm mb-0">
                    <thead class="table-gradient">
                        <tr>
                            <th>Date</th>
                            <th>PC</th>
                            <th>COA</th>
                            <th>Account Name</th>
                            <th>Balance USD</th>
                            <th>KMK Rate</th>
                            <th>Book IDR</th>
                            <th>Market IDR</th>
                            <th>Difference</th>
                            <th>Payable Side</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hasil['baris'] as $b) { ?>
                        <tr<?= !empty($b['periode_tutup']) ? ' style="background:#fffbe6;"' : ''; ?>>
                            <td><?= date('d-M-Y', strtotime($b['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($b['pc']); ?></td>
                            <td><?= htmlspecialchars($b['no_coa']); ?></td>
                            <td><?= htmlspecialchars($b['nama_coa']); ?></td>
                            <td class="num"><?= number_format($b['saldo_usd'], 2); ?></td>
                            <td class="num"><?= number_format($b['rate'], 2); ?></td>
                            <td class="num"><?= number_format($b['buku'], 2); ?></td>
                            <td class="num"><?= number_format($b['pasar'], 2); ?></td>
                            <td class="num <?= $b['selisih'] < 0 ? 'negative' : ''; ?>"><?= number_format($b['selisih'], 2); ?></td>
                            <td><?= $b['selisih'] > 0 ? 'Credit' : 'Debit'; ?></td>
                            <td><?= !empty($b['periode_tutup']) ? '<span class="fxap-tag fxap-loss">Period closed</span>' : 'Will be posted'; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>

            <?php if ($jmlDok > 0) { ?>
            <form id="form-fxap-post" method="post" class="mt-3">
                <input type="hidden" name="fxap_action" value="post">
                <input type="hidden" name="start_date" value="<?= htmlspecialchars(date('d-m-Y', strtotime($start_date))); ?>">
                <input type="hidden" name="end_date" value="<?= htmlspecialchars(date('d-m-Y', strtotime($end_date))); ?>">
                <button type="submit" id="btnPost" class="btn btn-danger btn-sm">
                    <i class="fa fa-check-circle" aria-hidden="true"></i> Post <?= $jmlDok; ?> Journal(s)
                </button>
                <span class="text-muted ms-2">Figures are recalculated from the database when this button is pressed, not taken from the table above.</span>
            </form>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({ format: "dd-mm-yyyy", autoclose: true });
    });

    document.getElementById('form-fxap').addEventListener('submit', function () {
        document.getElementById('fxap_action').value = 'preview';
    });

    var postForm = document.getElementById('form-fxap-post');
    if (postForm) {
        postForm.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Post the journals now?',
                text: 'This creates or rewrites accounting entries and is not easy to undo. Make sure the preview is correct.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, post',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545'
            }).then(function (result) {
                if (result.isConfirmed) { postForm.submit(); }
            });
        });
    }
</script>
