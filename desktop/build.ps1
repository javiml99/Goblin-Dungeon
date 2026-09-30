$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
$root = Split-Path $PSScriptRoot -Parent
Set-Location $root
$out = Join-Path $root 'dist/GoblinDungeon-Windows'
New-Item $out -ItemType Directory -Force | Out-Null
New-Item 'build' -ItemType Directory -Force | Out-Null
$zipName = 'php-8.4.26-nts-Win32-vs17-x64.zip'
$expected = 'da68394f9193b7f6b89d0c76861a4034ae10efee7fd55a7255d8118c2acf70d7'
$zip = Join-Path $root 'build/php.zip'
try {
    Invoke-WebRequest "https://downloads.php.net/~windows/releases/$zipName" -OutFile $zip
} catch {
    Invoke-WebRequest "https://downloads.php.net/~windows/releases/archives/$zipName" -OutFile $zip
}
if ((Get-FileHash $zip -Algorithm SHA256).Hash.ToLowerInvariant() -ne $expected) { throw 'PHP SHA256 mismatch' }
Expand-Archive $zip "$out/runtime" -Force
Copy-Item 'desktop/php.ini' "$out/runtime/php.ini"
Copy-Item 'GoblinDungeon' "$out/GoblinDungeon" -Recurse -Force
Copy-Item 'router.php' "$out/router.php"
Copy-Item 'desktop/LEEME.txt' "$out/LEEME.txt"
Copy-Item 'docs/THIRD-PARTY.md' "$out/THIRD-PARTY.md"
$vswhere = (Join-Path ([Environment]::GetFolderPath('ProgramFilesX86')) 'Microsoft Visual Studio/Installer/vswhere.exe')
$vs = & $vswhere -latest -products '*' -property installationPath
$redist = Get-ChildItem "$vs/VC/Redist/MSVC" -Directory | Where-Object Name -Match '^\d' | Sort-Object Name -Descending | Select-Object -First 1
$crt = Get-ChildItem "$($redist.FullName)/x64" -Directory -Filter '*.CRT' | Select-Object -First 1
if (!$crt) { throw 'Visual C++ redistributable files not found' }
Copy-Item "$($crt.FullName)/*.dll" "$out/runtime/"
$csc = "$env:WINDIR/Microsoft.NET/Framework64/v4.0.30319/csc.exe"
& $csc /nologo /target:winexe /platform:x64 "/out:$out/GoblinDungeon.exe" /reference:System.Windows.Forms.dll /reference:System.Drawing.dll desktop/Launcher.cs
if ($LASTEXITCODE -ne 0) { throw 'Launcher compilation failed' }
& "$out/runtime/php.exe" -c "$out/runtime/php.ini" -d "extension_dir=$out/runtime/ext" -r 'if (!extension_loaded("pdo_sqlite")) exit(1); echo PHP_VERSION;'
if ($LASTEXITCODE -ne 0) { throw 'Bundled PHP or SQLite failed' }
$env:GOBLIN_DATA_DIR = Join-Path $root 'build/test data'
& "$out/runtime/php.exe" -c "$out/runtime/php.ini" -d "extension_dir=$out/runtime/ext" tests/regression.php
if ($LASTEXITCODE -ne 0) { throw 'Regression tests failed' }
python tests/http_smoke.py --root "$out" --php "$out/runtime/php.exe" --ini "$out/runtime/php.ini" --ext "$out/runtime/ext"
if ($LASTEXITCODE -ne 0) { throw 'HTTP tests failed' }
Get-ChildItem "$out/GoblinDungeon" -Filter '*.php' -Recurse | ForEach-Object {
    & "$out/runtime/php.exe" -n -l $_.FullName
    if ($LASTEXITCODE -ne 0) { throw "PHP syntax error: $_" }
}
Compress-Archive "$out/*" 'dist/GoblinDungeon-Windows.zip' -Force
(Get-FileHash 'dist/GoblinDungeon-Windows.zip' -Algorithm SHA256).Hash.ToLowerInvariant() + '  GoblinDungeon-Windows.zip' |
    Set-Content 'dist/SHA256SUMS.txt' -Encoding ascii
