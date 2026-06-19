<?php include '../header.php' ?>

<?php
mysqli_query($conn1, "CREATE TABLE IF NOT EXISTS tbl_closing_periode_fabric_wh (
  id INT(11) NOT NULL AUTO_INCREMENT,
  bulan INT(2) NOT NULL,
  tahun INT(4) NOT NULL,
  tgl_awal DATE NOT NULL,
  tgl_akhir DATE NOT NULL,
  status_closing VARCHAR(10) NOT NULL DEFAULT 'Open',
  keterangan VARCHAR(100) DEFAULT NULL,
  lock_by VARCHAR(50) DEFAULT NULL,
  lock_date DATETIME DEFAULT NULL,
  unlock_by VARCHAR(50) DEFAULT NULL,
  unlock_date DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_bulan_tahun (bulan, tahun)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn1, "CREATE TABLE IF NOT EXISTS tbl_closing_periode_fabric_wh_log (
  id INT(11) NOT NULL AUTO_INCREMENT,
  periode_id INT(11) NOT NULL,
  bulan INT(2) NOT NULL,
  tahun INT(4) NOT NULL,
  status_closing VARCHAR(10) NOT NULL,
  keterangan VARCHAR(100) DEFAULT NULL,
  action_by VARCHAR(50) DEFAULT NULL,
  action_date DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_periode (periode_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$tahun_ini = date('Y');
$tahun_periode = ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tahun_periode']))
    ? (int) $_POST['tahun_periode']
    : (int) $tahun_ini;

$cek = mysqli_query($conn1, "SELECT COUNT(*) as jml FROM tbl_closing_periode_fabric_wh WHERE tahun = '$tahun_periode'");
$row_cek = mysqli_fetch_assoc($cek);
if ($row_cek['jml'] == 0) {
    for ($b = 1; $b <= 12; $b++) {
        $tgl_awal  = date('Y-m-d', mktime(0, 0, 0, $b, 1, $tahun_periode));
        $tgl_akhir = date('Y-m-d', mktime(0, 0, 0, $b + 1, 0, $tahun_periode));
        mysqli_query($conn1, "INSERT INTO tbl_closing_periode_fabric_wh (bulan, tahun, tgl_awal, tgl_akhir, status_closing) VALUES ('$b','$tahun_periode','$tgl_awal','$tgl_akhir','Open')");
    }
}

// Count of status changes per period (for the History badge)
$history_count = [];
$cnt_sql = mysqli_query($conn1, "SELECT periode_id, COUNT(*) as cnt FROM tbl_closing_periode_fabric_wh_log WHERE tahun = '$tahun_periode' GROUP BY periode_id");
while ($r = mysqli_fetch_assoc($cnt_sql)) {
    $history_count[$r['periode_id']] = (int) $r['cnt'];
}

// Period rows for the selected year
$rows_periode = [];
$count_open = 0;
$count_closed = 0;
$sql = mysqli_query($conn1, "SELECT id, bulan, tahun, status_closing, tgl_awal, keterangan,
        COALESCE(lock_by, unlock_by) as updated_by,
        COALESCE(lock_date, unlock_date) as updated_date
    FROM tbl_closing_periode_fabric_wh WHERE tahun = '$tahun_periode' ORDER BY bulan ASC");
while ($row = mysqli_fetch_assoc($sql)) {
    if ($row['status_closing'] == 'Closed') {
        $count_closed++;
    } else {
        $count_open++;
    }
    $rows_periode[] = $row;
}
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fas fa-calendar-times" aria-hidden="true"></i> CLOSING PERIOD - FABRIC WAREHOUSE</h5>
    </div>

    <div class="card-body p-3">
      <form id="form-data" action="ca_fabric_closing_periode.php" method="post">
        <div class="row g-3 align-items-end">
          <div class="col-md-2">
            <label for="tahun_periode" class="form-label"><b>Period Year</b></label>
            <select class="form-control form-control-sm selectpicker" name="tahun_periode" id="tahun_periode" data-live-search="true" onchange="this.form.submit()">
              <?php
              for ($y = $tahun_ini + 1; $y >= $tahun_ini - 3; $y--) {
                  $sel = ($y == $tahun_periode) ? ' selected' : '';
                  echo "<option value=\"$y\"$sel>$y</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-10 mb-1">
            <span class="badge badge-pill badge-success p-2 mr-2"><i class="fas fa-unlock"></i> Open : <?= $count_open ?> Months</span>
            <span class="badge badge-pill badge-danger p-2"><i class="fas fa-lock"></i> Closed : <?= $count_closed ?> Months</span>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Card Table -->
  <div class="card shadow border-0 mt-4">
    <div class="card-body p-3">
      <div class="table-responsive">
        <table id="table-closing" class="table table-striped table-bordered table-hover table-sm align-middle" style="width:100%">
          <thead class="table-gradient text-white">
            <tr>
              <th style="text-align: center;vertical-align: middle;">Month</th>
              <th style="text-align: center;vertical-align: middle;">Year</th>
              <th style="text-align: center;vertical-align: middle;">Status</th>
              <th style="text-align: center;vertical-align: middle;">Updated By</th>
              <th style="text-align: center;vertical-align: middle;">Updated Date</th>
              <th style="text-align: center;vertical-align: middle;">History</th>
              <th style="text-align: center;vertical-align: middle;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($rows_periode as $row) {
                $status_target = $row['status_closing'] == 'Closed' ? 'Open' : 'Closed';
                $btn_class     = $status_target == 'Open' ? 'btn-info' : 'btn-warning';
                $icon_class    = $status_target == 'Open' ? 'fa-unlock' : 'fa-lock';
                $updated_date  = !empty($row['updated_date']) ? date("d-M-Y H:i:s", strtotime($row['updated_date'])) : '-';
                $hist_count    = $history_count[$row['id']] ?? 0;
                $bulan_label   = date("F", strtotime($row['tgl_awal']));
                $is_current    = ($row['bulan'] == (int) date('n') && $row['tahun'] == (int) date('Y'));
                $row_class     = $is_current ? ' current-period' : '';
                $current_badge = $is_current ? ' <span class="badge badge-current">Current</span>' : '';

                if ($row['status_closing'] == 'Closed') {
                    $status_badge = '<span class="badge badge-danger p-2"><i class="fas fa-lock"></i> Closed</span>';
                } else {
                    $status_badge = '<span class="badge badge-success p-2"><i class="fas fa-unlock"></i> Open</span>';
                }

                echo '<tr class="'.$row_class.'" style="font-size:13px;text-align:center;">
                    <td><b>'.$bulan_label.'</b>'.$current_badge.'</td>
                    <td>'.$row['tahun'].'</td>
                    <td>'.$status_badge.'</td>
                    <td>'.($row['updated_by'] ?: '-').'</td>
                    <td>'.$updated_date.'</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-history"
                            data-id="'.$row['id'].'"
                            data-label="'.$bulan_label.' '.$row['tahun'].'">
                            <i class="fas fa-history"></i> History <span class="badge badge-secondary">'.$hist_count.'</span>
                        </button>
                    </td>
                    <td>
                        <button class="btn btn-sm '.$btn_class.' open-status-confirm"
                            data-id="'.$row['id'].'"
                            data-status="'.$status_target.'"
                            data-keterangan="'.htmlspecialchars($row['keterangan'] ?? '').'"
                            data-action="'.$status_target.'">
                            <i class="fas '.$icon_class.'"></i> '.$status_target.'
                        </button>
                    </td>
                </tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal History -->
<div class="modal fade" id="modalHistory" tabindex="-1" role="dialog" aria-labelledby="modalHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="modal-title" id="modalHistoryLabel"><i class="fas fa-history"></i> Status Change History</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="d-flex align-items-center mb-3 p-2 px-3 rounded" style="background:#eaf4ff;">
          <i class="fas fa-box-open fa-2x mr-3" style="color:#1e90ff;"></i>
          <div>
            <div class="text-muted" style="font-size:0.75rem;letter-spacing:.05em;">PERIOD</div>
            <h5 class="mb-0" id="historyPeriodeLabel">-</h5>
          </div>
        </div>
        <div id="history-body" style="max-height:380px;overflow-y:auto;">
          <div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- CSS -->
<style>
  .table-gradient th {
      background: #1E3A8A;
      color: #fff;
      text-align: center;
      vertical-align: middle;
  }
  .badge-pill {
      font-size: 0.85rem;
  }

  /* Card polish */
  .card.shadow {
      border-radius: 12px;
      transition: box-shadow .2s ease-in-out;
  }
  .card.shadow:hover {
      box-shadow: 0 .75rem 1.5rem rgba(30,144,255,.15) !important;
  }
  .card.shadow > .card-header:first-child {
      border-radius: 12px 12px 0 0;
  }
  .card.shadow > .card-body:last-child {
      border-radius: 0 0 12px 12px;
  }

  /* Table polish */
  #table-closing tbody tr:hover {
      background-color: #f5faff !important;
  }
  #table-closing tbody tr.current-period {
      background-color: #eaf4ff !important;
  }
  .badge-current {
      background: #1e90ff;
      color: #fff;
      font-size: .65rem;
      vertical-align: middle;
  }
  .open-status-confirm, .btn-history {
      transition: transform .15s ease-in-out;
  }
  .open-status-confirm:hover, .btn-history:hover {
      transform: translateY(-1px);
  }

  /* History timeline */
  .timeline {
      list-style: none;
      padding-left: 0;
      margin: 0;
      position: relative;
  }
  .timeline::before {
      content: '';
      position: absolute;
      top: 6px;
      bottom: 6px;
      left: 17px;
      width: 2px;
      background: #e3e8f0;
  }
  .timeline-item {
      position: relative;
      padding: 0 0 18px 48px;
  }
  .timeline-item:last-child {
      padding-bottom: 0;
  }
  .timeline-icon {
      position: absolute;
      left: 0;
      top: 0;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: .9rem;
      box-shadow: 0 0 0 4px #fff;
      z-index: 1;
  }
  .timeline-icon.bg-success { background: #28a745; }
  .timeline-icon.bg-danger { background: #dc3545; }
  .timeline-content {
      background: #f8f9fa;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 10px 14px;
  }
  .timeline-remark {
      font-size: .85rem;
      color: #555;
      margin-top: 6px;
      padding-top: 6px;
      border-top: 1px dashed #dee2e6;
  }
</style>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

      var SeparatorTitle = $('.sidebar-separator-title');
      if ( SeparatorTitle.hasClass('d-flex') ) {
          SeparatorTitle.removeClass('d-flex');
      } else {
          SeparatorTitle.addClass('d-flex');
      }

      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>

<script>
  $(document).ready(function() {
      $('#table-closing').DataTable({
          paging: false,
          searching: false,
          info: false,
          ordering: false
      });

      $('.selectpicker').selectpicker();
  });
</script>

<script>
  function escapeHtml(str) {
      return String(str).replace(/[&<>"']/g, function (ch) {
          return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.open-status-confirm').forEach(button => {
          button.addEventListener('click', function () {
              const id = this.dataset.id;
              const status = this.dataset.status;
              const keterangan = this.dataset.keterangan || '';
              const action = this.dataset.action;
              const update_user = '<?php echo $user ?>';

              let title = '';
              let confirmButtonText = '';
              let icon = '';

              const swalOptions = {
                  showCancelButton: true,
                  confirmButtonColor: '#1e90ff',
                  cancelButtonColor: '#aaa'
              };

              if (action === 'Closed') {
                  swalOptions.title = 'Are you sure you want to close this period?';
                  swalOptions.text = "Status will be changed to: " + status;
                  swalOptions.icon = 'warning';
                  swalOptions.confirmButtonText = 'Yes, Close';
              } else {
                  swalOptions.title = 'Reopen this period?';
                  swalOptions.icon = 'question';
                  swalOptions.confirmButtonText = 'Yes, Open';
                  swalOptions.input = 'textarea';
                  swalOptions.inputLabel = 'Remarks (optional) - why is this period being reopened?';
                  swalOptions.inputPlaceholder = 'e.g. correction for stock adjustment...';
                  swalOptions.inputAttributes = { 'aria-label': 'Remarks' };
              }

              Swal.fire(swalOptions).then((result) => {
                  if (result.isConfirmed) {
                      const remarkValue = action === 'Open' ? (result.value || '').trim() : keterangan;

                      fetch('update_status_closing_fabric.php', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/x-www-form-urlencoded'
                          },
                          body: new URLSearchParams({
                              id: id,
                              status: status,
                              keterangan: remarkValue,
                              update_user: update_user
                          })
                      })
                      .then(res => res.json())
                      .then(res => {
                          if (res.success) {
                              Swal.fire({
                                  icon: 'success',
                                  title: 'Success!',
                                  text: 'Status updated successfully',
                                  timer: 1500,
                                  showConfirmButton: false
                              }).then(() => {
                                  window.location.reload();
                              });
                          } else {
                              Swal.fire({
                                  icon: 'error',
                                  title: 'Failed!',
                                  text: res.message || 'An error occurred'
                              });
                          }
                      });
                  }
              });
          });
      });

      document.querySelectorAll('.btn-history').forEach(button => {
          button.addEventListener('click', function () {
              const id = this.dataset.id;
              const label = this.dataset.label;

              document.getElementById('historyPeriodeLabel').textContent = label;
              document.getElementById('history-body').innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
              $('#modalHistory').modal('show');

              fetch('get_history_closing_fabric.php?id=' + id)
                  .then(res => res.json())
                  .then(res => {
                      if (!res.length) {
                          document.getElementById('history-body').innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2"></i><br>No history yet</div>';
                          return;
                      }
                      let html = '<ul class="timeline">';
                      res.forEach((r) => {
                          const isClosed = r.status_closing === 'Closed';
                          const iconBg = isClosed ? 'bg-danger' : 'bg-success';
                          const icon   = isClosed ? 'fa-lock' : 'fa-unlock';
                          const badge  = isClosed ? 'badge-danger' : 'badge-success';

                          html += '<li class="timeline-item">'
                              + '<span class="timeline-icon ' + iconBg + '"><i class="fas ' + icon + '"></i></span>'
                              + '<div class="timeline-content">'
                              + '<div class="d-flex justify-content-between align-items-center flex-wrap">'
                              + '<span class="badge ' + badge + ' p-2"><i class="fas ' + icon + '"></i> ' + r.status_closing + '</span>'
                              + '<small class="text-muted"><i class="far fa-clock mr-1"></i>' + r.action_date_fmt + '</small>'
                              + '</div>'
                              + '<div class="mt-2"><i class="fas fa-user-circle mr-1 text-secondary"></i><b>' + escapeHtml(r.action_by || '-') + '</b></div>'
                              + (r.keterangan ? '<div class="timeline-remark"><i class="fas fa-comment-dots mr-1"></i>' + escapeHtml(r.keterangan) + '</div>' : '')
                              + '</div>'
                              + '</li>';
                      });
                      html += '</ul>';
                      document.getElementById('history-body').innerHTML = html;
                  });
          });
      });
  });
</script>

</body>

</html>
