<?php
include '../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

// Jalankan query mutating (INSERT/UPDATE/DELETE) dan lempar Exception kalau
// gagal, supaya transaksi ke-rollback dengan benar.
function dbExec($conn, $sql)
{
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception('DB Error: ' . mysqli_error($conn));
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
  $nama_supp       = mysqli_real_escape_string($conn2, $header['supp']);
  $ref_num        = $header['ref'];
  $akun    = $header['account'];
  $curr   = $header['currency'];
  $kode_kas   = $header['kode_kas'];
  $pc_kas   = $header['pc_header'];
  $amount     = $header['amount'];
  $desc       = mysqli_real_escape_string($conn2, $header['desc']);
  $user = $_SESSION['username'] ?? 'system';
  $status = "Draft";
  $create_date = date("Y-m-d H:i:s");

    // =========================
    // FORMAT PREFIX
    // =========================

  $bulan = date('m',strtotime($doc_date));
  $tahun = date('Y',strtotime($doc_date));

  $prefix = "KKK/".$kode_kas."/".$tahun."/".$bulan;

/* =========================
   AMBIL MAX URUTAN
========================= */

$sql = dbExec($conn2,"
SELECT MAX(CAST(RIGHT(no_pco,5) AS UNSIGNED)) AS max_urut
FROM c_petty_cashout_h
WHERE no_pco LIKE '$prefix%'
FOR UPDATE
");

$sqlJ = dbExec($conn2,"
SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut
FROM tbl_list_journal
WHERE no_journal LIKE '$prefix%'
FOR UPDATE
");

$row = mysqli_fetch_assoc($sql);
$maxHeader = (int) ($row['max_urut'] ?? 0);

$rowJ = mysqli_fetch_assoc($sqlJ);
$maxJournal = (int) ($rowJ['max_urut'] ?? 0);

$urutan = max($maxHeader, $maxJournal) + 1;


/* =========================
   DOC NUMBER BARU
========================= */

$doc_num = $prefix."/".sprintf("%05d",$urutan);

$sqlcoa = dbExec($conn2,"select nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $akun) . "'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

    // =========================
    // INSERT HEADER
    // =========================
dbExec($conn2,"
INSERT INTO c_petty_cashout_h (no_pco,tgl_pco,reff,nama_supp,coa_akun,curr,amount,deskripsi,status, create_by,create_date, reff_doc)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$nama_supp', '$akun', '$curr', '$amount','$desc', '$status', '$user', '$create_date', '')
");


dbExec($conn2,"
INSERT INTO c_report_pettycash (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance, status)
VALUES
('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr', '0', '$amount', '$amount', '$status')
");

    // =========================
    // DETAIL LP + JURNAL (bulk insert)
    // =========================
    // Baris-baris c_petty_cashout_det / tbl_list_journal dikumpulkan dulu ke
    // array, baru di-insert sekali per tabel di akhir.
  $detRows     = [];
  $journalRows = ["('$doc_num', '$doc_date', '$ref_num', '$akun', '$nama_coa', '-', '-', '', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_kas')"];

  foreach($detail_pv as $pv){

    $no_lp  = $pv['no_lp'];
    $amount_lp = $pv['amount'];
    $pc     = $pv['pc'];

    $no_lp_esc = mysqli_real_escape_string($conn2, $no_lp);
    $pc_esc = mysqli_real_escape_string($conn2, $pc);

    dbExec($conn2, "SET @bayar := $amount_lp");

    $sql_pv = dbExec($conn2,"WITH
      total_lp as (   select a.no_coa, a.nama_coa, a.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, a.nama_supp,a.no_payment,a.tgl_payment,max(a.tgl_tempo) as due_date,a.curr, (SUM(a.amount) - SUM(c.tax) + SUM(a.pph_value)) subtotal, SUM(c.tax) ppn, SUM(a.pph_value) pph, ((SUM(a.amount) - SUM(c.tax)+ SUM(a.pph_value)) + SUM(c.tax) - SUM(a.pph_value)) total, a.status, IFNULL(d.rate,1) rate from list_payment a INNER JOIN master_pc b on b.kode_pc = a.profit_center INNER JOIN kontrabon_h c on c.no_kbon = a.no_kbon LEFT JOIN (SELECT tanggal, curr, rate FROM ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) d on d.curr = a.curr and d.tanggal = a.tgl_payment where a.no_payment = '$no_lp_esc' and a.profit_center = '$pc_esc' group by a.no_payment, a.profit_center
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
      throw new Exception("Data List Payment untuk $no_lp tidak ditemukan atau sudah lunas");
    }

    $pv_pc = $row_pv['profit_center'];
    $pv_number = mysqli_real_escape_string($conn2, $row_pv['no_payment']);
    $pv_date = $row_pv['tgl_payment'];
    $pv_duedate = $row_pv['due_date'];
    $pv_curr = $row_pv['curr'];
    $pv_sub = $row_pv['bayar_dpp'];
    $pv_ppn = $row_pv['bayar_ppn'];
    $pv_pph = $row_pv['bayar_pph'];
    $pv_rate = $row_pv['rate'];
    $pv_pph_idr= $pv_pph * $pv_rate;
    $pv_total = $amount_lp;
    $pv_no_coa = $row_pv['no_coa'];
    $pv_nama_coa = mysqli_real_escape_string($conn2, $row_pv['nama_coa']);
    $total_dppnya= $amount_lp + $pv_pph;
    $total_dppnya_idr= $total_dppnya * $pv_rate;

    $detRows[] = "('$doc_num', '$doc_date', '$pv_number', '$pv_date', '$pv_duedate', '$pv_sub', '$pv_ppn', '$pv_pph', '$pv_total', '$pv_curr', '$amount_lp', '$amount_lp')";

    $journalRows[] = "('$doc_num', '$doc_date', 'List Payment', '$pv_no_coa', '$pv_nama_coa', '-', '-', '$pv_number', '$pv_date', '-', '-', '$pv_curr', '$pv_rate', '$total_dppnya', '0', '$total_dppnya_idr', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pv_pc')";

    if ($pv_pph > 0) {

      $sqlpph = dbExec($conn2,"select no_coa,nama_coa from mtax where idtax = (select max(a.idtax) idtax from kontrabon a INNER JOIN list_payment b on b.no_kbon = a.no_kbon where b.no_payment = '$pv_number' group by a.no_kbon limit 1)");
      $rowpph = mysqli_fetch_array($sqlpph);
      $no_coa_pph = mysqli_real_escape_string($conn2, $rowpph['no_coa'] ?? '');
      $nama_coa_pph = mysqli_real_escape_string($conn2, $rowpph['nama_coa'] ?? '');

      if (!empty($no_coa_pph)) {
        $journalRows[] = "('$doc_num', '$doc_date', 'List Payment', '$no_coa_pph', '$nama_coa_pph', '-', '-', '$pv_number', '$pv_date', '-', '-', '$pv_curr', '$pv_rate', '0', '$pv_pph', '0', '$pv_pph_idr', 'Draft', '$desc','$user', '$create_date', '', '', '', '', '$pv_pc')";
      }

    }


  }

    // =========================
    // DETAIL ADJUST
    // =========================
  $adjRows = [];

  foreach($detail_adjust as $row){

    $coa   = $row['coa'];
    $pc    = $row['pc'];
    $cc    = $row['cc'];
    $debit = $row['debit'];
    $credit= $row['credit'];
    $desc2 = mysqli_real_escape_string($conn2, $row['desc']);
    $reff  = $row['reff_doc'];
    $reff_date = $row['reff_date'];


    $sql_coadet = dbExec($conn2,"select no_coa,nama_coa from mastercoa_v2 where no_coa = '" . mysqli_real_escape_string($conn2, $coa) . "'");
    $row_coadet = mysqli_fetch_array($sql_coadet);
    $nama_coa_adj = $row_coadet['nama_coa'];

    $sqlcc = dbExec($conn2,"select cc_name from b_master_cc where no_cc = '" . mysqli_real_escape_string($conn2, $cc) . "'");
    $rowcc = mysqli_fetch_array($sqlcc);
    $nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

    $adjRows[] = "('$doc_num', '$doc_date', '$coa', '$reff', '$reff_date', '$desc2', '$debit', '$credit', '$cc', '$pc')";

    $journalRows[] = "('$doc_num', '$doc_date', 'List Payment', '$coa', '$nama_coa_adj', '$cc', '$nama_cc', '$reff', '$reff_date', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$desc2', '$user', '$create_date', '', '', '', '', '$pc')";
  }

    // =========================
    // BULK INSERT
    // =========================
  if (!empty($detRows)) {
    dbExec($conn2, "
      INSERT INTO c_petty_cashout_det (no_pco, tgl_pco,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr,amount)
      VALUES " . implode(', ', $detRows)
    );
  }

  if (!empty($adjRows)) {
    dbExec($conn2, "
      INSERT INTO c_petty_cashout_adj_det
      (no_pco, tgl_pco, id_coa, reff_doc, reff_date, deskripsi, t_debit, t_credit, no_cc, profit_center)
      VALUES " . implode(', ', $adjRows)
    );
  }

  dbExec($conn2, "
    INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
    VALUES " . implode(', ', $journalRows)
  );

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
