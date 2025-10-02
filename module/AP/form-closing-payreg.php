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
    <h5 class="mb-0"><i class="fas fa-thumbs-up"></i> CLOSING PAYMENT REGULER <span class="badge bg-danger ms-2">
        <i class="fas fa-bell"></i> 
        <?php
        $nama_supplier ='ALL';
        $where ='';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_supplier'])) {
            $nama_supplier = $_POST['nama_supplier'];
        }

        elseif (isset($_GET['nama_supplier'])) {
            $nama_supplier = $_GET['nama_supplier'];
        }

        if($nama_supplier == 'ALL'){
            $where = '';
        }else {
            $where ="AND nama_supp = '$nama_supplier'";
        }
        
        $sql = mysqli_query($conn2,"select count(distinct(no_payment)) as jml from list_payment where status = 'Approved' $where ");
        $row = mysqli_fetch_array($sql);
        $jml = $row['jml'];
        echo $jml;
        ?>

    </span></h5>
</div>

<div class="card-body p-3">
    <form id="formSupplier" action="form-closing-payreg.php"method="post">
        <input type="hidden" name="nama_supplier" id="hidden_supplier">
        <div class="row g-3">
          <!-- Supplier -->
          <div class="col-md-3">
            <label for="nama_supplier"><b>Supplier</b></label>            
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
              <th style="width:100px;">No List Payment</th>
              <th style="width:100px;">List Payment Date</th>
              <th style="width:200px;">Supplier</th>
              <th style="width:100px;display: none;">Curr</th>                                                   
              <th style="width:150px;">Total List Payment</th>
              <th style="width:50px;width:100px;display: none;">No Payment</th>
              <th style="width:100px;width:100px;display: none;">Payment Date</th>
              <th style="width:100px;width:100px;display: none;">Pay Method</th>
              <th style="width:100px;display: none;">Currency</th>
              <th style="width:100px;width:100px;display: none;">Nominal</th>
              <th style="width:100px;display: none;">Currency</th>
              <th style="width:100px;">Due Date</th>
              <th style="width:5%; text-align: center;"><input type="checkbox" id="select_all"></th>                 
          </tr>
      </thead>
      <tbody>
        <?php
        $nama_supplier = 'ALL';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_supplier'])) {
            $nama_supplier = $_POST['nama_supplier'];
        }

        elseif (isset($_GET['nama_supplier'])) {
            $nama_supplier = $_GET['nama_supplier'];
        }

            // echo $nama_supplier;

        if(empty($nama_supplier) or $nama_supplier == 'ALL'){
            $sql = mysqli_query($conn2,"select a.no_payment, a.tgl_payment, a.nama_supp, a.curr, sum(a.amount) as amount, a.status, b.payment_ftr_id, b.tgl_pelunasan, b.cara_bayar, b.account, b.bank, b.valuta_bayar, b.nominal, b.nominal_fgn, b.ttl_bayar, a.tgl_tempo from list_payment a left join payment_ftr b on b.list_payment_id = a.no_payment where a.`status` = 'Approved' group by a.no_payment
                union

                select a.no_pay as no_payment, a.tgl_pay as tgl_payment, a.supplier as nama_supp, a.valuta as curr, sum(a.total) as amount,a.status,b.payment_ftr_id, b.tgl_pelunasan, b.cara_bayar, b.account, b.bank, b.valuta_bayar, b.nominal, b.nominal_fgn, b.ttl_bayar,due_date from saldo_awal a left join payment_ftr b on b.list_payment_id = a.no_pay where a.`status` = 'Approved' group by a.no_pay");                
        }else {
            $sql = mysqli_query($conn2,"select a.no_payment, a.tgl_payment, a.nama_supp, a.curr, sum(a.amount) as amount, a.status, b.payment_ftr_id, b.tgl_pelunasan, b.cara_bayar, b.account, b.bank, b.valuta_bayar, b.nominal, b.nominal_fgn, b.ttl_bayar, a.tgl_tempo from list_payment a left join payment_ftr b on b.list_payment_id = a.no_payment where a.`status` = 'Approved' and a.nama_supp = '$nama_supplier' group by a.no_payment
                union

                select a.no_pay as no_payment, a.tgl_pay as tgl_payment, a.supplier as nama_supp, a.valuta as curr, sum(a.total) as amount,a.status,b.payment_ftr_id, b.tgl_pelunasan, b.cara_bayar, b.account, b.bank, b.valuta_bayar, b.nominal, b.nominal_fgn, b.ttl_bayar,due_date from saldo_awal a left join payment_ftr b on b.list_payment_id = a.no_pay where a.`status` = 'Approved' and a.supplier = '$nama_supplier' group by a.no_pay");
        }

        while($row = mysqli_fetch_array($sql)){ 
            $curr = isset($row['valuta_bayar']) ? $row['valuta_bayar'] :null;
            $no_paymt = isset($row['payment_ftr_id']) ? $row['payment_ftr_id'] :null;
            $tgl_tempo = $row['tgl_tempo'];
            $tgl_sekarang = date("Y-m-d");

            if ($tgl_tempo <= $tgl_sekarang) {
                $bg = 'style="background-color: #CD5C5C;color: white;"';
                #DC143C
                #CD5C5C
            }else{
                $bg = '';
            }

            if ($no_paymt == '') {
                $nom = '-';
                $nom1 = '-';
                $method = '-';
                $tgl_lunas = '-';
                $no_lunas = '-';
            }else{
                $method = $row['cara_bayar'];
                $tgl_lunas = date("d-M-Y",strtotime($row['tgl_pelunasan']));
                $no_lunas = $row['payment_ftr_id'];
                if ($curr == 'IDR') {
                    $nom = isset($row['nominal']) ? $row['nominal'] :0;  
                    $nom1 = number_format($nom,2);  
                } elseif($curr == 'USD'){
                    $nom = isset($row['nominal_fgn']) ? $row['nominal_fgn'] :0;
                    $nom1 = number_format($nom,2);
                }else{
                    $nom = isset($row['ttl_bayar']) ? $row['ttl_bayar'] :0;
                    $nom1 = number_format($nom,2);
                }     
            }                                     
            echo'<tr '.$bg.' >                       
            <td style="" value="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td style="" value="'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
            <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>                                                                  
            <td style="display:none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="" value="'.$row['amount'].'">'.$row['curr'].' '.number_format($row['amount'],2).'</td>                                      
            <td style="display:none;" value="'.$no_lunas.'">'.$no_lunas.'</td>
            <td style=";display:none;" value="'.$tgl_lunas.'">'.$tgl_lunas.'</td>
            <td style=";display:none;" value="'.$method.'">'.$method.'</td>
            <td style="display: none;" value="'.$row['valuta_bayar'].'">'.$row['valuta_bayar'].'</td>                            
            <td style=";display:none;" value="'.$nom.'">'.$row['valuta_bayar'].' '.$nom1.'</td>
            <td style="display:none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="" value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>
            <td style="width:10px; text-align: center;"><input type="checkbox" name="select[]" data-id="'.$row['no_payment'].'"></td>
            </tr>';                

        } ?>
    </tbody>
</table>
</div>
<form id="form-simpan">
    <div class="mt-3">
        <button type="button" name="approve" id="approve" class="btn btn-success">
          <i class="fas fa-clipboard-check"></i> Close Payment
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
                <h4 class="modal-title" id="txt_list_payment"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tgl_list_payment" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
                  <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
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
        let save_supp = localStorage.getItem("nama_supplier") || "ALL";

    // set nilai ke select
    document.getElementById("nama_supplier").value = save_supp;
    $('#nama_supplier').selectpicker('refresh'); // wajib untuk bootstrap-select

    // event saat dropdown berubah
    document.getElementById("nama_supplier").addEventListener("change", function() {
        localStorage.setItem("nama_supplier", this.value);
        document.getElementById("formSupplier").submit();
    });
});
</script>


<script type="text/javascript">
    // Select All
    $("#select_all").on("click", function(){
      $("input[name='select[]']").prop("checked", this.checked);
  });

    $("#form-simpan").on("click", "#approve", function(e) {
        e.preventDefault();

        let selected = $("input[name='select[]']:checked");
        if (selected.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Please check at least one data before proceeding'
            });
            return;
        }

        let approve_user = '<?php echo $user ?>';
        let total = selected.length;
        let processed = 0;
        let errors = 0;

        let currentSupplier = $("#nama_supplier").val();

        selected.each(function () {
            let row = $(this).closest('tr');

            let data = {
                no_pay       : row.find('td:eq(0)').attr('value'),
                update_user  : approve_user,
                nama_supplier: currentSupplier
            };

            $.ajax({
                type: 'POST',
                url: 'closed.php',
                data: data,
                success: function(response) {
                    console.log("Approved:", response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.error(xhr.responseText);
                    errors++;
                },
                complete: function() {
                    processed++;
                    if (processed === total) {
                        if (errors === 0) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: total + ' Payment has been successfully closed'
                            }).then(() => {
                            // reload dengan supplier terakhir
                            window.location = 'form-closing-payreg.php?nama_supplier=' + encodeURIComponent(currentSupplier);
                        });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: (total - errors) + ' Payment has been successfully closed ' + errors + ' failed to close'
                            });
                        }
                    }
                }
            });
        });
    });




    $("#form-simpan").on("click", "#cancel", function(e){
        e.preventDefault();

        let selected = $("input[name='select[]']:checked");
        if(selected.length === 0){
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Please check at least one data before proceeding'
            });
            return;
        }

        let approve_user = '<?php echo $user ?>';
        let total = selected.length;
        let processed = 0;
        let errors = 0;

        selected.each(function () {
            let row = $(this).closest('tr');

            let data = {
                rvs_number  : row.find('td:eq(0)').attr('value'),
                approve_user: approve_user
            };

            $.ajax({
                type:'POST',
                url:'cancel_reverse_ap.php',
                data: data,
                success: function(response){
                    console.log("Cancelled:", response);
                },
                error: function(xhr, ajaxOptions, thrownError){
                    console.error(xhr.responseText);
                    errors++;
                },
                complete: function(){
                    processed++;
                    if(processed === total){
                        if(errors === 0){
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: total + ' reverse(s) Cancelled successfully'
                            }).then(() => {
                                window.location = 'form-closing-payreg.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: (total-errors) + ' Cancelled successfully, ' + errors + ' failed'
                            });
                        }
                    }
                }
            });
        });
    });


</script>
<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
        $('#modal-show').modal('show');
        var no_payment = $(this).closest('tr').find('td:eq(0)').attr('value');
        var tgl_list_payment = $(this).closest('tr').find('td:eq(1)').text();
        var supp = $(this).closest('tr').find('td:eq(2)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(3)').attr('value');
        var create_user = $(this).closest('tr').find('td:eq(7)').attr('value');
        var keterangan = $(this).closest('tr').find('td:eq(16)').attr('value');               

        $.ajax({
            type : 'post',
            url : 'ajaxlistpayment.php',
            data : {'no_payment': no_payment},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_list_payment').html(no_payment);
        $('#txt_tgl_list_payment').html('Tgl List Payment : ' + tgl_list_payment + '');
        $('#txt_nama_supp').html('Supplier : ' + supp + '');
        $('#txt_curr').html('Currency : ' + curr + '');                                         
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
<!-- 
<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var top = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(5)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(6)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(11)').text();        

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
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "form-closing-payreg.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "form-closing-payreg.php";
    };
</script>


<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
