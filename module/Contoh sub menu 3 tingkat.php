<?php
            $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Master'");
            $rs = mysqli_fetch_array($querys);
            $menu = isset($rs['menu']) ? $rs['menu'] :0;
            $id = isset($rs['id']) ? $rs['id'] :0;

            if($id == '35'){                             
                echo '<a href="#submenu37" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-money fa-fw mr-3"></span>
                    <span class="menu-collapsed">Master</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>

            <!-- Submenu content -->
            <div id="submenu37" class="collapse sidebar-submenu">
            <a href="#submenu38" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-money fa-fw mr-3"></span>
                    <span class="menu-collapsed">Master</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            </div>
            <div id="submenu38" class="collapse sidebar-submenu">
            <a href="../AP/ftrdp.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="fa fa-paper-plane-o fa-fw mr-3"></span>
                <span class="menu-collapsed">FTR DP</span>
            </a>
                </div>';
            }else{
                echo '';
            }
            ?>  