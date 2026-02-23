@echo off
REM Start local PHP server for desktop development
REM Usage: start-server.bat [port]
SET PORT=%1
IF "%PORT%"=="" SET PORT=8000

where php >nul 2>nul
IF ERRORLEVEL 1 (
  echo PHP not found in PATH. Please install PHP 8.2+ and add to PATH.
  exit /b 1
)

echo Starting PHP built-in server at http://localhost:%PORT%
start "Laravel Dev Server" "http://localhost:%PORT%"
php -S 127.0.0.1:%PORT% -t public
