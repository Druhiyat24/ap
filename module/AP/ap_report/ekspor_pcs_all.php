<?php
require '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

include '../../../conn/conn.php';

$start_date = date("Y-m-d",strtotime($_GET['start_date']));
$end_date   = date("Y-m-d",strtotime($_GET['end_date']));
$start_date_text = date("d F Y",strtotime($_GET['start_date']));
$end_date_text   = date("d F Y",strtotime($_GET['end_date']));
$nama_supp = $_GET['nama_supp'] ?? 'ALL';

$where = ($nama_supp != 'ALL') ? " where supplier='$nama_supp'" : "";


/*
|--------------------------------------------------------------------------
| SHEET 1 AP-BPB
|--------------------------------------------------------------------------
*/

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('AP-BPB');


$sheet->setCellValue('A1', 'Payable Card Statement - BPB');
$sheet->setCellValue('A2', 'PERIODE: '.$start_date_text.' - '.$end_date_text);
$sheet->mergeCells('A1:AM1');
$sheet->mergeCells('A2:AM2');
$sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);

$rowHeader1 = 4;
$rowHeader2 = 5;

$headersFix = [
    'No','Nama Supplier','Bpb Number','Bpb Date','Due Date','Currency',
    'Begining Balance','Addition','Reverse BPB','Deduction KB',
    'Reverse KB','Deduction GM','Ending Balance','Rate',
    'Ending Balance IDR','COA No','COA Name',
    'Item Type 1','Item Type 2','Relationship'
];

$colIndex = 1;
foreach ($headersFix as $h) {
    $sheet->setCellValueByColumnAndRow($colIndex, $rowHeader1, $h);
    $sheet->mergeCellsByColumnAndRow($colIndex, $rowHeader1, $colIndex, $rowHeader2);
    $colIndex++;
}

$colIndex++;

$agingStartCol = $colIndex;
$agingHeaders = ['Current','1-30','31-60','61-90','91-120','121-180','181-360','>360','Total'];

$sheet->setCellValueByColumnAndRow($agingStartCol, $rowHeader1, 'Account Payable Aging Based on Due Date');
$sheet->mergeCellsByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) - 1, $rowHeader1);

$agingCol = $agingStartCol;
foreach ($agingHeaders as $h) {
    $sheet->setCellValueByColumnAndRow($agingCol, $rowHeader2, $h);
    $agingCol++;
}

$colIndex = $agingCol + 1;

$projectionStartCol = $colIndex;

$sqlbulan = mysqli_query($conn1,"
SELECT CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)), LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun
FROM dim_date
WHERE kode_tanggal BETWEEN
CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01')
AND CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),
LPAD(IF(MONTH('$end_date')+5 > 12, MOD(MONTH('$end_date')+5,12), MONTH('$end_date')+5),2,'0'),'01')
GROUP BY bulan,tahun
ORDER BY kode_tanggal ASC
");

$projectionHeaders = ['Due'];
while($rowbulan = mysqli_fetch_array($sqlbulan)){
    $projectionHeaders[] = $rowbulan['bulan_tahun'];
}
$projectionHeaders[] = 'Total';

$sheet->setCellValueByColumnAndRow($projectionStartCol, $rowHeader1, 'Account Payable Based on Due Date Projection');
$sheet->mergeCellsByColumnAndRow($projectionStartCol, $rowHeader1, $projectionStartCol + count($projectionHeaders) - 1, $rowHeader1);

$projectionCol = $projectionStartCol;
foreach ($projectionHeaders as $h) {
    $sheet->setCellValueByColumnAndRow($projectionCol, $rowHeader2, $h);
    $projectionCol++;
}

$lastColIndex = $projectionCol - 1;

$sheet->getStyleByColumnAndRow(1, $rowHeader1, $lastColIndex, $rowHeader2)->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
]);

$sheet->getStyleByColumnAndRow(1, $rowHeader1, count($headersFix), $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE4E1');

$sheet->getStyleByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) -1, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('98FB98');

$sheet->getStyleByColumnAndRow($projectionStartCol, $rowHeader1, $lastColIndex, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('87CEFA');



$rowNum = $rowHeader2 + 1;
$no = 1;

$sql = mysqli_query($conn2, "WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

po_bpb as (select a.bpbno_int, b.supplier, a.pono, c.jml_pterms as top from bpb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier LEFT JOIN po_header c on c.pono = a.pono LEFT JOIN po_header_draft d on d.id = c.id_draft where a.bpbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int
UNION
select a.bppbno_int, b.supplier, '' pono, 0 top from bppb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.bppbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bppbno_int),

saldo_awal as (select supplier, no_bpb, tgl_bpb, 0 top, duedate, curr, total, rate, total_idr, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_bpb a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

trx_in as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_out_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_gm as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

trx_out_gm_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

saldo_in as (select supplier, no_bpb, tgl_bpb, top, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_bpb, tgl_bpb, top, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in_before
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in) a GROUP BY no_bpb, no_coa),

saldo_out_per_coa as (select no_journal, no_coa, sum(COALESCE(reverse_bpb_before,0)) reverse_bpb_before, sum(COALESCE(reverse_bpb,0)) reverse_bpb, sum(COALESCE(gm_before,0)) gm_before, sum(COALESCE(gm,0)) gm from (select no_journal, no_coa, 0 reverse_bpb_before, total reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse
UNION ALL
select no_journal, no_coa, total reverse_bpb_before, 0 reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse_before
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, 0 gm_before, total gm from trx_out_gm
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, total gm_before, 0 gm from trx_out_gm_before) a GROUP BY no_journal, no_coa),

saldo_out_per_bpb as (SELECT reff_doc, SUM(COALESCE(kontrabon_before,0)) kontrabon_before, SUM(COALESCE(kontrabon,0)) kontrabon, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(kontrabon_revisi_before,0)) kontrabon_revisi_before, SUM(COALESCE(kontrabon_revisi,0)) kontrabon_revisi, SUM(COALESCE(reverse_kontrabon_revisi_before,0)) reverse_kontrabon_revisi_before, SUM(COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon_revisi from (select reff_doc, 0 kontrabon_before, total kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb
UNION ALL
select reff_doc, total kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, total kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, total reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, total reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before) a GROUP BY reff_doc),

data_mutasi as (select a.supplier, a.no_bpb, a.tgl_bpb, a.duedate, a.curr, a.rate, a.no_coa, a.nama_coa, a.item_type1, a.item_type2, a.relasi, coalesce(a.saldo_awal,0) saldo_awal, coalesce(a.total_in,0) total_in, reverse_bpb_before, reverse_bpb, gm_before, gm, IF(kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_before, IF(kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon, IF(reverse_kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_before, IF(reverse_kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon, IF(kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi_before, IF(kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi, IF(reverse_kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi_before, IF(reverse_kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi from saldo_in a LEFT JOIN saldo_out_per_coa b on b.no_coa = a.no_coa and b.no_journal = a.no_bpb LEFT JOIN saldo_out_per_bpb c on c.reff_doc = a.no_bpb),

mutasi_det as (select supplier, no_bpb, tgl_bpb, duedate, curr, rate, (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0) - COALESCE(gm_before,0) - COALESCE(kontrabon_before,0) - COALESCE(kontrabon_revisi_before,0) + COALESCE(reverse_kontrabon_before,0) + COALESCE(reverse_kontrabon_revisi_before,0)) saldo_awal, COALESCE(total_in,0) in_bpb, COALESCE(reverse_bpb,0) reverse_bpb, (COALESCE(kontrabon,0) + COALESCE(kontrabon_revisi,0)) ded_kontrabon, (COALESCE(reverse_kontrabon,0) + COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon, COALESCE(gm,0) gm, no_coa, nama_coa, item_type1, item_type2, relasi from data_mutasi),

mutasi as (select supplier, no_bpb, tgl_bpb, duedate, a.curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir, IFNULL(b.rate,1) rate, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi_det a LEFT JOIN rate b on b.curr = a.curr),

laporan_mutasi as (SELECT supplier, no_bpb, tgl_bpb, duedate, curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir, rate, (saldo_akhir * rate) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN (saldo_akhir * rate) ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END
    ) AS tot_produe,
        CASE
            WHEN no_bpb LIKE '%RO%' 
              OR no_bpb LIKE '%OUT%' 
            THEN -1
            ELSE 1
        END AS f
        
    from mutasi)
        
        SELECT
    supplier,
    no_bpb,
    tgl_bpb,
    duedate,
    curr,

    saldo_awal * f AS saldo_awal,
    in_bpb * f AS in_bpb,
    reverse_bpb * f AS reverse_bpb,
    ded_kontrabon * f AS ded_kontrabon,
    reverse_kontrabon * f AS reverse_kontrabon,
    gm * f AS gm,
    saldo_akhir * f AS saldo_akhir,
    rate,
    saldo_akhir_idr * f AS saldo_akhir_idr,

    no_coa,
    nama_coa,
    item_type1,
    item_type2,
    relasi,

    due_current * f AS due_current,
    due_1_30 * f AS due_1_30,
    due_31_60 * f AS due_31_60,
    due_61_90 * f AS due_61_90,
    due_91_120 * f AS due_91_120,
    due_121_180 * f AS due_121_180,
    due_181_360 * f AS due_181_360,
    due_gt_360 * f AS due_gt_360,
    total_due * f AS total_due,

    pro_due * f AS pro_due,
    pro_due0 * f AS pro_due0,
    pro_due1 * f AS pro_due1,
    pro_due2 * f AS pro_due2,
    pro_due3 * f AS pro_due3,
    pro_due4 * f AS pro_due4,
    pro_due5 * f AS pro_due5,
    tot_produe * f AS tot_produe from laporan_mutasi $where"); // isi query kamu

while($data = mysqli_fetch_assoc($sql)){
    $col = 1; // pakai index angka untuk konsisten
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $no++);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['supplier']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['no_bpb']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['tgl_bpb']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['duedate']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['curr']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_awal']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['in_bpb']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['reverse_bpb']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['ded_kontrabon']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['reverse_kontrabon']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['gm']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['rate']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir_idr']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['no_coa']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['nama_coa']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['item_type1']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['item_type2']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['relasi']);
    $col++; // spacer

    // AGING
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['due_current']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['due_1_30']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['due_31_60']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['due_61_90']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['due_91_120']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['due_121_180']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['due_181_360']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['due_gt_360']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['total_due']);
    $col++; // spacer

    // PROJECTION
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due0']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due1']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due2']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due3']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due4']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due5']);
    $sheet->setCellValueByColumnAndRow($col++, $rowNum, $data['tot_produe']);

    $rowNum++;
}


$highestColumn = Coordinate::stringFromColumnIndex($lastColIndex);

$sheet->getStyle('G6:'.$highestColumn.$rowNum)
      ->getNumberFormat()->setFormatCode('#,##0.00');

$sheet->getStyle('A4:'.$highestColumn.($rowNum-1))
      ->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]]);

for($c=1; $c<=$lastColIndex; $c++){
    $colLetter = Coordinate::stringFromColumnIndex($c);
    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
}

$sheet->freezePaneByColumnAndRow(1, $rowHeader2 + 1);



/*
|---------------------------------------------------------------------------------------------------------------------------------------------------------
| SHEET 2 AP-Kontrabon
|---------------------------------------------------------------------------------------------------------------------------------------------------------
*/

$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('AP-Kontrabon');


$sheet2->setCellValue('A1', 'Payable Card Statement - KONTRABON');
$sheet2->setCellValue('A2', 'PERIODE: '.$start_date_text.' - '.$end_date_text);
$sheet2->mergeCells('A1:AN1');
$sheet2->mergeCells('A2:AN2');
$sheet2->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);

$rowHeader1 = 4;
$rowHeader2 = 5;

$headersFix = [
    'No','Nama Supplier','Kontrabon Number','Kontrabon Date','Due Date','Currency',
    'Begining Balance','Addition','Deduction Advance','Deduction Others',
    'Deduction LP','Deduction GM','Reverse','Ending Balance',
    'Rate','Ending Balance IDR','COA No','COA Name',
    'Item Type 1','Item Type 2','Relationship'
];

$colIndex = 1;
foreach ($headersFix as $h) {
    $sheet2->setCellValueByColumnAndRow($colIndex, $rowHeader1, $h);
    $sheet2->mergeCellsByColumnAndRow($colIndex, $rowHeader1, $colIndex, $rowHeader2);
    $colIndex++;
}

$colIndex++;

$agingStartCol = $colIndex;
$agingHeaders = ['Current','1-30','31-60','61-90','91-120','121-180','181-360','>360','Total'];

$sheet2->setCellValueByColumnAndRow($agingStartCol, $rowHeader1, 'Account Payable Aging Based on Due Date');
$sheet2->mergeCellsByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) - 1, $rowHeader1);

$agingCol = $agingStartCol;
foreach ($agingHeaders as $h) {
    $sheet2->setCellValueByColumnAndRow($agingCol, $rowHeader2, $h);
    $agingCol++;
}

$colIndex = $agingCol + 1;

$projectionStartCol = $colIndex;

$sqlbulan = mysqli_query($conn1,"
SELECT CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)), LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun
FROM dim_date
WHERE kode_tanggal BETWEEN
CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01')
AND CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),
LPAD(IF(MONTH('$end_date')+5 > 12, MOD(MONTH('$end_date')+5,12), MONTH('$end_date')+5),2,'0'),'01')
GROUP BY bulan,tahun
ORDER BY kode_tanggal ASC
");

$projectionHeaders = ['Due'];
while($rowbulan = mysqli_fetch_array($sqlbulan)){
    $projectionHeaders[] = $rowbulan['bulan_tahun'];
}
$projectionHeaders[] = 'Total';

$sheet2->setCellValueByColumnAndRow($projectionStartCol, $rowHeader1, 'Account Payable Based on Due Date Projection');
$sheet2->mergeCellsByColumnAndRow($projectionStartCol, $rowHeader1, $projectionStartCol + count($projectionHeaders) - 1, $rowHeader1);

$projectionCol = $projectionStartCol;
foreach ($projectionHeaders as $h) {
    $sheet2->setCellValueByColumnAndRow($projectionCol, $rowHeader2, $h);
    $projectionCol++;
}

$lastColIndex = $projectionCol - 1;

$sheet2->getStyleByColumnAndRow(1, $rowHeader1, $lastColIndex, $rowHeader2)->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
]);

$sheet2->getStyleByColumnAndRow(1, $rowHeader1, count($headersFix), $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE4E1');

$sheet2->getStyleByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) -1, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('98FB98');

$sheet2->getStyleByColumnAndRow($projectionStartCol, $rowHeader1, $lastColIndex, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('87CEFA');



$rowNum = $rowHeader2 + 1;
$no = 1;

$sql = mysqli_query($conn2, "WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_kbon, tgl_kbon, duedate, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_kontrabon a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

pph as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

ded_lp as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

ded_lp_before as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal between '$start_date' and '$end_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

saldo_in as (select supplier, no_kbon, tgl_kbon, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_kbon, tgl_kbon, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon_before
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon) a GROUP BY no_kbon),

Saldo_out as (select no_journal, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(uang_muka_before,0)) uang_muka_before, SUM(COALESCE(uang_muka,0)) uang_muka, SUM(COALESCE(pph_before,0)) pph_before, SUM(COALESCE(pph,0)) pph, SUM(COALESCE(potongan_before,0)) potongan_before, SUM(COALESCE(potongan,0)) potongan, SUM(COALESCE(ded_lp_before,0)) ded_lp_before, SUM(COALESCE(ded_lp,0)) ded_lp, SUM(COALESCE(ded_gm_before,0)) ded_gm_before, SUM(COALESCE(ded_gm,0)) ded_gm FROM (select no_journal, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, pph pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, total potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, total potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, total ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, total ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, total ded_gm_before, 0 ded_gm from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, total ded_gm from ded_gm_before) A GROUP BY no_journal),

data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_lp_before,2),0) ded_lp_before, COALESCE(round(ded_lp,2),0) ded_lp, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon),

mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + pph_before + potongan_before - (ded_lp_before + ded_gm_before)) saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + pph + uang_muka + potongan - (ded_lp - ded_gm)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in + pph + uang_muka + potongan - (ded_lp - ded_gm)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

select supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
WHEN duedate > '$end_date' THEN saldo_akhir_idr
ELSE 0
END AS due_current,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
THEN saldo_akhir_idr
ELSE 0
END AS due_1_30,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
THEN saldo_akhir_idr
ELSE 0
END AS due_31_60,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
THEN saldo_akhir_idr
ELSE 0
END AS due_61_90,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
THEN saldo_akhir_idr
ELSE 0
END AS due_91_120,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
THEN saldo_akhir_idr
ELSE 0
END AS due_121_180,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
THEN saldo_akhir_idr
ELSE 0
END AS due_181_360,

CASE
WHEN DATEDIFF('$end_date', duedate) > 360
THEN saldo_akhir_idr
ELSE 0
END AS due_gt_360,

(
CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
) AS total_due,

CASE
WHEN duedate <= '$end_date'
THEN saldo_akhir_idr
ELSE 0
END AS pro_due,

CASE
WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due0,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due1,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due2,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due3,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due4,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due5,

(
CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END
) AS tot_produe from report_mutasi $where"); // isi query kamu

while($data = mysqli_fetch_assoc($sql)){
    $col = 1; // pakai index angka untuk konsisten
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $no++);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['supplier']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['no_kbon']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['tgl_kbon']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['duedate']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['curr']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_awal']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['total_in']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['uang_muka']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['potongan']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['ded_lp']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['ded_gm']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['reverse_kontrabon']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['rate']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir_idr']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['no_coa']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['nama_coa']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['item_type1']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['item_type2']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['relasi']);
    $col++; // spacer

    // AGING
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['due_current']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['due_1_30']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['due_31_60']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['due_61_90']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['due_91_120']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['due_121_180']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['due_181_360']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['due_gt_360']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['total_due']);
    $col++; // spacer

    // PROJECTION
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due0']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due1']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due2']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due3']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due4']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due5']);
    $sheet2->setCellValueByColumnAndRow($col++, $rowNum, $data['tot_produe']);

    $rowNum++;
}


$highestColumn = Coordinate::stringFromColumnIndex($lastColIndex);

$sheet2->getStyle('G6:'.$highestColumn.$rowNum)
      ->getNumberFormat()->setFormatCode('#,##0.00');

$sheet2->getStyle('A4:'.$highestColumn.($rowNum-1))
      ->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]]);

for($c=1; $c<=$lastColIndex; $c++){
    $colLetter = Coordinate::stringFromColumnIndex($c);
    $sheet2->getColumnDimension($colLetter)->setAutoSize(true);
}

$sheet2->freezePaneByColumnAndRow(1, $rowHeader2 + 1);




/*
|---------------------------------------------------------------------------------------------------------------------------------------------------------
| SHEET 3 AP-List Payment
|---------------------------------------------------------------------------------------------------------------------------------------------------------
*/

$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('AP-List Payment');


$sheet3->setCellValue('A1', 'Payable Card Statement - LIST PAYMENT');
$sheet3->setCellValue('A2', 'PERIODE: '.$start_date_text.' - '.$end_date_text);
$sheet3->mergeCells('A1:AM1');
$sheet3->mergeCells('A2:AM2');
$sheet3->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);

$rowHeader1 = 4;
$rowHeader2 = 5;

$headersFix = [
    'No','Nama Supplier','List Payment Number','List Payment Date','Due Date','Created By',
    'Currency','Begining Balance','Addition','Deduction Bank',
    'Deduction Non Bank','Deduction Cash','Ending Balance',
    'Rate','Ending Balance IDR','COA No','COA Name',
    'Item Type 1','Item Type 2','Relationship'
];

$colIndex = 1;
foreach ($headersFix as $h) {
    $sheet3->setCellValueByColumnAndRow($colIndex, $rowHeader1, $h);
    $sheet3->mergeCellsByColumnAndRow($colIndex, $rowHeader1, $colIndex, $rowHeader2);
    $colIndex++;
}

$colIndex++;

$agingStartCol = $colIndex;
$agingHeaders = ['Current','1-30','31-60','61-90','91-120','121-180','181-360','>360','Total'];

$sheet3->setCellValueByColumnAndRow($agingStartCol, $rowHeader1, 'Account Payable Aging Based on Due Date');
$sheet3->mergeCellsByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) - 1, $rowHeader1);

$agingCol = $agingStartCol;
foreach ($agingHeaders as $h) {
    $sheet3->setCellValueByColumnAndRow($agingCol, $rowHeader2, $h);
    $agingCol++;
}

$colIndex = $agingCol + 1;

$projectionStartCol = $colIndex;

$sqlbulan = mysqli_query($conn1,"
SELECT CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)), LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun
FROM dim_date
WHERE kode_tanggal BETWEEN
CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01')
AND CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),
LPAD(IF(MONTH('$end_date')+5 > 12, MOD(MONTH('$end_date')+5,12), MONTH('$end_date')+5),2,'0'),'01')
GROUP BY bulan,tahun
ORDER BY kode_tanggal ASC
");

$projectionHeaders = ['Due'];
while($rowbulan = mysqli_fetch_array($sqlbulan)){
    $projectionHeaders[] = $rowbulan['bulan_tahun'];
}
$projectionHeaders[] = 'Total';

$sheet3->setCellValueByColumnAndRow($projectionStartCol, $rowHeader1, 'Account Payable Based on Due Date Projection');
$sheet3->mergeCellsByColumnAndRow($projectionStartCol, $rowHeader1, $projectionStartCol + count($projectionHeaders) - 1, $rowHeader1);

$projectionCol = $projectionStartCol;
foreach ($projectionHeaders as $h) {
    $sheet3->setCellValueByColumnAndRow($projectionCol, $rowHeader2, $h);
    $projectionCol++;
}

$lastColIndex = $projectionCol - 1;

$sheet3->getStyleByColumnAndRow(1, $rowHeader1, $lastColIndex, $rowHeader2)->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
]);

$sheet3->getStyleByColumnAndRow(1, $rowHeader1, count($headersFix), $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE4E1');

$sheet3->getStyleByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) -1, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('98FB98');

$sheet3->getStyleByColumnAndRow($projectionStartCol, $rowHeader1, $lastColIndex, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('87CEFA');



$rowNum = $rowHeader2 + 1;
$no = 1;

$sql = mysqli_query($conn2, "WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_listpayment a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_lp as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_payment
),

in_lp_before as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_payment
),

lp as (
    SELECT no_payment, SUM(amount) total_payment, SUM(pph_value) total_pph FROM list_payment WHERE status != 'Cancel' GROUP BY no_payment
),

payment_all as (
    SELECT list_payment_id no_payment, payment_ftr_id no_pay, ttl_bayar total_bayar, create_date FROM payment_ftr WHERE status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
        UNION ALL
    SELECT a.no_reff, b.no_bankout, a.for_balance, b.create_date FROM b_bankout_det a JOIN b_bankout_h b ON a.no_bankout = b.no_bankout WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
    UNION ALL
    SELECT a.no_reff, b.no_pco, a.total, b.create_date FROM c_petty_cashout_det a JOIN c_petty_cashout_h b ON b.no_pco = a.no_pco WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
),

total_paid as (
    SELECT no_payment, SUM(total_bayar) as total_bayar_lp FROM payment_all GROUP BY no_payment
),

ranked_payment as (
    SELECT p.*, ROW_NUMBER() OVER (PARTITION BY no_payment ORDER BY create_date, no_pay) as rn, COUNT(*) OVER (PARTITION BY no_payment) as cnt FROM payment_all p
),

all_payment as (
SELECT
    r.no_payment,
    lp.total_payment,
    lp.total_pph,
    r.no_pay,
    r.total_bayar,

    CASE
        WHEN r.rn = 1 AND lp.total_pph > 0
        THEN 'Y'
        ELSE 'N'
    END AS pph_flag,

    CASE
        WHEN r.rn = 1
        THEN lp.total_pph
        ELSE 0
    END AS pph_value

FROM ranked_payment r
JOIN lp ON lp.no_payment = r.no_payment
ORDER BY r.no_payment
),

pay_non_bank as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY list_payment_id),

pay_non_bank_before as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY list_payment_id),

pay_bank as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date between '$start_date' and '$end_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_bank_before as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date < '$start_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_cash as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff),

pay_cash_before as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco < '$start_date' GROUP BY a.no_reff),

saldo_in as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp_before
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp) a GROUP BY no_payment),

saldo_out as (select no_reff, ROUND(SUM(COALESCE(pay_bank_before,0)),2) pay_bank_before, ROUND(SUM(COALESCE(pay_bank,0)),2) pay_bank, ROUND(SUM(COALESCE(pay_non_bank_before,0)),2) pay_non_bank_before, ROUND(SUM(COALESCE(pay_non_bank,0)),2) pay_non_bank, ROUND(SUM(COALESCE(pay_cash_before,0)),2) pay_cash_before, ROUND(SUM(COALESCE(pay_cash,0)),2) pay_cash, ROUND(SUM(COALESCE(pph_before,0) * -1),2) pph_before, ROUND(SUM(COALESCE(pph,0) * -1),2) pph FROM (
select no_reff, total pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank_before
UNION ALL
select no_reff, 0 pay_bank_before, total pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, ttl_bayar pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank_before
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, ttl_bayar pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, total pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_cash_before
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, total pay_cash, 0 pph_before, 0 pph from pay_cash
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, pph pph_before, 0 pph from in_lp_before
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, pph from in_lp
) a GROUP BY no_reff),

data_det as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, ROUND(COALESCE(saldo_awal,0) - COALESCE(pay_bank_before,0) - COALESCE(pay_non_bank_before,0) - COALESCE(pay_cash_before,0),2) saldo_awal, COALESCE(total_in,0) total_in, coalesce(pay_bank,0) pay_bank, coalesce(pay_non_bank,0) pay_non_bank, coalesce(pay_cash,0) pay_cash, no_coa, nama_coa, item_type1, item_type2, relasi, rate from saldo_in a LEFT JOIN saldo_out b on b.no_reff = a.no_payment),

mutasi as (select supplier, no_payment, tgl_payment, duedate, create_user, a.curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, ROUND(saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash),2) saldo_akhir, IFNULL(b.rate,1) rate, ROUND((saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash)) * IFNULL(b.rate,1),2) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from data_det a LEFT JOIN rate b on b.curr = a.curr)


select supplier, no_payment, tgl_payment, duedate, create_user, curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN saldo_akhir_idr
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END
    ) AS tot_produe from mutasi $where"); // isi query kamu

while($data = mysqli_fetch_assoc($sql)){
    $col = 1; // pakai index angka untuk konsisten
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $no++);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['supplier']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['no_payment']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['tgl_payment']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['duedate']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['create_user']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['curr']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_awal']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['total_in']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pay_bank']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pay_non_bank']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pay_cash']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['rate']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir_idr']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['no_coa']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['nama_coa']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['item_type1']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['item_type2']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['relasi']);
    $col++; // spacer

    // AGING
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['due_current']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['due_1_30']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['due_31_60']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['due_61_90']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['due_91_120']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['due_121_180']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['due_181_360']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['due_gt_360']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['total_due']);
    $col++; // spacer

    // PROJECTION
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due0']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due1']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due2']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due3']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due4']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due5']);
    $sheet3->setCellValueByColumnAndRow($col++, $rowNum, $data['tot_produe']);

    $rowNum++;
}


$highestColumn = Coordinate::stringFromColumnIndex($lastColIndex);

$sheet3->getStyle('G6:'.$highestColumn.$rowNum)
      ->getNumberFormat()->setFormatCode('#,##0.00');

$sheet3->getStyle('A4:'.$highestColumn.($rowNum-1))
      ->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]]);

for($c=1; $c<=$lastColIndex; $c++){
    $colLetter = Coordinate::stringFromColumnIndex($c);
    $sheet3->getColumnDimension($colLetter)->setAutoSize(true);
}

$sheet3->freezePaneByColumnAndRow(1, $rowHeader2 + 1);


/*
|---------------------------------------------------------------------------------------------------------------------------------------------------------
| SHEET 4 AP- Summary Supllier
|---------------------------------------------------------------------------------------------------------------------------------------------------------
*/

$sheet4 = $spreadsheet->createSheet();
$sheet4->setTitle('AP-Sum Supp');


$sheet4->setCellValue('A1', 'Payable Card Statement - SUMMARY SUPPLIER');
$sheet4->setCellValue('A2', 'PERIODE: '.$start_date_text.' - '.$end_date_text);
$sheet4->mergeCells('A1:AG1');
$sheet4->mergeCells('A2:AG2');
$sheet4->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);

$rowHeader1 = 4;
$rowHeader2 = 5;

$headersFix = [
    'No','Nama Supplier','Currency','Begining Balance','Addition','Reverse',
    'Deduction Advance','Deduction Other','Deduction LP','Deduction GM',
    'Adjustment','Ending Balance','Rate', 'Ending Balance IDR'
];

$colIndex = 1;
foreach ($headersFix as $h) {
    $sheet4->setCellValueByColumnAndRow($colIndex, $rowHeader1, $h);
    $sheet4->mergeCellsByColumnAndRow($colIndex, $rowHeader1, $colIndex, $rowHeader2);
    $colIndex++;
}

$colIndex++;

$agingStartCol = $colIndex;
$agingHeaders = ['Current','1-30','31-60','61-90','91-120','121-180','181-360','>360','Total'];

$sheet4->setCellValueByColumnAndRow($agingStartCol, $rowHeader1, 'Account Payable Aging Based on Due Date');
$sheet4->mergeCellsByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) - 1, $rowHeader1);

$agingCol = $agingStartCol;
foreach ($agingHeaders as $h) {
    $sheet4->setCellValueByColumnAndRow($agingCol, $rowHeader2, $h);
    $agingCol++;
}

$colIndex = $agingCol + 1;

$projectionStartCol = $colIndex;

$sqlbulan = mysqli_query($conn1,"
SELECT CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)), LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun
FROM dim_date
WHERE kode_tanggal BETWEEN
CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01')
AND CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),
LPAD(IF(MONTH('$end_date')+5 > 12, MOD(MONTH('$end_date')+5,12), MONTH('$end_date')+5),2,'0'),'01')
GROUP BY bulan,tahun
ORDER BY kode_tanggal ASC
");

$projectionHeaders = ['Due'];
while($rowbulan = mysqli_fetch_array($sqlbulan)){
    $projectionHeaders[] = $rowbulan['bulan_tahun'];
}
$projectionHeaders[] = 'Total';

$sheet4->setCellValueByColumnAndRow($projectionStartCol, $rowHeader1, 'Account Payable Based on Due Date Projection');
$sheet4->mergeCellsByColumnAndRow($projectionStartCol, $rowHeader1, $projectionStartCol + count($projectionHeaders) - 1, $rowHeader1);

$projectionCol = $projectionStartCol;
foreach ($projectionHeaders as $h) {
    $sheet4->setCellValueByColumnAndRow($projectionCol, $rowHeader2, $h);
    $projectionCol++;
}

$lastColIndex = $projectionCol - 1;

$sheet4->getStyleByColumnAndRow(1, $rowHeader1, $lastColIndex, $rowHeader2)->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
]);

$sheet4->getStyleByColumnAndRow(1, $rowHeader1, count($headersFix), $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE4E1');

$sheet4->getStyleByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) -1, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('98FB98');

$sheet4->getStyleByColumnAndRow($projectionStartCol, $rowHeader1, $lastColIndex, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('87CEFA');



$rowNum = $rowHeader2 + 1;
$no = 1;

$sql = mysqli_query($conn2, "WITH
bpb as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

po_bpb as (select a.bpbno_int, b.supplier, a.pono, c.jml_pterms as top from bpb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier LEFT JOIN po_header c on c.pono = a.pono LEFT JOIN po_header_draft d on d.id = c.id_draft where a.bpbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int
UNION
select a.bppbno_int, b.supplier, '' pono, 0 top from bppb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.bppbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bppbno_int),

saldo_awal as (select supplier, no_bpb, tgl_bpb, 0 top, duedate, curr, total, rate, total_idr, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_bpb a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

trx_in as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_out_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_gm as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

trx_out_gm_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

saldo_in as (select supplier, no_bpb, tgl_bpb, top, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_bpb, tgl_bpb, top, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in_before
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in) a GROUP BY no_bpb, no_coa),

saldo_out_per_coa as (select no_journal, no_coa, sum(COALESCE(reverse_bpb_before,0)) reverse_bpb_before, sum(COALESCE(reverse_bpb,0)) reverse_bpb, sum(COALESCE(gm_before,0)) gm_before, sum(COALESCE(gm,0)) gm from (select no_journal, no_coa, 0 reverse_bpb_before, total reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse
UNION ALL
select no_journal, no_coa, total reverse_bpb_before, 0 reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse_before
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, 0 gm_before, total gm from trx_out_gm
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, total gm_before, 0 gm from trx_out_gm_before) a GROUP BY no_journal, no_coa),

saldo_out_per_bpb as (SELECT reff_doc, SUM(COALESCE(kontrabon_before,0)) kontrabon_before, SUM(COALESCE(kontrabon,0)) kontrabon, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(kontrabon_revisi_before,0)) kontrabon_revisi_before, SUM(COALESCE(kontrabon_revisi,0)) kontrabon_revisi, SUM(COALESCE(reverse_kontrabon_revisi_before,0)) reverse_kontrabon_revisi_before, SUM(COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon_revisi from (select reff_doc, 0 kontrabon_before, total kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb
UNION ALL
select reff_doc, total kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, total kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, total reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, total reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before) a GROUP BY reff_doc),

data_mutasi as (select a.supplier, a.no_bpb, a.tgl_bpb, a.duedate, a.curr, a.rate, a.no_coa, a.nama_coa, a.item_type1, a.item_type2, a.relasi, coalesce(a.saldo_awal,0) saldo_awal, coalesce(a.total_in,0) total_in, reverse_bpb_before, reverse_bpb, gm_before, gm, IF(kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_before, IF(kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon, IF(reverse_kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_before, IF(reverse_kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon, IF(kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi_before, IF(kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi, IF(reverse_kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi_before, IF(reverse_kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi from saldo_in a LEFT JOIN saldo_out_per_coa b on b.no_coa = a.no_coa and b.no_journal = a.no_bpb LEFT JOIN saldo_out_per_bpb c on c.reff_doc = a.no_bpb),

mutasi_det as (select supplier, no_bpb, tgl_bpb, duedate, curr, rate, (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0) - COALESCE(gm_before,0) - COALESCE(kontrabon_before,0) - COALESCE(kontrabon_revisi_before,0) + COALESCE(reverse_kontrabon_before,0) + COALESCE(reverse_kontrabon_revisi_before,0)) saldo_awal, COALESCE(total_in,0) in_bpb, COALESCE(reverse_bpb,0) reverse_bpb, (COALESCE(kontrabon,0) + COALESCE(kontrabon_revisi,0)) ded_kontrabon, (COALESCE(reverse_kontrabon,0) + COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon, COALESCE(gm,0) gm, no_coa, nama_coa, item_type1, item_type2, relasi from data_mutasi),

mutasi as (select supplier, no_bpb, tgl_bpb, duedate, a.curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir, IFNULL(b.rate,1) rate, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi_det a LEFT JOIN rate b on b.curr = a.curr),

laporan_mutasi as (SELECT supplier, no_bpb, tgl_bpb, duedate, curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir, rate, (saldo_akhir * rate) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN (saldo_akhir * rate) ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END
    ) AS tot_produe,
        CASE
            WHEN no_bpb LIKE '%RO%' 
              OR no_bpb LIKE '%OUT%' 
            THEN -1
            ELSE 1
        END AS f
        
    from mutasi)
        
        SELECT
    supplier,
    no_bpb,
    tgl_bpb,
    duedate,
    curr,

    saldo_awal * f AS saldo_awal,
    in_bpb * f AS in_bpb,
    reverse_bpb * f AS reverse_bpb,
    ded_kontrabon * f AS ded_kontrabon,
    reverse_kontrabon * f AS reverse_kontrabon,
    gm * f AS gm,
    saldo_akhir * f AS saldo_akhir,
    rate,
    saldo_akhir_idr * f AS saldo_akhir_idr,

    no_coa,
    nama_coa,
    item_type1,
    item_type2,
    relasi,

    due_current * f AS due_current,
    due_1_30 * f AS due_1_30,
    due_31_60 * f AS due_31_60,
    due_61_90 * f AS due_61_90,
    due_91_120 * f AS due_91_120,
    due_121_180 * f AS due_121_180,
    due_181_360 * f AS due_181_360,
    due_gt_360 * f AS due_gt_360,
    total_due * f AS total_due,

    pro_due * f AS pro_due,
    pro_due0 * f AS pro_due0,
    pro_due1 * f AS pro_due1,
    pro_due2 * f AS pro_due2,
    pro_due3 * f AS pro_due3,
    pro_due4 * f AS pro_due4,
    pro_due5 * f AS pro_due5,
    tot_produe * f AS tot_produe from laporan_mutasi  $where ),
        
        kontrabon as (
        WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_kbon, tgl_kbon, duedate, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_kontrabon a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

pph as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

ded_lp as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

ded_lp_before as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal between '$start_date' and '$end_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

saldo_in as (select supplier, no_kbon, tgl_kbon, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_kbon, tgl_kbon, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon_before
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon) a GROUP BY no_kbon),

Saldo_out as (select no_journal, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(uang_muka_before,0)) uang_muka_before, SUM(COALESCE(uang_muka,0)) uang_muka, SUM(COALESCE(pph_before,0)) pph_before, SUM(COALESCE(pph,0)) pph, SUM(COALESCE(potongan_before,0)) potongan_before, SUM(COALESCE(potongan,0)) potongan, SUM(COALESCE(ded_lp_before,0)) ded_lp_before, SUM(COALESCE(ded_lp,0)) ded_lp, SUM(COALESCE(ded_gm_before,0)) ded_gm_before, SUM(COALESCE(ded_gm,0)) ded_gm FROM (select no_journal, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, pph pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, total potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, total potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, total ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, total ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, total ded_gm_before, 0 ded_gm from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, total ded_gm from ded_gm_before) A GROUP BY no_journal),

data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_lp_before,2),0) ded_lp_before, COALESCE(round(ded_lp,2),0) ded_lp, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon),

mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + pph_before + potongan_before - (ded_lp_before + ded_gm_before)) saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + pph + uang_muka + potongan - (ded_lp - ded_gm)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in + pph + uang_muka + potongan - (ded_lp - ded_gm)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

select supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
WHEN duedate > '$end_date' THEN saldo_akhir_idr
ELSE 0
END AS due_current,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
THEN saldo_akhir_idr
ELSE 0
END AS due_1_30,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
THEN saldo_akhir_idr
ELSE 0
END AS due_31_60,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
THEN saldo_akhir_idr
ELSE 0
END AS due_61_90,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
THEN saldo_akhir_idr
ELSE 0
END AS due_91_120,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
THEN saldo_akhir_idr
ELSE 0
END AS due_121_180,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
THEN saldo_akhir_idr
ELSE 0
END AS due_181_360,

CASE
WHEN DATEDIFF('$end_date', duedate) > 360
THEN saldo_akhir_idr
ELSE 0
END AS due_gt_360,

(
CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
) AS total_due,

CASE
WHEN duedate <= '$end_date'
THEN saldo_akhir_idr
ELSE 0
END AS pro_due,

CASE
WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due0,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due1,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due2,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due3,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due4,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due5,

(
CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END
) AS tot_produe from report_mutasi  $where ),

list_payment as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_listpayment a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_lp as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_payment
),

in_lp_before as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_payment
),

lp as (
    SELECT no_payment, SUM(amount) total_payment, SUM(pph_value) total_pph FROM list_payment WHERE status != 'Cancel' GROUP BY no_payment
),

payment_all as (
    SELECT list_payment_id no_payment, payment_ftr_id no_pay, ttl_bayar total_bayar, create_date FROM payment_ftr WHERE status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
        UNION ALL
    SELECT a.no_reff, b.no_bankout, a.for_balance, b.create_date FROM b_bankout_det a JOIN b_bankout_h b ON a.no_bankout = b.no_bankout WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
    UNION ALL
    SELECT a.no_reff, b.no_pco, a.total, b.create_date FROM c_petty_cashout_det a JOIN c_petty_cashout_h b ON b.no_pco = a.no_pco WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
),

total_paid as (
    SELECT no_payment, SUM(total_bayar) as total_bayar_lp FROM payment_all GROUP BY no_payment
),

ranked_payment as (
    SELECT p.*, ROW_NUMBER() OVER (PARTITION BY no_payment ORDER BY create_date, no_pay) as rn, COUNT(*) OVER (PARTITION BY no_payment) as cnt FROM payment_all p
),

all_payment as (
SELECT
    r.no_payment,
    lp.total_payment,
    lp.total_pph,
    r.no_pay,
    r.total_bayar,

    CASE
        WHEN r.rn = 1 AND lp.total_pph > 0
        THEN 'Y'
        ELSE 'N'
    END AS pph_flag,

    CASE
        WHEN r.rn = 1
        THEN lp.total_pph
        ELSE 0
    END AS pph_value

FROM ranked_payment r
JOIN lp ON lp.no_payment = r.no_payment
ORDER BY r.no_payment
),

pay_non_bank as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY list_payment_id),

pay_non_bank_before as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY list_payment_id),

pay_bank as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date between '$start_date' and '$end_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_bank_before as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date < '$start_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_cash as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff),

pay_cash_before as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco < '$start_date' GROUP BY a.no_reff),

saldo_in as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp_before
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp) a GROUP BY no_payment),

saldo_out as (select no_reff, ROUND(SUM(COALESCE(pay_bank_before,0)),2) pay_bank_before, ROUND(SUM(COALESCE(pay_bank,0)),2) pay_bank, ROUND(SUM(COALESCE(pay_non_bank_before,0)),2) pay_non_bank_before, ROUND(SUM(COALESCE(pay_non_bank,0)),2) pay_non_bank, ROUND(SUM(COALESCE(pay_cash_before,0)),2) pay_cash_before, ROUND(SUM(COALESCE(pay_cash,0)),2) pay_cash, ROUND(SUM(COALESCE(pph_before,0) * -1),2) pph_before, ROUND(SUM(COALESCE(pph,0) * -1),2) pph FROM (
select no_reff, total pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank_before
UNION ALL
select no_reff, 0 pay_bank_before, total pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, ttl_bayar pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank_before
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, ttl_bayar pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, total pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_cash_before
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, total pay_cash, 0 pph_before, 0 pph from pay_cash
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, pph pph_before, 0 pph from in_lp_before
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, pph from in_lp
) a GROUP BY no_reff),

data_det as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, ROUND(COALESCE(saldo_awal,0) - COALESCE(pay_bank_before,0) - COALESCE(pay_non_bank_before,0) - COALESCE(pay_cash_before,0),2) saldo_awal, COALESCE(total_in,0) total_in, coalesce(pay_bank,0) pay_bank, coalesce(pay_non_bank,0) pay_non_bank, coalesce(pay_cash,0) pay_cash, no_coa, nama_coa, item_type1, item_type2, relasi, rate from saldo_in a LEFT JOIN saldo_out b on b.no_reff = a.no_payment),

mutasi as (select supplier, no_payment, tgl_payment, duedate, create_user, a.curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, ROUND(saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash),2) saldo_akhir, IFNULL(b.rate,1) rate, ROUND((saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash)) * IFNULL(b.rate,1),2) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from data_det a LEFT JOIN rate b on b.curr = a.curr)


select supplier, no_payment, tgl_payment, duedate, create_user, curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN saldo_akhir_idr
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END
    ) AS tot_produe from mutasi  $where )

        SELECT 
        supplier,
    a.curr,
        ROUND(SUM(saldo_awal),2) saldo_awal,
        ROUND(SUM(in_bpb),2) addition,
        ROUND(SUM(reverse_bpb),2) reverse,
        ROUND(SUM(uang_muka),2) deduction_advance,
        ROUND(SUM(potongan),2) deduction_other,
        (ROUND(SUM(pay_bank),2) + ROUND(SUM(pay_non_bank),2) + ROUND(SUM(pay_cash),2)) deduction_lp,
        ROUND(SUM(gm),2) deduction_gm,
        ((ROUND(SUM(add_lp),2) - ROUND(SUM(ded_lp),2)) + (ROUND(SUM(add_kbon),2) - ROUND(SUM(ded_bpb),2))) adjust,
        ROUND(SUM(saldo_akhir),2) saldo_akhir,
        ROUND(SUM(saldo_akhir * IFNULL(b.rate,1)),2) saldo_akhir_idr,
        IFNULL(b.rate,1) rate,

    ROUND(SUM(due_current),2) due_current,
    ROUND(SUM(due_1_30),2) due_1_30,
    ROUND(SUM(due_31_60),2) due_31_60,
    ROUND(SUM(due_61_90),2) due_61_90,
    ROUND(SUM(due_91_120),2) due_91_120,
    ROUND(SUM(due_121_180),2) due_121_180,
    ROUND(SUM(due_181_360),2) due_181_360,
    ROUND(SUM(due_gt_360),2) due_gt_360,
    ROUND(SUM(total_due),2) total_due,
    ROUND(SUM(pro_due),2) pro_due,
    ROUND(SUM(pro_due0),2) pro_due0,
    ROUND(SUM(pro_due1),2) pro_due1,
    ROUND(SUM(pro_due2),2) pro_due2,
    ROUND(SUM(pro_due3),2) pro_due3,
    ROUND(SUM(pro_due4),2) pro_due4,
    ROUND(SUM(pro_due5),2) pro_due5,
    ROUND(SUM(tot_produe),2) tot_produe FROM (select supplier, curr, saldo_awal, in_bpb, 0 uang_muka, 0 potongan, 0 ded_lp, ded_kontrabon ded_bpb, 0 add_kbon, 0 add_lp, 0 pay_bank, 0 pay_non_bank, 0 pay_cash, reverse_bpb, gm, saldo_akhir, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_gt_360, due_91_120, due_121_180, due_181_360, total_due, pro_due2, pro_due, pro_due0, pro_due1, pro_due3, pro_due4, pro_due5, tot_produe from bpb
        UNION ALL
        select supplier, curr, saldo_awal, 0 in_bpb, uang_muka, potongan, ded_lp, 0 ded_bpb, total_in add_kbon, 0 add_lp, 0 pay_bank, 0 pay_non_bank, 0 pay_cash, 0 reverse_bpb, ded_gm, saldo_akhir, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from kontrabon
        UNION ALL
        select supplier, curr, saldo_awal, 0 in_bpb, 0 uang_muka, 0 potongan, 0 ded_lp, 0 ded_bpb, 0 add_kbon, total_in add_lp, pay_bank, pay_non_bank, pay_cash, 0 reverse_bpb, 0 gm, saldo_akhir, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from list_payment) a LEFT JOIN (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate) b on b.curr = a.curr GROUP BY supplier,a.curr order by supplier,a.curr asc"); // isi query kamu

while($data = mysqli_fetch_assoc($sql)){
    $col = 1; // pakai index angka untuk konsisten
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $no++);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['supplier']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['curr']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_awal']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['addition']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['reverse']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['deduction_advance']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['deduction_other']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['deduction_lp']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['deduction_gm']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['adjust']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['rate']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir_idr']);
    $col++; // spacer

    // AGING
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['due_current']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['due_1_30']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['due_31_60']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['due_61_90']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['due_91_120']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['due_121_180']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['due_181_360']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['due_gt_360']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['total_due']);
    $col++; // spacer

    // PROJECTION
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due0']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due1']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due2']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due3']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due4']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due5']);
    $sheet4->setCellValueByColumnAndRow($col++, $rowNum, $data['tot_produe']);

    $rowNum++;
}


$highestColumn = Coordinate::stringFromColumnIndex($lastColIndex);

$sheet4->getStyle('D6:'.$highestColumn.$rowNum)
      ->getNumberFormat()->setFormatCode('#,##0.00');

$sheet4->getStyle('A4:'.$highestColumn.($rowNum-1))
      ->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]]);

for($c=1; $c<=$lastColIndex; $c++){
    $colLetter = Coordinate::stringFromColumnIndex($c);
    $sheet4->getColumnDimension($colLetter)->setAutoSize(true);
}

$sheet4->freezePaneByColumnAndRow(1, $rowHeader2 + 1);



/*
|---------------------------------------------------------------------------------------------------------------------------------------------------------
| SHEET 5 AP- SUMMARY TYPE 1
|---------------------------------------------------------------------------------------------------------------------------------------------------------
*/

$sheet5 = $spreadsheet->createSheet();
$sheet5->setTitle('AP-Sum Type 1');


$sheet5->setCellValue('A1', 'Payable Card Statement - SUMMARY TYPE 1');
$sheet5->setCellValue('A2', 'PERIODE: '.$start_date_text.' - '.$end_date_text);
$sheet5->mergeCells('A1:Y1');
$sheet5->mergeCells('A2:Y2');
$sheet5->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);

$rowHeader1 = 4;
$rowHeader2 = 5;

$headersFix = [
    'No','Nama Supplier','Item Type','Relationship','Amount (Equivalent IDR)','Percentage from Total'
];

$colIndex = 1;
foreach ($headersFix as $h) {
    $sheet5->setCellValueByColumnAndRow($colIndex, $rowHeader1, $h);
    $sheet5->mergeCellsByColumnAndRow($colIndex, $rowHeader1, $colIndex, $rowHeader2);
    $colIndex++;
}

$colIndex++;

$agingStartCol = $colIndex;
$agingHeaders = ['Current','1-30','31-60','61-90','91-120','121-180','181-360','>360','Total'];

$sheet5->setCellValueByColumnAndRow($agingStartCol, $rowHeader1, 'Account Payable Aging Based on Due Date');
$sheet5->mergeCellsByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) - 1, $rowHeader1);

$agingCol = $agingStartCol;
foreach ($agingHeaders as $h) {
    $sheet5->setCellValueByColumnAndRow($agingCol, $rowHeader2, $h);
    $agingCol++;
}

$colIndex = $agingCol + 1;

$projectionStartCol = $colIndex;

$sqlbulan = mysqli_query($conn1,"
SELECT CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)), LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun
FROM dim_date
WHERE kode_tanggal BETWEEN
CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01')
AND CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),
LPAD(IF(MONTH('$end_date')+5 > 12, MOD(MONTH('$end_date')+5,12), MONTH('$end_date')+5),2,'0'),'01')
GROUP BY bulan,tahun
ORDER BY kode_tanggal ASC
");

$projectionHeaders = ['Due'];
while($rowbulan = mysqli_fetch_array($sqlbulan)){
    $projectionHeaders[] = $rowbulan['bulan_tahun'];
}
$projectionHeaders[] = 'Total';

$sheet5->setCellValueByColumnAndRow($projectionStartCol, $rowHeader1, 'Account Payable Based on Due Date Projection');
$sheet5->mergeCellsByColumnAndRow($projectionStartCol, $rowHeader1, $projectionStartCol + count($projectionHeaders) - 1, $rowHeader1);

$projectionCol = $projectionStartCol;
foreach ($projectionHeaders as $h) {
    $sheet5->setCellValueByColumnAndRow($projectionCol, $rowHeader2, $h);
    $projectionCol++;
}

$lastColIndex = $projectionCol - 1;

$sheet5->getStyleByColumnAndRow(1, $rowHeader1, $lastColIndex, $rowHeader2)->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
]);

$sheet5->getStyleByColumnAndRow(1, $rowHeader1, count($headersFix), $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE4E1');

$sheet5->getStyleByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) -1, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('98FB98');

$sheet5->getStyleByColumnAndRow($projectionStartCol, $rowHeader1, $lastColIndex, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('87CEFA');



$rowNum = $rowHeader2 + 1;
$no = 1;

$sql = mysqli_query($conn2, "WITH
bpb as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

po_bpb as (select a.bpbno_int, b.supplier, a.pono, c.jml_pterms as top from bpb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier LEFT JOIN po_header c on c.pono = a.pono LEFT JOIN po_header_draft d on d.id = c.id_draft where a.bpbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int
UNION
select a.bppbno_int, b.supplier, '' pono, 0 top from bppb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.bppbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bppbno_int),

saldo_awal as (select supplier, no_bpb, tgl_bpb, 0 top, duedate, curr, total, rate, total_idr, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_bpb a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

trx_in as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_out_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_gm as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

trx_out_gm_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

saldo_in as (select supplier, no_bpb, tgl_bpb, top, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_bpb, tgl_bpb, top, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in_before
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in) a GROUP BY no_bpb, no_coa),

saldo_out_per_coa as (select no_journal, no_coa, sum(COALESCE(reverse_bpb_before,0)) reverse_bpb_before, sum(COALESCE(reverse_bpb,0)) reverse_bpb, sum(COALESCE(gm_before,0)) gm_before, sum(COALESCE(gm,0)) gm from (select no_journal, no_coa, 0 reverse_bpb_before, total reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse
UNION ALL
select no_journal, no_coa, total reverse_bpb_before, 0 reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse_before
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, 0 gm_before, total gm from trx_out_gm
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, total gm_before, 0 gm from trx_out_gm_before) a GROUP BY no_journal, no_coa),

saldo_out_per_bpb as (SELECT reff_doc, SUM(COALESCE(kontrabon_before,0)) kontrabon_before, SUM(COALESCE(kontrabon,0)) kontrabon, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(kontrabon_revisi_before,0)) kontrabon_revisi_before, SUM(COALESCE(kontrabon_revisi,0)) kontrabon_revisi, SUM(COALESCE(reverse_kontrabon_revisi_before,0)) reverse_kontrabon_revisi_before, SUM(COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon_revisi from (select reff_doc, 0 kontrabon_before, total kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb
UNION ALL
select reff_doc, total kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, total kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, total reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, total reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before) a GROUP BY reff_doc),

data_mutasi as (select a.supplier, a.no_bpb, a.tgl_bpb, a.duedate, a.curr, a.rate, a.no_coa, a.nama_coa, a.item_type1, a.item_type2, a.relasi, coalesce(a.saldo_awal,0) saldo_awal, coalesce(a.total_in,0) total_in, reverse_bpb_before, reverse_bpb, gm_before, gm, IF(kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_before, IF(kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon, IF(reverse_kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_before, IF(reverse_kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon, IF(kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi_before, IF(kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi, IF(reverse_kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi_before, IF(reverse_kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi from saldo_in a LEFT JOIN saldo_out_per_coa b on b.no_coa = a.no_coa and b.no_journal = a.no_bpb LEFT JOIN saldo_out_per_bpb c on c.reff_doc = a.no_bpb),

mutasi_det as (select supplier, no_bpb, tgl_bpb, duedate, curr, rate, (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0) - COALESCE(gm_before,0) - COALESCE(kontrabon_before,0) - COALESCE(kontrabon_revisi_before,0) + COALESCE(reverse_kontrabon_before,0) + COALESCE(reverse_kontrabon_revisi_before,0)) saldo_awal, COALESCE(total_in,0) in_bpb, COALESCE(reverse_bpb,0) reverse_bpb, (COALESCE(kontrabon,0) + COALESCE(kontrabon_revisi,0)) ded_kontrabon, (COALESCE(reverse_kontrabon,0) + COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon, COALESCE(gm,0) gm, no_coa, nama_coa, item_type1, item_type2, relasi from data_mutasi),

mutasi as (select supplier, no_bpb, tgl_bpb, duedate, a.curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir, IFNULL(b.rate,1) rate, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi_det a LEFT JOIN rate b on b.curr = a.curr),

laporan_mutasi as (SELECT supplier, no_bpb, tgl_bpb, duedate, curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir, rate, (saldo_akhir * rate) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN (saldo_akhir * rate) ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END
    ) AS tot_produe,
        CASE
            WHEN no_bpb LIKE '%RO%' 
              OR no_bpb LIKE '%OUT%' 
            THEN -1
            ELSE 1
        END AS f
        
    from mutasi)
        
        SELECT
    supplier,
    no_bpb,
    tgl_bpb,
    duedate,
    curr,

    saldo_awal * f AS saldo_awal,
    in_bpb * f AS in_bpb,
    reverse_bpb * f AS reverse_bpb,
    ded_kontrabon * f AS ded_kontrabon,
    reverse_kontrabon * f AS reverse_kontrabon,
    gm * f AS gm,
    saldo_akhir * f AS saldo_akhir,
    rate,
    saldo_akhir_idr * f AS saldo_akhir_idr,

    no_coa,
    nama_coa,
    item_type1,
    item_type2,
    relasi,

    due_current * f AS due_current,
    due_1_30 * f AS due_1_30,
    due_31_60 * f AS due_31_60,
    due_61_90 * f AS due_61_90,
    due_91_120 * f AS due_91_120,
    due_121_180 * f AS due_121_180,
    due_181_360 * f AS due_181_360,
    due_gt_360 * f AS due_gt_360,
    total_due * f AS total_due,

    pro_due * f AS pro_due,
    pro_due0 * f AS pro_due0,
    pro_due1 * f AS pro_due1,
    pro_due2 * f AS pro_due2,
    pro_due3 * f AS pro_due3,
    pro_due4 * f AS pro_due4,
    pro_due5 * f AS pro_due5,
    tot_produe * f AS tot_produe from laporan_mutasi  $where ),
        
        kontrabon as (
        WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_kbon, tgl_kbon, duedate, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_kontrabon a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

pph as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

ded_lp as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

ded_lp_before as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal between '$start_date' and '$end_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

saldo_in as (select supplier, no_kbon, tgl_kbon, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_kbon, tgl_kbon, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon_before
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon) a GROUP BY no_kbon),

Saldo_out as (select no_journal, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(uang_muka_before,0)) uang_muka_before, SUM(COALESCE(uang_muka,0)) uang_muka, SUM(COALESCE(pph_before,0)) pph_before, SUM(COALESCE(pph,0)) pph, SUM(COALESCE(potongan_before,0)) potongan_before, SUM(COALESCE(potongan,0)) potongan, SUM(COALESCE(ded_lp_before,0)) ded_lp_before, SUM(COALESCE(ded_lp,0)) ded_lp, SUM(COALESCE(ded_gm_before,0)) ded_gm_before, SUM(COALESCE(ded_gm,0)) ded_gm FROM (select no_journal, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, pph pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, total potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, total potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, total ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, total ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, total ded_gm_before, 0 ded_gm from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, total ded_gm from ded_gm_before) A GROUP BY no_journal),

data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_lp_before,2),0) ded_lp_before, COALESCE(round(ded_lp,2),0) ded_lp, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon),

mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + pph_before + potongan_before - (ded_lp_before + ded_gm_before)) saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + pph + uang_muka + potongan - (ded_lp - ded_gm)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in + pph + uang_muka + potongan - (ded_lp - ded_gm)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

select supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
WHEN duedate > '$end_date' THEN saldo_akhir_idr
ELSE 0
END AS due_current,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
THEN saldo_akhir_idr
ELSE 0
END AS due_1_30,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
THEN saldo_akhir_idr
ELSE 0
END AS due_31_60,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
THEN saldo_akhir_idr
ELSE 0
END AS due_61_90,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
THEN saldo_akhir_idr
ELSE 0
END AS due_91_120,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
THEN saldo_akhir_idr
ELSE 0
END AS due_121_180,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
THEN saldo_akhir_idr
ELSE 0
END AS due_181_360,

CASE
WHEN DATEDIFF('$end_date', duedate) > 360
THEN saldo_akhir_idr
ELSE 0
END AS due_gt_360,

(
CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
) AS total_due,

CASE
WHEN duedate <= '$end_date'
THEN saldo_akhir_idr
ELSE 0
END AS pro_due,

CASE
WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due0,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due1,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due2,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due3,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due4,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due5,

(
CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END
) AS tot_produe from report_mutasi  $where ),

list_payment as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_listpayment a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_lp as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_payment
),

in_lp_before as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_payment
),

lp as (
    SELECT no_payment, SUM(amount) total_payment, SUM(pph_value) total_pph FROM list_payment WHERE status != 'Cancel' GROUP BY no_payment
),

payment_all as (
    SELECT list_payment_id no_payment, payment_ftr_id no_pay, ttl_bayar total_bayar, create_date FROM payment_ftr WHERE status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
        UNION ALL
    SELECT a.no_reff, b.no_bankout, a.for_balance, b.create_date FROM b_bankout_det a JOIN b_bankout_h b ON a.no_bankout = b.no_bankout WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
    UNION ALL
    SELECT a.no_reff, b.no_pco, a.total, b.create_date FROM c_petty_cashout_det a JOIN c_petty_cashout_h b ON b.no_pco = a.no_pco WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
),

total_paid as (
    SELECT no_payment, SUM(total_bayar) as total_bayar_lp FROM payment_all GROUP BY no_payment
),

ranked_payment as (
    SELECT p.*, ROW_NUMBER() OVER (PARTITION BY no_payment ORDER BY create_date, no_pay) as rn, COUNT(*) OVER (PARTITION BY no_payment) as cnt FROM payment_all p
),

all_payment as (
SELECT
    r.no_payment,
    lp.total_payment,
    lp.total_pph,
    r.no_pay,
    r.total_bayar,

    CASE
        WHEN r.rn = 1 AND lp.total_pph > 0
        THEN 'Y'
        ELSE 'N'
    END AS pph_flag,

    CASE
        WHEN r.rn = 1
        THEN lp.total_pph
        ELSE 0
    END AS pph_value

FROM ranked_payment r
JOIN lp ON lp.no_payment = r.no_payment
ORDER BY r.no_payment
),

pay_non_bank as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY list_payment_id),

pay_non_bank_before as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY list_payment_id),

pay_bank as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date between '$start_date' and '$end_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_bank_before as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date < '$start_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_cash as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff),

pay_cash_before as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco < '$start_date' GROUP BY a.no_reff),

saldo_in as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp_before
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp) a GROUP BY no_payment),

saldo_out as (select no_reff, ROUND(SUM(COALESCE(pay_bank_before,0)),2) pay_bank_before, ROUND(SUM(COALESCE(pay_bank,0)),2) pay_bank, ROUND(SUM(COALESCE(pay_non_bank_before,0)),2) pay_non_bank_before, ROUND(SUM(COALESCE(pay_non_bank,0)),2) pay_non_bank, ROUND(SUM(COALESCE(pay_cash_before,0)),2) pay_cash_before, ROUND(SUM(COALESCE(pay_cash,0)),2) pay_cash, ROUND(SUM(COALESCE(pph_before,0) * -1),2) pph_before, ROUND(SUM(COALESCE(pph,0) * -1),2) pph FROM (
select no_reff, total pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank_before
UNION ALL
select no_reff, 0 pay_bank_before, total pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, ttl_bayar pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank_before
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, ttl_bayar pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, total pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_cash_before
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, total pay_cash, 0 pph_before, 0 pph from pay_cash
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, pph pph_before, 0 pph from in_lp_before
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, pph from in_lp
) a GROUP BY no_reff),

data_det as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, ROUND(COALESCE(saldo_awal,0) - COALESCE(pay_bank_before,0) - COALESCE(pay_non_bank_before,0) - COALESCE(pay_cash_before,0),2) saldo_awal, COALESCE(total_in,0) total_in, coalesce(pay_bank,0) pay_bank, coalesce(pay_non_bank,0) pay_non_bank, coalesce(pay_cash,0) pay_cash, no_coa, nama_coa, item_type1, item_type2, relasi, rate from saldo_in a LEFT JOIN saldo_out b on b.no_reff = a.no_payment),

mutasi as (select supplier, no_payment, tgl_payment, duedate, create_user, a.curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, ROUND(saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash),2) saldo_akhir, IFNULL(b.rate,1) rate, ROUND((saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash)) * IFNULL(b.rate,1),2) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from data_det a LEFT JOIN rate b on b.curr = a.curr)


select supplier, no_payment, tgl_payment, duedate, create_user, curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN saldo_akhir_idr
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END
    ) AS tot_produe from mutasi  $where )

        SELECT supplier,
    item_type1,
    relasi,

    ROUND(SUM(saldo_akhir), 2) AS saldo_akhir,

    ROUND(
        SUM(saldo_akhir) / SUM(SUM(saldo_akhir)) OVER () * 100
    , 2) AS saldo_akhir_persen,

    ROUND(SUM(due_current),2) due_current,
    ROUND(SUM(due_1_30),2) due_1_30,
    ROUND(SUM(due_31_60),2) due_31_60,
    ROUND(SUM(due_61_90),2) due_61_90,
    ROUND(SUM(due_91_120),2) due_91_120,
    ROUND(SUM(due_121_180),2) due_121_180,
    ROUND(SUM(due_181_360),2) due_181_360,
        ROUND(SUM(due_gt_360),2) due_gt_360,
    ROUND(SUM(total_due),2) total_due,
    ROUND(SUM(pro_due),2) pro_due,
    ROUND(SUM(pro_due0),2) pro_due0,
    ROUND(SUM(pro_due1),2) pro_due1,
    ROUND(SUM(pro_due2),2) pro_due2,
    ROUND(SUM(pro_due3),2) pro_due3,
    ROUND(SUM(pro_due4),2) pro_due4,
    ROUND(SUM(pro_due5),2) pro_due5,
    ROUND(SUM(tot_produe),2) tot_produe FROM (select supplier, item_type1, relasi, saldo_akhir_idr saldo_akhir, due_current, due_1_30, due_31_60, due_61_90, due_gt_360, due_91_120, due_121_180, due_181_360, total_due, pro_due2, pro_due, pro_due0, pro_due1, pro_due3, pro_due4, pro_due5, tot_produe from bpb
        UNION ALL
        select supplier, item_type1, relasi, saldo_akhir_idr saldo_akhir, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from kontrabon
        UNION ALL
        select supplier, item_type1, relasi, saldo_akhir_idr saldo_akhir, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from list_payment) a  GROUP BY supplier,item_type1,relasi order by supplier,item_type1 asc"); // isi query kamu

while($data = mysqli_fetch_assoc($sql)){
    $col = 1; // pakai index angka untuk konsisten
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $no++);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['supplier']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['item_type1']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['relasi']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir_persen'] . '%');
    $col++; // spacer

    // AGING
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['due_current']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['due_1_30']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['due_31_60']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['due_61_90']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['due_91_120']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['due_121_180']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['due_181_360']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['due_gt_360']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['total_due']);
    $col++; // spacer

    // PROJECTION
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due0']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due1']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due2']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due3']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due4']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due5']);
    $sheet5->setCellValueByColumnAndRow($col++, $rowNum, $data['tot_produe']);

    $rowNum++;
}


$highestColumn = Coordinate::stringFromColumnIndex($lastColIndex);

$sheet5->getStyle('E6:'.$highestColumn.$rowNum)
      ->getNumberFormat()->setFormatCode('#,##0.00');

$sheet5->getStyle('A4:'.$highestColumn.($rowNum-1))
      ->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]]);

for($c=1; $c<=$lastColIndex; $c++){
    $colLetter = Coordinate::stringFromColumnIndex($c);
    $sheet5->getColumnDimension($colLetter)->setAutoSize(true);
}

$sheet5->freezePaneByColumnAndRow(1, $rowHeader2 + 1);




/*
|---------------------------------------------------------------------------------------------------------------------------------------------------------
| SHEET 6 AP- SUMMARY TYPE 2
|---------------------------------------------------------------------------------------------------------------------------------------------------------
*/

$sheet6 = $spreadsheet->createSheet();
$sheet6->setTitle('AP-Sum Type 2');


$sheet6->setCellValue('A1', 'Payable Card Statement - SUMMARY TYPE 2');
$sheet6->setCellValue('A2', 'PERIODE: '.$start_date_text.' - '.$end_date_text);
$sheet6->mergeCells('A1:Y1');
$sheet6->mergeCells('A2:Y2');
$sheet6->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);

$rowHeader1 = 4;
$rowHeader2 = 5;

$headersFix = [
    'No','Nama Supplier','Item Type','Relationship','Amount (Equivalent IDR)','Percentage from Total'
];

$colIndex = 1;
foreach ($headersFix as $h) {
    $sheet6->setCellValueByColumnAndRow($colIndex, $rowHeader1, $h);
    $sheet6->mergeCellsByColumnAndRow($colIndex, $rowHeader1, $colIndex, $rowHeader2);
    $colIndex++;
}

$colIndex++;

$agingStartCol = $colIndex;
$agingHeaders = ['Current','1-30','31-60','61-90','91-120','121-180','181-360','>360','Total'];

$sheet6->setCellValueByColumnAndRow($agingStartCol, $rowHeader1, 'Account Payable Aging Based on Due Date');
$sheet6->mergeCellsByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) - 1, $rowHeader1);

$agingCol = $agingStartCol;
foreach ($agingHeaders as $h) {
    $sheet6->setCellValueByColumnAndRow($agingCol, $rowHeader2, $h);
    $agingCol++;
}

$colIndex = $agingCol + 1;

$projectionStartCol = $colIndex;

$sqlbulan = mysqli_query($conn1,"
SELECT CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)), LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun
FROM dim_date
WHERE kode_tanggal BETWEEN
CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01')
AND CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),
LPAD(IF(MONTH('$end_date')+5 > 12, MOD(MONTH('$end_date')+5,12), MONTH('$end_date')+5),2,'0'),'01')
GROUP BY bulan,tahun
ORDER BY kode_tanggal ASC
");

$projectionHeaders = ['Due'];
while($rowbulan = mysqli_fetch_array($sqlbulan)){
    $projectionHeaders[] = $rowbulan['bulan_tahun'];
}
$projectionHeaders[] = 'Total';

$sheet6->setCellValueByColumnAndRow($projectionStartCol, $rowHeader1, 'Account Payable Based on Due Date Projection');
$sheet6->mergeCellsByColumnAndRow($projectionStartCol, $rowHeader1, $projectionStartCol + count($projectionHeaders) - 1, $rowHeader1);

$projectionCol = $projectionStartCol;
foreach ($projectionHeaders as $h) {
    $sheet6->setCellValueByColumnAndRow($projectionCol, $rowHeader2, $h);
    $projectionCol++;
}

$lastColIndex = $projectionCol - 1;

$sheet6->getStyleByColumnAndRow(1, $rowHeader1, $lastColIndex, $rowHeader2)->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
]);

$sheet6->getStyleByColumnAndRow(1, $rowHeader1, count($headersFix), $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE4E1');

$sheet6->getStyleByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) -1, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('98FB98');

$sheet6->getStyleByColumnAndRow($projectionStartCol, $rowHeader1, $lastColIndex, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('87CEFA');



$rowNum = $rowHeader2 + 1;
$no = 1;

$sql = mysqli_query($conn2, "WITH
bpb as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

po_bpb as (select a.bpbno_int, b.supplier, a.pono, c.jml_pterms as top from bpb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier LEFT JOIN po_header c on c.pono = a.pono LEFT JOIN po_header_draft d on d.id = c.id_draft where a.bpbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int
UNION
select a.bppbno_int, b.supplier, '' pono, 0 top from bppb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.bppbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bppbno_int),

saldo_awal as (select supplier, no_bpb, tgl_bpb, 0 top, duedate, curr, total, rate, total_idr, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_bpb a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

trx_in as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_out_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_gm as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

trx_out_gm_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

saldo_in as (select supplier, no_bpb, tgl_bpb, top, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_bpb, tgl_bpb, top, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in_before
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in) a GROUP BY no_bpb, no_coa),

saldo_out_per_coa as (select no_journal, no_coa, sum(COALESCE(reverse_bpb_before,0)) reverse_bpb_before, sum(COALESCE(reverse_bpb,0)) reverse_bpb, sum(COALESCE(gm_before,0)) gm_before, sum(COALESCE(gm,0)) gm from (select no_journal, no_coa, 0 reverse_bpb_before, total reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse
UNION ALL
select no_journal, no_coa, total reverse_bpb_before, 0 reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse_before
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, 0 gm_before, total gm from trx_out_gm
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, total gm_before, 0 gm from trx_out_gm_before) a GROUP BY no_journal, no_coa),

saldo_out_per_bpb as (SELECT reff_doc, SUM(COALESCE(kontrabon_before,0)) kontrabon_before, SUM(COALESCE(kontrabon,0)) kontrabon, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(kontrabon_revisi_before,0)) kontrabon_revisi_before, SUM(COALESCE(kontrabon_revisi,0)) kontrabon_revisi, SUM(COALESCE(reverse_kontrabon_revisi_before,0)) reverse_kontrabon_revisi_before, SUM(COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon_revisi from (select reff_doc, 0 kontrabon_before, total kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb
UNION ALL
select reff_doc, total kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, total kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, total reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, total reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before) a GROUP BY reff_doc),

data_mutasi as (select a.supplier, a.no_bpb, a.tgl_bpb, a.duedate, a.curr, a.rate, a.no_coa, a.nama_coa, a.item_type1, a.item_type2, a.relasi, coalesce(a.saldo_awal,0) saldo_awal, coalesce(a.total_in,0) total_in, reverse_bpb_before, reverse_bpb, gm_before, gm, IF(kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_before, IF(kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon, IF(reverse_kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_before, IF(reverse_kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon, IF(kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi_before, IF(kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi, IF(reverse_kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi_before, IF(reverse_kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi from saldo_in a LEFT JOIN saldo_out_per_coa b on b.no_coa = a.no_coa and b.no_journal = a.no_bpb LEFT JOIN saldo_out_per_bpb c on c.reff_doc = a.no_bpb),

mutasi_det as (select supplier, no_bpb, tgl_bpb, duedate, curr, rate, (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0) - COALESCE(gm_before,0) - COALESCE(kontrabon_before,0) - COALESCE(kontrabon_revisi_before,0) + COALESCE(reverse_kontrabon_before,0) + COALESCE(reverse_kontrabon_revisi_before,0)) saldo_awal, COALESCE(total_in,0) in_bpb, COALESCE(reverse_bpb,0) reverse_bpb, (COALESCE(kontrabon,0) + COALESCE(kontrabon_revisi,0)) ded_kontrabon, (COALESCE(reverse_kontrabon,0) + COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon, COALESCE(gm,0) gm, no_coa, nama_coa, item_type1, item_type2, relasi from data_mutasi),

mutasi as (select supplier, no_bpb, tgl_bpb, duedate, a.curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir, IFNULL(b.rate,1) rate, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi_det a LEFT JOIN rate b on b.curr = a.curr),

laporan_mutasi as (SELECT supplier, no_bpb, tgl_bpb, duedate, curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir, rate, (saldo_akhir * rate) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN (saldo_akhir * rate) ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END
    ) AS tot_produe,
        CASE
            WHEN no_bpb LIKE '%RO%' 
              OR no_bpb LIKE '%OUT%' 
            THEN -1
            ELSE 1
        END AS f
        
    from mutasi)
        
        SELECT
    supplier,
    no_bpb,
    tgl_bpb,
    duedate,
    curr,

    saldo_awal * f AS saldo_awal,
    in_bpb * f AS in_bpb,
    reverse_bpb * f AS reverse_bpb,
    ded_kontrabon * f AS ded_kontrabon,
    reverse_kontrabon * f AS reverse_kontrabon,
    gm * f AS gm,
    saldo_akhir * f AS saldo_akhir,
    rate,
    saldo_akhir_idr * f AS saldo_akhir_idr,

    no_coa,
    nama_coa,
    item_type1,
    item_type2,
    relasi,

    due_current * f AS due_current,
    due_1_30 * f AS due_1_30,
    due_31_60 * f AS due_31_60,
    due_61_90 * f AS due_61_90,
    due_91_120 * f AS due_91_120,
    due_121_180 * f AS due_121_180,
    due_181_360 * f AS due_181_360,
    due_gt_360 * f AS due_gt_360,
    total_due * f AS total_due,

    pro_due * f AS pro_due,
    pro_due0 * f AS pro_due0,
    pro_due1 * f AS pro_due1,
    pro_due2 * f AS pro_due2,
    pro_due3 * f AS pro_due3,
    pro_due4 * f AS pro_due4,
    pro_due5 * f AS pro_due5,
    tot_produe * f AS tot_produe from laporan_mutasi  $where ),
        
        kontrabon as (
        WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_kbon, tgl_kbon, duedate, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_kontrabon a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

pph as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

ded_lp as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

ded_lp_before as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal between '$start_date' and '$end_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

saldo_in as (select supplier, no_kbon, tgl_kbon, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_kbon, tgl_kbon, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon_before
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon) a GROUP BY no_kbon),

Saldo_out as (select no_journal, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(uang_muka_before,0)) uang_muka_before, SUM(COALESCE(uang_muka,0)) uang_muka, SUM(COALESCE(pph_before,0)) pph_before, SUM(COALESCE(pph,0)) pph, SUM(COALESCE(potongan_before,0)) potongan_before, SUM(COALESCE(potongan,0)) potongan, SUM(COALESCE(ded_lp_before,0)) ded_lp_before, SUM(COALESCE(ded_lp,0)) ded_lp, SUM(COALESCE(ded_gm_before,0)) ded_gm_before, SUM(COALESCE(ded_gm,0)) ded_gm FROM (select no_journal, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, pph pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, total potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, total potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, total ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, total ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, total ded_gm_before, 0 ded_gm from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, total ded_gm from ded_gm_before) A GROUP BY no_journal),

data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_lp_before,2),0) ded_lp_before, COALESCE(round(ded_lp,2),0) ded_lp, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon),

mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + pph_before + potongan_before - (ded_lp_before + ded_gm_before)) saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + pph + uang_muka + potongan - (ded_lp - ded_gm)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in + pph + uang_muka + potongan - (ded_lp - ded_gm)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

select supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
WHEN duedate > '$end_date' THEN saldo_akhir_idr
ELSE 0
END AS due_current,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
THEN saldo_akhir_idr
ELSE 0
END AS due_1_30,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
THEN saldo_akhir_idr
ELSE 0
END AS due_31_60,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
THEN saldo_akhir_idr
ELSE 0
END AS due_61_90,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
THEN saldo_akhir_idr
ELSE 0
END AS due_91_120,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
THEN saldo_akhir_idr
ELSE 0
END AS due_121_180,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
THEN saldo_akhir_idr
ELSE 0
END AS due_181_360,

CASE
WHEN DATEDIFF('$end_date', duedate) > 360
THEN saldo_akhir_idr
ELSE 0
END AS due_gt_360,

(
CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
) AS total_due,

CASE
WHEN duedate <= '$end_date'
THEN saldo_akhir_idr
ELSE 0
END AS pro_due,

CASE
WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due0,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due1,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due2,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due3,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due4,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due5,

(
CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END
) AS tot_produe from report_mutasi  $where ),

list_payment as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_listpayment a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_lp as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_payment
),

in_lp_before as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_payment
),

lp as (
    SELECT no_payment, SUM(amount) total_payment, SUM(pph_value) total_pph FROM list_payment WHERE status != 'Cancel' GROUP BY no_payment
),

payment_all as (
    SELECT list_payment_id no_payment, payment_ftr_id no_pay, ttl_bayar total_bayar, create_date FROM payment_ftr WHERE status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
        UNION ALL
    SELECT a.no_reff, b.no_bankout, a.for_balance, b.create_date FROM b_bankout_det a JOIN b_bankout_h b ON a.no_bankout = b.no_bankout WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
    UNION ALL
    SELECT a.no_reff, b.no_pco, a.total, b.create_date FROM c_petty_cashout_det a JOIN c_petty_cashout_h b ON b.no_pco = a.no_pco WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
),

total_paid as (
    SELECT no_payment, SUM(total_bayar) as total_bayar_lp FROM payment_all GROUP BY no_payment
),

ranked_payment as (
    SELECT p.*, ROW_NUMBER() OVER (PARTITION BY no_payment ORDER BY create_date, no_pay) as rn, COUNT(*) OVER (PARTITION BY no_payment) as cnt FROM payment_all p
),

all_payment as (
SELECT
    r.no_payment,
    lp.total_payment,
    lp.total_pph,
    r.no_pay,
    r.total_bayar,

    CASE
        WHEN r.rn = 1 AND lp.total_pph > 0
        THEN 'Y'
        ELSE 'N'
    END AS pph_flag,

    CASE
        WHEN r.rn = 1
        THEN lp.total_pph
        ELSE 0
    END AS pph_value

FROM ranked_payment r
JOIN lp ON lp.no_payment = r.no_payment
ORDER BY r.no_payment
),

pay_non_bank as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY list_payment_id),

pay_non_bank_before as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY list_payment_id),

pay_bank as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date between '$start_date' and '$end_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_bank_before as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date < '$start_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_cash as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff),

pay_cash_before as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco < '$start_date' GROUP BY a.no_reff),

saldo_in as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp_before
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp) a GROUP BY no_payment),

saldo_out as (select no_reff, ROUND(SUM(COALESCE(pay_bank_before,0)),2) pay_bank_before, ROUND(SUM(COALESCE(pay_bank,0)),2) pay_bank, ROUND(SUM(COALESCE(pay_non_bank_before,0)),2) pay_non_bank_before, ROUND(SUM(COALESCE(pay_non_bank,0)),2) pay_non_bank, ROUND(SUM(COALESCE(pay_cash_before,0)),2) pay_cash_before, ROUND(SUM(COALESCE(pay_cash,0)),2) pay_cash, ROUND(SUM(COALESCE(pph_before,0) * -1),2) pph_before, ROUND(SUM(COALESCE(pph,0) * -1),2) pph FROM (
select no_reff, total pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank_before
UNION ALL
select no_reff, 0 pay_bank_before, total pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, ttl_bayar pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank_before
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, ttl_bayar pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, total pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_cash_before
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, total pay_cash, 0 pph_before, 0 pph from pay_cash
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, pph pph_before, 0 pph from in_lp_before
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, pph from in_lp
) a GROUP BY no_reff),

data_det as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, ROUND(COALESCE(saldo_awal,0) - COALESCE(pay_bank_before,0) - COALESCE(pay_non_bank_before,0) - COALESCE(pay_cash_before,0),2) saldo_awal, COALESCE(total_in,0) total_in, coalesce(pay_bank,0) pay_bank, coalesce(pay_non_bank,0) pay_non_bank, coalesce(pay_cash,0) pay_cash, no_coa, nama_coa, item_type1, item_type2, relasi, rate from saldo_in a LEFT JOIN saldo_out b on b.no_reff = a.no_payment),

mutasi as (select supplier, no_payment, tgl_payment, duedate, create_user, a.curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, ROUND(saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash),2) saldo_akhir, IFNULL(b.rate,1) rate, ROUND((saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash)) * IFNULL(b.rate,1),2) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from data_det a LEFT JOIN rate b on b.curr = a.curr)


select supplier, no_payment, tgl_payment, duedate, create_user, curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN saldo_akhir_idr
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END
    ) AS tot_produe from mutasi  $where )

        SELECT supplier,
    item_type2,
    relasi,

    ROUND(SUM(saldo_akhir), 2) AS saldo_akhir,

    ROUND(
        SUM(saldo_akhir) / SUM(SUM(saldo_akhir)) OVER () * 100
    , 2) AS saldo_akhir_persen,

    ROUND(SUM(due_current),2) due_current,
    ROUND(SUM(due_1_30),2) due_1_30,
    ROUND(SUM(due_31_60),2) due_31_60,
    ROUND(SUM(due_61_90),2) due_61_90,
    ROUND(SUM(due_91_120),2) due_91_120,
    ROUND(SUM(due_121_180),2) due_121_180,
    ROUND(SUM(due_181_360),2) due_181_360,
        ROUND(SUM(due_gt_360),2) due_gt_360,
    ROUND(SUM(total_due),2) total_due,
    ROUND(SUM(pro_due),2) pro_due,
    ROUND(SUM(pro_due0),2) pro_due0,
    ROUND(SUM(pro_due1),2) pro_due1,
    ROUND(SUM(pro_due2),2) pro_due2,
    ROUND(SUM(pro_due3),2) pro_due3,
    ROUND(SUM(pro_due4),2) pro_due4,
    ROUND(SUM(pro_due5),2) pro_due5,
    ROUND(SUM(tot_produe),2) tot_produe FROM (select supplier, item_type2, relasi, saldo_akhir_idr saldo_akhir, due_current, due_1_30, due_31_60, due_61_90, due_gt_360, due_91_120, due_121_180, due_181_360, total_due, pro_due2, pro_due, pro_due0, pro_due1, pro_due3, pro_due4, pro_due5, tot_produe from bpb
        UNION ALL
        select supplier, item_type2, relasi, saldo_akhir_idr saldo_akhir, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from kontrabon
        UNION ALL
        select supplier, item_type2, relasi, saldo_akhir_idr saldo_akhir, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from list_payment) a  GROUP BY supplier,item_type2,relasi order by supplier,item_type2 asc"); // isi query kamu

while($data = mysqli_fetch_assoc($sql)){
    $col = 1; // pakai index angka untuk konsisten
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $no++);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['supplier']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['item_type2']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['relasi']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir_persen'] . '%');
    $col++; // spacer

    // AGING
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['due_current']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['due_1_30']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['due_31_60']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['due_61_90']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['due_91_120']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['due_121_180']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['due_181_360']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['due_gt_360']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['total_due']);
    $col++; // spacer

    // PROJECTION
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due0']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due1']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due2']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due3']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due4']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due5']);
    $sheet6->setCellValueByColumnAndRow($col++, $rowNum, $data['tot_produe']);

    $rowNum++;
}


$highestColumn = Coordinate::stringFromColumnIndex($lastColIndex);

$sheet6->getStyle('E6:'.$highestColumn.$rowNum)
      ->getNumberFormat()->setFormatCode('#,##0.00');

$sheet6->getStyle('A4:'.$highestColumn.($rowNum-1))
      ->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]]);

for($c=1; $c<=$lastColIndex; $c++){
    $colLetter = Coordinate::stringFromColumnIndex($c);
    $sheet6->getColumnDimension($colLetter)->setAutoSize(true);
}

$sheet6->freezePaneByColumnAndRow(1, $rowHeader2 + 1);




/*
|---------------------------------------------------------------------------------------------------------------------------------------------------------
| SHEET 7 AP-Payable Card Statement - SUMMARY
|---------------------------------------------------------------------------------------------------------------------------------------------------------
*/

$sheet7 = $spreadsheet->createSheet();
$sheet7->setTitle('AP-Summary');


$sheet7->setCellValue('A1', 'Payable Card Statement - SUMMARY');
$sheet7->setCellValue('A2', 'PERIODE: '.$start_date_text.' - '.$end_date_text);
$sheet7->mergeCells('A1:X1');
$sheet7->mergeCells('A2:X2');
$sheet7->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);

$rowHeader1 = 4;
$rowHeader2 = 5;

$colIndex = 1;
$sheet7->setCellValueByColumnAndRow($colIndex, $rowHeader1, 'No');
$sheet7->mergeCellsByColumnAndRow($colIndex, $rowHeader1, $colIndex, $rowHeader2);
$colIndex++;

/* Supplier */
$sheet7->setCellValueByColumnAndRow($colIndex, $rowHeader1, 'Supplier Category & Name');
$sheet7->mergeCellsByColumnAndRow($colIndex, $rowHeader1, $colIndex, $rowHeader2);
$colIndex++;

/* ================= AMOUNT (COLSPAN 3) ================= */

$amountStartCol = $colIndex;

$sheet7->setCellValueByColumnAndRow($amountStartCol, $rowHeader1, 'Amount');
$sheet7->mergeCellsByColumnAndRow(
    $amountStartCol,
    $rowHeader1,
    $amountStartCol + 2,
    $rowHeader1
);

/* Sub Header Amount */
$amountHeaders = ['Currency','Foreign Currency','Equivalent IDR'];

foreach ($amountHeaders as $h) {
    $sheet7->setCellValueByColumnAndRow($colIndex, $rowHeader2, $h);
    $colIndex++;
}

$colIndex++;

$agingStartCol = $colIndex;
$agingHeaders = ['Current','1-30','31-60','61-90','91-120','121-180','181-360','>360','Total'];

$sheet7->setCellValueByColumnAndRow($agingStartCol, $rowHeader1, 'Account Payable Aging Based on Due Date');
$sheet7->mergeCellsByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) - 1, $rowHeader1);

$agingCol = $agingStartCol;
foreach ($agingHeaders as $h) {
    $sheet7->setCellValueByColumnAndRow($agingCol, $rowHeader2, $h);
    $agingCol++;
}

$colIndex = $agingCol + 1;

$projectionStartCol = $colIndex;

$sqlbulan = mysqli_query($conn1,"
SELECT CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)), LOWER(SUBSTR(nama_bulan_singkat,2)), ' ',tahun) bulan_tahun
FROM dim_date
WHERE kode_tanggal BETWEEN
CONCAT(YEAR('$end_date'),LPAD(MONTH('$end_date'),2,0),'01')
AND CONCAT(IF(MONTH('$end_date')+5 > 12,YEAR('$end_date')+1,YEAR('$end_date')),
LPAD(IF(MONTH('$end_date')+5 > 12, MOD(MONTH('$end_date')+5,12), MONTH('$end_date')+5),2,'0'),'01')
GROUP BY bulan,tahun
ORDER BY kode_tanggal ASC
");

$projectionHeaders = ['Due'];
while($rowbulan = mysqli_fetch_array($sqlbulan)){
    $projectionHeaders[] = $rowbulan['bulan_tahun'];
}
$projectionHeaders[] = 'Total';

$sheet7->setCellValueByColumnAndRow($projectionStartCol, $rowHeader1, 'Account Payable Based on Due Date Projection');
$sheet7->mergeCellsByColumnAndRow($projectionStartCol, $rowHeader1, $projectionStartCol + count($projectionHeaders) - 1, $rowHeader1);

$projectionCol = $projectionStartCol;
foreach ($projectionHeaders as $h) {
    $sheet7->setCellValueByColumnAndRow($projectionCol, $rowHeader2, $h);
    $projectionCol++;
}

$lastColIndex = $projectionCol - 1;

$rowSection= 6;

$sheet7->mergeCells("A{$rowSection}:E{$rowSection}");
$sheet7->mergeCells("G{$rowSection}:O{$rowSection}");
$sheet7->mergeCells("Q{$rowSection}:X{$rowSection}");

/* Isi Text */
$sheet7->setCellValue("A{$rowSection}", "GROUP");
$sheet7->getStyle("A{$rowSection}")
        ->getFont()->setBold(true);


$sheet7->getStyleByColumnAndRow(1, $rowHeader1, $lastColIndex, $rowHeader2)->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER
    ],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
]);

$sheet7->getStyleByColumnAndRow(1, $rowHeader1, count($headersFix), $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFE4E1');

$sheet7->getStyleByColumnAndRow($agingStartCol, $rowHeader1, $agingStartCol + count($agingHeaders) -1, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('98FB98');

$sheet7->getStyleByColumnAndRow($projectionStartCol, $rowHeader1, $lastColIndex, $rowHeader2)
      ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('87CEFA');


$rowNum = $rowHeader2 + 2;
$no = 1;

$sql = mysqli_query($conn2, "WITH
bpb as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

po_bpb as (select a.bpbno_int, b.supplier, a.pono, c.jml_pterms as top from bpb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier LEFT JOIN po_header c on c.pono = a.pono LEFT JOIN po_header_draft d on d.id = c.id_draft where a.bpbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int
UNION
select a.bppbno_int, b.supplier, '' pono, 0 top from bppb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.bppbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bppbno_int),

saldo_awal as (select supplier, no_bpb, tgl_bpb, 0 top, duedate, curr, total, rate, total_idr, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_bpb a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

trx_in as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_out_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_gm as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

trx_out_gm_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

saldo_in as (select supplier, no_bpb, tgl_bpb, top, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_bpb, tgl_bpb, top, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in_before
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in) a GROUP BY no_bpb, no_coa),

saldo_out_per_coa as (select no_journal, no_coa, sum(COALESCE(reverse_bpb_before,0)) reverse_bpb_before, sum(COALESCE(reverse_bpb,0)) reverse_bpb, sum(COALESCE(gm_before,0)) gm_before, sum(COALESCE(gm,0)) gm from (select no_journal, no_coa, 0 reverse_bpb_before, total reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse
UNION ALL
select no_journal, no_coa, total reverse_bpb_before, 0 reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse_before
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, 0 gm_before, total gm from trx_out_gm
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, total gm_before, 0 gm from trx_out_gm_before) a GROUP BY no_journal, no_coa),

saldo_out_per_bpb as (SELECT reff_doc, SUM(COALESCE(kontrabon_before,0)) kontrabon_before, SUM(COALESCE(kontrabon,0)) kontrabon, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(kontrabon_revisi_before,0)) kontrabon_revisi_before, SUM(COALESCE(kontrabon_revisi,0)) kontrabon_revisi, SUM(COALESCE(reverse_kontrabon_revisi_before,0)) reverse_kontrabon_revisi_before, SUM(COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon_revisi from (select reff_doc, 0 kontrabon_before, total kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb
UNION ALL
select reff_doc, total kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, total kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, total reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, total reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before) a GROUP BY reff_doc),

data_mutasi as (select a.supplier, a.no_bpb, a.tgl_bpb, a.duedate, a.curr, a.rate, a.no_coa, a.nama_coa, a.item_type1, a.item_type2, a.relasi, coalesce(a.saldo_awal,0) saldo_awal, coalesce(a.total_in,0) total_in, reverse_bpb_before, reverse_bpb, gm_before, gm, IF(kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_before, IF(kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon, IF(reverse_kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_before, IF(reverse_kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon, IF(kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi_before, IF(kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi, IF(reverse_kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi_before, IF(reverse_kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi from saldo_in a LEFT JOIN saldo_out_per_coa b on b.no_coa = a.no_coa and b.no_journal = a.no_bpb LEFT JOIN saldo_out_per_bpb c on c.reff_doc = a.no_bpb),

mutasi_det as (select supplier, no_bpb, tgl_bpb, duedate, curr, rate, (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0) - COALESCE(gm_before,0) - COALESCE(kontrabon_before,0) - COALESCE(kontrabon_revisi_before,0) + COALESCE(reverse_kontrabon_before,0) + COALESCE(reverse_kontrabon_revisi_before,0)) saldo_awal, COALESCE(total_in,0) in_bpb, COALESCE(reverse_bpb,0) reverse_bpb, (COALESCE(kontrabon,0) + COALESCE(kontrabon_revisi,0)) ded_kontrabon, (COALESCE(reverse_kontrabon,0) + COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon, COALESCE(gm,0) gm, no_coa, nama_coa, item_type1, item_type2, relasi from data_mutasi),

mutasi as (select supplier, no_bpb, tgl_bpb, duedate, a.curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir, IFNULL(b.rate,1) rate, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi_det a LEFT JOIN rate b on b.curr = a.curr),

laporan_mutasi as (SELECT supplier, no_bpb, tgl_bpb, duedate, curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir, rate, (saldo_akhir * rate) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN (saldo_akhir * rate) ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END
    ) AS tot_produe,
        CASE
            WHEN no_bpb LIKE '%RO%' 
              OR no_bpb LIKE '%OUT%' 
            THEN -1
            ELSE 1
        END AS f
        
    from mutasi)
        
        SELECT
    supplier,
    no_bpb,
    tgl_bpb,
    duedate,
    curr,

    saldo_awal * f AS saldo_awal,
    in_bpb * f AS in_bpb,
    reverse_bpb * f AS reverse_bpb,
    ded_kontrabon * f AS ded_kontrabon,
    reverse_kontrabon * f AS reverse_kontrabon,
    gm * f AS gm,
    saldo_akhir * f AS saldo_akhir,
    rate,
    saldo_akhir_idr * f AS saldo_akhir_idr,

    no_coa,
    nama_coa,
    item_type1,
    item_type2,
    relasi,

    due_current * f AS due_current,
    due_1_30 * f AS due_1_30,
    due_31_60 * f AS due_31_60,
    due_61_90 * f AS due_61_90,
    due_91_120 * f AS due_91_120,
    due_121_180 * f AS due_121_180,
    due_181_360 * f AS due_181_360,
    due_gt_360 * f AS due_gt_360,
    total_due * f AS total_due,

    pro_due * f AS pro_due,
    pro_due0 * f AS pro_due0,
    pro_due1 * f AS pro_due1,
    pro_due2 * f AS pro_due2,
    pro_due3 * f AS pro_due3,
    pro_due4 * f AS pro_due4,
    pro_due5 * f AS pro_due5,
    tot_produe * f AS tot_produe from laporan_mutasi  $where ),
        
        kontrabon as (
        WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_kbon, tgl_kbon, duedate, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_kontrabon a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

pph as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

ded_lp as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

ded_lp_before as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal between '$start_date' and '$end_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

saldo_in as (select supplier, no_kbon, tgl_kbon, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_kbon, tgl_kbon, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon_before
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon) a GROUP BY no_kbon),

Saldo_out as (select no_journal, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(uang_muka_before,0)) uang_muka_before, SUM(COALESCE(uang_muka,0)) uang_muka, SUM(COALESCE(pph_before,0)) pph_before, SUM(COALESCE(pph,0)) pph, SUM(COALESCE(potongan_before,0)) potongan_before, SUM(COALESCE(potongan,0)) potongan, SUM(COALESCE(ded_lp_before,0)) ded_lp_before, SUM(COALESCE(ded_lp,0)) ded_lp, SUM(COALESCE(ded_gm_before,0)) ded_gm_before, SUM(COALESCE(ded_gm,0)) ded_gm FROM (select no_journal, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, pph pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, total potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, total potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, total ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, total ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, total ded_gm_before, 0 ded_gm from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, total ded_gm from ded_gm_before) A GROUP BY no_journal),

data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_lp_before,2),0) ded_lp_before, COALESCE(round(ded_lp,2),0) ded_lp, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon),

mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + pph_before + potongan_before - (ded_lp_before + ded_gm_before)) saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + pph + uang_muka + potongan - (ded_lp - ded_gm)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in + pph + uang_muka + potongan - (ded_lp - ded_gm)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

select supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
WHEN duedate > '$end_date' THEN saldo_akhir_idr
ELSE 0
END AS due_current,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
THEN saldo_akhir_idr
ELSE 0
END AS due_1_30,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
THEN saldo_akhir_idr
ELSE 0
END AS due_31_60,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
THEN saldo_akhir_idr
ELSE 0
END AS due_61_90,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
THEN saldo_akhir_idr
ELSE 0
END AS due_91_120,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
THEN saldo_akhir_idr
ELSE 0
END AS due_121_180,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
THEN saldo_akhir_idr
ELSE 0
END AS due_181_360,

CASE
WHEN DATEDIFF('$end_date', duedate) > 360
THEN saldo_akhir_idr
ELSE 0
END AS due_gt_360,

(
CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
) AS total_due,

CASE
WHEN duedate <= '$end_date'
THEN saldo_akhir_idr
ELSE 0
END AS pro_due,

CASE
WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due0,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due1,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due2,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due3,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due4,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due5,

(
CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END
) AS tot_produe from report_mutasi  $where ),

list_payment as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_listpayment a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_lp as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_payment
),

in_lp_before as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_payment
),

lp as (
    SELECT no_payment, SUM(amount) total_payment, SUM(pph_value) total_pph FROM list_payment WHERE status != 'Cancel' GROUP BY no_payment
),

payment_all as (
    SELECT list_payment_id no_payment, payment_ftr_id no_pay, ttl_bayar total_bayar, create_date FROM payment_ftr WHERE status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
        UNION ALL
    SELECT a.no_reff, b.no_bankout, a.for_balance, b.create_date FROM b_bankout_det a JOIN b_bankout_h b ON a.no_bankout = b.no_bankout WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
    UNION ALL
    SELECT a.no_reff, b.no_pco, a.total, b.create_date FROM c_petty_cashout_det a JOIN c_petty_cashout_h b ON b.no_pco = a.no_pco WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
),

total_paid as (
    SELECT no_payment, SUM(total_bayar) as total_bayar_lp FROM payment_all GROUP BY no_payment
),

ranked_payment as (
    SELECT p.*, ROW_NUMBER() OVER (PARTITION BY no_payment ORDER BY create_date, no_pay) as rn, COUNT(*) OVER (PARTITION BY no_payment) as cnt FROM payment_all p
),

all_payment as (
SELECT
    r.no_payment,
    lp.total_payment,
    lp.total_pph,
    r.no_pay,
    r.total_bayar,

    CASE
        WHEN r.rn = 1 AND lp.total_pph > 0
        THEN 'Y'
        ELSE 'N'
    END AS pph_flag,

    CASE
        WHEN r.rn = 1
        THEN lp.total_pph
        ELSE 0
    END AS pph_value

FROM ranked_payment r
JOIN lp ON lp.no_payment = r.no_payment
ORDER BY r.no_payment
),

pay_non_bank as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY list_payment_id),

pay_non_bank_before as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY list_payment_id),

pay_bank as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date between '$start_date' and '$end_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_bank_before as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date < '$start_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_cash as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff),

pay_cash_before as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco < '$start_date' GROUP BY a.no_reff),

saldo_in as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp_before
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp) a GROUP BY no_payment),

saldo_out as (select no_reff, ROUND(SUM(COALESCE(pay_bank_before,0)),2) pay_bank_before, ROUND(SUM(COALESCE(pay_bank,0)),2) pay_bank, ROUND(SUM(COALESCE(pay_non_bank_before,0)),2) pay_non_bank_before, ROUND(SUM(COALESCE(pay_non_bank,0)),2) pay_non_bank, ROUND(SUM(COALESCE(pay_cash_before,0)),2) pay_cash_before, ROUND(SUM(COALESCE(pay_cash,0)),2) pay_cash, ROUND(SUM(COALESCE(pph_before,0) * -1),2) pph_before, ROUND(SUM(COALESCE(pph,0) * -1),2) pph FROM (
select no_reff, total pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank_before
UNION ALL
select no_reff, 0 pay_bank_before, total pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, ttl_bayar pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank_before
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, ttl_bayar pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, total pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_cash_before
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, total pay_cash, 0 pph_before, 0 pph from pay_cash
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, pph pph_before, 0 pph from in_lp_before
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, pph from in_lp
) a GROUP BY no_reff),

data_det as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, ROUND(COALESCE(saldo_awal,0) - COALESCE(pay_bank_before,0) - COALESCE(pay_non_bank_before,0) - COALESCE(pay_cash_before,0),2) saldo_awal, COALESCE(total_in,0) total_in, coalesce(pay_bank,0) pay_bank, coalesce(pay_non_bank,0) pay_non_bank, coalesce(pay_cash,0) pay_cash, no_coa, nama_coa, item_type1, item_type2, relasi, rate from saldo_in a LEFT JOIN saldo_out b on b.no_reff = a.no_payment),

mutasi as (select supplier, no_payment, tgl_payment, duedate, create_user, a.curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, ROUND(saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash),2) saldo_akhir, IFNULL(b.rate,1) rate, ROUND((saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash)) * IFNULL(b.rate,1),2) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from data_det a LEFT JOIN rate b on b.curr = a.curr)


select supplier, no_payment, tgl_payment, duedate, create_user, curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN saldo_akhir_idr
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END
    ) AS tot_produe from mutasi  $where )

        SELECT 
        supplier,
                curr,
        ROUND(SUM(saldo_akhir),2) saldo_akhir,
        ROUND(SUM(saldo_akhir_idr),2) saldo_akhir_idr,
                ROUND(SUM(due_current),2) due_current,
                ROUND(SUM(due_1_30),2) due_1_30,
                ROUND(SUM(due_31_60),2) due_31_60,
                ROUND(SUM(due_61_90),2) due_61_90,
                ROUND(SUM(due_91_120),2) due_91_120,
                ROUND(SUM(due_121_180),2) due_121_180,
                ROUND(SUM(due_181_360),2) due_181_360,
                ROUND(SUM(due_gt_360),2) due_gt_360,
                ROUND(SUM(total_due),2) total_due,
                ROUND(SUM(pro_due),2) pro_due,
                ROUND(SUM(pro_due0),2) pro_due0,
                ROUND(SUM(pro_due1),2) pro_due1,
                ROUND(SUM(pro_due2),2) pro_due2,
                ROUND(SUM(pro_due3),2) pro_due3,
                ROUND(SUM(pro_due4),2) pro_due4,
                ROUND(SUM(pro_due5),2) pro_due5,
                ROUND(SUM(tot_produe),2) tot_produe FROM (select supplier, curr, IF(CURR = 'usd', saldo_akhir, 0) saldo_akhir, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_gt_360, due_91_120, due_121_180, due_181_360, total_due, pro_due2, pro_due, pro_due0, pro_due1, pro_due3, pro_due4, pro_due5, tot_produe from bpb where relasi = 'GROUP'
        UNION ALL
        select supplier, curr, IF(CURR = 'usd', saldo_akhir, 0) saldo_akhir, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from kontrabon where relasi = 'GROUP'
        UNION ALL
        select supplier, curr, IF(CURR = 'usd', saldo_akhir, 0) saldo_akhir, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from list_payment where relasi = 'GROUP') a  GROUP BY supplier,curr order by supplier,curr asc"); 

while($data = mysqli_fetch_assoc($sql)){
    $col = 1; // pakai index angka untuk konsisten
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $no++);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['supplier']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['curr']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir_idr']);
    $col++; // spacer

    // AGING
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_current']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_1_30']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_31_60']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_61_90']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_91_120']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_121_180']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_181_360']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_gt_360']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['total_due']);
    $col++; // spacer

    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due0']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due1']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due2']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due3']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due4']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due5']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['tot_produe']);

    $rowNum++;
}


$dataStartRow = $rowHeader2 + 2;
$dataEndRow   = $rowNum - 1;

if ($dataEndRow >= $dataStartRow) {

    $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex);

    /* ===============================
       TOTAL IDR
    ===============================*/
    $totalIDRRow = $rowNum;
    $sheet7->mergeCells("A{$totalIDRRow}:C{$totalIDRRow}");
    $sheet7->setCellValue("A{$totalIDRRow}", "Total IDR");
    $sheet7->getStyle("A{$totalIDRRow}")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

    // Saldo
    $sheet7->setCellValue("D{$totalIDRRow}",
        "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"IDR\",D{$dataStartRow}:D{$dataEndRow})");

    $sheet7->setCellValue("E{$totalIDRRow}",
        "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"IDR\",E{$dataStartRow}:E{$dataEndRow})");

    // AGING (SUMIF IDR)
    for ($i = 0; $i < count($agingHeaders); $i++) {

        $colIndex  = $agingStartCol + $i;
        $colLetter = Coordinate::stringFromColumnIndex($colIndex);

        $sheet7->setCellValue(
            $colLetter.$totalIDRRow,
            "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"IDR\",{$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})"
        );
    }

    // PROJECTION (SUMIF IDR)
    for ($col = $projectionStartCol; $col <= $lastColIndex; $col++) {

        $colLetter = Coordinate::stringFromColumnIndex($col);

        $sheet7->setCellValue(
            $colLetter.$totalIDRRow,
            "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"IDR\",{$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})"
        );
    }


    /* ===============================
       TOTAL USD
    ===============================*/
    $totalUSDRow = $totalIDRRow + 1;
    $sheet7->mergeCells("A{$totalUSDRow}:C{$totalUSDRow}");
    $sheet7->setCellValue("A{$totalUSDRow}", "Total USD");
    $sheet7->getStyle("A{$totalUSDRow}")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

    $sheet7->setCellValue("D{$totalUSDRow}",
        "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"USD\",D{$dataStartRow}:D{$dataEndRow})");

    $sheet7->setCellValue("E{$totalUSDRow}",
        "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"USD\",E{$dataStartRow}:E{$dataEndRow})");

    // AGING (SUMIF USD)
    for ($i = 0; $i < count($agingHeaders); $i++) {

        $colIndex  = $agingStartCol + $i;
        $colLetter = Coordinate::stringFromColumnIndex($colIndex);

        $sheet7->setCellValue(
            $colLetter.$totalUSDRow,
            "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"USD\",{$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})"
        );
    }

    // PROJECTION (SUMIF USD)
    for ($col = $projectionStartCol; $col <= $lastColIndex; $col++) {

        $colLetter = Coordinate::stringFromColumnIndex($col);

        $sheet7->setCellValue(
            $colLetter.$totalUSDRow,
            "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"USD\",{$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})"
        );
    }


    /* ===============================
       SUMMARY GROUP (GRAND TOTAL)
    ===============================*/
    $summaryRow_GROUP = $totalUSDRow + 1;
    $sheet7->mergeCells("A{$summaryRow_GROUP}:D{$summaryRow_GROUP}");
    $sheet7->setCellValue("A{$summaryRow_GROUP}", "Summary Group");
    $sheet7->getStyle("A{$summaryRow_GROUP}")
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
    ->setVertical(Alignment::VERTICAL_CENTER);

    // Saldo Total

    $sheet7->setCellValue("E{$summaryRow_GROUP}",
        "=SUM(E{$dataStartRow}:E{$dataEndRow})");

    // AGING TOTAL
    for ($i = 0; $i < count($agingHeaders); $i++) {

        $colIndex  = $agingStartCol + $i;
        $colLetter = Coordinate::stringFromColumnIndex($colIndex);

        $sheet7->setCellValue(
            $colLetter.$summaryRow_GROUP,
            "=SUM({$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})"
        );
    }

    // PROJECTION TOTAL
    for ($col = $projectionStartCol; $col <= $lastColIndex; $col++) {

        $colLetter = Coordinate::stringFromColumnIndex($col);

        $sheet7->setCellValue(
            $colLetter.$summaryRow_GROUP,
            "=SUM({$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})"
        );
    }


    /* ===============================
       STYLING
    ===============================*/

    $sheet7->getStyle("A{$totalIDRRow}:{$lastColLetter}{$summaryRow_GROUP}")
        ->getFont()->setBold(true);

    $sheet7->getStyle("A{$totalIDRRow}:{$lastColLetter}{$summaryRow_GROUP}")
        ->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

    $sheet7->getStyle("A{$summaryRow_GROUP}:{$lastColLetter}{$summaryRow_GROUP}")
        ->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB('EAEAEA');

    $rowNum = $summaryRow_GROUP + 1;
}



/* ============================================================
   NON GROUP SECTION
============================================================ */

$rowSection = $rowNum + 1;

$sheet7->mergeCells("A{$rowSection}:E{$rowSection}");
$sheet7->mergeCells("G{$rowSection}:O{$rowSection}");
$sheet7->mergeCells("Q{$rowSection}:X{$rowSection}");

$sheet7->setCellValue("A{$rowSection}", "NON GROUP");
$sheet7->getStyle("A{$rowSection}")->getFont()->setBold(true);

$rowNum = $rowSection + 1;

$no = 1;

$sql = mysqli_query($conn2, "WITH
bpb as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

po_bpb as (select a.bpbno_int, b.supplier, a.pono, c.jml_pterms as top from bpb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier LEFT JOIN po_header c on c.pono = a.pono LEFT JOIN po_header_draft d on d.id = c.id_draft where a.bpbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bpbno_int
UNION
select a.bppbno_int, b.supplier, '' pono, 0 top from bppb a INNER JOIN mastersupplier b on b.Id_Supplier = a.id_supplier where a.bppbdate > '2025-12-31' and confirm = 'Y' and cancel = 'N' GROUP BY bppbno_int),

saldo_awal as (select supplier, no_bpb, tgl_bpb, 0 top, duedate, curr, total, rate, total_idr, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_bpb a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

trx_in as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'AP - BPB RETURN',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'AP - BPB RETURN',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal a where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - BPB','AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_in_reverse_before as (select supplier, no_journal, tgl_journal, top, due_date, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(type_journal = 'Reverse AP - BPB',sum(debit),sum(credit)) total, a.rate, IF(type_journal = 'Reverse AP - BPB',sum(debit * rate),sum(credit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - BPB','Reverse AP - BPB RETURN') and nama_coa like '%GR/IR%') a INNER JOIN po_bpb b on b.bpbno_int = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY no_journal, no_coa) a where total != 0
),

trx_out_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal not like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * a.rate),sum(debit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_reverse_kb_revisi_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, a.curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit),sum(credit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(debit * a.rate),sum(credit * a.rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select *  from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('Reverse AP - Kontrabon') and nama_coa like '%GR/IR%' and no_journal like '%-REV_%') a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN kontrabon_h d on d.no_kbon = a.no_journal where d.status != 'Cancel' GROUP BY reff_doc) a where total != 0
),

trx_out_gm as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

trx_out_gm_before as (select reff_doc, no_journal, tgl_journal, curr, total, rate, total_idr, no_coa, nama_coa, item_type1, item_type2, relasi from (select reff_doc, no_journal, tgl_journal, COALESCE(top,0) top, DATE_ADD(tgl_journal, INTERVAL COALESCE(top,0) DAY) as due_date, curr, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit),sum(debit)) total, a.rate, IF(reff_doc like '%/RO/%' OR reff_doc like '%/OUT/%',sum(credit * rate),sum(debit * rate)) total_idr, a.no_coa, a.nama_coa, c.item_type1,c.item_type2,c.relasi from (select no_journal, tgl_journal, type_journal, no_coa, nama_coa, reff_doc, curr, rate, debit, credit from tbl_list_journal where tgl_journal < '$start_date' and tgl_journal > '2025-12-31' and type_journal IN ('ACCOUNT PAYABLE') and nama_coa like '%GR/IR%') a LEFT JOIN po_bpb b on b.bpbno_int = a.reff_doc INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa GROUP BY reff_doc, no_coa) a where total != 0),

saldo_in as (select supplier, no_bpb, tgl_bpb, top, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_bpb, tgl_bpb, top, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in_before
UNION ALL
select supplier, no_journal, tgl_journal, top, due_date, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from trx_in) a GROUP BY no_bpb, no_coa),

saldo_out_per_coa as (select no_journal, no_coa, sum(COALESCE(reverse_bpb_before,0)) reverse_bpb_before, sum(COALESCE(reverse_bpb,0)) reverse_bpb, sum(COALESCE(gm_before,0)) gm_before, sum(COALESCE(gm,0)) gm from (select no_journal, no_coa, 0 reverse_bpb_before, total reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse
UNION ALL
select no_journal, no_coa, total reverse_bpb_before, 0 reverse_bpb, 0 gm_before, 0 gm from trx_in_reverse_before
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, 0 gm_before, total gm from trx_out_gm
UNION ALL
select reff_doc, no_coa, 0 reverse_bpb_before, 0 reverse_bpb, total gm_before, 0 gm from trx_out_gm_before) a GROUP BY no_journal, no_coa),

saldo_out_per_bpb as (SELECT reff_doc, SUM(COALESCE(kontrabon_before,0)) kontrabon_before, SUM(COALESCE(kontrabon,0)) kontrabon, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(kontrabon_revisi_before,0)) kontrabon_revisi_before, SUM(COALESCE(kontrabon_revisi,0)) kontrabon_revisi, SUM(COALESCE(reverse_kontrabon_revisi_before,0)) reverse_kontrabon_revisi_before, SUM(COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon_revisi from (select reff_doc, 0 kontrabon_before, total kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb
UNION ALL
select reff_doc, total kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, total kontrabon_revisi, 0 reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_kb_revisi
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, total reverse_kontrabon_revisi_before, 0 reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before
UNION ALL
select reff_doc, 0 kontrabon_before, 0 kontrabon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 kontrabon_revisi_before, 0 kontrabon_revisi, 0 reverse_kontrabon_revisi_before, total reverse_kontrabon_revisi from trx_out_reverse_kb_revisi_before) a GROUP BY reff_doc),

data_mutasi as (select a.supplier, a.no_bpb, a.tgl_bpb, a.duedate, a.curr, a.rate, a.no_coa, a.nama_coa, a.item_type1, a.item_type2, a.relasi, coalesce(a.saldo_awal,0) saldo_awal, coalesce(a.total_in,0) total_in, reverse_bpb_before, reverse_bpb, gm_before, gm, IF(kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_before, IF(kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon, IF(reverse_kontrabon_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_before, IF(reverse_kontrabon > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon, IF(kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi_before, IF(kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) kontrabon_revisi, IF(reverse_kontrabon_revisi_before > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi_before, IF(reverse_kontrabon_revisi > 0,if(COALESCE(total_in,0) > 0, (COALESCE(total_in,0) - COALESCE(reverse_bpb,0)), (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0))),0) reverse_kontrabon_revisi from saldo_in a LEFT JOIN saldo_out_per_coa b on b.no_coa = a.no_coa and b.no_journal = a.no_bpb LEFT JOIN saldo_out_per_bpb c on c.reff_doc = a.no_bpb),

mutasi_det as (select supplier, no_bpb, tgl_bpb, duedate, curr, rate, (COALESCE(saldo_awal,0) - COALESCE(reverse_bpb_before,0) - COALESCE(gm_before,0) - COALESCE(kontrabon_before,0) - COALESCE(kontrabon_revisi_before,0) + COALESCE(reverse_kontrabon_before,0) + COALESCE(reverse_kontrabon_revisi_before,0)) saldo_awal, COALESCE(total_in,0) in_bpb, COALESCE(reverse_bpb,0) reverse_bpb, (COALESCE(kontrabon,0) + COALESCE(kontrabon_revisi,0)) ded_kontrabon, (COALESCE(reverse_kontrabon,0) + COALESCE(reverse_kontrabon_revisi,0)) reverse_kontrabon, COALESCE(gm,0) gm, no_coa, nama_coa, item_type1, item_type2, relasi from data_mutasi),

mutasi as (select supplier, no_bpb, tgl_bpb, duedate, a.curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, (saldo_awal + in_bpb - reverse_bpb - ded_kontrabon + reverse_kontrabon - gm) saldo_akhir, IFNULL(b.rate,1) rate, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi_det a LEFT JOIN rate b on b.curr = a.curr),

laporan_mutasi as (SELECT supplier, no_bpb, tgl_bpb, duedate, curr, saldo_awal, in_bpb, reverse_bpb, ded_kontrabon, reverse_kontrabon, gm, saldo_akhir, rate, (saldo_akhir * rate) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN (saldo_akhir * rate) ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN (saldo_akhir * rate)
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN (saldo_akhir * rate) ELSE 0 END
    ) AS tot_produe,
        CASE
            WHEN no_bpb LIKE '%RO%' 
              OR no_bpb LIKE '%OUT%' 
            THEN -1
            ELSE 1
        END AS f
        
    from mutasi)
        
        SELECT
    supplier,
    no_bpb,
    tgl_bpb,
    duedate,
    curr,

    saldo_awal * f AS saldo_awal,
    in_bpb * f AS in_bpb,
    reverse_bpb * f AS reverse_bpb,
    ded_kontrabon * f AS ded_kontrabon,
    reverse_kontrabon * f AS reverse_kontrabon,
    gm * f AS gm,
    saldo_akhir * f AS saldo_akhir,
    rate,
    saldo_akhir_idr * f AS saldo_akhir_idr,

    no_coa,
    nama_coa,
    item_type1,
    item_type2,
    relasi,

    due_current * f AS due_current,
    due_1_30 * f AS due_1_30,
    due_31_60 * f AS due_31_60,
    due_61_90 * f AS due_61_90,
    due_91_120 * f AS due_91_120,
    due_121_180 * f AS due_121_180,
    due_181_360 * f AS due_181_360,
    due_gt_360 * f AS due_gt_360,
    total_due * f AS total_due,

    pro_due * f AS pro_due,
    pro_due0 * f AS pro_due0,
    pro_due1 * f AS pro_due1,
    pro_due2 * f AS pro_due2,
    pro_due3 * f AS pro_due3,
    pro_due4 * f AS pro_due4,
    pro_due5 * f AS pro_due5,
    tot_produe * f AS tot_produe from laporan_mutasi  $where ),
        
        kontrabon as (
        WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_kbon, tgl_kbon, duedate, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_kontrabon a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_kontrabon as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

in_kontrabon_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate, d.no_coa, d.nama_coa, item_type1, item_type2, relasi from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa INNER JOIN (select no_kbon, no_coa, nama_coa from kontrabon_h where status != 'cancel' and no_coa is not null GROUP BY no_kbon) d on d.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%GR/IR%' and b.status != 'Cancel' GROUP BY no_journal
),

reverse_kontrabon as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

reverse_kontrabon_before as (select a.no_journal, sum(credit - debit) total from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'Reverse AP - Kontrabon' and a.nama_coa like '%UTANG USAHA%' and b.status != 'Cancel' GROUP BY no_journal),

uang_muka as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

uang_muka_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%UANG MUKA%' and b.status != 'Cancel' GROUP BY no_journal
),

pph as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

pph_before as (select no_kbon, 0 pph from kontrabon_h where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

potongan as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal BETWEEN '$start_date' and '$end_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

potongan_before as (select b.nama_supp, a.no_journal, a.tgl_journal, tgl_tempo, a.curr, sum(debit - credit) total, a.rate from tbl_list_journal a INNER JOIN kontrabon_h b on b.no_kbon = a.no_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and type_journal = 'AP - Kontrabon' and a.nama_coa like '%BEBAN%' and b.status != 'Cancel' GROUP BY no_journal),

ded_lp as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' and status != 'Cancel' GROUP BY no_kbon),

ded_lp_before as (select no_kbon, sum(amount + pph_value) total from list_payment where DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' and status != 'Cancel' GROUP BY no_kbon),

ded_gm as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal between '$start_date' and '$end_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

ded_gm_before as (select reff_doc, sum(debit - credit) total from tbl_list_journal where tgl_journal > '2025-12-31' and tgl_journal < '$start_date' and reff_doc like '%SI/APR%' and nama_coa like '%UTANG USAHA%' and type_journal = 'ACCOUNT PAYABLE' GROUP BY reff_doc),

saldo_in as (select supplier, no_kbon, tgl_kbon, duedate, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_kbon, tgl_kbon, duedate, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon_before
UNION ALL
select nama_supp, no_journal, tgl_journal, tgl_tempo, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_kontrabon) a GROUP BY no_kbon),

Saldo_out as (select no_journal, SUM(COALESCE(reverse_kontrabon_before,0)) reverse_kontrabon_before, SUM(COALESCE(reverse_kontrabon,0)) reverse_kontrabon, SUM(COALESCE(uang_muka_before,0)) uang_muka_before, SUM(COALESCE(uang_muka,0)) uang_muka, SUM(COALESCE(pph_before,0)) pph_before, SUM(COALESCE(pph,0)) pph, SUM(COALESCE(potongan_before,0)) potongan_before, SUM(COALESCE(potongan,0)) potongan, SUM(COALESCE(ded_lp_before,0)) ded_lp_before, SUM(COALESCE(ded_lp,0)) ded_lp, SUM(COALESCE(ded_gm_before,0)) ded_gm_before, SUM(COALESCE(ded_gm,0)) ded_gm FROM (select no_journal, total reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, total reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from reverse_kontrabon
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, total uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, total uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from uang_muka
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, pph pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from pph
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, total potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan_before
UNION ALL
select no_journal, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, total potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from potongan
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, total ded_lp_before, 0 ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp_before
UNION ALL
select no_kbon, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, total ded_lp, 0 ded_gm_before, 0 ded_gm from ded_lp
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, total ded_gm_before, 0 ded_gm from ded_gm_before
UNION ALL
select reff_doc, 0 reverse_kontrabon_before, 0 reverse_kontrabon, 0 uang_muka_before, 0 uang_muka, 0 pph_before, 0 pph, 0 potongan_before, 0 potongan, 0 ded_lp_before, 0 ded_lp, 0 ded_gm_before, total ded_gm from ded_gm_before) A GROUP BY no_journal),

data_detail as (select supplier, no_kbon, tgl_kbon, duedate, curr, COALESCE(round(saldo_awal,2),0) saldo_awal, COALESCE(round(total_in,2),0) total_in, COALESCE(round(rate,2),1) rate, no_coa, nama_coa, item_type1, item_type2, relasi, COALESCE(round(reverse_kontrabon_before,2),0) reverse_kontrabon_before, COALESCE(round(reverse_kontrabon,2),0) reverse_kontrabon, COALESCE(round(uang_muka_before,2),0) uang_muka_before, COALESCE(round(uang_muka,2),0) uang_muka, COALESCE(round(pph_before,2),0) pph_before, COALESCE(round(pph,2),0) pph, COALESCE(round(potongan_before,2),0) potongan_before, COALESCE(round(potongan,2),0) potongan, COALESCE(round(ded_lp_before,2),0) ded_lp_before, COALESCE(round(ded_lp,2),0) ded_lp, COALESCE(round(ded_gm_before,2),0) ded_gm_before, COALESCE(round(ded_gm,2),0) ded_gm from saldo_in a LEFT JOIN saldo_out b on b.no_journal = a.no_kbon),

mutasi as (select supplier, no_kbon, tgl_kbon, duedate, curr, (saldo_awal + reverse_kontrabon_before + uang_muka_before + pph_before + potongan_before - (ded_lp_before + ded_gm_before)) saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, rate, no_coa, nama_coa, item_type1, item_type2, relasi from data_detail),

report_mutasi as (select supplier, no_kbon, tgl_kbon, duedate, a.curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, (saldo_awal + total_in + reverse_kontrabon + pph + uang_muka + potongan - (ded_lp - ded_gm)) saldo_akhir, IFNULL(b.rate,1) rate, ((saldo_awal + total_in + pph + uang_muka + potongan - (ded_lp - ded_gm)) * IFNULL(b.rate,1)) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from mutasi a LEFT JOIN rate b on b.curr = a.curr)

select supplier, no_kbon, tgl_kbon, duedate, curr, saldo_awal, total_in, pph, uang_muka, potongan, ded_lp, ded_gm, reverse_kontrabon, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
WHEN duedate > '$end_date' THEN saldo_akhir_idr
ELSE 0
END AS due_current,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
THEN saldo_akhir_idr
ELSE 0
END AS due_1_30,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
THEN saldo_akhir_idr
ELSE 0
END AS due_31_60,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
THEN saldo_akhir_idr
ELSE 0
END AS due_61_90,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
THEN saldo_akhir_idr
ELSE 0
END AS due_91_120,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
THEN saldo_akhir_idr
ELSE 0
END AS due_121_180,

CASE
WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
THEN saldo_akhir_idr
ELSE 0
END AS due_181_360,

CASE
WHEN DATEDIFF('$end_date', duedate) > 360
THEN saldo_akhir_idr
ELSE 0
END AS due_gt_360,

(
CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
) AS total_due,

CASE
WHEN duedate <= '$end_date'
THEN saldo_akhir_idr
ELSE 0
END AS pro_due,

CASE
WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due0,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due1,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due2,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due3,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due4,

CASE
WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr
ELSE 0
END AS pro_due5,

(
CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate > '$end_date'
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END +
CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
THEN saldo_akhir_idr ELSE 0 END
) AS tot_produe from report_mutasi  $where ),

list_payment as (
WITH
rate as (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate),

saldo_awal as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total, rate, a.no_coa, nama_coa, item_type1, item_type2, relasi from ap_saldo_awal_listpayment a INNER JOIN mastercoa_v2 c on c.no_coa = a.no_coa),

in_lp as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY no_payment
),

in_lp_before as (select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, a.curr, sum(amount + pph_value) total, pph_value pph, IFNULL(rate,1) rate, a.no_coa, a.nama_coa, item_type1, item_type2, relasi from list_payment a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = DATE_FORMAT(a.create_date, '%Y-%m-%d') where a.status != 'Cancel' and DATE_FORMAT(a.create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(a.create_date, '%Y-%m-%d') < '$start_date' GROUP BY no_payment
),

lp as (
    SELECT no_payment, SUM(amount) total_payment, SUM(pph_value) total_pph FROM list_payment WHERE status != 'Cancel' GROUP BY no_payment
),

payment_all as (
    SELECT list_payment_id no_payment, payment_ftr_id no_pay, ttl_bayar total_bayar, create_date FROM payment_ftr WHERE status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
        UNION ALL
    SELECT a.no_reff, b.no_bankout, a.for_balance, b.create_date FROM b_bankout_det a JOIN b_bankout_h b ON a.no_bankout = b.no_bankout WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
    UNION ALL
    SELECT a.no_reff, b.no_pco, a.total, b.create_date FROM c_petty_cashout_det a JOIN c_petty_cashout_h b ON b.no_pco = a.no_pco WHERE b.status != 'Cancel' AND a.no_reff LIKE '%LP/%' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31'
),

total_paid as (
    SELECT no_payment, SUM(total_bayar) as total_bayar_lp FROM payment_all GROUP BY no_payment
),

ranked_payment as (
    SELECT p.*, ROW_NUMBER() OVER (PARTITION BY no_payment ORDER BY create_date, no_pay) as rn, COUNT(*) OVER (PARTITION BY no_payment) as cnt FROM payment_all p
),

all_payment as (
SELECT
    r.no_payment,
    lp.total_payment,
    lp.total_pph,
    r.no_pay,
    r.total_bayar,

    CASE
        WHEN r.rn = 1 AND lp.total_pph > 0
        THEN 'Y'
        ELSE 'N'
    END AS pph_flag,

    CASE
        WHEN r.rn = 1
        THEN lp.total_pph
        ELSE 0
    END AS pph_value

FROM ranked_payment r
JOIN lp ON lp.no_payment = r.no_payment
ORDER BY r.no_payment
),

pay_non_bank as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') BETWEEN '$start_date' and '$end_date' GROUP BY list_payment_id),

pay_non_bank_before as (select list_payment_id, SUM(ttl_bayar + coalesce(pph_value,0)) ttl_bayar from payment_ftr a left join all_payment b on b.no_payment = a.list_payment_id and b.no_pay = a.payment_ftr_id where status != 'Cancel' and DATE_FORMAT(create_date, '%Y-%m-%d') > '2025-12-31' and DATE_FORMAT(create_date, '%Y-%m-%d') < '$start_date' GROUP BY list_payment_id),

pay_bank as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date between '$start_date' and '$end_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_bank_before as (select no_reff, sum(for_balance + coalesce(pph_value,0)) total from b_bankout_det a INNER JOIN b_bankout_h b on a.no_bankout = b.no_bankout left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_bankout where b.bankout_date > '2025-12-31' and b.bankout_date < '$start_date' and no_reff like '%LP/%' GROUP BY no_reff),

pay_cash as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco between '$start_date' and '$end_date' GROUP BY a.no_reff),

pay_cash_before as (select a.no_reff,sum(a.total + coalesce(pph_value,0)) total from c_petty_cashout_det a inner join c_petty_cashout_h b on b.no_pco = a.no_pco left join all_payment c on c.no_payment = a.no_reff and c.no_pay = b.no_pco where b.status != 'Cancel' and a.no_reff like '%LP/%' and a.tgl_pco > '2025-12-31' and a.tgl_pco < '$start_date' GROUP BY a.no_reff),

saldo_in as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, sum(COALESCE(saldo_awal,0)) saldo_awal, sum(COALESCE(total_in,0)) total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from (select supplier, no_payment, tgl_payment, duedate, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from saldo_awal
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, total saldo_awal, 0 total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp_before
UNION ALL
select nama_supp, no_payment, tgl_payment, tgl_tempo, create_user, curr, 0 saldo_awal, total total_in, rate, no_coa, nama_coa, item_type1, item_type2, relasi from in_lp) a GROUP BY no_payment),

saldo_out as (select no_reff, ROUND(SUM(COALESCE(pay_bank_before,0)),2) pay_bank_before, ROUND(SUM(COALESCE(pay_bank,0)),2) pay_bank, ROUND(SUM(COALESCE(pay_non_bank_before,0)),2) pay_non_bank_before, ROUND(SUM(COALESCE(pay_non_bank,0)),2) pay_non_bank, ROUND(SUM(COALESCE(pay_cash_before,0)),2) pay_cash_before, ROUND(SUM(COALESCE(pay_cash,0)),2) pay_cash, ROUND(SUM(COALESCE(pph_before,0) * -1),2) pph_before, ROUND(SUM(COALESCE(pph,0) * -1),2) pph FROM (
select no_reff, total pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank_before
UNION ALL
select no_reff, 0 pay_bank_before, total pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_bank
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, ttl_bayar pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank_before
UNION ALL
select list_payment_id, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, ttl_bayar pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_non_bank
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, total pay_cash_before, 0 pay_cash, 0 pph_before, 0 pph from pay_cash_before
UNION ALL
select no_reff, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, total pay_cash, 0 pph_before, 0 pph from pay_cash
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, pph pph_before, 0 pph from in_lp_before
UNION ALL
select no_payment, 0 pay_bank_before, 0 pay_bank, 0 pay_non_bank_before, 0 pay_non_bank, 0 pay_cash_before, 0 pay_cash, 0 pph_before, pph from in_lp
) a GROUP BY no_reff),

data_det as (select supplier, no_payment, tgl_payment, duedate, create_user, curr, ROUND(COALESCE(saldo_awal,0) - COALESCE(pay_bank_before,0) - COALESCE(pay_non_bank_before,0) - COALESCE(pay_cash_before,0),2) saldo_awal, COALESCE(total_in,0) total_in, coalesce(pay_bank,0) pay_bank, coalesce(pay_non_bank,0) pay_non_bank, coalesce(pay_cash,0) pay_cash, no_coa, nama_coa, item_type1, item_type2, relasi, rate from saldo_in a LEFT JOIN saldo_out b on b.no_reff = a.no_payment),

mutasi as (select supplier, no_payment, tgl_payment, duedate, create_user, a.curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, ROUND(saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash),2) saldo_akhir, IFNULL(b.rate,1) rate, ROUND((saldo_awal + total_in - (pay_bank + pay_non_bank + pay_cash)) * IFNULL(b.rate,1),2) saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi from data_det a LEFT JOIN rate b on b.curr = a.curr)


select supplier, no_payment, tgl_payment, duedate, create_user, curr, saldo_awal, total_in, pay_bank, pay_non_bank, pay_cash, saldo_akhir, rate, saldo_akhir_idr, no_coa, nama_coa, item_type1, item_type2, relasi,
CASE
        WHEN duedate > '$end_date' THEN saldo_akhir_idr
        ELSE 0
    END AS due_current,
        
        CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_1_30,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_31_60,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_61_90,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_91_120,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_121_180,

    CASE
        WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_181_360,

    CASE
        WHEN DATEDIFF('$end_date', duedate) > 360
        THEN saldo_akhir_idr
        ELSE 0
    END AS due_gt_360,

    (
        CASE WHEN duedate > '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 0 AND 30 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 31 AND 60 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 61 AND 90 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 91 AND 120 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 121 AND 180 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) BETWEEN 181 AND 360 THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN DATEDIFF('$end_date', duedate) > 360 THEN saldo_akhir_idr ELSE 0 END
    ) AS total_due,
        
        CASE
        WHEN duedate <= '$end_date'
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due,

    CASE
        WHEN duedate > '$end_date'
         AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due0,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due1,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due2,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due3,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
         AND duedate <  DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due4,

    CASE
        WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
        THEN saldo_akhir_idr
        ELSE 0
    END AS pro_due5,

    (
        CASE WHEN duedate <= '$end_date' THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate > '$end_date'
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 1 DAY)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 2 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 3 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 4 MONTH)
              AND duedate < DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END +
        CASE WHEN duedate >= DATE_ADD(LAST_DAY('$end_date'), INTERVAL 5 MONTH)
             THEN saldo_akhir_idr ELSE 0 END
    ) AS tot_produe from mutasi  $where )

        SELECT 
        item_type2,
                curr,
        ROUND(SUM(saldo_akhir),2) saldo_akhir,
        ROUND(SUM(saldo_akhir_idr),2) saldo_akhir_idr,
                ROUND(SUM(due_current),2) due_current,
                ROUND(SUM(due_1_30),2) due_1_30,
                ROUND(SUM(due_31_60),2) due_31_60,
                ROUND(SUM(due_61_90),2) due_61_90,
                ROUND(SUM(due_91_120),2) due_91_120,
                ROUND(SUM(due_121_180),2) due_121_180,
                ROUND(SUM(due_181_360),2) due_181_360,
                ROUND(SUM(due_gt_360),2) due_gt_360,
                ROUND(SUM(total_due),2) total_due,
                ROUND(SUM(pro_due),2) pro_due,
                ROUND(SUM(pro_due0),2) pro_due0,
                ROUND(SUM(pro_due1),2) pro_due1,
                ROUND(SUM(pro_due2),2) pro_due2,
                ROUND(SUM(pro_due3),2) pro_due3,
                ROUND(SUM(pro_due4),2) pro_due4,
                ROUND(SUM(pro_due5),2) pro_due5,
                ROUND(SUM(tot_produe),2) tot_produe FROM (select item_type2, curr, IF(CURR = 'usd', saldo_akhir, 0) saldo_akhir, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_gt_360, due_91_120, due_121_180, due_181_360, total_due, pro_due2, pro_due, pro_due0, pro_due1, pro_due3, pro_due4, pro_due5, tot_produe from bpb where relasi = 'NON GROUP'
        UNION ALL
        select item_type2, curr, IF(CURR = 'usd', saldo_akhir, 0) saldo_akhir, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from kontrabon where relasi = 'NON GROUP'
        UNION ALL
        select item_type2, curr, IF(CURR = 'usd', saldo_akhir, 0) saldo_akhir, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe from list_payment where relasi = 'NON GROUP') a  GROUP BY item_type2,curr order by item_type2,curr asc");

$dataStartRow = $rowNum;

while($data = mysqli_fetch_assoc($sql)){

    $col = 1;

    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $no++);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['item_type2']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['curr']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['saldo_akhir_idr']);
    $col++;

    /* AGING */
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_current']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_1_30']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_31_60']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_61_90']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_91_120']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_121_180']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_181_360']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['due_gt_360']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['total_due']);
    $col++;

    /* PROJECTION */
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due0']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due1']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due2']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due3']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due4']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['pro_due5']);
    $sheet7->setCellValueByColumnAndRow($col++, $rowNum, $data['tot_produe']);

    $rowNum++;
}

$dataEndRow     = $rowNum - 1;
$lastColLetter  = Coordinate::stringFromColumnIndex($lastColIndex);

/* KOLOM YANG HARUS DIKOSONGKAN */
$skipCols = [
    Coordinate::columnIndexFromString('F'),
    Coordinate::columnIndexFromString('P'),
];

if ($dataEndRow >= $dataStartRow) {

    /* BORDER DATA */
    $sheet7->getStyle("A{$dataStartRow}:{$lastColLetter}{$dataEndRow}")
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

    /* ================= TOTAL IDR NON GROUP ================= */
    $totalIDRRow_NON = $rowNum;

    $sheet7->mergeCells("A{$totalIDRRow_NON}:C{$totalIDRRow_NON}");
    $sheet7->setCellValue("A{$totalIDRRow_NON}", "Total IDR");

    $sheet7->getStyle("A{$totalIDRRow_NON}:{$lastColLetter}{$totalIDRRow_NON}")
        ->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $sheet7->getStyle("A{$totalIDRRow_NON}")
        ->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

    $sheet7->setCellValue("D{$totalIDRRow_NON}",
        "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"IDR\",D{$dataStartRow}:D{$dataEndRow})");

    $sheet7->setCellValue("E{$totalIDRRow_NON}",
        "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"IDR\",E{$dataStartRow}:E{$dataEndRow})");

    for ($col = $agingStartCol; $col <= $lastColIndex; $col++) {

        if (in_array($col, $skipCols)) continue;

        $colLetter = Coordinate::stringFromColumnIndex($col);

        $sheet7->setCellValue(
            $colLetter.$totalIDRRow_NON,
            "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"IDR\",{$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})"
        );
    }

    /* ================= TOTAL USD NON GROUP ================= */
    $totalUSDRow_NON = $totalIDRRow_NON + 1;

    $sheet7->mergeCells("A{$totalUSDRow_NON}:C{$totalUSDRow_NON}");
    $sheet7->setCellValue("A{$totalUSDRow_NON}", "Total USD");

    $sheet7->getStyle("A{$totalUSDRow_NON}:{$lastColLetter}{$totalUSDRow_NON}")
        ->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $sheet7->getStyle("A{$totalUSDRow_NON}")
        ->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

    $sheet7->setCellValue("D{$totalUSDRow_NON}",
        "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"USD\",D{$dataStartRow}:D{$dataEndRow})");

    $sheet7->setCellValue("E{$totalUSDRow_NON}",
        "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"USD\",E{$dataStartRow}:E{$dataEndRow})");

    for ($col = $agingStartCol; $col <= $lastColIndex; $col++) {

        if (in_array($col, $skipCols)) continue;

        $colLetter = Coordinate::stringFromColumnIndex($col);

        $sheet7->setCellValue(
            $colLetter.$totalUSDRow_NON,
            "=SUMIF(C{$dataStartRow}:C{$dataEndRow},\"USD\",{$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})"
        );
    }

    /* ================= SUMMARY NON GROUP ================= */
    $summaryRow = $totalUSDRow_NON + 1;

    $sheet7->mergeCells("A{$summaryRow}:D{$summaryRow}");
    $sheet7->setCellValue("A{$summaryRow}", "Summary Non Group");

    $sheet7->getStyle("A{$summaryRow}:{$lastColLetter}{$summaryRow}")
        ->applyFromArray([
            'font' => ['bold' => true],
            
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EAEAEA'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $sheet7->getStyle("A{$summaryRow}")
        ->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EAEAEA'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

    $sheet7->setCellValue("E{$summaryRow}",
        "=SUM(E{$dataStartRow}:E{$dataEndRow})");

    for ($col = $agingStartCol; $col <= $lastColIndex; $col++) {

        if (in_array($col, $skipCols)) continue;

        $colLetter = Coordinate::stringFromColumnIndex($col);

        $sheet7->setCellValue(
            $colLetter.$summaryRow,
            "=SUM({$colLetter}{$dataStartRow}:{$colLetter}{$dataEndRow})"
        );
    }

    $rowNum = $summaryRow + 2;
}

/* ============================================================
   GRAND TOTAL (GROUP + NON GROUP)
============================================================ */

/* ================= TOTAL IDR (ALL) ================= */
$grandTotalIDRRow = $rowNum;

$sheet7->mergeCells("A{$grandTotalIDRRow}:C{$grandTotalIDRRow}");
$sheet7->setCellValue("A{$grandTotalIDRRow}", "Grand Total IDR");

$sheet7->getStyle("A{$grandTotalIDRRow}:{$lastColLetter}{$grandTotalIDRRow}")
    ->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFF2CC'],
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
        ],
    ]);

    $sheet7->getStyle("A{$grandTotalIDRRow}")
    ->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFF2CC'],
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
        ],
    ]);

for ($col = 4; $col <= $lastColIndex; $col++) {

    if (in_array($col, $skipCols)) continue;

    $colLetter = Coordinate::stringFromColumnIndex($col);

    $sheet7->setCellValue(
        $colLetter.$grandTotalIDRRow,
        "={$colLetter}{$totalIDRRow}+{$colLetter}{$totalIDRRow_NON}"
    );
}

$rowNum++;

/* ================= TOTAL USD (ALL) ================= */
$grandTotalUSDRow = $rowNum;

$sheet7->mergeCells("A{$grandTotalUSDRow}:C{$grandTotalUSDRow}");
$sheet7->setCellValue("A{$grandTotalUSDRow}", "Grand Total USD");

$sheet7->getStyle("A{$grandTotalUSDRow}:{$lastColLetter}{$grandTotalUSDRow}")
    ->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFF2CC'],
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
        ],
    ]);

    $sheet7->getStyle("A{$grandTotalUSDRow}")
    ->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFF2CC'],
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
        ],
    ]);

for ($col = 4; $col <= $lastColIndex; $col++) {

    if (in_array($col, $skipCols)) continue;

    $colLetter = Coordinate::stringFromColumnIndex($col);

    $sheet7->setCellValue(
        $colLetter.$grandTotalUSDRow,
        "={$colLetter}{$totalUSDRow}+{$colLetter}{$totalUSDRow_NON}"
    );
}

$rowNum++;

/* ================= GRAND SUMMARY ================= */
$grandSummaryRow = $rowNum;

$sheet7->mergeCells("A{$grandSummaryRow}:D{$grandSummaryRow}");
$sheet7->setCellValue("A{$grandSummaryRow}", "Grand Summary");

$sheet7->getStyle("A{$grandSummaryRow}:{$lastColLetter}{$grandSummaryRow}")
    ->applyFromArray([
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFD966'],
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
        ],
    ]);

    $sheet7->getStyle("A{$grandSummaryRow}")
    ->applyFromArray([
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'FFD966'],
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
        ],
    ]);

for ($col = 5; $col <= $lastColIndex; $col++) {

    if (in_array($col, $skipCols)) continue;

    $colLetter = Coordinate::stringFromColumnIndex($col);

    $sheet7->setCellValue(
        $colLetter.$grandSummaryRow,
        "={$colLetter}{$summaryRow_GROUP}+{$colLetter}{$summaryRow}"
    );
}

$rowNum++;

/* ================= FORMAT ================= */

$highestColumn = Coordinate::stringFromColumnIndex($lastColIndex);

$sheet7->getStyle('D6:'.$highestColumn.$rowNum)
      ->getNumberFormat()->setFormatCode('#,##0.00');

$sheet7->getStyle('A4:'.$highestColumn.($rowNum-1))
      ->applyFromArray(['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN]]]);

for($c=1; $c<=$lastColIndex; $c++){
    $colLetter = Coordinate::stringFromColumnIndex($c);
    $sheet7->getColumnDimension($colLetter)->setAutoSize(true);
}

$sheet7->freezePaneByColumnAndRow(1, $rowHeader2 + 1);




/*
|--------------------------------------------------------------------------
| OUTPUT
|--------------------------------------------------------------------------
*/

$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Payable_Card_Statement.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
