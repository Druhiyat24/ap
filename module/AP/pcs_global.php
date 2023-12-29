<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">PAYABLE CARD STATEMENT GLOBAL</h2>
<div class="box">
    <div class="box header">

        
       <form id="form-data" action="pcs_global.php" method="post">        
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
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn1,"select * from(((select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '2022-04-14' and '$start_date' || a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com = 'REGULAR' and bpbdate between '2022-04-14' and '$start_date' || a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '2022-04-14' and '$start_date' group by a.bpbno_int order by bpbdate asc)union (
        select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '2022-04-14' and '$start_date' group by bppbno_int))
union

(select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '$start_date' and '$end_date' || a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com  = 'REGULAR' and bpbdate between '$start_date' and '$end_date' || a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '$start_date' and '$end_date' group by a.bpbno_int order by bpbdate asc) union (select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '$start_date' and '$end_date' group by bppbno_int)

union(select a.nama_supp, a.no_bpb, a.tgl_bpb,c.jml_pterms as top, DATE_ADD(b.bpbdate, INTERVAL c.jml_pterms DAY) as due_date, a.curr, a.total from saldo_bpb_ap a left join bpb b on b.bpbno_int = a.no_bpb INNER JOIN po_header c on c.pono = b.pono group by a.no_bpb)

union (select nama_supp, no_bpb, tgl_bpb, top, duedate, curr, total from tbl_tamb_bpb)
union (select nama_supp, no_bpb, tgl_bpb, top, duedate, curr, total from tbl_tamb_bpb2 where tgl_bpb between '$start_date' and '$end_date')) as b order by b.Supplier asc");
    }
    else {
        $sql = mysqli_query($conn1,"select * from(((select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '2022-04-14' and '$start_date' and b.Supplier = '$nama_supp' || a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com = 'REGULAR' and bpbdate between '2022-04-14' and '$start_date' and b.Supplier = '$nama_supp' || a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '2022-04-14' and '$start_date' and b.Supplier = '$nama_supp' group by a.bpbno_int order by bpbdate asc)union (
        select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '2022-04-14' and '$start_date' and c.Supplier = '$nama_supp' group by bppbno_int))
union

(select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '$start_date' and '$end_date' and b.Supplier = '$nama_supp' || a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com  = 'REGULAR' and bpbdate between '$start_date' and '$end_date' and b.Supplier = '$nama_supp' || a.r_ap is null and a.confirm = 'y' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '$start_date' and '$end_date' and b.Supplier = '$nama_supp' group by a.bpbno_int order by bpbdate asc) union (select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '$start_date' and '$end_date' and c.Supplier = '$nama_supp' group by bppbno_int)

union(select a.nama_supp, a.no_bpb, a.tgl_bpb,c.jml_pterms as top, DATE_ADD(b.bpbdate, INTERVAL c.jml_pterms DAY) as due_date, a.curr, a.total from saldo_bpb_ap a left join bpb b on b.bpbno_int = a.no_bpb INNER JOIN po_header c on c.pono = b.pono where a.nama_supp = '$nama_supp' group by a.no_bpb)

union (select nama_supp, no_bpb, tgl_bpb, top, duedate, curr, total from tbl_tamb_bpb where nama_supp = '$nama_supp')) as b order by b.Supplier asc");
    }
    $sa_akhir_  = 0;
    $kurang_    = 0;
    $sa_awal_   = 0;
    $tambah_    = 0;

    $sqldel = "delete from rpt_ap_bpb";
    $querydel = mysqli_query($conn1,$sqldel);

   while($row = mysqli_fetch_array($sql)){
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $tgl_bpb = $row['bpbdate'];
    $no_bpb = $row['bpbno_int'];
    $bbayar = $row['total'];
    $suppin = $row['Supplier'];
    $bpbin = $row['bpbno_int'];
    $tgl_bpbin = $row['bpbdate'];
    $currin = $row['curr'];

     $sqldate = mysqli_query($conn1,"select a.bppbno_int,b.jml_pterms as top,DATE_ADD(a.bppbdate, INTERVAL b.jml_pterms DAY) as due_date from bppb a left join bpb d on d.bpbno = a.bpbno_ro left JOIN po_header b on b.pono = d.pono inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.bppbno_int = '$no_bpb' and a.bpbno_ro != '' group by bppbno_int");
    $rowdate = mysqli_fetch_array($sqldate);
    $bppbno_int = isset($rowdate['bppbno_int']) ? $rowdate['bppbno_int'] : null;
    $due_date = isset($rowdate['due_date']) ? $rowdate['due_date'] : null;
    $jml_pterms = isset($rowdate['jml_pterms']) ? $rowdate['jml_pterms'] : null;

    if ($bbayar > 0) {
    $jml_tax = 0;
    $sqllp = mysqli_query($conn1,"select a.no_bpb,a.tgl_bpb from kontrabon a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bpb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and a.status != 'Cancel' GROUP BY a.no_bpb");
    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['no_bpb']) ? $rowlp['no_bpb'] : null;


    $sqllp2 = mysqli_query($conn1,"select a.no_bpb,a.tgl_bpb,d.tgl_kbon2 from kontrabon a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bpb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') < '$start_date' and a.status != 'Cancel' GROUP BY a.no_bpb");
    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['no_bpb']) ? $rowlp2['no_bpb'] : null;
    $tgl = isset($rowlp2['tgl_kbon2']) ? $rowlp2['tgl_kbon2'] : null;
}else{
    $sqltax = mysqli_query($conn1,"select a.bppbno_int, IF(po_header.tax is null,0,po_header.tax) as tax from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier INNER JOIN masteritem on masteritem.id_item = a.id_item right join bpb on bpb.bpbno = a.bpbno_ro left JOIN po_header on po_header.pono = bpb.pono where a.bppbno_int = '$no_bpb' GROUP BY a.bppbno_int");
    $rowtax = mysqli_fetch_array($sqltax);
    $jml_tax = isset($rowtax['tax']) ? $rowtax['tax'] : 0;

    $sqllp = mysqli_query($conn1,"select a.no_bppb,a.tgl_bppb from bppb_new a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bppb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and a.status != 'Cancel' GROUP BY a.no_bppb");
    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['no_bppb']) ? $rowlp['no_bppb'] : null;

    $sqllp2 = mysqli_query($conn1,"select a.no_bppb,a.tgl_bppb,d.tgl_kbon2 from bppb_new a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bppb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') < '$start_date' and a.status != 'Cancel' GROUP BY a.no_bppb");
    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['no_bppb']) ? $rowlp2['no_bppb'] : null;
    $tgl = isset($rowlp2['tgl_kbon2']) ? $rowlp2['tgl_kbon2'] : null;
}

    if($bppbno_int != null){
        $due_date_h = $due_date;
        $top_h = $jml_pterms;
    }else{
        $due_date_h = $row['due_date'];
        $top_h = $row['top'];
    }

    if($no_lp != null){
        $kurang = ($row['total'] + ($row['total'] * $jml_tax/100));
    }else{
        $kurang = 0;
    }

    if($no_lp2 != null){
        $bayar = ($row['total'] + ($row['total'] * $jml_tax/100));
    }else{
        $bayar = 0;
    }

    if($tgl_bpb < $start_date){
        $sa_awal = ($row['total'] + ($row['total'] * $jml_tax/100)) - $bayar;
    }
        else{
        $sa_awal = 0;
    }


    if($tgl_bpb >= $start_date and $tgl < $start_date){
        $tambah = ($row['total'] + ($row['total'] * $jml_tax/100)) - $bayar;
    }elseif($tgl_bpb >= $start_date){
        $tambah = ($row['total'] + ($row['total'] * $jml_tax/100));
    }else{
        $tambah = 0;
    }

    $sa_akhir = $sa_awal + $tambah - $kurang; 
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;
    if($sa_awal == '0' and $tambah == '0' and $kurang == '0' and $sa_akhir == '0'){
        echo '';
    }else{
             $queryin = "INSERT INTO rpt_ap_bpb (nama_supp,no_bpb,tgl_bpb,due_date,curr,beg_balance,addition,deduction,end_balance) 
                        VALUES 
                    ('$suppin', '$bpbin', '$tgl_bpbin', '$due_date_h', '$currin', '$sa_awal', '$tambah', '$kurang', '$sa_akhir')";

            $executein = mysqli_query($conn1,$queryin);
}
}

?>


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
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn1,"select * from (select * from(
        (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,(b.jml_potong - a.dp_value) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d')  asc) 
            
        union (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,(b.jml_potong - a.dp_value) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d') asc)

        union (select a.nama_supp, a.no_kbon, a.tgl_kbon,DATE_FORMAT(b.create_date, '%Y-%m-%d') as create_date,DATEDIFF(b.tgl_tempo,b.tgl_kbon) as top, b.tgl_tempo, a.curr, a.total as bayar, a.total_bpb as total_ori, a.adjust as jml_potong from saldo_kbon_ap a left join kontrabon_h b on b.no_kbon = a.no_kbon)
            
        union (select a.nama_supp,a.no_kbon,c.tgl_kbon2 as tgl_kbon, DATE_FORMAT(c.create_date, '%Y-%m-%d') as create_date,DATEDIFF(c.tgl_tempo,c.tgl_kbon) as top, c.tgl_tempo,a.curr,(a.amount + a.pph_value) as bayar,(a.total_kbon + a.pph_value - b.jml_potong + c.dp_value) as totalori,(b.jml_potong - c.dp_value) as jml_potong  from list_payment a inner join potongan b on b.no_kbon = a.no_kbon left join kontrabon_h c on c.no_kbon = a.no_kbon where DATE_FORMAT(c.create_date, '%Y-%m-%d') < '$start_date' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and c.status !='CANCEL')
     ) as b GROUP BY no_kbon order by b.nama_supp asc) a left join 
         (select no_kbon no_journal, no_coa,nama_coa from kontrabon_h where no_coa != '') b on b.no_journal = a.no_kbon");
    }

    else {
        $sql = mysqli_query($conn1,"select * from(
        (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,(b.jml_potong - a.dp_value) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' and a.nama_supp = '$nama_supp' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d')  asc) 
            
        union (select a.nama_supp, a.no_kbon, a.tgl_kbon2 as tgl_kbon, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,DATEDIFF(a.tgl_tempo,a.tgl_kbon) as top, a.tgl_tempo,a.curr, if(a.balance != a.total,if(a.curr = 'USD', ((a.total - a.balance) + a.pph_fgn),((a.total - a.balance) + a.pph_idr)),0) as bayar, if(a.curr = 'USD', (a.total + a.pph_fgn - b.jml_potong + a.dp_value),(a.total + a.pph_idr - b.jml_potong + a.dp_value)) as totalori,(b.jml_potong - a.dp_value) as jml_potong from kontrabon_h a inner join potongan b on b.no_kbon = a.no_kbon where a.status !='CANCEL' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and a.nama_supp = '$nama_supp' GROUP BY a.no_kbon order by DATE_FORMAT(a.create_date, '%Y-%m-%d') asc)

        union (select a.nama_supp, a.no_kbon, a.tgl_kbon,DATE_FORMAT(b.create_date, '%Y-%m-%d') as create_date,DATEDIFF(b.tgl_tempo,b.tgl_kbon) as top, b.tgl_tempo, a.curr, a.total as bayar, a.total_bpb as total_ori, a.adjust as jml_potong from saldo_kbon_ap a left join kontrabon_h b on b.no_kbon = a.no_kbon where a.nama_supp = '$nama_supp')
            
        union (select a.nama_supp,a.no_kbon,c.tgl_kbon2 as tgl_kbon, DATE_FORMAT(c.create_date, '%Y-%m-%d') as create_date,DATEDIFF(c.tgl_tempo,c.tgl_kbon) as top, c.tgl_tempo,a.curr,(a.amount + a.pph_value) as bayar,(a.total_kbon + a.pph_value - b.jml_potong + c.dp_value) as totalori,(b.jml_potong - c.dp_value) as jml_potong  from list_payment a inner join potongan b on b.no_kbon = a.no_kbon left join kontrabon_h c on c.no_kbon = a.no_kbon where DATE_FORMAT(c.create_date, '%Y-%m-%d') < '$start_date' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and c.status !='CANCEL' and a.nama_supp = '$nama_supp')
     ) as b GROUP BY no_kbon order by b.nama_supp asc");
    }

    $sa_akhir_  = 0;
    $kurang_    = 0;
    $sa_awal_   = 0;
    $tambah_    = 0;
    $tambahan_    = 0;

    $sqldel = "delete from rpt_ap_kbon";
    $querydel = mysqli_query($conn1,$sqldel);

   while($row = mysqli_fetch_array($sql)){
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $tgl_kbon = $row['create_date'];
    $no_kbon = $row['no_kbon'];
    $bbayar = $row['bayar'];
    // $kurang = $row['bayar'];
    $suppin2 = $row['nama_supp'];
    $kbonin = $row['no_kbon'];
    $tgl_kbonin = $row['tgl_kbon'];
    $duedate2 = $row['tgl_tempo'];
    $currin2 = $row['curr'];


    $sqlcek_tot = mysqli_query($conn1,"select total from kontrabon_h where no_kbon = '$no_kbon'");
    $rowcek_tot = mysqli_fetch_array($sqlcek_tot);
    $tot_total = isset($rowcek_tot['total']) ? $rowcek_tot['total'] : 0;


    $sqllp = mysqli_query($conn1,"select no_kbon,tgl_kbon from list_payment where no_kbon = '$no_kbon' and  DATE_FORMAT(create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY no_kbon");
    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['no_kbon']) ? $rowlp['no_kbon'] : null;


    $sqllp2 = mysqli_query($conn1,"select no_kbon,tgl_kbon from list_payment where no_kbon = '$no_kbon' and  DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_kbon");
    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['no_kbon']) ? $rowlp2['no_kbon'] : null;


// if($tot_total != 0){

    if($no_lp != null){
        $kurang = $row['bayar'];
    }else{
        $kurang = 0;
    }

    if($no_lp2 != null){
        $bayar = $row['bayar'];
    }else{
        $bayar = 0;
    }

    if($bbayar == '0' and $tgl_kbon < $start_date){
         $sa_awal = $row['totalori'] + $row['jml_potong'] - $bayar;
    }
    elseif($tgl_kbon < $start_date){
        $sa_awal = $row['bayar'] - $bayar;
    }
        else{
        $sa_awal = 0;
    }


    // if($tgl_kbon < $start_date ){
    //     $sawal = $row['totalori'];
    // $tamhan = $row['jml_potong'];
    // $sa_awal = $sawal + $tamhan;
    // }else{
    //     $sa_awal = 0;
    // }

    if($tgl_kbon >= $start_date){
        $tambah = $row['totalori'];
    }else{
        $tambah = 0;
    }

    if($tgl_kbon >= $start_date){
        $tambahan = $row['jml_potong'];
    }else{
        $tambahan = 0;
    }

// }else{
//     $kurang = 0;
//     $bayar = 0;
//     $sa_awal = 0;
//     $tambah = 0;
//     $tambahan = 0; 
// }

    
    $sa_akhir = $sa_awal + ($tambah + $tambahan) - $kurang; 
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;
    $tambahan_ += $tambahan;

    if($sa_awal == '0' and $tambah == '0' and $tambahan == '0' and $kurang == '0' and $sa_akhir == '0'){
        echo '';
    }else{

        $queryin2 = "INSERT INTO rpt_ap_kbon (nama_supp,no_kbon,tgl_kbon,due_date,curr,beg_balance,add_bpb,add_adj,deduction,end_balance) 
                        VALUES 
                    ('$suppin2', '$kbonin', '$tgl_kbonin', '$duedate2', '$currin2', '$sa_awal', '$tambah', '$tambahan', '$kurang', '$sa_akhir')";

            $executein2 = mysqli_query($conn1,$queryin2);

}
}

?>
                                                         
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
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn1,"select * from(
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' GROUP BY no_payment order by create_date asc) union 
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' GROUP BY no_payment order by create_date asc) union 
        (select '1' as abc, nama_supp, no_payment, tgl_payment, DATE_FORMAT(create_date, '%Y-%m-%d') as create_dat,DATEDIFF(duedate,tgl_payment) as top, duedate, curr, total from saldo_lp_ap GROUP BY no_payment order by tgl_payment asc)) as b order by b.nama_supp asc");
    }
    else{
        $sql = mysqli_query($conn1,"select * from(
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '2022-04-14' and '$start_date' and a.nama_supp = '$nama_supp' GROUP BY no_payment order by create_date asc) union 
        (select '' as abc,a.nama_supp, a.no_payment, a.tgl_payment, DATE_FORMAT(a.create_date, '%Y-%m-%d') as create_date,a.top,a.tgl_tempo,curr, sum(a.amount + a.pph_value) as total from list_payment a left join saldo_awal b on b.no_pay = a.no_payment where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and a.nama_supp = '$nama_supp' GROUP BY no_payment order by create_date asc) union 
        (select '1' as abc, nama_supp, no_payment, tgl_payment, DATE_FORMAT(create_date, '%Y-%m-%d') as create_dat,DATEDIFF(duedate,tgl_payment) as top, duedate, curr, total from saldo_lp_ap GROUP BY no_payment order by tgl_payment asc)) as b where nama_supp = '$nama_supp' order by b.nama_supp asc");
    }

    $sa_akhir_  = 0;
    $kurang_    = 0;
    $sa_awal_   = 0;
    $tambah_    = 0;

    $sqldel = "delete from rpt_ap_lp";
    $querydel = mysqli_query($conn1,$sqldel);

   while($row = mysqli_fetch_array($sql)){
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $tgl_lp = $row['create_date'];
    $no_payment = $row['no_payment'];
    $suppin3 = $row['nama_supp'];
    $paymentin = $row['no_payment'];
    $tgl_paymentin = $row['tgl_payment'];
    $due_date3 = $row['tgl_tempo'];
    $currin3 = $row['curr'];

    $sqlcreff = mysqli_query($conn1,"select if(COUNT(no_reff) = '0',1,COUNT(no_reff)) as c_reff from b_bankout_det where no_reff = '$no_payment'");
    $rowcreff = mysqli_fetch_array($sqlcreff);
    $creff = isset($rowcreff['c_reff']) ? $rowcreff['c_reff'] : 1;

    // $sqllp = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and tgl_pelunasan between '$start_date' and '$end_date' GROUP BY list_payment_id
    //     union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date between '$start_date' and '$end_date' GROUP BY a.no_reff");
    $sqllp = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and DATE_FORMAT(create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and status != 'Cancel' GROUP BY list_payment_id
        union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, sum(a.pph) pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date between '$start_date' and '$end_date' GROUP BY a.no_reff
        union select a.no_reff,a.total, 'CO' as kode,a.pph from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco where b.status != 'Cancel' and a.no_reff = '$no_payment' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff");

    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['list_payment_id']) ? $rowlp['list_payment_id'] : null;
    $kode = isset($rowlp['kode']) ? $rowlp['kode'] : null;
    $pph = isset($rowlp['pph']) ? $rowlp['pph'] : 0;
    $pph_h = $pph / $creff;
    


    // $sqllp2 = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and tgl_pelunasan < '$start_date' GROUP BY list_payment_id  union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date  < '$start_date' GROUP BY a.no_reff");
     $sqllp2 = mysqli_query($conn1,"select list_payment_id,ttl_bayar, 'P' as kode, '0' as pph from payment_ftr where list_payment_id = '$no_payment' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY list_payment_id  union select a.no_reff, sum(a.for_balance) as total, 'OB' as kode, a.pph from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout where a.no_reff = '$no_payment' and b.bankout_date < '$start_date' GROUP BY a.no_reff
         union select a.no_reff,a.total, 'CO' as kode,a.pph from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco where b.status != 'Cancel' and a.no_reff = '$no_payment' and a.tgl_pco < '$start_date' GROUP BY a.no_reff");

    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['list_payment_id']) ? $rowlp2['list_payment_id'] : null;
    $kode2 = isset($rowlp2['kode']) ? $rowlp2['kode'] : null;
    $pph2 = isset($rowlp2['pph']) ? $rowlp2['pph'] : 0;
    $pph_h2 = $pph2 / $creff;

    if($no_lp != null && $kode == "OB"){
        $kurang_h = $rowlp['ttl_bayar'];
        $kurang = $kurang_h + $pph_h;
    }elseif($no_lp != null && $kode == "P"){
        // $kurang = $rowlp['ttl_bayar'];
        $kurang = $row['total'];
    }elseif($no_lp != null && $kode == "CO"){
        // $kurang = $rowlp['ttl_bayar'];
        $kurang = $row['total'];
    }else{
        $kurang = 0;
    }

    if($no_lp2 != null && $kode2 == 'OB'){
        $bayar_h = $rowlp2['ttl_bayar'];
        $bayar = $bayar_h + $pph_h2;
    }elseif($no_lp2 != null && $kode2 == 'P'){
        // $bayar = $rowlp2['ttl_bayar'];
        $bayar = $row['total'];
    }elseif($no_lp2 != null && $kode2 == 'CO'){
        // $bayar = $rowlp2['ttl_bayar'];
        $bayar = $row['total'];
    }else{
        $bayar = 0;
    }


    if($tgl_lp < $start_date){
        $sa_awal = $row['total'] - $bayar;
    }else{
        $sa_awal = 0;
    }

    if($tgl_lp >= $start_date){
        $tambah = $row['total'] - $bayar;
    }else{
        $tambah = 0;
    }

    $sa_akhir = $sa_awal + $tambah - $kurang; 
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;

    if($sa_awal == '0' and $tambah == '0' and $kurang == '0' and $sa_akhir == '0'){
        echo '';
    }else{
             $queryin = "INSERT INTO rpt_ap_lp (nama_supp,no_payment,tgl_payment,due_date,curr,beg_balance,addition,deduction,end_balance) 
                        VALUES 
                    ('$suppin3', '$paymentin', '$tgl_paymentin', '$due_date3', '$currin3', '$sa_awal', '$tambah', '$kurang', '$sa_akhir')";

            $executein = mysqli_query($conn1,$queryin);

}
}
?>  



 <div style="margin-left: 30px">
        <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        echo '<a target="_blank" href="pcs_detail_lp.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> EXCEL </i></button></a>';
        ?>
    </div> 
</br>


        <div class="tableFix" style="height: 500px;">        
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">Nama Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Curr</th>
            <th style="text-align: center;vertical-align: middle;">Beginning balance</th>
            <th style="text-align: center;vertical-align: middle;">Addition</th>
            <th style="text-align: center;vertical-align: middle;">Deduction Adj/DP</th>
            <th style="text-align: center;vertical-align: middle;">Deduction LP</th>
            <th style="text-align: center;vertical-align: middle;">Adjustment</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance</th>
            <th style="text-align: center;vertical-align: middle;">rate</th>
            <th style="text-align: center;vertical-align: middle;">Ending Balance IDR</th>                                                                            
        </tr>
    </thead>
   
    <tbody>  
   <!--  <div class="loader"></div>     --> 
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
   if(empty($nama_supp)){
    echo '';
    }
    elseif ($nama_supp == 'ALL') {
        $sql = mysqli_query($conn1,"select Supplier,curr,sal_awal,addtn,ded_adj,deduction,adjusment, (sal_awal + addtn + ded_adj + deduction + adjusment) sal_akhir from(select a.Supplier,a.curr, (COALESCE(b.sal_awal,0) + COALESCE(c.sal_awal,0) + COALESCE(d.sal_awal,0)) sal_awal ,COALESCE(b.addtn,0) addtn, - COALESCE(c.add_adj,0) ded_adj, - COALESCE(d.deduction,0) deduction, - ((COALESCE(d.addtn_lp,0)- COALESCE(c.ded_kbn,0)) + (COALESCE(c.add_bpb,0) - COALESCE(b.ded_bpb,0))) adjusment from (select DISTINCT Supplier,curr from (select b.Supplier,a.curr from bpb a inner join mastersupplier b on b.id_supplier = a.id_supplier where a.bpbdate >= '2021-01-01' and a.curr != '') a ORDER BY a.supplier asc) a left join 
            (select nama_supp,curr, sum(beg_balance) sal_awal, sum(addition) addtn, SUM(deduction) ded_bpb from rpt_ap_bpb GROUP BY nama_supp,curr) b on b.nama_supp = a.Supplier and a.curr = b.curr left join 
            (select nama_supp,curr, sum(beg_balance) sal_awal, sum(add_adj) add_adj,SUM(deduction) ded_kbn, SUM(add_bpb) add_bpb from rpt_ap_kbon GROUP BY nama_supp,curr) c on c.nama_supp = a.Supplier and a.curr = c.curr left join
            (select nama_supp,curr, sum(beg_balance) sal_awal, SUM(deduction) deduction, SUM(addition) addtn_lp from rpt_ap_lp GROUP BY nama_supp,curr) d on d.nama_supp = a.Supplier and a.curr = d.curr group by a.Supplier,a.curr) a");
    }
    else{
        $sql = mysqli_query($conn1,"select Supplier,curr,sal_awal,addtn,ded_adj,deduction,adjusment, (sal_awal + addtn + ded_adj + deduction + adjusment) sal_akhir from(select a.Supplier,a.curr, (COALESCE(b.sal_awal,0) + COALESCE(c.sal_awal,0) + COALESCE(d.sal_awal,0)) sal_awal ,COALESCE(b.addtn,0) addtn, - COALESCE(c.add_adj,0) ded_adj, - COALESCE(d.deduction,0) deduction, - ((COALESCE(d.addtn_lp,0)- COALESCE(c.ded_kbn,0)) + (COALESCE(c.add_bpb,0) - COALESCE(b.ded_bpb,0))) adjusment from (select DISTINCT Supplier,curr from (select b.Supplier,a.curr from bpb a inner join mastersupplier b on b.id_supplier = a.id_supplier where a.bpbdate >= '2021-01-01' and a.curr != '') a ORDER BY a.supplier asc) a left join 
            (select nama_supp,curr, sum(beg_balance) sal_awal, sum(addition) addtn, SUM(deduction) ded_bpb from rpt_ap_bpb GROUP BY nama_supp,curr) b on b.nama_supp = a.Supplier and a.curr = b.curr left join 
            (select nama_supp,curr, sum(beg_balance) sal_awal, sum(add_adj) add_adj,SUM(deduction) ded_kbn, SUM(add_bpb) add_bpb from rpt_ap_kbon GROUP BY nama_supp,curr) c on c.nama_supp = a.Supplier and a.curr = c.curr left join
            (select nama_supp,curr, sum(beg_balance) sal_awal, SUM(deduction) deduction, SUM(addition) addtn_lp from rpt_ap_lp GROUP BY nama_supp,curr) d on d.nama_supp = a.Supplier and a.curr = d.curr group by a.Supplier,a.curr) a where a.Supplier = '$nama_supp'");
    }

    $sal_awal_    = 0;
    $addtn_       = 0;
    $ded_adj_     = 0;
    $deduction_   = 0;
    $adjusment_   = 0;
    $sal_akhir_   = 0;

   while($row = mysqli_fetch_array($sql)){
    $sal_awal = $row['sal_awal'];
    $addtn = $row['addtn'];
    $ded_adj = $row['ded_adj'];
    $deduction = $row['deduction'];
    $adjusment = $row['adjusment'];
    $sal_akhir = $row['sal_akhir'];

    $sal_awal_    += $sal_awal;
    $addtn_       += $addtn;
    $ded_adj_     += $ded_adj;
    $deduction_   += $deduction;
    $adjusment_   += $adjusment;
    $sal_akhir_   += $sal_akhir;


    if($sal_awal == '0' and $addtn == '0' and $ded_adj == '0' and $deduction == '0'and $adjusment == '0' and $sal_akhir == '0'){
        echo '';
    }else{
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['Supplier'].'">'.$row['Supplier'].'</td>                          
            <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
            <td style="text-align:right;" value = "'.$sal_awal.'">'.number_format($sal_awal,2).'</td>
            <td style="text-align:right;" value = "'.$addtn.'">'.number_format($addtn,2).'</td>         
            <td style="text-align:right;" value = "'.$ded_adj.'">'.number_format($ded_adj,2).'</td>
            <td style="text-align:right;" value = "'.$deduction.'">'.number_format($deduction,2).'</td>
            <td style="text-align:right;" value = "'.$adjusment.'">'.number_format($adjusment,2).'</td>
            <td style="text-align:right;" value = "'.$sal_akhir.'">'.number_format($sal_akhir,2).'</td>         
            <td style="text-align:right;" value = "">0.00</td>
            <td style="text-align:right;" value = "">0.00</td>
             ';
            

}
}
echo '
            <tr >
            <th colspan = "2" style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($sal_awal_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($addtn_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ded_adj_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($deduction_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($adjusment_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($sal_akhir_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">0.00</th>
            <th style="text-align: right;vertical-align: middle;">0.00</th>                                                                            
        </tr>';
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
