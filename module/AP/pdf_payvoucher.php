<?php
include '../../conn/conn.php';

// ob_start() harus paling awal, sebelum query/echo apapun - kalau ada output
// (termasuk PHP notice) yang lolos sebelum ini, teksnya bakal nyelip di depan
// byte "%PDF-" pas mpdf Output() dipanggil di akhir file, jadi PDF-nya corrupt.
ob_start();

$no_pv = $_GET['no_pv'] ?? '';

$rs = mysqli_fetch_array(mysqli_query($conn2, "select * from tbl_pv_h where no_pv = '$no_pv'")) ?: [];

$sqlys = " select a.no_pv,concat(b.no_coa,' - ',b.nama_coa) as nama_coa,if(d.cc_name is null,'-',concat(d.no_cc, ' - ',d.cc_name)) cc_name, if(a.reff_doc = '','-',a.reff_doc) as reff_doc,a.reff_date,if(a.deskripsi = '','-',a.deskripsi) as deskripsi,a.amount,a.ded_add,a.due_date, (a.amount * (a.pph/100)) as pph, coalesce(pc.nama_pc,'-') profit_center from tbl_pv a left join mastercoa_v2 b on b.no_coa = a.coa left join b_master_cc d on d.no_cc = a.no_cc left join master_pc pc on pc.kode_pc = a.profit_center where no_pv = '$no_pv' and amount != '0' OR no_pv = '$no_pv' and ded_add != '0' order by a.reff_doc asc";

$sqlas = "select curr from tbl_pv_h where no_pv = '$no_pv'";
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <style>



    @page * {
      margin-top: 1.54cm;
      margin-bottom: 1.54cm;
      margin-left: 3.175cm;
      margin-right: 3.175cm;
    }

    table   { margin: auto; border-collapse: collapse; width: 100%; }
    td, th  { padding: 3px 2px; text-align: left; }
    h1      { text-align: center; }
    th      { text-align: center; padding: 10px; }

    .footer        { width: 100%; height: 30px; margin-top: 50px; text-align: right; }
    .header        { width: 100%; height: 20px; padding-top: 0; margin-bottom: 10px; }
    .title         { font-size: 30px; font-weight: bold; text-align: center; margin-top: -90px; }
    .horizontal    { height: 0; width: 100%; border: 1px solid #000000; }
    .position_top  { vertical-align: top; }
    .td1           { border: 1px solid black; border-top: none; border-bottom: none; }
    .header_title  { width: 100%; height: auto; text-align: center; font-size: 12px; }

    /* Sel di 2 tabel info (header dokumen + Payment To..Amount) pakai class di
       bawah ini gantiin inline style yang tadinya diulang manual di tiap <td>.
       Border tiap sisi di-none-kan satu-satu supaya yang kelihatan cuma
       bingkai TERLUAR tabel (efek "borderless") - baris/kolom di tengah semua
       nge-blend karena saling menghilangkan border masing-masing. table2 juga
       nge-none-in border-top di SEMUA barisnya karena nempel langsung di
       bawah table1 (yang gambar garis pemisah cukup dari border-bottom baris
       terakhir table1, biar gak dobel garis). */
    .fs12 { font-size: 12px; }
    .bl0  { border-left: none; }
    .br0  { border-right: none; }
    .bt0  { border-top: none; }
    .bb0  { border-bottom: none; }
    .bn   { border: none; }
    .vt   { vertical-align: top; }
    .tac  { text-align: center; }
    .tar  { text-align: right; }
    .fwb  { font-weight: bold; }
    .w1   { width: 1%; }
    .w2   { width: 2%; }
    .w3   { width: 3%; }
    .w12  { width: 12%; }
    .w19  { width: 19%; }
    .w38  { width: 38%; }
    .w51  { width: 51%; }

    /* Header dokumen dibuat rapat: tanpa ruang padding atas/bawah pada
       sel judul, logo, dan identitas voucher. */
    .voucher-document-header td {
      padding: 0 2px;
      line-height: 16px;
    }

    /* Tabel tanda tangan menempel ke tabel header sebelumnya. Hilangkan
       border atas tabel ini supaya tidak menjadi garis dobel/lebih tebal. */
    .approval-signatures {
      border-collapse: collapse;
      border: 1px solid #000;
      border-top: 0 !important;
      margin-bottom: 0;
      table-layout: fixed;
      width: 100%;
    }
    .approval-signatures td {
      border: 1px solid #000;
    }
    .approval-signatures tr:first-child td {
      border-top: 0 !important;
    }

    /* Kolom label informasi voucher tidak boleh mengambil sisa lebar tabel;
       titik dua dibuat dekat dengan labelnya. */
    .voucher-info tr > td:first-child {
      width: 13% !important;
      padding-right: 0;
      white-space: nowrap;
    }
    .voucher-info tr > td:nth-child(2) {
      padding-left: 0;
    }
    .voucher-info td {
      padding-top: 4px;
      padding-bottom: 4px;
    }

    /* Area Description di header selalu disediakan untuk empat baris.
       Teks selebihnya disembunyikan agar layout PDF tetap konsisten. */
    .description-fixed-4-lines {
      height: 56px;
      line-height: 14px;
      overflow: hidden;
      white-space: pre-line;
    }
</style>


<title>Payment Voucher</title>
</head>
<body style=" padding-left:5%; padding-right:5%;">
  <div class="header">
    <table class="voucher-document-header" width="100%" border="1">
        <tr>
            <td rowspan ="3" style="border-right: none;">
                <img src="../../images/img-01.png" style="heigh:65px; width:75px;">
            </td>
            <td rowspan ="3" style="text-align: center;border-left: none; width: 51%;font-weight: bold;">
                PAYMENT VOUCHER
            </td>
            <td style="border-right: none;font-size: 12px;width: 12%;border-bottom: none;">
                Document No
            </td>
            <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-bottom: none;">
                :
            </td>
            <td style="border-left: none;font-size: 12px;border-bottom: none;">
                <?php echo $no_pv?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none;font-size: 12px;width: 12%;border-bottom: none;border-top: none;">
                Date
            </td>
            <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-bottom: none;border-top: none;">
                :
            </td>
            <td style="border-left: none;font-size: 12px;border-bottom: none;border-top: none;">
                <?php echo date("d F Y",strtotime($rs['pv_date']));?>
            </td>
        </tr>
        <tr>
            <td style="border-right: none;font-size: 12px;width: 12%;border-top: none;">
                Division
            </td>
            <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;">
                :
            </td>
            <td style="border-left: none;font-size: 12px;border-top: none;white-space: nowrap;">
                PT Nirwana Alabare Garment
            </td>
        </tr>
    </table>
    <table class="voucher-info" width="100%" border="1">
       <tr>
        <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
            Payment To
        </td>
        <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
            :
        </td>
        <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
            <?php echo $rs['nama_supp'];?>
        </td>
        <td style="width: 3%; border: none;">

        </td>
        <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
            Payment Date
        </td>
        <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
            :
        </td>
        <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;">
            <?php echo date("d F Y",strtotime($rs['pay_date']));?>
        </td>
        <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">

        </td>
    </tr>
    <tr>
        <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
            Payment For
        </td>
        <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
            :
        </td>
        <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
            <?php
            $sql3 = mysqli_query($conn2," select for_pay,ke,dari from tbl_pv_h where no_pv = '$no_pv'");
            $rows3 = mysqli_fetch_array($sql3);
            $for_pay = $rows3['for_pay'];
            $ke = $rows3['ke'];
            $dari = $rows3['dari'];
            if ($for_pay == 'Cicilan Pinjaman Bank' || $for_pay == 'Cicilan Aktiva Tetap') {
             echo $for_pay; echo ' (Cicilan ke '; echo $ke; echo ' dari '; echo $dari; echo')';
         }else{
             echo $for_pay;
         }
         ?>
     </td>
     <td style="width: 3%; border: none;">

     </td>
     <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">

     </td>
     <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">

     </td>
     <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">

     </td>
     <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">

     </td>
 </tr>

 <tr>
    <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        Payment Method
    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        :
    </td>
    <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
        <?php echo $rs['pay_meth'];?>
    </td>
    
 <td style="width: 3%; border: none;">

 </td>
 <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;border-left: none;">
        Cheque No
    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        :
    </td>
    <td style="border-left: none;font-size: 12px;border-top: none;border-right: none;">
       <?php
       $sql3 = mysqli_query($conn2," select no_cek from tbl_pv_h where no_pv = '$no_pv'");
       $rows3 = mysqli_fetch_array($sql3);
       $no_cek = $rows3['no_cek'];
       if ($no_cek == '') {
         echo '-'; 
     }else{
         echo $no_cek;
     }
     ?>
 </td>
<td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">

</td>
</tr>

<tr>
    <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        Charge To Buyer
    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        :
    </td>
    <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
     <?php $ctb =  $rs['ctb'];
     if ($ctb == '' || $ctb == 'Unrealize') {
         echo '-'; 
     }else{
         echo $ctb;
     }?>
 </td>
 <td style="width: 3%; border: none;">

 </td>
  <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">
    Cheque Date
</td>
<td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
    :
</td>
<td style="border-left: none;font-size: 12px; border-top: none;border-right: none;">
    <?php
    $sql3 = mysqli_query($conn2," select cek_date from tbl_pv_h where no_pv = '$no_pv'");
    $rows3 = mysqli_fetch_array($sql3);
    $cek_date = $rows3['cek_date'];
    if ($cek_date == '0000-00-00' || $cek_date == '1970-01-01') {
     echo '-'; 
 }else{
     echo date("d F Y",strtotime($cek_date));
 }
 ?>
</td>
 
 <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">

 </td>
</tr>

 <tr>
    <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        From Account
    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        :
    </td>
    <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
     <?php
     $sql3 = mysqli_query($conn2," select CONCAT(sob,' ',frm_akun) frm_akun from tbl_pv_h a INNER JOIN b_masterbank b on b.bank_account = a.frm_akun where no_pv = '$no_pv'");
     $rows3 = mysqli_fetch_array($sql3);
     $frm_akun = $rows3['frm_akun'];
     if ($frm_akun == '-') {
         echo ''; 
     }else{
         echo $frm_akun;
     }
     ?>
 </td>
 <td style="width: 3%; border: none;">

 </td>
  <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">

 </td>
 <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">

 </td>
 <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">

 </td>
 
<td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">

</td>
</tr>
<tr>
    <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-bottom: none;">
    To Account
</td>
<td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">
    :
</td>
<td colspan="5" style="border-left: none;font-size: 12px;border-top: none;border-right: none;">
    <?php
    $sql3 = mysqli_query($conn2," select UPPER(IFNULL(NULLIF(to_akun, ''), '-')) AS to_akun from (select CONCAT(beneficiary_name,' - ',bank_name,' - ',to_akun) to_akun from tbl_pv_h a INNER JOIN master_supplier_bank b on b.bank_account = a.to_akun where no_pv = '$no_pv'
    UNION ALL
    select CONCAT(beneficiary_name,' - ',bank_name,' ',to_akun) to_akun from tbl_pv_h a INNER JOIN b_masterbank b on b.bank_account = a.to_akun where no_pv = '$no_pv' and b.status = 'Active') a limit 1");
    $rows3 = mysqli_fetch_array($sql3);
    $to_akun = $rows3['to_akun'] ?? '-';
    if ($to_akun == '-') {
     echo '-'; 
 }else{
     echo $to_akun;
 }
 ?>
</td>

    <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">

    </td>
</tr>

<tr>
    <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        Supporting Doc
    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        :
    </td>
    <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
        <?php echo $rs['supp_doc'];?>
    </td>
    <td style="width: 3%; border: none;">

    </td>
    <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">

    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">

    </td>
    <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">

    </td>
    <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">

    </td>
</tr>

<tr>
    <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;vertical-align: top;">
        Refference Doc
    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        :
    </td>
    <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
        <?php
        $sql3 = mysqli_query($conn2," select GROUP_CONCAT(reff_doc) as reff_doc from (select if(reff_doc = '', '-', CONCAT(' ',reff_doc)) as reff_doc from tbl_pv where no_pv = '$no_pv' group by reff_doc ORDER BY id asc) b");
        $rows3 = mysqli_fetch_array($sql3);
        $reff_doc = $rows3['reff_doc'];
        if ($reff_doc == '') {
         echo ''; 
     }else{
         echo $reff_doc;
     }
     ?>
 </td>
 <td style="width: 3%; border: none;">

 </td>
<td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;border-left: none;vertical-align: top;">
        Reff Doc Date
    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        :
    </td>
    <td style="border-left: none;font-size: 12px;border-top: none;border-right: none;">
        <?php
        $sql3 = mysqli_query($conn2,"select GROUP_CONCAT(reff_date) as reff_date from (select if(reff_date = '1970-01-01', '-', CONCAT(' ',reff_date)) as reff_date from tbl_pv where no_pv = '$no_pv' group by reff_doc ORDER BY id asc) a");
        $rows3 = mysqli_fetch_array($sql3);
        $reff_date = $rows3['reff_date'];
        if ($reff_date == '') {
         echo ''; 
     }else{
         echo $reff_date;
     }
     ?>
 </td>
 <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">

 </td>
</tr>

<tr>
    <td height="56" style="height:56px;border-right: none;font-size: 12px;border-top: none;border-bottom: none;width: 19%;vertical-align: top;">
        Description
    </td>
    <td height="56" style="height:56px;width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;vertical-align: top;">
        :
    </td>
    <td colspan="5" height="56" style="height:56px;border-left: none;font-size: 12px;border-top: none;border-right: none;vertical-align: top;">
        <div class="description-fixed-4-lines"><?php echo $rs['deskripsi'] ?? '';?></div>
    </td>
    <td height="56" style="height:56px;width: 2%;border-left: none; border-top: none;border-bottom: none;">

    </td>
</tr>

<tr>
    <td style="border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        Amount
    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;border-bottom: none;">
        :
    </td>
    <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none;">
       <?php echo $rs['curr']; echo ' '; echo number_format($rs['total'], 2); ?>
    </td>
    <td style="width: 3%; border: none;">

    </td>
    <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;border-bottom: none;">

    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;border-bottom: none;">

    </td>
    <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;border-bottom: none;">

    </td>
    <td style="width: 2%;border-left: none; border-top: none;border-bottom: none;">

    </td>
</tr>


<tr>
    <td style="border-right: none;font-size: 12px;border-top: none;padding-left: 10px;padding-top: 8px;">

    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px;border-top: none;">

    </td>
    <td style="border-left: none;font-size: 12px;width: 38%;border-top: none;border-right: none; text-align: right;padding-right: 20px;">

    </td>
    <td style="width: 3%;border-left: none;border-right: none;border-top: none; ">

    </td>
    <td style="border-right: none;font-size: 12px; width: 12%; border-top: none;border-left: none;">

    </td>
    <td style="width: 1%; border-left: none;border-right: none;font-size: 12px; border-top: none;">

    </td>
    <td style="border-left: none;font-size: 12px; border-top: none;border-right: none;">

    </td>
    <td style="width: 2%;border-left: none; border-top: none;">

    </td>
</tr>

</table>
<?php
$isTaxPv = ($rs['pv_tax_type'] ?? '') === 'Tax';
$approvedByCols = $isTaxPv ? 3 : 2;
$totalCols = 1 + 2 + $approvedByCols;
$mandyColIndex = 1;
$mandyWidthPct = (100 / $totalCols) + 2;
$otherWidthPct = (100 - $mandyWidthPct) / ($totalCols - 1);
$colWidths = [];
for ($i = 0; $i < $totalCols; $i++) {
    $colWidths[] = $i === $mandyColIndex ? $mandyWidthPct : $otherWidthPct;
}
?>
<table class="approval-signatures" width="100%" border="0">
    <tr>
        <td style="font-size: 11px; text-align: center; font-weight: bold;">
            Created By
        </td>
        <td colspan="2" style="font-size: 11px; text-align: center; font-weight: bold;">
            Checked By
        </td>
        <td colspan="<?= $approvedByCols ?>" style="font-size: 11px; text-align: center; font-weight: bold;">
            Approved By
        </td>
    </tr>
    <tr>
        <?php foreach ($colWidths as $w): ?>
        <td style="width:<?= $w ?>%;height: 40px;"></td>
        <?php endforeach; ?>
    </tr>
    <tr>
        <td style="width:<?= $colWidths[0] ?>%;font-size: 11px; text-align: center; font-weight: bold;">
            <?php
$sql_user = mysqli_query($conn2,"select DISTINCT CONCAT(UPPER(LEFT(if(create_by = 'mandy1','Mandy', create_by),1)),LOWER(SUBSTRING(if(create_by = 'mandy1','Mandy', create_by),2))) create_by from tbl_pv_h WHERE no_pv = '$no_pv'
");

$rows_user = mysqli_fetch_array($sql_user);
$user = $rows_user['create_by'];

echo $user;
?>

     </td>
        <td style="width:<?= $colWidths[1] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Mandy</td>
        <td style="width:<?= $colWidths[2] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Willy</td>
        <td style="width:<?= $colWidths[3] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Herman</td>
        <?php if ($isTaxPv): ?>
        <td style="width:<?= $colWidths[4] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Budiarto</td>
        <?php endif; ?>
        <td style="width:<?= $colWidths[$totalCols - 1] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Syenni Santosa</td>
    </tr>
    <tr>
        <td style="width:<?= $colWidths[0] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Staff Bank</td>
        <td style="width:<?= $colWidths[1] ?>%;font-size: 11px; text-align: center; font-weight: bold;">SPV Bank</td>
        <td style="width:<?= $colWidths[2] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Fin Manager</td>
        <td style="width:<?= $colWidths[3] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Kadept Fin&Acc</td>
        <?php if ($isTaxPv): ?>
        <td style="width:<?= $colWidths[4] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Kadiv Fin&Acc</td>
        <?php endif; ?>
        <td style="width:<?= $colWidths[$totalCols - 1] ?>%;font-size: 11px; text-align: center; font-weight: bold;">Director</td>
    </tr>
</table>

</div>


<?php
// Dorong section "DETAIL" ke halaman berikutnya - spasi manual karena mpdf
// versi ini tidak selalu konsisten menghormati page-break-before di sini.
echo str_repeat("<br/>\n", 46);
?>


<table style="page-break-inside: avoid; font-size:11px;" border="0">
 <tr>
    <td style="font-weight: bold">DETAIL :</td>
</tr>
<tr>
    <td>Payment Voucher Number : <?php echo $no_pv ?></td>
</tr>

</table>
<?php
$query1 = mysqli_query($conn2,$sqlas)or die(mysqli_error($conn2));
$data1=mysqli_fetch_array($query1);
$curr1 = $data1['curr'];
?>

<table  border="1" cellspacing="0" style="width:100%;font-size:10px;border-spacing:2px;">
  <tr>

      <th style="width: 15%;border: 1px solid black;text-align:center;">COA</th>
      <th style="width: 10%;border: 1px solid black;text-align:center;">Profit Center</th>
      <th style="width: 11%;border: 1px solid black;text-align:center;">Cost Center</th>
      <th style="width: 12%;border: 1px solid black;text-align:center;">Reff Doc</th>
      <th style="width: 8%;border: 1px solid black;text-align:center;">Reff Date</th>
      <th style="width: 19%;border: 1px solid black;text-align:center;">Description</th>
      <th style="width: 8%;border: 1px solid black;text-align:center;">Due Date</th>
      <th style="width: 9%;border: 1px solid black;text-align:center;">Amount (<?php echo $curr1 ?>)</th>
      <th style="width: 9%;border: 1px solid black;text-align:center;">Deduction (<?php echo $curr1 ?>)</th>
      <th style="width: 9%;border: 1px solid black;text-align:center;">PPH (<?php echo $curr1 ?>)</th>
  </tr>
  <tbody >
    <?php
    $query = mysqli_query($conn2,$sqlys)or die(mysqli_error($conn2));
    $sum_amount = 0;
    $sum_ded = 0;
    $sum_pph = 0;

    while($data=mysqli_fetch_array($query)){
       $sum_amount += $data['amount']; 
       $sum_ded += $data['ded_add'];
       $sum_pph += $data['pph'];
       $duedate = $data['due_date'];
       $reffdate = $data['reff_date'];
       if ($duedate == '' || $duedate == '1970-01-01') { 
           $due_date = '-';
       }else{
        $due_date = date("d-M-Y",strtotime($data['due_date'])); 
    } 
    if ($reffdate == '' || $reffdate == '1970-01-01') { 
       $reff_date = '-';
   }else{
    $reff_date = date("d-M-Y",strtotime($data['reff_date'])); 
} 
echo '<tr>
<td style="text-align: center" value="'.$data['nama_coa'].'">'.$data['nama_coa'].'</td>
<td style="text-align: center" value="'.$data['profit_center'].'">'.$data['profit_center'].'</td>
<td style="text-align: center" value="'.$data['cc_name'].'">'.$data['cc_name'].'</td>
<td style="text-align: center" value="'.$data['reff_doc'].'">'.$data['reff_doc'].'</td> 
<td style="text-align: center" value="'.$reff_date.'">'.$reff_date.'</td>                                                                      
<td style="text-align: center" value="'.$data['deskripsi'].'">'.$data['deskripsi'].'</td>                            
<td style="text-align: center" value="'.$due_date.'">'.$due_date.'</td>
<td style="text-align: right" value="'.$data['amount'].'">'.number_format($data['amount'],2).'</td>
<td style="text-align: right" value="'.$data['ded_add'].'">- '.number_format($data['ded_add'],2).'</td>                            
<td style="text-align: right" value="'.$data['pph'].'">- '.number_format($data['pph'],2).'</td>
</tr>';	
};	
?>
<tr>
  <td colspan="7" style="width:70%;border: 1px solid black;text-align:center;font-size:10px"><b>Total</b></td>
  <td style="width:10%;text-align:right;"><?php echo number_format($sum_amount,2) ?></td> 
  <td style="width:10%;text-align:right;"><?php echo '- '.number_format($sum_ded,2) ?></td>
  <td style="width:10%;text-align:right;"><?php echo '- '.number_format($sum_pph, 2) ?></td>
</tr>

</tbody>
</table>
<br>
<?php
$sql2 = mysqli_query($conn1,"select no_pv,subtotal,adjust,pph,ppn,total from tbl_pv_h where no_pv = '$no_pv'");
$row2 = mysqli_fetch_assoc($sql2);
?>

<div style="margin-bottom: 2.54cm; page-break-inside: avoid;">
    <table width="100%" border="0" style="page-break-inside: avoid;font-size:11px;">

       <tr>
          <td width="58%">

          </td>

          <td>
             SubTotal
         </td>
         <td style="width:1%">:</td>
         <td style="text-align:right">
             <?php echo $curr1." ".number_format($row2['subtotal'], 2); ?>
         </td>		
     </tr>

     <tr>
      <td width="58%">

      </td>

      <td>
         Deduction
     </td>
     <td style="width:1%">:</td>
     <td style="text-align:right">
         <?php echo $curr1."( - ".number_format(abs($row2['adjust']), 2)." )"; ?>
     </td>		
 </tr>
 <tr>
  <td width="58%">

  </td>

  <td>
     PPN
 </td>
 <td style="width:1%">:</td>
 <td style="text-align:right">
     <?php echo $curr1." ".number_format($row2['ppn'], 2); ?>
 </td>		
</tr>			
<tr>
  <td width="58%">

  </td>

  <td>
     PPH
 </td>
 <td style="width:1%">:</td>
 <td style="text-align:right;">
     <?php echo $curr1."( - ".number_format(abs($row2['pph']), 2)." )"; ?>
 </td>		
</tr>

<tr>
  <td width="58%">

  </td>

  <td style="font-weight: bold;">
     Total
 </td>
 <td style="width:1%">:</td>
 <td style="text-align:right;font-weight: bold;">
     <?php echo $curr1." ".number_format($row2['total'], 2) ?>
 </td>
</tr>

</table>
</div> 

</body>


</html>  

<?php
$html = ob_get_clean();

require_once __DIR__ . '/../../mpdf8/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . '/../../mpdf8/tmp'
]);

$mpdf->WriteHTML($html);
$mpdf->Output();
exit;
?>
