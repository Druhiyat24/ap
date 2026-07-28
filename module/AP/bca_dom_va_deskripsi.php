<?php
/**
 * Isi sheet "Deskripsi" pada file export BCA Dom VA - berisi Matrix
 * skenario, dokumentasi struktur file (Header & Records), dan pilihan
 * field, sesuai spesifikasi resmi template BCA Bulk Transfer.
 *
 * Dipanggil dari export_payment_list.php dan export_transfer_list.php
 * supaya isinya konsisten dan tidak duplikat.
 *
 * Tabel Struktur File (Header/Records) dibuat selebar tabel Matrix
 * (kolom A:G) dengan Keterangan di-merge B:D dan Mandatory di-merge E:F,
 * supaya tampilannya senada/serapi Matrix walau jumlah datanya cuma 4
 * "kolom logis" (Column, Keterangan, Mandatory, Length).
 */

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

function fillBcaDomVaDeskripsiSheet($sheet)
{
    $NAVY = '1F3864';
    $SUBHEAD = '3B5998';

    /* ==============================
       MATRIX
    ============================== */
    $sheet->setCellValue('A1', 'Matrix');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

    $sheet->mergeCells('A3:B3');
    $sheet->setCellValue('A3', 'Scenario');
    $sheet->mergeCells('C3:G3');
    $sheet->setCellValue('C3', 'Input Location');
    $sheet->getStyle('A3:G3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle('A3:G3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($NAVY);
    $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

    $sheet->fromArray(['Statement Type', 'Authorization Type', 'Effective Date/Time', 'Debited Account', 'Charge Account', 'Charge Type', 'Currency'], null, 'A4');
    $sheet->getStyle('A4:G4')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle('A4:G4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('9DC3E6');
    $sheet->getStyle('A4:G4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);

    $sheet->mergeCells('A5:A6');
    $sheet->setCellValue('A5', 'Multi Debet');
    $sheet->setCellValue('B5', 'Bulk');
    $sheet->mergeCells('C5:G5');
    $sheet->setCellValue('C5', 'Header/Detail');

    $sheet->setCellValue('B6', 'Satuan');
    $sheet->mergeCells('C6:G6');
    $sheet->setCellValue('C6', 'Header/Detail');

    $sheet->setCellValue('A7', 'Single Debet');
    $sheet->setCellValue('B7', 'Bulk');
    $sheet->setCellValue('C7', 'Header');
    $sheet->mergeCells('D7:G7');
    $sheet->setCellValue('D7', 'Header/Detail');

    $sheet->getStyle('A5:B7')->getFont()->setBold(true);
    $sheet->getStyle('A5:G7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A3:G7')->getBorders()->getAllBorders()->setBorderStyle('thin');
    $sheet->getStyle('A5:B6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDEBF7');

    /* ==============================
       STRUKTUR FILE - HEADER
       (tabel selebar Matrix: A:G, Keterangan di-merge B:D, Mandatory E:F)
    ============================== */
    $sheet->setCellValue('A10', 'Struktur File');
    $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(12);

    $sheet->mergeCells('A12:G12');
    $sheet->setCellValue('A12', 'Header');
    $sheet->getStyle('A12')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle('A12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($SUBHEAD);

    $sheet->mergeCells('B13:D13');
    $sheet->mergeCells('E13:F13');
    $sheet->fromArray(['Column', 'Keterangan', 'Mandatory', 'Length (max. karakter)'], null, 'A13');
    $sheet->setCellValue('G13', 'Length (max. karakter)');
    $sheet->setCellValue('E13', 'Mandatory');
    $sheet->getStyle('A13:G13')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle('A13:G13')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($NAVY);
    $sheet->getStyle('A13:G13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $headerRows = [
        ['Tipe Record', 'Berisi "0" untuk Header (Otomatis Terbentuk)', 'Y (Otomatis Terbentuk)', '1', false],
        ['Tipe Transaksi', 'Berisi "FT" (Otomatis Terbentuk)', 'Y (Otomatis Terbentuk)', '2', false],
        ['Tipe Mutasi', "Menentukan Tipe Mutasi pada Pengirim:\nSingle (SD) = Terbentuk 1 mutasi total seluruh transaksi\nMulti (MD) = Terbentuk mutasi sebanyak jumlah record", 'Y', '2', true],
        ['Currency/Mata Uang', 'Berisikan mata uang yang digunakan dalam transaksi', 'C (Mandatory apabila pada Detail record tidak diisi)', '3', false],
        ['Jenis Otorisasi', "Tipe Otorisasi yang akan digunakan oleh nasabah (Satuan atau Bulk).\nNote: Untuk Layanan Transfer BI-FAST jenis otorisasi hanya bisa Bulk.", 'Y', '1', true],
        ['Corporate ID', 'Corporate ID myBCA Bisnis anda', 'Y', '10', false],
        ['Header ID', 'Nomor unik dari file Bulk Transfer per corporate ID (Harus numeric)', 'N', '8', false],
        ['Dependency Header ID', "Nomor unik yang digunakan untuk transaksi prioritas yang berurutan\nNote: currently not available.", 'N', '8', false],
        ['Tanggal Efektif', "Tanggal efektif transaksi dijalankan dengan format : YYYYMMDD. Contoh: 20251208", 'C (Mandatory apabila pada Detail record tidak diisi)', '8', false],
        ['Waktu Proses Transaksi', 'Jam kapan transaksi akan dijalankan. Diisi dengan format jam (HH)', 'N (Diisi pada Header/Detail)', '2', false],
        ['Rekening Asal', 'Rekening Sumber Dana, rekening debet harus merupakan rekening yang terdaftar di myBCA Bisnis sebagai rekening operasional dari Corporate ID yang bersangkutan', 'C (Mandatory apabila pada Detail record tidak diisi)', '10', false],
        ['Jenis Biaya', "Jenis pembebanan biaya yang dapat dipilih nasabah\nOUR = Biaya ditanggung Pengirim\nBEN = Biaya ditanggung Penerima\nSHA = Biaya terbagi 50:50 antar Pengirim dan Penerima", "C (Mandatory apabila pada Detail record tidak diisi, khusus Layanan Transfer LLG/RTG/BIF)", '3', true],
        ['Rekening Biaya', 'Rekening yang akan digunakan untuk mendebitkan biaya transfer ke bank lain. Rekening biaya harus terdaftar sebagai rekening operasional di myBCA Bisnis', 'C (Mandatory apabila pada Detail record tidak diisi, khusus Layanan Transfer LLG/RTG/BIF)', '10', true],
        ['Berita 1', 'Keterangan yang diinput nasabah dan akan muncul pada email pengirim', 'N', '18', false],
        ['Berita 2', 'Keterangan yang diinput nasabah dan akan muncul pada email pengirim', 'N', '18', false],
        ['Validasi Nama Penerima', "\u{2022} Y: Wajib isi nama penerima. Nama Penerima Rekening BCA akan diverifikasi sesuai data BCA. Untuk transfer BIF, nama penerima diverifikasi sesuai data dari sistem BI-FAST.\n\u{2022} C: Nama penerima tidak wajib diisi. Jika diisi, akan diverifikasi seperti pada opsi Y. Jika tidak diisi, nama penerima akan otomatis diisi berdasarkan hasil inquiry ke database BCA untuk rekening BCA atau sistem BI-FAST untuk Layanan Transfer BIF.\n\u{2022} N: Nama penerima tidak wajib diisi. Jika diisi, nama yang muncul adalah hasil input dari Nasabah (tidak akan dilakukan inquiry untuk rekening BCA). Jika tidak diisi, akan dilakukan inquiry seperti pada opsi C. Untuk layanan transfer BIF, nama penerima akan otomatis diganti dengan hasil inquiry dari sistem BI-FAST.\nNote: Jika tidak diisi maka akan dianggap N.", 'N', '1', true],
        ['Persetujuan Penyesuaian Tanggal Efektif', "Khusus transaksi bertipe otorisasi Bulk dan Tipe Mutasi Multi.\nBila diisi dengan \"Y\" maka nasabah setuju transaksi yang jatuh diluar jam transaksi ke Bank Lain akan dijalankan pada hari kerja berikutnya.\nJika diisi dengan \"N\" maka nasabah tidak setuju transaksi yang jatuh diluar jam transaksi ke Bank Lain akan dijalankan pada hari kerja berikutnya.", 'N', '1', true],
    ];

    $row = 14;
    foreach ($headerRows as $data) {
        [$col, $ket, $mand, $len, $highlight] = $data;
        $sheet->setCellValue('A' . $row, $col);
        $sheet->setCellValue('B' . $row, $ket);
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->setCellValue('E' . $row, $mand);
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->setCellValue('G' . $row, $len);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        if ($highlight) {
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDEBF7');
        }
        $row++;
    }
    $headerTableEnd = $row - 1;
    $sheet->getStyle('A13:G' . $headerTableEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
    $sheet->getStyle('A14:G' . $headerTableEnd)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
    $sheet->getStyle('G14:G' . $headerTableEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_TOP);

    /* ==============================
       STRUKTUR FILE - RECORDS
    ============================== */
    $row += 2;
    $sheet->mergeCells('A' . $row . ':G' . $row);
    $sheet->setCellValue('A' . $row, 'Records');
    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($SUBHEAD);
    $row++;

    $sheet->mergeCells('B' . $row . ':D' . $row);
    $sheet->mergeCells('E' . $row . ':F' . $row);
    $sheet->setCellValue('A' . $row, 'Column');
    $sheet->setCellValue('B' . $row, 'Keterangan');
    $sheet->setCellValue('E' . $row, 'Mandatory');
    $sheet->setCellValue('G' . $row, 'Length (max. karakter)');
    $sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($NAVY);
    $sheet->getStyle('A' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $row++;
    $recordsHeaderRow = $row - 1;

    $recordsRows = [
        ['Tipe Record', 'Berisi "1" untuk Detail (Otomatis Terbentuk)', 'Y (Otomatis Terbentuk)', '1', false],
        ['Transaction ID', 'ID Transaksi harus unik, belum pernah digunakan transaksi sebelumnya', 'Y', '18', false],
        ['Layanan Transfer', 'Jenis Layanan Transfer, yang terdiri dari: BCA, LLG, RTG, BIF.', 'Y', '3', false],
        ['Rekening Asal', 'Rekening Sumber Dana yang terdaftar sebagai rekening operasional di myBCA', 'Y (Diisi Pada Header/Detail)', '10', true],
        ['Currency/Mata Uang', 'Berisikan mata uang yang digunakan dalam transaksi', 'C (Mandatory jika pada Header tidak diisi)', '3', false],
        ['Jenis Biaya', "Jenis pembebanan biaya yang dapat dipilih nasabah\nOUR = Biaya ditanggung Pengirim\nBEN = Biaya ditanggung Penerima\nSHA = Biaya terbagi 50:50 antar Pengirim dan Penerima", 'C (Mandatory jika pada Header tidak diisi, khusus Layanan Transfer LLG/RTG/BIF)', '3', true],
        ['Rekening Biaya', 'Rekening yang akan digunakan untuk mendebitkan biaya transfer ke bank lain. Rekening biaya harus terdaftar sebagai rekening operasional di myBCA Bisnis', 'C (Mandatory jika pada Header tidak diisi, khusus Layanan Transfer LLG/RTG/BIF)', '10', true],
        ['Tanggal Efektif', 'Tanggal efektif transaksi dijalankan dengan format : YYYYMMDD. Contoh: 20251208', 'C (Mandatory jika pada Header tidak diisi)', '8', false],
        ['Waktu Proses Transaksi', 'Jam kapan transaksi akan dijalankan. Diisi dengan format jam (HH)', 'N (Diisi Pada Header/Detail)', '2', false],
        ['Beneficiary ID', "Jika nasabah mengisi Beneficiary ID, maka field-field di bawah ini untuk layanan transfer BCA/LLG/RTG/BIF akan diabaikan karena datanya diambil dari data Daftar Rekening Tujuan sesuai dengan Beneficiary ID yang diisi:\n1. Rekening Tujuan\n2. Nama Penerima\n3. Email Penerima\n4. Kode SWIFT Bank Tujuan\n5. Kategori Penerima\n6. Kependudukan\n7. Kewarganegaraan", 'C (Mandatory jika menggunakan Designated Account)', '70', true],
        ['Rekening Tujuan', 'Nomor rekening tujuan transaksi', 'C (Mandatory jika tidak menggunakan Designated Account)', '34', false],
        ['Nama Penerima', 'Nama penerima tujuan transaksi', "C\nNote:\n1. Mandatory jika tidak menggunakan DA\n2. Mandatory jika layanan transfer LLG/RTG", "- BCA: 40\n- LLG/RTGS: 70\n- BI-FAST: 140", false],
        ['Nominal', 'Nominal transaksi dengan format 2 angka decimal', 'Y', '13 + 2 decimal', false],
        ['Berita 1', "Keterangan yang diinput nasabah dan muncul pada :\nMulti Debet : Email & Mutasi Pengirim dan Penerima\nSingle Debet : Email & Mutasi Pengirim", 'N', '18', false],
        ['Berita 2', "Keterangan yang diinput nasabah dan muncul pada :\nMulti Debet : Email & Mutasi Pengirim dan Penerima\nSingle Debet : Email & Mutasi Pengirim", 'N', '18', false],
        ['Deskripsi', 'Keterangan yang akan tampil pada bukti transaksi dan email penerima', 'N', '200', false],
        ['Email Penerima', 'Diisi dengan alamat email untuk pengiriman notifikasi transaksi berhasil', 'N', '300', false],
        ['Kode Swift Bank Tujuan', 'SWIFT Code bank tujuan transaksi', "C (Mandatory jika LLG/RTG/BIF)\nNote : Not Mandatory jika menggunakan DA", '11', false],
        ['Kategori Penerima', 'Jenis tipe nasabah tujuan transaksi', "C (Mandatory jika LLG/RTG)\nNote : Not Mandatory jika menggunakan DA", '1', false],
        ['Penduduk/Non-Penduduk', 'Jenis resident nasabah tujuan transaksi', "C (Mandatory jika LLG/RTG)\nNote : Not Mandatory jika menggunakan DA", '1', false],
        ['Kewarganegaraan', 'Kewarganegaraan Penerima (WNI / WNA)', "C (Mandatory jika LLG/RTG)\nNote : Not Mandatory jika menggunakan DA", '1', false],
        ['Tujuan Transaksi', 'Tujuan transaksi yang harus diisi jika Layanan Transfer = BIF', 'Y (Apabila BIF)', '2', false],
    ];

    foreach ($recordsRows as $data) {
        [$col, $ket, $mand, $len, $highlight] = $data;
        $sheet->setCellValue('A' . $row, $col);
        $sheet->setCellValue('B' . $row, $ket);
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->setCellValue('E' . $row, $mand);
        $sheet->mergeCells('E' . $row . ':F' . $row);
        $sheet->setCellValue('G' . $row, $len);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        if ($highlight) {
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DDEBF7');
        }
        $row++;
    }
    $recordsTableEnd = $row - 1;
    $sheet->getStyle('A' . $recordsHeaderRow . ':G' . $recordsTableEnd)->getBorders()->getAllBorders()->setBorderStyle('thin');
    $sheet->getStyle('A' . ($recordsHeaderRow + 1) . ':G' . $recordsTableEnd)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
    $sheet->getStyle('G' . ($recordsHeaderRow + 1) . ':G' . $recordsTableEnd)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->getColumnDimension('A')->setWidth(28);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(18);
    $sheet->getColumnDimension('F')->setWidth(18);
    $sheet->getColumnDimension('G')->setWidth(16);

    /* ==============================
       PILIHAN FIELD (kolom I:J)
    ============================== */
    $sheet->setCellValue('I1', 'Pilihan Field');
    $sheet->getStyle('I1')->getFont()->setBold(true)->setSize(12);

    $lookupRow = 3;

    $lookups = [
        'Tipe Transaksi' => [
            ['BCA', 'Transfer sesama BCA'],
            ['LLG', 'Transfer Bank Lain dalam negeri dengan LLG'],
            ['RTG', 'Transfer Bank Lain dalam negeri dengan RTGS'],
            ['BIF', 'Transfer Bank Lain dalam negeri dengan BI-FAST'],
        ],
        'Kategori Penerima' => [
            ['1', 'Perorangan'],
            ['2', 'Perusahaan'],
            ['3', 'Pemerintah'],
        ],
        'Kependudukan' => [
            ['1', 'Residence / Penduduk'],
            ['2', 'Non Residence / Bukan Penduduk'],
        ],
        'Kewarganegaraan' => [
            ['C', 'Warga Negara Indonesia'],
            ['N', 'Warga Negara Asing'],
        ],
        'Tujuan Transaksi' => [
            ['01', 'Investasi'],
            ['02', 'Pemindahan Dana'],
            ['03', 'Pembelian'],
            ['99', 'Lainnya'],
        ],
    ];

    foreach ($lookups as $title => $items) {
        $sheet->fromArray([$title, 'Keterangan'], null, 'I' . $lookupRow);
        $sheet->getStyle('I' . $lookupRow . ':J' . $lookupRow)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('I' . $lookupRow . ':J' . $lookupRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($NAVY);
        $sheet->getStyle('I' . $lookupRow . ':J' . $lookupRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $startData = $lookupRow + 1;
        $lookupRow++;
        foreach ($items as $item) {
            $sheet->fromArray($item, null, 'I' . $lookupRow);
            $lookupRow++;
        }
        $sheet->getStyle('I' . $startData . ':J' . ($lookupRow - 1))->getBorders()->getAllBorders()->setBorderStyle('thin');
        $lookupRow += 2;
    }

    $sheet->getColumnDimension('I')->setWidth(20);
    $sheet->getColumnDimension('J')->setWidth(45);

    $sheet->getStyle('A1:J' . max($recordsTableEnd, $lookupRow))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
}
