<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
    }

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fa fa-info-circle"></i> STATUS INFORMATION</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="status.php" method="post">
    <div class="row g-3">
      <!-- Supplier -->
      <div class="col-md-4">
        <label for="nama_supp" class="form-label"><b>Supplier</b></label>
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

    <!-- Filter -->
    <div class="col-md-2">
        <label for="filter" class="form-label"><b>Date Filter</b></label>
        <select class="form-control selectpicker" name="filter" id="filter" data-dropup-auto="false" data-live-search="true">
            <option value="tgl_bpb" <?php
            $status = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
            }                 
            if($status == 'tgl_bpb'){
                $isSelected = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo $isSelected;
            ?>
            >BPB Date</option>
            <option value="tgl_kbon" <?php
            $status = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
            }                 
            if($status == 'tgl_kbon'){
                $isSelected = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo $isSelected;
            ?>
            >Kontrabon Date</option>
            <option value="tgl_lp" <?php
            $status = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
            }                 
            if($status == 'tgl_lp'){
                $isSelected = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo $isSelected;
            ?>
            >List Payment Date</option> 
            <option value="tgl_pay" <?php
            $status = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
            }                 
            if($status == 'tgl_pay'){
                $isSelected = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo $isSelected;
            ?>
            >Payment Date</option>                                                                                                            
        </select>
    </div>

    <!-- Spacer biar rata -->
    <div class="col-md-6"></div>

    <!-- Start Date -->
    <div class="col-md-2">
        <label for="start_date" class="form-label"><b>From</b></label>
        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
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
       } ?>" placeholder="Start Date" autocomplete="off">
   </div>

   <!-- End Date -->
   <div class="col-md-2">
    <label for="end_date" class="form-label"><b>To</b></label>
    <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
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
   } ?>"  placeholder="End Date" autocomplete="off">
</div>

<!-- Tombol -->
<div class="col-md-3 d-flex align-items-end">
  <button type="submit" class="btn btn-info btn-sm me-2">
    <i class="fa fa-search"></i> Search
</button>
<button type="button" id="reset" class="btn btn-danger btn-sm ml-2">
    <i class="fa fa-undo"></i> Reset
</button>

<?php
$nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
$status = isset($_POST['status']) ? $_POST['status']: null;
$filter = isset($_POST['filter']) ? $_POST['filter']: null;
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date'])); 
// echo $filter;

if ($filter == 'tgl_bpb') {
    $filterr = "BPB Date";
            if ($nama_supp == 'ALL') {
                $sql = "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where bpbdate BETWEEN '$start_date' AND '$end_date' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
                    (select no_bpb, create_date verif_date from bpb_new where tgl_bpb BETWEEN '$start_date' AND '$end_date' and status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
                    (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where tgl_kbon >= '$start_date' and status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
                    (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment >= '$start_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
                    (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
                    UNION ALL
                    select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment order by bpbdate asc";
            }else{
                $sql = "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where bpbdate BETWEEN '$start_date' AND '$end_date' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
                    (select no_bpb, create_date verif_date from bpb_new where tgl_bpb BETWEEN '$start_date' AND '$end_date' and status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
                    (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where tgl_kbon >= '$start_date' and status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
                    (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment >= '$start_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
                    (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
                    UNION ALL
                    select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment where nama_supp = '$nama_supp' order by bpbdate asc";
            }
        }elseif ($filter == 'tgl_kbon') {
            $filterr = "Kontrabon Date";
         if ($nama_supp == 'ALL') {
             $sql = "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
                (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int INNER JOIN
                (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where tgl_kbon BETWEEN '$start_date' AND '$end_date' and status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
                (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment >= '$start_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
                (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
                UNION ALL
                select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment order by bpbdate asc";
         }else{
            $sql = "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
                (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int INNER JOIN
                (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where tgl_kbon BETWEEN '$start_date' AND '$end_date' and status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
                (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment >= '$start_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
                (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
                UNION ALL
                select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment where nama_supp = '$nama_supp' order by bpbdate asc";
        }
    }elseif ($filter == 'tgl_lp') {
        $filterr = "List Payment Date";
        if ($nama_supp == 'ALL') {
         $sql = "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
            (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
            (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int INNER JOIN
            (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment BETWEEN '$start_date' AND '$end_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
            (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
            UNION ALL
            select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment order by bpbdate asc";
     }else{
        $sql = "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
            (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
            (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int INNER JOIN
            (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment BETWEEN '$start_date' AND '$end_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
            (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
            UNION ALL
            select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment where nama_supp = '$nama_supp' order by bpbdate asc";
    }

}else{
    if ($nama_supp == 'ALL') {
        $filterr = "Payment Date";
     $sql = "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
        (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
        (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
        (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment GROUP BY no_kbon) d on d.no_kbon = c.no_kbon INNER JOIN 
        (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date BETWEEN '$start_date' AND '$end_date' and b.status != 'Cancel' and no_reff like '%LP%'
        UNION ALL
        select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan BETWEEN '$start_date' AND '$end_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment order by bpbdate asc";
 }else{
    $sql = "select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
        (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
        (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
        (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment GROUP BY no_kbon) d on d.no_kbon = c.no_kbon INNER JOIN 
        (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date BETWEEN '$start_date' AND '$end_date' and b.status != 'Cancel' and no_reff like '%LP%'
        UNION ALL
        select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan BETWEEN '$start_date' AND '$end_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment where nama_supp = '$nama_supp' order by bpbdate asc";
}
}


    echo '<a target="_blank" href="ekspor_status.php?nama_supp='.$nama_supp.'&&filter='.$filterr.'&& start_date='.$start_date.' && end_date='.$end_date.'&& query='.$sql.'"><button type="button" class="btn btn-success ml-2" style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';

?>     

</div>

</div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
      <div class="table-responsive">
          <table id="mytable" 
          class="table table-striped table-bordered table-hover table-sm nowrap" 
          style="width:100%">
          <thead class="table-gradient text-white">
            <tr>
              <th>Supplier</th>
              <th>No BPB</th>
              <th>BPB Date</th>
              <th>BPB Approved Date</th>
              <th>BPB Verified Date</th>
              <th>No SJ</th>
              <th>No WS</th>
              <th>Style</th>
              <th>No Kontrabon</th>
              <th>Kontrabon Date</th>
              <th>Kontrabon Approved Date</th>
              <th>No List Payment</th>
              <th>List Payment Date</th>
              <th>List Payment Approved Date</th>
              <th>List Payment Closed Date</th>
              <th>No Payment</th>
              <th>Payment Date</th>
          </tr>
      </thead>
      <tbody>
        <?php
        $nama_supp ='';
        $status = '';
        $filter = '';
        $start_date ='';
        $end_date ='';
        $date_now = date("Y-m-d");                    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
            $status = isset($_POST['status']) ? $_POST['status']: null;
            $filter = isset($_POST['filter']) ? $_POST['filter']: null;
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
        }

        if ($filter == 'tgl_bpb') {
            if ($nama_supp == 'ALL') {
                $sql = mysqli_query($conn2,"select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where bpbdate BETWEEN '$start_date' AND '$end_date' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
                    (select no_bpb, create_date verif_date from bpb_new where tgl_bpb BETWEEN '$start_date' AND '$end_date' and status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
                    (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where tgl_kbon >= '$start_date' and status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
                    (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment >= '$start_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
                    (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
                    UNION ALL
                    select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment order by bpbdate asc");
            }else{
                $sql = mysqli_query($conn2,"select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where bpbdate BETWEEN '$start_date' AND '$end_date' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
                    (select no_bpb, create_date verif_date from bpb_new where tgl_bpb BETWEEN '$start_date' AND '$end_date' and status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
                    (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where tgl_kbon >= '$start_date' and status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
                    (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment >= '$start_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
                    (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
                    UNION ALL
                    select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment where nama_supp = '$nama_supp' order by bpbdate asc");
            }
        }elseif ($filter == 'tgl_kbon') {
         if ($nama_supp == 'ALL') {
             $sql = mysqli_query($conn2,"select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
                (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int INNER JOIN
                (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where tgl_kbon BETWEEN '$start_date' AND '$end_date' and status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
                (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment >= '$start_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
                (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
                UNION ALL
                select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment order by bpbdate asc");
         }else{
            $sql = mysqli_query($conn2,"select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
                (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int INNER JOIN
                (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where tgl_kbon BETWEEN '$start_date' AND '$end_date' and status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
                (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment >= '$start_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
                (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
                UNION ALL
                select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment where nama_supp = '$nama_supp' order by bpbdate asc");
        }
    }elseif ($filter == 'tgl_lp') {
        if ($nama_supp == 'ALL') {
         $sql = mysqli_query($conn2,"select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
            (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
            (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int INNER JOIN
            (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment BETWEEN '$start_date' AND '$end_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
            (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
            UNION ALL
            select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment order by bpbdate asc");
     }else{
        $sql = mysqli_query($conn2,"select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
            (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
            (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int INNER JOIN
            (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment where tgl_payment BETWEEN '$start_date' AND '$end_date' GROUP BY no_kbon) d on d.no_kbon = c.no_kbon LEFT JOIN 
            (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date >= '$start_date' and b.status != 'Cancel' and no_reff like '%LP%'
            UNION ALL
            select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan >= '$start_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment where nama_supp = '$nama_supp' order by bpbdate asc");
    }

}else{
    if ($nama_supp == 'ALL') {
     $sql = mysqli_query($conn2,"select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
        (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
        (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
        (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment GROUP BY no_kbon) d on d.no_kbon = c.no_kbon INNER JOIN 
        (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date BETWEEN '$start_date' AND '$end_date' and b.status != 'Cancel' and no_reff like '%LP%'
        UNION ALL
        select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan BETWEEN '$start_date' AND '$end_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment order by bpbdate asc");
 }else{
    $sql = mysqli_query($conn2,"select nama_supp, bpbno_int no_bpb, bpbdate tgl_bpb, a.confirm_date approve_bpb, verif_date, c.no_kbon, c.tgl_kbon, c.confirm_date approve_kbon, d.no_payment, d.tgl_payment, d.confirm_date approve_lp, d.closed_date close_lp, e.no_bankout no_pelunasan, e.bankout_date tgl_pelunasan, no_sj, no_ws, style from (select supplier nama_supp, bpbno_int, bpbdate, confirm_date, invno no_sj, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.kpno),'-') no_ws, COALESCE(GROUP_CONCAT(DISTINCT tmpjo.styleno),'-') style from bpb a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo where confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int) a LEFT JOIN
        (select no_bpb, create_date verif_date from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int LEFT JOIN
        (select no_kbon, tgl_kbon, confirm_date, no_bpb from kontrabon where status != 'Cancel' GROUP BY no_bpb) c on c.no_bpb = a.bpbno_int LEFT JOIN
        (select no_payment, tgl_payment, no_kbon, confirm_date, closed_date from list_payment GROUP BY no_kbon) d on d.no_kbon = c.no_kbon INNER JOIN 
        (select * from (select b.no_bankout, b.bankout_date, no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where bankout_date BETWEEN '$start_date' AND '$end_date' and b.status != 'Cancel' and no_reff like '%LP%'
        UNION ALL
        select payment_ftr_id, tgl_pelunasan, list_payment_id from payment_ftr where tgl_pelunasan BETWEEN '$start_date' AND '$end_date' AND status != 'Cancel') a GROUP BY no_reff) e on e.no_reff = d.no_payment where nama_supp = '$nama_supp' order by bpbdate asc");
}
}


function formatDateOrDash($date) {
    return (!empty($date) && $date != '0000-00-00')
    ? date("d-M-Y", strtotime($date))
    : '-';
}

function valueOrDash($value) {
    return !empty($value) ? $value : '-';
}

while ($row = mysqli_fetch_array($sql)) {
    $tgl_verif_bpb   = formatDateOrDash($row['verif_date'] ?? null);
    $tgl_approve_bpb = formatDateOrDash($row['approve_bpb'] ?? null);
    $tgl_approve_kbon= formatDateOrDash($row['approve_kbon'] ?? null);
    $tgl_approve_lp  = formatDateOrDash($row['approve_lp'] ?? null);
    $tgl_closing_lp  = formatDateOrDash($row['close_lp'] ?? null);
    $tgl_kbon        = formatDateOrDash($row['tgl_kbon'] ?? null);
    $tgl_lipa        = formatDateOrDash($row['tgl_payment'] ?? null);
    $tgl_payment     = formatDateOrDash($row['tgl_pelunasan'] ?? null);

    $kbon    = valueOrDash($row['no_kbon'] ?? null);
    $lipa    = valueOrDash($row['no_payment'] ?? null);
    $payment = valueOrDash($row['no_pelunasan'] ?? null);

    echo '<tr style="font-size: 12px; text-align: center;">';
    echo '<td style="width: 250px; text-align: left;" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>';
    echo '<td style="text-align: left;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>';
    echo '<td value="'.$row['tgl_bpb'].'">'.formatDateOrDash($row['tgl_bpb']).'</td>';
    echo '<td value="'.$tgl_approve_bpb.'">'.$tgl_approve_bpb.'</td>';
    echo '<td value="'.$tgl_verif_bpb.'">'.$tgl_verif_bpb.'</td>';
    echo '<td style="text-align: left;" value="'.$row['no_sj'].'">'.$row['no_sj'].'</td>';
    echo '<td style="text-align: left;" value="'.$row['no_ws'].'">'.$row['no_ws'].'</td>';
    echo '<td style="text-align: left;" value="'.$row['style'].'">'.$row['style'].'</td>';
    echo '<td value="'.$kbon.'">'.$kbon.'</td>';
    echo '<td value="'.$tgl_kbon.'">'.$tgl_kbon.'</td>';
    echo '<td value="'.$tgl_approve_kbon.'">'.$tgl_approve_kbon.'</td>';
    echo '<td value="'.$lipa.'">'.$lipa.'</td>';
    echo '<td value="'.$tgl_lipa.'">'.$tgl_lipa.'</td>';
    echo '<td value="'.$tgl_approve_lp.'">'.$tgl_approve_lp.'</td>';
    echo '<td value="'.$tgl_closing_lp.'">'.$tgl_closing_lp.'</td>';
    echo '<td value="'.$payment.'">'.$payment.'</td>';
    echo '<td value="'.$tgl_payment.'">'.$tgl_payment.'</td>';
    echo '</tr>';
}
?>
</tbody>
</table>
</div>
</div>
</div>
</div>

<!-- CSS -->
<style>
  .table-gradient th {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}
div.dataTables_wrapper .dataTables_paginate {
    float: right;
    margin-top: 10px;
}
div.dataTables_wrapper .dataTables_info {
    float: left;
    margin-top: 10px;
}


</style>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="txt_bpb"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div id="txt_tglbpb" class="col-md-6 mb-2"></div>
          <div id="txt_no_po" class="col-md-6 mb-2"></div>
          <div id="txt_supp" class="col-md-6 mb-2"></div>
          <div id="txt_top" class="col-md-6 mb-2"></div>
          <div id="txt_curr" class="col-md-6 mb-2"></div>
          <div id="txt_confirm" class="col-md-6 mb-2"></div>
          <div id="txt_confirm2" class="col-md-6 mb-2"></div>
          <div id="txt_tgl_po" class="col-md-6 mb-2"></div>
          <div id="details" class="col-12 mt-2"></div>
      </div>
  </div>
</div>
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
        $('#mytable').DataTable({
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: false 
        });

        $("[data-toggle=tooltip]").tooltip();
    });

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
<!-- 
<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var top = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(5)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(6)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(11)').text();        

    $.ajax({
    type : 'post',
    url : 'ajaxbpb.php',
    data : {'no_bpb': no_bpb},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_bpb);
    $('#txt_tglbpb').html('Tgl BPB : ' + tgl_bpb + '');
    $('#txt_no_po').html('No PO : ' + no_po + '');
    $('#txt_supp').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "status.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "status.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
