<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM USER ROLE</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-9 mb-3">
            <label for="nama_supp"><b>Full Name</b></label>            
            <div class="input-group">
            <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                echo $nama_supp; 
            ?>">

    <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose Username</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
            <label for="nama_supp"><b>Username</b></label>
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(FullName) from userpassword order by FullName ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['FullName'];
                    if($row['FullName'] == $_POST['nama_supp']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                <div class="modal-footer">
                    <button type="submit" id="send" name="send" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                </div>           
            </form>
        </div>
      </div>


        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>

            <div class="input-group-append col">
            <input style="border: 0;
    line-height: 1;
    padding: 0 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="mysupp" id="mysupp" data-target="#mymodal" data-toggle="modal" value="Select">
            <input type="hidden" name="bpbvalue" id="bpbvalue" value="">      
        </div>
    </div>
</div>                   
    </div>
</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:8%;">-</th>
                            <th style="width:30%;">User Name</th>
                            <th style="width:30%;">Full Name</th>                            
                            <th style="width:32%;">Description</th>                                                                              
                            <!--<th style="width:50px;">Delete</th>-->
                        </tr>
                    </thead>

            <tbody>
            <?php
            $sql = mysqli_query($conn1,"select * from userpassword where FullName = '$nama_supp'");

            while($row = mysqli_fetch_array($sql)){      

                    echo '<tr>
                            <td style="width:10px;"><input type="radio" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                             <td style="width:50px;" value1="'.$row['username'].'">'.$row['username'].'</td>
                            <td style="width:50px;" value2="'.$row['FullName'].'">'.$row['FullName'].'</td>                                                                                                                       
                            <td value="'.$row['Groupp'].'">'.$row['Groupp'].'</td>
                        </tr>';
                        
                }                
                    ?>
            </tbody>                    
            </table> 
        </div>

        <div class="col-md-3">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="50%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th ><input type="checkbox" id="select_all"></th>
                            <th >Menu</th>                                                                           
                            <!--<th style="width:50px;">Delete</th>-->
                        </tr>
                    </thead>

            <tbody>
            <?php
            $sql = mysqli_query($conn2,"select * from menurole where id != '35' and NOT EXISTS (select * from useraccess where menurole.menu = useraccess.menu and useraccess.fullname = '$nama_supp')");
            while($row = mysqli_fetch_array($sql)){            
                    echo '<tr>
                            <td ><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td  menus="'.$row['menu'].'">'.$row['menu'].'</td>                                                                                                                       
                        </tr>';
                        
                }                
                    ?>
            </tbody>                    
            </table> 
            </div> 
<div class="box footer col-md-9">   
        <form id="form-simpan">
            </br>                  
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 100px;"><b>User Name</b></label>
            <div class="col-md-6 mb-3">                              
                <input type="text" class="form-control-plaintext" name="user" id="user" value="" placeholder="-" style="font-size: 14px;text-align: left;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Full Name</b></label>
            <div class="col-md-6 mb-3">                              
                <input type="text" class="form-control-plaintext" name="fullname" id="fullname" value="" placeholder="-" style="font-size: 14px;text-align: left;" readonly>
            </div>
            </div> 
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Menu</b></label>
            <div class="col-md-6 mb-5">                              
                <input type="text" class="form-control-plaintext" name="menu" id="menu" value="" placeholder="-" style="font-size: 14px;text-align: left;" readonly>
            </div>
            </div>         
          <!--  <div class="form-row col">
                <label for="carabayar" class="col-form-label" style="width: 100px;"><b>Menu</b></label> 
            <div class="col-md-3 mb-3">                              
                <select class="form-control selectpicker" name="menu" id="menu" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Select</option>                
                <?php 

                // $sqll = mysqli_query($conn1,"select distinct(username) from userpassword where FullName = '$nama_supp'");
                // $rowl = mysqli_fetch_array($sqll);
                // $name = $rowl['username'];

                // $sqlll = mysqli_query($conn2,"select menu from useraccess where username = '$name'");
                // while ($rowll = mysqli_fetch_array($sqlll)) {
                //     $menu = $rowll['menu'];
                // }

                $sql = mysqli_query($conn2,"select distinct(menu) from menurole where id != '1' order by id ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['menu'];
                    if($row['menu'] == $_POST['menu']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>    
            </div>
            </div> -->

           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='userrole.php'"><span class="fa fa-angle-double-left"></span> Back</button>               
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalpo" data-target="#mymodalpo" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_po"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                            
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
    $('#mytable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

</script>


<script type="text/javascript">    

    $("input[type=checkbox]").change(function(){
    var menus = '';

    $("input[type=checkbox]:checked").each(function () {        
    var menu = $(this).closest('tr').find('td:eq(1)').attr('menus') || '';           

    menus += menu+",";
    });

    $("#menu").val(menus);
    }); 

</script>

<script type="text/javascript">    

    $("input[type=radio]").change(function(){
    var username = '';
    var fullname = '';

    $("input[type=radio]:checked").each(function () {        
    var user = $(this).closest('tr').find('td:eq(1)').attr('value1');
    var fname = $(this).closest('tr').find('td:eq(2)').attr('value2'); 
              
    username = user;
    fullname = fname;
    });
    $("#user").val(username);
    $("#fullname").val(fullname);
    }); 

</script>



<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        $("input[type=checkbox]:checked").each(function () {
        var username = document.getElementById('user').value;        
        var fullname = document.getElementById('fullname').value;
        var menu = $(this).closest('tr').find('td:eq(1)').attr('menus');   
        var create_user = '<?php echo $user; ?>';   
        if(menu != '' && username !=''){        
        $.ajax({
            type:'POST',
            url:'insertuser.php',
            data: {'username':username, 'fullname':fullname, 'menu':menu, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert("Data saved successfully");
                window.location = 'userrole.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    }else{
        document.getElementById('user').focus();
        alert("Username and Menu can't be empty");
    }
        });                
        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Please check the User Name");
        }else{
            alert("data submitted successfully");
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
    $('#mymodalpo').modal('show');
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var no_bpb = $(this).closest('tr').find('td:eq(8)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(3)').attr('value');
    var tgl_po2 = $(this).closest('tr').find('td:eq(3)').text();
    var supp = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');   

    $.ajax({
    type : 'post',
    url : 'ajaxpodp.php',
    data : {'no_po': no_po, 'no_bpb':no_bpb},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_po').html(no_po);
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po2 + '');
    $('#txt_supp2').html('Supplier : ' + supp + '');
    $('#txt_curr').html('Currency : ' + curr + '');                               
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
