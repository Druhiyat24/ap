<?php
session_start();
include '../conn/conn.php';
$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user == '') {
    $script = "<script>
    window.location = 'function/logout.php';</script>";
    echo $script;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        img {
          display: block;
          margin-left: auto;
          margin-right: auto;
          height: 30px;
      }
      .box {
          border-style: outset;
          box-sizing: border-box;
      }
      .body {
        font-size: 14px;
    }
    .box .header {
        font-size: 14px;
    }
    .form-control-plaintext {
      border: 1px solid grey;
  }
  .form-row {
      margin-right: 0;
      margin-left: -10px;
  }
  .filter-option {
    font-size: 12px;
}
.datatable_wrapper{
    font-size: 12px;
}

.container-1 input#myInput{
  width: 220px;
  height: 32px;
  position: relative;
  background: white;
  font-size: 12pt;
  float: right;
  color: #63717f;
  padding-left: 15px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
}


/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.tableFix { /* Scrollable parent element */
  position: relative;
  overflow: auto;
  height: 100px;
}

.tableFix table{
  width: 100%;
  border-collapse: collapse;
}

.tableFix th,
.tableFix td{
  padding: 8px;
  text-align: left;
}

.tableFix thead th {
  position: sticky;  /* Edge, Chrome, FF */
  top: 0px;
  background: #F0F8FF;  /* Some background is needed */
}


.dropdown-submenu {
    position: relative;
}

.dropdown-submenu>.dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -6px;
    margin-left: -1px;
    -webkit-border-radius: 0 6px 6px 6px;
    -moz-border-radius: 0 6px 6px;
    border-radius: 0 6px 6px 6px;
}

.dropdown-submenu:hover>.dropdown-menu {
    display: block;
}

.dropdown-submenu>a:after {
    display: block;
    content: " ";
    float: right;
    width: 0;
    height: 0;
    border-color: transparent;
    border-style: solid;
    border-width: 5px 0 5px 5px;
    border-left-color: black;
    margin-top: 5px;
    margin-right: -10px;
}

.dropdown-submenu:hover>a:after {
    border-left-color: #fff;
}

.dropdown-submenu.pull-left {
    float: none;
}

.dropdown-submenu.pull-left>.dropdown-menu {
    left: -100%;
    margin-left: 10px;
    -webkit-border-radius: 6px 0 6px 6px;
    -moz-border-radius: 6px 0 6px 6px;
    border-radius: 6px 0 6px 6px;
}


/* Modify the background color */
.skin-green .main-header .navbar {
    background-color: black;
}

.swal-wide{
    width:400px !important;
    height: 200px !important;
}

</style>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">

<title>SB V2.0</title>

<!-- Bootstrap core CSS -->
<link href="css/4.1.1/main.css" rel="stylesheet">  
<link href="css/4.1.1/bootstrap.min.css" rel="stylesheet">
<link href="css/4.1.1/datatables.min.css" rel="stylesheet">
<link href="css/4.1.1/bootstrap-select.min.css" rel="stylesheet">
<link href="fontawesome/css/font-awesome.min.css" rel="stylesheet">
<link href="css/4.1.1/datepicker3.css" rel="stylesheet">

<link href="css/4.1.1/bootstrap-multiselect.min.css" rel="stylesheet">
<link href="css/4.1.1/select2.min.css" rel="stylesheet">
<link href="css/4.1.1/select2-bootstrap4.min.css" rel="stylesheet">
<link href="css/4.1.1/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="css/4.1.1/sweetalert2.min" rel="stylesheet">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
    <link rel="stylesheet" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css" /> -->
</head>

<body>
    <!-- Bootstrap NavBar -->
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#">
        <img src="img/NAG logo SIGN.png" alt="">
    </a>
    <a class="navbar-brand text-white" style="font-size: 17px;"><b>PT.NIRWANA ALABARE GARMENT</b></a>

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav mr-auto">
        <!-- <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
      </div> -->

      <!-- navbar master -->
      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="fa fa-book mr-1"> Master
          </a>
          <div class="dropdown-menu bg-dark " aria-labelledby="navbarDropdown" style="width:200px;">
              <?php
              $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Master'");
              $rs = mysqli_fetch_array($querys);
              $menu = isset($rs['menu']) ? $rs['menu'] :0;
              $id = isset($rs['id']) ? $rs['id'] :0;

              if($id == '49'){                             
                echo '
                <a href="AP/master-cash-flow.php" class="dropdown-item bg-dark text-white">
                <span class="fa fa-paperclip fa-fw"></span>
                <span class="menu-collapsed">Cash Flow</span>

                </a>
                <a href="AP/master-coa-category1.php" class="dropdown-item bg-dark text-white">
                <span class="fa fa-paperclip fa-fw "></span>
                <span class="menu-collapsed">Category COA</span>
                </a>
                <a href="AP/master-coa.php" class="dropdown-item bg-dark text-white">
                <span class="fa fa-paperclip fa-fw "></span>
                <span class="menu-collapsed">Chart Of Account</span>
                </a>
                <a href="AP/master-costcenter.php" class="dropdown-item bg-dark text-white">
                <span class="fa fa-paperclip fa-fw "></span>
                <span class="menu-collapsed">Cost Center</span>
                </a>
                <a href="AP/master-profit-center.php" class="dropdown-item bg-dark text-white">
                <span class="fa fa-paperclip fa-fw "></span>
                <span class="menu-collapsed">Profit Center</span>
                </a>
                <a href="AP/master-bank.php" class="dropdown-item bg-dark text-white">
                <span class="fa fa-paperclip fa-fw "></span>
                <span class="menu-collapsed">Bank</span>
                </a>
                <a href="AP/master-mapping-memo.php" class="dropdown-item bg-dark text-white">
                <span class="fa fa-paperclip fa-fw "></span>
                <span class="menu-collapsed">Mapping Memo</span>
                </a>
                ';
            }else{
                echo '';
            }
            ?>  
        </div>
    </li>

    <!-- navbar AP -->
    <li class="nav-item dropdown active">
      <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-paypal mr-1"> AP<span class="caret"></span></a>
      <ul class="dropdown-menu bg-dark text-white" role="menu">
          <?php
          $querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
          $rs = mysqli_fetch_array($querys);
          $group = $rs['Groupp'];
          $pur = $rs['purchasing'];
          $app_po = $rs['approve_po'];

          $queryss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%BPB%' and useraccess.menu != 'Transfer BPB' and useraccess.menu != 'Accept BPB Whs-Acc' and useraccess.menu not like '%create%' group by username");
          while($rss = mysqli_fetch_array($queryss)){
            $menu = isset($rss['ket']) ? $rss['ket'] :0;
            $id = isset($rss['id']) ? $rss['id'] :0;

            $sql = mysqli_query($conn2,"select count(distinct(no_bpb)) as no_bpb from bpb_new where status = 'GMF'");
            $row = mysqli_fetch_array($sql);
            $count = $row['no_bpb'];
            if($count != '0'){
                $notif = '<span class="badge" style="background-color: red;">'.$count.'</span>';
            }else{
                $notif = '';
            } 

            $sql1 = mysqli_query($conn2,"select count(distinct(no_ro)) as no_ro from bppb_new where status = 'GMF'");
            $row1 = mysqli_fetch_array($sql1);
            $count1 = $row1['no_ro'];
            $countjml = $count + $count1;
            if($count1 != '0'){
                $notif1 = '<span class="badge" style="background-color: red;">'.$count1.'</span>';
            }else{
                $notif1 = '';
            }


        } 

        $queryss2 = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%update%'  group by username");
        $menu2 = '';
        while($rss2 = mysqli_fetch_array($queryss2)){
            $menu2 = isset($rss2['ket']) ? $rss2['ket'] :0;
        }              

        if($menu == 'Y' || $menu2 == 'Y'){  
            echo '
            <li class="dropdown-submenu ">
            <a class="dropdown-item bg-dark text-white" href="#">
            <span class="fa fa-envelope-o fa-fw "></span>
            <span class="menu-collapsed">BPB</span>
            </a>
            <ul class="dropdown-menu bg-dark text-white" role="menu">';
            if($id == '1'){ 
             echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw "></span>
             <span class="menu-collapsed">Approve BPB</span>
             '.$notif.'
             </a>';
         }elseif($id == '2'){ 
             echo'<a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB</span>
             </a>';
         }elseif($id == '19'){ 
             echo'<a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
             <span class="menu-collapsed">Approve BPB Return</span>
             '.$notif1.'
             </a>';
         }elseif($id == '20'){ 
             echo'<a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
             <span class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB Return</span>
             </a>';
         }elseif($id == '1,2'){ 
             echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw "></span>
             <span class="menu-collapsed">Approve BPB</span>
             '.$notif.'
             </a>
             <a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB</span>
             </a>';
         }elseif($id == '1,19'){ 
             echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw "></span>
             <span class="menu-collapsed">Approve BPB</span>
             '.$notif.'
             </a>
             <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
             <span class="menu-collapsed">Approve BPB Return</span>
             '.$notif1.'
             </a>';
         }elseif($id == '1,20'){ 
             echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw "></span>
             <span class="menu-collapsed">Approve BPB</span>
             '.$notif.'
             </a>
             <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB Return</span>
             </a>';
         }elseif($id == '2,19'){ 
             echo'<a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB</span>
             </a>
             <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
             <span class="menu-collapsed">Approve BPB Return</span>
             '.$notif1.'
             </a>';
         }elseif($id == '2,20'){ 
             echo'<a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB</span>
             </a>
             <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB Return</span>
             </a>';
         }elseif($id == '19,20'){ 
             echo'<a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
             <span class="menu-collapsed">Approve BPB Return</span>
             '.$notif1.'
             </a>
             <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB Return</span>
             </a>';
         }elseif($id == '1,2,19'){ 
             echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw "></span>
             <span class="menu-collapsed">Approve BPB</span>
             '.$notif.'
             </a>
             <a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB</span>
             </a>
             <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
             <span class="menu-collapsed">Approve BPB Return</span>
             '.$notif1.'
             </a>';
         }elseif($id == '1,2,20'){ 
             echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw "></span>
             <span class="menu-collapsed">Approve BPB</span>
             '.$notif.'
             </a>
             <a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB</span>
             </a>
             <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB Return</span>
             </a>';
         }elseif($id == '2,19,20'){ 
             echo'<a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB</span>
             </a>
             <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
             <span class="menu-collapsed">Approve BPB Return</span>
             '.$notif1.'
             </a>
             <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB Return</span>
             </a>';
         }elseif($id == '1,2,19,20'){ 
             echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw "></span>
             <span class="menu-collapsed">Approve BPB</span>
             '.$notif.'
             </a>
             <a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB</span>
             </a>
             <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa fa-thumbs-up fa-fw "></span>
             <span class="menu-collapsed">Approve BPB Return</span>
             '.$notif1.'
             </a>
             <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
             <span  class="fa fa-share fa-fw "></span>
             <span class="menu-collapsed">Verifikasi BPB Return</span>
             </a>
             ';
         }else{
            echo '';
        }
        if($menu2 == 'Y'){
            echo '<a href="AP/update_bpb.php" class="dropdown-item bg-dark text-white">
            <span  class="fa fa-pencil fa-fw "></span>
            <span class="menu-collapsed">Update BPB</span>
            </a>
            <a href="AP/report-faktur-pajak.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-files-o fa-fw "></span>
            <span class="menu-collapsed">Report FP</span>
            </a>';
        }else{
        }

        echo'</ul>
        </li>';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'FTR'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '4'){  
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span s class="fa fa-money fa-fw "></span>
        <span class="menu-collapsed">FTR</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/ftrcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-paper-plane-o fa-fw "></span>
        <span class="menu-collapsed">FTR CBD</span>
        </a>
        <a href="AP/ftrdp.php" class="dropdown-item bg-dark text-white">
        <span  class="fa fa-paper-plane-o fa-fw "></span>
        <span class="menu-collapsed">FTR DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>


    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Invoice Received'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    $querys2 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Transfer BPB'");
    $rs2 = mysqli_fetch_array($querys2);
    $menu2 = isset($rs2['menu']) ? $rs2['menu'] :0;
    $id2 = isset($rs2['id']) ? $rs2['id'] :0;

    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span s class="fa fa-list-alt fa-fw "></span>
    <span class="menu-collapsed">Document Tracking</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">';
    if($id2 == '77'){    
        echo '<a href="AP/bpb_received.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-share fa-fw "></span>
        <span class="menu-collapsed">BPB Transferred</span>
        </a>';
    }
    if($id == '66'){    
        echo '<a href="AP/invoice_received.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-share fa-fw "></span>
        <span class="menu-collapsed">Invoice Received</span>
        </a>
        <a href="AP/report_invoice_received.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">IR Report</span>
        </a>';
    }
    echo'</ul>
    </li>';
    ?>


    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Kontrabon'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '6'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span s class="fa fa-btc fa-fw "></span>
        <span class="menu-collapsed">Kontra Bon</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/kontrabon.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-ticket fa-fw "></span>
        <span class="menu-collapsed">Kontra Bon Reg</span>
        </a>
        <a href="AP/kontrabonftrcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-ticket fa-fw "></span>
        <span class="menu-collapsed">Kontra Bon FTR CBD</span>
        </a>
        <a href="AP/kontrabonftrdp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-ticket fa-fw "></span>
        <span class="menu-collapsed">Kontra Bon FTR DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>


    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'List Payment'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '8'){  
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-usd fa-fw"></span>
        <span class="menu-collapsed">List Payment</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/payment.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">List Payment Reg</span>
        </a>
        <a href="AP/listpaymentcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">List Payment CBD</span>
        </a>
        <a href="AP/listpaymentdp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">List Payment DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>


    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Payment'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '10'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-usd fa-fw"></span>
        <span class="menu-collapsed">Payment</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/pelunasanftr.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Payment Reg</span>
        </a>
        <a href="AP/pelunasanftrcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Payment CBD</span>
        </a>
        <a href="AP/pelunasanftrdp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Payment DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>


    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Closing%'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    $sql123 = mysqli_query($conn2,"select count(distinct(no_payment)) as no_pay from list_payment where status = 'Approved'");
    $row123 = mysqli_fetch_array($sql123);
    $count123 = $row123['no_pay'];

    $sqlsa = mysqli_query($conn2,"select count(distinct(no_pay)) as no_paysa from saldo_awal where status = 'Approved' and no_pay not like '%LP/NAG%'");
    $rowsa = mysqli_fetch_array($sqlsa);
    $countsa = $rowsa['no_paysa'];

    $countlpsa12 = $count123 + $countsa;
    if($countlpsa12 != '0'){
        $notif123 = '<span class="badge" style="background-color: red;">'.$countlpsa12.'</span>';
    }else{
        $notif123 = '';
    }

    $sql456 = mysqli_query($conn2,"select count(distinct(no_payment)) as no_pay from list_payment_cbd where status = 'Approved'");
    $row456 = mysqli_fetch_array($sql456);
    $count456 = $row456['no_pay'];
    if($count456 != '0'){
        $notif456 = '<span class="badge" style="background-color: red;">'.$count456.'</span>';
    }else{
        $notif456 = '';
    }

    $sql789 = mysqli_query($conn2,"select count(distinct(no_payment)) as no_pay from list_payment_dp where status = 'Approved'");
    $row789 = mysqli_fetch_array($sql789);
    $count789 = $row789['no_pay'];
    if($count789 != '0'){
        $notif789 = '<span class="badge" style="background-color: red;">'.$count789.'</span>';
    }else{
        $notif789 = '';
    }

    if($id == '22'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-credit-card fa-fw"></span>
        <span class="menu-collapsed">Closing Payment</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/formclosing-payreg.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Close Payment Reg</span>
        '.$notif123.'
        </a>
        <a href="AP/formclosing-paycbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Close Payment CBD</span>
        '.$notif456.'
        </a>
        <a href="AP/formclosing-paydp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed"> Close Payment DP</span>
        '.$notif789.'
        </a>
        <a href="AP/status_closing.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Closing Info</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Status'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '30'){       
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-list-ul fa-fw"></span>
        <span class="menu-collapsed">Status</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/status.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-paperclip fa-fw"></span>
        <span class="menu-collapsed">Status</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Report'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;


    $querys2 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Development'");
    $rs2 = mysqli_fetch_array($querys2);
    $menu2 = isset($rs2['menu']) ? $rs2['menu'] :0;
    $id2 = isset($rs2['id']) ? $rs2['id'] :0;


    $querys3 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Rekap Pelunasan%'");
    $rs3 = mysqli_fetch_array($querys3);
    $menu3 = isset($rs3['menu']) ? $rs3['menu'] :0;
    $id3 = isset($rs3['id']) ? $rs3['id'] :0;

    $sql_pr = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Purchase Report'  group by username");
    $menu_pr = '';
    while($rpr = mysqli_fetch_array($sql_pr)){
        $menu_pr = isset($rpr['ket']) ? $rpr['ket'] :0;
    } 

    echo '<li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-files-o fa-fw"></span>
    <span class="menu-collapsed">Report</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">';

    if($id2 == '35'){
        echo '<a href="AP/pcs_detail.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">AP Report</span>
        </a>
        <a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">PCS Detail Dev</span>
        </a>
        <a href="AP/rekap-pelunasan.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Rekap Pelunasan</span>
        </a>';
    }elseif($id == '18' && $id3 == '0'){
        echo '<a href="AP/pcs_detail.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">AP Report</span>
        </a>';
    }elseif($id == '0' && $id3 == '57'){
        echo '<a href="AP/rekap-pelunasan.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Rekap Pelunasan</span>
        </a>';
    }elseif($id == '18' && $id3 == '57'){
        echo '<a href="AP/pcs_detail.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">AP Report</span>
        </a>
        <a href="AP/rekap-pelunasan.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Rekap Pelunasan</span>
        </a>';
    }else{
        echo '';
    }
    if($menu_pr == 'Y'){
        echo '<a href="AP/laporan_pembelian.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Purchase Report</span>
        </a>
        <a href="AP/laporan_retur_pembelian.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-tags fa-fw "></span>
        <span class="menu-collapsed">Purchase Return Report</span>
        </a>';
    }else{
    }
    if ($menu_pr == 'Y' || $id == '18' && $id3 == '57' || $id == '0' && $id3 == '57' || $id2 == '35' || 
        $id == '18' && $id3 == '0') {
        echo '';
}
echo '</ul>
</li>';
?>

<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Approval%'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

$sqlkb = mysqli_query($conn2," select count(distinct(no_kbon)) as no_kbon from kontrabon_h where status = 'draft'");
$rowkb = mysqli_fetch_array($sqlkb);
$countkb = $rowkb['no_kbon'];
if($countkb != '0'){
    $notifkb = '<span class="badge" style="background-color: red;">'.$countkb.'</span>';
}else{
    $notifkb = '';
}

$sqllp = mysqli_query($conn2," select count(distinct(no_payment)) as no_pay from list_payment where status = 'draft'");
$rowlp = mysqli_fetch_array($sqllp);
$countlp = $rowlp['no_pay'];

$sqlsa = mysqli_query($conn2,"select count(distinct(no_pay)) as no_paysa from saldo_awal where status = 'draft' and no_pay not like '%LP/NAG%'");
$rowsa = mysqli_fetch_array($sqlsa);
$countsa = $rowsa['no_paysa'];

$countlpsa = $countlp + $countsa;

if($countlpsa != '0'){
    $notiflp = '<span class="badge" style="background-color: red;">'.$countlpsa.'</span>';
}else{
    $notiflp = '';
}

echo '<li class="dropdown-submenu ">
<a class="dropdown-item bg-dark text-white" href="#">
<span class="fa fa-thumbs-o-up fa-fw"></span>
<span class="menu-collapsed">Approval</span>
</a>
<ul class="dropdown-menu bg-dark text-white" role="menu">';

if($id == '32'){
    echo '<a href="AP/formapprovelp.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">List Payment Reg</span>
    '.$notiflp.'
    </a>
    <a href="AP/formapprovekb.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">Kontrabon Reg</span>
    '.$notifkb.'
    </a>';
}elseif($id == '31'){
    echo '<a href="AP/formapprovekb.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">Kontrabon Reg</span>
    '.$notifkb.'
    </a>';
}if($id == '33'){
    echo '<a href="AP/formapprovelp.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">List Payment Reg</span>
    '.$notiflp.'
    </a>';
}else{
    echo '';
}
echo '</ul>
</li>';
?>

<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Request Debitnote'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

if($id == '78'){    
    echo '
    <a href="AP/request_debitnote.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-registered fa-fw"></span>
    <span class="menu-collapsed">Request Debit Note</span>
    </a>';
}
?>

 <?php
      $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Reverse'");
      $rs = mysqli_fetch_array($querys);
      $menu = isset($rs['menu']) ? $rs['menu'] :0;
      $id = isset($rs['id']) ? $rs['id'] :0;

      if($id == '34'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-refresh fa-fw"></span>
        <span class="menu-collapsed">Reverse</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/formreversebpb.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">BPB</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Maintain FTR'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '12'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-money fa-fw"></span>
        <span class="menu-collapsed">Maintain FTR</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/pengajuan_ftrcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">FTR CBD</span>
        </a>
        <a href="AP/pengajuan_ftrdp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">FTR DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Maintain Kontrabon'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '14'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-btc fa-fw"></span>
        <span class="menu-collapsed">Maintain Kontra Bon</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/pengajuankb.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed"> Kontrabon Reg</span>
        </a>
        <a href="AP/pengajuankb_cbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">Kontrabon CBD</span>
        </a>
        <a href="AP/pengajuankb_dp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">Kontrabon DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Maintain List Payment'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '16'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-usd fa-fw"></span>
        <span class="menu-collapsed">Maintain List Payment</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/pengajuanpayment.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">List Payment Reg</span>
        </a>
        <a href="AP/pengajuanpaymentcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">List Payment CBD</span>
        </a>
        <a href="AP/pengajuanpaymentdp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">List Payment DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>

</ul>
</li>
<!-- END Menu AP -->
<!-- navbar Bank -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-university mr-1"> Bank<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php
      $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Bank'");
      $rs = mysqli_fetch_array($querys);
      $menu = isset($rs['menu']) ? $rs['menu'] :0;
      $id = isset($rs['id']) ? $rs['id'] :0;

      $querys2 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'E - Statement'");
      $rs2 = mysqli_fetch_array($querys2);
      $menu2 = isset($rs2['menu']) ? $rs2['menu'] :0;
      $id2 = isset($rs2['id']) ? $rs2['id'] :0;

      if($id == '36'){
        echo '
        <a href="AP/bank-in22.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-sign-in fa-fw mr-3"></span>
        <span class="menu-collapsed">Bank In</span>
        </a>
        <a href="AP/bank-out.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-sign-out fa-fw mr-3"></span>
        <span class="menu-collapsed">Bank Out</span>
        </a>
        <a href="AP/payment-voucher.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-money fa-fw mr-3"></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a>
        <a href="AP/bankreport.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-file-excel-o fa-fw mr-3"></span>
        <span class="menu-collapsed">Report</span>
        </a>';
    }

    if($id2 == '62'){
        echo '<a href="AP/e_statement.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-money fa-fw mr-3"></span>
        <span class="menu-collapsed">E-Statement</span>
        </a>';
    }

    ?>
    <?php
    $querys = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and  useraccess.menu like '%Bank%' and useraccess.menu like '%Approval%' group by username");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-thumbs-up fa-fw"></span>
    <span class="menu-collapsed">Approval</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">';

    if ($id == '41') {
        echo '
        <a href="AP/approve-pv.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a> ';
    }elseif($id == '42'){
        echo '
        <a href="AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Incoming Bank</span>
        </a> ';
    }elseif($id == '43'){
        echo '
        <a href="AP/approve-outbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Outgoing bank</span>
        </a> ';
    }elseif($id == '41,42'){
        echo '
        <a href="AP/approve-pv.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a>
        <a href="AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Incoming Bank</span>
        </a> ';
    }elseif($id == '41,43'){
        echo '
        <a href="AP/approve-pv.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a>
        <a href="AP/approve-outbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Outgoing bank</span>
        </a> ';
    }elseif($id == '42,43'){
        echo '
        <a href="AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Incoming Bank</span>
        </a>
        <a href="AP/approve-outbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Outgoing bank</span>
        </a>';
    }elseif($id == '41,42,43'){
        echo '
        <a href="AP/approve-pv.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a>
        <a href="AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Incoming Bank</span>
        </a>
        <a href="AP/approve-outbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Outgoing bank</span>
        </a> ';
    }else{
        echo '';
    }
    echo '</ul>
    </li>';
    ?>
</ul>
</li>
<!-- END Menu Bank -->

<!-- navbar Cash -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-money mr-1"> Cash<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php
      $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Cash'");
      $rs = mysqli_fetch_array($querys);
      $menu = isset($rs['menu']) ? $rs['menu'] :0;
      $id = isset($rs['id']) ? $rs['id'] :0;

      if($id == '38'){                             
        echo '
        <a href="AP/cash-in.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-sign-in fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/cash-out.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-sign-out fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-credit-card fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-credit-card fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a>
        <a href="AP/cashreport.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-file-excel-o fa-fw"></span>
        <span class="menu-collapsed">Report Cash</span>
        </a>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and  useraccess.menu like '%Cash%' and useraccess.menu like '%Approval%' group by username");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-thumbs-up fa-fw"></span>
    <span class="menu-collapsed">Approval</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">';

    if ($id == '44') {
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a> ';
    }elseif($id == '45'){
        echo '
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a> ';
    }elseif($id == '46'){
        echo '
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a> ';
    }elseif($id == '47'){
        echo '
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '44,45'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>';
    }elseif($id == '44,46'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a> ';
    }elseif($id == '44,47'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '45,46'){
        echo '
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>';
    }elseif($id == '45,47'){
        echo '
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '46,47'){
        echo '
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '44,45,46'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a> ';
    }elseif($id == '44,45,47'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '44,46,47'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '45,46,47'){
        echo '
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '44,45,46,47'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }else{
        echo '';
    }
    echo '</ul>
    </li>';
    ?>
</ul>
</li>
<!-- END Menu Cash -->
<!-- navbar Accounting -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-bar-chart mr-1"> Accounting<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php
      $querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
      $rs = mysqli_fetch_array($querys);
      $group = $rs['Groupp'];
      $pur = $rs['purchasing'];
      $app_po = $rs['approve_po'];

      $queryss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Acct%' and menurole.status = 'Menu' group by username");
      while($rss = mysqli_fetch_array($queryss)){
        $menu = isset($rss['ket']) ? $rss['ket'] :0;
        $id = isset($rss['id']) ? $rss['id'] :0;

    }           

    if($menu == 'Y'){               
        echo '';
        if(strpos($id, '50') !== false){ 
         echo'<a href="AP/memorial-journal.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-bars fa-fw"></span>
         <span class="menu-collapsed">Memorial Journal</span>
         </a>';
     }if(strpos($id, '51') !== false){ 
         echo'<a href="AP/list-journal.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-list-alt fa-fw"></span>
         <span class="menu-collapsed">List Journal</span>
         </a>';
     }if(strpos($id, '52') !== false){ 
         echo'<a href="AP/general-ledger.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-print fa-list"></span>
         <span class="menu-collapsed">General Ledger</span>
         </a>';
     }

     echo '<li class="dropdown-submenu ">
     <a class="dropdown-item bg-dark text-white" href="#">
     <span class="fa fa-list-ul fa-fw"></span>
     <span class="menu-collapsed">Sub Ledger</span>
     </a>
     <ul class="dropdown-menu bg-dark text-white" role="menu">';
     if(strpos($id, '64') !== false){
        echo'<a href="AP/other_receivable_report.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-fax fa-fw"></span>
        <span class="menu-collapsed">Other Receivable</span>
        </a>';
    }
    if(strpos($id, '65') !== false){
        echo'<a href="AP/other_payable_report.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-fax fa-fw"></span>
        <span class="menu-collapsed">Other Payable</span>
        </a>';
    }
    if(strpos($id, '82') !== false){
        echo'<a href="AP/purchase_advance_report.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-fax fa-fw"></span>
        <span class="menu-collapsed">Purchase Advance</span>
        </a>';
    }
    echo'</ul>
    </li>';
    if(strpos($id, '53') !== false){ 
     echo'<li class="dropdown-submenu ">
     <a class="dropdown-item bg-dark text-white" href="#">
     <span class="fa fa-balance-scale fa-fw"></span>
     <span class="menu-collapsed">Financial Statement</span>
     </a>
     <ul class="dropdown-menu bg-dark text-white" role="menu">
     <a href="AP/trial-balance-ytd.php" class="dropdown-item bg-dark text-white">
     <span class="fa fa-calendar fa-fw mr-3"></span>
     <span class="menu-collapsed">Year To Date</span>
     </a>
     <a href="AP/trial-balance-monthly.php" class="dropdown-item bg-dark text-white">
     <span class="fa fa-calendar-o fa-fw mr-3"></span>
     <span class="menu-collapsed">Monthly</span>
     </a>
     </ul>
     </li>';
 }

}

?>
</ul>
</li>
<!-- END Menu Accounting -->
<!-- navbar Exim -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cubes mr-1"> Exim<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php

      $queryss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Exim%' and menurole.status = 'Menu' group by username");
      while($rss = mysqli_fetch_array($queryss)){
        $menu = isset($rss['ket']) ? $rss['ket'] :0;
        $id = isset($rss['id']) ? $rss['id'] :0;

    }           

    if($menu == 'Y'){               
        echo '';
        if(strpos($id, '83') !== false){ 
         echo'<a href="AP/exim-calculatin-cost-report.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-bars fa-fw"></span>
         <span class="menu-collapsed">Calculation Cost Report</span>
         </a>';
     }
     // if(strpos($id, '84') !== false){ 
     //     echo'<a href="#" class="dropdown-item bg-dark text-white">
     //     <span class="fa fa-list-alt fa-fw"></span>
     //     <span class="menu-collapsed">Checklist Cost Report</span>
     //     </a>';
     // }


 }

 ?>
</ul>
</li>
<!-- END Menu Exim -->

<!-- navbar Other Menu -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cogs mr-1"></span>Setting<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
        <?php
        $querys = mysqli_query($conn1,"select Groupp from userpassword where username = '$user'");
        $rs = mysqli_fetch_array($querys);
        $group = isset($rs['Groupp']) ? $rs['Groupp'] : null;

        if($group != 'STAFF' && $group != null){                      
            echo '
            <a href="AP/userrole.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-user-plus fa-fw"></span>
            <span class="menu-collapsed">Userrole</span>
            </a>';
        }else{
            echo '';
        }
        ?>
    </ul>
</li>
<!-- END Menu Other Menu -->

      <!-- <li class="nav-item dropdown active">
          <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown">Proses<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">

            <li class="dropdown-submenu">
              <a class="dropdown-item" href="#">Another action</a>
              <ul class="dropdown-menu" role="menu">
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
              </ul>
            </li>

            <li class="dropdown-submenu">
              <a class="dropdown-item" href="#">Another action</a>
              <ul class="dropdown-menu" role="menu">
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
              </ul>
            </li>

          </ul>
        </li>
    -->
</ul>

<ul class="navbar-nav ml-auto">
    <li class="nav-item active">
        <a class="nav-link" href="function/logout.php" style="font-size:14px;"><span class="fa fa-power-off">Log-out</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="index.php" style="font-size:14px;"><span class="fa fa-home">Home</a>
            </li>
            <li class="nav-item active">
                <span class="navbar-text text-white" style="font-size:14px;"><span class="fa fa-user"> <?php echo $user ?> </span></span>
            </li>
        </div>

    </nav>

    <!-- MAIN -->

    <div class="col p-3">
        <div class="col-md-2 pl-0 ">
          <select style="background-color: gray;" class="form-control selectpicker" name="pilih_dashboard" id="pilih_dashboard" onchange="ubahdashboard(this.value)" required>
             <option value="-" disabled selected="true">Select Dashboard</option> 
             <?php
             $pilih_dashboard ='';
             if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $pilih_dashboard = isset($_POST['pilih_dashboard']) ? $_POST['pilih_dashboard']: null;
            }                 
            $sql = mysql_query("select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and menurole.status = 'DSB'",$conn1);
            while ($row = mysql_fetch_array($sql)) {
                $data = $row['menu'];
                $id = $row['id'];
                if($row['id'] == $_POST['pilih_dashboard']){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo '<option value="'.$id.'"'.$isSelected.'">'. $data .'</option>';    
            }?>
        </select>
    </div>
    <div class="box " style="background-color: #F0F8FF;">

        <div id="isi_dashboard">
            <?php 
            include '../dashboard/welcome-page.php';  
            ?>
        </div>
    </div>
</div>
<!-- Main Col END -->

</div><!-- body-row END -->

<!-- Bootstrap core JavaScript -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>  
<script type="text/javascript" src="css/4.1.1/fusionchart/fusioncharts.js"></script>
<script type="text/javascript" src="css/4.1.1/fusionchart/themes/fusioncharts.theme.fusion.js"></script>
<script type="text/javascript" src="css/4.1.1/apexchart/apexcharts.js"></script>
<script type="text/javascript" src="css/4.1.1/canvaschart/canvasjs.min.js"></script>
<script type="text/javascript" src="css/4.1.1/canvaschart/canvasjs.stock.min.js"></script>
<script type="text/javascript" src="css/4.1.1/amchart/index.js"></script>
<script type="text/javascript" src="css/4.1.1/amchart/xy.js"></script>
<script type="text/javascript" src="css/4.1.1/amchart/animated.js"></script>
<script type="text/javascript" src="css/4.1.1/amchart/radar.js"></script>
<script language="JavaScript" src="css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="css/4.1.1/select2.full.min.js"></script>
<!--  <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
    <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script> -->
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

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });

      //Initialize Select2 Elements
      $('.select2').select2({
        theme: 'bootstrap4',
    });

      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4',
        width: '100%',
        containerCssClass: 'form-control-sm'
    })

</script>

<script type="text/javascript">
    function ubahdashboard(id){
        var id_dsb = id;
        $.ajax({
            type: 'POST', 
            url: 'ubah_dashboard.php', 
            data: {'id_dsb':id_dsb},
            success: function(response) { 
                $('#isi_dashboard').html(response); 
            }
        });
    }
</script>



<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
