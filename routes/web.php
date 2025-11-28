<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\InterventionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour les entreprises
    Route::resource('companies', CompanyController::class);

    // Routes pour les techniciens
    Route::resource('technicians', TechnicianController::class);

    // Routes pour les interventions
    Route::resource('interventions', InterventionController::class);
});

require __DIR__.'/auth.php';
