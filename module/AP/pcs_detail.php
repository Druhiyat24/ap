<?php include '../header.php';
ini_set('date.timezone', 'Asia/Jakarta');
$insert_date = date("Y-m-d H:i:s");?>

    <!-- MAIN -->
    <div class="col p-4">
        <h4 class="text-center">PAYABLE CARD STATEMENT</h4>
<div class="box">
    <div class="box header">

        
       <form id="form-data" action="pcs_detail.php" method="post">        
        <div class="form-row">
            <div class="col-md-4">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
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
                $sql = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC",$conn1);
                while ($row = mysql_fetch_array($sql)) {
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
            placeholder="Start Date" autocomplete='off'>
            </div>

            <div class="col-md-2 mb-1">
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
            placeholder="End Date" autocomplete='off'>
            </div>

            <div class="col-md-4 input-group-append col">                                   
            <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);" onclick="getdatabulan()"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
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
</div>
</form> 


<?php
    //     $querys = mysqli_query($conn1,"select Groupp, finance from userpassword where username = '$user'");
    //     $rs = mysqli_fetch_array($querys);
    //     $group = $rs['Groupp'];
    //     $fin = $rs['finance'];

    //     if($fin == '0'){
    // echo '';
    //     }else{
    // echo '<button id="btncreate" type="button" class="btn-primary btn-xs"><span class="fa fa-pencil-square-o"></span> Create</button>';
    // // echo "<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    // // echo '<button id="btndraft" type="button" class="btn-warning btn-xs" hidden><span class="fa fa-bars"></span> List Draft</button>';
    // }
?>
    </div>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">
                </br>
    <?php 
        include 'pcs detail/ap_bpb.php'; 
 
        include 'pcs detail/ap_kb.php'; 
 
        include 'pcs detail/ap_lp.php'; 

        include 'pcs detail/ap_sum_supp.php';

        include 'pcs detail/ap_sum_type1.php';

        include 'pcs detail/ap_sum_type2.php';

        include 'pcs detail/ap_summary.php';
    ?>


<style type="text/css">
     .modal-dialog-full-width {
        width: 85% !important;
        height: 85% !important;
        margin: 210px 255px !important;
        padding: 0 !important;
        max-width:none !important;


    }

    .modal-content-full-width  {
        height: auto !important;
        min-height: 85% !important;
        border-radius: 0 !important;
        background-color: white !important;
        max-height: calc(100% - 200px);
         overflow-y: scroll;


    }

    .modal-header-full-width  {
        border-bottom: 1px solid #9ea2a2 !important;
    }

    .modal-footer-full-width  {
        border-top: 1px solid #9ea2a2 !important;
    }
</style>

   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade center" id="mymodalbon" data-target="#mymodalbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
       <div class="modal-dialog-full-width modal-dialog momodel modal-fluid" role="document">
        <div class="modal-content-full-width modal-content ">
            <div class=" modal-header-full-width   modal-header text-left">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_kbon"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_kbon" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
     <!--      <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_tempo" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->        
          <!-- <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
          <!-- <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                                          
          <div id="details" class="modal-body col-15" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        <div class="modal-footer-full-width  modal-footer">
                <button type="button" class="btn btn-danger btn-md btn-rounded" data-dismiss="modal">Close</button>
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

<!-- <script>
    $(document).ready(function() {

    $('#datatable').dataTable();

     $("[data-toggle=tooltip]").tooltip();
    
} );
</script> -->

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        startDate : "14-04-2022",
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
    function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
    }

    function getdatabulan() {
        var tgl_akhir = document.getElementById('end_date').value;
        var newdate = tgl_akhir.split("-").reverse().join("-");       
        const currentMonth = new Date(newdate);
        var tahun  = currentMonth.getFullYear()
        var bulan1 = currentMonth.getMonth();
        var bulan2 = currentMonth.getMonth() + 1;
        var bulan3 = currentMonth.getMonth() + 2;
        var bulan4 = currentMonth.getMonth() + 3;
        var bulan5 = currentMonth.getMonth() + 4;
        var bulan6 = currentMonth.getMonth() + 5;
        if (bulan1 > 11) {
            var tahun_1 = tahun +1;
        }else{
            var tahun_1 = tahun;
        }
        if (bulan2 > 11) {
            var tahun_2 = tahun +1;
        }else{
            var tahun_2 = tahun;
        }
        if (bulan3 > 11) {
            var tahun_3 = tahun +1;
        }else{
            var tahun_3 = tahun;
        }
        if (bulan4 > 11) {
            var tahun_4 = tahun +1;
        }else{
            var tahun_4 = tahun;
        }
        if (bulan5 > 11) {
            var tahun_5 = tahun +1;
        }else{
            var tahun_5 = tahun;
        }
        if (bulan6 > 11) {
            var tahun_6 = tahun +1;
        }else{
            var tahun_6 = tahun;
        }
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var bulan_1 =  months[bulan1]; 
        var bulan_2 =  months[bulan2]; 
        var bulan_3 =  months[bulan3]; 
        var bulan_4 =  months[bulan4]; 
        var bulan_5 =  months[bulan5]; 
        var bulan_6 =  months[bulan6];

        var data1 = bulan_1 + ' ' + tahun_1; 
        var data2 = bulan_2 + ' ' + tahun_2; 
        var data3 = bulan_3 + ' ' + tahun_3; 
        var data4 = bulan_4 + ' ' + tahun_4; 
        var data5 = bulan_5 + ' ' + tahun_5; 
        var data6 = bulan_6 + ' ' + tahun_6; 

        // alert(data1 + data2 + data3 + data4 + data5 + data6);

        $("#bulan1").val(data1);

        // $('#bulan_1').val(bulan_1 + ' ' + tahun_1);
        // $('#bulan_2').val(bulan_2 + ' ' + tahun_2);
        // $('#bulan_3').val(bulan_3 + ' ' + tahun_3);
        // $('#bulan_4').val(bulan_4 + ' ' + tahun_4);
        // $('#bulan_5').val(bulan_5 + ' ' + tahun_5);
        // $('#bulan_6').val(bulan_6 + ' ' + tahun_6);     
    }

</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(9)', function(){                
    $('#mymodalbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(3)').text();
    var supp = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(11)').text();
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(8)').attr('value');
    var status = $(this).closest('tr').find('td:eq(9)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(12)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(13)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(14)').text();
    var start_date = document.getElementById('start_date').value;
    var end_date = document.getElementById('end_date').value;                

    $.ajax({
    type : 'post',
    url : 'ajaxkartu_hutang_new.php',
    data : {'supp': supp, 'start_date':start_date, 'end_date':end_date},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(supp);
    $('#txt_tgl_kbon').html('Periode : ' + start_date + ''+ " - " +''+ end_date +'');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    // $('#txt_tgl_tempo').html('No PO: ' + tgl_tempo + '');
    // $('#txt_curr').html('PO Date : ' + curr + '');        
    // $('#txt_create_user').html('Create By : ' + create_user + '');
    // $('#txt_status').html('Status : ' + status + '');
    // $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    // $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    // $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                                     
});

</script>



<!-- <script type="text/javascript">
    document.getElementById('btnbpb').onclick = function () {
    location.href = "kartuhutang.php";
};
</script>
<script type="text/javascript">
    document.getElementById('btnkb').onclick = function () {
    location.href = "pcs2.php";
};
</script>
<script type="text/javascript">
    document.getElementById('btnlp').onclick = function () {
    location.href = "pcs.php";
};
</script> -->


<script type="text/javascript">
    document.getElementById('btndraft').onclick = function () {
    location.href = "draft_kb.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "pcs_detail.php";
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
