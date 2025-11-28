<?php

use App\Http\Controllers\Auth\OwnerSetupController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForcedPasswordController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route pour la création du compte owner initial (accessible sans authentification)
Route::get('setup-owner', [OwnerSetupController::class, 'create'])
    ->name('owner.setup.create');

Route::post('setup-owner', [OwnerSetupController::class, 'store'])
    ->name('owner.setup.store');

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/force-password', [ForcedPasswordController::class, 'edit'])->name('password.force.edit');
    Route::put('/force-password', [ForcedPasswordController::class, 'update'])->name('password.force.update');

    Route::middleware('password.changed')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::resource('companies', CompanyController::class);
        Route::resource('technicians', TechnicianController::class);
        Route::resource('interventions', InterventionController::class);
        Route::resource('tags', TagController::class);

        Route::middleware('admin')->group(function () {
            Route::resource('users', UserController::class)->except(['show']);
        });
    });
});

require __DIR__.'/auth.php';
