<?php
// ============================================================================
// Accept Transfer SURAT JALAN (FG/OUT) — Warehouse -> Accounting.
// Memakai seluruh logika & tampilan form_approve_bpb.php dengan mode 'sj'
// (beda hanya: filter dokumen khusus FG/OUT, judul, & redirect). Dengan begini
// tidak ada duplikasi kode yang bisa drift.
// Role: menu "Document Handover - Accept SJ Warehouse To Accounting" (menurole id 137).
// ============================================================================
$APPROVE_MODE = 'sj';
include __DIR__ . '/form_approve_bpb.php';
