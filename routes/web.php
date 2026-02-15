<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PowerBiController;
use App\Services\PowerBiService;
use App\Http\Middleware\EnsureUserIsMaster;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function (PowerBiService $powerBiService) {
    return Inertia::render('Dashboard/Index', [
        'embedConfig' => $powerBiService->getEmbedConfig()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/powerbi', [PowerBiController::class, 'index'])->name('powerbi');
    Route::get('/powerbi/pages', [AdminController::class, 'getPowerBiPages'])->name('powerbi.pages');
});


require __DIR__.'/auth.php';

Route::middleware(['auth', EnsureUserIsMaster::class])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
    Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');

    Route::get('/settings', [AdminController::class, 'settingsIndex'])->name('admin.settings.index');
});
