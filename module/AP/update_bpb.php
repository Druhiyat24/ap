<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h3 class="text-center">LIST UPDATE BPB</h3>
<div class="box">
    <div class="box header">
<form id="form-data" action="update_bpb.php" method="post">    
<div style="padding-left: 10px;padding-top: 5px;">
            <button style="-ms-transform: skew(6deg);-webkit-transform: skew(6deg);transform: skew(10deg);" id="btnbpb" type="button" class="btn-primary active btn-xs"><span></span>List Update BPB</button>
            <button style="-ms-transform: skew(6deg);-webkit-transform: skew(6deg);transform: skew(10deg);" id="btnfaktur" type="button" class="btn-secondary btn-xs"><span></span>List Data Faktur</button>
        </div>    
        <div class="form-row">
            <div class="col-md-6">
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
               echo date("d-m-Y");
            } ?>" 
            placeholder="Start Date" autocomplete='off' style="height: 33px;">
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
               echo date("d-m-Y");
            } ?>" 
            placeholder="Tanggal Akhir" style="height: 33px;">
            </div>
        </div>

            <div class="input-group-append col">                                   
           <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 5px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button>           
            </div>                                                            
    </div>
    <br>
</div>
</form>
</form>
<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Update Faktur'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '58'){
    echo '<button id="btnupdate_faktur" type="button" class="btn-primary btn-xs"><span class="fa fa-pencil-square-o"></span> Update Faktur</button>';
        }else{
    echo '';
        }
?>

<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Update Invoice'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '59'){
    echo '<button id="btnupdate_inv" type="button" class="btn-info btn-xs"><span class="fa fa-pencil-square-o"></span> Update Invoice</button>';
        }else{
    echo '';
        }
?>

<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Cancel Faktur & Invoice'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '60'){
    echo '<button id="btncancel" type="button" class="btn-danger btn-xs"><span class="fa fa-pencil-square-o"></span> Cancel</button>';
        }else{
    echo '';
        }
?>


<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Update Faktur Scan'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '75'){
    echo '<button id="btnscan_faktur" type="button" class="btn-success btn-xs"><span class="fa fa-qrcode"></span> Update</button>';
        }else{
    echo '';
        }
?>
 
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">

    <div class="table-responsive">        
<table id="datatable" class="table table-striped table-bordered text-nowrap" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center; vertical-align: middle;">No Dokumen</th>
            <th style="text-align: center; vertical-align: middle;">Tgl Dokumen</th>
            <th style="text-align: center; vertical-align: middle;">Nama Supplier</th>
            <th style="text-align: center; vertical-align: middle;">No Invoice</th>
            <th style="text-align: center; vertical-align: middle;">Tgl Invoice</th>
            <th style="text-align: center; vertical-align: middle;">No Faktur</th>
            <th style="text-align: center; vertical-align: middle;">Tgl Faktur</th>                         
            <th style="text-align: center; vertical-align: middle;">Status</th>                                    
            <th style="text-align: center; vertical-align: middle;">Created By</th>
            <th style="display: none;">Created By</th> 
            <th style="text-align: center; vertical-align: middle;">Action</th>                    
        </tr>
    </thead>
    
<tbody>
    <?php
    $nama_supp ='';
    $status = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date'])); 

        $sqlupdate1 = mysqli_query($conn2,"UPDATE bpb_faktur_inv
INNER JOIN bpb_new ON bpb_new.no_bpb = bpb_faktur_inv.no_bpb
SET bpb_faktur_inv.no_inv = bpb_new.upt_no_inv, 
    bpb_faktur_inv.tgl_inv = bpb_new.upt_tgl_inv
where bpb_faktur_inv.jenis = 'FAK' and bpb_faktur_inv.status != 'CANCEL'");               
    }


    if($nama_supp == 'ALL'){
    $sql = mysqli_query($conn2,"SELECT no_dok, tgl_dok, no_inv, tgl_inv, no_faktur, tgl_faktur, no_bpb, tgl_bpb, nama_supp, status, created_by, created_date, jenis FROM bpb_faktur_inv WHERE tgl_dok BETWEEN '$start_date' and '$end_date' group by no_dok order by SUBSTR(no_dok,14,5) asc");
    }      
    else{
    $sql = mysqli_query($conn2,"SELECT no_dok, tgl_dok, no_inv, tgl_inv, no_faktur, tgl_faktur, no_bpb, tgl_bpb, nama_supp, status, created_by, created_date, jenis FROM bpb_faktur_inv WHERE tgl_dok BETWEEN '$start_date' and '$end_date' and nama_supp = '$nama_supp' group by no_dok order by SUBSTR(no_dok,14,5) asc");
    }                 

    while($row = mysqli_fetch_array($sql)){
    $nofaktur = $row['no_faktur']; 
    $noinv = $row['no_inv'];  
        if ($noinv == '') {
            $no_inv = '-';
            $tgl_inv = '-';
        } else{
            $no_inv = $row['no_inv']; 
            $tgl_inv = date("d-M-Y",strtotime($row['tgl_inv']));
        }

        if ($nofaktur == '') {
            $no_faktur = '-';
            $tgl_faktur = '-';
        } else{
            $no_faktur = $row['no_faktur']; 
            $tgl_faktur = date("d-M-Y",strtotime($row['tgl_faktur']));
        }     
    if (!empty($row)) {
        echo'<tr style="font-size: 12px; text-align: center;">
            <td value="'.$row['no_dok'].'">'.$row['no_dok'].'</td>
            <td value="'.$row['tgl_dok'].'">'.date("d-M-Y",strtotime($row['tgl_dok'])).'</td>
            <td value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td value="'.$no_inv.'">'.$no_inv.'</td>
            <td value="'.$tgl_inv.'">'.$tgl_inv.'</td>
            <td value="'.$no_faktur.'">'.$no_faktur.'</td>
            <td value="'.$tgl_faktur.'">'.$tgl_faktur.'</td>
            <td style="" value="'.$row['status'].'">'.$row['status'].'</td>
            <td style="" value="'.$row['created_by'].'">'.$row['created_by'].'</td>
            <td style="display:none;" value="'.$row['jenis'].'">'.$row['jenis'].'</td>
            <td style="text-align : center">';

            $querys3 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Cancel Faktur & Invoice'");
                $rs3 = mysqli_fetch_array($querys3);
                $id_cancel = isset($rs3['id']) ? $rs3['id'] : 0;
            if($row['status'] == 'POST' && $id_cancel == '60'){
                echo '<a id="delete" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-trash" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;" onclick="alert_cancel();"> Cancel</i></button></a>';
            }else{
                echo '-';
            }
        echo '</td></tr>';
    }
                        
}?>
</tbody>                    
</table>
</div>

<!-- <a href="edit_faktur_invoice.php?no_dok='.base64_encode($row['no_dok']).' "><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-pencil-square-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Edit</i></button></a> -->

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-dark text-white">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h5 class="modal-title" id="txt_no_dok"></h5>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>      
          <div id="supplier" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_invdate" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_fakturdate" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div> 

    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>
  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>  
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
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<script type="text/javascript">
    document.getElementById('btnbpb').onclick = function () {
    location.href = "update_bpb.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btnfaktur').onclick = function () {
    location.href = "list_data_faktur.php";
};
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
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_dok = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_dok = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(2)').attr('value');
    var no_inv = $(this).closest('tr').find('td:eq(3)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(4)').attr('value'); 
    var no_fak = $(this).closest('tr').find('td:eq(5)').attr('value');
    var tgl_fak = $(this).closest('tr').find('td:eq(6)').attr('value');
    var jenis = $(this).closest('tr').find('td:eq(9)').attr('value');      

    $.ajax({
    type : 'post',
    url : 'ajax_updatebpb.php',
    data : {'no_dok': no_dok,'jenis': jenis,'no_inv': no_inv,'tgl_inv': tgl_inv,'no_fak': no_fak,'tgl_fak': tgl_fak},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_no_dok').html(no_dok);
    $('#txt_tgl').html('Tgl Dokumen : ' + tgl_dok + '');
    $('#supplier').html('Supplier : ' + supp + '');
    $('#txt_inv').html('No Invoice : ' + no_inv + '');        
    $('#txt_invdate').html('Tgl Invoice : ' + tgl_inv + '');   
    $('#txt_faktur').html('No Faktur : ' + no_fak + '');        
    $('#txt_fakturdate').html('Tgl Faktur : ' + tgl_fak + '');                      
});

</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#delete", function(){                 
        var no_dok  = $(this).closest('tr').find('td:eq(0)').attr('value');
        var jenis   = $(this).closest('tr').find('td:eq(9)').attr('value'); 
        var cancel_user = '<?php echo $user ?>';        

        $.ajax({
            type:'POST',
            url:'cancel_faktur_inv.php',
            data: {'no_dok':no_dok, 'jenis':jenis, 'cancel_user':cancel_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();
                // const form = document.createElement('form-data');
                // document.body.appendChild(form);
                // form.submit();                                                                            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script>
function alert_cancel() {
  alert("Data Berhasil di Cancel");
  // location.reload();
}
</script>

<script type="text/javascript">
    document.getElementById('btnupdate_inv').onclick = function () {
    location.href = "form_update_inv.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btnscan_faktur').onclick = function () {
    location.href = "form_scan_faktur.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btncancel').onclick = function () {
    location.href = "form_cancel_faktur_inv.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btnupdate_faktur').onclick = function () {
    location.href = "form_update_faktur.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "formverifikasibppb.php";
};
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "update_bpb.php";
};
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
