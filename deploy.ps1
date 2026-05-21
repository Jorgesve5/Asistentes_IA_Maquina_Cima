$SERVER   = "192.168.10.210"
$USER     = "svr-maquinas"
$APP      = "/home/svr-maquinas/apps/Asistentes_IA_Maquina_Cima"
$LOCAL    = "C:\Users\practicas.e5\Desktop\jesus\Asistentes_IA_Maquina_Cima"
$SOCK     = "$env:TEMP\ssh_cima_ctl"

Write-Host ""
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "  DESPLIEGUE - Asistentes IA CIMA" -ForegroundColor Cyan
Write-Host "  Servidor: $SERVER" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Abriendo conexion SSH (introduce la contrasena UNA SOLA VEZ)..." -ForegroundColor Yellow

# Abrir master SSH en background - solo pide password una vez
$masterJob = Start-Process -FilePath "ssh" `
    -ArgumentList "-M -S `"$SOCK`" -o ControlPersist=120 -o StrictHostKeyChecking=no ${USER}@${SERVER} `"sleep 120`"" `
    -PassThru -NoNewWindow
Start-Sleep -Seconds 8

if (-not (Test-Path $SOCK)) {
    Write-Host "ERROR: No se pudo establecer conexion SSH. Verifica la contrasena." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "[1/5] Subiendo MachineDetail.php ..." -ForegroundColor Green
& scp -o "ControlPath=$SOCK" -o StrictHostKeyChecking=no "$LOCAL\app\Livewire\MachineDetail.php" "${USER}@${SERVER}:${APP}/app/Livewire/MachineDetail.php"

Write-Host "[2/5] Subiendo AdminDashboard.php ..." -ForegroundColor Green
& scp -o "ControlPath=$SOCK" -o StrictHostKeyChecking=no "$LOCAL\app\Livewire\AdminDashboard.php" "${USER}@${SERVER}:${APP}/app/Livewire/AdminDashboard.php"

Write-Host "[3/5] Subiendo machine-detail.blade.php ..." -ForegroundColor Green
& scp -o "ControlPath=$SOCK" -o StrictHostKeyChecking=no "$LOCAL\resources\views\livewire\machine-detail.blade.php" "${USER}@${SERVER}:${APP}/resources/views/livewire/machine-detail.blade.php"

Write-Host "[4/5] Subiendo admin-dashboard.blade.php ..." -ForegroundColor Green
& scp -o "ControlPath=$SOCK" -o StrictHostKeyChecking=no "$LOCAL\resources\views\livewire\admin-dashboard.blade.php" "${USER}@${SERVER}:${APP}/resources/views/livewire/admin-dashboard.blade.php"

Write-Host "[5/5] Subiendo carpeta public/build ..." -ForegroundColor Green
& scp -o "ControlPath=$SOCK" -o StrictHostKeyChecking=no -r "$LOCAL\public\build" "${USER}@${SERVER}:${APP}/public/"

Write-Host ""
Write-Host "Archivos subidos. Limpiando cachs en el servidor ..." -ForegroundColor Cyan

$remoteCmd = "cd $APP && sudo chmod -R 775 storage bootstrap/cache; php artisan storage:link; php artisan config:clear; php artisan view:clear; php artisan cache:clear; echo DESPLIEGUE_COMPLETADO"
& ssh -S "$SOCK" -o StrictHostKeyChecking=no "${USER}@${SERVER}" $remoteCmd

# Cerrar conexion maestra
Stop-Process -Id $masterJob.Id -ErrorAction SilentlyContinue

Write-Host ""
Write-Host "=============================================" -ForegroundColor Green
Write-Host "  LISTO. Recarga http://192.168.10.210" -ForegroundColor Green
Write-Host "  con Ctrl+Shift+R en el navegador" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host ""
