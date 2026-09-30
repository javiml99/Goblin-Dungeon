param([Parameter(Mandatory=$true)][string]$GameRoot)
$ErrorActionPreference = 'Stop'
$report = Join-Path $env:RUNNER_TEMP ('goblin-' + [guid]::NewGuid() + '.txt')
$env:GOBLIN_SMOKE_REPORT = $report
$launcher = Start-Process (Join-Path $GameRoot 'GoblinDungeon.exe') -ArgumentList '--smoke-test' -PassThru
try {
    for ($i = 0; $i -lt 150 -and !(Test-Path $report); $i++) {
        if ($launcher.HasExited) { throw 'Launcher exited before readiness' }
        Start-Sleep -Milliseconds 100
    }
    if (!(Test-Path $report)) { throw 'Launcher did not report readiness' }
    $lines = Get-Content $report
    $url = $lines[0]
    $serverId = [int]$lines[1]
    $page = Invoke-WebRequest $url -NoProxy
    if ($page.StatusCode -ne 200 -or $page.Content -notmatch 'Entra en Goblin Dungeon') { throw 'Launcher did not serve the game' }
    $launcher.Refresh()
    if (!$launcher.CloseMainWindow()) { throw 'Cannot close launcher window' }
    if (!$launcher.WaitForExit(10000)) { throw 'Launcher did not exit' }
    if (Get-Process -Id $serverId -ErrorAction SilentlyContinue) { throw 'PHP remained alive after closing launcher' }
    Write-Output 'PASS: compiled launcher starts PHP, serves game and stops PHP on close'
} finally {
    if (!$launcher.HasExited) { $launcher.Kill(); $launcher.WaitForExit(5000) | Out-Null }
    Remove-Item $report -ErrorAction SilentlyContinue
    Remove-Item Env:GOBLIN_SMOKE_REPORT -ErrorAction SilentlyContinue
}
