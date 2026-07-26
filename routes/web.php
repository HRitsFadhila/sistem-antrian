<?php

use App\Http\Controllers\AntrianController;
use App\Http\Controllers\PoliController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/layar-tv', function () {
    return Inertia::render('Antrian/LayarTV');
});

Route::get('/api/layar-antrian', [AntrianController::class, 'layarAntrian']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth', 'role:admin')->group(function (){
    Route::resource('users', UserController::class);
    Route::resource('polis', PoliController::class);
    Route::resource('antrian', AntrianController::class);

    Route::get('/dashboard', [AntrianController::class, 'dashboard'])->name('dashboard');
    Route::post('/panggil-antrian', [AntrianController::class, 'panggilAntrian']);
    Route::post('/antrian-berikutnya', [AntrianController::class, 'antrianBerikutnya']);
    Route::post('/lewati-antrian', [AntrianController::class, 'lewatiAntrian']);
    Route::post('/panggil-dilewati', [AntrianController::class, 'panggilDilewati']);
});

require __DIR__.'/auth.php';
