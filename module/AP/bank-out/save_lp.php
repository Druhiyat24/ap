<?php
include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

function q($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception(mysqli_error($conn));
    }
    return $result;
}

mysqli_begin_transaction($conn2);

try {

  $json = $_POST['data'] ?? '';
  $data = json_decode($json, true);

  if(!$data){
    throw new Exception("Data tidak valid");
  }

  $header        = $data['header'];
  $detail_pv     = $data['detail_lp'];
  $detail_adjust = $data['detail_adjust'];

    // =========================
    // HEADER VALUE
    // =========================
  $doc_date        = date('Y-m-d', strtotime($header['tgl']));
  $supp       = mysqli_real_escape_string($conn2, $header['supp']);
  $pc_bank    = mysqli_real_escape_string($conn2, $header['pc_header'] ?? '');
  $akun    = mysqli_real_escape_string($conn2, $header['account'] ?? '');
  $bank       = mysqli_real_escape_string($conn2, $header['bank'] ?? '');
  $curr   = mysqli_real_escape_string($conn2, $header['currency'] ?? '');
  $amount     = (float) ($header['amount'] ?? 0);
  $rate       = (float) ($header['rate'] ?? 0);
  $eqv        = (float) ($header['eqv'] ?? 0);
  $desc       = mysqli_real_escape_string($conn2, trim($header['desc'] ?? ''));

  if ($akun === '') {
    throw new Exception('Account tidak boleh kosong.');
  }

  if ($desc === '') {
    throw new Exception('Description tidak boleh kosong.');
  }

  if ($amount <= 0) {
    throw new Exception('Amount tidak boleh kosong.');
  }

  if (empty($detail_pv)) {
    throw new Exception('Pilih minimal 1 List Payment.');
  }

    // =========================
    // COA WAJIB CC (sama seperti get_coa_wajib_cc.php)
    // =========================
  $coaWajibCC = [];
  $sqlWajibCcAll = q($conn1, "select no_coa from mastercoa_v2 where support_gen_adm = 'Y' OR support_prod = 'Y' OR prod = 'Y' OR support_sell = 'Y'");
  while ($rowWajib = mysqli_fetch_assoc($sqlWajibCcAll)) {
    $coaWajibCC[] = $rowWajib['no_coa'];
  }

    // =========================
    // FORMAT PREFIX
    // =========================
  $kode_bank = mysqli_real_escape_string($conn2, $header['kode_bank'] ?? '');
  $user = $_SESSION['username'] ?? 'system';

  $bulan = date('m',strtotime($doc_date));
  $tahun = date('y',strtotime($doc_date));
  $status = "Draft";
  $create_date = date("Y-m-d H:i:s");

  $prefix = "BK/".$kode_bank."/".$pc_bank."/".$bulan.$tahun;

    // =========================
    // AMBIL NOMOR (LOCK)
    // =========================
  $sql = q($conn2,"
    SELECT MAX(CAST(RIGHT(no_bankout,5) AS UNSIGNED)) AS max_urut
    FROM b_bankout_h
    WHERE no_bankout LIKE '$prefix%'
    FOR UPDATE
    ");

  $row = mysqli_fetch_assoc($sql);
  $maxHeader = (int) ($row['max_urut'] ?? 0);

  // Nomor yang pernah dipakai tetap tercatat permanen di tbl_list_journal
  // walau no_bankout di header sudah berubah (akun diganti saat edit),
  // jadi nomor lama tidak boleh dipakai ulang untuk dokumen baru.
  $sqlJ = q($conn2,"
    SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut
    FROM tbl_list_journal
    WHERE no_journal LIKE '$prefix%'
    FOR UPDATE
    ");

  $rowJ = mysqli_fetch_assoc($sqlJ);
  $maxJournal = (int) ($rowJ['max_urut'] ?? 0);

  $urutan = max($maxHeader, $maxJournal) + 1;

  $doc_num = $prefix."/".sprintf("%05d",$urutan);

  $sqlcoa1 = q($conn2,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
  $rowcoa1 = mysqli_fetch_array($sqlcoa1);
  $no_coa1 = $rowcoa1['no_coa'] ?? null;
  $nama_coa1 = $rowcoa1['nama_coa'] ?? null;

  if (!$no_coa1) {
    throw new Exception('COA Bank untuk akun "'.$akun.'" tidak ditemukan di mastercoa_v2.');
  }

    // =========================
    // INSERT HEADER
    // =========================
  q($conn2,"
    INSERT INTO b_bankout_h
    (
    no_bankout, bankout_date, reff_doc, nama_supp, akun, bank, curr, amount, outstanding, rate, eqv_idr, deskripsi, status, create_by, create_date, stat_bi
    )
    VALUES
    ('$doc_num', '$doc_date', 'Payment', '$supp', '$akun', '$bank', '$curr', '$amount', '$amount', '$rate', '$eqv', '$desc', '$status', '$user', '$create_date', 'N')
    ");


  q($conn2,"
    INSERT INTO b_reportbank
    (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status)
    VALUES
    ('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr', '0', '$amount', '$amount', '$status')
    ");

    // Kumpulkan semua baris jurnal & tetap insert sekaligus (bulk) di akhir.
  $journalValues = [];
  $bankoutDetValues = [];
  $adjDetValues = [];

  $journalValues[] = "('$doc_num', '$doc_date', 'Payment', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_bank')";

    // =========================
    // INSERT DETAIL PV
    // =========================
  foreach($detail_pv as $pv){

    $no_lp  = mysqli_real_escape_string($conn2, $pv['no_lp']);
    $amountLp = (float) $pv['amount'];
    $pc     = mysqli_real_escape_string($conn2, $pv['pc']);

    q($conn2, "SET @bayar := $amountLp");

    $sql_pv = q($conn2,"WITH
      total_lp as (   select a.no_coa, a.nama_coa, a.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, a.nama_supp,a.no_payment,a.tgl_payment,max(a.tgl_tempo) as due_date,a.curr, (SUM(a.amount) - SUM(c.tax) + SUM(a.pph_value)) subtotal, SUM(c.tax) ppn, SUM(a.pph_value) pph, ((SUM(a.amount) - SUM(c.tax)+ SUM(a.pph_value)) + SUM(c.tax) - SUM(a.pph_value)) total, a.status, IFNULL(d.rate,1) rate from list_payment a INNER JOIN master_pc b on b.kode_pc = a.profit_center INNER JOIN kontrabon_h c on c.no_kbon = a.no_kbon LEFT JOIN (SELECT tanggal, curr, rate FROM ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) d on d.curr = a.curr and d.tanggal = a.tgl_payment where a.no_payment = '$no_lp' and a.profit_center = '$pc' group by a.no_payment, a.profit_center
      ),
      total_bk as (select a.profit_center, no_reff, sum(dpp) dpp_lp, sum(ppn) ppn_lp, sum(pph) pph_lp, sum(total) total_lp from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where b.status != 'Cancel' and no_reff like '%LP%' GROUP BY no_reff,a.profit_center)

      select a.no_coa, a.nama_coa, a.profit_center, a.nama_pc, a.nama_supp, a.no_payment, a.tgl_payment,a.due_date, a.curr, a.rate, (a.subtotal - COALESCE(b.dpp_lp,0)) subtotal, (a.ppn - COALESCE(b.ppn_lp,0)) ppn, (a.pph - COALESCE(b.pph_lp,0)) pph ,(a.total - COALESCE(b.total_lp,0)) total, a.status, LEAST(
      (a.pph - COALESCE(b.pph_lp,0)),
      @bayar
      ) AS bayar_pph,
      LEAST(
      (a.ppn - COALESCE(b.ppn_lp,0)),
      @bayar
      - LEAST((a.pph - COALESCE(b.pph_lp,0)), @bayar)
      ) AS bayar_ppn,LEAST(
      (a.subtotal - COALESCE(b.dpp_lp,0)),
      @bayar
      - LEAST((a.pph - COALESCE(b.pph_lp,0)), @bayar)
      - LEAST(
      (a.ppn - COALESCE(b.ppn_lp,0)),
      @bayar - LEAST((a.pph - COALESCE(b.pph_lp,0)), @bayar)
      )
      ) AS bayar_dpp
      from total_lp a LEFT JOIN total_bk b on b.no_reff = a.no_payment and b.profit_center = a.profit_center where (a.total - COALESCE(b.total_lp,0)) > 0
      ");
    $row_pv = mysqli_fetch_array($sql_pv);

    if (!$row_pv) {
      throw new Exception('List Payment "'.$no_lp.'" tidak ditemukan atau sudah lunas.');
    }

    $pv_pc = mysqli_real_escape_string($conn2, $row_pv['profit_center']);
    $pv_number = mysqli_real_escape_string($conn2, $row_pv['no_payment']);
    $pv_date = $row_pv['tgl_payment'];
    $pv_duedate = $row_pv['due_date'];
    $pv_curr = mysqli_real_escape_string($conn2, $row_pv['curr']);
    $pv_sub = (float) $row_pv['bayar_dpp'];
    $pv_ppn = (float) $row_pv['bayar_ppn'];
    $pv_pph = (float) $row_pv['bayar_pph'];
    $pv_rate = (float) $row_pv['rate'];
    $pv_pph_idr= $pv_pph * $pv_rate;
    $pv_total = $amountLp;
    $pv_no_coa = mysqli_real_escape_string($conn2, $row_pv['no_coa']);
    $pv_nama_coa = mysqli_real_escape_string($conn2, $row_pv['nama_coa']);
    $total_dppnya= $amountLp + $pv_pph;
    $total_dppnya_idr= $total_dppnya * $pv_rate;

    $bankoutDetValues[] = "('$doc_num', '$pv_number', '$pv_date', '$pv_duedate', '$pv_sub', '$pv_ppn', '$pv_pph', '$pv_total', '$pv_curr', '$amountLp', '1', '$amountLp', '$pv_pc')";

    $journalValues[] = "('$doc_num', '$doc_date', 'Payment', '$pv_no_coa', '$pv_nama_coa', '-', '-', '$pv_number', '$pv_date', '-', '-', '$pv_curr', '$pv_rate', '$total_dppnya', '0', '$total_dppnya_idr', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pv_pc')";

    if ($pv_pph > 0) {

      $sqlpph = q($conn2,"select no_coa,nama_coa from mtax where idtax = (select max(a.idtax) idtax from kontrabon a INNER JOIN list_payment b on b.no_kbon = a.no_kbon where b.no_payment = '$pv_number' group by a.no_kbon limit 1)");
      $rowpph = mysqli_fetch_array($sqlpph);
      $no_coa_pph = mysqli_real_escape_string($conn2, $rowpph['no_coa'] ?? '');
      $nama_coa_pph = mysqli_real_escape_string($conn2, $rowpph['nama_coa'] ?? '');

      $journalValues[] = "('$doc_num', '$doc_date', 'Payment', '$no_coa_pph', '$nama_coa_pph', '-', '-', '$pv_number', '$pv_date', '-', '-', '$pv_curr', '$pv_rate', '0', '$pv_pph', '0', '$pv_pph_idr', 'Draft', '$desc','$user', '$create_date', '', '', '', '', '$pv_pc')";

    }
  }

    // =========================
    // INSERT DETAIL ADJUST
    // =========================
  foreach($detail_adjust as $i => $rowAdj){

    $coa   = trim($rowAdj['coa'] ?? '');
    $pc    = trim($rowAdj['pc'] ?? '');
    $cc    = trim($rowAdj['cc'] ?? '') ?: '-';
    $debit = (float) ($rowAdj['debit'] ?? 0);
    $credit= (float) ($rowAdj['credit'] ?? 0);
    $desc2 = mysqli_real_escape_string($conn2, $rowAdj['desc'] ?? '');
    $reff  = mysqli_real_escape_string($conn2, $rowAdj['reff_doc'] ?? '-');
    $reff_date = !empty($rowAdj['reff_date']) ? date('Y-m-d', strtotime($rowAdj['reff_date'])) : $doc_date;

    if (!$coa || $coa === '-') {
      throw new Exception('Adjust baris ke-'.($i + 1).': COA wajib diisi.');
    }

    if (!$pc || $pc === '-') {
      throw new Exception('Adjust baris ke-'.($i + 1).': Profit Center wajib diisi.');
    }

    if (in_array($coa, $coaWajibCC, true) && ($cc === '-' || $cc === '')) {
      throw new Exception('Adjust baris ke-'.($i + 1).': COA '.$coa.' wajib isi Cost Center.');
    }

    if ($debit == 0 && $credit == 0) {
      throw new Exception('Adjust baris ke-'.($i + 1).': Debit/Credit harus diisi.');
    }

    $coaEsc = mysqli_real_escape_string($conn2, $coa);
    $pcEsc = mysqli_real_escape_string($conn2, $pc);
    $ccEsc = mysqli_real_escape_string($conn2, $cc);

    $sql_coadet = q($conn2,"select no_coa,nama_coa from mastercoa_v2 where no_coa = '$coaEsc'");
    $row_coadet = mysqli_fetch_array($sql_coadet);
    $nama_coa_adj = $row_coadet['nama_coa'] ?? null;

    if (!$nama_coa_adj) {
      throw new Exception('Adjust baris ke-'.($i + 1).': COA "'.$coa.'" tidak ditemukan.');
    }

    $nama_cc = null;
    if ($ccEsc !== '-' && $ccEsc !== '') {
      $sqlcc = q($conn2,"select cc_name from b_master_cc where no_cc = '$ccEsc'");
      $rowcc = mysqli_fetch_array($sqlcc);
      $nama_cc = $rowcc['cc_name'] ?? null;
    }
    $nama_cc = mysqli_real_escape_string($conn2, $nama_cc ?: '-');

    $adjDetValues[] = "('$doc_num', '$coaEsc', '$ccEsc', '$reff', '$reff_date', '$desc2', '$debit', '$credit', '$pcEsc')";

    $journalValues[] = "('$doc_num', '$doc_date', 'Payment', '$coaEsc', '$nama_coa_adj', '$ccEsc', '$nama_cc', '$reff', '$reff_date', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$desc2', '$user', '$create_date', '', '', '', '', '$pcEsc')";
  }

    // =========================
    // BULK INSERT
    // =========================
  if (!empty($bankoutDetValues)) {
    q($conn2, "INSERT INTO b_bankout_det (no_bankout,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr, rates, for_balance, profit_center)
      VALUES " . implode(',', $bankoutDetValues));
  }

  if (!empty($adjDetValues)) {
    q($conn2, "INSERT INTO b_bankout_adj_det
      (no_bankout,id_coa,no_cc,reff_doc,reff_date,deskripsi,t_debit,t_credit, profit_center)
      VALUES " . implode(',', $adjDetValues));
  }

  q($conn2, "INSERT INTO tbl_list_journal
    (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
    VALUES " . implode(',', $journalValues));

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
