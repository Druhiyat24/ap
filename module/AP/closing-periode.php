<?php include '../header.php' ?>

<!-- MAIN -->
<div class="col p-4">
    <h2 class="text-center">CLOSING PERIODE</h2>
    <div class="box">
        <div class="box header">

            <form id="form-data" action="closing-periode.php" method="post">        
                <div class="form-row">
                   <div class="col-md-3">
                    <label for="tahun_periode"><b>Tahun Periode</b></label>            
                    <select class="form-control selectpicker" name="tahun_periode" id="tahun_periode" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                        <?php
                        $tahun_ini = date('Y');
                        $tahun_periode = '';

                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tahun_periode']) && $_POST['tahun_periode'] !== '') {
                            $tahun_periode = $_POST['tahun_periode'];
                        } else {
                            $tahun_periode = $tahun_ini; 
                        }

                        $sql = mysql_query("SELECT DISTINCT tahun FROM tbl_closing_periode", $conn1);
                        while ($row = mysql_fetch_array($sql)) {
                            $data = $row['tahun'];
                            $isSelected = ($data == $tahun_periode) ? ' selected="selected"' : '';
                            echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';
                        }
                        ?>
                    </select>

                </div>  

                <div class="input-group-append col">                                   
                    <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 0px;margin-right: 15px;border: 0;
                   line-height: 1;
                   padding: -2px 8px;
                   font-size: 1rem;
                   text-align: center;
                   color: #fff;
                   text-shadow: 1px 1px 1px #000;
                   border-radius: 6px;
                   background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>

<!--     <?php
        $status = isset($_POST['status']) ? $_POST['status']: null;

        if($status == 'ALL'){
            echo '<a target="_blank" href="ekspor_lp_all.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($status == 'draft'){
            echo '<a target="_blank" href="ekspor_lp_draft.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }elseif($status == 'Approved'){
            echo '<a target="_blank" href="ekspor_lp_app.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($status == 'Cancel'){
            echo '<a target="_blank" href="ekspor_lp_cancel.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }elseif($status == 'Closed'){
            echo '<a target="_blank" href="ekspor_lp_closed.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }else{
            $filterr = ""; 
        }
    ?>  -->
</div>                                                            
</div>
<br/>
</div>
</form> 

</div>
<div class="box body">
    <div class="row">       
        <div class="col-md-12">


            <table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
                <thead>
                    <tr class="thead-dark">
                        <th style="text-align: center;vertical-align: middle;width: 16%;">Bulan</th>
                        <th style="text-align: center;vertical-align: middle;width: 16%;">Tahun</th>
                        <th style="text-align: center;vertical-align: middle;width: 16%;">Status</th>
                        <th style="text-align: center;vertical-align: middle;width: 16%;">Closed By</th>
                        <th style="text-align: center;vertical-align: middle;width: 16%;">Closed Date</th>
                        <th style="text-align: center;vertical-align: middle;width: 20%;">Action</th>
                        <th style="display: none;">Action</th>
                        
                    </tr>
                </thead>
                
                <tbody>
                    <?php
                    $tahun_periode =date("Y");
                    $date_now = date("Y-m-d");                
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $tahun_periode = isset($_POST['tahun_periode']) ? $_POST['tahun_periode']: null;             
                    }
                    
                    $sql = mysqli_query($conn1, "SELECT id, kode_closing, bulan, tahun, status_closing, tgl_awal, keterangan, CONCAT(COALESCE(lock_by,'-'),' (',lock_date,')') user_lock, lock_by, lock_date FROM tbl_closing_periode where tahun = '$tahun_periode' order by id asc");

                    while($row = mysqli_fetch_array($sql)){

                        $status_target = $row['status_closing'] == 'Closed' ? 'Open' : 'Closed';
                        $btn_class = $status_target == 'Open' ? 'btn-info' : 'btn-warning';
                        $icon_class = $status_target == 'Open' ? 'fa-unlock text-dark' : 'fa-lock text-danger';

                        if ($row['lock_date'] == null || $row['lock_date'] == '') {
                            $lock_date = '-';
                        }else{
                            $lock_date = date("d-M-Y H:i:s",strtotime($row['lock_date']));
                        }

                        echo '<tr style="font-size:13px;text-align:center;">
                        <td style="width: 150px;" value = "'.$row['bulan'].'">'.date("F",strtotime($row['tgl_awal'])).'</td>
                        <td style="width: 150px;" value = "'.$row['tahun'].'">'.$row['tahun'].'</td>';
                        if ($row['status_closing'] == 'Closed') {
                            echo '<td style="text-align: center;"><i class="fas fa-lock text-warning"></i> <b>Closed</b></td>';
                        }else{
                            echo '<td style="text-align: center;"><i class="fas fa-circle text-success"></i> <b>Open</b></td>';
                        }

                        echo '
                        <td style="width: 150px;" value = "'.$row['lock_by'].'">'.$row['lock_by'].'</td>
                        <td style="width: 150px;" value = "'.$row['lock_date'].'">'.$lock_date.'</td>';
                        echo '
                        <td style="width: 150px;">
                        <button class="btn btn-sm '.$btn_class.' open-status-confirm"
                        data-id="'.$row['id'].'"
                        data-status="'.$status_target.'"
                        data-keterangan="'.$row['keterangan'].'"
                        data-action="'.$status_target.'">
                        <i class="fas '.$icon_class.' me-1"> '.$status_target.'</i>
                        </button>
                        </td>
                        <td style="display: none" value = "'.$row['id'].'">'.$row['id'].'</td>';

                        echo '</tr>';
                    }?>
                </tbody>                    
            </table>

        </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
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
<!--           <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
  <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
  <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
  <!--         <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                    
  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
</div>
</div>
</div>
    <!-- /.modal-content 
  </div>
      /.modal-dialog 
  </div> -->         
  
</div><!-- body-row END -->
</div>
</div>

<!-- Modal Edit Status -->
<div class="modal fade" id="modalUpdate" tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formUpdate">
        <div class="modal-header">
          <h5 class="modal-title" id="modalUpdateLabel">Ubah Status Periode</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="id" id="update_id">
          <input type="hidden" name="status" id="update_status">

          <div class="mb-3">
            <label for="update_keterangan" class="form-label">Keterangan</label>
            <input type="text" class="form-control" name="keterangan" id="update_keterangan" placeholder="Masukkan keterangan perubahan">
        </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
  </div>
</form>
</div>
</div>
</div>


<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        $('#datatable').DataTable({
            "order": [[6, 'asc']]
        });

        
        $("[data-toggle=tooltip]").tooltip();
        
    } );
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hasPosted = <?php echo isset($_POST['tahun_periode']) ? 'true' : 'false'; ?>;
        if (!hasPosted) {
            document.getElementById('tahun_periode').form.submit();
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.open-status-confirm').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const status = this.dataset.status;
                const keterangan = this.dataset.keterangan || '';
                const action = this.dataset.action;
                var update_user = '<?php echo $user ?>';

                let title = '';
                let confirmButtonText = '';
                let icon = '';

                if (action === 'Closed') {
                    title = 'Yakin ingin menutup periode ini?';
                    confirmButtonText = 'Ya, Tutup';
                    icon = 'warning';
                } else {
                    title = 'Yakin ingin membuka kembali periode ini?';
                    confirmButtonText = 'Ya, Buka';
                    icon = 'question';
                }

                Swal.fire({
                    title: title,
                    text: "Status akan diubah menjadi: " + status,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: confirmButtonText
                }).then((result) => {
                    if (result.isConfirmed) {
                    // Kirim AJAX ke server
                    fetch('update_status_closing.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            id: id,
                            status: status,
                            keterangan: keterangan,
                            update_user: update_user
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Status berhasil diubah',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: res.message || 'Terjadi kesalahan'
                            });
                        }
                    });
                }
            });
            });
        });
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
    $("table tbody tr").on("click", "#active", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'activebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Active");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
           }
       });
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#deactive", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'deactivebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Deactive");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
           }
       });
    });
</script>


<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
        $('#mymodal').modal('show');
        var no_ib = $(this).closest('tr').find('td:eq(0)').attr('value');
        var date = $(this).closest('tr').find('td:eq(1)').text();
        var reff = $(this).closest('tr').find('td:eq(2)').attr('value');
        var reff_doc = $(this).closest('tr').find('td:eq(3)').attr('value');
        var oth_doc = $(this).closest('tr').find('td:eq(4)').attr('value');
        var curr = "IDR";

        $.ajax({
            type : 'post',
            url : 'ajax_cashin.php',
            data : {'no_ib': no_ib},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_bpb').html(no_ib);
        $('#txt_tglbpb').html('Date : ' + date + '');
        $('#txt_no_po').html('Refference : ' + reff + '');
        $('#txt_supp').html('Refference Document : ' + reff_doc + '');
    // $('#txt_top').html('Other Document : ' + oth_doc + '');
    // $('#txt_curr').html('Kas Account : ' + akun + '');        
    $('#txt_confirm').html('Currency : ' + curr + '');
    // $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create-cashin.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "closing-periode.php";
    };
</script>

<script>
    function alert_cancel() {
      alert("Master Bank Deactive");
      location.reload();
  }
  function alert_approve() {
      alert("Master Bank Active");
      location.reload();
  }
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
    
</body>

</html>