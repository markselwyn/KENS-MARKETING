<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportsController; 
use App\Http\Controllers\DssInsightsController; // NEW: Imported the DSS Controller!

// Redirect the root URL straight to the login page
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// GUEST ROUTES (Accessible only if NOT logged in)
// ==========================================
Route::middleware('guest')->group(function () {
    // Display the Login Page
    Route::get('/login', function () {
        return view('login'); // Points to resources/views/login.blade.php
    })->name('login');

    // Process the Login Form submission
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ==========================================
// AUTH ROUTES (Protected - Must be logged in)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // Process Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Main Dashboard Module
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // GLOBAL LIVE SEARCH ROUTE (Option 1)
    Route::get('/global-search', [DashboardController::class, 'globalSearch'])->name('global.search');

    // ==========================================
    // Sales / POS Module
    // ==========================================
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/sales/store', [SalesController::class, 'store'])->name('sales.store');

    // ==========================================
    // Inventory Module
    // ==========================================
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/store', [InventoryController::class, 'store'])->name('inventory.store'); 
    Route::post('/inventory/update', [InventoryController::class, 'update'])->name('inventory.update');
    Route::post('/inventory/delete', [InventoryController::class, 'destroy'])->name('inventory.delete');
    Route::post('/import-inventory', [InventoryController::class, 'importExcel'])->name('inventory.import');
    
    // The Restock Shipment Route
    Route::post('/inventory/restock', [InventoryController::class, 'restock'])->name('inventory.restock');

    // ==========================================
    // Reports Module
    // ==========================================
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports'); 
    Route::post('/reports/generate', [ReportsController::class, 'generate'])->name('reports.generate');
    
    // Functional Archive Buttons
    Route::get('/reports/archive/{id}/view', [ReportsController::class, 'viewArchive'])->name('reports.view');
    Route::get('/reports/archive/{id}/download', [ReportsController::class, 'downloadArchive'])->name('reports.download');
    Route::delete('/reports/archive/{id}', [ReportsController::class, 'destroy'])->name('reports.delete'); 

    // ==========================================
    // Decision Support System (DSS) Insights Module
    // ==========================================
    Route::get('/dss-insights', [DssInsightsController::class, 'index'])->name('dss-insights');
    
    // NEW: Apply Discount Route
    Route::post('/dss-insights/apply-discount', [DssInsightsController::class, 'applyDiscount'])->name('dss.apply-discount');
    
});