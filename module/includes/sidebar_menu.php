<?php
/**
 * Shared AdminLTE sidebar menu.
 * Requires: $conn1, $conn2, $user already defined.
 * Requires: $AP = URL prefix to the AP folder (e.g. '../AP/' or 'AP/')
 */
if (!isset($AP)) $AP = '../AP/';

function _ni($href, $label, $badge = '', $icon = '') {
    $ico = $icon
        ? '<i class="nav-icon '.$icon.'"></i>'
        : '<i class="far fa-circle nav-icon" style="font-size:8px;"></i>';
    return '<li class="nav-item"><a href="'.$href.'" class="nav-link">'
          .$ico.'<p>'.$label.' '.$badge.'</p></a></li>';
}
function _so($faClass, $label) {
    return '<li class="nav-item has-treeview"><a href="#" class="nav-link">'
          .'<i class="nav-icon '.$faClass.'"></i>'
          .'<p>'.$label.' <i class="right fas fa-angle-left"></i></p>'
          .'</a><ul class="nav nav-treeview">';
}
function _sc() { return '</ul></li>'; }
?>

<ul class="nav nav-pills nav-sidebar flex-column"
    data-widget="treeview" role="menu" data-accordion="false">

  <!-- ══ MASTER ══════════════════════════════════════════ -->
  <li class="nav-header">MASTER</li>
  <?php
  $q = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Master'");
  $r = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  if ($id == '49') {
      echo _so('fas fa-book', 'Master');
      echo _ni($AP.'master-cash-flow.php',        'Cash Flow');
      echo _ni($AP.'master-coa-category1.php',    'Category COA');
      echo _ni($AP.'master-coa.php',              'Chart Of Account');
      echo _ni($AP.'master-costcenter.php',       'Cost Center');
      echo _ni($AP.'master-profit-center.php',    'Profit Center');
      echo _ni($AP.'master-bank.php',             'Bank');
      echo _ni($AP.'master-mapping-memo.php',     'Mapping Memo');
      echo _ni($AP.'master-supplier-bank.php',    'Master Bank Supplier');
      echo _ni($AP.'master-rate.php',             'Rate');
      echo _sc();
  }
  ?>

  <!-- ══ ACCOUNTS PAYABLE ════════════════════════════════ -->
  <li class="nav-header">ACCOUNTS PAYABLE</li>
  <?php
  $qss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%BPB%' and useraccess.menu != 'Transfer BPB' and useraccess.menu != 'Accept BPB Whs-Acc' and useraccess.menu != 'Maintain BPB' and useraccess.menu != 'Acct - Rekonsiliasi Jurnal-BPB' and useraccess.menu != 'Acct - Repost Jurnal BPB' and useraccess.menu not like '%create%' and profit_center != 'NAK' group by username");
  $menu = 0; $id = 0; $notif = ''; $notif1 = '';
  while ($rss = mysqli_fetch_array($qss)) {
      $menu = isset($rss['ket']) ? $rss['ket'] : 0;
      $id   = isset($rss['id'])  ? $rss['id']  : 0;
      $c  = (mysqli_fetch_array(mysqli_query($conn2,"select count(distinct(no_bpb)) as c from bpb_new where status = 'GMF' and profit_center = 'NAG'")))['c'] ?? 0;
      $c1 = (mysqli_fetch_array(mysqli_query($conn2,"select count(distinct(no_ro)) as c from bppb_new where status = 'GMF'")))['c'] ?? 0;
      $notif  = $c  != '0' ? '<span class="badge badge-danger">'.$c.'</span>'  : '';
      $notif1 = $c1 != '0' ? '<span class="badge badge-danger">'.$c1.'</span>' : '';
  }
  $qss2 = mysqli_query($conn2,"select 'Y' as ket from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%update%'");
  $menu2 = ($r2 = mysqli_fetch_array($qss2)) ? ($r2['ket'] ?? 0) : 0;

  if ($menu == 'Y' || $menu2 == 'Y') {
      echo _so('far fa-envelope', 'BPB Garment');
      if     ($id=='1')      echo _ni($AP.'formapprovebpb.php',  'Approve BPB',          $notif);
      elseif ($id=='2')      echo _ni($AP.'verifikasibpb.php',   'Verifikasi BPB');
      elseif ($id=='19')     echo _ni($AP.'formapprovebppb.php', 'Approve BPB Return',   $notif1);
      elseif ($id=='20')     echo _ni($AP.'verifikasibppb.php',  'Verifikasi BPB Return');
      else {
          if (strpos($id,'1') !==false) echo _ni($AP.'formapprovebpb.php',  'Approve BPB',          $notif);
          if (strpos($id,'2') !==false) echo _ni($AP.'verifikasibpb.php',   'Verifikasi BPB');
          if (strpos($id,'19')!==false) echo _ni($AP.'formapprovebppb.php', 'Approve BPB Return',   $notif1);
          if (strpos($id,'20')!==false) echo _ni($AP.'verifikasibppb.php',  'Verifikasi BPB Return');
      }
      echo _sc();
  }

  /* Transfer Memo */
  $q = mysqli_query($conn2,"select 'Y' as ket from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and (useraccess.menu = 'Approve Transfer Memo' or useraccess.menu = 'Transfer Memo')");
  if (($r = mysqli_fetch_array($q)) && $r['ket'] == 'Y') {
      echo _so('fas fa-paper-plane', 'Transfer Memo');
      echo _ni($AP.'approve_transfer_memo.php', 'Approve Transfer Memo');
      echo _ni($AP.'transfer_memo.php',         'Transfer Memo');
      echo _sc();
  }

  /* Kontra Bon */
  $q = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Kontrabon'");
  $r = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  if ($id == '6') {
      echo _so('fas fa-coins', 'Kontra Bon');
      echo _ni($AP.'kontrabon.php',       'Kontra Bon Reg');
      echo _ni($AP.'kontrabonftrcbd.php', 'Kontra Bon FTR CBD');
      echo _ni($AP.'kontrabonftrdp.php',  'Kontra Bon FTR DP');
      echo _sc();
  }

  /* List Payment */
  $q = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'List Payment'");
  $r = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  if ($id == '8') {
      echo _so('fas fa-dollar-sign', 'List Payment');
      echo _ni($AP.'payment.php',        'List Payment Reg');
      echo _ni($AP.'listpaymentcbd.php', 'List Payment CBD');
      echo _ni($AP.'listpaymentdp.php',  'List Payment DP');
      echo _sc();
  }

  /* Payment */
  $q = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Payment'");
  $r = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  if ($id == '10') {
      echo _so('fas fa-dollar-sign', 'Payment');
      echo _ni($AP.'pelunasanftr.php',    'Payment Reg');
      echo _ni($AP.'pelunasanftrcbd.php', 'Payment CBD');
      echo _ni($AP.'pelunasanftrdp.php',  'Payment DP');
      echo _sc();
  }

  /* Closing Payment */
  $q  = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Closing%' and useraccess.menu != 'Closing Periode'");
  $r  = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  $n123 = (($v = (mysqli_fetch_array(mysqli_query($conn2,"select count(distinct(no_payment)) as c from list_payment where status = 'Approved'")))['c'] ?? 0) + (($v2 = (mysqli_fetch_array(mysqli_query($conn2,"select count(distinct(no_pay)) as c from saldo_awal where status = 'Approved' and no_pay not like '%LP/NAG%'")))['c'] ?? 0))) != '0' ? '<span class="badge badge-danger">'.($v+$v2).'</span>' : '';
  $n456 = (($v = (mysqli_fetch_array(mysqli_query($conn2,"select count(distinct(no_payment)) as c from list_payment_cbd where status = 'Approved'")))['c'] ?? 0)) != '0' ? '<span class="badge badge-danger">'.$v.'</span>' : '';
  $n789 = (($v = (mysqli_fetch_array(mysqli_query($conn2,"select count(distinct(no_payment)) as c from list_payment_dp where status = 'Approved'")))['c'] ?? 0)) != '0' ? '<span class="badge badge-danger">'.$v.'</span>' : '';
  if ($id == '22') {
      echo _so('fas fa-credit-card', 'Closing Payment');
      echo _ni($AP.'form-closing-payreg.php', 'Close Payment Reg', $n123);
      echo _ni($AP.'formclosing-paycbd.php',  'Close Payment CBD', $n456);
      echo _ni($AP.'formclosing-paydp.php',   'Close Payment DP',  $n789);
      echo _ni($AP.'status_closing.php',       'Closing Info');
      echo _sc();
  }

  /* Status */
  $q = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Status'");
  $r = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  if ($id == '30') echo _ni($AP.'status.php', 'Status', '', 'fas fa-info-circle');

  /* Report */
  $q  = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Report'");
  $r  = mysqli_fetch_array($q); $id  = isset($r['id'])  ? $r['id']  : 0;
  $q2 = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Development'");
  $r2 = mysqli_fetch_array($q2); $id2 = isset($r2['id']) ? $r2['id'] : 0;
  $q3 = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Rekap Pelunasan%'");
  $r3 = mysqli_fetch_array($q3); $id3 = isset($r3['id']) ? $r3['id'] : 0;
  $qpr = mysqli_query($conn2,"select 'Y' as ket from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Purchase Report'");
  $menu_pr = ($rpr = mysqli_fetch_array($qpr)) ? ($rpr['ket'] ?? 0) : 0;
  if ($id == '18' || $id2 == '35' || $id3 == '57' || $menu_pr == 'Y') {
      echo _so('fas fa-chart-line', 'Report');
      if ($id == '18' || $id2 == '35') { echo _ni($AP.'pcs_detail.php', 'AP Report'); echo _ni($AP.'payable_card_statement.php', 'AP Report New'); }
      if ($id3 == '57' || $id2 == '35') echo _ni($AP.'rekap-pelunasan.php', 'Rekap Pelunasan');
      if ($menu_pr == 'Y') { echo _ni($AP.'laporan_pembelian.php', 'Purchase Report'); echo _ni($AP.'laporan_retur_pembelian.php', 'Purchase Return Report'); }
      echo _sc();
  }

  /* Approval AP */
  $q = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Approval%'");
  $r = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  $nkb  = (($v=(mysqli_fetch_array(mysqli_query($conn2,"select count(distinct(no_kbon)) as c from kontrabon_h where status = 'draft'")))['c']??0)) != '0' ? '<span class="badge badge-danger">'.$v.'</span>' : '';
  $nlp  = (($vl=(mysqli_fetch_array(mysqli_query($conn2,"select count(distinct(no_payment)) as c from list_payment where status = 'draft'")))['c']??0) + ($vsa=(mysqli_fetch_array(mysqli_query($conn2,"select count(distinct(no_pay)) as c from saldo_awal where status = 'draft' and no_pay not like '%LP/NAG%'")))['c']??0)) != '0' ? '<span class="badge badge-danger">'.($vl+$vsa).'</span>' : '';
  $npay = (($vp=(mysqli_fetch_array(mysqli_query($conn2,"select COUNT(id) jml from (select a.id from payment_ftr a INNER JOIN master_pc b on b.kode_pc = a.profit_center where a.status = 'draft' GROUP BY payment_ftr_id) a")))['jml']??0)) != '0' ? '<span class="badge badge-danger">'.$vp.'</span>' : '';
  if (strpos($id,'31')!==false || strpos($id,'33')!==false || strpos($id,'91')!==false) {
      echo _so('far fa-thumbs-up', 'Approval');
      if (strpos($id,'31')!==false) echo _ni($AP.'formapprovekb.php',        'Kontrabon Reg',    $nkb);
      if (strpos($id,'33')!==false) echo _ni($AP.'formapprovelp.php',        'List Payment Reg', $nlp);
      if (strpos($id,'91')!==false) echo _ni($AP.'form_approve_payment.php', 'Payment Reg',      $npay);
      echo _sc();
  }

  /* Request Debit Note */
  $q = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Request Debitnote'");
  $r = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  if ($id == '78') echo _ni($AP.'request_debitnote.php', 'Request Debit Note', '', 'fas fa-file-invoice');
  ?>

  <!-- ══ BANK ════════════════════════════════════════════ -->
  <li class="nav-header">BANK</li>
  <?php
  $q  = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Bank'");
  $r  = mysqli_fetch_array($q); $id  = isset($r['id'])  ? $r['id']  : 0;
  $q2 = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'E - Statement'");
  $r2 = mysqli_fetch_array($q2); $id2 = isset($r2['id']) ? $r2['id'] : 0;
  echo _so('fas fa-university', 'Bank');
  if ($id == '36') {
      echo _ni($AP.'bank-in.php',         'Bank In');
      echo _ni($AP.'bank-out.php',        'Bank Out');
      echo _ni($AP.'payment-voucher.php', 'Payment Voucher');
      echo _ni($AP.'bankreport.php',      'Report');
  }
  if ($id2 == '62') echo _ni($AP.'e_statement.php', 'E-Statement');
  $q  = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Bank%' and useraccess.menu like '%Approval%' and useraccess.menu not like '%reverse%' group by username");
  $r  = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  $bItems = ['41'=>[$AP.'approve-pv.php','Payment Voucher'],'42'=>[$AP.'approve-inbank.php','Incoming Bank'],'43'=>[$AP.'approve_bank_out.php','Outgoing Bank']];
  $hasBa  = false; foreach ($bItems as $k=>$v) { if (strpos($id,$k)!==false) { $hasBa=true; break; } }
  if ($hasBa) { echo _so('fas fa-thumbs-up','Approval'); foreach ($bItems as $k=>$v) { if (strpos($id,$k)!==false) echo _ni($v[0],$v[1]); } echo _sc(); }
  echo _sc();
  ?>

  <!-- ══ CASH ═════════════════════════════════════════════ -->
  <li class="nav-header">CASH</li>
  <?php
  $q = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Cash'");
  $r = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  echo _so('fas fa-wallet', 'Cash');
  if ($id == '38') {
      echo _ni($AP.'cash-in.php',      'Cash In');
      echo _ni($AP.'cash-out.php',     'Cash Out');
      echo _ni($AP.'petty-cashin.php', 'Petty Cash In');
      echo _ni($AP.'petty-cashout.php','Petty Cash Out');
      echo _ni($AP.'cashreport.php',   'Report');
  }
  $q = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Cash%' and useraccess.menu like '%Approval%' and useraccess.menu not like '%reverse%' group by username");
  $r = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  $cItems = ['44'=>[$AP.'approve-cashin.php','Cash In'],'45'=>[$AP.'approve-cashout.php','Cash Out'],'46'=>[$AP.'approve-petty-cashin.php','Petty Cash In'],'47'=>[$AP.'approve-petty-cashout.php','Petty Cash Out']];
  $hasCa  = false; foreach ($cItems as $k=>$v) { if (strpos($id,$k)!==false) { $hasCa=true; break; } }
  if ($hasCa) { echo _so('fas fa-thumbs-up','Approval'); foreach ($cItems as $k=>$v) { if (strpos($id,$k)!==false) echo _ni($v[0],$v[1]); } echo _sc(); }
  echo _sc();
  ?>

  <!-- ══ ACCOUNTING ══════════════════════════════════════ -->
  <li class="nav-header">ACCOUNTING</li>
  <?php
  $qss  = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Acct%' and menurole.status = 'Menu' group by username");
  $menu = 0; $id = 0;
  while ($rss = mysqli_fetch_array($qss)) { $menu = $rss['ket'] ?? 0; $id = $rss['id'] ?? 0; }
  $qss2 = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Closing Periode' and menurole.status = 'Menu' group by username");
  $id2 = 0;
  while ($rss2 = mysqli_fetch_array($qss2)) { $id2 = $rss2['id'] ?? 0; }
  echo _so('fas fa-chart-bar','Accounting');
  if ($menu == 'Y') {
      if (strpos($id,'50')!==false) echo _ni($AP.'memorial-journal.php','Memorial Journal');
      if (strpos($id,'51')!==false) echo _ni($AP.'list-journal.php',    'List Journal');
      if (strpos($id,'52')!==false) { echo _ni($AP.'general-ledger.php','General Ledger'); if (in_array($user,['indro','willy','steven'])) echo _ni($AP.'general_ledger.php','General Ledger New'); }
      if (strpos($id,'104')!==false) echo _ni($AP.'trial_balance.php',  'Trial Balance');
      $hasSL = strpos($id,'64')!==false||strpos($id,'65')!==false||strpos($id,'82')!==false||strpos($id,'105')!==false;
      if ($hasSL) {
          echo _so('fas fa-list-ul','Sub Ledger');
          if (strpos($id,'64') !==false) echo _ni($AP.'other_receivable_report.php', 'Other Receivable');
          if (strpos($id,'65') !==false) echo _ni($AP.'other_payable_report.php',    'Other Payable');
          if (strpos($id,'82') !==false) echo _ni($AP.'purchase_advance_report.php', 'Purchase Advance');
          if (strpos($id,'105')!==false) echo _ni($AP.'prepaid_tax_report.php',      'Prepaid Tax');
          echo _sc();
      }
      if (strpos($id,'53')!==false) { echo _so('fas fa-balance-scale','Financial Statement'); echo _ni($AP.'financial-statement-ytd.php','Year To Date'); echo _ni($AP.'trial-balance-monthly.php','Monthly'); echo _sc(); }
      if (in_array($user,['indro','willy','steven'])) { echo _so('fas fa-balance-scale','Financial Statement 2'); echo _ni($AP.'financial_statement_ytd.php','Year To Date'); echo _sc(); }
  }
  if ($id2 && strpos($id2,'88')!==false) echo _ni($AP.'closing-periode.php','Closing Periode');

  /* Repost Journal */
  $hasRepost = strpos($id,'90')!==false||strpos($id,'106')!==false||strpos($id,'107')!==false||strpos($id,'108')!==false||strpos($id,'109')!==false;
  if ($hasRepost) {
      echo _so('fas fa-redo','Repost Journal');
      if (strpos($id,'90') !==false) echo _ni($AP.'repost-bank-out.php',  'Bank Out');
      if (strpos($id,'106')!==false) echo _ni($AP.'repost-bank-in.php',   'Bank In');
      if (strpos($id,'107')!==false) echo _ni($AP.'repost-payment.php',   'Payment');
      if (strpos($id,'108')!==false) echo _ni($AP.'repost-kontrabon.php', 'Kontrabon');
      if (strpos($id,'109')!==false) echo _ni($AP.'edit-journal.php',     'Edit Jurnal');
      echo _sc();
  }
  echo _sc(); /* end Accounting */

  /* Cost Accounting */
  $qss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Cost Accounting%' and menurole.status = 'Menu' group by username");
  $caMenu = 0; $caId = 0;
  while ($rca = mysqli_fetch_array($qss)) { $caMenu = $rca['ket'] ?? 0; $caId = $rca['id'] ?? 0; }
  if (strpos($caId,'85')!==false) {
      echo _so('fas fa-industry','Cost Accounting');
      echo _so('fas fa-tasks','Fabric');
      echo _ni($AP.'ca_fabric_trx_in_new.php',      'Trx Item In');
      echo _ni($AP.'ca_fabric_trx_in_barcode.php',  'Trx Barcode In');
      echo _ni($AP.'ca_fabric_trx_out_item.php',    'Trx Item Out');
      echo _ni($AP.'ca_fabric_trx_out_barcode.php', 'Trx Barcode Out');
      echo _ni($AP.'ca_fabric_summary_item.php',    'Summary Item');
      echo _ni($AP.'ca_fabric_summary_barcode.php', 'Summary Barcode');
      echo _ni($AP.'ca_fabric_summary_sc.php',      'Summary Subcont');
      echo _ni($AP.'ca_fabric_summary_subcont.php', 'Summary Subcont New');
      echo _ni($AP.'update_bpb_fabric.php',         'Update Trx In');
      echo _ni($AP.'adjust-subcont.php',            'Update Subcontractor');
      echo _sc();
      echo _sc();
  }

  /* Exim */
  $qss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Exim%' and menurole.status = 'Menu' group by username");
  $exMenu = 0; $exId = 0;
  while ($rex = mysqli_fetch_array($qss)) { $exMenu = $rex['ket'] ?? 0; $exId = $rex['id'] ?? 0; }
  if ($exMenu == 'Y' && strpos($exId,'83')!==false) {
      echo _so('fas fa-cubes','Exim');
      echo _ni($AP.'exim-calculatin-cost-report.php','Calculation Cost Report');
      echo _sc();
  }
  ?>

  <!-- ══ TOOLS ════════════════════════════════════════════ -->
  <li class="nav-header">TOOLS</li>
  <?php
  $q  = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Reverse'");
  $r  = mysqli_fetch_array($q); $id = isset($r['id']) ? $r['id'] : 0;
  $qm = mysqli_query($conn2,"select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Maintain BPB'");
  $rm = mysqli_fetch_array($qm); $mtn = isset($rm['id']) ? $rm['id'] : 0;
  $qi = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Reverse%' group by username");
  $ri = mysqli_fetch_array($qi); $id_rvs = isset($ri['id']) ? $ri['id'] : 0;
  $nrk = (($v=(mysqli_fetch_array(mysqli_query($conn2,"select COUNT(id) c from (select id from ap_reverse_h where rvs_number like '%SI/%' and status='DRAFT') a")))['c']??0))!='0'?'<span class="badge badge-danger">'.$v.'</span>':'';
  $nrp = (($v=(mysqli_fetch_array(mysqli_query($conn2,"select COUNT(id) c from (select id from ap_reverse_h where rvs_number like '%FT/%' and status='DRAFT') a")))['c']??0))!='0'?'<span class="badge badge-danger">'.$v.'</span>':'';
  $nrb = (($v=(mysqli_fetch_array(mysqli_query($conn2,"select COUNT(id) c from (select id from ap_reverse_h where rvs_number like '%BN/%' and status='DRAFT') a")))['c']??0))!='0'?'<span class="badge badge-danger">'.$v.'</span>':'';
  $nrpc= (($v=(mysqli_fetch_array(mysqli_query($conn2,"select COUNT(id) c from (select id from ap_reverse_h where rvs_number like '%PC/%' and status='DRAFT') a")))['c']??0))!='0'?'<span class="badge badge-danger">'.$v.'</span>':'';

  if ($mtn || $id == '34' || $id_rvs) {
      echo _so('fas fa-retweet','Reverse');
      if ($mtn || $id == '34') {
          echo _so('fas fa-envelope','BPB');
          if (strpos($mtn,'89')!==false) echo _ni($AP.'maintain-bpb.php',   'BPB');
          if ($id == '34')               echo _ni($AP.'formreversebpb.php', 'Verifikasi BPB');
          echo _sc();
      }
      if (strpos($id_rvs,'94')!==false||strpos($id_rvs,'92')!==false||strpos($id_rvs,'98')!==false||strpos($id_rvs,'101')!==false) {
          echo _so('fas fa-history','Reverse');
          if (strpos($id_rvs,'94') !==false) echo _ni($AP.'reverse_kontrabon.php', 'Kontrabon');
          if (strpos($id_rvs,'92') !==false) echo _ni($AP.'reverse_payment.php',   'Payment');
          if (strpos($id_rvs,'98') !==false) echo _ni($AP.'reverse_bank.php',      'Bank');
          if (strpos($id_rvs,'101')!==false) echo _ni($AP.'reverse_petty_cash.php','Petty Cash');
          echo _sc();
      }
      if (strpos($id_rvs,'95')!==false||strpos($id_rvs,'93')!==false||strpos($id_rvs,'100')!==false||strpos($id_rvs,'103')!==false) {
          echo _so('far fa-thumbs-up','Approval');
          if (strpos($id_rvs,'95') !==false) echo _ni($AP.'form_approve_reverse_kontrabon.php', 'Kontrabon',  $nrk);
          if (strpos($id_rvs,'93') !==false) echo _ni($AP.'form_approve_reverse_payment.php',  'Payment',    $nrp);
          if (strpos($id_rvs,'100')!==false) echo _ni($AP.'form_approve_reverse_bank.php',     'Bank',       $nrb);
          if (strpos($id_rvs,'103')!==false) echo _ni($AP.'form_approve_reverse_petty_cash.php','Petty Cash',$nrpc);
          echo _sc();
      }
      echo _sc();
  }

  /* Setting */
  $q = mysqli_query($conn1,"select Groupp from userpassword where username = '$user'");
  $r = mysqli_fetch_array($q); $group = isset($r['Groupp']) ? $r['Groupp'] : null;
  if ($group != 'STAFF' && $group != null) {
      echo _so('fas fa-cogs','Setting');
      echo _ni($AP.'userrole.php','Userrole');
      echo _sc();
  }
  ?>

</ul>
