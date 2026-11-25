<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;


// ==========================================
// AUTENTICACIÓN
// ==========================================

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

});


// ==========================================
// RUTAS PROTEGIDAS
// ==========================================

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard.index');


    // Módulos principales
    Route::resource('categories', CategoryController::class)
        ->except('show');

    Route::resource('medicines', MedicineController::class)
        ->except('show');

    Route::resource('customers', CustomerController::class)
        ->except('show');

    Route::resource('sales', SaleController::class)
        ->except(['edit', 'update']);

    Route::resource('roles', RoleController::class)
        ->except('show');

    Route::resource('users', UserController::class)
        ->except('show');


    // Reportes
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');


    // Perfil del usuario
    Route::get('/profile', [AuthController::class, 'profile'])
        ->name('profile.edit');

    Route::put('/profile', [AuthController::class, 'updateProfile'])
        ->name('profile.update');


    // Cambiar contraseña
    Route::get('/password', [AuthController::class, 'password'])
        ->name('password.edit');

    Route::put('/password', [AuthController::class, 'updatePassword'])
        ->name('password.update');


    // Preferencias
    Route::get('/preferences', [AuthController::class, 'preferences'])
        ->name('preferences.edit');

    Route::put('/preferences', [AuthController::class, 'updatePreferences'])
        ->name('preferences.update');


    // Cerrar sesión
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

});

