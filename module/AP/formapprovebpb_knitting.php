<?php include '../header.php' ?>

<!-- Tiap berkas CSS ditaut sendiri-sendiri dgn penanda versi dari
     filemtime. Kosakata .ftl- dipakai bersama daftar FTR CBD/DP dan
     Petty Cash Out - bentuknya sama dgn List Memorial Journal. -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="ftl-card">
    <div class="ftl-head">
      <span class="ftl-head-icon"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>
      <div>
        <h1>Approve BPB Knitting</h1>
        <span class="ftl-crumb">AP &rsaquo; Approve BPB Knitting</span>
      </div>
    </div><!-- /.ftl-head -->

    <div class="ftl-panel">
<form id="form-data" action="formapprovebpb_knitting.php" method="post">
        <div class="form-row">
            <div class="col-12 col-sm-6 col-xl-4 mb-2">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()" data-container="body">
                <option value="ALL" <?php
                $nama_supp = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                    if($nama_supp == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>                                 
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

           
        </div>
</form>
    </div><!-- /.ftl-panel -->
  </div><!-- /.ftl-card: kartu filter -->

  <div class="ftl-card mt-3">
    <div class="ftl-body">
      <!-- Kotak cari buatan sendiri (#myInput + myFunction) DIBUANG.
           Ia menyapu <tr> di DOM, padahal DataTables hanya merender baris
           halaman aktif - jadi yang tersaring cuma halaman yang sedang
           dibuka. Pencarian bawaan DataTables menyaring seluruh data. -->
      <div class="ftl-tblwrap">
            <table id="mytable" class="table ftl-tbl" cellspacing="0" width="100%">
                    <thead>
                        <tr class="thead-dark">
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>                        
                            <th style="width:50px;">No BPB</th>
                            <th style="width:40px;">BPB Date</th>
                            <th style="width:40px;">Verification Date</th>
                            <th style="width:50px;">No PO</th>                                                                                
                            <th style="width:100px;">Supplier</th>
                            <th style="width:30px;">TOP</th>
                            <th style="width:100px;display: none;">Currency</th>
                            <th style="width:100px;display: none;">Confirm</th>
                            <th style="width:100px;display: none;">Currency</th>
                            <th style="width:100px;display: none;">Confirm</th>                                                                                                                
                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
           
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            
            }

            if(empty($nama_supp) or $nama_supp == 'ALL'){
            $sql = mysqli_query($conn2,"select id, no_bpb, tgl_bpb, create_date, pono, supplier, top, curr, confirm1, tgl_po, SUM((qty * price) + ((qty * price) * (tax /100))) as total from bpb_new where profit_center = 'NAK' and confirm2 = '' and status != 'Cancel' group by no_bpb");                
            }else {
            $sql = mysqli_query($conn2,"select id, no_bpb, tgl_bpb, create_date, pono, supplier, top, curr, confirm1, tgl_po, SUM((qty * price) + ((qty * price) * (tax /100))) as total from bpb_new where profit_center = 'NAK' and supplier = '$nama_supp' and confirm2 = '' and status != 'Cancel' group by no_bpb");
            }
                                                                         
            while($row = mysqli_fetch_array($sql)){                                          
                    echo'<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                            <td style="width:100px;" value="'.$row['create_date'].'">'.date("d-M-Y",strtotime($row['create_date'])).'</td>
                            <td style="width:100px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                                                                                                        
                            <td style="width:50px;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="width:50px;" value="'.$row['top'].'">'.$row['top'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['tgl_po'].'">'.$row['tgl_po'].'</td>
                            <td style="display: none;" value="'.$row['total'].'">'.$row['total'].'</td>                            
                        </tr>';                
                   
                    } ?>
                    </tbody>
                    </table>
      </div><!-- /.ftl-tblwrap -->

<div class="modal fade ftl-modal" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_bpb"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>          
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>                            
                    
      <form id="form-simpan" class="ftl-actions mt-3">
        <button type="button" class="app-btn app-btn-primary app-btn-sm" name="approve" id="approve"><i class="fa fa-thumbs-up" aria-hidden="true"></i> Approve</button>
        <button type="button" class="app-btn app-btn-danger app-btn-sm" name="cancel" id="cancel"><i class="fa fa-ban" aria-hidden="true"></i> Cancel</button>
      </form>
    </div><!-- /.ftl-body -->
  </div><!-- /.ftl-card: kartu tabel -->
</div><!-- /.container-fluid -->

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
  <!-- SweetAlert2 belum pernah dimuat halaman ini; dipakai utk konfirmasi
       dan pesan hasil Approve/Cancel. -->
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
    /* Dideklarasikan DI LUAR blok siap-dokumen supaya tombol Approve, Cancel,
       dan centang "pilih semua" di blok <script> lain bisa memakainya - tanpa
       ini ia cuma jadi variabel global tersirat. */
    var tabelBpb;

    $(document).ready(function() {
    /* Pencarian bawaan DataTables DIHIDUPKAN (dulu "bFilter": false).
       Ia menyaring SELURUH data - bukan cuma baris halaman aktif - dan
       ikut memperbarui jumlah entri serta penomoran halamannya.

       Variabelnya disimpan karena tombol Approve/Cancel dan centang
       "pilih semua" perlu API-nya untuk menjangkau baris di halaman lain
       yang TIDAK ADA di DOM. */
    tabelBpb = $('#mytable').DataTable({
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        columnDefs: [
            { targets: [0], orderable: false, searchable: false, className: 'text-center' },
            { targets: [1], className: 'text-left ftl-doc' },
            { targets: [5], className: 'text-left' }
        ],
        language: {
            search: 'Search:',
            emptyTable: 'No BPB waiting for approval.',
            zeroRecords: 'No BPB matches your search.'
        }
    });
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<!-- Blok myFunction() dibuang: lihat keterangan di dekat tabel. -->

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
    /* Didelegasikan ke #mytable tbody, bukan diikat ke baris yang kebetulan ada
       saat halaman dimuat: DataTables membuat ulang barisnya setiap kali
       berpindah halaman atau mencari, jadi ikatan lama ikut hilang. */
    $('#mytable tbody').on('click', 'td:nth-child(2)', function(){                
    $('#mymodal').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(4)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(5)').attr('value');
    var top = $(this).closest('tr').find('td:eq(6)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(8)').attr('value');

    $.ajax({
    type : 'post',
    url : 'ajaxbpb.php',
    data : {'no_bpb': no_bpb},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_bpb);
    $('#txt_tglbpb').html('Tgl BPB : ' + tgl_bpb + '');
    $('#txt_no_po').html('No PO : ' + no_po + '');
    $('#txt_supp').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By : ' + confirm + '');                
});

</script>

<!--<script type="text/javascript"> 
    $("#mytable").on("click", "#delbutton", function() {
    var sub = $(this).closest('tr').find('td:eq(4)').attr('data-subtotal');
    var pajak = $(this).closest('tr').find('td:eq(5)').attr('data-tax');
    var total = $(this).closest('tr').find('td:eq(6)').attr('data-total');        
    var sub_val = document.getElementById("subtotal").value.replace(/[^0-9.]/g, '');
    var sub_tax = document.getElementById("pajak").value.replace(/[^0-9.]/g, '');
    var sub_total = document.getElementById("total").value.replace(/[^0-9.]/g, '');
    var min_sub = 0;
    var min_tax = 0;
    var min_total = 0;
    min_sub = sub_val - sub;
    min_tax = sub_tax - pajak;
    min_total = sub_total - total;
    $('#subtotal').val(formatMoney(min_sub));
    $('#pajak').val(formatMoney(min_tax));
    $('#total').val(formatMoney(min_total));                      
    $(this).closest("tr").remove();

});
</script>-->

<script type="text/javascript">
function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
  try {
    decimalCount = Math.abs(decimalCount);
    decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

    const negativeSign = amount < 0 ? "-" : "";

    let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
    let j = (i.length > 3) ? i.length % 3 : 0;

    return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
  } catch (e) {
    console.log(e)
  }
};
    /* Penghitung subtotal/pajak/total DIBUANG - salinan dari halaman lain:
       isian tujuannya (#subtotal, #pajak, #total) tidak ada di halaman ini,
       dan indeks kolom yang dibacanya tidak cocok dgn tabel ini (td:eq(5)
       di sini Supplier, bukan harga). Pemilih centangnya juga tidak dibatasi
       ke tabel sehingga ikut terpicu centang "pilih semua". */        
</script>

<!--<script type="text/javascript">
$(document).ready(function(){
    $("#supp").on("change", function(){
        var supp= $('select[name=supp] option').filter(':selected').val();
        $.ajax({
            type:'POST',
            url:'cek.php',
            data: {'supp':supp},
            close: function(e){
                e.preventDefault();
            },
            success: function(html){
                console.log(html);
                $("#no_po").html(html);
            },
            error:  function (xhr, ajaxOptions, thrownError) {
                alert(xhr);
            }
        });            
        });
    });    
</script>-->

<script type="text/javascript">
    /* Baris yang tercentang diambil lewat API DataTables, BUKAN dari DOM.
       Saat tabel memakai halaman, baris halaman lain tidak dirender sama
       sekali - jadi $('input:checked') hanya melihat halaman yang terbuka.
       rows().nodes() mencakup semuanya.

       Dibatasi ke input bernama select[] supaya centang "pilih semua" di
       kepala tabel tidak ikut terhitung; dulu ikut, dan mengirim satu
       permintaan tambahan dgn no_bpb kosong. */
    function barisTercentang() {
        var hasil = [];
        tabelBpb.rows().nodes().each(function (tr) {
            var c = $(tr).find('input[name="select[]"]');
            if (c.length && c.prop('checked')) { hasil.push($(tr)); }
        });
        return hasil;
    }

    function nilaiKolom($tr, i) {
        return $tr.find('td:eq(' + i + ')').attr('value');
    }

    /* Permintaan dikumpulkan dulu, lalu DITUNGGU semuanya dgn $.when baru
       halaman berpindah. Sebelumnya tiap permintaan langsung memindahkan
       halaman pada yang pertama selesai, sehingga sisanya bisa terputus. */
    function kirimBanyak(url, daftar, kataKerja, pesanSukses) {
        if (!daftar.length) {
            Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Silahkan ceklist No BPB dahulu.' });
            return;
        }

        /* Jumlah dokumen disebut di pertanyaannya: Approve/Cancel di sini bisa
           mengenai banyak baris sekaligus - termasuk baris di halaman lain yang
           tidak terlihat - jadi angkanya penting sebelum menekan Yes. */
        Swal.fire({
            icon: 'question',
            title: 'Are you sure?',
            text: kataKerja + ' ' + daftar.length + ' BPB?',
            showCancelButton: true,
            confirmButtonText: 'Yes, ' + kataKerja.toLowerCase() + ' it!',
            cancelButtonText: 'Cancel'
        }).then(function (hasil) {
            if (!hasil.isConfirmed) { return; }

            $('#approve, #cancel').prop('disabled', true);
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                didOpen: function () { Swal.showLoading(); }
            });

            var janji = daftar.map(function (d) {
                return $.ajax({ type: 'POST', url: url, data: d });
            });
            $.when.apply($, janji).done(function () {
                Swal.fire({ icon: 'success', title: 'Success', text: pesanSukses })
                    .then(function () { window.location = 'formapprovebpb_knitting.php'; });
            }).fail(function (xhr) {
                $('#approve, #cancel').prop('disabled', false);
                Swal.fire({
                    icon: 'error', title: 'Failed',
                    text: 'HTTP ' + (xhr && xhr.status ? xhr.status : '?') + ' - ' + (xhr && xhr.responseText ? xhr.responseText : 'no response')
                });
            });
        });
    }

    $("#form-simpan").on("click", "#approve", function () {
        var pengguna = '<?php echo $user ?>';
        var daftar = barisTercentang().map(function ($tr) {
            return {
                no_bpb:       nilaiKolom($tr, 1),
                approve_user: pengguna,
                update_user:  pengguna,
                curr:         nilaiKolom($tr, 6),
                pono:         nilaiKolom($tr, 4),
                tgl_bpb:      nilaiKolom($tr, 2),
                tgl_po:       nilaiKolom($tr, 8),
                supp:         nilaiKolom($tr, 5),
                total:        nilaiKolom($tr, 9)
            };
        });
        kirimBanyak('approvebpb.php', daftar, 'Approve', 'Data berhasil di-approve.');
    });
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#cancel", function () {
        var pengguna = '<?php echo $user ?>';
        var daftar = barisTercentang().map(function ($tr) {
            return { no_bpb: nilaiKolom($tr, 1), update_user: pengguna };
        });
        kirimBanyak('cancelbpb.php', daftar, 'Cancel', 'Data berhasil di-cancel.');
    });
</script>

<script type="text/javascript">
/* Dulu $(':checkbox') - mencentang SEMUA kotak centang di halaman, termasuk
   yang di luar tabel, dan hanya yang sedang terlihat. Sekarang lewat API
   DataTables supaya baris di halaman lain ikut, dan dibatasi pada baris
   yang SEDANG TERSARING - jadi "pilih semua" setelah mencari benar-benar
   berarti "semua hasil pencarian". */
$("#select_all").click(function () {
  var c = this.checked;
  tabelBpb.rows({ search: 'applied' }).nodes().each(function (tr) {
    $(tr).find('input[name="select[]"]').prop('checked', c);
  });
});

/* Centang kepala dilepas kalau ada baris yang dilepas satu per satu. */
$('#mytable tbody').on('change', 'input[name="select[]"]', function () {
  if (!this.checked) { $('#select_all').prop('checked', false); }
});
</script>

<!--<script>
    $(document).ready(){
        $('#mybpb').click(function){
            $('#mymodal').modal('show');
        }
    }
</script>-->
<!--<script>
$(document).ready(function() {   
    $("#send").click(function(e) {
        e.preventDefault();
        var datas= $(this).children("option:selected").val();
        $.ajax({
            type:"post",
            url:"cek.php",
            dataType: "json",
            data: {datas:datas},
            success: function(data){
                alert("Success: " + data);
            }
        });               
    });
</script>-->
<!--<script>
$(document).ready(function (){
    $("select.selectpicker").change(function(){
        var selectedbpb = $(this).children("option:selected").val();
        document.getElementById("bpbvalue").value = selectedbpb;             
    });
});
</script>-->
<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
