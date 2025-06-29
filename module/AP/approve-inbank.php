<?php include '../header.php' ?>
<style>
  /* 1. Hover baris tabel */
  #mytable tbody tr:hover {
    background-color: #f0f8ff;
    cursor: pointer;
  }

  /* 2. Checkbox agar lebih besar dan terpusat */
  #mytable input[type="checkbox"] {
    transform: scale(1.2);
    margin: 0 auto;
    display: block;
  }

  /* 3. Tabel padding agar tidak terlalu sempit */
  .table td, .table th {
    padding: 0.5rem;
    vertical-align: middle;
  }

  /* 4. Header sticky (tetap di atas saat scroll) */
  table.dataTable thead th {
    position: sticky;
    top: 0;
    background-color: #343a40;
    color: white;
    z-index: 2;
  }


  /* 6. Styling tombol aksi */
  .btn {
    border-radius: 10px;
    padding: 6px 15px;
    font-size: 14px;
  }

  .btn i {
    margin-right: 5px;
  }

</style>

<!-- MAIN -->    
<div class="container-fluid mt-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-center">
      <h5 class="mb-0"><i class="fa fa-university"></i> APPROVE INCOMING BANK</h5>
  </div>

  <form id="form-data" action="approve-inbank.php" method="post">
     <!--  <div class="card-body" style="max-height: 350px; overflow-y: auto;"> -->
         <div class="card-body container-1">
            <div class="table-responsive">
            <table id="mytable" class="table table-bordered table-striped table-hover text-center"cellspacing="0" width="100%">
              <thead class="thead-dark">
                <tr>
                  <th style="width: 6%"><input type="checkbox" id="select_all"></th>
                  <th style="width: 18%">No Bank In</th>
                  <th style="width: 22%">Source</th>
                  <th style="width: 13%">Date</th>
                  <th style="width: 11%">Currency</th>
                  <th style="width: 15%">Amount</th>
                  <th style="width: 15%">Outstanding</th>
                  <th style="display: none;">Status</th>
                  <th style="display: none;">Ref</th>
                  <th style="display: none;">Akun</th>
                  <th style="display: none;">Bank</th>
                  <th style="display: none;">Deskripsi</th>
              </tr>
          </thead>
          <tbody>
            <?php
            $sql = mysqli_query($conn2, "SELECT doc_num, customer, date, ref_data, akun, bank, curr, amount, outstanding, status, deskripsi FROM tbl_bankin_arcollection WHERE status = 'Draft' GROUP BY doc_num ORDER BY id ASC");
            while($row = mysqli_fetch_array($sql)){
              echo'<tr>
              <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
              <td style="width:50px; text-align : left" value="'.$row['doc_num'].'">'.$row['doc_num'].'</td>
              <td style="width:50px; text-align : left" value="'.$row['customer'].'">'.$row['customer'].'</td> 
              <td style="width:100px; text-align : left" value="'.$row['date'].'">'.date("d-M-Y",strtotime($row['date'])).'</td>                                                                                             
              <td style="width:50px; text-align : center" value="'.$row['curr'].'">'.$row['curr'].'</td>
              <td style="width:50px; text-align : right" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
              <td style="width:50px; text-align : right" value="'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td> 
              <td style=" display: none" value="'.$row['status'].'">'.$row['status'].'</td> 
              <td style="display: none" value="'.$row['ref_data'].'">'.$row['ref_data'].'</td>
              <td style="display: none" value="'.$row['akun'].'">'.$row['akun'].'</td>
              <td style="display: none" value="'.$row['bank'].'">'.$row['bank'].'</td>
              <td style="display: none" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>                          
              </tr>';   
          }
          ?>
      </tbody>
  </table>
</div>
</div>

<div class="card-footer text-right">
    <button type="submit" class="btn btn-sm btn-success" name="approve" id="approve">
      <i class="fa fa-thumbs-up"></i> Approve
  </button>
  <button type="submit" class="btn btn-sm btn-danger" name="cancel" id="cancel">
      <i class="fa fa-ban"></i> Cancel
  </button>
</div>
</form>
</div>
</div>


<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_bpb"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
                  <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>          
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
              </div>
          </div>
      </div>
      <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div> 

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

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
        $('#mytable').DataTable({
            scrollY: "350px",
            scrollCollapse: true,
            paging: false,
            searching: true,
            bFilter: true,
            info: false,
            ordering: true
        });



        $("[data-toggle=tooltip]").tooltip();

    } );
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
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
        $('#mymodal').modal('show');
        var no_ib = $(this).closest('tr').find('td:eq(1)').attr('value');
        var supp = $(this).closest('tr').find('td:eq(2)').text();
        var refdoc = $(this).closest('tr').find('td:eq(8)').attr('value');
        var akun = $(this).closest('tr').find('td:eq(9)').attr('value');
        var bank = $(this).closest('tr').find('td:eq(10)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(4)').attr('value');
        var status = $(this).closest('tr').find('td:eq(7)').attr('value');
        var desk = $(this).closest('tr').find('td:eq(11)').text();

        $.ajax({
            type : 'post',
            url : 'ajaxbankin.php',
            data : {'no_ib': no_ib, 'refdoc': refdoc},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_bpb').html(no_ib);
        $('#txt_tglbpb').html('Customer : ' + supp + '');
        $('#txt_no_po').html('Reff Doc : ' + refdoc + '');
        $('#txt_supp').html('Account : ' + akun + '');
        $('#txt_top').html('Bank : ' + bank + '');
        $('#txt_curr').html('Currency : ' + curr + '');        
        $('#txt_confirm').html('Status : ' + status + '');
        $('#txt_tgl_po').html('Description : ' + desk + '');                    
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
  $("#form-data").on("click", "#approve", function (e) {
    e.preventDefault(); // cegah submit langsung

    const selected = $("input[name='select[]']:checked");

    if (selected.length < 1) {
      Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: 'Silakan ceklist No Bank In terlebih dahulu!'
      });
      return;
    }

    Swal.fire({
      title: 'Yakin ingin Approve data terpilih?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Approve',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        let approvedCount = 0;
        const approve_user = '<?php echo $user ?>';

        selected.each(function () {
          const no_bi = $(this).closest('tr').find('td:eq(1)').attr('value');

          $.ajax({
            type: 'POST',
            url: 'approve_inbank.php',
            data: { 'no_bi': no_bi, 'approve_user': approve_user },
            success: function (response) {
              approvedCount++;
              if (approvedCount === selected.length) {
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: 'Data berhasil di-approve!',
                  timer: 1500,
                  showConfirmButton: false
                }).then(() => {
                  window.location = 'approve-inbank.php';
                });
              }
            },
            error: function (xhr, ajaxOptions, thrownError) {
              Swal.fire({
                icon: 'error',
                title: 'Terjadi kesalahan!',
                text: xhr.responseText || thrownError
              });
            }
          });
        });
      }
    });
  });
</script>

<script type="text/javascript">
  $("#form-data").on("click", "#cancel", function (e) {
    e.preventDefault(); // cegah submit langsung

    const selected = $("input[name='select[]']:checked");

    if (selected.length < 1) {
      Swal.fire({
        icon: 'warning',
        title: 'Oops...',
        text: 'Silakan ceklist No Bank In terlebih dahulu!'
      });
      return;
    }

    Swal.fire({
      title: 'Yakin ingin Cancel data terpilih?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Cancel',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        let cancelledCount = 0;
        const cancel_user = '<?php echo $user ?>';

        selected.each(function () {
          const no_bi = $(this).closest('tr').find('td:eq(1)').attr('value');

          $.ajax({
            type: 'POST',
            url: 'cancel_inbank.php',
            data: { 'no_bi': no_bi, 'cancel_user': cancel_user },
            success: function (response) {
              cancelledCount++;
              if (cancelledCount === selected.length) {
                Swal.fire({
                  icon: 'success',
                  title: 'Dibatalkan!',
                  text: 'Data berhasil dibatalkan.',
                  timer: 1500,
                  showConfirmButton: false
                }).then(() => {
                  window.location = 'approve-inbank.php';
                });
              }
            },
            error: function (xhr, ajaxOptions, thrownError) {
              Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: xhr.responseText || thrownError
              });
            }
          });
        });
      }
    });
  });
</script>


<script type="text/javascript">
    $("#select_all").click(function() {
      var c = this.checked;
      $(':checkbox').prop('checked', c);
  });  
</script>

<script type="text/javascript">
    document.getElementById('btnpv').onclick = function () {
        location.href = "approve-pv.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('btnib').onclick = function () {
        location.href = "approve-inbank.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('btnob').onclick = function () {
        location.href = "approve-outbank.php";
    };
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
