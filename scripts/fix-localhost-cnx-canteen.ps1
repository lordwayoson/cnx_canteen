param(
    [string]$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path,
    [string]$WebRoot = 'C:\xampp\htdocs',
    [string]$AliasName = 'cnx_canteen',
    [string]$ApacheExe = 'C:\xampp\apache\bin\httpd.exe'
)

$ErrorActionPreference = 'Stop'

function Assert-Admin {
    $identity = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = [Security.Principal.WindowsPrincipal]::new($identity)
    if (-not $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
        throw 'Run this script from an elevated PowerShell window: right-click PowerShell and choose "Run as administrator".'
    }
}

function Stop-IisPort80 {
    foreach ($serviceName in @('W3SVC', 'WAS')) {
        $service = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
        if ($service -and $service.Status -ne 'Stopped') {
            Stop-Service -Name $serviceName -Force
        }
    }
}

function Ensure-CnxAlias {
    $aliasPath = Join-Path $WebRoot $AliasName
    if (Test-Path $aliasPath) {
        $item = Get-Item $aliasPath
        if ($item.LinkType -eq 'Junction' -and $item.Target -contains $ProjectRoot) {
            return
        }

        throw "Path already exists and is not the expected junction: $aliasPath"
    }

    New-Item -ItemType Junction -Path $aliasPath -Target $ProjectRoot | Out-Null
}

function Start-XamppApache {
    if (-not (Test-Path $ApacheExe)) {
        throw "Apache executable not found: $ApacheExe"
    }

    & $ApacheExe -t | Out-Host
    if ($LASTEXITCODE -ne 0) {
        throw 'Apache configuration test failed.'
    }

    $apacheService = Get-Service -Name 'Apache2.4' -ErrorAction SilentlyContinue
    if ($apacheService) {
        if ($apacheService.Status -ne 'Running') {
            Start-Service -Name 'Apache2.4'
        }
        return
    }

    $running = Get-Process -Name httpd -ErrorAction SilentlyContinue
    if (-not $running) {
        Start-Process -FilePath $ApacheExe -WorkingDirectory (Split-Path $ApacheExe) -WindowStyle Hidden
        Start-Sleep -Seconds 2
    }
}

Assert-Admin
Stop-IisPort80
Ensure-CnxAlias
Start-XamppApache

$response = Invoke-WebRequest -Uri "http://localhost/$AliasName/" -UseBasicParsing
if ($response.StatusCode -ne 200) {
    throw "Unexpected status code from http://localhost/$AliasName/: $($response.StatusCode)"
}

Write-Host "OK: http://localhost/$AliasName/ is reachable." -ForegroundColor Green
