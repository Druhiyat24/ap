<?php include '../header.php' ?>

<style type="text/css">
    label { font-size: 14px; }

    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        border-bottom: none;
    }

    #datatable { border-collapse: separate; border-spacing: 0; }
    #datatable thead th:first-child { border-top-left-radius: 10px; }
    #datatable thead th:last-child { border-top-right-radius: 10px; }
    #datatable tbody td { vertical-align: middle; padding: 13px 10px; border-color: #eef1f5; }
    #datatable tbody tr { transition: background-color .15s ease; }
    #datatable tbody tr:hover { background-color: #f5f8ff; }

    .header-sub { font-size: 11.5px; font-weight: 400; opacity: .85; margin-top: 1px; }

    .user-cell { display: flex; align-items: center; gap: 10px; }

    .user-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        color: #fff;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,.2);
    }

    .username-text { font-weight: 700; color: #1e293b; font-size: 13px; }

    div.dataTables_wrapper .dataTables_paginate { float: right; margin-top: 10px; }
    div.dataTables_wrapper .dataTables_info { float: left; margin-top: 10px; }

    .select2-selection--single{ height: calc(1.5em + .75rem + 2px) !important; padding:.375rem .75rem !important; display:flex !important; align-items:center; }
    .select2-selection--single .select2-selection__rendered{ line-height:1.5 !important; padding-left:0 !important; font-size:14px; }
    .select2-selection--single .select2-selection__arrow{ height: calc(1.5em + .75rem) !important; top:0 !important; }

    .kbon-action-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }

    .kbon-action-buttons .btn {
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 11px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .kbon-action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.25);
    }

    .menu-badge {
        display: inline-block;
        background: #eef2ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
        border-radius: 12px;
        padding: 2px 9px;
        font-size: 11px;
        font-weight: 600;
        margin: 2px 3px 2px 0;
        white-space: nowrap;
    }

    .menu-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #1e3a8a;
        color: #fff;
        font-weight: 700;
        font-size: 12px;
    }

    #modalManageRole .modal-dialog { max-width: 1180px; }
    #modalManageRole .modal-content { border-radius: 14px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,.25); border: none; }
    #modalManageRole .modal-header { background: linear-gradient(135deg, #172554, #2563eb); color: #fff; border: none; }
    #modalManageRole .modal-header .close { color: #fff; opacity: .85; text-shadow: none; }
    #modalManageRole label { font-size: 12px; text-transform: uppercase; letter-spacing: .03em; font-weight: 700; color: #475569; }

    .menu-toggle-panel {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        max-height: 440px;
        overflow-y: auto;
        padding: 12px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 8px;
        align-content: start;
        background: #f8fafc;
    }

    .menu-toggle-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 9px 12px;
        font-size: 13px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #cbd5e1;
        border-radius: 8px;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .menu-toggle-row .menu-label { display: flex; align-items: flex-start; gap: 8px; min-width: 0; flex: 1; }
    .menu-toggle-row .menu-label i { flex-shrink: 0; font-size: 12px; opacity: .85; margin-top: 2px; }
    /* Jangan truncate: nama penuh, wrap ke baris ke-2 kalau kepanjangan. */
    .menu-toggle-row .menu-label span { white-space: normal; overflow-wrap: anywhere; line-height: 1.3; }
    .menu-toggle-row:hover { border-color: #93c5fd; box-shadow: 0 2px 6px rgba(37,99,235,.18); transform: translateY(-1px); }
    .menu-toggle-row.is-on { background: #f8faff; box-shadow: 0 1px 3px rgba(0,0,0,.06); }

    /* Pemisah per bagian (grup navbar) - full-width di dalam grid 2 kolom. */
    .menu-section-header {
        grid-column: 1 / -1;
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 11px 0 1px;
        padding: 0 2px;
        background: none;
        border: none;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .menu-section-header:first-child { margin-top: 2px; }
    .menu-section-header i { font-size: 12px; flex-shrink: 0; }
    /* garis tipis memanjang setelah label */
    .menu-section-header::after {
        content: "";
        order: 1;
        flex: 1;
        height: 1px;
        background: currentColor;
        opacity: .22;
    }
    .menu-section-header .menu-section-count {
        order: 2;
        margin-left: 6px;
        color: #94a3b8;
        font-size: 10px;
        font-weight: 600;
    }

    .menu-category-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
    }

    .menu-category-legend span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
    }

    .menu-category-legend .dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: background-color .2s ease;
        border-radius: 24px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: #fff;
        transition: transform .2s ease;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,.35);
    }

    .toggle-switch input:checked + .toggle-slider { background-color: #2563eb; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }
    .toggle-switch input:focus + .toggle-slider { box-shadow: 0 0 0 3px rgba(37,99,235,.25); }

    .menu-empty {
        padding: 24px;
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Table -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3 d-flex justify-content-between align-items-center"
            style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <div>
                <h5 class="mb-0"><i class="fa fa-users" aria-hidden="true"></i> USER ROLE AP</h5>
                <div class="header-sub" id="headerSub">Loading users...</div>
            </div>
            <button id="btncreate" type="button" class="btn btn-sm btn-light text-primary font-weight-bold"><i class="fa fa-plus-circle"></i> Add User Role</button>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="datatable" class="table table-hover table-gradient" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:16%;">Username</th>
                            <th style="width:20%;">Full Name</th>
                            <th>Menu Access</th>
                            <th style="width:70px;">Total</th>
                            <th style="width:110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Manage Role -->
<div class="modal fade" id="modalManageRole" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-shield"></i> Manage User Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="modal_user_select">User</label>
                        <select class="form-control select2" id="modal_user_select" style="width:100%">
                            <option value="" selected disabled>-- Select a user --</option>
                            <?php
                            $sql = mysqli_query($conn1, "select username, FullName from userpassword order by username ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                echo '<option value="' . htmlspecialchars($row['username']) . '" data-fullname="' . htmlspecialchars($row['FullName']) . '">' . htmlspecialchars($row['username']) . ' - ' . htmlspecialchars($row['FullName']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="modal_menu_search">Search Menu</label>
                        <input type="text" class="form-control" id="modal_menu_search" placeholder="Type to filter...">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
                    <span style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:.03em;">
                        <i class="fa fa-list-ul"></i> Menu List
                    </span>
                    <span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAllMenu"><i class="fa fa-check-square-o"></i> Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearAllMenu"><i class="fa fa-square-o"></i> Clear All</button>
                    </span>
                </div>

                <div class="menu-category-legend" id="menuCategoryLegend"></div>

                <div class="menu-toggle-panel" id="menuTogglePanel">
                    <div class="menu-empty">Select a user to manage their menu access.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn btn-primary" id="btnSaveMenu"><i class="fa fa-floppy-o"></i> Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
    // Hide submenus
    $('#body-row .collapse').collapse('hide');

    // Collapse/Expand icon
    $('#collapse-icon').addClass('fa-angle-double-left');

    // Collapse click
    $('[data-toggle=sidebar-colapse]').click(function() {
        SidebarCollapse();
    });

    function SidebarCollapse() {
        $('.menu-collapsed').toggleClass('d-none');
        $('.sidebar-submenu').toggleClass('d-none');
        $('.submenu-icon').toggleClass('d-none');
        $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

        var SeparatorTitle = $('.sidebar-separator-title');
        if (SeparatorTitle.hasClass('d-flex')) {
            SeparatorTitle.removeClass('d-flex');
        } else {
            SeparatorTitle.addClass('d-flex');
        }

        $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
    }
</script>

<script>
    $(function() {
        $('.select2').select2({ theme: 'bootstrap4', dropdownAutoWidth: true });
        $('#modal_user_select').select2({ theme: 'bootstrap4', dropdownAutoWidth: true, dropdownParent: $('#modalManageRole') });
    });

    var datatable;
    var currentModalUsername = '';
    var currentModalFullname = '';

    var AVATAR_COLORS = ['#2563eb', '#7c3aed', '#0891b2', '#059669', '#d97706', '#dc2626', '#db2777', '#4f46e5'];

    function avatarColor(text) {
        var sum = 0;
        for (var i = 0; i < text.length; i++) { sum += text.charCodeAt(i); }
        return AVATAR_COLORS[sum % AVATAR_COLORS.length];
    }

    function avatarInitials(text) {
        return (text || '?').substring(0, 2).toUpperCase();
    }

    $(document).ready(function() {
        datatable = $('#datatable').DataTable({
            ordering: true,
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            data: [],
            columns: [
                {
                    data: 'username',
                    render: function(username) {
                        return '<div class="user-cell">' +
                            '<span class="user-avatar" style="background:' + avatarColor(username) + ';">' + avatarInitials(username) + '</span>' +
                            '<span class="username-text">' + username + '</span>' +
                            '</div>';
                    }
                },
                { data: 'fullname' },
                {
                    data: 'menus', orderable: false,
                    render: function(menus) {
                        if (!menus || menus.length === 0) {
                            return '<span class="text-muted" style="font-size:12px;">No menu assigned</span>';
                        }
                        var MAX_SHOWN = 4;
                        var shown = menus.slice(0, MAX_SHOWN).map(function(m) { return '<span class="menu-badge">' + m + '</span>'; }).join('');
                        if (menus.length <= MAX_SHOWN) {
                            return shown;
                        }
                        var hidden = menus.slice(MAX_SHOWN).map(function(m) { return '<span class="menu-badge">' + m + '</span>'; }).join('');
                        return '<span class="menu-badge-shown">' + shown + '</span>' +
                            '<span class="menu-badge-hidden" style="display:none;">' + hidden + '</span>' +
                            '<span class="menu-badge menu-badge-more" style="cursor:pointer;background:#1e3a8a;color:#fff;">+' + (menus.length - MAX_SHOWN) + ' more</span>';
                    }
                },
                {
                    data: 'menu_count', orderable: true, className: 'text-center',
                    render: function(count) {
                        return '<span class="menu-count-badge">' + count + '</span>';
                    }
                },
                {
                    data: null, orderable: false, className: 'text-center',
                    render: function(row) {
                        return '<button type="button" class="btn btn-sm btn-primary btn-manage-role" data-username="' + row.username + '" data-fullname="' + row.fullname + '"><i class="fa fa-cog"></i> Manage</button>';
                    }
                }
            ]
        });

        $("[data-toggle=tooltip]").tooltip();
        loadUserRole();
    });

    function loadUserRole() {
        $.ajax({
            type: 'POST',
            url: 'ajx_userrole.php',
            data: { nama_supp: 'ALL' },
            dataType: 'json',
            success: function(json) {
                datatable.clear();
                datatable.rows.add(json.data);
                datatable.draw();

                var totalMenus = json.data.reduce(function(sum, row) { return sum + row.menu_count; }, 0);
                $('#headerSub').text(json.data.length + ' user(s) with role · ' + totalMenus + ' menu assignment(s) total');
            }
        });
    }

    function openManageModal(username, fullname) {
        currentModalUsername = username || '';
        currentModalFullname = fullname || '';
        $('#modal_menu_search').val('');
        $('#modalManageRole').modal('show');

        if (currentModalUsername) {
            $('#modal_user_select').val(currentModalUsername).trigger('change');
        } else {
            $('#modal_user_select').val(null).trigger('change');
            $('#menuTogglePanel').html('<div class="menu-empty">Select a user to manage their menu access.</div>');
        }
    }

    $('#btncreate').on('click', function() {
        openManageModal('', '');
    });

    $('#datatable').on('click', '.menu-badge-more', function() {
        var $btn = $(this);
        var $cell = $btn.closest('td');
        var $hidden = $cell.find('.menu-badge-hidden');

        if (!$btn.data('label')) {
            $btn.data('label', $btn.text());
        }

        if ($hidden.is(':visible')) {
            $hidden.hide();
            $btn.text($btn.data('label'));
        } else {
            $hidden.show();
            $btn.text('Show less');
        }
    });

    $('#datatable').on('click', '.btn-manage-role', function() {
        var $btn = $(this);
        openManageModal($btn.data('username'), $btn.data('fullname'));
    });

    $('#modal_user_select').on('change', function() {
        var username = $(this).val();
        if (!username) {
            return;
        }
        currentModalUsername = username;
        currentModalFullname = $(this).find(':selected').data('fullname') || '';
        loadMenuToggles();
    });

    function loadMenuToggles() {
        $('#menuTogglePanel').html('<div class="menu-empty">Loading...</div>');

        $.ajax({
            type: 'POST',
            url: 'ajx_user_menu.php',
            data: { username: currentModalUsername },
            dataType: 'json',
            success: function(json) {
                renderMenuToggles(json.menus);
            },
            error: function() {
                Swal.fire('Error', 'Failed to load menu data', 'error');
            }
        });
    }

    // Grup mengikuti menu navbar atas. Urutan = urutan tampil di legend.
    // Warna/ikon disamakan dgn navbar. `test` (deteksi awalan-nama) hanya
    // fallback kalau menurole.menu_group kosong; utama pakai kolom menu_group.
    var MENU_CATEGORIES = [
        { key: 'Master',          test: /^master/i,           color: '#0ea5e9', icon: 'fa-book' },
        { key: 'AP',              test: /^ap\b/i,             color: '#2563eb', icon: 'fa-paypal' },
        { key: 'Bank',            test: /^bank/i,             color: '#0891b2', icon: 'fa-university' },
        { key: 'Cash',            test: /^cash/i,             color: '#16a34a', icon: 'fa-money' },
        { key: 'Accounting',      test: /^(acct\s*-|fs\s*-)/i, color: '#7c3aed', icon: 'fa-bar-chart' },
        { key: 'Cost Accounting', test: /^cost accounting/i,  color: '#d97706', icon: 'fa-industry' },
        { key: 'Exim',            test: /^exim/i,             color: '#db2777', icon: 'fa-cubes' },
        { key: 'Reverse',         test: /^(reverse|cancel)/i, color: '#dc2626', icon: 'fa-retweet' },
        { key: 'Setting',         test: /^setting/i,          color: '#475569', icon: 'fa-cogs' },
        { key: 'Other',           test: /.*/,                 color: '#94a3b8', icon: 'fa-circle-o' }
    ];

    function categoryOf(menu) {
        for (var i = 0; i < MENU_CATEGORIES.length; i++) {
            if (MENU_CATEGORIES[i].test.test(menu)) {
                return MENU_CATEGORIES[i];
            }
        }
        return MENU_CATEGORIES[MENU_CATEGORIES.length - 1];
    }

    function categoryByKey(key) {
        for (var i = 0; i < MENU_CATEGORIES.length; i++) {
            if (MENU_CATEGORIES[i].key === key) { return MENU_CATEGORIES[i]; }
        }
        return null;
    }

    // Grup eksplisit dari DB (menurole.menu_group) menang; kalau kosong,
    // fallback ke deteksi awalan-nama lama berdasarkan key `menu`.
    function effectiveCategory(item) {
        if (item && item.menu_group) {
            var byKey = categoryByKey(item.menu_group);
            if (byKey) { return byKey; }
        }
        return categoryOf(item.menu);
    }

    // Label formal dari DB (menurole.display_name); fallback ke key `menu`.
    function labelOf(item) {
        return (item && item.display_name) ? item.display_name : item.menu;
    }

    function renderCategoryLegend(menus) {
        var usedKeys = {};
        menus.forEach(function(item) { usedKeys[effectiveCategory(item).key] = true; });

        var $legend = $('#menuCategoryLegend').empty();
        MENU_CATEGORIES.forEach(function(cat) {
            if (!usedKeys[cat.key]) {
                return;
            }
            $legend.append(
                $('<span></span>').append(
                    $('<span class="dot"></span>').css('background', cat.color),
                    $('<span></span>').text(cat.key)
                )
            );
        });
    }

    function renderMenuToggles(menus) {
        var $panel = $('#menuTogglePanel');
        $panel.empty();

        if (!menus || menus.length === 0) {
            $panel.html('<div class="menu-empty">No menu available.</div>');
            $('#menuCategoryLegend').empty();
            return;
        }

        renderCategoryLegend(menus);

        // Kelompokkan per grup navbar, pertahankan urutan item asli (id).
        var grouped = {};
        menus.forEach(function(item) {
            var key = effectiveCategory(item).key;
            (grouped[key] = grouped[key] || []).push(item);
        });

        // Render per grup mengikuti urutan MENU_CATEGORIES (= urutan navbar),
        // tiap grup diawali header pemisah full-width.
        MENU_CATEGORIES.forEach(function(cat) {
            var items = grouped[cat.key];
            if (!items || !items.length) { return; }

            var $header = $('<div class="menu-section-header"></div>')
                .attr('data-group', cat.key)
                .css({ 'border-left-color': cat.color, 'color': cat.color });
            $header.append($('<i class="fa"></i>').addClass(cat.icon));
            $header.append($('<span></span>').text(cat.key));
            $header.append($('<span class="menu-section-count"></span>').text(items.length));
            $panel.append($header);

            items.forEach(function(item) {
                var c = effectiveCategory(item);
                var $row = $('<div class="menu-toggle-row"></div>')
                    .attr('title', item.menu) // tooltip tetap key internal, bantu admin
                    .attr('data-group', cat.key)
                    .css('border-left-color', c.color)
                    .toggleClass('is-on', item.assigned);
                var $label = $('<div class="menu-label"></div>');
                var $icon = $('<i class="fa"></i>').addClass(c.icon).css('color', c.color);
                var $text = $('<span></span>').text(labelOf(item)); // nama formal (fallback ke key)
                $label.append($icon).append($text);
                var $switchLabel = $('<label class="toggle-switch"></label>');
                // value TETAP key internal `menu` - inilah yg disimpan ke useraccess.
                var $input = $('<input type="checkbox" class="chk-menu-toggle">').val(item.menu).prop('checked', item.assigned);
                var $slider = $('<span class="toggle-slider"></span>');
                $switchLabel.append($input).append($slider);
                $row.append($label).append($switchLabel);
                $panel.append($row);
            });
        });
    }

    $('#menuTogglePanel').on('change', '.chk-menu-toggle', function() {
        $(this).closest('.menu-toggle-row').toggleClass('is-on', $(this).is(':checked'));
    });

    $('#modal_menu_search').on('keyup', function() {
        var filter = $(this).val().toUpperCase();
        $('#menuTogglePanel .menu-toggle-row').each(function() {
            // cocokkan nama formal (teks) DAN key internal (title) - admin bisa
            // cari pakai "FS" walau labelnya sudah "Financial Statement ...".
            var text = ($(this).text() + ' ' + ($(this).attr('title') || '')).toUpperCase();
            $(this).toggle(text.indexOf(filter) > -1);
        });
        // Sembunyikan header pemisah kalau semua baris di grupnya ter-filter.
        $('#menuTogglePanel .menu-section-header').each(function() {
            var grp = $(this).attr('data-group');
            var anyVisible = $('#menuTogglePanel .menu-toggle-row[data-group="' + grp + '"]:visible').length > 0;
            $(this).toggle(anyVisible);
        });
    });

    $('#btnSelectAllMenu').on('click', function() {
        $('#menuTogglePanel .menu-toggle-row:visible .chk-menu-toggle').prop('checked', true).trigger('change');
    });

    $('#btnClearAllMenu').on('click', function() {
        $('#menuTogglePanel .menu-toggle-row:visible .chk-menu-toggle').prop('checked', false).trigger('change');
    });

    $('#btnSaveMenu').on('click', function() {
        if (!currentModalUsername) {
            Swal.fire('Warning', 'Please select a user first.', 'warning');
            return;
        }

        var menus = [];
        $('.chk-menu-toggle:checked').each(function() {
            menus.push($(this).val());
        });

        Swal.fire({
            title: 'Save changes?',
            text: 'Menu access for ' + currentModalUsername + ' will be updated.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it',
            confirmButtonColor: '#2563eb'
        }).then(function(result) {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                type: 'POST',
                url: 'save_user_menu.php',
                data: {
                    username: currentModalUsername,
                    fullname: currentModalFullname,
                    menus: JSON.stringify(menus),
                    create_user: '<?php echo $user; ?>'
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Saved', 'Menu access has been updated.', 'success');
                        loadUserRole();
                    } else {
                        Swal.fire('Error', res.error || 'Failed to save.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to save.', 'error');
                }
            });
        });
    });
</script>

</body>

</html>
