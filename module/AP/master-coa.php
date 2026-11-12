<?php include '../header.php' ?>
<?php
/* ============================================================================
   LIST CHART OF ACCOUNT

   TAMPILANNYA DISAMAKAN DGN HALAMAN DAFTAR LAIN (List Payment Plan, List
   Petty Cash Out, FTR CBD/DP) - diminta pemakai 11 Nov 2026: kartu
   .ftl-card, kepala biru berkotak ikon + jejak menu, panel saringan
   .ftl-panel, tombol .app-btn, tabel .ftl-tbl. Formulir Create & Edit sudah
   disamakan lebih dulu (10 Nov 2026) memakai kosakata .pco-; halaman ini
   pasangannya.

   KOLOMNYA TETAP SEPERTI SEBELUMNYA. Sempat ditambahkan tujuh kolom baru
   (empat Cost Center, Petty Cash Code, dua Bank Approval) mengikuti tujuh
   isian baru di formulirnya, lalu DICABUT LAGI atas permintaan pemakai pada
   hari yang sama: "ini ngga perlu ditampilkan, untuk kolom kolom pakai yang
   sebelumnya saja, cuman merubah styling". Ketujuh kolom itu tetap bisa
   diisi & dilihat lewat formulir Create/Edit. Catatan ini ditulis supaya
   tidak ada yang menambahkannya lagi sbg "kelanjutan yang terlupa".

   Karena itu pula halaman ini TIDAK menyentuh bankin_no_approve /
   bankout_no_approve, jadi tidak bergantung pada migrasi
   db_migrate_LIVE_20261109_bank_post_tanpa_approval.sql - berbeda dgn
   formulir Create/Edit-nya.

   TIDAK ADA KOLOM LAMA YANG DIBUANG ATAU DIGABUNG. Delapan kolom nama Inggris
   (Category 1-5 + Cash Flow Debit/Credit/Indirect) dulu dicetak sbg
   <td style="display:none"> - tidak pernah terlihat, tapi ikut terbaca kotak
   cari. Sekarang kolomnya diurus DataTables sendiri (visible:false,
   searchable:true): tetap bisa dicari, tapi lebarnya tidak lagi ikut
   dihitung oleh gulir mendatar.
   ============================================================================ */

/* ---------------------------------------------------------------------------
   SARINGAN. Formulirnya dipindah ke method="get" (dulu post) dgn tiga alasan:
   (1) sesudah tombol Active/Deactive ditekan halaman ini memuat ulang - di
   atas POST, Chrome memunculkan kotak "Confirm Form Resubmission"; (2) hasil
   saringan jadi bisa ditautkan/di-bookmark; (3) tombol Excel meneruskan
   saringan yang SAMA tanpa menebak. Dibaca lewat $_REQUEST supaya taut lama
   yang masih mengirim POST tetap jalan.

   Nilainya juga DILOLOSKAN (mysqli_real_escape_string) sebelum masuk WHERE -
   dulu ditempel apa adanya dari POST.
   --------------------------------------------------------------------------- */
$nama_ctg2 = isset($_REQUEST['nama_ctg2']) && $_REQUEST['nama_ctg2'] !== '' ? $_REQUEST['nama_ctg2'] : 'ALL';
$nama_ctg5 = isset($_REQUEST['nama_ctg5']) && $_REQUEST['nama_ctg5'] !== '' ? $_REQUEST['nama_ctg5'] : 'ALL';
$Status    = isset($_REQUEST['Status'])    && $_REQUEST['Status']    !== '' ? $_REQUEST['Status']    : 'ALL';

/* Dulu: saat halaman dibuka pertama kali (bukan POST) ketiga nilai ini kosong,
   dan rangkaian if/elseif-nya jatuh ke cabang terakhir -> WHERE id_ctg2 = ''
   and id_ctg5 = '' and status = '' -> TABEL KOSONG, padahal ketiga dropdown
   memperlihatkan 'ALL'. Sekarang bawaannya ALL/ALL/ALL, jadi yang terlihat
   sama dgn yang tertulis di saringannya. */
$syarat = array();
if ($nama_ctg2 !== 'ALL') { $syarat[] = "id_ctg2 = '" . mysqli_real_escape_string($conn1, $nama_ctg2) . "'"; }
if ($nama_ctg5 !== 'ALL') { $syarat[] = "id_ctg5 = '" . mysqli_real_escape_string($conn1, $nama_ctg5) . "'"; }
if ($Status    !== 'ALL') { $syarat[] = "status = '"  . mysqli_real_escape_string($conn1, $Status)    . "'"; }
$where = $syarat ? 'where ' . implode(' and ', $syarat) : '';

/* Taut Excel: tiga nilai saringan saja. Kolom WHERE TIDAK lagi dikirim lewat
   alamat - ekspor_master_coa.php membangunnya sendiri dari ketiga nilai itu.
   Dulu tautnya ditulis "...?nama_ctg2=X && nama_ctg5=Y && Status=Z && where=..."
   pakai '&&' dan spasi, sehingga yang sampai di sana hanya satu parameter
   berisi semuanya: SELURUH saringan tidak terbaca dan Excel-nya selalu
   mengekspor semua baris. */
$taut_excel = 'ekspor_master_coa.php?nama_ctg2=' . urlencode($nama_ctg2)
            . '&nama_ctg5=' . urlencode($nama_ctg5)
            . '&Status=' . urlencode($Status);

/* Hak akses dibaca SEKALI di sini. Dulu query userpassword ini dijalankan
   ULANG untuk setiap baris - 693 query tambahan sekali muat. */
$rs_hak = mysqli_fetch_array(mysqli_query($conn1,
    "select Groupp, finance, ap_apprv_lp from userpassword where username = '"
    . mysqli_real_escape_string($conn1, $user) . "'"));
$group = isset($rs_hak['Groupp'])  ? $rs_hak['Groupp']  : '';
$fin   = isset($rs_hak['finance']) ? $rs_hak['finance'] : '';

?>
<!-- Tiap berkas CSS ditaut sendiri dgn penanda versi dari filemtime, jadi
     perubahannya langsung sampai ke pemakai tanpa kena cache. -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<style>
/* ---- Kepala tabel bertingkat ----
   Kelompok di baris atas, kolomnya di baris bawah. Ini BUKAN menggabungkan
   kolom: tiap nilai tetap punya kolom sendiri. Warna latarnya SENGAJA sama
   dgn kepala lain - versi pertama memberi baris kelompok navy yang lebih tua,
   hasilnya kepala tabel terbelah jadi dua pita warna yang tidak rata
   tingginya (sel rowspan=2 di kiri tetap warna asli) dan yang terbaca justru
   beda warnanya, bukan kelompoknya. Pembedanya cukup garis tipis. */
.ftl-tbl thead th.grp {
  font-size: 10.5px; letter-spacing: .05em;
  border-bottom: 1px solid rgba(30, 58, 138, .22) !important;
}

/* Nomor & nama COA: jangkar baca di kolom paling kiri. */
.coa-tbl td.col-coa  { white-space: nowrap; font-weight: 700; color: #1e3a8a; }
.coa-tbl td.col-nama { text-align: left; }

/* Tabel ini 12 kolom terlihat, jadi memang digulir ke samping (scrollX).
   Isi sel TIDAK boleh membungkus: kalau membungkus, tinggi barisnya ikut
   melar dan 693 baris jadi tidak terbaca sekali pandang. */
.coa-tbl td { white-space: nowrap; }

/* Barisnya TIDAK membuka apa pun kalau diklik - di halaman daftar lain
   .ftl-tbl memberi cursor:pointer karena barisnya membuka modal rincian. */
.coa-tbl tbody tr { cursor: default; }

/* ---- Kolom beku: No COA (kiri) & Action (kanan) ----
   Memakai position:sticky LANGSUNG, bukan pustaka FixedColumns. Berkas
   dataTables.fixedColumns.min.js yang ada di aplikasi ini versi 4.2.1
   sedangkan DataTables-nya 1.10.21, dan CSS-nya ditaut header.php dari
   cdn.datatables.net - dua hal yang tidak bisa diandalkan di jaringan pabrik.
   Aturan di bawah tidak bergantung pada keduanya.

   Dua baris kepala: kolom pertama/terakhir SELALU ada di baris kepala
   PERTAMA (keduanya rowspan=2), jadi penunjuknya dibatasi ke tr:first-child -
   kalau tidak, 'Debit' dan 'Bank Out' di baris kedua ikut dibekukan. */
.coa-tbl thead tr:first-child > th:first-child,
.coa-tbl tbody td:first-child {
  position: sticky; left: 0; z-index: 3;
}
.coa-tbl thead tr:first-child > th:last-child,
.coa-tbl tbody td:last-child {
  position: sticky; right: 0; z-index: 3;
}
/* Latar WAJIB pekat: tanpa ini isi kolom yang lewat di belakangnya terbaca
   menumpuk di atas nomor COA dan tombol Edit. Kepala tabel sudah punya latar
   sendiri (#eef3fb) dari app-ftr-list.css. */
.coa-tbl tbody td:first-child,
.coa-tbl tbody td:last-child { background: #fff; z-index: 2; }
.coa-tbl tbody tr:hover > td:first-child,
.coa-tbl tbody tr:hover > td:last-child { background: #f5f8ff; }
/* Garis bayangan tipis supaya terlihat kolomnya memang dipaku, bukan
   kebetulan berhenti di situ. */
.coa-tbl tr > :first-child { box-shadow: 1px 0 0 #e8edf5; }
.coa-tbl tr > :last-child  { box-shadow: -1px 0 0 #e8edf5; }
/* Kotak gulir DataTables menggantikan .ftl-tblwrap sbg tepi tabel. */
.ftl-tblwrap .dataTables_scrollBody { border-radius: 0 0 10px 10px; }
.ftl-tblwrap .dataTables_scrollHead { border-radius: 10px 10px 0 0; }

/* Lebar kolom Action dijaga supaya kedua tombolnya tidak pernah terbelah. */
.coa-tbl td.ftl-act-cell { min-width: 168px; }

/* Di layar HP dua kolom beku memakan hampir seluruh lebar layar (No COA 61px
   + Action 188px dari +-360px), jadi yang tersisa untuk isinya tinggal
   seruas. Di bawah 768px kolom Action dilepas - No COA yang tetap dipaku,
   karena itu yang dipakai orang untuk tahu sedang melihat baris mana. */
@media (max-width: 767.98px) {
  .coa-tbl thead tr:first-child > th:last-child,
  .coa-tbl tbody td:last-child { position: static; box-shadow: none; }
}
</style>

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">

  <div class="ftl-card">
    <div class="ftl-head">
      <span class="ftl-head-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
      <div>
        <h1>List Chart of Account</h1>
        <span class="ftl-crumb">Master &rsaquo; Chart of Account</span>
      </div>
    </div><!-- /.ftl-head -->

    <div class="ftl-panel">
      <form id="form-data" action="master-coa.php" method="get">
        <div class="row">

          <div class="col-12 col-sm-6 col-lg-3 mb-2">
            <label for="nama_ctg2"><b>Category 2</b></label>
            <select class="form-control app-select2" name="nama_ctg2" id="nama_ctg2">
              <option value="ALL"<?php echo $nama_ctg2 === 'ALL' ? ' selected="selected"' : ''; ?>>ALL</option>
              <?php
              $q_ctg2 = mysqli_query($conn1, "select id_ctg2, CONCAT(id_ctg2,' - ',ind_name) as name from master_coa_ctg2 group by id");
              while ($row = mysqli_fetch_array($q_ctg2)) {
                  echo '<option value="' . htmlspecialchars($row['id_ctg2'], ENT_QUOTES) . '"'
                     . ($row['id_ctg2'] == $nama_ctg2 ? ' selected="selected"' : '') . '>'
                     . htmlspecialchars($row['name'], ENT_QUOTES) . '</option>';
              }
              ?>
            </select>
          </div>

          <div class="col-12 col-sm-6 col-lg-3 mb-2">
            <label for="nama_ctg5"><b>Category 5</b></label>
            <select class="form-control app-select2" name="nama_ctg5" id="nama_ctg5">
              <option value="ALL"<?php echo $nama_ctg5 === 'ALL' ? ' selected="selected"' : ''; ?>>ALL</option>
              <?php
              /* Daftarnya DIPERSEMPIT oleh Category 2 lewat ubah_ctg5.php
                 (AJAX) begitu Category 2 diubah. Waktu halaman dimuat ulang
                 dgn saringan terpilih, pilihan yang sedang aktif harus sudah
                 ada di dalam <select> ini - kalau tidak, isinya tampak
                 kembali ke ALL padahal saringannya masih jalan. */
              $sql_ctg5 = "select id_ctg5, CONCAT(id_ctg5,' - ',ind_name) as name from master_coa_ctg5";
              if ($nama_ctg2 !== 'ALL') {
                  $sql_ctg5 .= " where id_ctg2 = '" . mysqli_real_escape_string($conn1, $nama_ctg2) . "'";
              }
              $sql_ctg5 .= " group by id";
              $q_ctg5 = mysqli_query($conn1, $sql_ctg5);
              while ($row = mysqli_fetch_array($q_ctg5)) {
                  echo '<option value="' . htmlspecialchars($row['id_ctg5'], ENT_QUOTES) . '"'
                     . ($row['id_ctg5'] == $nama_ctg5 ? ' selected="selected"' : '') . '>'
                     . htmlspecialchars($row['name'], ENT_QUOTES) . '</option>';
              }
              ?>
            </select>
          </div>

          <div class="col-12 col-sm-6 col-lg-2 mb-2">
            <label for="Status"><b>Status</b></label>
            <select class="form-control app-select2" name="Status" id="Status">
              <option value="ALL"<?php      echo $Status === 'ALL'      ? ' selected="selected"' : ''; ?>>ALL</option>
              <option value="Active"<?php   echo $Status === 'Active'   ? ' selected="selected"' : ''; ?>>Active</option>
              <option value="Deactive"<?php echo $Status === 'Deactive' ? ' selected="selected"' : ''; ?>>Deactive</option>
            </select>
          </div>

          <div class="col-12 col-lg-4 mb-2 ftl-actions">
            <button type="submit" class="app-btn app-btn-primary app-btn-sm">
              <i class="fa fa-search" aria-hidden="true"></i> Search
            </button>
            <button type="button" id="reset" class="app-btn app-btn-light app-btn-sm">
              <i class="fa fa-repeat" aria-hidden="true"></i> Reset
            </button>
            <a target="_blank" href="<?php echo htmlspecialchars($taut_excel, ENT_QUOTES); ?>"
               class="app-btn app-btn-excel app-btn-sm">
              <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
            </a>
            <button id="btncreate" type="button" class="app-btn app-btn-dark app-btn-sm">
              <i class="fa fa-plus-circle" aria-hidden="true"></i> Create
            </button>
          </div>

        </div>
      </form>
    </div><!-- /.ftl-panel -->

    <div class="ftl-body">
      <form id="formdata2">
        <div class="ftl-tblwrap">
          <table id="datatable" class="table ftl-tbl coa-tbl" role="grid" cellspacing="0" style="width:100%;">
            <thead>
              <tr>
                <th rowspan="2">No COA</th>
                <th rowspan="2">COA Name</th>
                <th rowspan="2">Category 1</th>
                <th rowspan="2">Category 1 (English)</th>
                <th rowspan="2">Category 2</th>
                <th rowspan="2">Category 2 (English)</th>
                <th rowspan="2">Category 3</th>
                <th rowspan="2">Category 3 (English)</th>
                <th rowspan="2">Category 4</th>
                <th rowspan="2">Category 4 (English)</th>
                <th rowspan="2">Category 5</th>
                <th rowspan="2">Category 5 (English)</th>
                <th colspan="4" class="grp">Category Cash Flow Direct</th>
                <th rowspan="2">Category Cash Flow Indirect</th>
                <th rowspan="2">Cash Flow Indirect (English)</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Action</th>
              </tr>
              <tr>
                <th>Debit</th>
                <th>Debit (English)</th>
                <th>Credit</th>
                <th>Credit (English)</th>
              </tr>
            </thead>

            <tbody id="tbl_cf">
            <?php
            /* Query DIBIARKAN APA ADANYA - bentuk join bersarangnya sama
               persis dgn ekspor_master_coa.php, dan keduanya memang harus
               memberi baris yang sama. */
            $sql = mysqli_query($conn1, "select * from ((select no_coa,id_direct_debit,id_direct_credit,id_indirect,nama_coa,id_ctg5,id_ctg2,status from mastercoa_v2) coa INNER JOIN

(select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
left join

(select id,ind_name as idndirdebit, eng_name as engdirdebit from tbl_master_cashflow) dirdebit on dirdebit.id = coa.id_direct_debit left join

(select id,ind_name as idndircredit, eng_name as engdircredit from tbl_master_cashflow) dircredit on dircredit.id = coa.id_direct_credit left join

(select id,ind_name as idnindir, eng_name as engindir from tbl_master_cashflow) indir on indir.id = coa.id_indirect) $where");

            while ($row = mysqli_fetch_array($sql)) {
                $status = $row['status'];
                /* Cash flow boleh belum dipilih (LEFT JOIN) - 'NA' sama dgn
                   yang dipakai halaman ini sejak dulu dan dgn ekspornya. */
                $idndirdebit  = isset($row['idndirdebit'])  ? $row['idndirdebit']  : "NA";
                $engdirdebit  = isset($row['engdirdebit'])  ? $row['engdirdebit']  : "NA";
                $idndircredit = isset($row['idndircredit']) ? $row['idndircredit'] : "NA";
                $engdircredit = isset($row['engdircredit']) ? $row['engdircredit'] : "NA";
                $idnindir     = isset($row['idnindir'])     ? $row['idnindir']     : "NA";
                $engindir     = isset($row['engindir'])     ? $row['engindir']     : "NA";

                echo '<tr>
                <td class="col-coa">' . htmlspecialchars($row['no_coa'], ENT_QUOTES) . '</td>
                <td class="col-nama">' . htmlspecialchars($row['nama_coa'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['indname1'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['engname1'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['indname2'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['engname2'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['indname3'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['engname3'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['indname4'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['engname4'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['indname5'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($row['engname5'], ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($idndirdebit, ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($engdirdebit, ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($idndircredit, ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($engdircredit, ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($idnindir, ENT_QUOTES) . '</td>
                <td>' . htmlspecialchars($engindir, ENT_QUOTES) . '</td>
                <td><span class="ftl-st ' . ($status === 'Active' ? 'is-approved' : 'is-cancel') . '">'
                    . htmlspecialchars($status, ENT_QUOTES) . '</span></td>';

                /* Tombolnya membawa nomor COA & status di data-* sendiri.
                   Dulu penangannya membaca td:eq(18) dan td:eq(0) - nomor
                   urut sel yang langsung salah begitu ada kolom disisipkan,
                   dan kini memang tidak bisa dipakai lagi: kolom yang
                   disembunyikan DataTables dibuang dari DOM, jadi urutan sel
                   di layar tidak sama dgn urutan kolom. Aturan siapa yang
                   boleh menekan apa DIBIARKAN SAMA PERSIS - termasuk bahwa
                   baris Deactive tidak punya tombol Edit. */
                echo '<td class="ftl-act-cell">';
                if ($status == 'Deactive' and $group != 'STAFF' and $fin == '1') {
                    echo '<div class="ftl-act">'
                       . '<button type="button" name="btnupdate" class="app-btn app-btn-success app-btn-sm js-coa-status"'
                       . ' data-coa="' . htmlspecialchars($row['no_coa'], ENT_QUOTES) . '" data-status="' . htmlspecialchars($status, ENT_QUOTES) . '">Active</button>'
                       . '</div>';
                } elseif ($status == 'Deactive' and $fin == '1') {
                    echo '-';
                } elseif ($status == 'Active' and $group != 'STAFF' and $fin == '1') {
                    echo '<div class="ftl-act">'
                       . '<a href="edit-master-coa.php?no_coa=' . urlencode(base64_encode($row['no_coa'])) . '"'
                       . ' class="app-btn app-btn-warning app-btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>'
                       . '<button type="button" name="btnupdate" class="app-btn app-btn-danger app-btn-sm js-coa-status"'
                       . ' data-coa="' . htmlspecialchars($row['no_coa'], ENT_QUOTES) . '" data-status="' . htmlspecialchars($status, ENT_QUOTES) . '">Deactive</button>'
                       . '</div>';
                } elseif ($status == 'Active' and $fin == '1') {
                    echo '-';
                } else {
                    echo '';
                }
                echo '</td>';
                echo '</tr>';
            }
            ?>
            </tbody>
          </table>
        </div><!-- /.ftl-tblwrap -->
      </form>
    </div><!-- /.ftl-body -->
  </div><!-- /.ftl-card -->
</div><!-- /.container-fluid -->

<!-- Modal sandi: penjaga sebelum status COA diubah. Bentuknya mengikuti
     kosakata .ftl-modal yang dipakai halaman daftar lain. -->
<div class="modal fade ftl-modal" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="Heading" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document" style="max-width:420px;">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="Heading">Change COA Status</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="container">
        <form id="modal-form2" method="post">
          <p class="mb-2" style="font-size:12px;color:#475569;">
            <b id="coa_info"></b><br>Password Authentication Required
          </p>
          <input type="hidden" name="txt_pass" id="txt_pass" value="Cobaaja123">
          <input type="hidden" name="txt_type" id="txt_type" value="">
          <input type="hidden" name="txt_id" id="txt_id" value="">
          <div class="input-group">
            <input type="password" id="pass" name="pass" class="form-control" autocomplete="off">
            <div class="input-group-append">
              <!-- onclick=change() menukar ikon mata buka/tutup -->
              <span id="mybutton" onclick="change()" class="input-group-text" style="cursor:pointer;">
                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor"
                     xmlns="http://www.w3.org/2000/svg">
                  <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                  <path fill-rule="evenodd"
                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                </svg>
              </span>
            </div>
          </div>
          <div class="text-right mt-3 mb-2">
            <button type="button" class="app-btn app-btn-light app-btn-sm" data-dismiss="modal">
              <i class="fa fa-times"></i> Cancel
            </button>
            <button type="submit" id="send2" name="send2" class="app-btn app-btn-primary app-btn-sm">
              <i class="fa fa-thumbs-up"></i> Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript. header.php TIDAK memuat satu pun pustaka JS,
     jadi tiap halaman memuat miliknya sendiri. -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>

<script>
// Hide submenus
$('#body-row .collapse').collapse('hide');

// Collapse/Expand icon
$('#collapse-icon').addClass('fa-angle-double-left');

// Collapse click
$('[data-toggle=sidebar-colapse]').click(function() {
    SidebarCollapse();
});

function SidebarCollapse () {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }

    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>

<script>
  /* select2 di semua dropdown saringan - sama dgn List Payment Plan. Panelnya
     ditempel ke <body> supaya tidak terpotong kartu, dan daftar pendek
     (Status, 3 pilihan) tidak diberi kotak cari. */
  $(function () {
    $('.app-select2').each(function () {
      var $s = $(this);
      var o = { width: '100%', dropdownParent: $('body') };
      if ($s.find('option').length < 8) { o.minimumResultsForSearch = Infinity; }
      $s.select2(o);
    });
  });
</script>

<script>
$(document).ready(function () {

  /* 20 kolom, 8 di antaranya nama Inggris yang tidak ditampilkan tapi TETAP
     ikut dicari (visible:false, sedangkan searchable tetap bawaannya: true).
     Nomor di bawah ini urutan kolom, dihitung dari 0:

         0  No COA                  10  Category 5
         1  COA Name                11  Category 5 (English)  *
         2  Category 1              12  Debit
         3  Category 1 (English)  * 13  Debit (English)       *
         4  Category 2              14  Credit
         5  Category 2 (English)  * 15  Credit (English)      *
         6  Category 3              16  Cash Flow Indirect
         7  Category 3 (English)  * 17  Indirect (English)    *
         8  Category 4              18  Status
         9  Category 4 (English)  * 19  Action

     scrollX WAJIB: 12 kolom terlihat tidak muat satu layar (nama kategori &
     cash flow panjang-panjang), dan tanpa kotak gulir sendiri DataTables
     akan memeras kolomnya. */
  $('#datatable').DataTable({
    scrollX: true,
    order: [],               /* urutan apa adanya dari query */
    pageLength: 10,                 /* 10 baris per halaman - diminta pemakai */
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
    columnDefs: [
      { targets: [3, 5, 7, 9, 11, 13, 15, 17], visible: false },
      { targets: [19], orderable: false }
    ],
    language: { searchPlaceholder: 'Search COA...', search: '' }
  });
});
</script>

<script type="text/javascript">
  /* Tombol Active/Deactive -> modal sandi. Penangannya ditempel ke #formdata2
     (bukan ke tombolnya) supaya tetap bekerja sesudah DataTables menggambar
     ulang halaman tabel. */
  $("#formdata2").on("click", ".js-coa-status", function () {
    $('#txt_type').val($(this).data('status'));
    $('#txt_id').val($(this).data('coa'));
    $('#coa_info').text($(this).data('coa') + ' → ' + ($(this).data('status') === 'Active' ? 'Deactive' : 'Active'));
    $('#pass').val('');
    $('#mymodal2').modal('show');
  });
</script>

<script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){
        var pass = document.getElementById('pass').value;
        var txt_pass = document.getElementById('txt_pass').value;
        var status = document.getElementById('txt_type').value;
        var id_cf = document.getElementById('txt_id').value;
        var update_user = '<?php echo $user; ?>';

         if (pass == txt_pass) {
        $.ajax({
            type:'POST',
            url:'update_coa.php',
            data: {'status':status, 'id_cf':id_cf, 'update_user':update_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                window.location.reload(true);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        }
        if(document.getElementById('pass').value == document.getElementById('txt_pass').value){
            alert("Data changed successfully");
            return false;
        }else{
            alert("Incorrect Password");
        return false;
        }
    });
</script>

<script type="text/javascript">
    /* Category 2 mempersempit daftar Category 5. */
    $('#nama_ctg2').change(function() {
        var id = $(this).val();
        $.ajax({
            type: 'POST',
            url: 'ubah_ctg5.php',
            data: {'id':id},
            success: function(response) {
                /* .val('ALL') WAJIB: ubah_ctg5.php mencetak selected="selected"
                   di SETIAP option, jadi yang terpilih jadi option TERAKHIR -
                   saringan ikut berubah ke kategori yang tidak pernah dipilih
                   pemakai. Berkas itu dipakai bersama master-costcenter.php,
                   jadi dibetulkan di sini saja supaya menu lain tidak ikut
                   berubah tanpa diminta. */
                $('#nama_ctg5').html(response).val('ALL').trigger('change.select2');
            }
        });
    });
</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "create-master-coa.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "master-coa.php";
};
</script>

<script>
    // membuat fungsi change - menukar ikon mata di kotak sandi
    function change() {
        var x = document.getElementById('pass').type;
        if (x == 'password') {
            document.getElementById('pass').type = 'text';
            document.getElementById('mybutton').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                            <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                            <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                            </svg>`;
        }
        else {
            document.getElementById('pass').type = 'password';
            document.getElementById('mybutton').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                            <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                            </svg>`;
        }
    }
</script>

</body>

</html>
