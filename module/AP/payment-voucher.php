<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;
    }

    input {
        font-size: 14px;
    }

    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    #mytable th:nth-child(3), #mytable td:nth-child(3),
    #mytable th:nth-child(5), #mytable td:nth-child(5) {
        overflow: hidden;
        text-overflow: ellipsis;
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

    .kbon-action-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 6px;
    }

    .kbon-action-buttons .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 7px;
        width: 30px;
        height: 30px;
        padding: 0;
        font-size: 12px;
        box-shadow: 0 2px 5px rgba(15, 23, 42, 0.16);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .kbon-action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(15, 23, 42, 0.24);
    }

    .kbon-action-buttons .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .kbon-action-buttons .btn-success {
        background: linear-gradient(135deg, #16a34a, #15803d);
    }

    .kbon-action-buttons .btn-warning {
        color: #fff;
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .kbon-action-buttons .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .modal {
      text-align: center;
      padding: 0!important;
  }

  .modal:before {
      content: '';
      display: inline-block;
      height: 100%;
      vertical-align: middle;
      margin-right: -4px;
  }

  .modal-dialog {
      display: inline-table;
      width: 700px;
      text-align: left;
      vertical-align: middle;
  }

  /* Detail Payment Voucher */
  #mymodal .modal-dialog {
      display: inline-block;
      width: 94%;
      max-width: 1120px;
      margin: 1.75rem auto;
      vertical-align: middle;
  }

  #mymodal .modal-content {
      border: 0;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 16px 42px rgba(15, 23, 42, 0.24);
  }

  #mymodal .modal-header {
      padding: 16px 22px;
      border: 0;
      background: linear-gradient(135deg, #172554, #2563eb);
  }

  #mymodal .modal-title {
      font-size: 18px;
      font-weight: 700;
  }

  #mymodal .close {
      color: #fff;
      opacity: .9;
      text-shadow: none;
  }

  /* Update Cheque/Giro & Reverse Payment Voucher - modal kecil, style disamakan
     ke Detail Payment Voucher (#mymodal) supaya konsisten satu halaman. */
  #mymodal2 .modal-dialog, #mymodal3 .modal-dialog {
      max-width: 480px;
      margin: 1.75rem auto;
  }

  #mymodal2 .modal-content, #mymodal3 .modal-content {
      border: 0;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 16px 42px rgba(15, 23, 42, 0.24);
  }

  #mymodal2 .modal-header, #mymodal3 .modal-header {
      padding: 16px 22px;
      border: 0;
      background: linear-gradient(135deg, #172554, #2563eb);
  }

  #mymodal2 .modal-title, #mymodal3 .modal-title {
      font-size: 17px;
      font-weight: 700;
      color: #fff;
  }

  #mymodal2 .close, #mymodal3 .close {
      color: #fff;
      opacity: .9;
      text-shadow: none;
  }

  #mymodal2 .modal-body, #mymodal3 .modal-body {
      padding: 22px 24px 8px;
  }

  #mymodal2 label, #mymodal3 label {
      font-size: 12px;
      font-weight: 700;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: .03em;
  }

  #mymodal2 .form-control, #mymodal3 .form-control {
      border-radius: 8px;
      border: 1px solid #cbd5e1;
  }

  #mymodal2 .modal-footer, #mymodal3 .modal-footer {
      border: 0;
      padding: 16px 24px 22px;
  }

  #mymodal2 .btn-success, #mymodal3 .btn-success {
      border-radius: 8px;
      background: linear-gradient(135deg, #16a34a, #15803d);
      border: 0;
      font-weight: 600;
  }

  .pv-detail-summary {
      padding: 16px 20px 8px;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
  }

  .pv-detail-field {
      margin-bottom: 10px;
      font-size: 12px;
      color: #475569;
  }

  .pv-detail-field strong {
      display: block;
      margin-top: 2px;
      color: #0f172a;
      font-size: 13px;
      font-weight: 600;
  }

  #details {
      padding: 16px 20px 4px;
  }

  #details .pv-detail-table {
      margin-bottom: 14px;
      font-size: 11px;
  }

  #details .pv-detail-table thead th {
      background: #eaf1ff;
      color: #1e3a8a;
      border-color: #cbd5e1;
      font-weight: 700;
      vertical-align: middle;
      white-space: nowrap;
  }

  #details .pv-detail-table td {
      border-color: #e2e8f0;
      vertical-align: middle;
  }

  #details .pv-detail-totals {
      font-size: 12px;
  }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-list-alt" aria-hidden="true"></i> LIST PAYMENT VOUCHER</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-data" action="payment-voucher.php" method="post">
                <div class="row g-3">

                    <div class="col-md-2">
                        <label for="nama_supp"><b>Pay Method</b></label>            
                        <select class="form-control selectpicker" name="meth" id="meth" data-dropup-auto="false" data-live-search="true">
                            <option value="ALL" <?php
                            $meth = '';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $meth = isset($_POST['meth']) ? $_POST['meth']: null;
                            }                 
                            if($meth == 'ALL'){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo $isSelected;
                            ?>                
                            >ALL</option>                                          
                            <?php
                            $meth ='';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $meth = isset($_POST['meth']) ? $_POST['meth']: null;
                            }                 
                            $sql = mysqli_query($conn1,"select pay_method from tbl_paymethod ");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['pay_method'];
                                if($row['pay_method'] == $_POST['meth']){
                                    $isSelected = ' selected="selected"';
                                }else{
                                    $isSelected = '';

                                }
                                echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            }?> 
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="nama_supp"><b>Supplier</b></label>
                        <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                            <?php
                            $nama_supp = 'ALL';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : 'ALL';
                            }

                            $isSelected = ($nama_supp == 'ALL') ? ' selected="selected"' : '';
                            echo '<option value="ALL"' . $isSelected . '>ALL</option>';

                            $sql = mysqli_query($conn1, "SELECT DISTINCT(Supplier) FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['Supplier'];
                                $isSelected = ($data == $nama_supp) ? ' selected="selected"' : '';
                                echo '<option value="' . $data . '"' . $isSelected . '>' . $data . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="nama_supp"><b>Outstanding</b></label>
                        <select class="form-control selectpicker" name="ost" id="ost" data-dropup-auto="false" data-live-search="true">
                            <option value="ALL" <?php
                            $ost = '';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $ost = isset($_POST['ost']) ? $_POST['ost']: null;
                            }                 
                            if($ost == 'ALL'){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo $isSelected;
                            ?>                
                            >ALL</option>                                        
                            <option value="0" <?php
                            $ost = '';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $ost = isset($_POST['ost']) ? $_POST['ost']: null;
                            }                 
                            if($ost == '0'){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo $isSelected;
                            ?>                
                            >Yes</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4"></div>


                    <div class="col-md-2">
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
                            <option value="Closed" <?php
                            $status = '';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $status = isset($_POST['status']) ? $_POST['status']: null;
                            }                 
                            if($status == 'Closed'){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo $isSelected;
                            ?>
                            >Closed</option>                                                                                                             
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="start_date"><b>From</b></label>
                        <input type="text" style="font-size: 12px;" class="form-control tanggal" id="start_date" name="start_date" 
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
                 </div>

                 <div class="col-md-2">
                    <label for="end_date"><b>To</b></label>
                    <input type="text" style="font-size: 12px;" class="form-control tanggal" id="end_date" name="end_date" 
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
                 placeholder="Tanggal Awal">
             </div>     

                    <div class="col-md-3 d-flex align-items-end mt-2">
                        <button type="submit" class="btn btn-info btn-sm me-2">
                            <i class="fa fa-search" aria-hidden="true"></i> Search</button>

                        <?php
                        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Bank'");
                        $rs = mysqli_fetch_array($querys);
                        $id = isset($rs['id']) ? $rs['id'] : 0;

                        if($id == '37'){
                            echo '<button id="btncreate" type="button" class="btn btn-primary btn-sm ml-2"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create</button>';
                        }
                        ?>

                        <!-- href diisi/diupdate lewat JS (lihat dataTableReload()) setiap
                             kali tabel dimuat ulang, supaya selalu ikut filter yang aktif
                             sekarang walau tabelnya dimuat lewat AJAX tanpa reload halaman. -->
                        <a target="_blank" id="btnExportExcel" href="#" class="ml-2">
                            <button type="button" class="btn btn-success btn-sm">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </button>
                        </a>
                    </div>
      </div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
        <div class="table-responsive">

           <form id="formdata">
            <table id="mytable" class="table table-striped table-bordered table-hover table-sm" role="grid" cellspacing="0" width="100%">
                <thead class="table-gradient">
                    <tr>
                        <th style="text-align: center;vertical-align: middle;">No Payment Voucher</th>
                        <th style="text-align: center;vertical-align: middle;">PV Date</th>
                        <th style="text-align: center;vertical-align: middle;width: 180px;">Supplier</th>
                        <th style="text-align: center;vertical-align: middle;">Payment Method</th>
                        <th style="text-align: center;vertical-align: middle;">For Payment</th>
                        <th style="text-align: center;vertical-align: middle;">Due Date</th>
                        <th style="text-align: center;vertical-align: middle;">Curreny</th>
                        <th style="text-align: center;vertical-align: middle;">Amount</th>
                        <th style="text-align: center;vertical-align: middle;">Outstanding</th>
                        <th style="text-align: center;vertical-align: middle;">Status</th>
                        <th style="text-align: center;vertical-align: middle;width: 130px;">Action</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
        </form>
        </div>
        <div class="mt-2" style="font-size:13px"><b>Total Amount:</b> <span id="txtTotalAmount">0.00</span> &nbsp; | &nbsp; <b>Total Outstanding:</b> <span id="txtTotalOutstanding">0.00</span></div>
    </div>
</div>

<!-- if ($pay_meth == 'CHEQUE/GIRO') {
                                echo '<button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-warning">Update</button>
                                <a href="pdf_payvoucher.php?no_pv='.$row['no_pv'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> 
                            </td>';
                            }else{
                            echo '<a href="pdf_payvoucher.php?no_pv='.$row['no_pv'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> 
                            </td>';
                        } -->

                        <div class="form-row">
                            <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                                            <h4 class="modal-title" id="Heading">Update Cheque / Giro</h4>
                                        </div>
                                        <div class="modal-body">
                                            <form id="modal-form2" method="post">
                                                <div class="form-row">
                                                    <div class="col-md-12 mb-3">
                                                        <label for="txt_pv">No Payment Voucher</label>
                                                        <input type="text" readonly class="form-control" name="txt_pv" id="txt_pv" value="">
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="txt_cekgiro">No Cheque/Giro</label>
                                                        <input type="text" class="form-control" name="txt_cekgiro" id="txt_cekgiro">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="txt_cekdate">Cheque/Giro Date</label>
                                                        <input type="text" class="form-control tanggal" name="txt_cekdate" id="txt_cekdate">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" id="send2" name="send2" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



            <div class="modal fade" id="mymodal3" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="Heading">Reverse Payment Voucher</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                        </div>
                        <div class="modal-body">
                            <form id="modal-form3" method="post">
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="txt_pv3">No Payment Voucher</label>
                                        <input type="text" readonly class="form-control" name="txt_pv3" id="txt_pv3" value="">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                        <label for="txt_reverse">Description Reverse</label>
                                        <input type="text" class="form-control" name="txt_reverse" id="txt_reverse">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" id="send3" name="send3" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="pvDetailTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h4 class="modal-title" id="pvDetailTitle">Detail Payment Voucher</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-0">
                <div class="pv-detail-summary">
                    <div class="row">
                        <div id="txt_tglbpb" class="col-md-4 pv-detail-field"></div>
                        <div id="txt_no_po" class="col-md-4 pv-detail-field"></div>
                        <div id="txt_supp1" class="col-md-4 pv-detail-field"></div>
                        <div id="txt_supp" class="col-md-4 pv-detail-field"></div>
                        <div id="txt_top" class="col-md-4 pv-detail-field"></div>
                        <div id="txt_curr" class="col-md-4 pv-detail-field"></div>
                        <div id="txt_confirm" class="col-md-4 pv-detail-field"></div>
                        <div id="txt_confirm1" class="col-md-4 pv-detail-field"></div>
                        <div id="txt_confirm2" class="col-md-4 pv-detail-field"></div>
                    </div>
                </div>
                <div id="details"></div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
      </div>
      <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 

</div><!-- body-row END -->
</div>
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
    // Tabel diisi lewat AJAX (ajx_payment-voucher.php) supaya Search/filter
    // tidak reload seluruh halaman lagi - dulu form filter submit biasa
    // (method="post" ke halaman sendiri), sekarang cukup ajax.reload().
    // Data pendukung modal (no_cek, cek_date, pay_to, dst) dan tombol Excel/
    // caption Total juga ikut di-refresh dari response yang sama.
    var datatable;

    $(document).ready(function() {
        datatable = $('#mytable').DataTable({
            ordering: true,
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            ajax: {
                url: 'ajx_payment-voucher.php',
                type: 'POST',
                data: function (d) {
                    d.nama_supp = $('#nama_supp').val();
                    d.meth = $('#meth').val();
                    d.ost = $('#ost').val();
                    d.status = $('#status').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
                dataSrc: function (json) {
                    $('#txtTotalAmount').text(json.total_amount);
                    $('#txtTotalOutstanding').text(json.total_outstanding);
                    $('#btnExportExcel').attr('href', 'ekspor_payment_voucher.php?where=' + encodeURIComponent(json.where) + ' && start_date=' + json.start_date + ' && end_date=' + json.end_date);
                    return json.data;
                }
            },
            columns: [
                { data: 'no_pv' },
                { data: 'pv_date' },
                { data: 'pay_to' },
                { data: 'pay_meth' },
                { data: 'for_pay' },
                { data: 'due_date' },
                { data: 'curr' },
                { data: 'total' },
                { data: 'outstanding' },
                { data: 'status' },
                { data: 'action', orderable: false },
            ],
            columnDefs: [
                { targets: 0, width: '155px' }, // No Payment Voucher
                { targets: 1, width: '95px' },  // PV Date
                { targets: 2, width: '180px' }, // Supplier
                { targets: 3, width: '115px' }, // Payment Method
                { targets: 4, width: '190px' }, // For Payment
                { targets: 5, width: '95px' },  // Due Date
                { targets: 6, width: '65px' },  // Currency
                { targets: 7, width: '110px' }, // Amount
                { targets: 8, width: '120px' }, // Outstanding
                { targets: 9, width: '80px' },  // Status
                { targets: 10, width: '130px' }, // Action
                { targets: [0, 1, 3, 5, 6, 7, 8, 9, 10], className: 'text-center' }
            ]
        });

        $("[data-toggle=tooltip]").tooltip();
    });

    function dataTableReload() {
        datatable.ajax.reload();
    }

    $('#form-data').on('submit', function (e) {
        e.preventDefault();
        dataTableReload();
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
            startDate : "01-01-2021",
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
    $("#formdata").on("click", "#btnupdate", function(){
        $('#mymodal2').modal('show');
        var data = datatable.row($(this).closest('tr')).data();
        if (!data) return;

        $('#txt_pv').val(data.no_pv);
        $('#txt_cekgiro').val(data.no_cek);
        $('#txt_cekdate').val(data.cek_date);

    });

</script>

<script type="text/javascript">     
    $("#formdata").on("click", "#btn_maintainpv", function(){
        $('#mymodal3').modal('show');
        var data = datatable.row($(this).closest('tr')).data();
        if (!data) return;

        $('#txt_pv3').val(data.no_pv);

    });

</script>

<script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){ 
        var no_pv = document.getElementById('txt_pv').value; 
        var cekgiro = document.getElementById('txt_cekgiro').value; 
        var cekdate = document.getElementById('txt_cekdate').value; 
        var update_user = '<?php echo $user; ?>';   


        $.ajax({
            type:'POST',
            url:'update_cekgiro.php',
            data: {'no_pv':no_pv, 'cekgiro':cekgiro, 'cekdate':cekdate, 'update_user':update_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                $('#mymodal2').modal('hide');
                dataTableReload();
             },
             error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        return false; 

    });


</script>


<script type="text/javascript">
    $("#modal-form3").on("click", "#send3", function(){ 
        var no_pv = document.getElementById('txt_pv3').value; 
        var txt_reverse = document.getElementById('txt_reverse').value; 
        var update_user = '<?php echo $user; ?>';   

        if (txt_reverse != ''){
            $.ajax({
                type:'POST',
                url:'reverse_pv.php',
                data: {'no_pv':no_pv, 'txt_reverse':txt_reverse, 'update_user':update_user},
                cache: 'false',
                close: function(e){
                    e.preventDefault();
                },
                success: function(response){
                    console.log(response);
                $('#mymodal3').modal('hide');
                dataTableReload();
             },
             error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });   
        }          
        if(document.getElementById('txt_reverse').value == '' || document.getElementById('txt_reverse').value == null){
            alert("Please input descriptions");
            return false; 
        }else{
        }

    });


</script>

<script type="text/javascript">     
    $('#formdata').on('click', '.btn-detail-pv', function(){
        $('#mymodal').modal('show');
        var data = datatable.row($(this).closest('tr')).data();
        if (!data) return;

        var no_pv = data.no_pv;
        var tgl_pv = data.pv_date;
        var pay_to = data.pay_to;
        var pay_date = data.pay_date;
        var pay_meth = data.pay_meth;
        var f_akun = data.frm_akun;
        var t_akun = data.to_akun;
        var cek_no = data.no_cek;
        var cek_date = data.cek_date;
        var pay_for = data.for_pay;

        $.ajax({
            type : 'post',
            url : 'ajax_pv.php',
            data : {'no_pv': no_pv},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#pvDetailTitle').text('Detail Payment Voucher · ' + no_pv);
        $('#txt_tglbpb').html('PV Date<strong>' + tgl_pv + '</strong>');
        $('#txt_top').html('Payment Method<strong>' + pay_meth + '</strong>');
        $('#txt_no_po').html('Payment To<strong>' + pay_to + '</strong>');
        $('#txt_supp1').html('Payment For<strong>' + pay_for + '</strong>');
        $('#txt_supp').html('Payment Date<strong>' + pay_date + '</strong>');
        $('#txt_curr').html('From Account<strong>' + f_akun + '</strong>');
        $('#txt_confirm').html('To Account<strong>' + t_akun + '</strong>');
        $('#txt_confirm1').html('Cheque No<strong>' + cek_no + '</strong>');
        $('#txt_confirm2').html('Cheque Date<strong>' + cek_date + '</strong>');
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
    $("#form-simpan").on("click", "#simpan", function(){
        $("input[type=checkbox]:checked").each(function () {        
            var ceklist = document.getElementById('select').value;         
            var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
            var no_bpb_asal = $(this).closest('tr').find('td:eq(4)').attr('value');
            var create_user = '<?php echo $user ?>';
            var start_date = document.getElementById('start_date').value;
            var end_date = document.getElementById('end_date').value;
            var tgl_po = $(this).closest('tr').find('td:eq(9)').attr('value');
            var pterms = $(this).closest('tr').find('td:eq(10)').attr('value');        

            $.ajax({
                type:'POST',
                url:'insertfmvbpb.php',
                data: {'no_bpb':no_bpb, 'no_bpb_asal':no_bpb_asal, 'ceklist':ceklist, 'create_user':create_user, 'start_date':start_date, 'end_date':end_date, 'tgl_po':tgl_po, 'pterms':pterms},
                close: function(e){
                    e.preventDefault();
                },
                success: function(response){                
                    console.log(response);
                    window.location = 'verifikasibpb.php';

                },
                error:  function (xhr, ajaxOptions, thrownError) {
                 alert(xhr);
             }
         });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data saved successfully");
        }else{
            alert("Please check the BPB number");
        }        
    });
</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create-paymentvoucher.php";
    };
</script>

<script type="text/javascript">
    $("#select_all").click(function() {
      var c = this.checked;
      $(':checkbox').prop('checked', c);
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
