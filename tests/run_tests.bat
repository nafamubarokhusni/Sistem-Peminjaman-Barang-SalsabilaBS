@echo off
echo ============================================================
echo  Sistem Peminjaman Barang - PHPUnit Test Runner
echo ============================================================
cd /d %~dp0
C:\xampp\php\php.exe vendor\bin\phpunit --testdox %*
echo.
echo ============================================================
echo  Done.
echo ============================================================
pause
