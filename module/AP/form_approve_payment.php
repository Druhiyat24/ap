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
    <h5 class="mb-0"><i class="fas fa-thumbs-up"></i> APPROVAL REGULAR PAYMENT <span class="badge bg-danger ms-2">
        <i class="fas fa-bell"></i> 
        <?php
        $nama_supp ='ALL';
        $profit_center ='ALL';
        $where ='';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: 'ALL';
            $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center']: 'ALL';
            
        }

        if($nama_supp == 'ALL' && $profit_center == 'ALL'){
            $where ='';
        }elseif($nama_supp != 'ALL' && $profit_center == 'ALL'){
            $where ="AND nama_supp = '$nama_supp' ";
        }elseif($nama_supp == 'ALL' && $profit_center != 'ALL'){
            $where ="AND profit_center = '$profit_center' ";
        }else {
            $where ="AND nama_supp = '$nama_supp' AND profit_center = '$profit_center'";
        }
        $sql = mysqli_query($conn2,"select COUNT(id) jml from (select a.id, payment_ftr_id, tgl_pelunasan, nama_pc, nama_supp, valuta_ftr curr, sum(ttl_bayar) total from payment_ftr a INNER JOIN master_pc b on b.kode_pc = a.profit_center where a.status = 'draft' $where GROUP BY payment_ftr_id) a");
        $row = mysqli_fetch_array($sql);
        $jml = $row['jml'];
        echo $jml;
        ?>

    </span></h5>
</div>

<div class="card-body p-3">
    <form id="form-data" action="form_approve_payment.php"method="post">
        <div class="row g-3">
          <!-- Supplier -->
          <div class="col-md-3">
            <label for="nama_supp"><b>Supplier</b></label>            
            <select class="form-control select2bs4" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
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

        <div class="col-md-3">
            <label for="profit_center"><b>Profit Center</b></label>            
            <select class="form-control select2bs4" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" <?php
                $profit_center = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $status = isset($_POST['profit_center']) ? $_POST['profit_center']: null;
                }                 
                if($profit_center == 'ALL'){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo $isSelected;
                ?>                
                >ALL</option>                                 
                <?php
                $profit_center ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center']: null;
                }                 
                $sql = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['kode_pc'];
                    $data2 = $row['tampil'];
                    if($row['kode_pc'] == $_POST['profit_center']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                }?>
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
              <th style="width: 15%;">No Payment</th>
              <th style="width: 15%;">Payment Date</th>
              <th style="width: 22%;">Profit Center</th>
              <th style="width: 22%;">Supplier</th>
              <th style="width: 8%;">Currency</th>
              <th style="width: 13%;">Total</th>
              <th style="width:5%; text-align: center;"><input type="checkbox" id="select_all"></th>
          </tr>
      </thead>
      <tbody>
        <?php
        $nama_supp ='ALL';
        $profit_center ='ALL';
        $where ='';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: 'ALL';
            $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center']: 'ALL';

        }

        if($nama_supp == 'ALL' && $profit_center == 'ALL'){
            $where ='';
        }elseif($nama_supp != 'ALL' && $profit_center == 'ALL'){
            $where ="AND nama_supp = '$nama_supp' ";
        }elseif($nama_supp == 'ALL' && $profit_center != 'ALL'){
            $where ="AND profit_center = '$profit_center' ";
        }else {
            $where ="AND nama_supp = '$nama_supp' AND profit_center = '$profit_center'";
        }
        $sql = mysqli_query($conn2,"select payment_ftr_id, tgl_pelunasan, nama_pc, nama_supp, valuta_ftr curr, sum(ttl_bayar) total from payment_ftr a INNER JOIN master_pc b on b.kode_pc = a.profit_center where a.status = 'draft' $where GROUP BY payment_ftr_id");

        while($row = mysqli_fetch_array($sql)){ 
            $tgl_pay = $row['tgl_payment']; 
            if ($tgl_pay != '') {
                $tgl_payment = date("d-M-Y",strtotime($row['tgl_payment']));
            } else{
              $tgl_payment = '-';  
          }                                    
          echo'<tr style="text-align:center;">                    
          <td style="" value = "'.$row['payment_ftr_id'].'">'.$row['payment_ftr_id'].'</td>
          <td style="" value = "'.$row['tgl_pelunasan'].'">'.date("d-M-Y",strtotime($row['tgl_pelunasan'])).'</td>
          <td style="" value = "'.$row['nama_pc'].'">'.$row['nama_pc'].'</td>
          <td style="" value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
          <td style="" value = "'.$row['curr'].'">'.$row['curr'].'</td>
          <td style="text-align:right;" value = "'.$row['total'].'">'.number_format($row['total'],2).'</td>
          <td style="width:10px; text-align: center;"><input type="checkbox" name="select[]" data-id="'.$row['payment_ftr_id'].'"></td>                      
          </tr>';                

      } ?>
  </tbody>
</table>
</div>
<form id="form-simpan">
    <div class="mt-3">
        <button type="button" name="approve" id="approve" class="btn btn-success">
          <i class="fas fa-check-circle"></i> Approve
      </button>
  </div>
</form>
</div>

</div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #2563EB;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_header_modal"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_data_1" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_data_2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
                  <div id="txt_data_3" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                                    
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>           
              </div>
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

<script type="text/javascript">
    // Select All
    $("#select_all").on("click", function(){
      $("input[name='selected_ids[]']").prop("checked", this.checked);
  });

    $("#form-simpan").on("click", "#approve", function(e){
        e.preventDefault();

        let selected = $("input[name='select[]']:checked");
        if(selected.length === 0){
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Silahkan ceklist data terlebih dahulu'
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
                no_payment  : row.find('td:eq(0)').attr('value'),
                approve_user: approve_user
            };

            $.ajax({
                type:'POST',
                url:'approve_payment.php',
                data: data,
                success: function(response){
                    console.log("Approved:", response);
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
                                title: 'Berhasil!',
                                text: total + ' payment berhasil di-approve'
                            }).then(() => {
                                window.location = 'form_approve_payment.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: (total-errors) + ' berhasil, ' + errors + ' gagal'
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
        $('#mymodal').modal('show');
        var payment_ftr_id = $(this).closest('tr').find('td:eq(0)').text();
        var tgl_pelunasan = $(this).closest('tr').find('td:eq(1)').text();
        var profit_center = $(this).closest('tr').find('td:eq(2)').text();
        var nama_supp = $(this).closest('tr').find('td:eq(3)').text();
        $.ajax({
            type : 'post',
            url : 'ajax_payment2.php',
            data : {'payment_ftr_id': payment_ftr_id},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_header_modal').html(payment_ftr_id);
        $('#txt_data_1').html('Payment Date: ' + tgl_pelunasan + '');
        $('#txt_data_2').html('Profit Center : ' + profit_center + '');
        $('#txt_data_3').html('Supplier : ' + nama_supp + '');                                        
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
        location.href = "form_approve_payment.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "form_approve_payment.php";
    };
</script>


<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
