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

  .swal-popup-small {
    font-size: 13px !important;
  }
  .swal-title-small {
    font-size: 14px !important;
  }
  .swal-confirm-small,
  .swal-cancel-small {
    font-size: 12px !important;
    padding: 4px 10px;
  }


</style>

<!-- MAIN -->    
<div class="container-fluid mt-4">
  <div class="card border-secondary mb-3">
    <div class="card-header text-center">
      <h5 class="mb-0"><i class="fa fa-university"></i> REPOST JOURNAL BANK OUT</h5>
    </div>

    <form id="form-data" action="repost-bank-out.php" method="post">
     <!--  <div class="card-body" style="max-height: 350px; overflow-y: auto;"> -->
       <div class="card-body container-1">
        <div class="table-responsive">
          <table id="mytable" class="table table-bordered table-striped table-hover text-center"cellspacing="0" width="100%">
            <thead class="thead-dark">
              <tr>
               <th style="width:10px;"><input type="checkbox" id="select_all"></th>                        
               <th style="width:100px;">No Bank Out</th>
               <th style="width:70px;">Bank Out Date</th>
               <th style="width:70px;">Type</th>
               <th style="width:50px;">Debit</th>                                                                                
               <th style="width:80px;">Credit</th>
               <th style="width:80px;">Different</th>
               <th style="display: none;">-</th>
               <th style="display: none;">-</th>
               <th style="display: none;">-</th>
               <th style="display: none;">-</th>
               <th style="display: none;">-</th>
             </tr>
           </thead>
           <tbody>
            <?php
            $sql = mysqli_query($conn2,"select a.*, b.status, b.reff_doc, b.akun, b.bank, b.deskripsi from (select no_journal, tgl_journal, type_journal, sum(debit * rate) debit_idr, sum(credit * rate) credit_idr, (sum(debit * rate) - sum(credit * rate)) diff from tbl_list_journal where tgl_journal BETWEEN '2026-01-01' and '2026-12-31' and type_journal = 'Payment Voucher' GROUP BY no_journal) a INNER JOIN b_bankout_h b on a.no_journal = b.no_bankout where diff != 0 AND (diff >= 1 OR diff <= -1)");

            while($row = mysqli_fetch_array($sql)){                                          
              echo'<tr>
              <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
              <td style="width:50px; text-align : center" value="'.$row['no_journal'].'">'.$row['no_journal'].'</td>
              <td style="width:100px; text-align : center" value="'.$row['tgl_journal'].'">'.date("d-M-Y",strtotime($row['tgl_journal'])).'</td>                                                                                             
              <td style="width:50px; text-align : center" value="'.$row['type_journal'].'">'.$row['type_journal'].'</td> 
              <td style="width:50px; text-align : center" value="'.$row['debit_idr'].'">'.number_format($row['debit_idr'],2).'</td>
              <td style="width:50px; text-align : center" value="'.$row['credit_idr'].'">'.number_format($row['credit_idr'],2).'</td> 
              <td style="width:50px; text-align : center" value="'.$row['diff'].'">'.number_format($row['diff'],2).'</td>   
              <td style="display: none;" value="'.$row['status'].'">'.$row['status'].'</td>
              <td style="display: none; text-align : center" value="'.$row['reff_doc'].'">'.$row['reff_doc'].'</td>
              <td style="display: none; text-align : center" value="'.$row['akun'].'">'.$row['akun'].'</td> 
              <td style="display: none; text-align : center" value="'.$row['bank'].'">'.$row['bank'].'</td>  
              <td style="display: none; text-align : center" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>                         
              </tr>';                

            } ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer text-right">
      <button type="submit" class="btn btn-sm btn-success" name="approve" id="approve">
        <i class="fa fa-thumbs-up"></i> Repost
      </button>
    </div>
  </form>
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
          <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                     
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
    var no_ob = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(2)').text();
    var refdoc = $(this).closest('tr').find('td:eq(8)').attr('value');
    var akun = $(this).closest('tr').find('td:eq(9)').attr('value');
    var bank = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(4)').attr('value');
    var status = $(this).closest('tr').find('td:eq(7)').attr('value');
    var desk = $(this).closest('tr').find('td:eq(11)').text();

    $.ajax({
      type : 'post',
      url : 'ajaxbankout.php',
      data : {'no_ob': no_ob, 'refdoc': refdoc},
      success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
  }
});         
        //make your ajax call populate items or what even you need
        $('#txt_bpb').html(no_ob);
        $('#txt_no_po').html('Reff Doc : ' + refdoc + '');
        $('#txt_supp').html('Account : ' + akun + '');
        $('#txt_top').html('Bank : ' + bank + '');
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
        text: 'Silakan ceklist No Bank Out terlebih dahulu!',
        customClass: {
          title: 'swal-title-small',
          popup: 'swal-popup-small',
          confirmButton: 'swal-confirm-small'
        }
      });
      return;
    }

    Swal.fire({
      title: 'Aksi ini akan menghapus jurnal yang lama dan membuat jurnal yang baru.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Repost',
      cancelButtonText: 'Batal',
      customClass: {
        title: 'swal-title-small',
        popup: 'swal-popup-small',
        confirmButton: 'swal-confirm-small',
        cancelButton: 'swal-cancel-small'
      }
    })
    .then((result) => {
      if (result.isConfirmed) {
        let approvedCount = 0;
        const approve_user = '<?php echo $user ?>';

        selected.each(function () {
          const no_dok = $(this).closest('tr').find('td:eq(1)').attr('value');

          $.ajax({
            type: 'POST',
            url: 'insert-report-bankout.php',
            data: { 'no_dok': no_dok, 'approve_user': approve_user },
            success: function (response) {
              approvedCount++;
              if (approvedCount === selected.length) {
                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: 'Data berhasil di Repost',
                  timer: 1500,
                  showConfirmButton: false,
                  customClass: {
                    title: 'swal-title-small',
                    popup: 'swal-popup-small',
                    confirmButton: 'swal-confirm-small',
                  }
                }).then(() => {
                  window.location = 'repost-bank-out.php';
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
        text: 'Silakan ceklist No Bank In terlebih dahulu!',
        customClass: {
          title: 'swal-title-small',
          popup: 'swal-popup-small',
          confirmButton: 'swal-confirm-small',
        }
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
                  window.location = 'repost-bank-out.php';
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
    location.href = "repost-bank-out.php";
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
