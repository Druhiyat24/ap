@echo off
REM ============================================================================
REM  PEMASANG PENJADWAL - Sync Selisih Kurs (FX revaluation)
REM
REM  Berkas ini SATU untuk dua pekerjaan:
REM    1. Dijalankan TANPA argumen  -> memasang tugas terjadwal di Windows.
REM    2. Dijalankan dengan "RUN"   -> itu yang dikerjakan tugasnya tiap malam.
REM  Argumen "UNINSTALL" menghapus tugasnya lagi.
REM
REM  CARA PAKAI DI SERVER:
REM    - salin berkas ini ke server (mis. D:\xampp\task_sync_selisih_kurs.bat)
REM    - klik kanan -> Run as administrator
REM
REM  Kenapa lewat HTTP, bukan php.exe: catatan di dalam
REM  sync_selisih_kurs_scheduler.php sendiri menulis bahwa di server ini
REM  ekstensi php-cli TIDAK sama dengan yang dipakai Apache/mod_php, jadi
REM  memanggilnya lewat URL lebih aman. Kalau php-cli di server ternyata
REM  sudah beres, ganti MODE di bawah jadi CLI.
REM ============================================================================

setlocal EnableExtensions

REM ---------------------------------------------------------------- setelan --
REM  MODE : HTTP (disarankan) atau CLI
set "MODE=HTTP"

set "TASKNAME=AP_Sync_Selisih_Kurs"
set "JAM=00:00"
set "URL=http://localhost/ap/module/AP/sync_selisih_kurs_scheduler.php"

REM  Dipakai hanya kalau MODE=CLI
set "PHPEXE=D:\xampp\php\php.exe"
set "PHPFILE=D:\xampp\htdocs\ap\module\AP\sync_selisih_kurs_scheduler.php"

REM  Catatan hasil. SENGAJA DI LUAR htdocs: apa pun di dalam htdocs bisa
REM  dibuka lewat browser oleh siapa saja, dan keluaran skrip ini memuat
REM  nomor dokumen jurnal.
set "LOGDIR=D:\xampp\task_logs\sync_selisih_kurs"

REM  Simpan catatan berapa hari ke belakang.
set "SIMPAN_HARI=60"
REM ---------------------------------------------------------- akhir setelan --

if /i "%~1"=="RUN"       goto :JALANKAN
if /i "%~1"=="UNINSTALL" goto :COPOT
goto :PASANG


REM ===========================================================================
REM  1. MEMASANG TUGAS
REM ===========================================================================
:PASANG
echo.
echo ===========================================================
echo   Memasang tugas terjadwal: %TASKNAME%
echo   Jadwal : setiap hari jam %JAM%
echo   Cara   : %MODE%
echo ===========================================================
echo.

REM -- harus Administrator, kalau tidak schtasks /RU SYSTEM akan ditolak --
net session >nul 2>&1
if errorlevel 1 (
    echo [GAGAL] Jendela ini BUKAN Administrator.
    echo         Tutup, lalu klik kanan berkas ini - "Run as administrator".
    echo.
    pause
    exit /b 1
)

REM -- berkas PHP-nya benar-benar ada? --
if not exist "%PHPFILE%" (
    echo [GAGAL] Tidak ketemu: %PHPFILE%
    echo         Betulkan PHPFILE di bagian setelan berkas ini.
    echo.
    pause
    exit /b 1
)

if /i "%MODE%"=="CLI" (
    if not exist "%PHPEXE%" (
        echo [GAGAL] Tidak ketemu: %PHPEXE%
        echo         Betulkan PHPEXE di bagian setelan, atau pakai MODE=HTTP.
        echo.
        pause
        exit /b 1
    )
)

if not exist "%LOGDIR%" mkdir "%LOGDIR%" 2>nul
if not exist "%LOGDIR%" (
    echo [GAGAL] Tidak bisa membuat folder catatan: %LOGDIR%
    echo.
    pause
    exit /b 1
)

REM -- kalau tugas dgn nama ini sudah ada, beri tahu dulu; /F akan menimpanya --
schtasks /Query /TN "%TASKNAME%" >nul 2>&1
if not errorlevel 1 (
    echo [INFO] Tugas "%TASKNAME%" sudah ada dan akan DITIMPA.
    echo.
)

REM -- /RU SYSTEM: jalan walaupun tidak ada yang login, tanpa simpan sandi --
schtasks /Create /TN "%TASKNAME%" /TR "\"%~f0\" RUN" /SC DAILY /ST %JAM% /RU SYSTEM /RL HIGHEST /F
if errorlevel 1 (
    echo.
    echo [GAGAL] schtasks menolak membuat tugasnya. Pesan galatnya di atas.
    echo.
    pause
    exit /b 1
)

echo.
echo [BERES] Tugas terpasang.
echo.
schtasks /Query /TN "%TASKNAME%" /FO LIST
echo.
echo -----------------------------------------------------------
echo  Catatan hasil : %LOGDIR%
echo  Uji sekarang  : schtasks /Run /TN "%TASKNAME%"
echo  Hapus tugas   : "%~f0" UNINSTALL
echo -----------------------------------------------------------
echo.
echo  CATATAN: menguji sekarang berarti skripnya BENAR-BENAR menulis
echo  jurnal selisih kurs ke basis data produksi. Skripnya memang aman
echo  diulang (per tanggal: sisip-atau-perbarui, tidak pernah dobel),
echo  tapi tetap sebuah penulisan ke produksi.
echo.
set "UJI="
set /p "UJI=Jalankan sekali sekarang untuk menguji? (y/N): "
if /i "%UJI%"=="y" (
    echo.
    echo Menjalankan...
    schtasks /Run /TN "%TASKNAME%"
    echo.
    echo Tunggu beberapa saat, lalu lihat berkas catatan terbaru di:
    echo   %LOGDIR%
) else (
    echo Dilewati. Tugasnya tetap akan jalan sendiri jam %JAM%.
)
echo.
pause
exit /b 0


REM ===========================================================================
REM  2. YANG DIKERJAKAN TUGASNYA TIAP MALAM
REM ===========================================================================
:JALANKAN
if not exist "%LOGDIR%" mkdir "%LOGDIR%" 2>nul

REM  Penanda tanggal dibuat lewat PowerShell, BUKAN %DATE%: bentuk %DATE%
REM  ikut setelan wilayah Windows, jadi nama berkasnya bisa berbeda-beda
REM  (atau memuat garis miring) di server yang setelannya lain.
set "STAMP="
for /f "usebackq delims=" %%i in (`powershell -NoProfile -Command "Get-Date -Format yyyyMMdd_HHmmss"`) do set "STAMP=%%i"
if not defined STAMP set "STAMP=tanpa_tanggal"
set "LOG=%LOGDIR%\sync_%STAMP%.log"

echo ===== MULAI %DATE% %TIME% =====>>"%LOG%"
echo mode=%MODE%>>"%LOG%"

if /i "%MODE%"=="CLI" goto :JALAN_CLI

echo url=%URL%>>"%LOG%"
REM  curl.exe sudah ikut Windows 10/Server 2019 ke atas. Kalau tidak ada,
REM  jatuh ke PowerShell - dua-duanya menulis keluaran skrip apa adanya.
where curl.exe >nul 2>&1
if errorlevel 1 goto :JALAN_PS

curl.exe -sS -L --max-time 3600 -w "\nHTTP_STATUS=%%{http_code}\n" "%URL%" >>"%LOG%" 2>&1
set "RC=%ERRORLEVEL%"
REM  curl pulang exit 0 walaupun server menjawab 500, jadi statusnya
REM  diperiksa sendiri - supaya tugas yang gagal tampak merah di
REM  Task Scheduler, bukan diam-diam "sukses".
if "%RC%"=="0" (
    findstr /C:"HTTP_STATUS=200" "%LOG%" >nul 2>&1
    if errorlevel 1 (
        echo [GAGAL] Status HTTP bukan 200 - lihat baris HTTP_STATUS di atas.>>"%LOG%"
        set "RC=1"
    )
)
goto :SELESAI

:JALAN_PS
powershell -NoProfile -ExecutionPolicy Bypass -Command "$ErrorActionPreference='Stop'; try { (Invoke-WebRequest -UseBasicParsing -Uri '%URL%' -TimeoutSec 3600).Content } catch { Write-Output ('ERROR: ' + $_.Exception.Message); exit 1 }" >>"%LOG%" 2>&1
set "RC=%ERRORLEVEL%"
goto :SELESAI

:JALAN_CLI
echo file=%PHPFILE%>>"%LOG%"
"%PHPEXE%" "%PHPFILE%" >>"%LOG%" 2>&1
set "RC=%ERRORLEVEL%"
goto :SELESAI

:SELESAI
echo ===== SELESAI %DATE% %TIME% (exit=%RC%) =====>>"%LOG%"

REM  Buang catatan yang lebih tua dari SIMPAN_HARI supaya foldernya tidak
REM  menumpuk tanpa batas.
forfiles /p "%LOGDIR%" /m sync_*.log /d -%SIMPAN_HARI% /c "cmd /c del @path" >nul 2>&1

exit /b %RC%


REM ===========================================================================
REM  3. MENGHAPUS TUGAS
REM ===========================================================================
:COPOT
net session >nul 2>&1
if errorlevel 1 (
    echo [GAGAL] Jendela ini BUKAN Administrator.
    pause
    exit /b 1
)
schtasks /Delete /TN "%TASKNAME%" /F
echo.
echo Tugas dihapus. Folder catatan TIDAK ikut dihapus: %LOGDIR%
echo.
pause
exit /b 0
