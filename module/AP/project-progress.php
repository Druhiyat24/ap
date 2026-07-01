<?php
include '../header.php';

// Only accessible by indro
if ($user !== 'indro') {
    echo '<div class="col p-4"><div class="alert alert-danger"><b>Akses Ditolak.</b> Halaman ini hanya untuk user yang berwenang.</div></div>';
    exit;
}

// Auto-create tables on first load
mysqli_query($conn2, "CREATE TABLE IF NOT EXISTS `project_progress_h` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `periode`      VARCHAR(30)  NOT NULL DEFAULT '',
    `nama_project` VARCHAR(200) NOT NULL DEFAULT '',
    `description`  TEXT,
    `programmer`   VARCHAR(100) DEFAULT '',
    `sistem_analis`VARCHAR(100) DEFAULT '',
    `user_pic`     VARCHAR(100) DEFAULT '',
    `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

mysqli_query($conn2, "CREATE TABLE IF NOT EXISTS `project_progress_d` (
    `id`            INT AUTO_INCREMENT PRIMARY KEY,
    `project_id`    INT NOT NULL,
    `modul`         VARCHAR(50)  DEFAULT '',
    `sub_modul`     VARCHAR(200) DEFAULT '',
    `detail`        TEXT,
    `it_target`     DATE NULL,
    `it_actual`     DATE NULL,
    `sistem_target` DATE NULL,
    `sistem_actual` DATE NULL,
    `sistem_paraf`  VARCHAR(100) DEFAULT '',
    `user_target`   DATE NULL,
    `user_actual`   DATE NULL,
    `user_paraf`    VARCHAR(100) DEFAULT '',
    `notes`         TEXT,
    `status`        ENUM('DONE','ON PROGRESS','PENDING') DEFAULT 'PENDING',
    `bobot`         ENUM('RINGAN','MENENGAH','BERAT') NULL,
    `sort_order`    INT DEFAULT 0,
    `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// === AJAX handlers ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    if ($action === 'save_project') {
        $id            = (int)($_POST['id'] ?? 0);
        $periode       = mysqli_real_escape_string($conn2, trim($_POST['periode'] ?? ''));
        $nama_project  = mysqli_real_escape_string($conn2, trim($_POST['nama_project'] ?? ''));
        $description   = mysqli_real_escape_string($conn2, trim($_POST['description'] ?? ''));
        $programmer    = mysqli_real_escape_string($conn2, trim($_POST['programmer'] ?? ''));
        $sistem_analis = mysqli_real_escape_string($conn2, trim($_POST['sistem_analis'] ?? ''));
        $user_pic      = mysqli_real_escape_string($conn2, trim($_POST['user_pic'] ?? ''));

        if ($id > 0) {
            mysqli_query($conn2, "UPDATE project_progress_h SET periode='$periode',nama_project='$nama_project',description='$description',programmer='$programmer',sistem_analis='$sistem_analis',user_pic='$user_pic' WHERE id=$id");
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            mysqli_query($conn2, "INSERT INTO project_progress_h (periode,nama_project,description,programmer,sistem_analis,user_pic) VALUES ('$periode','$nama_project','$description','$programmer','$sistem_analis','$user_pic')");
            echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn2)]);
        }
        exit;
    }

    if ($action === 'delete_project') {
        $id = (int)($_POST['id'] ?? 0);
        mysqli_query($conn2, "DELETE FROM project_progress_d WHERE project_id=$id");
        mysqli_query($conn2, "DELETE FROM project_progress_h WHERE id=$id");
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'save_task') {
        $id            = (int)($_POST['id'] ?? 0);
        $project_id    = (int)($_POST['project_id'] ?? 0);
        $modul         = mysqli_real_escape_string($conn2, trim($_POST['modul'] ?? ''));
        $sub_modul     = mysqli_real_escape_string($conn2, trim($_POST['sub_modul'] ?? ''));
        $detail        = mysqli_real_escape_string($conn2, trim($_POST['detail'] ?? ''));
        $it_target     = !empty($_POST['it_target'])     ? "'" . mysqli_real_escape_string($conn2, $_POST['it_target']) . "'"     : 'NULL';
        $it_actual     = !empty($_POST['it_actual'])     ? "'" . mysqli_real_escape_string($conn2, $_POST['it_actual']) . "'"     : 'NULL';
        $sistem_target = !empty($_POST['sistem_target']) ? "'" . mysqli_real_escape_string($conn2, $_POST['sistem_target']) . "'" : 'NULL';
        $sistem_actual = !empty($_POST['sistem_actual']) ? "'" . mysqli_real_escape_string($conn2, $_POST['sistem_actual']) . "'" : 'NULL';
        $sistem_paraf  = mysqli_real_escape_string($conn2, trim($_POST['sistem_paraf'] ?? ''));
        $user_target   = !empty($_POST['user_target'])   ? "'" . mysqli_real_escape_string($conn2, $_POST['user_target']) . "'"   : 'NULL';
        $user_actual   = !empty($_POST['user_actual'])   ? "'" . mysqli_real_escape_string($conn2, $_POST['user_actual']) . "'"   : 'NULL';
        $user_paraf    = mysqli_real_escape_string($conn2, trim($_POST['user_paraf'] ?? ''));
        $notes         = mysqli_real_escape_string($conn2, trim($_POST['notes'] ?? ''));
        $status        = mysqli_real_escape_string($conn2, $_POST['status'] ?? 'PENDING');
        $bobot         = !empty($_POST['bobot']) ? "'" . mysqli_real_escape_string($conn2, $_POST['bobot']) . "'" : 'NULL';

        if ($id > 0) {
            mysqli_query($conn2, "UPDATE project_progress_d SET modul='$modul',sub_modul='$sub_modul',detail='$detail',it_target=$it_target,it_actual=$it_actual,sistem_target=$sistem_target,sistem_actual=$sistem_actual,sistem_paraf='$sistem_paraf',user_target=$user_target,user_actual=$user_actual,user_paraf='$user_paraf',notes='$notes',status='$status',bobot=$bobot WHERE id=$id AND project_id=$project_id");
        } else {
            mysqli_query($conn2, "INSERT INTO project_progress_d (project_id,modul,sub_modul,detail,it_target,it_actual,sistem_target,sistem_actual,sistem_paraf,user_target,user_actual,user_paraf,notes,status,bobot) VALUES ($project_id,'$modul','$sub_modul','$detail',$it_target,$it_actual,$sistem_target,$sistem_actual,'$sistem_paraf',$user_target,$user_actual,'$user_paraf','$notes','$status',$bobot)");
        }
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'delete_task') {
        $id         = (int)($_POST['id'] ?? 0);
        $project_id = (int)($_POST['project_id'] ?? 0);
        mysqli_query($conn2, "DELETE FROM project_progress_d WHERE id=$id AND project_id=$project_id");
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'msg' => 'Unknown action']);
    exit;
}

// === Page view logic ===
$view       = $_GET['view'] ?? 'list';
$project_id = (int)($_GET['id'] ?? 0);
$project    = null;
$tasks      = [];

$bobotNilai = [
    'RINGAN'   => ['IT' => 1,   'SISTEM' => 2, 'USER' => 4],
    'MENENGAH' => ['IT' => 2,   'SISTEM' => 3, 'USER' => 4.5],
    'BERAT'    => ['IT' => 3,   'SISTEM' => 4, 'USER' => 5],
];

if ($view === 'detail' && $project_id > 0) {
    $sqlP = mysqli_query($conn2, "SELECT * FROM project_progress_h WHERE id=$project_id LIMIT 1");
    $project = mysqli_fetch_assoc($sqlP);
    if (!$project) { $view = 'list'; $project_id = 0; }

    if ($project) {
        $sqlT = mysqli_query($conn2, "SELECT * FROM project_progress_d WHERE project_id=$project_id ORDER BY sort_order ASC, id ASC");
        while ($t = mysqli_fetch_assoc($sqlT)) $tasks[] = $t;
    }
}

// List view: load all projects with task summary
$projects = [];
if ($view === 'list') {
    $sqlAll = mysqli_query($conn2, "
        SELECT h.*,
               COUNT(d.id) total_tasks,
               SUM(d.status='DONE') done_tasks,
               SUM(d.status='ON PROGRESS') prog_tasks,
               SUM(d.status='PENDING') pend_tasks
        FROM project_progress_h h
        LEFT JOIN project_progress_d d ON d.project_id = h.id
        GROUP BY h.id
        ORDER BY h.id DESC
    ");
    while ($p = mysqli_fetch_assoc($sqlAll)) $projects[] = $p;
}

function fmtDate($d) {
    return !empty($d) ? date('d-M-Y', strtotime($d)) : '-';
}
function statusBadge($s) {
    $map = ['DONE' => 'success', 'ON PROGRESS' => 'warning', 'PENDING' => 'secondary'];
    $cls = $map[$s] ?? 'secondary';
    return '<span class="badge badge-'.$cls.'">'.$s.'</span>';
}
?>

<style>
.tbl-progress th { background-color: #2c3e50; color: #fff; text-align: center; vertical-align: middle; font-size: 11px; white-space: nowrap; }
.tbl-progress td { font-size: 11px; vertical-align: middle; }
.tbl-progress .group-it    { background-color: #d4e6f1; }
.tbl-progress .group-sist  { background-color: #d5f5e3; }
.tbl-progress .group-user  { background-color: #fdebd0; }
.badge-done { background-color: #27ae60; color: #fff; }
.stat-card { border-radius: 8px; padding: 12px 18px; color: #fff; font-size: 13px; font-weight: 600; }
.stat-done  { background: linear-gradient(135deg,#27ae60,#1e8449); }
.stat-prog  { background: linear-gradient(135deg,#e67e22,#ca6f1e); }
.stat-pend  { background: linear-gradient(135deg,#7f8c8d,#5d6d7e); }
.bobot-legend th, .bobot-legend td { font-size: 11px; padding: 3px 8px; text-align: center; }
.btn-action { font-size: 11px; padding: 2px 8px; }
</style>

<div class="col p-3">

<?php if ($view === 'list'): ?>
<!-- ===== LIST VIEW ===== -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="fa fa-tasks mr-2"></i>Project Progress</h4>
    <button class="btn btn-success btn-sm" onclick="openProjectModal()">
        <i class="fa fa-plus"></i> Tambah Project
    </button>
</div>

<div class="table-responsive">
<table class="table table-bordered table-hover table-sm" id="tbl-projects">
    <thead class="thead-dark">
        <tr>
            <th style="width:40px">No</th>
            <th>Periode</th>
            <th>Nama Project</th>
            <th>Deskripsi</th>
            <th>Programmer</th>
            <th>Sistem Analis</th>
            <th>User</th>
            <th>Progress</th>
            <th style="width:110px">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($projects)): ?>
        <tr><td colspan="9" class="text-center text-muted">Belum ada project. Klik "Tambah Project" untuk menambahkan.</td></tr>
    <?php else: ?>
    <?php foreach ($projects as $i => $p):
        $total = (int)$p['total_tasks'];
        $done  = (int)$p['done_tasks'];
        $pct   = $total > 0 ? round($done/$total*100) : 0;
    ?>
    <tr>
        <td class="text-center"><?= $i+1 ?></td>
        <td><?= htmlspecialchars($p['periode']) ?></td>
        <td><b><?= htmlspecialchars($p['nama_project']) ?></b></td>
        <td><?= htmlspecialchars($p['description']) ?></td>
        <td><?= htmlspecialchars($p['programmer']) ?></td>
        <td><?= htmlspecialchars($p['sistem_analis']) ?></td>
        <td><?= htmlspecialchars($p['user_pic']) ?></td>
        <td style="min-width:140px">
            <div class="d-flex align-items-center">
                <div class="progress flex-grow-1 mr-1" style="height:14px">
                    <div class="progress-bar <?= $pct==100?'bg-success':($pct>0?'bg-warning':'bg-secondary') ?>" style="width:<?=$pct?>%"></div>
                </div>
                <small><?=$done?>/<?=$total?> (<?=$pct?>%)</small>
            </div>
            <div class="mt-1">
                <span class="badge badge-success"><?= (int)$p['done_tasks'] ?> Done</span>
                <span class="badge badge-warning text-dark"><?= (int)$p['prog_tasks'] ?> Proses</span>
                <span class="badge badge-secondary"><?= (int)$p['pend_tasks'] ?> Pending</span>
            </div>
        </td>
        <td class="text-center">
            <a href="project-progress.php?view=detail&id=<?=$p['id']?>" class="btn btn-primary btn-action">
                <i class="fa fa-eye"></i> Detail
            </a>
            <button class="btn btn-danger btn-action mt-1" onclick="deleteProject(<?=$p['id']?>, '<?=htmlspecialchars($p['nama_project'], ENT_QUOTES)?>')">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>

<?php elseif ($view === 'detail' && $project): ?>
<!-- ===== DETAIL VIEW ===== -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <a href="project-progress.php" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
    <h5 class="mb-0 mx-auto"><i class="fa fa-tasks mr-1"></i>Detail Project Progress</h5>
    <button class="btn btn-warning btn-sm" onclick="openProjectModal(<?=htmlspecialchars(json_encode($project), ENT_QUOTES)?>)">
        <i class="fa fa-pencil"></i> Edit Header
    </button>
</div>

<!-- Header Info -->
<div class="card mb-3 border-primary">
    <div class="card-body py-2">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0" style="font-size:12px">
                    <tr><td style="width:130px"><b>Periode</b></td><td>: <?= htmlspecialchars($project['periode']) ?></td></tr>
                    <tr><td><b>Nama Project</b></td><td>: <?= htmlspecialchars($project['nama_project']) ?></td></tr>
                    <tr><td><b>Deskripsi</b></td><td>: <?= htmlspecialchars($project['description']) ?></td></tr>
                </table>
            </div>
            <div class="col-md-3">
                <table class="table table-sm table-borderless mb-0" style="font-size:12px">
                    <tr><td style="width:120px"><b>Programmer</b></td><td>: <?= htmlspecialchars($project['programmer']) ?></td></tr>
                    <tr><td><b>Sistem Analis</b></td><td>: <?= htmlspecialchars($project['sistem_analis']) ?></td></tr>
                    <tr><td><b>User</b></td><td>: <?= htmlspecialchars($project['user_pic']) ?></td></tr>
                </table>
            </div>
            <div class="col-md-3">
                <!-- Bobot/Nilai legend -->
                <table class="table table-sm table-bordered bobot-legend mb-0">
                    <thead class="thead-dark">
                        <tr><th colspan="4" class="text-center">PROGRES PROJECT</th></tr>
                        <tr><th>BOBOT/NILAI</th><th>IT</th><th>SISTEM</th><th>USER</th></tr>
                    </thead>
                    <tbody>
                        <tr><td><b>RINGAN</b></td><td>1</td><td>2</td><td>4</td></tr>
                        <tr><td><b>MENENGAH</b></td><td>2</td><td>3</td><td>4.5</td></tr>
                        <tr><td><b>BERAT</b></td><td>3</td><td>4</td><td>5</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Summary cards -->
<?php
$sumDone = 0; $sumProg = 0; $sumPend = 0;
foreach ($tasks as $t) {
    if ($t['status'] === 'DONE') $sumDone++;
    elseif ($t['status'] === 'ON PROGRESS') $sumProg++;
    else $sumPend++;
}
$sumTotal = count($tasks);
$sumPct = $sumTotal > 0 ? round($sumDone/$sumTotal*100) : 0;
?>
<div class="row mb-3">
    <div class="col-md-3"><div class="stat-card stat-done"><i class="fa fa-check-circle mr-1"></i>DONE &nbsp;<span style="font-size:20px"><?=$sumDone?></span> / <?=$sumTotal?></div></div>
    <div class="col-md-3"><div class="stat-card stat-prog"><i class="fa fa-spinner mr-1"></i>ON PROGRESS &nbsp;<span style="font-size:20px"><?=$sumProg?></span></div></div>
    <div class="col-md-3"><div class="stat-card stat-pend"><i class="fa fa-clock-o mr-1"></i>PENDING &nbsp;<span style="font-size:20px"><?=$sumPend?></span></div></div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#2980b9,#1a5276)">
            <i class="fa fa-percent mr-1"></i>PROGRESS &nbsp;<span style="font-size:20px"><?=$sumPct?>%</span>
        </div>
    </div>
</div>

<!-- Task table -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0">Daftar Task</h6>
    <button class="btn btn-success btn-sm" onclick="openTaskModal()">
        <i class="fa fa-plus"></i> Tambah Task
    </button>
</div>

<div class="table-responsive">
<table class="table table-bordered tbl-progress" id="tbl-tasks">
    <thead>
        <tr>
            <th rowspan="2" style="width:35px">NO</th>
            <th rowspan="2">MODUL</th>
            <th rowspan="2">SUB MODUL</th>
            <th rowspan="2">DETAIL</th>
            <th colspan="2" class="group-it">IT</th>
            <th colspan="3" class="group-sist">SISTEM</th>
            <th colspan="3" class="group-user">USER</th>
            <th rowspan="2">NOTE</th>
            <th rowspan="2">STATUS</th>
            <th colspan="2">NILAI KPI</th>
            <th rowspan="2" style="width:70px">AKSI</th>
        </tr>
        <tr>
            <th class="group-it">TARGET</th>
            <th class="group-it">ACTUAL</th>
            <th class="group-sist">TARGET</th>
            <th class="group-sist">ACTUAL</th>
            <th class="group-sist">PARAF</th>
            <th class="group-user">TARGET</th>
            <th class="group-user">ACTUAL</th>
            <th class="group-user">PARAF</th>
            <th>BOBOT</th>
            <th>NILAI</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($tasks)): ?>
        <tr><td colspan="17" class="text-center text-muted">Belum ada task. Klik "Tambah Task".</td></tr>
    <?php else: ?>
    <?php foreach ($tasks as $i => $t):
        // Compute nilai KPI
        $nilai = '';
        if (!empty($t['bobot']) && isset($bobotNilai[$t['bobot']])) {
            $bv = $bobotNilai[$t['bobot']];
            if (!empty($t['user_actual'])) $nilai = $bv['USER'];
            elseif (!empty($t['sistem_actual'])) $nilai = $bv['SISTEM'];
            elseif (!empty($t['it_actual'])) $nilai = $bv['IT'];
        }
        // Status style
        $statusStyle = '';
        if ($t['status'] === 'DONE') $statusStyle = 'color:#155724;font-weight:700';
        elseif ($t['status'] === 'ON PROGRESS') $statusStyle = 'color:#856404;font-weight:700';
        else $statusStyle = 'color:#6c757d';
    ?>
    <tr>
        <td class="text-center"><?= $i+1 ?></td>
        <td><?= htmlspecialchars($t['modul']) ?></td>
        <td><?= htmlspecialchars($t['sub_modul']) ?></td>
        <td style="max-width:200px;white-space:normal"><?= htmlspecialchars($t['detail']) ?></td>
        <td class="text-center group-it"><?= fmtDate($t['it_target']) ?></td>
        <td class="text-center group-it"><?= fmtDate($t['it_actual']) ?></td>
        <td class="text-center group-sist"><?= fmtDate($t['sistem_target']) ?></td>
        <td class="text-center group-sist"><?= fmtDate($t['sistem_actual']) ?></td>
        <td class="text-center group-sist"><?= htmlspecialchars($t['sistem_paraf']) ?></td>
        <td class="text-center group-user"><?= fmtDate($t['user_target']) ?></td>
        <td class="text-center group-user"><?= fmtDate($t['user_actual']) ?></td>
        <td class="text-center group-user"><?= htmlspecialchars($t['user_paraf']) ?></td>
        <td><?= htmlspecialchars($t['notes']) ?></td>
        <td class="text-center" style="<?=$statusStyle?>"><?= htmlspecialchars($t['status']) ?></td>
        <td class="text-center"><?= htmlspecialchars($t['bobot'] ?? '') ?></td>
        <td class="text-center"><?= $nilai !== '' ? $nilai : '' ?></td>
        <td class="text-center">
            <button class="btn btn-warning btn-action" onclick='openTaskModal(<?= htmlspecialchars(json_encode($t), ENT_QUOTES) ?>)'>
                <i class="fa fa-pencil"></i>
            </button>
            <button class="btn btn-danger btn-action mt-1" onclick="deleteTask(<?=$t['id']?>, <?=$project_id?>)">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
</div>

<!-- ===== PROJECT MODAL ===== -->
<div class="modal fade" id="modalProject" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title mb-0" id="modalProjectTitle">Tambah Project</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="proj_id" value="0">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label><b>Periode</b></label>
                        <input type="text" class="form-control form-control-sm" id="proj_periode" placeholder="cth: MEI2026">
                    </div>
                    <div class="form-group col-md-8">
                        <label><b>Nama Project</b></label>
                        <input type="text" class="form-control form-control-sm" id="proj_nama">
                    </div>
                </div>
                <div class="form-group">
                    <label><b>Deskripsi</b></label>
                    <textarea class="form-control form-control-sm" id="proj_desc" rows="2"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label><b>Programmer</b></label>
                        <input type="text" class="form-control form-control-sm" id="proj_programmer">
                    </div>
                    <div class="form-group col-md-4">
                        <label><b>Sistem Analis</b></label>
                        <input type="text" class="form-control form-control-sm" id="proj_sist_analis">
                    </div>
                    <div class="form-group col-md-4">
                        <label><b>User</b></label>
                        <input type="text" class="form-control form-control-sm" id="proj_user">
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary btn-sm" onclick="saveProject()"><i class="fa fa-save mr-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- ===== TASK MODAL ===== -->
<?php if ($view === 'detail' && $project): ?>
<div class="modal fade" id="modalTask" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title mb-0" id="modalTaskTitle">Tambah Task</h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="task_id" value="0">
                <input type="hidden" id="task_project_id" value="<?=$project_id?>">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label><b>Modul</b></label>
                        <input type="text" class="form-control form-control-sm" id="task_modul" placeholder="AR / AP / NDS">
                    </div>
                    <div class="form-group col-md-4">
                        <label><b>Sub Modul</b></label>
                        <input type="text" class="form-control form-control-sm" id="task_sub_modul">
                    </div>
                    <div class="form-group col-md-4">
                        <label><b>Detail</b></label>
                        <textarea class="form-control form-control-sm" id="task_detail" rows="1"></textarea>
                    </div>
                    <div class="form-group col-md-2">
                        <label><b>Status</b></label>
                        <select class="form-control form-control-sm" id="task_status">
                            <option value="PENDING">PENDING</option>
                            <option value="ON PROGRESS">ON PROGRESS</option>
                            <option value="DONE">DONE</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="card border-info mb-2">
                            <div class="card-header bg-info text-white py-1" style="font-size:11px"><b>IT</b></div>
                            <div class="card-body py-2">
                                <div class="form-row">
                                    <div class="form-group col-6 mb-1">
                                        <label style="font-size:11px">Target</label>
                                        <input type="date" class="form-control form-control-sm" id="task_it_target">
                                    </div>
                                    <div class="form-group col-6 mb-1">
                                        <label style="font-size:11px">Actual</label>
                                        <input type="date" class="form-control form-control-sm" id="task_it_actual">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success mb-2">
                            <div class="card-header bg-success text-white py-1" style="font-size:11px"><b>SISTEM</b></div>
                            <div class="card-body py-2">
                                <div class="form-row">
                                    <div class="form-group col-4 mb-1">
                                        <label style="font-size:11px">Target</label>
                                        <input type="date" class="form-control form-control-sm" id="task_sistem_target">
                                    </div>
                                    <div class="form-group col-4 mb-1">
                                        <label style="font-size:11px">Actual</label>
                                        <input type="date" class="form-control form-control-sm" id="task_sistem_actual">
                                    </div>
                                    <div class="form-group col-4 mb-1">
                                        <label style="font-size:11px">Paraf</label>
                                        <input type="text" class="form-control form-control-sm" id="task_sistem_paraf">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning mb-2">
                            <div class="card-header bg-warning py-1" style="font-size:11px"><b>USER</b></div>
                            <div class="card-body py-2">
                                <div class="form-row">
                                    <div class="form-group col-4 mb-1">
                                        <label style="font-size:11px">Target</label>
                                        <input type="date" class="form-control form-control-sm" id="task_user_target">
                                    </div>
                                    <div class="form-group col-4 mb-1">
                                        <label style="font-size:11px">Actual</label>
                                        <input type="date" class="form-control form-control-sm" id="task_user_actual">
                                    </div>
                                    <div class="form-group col-4 mb-1">
                                        <label style="font-size:11px">Paraf</label>
                                        <input type="text" class="form-control form-control-sm" id="task_user_paraf">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label><b>Note</b></label>
                        <input type="text" class="form-control form-control-sm" id="task_notes">
                    </div>
                    <div class="form-group col-md-4">
                        <label><b>Bobot</b></label>
                        <select class="form-control form-control-sm" id="task_bobot">
                            <option value="">-- Pilih Bobot --</option>
                            <option value="RINGAN">RINGAN (IT:1 | SIST:2 | USER:4)</option>
                            <option value="MENENGAH">MENENGAH (IT:2 | SIST:3 | USER:4.5)</option>
                            <option value="BERAT">BERAT (IT:3 | SIST:4 | USER:5)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button class="btn btn-success btn-sm" onclick="saveTask()"><i class="fa fa-save mr-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
function openProjectModal(data) {
    if (data) {
        $('#modalProjectTitle').text('Edit Project');
        $('#proj_id').val(data.id);
        $('#proj_periode').val(data.periode);
        $('#proj_nama').val(data.nama_project);
        $('#proj_desc').val(data.description);
        $('#proj_programmer').val(data.programmer);
        $('#proj_sist_analis').val(data.sistem_analis);
        $('#proj_user').val(data.user_pic);
    } else {
        $('#modalProjectTitle').text('Tambah Project');
        $('#proj_id').val(0);
        $('#proj_periode,#proj_nama,#proj_desc,#proj_programmer,#proj_sist_analis,#proj_user').val('');
    }
    $('#modalProject').modal('show');
}

function saveProject() {
    var data = {
        action        : 'save_project',
        id            : $('#proj_id').val(),
        periode       : $('#proj_periode').val(),
        nama_project  : $('#proj_nama').val(),
        description   : $('#proj_desc').val(),
        programmer    : $('#proj_programmer').val(),
        sistem_analis : $('#proj_sist_analis').val(),
        user_pic      : $('#proj_user').val()
    };
    if (!data.nama_project.trim()) { Swal.fire('Error', 'Nama project wajib diisi.', 'error'); return; }
    $.post('project-progress.php', data, function(res) {
        if (res.success) {
            $('#modalProject').modal('hide');
            <?php if ($view === 'detail'): ?>
                location.reload();
            <?php else: ?>
                location.href = 'project-progress.php?view=detail&id=' + res.id;
            <?php endif; ?>
        } else {
            Swal.fire('Error', 'Gagal menyimpan project.', 'error');
        }
    }, 'json').fail(function() { Swal.fire('Error', 'Request gagal.', 'error'); });
}

function deleteProject(id, nama) {
    Swal.fire({
        title: 'Hapus Project?',
        html: '<b>' + nama + '</b><br>Semua task dalam project ini akan ikut terhapus.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
    }).then(function(r) {
        if (!r.isConfirmed) return;
        $.post('project-progress.php', {action:'delete_project', id:id}, function(res) {
            if (res.success) location.reload();
            else Swal.fire('Error', 'Gagal menghapus.', 'error');
        }, 'json');
    });
}

<?php if ($view === 'detail' && $project): ?>
function openTaskModal(data) {
    var fields = ['id','modul','sub_modul','detail','it_target','it_actual','sistem_target','sistem_actual','sistem_paraf','user_target','user_actual','user_paraf','notes','status','bobot'];
    if (data) {
        $('#modalTaskTitle').text('Edit Task');
        $('#task_id').val(data.id);
        $('#task_modul').val(data.modul);
        $('#task_sub_modul').val(data.sub_modul);
        $('#task_detail').val(data.detail);
        $('#task_it_target').val(data.it_target || '');
        $('#task_it_actual').val(data.it_actual || '');
        $('#task_sistem_target').val(data.sistem_target || '');
        $('#task_sistem_actual').val(data.sistem_actual || '');
        $('#task_sistem_paraf').val(data.sistem_paraf);
        $('#task_user_target').val(data.user_target || '');
        $('#task_user_actual').val(data.user_actual || '');
        $('#task_user_paraf').val(data.user_paraf);
        $('#task_notes').val(data.notes);
        $('#task_status').val(data.status);
        $('#task_bobot').val(data.bobot || '');
    } else {
        $('#modalTaskTitle').text('Tambah Task');
        $('#task_id').val(0);
        $('#task_modul,#task_sub_modul,#task_detail,#task_it_target,#task_it_actual,#task_sistem_target,#task_sistem_actual,#task_sistem_paraf,#task_user_target,#task_user_actual,#task_user_paraf,#task_notes').val('');
        $('#task_status').val('PENDING');
        $('#task_bobot').val('');
    }
    $('#modalTask').modal('show');
}

function saveTask() {
    var data = {
        action         : 'save_task',
        id             : $('#task_id').val(),
        project_id     : $('#task_project_id').val(),
        modul          : $('#task_modul').val(),
        sub_modul      : $('#task_sub_modul').val(),
        detail         : $('#task_detail').val(),
        it_target      : $('#task_it_target').val(),
        it_actual      : $('#task_it_actual').val(),
        sistem_target  : $('#task_sistem_target').val(),
        sistem_actual  : $('#task_sistem_actual').val(),
        sistem_paraf   : $('#task_sistem_paraf').val(),
        user_target    : $('#task_user_target').val(),
        user_actual    : $('#task_user_actual').val(),
        user_paraf     : $('#task_user_paraf').val(),
        notes          : $('#task_notes').val(),
        status         : $('#task_status').val(),
        bobot          : $('#task_bobot').val()
    };
    if (!data.sub_modul.trim()) { Swal.fire('Error', 'Sub Modul wajib diisi.', 'error'); return; }
    $.post('project-progress.php', data, function(res) {
        if (res.success) { $('#modalTask').modal('hide'); location.reload(); }
        else Swal.fire('Error', 'Gagal menyimpan task.', 'error');
    }, 'json').fail(function() { Swal.fire('Error', 'Request gagal.', 'error'); });
}

function deleteTask(id, project_id) {
    Swal.fire({
        title: 'Hapus task ini?', icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
    }).then(function(r) {
        if (!r.isConfirmed) return;
        $.post('project-progress.php', {action:'delete_task', id:id, project_id:project_id}, function(res) {
            if (res.success) location.reload();
            else Swal.fire('Error', 'Gagal menghapus.', 'error');
        }, 'json');
    });
}
<?php endif; ?>
</script>
</body>
</html>
