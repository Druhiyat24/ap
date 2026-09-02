<?php
// Migrasi staging Kontrabon New:
//  1) ir_kontrabon_temp        : draft per-user (JSON snapshot) -> RESUME di PC
//     mana pun; di-upsert tiap ada perubahan (auto-save).
//  2) ir_kontrabon_bpb_reserve : RESERVASI BPB (no_bpb UNIQUE global) supaya
//     2 user / 2 draft tidak bisa memakai BPB yang sama. Di-insert saat scan,
//     dihapus saat BPB dilepas / draft dibatalkan / kontrabon disimpan.
// Idempoten.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

function tbl($conn, $t){ $r=mysqli_query($conn,"SHOW TABLES LIKE '".mysqli_real_escape_string($conn,$t)."'"); return $r&&mysqli_num_rows($r)>0; }

if (!tbl($conn2, 'ir_kontrabon_temp')) {
    $ok = mysqli_query($conn2, "CREATE TABLE ir_kontrabon_temp (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        create_user VARCHAR(100) NOT NULL,
        unik_code   VARCHAR(30) NULL,
        doc_number  VARCHAR(30) NULL,
        payload     MEDIUMTEXT NULL,
        update_date DATETIME NULL,
        UNIQUE KEY uq_user (create_user)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    echo $ok ? "OK: ir_kontrabon_temp dibuat\n" : "ERR temp: " . mysqli_error($conn2) . "\n";
} else echo "SKIP: ir_kontrabon_temp sudah ada\n";

if (!tbl($conn2, 'ir_kontrabon_bpb_reserve')) {
    $ok = mysqli_query($conn2, "CREATE TABLE ir_kontrabon_bpb_reserve (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        no_bpb      VARCHAR(100) NOT NULL,
        create_user VARCHAR(100) NULL,
        unik_code   VARCHAR(30) NULL,
        nama_supp   VARCHAR(150) NULL,
        create_date DATETIME NULL,
        UNIQUE KEY uq_bpb (no_bpb),
        KEY idx_user (create_user),
        KEY idx_unik (unik_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    echo $ok ? "OK: ir_kontrabon_bpb_reserve dibuat\n" : "ERR reserve: " . mysqli_error($conn2) . "\n";
} else echo "SKIP: ir_kontrabon_bpb_reserve sudah ada\n";
mysqli_close($conn2);
