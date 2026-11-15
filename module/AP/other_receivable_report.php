<?php include '../header.php' ?>
<style >
    .modal {
      text-align: center;
      padding: 0!important;
  }

  .modal:before {
      content: '';
      display: inline-block;
      height: 100%;
      vertical-align: middle;
      margin-right: -4px;
  }

  .modal-dialog {
      display: inline-table;
      width: 700px;
      text-align: left;
      vertical-align: middle;
  }
</style>
<!-- MAIN -->
<div class="col p-4">
    <h2 class="text-center">OTHER RECEIVABLE REPORT</h2>
    <div class="box">
        <div class="box header">

            <form id="form-data" action="other_receivable_report.php" method="post">        
                <div class="form-row">

                  <div class="col-md-3">
                    <label for="nama_type"><b>No COA</b></label>            
                    <select style="background-color: gray;" class="form-control selectpicker" name="coa_number" id="coa_number" data-dropup-auto="false" data-live-search="true" required>
                     <option value="-" disabled selected="true">Select coa</option> 
                     <?php
                     $coa_number ='';
                     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $coa_number = isset($_POST['coa_number']) ? $_POST['coa_number']: null;
                    }                 
                    $sql = mysql_query("select DISTINCT no_coa, nama_coa, CONCAT(no_coa,' - ',nama_coa) as coa from mastercoa_v2 where no_coa in ('1.34.05','1.34.04')",$conn1);
                    while ($row = mysql_fetch_array($sql)) {
                        $data = $row['coa'];
                        $id_ctg2 = $row['no_coa'];
                        if($row['no_coa'] == $_POST['coa_number']){
                            $isSelected = ' selected="selected"';
                        }else{
                            $isSelected = '';
                        }
                        echo '<option value="'.$id_ctg2.'"'.$isSelected.'">'. $data .'</option>';    
                    }?>
                </select>
            </div>

            <div class="col-md-2 mb-3"> 
                <label for="start_date"><b>From</b></label>          
                <input type="text" style="font-size: 12px;" class="form-control tanggal" id="start_date" name="start_date" 
                value="<?php
                $start_date ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                   $start_date = date("Y-m-d",strtotime($_POST['start_date']));
               }
               if(!empty($_POST['start_date'])) {
                   echo $_POST['start_date'];
               }
               else{
                   echo date("d-m-Y");
               } ?>" 
               placeholder="Tanggal Awal">
           </div>

           <div class="col-md-2 mb-3"> 
            <label for="end_date"><b>To</b></label>          
            <input type="text" style="font-size: 12px;" class="form-control tanggal" id="end_date" name="end_date" 
            value="<?php
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
               $end_date = date("Y-m-d",strtotime($_POST['end_date']));
           }
           if(!empty($_POST['end_date'])) {
               echo $_POST['end_date'];
           }
           else{
               echo date("d-m-Y");
           } ?>" 
           placeholder="Tanggal Awal">
       </div>
       <div class="input-group-append col">                                   
        <button  type="submit" id="submit" value=" Search " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
        line-height: 1;
        padding: -2px 8px;
        font-size: 1rem;
        text-align: center;
        color: #fff;
        text-shadow: 1px 1px 1px #000;
        border-radius: 6px;
        background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
<!--             <button type="button" id="reset" value=" Reset " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button> -->

    <?php
        // $status = isset($_POST['status']) ? $_POST['status']: null;
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $tanggal_awal = date("Y-m-d",strtotime($start_date));
    $tanggal_akhir = date("Y-m-d",strtotime($end_date)); 
    $tanggal1 = isset($tanggal_awal) ? $tanggal_awal : 0;
    $tanggal2 = isset($tanggal_akhir) ? $tanggal_akhir : 0;
    $kata_awal = date("M",strtotime($start_date));
    $tengah = '_';
    $kata_akhir = date("Y",strtotime($start_date));
    $kata_filter = $kata_awal . $tengah . $kata_akhir;
    $coa_number = isset($_POST['coa_number']) ? $_POST['coa_number']: null;
                            
if ($coa_number == '1.34.05') {

    echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_or_report_material.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-info " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

    ';
}else{
    echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_or_report.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

    ';
}
        //<a style="padding-right: 5px;" target="_blank" href="ekspor_sfp_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SFP</i></button></a>

        // <a style="padding-right: 5px;" target="_blank" href="ekspor_spl_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SPL</i></button></a>

        //     <a style="padding-left: 10px";><button type="button" class="btn btn-info " name="co_sal" id="co_sal" style= "margin-top: 30px;"><i class="fa fa-clipboard" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Copy Saldo</i></button></a>


    ?>  

</div>                                                            
</div>
<br/>
</div>
</form> 

<!-- <?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create List payment'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '9'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>
            <button id="btnupload" type="button" class="btn-success btn-xs" style="border-radius: 6%"><span class="fa fa-upload" aria-hidden="true"></span> Upload</button>';
        }else{
    echo '';
    }
?> -->
<!-- Tanda sedang memuat. Muncul saat tombol Search ditekan (querynya berat,
     bisa belasan detik) dan saat tabel sedang disiapkan DataTables. -->
<div id="or-loading" style="display:none;">
    <div class="or-loading-kotak"><span class="or-spin"></span> Loading data...</div>
</div>
<style>
/* ============================================================================
   Tabel laporan ini dipakai DUA tampilan dgn jumlah kolom berbeda:
     - COA 1.34.05 : 36 kolom, 8 grup bertingkat   -> .or-tbl-grp
     - COA lain    : 13 kolom biasa                -> .or-tbl-04
   Gayanya disamakan dgn menu VAT In (ppn_masukan_report.php): tiap sel punya
   latar warna grup + garis pembatas supaya kolom mana milik grup mana tetap
   kelihatan walau digeser jauh ke kanan, kepala biru tua, dan dua kolom
   pertama menempel saat digulung.

   Peta kolom 1.34.05 (1-based):
     1 No PO | 2 No BPB | 3 BPB Date | 4 Supplier | 5 Buyer | 6 No Req
     7 Req Date | 8 No DN | 9 DN Date | 10 Rate
     11-13 Beginning | 14-16 Addition | 17-19 Addition Return
     20-24 Deduction Return | 25-27 Deduction (ALK) | 28-30 Deduction (GM)
     31-33 Forex | 34-36 Ending
   Peta kolom COA lain (1-based):
     1 No Memo | 2 Memo Date | 3 No Invoice Vendor | 4 Supplier | 5 Buyer
     6 No DN | 7 DN Date | 8 Beginning | 9 Addition | 10 Deduction (ALK)
     11 Deduction (GM) | 12 Forex | 13 Ending
   ============================================================================ */

/* Hanya TABEL yang bisa digulung mendatar; Search/length/info/pagination
   ditaruh di luar kotak ini lewat pilihan 'dom' DataTables. */
.app-dt-scroll { overflow-x: auto; }

/* ---------------- yang dipakai DUA tampilan ---------------- */
#mytable2.or-tbl { font-size: 12px; border-collapse: separate; border-spacing: 0; }

#mytable2.or-tbl thead th { text-align: center; font-weight: 600; border: 1px solid rgba(255,255,255,.42) !important; }
#mytable2.or-tbl thead tr:first-child th { background:#1E3A8A; color:#fff; padding:10px 12px; font-size:12px; letter-spacing:.02em; }
/* Baris KEDUA kepala cuma ada di tampilan bergrup. Sengaja nth-child(2), BUKAN
   last-child - di tampilan 13 kolom baris pertama sekaligus baris terakhir,
   jadi last-child akan ikut mengecilkan judul utamanya. */
#mytable2.or-tbl thead tr:nth-child(2) th { background:#33509f; color:#e2ebff; padding:7px 10px; font-size:10px; text-transform:uppercase; letter-spacing:.06em; }

#mytable2.or-tbl tbody td {
    white-space: nowrap; padding: 7px 10px; color: #334155; font-variant-numeric: tabular-nums;
    border-right: 1px solid #d7dfea !important; border-bottom: 1px solid #e3e9f2 !important;
}
#mytable2.or-tbl tbody td:last-child { border-right: 0 !important; }
#mytable2.or-tbl tbody tr:hover td { filter: brightness(.975); }

/* dua kolom pertama menempel saat digulung ke kanan (offset 'left' diisi JS) */
#mytable2.or-tbl thead tr:first-child th:nth-child(-n+2),
#mytable2.or-tbl tbody td:nth-child(-n+2) { position: sticky; z-index: 2; }
#mytable2.or-tbl tbody td:nth-child(-n+2) { background: #fff; }
#mytable2.or-tbl tbody tr:hover td:nth-child(-n+2) { background: #f5f8ff; }
#mytable2.or-tbl thead tr:first-child th:nth-child(-n+2) { background: #1E3A8A; z-index: 3; }
#mytable2.or-tbl thead tr:first-child th:nth-child(2),
#mytable2.or-tbl tbody td:nth-child(2) { box-shadow: 4px 0 8px -5px rgba(15,23,42,.35); }

/* ---- baris Total ---- */
#mytable2.or-tbl tfoot th {
    background:#eef2f7; border-top:2px solid #1E3A8A !important; border-right:1px solid #cbd5e1;
    border-bottom:0; padding:10px; font-size:12px; font-weight:700; color:#0f172a; white-space:nowrap;
    font-variant-numeric: tabular-nums;
}
#mytable2.or-tbl tfoot th:first-child { background:#e2e8f0; }
#mytable2.or-tbl tfoot th:last-child { border-right:0; color:#1E3A8A; }

/* Warna grup ikut dibawa ke baris Total, senada tapi lebih pekat - sama spt
   menu VAT In. Sel pertama ber-colspan, jadi nomor selnya bergeser satu. */
#mytable2.or-tbl-grp tfoot th:nth-child(n+2):nth-child(-n+4)   { background:#e2e8f0; } /* Beginning */
#mytable2.or-tbl-grp tfoot th:nth-child(n+5):nth-child(-n+7)   { background:#d7f5e4; } /* Addition */
#mytable2.or-tbl-grp tfoot th:nth-child(n+8):nth-child(-n+10)  { background:#fdf0cf; } /* Addition Return */
#mytable2.or-tbl-grp tfoot th:nth-child(n+11):nth-child(-n+15) { background:#fce7f3; } /* Deduction Return */
#mytable2.or-tbl-grp tfoot th:nth-child(n+16):nth-child(-n+18) { background:#fde2e2; } /* Deduction (ALK) */
#mytable2.or-tbl-grp tfoot th:nth-child(n+19):nth-child(-n+21) { background:#dfe5fb; } /* Deduction (GM) */
#mytable2.or-tbl-grp tfoot th:nth-child(n+22):nth-child(-n+24) { background:#ede9fe; } /* Forex */
#mytable2.or-tbl-grp tfoot th:nth-child(n+25):nth-child(-n+27) { background:#d8eefc; } /* Ending */

#mytable2.or-tbl-04 tfoot th:nth-child(2) { background:#e2e8f0; }
#mytable2.or-tbl-04 tfoot th:nth-child(3) { background:#d7f5e4; }
#mytable2.or-tbl-04 tfoot th:nth-child(4) { background:#fde2e2; }
#mytable2.or-tbl-04 tfoot th:nth-child(5) { background:#dfe5fb; }
#mytable2.or-tbl-04 tfoot th:nth-child(6) { background:#ede9fe; }
#mytable2.or-tbl-04 tfoot th:nth-child(7) { background:#d8eefc; }

/* ---------------- 1.34.05: 36 kolom bergrup ---------------- */
/* garis warna penanda grup di bawah judulnya (anak ke-11 s/d ke-18) */
#mytable2.or-tbl-grp thead tr:first-child th:nth-child(n+11) { border-bottom: 3px solid transparent !important; }
#mytable2.or-tbl-grp thead tr:first-child th:nth-child(11) { border-bottom-color:#94a3b8 !important; } /* Beginning */
#mytable2.or-tbl-grp thead tr:first-child th:nth-child(12) { border-bottom-color:#22c07f !important; } /* Addition */
#mytable2.or-tbl-grp thead tr:first-child th:nth-child(13) { border-bottom-color:#f0a413 !important; } /* Addition Return */
#mytable2.or-tbl-grp thead tr:first-child th:nth-child(14) { border-bottom-color:#ec4899 !important; } /* Deduction Return */
#mytable2.or-tbl-grp thead tr:first-child th:nth-child(15) { border-bottom-color:#f4635f !important; } /* Deduction (ALK) */
#mytable2.or-tbl-grp thead tr:first-child th:nth-child(16) { border-bottom-color:#6366f1 !important; } /* Deduction (GM) */
#mytable2.or-tbl-grp thead tr:first-child th:nth-child(17) { border-bottom-color:#a855f7 !important; } /* Forex */
#mytable2.or-tbl-grp thead tr:first-child th:nth-child(18) { border-bottom-color:#38bdf8 !important; } /* Ending */

/* latar per grup */
#mytable2.or-tbl-grp tbody td:nth-child(n+11):nth-child(-n+13) { background:#f1f5f9; } /* Beginning */
#mytable2.or-tbl-grp tbody td:nth-child(n+14):nth-child(-n+16) { background:#ecfdf3; } /* Addition */
#mytable2.or-tbl-grp tbody td:nth-child(n+17):nth-child(-n+19) { background:#fffbeb; } /* Addition Return */
#mytable2.or-tbl-grp tbody td:nth-child(n+20):nth-child(-n+24) { background:#fdf2f8; } /* Deduction Return */
#mytable2.or-tbl-grp tbody td:nth-child(n+25):nth-child(-n+27) { background:#fef2f2; } /* Deduction (ALK) */
#mytable2.or-tbl-grp tbody td:nth-child(n+28):nth-child(-n+30) { background:#eef2ff; } /* Deduction (GM) */
#mytable2.or-tbl-grp tbody td:nth-child(n+31):nth-child(-n+33) { background:#f5f3ff; } /* Forex */
#mytable2.or-tbl-grp tbody td:nth-child(n+34):nth-child(-n+36) { background:#f0f9ff; } /* Ending */

/* kolom Currency - isinya berulang, dikalemkan */
#mytable2.or-tbl-grp tbody td:nth-child(11), #mytable2.or-tbl-grp tbody td:nth-child(14),
#mytable2.or-tbl-grp tbody td:nth-child(17), #mytable2.or-tbl-grp tbody td:nth-child(22),
#mytable2.or-tbl-grp tbody td:nth-child(25), #mytable2.or-tbl-grp tbody td:nth-child(28),
#mytable2.or-tbl-grp tbody td:nth-child(31), #mytable2.or-tbl-grp tbody td:nth-child(34)
    { color:#64748b; font-size:11.5px; font-weight:600; }

/* kolom Eqv IDR - angka utama, ditebalkan & diberi garis pemisah grup */
#mytable2.or-tbl-grp tbody td:nth-child(13), #mytable2.or-tbl-grp tbody td:nth-child(16),
#mytable2.or-tbl-grp tbody td:nth-child(19), #mytable2.or-tbl-grp tbody td:nth-child(24),
#mytable2.or-tbl-grp tbody td:nth-child(27), #mytable2.or-tbl-grp tbody td:nth-child(30),
#mytable2.or-tbl-grp tbody td:nth-child(33), #mytable2.or-tbl-grp tbody td:nth-child(36)
    { font-weight:700; color:#0f172a; border-right:1px solid #aebbcd !important; }

/* ---------------- COA lain: 13 kolom biasa ---------------- */
/* Kolomnya SENGAJA tidak diubah - yang disamakan cuma gayanya. Tiap kolom uang
   dapat warnanya sendiri dgn urutan warna yang sama spt tampilan bergrup. */
#mytable2.or-tbl-04 thead tr:first-child th:nth-child(n+8) { border-bottom: 3px solid transparent !important; }
#mytable2.or-tbl-04 thead tr:first-child th:nth-child(8)  { border-bottom-color:#94a3b8 !important; } /* Beginning */
#mytable2.or-tbl-04 thead tr:first-child th:nth-child(9)  { border-bottom-color:#22c07f !important; } /* Addition */
#mytable2.or-tbl-04 thead tr:first-child th:nth-child(10) { border-bottom-color:#f4635f !important; } /* Deduction (ALK) */
#mytable2.or-tbl-04 thead tr:first-child th:nth-child(11) { border-bottom-color:#6366f1 !important; } /* Deduction (GM) */
#mytable2.or-tbl-04 thead tr:first-child th:nth-child(12) { border-bottom-color:#a855f7 !important; } /* Forex */
#mytable2.or-tbl-04 thead tr:first-child th:nth-child(13) { border-bottom-color:#38bdf8 !important; } /* Ending */

#mytable2.or-tbl-04 tbody td:nth-child(8)  { background:#f1f5f9; }
#mytable2.or-tbl-04 tbody td:nth-child(9)  { background:#ecfdf3; }
#mytable2.or-tbl-04 tbody td:nth-child(10) { background:#fef2f2; }
#mytable2.or-tbl-04 tbody td:nth-child(11) { background:#eef2ff; }
#mytable2.or-tbl-04 tbody td:nth-child(12) { background:#f5f3ff; }
#mytable2.or-tbl-04 tbody td:nth-child(13) { background:#f0f9ff; font-weight:700; color:#0f172a; }

#or-loading { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 2000;
              background: rgba(255,255,255,.75); align-items: center; justify-content: center; }
.or-loading-kotak { background:#fff; border:1px solid #d7dfea; border-radius:10px;
              padding:14px 22px; font-weight:600; color:#1e293b;
              box-shadow:0 10px 30px -12px rgba(15,37,87,.45); }
.or-spin { display:inline-block; width:16px; height:16px; vertical-align:-3px;
           margin-right:8px; border:3px solid #cfd8e6; border-top-color:#1e4fa3;
           border-radius:50%; animation: or-putar .8s linear infinite; }
@keyframes or-putar { to { transform: rotate(360deg); } }
</style>

<div class="box body">
    <div class="row">       
        <div class="col-md-12">      
           <!-- SENGAJA tanpa .tableFix: yang menggulung mendatar adalah kotak
                .app-dt-scroll bikinan DataTables, yang isinya CUMA tabel - jadi
                Search & paging di luarnya tidak ikut bergeser ke kanan. -->
           <div>        
            <?php
            /* Gaya meniru menu VAT In. Tabel yang sama dipakai dua tampilan dgn
               jumlah kolom berbeda, jadi peta warna kolomnya dipisah:
                 .or-tbl     - yang sama-sama dipakai (kepala, garis, baris Total,
                               dua kolom pertama menempel)
                 .or-tbl-grp - 1.34.05, 36 kolom bergrup
                 .or-tbl-04  - akun lain, 13 kolom biasa */
            $or_grup = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $or_grup = (isset($_POST['coa_number']) && $_POST['coa_number'] == '1.34.05')
                         ? ' or-tbl or-tbl-grp' : ' or-tbl or-tbl-04';
            }
            ?>
            <table id="mytable2" class="table table-striped table-bordered text-nowrap<?php echo $or_grup; ?>" role="grid" cellspacing="0" width="100%">
                <thead>
                    <tr class="thead-dark">
                        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $coa_number = isset($_POST['coa_number']) ? $_POST['coa_number']: null;
                // ---------------------------------------------------------------------------
// PENYAJIAN SELISIH KURS 12 BPB (permintaan user, 16 Sep 2026).
//
// Ke-12 BPB ini BPB USD: nilai awalnya dibukukan pakai kurs PAJAK tanggal BPB,
// sedangkan alokasi DN-nya (0043/ALK/NAG/0826, 28-Ags-2026) pakai kurs 17.796.
// Sisanya = jumlah USD x selisih kurs, dan sudah ditutup akuntansi lewat jurnal
// reklas GM/NAG/0826/00197 (31-Ags-2026, total 472.818,49).
//
// Karena sisanya memang selisih kurs, user minta angka itu TAMPIL di kolom
// Forex Gain/(Loss), bukan di Deduction (GM), DAN jurnal GM-nya akan dibatalkan.
// Maka forex 12 BPB ini dihitung SENDIRI dari SELISIH KURS:
//     jumlah USD x (kurs PAJAK tgl BPB - kurs alokasi)
// tanpa mengambil angka dari jurnal GM, karena jurnal itu akan dibatalkan.
//
// Jurnal GM SENGAJA TETAP DITAMPILKAN apa adanya di kolom Deduction (GM)
// (permintaan user 16 Sep 2026) supaya beda sebelum & sesudah cancel kelihatan:
//   - SELAMA GM belum dibatalkan: nilainya terhitung dua kali (GM + Forex),
//     saldo akhir 12 baris ini tampil MINUS 472.818,49. Ini DISENGAJA sbg
//     penanda bahwa jurnal GM masih perlu dibatalkan - bukan bug.
//   - SESUDAH GM dibatalkan: Deduction (GM) jadi 0, tinggal kolom Forex,
//     saldo akhir Agustus 2026 = 0 dan saldo awal September 2026 = 0.
// Pengecualian ini SENGAJA dibatasi ke 12 nomor di bawah; ratusan alokasi BPB
// USD lain yang kolom Forex-nya juga 0 DIBIARKAN apa adanya (belum diminta).
//
// Kolom ded_fgl yang umum (tot_dn - total_alk2) memang selalu 0 untuk BPB USD -
// kedua sisinya sama-sama dikali kurs alokasi. Rumus itu dibuat untuk kasus
// sebaliknya (BPB IDR + alokasi valas) dan sengaja dibiarkan apa adanya.
//
// Catatan: query 1.34.05 ini disalin utuh di other_receivable_report.php dan
// ekspor_or_report_material.php - kalau salah satu diubah, ubah dua-duanya.
// ---------------------------------------------------------------------------
$OR_FX_GM  = 'GM/NAG/0826/00197';   // jurnal reklas yang digantikan perhitungan di bawah
$OR_FX_ARR = array(
   'GACC/IN/0626/03136','GACC/IN/0626/03144','GACC/IN/0626/03357',
   'GACC/IN/0626/03445','GACC/IN/0626/03446','GACC/IN/0626/03518',
   'GACC/IN/0626/03522','GACC/IN/0626/03572','GACC/IN/0626/03573',
   'GACC/IN/0726/03977','GACC/IN/0726/04267','GACC/IN/0726/04388',
   // Ditambahkan 8 Okt 2026 - alokasi 0033/ALK/NAG/0926 (kurs 17.863)
   // vs kurs pajak tgl BPB 17.937. Dicek ke produksi: TIDAK ADA jurnal
   // GM di 1.34.05 untuk keenamnya, jadi tidak ada risiko dobel hitung.
   'GACC/IN/0726/04456','GACC/IN/0726/04475','GACC/IN/0726/04502',
   'GACC/IN/0726/04503','GACC/IN/0726/04504','GACC/IN/0726/04508'
);
/* Versi teks utk dipakai di dalam SQL. Satu daftar, dua bentuk -
   supaya penambahan BPB baru cukup di satu tempat. */
$OR_FX_BPB = "'" . implode("','", $OR_FX_ARR) . "'";
            if ($coa_number == '1.34.05') {
                                echo '<th rowspan="2" style="text-align:center;vertical-align:middle;">No PO</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">No BPB</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">BPB Date</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">Supplier</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">Buyer</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">No Req</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">Req Date</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">No DN</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">DN Date</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">Rate</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">Begining Balance</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">Addition</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">Addition Return</th>
                                <th colspan="5" style="text-align:center;vertical-align:middle;">Deduction Return</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">Deduction (ALK)</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">Deduction (GM)</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">Forex Gain / (Loss)</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">Ending Balance</th>';
                                /* Tingkat KEDUA kepala tabel: tiap bagian uang dipecah
                                   jadi Currency / Amount OCY / Eqv IDR, meniru menu
                                   PPN Masukan (permintaan pemakai 13 Nov 2026). */
                                $sub = '<th style="text-align:center;">Currency</th>' . '<th style="text-align:center;">Amount OCY</th>' . '<th style="text-align:center;">Eqv IDR</th>';
                                /* Khusus Addition Return, nomor &amp; tanggal dokumen RO-nya
                                   ikut ditampilkan - pemakai perlu tahu RO mana pasangannya
                                   (pola yang sama dipakai menu PPN Masukan). */
                                /* Nomor &amp; tanggal dokumen RI ditaruh di bagian
                                   DEDUCTION RETURN - di situlah nilainya. Nomor RO-nya
                                   tidak perlu Doc No sendiri karena sudah jadi No BPB
                                   baris ini (permintaan pemakai 13 Nov 2026). */
                                $subRI = '<th style="text-align:center;">Doc No</th>'
                                       . '<th style="text-align:center;">Date</th>' . $sub;
                                $baris_kepala2 = str_repeat($sub, 3) . $subRI . str_repeat($sub, 4);
                            }else{
                                echo '<th style="text-align: center;vertical-align: middle;width: 9%;">No Memo</th>
                                <th style="text-align: center;vertical-align: middle;width: 8%;">Memo Date</th>
                                <th style="text-align: center;vertical-align: middle;width: 8%;">No Invoice Vendor</th>
                                <th style="text-align: center;vertical-align: middle;width: 9%;">Supplier</th>
                                <th style="text-align: center;vertical-align: middle;width: 9%;">Buyer</th>
                                <th style="text-align: center;vertical-align: middle;width: 8%;">No DN</th>
                                <th style="text-align: center;vertical-align: middle;width: 7%;">DN Date</th>
                                <th style="text-align: center;vertical-align: middle;width: 7%;">Begining Balance</th>
                                <th style="text-align: center;vertical-align: middle;width: 7%;">Addition</th>
                                <th style="text-align: center;vertical-align: middle;width: 7%;">Deduction (ALK)</th>
                                <th style="text-align: center;vertical-align: middle;width: 7%;">Deduction (GM)</th>
                                <th style="text-align: center;vertical-align: middle;width: 7%;">Forex Gain / (Loss)</th>
                                <th style="text-align: center;vertical-align: middle;width: 7%;">Ending Balance</th>';
                            }
                        }else{
                            echo '<th style="text-align: center;vertical-align: middle;width: 9%;">No Memo</th>
                            <th style="text-align: center;vertical-align: middle;width: 8%;">Memo Date</th>
                            <th style="text-align: center;vertical-align: middle;width: 8%;">No Invoice Vendor</th>
                            <th style="text-align: center;vertical-align: middle;width: 9%;">Supplier</th>
                            <th style="text-align: center;vertical-align: middle;width: 9%;">Buyer</th>
                            <th style="text-align: center;vertical-align: middle;width: 8%;">No DN</th>
                            <th style="text-align: center;vertical-align: middle;width: 7%;">DN Date</th>
                            <th style="text-align: center;vertical-align: middle;width: 7%;">Begining Balance</th>
                            <th style="text-align: center;vertical-align: middle;width: 7%;">Addition</th>
                            <th style="text-align: center;vertical-align: middle;width: 7%;">Deduction (ALK)</th>
                            <th style="text-align: center;vertical-align: middle;width: 7%;">Deduction (GM)</th>
                            <th style="text-align: center;vertical-align: middle;width: 7%;">Forex Gain / (Loss)</th>
                            <th style="text-align: center;vertical-align: middle;width: 7%;">Ending Balance</th>';
                        }
                        ?>
                    </tr>
                    <?php if (isset($baris_kepala2)) { echo '<tr class="thead-dark">'.$baris_kepala2.'</tr>'; } ?>
                </thead>
            <tbody>

            <?php
            $coa_number ='';
            $start_date ='';
            $end_date =''; 
            $date_now = date("Y-m-d");  
            $tanggal_awal = date("Y-m-d",strtotime($date_now ));
            $tanggal_akhir = date("Y-m-d",strtotime($date_now ));
            $bulan_awal = date("m",strtotime($date_now));
            $bulan_akhir = date("m",strtotime($date_now));  
            $tahun_awal = date("Y",strtotime($date_now));
            $tahun_akhir = date("Y",strtotime($date_now));
            $kata_awal = date("M",strtotime($date_now));
            $tengah = '_';
            $kata_akhir = date("Y",strtotime($date_now));
            $kata_filter = $kata_awal . $tengah . $kata_akhir;
            $kata_filter2 = $kata_awal . $tengah . $kata_akhir;          
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $coa_number = isset($_POST['coa_number']) ? $_POST['coa_number']: null;
                $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                $end_date = date("Y-m-d",strtotime($_POST['end_date']));   
                $tanggal_awal = date("Y-m-d",strtotime($_POST['start_date']));
                $tanggal_akhir = date("Y-m-d",strtotime($_POST['end_date'])); 

                $bulan_awal = date("m",strtotime($_POST['start_date']));
                $bulan_akhir = date("m",strtotime($_POST['end_date']));  
                $tahun_awal = date("Y",strtotime($_POST['start_date']));
                $tahun_akhir = date("Y",strtotime($_POST['end_date']));

                $kata_awal = date("M",strtotime($_POST['start_date']));
                $tengah = '_';
                $kata_akhir = date("Y",strtotime($_POST['start_date']));
                $kata_filter = $kata_awal . $tengah . $kata_akhir;         
            }

            if ($coa_number == '1.34.05') {
                /* ---------------------------------------------------------
                   BARIS RETUR DIPISAH JADI DUA KOLOM (13 Nov 2026, pemakai).

                   Selama ini BPB bernomor .../RI/... ikut masuk kolom
                   Addition. Sekarang dipisah: nilai RI-nya ke kolom
                   'Deduction Return', dan pasangan RO-nya ke kolom
                   'Addition Return' bertanda MINUS.

                   PASANGAN RI-RO lewat NOMOR SURAT JALAN, bukan nomor
                   dokumen: bpb.bppbno dicocokkan ke bppb.bppbno, lalu
                   bppb.bppbno_int itulah nomor RO-nya (cara ini diberi tahu
                   pemakai). Tabelnya bppb, BUKAN bppb_new - di bppb_new
                   pasangannya tidak ketemu satu pun.

                   NILAI PASANGANNYA SAMA PERSIS dlm mata uang aslinya,
                   jadi kedua kolom itu SALING MENIADAKAN. Memang begitu
                   kenyataannya: barang keluar lalu masuk lagi dgn nilai
                   yang sama.

                   KURSNYA PAKAI TANGGAL DOKUMENNYA MASING-MASING
                   (permintaan pemakai 13 Nov 2026): RI pakai kurs pajak
                   tanggal RI, RO pakai kurs pajak tanggal RO. Utk dokumen
                   USD yang tanggalnya beda, rupiahnya jadi tidak sama lagi
                   - SELISIHNYA MASUK KE KOLOM FOREX GAIN/(LOSS), jadi
                   Ending Balance-nya tetap nol. Dicek ke produksi Okt-Nov
                   2026: 18 pasang, total selisih 8.153,29.

                   BATAS 1 OKT 2026 (pemakai: 'September ke belakang jangan
                   diubah'). Penandanya TANGGAL BPB-nya, bukan periode yang
                   sedang dilihat, supaya laporan bulan lama tetap sama
                   persis - termasuk waktu dokumennya terbawa sbg saldo awal
                   periode berikutnya.
                   --------------------------------------------------------- */
                $eRetur = "(a.bpbno_int LIKE '%/RI/%' AND a.bpbdate >= '2026-10-01')";

                /* Baris retur yang jatuh di SALDO AWAL (tanggalnya sebelum
                   periode). Hanya baris ini yang pasangan RO-nya dipotong
                   lewat saldo_awal - baris periode berjalan sudah ditangani
                   dua kolom barunya, kalau ikut dipotong di sini RO-nya
                   terhitung DUA KALI. */
                $eReturAwal = "($eRetur AND a.bpbdate < '$start_date')";

                /* RI yang terjadi DI periode ini - dibedakan dari yang cuma
                   terbawa sbg saldo awal, supaya kolom returnya tidak terisi
                   lagi di periode-periode berikutnya. */
                $eReturIni = "($eRetur AND a.bpbdate >= '$start_date')";

                /* Nilai RO pasangan dlm rupiah. Pengkursannya dikerjakan di
                   dalam sambungannya, pakai KURS TANGGAL RO SENDIRI. */
                $eRO = "ROUND(COALESCE(ro.idr,0), 2)";

                /* Nilai RI-nya. Satu baris cuma mengisi salah satu kolom:
                   kalau RI-nya di periode ini isinya 'tambah', kalau terbawa
                   dari periode lalu isinya 'saldo_awal'. */
                $eNilaiRI = "ROUND(COALESCE(saldo_awal,0) + COALESCE(tambah,0), 2)";

                /* SELISIH KURS RI vs RO -> kolom Forex Gain/(Loss). Hanya
                   kalau pasangan RO-nya KETEMU; kalau tidak ketemu, RI-nya
                   dibiarkan menggantung supaya kelihatan - jangan disapu
                   diam-diam ke forex. */
                $eSelisih = "IF(ro.bppbno IS NULL, 0, ROUND($eNilaiRI - $eRO, 2))";

                $sql = mysqli_query($conn2,"SELECT pono, bpbno_int, bpbdate, supplier, buyer,no_req, tgl_req, no_dn, tgl_dn, curr, COALESCE(nilai_asli,0) nilai_asli, COALESCE(rate_doc,1) rate_doc, IF($eRetur, COALESCE(ro.asli,0), 0) nilai_asli_ro, IF($eRetur, COALESCE(ro.rate_ro,0), 0) rate_ro, IF($eRetur, COALESCE(ro.no_ro,'-'), '-') no_ro, IF($eRetur, COALESCE(ro.tgl_ro,'-'), '-') tgl_ro, round(IF($eReturAwal, COALESCE(saldo_awal,0) - $eRO - $eSelisih, COALESCE(saldo_awal,0)) - COALESCE(ded_alk_bfr,0) - COALESCE(tot_gm_before,0),2) saldo_awal, IF($eRetur, 0, COALESCE(tambah,0)) tambah, IF($eReturIni, -$eRO, 0) tambah_retur, IF($eReturIni, COALESCE(tambah,0), 0) ded_retur, COALESCE(ded_alk,0) ded_alk, COALESCE(tot_gm,0) ded_gm, round(COALESCE(ded_fgl,0) + IF($eReturIni, $eSelisih, 0),2) ded_fgl, round(IF($eReturAwal, COALESCE(saldo_awal,0) - $eRO - $eSelisih, COALESCE(saldo_awal,0)) - COALESCE(ded_alk_bfr,0)  - COALESCE(tot_gm_before,0) - COALESCE(tot_gm,0) + IF($eRetur, 0, COALESCE(tambah,0)) + IF($eReturIni, -$eRO, 0) + IF($eReturIni, COALESCE(tambah,0), 0) - (COALESCE(ded_alk,0) + COALESCE(ded_fgl,0) + IF($eReturIni, $eSelisih, 0)),2) saldo_akhir from (select a.pono, a.bpbno_int, a.bpbdate, b.supplier, buyer, curr, round(if(a.curr = 'IDR', (sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100))), (round(sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100)),4) * rate)),2) saldo_awal, 0 tambah, round(sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100)),4) nilai_asli, MAX(a.bppbno) bppbno, MAX(rt.rate) rate_doc from bpb a 
                    inner join mastersupplier b on b.id_supplier = a.id_supplier 
                    left join (select id_jo,kpno,styleno, b.supplier buyer from act_costing ac inner join so on ac.id=so.id_cost 
                        inner join jo_det jod on so.id=jod.id_so inner join mastersupplier b on b.id_supplier = ac.id_buyer  group by id_jo) c on c. id_jo = a.id_jo 
                                                INNER JOIN (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) rt on rt.tanggal = a.bpbdate
                    left join po_header ph on a.pono = ph.pono
                    left join po_header_draft phd on phd.id = ph.id_draft where phd.tipe_com = 'Buyer' and a.bpbdate > '2024-10-01' and a.bpbdate < '$start_date' and a.cancel != 'Y' and a.confirm = 'Y' GROUP BY a.bpbno_int
                    UNION
                    select a.pono, a.bpbno_int, a.bpbdate, b.supplier, buyer, curr, 0 saldo_awal, round(if(a.curr = 'IDR', (sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100))), (round(sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100)),4) * rate)),2) tambah, round(sum((qty - COALESCE(qty_reject,0)) * price) + (sum((qty - COALESCE(qty_reject,0)) * price) * (ph.ppn/ 100)),4) nilai_asli, MAX(a.bppbno) bppbno, MAX(rt.rate) rate_doc from bpb a 
                    inner join mastersupplier b on b.id_supplier = a.id_supplier 
                    left join (select id_jo,kpno,styleno, b.supplier buyer from act_costing ac inner join so on ac.id=so.id_cost 
                        inner join jo_det jod on so.id=jod.id_so inner join mastersupplier b on b.id_supplier = ac.id_buyer  group by id_jo) c on c. id_jo = a.id_jo 
                                        INNER JOIN (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) rt on rt.tanggal = a.bpbdate
                    left join po_header ph on a.pono = ph.pono
                    left join po_header_draft phd on phd.id = ph.id_draft where phd.tipe_com = 'Buyer' and bpbdate >= '2024-10-01' and a.bpbdate >= '$start_date' and a.bpbdate <= '$end_date' and a.cancel != 'Y' and a.confirm = 'Y' GROUP BY a.bpbno_int) a 
                LEFT JOIN
                (select no_bpb,curr_alk,curr_bpb,sum(if(curr_alk = curr_bpb,total_alk,total_alk2)) ded_alk, sum(COALESCE(fgl_fix, if(curr_alk = 'IDR' and curr_bpb = 'IDR',0,(tot_dn - total_alk2)))) ded_fgl from (select no_bpb, curr_alk, curr_bpb, total_alk,total_alk2,tot_dn,fgl_fix from (SELECT DISTINCT g.no_alk,g.tgl_alk, c.nm_memo no_bpb, g.curr curr_alk, h.curr curr_bpb, g.rate, IF(h.curr != 'IDR',(c.value * rate),value) tot_dn,c.amount amount_dn,(c.amount * g.rate) total_alk2,if(f.amount > c.amount,IF(e.from_curr = 'USD',(c.value * rate),c.value),(f.amount * rate)) total_alk, f.amount amount, IF(c.nm_memo IN ($OR_FX_BPB), ROUND(IF(f.amount > c.amount, c.amount, f.amount) * (COALESCE((select mr.rate from masterrate mr where mr.v_codecurr = 'PAJAK' and mr.curr = h.curr and mr.tanggal = h.bpbdate limit 1), g.rate) - g.rate), 2), NULL) fgl_fix from (select id,no_dn,SUM(value) value, SUM(amount) amount,nm_memo from tbl_debitnote_det where nm_memo != '' and id_memo_det = '' GROUP BY no_dn,nm_memo) c INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN (select * from tbl_alokasi_detail where coa = '1.34.05') f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk INNER JOIN (select bpbno_int,curr,min(bpbdate) bpbdate from bpb a inner join po_header b on b.pono = a.pono INNER JOIN po_header_draft c on c.id = b.id_draft where c.tipe_com = 'Buyer' and bpbdate >= '2024-10-01' GROUP BY bpbno_int) h on h.bpbno_int = c.nm_memo where g.tgl_alk BETWEEN '$start_date' and '$end_date' and g.status != 'Cancel' and c.nm_memo is not null  order by c.nm_memo asc) a) a GROUP BY no_bpb) b on b.no_bpb = a.bpbno_int
                LEFT JOIN
                (select no_bpb no_bpb_bfr, (ded_alk + ded_fgl) ded_alk_bfr from (select no_bpb,curr_alk,curr_bpb,sum(if(curr_alk = curr_bpb,total_alk,total_alk2)) ded_alk, sum(COALESCE(fgl_fix, if(curr_alk = 'IDR' and curr_bpb = 'IDR',0,(tot_dn - total_alk2)))) ded_fgl from (select no_bpb, curr_alk, curr_bpb, total_alk,total_alk2,tot_dn,fgl_fix from (SELECT DISTINCT g.no_alk,g.tgl_alk, c.nm_memo no_bpb, g.curr curr_alk, h.curr curr_bpb, g.rate, IF(h.curr != 'IDR',(c.value * rate),value) tot_dn,c.amount amount_dn,(c.amount * g.rate) total_alk2,if(f.amount > c.amount,IF(e.from_curr = 'USD',(c.value * rate),c.value),(f.amount * rate)) total_alk, f.amount amount, IF(c.nm_memo IN ($OR_FX_BPB), ROUND(IF(f.amount > c.amount, c.amount, f.amount) * (COALESCE((select mr.rate from masterrate mr where mr.v_codecurr = 'PAJAK' and mr.curr = h.curr and mr.tanggal = h.bpbdate limit 1), g.rate) - g.rate), 2), NULL) fgl_fix from (select id,no_dn,SUM(value) value, SUM(amount) amount,nm_memo from tbl_debitnote_det where nm_memo != '' and id_memo_det = '' GROUP BY no_dn,nm_memo) c INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN (select * from tbl_alokasi_detail where coa = '1.34.05') f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk INNER JOIN (select bpbno_int,curr,min(bpbdate) bpbdate from bpb a inner join po_header b on b.pono = a.pono INNER JOIN po_header_draft c on c.id = b.id_draft where c.tipe_com = 'Buyer' and bpbdate >= '2024-10-01' GROUP BY bpbno_int) h on h.bpbno_int = c.nm_memo where g.tgl_alk < '$start_date' and g.status != 'Cancel' and c.nm_memo is not null  order by c.nm_memo asc) a) a GROUP BY no_bpb) a) c on c.no_bpb_bfr = a.bpbno_int
                LEFT JOIN
                (select reff_doc, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm from tbl_list_journal where no_coa = '1.34.05' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' GROUP BY reff_doc) d on d.reff_doc = a.bpbno_int
                LEFT JOIN
                (select reff_doc reff_before, if(curr = 'IDR',sum((credit * rate) - (debit * rate)),(credit - debit)) tot_gm_before from tbl_list_journal where no_coa = '1.34.05' and no_journal like '%GM/%' and tgl_journal >= '2024-10-01' and tgl_journal < '$start_date' GROUP BY reff_doc) e on e.reff_before = a.bpbno_int
                /* DOKUMEN RO pasangan baris RI, dicari lewat nomor surat
                   jalannya. TIDAK disaring periode: pasangan RO-RI satu
                   kejadian, dan tanggalnya bisa beda bulan (mis.
                   GACC/RI/1126/07004 pasangannya GACC/RO/1026/14182).
                   Kalau RO-nya ikut disaring periode, pasangannya bisa
                   lepas dan barisnya tidak lagi net nol.
                   Dikelompokkan per bppbno supaya tidak menggandakan baris.
                   Rupiahnya dihitung DI SINI, per baris RO, supaya tiap RO
                   memakai kurs pajak tanggalnya sendiri. */
                LEFT JOIN
                (select x.bppbno, GROUP_CONCAT(DISTINCT x.bppbno_int) no_ro,
                        MIN(x.bppbdate) tgl_ro,
                        ROUND(SUM(x.qty * x.price * (1 + COALESCE(x.ppn,0)/100)),4) asli,
                        MAX(r2.rate) rate_ro,
                        ROUND(SUM(IF(x.curr = 'IDR',
                                     x.qty * x.price * (1 + COALESCE(x.ppn,0)/100),
                                     x.qty * x.price * (1 + COALESCE(x.ppn,0)/100) * COALESCE(r2.rate,1))),2) idr
                 from bppb x
                 left join (select tanggal, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) r2
                        on r2.tanggal = x.bppbdate
                 where x.cancel = 'N' and x.bppbno_int like '%/RO/%'
                 group by x.bppbno) ro on ro.bppbno = a.bppbno
                LEFT JOIN
                /* No Req & No DN per BPB. Dikelompokkan per (BPB, request) supaya 1 BPB yang diminta di beberapa RQDN tampil semuanya, bukan cuma 1. Nomor BPB dibersihkan dulu dari spasi/tab/enter yang kebawa dari alat pindai - TRIM saja TIDAK cukup, MySQL cuma mengabaikan SPASI di belakang, tab tetap bikin tidak ketemu. '-' = request ada tapi DN-nya belum terbit. */ (select GROUP_CONCAT(no_req ORDER BY no_req SEPARATOR ', ') no_req, GROUP_CONCAT(tgl_req ORDER BY no_req SEPARATOR ', ') tgl_req, GROUP_CONCAT(COALESCE(no_dn,'-') ORDER BY no_req SEPARATOR ', ') no_dn, GROUP_CONCAT(COALESCE(tgl_dn,'-') ORDER BY no_req SEPARATOR ', ') tgl_dn, no_bpb from (select no_req, tgl_req, no_dn, tgl_dn, no_bpb from (select a.no_req, a.tgl_req, c.no_dn, c.tgl_dn, replace(replace(replace(trim(b.no_bpb),char(9),''),char(13),''),char(10),'') no_bpb from req_dn_h a left join req_dn b on b.no_req = a.no_req left join tbl_debitnote_h c on c.no_dn = a.no_dn where COALESCE(a.status,'') != 'Cancel' and b.no_bpb is not null) x where no_bpb not in ('','-') GROUP BY no_bpb, no_req) a GROUP BY no_bpb ) f on f.no_bpb = trim(a.bpbno_int)");
/* Satu bagian uang = tiga sel: mata uang, nilai di mata uang aslinya (OCY),
   dan setaranya dlm rupiah. Dipakai berulang supaya baris & totalnya seragam. */
if (!function_exists('orGrup')) {
    function orGrup($curr, $ocy, $idr) {
        $des = ($curr == 'IDR') ? 2 : 4;
        return '<td style="text-align:center;">'.$curr.'</td>'
             . '<td style="text-align:right;" value = "'.$ocy.'">'.number_format($ocy,$des).'</td>'
             . '<td style="text-align:right;" value = "'.$idr.'">'.number_format($idr,2).'</td>';
    }
    /* Di baris Total, Amount OCY TIDAK dijumlahkan: satu laporan bisa berisi
       USD & IDR sekaligus, jadi jumlahnya tidak ada artinya (cara yang sama
       dipakai menu PPN Masukan). */
    /* Nilai rupiah dibalik ke mata uang asli dokumennya. Dokumen rupiah
       tidak perlu dibagi apa-apa. */
    function orOcy($curr, $kurs, $idr) {
        return ($curr == 'IDR') ? $idr : round($idr / $kurs, 4);
    }

    function orGrupTotal($idr) {
        return '<th></th><th style="text-align:center;">-</th>'
             . '<th style="text-align:right;" value = "'.$idr.'">'.number_format($idr,2).'</th>';
    }
}
$ttl_beg =0;
$ttl_add =0;
$ttl_add_ret =0;
$ttl_ded_ret =0;
$ttl_ded =0;
$ttl_for =0;
$ttl_end =0;
$ttl_mj =0;
$sisa_dn = 0;
$sisa_dn_bfr = 0;
$ost_dn = 0;
while($row2 = mysqli_fetch_array($sql)){
    $saldo_awal = isset($row2['saldo_awal']) ? $row2['saldo_awal'] : 0;
    $tambah = isset($row2['tambah']) ? $row2['tambah'] : 0;
    /* Dua kolom retur: nilai RI-nya (Deduction Return) dan nilai RO
       pasangannya yang bertanda minus (Addition Return). */
    $tambah_retur = isset($row2['tambah_retur']) ? $row2['tambah_retur'] : 0;
    $ded_retur = isset($row2['ded_retur']) ? $row2['ded_retur'] : 0;
    /* Nilai & kurs dlm mata uang asli dokumennya, supaya angka rupiah di
       kanan bisa ditelusuri: Addition = Amount (Orig) x Rate. */
    $nilai_asli = isset($row2['nilai_asli']) ? $row2['nilai_asli'] : 0;
    $rate_doc = isset($row2['rate_doc']) ? $row2['rate_doc'] : 1;
    $nilai_asli_ro = isset($row2['nilai_asli_ro']) ? $row2['nilai_asli_ro'] : 0;
    $rate_ro = isset($row2['rate_ro']) ? $row2['rate_ro'] : 0;
    $cr = isset($row2['curr']) ? $row2['curr'] : 'IDR';

    /* ---- nomor dokumen yang dipakai baris ini ----
       Utk baris retur, yang jadi identitas baris adalah dokumen RO-nya
       (permintaan pemakai: &#39;no bpb nya isi nomor RO saja&#39;), sedangkan
       nomor RI-nya tampil di bagian Deduction Return - tempat nilainya.
       Kalau pasangan RO-nya tidak ketemu, nomor BPB-nya dibiarkan apa adanya
       supaya barisnya tidak jadi tanpa nama. */
    $ada_ro = (isset($row2['no_ro']) && $row2['no_ro'] != '-' && $row2['no_ro'] != '');
    $no_bpb_tampil  = $ada_ro ? $row2['no_ro']  : $row2['bpbno_int'];
    $tgl_bpb_tampil = $ada_ro ? $row2['tgl_ro'] : $row2['bpbdate'];
    $no_ri_tampil   = $ada_ro ? $row2['bpbno_int'] : '-';
    $tgl_ri_tampil  = $ada_ro ? $row2['bpbdate']   : '-';
    $ded_alk = isset($row2['ded_alk']) ? $row2['ded_alk'] : 0;
    $ded_gm = isset($row2['ded_gm']) ? $row2['ded_gm'] : 0;
    $ded_fgl = isset($row2['ded_fgl']) ? $row2['ded_fgl'] : 0;
    $saldo_akhir = isset($row2['saldo_akhir']) ? $row2['saldo_akhir'] : 0;

    /* BPB yang selisih kursnya diakui sbg Forex Gain/(Loss).
       Dipakai SISA SEBENARNYA, bukan selisih kurs yang dihitung ulang:
       saldo awal BPB tidak selalu sama persis dgn amount DN x kurs pajak,
       dan perbedaan kecil itu menyisakan angka di Ending Balance.
       Deduction (GM) ikut dikurangkan, jadi kalau selisihnya sudah
       ditutup lewat jurnal GM, Forex-nya jadi 0 dgn sendirinya - tidak
       pernah terhitung dua kali. */
    /* PENJAGA: hanya kalau BPB itu MEMANG dialokasi pada periode ini.
       Tanpa penjaga ini, pada periode SEBELUM alokasinya terjadi seluruh
       saldo BPB ikut tersapu ke Forex dan Ending-nya jadi nol - padahal
       saat itu saldonya memang masih menggantung. Ketahuan saat menguji
       periode Jun-Agu 2026 ke data produksi. */
    if ($ded_alk != 0 && in_array($row2['bpbno_int'], $OR_FX_ARR)) {
        $ded_fgl = round($saldo_awal + $tambah - $ded_alk - $ded_gm, 2);
        $saldo_akhir = 0;
    }


    $ttl_beg +=$saldo_awal;
    $ttl_add +=$tambah;
    $ttl_add_ret +=$tambah_retur;
    $ttl_ded_ret +=$ded_retur;
    $ttl_ded +=$ded_alk;
    $ttl_mj  +=$ded_gm;
    $ttl_for +=$ded_fgl;
    $ttl_end +=$saldo_akhir;

    /* ---- nilai tiap bagian dlm MATA UANG ASLI dokumennya ----
       Tiga bagian punya angka asli yang memang tersimpan: Addition &
       Deduction Return dari nilai BPB-nya, Addition Return dari dokumen RO
       pasangannya. Sisanya (saldo awal, ALK, GM, saldo akhir) tidak punya
       angka valas tersendiri - dibalik dari rupiahnya dibagi kurs BPB,
       jadi OCY x Rate selalu sama persis dgn Eqv IDR-nya.
       Forex selalu rupiah: isinya memang SELISIH kurs. */
    $kurs = ($rate_doc > 0) ? $rate_doc : 1;
    $beg_ocy    = orOcy($cr, $kurs, $saldo_awal);
    $add_ocy    = ($tambah == 0) ? 0 : $nilai_asli;
    $addret_ocy = ($tambah_retur == 0) ? 0 : -$nilai_asli_ro;
    $dedret_ocy = ($ded_retur == 0) ? 0 : $nilai_asli;
    $alk_ocy    = orOcy($cr, $kurs, $ded_alk);
    $gm_ocy     = orOcy($cr, $kurs, $ded_gm);
    $end_ocy    = orOcy($cr, $kurs, $saldo_akhir);

    /* Baris retur punya saldo awal & Addition 0 - angkanya ada di dua kolom
       retur. Tanpa dua syarat tambahan ini barisnya ikut tersembunyi. */
    if ($saldo_awal == 0 && $tambah == 0 && $tambah_retur == 0 && $ded_retur == 0) {
            // code...
    }else{
        echo ' <tr style="font-size:12px;text-align:center;">
        <td style="text-align : left;" value = "'.$row2['pono'].'">'.$row2['pono'].'</td>
        <td style="text-align : left;" value = "'.$no_bpb_tampil.'">'.$no_bpb_tampil.'</td>
        <td style="width: 100px;" value = "'.$tgl_bpb_tampil.'">'.date("d-M-Y",strtotime($tgl_bpb_tampil)).'</td>
        <td style="text-align : left;" value = "'.$row2['supplier'].'">'.$row2['supplier'].'</td>
        <td style="text-align : left;" value = "'.$row2['buyer'].'">'.$row2['buyer'].'</td>
        <td style="text-align : left;" value = "'.$row2['no_req'].'">'.$row2['no_req'].'</td>
        <td style="text-align : left;" value = "'.$row2['tgl_req'].'">'.$row2['tgl_req'].'</td>
        <td style="text-align : left;" value = "'.$row2['no_dn'].'">'.$row2['no_dn'].'</td>
        <td style="text-align : left;" value = "'.$row2['tgl_dn'].'">'.$row2['tgl_dn'].'</td>
        <td style="text-align : right;" value = "'.$rate_doc.'">'.number_format($rate_doc,2).'</td>'
        . orGrup($cr, $beg_ocy, $saldo_awal)
        . orGrup($cr, $add_ocy, $tambah)
        . orGrup($cr, $addret_ocy, $tambah_retur)
        . '<td style="text-align:left;">'.$no_ri_tampil.'</td>'
        . '<td>'.($tgl_ri_tampil == '-' ? '-' : date("d-M-Y", strtotime($tgl_ri_tampil))).'</td>'
        . orGrup($cr, $dedret_ocy, $ded_retur)
        . orGrup($cr, $alk_ocy, $ded_alk)
        . orGrup($cr, $gm_ocy, $ded_gm)
        . orGrup('IDR', $ded_fgl, $ded_fgl)
        . orGrup($cr, $end_ocy, $saldo_akhir)
        . '</tr>
        ';
    }
}

$baris_total = ' <tr >
<th colspan="10" style="text-align : center;">Total</th>'
. orGrupTotal($ttl_beg)
. orGrupTotal($ttl_add)
. orGrupTotal($ttl_add_ret)
. '<th></th><th></th>'
. orGrupTotal($ttl_ded_ret)
. orGrupTotal($ttl_ded)
. orGrupTotal($ttl_mj)
. orGrupTotal($ttl_for)
. orGrupTotal($ttl_end)
. '</tr>
';

}else{
    $sql = mysqli_query($conn2,"SELECT nm_memo,tgl_memo,no_invoice,supplier,buyer,no_dn,tgl_dn,IF(tgl_memo < '$start_date',total_memo,0) beg_balance, IF(tgl_memo >= '$start_date',total_memo,0) addition,total_alk deduction, total_alk2 deduction2,total_dn,total_dn_alk,total_dn_alk2,sisa_dn,sisa_dn_bfr,(total_dn - total_dn_alk - total_dn_alk2) ost_dn FROM (SELECT a.nm_memo,tgl_memo,no_invoice,supplier,buyer,if(sisa_dn_bfr > 1,ROUND(total_memo - COALESCE(val_alk_bfr,0),2),total_memo) total_memo,IF(no_dn is null,'-',no_dn) no_dn,IF(tgl_dn is null,'-',tgl_dn) tgl_dn,COALESCE(val_memodn,0) total_memo_dn,COALESCE(b.rate,1) rate_dn, COALESCE(val_dn,0) total_dn,IF(no_alk is null,'-',no_alk) no_alk,IF(tgl_alk is null,'-',tgl_alk) tgl_alk,COALESCE(val_dn_alk,0) total_dn_alk,COALESCE(c.rate,1) rate_alk, COALESCE(val_alk,0) total_alk,IF(no_alk_bfr is null,'-',no_alk_bfr) no_alk_bfr,IF(tgl_alk_bfr is null,'-',tgl_alk_bfr) tgl_alk_bfr,COALESCE(val_dn_alk_bfr,0) total_dn_alk2,COALESCE(d.rate_bfr,1) rate_alk2, COALESCE(val_alk_bfr,0) total_alk2,sisa_dn,sisa_dn_bfr FROM (
        SELECT nm_memo, tgl_memo,no_invoice,supplier,buyer,total_memo from (SELECT b.id,a.id_supplier,d.supplier,g.supplier buyer,a.nm_memo, a.tgl_memo,IF(b.inv_vendor is null,'-',b.inv_vendor) no_invoice,'IDR' curr,sum(b.biaya * IFNULL(rate,1)) total_memo,'' id_book from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join mastersupplier g on g.id_supplier =a.id_buyer inner join memo_det b on b.id_h = a.id_h left JOIN (select tanggal, rate, curr from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) rt on rt.tanggal = a.tgl_memo and rt.curr = a.curr where  a.nm_memo >= 'MEMO/NAG/2310/01039' and a.tgl_memo BETWEEN '2023-01-01' and '$end_date' and a.ditagihkan = 'Y' and b.cancel = 'N' and a.status NOT IN ('DRAFT','CANCEL') and b.nm_sub_ctg != 'VAT' GROUP BY a.nm_memo order by a.nm_memo asc ) a) a left join

    (SELECT no_dn,tgl_dn,sum(value) val_memodn,rate,SUM(amount) val_dn,nm_memo from (SELECT DISTINCT b.id,c.no_dn,e.tgl_dn,c.value,c.rate,c.amount,c.nm_memo from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join memo_det b on b.id_h = a.id_h INNER JOIN tbl_debitnote_det c on c.id_memo_det = b.id INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn where a.tgl_memo BETWEEN '2023-01-01' and '$end_date' and b.cancel = 'N' AND (
    b.nm_sub_ctg != 'VAT'
    OR a.nm_memo = 'MEMO/NAG/2402/01544'
) and e.status != 'Cancel' and c.nm_memo is not null order by a.nm_memo asc) a GROUP BY nm_memo) b on b.nm_memo = a.nm_memo left join

    (select sisa_dn,a.no_alk,a.tgl_alk,sum(IF(sisa_dn > 1,b.value,a.val_alk)) val_alk,a.rate,SUM(IF(sisa_dn > 1,b.amount,a.val_dn_alk)) val_dn_alk,a.nm_memo from (SELECT no_alk,tgl_alk,sum(value) val_alk,rate,SUM(amount) val_dn_alk,nm_memo from (SELECT DISTINCT b.id,g.no_alk,g.tgl_alk,(g.rate * c.amount) value,g.rate,c.amount,c.nm_memo from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join memo_det b on b.id_h = a.id_h INNER JOIN tbl_debitnote_det c on c.id_memo_det = b.id INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN tbl_alokasi_detail f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk where g.tgl_alk BETWEEN '$start_date' and '$end_date' and b.cancel = 'N' and b.nm_sub_ctg != 'VAT' and g.status != 'Cancel' and c.nm_memo is not null order by a.nm_memo asc) a GROUP BY nm_memo) a left join 
        (SELECT DISTINCT c.id,g.no_alk,g.tgl_alk,f.eqp_idr value,g.rate,f.amount,c.amount amount_dn,ROUND(c.amount - f.amount,2) sisa_dn,c.nm_memo from (select id,no_dn,SUM(value) value, SUM(amount) amount,nm_memo from tbl_debitnote_det GROUP BY nm_memo) c  INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN tbl_alokasi_detail f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk where g.tgl_alk BETWEEN '$start_date' and '$end_date' and g.status != 'Cancel' and c.nm_memo is not null order by c.nm_memo asc
            ) b on b.nm_memo = a.nm_memo GROUP BY a.nm_memo ) c on c.nm_memo = a.nm_memo left join

    (select sisa_dn sisa_dn_bfr,a.no_alk no_alk_bfr,a.tgl_alk tgl_alk_bfr,sum(IF(sisa_dn > 1,b.value,a.val_alk)) val_alk_bfr,a.rate rate_bfr,SUM(IF(sisa_dn > 1,b.amount,a.val_dn_alk)) val_dn_alk_bfr,a.nm_memo nm_memo_bfr from (SELECT no_alk,tgl_alk,sum(value) val_alk,rate,SUM(amount) val_dn_alk,nm_memo from (SELECT DISTINCT b.id,g.no_alk,g.tgl_alk,(g.rate * c.amount) value,g.rate,c.amount,c.nm_memo from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join memo_det b on b.id_h = a.id_h INNER JOIN tbl_debitnote_det c on c.id_memo_det = b.id INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN tbl_alokasi_detail f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk where g.tgl_alk < '$start_date' and b.cancel = 'N' and b.nm_sub_ctg != 'VAT' and g.status != 'Cancel' and c.nm_memo is not null order by a.nm_memo asc) a GROUP BY nm_memo) a left join 
        (SELECT DISTINCT c.id,g.no_alk,g.tgl_alk,f.eqp_idr value,g.rate,f.amount,c.amount amount_dn,ROUND(c.amount - f.amount,2) sisa_dn,c.nm_memo from (select id,no_dn,SUM(value) value, SUM(amount) amount,nm_memo from tbl_debitnote_det GROUP BY nm_memo) c  INNER JOIN tbl_debitnote_h e on e.no_dn = c.no_dn INNER JOIN tbl_alokasi_detail f on f.no_ref = e.no_dn INNER JOIN tbl_alokasi g on g.no_alk = f.no_alk where g.tgl_alk < '$start_date'and g.status != 'Cancel' and c.nm_memo is not null order by c.nm_memo asc
    ) b on b.nm_memo = a.nm_memo GROUP BY a.nm_memo) d on d.nm_memo_bfr = a.nm_memo) a");

$ttl_beg =0;
$ttl_add =0;
$ttl_ded =0;
$ttl_for =0;
$ttl_end =0;
$ttl_mj =0;
$sisa_dn = 0;
$sisa_dn_bfr = 0;
$ost_dn = 0;
while($row2 = mysqli_fetch_array($sql)){
    $nm_memo = $row2['nm_memo'];
    $sisa_dn = isset($row2['sisa_dn']) ? $row2['sisa_dn'] : 0;
    $sisa_dn_bfr = isset($row2['sisa_dn_bfr']) ? $row2['sisa_dn_bfr'] : 0;
    if ($row2['nm_memo'] != 'MEMO/NAG/2402/01544') {
      $sisa_dn = $sisa_dn;
      $sisa_dn_bfr = $sisa_dn_bfr;
    }else{
      $sisa_dn = 0;
      $sisa_dn_bfr = 0;
    }
    $ost_dn = isset($row2['ost_dn']) ? $row2['ost_dn'] : 0;

    $sql_mj_b = mysqli_query($conn2,"select no_journal,tgl_journal,sum((credit * rate) - (debit * rate)) total, rate,((credit * rate) - (debit * rate)) total2, reff_doc from tbl_list_journal where no_coa = '1.34.04' and no_journal like '%GM/%' and tgl_journal < '$start_date' and reff_doc = '$nm_memo'");
    $row_mj_b = mysqli_fetch_array($sql_mj_b);
    $val_mj_b = isset($row_mj_b['total']) ? $row_mj_b['total'] : 0;

    if ($val_mj_b != 0) {
      if ($row2['nm_memo']== 'MEMO/NAG/2402/01544' && $start_date >= '2026-06-01') {
        $beg_balance = 0; 
      }else{
        $beg_balance = $row2['beg_balance'] - $val_mj_b;
      }
 }else{
     if ($row2['nm_memo']== 'MEMO/NAG/2402/01544' && $start_date >= '2026-06-01') {
        $beg_balance = 0; 
      }else{
        $beg_balance = $row2['beg_balance'];
      }
 }

        // $beg_balance = $row2['beg_balance'];
 $addition = $row2['addition'];
 $deduction = $row2['deduction'];
 $deduction2 = $row2['deduction2'];

 $sql_mj = mysqli_query($conn2,"select no_journal,tgl_journal,sum((credit * rate) - (debit * rate)) total, rate,((credit * rate) - (debit * rate)) total2, reff_doc from tbl_list_journal where no_coa = '1.34.04' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' and reff_doc = '$nm_memo'");
 $row_mj = mysqli_fetch_array($sql_mj);
 $val_mj = isset($row_mj['total']) ? $row_mj['total'] : 0;

 if ($deduction > 0 && $sisa_dn < 1) {
    if ($beg_balance > 0 && $sisa_dn_bfr < 1) {
        $forex = $beg_balance - $deduction - $val_mj;
    }else{
        $forex = $addition - $deduction - $val_mj;
    }
}else{
    $forex = 0;
}


  $end_balance = $beg_balance + $addition - $deduction - $val_mj - $forex; 


if ($deduction2 > 0 and $ost_dn < 1 and $row2['nm_memo'] != 'MEMO/NAG/2402/01544') {
}else{
    $ttl_beg +=$beg_balance;
    $ttl_add +=$addition;
    $ttl_ded +=$deduction;
    $ttl_for +=$forex;
    $ttl_end +=$end_balance;
    $ttl_mj  +=$val_mj;

    if ($beg_balance == 0 && $addition == 0) {
            // code...
    }else{
        echo ' <tr style="font-size:12px;text-align:center;">
        <td style="text-align : left;" value = "'.$row2['nm_memo'].'">'.$row2['nm_memo'].'</td>
        <td style="width: 100px;" value = "'.$row2['tgl_memo'].'">'.date("d-M-Y",strtotime($row2['tgl_memo'])).'</td>
        <td style="text-align : left;" value = "'.$row2['no_invoice'].'">'.$row2['no_invoice'].'</td>
        <td style="text-align : left;" value = "'.$row2['supplier'].'">'.$row2['supplier'].'</td>
        <td style="text-align : left;" value = "'.$row2['buyer'].'">'.$row2['buyer'].'</td>
        <td style="text-align : left;" value = "'.$row2['no_dn'].'">'.$row2['no_dn'].'</td>
        <td style="text-align : left;" value = "'.$row2['tgl_dn'].'">'.$row2['tgl_dn'].'</td>
        <td style="text-align : right;" value = "'.$beg_balance.'">'.number_format($beg_balance,2).'</td>
        <td style="text-align : right;" value = "'.$row2['addition'].'">'.number_format($row2['addition'],2).'</td>
        <td style="text-align : right;" value = "'.$row2['deduction'].'">'.number_format($row2['deduction'],2).'</td>
        <td style="text-align : right;" value = "'.$val_mj.'">'.number_format($val_mj,2).'</td>
        <td style="text-align : right;" value = "'.$forex.'">'.number_format($forex,2).'</td>
        <td style="text-align : right;" value = "'.$end_balance.'">'.number_format($end_balance,2).'</td>
        </tr>
        ';
    }
}
}

$baris_total = ' <tr >
<th colspan="7" style="text-align : center;" value = "Total">Total</td>
<th style="text-align : right;" value = "'.$ttl_beg.'">'.number_format($ttl_beg,2).'</th>
<th style="text-align : right;" value = "'.$ttl_add.'">'.number_format($ttl_add,2).'</th>
<th style="text-align : right;" value = "'.$ttl_ded.'">'.number_format($ttl_ded,2).'</th>
<th style="text-align : right;" value = "'.$ttl_mj.'">'.number_format($ttl_mj,2).'</th>
<th style="text-align : right;" value = "'.$ttl_for.'">'.number_format($ttl_for,2).'</th>
<th style="text-align : right;" value = "'.$ttl_end.'">'.number_format($ttl_end,2).'</th>
</tr>
';

}

?>  
</tbody>
<?php
/* Baris Total ditaruh di <tfoot>, BUKAN di dalam <tbody>: kalau ikut di
   dalam isi, DataTables memperlakukannya sbg data biasa - ikut terurut,
   ikut tersaring, dan cuma muncul di halaman terakhir. */
if (isset($baris_total)) { echo '<tfoot>'.$baris_total.'</tfoot>'; }
?>
</table>
</div>                  
</div>

</div>
</div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div style="width:450px;" class="modal-dialog modal-md">
            <div style="height: 225px" class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                    <h4 class="modal-title" id="Heading" style="text-align: center;"><b>UPLOAD</b></h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <form method="post" enctype="multipart/form-data" action="proses_upload.php">
                        Pilih File:
                        <input class="form-control" name="fileexcel" type="file" required="required">
                        <br>
                        <button class="btn btn-sm btn-info" type="submit">Submit</button>
                        <a target="_blank" href="format_upload_mj.xls"><button type="button" class="btn btn-warning "><i class="fa fa-file-excel-o" aria-hidden="true"> Format Upload</i></button></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_bpb"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
<!--           <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
  <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
  <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
  <!--         <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                    
  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
</div>
</div>
</div>
    <!-- /.modal-content 
  </div>
      /.modal-dialog 
  </div> -->         

</div><!-- body-row END -->
</div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min.js"></script>
<script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide'); 

// Collapse/Expand icon
$('#collapse-icon').addClass('fa-angle-double-left'); 

// Collapse click
$('[data-toggle=sidebar-colapse]').click(function() {
    SidebarCollapse();
});

function SidebarCollapse () {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
    
    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }
    
    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>
<!-- <script>
    $(document).ready(function() {
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script> -->

<script>
/* Tabel laporan: dipecah 10 baris per halaman supaya tidak perlu digulung
   panjang, dan bisa dicari/diurutkan tanpa memuat ulang halaman. Baris
   Total ada di <tfoot> jadi tidak ikut terpecah. */
$(document).ready(function () {
    $('#form-data').on('submit', function () {
        $('#or-loading').css('display', 'flex');
    });

    var tabel = $('#mytable2');
    if (tabel.length && tabel.find('tbody tr').length > 0) {
        tabel.DataTable({
            'pageLength': 10,
            'lengthMenu': [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            'order': [],
            'autoWidth': false,
            /* Length+Search di atas, Info+Pagination di bawah, dan DI LUAR
               kotak gulung - hanya 't' (tabelnya) yang masuk .app-dt-scroll,
               jadi kontrolnya tetap di tempat walau tabel digeser ke kanan.
               Susunan ini disalin dari menu VAT In. */
            'dom': "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                   "<'app-dt-scroll't>" +
                   "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",
            'language': {
                'search': 'Search:',
                'lengthMenu': 'Show _MENU_ rows',
                'info': 'Row _START_ to _END_ of _TOTAL_',
                'infoEmpty': 'No data',
                'emptyTable': 'No data available',
                'zeroRecords': 'No matching rows found'
            }
        });
        /* Dua kolom pertama (No PO & No BPB) menempel saat digulung ke kanan.
           Offset 'left' harus dihitung dari lebar sebenarnya - cara yang sama
           dipakai menu VAT In. Dihitung ulang tiap gambar & saat layar diubah. */
        var bekukan = function () {
            var $hdr = $('#mytable2 thead tr').first().children('th').slice(0, 2);
            if (!$hdr.length) { return; }
            /* PENJAGA: hanya kalau selnya memang position:sticky. Tanpa ini,
               'left' tetap berlaku pada sel position:relative (DataTables memang
               memberi relative ke tiap <th> utk ikon urutannya) - selnya tergeser
               ke kanan dan judulnya seolah hilang. Itu yang terjadi pada
               tampilan 13 kolom: kepala 'Memo Date' tergeser 152px. */
            if ($hdr.css('position') !== 'sticky') { return; }
            var kiri = 0, offs = [];
            $hdr.each(function () { offs.push(kiri); kiri += $(this).outerWidth(); });
            $hdr.each(function (i) { $(this).css('left', offs[i] + 'px'); });
            $('#mytable2 tbody tr').each(function () {
                $(this).children('td').slice(0, 2).each(function (i) { $(this).css('left', offs[i] + 'px'); });
            });
        };
        tabel.DataTable().on('draw.dt', bekukan);
        bekukan();
        $(window).on('resize', bekukan);
    }
    $('#or-loading').hide();
});
</script>

<script>
    $(document).ready(function() {
        $('#mytable').dataTable({
            'order': [1, 'asc'],
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false
        });

        $("[data-toggle=tooltip]").tooltip();

    } );

</script>

<script>
    function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("datatable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
    } else {
        tr[i].style.display = "none";
    }
}
}
}
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            startDate : "01-01-2023",
            autoclose:true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script type="text/javascript">
    $("#form-data").on("click", "#co_sal", function(){ 
        var no_coa = $(this).closest('tr').find('td:eq(1)').attr('value');
        var beg_balance = $(this).closest('tr').find('td:eq(7)').attr('value');
        var debit = $(this).closest('tr').find('td:eq(8)').attr('value');
        var credit = $(this).closest('tr').find('td:eq(9)').attr('value');
        var end_balance = $(this).closest('tr').find('td:eq(10)').attr('value');
        var copy_user = '<?php echo $user ?>';
        var to_saldo = document.getElementById('to_saldo').value;

        $.ajax({
            type:'POST',
            url:'copy_saldo_tb.php',
            data: {'no_coa':no_coa, 'beg_balance':beg_balance,'debit':debit, 'credit':credit,'end_balance':end_balance, 'copy_user':copy_user,'to_saldo':to_saldo},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                // alert(response);            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
           }
       });
        alert("Copy Saldo successfully");     
    });
</script>


<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_ib = $(this).closest('tr').find('td:eq(0)').attr('value');
    var date = $(this).closest('tr').find('td:eq(1)').text();
    var reff = $(this).closest('tr').find('td:eq(2)').attr('value');
    var reff_doc = $(this).closest('tr').find('td:eq(3)').attr('value');
    var oth_doc = $(this).closest('tr').find('td:eq(4)').attr('value');
    var curr = "IDR";

    $.ajax({
    type : 'post',
    url : 'ajax_cashin.php',
    data : {'no_ib': no_ib},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_ib);
    $('#txt_tglbpb').html('Date : ' + date + '');
    $('#txt_no_po').html('Refference : ' + reff + '');
    $('#txt_supp').html('Refference Document : ' + reff_doc + '');
    // $('#txt_top').html('Other Document : ' + oth_doc + '');
    // $('#txt_curr').html('Kas Account : ' + akun + '');        
    $('#txt_confirm').html('Currency : ' + curr + '');
    // $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create-list-journal.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('btnupload').onclick = function () {
        location.href = "upload-list-journal.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "list-journal.php";
    };
</script>

<!-- <script type="text/javascript">     
    document.getElementById('btnupload').onclick = function (){ 
    // var txt_type = $(this).closest('tr').find('td:eq(4)').attr('value'); 
    // var txt_id = $(this).closest('tr').find('td:eq(0)').attr('value');           
    $('#mymodal2').modal('show');
    // $('#txt_type').val(txt_type);
    // $('#txt_id').val(txt_id);

};

</script> -->

<script>
    function alert_cancel() {
      alert("Master Bank Deactive");
      location.reload();
  }
  function alert_approve() {
      alert("Master Bank Active");
      location.reload();
  }
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>