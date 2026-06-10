<html>
<head>
    <title>Export Data </title>
</head>
<body>
    <style type="text/css">
        body{
            font-family: sans-serif;
        }
        table{
            margin: 20px auto;
            border-collapse: collapse;
        }
        table th,
        table td{
            border: 1px solid #3c3c3c;
            padding: 3px 8px;

        }
        a{
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Item General Usage.xls");
    include '../../conn/conn.php';
    $startdate = date("d F Y",strtotime($_GET['start_date']));
    $enddate = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4>ITEM GENERAL USAGE<br/> PERIODE: <?php echo $startdate; ?> - <?php echo $enddate; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
                <th style="text-align: center;vertical-align: middle;">No</th>
                <th style="text-align: center;vertical-align: middle;">Trans #</th>
                <th style="text-align: center;vertical-align: middle;">Tgl. Trans</th>
                <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                <th style="text-align: center;vertical-align: middle;">No Coa</th>
                <th style="text-align: center;vertical-align: middle;">Nama Coa</th>
                <th style="text-align: center;vertical-align: middle;">No Cost Center</th>
                <th style="text-align: center;vertical-align: middle;">Nama Cost Center</th>
                <th style="text-align: center;vertical-align: middle;">Inv #</th>
                <th style="text-align: center;vertical-align: middle;">Jenis Dok</th>
                <th style="text-align: center;vertical-align: middle;">Nomor Aju</th>
                <th style="text-align: center;vertical-align: middle;">Tgl Aju</th>
                <th style="text-align: center;vertical-align: middle;">Nomor Daftar</th>
                <th style="text-align: center;vertical-align: middle;">Tgl Daftar</th>
                <th style="text-align: center;vertical-align: middle;">Supplier</th>
                <th style="text-align: center;vertical-align: middle;">PO #</th>
                <th style="text-align: center;vertical-align: middle;">Type #</th>
                <th style="text-align: center;vertical-align: middle;">Inv/SJ #</th>
                <th style="text-align: center;vertical-align: middle;">Id Item</th>
                <th style="text-align: center;vertical-align: middle;">Kode Barang</th>
                <th style="text-align: center;vertical-align: middle;">Nama Barang</th>
                <th style="text-align: center;vertical-align: middle;">Kategori</th>
                <th style="text-align: center;vertical-align: middle;">Warna</th>
                <th style="text-align: center;vertical-align: middle;">Ukuran</th>
                <th style="text-align: center;vertical-align: middle;">Jumlah BPB</th>
                <th style="text-align: center;vertical-align: middle;">Satuan</th>
                <th style="text-align: center;vertical-align: middle;">Berat Bersih</th>
                <th style="text-align: center;vertical-align: middle;">Keterangan</th>
                <th style="text-align: center;vertical-align: middle;">Nama User</th>
                <th style="text-align: center;vertical-align: middle;">Approve By</th>
                <th style="text-align: center;vertical-align: middle;">WS #</th>
                <th style="text-align: center;vertical-align: middle;">Style #</th>
                <th style="text-align: center;vertical-align: middle;">Curr</th>
                <th style="text-align: center;vertical-align: middle;">Price</th>
                <th style="text-align: center;vertical-align: middle;">Jenis Trans</th>
                <th style="text-align: center;vertical-align: middle;">Reff No</th>
                
            </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        $id_supplier = $_GET['id_supplier'];

        if ($id_supplier != 'ALL') {
            $where = "and a.id_supplier = '$id_supplier'";
        }else{
            $where = "";
        }


        $sql = mysqli_query($conn2,"select a.*, IFNULL(b.no_coa,'-') no_coa, IFNULL(b.nama_coa,'-') nama_coa from (SELECT if(a.bpbno_int!='',a.bpbno_int,a.bpbno) bpbno,a.bpbdate, ifnull(mp.nama_pc, 'NIRWANA ALABARE GARMENT') profit_center, a.invno,a.jenis_dok,right(a.nomor_aju,6) nomor_aju,a.tanggal_aju, lpad(a.bcno,6,'0') bcno,a.bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,s.goods_code, CONCAT(s.itemdesc,' ',coalesce(s.add_info,'')) itemdesc,m.description,s.color,s.size, a.qty,a.unit,a.berat_bersih,a.remark,a.username,CONCAT(a.confirm_by, ' (', a.confirm_date, ')') AS confirm_by,r.reqno,'' whs_code,a.curr,if(z.tipe_com ='FOC','0',a.price) price,a.jenis_trans,a.reffno, IFNULL(cc.no_cc,'-') no_cc, IFNULL(cc.cc_name,'-') cc_name,CASE
    WHEN id_group2 = 1 THEN coa_production
    WHEN id_group2 = 2 THEN coa_sup_production
    WHEN id_group2 = 3 THEN coa_sup_gen_adm
    WHEN id_group2 = 4 THEN coa_sup_selling
    ELSE NULL
END AS coa
    from bpb a 
    inner join masteritem s on a.id_item=s.id_item 
    LEFT join (select pono,tipe_com from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft) z on a.pono = z.pono 
    LEFT join mastersupplier d on a.id_supplier=d.id_supplier 
    LEFT join master_pc mp on mp.kode_pc = a.profit_center 
    LEFT OUTER JOIN mapping_category AS m ON s.n_code_category=m.n_id 
    left join reqnon_header r on a.id_jo = r.id 
    LEFT JOIN userpassword up on up.username = r.username
    LEFT JOIN b_master_cc cc on cc.no_cc = up.no_cc
    where bpbno_int LIKE '%GEN%' AND a.bpbdate >= '$start_date' AND a.bpbdate <= '$end_date' $where) a LEFT JOIN mastercoa_v2 b on b.no_coa = a.coa
");



$no = 1;
while($row = mysqli_fetch_array($sql)){

    echo ' <tr style="font-size:12px;text-align:left;">
    <td style="text-align:center;">'.$no++.'</td>
    <td value="'.$row['bpbno'].'">'.$row['bpbno'].'</td> 
    <td value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>                          
    <td value="'.$row['profit_center'].'">'.$row['profit_center'].'</td>                            
    <td value="'.$row['no_coa'].'">'.$row['no_coa'].'</td> 
    <td value="'.$row['nama_coa'].'">'.$row['nama_coa'].'</td> 
    <td value="'.$row['no_cc'].'">'.$row['no_cc'].'</td> 
    <td value="'.$row['cc_name'].'">'.$row['cc_name'].'</td> 
    <td value="'.$row['invno'].'">'.$row['invno'].'</td> 
    <td value="'.$row['jenis_dok'].'">'.$row['jenis_dok'].'</td> 
    <td value="'.$row['nomor_aju'].'">'.$row['nomor_aju'].'</td> 
    <td value="'.$row['tanggal_aju'].'">'.
(
    empty($row['tanggal_aju']) ||
    $row['tanggal_aju'] == '0000-00-00' ||
    $row['tanggal_aju'] == '0000-00-00 00:00:00'
        ? '-'
        : date("d-M-Y", strtotime($row['tanggal_aju']))
).
'</td>
    <td value="'.$row['bcno'].'">'.$row['bcno'].'</td> 
    <td value="'.$row['bcdate'].'">'.
(
    empty($row['bcdate']) ||
    $row['bcdate'] == '0000-00-00' ||
    $row['bcdate'] == '0000-00-00 00:00:00'
        ? '-'
        : date("d-M-Y", strtotime($row['bcdate']))
).
'</td>
    <td value="'.$row['supplier'].'">'.$row['supplier'].'</td>                            
    <td value="'.$row['pono'].'">'.$row['pono'].'</td> 
    <td value="'.$row['tipe_com'].'">'.$row['tipe_com'].'</td> 
    <td value="'.$row['invno'].'">'.$row['invno'].'</td> 
    <td value="'.$row['id_item'].'">'.$row['id_item'].'</td> 
    <td value="'.$row['goods_code'].'">'.$row['goods_code'].'</td>                            
    <td value="'.$row['itemdesc'].'">'.$row['itemdesc'].'</td> 
    <td value="'.$row['description'].'">'.$row['description'].'</td> 
    <td value="'.$row['color'].'">'.$row['color'].'</td> 
    <td value="'.$row['size'].'">'.$row['size'].'</td> 
    <td style="text-align:right;" value = "'.number_format($row['qty'],4).'">'.number_format($row['qty'],4).'</td>
    <td value="'.$row['unit'].'">'.$row['unit'].'</td> 
    <td value="'.$row['berat_bersih'].'">'.$row['berat_bersih'].'</td> 
    <td value="'.$row['remark'].'">'.$row['remark'].'</td> 
    <td value="'.$row['username'].'">'.$row['username'].'</td>
    <td value="'.$row['confirm_by'].'">'.$row['confirm_by'].'</td> 
    <td value="'.$row['reqno'].'">'.$row['reqno'].'</td> 
    <td value="'.$row['whs_code'].'">'.$row['whs_code'].'</td> 
    <td value="'.$row['curr'].'">'.$row['curr'].'</td>
    <td style="text-align:right;" value = "'.number_format($row['price'],4).'">'.number_format($row['price'],4).'</td>
    <td value="'.$row['jenis_trans'].'">'.$row['jenis_trans'].'</td> 
    <td value="'.$row['reffno'].'">'.$row['reffno'].'</td>
    ';

    ?>
    <?php 

}
?>
</table>

</body>
</html>




