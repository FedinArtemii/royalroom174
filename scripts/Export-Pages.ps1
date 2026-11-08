$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)
$php = (Get-Command php.exe -ErrorAction Stop).Source
$ext = Join-Path (Split-Path $php -Parent) 'ext'
& $php -d "extension_dir=$ext" -d extension=pdo_sqlite -d extension=gd -d extension=mbstring 'scripts/export.php'
exit $LASTEXITCODE
