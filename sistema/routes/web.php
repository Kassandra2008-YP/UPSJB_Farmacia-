<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard.index');

// Categorías
Route::resource('categories', CategoryController::class)
    ->except('show');

// Medicamentos
Route::resource('medicines', MedicineController::class)
    ->except('show');

// Clientes
Route::resource('customers', CustomerController::class)
    ->except('show');

// Ventas
Route::resource('sales', SaleController::class)
    ->except(['edit', 'update']);

// Roles
Route::resource('roles', RoleController::class)
    ->except('show');

// Usuarios
Route::resource('users', UserController::class)
    ->except('show');

// Reportes
Route::get('/reports', [ReportController::class, 'index'])
    ->name('reports.index');

    