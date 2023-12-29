<?php
  session_start();
  include '../conn/conn.php';
  $user = $_SESSION['username'];
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
</style>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>SB V2.0</title>

  <!-- Bootstrap core CSS -->
<link href="css/4.1.1/main.css" rel="stylesheet">  
<link href="css/4.1.1/bootstrap.min.css" rel="stylesheet">
<link href="fontawesome/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>
<!-- Bootstrap NavBar -->
<nav class="navbar navbar-fixed-top navbar-expand-md navbar-dark bg-primary">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#">
        <img src="img/NAG logo SIGN.png" alt="">
    </a>
    <a class="navbar-brand text-white"><b>PT.NIRWANA ALABARE GARMENT</b></a>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#top">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <span class="navbar-text text-white"><span class="fa fa-user"> <?php echo $user ?> </span></span>
            </li>
<!--             <li class="nav-item">
                <a class="nav-link" href="#top">Pricing</a>
            </li>-->
            <!-- This menu is hidden in bigger devices with d-sm-none. 
           The sidebar isn't proper for smaller screens imo, so this dropdown menu can keep all the useful sidebar itens exclusively for smaller screens  -->
            <li class="nav-item dropdown d-sm-block d-md-none">
                <a class="nav-link dropdown-toggle" href="#" id="smallerscreenmenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Menu </a>
                <div class="dropdown-menu" aria-labelledby="smallerscreenmenu">
                    <a class="dropdown-item" href="#top">Pembelian</a>
                    <a class="dropdown-item" href="#top">Penjualan</a>
                    <a class="dropdown-item" href="#top">Tasks</a>
                    <a class="dropdown-item" href="#top">Etc ...</a>
                </div>
            </li><!-- Smaller devices menu END -->
        </ul>
    </div>
</nav><!-- NavBar END -->
<!-- Bootstrap row -->
<div class="row " id="body-row">
    <!-- Sidebar -->
    <div id="sidebar-container" class="sidebar-expanded d-none d-md-block">
        <!-- d-* hiddens the Sidebar in smaller devices. Its itens can be kept on the Navbar 'Menu' -->
        <!-- Bootstrap List Group -->
        <ul class="list-group">
            <!-- Separator with title -->

           
            <!-- Separator with title -->
           <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu not like '%Maintain%'");
            $rs = mysqli_fetch_array($querys);
            $id = isset($rs['id']) ? $rs['id'] : 0;
                
                if($id == '0'){
                    echo '';
                }else{
                    echo '<li class="list-group-item sidebar-separator-title text-muted d-flex align-items-center menu-collapsed">
                  <span class="fa fa-book fa-lg mr-3"></span>
                <small style="font-size: 15px;font-family:Trebuchet MS;"><b>MAIN MENU</b></small>
            </li>';
            }
            ?> 
            <!-- /END Separator -->
            <!-- Menu with submenu -->
            <?php
            $querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $pur = $rs['purchasing'];
            $app_po = $rs['approve_po'];

            $queryss = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%BPB%'");
            while($rss = mysqli_fetch_array($queryss)){
            $menu = isset($rss['menu']) ? $rss['menu'] :0;
            $id = isset($rss['id']) ? $rss['id'] :0;

            $sql = mysqli_query($conn2,"select count(distinct(no_bpb)) as no_bpb from bpb_new where status = 'GMF'");
            $row = mysqli_fetch_array($sql);
            $count = $row['no_bpb'];
            if($count != '0'){
            $notif = '<span class="badge" style="background-color: red;">'.$count.'</span>';
            }else{
            $notif = '';
            }            

            if($id == '1'){               
                echo '<a href="#submenu1" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-envelope-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">BPB</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu1" class="collapse sidebar-submenu">
                <a href="AP/formapprovebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approve BPB</span>
                    '.$notif.'
                </a>
                </div>';
            }
            elseif($id == '2'){               
                echo '<a href="#submenu16" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-envelope-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">BPB</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu16" class="collapse sidebar-submenu">
            <a href="AP/verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>
                </div>';
            }else{
                echo '';
            }
        }
            ?>                                                                                            
            
             <?php
            $querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $pur = $rs['purchasing'];
            $app_po = $rs['approve_po'];

            $queryss = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Return%'");
            while($rss = mysqli_fetch_array($queryss)){
            $menu = isset($rss['menu']) ? $rss['menu'] :0;
            $id = isset($rss['id']) ? $rss['id'] :0;

            $sql = mysqli_query($conn2,"select count(distinct(no_ro)) as no_ro from bppb_new where status = 'GMF'");
            $row = mysqli_fetch_array($sql);
            $count = $row['no_ro'];
            if($count != '0'){
            $notif1 = '<span class="badge" style="background-color: red;">'.$count.'</span>';
            }else{
            $notif1 = '';
            }            

            if($id == '19'){               
                echo '<a href="#submenu17" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-envelope-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">BPB Return</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu17" class="collapse sidebar-submenu">
               <a href="AP/formapprovebppb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span class="fa fa-share fa-fw mr-2"></span>
                    <span class="menu-collapsed">Approve BPB Return</span>
                    '.$notif1.'
                </a>
                </div>';
            }
            elseif($id == '20'){               
                echo '<a href="#submenu18" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-envelope-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">BPB Return</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu18" class="collapse sidebar-submenu">
             <a href="AP/verifikasibppb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB Return</span>
            </a>
                </div>';
            }else{
                echo '';
            }
        }
            ?> 


            <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'FTR'");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if($id == '4'){                             
                echo '<a href="#submenu2" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-money fa-fw mr-3"></span>
                    <span class="menu-collapsed">FTR</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu2" class="collapse sidebar-submenu">
            <a href="AP/ftrcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paper-plane-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">FTR CBD</span>
            </a>
            <a href="AP/ftrdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paper-plane-o fa-fw mr-3"></span>
                <span class="menu-collapsed">FTR DP</span>
            </a>
                </div>';
            }else{
                echo '';
            }
            ?>
             
            <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Kontrabon'");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if($id == '6'){
                    echo '<a href="#submenu3" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-btc fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontra Bon</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu3" class="collapse sidebar-submenu">
            <a href="AP/kontrabon.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-ticket fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontra Bon Reg</span>
                </a>
                <a href="AP/kontrabonftrcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-ticket fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontra Bon FTR CBD</span>
                </a>
                <a href="AP/kontrabonftrdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-ticket fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontra Bon FTR DP</span>
                </a>
                </div>';
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
                    echo '<a href="#submenu4" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-usd fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu4" class="collapse sidebar-submenu">
                <a href="AP/payment.php" class="list-group-item list-group-item-action bg-dark text-white">
                <span class="fa fa-tags fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment Reg</span>
                </a>
                <a href="AP/listpaymentcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-tags fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment CBD</span>
                </a>
                <a href="AP/listpaymentdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-tags fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment DP</span>
                </a>
                </div>';
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
                    echo '<a href="#submenu5" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-credit-card fa-fw mr-3"></span>
                    <span class="menu-collapsed">Payment</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu5" class="collapse sidebar-submenu">
                <a href="AP/pelunasanftr.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-tag fa-fw mr-3"></span>
                    <span class="menu-collapsed">Payment Reg</span>
                </a>
                <a href="AP/pelunasanftrcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-tag fa-fw mr-3"></span>
                    <span class="menu-collapsed">Payment CBD</span>
                </a>
                <a href="AP/pelunasanftrdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-tag fa-fw mr-3"></span>
                    <span class="menu-collapsed">Payment DP</span>
                </a>
                </div>';
                }else{
                    echo '';
            }
            ?>

            <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Closing%'");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            $sql123 = mysqli_query($conn2,"select count(distinct(payment_ftr_id)) as no_pay from payment_ftr where keterangan = 'Paid'");
            $row123 = mysqli_fetch_array($sql123);
            $count123 = $row123['no_pay'];
            if($count123 != '0'){
            $notif123 = '<span class="badge" style="background-color: red;">'.$count123.'</span>';
            }else{
            $notif123 = '';
            }

            $sql456 = mysqli_query($conn2,"select count(distinct(payment_ftr_id)) as no_pay from payment_ftrcbd where keterangan = 'Paid'");
            $row456 = mysqli_fetch_array($sql456);
            $count456 = $row456['no_pay'];
            if($count456 != '0'){
            $notif456 = '<span class="badge" style="background-color: red;">'.$count456.'</span>';
            }else{
            $notif456 = '';
            }

            $sql789 = mysqli_query($conn2,"select count(distinct(payment_ftr_id)) as no_pay from payment_ftrdp where keterangan = 'Paid'");
            $row789 = mysqli_fetch_array($sql789);
            $count789 = $row789['no_pay'];
            if($count789 != '0'){
            $notif789 = '<span class="badge" style="background-color: red;">'.$count789.'</span>';
            }else{
            $notif789 = '';
            }

            if($id == '22'){
                    echo '<a href="#submenu22" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-credit-card fa-fw mr-3"></span>
                    <span class="menu-collapsed">Closing Payment</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu22" class="collapse sidebar-submenu">
                <a href="AP/formclosing-payreg.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-tag fa-fw mr-2"></span>
                    <span class="menu-collapsed">Close Payment Reg</span>
                    '.$notif123.'
                </a>
                <a href="AP/formclosing-paycbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-tag fa-fw mr-2"></span>
                    <span class="menu-collapsed">Close Payment CBD</span>
                    '.$notif456.'
                </a>
                <a href="AP/formclosing-paydp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-tag fa-fw mr-2"></span>
                    <span class="menu-collapsed"> Close Payment DP</span>
                    '.$notif789.'
                </a>
                </div>';
                }else{
                    echo '';
            }
            ?>

              <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Report'");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if($id == '18'){
                    echo '<a href="#submenu11" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-files-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">Report</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu11" class="collapse sidebar-submenu">
                
                <a href="AP/pcs_persupp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">PCS Global</span>
                </a>
                <a href="AP/kartu_hutang.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">PCS Detail</span>
                </a>
                </div>';
                }else{
                    echo '';
            }
            ?>

           <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Maintain%'");
            $rs = mysqli_fetch_array($querys);
            $id = isset($rs['id']) ? $rs['id'] : 0;
                
                if($id == '0'){
                    echo '';
                }else{
                    echo '<li class="list-group-item sidebar-separator-title text-muted d-flex align-items-center menu-collapsed">
                  <span class="fa fa-book fa-lg mr-3"></span>
                <small style="font-size: 15px;font-family:Trebuchet MS;"><b>MAINTAIN MENU</b></small>
            </li>';
            }
            ?> 
           

             <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Maintain FTR'");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if($id == '12'){
                    echo '<a href="#submenu9" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-money fa-fw mr-2"></span>
                    <span class="menu-collapsed" style="font-size: 14px;">Maintain FTR</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu9" class="collapse sidebar-submenu">
            <a href="AP/pengajuan_ftrcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">FTR CBD</span>
                </a>
                <a href="AP/pengajuan_ftrdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">FTR DP</span>
                </a>
             
                </div>';
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
                    echo '<a href="#submenu7" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-btc fa-fw mr-2"></span>
                    <span class="menu-collapsed" style="font-size: 14px;">Maintain Kontra Bon</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu7" class="collapse sidebar-submenu">
            <a href="AP/pengajuankb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed"> Kontrabon Reg</span>
                </a>
                <a href="AP/pengajuankb_cbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontrabon CBD</span>
                </a>
                <a href="AP/pengajuankb_dp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontrabon DP</span>
                </a>
                </div>';
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
                    echo '<a href="#submenu10" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-usd fa-fw mr-2"></span>
                    <span class="menu-collapsed" style="font-size: 14px;">Maintain List Payment</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu10" class="collapse sidebar-submenu">
            <a href="AP/pengajuanpayment.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment Reg</span>
                </a>
                <a href="AP/pengajuanpaymentcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                        <span class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment CBD</span>
                </a>
                <a href="AP/pengajuanpaymentdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                        <span class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment DP</span>
                </a>
             
                </div>';
                }else{
                    echo '';
            }
            ?>

            
            <!-- Separator with title -->
            <li class="list-group-item sidebar-separator-title text-muted d-flex align-items-center menu-collapsed">
                <span class="fa fa-tasks fa-lg mr-3"></span>
                <small style="font-size: 15px;font-family:'Trebuchet MS';"><b>OTHER MENU</b></small>
            </li>
            
            <?php
            $querys = mysqli_query($conn1,"select Groupp from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            
            if($group != 'STAFF'){                             
                echo '
            <a href="AP/userrole.php" class="bg-dark list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-user-plus fa-fw mr-3"></span>
                    <span class="menu-collapsed">Userrole</span>
                </div>
            </a>';
            }else{
                echo '';
            }
            ?> 

            <a href="function/logout.php" class="bg-dark list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-power-off fa-fw mr-3"></span>
                    <span class="menu-collapsed">Logout</span>
                </div>
            </a>



        </ul><!-- List Group END-->
    </div><!-- sidebar-container END -->
    <!-- MAIN -->
    <div class="col p-4" style="background-image: url('../images/banner-nirwana-3.jpg'); height: 650px; background-size: cover; background-repeat: no-repeat;">
        <h1 class="display-4 text-white"><b><?php echo "Welcome $user"?></b></h1>
    </div><!-- Main Col END -->
</div><!-- body-row END -->

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>  
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
 
<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
