<?php include '../header.php' ?>

<style type="text/css">

    /* Collapsible card headers (BPB/RO/FTR/Potongan/etc across all PV tabs):
       Bootstrap toggles ".collapsed" on any [data-toggle=collapse] trigger
       automatically, so the chevron can flip via pure CSS - no JS needed. */
    [data-toggle="collapse"] .fa-chevron-up {
        transition: transform 0.2s ease;
    }
    [data-toggle="collapse"].collapsed .fa-chevron-up {
        transform: rotate(180deg);
    }

    .pv-tab-nav {
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px;
        margin: 1.25rem 1rem 0 1rem;
        background: linear-gradient(135deg, #eef2f9, #f7f9fc);
        border-radius: 16px;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.06);
    }

    .pv-tab-nav .nav-link {
        border: none;
        background: #ffffff;
        color: #5a6473;
        padding: 10px 20px;
        font-size: 0.88rem;
        font-weight: 600;
        letter-spacing: 0.2px;
        border-radius: 30px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.25s ease;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
    }

    .pv-tab-nav .nav-link i {
        font-size: 0.95rem;
        opacity: 0.85;
    }

    .pv-tab-nav .nav-link:hover {
        background: #e8f1ff;
        color: #0056b3;
        transform: translateY(-2px);
    }

    .pv-tab-nav .nav-link.active {
        background: linear-gradient(135deg, #1e90ff, #191970);
        color: #fff;
        box-shadow: 0 6px 16px rgba(25, 25, 112, 0.35);
        transform: translateY(-1px);
    }

    .pv-tab-nav .nav-link.active i {
        opacity: 1;
    }

    .pv-coming-soon {
        text-align: center;
        padding: 60px 20px;
    }

    .pv-coming-soon .pv-icon-circle {
        width: 72px;
        height: 72px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: linear-gradient(135deg, #eef2f9, #dce7fb);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #1e90ff;
    }

    .pv-coming-soon h6 {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 6px;
    }

    .pv-coming-soon .badge-coming-soon {
        display: inline-block;
        margin-top: 12px;
        padding: 5px 14px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #fff;
        background: linear-gradient(135deg, #1e90ff, #191970);
        border-radius: 20px;
    }
</style>


<!-- Bootstrap core JavaScript (loaded before tab content since the tab partials run inline jQuery on include) -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<ul class="nav pv-tab-nav" id="pvTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="pv-regular-tab" data-toggle="tab" href="#pv_regular" role="tab" aria-controls="pv_regular" aria-selected="true"><i class="fa fa-file-text-o"></i> Regular</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pv-installment-tab" data-toggle="tab" href="#pv_installment" role="tab" aria-controls="pv_installment" aria-selected="false"><i class="fa fa-calendar-check-o"></i> Installment</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pv-dp-tab" data-toggle="tab" href="#pv_dp" role="tab" aria-controls="pv_dp" aria-selected="false"><i class="fa fa-money"></i> DP</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pv-cbd-tab" data-toggle="tab" href="#pv_cbd" role="tab" aria-controls="pv_cbd" aria-selected="false"><i class="fa fa-exchange"></i> CBD</a>
    </li>
</ul>

<div class="tab-content" id="pvTabContent">
    <div class="tab-pane fade show active" id="pv_regular" role="tabpanel" aria-labelledby="pv-regular-tab">
        <?php include 'payment_voucher_ap/pv_regular.php'; ?>
    </div>

    <div class="tab-pane fade" id="pv_installment" role="tabpanel" aria-labelledby="pv-installment-tab">
        <?php include 'payment_voucher_ap/pv_installment.php'; ?>
    </div>

    <div class="tab-pane fade" id="pv_dp" role="tabpanel" aria-labelledby="pv-dp-tab">
        <?php include 'payment_voucher_ap/pv_dp.php'; ?>
    </div>

    <div class="tab-pane fade" id="pv_cbd" role="tabpanel" aria-labelledby="pv-cbd-tab">
        <?php include 'payment_voucher_ap/pv_cbd.php'; ?>
    </div>
</div>

<script>
    // The search modals inside each tab do a full page reload (no AJAX), and the
    // markup always defaults to the Regular tab being active - restore whichever
    // tab the user was on before reloading, and remember future tab switches.
    $(document).ready(function () {
        var lastPvTab = localStorage.getItem('pv_active_tab');
        if (lastPvTab && $('#' + lastPvTab).length) {
            $('#' + lastPvTab).tab('show');
        }
    });

    $('#pvTab a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('pv_active_tab', $(e.target).attr('id'));

        // DataTables initialized while its tab-pane is hidden (display:none)
        // can't measure column widths correctly - recalculate now that it's visible.
        if ($.fn.dataTable) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        }
    });
</script>

</div><!-- body-row END -->
</div>
</div>

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

</body>

</html>
