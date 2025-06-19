 <div style="margin-left: 10px;margin-bottom: 10px;">
        <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        echo '<a target="_blank" href="pcs_detail_bpb.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size:11px"> EXCEL AP - BPB</i></button></a>';
        ?>
    </div> 

    <div class="tableFix" style="height: 350px;">        
<table id="datatable" class="table table-striped table-bordered text-nowrap" style="font-size: 12px;" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Nama Supplier</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Bpb Number</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Bpb Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;display: none;">TOP</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Due Date</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Currency</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Begining Balance</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Addition</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Rate</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance IDR</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">COA No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">COA Name</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Item Type 1</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Item Type 2</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Relationship</th> 
            <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th>    
            <th colspan="9" style="text-align: center;vertical-align: middle;background-color: #98FB98;">Account Payable Aging Based on Due Date</th>
            <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th> 
            <th colspan="8" style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Account Payable Based on Due Date Projection</th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Current</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">1-30</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">31-60</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">61-90</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">91-120</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">121-180</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">181-360</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">>360</th>
            <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Total</th>
            <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Due</th>
            <?php 
                $end_date = 0;
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $end_date = date("Y-m-d",strtotime($_POST['end_date']));

                $sqlbulan = mysqli_query($conn1,"select kode_tanggal,bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun, CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)),LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun from dim_date where kode_tanggal BETWEEN CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01') and CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),LPAD(IF(MONTH('$end_date')+5 > 12,MOD((MONTH('$end_date')+5),12),(MONTH('$end_date')+5)),2,0),'01') GROUP BY bulan,tahun order by kode_tanggal asc");
                while($rowbulan = mysqli_fetch_array($sqlbulan)){
                    echo'<th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">'.$rowbulan['bulan_tahun'].'</th>';
                }
                }else{
                    echo'<th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>
                    <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">-</th>';
                } 
            ?>
            <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Total</th>
        </tr>
    </thead>
   
    <tbody>
<?php
    $value = '';
    $start_date ='';
    $end_date ='';
    $bulan ='';
    $tahun ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));
    $bulan = date("m",strtotime($_POST['end_date']));  
    $tahun = date("Y",strtotime($_POST['end_date']));
    }

    $sql_tanggal = mysqli_query($conn1,"select tgl_akhir from tbl_tgl_tb where bulan = '$bulan' and tahun = '$tahun'");
    $row_tanggal = mysqli_fetch_array($sql_tanggal);
    $tgl_akhir = isset($row_tanggal['tgl_akhir']) ? $row_tanggal['tgl_akhir'] : 1;

    if ($tgl_akhir == $end_date) {
        $sql_rate = mysqli_query($conn1,"select v_codecurr, tanggal, rate from masterrate where tanggal = '$end_date' and v_codecurr = 'HARIAN'");
    }else{
        $sql_rate = mysqli_query($conn1,"select v_codecurr, tanggal, rate from masterrate where tanggal = '$end_date' and v_codecurr = 'PAJAK'");
    }
    
    $row_rate = mysqli_fetch_array($sql_rate);
    $jml_rate = isset($row_rate['rate']) ? $row_rate['rate'] : 1;

   if(empty($nama_supp) and empty($start_date) and empty($end_date)){
    echo '';
    }
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn1,"select * from(((select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '2022-04-14' and '$start_date' and b.tipe_sup != 'D' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com IN ('REGULAR','') and bpbdate between '2022-04-14' and '$start_date' and b.tipe_sup != 'D' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '2022-04-14' and '$start_date' and b.tipe_sup != 'D' group by a.bpbno_int order by bpbdate asc)union (
        select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '2022-04-14' and '$start_date' and c.tipe_sup != 'D' group by bppbno_int))
union

(select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '$start_date' and '$end_date' and b.tipe_sup != 'D' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com IN ('REGULAR','') and bpbdate between '$start_date' and '$end_date' and b.tipe_sup != 'D' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '$start_date' and '$end_date' and b.tipe_sup != 'D' group by a.bpbno_int order by bpbdate asc) union (select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '$start_date' and '$end_date' and c.tipe_sup != 'D' group by bppbno_int)

union(select a.nama_supp, a.no_bpb, a.tgl_bpb,c.jml_pterms as top, DATE_ADD(b.bpbdate, INTERVAL c.jml_pterms DAY) as due_date, a.curr, a.total from saldo_bpb_ap a left join bpb b on b.bpbno_int = a.no_bpb INNER JOIN po_header c on c.pono = b.pono group by a.no_bpb)

union (select nama_supp, no_bpb, tgl_bpb, top, duedate, curr, total from tbl_tamb_bpb where tgl_bpb <= '$end_date')
union (select nama_supp, no_bpb, tgl_bpb, top, duedate, curr, total from tbl_tamb_bpb2 where tgl_bpb <= '$end_date' )) as b order by b.Supplier asc");
    }
    else {
        $sql = mysqli_query($conn1,"select * from(((select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '2022-04-14' and '$start_date' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com IN ('REGULAR','') and bpbdate between '2022-04-14' and '$start_date' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '2022-04-14' and '$start_date' group by a.bpbno_int order by bpbdate asc)union (
        select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '2022-04-14' and '$start_date' group by bppbno_int))
union

(select b.Supplier,a.bpbno_int,bpbdate,c.jml_pterms as top, DATE_ADD(a.bpbdate, INTERVAL c.jml_pterms DAY) as due_date,a.curr,round(sum((((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) + (((IF(a.qty_reject IS NULL,(a.qty), (a.qty - a.qty_reject))) * a.price) * (c.tax /100)))),2) as total from bpb a INNER JOIN po_header c on c.pono = a.pono INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier left JOIN po_header_draft d on d.id = c.id_draft where a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com is null and bpbdate between '$start_date' and '$end_date' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com IN ('REGULAR','') and bpbdate between '$start_date' and '$end_date' || a.r_ap is null and a.confirm = 'y' and c.app = 'A' and a.price != '0' and cancel = 'N' and d.tipe_com = 'BUYER' and bpbdate between '$start_date' and '$end_date' group by a.bpbno_int order by bpbdate asc) union (select c.Supplier,a.bppbno_int,a.bppbdate, '0' as top, '0000-00-00' as due_date,a.curr,ROUND(- SUM((a.qty * a.price)),2) as total from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier  where a.cancel != 'Y' and a.bpbno_ro != '' and a.confirm = 'Y' and a.bppbdate between '$start_date' and '$end_date' group by bppbno_int)

union(select a.nama_supp, a.no_bpb, a.tgl_bpb,c.jml_pterms as top, DATE_ADD(b.bpbdate, INTERVAL c.jml_pterms DAY) as due_date, a.curr, a.total from saldo_bpb_ap a left join bpb b on b.bpbno_int = a.no_bpb INNER JOIN po_header c on c.pono = b.pono group by a.no_bpb)

union (select nama_supp, no_bpb, tgl_bpb, top, duedate, curr, total from tbl_tamb_bpb)
union (select nama_supp, no_bpb, tgl_bpb, top, duedate, curr, total from tbl_tamb_bpb2 where tgl_bpb between '$start_date' and '$end_date')) as b where b.Supplier = '$nama_supp' order by b.Supplier asc");
    }


    $sa_akhir_  = 0;
    $kurang_    = 0;
    $sa_awal_   = 0;
    $tambah_    = 0;
    $saldo_akhir_idr_ = 0;
    $ttl_due_current = 0;
    $ttl_due_1 = 0;
    $ttl_due_2 = 0;
    $ttl_due_3 = 0;
    $ttl_due_4 = 0;
    $ttl_due_5 = 0;
    $ttl_due_6 = 0;
    $ttl_due_7 = 0;
    $ttl_tot_due = 0;
    $ttl_pro_due = 0;
    $ttl_pro_due0 = 0;
    $ttl_pro_due1 = 0;
    $ttl_pro_due2 = 0;
    $ttl_pro_due3 = 0;
    $ttl_pro_due4 = 0;
    $ttl_pro_due5 = 0;
    $ttl_tot_produe = 0;

    $sqldel = "delete from rpt_ap_bpb where user_create = '$user'";
    $querydel = mysqli_query($conn1,$sqldel);

    $sqldel2 = "ALTER TABLE rpt_ap_bpb";
    $querydel2 = mysqli_query($conn1,$sqldel2);
    
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
        if ($no_bpb == 'WIP/IN/0522/01805') {
           $jml_tax = 11;
        }else{
    $jml_tax = 0;
}
    $sqllp = mysqli_query($conn1,"select a.no_bpb,a.tgl_bpb from kontrabon a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bpb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and a.status != 'Cancel' GROUP BY a.no_bpb
        union
select no_doc, tgl_doc from tbl_tamb_ap where no_doc = '$no_bpb' and tgl_pay between '$start_date' and '$end_date' GROUP BY no_doc
UNION
select reff_doc,reff_date from tbl_list_journal where reff_doc = '$no_bpb' and no_journal like '%GM/NAG%' and type_journal = 'ACCOUNT PAYABLE' and debit != 0 and tgl_journal >= '2024-09-01' and tgl_journal between '$start_date' and '$end_date' GROUP BY reff_doc");
    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['no_bpb']) ? $rowlp['no_bpb'] : null;


    $sqllp2 = mysqli_query($conn1,"select a.no_bpb,a.tgl_bpb,d.tgl_kbon2 from kontrabon a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bpb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') < '$start_date' and a.status != 'Cancel' GROUP BY a.no_bpb
        union
select no_doc, tgl_doc, tgl_doc tgl_doc2 from tbl_tamb_ap where no_doc = '$no_bpb' and tgl_pay < '$start_date' GROUP BY no_doc
UNION
select reff_doc,reff_date,reff_date from tbl_list_journal where reff_doc = '$no_bpb' and no_journal like '%GM/NAG%' and type_journal = 'ACCOUNT PAYABLE' and debit != 0 and tgl_journal >= '2024-09-01' and tgl_journal < '$start_date' GROUP BY reff_doc");
    $rowlp2 = mysqli_fetch_array($sqllp2);
    $no_lp2 = isset($rowlp2['no_bpb']) ? $rowlp2['no_bpb'] : null;
    $tgl = isset($rowlp2['tgl_kbon2']) ? $rowlp2['tgl_kbon2'] : null;
}else{
    if ($no_bpb == 'GEN/RO/0722/00606' || $no_bpb == 'GEN/RO/0722/00623') {
     $jml_tax = 0;  
    }elseif($no_bpb == 'GK/RO/0525/00590'){
        $jml_tax = 10;
    }else{
    $sqltax = mysqli_query($conn1,"select a.bppbno_int, IF(po_header.tax is null,0,po_header.tax) as tax from bppb a inner join mastersupplier c on c.Id_Supplier = a.id_supplier INNER JOIN masteritem on masteritem.id_item = a.id_item right join bpb on bpb.bpbno = a.bpbno_ro left JOIN po_header on po_header.pono = bpb.pono where a.bppbno_int = '$no_bpb' GROUP BY a.bppbno_int");
    $rowtax = mysqli_fetch_array($sqltax);
    $jml_tax = isset($rowtax['tax']) ? $rowtax['tax'] : 0;
    }

    $sqllp = mysqli_query($conn1,"select a.no_bppb,a.tgl_bppb from bppb_new a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bppb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') between '$start_date' and '$end_date' and a.status != 'Cancel' GROUP BY a.no_bppb
        union
select no_doc, tgl_doc from tbl_tamb_ap where no_doc = '$no_bpb' and tgl_pay between '$start_date' and '$end_date' GROUP BY no_doc
UNION
select reff_doc,reff_date from tbl_list_journal where reff_doc = '$no_bpb' and tgl_journal >= '2024-09-01' and tgl_journal between '$start_date' and '$end_date' and no_journal like '%GM/NAG%' and debit != 0 and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc");
    $rowlp = mysqli_fetch_array($sqllp);
    $no_lp = isset($rowlp['no_bppb']) ? $rowlp['no_bppb'] : null;

    $sqllp2 = mysqli_query($conn1,"select a.no_bppb,a.tgl_bppb,d.tgl_kbon2 from bppb_new a inner join kontrabon_h d on d.no_kbon = a.no_kbon where a.no_bppb = '$no_bpb' and DATE_FORMAT(d.create_date, '%Y-%m-%d') < '$start_date' and a.status != 'Cancel' GROUP BY a.no_bppb
        union
select no_doc, tgl_doc, tgl_doc tgl_doc2 from tbl_tamb_ap where no_doc = '$no_bpb' and tgl_pay < '$start_date' GROUP BY no_doc
UNION
select reff_doc,reff_date,reff_date from tbl_list_journal where reff_doc = '$no_bpb' and no_journal like '%GM/NAG%' and debit != 0 and tgl_journal >= '2024-09-01' and tgl_journal < '$start_date' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc");
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

    if ($currin == 'IDR') {
        $rate = 1;
    }elseif ($currin == 'CNY') {
        $rate = 2234.01;
    }else{
        $rate = $jml_rate;
    }


    $sa_akhir = $sa_awal + $tambah - $kurang; 
    $saldo_akhir_idr = $sa_akhir * $rate;
    $saldo_akhir_idr_ += $saldo_akhir_idr;
    $sa_akhir_ += $sa_akhir;
    $kurang_ += $kurang;
    $sa_awal_ += $sa_awal;
    $tambah_ += $tambah;
    if($sa_awal == '0' and $tambah == '0' and $kurang == '0' and $sa_akhir == '0'){
        echo '';
    }else{
        if ($due_date_h > $end_date) {
            $due_current = $saldo_akhir_idr; 
        }else{
            $due_current = 0; 
        }
        $diff = (strtotime($end_date) - strtotime($due_date_h));
        $aging_days = floor($diff/ (60*60*24));
        if ($aging_days >= 0 && $aging_days < 31) { $due_1 = $saldo_akhir_idr; }else{ $due_1 = 0; }
        if ($aging_days > 30 && $aging_days < 61) { $due_2 = $saldo_akhir_idr; }else{ $due_2 = 0; }
        if ($aging_days > 60 && $aging_days < 91) { $due_3 = $saldo_akhir_idr; }else{ $due_3 = 0; }
        if ($aging_days > 90 && $aging_days < 121) { $due_4 = $saldo_akhir_idr; }else{ $due_4 = 0; }
        if ($aging_days > 120 && $aging_days < 181) { $due_5 = $saldo_akhir_idr; }else{ $due_5 = 0; }
        if ($aging_days > 180 && $aging_days < 361) { $due_6 = $saldo_akhir_idr; }else{ $due_6 = 0; }
        if ($aging_days > 360) { $due_7 = $saldo_akhir_idr; }else{ $due_7 = 0; }
        $tot_due = $due_current + $due_1 + $due_2 + $due_3 + $due_4 + $due_5 + $due_6 + $due_7;
        $ttl_due_current += $due_current;
        $ttl_due_1 += $due_1;
        $ttl_due_2 += $due_2;
        $ttl_due_3 += $due_3;
        $ttl_due_4 += $due_4;
        $ttl_due_5 += $due_5;
        $ttl_due_6 += $due_6;
        $ttl_due_7 += $due_7;
        $ttl_tot_due += $tot_due;

        if ($due_date_h <= $end_date) { $pro_due = $saldo_akhir_idr; }else{ $pro_due = 0; }
        $pro_thn = date("Y",strtotime($end_date));
        $pro_bln = date("m",strtotime($end_date));
        $pro_hri = date("d",strtotime($end_date));
        $date_pro = $pro_thn . '-' . $pro_bln . '-' . $pro_hri;
        $probln1 = $pro_bln + 1;
        $probln2 = $pro_bln + 2;
        $probln3 = $pro_bln + 3;
        $probln4 = $pro_bln + 4;
        $probln5 = $pro_bln + 5;
        if ($probln1 > 12) { $bln1 = fmod($probln1, 12); $prothn1 = $pro_thn + 1; }else{ $bln1 = $probln1; $prothn1 = $pro_thn; }
        if ($probln2 > 12) { $bln2 = fmod($probln2, 12); $prothn2 = $pro_thn + 1; }else{ $bln2 = $probln2; $prothn2 = $pro_thn; }
        if ($probln3 > 12) { $bln3 = fmod($probln3, 12); $prothn3 = $pro_thn + 1; }else{ $bln3 = $probln3; $prothn3 = $pro_thn; }
        if ($probln4 > 12) { $bln4 = fmod($probln4, 12); $prothn4 = $pro_thn + 1; }else{ $bln4 = $probln4; $prothn4 = $pro_thn; }
        if ($probln5 > 12) { $bln5 = fmod($probln5, 12); $prothn5 = $pro_thn + 1; }else{ $bln5 = $probln5; $prothn5 = $pro_thn; }
        $pro_bln1 = sprintf("%02s", $bln1);
        $pro_bln2 = sprintf("%02s", $bln2);
        $pro_bln3 = sprintf("%02s", $bln3);
        $pro_bln4 = sprintf("%02s", $bln4);
        $pro_bln5 = sprintf("%02s", $bln5);
        $date_pro1 = $prothn1 . '-' . $pro_bln1 . '-' . '01';
        $date_pro2 = $prothn2 . '-' . $pro_bln2 . '-' . '01';
        $date_pro3 = $prothn3 . '-' . $pro_bln3 . '-' . '01';
        $date_pro4 = $prothn4 . '-' . $pro_bln4 . '-' . '01';
        $date_pro5 = $prothn5 . '-' . $pro_bln5 . '-' . '01';

        if ($due_date_h > $date_pro && $due_date_h < $date_pro1) { $pro_due0 = $saldo_akhir_idr; }else{ $pro_due0 = 0; }
        if ($due_date_h >= $date_pro1 && $due_date_h < $date_pro2) { $pro_due1 = $saldo_akhir_idr; }else{ $pro_due1 = 0; }
        if ($due_date_h >= $date_pro2 && $due_date_h < $date_pro3) { $pro_due2 = $saldo_akhir_idr; }else{ $pro_due2 = 0; }
        if ($due_date_h >= $date_pro3 && $due_date_h < $date_pro4) { $pro_due3 = $saldo_akhir_idr; }else{ $pro_due3 = 0; }
        if ($due_date_h >= $date_pro4 && $due_date_h < $date_pro5) { $pro_due4 = $saldo_akhir_idr; }else{ $pro_due4 = 0; }
        if ($due_date_h >= $date_pro5) { $pro_due5 = $saldo_akhir_idr; }else{ $pro_due5 = 0; }

        $tot_produe = $pro_due + $pro_due0 + $pro_due1 + $pro_due2 + $pro_due3 + $pro_due4 + $pro_due5;

        $ttl_pro_due += $pro_due;
        $ttl_pro_due0 += $pro_due0;
        $ttl_pro_due1 += $pro_due1;
        $ttl_pro_due2 += $pro_due2;
        $ttl_pro_due3 += $pro_due3;
        $ttl_pro_due4 += $pro_due4;
        $ttl_pro_due5 += $pro_due5;
        $ttl_tot_produe += $tot_produe;

        $sqlcoa = mysqli_query($conn1,"select * from (select a.no_journal,a.no_coa,a.nama_coa,b.item_type1,b.item_type2,b.relasi from (select no_journal,no_coa,nama_coa from tbl_list_journal where credit != '' and type_journal = 'AP - BPB' and no_coa != '1.52.07' and no_journal = '$no_bpb' union select no_journal,no_coa,nama_coa from tbl_list_journal where debit != '' and type_journal = 'AP - BPB RETURN' and no_coa != '1.52.07' and no_journal = '$no_bpb') a left join mastercoa_v2 b on b.no_coa = a.no_coa) a where item_type1 is not null");
        $rowcoa = mysqli_fetch_array($sqlcoa);
        $no_coa = isset($rowcoa['no_coa']) ? $rowcoa['no_coa'] : null;
        $nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;
        $item_type1 = isset($rowcoa['item_type1']) ? $rowcoa['item_type1'] : null;
        $item_type2 = isset($rowcoa['item_type2']) ? $rowcoa['item_type2'] : null;
        $relasi = isset($rowcoa['relasi']) ? $rowcoa['relasi'] : null;
 
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['Supplier'].'">'.$row['Supplier'].'</td>
            <td value="'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>
            <td value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>                            
            <td style="display: none;" value="'.$top_h.'">'.$top_h.' Days</td>
            <td value="'.$due_date_h.'">'.date("d-M-Y",strtotime($due_date_h)).'</td>
            <td value="'.$row['curr'].'">'.$row['curr'].'</td>                            
            <td style="text-align:right;" value = "'.$sa_awal.'">'.number_format($sa_awal,2).'</td>
            <td style="text-align:right;" value = "'.$tambah.'">'.number_format($tambah,2).'</td>         
            <td style="text-align:right;" value = "'.$kurang.'">'.number_format($kurang,2).'</td>
            <td style="text-align:right;" value = "'.$sa_akhir.'">'.number_format($sa_akhir,2).'</td>
            <td style="text-align:right;" value="'.$rate.'">'.number_format($rate,2).'</td>
            <td style="text-align:right;" value="'.$saldo_akhir_idr.'">'.number_format($saldo_akhir_idr,2).'</td>
            <td value="'.$no_coa.'">'.$no_coa.'</td>
            <td value="'.$nama_coa.'">'.$nama_coa.'</td>
            <td value="'.$item_type1.'">'.$item_type1.'</td>
            <td value="'.$item_type2.'">'.$item_type2.'</td>
            <td value="'.$relasi.'">'.$relasi.'</td>
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;</td>
            <td style="text-align:right;" value="'.$due_current.'">'.number_format($due_current,2).'</td>
            <td style="text-align:right;" value="'.$due_1.'">'.number_format($due_1,2).'</td>
            <td style="text-align:right;" value="'.$due_2.'">'.number_format($due_2,2).'</td>
            <td style="text-align:right;" value="'.$due_3.'">'.number_format($due_3,2).'</td>
            <td style="text-align:right;" value="'.$due_4.'">'.number_format($due_4,2).'</td>
            <td style="text-align:right;" value="'.$due_5.'">'.number_format($due_5,2).'</td>
            <td style="text-align:right;" value="'.$due_6.'">'.number_format($due_6,2).'</td>
            <td style="text-align:right;" value="'.$due_7.'">'.number_format($due_7,2).'</td>
            <td style="text-align:right;" value="'.$tot_due.'">'.number_format($tot_due,2).'</td>
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;</td>
            <td style="text-align:right;" value="'.$pro_due.'">'.number_format($pro_due,2).'</td>
            <td style="text-align:right;" value="'.$pro_due0.'">'.number_format($pro_due0,2).'</td>
            <td style="text-align:right;" value="'.$pro_due1.'">'.number_format($pro_due1,2).'</td>
            <td style="text-align:right;" value="'.$pro_due2.'">'.number_format($pro_due2,2).'</td>
            <td style="text-align:right;" value="'.$pro_due3.'">'.number_format($pro_due3,2).'</td>
            <td style="text-align:right;" value="'.$pro_due4.'">'.number_format($pro_due4,2).'</td>
            <td style="text-align:right;" value="'.$pro_due5.'">'.number_format($pro_due5,2).'</td>
            <td style="text-align:right;" value="'.$tot_produe.'">'.number_format($tot_produe,2).'</td>
             ';

             $queryin = "INSERT INTO rpt_ap_bpb (nama_supp,no_bpb,tgl_bpb,due_date,curr,beg_balance,addition,deduction,end_balance,end_balance_idr,create_date,due_0,due_1,due_2,due_3,due_4,due_5,due_6,due_7,due_total,produe_0,produe_1,produe_2,produe_3,produe_4,produe_5,produe_6,produe_total,filter_awal,filter_akhir,no_coa,item_type1,item_type2,relasi,user_create) 
                        VALUES 
                    ('$suppin', '$bpbin', '$tgl_bpbin', '$due_date_h', '$currin', '$sa_awal', '$tambah', '$kurang', '$sa_akhir', '$saldo_akhir_idr', '$insert_date', '$due_current', '$due_1', '$due_2', '$due_3', '$due_4', '$due_5', '$due_6', '$due_7', '$tot_due', '$pro_due', '$pro_due0', '$pro_due1', '$pro_due2', '$pro_due3', '$pro_due4', '$pro_due5', '$tot_produe', '$start_date', '$end_date', '$no_coa', '$item_type1', '$item_type2', '$relasi','$user')";

            $executein = mysqli_query($conn1,$queryin);
            
        // }

}
}
echo '
            <tr >
            <th colspan = "5" style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($sa_awal_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($tambah_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($kurang_,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($sa_akhir_,2).'</th>
            <th style="text-align: right;vertical-align: middle;"></th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($saldo_akhir_idr_,2).'</th>    
            <th colspan = "5" style="text-align: right;vertical-align: middle;"></th>                                                                        
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_current,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_1,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_2,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_3,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_4,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_5,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_6,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_due_7,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_tot_due,2).'</th>
            <td style="width:50px;background-color: white;border:none" value="">&nbsp;&nbsp;&nbsp;</td>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due0,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due1,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due2,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due3,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due4,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_pro_due5,2).'</th>
            <th style="text-align: right;vertical-align: middle;">'.number_format($ttl_tot_produe,2).'</th>

        </tr>';

?>


                                                         
</tbody>                    
</table>
</div>
</br>