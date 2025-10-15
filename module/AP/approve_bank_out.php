<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
    }

    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }
    div.dataTables_wrapper .dataTables_paginate {
        float: right;
        margin-top: 10px;
    }
    div.dataTables_wrapper .dataTables_info {
        float: left;
        margin-top: 10px;
    }

    .select2-container {
      width: 100% !important;
      max-width: 100% !important;
      min-width: 100% !important;
  }

  .select2-container--bootstrap4 .select2-selection {
      height: calc(1.5em + .5rem + 2px) !important;  /* sama seperti form-control-sm */
      padding: .25rem .5rem !important;
      font-size: .875rem !important;  /* font kecil */
      line-height: 1.5 !important;
      border-radius: .2rem !important;
  }

  .select2-container--bootstrap4 .select2-selection__rendered {
      line-height: 1.5 !important;
      font-size: 12px !important;
  }

  .select2-container--bootstrap4 .select2-results__option {
      font-size: 12px !important;
      padding: 4px 8px; /* biar rapat */
  }

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-thumbs-up"></i> APPROVE OUTGOING BANK <span class="badge bg-danger ms-2">
        <i class="fas fa-bell"></i> 
        <?php
        $nama_supplier = 'ALL';
        $reff_doc = 'ALL';
        $where = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_supplier']) && isset($_POST['reff_doc'])) {
    $nama_supplier = $_POST['nama_supplier'];
    $reff_doc = $_POST['reff_doc'];
} 
elseif (isset($_GET['nama_supplier']) && isset($_GET['reff_doc'])) {
    $nama_supplier = $_GET['nama_supplier'];
    $reff_doc = $_GET['reff_doc'];
}

        if($nama_supplier == 'ALL' && $reff_doc == 'ALL'){
            $where = '';               
        }elseif($nama_supplier != 'ALL' && $reff_doc == 'ALL') {
            $where = "where nama_supp = '$nama_supplier'";
        }elseif($nama_supplier == 'ALL' && $reff_doc != 'ALL') {
            $where = "where reff_doc = '$reff_doc'";
        }else {
            $where = "where nama_supp = '$nama_supplier' and reff_doc = '$reff_doc'";
        }
        
        $sql = mysqli_query($conn2,"select count(distinct(no_payment)) as jml from list_payment where status = 'Approved' $where ");
        $row = mysqli_fetch_array($sql);
        $jml = $row['jml'];
        echo $jml;
        ?>

    </span></h5>
</div>

<div class="card-body p-3">
    <form id="formSupplier" action="approve_bank_out.php"method="post">
        <input type="hidden" name="nama_supplier" id="hidden_supplier">
        <div class="row g-3">
          <!-- Supplier -->
          <div class="col-md-3">
            <label for="nama_supplier"><b>Source</b></label>            
            <select class="form-control selectpicker" name="nama_supplier" id="nama_supplier" data-dropup-auto="false" data-live-search="true" onchange="document.getElementById('formSupplier').submit()">
                <option value="ALL" <?= ($nama_supplier == 'ALL') ? 'selected' : '' ?>>ALL</option>
                <?php
                $sql = mysqli_query($conn1,"SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
                while ($row = mysqli_fetch_assoc($sql)) {
                    $data = $row['Supplier'];
                    $isSelected = ($data == $nama_supplier) ? 'selected' : '';
                    if ($data != '') {
                    echo "<option value=\"$data\" $isSelected>$data</option>";
                    }
                }
                ?>

            </select>
        </div>

        <div class="col-md-2">
            <label for="reff_doc"><b>Reff Document</b></label>            
            <select class="form-control selectpicker" name="reff_doc" id="reff_doc" data-dropup-auto="false" data-live-search="true" onchange="document.getElementById('formSupplier').submit()">
                <option value="ALL" <?= ($reff_doc == 'ALL') ? 'selected' : '' ?>>ALL</option>
                <?php
                $sql = mysqli_query($conn1,"select IF(reff_doc = 'Payment','List Payment',reff_doc) as reff_doc from (select DISTINCT reff_doc from b_bankout_h) a ");
                while ($row = mysqli_fetch_assoc($sql)) {
                    $data = $row['reff_doc'];
                    $isSelected = ($data == $reff_doc) ? 'selected' : '';
                    if ($data != '') {
                    echo "<option value=\"$data\" $isSelected>$data</option>";
                    }
                }
                ?>

            </select>
        </div>

    </div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
      <div class="table-responsive">
          <table id="mytable" 
          class="table table-striped table-bordered table-hover table-sm nowrap" 
          style="width:100%">
          <thead class="table-gradient text-white">
            <tr>
              <th style="width:5%; text-align: center;"><input type="checkbox" id="select_all"></th>                 
              <th style="width:100px;">No Bank Out</th>
              <th style="width:70px;">Reff Doc</th>
              <th style="width:125px;">Source</th>
              <th style="width:70px;">Date</th>
              <th style="width:50px;">Curreny</th>                                                                                
              <th style="width:80px;">Amount</th>
              <th style="width:80px;">Outstanding</th>
              <th style="display: none;">-</th>
              <th style="display: none;">-</th>
              <th style="display: none;">-</th>
              <th style="display: none;">-</th>
              <th style="display: none;">-</th>
          </tr>
      </thead>
      <tbody>
        <?php
        $nama_supplier = 'ALL';
        $reff_doc = 'ALL';
        $where = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_supplier']) && isset($_POST['reff_doc'])) {
    $nama_supplier = $_POST['nama_supplier'];
    $reff_doc = $_POST['reff_doc'];
} 
elseif (isset($_GET['nama_supplier']) && isset($_GET['reff_doc'])) {
    $nama_supplier = $_GET['nama_supplier'];
    $reff_doc = $_GET['reff_doc'];
}


            // echo $nama_supplier;

        if($nama_supplier == 'ALL' && $reff_doc == 'ALL'){
            $where = '';               
        }elseif($nama_supplier != 'ALL' && $reff_doc == 'ALL') {
            $where = "where nama_supp = '$nama_supplier'";
        }elseif($nama_supplier == 'ALL' && $reff_doc != 'ALL') {
            $where = "where reff_doc = '$reff_doc'";
        }else {
            $where = "where nama_supp = '$nama_supplier' and reff_doc = '$reff_doc'";
        }
        $sql = mysqli_query($conn2,"select * from (select id, no_bankout,bankout_date,nama_supp,curr, amount, outstanding,IF(reff_doc = 'Payment','List Payment',reff_doc) as reff_doc, akun, bank,status,IF(deskripsi = '','-',deskripsi) as deskripsi from b_bankout_h where status = 'Draft' group by no_bankout) a $where order by id asc");


        while($row = mysqli_fetch_array($sql)){                                          
                    echo'<tr>
                            <td style="width:10px; text-align: center;"><input type="checkbox" name="select[]" data-id="'.$row['no_bankout'].'"></td>                      
                            <td style="width:50px; text-align : left" value="'.$row['no_bankout'].'">'.$row['no_bankout'].'</td>
                            <td style="width:50px; text-align : left" value="'.$row['reff_doc'].'">'.$row['reff_doc'].'</td>
                            <td style="width:50px; text-align : left" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td> 
                            <td style="width:100px; text-align : center" value="'.date("Y-m-d",strtotime($row['bankout_date'])).'">'.date("Y-m-d",strtotime($row['bankout_date'])).'</td>                                                                                             
                            <td style="width:50px; text-align : center" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px; text-align : right" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
                            <td style="width:50px; text-align : right" value="'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td>   
                            <td style="display: none;" value="'.$row['status'].'">'.$row['status'].'</td>
                            <td style="display: none; text-align : center" value="'.$row['reff_doc'].'">'.$row['reff_doc'].'</td>
                            <td style="display: none; text-align : center" value="'.$row['akun'].'">'.$row['akun'].'</td> 
                            <td style="display: none; text-align : center" value="'.$row['bank'].'">'.$row['bank'].'</td>  
                            <td style="display: none; text-align : center" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>                         
                        </tr>';                
                   
                    }

        // while($row = mysqli_fetch_array($sql)){ 
        //     $curr = isset($row['valuta_bayar']) ? $row['valuta_bayar'] :null;
        //     $no_paymt = isset($row['payment_ftr_id']) ? $row['payment_ftr_id'] :null;
        //     $tgl_tempo = $row['tgl_tempo'];
        //     $tgl_sekarang = date("Y-m-d");

        //     if ($tgl_tempo <= $tgl_sekarang) {
        //         $bg = 'style="background-color: #CD5C5C;color: white;"';
        //         #DC143C
        //         #CD5C5C
        //     }else{
        //         $bg = '';
        //     }

        //     if ($no_paymt == '') {
        //         $nom = '-';
        //         $nom1 = '-';
        //         $method = '-';
        //         $tgl_lunas = '-';
        //         $no_lunas = '-';
        //     }else{
        //         $method = $row['cara_bayar'];
        //         $tgl_lunas = date("d-M-Y",strtotime($row['tgl_pelunasan']));
        //         $no_lunas = $row['payment_ftr_id'];
        //         if ($curr == 'IDR') {
        //             $nom = isset($row['nominal']) ? $row['nominal'] :0;  
        //             $nom1 = number_format($nom,2);  
        //         } elseif($curr == 'USD'){
        //             $nom = isset($row['nominal_fgn']) ? $row['nominal_fgn'] :0;
        //             $nom1 = number_format($nom,2);
        //         }else{
        //             $nom = isset($row['ttl_bayar']) ? $row['ttl_bayar'] :0;
        //             $nom1 = number_format($nom,2);
        //         }     
        //     }                                     
        //     echo'<tr '.$bg.' >                       
        //     <td style="width:10px; text-align: center;"><input type="checkbox" name="select[]" data-id="'.$row['no_payment'].'"></td>
        //     <td style="" value="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
        //     <td style="" value="'.$row['tgl_payment'].'">'.date("Y-m-d",strtotime($row['tgl_payment'])).'</td>
        //     <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>                                                                  
        //     <td style="display:none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
        //     <td style="" value="'.$row['amount'].'">'.$row['curr'].' '.number_format($row['amount'],2).'</td>                                      
        //     <td style="display:none;" value="'.$no_lunas.'">'.$no_lunas.'</td>
        //     <td style=";display:none;" value="'.$tgl_lunas.'">'.$tgl_lunas.'</td>
        //     <td style=";display:none;" value="'.$method.'">'.$method.'</td>
        //     <td style="display: none;" value="'.$row['valuta_bayar'].'">'.$row['valuta_bayar'].'</td>                            
        //     <td style=";display:none;" value="'.$nom.'">'.$row['valuta_bayar'].' '.$nom1.'</td>
        //     <td style="display:none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
        //     <td style="" value="'.$row['tgl_tempo'].'">'.date("Y-m-d",strtotime($row['tgl_tempo'])).'</td>
        //     </tr>';                

        // } 
        ?>
    </tbody>
</table>
</div>
<form id="form-simpan">
    <div class="mt-3">
        <button type="button" name="approve" id="approve" class="btn btn-success">
          <i class="fas fas fa-thumbs-up"></i> Approve
      </button>

      <button type="button" name="cancel" id="cancel" class="btn btn-danger">
          <i class="fa fa-ban"></i> Cancel
      </button>
  </div>
</form>
</div>

</div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modal-show" data-target="#modal-show" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #2563EB;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_bpb"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> 
          <div id="txt_tgl_po" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>                     
                 <!--  <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_keterangan" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->                                                    
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
              </div>
    </div>
</div>
</div>      



<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>  
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Ambil nilai dari localStorage (fallback 'ALL')
    let save_supp = localStorage.getItem("nama_supplier") || "ALL";
    let reffnya   = localStorage.getItem("reff_doc") || "ALL";

    // Set nilai ke select (cek dulu elemen ada)
    const elSupplier = document.getElementById("nama_supplier");
    const elReff     = document.getElementById("reff_doc");

    if (elSupplier) {
        elSupplier.value = save_supp;
        // refresh jika menggunakan bootstrap-select (cek apakah method ada)
        if (typeof $ !== 'undefined' && typeof $(elSupplier).selectpicker === 'function') {
            $(elSupplier).selectpicker('refresh');
        }
        // optional: trigger change jika plugin atau logic membutuhkan event
        // elSupplier.dispatchEvent(new Event('change'));
    }

    if (elReff) {
        // PERBAIKAN: gunakan reffnya (bukan save_supp)
        elReff.value = reffnya;
        if (typeof $ !== 'undefined' && typeof $(elReff).selectpicker === 'function') {
            $(elReff).selectpicker('refresh');
        }
        // elReff.dispatchEvent(new Event('change'));
    }

    // Event saat dropdown berubah (cek elemen ada)
    if (elSupplier) {
        elSupplier.addEventListener("change", function() {
            localStorage.setItem("nama_supplier", this.value);
            // submit form jika memang ingin auto-submit
            const form = document.getElementById("formSupplier");
            if (form) form.submit();
        });
    }

    if (elReff) {
        elReff.addEventListener("change", function() {
            localStorage.setItem("reff_doc", this.value);
            const form = document.getElementById("formSupplier");
            if (form) form.submit();
        });
    }
});
</script>



<script type="text/javascript">
    // Select All
    $("#select_all").on("click", function(){
      $("input[name='select[]']").prop("checked", this.checked);
  });

   // === APPROVE ===
$(document).off("click", "#approve").on("click", "#approve", function (e) {
    e.preventDefault();
    e.stopPropagation();
    handleAction("approve");
});

// === CANCEL ===
$(document).off("click", "#cancel").on("click", "#cancel", function (e) {
    e.preventDefault();
    e.stopPropagation();
    handleAction("cancel");
});

function handleAction(action) {
    let url = action === "approve" ? "update_bank_out.php" : "cancel_bank_out.php";
    let successMsg = action === "approve" ? "Approved" : "Cancelled";

    let selected = $("input[name='select[]']:checked");
    if (selected.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Oops!",
            text: "Please check at least one data before proceeding"
        });
        return;
    }

    let approve_user = "<?php echo $user ?>";
    let total = selected.length;
    let processed = 0;
    let errors = 0;

    let currentSupplier = $("#nama_supplier").val();
    let currentReff = $("#reff_doc").val();

    Swal.fire({
        title: `${successMsg} in progress...`,
        text: `Please wait while processing ${total} data`,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    selected.each(function () {
        let no_pay = $(this).data("id");

        $.ajax({
            type: "POST",
            url: url,
            data: {
                no_pay: no_pay,
                update_user: approve_user,
                nama_supplier: currentSupplier,
                reff_doc: currentReff
            },
            dataType: "text",
            success: function (response) {
                console.log(`${successMsg} Response [${no_pay}]:`, response);
                if (!response.toUpperCase().includes("OK")) errors++;
            },
            error: function (xhr) {
                console.error(`AJAX Error [${no_pay}]:`, xhr.responseText);
                errors++;
            },
            complete: function () {
                processed++;
                // Jika semua AJAX selesai
                if (processed === total) {
                    Swal.close();

                    setTimeout(() => {
                        if (errors === 0) {
                            Swal.fire({
                                icon: "success",
                                title: "Success!",
                                text: `${total} Bank Out successfully ${successMsg.toLowerCase()}.`,
                                allowOutsideClick: false,
                                showConfirmButton: true
                            }).then(() => {
                                const urlReload = `approve_bank_out.php?nama_supplier=${encodeURIComponent(currentSupplier)}&reff_doc=${encodeURIComponent(currentReff)}`;
                                window.location = urlReload;
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Partial Failed!",
                                text: `${total - errors} ${successMsg}, ${errors} failed to ${successMsg.toLowerCase()}.`,
                                allowOutsideClick: false,
                                showConfirmButton: true
                            });
                        }
                    }, 500); // kasih jeda 0.5 detik supaya swal tampil
                }
            }
        });
    });
}

</script>
<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
        $('#modal-show').modal('show');
        var no_ob = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(3)').text();
    var refdoc = $(this).closest('tr').find('td:eq(9)').attr('value');
    var akun = $(this).closest('tr').find('td:eq(10)').attr('value');
    var bank = $(this).closest('tr').find('td:eq(11)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(5)').attr('value');
    var status = $(this).closest('tr').find('td:eq(8)').attr('value');
    var desk = $(this).closest('tr').find('td:eq(12)').text();              

        $.ajax({
            type : 'post',
            url : 'ajaxbankout.php',
    data : {'no_ob': no_ob, 'refdoc': refdoc},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_bpb').html(no_ob);
    $('#txt_tglbpb').html('Supplier : ' + supp + '');
    $('#txt_no_po').html('Reff Doc : ' + refdoc + '');
    $('#txt_supp').html('Account : ' + akun + '');
    $('#txt_top').html('Bank : ' + bank + '');;
    $('#txt_tgl_po').html('Description : ' + desk + '');                                          
    });

</script>


<script>

    $(function() {
      //Initialize Select2 Elements
      $('.select2').select2()
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
    })
  });

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
        $('#mytable').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        $("[data-toggle=tooltip]").tooltip();
    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            startDate : "01-01-2022",
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
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>


<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "approve_bank_out.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "approve_bank_out.php";
    };
</script>


<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
