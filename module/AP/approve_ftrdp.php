<?php
/* ============================================================================
   Approve FTR DP - halaman persetujuan, TERPISAH dari daftarnya.

   Dulu tombol Approve menempel di halaman daftar (ftrdp.php) sehingga
   menyetujui dan mengelola dokumen bercampur di satu layar. Sekarang
   persetujuan punya menunya sendiri - seperti menu Approval lain di
   aplikasi ini - dan daftarnya hanya menyisakan Cancel / Edit / Pdf.

   Isinya HANYA dokumen berstatus draft; penyaringnya dipaku di
   ajx_approve_ftrdp.php, bukan di tampilan.
   ============================================================================ */
include '../header.php';
?>

<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">

<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="ftl-card">
    <div class="ftl-head">
      <span class="ftl-head-icon"><i class="fa fa-exchange" aria-hidden="true"></i></span>
      <div>
        <h1>Approve FTR DP</h1>
        <span class="ftl-crumb">AP &rsaquo; FTR &rsaquo; Approval &rsaquo; FTR DP</span>
      </div>
    </div><!-- /.ftl-head -->

    <div class="ftl-panel">


            <form id="form-data" action="ftrdp.php" method="post">        
                <div class="form-row">
                    <div class="col-12 col-sm-6 col-xl-3 mb-2">
                        <label for="nama_supp"><b>Supplier</b></label>            
                        <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                            <option value="ALL" selected="true">ALL</option>                                                
                            <?php
                            $nama_supp ='';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                            }                 
                            $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['Supplier'];
                                if($row['Supplier'] == $_POST['nama_supp']){
                                    $isSelected = ' selected="selected"';
                                }else{
                                    $isSelected = '';
                                }
                                echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            }?>
                        </select>

                    </div>
                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="status"><b>Status</b></label>            
                        <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true">
                            <option value="ALL" <?php
                            $status = '';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $status = isset($_POST['status']) ? $_POST['status']: null;
                            }                 
                            if($status == 'ALL'){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo $isSelected;
                            ?>                
                            >ALL</option>
                            <option value="draft" <?php
                            $status = '';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $status = isset($_POST['status']) ? $_POST['status']: null;
                            }                 
                            if($status == 'draft'){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo $isSelected;
                            ?>
                            >Draft</option>
                            <option value="Approved" <?php
                            $status = '';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $status = isset($_POST['status']) ? $_POST['status']: null;
                            }                 
                            if($status == 'Approved'){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo $isSelected;
                            ?>
                            >Approved</option>
                            <option value="Cancel" <?php
                            $status = '';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $status = isset($_POST['status']) ? $_POST['status']: null;
                            }                 
                            if($status == 'Cancel'){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo $isSelected;
                            ?>
                            >Cancel</option>                                                                                                             
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                      <label for="start_date"><b>From</b></label>
                      <input type="text" class="form-control tanggal" id="start_date" name="start_date" 
                      value="<?php
                      $start_date ='';
                      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                       $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                   }
                   if(!empty($_POST['start_date'])) {
                       echo $_POST['start_date'];
                   }
                   else{
                       echo date("d-m-Y");
                   } ?>" 
                   autocomplete="off">
               </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                 <label for="end_date"><b>To</b></label>        
                 <input type="text" class="form-control tanggal" id="end_date" name="end_date" 
                 value="<?php
                 $end_date ='';
                 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                   $end_date = date("Y-m-d",strtotime($_POST['end_date']));
               }
               if(!empty($_POST['end_date'])) {
                   echo $_POST['end_date'];
               }
               else{
                   echo date("d-m-Y");
               } ?>" 
               autocomplete="off">
           </div>

           <div class="col-12 col-sm-6 col-xl-3 mb-2 ftl-actions">
            <button type="submit" id="submit" class="app-btn app-btn-primary app-btn-sm"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
<?php /* Tombol Create sengaja TIDAK ada di halaman persetujuan. */ ?>
        </div>                                                            
    </div>
</div>
</form>
</div><!-- /.ftl-card: kartu filter -->
<div class="ftl-card mt-3">
<div class="ftl-body">
    <div class="row">       
        <div class="col-md-12">

          <!-- .app-loading-wrap: area yang ditutup overlay saat data ditarik.
               Markup & kelasnya milik css/app-loading.css (dipakai bersama
               halaman lain), jadi tampilannya seragam antar menu. -->
          <div class="app-loading-wrap" id="ftrdpLoad">
            <div class="app-loading">
              <div class="app-loading-box">
                <div class="app-spinner"></div>
                <div class="app-loading-text">Loading data...</div>
              </div>
            </div>

          <div class="ftl-tblwrap">
            <table id="datatable" class="table ftl-tbl" role="grid" cellspacing="0" width="100%">
                <thead>
                    <tr class="thead-dark">
                        <th style="text-align: center;vertical-align: middle;"></th>
                        <th style="text-align: center;vertical-align: middle;">No FTR DP</th>
                        <th style="text-align: center;vertical-align: middle;width: 100px">FTR DP Date</th>
                        <th style="text-align: center;vertical-align: middle;">Supplier</th>            
                        <th style="text-align: center;vertical-align: middle;">No PO</th>
                        <th style="text-align: center;vertical-align: middle;">Total PO</th>
                        <th style="text-align: center;vertical-align: middle;">DP Amount</th>
                        <th style="text-align: center;vertical-align: middle;">Balance</th>
                        <th style="text-align: center;vertical-align: middle;">Currency</th>
                        <th style="text-align: center;vertical-align: middle;">Create By</th>
                        <th style="text-align: center;vertical-align: middle;">Status</th>
                        <th style="text-align: center;vertical-align: middle;width: 200px">Action</th>

                    </tr>
                </thead>

                <tbody></tbody>
            </table>
        </div>
          </div><!-- /.app-loading-wrap -->

    </div>
</div>
</div><!-- /.ftl-body -->
</div><!-- /.ftl-card: kartu tabel -->
</div><!-- /.container-fluid -->
</div>
</div>

<div class="modal fade ftl-modal" id="mymodalftrdp" data-target="#mymodalftrdp" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_dp"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tgl_dp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>       
                  <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_keterangan" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                                               
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
              </div>
          </div>
      </div> 
      <!-- /.modal-content --> 
      <!--  </div> -->
      <!-- /.modal-dialog --> 
      <!--    </div> -->        

  </div><!-- body-row END -->
</div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>  
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
    var PENGGUNA = '<?php echo $user; ?>';

    /* Dibiarkan di lingkup global supaya blok skrip di bawah (Approve, Cancel,
       modal rincian) bisa membaca data barisnya lewat ftrTable.row(...). */
    var ftrTable = null;


    function muatUlang() {
        if (ftrTable) { ftrTable.ajax.reload(null, false); }
    }

    $(document).ready(function () {
        ftrTable = $('#datatable').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: false,
            order: [[1, 'asc']],   // kolom 0 kini tombol "+"

            /* RESPONSIVE: kolom yang tidak muat DIKOLAPS jadi baris rincian
               yang dibuka lewat tanda "+", bukan dipaksa digulir ke samping.
               Modulnya sudah terbundel di datatables.min.js dan CSS-nya sudah
               dimuat header.php (responsive.bootstrap4.min.css). */
            responsive: {
                details: { type: 'column', target: 0 }
            },

            ajax: {
                url: 'ajx_approve_ftrdp.php',
                type: 'POST',
                data: function (d) {
                    d.nama_supp  = $('#nama_supp').val();
                    d.status     = $('#status').val();
                    d.start_date = $('#start_date').val();
                    d.end_date   = $('#end_date').val();
                    d.user       = PENGGUNA;
                },
                dataSrc: 'data',
                error: function (xhr) {
                    $('#ftrdpLoad').removeClass('is-loading');
                    Swal.fire({
                        icon: 'error', title: 'Failed to load the list',
                        text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response')
                    });
                }
            },

            /* Kolom tanggal & nilai uang dikirim DUA RUPA oleh ajx_ftrdp.php:
               satu untuk ditampilkan, satu untuk diurutkan. Tanpa itu
               "01-Oct-2026" diurutkan sbg teks dan "1,234.56" sbg kalimat. */
            /* responsivePriority: makin KECIL angkanya, makin lama kolom itu
               dipertahankan saat layar menyempit. Urutannya diambil dari cara
               orang membaca daftar ini: nomor dokumen & tombol aksi harus
               selalu ada, lalu status, lalu nilai Total, baru sisanya. */
            columns: [
                { data: null, defaultContent: '', orderable: false, className: 'dtr-control', responsivePriority: 1 },
                { data: 'no_ftr_dp', responsivePriority: 1 },
                { data: { _: 'tgl_urut',   display: 'tgl_tampil' }, responsivePriority: 6 },
                { data: 'supp',  responsivePriority: 5 },
                { data: 'no_po', responsivePriority: 7 },
                { data: { _: 'total_n',   display: 'total' },   responsivePriority: 8 },
                { data: { _: 'dp_n',      display: 'dp' },      responsivePriority: 4 },
                { data: { _: 'balance_n', display: 'balance' }, responsivePriority: 9 },
                { data: 'curr', responsivePriority: 10 },
                { data: 'create_user', responsivePriority: 11 },
                { data: { _: 'status',     display: 'status_html' }, responsivePriority: 3 },
                { data: 'action', orderable: false, searchable: false, responsivePriority: 2 }
            ],

            /* Kelas "all" milik Responsive = kolom ini TIDAK PERNAH dikolaps,
               seberapa pun sempit layarnya. responsivePriority saja tidak cukup:
               itu hanya menentukan URUTAN dibuang. Tiga yang dikunci: nomor
               dokumen, Status, dan Action - sisanya boleh masuk baris rincian.
               Nomor kolom bergeser +1 karena kolom "+" disisipkan di depan. */
            columnDefs: [
                { targets: [1],        className: 'text-left ftl-doc all' },
                { targets: [10],       className: 'text-center all' },
                { targets: [11],       className: 'text-center ftl-act-cell all' },
                { targets: [3, 4],     className: 'text-left' },            // Supplier, No PO
                { targets: [5, 6, 7],  className: 'text-right ftl-amt' },  // Total PO, DP Amount, Balance
                { targets: [2, 8, 9],  className: 'text-center' }          // tanggal, Currency, Create By
            ],

            language: {
                emptyTable: 'No FTR DP found for this filter.',
                zeroRecords: 'No FTR DP matches your search.'
            }
        });

        /* Overlay mengikuti status processing DataTables - kotak "Processing"
           bawaannya otomatis disembunyikan app-loading.css, jadi tidak muncul
           dua-duanya. */
        ftrTable.on('processing.dt', function (e, settings, processing) {
            $('#ftrdpLoad').toggleClass('is-loading', processing);
        });


        /* Tombol Search ada DI DALAM form, jadi cukup satu pengait submit:
           menangani klik tombol DAN tombol Enter di isian filter sekaligus.
           Halaman tidak lagi dimuat ulang - hanya datanya yang ditarik. */
        $('#form-data').on('submit', function (e) {
            e.preventDefault();
            ftrTable.ajax.reload();
        });

        $("[data-toggle=tooltip]").tooltip();
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script type="text/javascript">
    /* Semua pengait di bawah dipasang pada #datatable tbody (delegasi), BUKAN
       pada baris-barisnya langsung: barisnya kini dibuat ulang setiap kali data
       ditarik, jadi pengait yang menempel ke baris akan ikut hilang. */

    /* Satu baris = satu objek data dari ajx_ftrdp.php. Jauh lebih aman
       daripada membaca ulang isi sel: urutan kolom boleh berubah tanpa
       membuat tombol Approve mengirim nomor dokumen yang salah. */
    function dataBaris(el) {
        return ftrTable.row($(el).closest('tr')).data();
    }

    /* Approve & Cancel sama-sama lewat sini. approveftrdp.php dan
       cancelftrdp.php kini menjawab JSON {ok, message}, jadi pesan "berhasil"
       hanya muncul kalau datanya MEMANG berubah - dulu pesan itu selalu
       muncul asal permintaannya terkirim, termasuk saat dokumennya ternyata
       sudah di-approve orang lain. */
    function kirimAksi(opsi) {
        Swal.fire({
            title: 'Working...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: function () { Swal.showLoading(); }
        });

        $.ajax({
            type: 'POST',
            url: opsi.url,
            data: opsi.kirim,
            dataType: 'json'
        }).done(function (jwb) {
            if (!jwb || jwb.ok !== true) {
                Swal.fire({ icon: 'error', title: opsi.judulGagal,
                    text: (jwb && jwb.message) ? jwb.message : 'The server did not confirm the change.' });
                return;
            }
            Swal.fire({
                icon: 'success',
                title: opsi.judulBerhasil,
                html: jwb.message + '<div style="font-size:11.5px;color:#94a3b8;margin-top:6px">'
                    + jwb.rows + (jwb.rows === 1 ? ' PO row updated.' : ' PO rows updated.') + '</div>',
                confirmButtonText: 'OK'
            }).then(muatUlang);
        }).fail(function (xhr) {
            /* Pesan dari server dipakai kalau ada - jauh lebih berguna
               daripada sekadar nomor galat HTTP. */
            var pesan = '';
            try { pesan = (JSON.parse(xhr.responseText) || {}).message || ''; } catch (err) { pesan = ''; }
            Swal.fire({
                icon: 'error',
                title: opsi.judulGagal,
                text: pesan || ('HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response'))
            });
        });
    }

    $('#datatable tbody').on('click', '.ftl-mini.is-approve', function () {
        var d = dataBaris(this);
        if (!d) { return; }

        Swal.fire({
            icon: 'question',
            title: 'Approve this FTR DP?',
            html: '<b>' + d.no_ftr_dp + '</b>',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve',
            cancelButtonText: 'Cancel'
        }).then(function (jawab) {
            if (!jawab.isConfirmed) { return; }
            kirimAksi({
                url: 'approveftrdp.php',
                kirim: { noftrdp: d.no_ftr_dp, confirm_user: PENGGUNA },
                judulGagal: 'Approve failed',
                judulBerhasil: 'Approved'
            });
        });
    });

    $('#datatable tbody').on('click', '.ftl-mini.is-cancel', function () {
        var d = dataBaris(this);
        if (!d) { return; }

        /* Cancel tidak bisa dibatalkan dari menu ini, jadi ditanya dulu. */
        Swal.fire({
            icon: 'warning',
            title: 'Cancel this FTR DP?',
            html: '<b>' + d.no_ftr_dp + '</b><br>This cannot be undone from this menu.',
            showCancelButton: true,
            confirmButtonText: 'Yes, Cancel It',
            cancelButtonText: 'Back'
        }).then(function (jawab) {
            if (!jawab.isConfirmed) { return; }
            kirimAksi({
                url: 'cancelftrdp.php',
                kirim: { noftrdp: d.no_ftr_dp, cancel_user: PENGGUNA },
                judulGagal: 'Cancel failed',
                judulBerhasil: 'Canceled'
            });
        });
    });

    /* SELURUH BARIS membuka rinciannya kalau diklik - sama dgn menu List
       Memorial Journal, dan .ftl-tbl tbody tr sudah diberi cursor:pointer
       sbg petunjuknya. */
    /* Menyorot teks untuk disalin diakhiri peristiwa "click" juga, jadi tanpa
       penjaga di bawah ini setiap kali user mem-blok nomor atau nilai di dalam
       tabel, modalnya ikut terbuka. Dua tanda yang membedakannya:
         1. kursor bergeser lebih dari 4px antara ditekan dan dilepas
         2. masih ada teks tersorot saat tombol dilepas
       Pada klik biasa, peramban membersihkan sorotan di peristiwa mousedown,
       jadi saat click dijalankan sorotannya memang sudah kosong. */
    var tekanX = 0, tekanY = 0;
    $('#datatable tbody').on('mousedown', 'tr', function (e) {
        tekanX = e.clientX;
        tekanY = e.clientY;
    });

    $('#datatable tbody').on('click', 'tr', function (e) {
        if (Math.abs(e.clientX - tekanX) > 4 || Math.abs(e.clientY - tekanY) > 4) { return; }
        var tersorot = window.getSelection ? String(window.getSelection()) : '';
        if (tersorot.length > 0) { return; }

        /* Tiga hal di dalam tbody yang TIDAK boleh membuka modal: tombol "+"
           milik Responsive, baris rincian hasil bukaannya, dan kolom Action
           yang isinya tombol sendiri. */
        if ($(e.target).closest('td.dtr-control').length) { return; }
        if ($(e.target).closest('.ftl-act-cell').length) { return; }
        if ($(this).hasClass('child')) { return; }
        var d = dataBaris(this);
        if (!d) { return; }

        $('#txt_dp').html(d.no_ftr_dp);
        /* Label dan nilai ditulis sbg dua elemen terpisah supaya bisa
           disejajarkan lewat CSS - tidak lagi satu kalimat utuh. */
        function isiInfo(sel, label, nilai, mentah) {
            $(sel).html('<span class="ftl-k">' + label + '</span>'
                      + '<span class="ftl-v">' + (mentah ? nilai : $('<i>').text(nilai == null || nilai === '' ? '-' : nilai).html()) + '</span>');
        }

        isiInfo('#txt_tgl_dp',     'FTR DP Date', d.tgl_tampil);
        isiInfo('#txt_nama_supp',   'Supplier',     d.supp);
        isiInfo('#txt_curr',        'Currency',     d.curr);
        isiInfo('#txt_create_user', 'Created By',   d.create_user);
        isiInfo('#txt_status',      'Status',       d.status_html, true);
        isiInfo('#txt_keterangan',  'Remark',       d.keterangan);
        $('#details').html('');
        $('#mymodalftrdp').modal('show');

        $.ajax({
            type: 'post',
            url: 'ajaxdp.php',
            data: { noftrdp: d.no_ftr_dp },
            success: function (data) { $('#details').html(data); },
            error: function () { $('#details').html('<div style="color:#b3312c">Failed to load the PO detail.</div>'); }
        });
    });
</script>

<script type="text/javascript">
    /* Tombol Create hanya dicetak utk user ber-hak (id menu = 5). Tanpa
       penjaga ini, di user lain barisnya melempar galat dan blok skrip mati. */
    var tombolCreate = document.getElementById('btncreate');
    if (tombolCreate) {
        tombolCreate.onclick = function () { location.href = "formftrdp.php"; };
    }
</script>


<!--
<script>
function alert_cancel() {
  alert("Data Berhasil di Cancel");
  location.reload();
}
function alert_approve() {
  alert("Data Berhasil di Approve");
  location.reload();
}
</script>
-->

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
