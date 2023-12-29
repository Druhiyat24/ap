<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">LIST PENGAJUAN CANCEL KONTRA BON</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" action="pengajuankb.php" method="post">        
        <div class="form-row">
            <div class="col-md-12">
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

            <div class="col-md-5">
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
                </select>
                </div>
        <div class="form-row">
            <div class="col-md-6"> 
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
               echo '';
            } ?>" 
            placeholder="Tanggal Awal">
            </div>

            <div class="col-md-6 mb-1">
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
               echo '';
            } ?>" 
            placeholder="Tanggal Akhir">
            </div>
        </div>

            <div class="input-group-append col">                                   
            <input type="submit" id="submit" value="Search" style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;">
            <input type="button" id="reset" value="Reset" style="margin-top: 30px; margin-bottom: 5px;">
            </div>                                                            
    </div>
</div>
</form>

<?php
        $querys = mysqli_query($conn1,"select Groupp, finance from userpassword where username = '$user'");
        $rs = mysqli_fetch_array($querys);
        $group = $rs['Groupp'];
        $fin = $rs['finance'];

        if($fin == '0'){
    echo '';
        }else{
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs"><span class="fa fa-plus"></span> Buat Pengajuan</button>';
    }
?>
    </div>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">

            
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">No Kontra Bon</th>
            <th style="text-align: center;vertical-align: middle;">Tanggal Pengajuan</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Nama Pengaju</th>
            <th style="text-align: center;vertical-align: middle;width: 400px;">Pesan Pengajuan cancel</th>
            <th style="text-align: center;vertical-align: middle;width: 150px;">Status</th>                                    
            <th style="text-align: center;vertical-align: middle;display: none;">file</th>            
            <th style="text-align: center;vertical-align: middle;display: none;">Total</th>
            <th style="text-align: center;vertical-align: middle;display: none">Currency</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Create By</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Action</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Jatuh Tempo</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl No Faktur</th>
            <th style="text-align: center;vertical-align: middle;display: none;">No Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">PPh Code</th>                                          
        </tr>
    </thead>
   
    <tbody>
<?php
// $nama_supp ='';
//     $status = '';
//     $start_date ='';
//     $end_date ='';
//     $awal = 3;
//     $akhir = 4;
//     $date_now = date("Y-m-d");                
//     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
//     $status = isset($_POST['status']) ? $_POST['status']: null;
//     $start_date = date("Y-m-d",strtotime($_POST['start_date']));
//     $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
//     }
//     if(empty($nama_supp) and empty($status) and empty($start_date) and empty($end_date)){
//      $sql = mysqli_query($conn2,"select no_kbon, tgl_kbon, nama_supp, SUM(subtotal), SUM(tax) as tax, SUM(total), curr, create_user, status, status_int, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code from kontrabon where status_int >2 and tgl_kbon = '$date_now' group by no_kbon");
//     }
//     elseif ($nama_supp == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {            
//      $sql = mysqli_query($conn2,"select no_kbon, tgl_kbon, nama_supp, SUM(subtotal), SUM(tax) as tax, SUM(total), curr, create_user, status, status_int, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code from kontrabon where status_int >2 group by no_kbon");
//     }
//     elseif ($nama_supp == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
//      $sql = mysqli_query($conn2,"select no_kbon, tgl_kbon, nama_supp, SUM(subtotal), SUM(tax) as tax, SUM(total), curr, create_user, status, status_int, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code from kontrabon where status_int >2 and tgl_kbon between '$start_date' and '$end_date' group by no_kbon");
//     }
//     elseif ($nama_supp != 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//      $sql = mysqli_query($conn2,"select no_kbon, tgl_kbon, nama_supp, SUM(subtotal), SUM(tax) as tax, SUM(total), curr, create_user, status, status_int, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code from kontrabon where status_int >2 and nama_supp = '$nama_supp' group by no_kbon");
//     }
//     elseif ($nama_supp != 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
//      $sql = mysqli_query($conn2,"select no_kbon, tgl_kbon, nama_supp, SUM(subtotal), SUM(tax) as tax, SUM(total), curr, create_user, status, status_int, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code from kontrabon where status_int >2 and nama_supp = '$nama_supp' and tgl_kbon between '$start_date' and '$end_date' group by no_kbon");
//     }
//     elseif ($nama_supp == 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//      $sql = mysqli_query($conn2,"select no_kbon, tgl_kbon, nama_supp, SUM(subtotal), SUM(tax) as tax, SUM(total), curr, create_user, status, status_int, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code from kontrabon where status_int >2 and status = '$status' group by no_kbon");
//     }
//     elseif ($nama_supp == 'ALL' and $status != 'ALL' and !empty($start_date) and !empty($end_date)) {
//      $sql = mysqli_query($conn2,"select no_kbon, tgl_kbon, nama_supp, SUM(subtotal), SUM(tax) as tax, SUM(total), curr, create_user, status, status_int, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code from kontrabon where status_int >2 and status = '$status' and tgl_kbon between '$start_date' and '$end_date' group by no_kbon");
//     }
//     elseif ($nama_supp != 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//      $sql = mysqli_query($conn2,"select no_kbon, tgl_kbon, nama_supp, SUM(subtotal), SUM(tax) as tax, SUM(total), curr, create_user, status, status_int, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code from kontrabon where status_int >2 and status = '$status' and nama_supp = '$nama_supp' group by no_kbon");
//     }
//     else{
    $sql = mysqli_query($conn2,"select cancel_kb.no_kbon as no_kbon, cancel_kb.nama_supp as nama_supp, cancel_kb.tgl_pengajuan as tgl_pengajuan, cancel_kb.nama_pengaju as nama_pengaju, cancel_kb.email as email, cancel_kb.pesan as pesan, kontrabon.status as status from cancel_kb INNER JOIN kontrabon on cancel_kb.no_kbon = kontrabon.no_kbon group by no_kbon");
// }
    while($row = mysqli_fetch_array($sql)){
    if (!empty($row)) {
    $status = $row['status'];         
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
            <td value = "'.$row['tgl_pengajuan'].'">'.date("d-M-Y",strtotime($row['tgl_pengajuan'])).'</td>
            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td value = "'.$row['nama_pengaju'].'">'.$row['nama_pengaju'].'</td>
            <td value = "'.$row['pesan'].'">'.$row['pesan'].'</td>';
           

            $querys = mysqli_query($conn1,"select Groupp, finance from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $fin = $rs['finance'];

            echo '<td width="75px;">';
            if($status == 'Approved' and $group != 'STAFF' and $fin == '1'){
                echo ' <i style="padding-right: 10px; padding-left: 5px;" ><b>Approved</b></i>';
            }elseif($status == 'Approved' and $group == 'STAFF' and $fin == '1'){
                echo ' <i style="padding-right: 10px; padding-left: 5px;" ><b>Approved</b></i>';
            }elseif($status == 'Draft' and $group != 'STAFF' and $fin == '1'){
                echo ' <a id="approve" href=""><button type="button" class="btn btn-success"><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;"></i></button></a>
                <a id="delete" href=""><button type="button" class="btn btn-danger"><i class="fa fa-trash" style="padding-right: 10px; padding-left: 5px;"></i></button></a>';       
            }elseif($status == 'Draft' and $group == 'STAFF' and $fin == '1') {
                echo ' <button type="button" class="btn btn-info"><i style="padding-right: 10px; padding-left: 5px;" ><b>Draft</b></i></button>';                
            }elseif($status == 'Cancel' and $group != 'STAFF' and $fin == '1') {
                echo ' <i style="padding-right: 10px; padding-left: 5px;" ><b>canceled</b></i> ';                
            }elseif($status == 'Cancel' and $group == 'STAFF' and $fin == '1') {
                echo ' <i style="padding-right: 10px; padding-left: 5px;" ><b>canceled</b></i> '; 
            }                                            
            echo '</td>
            
                                                                        
        </tr>';
        }
}?>
</tbody>                    
</table>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodalbon" data-target="#mymodalbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_kbon"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_kbon" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_tempo" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                           
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>         
                                
</div><!-- body-row END -->
</div>
</div>

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
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
    // $('#datatable').dataTable({
    //     "serverSide": true,
    //     "processing": true,
    //     "ajax": {
    //         url: "fetch_data_kontrabon.php",
    //         type: "post"
    //     }
    // });
    $('#datatable').dataTable();
    // $.fn.dataTable.ext.errMode = 'none';
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#approve", function(){                 
        var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approve_cancelkb.php',
            data: {'no_kbon':no_kbon, 'approve_user':approve_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#delete", function(){                 
        var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';        

        $.ajax({
            type:'POST',
            url:'cancel_cancelkb.php',
            data: {'no_kbon':no_kbon, 'cancel_user':cancel_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();                                                                            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodalbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(1)').text();
    var supp = $(this).closest('tr').find('td:eq(2)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(11)').text();
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(8)').attr('value');
    var status = $(this).closest('tr').find('td:eq(9)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(12)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(13)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(14)').text();                

    $.ajax({
    type : 'post',
    url : 'ajaxkbon.php',
    data : {'no_kbon': no_kbon},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_kbon);
    $('#txt_tgl_kbon').html('Tgl Kontrabon : ' + tgl_kbon + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_tgl_tempo').html('Tgl Jatuh Tempo : ' + tgl_tempo + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                                     
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "formcancelkb.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "listcancelkb.php";
};
</script>

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

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
