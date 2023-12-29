<?php
$nama_supp =  '';
$start_date = '';
$end_date = '';
$tanggal = '';
$no_faktur = '';
$txt_inv = '';
$txt_tglsi = '';
$nokontrabon = '';
$jurnal = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nokontrabon = isset($_POST['nokontrabon']) ? $_POST['nokontrabon']: null ;
  $jurnal = isset($_POST['jurnal']) ? $_POST['jurnal']: null ;  
  $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null ;
  $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal']: null ;
  $no_faktur = isset($_POST['no_faktur']) ? $_POST['no_faktur']: null ;
  $txt_inv = isset($_POST['txt_inv']) ? $_POST['txt_inv']: null ;
  $txt_tglsi = isset($_POST['txt_tglsi']) ? $_POST['txt_tglsi']: null ;        
  $start_date = date("Y-m-d",strtotime($_POST['start_date']));
  $end_date = date("Y-m-d",strtotime($_POST['end_date']));
}?>