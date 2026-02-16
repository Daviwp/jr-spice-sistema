<?php

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
    Route::get('/powerbi', [PowerBiController::class, 'index'])->name('powerbi');
    Route::get('/powerbi/pages', [AdminController::class, 'getPowerBiPages'])->name('powerbi.pages');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});


require __DIR__.'/auth.php';

Route::middleware(['auth', EnsureUserIsMaster::class])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
    Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'usersEdit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');

    Route::get('/settings', [AdminController::class, 'settingsIndex'])->name('admin.settings.index');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

    Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
    Route::post('/users/bulk-status', [AdminController::class, 'bulkUpdateStatus'])->name('admin.users.bulk-status');
});
