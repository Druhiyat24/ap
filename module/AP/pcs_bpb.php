<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">PAYABLE CARD STATEMENT DETAIL</h2>
<div class="box">
    <div class="box header">

        
       <form id="form-data" action="pcs_bpb.php" method="post">        
        <div class="form-row">
            <div class="col-md-6">
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

        <div class="form-row">
            <div class="col-md-6"> 
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

            <div class="col-md-6 mb-1">
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
    <div style="margin-left: 30px">
        <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        echo '<a target="_blank" href="pcs_detail_bpb.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> EXCEL AP - BPB</i></button></a>';
        ?>
    </div> 
    <br/>

    <div class="tableFix" style="height: 350px;">        
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">Nama Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Bpb Number</th>
            <th style="text-align: center;vertical-align: middle;">Bpb Date</th>
            <th style="text-align: center;vertical-align: middle;">Currency</th>
            <th style="text-align: center;vertical-align: middle;">Begining Balance</th>
            <th style="text-align: center;vertical-align: middle;">Addition</th>
            <th style="text-align: center;vertical-align: middle;">Reduction</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance</th>                                                                            
        </tr>
    </thead>
   
    <tbody>
<?php
    $value = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }
   if(empty($nama_supp) and empty($start_date) and empty($end_date)){
    echo '';
    }
    elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {            
     $sql = mysqli_query($conn1,"select b.Supplier,a.bpbno_int,bpbdate,curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.confirm = 'y' and price != '0' and status_retur = 'N' and bpbdate > '2022-04-13' group by a.bpbno_int order by bpbdate asc");
    }
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn1,"select b.Supplier,a.bpbno_int,bpbdate,curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.confirm = 'y' and price != '0' and status_retur = 'N' and bpbdate between '2022-04-14' and '$end_date' group by a.bpbno_int order by bpbdate asc");
    }
    elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn1,"select b.Supplier,a.bpbno_int,bpbdate,curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.confirm = 'y' and price != '0' and status_retur = 'N' and b.Supplier = '$nama_supp' group by a.bpbno_int order by bpbdate asc");
    }
    else{
    $sql = mysqli_query($conn1,"select b.Supplier,a.bpbno_int,bpbdate,curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.confirm = 'y' and price != '0' and status_retur = 'N' and bpbdate between '2022-04-14' and '$end_date' and b.Supplier = '$nama_supp' group by a.bpbno_int order by bpbdate asc");
}

    $sa_akhir_  = 0;
    $kurang_    = 0;
    $sa_awal_   = 0;
    $tambah_    = 0;

   while($row = mysqli_fetch_array($sql)){
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $tgl_bpb = $row['bpbdate'];
    $no_bpb = $row['bpbno_int'];

    $sqlap = mysqli_query($conn1,"select no_bpb, is_invoiced from bpb_new where tgl_bpb > '2022-04-13' and no_bpb = '$no_bpb' GROUP BY no_bpb");
    $rowap = mysqli_fetch_array($sqlap);
    $no_bpbap = isset($rowap['no_bpb']) ? $rowap['no_bpb'] : null;


    if($no_bpbap != null){
        $kurang = $row['total'];
    }else{
        $kurang = 0;
    }

    if($tgl_bpb < $start_date){
        $sa_awal = $row['total'];
    }else{
        $sa_awal = 0;
    }

    if($tgl_bpb >= $start_date){
        $tambah = $row['total'];
    }else{
        $tambah = 0;
    }

    $sa_akhir = $sa_awal + $tambah - $kurang; 
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['Supplier'].'">'.$row['Supplier'].'</td>
            <td value="'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>
            <td value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>                            
            <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
            <td style="text-align:right;" value = "'.$sa_awal.'">'.number_format($sa_awal,2).'</td>
            <td style="text-align:right;" value = "'.$tambah.'">'.number_format($tambah,2).'</td>         
            <td style="text-align:right;" value = "'.$kurang.'">'.number_format($kurang,2).'</td>
            <td style="text-align:right;" value = "'.$sa_akhir.'">'.number_format($sa_akhir,2).'</td>
             ';
            
        // }

}
// echo '
//             <tr >
//             <th colspan = "4" style="text-align: center;vertical-align: middle;">Total</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($sa_awal_,2).'</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($tambah_,2).'</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($kurang_,2).'</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($sa_akhir_,2).'</th>                                                                            
//         </tr>';
?>


                                                         
</tbody>                    
</table>
</div>
</br>


    <div style="margin-left: 30px">
        <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        echo '<a target="_blank" href="pcs_detail_kbon.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> EXCEL AP - KONTRABON </i></button></a>';
        ?>
    </div> 
</br>

    <div class="tableFix" style="height: 350px;">        
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">Nama Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Kontrabon Number</th>
            <th style="text-align: center;vertical-align: middle;">Kontrabon Date</th>
            <th style="text-align: center;vertical-align: middle;">Currency</th>
            <th style="text-align: center;vertical-align: middle;">Begining Balance</th>
            <th style="text-align: center;vertical-align: middle;">Addition</th>
            <th style="text-align: center;vertical-align: middle;">Reduction</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance</th>                                                                            
        </tr>
    </thead>
   
    <tbody>
<?php
    $value = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }
   if(empty($nama_supp) and empty($start_date) and empty($end_date)){
    echo '';
    }
    elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {            
     $sql = mysqli_query($conn1,"select nama_supp, no_kbon, tgl_kbon,curr, if(balance = '0',if(curr = 'USD', sum(total + pph_fgn),sum(total + pph_idr)),0) as bayar, if(curr = 'USD', sum(total + pph_fgn),sum(total + pph_idr)) as totalori,if(curr = 'USD', sum(total + pph_fgn),0) as total_sdh_kbusd , if(curr = 'IDR', sum(total + pph_idr), sum((total + pph_fgn) * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_kbon )))) as total_sdh_kbidr from kontrabon_h where status !='CANCEL' and tgl_kbon > '2022-04-13' GROUP BY no_kbon order by tgl_kbon asc");
    }
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn1,"select nama_supp, no_kbon, tgl_kbon,curr, if(balance = '0',if(curr = 'USD', sum(total + pph_fgn),sum(total + pph_idr)),0) as bayar, if(curr = 'USD', sum(total + pph_fgn),sum(total + pph_idr)) as totalori,if(curr = 'USD', sum(total + pph_fgn),0) as total_sdh_kbusd , if(curr = 'IDR', sum(total + pph_idr), sum((total + pph_fgn) * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_kbon )))) as total_sdh_kbidr from kontrabon_h where status !='CANCEL' and tgl_kbon between '2022-04-14' and '$end_date' GROUP BY no_kbon order by tgl_kbon asc");
    }
    elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn1,"select nama_supp, no_kbon, tgl_kbon,curr, if(balance = '0',if(curr = 'USD', sum(total + pph_fgn),sum(total + pph_idr)),0) as bayar, if(curr = 'USD', sum(total + pph_fgn),sum(total + pph_idr)) as totalori,if(curr = 'USD', sum(total + pph_fgn),0) as total_sdh_kbusd , if(curr = 'IDR', sum(total + pph_idr), sum((total + pph_fgn) * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_kbon )))) as total_sdh_kbidr from kontrabon_h where status !='CANCEL' and nama_supp = '$nama_supp' GROUP BY no_kbon order by tgl_kbon asc");
    }
    else{
    $sql = mysqli_query($conn1,"select nama_supp, no_kbon, tgl_kbon,curr, if(balance = '0',if(curr = 'USD', sum(total + pph_fgn),sum(total + pph_idr)),0) as bayar, if(curr = 'USD', sum(total + pph_fgn),sum(total + pph_idr)) as totalori,if(curr = 'USD', sum(total + pph_fgn),0) as total_sdh_kbusd , if(curr = 'IDR', sum(total + pph_idr), sum((total + pph_fgn) * if((select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_kbon ) is null, (select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and id IN (SELECT MAX(id) FROM masterrate)),(select ROUND(rate,2) as rate FROM masterrate where v_codecurr = 'HARIAN' and tanggal = tgl_kbon )))) as total_sdh_kbidr from kontrabon_h where status !='CANCEL' and nama_supp = '$nama_supp' and tgl_kbon between '2022-04-14' and '$end_date' GROUP BY no_kbon order by tgl_kbon asc");
}

    $sa_akhir_  = 0;
    $kurang_    = 0;
    $sa_awal_   = 0;
    $tambah_    = 0;

   while($row = mysqli_fetch_array($sql)){
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $tgl_kbon = $row['tgl_kbon'];

    $kurang = $row['bayar'];

    if($tgl_kbon < $start_date){
        $sa_awal = $row['totalori'];
    }else{
        $sa_awal = 0;
    }

    if($tgl_kbon >= $start_date){
        $tambah = $row['totalori'];
    }else{
        $tambah = 0;
    }

    $sa_akhir = $sa_awal + $tambah - $kurang; 
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td value="'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
            <td value="'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>                            
            <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
            <td style="text-align:right;" value = "'.$sa_awal.'">'.number_format($sa_awal,2).'</td>
            <td style="text-align:right;" value = "'.$tambah.'">'.number_format($tambah,2).'</td>         
            <td style="text-align:right;" value = "'.$kurang.'">'.number_format($kurang,2).'</td>
            <td style="text-align:right;" value = "'.$sa_akhir.'">'.number_format($sa_akhir,2).'</td>
             ';
            
        // }

}
// echo '
//             <tr >
//             <th colspan = "4" style="text-align: center;vertical-align: middle;">Total</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($sa_awal_,2).'</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($tambah_,2).'</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($kurang_,2).'</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($sa_akhir_,2).'</th>                                                                            
//         </tr>';
?>
                                                         
</tbody>                    
</table>
</div>
</br>


    <div style="margin-left: 30px">
        <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        echo '<a target="_blank" href="pcs_detail_lp.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> EXCEL AP - LIST PAYMENT </i></button></a>';
        ?>
    </div> 
</br>

    <div class="tableFix" style="height: 350px;">       
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">Nama Supplier</th>
            <th style="text-align: center;vertical-align: middle;">List Payment Number</th>
            <th style="text-align: center;vertical-align: middle;">List Payment Date</th>
            <th style="text-align: center;vertical-align: middle;">Currency</th>
            <th style="text-align: center;vertical-align: middle;">Begining Balance</th>
            <th style="text-align: center;vertical-align: middle;">Addition</th>
            <th style="text-align: center;vertical-align: middle;">Reduction</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance</th>                                                                            
        </tr>
    </thead>
   
    <tbody>
<?php
    $value = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }
   if(empty($nama_supp) and empty($start_date) and empty($end_date)){
    echo '';
    }
    elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {            
     $sql = mysqli_query($conn1,"select nama_supp, no_payment, tgl_payment,curr, sum(total_kbon) as total, if(status_int = '5',sum(total_kbon),0) as bayar from list_payment where status != 'Cancel' and tgl_payment > '2022-04-13' GROUP BY no_payment order by tgl_payment asc");
    }
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn1,"select nama_supp, no_payment, tgl_payment,curr, sum(total_kbon) as total, if(status_int = '5',sum(total_kbon),0) as bayar from list_payment where status != 'Cancel' and tgl_payment between '2022-04-14' and '$end_date' GROUP BY no_payment order by tgl_payment asc");
    }
    elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn1,"select nama_supp, no_payment, tgl_payment,curr, sum(total_kbon) as total, if(status_int = '5',sum(total_kbon),0) as bayar from list_payment where status != 'Cancel' and nama_supp = '$nama_supp' GROUP BY no_payment order by tgl_payment asc");
    }
    else{
    $sql = mysqli_query($conn1,"select nama_supp, no_payment, tgl_payment,curr, sum(total_kbon) as total, if(status_int = '5',sum(total_kbon),0) as bayar from list_payment where status != 'Cancel' and nama_supp = '$nama_supp' and tgl_payment between '2022-04-14' and '$end_date' GROUP BY no_payment order by tgl_payment asc");
}

    $sa_akhir_  = 0;
    $kurang_    = 0;
    $sa_awal_   = 0;
    $tambah_    = 0;

   while($row = mysqli_fetch_array($sql)){
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $tgl_kbon = $row['tgl_payment'];

    $kurang = $row['bayar'];

    if($tgl_kbon < $start_date){
        $sa_awal = $row['total'];
    }else{
        $sa_awal = 0;
    }

    if($tgl_kbon >= $start_date){
        $tambah = $row['total'];
    }else{
        $tambah = 0;
    }

    $sa_akhir = $sa_awal + $tambah - $kurang; 
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td value="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td value="'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>                            
            <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
            <td style="text-align:right;" value = "'.$sa_awal.'">'.number_format($sa_awal,2).'</td>
            <td style="text-align:right;" value = "'.$tambah.'">'.number_format($tambah,2).'</td>         
            <td style="text-align:right;" value = "'.$kurang.'">'.number_format($kurang,2).'</td>
            <td style="text-align:right;" value = "'.$sa_akhir.'">'.number_format($sa_akhir,2).'</td>
             ';
            
        // }

}
// echo '
//             <tr >
//             <th colspan = "4" style="text-align: center;vertical-align: middle;">Total</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($sa_awal_,2).'</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($tambah_,2).'</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($kurang_,2).'</th>
//             <th style="text-align: right;vertical-align: middle;">'.number_format($sa_akhir_,2).'</th>                                                                            
//         </tr>';
?>                                                         
</tbody>                    
</table>
</div>

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

<!-- <script type="text/javascript">
    $("table tbody tr").on("click", "#approve", function(){                 
        var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvekbon.php',
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
            url:'cancelkbon.php',
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
</script> -->

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
    location.href = "pcs_bpb.php";
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
