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


$querys = mysqli_query($conn2,"select * from (select curr,no_bppb, tgl_bppb, no_ro, no_bpb, sum((qty * price) + ((qty * price) * (tax /100))) as total,round(sum((qty * price) + ((qty * price) * (tax /100))),2) as total2 from bppb_new where supplier = '$nama_supp' and status != 'Cancel' and (no_kbon is null OR no_kbon = '$no_kbon') and status = 'GMF-PCH' GROUP BY no_bppb) a where total2 > 0 and tgl_bppb BETWEEN '$start_date' and '$end_date' order by no_bppb asc");

while($row1 = mysqli_fetch_array($querys)){
    $ro_no = isset($row1['no_ro']) ? $row1['no_ro'] : null ;
    $bpb_rtn = isset($row1['no_bppb']) ? $row1['no_bppb'] : null ;
    $tot_ro = isset($row1['total']) ? $row1['total'] : 0;
    $tot_ro2 = isset($row1['total2']) ? $row1['total2'] : 0;


    $Aquer123 = mysqli_query($conn2,"select DISTINCT no_bpbrtn, sum(DISTINCT total_ro) as amount from return_kb where no_bpbrtn = '$bpb_rtn' and status != 'Cancel' and no_kbon != '$no_kbon'");
    $g = mysqli_fetch_array($Aquer123);
    $nobpbrtn = isset($g['no_bpbrtn']) ? $g['no_bpbrtn'] : null ;
    $amt_ro = isset($g['amount']) ? $g['amount'] : 0 ;
    $sisaro = $tot_ro2 - $amt_ro;

    $querybppb = mysqli_query($conn2,"select bppbno,bppbno_int,bppbdate,curr,id_supplier,supplier,mattype,n_code_category,matclass,curr,COALESCE(tax,0) tax,username,dateinput,(dpp + (dpp * (COALESCE(tax,0)/100))) total,dpp,(dpp * (COALESCE(tax,0)/100)) ppn from (select bppbno, bppbno_int, bppb.bppbdate, bppb.id_supplier, supplier, mattype, n_code_category, 
        if(matclass like '%ACCESORIES%','ACCESORIES',mi.matclass) matclass, bppb.curr,bppb.username, bppb.dateinput, 
        SUM(((qty) * price)) as dpp,bpbno_ro
        from bppb 
        inner join masteritem mi on bppb.id_item = mi.id_item
        inner join mastersupplier ms on bppb.id_supplier = ms.id_supplier
        where bppbno_int = '$bpb_rtn' group by bppbno) a left join

        (select bpbno,pono from bpb where bpbno = (select DISTINCT bpbno_ro from bppb where bppbno_int = '$bpb_rtn') GROUP BY bpbno) b on b.bpbno = a.bpbno_ro
        left JOIN
        (select pono,tax from po_header GROUP BY pono) c on c.pono = b.pono");


    $rowbppb = mysqli_fetch_array($querybppb);

    $id_supplier_bppb = isset($rowbppb['id_supplier']) ? $rowbppb['id_supplier'] : null;

    $mattype_bppb = isset($rowbppb['mattype']) ? $rowbppb['mattype'] : null;
    $matclass1_bppb = isset($rowbppb['matclass']) ? $rowbppb['matclass'] : null;
    if ($mattype_bppb == 'C') {
        if ($matclass1_bppb == 'CMT' || $matclass1_bppb == 'PRINTING' || $matclass1_bppb == 'EMBRODEIRY' || $matclass1_bppb == 'WASHING' || $matclass1_bppb == 'PAINTING' || $matclass1_bppb == 'HEATSEAL') {
            $matclass_bppb = $matclass1_bppb;
        }else{
            $matclass_bppb = 'OTHER';
        }
    }else{
        $matclass_bppb = $matclass1_bppb;
    }

    if ($id_supplier_bppb == '342' || $id_supplier_bppb == '20' || $id_supplier_bppb == '19' || $id_supplier_bppb == '692' || $id_supplier_bppb == '17' || $id_supplier_bppb == '18') {
        $cust_ctg_bppb = 'Related';
    }else{
        $cust_ctg_bppb = 'Third';
    }

    $n_code_category_bppb = isset($rowbppb['n_code_category']) ? $rowbppb['n_code_category'] : null;


    if($sisaro != 0 || $nobpbrtn == null){
        echo '<tr>
        <td style="width:10px;"><input type="checkbox" class="select2_edit"  name="select2_edit[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
        <td style="width:50px;" data-ro="'.$row1['no_ro'].'">'.$row1['no_ro'].'</td>
        <td style="width:50px;" valuess="'.$row1['no_bppb'].'">'.$row1['no_bppb'].'</td>
        <td style="width:100px;" valuess="'.$row1['tgl_bppb'].'">'.date("d-M-Y",strtotime($row1['tgl_bppb'])).'</td>                            
        <td style="width:50px;" valuess="'.$row1['no_bpb'].'">'.$row1['no_bpb'].'</td>                            
        <td style="width:100px;text-align:right;" data-total-ro-edit="'.round($sisaro,2).'">'.number_format($sisaro,2).'</td>
        <td style="width:100px;">
        <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" class="txt_amount_edit" name="txt_amount_edit" value="'.round($sisaro,2).'" disabled>
        </td>
        <td style="display: none;" valuess="'.$row1['curr'].'">'.$row1['curr'].'</td>     
        <td style="display: none;" valuess="'.$mattype_bppb.'">'.$mattype_bppb.'</td> 
        <td style="display: none;" valuess="'.$matclass_bppb.'">'.$matclass_bppb.'</td> 
        <td style="display: none;" valuess="'.$n_code_category_bppb.'">'.$n_code_category_bppb.'</td>              
        <td style="display: none;" valuess="'.$cust_ctg_bppb.'">'.$cust_ctg_bppb.'</td>                                                                                            
        </tr>';
    }else{
        echo '';
    }}

    ?>
