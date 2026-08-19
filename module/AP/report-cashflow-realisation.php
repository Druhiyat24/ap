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
    /* Area tabel dibatasi tinggi biar bisa di-scroll di dalam kotaknya
       sendiri (bukan scroll seluruh halaman). */
    #cfr-table-wrapper {
        max-height: 65vh;
        overflow-y: auto;
        position: relative;
    }
    /* Freeze kolom DESCRIPTIONS (kolom 1 saja) supaya tetap kelihatan saat
       scroll horizontal - sengaja dikecualikan dari sel yang punya colspan
       (baris judul section/spacer), karena sel itu melebar ke seluruh baris
       jadi tidak cocok di-freeze. */
    #cfr-table th:nth-child(1),
    #cfr-table td:not([colspan]):nth-child(1) {
        position: sticky;
        left: 0;
        width: 220px;
        min-width: 220px;
        background: #fff;
        z-index: 2;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #cfr-table tr.grand-row td:not([colspan]):nth-child(1) { background: #eef2f9; }
    /* Baris nama bank (account-header-row) TIDAK punya sel untuk kolom 1-4
       sama sekali di DOM-nya (kolom itu sudah "diisi" rowspan=2 dari baris
       header pertama) - jadi th PERTAMA di baris ini sebenarnya kolom bank
       ke-1 (visual), BUKAN kolom DESCRIPTIONS, walau sama-sama nth-child(1).
       Tanpa override ini dia ikut ke-freeze di left:0 seolah-olah kolom
       DESCRIPTIONS, jadi ketutup/hilang di belakang kolom itu. */
    #cfr-table thead tr.account-header-row th:nth-child(1) {
        /* Tetap sticky (biar tetap nempel di bawah header saat discroll
           VERTIKAL, sama seperti sel header lainnya) - cuma left/width-nya
           yang direset supaya tidak ikut freeze HORIZONTAL seperti kolom
           DESCRIPTIONS. */
        position: sticky;
        left: auto;
        width: auto;
        min-width: 90px;
        background: #1E3A8A;
        overflow: visible;
        text-overflow: clip;
    }
    /* Freeze header (horizontal) - dipakai bareng dengan freeze kolom 1 di
       atas (vertical). Karena ini sticky di TABLE YANG SAMA (bukan tabel
       salinan/clone terpisah), lebar & posisi kolom header otomatis selalu
       identik dengan body-nya. Ketiga baris di thead (judul kolom, nama
       bank, SALDO AWAL) dibuat freeze - nilai `top` baris ke-2/ke-3 di-set
       lewat JS (bukan di-hardcode di sini) karena baris pertama pakai
       rowspan=2 sehingga tingginya tidak otomatis kehitung CSS untuk baris
       di bawahnya. */
    #cfr-table thead tr:nth-child(1) th,
    #cfr-table thead tr.account-header-row th,
    #cfr-table thead tr#cfr-saldo-awal-row td {
        position: sticky;
        z-index: 2;
        top: 0;
    }
    /* Sel pojok (kolom DESCRIPTIONS di tiap baris header) butuh z-index
       lebih tinggi supaya tidak ketutup kolom sticky-left baris body yang
       lewat di bawahnya saat discroll. */
    #cfr-table thead tr:nth-child(1) th:nth-child(1) {
        z-index: 3;
        background: #1E3A8A;
    }
    #cfr-table thead tr#cfr-saldo-awal-row td:nth-child(1) {
        z-index: 3;
    }
    /* Baris judul section yang di-merge (CASH RECEIPTS, 1. OPERATING
       ACTIVITIES, dst - pakai <td colspan>) melebar sepanjang baris, dan
       sticky langsung di <td> selebar itu ternyata tidak konsisten nempel di
       semua kondisi. Jadi dipakai teknik yang lebih pasti jalan: teks di
       dalamnya dibungkus <span> (lihat script di bawah), lalu SPAN itu yang
       di-sticky - caranya lebih reliable karena elemen yang di-sticky-kan
       kecil (selebar teksnya saja), bukan sel yang selebar seluruh baris. */
    #cfr-table td[colspan] > span.cfr-sticky-label {
        position: sticky;
        left: 8px;
        display: inline-block;
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

    /* Shadow tipis di tepi kolom/baris yang freeze - sinyal visual standar
       bahwa area itu "mengambang" di atas konten yang discroll di
       bawah/sampingnya. */
    #cfr-table th:nth-child(1),
    #cfr-table td:not([colspan]):nth-child(1) {
        box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.2);
    }
    #cfr-table thead tr#cfr-saldo-awal-row td {
        box-shadow: 0 2px 4px -2px rgba(0, 0, 0, 0.3);
    }

    /* Highlight baris saat di-hover - baris section-title/total/grand/
       disbursement-header dikecualikan karena sudah punya warna penekanan
       sendiri (biar tidak "bertabrakan" saat di-hover). */
    #cfr-table tbody tr:not(.section-title):not(.total-row):not(.grand-row):not(.disbursement-header):hover td:not([colspan]) {
        background: #eaf1ff;
    }

    /* Baris data selang-seling (zebra) - dikasih lewat class .cfr-row-alt
       yang di-set oleh JS (lihat script di bawah), bukan CSS :nth-child
       biasa, karena banyak baris spacer/section-title/total di antara baris
       data yang bikin pola :nth-child jadi tidak rapi kalau dihitung dari
       urutan DOM mentah. */
    #cfr-table tbody tr.cfr-row-alt td:not([colspan]) {
        background: #f7f9fc;
    }

    /* Angka negatif (format "(1.234,56)" dari cfrFmt) ditandai merah -
       konvensi umum laporan keuangan. Class ditambahkan lewat JS karena
       cfrFmt() cuma menghasilkan teks polos, tidak ada penanda negatif di
       markup-nya. */
    #cfr-table td.cfr-negative {
        color: #c00000;
    }

    /* Filter "Account" (dropdown REALISATION BY BANK) - gaya toggle switch
       yang sama seperti panel "Manage Role" di userrole.php, biar konsisten
       di seluruh aplikasi. */
    #cfrAccountDropdownMenu {
        min-width: 320px;
        max-height: 320px;
        overflow-y: auto;
    }
    .cfr-acc-panel {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .cfr-acc-all-row {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 8px;
        padding-bottom: 8px;
    }
    .cfr-acc-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 10px;
        font-size: 12.5px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        transition: box-shadow .15s ease, border-color .15s ease;
    }
    .cfr-acc-row:hover {
        border-color: #93c5fd;
        box-shadow: 0 2px 6px rgba(37, 99, 235, .15);
    }
    .cfr-acc-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .cfr-acc-all-row .cfr-acc-label {
        font-weight: 700;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 38px;
        height: 21px;
        flex-shrink: 0;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: background-color .2s ease;
        border-radius: 21px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 15px;
        width: 15px;
        left: 3px;
        bottom: 3px;
        background-color: #fff;
        transition: transform .2s ease;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .35);
    }
    .toggle-switch input:checked + .toggle-slider { background-color: #1E3A8A; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(17px); }
    .toggle-switch input:focus + .toggle-slider { box-shadow: 0 0 0 3px rgba(30, 58, 138, .25); }
</style>

<?php
require __DIR__ . '/cfr_functions.php';


// Default From/To = hari ini (bukan awal bulan) - dan laporan langsung tampil
// begitu halaman pertama kali dibuka (belum klik Search), pakai tanggal hari
// ini itu sebagai filter awal.
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$end_date   = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? date('Y-m-d', strtotime($_POST['end_date'])) : date('Y-m-d');
$doSearch   = true;

// Filter kolom "REALISATION BY BANK" - pilih akun mana yang mau ditampilkan
// (minimal 1, dipaksa lewat JS di form). Kosong (belum pernah submit/pertama
// buka halaman) berarti tampilkan SEMUA akun - lihat cfrComputeReportData().
$cfrAllAccounts = cfrGetAllAccounts($conn2);
$cfrSelectedAccounts = isset($_POST['accounts']) ? (array) $_POST['accounts'] : [];
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
                    <div class="col-md-3">
                        <label class="form-label"><b>Account</b></label>
                        <!-- Filter kolom "REALISATION BY BANK" - dropdown berisi kartu toggle
                             switch (gaya sama seperti "Manage Role" di userrole.php), bukan
                             checkbox Bootstrap biasa. Minimal 1 akun harus tetap ON - dipaksa
                             lewat JS di bawah (lihat #cfrAccountDropdownMenu). -->
                        <div class="dropdown">
                            <button type="button" class="btn btn-outline-secondary btn-sm form-control text-start dropdown-toggle" id="cfrAccountDropdownBtn" data-toggle="dropdown" aria-expanded="false">
                                All Accounts
                            </button>
                            <div class="dropdown-menu p-2" id="cfrAccountDropdownMenu">
                                <div class="cfr-acc-row cfr-acc-all-row">
                                    <span class="cfr-acc-label">ALL ACCOUNTS</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="cfr-acc-all" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="cfr-acc-panel">
                                    <?php foreach ($cfrAllAccounts as $acc) {
                                        $accId = 'cfr-acc-' . md5($acc['account']);
                                        $checked = empty($cfrSelectedAccounts) || in_array($acc['account'], $cfrSelectedAccounts, true);
                                    ?>
                                    <div class="cfr-acc-row">
                                        <span class="cfr-acc-label"><?= htmlspecialchars($acc['label']); ?></span>
                                        <label class="toggle-switch">
                                            <input type="checkbox" class="cfr-acc-item" name="accounts[]" value="<?= htmlspecialchars($acc['account']); ?>" id="<?= $accId; ?>"<?= $checked ? ' checked' : ''; ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
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
            <?php if (!$doSearch) { ?>
                <p class="text-muted mb-0">Pilih periode lalu klik Search untuk menampilkan laporan.</p>
            <?php } else {

            // =========================
            // HITUNG SEMUA DATA REPORT (akun, saldo awal/akhir, kategori,
            // realisasi, penerimaan/pelunasan pinjaman bank dedicated)
            // =========================
            extract(cfrComputeReportData($conn2, $start_date, $end_date, $cfrSelectedAccounts));
            ?>

            <!-- Judul laporan (nama PT/periode) sengaja DILUAR
                 #cfr-table-wrapper (bukan di dalamnya) supaya tidak ikut
                 kepotong saat tabel di-scroll ke samping - judul ini statis,
                 tidak perlu freeze karena memang tidak pernah scroll. -->
            <div class="mb-3">
                <div><b>PT NIRWANA ALABARE GARMENT</b></div>
                <div><b>CASH FLOW REALISATION</b></div>
                <div>FOR PERIOD <?= strtoupper(date('d M Y', strtotime($start_date))); ?> - <?= strtoupper(date('d M Y', strtotime($end_date))); ?></div>
            </div>

            <div class="table-responsive" id="cfr-table-wrapper">
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
                        <!-- Baris "SALDO AWAL" sengaja DIPINDAH ke dalam <thead> (bukan baris
                             <tbody> biasa yang "dipaksa" ikut sticky) - lebih stabil karena
                             perilaku sticky untuk baris thead jauh lebih konsisten di semua
                             browser dibanding baris tbody yang top-nya dihitung manual lewat JS. -->
                        <tr class="grand-row" id="cfr-saldo-awal-row">
                            <td>SALDO AWAL KAS DAN BANK</td>
                            <td class="num">-</td>
                            <td class="num"><?= cfrFmt($totalBegin); ?></td>
                            <td class="num">-</td>
                            <?php foreach ($accounts as $acc) { ?>
                                <td class="num"><?= cfrFmt($beginBalance[$acc['account']]); ?></td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
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
            </div>
            <?php } ?>
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

    // ====== FILTER AKUN "REALISATION BY BANK" (dropdown checkbox) ======
    // Klik "ALL" nyentang/lepas semua sekaligus; klik salah satu akun otomatis
    // sinkron balik status "ALL" (tercentang penuh/indeterminate). Minimal 1
    // akun harus tetap tercentang - kalau user coba lepas centang terakhir,
    // dibatalkan (balik tercentang).
    (function () {
        var allCheckbox = document.getElementById('cfr-acc-all');
        var itemCheckboxes = Array.prototype.slice.call(document.querySelectorAll('.cfr-acc-item'));
        var dropdownBtn = document.getElementById('cfrAccountDropdownBtn');
        var dropdownMenu = document.getElementById('cfrAccountDropdownMenu');
        if (!allCheckbox || !itemCheckboxes.length || !dropdownBtn || !dropdownMenu) return;

        function updateSummary() {
            var checked = itemCheckboxes.filter(function (cb) { return cb.checked; });
            if (checked.length === itemCheckboxes.length) {
                dropdownBtn.textContent = 'All Accounts';
            } else if (checked.length === 1) {
                dropdownBtn.textContent = checked[0].closest('.cfr-acc-row').querySelector('.cfr-acc-label').textContent;
            } else {
                dropdownBtn.textContent = checked.length + ' Accounts Selected';
            }
        }

        function updateAllCheckboxState() {
            var checkedCount = itemCheckboxes.filter(function (cb) { return cb.checked; }).length;
            allCheckbox.checked = checkedCount === itemCheckboxes.length;
            allCheckbox.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
        }

        allCheckbox.addEventListener('change', function () {
            itemCheckboxes.forEach(function (cb) { cb.checked = allCheckbox.checked; });
            if (!allCheckbox.checked) {
                // "ALL" tidak boleh sampai bikin semua ke-uncheck - minimal 1
                // akun harus tetap tercentang.
                itemCheckboxes[0].checked = true;
                allCheckbox.indeterminate = true;
            }
            updateSummary();
        });

        itemCheckboxes.forEach(function (cb) {
            cb.addEventListener('change', function () {
                var checkedCount = itemCheckboxes.filter(function (c) { return c.checked; }).length;
                if (checkedCount === 0) {
                    cb.checked = true; // batalkan uncheck terakhir
                    return;
                }
                updateAllCheckboxState();
                updateSummary();
            });
        });

        // Klik checkbox/label di dalam dropdown tidak boleh menutup dropdown-nya -
        // biar bisa centang beberapa akun sekaligus tanpa dropdown ketutup tiap klik.
        dropdownMenu.addEventListener('click', function (e) { e.stopPropagation(); });

        updateSummary();
        updateAllCheckboxState();
    })();

    function cfrSelectedAccounts() {
        return Array.prototype.slice.call(document.querySelectorAll('.cfr-acc-item:checked')).map(function (cb) { return cb.value; });
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

        let params = new URLSearchParams({ start_date: sd, end_date: ed });
        cfrSelectedAccounts().forEach(function (a) { params.append('accounts[]', a); });

        fetch(`ekspor_cashflow_realisation.php?${params.toString()}`)
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

    // Baris ke-2 (nama bank) & ke-3 (SALDO AWAL) di thead numpuk berurutan di
    // bawah baris sebelumnya - tinggi tiap baris dihitung dari hasil render
    // (bukan di-hardcode) supaya tetap presisi walau ukuran font berubah, lalu
    // di-set sebagai inline style top per sel (paling reliable, menang dari
    // aturan CSS manapun tanpa tergantung urutan/spesifisitas selector).
    (function () {
        var table = document.getElementById('cfr-table');
        if (!table) return;
        var row1 = table.querySelector('thead tr:first-child');
        var row2 = table.querySelector('thead tr.account-header-row');
        var row3 = table.querySelector('thead tr#cfr-saldo-awal-row');
        if (!row1 || !row2 || !row3) return;

        function setRowTop(row, top) {
            for (var i = 0; i < row.children.length; i++) {
                row.children[i].style.top = top + 'px';
            }
        }

        function updateStickyOffsets() {
            var h1 = row1.getBoundingClientRect().height;
            var h2 = row2.getBoundingClientRect().height;
            setRowTop(row2, h1);
            setRowTop(row3, h1 + h2);
        }

        updateStickyOffsets();
        window.addEventListener('resize', updateStickyOffsets);
    })();

    // Bungkus teks baris judul section (CASH RECEIPTS, 1. OPERATING
    // ACTIVITIES, dst - sel <td colspan> yang melebar sepanjang baris)
    // dengan <span> supaya bisa di-sticky lewat CSS (lihat aturan
    // #cfr-table td[colspan] > span.cfr-sticky-label) - tetap kelihatan di
    // tepi kiri area yang keliatan walau baris itu discroll ke samping.
    (function () {
        var table = document.getElementById('cfr-table');
        if (!table) return;
        var cells = table.querySelectorAll('td[colspan]');
        for (var i = 0; i < cells.length; i++) {
            var td = cells[i];
            var span = document.createElement('span');
            span.className = 'cfr-sticky-label';
            span.innerHTML = td.innerHTML;
            td.innerHTML = '';
            td.appendChild(span);
        }
    })();

    // Zebra striping (.cfr-row-alt) + tandai angka negatif (.cfr-negative).
    // Dilakukan di JS (bukan CSS :nth-child) supaya baris spacer/judul
    // section/total/grand tidak ikut kehitung dalam pola selang-seling -
    // yang di-zebra cuma baris data biasa.
    (function () {
        var table = document.getElementById('cfr-table');
        if (!table) return;

        var rows = table.querySelectorAll('tbody tr');
        var alt = false;
        for (var i = 0; i < rows.length; i++) {
            var tr = rows[i];
            if (tr.querySelector('td[colspan]')) continue;
            if (tr.classList.contains('grand-row') || tr.classList.contains('total-row') ||
                tr.classList.contains('disbursement-header')) continue;
            if (alt) tr.classList.add('cfr-row-alt');
            alt = !alt;
        }

        var numCells = table.querySelectorAll('td.num');
        for (var j = 0; j < numCells.length; j++) {
            if (numCells[j].textContent.trim().charAt(0) === '(') {
                numCells[j].classList.add('cfr-negative');
            }
        }
    })();
</script>

</body>
</html>
