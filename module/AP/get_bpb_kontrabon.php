<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$nama_supp  = $_POST['nama_supp'] ?? '';
$start_date = date("Y-m-d",strtotime($_POST['start_date'])) ?? '';
$end_date = date("Y-m-d",strtotime($_POST['end_date'])) ?? '';
$no_kbon = $_POST['no_kbon'] ?? '';
$profit_center = $_POST['profit_center'] ?? '';

$sub = '';
$tax = '';
$total = '';
$persen = '';     


$querys = mysqli_query($conn2,"select distinct (no_bpb) from bpb_new");
$rows = mysqli_fetch_array($querys);
$no_bpb = isset($rows['no_bpb']) ?  $rows['no_bpb'] : null;


$q = mysqli_query($conn1,"select idtax, kriteria, percentage from mtax where category_tax = 'PPH'");            
while($rs = mysqli_fetch_array($q)){
    $persen .= '<option data-idtax="'.$rs['idtax'].'" value="'.$rs['percentage'].'">'.$rs['kriteria'].'</option>';
}                        

$sql = mysqli_query($conn2,"select * from (select * from (select a.no_bpb,a.curr, a.pono, a.tgl_bpb, a.tgl_po, SUM(a.qty * a.price) as sub, if(a.qty is null,SUM((a.qty * a.price) * (a.tax / 100)) ,SUM(((a.qty) * a.price) * (a.tax / 100))) as tax, if(a.qty is null,SUM((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100))) ,SUM((a.qty * a.price) + (((a.qty) * a.price) * (a.tax / 100)))) as total, a.top, a.confirm1, a.confirm2, a.supplier,a.id_item,a.id_supplier,b.mattype,if(b.matclass like '%ACCESORIES%','ACCESORIES',b.matclass) matclass,if(b.n_code_category is null,'-',b.n_code_category) n_code_category from bpb_new a INNER JOIN masteritem b on b.id_item = a.id_item  where a.supplier = '$nama_supp' and a.tgl_bpb between '$start_date' and '$end_date' and a.is_invoiced != 'Invoiced' and a.confirm2 != '' and status != 'Cancel' and a.profit_center = '$profit_center' group by a.no_bpb) a
UNION
select * from (select a.no_bpb,a.curr, a.pono, a.tgl_bpb, a.tgl_po, SUM(a.qty * a.price) as sub, if(a.qty is null,SUM((a.qty * a.price) * (a.tax / 100)) ,SUM(((a.qty) * a.price) * (a.tax / 100))) as tax, if(a.qty is null,SUM((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100))) ,SUM((a.qty * a.price) + (((a.qty) * a.price) * (a.tax / 100)))) as total, a.top, a.confirm1, a.confirm2, a.supplier,a.id_item,a.id_supplier,b.mattype,if(b.matclass like '%ACCESORIES%','ACCESORIES',b.matclass) matclass,if(b.n_code_category is null,'-',b.n_code_category) n_code_category from bpb_new a INNER JOIN masteritem b on b.id_item = a.id_item INNER JOIN (select DISTINCT no_bpb from kontrabon where no_kbon = '$no_kbon' and status != 'Cancel') kbn on kbn.no_bpb = a.no_bpb where a.supplier = '$nama_supp' and a.tgl_bpb between '$start_date' and '$end_date' and a.confirm2 != '' and status != 'Cancel' and a.profit_center = '$profit_center' group by a.no_bpb) a) a GROUP BY no_bpb");


while($row = mysqli_fetch_array($sql)){
    $bpb = $row['no_bpb'];
    $id_supplier = $row['id_supplier'];
    $pono = isset($row['pono']) ? $row['pono'] : null;

    $mattype = $row['mattype'];
    $matclass1 = $row['matclass'];
    if ($mattype == 'C') {
        if ($matclass1 == 'CMT' || $matclass1 == 'PRINTING' || $matclass1 == 'EMBRODEIRY' || $matclass1 == 'WASHING' || $matclass1 == 'PAINTING' || $matclass1 == 'HEATSEAL') {
            $matclass = $matclass1;
        }else{
            $matclass = 'OTHER';
        }
    }else{
        $matclass = $matclass1;
    }

    if ($id_supplier == '342' || $id_supplier == '20' || $id_supplier == '19' || $id_supplier == '692' || $id_supplier == '17' || $id_supplier == '18') {
        $cust_ctg = 'Related';
    }else{
        $cust_ctg = 'Third';
    }

    $n_code_category = $row['n_code_category'];

    $Aquer = mysqli_query($conn2,"select no_po, sum(amount) as amount from list_payment_dp where no_po = '$pono' and status_int >= '4'");
    $b = mysqli_fetch_array($Aquer);
    $po_no = isset($b['no_po']) ? $b['no_po'] : null ;
    $dp = isset($b['amount']) ? $b['amount'] : 0 ;

    $Aquerrff = mysqli_query($conn2,"select DISTINCT list_payment_dp.no_po as no_po, sum(DISTINCT kontrabon_h.dp_value) as amount from list_payment_dp inner join kontrabon_h on kontrabon_h.no_po = list_payment_dp.no_po where kontrabon_h.no_po = '$pono' and list_payment_dp.status_int = '5' and kontrabon_h.`status` != 'Cancel'");
    $f = mysqli_fetch_array($Aquerrff);
    $po_nno22 = isset($f['no_po']) ? $f['no_po'] : null ;
    $cbd22 = isset($f['amount']) ? $f['amount'] : 0 ;

    $tot_sisadp = $dp - $cbd22;

    $Aquerr = mysqli_query($conn2,"select b.no_po, sum(total) as amount_update from list_payment_cbd a inner join kontrabon_cbd b on b.no_kbon = a.no_kbon where b.no_po = '$pono' and a.status_int >= '4'");
    $c = mysqli_fetch_array($Aquerr);
    $po_nno = isset($c['no_po']) ? $c['no_po'] : null ;
    $cbd = isset($c['amount_update']) ? $c['amount_update'] : 0 ;

    $Aquerraa = mysqli_query($conn2,"select DISTINCT list_payment_cbd.no_po as no_po, sum(DISTINCT kontrabon_h.dp_value) as amount from list_payment_cbd inner join kontrabon_cbd a on a.no_kbon = list_payment_cbd.no_kbon  inner join kontrabon_h on kontrabon_h.no_po = a.no_po where kontrabon_h.no_po = '$pono' and list_payment_cbd.status_int = '5' and kontrabon_h.`status` != 'Cancel'");
    $d = mysqli_fetch_array($Aquerraa);
    $po_nno11 = isset($d['no_po']) ? $d['no_po'] : null ;
    $cbd11 = isset($d['amount']) ? $d['amount'] : 0 ;


    $tot_sisa = $cbd - $cbd11;

    $querys = mysqli_query($conn2,"select no_bpb, status from kontrabon where no_bpb = '$bpb' and status != 'Cancel'");
    $rows = mysqli_fetch_array($querys);
    $n_bpb = isset($rows['no_bpb']);
    $stat = isset($rows['status']);                            
    $sub = $row['sub'];
    $tax = $row['tax'];
    $total = $row['total'];
    $dpnol = 0;
    if($bpb == $n_bpb and $stat != 'Cancel'){
        echo '';
    }elseif($pono == $po_no){
        echo '<tr>
        <td style="width:10px;"><input type="checkbox" class="select_edit"  name="select_edit[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
        <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
        <td style="width:100px;">                            
        <select name="combo_pph_edit" class="combo_pph_edit form-control form-control-sm" style="width:100%;min-width:110px;">
        <option data-idtax="0" value="0" selected="selected">Non PPH</option>
        '.$persen.'                                                                                     
        </select>                                                        
        </td>                           
        <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
        <td class="dt_tax" style="width:100px;text-align:right;display: none;" data="'.$tot_sisadp.'">'.number_format($tot_sisadp,2).'</td>
        <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
        <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
        <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
        <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>    
        <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td> 
        <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
        <td style="display: none;" value="'.$mattype.'">'.$mattype.'</td> 
        <td style="display: none;" value="'.$matclass.'">'.$matclass.'</td> 
        <td style="display: none;" value="'.$n_code_category.'">'.$n_code_category.'</td>              
        <td style="display: none;" value="'.$cust_ctg.'">'.$cust_ctg.'</td> 
        </tr>';
    }elseif($pono == $po_nno){
        echo '<tr>
        <td style="width:10px;"><input type="checkbox" class="select_edit" name="select_edit[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
        <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
        <td style="width:100px;">                            
        <select name="combo_pph_edit" class="combo_pph_edit form-control form-control-sm" style="width:100%;min-width:110px;">
        <option data-idtax="0" value="0" selected="selected">Non PPH</option>
        '.$persen.'                                                                                     
        </select>                                                        
        </td>                           
        <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
        <td class="dt_tax" style="width:100px;text-align:right;display: none;" data="'.$tot_sisa.'">'.number_format($tot_sisa,2).'</td>
        <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
        <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
        <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
        <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td> 
        <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td> 
        <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>  
        <td style="display: none;" value="'.$mattype.'">'.$mattype.'</td> 
        <td style="display: none;" value="'.$matclass.'">'.$matclass.'</td> 
        <td style="display: none;" value="'.$n_code_category.'">'.$n_code_category.'</td>              
        <td style="display: none;" value="'.$cust_ctg.'">'.$cust_ctg.'</td>                                                                                                              
        </tr>';
    } else{
        echo '<tr>
        <td style="width:10px;"><input type="checkbox" class="select_edit" name="select_edit[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
        <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
        <td style="width:100px;">                            
        <select name="combo_pph_edit" class="combo_pph_edit form-control form-control-sm" style="width:100%;min-width:110px;">
        <option data-idtax="0" value="0" selected="selected">Non PPH</option>
        '.$persen.'                                                                                     
        </select>                                                        
        </td>                           
        <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
        <td class="dt_tax" style="width:100px;text-align:right;display: none;" data="'.$dpnol.'">'.number_format($dpnol,2).'</td>
        <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
        <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
        <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
        <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td> 
        <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td> 
        <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>  
        <td style="display: none;" value="'.$mattype.'">'.$mattype.'</td> 
        <td style="display: none;" value="'.$matclass.'">'.$matclass.'</td> 
        <td style="display: none;" value="'.$n_code_category.'">'.$n_code_category.'</td>              
        <td style="display: none;" value="'.$cust_ctg.'">'.$cust_ctg.'</td>                                                                                                              
        </tr>';
    }            
}  

?>
