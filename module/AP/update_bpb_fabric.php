<?php include '../header.php' ?>

<style>
    #bpb-table {
        width: 100%;
        border-collapse: collapse;
    }

    #bpb-table th, #bpb-table td {
        padding: 8px 12px;
        text-align: center;
        vertical-align: middle;
    }

    #bpb-table th {
        background-color: #343a40; /* dark background for header */
        color: white;
    }

    #bpb-table td input[type="text"] {
        width: 100%;
        padding: 6px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .modal-footer {
        text-align: center;
    }

    .modal-header, .modal-footer {
        padding: 15px;
    }

    .btn-success {
        font-size: 14px;
        font-weight: bold;
    }

    /* Tambahkan scroll untuk tabel */
    .table-responsive {
        max-height: 300px; /* Atur tinggi maksimum tabel */
        overflow-y: auto; /* Scroll vertikal */
    }

    /* Untuk scroll horizontal */
    .table-responsive table {
        width: 100%;
        min-width: 1000px; /* Lebar minimum tabel agar scroll horizontal muncul */
    }
</style>

<!-- MAIN -->
<div class="col p-4">
    <h2 class="text-center">FABRIC WAREHOUSE RECEIVING LIST</h2>
    <div class="box">
        <div class="box header">

            <form id="form-data" action="update_bpb_fabric.php" method="post">        
                <div class="form-row">
                 <div class="col-md-3">
                  <label for="nama_supp"><b>Supplier</b></label>            
                  <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" >
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


            <div class="col-md-2 mb-3"> 
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

         <div class="col-md-2 mb-3"> 
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
     <div class="input-group-append col">                                   
        <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 15px;margin-right: 15px;border: 0;
        line-height: 1;
        padding: -2px 8px;
        font-size: 1rem;
        text-align: center;
        color: #fff;
        text-shadow: 1px 1px 1px #000;
        border-radius: 6px;
        background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
        <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 15px;margin-right: 15px;border: 0;
        line-height: 1;
        padding: -2px 8px;
        font-size: 1rem;
        text-align: center;
        color: #fff;
        text-shadow: 1px 1px 1px #000;
        border-radius: 6px;
        background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button>

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

<!-- <?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Cash'");
$rs = mysqli_fetch_array($querys);
$id = isset($rs['id']) ? $rs['id'] : 0;

if($id == '39'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>';
}else{
    echo '';
}
?> -->
</div>
<div class="box body">
    <div class="row">       
        <div class="col-md-12">
            <form id="formdata2">
                <table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
                    <thead>
                        <tr class="thead-dark">
                            <th style="text-align: center;vertical-align: middle;">No BPB</th>
                            <th style="text-align: center;vertical-align: middle;">BPB Date</th>
                            <th style="text-align: center;vertical-align: middle;">Supplier</th>
                            <th style="text-align: center;vertical-align: middle;">PO Number</th>
                            <th style="text-align: center;vertical-align: middle;">BC Type</th>
                            <th style="text-align: center;vertical-align: middle;">Purchase Type</th>
                            <th style="text-align: center;vertical-align: middle;">Commercial Type</th>
                            <th style="text-align: center;vertical-align: middle;">Action</th>

                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $nama_supp ='';
                        $start_date ='';
                        $end_date ='';
                        $date_now = date("Y-m-d");                
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;  
                            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                            $end_date = date("Y-m-d",strtotime($_POST['end_date']));               
                        }

                        if($nama_supp == 'ALL' ){
                           $where = "a.tgl_dok between '$start_date' and '$end_date'";
                       }else{
                           $where = "supplier = '$nama_supp' and a.tgl_dok between '$start_date' and '$end_date'";  
                       }


                       $sql = mysql_query("select a.id, no_dok, tgl_dok, supplier, IF(no_po is null,'-',no_po) no_po, type_bc, type_pch, tipe_com from whs_inmaterial_fabric a left join po_header b on b.pono = a.no_po left join po_header_draft c on c.id= b.id_draft where $where and a.`status` != 'Cancel' order by a.id asc",$conn1);

                       while($row = mysqli_fetch_array($sql)){

                        echo '<tr style="font-size:12px;text-align:left;">
                        <td style="width: 150px;" value = "'.$row['no_dok'].'">'.$row['no_dok'].'</td>
                        <td style="width: 100px;" value = "'.$row['tgl_dok'].'">'.date("d-M-Y",strtotime($row['tgl_dok'])).'</td>
                        <td style="width: 150px;" value = "'.$row['supplier'].'">'.$row['supplier'].'</td>
                        <td style="width: 150px;" value = "'.$row['no_po'].'">'.$row['no_po'].'</td>
                        <td style="width: 150px;" value = "'.$row['type_bc'].'">'.$row['type_bc'].'</td>
                        <td style="width: 150px;" value = "'.$row['type_pch'].'">'.$row['type_pch'].'</td>
                        <td style="width: 150px;" value = "'.$row['tipe_com'].'">'.$row['tipe_com'].'</td>';

                  //   echo "<td style='width: 50px;text-align:center;'>
                  //   <button class='update-btn btn-info btn-xs' data-id='{$row['id']}' data-bpb='{$row['no_dok']}' data-tgldok='{$row['tgl_dok']}'>
                  //       <i class='fa fa-pencil-square'></i> Update
                  //   </button>
                  // </td>";
                        echo '<td style="width: 50px;text-align:center;">
                        <button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-info"><i class="fa fa-pencil-square"></i> Update</button></td>'; 
                        echo '</tr>';
                    }?>
                </tbody>                    
            </table>
        </form>

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

<!-- Modal -->
<div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_nobpb" style="text-align: left; font-weight: bold;"></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form id="modal-form2" method="post">
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <div class="table-responsive">
                                    <table id="bpb-table" class="table table-bordered table-striped">
                                        <thead class="bg-dark text-white">
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">No WS</th>
                                                <th class="text-center">Id JO</th>
                                                <th class="text-center">Id Item</th>
                                                <th class="text-center">Desc Item</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Unit</th>
                                                <th class="text-center">Price</th>
                                                <th class="text-center">Update Price</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bpb-table-body">
                                            <!-- Data akan diisi oleh JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-8">
                            </div>
                            <div class="col-md-4">
                                <div class="modal-footer">
                                    <button type="submit" id="send2" name="send2" class="btn btn-success btn-sm" style="width: 100%; border-radius: 6px;">
                                        <span class="fa fa-thumbs-up"></span> Save
                                    </button>
                                </div>
                            </div>
                        </div>           
                    </form>
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
    $("#formdata2").on("click", "#btnupdate", function(){ 
        var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value'); 
        var tgl_bbp = $(this).closest('tr').find('td:eq(1)').attr('value'); 

        $.ajax({
            type: 'POST',
        url: 'get_data_bpb_fabric.php', // Ganti dengan path PHP file yang mengambil data
        data: {'no_bpb':no_bpb},
        dataType: 'json',
        success: function (data) {
            console.log(data);
            $('#bpb-table-body').empty();

            // Iterasi data dan tambahkan baris ke tabel
            data.forEach(function (item) {
                var row = `<tr>
                <td>${item.id}</td>
                <td>${item.no_ws}</td>
                <td style= "width: 60px;">${item.id_jo}</td>
                <td style= "width: 60px;">${item.id_item}</td>
                <td>${item.desc_item}</td>
                <td>${item.qty}</td>
                <td>${item.unit}</td>
                <td>${item.price}</td>
                <td><input type="text" style="font-size: 12px;" class="form-control" id="price_update" name="price_update[${item.id}]" value="${item.price_update !== null ? item.price_update : ''}"></td>
                </tr>`;
                $('#bpb-table-body').append(row);
            });
            $('#txt_nobpb').html(no_bpb);

            $('#mymodal2').modal('show');

            },
        error: function () {
            alert('Error retrieving data.');
        }
    });
        // $('#txt_type').val(no_bpb);
        // $('#txt_id').val(tgl_bbp);

    });


    $("#modal-form2").on("submit", function(e) {
        e.preventDefault(); // Mencegah form untuk submit secara default

        // Mengambil data dari tabel yang telah diperbarui
        var updatedData = [];
        $('#bpb-table-body tr').each(function() {
            var id = $(this).find('td:eq(0)').text();
            var updatedPrice = $(this).find('input[name="price_update[' + id + ']"]').val();
            updatedData.push({ id: id, price: updatedPrice });
        });

        // Kirim data ke server menggunakan AJAX
        $.ajax({
            type: 'POST',
            url: 'update_price_bpb_fabric.php', // Ganti dengan PHP file untuk menyimpan data
            data: { updatedData: updatedData },
            success: function(response) {
                alert('Data updated successfully!');
                $('#mymodal2').modal('hide');
            },
            error: function() {
                alert('Error saving data.');
            }
        });
    });
</script>

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


<!-- <script type="text/javascript">     
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

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create-cashin.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "update_bpb_fabric.php";
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