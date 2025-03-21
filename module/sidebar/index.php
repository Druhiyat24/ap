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
<div class="row" id="body-row">
    <!-- Sidebar -->
    <div id="sidebar-container" class="sidebar-expanded d-none d-md-block w-auto">
        <!-- d-* hiddens the Sidebar in smaller devices. Its itens can be kept on the Navbar 'Menu' -->
        <!-- Bootstrap List Group -->
        <ul class="list-group">
            <!-- Separator with title -->
            <li class="list-group-item sidebar-separator-title text-muted d-flex align-items-center menu-collapsed">
                  <span class="fa fa-home fa-lg mr-3"></span>
                <small ><a href="http://10.10.5.62:8080/erp/"><b style="font-size: 15px;font-family:Trebuchet MS;color: #696969;">MAIN MENU</b></a></small>
            </li>
            <!-- /END Separator -->
<!-- Start Menu Master --> 

            <a href="#menu2" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-book fa-fw mr-3"></span>
                    <span class="menu-collapsed">Master</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>

            <!-- Submenu content -->
            <div id="menu2" class="collapse sidebar-submenu">
            <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Master'");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if($id == '49'){                             
                echo '
            <a href="AP/master-cash-flow.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paperclip fa-fw mr-3"></span>
                    <span class="menu-collapsed">Cash Flow</span>

            </a>
            <a href="AP/master-coa-category1.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paperclip fa-fw mr-3"></span>
                <span class="menu-collapsed">Category COA</span>
            </a>
            <a href="AP/master-coa.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paperclip fa-fw mr-3"></span>
                <span class="menu-collapsed">Chart Of Account</span>
            </a>
            <a href="AP/master-costcenter.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paperclip fa-fw mr-3"></span>
                <span class="menu-collapsed">Cost Center</span>
            </a>
            <a href="AP/master-profit-center.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paperclip fa-fw mr-3"></span>
                    <span class="menu-collapsed">Profit Center</span>
            </a>
            <a href="AP/master-bank.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paperclip fa-fw mr-3"></span>
                    <span class="menu-collapsed">Bank</span>
                </a>
            <a href="AP/master-mapping-memo.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paperclip fa-fw mr-3"></span>
                    <span class="menu-collapsed">Mapping Memo</span>
                </a>
                ';
            }else{
                echo '';
            }
            ?>  
            </div>  

<!-- END Menu Master --> 
<!-- Start Menu AP -->            

          <a href="#menu1" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa fa-paypal fa-fw mr-3"></span>
                    <span class="menu-collapsed">AP</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <div id="menu1" class="collapse sidebar-submenu">
            <?php
            $querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $pur = $rs['purchasing'];
            $app_po = $rs['approve_po'];

            $queryss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%BPB%' and useraccess.menu not like '%create%' group by username");
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
            <a href="#submenu1" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-envelope-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">BPB</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>

            <div id="submenu1" class="collapse sidebar-submenu">';
            if($id == '1'){ 
               echo'<a href="AP/formapprovebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approve BPB</span>
                    '.$notif.'
                </a>';
            }elseif($id == '2'){ 
               echo'<a href="AP/verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>';
            }elseif($id == '19'){ 
               echo'<a href="AP/formapprovebppb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-2"></span>
                    <span class="menu-collapsed">Approve BPB Return</span>
                    '.$notif1.'
                </a>';
            }elseif($id == '20'){ 
               echo'<a href="AP/verifikasibppb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB Return</span>
            </a>';
            }elseif($id == '1,2'){ 
               echo'<a href="AP/formapprovebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approve BPB</span>
                    '.$notif.'
                </a>
                <a href="AP/verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>';
            }elseif($id == '1,19'){ 
               echo'<a href="AP/formapprovebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approve BPB</span>
                    '.$notif.'
                </a>
                <a href="AP/formapprovebppb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-2"></span>
                    <span class="menu-collapsed">Approve BPB Return</span>
                    '.$notif1.'
                </a>';
            }elseif($id == '1,20'){ 
               echo'<a href="AP/formapprovebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approve BPB</span>
                    '.$notif.'
                </a>
                <a href="AP/verifikasibppb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB Return</span>
            </a>';
            }elseif($id == '2,19'){ 
               echo'<a href="AP/verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>
                <a href="AP/formapprovebppb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-2"></span>
                    <span class="menu-collapsed">Approve BPB Return</span>
                    '.$notif1.'
                </a>';
            }elseif($id == '2,20'){ 
               echo'<a href="AP/verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>
                <a href="AP/verifikasibppb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB Return</span>
            </a>';
            }elseif($id == '19,20'){ 
               echo'<a href="AP/formapprovebppb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-2"></span>
                    <span class="menu-collapsed">Approve BPB Return</span>
                    '.$notif1.'
                </a>
                <a href="AP/verifikasibppb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB Return</span>
            </a>';
            }elseif($id == '1,2,19'){ 
               echo'<a href="AP/formapprovebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approve BPB</span>
                    '.$notif.'
                </a>
                <a href="AP/verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>
                <a href="AP/formapprovebppb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-2"></span>
                    <span class="menu-collapsed">Approve BPB Return</span>
                    '.$notif1.'
                </a>';
            }elseif($id == '1,2,20'){ 
               echo'<a href="AP/formapprovebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approve BPB</span>
                    '.$notif.'
                </a>
                <a href="AP/verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>
                <a href="AP/verifikasibppb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB Return</span>
            </a>';
            }elseif($id == '2,19,20'){ 
               echo'<a href="AP/verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>
                <a href="AP/formapprovebppb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-2"></span>
                    <span class="menu-collapsed">Approve BPB Return</span>
                    '.$notif1.'
                </a>
                <a href="AP/verifikasibppb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB Return</span>
            </a>';
            }elseif($id == '1,2,19,20'){ 
               echo'<a href="AP/formapprovebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approve BPB</span>
                    '.$notif.'
                </a>
                <a href="AP/verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>
                <a href="AP/formapprovebppb.php" class="list-group-item list-group-item-action bg-dark text-white">
            <span style = "Padding-left: 20px;" class="fa fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approve BPB Return</span>
                    '.$notif1.'
                </a>
                <a href="AP/verifikasibppb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-share fa-fw mr-3"></span>
                    <span class="menu-collapsed">Verifikasi BPB Return</span>
            </a>';
            }else{
                echo '';
            }
            if($menu2 == 'Y'){
                echo '<a href="AP/update_bpb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-pencil fa-fw mr-3"></span>
                    <span class="menu-collapsed">Update BPB</span>
                </a>';
            }else{
            }
                echo'</div>';
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
                    <span style = "Padding-left: 5px;" class="fa fa-money fa-fw mr-3"></span>
                    <span class="menu-collapsed">FTR</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu2" class="collapse sidebar-submenu">
            <a href="AP/ftrcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-paper-plane-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">FTR CBD</span>
            </a>
            <a href="AP/ftrdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-paper-plane-o fa-fw mr-3"></span>
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
                    <span style = "Padding-left: 5px;" class="fa fa-btc fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontra Bon</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu3" class="collapse sidebar-submenu">
            <a href="AP/kontrabon.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-ticket fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontra Bon Reg</span>
                </a>
                <a href="AP/kontrabonftrcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-ticket fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontra Bon FTR CBD</span>
                </a>
                <a href="AP/kontrabonftrdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-ticket fa-fw mr-3"></span>
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
                    <span style = "Padding-left: 5px;" class="fa fa-usd fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu4" class="collapse sidebar-submenu">
                <a href="AP/payment.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-tags fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment Reg</span>
                </a>
                <a href="AP/listpaymentcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-tags fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment CBD</span>
                </a>
                <a href="AP/listpaymentdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-tags fa-fw mr-3"></span>
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
                    <span style = "Padding-left: 5px;" class="fa fa-credit-card fa-fw mr-3"></span>
                    <span class="menu-collapsed">Payment</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu5" class="collapse sidebar-submenu">
                <a href="AP/pelunasanftr.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-tag fa-fw mr-3"></span>
                    <span class="menu-collapsed">Payment Reg</span>
                </a>
                <a href="AP/pelunasanftrcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-tag fa-fw mr-3"></span>
                    <span class="menu-collapsed">Payment CBD</span>
                </a>
                <a href="AP/pelunasanftrdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-tag fa-fw mr-3"></span>
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
                    echo '<a href="#submenu6" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-credit-card fa-fw mr-3"></span>
                    <span class="menu-collapsed">Closing Payment</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu6" class="collapse sidebar-submenu">
                <a href="AP/formclosing-payreg.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-tag fa-fw mr-3"></span>
                    <span class="menu-collapsed">Close Payment Reg</span>
                    '.$notif123.'
                </a>
                <a href="AP/formclosing-paycbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-tag fa-fw mr-3"></span>
                    <span class="menu-collapsed">Close Payment CBD</span>
                    '.$notif456.'
                </a>
                <a href="AP/formclosing-paydp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-tag fa-fw mr-3"></span>
                    <span class="menu-collapsed"> Close Payment DP</span>
                    '.$notif789.'
                </a>
                <a href="AP/status_closing.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-info-circle fa-fw mr-3"></span>
                    <span class="menu-collapsed">Closing Info</span>
                </a>
                </div>';
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
                    echo '<a href="#submenu7" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-list-ul fa-fw mr-3"></span>
                    <span class="menu-collapsed">Status</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu7" class="collapse sidebar-submenu">
                <a href="AP/status.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-list-ul fa-fw mr-3"></span>
                    <span class="menu-collapsed">Status</span>
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

            echo '<a href="#submenu8" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-files-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">Report</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu8" class="collapse sidebar-submenu">';

            if($id2 == '35'){
                    echo '<a href="AP/pcs_detail.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">AP Report</span>
                </a>
                <a href="AP/formapprovebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">PCS Detail Dev</span>
                </a>
                <a href="AP/rekap-pelunasan.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">Rekap Pelunasan</span>
                </a>
                ';
                }elseif($id == '18' && $id3 == '0'){
                    echo '<a href="AP/pcs_detail.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">AP Report</span>
                </a>';
                }elseif($id == '0' && $id3 == '57'){
                    echo '<a href="AP/rekap-pelunasan.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">Rekap Pelunasan</span>
                </a>';
                }elseif($id == '18' && $id3 == '57'){
                    echo '<a href="AP/pcs_detail.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">AP Report</span>
                </a>
                <a href="AP/rekap-pelunasan.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">Rekap Pelunasan</span>
                </a>';
                }else{
                    echo '';
            }
            if($menu_pr == 'Y'){
                echo '<a href="AP/laporan_pembelian.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">Purchase Report</span>
                </a>
                <a href="AP/laporan_retur_pembelian.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-print fa-fw mr-3"></span>
                    <span class="menu-collapsed">Purchase Return Report</span>
                </a>';
            }else{
            }
            if ($menu_pr == 'Y' || $id == '18' && $id3 == '57' || $id == '0' && $id3 == '57' || $id2 == '35' || 
$id == '18' && $id3 == '0') {
            echo '</div>';
            }
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

            if($id == '32'){
                    echo '<a href="#submenu16" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-thumbs-o-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approval</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu16" class="collapse sidebar-submenu">
                <a href="AP/formapprovelp.php" class="list-group-item list-group-item-action bg-dark text-white">
                <span style = "Padding-left: 20px;" class="fa fa-tags fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment Reg</span>
                    '.$notiflp.'
                </a>
                <a href="AP/formapprovekb.php" class="list-group-item list-group-item-action bg-dark text-white">
                <span style = "Padding-left: 20px;" class="fa fa-ticket fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontrabon Reg</span>
                    '.$notifkb.'
                </a>
                </div>
';
                }elseif($id == '31'){
                    echo '<a href="#submenu16" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-thumbs-o-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approval</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu16" class="collapse sidebar-submenu">
                <a href="AP/formapprovekb.php" class="list-group-item list-group-item-action bg-dark text-white">
                <span style = "Padding-left: 20px;" class="fa fa-ticket fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontrabon Reg</span>
                    '.$notifkb.'
                </a>
                </div>
';
                }if($id == '33'){
                    echo '<a href="#submenu16" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-thumbs-o-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approval</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu40" class="collapse sidebar-submenu">
                <a href="AP/formapprovelp.php" class="list-group-item list-group-item-action bg-dark text-white">
                <span style = "Padding-left: 20px;" class="fa fa-tags fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment Reg</span>
                    '.$notiflp.'
                </a>
                </div>
';
                }else{
                    echo '';
            }
            ?>

        </div>
<!-- END Menu AP -->
<!-- START MENU BANK -->

            <a href="#menu3" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-university fa-fw mr-3"></span>
                    <span class="menu-collapsed">Bank</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="menu3" class="collapse sidebar-submenu">
        
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
                <a href="AP/bank-in22.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-sign-in fa-fw mr-3"></span>
                    <span class="menu-collapsed">Bank In</span>
                </a>
                <a href="AP/bank-out.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-sign-out fa-fw mr-3"></span>
                    <span class="menu-collapsed">Bank Out</span>
                </a>
                <a href="AP/payment-voucher.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-money fa-fw mr-3"></span>
                    <span class="menu-collapsed">Payment Voucher</span>
                </a>
                <a href="AP/bankreport.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-file-excel-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">Report</span>
                </a>';
            }

            if($id2 == '62'){
            echo '<a href="AP/e_statement.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-money fa-fw mr-3"></span>
                    <span class="menu-collapsed">E-Statement</span>
                </a>';
            }

            ?>
        </div>
                <!-- Submenu content -->
            <div id="menu3" class="collapse sidebar-submenu">
            <a href="#submenu9" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approval</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <div id="submenu9" class="collapse sidebar-submenu">
            <?php
            $querys = mysqli_query($conn2, "select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and  useraccess.menu like '%Bank%' and useraccess.menu like '%Approval%' group by username");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if ($id == '41') {
            echo '
            <a href="AP/approve-pv.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Payment Voucher</span>
            </a> ';
            }elseif($id == '42'){
            echo '
            <a href="AP/approve-inbank.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Incoming Bank</span>
            </a> ';
            }elseif($id == '43'){
            echo '
           <a href="AP/approve-outbank.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Outgoing bank</span>
            </a> ';
            }elseif($id == '41,42'){
            echo '
            <a href="AP/approve-pv.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Payment Voucher</span>
            </a>
            <a href="AP/approve-inbank.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Incoming Bank</span>
            </a> ';
            }elseif($id == '41,43'){
            echo '
            <a href="AP/approve-pv.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Payment Voucher</span>
            </a>
            <a href="AP/approve-outbank.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Outgoing bank</span>
            </a> ';
            }elseif($id == '42,43'){
            echo '
            <a href="AP/approve-inbank.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Incoming Bank</span>
            </a>
            <a href="AP/approve-outbank.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Outgoing bank</span>
            </a>';
            }elseif($id == '41,42,43'){
            echo '
            <a href="AP/approve-pv.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Payment Voucher</span>
            </a>
            <a href="AP/approve-inbank.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Incoming Bank</span>
            </a>
            <a href="AP/approve-outbank.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Outgoing bank</span>
            </a> ';
        }else{
                echo '';
            }
       
            ?> 
        </div>
            </div>

<!-- END MENU BANK -->
<!-- START MENU CASH -->

            <a href="#menu4" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-money fa-fw mr-3"></span>
                    <span class="menu-collapsed">Cash</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="menu4" class="collapse sidebar-submenu">

            <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Cash'");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if($id == '38'){
                    echo '
                <a href="AP/cash-in.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-sign-in fa-fw mr-3"></span>
                    <span class="menu-collapsed">Cash In</span>
                </a>
                <a href="AP/cash-out.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-sign-out fa-fw mr-3"></span>
                    <span class="menu-collapsed">Cash Out</span>
                </a>
                <a href="AP/petty-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-credit-card fa-fw mr-3"></span>
                    <span class="menu-collapsed">Petty Cash In</span>
                </a>
                <a href="AP/petty-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-credit-card fa-fw mr-3"></span>
                    <span class="menu-collapsed">Petty Cash Out</span>
                </a>
                <a href="AP/cashreport.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-file-excel-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">Report Cash</span>
                </a>
                </div>

                <div id="menu4" class="collapse sidebar-submenu">
            <a href="#submenu10" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-thumbs-up fa-fw mr-3"></span>
                    <span class="menu-collapsed">Approval</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <div id="submenu10" class="collapse sidebar-submenu">';
            ?>
            <?php
            $querys = mysqli_query($conn2, "select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and  useraccess.menu like '%Cash%' and useraccess.menu like '%Approval%' group by username");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if ($id == '44') {
            echo '
            <a href="AP/approve-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash In</span>
            </a> ';
            }elseif($id == '45'){
            echo '
             <a href="AP/approve-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash Out</span>
            </a> ';
            }elseif($id == '46'){
            echo '
           <a href="AP/approve-petty-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash In</span>
            </a> ';
            }elseif($id == '47'){
            echo '
           <a href="AP/approve-petty-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash Out</span>
            </a> ';
            }elseif($id == '44,45'){
            echo '
            <a href="AP/approve-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash In</span>
            </a>
            <a href="AP/approve-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash Out</span>
            </a>';
            }elseif($id == '44,46'){
            echo '
            <a href="AP/approve-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash In</span>
            </a>
            <a href="AP/approve-petty-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash In</span>
            </a> ';
            }elseif($id == '44,47'){
            echo '
            <a href="AP/approve-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash In</span>
            </a>
            <a href="AP/approve-petty-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash Out</span>
            </a> ';
            }elseif($id == '45,46'){
            echo '
            <a href="AP/approve-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash Out</span>
            </a>
            <a href="AP/approve-petty-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash In</span>
            </a>';
            }elseif($id == '45,47'){
            echo '
            <a href="AP/approve-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash Out</span>
            </a>
           <a href="AP/approve-petty-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash Out</span>
            </a> ';
            }elseif($id == '46,47'){
            echo '
            <a href="AP/approve-petty-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash In</span>
            </a>
            <a href="AP/approve-petty-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash Out</span>
            </a> ';
            }elseif($id == '44,45,46'){
            echo '
            <a href="AP/approve-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash In</span>
            </a>
            <a href="AP/approve-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash Out</span>
            </a>
            <a href="AP/approve-petty-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash In</span>
            </a> ';
            }elseif($id == '44,45,47'){
            echo '
            <a href="AP/approve-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash In</span>
            </a>
            <a href="AP/approve-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash Out</span>
            </a>
            <a href="AP/approve-petty-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash Out</span>
            </a> ';
            }elseif($id == '44,46,47'){
            echo '
            <a href="AP/approve-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash In</span>
            </a>
            <a href="AP/approve-petty-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash In</span>
            </a>
            <a href="AP/approve-petty-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash Out</span>
            </a> ';
            }elseif($id == '45,46,47'){
            echo '
            <a href="AP/approve-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash Out</span>
            </a>
            <a href="AP/approve-petty-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash In</span>
            </a>
            <a href="AP/approve-petty-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash Out</span>
            </a> ';
            }elseif($id == '44,45,46,47'){
            echo '
            <a href="AP/approve-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash In</span>
            </a>
            <a href="AP/approve-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Cash Out</span>
            </a>
            <a href="AP/approve-petty-cashin.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash In</span>
            </a>
            <a href="AP/approve-petty-cashout.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "padding-left : 20px;" class="fa fa-thumbs-up fa-fw mr-3"></span>
                <span class="menu-collapsed">Petty Cash Out</span>
            </a> ';
            }else{
                echo '';
            }
       
            echo'</div>
                ';
                }else{
                    echo '';
            }
            ?>    
            </div>   

<!-- END MENU CASH -->
<!-- START MENU ACCOUNTING -->

            <a href="#menu5" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-bar-chart fa-fw mr-3"></span>
                    <span class="menu-collapsed">Accounting</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="menu5" class="collapse sidebar-submenu">
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
               echo'<a href="AP/memorial-journal.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-bars fa-fw mr-3"></span>
                    <span class="menu-collapsed">Memorial Journal</span>
                </a>';
            }if(strpos($id, '51') !== false){ 
               echo'<a href="AP/list-journal.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-list-alt fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Journal</span>
                </a>';
            }if(strpos($id, '52') !== false){ 
               echo'<a href="AP/general-ledger.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-print fa-list mr-3"></span>
                    <span class="menu-collapsed">General Ledger</span>
                </a>';
            }
            echo '</div>
                <div id="menu5" class="collapse sidebar-submenu">
            <a href="#submenu12" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-list-ul fa-fw mr-3"></span>
                    <span class="menu-collapsed">Sub Ledger</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <div id="submenu12" class="collapse sidebar-submenu">';
            if(strpos($id, '64') !== false){
            echo'<a href="AP/other_receivable_report.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-fax fa-fw mr-3"></span>
                    <span class="menu-collapsed">Other Receivable</span>
                </a>';
            }
            if(strpos($id, '65') !== false){
            echo'<a href="AP/other_payable_report.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-fax fa-fw mr-3"></span>
                    <span class="menu-collapsed">Other Payable</span>
                </a>';
            }
            echo'</div>';
            if(strpos($id, '53') !== false){ 
               echo'</div>
                <div id="menu5" class="collapse sidebar-submenu">
            <a href="#submenu11" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-balance-scale fa-fw mr-3"></span>
                    <span class="menu-collapsed">Financial Statement</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <div id="submenu11" class="collapse sidebar-submenu">
                <a href="AP/trial-balance-ytd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-calendar fa-fw mr-3"></span>
                    <span class="menu-collapsed">Year To Date</span>
                </a>
                <a href="AP/trial-balance-monthly.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style="padding-left: 20px;" class="fa fa-calendar-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">Monthly</span>
                </a>
                </div>';
            }

            }

            ?>
            </div>

<!-- END MENU ACCOUNTING -->

            <li class="list-group-item sidebar-separator-title text-muted d-flex align-items-center menu-collapsed">
                  <span class="fa fa-puzzle-piece fa-lg mr-3"></span>
                <small style="font-size: 15px;font-family:Trebuchet MS;"><b>MAINTAIN MENU</b></small>
            </li>
<!-- START MENU MAINTAIN AP -->
            <a href="#menu6" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa fa-paypal fa-fw mr-3"></span>
                    <span class="menu-collapsed">AP Maintain</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <div id="menu6" class="collapse sidebar-submenu">
            <?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Reverse'");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if($id == '34'){
                    echo '<a href="#submenu13" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-refresh fa-fw mr-2"></span>
                    <span class="menu-collapsed" style="font-size: 14px;">Reverse</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu13" class="collapse sidebar-submenu">
            <a href="AP/formreversebpb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">BPB</span>
                </a>
             
                </div>';
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
                    echo '<a href="#submenu12" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-money fa-fw mr-2"></span>
                    <span class="menu-collapsed" style="font-size: 14px;">Maintain FTR</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu12" class="collapse sidebar-submenu">
            <a href="AP/pengajuan_ftrcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">FTR CBD</span>
                </a>
                <a href="AP/pengajuan_ftrdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-minus-square-o fa-fw mr-3"></span>
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
                    echo '<a href="#submenu14" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-btc fa-fw mr-2"></span>
                    <span class="menu-collapsed" style="font-size: 14px;">Maintain Kontra Bon</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu14" class="collapse sidebar-submenu">
            <a href="AP/pengajuankb.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed"> Kontrabon Reg</span>
                </a>
                <a href="AP/pengajuankb_cbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">Kontrabon CBD</span>
                </a>
                <a href="AP/pengajuankb_dp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-minus-square-o fa-fw mr-3"></span>
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
                    echo '<a href="#submenu15" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span style = "Padding-left: 5px;" class="fa fa-usd fa-fw mr-2"></span>
                    <span class="menu-collapsed" style="font-size: 14px;">Maintain List Payment</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id="submenu15" class="collapse sidebar-submenu">
            <a href="AP/pengajuanpayment.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span style = "Padding-left: 20px;" class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment Reg</span>
                </a>
                <a href="AP/pengajuanpaymentcbd.php" class="list-group-item list-group-item-action bg-dark text-white">
                        <span style = "Padding-left: 20px;" class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment CBD</span>
                </a>
                <a href="AP/pengajuanpaymentdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                        <span style = "Padding-left: 20px;" class="fa fa-minus-square-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">List Payment DP</span>
                </a>
             
                </div>';
                }else{
                    echo '';
            }
            ?>

        </div>

<!-- END MENU MAINTAIN AP -->

            <li class="list-group-item sidebar-separator-title text-muted d-flex align-items-center menu-collapsed">
                <span class="fa fa-cogs fa-lg mr-3"></span>
                <small style="font-size: 15px;font-family:'Trebuchet MS';"><b>OTHER MENU</b></small>
            </li>
            <!-- /END Separator -->

            <?php
            $querys = mysqli_query($conn1,"select Groupp from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = isset($rs['Groupp']) ? $rs['Groupp'] : null;
            
            if($group != 'STAFF' && $group != null){                             
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
            <a href="#top" data-toggle="sidebar-colapse" class="bg-dark list-group-item list-group-item-action d-flex align-items-center">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span id="collapse-icon" class="fa fa-2x mr-3"></span>
                    <span id="collapse-text" class="menu-collapsed">Collapse</span>
                </div>
            </a>
        </ul>
    </div>
    <!-- MAIN -->
    <div class="col p-4" style="background-image: url('../images/banner-nirwana-3.jpg'); height: 650px; background-size: cover; background-repeat: no-repeat;">
        <h1 class="display-4 text-white"><b style="font-family: MV Boli;font-size: 50px;"><?php echo "Welcome $user"?></b></h1>
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
