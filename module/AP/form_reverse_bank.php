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

<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-left" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="mb-0 text-white" ><i class="fab fa-wpforms"></i> FORM REVERSE BANK</h5>
    </div>
    <form id="form-data" method="post">
        <div class="form-row mt-2">
            <div class="col-md-3 mb-3">            
                <label for="nopayment"><b>No Reverse</b></label>
                <?php
                $sql = mysqli_query($conn2,"select CONCAT('RVS/BN/',DATE_FORMAT(CURRENT_DATE(), '%m%y'),'/',LPAD((COALESCE(max(SUBSTR(rvs_number,13)),0) + 1),5,0)) nomor from ap_reverse_h WHERE rvs_number like '%BN%' and YEAR(rvs_date) = YEAR (CURRENT_DATE())");
                $row = mysqli_fetch_array($sql);
                $kodepay = $row['nomor'];

                echo'<input type="text" readonly style="font-size: 14px;" class="form-control form-control-sm" id="no_reverse" name="no_reverse" value="'.$kodepay.'">'
                ?>
            </div>
            <div class="col-md-2 mb-3">            
                <label for="tanggal"><b>Reverse Date <i style="color: red;">*</i></b></label>          
                <!-- <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control form-control-sm tanggal" onchange="ubahtanggal(this.value)"
                value="<?php             
                $start_date ='';
                $end_date ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $tanggal = date("Y-m-d",strtotime($_POST['h_tanggal']));
                    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                    $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                }

                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                $sql = mysqli_query($conn2,"select distinct max(tgl_kbon) from kontrabon_h where nama_supp = '$nama_supp'and status = 'Approved' and balance >= 1 and tgl_kbon between '$start_date' and '$end_date' ");
                $row = mysqli_fetch_array($sql);
                $tgl = $row['max(tgl_kbon)'];    


                // echo $tanggal; echo $tgl;

                if(!empty($nama_supp) && $tanggal != '1970-01-01') {
                    if($tanggal < $tgl){
                        echo date("d-m-Y",strtotime($tgl));
                        }else{
                          echo date("Y-m-d",strtotime($tanggal));  
                      }
                  }
                  else{
                    echo date("Y-m-d");
                }  ?>"> -->

                <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control form-control-sm" value="<?php echo date("Y-m-d");?>" readonly>

            </div>


            <div class="col-md-3 mb-3">            
                <label for="profit_center"><b>Type Document <i style="color: red;">*</i></b></label>            
                <select class="form-control form-control-sm select2bs4" name="type_doc" id="type_doc" data-dropup-auto="false" data-live-search="true">
                    <option value="" disabled selected="true">Select Type Document</option>                                                 
                    <option value="BANK IN" <?php
                    $type_doc = '';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null;
                    }                 
                    if($type_doc == 'BANK IN'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                    ?>
                    >BANK IN</option>

                    <option value="BANK OUT" <?php
                    $type_doc = '';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null;
                    }                 
                    if($type_doc == 'BANK OUT'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                    ?>
                    >BANK OUT</option>
                    
                </select>  

                <input type="hidden" readonly style="font-size: 13px;;" class="form-control form-control-sm form-control form-control-sm-sm" id="jurnal" name="jurnal" 
                value="0" placeholder="<?php echo "KONTRA BON" ?>">
            </div>

<!-- <div class="col-md-3 mb-3">            
    <label for="jurnal"><b>Journal</b></label>  -->           
    <input type="hidden" readonly style="font-size: 14px;" class="form-control form-control-sm" id="jurnal" name="jurnal" value="0"
    placeholder="<?php echo "Payment" ?>">
    <!-- </div>   -->          

    <div class="col-md-3 mb-3">            
    </div>            



    <div class="col-md-5 mb-3">
        <label for="txt_supp"><b>Source</b></label>
        <div class="input-group input-group-sm">
            <input type="text" 
            readonly 
            class="form-control" 
            style="font-size: 14px;" 
            name="txt_supp" 
            id="txt_supp" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            echo $nama_supp; 
        ?>">
        <button type="button" 
        class="btn btn-info btn-sm" 
        id="mysupp"><i class="fas fa-plus-square"></i> Select</button>
        <input type="hidden" name="bpbvalue" id="bpbvalue" value="">      
    </div>
</div>


<div class="col-md-7 mb-3">            
</div>  

<div class="col-md-5 mb-3">            
    <label for="memo"><b>Descriptions</b></label>          
    <textarea style="font-size: 14px;" class="form-control form-control-sm" 
    name="memo" id="memo" rows="3"><?php             
    if (!empty($_POST['memo'])) {
        echo $_POST['memo'];
    } else {
        echo '';
    }
?></textarea>
</div>


</div>
</form>

<div class="row">

    <div class="col-md-12">

        <div class="table-responsive p-1">
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                <thead>
                    <tr class="text-white" style="background-color: #1E3A8A;">
                        <th style="width:10px;">Check</th>
                        <th style="width:50px;">No Bank</th>
                        <th style="width:100px;">Bank Date</th>                            
                        <th style="width:100px;">Source</th>                            
                        <th style="width:50px;">Curr</th>
                        <th style="width:100px;">Total</th>
                        <th style="width:100px;">Descriptions</th>
                        <th style="width:50px;">Status Closing</th>
                        <th style="display: none;"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $start_date ='';
                    $end_date ='';
                    $sub = '';
                    $tax = '';
                    $total = '';  
                    $doc_type = '';          
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                        $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                        $doc_type = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
                    }

                    if ($doc_type === 'BANK IN') {
                        if ($nama_supp == 'ALL') {
                            $sql = mysqli_query($conn2,"select a.* from (select doc_num no_bank, date bank_date, customer, curr, amount, status_closing, c.nama_pc from tbl_bankin_arcollection a LEFT JOIN tbl_closing_periode b on a.date BETWEEN b.tgl_awal AND b.tgl_akhir left join master_pc c on c.kode_pc = a.profit_center where a.date BETWEEN '$start_date' and '$end_date' and a.status = 'Approved' GROUP BY doc_num) a LEFT JOIN
                                (select doc_number from ap_reverse_h a INNER JOIN ap_reverse_det b on b.rvs_number = a.rvs_number where a.status = 'DRAFT') b on b.doc_number = a.no_bank where b.doc_number is null");
                        }else{
                            $sql = mysqli_query($conn2,"select a.* from (select doc_num no_bank, date bank_date, customer, curr, amount, status_closing, c.nama_pc from tbl_bankin_arcollection a LEFT JOIN tbl_closing_periode b on a.date BETWEEN b.tgl_awal AND b.tgl_akhir left join master_pc c on c.kode_pc = a.profit_center where a.date BETWEEN '$start_date' and '$end_date' and customer = '$nama_supp' and a.status = 'Approved' GROUP BY doc_num) a LEFT JOIN
                                (select doc_number from ap_reverse_h a INNER JOIN ap_reverse_det b on b.rvs_number = a.rvs_number where a.status = 'DRAFT') b on b.doc_number = a.no_bank where b.doc_number is null");

                        }
                    }else{
                        if ($nama_supp == 'ALL') {
                            $sql = mysqli_query($conn2,"select a.* from (select no_bankout no_bank, bankout_date bank_date, nama_supp customer, curr, amount, status_closing, 'NIRWANA ALABARE GARMENT' nama_pc from b_bankout_h a LEFT JOIN tbl_closing_periode b on a.bankout_date BETWEEN b.tgl_awal AND b.tgl_akhir where a.bankout_date BETWEEN '$start_date' and '$end_date' and a.status = 'Approved' GROUP BY no_bankout) a LEFT JOIN
                                (select doc_number from ap_reverse_h a INNER JOIN ap_reverse_det b on b.rvs_number = a.rvs_number where a.status = 'DRAFT') b on b.doc_number = a.no_bank where b.doc_number is null");
                        }else{
                            $sql = mysqli_query($conn2,"select a.* from (select no_bankout no_bank, bankout_date bank_date, nama_supp customer, curr, amount, status_closing, 'NIRWANA ALABARE GARMENT' nama_pc from b_bankout_h a LEFT JOIN tbl_closing_periode b on a.bankout_date BETWEEN b.tgl_awal AND b.tgl_akhir where a.bankout_date BETWEEN '$start_date' and '$end_date' and nama_supp = '$nama_supp' and a.status = 'Approved' GROUP BY no_bankout) a LEFT JOIN
                                (select doc_number from ap_reverse_h a INNER JOIN ap_reverse_det b on b.rvs_number = a.rvs_number where a.status = 'DRAFT') b on b.doc_number = a.no_bank where b.doc_number is null");

                        }
                    }


                    while($row = mysqli_fetch_array($sql)){ 
                        if ($row['status_closing'] == 'Open') {
                            $disabled = '';
                        }else{
                            $disabled = 'disabled';
                        }                 
                        echo '<tr>
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" '.$disabled.'></td>                        
                        <td style="width:50px;" value="'.$row['no_bank'].'">'.$row['no_bank'].'</td>
                        <td style="width:100px;" value="'.$row['bank_date'].'">'.date("d-M-Y",strtotime($row['bank_date'])).'</td>
                        <td style="width:50px;text-align: left;" value="'.$row['customer'].'">'.$row['customer'].'</td>
                        <td style="width:50px;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                        <td style ="text-align: right;" class="dt_total" style="width:100px;" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
                        <td style="width:100px;">
                        <textarea class="form-control form-control-sm" id="txt_desc" name="txt_desc" style="font-size: 12px; text-align: left;" rows="1" disabled></textarea>
                        </td>
                        <td style="width:50px;" value="'.$row['status_closing'].'">'.$row['status_closing'].'</td>
                        <td style="display: none;" value="'.$row['nama_pc'].'">'.$row['nama_pc'].'</td>
                        </tr>';
                    }                  
                    ?>
                </tbody>  
            </table>                  
        </table>                    
        <form id="form-simpan">
            <div class="form-row col mt-3">
                <div class="col-md-3 mb-3">                              
                    <button type="button" style="border-radius: 6px" class="btn-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
                    <button type="button" style="border-radius: 6px" class="btn-danger btn-sm" name="batal" id="batal" onclick="location.href='reverse_bank.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
                </div>
            </div>                                    
        </form>
    </div>

</div>
</div>
</div>


<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-lg rounded-3">

      <!-- Header -->
      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Add Data</h4>
    </div>

    <!-- Body -->
    <div class="modal-body">
        <form id="modal-form" method="post">

          <!-- Supplier -->
          <div class="form-group mb-3">
            <label for="nama_supp"><b>Source</b></label>
            <select class="form-control form-control-sm selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" <?php
                $nama_supp = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                if($nama_supp == 'ALLP'){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo $isSelected;
                ?>
                >ALL</option>               
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'C' order by Supplier ASC");
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

        <input type="hidden" style="font-size: 13px;" name="h_profit_center" id="h_profit_center" class="form-control form-control-sm form-control form-control-sm-sm" value="">
        <input type="hidden" style="font-size: 13px;" name="h_tanggal" id="h_tanggal" class="form-control form-control-sm" value="">

        <!-- BPB Date -->
        <div class="form-group">
            <label><b>Bank Date</b></label>
            <div class="d-flex align-items-center gap-2">
               <input type="text" style="font-size: 14px;" class="form-control form-control-sm tanggal3" id="start_date" name="start_date" 
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
            placeholder="Tanggal Awal">

            <span class="mx-2">-</span>

            <input type="text" style="font-size: 14px;" class="form-control form-control-sm tanggal3" id="end_date" name="end_date" 
            value="<?php
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
              $end_date = date("Y-m-d",strtotime($_POST['start_date']));
          }
          if(!empty($_POST['end_date'])) {
            echo $_POST['end_date'];
        }
        else{
            echo date("d-m-Y");
        } ?>" 
        placeholder="Tanggal Akhir">
    </div>
</div>

</form>
</div>

<!-- Footer -->
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">
      <i class="fa fa-times"></i> Close
  </button>
  <button type="submit" form="modal-form" id="send" name="send" class="btn btn-warning">
      <i class="fa fa-search" aria-hidden="true"></i> Search
  </button>
</div>

</div>
</div>
</div>

<div class="modal fade" id="mymodal2" data-target="#mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
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
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script type="text/javascript">
   $(document).ready(function() {
    $("#mysupp").on("click", function() {
        let type_doc = $('select[name=type_doc] option').filter(':selected').val();
        if (type_doc == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Please Input Type Document.',
                confirmButtonText: 'OK'
            }).then(() => {
                $('#type_doc').select2('open');
            });
            return;
        }

        $('#h_profit_center').val(type_doc);
        let tanggal = document.getElementById('tanggal').value;
        $('#h_tanggal').val(tanggal);
        $("#mymodal").modal("show");
    });
});


   document.addEventListener("DOMContentLoaded", function () {
    let savedPc = localStorage.getItem("type_doc");
    let savedMemo = localStorage.getItem("memo");

    // flag untuk deteksi load pertama
    let isInitialLoad = true;

    // restore type_doc (select2)
    if (savedPc) {
        $("#type_doc").val(savedPc).trigger("change.select2"); // update UI saja
    }

    // restore memo (textarea)
    if (savedMemo) {
        $("#memo").val(savedMemo);
    }

    // setelah selesai load, baru flag dimatikan
    isInitialLoad = false;

    // simpan type_doc setiap kali berubah
    $("#type_doc").on("change", function () {
        localStorage.setItem("type_doc", $(this).val());

        // hanya clear tabel kalau bukan load awal
        if (!isInitialLoad) {
            document.querySelector("#mytable tbody").innerHTML = "";
        }
    });

    // simpan memo setiap kali ada perubahan
    $("#memo").on("input", function () {
        localStorage.setItem("memo", $(this).val());
    });
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

    } );
</script>
<script type="text/javascript">
    $(document).ready(function () {
        // var tgl1 = document.getElementById('tanggal1').value;
        $('.tanggal').datepicker({
            format: "yyyy-mm-dd",
            autoclose:true,
        // startDate: new Date(tgl1)
    });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        var tgl = document.getElementById('due_date1').value;
        $('.due_date').datepicker({
            format: "yyyy-mm-dd",
            autoclose:true
        // startDate: new Date(tgl)
    });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal3').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>

<script type="text/javascript"> 
    var tgl = 0;
    var tgl2 = '';
    function ubahtanggal(value){  
        var tanggal = document.getElementById('tanggal').value; 
        var coba = new Date();
        // alert(tanggal);
        $.ajax({
            type: 'POST', 
            url: 'getnomor_lp.php', 
            data: {'tanggal':tanggal},
            success: function(response) { 
                // alert(response);
                $('#nopayment').val(response);
                updateNoLP(); 
            }
        });
    };
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
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
    var sum_kb = 0;
    var sum_amount = 0;
    var ceklist = 0;
    var sum_total = 0;
    var dates= '';
    $(this).closest('tr').find('td:eq(6) textarea').prop('disabled', true);
    $(this).closest('tr').find('td:eq(6) textarea').val("");                  
    $("input[type=checkbox]:checked").each(function () {   
        var select_amount = $(this).closest('tr').find('td:eq(6) textarea');
        select_amount.prop('disabled', false);              
    });                 
});        
</script>

<script type="text/javascript">
    $("input[name=txt_amount]").keyup(function(){
        var sum_kb = 0;
        var sum_amount = 0;
        var sum_total = 0;
        var sum_balance = 0;        
        $("input[type=checkbox]:checked").each(function () {        
            var kb = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
            var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
            var balance = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
            var select_amount = $(this).closest('tr').find('td:eq(6) input');                
            if(amount > balance){
                sum_kb += kb;
                select_amount.val(balance);
                sum_amount += balance;
                sum_total = sum_kb - sum_amount;
            }else{
                sum_kb += kb;
                sum_amount += amount;
                sum_total = sum_kb - sum_amount;        
            }   
        });
        $("#subtotal").val(formatMoney(sum_kb));
        $("#pajak").val(formatMoney(sum_amount));    
        $("#total").val(formatMoney(sum_total));
    });
</script>

<!-- <script type="text/javascript">
    $("#txt_dp").keyup(function(){
        var dp_value = 0;
        var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
        var dp = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;
        dp_value = total_po * (dp / 100);
        $("#txt_dp_value").val(formatMoney(dp_value));
    });
</script> -->

<!-- <script type="text/javascript">
    $("#txt_dp_value").keyup(function(){
        var dp_code = 0;
        var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
        var dp_value = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
        dp_code = (dp_value / total_po) * 100;
        $("#txt_dp").val(dp_code);
    });
</script> -->

<!--<script type="text/javascript">
    $("#form-data").on("click", "#send", function(){
        var datas= $('select[name=nama_supp] option').filter(':selected').val();
        var start_date= $('#start_date').attr('value');
        var end_date= $('#start_date').attr('value');
        $.ajax({
            type:'POST',
            url:'cek.php',
            data: {'nama_supp':datas, 'start_date': start_date, 'end_date': end_date},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                alert(response);
            },
            error:  function (xhr, ajaxOptions, thrownError) {
                alert(xhr);
            }
        });
    });
</script>-->

<script type="text/javascript">
// get all number fields
var numInputs = document.querySelectorAll('input[type="number"]');

// Loop through the collection and call addListener on each element
Array.prototype.forEach.call(numInputs, addListener); 


function addListener(elm,index){
  elm.setAttribute('min', 1);  // set the min attribute on each field
  
  elm.addEventListener('keypress', function(e){  // add listener to each field 
   var key = !isNaN(e.charCode) ? e.charCode : e.keyCode;
   str = String.fromCharCode(key); 
   if (str.localeCompare('-') === 0){
     event.preventDefault();
 }

});
  
}
</script>

<script type="text/javascript">
  $("#form-simpan").on("click", "#simpan", function () {
    var no_reverse   = $("#no_reverse").val();
    var reverse_date = $("#tanggal").val();
    var type_doc     = $("#type_doc").val();
    var deskripsi    = $("#memo").val();
    var create_user  = '<?php echo $user ?>';

    let checked = $("input[name='select[]']:checked");

    if (type_doc !== '' && deskripsi !== '') {
        if (checked.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Please check Document Number'
            });
            return;
        }

        // === SIMPAN HEADER ===
        $.ajax({
            type: 'POST',
            url: 'insert_ap_reverse_h.php',
            data: {
                'no_reverse': no_reverse,
                'reverse_date': reverse_date,
                'type_doc': type_doc,
                'deskripsi': deskripsi,
                'create_user': create_user
            },
            cache: false,
            success: function (response) {
                localStorage.removeItem("type_doc");
                localStorage.removeItem("memo");
                $("#memo").val("");

                // === LOOP DETAIL ===
                checked.each(function () {
                    var tr = $(this).closest("tr");
                    var no_dokumen   = tr.find('td:eq(1)').attr('value');
                    var tgl_dokumen  = tr.find('td:eq(2)').attr('value');
                    var nama_supp    = tr.find('td:eq(3)').attr('value');
                    var curr         = tr.find('td:eq(4)').attr('value');
                    var total        = parseFloat(tr.find('td:eq(5)').attr('value'), 10) || 0;
                    var deskripsi_det = tr.find('td:eq(6) textarea').val() || '-';

                    if (total >= 0) {
                        $.ajax({
                            type: 'POST',
                            url: 'insert_ap_reverse_det.php',
                            data: {
                                'no_reverse': no_reverse,
                                'create_user': create_user,
                                'no_dokumen': no_dokumen,
                                'tgl_dokumen': tgl_dokumen,
                                'nama_supp': nama_supp,
                                'curr': curr,
                                'total': total,
                                'deskripsi': deskripsi_det
                            },
                            cache: false,
                            success: function (res) {
                                console.log("Detail inserted:", res);
                            },
                            error: function (xhr) {
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Data saved successfully!'
                }).then(() => {
                    window.location = 'reverse_bank.php';
                });

            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseText
                });
            }
        });
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Please input Type Document and Description'
        });
    }
});

</script>

<script type="text/javascript">
    $("#select_all").click(function() {
      var c = this.checked;
      $(':checkbox').prop('checked', c);
  });  
</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
        $('#mymodal2').modal('show');
        var no_kbon = $(this).closest('tr').find('td:eq(1)').text();
        var tgl_pelunasan = $(this).closest('tr').find('td:eq(2)').text();
        var profit_center = $(this).closest('tr').find('td:eq(8)').text();
        var nama_supp = $(this).closest('tr').find('td:eq(3)').text();
        $.ajax({
            type : 'post',
            url : 'ajaxkbon.php',
            data : {'no_kbon': no_kbon},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_header_modal').html(no_kbon);
        $('#txt_data_1').html('Payment Date: ' + tgl_pelunasan + '');
        $('#txt_data_2').html('Profit Center : ' + profit_center + '');
        $('#txt_data_3').html('Supplier : ' + nama_supp + '');                                        
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
