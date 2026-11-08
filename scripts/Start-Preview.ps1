$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)

$php = (Get-Command php.exe -ErrorAction Stop).Source
$ext = Join-Path (Split-Path $php -Parent) 'ext'
$phpArgs = @('-d', "extension_dir=$ext", '-d', 'extension=pdo_sqlite', '-d', 'extension=gd', '-d', 'extension=mbstring')
$local = New-Item -ItemType Directory -Force '.local'
$root = Join-Path $local.FullName 'wordpress'

if (!(Test-Path "$root/wp-load.php")) {
    $archive = Join-Path $local.FullName 'wordpress.zip'
    Invoke-WebRequest 'https://wordpress.org/latest.zip' -OutFile $archive
    Expand-Archive -LiteralPath $archive -DestinationPath $local.FullName -Force
}
if (!(Test-Path "$root/wp-content/db.php")) {
    $archive = Join-Path $local.FullName 'sqlite.zip'
    Invoke-WebRequest 'https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip' -OutFile $archive
    Expand-Archive -LiteralPath $archive -DestinationPath "$root/wp-content/plugins" -Force
    Copy-Item "$root/wp-content/plugins/sqlite-database-integration/db.copy" "$root/wp-content/db.php"
}
if (!(Test-Path "$root/wp-content/themes/royal-room")) {
    New-Item -ItemType Junction -Path "$root/wp-content/themes/royal-room" -Target (Resolve-Path 'theme/royal-room').Path | Out-Null
}

& $php @phpArgs 'scripts/install-local.php'
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

$listening = Get-NetTCPConnection -LocalPort 8874 -State Listen -ErrorAction SilentlyContinue
if (!$listening) {
    Start-Process -FilePath $php -ArgumentList ($phpArgs + @('-S', '127.0.0.1:8874')) -WorkingDirectory $root -WindowStyle Hidden -RedirectStandardOutput "$($local.FullName)/server.stdout.log" -RedirectStandardError "$($local.FullName)/server.stderr.log"
}
