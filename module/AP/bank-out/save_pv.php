<?php
include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

mysqli_begin_transaction($conn2);

try {

    $json = $_POST['data'] ?? '';
    $data = json_decode($json, true);

    if(!$data){
        throw new Exception("Data tidak valid");
    }

    $header        = $data['header'];
    $detail_pv     = $data['detail_pv'];
    $detail_adjust = $data['detail_adjust'];

    // =========================
    // HEADER VALUE
    // =========================
    $doc_date        = date('Y-m-d', strtotime($header['tgl']));
    $supp       = mysqli_real_escape_string($conn2, $header['supp']);
    $pc_bank    = $header['pc_header'];
    $akun    = $header['account'];
    $bank       = $header['bank'];
    $curr   = $header['currency'];
    $amount     = $header['amount'];
    $rate       = $header['rate'];
    $eqv        = $header['eqv'];
    $desc       = mysqli_real_escape_string($conn2, $header['desc']);

    // =========================
    // FORMAT PREFIX
    // =========================
    $kode_bank = $header['kode_bank'];
    $user = $_SESSION['username'] ?? 'system';

    $bulan = date('m',strtotime($doc_date));
    $tahun = date('y',strtotime($doc_date));
    $status = "Draft";
    $create_date = date("Y-m-d H:i:s");

    $prefix = "BK/".$kode_bank."/".$pc_bank."/".$bulan.$tahun;

    // =========================
    // AMBIL NOMOR (LOCK)
    // =========================
    $sql = mysqli_query($conn2,"
        SELECT MAX(CAST(RIGHT(no_bankout,5) AS UNSIGNED)) AS max_urut
        FROM b_bankout_h
        WHERE no_bankout LIKE '$prefix%'
        FOR UPDATE
        ");

    $row = mysqli_fetch_assoc($sql);
    $urutan = ($row['max_urut'] ?? 0) + 1;

    $doc_num = $prefix."/".sprintf("%05d",$urutan);

    // DEBUG
    error_log("DOC NUM: ".$doc_num);

    $sqlcoa1 = mysqli_query($conn2,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
    $rowcoa1 = mysqli_fetch_array($sqlcoa1);
    $no_coa1 = $rowcoa1['no_coa'];
    $nama_coa1 = $rowcoa1['nama_coa'];

    // =========================
    // INSERT HEADER
    // =========================
    mysqli_query($conn2,"
        INSERT INTO b_bankout_h
        (
        no_bankout, bankout_date, reff_doc, nama_supp, akun, bank, curr, amount, outstanding, rate, eqv_idr, deskripsi, status, create_by, create_date, stat_bi
        )
        VALUES
        ('$doc_num', '$doc_date', 'Payment Voucher', '$supp', '$akun', '$bank', '$curr', '$amount', '$amount', '$rate', '$eqv', '$desc', '$status', '$user', '$create_date', 'N')
        ");


    mysqli_query($conn2,"
        INSERT INTO b_reportbank
        (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status)
        VALUES
        ('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr', '0', '$amount', '$amount', '$status')
        ");

    mysqli_query($conn2,"
        INSERT INTO tbl_list_journal
        (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
        VALUES
        ('$doc_num', '$doc_date', 'Payment Voucher', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_bank')
        ");

    // =========================
    // INSERT DETAIL PV
    // =========================
    foreach($detail_pv as $pv){

        $no_pv  = $pv['no_pv'];
        $amount = $pv['amount'];
        $pc     = $pv['pc'];

        $sql_pv = mysqli_query($conn2,"WITH
            total_pv as (select b.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, a.nama_supp,a.no_pv,a.pv_date,max(b.due_date) as due_date,a.curr, SUM(b.amount - ded_add) subtotal, SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) ppn, SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100)) pph, (SUM(b.amount - ded_add) + SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) - SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100))) total, a.status, a.frm_akun, if(a.frm_akun = '-','-',c.bank_name) as bank_name, c.b_code from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv left join b_masterbank c on c.bank_account = a.frm_akun INNER JOIN master_pc d on d.kode_pc = b.profit_center where a.no_pv = '$no_pv' and d.kode_pc = '$pc' group by a.no_pv, b.profit_center),

            total_bk as (select a.profit_center, no_reff, sum(dpp) dpp_pv, sum(ppn) ppn_pv, sum(pph) pph_pv, sum(total) total_pv from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where b.status != 'Cancel' and no_reff like '%PV%' GROUP BY no_reff,a.profit_center)

            select a.profit_center, a.nama_pc, a.nama_supp, a.no_pv, a.pv_date,a.due_date, a.curr, (a.subtotal - COALESCE(b.dpp_pv,0)) subtotal, (a.ppn - COALESCE(b.ppn_pv,0)) ppn, (a.pph - COALESCE(b.pph_pv,0)) pph ,(a.total - COALESCE(b.total_pv,0)) total, a.status, a.frm_akun, a.bank_name, a.b_code, IFNULL(c.rate,1) rate from total_pv a LEFT JOIN total_bk b on b.no_reff = a.no_pv and b.profit_center = a.profit_center LEFT JOIN (SELECT tanggal, curr, rate FROM ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = a.pv_date");
        $row_pv = mysqli_fetch_array($sql_pv);
        $pv_pc = $row_pv['profit_center'];
        $pv_number = $row_pv['no_pv'];
        $pv_date = $row_pv['pv_date'];
        $pv_duedate = $row_pv['due_date'];
        $pv_curr = $row_pv['curr'];
        $pv_sub = $row_pv['subtotal'];
        $pv_ppn = $row_pv['ppn'];
        $pv_pph = $row_pv['pph'];
        $pv_total = $row_pv['total'];
        $pv_rate = $row_pv['rate'];

        mysqli_query($conn2,"
            INSERT INTO b_bankout_det (no_bankout,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr, rates, for_balance, profit_center) 
            VALUES
            ('$doc_num', '$pv_number', '$pv_date', '$pv_duedate', '$pv_sub', '$pv_ppn', '$pv_pph', '$pv_total', '$pv_curr', '$amount', '$pv_rate', '$amount', '$pv_pc')
            ");

        mysqli_query($conn2,"insert into tbl_list_journal select '' id, '$doc_num' no_journal, '$doc_date' tgl_journal, type_journal, coa, nama_coa, no_cc, COALESCE(cc_name,'-') cc_name, no_pv no_reff, pv_date reff_date, buyer, no_ws, a.curr, IF(rate is null,1,rate) rate, debit, credit, round(debit * IF(rate is null,1,rate),4) debit_idr, round(credit * IF(rate is null,1,rate),4) credit_idr, 'Draft' status, deskripsi, '$user' create_by, CURRENT_TIMESTAMP() create_date, '' approve_by, '' approve_date, '' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from 
            (select a.id, 'Payment Voucher' type_journal, d.no_coa coa, d.nama_coa, a.no_cc, b.cc_name,h.no_pv, h.pv_date,  '-' buyer, '-' no_ws, h.curr, amount debit, ded_add credit, a.deskripsi, a.profit_center from tbl_pv a INNER JOIN tbl_pv_h h on h.no_pv = a.no_pv left join b_master_cc b on b.no_cc = a.no_cc INNER JOIN mastercoa_v2 d on d.no_coa = a.coa where a.no_pv = '$pv_number'
            UNION
            select a.id, 'Payment Voucher' type_journal, d.no_coa, d.nama_coa, a.no_cc, b.cc_name,h.no_pv, h.pv_date,  '-' buyer, '-' no_ws, h.curr, (ded_add * a.pph/100) debit, (amount * a.pph/100) credit, a.deskripsi, a.profit_center from tbl_pv a INNER JOIN tbl_pv_h h on h.no_pv = a.no_pv left join b_master_cc b on b.no_cc = a.no_cc INNER JOIN mtax d on d.idtax = a.id_pph where a.no_pv = '$pv_number'
            UNION
            select a.id, 'Payment Voucher' type_journal, d.no_coa, d.nama_coa, a.no_cc, b.cc_name,no_pv, pv_date,  '-' buyer, '-' no_ws, curr, (amount * ppn/100) debit, (ded_add * a.ppn/100) credit, deskripsi, a.profit_center from (select id,no_pv, pv_date, a.profit_center, coa, no_cc, reff_doc, reff_date, deskripsi, curr, ded_add, amount, pph, IF(per_ppn = 0,ppn,per_ppn) ppn, IF(per_ppn = 0,id_ppn,id_per_ppn) id_ppn from (select a.*,b.per_ppn, b.pv_date, b.curr, CASE WHEN b.per_ppn BETWEEN 1.09 AND 1.11 THEN 20 WHEN b.per_ppn = 11 THEN 1 WHEN b.per_ppn BETWEEN 1.19 AND 1.21 THEN 23 ELSE 0 END AS id_per_ppn from tbl_pv a INNER JOIN tbl_pv_h b on b.no_pv = a.no_pv where a.no_pv = '$pv_number' and a.profit_center = '$pv_pc') a) a left join b_master_cc b on b.no_cc = a.no_cc INNER JOIN mtax d on d.idtax = a.id_ppn
        ) a LEFT JOIN (select tanggal, curr, rate from ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.pv_date and b.curr = a.curr order by a.id, credit asc");
    }

    // =========================
    // INSERT DETAIL ADJUST
    // =========================
    foreach($detail_adjust as $row){

        $coa   = $row['coa'];
        $pc    = $row['pc'];
        $cc    = $row['cc'];
        $debit = $row['debit'];
        $credit= $row['credit'];
        $desc2 = mysqli_real_escape_string($conn2, $row['desc']);
        $curr  = $row['curr'] ?? 'IDR';
        $reff  = $row['reff_doc'];
        $reff_date = $row['reff_date'];


        $sql_coadet = mysqli_query($conn2,"select no_coa,nama_coa from mastercoa_v2 where no_coa = '$coa'");
        $row_coadet = mysqli_fetch_array($sql_coadet);
        $nama_coa_adj = $row_coadet['nama_coa'];

        $sqlcc = mysqli_query($conn2,"select cc_name from b_master_cc where no_cc = '$cc'");
        $rowcc = mysqli_fetch_array($sqlcc);
        $nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

        mysqli_query($conn2,"
            INSERT INTO b_bankout_adj_det
            (no_bankout,id_coa,no_cc,reff_doc,reff_date,deskripsi,t_debit,t_credit, profit_center)
            VALUES
            ('$doc_num', '$coa', '$cc', '$reff', '$reff_date', '$desc2', '$debit', '$credit', '$pc')
            ");


        mysqli_query($conn2,"
            INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES
            ('$doc_num', '$doc_date', 'Payment Voucher', '$coa', '$nama_coa_adj', '$cc', '$nama_cc', '$reff', '$reff_date', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$desc2', '$user', '$create_date', '', '', '', '', '$pc')
            ");
    }

    // =========================
    // COMMIT
    // =========================
    mysqli_commit($conn2);

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Data berhasil disimpan. No: '.$doc_num
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
