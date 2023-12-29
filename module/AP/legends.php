<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">Actions Account Payable</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" >        
        <div class="form-row">
            <div class="col-md-9 mb-3">   
            </br>         
            <label for="tanggal" style="font-size: 18px"><b>Status BPB </b></label>
            </br>  
             <label for="tanggal" style="font-size: 15px"><i>1. GMF</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana nomor BPB dibuat atau diajukan untuk di verifikasi dikeadaan ini juga status invoice BPB di SET Waiting</label>
             </br>  
             <label for="tanggal" style="font-size: 15px"><i>2. GMF - PCH</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana nomor BPB sudah diverifikasi /  di approve</label>
               </br>  
             <label for="tanggal" style="font-size: 15px"><i>3. Cancel</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana nomor BPB sudah dicancel /label>             
            </div>
</div>
</form>

 <form id="form-data" >        
        <div class="form-row">
            <div class="col-md-9 mb-3">   
            </br>         
            <label for="tanggal" style="font-size: 18px"><b>Status Invoice BPB </b></label>
            </br>  
             <label for="tanggal" style="font-size: 15px"><p>Status ini dipakai sebagai syarat masuk atau dibuatkan nya kontra bon</p></label> 
            </br>  
             <label for="tanggal" style="font-size: 15px"><i>1. Waiting</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana nomor BPB belum dibuatkan dan dapat dibuatkan kontrabon</label>
             </br>  
             <label for="tanggal" style="font-size: 15px"><i>2. Invoiced</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana nomor BPB sudah dibuatkan kontrabon dan tidak dapat dibuatkan kembali kontrabon</label>            
            </div>
</div>
</form>
<form id="form-data" >        
        <div class="form-row">
            <div class="col-md-9 mb-3">   
            </br>         
            <label for="tanggal" style="font-size: 18px"><b>Status FTR </b></label>
            </br>  
             <label for="tanggal" style="font-size: 15px"><i>1. Draft</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana nomor PO baru dibuatkan FTR dikeadaan ini juga status invoice FTR di SET Waiting</label>
             </br>  
             <label for="tanggal" style="font-size: 15px"><i>2. Approved</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana FTR sudah diverifikasi /  di approve</label>
               </br>  
             <label for="tanggal" style="font-size: 15px"><i>3. Cancel</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana FTR sudah dicancel /label>             
            </div>
</div>
</form>
 <form id="form-data" >        
        <div class="form-row">
            <div class="col-md-9 mb-3">   
            </br>         
            <label for="tanggal" style="font-size: 18px"><b>Status Invoice FTR </b></label>
            </br>  
             <label for="tanggal" style="font-size: 15px"><p>Status ini dipakai sebagai syarat masuk atau dibuatkan nya kontra bon</p></label> 
            </br>  
             <label for="tanggal" style="font-size: 15px"><i>1. Waiting</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana FTR belum dibuatkan dan dapat dibuatkan kontrabon</label>
             </br>  
             <label for="tanggal" style="font-size: 15px"><i>2. Invoiced</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana FTR sudah dibuatkan kontrabon dan tidak dapat dibuatkan kembali kontrabon</label>            
            </div>
</div>
</form>


<form id="form-data" >        
        <div class="form-row">
            <div class="col-md-9 mb-3">   
            </br>         
            <label for="tanggal" style="font-size: 18px"><b>Status Kontra Bon Reguler </b></label>
            </br>  
            <label for="tanggal" style="font-size: 18px"><p>No BPB yang bisa dibuatkan kontrabon harus memiliki 2 syarat yaitu Status harus GMF-PCH dan Invoice harus waiting</p></label>
            </br>  
             <label for="tanggal" style="font-size: 15px"><i>1. Post --> 2 </i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon baru dibuat, jadi semua BPB yang dibuatkan kontrabon akan berstatus Post / 2</label>
             <p>Dikeadaan ini kontrabon memiliki 2 action yaitu Approved dan Cancel. Manager Finance bisa menerima data dengan meng klik tombol Approved atau menolak dengan mengklik tombol Cancel. Tidak hanya itu, dikeadaan ini Staff atau si pembuat kontra bon bisa mengajukan Cancel pada Menu Maintail Batal Kontrabon.</p>
             </br>  
             <label for="tanggal" style="font-size: 15px"><i>2. Approved --> 4</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon yang dibuat itu disetujui oleh Manager Finance dan siap untuk dibuatkan Payment.</label>
             </br> 
             </br> 
             <label for="tanggal" style="font-size: 15px"><i>3. Cancel --> 1 </i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon yang dibuat itu dibatalkan oleh Manager finance </label>
             <p>Cancel ini hanya dapat dilakukan oleh Manager Finance, Staff hanya dapat mengajukan pembatalan Kontra Bon.</p>
             </br>  
             <label for="tanggal" style="font-size: 15px"><i>4. Draft --> 3 </i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon sudah diajukan dimenu Maintain Batal Kontra bon oleh Staff </label>
             <p>Dikeadaan ini kontrabon memiliki 2 action yaitu Approved dan Cancel. Manager Finance bisa menerima data yang akan di Cancel dengan meng klik tombol Approved atau menolak dengan mengklik tombol Cancel. pada saat Manager Finance menerima ajuan data tersebut, data akan berstatus Cancel pada Kontra Bon dan akan dikembalikan status BPB menjadi Waiting. jika Manager Finance menolak maka data kontrabon akan tetap berstatus Post.</p>
             </br>        
            </div>
</div>
</form>

<form id="form-data" >        
        <div class="form-row">
            <div class="col-md-9 mb-3">   
            </br>         
            <label for="tanggal" style="font-size: 18px"><b>Status Kontra Bon By FTR</b></label>
            </br>  
            <label for="tanggal" style="font-size: 18px"><p>No FTR yang bisa dibuatkan kontrabon harus memiliki 2 syarat yaitu Status harus Approved dan Invoice harus waiting</p></label>
            </br>  
             <label for="tanggal" style="font-size: 15px"><i>1. Post --> 2 </i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon baru dibuat, jadi semua FTR yang dibuatkan kontrabon akan berstatus Post / 2</label>
             <p>Dikeadaan ini kontrabon memiliki 2 action yaitu Approved dan Cancel. Manager Finance bisa menerima data dengan meng klik tombol Approved atau menolak dengan mengklik tombol Cancel. Tidak hanya itu, dikeadaan ini Staff atau si pembuat kontra bon bisa mengajukan Cancel pada Menu Maintail Batal Kontrabon.</p>
             </br>  
             <label for="tanggal" style="font-size: 15px"><i>2. Approved --> 4</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon yang dibuat itu disetujui oleh Manager Finance dan siap untuk dibuatkan Payment.</label>
             </br> 
             </br> 
             <label for="tanggal" style="font-size: 15px"><i>3. Cancel --> 1 </i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon yang dibuat itu dibatalkan oleh Manager finance </label>
             <p>Cancel ini hanya dapat dilakukan oleh Manager Finance, Staff hanya dapat mengajukan pembatalan Kontra Bon.</p>
             </br>  
             <label for="tanggal" style="font-size: 15px"><i>4. Draft --> 3 </i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon sudah diajukan dimenu Maintain Batal Kontra bon oleh Staff </label>
             <p>Dikeadaan ini kontrabon memiliki 2 action yaitu Approved dan Cancel. Manager Finance bisa menerima data yang akan di Cancel dengan meng klik tombol Approved atau menolak dengan mengklik tombol Cancel. pada saat Manager Finance menerima ajuan data tersebut, data akan berstatus Cancel pada Kontra Bon dan akan dikembalikan status FTR menjadi Waiting. jika Manager Finance menolak maka data kontrabon akan tetap berstatus Post.</p>
             </br>        
            </div>
</div>
</form>

<form id="form-data" >        
        <div class="form-row">
            <div class="col-md-9 mb-3">   
            </br>         
            <label for="tanggal" style="font-size: 18px"><b>Status Maintain Kontra Bon </b></label>
            </br>  
             <label for="tanggal" style="font-size: 15px">syarat masuk menu ini yaitu kontra bon harus berstatus post</label> 
            </br>  
             <label for="tanggal" style="font-size: 15px"><i>1. Draft </i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon sudah diajukan untuk dibatalkan</label>
             <p>Dikeadaan ini kontrabon memiliki 2 action yaitu Approved dan Cancel. Manager Finance bisa menerima data dengan meng klik tombol Approved atau menolak dengan mengklik tombol Cancel. Jika tombol cancel di klik maka status kontra bon akan kembali menjadi Post(2) dan jika tombol Approved di klik maka status kontrabon menjadi Cancel(1).</p>
             </br>  
             <label for="tanggal" style="font-size: 15px"><i>2. Approved</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon yang dibuat pengajuan batal itu disetujui oleh Manager Finance maka data kontrabon berstatus Cancel.</label>
             </br> 
             </br> 
             <label for="tanggal" style="font-size: 15px"><i>3. Cancel</i></label> 
             </br>  
             <label for="tanggal">suatu keadaan dimana kontrabon yang dibuat pengajuan batal itu dibatalkan oleh Manager finance, maka data kontra bon berstatus kembali Post</label>
             <p>Cancel ini hanya dapat dilakukan oleh Manager Finance, Staff hanya dapat mengajukan pembatalan Kontra Bon.</p>
             
             </br>        
            </div>
</div>
</form>
</div>
</div>
</div>
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
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>


<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
