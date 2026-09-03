<?php include '../header.php' ?>
<?php
// ============================================================================
// Mode halaman:
//   'bpb' (default)         -> Accept Transfer BPB (semua dokumen NON FG/OUT)
//   'sj'  (Surat Jalan)     -> Accept Transfer Surat Jalan (khusus FG/OUT)
// form_approve_sj.php meng-include file ini dengan $APPROVE_MODE='sj' supaya
// logikanya identik — yang beda hanya filter jenis dokumen, judul, & redirect.
// FG/OUT = Surat Jalan (BPB keluar); tidak pernah ada transfer yang mencampur
// FG/OUT dengan dokumen lain, jadi aman difilter di level no_transfer.
// ============================================================================
$APPROVE_MODE = (isset($APPROVE_MODE) && $APPROVE_MODE === 'sj') ? 'sj' : 'bpb';
$IS_SJ  = ($APPROVE_MODE === 'sj');
$SELF   = $IS_SJ ? 'form_approve_sj.php' : 'form_approve_bpb.php';
$TITLE_PAGE  = $IS_SJ ? 'ACCEPT TRANSFER SURAT JALAN (FG/OUT) FROM WAREHOUSE TO ACCOUNTING' : 'ACCEPT TRANSFER BPB FROM WAREHOUSE TO ACCOUNTING';
$TITLE_MODAL = $IS_SJ ? 'ACCEPT TRANSFER SURAT JALAN' : 'ACCEPT TRANSFER BPB';
// Filter jenis dokumen di ir_trans_bpb — SJ hanya FG/OUT, BPB semua selain FG/OUT.
$DOC_FILTER = $IS_SJ
    ? "and no_transfer in (select no_transfer from ir_trans_bpb where no_bpb like 'FG/OUT/%')"
    : "and no_transfer not in (select no_transfer from ir_trans_bpb where no_bpb like 'FG/OUT/%')";
?>
<!-- DH-SKIN-START -->
<style>
  /* ===== Skin "Document Handover" (inline) — meniru document_handover.php ===== */
  .col.p-4{ background:transparent !important; box-shadow:none !important; }
  /* Netralkan wrapper .box supaya header NEMPEL ke form jadi 1 card */
  .col.p-4 > .box,
  .col.p-4 .box.header,
  .col.p-4 .card-body.table-responsive{
    background:transparent !important; border:0 !important; box-shadow:none !important;
    padding:0 !important; margin:0 !important;
  }

  /* ===== CARD 1 = header judul (atap) + form (body) ===== */
  .col.p-4 > h3.text-center{
    background:linear-gradient(90deg,#191970,#1e90ff); color:#fff;
    text-align:left !important; font-size:18px; font-weight:700; letter-spacing:.02em;
    margin:0 !important; padding:14px 22px; border-radius:14px 14px 0 0;
    display:flex; align-items:center; gap:10px;
  }
  .col.p-4 > h3.text-center .fa{ font-size:16px; opacity:.95; }
  /* ===== Header + tabel + Back = SATU kartu utuh =====
     Sudut atas membulat dari header (h3), sudut bawah dari sini. */
  .col.p-4 .box.body{
    background:#fff !important; border:1px solid #e8edf5 !important; border-top:0 !important;
    border-radius:0 0 14px 14px !important;
    padding:18px 20px !important; box-shadow:0 12px 30px rgba(15,23,42,.07) !important; margin:0 !important;
  }
  .col.p-4 .box.body > .row{ margin:0 !important; }
  .col.p-4 .box.body .col-md-12{ padding:0 !important; }
  #mytable_wrapper{ background:transparent; border:0; box-shadow:none; padding:0; }
  /* Footer (tombol Back) nyatu DI DALAM card tabel, dipisah garis halus di atasnya */
  .col.p-4 .box.footer{
    background:transparent !important; border:0 !important; box-shadow:none !important;
    padding:14px 0 0 !important; margin-top:14px !important; border-top:1px solid #eef2f7 !important;
  }
  .col.p-4 .box.footer .form-row.col{ margin:0 !important; padding:0 !important; }
  .col.p-4 .box.footer .col-md-3{ padding:0 !important; }

  /* Tabel utama -> header biru + hover (seperti Document Handover) */
  #mytable{ border-collapse:separate !important; border-spacing:0; }
  #mytable thead tr.bg-dark, #mytable thead tr.thead-dark{ background:transparent !important; }
  #mytable thead th{ background:#1E3A8A !important; color:#fff !important; font-weight:600; letter-spacing:.02em; border:0 !important; vertical-align:middle; padding:10px 12px; }
  #mytable tbody td{ border-color:#eef2f7 !important; vertical-align:middle; }
  #mytable tbody tr:hover{ background:#f5f8ff; }

  /* Modal -> header gradient + rounded (seperti modal Document Handover) */
  .modal-header.bg-dark{ background:linear-gradient(135deg,#172554,#2563eb) !important; border:0; }
  .modal-content{ border:0; border-radius:14px; overflow:hidden; box-shadow:0 18px 55px rgba(15,23,42,.25); }
  /* Tabel DI DALAM modal (mis. modal Accept #mytable2) -> header biru juga */
  .modal thead tr.bg-dark, .modal thead tr.thead-dark{ background:transparent !important; }
  .modal thead th{ background:#1E3A8A !important; color:#fff !important; font-weight:600 !important; letter-spacing:.02em; border:0 !important; vertical-align:middle; padding:9px 11px; }
  .modal tbody td{ border-color:#eef2f7 !important; }
  .modal tbody tr:hover{ background:#f5f8ff; }
  /* Perbesar modal (mis. Accept = modal-lg) sedikit */
  .modal-lg{ max-width:920px !important; }

  /* ===== Modal Accept (#modal-approve): diperlebar, tinggi DIBATASI dengan
     scroll internal pada tabel (header tabel tetap menempel), tampilan lebih rapi.
     Catatan: tabel di modal ini BUKAN DataTable (tanpa paging) & tombol Accept
     mengiterasi SEMUA baris di DOM — jadi scroll tidak mengurangi yang di-approve. */
  #modal-approve .modal-dialog{ max-width:1080px !important; margin:1.6rem auto; }
  #modal-approve .modal-content{ max-height:92vh; }
  /* .container bawaan Bootstrap membatasi lebar — paksa penuh mengikuti dialog */
  #modal-approve .container{ max-width:100% !important; width:100% !important; padding:0 16px 14px; }
  /* Info No Document & Document Date = satu KOTAK dgn label kecil (uppercase)
     di atas dan nilainya di bawah, dibagi kiri-kanan. */
  #modal-approve .container > .row{ margin:0; display:flex; flex-wrap:wrap; align-items:stretch; }
  #modal-approve .trf-meta{
    flex:0 0 100%; width:100%;
    display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:6px 32px;
    margin:14px 0 2px; padding:12px 16px;
    background:#f8fafc; border:1px solid #e6ebf3; border-radius:10px;
  }
  #modal-approve .trf-meta > div{ display:flex; flex-direction:column; min-width:0; }
  #modal-approve .tm-lbl{ font-size:10.5px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#94a3b8; margin-bottom:3px; }
  #modal-approve .tm-val{ font-size:14px; font-weight:600; color:#1e293b; }
  /* Area tabel: penuh 1 baris, batasi tinggi + scroll vertikal, header sticky */
  #modal-approve .card-body.table-responsive{
    flex:0 0 100%; width:100%;
    max-height:56vh; overflow:auto !important; margin:10px 0 0; padding:0 !important;
    border:1px solid #e8edf5 !important; border-radius:12px;
  }
  #mytable2{ margin:0 !important; }
  #mytable2 thead th{ position:sticky; top:0; z-index:3; }
  #mytable2 tbody tr:hover{ background:#eef4ff; }
  /* Teks sel tabel rata kiri (kolom checkbox tetap di tengah) */
  #mytable2 th, #mytable2 td{ text-align:left !important; }
  #mytable2 th:first-child, #mytable2 td:first-child{ text-align:center !important; }
  #modal-approve .modal-footer{ border-top:1px solid #eef2f7; padding:12px 20px; }

  /* Tombol Post / Back / Accept lebih modern */
  #simpan,#batal,#approve,#reject,#post,#accept{ border-radius:8px !important; font-weight:600; padding:6px 16px; transition:transform .12s ease, box-shadow .15s ease; }
  #simpan:hover,#batal:hover,#approve:hover,#reject:hover,#post:hover,#accept:hover{ transform:translateY(-1px); box-shadow:0 5px 13px rgba(30,58,138,.2); }
</style>
<script>
  // Ikon judul: sisipkan <i class="fa fa-exchange"> asli (FA5 + v4-shims yg dimuat app).
  document.addEventListener('DOMContentLoaded', function () {
    var h = document.querySelector('.col.p-4 > h3.text-center');
    if (h && !h.querySelector('.fa')) {
      var i = document.createElement('i'); i.className = 'fa fa-exchange';
      h.insertBefore(i, h.firstChild);
    }
  });
</script>
<!-- DH-SKIN-END -->

    <!-- MAIN -->    
    <div class="col p-4">
        <h3 class="text-center"><?= $TITLE_PAGE ?></h3>
<div class="box">
    <!-- Filter Supplier dihapus (tidak terpakai) — tabel selalu tampil semua transfer. -->
    <div class="box body">
        <div class="row">
        
                <!-- <div class="container-1 mr-4 mt-1" style="margin-bottom-">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search no kontrabon..">
                </div> -->
            <div class="card-body table-responsive">
                <!-- <div class="d-flex justify-content-between mr-2 mb-1">
                    <div class="ml-auto">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                    <input type="text"  id="myInput" name="myInput" required autocomplete="off" placeholder="Search No kontrabon.." onkeyup="myFunction()">
                </div> -->
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr class="thead-dark">                      
                            <th style="width:15%;">No Document</th>
                            <th style="width:10%;">Document Date</th> 
                            <th style="display: none;">No Kontrabon</th>
                            <th style="display: none;">Kontrabon Date</th>                            
                            <th style="display: none;">Supplier</th>
                            <th style="width:10%;">User Created </th>
                            <th style="width:15%;">Transfer Date</th>
                            <th style="width:6%;">Action</th>

                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
           
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            
            }

            // if(empty($nama_supp) or $nama_supp == 'ALL'){
            // $sql = mysqli_query($conn2,"select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user,updated_at from ir_invoice_supp_h where status = 'Post Pch To Fin'");                
            // }else {
            // $sql = mysqli_query($conn2,"select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user,updated_at from ir_invoice_supp_h where status = 'Post Pch To Fin' and nama_supp = '$nama_supp'");
            // }

            // $DOC_FILTER memisahkan BPB vs Surat Jalan (FG/OUT) sesuai mode halaman.
            $sql = mysqli_query($conn2,"select no_transfer,tgl_transfer,nama_supp,SUM(total) total,created_at,created_by from ir_trans_bpb where status = 'Transfer' $DOC_FILTER GROUP BY no_transfer");
                                                                         
            while($row = mysqli_fetch_array($sql)){                               
                    echo'<tr>                       
                           <td style="width:50px;" value="'.$row['no_transfer'].'">'.$row['no_transfer'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_transfer'].'">'.date("d-M-Y",strtotime($row['tgl_transfer'])).'</td>
                            <td style="display: none;" value="'.$row['no_transfer'].'">'.$row['no_transfer'].'</td>
                            <td style="display: none;" value="'.$row['tgl_transfer'].'">'.date("d-M-Y",strtotime($row['tgl_transfer'])).'</td>
                            <td style="display: none;" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td class="dt_total" style="width:100px;" value="'.$row['created_by'].'">'.$row['created_by'].'</td>
                            <td style="" value = "'.$row['created_at'].'">'.$row['created_at'].'</td>   
                            <td><a id="showapp" ><button style="border-radius: 6px" type="button" class="btn-xs btn-info"><i class="fa fa-thumbs-up"aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Accept</i></button></a></td>                   
                        </tr>';                
                   
                    } ?>
                    </tbody>
                    </table>
                    </div>
                    </div>

        <!-- Footer (Back) — nyatu di dalam card tabel -->
        <div class="box footer">
            <button type="button" style="border-radius: 6px" class="btn-outline-danger" name="batal" id="batal" onclick="location.href='document_handover.php?type=bpb'"><span class="fa fa-angle-double-left"></span> Back</button>
        </div>

<div class="modal fade" id="mymodalkbon" data-target="#mymodalkbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-dark text-white">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_kbon"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_kbon" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>  

    <div class="modal fade" id="modal-approve" data-target="#modal-approve" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-dark text-white">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h5 class="modal-title" id="txt_dp"><?= $TITLE_MODAL ?></h5>
        </div>
        <div class="container">
        <div class="row">
          <div class="trf-meta">
            <div><span class="tm-lbl">No Document</span><span class="tm-val" id="txt_notrf">-</span></div>
            <div><span class="tm-lbl">Document Date</span><span class="tm-val" id="txt_tgl_trf">-</span></div>
          </div>
          <div class="card-body table-responsive">
                <!-- <div class="d-flex justify-content-between mr-2 mb-1">
                    <div class="ml-auto">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                    <input type="text"  id="myInput" name="myInput" required autocomplete="off" placeholder="Search No kontrabon.." onkeyup="myFunction()">
                </div> -->
            <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr class="thead-dark">
                            <th style="width:5%"><input type="checkbox" id="select_all"></th>                        
                            <th style="display: none;">No Document</th>
                            <th style="display: none;">Document Date</th> 
                            <th style="width:15%;">No BBP</th>
                            <th style="width:10%;">BPB Date</th>                            
                            <th style="width:25%;">Supplier</th>
                            <th style="width:15%;">Created By</th>
                            <th style="width:20%;">Transfer Date</th>

                        </tr>
                    </thead>

            <tbody id="data_invoice">
            
            </tbody>
            </table> 
            </div>      
        </div>
        </div>
        <div class="modal-footer">
            <form id="form-simpan">
        <button style="border-radius: 5px" type="button" class="btn-outline-primary btn-sm" name="approve" id="approve"><span class="fa fa-thumbs-up"></span> Accept</button>
    </form>
      </div>
        </div>       
                                
</div><!-- body-row END -->
</div>
</div>
                     
                    
</div><!-- body-row END -->
</div>

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>

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
    $(document).ready(function() {
    $('#mytable').dataTable({
    });
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#showapp", function(){                 
        var notrf = $(this).closest('tr').find('td:eq(0)').attr('value');
        var tgl_trf = $(this).closest('tr').find('td:eq(1)').attr('value');
        
        $.ajax({
            type:'POST',
            url:'cari_data_bpb.php',
            data: {'notrf':notrf},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false; 
            },
            success: function(data){
                $('#data_invoice').html(data);
               
                // alert(data);  
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        }); 

        $('#txt_notrf').text(notrf);
        $('#txt_tgl_trf').text(tgl_trf);
        $('#modal-approve').modal('show');
    });
</script>   

<script>
function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("mytable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
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
    $('table tbody tr').on('click', 'td:eq(2)', function(){                
    $('#mymodalkbon').modal('show');
    var no_document = $(this).closest('tr').find('td:eq(2)').attr('value'); 
    var tgl_doc = $(this).closest('tr').find('td:eq(3)').attr('value'); 
    var supp = $(this).closest('tr').find('td:eq(4)').attr('value');                 

    $.ajax({
    type : 'post',
    url : 'ajax_suppinv.php',
    data : {'no_document': no_document},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_document);
    $('#txt_tgl_kbon').html('Invoice Date : ' + tgl_doc + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');                            
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
    $("input[type=checkbox]").change(function(){
    var sub = 0;
    var tax = 0;
    var total = 0;
    var ceklist = 0;         
    $("input[type=checkbox]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(5)').attr('value'),10) || 0;
    var qty = parseFloat($(this).closest('tr').find('td:eq(7)').attr('value'),10) ||0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(8)').attr('value'),10) ||0;               
    sub += price * qty;
    tax += tax;
    total = sub + tax;     
    });
    $("#subtotal").val(formatMoney(sub));
    $("#pajak").val(formatMoney(tax));
    $("#total").val(formatMoney(total));
    $("#select").val("1");                    
});        
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
    $("#form-simpan").on("click", "#approve", function(){
        $("input[name='select[]']:checked").each(function () {                
        var no_dok = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(3)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approve_whstoacc.php',
            data: {'no_dok':no_dok, 'no_bpb':no_bpb, 'approve_user':approve_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = '<?= $SELF ?>';
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        
            alert("Data Berhasil Di Approve");
               
    });
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#approve", function(){
        $("input[name='select[]']:not(:checked)").each(function () {                     
        var no_dok = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(3)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'cancel_whstoacc.php',
            data: {'no_dok':no_dok, 'no_bpb':no_bpb, 'approve_user':approve_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = '<?= $SELF ?>';                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        // if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
        //     alert("Data Berhasil Di Cancel");
        // }else{
        //     alert("Silahkan Ceklist No Kontrabon");
        // }        
    });
</script>

<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>
  
</body>

</html>
