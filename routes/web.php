<?php

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\PoliController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/layar-tv', function () {
    return Inertia::render('Antrian/LayarTV');
});

Route::resource('antrian', AntrianController::class);

Route::get('/api/layar-antrian', [AntrianController::class, 'layarAntrian']);
;
Route::middleware('auth', 'role:admin')->group(function (){
    Route::resource('users', UserController::class);
    Route::resource('polis', PoliController::class);


    Route::get('/dashboard', [AntrianController::class, 'dashboard'])->name('dashboard');
    Route::post('/panggil-antrian', [AntrianController::class, 'panggilAntrian']);
    Route::post('/antrian-berikutnya', [AntrianController::class, 'antrianBerikutnya']);
    Route::post('/lewati-antrian', [AntrianController::class, 'lewatiAntrian']);
    Route::post('/panggil-dilewati', [AntrianController::class, 'panggilDilewati']);

    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
