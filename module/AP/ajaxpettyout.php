<?php
include '../../conn/conn.php';
$sub = 0;
$tax = 0;
$total = 0; 
$amount = 0;
$rate = 0;
$eqv_idr = 0;
$no_ob = isset($_POST['no_ob']) ? $_POST['no_ob']: null;
$refdoc = isset($_POST['refdoc']) ? $_POST['refdoc']: null;

/* Payment Voucher, List Payment, dan FTR (CBD / DP) sama-sama menyimpan
   barisnya di c_petty_cashout_det + c_petty_cashout_adj_det, jadi dibaca
   dgn query yang sama. Sebelumnya FTR tidak punya cabang sama sekali
   sehingga $table tak pernah terbentuk dan totalnya selalu 0. */
$grupDok = ($refdoc == 'List Payment' || $refdoc == 'Payment Voucher' || $refdoc == 'FTR (CBD / DP)');

/* Label kolom pertama menyesuaikan jenis dokumennya. */
$labelDok = ($refdoc == 'FTR (CBD / DP)') ? 'FTR' : (($refdoc == 'List Payment') ? 'LP' : 'PV');
if ($grupDok) {
    $sql = mysqli_query($conn1,"select no_reff, IF(reff_date = '1970-01-01', '-', DATE_FORMAT(reff_date, '%d-%m-%Y')) as reff_date, IF(due_date = '1970-01-01', '-', DATE_FORMAT(due_date, '%d-%m-%Y')) as due_date,dpp,ppn,pph,total,amount from c_petty_cashout_det where no_pco = '$no_ob'");
    $sql2 = mysqli_query($conn1,"select CONCAT(b.no_coa,' ',b.nama_coa) as coa, IF(a.reff_doc = '', '-', a.reff_doc) as reff_doc,IF(a.reff_date = '1970-01-01', '-', DATE_FORMAT(a.reff_date, '%d-%m-%Y')) as reff_date, a.deskripsi, a.t_debit, a.t_credit from c_petty_cashout_adj_det a left join mastercoa_v2 b on b.no_coa = a.id_coa where no_pco = '$no_ob'"); 
    $sql3 = mysqli_query($conn1,"select amount from c_petty_cashout_h where no_pco = '$no_ob'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];

}elseif ($refdoc == 'None' || $refdoc == 'Settlement' || $refdoc == 'Advance') {
    $sql = mysqli_query($conn1,"select DISTINCT if(a.no_coa = '-', '-',CONCAT(b.no_coa,' ',b.nama_coa)) as coa,if(a.no_costcntr = '-','-',c.cc_name) as cost_center,if(a.buyer = '','-',a.buyer) as buyer ,if(a.no_ws = '','-',a.no_ws) as no_ws,a.curr,a.debit,a.credit,a.deskripsi from c_petty_cashout_none a left join mastercoa_v2 b on b.no_coa = a.no_coa left join b_master_cc c on c.no_cc = a.no_costcntr where a.no_pco = '$no_ob'");
    $sql3 = mysqli_query($conn1,"select amount from c_petty_cashout_h where no_pco = '$no_ob'");
    $row3 = mysqli_fetch_assoc($sql3);
    $amount = $row3['amount'];
    
}else{
}
  

if($refdoc == 'None' || $refdoc == 'Settlement' || $refdoc == 'Advance'){
    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:25%;">Coa</th>
                            <th style="width:15%;">Cost Center</th>
                            <th style="width:15%;">Buyer</th>                                                                                
                            <th style="width:15%;">WS</th>
                            <th style="width:8%;">Curr</th>
                            <th style="width:11%;">Debit</th> 
                            <th style="width:11%;">Credit</th>                                                                                    
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="" value="'.$row['coa'].'">'.$row['coa'].'</td>
                            <td style="" value="'.$row['cost_center'].'">'.$row['cost_center'].'</td>
                            <td style="" value="'.$row['buyer'].'">'.$row['buyer'].'</td>
                            <td style="" value="'.$row['no_ws'].'">'.$row['no_ws'].'</td>
                            <td style="" value="'.$row['curr'].'">'.$row['curr'].'</td>                                                                       
                            <td style="text-align: right;" value="'.$row['debit'].'">'.number_format($row['debit'],2).'</td>
                            <td style="text-align: right;" value="'.$row['credit'].'">'.number_format($row['credit'],2).'</td> 
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

}elseif($grupDok){

    $table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:10px;">#' . $labelDok . '</th>
                             <th style="width:100px;">No ' . $labelDok . '</th>
                            <th style="width:100px;">' . $labelDok . ' Date</th>
                            <th style="width:50px;">Duedate</th>                                                                                
                            <th style="width:50px;">DPP</th>
                            <th style="width:50px;">PPN</th>
                            <th style="width:50px;">PPH</th> 
                            <th style="width:50px;">Total</th>
                            <th style="width:50px;">Amount</th>                                                                                    
                        </tr>
                        <tr>                       
                            <th style="width:10px;">#Adj</th>
                             <th style="width:100px;">COA</th>
                            <th style="width:100px;">Reff Doc</th>
                            <th style="width:50px;">Reff Date</th>                                                                                
                            <th colspan = "3"; style="width:50px;">Description</th>
                            <th style="width:50px;">Credit</th>                                                                                    
                            <th style="width:50px;">Debit</th> 
                        </tr>
                    </thead>';

            $table .= '<tbody>';
            while ($row = mysqli_fetch_assoc($sql)) {
            $table .= '<tr>                       
                            <td style="width:10px;">#' . $labelDok . '</td>
                            <td style="width:100px;" value="'.$row['no_reff'].'">'.$row['no_reff'].'</td>
                            <td style="width:100px;" value="'.$row['reff_date'].'">'.$row['reff_date'].'</td>
                            <td style="width:100px;" value="'.$row['due_date'].'">'.$row['due_date'].'</td>                                                                       
                            <td style="width:50px;text-align: right;" value="'.$row['dpp'].'">'.number_format($row['dpp'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['ppn'].'">'.number_format($row['ppn'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['pph'].'">'.number_format($row['pph'],2).'</td>
                            <td style="width:50px;text-align: right;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td> 
                            <td style="width:50px;text-align: right;" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td> 
                       </tr>
                       ';
                   }

            while ($row2 = mysqli_fetch_assoc($sql2)) {
            $table .= '<tr>                       
                            <td style="width:10px;">#Adj</td>
                            <td style="width:100px;" value="'.$row2['coa'].'">'.$row2['coa'].'</td>
                            <td style="width:100px;" value="'.$row2['reff_doc'].'">'.$row2['reff_doc'].'</td>                                                                       
                            <td style="width:100px;" value="'.$row2['reff_date'].'">'.$row2['reff_date'].'</td>
                            <td colspan = "3"; style="width:100px;" value="'.$row2['deskripsi'].'">'.$row2['deskripsi'].'</td>
                            <td style="width:50px;text-align: right;" value="'.$row2['t_credit'].'">'.number_format($row2['t_credit'],2).'</td> 
                            <td style="width:50px;text-align: right;" value="'.$row2['t_debit'].'">'.number_format($row2['t_debit'],2).'</td>
                       </tr>
                       ';
            $table .= '</tbody>';
        }
            $table .= '</table>';


}else{

}

echo $table;

if ($grupDok) {
 echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Total Amount
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($amount,2).'
        </td>       
    </tr>   
</table>';

}else{
echo '<table width="100%" border="0" style="font-size:12px">

    <tr>
        <td width="70%">
            
        </td>
            
        <td style="font-weight:bold;">
            Total Amount
        </td>
        <td style="width:1%">:</td>
        <td style="text-align:right;font-weight:bold;">
            '.number_format($amount,2).'
        </td>       
    </tr>   
</table>';
}

// echo '<div id="txt_sub" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Subtotal: '.number_format($sub,2).'</h7></div>';
// echo '<div id="txt_tax" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h7>Tax: '.number_format($tax,2).'</h7></div>';
// echo '<div id="txt_total" class="modal-body col-6" style="padding: 0.5rem; margin-left: 65%;"><h6>Total: '.number_format($total,2).'</h6></div>';
/* Penutup ?> DIHAPUS: berkas ini murni PHP dan keluarannya dipakai sbg isi
   modal, jadi spasi/baris kosong sesudah penutup akan ikut terkirim. Blok
   riwayat di bawah dulu mendarat SESUDAH penutup ini sehingga komentarnya
   ikut tercetak ke layar. */


/* ============================================================================
   RIWAYAT DOKUMEN

   Dibuat / di-approve / di-cancel diambil dari c_petty_cashout_h
   (create_by, approve_by, cancel_by beserta tanggalnya).

   DIEDIT diambil dari JURNAL, bukan dari kolom di tabel header - tabel itu
   memang tidak punya kolom edit_by/edit_date. Setiap kali dokumen disimpan
   ulang, endpoint update_* menulis jurnal baru bernama "<reff> (Rev N)"
   lengkap dgn create_by & create_date-nya, jadi di situlah jejak suntingannya
   tersimpan. Baris "Reverse ..." dikecualikan: itu jurnal pembalik yang dibuat
   proses yang sama, bukan suntingan tersendiri.
   ============================================================================ */
$noEsc = mysqli_real_escape_string($conn1, $no_ob);
$qH = mysqli_query($conn1, "select * from c_petty_cashout_h where no_pco = '$noEsc' limit 1");
$H  = $qH ? mysqli_fetch_assoc($qH) : null;

if ($H) {
    $jejak = array(
        array('Created', 'fa-plus-circle', 'is-create', $H['create_by'] ?? '', $H['create_date'] ?? ''),
    );

    /* Satu baris riwayat per revisi. Dikelompokkan supaya beberapa baris jurnal
       dari satu penyimpanan tidak tampil berulang-ulang. */
    $qRev = mysqli_query($conn1, "select type_journal, create_by, MIN(create_date) tgl
        from tbl_list_journal
        where no_journal = '$noEsc'
          and type_journal like '%(Rev %'
          and type_journal not like 'Reverse %'
        group by type_journal, create_by
        order by tgl asc");
    if ($qRev) {
        while ($r = mysqli_fetch_assoc($qRev)) {
            $label = 'Edited';
            if (preg_match('/\(Rev\s*(\d+)\)/i', $r['type_journal'], $m)) { $label = 'Edited (Rev ' . $m[1] . ')'; }
            $jejak[] = array($label, 'fa-pencil', 'is-edit', $r['create_by'], $r['tgl']);
        }
    }

    /* Langkah yang BELUM terjadi tidak lagi ditampilkan begitu saja.

       Versi pertama selalu memasang Approved dan Cancelled, yang belum terjadi
       diredupkan. Hasilnya menyesatkan: dokumen yang SUDAH di-approve tetap
       menampilkan "Cancelled" diredupkan, seolah-olah masih ada langkah
       membatalkan yang menunggu - padahal dokumen yang sudah disetujui memang
       tidak akan dibatalkan lewat alur ini.

       Sekarang: hanya yang benar-benar terjadi yang muncul. Satu-satunya
       langkah tertunda yang ditampilkan adalah yang memang sedang ditunggu,
       yaitu persetujuan ketika dokumennya masih Draft. */
    $punyaIsi = function ($oleh, $kapan) {
        return (trim((string) $oleh) !== '')
            || (trim((string) $kapan) !== '' && substr((string) $kapan, 0, 4) !== '0000');
    };

    $adaApprove = $punyaIsi($H['approve_by'] ?? '', $H['approve_date'] ?? '');
    $adaCancel  = $punyaIsi($H['cancel_by'] ?? '',  $H['cancel_date'] ?? '');
    $status     = trim((string) ($H['status'] ?? ''));

    if ($adaApprove) {
        $jejak[] = array('Approved', 'fa-check-circle', 'is-approve', $H['approve_by'], $H['approve_date']);
    }
    if ($adaCancel) {
        $jejak[] = array('Cancelled', 'fa-times-circle', 'is-cancel', $H['cancel_by'], $H['cancel_date']);
    }
    if (!$adaApprove && !$adaCancel && strcasecmp($status, 'Draft') === 0) {
        $jejak[] = array('Pending approval', 'fa-clock-o', 'is-nanti', '', '');
    }

    echo '<div class="ftl-hist">';
    echo '<div class="ftl-hist-title"><i class="fa fa-history" aria-hidden="true"></i> History</div>';
    echo '<ul class="ftl-hist-list">';

    foreach ($jejak as $j) {
        list($judul, $ikon, $kelas, $oleh, $kapan) = $j;
        $adaIsi = $punyaIsi($oleh, $kapan);
        echo '<li class="' . $kelas . ($adaIsi ? '' : ' is-belum') . '">'
           . '<span class="ftl-hist-ic"><i class="fa ' . $ikon . '" aria-hidden="true"></i></span>'
           . '<span class="ftl-hist-txt">'
           . '<b>' . htmlspecialchars($judul, ENT_QUOTES) . '</b>'
           . '<span>' . ($adaIsi
                ? htmlspecialchars(trim((string) $oleh) !== '' ? $oleh : '-', ENT_QUOTES)
                  . ' &middot; ' . (trim((string) $kapan) !== '' && substr((string) $kapan, 0, 4) !== '0000'
                        ? date('d-M-Y H:i', strtotime($kapan)) : '-')
                : 'waiting')
           . '</span></span></li>';
    }

    echo '</ul></div>';
}
