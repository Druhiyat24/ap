<?php include '../header.php' ?>
<?php
/* Cash > Projection > Other - halaman DAFTAR.
   Satu daftar untuk tiga jenis (Book Transfer, Other Inflow, PV Expense),
   dibedakan kolom Type - polanya meniru bank-in.php.
   Ketiga jenisnya sudah jalan.
   Keterangan lengkap ada di menu-docs.php. */

include 'cash_projection/cfp_lib.php';

/* saringan: Type, From, To */
$jenis      = isset($_REQUEST['jenis'])      && $_REQUEST['jenis']      !== '' ? $_REQUEST['jenis'] : 'ALL';
$start_date = !empty($_REQUEST['start_date']) ? date('Y-m-d', strtotime($_REQUEST['start_date'])) : date('Y-m-01');
$end_date   = !empty($_REQUEST['end_date'])   ? date('Y-m-d', strtotime($_REQUEST['end_date']))   : date('Y-m-t');

/* Rentang tanggal TIDAK dipasang di sini - lihat catatan di query bawah.
   Yang di WHERE cuma jenis, karena satu dokumen pasti satu jenis. */
$start_date = mysqli_real_escape_string($conn1, $start_date);
$end_date   = mysqli_real_escape_string($conn1, $end_date);

$where = '';
if ($jenis !== 'ALL') {
    $where = "where jenis = '" . mysqli_real_escape_string($conn1, $jenis) . "'";
}

$taut_excel = 'ekspor_cash_projection.php?jenis=' . urlencode($jenis)
            . '&start_date=' . urlencode($start_date)
            . '&end_date=' . urlencode($end_date);

/* Pil jenis - tulisannya tetap apa adanya, warnanya hanya membantu memindai. */
function cfp_pil_jenis($jenis)
{
    $kelas = array('BOOK TRANSFER' => 'bt', 'OTHER INFLOW' => 'oi', 'PV EXPENSE' => 'pv');
    $j = strtoupper(trim($jenis));
    return '<span class="cfp-jenis ' . (isset($kelas[$j]) ? $kelas[$j] : 'bt') . '">'
         . htmlspecialchars($j, ENT_QUOTES) . '</span>';
}
function cfp_angka($n)   { return number_format((float) $n, 2, ',', '.'); }
function cfp_tgl($t)     { return ($t === null || $t === '0000-00-00' || $t === '') ? '-' : date('d M Y', strtotime($t)); }
function cfp_waktu($t)   { return ($t === null || $t === '0000-00-00 00:00:00' || $t === '') ? '-' : date('d M Y H:i', strtotime($t)); }
function cfp_isi($v)     { $v = trim((string) $v); return $v === '' ? '-' : htmlspecialchars($v, ENT_QUOTES); }
?>
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">
<link rel="stylesheet" href="../css/app-cfp.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-cfp.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">

  <div class="ftl-card">
    <div class="ftl-head">
      <span class="ftl-head-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
      <div>
        <h1>Projection Other</h1>
        <span class="ftl-crumb">Bank &rsaquo; Report &rsaquo; Projection &rsaquo; Other</span>
      </div>
    </div><!-- /.ftl-head -->

    <div class="ftl-panel">
      <form id="form-data" action="cash-projection.php" method="get">
        <div class="row">
          <!-- Type paling kiri: jenis dokumen menentukan arti kolom
               berikutnya, jadi dibaca lebih dulu daripada rentang tanggal. -->
          <div class="col-12 col-sm-6 col-lg-3 mb-2">
            <label for="jenis"><b>Type</b></label>
            <select class="form-control app-select2" name="jenis" id="jenis">
              <option value="ALL"<?php           echo $jenis === 'ALL'           ? ' selected="selected"' : ''; ?>>ALL</option>
              <option value="BOOK TRANSFER"<?php echo $jenis === 'BOOK TRANSFER' ? ' selected="selected"' : ''; ?>>Book Transfer</option>
              <option value="OTHER INFLOW"<?php  echo $jenis === 'OTHER INFLOW'  ? ' selected="selected"' : ''; ?>>Other Inflow</option>
              <option value="PV EXPENSE"<?php    echo $jenis === 'PV EXPENSE'    ? ' selected="selected"' : ''; ?>>PV Expense</option>
            </select>
          </div>
          <div class="col-12 col-sm-6 col-lg-2 mb-2">
            <label for="start_date"><b>Date From</b></label>
            <input type="text" class="form-control tanggal" id="start_date" name="start_date" autocomplete="off"
                   value="<?php echo date('d M Y', strtotime($start_date)); ?>">
          </div>
          <div class="col-12 col-sm-6 col-lg-2 mb-2">
            <label for="end_date"><b>To</b></label>
            <input type="text" class="form-control tanggal" id="end_date" name="end_date" autocomplete="off"
                   value="<?php echo date('d M Y', strtotime($end_date)); ?>">
          </div>
          <div class="col-12 col-lg-5 mb-2 ftl-actions">
            <button type="submit" class="app-btn app-btn-primary app-btn-sm">
              <i class="fa fa-search" aria-hidden="true"></i> Search
            </button>
            <!-- SATU TOMBOL, jenisnya dipilih di kotak sesudahnya (permintaan
                 pemakai 20 Nov 2026: "biar rapi satu button saja, nanti ada
                 swal pilihannya"). Sejak Daily ikut ada, tiga tombol
                 berjajar membuat baris saringan ini penuh dan yang dicari
                 pemakai - Search - tenggelam di antaranya. Formulirnya SAMA
                 PERSIS untuk ketiganya; yang berbeda cuma nilai type_report
                 yang ikut tersimpan. -->
            <button type="button" id="btncreate"
                    class="app-btn app-btn-success app-btn-sm">
              <i class="fa fa-plus-circle" aria-hidden="true"></i> Create
            </button>
            <a target="_blank" href="<?php echo htmlspecialchars($taut_excel, ENT_QUOTES); ?>"
               class="app-btn app-btn-excel app-btn-sm">
              <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
            </a>
          </div>
        </div>
      </form>
    </div><!-- /.ftl-panel -->

    <div class="ftl-body">
      <form id="formdata2">
        <div class="ftl-tblwrap cfp-tblwrap">
          <table id="datatable" class="table ftl-tbl cfp-tbl" role="grid" cellspacing="0" style="width:100%;">
            <thead>
              <tr>
                <th>No Doc</th>
                <th>Date</th>
                <th>Type</th>
                <th>Report</th>
                <th class="num">Total Amount (IDR)</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Created Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            <?php
            /* SATU BARIS = SATU DOKUMEN. Rinciannya dibuka lewat modal,
               jadi yang di sini cukup ringkasannya saja.
               Saringan tanggal dipasang di HAVING, bukan WHERE: kalau
               dipasang di WHERE, dokumen yang barisnya melewati batas
               tanggal cuma terjumlah sebagian dan totalnya jadi beda
               dgn isi modalnya. */
            $sql = mysqli_query($conn1, "select no_doc, jenis,
                                                max(type_report) as type_report,
                                                min(tanggal)   as tgl_awal,
                                                max(tanggal)   as tgl_akhir,
                                                count(*)       as jml_baris,
                                                sum(amount_idr) as total_idr,
                                                max(status)       as status,
                                                max(created_by)   as created_by,
                                                max(created_date) as created_date
                                         from ap_cf_projection
                                         $where
                                         group by no_doc, jenis
                                         having min(tanggal) <= '$end_date'
                                            and max(tanggal) >= '$start_date'
                                         order by jenis asc, no_doc asc");
            while ($row = mysqli_fetch_assoc($sql)) {
                $batal = (strcasecmp($row['status'], 'Cancel') === 0);

                // tanggalnya ditulis sebagai rentang kalau barisnya tidak satu tanggal
                $tgl = cfp_tgl($row['tgl_awal']);
                if ($row['tgl_akhir'] != $row['tgl_awal']) {
                    $tgl .= ' - ' . cfp_tgl($row['tgl_akhir']);
                }

                echo '<tr' . ($batal ? ' class="batal"' : '') . '>
                <td><a href="javascript:void(0);" class="js-cfp-detail"
                       data-doc="' . htmlspecialchars($row['no_doc'], ENT_QUOTES) . '">'
                    . cfp_isi($row['no_doc']) . '</a></td>
                <td>' . $tgl . '</td>
                <td>' . cfp_pil_jenis($row['jenis']) . '</td>
                <td><span class="cfp-rep">' . cfp_isi($row['type_report']) . '</span></td>
                <td class="num">' . cfp_angka($row['total_idr']) . '</td>
                <td><span class="ftl-st ' . ($batal ? 'is-cancel' : 'is-approved') . '">'
                    . cfp_isi($row['status']) . '</span></td>
                <td class="jejak">' . cfp_isi($row['created_by']) . '</td>
                <td class="jejak">' . cfp_waktu($row['created_date']) . '</td>';

                /* Tombolnya membawa nomor dokumennya sendiri lewat data-doc.
                   TIDAK memakai td:eq(n) - nomor urut sel gampang meleset
                   begitu ada kolom disisipkan. */
                echo '<td class="ftl-act-cell">';
                if ($batal) {
                    echo '<span class="text-danger font-weight-bold" style="font-size:11.5px;">Cancelled</span>';
                } else {
                    echo '<div class="ftl-act">'
                       . '<a href="edit-cash-projection.php?no_doc=' . urlencode($row['no_doc']) . '"'
                       . ' class="app-btn app-btn-primary app-btn-sm">'
                       . '<i class="fa fa-pencil" aria-hidden="true"></i> Edit</a>'
                       . '<button type="button" class="app-btn app-btn-danger app-btn-sm js-cfp-cancel"'
                       . ' data-doc="' . htmlspecialchars($row['no_doc'], ENT_QUOTES) . '">'
                       . '<i class="fa fa-ban" aria-hidden="true"></i> Cancel</button>'
                       . '</div>';
                }
                echo '</td></tr>';
            }
            ?>
            </tbody>
          </table>
        </div><!-- /.ftl-tblwrap -->
      </form>
    </div><!-- /.ftl-body -->
  </div><!-- /.ftl-card -->

  <!-- Rincian dokumen. Isinya diambil lewat AJAX waktu No Doc diklik,
       jadi halaman daftarnya tetap ringan walau dokumennya banyak.
       ftl-modal + is-wide = kosakata modal yang sudah dipakai halaman lain
       (kepala biru, lebar 96vw, isinya yang menggulir). is-titlefirst
       dipakai karena di sini judulnya ditulis sebelum tombol tutup. -->
  <div class="modal fade ftl-modal is-wide is-titlefirst" id="modalDetail" tabindex="-1" role="dialog">
    <!-- modal-xl WAJIB ikut ditulis: aturan lebar 780px di app-ftr-list.css
         memakai :not(.modal-xl), jadi tanpa kelas itu is-wide kalah. -->
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-file-text-o"></i> Document Detail</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body" id="isiDetail">
          <div class="cfp-detail-memuat">Loading...</div>
        </div>
        <div class="modal-foot">
          <button type="button" class="app-btn app-btn-danger app-btn-sm" data-dismiss="modal">
            <i class="fa fa-times"></i> Close
          </button>
        </div>
      </div>
    </div>
  </div>
      </div>
    </div>
  </div>
</div><!-- /.container-fluid -->

<!-- Bootstrap core JavaScript. header.php TIDAK memuat satu pun pustaka JS,
     jadi tiap halaman memuat miliknya sendiri. -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

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

    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }

    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>

<script>
  $(function () {
    $('.app-select2').each(function () {
      var $s = $(this);
      var o = { width: '100%', dropdownParent: $('body') };
      if ($s.find('option').length < 8) { o.minimumResultsForSearch = Infinity; }
      $s.select2(o);
    });

    /* "12 Nov 2026" - bentuk yang sama dgn List Payment Plan. Dibaca server
       lewat strtotime(), yang tidak bisa salah menebak bulan kalau nama
       bulannya ditulis. */
    $('.tanggal').datepicker({ format: 'dd M yyyy', autoclose: true });

    /* 23 kolom, jadi memang digulir ke samping; No Doc & Action dipaku
       lewat position:sticky di app-cfp.css. */
    /* scrollX dimatikan: kolomnya tinggal 8 dan lebarnya diatur di
       app-cfp.css. Kalau scrollX hidup, DataTables menimpa lebar itu
       dgn hitungannya sendiri dan kolomnya jadi tidak rata. */
    $('#datatable').DataTable({
      autoWidth: false,
      order: [],
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
      /* kolom terakhir = Action; nomornya naik satu sejak kolom Report
         disisipkan. */
      columnDefs: [{ targets: [8], orderable: false }],
      language: { searchPlaceholder: 'Search...', search: '' }
    });
  });

  /* Jenis laporannya dipilih di kotak Swal, bukan lewat tombol berjajar.
     Daftarnya ditulis sekali di sini - menambah jenis keempat nanti cukup
     menambah satu baris, tanpa menyentuh tata letak baris saringan. */
  var CFP_JENIS = [
    { kode: 'weekly',  nama: 'Weekly',  ket: 'Entries for the weekly projection',  warna: 'app-btn-success' },
    { kode: 'monthly', nama: 'Monthly', ket: 'Entries for the monthly projection',   warna: 'app-btn-primary' },
    { kode: 'daily',   nama: 'Daily',   ket: 'Entries for the daily projection',    warna: 'app-btn-dark' }
  ];

  $('#btncreate').on('click', function () {
    var h = '<div class="cfp-pilih">';
    for (var i = 0; i < CFP_JENIS.length; i++) {
      var j = CFP_JENIS[i];
      h += '<button type="button" class="app-btn ' + j.warna + ' cfp-pilih-btn" data-report="'
         + j.kode + '"><span class="nm">' + j.nama + '</span>'
         + '<span class="kt">' + j.ket + '</span></button>';
    }
    h += '</div>';

    Swal.fire({
      title: 'Create Projection Other',
      html: h,
      /* Tombol OK sengaja tidak ada: ketiga pilihan DI DALAM kotaknya sudah
         sekaligus keputusan dan tindakan, jadi tombol OK tidak punya arti. */
      showConfirmButton: false,
      showCancelButton: true,
      cancelButtonText: 'Cancel',
      didOpen: function (el) {
        $(el).find('.cfp-pilih-btn').on('click', function () {
          location.href = 'create-cash-projection.php?report='
                        + encodeURIComponent($(this).data('report'));
        });
      }
    });
  });
</script>

<script>
  /* Cancel - penangannya ditempel ke #formdata2 supaya tetap bekerja sesudah
     DataTables menggambar ulang halaman tabel. Dokumen yang sudah Cancel
     tidak punya tombol ini sama sekali. */
  $('#formdata2').on('click', '.js-cfp-detail', function () {
    var doc = $(this).data('doc');
    $('#isiDetail').html('<div class="cfp-detail-memuat">Loading...</div>');
    $('#modalDetail').modal('show');
    $('#isiDetail').load('detail_cash_projection.php', { no_doc: doc });
  });

  $('#formdata2').on('click', '.js-cfp-cancel', function () {
    var doc = $(this).data('doc');
    Swal.fire({
      title: 'Cancel this document?',
      html: 'Document <b>' + doc + '</b> and all its rows will be set to <b>Cancel</b> and excluded from the projection.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, cancel',
      cancelButtonText: 'No'
    }).then(function (hasil) {
      if (!hasil.isConfirmed) { return; }
      $.post('cancel_cash_projection.php', { no_doc: doc }, function (balasan) {
        if (balasan && balasan.status === 'ok') {
          Swal.fire({ icon: 'success', title: 'Document cancelled', timer: 1200, showConfirmButton: false })
              .then(function () { location.reload(); });
        } else {
          Swal.fire({ icon: 'error', title: 'Failed', text: (balasan && balasan.pesan) ? balasan.pesan : 'Unknown error' });
        }
      }, 'json').fail(function (xhr) {
        Swal.fire({ icon: 'error', title: 'Failed', text: xhr.responseText || 'Cannot reach the server' });
      });
    });
  });
</script>

</body>

</html>
