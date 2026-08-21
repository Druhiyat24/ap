<?php include '../header.php' ?>

<style type="text/css">
    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    #fxsk-table td, #fxsk-table th {
        padding: 4px 10px;
        font-size: 12.5px;
    }
    #fxsk-table td.num {
        text-align: right;
    }
    #fxsk-table td.num.negative {
        color: #c00000;
    }
</style>

<?php
require __DIR__ . '/fxsk_functions.php';

$account = isset($_POST['account']) ? $_POST['account'] : '';
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? date('Y-m-d', strtotime($_POST['end_date'])) : date('Y-m-d');
$action = isset($_POST['fxsk_action']) ? $_POST['fxsk_action'] : '';

$previewRows = null;
$postResult = null;

if ($action === 'preview' && $account !== '') {
    $previewRows = fxSkCalcRevaluation($conn1, $account, $start_date, $end_date);
} elseif ($action === 'post' && $account !== '') {
    $sqlBank = mysqli_query($conn1, "select b_code, id_coa, coa_name, profit_center_bank from b_masterbank where bank_account = '" . mysqli_real_escape_string($conn1, $account) . "'");
    $bankInfo = mysqli_fetch_assoc($sqlBank);

    if (!$bankInfo || empty($bankInfo['id_coa'])) {
        $postResult = ['ok' => false, 'message' => 'Akun ini belum punya COA (id_coa) di Master Bank - lengkapi dulu sebelum posting.'];
    } else {
        // Dihitung ULANG dari DB (bukan percaya angka dari form preview) -
        // supaya tidak bisa "dimanipulasi" lewat request yang di-tamper, dan
        // supaya kalau ada transaksi baru masuk di antara Preview dan Post,
        // yang dibukukan tetap angka yang paling baru/benar.
        $rows = fxSkCalcRevaluation($conn1, $account, $start_date, $end_date);

        // Setup kategori memorial journal (idempotent, bukan data finansial) -
        // di luar transaksi, tidak perlu ikut rollback kalau posting gagal.
        fxSkEnsureCategory($conn1);

        mysqli_begin_transaction($conn2);
        try {
            $created = [];
            foreach ($rows as $row) {
                $result = fxSkPostOrUpdateJournal($conn1, $conn2, $account, $bankInfo, $row['date'], $row['selisih'], $row['existing_doc'], $user);
                $created[] = ['date' => $row['date'], 'selisih' => $row['selisih'], 'doc' => $result['doc'], 'action' => $result['action']];
            }
            mysqli_commit($conn2);
            $postResult = ['ok' => true, 'created' => $created];
        } catch (\Throwable $e) {
            mysqli_rollback($conn2);
            $postResult = ['ok' => false, 'message' => 'Posting gagal, semua perubahan dibatalkan: ' . $e->getMessage()];
        }
    }
}
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-exchange" aria-hidden="true"></i> AUTO JURNAL SELISIH KURS BANK</h5>
        </div>
        <div class="card-body p-3">
            <p class="text-muted mb-3">
                Menghitung selisih revaluasi saldo bank USD - saldo BUKU (akumulasi per transaksi pakai kurs HARIAN tanggalnya sendiri) dibanding saldo PASAR (saldo native x kurs HARIAN tanggal itu sendiri) - PER TANGGAL YANG ADA TRANSAKSINYA saja (tanggal tanpa transaksi mustahil punya selisih). Tiap tanggal = 1 jurnal sendiri; kalau tanggal itu sudah pernah dijurnal, di-UPDATE (bukan dobel). Klik <b>Preview</b> dulu untuk melihat hitungannya sebelum benar-benar diposting.
            </p>
            <form id="form-fxsk" method="post">
                <input type="hidden" name="fxsk_action" id="fxsk_action" value="preview">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="account" class="form-label"><b>Account</b></label>
                        <select class="form-control selectpicker" name="account" id="account" data-live-search="true" required>
                            <option value="" disabled <?= $account === '' ? 'selected' : ''; ?>>Select Account</option>
                            <?php
                            // Selisih kurs cuma relevan buat akun USD (sesuai arahan user) - bukan
                            // sekadar "non-IDR" (walau saat ini kebetulan USD-lah satu-satunya mata
                            // uang asing yang dipakai, filter-nya sengaja dipersempit eksplisit).
                            $sqlAcc = mysqli_query($conn1, "select bank_account, curr, bank_name from b_masterbank where status = 'Active' and curr = 'USD' order by bank_account");
                            while ($row = mysqli_fetch_array($sqlAcc)) {
                                $sel = ($row['bank_account'] === $account) ? ' selected' : '';
                                echo '<option value="' . htmlspecialchars($row['bank_account']) . '"' . $sel . '>' . htmlspecialchars($row['bank_account']) . ' - ' . htmlspecialchars($row['bank_name']) . ' (' . htmlspecialchars($row['curr']) . ')</option>';
                            }
                            ?>
                        </select>
                        <small class="text-muted">Cuma akun USD yang relevan direvaluasi.</small>
                    </div>
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
                </div>
            </form>
        </div>
    </div>

    <?php if ($postResult !== null) { ?>
    <div class="card shadow border-0 mt-4">
        <div class="card-body p-4">
            <?php if ($postResult['ok']) { ?>
                <?php if (empty($postResult['created'])) { ?>
                    <div class="alert alert-warning mb-0">Tidak ada tanggal dengan selisih di periode ini - tidak ada jurnal yang dibuat/diupdate.</div>
                <?php } else { ?>
                    <div class="alert alert-success">
                        <b><?= count($postResult['created']); ?> jurnal berhasil diproses.</b>
                    </div>
                    <table class="table table-bordered table-sm">
                        <thead class="table-gradient">
                            <tr><th>Tanggal</th><th>Selisih (IDR)</th><th>No. Dokumen</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($postResult['created'] as $c) { ?>
                            <tr>
                                <td><?= date('d-M-Y', strtotime($c['date'])); ?></td>
                                <td class="text-end"><?= number_format($c['selisih'], 2); ?></td>
                                <td><?= htmlspecialchars($c['doc']); ?></td>
                                <td><?= $c['action'] === 'update' ? 'Update (sudah pernah ada)' : 'Baru'; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            <?php } else { ?>
                <div class="alert alert-danger mb-0"><?= htmlspecialchars($postResult['message']); ?></div>
            <?php } ?>
        </div>
    </div>
    <?php } elseif ($previewRows !== null) { ?>
    <div class="card shadow border-0 mt-4">
        <div class="card-body p-4">
            <?php
            $totalSelisih = 0;
            foreach ($previewRows as $r) {
                $totalSelisih += $r['selisih'];
            }
            $rowCount = count($previewRows);
            ?>
            <div class="mb-3">
                <?php if ($rowCount === 0) { ?>
                    Tidak ada tanggal dengan selisih di periode ini - tidak ada yang perlu dijurnal.
                <?php } else { ?>
                    <b><?= $rowCount; ?></b> tanggal punya selisih (akan jadi <?= $rowCount; ?> jurnal kalau di-Post - total gabungan <?= number_format($totalSelisih, 2); ?> IDR, tapi tetap dibukukan sebagai <?= $rowCount; ?> jurnal terpisah per tanggal).
                <?php } ?>
            </div>
            <?php if ($rowCount > 0) { ?>
            <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                <table id="fxsk-table" class="table table-bordered table-sm mb-0">
                    <thead class="table-gradient">
                        <tr>
                            <th>Tanggal</th>
                            <th>Ada Transaksi?</th>
                            <th>Saldo Native</th>
                            <th>Kurs Pajak</th>
                            <th>Kurs Harian</th>
                            <th>Saldo Buku IDR</th>
                            <th>Saldo Pasar IDR</th>
                            <th>Selisih (IDR)</th>
                            <th>Jenis</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($previewRows as $r) { ?>
                        <tr>
                            <td><?= date('d-M-Y', strtotime($r['date'])); ?></td>
                            <td><?= $r['has_transaction'] ? 'Ya' : 'Tidak (murni revaluasi)'; ?></td>
                            <td class="num"><?= number_format($r['native_balance'], 2); ?></td>
                            <td class="num"><?= number_format($r['pajak_rate'], 2); ?></td>
                            <td class="num"><?= number_format($r['harian_rate'], 2); ?></td>
                            <td class="num"><?= number_format($r['book_idr'], 2); ?></td>
                            <td class="num"><?= number_format($r['market_idr'], 2); ?></td>
                            <td class="num <?= $r['selisih'] < 0 ? 'negative' : ''; ?>"><?= number_format($r['selisih'], 2); ?></td>
                            <td><?= $r['selisih'] > 0 ? 'Bank In (Debit)' : 'Bank Out (Credit)'; ?></td>
                            <td><?= $r['existing_doc'] ? 'Update ' . htmlspecialchars($r['existing_doc']) : 'Baru'; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <form id="form-fxsk-post" method="post" class="mt-3">
                <input type="hidden" name="fxsk_action" value="post">
                <input type="hidden" name="account" value="<?= htmlspecialchars($account); ?>">
                <input type="hidden" name="start_date" value="<?= htmlspecialchars(date('d-m-Y', strtotime($start_date))); ?>">
                <input type="hidden" name="end_date" value="<?= htmlspecialchars(date('d-m-Y', strtotime($end_date))); ?>">
                <button type="submit" id="btnPost" class="btn btn-danger btn-sm">
                    <i class="fa fa-check-circle" aria-hidden="true"></i> Post <?= $rowCount; ?> Jurnal
                </button>
                <span class="text-muted ms-2">Angka dihitung ulang dari database saat tombol ini ditekan (bukan dari tabel di atas) - jadi tetap akurat walau ada transaksi baru masuk di antara Preview dan Post.</span>
            </form>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        $('.selectpicker').selectpicker();
    });

    document.getElementById('form-fxsk').addEventListener('submit', function () {
        document.getElementById('fxsk_action').value = 'preview';
    });

    var postForm = document.getElementById('form-fxsk-post');
    if (postForm) {
        postForm.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Post jurnal sekarang?',
                text: 'Tindakan ini membuat/mengubah entry akuntansi dan tidak mudah dibatalkan. Pastikan hasil Preview sudah benar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Post',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then(function (result) {
                if (result.isConfirmed) {
                    postForm.submit();
                }
            });
        });
    }
</script>
