<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportsController; 
use App\Http\Controllers\DssInsightsController; 
use App\Http\Controllers\SettingsController;

// Redirect the root URL straight to the login page
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// GUEST ROUTES (Accessible only if NOT logged in)
// ==========================================
Route::middleware('guest')->group(function () {
    
    // Display the Login Page
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::get('/staff/login', [AuthController::class, 'showLogin'])->name('staff.login');

    // Process the Login Form submission
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    
    // Display the Registration Page
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    
    // Process the Registration Form submission
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // ==========================================
    // PASSWORD RECOVERY ROUTES
    // ==========================================
    // 1. Show the "Enter Email" form
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    
    // 2. Process the "Enter Email" form and send the link
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    
    // 3. Show the "Enter New Password" form (User clicked the email link)
    // NOTE: This must be named exactly 'password.reset' for Laravel's mailer to work
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    
    // 4. Process the "Enter New Password" form and save to database
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    
});

// ==========================================
// AUTH ROUTES (Protected - Must be logged in)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // Process Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // User Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/photo', [AuthController::class, 'profilePhoto'])->name('profile.photo');

    // Application Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::patch('/settings/sidebar', [SettingsController::class, 'updateSidebar'])->name('settings.sidebar.update');

    // ==========================================
    // Admin Security Hub
    // ==========================================
    Route::get('/admin/security', [AdminController::class, 'securityHub'])->name('admin.security');
    Route::post('/admin/approve-staff/{id}', [AdminController::class, 'approveStaff'])->name('admin.approve');
    Route::post('/admin/decline-staff/{id}', [AdminController::class, 'declineStaff'])->name('admin.decline');
    Route::post('/admin/revoke-staff/{id}', [AdminController::class, 'revokeStaff'])->name('admin.revoke'); 
    Route::post('/admin/restore-staff/{id}', [AdminController::class, 'restoreStaff'])->name('admin.restore');
    Route::delete('/admin/revoked-staff/{id}', [AdminController::class, 'deleteRevokedStaff'])->name('admin.revoked.delete');

    // Main Dashboard Module
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // GLOBAL LIVE SEARCH ROUTE
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
    Route::post('/dss-insights/apply-discount', [DssInsightsController::class, 'applyDiscount'])->name('dss.apply-discount');
    
});
