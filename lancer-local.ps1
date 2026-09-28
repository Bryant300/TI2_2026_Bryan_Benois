param([int]$Port = 8082)
$ErrorActionPreference = 'Stop'
$php = Get-Command php -ErrorAction SilentlyContinue
if (-not $php) { throw 'PHP introuvable : ajoutez PHP au PATH ou utilisez WampServer.' }
$argsPhp = @()
$modules = & $php.Source -m
foreach ($extension in @('pdo_mysql', 'mbstring')) {
    if ($modules -notcontains $extension) {
        $dll = Join-Path (Split-Path $php.Source) "ext\php_$extension.dll"
        if (-not (Test-Path -LiteralPath $dll)) { throw "Extension manquante : $extension" }
        $argsPhp += @('-d', "extension=$dll")
    }
}
if (-not (Test-Path -LiteralPath (Join-Path $PSScriptRoot 'config.php'))) {
    Copy-Item -LiteralPath (Join-Path $PSScriptRoot 'config.php.ini') -Destination (Join-Path $PSScriptRoot 'config.php')
    Write-Host 'config.php créé : vérifiez le port et les identifiants MariaDB.'
}
Write-Host "Site local : http://127.0.0.1:$Port/ - Ctrl+C pour arrêter."
& $php.Source @argsPhp -S "127.0.0.1:$Port" -t (Join-Path $PSScriptRoot 'public')
