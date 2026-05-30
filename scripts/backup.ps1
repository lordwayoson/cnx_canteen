Param(
    [string]$OutputDirectory = "C:\\Backups\\canteen",
    [string]$MysqlPath = "C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe",
    [string]$DbHost = "127.0.0.1",
    [string]$User = "root",
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
    [string]$Password = "admin"
=======
    [string]$Password = ""
>>>>>>> theirs
=======
    [string]$Password = ""
>>>>>>> theirs
=======
    [string]$Password = ""
>>>>>>> theirs
)

if (-not (Test-Path $OutputDirectory)) {
    New-Item -ItemType Directory -Path $OutputDirectory | Out-Null
}

if (-not (Test-Path $MysqlPath)) {
    $mysqldumpCmd = Get-Command mysqldump.exe -ErrorAction SilentlyContinue
    if ($mysqldumpCmd) {
        $MysqlPath = $mysqldumpCmd.Source
    } else {
        throw "mysqldump.exe not found. Update -MysqlPath to your MySQL installation."
    }
}

$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$filename = Join-Path $OutputDirectory "canteen_db-$timestamp.sql"

$arguments = @(
    "--host=$DbHost",
    "--user=$User",
    "--password=$Password",
    "--routines",
    "--events",
    "--single-transaction",
    "canteen_db"
)

& $MysqlPath @arguments > $filename

Write-Host "Backup created at $filename"
