<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\MachineDetail;
use App\Livewire\AdminDashboard;
use App\Livewire\AdminLogin;
use App\Livewire\ManualsExplorer;

use App\Http\Controllers\ApiController;

Route::get('/', Dashboard::class);
Route::get('/machines/{id}', MachineDetail::class);
Route::get('/manuals', ManualsExplorer::class);
Route::get('/admin/login', AdminLogin::class)->name('login');
Route::get('/admin', AdminDashboard::class);

// API REST Endpoints
Route::prefix('api')->group(function () {
    Route::get('/machines', [ApiController::class, 'getMachines']);
    Route::get('/machines/{id}', [ApiController::class, 'getMachine']);
    Route::post('/machines/{id}/status', [ApiController::class, 'updateMachineStatus']);
    Route::get('/alerts', [ApiController::class, 'getAlerts']);
});

Route::get('/limpiar-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return 'Caché borrada con éxito. Ya puedes volver a la aplicación y probar.';
});

Route::get('/limpiar-bd', function () {
    \App\Models\MachineError::truncate();
    return 'Base de datos borrada con éxito. Historial de errores vaciado. Vuelve a la aplicación y prueba.';
});
