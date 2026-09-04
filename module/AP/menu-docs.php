<?php
include '../header.php';

$current_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';

if ($current_user !== 'indro') {
    echo '<div class="container-fluid mt-5 p-4 text-center">
            <i class="fa fa-lock" style="font-size:48px;color:#c0c4cc;"></i>
            <h4 class="mt-3" style="color:#5a6472;">Restricted Page</h4>
            <p style="color:#8a93a2;">You do not have access to this page.</p>
          </div>
          </body></html>';
    exit;
}

// ===== Documentation data =====
// Grouped to mirror the REAL sidebar hierarchy in module/header.php (e.g. the
// "Cash" dropdown → Cash In / Cash Out / Petty Cash In / Petty Cash Out /
// Report Cash). Every real sub-menu under a documented group is listed here
// even before it has a write-up - items without a 'doc' key just render as a
// disabled "belum" placeholder so the sidebar always matches the app's actual
// menu shape. To document another item, just fill in its 'doc' key.
$menuGroups = [
    'cash' => [
        'title' => 'Cash',
        'icon'  => 'fa-money',
        'items' => [
            'cash-in' => [
                'title' => 'Cash In', 'icon' => 'fa-sign-in', 'path' => 'module/AP/cash-in.php',
                'doc' => [
                    'summary' => 'Mencatat uang masuk ke akun Kas Besar (COA 1.01.03) — form satu halaman tanpa tab varian seperti Petty Cash In, dan tanpa fitur edit/reverse.',
                    'purpose' => 'Cash In dipakai untuk transaksi kas MASUK di level "kas besar" (bukan kas kecil/petty cash). Formnya lebih sederhana dari Petty Cash In: satu header (Tipe/Referensi, Dokumen, Profit Center, Deskripsi) + satu grid detail (COA, Cost Center, Buyer, WS, Submitted By, Jumlah) — tidak ada pilihan tab.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Isi form', 'desc' => 'User isi header + grid detail. Bisa lebih dari satu baris detail, masing-masing dengan akun lawan &amp; jumlah sendiri.'],
                        ['title' => 'Simpan → status Draft', 'desc' => 'Nomor dokumen dibuat format <code>RCI/NAG/bulanTahun/urutan</code>. Dua panggilan berurutan: <code>log_create_cashin.php</code> menulis baris ke <code>tbl_log_cash</code> (log sekaligus jembatan nomor dokumen), baris debit ke <code>c_report_pettycash</code>, dan baris jurnal agregat (debit Kas Besar per Profit Center) ke <code>tbl_list_journal</code>; lalu <code>insert_cashin.php</code> jalan sekali per baris detail, menulis ke <code>c_cash_in</code> plus baris credit jurnal lawannya.'],
                        ['title' => 'Approve (halaman terpisah)', 'desc' => 'Dari halaman menu "Approval &rsaquo; Cash In" (bukan tombol di list utama), dokumen Draft dicentang lalu di-approve — status di <code>c_cash_in</code> dan <code>tbl_list_journal</code> jadi <b>Approved</b>.'],
                        ['title' => 'Cancel', 'desc' => 'Dari halaman Approval yang sama, dokumen Draft bisa di-Cancel — baris jurnal diarsipkan ke <code>tbl_list_journal_cancel</code> lalu dihapus dari jurnal aktif, dan baris <code>c_report_pettycash</code> ditandai Cancel.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel (dari Draft)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'c_cash_in', 'actions' => ['insert','update'], 'desc' => 'Tabel gabungan header + detail dalam satu tabel (beda dari Petty Cash In yang punya tabel header terpisah) — satu dokumen = beberapa baris berbagi no_ci yang sama. Insert saat Simpan, Update saat Approve/Cancel.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert','update','delete'], 'desc' => 'Jurnal umum — debit Kas Besar (agregat per Profit Center) lawan credit per baris detail. Insert saat Simpan, Update status saat Approve, Delete (setelah diarsip) saat Cancel.'],
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip baris jurnal saat dokumen dibatalkan.'],
                        ['name' => 'c_report_pettycash', 'actions' => ['insert','update'], 'desc' => 'Buku kas bersama (dipakai juga oleh Petty Cash & Report Cash) — Cash In dicatat sebagai debit ke akun Kas Besar (1.01.03). Insert saat Simpan, Update status saat Cancel.'],
                        ['name' => 'tbl_log_cash', 'actions' => ['insert'], 'desc' => 'Log aktivitas, sekaligus mekanisme serah-terima nomor dokumen antara log_create_cashin.php dan insert_cashin.php.'],
                    ],
                    'tables_read' => [
                        ['name' => 'master_forpay', 'desc' => 'Sumber dropdown Tipe/Referensi (filter ket=4, khusus Cash In).'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Accounts.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center (NAG/NAK).'],
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs harian — diambil, namun praktis tidak digunakan karena mata uangnya selalu IDR.'],
                    ],
                    'notes' => [
                        'Tidak tersedia fitur edit maupun reverse untuk Cash In (berbeda dengan Petty Cash In). Sekali disimpan, dokumen hanya dapat di-Approve atau di-Cancel, tidak dapat diubah lagi.',
                        'Approve hanya mengubah status pada c_cash_in dan tbl_list_journal — baris pada c_report_pettycash tetap berstatus Draft meskipun dokumennya sudah Approved (baru berubah apabila di-Cancel). Hal ini perlu diperhatikan apabila ada laporan yang mengandalkan status pada c_report_pettycash.',
                        'Tidak ada relasi langsung ke Petty Cash In/Out dari Cash In — beda dengan Cash Out yang punya kolom penanda stat_pci.',
                    ],
                ],
            ],
            'cash-out' => [
                'title' => 'Cash Out', 'icon' => 'fa-sign-out', 'path' => 'module/AP/cash-out.php',
                'doc' => [
                    'summary' => 'Mencatat uang keluar dari akun Kas Besar (COA 1.01.03) — struktur & alur sama persis dengan Cash In (cerminannya), plus satu keterkaitan tambahan ke Petty Cash In lewat tipe referensi "Petty Cash In".',
                    'purpose' => 'Cash Out dipakai untuk transaksi kas KELUAR di level kas besar. Salah satu pilihan tipe referensinya adalah "Petty Cash In" — dipakai saat uang ini dikirim khusus untuk mengisi ulang kas kecil, yang nantinya harus "diterima"/ditutup lewat dokumen Petty Cash In (tab Cash Out).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Isi form', 'desc' => 'Sama seperti Cash In: header (Tipe/Referensi dari master_forpay, Dokumen, Profit Center, Deskripsi) + grid detail.'],
                        ['title' => 'Simpan → status Draft', 'desc' => 'Nomor dokumen format <code>RCO/NAG/bulanTahun/urutan</code>. <code>log_create_cashout.php</code> menulis tbl_log_cash + baris credit ke c_report_pettycash (saldo Kas Besar berkurang) + baris jurnal agregat credit; <code>insert_cashout.php</code> per baris detail menulis c_cash_out (kolom stat_pci default "N") + baris debit jurnal lawannya.'],
                        ['title' => 'Approve (halaman terpisah)', 'desc' => 'Sama seperti Cash In — dari halaman "Approval &rsaquo; Cash Out", status jadi <b>Approved</b> di c_cash_out &amp; tbl_list_journal.'],
                        ['title' => 'Cancel', 'desc' => 'Sama seperti Cash In — jurnal diarsipkan lalu dihapus dari jurnal aktif, baris c_report_pettycash ditandai Cancel.'],
                        ['title' => '(Opsional) Diterima melalui Petty Cash In', 'desc' => 'Apabila tipe referensinya "Petty Cash In" dan belum ditandai diterima (stat_pci = "N"), dokumen ini otomatis muncul pada dropdown tab "Cash Out" di form Create Petty Cash In. Setelah dipilih &amp; disimpan di sana, stat_pci berubah menjadi "Y" sehingga dokumen ini tidak akan muncul lagi pada dropdown tersebut.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel (dari Draft)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'c_cash_out', 'actions' => ['insert','update'], 'desc' => 'Tabel gabungan header + detail, serupa dengan c_cash_in, namun memiliki kolom tambahan <b>stat_pci</b> ("N"/"Y") — penanda apakah dokumen ini sudah "diterima" melalui tab Cash Out pada Petty Cash In. Insert saat Simpan, Update saat Approve/Cancel.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert','update','delete'], 'desc' => 'Jurnal umum — credit Kas Besar (agregat per Profit Center) lawan debit per baris detail (kebalikan dari Cash In).'],
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip baris jurnal saat dibatalkan.'],
                        ['name' => 'c_report_pettycash', 'actions' => ['insert','update'], 'desc' => 'Buku kas bersama — Cash Out dicatat sebagai credit ke Kas Besar (mengurangi saldo berjalan).'],
                        ['name' => 'tbl_log_cash', 'actions' => ['insert'], 'desc' => 'Log aktivitas sekaligus jembatan nomor dokumen antar panggilan AJAX (sama seperti Cash In).'],
                    ],
                    'tables_read' => [
                        ['name' => 'master_forpay', 'desc' => 'Sumber dropdown Tipe/Referensi (filter ket=3, khusus Cash Out) — salah satu opsinya adalah "Petty Cash In".'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Accounts.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center.'],
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs harian — praktis tidak digunakan (mata uang selalu IDR).'],
                    ],
                    'notes' => [
                        'Sama seperti Cash In: tidak tersedia fitur edit maupun reverse, dan Approve/Cancel dilakukan dari halaman terpisah "Approval &rsaquo; Cash Out", bukan tombol pada list utama.',
                        'Kolom stat_pci merupakan satu-satunya penghubung nyata antara modul Cash (besar) dan Petty Cash — digunakan oleh petty-in/pettyin_cashout.php dan petty-in/save_pettyin_cashout.php pada sisi Petty Cash In untuk mengetahui dokumen Cash Out mana saja yang masih "terbuka" (belum diterima).',
                        'Sama seperti Cash In, Approve tidak mengubah status di c_report_pettycash — hanya Cancel yang mengubahnya.',
                    ],
                ],
            ],
            'petty-cash-in' => [
                'title' => 'Petty Cash In', 'icon' => 'fa-credit-card', 'path' => 'module/AP/petty-cashin.php',
                'doc' => [
                    'summary' => 'Mencatat uang masuk ke akun Kas Kecil — baik sebagai penerimaan berdiri sendiri, penyelesaian atas kasbon Petty Cash Out, maupun penerimaan yang menutup dokumen Cash Out.',
                    'purpose' => 'Petty Cash In dipakai setiap kali ada uang yang MASUK ke rekening/akun Kas Kecil (akun COA dengan kategori "1.01 Kas Kecil"). Halaman create-nya punya 3 tab, tergantung dari mana asal uang tersebut:',
                    'variants' => [
                        ['name' => 'None',       'icon' => 'fa-file-o',      'desc' => 'Penerimaan kas kecil berdiri sendiri, tidak terkait dokumen lain — misalnya setoran tunai biasa.'],
                        ['name' => 'Settlement', 'icon' => 'fa-handshake-o', 'desc' => 'Menutup/menyelesaikan kasbon (advance) yang sebelumnya dikeluarkan lewat Petty Cash Out — user memilih dokumen Petty Cash Out yang mau diselesaikan, baris detailnya otomatis terisi dari kasbon aslinya.'],
                        ['name' => 'Cash Out',   'icon' => 'fa-exchange',    'desc' => 'Mencatat uang yang diterima untuk menutup/reimburse dokumen Cash Out (dari modul Cash umum) yang sudah ditandai type "Petty Cash In".'],
                    ],
                    'flow' => [
                        ['title' => 'Pilih tab & isi form', 'desc' => 'User pilih salah satu dari 3 tab di atas, isi akun Kas Kecil, jumlah, Kategori Cash Flow (wajib), dan grid detail (COA/Profit Center/Cost Center/Debit/Credit). Untuk Settlement & Cash Out, memilih dokumen referensi otomatis mengisi grid detail dari dokumen asalnya. Total debit header harus sama dengan total credit detail per Profit Center sebelum bisa disimpan.'],
                        ['title' => 'Simpan → status Draft', 'desc' => 'Nomor dokumen baru dibuat (format <code>KKM/kode_kas/tahun/bulan/urutan</code>). Dalam satu transaksi database: header masuk ke <code>c_petty_cashin_h</code>, baris ke buku kas <code>c_report_pettycash</code>, baris detail ke <code>c_petty_cashin_none</code>, dan jurnal double-entry (debit Kas Kecil, credit akun tujuan) ke <code>tbl_list_journal</code> — seluruhnya berstatus <b>Draft</b>. Apabila menggunakan tab Settlement/Cash Out, dokumen sumbernya (<code>c_petty_cashout_h</code> / <code>c_cash_out</code>) ditandai sudah diselesaikan.'],
                        ['title' => 'Edit (khusus status Draft)', 'desc' => 'Selama masih berstatus Draft, dokumen dapat diedit. Sistem otomatis membuat jurnal pembalik (reverse) untuk baris lama, mengarsipkan detail lama ke <code>c_petty_cashin_none_cancel</code>, lalu menulis ulang header/detail/jurnal dengan nilai baru. Apabila akun Kas Kecil diganti (kode_kas berbeda), seluruh nomor dokumen dibuat ulang.'],
                        ['title' => 'Approve', 'desc' => 'Dari halaman terpisah "Approve Petty Cash In", dokumen Draft dicentang lalu di-approve — status header jadi <b>Approved</b> dan baris jurnalnya ikut di-set Approved. Setelah ini dokumen tidak bisa diedit langsung lagi.'],
                        ['title' => 'Cancel (Draft) atau Reverse (Approved)', 'desc' => 'Dokumen Draft bisa langsung di-Cancel (status jadi Cancel, jurnal diarsipkan ke <code>tbl_list_journal_cancel</code> lalu dihapus dari jurnal aktif — final, tidak bisa diubah lagi). Dokumen yang sudah Approved tidak bisa dibatalkan langsung — harus lewat alur "Reverse Petty Cash" (pengajuan pembalik yang perlu di-approve juga) yang akan mengembalikan status dokumen ke Draft supaya bisa dikoreksi.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel (dari Draft)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'c_petty_cashin_h', 'actions' => ['insert','update'], 'desc' => 'Tabel header / dokumen utama. 1 baris = 1 dokumen Petty Cash In (no_pci, tanggal, tipe/reff, akun kas, jumlah, status Draft/Approved/Cancel). Insert saat Simpan, Update saat Edit/Approve/Cancel.'],
                        ['name' => 'c_petty_cashin_none', 'actions' => ['insert','delete'], 'desc' => 'Baris detail alokasi (akun lawan, profit center, cost center, debit/credit) — digunakan oleh ketiga tipe (None/Settlement/Cash Out), bukan hanya tipe "None" meskipun penamaannya demikian. Insert baris baru saat Simpan; saat Edit, baris lama dihapus (setelah diarsip) lalu baris baru di-insert.'],
                        ['name' => 'c_petty_cashin_none_cancel', 'actions' => ['insert'], 'desc' => 'Arsip baris detail lama, tersimpan otomatis setiap kali dokumen Draft diedit — supaya histori versi sebelumnya tidak hilang.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert','update','delete'], 'desc' => 'Jurnal umum (buku besar) perusahaan — setiap Petty Cash In otomatis membentuk entry double-entry di sini (debit Kas Kecil, credit akun tujuan), inilah yang membuat dokumen ini berpengaruh ke laporan keuangan.'],
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip baris jurnal yang dihapus saat dokumen dibatalkan (Cancel).'],
                        ['name' => 'c_report_pettycash', 'actions' => ['insert','update'], 'desc' => 'Buku kas / ledger kas kecil (dipakai bersama Petty Cash Out) — sumber laporan saldo kas kecil berjalan.'],
                        ['name' => 'c_petty_cashout_h', 'actions' => ['update'], 'desc' => 'Ditandai "settlement = Y" apabila dokumen ini menyelesaikan kasbon dari tab Settlement — Petty Cash In tidak pernah membuat baris baru di tabel ini, hanya memperbarui kolom settlement pada dokumen Petty Cash Out yang sudah ada.'],
                        ['name' => 'c_cash_out', 'actions' => ['update'], 'desc' => 'Ditandai "sudah diterima" (stat_pci = "Y") apabila dokumen ini menutup dokumen Cash Out dari tab Cash Out — hanya memperbarui, tidak pernah membuat baris baru.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Accounts — sumber dropdown akun Kas Kecil dan akun lawan di grid detail.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center (NAG/NAK).'],
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center, muncul bertingkat mengikuti Profit Center yang dipilih.'],
                        ['name' => 'master_cash_flow', 'desc' => 'Master Kategori Cash Flow — wajib dipilih tiap transaksi, dipakai untuk laporan arus kas.'],
                        ['name' => 'master_forpay', 'desc' => 'Master referensi/tujuan pembayaran, dipakai bersama beberapa modul kas lain (untuk filter "Reference" di halaman list).'],
                        ['name' => 'b_saldoawal_pettycash', 'desc' => 'Saldo awal per akun Kas Kecil, jadi titik awal perhitungan saldo berjalan di laporan.'],
                        ['name' => 'c_petty_cashout_none', 'desc' => 'Baris detail kasbon Petty Cash Out asli — dipakai untuk mengisi otomatis grid detail di tab Settlement.'],
                    ],
                    'notes' => [
                        'Pada folder module/AP/ terdapat berkas-berkas lama (create-petty-cashin.php, insert_petty_in_h.php, insert_petty_in_det.php, insert_petty_in_none.php) yang sudah tidak digunakan lagi oleh halaman list saat ini — masih tersimpan di server, namun bukan bagian dari alur yang aktif.',
                        'Kolom "balance" pada c_report_pettycash saat ini diisi langsung dari nominal transaksinya saja, bukan hasil perhitungan saldo kumulatif berjalan (berbeda dengan versi lama insert_petty_in_h.php yang sempat menghitung saldo kumulatif) — perlu diperiksa kembali apabila laporan saldo kas kecil terlihat tidak akumulatif.',
                        'Tabel ap_reverse_h/ap_reverse_det TIDAK disentuh langsung oleh menu Petty Cash In ini — tabel tersebut milik alur "Reverse Petty Cash" terpisah, yang membaca status dokumen di sini lalu menuliskannya kembali ke Draft setelah pengajuan reversal disetujui.',
                    ],
                ],
            ],
            'petty-cash-out' => [
                'title' => 'Petty Cash Out', 'icon' => 'fa-credit-card', 'path' => 'module/AP/petty-cashout.php',
                'doc' => [
                    'summary' => 'Mencatat uang keluar dari akun Kas Kecil — merupakan cerminan dari Petty Cash In, namun memiliki lebih banyak tab: None, Advance (kasbon), Settlement (menutup kasbon), dan Payment Voucher (membayar tagihan AP menggunakan kas kecil).',
                    'purpose' => 'Petty Cash Out dipakai setiap kali ada uang yang KELUAR dari akun Kas Kecil. Halaman create-nya punya 4 tab aktif (dulu ada 5, tab "List Payment" sekarang disembunyikan/diganti Payment Voucher):',
                    'variants' => [
                        ['name' => 'None',            'icon' => 'fa-file-o',           'desc' => 'Pengeluaran kas kecil berdiri sendiri, tidak terkait dokumen lain.'],
                        ['name' => 'Advance',         'icon' => 'fa-hand-o-right',     'desc' => 'Kasbon — uang keluar sebagai "titipan/panjar" ke karyawan/pihak lain. Statusnya tetap "terbuka" sampai ada dokumen Settlement yang menutupnya.'],
                        ['name' => 'Settlement',      'icon' => 'fa-handshake-o',      'desc' => 'Menutup kasbon (Advance) yang masih terbuka — digunakan apabila ternyata dibutuhkan tambahan pembayaran terkait kasbon yang sama (jumlah sebelumnya kurang, sehingga dibayarkan kembali melalui Petty Cash Out, bukan hanya dikembalikan melalui Petty Cash In).'],
                        ['name' => 'Payment Voucher', 'icon' => 'fa-file-text-o',      'desc' => 'Membayar tagihan AP (kontrabon — Regular/Installment/DP/CBD/Saldo Awal) pakai kas kecil, plus baris penyesuaian manual (misalnya PPh).'],
                    ],
                    'flow' => [
                        ['title' => 'Pilih tab & isi form', 'desc' => 'None/Advance: isi header + grid detail biasa. Settlement: pilih dokumen Advance yang masih terbuka (belum ditutup) dari dropdown. Payment Voucher: cari & centang tagihan AP yang mau dibayar, plus opsional baris penyesuaian manual.'],
                        ['title' => 'Simpan → status Draft', 'desc' => 'Nomor dokumen dibuat format <code>KKK/kode_kas/tahun/bulan/urutan</code> ("KKK" = Kas Kecil Keluar, beda dari Petty Cash In yang pakai "KKM"). Header masuk ke <code>c_petty_cashout_h</code>, baris ke buku kas <code>c_report_pettycash</code>, detail ke <code>c_petty_cashin_none</code> (tab None/Advance/Settlement) atau <code>c_petty_cashout_det</code> + <code>c_petty_cashout_adj_det</code> (tab Payment Voucher), dan jurnal double-entry ke <code>tbl_list_journal</code> — semua Draft. Tab Settlement juga langsung menandai <code>c_petty_cashout_h.settlement = "Y"</code> pada dokumen Advance yang ditutup.'],
                        ['title' => 'Edit (khusus status Draft)', 'desc' => 'Sama seperti Petty Cash In: jurnal lama ditandai "Updated" + dibuatkan jurnal pembalik, detail lama diarsipkan ke <code>c_petty_cashout_none_cancel</code>, lalu header/detail/jurnal ditulis ulang. Ganti akun Kas Kecil = seluruh nomor dokumen di-generate ulang.'],
                        ['title' => 'Approve', 'desc' => 'Dari halaman terpisah "Approve Petty Cash Out", dokumen Draft dicentang lalu di-approve — status jadi <b>Approved</b> di header &amp; jurnal.'],
                        ['title' => 'Cancel (Draft) atau Reverse (Approved)', 'desc' => 'Draft bisa langsung di-Cancel (final). Dokumen Approved harus lewat alur "Reverse Petty Cash" yang sama dengan Petty Cash In (pengajuan reversal yang perlu di-approve dulu) untuk dikembalikan ke status Draft supaya bisa dikoreksi.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel (dari Draft)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'c_petty_cashout_h', 'actions' => ['insert','update'], 'desc' => 'Tabel header / dokumen utama. Kolom "settlement" (NULL/"Y") khusus dipakai untuk melacak apakah suatu dokumen Advance sudah ditutup atau belum. Insert saat Simpan, Update saat Edit/Approve/Cancel/Settlement.'],
                        ['name' => 'c_petty_cashin_none', 'actions' => ['insert','delete'], 'desc' => 'Baris detail alokasi — digunakan bersama oleh tab None, Advance, dan Settlement (nama tabelnya "cashin", namun tabel detail ini digunakan bersama untuk kedua arah, baik in maupun out).'],
                        ['name' => 'c_petty_cashout_none_cancel', 'actions' => ['insert'], 'desc' => 'Arsip baris detail lama saat dokumen Draft diedit.'],
                        ['name' => 'c_petty_cashout_det', 'actions' => ['insert'], 'desc' => 'Baris detail khusus tab Payment Voucher — 1 baris per tagihan AP (kontrabon) yang dibayar.'],
                        ['name' => 'c_petty_cashout_adj_det', 'actions' => ['insert'], 'desc' => 'Baris penyesuaian manual (adjustment) pada tab Payment Voucher, misalnya untuk PPh atau pembulatan.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert','update','delete'], 'desc' => 'Jurnal umum — credit Kas Kecil lawan debit akun/tagihan yang dibayar.'],
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip baris jurnal saat dokumen dibatalkan.'],
                        ['name' => 'c_report_pettycash', 'actions' => ['insert','update'], 'desc' => 'Buku kas kecil bersama (dipakai juga oleh Petty Cash In) — dicatat sebagai credit (saldo berkurang).'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Accounts — sumber dropdown akun Kas Kecil.'],
                        ['name' => 'master_cash_flow', 'desc' => 'Master Kategori Cash Flow, difilter ke tipe "Cash Out".'],
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier — sumber dropdown "Supplier/Tujuan", digabung dengan daftar Cost Center aktif.'],
                        ['name' => 'mtax', 'desc' => 'Master Pajak — dipakai tab Payment Voucher untuk menentukan akun PPh dari tagihan yang dibayar.'],
                        ['name' => 'kontrabon', 'desc' => 'Tabel induk tagihan AP (kontrabon) yang dicari & dibayar lewat tab Payment Voucher.'],
                        ['name' => 'kontrabon_h', 'desc' => 'Header Kontrabon tipe Regular/Installment.'],
                        ['name' => 'kontrabon_h_dp', 'desc' => 'Header Kontrabon tipe Down Payment (DP).'],
                        ['name' => 'kontrabon_h_cbd', 'desc' => 'Header Kontrabon tipe Cash Before Delivery (CBD).'],
                        ['name' => 'kontrabon_h_installment_detail', 'desc' => 'Detail cicilan untuk Kontrabon tipe Installment.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center, ditampilkan di PDF voucher.'],
                    ],
                    'notes' => [
                        'Tab "List Payment" masih terdapat pada kode (petty-out/pettyout_lp.php), namun tombolnya sudah disembunyikan pada halaman Create — telah digantikan oleh tab "Payment Voucher". Dokumen List Payment yang lama masih dapat diedit melalui form_edit_pettycash_out_lp.php, namun tidak dapat dibuat baru lagi.',
                        'Catatan penting: dokumen Advance dapat ditutup (settlement="Y") dari DUA arah — melalui tab Settlement pada Petty Cash In (uang kembali MASUK) ATAU tab Settlement pada Petty Cash Out ini sendiri (uang tambahan keluar kembali). Siapa pun yang lebih dahulu mengirimkan dokumen Settlement, dialah yang menutup Advance tersebut — sehingga Advance tidak "dimiliki" oleh satu arah saja.',
                        'Terdapat berkas-berkas lama yang sudah tidak digunakan: create-petty-cashout.php beserta insert_petty_cashout_h/det/none.php (form lama, berbeda dari create-pettycash-out.php yang aktif saat ini), dan update_pco_none.php (handler edit yang sudah tidak dipanggil, telah digantikan oleh petty-out/update_pettyout_none.php).',
                        'Tabel ap_reverse_h/ap_reverse_det TIDAK disentuh langsung oleh menu Petty Cash Out ini — sama seperti pada Petty Cash In, tabel tersebut milik alur "Reverse Petty Cash" terpisah.',
                        'Nomor dokumen pakai prefix "KKK" (Kas Kecil Keluar) — beda dengan Petty Cash In yang pakai "KKM" (Kas Kecil Masuk).',
                    ],
                ],
            ],
            'report-cash' => [
                'title' => 'Report Cash', 'icon' => 'fa-file-excel-o', 'path' => 'module/AP/cashreport.php',
                'doc' => [
                    'summary' => 'Laporan saldo berjalan (running balance) untuk satu akun kas — mencakup Kas Kecil Pabrik, Kas Kecil Kantor, maupun Kas Besar dalam satu format laporan yang sama, karena ketiganya menulis ke buku besar kas yang sama.',
                    'purpose' => 'Report Cash dipakai untuk melihat mutasi dan saldo berjalan satu akun kas dalam periode tertentu. Laporan ini murni baca data (tidak ada input/simpan) dan dipakai bersama oleh 4 menu lain di grup Cash — transaksi dari Cash In, Cash Out, Petty Cash In, maupun Petty Cash Out semuanya bermuara ke laporan yang sama, tinggal beda akun yang dipilih:',
                    'variants' => [
                        ['name' => 'Kas Kecil Pabrik', 'icon' => 'fa-industry', 'desc' => 'Akun COA 1.01.01 — diisi oleh transaksi Petty Cash In / Petty Cash Out.'],
                        ['name' => 'Kas Kecil Kantor', 'icon' => 'fa-building-o', 'desc' => 'Akun COA 1.01.02 — diisi oleh transaksi Petty Cash In / Petty Cash Out.'],
                        ['name' => 'Kas Besar', 'icon' => 'fa-money', 'desc' => 'Akun COA 1.01.03 — diisi oleh transaksi Cash In / Cash Out biasa (bukan petty cash).'],
                    ],
                    'flow' => [
                        ['title' => 'Pilih akun & rentang tanggal', 'desc' => 'User pilih salah satu dari 3 akun kas di atas (dropdown-nya otomatis ambil semua COA yang nomornya diawali <code>1.01</code>) plus tanggal awal/akhir, lalu submit form (halaman ini reload biasa, bukan AJAX).'],
                        ['title' => 'Hitung Saldo Awal', 'desc' => 'Saldo awal diambil dari <code>b_saldoawal_pettycash</code> untuk akun terpilih, lalu "digulung maju" (rolled forward) melewati semua transaksi SEBELUM tanggal awal filter yang berstatus bukan Cancel, pakai variabel session MySQL (<code>@runtot</code>) sebagai saldo berjalan.'],
                        ['title' => 'Tampilkan baris transaksi', 'desc' => 'Setiap baris di <code>c_report_pettycash</code> dalam rentang tanggal (status ≠ Cancel) ditampilkan: tanggal, no dokumen/jurnal, deskripsi, debit, credit, dan saldo berjalan setelah baris itu.'],
                        ['title' => 'Saldo Akhir & Export', 'desc' => 'Baris terakhir menunjukkan saldo akhir periode. Tombol "Excel" (report_idrcash.php) menjalankan ulang query & perhitungan yang persis sama secara terpisah, supaya hasil export selalu konsisten dengan tampilan di layar.'],
                    ],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'c_report_pettycash', 'desc' => 'Sumber utama baris transaksi. Namanya "pettycash", namun sebenarnya digunakan bersama oleh SELURUH transaksi kas (Cash In/Out juga ditulis ke sini melalui log_create_cashin.php/log_create_cashout.php dengan akun di-hardcode ke 1.01.03), bukan eksklusif untuk Petty Cash.'],
                        ['name' => 'b_saldoawal_pettycash', 'desc' => 'Saldo awal per akun kas (nominal awal sebelum ada transaksi tercatat) — juga dipakai bersama, bukan eksklusif Petty Cash walau namanya begitu.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Sumber dropdown akun kas, difilter ke COA yang nomornya diawali "1.01".'],
                        ['name' => 'c_petty_cashin_h', 'desc' => 'Hanya dipakai untuk ambil kode mata uang (curr) akun terpilih, ditampilkan di header laporan.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs untuk kolom "Balance Eq IDR" apabila akunnya bukan IDR — dalam praktiknya kolom ini hampir tidak pernah aktif karena dropdown akun selalu mengirim curr=IDR (lihat catatan teknis).'],
                    ],
                    'notes' => [
                        'Kolom "Category" pada tabel laporan selalu kosong — laporan ini tidak membedakan baris mana yang berasal dari Cash In, Cash Out, Petty Cash In, atau Petty Cash Out. Satu-satunya cara mengetahui jenis transaksinya adalah dari prefix nomor dokumen atau teks deskripsinya.',
                        'Kolom "Balance Eq IDR" (saldo dalam ekuivalen IDR untuk mata uang asing) terdapat pada markup, namun praktis tidak pernah terisi, karena query dropdown akun selalu mengembalikan curr=IDR untuk ketiga akun kas — sehingga cabang logika mata uang asing tersebut tidak pernah digunakan.',
                        'Tidak terdapat filter Profit Center / Divisi pada laporan ini, karena tabel c_report_pettycash tidak menyimpan kolom profit center (berbeda dengan tbl_list_journal yang menyimpannya).',
                    ],
                ],
            ],
        ],
    ],
    'bank' => [
        'title' => 'Bank',
        'icon'  => 'fa-university',
        'items' => [
            'bank-in' => [
                'title' => 'Bank In', 'icon' => 'fa-sign-in', 'path' => 'module/AP/bank-in.php',
                'doc' => [
                    'summary' => 'Mencatat uang masuk ke rekening bank, dengan 3 tab sumber: AR Collection, dari dokumen Bank Out yang sudah disetujui, atau entri bebas (None).',
                    'purpose' => 'Bank In digunakan untuk mencatat penerimaan uang ke rekening bank perusahaan. Terdapat 3 tab pada halaman pembuatannya, tergantung sumber penerimaannya:',
                    'variants' => [
                        ['name' => 'AR Collection', 'icon' => 'fa-users', 'desc' => 'Penerimaan pelunasan piutang dari pelanggan (Account Receivable).'],
                        ['name' => 'Bank Out', 'icon' => 'fa-exchange', 'desc' => 'Diajukan terhadap dokumen Bank Out yang sudah berstatus Approved dan belum ditautkan (pola transfer/reimbursement antar rekening internal).'],
                        ['name' => 'None', 'icon' => 'fa-file-o', 'desc' => 'Entri jurnal manual bebas, tidak terkait dokumen lain.'],
                    ],
                    'flow' => [
                        ['title' => 'Pilih tab & isi form', 'desc' => 'Pengguna memilih salah satu dari 3 tab, mengisi rekening bank tujuan, jumlah, dan grid detail. Untuk tab Bank Out, dokumen referensinya dipilih dari daftar Bank Out yang sudah Approved dan belum ditautkan.'],
                        ['title' => 'Simpan → status Draft', 'desc' => 'Nomor dokumen dibuat dengan format <code>BM/kode_bank/profit_center/bulanTahun/urutan</code> ("BM" = Bank Masuk). Dalam satu transaksi database: header masuk ke <code>tbl_bankin_arcollection</code> (nama tabel legacy, tetap dipakai untuk ketiga tab), satu baris debit ke <code>b_reportbank</code>, baris detail ke <code>tbl_bankin</code> (tab None) atau <code>b_bankin_none</code> (tab Bank Out), dan jurnal ke <code>tbl_list_journal</code> — seluruhnya berstatus Draft.'],
                        ['title' => 'Edit (khusus status Draft)', 'desc' => 'Hanya dapat diedit selama masih Draft, dan hanya apabila tanggal dokumennya berada pada periode akuntansi yang masih terbuka. Jurnal lama ditandai "Updated" dan dibuatkan jurnal pembalik, lalu header/detail/jurnal ditulis ulang dengan nilai baru. Apabila rekening bank diganti, nomor dokumen dibuat ulang.'],
                        ['title' => 'Approve', 'desc' => 'Dari halaman terpisah "Approval &rsaquo; Incoming Bank", dokumen Draft disetujui — status pada <code>tbl_bankin_arcollection</code> dan <code>tbl_list_journal</code> menjadi Approved. Baris pada <code>b_reportbank</code> TIDAK ikut berubah status saat Approve — tetap Draft sampai dokumennya dibatalkan.'],
                        ['title' => 'Cancel atau Reverse', 'desc' => 'Dokumen Draft dapat langsung dibatalkan (jurnal diarsipkan lalu dihapus dari jurnal aktif, baris buku bank ditandai Cancel). Dokumen yang sudah Approved hanya dapat dibuka kembali melalui alur "Reverse Bank" terpisah (permohonan pembalik yang perlu disetujui juga), yang berlaku sama untuk Bank In maupun Bank Out.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel (dari Draft)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'tbl_bankin_arcollection', 'actions' => ['insert','update'], 'desc' => 'Tabel header untuk SELURUH dokumen Bank In dari ketiga tab (nama tabelnya berasal dari masa ketika hanya tab AR Collection yang ada). Kolom <code>ref_data</code> menjadi penanda tab asal (AR Collection/Bank Keluar/None). Insert saat Simpan, Update saat Edit/Approve/Cancel.'],
                        ['name' => 'tbl_bankin', 'actions' => ['insert'], 'desc' => 'Baris detail untuk tab "None" — grid jurnal manual.'],
                        ['name' => 'b_bankin_none', 'actions' => ['insert'], 'desc' => 'Baris detail untuk tab "Bank Out" — referensi ke dokumen Bank Out sumbernya.'],
                        ['name' => 'b_reportbank', 'actions' => ['insert','update'], 'desc' => 'Buku besar/ledger bank bersama (dipakai juga oleh Bank Out dan Payment Voucher) — Bank In dicatat sebagai debit. Insert saat Simpan, Update status saat Cancel.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert','update','delete'], 'desc' => 'Jurnal umum perusahaan. Untuk transaksi lintas Profit Center turut dibuat baris tambahan ke akun Utang/Piutang Antar Divisi; untuk dokumen dari tab Bank Out yang nilainya berbeda karena kurs, turut dibuat baris Selisih Kurs.'],
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip baris jurnal saat dokumen dibatalkan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'b_masterbank', 'desc' => 'Master rekening bank — sumber dropdown rekening, mata uang, dan kode bank untuk penomoran dokumen.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center, juga sumber kode bank (b_code) untuk penomoran dokumen.'],
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center.'],
                        ['name' => 'master_cash_flow', 'desc' => 'Master Kategori Cash Flow.'],
                        ['name' => 'ap_masterrate', 'desc' => 'Kurs pajak (PAJAK) — diambil otomatis apabila rekening bukan IDR, untuk menghitung nilai ekuivalen IDR.'],
                        ['name' => 'tbl_closing_periode', 'desc' => 'Status buka/tutup periode akuntansi, dicek untuk menentukan apakah dokumen masih boleh diedit.'],
                    ],
                    'notes' => [
                        'Rekening bank dapat bermata uang asing (bukan hanya IDR). Apabila mata uangnya bukan IDR, kurs diambil otomatis dari ap_masterrate pada tanggal dokumen, dan nilai ekuivalen IDR dihitung otomatis.',
                        'Banyak berkas lama (create-bank-in.php, create-bank-in2.php, insert_bankin.php, dan sejenisnya) serta beberapa folder cadangan (BAK, Old, New folder, dst.) masih ada di server namun sudah tidak digunakan oleh alur yang aktif saat ini.',
                        'Approve tidak mengubah status pada b_reportbank — hanya Cancel yang mengubahnya. Laporan yang membaca status pada tabel tersebut perlu memperhitungkan hal ini.',
                    ],
                ],
            ],
            'bank-out' => [
                'title' => 'Bank Out', 'icon' => 'fa-sign-out', 'path' => 'module/AP/bank-out.php',
                'doc' => [
                    'summary' => 'Mencatat uang keluar dari rekening bank — dua tab aktif (Payment Voucher dan None), plus fitur unggah lampiran PDF bukti transfer yang tidak dimiliki Bank In.',
                    'purpose' => 'Bank Out digunakan untuk mencatat pengeluaran uang dari rekening bank perusahaan, baik untuk membayar Payment Voucher/tagihan AP yang sudah disetujui maupun sebagai entri jurnal manual.',
                    'variants' => [
                        ['name' => 'Payment Voucher', 'icon' => 'fa-money', 'desc' => 'Memilih satu atau beberapa Payment Voucher/dokumen AP (Kontrabon) yang sudah Approved untuk dibayar — sistem menghitung sisa tagihan yang belum dibayar (mendukung pembayaran sebagian/partial).'],
                        ['name' => 'None', 'icon' => 'fa-file-o', 'desc' => 'Entri jurnal manual bebas, tidak terkait dokumen lain.'],
                    ],
                    'flow' => [
                        ['title' => 'Pilih tab & isi form', 'desc' => 'Tab Payment Voucher: mencari &amp; memilih dokumen AP yang sudah Approved untuk dibayar, plus opsional baris penyesuaian manual. Tab None: mengisi grid jurnal manual langsung.'],
                        ['title' => 'Simpan → status Draft', 'desc' => 'Nomor dokumen format <code>BK/kode_bank/profit_center/bulanTahun/urutan</code> ("BK" = Bank Keluar). Header masuk ke <code>b_bankout_h</code>, satu baris credit ke <code>b_reportbank</code>, detail ke <code>b_bankout_det</code> (+ <code>b_bankout_adj_det</code> untuk penyesuaian manual pada tab Payment Voucher) atau <code>b_bankout_none</code> (tab None), dan jurnal ke <code>tbl_list_journal</code> — seluruhnya Draft.'],
                        ['title' => 'Edit (khusus status Draft)', 'desc' => 'Pola yang sama seperti Bank In: jurnal lama ditandai "Updated" dan dibuatkan jurnal pembalik, lalu ditulis ulang. Perlu dicatat bahwa pengecekan periode akuntansi terbuka pada Bank Out saat ini tidak berfungsi sebagaimana mestinya (nilainya di-hardcode "Open" pada sisi tampilan).'],
                        ['title' => 'Approve', 'desc' => 'Dari halaman terpisah "Approval &rsaquo; Outgoing Bank", status pada <code>b_bankout_h</code> dan <code>tbl_list_journal</code> menjadi Approved. Sama seperti Bank In, baris pada <code>b_reportbank</code> tidak ikut berubah status.'],
                        ['title' => 'Cancel atau Reverse', 'desc' => 'Pembatalan dokumen Draft juga mengembalikan status dokumen AP yang sempat dibayar (outstanding dikembalikan, tautan Payment List/memo dilepas). Dokumen Approved hanya dapat dibuka kembali lewat alur "Reverse Bank" (sama dengan Bank In).'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel (dari Draft)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'b_bankout_h', 'actions' => ['insert','update'], 'desc' => 'Tabel header untuk seluruh dokumen Bank Out. Kolom <code>stat_bi</code> menandai apakah sudah ada Bank In yang diajukan terhadap dokumen ini (lihat tab "Bank Out" pada Bank In). Insert saat Simpan, Update saat Edit/Approve/Cancel.'],
                        ['name' => 'b_bankout_det', 'actions' => ['insert'], 'desc' => 'Baris detail tab Payment Voucher — satu baris per dokumen AP yang dibayar, mendukung pembayaran sebagian (kolom for_balance/eqv_idr).'],
                        ['name' => 'b_bankout_adj_det', 'actions' => ['insert'], 'desc' => 'Baris penyesuaian manual tambahan pada tab Payment Voucher (misalnya untuk PPh atau pembulatan).'],
                        ['name' => 'b_bankout_none', 'actions' => ['insert'], 'desc' => 'Baris detail untuk tab "None" — grid jurnal manual.'],
                        ['name' => 'b_bankout_dok', 'actions' => ['insert','delete'], 'desc' => 'Lampiran PDF (bukti transfer, dsb.) yang diunggah untuk dokumen ini — fitur yang tidak dimiliki Bank In. Dapat ditambah maupun dihapus dari halaman list.'],
                        ['name' => 'b_reportbank', 'actions' => ['insert','update'], 'desc' => 'Buku besar/ledger bank bersama — Bank Out dicatat sebagai credit (saldo berkurang).'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert','update','delete'], 'desc' => 'Jurnal umum, sama seperti Bank In.'],
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip baris jurnal saat dokumen dibatalkan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'b_masterbank', 'desc' => 'Master rekening bank.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center.'],
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center.'],
                        ['name' => 'master_cash_flow', 'desc' => 'Master Kategori Cash Flow.'],
                        ['name' => 'ap_masterrate', 'desc' => 'Kurs pajak (PAJAK) untuk nilai ekuivalen IDR.'],
                        ['name' => 'tbl_pv_h', 'desc' => 'Header Payment Voucher (Biaya) — dicari dan dipilih untuk dibayar melalui tab Payment Voucher.'],
                        ['name' => 'tbl_pv', 'desc' => 'Detail Payment Voucher (Biaya) terkait.'],
                        ['name' => 'kontrabon', 'desc' => 'Tabel induk tagihan AP (Kontrabon) yang juga dapat dibayar melalui tab Payment Voucher.'],
                        ['name' => 'kontrabon_h', 'desc' => 'Header Kontrabon tipe Regular/Installment.'],
                        ['name' => 'kontrabon_h_dp', 'desc' => 'Header Kontrabon tipe Down Payment (DP).'],
                        ['name' => 'kontrabon_h_cbd', 'desc' => 'Header Kontrabon tipe Cash Before Delivery (CBD).'],
                        ['name' => 'kontrabon_h_installment_detail', 'desc' => 'Detail cicilan untuk Kontrabon tipe Installment.'],
                    ],
                    'notes' => [
                        'Berbeda dengan Bank In, Bank Out mendukung unggah lampiran PDF (misalnya bukti transfer) yang dapat ditambah/dihapus langsung dari halaman list.',
                        'Sebuah tab ketiga, "List Payment" (LP), masih berfungsi penuh di sisi backend namun tombolnya sudah disembunyikan dari tampilan Create — dokumen LP lama masih dapat diedit, namun tidak dapat dibuat baru lagi.',
                        'Proses pembatalan (Cancel) pada Bank Out lebih kompleks dibandingkan Bank In: turut mengembalikan status dokumen AP yang dibayar (outstanding), melepas tautan ke Payment List/memo terkait, dan menyisipkan jurnal pembalik apabila belum ada.',
                        'Sama seperti Bank In, banyak berkas dan folder cadangan lama (create-bankout.php, approve-outbank.php, BAK, Old, dst.) masih tersimpan di server namun tidak lagi digunakan oleh alur aktif.',
                    ],
                ],
            ],
            'payment-voucher' => [
                'title' => 'Payment Voucher', 'icon' => 'fa-money', 'path' => 'module/AP/payment-voucher.php',
                'doc' => [
                    'summary' => 'Dokumen pembayaran biaya/pengeluaran umum yang berdiri sendiri (bukan pelunasan Kontrabon) — perlu diperhatikan bahwa ada FITUR LAIN dengan nama sama di menu Kontrabon, lihat catatan teknis di bawah.',
                    'purpose' => 'Payment Voucher pada grup menu Bank ini (dikenal secara internal sebagai tipe "Biaya") digunakan untuk mengajukan pembayaran biaya atau pengeluaran umum yang tidak terkait dengan pelunasan tagihan AP (Kontrabon). Dokumennya TIDAK langsung memindahkan uang — hanya setelah disetujui (Approved), dokumen ini baru dapat dipilih sebagai referensi pembayaran pada Bank Out atau Petty Cash Out.',
                    'variants' => [
                        ['name' => 'Regular', 'icon' => 'fa-file-o', 'desc' => 'Payment Voucher biaya umum — input COA bebas pada grid detail.'],
                        ['name' => 'EXIM', 'icon' => 'fa-globe', 'desc' => 'Payment Voucher yang baris detailnya diambil dari memo Export-Import (memo_h), dengan tujuan pembayaran terkunci ke "Export - Import".'],
                    ],
                    'flow' => [
                        ['title' => 'Isi form', 'desc' => 'Pengguna mengisi header (supplier, tanggal, metode &amp; tanggal bayar, rekening asal/tujuan, mata uang &amp; kurs, Kategori Cash Flow, Tipe Pajak) dan satu atau lebih baris detail (COA, Cost Center, Profit Center, jumlah, PPN/PPh, dan Nomor/Tanggal Faktur Pajak apabila COA-nya "1.52.04" — PPN Masukan yang dibayar di muka).'],
                        ['title' => 'Simpan → status Draft', 'desc' => 'Nomor dokumen dibuat dengan format tunggal <code>PV/NAG/bulanTahun/urutan</code> (dipakai bersama oleh tab Regular maupun EXIM). Header masuk ke <code>tbl_pv_h</code>, detail ke <code>tbl_pv</code> — TIDAK ada jurnal atau posting buku bank pada tahap ini.'],
                        ['title' => 'Edit (khusus status Draft)', 'desc' => 'Versi sebelum diedit diarsipkan ke <code>tbl_edit_pv_h</code>/<code>tbl_edit_pv</code> beserta catatan riwayatnya di <code>tbl_log_edit_pv</code>, lalu seluruh baris detail lama dihapus dan diganti dengan yang baru.'],
                        ['title' => 'Approve', 'desc' => 'Dari halaman "Approval &rsaquo; Payment Voucher", status pada <code>tbl_pv_h</code> menjadi Approved dalam satu tahap (tidak berjenjang). Pada tahap ini masih belum ada jurnal atau posting buku bank yang terbentuk.'],
                        ['title' => 'Dibayar melalui Bank Out / Petty Cash Out', 'desc' => 'Setelah Approved, dokumen ini baru dapat dipilih pada tab "Payment Voucher" di Bank Out (atau Petty Cash Out) untuk benar-benar dibayar — di sinilah jurnal dan posting ke buku bank sebenarnya terbentuk. Sistem menghitung sisa yang belum dibayar secara otomatis, sehingga mendukung pembayaran bertahap/sebagian.'],
                        ['title' => 'Cancel atau Reverse', 'desc' => 'Dari halaman Approval, dokumen dapat dibatalkan. Pengguna dengan hak akses khusus ("maintain_pv") juga dapat mengembalikan dokumen Approved ke status Draft melalui tombol "Set to Draft" pada halaman list, tanpa perlu jurnal pembalik formal (karena memang belum ada jurnal yang terbentuk sebelumnya).'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'tbl_pv_h', 'actions' => ['insert','update'], 'desc' => 'Tabel header. Kolom <code>outstanding</code> menyimpan sisa yang belum dibayar (berkurang setiap kali dibayar sebagian lewat Bank Out/Petty Cash Out); kolom <code>pv_form_type</code> membedakan Regular/EXIM. Insert saat Simpan, Update saat Edit/Approve/Cancel/Reverse.'],
                        ['name' => 'tbl_pv', 'actions' => ['insert','delete'], 'desc' => 'Baris detail — termasuk kolom <code>faktur_pajak</code>/<code>tgl_faktur_pajak</code> yang wajib diisi khusus untuk baris dengan COA "1.52.04". Insert saat Simpan; saat Edit seluruh baris lama dihapus (setelah diarsip) lalu ditulis ulang.'],
                        ['name' => 'tbl_edit_pv_h', 'actions' => ['insert'], 'desc' => 'Arsip snapshot header sesaat sebelum diedit.'],
                        ['name' => 'tbl_edit_pv', 'actions' => ['insert'], 'desc' => 'Arsip snapshot detail sesaat sebelum diedit.'],
                        ['name' => 'tbl_log_edit_pv', 'actions' => ['insert'], 'desc' => 'Catatan riwayat setiap kejadian edit (siapa, kapan).'],
                        ['name' => 'memo_h', 'actions' => ['update'], 'desc' => 'Status memo Export-Import ditandai sudah dibuatkan Payment Voucher, khusus tab EXIM — hanya memperbarui baris memo yang sudah ada, tidak membuat baris baru.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Accounts, termasuk penentu wajib-isi Cost Center per akun.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center.'],
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center.'],
                        ['name' => 'mtax', 'desc' => 'Master tarif PPN/PPh.'],
                        ['name' => 'b_masterbank', 'desc' => 'Master rekening bank — sumber dropdown Rekening Asal.'],
                        ['name' => 'master_supplier_bank', 'desc' => 'Rekening bank milik supplier — sumber dropdown Rekening Tujuan.'],
                        ['name' => 'master_cash_flow', 'desc' => 'Master Kategori Cash Flow ("Payment For").'],
                        ['name' => 'tbl_paymethod', 'desc' => 'Master metode pembayaran.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs harian.'],
                        ['name' => 'ap_masterrate', 'desc' => 'Kurs pajak (PAJAK) per tanggal dokumen.'],
                        ['name' => 'userpassword.maintain_pv', 'desc' => 'Flag per pengguna yang menentukan siapa saja yang boleh menggunakan tombol "Set to Draft" (Reverse) pada dokumen Approved.'],
                    ],
                    'notes' => [
                        'PENTING — ada DUA fitur berbeda yang sama-sama disebut "Payment Voucher" pada aplikasi ini: yang didokumentasikan di sini adalah Payment Voucher pada grup menu Bank (tipe internal "Biaya", tabel tbl_pv_h/tbl_pv). Fitur LAIN dengan nama serupa ada pada menu Kontrabon (Regular/Installment/DP/CBD/Saldo Awal, tabel kontrabon_h dan keluarganya, berkas pdf_pv_regular.php dkk.) — fitur itu dipakai untuk melunasi tagihan AP yang sudah dibukukan sebelumnya, dan proses persetujuannya berjenjang 2 tahap. Keduanya TIDAK sama dan tidak boleh tertukar.',
                        'Approve pada Payment Voucher (grup Bank ini) sama sekali tidak membentuk jurnal maupun posting ke buku bank — baru terbentuk belakangan saat dokumennya benar-benar dipilih dan dibayar lewat Bank Out atau Petty Cash Out.',
                        'Ada opsi status "Closed" pada filter halaman list, namun tidak ada satu pun alur kode yang benar-benar mengatur dokumen ke status tersebut — tampaknya sisa dari templat halaman list yang dipakai bersama fitur lain.',
                        'Tab ketiga, "FTR", ada di kode namun tombolnya sudah dinonaktifkan pada halaman Create — hanya dapat diakses dengan mengetik URL secara langsung.',
                        'Setelah Approved, dokumen ini dapat dikumpulkan dalam satu batch lewat menu Payment List sebelum benar-benar dibayar — lihat dokumentasi Payment List untuk alur selanjutnya.',
                    ],
                ],
            ],
            'report-bank' => [
                'title' => 'Report Bank', 'icon' => 'fa-file-excel-o', 'path' => 'module/AP/bankreport.php',
                'doc' => [
                    'summary' => 'Laporan mutasi & saldo berjalan (running balance) untuk satu rekening bank, mendukung mata uang asing dengan kolom ekuivalen IDR per transaksi.',
                    'purpose' => 'Report Bank dipakai untuk melihat histori transaksi 1 rekening bank dalam rentang tanggal tertentu — saldo awal, tiap mutasi debit/credit, sampai saldo akhir. Karena banyak rekening bank di aplikasi ini mata uangnya bukan IDR (USD, dsb), laporan ini juga menghitung nilai ekuivalen IDR di tiap baris.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Pilih rekening & rentang tanggal', 'desc' => 'User pilih 1 rekening bank dari <code>b_masterbank</code> dan tanggal awal/akhir.'],
                        ['title' => 'Hitung Saldo Awal', 'desc' => 'Diambil dari <code>b_saldoawal_bank</code>, digulung maju melewati semua mutasi SEBELUM tanggal awal filter, dikonversi ke IDR pakai kurs <b>HARIAN</b> (kurs pasar/harian, bukan kurs resmi pajak).'],
                        ['title' => 'Tampilkan baris transaksi', 'desc' => 'Tiap baris <code>b_reportbank</code> dalam rentang tanggal ditampilkan lengkap dengan kolom Debit IDR/Credit IDR — dikonversi pakai kurs <b>PAJAK</b> di tanggal transaksi masing-masing baris (bukan satu kurs untuk semua baris).'],
                        ['title' => 'Baris penyesuaian selisih kurs dikecualikan dari saldo native', 'desc' => 'Baris dari fitur Auto Jurnal Selisih Kurs (nomor dokumen berawalan <code>FX/</code>) murni merupakan penyesuaian NILAI IDR, bukan pergerakan uang asli — sehingga dikecualikan saat menghitung akumulasi saldo mata uang asli, agar tidak terhitung ganda. Nilai IDR-nya sendiri tetap ditampilkan apa adanya.'],
                        ['title' => 'Saldo Akhir & Export', 'desc' => 'Baris terakhir menunjukkan saldo akhir periode. Tombol Export Excel (report_usdbank.php) menjalankan ulang logika yang sama persis, agar hasil download selalu konsisten dengan tampilan di layar.'],
                    ],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'tbl_list_journal', 'desc' => 'Dibaca untuk mengambil rate yang BENAR-BENAR dipakai saat posting - baris bank dicocokkan lewat no_journal = no_doc dan no_coa = b_masterbank.id_coa, termasuk rate MANUAL yang diinput user saat bank-out.'],
                        ['name' => 'b_reportbank', 'desc' => 'Sumber utama baris mutasi bank — tabel ledger untuk seluruh transaksi bank (Bank In, Bank Out, Payment Voucher, dan baris penyesuaian selisih kurs otomatis).'],
                        ['name' => 'b_masterbank', 'desc' => 'Master rekening bank (nama bank, nomor rekening, mata uang, kode_cash, limit fasilitas pinjaman).'],
                        ['name' => 'b_saldoawal_bank', 'desc' => 'Saldo awal per rekening bank, sebagai titik nol sebelum ada transaksi tercatat.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs harian (HARIAN, untuk saldo awal) dan kurs pajak (PAJAK, untuk konversi tiap baris transaksi ke IDR).'],
                    ],
                    'notes' => [
                        'Perbaikan (commit 6553ae7, Sep 2026): konversi IDR transaksi bank valas kini memakai rate dari baris bank di tbl_list_journal (bisa rate MANUAL yang diinput saat bank-out), bukan diturunkan ulang dari masterrate PAJAK - dulu nilai laporan bisa berbeda dari GL. Fallback ke masterrate PAJAK hanya bila dokumen tidak punya baris jurnal. Pencarian masterrate juga kini difilter per curr, karena satu tanggal bisa memuat lebih dari satu baris kurs mata uang berbeda.',
                        'Pengecualian baris FX/ (selisih kurs) dari akumulasi saldo native merupakan perbaikan penting yang pernah dilakukan pada sesi ini — sebelumnya baris penyesuaian ini turut dianggap sebagai mutasi asli, sehingga menyebabkan saldo membengkak secara signifikan dan tidak akurat.',
                        'Excel export (report_usdbank.php) harus selalu disinkronkan logikanya dengan halaman ini — apabila salah satu diubah, yang lainnya juga perlu diperiksa agar angkanya tetap sama persis.',
                    ],
                ],
            ],
            'report-cashflow-realisation' => [
                'title' => 'Report Cash Flow Realisation', 'icon' => 'fa-line-chart', 'path' => 'module/AP/report-cashflow-realisation.php',
                'doc' => [
                    'summary' => 'Laporan arus kas terstruktur per kategori (Cash Receipt/Cash Disbursement) dengan realisasi aktual, plus section khusus mutasi Pinjaman Bank untuk 2 rekening tertentu.',
                    'purpose' => 'Report Cash Flow Realisation dipakai untuk melihat realisasi arus kas perusahaan dikelompokkan per kategori (sumber: master Kategori Cash Flow yang sama dengan yang dipakai form Petty Cash In/Out), dibandingkan dengan Saldo Awal & Saldo Akhir Kas dan Bank gabungan. Ada juga section khusus untuk memantau mutasi Pinjaman Bank.',
                    'variants' => [
                        ['name' => 'Cash Receipt / Disbursement', 'icon' => 'fa-list-alt', 'desc' => 'Baris-baris realisasi dikelompokkan per kategori/subkategori Cash Flow (dari master_cash_flow), diambil dari mutasi bank (b_reportbank) dan kas (c_report_pettycash) yang bertag id_cash_flow.'],
                        ['name' => 'Saldo Kas & Bank', 'icon' => 'fa-balance-scale', 'desc' => 'Baris Saldo Awal dan Saldo Akhir gabungan seluruh rekening kas dan bank, muncul di atas dan bawah daftar kategori.'],
                        ['name' => 'Pinjaman Bank', 'icon' => 'fa-bank', 'desc' => 'Section khusus untuk 2 rekening (008-997-1979 & 008-998-1982) yang berfungsi sebagai fasilitas pinjaman — menampilkan Saldo Awal, Penambahan, Pelunasan, Selisih Kurs, Saldo Akhir, Limit, dan Sisa Limit.'],
                    ],
                    'flow' => [
                        ['title' => 'Pilih rentang tanggal', 'desc' => 'Satu filter tanggal berlaku untuk seluruh laporan (kategori maupun section Pinjaman Bank).'],
                        ['title' => 'Hitung Saldo Awal Kas & Bank', 'desc' => 'Dari <code>b_saldoawal_bank</code> + <code>b_saldoawal_pettycash</code>, digulung maju sampai (tanggal_awal - 1 hari), dikonversi pakai kurs <b>HARIAN</b>.'],
                        ['title' => 'Hitung realisasi per kategori', 'desc' => 'Setiap baris mutasi di <code>b_reportbank</code>/<code>c_report_pettycash</code> yang bertag <code>id_cash_flow</code> dikonversi ke IDR pakai kurs <b>PAJAK</b> di tanggal transaksi BARIS itu sendiri (bukan satu kurs untuk semua baris dalam kategori yang sama) — lalu dijumlah per kategori/subkategori sesuai <code>master_cash_flow</code>.'],
                        ['title' => 'Kategori Selisih Kurs (id 55) dihitung khusus', 'desc' => 'Kategori "Pengakuan Laba/(Rugi) Selisih Kurs" menggunakan formula yang berbeda dari kategori lain — dihitung dari net debit dikurangi credit secara langsung (bukan formula umum yang hanya menggunakan sisi credit untuk Cash Out).'],
                        ['title' => 'Section Pinjaman Bank', 'desc' => 'Khusus 2 rekening 1979/1982: Saldo Awal (raw, tidak dibatasi ke 0), Penambahan &amp; Pelunasan (dari kategori Cash Flow "Penerimaan/Pelunasan Pinjaman Bank", tandanya sengaja dibalik agar penambahan utang = minus), Selisih Kurs (dari kategori id 55, khusus akun 1982 hanya ditampilkan apabila saldo native-nya positif), Saldo Akhir, Limit (dari <code>b_masterbank.fac_limit</code>), dan Sisa Limit.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'tbl_list_journal', 'desc' => 'Dibaca lewat cfr_functions.php untuk mengambil rate asli baris bank (termasuk rate manual), menggantikan penurunan ulang dari masterrate PAJAK.'],
                        ['name' => 'b_reportbank', 'desc' => 'Sumber mutasi rekening BANK yang bertag id_cash_flow.'],
                        ['name' => 'c_report_pettycash', 'desc' => 'Sumber mutasi rekening KAS/Petty Cash yang bertag id_cash_flow.'],
                        ['name' => 'b_saldoawal_bank', 'desc' => 'Saldo awal per rekening bank, titik nol perhitungan saldo berjalan.'],
                        ['name' => 'b_saldoawal_pettycash', 'desc' => 'Saldo awal per akun kas/petty cash, titik nol perhitungan saldo berjalan.'],
                        ['name' => 'master_cash_flow', 'desc' => 'Master Kategori Cash Flow — sumber nama kategori/subkategori dan urutan tampil (display_seq), sama persis dengan yang dipakai form Petty Cash In/Out.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs HARIAN untuk saldo awal, kurs PAJAK per tanggal transaksi untuk tiap baris realisasi.'],
                        ['name' => 'b_masterbank', 'desc' => 'Sumber Limit fasilitas pinjaman (fac_limit) untuk section Pinjaman Bank.'],
                    ],
                    'notes' => [
                        'Perbaikan (commit 6553ae7, Sep 2026): transaksi bank valas dikonversi memakai rate dari baris bank di tbl_list_journal, bukan masterrate PAJAK. Perbaikan ada di cfr_functions.php yang dipakai BERSAMA oleh halaman laporan dan export Excel, sehingga keduanya otomatis konsisten.',
                        'Kolom Projection dan Variance terdapat pada layout laporan, namun saat ini nilainya di-set 0 untuk seluruh baris — belum ada sumber data proyeksi yang terhubung (rencananya melalui menu terpisah "Upload Projection Cash Flow").',
                        'Section Pinjaman Bank sengaja dibatasi hanya untuk 2 akun (1979 & 1982) dan formulanya berbeda dari section kategori biasa — tanda Penambahan/Pelunasan dibalik, dan kondisi tampil/sembunyi berbeda bergantung pada tanda saldo (khusus akun 1982).',
                        'Memiliki kembaran Excel export (ekspor_cashflow_realisation.php) yang sudah diverifikasi menghasilkan angka persis sama dengan tampilan layar.',
                        'Realisasi per baris wajib membaca kurs PAJAK per-transaksi (bukan per-akun) — pernah terjadi kondisi di mana pembacaan kurs pada level akun menyebabkan nilai selisih kurs membengkak hingga ratusan miliar akibat konversi ganda (double-conversion).',
                    ],
                ],
            ],
            'e-statement' => [
                'title' => 'E-Statement', 'icon' => 'fa-file-text-o', 'path' => 'module/AP/e_statement.php',
                'doc' => [
                    'summary' => 'Bukan alat rekonsiliasi transaksi — ini adalah checklist pengarsipan rekening koran bulanan per rekening bank, berupa bukti unggah berkas PDF.',
                    'purpose' => 'E-Statement digunakan untuk memastikan rekening koran (bank statement) setiap rekening bank sudah diperoleh dan diarsipkan setiap bulannya. Halaman ini menampilkan matriks Rekening Bank × Bulan, menandai mana yang sudah diunggah dan mana yang belum. Tidak ada logika yang mencocokkan isi rekening koran dengan transaksi pada b_reportbank — murni pengarsipan dokumen, bukan rekonsiliasi.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Pilih periode, bank, & status', 'desc' => 'Filter menampilkan grid satu baris per Rekening Bank × Bulan pada rentang periode yang dipilih, dengan tanda visual "Sudah Upload" atau "Belum Upload".'],
                        ['title' => 'Upload', 'desc' => 'Untuk kombinasi rekening + bulan tertentu, pengguna dapat mengunggah berkas PDF rekening koran melalui modal — berkas disimpan pada folder file_pdf/ dan dicatat pada tabel b_estatement.'],
                        ['title' => 'View & Export', 'desc' => '"View" menampilkan PDF yang sudah diunggah langsung pada modal. "Excel" mengekspor checklist yang sama untuk rentang periode terpilih.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'b_estatement', 'actions' => ['insert'], 'desc' => 'Catatan unggahan — satu baris per unggahan (rekening, bulan, nama berkas, pengunggah, tanggal). Checklist selalu menampilkan unggahan TERBARU per kombinasi rekening + bulan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'b_masterbank', 'desc' => 'Master rekening bank (nama, nomor rekening, status aktif) — sumber baris pada grid.'],
                        ['name' => 'dim_date', 'desc' => 'Tabel dimensi tanggal — dipakai untuk membangun kolom bulan pada grid, sehingga baris tetap muncul meskipun belum ada unggahan.'],
                    ],
                    'notes' => [
                        'Tombol "Upload" saat ini hanya tampil untuk pengguna dengan username "indro" secara hardcode, bukan mengikuti hak akses menu (menu "Upload E - Statement") sebagaimana seharusnya.',
                        'Tidak ada logika pencocokan/rekonsiliasi transaksi sama sekali pada fitur ini — namanya mirip istilah "rekonsiliasi bank" pada umumnya, namun fungsinya murni checklist kelengkapan dokumen.',
                    ],
                ],
            ],
            'payment-list' => [
                'title' => 'Payment List', 'icon' => 'fa-list-alt', 'path' => 'module/AP/payment-list.php',
                'doc' => [
                    'summary' => 'Mengelompokkan Payment Voucher yang sudah disetujui (dari berbagai tipe) ke dalam satu batch per rekening sumber dana, melalui persetujuan berjenjang 2 tahap — tidak melakukan pembayaran itu sendiri.',
                    'purpose' => 'Payment List digunakan untuk mengumpulkan dokumen Payment Voucher/Kontrabon yang SUDAH disetujui sepenuhnya (Regular, Installment, DP, CBD, Biaya, Saldo Awal), memilih satu rekening sumber dana untuk seluruh batch tersebut, lalu meloloskannya melalui dua tahap persetujuan sebelum benar-benar siap dibayarkan. Payment List sendiri TIDAK memposting jurnal maupun menandai dokumen sebagai "Paid" — status tersebut baru ditentukan belakangan lewat Bank Out/Petty Cash Out.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Cari & pilih dokumen', 'desc' => 'Sistem menampilkan seluruh Payment Voucher/Kontrabon yang sudah lolos persetujuan tahap akhirnya masing-masing dan belum diklaim oleh Payment List lain. Pengguna memilih satu Rekening Sumber Dana (bank atau kas kecil) dan mencentang dokumen yang ingin dimasukkan ke batch ini.'],
                        ['title' => 'Simpan → status Draft', 'desc' => 'Nomor dokumen dibuat dengan format <code>PL-PV/bulanTahun/urutan</code>. Header masuk ke <code>pv_payment_list_h</code>, detail (referensi tipe + nomor dokumen sumber) ke <code>pv_payment_list_det</code>, dan setiap dokumen sumber ditandai <code>status_pl = "Draft"</code> supaya tidak dapat diklaim ganda oleh batch lain.'],
                        ['title' => 'Edit atau Cancel (Draft/First Approved)', 'desc' => 'Selama Draft, batch dapat diedit (ganti rekening sumber, tambah/kurangi dokumen). Pembatalan (pada status Draft maupun First Approved) melepaskan kembali seluruh dokumen di dalamnya supaya dapat dimasukkan ke Payment List lain.'],
                        ['title' => 'First Approval', 'desc' => 'Dari halaman "Approval &rsaquo; Payment List - First", batch Draft disetujui tahap pertama — status menjadi "FIRST APPROVED", diteruskan ke seluruh dokumen sumber di dalamnya.'],
                        ['title' => 'Second Approval', 'desc' => 'Dari halaman "Approval &rsaquo; Payment List - Second" (oleh approver yang berbeda), batch disetujui tahap akhir — status menjadi "SECOND APPROVED" (final), dan rekening sumber dana pada batch ini disinkronkan ke seluruh dokumen sumber di dalamnya. Setelah tahap ini, batch tidak dapat lagi diedit/dibatalkan langsung, hanya lewat alur Reverse formal terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'First Approved', 'cls' => 'progress'],
                        ['label' => 'Second Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pv_payment_list_h', 'actions' => ['insert','update'], 'desc' => 'Header batch: nomor, tanggal, rekening sumber dana, status (Draft/First Approved/Second Approved/Cancel). Insert saat Simpan, Update saat Edit/First Approval/Second Approval/Cancel.'],
                        ['name' => 'pv_payment_list_det', 'actions' => ['insert','delete'], 'desc' => 'Detail batch — satu baris per dokumen yang diklaim, disimpan sebagai pasangan (tipe dokumen, nomor dokumen), tanpa relasi foreign key formal ke tabel sumbernya. Insert saat Simpan/Edit; baris dapat dihapus saat Edit (dokumen dikeluarkan dari batch) atau Cancel.'],
                        ['name' => 'kontrabon', 'actions' => ['update'], 'desc' => 'Kolom status_pl diperbarui mengikuti status batch Payment List yang mengklaim dokumen ini — hanya update, tidak pernah insert baris baru dari menu ini.'],
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Kolom status_pl serta rekening sumber dana diperbarui mengikuti status batch yang mengklaimnya.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Kolom status_pl serta rekening sumber dana diperbarui mengikuti status batch yang mengklaimnya.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Kolom status_pl serta rekening sumber dana diperbarui mengikuti status batch yang mengklaimnya.'],
                        ['name' => 'kontrabon_h_installment_detail', 'actions' => ['update'], 'desc' => 'Kolom status_pl per cicilan diperbarui mengikuti status batch yang mengklaimnya.'],
                        ['name' => 'tbl_pv_h', 'actions' => ['update'], 'desc' => 'Kolom status_pl serta rekening sumber dana pada Payment Voucher (Biaya) diperbarui mengikuti status batch yang mengklaimnya.'],
                        ['name' => 'tbl_pv', 'actions' => ['update'], 'desc' => 'Kolom terkait pada baris detail Payment Voucher ikut diperbarui apabila diperlukan oleh sinkronisasi rekening sumber dana.'],
                        ['name' => 'ap_saldo_payment_voucher', 'actions' => ['update'], 'desc' => 'Kolom status_pl pada entri Saldo Awal diperbarui mengikuti status batch yang mengklaimnya.'],
                    ],
                    'tables_read' => [
                        ['name' => 'b_masterbank', 'desc' => 'Sumber dropdown Rekening Sumber Dana (bank).'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Sumber dropdown Rekening Sumber Dana (kas kecil).'],
                        ['name' => 'master_supplier_bank', 'desc' => 'Rekening bank penerima per dokumen, untuk keperluan cetak/ekspor.'],
                        ['name' => 'b_bankout_det', 'desc' => 'Dicek untuk mengetahui apakah suatu dokumen sumber sudah benar-benar dibayar lewat Bank Out.'],
                        ['name' => 'b_bankout_h', 'desc' => 'Dicek bersama b_bankout_det untuk status pembayaran via Bank Out.'],
                        ['name' => 'c_petty_cashout_det', 'desc' => 'Dicek untuk mengetahui apakah suatu dokumen sumber sudah benar-benar dibayar lewat Petty Cash Out.'],
                        ['name' => 'c_petty_cashout_h', 'desc' => 'Dicek bersama c_petty_cashout_det untuk status pembayaran via Petty Cash Out.'],
                        ['name' => 'payment_ftr', 'desc' => 'Kanal pelunasan lama (legacy) yang juga dicek — Payment List sendiri tidak pernah menandai status "Paid" secara langsung.'],
                    ],
                    'notes' => [
                        'Payment List TIDAK pernah memposting jurnal maupun menyentuh buku bank — statusnya murni tahap persetujuan administratif sebelum pembayaran benar-benar dieksekusi lewat Bank Out/Petty Cash Out.',
                        'Setelah Second Approved, sebuah batch Payment List dapat dipilih pada menu Transfer List untuk digabungkan dengan batch lain menjadi satu berkas pengajuan transfer ke bank.',
                        'Ada alur "Reverse Payment List" terpisah (di luar grup menu Bank) untuk mengembalikan batch yang sudah lolos persetujuan berjenjang ke status Draft apabila diperlukan koreksi.',
                    ],
                ],
            ],
            'transfer-list' => [
                'title' => 'Transfer List', 'icon' => 'fa-exchange', 'path' => 'module/AP/transfer-list.php',
                'doc' => [
                    'summary' => 'Menggabungkan satu atau beberapa Payment List menjadi satu berkas Excel siap-impor untuk sistem transfer massal bank (format BCA Dom VA) — tidak memposting jurnal maupun menyentuh buku bank.',
                    'purpose' => 'Transfer List adalah lapisan pengelompokan di atas Payment List — dipakai untuk menyatukan beberapa Payment List (yang sudah Draft atau First Approved) beserta metadata wajib transfer bank (Tipe Mutasi, Jenis Otorisasi, Tanggal Efektif, Jenis Biaya), lalu mengekspornya sebagai berkas yang siap diserahkan ke sistem cash-management bank untuk dieksekusi.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Isi metadata & pilih Payment List', 'desc' => 'Pengguna mengisi Tipe Mutasi (Single/Multi), Jenis Otorisasi (Bulk/Satuan), Tanggal Efektif (minimal hari ini), dan Jenis Biaya (OUR/BEN/SHA — kode penanggung biaya transfer sesuai standar SWIFT), lalu memilih satu atau lebih Payment List yang memenuhi syarat (Draft/First Approved, belum dipakai batch lain).'],
                        ['title' => 'Simpan → status Draft', 'desc' => 'Nomor dokumen format <code>TL/tahun/bulan/urutan</code>. Header masuk ke <code>pv_transfer_list_h</code>, dan setiap Payment List yang dipilih ditautkan lewat baris di <code>pv_transfer_list_det</code>. Status Payment List yang ditautkan TIDAK ikut berubah pada tahap ini.'],
                        ['title' => 'Lihat atau Cancel', 'desc' => '"Show" menampilkan seluruh Payment List yang tertaut beserta total per mata uang. Pembatalan hanya mengubah status Transfer List itu sendiri menjadi Cancel dan melepaskan Payment List di dalamnya untuk dapat dipakai pada batch lain.'],
                        ['title' => 'Export (hasil akhir yang sesungguhnya)', 'desc' => 'Menghasilkan berkas Excel dua sheet sesuai templat transfer massal BCA Dom VA — satu baris per pasangan (Payment List, rekening penerima), nominal dijumlahkan lintas dokumen ke penerima yang sama. Berkas inilah yang diserahkan secara manual ke sistem/portal bank.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pv_transfer_list_h', 'actions' => ['insert','update'], 'desc' => 'Header batch transfer: nomor, tanggal, tipe mutasi, jenis otorisasi, tanggal efektif, jenis biaya, status. Insert saat Simpan, Update status saat Cancel.'],
                        ['name' => 'pv_transfer_list_det', 'actions' => ['insert'], 'desc' => 'Tabel penghubung murni — hanya menyimpan pasangan (nomor Transfer List, nomor Payment List), tanpa data nominal sendiri (nominal selalu diambil ulang dari Payment List yang ditautkan).'],
                    ],
                    'tables_read' => [
                        ['name' => 'pv_payment_list_h', 'desc' => 'Sumber daftar Payment List yang memenuhi syarat untuk ditautkan.'],
                        ['name' => 'pv_payment_list_det', 'desc' => 'Isi & nominal batch Payment List yang ditautkan.'],
                        ['name' => 'b_masterbank', 'desc' => '(khusus saat Export) Melengkapi data rekening sumber dana.'],
                        ['name' => 'master_supplier_bank', 'desc' => '(khusus saat Export) Rekening/bank penerima tiap dokumen.'],
                        ['name' => 'supplier_master_pilihan', 'desc' => '(khusus saat Export) Data tambahan bank penerima.'],
                        ['name' => 'kontrabon_h', 'desc' => '(khusus saat Export) Melengkapi tanggal jatuh tempo & referensi rekening dokumen Kontrabon Regular/Installment.'],
                        ['name' => 'kontrabon_h_dp', 'desc' => '(khusus saat Export) Melengkapi tanggal jatuh tempo & referensi rekening dokumen Kontrabon DP.'],
                        ['name' => 'kontrabon_h_cbd', 'desc' => '(khusus saat Export) Melengkapi tanggal jatuh tempo & referensi rekening dokumen Kontrabon CBD.'],
                        ['name' => 'kontrabon_h_installment_detail', 'desc' => '(khusus saat Export) Melengkapi detail cicilan dokumen Kontrabon Installment.'],
                        ['name' => 'tbl_pv_h', 'desc' => '(khusus saat Export) Melengkapi tanggal jatuh tempo & referensi rekening dokumen Payment Voucher (Biaya).'],
                        ['name' => 'tbl_pv', 'desc' => '(khusus saat Export) Detail Payment Voucher (Biaya) terkait.'],
                        ['name' => 'ap_saldo_payment_voucher', 'desc' => '(khusus saat Export) Melengkapi referensi rekening entri Saldo Awal.'],
                    ],
                    'notes' => [
                        'Transfer List sama sekali tidak memposting jurnal maupun mengubah status Payment List yang ditautkan — murni fitur pengelompokan & ekspor berkas.',
                        'Tidak ditemukan keterkaitan kode langsung antara Transfer List dan Bank Out — pencatatan transaksi bank yang sesungguhnya (dan jurnalnya) terjadi belakangan dan terpisah pada Bank Out, tanpa mekanisme umpan balik status otomatis dari Bank Out kembali ke Transfer List maupun Payment List.',
                        'Berkas export ini secara khusus mengikuti templat "Dom VA" milik BCA — apabila suatu saat berpindah bank atau menambah bank lain, format ekspornya kemungkinan perlu disesuaikan.',
                    ],
                ],
            ],
        ],
    ],
    'ap' => [
        'title' => 'AP',
        'icon'  => 'fa-paypal',
        'items' => [
            '_s1' => ['section' => 'BPB Garment'],
            'verifikasi-bpb-garment' => [
                'title' => 'Verifikasi BPB', 'icon' => 'fa-share', 'path' => 'module/AP/verifikasibpb.php',
                'doc' => [
                    'summary' => 'Menarik BPB (Bukti Penerimaan Barang) yang sudah dikonfirmasi Warehouse ke dalam antrian AP, sekaligus jadi langkah verifikasi pertama — BPB itu sendiri TIDAK dibuat di sini, hanya "ditarik masuk".',
                    'purpose' => 'BPB (goods receipt) dibuat oleh sistem Warehouse di luar modul AP ini. Menu ini adalah titik masuk BPB ke proses AP: mencari BPB yang sudah dikonfirmasi Warehouse (<code>bpb.confirm=\'Y\'</code>) dan belum pernah ditarik, lalu menariknya jadi record kerja AP (<code>bpb_new</code>) — sekaligus halaman pencarian/list untuk memantau statusnya.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Cari BPB terkonfirmasi', 'desc' => 'Tombol "Create" (hanya muncul bagi pengguna dengan hak akses "Create BPB" terpisah) membuka pencarian BPB dari tabel master <code>bpb</code> yang sudah dikonfirmasi Warehouse dan belum ditarik AP manapun.'],
                        ['title' => 'Tarik ke antrian AP', 'desc' => 'BPB yang dicentang &amp; disimpan menghasilkan record baru di <code>bpb_new</code> berstatus "GMF", dan menandai baris <code>bpb</code> sumbernya sebagai sudah diklaim (<code>ap_inv=\'1\'</code>) supaya tidak ditarik dua kali.'],
                        ['title' => 'Approve atau Cancel', 'desc' => 'Dari halaman list ini juga tersedia ikon Approve/Cancel inline per baris (memanggil endpoint yang sama dengan menu "Approve BPB" terpisah) — tersembunyi untuk pengguna staf tertentu atau baris yang sudah diproses.'],
                    ],
                    'status_flow' => [
                        ['label' => 'GMF (ditarik)', 'cls' => 'planned'],
                        ['label' => 'GMF-PCH (Approved)', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'bpb_new', 'actions' => ['insert'], 'desc' => 'Record kerja AP untuk BPB ini — 1 baris per BPB yang ditarik, status awal "GMF".'],
                        ['name' => 'bpb_ri', 'actions' => ['insert'], 'desc' => 'Penanda rantai BPB (menghubungkan nomor BPB baru ke BPB asal, untuk kasus BPB hasil retur).'],
                        ['name' => 'bpb', 'actions' => ['update'], 'desc' => 'Kolom ap_inv ditandai "1" (sudah diklaim AP) — TIDAK PERNAH insert baris baru (BPB dibuat di sistem Warehouse, bukan di sini).'],
                    ],
                    'tables_read' => [
                        ['name' => 'po_header', 'desc' => 'Header Purchase Order terkait.'],
                        ['name' => 'po_header_draft', 'desc' => 'Draft Purchase Order terkait.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'masterpterms', 'desc' => 'Master termin pembayaran.'],
                    ],
                    'notes' => [
                        'Perbaikan (commit 9462ad4, Sep 2026) - cegah SIMPAN DOBEL: dulu tombol Save bisa ditekan berulang sehingga SEMUA baris tercentang terkirim ulang dan tersimpan dua kali di <code>bpb_new</code> serta <code>bpb_ri</code>. Sekarang: tombol dikunci selama proses berjalan, hanya checkbox baris yang dikirim (dulu checkbox "select all" di header ikut terkirim sebagai request kosong ber-no_bpb undefined), dan perpindahan halaman baru dilakukan setelah SELURUH request selesai (dulu redirect terjadi pada respons baris pertama sehingga sisanya bisa terputus / tersimpan sebagian).',
                        'Pengaman kedua di sisi server: <code>insertfmvbpb.php</code> kini menolak menyimpan ulang bila <code>no_bpb</code> tersebut sudah punya baris di <code>bpb_new</code> (satu BPB hanya boleh diverifikasi sekali; satu BPB memang menghasilkan BANYAK baris, satu per item, jadi keberadaan baris apa pun berarti sudah pernah tersimpan). Ini menutup semua jalur, termasuk refresh atau POST langsung.',
                        'Data lama (audit Sep 2026, patokan tabel <code>bpb</code>): 2.887 BPB punya baris berlebih di <code>bpb_new</code> (9.855 baris). Terbagi 3 - 500 BPB benar-benar simpan dobel (lebih dari satu create_date), 61 BPB tidak punya pasangan di <code>bpb</code>, dan 2.326 BPB batch simpannya tunggal tetapi barisnya berlipat tidak bulat (indikasi fan-out JOIN jo/jo_det/so/act_costing pada query penarikan, bukan klik ganda). Data lama ini BELUM dibersihkan.',
                        'PENTING: BPB (tabel master "bpb") sama sekali TIDAK dibuat di modul AP ini — sudah dicari ke seluruh module/ dan tidak ditemukan satu pun perintah INSERT ke tabel bpb. BPB dibuat oleh sistem Warehouse/Gudang yang terpisah; modul AP hanya membaca BPB yang sudah dikonfirmasi lalu menariknya.',
                        'Tombol "Create" di halaman ini butuh hak akses menu terpisah bernama "Create BPB" yang sengaja tidak muncul di sidebar sebagai menu sendiri — jadi walau tidak ada menu "Create BPB" yang terlihat, fungsinya tetap ada, tersemat sebagai tombol di halaman Verifikasi ini.',
                        'Ada beberapa berkas mati/duplikat terkait BPB (verifikasibpbedit.php yang sudah yatim, beberapa salinan "Copy" dan folder cadangan BAK/Old) yang tidak lagi dipakai.',
                    ],
                ],
            ],
            'approve-bpb-garment' => [
                'title' => 'Approve BPB', 'icon' => 'fa-thumbs-up', 'path' => 'module/AP/formapprovebpb.php',
                'doc' => [
                    'summary' => 'Antrian persetujuan untuk BPB yang sudah ditarik ke AP (lewat menu Verifikasi BPB) — begitu di-approve, langsung terbentuk posting hutang (kartu_hutang).',
                    'purpose' => 'Dipakai untuk menyetujui BPB yang statusnya masih "GMF" (baru ditarik, belum disetujui) — persetujuan ini yang membentuk posting awal ke kartu hutang (AP subledger), bukan Kontrabon.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Menampilkan BPB berstatus "GMF" (belum disetujui) untuk Profit Center Garment (NAG).'],
                        ['title' => 'Approve', 'desc' => 'Status berubah jadi "GMF-PCH"; sebuah baris baru dibuat di kartu_hutang (dikonversi ke IDR pakai kurs HARIAN kalau mata uang asing) dan di tabel status; baris BPB sumbernya ditandai selesai diproses (ap_inv="2").'],
                        ['title' => 'Cancel', 'desc' => 'Status jadi Cancel, dan baris BPB sumbernya dilepas kembali (ap_inv dikosongkan) supaya bisa ditarik ulang lewat Verifikasi BPB.'],
                    ],
                    'status_flow' => [
                        ['label' => 'GMF', 'cls' => 'planned'],
                        ['label' => 'GMF-PCH (Approved)', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'bpb_new', 'actions' => ['update'], 'desc' => 'Status diperbarui ke "GMF-PCH" (approve) atau "Cancel".'],
                        ['name' => 'kartu_hutang', 'actions' => ['insert'], 'desc' => 'Posting hutang (payable) dibuat pada saat Approve — inilah titik BPB benar-benar tercatat sebagai kewajiban ke supplier.'],
                        ['name' => 'status', 'actions' => ['insert'], 'desc' => 'Log ringkas lintas-dokumen (supplier, no BPB, tanggal BPB).'],
                        ['name' => 'bpb', 'actions' => ['update'], 'desc' => 'Kolom ap_inv ditandai "2" (selesai diproses) saat Approve, atau dikosongkan lagi saat Cancel.'],
                    ],
                    'tables_read' => [
                        ['name' => 'masterrate', 'desc' => 'Kurs HARIAN, untuk konversi ke IDR pada BPB mata uang asing.'],
                    ],
                    'notes' => [
                        'Menu ini bisa juga diakses lewat ikon Approve inline di halaman "Verifikasi BPB" — keduanya memanggil endpoint backend yang sama.',
                        'Tidak ada langkah "tolak/kembalikan" yang terpisah dari Cancel — membatalkan BPB di sini melepasnya kembali supaya bisa ditarik ulang dari awal.',
                    ],
                ],
            ],
            'verifikasi-bpb-return' => [
                'title' => 'Verifikasi BPB Return', 'icon' => 'fa-share', 'path' => 'module/AP/verifikasibppb.php',
                'doc' => [
                    'summary' => 'Kembaran "Verifikasi BPB" tapi untuk BPPB (Bukti Pengembalian/Retur Barang ke supplier) — dokumen pergerakan barang KEMBALI ke supplier, bukan koreksi/pembalik BPB.',
                    'purpose' => 'Menarik BPPB (retur barang) yang sudah dikonfirmasi Warehouse ke antrian kerja AP, persis seperti alur BPB biasa tapi di jalur/tabel terpisah.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Cari BPPB terkonfirmasi', 'desc' => 'Tombol "Create" (hak akses "Create BPB Return" terpisah) mencari baris BPPB master yang sudah dikonfirmasi Warehouse dan belum ditarik.'],
                        ['title' => 'Tarik ke antrian AP', 'desc' => 'Menghasilkan record baru di bppb_new berstatus "GMF", menandai baris bppb sumbernya sudah diklaim.'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Sama seperti BPB biasa, tersedia inline maupun lewat menu "Approve BPB Return" terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'GMF', 'cls' => 'planned'],
                        ['label' => 'GMF-PCH (Approved)', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'bppb_new', 'actions' => ['insert'], 'desc' => 'Record kerja AP untuk BPPB ini, status awal "GMF".'],
                        ['name' => 'bppb', 'actions' => ['update'], 'desc' => 'Kolom ap_inv ditandai "1" — BPPB itu sendiri dibuat di sistem Warehouse, bukan di sini.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                    ],
                    'notes' => [
                        'BPB Return adalah dokumen pergerakan barang KEMBALI ke supplier (retur fisik) — bukan pembatalan/koreksi atas BPB penerimaan. Setiap baris BPPB menunjuk balik ke BPB asal yang barangnya diretur.',
                        'Fitur ini HANYA ada untuk divisi Garment — tidak ada menu setara "BPB Return" untuk Knitting.',
                    ],
                ],
            ],
            'approve-bpb-return' => [
                'title' => 'Approve BPB Return', 'icon' => 'fa-thumbs-up', 'path' => 'module/AP/formapprovebppb.php',
                'doc' => [
                    'summary' => 'Antrian persetujuan BPPB (retur barang) — berbeda dari Approve BPB biasa, di sini TIDAK terbentuk posting hutang baru.',
                    'purpose' => 'Menyetujui BPPB berstatus "GMF" yang sudah ditarik lewat menu Verifikasi BPB Return.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'BPPB berstatus GMF, dikelompokkan per No RO (Return Order).'],
                        ['title' => 'Approve', 'desc' => 'Status jadi "GMF-PCH"; dicatat ke tabel ttl_bppb; baris BPPB sumbernya ditandai selesai (ap_inv="2"). TIDAK ada posting ke kartu_hutang di langkah ini.'],
                        ['title' => 'Cancel', 'desc' => 'Status jadi Cancel, baris BPPB sumbernya dilepas kembali.'],
                    ],
                    'status_flow' => [
                        ['label' => 'GMF', 'cls' => 'planned'],
                        ['label' => 'GMF-PCH (Approved)', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'bppb_new', 'actions' => ['update'], 'desc' => 'Status diperbarui ke "GMF-PCH" atau "Cancel".'],
                        ['name' => 'ttl_bppb', 'actions' => ['insert'], 'desc' => 'Catatan total retur saat Approve.'],
                        ['name' => 'bppb', 'actions' => ['update'], 'desc' => 'Kolom ap_inv diperbarui.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Berbeda dari Approve BPB biasa: menyetujui BPB Return TIDAK membuat baris baru di kartu_hutang — retur murni dicatat sebagai pengurang barang, dampaknya ke hutang baru terlihat nanti saat Kontrabon dibuat (baris return_kb).',
                    ],
                ],
            ],
            'update-bpb' => [
                'title' => 'Update BPB', 'icon' => 'fa-pencil', 'path' => 'module/AP/update_bpb.php',
                'doc' => [
                    'summary' => 'Halaman pelacakan & penautan Faktur Pajak/Invoice ke BPB — bukan halaman koreksi data BPB itu sendiri, lebih ke peluncur (launcher) 4 sub-form terpisah.',
                    'purpose' => 'Menampilkan daftar keterkaitan BPB dengan nomor Invoice dan Faktur Pajak supplier, dengan tombol-tombol untuk update Faktur, update Invoice, batalkan Faktur/Invoice, dan update hasil scan Faktur.',
                    'variants' => [
                        ['name' => 'Update Faktur', 'icon' => 'fa-pencil-square-o', 'desc' => 'Membuka form_update_faktur.php.'],
                        ['name' => 'Update Invoice', 'icon' => 'fa-pencil-square-o', 'desc' => 'Membuka form_update_inv.php.'],
                        ['name' => 'Cancel Faktur & Invoice', 'icon' => 'fa-times-circle', 'desc' => 'Membuka form_cancel_faktur_inv.php, atau pembatalan langsung per baris.'],
                        ['name' => 'Update Faktur Scan', 'icon' => 'fa-camera', 'desc' => 'Membuka form_scan_faktur.php.'],
                    ],
                    'flow' => [
                        ['title' => 'Cari & sinkronkan', 'desc' => 'Setiap pencarian otomatis menjalankan sinkronisasi housekeeping (nomor &amp; tanggal invoice pada bpb_faktur_inv disamakan dengan data terbaru di bpb_new).'],
                        ['title' => 'Buka sub-form terkait', 'desc' => 'Masing-masing dari 4 tombol punya hak akses menu sendiri-sendiri, terpisah dari hak akses BPB Garment/Knitting.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'bpb_faktur_inv', 'actions' => ['update'], 'desc' => 'Nomor & tanggal invoice disinkronkan ulang setiap kali halaman ini dicari — baris barunya sendiri dibuat dari sub-form terkait (di luar cakupan dokumentasi ini).'],
                    ],
                    'tables_read' => [
                        ['name' => 'bpb_new', 'desc' => 'Sumber data BPB yang sudah diproses AP.'],
                    ],
                    'notes' => [
                        'Halaman ini murni "launcher" — 4 sub-form aktualnya (Update Faktur/Invoice/Cancel/Scan) tidak termasuk cakupan dokumentasi ini dan masing-masing punya hak akses sendiri, terpisah dari izin BPB Garment/Knitting.',
                    ],
                ],
            ],
            'report-fp' => [
                'title' => 'Report FP', 'icon' => 'fa-files-o', 'path' => 'module/AP/report-faktur-pajak.php',
                'doc' => [
                    'summary' => 'Laporan Faktur Pajak (tax invoice) murni baca, menggabungkan Faktur yang sudah "di-scan" maupun yang belum, per rentang tanggal.',
                    'purpose' => 'Menampilkan daftar Faktur Pajak beserta detail barang (harga, qty, DPP, diskon, PPN, total) untuk keperluan administrasi pajak — sumbernya digabung dari 2 jalur: Faktur yang sudah melalui proses "scan" dan yang belum.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'bpb_scan_faktur', 'desc' => 'Detail Faktur Pajak yang sudah di-scan.'],
                        ['name' => 'bpb_scan_faktur_h', 'desc' => 'Header Faktur Pajak yang sudah di-scan.'],
                        ['name' => 'bpb_faktur_inv', 'desc' => 'Penaut BPB ke nomor Invoice/Faktur Pajak.'],
                        ['name' => 'bpb_new', 'desc' => 'Data baris BPB, untuk Faktur yang belum melalui proses scan.'],
                    ],
                    'notes' => [
                        'Punya kembaran ekspor Excel (ekspor-report-faktur-pajak.php).',
                    ],
                ],
            ],
            '_s2' => ['section' => 'BPB Knitting'],
            'verifikasi-bpb-knitting' => [
                'title' => 'Verifikasi BPB (Knitting)', 'icon' => 'fa-share', 'path' => 'module/AP/verifikasibpb_knitting.php',
                'doc' => [
                    'summary' => 'Kembaran "Verifikasi BPB" untuk divisi Knitting — perbedaan mendasarnya, sumber data BPB yang ditarik BUKAN dari tabel lokal, melainkan dari basis data PostgreSQL milik sistem Knitting yang terpisah.',
                    'purpose' => 'Menarik BPB Knitting yang berstatus "approved" di sistem sumbernya ke antrian kerja AP, memakai tabel tujuan yang identik dengan Garment (bpb_new) namun ditandai profit_center "NAK".',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Cari BPB dari sistem Knitting', 'desc' => 'Tombol "Create" membuka <code>formverifikasibpb_knitting.php</code>, yang melakukan <code>pg_query()</code> ke basis data PostgreSQL <code>alabare_knitting</code> (host 10.10.5.62) — membaca tabel <code>bpb</code>, <code>master_supplier</code>, dan <code>purchase_orders</code> milik sistem Knitting, hanya baris berstatus "approved".'],
                        ['title' => 'Tarik ke antrian AP', 'desc' => 'BPB terpilih disalin menjadi baris baru di <code>bpb_new</code> (MySQL, profit_center = "NAK") berstatus "GMF", beserta baris <code>bpb_ri</code> penanda rantai BPB.'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Tersedia inline maupun lewat menu "Approve BPB (Knitting)" terpisah — memakai backend yang SAMA PERSIS dengan BPB Garment (approvebpb.php/cancelbpb.php), hanya berbeda pada filter profit_center.'],
                    ],
                    'status_flow' => [
                        ['label' => 'GMF (ditarik)', 'cls' => 'planned'],
                        ['label' => 'GMF-PCH (Approved)', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'bpb_new', 'actions' => ['insert'], 'desc' => 'Record kerja AP untuk BPB Knitting ini, status awal "GMF", profit_center "NAK" — tabel yang sama persis dipakai BPB Garment.'],
                        ['name' => 'bpb_ri', 'actions' => ['insert'], 'desc' => 'Penanda rantai BPB, sama seperti alur Garment.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier (untuk filter pencarian).'],
                        ['name' => 'bpb [Knitting]', 'db' => 'PostgreSQL alabare_knitting @ 10.10.5.62 (conn4)', 'desc' => 'Tabel BPB milik sistem Knitting sendiri (nama tabel sama persis dengan tabel "bpb" MySQL milik Garment, namun basis data fisiknya berbeda — diberi label [Knitting] di sini agar tidak tertukar) — sumber data asli, hanya baris berstatus "approved" yang boleh ditarik.'],
                        ['name' => 'master_supplier', 'db' => 'PostgreSQL alabare_knitting @ 10.10.5.62 (conn4)', 'desc' => 'Master Supplier versi sistem Knitting (terpisah dari mastersupplier MySQL).'],
                        ['name' => 'purchase_orders', 'db' => 'PostgreSQL alabare_knitting @ 10.10.5.62 (conn4)', 'desc' => 'Data Purchase Order versi sistem Knitting.'],
                    ],
                    'notes' => [
                        'PENTING: sumber data BPB Knitting adalah basis data PostgreSQL terpisah (<code>alabare_knitting</code> @ 10.10.5.62, tabel <code>bpb</code>/<code>master_supplier</code>/<code>purchase_orders</code>), BUKAN tabel <code>bpb</code> MySQL yang dipakai Garment — begitu ditarik lewat menu ini, datanya disalin ke <code>bpb_new</code> MySQL yang sama dengan Garment, jadi seluruh proses sesudahnya (approve, Kontra Bon, dst.) berjalan seragam.',
                        'Karena sumbernya sistem lain, tidak ada kolom <code>ap_inv</code> yang perlu ditandai balik di sisi Postgres — tidak seperti BPB Garment yang menandai baris <code>bpb</code> lokal saat ditarik.',
                    ],
                ],
            ],
            'approve-bpb-knitting' => [
                'title' => 'Approve BPB (Knitting)', 'icon' => 'fa-thumbs-up', 'path' => 'module/AP/formapprovebpb_knitting.php',
                'doc' => [
                    'summary' => 'Antrian persetujuan BPB Knitting berstatus "GMF" — memakai backend approve/cancel yang sama persis dengan BPB Garment, hanya berbeda filter profit_center ("NAK").',
                    'purpose' => 'Menyetujui BPB Knitting yang sudah ditarik dari sistem Postgres Knitting lewat menu Verifikasi BPB (Knitting), membentuk posting hutang di kartu_hutang persis seperti alur Garment.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'BPB berstatus "GMF" dengan profit_center = "NAK" (Knitting).'],
                        ['title' => 'Approve', 'desc' => 'Status berubah "GMF-PCH"; baris baru dibuat di kartu_hutang (dikonversi ke IDR pakai kurs harian bila mata uang asing) dan di tabel status.'],
                        ['title' => 'Cancel', 'desc' => 'Status jadi Cancel.'],
                    ],
                    'status_flow' => [
                        ['label' => 'GMF', 'cls' => 'planned'],
                        ['label' => 'GMF-PCH (Approved)', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'bpb_new', 'actions' => ['update'], 'desc' => 'Status diperbarui ke "GMF-PCH" atau "Cancel".'],
                        ['name' => 'kartu_hutang', 'actions' => ['insert'], 'desc' => 'Posting hutang dibuat pada saat Approve — file backend sama persis dengan BPB Garment.'],
                        ['name' => 'status', 'actions' => ['insert'], 'desc' => 'Log ringkas lintas-dokumen.'],
                    ],
                    'tables_read' => [
                        ['name' => 'masterrate', 'desc' => 'Kurs harian untuk konversi ke IDR.'],
                    ],
                    'notes' => [
                        'Backend approvebpb.php/cancelbpb.php dipakai bersama oleh BPB Garment maupun Knitting (file identik) — perbedaan Garment vs Knitting murni terletak pada filter profit_center di halaman antrian masing-masing, bukan pada logika approve/cancel-nya.',
                    ],
                ],
            ],
            '_s3' => ['section' => 'FTR'],
            'ftr-cbd' => [
                'title' => 'FTR CBD', 'icon' => 'fa-paper-plane-o', 'path' => 'module/AP/ftrcbd.php',
                'doc' => [
                    'summary' => 'Pembuatan dan approve/cancel FTR (Form Transfer Request) tipe CBD — permintaan pembayaran ke supplier SEBELUM barang diterima, menggantikan peran BPB sebagai pemicu tagihan.',
                    'purpose' => '"FTR" adalah singkatan dari "Transfer Request" (tercantum literal pada judul cetak dokumen "FORM TRANSFER REQUEST (CBD/DP)"). Tipe CBD (Cash Before Delivery) dipakai ketika supplier mensyaratkan pembayaran penuh di muka, sebelum PO benar-benar dikirim/diterima — sehingga tidak ada BPB yang bisa dijadikan dasar tagihan.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Buat FTR', 'desc' => 'Form (<code>formftrcbd.php</code>) mengambil data PO/PI terkait, lalu <code>insertftrcbd.php</code> menyimpan header FTR CBD berstatus draf.'],
                        ['title' => 'Approve', 'desc' => 'Status menjadi "Approved" — FTR siap ditautkan menjadi Kontra Bon lewat menu "Kontra Bon FTR CBD" (alur klasik) maupun tab CBD pada "Payment Voucher (Kontrabon)".'],
                        ['title' => 'Cancel', 'desc' => 'Status menjadi "Cancel".'],
                        ['title' => 'Ditarik jadi Kontra Bon', 'desc' => 'Begitu dipakai membentuk Kontra Bon CBD, kolom <code>is_invoiced</code> berubah "Invoiced" dan <code>kb_inv</code> terisi — dikelola dari menu Kontra Bon FTR CBD, bukan di sini.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ftr_cbd', 'actions' => ['insert', 'update'], 'desc' => 'Header FTR CBD (no_ftr_cbd, tanggal, supplier, No PO/PI, subtotal, pajak, total, mata uang, biaya tambahan) — dibuat saat simpan, status diperbarui saat approve/cancel.'],
                    ],
                    'tables_read' => [
                        ['name' => 'po_header', 'desc' => 'Data Purchase Order sumber.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                    ],
                    'notes' => [
                        'Pembayaran CBD/DP TIDAK diposting ke jurnal umum (tbl_list_journal) maupun Kartu Hutang di titik manapun dalam siklusnya — murni dicatat di subledger FTR/Kontra Bon CBD-DP. Ini perbedaan fungsional nyata dari alur BPB biasa, bukan sekadar beda penamaan.',
                        'Menggantikan peran BPB: pada alur normal, BPB-lah yang memicu Kontra Bon; pada alur CBD/DP, FTR yang berperan sebagai pemicu karena barang belum diterima saat pembayaran harus dilakukan.',
                    ],
                ],
            ],
            'ftr-dp' => [
                'title' => 'FTR DP', 'icon' => 'fa-paper-plane-o', 'path' => 'module/AP/ftrdp.php',
                'doc' => [
                    'summary' => 'Pembuatan dan approve/cancel FTR (Form Transfer Request) tipe DP — permintaan uang muka ke supplier sebagai bagian dari pembayaran PO, sebelum barang diterima penuh.',
                    'purpose' => 'Tipe DP (Down Payment) dipakai ketika supplier mensyaratkan sebagian pembayaran di muka sebagai uang muka pemesanan, berbeda dari CBD yang mensyaratkan pembayaran penuh di muka.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Buat FTR', 'desc' => '<code>insertftrdp.php</code> menyimpan header FTR DP berstatus draf, termasuk nilai DP dan sisa saldo (balance).'],
                        ['title' => 'Approve', 'desc' => 'Status menjadi "Approved" — siap ditautkan menjadi Kontra Bon lewat menu "Kontra Bon FTR DP" maupun tab DP pada "Payment Voucher (Kontrabon)".'],
                        ['title' => 'Cancel', 'desc' => 'Status menjadi "Cancel".'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ftr_dp', 'actions' => ['insert', 'update'], 'desc' => 'Header FTR DP (no_ftr_dp, tanggal, supplier, No PO/PI, total, nilai DP, balance, mata uang) — dibuat saat simpan, status diperbarui saat approve/cancel.'],
                    ],
                    'tables_read' => [
                        ['name' => 'po_header', 'desc' => 'Data Purchase Order sumber.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                    ],
                    'notes' => [
                        'Sama seperti FTR CBD, pembayaran DP tidak diposting ke jurnal umum maupun Kartu Hutang — murni subledger.',
                    ],
                ],
            ],
            '_s4' => ['section' => 'Document Tracking'],
            'bpb-transferred' => [
                'title' => 'Document Handover', 'icon' => 'fa-exchange', 'path' => 'module/AP/document_handover.php',
                'doc' => [
                    'summary' => 'Daftar batch serah-terima dokumen fisik BPB dari Warehouse ke Accounting — murni pelacakan hand-off antar departemen, bukan input data transaksi.',
                    'purpose' => 'Dipakai Accounting untuk menerima (accept) batch BPB yang sudah dikirim/dibundel oleh Warehouse, sebagai syarat sebelum BPB tersebut bisa diproses lebih lanjut menjadi Kontrabon.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar batch', 'desc' => 'Setiap batch transfer (<code>no_transfer</code>) berisi beberapa baris BPB. Status batch (Transfer/Approved/Approved Partial/Cancel) dihitung otomatis dari campuran status baris-baris di dalamnya, bukan kolom status tersendiri.'],
                        ['title' => 'Accept BPB', 'desc' => 'Accounting mencentang &amp; menerima baris-baris BPB dalam batch — status tiap baris berubah jadi Approved.'],
                        ['title' => 'Cancel', 'desc' => 'Batch/baris juga dapat dibatalkan.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Transfer', 'cls' => 'planned'],
                        ['label' => 'Approved / Approved Partial', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ir_trans_bpb', 'actions' => ['update'], 'desc' => 'Baris transfer batch BPB — halaman ini HANYA memperbarui status/approved_by/approved_date; baris batchnya sendiri dibuat dari modul Warehouse, bukan dari menu AP ini.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'ir_status', 'desc' => 'Master label status untuk BPB.'],
                    ],
                    'notes' => [
                        'Perbaikan (commit f29c912, Sep 2026): syarat transfer pertama (TFTA, Fin ke Acc) kini HANYA mewajibkan BPB - faktur tidak lagi digating karena nomor faktur sah boleh berupa strip "-". Guard ada di insert_trf_fintoacc.php (server, balas HTTP 422) dan post_inv_fintoacc.php (checkbox di-disable). IR alur lama tanpa ir_kontrabon_h tetap tidak digating.',
                        'Perbaikan (commit 165efa8, Sep 2026) - CELAH DITUTUP: guard BPB semula hanya memeriksa TFTA, padahal menu TRANSFER INVOICE FROM ACCOUNTING TO PURCHASING (post_inv_acctopch.php) juga menampilkan IR berstatus "Received" (belum lewat Fin ke Acc), sehingga IR tanpa BPB bisa lolos lewat jalur itu. Guard kini berlaku utk TFTA DAN TATP, dan menu Acc ke Pch - yang sebelumnya tidak punya pembatasan sama sekali - kini ikut men-disable checkbox beserta notifikasinya. TPTF tidak perlu digating karena menunya hanya menerima dokumen berstatus "Accepted Pch".',
                        'Catatan alur: menu transfer memanggil insert_log_trans.php DULU (membuat log batch), baru insert_trf_fintoacc.php per baris. Jadi bila guard menolak, log batch sudah terlanjur dibuat - perilaku lama yang berlaku untuk kegagalan apa pun di endpoint itu. Pertahanan utamanya ada di checkbox yang di-disable.',
'Menu ini MENGGANTIKAN dua menu lama yang kini sudah tidak ada di sidebar: BPB Transferred (bpb_received.php) dan Invoice Received lama (invoice_received.php). Keduanya digabung menjadi satu halaman dengan pilihan Type Document - Invoice atau BPB & SJ - dan tombol aksinya (Post/Accept per tahap, Accept BPB, Accept SJ, Reverse) muncul sesuai type dan hak akses menurole pengguna.',
                        'Perbaikan (commit 548b66d, Sep 2026): Accept dipisah menjadi dua menu - Accept BPB untuk dokumen selain FG/OUT, dan Accept SJ khusus Surat Jalan (no_bpb berawalan FG/OUT) dengan menurole terpisah id 137 (Document Handover - Accept SJ Warehouse To Accounting). Halaman SJ memakai logika yang sama lewat parameter mode sehingga tidak ada duplikasi kode. Pemisahan aman karena tidak ada satu pun no_transfer yang mencampur FG/OUT dengan dokumen lain.',
                        'Pembuatan baris ir_trans_bpb (batch transfer itu sendiri) TIDAK ditemukan sama sekali di dalam module/AP/ — dibuat dari modul Warehouse/Gudang yang terpisah. Menu ini murni sisi penerimaan (Accounting).',
                    ],
                ],
            ],
            'invoice-received-new' => [
                'title' => 'Invoice Received', 'icon' => 'fa-file-text-o', 'path' => 'module/AP/kontrabon_new.php',
                'doc' => [
                    'summary' => 'Pencatatan penerimaan invoice supplier versi baru (dulu bernama "Kontrabon New"). Satu dokumen IR memuat beberapa Invoice, tiap invoice punya Faktur Pajak, dan tiap faktur punya daftar BPB/RO. Jadi sumber nilai untuk PV (Payment Voucher).',
                    'purpose' => 'Menggantikan input Kontrabon lama. Nomor invoice, faktur pajak, dan BPB divalidasi saat di-scan supaya tidak dobel dan tidak tertukar antar supplier. BPB yang sedang dipakai draft user lain dikunci lewat tabel reservasi agar tidak diambil dua orang.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Create', 'desc' => 'create_kontrabon_new.php — pilih Supplier, scan No Invoice, scan Faktur Pajak, lalu scan BPB/RO per faktur. Draft tersimpan per user sehingga bisa dilanjutkan dari komputer lain. Simpan lewat kontrabon_new_save.php.'],
                        ['title' => 'Validasi saat scan', 'desc' => 'kontrabon_new_check_inv.php (No Invoice belum dipakai supplier yang sama) dan kontrabon_new_check_bpb.php (BPB milik supplier terpilih, belum dipakai IR lain, lalu di-RESERVE). Nomor berupa strip "-" boleh dipakai berulang karena artinya "tanpa nomor".'],
                        ['title' => 'Edit', 'desc' => 'edit_kontrabon_new.php — prefill dari DB, tanpa draft/reservasi. Seluruh detail ditulis ulang dalam satu transaksi oleh kontrabon_new_update.php (all-or-nothing).'],
                        ['title' => 'Terpakai di PV', 'desc' => 'Dokumen IR menjadi dasar pembuatan Payment Voucher; sesudah IR dipakai transaksi lain (status bergerak dari Received atau ada transfer aktif), IR TIDAK bisa diedit lagi.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Received', 'cls' => 'planned'],
                        ['label' => 'Dipakai PV / transfer', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'done'],
                    ],
                    'tables_write' => [
                        ['name' => 'ir_kontrabon_h', 'actions' => ['insert','update'], 'desc' => 'Header IR: doc_number, no_reff, nama_supp, tanggal, total_amount, amount_add_pv.'],
                        ['name' => 'ir_kontrabon_inv', 'actions' => ['insert','delete'], 'desc' => 'Daftar invoice per IR. Saat edit dihapus lalu ditulis ulang.'],
                        ['name' => 'ir_kontrabon_faktur', 'actions' => ['insert','delete'], 'desc' => 'Faktur pajak per invoice (nama/NPWP supplier, DPP, PPN, PPnBM).'],
                        ['name' => 'ir_kontrabon_bpb', 'actions' => ['insert','delete'], 'desc' => 'BPB/RO per faktur beserta nilainya. RO/retur bernilai NEGATIF.'],
                        ['name' => 'ir_kontrabon_bpb_reserve', 'actions' => ['insert','delete'], 'desc' => 'Kunci BPB per draft (no_bpb UNIQUE) supaya dua user tidak memakai BPB yang sama. Reservasi lebih dari 24 jam dilepas otomatis.'],
                        ['name' => 'ir_invoice_supp_h', 'actions' => ['insert','update'], 'desc' => 'Mirror header untuk alur Document Handover.'],
                        ['name' => 'ir_invoice_supp', 'actions' => ['insert','delete'], 'desc' => 'Mirror detail invoice.'],
                        ['name' => 'bpb_new', 'actions' => ['update'], 'desc' => 'Kolom upt_* (No/Tgl Invoice & Faktur) diisi per BPB lewat bpbnew_apply_docinfo() — dipakai laporan pembelian.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier untuk dropdown header.'],
                        ['name' => 'bpb_new', 'desc' => 'Sumber BPB masuk: pemilik supplier, tanggal, qty/price/tax untuk DPP, PPN, dan Total.'],
                        ['name' => 'bppb_new', 'desc' => 'Sumber RO/retur (no_ro / no_bppb) — nilainya dibalik jadi negatif.'],
                    ],
                    'notes' => [
                        'Perbaikan (commit f29c912, Sep 2026) - SYARAT TRANSFER DILONGGARKAN: dulu IR baru boleh ditransfer bila sudah ada faktur DAN BPB. Sekarang cukup BPB saja. Alasannya, nomor faktur yang sah boleh berupa STRIP "-" (artinya memang tanpa nomor faktur), sehingga keberadaan faktur tidak bisa dipakai sebagai syarat. Notifikasi di daftar juga berubah dari "IR belum lengkap: belum ada faktur & BPB" menjadi "IR belum ada BPB".',
                        'Perbaikan (commit c034a17 dan 4423b91, Sep 2026): No Faktur dan No Invoice berupa STRIP "-" tidak lagi dianggap duplikat, karena strip artinya "tanpa nomor" dan wajar dipakai berulang.',
                        'Perbaikan (commit 45ef4f7, Sep 2026) - SUPPLIER DI FORM EDIT KINI BISA DIGANTI: satu perusahaan kadang punya DUA record di mastersupplier dengan susunan nama berbeda (contoh "CV. MITRA EKA PERKASA" dan "MITRA EKA PERKASA, CV"), sedangkan BPB-nya hanya terdaftar di salah satunya - akibatnya BPB yang sah ikut tertolak. Field Supplier diubah dari readonly menjadi dropdown pencarian.',
                        'Pengaman perubahan supplier: kalau BPB sudah terisi lalu supplier diganti ke nama yang BEDA, penggantian DITOLAK dan pilihan dikembalikan, disertai daftar BPB beserta pemilik aslinya. Di sisi server (kontrabon_new_update.php) dicek ulang 3 lapis - supplier harus ada di mastersupplier, harus cocok dengan supplier tiap BPB pada payload, dan diverifikasi langsung ke bpb_new/bppb_new supaya tidak bisa ditembus dari browser. Gagal salah satu = seluruh transaksi rollback.',
                        'Supplier baru disimpan ke ir_kontrabon_h DAN ir_invoice_supp_h sekaligus agar header dan mirror-nya tidak desync.',
                        'IR yang sudah dipakai transaksi lain tidak bisa diedit - harus di-reverse dulu dari menu terkait.',
                    ],
                ],
            ],
            'invoice-received' => [
                'title' => 'Invoice Received (lama - digabung ke Document Handover)', 'icon' => 'fa-share', 'path' => 'module/AP/invoice_received.php',
                'doc' => [
                    'summary' => 'Mencatat kedatangan faktur/invoice fisik dari supplier, lalu melacak serah-terimanya lintas 3 departemen (Finance → Accounting → Purchasing → Finance) sebelum bisa dicocokkan ke Kontrabon.',
                    'purpose' => 'Dipakai untuk mencatat penerimaan faktur pajak/invoice supplier secara fisik, kemudian mendokumentasikan alur serah-terimanya antar departemen sampai kembali ke Finance untuk pengarsipan/verifikasi.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Invoice Received', 'desc' => 'Input penerimaan — satu header per batch penerimaan, satu baris detail per nomor invoice fisik yang diterima.'],
                        ['title' => 'Post Fin → Acc → Accept Acc', 'desc' => 'Finance mengirim batch ke Accounting; Accounting menerima (accept).'],
                        ['title' => 'Post Acc → Pch → Accept Pch', 'desc' => 'Accounting mengirim ke Purchasing; Purchasing menerima.'],
                        ['title' => 'Post Pch → Fin → Accept Fin', 'desc' => 'Purchasing mengirim kembali ke Finance; Finance menerima — siklus tertutup.'],
                        ['title' => 'Reverse', 'desc' => 'Transfer yang sudah diposting dapat dibatalkan/dikembalikan (reverse) apabila diperlukan.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Received', 'cls' => 'planned'],
                        ['label' => 'Post/Accepted (bergantian Fin↔Acc↔Pch)', 'cls' => 'progress'],
                        ['label' => 'Accepted Fin (selesai)', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'ir_kontrabon_h', 'desc' => 'Header Invoice Received versi baru (menu Invoice Received / kontrabon_new).'],
                        ['name' => 'ir_kontrabon_inv', 'desc' => 'Daftar invoice per Invoice Received.'],
                        ['name' => 'ir_kontrabon_faktur', 'desc' => 'Faktur pajak per invoice.'],
                        ['name' => 'ir_kontrabon_bpb', 'desc' => 'BPB per faktur.'],
                        ['name' => 'ir_invoice_supp_h', 'actions' => ['insert','update'], 'desc' => 'Header per batch penerimaan. Insert saat Invoice Received; Update status & tanggal setiap tahap Post/Accept berjalan.'],
                        ['name' => 'ir_invoice_supp', 'actions' => ['insert'], 'desc' => 'Detail — satu baris per nomor invoice fisik dalam batch.'],
                        ['name' => 'ir_trans_invoice_supp', 'actions' => ['insert','update'], 'desc' => 'Catatan tiap event transfer (Fin→Acc/Acc→Pch/Pch→Fin), termasuk update status saat di-approve.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'ir_status', 'desc' => 'Master label status.'],
                    ],
                    'notes' => [
'MENU LAMA - sudah TIDAK ada di sidebar. Fungsinya digabung ke menu Document Handover (satu halaman dengan pilihan Type Document: Invoice atau BPB & SJ). Entri ini dipertahankan sebagai rujukan alur lama; halaman invoice_received.php sendiri masih ada di server tetapi tidak lagi ditautkan dari menu.',
                        'Perbaikan (commit c034a17 dan 4423b91, Sep 2026): nomor Faktur dan No Invoice yang berupa STRIP (hanya tanda hubung) adalah penanda tanpa nomor sehingga boleh dipakai berkali-kali. Aturan tidak boleh dobel kini dilewati untuk strip - baik pada pengecekan daftar di layar maupun pada pengecekan server yang menolak invoice yang sudah dipakai supplier yang sama. Nomor asli tetap dijaga.',
                        'Perbaikan tampilan (commit 48b1b26, Sep 2026): halaman daftar memakai skin bersama module/css/app-skin.css - kartu, header tabel, badge status, tombol, kontrol DataTables (Show entries, Search, info, pagination), dropdown dan datepicker. Ditambah overlay spinner saat memuat dan mode responsif yang melipat kolom ke baris detail di layar kecil.',
                        'Status berjalan panjang dan bolak-balik antar 3 departemen: Received → Post Fin To Acc → Accepted Acc → Post Acc To Pch → Accepted Pch → Post Pch To Fin → Accepted Fin.',
                    ],
                ],
            ],
            'ir-report' => [
                'title' => 'IR Report', 'icon' => 'fa-tags', 'path' => 'module/AP/report_invoice_received.php',
                'doc' => [
                    'summary' => 'Laporan gabungan murni baca — menunjukkan sejauh mana satu invoice supplier sudah berjalan di seluruh rantai AP, dari diterima sampai dibayar.',
                    'purpose' => 'Dipakai sebagai dashboard "sudah sampai mana" untuk satu invoice supplier: tanggal IR, tanggal-tanggal BPB, tiap tahap transfer Invoice Received, tanggal Kontrabon, tanggal List Payment beserta approval-nya, sampai tanggal pembayaran aktual (Bank Out/Petty Cash Out).',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'ir_invoice_supp', 'desc' => 'Detail invoice yang diterima.'],
                        ['name' => 'ir_invoice_supp_h', 'desc' => 'Header & status tiap tahap serah-terima invoice.'],
                        ['name' => 'bpb', 'desc' => 'Tanggal BPB terkait.'],
                        ['name' => 'bpb_new', 'desc' => 'Tanggal verifikasi BPB.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'kontrabon', 'desc' => 'Tanggal Kontrabon terkait.'],
                        ['name' => 'list_payment', 'desc' => 'Tanggal List Payment & approval-nya.'],
                        ['name' => 'b_bankout_det', 'desc' => 'Tanggal pembayaran via Bank Out.'],
                        ['name' => 'b_bankout_h', 'desc' => 'Header Bank Out terkait.'],
                        ['name' => 'c_petty_cashout_det', 'desc' => 'Tanggal pembayaran via Petty Cash Out.'],
                        ['name' => 'c_petty_cashout_h', 'desc' => 'Header Petty Cash Out terkait.'],
                        ['name' => 'payment_ftr', 'desc' => 'Kanal pelunasan lama (legacy) yang juga dicek.'],
                    ],
                    'notes' => [
                        'Murni laporan baca — tidak ada satu pun aksi tulis dari halaman ini.',
                    ],
                ],
            ],
            'transfer-memo' => [
                'title' => 'Transfer Memo', 'icon' => 'fa-paper-plane', 'path' => 'module/AP/transfer_memo.php',
                'doc' => [
                    'summary' => 'Mengumpulkan memo biaya EXIM yang sudah disetujui menjadi satu batch transfer ke Finance — bukan tentang BPB/invoice, ini fitur terpisah untuk klaim biaya Export-Import.',
                    'purpose' => 'Sebuah "Memo" di sini adalah dokumen biaya/fee terkait transaksi Export-Import (bukan BPB atau invoice supplier). Transfer Memo dipakai untuk membundel memo yang sudah Approved menjadi satu batch, dikirim ke Finance, supaya nantinya bisa dibuatkan Payment Voucher EXIM.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Cari & pilih memo', 'desc' => 'Mencari memo EXIM berstatus sudah disetujui (status_transfer = "A-TETF") dan belum pernah ditransfer.'],
                        ['title' => 'Buat batch', 'desc' => 'Memo yang dipilih dibundel jadi satu batch baru (nomor format <code>TFTE/NAG/bulanTahun/urutan</code>), status POST.'],
                        ['title' => 'Cancel', 'desc' => 'Pembuat batch dapat membatalkannya sebelum di-approve Finance.'],
                    ],
                    'status_flow' => [
                        ['label' => 'POST', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'transfer_memo_exim_h', 'actions' => ['insert','update'], 'desc' => 'Header batch transfer memo. Insert saat dibuat, Update status saat Cancel.'],
                        ['name' => 'transfer_memo_exim_det', 'actions' => ['insert','update'], 'desc' => 'Detail memo dalam batch.'],
                        ['name' => 'memo_h', 'actions' => ['update'], 'desc' => 'Kolom status_transfer ditandai "TFTE" (sudah masuk batch transfer) — hanya update, tidak pernah membuat memo baru dari sini.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                    ],
                    'notes' => [
                        'Berada di bawah section "Document Tracking" karena polanya sama (batch → approve/accept → cancel), bukan karena objek dokumennya sama dengan BPB/Invoice.',
                    ],
                ],
            ],
            'approve-transfer-memo' => [
                'title' => 'Approve Transfer Memo', 'icon' => 'fa-thumbs-o-up', 'path' => 'module/AP/approve_transfer_memo.php',
                'doc' => [
                    'summary' => 'Antrian persetujuan Finance untuk batch Transfer Memo EXIM yang masuk dari Transfer Memo.',
                    'purpose' => 'Dipakai Finance untuk meninjau & menyetujui (atau membatalkan per baris) batch memo EXIM yang diajukan lewat menu Transfer Memo, sebelum memo-memo tersebut bisa dibuatkan Payment Voucher EXIM.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan batch pending', 'desc' => 'Hanya batch berstatus POST yang ditampilkan, dengan badge jumlah pending.'],
                        ['title' => 'Approve/Cancel per baris', 'desc' => 'Finance mencentang baris memo untuk disetujui (status_transfer memo jadi "A-TETF" final) atau ditolak (dikembalikan, dilepas dari batch).'],
                    ],
                    'status_flow' => [
                        ['label' => 'POST', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'memo_h', 'actions' => ['update'], 'desc' => 'status_transfer diperbarui final ("A-TETF") untuk baris yang disetujui, atau dikembalikan untuk baris yang ditolak.'],
                        ['name' => 'transfer_memo_exim_det', 'actions' => ['update'], 'desc' => 'Status baris diperbarui sesuai keputusan approve/tolak.'],
                        ['name' => 'transfer_memo_exim_h', 'actions' => ['update'], 'desc' => 'Status header batch diperbarui menjadi APPROVED setelah seluruh baris diputuskan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'transfer_memo_exim_h', 'desc' => 'Daftar batch pending.'],
                        ['name' => 'memo_h', 'desc' => 'Detail memo per batch.'],
                    ],
                    'notes' => [],
                ],
            ],
            '_s5' => ['section' => 'Kontra Bon'],
            'kontrabon-reg' => [
                'title' => 'Kontra Bon Reg', 'icon' => 'fa-ticket', 'path' => 'module/AP/kontrabon.php',
                'doc' => [
                    'summary' => 'Pembuatan dan pengelolaan Kontra Bon tipe Reguler — dokumen tagihan resmi yang mengubah satu/lebih BPB (dan opsional retur BPPB) milik satu supplier menjadi kewajiban bayar, lengkap dengan jurnal draf dan posting ke Kartu Hutang.',
                    'purpose' => 'Kontra Bon adalah dokumen inti proses AP: menggabungkan BPB yang sudah disetujui menjadi satu tagihan, dilengkapi berbagai penyesuaian (selisih kurs, qty, harga, materai, potongan pembelian, ekspedisi, MOQ) serta pajak, dan secara otomatis membentuk baris jurnal draf dan posting ke subledger hutang.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Pilih BPB & buat header', 'desc' => 'Nomor didapat dari pratinjau (format <code>SI/APR/YYYY/MM/NNNNN</code>, segmen profit center baru ditambahkan saat benar-benar disimpan); form memilih satu/lebih BPB berstatus siap tagih milik satu supplier, opsional menyertakan retur BPPB dan/atau FTR.'],
                        ['title' => 'Simpan detail', 'desc' => 'Header &amp; baris penyesuaian tersimpan berstatus draf, jurnal draf terbentuk otomatis per komponen, BPB sumber ditandai "Invoiced", dan Kartu Hutang ditautkan ke Kontra Bon ini.'],
                        ['title' => 'Approve', 'desc' => 'Lewat menu "Approve Kontrabon Reg" (endpoint sama): status menjadi "Approved", jurnal draf dikonfirmasi, Kartu Hutang dilengkapi data faktur pajak &amp; jatuh tempo.'],
                        ['title' => 'Cancel', 'desc' => 'Status menjadi "Cancel"; BPB sumber dikembalikan ke status "Waiting" agar bisa ditarik ulang, dan jurnal dibalik otomatis lewat baris baru berlabel "Reverse ...".'],
                        ['title' => 'Edit (revisi)', 'desc' => 'Tombol Edit mengunci dokumen ("Updating"), menyalin datanya ke tabel staging internal, lalu saat disimpan membentuk Kontra Bon revisi baru bernomor "...-REV_01" dst. — dokumen lama ditandai "Updated" dan BPB sumber dipindah kepemilikannya ke nomor revisi baru.'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel / Updated', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'kontrabon_h', 'actions' => ['insert', 'update'], 'desc' => 'Header Kontra Bon — dibuat berstatus draf, diperbarui saat approve ("Approved"), cancel ("Cancel"), mulai-edit ("Updating"), atau digantikan revisi baru ("Updated").'],
                        ['name' => 'kontrabon', 'actions' => ['insert', 'update', 'delete'], 'desc' => 'Baris detail per BPB — status mengikuti header saat approve/cancel; baris kosong sisa proses (tanpa No BPB/PO) dibersihkan otomatis setiap simpan.'],
                        ['name' => 'potongan', 'actions' => ['insert', 'update'], 'desc' => 'Baris penyesuaian (selisih kurs, qty, harga, materai, potongan beli, ekspedisi, MOQ) dan jumlah retur — status mengikuti header.'],
                        ['name' => 'kontrabon_ftr', 'actions' => ['insert', 'update'], 'desc' => 'Dibuat bila Kontra Bon menyertakan FTR; ditutup ("Cancel") baik saat header di-approve maupun di-cancel.'],
                        ['name' => 'return_kb', 'actions' => ['insert', 'update'], 'desc' => 'Dibuat bila Kontra Bon menyertakan retur BPPB; status mengikuti header.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert', 'update'], 'desc' => 'Baris jurnal draf per komponen penyesuaian & total dibuat saat simpan; dikonfirmasi ("Approved") saat approve; dibalik otomatis (baris baru berlabel "Reverse ...") saat cancel.'],
                        ['name' => 'kartu_hutang', 'actions' => ['update'], 'desc' => 'Baris hutang milik BPB terkait ditautkan ke Kontra Bon ini saat simpan, dilengkapi data faktur pajak & jatuh tempo saat approve, dilepas ("-") saat cancel.'],
                        ['name' => 'status', 'actions' => ['update'], 'desc' => 'Log ringkas lintas dokumen disinkronkan dengan nomor & tanggal Kontra Bon, dikosongkan saat cancel.'],
                        ['name' => 'bpb_new', 'actions' => ['update'], 'desc' => 'Ditandai "Invoiced" saat ditagih dalam Kontra Bon ini, dikembalikan ke "Waiting" saat Kontra Bon dibatalkan.'],
                        ['name' => 'bppb_new', 'actions' => ['update'], 'desc' => 'BPPB retur yang disertakan ditandai "Invoiced" & ditaut ke Kontra Bon ini, dilepas kembali ("Waiting") saat dibatalkan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'masterrate', 'desc' => 'Kurs harian, untuk konversi ke IDR pada Kontra Bon mata uang asing.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                    ],
                    'notes' => [
                        'Nomor pratinjau (sebelum disimpan) tidak menyertakan segmen profit center yang sebenarnya ditambahkan saat data benar-benar tersimpan, sehingga nomor yang tampil di layar berbeda dari nomor final — pola ini konsisten di seluruh varian Kontra Bon, termasuk semua tab Payment Voucher (Kontrabon).',
                        'Fitur Edit/revisi memakai satu set tabel staging internal (diawali <code>ap_edit_</code>) dan tabel <code>ap_journal_temp</code> yang tersembunyi di balik tombol Edit pada halaman ini — bukan menu tersendiri.',
                        'Menu ini menjadi backend bersama untuk tab Reguler & Installment pada "Payment Voucher (Kontrabon)" — keduanya menulis ke tabel <code>kontrabon_h</code>/<code>kontrabon</code> yang sama persis, hanya berbeda prefiks nomor dan alur approval. Lihat catatan disambiguasi pada menu tersebut.',
                        '<code>ap_reverse_h</code>/<code>ap_reverse_det</code> TIDAK pernah disentuh oleh alur ini — pembalikan jurnal saat Cancel dilakukan murni lewat baris baru berlabel "Reverse ..." di <code>tbl_list_journal</code>, mekanisme yang sepenuhnya terpisah dari modul Reverse.',
                    ],
                ],
            ],
            'kontrabon-ftr-cbd' => [
                'title' => 'Kontra Bon FTR CBD', 'icon' => 'fa-ticket', 'path' => 'module/AP/kontrabonftrcbd.php',
                'doc' => [
                    'summary' => 'Antrian approve/cancel untuk Kontra Bon tipe CBD (Cash Before Delivery) — dokumen tagihan yang dibentuk dari FTR CBD, bukan dari BPB.',
                    'purpose' => 'Menyetujui atau membatalkan Kontra Bon CBD, baik yang dibuat lewat menu ini sendiri maupun lewat tab CBD pada Payment Voucher (Kontrabon) — murni flip status dua tabel, tanpa membentuk jurnal maupun posting Kartu Hutang.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Baris Kontra Bon CBD berstatus draf, dikelompokkan per No PO, ditautkan ke FTR CBD asalnya.'],
                        ['title' => 'Approve', 'desc' => 'Status menjadi "Approved".'],
                        ['title' => 'Cancel', 'desc' => 'Status menjadi "Cancel", dan FTR CBD sumber dilepas kembali ("Waiting", tautan kosong) supaya bisa dipakai ulang.'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'kontrabon_cbd', 'actions' => ['update'], 'desc' => 'Baris detail — status & audit approve/cancel.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Header — status & audit approve/cancel.'],
                        ['name' => 'ftr_cbd', 'actions' => ['update'], 'desc' => 'FTR CBD sumber dikembalikan "Waiting" & tautannya dikosongkan saat Kontra Bon CBD ini dibatalkan.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Tidak ada posting ke <code>tbl_list_journal</code> maupun <code>kartu_hutang</code> di alur ini — pembayaran CBD memang tidak tercatat di General Ledger, hanya di subledger (kontras dengan Kontra Bon Reg berbasis BPB yang memposting jurnal penuh).',
                    ],
                ],
            ],
            'kontrabon-ftr-dp' => [
                'title' => 'Kontra Bon FTR DP', 'icon' => 'fa-ticket', 'path' => 'module/AP/kontrabonftrdp.php',
                'doc' => [
                    'summary' => 'Kembaran "Kontra Bon FTR CBD" untuk tipe DP (Down Payment) — dokumen tagihan uang muka yang dibentuk dari FTR DP.',
                    'purpose' => 'Menyetujui atau membatalkan Kontra Bon DP, baik yang dibuat lewat menu ini sendiri maupun lewat tab DP pada Payment Voucher (Kontrabon).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Baris Kontra Bon DP berstatus draf, ditautkan ke FTR DP asalnya.'],
                        ['title' => 'Approve', 'desc' => 'Status menjadi "Approved".'],
                        ['title' => 'Cancel', 'desc' => 'Status menjadi "Cancel", FTR DP sumber dilepas kembali ("Waiting", tautan kosong).'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'kontrabon_dp', 'actions' => ['update'], 'desc' => 'Baris detail — status & audit approve/cancel.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Header — status & audit approve/cancel.'],
                        ['name' => 'ftr_dp', 'actions' => ['update'], 'desc' => 'FTR DP sumber dikembalikan "Waiting" & tautannya dikosongkan saat Kontra Bon DP ini dibatalkan.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Sama seperti Kontra Bon FTR CBD — tidak posting ke jurnal umum maupun Kartu Hutang; pembayaran DP murni subledger.',
                    ],
                ],
            ],
            '_s6' => ['section' => 'Payment Voucher (Kontrabon)'],
            'payment-voucher-ap' => [
                'title' => 'Payment Voucher', 'icon' => 'fa-credit-card', 'path' => 'module/AP/payment-voucher-ap.php',
                'doc' => [
                    'summary' => 'Antarmuka terpadu & alur approval 2 tahap (First → Second) yang lebih baru untuk membuat dokumen yang SAMA dengan Kontra Bon (Reguler, Installment, DP, CBD) — bukan tabel/data terpisah, hanya "pintu masuk" lain menuju tabel kontrabon_h/kontrabon dkk. yang identik.',
                    'purpose' => 'Menyatukan pembuatan 4 tipe dokumen (Reguler, Installment/cicilan, DP, CBD) dalam satu halaman bertab, dengan alur approval 2 tahap (First Approval → Second Approval) menggantikan approval sekali-klik pada menu Kontra Bon klasik. Berlaku juga untuk 2 tipe tambahan yang muncul di antrian approval yang sama: Saldo Awal & Biaya (PV non-Kontrabon).',
                    'variants' => [
                        ['name' => 'Reguler', 'icon' => 'fa-file-text-o', 'desc' => 'Menulis ke kontrabon_h/kontrabon — tabel & file backend PERSIS SAMA dengan menu Kontra Bon Reg klasik, prefiks nomor "PV-AP/REG/...".'],
                        ['name' => 'Installment', 'icon' => 'fa-calendar', 'desc' => 'Menulis ke kontrabon_h/kontrabon plus rincian per cicilan di kontrabon_h_installment_detail, prefiks nomor "PV-AP/INS/...".'],
                        ['name' => 'DP', 'icon' => 'fa-arrow-down', 'desc' => 'Menulis ke kontrabon_h_dp/kontrabon_dp — tabel & file backend sama dengan Kontra Bon FTR DP klasik, prefiks nomor "PV-AP/DP/...".'],
                        ['name' => 'CBD', 'icon' => 'fa-money', 'desc' => 'Menulis ke kontrabon_h_cbd/kontrabon_cbd — tabel & file backend sama dengan Kontra Bon FTR CBD klasik, prefiks nomor "PV-AP/CBD/...".'],
                    ],
                    'flow' => [
                        ['title' => 'Pilih tab & isi form', 'desc' => 'Nomor pratinjau memakai prefiks "PV-AP/REG|INS|DP|CBD/..." (segmen profit center baru ditambahkan saat benar-benar disimpan, sama seperti pola Kontra Bon klasik).'],
                        ['title' => 'Simpan', 'desc' => 'Berstatus "draft" — memakai file backend YANG SAMA dengan alur klasik untuk tab Reguler/DP/CBD (insertkbon.php, ajaxdp.php, ajaxcbd.php dipakai bersama).'],
                        ['title' => 'First Approval', 'desc' => 'Status menjadi "FIRST APPROVED" — hanya mengubah status &amp; kolom audit tahap pertama, belum memposting jurnal/kartu hutang.'],
                        ['title' => 'Second Approval', 'desc' => 'Status menjadi "SECOND APPROVED" — untuk tipe Reguler/Installment, di titik inilah jurnal dikonfirmasi &amp; Kartu Hutang diperbarui (setara langkah Approve pada Kontra Bon klasik). Untuk tipe DP/CBD, tahap kedua justru dieksekusi dari halaman approval "PV Biaya" gabungan (approve-pv.php), bukan dari halaman Second Approval khusus PV-AP.'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'FIRST APPROVED', 'cls' => 'progress'],
                        ['label' => 'SECOND APPROVED', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'kontrabon_h', 'actions' => ['insert', 'update'], 'desc' => 'Tab Reguler/Installment — tabel identik dengan Kontra Bon Reg klasik, ditambah kolom rekening bank sumber dana (id_bank_account, from_account, from_bank, from_bank_curr) yang tidak ada di alur klasik.'],
                        ['name' => 'kontrabon', 'actions' => ['insert', 'update'], 'desc' => 'Tab Reguler/Installment — detail per BPB, file backend sama persis dengan Kontra Bon Reg klasik (insertkbon.php).'],
                        ['name' => 'kontrabon_h_installment_detail', 'actions' => ['insert', 'update'], 'desc' => 'Tab Installment — satu baris per cicilan (nomor cicilan, termin, tanggal jatuh tempo, DPP/PPN/PPh, total); status header ikut berubah hanya setelah SELURUH baris cicilan mencapai status yang sama.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['insert', 'update'], 'desc' => 'Tab DP — tabel identik dengan Kontra Bon FTR DP klasik, ditambah kolom rekening bank sumber dana.'],
                        ['name' => 'kontrabon_dp', 'actions' => ['insert', 'update'], 'desc' => 'Tab DP — detail, ditautkan ke ftr_dp.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['insert', 'update'], 'desc' => 'Tab CBD — tabel identik dengan Kontra Bon FTR CBD klasik, ditambah kolom rekening bank sumber dana.'],
                        ['name' => 'kontrabon_cbd', 'actions' => ['insert', 'update'], 'desc' => 'Tab CBD — detail, ditautkan ke ftr_cbd.'],
                        ['name' => 'potongan', 'actions' => ['insert', 'update'], 'desc' => 'Tab Reguler/Installment — baris penyesuaian, sama dengan alur klasik.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert', 'update'], 'desc' => 'Tab Reguler/Installment — jurnal draf dibuat saat simpan, dikonfirmasi saat Second Approval, dibalik ("Reverse ...") saat cancel. Tab DP/CBD tidak menyentuh tabel ini sama sekali.'],
                        ['name' => 'kartu_hutang', 'actions' => ['update'], 'desc' => 'Hanya tab Reguler/Installment — DP/CBD tidak pernah menyentuh tabel ini karena keduanya adalah pembayaran di muka yang tidak masuk GL.'],
                        ['name' => 'ftr_dp', 'actions' => ['update'], 'desc' => 'Tab DP — ditandai "Invoiced" & ditaut ke Kontra Bon DP ini saat disimpan.'],
                        ['name' => 'ftr_cbd', 'actions' => ['update'], 'desc' => 'Tab CBD — ditandai "Invoiced" & ditaut ke Kontra Bon CBD ini saat disimpan.'],
                        ['name' => 'pv_mapping_jurnal_dp', 'actions' => ['select'], 'desc' => 'Tabel pemetaan akun COA bersama untuk tab DP maupun CBD (tidak ada tabel pemetaan khusus CBD terpisah), dipakai untuk menentukan akun jurnal berdasarkan tipe item & area supplier.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier — sumber penentuan akun COA pada tab DP/CBD.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs harian.'],
                    ],
                    'notes' => [
                        'DISAMBIGUASI PENTING: menu ini BUKAN "Payment Voucher" pada grup menu Bank (yang menulis ke tbl_pv_h/tbl_pv, tipe "Biaya", voucher pengeluaran berdiri sendiri). Menu ini adalah antarmuka baru di atas tabel Kontra Bon yang sama dengan menu Kontra Bon Reg/FTR CBD/FTR DP klasik — dua UI berbeda menulis ke satu set tabel yang identik.',
                        'Untuk tipe Reguler, antrian approval (First/Second) hanya menampilkan Kontra Bon dengan tanggal dibuat pada atau setelah 1 Juli 2026 (nilai tetap tertanam di kode) — tipe Installment/DP/CBD/Saldo Awal tidak memiliki batas tanggal ini.',
                        'Antrian approval yang sama juga mencakup 2 tipe dokumen di luar 4 tab pembuatan di atas: "Saldo Awal" (ap_saldo_payment_voucher, PV pembukaan saldo) dan "Biaya" (tbl_pv_h/tbl_pv, sekali-klik — inilah PV Bank yang disebutkan pada catatan disambiguasi di atas, ikut nampang di antrian yang sama meski berasal dari modul berbeda).',
                        'Tombol Edit memakai jalur staging yang sama dengan Kontra Bon Reg klasik (form_edit_pv_regular.php adalah kembaran form_edit_kontrabon.php, keduanya dipicu dari copy_data_kontrabon.php yang sama).',
                    ],
                ],
            ],
            'payment-voucher-list' => [
                'title' => 'Payment Voucher List', 'icon' => 'fa-list-alt', 'path' => 'module/AP/payment-voucher-list.php',
                'doc' => [
                    'summary' => 'Lapisan pengelompokan (batching) satu tahap untuk dokumen AP yang sudah final-approved — mengumpulkan Kontra Bon/PV dari berbagai tipe ke dalam satu daftar sebelum diproses ke tahap List Payment.',
                    'purpose' => 'Mengelompokkan dokumen yang sudah disetujui (Kontra Bon Reguler/Installment/DP/CBD, PV Saldo Awal, PV Biaya) menjadi satu batch Payment Voucher List, ditandai lewat kolom penanda status_pvl pada masing-masing tabel dokumen sumber (bukan pada tabel header PVL itu sendiri).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar batch', 'desc' => 'Header pv_payment_voucher_list_h berstatus "Draft", dengan detail per dokumen di pv_payment_voucher_list_det.'],
                        ['title' => 'Approve', 'desc' => 'Lewat menu "Approve Payment Voucher List" (halaman terpisah): status header menjadi "APPROVED", dan setiap dokumen sumber pada detail ditandai status_pvl = "APPROVED".'],
                        ['title' => 'Cancel', 'desc' => 'Hanya bisa dibatalkan selama masih "Draft" — status header &amp; detail menjadi "Cancel", penanda status_pvl pada dokumen sumber dikosongkan kembali.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pv_payment_voucher_list_h', 'actions' => ['update'], 'desc' => 'Header batch — status (kolom bernama polos "status", bukan "status_pvl") berubah Draft/APPROVED/Cancel.'],
                        ['name' => 'pv_payment_voucher_list_det', 'actions' => ['update'], 'desc' => 'Baris detail per dokumen dalam batch — status mengikuti header.'],
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Penanda status_pvl diisi/dikosongkan untuk dokumen tipe Reguler yang masuk batch ini.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Penanda status_pvl untuk dokumen tipe DP.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Penanda status_pvl untuk dokumen tipe CBD.'],
                        ['name' => 'kontrabon_h_installment_detail', 'actions' => ['update'], 'desc' => 'Penanda status_pvl per baris cicilan untuk dokumen tipe Installment.'],
                        ['name' => 'ap_saldo_payment_voucher', 'actions' => ['update'], 'desc' => 'Penanda status_pvl untuk dokumen tipe Saldo Awal.'],
                        ['name' => 'tbl_pv_h', 'actions' => ['update'], 'desc' => 'Penanda status_pvl untuk dokumen tipe Biaya (PV Bank non-Kontrabon).'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Berbeda dari "Payment List" pada grup menu Bank (pv_payment_list_h/det, kolom status_pl, approval 2 tahap, mensyaratkan rekening dana & berlanjut ke Transfer List) — dua lapisan batching ini tampak tumpang tindih secara fungsi dan kemungkinan besar hasil duplikasi organik, bukan desain yang disengaja. Payment Voucher List (menu ini) hanya satu tahap approval dan tidak mensyaratkan rekening dana.',
                        'Nama kolom status_pvl bisa menyesatkan: kolom ini TIDAK berada di tabel header pv_payment_voucher_list_h itu sendiri (yang memakai kolom polos "status"), melainkan ditulis balik ke tabel-tabel dokumen sumber sebagai penanda "sudah masuk batch mana".',
                    ],
                ],
            ],
            '_s7' => ['section' => 'List Payment'],
            'list-payment-reg' => [
                'title' => 'List Payment Reg', 'icon' => 'fa-tags', 'path' => 'module/AP/payment.php',
                'doc' => [
                    'summary' => 'Pengelompokan (batching) Kontra Bon Reguler yang sudah disetujui menjadi satu dokumen List Payment bernomor — lapisan wajib antara Kontra Bon dan pembayaran aktual (Payment).',
                    'purpose' => 'List Payment mengumpulkan satu atau lebih Kontra Bon Reguler berstatus "Approved" menjadi satu batch, yang harus disetujui lalu "ditutup" (Closing) sebelum bisa dijadikan dasar pembuatan Payment.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Buat List Payment', 'desc' => 'Form memilih Kontra Bon Reguler berstatus Approved; disimpan berstatus draf, menahan sebagian saldo (balance) Kontra Bon sumber, dan menandainya sebagai sudah masuk List Payment (lp_inv = "1").'],
                        ['title' => 'Approve', 'desc' => 'Status menjadi "Approved"; outstanding per baris dikurangi.'],
                        ['title' => 'Ditutup (Closing)', 'desc' => 'Lewat menu "Close Payment Reg" terpisah: status menjadi "Closed" — inilah syarat wajib sebelum Payment bisa dibuat terhadap List Payment ini.'],
                        ['title' => 'Dibayar', 'desc' => 'Setelah dokumen Payment (menu terpisah) dibuat terhadap List Payment yang sudah Closed, status akhirnya berubah menjadi "Paid".'],
                        ['title' => 'Cancel', 'desc' => 'Status menjadi "Cancel"; saldo Kontra Bon sumber dikembalikan.'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Closed', 'cls' => 'progress'],
                        ['label' => 'Paid', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'list_payment', 'actions' => ['insert', 'update'], 'desc' => 'Header/detail List Payment — dibuat berstatus draf, status berubah saat approve/cancel; kolom outstanding dikurangi saat approve.'],
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Sebagian balance ditahan saat List Payment dibuat, dikembalikan bila List Payment ini dibatalkan.'],
                        ['name' => 'kontrabon', 'actions' => ['update'], 'desc' => 'Ditandai lp_inv = "1" saat dimasukkan ke List Payment ini.'],
                        ['name' => 'status', 'actions' => ['update'], 'desc' => 'Log ringkas lintas dokumen disinkronkan dengan nomor & tanggal List Payment, dikosongkan saat cancel.'],
                        ['name' => 'saldo_awal', 'actions' => ['update'], 'desc' => 'Baris saldo awal (carry-forward) lama yang disatukan (UNION) ke tampilan List Payment Reg — ikut diproses draft/Approved/Cancel oleh backend yang sama; khusus warisan data lama, tidak relevan untuk Kontra Bon baru.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Nomor: LP/NAG/MMYY/00001.',
                        '"Closed" tidak memiliki nilai status_int tersendiri — ia berbagi status_int = 4 yang sama dengan "Approved", sehingga hanya kolom status (teks) yang membedakan sudah ditutup atau belum, bukan status_int.',
                        'List Payment adalah PRASYARAT WAJIB sebelum Payment bisa dibuat — lihat menu Close Payment Reg dan Payment Reg untuk kelanjutan alurnya. Urutan menu di sidebar (List Payment, Payment, Closing Payment) dapat menyesatkan; urutan proses sesungguhnya adalah List Payment → Closing → Payment.',
                    ],
                ],
            ],
            'list-payment-cbd' => [
                'title' => 'List Payment CBD', 'icon' => 'fa-tags', 'path' => 'module/AP/listpaymentcbd.php',
                'doc' => [
                    'summary' => 'Kembaran "List Payment Reg" untuk Kontra Bon tipe CBD (Cash Before Delivery).',
                    'purpose' => 'Mengelompokkan Kontra Bon CBD berstatus "Approved" menjadi satu batch List Payment CBD, sebelum bisa ditutup (Closing) dan dijadikan dasar Payment CBD.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Buat List Payment', 'desc' => 'Memilih Kontra Bon CBD Approved; disimpan draf, menahan balance Kontra Bon CBD sumber, menandai lp_inv = "1".'],
                        ['title' => 'Approve', 'desc' => 'Status menjadi "Approved".'],
                        ['title' => 'Cancel', 'desc' => 'Status menjadi "Cancel"; balance Kontra Bon CBD dikembalikan.'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Closed', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'list_payment_cbd', 'actions' => ['insert', 'update'], 'desc' => 'Header/detail List Payment CBD — status draft/Approved/Cancel, outstanding dikurangi saat approve.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Balance ditahan saat dibuat, dikembalikan saat dibatalkan.'],
                        ['name' => 'kontrabon_cbd', 'actions' => ['update'], 'desc' => 'Ditandai lp_inv = "1".'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Nomor: LP/CBD/NAG/MMYY/00001.',
                        'Bug ditemukan: query penomoran (getnomor_lpcbd.php) memiliki pengecualian satu nomor historis tertentu yang di-hardcode permanen di kode produksi — kemungkinan tambalan sementara di masa lalu untuk menghindari satu dokumen bermasalah yang tidak pernah dibersihkan.',
                        'Tidak seperti List Payment Reg, tidak ada tabel status/saldo_awal yang terlibat di sini.',
                    ],
                ],
            ],
            'list-payment-dp' => [
                'title' => 'List Payment DP', 'icon' => 'fa-tags', 'path' => 'module/AP/listpaymentdp.php',
                'doc' => [
                    'summary' => 'Kembaran "List Payment Reg" untuk Kontra Bon tipe DP (Down Payment).',
                    'purpose' => 'Mengelompokkan Kontra Bon DP berstatus "Approved" menjadi satu batch List Payment DP, sebelum bisa ditutup (Closing) dan dijadikan dasar Payment DP.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Buat List Payment', 'desc' => 'Memilih Kontra Bon DP Approved; disimpan draf, menahan balance Kontra Bon DP sumber, menandai lp_inv = "1".'],
                        ['title' => 'Approve', 'desc' => 'Status menjadi "Approved".'],
                        ['title' => 'Cancel', 'desc' => 'Status menjadi "Cancel"; balance Kontra Bon DP dikembalikan.'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Closed', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'list_payment_dp', 'actions' => ['insert', 'update'], 'desc' => 'Header/detail List Payment DP — status draft/Approved/Cancel, outstanding dikurangi saat approve.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Balance ditahan saat dibuat, dikembalikan saat dibatalkan.'],
                        ['name' => 'kontrabon_dp', 'actions' => ['update'], 'desc' => 'Ditandai lp_inv = "1".'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Nomor: LP/DP/NAG/MMYY/00001.',
                        'Bug ditemukan: salah satu cabang filter (supplier tertentu + status tertentu, tanpa rentang tanggal) memiliki salah ketik SQL ("here" alih-alih "where") pada listpaymentdp.php, menyebabkan query gagal secara diam-diam dan tabel tampil kosong tanpa pesan error saat kombinasi filter tersebut dipakai.',
                    ],
                ],
            ],
            '_s8' => ['section' => 'Payment'],
            'payment-reg' => [
                'title' => 'Payment Reg', 'icon' => 'fa-tags', 'path' => 'module/AP/pelunasanftr.php',
                'doc' => [
                    'summary' => 'Pencatatan pembayaran aktual (disbursement) terhadap List Payment Reguler yang sudah Closed — titik jurnal umum benar-benar terbentuk untuk siklus AP Reguler berbasis BPB.',
                    'purpose' => 'Menyelesaikan alur Kontra Bon → List Payment → Closing → Payment dengan mencatat pembayaran final, memposting jurnal ke buku besar, dan melengkapi Kartu Hutang. Nama berkas "pelunasanftr.php" menyisakan istilah lama, namun menu ini murni "Payment Reg" — tidak terbatas pada dokumen berbasis FTR saja.',
                    'variants' => [
                        ['name' => 'Jalur List Payment (Normal)', 'icon' => 'fa-list-alt', 'desc' => 'Membayar terhadap List Payment Reguler yang berstatus "Closed" — jalur baku, memposting jurnal "Payment Non Bank" ke tbl_list_journal.'],
                        ['name' => 'Jalur PV Langsung (Bypass)', 'icon' => 'fa-bolt', 'desc' => 'Membayar langsung dari dokumen Payment Voucher (Reguler/Installment/DP/CBD/Saldo Awal) yang sudah "SECOND APPROVED", tanpa melalui List Payment sama sekali — tetap memposting ke tbl_list_journal meski sumbernya DP/CBD, yang pada jalur baku (menu Payment CBD/Payment DP) tidak pernah menyentuh jurnal umum.'],
                    ],
                    'flow' => [
                        ['title' => 'Pilih List Payment Closed', 'desc' => 'Form pembuatan hanya menampilkan List Payment berstatus "Closed" dengan total Kontrabon tidak nol sebagai kandidat pembayaran.'],
                        ['title' => 'Simpan', 'desc' => 'Header payment_ftr tersimpan berstatus draf, jurnal "Payment Non Bank" diposting ke tbl_list_journal, Kartu Hutang dilengkapi (plus baris "Selisih Kurs" bila mata uang asing), dan status List Payment berubah menjadi "Paid".'],
                        ['title' => 'Approve', 'desc' => 'Lewat menu "Approve Payment Reg" terpisah: status menjadi "Approved".'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'payment_ftr', 'actions' => ['insert', 'update'], 'desc' => 'Header pembayaran — dibuat berstatus draf (ditandai type_pv untuk jalur PV Langsung), status berubah "Approved" lewat menu approval terpisah.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert'], 'desc' => 'Jurnal "Payment Non Bank" diposting saat pembayaran disimpan — berlaku untuk kedua jalur (Normal maupun PV Langsung), termasuk saat sumbernya Kontrabon DP/CBD lewat jalur PV Langsung.'],
                        ['name' => 'kartu_hutang', 'actions' => ['insert'], 'desc' => 'Baris "Payment" diposting; ditambah baris kedua berketerangan "Selisih Kurs" apabila mata uang bukan IDR.'],
                        ['name' => 'payment_ftr_det', 'actions' => ['insert'], 'desc' => 'Hanya terisi apabila pengguna menambahkan baris penyesuaian debit/kredit manual.'],
                        ['name' => 'list_payment', 'actions' => ['update'], 'desc' => 'Status berubah final menjadi "Paid" setelah Payment berhasil dibuat (khusus jalur Normal).'],
                        ['name' => 'status', 'actions' => ['update'], 'desc' => 'Log ringkas lintas dokumen disinkronkan dengan nomor & tanggal Payment.'],
                    ],
                    'tables_read' => [
                        ['name' => 'b_bankout_h', 'desc' => 'Dibaca (jalur PV Langsung) untuk memeriksa apakah dokumen sumber sudah pernah dibayar lewat Bank Out, mencegah pembayaran ganda lintas kanal.'],
                        ['name' => 'b_bankout_det', 'desc' => 'Sama seperti b_bankout_h, level detail.'],
                        ['name' => 'c_petty_cashout_h', 'desc' => 'Dibaca (jalur PV Langsung) untuk memeriksa apakah dokumen sumber sudah pernah dibayar lewat Petty Cash Out.'],
                        ['name' => 'c_petty_cashout_det', 'desc' => 'Sama seperti c_petty_cashout_h, level detail.'],
                    ],
                    'notes' => [
                        'PENTING: jalur "PV Langsung" pada form pembuatan Payment Reg dapat memposting Kontrabon tipe DP/CBD ke jurnal umum (tbl_list_journal) — bertentangan dengan sifat DP/CBD yang subledger-only pada jalur List Payment baku (lihat Payment CBD/Payment DP). Perbedaan ini murni tergantung jalur pembayaran yang dipilih pengguna, bukan tipe dokumennya.',
                        'Nomor: jalur Normal PAY/LP/NAG/MMYY/00001; jalur PV Langsung PAY/LP/{profit_center}/MMYY/00001 (segmen profit center, bukan "NAG" tetap).',
                        'Meski tidak pernah menulis ke tabel Bank Out/Petty Cash Out, jalur PV Langsung membaca kedua tabel tersebut untuk mencegah dokumen yang sama dibayar dua kali lewat kanal berbeda.',
                        'Terdapat tombol Approve/Cancel inline pada halaman ini yang sudah tidak aktif (disembunyikan dari HTML) — fungsi approve/cancel sesungguhnya sudah dipindah sepenuhnya ke menu "Approve Payment Reg" terpisah.',
                    ],
                ],
            ],
            'payment-cbd' => [
                'title' => 'Payment CBD', 'icon' => 'fa-tags', 'path' => 'module/AP/pelunasanftrcbd.php',
                'doc' => [
                    'summary' => 'Pencatatan pembayaran aktual terhadap List Payment CBD yang sudah Closed — murni subledger, tidak memposting jurnal umum lewat jalur bakunya.',
                    'purpose' => 'Menyelesaikan alur Kontra Bon CBD → List Payment CBD → Closing → Payment dengan mencatat pembayaran final di Kartu Hutang. Berbeda dari Payment Reg, menu ini tidak memiliki UI approval yang bisa dijangkau (lihat catatan).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Pilih List Payment CBD Closed', 'desc' => 'Hanya baris dengan list_payment_cbd.status_int = "4" dan kontrabon_cbd.status = "Approved" yang bisa dipilih sebagai kandidat pembayaran.'],
                        ['title' => 'Simpan', 'desc' => 'Header payment_ftrcbd tersimpan berstatus draf, Kartu Hutang diposting dengan keterangan "Payment CBD" (plus baris "Selisih Kurs" bila mata uang asing), dan list_payment_cbd.status_int berubah menjadi 5 — kolom teks status TETAP "Closed", tidak pernah berubah menjadi "Paid".'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                    ],
                    'tables_write' => [
                        ['name' => 'payment_ftrcbd', 'actions' => ['insert'], 'desc' => 'Header pembayaran CBD, berstatus draf.'],
                        ['name' => 'kartu_hutang', 'actions' => ['insert'], 'desc' => 'Baris "Payment CBD" diposting; ditambah baris "Selisih Kurs" apabila mata uang bukan IDR.'],
                        ['name' => 'list_payment_cbd', 'actions' => ['update'], 'desc' => 'Kolom status_int berubah menjadi 5 (sudah dibayar) — kolom teks status TIDAK ikut berubah, tetap "Closed".'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Berbeda dari Payment Reg, jalur ini TIDAK PERNAH memposting ke tbl_list_journal — murni subledger (Kartu Hutang saja).',
                        'Kolom teks status pada list_payment_cbd tidak pernah berubah menjadi "Paid" seperti Payment Reg — perubahan hanya terlihat pada status_int (4 → 5) yang tidak ditampilkan di UI manapun, sehingga secara visual List Payment CBD yang sudah dibayar tetap tampak "Closed" selamanya.',
                        'Terdapat endpoint approve (approvepelunasanftrcbd.php) di backend, namun tidak ada menu maupun tombol aktif manapun di aplikasi yang menjangkaunya — dokumen Payment CBD secara praktis tidak pernah keluar dari status "draft" lewat jalur resmi. Kemungkinan celah desain yang perlu ditinjau ulang.',
                        'Nomor PAY/LP/CBD/NAG/MMYY/00001, dihitung di sisi klien lewat MAX(payment_ftr_id)+1 tanpa penguncian transaksi — berisiko duplikasi nomor apabila dua pengguna membuat dokumen bersamaan, dan penomoran tidak pernah reset per bulan meski formatnya menyertakan segmen bulan/tahun.',
                    ],
                ],
            ],
            'payment-dp' => [
                'title' => 'Payment DP', 'icon' => 'fa-tags', 'path' => 'module/AP/pelunasanftrdp.php',
                'doc' => [
                    'summary' => 'Kembaran "Payment CBD" untuk Kontra Bon tipe DP — pencatatan pembayaran aktual terhadap List Payment DP yang sudah Closed, murni subledger.',
                    'purpose' => 'Menyelesaikan alur Kontra Bon DP → List Payment DP → Closing → Payment dengan mencatat pembayaran final di Kartu Hutang.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Pilih List Payment DP Closed', 'desc' => 'Hanya baris dengan list_payment_dp.status_int = "4" dan kontrabon_dp.status = "Approved" yang bisa dipilih.'],
                        ['title' => 'Simpan', 'desc' => 'Header payment_ftrdp tersimpan berstatus draf, Kartu Hutang diposting dengan keterangan "Payment DP" (plus baris "Selisih Kurs" bila mata uang asing), dan list_payment_dp.status_int berubah menjadi 5 — kolom teks status TETAP "Closed".'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                    ],
                    'tables_write' => [
                        ['name' => 'payment_ftrdp', 'actions' => ['insert'], 'desc' => 'Header pembayaran DP, berstatus draf.'],
                        ['name' => 'kartu_hutang', 'actions' => ['insert'], 'desc' => 'Baris "Payment DP" diposting; ditambah baris "Selisih Kurs" apabila mata uang bukan IDR.'],
                        ['name' => 'list_payment_dp', 'actions' => ['update'], 'desc' => 'Kolom status_int berubah menjadi 5 — kolom teks status TIDAK ikut berubah, tetap "Closed".'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Tidak pernah memposting ke tbl_list_journal — murni subledger, sama seperti Payment CBD.',
                        'Sama seperti Payment CBD: endpoint approve (approvepelunasanftrdp.php) ada di backend namun tidak terjangkau UI manapun — dokumen Payment DP secara praktis tidak pernah keluar dari status "draft".',
                        'Nomor PAY/LP/DP/NAG/MMYY/00001, pola penomoran client-side yang sama (berisiko duplikasi, tidak reset per bulan) seperti Payment CBD.',
                    ],
                ],
            ],
            '_s9' => ['section' => 'Closing Payment'],
            'close-payment-reg' => [
                'title' => 'Close Payment Reg', 'icon' => 'fa-tags', 'path' => 'module/AP/form-closing-payreg.php',
                'doc' => [
                    'summary' => 'Menutup ("Closing") List Payment Reguler yang sudah Approved — langkah wajib sebelum Payment Reg bisa dibuat terhadapnya, meski urutannya di sidebar terletak setelah menu Payment.',
                    'purpose' => 'Menandai List Payment Reguler sebagai siap dibayar dengan mengubah statusnya menjadi "Closed", satu-satunya prasyarat yang diperiksa oleh form pembuatan Payment Reg.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Pilih List Payment Approved', 'desc' => 'Menampilkan List Payment Reguler berstatus "Approved".'],
                        ['title' => 'Tutup', 'desc' => 'UPDATE status menjadi "Closed" beserta closed_date/closed_by, hanya berlaku pada baris yang statusnya masih persis "Approved".'],
                    ],
                    'status_flow' => [
                        ['label' => 'Approved', 'cls' => 'planned'],
                        ['label' => 'Closed', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'list_payment', 'actions' => ['update'], 'desc' => 'Status berubah "Approved" → "Closed" beserta audit closed_date/closed_by.'],
                        ['name' => 'saldo_awal', 'actions' => ['update'], 'desc' => 'Cabang paralel untuk baris saldo awal (carry-forward) warisan lama, mengikuti pola closing yang sama.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'PENTING: Closing harus terjadi SEBELUM Payment dibuat, bukan sesudahnya — walau urutan menu di sidebar (List Payment, Payment, Closing Payment) dapat menyesatkan pembacanya. Menu Payment Reg hanya menampilkan List Payment berstatus "Closed" sebagai kandidat pembayaran.',
                        'status_int TIDAK berubah saat Closing (tetap 4, sama dengan Approved) — hanya kolom teks status yang membedakan "Approved" dari "Closed".',
                        'Terdapat handler tombol Cancel pada halaman ini yang menunjuk ke cancel_reverse_ap.php (bagian dari subsistem "AP Reverse" yang sepenuhnya terpisah, mengubah status pada tabel ap_reverse_h) — namun tidak ada tombol Cancel apapun pada HTML halaman ini. Kode mati sisa salin-tempel dari halaman lain, tidak pernah benar-benar berjalan.',
                    ],
                ],
            ],
            'close-payment-cbd' => [
                'title' => 'Close Payment CBD', 'icon' => 'fa-tags', 'path' => 'module/AP/formclosing-paycbd.php',
                'doc' => [
                    'summary' => 'Kembaran "Close Payment Reg" untuk List Payment CBD.',
                    'purpose' => 'Menandai List Payment CBD sebagai siap dibayar dengan mengubah statusnya menjadi "Closed" — prasyarat sebelum Payment CBD bisa dibuat.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Pilih List Payment CBD Approved', 'desc' => 'Menampilkan List Payment CBD berstatus "Approved".'],
                        ['title' => 'Tutup', 'desc' => 'Status berubah "Approved" → "Closed" beserta closed_date/closed_by.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Approved', 'cls' => 'planned'],
                        ['label' => 'Closed', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'list_payment_cbd', 'actions' => ['update'], 'desc' => 'Status berubah "Approved" → "Closed" beserta audit closed_date/closed_by.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Tidak ada tabel lain yang tersentuh — jauh lebih sederhana dari Close Payment Reg (tidak ada cabang saldo_awal maupun kode mati terkait AP Reverse).',
                    ],
                ],
            ],
            'close-payment-dp' => [
                'title' => 'Close Payment DP', 'icon' => 'fa-tags', 'path' => 'module/AP/formclosing-paydp.php',
                'doc' => [
                    'summary' => 'Kembaran "Close Payment Reg" untuk List Payment DP.',
                    'purpose' => 'Menandai List Payment DP sebagai siap dibayar dengan mengubah statusnya menjadi "Closed" — prasyarat sebelum Payment DP bisa dibuat.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Pilih List Payment DP Approved', 'desc' => 'Menampilkan List Payment DP berstatus "Approved".'],
                        ['title' => 'Tutup', 'desc' => 'Status berubah "Approved" → "Closed" beserta closed_date/closed_by.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Approved', 'cls' => 'planned'],
                        ['label' => 'Closed', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'list_payment_dp', 'actions' => ['update'], 'desc' => 'Status berubah "Approved" → "Closed" beserta audit closed_date/closed_by.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Struktur identik dengan Close Payment CBD, hanya berbeda tabel target.',
                    ],
                ],
            ],
            'closing-info' => [
                'title' => 'Closing Info', 'icon' => 'fa-tags', 'path' => 'module/AP/status_closing.php',
                'doc' => [
                    'summary' => 'Dasbor riwayat penutupan (Closing) List Payment Reguler — murni baca, tanpa aksi apapun.',
                    'purpose' => 'Menyediakan jejak audit tanggal Approve dan Closed untuk List Payment Reguler, sebagai referensi cepat tanpa perlu membuka halaman List Payment/Close Payment satu per satu.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan riwayat', 'desc' => 'Menggabungkan (UNION) list_payment dan saldo_awal berdasarkan tanggal confirm/closed/payment untuk membentuk satu jejak audit closing.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'list_payment', 'desc' => 'Sumber utama riwayat closing List Payment Reguler.'],
                        ['name' => 'saldo_awal', 'desc' => 'Baris saldo awal (carry-forward) lama yang digabungkan ke tampilan yang sama.'],
                    ],
                    'notes' => [
                        'Hanya mencakup Reguler — tidak ada laporan setara yang menggabungkan list_payment_cbd/list_payment_dp, sehingga riwayat closing CBD/DP tidak memiliki halaman pelaporan khusus dalam kelompok menu ini.',
                    ],
                ],
            ],
            '_s10' => ['section' => 'Status'],
            'status' => [
                'title' => 'Status', 'icon' => 'fa-paperclip', 'path' => 'module/AP/status.php',
                'doc' => [
                    'summary' => 'Pencarian status satu BPB di seluruh tahap siklus AP dalam satu baris — murni laporan baca, tidak ada aksi tulis apa pun.',
                    'purpose' => 'Dipakai untuk menelusuri "sudah sampai mana" satu BPB: tanggal BPB, tanggal verifikasi, Kontrabon terkait, List Payment terkait beserta approval/closing-nya, sampai tanggal pembayaran — semua dalam satu baris per BPB. Filter tanggal dapat diarahkan ke salah satu dari 4 tahap (BPB/Kontrabon/List Payment/Payment Date).',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'bpb', 'desc' => 'Data BPB utama.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'act_costing', 'desc' => 'Data costing terkait, untuk menurunkan info WS/Style.'],
                        ['name' => 'so', 'desc' => 'Sales Order terkait.'],
                        ['name' => 'jo_det', 'desc' => 'Detail Job Order, sumber WS/Style.'],
                        ['name' => 'bpb_new', 'desc' => 'Tanggal verifikasi BPB.'],
                        ['name' => 'kontrabon', 'desc' => 'Kontrabon terkait.'],
                        ['name' => 'list_payment', 'desc' => 'List Payment terkait beserta tanggal approve/closed.'],
                        ['name' => 'b_bankout_det', 'desc' => 'Tanggal pembayaran via Bank Out.'],
                        ['name' => 'b_bankout_h', 'desc' => 'Header Bank Out terkait.'],
                        ['name' => 'payment_ftr', 'desc' => 'Kanal pelunasan lama (legacy) yang juga dicek.'],
                    ],
                    'notes' => [
                        'Halaman ini tidak memiliki alur status sendiri — ia hanya membaca & menampilkan status yang sudah dimiliki oleh fitur BPB/Kontrabon/List Payment lain.',
                    ],
                ],
            ],
            '_s11' => ['section' => 'Report'],
            'ap-report-legacy' => [
                'title' => 'AP Report (Apr 2022 - Dec 2025)', 'icon' => 'fa-tags', 'path' => 'module/AP/pcs_detail.php',
                'doc' => [
                    'summary' => 'Laporan Kartu Hutang (Payable Card Statement/aging AP) versi PERTAMA — arsip beku untuk periode April 2022 s.d. Desember 2025, dipertahankan apa adanya supaya angka historis tidak berubah.',
                    'purpose' => 'Menampilkan saldo & mutasi hutang usaha per supplier (BPB, Kontrabon, List Payment, ringkasan per supplier/tipe) untuk periode lama. Ada 3 versi "AP Report" di menu ini (lihat catatan teknis) — file ini adalah versi tertua, khusus periode yang sudah lewat.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Generate laporan', 'desc' => 'Setiap kali dijalankan, sistem MENGHAPUS lalu MENULIS ULANG baris staging milik pengguna yang login ke tabel <code>rpt_ap_bpb</code>/<code>rpt_ap_kbon</code>/<code>rpt_ap_lp</code>, lalu beberapa section (per-BPB, per-Kontrabon, per-List Payment, ringkasan per supplier/tipe) dirender dari tabel staging tersebut sebagai HTML penuh dalam satu response.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'rpt_ap_bpb', 'actions' => ['insert','delete'], 'desc' => 'Tabel staging sementara (dihapus lalu ditulis ulang tiap generate, per pengguna).'],
                        ['name' => 'rpt_ap_kbon', 'actions' => ['insert','delete'], 'desc' => 'Tabel staging sementara.'],
                        ['name' => 'rpt_ap_lp', 'actions' => ['insert','delete'], 'desc' => 'Tabel staging sementara.'],
                    ],
                    'tables_read' => [
                        ['name' => 'bpb', 'desc' => 'Data BPB.'],
                        ['name' => 'bppb', 'desc' => 'Data BPB Return.'],
                        ['name' => 'po_header', 'desc' => 'Header Purchase Order.'],
                        ['name' => 'po_header_draft', 'desc' => 'Draft Purchase Order.'],
                        ['name' => 'kontrabon_h', 'desc' => 'Header Kontrabon.'],
                        ['name' => 'potongan', 'desc' => 'Potongan/deduksi pada Kontrabon.'],
                        ['name' => 'list_payment', 'desc' => 'Data List Payment.'],
                        ['name' => 'saldo_awal', 'desc' => 'Saldo awal hutang.'],
                        ['name' => 'saldo_bpb_ap', 'desc' => 'Saldo awal per BPB.'],
                        ['name' => 'saldo_kbon_ap', 'desc' => 'Saldo awal per Kontrabon.'],
                        ['name' => 'saldo_lp_ap', 'desc' => 'Saldo awal per List Payment.'],
                        ['name' => 'tbl_tamb_bpb', 'desc' => 'Data tambahan BPB.'],
                        ['name' => 'tbl_tamb_bpb2', 'desc' => 'Data tambahan BPB (varian kedua).'],
                        ['name' => 'tbl_list_journal', 'desc' => 'Jurnal umum, untuk sebagian perhitungan.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Accounts.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs.'],
                    ],
                    'notes' => [
                        'Ada 3 menu "AP Report" berurutan (file ini, payable_card_statement.php, payable_card_statement2.php) — bukan tumpang tindih, tapi 3 generasi penulisan ulang laporan yang sama, masing-masing dikunci ke rentang periode tertentu supaya angka historis yang sudah pernah dilihat/dicetak tidak berubah walau logikanya terus disempurnakan.',
                        'Masih memakai fungsi mysql_query()/mysql_fetch_array() (API PHP lama) untuk dropdown supplier — ciri khas kode lama yang belum dimodernisasi.',
                        'Ada beberapa berkas "old"/cadangan terkait (pcs_detail_old.php, filter_pcsold.php, ekspor_pa_report_old.php, plus salinan di folder BAK/Old) yang sudah tidak dipakai.',
                    ],
                ],
            ],
            'ap-report-2026a' => [
                'title' => 'AP Report (Jan 2026 - Jun 2026)', 'icon' => 'fa-tags', 'path' => 'module/AP/payable_card_statement.php',
                'doc' => [
                    'summary' => 'Laporan Kartu Hutang versi KEDUA — periode Januari-Juni 2026, ditulis ulang pakai DataTables AJAX (per-section, paginasi server-side) demi performa, konsep sectionnya masih sama dengan versi pertama.',
                    'purpose' => 'Sama seperti AP Report versi pertama (saldo & mutasi hutang per supplier), tapi khusus periode H1 2026, dengan arsitektur baru: 8 section masing-masing punya endpoint AJAX sendiri (folder ap_report/) supaya data besar tidak perlu dirender sekaligus dalam satu halaman.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'kontrabon_h', 'desc' => 'Sumber utama saldo/mutasi Kontrabon.'],
                        ['name' => 'list_payment', 'desc' => 'Sumber mutasi List Payment.'],
                        ['name' => 'bpb', 'desc' => 'Data BPB terkait.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                    ],
                    'notes' => [
                        'Lihat catatan pada "AP Report (Apr 2022 - Dec 2025)" untuk penjelasan kenapa ada 3 versi laporan ini sekaligus.',
                    ],
                ],
            ],
            'ap-report' => [
                'title' => 'AP Report', 'icon' => 'fa-tags', 'path' => 'module/AP/payable_card_statement2.php',
                'doc' => [
                    'summary' => 'Laporan Kartu Hutang versi KETIGA (berjalan/terkini, mulai Juli 2026) — section Kontrabon & List Payment digantikan section "Payment Voucher" yang sumber datanya langsung dari jurnal umum, mencerminkan perubahan proses bisnis nyata mulai Juli 2026.',
                    'purpose' => 'Versi AP Report yang sedang berjalan untuk periode Juli 2026 dan seterusnya. Bedanya dari 2 versi sebelumnya: saldo hutang tidak lagi dihitung dari tabel transaksional List Payment, melainkan langsung dari <code>tbl_list_journal</code> (baris dengan type_journal terkait "AP - Kontrabon"/"Payment Voucher"), sejalan dengan pergeseran mekanisme pelunasan supplier dari "List Payment" lama ke "Payment Voucher" berbasis jurnal.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'tbl_list_journal', 'desc' => 'Sumber utama saldo hutang — difilter ke type_journal terkait Kontrabon/Payment Voucher dan akun GR/IR serta Utang Usaha, dengan cutoff mulai setelah 30 Juni 2026.'],
                        ['name' => 'bpb', 'desc' => 'Data BPB terkait.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                    ],
                    'notes' => [
                        'Perbaikan (commit 0defbd8, Sep 2026): filter From dan To kini otomatis berisi tanggal hari ini saat halaman dibuka - sebelumnya nilainya dipaku ke 2026-07-01.',
                        'Perubahan sumber data dari list_payment ke tbl_list_journal ini menandakan proses bisnis pelunasan supplier benar-benar berubah mulai Juli 2026 (bukan sekadar penulisan ulang kode) — perlu diperhatikan kalau membandingkan angka lintas periode/versi laporan.',
                        'Lihat juga catatan pada "AP Report (Apr 2022 - Dec 2025)" untuk konteks 3 versi laporan ini.',
                    ],
                ],
            ],
            'rekap-pelunasan' => [
                'title' => 'Rekap Pelunasan', 'icon' => 'fa-tags', 'path' => 'module/AP/rekap-pelunasan.php',
                'doc' => [
                    'summary' => 'Rekap realisasi pembayaran AKTUAL (bukan saldo terutang) dari seluruh jalur pelunasan — Bank Out, Petty Cash Out, maupun kanal lama payment_ftr.',
                    'purpose' => 'Berbeda dari AP Report (yang menunjukkan saldo/aging terutang), Rekap Pelunasan menunjukkan uang yang SUDAH benar-benar keluar dalam suatu periode — dipakai untuk rekonsiliasi arus kas/treasury, bukan untuk melihat sisa hutang.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'payment_ftr', 'desc' => 'Pelunasan lewat kanal List Payment lama.'],
                        ['name' => 'b_bankout_h', 'desc' => 'Header pembayaran via Bank Out.'],
                        ['name' => 'b_bankout_det', 'desc' => 'Detail pembayaran via Bank Out (baik referensi List Payment maupun Payment Voucher).'],
                        ['name' => 'c_petty_cashout_h', 'desc' => 'Header pembayaran via Petty Cash Out.'],
                        ['name' => 'c_petty_cashout_det', 'desc' => 'Detail pembayaran via Petty Cash Out.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                    ],
                    'notes' => [
                        'Murni laporan baca — tombol create/upload yang sempat ada di kode saat ini dinonaktifkan.',
                    ],
                ],
            ],
            'purchase-report' => [
                'title' => 'Purchase Report', 'icon' => 'fa-tags', 'path' => 'module/AP/laporan_pembelian.php',
                'doc' => [
                    'summary' => 'Laporan detail pembelian berbasis BPB (bukan berbasis Kontrabon) — mencakup Bahan Baku, Barang Dalam Proses, dan Item General.',
                    'purpose' => 'Menampilkan detail penerimaan barang (BPB) per item, lengkap dengan referensi PO/dokumen customs (No Aju/No Daftar — untuk kawasan berikat) dan status pencocokan ke invoice supplier. Filter "Tipe" memilih antara Bahan Baku, Barang Dalam Proses (WIP), Item General, atau gabungan semuanya.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'bpb', 'desc' => 'Data BPB — sumber utama Bahan Baku.'],
                        ['name' => 'bpb_knitting', 'desc' => 'Data BPB khusus divisi Knitting.'],
                        ['name' => 'masteritem', 'desc' => 'Master Item.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'po_header', 'desc' => 'Header Purchase Order.'],
                        ['name' => 'po_header_draft', 'desc' => 'Draft Purchase Order.'],
                        ['name' => 'act_costing', 'desc' => 'Data costing, untuk linkage Work Order/Style.'],
                        ['name' => 'so', 'desc' => 'Sales Order terkait.'],
                        ['name' => 'jo_det', 'desc' => 'Detail Job Order.'],
                        ['name' => 'bpb_new', 'desc' => 'Status pencocokan BPB ke invoice/faktur pajak supplier.'],
                    ],
                    'notes' => [
                        'Punya kembaran ekspor Excel (ekspor_lap_pembelian*.php) dan varian ringkas "Global" (laporan_pembelian_global.php).',
                    ],
                ],
            ],
            'purchase-return-report' => [
                'title' => 'Purchase Return Report', 'icon' => 'fa-tags', 'path' => 'module/AP/laporan_retur_pembelian.php',
                'doc' => [
                    'summary' => 'Kembaran Purchase Report, tapi untuk barang yang DIKEMBALIKAN ke supplier (retur) — sumber datanya BPB Return (bppb), bukan BPB.',
                    'purpose' => 'Menampilkan detail retur pembelian per item, struktur & filter Tipe-nya sama persis dengan Purchase Report.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'bppb', 'desc' => 'Data BPB Return (Bukti Pengembalian/Retur Barang) — sumber utama.'],
                        ['name' => 'tbl_return_report', 'desc' => 'Tabel pra-agregat lama, dipakai untuk kategori WIP/Item General.'],
                        ['name' => 'masteritem', 'desc' => 'Master Item.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                    ],
                    'notes' => [
                        'Punya kembaran ekspor Excel (ekspor_lap_retur_pembelian*.php) dan varian ringkas "Global" (laporan_retur_pembelian_global.php).',
                    ],
                ],
            ],
            '_s12' => ['section' => 'Approval'],
            'approve-kontrabon-reg' => [
                'title' => 'Kontrabon Reg', 'icon' => 'fa-ticket', 'path' => 'module/AP/formapprovekb.php',
                'doc' => [
                    'summary' => 'Halaman approval klasik (sekali klik) untuk Kontra Bon Reguler — memakai backend yang SAMA PERSIS dengan tombol Approve/Cancel inline pada menu Kontra Bon Reg.',
                    'purpose' => 'Menyediakan antrian approval terpusat bagi Kontra Bon Reguler berstatus draf, terpisah dari halaman pembuatannya, agar approver tidak perlu membuka daftar Kontra Bon penuh.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Kontra Bon Reguler berstatus draf.'],
                        ['title' => 'Approve', 'desc' => 'Memanggil approvekbon.php — file backend yang sama dengan menu Kontra Bon Reg; status menjadi "Approved", jurnal dikonfirmasi, Kartu Hutang dilengkapi.'],
                        ['title' => 'Cancel', 'desc' => 'Memanggil cancelkbon.php — status menjadi "Cancel".'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Sama seperti menu Kontra Bon Reg — status & audit approve/cancel.'],
                        ['name' => 'kontrabon', 'actions' => ['update'], 'desc' => 'Sama seperti menu Kontra Bon Reg.'],
                        ['name' => 'potongan', 'actions' => ['update'], 'desc' => 'Sama seperti menu Kontra Bon Reg.'],
                        ['name' => 'return_kb', 'actions' => ['update'], 'desc' => 'Sama seperti menu Kontra Bon Reg.'],
                        ['name' => 'kontrabon_ftr', 'actions' => ['update'], 'desc' => 'Sama seperti menu Kontra Bon Reg.'],
                        ['name' => 'kartu_hutang', 'actions' => ['update'], 'desc' => 'Sama seperti menu Kontra Bon Reg.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['update', 'insert'], 'desc' => 'Sama seperti menu Kontra Bon Reg — dikonfirmasi saat approve, dibalik saat cancel.'],
                        ['name' => 'status', 'actions' => ['update'], 'desc' => 'Sama seperti menu Kontra Bon Reg.'],
                        ['name' => 'bpb_new', 'actions' => ['update'], 'desc' => 'Dikembalikan "Waiting" hanya pada alur cancel.'],
                        ['name' => 'bppb_new', 'actions' => ['update'], 'desc' => 'Dikembalikan "Waiting" hanya pada alur cancel.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Halaman ini murni "pintu approval" tambahan — tidak ada logika backend yang berbeda dari tombol Approve/Cancel inline pada menu Kontra Bon Reg, hanya cakupan datanya dipersempit ke status draf saja.',
                        'Diketahui ada bug kecil pada approvekbon.php: kolom tbl_list_journal.approve_date selalu tersimpan kosong karena variabel tanggal-nya tidak pernah diisi.',
                    ],
                ],
            ],
            'approve-list-payment-reg' => [
                'title' => 'List Payment Reg', 'icon' => 'fa-ticket', 'path' => 'module/AP/formapprovelp.php',
                'doc' => [
                    'summary' => 'Antrian approval massal (bulk) untuk List Payment Reguler berstatus draf — memakai backend yang SAMA PERSIS dengan tombol approve/cancel inline pada menu List Payment Reg.',
                    'purpose' => 'Menyediakan tampilan checklist terpusat agar approver bisa menyetujui/membatalkan banyak List Payment sekaligus, tanpa membuka daftar List Payment Reg penuh satu per satu.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'List Payment Reguler (dan saldo_awal) berstatus "draft", dengan kotak centang per baris.'],
                        ['title' => 'Approve massal', 'desc' => 'Memanggil approvelistpayment.php — endpoint yang sama dengan aksi inline di List Payment Reg; status menjadi "Approved".'],
                        ['title' => 'Cancel massal', 'desc' => 'Memanggil cancellistpayment.php — status menjadi "Cancel", saldo Kontra Bon sumber dikembalikan.'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'list_payment', 'actions' => ['update'], 'desc' => 'Sama seperti List Payment Reg — status berubah Approved/Cancel.'],
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Balance dikembalikan, hanya pada alur cancel.'],
                        ['name' => 'status', 'actions' => ['update'], 'desc' => 'Sama seperti List Payment Reg.'],
                        ['name' => 'saldo_awal', 'actions' => ['update'], 'desc' => 'Cabang paralel untuk baris warisan lama, sama seperti List Payment Reg.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Halaman ini murni "pintu approval" tambahan — tidak ada logika backend yang berbeda dari tombol approve/cancel inline pada List Payment Reg, hanya berupa antrian checklist massal.',
                    ],
                ],
            ],
            'approve-payment-reg' => [
                'title' => 'Payment Reg', 'icon' => 'fa-ticket', 'path' => 'module/AP/form_approve_payment.php',
                'doc' => [
                    'summary' => 'Antrian approval massal untuk Payment Reguler (payment_ftr) berstatus draf, difilter per Profit Center.',
                    'purpose' => 'Menyetujui dokumen Payment Reguler yang sudah dibuat di menu Payment Reg, sebagai langkah approval final atas pembayaran yang sudah terjadi (jurnal & Kartu Hutang sudah terbentuk sejak dokumen dibuat, bukan saat approval ini).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Dokumen payment_ftr berstatus "draft", dapat difilter per Profit Center.'],
                        ['title' => 'Approve', 'desc' => 'Status menjadi "Approved" beserta approve_by/approve_date.'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'payment_ftr', 'actions' => ['update'], 'desc' => 'Status berubah "draft" → "Approved" beserta audit approve_by/approve_date.'],
                    ],
                    'tables_read' => [
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center, untuk filter antrian.'],
                    ],
                    'notes' => [
                        'Bug ditemukan: setelah UPDATE status berhasil, berkas backend (approve_payment.php) menjalankan sebuah query kedua dari variabel yang tidak pernah didefinisikan (baris query aslinya — sebuah INSERT INTO tbl_list_journal tambahan — dikomentari, namun baris eksekusinya tetap aktif). Ini memicu peringatan PHP "undefined variable" dan membuat maksud aslinya (memposting baris penyesuaian manual ke jurnal SAAT approval) menjadi kode mati yang tidak pernah benar-benar berjalan; jurnal utama tetap terbentuk lebih awal, saat dokumen Payment dibuat (lihat menu Payment Reg), bukan di sini.',
                        'Tidak ada menu approval setara untuk Payment CBD/DP — konsisten dengan tidak adanya UI approval yang bisa dijangkau untuk kedua tipe tersebut (lihat catatan pada menu Payment CBD/Payment DP).',
                    ],
                ],
            ],
            'approve-pv-ap-first' => [
                'title' => 'Payment Voucher - First', 'icon' => 'fa-ticket', 'path' => 'module/AP/approve-payment-voucher-ap-first.php',
                'doc' => [
                    'summary' => 'Tahap approval pertama untuk seluruh dokumen Payment Voucher (Kontrabon) — mencakup 6 tipe dokumen: Reguler, Installment, DP, CBD, Saldo Awal, dan Biaya.',
                    'purpose' => 'Menyaring dokumen berstatus "draft" (khusus tipe Reguler, hanya yang dibuat pada atau setelah 1 Juli 2026) dan memberi persetujuan tahap pertama, sebelum diteruskan ke Second Approval.',
                    'variants' => [
                        ['name' => 'Reguler', 'icon' => 'fa-file-text-o', 'desc' => 'first_approve_kbon.php — kontrabon_h/kontrabon: draft → FIRST APPROVED.'],
                        ['name' => 'Installment', 'icon' => 'fa-calendar', 'desc' => 'first_approve_installment.php — kontrabon_h_installment_detail per cicilan; header ikut berubah hanya setelah seluruh cicilan disetujui.'],
                        ['name' => 'DP', 'icon' => 'fa-arrow-down', 'desc' => 'first_approve_dp.php — kontrabon_dp/kontrabon_h_dp: draft → FIRST APPROVED.'],
                        ['name' => 'CBD', 'icon' => 'fa-money', 'desc' => 'first_approve_cbd.php — kontrabon_cbd/kontrabon_h_cbd: draft → FIRST APPROVED.'],
                        ['name' => 'Saldo Awal', 'icon' => 'fa-book', 'desc' => 'first_approve_saldo_awal.php — ap_saldo_payment_voucher: draft → FIRST APPROVED.'],
                    ],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Endpoint ajx_approve-payment-voucher-ap.php membaca parameter tahap ("first") dan menyaring status = "draft" untuk seluruh tipe dokumen.'],
                        ['title' => 'Approve tahap pertama', 'desc' => 'Status berubah "FIRST APPROVED" beserta kolom audit tahap pertama (first_approve_by/user/date) — belum memposting jurnal maupun Kartu Hutang.'],
                        ['title' => 'Cancel', 'desc' => 'Dokumen bisa dibatalkan langsung dari halaman ini juga, memakai endpoint cancel yang sama dengan menu pembuatan masing-masing tipe.'],
                    ],
                    'status_flow' => [
                        ['label' => 'draft', 'cls' => 'planned'],
                        ['label' => 'FIRST APPROVED', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Tipe Reguler — status & audit tahap pertama.'],
                        ['name' => 'kontrabon', 'actions' => ['update'], 'desc' => 'Tipe Reguler.'],
                        ['name' => 'kontrabon_h_installment_detail', 'actions' => ['update'], 'desc' => 'Tipe Installment, per baris cicilan.'],
                        ['name' => 'kontrabon_dp', 'actions' => ['update'], 'desc' => 'Tipe DP.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Tipe DP.'],
                        ['name' => 'kontrabon_cbd', 'actions' => ['update'], 'desc' => 'Tipe CBD.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Tipe CBD.'],
                        ['name' => 'ap_saldo_payment_voucher', 'actions' => ['update'], 'desc' => 'Tipe Saldo Awal.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Batas tanggal 1 Juli 2026 (nilai tetap tertanam di kode) hanya berlaku untuk tipe Reguler pada halaman approval ini — tipe lain tidak dibatasi tanggal pembuatan.',
                        'Tipe "Biaya" (PV Bank non-Kontrabon, tbl_pv_h/tbl_pv) turut tampil di antrian yang sama namun memakai alur approve sekali-klik sendiri (approvepv.php), bukan mekanisme dua tahap ini.',
                    ],
                ],
            ],
            'approve-pv-ap-second' => [
                'title' => 'Payment Voucher - Second', 'icon' => 'fa-ticket', 'path' => 'module/AP/approve-payment-voucher-ap-second.php',
                'doc' => [
                    'summary' => 'Tahap approval kedua (final) untuk dokumen Payment Voucher (Kontrabon) tipe Reguler, Installment, dan Saldo Awal — untuk tipe DP & CBD, tahap kedua sebenarnya dieksekusi dari halaman approval "PV Biaya" gabungan (approve-pv.php), BUKAN dari halaman ini.',
                    'purpose' => 'Memberi persetujuan final atas dokumen yang sudah lolos First Approval ("FIRST APPROVED"). Untuk tipe Reguler & Installment, di titik inilah efek penuh setara "Approve" pada Kontra Bon klasik terjadi: jurnal dikonfirmasi dan Kartu Hutang diperbarui.',
                    'variants' => [
                        ['name' => 'Reguler', 'icon' => 'fa-file-text-o', 'desc' => 'second_approve_kbon.php — status "SECOND APPROVED"; turut memperbarui potongan, return_kb, kontrabon_ftr (→ Cancel), kartu_hutang, status, tbl_list_journal — mirip efek approvekbon.php klasik namun status akhirnya "SECOND APPROVED", bukan "Approved".'],
                        ['name' => 'Installment', 'icon' => 'fa-calendar', 'desc' => 'second_approve_installment.php — per cicilan → "SECOND APPROVED"; header kontrabon_h ikut disetujui (confirm_user/confirm_date) hanya setelah seluruh cicilan selesai.'],
                        ['name' => 'Saldo Awal', 'icon' => 'fa-book', 'desc' => 'second_approve_saldo_awal.php — ap_saldo_payment_voucher: FIRST APPROVED → SECOND APPROVED.'],
                    ],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Dokumen berstatus "FIRST APPROVED" untuk tipe Reguler/Installment/Saldo Awal.'],
                        ['title' => 'Approve tahap kedua', 'desc' => 'Status menjadi "SECOND APPROVED"; khusus Reguler/Installment, jurnal &amp; Kartu Hutang ikut diproses final di tahap ini.'],
                        ['title' => 'Cancel', 'desc' => 'Tersedia lewat endpoint cancel yang sama dengan tipe masing-masing (termasuk cancelkboninstallment.php khusus Installment, yang membatalkan berjenjang seluruh baris cicilan anak).'],
                    ],
                    'status_flow' => [
                        ['label' => 'FIRST APPROVED', 'cls' => 'planned'],
                        ['label' => 'SECOND APPROVED', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Tipe Reguler — status final & audit tahap kedua.'],
                        ['name' => 'kontrabon', 'actions' => ['update'], 'desc' => 'Tipe Reguler.'],
                        ['name' => 'potongan', 'actions' => ['update'], 'desc' => 'Tipe Reguler.'],
                        ['name' => 'return_kb', 'actions' => ['update'], 'desc' => 'Tipe Reguler.'],
                        ['name' => 'kontrabon_ftr', 'actions' => ['update'], 'desc' => 'Tipe Reguler — ditutup ("Cancel") saat Second Approval.'],
                        ['name' => 'kartu_hutang', 'actions' => ['update'], 'desc' => 'Tipe Reguler/Installment — dilengkapi data faktur pajak & jatuh tempo, setara langkah Approve pada Kontra Bon klasik.'],
                        ['name' => 'status', 'actions' => ['update'], 'desc' => 'Tipe Reguler.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['update'], 'desc' => 'Tipe Reguler/Installment — jurnal draf dikonfirmasi final.'],
                        ['name' => 'kontrabon_h_installment_detail', 'actions' => ['update'], 'desc' => 'Tipe Installment, per baris cicilan.'],
                        ['name' => 'ap_saldo_payment_voucher', 'actions' => ['update'], 'desc' => 'Tipe Saldo Awal.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'PENTING: tipe DP & CBD TIDAK diproses tahap keduanya dari halaman ini, meski keduanya sempat melewati First Approval di halaman sebelah. Tahap kedua untuk DP/CBD justru dipindahkan ke halaman approval "PV Biaya" gabungan (approve-pv.php), yang memanggil second_approve_dp.php/second_approve_cbd.php secara terpisah — jika mendokumentasikan alur DP/CBD, jangan menyebut halaman ini sebagai tempat approval final-nya.',
                    ],
                ],
            ],
            'approve-pv-list' => [
                'title' => 'Payment Voucher List', 'icon' => 'fa-ticket', 'path' => 'module/AP/approve-payment-voucher-list.php',
                'doc' => [
                    'summary' => 'Halaman approval satu tahap untuk batch "Payment Voucher List" — menyetujui sekelompok dokumen AP yang sudah final-approved sekaligus.',
                    'purpose' => 'Memverifikasi hak akses approver ("Approval Payment Voucher List") lalu menyetujui batch berstatus "Draft", menandai seluruh dokumen anggota batch sebagai sudah dikelompokkan.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Batch pv_payment_voucher_list_h berstatus "Draft".'],
                        ['title' => 'Approve', 'desc' => 'Setelah pemeriksaan hak akses, status header menjadi "APPROVED"; setiap dokumen sumber pada detail ditandai status_pvl = "APPROVED".'],
                        ['title' => 'Cancel', 'desc' => 'Hanya selama batch masih "Draft" — status kembali "Cancel", penanda pada dokumen sumber dikosongkan.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pv_payment_voucher_list_h', 'actions' => ['update'], 'desc' => 'Status batch menjadi "APPROVED" beserta audit approve.'],
                        ['name' => 'pv_payment_voucher_list_det', 'actions' => ['select'], 'desc' => 'Dibaca untuk menentukan dokumen mana saja yang perlu ditandai status_pvl.'],
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Penanda status_pvl = "APPROVED" untuk dokumen tipe Reguler dalam batch.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Penanda status_pvl untuk dokumen tipe DP.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Penanda status_pvl untuk dokumen tipe CBD.'],
                        ['name' => 'kontrabon_h_installment_detail', 'actions' => ['update'], 'desc' => 'Penanda status_pvl untuk dokumen tipe Installment.'],
                        ['name' => 'ap_saldo_payment_voucher', 'actions' => ['update'], 'desc' => 'Penanda status_pvl untuk dokumen tipe Saldo Awal.'],
                        ['name' => 'tbl_pv_h', 'actions' => ['update'], 'desc' => 'Penanda status_pvl untuk dokumen tipe Biaya.'],
                    ],
                    'tables_read' => [
                        ['name' => 'useraccess', 'desc' => 'Pemeriksaan hak akses menu approval.'],
                        ['name' => 'menurole', 'desc' => 'Pemeriksaan hak akses menu approval.'],
                    ],
                    'notes' => [
                        'Hanya satu tahap approval (berbeda dari approval Payment List di grup menu Bank yang dua tahap).',
                    ],
                ],
            ],
            '_s13' => ['section' => 'Lainnya'],
            'request-debitnote' => [
                'title' => 'Request Debit Note', 'icon' => 'fa-registered', 'path' => 'module/AP/request_debitnote.php',
                'doc' => [
                    'summary' => 'Pengajuan & pembatalan permintaan Debit Note ke supplier — dokumen klaim pengurangan tagihan (misalnya karena barang cacat/retur/selisih harga) yang berdiri sendiri di tabel req_dn.',
                    'purpose' => 'Mencatat permintaan Debit Note beserta dokumen pendukungnya, dan menyediakan pembatalan baik untuk permintaan itu sendiri maupun dokumen lampirannya secara terpisah.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Permintaan Debit Note beserta detail PO/BPB/item/qty/harga/total yang menyertainya, direnderkan lewat modal murni-baca.'],
                        ['title' => 'Cancel permintaan', 'desc' => 'Header req_dn_h ditandai "Cancel".'],
                        ['title' => 'Cancel dokumen lampiran', 'desc' => 'Baris dokumen pada req_dn_dok ditandai "CANCEL" (huruf besar, tidak konsisten dengan status "Cancel" pada req_dn_h) secara terpisah dari pembatalan header.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Diajukan', 'cls' => 'planned'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'req_dn_h', 'actions' => ['update'], 'desc' => 'Header permintaan Debit Note — ditandai "Cancel" beserta audit pembatalan.'],
                        ['name' => 'req_dn_dok', 'actions' => ['update'], 'desc' => 'Dokumen lampiran permintaan — ditandai "CANCEL" secara independen dari status header.'],
                    ],
                    'tables_read' => [
                        ['name' => 'req_dn', 'desc' => 'Detail baris permintaan (PO/BPB/item/qty/harga/total/attn/season/reff) untuk modal rincian.'],
                    ],
                    'notes' => [
                        'Halaman ini memuat kode mati: sebuah handler klik ber-ID "#approve" yang menunjuk ke endpoint approveftrdp.php, namun elemen dengan id tersebut tidak pernah ada di halaman ini — hampir pasti sisa salin-tempel dari halaman pengajuan FTR DP, dan tidak pernah benar-benar berjalan karena event delegation jQuery hanya aktif untuk elemen yang benar-benar ada di DOM.',
                        'Berdiri sendiri sepenuhnya dari tabel Kontra Bon/FTR/jurnal — tidak ada penulisan ke tbl_list_journal, kartu_hutang, kontrabon manapun, atau ap_reverse_*.',
                    ],
                ],
            ],
        ],
    ],
    'accounting' => [
        'title' => 'Accounting',
        'icon'  => 'fa-bar-chart',
        'items' => [
            'memorial-journal' => [
                'title' => 'Memorial Journal', 'icon' => 'fa-bars', 'path' => 'module/AP/memorial-journal.php',
                'doc' => [
                    'summary' => 'Pembuatan jurnal manual (Memorial Journal) lewat 3 jalur — input manual, dari data HRIS (payroll), atau unggah berkas — semuanya bermuara ke tabel jurnal umum tunggal yang dipakai bersama seluruh dokumen AP lainnya.',
                    'purpose' => 'Menyediakan cara mencatat transaksi akuntansi yang TIDAK berasal dari dokumen transaksional lain (BPB, Bank Out, Kontrabon, dsb.) — misalnya jurnal penyesuaian, jurnal dari payroll HRIS, atau koreksi manual.',
                    'variants' => [
                        ['name' => 'Input Manual', 'icon' => 'fa-keyboard-o', 'desc' => 'Mengisi baris jurnal langsung lewat form.'],
                        ['name' => 'Journal From HRIS', 'icon' => 'fa-users', 'desc' => 'Menarik data payroll dari basis data HRIS terpisah (hris_nag) dan mengonversinya menjadi baris jurnal.'],
                        ['name' => 'Upload', 'icon' => 'fa-upload', 'desc' => 'Mengunggah berkas berisi banyak baris jurnal sekaligus, diproses lewat tabel staging sementara.'],
                    ],
                    'flow' => [
                        ['title' => 'Buat jurnal', 'desc' => 'Ketiga jalur (Manual/HRIS/Upload) sama-sama menyimpan header+detail ke <code>tbl_memorial_journal</code> DAN ke <code>tbl_list_journal</code> (tabel jurnal umum tunggal yang dipakai bersama BPB, Bank Out, Kontrabon, Payment Voucher, dst.) — keduanya langsung berstatus "Post" saat disimpan, BUKAN "Draft".'],
                        ['title' => '(Opsional) Salin ke SB1', 'desc' => 'Apabila mencentang opsi "to SB1", data yang sama juga disalin ke tabel bayangan <code>sb_memorial_journal</code>/<code>sb_list_journal</code> sebagai sub-ledger cadangan terpisah.'],
                        ['title' => 'Cancel', 'desc' => 'Jurnal yang sudah tersimpan diarsipkan ke <code>tbl_list_journal_cancel</code> lalu DIHAPUS PERMANEN dari <code>tbl_list_journal</code> (bukan sekadar ditandai status Cancel) — apabila sumbernya HRIS, tautan di basis data HRIS turut dilepas.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Post (langsung saat dibuat)', 'cls' => 'progress'],
                        ['label' => 'Cancel (arsip lalu dihapus)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'tbl_memorial_journal', 'actions' => ['insert', 'update'], 'desc' => 'Header/detail Memorial Journal — selalu tersimpan berstatus "Post", tidak pernah "Draft" pada alur pembuatan saat ini.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert', 'update', 'delete'], 'desc' => 'Tabel jurnal umum TUNGGAL yang dipakai bersama SELURUH jenis dokumen di aplikasi ini (BPB, Bank Out, Kontrabon, Payment Voucher, dst.) — baris dibuat saat jurnal disimpan, diperbarui saat Post, diarsipkan & dihapus saat Cancel.'],
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip cadangan baris tbl_list_journal sesaat sebelum dihapus permanen saat Cancel.'],
                        ['name' => 'sb_memorial_journal', 'actions' => ['insert', 'update'], 'desc' => 'Salinan bayangan header/detail, hanya terisi apabila opsi "to SB1" dicentang saat membuat jurnal.'],
                        ['name' => 'sb_list_journal', 'actions' => ['insert', 'delete'], 'desc' => 'Salinan bayangan baris jurnal umum, mengikuti siklus hidup sb_memorial_journal.'],
                        ['name' => 'status_memorial_journal', 'actions' => ['insert', 'update'], 'desc' => 'Header penanda status salinan SB1 (no_mj, no_mj_sb, status), dibuat hanya saat opsi "to SB1" dicentang.'],
                        ['name' => 'jurnal', 'db' => 'MySQL hris_nag @ 10.10.5.111 (conn3)', 'actions' => ['update'], 'desc' => 'Khusus jalur "Journal From HRIS" — tautan dilepas (no_journal dikosongkan) saat jurnal hasil tarikan HRIS ini dibatalkan.'],
                        ['name' => 'log_jurnal_bpjs', 'db' => 'MySQL hris_nag @ 10.10.5.111 (conn3)', 'actions' => ['update'], 'desc' => 'Khusus jalur "Journal From HRIS" — ditandai status "CANCEL" saat jurnal hasil tarikan HRIS ini dibatalkan.'],
                        ['name' => 'tbl_memorial_journal_temp', 'actions' => ['delete'], 'desc' => 'Tabel staging khusus jalur Upload — baris dihapus setelah berhasil dipindahkan menjadi jurnal permanen.'],
                    ],
                    'tables_read' => [
                        ['name' => 'mastercoa_v2', 'desc' => 'Master COA - flag grup (support_gen_adm, support_prod, prod, support_sell) menentukan Cost Center mana yang sah untuk COA tersebut, sekaligus menentukan COA wajib Cost Center atau tidak.'],
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center - disaring lewat id_pc (Profit Center baris) DAN group2 (harus termasuk grup COA baris).'],
                        ['name' => 'master_category_mj', 'desc' => 'Kategori Memorial Journal, menentukan nama type_journal.'],
                    ],
                    'notes' => [
                        'Perbaikan (commit c7bdebc, Sep 2026): pembatasan Cost Center per COA diperketat. Di form EDIT, dropdown Cost Center dulu memuat SEMUA cost center milik Profit Center itu tanpa menyaring grup COA - sekarang ikut disaring group2 sesuai grup COA. Di jalur UPLOAD, Cost Center KOSONG dulu selalu dianggap sah untuk semua COA - sekarang hanya sah untuk COA tanpa grup (COA neraca), sehingga COA yang wajib Cost Center akan ditandai merah dan tidak bisa disimpan.',
                        'Perbaikan (commit e3423eb, Sep 2026): baris ber-mata-uang IDR tidak lagi dikali kurs. Form mengirim satu rate global untuk SEMUA baris sehingga baris IDR ikut dikali kurs USD dan total IDR membengkak sampai ratusan miliar. Sekarang seluruh jalur simpan memaksa rate=1 untuk baris IDR. CATATAN: baris lama yang terlanjur salah BELUM diperbaiki.',
                        'Riwayat edit tersimpan di tbl_list_journal_cancel - versi SEBELUM diedit dipindahkan ke sana, sehingga berguna untuk menelusuri kondisi awal sebuah jurnal (mis. Cost Center yang berubah saat edit).',
                        'Nomor: GM/NAG/MMYY/00001 — PENTING: prefiks "NAG" ini tetap sama meski barisnya sebenarnya milik Profit Center NAK, sehingga satu seri nomor dipakai bersama lintas Profit Center (berbeda dari kebiasaan aplikasi ini yang biasanya memisahkan NAG/NAK).',
                        'BUG/kode mati: seluruh jalur pembuatan jurnal (Manual/HRIS/Upload) menetapkan status "Post" secara langsung — tidak ada jalur aktif manapun di UI saat ini yang menghasilkan status "Draft". Akibatnya tombol/menu "Post" terpisah (formverifikasimj.php/approvemj.php/post_memorialjournal.php) menjadi kode yang praktis tidak pernah terpakai, kecuali ada baris Draft yang diubah manual langsung di database.',
                        'tbl_list_journal adalah TABEL JURNAL UMUM TUNGGAL yang dipakai bersama oleh SEMUA jenis dokumen di seluruh aplikasi ini — BPB, Bank Out, Kontrabon, Payment Voucher, dan Memorial Journal semuanya menulis ke tabel yang sama ini. Kolom status-nya TIDAK konsisten sebagai satu enum: Memorial Journal memakai "Post"/"Draft"/"Cancel", sedangkan baris asal BPB memakai "APPROVED"/"DRAFT" — kode yang menyaring status="Draft" secara spesifik tidak akan pernah cocok dengan baris asal BPB.',
                        'Terdapat berkas legacy yang sudah tidak tertaut dari menu manapun (create-memorial-journal.php, edit-memorial-journal-old.php) — sisa versi lama, aman diabaikan.',
                    ],
                ],
            ],
            'list-journal' => [
                'title' => 'List Journal', 'icon' => 'fa-list-alt', 'path' => 'module/AP/list-journal.php',
                'doc' => [
                    'summary' => 'Tampilan daftar seluruh baris jurnal umum (tbl_list_journal) lintas jenis dokumen — namun BUKAN halaman baca murni: setiap kali dibuka, halaman ini diam-diam menjalankan beberapa UPDATE perbaikan data dan sebuah INSERT jurnal selisih kurs otomatis.',
                    'purpose' => 'Memberi pandangan gabungan seluruh jurnal (dari BPB, Bank Out, Kontrabon, Memorial Journal, dsb.) dalam satu tabel, sekaligus — secara tidak eksplisit terlihat oleh pengguna — menjalankan beberapa rutinitas perbaikan data setiap kali halaman dimuat.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Muat halaman', 'desc' => 'Menampilkan gabungan (UNION) baris tbl_list_journal, dengan penanganan khusus untuk jurnal Memorial Journal ("GM/NAG") dan jurnal berprefiks "KKK".'],
                        ['title' => '(Otomatis, tersembunyi) Perbaikan data', 'desc' => 'Pada SETIAP pemuatan halaman, 3 pernyataan UPDATE dijalankan untuk melengkapi kolom create_date/create_by yang kosong pada tbl_list_journal, mengambil nilainya dari tabel log tbl_log.'],
                        ['title' => '(Otomatis, tersembunyi) Jurnal selisih kurs', 'desc' => 'Pada setiap pemuatan halaman, sistem turut menyisipkan baris jurnal baru berlabel "SELISIH KURS KONTRABON" untuk Kontra Bon USD yang saldo IDR-nya belum seimbang, dibatasi tanggal jurnal mulai 2026-01-01 (nilai tetap tertanam di kode).'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'tbl_list_journal', 'actions' => ['update', 'insert'], 'desc' => 'UPDATE mengisi create_date/create_by yang kosong (disinkron dari tbl_log); INSERT menambahkan baris jurnal "SELISIH KURS KONTRABON" otomatis untuk Kontrabon USD yang belum seimbang — keduanya berjalan otomatis setiap halaman dimuat, bukan aksi eksplisit pengguna.'],
                    ],
                    'tables_read' => [
                        ['name' => 'tbl_log', 'desc' => 'Sumber data create_date/create_by untuk perbaikan otomatis di atas.'],
                    ],
                    'notes' => [
                        'PENTING: halaman ini secara teknis BUKAN halaman baca murni — setiap kali dibuka (baik lewat GET maupun POST) ia menjalankan operasi tulis ke database tanpa interaksi eksplisit dari pengguna. Perlu kehati-hatian apabila halaman ini di-refresh berulang kali atau dipanggil otomatis oleh proses lain.',
                        'Logika penyisipan jurnal "SELISIH KURS" di halaman ini TUMPANG TINDIH dengan tombol sinkronisasi selisih kurs terpisah di menu Memorial Journal — keduanya bisa menghasilkan baris jurnal selisih kurs untuk kasus yang sama tanpa mekanisme pencegahan duplikasi bersama, berisiko dobel posting.',
                        'Bug ditemukan: salah satu cabang query (dipicu saat halaman diakses tanpa rentang tanggal) memiliki SQL rusak (dua kata kunci FROM berurutan, sisa salin-tempel) — jarang terpicu karena input tanggal client-side sudah punya nilai default, namun berpotensi error database apabila benar-benar terpicu.',
                        'Batas tanggal 2026-01-01 pada logika selisih kurs otomatis adalah nilai tetap tertanam di kode, bukan konfigurasi — perlu diperbarui manual tiap pergantian skema tahun berjalan.',
                    ],
                ],
            ],
            'general-ledger' => [
                'title' => 'General Ledger', 'icon' => 'fa-print', 'path' => 'module/AP/general-ledger.php',
                'doc' => [
                    'summary' => 'Laporan General Ledger versi lama — murni baca, menampilkan mutasi tbl_list_journal per akun COA beserta saldo awal & saldo berjalan.',
                    'purpose' => 'Menampilkan buku besar (mutasi debit/kredit per akun) untuk satu akun COA dalam rentang tanggal tertentu, tanpa filter Profit Center (lihat menu General Ledger New untuk versi yang lebih baru dengan filter Profit Center).',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'saldo_awal_tb', 'desc' => 'Saldo awal per akun COA — skema satu kolom per bulan (mis. kolom bernama sesuai bulan_tahun).'],
                        ['name' => 'tbl_list_journal', 'desc' => 'Mutasi jurnal per akun COA dalam rentang tanggal, saldo berjalan dihitung dengan variabel sesi MySQL.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Account, untuk dropdown pilihan akun.'],
                    ],
                    'notes' => [
                        'Bug ditemukan: sama seperti menu List Journal, cabang query "tanpa rentang tanggal" pada halaman ini memiliki SQL rusak (dua kata kunci FROM berurutan) — kemungkinan besar hasil salin-tempel dari berkas yang sama.',
                        'Berbeda dari General Ledger New: tidak ada filter Profit Center, dan saldo awal diambil dari tabel saldo_awal_tb (bukan fs_saldo_awal_tb) — kedua tabel saldo awal ini dapat berbeda nilai untuk akun COA & periode yang sama apabila tidak disinkronkan, sehingga kedua versi General Ledger berisiko menampilkan saldo awal yang tidak sama untuk akun yang sama.',
                    ],
                ],
            ],
            'general-ledger-new' => [
                'title' => 'General Ledger New', 'icon' => 'fa-print', 'path' => 'module/AP/general_ledger.php',
                'doc' => [
                    'summary' => 'Laporan General Ledger versi baru — murni baca, perbaikan dari versi lama dengan tambahan filter Profit Center dan perhitungan saldo berjalan yang lebih andal.',
                    'purpose' => 'Versi General Ledger yang lebih baru dan lebih lengkap: menambahkan filter Profit Center, memakai tabel saldo awal terpisah (fs_saldo_awal_tb), dan menghitung saldo berjalan memakai window function SQL alih-alih trik variabel sesi pada versi lama.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'fs_saldo_awal_tb', 'desc' => 'Saldo awal per akun COA — versi terpisah dari saldo_awal_tb yang dipakai General Ledger lama, bisa difilter per Profit Center.'],
                        ['name' => 'tbl_list_journal', 'desc' => 'Mutasi jurnal per akun COA, saldo berjalan dihitung dengan window function SQL (SUM ... OVER).'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center, untuk filter tambahan yang tidak ada di versi lama.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Account.'],
                    ],
                    'notes' => [
                        'Memakai tabel saldo awal BERBEDA (fs_saldo_awal_tb) dari General Ledger versi lama (saldo_awal_tb) — kedua tabel ini berpotensi tidak sinkron, sehingga saldo awal yang ditampilkan untuk akun & periode yang sama bisa berbeda antara kedua versi General Ledger.',
                        'Bug kecil: endpoint AJAX-nya (ajx_get_data_gl.php) turut mengembalikan SQL mentah dalam respons JSON yang kemudian di-console.log di sisi browser — bukan celah keamanan kredensial, namun membocorkan struktur query/nama tabel/filter ke devtools browser.',
                    ],
                ],
            ],
            'trial-balance' => [
                'title' => 'Trial Balance', 'icon' => 'fa-line-chart', 'path' => 'module/AP/trial_balance.php',
                'doc' => [
                    'summary' => 'Laporan Neraca Saldo (Trial Balance) — murni baca, mengagregasi tbl_list_journal per akun COA untuk satu periode akuntansi.',
                    'purpose' => 'Menampilkan total debit/kredit per akun COA (dengan hierarki kategori 1 sampai 4) untuk satu periode, di mana periode itu sendiri diterjemahkan dari tabel pemetaan periode-ke-rentang-tanggal.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'tbl_list_journal', 'desc' => 'Sumber agregasi debit/kredit per akun COA.'],
                        ['name' => 'saldo_awal_tb', 'desc' => 'Saldo awal per akun COA (versi yang sama dipakai General Ledger lama).'],
                        ['name' => 'tbl_tgl_tb', 'desc' => 'Tabel pemetaan "periode akuntansi" (bulan/tahun) ke rentang tanggal kalender aktualnya — memungkinkan periode tutup buku tidak harus persis mengikuti kalender.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Account.'],
                        ['name' => 'master_coa_ctg1', 'desc' => 'Hierarki kategori akun tingkat 1, bagian dari rantai kategori COA.'],
                    ],
                    'notes' => [
                        'Sama seperti General Ledger lama, memakai saldo_awal_tb (bukan fs_saldo_awal_tb) sebagai sumber saldo awal.',
                        'Terdapat markup modal detail BPB (sisa salin-tempel dari halaman list BPB) yang tidak berfungsi apa pun di halaman ini — klik baris tidak memicu apa-apa.',
                    ],
                ],
            ],
            '_s1' => ['section' => 'Sub Ledger'],
            'other-receivable' => [
                'title' => 'Other Receivable', 'icon' => 'fa-fax', 'path' => 'module/AP/other_receivable_report.php',
                'doc' => [
                    'summary' => 'Laporan piutang lain-lain (murni baca) — dua mode perhitungan tergantung akun COA yang dipilih: "Uang Muka Material" berbasis BPB, atau memo generik biasa.',
                    'purpose' => 'Menampilkan saldo & mutasi akun receivable non-dagang (COA 1.34.05 "Uang Muka Material" atau 1.34.04 generik) — penambahan dari BPB/Memo, pengurangan lewat alokasi Debit Note ke buyer.',
                    'variants' => [
                        ['name' => 'COA 1.34.05 — Uang Muka Material', 'icon' => 'fa-cube', 'desc' => 'Berbasis BPB (bukan Memo), dikurangi lewat Debit Note + Alokasi serta jurnal penyesuaian GM.'],
                        ['name' => 'COA lain (default 1.34.04)', 'icon' => 'fa-file-text-o', 'desc' => 'Berbasis Memo generik (memo_h/memo_det, ditagihkan="Y"), dikurangi lewat Debit Note + Alokasi.'],
                    ],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'mastercoa_v2', 'desc' => 'Dropdown pilihan akun COA.'],
                        ['name' => 'bpb', 'desc' => 'Sumber Uang Muka Material, khusus COA 1.34.05.'],
                        ['name' => 'memo_h', 'desc' => 'Sumber Memo generik, mode default.'],
                        ['name' => 'memo_det', 'desc' => 'Detail Memo.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs.'],
                        ['name' => 'po_header', 'desc' => 'Data PO terkait (mode Uang Muka Material).'],
                        ['name' => 'po_header_draft', 'desc' => 'Data draf PO terkait.'],
                        ['name' => 'tbl_debitnote_h', 'desc' => 'Header Debit Note, pengurang saldo.'],
                        ['name' => 'tbl_debitnote_det', 'desc' => 'Detail Debit Note.'],
                        ['name' => 'tbl_alokasi', 'desc' => 'Header alokasi Debit Note ke dokumen sumber.'],
                        ['name' => 'tbl_alokasi_detail', 'desc' => 'Detail alokasi.'],
                        ['name' => 'tbl_list_journal', 'desc' => 'Jurnal penyesuaian manual (GM) terhadap akun ini.'],
                        ['name' => 'req_dn_h', 'desc' => 'Header Request Debit Note, khusus mode Uang Muka Material.'],
                        ['name' => 'req_dn', 'desc' => 'Detail Request Debit Note.'],
                    ],
                    'notes' => [
                        'Dua ekspor Excel terpisah: satu untuk mode Memo generik, satu untuk mode Uang Muka Material.',
                        'Terdapat tombol "Copy Saldo" yang sudah disembunyikan dari tampilan (dikomentari), namun handler JS-nya masih tersambung ke endpoint copy_saldo_tb.php yang sesungguhnya aktif — endpoint tersebut mengabaikan parameter akun & selalu memproses seluruh tabel staging, sehingga saat ini praktis hanya benar-benar terjangkau lewat menu Financial Statement (tombol Copy Saldo Trial Balance), bukan dari halaman ini.',
                        'Terdapat beberapa batas tanggal & pengecualian satu dokumen tertentu yang tertanam tetap di kode — kemungkinan tambalan perbaikan data historis, bukan aturan bisnis umum.',
                    ],
                ],
            ],
            'other-payable' => [
                'title' => 'Other Payable', 'icon' => 'fa-fax', 'path' => 'module/AP/other_payable_report.php',
                'doc' => [
                    'summary' => 'Laporan utang lain-lain (murni baca) — sumber data sama seperti Other Receivable (tabel Memo), namun dikurangi lewat pembayaran (Payment Voucher/Bank Out), bukan Debit Note.',
                    'purpose' => 'Menampilkan saldo & mutasi akun payable non-dagang (selalu akun 2.18.02, tidak ada pilihan akun di layar) — penambahan dari Memo yang belum ditagihkan, pengurangan lewat Payment Voucher yang mengacu ke Memo tersebut.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'memo_h', 'desc' => 'Sumber saldo utang — dokumen Memo yang sama dipakai Other Receivable, sisi utang.'],
                        ['name' => 'memo_det', 'desc' => 'Detail Memo.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs.'],
                        ['name' => 'tbl_pv', 'desc' => 'Detail Payment Voucher yang mengacu ke Memo (reff_doc), pengurang saldo.'],
                        ['name' => 'tbl_pv_h', 'desc' => 'Header Payment Voucher terkait.'],
                        ['name' => 'b_bankout_det', 'desc' => 'Detail Bank Out realisasi pembayaran.'],
                        ['name' => 'b_bankout_h', 'desc' => 'Header Bank Out.'],
                        ['name' => 'tbl_list_journal', 'desc' => 'Jurnal penyesuaian manual (GM) terhadap akun ini.'],
                    ],
                    'notes' => [
                        'Berbeda dari Other Receivable: tidak ada pilihan akun COA di layar — laporan ini selalu memakai satu akun tetap (2.18.02).',
                        'Sama seperti Other Receivable, terdapat tombol "Copy Saldo" tersembunyi dengan handler yang masih tersambung ke endpoint yang sama.',
                        'Tersedia ekspor Excel terpisah.',
                    ],
                ],
            ],
            'purchase-advance' => [
                'title' => 'Purchase Advance', 'icon' => 'fa-fax', 'path' => 'module/AP/purchase_advance_report.php',
                'doc' => [
                    'summary' => 'Laporan saldo Uang Muka Pembelian (murni baca) ke supplier — mencakup seluruh akun COA yang namanya mengandung "Uang Muka Pembelian", bisa dipecah per Profit Center.',
                    'purpose' => 'Menampilkan penambahan (posting Bank Out/Petty Cash Out ke akun uang muka) dan pengurangan (Settlement lewat Kontrabon DP/CBD) saldo uang muka pembelian per akun/Profit Center.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'mastercoa_v2', 'desc' => 'Dropdown akun COA bernama mengandung "Uang Muka Pembelian".'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center, untuk breakdown (khusus perhitungan versi baru, mulai 1 September 2025).'],
                        ['name' => 'tbl_list_journal', 'desc' => 'Sumber penambahan (posting Bank Out/Petty Cash Out) dan label "Settlement" untuk pengurangan.'],
                        ['name' => 'b_bankout_h', 'desc' => 'Header Bank Out sumber penambahan saldo.'],
                        ['name' => 'c_petty_cashout_h', 'desc' => 'Header Petty Cash Out sumber penambahan saldo.'],
                        ['name' => 'tbl_pv', 'desc' => 'Detail Payment Voucher terkait.'],
                        ['name' => 'kontrabon_h_cbd', 'desc' => 'Header Kontrabon CBD, sumber pengurangan (Settlement).'],
                        ['name' => 'kontrabon_h', 'desc' => 'Header Kontrabon Reguler — dp_value dipakai sebagai pengurang saldo uang muka.'],
                        ['name' => 'kontrabon_cbd', 'desc' => 'Detail Kontrabon CBD.'],
                        ['name' => 'kontrabon_dp', 'desc' => 'Detail Kontrabon DP.'],
                        ['name' => 'kontrabon_ftr', 'desc' => 'Penaut Kontrabon ke FTR.'],
                        ['name' => 'ftr_cbd', 'desc' => 'Data FTR CBD terkait.'],
                        ['name' => 'ftr_dp', 'desc' => 'Data FTR DP terkait.'],
                        ['name' => 'list_payment_cbd', 'desc' => 'List Payment CBD terkait.'],
                        ['name' => 'list_payment_dp', 'desc' => 'List Payment DP terkait.'],
                        ['name' => 'c_petty_cashin_h', 'desc' => 'Petty Cash In terkait (versi perhitungan baru).'],
                        ['name' => 'tbl_adjust_purchase_advance', 'desc' => 'Penyesuaian manual saldo uang muka pembelian.'],
                        ['name' => 'pa_saldo_awal', 'desc' => 'Saldo awal uang muka pembelian.'],
                    ],
                    'notes' => [
                        'Terdapat DUA "mesin hitung" berbeda dalam satu berkas yang sama: tanggal mulai ≥ 1 September 2025 memakai perhitungan baru (termasuk breakdown Profit Center & tabel penyesuaian manual), sedangkan tanggal sebelumnya memakai query lama yang lebih sederhana — dua ekspor Excel terpisah mengikuti percabangan tanggal yang sama.',
                        'Berkas menyimpan sisa versi query lama sebagai komentar di akhir berkas, tidak lagi dieksekusi — bukan bug, hanya jejak riwayat pengembangan.',
                    ],
                ],
            ],
            'prepaid-tax' => [
                'title' => 'Prepaid Tax', 'icon' => 'fa-fax', 'path' => 'module/AP/prepaid_tax_report.php',
                'doc' => [
                    'summary' => 'Laporan saldo Pajak Dibayar Dimuka (murni baca) — satu-satunya laporan Sub Ledger yang berbasis KATEGORI akun COA (bukan kode akun tertanam tetap), sehingga otomatis mengikuti akun mana pun yang ditandai kategori "PREPAID TAX".',
                    'purpose' => 'Menampilkan saldo yang bertambah dari posting BPB dan berkurang saat Kontrabon (pelunasan tagihan supplier) diproses, untuk akun mana pun yang tergolong kategori Pajak Dibayar Dimuka.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'mastercoa_v2', 'desc' => 'Sumber daftar akun COA berkategori "PREPAID TAX" — dropdown otomatis mengikuti kategori ini, tidak di-hardcode per kode akun.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center.'],
                        ['name' => 'acc_saldo_awal_prepaid_tax', 'desc' => 'Saldo awal per akun.'],
                        ['name' => 'bpb', 'desc' => 'Sumber penambahan saldo (posting BPB).'],
                        ['name' => 'bppb', 'desc' => 'Sumber penambahan/pengurangan dari retur.'],
                        ['name' => 'tbl_list_journal', 'desc' => 'Sumber jurnal BPB (penambahan), Kontrabon (pengurangan), dan penyesuaian manual (GM).'],
                    ],
                    'notes' => [
                        'Satu-satunya laporan di kelompok Sub Ledger yang dibangun dengan gaya SQL modern (CTE/WITH) dan backend AJAX server-side terpisah — tiga laporan Sub Ledger lainnya masih bergaya lama (dirender langsung di halaman saat form disubmit).',
                        'Terdapat batas tanggal 31 Desember 2025 tertanam tetap di kode sebagai titik pemisah data "sebelum" dan "berjalan".',
                    ],
                ],
            ],
            '_s2' => ['section' => 'Financial Statement'],
            'fs-ytd' => [
                'title' => 'Year To Date', 'icon' => 'fa-calendar', 'path' => 'module/AP/financial_statement.php?h_fs_system=1&h_report_type=ytd',
                'doc' => [
                    'summary' => 'Laporan Keuangan (Neraca, Laba Rugi, Trial Balance, Arus Kas) tampilan Year To Date, versi LAMA (Financial Statement 1) tanpa breakdown Profit Center — satu berkas yang sama dengan menu Monthly & Financial Statement 2, dibedakan lewat parameter URL.',
                    'purpose' => 'Menampilkan laporan keuangan kumulatif dari awal tahun sampai periode terpilih, versi sistem lama (FS1) — saat ini hanya tab Trial Balance yang benar-benar berfungsi, tab Neraca/Laba-Rugi/Arus Kas lainnya masih menampilkan "belum tersedia".',
                    'variants' => [
                        ['name' => 'Trial Balance', 'icon' => 'fa-balance-scale', 'desc' => 'Satu-satunya tab yang berfungsi penuh pada mode FS1 — memuat fs1_ytd/trial_balance.php.'],
                        ['name' => 'Neraca (SFP)', 'icon' => 'fa-file-text-o', 'desc' => 'Placeholder "belum tersedia" pada mode FS1.'],
                        ['name' => 'Laba Rugi (SPL)', 'icon' => 'fa-line-chart', 'desc' => 'Placeholder "belum tersedia" pada mode FS1.'],
                        ['name' => 'Arus Kas (Direct/Indirect)', 'icon' => 'fa-exchange', 'desc' => 'Placeholder "belum tersedia" pada mode FS1.'],
                    ],
                    'flow' => [
                        ['title' => 'Dimuat lewat parameter URL', 'desc' => 'Diakses dengan <code>h_fs_system=1&h_report_type=ytd</code> — kombinasi parameter inilah yang menentukan set tab mana yang di-include (lihat catatan disambiguasi).'],
                        ['title' => 'Tab Trial Balance dimuat', 'desc' => 'Sebagai EFEK SAMPING dari sekadar membuka tab ini (bukan aksi eksplisit pengguna), sistem menghapus lalu mengisi ulang tabel staging berisi cuplikan saldo saat ini — setiap kali tab dilihat.'],
                        ['title' => '(Hak akses terbatas) Copy Saldo', 'desc' => 'Tombol terpisah yang mengunci saldo akhir Trial Balance saat ini menjadi saldo awal bulan berikutnya secara permanen — lihat catatan.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'tbl_saldo_tb_temp', 'actions' => ['delete', 'insert'], 'desc' => 'Tabel staging cuplikan saldo Trial Balance FS1 — dihapus lalu diisi ulang otomatis setiap tab Trial Balance YTD dibuka, bukan aksi eksplisit pengguna.'],
                        ['name' => 'tbl_saldo_tb', 'actions' => ['insert'], 'desc' => 'Hanya terisi saat tombol "Copy Saldo" ditekan — komit permanen dari data staging di atas.'],
                        ['name' => 'saldo_awal_tb', 'actions' => ['update'], 'desc' => 'Saat "Copy Saldo" ditekan, kolom bulan berikutnya diisi dengan saldo akhir bulan berjalan.'],
                        ['name' => 'tbl_log_copsal_tb', 'actions' => ['insert'], 'desc' => 'Log audit setiap kali "Copy Saldo" dijalankan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'tbl_list_journal', 'desc' => 'Sumber mutasi jurnal untuk seluruh tab.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Account & kategori klasifikasi laporan.'],
                        ['name' => 'tbl_tgl_tb', 'desc' => 'Pemetaan periode akuntansi ke rentang tanggal kalender.'],
                    ],
                    'notes' => [
                        'PENTING DISAMBIGUASI: menu ini, menu "Monthly", dan menu "Financial Statement 2" adalah SATU BERKAS YANG SAMA (financial_statement.php) — dibedakan murni lewat parameter URL <code>h_fs_system</code> (1 = versi lama/FS1 tanpa Profit Center, 2 = versi baru/FS2 dengan breakdown Profit Center) dan <code>h_report_type</code> (ytd/monthly). Menu ini memakai h_fs_system=1&h_report_type=ytd. Pengguna bisa berpindah ke kombinasi lain langsung dari dropdown di halaman yang sama, tanpa membuka menu berbeda.',
                        'Tombol "Copy Saldo" adalah aksi TUTUP BUKU Trial Balance yang sesungguhnya — SAMA SEKALI TIDAK terhubung dengan mekanisme "Closing Periode" (tbl_closing_periode); keduanya adalah dua konsep "penutupan" yang berbeda dan tidak saling mengunci satu sama lain.',
                        'Hak akses "Financial Statement" (menu ini & Monthly) memakai sistem hak akses standar (menu id 53) — berbeda dari "Financial Statement 2" yang memakai pemeriksaan username tertanam tetap di kode (lihat menu tersebut).',
                    ],
                ],
            ],
            'fs-monthly' => [
                'title' => 'Monthly', 'icon' => 'fa-calendar-o', 'path' => 'module/AP/financial_statement.php?h_fs_system=1&h_report_type=monthly',
                'doc' => [
                    'summary' => 'Kembaran "Year To Date" namun tampilan Bulanan (Financial Statement 1, tanpa Profit Center) — berkas yang persis sama, hanya parameter URL berbeda.',
                    'purpose' => 'Menampilkan laporan keuangan versi lama (FS1) untuk satu bulan tertentu saja (bukan kumulatif dari awal tahun).',
                    'variants' => [
                        ['name' => 'Trial Balance', 'icon' => 'fa-balance-scale', 'desc' => 'Tab yang berfungsi penuh pada mode FS1.'],
                        ['name' => 'Neraca / Laba Rugi / Arus Kas', 'icon' => 'fa-file-text-o', 'desc' => 'Masih placeholder "belum tersedia" pada mode FS1 Bulanan, sama seperti mode YTD.'],
                    ],
                    'flow' => [
                        ['title' => 'Dimuat lewat parameter URL', 'desc' => 'Diakses dengan <code>h_fs_system=1&h_report_type=monthly</code>.'],
                        ['title' => '(Hak akses terbatas) Copy Saldo', 'desc' => 'Tombol yang sama dengan mode YTD tetap tersedia di halaman ini — menekannya akan memaksa penyegaran ulang cuplikan staging YTD di belakang layar untuk FS1 maupun FS2 sekaligus, sebelum benar-benar mengunci saldo.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'tbl_saldo_tb', 'actions' => ['insert'], 'desc' => 'Hanya terisi saat tombol "Copy Saldo" ditekan dari halaman ini — lihat menu "Year To Date" untuk detail mekanismenya.'],
                        ['name' => 'saldo_awal_tb', 'actions' => ['update'], 'desc' => 'Sama seperti Year To Date — diisi saat "Copy Saldo" ditekan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'tbl_list_journal', 'desc' => 'Sumber mutasi jurnal untuk periode bulan terpilih.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Account.'],
                        ['name' => 'tbl_tgl_tb', 'desc' => 'Pemetaan periode akuntansi ke rentang tanggal kalender.'],
                    ],
                    'notes' => [
                        'Satu berkas yang sama dengan "Year To Date" dan "Financial Statement 2" — lihat catatan lengkap pada menu "Year To Date" untuk penjelasan matriks h_fs_system/h_report_type.',
                        'Tidak seperti mode YTD, membuka tab Trial Balance Bulanan TIDAK memicu tulis-ulang otomatis ke tabel staging — penulisan hanya terjadi saat tombol Copy Saldo benar-benar ditekan.',
                    ],
                ],
            ],
            'financial-statement-2' => [
                'title' => 'Financial Statement 2', 'icon' => 'fa-balance-scale', 'path' => 'module/AP/financial_statement.php',
                'doc' => [
                    'summary' => 'Tampilan default saat menu diakses tanpa parameter URL apa pun — Financial Statement versi BARU (FS2) dengan breakdown per Profit Center (NAG/NAK), tampilan Bulanan.',
                    'purpose' => 'Versi laporan keuangan yang lebih baru dan lebih lengkap dari FS1, dengan breakdown Profit Center — namun hak aksesnya diperiksa dengan cara yang BERBEDA dari kedua menu FS1 (lihat catatan).',
                    'variants' => [
                        ['name' => 'Trial Balance', 'icon' => 'fa-balance-scale', 'desc' => 'Memakai fs_saldo_awal_tb sebagai saldo awal, bisa dipecah per Profit Center.'],
                        ['name' => 'Neraca (SFP) / Laba Rugi (SPL) / Arus Kas', 'icon' => 'fa-file-text-o', 'desc' => 'Lebih lengkap dibanding mode FS1 — sudah tersedia, tidak sekadar placeholder.'],
                    ],
                    'flow' => [
                        ['title' => 'Dibuka tanpa parameter', 'desc' => 'Otomatis jatuh ke mode FS2 + Bulanan (h_fs_system=2, h_report_type=monthly secara default) saat parameter URL kosong.'],
                        ['title' => 'Berpindah mode', 'desc' => 'Pengguna bisa berpindah ke ketiga kombinasi lain (FS1/FS2 × YTD/Bulanan) lewat dropdown di halaman yang sama tanpa membuka menu berbeda.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'fs_saldo_tb_temp', 'actions' => ['delete', 'insert'], 'desc' => 'Versi FS2 dari tabel staging Trial Balance — mengikuti pola yang sama dengan tbl_saldo_tb_temp pada FS1 (lihat menu "Year To Date"), disegarkan otomatis saat tab Trial Balance YTD versi FS2 dibuka.'],
                    ],
                    'tables_read' => [
                        ['name' => 'tbl_list_journal', 'desc' => 'Sumber mutasi jurnal, difilter juga per Profit Center.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Account.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center — dasar breakdown NAG/NAK yang menjadi ciri khas FS2.'],
                        ['name' => 'fs_saldo_awal_tb', 'desc' => 'Saldo awal per akun COA versi FS2 — terpisah dari saldo_awal_tb yang dipakai FS1.'],
                        ['name' => 'tbl_tgl_tb', 'desc' => 'Pemetaan periode akuntansi ke rentang tanggal kalender.'],
                    ],
                    'notes' => [
                        'Satu berkas yang sama dengan "Year To Date" dan "Monthly" — lihat catatan lengkap pada menu "Year To Date" untuk penjelasan matriks h_fs_system/h_report_type.',
                        'CELAH HAK AKSES: berbeda dari kedua menu FS1 yang memakai sistem hak akses standar (menu id 53), menu Financial Statement 2 memeriksa akses lewat DAFTAR USERNAME yang tertanam tetap di kode (hanya "indro", "willy", "steven") — menambah pengguna berwenang baru memerlukan perubahan kode aplikasi, bukan sekadar perubahan role/hak akses lewat menu Userrole.',
                        'Tombol "Copy Saldo" pada mode ini menutup Trial Balance untuk FS1 maupun FS2 SEKALIGUS dalam satu klik (dikonfirmasi lewat teks dialog konfirmasinya sendiri) — bukan dua aksi terpisah.',
                    ],
                ],
            ],
            'closing-periode' => [
                'title' => 'Closing Periode', 'icon' => 'fa-lock', 'path' => 'module/AP/closing-periode.php',
                'doc' => [
                    'summary' => 'Register buka/tutup periode akuntansi bulanan — HANYA mengubah status & mencatat jejak audit; TIDAK secara otomatis mengunci pembuatan transaksi baru di sebagian besar menu aplikasi.',
                    'purpose' => 'Menandai suatu bulan/tahun sebagai "Closed" atau "Open", dipakai sebagai REFERENSI oleh beberapa fitur lain (terutama alur Reverse dan Edit Jurnal) — namun bukan kunci keras tingkat database untuk sebagian besar transaksi (lihat catatan).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar periode', 'desc' => 'Satu baris per bulan untuk tahun terpilih — baris untuk 12 bulan disiapkan otomatis berstatus "Open" saat tahun itu pertama kali dibuka, tanpa perlu dibuat manual.'],
                        ['title' => 'Tutup periode', 'desc' => 'Status menjadi "Closed", dicatat siapa &amp; kapan (lock_by/lock_date).'],
                        ['title' => 'Buka kembali', 'desc' => 'Status menjadi "Open" (unlock_by/unlock_date dicatat), dengan kolom keterangan opsional untuk alasan pembukaan kembali.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Open', 'cls' => 'planned'],
                        ['label' => 'Closed', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'tbl_closing_periode', 'actions' => ['update'], 'desc' => 'Kolom status_closing diubah Open/Closed beserta audit lock/unlock — TIDAK PERNAH insert baris baru lewat halaman ini (baris 12 bulan disiapkan otomatis di baliknya, bukan input manual pengguna).'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'PENTING — CELAH KONTROL: menutup periode di sini TIDAK mencegah pembuatan Kontra Bon, Payment Voucher, Bank In/Out, atau jurnal manual baru bertanggal di periode tersebut — hampir seluruh skrip penyimpanan dokumen transaksional TIDAK memeriksa tabel ini sama sekali. Yang benar-benar terkunci hanyalah: (1) pembatasan tanggal minimal pada date-picker di sejumlah form (murni kenyamanan tampilan, bisa dilewati lewat pengiriman data langsung), (2) menonaktifkan opsi pembatalan/reversal atas Kontra Bon/Payment/Bank/Petty Cash yang bertanggal di periode tertutup (menu-menu Reverse), dan (3) menyembunyikan tombol Update/Cancel di menu Edit Jurnal untuk baris bertanggal periode tertutup (murni tampilan, penyimpanan backend-nya sendiri tidak memvalidasi ulang).',
                        'Jangan disamakan dengan tombol "Copy Saldo" pada menu Financial Statement (aksi tutup-buku Trial Balance yang sesungguhnya, tersimpan di tabel terpisah) — keduanya adalah dua mekanisme "penutupan" berbeda yang tidak saling terhubung.',
                        'Jangan disamakan pula dengan menu "Closing Info" (grup AP, melaporkan status closed_by/closed_date milik List Payment) maupun "Closing Period &gt; Fabric Warehouse" (grup Cost Accounting, mekanisme penutupan periode terpisah khusus gudang fabric, TIDAK terhubung ke tabel tbl_closing_periode ini) — tiga fitur berbeda yang kebetulan memakai kata "closing" yang sama.',
                    ],
                ],
            ],
            '_s3' => ['section' => 'Repost Journal'],
            'repost-bank-out' => [
                'title' => 'Bank Out', 'icon' => 'fa-university', 'path' => 'module/AP/repost-bank-out.php',
                'doc' => [
                    'summary' => 'Perkakas perbaikan (repair tool): mendeteksi jurnal Bank Out yang debit/kreditnya tidak seimbang, lalu menghapus dan membentuk ulang jurnalnya dari data sumber.',
                    'purpose' => 'Menangani kasus jurnal Bank Out yang rusak/tidak seimbang (selisih debit-kredit ≥ 1) dengan menghapus baris jurnal lama dan membangun ulang jurnal yang benar langsung dari data Bank Out/Payment Voucher sumbernya.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Deteksi ketidakseimbangan', 'desc' => 'Menampilkan jurnal Bank Out (type_journal = "Payment Voucher") dalam rentang tanggal tetap 2026-01-01 s.d. 2026-12-31 (nilai tahun tertanam di kode) yang total debit dan kreditnya tidak sama.'],
                        ['title' => 'Repost (checkbox terpilih)', 'desc' => 'Mengarsipkan jurnal lama ke tbl_list_journal_cancel, menghapusnya dari tbl_list_journal, lalu membentuk ulang dari b_bankout_h/b_bankout_det/tbl_pv/tbl_pv_h/b_bankout_adj_det — nomor jurnal baru tetap memakai no_bankout yang sama (bukan nomor baru).'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert', 'delete'], 'desc' => 'Arsip cadangan jurnal lama sebelum dihapus (baris lama untuk no_journal yang sama dibersihkan dulu sebelum arsip baru dibuat).'],
                        ['name' => 'tbl_list_journal', 'actions' => ['delete', 'insert'], 'desc' => 'Jurnal lama yang tidak seimbang dihapus, lalu dibentuk ulang dari data Bank Out/Payment Voucher sumber.'],
                    ],
                    'tables_read' => [
                        ['name' => 'b_bankout_h', 'desc' => 'Header Bank Out, sumber pembentukan ulang jurnal.'],
                        ['name' => 'b_bankout_det', 'desc' => 'Detail Bank Out.'],
                        ['name' => 'tbl_pv_h', 'desc' => 'Header Payment Voucher (Biaya) terkait, ikut jadi sumber.'],
                        ['name' => 'tbl_pv', 'desc' => 'Detail Payment Voucher (Biaya).'],
                        ['name' => 'b_bankout_adj_det', 'desc' => 'Baris penyesuaian Bank Out.'],
                        ['name' => 'mtax', 'desc' => 'Master pajak, untuk perhitungan ulang komponen pajak jurnal.'],
                    ],
                    'notes' => [
                        'PERINGATAN: rentang tanggal 2026-01-01 s.d. 2026-12-31 tertanam tetap di kode — halaman ini perlu diedit manual setiap pergantian tahun agar tetap berfungsi untuk periode berjalan.',
                        'Tidak ada penjagaan status/hak akses selain tombol Repost itu sendiri — siapa pun yang bisa membuka halaman ini dapat menghapus & membentuk ulang jurnal Bank Out mana pun yang tidak seimbang dalam rentang tahun yang di-hardcode.',
                        'Bug ditemukan: bagian akhir query pembentukan-ulang jurnal (pada berkas backend insert-report-bankout.php) memiliki karakter kutip & tanda kurung yang tidak wajar di ujung string SQL — sisa kemungkinan salin-tempel/edit yang belum tuntas, perlu diuji manual sebelum terlalu diandalkan.',
                        'Nomor jurnal hasil repost memakai ulang nomor Bank Out (no_bankout) itu sendiri sebagai no_journal — tidak ada nomor jurnal baru yang dibuat.',
                    ],
                ],
            ],
            'repost-bpb' => [
                'title' => 'BPB', 'icon' => 'fa-archive', 'path' => 'module/AP/repost-bpb.php',
                'doc' => [
                    'summary' => 'Kembaran "Repost Bank Out" untuk jurnal asal BPB (termasuk BPB Knitting & retur BPPB) — mendeteksi jurnal BPB yang tidak seimbang lalu membentuknya ulang dari data sumber.',
                    'purpose' => 'Menangani kasus jurnal BPB yang tidak seimbang dengan menghapus & membangun ulang langsung dari data BPB dan pemetaan akun COA, mencakup baik pembelian normal maupun retur.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Deteksi ketidakseimbangan', 'desc' => 'Menampilkan BPB berstatus "APPROVED" yang jumlah DPP/PPN/Total hasil rekonstruksi dari tbl_list_journal (lewat pencocokan pola nama akun COA) tidak sama dengan seharusnya.'],
                        ['title' => 'Repost (checkbox terpilih)', 'desc' => 'Mengarsipkan jurnal lama ke tbl_list_journal_cancel, menghapusnya, lalu membentuk ulang dua jenis jurnal: pembelian normal (dari bpb) dan retur (dari bppb, dengan sisi debit/kredit terbalik) — akun COA ditentukan lewat tabel pemetaan ap_mapping_coa_jurnal.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip cadangan jurnal lama sebelum dihapus.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['delete', 'insert'], 'desc' => 'Jurnal lama dihapus, dibentuk ulang berstatus "APPROVED" dari data BPB/BPPB sumber — kosakata status ini berbeda dari Memorial Journal (Post/Draft/Cancel), menegaskan tbl_list_journal.status bukan satu enum yang konsisten lintas jenis dokumen.'],
                    ],
                    'tables_read' => [
                        ['name' => 'ap_masterrate', 'desc' => 'Kurs PAJAK per tanggal dan mata uang, dipakai mengonversi nilai BPB ke IDR saat jurnal dibentuk ulang.'],
                        ['name' => 'act_costing', 'desc' => 'Ikut di-JOIN (lewat jo, jo_det, so) untuk BPB non-GEN guna mengambil nomor WS - rantai JOIN ini dapat MENGGANDAKAN baris hasil (fan-out) bila satu BPB terkait banyak SO.'],
                        ['name' => 'bpb', 'desc' => 'Sumber data BPB Garment untuk pembentukan ulang jurnal pembelian normal.'],
                        ['name' => 'bpb_knitting', 'desc' => 'Sumber data BPB Knitting.'],
                        ['name' => 'bppb', 'desc' => 'Sumber data retur BPPB, untuk pembentukan ulang jurnal retur.'],
                        ['name' => 'masteritem', 'desc' => 'Master Item.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'ap_mapping_supplier', 'desc' => 'Pemetaan supplier ke kategori tertentu, memengaruhi pemilihan akun COA.'],
                        ['name' => 'po_header', 'desc' => 'Data Purchase Order terkait.'],
                        ['name' => 'ap_mapping_coa_jurnal', 'desc' => 'Tabel pemetaan akun COA jurnal berdasarkan tipe barang/kategori supplier/kelas barang — menentukan akun mana yang dipakai saat jurnal dibentuk ulang.'],
                    ],
                    'notes' => [
                        'Perbaikan (commit f78dd52, Sep 2026): repost sempat gagal total dengan galat Column count does not match value count, karena kedua perintah INSERT INTO tbl_list_journal tidak menyebutkan daftar kolom sementara tabelnya bertambah dua kolom (faktur_pajak dan tgl_faktur_pajak) dari pekerjaan PV. Sekarang daftar kolom ditulis EKSPLISIT (29 kolom) sehingga aman terhadap penambahan kolom berikutnya; kedua kolom faktur pajak sengaja dibiarkan NULL karena repost BPB adalah tahap PPN UNBILLED (COA 1.52.07).',
                        'Perbaikan tampilan: dulu hasil GAGAL tetap muncul sebagai kotak hijau bertulis Berhasil karena pesan sukses ditampilkan tanpa membaca isi respons. Sekarang status dibaca dari isi respons sehingga kegagalan tampil sebagai galat, dan tabel hanya dimuat ulang bila benar-benar sukses.',
                        'Nomor jurnal hasil repost memakai ulang nomor BPB itu sendiri (bpbno_int) sebagai no_journal, sama seperti pola Repost Bank Out.',
                        'Berbeda dari Repost Bank Out, tidak ada rentang tahun yang di-hardcode di sini — filter tanggal dikendalikan pengguna lewat input di halaman.',
                        'Menu ini adalah versi "actionable" (bisa memperbaiki) dari menu Rekonsiliasi Jurnal-BPB, yang berbagi logika perbandingan serupa namun murni baca.',
                    ],
                ],
            ],
            '_s4' => ['section' => 'Lainnya'],
            'rekonsiliasi-jurnal-bpb' => [
                'title' => 'Rekonsiliasi Jurnal-BPB', 'icon' => 'fa-file-text-o', 'path' => 'module/AP/rekonsiliasi_jurnal_bpb.php',
                'doc' => [
                    'summary' => 'Laporan rekonsiliasi murni baca — membandingkan setiap BPB (status apa pun) dengan jurnal turunannya di tbl_list_journal, ditandai warna untuk selisih.',
                    'purpose' => 'Sebagai versi "hanya lihat" dari perbandingan yang dipakai menu Repost Journal &gt; BPB — tanpa tombol perbaikan, murni untuk memantau kesehatan data. Untuk memperbaiki BPB yang tidak seimbang, pengguna diarahkan ke menu Repost Journal &gt; BPB.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'bpb', 'desc' => 'Data BPB Garment, dibandingkan terhadap jurnal turunannya.'],
                        ['name' => 'bpb_knitting', 'desc' => 'Data BPB Knitting.'],
                        ['name' => 'bppb', 'desc' => 'Data retur BPPB.'],
                        ['name' => 'tbl_list_journal', 'desc' => 'Sumber rekonstruksi DPP/PPN/Total lewat pencocokan pola nama akun COA, untuk dibandingkan dengan nilai BPB aslinya.'],
                    ],
                    'notes' => [
                        'Warna penanda (hijau = seimbang, kuning = selisih kecil, merah = salah satu sisi nol) dihitung di sisi browser dari data JSON yang dikembalikan — tidak disimpan di database mana pun.',
                        'Mencakup BPB berstatus apa pun (termasuk yang belum Approved), berbeda dari Repost Journal &gt; BPB yang hanya menampilkan BPB berstatus "APPROVED" saja.',
                        'Tersedia ekspor Excel terpisah untuk laporan yang sama.',
                    ],
                ],
            ],
            'edit-journal' => [
                'title' => 'Edit Jurnal', 'icon' => 'fa-pencil', 'path' => 'module/AP/edit-journal.php',
                'doc' => [
                    'summary' => 'Editor manual langsung atas baris tbl_list_journal APA PUN asal dokumennya (BPB, Bank Out, Kontrabon, Payment Voucher, Memorial Journal) — satu-satunya penjagaan adalah kunci periode tutup buku, BUKAN status dokumen sumbernya.',
                    'purpose' => 'Memungkinkan koreksi manual atas baris jurnal yang sudah terlanjur salah, tanpa harus membatalkan dokumen sumbernya — namun karena tidak menyentuh tabel sumber (bpb/b_bankout_h/kontrabon_h/dst.), mengedit jurnal di sini bisa membuat jurnal & dokumen sumbernya menjadi tidak sinkron.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Seluruh jurnal dalam rentang tanggal, dikelompokkan per no_journal; baris yang tanggalnya jatuh dalam periode yang sudah ditutup (lihat menu Closing Periode) ditandai "PERIOD LOCKED" dan tombol Update/Cancel disembunyikan — inilah satu-satunya penjagaan yang ada, TIDAK ada pemeriksaan terhadap status jurnal maupun jenis dokumen sumbernya.'],
                        ['title' => 'Edit', 'desc' => 'Memuat seluruh baris satu no_journal ke form yang bisa ditambah/disisipkan/dihapus barisnya, lalu validasi keseimbangan debit-kredit di sisi server sebelum disimpan.'],
                        ['title' => 'Simpan', 'desc' => 'Jurnal lama diarsipkan ke tbl_list_journal_cancel lalu dihapus, kemudian ditulis ulang ke tbl_list_journal dengan status di-set ulang menjadi "Post" — TERMASUK untuk jurnal yang asalnya berstatus "APPROVED" (asal BPB) atau kosakata lainnya, sehingga status aslinya ikut berubah tanpa disengaja.'],
                        ['title' => 'Cancel', 'desc' => 'Mengarsipkan &amp; menghapus permanen jurnal manapun dari tbl_list_journal — dokumen sumber aslinya (BPB/Bank Out/Kontrabon/dst.) TIDAK ikut diubah atau ditandai, berpotensi menyisakan dokumen sumber tanpa jurnal.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'tbl_log_edit_mj', 'actions' => ['insert'], 'desc' => 'Log audit setiap kali sebuah jurnal diedit lewat halaman ini, mencatat siapa & kapan.'],
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip cadangan sebelum baris jurnal lama dihapus, baik saat Edit maupun Cancel.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['delete', 'insert'], 'desc' => 'Baris lama dihapus, ditulis ulang dengan status "Post" saat Edit; dihapus permanen (tanpa ditulis ulang) saat Cancel.'],
                        ['name' => 'tbl_edit_mj', 'actions' => ['insert'], 'desc' => 'Arsip header Memorial Journal sebelum dihapus — HANYA terisi apabila type_journal jurnal yang diedit kebetulan cocok dengan salah satu nama kategori Memorial Journal.'],
                        ['name' => 'tbl_memorial_journal', 'actions' => ['delete', 'insert'], 'desc' => 'Dihapus lalu ditulis ulang — HANYA berlaku untuk jurnal yang teridentifikasi berasal dari Memorial Journal; jurnal asal BPB/Bank Out/dst. tidak menyentuh tabel ini sama sekali.'],
                    ],
                    'tables_read' => [
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center untuk dropdown per baris (endpoint cc.php) - disaring id_pc sesuai Profit Center baris DAN group2 sesuai grup COA baris.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center untuk dropdown per baris.'],
                        ['name' => 'tbl_closing_periode', 'desc' => 'Menentukan apakah tanggal jurnal berada dalam periode yang sudah ditutup — satu-satunya sumber penjagaan edit di halaman ini.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Account, untuk pemilihan akun pada form edit.'],
                        ['name' => 'master_category_mj', 'desc' => 'Dipakai untuk mencocokkan type_journal ke kategori Memorial Journal — menentukan apakah tbl_memorial_journal ikut diproses.'],
                    ],
                    'notes' => [
                        'Perbaikan (commit d3cecbf, Sep 2026): dropdown Cost Center kini dibatasi DUA lapis - Profit Center DAN grup COA baris tersebut (endpoint cc.php). Sebelumnya hanya disaring Profit Center sehingga cost center yang grupnya tidak cocok tetap dapat dipilih. Cost Center juga ikut dikosongkan saat COA diganti, bukan hanya saat Profit Center diganti.',
                        'Perbaikan (commit d3cecbf): validasi balance dulu membulatkan debit, credit dan rate ke 2 desimal SEBELUM dikali rate. Karena nilai jurnal bisa 4 desimal, jurnal yang sebenarnya balance dilaporkan Not Balanced (contoh selisih 151,18) dan nilai bulat itu ikut TERSIMPAN sehingga merusak angka asli. Sekarang pembulatan hanya dikenakan pada hasil debit_idr dan credit_idr.',
                        'Tampilan debit, credit, debit IDR dan credit IDR kini memakai 4 angka di belakang koma - baik di tabel daftar maupun di modal edit dan kartu total - supaya digit di belakang tidak tersamarkan.',
                        'PENTING — RISIKO DESAIN: halaman ini bisa mengedit atau membatalkan jurnal APA PUN, dari dokumen sumber APA PUN, sepanjang tanggalnya belum berada di periode tertutup — tidak peduli status dokumen sumbernya (Draft/Approved/dsb.). Mengedit/membatalkan jurnal asal BPB/Bank Out/Kontrabon di sini TIDAK mengubah status dokumen sumber tersebut, sehingga berpotensi menciptakan ketidaksinkronan yang baru terdeteksi lewat menu Rekonsiliasi Jurnal-BPB atau Repost Journal.',
                        'Menyimpan hasil edit SELALU menetapkan status jurnal menjadi "Post" — jurnal yang asalnya berstatus "APPROVED" (dari BPB) akan berubah kosakata statusnya menjadi "Post" setelah diedit di sini.',
                        'Nama kolom tbl_log_edit_mj.no_mj tetap dipakai untuk mencatat NOMOR JURNAL APA PUN yang diedit (bukan cuma Memorial Journal) — sisa penamaan dari saat fitur ini awalnya khusus Memorial Journal.',
                    ],
                ],
            ],
        ],
    ],
    'cost-accounting' => [
        'title' => 'Cost Accounting',
        'icon'  => 'fa-industry',
        'items' => [
            '_s1' => ['section' => 'Fabric'],
            'ca-list-barcode' => [
                'title' => 'List Barcode', 'icon' => 'fa-barcode', 'path' => 'module/AP/ca_fabric_list_barcode.php',
                'doc' => [
                    'summary' => 'Registry master barcode/roll fabric — satu baris per roll/lot fisik yang diterima, murni baca (kecuali satu aksi penyegaran lewat prosedur database).',
                    'purpose' => 'Menelusuri setiap barcode/roll fabric individual beserta dokumen penerimaan, item, JO/WS/style, dan harga beli aslinya — unit atom pelacakan biaya fabric di seluruh kelompok menu ini.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan katalog', 'desc' => 'Dari view database <code>vw_whs_master_barcode</code> — gabungan tabel penerimaan, lokasi barcode, item, dan data WS/style.'],
                        ['title' => '"Update Barcode Out"', 'desc' => 'Memanggil prosedur tersimpan (stored procedure) langsung di database untuk menyegarkan status keluar barcode — logikanya berada sepenuhnya di sisi database, tidak terlihat di kode PHP aplikasi.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'vw_whs_master_barcode', 'desc' => 'View database yang menggabungkan whs_inmaterial_fabric, whs_lokasi_inmaterial, masteritem, dan data WS/style (act_costing/so/jo_det) — sumber tunggal katalog barcode.'],
                    ],
                    'notes' => [
                        'Tombol "Update Barcode Out" memanggil prosedur tersimpan (CALL whs_update_out_barcode()) — perubahan datanya terjadi di sisi database, di luar jangkauan kode PHP yang bisa ditelusuri di sini.',
                        'Tersedia ekspor Excel terpisah.',
                    ],
                ],
            ],
            'ca-trx-item-in' => [
                'title' => 'Trx Item In', 'icon' => 'fa-cart-arrow-down', 'path' => 'module/AP/ca_fabric_trx_in_new.php',
                'doc' => [
                    'summary' => 'Laporan penerimaan fabric dirangkum ke level ITEM (murni baca) — rollup dari data per-barcode yang sama dipakai menu List Barcode.',
                    'purpose' => 'Menampilkan penerimaan fabric dalam bentuk ringkas per item (bukan per roll/barcode individual), untuk kebutuhan analisis yang tidak perlu detail per roll.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'whs_inmaterial_fabric', 'desc' => 'Header penerimaan fabric.'],
                        ['name' => 'whs_inmaterial_fabric_det', 'desc' => 'Detail penerimaan per item/JO.'],
                        ['name' => 'whs_lokasi_inmaterial', 'desc' => 'Detail level barcode/roll — sumber rollup ke level item.'],
                        ['name' => 'masteritem', 'desc' => 'Master Item.'],
                        ['name' => 'act_costing', 'desc' => 'Data WS/style terkait.'],
                        ['name' => 'so', 'desc' => 'Sales Order terkait, untuk WS/style.'],
                        ['name' => 'jo_det', 'desc' => 'Detail Job Order.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs harian, untuk komponen bertanda mata uang PAJAK.'],
                    ],
                    'notes' => [
                        'Bukan sumber data baru — murni rollup dari baris yang sudah tersimpan di whs_lokasi_inmaterial/whs_inmaterial_fabric_det ke level item.',
                        'Harga per unit memakai np_price_rev (revisi) apabila ada, jika tidak memakai np_price asli — pola IFNULL(revisi, asli) ini konsisten dipakai di hampir seluruh laporan Fabric (lihat menu Revisi Barcode untuk mekanisme revisinya).',
                    ],
                ],
            ],
            'ca-trx-barcode-in' => [
                'title' => 'Trx Barcode In', 'icon' => 'fa-cart-arrow-down', 'path' => 'module/AP/ca_fabric_trx_in_barcode.php',
                'doc' => [
                    'summary' => 'Tampilan detail penerimaan fabric per BARCODE/roll — PENTING: sumber datanya BUKAN basis data aplikasi ini, ditarik langsung dari sistem eksternal terpisah "NDS WIP".',
                    'purpose' => 'Menampilkan penerimaan fabric pada level barcode/roll individual, sebagai rekan sejawat granular dari Trx Item In.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [],
                    'notes' => [
                        'PENTING: layar ini mengambil datanya lewat pemanggilan API ke sistem eksternal "NDS WIP" (http://10.10.5.62:8000/nds_wip/...), BUKAN dari basis data signalbit_erp aplikasi ini — data per-barcode versi lokal tetap ada di tabel whs_lokasi_inmaterial (dipakai laporan lain di grup ini), namun tampilan ini sepenuhnya menampilkan hasil dari sistem lain.',
                        'Tersedia ekspor Excel terpisah.',
                    ],
                ],
            ],
            'ca-trx-item-out' => [
                'title' => 'Trx Item Out', 'icon' => 'fa-paper-plane', 'path' => 'module/AP/ca_fabric_trx_out_item.php',
                'doc' => [
                    'summary' => 'Laporan pengeluaran fabric (BPPB — Bukti Pengeluaran Barang) dirangkum ke level ITEM — murni baca, sisi "keluar" yang berpasangan dengan Trx Item In.',
                    'purpose' => 'Menampilkan fabric yang keluar dari inventaris ke berbagai tujuan (produksi, subkontraktor, retur, sample room, penjualan), dirangkum per item.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'whs_bppb_h', 'desc' => 'Header BPPB (pengeluaran fabric).'],
                        ['name' => 'whs_bppb_det', 'desc' => 'Detail baris pengeluaran, termasuk kolom id_roll yang menunjuk balik ke barcode/roll spesifik yang dipakai.'],
                        ['name' => 'masteritem', 'desc' => 'Master Item.'],
                        ['name' => 'act_costing', 'desc' => 'Data WS/style terkait.'],
                        ['name' => 'so', 'desc' => 'Sales Order terkait.'],
                        ['name' => 'jo_det', 'desc' => 'Detail Job Order.'],
                    ],
                    'notes' => [
                        'whs_bppb_h/det adalah cermin sisi "keluar" dari whs_inmaterial_fabric/det (sisi "masuk") — setiap baris keluar (id_roll) menunjuk balik ke barcode/roll spesifik yang dipakai, sehingga metode costing yang berlaku adalah identifikasi spesifik per roll (specific identification), bukan rata-rata tertimbang maupun FIFO.',
                        'Kategori jenis pengeluaran (jenis_pengeluaran) mencakup: Pemakaian Produksi, Pengiriman ke Subkontraktor (CMT/Jasa), Retur Pembelian (Lokal/Impor), Pemakaian Sample Room, Penjualan (Group/Non-Group), dan Lainnya.',
                    ],
                ],
            ],
            'ca-trx-barcode-out' => [
                'title' => 'Trx Barcode Out', 'icon' => 'fa-paper-plane', 'path' => 'module/AP/ca_fabric_trx_out_barcode.php',
                'doc' => [
                    'summary' => 'Kembaran "Trx Barcode In" untuk sisi keluar — sama-sama bersumber dari sistem eksternal NDS WIP, dengan satu bug: tombol ekspornya rusak.',
                    'purpose' => 'Menampilkan pengeluaran fabric pada level barcode/roll individual.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [],
                    'notes' => [
                        'PENTING: sama seperti Trx Barcode In, data pada layar ini bersumber dari API sistem eksternal "NDS WIP", bukan basis data aplikasi ini.',
                        'BUG: tombol ekspor Excel pada halaman ini menunjuk ke berkas yang TIDAK PERNAH DIBUAT di server (ekspor_ca_fabric_trx_out_barcode.php tidak ada) — mengklik tombol ekspor akan gagal (404).',
                    ],
                ],
            ],
            'ca-summary-item' => [
                'title' => 'Summary Item', 'icon' => 'fa-calculator', 'path' => 'module/AP/ca_fabric_summary_item.php',
                'doc' => [
                    'summary' => 'Laporan mutasi (rollforward: Saldo Awal → rincian Masuk → rincian Keluar → Saldo Akhir) fabric per ITEM — tampilan layar bersumber dari sistem eksternal NDS WIP, namun ekspor Excel dihitung lokal dengan logika bercabang menurut tanggal.',
                    'purpose' => 'Memberi gambaran mutasi fabric per item dalam satu periode, dipecah menurut kategori masuk/keluar.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [],
                    'notes' => [
                        'Sejak 1 November 2025, tampilan layar sepenuhnya bersumber dari API NDS WIP eksternal — namun ekspor Excel untuk periode mulai 1 November 2025 memakai skrip lokal baru, sedangkan periode sebelumnya memakai skrip lokal lama. Akibatnya data yang tampil di layar dan data hasil ekspor berpotensi dihasilkan oleh dua sistem perhitungan yang berbeda untuk rentang tanggal yang sama.',
                        'Kategori Masuk: Pembelian Lokal/Impor, Retur dari Subkontraktor-Fabric, Retur dari Produksi, Retur dari Sample Room. Kategori Keluar: Kirim ke Produksi, Kirim ke Subkontraktor-Fabric, Retur ke Supplier Lokal/Impor, Kirim ke Sample Room, Penjualan Non-Group/Group, Lainnya.',
                    ],
                ],
            ],
            'ca-summary-barcode' => [
                'title' => 'Summary Barcode', 'icon' => 'fa-calculator', 'path' => 'module/AP/ca_fabric_summary_barcode.php',
                'doc' => [
                    'summary' => 'Kembaran "Summary Item" namun per BARCODE, ditambah kolom Penyesuaian (Adjustment) dan satu aksi tulis penting: "Copy Saldo" — menyalin saldo akhir bulan berjalan menjadi saldo awal bulan berikutnya.',
                    'purpose' => 'Rollforward per barcode, sekaligus menyediakan mekanisme roll-forward saldo awal bulanan manual (satu-satunya penulisan data di seluruh laporan mutasi Fabric).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan mutasi', 'desc' => 'Sumber layar dari API NDS WIP eksternal, sama seperti Summary Item.'],
                        ['title' => 'Copy Saldo', 'desc' => 'Hanya aktif saat filter tepat satu bulan kalender penuh — menghitung ulang saldo akhir per barcode secara LOKAL (bukan dari API) lewat rantai CTE panjang yang menggabungkan saldo awal periode sebelumnya, data masuk/keluar, dan penyesuaian manual, lalu menyimpannya sebagai saldo AWAL bulan berikutnya.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'whs_saldo_awal_nilai_persediaan', 'actions' => ['delete', 'insert'], 'desc' => 'Baris bulan berikutnya yang sudah ada dihapus dulu, lalu saldo akhir bulan berjalan dituliskan sebagai saldo awal bulan berikutnya (hanya untuk barcode dengan saldo akhir tidak nol).'],
                    ],
                    'tables_read' => [
                        ['name' => 'whs_mut_lokasi', 'desc' => 'Tabel pemetaan barcode lama→baru untuk kasus penomoran ulang — memuat daftar tertanam tetap sekitar 15 pasangan nomor barcode spesifik, kemungkinan tambalan data historis satu kali.'],
                        ['name' => 'whs_inmaterial_fabric', 'desc' => 'Sumber data masuk.'],
                        ['name' => 'whs_lokasi_inmaterial', 'desc' => 'Sumber data masuk level barcode.'],
                        ['name' => 'whs_bppb_h', 'desc' => 'Sumber data keluar.'],
                        ['name' => 'whs_bppb_det', 'desc' => 'Sumber data keluar level barcode.'],
                        ['name' => 'whs_adjust_nilai_persediaan', 'desc' => 'Penyesuaian manual nilai persediaan per barcode.'],
                    ],
                    'notes' => [
                        'Tombol "Copy Saldo" hanya aktif apabila rentang filter tepat satu bulan kalender penuh dipilih.',
                        'Ini adalah SATU-SATUNYA aksi tulis di seluruh kelompok laporan mutasi Fabric (Summary Item/Barcode/Subcont) — semua laporan lain di kelompok ini murni baca.',
                    ],
                ],
            ],
            'ca-summary-subcont' => [
                'title' => 'Summary Subcont', 'icon' => 'fa-calculator', 'path' => 'module/AP/ca_fabric_summary_sc.php',
                'doc' => [
                    'summary' => 'Laporan mutasi fabric di tangan Subkontraktor — versi LAMA, sepenuhnya dihitung lokal (tidak lewat API eksternal), memakai skema tabel yang sudah tergantikan oleh menu "Summary Subcont New".',
                    'purpose' => 'Menampilkan saldo fabric yang sedang berada di subkontraktor (dikirim untuk proses CMT/jasa) beserta mutasinya, versi perhitungan lama.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'ca_saldoawal_subkon', 'desc' => 'Saldo awal versi skema lama — sudah tergantikan whs_saldo_awal_subcont_fabric pada versi baru.'],
                        ['name' => 'whs_bppb_h', 'desc' => 'Sumber kirim/retur ke-dari subkontraktor (filter jenis_pengeluaran mengandung "Subkontraktor").'],
                        ['name' => 'whs_bppb_det', 'desc' => 'Detail baris kirim/retur.'],
                        ['name' => 'whs_inmaterial_fabric', 'desc' => 'Untuk menghargai baris retur.'],
                        ['name' => 'whs_lokasi_inmaterial', 'desc' => 'Detail level barcode untuk penghargaan retur.'],
                        ['name' => 'ca_adjust_subcont', 'desc' => 'Penyesuaian manual versi LAMA (sebelum Desember 2024) — sudah TIDAK DIISI lagi oleh fitur input penyesuaian yang aktif saat ini (lihat menu Update Subcontractor), hanya dibaca laporan lama ini.'],
                        ['name' => 'ca_adjust_input', 'desc' => 'Penyesuaian manual versi baru (mulai Desember 2024), turut dibaca laporan lama ini sebagai pelengkap.'],
                    ],
                    'notes' => [
                        'Tabel ca_adjust_subcont sudah tidak lagi diisi oleh fitur input penyesuaian yang aktif saat ini — sisa skema sebelum migrasi ke ca_adjust_input pada Desember 2024, kini hanya dibaca laporan lama ini.',
                        'Berkas menyimpan versi query lama (sekitar 50 baris) sebagai komentar, tidak lagi dieksekusi — jejak riwayat pengembangan, bukan bug.',
                        'Untuk data terkini & sumber tabel yang sudah dimutakhirkan, gunakan menu "Summary Subcont New".',
                    ],
                ],
            ],
            'ca-summary-subcont-new' => [
                'title' => 'Summary Subcont New', 'icon' => 'fa-calculator', 'path' => 'module/AP/ca_fabric_summary_subcont.php',
                'doc' => [
                    'summary' => 'Versi BARU "Summary Subcont" — tampilan layar bersumber dari API NDS WIP eksternal (sama seperti Summary Item/Barcode), namun ekspor Excel SELALU dihitung lokal dari skema tabel terbaru, tidak bercabang menurut tanggal.',
                    'purpose' => 'Menggantikan "Summary Subcont" lama dengan sumber saldo awal & penyesuaian yang sudah dimutakhirkan.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'whs_saldo_awal_subcont_fabric', 'desc' => 'Saldo awal versi baru, pengganti ca_saldoawal_subkon.'],
                        ['name' => 'ca_adjust_input', 'desc' => 'Satu-satunya sumber penyesuaian manual di versi ini — ca_adjust_subcont lama tidak lagi dipakai di sini.'],
                        ['name' => 'whs_bppb_h', 'desc' => 'Sumber kirim/retur ke-dari subkontraktor.'],
                        ['name' => 'whs_bppb_det', 'desc' => 'Detail baris kirim/retur.'],
                        ['name' => 'whs_inmaterial_fabric', 'desc' => 'Untuk menghargai baris retur.'],
                        ['name' => 'whs_lokasi_inmaterial', 'desc' => 'Detail level barcode.'],
                    ],
                    'notes' => [
                        'Berbeda dari Summary Item, ekspor Excel di sini SELALU dihitung lokal (tidak ada percabangan berdasar tanggal) — namun tetap ada batas tanggal 30 November 2025 tertanam di kode ekspor sebagai titik pemisah data "sebelum" (dilipat ke saldo awal) vs data transaksi "berjalan".',
                        'Menggantikan menu "Summary Subcont" (lama) sepenuhnya secara sumber data — kedua tabel intinya (saldo awal & penyesuaian) sudah beda generasi skema.',
                    ],
                ],
            ],
            'ca-update-trx-in' => [
                'title' => 'Update Trx In', 'icon' => 'fa-pencil-square', 'path' => 'module/AP/update_bpb_fabric.php',
                'doc' => [
                    'summary' => 'Halaman koreksi harga penerimaan fabric — KEMUNGKINAN BESAR SUDAH MENJADI FITUR YATIM (orphaned): kolom yang diubahnya hanya dibaca laporan lama yang sudah digantikan, bukan laporan aktif manapun di grup menu ini.',
                    'purpose' => 'Mengizinkan input harga koreksi manual pada baris penerimaan fabric yang sudah tersimpan — namun lihat catatan mengenai relevansinya saat ini.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Cari dokumen penerimaan', 'desc' => 'Daftar dokumen dari whs_inmaterial_fabric.'],
                        ['title' => 'Masukkan harga koreksi', 'desc' => 'Disimpan ke kolom price_update TERPISAH pada whs_inmaterial_fabric_det — TIDAK mengubah kolom price asli maupun np_price/np_price_rev yang sesungguhnya dipakai laporan aktif.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'whs_inmaterial_fabric_det', 'actions' => ['update'], 'desc' => 'Kolom price_update diperbarui — lihat catatan risiko keamanan mengenai cara input ini diproses.'],
                    ],
                    'tables_read' => [
                        ['name' => 'whs_inmaterial_fabric', 'desc' => 'Daftar dokumen penerimaan.'],
                        ['name' => 'po_header', 'desc' => 'Data PO terkait.'],
                        ['name' => 'po_header_draft', 'desc' => 'Data draf PO terkait.'],
                    ],
                    'notes' => [
                        'RISIKO KEAMANAN: baik pengambilan data maupun penyimpanan pada halaman ini membangun query SQL lewat penggabungan string langsung dari input pengguna tanpa penyaringan (mysqli_real_escape_string) — celah SQL injection nyata yang perlu diperbaiki.',
                        'KEMUNGKINAN FITUR YATIM: kolom price_update yang diubah di sini hanya dibaca oleh dua berkas laporan LAMA yang sudah digantikan versi terbaru di grup menu ini — tidak ada satu pun laporan aktif (Trx Item In, Summary Item, dst.) yang membacanya. Untuk koreksi harga yang benar-benar berdampak ke laporan terkini, gunakan menu "Revisi Barcode".',
                        'Tidak ada tabel riwayat/audit — perubahan langsung menimpa nilai lama tanpa jejak.',
                        'Nama berkas (update_bpb_fabric.php, garis bawah) mirip namun BUKAN berkas yang sama dengan update-bpb-fabric.php (tanda hubung) milik menu "Update BPB &gt; Fabric" pada kelompok menu terpisah — dua fitur yang benar-benar berbeda, mudah tertukar saat mencari berkas.',
                    ],
                ],
            ],
            'ca-revisi-barcode' => [
                'title' => 'Revisi Barcode', 'icon' => 'fa-pencil-square', 'path' => 'module/AP/update_np_revisi.php',
                'doc' => [
                    'summary' => 'Mekanisme koreksi harga/mata uang per BARCODE yang sesungguhnya AKTIF & dipakai seluruh laporan Fabric — lewat unggah Excel massal, lengkap riwayat & pembatalan.',
                    'purpose' => 'Merevisi nilai pembelian (mata uang/harga) suatu barcode secara massal lewat unggah Excel — dibaca oleh hampir seluruh laporan Fabric lewat pola "pakai nilai revisi apabila ada, jika tidak pakai nilai asli".',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Unggah Excel', 'desc' => 'Setiap baris ditandai tipe IN (koreksi whs_lokasi_inmaterial, dicocokkan No Dokumen + No Barcode) atau OUT (koreksi whs_bppb_det, dicocokkan No BPPB + id_roll).'],
                        ['title' => 'Tersimpan', 'desc' => 'Header berstatus "Updated", detail per baris menyimpan nilai lama DAN baru untuk keperluan audit/pembatalan.'],
                        ['title' => 'Cancel', 'desc' => 'Nilai lama yang tersimpan ditulis balik ke tabel sumber, header berstatus "Cancel".'],
                    ],
                    'status_flow' => [
                        ['label' => 'Updated', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'update_np_revisi_h', 'actions' => ['insert'], 'desc' => 'Header pengajuan revisi — tabel dibuat otomatis oleh aplikasi sendiri (CREATE TABLE IF NOT EXISTS) saat fitur ini pertama kali dipakai, bukan lewat skrip instalasi terpisah.'],
                        ['name' => 'update_np_revisi_d', 'actions' => ['insert'], 'desc' => 'Detail per baris Excel yang diunggah, menyimpan nilai lama & baru.'],
                        ['name' => 'whs_lokasi_inmaterial', 'actions' => ['update'], 'desc' => 'Kolom np_curr_rev/np_price_rev diperbarui untuk baris tipe IN, dikembalikan ke nilai lama saat Cancel.'],
                        ['name' => 'whs_bppb_det', 'actions' => ['update'], 'desc' => 'Kolom np_curr_rev/np_price_rev diperbarui untuk baris tipe OUT, dikembalikan ke nilai lama saat Cancel.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Nomor: REV/GK/MMYY/00001.',
                        'INILAH mekanisme koreksi harga yang SESUNGGUHNYA berdampak — kolom np_price_rev/np_curr_rev yang diubah di sini dibaca lewat pola "pakai revisi jika ada" oleh hampir seluruh laporan Fabric (Trx Item In/Out, Copy Saldo pada Summary Barcode, dst.), berbeda dari menu "Update Trx In" yang kemungkinan sudah menjadi fitur yatim.',
                    ],
                ],
            ],
            'ca-update-subcontractor' => [
                'title' => 'Update Subcontractor', 'icon' => 'fa-pencil-square', 'path' => 'module/AP/adjust-subcont.php',
                'doc' => [
                    'summary' => 'Daftar & pembatalan penyesuaian manual fabric di tangan Subkontraktor — tabel intinya dipakai bersama oleh beberapa laporan mutasi Subkontraktor/Item.',
                    'purpose' => 'Melihat & membatalkan entri penyesuaian kuantitas/nilai fabric subkontraktor yang sudah dibuat (pembuatan baru dilakukan lewat form "Create" terpisah).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Dikelompokkan per No Dokumen dari ca_adjust_input.'],
                        ['title' => 'Cancel', 'desc' => 'Status berubah "N" beserta audit pembatalan.'],
                        ['title' => '(Form terpisah "Create")', 'desc' => 'Membuat penyesuaian baru, bernomor ADS/NAG/MMYY/00001.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'ca_adjust_input', 'actions' => ['update'], 'desc' => 'Status diubah "N" (dibatalkan) beserta audit pembatalan.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Berkas backend pembatalan bernama "calcel_adj_subcont.php" (salah ketik dari "cancel", dibiarkan apa adanya di kode produksi).',
                        'Tabel ca_adjust_input adalah sumber penyesuaian yang dipakai BERSAMA oleh menu Summary Subcont (lama, sebagian), Summary Subcont New, dan cadangan ekspor Summary Item — menggantikan tabel ca_adjust_subcont yang lebih lama.',
                    ],
                ],
            ],
            '_s2' => ['section' => 'Item General'],
            'item-general-usage' => [
                'title' => 'Item General Usage', 'icon' => 'fa-cart-arrow-down', 'path' => 'module/AP/item_general_usage.php',
                'doc' => [
                    'summary' => 'Laporan pemakaian Item General (ATK/perlengkapan umum/spare part) — sistem PARALEL terhadap Fabric, memakai tabel BPB/BPPB generik (bukan whs_inmaterial_fabric/whs_bppb_h), dan satu-satunya menu di kelompok ini yang dirancang memposting jurnal.',
                    'purpose' => 'Menampilkan penerimaan Item General beserta pemetaan akun COA-nya, dengan aksi "Repost Jurnal" untuk membentuk ulang jurnal dari data sumber — namun tombolnya saat ini tidak berfungsi (lihat catatan bug).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Dari tabel bpb (bukan whs_inmaterial_fabric), difilter kategori barang umum (bukan fabric), menampilkan akun COA hasil pemetaan.'],
                        ['title' => 'Repost Jurnal (dimaksudkan)', 'desc' => 'Mengarsipkan &amp; menghapus jurnal lama, membentuk ulang dari data bpb/bppb lewat tabel pemetaan akun — lihat catatan bug mengenai tombol ini.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'tbl_list_journal_cancel', 'actions' => ['insert'], 'desc' => 'Arsip jurnal lama sebelum dihapus (dimaksudkan; lihat catatan bug).'],
                        ['name' => 'tbl_list_journal', 'actions' => ['delete', 'insert'], 'desc' => 'Dihapus & dibentuk ulang dari data bpb/bppb (dimaksudkan; lihat catatan bug).'],
                    ],
                    'tables_read' => [
                        ['name' => 'bpb', 'desc' => 'Sumber data penerimaan Item General.'],
                        ['name' => 'masteritem', 'desc' => 'Master Item.'],
                        ['name' => 'po_header', 'desc' => 'Data PO terkait.'],
                        ['name' => 'po_header_draft', 'desc' => 'Data draf PO terkait.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'master_pc', 'desc' => 'Master Profit Center.'],
                        ['name' => 'mapping_category', 'desc' => 'Pemetaan kategori barang.'],
                        ['name' => 'reqnon_header', 'desc' => 'Data permintaan non-fabric terkait.'],
                        ['name' => 'b_master_cc', 'desc' => 'Master Cost Center.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Master Chart of Account, untuk kolom akun hasil pemetaan.'],
                        ['name' => 'ap_mapping_coa_jurnal', 'desc' => 'Tabel pemetaan akun COA jurnal berdasarkan tipe/kategori barang.'],
                    ],
                    'notes' => [
                        'BUG: tombol "Repost Jurnal" saat ini TIDAK BERFUNGSI — kolom checkbox seleksi baris yang seharusnya dipakai tombol ini tidak pernah didefinisikan di konfigurasi tabelnya, sehingga skrip selalu mendapati "tidak ada baris terpilih" walau pengguna sudah mencentang baris. Aksi tulis di atas TIDAK PERNAH benar-benar berjalan dalam kondisi aplikasi saat ini.',
                        'Berbeda dari seluruh laporan Fabric lainnya di kelompok menu ini (yang semuanya murni subledger inventaris tanpa jurnal), menu ini SATU-SATUNYA yang dirancang memposting ke tbl_list_journal — menegaskan sistem Item General memang sistem paralel yang terhubung ke akuntansi, berbeda dari sistem Fabric.',
                    ],
                ],
            ],
            '_s3' => ['section' => 'Update BPB'],
            'update-bpb-fabric' => [
                'title' => 'Fabric', 'icon' => 'fa-warehouse', 'path' => 'module/AP/update-bpb-fabric.php',
                'doc' => [
                    'summary' => 'Pengajuan koreksi harga/PPN pada baris BPB (penerimaan) atau BPPB (retur) fabric yang sudah tersimpan — alur draf lalu approval terpisah, BUKAN toggle sekali klik.',
                    'purpose' => 'Mencari BPB/BPPB fabric yang sudah diposting dan mengusulkan koreksi harga & PPN per baris beserta alasannya — perubahan sesungguhnya baru terjadi setelah disetujui lewat menu "Approve Fabric".',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Cari & usulkan', 'desc' => 'Form terpisah ("Create New") mencari baris BPB/BPPB fabric; baris yang sudah cocok dengan PO ditampilkan terkunci, sisanya bisa diusulkan harga/PPN barunya.'],
                        ['title' => 'Simpan', 'desc' => 'Tersimpan sebagai pengajuan berstatus "Draft" — BELUM mengubah data BPB/BPPB sama sekali di titik ini.'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Lewat menu "Approve Fabric" terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'update_bpb_fabric_h', 'actions' => ['insert', 'update'], 'desc' => 'Header pengajuan — tabel dibuat otomatis oleh aplikasi (CREATE TABLE IF NOT EXISTS) saat pertama dipakai; status diperbarui saat Cancel individual dari halaman ini.'],
                        ['name' => 'update_bpb_fabric', 'actions' => ['insert'], 'desc' => 'Detail baris koreksi harga/PPN yang diusulkan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'whs_inmaterial_fabric', 'desc' => 'Sumber pencarian dokumen tipe penerimaan (BPB).'],
                        ['name' => 'whs_inmaterial_fabric_det', 'desc' => 'Detail baris penerimaan.'],
                        ['name' => 'whs_bppb_h', 'desc' => 'Sumber pencarian dokumen tipe retur (BPPB).'],
                        ['name' => 'whs_bppb_ro', 'desc' => 'Detail baris retur.'],
                        ['name' => 'po_header', 'desc' => 'Untuk validasi kecocokan harga terhadap PO.'],
                        ['name' => 'po_header_draft', 'desc' => 'Draf PO terkait.'],
                    ],
                    'notes' => [
                        'Status "Waiting" masih disebut di logika warna badge namun TIDAK PERNAH benar-benar dipakai — pengajuan langsung berstatus "Draft" dan langsung layak diproses approval, sisa rencana fitur yang tidak jadi dipakai.',
                        'TIDAK ada pemeriksaan terhadap Closing Periode manapun (baik yang utama maupun yang khusus fabric) — periode tertutup TIDAK menghalangi pembuatan pengajuan koreksi ini.',
                        'Hati-hati saat menelusuri kode: nama berkas ini (update-bpb-fabric.php, tanda hubung) mirip namun BUKAN berkas yang sama dengan update_bpb_fabric.php (garis bawah) milik menu "Update Trx In" pada kelompok menu Fabric — dua fitur yang benar-benar berbeda.',
                    ],
                ],
            ],
            'approve-update-bpb-fabric' => [
                'title' => 'Approve Fabric', 'icon' => 'fa-check-circle', 'path' => 'module/AP/approve_update_bpb_fabric.php',
                'doc' => [
                    'summary' => 'Approval untuk pengajuan koreksi harga/PPN BPB Fabric — begitu disetujui, harga BARU langsung ditulis ke tabel BPB/BPPB asli, dan APABILA dokumen sumbernya sudah pernah diposting jurnal, jurnal lama dibalik & dibentuk ulang dengan nilai terkoreksi.',
                    'purpose' => 'Menyetujui atau menolak pengajuan koreksi dari menu "Update BPB &gt; Fabric".',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Pengajuan berstatus selain Approved/Cancel.'],
                        ['title' => 'Approve', 'desc' => 'Harga/PPN baru ditulis langsung ke tabel sumber (bpb/whs_inmaterial_fabric_det untuk penerimaan, atau bppb/whs_bppb_ro untuk retur). APABILA dokumen sumber berstatus "Approved" (sudah pernah diposting jurnal): jurnal lama dibalik (baris baru berlabel "Reverse ...", jurnal lama ditandai status "Updated") dan jurnal baru dibentuk ulang dengan angka terkoreksi, diberi label "(Rev N)".'],
                        ['title' => 'Cancel', 'desc' => 'Pengajuan ditolak — tidak ada perubahan apa pun ke BPB/BPPB maupun jurnal.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Draft', 'cls' => 'planned'],
                        ['label' => 'Approved', 'cls' => 'progress'],
                        ['label' => 'Cancel', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'update_bpb_fabric_h', 'actions' => ['update'], 'desc' => 'Status berubah Approved/Cancel.'],
                        ['name' => 'bpb', 'actions' => ['update'], 'desc' => 'Harga/PPN terkoreksi ditulis, khusus dokumen tipe penerimaan.'],
                        ['name' => 'whs_inmaterial_fabric_det', 'actions' => ['update'], 'desc' => 'Harga/PPN terkoreksi ditulis, khusus dokumen tipe penerimaan.'],
                        ['name' => 'bppb', 'actions' => ['update'], 'desc' => 'Harga/PPN terkoreksi ditulis, khusus dokumen tipe retur.'],
                        ['name' => 'whs_bppb_ro', 'actions' => ['update'], 'desc' => 'Harga/PPN terkoreksi ditulis, khusus dokumen tipe retur.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert', 'update'], 'desc' => 'HANYA apabila dokumen sumber sudah berstatus Approved (sudah diposting): jurnal lama ditandai "Updated", jurnal pembalik ("Reverse ...") disisipkan, dan jurnal baru terkoreksi ("(Rev N)") dibentuk.'],
                    ],
                    'tables_read' => [
                        ['name' => 'update_bpb_fabric', 'desc' => 'Detail pengajuan yang sedang diproses.'],
                        ['name' => 'mastersupplier', 'desc' => 'Master Supplier.'],
                        ['name' => 'po_header', 'desc' => 'Data PO terkait.'],
                        ['name' => 'po_header_draft', 'desc' => 'Draf PO terkait.'],
                        ['name' => 'masterrate', 'desc' => 'Kurs, untuk perhitungan ulang total.'],
                        ['name' => 'mastercoa_v2', 'desc' => 'Pemilihan akun COA untuk jurnal yang dibentuk ulang.'],
                    ],
                    'notes' => [
                        'Apabila BPB/BPPB sumber BELUM diposting jurnal (belum berstatus Approved), Approve di sini hanya menulis harga koreksi ke tabel sumber TANPA menyentuh jurnal sama sekali — efeknya baru terlihat nanti saat dokumen sumber tersebut benar-benar diposting.',
                        'Sama seperti menu pengajuannya, TIDAK ada pemeriksaan terhadap Closing Periode manapun.',
                    ],
                ],
            ],
            '_s4' => ['section' => 'Closing Period'],
            'closing-fabric-warehouse' => [
                'title' => 'Fabric Warehouse', 'icon' => 'fa-warehouse', 'path' => 'module/AP/ca_fabric_closing_periode.php',
                'doc' => [
                    'summary' => 'Register buka/tutup periode khusus gudang Fabric — TERPISAH SEPENUHNYA dari menu "Closing Periode" utama (grup Accounting), dan saat ini HANYA mencatat status & jejak audit — belum ada satu pun proses penyimpanan transaksi fabric yang benar-benar memeriksa/menghalangi berdasarkan tabel ini.',
                    'purpose' => 'Menandai bulan/tahun tertentu sebagai "Open"/"Closed" khusus untuk aktivitas gudang fabric, lengkap riwayat audit siapa mengunci/membuka & kapan.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar periode', 'desc' => 'Baris 12 bulan disiapkan otomatis (berstatus "Open") saat suatu tahun pertama kali dibuka — tabel status maupun tabel log dibuat otomatis oleh aplikasi sendiri saat halaman pertama kali diakses.'],
                        ['title' => 'Tutup periode', 'desc' => 'Status menjadi "Closed", dicatat siapa &amp; kapan mengunci.'],
                        ['title' => 'Buka kembali', 'desc' => 'Status menjadi "Open", dengan kolom alasan pembukaan kembali opsional lewat dialog konfirmasi.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Open', 'cls' => 'planned'],
                        ['label' => 'Closed', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'tbl_closing_periode_fabric_wh', 'actions' => ['insert', 'update'], 'desc' => 'Baris 12 bulan disiapkan otomatis saat tahun baru pertama dibuka; status_closing beserta audit lock/unlock diperbarui saat tutup/buka.'],
                        ['name' => 'tbl_closing_periode_fabric_wh_log', 'actions' => ['insert'], 'desc' => 'Satu baris log audit per perubahan status.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'PENTING — CELAH KONTROL: tabel ini HANYA direferensikan oleh 3 berkas (halaman ini & dua endpoint aksinya sendiri) — TIDAK ADA satu pun skrip penyimpanan penerimaan/koreksi fabric (termasuk menu Update BPB &gt; Fabric) yang memeriksa status penutupan ini sebelum menyimpan. Saat ini fitur ini murni pencatatan/register, belum benar-benar mengunci transaksi apa pun.',
                        'JANGAN disamakan dengan menu "Closing Periode" pada grup Accounting (tabel tbl_closing_periode, terpisah sepenuhnya, dipakai lebih luas sebagai referensi beberapa fitur Reverse & Edit Jurnal) maupun menu "Closing Info" pada grup AP (melaporkan status closed_by/closed_date milik List Payment) — tiga fitur berbeda yang kebetulan sama-sama memakai kata "closing".',
                    ],
                ],
            ],
        ],
    ],
    'exim' => [
        'title' => 'Exim',
        'icon'  => 'fa-cubes',
        'items' => [
            'exim-calculation-cost-report' => [
                'title' => 'Calculation Cost Report', 'icon' => 'fa-bars', 'path' => 'module/AP/exim-calculatin-cost-report.php',
                'doc' => [
                    'summary' => 'Laporan biaya landing (landing cost) impor/ekspor per unit barang — murni baca, mengalokasikan biaya tambahan (bea masuk, forwarder, asuransi, dst.) dari dokumen Memo ke tiap unit barang yang diinvoice.',
                    'purpose' => 'Menampilkan efektif biaya tambahan per unit garmen/barang untuk keperluan Exim, dipecah per kategori biaya (PPJK/Customs Brokerage, Storage, Forwarder/Shipping Line, Asuransi, Disperindag, PPN, Custom Bond, Iuran APKB, Kurir, PDRI), per buyer & season.',
                    'variants' => [],
                    'flow' => [],
                    'status_flow' => [],
                    'tables_write' => [],
                    'tables_read' => [
                        ['name' => 'memo_h', 'desc' => 'Header dokumen Memo biaya (jns_inv="INVOICE", status bukan "CANCEL").'],
                        ['name' => 'memo_det', 'desc' => 'Baris biaya per kategori pada Memo, dipetakan ke 10 kategori tetap.'],
                        ['name' => 'memo_inv', 'desc' => 'Penaut Memo ke nomor invoice.'],
                        ['name' => 'mastersupplier', 'desc' => 'Dibaca dua kali — sekali sebagai Supplier, sekali sebagai Buyer.'],
                        ['name' => 'tbl_invoice_detail', 'desc' => 'Qty & tanggal invoice, dasar perhitungan biaya per unit.'],
                        ['name' => 'tbl_book_invoice', 'desc' => 'Header invoice terkait.'],
                        ['name' => 'so', 'desc' => 'Sales Order terkait.'],
                        ['name' => 'masterseason', 'desc' => 'Deskripsi season.'],
                    ],
                    'notes' => [
                        'Data master kategori biaya sesungguhnya (master_memo_ctg/master_memo_subctg) TIDAK dipakai oleh laporan ini — 10 label kolom biaya tertanam tetap sebagai teks HTML, diasumsikan berurutan sesuai id kategori 1 s.d. 10. Apabila kategori master berubah/ditambah/diurutkan ulang, label kolom laporan ini berpotensi tidak lagi cocok tanpa terdeteksi otomatis.',
                        'Berdasarkan penelusuran menyeluruh, TIDAK ditemukan satu pun proses di modul AP ini yang membuat data memo_h/memo_det — data ini kemungkinan besar dibuat oleh bagian aplikasi lain di luar cakupan dokumentasi ini; laporan ini murni konsumen data yang sudah ada.',
                        'Tidak terhubung ke mekanisme Closing Periode manapun — filter murni berdasarkan tanggal memo.',
                        'Ditemukan sejumlah cacat kosmetik/teknis (tidak memengaruhi keakuratan angka): markup tabel HTML kurang rapi, ID tabel yang tidak cocok dengan skrip DataTable sehingga fitur pencarian/pengurutan tabel tidak aktif, beberapa penanda tombol sisa salin-tempel yang menunjuk elemen tidak ada di halaman, serta ekspor Excel yang memuat parameter URL kurang valid (pemisah ganda, bukan pemisah baku) dan judul unduhan berkas yang salah (warisan dari laporan lain).',
                    ],
                ],
            ],
        ],
    ],
    'reverse' => [
        'title' => 'Reverse',
        'icon'  => 'fa-retweet',
        'items' => [
            '_s1' => ['section' => 'BPB'],
            'maintain-bpb' => [
                'title' => 'BPB', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/maintain-bpb.php',
                'doc' => [
                    'summary' => 'Approval/cancel untuk pengajuan reversal BPB — PENTING: pengajuannya sendiri DIBUAT OLEH SISTEM LAIN, bukan modul AP ini (diduga kuat sistem gudang/produksi terpisah yang berbagi basis data yang sama).',
                    'purpose' => 'Menyetujui atau menolak permintaan pembatalan/reversal BPB yang sudah masuk ke antrian — Approve benar-benar membalik status BPB (Garment, Knitting, Celup) sekaligus data terkait di gudang, termasuk sebagian di basis data PostgreSQL milik sistem Knitting.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Pengajuan reversal BPB berstatus "POST" — TIDAK ADA halaman "buat pengajuan" untuk fitur ini di modul AP, baris di sini diasumsikan berasal dari sistem gudang/produksi eksternal yang berbagi basis data yang sama.'],
                        ['title' => 'Approve', 'desc' => 'Membalikkan bpb/bppb (confirm="N", dikosongkan), whs_inmaterial_fabric/whs_bppb_h (status="Pending"), data Postgres milik sistem Knitting (status_bpb="draft"), menghapus baris pelengkap BPB Knitting, dan menyisipkan jurnal pembalik ("Reverse ...") ke tbl_list_journal.'],
                        ['title' => 'Cancel', 'desc' => 'Hanya menolak pengajuan — TIDAK mengubah data BPB asli sama sekali.'],
                    ],
                    'status_flow' => [
                        ['label' => 'POST', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'maintain_bpb_h', 'actions' => ['update'], 'desc' => 'Status pengajuan berubah APPROVED/CANCEL.'],
                        ['name' => 'maintain_bpb_det', 'actions' => ['update'], 'desc' => 'Status baris berubah "N" saat Cancel.'],
                        ['name' => 'bpb', 'actions' => ['update'], 'desc' => 'Ditandai confirm="N" & status_maintain dikosongkan — hanya saat Approve.'],
                        ['name' => 'bppb', 'actions' => ['update'], 'desc' => 'Sama seperti bpb, untuk dokumen retur — hanya saat Approve.'],
                        ['name' => 'whs_inmaterial_fabric', 'actions' => ['update'], 'desc' => 'Status dikembalikan "Pending" — hanya saat Approve.'],
                        ['name' => 'whs_bppb_h', 'actions' => ['update'], 'desc' => 'Status dikembalikan "Pending" — hanya saat Approve.'],
                        ['name' => 'bpb_knitting', 'actions' => ['delete'], 'desc' => 'Baris pelengkap BPB Knitting dihapus — hanya saat Approve.'],
                        ['name' => 'tbl_tamb_bpb2', 'actions' => ['delete'], 'desc' => 'Baris tambahan terkait dihapus — hanya saat Approve.'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert'], 'desc' => 'Jurnal pembalik ("Reverse ...") disisipkan — hanya saat Approve.'],
                        ['name' => 'bpb [Knitting]', 'db' => 'PostgreSQL alabare_knitting @ 10.10.5.62 (conn4)', 'actions' => ['update'], 'desc' => 'Status dikembalikan "draft" di sisi sistem Knitting — hanya saat Approve.'],
                        ['name' => 'gp_penerimaan_greige_h', 'db' => 'PostgreSQL alabare_knitting @ 10.10.5.62 (conn4)', 'actions' => ['update'], 'desc' => 'Ikut dibalik statusnya — hanya saat Approve.'],
                        ['name' => 'bpb_celup', 'db' => 'PostgreSQL alabare_knitting @ 10.10.5.62 (conn4)', 'actions' => ['update'], 'desc' => 'Status dikembalikan "draft" — hanya saat Approve.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'PENTING: tidak ditemukan satu pun perintah INSERT ke maintain_bpb_h di seluruh kode aplikasi ini — pengajuan reversal BPB yang muncul di antrian halaman ini dibuat oleh sistem LAIN (kemungkinan besar aplikasi gudang/produksi terpisah) yang kebetulan berbagi basis data signalbit_erp yang sama.',
                        'Berkas backend approve mencampur dua cara pemanggilan driver database (mysqli_query dan mysql_query/shim) pada berkas yang sama — berisiko galat senyap apabila keduanya diasumsikan berbagi state koneksi yang identis.',
                        'Terdapat pemanggilan fungsi error database tanpa argumen koneksi di beberapa titik — berpotensi galat PHP alih-alih pesan error yang informatif.',
                    ],
                ],
            ],
            'formreversebpb' => [
                'title' => 'Verifikasi BPB', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/formreversebpb.php',
                'doc' => [
                    'summary' => 'Pembatalan BPB LANGSUNG & SEKALI KLIK, tanpa tahap pengajuan/approval sama sekali — berbeda mendasar dari 9 menu Reverse lainnya di kelompok ini yang semuanya bertahap.',
                    'purpose' => 'Membatalkan BPB yang belum pernah dijadikan Kontra Bon (is_invoiced = "Waiting") secara langsung, satu klik.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Pilih BPB', 'desc' => 'Menampilkan BPB (bpb_new) berstatus "Waiting" (belum ditagih lewat Kontra Bon apa pun).'],
                        ['title' => 'Reverse', 'desc' => 'Klik tombol langsung membatalkan BPB terpilih — TIDAK ADA tahap pengajuan maupun approval terpisah, berbeda dari seluruh menu Reverse lainnya.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Waiting', 'cls' => 'planned'],
                        ['label' => 'Cancel (langsung)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'bpb_new', 'actions' => ['update'], 'desc' => 'Status langsung berubah "Cancel" beserta audit reverse_user/reverse_date, seketika saat tombol diklik.'],
                        ['name' => 'bpb', 'actions' => ['update'], 'desc' => 'Kolom ap_inv dikosongkan kembali, seketika saat tombol diklik.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'PENTING: menu ini adalah SATU-SATUNYA di seluruh kelompok Reverse yang tidak melalui tahap pengajuan+approval — begitu tombol Reverse diklik, BPB langsung berstatus Cancel tanpa peninjauan pihak lain.',
                        'Bug ditemukan: variabel hasil query hanya diisi di dalam perulangan baris hasil — apabila pencarian tidak menghasilkan baris apa pun, pemeriksaan sesudahnya beroperasi pada variabel yang belum terdefinisi.',
                    ],
                ],
            ],
            '_s2' => ['section' => 'FTR'],
            'pengajuan-ftr-cbd' => [
                'title' => 'FTR CBD', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/pengajuan_ftrcbd.php',
                'doc' => [
                    'summary' => 'Pengajuan & approval pembatalan (reversal) FTR CBD — mengunci FTR saat diajukan, benar-benar membukanya kembali ke "draft" saat disetujui, bukan menghapusnya.',
                    'purpose' => 'Mengajukan permintaan pembatalan atas FTR CBD yang sudah Approved, ditinjau sebelum FTR tersebut benar-benar dikembalikan ke status bisa-diedit.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Memilih FTR CBD berstatus Approved &amp; belum terpakai Kontra Bon; begitu diajukan, FTR CBD sumber LANGSUNG dikunci ("Waiting") — sebelum ada keputusan apa pun.'],
                        ['title' => 'Approve', 'desc' => 'Pengajuan disetujui; FTR CBD sumber dikembalikan ke status "draft" (bisa diedit/dipakai ulang) — INI BUKAN penghapusan, murni membuka kunci.'],
                        ['title' => 'Cancel (tolak)', 'desc' => 'Pengajuan ditolak; FTR CBD sumber dikembalikan ke "Approved" seperti semula.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Waiting (diajukan)', 'cls' => 'planned'],
                        ['label' => 'Approved (disetujui → FTR jadi draft)', 'cls' => 'progress'],
                        ['label' => 'Cancel (ditolak)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pengajuan_ftrcbd', 'actions' => ['insert', 'update'], 'desc' => 'Baris diajukan (dari form terpisah), status diperbarui saat Approve/Cancel.'],
                        ['name' => 'ftr_cbd', 'actions' => ['update'], 'desc' => 'Dikunci "Waiting" saat diajukan; dibuka ke "draft" saat disetujui; dikembalikan ke "Approved" saat ditolak.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Menyetujui pengajuan di sini BUKAN berarti FTR dibatalkan permanen — efeknya justru mengembalikan FTR ke status "draft" sehingga bisa diedit/dipakai ulang untuk Kontra Bon berikutnya.',
                        'Tidak ada nomor pengajuan tersendiri yang dibuat — hanya id auto-increment pada tabel pengajuan, nomor FTR asli hanya disalin sebagai referensi.',
                    ],
                ],
            ],
            'pengajuan-ftr-dp' => [
                'title' => 'FTR DP', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/pengajuan_ftrdp.php',
                'doc' => [
                    'summary' => 'Kembaran "FTR CBD" untuk FTR DP — pola identik: mengunci saat diajukan, membuka ke draft saat disetujui.',
                    'purpose' => 'Mengajukan permintaan pembatalan atas FTR DP yang sudah Approved.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Memilih FTR DP berstatus Approved &amp; belum terpakai Kontra Bon; FTR DP sumber langsung dikunci "Waiting".'],
                        ['title' => 'Approve', 'desc' => 'FTR DP sumber dikembalikan ke "draft".'],
                        ['title' => 'Cancel (tolak)', 'desc' => 'FTR DP sumber dikembalikan ke "Approved".'],
                    ],
                    'status_flow' => [
                        ['label' => 'Waiting (diajukan)', 'cls' => 'planned'],
                        ['label' => 'Approved (disetujui → FTR jadi draft)', 'cls' => 'progress'],
                        ['label' => 'Cancel (ditolak)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pengajuan_ftrdp', 'actions' => ['insert', 'update'], 'desc' => 'Baris diajukan, status diperbarui saat Approve/Cancel.'],
                        ['name' => 'ftr_dp', 'actions' => ['update'], 'desc' => 'Dikunci "Waiting" saat diajukan; dibuka "draft" saat disetujui; dikembalikan "Approved" saat ditolak.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Bug penamaan: kolom nomor pada tabel pengajuan ini masih bernama "no_cbd" walau isinya nomor FTR DP — sisa salin-tempel dari fitur FTR CBD yang dibuat lebih dulu, tidak memengaruhi fungsi.',
                    ],
                ],
            ],
            '_s3' => ['section' => 'Kontra Bon'],
            'pengajuan-kb-reg' => [
                'title' => 'Kontrabon Reg', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/pengajuankb.php',
                'doc' => [
                    'summary' => 'Pengajuan & approval pembatalan (reversal) Kontra Bon Reguler — pola sama seperti FTR: mengunci saat diajukan, membuka kembali ke draft saat disetujui.',
                    'purpose' => 'Mengajukan permintaan pembatalan atas Kontra Bon Reguler yang sudah Approved.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Memilih Kontra Bon Reguler Approved; sumbernya langsung dikunci ("Waiting", status_int="3").'],
                        ['title' => 'Approve', 'desc' => 'Kontra Bon sumber dikembalikan ke "draft" (status_int="2") — bisa direvisi/diproses ulang.'],
                        ['title' => 'Cancel (tolak)', 'desc' => 'Kontra Bon sumber dikembalikan ke "Approved" (status_int="4").'],
                    ],
                    'status_flow' => [
                        ['label' => 'Waiting (diajukan)', 'cls' => 'planned'],
                        ['label' => 'Approved (disetujui → Kontrabon jadi draft)', 'cls' => 'progress'],
                        ['label' => 'Cancel (ditolak)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pengajuan_kb', 'actions' => ['insert', 'update'], 'desc' => 'Baris diajukan, status diperbarui saat Approve/Cancel.'],
                        ['name' => 'kontrabon', 'actions' => ['update'], 'desc' => 'Dikunci "Waiting"/status_int=3 saat diajukan; "draft"/status_int=2 saat disetujui; "Approved"/status_int=4 saat ditolak.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Bug ditemukan: berkas approve mengambil data pengajuan satu kali, namun kemudian mengulang pernyataan UPDATE yang identik untuk setiap baris BPB yang tertaut pada Kontra Bon yang sama — berlebihan (boros query) namun tidak salah secara hasil akhir karena kondisinya idempoten.',
                    ],
                ],
            ],
            'pengajuan-kb-cbd' => [
                'title' => 'Kontrabon CBD', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/pengajuankb_cbd.php',
                'doc' => [
                    'summary' => 'Kembaran "Kontrabon Reg" untuk Kontra Bon CBD.',
                    'purpose' => 'Mengajukan permintaan pembatalan atas Kontra Bon CBD yang sudah Approved & belum masuk List Payment.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Memilih Kontra Bon CBD Approved yang belum masuk List Payment; sumbernya langsung dikunci "Waiting"/status_int=3.'],
                        ['title' => 'Approve', 'desc' => 'Kontra Bon CBD sumber dikembalikan ke "draft"/status_int=2.'],
                        ['title' => 'Cancel (tolak)', 'desc' => 'Kontra Bon CBD sumber dikembalikan ke "Approved"/status_int=4.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Waiting (diajukan)', 'cls' => 'planned'],
                        ['label' => 'Approved (disetujui → jadi draft)', 'cls' => 'progress'],
                        ['label' => 'Cancel (ditolak)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pengajuan_kb_cbd', 'actions' => ['insert', 'update'], 'desc' => 'Baris diajukan, status diperbarui saat Approve/Cancel.'],
                        ['name' => 'kontrabon_cbd', 'actions' => ['update'], 'desc' => 'Dikunci saat diajukan, dibuka "draft" saat disetujui, dikembalikan "Approved" saat ditolak.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Bug penamaan: kolom audit pada kontrabon_cbd yang sebenarnya dipakai mencatat SIAPA MENYETUJUI pengajuan reversal ini malah bernama cancel_user/cancel_date — sisa salin-tempel yang membingungkan secara skema, walau tidak salah secara fungsi.',
                    ],
                ],
            ],
            'pengajuan-kb-dp' => [
                'title' => 'Kontrabon DP', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/pengajuankb_dp.php',
                'doc' => [
                    'summary' => 'Kembaran "Kontrabon CBD" untuk Kontra Bon DP.',
                    'purpose' => 'Mengajukan permintaan pembatalan atas Kontra Bon DP yang sudah Approved & belum masuk List Payment.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Memilih Kontra Bon DP Approved yang belum masuk List Payment; sumbernya langsung dikunci "Waiting"/status_int=3.'],
                        ['title' => 'Approve', 'desc' => 'Kontra Bon DP sumber dikembalikan ke "draft"/status_int=2.'],
                        ['title' => 'Cancel (tolak)', 'desc' => 'Kontra Bon DP sumber dikembalikan ke "Approved"/status_int=4.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Waiting (diajukan)', 'cls' => 'planned'],
                        ['label' => 'Approved (disetujui → jadi draft)', 'cls' => 'progress'],
                        ['label' => 'Cancel (ditolak)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pengajuan_kb_dp', 'actions' => ['insert', 'update'], 'desc' => 'Baris diajukan, status diperbarui saat Approve/Cancel.'],
                        ['name' => 'kontrabon_dp', 'actions' => ['update'], 'desc' => 'Dikunci saat diajukan, dibuka "draft" saat disetujui, dikembalikan "Approved" saat ditolak.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Bug paling jelas di kelompok BPB/FTR/Kontrabon Reverse ini: berkas approve pengajuan Kontra Bon DP justru mengarahkan kembali ke halaman Kontrabon Reg setelah selesai, bukan ke halaman Kontrabon DP miliknya sendiri — sisa salin-tempel murni (dampak terbatas karena arahan ini tidak benar-benar dieksekusi peramban dalam pemanggilan berbasis AJAX, namun tetap sebuah kekeliruan nyata di kode).',
                    ],
                ],
            ],
            '_s4' => ['section' => 'List Payment'],
            'pengajuan-payment-reg' => [
                'title' => 'List Payment Reg', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/pengajuanpayment.php',
                'doc' => [
                    'summary' => 'Pengajuan & approval pembatalan (reversal) List Payment Reguler — TERDAPAT BUG: saldo Kontra Bon yang seharusnya dipulihkan saat disetujui TIDAK PERNAH benar-benar dikembalikan.',
                    'purpose' => 'Mengajukan permintaan pembatalan atas List Payment Reguler yang sudah lunas (status_int="4").',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Memilih List Payment Reguler berstatus lunas; sumbernya langsung dikunci "Waiting"/status_int=3.'],
                        ['title' => 'Approve', 'desc' => 'List Payment sumber dikembalikan ke "draft"/status_int=2 — DIMAKSUDKAN juga memulihkan saldo outstanding pada Kontra Bon terkait, namun lihat catatan bug.'],
                        ['title' => 'Cancel (tolak)', 'desc' => 'List Payment sumber dikembalikan ke "Approved"/status_int=4.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Waiting (diajukan)', 'cls' => 'planned'],
                        ['label' => 'Approved (disetujui → jadi draft)', 'cls' => 'progress'],
                        ['label' => 'Cancel (ditolak)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pengajuan_payment', 'actions' => ['insert', 'update'], 'desc' => 'Baris diajukan, status diperbarui saat Approve/Cancel.'],
                        ['name' => 'list_payment', 'actions' => ['update'], 'desc' => 'Dikunci "Waiting"/status_int=3 saat diajukan; "draft"/status_int=2 saat disetujui; "Approved"/status_int=4 saat ditolak.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'BUG SERIUS: berkas approve menghitung nilai saldo kontrabon_h yang seharusnya dipulihkan, namun pernyataan UPDATE yang benar-benar menuliskannya DIKOMENTARI (tidak aktif) — sementara baris yang MENGEKSEKUSI query tersebut TETAP AKTIF dan merujuk ke variabel yang tidak pernah terisi. Akibatnya, saldo outstanding pada Kontra Bon TIDAK PERNAH benar-benar dipulihkan ketika reversal List Payment ini disetujui, walau secara tampilan status sudah berubah seolah-olah selesai.',
                    ],
                ],
            ],
            'pengajuan-payment-cbd' => [
                'title' => 'List Payment CBD', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/pengajuanpaymentcbd.php',
                'doc' => [
                    'summary' => 'Kembaran "List Payment Reg" untuk List Payment CBD — bug saldo yang sama persis juga berlaku di sini.',
                    'purpose' => 'Mengajukan permintaan pembatalan atas List Payment CBD yang sudah lunas.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Memilih List Payment CBD berstatus lunas; sumbernya langsung dikunci "Waiting"/status_int=3.'],
                        ['title' => 'Approve', 'desc' => 'List Payment CBD sumber dikembalikan ke "draft"/status_int=2.'],
                        ['title' => 'Cancel (tolak)', 'desc' => 'List Payment CBD sumber dikembalikan ke "Approved"/status_int=4.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Waiting (diajukan)', 'cls' => 'planned'],
                        ['label' => 'Approved (disetujui → jadi draft)', 'cls' => 'progress'],
                        ['label' => 'Cancel (ditolak)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pengajuan_paymentcbd', 'actions' => ['insert', 'update'], 'desc' => 'Baris diajukan, status diperbarui saat Approve/Cancel.'],
                        ['name' => 'list_payment_cbd', 'actions' => ['update'], 'desc' => 'Dikunci saat diajukan, dibuka "draft" saat disetujui, dikembalikan "Approved" saat ditolak.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'BUG SALDO yang sama persis dengan List Payment Reg: nilai kontrabon_h_cbd.balance yang seharusnya dipulihkan saat Approve TIDAK PERNAH benar-benar tertulis (pernyataan UPDATE-nya dikomentari, namun baris eksekusinya tetap aktif merujuk variabel kosong).',
                    ],
                ],
            ],
            'pengajuan-payment-dp' => [
                'title' => 'List Payment DP', 'icon' => 'fa-minus-square-o', 'path' => 'module/AP/pengajuanpaymentdp.php',
                'doc' => [
                    'summary' => 'Kembaran "List Payment CBD" untuk List Payment DP — bug saldo yang sama persis juga berlaku di sini.',
                    'purpose' => 'Mengajukan permintaan pembatalan atas List Payment DP yang sudah lunas.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Memilih List Payment DP berstatus lunas; sumbernya langsung dikunci "Waiting"/status_int=3.'],
                        ['title' => 'Approve', 'desc' => 'List Payment DP sumber dikembalikan ke "draft"/status_int=2.'],
                        ['title' => 'Cancel (tolak)', 'desc' => 'List Payment DP sumber dikembalikan ke "Approved"/status_int=4.'],
                    ],
                    'status_flow' => [
                        ['label' => 'Waiting (diajukan)', 'cls' => 'planned'],
                        ['label' => 'Approved (disetujui → jadi draft)', 'cls' => 'progress'],
                        ['label' => 'Cancel (ditolak)', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'pengajuan_paymentdp', 'actions' => ['insert', 'update'], 'desc' => 'Baris diajukan, status diperbarui saat Approve/Cancel.'],
                        ['name' => 'list_payment_dp', 'actions' => ['update'], 'desc' => 'Dikunci saat diajukan, dibuka "draft" saat disetujui, dikembalikan "Approved" saat ditolak.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'BUG SALDO yang sama persis dengan List Payment Reg/CBD: nilai kontrabon_h_dp.balance yang seharusnya dipulihkan saat Approve TIDAK PERNAH benar-benar tertulis.',
                    ],
                ],
            ],
            '_s5' => ['section' => 'Pengajuan Reverse'],
            'reverse-kontrabon' => [
                'title' => 'Kontrabon', 'icon' => 'fa-angle-right', 'path' => 'module/AP/reverse_kontrabon.php',
                'doc' => [
                    'summary' => 'Pengajuan reversal Kontra Bon — mengumpulkan pengajuan ke dalam SATU PASANG tabel bersama (ap_reverse_h/ap_reverse_det) yang dipakai bersama oleh seluruh 7 jenis dokumen di kelompok Reverse ini. TERDAPAT BUG SERIUS: pilihan tipe "KONTRABON CBD"/"KONTRABON DP" salah mengambil data dari tabel Payment, bukan Kontra Bon.',
                    'purpose' => 'Mengajukan permintaan reversal atas Kontra Bon yang sudah Approved, sebelum benar-benar dibalik lewat menu "Approve Reverse Kontrabon" terpisah.',
                    'variants' => [
                        ['name' => 'Kontrabon Reguler', 'icon' => 'fa-file-text-o', 'desc' => 'Kandidat diambil dari kontrabon_h — benar sesuai tipenya.'],
                        ['name' => 'Kontrabon CBD / DP', 'icon' => 'fa-exclamation-triangle', 'desc' => 'BUG: keduanya sama-sama mengambil kandidat dari payment_ftrcbd (tabel Payment, bukan Kontra Bon CBD/DP) — menampilkan daftar yang identik & keliru.'],
                    ],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Baris ap_reverse_h/ap_reverse_det bertipe Kontrabon (nomor berawalan RVS/SI/...).'],
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Memilih tipe (Reguler/CBD/DP) lalu dokumen sumbernya; dokumen sumber TIDAK dikunci/ditandai sama sekali pada tahap ini — hanya disaring dari daftar pilihan agar tidak diajukan dua kali (murni penyaringan tampilan, bukan penguncian database).'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Lewat menu "Approve Reverse Kontrabon" terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['insert'], 'desc' => 'Header pengajuan reversal, berstatus "DRAFT" — tabel bersama yang dipakai seluruh 7 jenis dokumen Reverse, dibedakan lewat kolom type_doc & prefiks nomor.'],
                        ['name' => 'ap_reverse_det', 'actions' => ['insert'], 'desc' => 'Detail dokumen yang diajukan reversal.'],
                    ],
                    'tables_read' => [
                        ['name' => 'kontrabon_h', 'desc' => 'Kandidat tipe Reguler.'],
                        ['name' => 'payment_ftrcbd', 'desc' => 'Kandidat tipe CBD maupun DP (BUG — seharusnya kontrabon_h_cbd/kontrabon_h_dp).'],
                    ],
                    'notes' => [
                        'Nomor: RVS/SI/MMYY/00001 — nomor urut dihitung per TAHUN (bukan per bulan) walau formatnya menyertakan segmen bulan, sehingga nomor urut tidak pernah kembali ke 1 tiap ganti bulan, hanya tiap ganti tahun.',
                        'Menu ini & enam menu "Pengajuan Reverse" lainnya (Payment/Bank/Petty Cash/Payment Voucher/Payment Voucher List/Payment List) semuanya menulis ke tabel bersama yang sama (ap_reverse_h/ap_reverse_det) — bukan tabel terpisah per jenis dokumen.',
                        'Hak akses menu (menurole) hanya mengatur tampil/tidaknya tautan di sidebar — berkas backend pengajuan maupun approval TIDAK memeriksa ulang hak akses di sisi server, berlaku di seluruh 14 menu kelompok Pengajuan+Approval Reverse ini.',
                    ],
                ],
            ],
            'reverse-payment' => [
                'title' => 'Payment', 'icon' => 'fa-angle-right', 'path' => 'module/AP/reverse_payment.php',
                'doc' => [
                    'summary' => 'Kembaran "Kontrabon" untuk pengajuan reversal Payment — memiliki bug tipe CBD/DP yang salah sasaran tabel serupa, meski menu approval-nya (terpisah) justru sudah benar menyasar tabel yang tepat.',
                    'purpose' => 'Mengajukan permintaan reversal atas Payment yang sudah Approved.',
                    'variants' => [
                        ['name' => 'Payment Reguler', 'icon' => 'fa-file-text-o', 'desc' => 'Kandidat diambil dari payment_ftr — benar sesuai tipenya.'],
                        ['name' => 'Payment CBD / DP', 'icon' => 'fa-exclamation-triangle', 'desc' => 'BUG: keduanya sama-sama mengambil kandidat dari payment_ftrcbd — payment_ftrdp yang seharusnya dipakai untuk pilihan DP tidak pernah disentuh form pengajuan ini.'],
                    ],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Baris ap_reverse_h/ap_reverse_det bertipe Payment (nomor berawalan RVS/PAY/...).'],
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Dokumen sumber tidak dikunci saat diajukan, sama seperti menu Kontrabon.'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Lewat menu "Approve Reverse Payment" terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['insert'], 'desc' => 'Header pengajuan, berstatus "DRAFT".'],
                        ['name' => 'ap_reverse_det', 'actions' => ['insert'], 'desc' => 'Detail dokumen Payment yang diajukan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'payment_ftr', 'desc' => 'Kandidat tipe Reguler.'],
                        ['name' => 'payment_ftrcbd', 'desc' => 'Kandidat tipe CBD maupun DP (BUG — seharusnya payment_ftrdp untuk pilihan DP).'],
                    ],
                    'notes' => [
                        'Nomor: RVS/PAY/MMYY/00001, dihitung per tahun (pola sama seperti Kontrabon).',
                        'Ketidaksesuaian menarik: menu APPROVAL untuk Payment (terpisah) justru SUDAH benar menyasar payment_ftrdp — sehingga ada dokumen DP yang secara teknis bisa diproses approval-nya namun tidak pernah bisa benar-benar diajukan lewat form ini karena bug pemilihan kandidat di atas.',
                    ],
                ],
            ],
            'reverse-bank' => [
                'title' => 'Bank', 'icon' => 'fa-angle-right', 'path' => 'module/AP/reverse_bank.php',
                'doc' => [
                    'summary' => 'Pengajuan reversal Bank In/Out — BERBEDA dari Kontrabon/Payment: pemilihan tipe In dan Out benar-benar diambil dari tabel yang tepat, tidak ditemukan bug tertukar tabel di sini.',
                    'purpose' => 'Mengajukan permintaan reversal atas transaksi Bank In atau Bank Out yang sudah Approved.',
                    'variants' => [
                        ['name' => 'Bank In', 'icon' => 'fa-arrow-down', 'desc' => 'Kandidat dari tbl_bankin_arcollection.'],
                        ['name' => 'Bank Out', 'icon' => 'fa-arrow-up', 'desc' => 'Kandidat dari b_bankout_h.'],
                    ],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Baris ap_reverse_h/ap_reverse_det bertipe Bank (nomor berawalan RVS/BN/...).'],
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Dokumen sumber tidak dikunci saat diajukan.'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Lewat menu "Approve Reverse Bank" terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['insert'], 'desc' => 'Header pengajuan, berstatus "DRAFT".'],
                        ['name' => 'ap_reverse_det', 'actions' => ['insert'], 'desc' => 'Detail dokumen Bank yang diajukan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'tbl_bankin_arcollection', 'desc' => 'Kandidat tipe Bank In.'],
                        ['name' => 'b_bankout_h', 'desc' => 'Kandidat tipe Bank Out.'],
                    ],
                    'notes' => [
                        'Nomor: RVS/BN/MMYY/00001, dihitung per tahun.',
                    ],
                ],
            ],
            'reverse-petty-cash' => [
                'title' => 'Petty Cash', 'icon' => 'fa-angle-right', 'path' => 'module/AP/reverse_petty_cash.php',
                'doc' => [
                    'summary' => 'Pengajuan reversal Petty Cash In/Out — sama seperti Bank, tipe In/Out diambil dari tabel yang tepat, tidak ada bug tertukar tabel.',
                    'purpose' => 'Mengajukan permintaan reversal atas transaksi Petty Cash In atau Petty Cash Out yang sudah Approved.',
                    'variants' => [
                        ['name' => 'Petty Cash In', 'icon' => 'fa-arrow-down', 'desc' => 'Kandidat dari c_petty_cashin_h.'],
                        ['name' => 'Petty Cash Out', 'icon' => 'fa-arrow-up', 'desc' => 'Kandidat dari c_petty_cashout_h.'],
                    ],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Baris ap_reverse_h/ap_reverse_det bertipe Petty Cash (nomor berawalan RVS/PC/...).'],
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Dokumen sumber tidak dikunci saat diajukan.'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Lewat menu "Approve Reverse Petty Cash" terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['insert'], 'desc' => 'Header pengajuan, berstatus "DRAFT".'],
                        ['name' => 'ap_reverse_det', 'actions' => ['insert'], 'desc' => 'Detail dokumen Petty Cash yang diajukan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'c_petty_cashin_h', 'desc' => 'Kandidat tipe Petty Cash In.'],
                        ['name' => 'c_petty_cashout_h', 'desc' => 'Kandidat tipe Petty Cash Out.'],
                    ],
                    'notes' => [
                        'Nomor: RVS/PC/MMYY/00001, dihitung per tahun.',
                    ],
                ],
            ],
            'reverse-pv' => [
                'title' => 'Payment Voucher', 'icon' => 'fa-angle-right', 'path' => 'module/AP/reverse_payment_voucher.php',
                'doc' => [
                    'summary' => 'Pengajuan reversal Payment Voucher — mencakup 4 dari 5 tipe (Reguler, Installment, DP, CBD; Biaya turut tercakup, Saldo Awal tidak), memakai tabel sumber yang BENAR sesuai tipenya masing-masing (tidak ada bug tertukar tabel seperti Kontrabon/Payment), plus peringatan visual apabila dokumen sedang terikat batch List Payment/Payment Voucher List.',
                    'purpose' => 'Mengajukan permintaan reversal atas Payment Voucher (Kontrabon) yang sudah lolos approval tahap akhirnya.',
                    'variants' => [
                        ['name' => 'Reguler', 'icon' => 'fa-file-text-o', 'desc' => 'Kandidat dari kontrabon/kontrabon_h, syarat status "SECOND APPROVED".'],
                        ['name' => 'Installment', 'icon' => 'fa-calendar', 'desc' => 'Kandidat dari kontrabon_h_installment_detail.'],
                        ['name' => 'DP', 'icon' => 'fa-arrow-down', 'desc' => 'Kandidat dari kontrabon_h_dp.'],
                        ['name' => 'CBD', 'icon' => 'fa-money', 'desc' => 'Kandidat dari kontrabon_h_cbd.'],
                        ['name' => 'Biaya', 'icon' => 'fa-credit-card', 'desc' => 'Kandidat dari tbl_pv_h/tbl_pv — PV Bank non-Kontrabon.'],
                    ],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Baris ap_reverse_h/ap_reverse_det bertipe Payment Voucher (nomor berawalan RVS/PV/..., tidak termasuk RVS/PVL/...).'],
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Baris kandidat ditampilkan DINONAKTIFKAN (tidak bisa dipilih) apabila dokumen sudah masuk Payment List aktif, dan diberi PERINGATAN (tetap bisa dipilih) apabila dokumen masuk Payment Voucher List aktif — pratinjau langsung dari efek berantai yang benar-benar terjadi saat disetujui.'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Lewat menu "Approve Reverse Payment Voucher" terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['insert'], 'desc' => 'Header pengajuan, type_doc="PAYMENT VOUCHER", berstatus "DRAFT".'],
                        ['name' => 'ap_reverse_det', 'actions' => ['insert'], 'desc' => 'Detail dokumen PV yang diajukan, termasuk kolom type_pv yang hanya diisi lewat jalur pengajuan Payment Voucher ini.'],
                    ],
                    'tables_read' => [
                        ['name' => 'kontrabon_h', 'desc' => 'Kandidat tipe Reguler.'],
                        ['name' => 'kontrabon_h_installment_detail', 'desc' => 'Kandidat tipe Installment.'],
                        ['name' => 'kontrabon_h_dp', 'desc' => 'Kandidat tipe DP.'],
                        ['name' => 'kontrabon_h_cbd', 'desc' => 'Kandidat tipe CBD.'],
                        ['name' => 'tbl_pv_h', 'desc' => 'Kandidat tipe Biaya.'],
                    ],
                    'notes' => [
                        'Nomor: RVS/PV/MMYY/00001 — BERBEDA dari 4 menu Pengajuan Reverse sebelumnya (Kontrabon/Payment/Bank/Petty Cash), nomor urut di sini dihitung per BULAN (benar-benar reset tiap bulan), bukan per tahun — generasi kode yang lebih baru.',
                        'Tipe "Saldo Awal" (PV pembukaan saldo) TIDAK tercakup di form pengajuan ini meski fungsi pendukungnya tersedia di kode — sehingga PV Saldo Awal tidak bisa diajukan reversal lewat menu ini.',
                        'Menu ini adalah satu-satunya di antara 7 menu Pengajuan Reverse yang memberi peringatan visual efek berantai sebelum pengajuan dibuat — lihat menu "Approve Reverse Payment Voucher" untuk efek berantai yang sesungguhnya terjadi saat disetujui.',
                    ],
                ],
            ],
            'reverse-pv-list' => [
                'title' => 'Payment Voucher List', 'icon' => 'fa-angle-right', 'path' => 'module/AP/reverse_payment_voucher_list.php',
                'doc' => [
                    'summary' => 'Pengajuan reversal atas seluruh dokumen Payment Voucher List (satu batch penuh, bukan PV individual).',
                    'purpose' => 'Mengajukan permintaan reversal atas batch Payment Voucher List yang sudah berstatus APPROVED.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Baris ap_reverse_h/ap_reverse_det bertipe Payment Voucher List (nomor berawalan RVS/PVL/...).'],
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Baris kandidat batch ditandai peringatan apabila salah satu PV di dalamnya sudah dibayar (lewat Bank Out/Petty Cash Out non-bank) atau sudah masuk Payment List aktif.'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Lewat menu "Approve Reverse Payment Voucher List" terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['insert'], 'desc' => 'Header pengajuan, type_doc="PAYMENT VOUCHER LIST", berstatus "DRAFT".'],
                        ['name' => 'ap_reverse_det', 'actions' => ['insert'], 'desc' => 'Detail batch PVL yang diajukan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'pv_payment_voucher_list_h', 'desc' => 'Kandidat batch berstatus APPROVED.'],
                    ],
                    'notes' => [
                        'Nomor: RVS/PVL/MMYY/00001, dihitung per bulan (generasi kode baru, sama seperti Payment Voucher).',
                    ],
                ],
            ],
            'reverse-payment-list' => [
                'title' => 'Payment List', 'icon' => 'fa-angle-right', 'path' => 'module/AP/reverse_payment_list.php',
                'doc' => [
                    'summary' => 'Pengajuan reversal atas seluruh dokumen Payment List (satu batch penuh), mensyaratkan status First/Second Approved.',
                    'purpose' => 'Mengajukan permintaan reversal atas batch Payment List yang sudah lolos salah satu tahap approval-nya.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan daftar', 'desc' => 'Baris ap_reverse_h/ap_reverse_det bertipe Payment List (nomor berawalan RVS/PL/...).'],
                        ['title' => 'Ajukan (form terpisah)', 'desc' => 'Baris kandidat batch ditandai peringatan apabila salah satu PV di dalamnya sudah dibayar lewat Bank Out/Petty Cash Out.'],
                        ['title' => 'Approve/Cancel', 'desc' => 'Lewat menu "Approve Reverse Payment List" terpisah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['insert'], 'desc' => 'Header pengajuan, type_doc="PAYMENT LIST", berstatus "DRAFT".'],
                        ['name' => 'ap_reverse_det', 'actions' => ['insert'], 'desc' => 'Detail batch Payment List yang diajukan.'],
                    ],
                    'tables_read' => [
                        ['name' => 'pv_payment_list_h', 'desc' => 'Kandidat batch berstatus First/Second Approved.'],
                    ],
                    'notes' => [
                        'Nomor: RVS/PL/MMYY/00001, dihitung per bulan.',
                    ],
                ],
            ],
            '_s6' => ['section' => 'Approval Reverse'],
            'approve-reverse-kontrabon' => [
                'title' => 'Kontrabon', 'icon' => 'fa-angle-right', 'path' => 'module/AP/form_approve_reverse_kontrabon.php',
                'doc' => [
                    'summary' => 'Approval reversal Kontra Bon — TANPA PENJAGAAN STATUS: bisa diproses berulang kali tanpa pengecekan, berisiko duplikasi jurnal pembalik apabila diklik dua kali.',
                    'purpose' => 'Menyetujui/menolak pengajuan reversal Kontra Bon — Approve benar-benar mengembalikan Kontra Bon ke status draft dan menyisipkan jurnal pembalik.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Pengajuan reversal Kontrabon.'],
                        ['title' => 'Approve', 'desc' => 'Status pengajuan menjadi "APPROVED" (TANPA memeriksa status sebelumnya terlebih dahulu); Kontra Bon sumber dicoba dikembalikan ke "draft" di ketiga tabel Reguler/CBD/DP sekaligus (hanya salah satu yang benar-benar cocok, tergantung tipe aslinya); jurnal pembalik disisipkan ke tbl_list_journal (debit/kredit ditukar, label "Reverse ...").'],
                        ['title' => 'Cancel', 'desc' => 'Status pengajuan menjadi "CANCEL" lewat endpoint bersama — lihat catatan keamanan.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['update'], 'desc' => 'Status berubah APPROVED/CANCEL — TANPA pemeriksaan status sebelumnya.'],
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Dicoba dikembalikan ke "draft" — hanya cocok apabila dokumennya benar Reguler.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Dicoba dikembalikan ke "draft" — karena bug pada form pengajuan, praktis tidak pernah benar-benar cocok.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Dicoba dikembalikan ke "draft" — karena bug pada form pengajuan, praktis tidak pernah benar-benar cocok.'],
                        ['name' => 'kontrabon', 'actions' => ['update'], 'desc' => 'status="draft", status_int="2".'],
                        ['name' => 'kontrabon_cbd', 'actions' => ['update'], 'desc' => 'status="draft", status_int="2" (jarang benar-benar cocok, lihat catatan bug).'],
                        ['name' => 'kontrabon_dp', 'actions' => ['update'], 'desc' => 'status="draft", status_int="2" (jarang benar-benar cocok, lihat catatan bug).'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert'], 'desc' => 'Jurnal pembalik disisipkan (debit/kredit ditukar, label "Reverse ...").'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'BUG: karena pengajuan tipe CBD/DP salah sasaran tabel (lihat menu "Kontrabon" pada Pengajuan Reverse), cabang CBD/DP di sini secara praktis TIDAK PERNAH benar-benar cocok dengan baris manapun di kontrabon_h_cbd/kontrabon_h_dp.',
                        'BUG: tanggal jurnal pembalik hanya diambil dari kontrabon_h.confirm_date — untuk kasus CBD/DP (seandainya cocok), tanggal ini kosong sehingga jurnal pembalik tersimpan dengan tanggal jurnal tidak valid.',
                        'BUG KEANDALAN: TIDAK ADA pemeriksaan status "DRAFT" sebelum memproses Approve — mengklik tombol berulang kali (klik ganda, retry jaringan, dua approver bersamaan) dapat menyisipkan jurnal pembalik BERULANG KALI untuk pengajuan yang sama.',
                        'Aksi Cancel memakai endpoint bersama yang menerima input tanpa penyaringan SQL yang memadai (celah injeksi) dan tanpa memeriksa status pengajuan sebelumnya (bisa "membatalkan" pengajuan yang sudah APPROVED) — berlaku sama untuk 4 menu approval "generasi lama" (Kontrabon/Payment/Bank/Petty Cash).',
                        'Kartu Hutang TIDAK disentuh oleh approval ini.',
                    ],
                ],
            ],
            'approve-reverse-payment' => [
                'title' => 'Payment', 'icon' => 'fa-angle-right', 'path' => 'module/AP/form_approve_reverse_payment.php',
                'doc' => [
                    'summary' => 'Kembaran "Approve Reverse Kontrabon" untuk Payment — pola bug identik (tanpa penjagaan status), namun tanpa bug tanggal jurnal.',
                    'purpose' => 'Menyetujui/menolak pengajuan reversal Payment.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Pengajuan reversal Payment.'],
                        ['title' => 'Approve', 'desc' => 'Status pengajuan menjadi "APPROVED" (TANPA memeriksa status sebelumnya); Payment sumber dicoba dikembalikan ke "draft" di ketiga tabel Reguler/CBD/DP sekaligus — berbeda dari Kontrabon, di sini ketiganya benar menyasar tabel yang tepat; jurnal pembalik disisipkan dengan tanggal approval saat ini.'],
                        ['title' => 'Cancel', 'desc' => 'Status pengajuan menjadi "CANCEL" lewat endpoint bersama yang sama dengan menu lain.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['update'], 'desc' => 'Status berubah APPROVED/CANCEL — TANPA pemeriksaan status sebelumnya.'],
                        ['name' => 'payment_ftr', 'actions' => ['update'], 'desc' => 'status="draft".'],
                        ['name' => 'payment_ftrdp', 'actions' => ['update'], 'desc' => 'status="draft" — benar menyasar tabel yang tepat (tidak seperti pengajuannya).'],
                        ['name' => 'payment_ftrcbd', 'actions' => ['update'], 'desc' => 'status="draft".'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert'], 'desc' => 'Jurnal pembalik disisipkan, tanggal = tanggal approval.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Sama seperti Kontrabon: TIDAK ADA pemeriksaan status "DRAFT" sebelum Approve — berisiko duplikasi jurnal pembalik bila diproses berulang.',
                        'Berbeda dari Kontrabon: cabang UPDATE di sini benar menyasar ketiga tabel Payment yang tepat (termasuk payment_ftrdp) — hanya form PENGAJUANnya (menu "Payment" pada Pengajuan Reverse) yang punya bug salah sasaran tabel untuk tipe CBD/DP.',
                        'Kartu Hutang tidak disentuh.',
                    ],
                ],
            ],
            'approve-reverse-bank' => [
                'title' => 'Bank', 'icon' => 'fa-angle-right', 'path' => 'module/AP/form_approve_reverse_bank.php',
                'doc' => [
                    'summary' => 'Kembaran approval untuk Bank In/Out — TERDAPAT BUG NYATA: langkah "tandai jurnal lama sebagai sudah digantikan" gagal senyap karena salah nama variabel, sehingga jurnal lama TIDAK PERNAH benar-benar ditandai walau jurnal pembalik baru tetap tersisip.',
                    'purpose' => 'Menyetujui/menolak pengajuan reversal Bank In/Out.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Pengajuan reversal Bank.'],
                        ['title' => 'Approve', 'desc' => 'Status pengajuan menjadi "APPROVED" (TANPA memeriksa status sebelumnya); Bank In/Out sumber dicoba dikembalikan ke "Draft"; jurnal pembalik disisipkan LANGSUNG berstatus "Updated" (berbeda dari 3 menu approval generasi lama lainnya yang mewarisi status baris asli); DIMAKSUDKAN juga menandai jurnal lama sebagai sudah digantikan, namun lihat catatan bug.'],
                        ['title' => 'Cancel', 'desc' => 'Status pengajuan menjadi "CANCEL" lewat endpoint bersama.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['update'], 'desc' => 'Status berubah APPROVED/CANCEL — TANPA pemeriksaan status sebelumnya.'],
                        ['name' => 'b_bankout_h', 'actions' => ['update'], 'desc' => 'status="Draft".'],
                        ['name' => 'tbl_bankin_arcollection', 'actions' => ['update'], 'desc' => 'status="Draft".'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert', 'update'], 'desc' => 'Jurnal pembalik disisipkan (langsung berstatus "Updated"); DIMAKSUDKAN juga menandai jurnal lama sebagai sudah digantikan, namun pernyataan UPDATE ini gagal senyap — lihat catatan bug.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'BUG NYATA: pernyataan UPDATE yang dimaksudkan menandai jurnal lama sebagai sudah digantikan memakai nama variabel yang SALAH (tidak terdefinisi) sebagai syarat pencariannya — akibatnya pernyataan ini selalu tidak menemukan baris apa pun dan jurnal lama TIDAK PERNAH benar-benar ditandai, walau jurnal pembalik yang baru tetap berhasil tersisip. Ini satu-satunya menu Reverse yang mencoba langkah "tandai jurnal lama" — dan justru langkah tambahan inilah yang cacat.',
                        'Sama seperti Kontrabon/Payment: TIDAK ADA pemeriksaan status "DRAFT" sebelum Approve.',
                        'Kartu Hutang tidak disentuh.',
                    ],
                ],
            ],
            'approve-reverse-petty-cash' => [
                'title' => 'Petty Cash', 'icon' => 'fa-angle-right', 'path' => 'module/AP/form_approve_reverse_petty_cash.php',
                'doc' => [
                    'summary' => 'Kembaran approval untuk Petty Cash In/Out — pola bug sama (tanpa penjagaan status), namun tanpa bug tanggal maupun bug "tandai jurnal lama" yang ditemukan pada menu Bank.',
                    'purpose' => 'Menyetujui/menolak pengajuan reversal Petty Cash In/Out.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Pengajuan reversal Petty Cash.'],
                        ['title' => 'Approve', 'desc' => 'Status pengajuan menjadi "APPROVED" (TANPA memeriksa status sebelumnya); Petty Cash In/Out sumber dicoba dikembalikan ke "Draft"; jurnal pembalik disisipkan dengan tanggal = tanggal approval.'],
                        ['title' => 'Cancel', 'desc' => 'Status pengajuan menjadi "CANCEL" lewat endpoint bersama.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                        ['label' => 'CANCEL', 'cls' => 'hold'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['update'], 'desc' => 'Status berubah APPROVED/CANCEL — TANPA pemeriksaan status sebelumnya.'],
                        ['name' => 'c_petty_cashout_h', 'actions' => ['update'], 'desc' => 'status="Draft".'],
                        ['name' => 'c_petty_cashin_h', 'actions' => ['update'], 'desc' => 'status="Draft".'],
                        ['name' => 'tbl_list_journal', 'actions' => ['insert'], 'desc' => 'Jurnal pembalik disisipkan, tanggal = tanggal approval.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'Sama seperti Kontrabon/Payment/Bank: TIDAK ADA pemeriksaan status "DRAFT" sebelum Approve.',
                        'Tidak memiliki bug "tandai jurnal lama" seperti Bank — hanya menyisipkan jurnal pembalik.',
                        'Kartu Hutang tidak disentuh.',
                    ],
                ],
            ],
            'approve-reverse-payment-list' => [
                'title' => 'Payment List', 'icon' => 'fa-angle-right', 'path' => 'module/AP/form_approve_reverse_payment_list.php',
                'doc' => [
                    'summary' => 'Approval reversal Payment List — GENERASI KODE LEBIH BARU: SUDAH memiliki pemeriksaan status "DRAFT" sebelum memproses, dan efeknya murni tingkat alur kerja (tidak memposting jurnal apa pun).',
                    'purpose' => 'Menyetujui reversal atas satu batch Payment List — mereset status & jejak approval batch tersebut ke Draft, sekaligus melepas seluruh PV di dalamnya dari batch ini (TANPA mengubah status approval PV itu sendiri).',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Pengajuan reversal berstatus DRAFT.'],
                        ['title' => 'Approve', 'desc' => 'Status pengajuan diperiksa harus "DRAFT" terlebih dahulu; Payment List sumber direset penuh ke "Draft" (jejak approval tahap pertama &amp; kedua dikosongkan); setiap PV di dalamnya dilepas dari batch ini (penanda batch dikosongkan pada tabel sumber masing-masing), namun status approval PV itu sendiri TIDAK diubah.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['update'], 'desc' => 'Status berubah APPROVED — dengan pemeriksaan status DRAFT terlebih dahulu (generasi kode lebih matang).'],
                        ['name' => 'pv_payment_list_h', 'actions' => ['update'], 'desc' => 'status="Draft", jejak approval tahap pertama & kedua dikosongkan.'],
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Penanda batch Payment List dikosongkan, untuk dokumen tipe Reguler dalam batch.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe DP.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe CBD.'],
                        ['name' => 'kontrabon_h_installment_detail', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe Installment.'],
                        ['name' => 'tbl_pv_h', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe Biaya.'],
                        ['name' => 'ap_saldo_payment_voucher', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe Saldo Awal.'],
                    ],
                    'tables_read' => [
                        ['name' => 'pv_payment_list_det', 'desc' => 'Daftar dokumen anggota batch yang perlu dilepas.'],
                    ],
                    'notes' => [
                        'Berbeda dari 4 menu approval reversal sebelumnya (Kontrabon/Payment/Bank/Petty Cash): menu ini SUDAH memeriksa status pengajuan harus "DRAFT" sebelum memproses — generasi kode yang lebih matang/lebih aman.',
                        'TIDAK memposting jurnal apa pun — konsisten dengan sifat Payment List sendiri yang murni lapisan penjadwalan/pengelompokan, bukan dokumen akuntansi.',
                        'TIDAK mengubah status approval dokumen PV yang dilepas dari batch — hanya melepas keterikatannya ke batch Payment List ini.',
                        'Kartu Hutang tidak disentuh.',
                    ],
                ],
            ],
            'approve-reverse-pv-list' => [
                'title' => 'Payment Voucher List', 'icon' => 'fa-angle-right', 'path' => 'module/AP/form_approve_reverse_payment_voucher_list.php',
                'doc' => [
                    'summary' => 'Approval reversal Payment Voucher List — pola sama dengan Approve Reverse Payment List: sudah ada penjagaan status, murni tingkat alur kerja, tidak memposting jurnal.',
                    'purpose' => 'Menyetujui reversal atas satu batch Payment Voucher List.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Pengajuan reversal berstatus DRAFT.'],
                        ['title' => 'Approve', 'desc' => 'Status pengajuan diperiksa harus "DRAFT"; Payment Voucher List sumber direset ke "Draft" (jejak approval satu-tahap dikosongkan, sesuai sifat approval PVL yang satu tahap saja); setiap PV di dalamnya dilepas dari batch ini (penanda dikosongkan); baris detail batch TIDAK ditandai batal.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['update'], 'desc' => 'Status berubah APPROVED — dengan pemeriksaan status DRAFT terlebih dahulu.'],
                        ['name' => 'pv_payment_voucher_list_h', 'actions' => ['update'], 'desc' => 'status="Draft", jejak approval (satu tahap) dikosongkan.'],
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Penanda batch PVL dikosongkan, tipe Reguler.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe DP.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe CBD.'],
                        ['name' => 'kontrabon_h_installment_detail', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe Installment.'],
                        ['name' => 'tbl_pv_h', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe Biaya.'],
                        ['name' => 'ap_saldo_payment_voucher', 'actions' => ['update'], 'desc' => 'Penanda batch dikosongkan, tipe Saldo Awal.'],
                    ],
                    'tables_read' => [
                        ['name' => 'pv_payment_voucher_list_det', 'desc' => 'Daftar dokumen anggota batch yang perlu dilepas.'],
                    ],
                    'notes' => [
                        'Sama seperti Approve Reverse Payment List: sudah memakai penjagaan status DRAFT, tidak memposting jurnal, Kartu Hutang tidak disentuh.',
                        'Baris detail batch (pv_payment_voucher_list_det) TIDAK ditandai batal saat reversal disetujui — hanya header yang direset, detail dibiarkan apa adanya.',
                    ],
                ],
            ],
            'approve-reverse-pv' => [
                'title' => 'Payment Voucher', 'icon' => 'fa-angle-right', 'path' => 'module/AP/form_approve_reverse_payment_voucher.php',
                'doc' => [
                    'summary' => 'Approval reversal Payment Voucher — PALING KOMPLEKS di seluruh kelompok Reverse: bisa memicu EFEK BERANTAI membatalkan seluruh Payment Voucher List yang memuat PV tersebut, sekaligus melepas PV-PV LAIN di batch yang sama (tanpa mereversal PV lain tersebut).',
                    'purpose' => 'Menyetujui reversal atas satu dokumen Payment Voucher (Reguler/Installment/DP/CBD/Biaya) — mereset penuh status & jejak approvalnya, dengan efek berantai ke Payment Voucher List apabila PV ini masih terikat batch.',
                    'variants' => [
                        ['name' => 'Reguler/Installment/DP/CBD', 'icon' => 'fa-file-text-o', 'desc' => 'Header maupun detail direset penuh ke "draft" beserta seluruh jejak kolom approval-nya dikosongkan.'],
                        ['name' => 'Biaya', 'icon' => 'fa-credit-card', 'desc' => 'Hanya status yang direset — jejak approvalnya lebih dangkal karena skema tipe ini memang tidak melacak kolom approval selengkap 4 tipe lainnya.'],
                    ],
                    'flow' => [
                        ['title' => 'Tampilkan antrian', 'desc' => 'Pengajuan reversal berstatus DRAFT.'],
                        ['title' => 'Approve', 'desc' => 'Status pengajuan diperiksa harus "DRAFT" terlebih dahulu. APABILA PV ini masih terikat Payment Voucher List aktif: SELURUH PVL tersebut dibatalkan (status "Cancel"), dan setiap PV LAIN yang turut berada di PVL yang sama dilepas dari batch itu (tanpa direversal, hanya dilepas keterikatannya). PV yang diajukan reversal ini sendiri direset penuh ke "draft" sesuai tipenya.'],
                    ],
                    'status_flow' => [
                        ['label' => 'DRAFT', 'cls' => 'planned'],
                        ['label' => 'APPROVED', 'cls' => 'progress'],
                    ],
                    'tables_write' => [
                        ['name' => 'ap_reverse_h', 'actions' => ['update'], 'desc' => 'Status berubah APPROVED — dengan pemeriksaan status DRAFT terlebih dahulu.'],
                        ['name' => 'pv_payment_voucher_list_h', 'actions' => ['update'], 'desc' => 'status="Cancel" — HANYA apabila PV yang direversal ini masih terikat PVL aktif (efek berantai).'],
                        ['name' => 'pv_payment_voucher_list_det', 'actions' => ['update'], 'desc' => 'status="Cancel" untuk seluruh baris PVL yang dibatalkan tersebut (efek berantai).'],
                        ['name' => 'kontrabon_h', 'actions' => ['update'], 'desc' => 'Tipe Reguler — direset penuh ke draft beserta jejak approval.'],
                        ['name' => 'kontrabon', 'actions' => ['update'], 'desc' => 'Tipe Reguler.'],
                        ['name' => 'kontrabon_h_installment_detail', 'actions' => ['update'], 'desc' => 'Tipe Installment — per baris cicilan direset.'],
                        ['name' => 'kontrabon_h_dp', 'actions' => ['update'], 'desc' => 'Tipe DP — direset penuh.'],
                        ['name' => 'kontrabon_dp', 'actions' => ['update'], 'desc' => 'Tipe DP.'],
                        ['name' => 'kontrabon_h_cbd', 'actions' => ['update'], 'desc' => 'Tipe CBD — direset penuh.'],
                        ['name' => 'kontrabon_cbd', 'actions' => ['update'], 'desc' => 'Tipe CBD.'],
                        ['name' => 'tbl_pv_h', 'actions' => ['update'], 'desc' => 'Tipe Biaya — hanya status direset, tidak sedalam 4 tipe lainnya.'],
                    ],
                    'tables_read' => [],
                    'notes' => [
                        'PALING KOMPLEKS di kelompok Reverse: mereversal satu PV yang kebetulan masih terikat Payment Voucher List akan ikut MEMBATALKAN SELURUH PVL tersebut, termasuk melepas (bukan mereversal) PV-PV LAIN yang kebetulan berada di batch PVL yang sama — efek berantai ini sudah diperingatkan lebih dulu lewat tanda visual pada form pengajuan (menu "Payment Voucher" pada Pengajuan Reverse).',
                        'Reset untuk tipe Biaya lebih dangkal dibanding 4 tipe lainnya (Reguler/Installment/DP/CBD) — hanya kolom status yang dikembalikan, tanpa mengosongkan jejak kolom approval, karena skema tabel tipe Biaya memang tidak melacak jejak approval selengkap tipe lainnya.',
                        'TIDAK memposting jurnal apa pun — sama seperti Payment List/Payment Voucher List, sifat reversal di sini murni tingkat alur kerja.',
                        'Kartu Hutang tidak disentuh.',
                        'Sudah memakai penjagaan status DRAFT sebelum memproses, konsisten dengan 2 menu approval "generasi baru" lainnya (Payment List, Payment Voucher List).',
                    ],
                ],
            ],
        ],
    ],
    'setting' => [
        'title' => 'Setting',
        'icon'  => 'fa-cogs',
        'items' => [
            'userrole' => [
                'title' => 'Userrole', 'icon' => 'fa-user-plus', 'path' => 'module/AP/userrole.php',
                'doc' => [
                    'summary' => 'Manajemen hak akses menu per pengguna — memberi atau mencabut akses ke menu tertentu untuk setiap user aplikasi lewat toggle switch, tanpa tombol "Simpan" (setiap toggle langsung tersimpan).',
                    'purpose' => 'Mengelola tabel useraccess yang menjadi dasar pengecekan hak akses di seluruh aplikasi ini — menu mana saja yang boleh dilihat/dipakai oleh user tertentu.',
                    'variants' => [],
                    'flow' => [
                        ['title' => 'Tampilkan ringkasan', 'desc' => 'Satu baris per user yang sudah memiliki minimal 1 akses menu, beserta daftar &amp; jumlah menunya — digabung dari userpassword + useraccess.'],
                        ['title' => 'Buka "Manage Role"', 'desc' => 'Modal menampilkan SELURUH menu terdaftar (dari tabel menurole) beserta status assigned/tidak untuk user yang dipilih.'],
                        ['title' => 'Toggle & tersimpan otomatis', 'desc' => 'Setiap perubahan toggle langsung memicu satu operasi ke database: mencentang = INSERT satu baris ke useraccess, melepas centang = DELETE baris tersebut. Tidak ada tombol simpan tunggal di akhir.'],
                    ],
                    'status_flow' => [],
                    'tables_write' => [
                        ['name' => 'useraccess', 'actions' => ['insert', 'delete'], 'desc' => 'Satu baris = satu kombinasi (username, menu) yang diizinkan — dibuat saat toggle dicentang, dihapus saat toggle dilepas.'],
                    ],
                    'tables_read' => [
                        ['name' => 'userpassword', 'desc' => 'Daftar user & nama lengkap untuk baris ringkasan.'],
                        ['name' => 'menurole', 'desc' => 'Daftar seluruh menu terdaftar di aplikasi — sumber kebenaran daftar menu yang bisa di-assign.'],
                    ],
                    'notes' => [
                        'Baris menurole dengan id = "35" sengaja dikecualikan dari daftar menu yang bisa di-assign lewat UI ini — kemungkinan menu internal/tersembunyi yang aksesnya tidak dimaksudkan untuk diberikan lewat halaman ini.',
                        'Komentar kode pada berkas backend (ajx_userrole.php) secara eksplisit menegaskan bahwa conn1/conn2 menunjuk ke basis data fisik yang sama, hanya beda resource koneksi — konsisten dengan catatan sumber data pada panel "Ringkasan Tabel Database" di halaman ini.',
                    ],
                ],
            ],
        ],
    ],
];

// Flatten to a simple key => doc lookup so the content-panel loop below
// doesn't need to know about grouping. Also track which group holds the
// first documented item and a documented/total count per group, so the
// sidebar can start with only the relevant group expanded (accordion)
// instead of dumping every item from every group on screen at once.
$firstDocumentedKey = null;
$firstDocumentedGroup = null;
$groupCounts = [];
// Maps itemKey => the nearest preceding section-divider label within its
// group (e.g. "Approval"), so the breadcrumb on each doc panel can show the
// full real menu path (Group > Section > Item) instead of just Group > Item.
$itemSectionMap = [];
foreach ($menuGroups as $groupKey => $group) {
    $done = 0;
    $total = 0;
    $currentSection = null;
    foreach ($group['items'] as $itemKey => $item) {
        if (isset($item['section'])) { $currentSection = $item['section']; continue; }
        $itemSectionMap[$itemKey] = $currentSection;
        $total++;
        if ($item['doc'] !== null) {
            $done++;
            if ($firstDocumentedKey === null) {
                $firstDocumentedKey = $itemKey;
                $firstDocumentedGroup = $groupKey;
            }
        }
    }
    $groupCounts[$groupKey] = ['done' => $done, 'total' => $total];
}

// ===== Database table cross-reference =====
// Built automatically from every documented item's tables_write/tables_read -
// no separate data entry needed, so it stays in sync as more menus get
// documented. Table-name cells are mostly single names ("mastercoa_v2") but a
// few are written as readable composites ("a / b", "a, b, c", or prose like
// "kontrabon & keluarganya (...)") - split on "," and "/" at the top level
// (respecting parentheses) and keep anything with "&"/"("/"." as one
// descriptive entry rather than guessing at names buried in prose.
function pdSplitTop($raw, $delim) {
    $parts = []; $depth = 0; $buf = ''; $len = strlen($raw); $dl = strlen($delim);
    for ($i = 0; $i < $len; $i++) {
        $ch = $raw[$i];
        if ($ch === '(') $depth++;
        elseif ($ch === ')') $depth--;
        if ($depth <= 0 && substr($raw, $i, $dl) === $delim) { $parts[] = $buf; $buf = ''; $i += $dl - 1; continue; }
        $buf .= $ch;
    }
    $parts[] = $buf;
    return $parts;
}
function pdTableNames($raw) {
    $out = [];
    foreach (pdSplitTop($raw, ',') as $seg) {
        $seg = trim($seg);
        if ($seg === '') continue;
        if (strpos($seg, '&') !== false || strpos($seg, '(') !== false || strpos($seg, '.') !== false) {
            $out[] = html_entity_decode($seg, ENT_QUOTES);
        } else {
            foreach (pdSplitTop($seg, '/') as $piece) {
                $piece = trim($piece);
                if ($piece !== '') $out[] = $piece;
            }
        }
    }
    return array_values(array_unique($out));
}

$tableIndex = []; // table name => [ ['menu' => 'Group › Section › Item', 'actions' => ['insert', ...]], ... ]
$tableDb = []; // table name => db source label (only set when it's not the default main DB)
foreach ($menuGroups as $group) {
    // Sub-judul di dalam grup (mis. "BPB Garment") ikut dibawa supaya label menu
    // lengkap sampai level terdalam - "AP › BPB Garment › Verifikasi BPB", bukan
    // hanya "AP › Verifikasi BPB".
    $curSection = null;
    foreach ($group['items'] as $item) {
        if (isset($item['section'])) { $curSection = $item['section']; continue; }
        if ($item['doc'] === null) continue;
        $menuLabel = $group['title'] . ' › ' . ($curSection !== null ? $curSection . ' › ' : '') . $item['title'];
        foreach (($item['doc']['tables_write'] ?? []) as $t) {
            foreach (pdTableNames($t['name']) as $tn) {
                $tableIndex[$tn][] = ['menu' => $menuLabel, 'actions' => $t['actions'] ?? ['update']];
                if (isset($t['db']) && !isset($tableDb[$tn])) $tableDb[$tn] = $t['db'];
            }
        }
        foreach (($item['doc']['tables_read'] ?? []) as $t) {
            foreach (pdTableNames($t['name']) as $tn) {
                $tableIndex[$tn][] = ['menu' => $menuLabel, 'actions' => ['select']];
                if (isset($t['db']) && !isset($tableDb[$tn])) $tableDb[$tn] = $t['db'];
            }
        }
    }
}
ksort($tableIndex, SORT_FLAG_CASE | SORT_STRING);
?>

<style>
  :root {
    --ink: #181d1a; --ink-soft: #667066; --line: #e2e6e0; --line-soft: #eef1ec;
    --surface: #ffffff; --accent: #0e7a5f; --accent-2: #b45309;
  }
  .docs-page {
    background: radial-gradient(900px circle at 8% -10%, rgba(14,122,95,.08) 0%, rgba(14,122,95,0) 45%),
      radial-gradient(800px circle at 100% 0%, rgba(180,83,9,.06) 0%, rgba(180,83,9,0) 40%), #f6f7f3;
    border-radius: 18px; padding: 20px;
  }
  .docs-hero {
    position: relative; overflow: hidden;
    background: linear-gradient(120deg, #0a1410 0%, #0d3327 42%, #0e7a5f 85%, #34d399 120%);
    border-radius: 16px; color: #fff; padding: 26px 30px; box-shadow: 0 14px 34px rgba(11,46,34,.28);
  }
  .docs-hero .eyebrow { font-size: 11px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; opacity: .78; }
  .docs-hero h4 { margin: 4px 0 0; font-weight: 800; font-size: 24px; }
  .docs-hero p { margin: 6px 0 0; opacity: .92; font-size: 13px; }

  .docs-layout { display: grid; grid-template-columns: 260px 1fr; gap: 18px; margin-top: 18px; align-items: start; }
  .docs-nav {
    background: var(--surface); border: 1px solid var(--line); border-radius: 14px; padding: 10px;
    position: sticky; top: 12px; max-height: calc(100vh - 90px); overflow-y: auto;
  }
  .docs-nav-group { margin-bottom: 2px; border-bottom: 1px solid var(--line-soft); }
  .docs-nav-group:last-child { border-bottom: none; }
  .docs-nav-group-title {
    display:flex; align-items:center; gap:8px; padding: 10px 8px; font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .05em; color: var(--ink); cursor: pointer; border-radius: 8px;
    user-select: none; transition: background .15s ease;
  }
  .docs-nav-group-title:hover { background: var(--line-soft); }
  .docs-nav-group-title .grp-count {
    margin-left: auto; font-size: 9.5px; font-weight: 800; color: var(--ink-soft); background: var(--line-soft);
    padding: 1px 7px; border-radius: 999px;
  }
  .docs-nav-group-title .grp-chevron { font-size: 10px; color: var(--ink-soft); transition: transform .2s ease; flex-shrink: 0; }
  .docs-nav-group.collapsed .grp-chevron { transform: rotate(-90deg); }
  .docs-nav-group-body { overflow: hidden; max-height: 3000px; transition: max-height .25s ease; padding-bottom: 4px; }
  .docs-nav-group.collapsed .docs-nav-group-body { max-height: 0; padding-bottom: 0; }
  .docs-nav-item {
    display: flex; align-items: center; gap: 10px; padding: 9px 12px 9px 22px; border-radius: 10px; cursor: pointer;
    font-size: 12.5px; font-weight: 700; color: var(--ink-soft); transition: .15s; position: relative;
  }
  .docs-nav-item::before { content:''; position:absolute; left: 10px; top: 50%; width: 5px; height: 5px; border-radius: 50%; background: var(--line); transform: translateY(-50%); }
  .docs-nav-item .ic { width: 24px; height: 24px; border-radius: 7px; background: var(--line-soft); color: var(--accent); display:flex; align-items:center; justify-content:center; font-size: 10.5px; flex-shrink:0; }
  .docs-nav-item:hover { background: var(--line-soft); }
  .docs-nav-item.active { background: linear-gradient(120deg,#0b3d2e,#0e7a5f); color: #fff; }
  .docs-nav-item.active::before { background: #fff; }
  .docs-nav-item.active .ic { background: rgba(255,255,255,.18); color: #fff; }
  .docs-nav-item.disabled { cursor: default; opacity: .55; }
  .docs-nav-item.disabled:hover { background: none; }
  .docs-nav-item .soon-badge { margin-left: auto; font-size: 8.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .03em; color: #a3abbd; background: var(--line-soft); padding: 2px 6px; border-radius: 5px; }
  .docs-nav-section { font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-soft); opacity: .55; padding: 10px 12px 3px 22px; }
  .docs-nav-empty { font-size: 11.5px; color: #a3abbd; padding: 12px; text-align:center; border-top: 1px dashed var(--line); margin-top: 6px; }

  .docs-nav-pinned {
    display:flex; align-items:center; gap:10px; padding: 10px 12px; border-radius: 10px; cursor:pointer;
    font-size: 12.5px; font-weight: 800; color: var(--ink); background: var(--line-soft); margin-bottom: 8px; transition: .15s;
  }
  .docs-nav-pinned .ic { width: 24px; height: 24px; border-radius: 7px; background: var(--accent); color:#fff; display:flex; align-items:center; justify-content:center; font-size: 10.5px; flex-shrink:0; }
  .docs-nav-pinned:hover { background: #e6ede8; }
  .docs-nav-pinned.active { background: linear-gradient(120deg,#0b3d2e,#0e7a5f); color: #fff; }
  .docs-nav-pinned.active .ic { background: rgba(255,255,255,.18); }

  .docs-content { display: flex; flex-direction: column; gap: 16px; }
  .doc-card { background: var(--surface); border: 1px solid var(--line); border-radius: 14px; padding: 20px 22px; box-shadow: 0 2px 10px rgba(23,27,46,.05); }
  .doc-card h5 { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: var(--ink-soft); margin: 0 0 14px; display:flex; align-items:center; gap:8px; }
  .doc-card h5 i { color: var(--accent); }
  .doc-head { display:flex; align-items:flex-start; gap: 14px; }
  .doc-head .ic-big { width: 48px; height: 48px; border-radius: 13px; background: linear-gradient(135deg,#0e7a5f,#34d399); color:#fff; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; box-shadow: 0 8px 18px rgba(14,122,95,.3); }
  .doc-head .name { font-size: 20px; font-weight: 800; color: var(--ink); }
  .doc-head .path { font-family: monospace; font-size: 11.5px; color: var(--ink-soft); background: var(--line-soft); padding: 2px 8px; border-radius: 6px; display:inline-block; margin-top: 4px; }
  .doc-head .summary { font-size: 13.5px; color: var(--ink-soft); margin-top: 10px; line-height: 1.6; }

  .variant-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
  .variant-card { border: 1px solid var(--line); border-radius: 12px; padding: 14px; background: var(--line-soft); }
  .variant-card .vt { display:flex; align-items:center; gap:8px; font-weight:800; font-size: 13px; color: var(--ink); margin-bottom: 6px; }
  .variant-card .vt i { color: var(--accent); }
  .variant-card p { font-size: 12px; color: var(--ink-soft); margin: 0; line-height: 1.5; }

  .flow-steps { position: relative; padding-left: 34px; }
  .flow-steps::before { content:''; position:absolute; left: 13px; top: 6px; bottom: 6px; width: 2px; background: var(--line); }
  .flow-step { position: relative; margin-bottom: 20px; }
  .flow-step:last-child { margin-bottom: 0; }
  .flow-step .num {
    position: absolute; left: -34px; top: 0; width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg,#0e7a5f,#34d399); color:#fff; font-weight:800; font-size:12.5px;
    display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 10px rgba(14,122,95,.3);
  }
  .flow-step .t { font-weight: 800; font-size: 13.5px; color: var(--ink); margin-bottom: 4px; }
  .flow-step .d { font-size: 12.5px; color: var(--ink-soft); line-height: 1.65; }
  .flow-step .d code { background: var(--line-soft); padding: 1px 5px; border-radius: 4px; font-size: 11.5px; color: var(--accent-2); }

  .status-flow-row { display:flex; align-items:center; flex-wrap: wrap; gap: 10px; }
  .status-pill { padding: 6px 16px; border-radius: 999px; font-size: 12px; font-weight: 800; }
  .status-pill.planned { background:#eef0f5; color:#5a6472; }
  .status-pill.progress { background:#e0f4f7; color:#0e7490; }
  .status-pill.hold { background:#fdf2df; color:#a2650a; }
  .status-arrow { color: var(--ink-soft); font-size: 14px; }

  .tbl-list { display: flex; flex-direction: column; gap: 0; }
  .tbl-row { display:flex; gap: 14px; padding: 10px 4px; border-bottom: 1px dashed var(--line); align-items:flex-start; flex-wrap: wrap; }
  .tbl-row:last-child { border-bottom: none; }
  .tbl-row .tname { font-family: monospace; font-size: 12px; font-weight: 800; color: var(--accent); background: var(--line-soft); padding: 3px 9px; border-radius: 6px; flex-shrink: 0; min-width: 170px; }
  .tbl-row .tdb { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; padding: 3px 7px; border-radius: 5px; flex-shrink: 0; align-self: flex-start; color: var(--ink-soft); background: transparent; border: 1px solid var(--line); opacity: .75; }
  .tbl-row .tdb.ext { color: #8a3fc4; border-color: #d8bdf0; background: #f8f0ff; opacity: 1; font-weight: 800; }
  .tbl-row .tdesc { font-size: 12.5px; color: var(--ink-soft); line-height: 1.55; padding-top: 3px; flex: 1 1 260px; min-width: 0; }
  .tbl-row .tactions { display:flex; gap: 4px; flex-shrink: 0; margin-left: auto; padding-top: 1px; }
  .tact { font-size: 9px; font-weight: 800; padding: 3px 8px; border-radius: 5px; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
  .tact.insert { background:#e5f7ea; color:#178a41; }
  .tact.update { background:#fdf2df; color:#a2650a; }
  .tact.delete { background:#fdeaea; color:#c0261d; }
  .tact.select { background:#eef0f5; color:#5a6472; }

  .notes-list { margin:0; padding-left: 18px; }
  .notes-list li { font-size: 12.5px; color: var(--ink-soft); line-height: 1.65; margin-bottom: 8px; }
  .notes-list li:last-child { margin-bottom: 0; }

  /* ---------- Database table summary ---------- */
  .db-stats-row { display:flex; gap: 14px; flex-wrap: wrap; margin-bottom: 16px; }
  .db-stat { flex: 1 1 160px; background: var(--line-soft); border-radius: 12px; padding: 14px 16px; }
  .db-stat .num { font-size: 24px; font-weight: 800; color: var(--ink); line-height:1; }
  .db-stat .lbl { font-size: 11px; color: var(--ink-soft); font-weight: 700; text-transform: uppercase; letter-spacing:.03em; margin-top: 4px; }
  .db-search-wrap { position: relative; margin-bottom: 16px; }
  .db-search-wrap i { position:absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--ink-soft); font-size: 13px; }
  .db-search-wrap input {
    width: 100%; border: 1.5px solid var(--line); border-radius: 10px; padding: 10px 14px 10px 38px;
    font-size: 13px; background: var(--surface); color: var(--ink); transition: border-color .15s ease;
  }
  .db-search-wrap input:focus { outline:none; border-color: var(--accent); }
  .db-search-count { font-size: 11px; color: var(--ink-soft); margin-top: 6px; }
  .db-table-wrap { border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
  .db-row { display:grid; grid-template-columns: 210px 1fr; gap: 14px; padding: 12px 16px; border-bottom: 1px solid var(--line-soft); align-items: start; }
  .db-row:last-child { border-bottom: none; }
  .db-row:nth-child(even) { background: var(--line-soft); }
  .db-row .tname { font-family: monospace; font-size: 12.5px; font-weight: 800; color: var(--accent); word-break: break-word; padding-top: 4px; }
  .db-usage { display:flex; flex-direction: column; gap: 6px; }
  .db-usage-line { display:flex; align-items:center; flex-wrap: wrap; gap: 6px; font-size: 12px; }
  .db-usage-menu { color: var(--ink); font-weight: 700; margin-right: 2px; }
  .db-empty-hint { text-align:center; padding: 40px 0; color: #a3abbd; font-size: 12.5px; }
</style>

<div class="docs-page">
  <div class="docs-hero">
    <div class="eyebrow">Internal Reference</div>
    <h4><i class="fa fa-book mr-2"></i>Dokumentasi Menu</h4>
    <p>Penjelasan tiap menu yang sudah dibuat — untuk apa, alurnya bagaimana, dan tabel database apa saja yang terlibat.</p>
  </div>

  <div class="docs-layout">
    <div class="docs-nav" id="docs-nav">
      <div class="docs-nav-pinned" id="pinned-db-summary" onclick="showDbSummary(this)">
        <span class="ic"><i class="fa fa-database"></i></span>
        <span>Ringkasan Tabel Database</span>
      </div>
      <?php foreach ($menuGroups as $groupKey => $group): ?>
        <?php $isActiveGroup = ($groupKey === $firstDocumentedGroup); $gc = $groupCounts[$groupKey]; ?>
        <div class="docs-nav-group<?= $isActiveGroup ? '' : ' collapsed' ?>" id="grp-<?= htmlspecialchars($groupKey) ?>">
          <div class="docs-nav-group-title" onclick="toggleGroup('<?= htmlspecialchars($groupKey) ?>')">
            <i class="fa <?= htmlspecialchars($group['icon']) ?>"></i><?= htmlspecialchars($group['title']) ?>
            <span class="grp-count"><?= $gc['done'] ?>/<?= $gc['total'] ?></span>
            <i class="fa fa-chevron-down grp-chevron"></i>
          </div>
          <div class="docs-nav-group-body">
          <?php foreach ($group['items'] as $itemKey => $item): ?>
            <?php if (isset($item['section'])): ?>
              <div class="docs-nav-section"><?= htmlspecialchars($item['section']) ?></div>
            <?php elseif ($item['doc'] !== null): ?>
              <div class="docs-nav-item<?= $itemKey === $firstDocumentedKey ? ' active' : '' ?>" data-key="<?= htmlspecialchars($itemKey) ?>" onclick="showDoc('<?= htmlspecialchars($itemKey) ?>', this)">
                <span class="ic"><i class="fa <?= htmlspecialchars($item['icon']) ?>"></i></span>
                <span><?= htmlspecialchars($item['title']) ?></span>
              </div>
            <?php else: ?>
              <div class="docs-nav-item disabled" title="Belum tersedia dokumentasinya">
                <span class="ic"><i class="fa <?= htmlspecialchars($item['icon']) ?>"></i></span>
                <span><?= htmlspecialchars($item['title']) ?></span>
                <span class="soon-badge">Belum</span>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="docs-nav-empty"><i class="fa fa-ellipsis-h"></i><br>Kelompok menu lainnya menyusul</div>
    </div>

    <div class="docs-content">

      <div class="doc-panel" id="doc-__db_summary__" style="display:none;">
        <div class="doc-card">
          <h5><i class="fa fa-database"></i>Ringkasan Tabel Database</h5>
          <p style="font-size:12.5px;color:var(--ink-soft);margin-bottom:16px;line-height:1.6;">
            Daftar seluruh nama tabel yang disebutkan pada dokumentasi menu di atas, dikumpulkan otomatis dari setiap menu yang sudah didokumentasikan. Untuk tiap tabel dapat dilihat menu mana saja yang menulis (merah) atau membaca (biru) tabel tersebut.
          </p>
          <p style="font-size:12px;color:var(--ink-soft);margin-bottom:16px;line-height:1.7;background:var(--line-soft);border-radius:8px;padding:10px 14px;">
            <i class="fa fa-server mr-1"></i><b>Sumber basis data:</b> kecuali ditandai lain, seluruh tabel berasal dari basis data MySQL utama <code>signalbit_erp</code> di server <code>10.10.5.12</code> (diakses lewat koneksi <code>conn1</code>/<code>conn2</code>/<code>conn_live</code> pada <code>conn/conn.php</code> — ketiganya menunjuk ke basis data fisik yang sama persis, bukan basis data terpisah). Dua pengecualian nyata di seluruh aplikasi ini: basis data PostgreSQL <code>alabare_knitting</code> di server <code>10.10.5.62</code> (via <code>conn4</code>, dipakai khusus menu BPB Knitting untuk menarik data dari sistem Knitting yang terpisah) dan basis data MySQL <code>hris_nag</code> di server <code>10.10.5.111</code> (via <code>conn3</code>, sistem HR terpisah). Tabel yang berasal dari pengecualian ini ditandai lencana ungu di setiap baris tabel.
          </p>
          <div class="db-stats-row">
            <div class="db-stat"><div class="num"><?= count($tableIndex) ?></div><div class="lbl">Nama Tabel Tercatat</div></div>
            <div class="db-stat"><div class="num"><?= $firstDocumentedGroup !== null ? array_sum(array_column($groupCounts, 'done')) : 0 ?></div><div class="lbl">Menu Sudah Terdokumentasi</div></div>
            <div class="db-stat"><div class="num"><?= count($menuGroups) ?></div><div class="lbl">Kelompok Menu</div></div>
          </div>
          <div class="db-search-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="db-search-input" placeholder="Cari nama tabel atau nama menu..." onkeyup="filterDbTable()">
          </div>
          <div class="db-search-count" id="db-search-count"></div>
          <div class="db-table-wrap" id="db-table-wrap">
            <?php foreach ($tableIndex as $tname => $usages): ?>
              <?php $searchBlob = strtolower($tname . ' ' . implode(' ', array_column($usages, 'menu'))); ?>
              <div class="db-row" data-search="<?= htmlspecialchars($searchBlob) ?>">
                <div class="tname"><?= htmlspecialchars($tname) ?><?php if (isset($tableDb[$tname])): ?><br><span class="tdb ext" style="margin-top:4px;display:inline-block;"><?= htmlspecialchars($tableDb[$tname]) ?></span><?php endif; ?></div>
                <div class="db-usage">
                  <?php foreach ($usages as $u): ?>
                    <div class="db-usage-line">
                      <span class="db-usage-menu"><?= htmlspecialchars($u['menu']) ?>:</span>
                      <?php foreach ($u['actions'] as $a): ?><span class="tact <?= htmlspecialchars($a) ?>"><?= htmlspecialchars($a) ?></span><?php endforeach; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="db-empty-hint" id="db-empty-hint" style="display:none;"><i class="fa fa-search" style="font-size:28px;"></i><div class="mt-2">Tidak ada tabel yang cocok dengan pencarian.</div></div>
        </div>
      </div>

      <?php foreach ($menuGroups as $group): foreach ($group['items'] as $itemKey => $item):
        if (isset($item['section']) || $item['doc'] === null) continue;
        $doc = $item['doc'];
      ?>
      <div class="doc-panel" id="doc-<?= htmlspecialchars($itemKey) ?>" style="<?= $itemKey === $firstDocumentedKey ? '' : 'display:none;' ?>">

        <div class="doc-card doc-head">
          <div class="ic-big"><i class="fa <?= htmlspecialchars($item['icon']) ?>"></i></div>
          <div>
            <div class="name"><?= htmlspecialchars($item['title']) ?></div>
            <div class="path"><i class="fa fa-sitemap mr-1"></i><?= htmlspecialchars($group['title']) ?> &rsaquo; <?php if (!empty($itemSectionMap[$itemKey])): ?><?= htmlspecialchars($itemSectionMap[$itemKey]) ?> &rsaquo; <?php endif; ?><?= htmlspecialchars($item['title']) ?>
              <span style="margin-left:8px;"><i class="fa fa-code mr-1"></i><?= htmlspecialchars($item['path']) ?></span>
            </div>
            <div class="summary"><?= htmlspecialchars($doc['summary']) ?></div>
          </div>
        </div>

        <?php if (!empty($doc['purpose'])): ?>
        <div class="doc-card">
          <h5><i class="fa fa-bullseye"></i>Untuk Apa Menu Ini</h5>
          <p style="font-size:12.5px;color:var(--ink-soft);line-height:1.6;"><?= htmlspecialchars($doc['purpose']) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($doc['variants'])): ?>
        <div class="doc-card">
          <h5><i class="fa fa-sitemap"></i>Variannya</h5>
          <div class="variant-grid">
            <?php foreach ($doc['variants'] as $v): ?>
              <div class="variant-card">
                <div class="vt"><i class="fa <?= htmlspecialchars($v['icon']) ?>"></i><?= htmlspecialchars($v['name']) ?></div>
                <p><?= htmlspecialchars($v['desc']) ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($doc['flow'])): ?>
        <div class="doc-card">
          <h5><i class="fa fa-random"></i>Alur Proses</h5>
          <div class="flow-steps">
            <?php foreach ($doc['flow'] as $i => $step): ?>
              <div class="flow-step">
                <div class="num"><?= $i + 1 ?></div>
                <div class="t"><?= htmlspecialchars($step['title']) ?></div>
                <div class="d"><?= $step['desc'] ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($doc['status_flow'])): ?>
        <div class="doc-card">
          <h5><i class="fa fa-flag-checkered"></i>Status Dokumen</h5>
          <div class="status-flow-row">
            <?php foreach ($doc['status_flow'] as $i => $s): ?>
              <?php if ($i > 0): ?><span class="status-arrow"><i class="fa fa-long-arrow-right"></i></span><?php endif; ?>
              <span class="status-pill <?= htmlspecialchars($s['cls']) ?>"><?= htmlspecialchars($s['label']) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($doc['tables_write'])): ?>
        <div class="doc-card">
          <h5><i class="fa fa-database"></i>Tabel Ditulis / Diubah</h5>
          <div class="tbl-list">
            <?php foreach ($doc['tables_write'] as $t): ?>
              <div class="tbl-row">
                <span class="tname"><?= htmlspecialchars($t['name']) ?></span>
                <span class="tdb<?= isset($t['db']) ? ' ext' : '' ?>"><?= htmlspecialchars($t['db'] ?? 'signalbit_erp (conn2)') ?></span>
                <span class="tdesc"><?= htmlspecialchars($t['desc']) ?></span>
                <span class="tactions"><?php foreach (($t['actions'] ?? []) as $a): ?><span class="tact <?= htmlspecialchars($a) ?>"><?= htmlspecialchars($a) ?></span><?php endforeach; ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($doc['tables_read'])): ?>
        <div class="doc-card">
          <h5><i class="fa fa-eye"></i>Tabel Referensi / Master (Baca Saja)</h5>
          <div class="tbl-list">
            <?php foreach ($doc['tables_read'] as $t): ?>
              <div class="tbl-row">
                <span class="tname"><?= htmlspecialchars($t['name']) ?></span>
                <span class="tdb<?= isset($t['db']) ? ' ext' : '' ?>"><?= htmlspecialchars($t['db'] ?? 'signalbit_erp (conn2)') ?></span>
                <span class="tdesc"><?= htmlspecialchars($t['desc']) ?></span>
                <span class="tactions"><span class="tact select">select</span></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($doc['notes'])): ?>
        <div class="doc-card">
          <h5><i class="fa fa-info-circle"></i>Catatan Teknis</h5>
          <ul class="notes-list">
            <?php foreach ($doc['notes'] as $n): ?><li><?= htmlspecialchars($n) ?></li><?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

      </div>
      <?php endforeach; endforeach; ?>
    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
  $('#body-row .collapse').collapse('hide');
  $('#collapse-icon').addClass('fa-angle-double-left');
  $('[data-toggle=sidebar-colapse]').click(function () { SidebarCollapse(); });
  function SidebarCollapse() {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
    var sep = $('.sidebar-separator-title');
    sep.hasClass('d-flex') ? sep.removeClass('d-flex') : sep.addClass('d-flex');
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }

  function showDoc(key, el) {
    $('.doc-panel').hide();
    $('#doc-' + key).show();
    $('.docs-nav-item, .docs-nav-pinned').removeClass('active');
    $(el).addClass('active');
  }

  function toggleGroup(key) {
    $('#grp-' + key).toggleClass('collapsed');
  }

  function showDbSummary(el) {
    showDoc('__db_summary__', el);
  }

  function filterDbTable() {
    var q = $('#db-search-input').val().trim().toLowerCase();
    var visible = 0;
    $('#db-table-wrap .db-row').each(function () {
      var match = !q || $(this).data('search').indexOf(q) !== -1;
      $(this).toggle(match);
      if (match) visible++;
    });
    $('#db-empty-hint').toggle(visible === 0);
    $('#db-search-count').text(q ? visible + ' tabel cocok dengan "' + q + '"' : '');
  }
</script>
